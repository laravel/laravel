/**
 *  Modal Example Wizard
 */

'use strict';

$(function () {
  // Modal id
  const appModal = document.getElementById('createApp');

  // Credit Card
  const creditCardMask1 = document.querySelector('.app-credit-card-mask'),
    expiryDateMask1 = document.querySelector('.app-expiry-date-mask'),
    cvvMask1 = document.querySelector('.app-cvv-code-mask');
  let cleave;

  // Cleave JS card Mask
  function initCleave() {
    if (creditCardMask1) {
      cleave = new Cleave(creditCardMask1, {
        creditCard: true,
        onCreditCardTypeChanged: function (type) {
          if (type != '' && type != 'unknown') {
            document.querySelector('.app-card-type').innerHTML =
              '<img src="' + assetsPath + 'img/icons/payments/' + type + '-cc.png" class="cc-icon-image" height="28"/>';
          } else {
            document.querySelector('.app-card-type').innerHTML = '';
          }
        }
      });
    }
  }

  // Expiry Date Mask
  if (expiryDateMask1) {
    new Cleave(expiryDateMask1, {
      date: true,
      delimiter: '/',
      datePattern: ['m', 'y']
    });
  }

  // CVV
  if (cvvMask1) {
    new Cleave(cvvMask1, {
      numeral: true,
      numeralPositiveOnly: true
    });
  }
  appModal.addEventListener('show.bs.modal', function (event) {
    const wizardCreateApp = document.querySelector('#wizard-create-app');
    if (typeof wizardCreateApp !== undefined && wizardCreateApp !== null) {
      // Wizard next prev button
      const wizardCreateAppNextList = [].slice.call(wizardCreateApp.querySelectorAll('.btn-next'));
      const wizardCreateAppPrevList = [].slice.call(wizardCreateApp.querySelectorAll('.btn-prev'));
      const wizardCreateAppBtnSubmit = wizardCreateApp.querySelector('.btn-submit');

      const createAppStepper = new Stepper(wizardCreateApp, {
        linear: false
      });

      if (wizardCreateAppNextList) {
        wizardCreateAppNextList.forEach(wizardCreateAppNext => {
          wizardCreateAppNext.addEventListener('click', event => {
            createAppStepper.next();
            initCleave();
          });
        });
      }
      if (wizardCreateAppPrevList) {
        wizardCreateAppPrevList.forEach(wizardCreateAppPrev => {
          wizardCreateAppPrev.addEventListener('click', event => {
            createAppStepper.previous();
            initCleave();
          });
        });
      }

      if (wizardCreateAppBtnSubmit) {
        wizardCreateAppBtnSubmit.addEventListener('click', event => {
          alert('Submitted..!!');
        });
      }
    }
  });
});
