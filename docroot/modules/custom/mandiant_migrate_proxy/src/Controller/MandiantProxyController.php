<?php

namespace Drupal\mandiant_migrate_proxy\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * An mandiant_migrate_proxy controller.
 */
class MandiantProxyController extends ControllerBase {

  /**
   * Returns a current path.
   */
  public function proxy() {
    $output = [];
    $url = \Drupal::request()->query->get('url');
    if (!empty($url)) {
      try {
        $response = \Drupal::httpClient()->get($url, ['headers' => ['Accept' => 'text/plain']]);
        $output = json_decode($response->getBody());
      }
      catch (RequestException $e) {
        watchdog_exception('mandiant_migrate_proxy', $e);
      }
    }
    return new JsonResponse($output);
  }

}
