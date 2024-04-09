/**
 * Two Factor Authentication
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const phoneMaskList = document.querySelectorAll('#twoFactorAuthInputSms');

    // Phone Number
    if (phoneMaskList) {
      phoneMaskList.forEach(function (phoneMask) {
        new Cleave(phoneMask, {
          phone: true,
          phoneRegionCode: 'US'
        });
      });
    }
  })();
});
