/*
 * Flot plugin to order bars side by side.
 * 
 * Released under the MIT license by Benjamin BUFFET, 20-Sep-2010.
 *
 * This plugin is an alpha version.
 *
 * To activate the plugin you must specify the parameter "order" for the specific serie :
 *
 *  $.plot($("#placeholder"), [{ data: [ ... ], bars :{ order = null or integer }])
 *
 * If 2 series have the same order param, they are ordered by the position in the array;
 *
 * The plugin adjust the point by adding a value depanding of the barwidth
 * Exemple for 3 series (barwidth : 0.1) :
 *
 *          first bar décalage : -0.15
 *          second bar décalage : -0.05
 *          third bar décalage : 0.05
 *
 */

(function(b){function c(n){var i;var t;var w;var u;var l=1;var g=false;function d(D,B,C){var z=null;if(p(B)){j(B);v(D);r(D);s(B);if(t>=2){var y=q(B);var E=0;var A=h();if(k(y)){E=-1*(m(i,y-1,Math.floor(t/2)-1))-A}else{E=m(i,Math.ceil(t/2),y-2)+A+u*2}z=e(C,B,E);C.points=z}}return z}function p(y){return y.bars!=null&&y.bars.show&&y.bars.order!=null}function v(B){var z=g?B.getPlaceholder().innerHeight():B.getPlaceholder().innerWidth();var A=g?x(B.getData(),1):x(B.getData(),0);var y=A[1]-A[0];l=y/z}function x(z,B){var A=new Array();for(var y=0;y<z.length;y++){A[0]=z[y].data[0][B];A[1]=z[y].data[z[y].data.length-1][B]}return A}function r(y){i=f(y.getData());t=i.length}function f(z){var A=new Array();for(var y=0;y<z.length;y++){if(z[y].bars.order!=null&&z[y].bars.show){A.push(z[y])}}return A.sort(o)}function o(B,A){var z=B.bars.order;var C=A.bars.order;return((z<C)?-1:((z>C)?1:0))}function s(y){w=y.bars.lineWidth?y.bars.lineWidth:2;u=w*l}function j(y){if(y.bars.horizontal){g=true}}function q(z){var A=0;for(var y=0;y<i.length;++y){if(z==i[y]){A=y;break}}return A+1}function h(){var y=0;if(t%2!=0){y=(i[Math.ceil(t/2)].bars.barWidth)/2}return y}function k(y){return y<=Math.ceil(t/2)}function m(B,C,z){var y=0;for(var A=C;A<=z;A++){y+=B[A].bars.barWidth+u*2}return y}function e(D,C,y){var E=D.pointsize;var B=D.points;var z=0;for(var A=g?1:0;A<B.length;A+=E){B[A]+=y;C.data[z][3]=B[A];z++}return B}n.hooks.processDatapoints.push(d)}var a={series:{bars:{order:null}}};b.plot.plugins.push({init:c,options:a,name:"orderBars",version:"0.2"})})(jQuery);