
/*
* Licensed to the Apache Software Foundation (ASF) under one
* or more contributor license agreements.  See the NOTICE file
* distributed with this work for additional information
* regarding copyright ownership.  The ASF licenses this file
* to you under the Apache License, Version 2.0 (the
* "License"); you may not use this file except in compliance
* with the License.  You may obtain a copy of the License at
*
*   http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing,
* software distributed under the License is distributed on an
* "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
* KIND, either express or implied.  See the License for the
* specific language governing permissions and limitations
* under the License.
*/


!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?t(exports,require("echarts")):"function"==typeof define&&define.amd?define(["exports","echarts"],t):t((e=e||self).dataTool={},e.echarts)}(this,function(e,t){"use strict";var r=Array.prototype,i=r.slice,l=r.map,o=function(){}.constructor,r=o?o.prototype:null;function a(e,t,r){if(!e)return[];if(!t)return function(e){for(var t=[],r=1;r<arguments.length;r++)t[r-1]=arguments[r];return i.apply(e,t)}(e);if(e.map&&e.map===l)return e.map(t,r);for(var o=[],n=0,a=e.length;n<a;n++)o.push(t.call(r,e[n],n,e));return o}r&&"function"==typeof r.bind&&r.call.bind(r.bind);function p(e,t){return e.getAttribute(t)}function c(e,t){for(var r=e.firstChild;r;){if(1===r.nodeType&&r.nodeName.toLowerCase()===t.toLowerCase())return r;r=r.nextSibling}return null}function d(e,t){for(var r=e.firstChild,o=[];r;)r.nodeName.toLowerCase()===t.toLowerCase()&&o.push(r),r=r.nextSibling;return o}o=Object.freeze({__proto__:null,parse:function(e){if(!(t="string"==typeof e?(new DOMParser).parseFromString(e,"text/xml"):e)||t.getElementsByTagName("parsererror").length)return null;if(!(e=c(t,"gexf")))return null;for(var f,t=c(e,"graph"),r=(e=c(t,"attributes"))?a(d(e,"attribute"),function(e){return{id:p(e,"id"),title:p(e,"title"),type:p(e,"type")}}):[],o={},n=0;n<r.length;n++)o[r[n].id]=r[n];return{nodes:(e=c(t,"nodes"),f=o,e?a(d(e,"node"),function(e){var t={id:p(e,"id"),name:p(e,"label"),itemStyle:{normal:{}}},r=c(e,"viz:size"),o=c(e,"viz:position"),n=c(e,"viz:color"),e=c(e,"attvalues");if(r&&(t.symbolSize=parseFloat(p(r,"value"))),o&&(t.x=parseFloat(p(o,"x")),t.y=parseFloat(p(o,"y"))),n&&(t.itemStyle.normal.color="rgb("+[0|p(n,"r"),0|p(n,"g"),0|p(n,"b")].join(",")+")"),e){var a=d(e,"attvalue");t.attributes={};for(var i=0;i<a.length;i++){var l=a[i],u=p(l,"for"),s=p(l,"value"),l=f[u];if(l){switch(l.type){case"integer":case"long":s=parseInt(s,10);break;case"float":case"double":s=parseFloat(s);break;case"boolean":s="true"===s.toLowerCase()}t.attributes[u]=s}}}return t}):[]),links:(t=c(t,"edges"))?a(d(t,"edge"),function(e){var t={id:p(e,"id"),name:p(e,"label"),source:p(e,"source"),target:p(e,"target"),lineStyle:{normal:{}}},r=t.lineStyle.normal,o=c(e,"viz:thickness"),e=c(e,"viz:color");return o&&(r.width=parseFloat(o.getAttribute("value"))),e&&(r.color="rgb("+[0|p(e,"r"),0|p(e,"g"),0|p(e,"b")].join(",")+")"),t}):[]}}});function y(e,t){var r=(e.length-1)*t+1,o=Math.floor(r),t=+e[o-1],r=r-o;return r?t+r*(e[o]-t):t}function n(e,t){for(var r=[],o=[],n=[],a=(t=t||{}).boundIQR,i="none"===a||0===a,l=0;l<e.length;l++){n.push(l+"");var u=((g=e[l].slice()).sort(function(e,t){return e-t}),g),s=y(u,.25),f=y(u,.5),p=y(u,.75),c=u[0],d=u[u.length-1],g=(null==a?1.5:a)*(p-s),v=i?c:Math.max(c,s-g),b=i?d:Math.min(d,p+g);r.push([v,s,f,p,b]);for(var h=0;h<u.length;h++){var m=u[h];(m<v||b<m)&&(m=[l,m],"vertical"===t.layout&&m.reverse(),o.push(m))}}return{boxData:r,outliers:o,axisData:n}}r="1.0.0";t.dataTool&&(t.dataTool.version=r,t.dataTool.gexf=o,t.dataTool.prepareBoxplotData=n),e.gexf=o,e.prepareBoxplotData=n,e.version=r,Object.defineProperty(e,"__esModule",{value:!0})});