<?php


namespace Drupal\mandiant_migrate\Plugin\migrate\source;


use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;

/**
 * Class Video
 *
 * @MigrateSource(
 *   id = "mandiant_podcast"
 * )
 */
class Podcast extends SourcePluginBase {

  public const TITLE = 3;
  public const SEASON = 1;
  public const EPISODE = 2;
  public const PODCAST_SERIES = 0;
  public const DESCRIPTION = 7;
  public const DATE = 4;
  public const TOPIC = 11;
  public const LINK = 8;
  public const BUZZSPROUT_EMBED = 14;
  public const APPLE_LINK = 15;
  public const SPOTIFY_LINK = 16;
  public const YOUTUBE_EMBED = 17;
  public const ID = 18;
  public const GUEST_BIO = 6;
  public const GUEST = 5;
  public const INDUSTRY = 13;

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id' => $this->t('ID'),
      'title' => $this->t('Title'),
      'description' => $this->t('Podcast episode description'),
      'date' => $this->t('Date'),
      'topic' => $this->t('Topics'),
      'guest' => $this->t('Guest'),
      'guest_bio' => $this->t('Guest Bio'),
      'episode' => $this->t('Episode'),
      'series' => $this->t('Series'),
      'apple' => $this->t('Apple podcast link'),
      'spotify' => $this->t('Spotify podcast link'),
      'youtube_embed' => $this->t('YouTube Embed'),
      'youtube_source' => $this->t('YouTube Source'),
      'industry' => $this->t('Industry'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return 'Mandiant Podcasts';
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
    $csv_file = "$base_dir/podcasts_raw.csv";
    if (($handle = fopen($csv_file, 'r')) !== FALSE) {
      // Skip first row.
      fgetcsv($handle);
      while (($csvrow = fgetcsv($handle)) !== FALSE) {
        $row = [];
        $row['id'] = $csvrow[self::ID];
        $row['title'] = $csvrow[self::TITLE];
        $row['topic'] = array_map('trim', explode(',', $csvrow[self::TOPIC]));
        $row['date'] = $this->formatDate($csvrow[self::DATE]);
        $row['guest'] = $csvrow[self::GUEST];
        $row['guest_bio'] = $csvrow[self::GUEST_BIO];
        $row['episode'] = $csvrow[self::EPISODE];
        $row['series'] = $this->getSeries($csvrow);
        $row['description'] = $csvrow[self::DESCRIPTION];
        $row['spotify'] = $csvrow[self::SPOTIFY_LINK];
        $row['apple'] = $csvrow[self::APPLE_LINK];
        $row['industry'] = $csvrow[self::INDUSTRY];
        if ('use YouTube embed code' != $csvrow[self::BUZZSPROUT_EMBED]) {
          $row['buzzsprout'] = $csvrow[self::BUZZSPROUT_EMBED];
        }
        else {
          $youtube_source = $this->getYouTubeSource($csvrow);
          if (!empty($youtube_source)) {
            $row['youtube_embed'] = $csvrow[self::YOUTUBE_EMBED];
            $row['youtube_source'] = sprintf('https://www.youtube.com/watch?v=%s', $youtube_source);
          }
        }
        $rows[] = $row;
      }
    }
    return new \ArrayIterator($rows);
  }

  /**
   * Format the date correctly.
   *
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
   * Get the podcast series name.
   *
   * @param array $row
   *   The csv row of data.
   *
   * @return string
   *   The name of the series.
   */
  protected function getSeries(array $row) {
    $podcast = $row[self::PODCAST_SERIES];
    $season = $row[self::SEASON];
    if (empty($season) || 'N/A' == $season) {
      return $podcast;
    }
    return "$podcast, $season";
  }

  protected function getYouTubeSource(array $row) {
    $source = $row[self::YOUTUBE_EMBED];
    if (empty($source)) {
      return NULL;
    }
    // See https://regex101.com/r/GRBFxo/1.
    preg_match('/https:\/\/www.youtube.com\/embed\/([^"]*)/', $source, $matches);
    return $matches[1];
  }
}
