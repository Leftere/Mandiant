<?php

/**
 * @file
 * Profile code for Mandiant main.
 */

/**
 * Implements hook_config_ignore_settings_alter().
 */
function mandiant_main_config_ignore_settings_alter(array &$settings) {
  $settings[] = 'webform.webform.*';
}
