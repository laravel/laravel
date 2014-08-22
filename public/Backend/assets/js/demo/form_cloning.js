
/*
 * MoonCake v1.3.1 - Form Cloning Demo JS
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
		if( $.fn.sheepIt ) {

			$('#input_cloning').sheepIt({
				separator: '', 
				maxFormsCount: 5
			});
			$('#oinput_cloning').sheepIt({
				separator: '', 
				iniFormsCount: 0, 
				minFormsCount: 0, 
				maxFormsCount: 5
			});
			$('#event_cloning').sheepIt({
				separator: '', 
				data: [
					{ ename: 'Halloween Party', estart: '10/31/2012', eend: '11/02/2012' }, 
					{ ename: 'Annexation Ceremony', estart: '11/10/2012', eend: '11/12/2012' }
				], 
				afterAdd: function(source, newform) {
					if( $.fn.datepicker ) {
						var from = $(newform).find('.datepicker_s'), 
							to = $(newform).find('.datepicker_e');

						from.datepicker({
							defaultDate: "+1w",
							numberOfMonths: 1, 
							showOtherMonths: true, 
							onSelect: function( selectedDate ) {
								to.datepicker( "option", "minDate", selectedDate );
							}
						});
						
						to.datepicker({
							defaultDate: "+1w",
							numberOfMonths: 1, 
							showOtherMonths: true, 
							onSelect: function( selectedDate ) {
								from.datepicker( "option", "maxDate", selectedDate );
							}
						});

					}
				}
			});
		}
	});
	
	
}) (jQuery, window, document);