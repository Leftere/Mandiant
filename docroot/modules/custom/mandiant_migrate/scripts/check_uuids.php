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

$english_files = scandir($blogs_english_data_dir);
$translation_files = scandir($blogs_translations_data_dir);
array_shift($english_files);
array_shift($english_files);
array_shift($translation_files);
array_shift($translation_files);


$x = 5;

$english_uuids = [];
$translation_uuids = [];

foreach ($english_files as $file) {
  $file = "$blogs_english_data_dir/$file";
  $content = trim(file_get_contents($file));
  $content = json_decode($content, TRUE);
  $uuid = $content["jcr:content"]["jcr:uuid"];
  $english_uuids[$uuid] = $content;
}

foreach ($translation_files as $file) {
  $file = "$blogs_translations_data_dir/$file";
  $content = trim(file_get_contents($file));
  $content = json_decode($content, TRUE);
  $uuid = $content["jcr:content"]["jcr:uuid"];
  $translation_uuids[$uuid] = $content;
}

$translation_uuid_values = array_keys($translation_uuids);
$english_uuid_values = array_keys($english_uuids);

$common_uuids = array_intersect($translation_uuid_values, $english_uuid_values);

$x = 5;
