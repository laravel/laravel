/**
 *  Page auth two steps
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    let maskWrapper = document.querySelector('.numeral-mask-wrapper');

    for (let pin of maskWrapper.children) {
      pin.onkeyup = function (e) {
        // While entering value, go to next
        if (pin.nextElementSibling) {
          if (this.value.length === parseInt(this.attributes['maxlength'].value)) {
            pin.nextElementSibling.focus();
          }
        }

        // While deleting entered value, go to previous
        // Delete using backspace and delete
        if (pin.previousElementSibling) {
          if (e.keyCode === 8 || e.keyCode === 46) {
            pin.previousElementSibling.focus();
          }
        }
      };
    }

    const twoStepsForm = document.querySelector('#twoStepsForm');

    // Form validation for Add new record
    if (twoStepsForm) {
      const fv = FormValidation.formValidation(twoStepsForm, {
        fields: {
          otp: {
            validators: {
              notEmpty: {
                message: 'Please enter otp'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: '.mb-3'
          }),
          submitButton: new FormValidation.plugins.SubmitButton(),

          defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
          autoFocus: new FormValidation.plugins.AutoFocus()
        }
      });

      const numeralMaskList = twoStepsForm.querySelectorAll('.numeral-mask');
      const keyupHandler = function () {
        let otpFlag = true,
          otpVal = '';
        numeralMaskList.forEach(numeralMaskEl => {
          if (numeralMaskEl.value === '') {
            otpFlag = false;
            twoStepsForm.querySelector('[name="otp"]').value = '';
          }
          otpVal = otpVal + numeralMaskEl.value;
        });
        if (otpFlag) {
          twoStepsForm.querySelector('[name="otp"]').value = otpVal;
        }
      };
      numeralMaskList.forEach(numeralMaskEle => {
        numeralMaskEle.addEventListener('keyup', keyupHandler);
      });
    }
  })();
});
