<?php

include_once __DIR__ . '/group_translations.php';

$sources_dir = dirname($blogs_file);
$blog_data_dir = $sources_dir . '/blog_data';
$sources_file = $sources_dir . '/blog_sources.csv';
$translations_file = $sources_dir . '/blog_translations.csv';

$files = scandir($blog_data_dir);
array_shift($files);
array_shift($files);
$blog_languages = [];
$missing_languages = [];
foreach ($files as $file) {
  $hash = substr($file, 0, -5);
  $path = $blog_data_dir . '/' . $file;
  $contents = file_get_contents($path);
  $json = json_decode($contents, TRUE);
  $blog_language = $json["jcr:content"]["jcr:language"];
  if (empty($blog_language)) {
    $missing_languages[$hash] = [
      'data' => $data[$hash],
      'json' => $json,
    ];
  }
  else {
    if (is_array($blog_language)) {
      $blog_language = 'en_US';
    }
    $blog_languages[trim($blog_language)][] = $file;
  }
}

// In the migration source plugin, if there is no language in the JSON data then
// it should be assumed that it is english (en).


$languages = \Drupal::languageManager()->getLanguages();

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

$languages = array_unique(array_keys($languages));
$blog_language_ids = array_unique(array_values($blog_language_mappings));
$common_languages = array_diff($blog_language_ids, $languages);
assert(empty($common_languages), "Drupal has all mapped languages enabled.");

$source_csvs = [];
$translation_csvs = [];

$sources_handle = fopen($sources_file, 'w');
foreach ($sources as $last_segment => $excel_id) {
  $hash = md5($excel_id);
  assert(isset($data[$hash]), "Data exists for resource.");
  fputcsv($sources_handle, [$hash, $data[$hash][2]]);
}

$translations_handle = fopen($translations_file, 'w');
foreach ($multiples as $last_segment => $excel_ids) {
  $language_versions = [];
  foreach ($excel_ids as $excel_id) {
    $hash = md5($excel_id);
    $data_file = "$blog_data_dir/$hash.json";
    $json = file_get_contents($data_file);
    $decoded = json_decode($json, TRUE);
    $language = convertBlogLanguageToDrupal($decoded["jcr:content"]["jcr:language"]);
    $language_versions[$language] = [
      'hash' => $hash,
      'data' => $decoded
    ];
  }
  assert(array_key_exists('en', $language_versions), "English exists as a language.");
  $english = $language_versions['en'];
  unset($language_versions['en']);
  $source_hash = $english['hash'];
  // Add the source translation to the blog_sources.csv files, it gets migrated
  // first.
  fputcsv($sources_handle, [$source_hash, $data[$source_hash][2]]);
  foreach ($language_versions as $language => $language_version) {
    // Add the translations for the source to blog_translation.csv.
    fputcsv($translations_handle, [
      $language_version['hash'],
      $data[$language_version['hash']][2],
      $source_hash,
    ]);
  }
}
fclose($sources_handle);
fclose($translations_handle);

$x = 5;


function convertBlogLanguageToDrupal($language) {
  if (empty($language)) {
    return 'en';
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
