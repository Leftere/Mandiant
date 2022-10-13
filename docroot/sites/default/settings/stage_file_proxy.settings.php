<?php

use Acquia\Blt\Robo\Common\EnvironmentDetector;

if (EnvironmentDetector::isLocalEnv()) {
  // Stage file proxy custom settings.
  $set_proxy_origin = 'prod'; // (dev|stg|prod)
  $protocol = 'https';
  $domain = 'mandiant';
  $proxy_origins = [
    'dev'  => "{$protocol}://{$domain}dev.prod.acquia-sites.com",
    'stg'  => "{$protocol}://{$domain}stg.prod.acquia-sites.com",
    'prod' => "{$protocol}://www.{$domain}.com",
  ];
  // Stage file proxy module settings.
  $config['stage_file_proxy.settings']['verify'] = FALSE;
  $config['stage_file_proxy.settings']['use_imagecache_root'] = TRUE;
  $config['stage_file_proxy.settings']['origin'] = $proxy_origins[$set_proxy_origin];
}
