<?php

namespace Drupal\mandiant_migrate\Plugin\migrate\process;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\process\DomProcessBase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ConvertImagesToMedias.
 *
 * @MigrateProcessPlugin(
 *   id = "dom_convert_images_to_media"
 * )
 */
class ConvertImagesToMedia extends DomProcessBase implements ContainerFactoryPluginInterface {

  /**
   * The HTTP client service.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected ClientInterface $httpClient;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected FileSystemInterface $fileSystem;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client, FileSystemInterface $file_system, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
    $this->fileSystem = $file_system;
    $this->entityTypeManager = $entity_type_manager;
  }


  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
      $container->get('file_system'),
      $container->get('entity_type.manager')
    );
  }

  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $this->init($value, $destination_property);
    /** @var \DOMNodeList $images */
    $images = $this->xpath->query('//img');
    /** @var \DOMElement $image */
    foreach ($images as $image) {
      // Make sure it's not an inline png.
      $src = $image->getAttribute('src');
      if (!stripos($src, 'image/png')) {
        // Retrieve file from remote source.
        $location = $this->retrieveRemoteFile($src);
        if (!empty($location)) {
          $is_image = $this->fileIsImage($location);
          if ($is_image) {
            $file = $this->createFileEntity($location);
            if (!empty($file)) {
              $alt = $image->getAttribute('alt');
              $title = $image->getAttribute('title');
              $media = $this->createMediaEntity($file, $alt, $title);
              if (!empty($media)) {
                // Create a new DOM element for the image in the text.
                $new_node = $this->document->createElement('drupal-media', "");
                // Add attributes to that element - start with uuid.
                $uuid_att = $this->document->createAttribute('data-entity-uuid');
                $uuid_att->value = $media->uuid();
                $type_att = $this->document->createAttribute('data-entity-type');
                $type_att->value = 'media';
                $alt_att = $this->document->createAttribute('alt');
                $alt_att->value = htmlspecialchars($alt);
                $title_att = $this->document->createAttribute('title');
                $title_att->value = htmlspecialchars($title);
                $new_node->appendChild($uuid_att);
                $new_node->appendChild($type_att);
                $new_node->appendChild($alt_att);
                $new_node->appendChild($title_att);
                $image->parentNode->replaceChild($new_node, $image);
              }
            }
          }
        }
      }
    }
    return $this->document;
  }

  /**
   * @param string $path
   *   The relative path to the file.
   * @return string|boolean
   *   The public location of the file if downloaded, FALSE otherwise.
   */
  protected function retrieveRemoteFile($path) {
    $remote_host = 'https://www.fireeye.com';
    // Strip out fireeye domain to normalize path.
    $path = str_replace($remote_host, '', $path);
    $destination_path = 'public://2021-09';
    $this->fileSystem->prepareDirectory($destination_path, FileSystemInterface::MODIFY_PERMISSIONS | FileSystemInterface::CREATE_DIRECTORY);
    $file_path_info = pathinfo($path);
    $path = ltrim($path, '/');
    $remote_file_location = "$remote_host/$path";
    $basename = $file_path_info['basename'];
    if (!empty($basename)) {
      $destination_file_location = "$destination_path/$basename";
      $temp_dir = $this->fileSystem->getTempDirectory();
      $temp_file = $this->fileSystem->tempnam($temp_dir, 'migration_');
      try {
        $response = $this->httpClient->get($remote_file_location, ['sink' => $temp_file]);
      }
      catch (RequestException $e) {
        return FALSE;
      }
      if (200 == $response->getStatusCode()) {
        // File successfully downloaded, move it into file system and replace it
        // if it already exists.
        $this->fileSystem->move($temp_file, $destination_file_location, FileSystemInterface::EXISTS_RENAME);
        return $destination_file_location;
      }
    }
    return FALSE;
  }

  /**
   * @param string $path
   *  Public stream wrapper pointing to location of file.
   *
   * @return \Drupal\file\FileInterface|bool
   *   The file entity if create, FALSE otherwise.
   */
  protected function createFileEntity($path) {
    $filename = pathinfo($path, PATHINFO_BASENAME);
    try {
      /** @var \Drupal\file\FileInterface $file */
      $file = $this->entityTypeManager->getStorage('file')->create();
      $file->setFileUri($path);
      $file->setFilename($filename);
      $file->setMimeType(mime_content_type($path));
      $file->setSize(filesize($path));
      $file->setPermanent();
      $file->save();
      return $file;
    }
    catch (\Exception $e) {
      $x = 5;
    }

    return FALSE;
  }

  /**
   * Create a media entity from a file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity which should be used by the media entity.
   *
   * @return \Drupal\media\MediaInterface|bool
   *   The media entity, if created. FALSE otherwise.
   */
  protected function createMediaEntity($file, $alt = '', $title = '') {
    try {
      /** @var \Drupal\media\MediaInterface $media */
      $media = $this->entityTypeManager->getStorage('media')->create([
        'bundle' => 'image',
        'langcode' => 'en',
        'field_media_image' => [
          'target_id' => $file->id(),
          'alt' => $alt,
          'title' => $title,
        ],
      ]);
      $media->save();
      return $media;
    }
    catch (\Exception $e) {
      $x = 5;
    }
    return FALSE;
  }

  /**
   * @param string $path
   *   The path to the file in Drupal.
   *
   * @return boolean
   *   TRUE if the file is an image, FALSE otherwise.
   */
  protected function fileIsImage($path) {
    $mime = mime_content_type($path);
    [$image, $extension] = explode('/', $mime);
    return 'image' == $image;
  }

}
