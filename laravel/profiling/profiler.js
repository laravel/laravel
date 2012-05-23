var anbu = {

	// BOUND ELEMENTS
	// -------------------------------------------------------------
	// Binding these elements early, stops jQuery from "querying"
	// the DOM every time they are used.

	el: {
		main: $('.anbu'),
		close: $('#anbu-close'),
		zoom: $('#anbu-zoom'),
		hide: $('#anbu-hide'),
		show: $('#anbu-show'),
		tab_pane: $('.anbu-tab-pane'),
		hidden_tab_pane: $('.anbu-tab-pane:visible'),
		tab: $('.anbu-tab'),
		tabs: $('.anbu-tabs'),
		tab_links: $('.anbu-tabs a'),
		window: $('.anbu-window'),
		closed_tabs: $('#anbu-closed-tabs'),
		open_tabs: $('#anbu-open-tabs'),
		content_area: $('.anbu-content-area')
	},

	// CLASS ATTRIBUTES
	// -------------------------------------------------------------
	// Useful variable for Anbu.

	// is anbu in full screen mode
	is_zoomed: false,

	// initial height of content area
	small_height: $('.anbu-content-area').height(),

	// the name of the active tab css
	active_tab: 'anbu-active-tab',

	// the data attribute of the tab link
	tab_data: 'data-anbu-tab',

	// size of anbu when compact
	mini_button_width: '2.6em',

	// is the top window open?
	window_open: false,

	// current active pane
	active_pane: '',

	// START()
	// -------------------------------------------------------------
	// Sets up all the binds for Anbu!

	start: function() {

		// hide initial elements
		anbu.el.close.css('visibility', 'visible').hide();
		anbu.el.zoom.css('visibility', 'visible').hide();
		anbu.el.tab_pane.css('visibility', 'visible').hide();

		// bind all click events
		anbu.el.close.click(function(event) {
			anbu.close_window();
			event.preventDefault();
		});
		anbu.el.hide.click(function(event) {
			anbu.hide();
			event.preventDefault();
		});
		anbu.el.show.click(function(event) {
			anbu.show();
			event.preventDefault();
		});
		anbu.el.zoom.click(function(event) {
			anbu.zoom();
			event.preventDefault();
		});
		anbu.el.tab.click(function(event) {
			anbu.clicked_tab($(this));
			event.preventDefault();
		});

	},

	// CLICKED_TAB()
	// -------------------------------------------------------------
	// A tab has been clicked, decide what to do.

	clicked_tab: function(tab) {

		// if the tab is closed
		if (anbu.window_open && anbu.active_pane == tab.attr(anbu.tab_data)) {
			anbu.close_window();
		} else {
			anbu.open_window(tab);
		}

	},

	// OPEN_WINDOW()
	// -------------------------------------------------------------
	// Animate open the top window to the appropriate tab.

	open_window: function(tab) {

		// can't directly assign this line, but it works
		$('.anbu-tab-pane:visible').fadeOut(200);
		$('.' + tab.attr(anbu.tab_data)).delay(220).fadeIn(300);
		anbu.el.tab_links.removeClass(anbu.active_tab);
		tab.addClass(anbu.active_tab);
		anbu.el.window.slideDown(300);
		anbu.el.close.fadeIn(300);
		anbu.el.zoom.fadeIn(300);
		anbu.active_pane = tab.attr(anbu.tab_data);
		anbu.window_open = true;

	},

	// CLOSE_WINDOW()
	// -------------------------------------------------------------
	// Animate closed the top window hiding all tabs.

	close_window: function() {

		anbu.el.tab_pane.fadeOut(100);
		anbu.el.window.slideUp(300);
		anbu.el.close.fadeOut(300);
		anbu.el.zoom.fadeOut(300);
		anbu.el.tab_links.removeClass(anbu.active_tab);
		anbu.active_pane = '';
		anbu.window_open = false;

	},

	// SHOW()
	// -------------------------------------------------------------
	// Show the Anbu toolbar when it has been compacted.

	show: function() {

		anbu.el.closed_tabs.fadeOut(600, function () {
			anbu.el.main.removeClass('anbu-hidden');
			anbu.el.open_tabs.fadeIn(200);
		});
		anbu.el.main.animate({width: '100%'}, 700);

	},

	// HIDE()
	// -------------------------------------------------------------
	// Hide the anbu toolbar, show a tiny re-open button.

	hide: function() {

		anbu.close_window();

		setTimeout(function() {
			anbu.el.window.slideUp(400, function () {
				anbu.close_window();
				anbu.el.main.addClass('anbu-hidden');
				anbu.el.open_tabs.fadeOut(200, function () {
					anbu.el.closed_tabs.fadeIn(200);
				});
				anbu.el.main.animate({width: anbu.mini_button_width}, 700);
			});
		}, 100);

	},

	// TOGGLEZOOM()
	// -------------------------------------------------------------
	// Toggle the zoomed mode of the top window.

	zoom: function() {

		if (anbu.is_zoomed) {
			height = anbu.small_height;
			anbu.is_zoomed = false;
		} else {
			// the 6px is padding on the top of the window
			height = ($(window).height() - anbu.el.tabs.height() - 6) + 'px';
			anbu.is_zoomed = true;
		}

		anbu.el.content_area.animate({height: height}, 700);

	}

};

// launch anbu on jquery dom ready
jQuery(document).ready(function() {
	anbu.start();
});