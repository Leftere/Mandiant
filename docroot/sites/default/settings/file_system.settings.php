<?php

use Acquia\Blt\Robo\Common\EnvironmentDetector;

if (EnvironmentDetector::isLocalEnv()) {
  // Configure local paths to be at root/sites/default/files and
  // root/files-private/default by... default.
  $settings['file_public_path'] = "sites/default/files";
  $settings['file_private_path'] = "$repo_root/files-private/default";
}
