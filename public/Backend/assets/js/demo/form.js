/*
 * MoonCake v1.3.1 - Form Demo JS
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
			
	var demos = {
	};

	$(window).load(function() { });
	
	$(document).ready(function() {
		
		// When all page resources has finished loading
		
		if($.fn.select2) {
			
			var opts = [
				{}, 
				{ minimumInputLength: 3 }, 
				{ tags: ['Sport', 'Gadget', 'Politics'] }
			];

			$.each(opts, function(i, o) {
				$('.select2-select-0' + i).select2(o);
			});			
		}

		$.fn.autosize && $('.autosize').autosize();
		
		if( $.fn.picklist ) {
			
			$( '#picklist-ex' ).picklist({
				addAllLabel: '<i class="icon-caret-right"></i><i class="icon-caret-right"></i>', 
				addLabel: '<i class="icon-caret-right"></i>', 
				removeAllLabel: '<i class="icon-caret-left"></i><i class="icon-caret-left"></i>', 
				removeLabel: '<i class="icon-caret-left"></i>'
			});
		}

		if( $.fn.autocomplete ) {
			var d = ["Alabama","Alaska","Arizona","Arkansas","California","Colorado","Connecticut","Delaware","Florida","Georgia","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Dakota","North Carolina","Ohio","Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia","Wisconsin","Wyoming"];
			$('#autocomplete-ex').autocomplete({ source: d });
		}

		if( $.fn.spinner ) {

            $('#spinner').spinner();

            $('#spinner-decimal').spinner({
                step: 0.01,
                numberFormat: "n"
            });

            $.widget( "ui.timespinner", $.ui.spinner, {
                options: {
                    // seconds
                    step: 60 * 1000,
                    // hours
                    page: 60
                },
         
                _parse: function( value ) {
                    if ( typeof value === "string" ) {
                        // already a timestamp
                        if ( Number( value ) == value ) {
                            return Number( value );
                        }
                        return +Globalize.parseDate( value );
                    }
                    return value;
                },
         
                _format: function( value ) {
                    return Globalize.format( new Date(value), "t" );
                }
            });

            $( "#spinner-time" ).timespinner({
                value: new Date().getTime()
            });
		}
	});
	
}) (jQuery, window, document);