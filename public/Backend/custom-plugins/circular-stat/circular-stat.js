/*
 * MoonCake v1.3.1 - Circular Stat JS
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

;(function( $, window, document, undefined ) {
	"use strict";
	
	// our plugin constructor
	function CircularStat( element, options ) {
		if( arguments.length ) {
			this._init( element, options );
		}
    };
	
	// the plugin prototype
	CircularStat.prototype = {
		defaults: {
			percent: true, 
			value: 0, 
			maxValue: 100, 
			radius: 32, 
			thickness: 6, 
			backFillColor: '#eeeeee', 
			fillColor: '#e15656', 
			centerFillColor: '#ffffff', 
			decimals: 0, 
			retinaReady: true
		}, 

		_init: function( element, options ) {
			this.element = $( element );
			this.options = $.extend( {}, this.defaults, options, this.element.data() );
			this.canvas = this._build();

			this._draw( this._prepareCanvas( this.canvas ) );
		}, 

		_build: function() {
			var span = $('<span></span>'), 
				canvas = document.createElement('canvas');
			
			this.element
				.append(span.clone().addClass('digit-container'))
				.append(span.clone().addClass('canvas-container').append($(canvas)))
				.addClass('circular-stat');
				
			if (!canvas.getContext) {
				if(typeof(window.G_vmlCanvasManager) !== 'undefined') {
					canvas = window.G_vmlCanvasManager.initElement(canvas);
				} else {
					console.log('Your browser does not support HTML5 Canvas, or excanvas.js is missing on IE');
					this.element.hide();
					return false;
				}
			}
			
			return canvas;
		}, 

		_getPixelRatio: function( context ) {
			// http://www.html5rocks.com/en/tutorials/canvas/hidpi/
			var devicePixelRatio = window.devicePixelRatio || 1;
			var backingStoreRatio = 
				context.webkitBackingStorePixelRatio ||
				context.mozBackingStorePixelRatio ||
				context.msBackingStorePixelRatio ||
				context.oBackingStorePixelRatio ||
				context.backingStorePixelRatio || 1;

			return devicePixelRatio / backingStoreRatio;
		}, 

		_prepareCanvas: function( canvas ) {
			var diameter = this.options.radius * 2;
			var context = canvas.getContext("2d");
			var ratio = this._getPixelRatio( context );

			canvas.width = canvas.height = diameter * ratio;

			if( ratio > 1 ) {
				canvas.style.width = diameter + 'px';
				canvas.style.height = diameter + 'px';
			}

			return canvas;
		}, 

		_draw: function( canvas ) {

			var context = canvas.getContext("2d");
			var ratio = this._getPixelRatio( context );

			var data = {
				endAngle: ((this.options.value / this.options.maxValue) * 2 * Math.PI), 
				endValue: this.options.value, 
				centerX: canvas.width / 2, 
				centerY: canvas.height / 2, 
				radius: this.options.radius
			};
			var value = this._getVal().toFixed(this.options.decimals);

			context.save();

			context.clearRect(0, 0, canvas.width, canvas.height);
			context.translate(data.centerX, data.centerY);

			context.scale(ratio, ratio);
			context.rotate(-Math.PI / 2);

				context.fillStyle = this.options.backFillColor;
				context.beginPath();
					context.arc(0, 0, data.radius, 0, Math.PI * 2, false);
				context.closePath();
				context.fill();

				context.fillStyle = this.options.fillColor;
				context.beginPath();
					context.arc(0, 0, data.radius, 0, data.endAngle, false);
					context.lineTo(0, 0);
				context.closePath();
				context.fill();

				context.fillStyle = this.options.centerFillColor;
				context.beginPath();
					context.arc(0, 0, data.radius - this.options.thickness, 0, Math.PI * 2, false);
				context.closePath();
				context.fill();

			context.restore();


			$('.digit-container', this.element)
				.css({ lineHeight: this.options.radius * 2 + 'px' })[0]
					.innerHTML = this.options.percent? 
						('<span>' + value + '%</span>') : 
						('<span>' + value + '</span>' + '/' + this.options.maxValue.toFixed(this.options.decimals));
		}, 
		
		_getVal: function() {
			if(this.options.percent)
				return (this.options.value / this.options.maxValue) * 100;
			else {
				return this.options.value;
			}
		}, 

		redraw: function() {
			this._draw( this.canvas );
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

				switch(key) {
					case 'value':
						value = Math.max(0, Math.min(value, this.options.maxValue));
						break;
					default:
						break;
				}

				this.options[ key ] = value;
				this._draw( key === 'radius'? this._prepareCanvas( this.canvas ) : this.canvas );
			}

			return this;
		}
	}

	$.fn.circularStat = function( options ) {

		var isMethodCall = typeof options === "string",
			args = Array.prototype.slice.call( arguments, 1 ),
			returnValue = this;

		// prevent calls to internal methods
		if ( isMethodCall && options.charAt( 0 ) === "_" ) {
			return returnValue;
		}

		if ( isMethodCall ) {
			this.each(function() {
				var instance = $.data( this, 'circular' ),
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
				var instance = $.data( this, 'circular' );
				if ( !instance ) {
					$.data( this, 'circular', new CircularStat( this, options ) );
				}
			});
		}

		return returnValue;
	};

	/* DATA-API
	* ================== */

	$(function () {
		$('[data-provide="circular"]').each(function () {
			var $circular = $(this);
			$circular.circularStat($circular.data());
		});
	});

})( jQuery, window , document );
