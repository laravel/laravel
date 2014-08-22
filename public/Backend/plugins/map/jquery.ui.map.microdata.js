 /*!
 * jQuery UI Google Map 3.0-beta
 * http://code.google.com/p/jquery-ui-map/
 * Copyright (c) 2010 - 2011 Johan Säll Larsson
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 *
 * Depends:
 *		jquery.ui.map.js
 */
( function($) {
	
	$.extend($.ui.gmap.prototype, {
		
		/**
		 * Extracts microdata from the HTML by specified namespace 
		 * @param ns:string
		 * @param callback:function(microdata:object, element:jQuery object, iterator:int)
		 */
		microdata: function(ns, callback) { 
			var self = this;
			$('[itemtype="{0}"]'.replace('{0}', ns)).each(function(i) {
				callback(self._traverse($(this), {'@type': self._resolveType($(this).attr('itemtype'))}), this, i);
			});
		},
		
		/**
		 * Traverse through all child nodes
		 * @param $el:jQuery Object
		 * @param obj:Object
		 */
		_traverse: function(node, obj) {
			var self = this;
			node.children().each(function() {
				var $this = $(this), itemType = $this.attr('itemtype'), itemProp = $this.attr('itemProp');
				// skip check for itemscope since we are only interested in typed items
				if ( itemType != undefined && $this.children().length > 0 ) {
					if ( !obj[itemProp] ) {
						obj[itemProp] = [];
					}
					obj[itemProp].push({'@type': self._resolveType(itemType)});
					self._traverse($this, obj[itemProp][obj[itemProp].length-1]);					
				} else if ( itemProp ) {
					if ( obj[itemProp] ) {
						// In case any property is duplicated
						if ( typeof obj[itemProp] === 'string' ) {
							var temp = obj[itemProp];
							obj[itemProp] = [];
							obj[itemProp].push(temp);
						}
						obj[itemProp].push(self._extract($this));
					} else {
						obj[itemProp] = self._extract($this);
					}
				} else {
					self._traverse($this, obj);
				}
			});
			return obj;
		},
		
		/**
		 * Extract the proper value based on element attribute
		 * @param $el:jQuery Object
		 * @param className:string
		 */
		_extract: function($el) {
			if ( $el.attr('src') ) {
				return $el.attr('src');
			} else if ( $el.attr('href') ) {
				return $el.attr('href');
			} else if ( $el.attr('content') ) {
				return $el.attr('content');
			} else if ( $el.attr('datetime') ) {
				return $el.attr('datetime');
			} else if ( $el.text() ) {
				return $el.text();
			}
			return;
		},
		
		/**
		 * Removes any url or prefix
		 * @param $el:jQuery Object
		 * @param className:string
		 */
		_resolveType: function(type) {
			if ( type.indexOf('http') > -1 ) {
				type = type.substr(type.lastIndexOf('/')+1).replace('?','').replace('#','');
			} else if ( type.indexOf(':') > -1 ) {
				type = type.split(':')[1];
			}
			return type;
		}
	
	});
	
} (jQuery) );