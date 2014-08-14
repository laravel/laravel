/*
 CLEditor Icon Plugin v1.0
 http://premiumsoftware.net/cleditor
 requires CLEditor v1.2 or later

 Copyright 2010, Chris Landowski, Premium Software, LLC
 Dual licensed under the MIT or GPL Version 2 licenses.
*/
(function(a){var d=a.cleditor.imagesPath()+"icons/",e="URL("+d+"icons.gif)";a.cleditor.buttons.icon={name:"icon",css:{backgroundImage:e,backgroundPosition:"2px 2px"},title:"Insert Icon",command:"insertimage",popupName:"Icon",popupHover:true,buttonClick:function(f,b){a(b.popup).width(60)},popupClick:function(f,b){var h=-parseInt(f.target.style.backgroundPosition)/20+1;b.value=d+h+".gif"}};for(var g=a("<div>"),c=0;c<12;c++)a("<div>").css({width:20,height:20,backgroundImage:e,backgroundPosition:c*-20}).css("float",
"left").appendTo(g);a.cleditor.buttons.icon.popupContent=g.children();a.cleditor.defaultOptions.controls=a.cleditor.defaultOptions.controls.replace("| cut","icon | cut")})(jQuery);