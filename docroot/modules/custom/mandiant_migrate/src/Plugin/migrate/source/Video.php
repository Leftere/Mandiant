<?php


namespace Drupal\mandiant_migrate\Plugin\migrate\source;


use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;

/**
 * Class Video
 *
 * @MigrateSource(
 *   id = "mandiant_video"
 * )
 */
class Video extends SourcePluginBase {

  public const TITLE = 0;
  public const ID = 1;
  public const TOPIC = 9;
  public const INDUSTRY = 11;

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id' => $this->t('ID'),
      'title' => $this->t('Title'),
      'video_url' => $this->t('Video URL'),
      'topic' => $this->t('Topic'),
      'industry' => $this->t('Industry'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return 'Mandiant Video';
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    // TODO: Implement getIds() method.
    return [
      'id' => [
        'type' => 'string',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeIterator() {
    $rows = [];
    $base_dir = drupal_get_path('module', 'mandiant_migrate') . "/sources";
    $csv_file = "$base_dir/videos_raw.csv";
    if (($handle = fopen($csv_file, 'r')) !== FALSE) {
      // Skip first row.
      fgetcsv($handle);
      while (($csvrow = fgetcsv($handle)) !== FALSE) {
        $row = [];
        $id = $csvrow[self::ID];
        $row['id'] = $id;
        $row['title'] = $csvrow[self::TITLE];
        $row['industry'] = trim($csvrow[self::INDUSTRY]);
        $row['topic'] = array_filter(array_map('trim', explode(',', $csvrow[self::TOPIC])));
        $row['video_url'] = sprintf('https://play.vidyard.com/%s', $id);
        $rows[] = $row;
      }
    }
    return new \ArrayIterator($rows);
  }

}
