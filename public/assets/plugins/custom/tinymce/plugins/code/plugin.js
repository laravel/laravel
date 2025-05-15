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

    var setContent = function (editor, html) {
      editor.focus();
      editor.undoManager.transact(function () {
        editor.setContent(html);
      });
      editor.selection.setCursorLocation();
      editor.nodeChanged();
    };
    var getContent = function (editor) {
      return editor.getContent({ source_view: true });
    };

    var open = function (editor) {
      var editorContent = getContent(editor);
      editor.windowManager.open({
        title: 'Source Code',
        size: 'large',
        body: {
          type: 'panel',
          items: [{
              type: 'textarea',
              name: 'code'
            }]
        },
        buttons: [
          {
            type: 'cancel',
            name: 'cancel',
            text: 'Cancel'
          },
          {
            type: 'submit',
            name: 'save',
            text: 'Save',
            primary: true
          }
        ],
        initialData: { code: editorContent },
        onSubmit: function (api) {
          setContent(editor, api.getData().code);
          api.close();
        }
      });
    };

    var register = function (editor) {
      editor.addCommand('mceCodeEditor', function () {
        open(editor);
      });
    };

    var register$1 = function (editor) {
      editor.ui.registry.addButton('code', {
        icon: 'sourcecode',
        tooltip: 'Source code',
        onAction: function () {
          return open(editor);
        }
      });
      editor.ui.registry.addMenuItem('code', {
        icon: 'sourcecode',
        text: 'Source code',
        onAction: function () {
          return open(editor);
        }
      });
    };

    function Plugin () {
      global.add('code', function (editor) {
        register(editor);
        register$1(editor);
        return {};
      });
    }

    Plugin();

}());
