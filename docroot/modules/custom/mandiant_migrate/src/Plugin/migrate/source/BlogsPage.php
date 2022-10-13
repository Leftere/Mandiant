<?php

namespace Drupal\mandiant_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Row;
use GuzzleHttp\Exception\RequestException;

/**
 * Source plugin to import data from JSON files
 * @MigrateSource(
 *   id = "blogs_page"
 * )
 */
class BlogsPage extends SourcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids = [
      'id' => [
        'type' => 'string'
      ]
    ];
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'title' => $this->t('Title'),
      'topic' => $this->t('Topic'),
      'tags' => $this->t('Tags'),
      'id' => $this->t('ID'),
      'language' => $this->t('Language'),
      'body' => $this->t('Body content'),
      'author' => $this->t('Author'),
      'published' => $this->t('Publish date'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Last modified timestamp')
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return "Blog Data";
  }

  /**
   * Initializes the iterator with the source data.
   * @return \Iterator
   *   An iterator containing the data for this source.
   */
  protected function initializeIterator() {
    $rows = [];
    $csv_file = $this->configuration['csv'];
    $base_dir = drupal_get_path('module', 'mandiant_migrate') . "/sources";
    $data_dir = "$base_dir/blog_data";
    $csv_file = "$base_dir/$csv_file.csv";

    if (($handle = fopen($csv_file, 'r')) !== FALSE) {
      while (($csvrow = fgetcsv($handle)) !== FALSE) {
        $row = [];
        try {
          $id = $csvrow[0];
          $row['id'] = $id;
          $row['topic'] = $csvrow[1];
          $parent = isset($csvrow[2]) ? $csvrow[2] : NULL;
          if (!empty($parent)) {
            $row['parent'] = $parent;
            $parent_data = file_get_contents("$data_dir/$parent.json");
            $parent_json = json_decode($parent_data, TRUE);
            $row['source_language'] = $this->convertBlogLanguageToDrupal($parent_json['jcr:content']['jcr:language']);
          }
          $data = file_get_contents("$data_dir/$id.json");
          $json_decoded = json_decode($data, TRUE);
          $content = $json_decoded['jcr:content'];
          if (!empty($content)) {
            $row += $content;
            $row['title'] = $content['jcr:title'];
            $row['language'] = $this->convertBlogLanguageToDrupal($content['jcr:language']);
            $body = $this->getBodyValue($row);
            if (!empty($body)) {
              $row['body'] = $body;
            }
            $row['author'] = $content['author'];
            $publish_date = $this->convertPublishDate($row['published']);
            if (!empty($publish_date)) {
              $row['published'] = $publish_date;
            }
            // Map created changed from jcr:created and jcr:content->cq:lastModified.
            $row['created'] = strtotime($json_decoded['jcr:created']);
            $row['changed'] = strtotime($content['cq:lastModified']);
            if (!empty($content['cq:tags'])) {
              $row['tags'] = $content['cq:tags'];
            }
            $rows[] = $row;
          }
        } catch (RequestException $e) {
          watchdog_exception('mandiant_migrate', $e);
        }
      }
      fclose($handle);
    }

    return new \ArrayIterator($rows);
  }

  /**
   * Get a list of filtered blog tags.
   *
   * @param string[] tags
   *   List of tags to filter.
   *
   * @return string[]|bool
   *   Filtered tags. FALSE otherwise.
   */
  protected function getBlogTags(array $tags) {
    if (!empty($tags)) {
      return array_map(function ($tag) {
        [$tag_type, $filtered_tag] = explode(':', $tag, 2);
        return $filtered_tag;
      }, $tags);
    }
    return FALSE;
  }

  protected function getBlogDataSourceUrl($url) {
    $url = str_replace('.html', '.3.json', $url);
    $url = str_replace('https://www.fireeye.com', 'https://stage.fireeye.com', $url);
    return $url;
  }

  protected function convertBlogLanguageToDrupal($language = NULL) {
    if (empty($language)) {
      return 'en';
    }
    if (is_array($language)) {
      $language = 'en_US';
    }
    $blog_language_mappings = [
      'ja' => 'ja',
      'en_us' => 'en',
      'ko_kr' => 'ko',
      'fr' => 'fr',
      'de_de' => 'de',
      'en_US' => 'en',
      'fr_FR' => 'fr',
      'en' => 'en',
      'de_DE' => 'de',
    ];
    return $blog_language_mappings[$language];
  }

  protected function getBodyValue($row) {
    $par = $row['par'];
    if (!empty($par['entry']['text']) && is_string($par['entry']['text'])) {
      return $par['entry']['text'];
    }
    else {
      foreach ($par as $index => $value) {
        if (is_array($value)) {
          if (!empty($value['text']) && is_string($value['text'])) {
            return $value['text'];
          }
        }
      }
    }
    return FALSE;
  }

  protected function convertPublishDate($published) {
    // Publish date is formatted in AEM as
    // Mon Feb 10 2020 11:00:00 GMT+0900.
    $new_date = date("Y-m-d\TH:i:s", strtotime($published));
    return $new_date;
  }

}
