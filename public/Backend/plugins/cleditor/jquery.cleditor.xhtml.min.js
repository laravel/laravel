/*
 CLEditor XHTML Plugin v1.0.0
 http://premiumsoftware.net/cleditor
 requires CLEditor v1.3.0 or later

 Copyright 2010, Chris Landowski, Premium Software, LLC
 Dual licensed under the MIT or GPL Version 2 licenses.

 Based on John Resig's HTML Parser Project (ejohn.org)
 http://ejohn.org/files/htmlparser.js
 Original code by Erik Arvidsson, Mozilla Public License
 http://erik.eae.net/simplehtmlparser/simplehtmlparser.js
*/
(function(k){var n=k.cleditor.defaultOptions.updateTextArea;k.cleditor.defaultOptions.updateTextArea=function(a){if(n)a=n(a);return k.cleditor.convertHTMLtoXHTML(a)};k.cleditor.convertHTMLtoXHTML=function(a){function i(e){var b={};e=e.split(",");for(var f=0;f<e.length;f++)b[e[f]]=true;return b}function v(e,b,f,h){b=b.toLowerCase();if(w[b])for(;c.last()&&x[c.last()];)j("",c.last());y[b]&&c.last()==b&&j("",b);(h=z[b]||!!h)||c.push(b);var l=[];f.replace(A,function(D,m,o,p,q){l.push({name:m,escaped:(o?
o:p?p:q?q:B[m]?m:"").replace(/(^|[^\\])"/g,'$1\\"')})});g+="<"+b;for(e=0;e<l.length;e++)g+=" "+l[e].name+'="'+l[e].escaped+'"';g+=(h?"/":"")+">"}function j(e,b){if(b){b=b.toLowerCase();for(f=c.length-1;f>=0;f--)if(c[f]==b)break}else var f=0;if(f>=0){for(var h=c.length-1;h>=f;h--)g+="</"+c[h]+">";c.length=f}}function r(e,b){g=g.replace(e,b)}var s=/^<(\w+)((?:\s+\w+(?:\s*=\s*(?:(?:"[^"]*")|(?:'[^']*')|[^>\s]+))?)*)\s*(\/?)>/,t=/^<\/(\w+)[^>]*>/,A=/(\w+)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|([^>\s]+)))?/g,
z=i("area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed"),w=i("address,applet,blockquote,button,center,dd,del,dir,div,dl,dt,fieldset,form,frameset,hr,iframe,ins,isindex,li,map,menu,noframes,noscript,object,ol,p,pre,script,table,tbody,td,tfoot,th,thead,tr,ul"),x=i("a,abbr,acronym,applet,b,basefont,bdo,big,br,button,cite,code,del,dfn,em,font,i,iframe,img,input,ins,kbd,label,map,object,q,s,samp,script,select,small,span,strike,strong,sub,sup,textarea,tt,u,var"),y=i("colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr"),
B=i("checked,compact,declare,defer,disabled,ismap,multiple,nohref,noresize,noshade,nowrap,readonly,selected"),C=i("script,style"),c=[];c.last=function(){return this[this.length-1]};for(var d,u=a,g="";a;){if(!c.last()||!C[c.last()])if(a.indexOf("<!--")==0){d=a.indexOf("--\>");if(d>=0){g+=a.substring(0,d+3);a=a.substring(d+3)}}else if(a.indexOf("</")==0){if(d=a.match(t)){a=a.substring(d[0].length);d[0].replace(t,j)}}else if(a.indexOf("<")==0){if(d=a.match(s)){a=a.substring(d[0].length);d[0].replace(s,
v)}}else{d=a.indexOf("<");g+=d<0?a:a.substring(0,d);a=d<0?"":a.substring(d)}else{a=a.replace(RegExp("(.*)</"+c.last()+"[^>]*>"),function(e,b){b=b.replace(/<!--(.*?)--\>/g,"$1").replace(/<!\[CDATA\[(.*?)]]\>/g,"$1");g+=b;return""});j("",c.last())}if(a==u)throw"Parse Error: "+a;u=a}j();r(/<b>(.*?)<\/b>/g,"<strong>$1</strong>");r(/<i>(.*?)<\/i>/g,"<em>$1</em>");return g}})(jQuery);