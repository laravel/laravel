// jsreporter.js
// Script to support JsReporter class
// Relies heavily on the X library in x.js
//     X v3.14.1, Cross-Browser DHTML Library from Cross-Browser.com
// Copyright (c) 2004 Jason E. Sweat (jsweat_php@yahoo.com)
// 
// SimpleTest - http://simpletest.sf.net/
// Copyright (c) 2003,2004 Marcus Baker (marcus@lastcraft.com)
// $Id: webunit.js 506 2004-02-14 18:24:13Z jsweat $


// Variables:
min_x=500;
min_y=400;
groups = new Array();
cases = new Array();
methods = new Array();
current_group=0;
current_case=0;
current_method=0;

Hash = {
	Set : function(foo,bar) {this[foo] = bar;},
	Get : function(foo) {return this[foo];}
}

// Functions:
function wait_start() {
  var wait_x;
  var wait_y;

  wait_x = xWidth('wait');
  wait_y = xHeight('wait');
  xMoveTo('wait', (xClientWidth()-wait_x)/2, (xClientHeight()-wait_y)/2);
  xShow('wait');
}

function layout() {
	xResizeTo('webunit', max(xClientWidth()-30,min_x), max(xClientHeight()-20,min_y));
	xMoveTo('webunit', 5, 5);
	xResizeTo('tabs', xWidth('webunit')-10, xHeight('webunit')/3);
	xLeft('tabs', 5);
	xShow('webunit');
	xShow('tabs');
	activate_tab('fail');
	xShow('visible_tab');
	xZIndex('visible_tab', 2)
	xResizeTo('msg', xWidth('webunit')-17, xHeight('webunit')/3-20);
	xLeft('msg', 2);
	xTop('msg',2*xHeight('webunit')/3);
	xShow('msg');
}

function set_div_content(div, content) {
	xGetElementById(div).innerHTML = content;
}

function copy_div_content(divsrc, divtrgt) {
	xGetElementById(divtrgt).innerHTML = xGetElementById(divsrc).innerHTML;
}

function activate_tab(tab) {
	if (tab == 'fail') {
		copy_div_content('fail', 'visible_tab');
		xGetElementById('failtab').className = 'activetab';
		xZIndex('failtab', 3)
		xGetElementById('treetab').className = 'inactivetab';
		xZIndex('treetab', 1)
	}
	if (tab == 'tree') {
		copy_div_content('tree', 'visible_tab');
		xGetElementById('failtab').className = 'inactivetab';
		xZIndex('failtab', 1)
		xGetElementById('treetab').className = 'activetab';
		xZIndex('treetab', 3)
	}
}

function add_group(group_name) {
  var add;
  
  add = {
		Set : function(foo,bar) {this[foo] = bar;},
		Get : function(foo) {return this[foo];}
  }
  add.Set('desc', group_name);
  add.Set('pass', true);
  groups[groups.length] = add;
  current_group = groups.length - 1;
  cases[current_group] = new Array();
  methods[current_group] = new Array();
}

function add_case(case_name) {
  var curgroup;
  var add;
  
  add = {
		Set : function(foo,bar) {this[foo] = bar;},
		Get : function(foo) {return this[foo];}
  }
  add.Set('desc', case_name);
  add.Set('pass', true);
  curgroup = cases[current_group];
  cases[current_group][curgroup.length] = add;
  current_case = curgroup.length - 1;
  methods[current_group][current_case] = new Array();
}

function add_method(method_name) {
	var curcase;
  var add;
  
  add = {
		Set : function(foo,bar) {this[foo] = bar;},
		Get : function(foo) {return this[foo];}
  }
  add.Set('desc', method_name);
  add.Set('pass', true);
  add.Set('msg','');
	curcase = methods[current_group][current_case];
	methods[current_group][current_case][curcase.length] = add;
	current_method = curcase.length - 1;
}

function add_fail(msg) {
  var oldmsg;
  add_log(msg);
  groups[current_group].Set('pass', false);
  cases[current_group][current_case].Set('pass', false);
  methods[current_group][current_case][current_method].Set('pass', false);
  oldmsg = methods[current_group][current_case][current_method].Get('msg');
  methods[current_group][current_case][current_method].Set('msg', oldmsg+msg);
}

function add_log(msg) {
  var faildiv;
  faildiv = xGetElementById('fail');
  faildiv.innerHTML = faildiv.innerHTML + msg;
}

function set_msg(gid, cid, mid) {
	var passfail;
	var msg=methods[gid][cid][mid].Get('msg');
	if ('' == msg) {
		passfail = (methods[gid][cid][mid].Get('pass')) ? 'pass' : 'fail';
	  msg = 'No output for <span class="' + passfail + '">'
	  	+ groups[gid].Get('desc') + '-&gt;'
	  	+ cases[gid][cid].Get('desc') + '-&gt;'
	  	+ methods[gid][cid][mid].Get('desc') + '</span><br />';
	}
  xGetElementById('msg').innerHTML = msg;
}

function make_tree() {
	var content;
	var passfail;
	content = '<ul>';
	for (x in groups) {
	  passfail = (groups[x].Get('pass')) ? 'pass' : 'fail';	
		content += '<li class="'+passfail+'">'+groups[x].Get('desc')+'<ul>';
		for (y in cases[x]) {
	    passfail = (cases[x][y].Get('pass')) ? 'pass' : 'fail';	
			content += '<li class="'+passfail+'">'+cases[x][y].Get('desc')+'<ul>';
			for (z in methods[x][y]) {
	      passfail = (methods[x][y][z].Get('pass')) ? 'pass' : 'fail';	
			  content += '<li class="'+passfail+'"><a href="javascript:set_msg('+x+','+y+','+z+')">'+methods[x][y][z].Get('desc')+'</a></li>';
			}
			content += '</ul></li>';
		}
		content += '</ul></li>';
	}
	content += '</ul>';
	xGetElementById('tree').innerHTML = content;
	if (xGetElementById('treetab').className == 'activetab') { 
	  activate_tab('tree'); 
	} else {
	  activate_tab('fail'); 
	}
}

function make_output(data) { 
}

function make_fail_msg(id, msg) {
}

function max(n1, n2) {
  if (n1 > n2) {
  	return n1;
  } else {
  	return n2;
  }
}
