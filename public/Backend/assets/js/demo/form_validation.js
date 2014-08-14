/*
 * MoonCake v1.3.1 - Form Validation Demo JS
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
			
	var demos = {
	};

	$(document).ready(function() {
							   
		if($.fn.select2) {
			
			$( '.select2-select' ).select2();
			
		}
		
		if( $.fn.validate ) {
			
			
			$("#validate-1").validate({
				rules: {
					req1: {
						required: true
					}, 
					email1: {
						required: true, 
						email: true
					}, 
					url1: {
						required: true, 
						url: true
					}, 
					pass1: {
						required: true, 
						minlength: 5
					}, 
					cpass1: {
						required: true, 
						minlength: 5, 
						equalTo: '[name="pass1"]'
					}, 
					digits1: {
						required: true, 
						digits: true
					}
				}, 
				invalidHandler: function(form, validator) {
					var errors = validator.numberOfInvalids();
					if (errors) {
						var message = errors == 1
						? 'You missed 1 field. It has been highlighted'
						: 'You missed ' + errors + ' fields. They have been highlighted';
						$("#da-ex-val1-error").html(message).show();
					} else {
						$("#da-ex-val1-error").hide();
					}
				}
			});
			
			$("#validate-2").validate({
				rules: {
					minl1: {
						required: true, 
						minlength: 5
					}, 
					maxl1: {
						required: true, 
						maxlength: 5
					}, 
					rangel1: {
						required: true, 
						rangelength: [5, 10]
					}, 
					min1: {
						required: true, 
						digits: true, 
						min: 5
					}, 
					max1: {
						required: true, 
						digits: true, 
						max: 5
					}, 
					range1: {
						required: true, 
						digits: true, 
						range: [5, 10]
					}
				}, 
				invalidHandler: function(form, validator) {
					var errors = validator.numberOfInvalids();
					if (errors) {
						var message = errors == 1
						? 'You missed 1 field. It has been highlighted'
						: 'You missed ' + errors + ' fields. They have been highlighted';
						$("#da-ex-val2-error").html(message).show();
					} else {
						$("#da-ex-val2-error").hide();
					}
				}
			});
			
			$("#validate-3").validate({
				ignore: '.ignore', 
				rules: {
					gender: {
						required: true
					}, 
					sport: {
						required: true
					}, 
					file1: {
						required: true, 
						accept: ['.jpeg']
					}, 
					dd1: {
						required: true
					}, 
					chosen1: {
						required: true
					}, 
					spin1: {
						required: true, 
						min: 5, 
						max: 10
					}
				}
			});
			
			$("#validate-4").validate({
				rules: {
					email: {
						required: true, 
						email: true
					}, 
					pass2: {
						required: true, 
						minlength: 5
					}, 
					cpass2: {
						required: true, 
						minlength: 5, 
						equalTo: '#pass2'
					}, 
					address: {
						required: "#souvenirs:checked"
					}
				}
			});
			
		}
		
	});
	
	$(window).load(function() {
		
		// When all page resources has finished loading
	});
	
}) (jQuery, window, document);