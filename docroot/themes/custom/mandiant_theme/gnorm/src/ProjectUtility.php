<?php

namespace Gnorm\Fed;

use Composer\Script\Event;

class ProjectUtility {
  public static function removeVirtualHost(Event $event) {
    $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
    $docksal_env = realpath("$vendorDir/../.docksal/docksal.env");
    if (file_exists($docksal_env)) {
      $content = file($docksal_env);
      $stripped_virtual_host = '';
      foreach ($content as $line) {
        if (!preg_match('/^VIRTUAL_HOST=/', $line)) {
          $stripped_virtual_host .= $line;
        }
      }
      file_put_contents($docksal_env, $stripped_virtual_host);
    }
  }

  public static function updatePipelines(Event $event) {
    $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
    $bitbucket_pipelines = realpath("$vendorDir/../bitbucket-pipelines.yml");
    $bitbucket_pipelines_example = realpath("$vendorDir/../example.bitbucket-pipelines.yml");
    unlink($bitbucket_pipelines);
    rename($bitbucket_pipelines_example, $bitbucket_pipelines);
  }
}
