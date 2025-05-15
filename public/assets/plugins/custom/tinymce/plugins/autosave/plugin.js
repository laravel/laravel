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

    var eq = function (t) {
      return function (a) {
        return t === a;
      };
    };
    var isUndefined = eq(undefined);

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.Delay');

    var global$2 = tinymce.util.Tools.resolve('tinymce.util.LocalStorage');

    var global$3 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    var fireRestoreDraft = function (editor) {
      return editor.fire('RestoreDraft');
    };
    var fireStoreDraft = function (editor) {
      return editor.fire('StoreDraft');
    };
    var fireRemoveDraft = function (editor) {
      return editor.fire('RemoveDraft');
    };

    var parse = function (timeString, defaultTime) {
      var multiples = {
        s: 1000,
        m: 60000
      };
      var toParse = timeString || defaultTime;
      var parsedTime = /^(\d+)([ms]?)$/.exec('' + toParse);
      return (parsedTime[2] ? multiples[parsedTime[2]] : 1) * parseInt(toParse, 10);
    };

    var shouldAskBeforeUnload = function (editor) {
      return editor.getParam('autosave_ask_before_unload', true);
    };
    var getAutoSavePrefix = function (editor) {
      var location = document.location;
      return editor.getParam('autosave_prefix', 'tinymce-autosave-{path}{query}{hash}-{id}-').replace(/{path}/g, location.pathname).replace(/{query}/g, location.search).replace(/{hash}/g, location.hash).replace(/{id}/g, editor.id);
    };
    var shouldRestoreWhenEmpty = function (editor) {
      return editor.getParam('autosave_restore_when_empty', false);
    };
    var getAutoSaveInterval = function (editor) {
      return parse(editor.getParam('autosave_interval'), '30s');
    };
    var getAutoSaveRetention = function (editor) {
      return parse(editor.getParam('autosave_retention'), '20m');
    };

    var isEmpty = function (editor, html) {
      if (isUndefined(html)) {
        return editor.dom.isEmpty(editor.getBody());
      } else {
        var trimmedHtml = global$3.trim(html);
        if (trimmedHtml === '') {
          return true;
        } else {
          var fragment = new DOMParser().parseFromString(trimmedHtml, 'text/html');
          return editor.dom.isEmpty(fragment);
        }
      }
    };
    var hasDraft = function (editor) {
      var time = parseInt(global$2.getItem(getAutoSavePrefix(editor) + 'time'), 10) || 0;
      if (new Date().getTime() - time > getAutoSaveRetention(editor)) {
        removeDraft(editor, false);
        return false;
      }
      return true;
    };
    var removeDraft = function (editor, fire) {
      var prefix = getAutoSavePrefix(editor);
      global$2.removeItem(prefix + 'draft');
      global$2.removeItem(prefix + 'time');
      if (fire !== false) {
        fireRemoveDraft(editor);
      }
    };
    var storeDraft = function (editor) {
      var prefix = getAutoSavePrefix(editor);
      if (!isEmpty(editor) && editor.isDirty()) {
        global$2.setItem(prefix + 'draft', editor.getContent({
          format: 'raw',
          no_events: true
        }));
        global$2.setItem(prefix + 'time', new Date().getTime().toString());
        fireStoreDraft(editor);
      }
    };
    var restoreDraft = function (editor) {
      var prefix = getAutoSavePrefix(editor);
      if (hasDraft(editor)) {
        editor.setContent(global$2.getItem(prefix + 'draft'), { format: 'raw' });
        fireRestoreDraft(editor);
      }
    };
    var startStoreDraft = function (editor) {
      var interval = getAutoSaveInterval(editor);
      global$1.setEditorInterval(editor, function () {
        storeDraft(editor);
      }, interval);
    };
    var restoreLastDraft = function (editor) {
      editor.undoManager.transact(function () {
        restoreDraft(editor);
        removeDraft(editor);
      });
      editor.focus();
    };

    var get = function (editor) {
      return {
        hasDraft: function () {
          return hasDraft(editor);
        },
        storeDraft: function () {
          return storeDraft(editor);
        },
        restoreDraft: function () {
          return restoreDraft(editor);
        },
        removeDraft: function (fire) {
          return removeDraft(editor, fire);
        },
        isEmpty: function (html) {
          return isEmpty(editor, html);
        }
      };
    };

    var global$4 = tinymce.util.Tools.resolve('tinymce.EditorManager');

    var setup = function (editor) {
      editor.editorManager.on('BeforeUnload', function (e) {
        var msg;
        global$3.each(global$4.get(), function (editor) {
          if (editor.plugins.autosave) {
            editor.plugins.autosave.storeDraft();
          }
          if (!msg && editor.isDirty() && shouldAskBeforeUnload(editor)) {
            msg = editor.translate('You have unsaved changes are you sure you want to navigate away?');
          }
        });
        if (msg) {
          e.preventDefault();
          e.returnValue = msg;
        }
      });
    };

    var makeSetupHandler = function (editor) {
      return function (api) {
        api.setDisabled(!hasDraft(editor));
        var editorEventCallback = function () {
          return api.setDisabled(!hasDraft(editor));
        };
        editor.on('StoreDraft RestoreDraft RemoveDraft', editorEventCallback);
        return function () {
          return editor.off('StoreDraft RestoreDraft RemoveDraft', editorEventCallback);
        };
      };
    };
    var register = function (editor) {
      startStoreDraft(editor);
      editor.ui.registry.addButton('restoredraft', {
        tooltip: 'Restore last draft',
        icon: 'restore-draft',
        onAction: function () {
          restoreLastDraft(editor);
        },
        onSetup: makeSetupHandler(editor)
      });
      editor.ui.registry.addMenuItem('restoredraft', {
        text: 'Restore last draft',
        icon: 'restore-draft',
        onAction: function () {
          restoreLastDraft(editor);
        },
        onSetup: makeSetupHandler(editor)
      });
    };

    function Plugin () {
      global.add('autosave', function (editor) {
        setup(editor);
        register(editor);
        editor.on('init', function () {
          if (shouldRestoreWhenEmpty(editor) && editor.dom.isEmpty(editor.getBody())) {
            restoreDraft(editor);
          }
        });
        return get(editor);
      });
    }

    Plugin();

}());
