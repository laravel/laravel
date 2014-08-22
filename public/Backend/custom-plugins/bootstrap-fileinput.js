/*
 * MoonCake v1.3.1 - FileInput Plugin JS
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
	function FileInput( element, options ) {
		if( arguments.length ) {
			this._init( element, options );
		}
    };
	
	// the plugin prototype
	FileInput.prototype = {
		defaults: {
			placeholder: 'No file selected...', 
			buttontext: 'Browse...', 
			inputSize: 'large'
		}, 

		_init: function( element, options ) {
			this.element = $( element );
			this.options = $.extend( {}, this.defaults, options, this.element.data() );

			this._build();
		}, 

		_build: function () {

			this.element.css( {
				'position': 'absolute', 
				'top': 0, 
				'right': 0, 
				'margin': 0, 
				'cursor': 'pointer', 
				'fontSize': '99px', 
				'opacity': 0, 
				'filter': 'alpha(opacity=0)'
			} )
			.on( 'change.fileupload', $.proxy( this._change, this) );

			this.container = $( '<div class="fileinput-holder input-append"></div>' )
				.append( $( '<div class="fileinput-preview uneditable-input" style="cursor: text; text-overflow: ellipsis; "></div>' )
						.addClass( 'input-' + this.options.inputSize ).text( this.options.placeholder ) )
				.append( $( '<span class="fileinput-btn btn" style="overflow: hidden; position: relative; cursor: pointer; "></span>' )
						.text( this.options.buttontext ) )
				.insertAfter( this.element );

			this.element.appendTo( this.container.find( '.fileinput-btn' ) );

		}, 

		_change: function ( e ) {
			
			var file = e.target.files !== undefined ? e.target.files[0] : { name: e.target.value.replace(/^.+\\/, '') };
			if ( !file ) return;
			
			this.container.find( '.fileinput-preview ' ).text(file.name);
		}
	}

	$.fn.fileInput = function( options ) {
		return this.each(function() {
			new FileInput( this, options );
		});
	};

	/* DATA-API
	* ================== */

	$(function () {
		$('[data-provide="fileinput"]').each(function () {
			var $input = $(this);
			$input.fileInput($input.data());
		});
	});

})( jQuery, window , document );
