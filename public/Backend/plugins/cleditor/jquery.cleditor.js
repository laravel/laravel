/**
 @preserve CLEditor WYSIWYG HTML Editor v1.3.0
 http://premiumsoftware.net/cleditor
 requires jQuery v1.4.2 or later

 Copyright 2010, Chris Landowski, Premium Software, LLC
 Dual licensed under the MIT or GPL Version 2 licenses.
*/

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// @output_file_name jquery.cleditor.min.js
// ==/ClosureCompiler==

(function($) {

  //==============
  // jQuery Plugin
  //==============

  $.cleditor = {

    // Define the defaults used for all new cleditor instances
    defaultOptions: {
      width:        500, // width not including margins, borders or padding
      height:       250, // height not including margins, borders or padding
      controls:     // controls to add to the toolbar
                    "bold italic underline strikethrough subscript superscript | font size " +
                    "style | color highlight removeformat | bullets numbering | outdent " +
                    "indent | alignleft center alignright justify | undo redo | " +
                    "rule image link unlink | cut copy paste pastetext | print source",
      colors:       // colors in the color popup
                    "FFF FCC FC9 FF9 FFC 9F9 9FF CFF CCF FCF " +
                    "CCC F66 F96 FF6 FF3 6F9 3FF 6FF 99F F9F " +
                    "BBB F00 F90 FC6 FF0 3F3 6CC 3CF 66C C6C " +
                    "999 C00 F60 FC3 FC0 3C0 0CC 36F 63F C3C " +
                    "666 900 C60 C93 990 090 399 33F 60C 939 " +
                    "333 600 930 963 660 060 366 009 339 636 " +
                    "000 300 630 633 330 030 033 006 309 303",    
      fonts:        // font names in the font popup
                    "Arial,Arial Black,Comic Sans MS,Courier New,Narrow,Garamond," +
                    "Georgia,Impact,Sans Serif,Serif,Tahoma,Trebuchet MS,Verdana",
      sizes:        // sizes in the font size popup
                    "1,2,3,4,5,6,7",
      styles:       // styles in the style popup
                    [["Paragraph", "<p>"], ["Header 1", "<h1>"], ["Header 2", "<h2>"],
                    ["Header 3", "<h3>"],  ["Header 4","<h4>"],  ["Header 5","<h5>"],
                    ["Header 6","<h6>"]],
      useCSS:       false, // use CSS to style HTML when possible (not supported in ie)
      docType:      // Document type contained within the editor
                    '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
      docCSSFile:   // CSS file used to style the document contained within the editor
                    "", 
      bodyStyle:    // style to assign to document body contained within the editor
                    "margin:4px; font:10pt Arial,Verdana; cursor:text"
    },

    // Define all usable toolbar buttons - the init string property is 
    //   expanded during initialization back into the buttons object and 
    //   seperate object properties are created for each button.
    //   e.g. buttons.size.title = "Font Size"
    buttons: {
      // name,title,command,popupName (""=use name)
      init:
      "bold,,|" +
      "italic,,|" +
      "underline,,|" +
      "strikethrough,,|" +
      "subscript,,|" +
      "superscript,,|" +
      "font,,fontname,|" +
      "size,Font Size,fontsize,|" +
      "style,,formatblock,|" +
      "color,Font Color,forecolor,|" +
      "highlight,Text Highlight Color,hilitecolor,color|" +
      "removeformat,Remove Formatting,|" +
      "bullets,,insertunorderedlist|" +
      "numbering,,insertorderedlist|" +
      "outdent,,|" +
      "indent,,|" +
      "alignleft,Align Text Left,justifyleft|" +
      "center,,justifycenter|" +
      "alignright,Align Text Right,justifyright|" +
      "justify,,justifyfull|" +
      "undo,,|" +
      "redo,,|" +
      "rule,Insert Horizontal Rule,inserthorizontalrule|" +
      "image,Insert Image,insertimage,url|" +
      "link,Insert Hyperlink,createlink,url|" +
      "unlink,Remove Hyperlink,|" +
      "cut,,|" +
      "copy,,|" +
      "paste,,|" +
      "pastetext,Paste as Text,inserthtml,|" +
      "print,,|" +
      "source,Show Source"
    },

    // imagesPath - returns the path to the images folder
    imagesPath: function() { return imagesPath(); }

  };

  // cleditor - creates a new editor for each of the matched textareas
  $.fn.cleditor = function(options) {

    // Create a new jQuery object to hold the results
    var $result = $([]);

    // Loop through all matching textareas and create the editors
    this.each(function(idx, elem) {
      if (elem.tagName == "TEXTAREA") {
        var data = $.data(elem, CLEDITOR);
        if (!data) data = new cleditor(elem, options);
        $result = $result.add(data);
      }
    });

    // return the new jQuery object
    return $result;

  };
    
  //==================
  // Private Variables
  //==================

  var

  // Misc constants
  BACKGROUND_COLOR = "backgroundColor",
  BUTTON           = "button",
  BUTTON_NAME      = "buttonName",
  CHANGE           = "change",
  CLEDITOR         = "cleditor",
  CLICK            = "click",
  DISABLED         = "disabled",
  DIV_TAG          = "<div>",
  TRANSPARENT      = "transparent",
  UNSELECTABLE     = "unselectable",

  // Class name constants
  MAIN_CLASS       = "cleditorMain",    // main containing div
  TOOLBAR_CLASS    = "cleditorToolbar", // toolbar div inside main div
  GROUP_CLASS      = "cleditorGroup",   // group divs inside the toolbar div
  BUTTON_CLASS     = "cleditorButton",  // button divs inside group div
  DISABLED_CLASS   = "cleditorDisabled",// disabled button divs
  DIVIDER_CLASS    = "cleditorDivider", // divider divs inside group div
  POPUP_CLASS      = "cleditorPopup",   // popup divs inside body
  LIST_CLASS       = "cleditorList",    // list popup divs inside body
  COLOR_CLASS      = "cleditorColor",   // color popup div inside body
  PROMPT_CLASS     = "cleditorPrompt",  // prompt popup divs inside body
  MSG_CLASS        = "cleditorMsg",     // message popup div inside body

  // Test for ie
  ie = $.browser.msie,
  ie6 = /msie\s6/i.test(navigator.userAgent),

  // Test for iPhone/iTouch/iPad
  iOS = /iphone|ipad|ipod/i.test(navigator.userAgent),

  // Popups are created once as needed and shared by all editor instances
  popups = {},

  // Used to prevent the document click event from being bound more than once
  documentClickAssigned,

  // Local copy of the buttons object
  buttons = $.cleditor.buttons;

  //===============
  // Initialization
  //===============

  // Expand the buttons.init string back into the buttons object
  //   and create seperate object properties for each button.
  //   e.g. buttons.size.title = "Font Size"
  $.each(buttons.init.split("|"), function(idx, button) {
    var items = button.split(","), name = items[0];
    buttons[name] = {
      stripIndex: idx,
      name: name,
      title: items[1] === "" ? name.charAt(0).toUpperCase() + name.substr(1) : items[1],
      command: items[2] === "" ? name : items[2],
      popupName: items[3] === "" ? name : items[3]
    };
  });
  delete buttons.init;

  //============
  // Constructor
  //============

  // cleditor - creates a new editor for the passed in textarea element
  cleditor = function(area, options) {

    var editor = this;

    // Get the defaults and override with options
    editor.options = options = $.extend({}, $.cleditor.defaultOptions, options);

    // Hide the textarea and associate it with this editor
    var $area = editor.$area = $(area)
      .hide()
      .data(CLEDITOR, editor)
      .blur(function() {
        // Update the iframe when the textarea loses focus
        updateFrame(editor, true);
      });

    // Create the main container and append the textarea
    var $main = editor.$main = $(DIV_TAG)
      .addClass(MAIN_CLASS)
      .width(options.width)
      .height(options.height);

    // Create the toolbar
    var $toolbar = editor.$toolbar = $(DIV_TAG)
      .addClass(TOOLBAR_CLASS)
      .appendTo($main);

    // Add the first group to the toolbar
    var $group = $(DIV_TAG)
      .addClass(GROUP_CLASS)
      .appendTo($toolbar);
    
    // Add the buttons to the toolbar
    $.each(options.controls.split(" "), function(idx, buttonName) {
      if (buttonName === "") return true;

      // Divider
      if (buttonName == "|") {

        // Add a new divider to the group
        var $div = $(DIV_TAG)
          .addClass(DIVIDER_CLASS)
          .appendTo($group);

        // Create a new group
        $group = $(DIV_TAG)
          .addClass(GROUP_CLASS)
          .appendTo($toolbar);

      }

      // Button
      else {
        
        // Get the button definition
        var button = buttons[buttonName];

        // Add a new button to the group
        var $buttonDiv = $(DIV_TAG)
          .data(BUTTON_NAME, button.name)
          .addClass(BUTTON_CLASS)
          .attr("title", button.title)
          .bind(CLICK, $.proxy(buttonClick, editor))
          .appendTo($group)
          .hover(hoverEnter, hoverLeave);

        // Prepare the button image
        var map = {};
        if (button.css) map = button.css;
        else if (button.image) map.backgroundImage = imageUrl(button.image);
        if (button.stripIndex) map.backgroundPosition = button.stripIndex * -24;
        $buttonDiv.css(map);

        // Add the unselectable attribute for ie
        if (ie)
          $buttonDiv.attr(UNSELECTABLE, "on");

        // Create the popup
        if (button.popupName)
          createPopup(button.popupName, options, button.popupClass,
            button.popupContent, button.popupHover);
        
      }

    });

    // Add the main div to the DOM and append the textarea
    $main.insertBefore($area)
      .append($area);

    // Bind the document click event handler
    if (!documentClickAssigned) {
      $(document).click(function(e) {
        // Dismiss all non-prompt popups
        var $target = $(e.target);
        if (!$target.add($target.parents()).is("." + PROMPT_CLASS))
          hidePopups();
      });
      documentClickAssigned = true;
    }

    // Bind the window resize event when the width or height is auto or %
    if (/auto|%/.test("" + options.width + options.height))
      $(window).resize(function() {refresh(editor);});

    // Create the iframe and resize the controls
    refresh(editor);

  };

  //===============
  // Public Methods
  //===============

  var fn = cleditor.prototype,

  // Expose the following private functions as methods on the cleditor object.
  // The closure compiler will rename the private functions. However, the
  // exposed method names on the cleditor object will remain fixed.
  methods = [
    ["clear", clear],
    ["disable", disable],
    ["execCommand", execCommand],
    ["focus", focus],
    ["hidePopups", hidePopups],
    ["sourceMode", sourceMode, true],
    ["refresh", refresh],
    ["select", select],
    ["selectedHTML", selectedHTML, true],
    ["selectedText", selectedText, true],
    ["showMessage", showMessage],
    ["updateFrame", updateFrame],
    ["updateTextArea", updateTextArea]
  ];

  $.each(methods, function(idx, method) {
    fn[method[0]] = function() {
      var editor = this, args = [editor];
      // using each here would cast booleans into objects!
      for(var x = 0; x < arguments.length; x++) {args.push(arguments[x]);}
      var result = method[1].apply(editor, args);
      if (method[2]) return result;
      return editor;
    };
  });

  // change - shortcut for .bind("change", handler) or .trigger("change")
  fn.change = function(handler) {
    var $this = $(this);
    return handler ? $this.bind(CHANGE, handler) : $this.trigger(CHANGE);
  };

  //===============
  // Event Handlers
  //===============

  // buttonClick - click event handler for toolbar buttons
  function buttonClick(e) {

    var editor = this,
        buttonDiv = e.target,
        buttonName = $.data(buttonDiv, BUTTON_NAME),
        button = buttons[buttonName],
        popupName = button.popupName,
        popup = popups[popupName];

    // Check if disabled
    if (editor.disabled || $(buttonDiv).attr(DISABLED) == DISABLED)
      return;

    // Fire the buttonClick event
    var data = {
      editor: editor,
      button: buttonDiv,
      buttonName: buttonName,
      popup: popup,
      popupName: popupName,
      command: button.command,
      useCSS: editor.options.useCSS
    };

    if (button.buttonClick && button.buttonClick(e, data) === false)
      return false;

    // Toggle source
    if (buttonName == "source") {

      // Show the iframe
      if (sourceMode(editor)) {
        delete editor.range;
        editor.$area.hide();
        editor.$frame.show();
        buttonDiv.title = button.title;
      }

      // Show the textarea
      else {
        editor.$frame.hide();
        editor.$area.show();
        buttonDiv.title = "Show Rich Text";
      }

      // Enable or disable the toolbar buttons
      // IE requires the timeout
      setTimeout(function() {refreshButtons(editor);}, 100);

    }

    // Check for rich text mode
    else if (!sourceMode(editor)) {

      // Handle popups
      if (popupName) {
        var $popup = $(popup);

        // URL
        if (popupName == "url") {

          // Check for selection before showing the link url popup
          if (buttonName == "link" && selectedText(editor) === "") {
            showMessage(editor, "A selection is required when inserting a link.", buttonDiv);
            return false;
          }

          // Wire up the submit button click event handler
          $popup.children(":button")
            .unbind(CLICK)
            .bind(CLICK, function() {

              // Insert the image or link if a url was entered
              var $text = $popup.find(":text"),
                url = $.trim($text.val());
              if (url !== "")
                execCommand(editor, data.command, url, null, data.button);

              // Reset the text, hide the popup and set focus
              $text.val("http://");
              hidePopups();
              focus(editor);

            });

        }

        // Paste as Text
        else if (popupName == "pastetext") {

          // Wire up the submit button click event handler
          $popup.children(":button")
            .unbind(CLICK)
            .bind(CLICK, function() {

              // Insert the unformatted text replacing new lines with break tags
              var $textarea = $popup.find("textarea"),
                text = $textarea.val().replace(/\n/g, "<br />");
              if (text !== "")
                execCommand(editor, data.command, text, null, data.button);

              // Reset the text, hide the popup and set focus
              $textarea.val("");
              hidePopups();
              focus(editor);

            });

        }

        // Show the popup if not already showing for this button
        if (buttonDiv !== $.data(popup, BUTTON)) {
          showPopup(editor, popup, buttonDiv);
          return false; // stop propagination to document click
        }

        // propaginate to documnt click
        return;

      }

      // Print
      else if (buttonName == "print")
        editor.$frame[0].contentWindow.print();

      // All other buttons
      else if (!execCommand(editor, data.command, data.value, data.useCSS, buttonDiv))
        return false;

    }

    // Focus the editor
    focus(editor);

  }

  // hoverEnter - mouseenter event handler for buttons and popup items
  function hoverEnter(e) {
    var $div = $(e.target).closest("div");
    $div.css(BACKGROUND_COLOR, $div.data(BUTTON_NAME) ? "#FFF" : "#FFC");
  }

  // hoverLeave - mouseleave event handler for buttons and popup items
  function hoverLeave(e) {
    $(e.target).closest("div").css(BACKGROUND_COLOR, "transparent");
  }

  // popupClick - click event handler for popup items
  function popupClick(e) {

    var editor = this,
        popup = e.data.popup,
        target = e.target;

    // Check for message and prompt popups
    if (popup === popups.msg || $(popup).hasClass(PROMPT_CLASS))
      return;

    // Get the button info
    var buttonDiv = $.data(popup, BUTTON),
        buttonName = $.data(buttonDiv, BUTTON_NAME),
        button = buttons[buttonName],
        command = button.command,
        value,
        useCSS = editor.options.useCSS;

    // Get the command value
    if (buttonName == "font")
      // Opera returns the fontfamily wrapped in quotes
      value = target.style.fontFamily.replace(/"/g, "");
    else if (buttonName == "size") {
      if (target.tagName == "DIV")
        target = target.children[0];
      value = target.innerHTML;
    }
    else if (buttonName == "style")
      value = "<" + target.tagName + ">";
    else if (buttonName == "color")
      value = hex(target.style.backgroundColor);
    else if (buttonName == "highlight") {
      value = hex(target.style.backgroundColor);
      if (ie) command = 'backcolor';
      else useCSS = true;
    }

    // Fire the popupClick event
    var data = {
      editor: editor,
      button: buttonDiv,
      buttonName: buttonName,
      popup: popup,
      popupName: button.popupName,
      command: command,
      value: value,
      useCSS: useCSS
    };

    if (button.popupClick && button.popupClick(e, data) === false)
      return;

    // Execute the command
    if (data.command && !execCommand(editor, data.command, data.value, data.useCSS, buttonDiv))
      return false;

    // Hide the popup and focus the editor
    hidePopups();
    focus(editor);

  }

  //==================
  // Private Functions
  //==================

  // checksum - returns a checksum using the Adler-32 method
  function checksum(text)
  {
    var a = 1, b = 0;
    for (var index = 0; index < text.length; ++index) {
      a = (a + text.charCodeAt(index)) % 65521;
      b = (b + a) % 65521;
    }
    return (b << 16) | a;
  }

  // clear - clears the contents of the editor
  function clear(editor) {
    editor.$area.val("");
    updateFrame(editor);
  }

  // createPopup - creates a popup and adds it to the body
  function createPopup(popupName, options, popupTypeClass, popupContent, popupHover) {

    // Check if popup already exists
    if (popups[popupName])
      return popups[popupName];

    // Create the popup
    var $popup = $(DIV_TAG)
      .hide()
      .addClass(POPUP_CLASS)
      .appendTo("body");

    // Add the content

    // Custom popup
    if (popupContent)
      $popup.html(popupContent);

    // Color
    else if (popupName == "color") {
      var colors = options.colors.split(" ");
      if (colors.length < 10)
        $popup.width("auto");
      $.each(colors, function(idx, color) {
        $(DIV_TAG).appendTo($popup)
          .css(BACKGROUND_COLOR, "#" + color);
      });
      popupTypeClass = COLOR_CLASS;
    }

    // Font
    else if (popupName == "font")
      $.each(options.fonts.split(","), function(idx, font) {
        $(DIV_TAG).appendTo($popup)
          .css("fontFamily", font)
          .html(font);
      });

    // Size
    else if (popupName == "size")
      $.each(options.sizes.split(","), function(idx, size) {
        $(DIV_TAG).appendTo($popup)
          .html("<font size=" + size + ">" + size + "</font>");
      });

    // Style
    else if (popupName == "style")
      $.each(options.styles, function(idx, style) {
        $(DIV_TAG).appendTo($popup)
          .html(style[1] + style[0] + style[1].replace("<", "</"));
      });

    // URL
    else if (popupName == "url") {
      $popup.html('Enter URL:<br><input type=text value="http://" size=35><br><input type=button value="Submit">');
      popupTypeClass = PROMPT_CLASS;
    }

    // Paste as Text
    else if (popupName == "pastetext") {
      $popup.html('Paste your content here and click submit.<br /><textarea cols=40 rows=3></textarea><br /><input type=button value=Submit>');
      popupTypeClass = PROMPT_CLASS;
    }

    // Add the popup type class name
    if (!popupTypeClass && !popupContent)
      popupTypeClass = LIST_CLASS;
    $popup.addClass(popupTypeClass);

    // Add the unselectable attribute to all items
    if (ie) {
      $popup.attr(UNSELECTABLE, "on")
        .find("div,font,p,h1,h2,h3,h4,h5,h6")
        .attr(UNSELECTABLE, "on");
    }

    // Add the hover effect to all items
    if ($popup.hasClass(LIST_CLASS) || popupHover === true)
      $popup.children().hover(hoverEnter, hoverLeave);

    // Add the popup to the array and return it
    popups[popupName] = $popup[0];
    return $popup[0];

  }

  // disable - enables or disables the editor
  function disable(editor, disabled) {

    // Update the textarea and save the state
    if (disabled) {
      editor.$area.attr(DISABLED, DISABLED);
      editor.disabled = true;
    }
    else {
      editor.$area.removeAttr(DISABLED);
      delete editor.disabled;
    }

    // Switch the iframe into design mode.
    // ie6 does not support designMode.
    // ie7 & ie8 do not properly support designMode="off".
    try {
      if (ie) editor.doc.body.contentEditable = !disabled;
      else editor.doc.designMode = !disabled ? "on" : "off";
    }
    // Firefox 1.5 throws an exception that can be ignored
    // when toggling designMode from off to on.
    catch (err) {}

    // Enable or disable the toolbar buttons
    refreshButtons(editor);

  }

  // execCommand - executes a designMode command
  function execCommand(editor, command, value, useCSS, button) {

    // Restore the current ie selection
    restoreRange(editor);

    // Set the styling method
    if (!ie) {
      if (useCSS === undefined || useCSS === null)
        useCSS = editor.options.useCSS;
      editor.doc.execCommand("styleWithCSS", 0, useCSS.toString());
    }

    // Execute the command and check for error
    var success = true, description;
    if (ie && command.toLowerCase() == "inserthtml")
      getRange(editor).pasteHTML(value);
    else {
      try { success = editor.doc.execCommand(command, 0, value || null); }
      catch (err) { description = err.description; success = false; }
      if (!success) {
        if ("cutcopypaste".indexOf(command) > -1)
          showMessage(editor, "For security reasons, your browser does not support the " +
            command + " command. Try using the keyboard shortcut or context menu instead.",
            button);
        else
          showMessage(editor,
            (description ? description : "Error executing the " + command + " command."),
            button);
      }
    }

    // Enable the buttons
    refreshButtons(editor);
    return success;

  }

  // focus - sets focus to either the textarea or iframe
  function focus(editor) {
    setTimeout(function() {
      if (sourceMode(editor)) editor.$area.focus();
      else editor.$frame[0].contentWindow.focus();
      refreshButtons(editor);
    }, 0);
  }

  // getRange - gets the current text range object
  function getRange(editor) {
    if (ie) return getSelection(editor).createRange();
    return getSelection(editor).getRangeAt(0);
  }

  // getSelection - gets the current text range object
  function getSelection(editor) {
    if (ie) return editor.doc.selection;
    return editor.$frame[0].contentWindow.getSelection();
  }

  // Returns the hex value for the passed in string.
  //   hex("rgb(255, 0, 0)"); // #FF0000
  //   hex("#FF0000"); // #FF0000
  //   hex("#F00"); // #FF0000
  function hex(s) {
    var m = /rgba?\((\d+), (\d+), (\d+)/.exec(s),
      c = s.split("");
    if (m) {
      s = ( m[1] << 16 | m[2] << 8 | m[3] ).toString(16);
      while (s.length < 6)
        s = "0" + s;
    }
    return "#" + (s.length == 6 ? s : c[1] + c[1] + c[2] + c[2] + c[3] + c[3]);
  }

  // hidePopups - hides all popups
  function hidePopups() {
    $.each(popups, function(idx, popup) {
      $(popup)
        .hide()
        .unbind(CLICK)
        .removeData(BUTTON);
    });
  }

  // imagesPath - returns the path to the images folder
  function imagesPath() {
    var cssFile = "jquery.cleditor.css",
        href = $("link[href$='" + cssFile +"']").attr("href");
    return href.substr(0, href.length - cssFile.length) + "images/";
  }

  // imageUrl - Returns the css url string for a filemane
  function imageUrl(filename) {
    return "url(" + imagesPath() + filename + ")";
  }

  // refresh - creates the iframe and resizes the controls
  function refresh(editor) {

    var $main = editor.$main,
      options = editor.options;

    // Remove the old iframe
    if (editor.$frame) 
      editor.$frame.remove();

    // Create a new iframe
    var $frame = editor.$frame = $('<iframe frameborder="0" src="javascript:true;">')
      .hide()
      .appendTo($main);

    // Load the iframe document content
    var contentWindow = $frame[0].contentWindow,
      doc = editor.doc = contentWindow.document,
      $doc = $(doc);

    doc.open();
    doc.write(
      options.docType +
      '<html>' +
      ((options.docCSSFile === '') ? '' : '<head><link rel="stylesheet" type="text/css" href="' + options.docCSSFile + '" /></head>') +
      '<body style="' + options.bodyStyle + '"></body></html>'
    );
    doc.close();

    // Work around for bug in IE which causes the editor to lose
    // focus when clicking below the end of the document.
    if (ie)
      $doc.click(function() {focus(editor);});

    // Load the content
    updateFrame(editor);

    // Bind the ie specific iframe event handlers
    if (ie) {

      // Save the current user selection. This code is needed since IE will
      // reset the selection just after the beforedeactivate event and just
      // before the beforeactivate event.
      $doc.bind("beforedeactivate beforeactivate selectionchange keypress", function(e) {
        
        // Flag the editor as inactive
        if (e.type == "beforedeactivate")
          editor.inactive = true;
        
        // Get rid of the bogus selection and flag the editor as active
        else if (e.type == "beforeactivate") {
          if (!editor.inactive && editor.range && editor.range.length > 1)
            editor.range.shift();
          delete editor.inactive;
        }

        // Save the selection when the editor is active
        else if (!editor.inactive) {
          if (!editor.range) 
            editor.range = [];
          editor.range.unshift(getRange(editor));

          // We only need the last 2 selections
          while (editor.range.length > 2)
            editor.range.pop();
        }

      });

      // Restore the text range when the iframe gains focus
      $frame.focus(function() {
        restoreRange(editor);
      });

    }

    // Update the textarea when the iframe loses focus
    ($.browser.mozilla ? $doc : $(contentWindow)).blur(function() {
      updateTextArea(editor, true);
    });

    // Enable the toolbar buttons as the user types or clicks
    $doc.click(hidePopups)
      .bind("keyup mouseup", function() {
        refreshButtons(editor);
      });

    // Show the textarea for iPhone/iTouch/iPad or
    // the iframe when design mode is supported.
    if (iOS) editor.$area.show();
    else $frame.show();

    // Wait for the layout to finish - shortcut for $(document).ready()
    $(function() {

      var $toolbar = editor.$toolbar,
          $group = $toolbar.children("div:last"),
          wid = $main.width();

      // Resize the toolbar
      var hgt = $group.offset().top + $group.outerHeight() - $toolbar.offset().top + 1;
      $toolbar.height(hgt);

      // Resize the iframe
      hgt = (/%/.test("" + options.height) ? $main.height() : parseInt(options.height)) - hgt;
      $frame.width(wid).height(hgt);

      // Resize the textarea. IE6 textareas have a 1px top
      // & bottom margin that cannot be removed using css.
      editor.$area.width(wid).height(ie6 ? hgt - 2 : hgt);

      // Switch the iframe into design mode if enabled
      disable(editor, editor.disabled);

      // Enable or disable the toolbar buttons
      refreshButtons(editor);

    });

  }

  // refreshButtons - enables or disables buttons based on availability
  function refreshButtons(editor) {

    // Webkit requires focus before queryCommandEnabled will return anything but false
    if (!iOS && $.browser.webkit && !editor.focused) {
      editor.$frame[0].contentWindow.focus();
      window.focus();
      editor.focused = true;
    }

    // Get the object used for checking queryCommandEnabled
    var queryObj = editor.doc;
    if (ie) queryObj = getRange(editor);

    // Loop through each button
    var inSourceMode = sourceMode(editor);
    $.each(editor.$toolbar.find("." + BUTTON_CLASS), function(idx, elem) {

      var $elem = $(elem),
        button = $.cleditor.buttons[$.data(elem, BUTTON_NAME)],
        command = button.command,
        enabled = true;

      // Determine the state
      if (editor.disabled)
        enabled = false;
      else if (button.getEnabled) {
        var data = {
          editor: editor,
          button: elem,
          buttonName: button.name,
          popup: popups[button.popupName],
          popupName: button.popupName,
          command: button.command,
          useCSS: editor.options.useCSS
        };
        enabled = button.getEnabled(data);
        if (enabled === undefined)
          enabled = true;
      }
      else if (((inSourceMode || iOS) && button.name != "source") ||
      (ie && (command == "undo" || command == "redo")))
        enabled = false;
      else if (command && command != "print") {
        if (ie && command == "hilitecolor")
          command = "backcolor";
        // IE does not support inserthtml, so it's always enabled
        if (!ie || command != "inserthtml") {
          try {enabled = queryObj.queryCommandEnabled(command);}
          catch (err) {enabled = false;}
        }
      }

      // Enable or disable the button
      if (enabled) {
        $elem.removeClass(DISABLED_CLASS);
        $elem.removeAttr(DISABLED);
      }
      else {
        $elem.addClass(DISABLED_CLASS);
        $elem.attr(DISABLED, DISABLED);
      }

    });
  }

  // restoreRange - restores the current ie selection
  function restoreRange(editor) {
    if (ie && editor.range)
      editor.range[0].select();
  }

  // select - selects all the text in either the textarea or iframe
  function select(editor) {
    setTimeout(function() {
      if (sourceMode(editor)) editor.$area.select();
      else execCommand(editor, "selectall");
    }, 0);
  }

  // selectedHTML - returns the current HTML selection or and empty string
  function selectedHTML(editor) {
    restoreRange(editor);
    var range = getRange(editor);
    if (ie)
      return range.htmlText;
    var layer = $("<layer>")[0];
    layer.appendChild(range.cloneContents());
    var html = layer.innerHTML;
    layer = null;
    return html;
  }

  // selectedText - returns the current text selection or and empty string
  function selectedText(editor) {
    restoreRange(editor);
    if (ie) return getRange(editor).text;
    return getSelection(editor).toString();
  }

  // showMessage - alert replacement
  function showMessage(editor, message, button) {
    var popup = createPopup("msg", editor.options, MSG_CLASS);
    popup.innerHTML = message;
    showPopup(editor, popup, button);
  }

  // showPopup - shows a popup
  function showPopup(editor, popup, button) {

    var offset, left, top, $popup = $(popup);

    // Determine the popup location
    if (button) {
      var $button = $(button);
      offset = $button.offset();
      left = --offset.left;
      top = offset.top + $button.height();
    }
    else {
      var $toolbar = editor.$toolbar;
      offset = $toolbar.offset();
      left = Math.floor(($toolbar.width() - $popup.width()) / 2) + offset.left;
      top = offset.top + $toolbar.height() - 2;
    }

    // Position and show the popup
    hidePopups();
    $popup.css({left: left, top: top})
      .show();

    // Assign the popup button and click event handler
    if (button) {
      $.data(popup, BUTTON, button);
      $popup.bind(CLICK, {popup: popup}, $.proxy(popupClick, editor));
    }

    // Focus the first input element if any
    setTimeout(function() {
      $popup.find(":text,textarea").eq(0).focus().select();
    }, 100);

  }

  // sourceMode - returns true if the textarea is showing
  function sourceMode(editor) {
    return editor.$area.is(":visible");
  }

  // updateFrame - updates the iframe with the textarea contents
  function updateFrame(editor, checkForChange) {
    
    var code = editor.$area.val(),
      options = editor.options,
      updateFrameCallback = options.updateFrame,
      $body = $(editor.doc.body);

    // Check for textarea change to avoid unnecessary firing
    // of potentially heavy updateFrame callbacks.
    if (updateFrameCallback) {
      var sum = checksum(code);
      if (checkForChange && editor.areaChecksum == sum)
        return;
      editor.areaChecksum = sum;
    }

    // Convert the textarea source code into iframe html
    var html = updateFrameCallback ? updateFrameCallback(code) : code;

    // Prevent script injection attacks by html encoding script tags
    html = html.replace(/<(?=\/?script)/ig, "&lt;");

    // Update the iframe checksum
    if (options.updateTextArea)
      editor.frameChecksum = checksum(html);

    // Update the iframe and trigger the change event
    if (html != $body.html()) {
      $body.html(html);
      $(editor).triggerHandler(CHANGE);
    }

  }

  // updateTextArea - updates the textarea with the iframe contents
  function updateTextArea(editor, checkForChange) {

    var html = $(editor.doc.body).html(),
      options = editor.options,
      updateTextAreaCallback = options.updateTextArea,
      $area = editor.$area;

    // Check for iframe change to avoid unnecessary firing
    // of potentially heavy updateTextArea callbacks.
    if (updateTextAreaCallback) {
      var sum = checksum(html);
      if (checkForChange && editor.frameChecksum == sum)
        return;
      editor.frameChecksum = sum;
    }

    // Convert the iframe html into textarea source code
    var code = updateTextAreaCallback ? updateTextAreaCallback(html) : html;

    // Update the textarea checksum
    if (options.updateFrame)
      editor.areaChecksum = checksum(code);

    // Update the textarea and trigger the change event
    if (code != $area.val()) {
      $area.val(code);
      $(editor).triggerHandler(CHANGE);
    }

  }

})(jQuery);
