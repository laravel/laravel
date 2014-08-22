/*
 * MoonCake v1.3.1 - Statistic Demo JS
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

		var cs = $('#cs-api-sample');

		$( '#cs-val-btn' ).on( 'click', function() {
			cs.circularStat( 'option', 'value', (Math.random() * 75 + 25 ));
		});

		$( '#cs-rad-btn' ).on( 'click', function() {
			cs.circularStat( 'option', 'radius', (Math.random() * 68 + 32 ));
		});

		$( '#cs-dec-btn' ).on( 'click', function() {
			cs.circularStat( 'option', 'decimals', Math.floor(Math.random() * 4 ));
		});

		$( '#cs-th-btn' ).on( 'click', function() {
			cs.circularStat( 'option', 'thickness', (cs.circularStat( 'option', 'radius') * 0.2 ) + Math.random() * (cs.circularStat( 'option', 'radius') * 0.2 ) );
		});

		$( '#cs-col-btn' ).on( 'click', function() {

			// http://paulirish.com/2009/random-hex-color-code-snippets/
			cs.circularStat( 'option', 'fillColor', '#'+(Math.random()*0xFFFFFF<<0).toString(16) );
		});
	});
	
}) (jQuery, window, document);