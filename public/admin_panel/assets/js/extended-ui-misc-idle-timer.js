/**
 * Ideal Timer (jquery)
 */

'use strict';

$(function () {
  var timerDoc = $('#document-Status'),
    btnPause = $('#document-Pause'),
    btnResume = $('#document-Resume'),
    btnElapsed = $('#document-Elapsed'),
    btnDestroy = $('#document-Destroy'),
    btnInit = $('#document-Init');

  // Document 5 Sec Timeout
  // --------------------------------------------------------------------
  if (timerDoc.length) {
    var docTimeout = 5000;
    // idle/active events
    $(document).on('idle.idleTimer', function (event, elem, obj) {
      timerDoc
        .val(function (i, value) {
          return value + 'Idle @ ' + moment().format() + ' \n';
        })
        .removeClass('alert-success')
        .addClass('alert-warning');
    });
    $(document).on('active.idleTimer', function (event, elem, obj, e) {
      timerDoc
        .val(function (i, value) {
          return value + 'Active [' + e.type + '] [' + e.target.nodeName + '] @ ' + moment().format() + ' \n';
        })
        .addClass('alert-success')
        .removeClass('alert-warning');
    });

    // button events
    btnPause.on('click', function () {
      // Pause
      $(document).idleTimer('pause');
      timerDoc.val(function (i, value) {
        return value + 'Paused @ ' + moment().format() + ' \n';
      });
      $(this).blur();
      return false;
    });
    btnResume.on('click', function () {
      // Resume
      $(document).idleTimer('resume');
      timerDoc.val(function (i, value) {
        return value + 'Resumed @ ' + moment().format() + ' \n';
      });
      $(this).blur();
      return false;
    });
    btnElapsed.on('click', function () {
      // Elapsed
      timerDoc.val(function (i, value) {
        return value + 'Elapsed (since becoming active): ' + $(document).idleTimer('getElapsedTime') + ' \n';
      });
      $(this).blur();
      return false;
    });
    btnDestroy.on('click', function () {
      // Destroy
      $(document).idleTimer('destroy');
      timerDoc
        .val(function (i, value) {
          return value + 'Destroyed: @ ' + moment().format() + ' \n';
        })
        .removeClass('alert-success')
        .removeClass('alert-warning');
      $(this).blur();
      return false;
    });
    btnInit.on('click', function () {
      // Initialize
      // show init with object
      $(document).idleTimer({
        timeout: docTimeout
      });
      timerDoc.val(function (i, value) {
        return value + 'Init: @ ' + moment().format() + ' \n';
      });

      // Apply classes for default state
      if ($(document).idleTimer('isIdle')) {
        timerDoc.removeClass('alert-success').addClass('alert-warning');
      } else {
        timerDoc.addClass('alert-success').removeClass('alert-warning');
      }
      $(this).blur();
      return false;
    });

    // Clear old statuses
    timerDoc.val('');

    // Start timeout, passing no options
    $(document).idleTimer(docTimeout);

    // style based on state
    if ($(document).idleTimer('isIdle')) {
      timerDoc
        .val(function (i, value) {
          return value + 'Initial Idle State @ ' + moment().format() + ' \n';
        })
        .removeClass('alert-success')
        .addClass('alert-warning');
    } else {
      timerDoc
        .val(function (i, value) {
          return value + 'Initial Active State @ ' + moment().format() + ' \n';
        })
        .addClass('alert-success')
        .removeClass('alert-warning');
    }
  }

  // Element 3 Sec Timeout
  // --------------------------------------------------------------------
  var elementTimer = $('#element-Status'),
    btnReset = $('#element-Reset'),
    btnRemaining = $('#element-Remaining'),
    btnLastActive = $('#element-LastActive'),
    btnState = $('#element-State');
  if (elementTimer.length) {
    var elTimeout = 3000;
    // idle/active events
    elementTimer.on('idle.idleTimer', function (event, elem, obj) {
      event.stopPropagation();

      elementTimer
        .val(function (i, value) {
          return value + 'Idle @ ' + moment().format() + ' \n';
        })
        .removeClass('alert-success')
        .addClass('alert-warning');
    });
    elementTimer.on('active.idleTimer', function (event) {
      event.stopPropagation();

      elementTimer
        .val(function (i, value) {
          return value + 'Active @ ' + moment().format() + ' \n';
        })
        .addClass('alert-success')
        .removeClass('alert-warning');
    });

    // button events
    btnReset.on('click', function () {
      // Reset
      elementTimer.idleTimer('reset').val(function (i, value) {
        return value + 'Reset @ ' + moment().format() + ' \n';
      });

      // classes for default state
      if ($('#element-Status').idleTimer('isIdle')) {
        elementTimer.removeClass('alert-success').addClass('alert-warning');
      } else {
        elementTimer.addClass('alert-success').removeClass('alert-warning');
      }
      $(this).blur();
      return false;
    });
    btnRemaining.on('click', function () {
      // Remaining
      elementTimer.val(function (i, value) {
        return value + 'Remaining: ' + elementTimer.idleTimer('getRemainingTime') + ' \n';
      });
      $(this).blur();
      return false;
    });
    btnLastActive.on('click', function () {
      // Last Active
      elementTimer.val(function (i, value) {
        return value + 'LastActive: ' + elementTimer.idleTimer('getLastActiveTime') + ' \n';
      });
      $(this).blur();
      return false;
    });
    btnState.on('click', function () {
      // State
      elementTimer.val(function (i, value) {
        return value + 'State: ' + ($('#element-Status').idleTimer('isIdle') ? 'idle' : 'active') + ' \n';
      });
      $(this).blur();
      return false;
    });

    // Clear value if cached & start time
    elementTimer.val('').idleTimer(elTimeout);

    // show initial state
    if (elementTimer.idleTimer('isIdle')) {
      elementTimer
        .val(function (i, value) {
          return value + 'Initial Idle @ ' + moment().format() + ' \n';
        })
        .removeClass('alert-success')
        .addClass('alert-warning');
    } else {
      elementTimer
        .val(function (i, value) {
          return value + 'Initial Active @ ' + moment().format() + ' \n';
        })
        .addClass('alert-success')
        .removeClass('alert-warning');
    }
  }
});
