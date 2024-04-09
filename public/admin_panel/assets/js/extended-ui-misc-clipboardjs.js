/**
 * Clipboard
 */

'use strict';

(function () {
  const clipboardList = [].slice.call(document.querySelectorAll('.clipboard-btn'));
  if (ClipboardJS) {
    clipboardList.map(function (clipboardEl) {
      const clipboard = new ClipboardJS(clipboardEl);
      clipboard.on('success', function (e) {
        if (e.action == 'copy') {
          toastr['success']('', 'Copied to Clipboard!!');
        }
      });
    });
  } else {
    clipboardList.map(function (clipboardEl) {
      clipboardEl.setAttribute('disabled', true);
    });
  }
})();
