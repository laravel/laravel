/**
 @preserve CLEditor XHTML Plugin v1.0.0
 http://premiumsoftware.net/cleditor
 requires CLEditor v1.3.0 or later
 
 Copyright 2010, Chris Landowski, Premium Software, LLC
 Dual licensed under the MIT or GPL Version 2 licenses.

 Based on John Resig's HTML Parser Project (ejohn.org)
 http://ejohn.org/files/htmlparser.js
 Original code by Erik Arvidsson, Mozilla Public License
 http://erik.eae.net/simplehtmlparser/simplehtmlparser.js
*/

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// @output_file_name jquery.cleditor.xhtml.min.js
// ==/ClosureCompiler==

(function($) {

  // Save the previously assigned callback handler
  var oldCallback = $.cleditor.defaultOptions.updateTextArea;

  // Wireup the updateTextArea callback handler
  $.cleditor.defaultOptions.updateTextArea = function(html) {

    // Fire the previously assigned callback handler
    if (oldCallback)
      html = oldCallback(html);

    // Convert the HTML to XHTML
    return $.cleditor.convertHTMLtoXHTML(html);

  }

  // Expose the convertHTMLtoXHTML method
  $.cleditor.convertHTMLtoXHTML = function(html) {

    // Regular Expressions for parsing tags and attributes
    var startTag = /^<(\w+)((?:\s+\w+(?:\s*=\s*(?:(?:"[^"]*")|(?:'[^']*')|[^>\s]+))?)*)\s*(\/?)>/,
		  endTag = /^<\/(\w+)[^>]*>/,
		  attr = /(\w+)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|([^>\s]+)))?/g;

    // Empty Elements - HTML 4.01
    var empty = makeMap("area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed");

    // Block Elements - HTML 4.01
    var block = makeMap("address,applet,blockquote,button,center,dd,del,dir,div,dl,dt,fieldset,form,frameset,hr,iframe,ins,isindex,li,map,menu,noframes,noscript,object,ol,p,pre,script,table,tbody,td,tfoot,th,thead,tr,ul");

    // Inline Elements - HTML 4.01
    var inline = makeMap("a,abbr,acronym,applet,b,basefont,bdo,big,br,button,cite,code,del,dfn,em,font,i,iframe,img,input,ins,kbd,label,map,object,q,s,samp,script,select,small,span,strike,strong,sub,sup,textarea,tt,u,var");

    // Elements that you can, intentionally, leave open (and which close themselves)
    var closeSelf = makeMap("colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr");

    // Attributes that have their values filled in disabled="disabled"
    var fillAttrs = makeMap("checked,compact,declare,defer,disabled,ismap,multiple,nohref,noresize,noshade,nowrap,readonly,selected");

    // Special Elements (can contain anything)
    var special = makeMap("script,style");

    // Stack of open tag names
    var stack = [];
    stack.last = function () {
      return this[this.length - 1];
    };

    var index, match, last = html, results = "";

    // Cycle through all html fragments
    while (html) {

      // Make sure we're not in a script or style element
      if (!stack.last() || !special[stack.last()]) {

        // Comment
        if (html.indexOf("<!--") == 0) {
          index = html.indexOf("-->");
          if (index >= 0) {
            results += html.substring(0, index + 3);
            html = html.substring(index + 3);
          }
        }

        // End tag
        else if (html.indexOf("</") == 0) {
          match = html.match(endTag);
          if (match) {
            html = html.substring(match[0].length);
            match[0].replace(endTag, parseEndTag);
          }
        }
        
        // Start tag
        else if (html.indexOf("<") == 0) {
          match = html.match(startTag);
          if (match) {
            html = html.substring(match[0].length);
            match[0].replace(startTag, parseStartTag);
          }
        }

        // Text
        else {
          index = html.indexOf("<");
          results += (index < 0 ? html : html.substring(0, index));
          html = index < 0 ? "" : html.substring(index);
        }
      }

      // Handle script and style tags
      else {
        html = html.replace(new RegExp("(.*)<\/" + stack.last() + "[^>]*>"), function (all, text) {
          text = text.replace(/<!--(.*?)-->/g, "$1")
					  .replace(/<!\[CDATA\[(.*?)]]>/g, "$1");
          results += text;
          return "";
        });
        parseEndTag("", stack.last());
      }

      // Handle parsing error
      if (html == last)
        throw "Parse Error: " + html;
      last = html;
    }

    // Clean up any remaining tags
    parseEndTag();

    // Replace depreciated tags
    replace(/<b>(.*?)<\/b>/g, "<strong>$1</strong>");
    replace(/<i>(.*?)<\/i>/g, "<em>$1</em>");

    // Return the XHTML
    return results;

    //-----------------
    // Helper Functions
    //-----------------

    // makeMap - creates a map array object from the passed in comma delimitted string
    function makeMap(str) {
      var obj = {}, items = str.split(",");
      for (var i = 0; i < items.length; i++)
        obj[items[i]] = true;
      return obj;
    }

    // parseStartTag - handles an opening tag
    function parseStartTag(tag, tagName, rest, unary) {

      // IE generates tags in uppercase
      tagName = tagName.toLowerCase();

      // Close all inline tags before this block tag
      if (block[tagName])
        while (stack.last() && inline[stack.last()])
          parseEndTag("", stack.last());

      // Close the self closing tag prior to this one
      if (closeSelf[tagName] && stack.last() == tagName)
        parseEndTag("", tagName);

      // Push tag onto the stack
      unary = empty[tagName] || !!unary;
      if (!unary)
        stack.push(tagName);

      // Load the tags attributes
      var attrs = [];

      rest.replace(attr, function (match, name) {
        var value = arguments[2] ? arguments[2] :
				  arguments[3] ? arguments[3] :
				  arguments[4] ? arguments[4] :
				  fillAttrs[name] ? name : "";

        attrs.push({
          name: name,
          escaped: value.replace(/(^|[^\\])"/g, '$1\\\"') //"
        });

      });

      // Append the tag to the results
      results += "<" + tagName;

      for (var i = 0; i < attrs.length; i++)
        results += " " + attrs[i].name + '="' + attrs[i].escaped + '"';

      results += (unary ? "/" : "") + ">";

    }

    // parseEndTag - handles a closing tag
    function parseEndTag(tag, tagName) {

      // If no tag name is provided, clean shop
      if (!tagName)
        var pos = 0;

      // Find the closest opened tag of the same type
      else {
        tagName = tagName.toLowerCase();
        for (var pos = stack.length - 1; pos >= 0; pos--)
          if (stack[pos] == tagName)
            break;
      }

      if (pos >= 0) {
        // Close all the open elements, up the stack
        for (var i = stack.length - 1; i >= pos; i--)
          results += "</" + stack[i] + ">";

        // Remove the open elements from the stack
        stack.length = pos;
      }

    }

    // replace - replace shorthand
    function replace(regexp, newstring) {
			results = results.replace(regexp, newstring);
		}

  }

})(jQuery);