/**
 * @file
*/
 (function ($, Drupal, drupalSettings) {

    var countMarketo = 0;

    Drupal.behaviors.marketoForm = {
      attach: function (context, settings) {
        // Getting a marketo form
        var checkExist = setInterval(function() {
          countMarketo++;
          if ($('form[id^="mk"] :input', context).length && countMarketo < 30) {
             var $form = $('form[id^="mk"] :input', context).once('marketo-event-handlers');
             $form
              .each(function () {
                // Attach blur and keyup event handlers.
                $(this)
                  .on('blur', function () {
                    if( $(this).val().replace(/\s+/g, '').length > 0) {                      
                      $(this).siblings('label').children('.mktoAsterix').hide();
                    }
                    else {
                      $(this).siblings('label').children('.mktoAsterix').show();
                    }
                  }).on('keyup', function () {
                    if ($(this).val().replace(/\s+/g, '').length  > 0) {                      
                      $(this).siblings('label').children('.mktoAsterix').hide();
                    }
                  });
              });
             clearInterval(checkExist);
          }
       }, 500); // check every 100ms
      }
    };
  })(jQuery, Drupal, drupalSettings);
  