/*
 * MoonCake v1.3.1 - Alerts Demo JS
 *
 * This file is part of MoonCake, an Admin template build for sale at ThemeForest.
 * For questions, suggestions or support request, please mail me at maimairel@yahoo.com
 *
 * Development Started:
 * July 28, 2012
 * Last Update:
 * December 07, 2012
 *
 */

;(function( $, window, document, undefined ) {

	$(document).ready(function() {

		if($.fn.slider) {

			$( '#widget-slider-ex' ).slider({
				range: 'min', 
				value: 36
			});

			$( '#widget-slider-ex-2' ).slider({
				range: 'min', 
				value: 71
			});
		}

	});
	
}) (jQuery, window, document);