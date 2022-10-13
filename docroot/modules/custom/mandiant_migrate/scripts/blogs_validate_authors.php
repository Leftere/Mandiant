<?php

$data_dir = dirname(__DIR__) . '/sources/blog_data';
$files = scandir($data_dir);
array_shift($files);
array_shift($files);

$authors = [];
foreach ($files as $file) {
  $file_path = "$data_dir/$file";
  $contents = file_get_contents($file_path);
  $json = json_decode($contents, TRUE);
  assert(!empty($json['jcr:content']['author']), "Author in file $file.");
  $authors[substr($file, 0, -5)] = $json['jcr:content']['author'];
}
