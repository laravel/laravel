/*
 * MoonCake v1.3.1 - PickList Plugin
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

;( function( $, window, document, undefined ) {
	"use strict";
	
	// our plugin constructor
	var PickList = function( element, options ) {
		if( arguments.length ) {
			this._init( element, options );
		}
    };
	
	// our plugin counter
	var plCount = 0;
	
	// the plugin prototype
	PickList.prototype = {
		defaults: {
			// Container classes
			mainClass:                  "pickList",
			listContainerClass:         "pickList_listContainer",
			sourceListContainerClass:   "pickList_sourceListContainer",
			controlsContainerClass:     "pickList_controlsContainer",
			targetListContainerClass:   "pickList_targetListContainer",
			listClass:                  "pickList_list",
			sourceListClass:            "pickList_sourceList",
			targetListClass:            "pickList_targetList",

			// List item classes
			listItemClass:              "pickList_listItem",
			selectedListItemClass:      "pickList_selectedListItem",
			
			// Filter classes
			filterContainerClass:		"pickList_filter", 
			sourceFilterClass:			"pickList_sourceFilter", 
			targetFilterClass:			"pickList_targetFilter", 

			// Control classes
			addAllClass:                "pickList_addAll",
			addClass:                   "pickList_add",
			removeAllClass:             "pickList_removeAll",
			removeClass:                "pickList_remove",

			// Control labels
			addAllLabel:                '&gt;&gt',
			addLabel:                   '&gt;', 
			removeAllLabel:             '&lt;&lt;', 
			removeLabel:                '&lt;', 

			// List labels
			sourceListLabel:            "Available Options", 
			targetListLabel:            "Selected Options", 
			listLabelClass:             "pickList_listLabel", 
			sourceListLabelClass:       "pickList_sourceListLabel", 
			targetListLabelClass:       "pickList_targetListLabel", 
			
			enableCounters:				true, 
			enableFilters:				true, 
			sortList: 					true, 
			guidPrefix:					'pl'
		},  

		// Private Methods
		
		_init: function( element, options ) {
			this.element = $( element );
			this.options = $.extend( {}, this.defaults, options, this.element.data() );

			this._itemDictionary = {};
			this._buildPickList();
			this._refresh();
		}, 

		_buildPickList: function() {
			var self = this;
			
			if( !self.element.attr( 'multiple' ) ) {
				self.element
					.attr( 'multiple', true )
					.html( self.element.html() );
			}
			
			self._pickList = $( '<div></div>' )
					.addClass( self.options.mainClass )
					.append( self._buildList( "source" ) )
					.append( self._buildControls() )
					.append( self._buildList( "target" ) );

			self._populateLists();			
			self._pickList.insertBefore( self.element.hide() );
		}, 
		
		_buildList: function( type ) {
			var self = this;

			var container = $( "<div></div>" )
					.addClass( self.options.listContainerClass )
					.addClass( self.options[ type + "ListContainerClass"] );

			self[ type + "Label" ] = $( "<div></div>" )
					.text( self.options[ type + "ListLabel" ] )
					.addClass( self.options.listLabelClass )
					.addClass( self.options[ type + "ListLabelClass" ] )
					.append( $( '<span></span>' ).toggle( self.options.showCounters ) );

			self[ type + "List" ] = $( "<ul></ul>" )
					.addClass(self.options.listClass)
					.addClass(self.options[ type + "ListClass" ] )
					.delegate( "li", "click", { pickList: self }, self._changeHandler )
					.css({
						"-moz-user-select": "none",
						"-webkit-user-select": "none",
						"user-select": "none",
						"-ms-user-select": "none"
					})
					.each( function() {
						this.onselectstart = function() { return false; };
					});

			$( '<div></div>' )
					.append( self[ type + "Label" ] )
					.append( self[ type + "List" ] )
					.appendTo( container );
					
			var filter = 
				$( '<div></div>', { 
					"class": self.options.filterContainerClass, 
					"css": { 
						"display": 'none' 
					}
				} )
				.addClass( self.options[ type + "FilterClass" ] )
				.append( $( '<input type="text">' ) ).insertAfter( self[ type + "Label" ] );
				
			if( self.options.enableFilters ) {
				filter
					.find( 'input[type="text"]' )
						.on( 'keyup', $.proxy( self._filterHandler, self ) )
					.end()
				.show();
			}

			return container;
		}, 

		_buildControls: function() {
			var self = this;

			self.controls = $("<div></div>").addClass(self.options.controlsContainerClass);

			self.addAllButton = $('<button class="btn"></button>')
				.on( 'click', $.proxy( self._addAllHandler, self ) )
				.html(self.options.addAllLabel).addClass(self.options.addAllClass);
				
			self.addButton = $('<button class="btn"></button>')
				.on( 'click', $.proxy( self._addHandler, self ) )
				.html(self.options.addLabel).addClass(self.options.addClass);
				
			self.removeButton = $('<button class="btn"></button>')
				.on( 'click', $.proxy( self._removeHandler, self ) )
				.html(self.options.removeLabel).addClass(self.options.removeClass);
				
			self.removeAllButton = $('<button class="btn"></button>')
				.on( 'click', $.proxy( self._removeAllHandler, self ) )
				.html(self.options.removeAllLabel).addClass(self.options.removeAllClass);

			self.controls
					.append(self.addAllButton)
					.append(self.addButton)
					.append(self.removeButton)
					.append(self.removeAllButton);

			return self.controls;
		},

		_populateLists: function() {
			var self = this, 
				nothingSelected = (self.element[0].selectedIndex <= 0), 
				randomId = self._generateRandomId();

			self.element.children().each(function() {
				var el = $( this ),	
					text = el.text(), 
					guid = randomId + '_' + $(this).index(), 
					copy = $( "<li></li>" )
						.text( text )
						.attr( 'id', guid )
						.addClass( self.options.listItemClass );
						
				self._itemDictionary[ guid ] = el;
				if(nothingSelected || !el.is(':selected'))
					self.sourceList.append( copy );
				else
					self.targetList.append( copy );
			});
			
			// Sort the selection lists.
			if( self.options.sortList ) {
				self._sortItems( self.sourceList );
				self._sortItems( self.targetList );
			}
		}, 
		
		_addItem: function( items ) {
			var self = this;
			
			items.each(function(k, v) {
				self.targetList.append( self._removeSelection( $(v) ) );
				
				itemId = $(v).attr("id");
				self._itemDictionary[ itemId ].attr("selected", true);
			});
			
			// Sort the selection lists.
			if( self.options.sortList ) {
				self._sortItems( self.sourceList );
				self._sortItems( self.targetList );
			}
		},

		_removeItem: function(items) {
			var self = this;
			
			items.each(function(k, v) {
				self.sourceList.append( self._removeSelection( $(v) ) );
				
				itemId = $(v).attr("id");
				self._itemDictionary[ itemId ].removeAttr( "selected" );
			});
			
			// Sort the selection lists.
			if( self.options.sortList ) {
				self._sortItems( self.sourceList );
				self._sortItems( self.targetList );
			}
		},
		
		_filterHandler: function(e) {
			if( !this.options.enableFilters )
				return;
				
			var self = this, 
				filter = $( e.target ), 
				val = filter.val().toString().toLowerCase();
			
			filter.parent().next().children( 'li' ).show().filter( function() {
				var toMatch = $(this).text().toString().toLowerCase();
				return toMatch.indexOf( val ) == -1;
			} ).each( function( i, v ) {
				self._removeSelection( $( v ) ).hide();
			} );
			
			this._refresh();
		}, 

		_addAllHandler: function(e) {
			this._addItem( this.sourceList.children( ':visible' ) );
			this._refresh();
			
			e.preventDefault();
		},

		_addHandler: function(e) {
			this._addItem( this.sourceList.children( '.pickList_selected:visible' ) );
			this._refresh();
			
			e.preventDefault();			
		},

		_removeHandler: function(e) {
			this._removeItem( this.targetList.children( '.pickList_selected:visible' ) );
			this._refresh();
			
			e.preventDefault();
		},

		_removeAllHandler: function(e) {
			this._removeItem( this.targetList.children( ':visible' ) );
			this._refresh();
			
			e.preventDefault();
		},  
		
		_refresh: function() {
			var self = this;

			// Enable/disable the Add All button state.
			if(self.sourceList.children( 'li:visible' ).length) {
				self.addAllButton.removeAttr( "disabled" );
			} else {
				self.addAllButton.attr("disabled", true);
			}

			// Enable/disable the Remove All button state.
			if(self.targetList.children( 'li:visible' ).length) {
				self.removeAllButton.removeAttr("disabled");
			} else {
				self.removeAllButton.attr("disabled", true);
			}

			// Enable/disable the Add button state.
			if(self.sourceList.children( ".pickList_selected:visible" ).length) {
				self.addButton.removeAttr("disabled");
			} else {
				self.addButton.attr("disabled", true);
			}

			// Enable/disable the Remove button state.
			if(self.targetList.children( ".pickList_selected:visible" ).length) {
				self.removeButton.removeAttr("disabled");
			} else {
				self.removeButton.attr("disabled", true);
			}
			
			if(self.options.showCounters) {
				$.each( ['source', 'target'], function( i, type ) {
					var children = self[ type + 'List'].children('li');

					$( 'span', self[ type + "Label" ] )
						.text( self._formatString(" - showing {0} of {1}", children.filter(':visible').length, children.length ) );
				});
			}
		},

		_sortItems: function(list) {
			var listitems = list.children('li');
			
			listitems.sort(function(a, b) {
			   return $(a).text().toLowerCase().localeCompare( $(b).text().toLowerCase() );
			})
			$.each(listitems, function(idx, itm) { list.append(itm); });
		},

		_changeHandler: function(e) {
			var self = e.data.pickList;

			if(e.ctrlKey) {
				if(self._isSelected( $(this) )) {
					self._removeSelection( $(this) );
				} else {
					self.lastSelectedItem = $(this);
					self._addSelection( $(this) );
				}
			} else if(e.shiftKey) {
				var current = $(this).get(0);
				var last = self.lastSelectedItem.get(0);

				if($(this).index() < $(self.lastSelectedItem).index()) {
					var temp = current;
					current = last;
					last = temp;
				}

				var pastStart = false;
				var beforeEnd = true;

				self._clearSelections( $(this).parent() );

				$(this).parent().children( ':visible' ).each(function() {
					if($(this).get(0) == last) {
						pastStart = true;
					}

					if(pastStart && beforeEnd) {
						self._addSelection( $(this) );
					}

					if($(this).get(0) == current) {
						beforeEnd = false;
					}
				});
			} else {
				self.lastSelectedItem = $(this);
				self._clearSelections( $(this).parent() );
				self._addSelection( $(this) );
			}

			self._refresh();
		},

		_isSelected: function(listItem) {
			return listItem.hasClass("pickList_selected");
		},

		_addSelection: function(listItem) {
			var self = this;

			return listItem
					.addClass("pickList_selected")
					.addClass(self.options.selectedListItemClass);
		},

		_removeSelection: function(listItem) {
			var self = this;

			return listItem
				.removeClass("pickList_selected")
				.removeClass(self.options.selectedListItemClass);
		},

		_clearSelections: function(list) {
			var self = this;

			list.children().each(function() {
				self._removeSelection( $(this) );
			});
		},
		
		_generateRandomId: function() {
			var guid = new Date().getTime().toString(32), i;

			for (i = 0; i < 5; i++) {
				guid += Math.floor(Math.random() * 65535).toString(32);
			}

			return (this.options.guidPrefix) + guid + (plCount++).toString(32);
		}, 

		_formatString: function( str ) {
		  var args = [].splice.call(arguments, 1);
		  return str.replace(/{(\d+)}/g, function(match, number) { 
			return typeof args[number] != 'undefined'
			  ? args[number]
			  : match
			;
		  });
		}, 

		// Public Methods

		option: function( key, value ) {
			
			if ( arguments.length === 0 ) {
				// don't return a reference to the internal hash
				return $.extend( {}, this.options );
			}

			if  (typeof key === "string" ) {
				if ( value === undefined ) {
					return this.options[ key ];
				}

				this.options[ key ] = value;
			}

			return this;
		}, 

		destroy: function() {
			var self = this;

			self.pickList.remove();
			self.element.show();
		}
	}
	
	$.fn.picklist = function(options) {
		var isMethodCall = typeof options === "string",
			args = Array.prototype.slice.call( arguments, 1 ),
			returnValue = this;

		// prevent calls to internal methods
		if ( isMethodCall && options.charAt( 0 ) === "_" ) {
			return returnValue;
		}

		if ( isMethodCall ) {
			this.each(function() {
				var instance = $.data( this, 'pickList' ),
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
				var instance = $.data( this, 'pickList' );
				if ( !instance ) {
					$.data( this, 'pickList', new PickList( this, options ) );
				}
			});
		}

		return returnValue;
	};

	$(function () {
		$('[data-provide="picklist"]').each(function () {
			var $el = $(this)
			$el.picklist($el.data())
		})
	})
	
})( jQuery, window , document );
