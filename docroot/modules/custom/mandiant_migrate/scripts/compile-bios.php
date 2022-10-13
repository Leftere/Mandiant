<?php

$webinars_csv = dirname(__DIR__) . '/sources/webinars_raw.csv';
$podcasts_csv = dirname(__DIR__) . '/sources/podcasts_raw.csv';
$sources_dir = dirname($webinars_csv);

$webinars_handle = fopen($webinars_csv, 'r');

// Read and discard header row.
fgetcsv($webinars_handle);

$bios = [];

while ($row = fgetcsv($webinars_handle)) {

}
