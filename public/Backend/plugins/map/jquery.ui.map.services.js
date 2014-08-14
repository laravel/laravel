 /*!
 * jQuery UI Google Map 3.0-rc
 * http://code.google.com/p/jquery-ui-map/
 * Copyright (c) 2010 - 2012 Johan Säll Larsson
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 *
 * Depends:
 *		jquery.ui.map.js
 */
( function($) {
	
	$.extend($.ui.gmap.prototype, {
		
		/**
		 * Computes directions between two or more places.
		 * @param directionsRequest:google.maps.DirectionsRequest
		 * @param directionsRendererOptions:google.maps.DirectionsRendererOptions (optional)
		 * @param callback:function(result:google.maps.DirectionsResult, status:google.maps.DirectionsStatus)
		 * @see http://code.google.com/intl/sv-SE/apis/maps/documentation/javascript/reference.html#DirectionsRequest
		 * @see http://code.google.com/intl/sv-SE/apis/maps/documentation/javascript/reference.html#DirectionsRendererOptions
		 * @see http://code.google.com/intl/sv-SE/apis/maps/documentation/javascript/reference.html#DirectionsResult
		 */
		displayDirections: function(directionsRequest, directionsRendererOptions, callback) {
			var self = this;		
			var directionService = this.get('services > DirectionsService', new google.maps.DirectionsService());
			var directionRenderer = this.get('services > DirectionsRenderer', new google.maps.DirectionsRenderer());
			if ( directionsRendererOptions ) {
				directionRenderer.setOptions(directionsRendererOptions);
			}
			directionService.route(directionsRequest, function(results, status) {
				if ( status === 'OK' ) {
					directionRenderer.setDirections(results);
					directionRenderer.setMap(self.get('map'));
				} else {
					directionRenderer.setMap(null);
				}
				callback(results, status);
			});
		},
		
		/**
		 * Displays the panorama for a given LatLng or panorama ID.
		 * @param panel:jQuery/String/Node
		 * @param streetViewPanoramaOptions:google.maps.StreetViewPanoramaOptions (optional) 
		 * @see http://code.google.com/intl/sv-SE/apis/maps/documentation/javascript/reference.html#StreetViewPanoramaOptions
		 */
		displayStreetView: function(panel, streetViewPanoramaOptions) {
			this.get('map').setStreetView(this.get('services > StreetViewPanorama', new google.maps.StreetViewPanorama(this._unwrap(panel), streetViewPanoramaOptions)));
		},
		
		/**
		 * A service for converting between an address and a LatLng.
		 * @param geocoderRequest:google.maps.GeocoderRequest
		 * @param callback:function(result:google.maps.GeocoderResult, status:google.maps.GeocoderStatus), 
		 * @see http://code.google.com/intl/sv-SE/apis/maps/documentation/javascript/reference.html#GeocoderResult
		 */
		search: function(geocoderRequest, callback) {
			this.get('services > Geocoder', new google.maps.Geocoder()).geocode(geocoderRequest, callback);
		}
	
	});
	
} (jQuery) );