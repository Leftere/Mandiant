(function ($) {
  'use strict';

  Drupal.behaviors.layoutModal = {
    attach: function (context, settings) {
      var destination = '.modal-styling-options .details-wrapper';

      /* We need to prepare different types of selectors based on the Layout
      Builder Styles grouped configuretion (single or multi value). */
      var targets = [
        '.form-item-layout-builder-style-alignment',
        '[data-drupal-selector="edit-layout-builder-style-alignment"]',
        '.form-item-layout-builder-style-background',
        '[data-drupal-selector="edit-layout-builder-style-background"]',
        '.form-item-layout-builder-style-size',
        '[data-drupal-selector="edit-layout-builder-style-size"]',
        '.form-item-layout-builder-style-modifiers-type',
        '[data-drupal-selector="edit-layout-builder-style-modifiers-type"]',
        '.form-item-layout-builder-style-add-ons',
        '[data-drupal-selector="edit-layout-builder-style-add-ons"]',
        '.form-item-layout-builder-style-add-ons',
        '[data-drupal-selector="edit-layout-builder-style-modifiers-layout"]',
        '.form-item-layout-builder-style-modifiers-style-featured-logos',
        '[data-drupal-selector="edit-layout-builder-style-modifiers-style-featured-logos"]',
        '.form-item-layout-builder-style-modifiers-image',
        '[data-drupal-selector="edit-layout-builder-style-modifiers-image"]',
        '.form-item-layout-builder-style-modifiers-link',
        '[data-drupal-selector="edit-layout-builder-style-modifiers-link"]',
        '.form-item-layout-builder-style-modifiers-style',
        '[data-drupal-selector="edit-layout-builder-style-modifiers-style"]',
        '.glb-form-wrapper .form-item-settings-layout-builder-id',
        
      ];

      // Move every Layout Builder Style group to the Settings form group.
      for (var i = 0; i < targets.length; i++) {
        var $el = $('form > ' + targets[i])

        if ($el.length === 1) {
          $el.appendTo(destination)
        }
      }
    }
  }
})(jQuery);
