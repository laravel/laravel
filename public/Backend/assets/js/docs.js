/*
 * MoonCake v1.3.1 - Documentation JS
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

		if( $.fn.affix ) {
			var initialTopOffset = $( '#docs-nav-wrap' ).offset().top, 
				docsNav = $( '#docs-nav' );

			docsNav.affix({
				offset: {
					top: function() { return initialTopOffset; }
				}
			});
		}

		window.prettyPrint && prettyPrint();
	});
	
}) (jQuery, window, document);