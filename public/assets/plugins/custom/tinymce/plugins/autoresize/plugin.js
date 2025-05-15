/**
 * Copyright (c) Tiny Technologies, Inc. All rights reserved.
 * Licensed under the LGPL or a commercial license.
 * For LGPL see License.txt in the project root for license information.
 * For commercial licenses see https://www.tiny.cloud/
 *
 * Version: 5.8.1 (2021-05-20)
 */
(function () {
    'use strict';

    var Cell = function (initial) {
      var value = initial;
      var get = function () {
        return value;
      };
      var set = function (v) {
        value = v;
      };
      return {
        get: get,
        set: set
      };
    };

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var global$1 = tinymce.util.Tools.resolve('tinymce.Env');

    var global$2 = tinymce.util.Tools.resolve('tinymce.util.Delay');

    var fireResizeEditor = function (editor) {
      return editor.fire('ResizeEditor');
    };

    var getAutoResizeMinHeight = function (editor) {
      return editor.getParam('min_height', editor.getElement().offsetHeight, 'number');
    };
    var getAutoResizeMaxHeight = function (editor) {
      return editor.getParam('max_height', 0, 'number');
    };
    var getAutoResizeOverflowPadding = function (editor) {
      return editor.getParam('autoresize_overflow_padding', 1, 'number');
    };
    var getAutoResizeBottomMargin = function (editor) {
      return editor.getParam('autoresize_bottom_margin', 50, 'number');
    };
    var shouldAutoResizeOnInit = function (editor) {
      return editor.getParam('autoresize_on_init', true, 'boolean');
    };

    var isFullscreen = function (editor) {
      return editor.plugins.fullscreen && editor.plugins.fullscreen.isFullscreen();
    };
    var wait = function (editor, oldSize, times, interval, callback) {
      global$2.setEditorTimeout(editor, function () {
        resize(editor, oldSize);
        if (times--) {
          wait(editor, oldSize, times, interval, callback);
        } else if (callback) {
          callback();
        }
      }, interval);
    };
    var toggleScrolling = function (editor, state) {
      var body = editor.getBody();
      if (body) {
        body.style.overflowY = state ? '' : 'hidden';
        if (!state) {
          body.scrollTop = 0;
        }
      }
    };
    var parseCssValueToInt = function (dom, elm, name, computed) {
      var value = parseInt(dom.getStyle(elm, name, computed), 10);
      return isNaN(value) ? 0 : value;
    };
    var resize = function (editor, oldSize) {
      var dom = editor.dom;
      var doc = editor.getDoc();
      if (!doc) {
        return;
      }
      if (isFullscreen(editor)) {
        toggleScrolling(editor, true);
        return;
      }
      var docEle = doc.documentElement;
      var resizeBottomMargin = getAutoResizeBottomMargin(editor);
      var resizeHeight = getAutoResizeMinHeight(editor);
      var marginTop = parseCssValueToInt(dom, docEle, 'margin-top', true);
      var marginBottom = parseCssValueToInt(dom, docEle, 'margin-bottom', true);
      var contentHeight = docEle.offsetHeight + marginTop + marginBottom + resizeBottomMargin;
      if (contentHeight < 0) {
        contentHeight = 0;
      }
      var containerHeight = editor.getContainer().offsetHeight;
      var contentAreaHeight = editor.getContentAreaContainer().offsetHeight;
      var chromeHeight = containerHeight - contentAreaHeight;
      if (contentHeight + chromeHeight > getAutoResizeMinHeight(editor)) {
        resizeHeight = contentHeight + chromeHeight;
      }
      var maxHeight = getAutoResizeMaxHeight(editor);
      if (maxHeight && resizeHeight > maxHeight) {
        resizeHeight = maxHeight;
        toggleScrolling(editor, true);
      } else {
        toggleScrolling(editor, false);
      }
      if (resizeHeight !== oldSize.get()) {
        var deltaSize = resizeHeight - oldSize.get();
        dom.setStyle(editor.getContainer(), 'height', resizeHeight + 'px');
        oldSize.set(resizeHeight);
        fireResizeEditor(editor);
        if (global$1.browser.isSafari() && global$1.mac) {
          var win = editor.getWin();
          win.scrollTo(win.pageXOffset, win.pageYOffset);
        }
        if (editor.hasFocus()) {
          editor.selection.scrollIntoView(editor.selection.getNode());
        }
        if (global$1.webkit && deltaSize < 0) {
          resize(editor, oldSize);
        }
      }
    };
    var setup = function (editor, oldSize) {
      editor.on('init', function () {
        var overflowPadding = getAutoResizeOverflowPadding(editor);
        var dom = editor.dom;
        dom.setStyles(editor.getDoc().documentElement, { height: 'auto' });
        dom.setStyles(editor.getBody(), {
          'paddingLeft': overflowPadding,
          'paddingRight': overflowPadding,
          'min-height': 0
        });
      });
      editor.on('NodeChange SetContent keyup FullscreenStateChanged ResizeContent', function () {
        resize(editor, oldSize);
      });
      if (shouldAutoResizeOnInit(editor)) {
        editor.on('init', function () {
          wait(editor, oldSize, 20, 100, function () {
            wait(editor, oldSize, 5, 1000);
          });
        });
      }
    };

    var register = function (editor, oldSize) {
      editor.addCommand('mceAutoResize', function () {
        resize(editor, oldSize);
      });
    };

    function Plugin () {
      global.add('autoresize', function (editor) {
        if (!editor.settings.hasOwnProperty('resize')) {
          editor.settings.resize = false;
        }
        if (!editor.inline) {
          var oldSize = Cell(0);
          register(editor, oldSize);
          setup(editor, oldSize);
        }
      });
    }

    Plugin();

}());
