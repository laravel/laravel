/*
 * MoonCake v1.3.1 - Gallery Demo JS
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
	
	function support() {
		var vendorPrefixes = "O Ms Webkit Moz".split( ' ' ),	
			i = vendorPrefixes.length, support = true, 
			divStyle = document.createElement('div').style;

		while( i-- ) {
			for(var a = 0, support = true; a < arguments.length; a++) {
				support = (vendorPrefixes[ i ] + arguments[ a ] in divStyle);
			}

			if( support ) return true;
		}

		return false;
	}
	
	$( document ).ready( function( e ) {
		// Freetile
		if( $.fn.freetile ) {
			var f = $( '.gallery > ul' ).freetile({
				selector: 'li', 
				animate: support( 'Transition' )
			}).on( 'resize', function() {
				f.freetile( 'layout' );
			});
		}

		$.fn.prettyPhoto && $("a[rel^='prettyPhoto']").prettyPhoto();
	});
	
}) (jQuery, window, document);