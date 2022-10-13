<?php

//include_once '/var/www/.scratch/group_translations.php';
$press_releases_url_csv_file = dirname(__DIR__) . '/sources/press_releases_url_list.csv';
$failed_data_file = dirname(__DIR__) . '/sources/failed_press_release_requests.csv';
$stage_domain = 'https://stage.fireeye.com';
$production_domain = 'https://www.fireeye.com';

$failed_responses = [];
$file_handle = fopen($press_releases_url_csv_file, 'r');
while ($line = fgetcsv($file_handle)) {
  $hash = md5($line[0]);
  $data_file = "/var/www/docroot/modules/custom/mandiant_migrate/sources/press_release_data/$hash.json";
  // Skip the request process if we already have the data cached locally.
  if (file_exists($data_file)) {
    continue;
  }
  $request_url = sprintf('https://mandiantdev.prod.acquia-sites.com/proxy?url=%s', str_replace($production_domain, $stage_domain, $line[0]));
  try {
    $response = \Drupal::httpClient()->get($request_url);
    $json = $response->getBody()->getContents();
    $trimmed_json = trim($json);
    $decoded_json = json_decode($trimmed_json, TRUE);
    if (empty($decoded_json)) {
      throw new \Exception(sprintf('Request to %s for hash value %s returned empty. Verify it is a real page.', $request_url, $hash));
    }
    file_put_contents($data_file, $trimmed_json);

  }
  catch (\Exception $e) {
    $failed_responses[$hash] = array_merge([$request_url]);
  }

}

$failed_data_handler = fopen($failed_data_file, 'w');
foreach ($failed_responses as $hash => $values) {
  fputcsv($failed_data_handler, array_merge([$hash], $values));
}
fclose($failed_data_handler);
