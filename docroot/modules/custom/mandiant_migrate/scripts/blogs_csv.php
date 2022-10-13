<?php

// This script is not used currently. It is being kept for historical purposes
// and also as reference material for previous migration work.
//
// For current cleaning solution see create_csvs.php, group_translations.php,
// and download_data.php.

$csv_file = '/var/www/docroot/modules/custom/mandiant_migrate/sources/blogs.csv';
$blogs_clean_file = '/var/www/docroot/modules/custom/mandiant_migrate/sources/blogs_clean.csv';
$blogs_english_file = '/var/www/docroot/modules/custom/mandiant_migrate/sources/blogs_english.csv';
$blogs_translations_file = '/var/www/docroot/modules/custom/mandiant_migrate/sources/blogs_translations.csv';
$blogs_clean_data = [];
$blogs_english_data = [['URL', 'Type', 'Blog Category', 'ID']];
$blogs_translations_data = [['URL', 'Type', 'Blog Category', 'Translation Source']];

$blogs_english_stream = fopen($blogs_english_file, 'w');
$blogs_translations_stream = fopen($blogs_translations_file, 'w');

$row = 0;
if (($handle = fopen($blogs_clean_file, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    if ($row++ === 0)
      continue;
    $slice = array_slice($data, 0, 3);
    $key = get_translation_key($slice[0]);
    if (is_english($slice[0])) {
      $blogs_english_data[md5($key)] = array_merge($slice, [$key]);
    }
    else {
      $blogs_translations_data[] = array_merge($slice, [$key]);
    }
    $x = 5;
  }
  fclose($handle);
}

filter_out_missing_sources($blogs_english_data, $blogs_translations_data);

foreach ($blogs_english_data as $data) {
  fputcsv($blogs_english_stream, $data);
}
foreach ($blogs_translations_data as $data) {
  fputcsv($blogs_translations_stream, $data);
}


fclose($blogs_english_stream);
fclose($blogs_translations_stream);

$x = 5;

function is_english($url) {
  return substr($url, 0, 5) === 'https';
}

function get_translation_key($url) {
  $value = '';
  $regex = '/\/[\d]{4}.+\.html$/';
  preg_match($regex, $url, $matches);
  return empty($matches[0]) ? FALSE : $matches[0];
}

function filter_out_missing_sources(array $english, array &$translations) {
  $translations_copy = $translations;
  array_shift($english);
  array_shift($translations_copy);
  $missing_sources = [];
  foreach ($translations_copy as $index => $translation) {
    $key = $translation[3];
    $source_exists = array_key_exists(md5($key), $english);
    if (!$source_exists) {
      $missing_sources[$key][] = $translation[0];
      // Unset the translation, since we have no english source, we can't
      // migrate it.
      unset($translations[$index]);
    }
  }
  if (!empty($missing_sources)) {
    $x = 5;
  }
}
