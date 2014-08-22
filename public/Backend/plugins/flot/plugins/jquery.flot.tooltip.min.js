/*
 * jquery.flot.tooltip
 *
 * desc:  create tooltip with values of hovered point on the graph, 
          support many series, time mode, stacking and pie charts
          you can set custom tip content (also with use of HTML tags) and precision of values
 * version: 0.4.4
 * author:  Krzysztof Urbas @krzysu [myviews.pl] with help of @ismyrnow
 * website: https://github.com/krzysu/flot.tooltip
 * 
 * released under MIT License, 2012
*/

(function(a){var b={tooltip:false,tooltipOpts:{content:"%s | X: %x | Y: %y.2",dateFormat:"%y-%0m-%0d",shifts:{x:10,y:20},defaultTheme:true}};var c=function(b){var c={x:0,y:0};var d=b.getOptions();var e=function(a){c.x=a.x;c.y=a.y};var f=function(a){var b={x:0,y:0};b.x=a.pageX;b.y=a.pageY;e(b)};var g=function(b){var c=new Date(b);return a.plot.formatDate(c,d.tooltipOpts.dateFormat)};b.hooks.bindEvents.push(function(b,e){var i=d.tooltipOpts;var j=b.getPlaceholder();var k;if(d.tooltip===false)return;if(a("#flotTip").length>0){k=a("#flotTip")}else{k=a("<div />").attr("id","flotTip");k.appendTo("body").hide().css({position:"absolute"});if(i.defaultTheme){k.css({background:"#fff","z-index":"100",padding:"0.4em 0.6em","border-radius":"0.5em","font-size":"0.8em",border:"1px solid #111"})}}a(j).bind("plothover",function(a,b,e){if(e){var f;if(d.xaxis.mode==="time"||d.xaxes[0].mode==="time"){f=h(i.content,e,g)}else{f=h(i.content,e)}k.html(f).css({left:c.x+i.shifts.x,top:c.y+i.shifts.y}).show()}else{k.hide().html("")}});e.mousemove(f)});var h=function(a,b,c){var d=/%p\.{0,1}(\d{0,})/;var e=/%s/;var f=/%x\.{0,1}(\d{0,})/;var g=/%y\.{0,1}(\d{0,})/;if(typeof b.series.percent!=="undefined"){a=i(d,a,b.series.percent)}if(typeof b.series.label!=="undefined"){a=a.replace(e,b.series.label)}if(typeof c==="function"){a=a.replace(f,c(b.series.data[b.dataIndex][0]))}else if(typeof b.series.data[b.dataIndex][0]==="number"){a=i(f,a,b.series.data[b.dataIndex][0])}if(typeof b.series.data[b.dataIndex][1]==="number"){a=i(g,a,b.series.data[b.dataIndex][1])}return a};var i=function(a,b,c){var d;if(b.match(a)!=="null"){if(RegExp.$1!==""){d=RegExp.$1;c=c.toFixed(d)}b=b.replace(a,c)}return b}};a.plot.plugins.push({init:c,options:b,name:"tooltip",version:"0.4.4"})})(jQuery)