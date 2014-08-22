/*
 * MoonCake v1.3.1 - Contact List Plugin
 *
 * This file is part of MoonCake, an Admin template build for sale at ThemeForest.
 * For questions, suggestions or support request, please mail me at maimairel@yahoo.com
 *
 * Development Started:
 * July 28, 2012
 * Last Update:
 * December 07, 2012
 *
 * 'Highly configurable' mutable plugin boilerplate
 * Author: @markdalgleish
 * Further changes, comments: @addyosmani
 * Licensed under the MIT license
 *
 */
 
;( function( $, window, document, undefined ) {
	"use strict";
	
	// our plugin constructor
	var ContactList = function( element, options ) {
		if( arguments.length ) {
			this._init( element, options );
		}
    };
	
	// the plugin prototype
	ContactList.prototype = {
		defaults: {
			contactListClass: 'thumbnails', 

			scrollTimeout: 100, // amount of time needed to trigger scrolling
			scrollOptions: {
				duration: 500, 
				easing: 'swing'
			}, 

			showFilters: true, 
			filterPlaceholder: 'Search...', 
			filterContainerClass: 'search-form', 
			filterInputClass: 'search-query', 
			filterFunction: null //function( element, keyword );
		}, 
		
		_init: function( element, options ) {
			this.element = $( element );
			this.options = $.extend( {}, this.defaults, options, this.element.data() );
			
			this._groups = [];
			this._container = this._build();
			this._listen();
			
			return this;
		}, 
		
		_build: function() {
			
			var self = this, 
				markup = 
					'<div class="contact-list">' + 
						'<div class="contact-filter">' + 
							'<input type="text">' + 
						'</div>' + 
						'<div class="contact-contents">' + 
							'<div class="contact-items-container"></div>' + 
							'<div class="contact-nav">' + 
								'<ul></ul>' + 
							'</div>' + 
						'</div>' + 
					'</div>', 
				container = $( markup ), 
				filter = $( '.contact-filter', container ), 
				contents = $( '.contact-contents', container ), 
				nav = $( '.contact-nav', container );

			self.element
				.addClass( 'contact-items' )
				.addClass( self.options.contactListClass );

			filter
				.addClass( self.options.filterContainerClass )
				.find( 'input' )
				.addClass( self.options.filterInputClass )
				.attr( 'placeholder', self.options.filterPlaceholder )
				.on( 'keyup.contactlist', function( e ) {
					var val = $(this).val().toString().toLowerCase(), 
						that = this, 
						items = $( ' > li > ul', self.element ).children( 'li' ).show(), 
						titles = items.parent().siblings( '.title' ).show();

					if( val ) {
						items.filter( function() {
							if( self.options.filterFunction ) {
								return self.options.filterFunction.call( that, this, val );
							} else {
								var toMatch = $(this).text().toString().toLowerCase();
								return toMatch.indexOf( val ) == -1;
							}
						}).add( titles ).hide();
					}
				})
					.end()
				.toggle( self.options.showFilters );

			$.each( this.element.children( 'li[data-group]' ), function( i, element ) {
				var group = $( element ).data( 'group' ), 
					navItem = $( '<a href="#" data-group="' + group + '"></a>' ).text( group );
					
				$( 'ul', nav ).append( $( '<li></li>' ).append( navItem ) );
				
				self._groups[group] = {
					element: $( element ), 
					nav: navItem.parent()
				};
				
			} );
			
			container.insertAfter( this.element );

			$( '.contact-items-container', contents )
				.append( this.element )
				.css( 'height', $( '.contact-nav', container ).height() );
			
			this._selectGroup( $( ' > ul > li:first a', nav ).data( 'group' ) );
			
			return container;
		}, 
		
		_listen: function() {
			
			var self = this;
			$( '.contact-nav', self._container )
				.on( 'mouseover.contactlist', 'a', function( e ) {
					var el = $( this ).addClass( 'focused' );
					
					setTimeout(function() {
						if( el.hasClass( 'focused' ) ) {
							self._selectGroup( el.data( 'group' ), true );
						}
					}, self.options.scrollTimeout );
				})
				.on( 'mouseleave.contactlist', 'a', function( e ) {
					$( this ).removeClass( 'focused' );
				})
				.on( 'click.contactlist', 'a', function( e ) {
					self._selectGroup( $( this ).data( 'group' ), false );
					e.preventDefault();
				})
		},
		
		_selectGroup: function( group, animate ) {
			if( typeof( group ) === 'string' )
				group = this._groups[group];
				
			if( typeof( group ) === 'object' ) {
				$( '.contact-nav li', this._container )
					.add( this.element.children( 'li' ) )
					.removeClass('selected');
					
				group.element
					.add( group.nav )
					.addClass( 'selected' );
					
				if( animate )
					$( '.contact-items-container', self._container ).stop().scrollTo(group.element, this.options.scrollOptions);
				else
					$( '.contact-items-container', self._container ).stop().scrollTo(group.element);
			}
		}, 

		option: function( key, value ) {
			
			if ( arguments.length === 0 ) {
				// don't return a reference to the internal hash
				return $.extend( {}, this.options );
			}

			if  (typeof key === "string" ) {
				if ( value === undefined ) {
					return this.options[ key ];
				}

				this.options[ key ] = value;
			}

			return this;
		}
	}
	
	$.fn.contactList = function(options) {

		var isMethodCall = typeof options === "string",
			args = Array.prototype.slice.call( arguments, 1 ),
			returnValue = this;

		// prevent calls to internal methods
		if ( isMethodCall && options.charAt( 0 ) === "_" ) {
			return returnValue;
		}

		if ( isMethodCall ) {
			this.each(function() {
				var instance = $.data( this, 'contactlist' ),
					methodValue = instance && $.isFunction( instance[options] ) ?
						instance[ options ].apply( instance, args ) :
						instance;

				if ( methodValue !== instance && methodValue !== undefined ) {
					returnValue = methodValue;
					return false;
				}
			});
		} else {
			this.each(function() {
				var instance = $.data( this, 'contactlist' );
				if ( !instance ) {
					$.data( this, 'contactlist', new ContactList( this, options ) );
				}
			});
		}

		return returnValue;
	};

})( jQuery, window , document );

/*!
 * jQuery.ScrollTo
 * Copyright (c) 2007-2012 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 4/09/2012
 *
 * @projectDescription Easy element scrolling using jQuery.
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 * @author Ariel Flesler
 * @version 1.4.3.1
 *
 * @id jQuery.scrollTo
 * @id jQuery.fn.scrollTo
 * @param {String, Number, DOMElement, jQuery, Object} target Where to scroll the matched elements.
 *	  The different options for target are:
 *		- A number position (will be applied to all axes).
 *		- A string position ('44', '100px', '+=90', etc ) will be applied to all axes
 *		- A jQuery/DOM element ( logically, child of the element to scroll )
 *		- A string selector, that will be relative to the element to scroll ( 'li:eq(2)', etc )
 *		- A hash { top:x, left:y }, x and y can be any kind of number/string like above.
 *		- A percentage of the container's dimension/s, for example: 50% to go to the middle.
 *		- The string 'max' for go-to-end. 
 * @param {Number, Function} duration The OVERALL length of the animation, this argument can be the settings object instead.
 * @param {Object,Function} settings Optional set of settings or the onAfter callback.
 *	 @option {String} axis Which axis must be scrolled, use 'x', 'y', 'xy' or 'yx'.
 *	 @option {Number, Function} duration The OVERALL length of the animation.
 *	 @option {String} easing The easing method for the animation.
 *	 @option {Boolean} margin If true, the margin of the target element will be deducted from the final position.
 *	 @option {Object, Number} offset Add/deduct from the end position. One number for both axes or { top:x, left:y }.
 *	 @option {Object, Number} over Add/deduct the height/width multiplied by 'over', can be { top:x, left:y } when using both axes.
 *	 @option {Boolean} queue If true, and both axis are given, the 2nd axis will only be animated after the first one ends.
 *	 @option {Function} onAfter Function to be called after the scrolling ends. 
 *	 @option {Function} onAfterFirst If queuing is activated, this function will be called after the first scrolling ends.
 * @return {jQuery} Returns the same jQuery object, for chaining.
 *
 * @desc Scroll to a fixed position
 * @example $('div').scrollTo( 340 );
 *
 * @desc Scroll relatively to the actual position
 * @example $('div').scrollTo( '+=340px', { axis:'y' } );
 *
 * @desc Scroll using a selector (relative to the scrolled element)
 * @example $('div').scrollTo( 'p.paragraph:eq(2)', 500, { easing:'swing', queue:true, axis:'xy' } );
 *
 * @desc Scroll to a DOM element (same for jQuery object)
 * @example var second_child = document.getElementById('container').firstChild.nextSibling;
 *			$('#container').scrollTo( second_child, { duration:500, axis:'x', onAfter:function(){
 *				alert('scrolled!!');																   
 *			}});
 *
 * @desc Scroll on both axes, to different values
 * @example $('div').scrollTo( { top: 300, left:'+=200' }, { axis:'xy', offset:-20 } );
 */

;(function( $ ){
	
	var $scrollTo = $.scrollTo = function( target, duration, settings ){
		$(window).scrollTo( target, duration, settings );
	};

	$scrollTo.defaults = {
		axis:'xy',
		duration: parseFloat($.fn.jquery) >= 1.3 ? 0 : 1,
		limit:true
	};

	// Returns the element that needs to be animated to scroll the window.
	// Kept for backwards compatibility (specially for localScroll & serialScroll)
	$scrollTo.window = function( scope ){
		return $(window)._scrollable();
	};

	// Hack, hack, hack :)
	// Returns the real elements to scroll (supports window/iframes, documents and regular nodes)
	$.fn._scrollable = function(){
		return this.map(function(){
			var elem = this,
				isWin = !elem.nodeName || $.inArray( elem.nodeName.toLowerCase(), ['iframe','#document','html','body'] ) != -1;

				if( !isWin )
					return elem;

			var doc = (elem.contentWindow || elem).document || elem.ownerDocument || elem;
			
			return /webkit/i.test(navigator.userAgent) || doc.compatMode == 'BackCompat' ?
				doc.body : 
				doc.documentElement;
		});
	};

	$.fn.scrollTo = function( target, duration, settings ){
		if( typeof duration == 'object' ){
			settings = duration;
			duration = 0;
		}
		if( typeof settings == 'function' )
			settings = { onAfter:settings };
			
		if( target == 'max' )
			target = 9e9;
			
		settings = $.extend( {}, $scrollTo.defaults, settings );
		// Speed is still recognized for backwards compatibility
		duration = duration || settings.duration;
		// Make sure the settings are given right
		settings.queue = settings.queue && settings.axis.length > 1;
		
		if( settings.queue )
			// Let's keep the overall duration
			duration /= 2;
		settings.offset = both( settings.offset );
		settings.over = both( settings.over );

		return this._scrollable().each(function(){
			// Null target yields nothing, just like jQuery does
			if (target == null) return;

			var elem = this,
				$elem = $(elem),
				targ = target, toff, attr = {},
				win = $elem.is('html,body');

			switch( typeof targ ){
				// A number will pass the regex
				case 'number':
				case 'string':
					if( /^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(targ) ){
						targ = both( targ );
						// We are done
						break;
					}
					// Relative selector, no break!
					targ = $(targ,this);
					if (!targ.length) return;
				case 'object':
					// DOMElement / jQuery
					if( targ.is || targ.style )
						// Get the real position of the target 
						toff = (targ = $(targ)).offset();
			}
			$.each( settings.axis.split(''), function( i, axis ){
				var Pos	= axis == 'x' ? 'Left' : 'Top',
					pos = Pos.toLowerCase(),
					key = 'scroll' + Pos,
					old = elem[key],
					max = $scrollTo.max(elem, axis);

				if( toff ){// jQuery / DOMElement
					attr[key] = toff[pos] + ( win ? 0 : old - $elem.offset()[pos] );

					// If it's a dom element, reduce the margin
					if( settings.margin ){
						attr[key] -= parseInt(targ.css('margin'+Pos)) || 0;
						attr[key] -= parseInt(targ.css('border'+Pos+'Width')) || 0;
					}
					
					attr[key] += settings.offset[pos] || 0;
					
					if( settings.over[pos] )
						// Scroll to a fraction of its width/height
						attr[key] += targ[axis=='x'?'width':'height']() * settings.over[pos];
				}else{ 
					var val = targ[pos];
					// Handle percentage values
					attr[key] = val.slice && val.slice(-1) == '%' ? 
						parseFloat(val) / 100 * max
						: val;
				}

				// Number or 'number'
				if( settings.limit && /^\d+$/.test(attr[key]) )
					// Check the limits
					attr[key] = attr[key] <= 0 ? 0 : Math.min( attr[key], max );

				// Queueing axes
				if( !i && settings.queue ){
					// Don't waste time animating, if there's no need.
					if( old != attr[key] )
						// Intermediate animation
						animate( settings.onAfterFirst );
					// Don't animate this axis again in the next iteration.
					delete attr[key];
				}
			});

			animate( settings.onAfter );			

			function animate( callback ){
				$elem.animate( attr, duration, settings.easing, callback && function(){
					callback.call(this, target, settings);
				});
			};

		}).end();
	};
	
	// Max scrolling position, works on quirks mode
	// It only fails (not too badly) on IE, quirks mode.
	$scrollTo.max = function( elem, axis ){
		var Dim = axis == 'x' ? 'Width' : 'Height',
			scroll = 'scroll'+Dim;
		
		if( !$(elem).is('html,body') )
			return elem[scroll] - $(elem)[Dim.toLowerCase()]();
		
		var size = 'client' + Dim,
			html = elem.ownerDocument.documentElement,
			body = elem.ownerDocument.body;

		return Math.max( html[scroll], body[scroll] ) 
			 - Math.min( html[size]  , body[size]   );
	};

	function both( val ){
		return typeof val == 'object' ? val : { top:val, left:val };
	};

})( jQuery );