/**
 * @summary     FixedColumns
 * @description Freeze columns in place on a scrolling DataTable
 * @file        FixedColumns.js
 * @version     2.0.3
 * @author      Allan Jardine (www.sprymedia.co.uk)
 * @license     GPL v2 or BSD 3 point style
 * @contact     www.sprymedia.co.uk/contact
 *
 * @copyright Copyright 2010-2011 Allan Jardine, all rights reserved.
 *
 * This source file is free software, under either the GPL v2 license or a
 * BSD style license, available at:
 *   http://datatables.net/license_gpl2
 *   http://datatables.net/license_bsd
 */


/* Global scope for FixedColumns */
var FixedColumns;

(function($, window, document) {


/** 
 * When making use of DataTables' x-axis scrolling feature, you may wish to 
 * fix the left most column in place. This plug-in for DataTables provides 
 * exactly this option (note for non-scrolling tables, please use the  
 * FixedHeader plug-in, which can fix headers, footers and columns). Key 
 * features include:
 *   <ul class="limit_length">
 *     <li>Freezes the left or right most columns to the side of the table</li>
 *     <li>Option to freeze two or more columns</li>
 *     <li>Full integration with DataTables' scrolling options</li>
 *     <li>Speed - FixedColumns is fast in its operation</li>
 *   </ul>
 *
 *  @class
 *  @constructor
 *  @param {object} oDT DataTables instance
 *  @param {object} [oInit={}] Configuration object for FixedColumns. Options are defined by {@link FixedColumns.defaults}
 * 
 *  @requires jQuery 1.3+
 *  @requires DataTables 1.8.0+
 * 
 *  @example
 *  	var oTable = $('#example').dataTable( {
 *  		"sScrollX": "100%"
 *  	} );
 *  	new FixedColumns( oTable );
 */
FixedColumns = function ( oDT, oInit ) {
	/* Sanity check - you just know it will happen */
	if ( ! this instanceof FixedColumns )
	{
		alert( "FixedColumns warning: FixedColumns must be initialised with the 'new' keyword." );
		return;
	}
	
	if ( typeof oInit == 'undefined' )
	{
		oInit = {};
	}
	
	/**
	 * Settings object which contains customisable information for FixedColumns instance
	 * @namespace
	 * @extends FixedColumns.defaults
	 */
	this.s = {
		/** 
		 * DataTables settings objects
		 *  @type     object
		 *  @default  Obtained from DataTables instance
		 */
		"dt": oDT.fnSettings(),
		
		/** 
		 * Number of columns in the DataTable - stored for quick access
		 *  @type     int
		 *  @default  Obtained from DataTables instance
		 */
		"iTableColumns": oDT.fnSettings().aoColumns.length,
		
		/** 
		 * Original widths of the columns as rendered by DataTables
		 *  @type     array.<int>
		 *  @default  []
		 */
		"aiWidths": [],
		
		/** 
		 * Flag to indicate if we are dealing with IE6/7 as these browsers need a little hack
		 * in the odd place
		 *  @type     boolean
		 *  @default  Automatically calculated
		 *  @readonly
		 */
		"bOldIE": ($.browser.msie && ($.browser.version == "6.0" || $.browser.version == "7.0"))
	};
	
	
	/**
	 * DOM elements used by the class instance
	 * @namespace
	 * 
	 */
	this.dom = {
		/**
		 * DataTables scrolling element
		 *  @type     node
		 *  @default  null
		 */
		"scroller": null,
		
		/**
		 * DataTables header table
		 *  @type     node
		 *  @default  null
		 */
		"header": null,
		
		/**
		 * DataTables body table
		 *  @type     node
		 *  @default  null
		 */
		"body": null,
		
		/**
		 * DataTables footer table
		 *  @type     node
		 *  @default  null
		 */
		"footer": null,

		/**
		 * Display grid elements
		 * @namespace
		 */
		"grid": {
			/**
			 * Grid wrapper. This is the container element for the 3x3 grid
			 *  @type     node
			 *  @default  null
			 */
			"wrapper": null,

			/**
			 * DataTables scrolling element. This element is the DataTables
			 * component in the display grid (making up the main table - i.e.
			 * not the fixed columns).
			 *  @type     node
			 *  @default  null
			 */
			"dt": null,

			/**
			 * Left fixed column grid components
			 * @namespace
			 */
			"left": {
				"wrapper": null,
				"head": null,
				"body": null,
				"foot": null
			},

			/**
			 * Right fixed column grid components
			 * @namespace
			 */
			"right": {
				"wrapper": null,
				"head": null,
				"body": null,
				"foot": null
			}
		},
		
		/**
		 * Cloned table nodes
		 * @namespace
		 */
		"clone": {
			/**
			 * Left column cloned table nodes
			 * @namespace
			 */
			"left": {
				/**
				 * Cloned header table
				 *  @type     node
				 *  @default  null
				 */
				"header": null,
		  	
				/**
				 * Cloned body table
				 *  @type     node
				 *  @default  null
				 */
				"body": null,
		  	
				/**
				 * Cloned footer table
				 *  @type     node
				 *  @default  null
				 */
				"footer": null
			},
			
			/**
			 * Right column cloned table nodes
			 * @namespace
			 */
			"right": {
				/**
				 * Cloned header table
				 *  @type     node
				 *  @default  null
				 */
				"header": null,
		  	
				/**
				 * Cloned body table
				 *  @type     node
				 *  @default  null
				 */
				"body": null,
		  	
				/**
				 * Cloned footer table
				 *  @type     node
				 *  @default  null
				 */
				"footer": null
			}
		}
	};

	/* Attach the instance to the DataTables instance so it can be accessed easily */
	this.s.dt.oFixedColumns = this;
	
	/* Let's do it */
	this._fnConstruct( oInit );
};



FixedColumns.prototype = {
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Public methods
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	
	/**
	 * Update the fixed columns - including headers and footers. Note that FixedColumns will
	 * automatically update the display whenever the host DataTable redraws.
	 *  @returns {void}
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	var oFC = new FixedColumns( oTable );
	 *  	
	 *  	// at some later point when the table has been manipulated....
	 *  	oFC.fnUpdate();
	 */
	"fnUpdate": function ()
	{
		this._fnDraw( true );
	},
	
	
	/**
	 * Recalculate the resizes of the 3x3 grid that FixedColumns uses for display of the table.
	 * This is useful if you update the width of the table container. Note that FixedColumns will
	 * perform this function automatically when the window.resize event is fired.
	 *  @returns {void}
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	var oFC = new FixedColumns( oTable );
	 *  	
	 *  	// Resize the table container and then have FixedColumns adjust its layout....
	 *      $('#content').width( 1200 );
	 *  	oFC.fnRedrawLayout();
	 */
	"fnRedrawLayout": function ()
	{
		this._fnGridLayout();
	},
	
	
	/**
	 * Mark a row such that it's height should be recalculated when using 'semiauto' row
	 * height matching. This function will have no effect when 'none' or 'auto' row height
	 * matching is used.
	 *  @param   {Node} nTr TR element that should have it's height recalculated
	 *  @returns {void}
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	var oFC = new FixedColumns( oTable );
	 *  	
	 *  	// manipulate the table - mark the row as needing an update then update the table
	 *  	// this allows the redraw performed by DataTables fnUpdate to recalculate the row
	 *  	// height
	 *  	oFC.fnRecalculateHeight();
	 *  	oTable.fnUpdate( $('#example tbody tr:eq(0)')[0], ["insert date", 1, 2, 3 ... ]);
	 */
	"fnRecalculateHeight": function ( nTr )
	{
		nTr._DTTC_iHeight = null;
		nTr.style.height = 'auto';
	},
	
	
	/**
	 * Set the height of a given row - provides cross browser compatibility
	 *  @param   {Node} nTarget TR element that should have it's height recalculated
	 *  @param   {int} iHeight Height in pixels to set
	 *  @returns {void}
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	var oFC = new FixedColumns( oTable );
	 *  	
	 *  	// You may want to do this after manipulating a row in the fixed column
	 *  	oFC.fnSetRowHeight( $('#example tbody tr:eq(0)')[0], 50 );
	 */
	"fnSetRowHeight": function ( nTarget, iHeight )
	{
		var jqBoxHack = $(nTarget).children(':first');
		var iBoxHack = jqBoxHack.outerHeight() - jqBoxHack.height();

		/* Can we use some kind of object detection here?! This is very nasty - damn browsers */
		if ( $.browser.mozilla || $.browser.opera )
		{
			nTarget.style.height = iHeight+"px";
		}
		else
		{
			$(nTarget).children().height( iHeight-iBoxHack );
		}
	},
	
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Private methods (they are of course public in JS, but recommended as private)
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	
	/**
	 * Initialisation for FixedColumns
	 *  @param   {Object} oInit User settings for initialisation
	 *  @returns {void}
	 *  @private
	 */
	"_fnConstruct": function ( oInit )
	{
		var i, iLen, iWidth,
			that = this;
		
		/* Sanity checking */
		if ( typeof this.s.dt.oInstance.fnVersionCheck != 'function' ||
		     this.s.dt.oInstance.fnVersionCheck( '1.8.0' ) !== true )
		{
			alert( "FixedColumns "+FixedColumns.VERSION+" required DataTables 1.8.0 or later. "+
				"Please upgrade your DataTables installation" );
			return;
		}
		
		if ( this.s.dt.oScroll.sX === "" )
		{
			this.s.dt.oInstance.oApi._fnLog( this.s.dt, 1, "FixedColumns is not needed (no "+
				"x-scrolling in DataTables enabled), so no action will be taken. Use 'FixedHeader' for "+
				"column fixing when scrolling is not enabled" );
			return;
		}
		
		/* Apply the settings from the user / defaults */
		this.s = $.extend( true, this.s, FixedColumns.defaults, oInit );

		/* Set up the DOM as we need it and cache nodes */
		this.dom.grid.dt = $(this.s.dt.nTable).parents('div.dataTables_scroll')[0];
		this.dom.scroller = $('div.dataTables_scrollBody', this.dom.grid.dt )[0];

		var iScrollWidth = $(this.dom.grid.dt).width();
		var iLeftWidth = 0;
		var iRightWidth = 0;

		$('tbody>tr:eq(0)>td', this.s.dt.nTable).each( function (i) {
			iWidth = $(this).outerWidth();
			that.s.aiWidths.push( iWidth );
			if ( i < that.s.iLeftColumns )
			{
				iLeftWidth += iWidth;
			}
			if ( that.s.iTableColumns-that.s.iRightColumns <= i )
			{
				iRightWidth += iWidth;
			}
		} );

		if ( this.s.iLeftWidth === null )
		{
			this.s.iLeftWidth = this.s.sLeftWidth == 'fixed' ?
				iLeftWidth : (iLeftWidth/iScrollWidth) * 100; 
		}
		
		if ( this.s.iRightWidth === null )
		{
			this.s.iRightWidth = this.s.sRightWidth == 'fixed' ?
				iRightWidth : (iRightWidth/iScrollWidth) * 100;
		}
		
		/* Set up the DOM that we want for the fixed column layout grid */
		this._fnGridSetup();

		/* Use the DataTables API method fnSetColumnVis to hide the columns we are going to fix */
		for ( i=0 ; i<this.s.iLeftColumns ; i++ )
		{
			this.s.dt.oInstance.fnSetColumnVis( i, false );
		}
		for ( i=this.s.iTableColumns - this.s.iRightColumns ; i<this.s.iTableColumns ; i++ )
		{
			this.s.dt.oInstance.fnSetColumnVis( i, false );
		}

		/* Event handlers */
		$(this.dom.scroller).scroll( function () {
			that.dom.grid.left.body.scrollTop = that.dom.scroller.scrollTop;
			if ( that.s.iRightColumns > 0 )
			{
				that.dom.grid.right.body.scrollTop = that.dom.scroller.scrollTop;
			}
		} );

		$(window).resize( function () {
			that._fnGridLayout.call( that );
		} );
		
		var bFirstDraw = true;
		this.s.dt.aoDrawCallback = [ {
			"fn": function () {
				that._fnDraw.call( that, bFirstDraw );
				that._fnGridHeight( that );
				bFirstDraw = false;
			},
			"sName": "FixedColumns"
		} ].concat( this.s.dt.aoDrawCallback );
		
		/* Get things right to start with - note that due to adjusting the columns, there must be
		 * another redraw of the main table. It doesn't need to be a full redraw however.
		 */
		this._fnGridLayout();
		this._fnGridHeight();
		this.s.dt.oInstance.fnDraw(false);
	},
	
	
	/**
	 * Set up the DOM for the fixed column. The way the layout works is to create a 1x3 grid
	 * for the left column, the DataTable (for which we just reuse the scrolling element DataTable
	 * puts into the DOM) and the right column. In each of he two fixed column elements there is a
	 * grouping wrapper element and then a head, body and footer wrapper. In each of these we then
	 * place the cloned header, body or footer tables. This effectively gives as 3x3 grid structure.
	 *  @returns {void}
	 *  @private
	 */
	"_fnGridSetup": function ()
	{
		var that = this;

		this.dom.body = this.s.dt.nTable;
		this.dom.header = this.s.dt.nTHead.parentNode;
		this.dom.header.parentNode.parentNode.style.position = "relative";
		
		var nSWrapper = 
			$('<div class="DTFC_ScrollWrapper" style="position:relative; clear:both;">'+
				'<div class="DTFC_LeftWrapper" style="position:absolute; top:0; left:0;">'+
					'<div class="DTFC_LeftHeadWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div>'+
					'<div class="DTFC_LeftBodyWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div>'+
					'<div class="DTFC_LeftFootWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div>'+
			  	'</div>'+
				'<div class="DTFC_RightWrapper" style="position:absolute; top:0; left:0;">'+
					'<div class="DTFC_RightHeadWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div>'+
					'<div class="DTFC_RightBodyWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div>'+
					'<div class="DTFC_RightFootWrapper" style="position:relative; top:0; left:0; overflow:hidden;"></div>'+
			  	'</div>'+
			  '</div>')[0];
		nLeft = nSWrapper.childNodes[0];
		nRight = nSWrapper.childNodes[1];

		this.dom.grid.wrapper = nSWrapper;
		this.dom.grid.left.wrapper = nLeft;
		this.dom.grid.left.head = nLeft.childNodes[0];
		this.dom.grid.left.body = nLeft.childNodes[1];

		if ( this.s.iRightColumns > 0 )
		{
			this.dom.grid.right.wrapper = nRight;
			this.dom.grid.right.head = nRight.childNodes[0];
			this.dom.grid.right.body = nRight.childNodes[1];
		}
		
		if ( this.s.dt.nTFoot )
		{
			this.dom.footer = this.s.dt.nTFoot.parentNode;
			this.dom.grid.left.foot = nLeft.childNodes[2];
			if ( this.s.iRightColumns > 0 )
			{
				this.dom.grid.right.foot = nRight.childNodes[2];
			}
		}

		nSWrapper.appendChild( nLeft );
		this.dom.grid.dt.parentNode.insertBefore( nSWrapper, this.dom.grid.dt );
		nSWrapper.appendChild( this.dom.grid.dt );

		this.dom.grid.dt.style.position = "absolute";
		this.dom.grid.dt.style.top = "0px";
		this.dom.grid.dt.style.left = this.s.iLeftWidth+"px";
		this.dom.grid.dt.style.width = ($(this.dom.grid.dt).width()-this.s.iLeftWidth-this.s.iRightWidth)+"px";
	},
	
	
	/**
	 * Style and position the grid used for the FixedColumns layout based on the instance settings.
	 * Specifically sLeftWidth ('fixed' or 'absolute'), iLeftWidth (px if fixed, % if absolute) and
	 * there 'right' counterparts.
	 *  @returns {void}
	 *  @private
	 */
	"_fnGridLayout": function ()
	{
		var oGrid = this.dom.grid;
		var iTotal = $(oGrid.wrapper).width();
		var iLeft = 0, iRight = 0, iRemainder = 0;

		if ( this.s.sLeftWidth == 'fixed' )
		{
			iLeft = this.s.iLeftWidth;
		}
		else
		{
			iLeft = ( this.s.iLeftWidth / 100 ) * iTotal;
		}

		if ( this.s.sRightWidth == 'fixed' )
		{
			iRight = this.s.iRightWidth;
		}
		else
		{
			iRight = ( this.s.iRightWidth / 100 ) * iTotal;
		}

		iRemainder = iTotal - iLeft - iRight;

		oGrid.left.wrapper.style.width = iLeft+"px";
		oGrid.dt.style.width = iRemainder+"px";
		oGrid.dt.style.left = iLeft+"px";

		if ( this.s.iRightColumns > 0 )
		{
			oGrid.right.wrapper.style.width = iRight+"px";
			oGrid.right.wrapper.style.left = (iTotal-iRight)+"px";
		}
	},
	
	
	/**
	 * Recalculate and set the height of the grid components used for positioning of the 
	 * FixedColumn display grid.
	 *  @returns {void}
	 *  @private
	 */
	"_fnGridHeight": function ()
	{
		var oGrid = this.dom.grid;
		var iHeight = $(this.dom.grid.dt).height();

		oGrid.wrapper.style.height = iHeight+"px";
		oGrid.left.body.style.height = $(this.dom.scroller).height()+"px";
		oGrid.left.wrapper.style.height = iHeight+"px";
		
		if ( this.s.iRightColumns > 0 )
		{
			oGrid.right.wrapper.style.height = iHeight+"px";
			oGrid.right.body.style.height = $(this.dom.scroller).height()+"px";
		}
	},
	
	
	/**
	 * Clone and position the fixed columns
	 *  @returns {void}
	 *  @param   {Boolean} bAll Indicate if the header and footer should be updated as well (true)
	 *  @private
	 */
	"_fnDraw": function ( bAll )
	{
		this._fnCloneLeft( bAll );
		this._fnCloneRight( bAll );

		/* Draw callback function */
		if ( this.s.fnDrawCallback !== null )
		{
			this.s.fnDrawCallback.call( this, this.dom.clone.left, this.dom.clone.right );
		}

		/* Event triggering */
		$(this).trigger( 'draw', { 
			"leftClone": this.dom.clone.left,
			"rightClone": this.dom.clone.right
		} );
	},
	
	
	/**
	 * Clone the right columns
	 *  @returns {void}
	 *  @param   {Boolean} bAll Indicate if the header and footer should be updated as well (true)
	 *  @private
	 */
	"_fnCloneRight": function ( bAll )
	{
		if ( this.s.iRightColumns <= 0 )
		{
			return;
		}
		
		var that = this,
			i, jq,
			aiColumns = [];

		for ( i=this.s.iTableColumns-this.s.iRightColumns ; i<this.s.iTableColumns ; i++ )
		{
			aiColumns.push( i );
		}

		this._fnClone( this.dom.clone.right, this.dom.grid.right, aiColumns, bAll );
	},
	
	
	/**
	 * Clone the left columns
	 *  @returns {void}
	 *  @param   {Boolean} bAll Indicate if the header and footer should be updated as well (true)
	 *  @private
	 */
	"_fnCloneLeft": function ( bAll )
	{
		if ( this.s.iLeftColumns <= 0 )
		{
			return;
		}
		
		var that = this,
			i, jq,
			aiColumns = [];
		
		for ( i=0 ; i<this.s.iLeftColumns ; i++ )
		{
			aiColumns.push( i );
		}

		this._fnClone( this.dom.clone.left, this.dom.grid.left, aiColumns, bAll );
	},
	
	
	/**
	 * Make a copy of the layout object for a header or footer element from DataTables. Note that
	 * this method will clone the nodes in the layout object.
	 *  @returns {Array} Copy of the layout array
	 *  @param   {Object} aoOriginal Layout array from DataTables (aoHeader or aoFooter)
	 *  @param   {Object} aiColumns Columns to copy
	 *  @private
	 */
	"_fnCopyLayout": function ( aoOriginal, aiColumns )
	{
		var aReturn = [];
		var aClones = [];
		var aCloned = [];

		for ( var i=0, iLen=aoOriginal.length ; i<iLen ; i++ )
		{
			var aRow = [];
			aRow.nTr = $(aoOriginal[i].nTr).clone(true)[0];

			for ( var j=0, jLen=this.s.iTableColumns ; j<jLen ; j++ )
			{
				if ( $.inArray( j, aiColumns ) === -1 )
				{
					continue;
				}

				var iCloned = $.inArray( aoOriginal[i][j].cell, aCloned );
				if ( iCloned === -1 )
				{
					var nClone = $(aoOriginal[i][j].cell).clone(true)[0];
					aClones.push( nClone );
					aCloned.push( aoOriginal[i][j].cell );

					aRow.push( {
						"cell": nClone,
						"unique": aoOriginal[i][j].unique
					} );
				}
				else
				{
					aRow.push( {
						"cell": aClones[ iCloned ],
						"unique": aoOriginal[i][j].unique
					} );
				}
			}
			
			aReturn.push( aRow );
		}

		return aReturn;
	},
	
	
	/**
	 * Clone the DataTable nodes and place them in the DOM (sized correctly)
	 *  @returns {void}
	 *  @param   {Object} oClone Object containing the header, footer and body cloned DOM elements
	 *  @param   {Object} oGrid Grid object containing the display grid elements for the cloned 
	 *                    column (left or right)
	 *  @param   {Array} aiColumns Column indexes which should be operated on from the DataTable
	 *  @param   {Boolean} bAll Indicate if the header and footer should be updated as well (true)
	 *  @private
	 */
	"_fnClone": function ( oClone, oGrid, aiColumns, bAll )
	{
		var that = this,
			i, iLen, j, jLen, jq, nTarget, iColumn, nClone, iIndex;

		/* 
		 * Header
		 */
		if ( bAll )
		{
			if ( oClone.header !== null )
			{
				oClone.header.parentNode.removeChild( oClone.header );
			}
			oClone.header = $(this.dom.header).clone(true)[0];
			oClone.header.className += " DTFC_Cloned";
			oClone.header.style.width = "100%";
			oGrid.head.appendChild( oClone.header );
			
			/* Copy the DataTables layout cache for the header for our floating column */
			var aoCloneLayout = this._fnCopyLayout( this.s.dt.aoHeader, aiColumns );
			var jqCloneThead = $('>thead', oClone.header);
			jqCloneThead.empty();

			/* Add the created cloned TR elements to the table */
			for ( i=0, iLen=aoCloneLayout.length ; i<iLen ; i++ )
			{
				jqCloneThead[0].appendChild( aoCloneLayout[i].nTr );
			}

			/* Use the handy _fnDrawHead function in DataTables to do the rowspan/colspan
			 * calculations for us
			 */
			this.s.dt.oApi._fnDrawHead( this.s.dt, aoCloneLayout, true );
		}
		else
		{
			/* To ensure that we copy cell classes exactly, regardless of colspan, multiple rows
			 * etc, we make a copy of the header from the DataTable again, but don't insert the 
			 * cloned cells, just copy the classes across. To get the matching layout for the
			 * fixed component, we use the DataTables _fnDetectHeader method, allowing 1:1 mapping
			 */
			var aoCloneLayout = this._fnCopyLayout( this.s.dt.aoHeader, aiColumns );
			var aoCurrHeader=[];

			this.s.dt.oApi._fnDetectHeader( aoCurrHeader, $('>thead', oClone.header)[0] );

			for ( i=0, iLen=aoCloneLayout.length ; i<iLen ; i++ )
			{
				for ( j=0, jLen=aoCloneLayout[i].length ; j<jLen ; j++ )
				{
					aoCurrHeader[i][j].cell.className = aoCloneLayout[i][j].cell.className;

					// If jQuery UI theming is used we need to copy those elements as well
					$('span.DataTables_sort_icon', aoCurrHeader[i][j].cell).each( function () {
						this.className = $('span.DataTables_sort_icon', aoCloneLayout[i][j].cell)[0].className;
					} );
				}
			}
		}
		this._fnEqualiseHeights( 'thead', this.dom.header, oClone.header );
		
		/* 
		 * Body
		 */
		if ( this.s.sHeightMatch == 'auto' )
		{
			/* Remove any heights which have been applied already and let the browser figure it out */
			$('>tbody>tr', that.dom.body).css('height', 'auto');
		}
		
		if ( oClone.body !== null )
		{
			oClone.body.parentNode.removeChild( oClone.body );
			oClone.body = null;
		}
		
		oClone.body = $(this.dom.body).clone(true)[0];
		oClone.body.className += " DTFC_Cloned";
		oClone.body.style.paddingBottom = this.s.dt.oScroll.iBarWidth+"px";
		oClone.body.style.marginBottom = (this.s.dt.oScroll.iBarWidth*2)+"px"; /* For IE */
		if ( oClone.body.getAttribute('id') !== null )
		{
			oClone.body.removeAttribute('id');
		}
		
		$('>thead>tr', oClone.body).empty();
		$('>tfoot', oClone.body).remove();
		
		var nBody = $('tbody', oClone.body)[0];
		$(nBody).empty();
		if ( this.s.dt.aiDisplay.length > 0 )
		{
			/* Copy the DataTables' header elements to force the column width in exactly the
			 * same way that DataTables does it - have the header element, apply the width and
			 * colapse it down
			 */
			var nInnerThead = $('>thead>tr', oClone.body)[0];
			for ( iIndex=0 ; iIndex<aiColumns.length ; iIndex++ )
			{
				iColumn = aiColumns[iIndex];

				nClone = this.s.dt.aoColumns[iColumn].nTh;
				nClone.innerHTML = "";

				oStyle = nClone.style;
				oStyle.paddingTop = "0";
				oStyle.paddingBottom = "0";
				oStyle.borderTopWidth = "0";
				oStyle.borderBottomWidth = "0";
				oStyle.height = 0;
				oStyle.width = that.s.aiWidths[iColumn]+"px";

				nInnerThead.appendChild( nClone );
			}

			/* Add in the tbody elements, cloning form the master table */
			$('>tbody>tr', that.dom.body).each( function (z) {
				var n = this.cloneNode(false);
				var i = that.s.dt.oFeatures.bServerSide===false ?
					that.s.dt.aiDisplay[ that.s.dt._iDisplayStart+z ] : z;
				for ( iIndex=0 ; iIndex<aiColumns.length ; iIndex++ )
				{
					iColumn = aiColumns[iIndex];
					if ( typeof that.s.dt.aoData[i]._anHidden[iColumn] != 'undefined' )
					{
						nClone = $(that.s.dt.aoData[i]._anHidden[iColumn]).clone(true)[0];
						n.appendChild( nClone );
					}
				}
				nBody.appendChild( n );
			} );
		}
		else
		{
			$('>tbody>tr', that.dom.body).each( function (z) {
				nClone = this.cloneNode(true);
				nClone.className += ' DTFC_NoData';
				$('td', nClone).html('');
				nBody.appendChild( nClone );
			} );
		}
		
		oClone.body.style.width = "100%";
		oGrid.body.appendChild( oClone.body );

		this._fnEqualiseHeights( 'tbody', that.dom.body, oClone.body );
		
		/*
		 * Footer
		 */
		if ( this.s.dt.nTFoot !== null )
		{
			if ( bAll )
			{
				if ( oClone.footer !== null )
				{
					oClone.footer.parentNode.removeChild( oClone.footer );
				}
				oClone.footer = $(this.dom.footer).clone(true)[0];
				oClone.footer.className += " DTFC_Cloned";
				oClone.footer.style.width = "100%";
				oGrid.foot.appendChild( oClone.footer );

				/* Copy the footer just like we do for the header */
				var aoCloneLayout = this._fnCopyLayout( this.s.dt.aoFooter, aiColumns );
				var jqCloneTfoot = $('>tfoot', oClone.footer);
				jqCloneTfoot.empty();
	
				for ( i=0, iLen=aoCloneLayout.length ; i<iLen ; i++ )
				{
					jqCloneTfoot[0].appendChild( aoCloneLayout[i].nTr );
				}
				this.s.dt.oApi._fnDrawHead( this.s.dt, aoCloneLayout, true );
			}
			else
			{
				var aoCloneLayout = this._fnCopyLayout( this.s.dt.aoFooter, aiColumns );
				var aoCurrFooter=[];

				this.s.dt.oApi._fnDetectHeader( aoCurrFooter, $('>tfoot', oClone.footer)[0] );

				for ( i=0, iLen=aoCloneLayout.length ; i<iLen ; i++ )
				{
					for ( j=0, jLen=aoCloneLayout[i].length ; j<jLen ; j++ )
					{
						aoCurrFooter[i][j].cell.className = aoCloneLayout[i][j].cell.className;
					}
				}
			}
			this._fnEqualiseHeights( 'tfoot', this.dom.footer, oClone.footer );
		}

		/* Equalise the column widths between the header footer and body - body get's priority */
		var anUnique = this.s.dt.oApi._fnGetUniqueThs( this.s.dt, $('>thead', oClone.header)[0] );
		$(anUnique).each( function (i) {
			iColumn = aiColumns[i];
			this.style.width = that.s.aiWidths[iColumn]+"px";
		} );

		if ( that.s.dt.nTFoot !== null )
		{
			anUnique = this.s.dt.oApi._fnGetUniqueThs( this.s.dt, $('>tfoot', oClone.footer)[0] );
			$(anUnique).each( function (i) {
				iColumn = aiColumns[i];
				this.style.width = that.s.aiWidths[iColumn]+"px";
			} );
		}
	},
	
	
	/**
	 * From a given table node (THEAD etc), get a list of TR direct child elements
	 *  @param   {Node} nIn Table element to search for TR elements (THEAD, TBODY or TFOOT element)
	 *  @returns {Array} List of TR elements found
	 *  @private
	 */
	"_fnGetTrNodes": function ( nIn )
	{
		var aOut = [];
		for ( var i=0, iLen=nIn.childNodes.length ; i<iLen ; i++ )
		{
			if ( nIn.childNodes[i].nodeName.toUpperCase() == "TR" )
			{
				aOut.push( nIn.childNodes[i] );
			}
		}
		return aOut;
	},

	
	/**
	 * Equalise the heights of the rows in a given table node in a cross browser way
	 *  @returns {void}
	 *  @param   {String} nodeName Node type - thead, tbody or tfoot
	 *  @param   {Node} original Original node to take the heights from
	 *  @param   {Node} clone Copy the heights to
	 *  @private
	 */
	"_fnEqualiseHeights": function ( nodeName, original, clone )
	{
		if ( this.s.sHeightMatch == 'none' && nodeName !== 'thead' && nodeName !== 'tfoot' )
		{
			return;
		}
		
		var that = this,
			i, iLen, iHeight, iHeight2, iHeightOriginal, iHeightClone,
			rootOriginal = original.getElementsByTagName(nodeName)[0],
			rootClone    = clone.getElementsByTagName(nodeName)[0],
			jqBoxHack    = $('>'+nodeName+'>tr:eq(0)', original).children(':first'),
			iBoxHack     = jqBoxHack.outerHeight() - jqBoxHack.height(),
			anOriginal   = this._fnGetTrNodes( rootOriginal ),
		 	anClone      = this._fnGetTrNodes( rootClone );
		
		for ( i=0, iLen=anClone.length ; i<iLen ; i++ )
		{
			if ( this.s.sHeightMatch == 'semiauto' && typeof anOriginal[i]._DTTC_iHeight != 'undefined' && 
				anOriginal[i]._DTTC_iHeight !== null )
			{
				/* Oddly enough, IE / Chrome seem not to copy the style height - Mozilla and Opera keep it */
				if ( $.browser.msie )
				{
					$(anClone[i]).children().height( anOriginal[i]._DTTC_iHeight-iBoxHack );
				}
				continue;
			}
			
			iHeightOriginal = anOriginal[i].offsetHeight;
			iHeightClone = anClone[i].offsetHeight;
			iHeight = iHeightClone > iHeightOriginal ? iHeightClone : iHeightOriginal;
			
			if ( this.s.sHeightMatch == 'semiauto' )
			{
				anOriginal[i]._DTTC_iHeight = iHeight;
			}
			
			/* Can we use some kind of object detection here?! This is very nasty - damn browsers */
			if ( $.browser.msie && $.browser.version < 8 )
			{
				$(anClone[i]).children().height( iHeight-iBoxHack );
				$(anOriginal[i]).children().height( iHeight-iBoxHack );	
			}
			else
			{
				anClone[i].style.height = iHeight+"px";
				anOriginal[i].style.height = iHeight+"px";
			}
		}
	}
};



/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Statics
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


/**
 * FixedColumns default settings for initialisation
 *  @namespace
 *  @static
 */
FixedColumns.defaults = {
	/** 
	 * Number of left hand columns to fix in position
	 *  @type     int
	 *  @default  1
	 *  @static
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	new FixedColumns( oTable, {
	 *  		"iLeftColumns": 2
	 *  	} );
	 */
	"iLeftColumns": 1,
	
	/** 
	 * Number of right hand columns to fix in position
	 *  @type     int
	 *  @default  0
	 *  @static
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	new FixedColumns( oTable, {
	 *  		"iRightColumns": 1
	 *  	} );
	 */
	"iRightColumns": 0,
	
	/** 
	 * Draw callback function which is called when FixedColumns has redrawn the fixed assets
	 *  @type     function(object, object):void
	 *  @default  null
	 *  @static
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	new FixedColumns( oTable, {
	 *  		"fnDrawCallback": function () {
	 *				alert( "FixedColumns redraw" );
	 *			}
	 *  	} );
	 */
	"fnDrawCallback": null,
	
	/** 
	 * Type of left column size calculation. Can take the values of "fixed", whereby the iLeftWidth
	 * value will be treated as a pixel value, or "relative" for which case iLeftWidth will be
	 * treated as a percentage value.
	 *  @type     string
	 *  @default  fixed
	 *  @static
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	new FixedColumns( oTable, {
	 *  		"sLeftWidth": "relative",
	 *  		"iLeftWidth": 10 // percentage
	 *  	} );
	 */
	"sLeftWidth": "fixed",
	
	/** 
	 * Width to set for the width of the left fixed column(s) - note that the behaviour of this
	 * property is directly effected by the sLeftWidth property. If not defined then this property
	 * is calculated automatically from what has been assigned by DataTables.
	 *  @type     int
	 *  @default  null
	 *  @static
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	new FixedColumns( oTable, {
	 *  		"iLeftWidth": 100 // pixels
	 *  	} );
	 */
	"iLeftWidth": null,
	
	/** 
	 * Type of right column size calculation. Can take the values of "fixed", whereby the 
	 * iRightWidth value will be treated as a pixel value, or "relative" for which case 
	 * iRightWidth will be treated as a percentage value.
	 *  @type     string
	 *  @default  fixed
	 *  @static
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	new FixedColumns( oTable, {
	 *  		"sRightWidth": "relative",
	 *  		"iRightWidth": 10 // percentage
	 *  	} );
	 */
	"sRightWidth": "fixed",
	
	/**
	 * Width to set for the width of the right fixed column(s) - note that the behaviour of this
	 * property is directly effected by the sRightWidth property. If not defined then this property
	 * is calculated automatically from what has been assigned by DataTables.
	 *  @type     int
	 *  @default  null
	 *  @static
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	new FixedColumns( oTable, {
	 *  		"iRightWidth": 200 // pixels
	 *  	} );
	 */
	"iRightWidth": null,
	
	/** 
	 * Height matching algorthim to use. This can be "none" which will result in no height
	 * matching being applied by FixedColumns (height matching could be forced by CSS in this
	 * case), "semiauto" whereby the height calculation will be performed once, and the result
	 * cached to be used again (fnRecalculateHeight can be used to force recalculation), or
	 * "auto" when height matching is performed on every draw (slowest but must accurate)
	 *  @type     string
	 *  @default  semiauto
	 *  @static
	 *  @example
	 *  	var oTable = $('#example').dataTable( {
	 *  		"sScrollX": "100%"
	 *  	} );
	 *  	new FixedColumns( oTable, {
	 *  		"sHeightMatch": "auto"
	 *  	} );
	 */
	"sHeightMatch": "semiauto"
};




/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Constants
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


/**
 * Name of this class
 *  @constant CLASS
 *  @type     String
 *  @default  FixedColumns
 */
FixedColumns.prototype.CLASS = "FixedColumns";


/**
 * FixedColumns version
 *  @constant  FixedColumns.VERSION
 *  @type      String
 *  @default   See code
 *  @static
 */
FixedColumns.VERSION = "2.0.3";



/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Fired events (for documentation)
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


/**
 * Event fired whenever FixedColumns redraws the fixed columns (i.e. clones the table elements from the main DataTable). This will occur whenever the DataTable that the FixedColumns instance is attached does its own draw.
 * @name FixedColumns#draw
 * @event
 * @param {event} e jQuery event object
 * @param {object} o Event parameters from FixedColumns
 * @param {object} o.leftClone Instance's object dom.clone.left for easy reference. This object contains references to the left fixed clumn column's nodes
 * @param {object} o.rightClone Instance's object dom.clone.right for easy reference. This object contains references to the right fixed clumn column's nodes
 */

})(jQuery, window, document);
