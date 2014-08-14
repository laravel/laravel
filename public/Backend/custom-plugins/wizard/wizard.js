/*
 * MoonCake v1.3.1 - Wizard Plugin
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

;(function($, window, document, undefined) {
	"use strict";

	// our plugin constructor
	var Wizard = function( element, options ) {
		if( arguments.length ) {
			this._init( element, options );
		}
    };
	
	// the plugin prototype
	Wizard.prototype = {
		defaults: {
			// Element Selectors
			element: '.wizard-step', 
			navLabelElement: '.wizard-label', 

			// Container Classes
			navContainerClass: 'wizard-nav', 
			buttonContainerClass: 'form-actions btn-toolbar', 

			// Button Classes
			defaultButtonClass: 'btn', 
			responsiveNextButtonClass: 'responsive-next-btn', 
			responsivePrevButtonClass: 'responsive-prev-btn', 
			submitButtonClass: 'btn-primary pull-right', 
			
			// Button Attributes
			responsiveNextButtonLabel: '<i class="icon-caret-right"></i>', 
			responsivePrevButtonLabel: '<i class="icon-caret-left"></i>', 
			nextButtonLabel: 'Next', 
			prevButtonLabel: 'Prev', 
			submitButtonLabel: 'Submit', 
			submitButtonName: 'wizard-submit', 

			// Wizard Options
			forwardOnly: false, 
			orientation: 'horizontal', 

			// Wizard Callbacks
			onStepLeave: null, // function(wizard, step);
			onStepShown: null, // function(wizard, step);
			onBeforeSubmit: null, // function(wizard, form);

			// Ajax Submit [Requires jQuery Form Plugin]
			ajaxSubmit: false, 
			ajaxOptions: {}
		}, 
		
		_init: function( element, options ) {

			// Basic Initialization
			this.element = $( element );
			this.options = $.extend( {}, this.defaults, options, this.element.data() );

			//Global Variables
			this._activeWzdId = -1;
			this._navigationLocked = false;
			this._activatedSteps = [];

			// Parse Options
			this._parseOptions();

			// Retrieve the steps
			this.steps = this.element.find( this.options.element );

			// Hide the steps
			this.steps.hide();

			// Build and retrieve Navigation
			this.nav = this._buildNavigation( this.steps );

			// Build and retrieve Buttons
			this.buttons = this._buildButtons();

			// Bind Events
			this._bindEvents();

			// Goto first step
			this._navigate( this.steps.eq( 0 ).data( 'wzd-id' ), true );
		}, 

		_parseOptions: function() {
			// Prepare Ajax Form
			if( this.options.ajaxSubmit && $.fn.ajaxSubmit ) {
				var formOptionsSuccess = this.options.ajaxOptions.success;
				var formOptionsComplete = this.options.ajaxOptions.complete;
				var formOptionsError = this.options.ajaxOptions.error;
				var formOptionsBeforeSend = this.options.ajaxOptions.beforeSend;
				var formOptionsBeforeSubmit = this.options.ajaxOptions.beforeSubmit;
				var formOptionsBeforeSerialize = this.options.ajaxOptions.beforeSerialize;

				this.options.ajaxOptions = $.extend( {}, this.options.ajaxOptions, {
					success: function( responseText, textStatus, xhr, form ) {
						$.isFunction( formOptionsSuccess ) && formOptionsSuccess.call( this, responseText, textStatus, xhr, form );
					}, 

					complete: function( xhr, textStatus ) {
						$.isFunction( formOptionsComplete ) && formOptionsComplete.call( this, xhr, textStatus );
					}, 

					error: function( xhr, textStatus ) {
						$.isFunction( formOptionsError ) && formOptionsError.call( this, xhr, textStatus );
					}, 

					beforeSubmit: function( data, form, options ) {
						if( $.isFunction( formOptionsBeforeSubmit ) ) {
							return formOptionsBeforeSubmit.call( this, data, form, options );
						}
						return true;
					}, 

					beforeSend: function( xhr ) {
						if( $.isFunction( formOptionsBeforeSend ) ) {
							return formOptionsBeforeSend.call( this, xhr );
						}
						return true;
					}, 

					beforeSerialize: function( form, options ) {
						if( $.isFunction( formOptionsBeforeSerialize ) ) {
							return formOptionsBeforeSerialize.call( this, form, options );
						}
						return true;
					}
				});

				this.element.ajaxForm( this.options.ajaxOptions );
			}

			// Set Orientation
			if( !$.inArray( this.options.orientation, [ 'horizontal', 'vertical' ] ) )
				this.options.orientation = 'horizontal';

			this.element.addClass( 'wizard-form ' + 'wizard-form-' + this.options.orientation );
		}, 

		_callFunction: function( fn, args ) {
			return !$.isFunction( fn )? true : fn.apply( this, args );
		}, 

		_generateRandomId: function() {
			var guid = new Date().getTime().toString(32), i;

			for (i = 0; i < 3; i++) {
				guid += Math.floor(Math.random() * 65535).toString(32);
			}

			return 'wzd_' + guid;
		}, 

		_buildNavigation: function( steps ) {
			var navContainer = $( '<div class="' + this.options.navContainerClass + ' ' + this.options.navContainerClass + '-' + this.options.orientation + '"></div>' );
			var nav = $( '<ul></ul>' );
			var guid = this._generateRandomId();

			$.each( steps, $.proxy(function( i, step ) {
				var item = $( '<li><span></span></li>' );
				var title = $( step ).find( this.options.navLabelElement ).hide();

				item.find( 'span' )
					.html( title && title.length? title.html() : 'Step ' + i )
					.end().appendTo( nav )
					.add( step ).attr( 'data-wzd-id', guid + '_' + i );
			}, this));

			return navContainer.append( nav ).insertBefore( this.element );
		}, 

		_buildButtons: function() {
			var btnContainer = $( '<div class="' + this.options.buttonContainerClass + '"></div>' );
			var btn = $( '<button type="button"></button>' ).addClass( this.options.defaultButtonClass );

			var prevButton = btn.clone().addClass( this.options.prevButtonClass ).text( this.options.prevButtonLabel );
			var responsivePrevButton = btn.clone().addClass( this.options.responsivePrevButtonClass ).html( this.options.responsivePrevButtonLabel );

			var nextButton = btn.clone().addClass( this.options.nextButtonClass ).text( this.options.nextButtonLabel );
			var responsiveNextButton = btn.clone().addClass( this.options.responsiveNextButtonClass ).html( this.options.responsiveNextButtonLabel );
			
			var submitButton = btn.clone().addClass( this.options.submitButtonClass ).text( this.options.submitButtonLabel ).attr( 'name', this.options.submitButtonName );

			this.nav && this.nav.length && this.nav.append( [ responsivePrevButton, responsiveNextButton ] );
			btnContainer.append( [ prevButton, nextButton, submitButton ] ).appendTo( this.element );

			return {
				prev: prevButton, 
				next: nextButton, 
				responsivePrev: responsivePrevButton, 
				responsiveNext: responsiveNextButton, 
				submit: submitButton
			};
		}, 

		_refreshButtons: function() {
			this.buttons.prev.add( this.buttons.responsivePrev ).attr( 'disabled', this._isFirstStep( this._activeWzdId ) || this.options.forwardOnly );
			this.buttons.next.add( this.buttons.responsiveNext ).attr( 'disabled', this._isLastStep( this._activeWzdId ) );

			this.buttons.submit.toggle( this._isLastStep( this._activeWzdId ) );
		}, 

		_bindEvents: function() {
			var that = this;

			this.nav.on( 'click.wizard', 'li', function( e ) {
				that._navigate( $( this ).data('wzd-id') );
				e.preventDefault();
			});

			this.buttons.prev.add( this.buttons.responsivePrev ).on( 'click.wizard', function( e ) {
				that.prev();
				e.preventDefault();
			});

			this.buttons.next.add( this.buttons.responsiveNext ).on( 'click.wizard', function( e ) {
				that.next();
				e.preventDefault();
			});

			this.buttons.submit.on( 'click.wizard', function( e ) {
				that.submitForm();
				e.preventDefault();
			});
		}, 

		_canNavigate: function( wzdId ) {
			var step = this._findStep( wzdId );
			var currentStep = this._findStep( this._activeWzdId );

			return !this._navigationLocked && !(this.options.forwardOnly && step && currentStep && step.index() <= currentStep.index());
		}, 

		_stepActivated: function( wzdId ) {
			return this._validWzdId( wzdId ) && $.inArray( wzdId, this._activatedSteps ) > -1;
		}, 

		_activateStep: function( wzdId ) {
			if ( this._validWzdId( wzdId ) ) {
				var stepIndex = this._findNav( wzdId ).index();
				for( var i = 0; i < stepIndex; ++i) { 
					if( $.inArray( this.steps.eq( i ).data( 'wzd-id' ), this._activatedSteps ) === -1 ) {
						return;
					}
				}
				$.inArray( wzdId, this._activatedSteps ) === -1 && this._activatedSteps.push( wzdId );
			}
		}, 

		_findStep: function( wzdId ) {
			return this.steps.filter( '[data-wzd-id="' + wzdId + '"]' ).first();
		}, 

		_findNav: function( wzdId ) {
			return this.nav.find('li').filter( '[data-wzd-id="' + wzdId + '"]' ).first();
		}, 

		_navigate: function( wzdId, ignore ) {
			if( this._validWzdId( wzdId ) ) {
				if( ignore || ( this._canNavigate( wzdId ) && this._callFunction(this.options.onStepLeave, [ this, this._findStep( this._activeWzdId )[0] ] ) ) ) {
					this._activateStep( wzdId );
					this._showStep( wzdId );
				}
			}
		}, 

		_showStep: function( wzdId ) {
			if( this._validWzdId( wzdId ) ) {
				if( this._activeWzdId === -1 ) {
					this.steps.hide();
					this._findStep( wzdId ).show();
					this._updateNav( wzdId );
					this._activeWzdId = wzdId;
					this._refreshButtons();
				} else if( wzdId !== this._activeWzdId && this._stepActivated( wzdId ) ) {
					var activeStep = this._findStep( this._activeWzdId );
					var that = this;

					this._navigationLocked = true;
					activeStep.fadeOut( 'fast', function() {
						that._updateNav( wzdId );

						var newStep = that._findStep( wzdId );
						newStep.fadeIn( 'fast', function() {
							that._activeWzdId = wzdId;
							that._navigationLocked = false;
							that._refreshButtons();

							that._callFunction( that.options.onStepShown, [ that, newStep ] );
						});
					});
				}
			}
		}, 

		_updateNav: function( wzdId ) {
			if( this._validWzdId( wzdId ) ) {
				var nav = this._findNav( wzdId );
				nav.siblings('li')
					.removeClass( 'current' )
					.end()
				.addClass( 'current' );
			}
		}, 

		_isLastStep: function( wzdId ) {
			return this._validWzdId( wzdId ) && wzdId === this.steps.last().data('wzd-id');
		}, 

		_isFirstStep: function( wzdId ) {
			return this._validWzdId( wzdId ) && wzdId === this.steps.first().data('wzd-id');
		}, 

		_validWzdId: function( wzdId ) {
			return typeof( wzdId ) === 'string' && wzdId.indexOf( 'wzd_' ) === 0;
		}, 

		next: function() {
			if( !this._isLastStep( this._activeWzdId ) ) {
				this._navigate( this._findStep( this._activeWzdId ).next().data( 'wzd-id' ) );
			}
		}, 

		prev: function() {
			if( !this._isFirstStep( this._activeWzdId ) ) {
				this._navigate( this._findStep( this._activeWzdId ).prev().data( 'wzd-id' ) );
			}
		}, 

		submitForm: function() {
			if( this._callFunction( this.options.onBeforeSubmit, [ this, this.element ] ) ) {
				this.element.submit();
			}
		}, 

		reset: function() {
			// Reset Variables
			this._activatedSteps = [];
			this._activeWzdId = -1;

			// Hide Steps
			this.steps.hide();

			// Clear form fields
			$.fn.clearForm && this.element.clearForm();

			// Go to first step
			this._navigate( this.steps.eq( 0 ).data( 'wzd-id' ), true );
		}
	}
	
	$.fn.wizard = function(options) {

		var isMethodCall = typeof options === "string",
			args = Array.prototype.slice.call( arguments, 1 ),
			returnValue = this;

		// prevent calls to internal methods
		if ( isMethodCall && options.charAt( 0 ) === "_" ) {
			return returnValue;
		}

		if ( isMethodCall ) {
			this.each(function() {
				var instance = $.data( this, 'wizard' ),
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
				var instance = $.data( this, 'wizard' );
				if ( !instance ) {
					$.data( this, 'wizard', new Wizard( this, options ) );
				}
			});
		}

		return returnValue;
	};

})(jQuery, window , document);
