<?php

namespace Drupal\mandiant_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Row;
use GuzzleHttp\Exception\RequestException;
use Acquia\Blt\Robo\Common\EnvironmentDetector;

/**
 * Source plugin to import data from JSON files
 * @MigrateSource(
 *   id = "press_releases"
 * )
 */
class PressReleases extends SourcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $published = $row->getSourceProperty('published');
    $newDate = date("Y-m-d\TH:i:s", strtotime($published));
    $row->setSourceProperty('publish_date', $newDate);

    return parent::prepareRow($row);
  }

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
      'tags' => $this->t('Tags'),
      'id' => $this->t('ID'),
      'language' => $this->t('Language'),
      'body' => $this->t('Body content'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Last modified timestamp'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return "Press releases";
  }

  /**
   * Initializes the iterator with the source data.
   * @return \Iterator
   *   An iterator containing the data for this source.
   */
  protected function initializeIterator() {
    $rows = [];
    $base_dir = drupal_get_path('module', 'mandiant_migrate') . "/sources";
    $data_dir = "$base_dir/press_release_data";
    $csv_file = "$base_dir/press_releases_url_list.csv";

    if (($handle = fopen($csv_file, 'r')) !== FALSE) {
      while (($csvrow = fgetcsv($handle)) !== FALSE) {
        $row = [];
        try {
          $id = md5($csvrow[0]);
          $row['id'] = $id;
          $row['language'] = 'en';
          $data = file_get_contents("$data_dir/$id.json");
          $json_decoded = json_decode($data, TRUE);
          $content = $json_decoded['jcr:content'];
          if (!empty($content)) {
            $row += $content;
            $row['title'] = $content['jcr:title'];
            $row['body'] = $this->getBodyValue($row);
            $tags = $content['cq:tags']; //$this->getBlogTags();
            if (!empty($tags)) {
              $row['tags'] = $tags;
            }
            $row['created'] = strtotime($json_decoded['jcr:created']);
            $row['changed'] = strtotime($content['cq:lastModified']);
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
    $longest_string = NULL;
    $all_strings = [];
    $this->findLongestString($row, $all_strings);
    usort($all_strings, function ($a, $b) {
      return strlen($a) < strlen($b);
    });
    $longest_string = array_shift($all_strings);
    $second_longest_string = array_shift($all_strings);
    return trim($longest_string);
  }

  private function findLongestString(array $search, &$all_strings) {
    foreach ($search as $element) {
      if (is_array($element)) {
        $this->findLongestString($element, $all_strings);
      }
      if (is_string($element)) {
        $all_strings[] = $element;
      }
    }
  }
}
