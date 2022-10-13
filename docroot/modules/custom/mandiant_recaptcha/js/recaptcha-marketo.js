/**
 * @file
 * Attaches behavior for integration between recaptcha and marketo form.
 */
 (function ($, Drupal, drupalSettings) {

  /*!
  * reCAPTCHA handling
  * Version: 1.0
  */
  function recaptchaSuccess() {
      $(".g-recaptcha").removeClass("mktoInvalid");
  }

  var count = 0;

  Drupal.behaviors.recaptchaMarketoCall = {
    attach: function (context, settings) {
    // The first one is the correct one.
    if (typeof drupalSettings.mandiantRecaptcha.recaptchaKey == "undefined" || drupalSettings.mandiantRecaptcha.recaptchaKey.length == 0 ) {
      return;
    }

    // Must be global so reCAPTCHA can access it as a callback.
    window.CaptchaCallback = function() {
    var timer = setInterval(function () {
      count++;
      // Looking for grecaptcha several times.
      if (grecaptcha !== undefined && count < 30) {
        var normalRecaptchaId = grecaptcha.render('normalRecaptcha', {'sitekey': drupalSettings.mandiantRecaptcha.recaptchaKey, 'callback': 'recaptchaSuccess', 'size': 'normal'});
        var compactRecaptchaId = grecaptcha.render('compactRecaptcha', {'sitekey': drupalSettings.mandiantRecaptcha.recaptchaKey, 'callback': 'recaptchaSuccess', 'size': 'compact'});
        // Getting a marketo form
        var mkForm = $( "form[id^='mk']" );
        // Only if marketo form exists.
        if (mkForm.length > 0) {
          MktoForms2.whenReady(function(form) {
            var formEl = form.getFormElem()[0],
              $submitRow = $(".mktoButtonRow").last(),
              recaptchaEl = document.querySelector('.g-recaptcha');
              $recaptcha = $(".g-recaptcha"),
              $recaptchaNormal = $("#normalRecaptcha"),
              $recaptchaCompact = $("#compactRecaptcha"),
              $lastRow = $(".mktoFormRow").last(),
              $errorBubble = null;
            form.submittable(false);
            // force resize reCAPTCHA frame
            recaptchaEl.querySelector('IFRAME').setAttribute('width', 'auto');
            // move reCAPTCHA inside form container
            $lastRow.find("> .mktoFormCol").before($recaptcha);

            // Make reCAPTCHA share the width.
            $lastRow.find(".mktoFormCol").addClass("halfWidth");
            $recaptcha.addClass("loaded");
            var scale = $recaptchaNormal.show().width()/ 300;
            // If needed scale down the recaptcha so it fits, but not too much.
            if (scale >= .80) {
              $recaptchaNormal.css({'transform':'scale(' + Math.min(scale,1) + ')','-webkit-transform':'scale(' + Math.min(scale,1) + ')'});
              $recaptchaNormal.css("display","inline-block");
              $recaptchaCompact.hide();
            }
            else {
              $recaptchaNormal.hide();
              $recaptchaCompact.css("display","inline-block");
            }

            // @todo : Check if this line make sense in others devices.
            // Move the submit button closer to the content.
            // $submitRow.appendTo($lastRow.find(".mktoFormCol"));
            form.onValidate(function(builtInValidation) {
              if (!builtInValidation)
                return;

              var recaptchaResponseNormal = grecaptcha.getResponse(normalRecaptchaId);
              var recaptchaResponseCompact = grecaptcha.getResponse(compactRecaptchaId);
              if (!recaptchaResponseNormal && !recaptchaResponseCompact) {
                $recaptcha.addClass('mktoInvalid');
                $errorBubble = $('<div class="mktoError"><div class="mktoErrorArrowWrap"><div class="mktoErrorArrow"></div></div><div class="mktoErrorMsg">This field is required.</div></div>');
                $recaptcha.append($errorBubble);
              }
              else {
                $recaptcha.removeClass('mktoInvalid');
                if ($errorBubble != null) {
                  $errorBubble.hide();
                }
                form.addHiddenFields({
                  lastRecaptchaUserInput : (recaptchaResponseNormal ? recaptchaResponseNormal : recaptchaResponseCompact)
                });
                form.submittable(true);
              }
            });
          });
        }
      }
      clearInterval(timer)
      }, 500);
    };
    }
  };
})(jQuery, Drupal, drupalSettings);

