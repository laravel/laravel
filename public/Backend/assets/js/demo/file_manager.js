/*
 * MoonCake v1.3.1 - File Manager Demo JS
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

		if($.fn.elfinder) {
			
			$( '#elfinder-demo' ).elfinder({
				url: 'plugins/elfinder/php/connector.php', 
				uiOptions : {
					// toolbar configuration
					toolbar : [
						['back', 'forward'],
						['reload'],
						['home', 'up'],
						// ['mkdir', 'mkfile', 'upload'],
						// ['open', 'download', 'getfile'],
						['info'],
						['quicklook'],
						['copy', 'cut', 'paste'],
						['rm'],
						['duplicate', 'rename', 'edit'],
						//['extract', 'archive'],
						['search'],
						['view'],
						['help']
					]
				}, 

				contextmenu : {
					// navbarfolder menu
					navbar : ['open', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'info'],

					// current directory menu
					cwd    : ['reload', 'back', '|', 'paste', '|', 'info'],

					// current directory file menu
					files  : [
						'getfile', '|','open', 'quicklook', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|',
						'rm', '|', 'edit', 'rename', 'resize', '|', 'archive', 'extract', '|', 'info'
					]
				},
			});
			
		}
	});
	
}) (jQuery, window, document);