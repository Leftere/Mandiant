<?php

// This script is not used currently. It is being kept for historical purposes
// and also as reference material for previous migration work.
//
// For current cleaning solution see create_csvs.php, group_translations.php,
// and download_data.php.

$blogs_english_file = '/var/www/docroot/modules/custom/mandiant_migrate/sources/blogs_english.csv';
$blogs_translations_file = '/var/www/docroot/modules/custom/mandiant_migrate/sources/blogs_translations.csv';
$blogs_english_data_dir = '/var/www/docroot/modules/custom/mandiant_migrate/sources/blogs_english';
$blogs_translations_data_dir = '/var/www/docroot/modules/custom/mandiant_migrate/sources/blogs_translations';

$current_file = $blogs_translations_file;
$data_dir = $blogs_translations_data_dir;

//$blogs_english_stream = fopen($blogs_english_file, 'r');
//$blogs_translations_stream = fopen($blogs_translations_file, 'r');
$request_options = [
  'headers' => [
    'Accept' => 'text/plain'
  ],
];
$row = 0;
if (($handle = fopen($current_file, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    if ($row++ === 0)
      continue;
//    $data_url = replace_url($data[0]);
    $data_url = replace_url_translated($data[0]);
    $response = \Drupal::httpClient()->get($data_url, $request_options);
//    $data_file = $blogs_english_data_dir . '/' . md5($data[3]) . '.json';
    $data_file = $data_dir . '/' . md5($data[0]) . '.json';
    file_put_contents($data_file, trim($response->getBody()));
    $x = 5;
  }
  fclose($handle);
}

function replace_url($url) {
  $url = str_replace('.html', '.3.json', $url);
  $url = str_replace('https://www.fireeye.com', 'https://stage.fireeye.com', $url);
  $url = sprintf('https://mandiantdev.prod.acquia-sites.com/proxy?url=%s', $url);
  return $url;
}

function replace_url_translated($url) {
  $url = str_replace('.html', '.3.json', $url);
  $url = "https://stage.fireeye.com$url";
  $url = sprintf('https://mandiantdev.prod.acquia-sites.com/proxy?url=%s', $url);
  return $url;
}
