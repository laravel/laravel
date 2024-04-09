/**
 * Send Invoice Offcanvas
 */

'use strict';

(function () {
  // Send invoice textarea
  const invoiceMsg = document.querySelector('#invoice-message');

  const trimMsg = invoiceMsg.textContent.replace(/^\s+|\s+$/gm, '');

  invoiceMsg.value = trimMsg;
})();
