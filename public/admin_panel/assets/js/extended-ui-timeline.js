/**
 * Timeline
 */

'use strict';

(function () {
  // Init Animation on scroll
  AOS.init({
    disable: function () {
      const maxWidth = 1024;
      return window.innerWidth < maxWidth;
    },
    once: true
  });
})();
