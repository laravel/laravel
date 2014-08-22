 /*!
 * jQuery UI Google Map 3.0-rc
 * http://code.google.com/p/jquery-ui-map/
 * Copyright (c) 2010 - 2011 Johan Säll Larsson
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 *
 * Depends:
 *      jquery.ui.map.js
 */
( function($) {

	$.extend($.ui.gmap.prototype, {
		 
		/**
		 * Gets the current position
		 * @param callback:function(position, status)
		 * @param geoPositionOptions:object, see https://developer.mozilla.org/en/XPCOM_Interface_Reference/nsIDOMGeoPositionOptions
		 */
		getCurrentPosition: function(callback, geoPositionOptions) {
			if ( navigator.geolocation ) {
				navigator.geolocation.getCurrentPosition ( 
					function(result) {
						callback(result, 'OK');
					}, 
					function(error) {
						callback(null, error);
					}, 
					geoPositionOptions 
				);	
			} else {
				callback(null, 'NOT_SUPPORTED');
			}
		},
		
		/**
		 * Watches current position
		 * To clear watch, call navigator.geolocation.clearWatch(this.get('watch'));
		 * @param callback:function(position, status)
		 * @param geoPositionOptions:object, see https://developer.mozilla.org/en/XPCOM_Interface_Reference/nsIDOMGeoPositionOptions
		 */
		watchPosition: function(callback, geoPositionOptions) {
			if ( navigator.geolocation ) {
				this.set('watch', navigator.geolocation.watchPosition ( 
					function(result) {
						callback(result, "OK");
					}, 
					function(error) {
						callback(null, error);
					}, 
					geoPositionOptions 
				));	
			} else {
				callback(null, "NOT_SUPPORTED");
			}
		},

		/**
		 * Clears any watches
		 */
		clearWatch: function() {
			if ( navigator.geolocation ) {
				navigator.geolocation.clearWatch(this.get('watch'));
			}
		},
		
		/**
		 * Autocomplete using Google Geocoder
		 * @param panel:string/node/jquery
		 * @param callback:function(results, status)
		 */
		autocomplete: function(panel, callback) {
			var self = this;
			$(this._unwrap(panel)).autocomplete({
				source: function( request, response ) {
					self.search({'address':request.term}, function(results, status) {
						if ( status === 'OK' ) {
							response( $.map( results, function(item) {
								return { label: item.formatted_address, value: item.formatted_address, position: item.geometry.location }
							}));
						} else if ( status === 'OVER_QUERY_LIMIT' ) {
							alert('Google said it\'s too much!');
						}
					});
				},
				minLength: 3,
				select: function(event, ui) { 
					self._call(callback, ui);
				},
				open: function() { $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" ); },
				close: function() { $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" ); }
			});
		},
		
		/**
		 * Retrieves a list of Places in a given area. The PlaceResultss passed to the callback are stripped-down versions of a full PlaceResult. A more detailed PlaceResult for each Place can be obtained by sending a Place Details request with the desired Place's reference value.
		 * @param placeSearchRequest:google.maps.places.PlaceSearchRequest, http://code.google.com/apis/maps/documentation/javascript/reference.html#PlaceSearchRequest
		 * @param callback:function(result:google.maps.places.PlaceResult, status:google.maps.places.PlacesServiceStatus), http://code.google.com/apis/maps/documentation/javascript/reference.html#PlaceResult
		 */
		placesSearch: function(placeSearchRequest, callback) {
			this.get('services > PlacesService', new google.maps.places.PlacesService(this.get('map'))).search(placeSearchRequest, callback);
		},
		
		/**
		 * Clears any directions
		 */
		clearDirections: function() {
			var directionsRenderer = this.get('services > DirectionsRenderer');
			if (directionsRenderer) {
				directionsRenderer.setMap(null);
				directionsRenderer.setPanel(null);
			}
		},
		
		/**
		 * Page through the markers. Very simple version.
		 * @param prop:the marker property to show in display, defaults to title
		 */
		pagination: function(prop) {
			var $el = $("<div id='pagination' class='pagination shadow gradient rounded clearfix'><div class='lt btn back-btn'></div><div class='lt display'></div><div class='rt btn fwd-btn'></div></div>");
			var self = this, i = 0, prop = prop || 'title';
			self.set('p_nav', function(a, b) {
				if (a) {
					i = i + b;
					$el.find('.display').text(self.get('markers')[i][prop]);
					self.get('map').panTo(self.get('markers')[i].getPosition());
				}
			});
			self.get('p_nav')(true, 0);
			$el.find('.back-btn').click(function() {
				self.get('p_nav')((i > 0), -1, this);
			});
			$el.find('.fwd-btn').click(function() {
				self.get('p_nav')((i < self.get('markers').length - 1), 1, this);
			});
			self.addControl($el, google.maps.ControlPosition.TOP_LEFT);			
		}
		
		/**
		 * A layer that displays data from Panoramio.
		 * @param panoramioLayerOptions:google.maps.panoramio.PanoramioLayerOptions, http://code.google.com/apis/maps/documentation/javascript/reference.html#PanoramioLayerOptions
		 */
		/*loadPanoramio: function(panoramioLayerOptions) {
			if ( !this.get('overlays').PanoramioLayer ) {
				this.get('overlays').PanoramioLayer = new google.maps.panoramio.PanoramioLayer();
			}
			this.get('overlays').PanoramioLayer.setOptions(jQuery.extend({'map': this.get('map') }, panoramioLayerOptions));
		},*/
		
		/**
		 * Makes an elevation request along a path, where the elevation data are returned as distance-based samples along that path.
		 * @param pathElevationRequest:google.maps.PathElevationRequest, http://code.google.com/apis/maps/documentation/javascript/reference.html#PathElevationRequest
		 * @param callback:function(result:google.maps.ElevationResult, status:google.maps.ElevationStatus), http://code.google.com/intl/sv-SE/apis/maps/documentation/javascript/reference.html#ElevationResult
		 */
		/*elevationPath: function(pathElevationRequest, callback) {
			this.get('services > ElevationService', new google.maps.ElevationService()).getElevationAlongPath(pathElevationRequest, callback);
		},*/
		
		/**
		 * Makes an elevation request for a list of discrete locations.
		 * @param pathElevationRequest:google.maps.PathElevationRequest, http://code.google.com/apis/maps/documentation/javascript/reference.html#PathElevationRequest
		 * @param callback:function(result:google.maps.ElevationResult, status:google.maps.ElevationStatus), http://code.google.com/intl/sv-SE/apis/maps/documentation/javascript/reference.html#ElevationResult
		 */
		/*elevationLocations: function(pathElevationRequest, callback) {
			this.get('services > ElevationService', new google.maps.ElevationService()).getElevationForLocations(pathElevationRequest, callback);
		},*/
		
		/* PLACES SERVICE */		
		
		/**
		 * Retrieves a list of Places in a given area. The PlaceResultss passed to the callback are stripped-down versions of a full PlaceResult. A more detailed PlaceResult for each Place can be obtained by sending a Place Details request with the desired Place's reference value.
		 * @param placeSearchRequest:google.maps.places.PlaceSearchRequest, http://code.google.com/apis/maps/documentation/javascript/reference.html#PlaceSearchRequest
		 * @param callback:function(result:google.maps.places.PlaceResult, status:google.maps.places.PlacesServiceStatus), http://code.google.com/apis/maps/documentation/javascript/reference.html#PlaceResult
		 */
		/*placesSearch: function(placeSearchRequest, callback) {
			this.get('services > PlacesService', new google.maps.places.PlacesService(this.get('map'))).search(placeSearchRequest, callback);
		},*/
		
		/**
		 * Retrieves details about the Place identified by the given reference.
		 * @param placeDetailsRequest:google.maps.places.PlaceDetailsRequest, http://code.google.com/apis/maps/documentation/javascript/reference.html#PlaceDetailsRequest
		 * @param callback:function(result:google.maps.places.PlaceResult, status:google.maps.places.PlacesServiceStatus), http://code.google.com/apis/maps/documentation/javascript/reference.html#PlaceResult
		 */
		/*placesDetails: function(placeDetailsRequest, callback) {
			this.get('services > PlacesService', new google.maps.places.PlacesService(this.get('map'))).getDetails(placeDetailsRequest, callback);
		},*/
		
		/**
		 * A service to predict the desired Place based on user input. The service is attached to an <input> field in the form of a drop-down list. The list of predictions is updated dynamically as text is typed into the input field. 
		 * @param panel:jquery/node/string
		 * @param autocompleteOptions:google.maps.places.AutocompleteOptions, http://code.google.com/apis/maps/documentation/javascript/reference.html#AutocompleteOptions
		 */		
		/*placesAutocomplete: function(panel, autocompleteOptions) {
			this.get('services > Autocomplete', new google.maps.places.Autocomplete(this._unwrap(panel)));
		},*/
		
		/* DISTANCE MATRIX SERVICE */
		
		/**
		 * Issues a distance matrix request.
		 * @param distanceMatrixRequest:google.maps.DistanceMatrixRequest, http://code.google.com/apis/maps/documentation/javascript/reference.html#DistanceMatrixRequest 
		 * @param callback:function(result:google.maps.DistanceMatrixResponse, status: google.maps.DistanceMatrixStatus), http://code.google.com/apis/maps/documentation/javascript/reference.html#DistanceMatrixResponse
		 */
		/*displayDistanceMatrix: function(distanceMatrixRequest, callback) {
			this.get('services > DistanceMatrixService', new google.maps.DistanceMatrixService()).getDistanceMatrix(distanceMatrixRequest, callback);
		}*/
	
	});
	
} (jQuery) );