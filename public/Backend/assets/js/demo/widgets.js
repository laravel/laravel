/*
 * MoonCake v1.3.1 - Widget Demo JS
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
		
		// When all page resources has finished loading
		
		if($.fn.tweet) {
			
			$( '#tweets-widget').tweet({
	            username: "envato_support", 
	            avatar_size: 60, 
	            template: '{avatar}{user}{time}{join} {text}{reply_action}{retweet_action}'
	        });
		}

		if($.fn.sortable) {
			$('.sortable-list ul').sortable({
				placeholder: 'placeholder', 
				forcePlaceholderSize: true, 
				update: function(event, ui) {
					$(ui.item).parents('.sortable-list ul').children('li').each(function(i, li) {
						$('.info .order', $(li)).text('Item Order: ' + (i + 1));
					});
				}
			});
		}
	});

	$(window).load(function() {

		if($.fn.gmap) {
            $('#gmap-canvas').gmap({
            	'center': getLatLng(), 
            	zoom: 15
            });

            if($.fn.autocomplete) {
	            $( '#gmap-canvas' ).gmap( 'autocomplete', $( '#gmap-search-key' ), function( ui ) {
	            	setPosition(ui.item.position);
	            });
            }
            
            $( '#gmap-getpos' ).on('click', function(e) {
	            // Try to get the user's position
	            $('#gmap-canvas').gmap('getCurrentPosition', function(position, status) {
	                    if ( status === 'OK' ) {
	                    	setPosition(position);
	                    } else {
                            $.pnotify({
                            	title: 'Google Maps', 
                            	text: 'We failed to find your location. Please try again.', 
                            	type:'error'
                            });
	                    }
	            });
	            e.preventDefault();
            });

            function setPosition(position) {
            	$('#gmap-canvas').gmap( 'option', 'center', position);
            	$('#gmap-canvas').gmap( 'clear', 'markers');
            	$('#gmap-canvas').gmap( 'addMarker', { bounds: true, position: position, 'animation': google.maps.Animation.DROP });
            }
            
            // Get the google loaders client location
            // If it fails, return some defult value
            function getLatLng() {
                    if ( google && google.loader && google.loader.ClientLocation != null )
                    	return new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);   
                    return new google.maps.LatLng( -6.175369, 106.827106 );
            }
		}
	});
	
}) (jQuery, window, document);