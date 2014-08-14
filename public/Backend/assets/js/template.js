/*
 * MoonCake v1.3.1 - Template JS
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
	
	var MoonCake = function( document ) {
		this.document = $(document);
	}

	MoonCake.prototype = {
		
		version: '1.0', 

		defaults: {
			showSidebarToggleButton: true, 
			fixedSidebar: false
		}, 

		init: function( options ) {

			this.options = $.extend({}, this.defaults, options);

			this.bindEventHandlers();

			this.updateSidebarNav( $( '#sidebar #navigation > ul > li.active' ).first(), true );

			this.options.showSidebarToggleButton && this.attachSidebarToggleButton();
			this.options.fixedSidebar && $.fn.affix && $('#sidebar').affix({
				offset: {
					top: 0
				}
			});
			
			return this;
		}, 

		ready: function( fn ) {
			this.document.ready($.proxy(function() {
				fn.call( this.document, this );
			}, this));

			return this;
		}, 

		attachSidebarToggleButton: function() {

			var toggleButton = $( '<li id="sidebar-toggle-wrap"><span id="sidebar-toggle"><span></span></span></li>' );

			toggleButton
				.appendTo( '#wrapper #sidebar #navigation > ul' )
				.children( '#sidebar-toggle' )
				.on( 'click.template', function(e) {
					if( !!$( '#sidebar #navigation > ul > li.active:first .inner-nav' ).length ) {
						$(this).parents( '#content' )
							.toggleClass( 'sidebar-minimized' )
						.end()
							.toggleClass( 'toggled' );
					}
					e.preventDefault();
				})
				.toggleClass( 'disabled', !$( '#sidebar #navigation > ul > li.active:first .inner-nav' ).length )
				.toggleClass( 'toggled', $( '#wrapper #content' ).hasClass( 'sidebar-minimized' ) );
		}, 

		bindEventHandlers: function() {

			// Search and Dropdown-menu inputs
			$( '#header #header-search .search-query')
				.add( $( '.dropdown-menu' )
				.find( ':input' ) )
				.on( 'click.template', function( e ) {
					e.stopPropagation();
				});

			var self = this;
			// Sidebar Navigation
			$( '#sidebar #navigation > ul > li' )
				.filter(':not(#sidebar-toggle-wrap)')
				.on( 'click.template', ' > a, > span', function( e ) {
					if( $(this).is('a') && undefined !== $(this).attr('href') )
						return;

					self.updateSidebarNav( $(this).parent() );
					e.stopPropagation();
				});

			// Collapsible Boxes
			$( '.widget .widget-header [data-toggle=widget]' )
			.each(function(i, element) {
				var p = $( this ).parents( '.widget' );
				if( !p.children( '.widget-inner-wrap' ).length ) {
					p.children( ':not(.widget-header)' )
						.wrapAll( $('<div></div>').addClass( 'widget-inner-wrap' ) );
				}
			}).on( 'click', function(e) {
				var p = $( this ).parents( '.widget' );
				if( p.hasClass('collapsed') ) {
					p.removeClass( 'collapsed' )
						.children( '.widget-inner-wrap' ).hide().slideDown( 250 );
				} else {
					p.children( '.widget-inner-wrap' ).slideUp( 250, function() {
						p.addClass( 'collapsed' );
					});
				}
				e.preventDefault();
			});
		}, 

		updateSidebarNav: function( nav, init ) {
			var hasInnerNav = !!nav.children( '.inner-nav' ).length;
			nav
				.siblings().removeClass( 'active open' )
			.end()
				.addClass( 'active' ).toggleClass( 'open' );

			!init && $( '#content' )
				.toggleClass( 'sidebar-minimized', !hasInnerNav );

			$( '#sidebar-toggle' )
				.toggleClass( 'disabled', !hasInnerNav )
				.toggleClass( 'toggled', $( '#content' ).hasClass( 'sidebar-minimized' ) );

			nav = nav.children( '.inner-nav' ).get(0);
			$( '#wrapper #sidebar #navigation > ul' )
				.css( 'minHeight', nav? $( nav ).outerHeight() : '');
		}
	};

	$.template = new MoonCake( document ).ready( function( template ) {

		template.init( $('body').data() );

	});

	
}) (jQuery, window, document);