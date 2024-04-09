/**
 * Add new credit card
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    // Variables
    const creditCardMask = document.querySelector('.credit-card-mask'),
      expiryDateMask = document.querySelector('.expiry-date-mask'),
      cvvMask = document.querySelector('.cvv-code-mask'),
      btnReset = document.querySelector('.btn-reset');
    let cleave;

    // Credit Card
    function initCleave() {
      if (creditCardMask) {
        cleave = new Cleave(creditCardMask, {
          creditCard: true,
          onCreditCardTypeChanged: function (type) {
            if (type != '' && type != 'unknown') {
              document.querySelector('.card-type').innerHTML =
                '<img src="' +
                assetsPath +
                'img/icons/payments/' +
                type +
                '-cc.png" class="cc-icon-image" height="28"/>';
            } else {
              document.querySelector('.card-type').innerHTML = '';
            }
          }
        });
      }
    }

    // Init cleave on show modal (To fix the cc image issue)
    let addNewCCModal = document.getElementById('addNewCCModal');
    addNewCCModal.addEventListener('show.bs.modal', function (event) {
      initCleave();
    });

    // Expiry Date Mask
    if (expiryDateMask) {
      new Cleave(expiryDateMask, {
        date: true,
        delimiter: '/',
        datePattern: ['m', 'y']
      });
    }

    // CVV
    if (cvvMask) {
      new Cleave(cvvMask, {
        numeral: true,
        numeralPositiveOnly: true
      });
    }

    // Credit card form validation
    FormValidation.formValidation(document.getElementById('addNewCCForm'), {
      fields: {
        modalAddCard: {
          validators: {
            notEmpty: {
              message: 'Please enter your credit card number'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-12'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        // Submit the form when all fields are valid
        // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      },
      init: instance => {
        instance.on('plugins.message.placed', function (e) {
          //* Move the error message out of the `input-group` element
          if (e.element.parentElement.classList.contains('input-group')) {
            e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
          }
        });
      }
    }).on('plugins.message.displayed', function (e) {
      if (e.element.parentElement.classList.contains('input-group')) {
        //* Move the error message out of the `input-group` element
        e.element.parentElement.insertAdjacentElement('afterend', e.messageElement.parentElement);
      }
    });

    // reset card image on click of cancel
    btnReset.addEventListener('click', function (e) {
      // blank '.card-type' innerHTML to remove image
      document.querySelector('.card-type').innerHTML = '';
      // destroy cleave and init again on modal open
      cleave.destroy();
    });
  })();
});
