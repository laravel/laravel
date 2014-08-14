/*
 * MoonCake v1.3.1 - Mail Page Demo JS
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
		
		if( $.fn.dataTable ) {
			
			$( '.mail .mail-pages table' ).dataTable({
				"sDom": "t<'dt_footer'<'row-fluid'<'span6'i><'span6'p>>>", 
				"aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0 ] } ], 
				"aaSorting": []
			});
		}
	});
	
}) (jQuery, window, document);