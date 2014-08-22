/*
 * MoonCake v1.3.1 - File Upload Demo JS
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

	$(document).ready(function() { });
	
	$(window).load(function() {
		
		// When all page resources has finished loading
		
		if( $.fn.pluploadBootstrap ) {
			
			
			$( "#plupload-demo" ).pluploadBootstrap({
											  
				// General settings
				runtimes : 'html5,html4',
				url : 'php/plupload.php',
				max_file_size : '256kb',
				chunk_size : '64kb',
				unique_names : true,
		
				// Resize images on clientside if we can
				resize : { width : 320, height : 240, quality : 90 },
		
				// Specify what files to browse for
				filters : [
					{title : "Image files", extensions : "jpg, jpeg, gif, png"}
				],
		
				// Flash settings
				flash_swf_url : 'plugins/plupload/plupload.flash.swf',
		
				// Silverlight settings
				silverlight_xap_url : 'plugins/plupload/plupload.silverlight.xap'
			});
			
		}
	});
	
}) (jQuery, window, document);