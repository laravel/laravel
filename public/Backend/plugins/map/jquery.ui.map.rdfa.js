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
		 * Extracts RDFa from the HTML by specified namespace 
		 * @param ns:string
		 * @param callback:function(microdata:object, element:jQuery object, iterator:int)
		 */
		rdfa: function(ns, callback) { 
			var self = this;
			$('[typeof="{0}"]'.replace('{0}', ns)).each(function(i) {
				callback(self._traverse($(this), {'@type': self._resolveType($(this).attr('typeof'))}), this, i);
			});
		},
		
		/**
		 * Traverse through all child nodes
		 * @param $el:jQuery Object
		 * @param obj:Object
		 */
		_traverse: function(node, obj) {
			var self = this;
			node.children().each( function() {
				var $this = $(this), typeOf = self._resolveType($this.attr('typeof')), rel = self._resolveType($this.attr('rel')), property = self._resolveType($this.attr('property'));
				if ( typeOf || rel || property ) {
					if (rel) {
						if ( $this.children().length > 0 ) {
							obj[rel] = [];
							self._traverse($this, obj[rel]);
						} else {
							obj[rel] = self._extract($this, true);
						}
					}
					if (typeOf) {
						obj.push({'@type': typeOf});
						self._traverse($this, obj[obj.length-1]);
					}
					if ( property ) {
						if ( obj[property] ) {
							obj[property] = [obj[property]];
							obj[property].push(self._extract($this, false));
						} else {
							obj[property] = self._extract($this, false);
						}
					}
				} else {
					self._traverse($this, obj);
				}
			});
			return obj;
		},
		
		/**
		 * Extract the proper value based on element attribute
		 * @param $el:jQuery object
		 * @param isLink:bool
		 */
		_extract: function($el, isLink) {
			if (isLink) {
				if ( $el.attr('src') ) { return $el.attr('src'); }  
				if ( $el.attr('href') ) { return $el.attr('href'); } 
			}
			if ( $el.attr('content') ) { return $el.attr('content'); }
			if ( $el.text() ) { return $el.text(); }
			return;
		},
		
		/**
		 * Removes any url or prefix
		 * @param $el:jQuery Object
		 * @param className:string
		 */
		_resolveType: function(type) {
			if (type) {
				if ( type.indexOf('http') > -1 ) {
					type = type.substr(type.lastIndexOf('/')+1).replace('?','').replace('#','');
				} else if ( type.indexOf(':') > -1 ) {
					type = type.split(':')[1];
				}
			}
			return type;
		}
	
	});
	
} (jQuery) );