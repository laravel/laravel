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
	
	var Customizer = function( ) {
		this.init();
	}

	Customizer.prototype = {

		patterns: [
			'assets/images/layout/bg/arches.png', 
			'assets/images/layout/bg/blu_stripes.png', 
			'assets/images/layout/bg/bright_squares.png', 
			'assets/images/layout/bg/brushed_alu.png', 
			'assets/images/layout/bg/circles.png', 
			'assets/images/layout/bg/climpek.png', 
			'assets/images/layout/bg/connect.png', 
			'assets/images/layout/bg/corrugation.png', 
			'assets/images/layout/bg/cubes.png', 
			'assets/images/layout/bg/diagonal-noise.png', 
			'assets/images/layout/bg/diagonal_striped_brick.png', 
			'assets/images/layout/bg/diamonds.png', 
			'assets/images/layout/bg/diamond_upholstery.png', 
			'assets/images/layout/bg/escheresque.png', 
			'assets/images/layout/bg/fabric_plaid.png', 
			'assets/images/layout/bg/furley_bg.png', 
			'assets/images/layout/bg/gplaypattern.png', 
			'assets/images/layout/bg/gradient_squares.png', 
			'assets/images/layout/bg/grey.png', 
			'assets/images/layout/bg/grilled.png', 
			'assets/images/layout/bg/hexellence.png', 
			'assets/images/layout/bg/lghtmesh.png', 
			'assets/images/layout/bg/light_alu.png', 
			'assets/images/layout/bg/light_checkered_tiles.png', 
			'assets/images/layout/bg/light_honeycomb.png', 
			'assets/images/layout/bg/littleknobs.png', 
			'assets/images/layout/bg/nistri.png', 
			'assets/images/layout/bg/noise_lines.png', 
			'assets/images/layout/bg/noise_pattern_with_crosslines.png', 
			'assets/images/layout/bg/noisy_grid.png', 
			'assets/images/layout/bg/norwegian_rose.png', 
			'assets/images/layout/bg/pineapplecut.png', 
			'assets/images/layout/bg/pinstripe.png', 
			'assets/images/layout/bg/project_papper.png', 
			'assets/images/layout/bg/ravenna.png', 
			'assets/images/layout/bg/reticular_tissue.png', 
			'assets/images/layout/bg/retina_wood.png', 
			'assets/images/layout/bg/rockywall.png', 
			'assets/images/layout/bg/roughcloth.png', 
			'assets/images/layout/bg/shattered.png', 
			'assets/images/layout/bg/silver_scales.png', 
			'assets/images/layout/bg/skelatal_weave.png', 
			'assets/images/layout/bg/small-crackle-bright.png', 
			'assets/images/layout/bg/small_tiles.png', 
			'assets/images/layout/bg/square_bg.png', 
			'assets/images/layout/bg/struckaxiom.png', 
			'assets/images/layout/bg/subtle_stripes.png', 
			'assets/images/layout/bg/vichy.png', 
			'assets/images/layout/bg/washi.png', 
			'assets/images/layout/bg/wavecut.png', 
			'assets/images/layout/bg/weave.png', 
			'assets/images/layout/bg/whitey.png', 
			'assets/images/layout/bg/wood_pattern.png', 
			'assets/images/layout/bg/white_brick_wall.png', 
			'assets/images/layout/bg/white_tiles.png', 
			'assets/images/layout/bg/worn_dots.png'
		], 
		invalidPatterns: [], 

		init: function( ) {
			var customizer = $( '#customizer', 'body' ), self = this;

			if( customizer && customizer.length ) {

				// Generate Pattern List
				var patternList = $( '<div id="pattern-list" class="clearfix"><ul></ul></div>' ).appendTo( customizer );

				customizer.find( '#showButton' ).on( 'click', $.proxy(function(e) { 
					customizer.animate({ left: customizer.css('left') === '0px'? '-236px' : 0 }, 'slow');

					if( !patternList.hasClass( 'patterns-loaded' ) ) {
						this.preloadPatterns(function() {
							$.each(this.patterns, function(i, p) {
								if( $.inArray( p, self.invalidPatterns) == -1 ) {
									$( 'ul', patternList).append( $('<li></li>').css( 'backgroundImage', 'url(' + p + ')' ) );
								}
							});
							patternList
								.addClass( 'patterns-loaded' )
								.on( 'click', 'li', function() {
									$('body').css( 'backgroundImage', $(this).css( 'backgroundImage' ) );
								});
						});
					}
				}, this));

				// Init Layout Changer
				customizer.find( 'input:checkbox[name="layout-mode"]' )
					.on( 'change', function() {
						$( '#wrapper' ).toggleClass( 'full', !$(this).attr('checked') );
					})
					.prop('checked', !$('#wrapper').hasClass('full'))
					.trigger('change');
			}
		}, 

		preloadPatterns: function( callback ) {
			var toLoad = this.patterns.length, 
				self = this;
			$.each(this.patterns, function(i, p) {
				var img = $( '<img />' ).load( function() {
					toLoad--;
					toLoad <= 0 && callback.call( self );
				}).error(function() {
					toLoad--;
					self.invalidPatterns.push(p);
				});

				img[0].src = p;
			});
		}
	};

	$( document ).ready( function( ) {
		new Customizer();
	});

	
}) (jQuery, window, document);