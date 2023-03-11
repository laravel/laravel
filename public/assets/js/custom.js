/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 * 
 */

"use strict";

$('.numberk').on('input', function (event) {
this.value = this.value.replace(/[^0-9.]/g, '');
}); 
