<?php
/**
 * @file
 * Contains \Drupal\mandiant_tweaks\Routing\RouteSubscriber.
 */

namespace Drupal\mandiant_tweaks\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    // Replace "some.route.name" below with the actual route you want to override.
    if ($route = $collection->get('node.add_page')) {
      $route->setDefaults(array(
        '_controller' => '\Drupal\mandiant_tweaks\Controller\MandiantTweaksController::nodeAdd',
      ));
    }
  }
}
