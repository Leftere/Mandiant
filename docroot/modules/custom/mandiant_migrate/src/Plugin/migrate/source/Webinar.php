<?php


namespace Drupal\mandiant_migrate\Plugin\migrate\source;


use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;

/**
 * Class Video
 *
 * @MigrateSource(
 *   id = "mandiant_webinar"
 * )
 */
class Webinar extends SourcePluginBase {

  public const TITLE = 0;
  public const DESCRIPTION = 1;
  public const DATE = 2;
  public const TOPIC = 13;
  public const BRIGHTTALK_URL = 5;
  public const PRESENTED_BY = 4;
  public const ID = 17;
  public const INDUSTRY = 15;

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id' => $this->t('ID'),
      'title' => $this->t('Title'),
      'date' => $this->t('Date'),
      'description' => $this->t('Description'),
      'brighttalk_url' => $this->t('BrightTalk URL'),
      'presented_by' => $this->t('Presented By'),
      'topic' => $this->t('Topics'),
      'industry' => $this->t('Industry'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return 'Mandiant Webinars';
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    // TODO: Implement getIds() method.
    return [
      'id' => [
        'type' => 'integer',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeIterator() {
    $rows = [];
    $base_dir = drupal_get_path('module', 'mandiant_migrate') . "/sources";
    $csv_file = "$base_dir/webinars_raw.csv";
    if (($handle = fopen($csv_file, 'r')) !== FALSE) {
      // Skip first row.
      fgetcsv($handle);
      while (($csvrow = fgetcsv($handle)) !== FALSE) {
        $row = [];
        $id = $csvrow[self::ID];
        $row['id'] = $id;
        $row['title'] = $csvrow[self::TITLE];
        $row['date'] = $csvrow[self::DATE];
        $row['description'] = $csvrow[self::DESCRIPTION];
        $row['brighttalk_url'] = $csvrow[self::BRIGHTTALK_URL];
        $row['topic'] = array_map('trim', explode(',', $csvrow[self::TOPIC]));
        $row['date'] = $this->formatDate($csvrow[self::DATE]);
        $row['presented_by'] = $this->chunkBios($csvrow);
        $row['industry'] = $csvrow[self::INDUSTRY];
        $rows[] = $row;
      }
    }
    return new \ArrayIterator($rows);
  }

  /**
   * @param string $date
   *   The date in the source (e.g. 6/9/2021).
   *
   * @return string
   *   The version to store in the database (e.g. 2021-09-06T00:00:00).
   */
  protected function formatDate($date) {
    [$month, $day, $year] = explode('/', $date);
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
    $day = str_pad($day, 2, '0', STR_PAD_LEFT);
    return sprintf('%s-%s-%sT00:00:00', $year, $month, $day);
  }

  /**
   * Chunk up bios so they can be fed to sub_process plugin.
   *
   * @param array $csvrow
   *   The row of source data.
   *
   * @return array
   *   Array or arrays keyed by name and role.
   */
  protected function chunkBios(array $csvrow) {
    $chunked_out = [];
    $persons = explode('||', $csvrow[self::PRESENTED_BY]);
    foreach ($persons as $person) {
      [$person, $role] = explode('##', $person);
      $person = trim($person);
      if (!empty($role)) {
        $role = trim($role);
      }
      $chunked_out[] = ['name' => $person, 'role' => $role];
    }
    return $chunked_out;
  }
}
