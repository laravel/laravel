/**
 * Edit credit card
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const editCreditCardMaskEdit = document.querySelector('.credit-card-mask-edit'),
      editExpiryDateMaskEdit = document.querySelector('.expiry-date-mask-edit'),
      editCVVMaskEdit = document.querySelector('.cvv-code-mask-edit');

    // Credit Card
    if (editCreditCardMaskEdit) {
      new Cleave(editCreditCardMaskEdit, {
        creditCard: true,
        onCreditCardTypeChanged: function (type) {
          if (type != '' && type != 'unknown') {
            document.querySelector('.card-type-edit').innerHTML =
              '<img src="' + assetsPath + 'img/icons/payments/' + type + '-cc.png" height="28"/>';
          } else {
            document.querySelector('.card-type-edit').innerHTML = '';
          }
        }
      });
    }

    // Expiry Date MaskEdit
    if (editExpiryDateMaskEdit) {
      new Cleave(editExpiryDateMaskEdit, {
        date: true,
        delimiter: '/',
        datePattern: ['m', 'y']
      });
    }

    // CVV MaskEdit
    if (editCVVMaskEdit) {
      new Cleave(editCVVMaskEdit, {
        numeral: true,
        numeralPositiveOnly: true
      });
    }

    // Credit card form validation
    FormValidation.formValidation(document.getElementById('editCCForm'), {
      fields: {
        modalEditCard: {
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
    });
  })();
});
