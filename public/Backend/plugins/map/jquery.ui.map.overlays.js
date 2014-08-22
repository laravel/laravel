 /*!
 * jQuery UI Google Map 3.0-rc
 * http://code.google.com/p/jquery-ui-map/
 * Copyright (c) 2010 - 2012 Johan Säll Larsson
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 *
 * Depends:
 *      jquery.ui.map.js
 */
( function($) {

	$.extend($.ui.gmap.prototype, {
		
		/**
		 * Adds a shape to the map
		 * @param shapeType:string Polygon, Polyline, Rectangle, Circle
		 * @param shapeOptions:object
		 * @return object
		 */
		addShape: function(shapeType, shapeOptions) {
			var shape = new google.maps[shapeType](jQuery.extend({'map': this.get('map')}, shapeOptions));
			this.get('overlays > ' + shapeType, []).push(shape);
			return $(shape);
		},
		
		/**
		 * Adds fusion data to the map.
		 * @param fusionTableOptions:google.maps.FusionTablesLayerOptions, http://code.google.com/intl/sv-SE/apis/maps/documentation/javascript/reference.html#FusionTablesLayerOptions
		 * @param fusionTableId:int
		 */
		loadFusion: function(fusionTableOptions, fusionTableId) {
			( (!fusionTableId) ? this.get('overlays > FusionTablesLayer', new google.maps.FusionTablesLayer()) : this.get('overlays > FusionTablesLayer', new google.maps.FusionTablesLayer(fusionTableId, fusionTableOptions)) ).setOptions(jQuery.extend({'map': this.get('map') }, fusionTableOptions));
		},
		
		/**
		 * Adds markers from KML file or GeoRSS feed
		 * @param uid:String - an identifier for the RSS e.g. 'rss_dogs'
		 * @param url:String - URL to feed
		 * @param kmlLayerOptions:google.maps.KmlLayerOptions, http://code.google.com/intl/sv-SE/apis/maps/documentation/javascript/reference.html#KmlLayerOptions
		 */
		loadKML: function(uid, url, kmlLayerOptions) {
			this.get('overlays > ' + uid, new google.maps.KmlLayer(url, jQuery.extend({'map': this.get('map')}, kmlLayerOptions)));
		}
	
	});
	
} (jQuery) );