(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Provides behaviour to control Slick carousel from external elements.
   */
  Drupal.behaviors.mybehavior = {
    attach: function (context, settings) {
      once('verticalTabs', 'html', context).forEach( function () {
        var blockBundle = $("div.block-bundle-tab");
        var blockBundleQuestions = $("div.block-bundle-featured_questions");

        blockBundle
          .find(".layout--twocol-section .layout__region--first .slick-for > a")
          .first()
          .addClass('active-trail');

        blockBundleQuestions
          .find(".layout--twocol-section .layout__region--first .slick-for > a")
          .first()
          .addClass("active-trail");

        blockBundle
          .find(".layout--twocol-section .layout__region--first .slick-for > a")
          .bind("click", function(e) {
            e.preventDefault();
            e.stopPropagation();

            var _this = $(this);
            var index = _this.index();

            blockBundle.find(".layout--twocol-section .layout__region--second .slick__slider")
              .slick('slickGoTo', index)
              .on('afterChange', function(){
                _this.parent().find('a').removeClass('active-trail');
                _this.addClass('active-trail');
            });
          });
        blockBundleQuestions
          .find(".layout--twocol-section .layout__region--first .slick-for > a")
          .bind("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            var _this = $(this);
            var index = _this.index();

            blockBundleQuestions
              .find(
                ".layout--twocol-section .layout__region--second .slick__slider"
              )
              .slick("slickGoTo", index)
              .on("afterChange", function () {
                _this.parent().find("a").removeClass("active-trail");
                _this.addClass("active-trail");
              });
          });
      })
    }
  };

})(jQuery, Drupal, drupalSettings, once);
