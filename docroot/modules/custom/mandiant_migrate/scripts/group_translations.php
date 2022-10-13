<?php

use GuzzleHttp\Exception\RequestException;

$blogs_file = dirname(__DIR__) . '/sources/blogs.csv';
$failed_data_file = __DIR__ . '/failed_requests.csv';

$handler = fopen($blogs_file, 'r');

$data = [];
$sources = [];
$translations = [];

$groupings = [];
$multiples = [];
$invalid_groups = [];
$urls = [];
$duplicates = [];

$row = 0;
while ($line = fgetcsv($handler)) {
  // Skip the header.
  if ($row++ === 0) {
    continue;
  }
  // Skip duplicates.
  if (in_array($line[0], $urls)) {
    continue;
  }
  $hash = md5($line[0]);
  if ('8e98e091a6c925e8106d374fb66ebcf4' === $hash) {
    // There is currently one singularly failing hash which returns an empty
    // response. It should be removed because we can't migrate it. The assertions
    // below should still pass.
    continue;
  }
  if ('cfcdbc63d124a8c0e0788bbd179fa456' === $hash) {
    // This is the threat research blog landing page, which is not supposed to
    // be migrated into the new system, it is being replaced.
    continue;
  }
  $urls[] = $line[0];
  $data[$hash] = array_slice($line, 0, 3);
  $data[$hash][2] = ucwords($data[$hash][2]);
  $data[$hash][] = get_data_source_url($data[$hash][0]);
  $url = $line[0];
  $parts = explode('/', $url);
  $last_segment = array_pop($parts);
  $groupings[$last_segment][] = $url;
}

foreach($groupings as $id => $paths) {
  if (count($paths) > 1) {
    $multiples[$id] = $paths;
  }
  else {
    $sources[$id] = reset($paths);
  }
}

// Assert that every grouping contains exactly one https://www.fireeye.com URL.
foreach($multiples as $id => $group) {
  $filtered = array_filter($group, function ($path) {
    return stristr($path, 'https://www.fireeye.com');
  });
  if (count($filtered) !== 1) {
    $invalid_groups[$id] = $group;
  }
}
assert(empty($invalid_groups), "Every multiples group has exactly one https://www.fireeye.com URL.");

$counts = array_count_values($urls);
$duplicates = array_filter($counts, function ($count) {
  return $count > 1;
});
assert(empty($duplicates), "There are no duplicate URLs being checked.");

// Confirm every sources entry has a data entry.
$source_in_data = [];
foreach ($sources as $key => $source) {
  $source_in_data[$key] = isset($data[md5($source)]);
}
$source_is_in_data = array_filter($source_in_data);
assert(count($source_is_in_data) === count($source_in_data), "All sources have a recorded data entry.");

$x = 5;

function get_data_source_url($url) {
  $staging_host = 'https://stage.fireeye.com';
  $production_host = 'https://www.fireeye.com';
  $url = str_replace($production_host, '', $url);
  $url = str_replace('.html', '.3.json', $url);
  return $staging_host . $url;
}
