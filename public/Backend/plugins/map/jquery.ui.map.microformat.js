 /*!
 * jQuery UI Google Map 3.0-alpha
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
		 * Extracts microformat from the HTML by specified root class
		 * @param ns:string - the 'namespace'/root class
		 * @param callback:function(microformat:object, element:jQuery object, iterator:int)
		 */
		microformat: function(ns, callback) {
			var self = this;
			jQuery.extend(self.get('properties', {}), {
				'additional-name': {},
				'adr': { 'hasChildren': true },
				'affiliation': {},
				'author': {},
				'bday': {},
				'category': { 'isMultivalued': true },
				'class': {},
				'contact': {},
				'country-name': {},
				'description': {},
				'dtend': {},
				'dtreviewed': {},
				'dtstart': {}, 
				'education': {},
				'entry-content': {}, 
				'entry-summary': {},
				'entry-title': {}, 
				'email': { 'hasChildren': true }, 
				'experience': {}, 					  		
				'extended-address': {},
				'family-name': {}, 
				'fn': {},
				'geo': { 'hasChildren': true }, 
				'given-name': {}, 
				'hentry': { 'isRoot': true }, 
				'hfeed': { 'isRoot': true },
				'honorific-prefix': {}, 
				'honorific-suffix': {},
				'hresume': { 'isRoot': true }, 
				'hreview': { 'isRoot': true },
				'item': {},
				'key': { 'hasChildren': true },
				'label': {},
				'latitude': {},
				'locality': {},
				'location': { 'hasChildren': true },
				'logo': {},
				'longitude': {},
				'mailer': {},
				'n': { 'hasChildren': true },
				'nickname': { 'isMultivalued': true },
				'note': {},
				'org': { 'hasChildren': true },
				'organization-name': {},
				'organization-unit': {},
				'permalink': {},
				'photo': {},
				'post-office-box': {},
				'postal-code': {},
				'profile': { 'isRoot': true },
				'publications': {},
				'published': {},
				'rating': {},
				'region': {},
				'rev': {},
				'reviewer': {},
				'role': {},
				'skill': {},
				'sort-string': {},
				'sound': {},
				'street-address': {},
				'summary': {},
				'tel': { 'isMultivalued': true },
				'title': {},
				'type': {},
				'tz': {},
				'uid': {},
				'updated': {},
				'url': { 'isMultivalued': true },
				'value': {},
				'value-title': {},
				'vcalendar': { 'isRoot': true },
				'vcard': { 'isRoot': true },
				'vevent': { 'isRoot': true },
				'version': {},
				'xoxo': { 'isRoot': true }
			});
			$(ns).each(function(i, node) {
				callback(self._traverse($(this), {'@type': ns.replace('.','')}), this, i);
			});
		},
		
		/**
		 * Traverse through all child nodes
		 * @param $el:jQuery Object
		 * @param obj:Object
		 */
		_traverse: function($el, obj) {
			var self = this;
			$el.children().each(function() {
				var $this = $(this);
				if ($this.attr('class')) {
					var temp = $this.attr('class').split(' '), cls = [], type;
					$.each(temp, function(itr, name) {
						if ( self.get('properties')[name] && self.get('properties')[name].isRoot ) {
							type = name;
						} else {
							cls.push(name);
						}
					});
					$.each(cls, function(itr, className) {
						if ( self.get('properties')[className] ) {
							type = type || className;
							if ( self.get('properties')[className].hasChildren && $this.children().length > 0 ) {
								if ( !obj[className] ) {
									obj[className] = [];
								}
								obj[className].push({'@type': type});
								self._traverse($this, obj[className][obj[className].length-1]);
							} else {
								if ( $this.children().length > 0 ) {
									obj[className] = {'@type': type};
									self._traverse($this, obj[className]);
								} else {
									if ( self.get('properties')[className].isMultivalued ) {
										if ( !obj[className] ) {
											obj[className] = [];
										}
										obj[className].push(self._extract($this, className));
									} else {
										obj[className] = self._extract($this, className);
									}
								}
							}
						}
					});
				} else {
					self._traverse($this, obj);
				}
			});
			return obj;
		},
		
		/**
		 * Extract the proper value based on class name and element attribute
		 * @param $el:jQuery Object
		 * @param className:string
		 */
		_extract: function($el, className) {
			if ( className === 'value-title' ) {
				return $el.attr('title');
			} else if ( className === 'url' ) {
				return $el.attr('href');
			}
			if ( $el.attr('src') ) {
				return $el.attr('src');
			} else if ( $el.attr('content') ) {
				return $el.attr('content');
			} else if ( $el.text() ) {
				return $el.text();
			}
			return;
		}
		
	});

} (jQuery) );