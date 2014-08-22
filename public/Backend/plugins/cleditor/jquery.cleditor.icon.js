/**
 @preserve CLEditor Icon Plugin v1.0
 http://premiumsoftware.net/cleditor
 requires CLEditor v1.2 or later
 
 Copyright 2010, Chris Landowski, Premium Software, LLC
 Dual licensed under the MIT or GPL Version 2 licenses.
*/

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// @output_file_name jquery.cleditor.icon.min.js
// ==/ClosureCompiler==

(function($) {

  // Constants
  var FOLDER = $.cleditor.imagesPath() + "icons/",
      STRIP = "icons",
      EXT = ".gif",
      URL = "URL(" + FOLDER + STRIP + EXT + ")",
      BUTTON_COUNT = 12,
      BUTTON_WIDTH = 20,
      BUTTON_HEIGHT = 20;

  // Define the icon button
  $.cleditor.buttons.icon = {
    name: "icon",
    css: {
      backgroundImage: URL,
      backgroundPosition: "2px 2px"
    },
    title: "Insert Icon",
    command: "insertimage",
    popupName: "Icon",
    popupHover: true,
    buttonClick: function(e, data) {
      $(data.popup).width(60);
    },
    popupClick: function(e, data) {
      var index = -parseInt(e.target.style.backgroundPosition) / BUTTON_WIDTH + 1;
      data.value = FOLDER + index + EXT;
    }
  };

  // Build the popup content
  var $content = $("<div>");
  for (var x = 0; x < BUTTON_COUNT; x++) {
    $("<div>")
      .css({
        width: BUTTON_WIDTH,
        height: BUTTON_HEIGHT,
        backgroundImage: URL,
        backgroundPosition: x * -BUTTON_WIDTH
      })
      .css("float", "left") // closure comiler errors when float is mapped
      .appendTo($content);
  }
  $.cleditor.buttons.icon.popupContent = $content.children();

  // Add the button to the default controls
  $.cleditor.defaultOptions.controls = $.cleditor.defaultOptions.controls
    .replace("| cut", "icon | cut");

})(jQuery);