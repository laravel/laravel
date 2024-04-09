/**
 *  Form Wizard
 */

'use strict';

$(function () {
  const select2 = $('.select2'),
    selectPicker = $('.selectpicker');

  // Bootstrap select
  if (selectPicker.length) {
    selectPicker.selectpicker();
  }

  // select2
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>');
      $this.select2({
        placeholder: 'Select value',
        dropdownParent: $this.parent()
      });
    });
  }
});
(function () {
  // Numbered Wizard
  // --------------------------------------------------------------------
  const wizardNumbered = document.querySelector('.wizard-numbered'),
    wizardNumberedBtnNextList = [].slice.call(wizardNumbered.querySelectorAll('.btn-next')),
    wizardNumberedBtnPrevList = [].slice.call(wizardNumbered.querySelectorAll('.btn-prev')),
    wizardNumberedBtnSubmit = wizardNumbered.querySelector('.btn-submit');

  if (typeof wizardNumbered !== undefined && wizardNumbered !== null) {
    const numberedStepper = new Stepper(wizardNumbered, {
      linear: false
    });
    if (wizardNumberedBtnNextList) {
      wizardNumberedBtnNextList.forEach(wizardNumberedBtnNext => {
        wizardNumberedBtnNext.addEventListener('click', event => {
          numberedStepper.next();
        });
      });
    }
    if (wizardNumberedBtnPrevList) {
      wizardNumberedBtnPrevList.forEach(wizardNumberedBtnPrev => {
        wizardNumberedBtnPrev.addEventListener('click', event => {
          numberedStepper.previous();
        });
      });
    }
    if (wizardNumberedBtnSubmit) {
      wizardNumberedBtnSubmit.addEventListener('click', event => {
        alert('Submitted..!!');
      });
    }
  }

  // Vertical Wizard
  // --------------------------------------------------------------------
  const wizardVertical = document.querySelector('.wizard-vertical'),
    wizardVerticalBtnNextList = [].slice.call(wizardVertical.querySelectorAll('.btn-next')),
    wizardVerticalBtnPrevList = [].slice.call(wizardVertical.querySelectorAll('.btn-prev')),
    wizardVerticalBtnSubmit = wizardVertical.querySelector('.btn-submit');

  if (typeof wizardVertical !== undefined && wizardVertical !== null) {
    const verticalStepper = new Stepper(wizardVertical, {
      linear: false
    });
    if (wizardVerticalBtnNextList) {
      wizardVerticalBtnNextList.forEach(wizardVerticalBtnNext => {
        wizardVerticalBtnNext.addEventListener('click', event => {
          verticalStepper.next();
        });
      });
    }
    if (wizardVerticalBtnPrevList) {
      wizardVerticalBtnPrevList.forEach(wizardVerticalBtnPrev => {
        wizardVerticalBtnPrev.addEventListener('click', event => {
          verticalStepper.previous();
        });
      });
    }

    if (wizardVerticalBtnSubmit) {
      wizardVerticalBtnSubmit.addEventListener('click', event => {
        alert('Submitted..!!');
      });
    }
  }

  // Modern Wizard
  // --------------------------------------------------------------------
  const wizardModern = document.querySelector('.wizard-modern-example'),
    wizardModernBtnNextList = [].slice.call(wizardModern.querySelectorAll('.btn-next')),
    wizardModernBtnPrevList = [].slice.call(wizardModern.querySelectorAll('.btn-prev')),
    wizardModernBtnSubmit = wizardModern.querySelector('.btn-submit');
  if (typeof wizardModern !== undefined && wizardModern !== null) {
    const modernStepper = new Stepper(wizardModern, {
      linear: false
    });
    if (wizardModernBtnNextList) {
      wizardModernBtnNextList.forEach(wizardModernBtnNext => {
        wizardModernBtnNext.addEventListener('click', event => {
          modernStepper.next();
        });
      });
    }
    if (wizardModernBtnPrevList) {
      wizardModernBtnPrevList.forEach(wizardModernBtnPrev => {
        wizardModernBtnPrev.addEventListener('click', event => {
          modernStepper.previous();
        });
      });
    }
    if (wizardModernBtnSubmit) {
      wizardModernBtnSubmit.addEventListener('click', event => {
        alert('Submitted..!!');
      });
    }
  }

  // Modern Vertical Wizard
  // --------------------------------------------------------------------
  const wizardModernVertical = document.querySelector('.wizard-modern-vertical'),
    wizardModernVerticalBtnNextList = [].slice.call(wizardModernVertical.querySelectorAll('.btn-next')),
    wizardModernVerticalBtnPrevList = [].slice.call(wizardModernVertical.querySelectorAll('.btn-prev')),
    wizardModernVerticalBtnSubmit = wizardModernVertical.querySelector('.btn-submit');
  if (typeof wizardModernVertical !== undefined && wizardModernVertical !== null) {
    const modernVerticalStepper = new Stepper(wizardModernVertical, {
      linear: false
    });
    if (wizardModernVerticalBtnNextList) {
      wizardModernVerticalBtnNextList.forEach(wizardModernVerticalBtnNext => {
        wizardModernVerticalBtnNext.addEventListener('click', event => {
          modernVerticalStepper.next();
        });
      });
    }
    if (wizardModernVerticalBtnPrevList) {
      wizardModernVerticalBtnPrevList.forEach(wizardModernVerticalBtnPrev => {
        wizardModernVerticalBtnPrev.addEventListener('click', event => {
          modernVerticalStepper.previous();
        });
      });
    }
    if (wizardModernVerticalBtnSubmit) {
      wizardModernVerticalBtnSubmit.addEventListener('click', event => {
        alert('Submitted..!!');
      });
    }
  }
})();
