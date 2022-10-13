<?php

use Acquia\Blt\Robo\Common\EnvironmentDetector;

if (EnvironmentDetector::isAhEnv()) {
  // Disable shield module on production.
  $config['shield.settings']['shield_enable'] = FALSE;

  if (EnvironmentDetector::isProdEnv()) {
    $current_environment = 'prod';
  }

  if (EnvironmentDetector::isStageEnv()) {
    $current_environment = 'stage';
  }

  if (EnvironmentDetector::isDevEnv()) {
    $current_environment = 'dev';
  }
}
else {
  $current_environment = 'local';
}

// Environment indicator.
$environment_indicator = [
  'local' => [
    'bg_color' => '#3f91d6',
    'fg_color' => '#feffff',
    'name' => 'Local',
  ],
  'dev' => [
    'bg_color' => '#2c884c',
    'fg_color' => '#feffff',
    'name' => 'Development',
  ],
  'stage' => [
    'bg_color' => '#fdcf6c',
    'fg_color' => '#000000',
    'name' => 'Staging',
  ],
  'production' => [
    'bg_color' => '#b34044',
    'fg_color' => '#feffff',
    'name' => 'Production',
  ],
];

$config['environment_indicator.indicator']['bg_color'] = $environment_indicator[$current_environment]['bg_color'];
$config['environment_indicator.indicator']['fg_color'] = $environment_indicator[$current_environment]['fg_color'];
$config['environment_indicator.indicator']['name'] = $environment_indicator[$current_environment]['name'];
