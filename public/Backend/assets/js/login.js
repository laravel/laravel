/*
 * MoonCake v1.3.1 - Login JS
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
	
	var LoginScreen = function() { }

	LoginScreen.prototype = {

		init: function() {
			this.transitionFn = this['_fade'];
			$( '#login-inner .login-inner-form' ).removeClass( 'active' );
			
			var firstInnerForm = $( '#login-inner .login-inner-form:first' ).addClass('active');

			$( '#login-buttons .btn' ).each($.proxy(function(i, btn) {
				var target = $($(btn).data( 'target' ));

				if( target && target.length ) {
					$(btn).toggleClass('disabled', $(target).is('.active'))
						.on('click', $.proxy(this._clickHandler, this));
				}
			}, this));

			return this;
		}, 

		_clickHandler: function(e) {
			var btn = $(e.currentTarget), 
				target = $(btn.data( 'target' ));

			if( !btn.is('.disabled') ) {
				if(this.transitionFn.call(this, target)) {
					$( '#login-buttons .btn' ).not(btn.addClass('disabled')).removeClass('disabled');
				}
			}

			e.preventDefault();
		}, 

		_fade: function( target ) {
			return !!$( '.login-inner-form.active' ).stop().fadeOut( 'normal', function() {
				target.stop().fadeIn( 'normal', function() {
					target.addClass('active');
				});
				$(this).removeClass( 'active' );
			}).length;
		}
	};

	$.loginScreen = new LoginScreen();

	$( document ).ready( function( e ) {

		$.loginScreen.init();

		// Style checkboxes and radios
		$.fn.uniform && $(':radio.uniform, :checkbox.uniform').uniform();

		// IE Placeholder
		$.fn.placeholder && $('[placeholder]').placeholder();

		// Validations
		if( $.fn.validate ) {

			$( '.login-inner-form > form' ).each(function() {
				$( this ).validate();
			});
		}
	});

	
}) (jQuery, window, document);
