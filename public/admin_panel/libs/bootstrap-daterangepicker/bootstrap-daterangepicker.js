import 'bootstrap-daterangepicker/daterangepicker';

// Patch detect when weeks are shown

const fnDaterangepicker = $.fn.daterangepicker;

$.fn.daterangepicker = function (options, callback) {
  fnDaterangepicker.call(this, options, callback);

  if (options && (options.showWeekNumbers || options.showISOWeekNumbers)) {
    this.each(function () {
      const instance = $(this).data('daterangepicker');
      console.log(instance);
      if (instance && instance.container) instance.container.addClass('with-week-numbers');
    });
  }

  return this;
};
