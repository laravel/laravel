/*
 * FullCalendar v1.5.4 Stylesheet
 *
 * Copyright (c) 2011 Adam Shaw
 * Dual licensed under the MIT and GPL licenses, located in
 * MIT-LICENSE.txt and GPL-LICENSE.txt respectively.
 *
 * Date: Tue Sep 4 23:38:33 2012 -0700
 *
 */


.fc {
	direction: ltr;
	text-align: left;
	}
	
.fc table {
	border-collapse: collapse;
	border-spacing: 0;
	}
	
html .fc,
.fc table {
	font-size: 1em;
	}
	
.fc td,
.fc th {
	padding: 0;
	vertical-align: top;
	}



/* Header
------------------------------------------------------------------------*/

.fc-header td {
	white-space: nowrap;
	}

.fc-header-left {
	width: 25%;
	text-align: left;
	}
	
.fc-header-center {
	text-align: center;
	}
	
.fc-header-right {
	width: 25%;
	text-align: right;
	}
	
.fc-header-title {
	display: inline-block;
	vertical-align: top;
	}
	
.fc-header-title h2 {
	margin-top: 0;
	white-space: nowrap;
	}
	
.fc .fc-header-space {
	padding-left: 10px;
	}
	
.fc-header .fc-button {
	margin-bottom: 1em;
	vertical-align: top;
	}
	
/* buttons edges butting together */

.fc-header .fc-button {
	margin-right: -1px;
	}
	
.fc-header .fc-corner-right {
	margin-right: 1px; /* back to normal */
	}
	
.fc-header .ui-corner-right {
	margin-right: 0; /* back to normal */
	}
	
/* button layering (for border precedence) */
	
.fc-header .fc-state-hover,
.fc-header .ui-state-hover {
	z-index: 2;
	}
	
.fc-header .fc-state-down {
	z-index: 3;
	}

.fc-header .fc-state-active,
.fc-header .ui-state-active {
	z-index: 4;
	}
	
	
	
/* Content
------------------------------------------------------------------------*/
	
.fc-content {
	clear: both;
	}
	
.fc-view {
	width: 100%; /* needed for view switching (when view is absolute) */
	overflow: hidden;
	}
	
	

/* Cell Styles
------------------------------------------------------------------------*/

.fc-widget-header,    /* <th>, usually */
.fc-widget-content {  /* <td>, usually */
	border: 1px solid #ccc;
	}
	
.fc-state-highlight { /* <td> today cell */ /* TODO: add .fc-today to <th> */
	background: #ffc;
	}
	
.fc-cell-overlay { /* semi-transparent rectangle while dragging */
	background: #9cf;
	opacity: .2;
	filter: alpha(opacity=20); /* for IE */
	}
	


/* Buttons
------------------------------------------------------------------------*/

.fc-button {
	position: relative;
	display: inline-block;
	cursor: pointer;
	}
	
.fc-state-default { /* non-theme */
	border-style: solid;
	border-width: 1px 0;
	}
	
.fc-button-inner {
	position: relative;
	float: left;
	overflow: hidden;
	}
	
.fc-state-default .fc-button-inner { /* non-theme */
	border-style: solid;
	border-width: 0 1px;
	}
	
.fc-button-content {
	position: relative;
	float: left;
	height: 1.9em;
	line-height: 1.9em;
	padding: 0 .6em;
	white-space: nowrap;
	}
	
/* icon (for jquery ui) */
	
.fc-button-content .fc-icon-wrap {
	position: relative;
	float: left;
	top: 50%;
	}
	
.fc-button-content .ui-icon {
	position: relative;
	float: left;
	margin-top: -50%;
	*margin-top: 0;
	*top: -50%;
	}
	
/* gloss effect */
	
.fc-state-default .fc-button-effect {
	position: absolute;
	top: 50%;
	left: 0;
	}
	
.fc-state-default .fc-button-effect span {
	position: absolute;
	top: -100px;
	left: 0;
	width: 500px;
	height: 100px;
	border-width: 100px 0 0 1px;
	border-style: solid;
	border-color: #fff;
	background: #444;
	opacity: .09;
	filter: alpha(opacity=9);
	}
	
/* button states (determines colors)  */
	
.fc-state-default,
.fc-state-default .fc-button-inner {
	border-style: solid;
	border-color: #ccc #bbb #aaa;
	background: #F3F3F3;
	color: #000;
	}
	
.fc-state-hover,
.fc-state-hover .fc-button-inner {
	border-color: #999;
	}
	
.fc-state-down,
.fc-state-down .fc-button-inner {
	border-color: #555;
	background: #777;
	}
	
.fc-state-active,
.fc-state-active .fc-button-inner {
	border-color: #555;
	background: #777;
	color: #fff;
	}
	
.fc-state-disabled,
.fc-state-disabled .fc-button-inner {
	color: #999;
	border-color: #ddd;
	}
	
.fc-state-disabled {
	cursor: default;
	}
	
.fc-state-disabled .fc-button-effect {
	display: none;
	}
	
	

/* Global Event Styles
------------------------------------------------------------------------*/
	 
.fc-event {
	border-style: solid;
	border-width: 0;
	font-size: .85em;
	cursor: default;
	}
	
a.fc-event,
.fc-event-draggable {
	cursor: pointer;
	}
	
a.fc-event {
	text-decoration: none;
	}
	
.fc-rtl .fc-event {
	text-align: right;
	}
	
.fc-event-skin {
	border-color: #36c;     /* default BORDER color */
	background-color: #36c; /* default BACKGROUND color */
	color: #fff;            /* default TEXT color */
	}
	
.fc-event-inner {
	position: relative;
	width: 100%;
	height: 100%;
	border-style: solid;
	border-width: 0;
	overflow: hidden;
	}
	
.fc-event-time,
.fc-event-title {
	padding: 0 1px;
	}
	
.fc .ui-resizable-handle { /*** TODO: don't use ui-resizable anymore, change class ***/
	display: block;
	position: absolute;
	z-index: 99999;
	overflow: hidden; /* hacky spaces (IE6/7) */
	font-size: 300%;  /* */
	line-height: 50%; /* */
	}
	
	
	
/* Horizontal Events
------------------------------------------------------------------------*/

.fc-event-hori {
	border-width: 1px 0;
	margin-bottom: 1px;
	}
	
/* resizable */
	
.fc-event-hori .ui-resizable-e {
	top: 0           !important; /* importants override pre jquery ui 1.7 styles */
	right: -3px      !important;
	width: 7px       !important;
	height: 100%     !important;
	cursor: e-resize;
	}
	
.fc-event-hori .ui-resizable-w {
	top: 0           !important;
	left: -3px       !important;
	width: 7px       !important;
	height: 100%     !important;
	cursor: w-resize;
	}
	
.fc-event-hori .ui-resizable-handle {
	_padding-bottom: 14px; /* IE6 had 0 height */
	}
	
	
	
/* Fake Rounded Corners (for buttons and events)
------------------------------------------------------------*/
	
.fc-corner-left {
	margin-left: 1px;
	}
	
.fc-corner-left .fc-button-inner,
.fc-corner-left .fc-event-inner {
	margin-left: -1px;
	}
	
.fc-corner-right {
	margin-right: 1px;
	}
	
.fc-corner-right .fc-button-inner,
.fc-corner-right .fc-event-inner {
	margin-right: -1px;
	}
	
.fc-corner-top {
	margin-top: 1px;
	}
	
.fc-corner-top .fc-event-inner {
	margin-top: -1px;
	}
	
.fc-corner-bottom {
	margin-bottom: 1px;
	}
	
.fc-corner-bottom .fc-event-inner {
	margin-bottom: -1px;
	}
	
	
	
/* Fake Rounded Corners SPECIFICALLY FOR EVENTS
-----------------------------------------------------------------*/
	
.fc-corner-left .fc-event-inner {
	border-left-width: 1px;
	}
	
.fc-corner-right .fc-event-inner {
	border-right-width: 1px;
	}
	
.fc-corner-top .fc-event-inner {
	border-top-width: 1px;
	}
	
.fc-corner-bottom .fc-event-inner {
	border-bottom-width: 1px;
	}
	
	
	
/* Reusable Separate-border Table
------------------------------------------------------------*/

table.fc-border-separate {
	border-collapse: separate;
	}
	
.fc-border-separate th,
.fc-border-separate td {
	border-width: 1px 0 0 1px;
	}
	
.fc-border-separate th.fc-last,
.fc-border-separate td.fc-last {
	border-right-width: 1px;
	}
	
.fc-border-separate tr.fc-last th,
.fc-border-separate tr.fc-last td {
	border-bottom-width: 1px;
	}
	
.fc-border-separate tbody tr.fc-first td,
.fc-border-separate tbody tr.fc-first th {
	border-top-width: 0;
	}
	
	

/* Month View, Basic Week View, Basic Day View
------------------------------------------------------------------------*/

.fc-grid th {
	text-align: center;
	}
	
.fc-grid .fc-day-number {
	float: right;
	padding: 0 2px;
	}
	
.fc-grid .fc-other-month .fc-day-number {
	opacity: 0.3;
	filter: alpha(opacity=30); /* for IE */
	/* opacity with small font can sometimes look too faded
	   might want to set the 'color' property instead
	   making day-numbers bold also fixes the problem */
	}
	
.fc-grid .fc-day-content {
	clear: both;
	padding: 2px 2px 1px; /* distance between events and day edges */
	}
	
/* event styles */
	
.fc-grid .fc-event-time {
	font-weight: bold;
	}
	
/* right-to-left */
	
.fc-rtl .fc-grid .fc-day-number {
	float: left;
	}
	
.fc-rtl .fc-grid .fc-event-time {
	float: right;
	}
	
	

/* Agenda Week View, Agenda Day View
------------------------------------------------------------------------*/

.fc-agenda table {
	border-collapse: separate;
	}
	
.fc-agenda-days th {
	text-align: center;
	}
	
.fc-agenda .fc-agenda-axis {
	width: 50px;
	padding: 0 4px;
	vertical-align: middle;
	text-align: right;
	white-space: nowrap;
	font-weight: normal;
	}
	
.fc-agenda .fc-day-content {
	padding: 2px 2px 1px;
	}
	
/* make axis border take precedence */
	
.fc-agenda-days .fc-agenda-axis {
	border-right-width: 1px;
	}
	
.fc-agenda-days .fc-col0 {
	border-left-width: 0;
	}
	
/* all-day area */
	
.fc-agenda-allday th {
	border-width: 0 1px;
	}
	
.fc-agenda-allday .fc-day-content {
	min-height: 34px; /* TODO: doesnt work well in quirksmode */
	_height: 34px;
	}
	
/* divider (between all-day and slots) */
	
.fc-agenda-divider-inner {
	height: 2px;
	overflow: hidden;
	}
	
.fc-widget-header .fc-agenda-divider-inner {
	background: #eee;
	}
	
/* slot rows */
	
.fc-agenda-slots th {
	border-width: 1px 1px 0;
	}
	
.fc-agenda-slots td {
	border-width: 1px 0 0;
	background: none;
	}
	
.fc-agenda-slots td div {
	height: 20px;
	}
	
.fc-agenda-slots tr.fc-slot0 th,
.fc-agenda-slots tr.fc-slot0 td {
	border-top-width: 0;
	}

.fc-agenda-slots tr.fc-minor th,
.fc-agenda-slots tr.fc-minor td {
	border-top-style: dotted;
	}
	
.fc-agenda-slots tr.fc-minor th.ui-widget-header {
	*border-top-style: solid; /* doesn't work with background in IE6/7 */
	}
	


/* Vertical Events
------------------------------------------------------------------------*/

.fc-event-vert {
	border-width: 0 1px;
	}
	
.fc-event-vert .fc-event-head,
.fc-event-vert .fc-event-content {
	position: relative;
	z-index: 2;
	width: 100%;
	overflow: hidden;
	}
	
.fc-event-vert .fc-event-time {
	white-space: nowrap;
	font-size: 10px;
	}
	
.fc-event-vert .fc-event-bg { /* makes the event lighter w/ a semi-transparent overlay  */
	position: absolute;
	z-index: 1;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: #fff;
	opacity: .3;
	filter: alpha(opacity=30);
	}
	
.fc .ui-draggable-dragging .fc-event-bg, /* TODO: something nicer like .fc-opacity */
.fc-select-helper .fc-event-bg {
	display: none\9; /* for IE6/7/8. nested opacity filters while dragging don't work */
	}
	
/* resizable */
	
.fc-event-vert .ui-resizable-s {
	bottom: 0        !important; /* importants override pre jquery ui 1.7 styles */
	width: 100%      !important;
	height: 8px      !important;
	overflow: hidden !important;
	line-height: 8px !important;
	font-size: 11px  !important;
	font-family: monospace;
	text-align: center;
	cursor: s-resize;
	}
	
.fc-agenda .ui-resizable-resizing { /* TODO: better selector */
	_overflow: hidden;
	}
	
	
