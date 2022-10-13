<?php

/**
 * @file
 * Language Negotiation Settings.
 */

use Acquia\Blt\Robo\Common\EnvironmentDetector;

if (!EnvironmentDetector::isProdEnv()) {
  $config['language.negotiation']['url']['source'] = 'path_prefix';
}
