<?php

namespace Gnorm\Fed;

/**
 * Class Drupal
 *
 * @package Gnorm\Fed\Extensions
 */
class Extensions extends \Twig_Extension {

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      // Add new filters to Gnorm Twig like this:
      // new \Twig_SimpleFilter('filter_name', [$this, 'passThrough'])
    ];
  }

  /**
   * Simple pass through the passed value.
   *
   * @param $element
   *   Any passed element.
   *
   * @return mixed
   *   The passed element.
   */
  public function passThrough($element) {
    return $element;
  }
}
