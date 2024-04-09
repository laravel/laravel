/**
 * Pricing
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (event) {
  (function () {
    const priceDurationToggler = document.querySelector('.price-duration-toggler'),
      priceMonthlyList = [].slice.call(document.querySelectorAll('.price-monthly')),
      priceYearlyList = [].slice.call(document.querySelectorAll('.price-yearly'));

    function togglePrice() {
      if (priceDurationToggler.checked) {
        // If checked
        priceYearlyList.map(function (yearEl) {
          yearEl.classList.remove('d-none');
        });
        priceMonthlyList.map(function (monthEl) {
          monthEl.classList.add('d-none');
        });
      } else {
        // If not checked
        priceYearlyList.map(function (yearEl) {
          yearEl.classList.add('d-none');
        });
        priceMonthlyList.map(function (monthEl) {
          monthEl.classList.remove('d-none');
        });
      }
    }
    // togglePrice Event Listener
    togglePrice();

    priceDurationToggler.onchange = function () {
      togglePrice();
    };
  })();
});
