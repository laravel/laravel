// <script src="/static/js/dev/grid.js"></script>

;(function(){

	var icon = {
		clear: '<svg width="30" height="30" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"><path class="cls-1" d="M10.21 22.78h9.58V11.41h-9.58v11.37zm6.59-9.58H18V21h-1.2v-7.8zm-2.39 0h1.2V21H14.4v-7.8zm-2.41 0h1.2V21H12v-7.8zm5.39-3.59V7.22h-4.78v2.39H9v1.2h12v-1.2h-3.61zm-1.2 0H13.8v-1.2h2.4v1.2z"/></svg>',
		scan: '<svg width="30" height="30" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"><path class="cls-1" d="M18.15 16a5.91 5.91 0 0 0 1.2-3.52 6 6 0 1 0-6 6 6.1 6.1 0 0 0 2.72-.66l5.64 5.64 1.86-1.86zm-4.71.6a4.12 4.12 0 1 1 4.12-4.12 4.14 4.14 0 0 1-4.12 4.15z"/></svg>',
		export: '<svg width="30" height="30" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"><path class="cls-1" d="M18.54 19.54H9.92v-8.62h4.31V9.69H8.69v11.08h11.08v-3.08h-1.23v1.85zM13 16.46s.43-3.08 5.54-3.08v3.08L25.31 12l-6.77-4.77v3.08c-5.91 0-5.54 6.15-5.54 6.15z"/></svg>'
	}

	var grid = {
		breakpoints: [
			'desktop-wide',
			'desktop',
			'tablet-wide',
			'tablet',
			'tablet-small',
			'phablet',
			'phone-wide',
			'phone',
			'base'
		],
		build: {},
		pages: [],
		readyToExport: false,
		init: function(){

			var storedData = localStorage.getItem('grid');

			// Have we stored this?
			if( storedData ){

				storedData = JSON.parse(storedData);

				grid.build = storedData.build;
				grid.pages = storedData.pages;

			}else{

				// Populate the breakpoints object with the different components
				grid.populateBuild();

				if( window.location.href.indexOf('.local:') >= 0 )
					grid.message('Warning', 'Using *.local domains can cause issues with local storage, we\'d suggest using a unique URL', 'orange');

			}

			// Are we ok to export?
			grid.readyToExport = grid.pages && grid.pages.length > 0;

			$('<style/>', {
				html: '.GRIDTOOL__root {position: fixed; opacity: .1; left: 10px; bottom: 10px; z-index: 1000; } .GRIDTOOL__root:hover { opacity: 1; } .GRIDTOOL__icon { cursor: pointer; display: block; width: 30px; height: 30px; background: white; } .GRIDTOOL__icon svg {display: block; width: 100%; height: 100%;} .GRIDTOOL__icon:hover { background: black;} .GRIDTOOL__icon:hover svg { fill: white; } .GRIDTOOL__close { padding: 10px; position: fixed; top: 20px; right: 20px; z-index: 1005; } .GRIDTOOL__clear:hover { background: red; }'
			}).appendTo('body');

			// Add some UI to the page
			var $root = $('<div/>', {
				'class': 'GRIDTOOL__root'
			});

			var $icon = $('<span/>', {
				'class': 'GRIDTOOL__icon'
			});

			var $clear = $icon.clone().html(icon.clear);
			var $export = $icon.clone().html(icon.export);
			var $scan = $icon.clone().html(icon.scan);

			$clear.appendTo($root).addClass('GRIDTOOL__clear');
			$export.appendTo($root);
			$scan.appendTo($root);

			$root.appendTo('body');

			$export.on('click', grid.export);
			$scan.on('click', grid.run);
			$clear.on('click', grid.clear);

		},
		populateBuild: function(){

			grid.build = {};

			for( var i = grid.breakpoints.length - 1; i >= 0; i-- ){

				grid.build[ grid.breakpoints[i] ] = {
					width: [],
					push: [],
					pull: [],
					block: [],
					flush: []
				}

			}

		},
		clear: function(){

			if( window.confirm('This will delete all scanned grid info for all pages.\nAre you sure you want to continue?') ){

				localStorage.removeItem('grid');

				grid.populateBuild();
				grid.pages = [];

				grid.readyToExport = false;

				grid.message('Deleted', 'Click the scan button to start again!', 'red');

			}

		},
		syncStorage: function(){

			// Put the object into storage
			var storedData = {
				build: grid.build,
				pages: grid.pages
			};

			localStorage.setItem('grid', JSON.stringify(storedData));

		},
		run: function(){

			// Do the scans
			grid.scan('width', 'width--');
			grid.scan('push', 'push--');
			grid.scan('pull', 'pull--');
			grid.scan('block', 'block-grid--');
			grid.scan('flush', 'flush-first--');

			// Get the page path and store that
			var pagePath = window.location.pathname,
				pageReport = '',
				alreadyScanned = false;

			// Add index to the name if it's the root (just to be clear)
			if( pagePath == '/' )
				pagePath = '/ (index)';

			// Add it to the list if it isn't already in there
			if( grid.pages.indexOf(pagePath) < 0 ){
				grid.pages.push(pagePath);
			}else{
				alreadyScanned = true;
			}

			// Loop through the pages to add them in
			for( var i = grid.pages.length - 1; i >= 0; i-- ){
				pageReport += '- ' + grid.pages[i] + '\n';
			}

			// Report on it in the console
			grid.message('Page ' + ( alreadyScanned ? 're-scanned' : 'scanned' ), 'In total, you have scanned ' + grid.pages.length + ' page' + ( grid.pages.length > 1 ? 's' : '' ) + ', see below for details:', 'orange');
			console.log(pageReport);
			grid.message(false, 'You can now export the results');

			// Store everything locally
			grid.syncStorage();

			// We're ready to export at this point
			grid.readyToExport = true;

		},
		scan: function(id, classSegment){

			// Gather
			$('[class*="' + classSegment + '"]')
				.each(function(){

					// Get the classes for each column and put them into an array
					var classes = this.classList;

					// Loop through the breakpoints
					for( var b = grid.breakpoints.length - 1; b >= 0; b-- ){

						// Store the object for easy access
						var bp = grid.build[grid.breakpoints[b]][id];

						// Loop through the classes for each breakpoint
						for( var c = classes.length - 1; c >= 0; c-- ){

							var selector = grid.breakpoints[b] === 'base' ? classSegment : classSegment + grid.breakpoints[b] + '-',
								expression = selector + '\\d',
								re = new RegExp(expression),
								found = classes[c].match(re);

							if( found ){

								var num = parseFloat( classes[c].replace(selector, ''));

								if( bp.indexOf(num) < 0 )
									bp.push(num);

							}

						}

					}

				});

			// Sort
			$.each(grid.build, function(bp, obj){

				// Block
				obj[id].sort(function(a, b){
					return a - b;
				});

			});

		},
		export: function(){

			if( !grid.readyToExport ){
				grid.message('No data', "We'll scan this page...", 'grey');
				grid.run();
			}

			var count = 0,
				scss = '$grid-bp: (\n';

			for( var i = grid.breakpoints.length - 1; i >= 0; i-- ){

				var bp = grid.build[ grid.breakpoints[i] ];

				scss += "	'" + grid.breakpoints[i] + "': (\n";
				scss += "		'width': (" + bp.width + "),\n"
				scss += "		'push': (" + bp.push + "),\n"
				scss += "		'pull': (" + bp.pull + "),\n"
				scss += "		'block': (" + bp.block + "),\n"
				scss += "		'flush': (" + bp.flush + ")\n"

				if( count == grid.breakpoints.length - 1 ){
					scss += '	)\n';
				}else{
					scss += '	),\n';
				}

				count++;

			}

			scss += ');';

			var $ta = $('<textarea></textarea>'),
				$darken = $('<div></div>');

			$darken
				.css({
					position: 'fixed',
					top: 0,
					left: 0,
					zIndex: 1001,
					width: '100vw',
					height: '100vh',
					background: 'rgba(0,0,0,.8)'
				});

			$ta
				.html(scss)
				.css({
					fontFamily: '"Courier New",Courier,"Lucida Sans Typewriter","Lucida Typewriter",monospace',
					fontSize: 13,
					position: 'fixed',
					top: '5vmin',
					left: '5vmin',
					zIndex: 1002,
					width: '90vw',
					height: '90vh'
				});

			var $close = $('<span/>', {
					'class': 'GRIDTOOL__icon GRIDTOOL__close',
					html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16.9 16.9"><path d="M16.9 1.9L15 0 8.5 6.5 1.9 0 0 1.9l6.5 6.6L0 15l1.9 1.9 6.6-6.5 6.5 6.5 1.9-1.9-6.5-6.5"/></svg>'
				})
				.appendTo('body')
				.on('click', function(){
					$close.remove();
					$ta.remove();
					$darken.remove();
				});

			$darken.appendTo('body');

			$ta
				.appendTo('body')
				.on('focus', function(){
					$ta.select();
				});

			grid.message('Success!', 'Remember, remove grid.js when making your project live.');

		},
		message: function(title, body, color){

			if( !color )
				color = 'green';

			var cssh = 'font-size: 13px; font-weight: bold; text-transform: uppercase; color: ' + color + ';',
				cssp = 'font-size: 11px; color: grey; font-style: italic;';

			if( title )
				console.log('%c\n' + title + ' %s', cssh, '');

			if( body )
				console.log('%c' + body + ' %s', cssp, '');

		}
	};

	grid.init();

	window.grid = grid;

}());
