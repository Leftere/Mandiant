<?php
/**
 * @file
 * Recaptcha integration settings file.
 *
 * IMPORTANT NOTE:
 * This file has the goal of getting the recaptcha site key that will be used
 * in mandiant_recaptcha module.
 */
$settings['recaptcha_key'] = $_ENV['RECAPTCHA_SITE_KEY'] ?? getenv('RECAPTCHA_SITE_KEY');

