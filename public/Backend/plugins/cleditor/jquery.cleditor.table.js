/**
 @preserve CLEditor Table Plugin v1.0.2
 http://premiumsoftware.net/cleditor
 requires CLEditor v1.2.2 or later
 
 Copyright 2010, Chris Landowski, Premium Software, LLC
 Dual licensed under the MIT or GPL Version 2 licenses.
*/

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// @output_file_name jquery.cleditor.table.min.js
// ==/ClosureCompiler==

(function($) {

  // Define the table button
  $.cleditor.buttons.table = {
    name: "table",
    image: "table.gif",
    title: "Insert Table",
    command: "inserthtml",
    popupName: "table",
    popupClass: "cleditorPrompt",
    popupContent:         
      "<table cellpadding=0 cellspacing=0><tr>" +
      "<td>Cols:<br><input type=text value=4 size=6></td>" +
      "<td>Rows:<br><input type=text value=4 size=6></td>" +
      "</tr></table><input type=button value=Submit>",
    buttonClick: tableButtonClick
  };

  // Add the button to the default controls
  $.cleditor.defaultOptions.controls = $.cleditor.defaultOptions.controls
    .replace("rule ", "rule table ");
        
  // Table button click event handler
  function tableButtonClick(e, data) {

    // Wire up the submit button click event handler
    $(data.popup).children(":button")
      .unbind("click")
      .bind("click", function(e) {

        // Get the editor
        var editor = data.editor;

        // Get the column and row count
        var $text = $(data.popup).find(":text"),
          cols = parseInt($text[0].value),
          rows = parseInt($text[1].value);

        // Build the html
        var html;
        if (cols > 0 && rows > 0) {
          html = "<table cellpadding=2 cellspacing=2 border=1>";
          for (y = 0; y < rows; y++) {
            html += "<tr>";
            for (x = 0; x < cols; x++)
              html += "<td>" + x + "," + y + "</td>";
            html += "</tr>";
          }
          html += "</table><br />";
        }

        // Insert the html
        if (html)
          editor.execCommand(data.command, html, null, data.button);

        // Reset the text, hide the popup and set focus
        $text.val("4");
        editor.hidePopups();
        editor.focus();

      });

    }

})(jQuery);