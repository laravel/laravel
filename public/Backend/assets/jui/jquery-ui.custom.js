/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
 *
 * MoonCake v1.3.1 - Extended jQuery-UI Widgets
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
 
/* jQuery-UI Extended Slider v1.1
 *
 * This extends jQuery-UI Slider with Tooltip and Ticks
 *
 * Options:
 *    ticks: An array that contains tick values to show below/beside the slider
 *    tooltip: none | always | ondrag
 *          This specify which event will toggle the tooltip
 *
================================================== */

$.widget('ui.slider', $.ui.slider, {
	options: {
		ticks: [], 		
		tooltip: 'always' // none | always | ondrag
	}, 

	_isExtended: false, 
	_handleMouseIn: false, 
	_sliding: false, 
	
	_create: function() {
		this._super();
	
		var self = this, 
			tooltip = '<span class="ui-slider-handle-tooltip ui-state-default" style="display: none; "></span>', 
			tooltips = [];
		
		this.handles.each(function( i ) {
			tooltips.push( tooltip );
		});
		
		this.tooltips = $( tooltips.join('') ).appendTo( self.element );
			
		self.handles.each( function( i, handle ) {
			$( handle )
				.on( "mouseenter.slider", { 'index': i }, $.proxy( self._handleMouseEnter, self ))
				.on( "mouseleave.slider", { 'index': i }, $.proxy( self._handleMouseLeave, self ));
		});
		
		this._buildTicks();
		this._isExtended = true;
		this._refreshValue();
	}, 

	_start: function( event, index ) {
		this.options.tooltip === 'ondrag' && $( this.tooltips[ index ] ).stop(true, true).fadeIn();
		this._sliding = true;
		
		return this._super(event, index );
	}, 

	_stop: function( event, index ) {
		!this._handleMouseIn && $( this.tooltips[ index ] ).stop(true, true).fadeOut();
		this._sliding = false;
		
		return this._super(event, index);
	}, 

	_setOption: function( key, value ) {
		this._super( key, value );

		switch ( key ) {
			case "ticks":
				this._clearTicks();
				this._buildTicks();
				break;
			default: 
				break;
		}
	}, 

	_refreshValue: function() {
		this._super();
		
		if( this._isExtended ) {
			var o = this.options,
				self = this,
				animate = ( !this._animateOff ) ? o.animate : false,
				valPercent,
				_css = {},
				t, 
				value,
				valueMin,
				valueMax;
			
			if ( o.values && o.values.length ) {
				this.handles.each(function( i, j ) {					
					t = $( self.tooltips[ i ] );
					if( t && t.length ) {
						valPercent = ( self.values(i) - self._valueMin() ) / ( self._valueMax() - self._valueMin() ) * 100;
						
						t.text( self._formatNumber( self.values(i) ) );
						
						if( self.orientation === "horizontal" ) {
							_css[ "marginLeft" ] = -( t.outerWidth() / 2 );
							_css[ "left" ] = valPercent + "%";
						} else {
							_css[ "marginBottom" ] = -( t.outerHeight() / 2 );
							_css[ "bottom" ] = valPercent + "%";
						}
							
						t.css( _css );
					}
				});
			} else {				
				t = $( self.tooltips[ 0 ] );
				if( t && t.length ) {
					value = this.value();
					valueMin = this._valueMin();
					valueMax = this._valueMax();
					valPercent = ( valueMax !== valueMin ) ?
							( value - valueMin ) / ( valueMax - valueMin ) * 100 :
							0;
							
					t.text( self._formatNumber( value ) );
					
					if( self.orientation === "horizontal" ) {
						_css[ "marginLeft" ] = -( t.outerWidth() / 2 );
						_css[ "left" ] = valPercent + "%";
					} else {
						_css[ "marginBottom" ] = -( t.outerHeight() / 2 );
						_css[ "bottom" ] = valPercent + "%";
					}
						
					t.css( _css );
				}
			}
		}
	}, 

	destroy: function() {
		
		if( this._isExtended ) {
			var self = this;
			
			self.handles.each(function( i, handle ) {
				$( handle ).off( ".slider");
			});
			
			self.tooltips.remove();
			self.ticks.remove();
		}
		
		this._super();
	}, 

	_buildTicks: function() {
		if( this.options.ticks && this.options.ticks.length ) {
			this._clearTicks();

			var ticks = $('<div class="ui-slider-ticks"></div>').appendTo( this.element ), 
				s = this.options.ticks, 
				prc = (100.0 / ( s.length - 1 ) );
			
			for( var i =  0; i < s.length; i++ ) {
				
				if( this.orientation === "horizontal" )
					ticks.append('<span style="left: ' + (i * prc) + '%">' + ( s[i] != '|' ? '<ins>' + s[i] + '</ins>' : '' ) + '</span>');
				else
					ticks.append('<span style="bottom: ' + (i * prc) + '%">' + ( s[i] != '|' ? '<ins>' + s[i] + '</ins>' : '' ) + '</span>');
			};
		}
	}, 

	_clearTicks: function() {
		this.element.find('.ui-slider-ticks').remove();
	}, 

	_formatNumber: function( value ){
		value = value.toString().replace(/,/gi, ".").replace(/ /gi, "");
		return new Number(value);
	}, 

	_handleMouseEnter: function( ev ) {
		if( !this._handleMouseIn && this.options.tooltip === 'always') {
			this._handleMouseIn = true;
			$( this.tooltips[ ev.data.index ] ).stop(true, true).fadeIn();
		}
	}, 

	_handleMouseLeave: function( ev ) {
		if( this._handleMouseIn && this.options.tooltip === 'always') {				
			if( !this._sliding )
				$( this.tooltips[ ev.data.index ] ).stop(true, true).fadeOut();
			this._handleMouseIn = false;
		}
	}
});


/* jQuery-UI Extended ProgressBar v1.0
 *
 * This integrates jQuery-UI ProgressBar with Bootstrap.
 *
 * Options:
 *    striped: enable Bootstrap striped progress bar style
 *    active: enable animation on striped progress bar
 *    showValue: show the progress bar value
 *    type: info | success | warning | danger, 
 *          This specifies which Bootstrap progress bar style
 *
================================================== */

$.widget('ui.progressbar', $.ui.progressbar, {
	options: {
		striped: false, 
		active: false, 
		showValue: false, 
		type: null
	}, 

	_create: function() {
		this._super();
		
		this.element
			.addClass( 'progress' );

		this.element.toggleClass( 'progress-striped', this.options.striped );
		this.element.toggleClass( 'active', this.options.active );
			
		if( $.inArray( this.options.type, ['info', 'success', 'warning', 'danger'] ) )
			this.element.addClass( 'progress-' + this.options.type );
			
		this.valueDiv
			.addClass( 'bar' )
			.append( $( '<span></span>' ).toggle( this.options.showValue ) );
			
		this._refreshValue();
	}, 

	_refreshValue: function() {
		
		this._super();
		
		var value = this.value();

		this.valueDiv
			.find( ' > span' )
			.text( value + '%' );
	}, 

	_setOption: function( key, value ) {
		this._super(key, value);

		switch ( key ) {
			case "striped":
				this.element.toggleClass( 'progress-striped', this.options.striped );
				break;
			case "active":
				this.element.toggleClass( 'active', this.options.active );
				break;
			case "showValue":
				this.valueDiv.find('span').toggle( this.options.showValue );
				break;
			case "type":
				this.element
					.removeClass(['progress-info', 'progress-success', 'progress-warning', 'progress-danger'].join(' '))
					.addClass( $.inArray( this.options.type, ['info', 'success', 'warning', 'danger'] )? 'progress-' + this.options.type : '' );
				break;
			default:
				break;
		}
	}, 

	destroy: function() {
		
		this.element.removeClass( 'progress progress-striped active ' + ['progress-info', 'progress-success', 'progress-warning', 'progress-danger'].join(' ') );
		
		this._super();
	}
});