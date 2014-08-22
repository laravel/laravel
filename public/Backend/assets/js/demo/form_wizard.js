/*
 * MoonCake v1.3.1 - Form Wizard Demo JS
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
			
	$(document).ready(function() {
		
		// When all page resources has finished loading
		
		if( $.fn.wizard ) {
			
			$( '#wizard-demo-1' ).wizard();
			
			if( $.fn.validate ) {
				$wzd_form = $( '#wizard-demo-2' ).validate({ onsubmit: false });
				
				$( '#wizard-demo-2' ).wizard({
					onStepLeave: function(wizard, step) {
						return $wzd_form.form();
					}, 
					onBeforeSubmit: function() {
						return $wzd_form.form();
					}
				});
				
				$wzd_v_form = $( '#wizard-demo-3' ).validate({ onsubmit: false });
				
				$( '#wizard-demo-3' ).wizard({
					onStepLeave: function(wizard, step) {
						return $wzd_v_form.form();
					}, 
					onBeforeSubmit: function() {
						return $wzd_v_form.form();
					}
				});

				$wzd_v1_form = $( '#wizard-demo-4' ).validate({ onsubmit: false });

				$( '#wizard-demo-4' ).wizard({
					onStepLeave: function(wizard, step) {
						return $wzd_v1_form.form();
					}, 
					onBeforeSubmit: function() {
						return $wzd_v1_form.form();
					}, 
					ajaxSubmit: true, 
					ajaxOptions: {
						dataType: 'text', 
						beforeSubmit: function(formData) {
							alert( 'You\'re about to submit:\n\n' + $.param(formData) );
							return true;
						}, 
						success: function(response, status, xhr, form) {
							if( confirm( 'Form successfully submitted.\nServer Response:\n' + response + '\n\nReset Wizard?' ) ) {
								form.wizard( 'reset' );
								$wzd_v1_form.resetForm();
							}
						}
					}
				});
			}			
		}
	});
	
}) (jQuery, window, document);