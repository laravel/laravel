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

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var global$1 = tinymce.util.Tools.resolve('tinymce.Env');

    var getSeparatorHtml = function (editor) {
      return editor.getParam('pagebreak_separator', '<!-- pagebreak -->');
    };
    var shouldSplitBlock = function (editor) {
      return editor.getParam('pagebreak_split_block', false);
    };

    var pageBreakClass = 'mce-pagebreak';
    var getPlaceholderHtml = function (shouldSplitBlock) {
      var html = '<img src="' + global$1.transparentSrc + '" class="' + pageBreakClass + '" data-mce-resize="false" data-mce-placeholder />';
      return shouldSplitBlock ? '<p>' + html + '</p>' : html;
    };
    var setup = function (editor) {
      var separatorHtml = getSeparatorHtml(editor);
      var shouldSplitBlock$1 = function () {
        return shouldSplitBlock(editor);
      };
      var pageBreakSeparatorRegExp = new RegExp(separatorHtml.replace(/[\?\.\*\[\]\(\)\{\}\+\^\$\:]/g, function (a) {
        return '\\' + a;
      }), 'gi');
      editor.on('BeforeSetContent', function (e) {
        e.content = e.content.replace(pageBreakSeparatorRegExp, getPlaceholderHtml(shouldSplitBlock$1()));
      });
      editor.on('PreInit', function () {
        editor.serializer.addNodeFilter('img', function (nodes) {
          var i = nodes.length, node, className;
          while (i--) {
            node = nodes[i];
            className = node.attr('class');
            if (className && className.indexOf(pageBreakClass) !== -1) {
              var parentNode = node.parent;
              if (editor.schema.getBlockElements()[parentNode.name] && shouldSplitBlock$1()) {
                parentNode.type = 3;
                parentNode.value = separatorHtml;
                parentNode.raw = true;
                node.remove();
                continue;
              }
              node.type = 3;
              node.value = separatorHtml;
              node.raw = true;
            }
          }
        });
      });
    };

    var register = function (editor) {
      editor.addCommand('mcePageBreak', function () {
        editor.insertContent(getPlaceholderHtml(shouldSplitBlock(editor)));
      });
    };

    var setup$1 = function (editor) {
      editor.on('ResolveName', function (e) {
        if (e.target.nodeName === 'IMG' && editor.dom.hasClass(e.target, pageBreakClass)) {
          e.name = 'pagebreak';
        }
      });
    };

    var register$1 = function (editor) {
      editor.ui.registry.addButton('pagebreak', {
        icon: 'page-break',
        tooltip: 'Page break',
        onAction: function () {
          return editor.execCommand('mcePageBreak');
        }
      });
      editor.ui.registry.addMenuItem('pagebreak', {
        text: 'Page break',
        icon: 'page-break',
        onAction: function () {
          return editor.execCommand('mcePageBreak');
        }
      });
    };

    function Plugin () {
      global.add('pagebreak', function (editor) {
        register(editor);
        register$1(editor);
        setup(editor);
        setup$1(editor);
      });
    }

    Plugin();

}());
