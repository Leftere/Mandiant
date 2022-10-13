(function ($) {
  'use strict';

  Drupal.behaviors.layoutBuilderTweaks = {
    togglePreview: function (status) {
      var regions = 'header, div.top, footer, div.bottom';
      status ? $(regions).show() : $(regions).hide();
    },
    attach: function (context, settings) {
      var _this = this
      $(context)
        .find("#edit-preview-block-layout")
        .once("preview-header-events")
        .each(function () {
          var $el = $(this).find('input');
          _this.togglePreview($el.is(':checked'))
          $(this).on('change', function (v) {
            _this.togglePreview($el.is(':checked'))
          })
        });
              
      var $blockTabs = $(context)
        .find("#block-tabs")
        .once("preview-header-events")
        .find('ul');
      
      $(context)
        .find('#edit-actions[role="region"]')
        .once("preview-header-events")
        .append($blockTabs);
    }
  }
})(jQuery);


