/*
 CLEditor Table Plugin v1.0.2
 http://premiumsoftware.net/cleditor
 requires CLEditor v1.2.2 or later

 Copyright 2010, Chris Landowski, Premium Software, LLC
 Dual licensed under the MIT or GPL Version 2 licenses.
*/
(function(b){b.cleditor.buttons.table={name:"table",image:"table.gif",title:"Insert Table",command:"inserthtml",popupName:"table",popupClass:"cleditorPrompt",popupContent:"<table cellpadding=0 cellspacing=0><tr><td>Cols:<br><input type=text value=4 size=6></td><td>Rows:<br><input type=text value=4 size=6></td></tr></table><input type=button value=Submit>",buttonClick:function(h,c){b(c.popup).children(":button").unbind("click").bind("click",function(){var d=c.editor,e=b(c.popup).find(":text"),f=parseInt(e[0].value),
g=parseInt(e[1].value),a;if(f>0&&g>0){a="<table cellpadding=2 cellspacing=2 border=1>";for(y=0;y<g;y++){a+="<tr>";for(x=0;x<f;x++)a+="<td>"+x+","+y+"</td>";a+="</tr>"}a+="</table><br />"}a&&d.execCommand(c.command,a,null,c.button);e.val("4");d.hidePopups();d.focus()})}};b.cleditor.defaultOptions.controls=b.cleditor.defaultOptions.controls.replace("rule ","rule table ")})(jQuery);