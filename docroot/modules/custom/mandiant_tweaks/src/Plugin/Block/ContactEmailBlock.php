<?php

namespace Drupal\mandiant_tweaks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ContactEmailBlock' Block.
 *
 * @Block(
 *   id = "contact_email_block",
 *   admin_label = @Translation("Contact Email Block"),
 *   category = @Translation("Contact Email Block"),
 * )
 */
class ContactEmailBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
        '#markup' => $this->t('Contact Email Block'),
    ];
  }

}
