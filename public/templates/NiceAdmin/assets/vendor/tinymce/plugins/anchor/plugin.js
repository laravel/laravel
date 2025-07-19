/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    var global$2 = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var global$1 = tinymce.util.Tools.resolve('tinymce.dom.RangeUtils');

    var global = tinymce.util.Tools.resolve('tinymce.util.Tools');

    const option = name => editor => editor.options.get(name);
    const register$2 = editor => {
      const registerOption = editor.options.register;
      registerOption('allow_html_in_named_anchor', {
        processor: 'boolean',
        default: false
      });
    };
    const allowHtmlInNamedAnchor = option('allow_html_in_named_anchor');

    const namedAnchorSelector = 'a:not([href])';
    const isEmptyString = str => !str;
    const getIdFromAnchor = elm => {
      const id = elm.getAttribute('id') || elm.getAttribute('name');
      return id || '';
    };
    const isAnchor = elm => elm.nodeName.toLowerCase() === 'a';
    const isNamedAnchor = elm => isAnchor(elm) && !elm.getAttribute('href') && getIdFromAnchor(elm) !== '';
    const isEmptyNamedAnchor = elm => isNamedAnchor(elm) && !elm.firstChild;

    const removeEmptyNamedAnchorsInSelection = editor => {
      const dom = editor.dom;
      global$1(dom).walk(editor.selection.getRng(), nodes => {
        global.each(nodes, node => {
          if (isEmptyNamedAnchor(node)) {
            dom.remove(node, false);
          }
        });
      });
    };
    const isValidId = id => /^[A-Za-z][A-Za-z0-9\-:._]*$/.test(id);
    const getNamedAnchor = editor => editor.dom.getParent(editor.selection.getStart(), namedAnchorSelector);
    const getId = editor => {
      const anchor = getNamedAnchor(editor);
      if (anchor) {
        return getIdFromAnchor(anchor);
      } else {
        return '';
      }
    };
    const createAnchor = (editor, id) => {
      editor.undoManager.transact(() => {
        if (!allowHtmlInNamedAnchor(editor)) {
          editor.selection.collapse(true);
        }
        if (editor.selection.isCollapsed()) {
          editor.insertContent(editor.dom.createHTML('a', { id }));
        } else {
          removeEmptyNamedAnchorsInSelection(editor);
          editor.formatter.remove('namedAnchor', undefined, undefined, true);
          editor.formatter.apply('namedAnchor', { value: id });
          editor.addVisual();
        }
      });
    };
    const updateAnchor = (editor, id, anchorElement) => {
      anchorElement.removeAttribute('name');
      anchorElement.id = id;
      editor.addVisual();
      editor.undoManager.add();
    };
    const insert = (editor, id) => {
      const anchor = getNamedAnchor(editor);
      if (anchor) {
        updateAnchor(editor, id, anchor);
      } else {
        createAnchor(editor, id);
      }
      editor.focus();
    };

    const insertAnchor = (editor, newId) => {
      if (!isValidId(newId)) {
        editor.windowManager.alert('ID should start with a letter, followed only by letters, numbers, dashes, dots, colons or underscores.');
        return false;
      } else {
        insert(editor, newId);
        return true;
      }
    };
    const open = editor => {
      const currentId = getId(editor);
      editor.windowManager.open({
        title: 'Anchor',
        size: 'normal',
        body: {
          type: 'panel',
          items: [{
              name: 'id',
              type: 'input',
              label: 'ID',
              placeholder: 'example'
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
        initialData: { id: currentId },
        onSubmit: api => {
          if (insertAnchor(editor, api.getData().id)) {
            api.close();
          }
        }
      });
    };

    const register$1 = editor => {
      editor.addCommand('mceAnchor', () => {
        open(editor);
      });
    };

    const isNamedAnchorNode = node => isEmptyString(node.attr('href')) && !isEmptyString(node.attr('id') || node.attr('name'));
    const isEmptyNamedAnchorNode = node => isNamedAnchorNode(node) && !node.firstChild;
    const setContentEditable = state => nodes => {
      for (let i = 0; i < nodes.length; i++) {
        const node = nodes[i];
        if (isEmptyNamedAnchorNode(node)) {
          node.attr('contenteditable', state);
        }
      }
    };
    const setup = editor => {
      editor.on('PreInit', () => {
        editor.parser.addNodeFilter('a', setContentEditable('false'));
        editor.serializer.addNodeFilter('a', setContentEditable(null));
      });
    };

    const registerFormats = editor => {
      editor.formatter.register('namedAnchor', {
        inline: 'a',
        selector: namedAnchorSelector,
        remove: 'all',
        split: true,
        deep: true,
        attributes: { id: '%value' },
        onmatch: (node, _fmt, _itemName) => {
          return isNamedAnchor(node);
        }
      });
    };

    const register = editor => {
      const onAction = () => editor.execCommand('mceAnchor');
      editor.ui.registry.addToggleButton('anchor', {
        icon: 'bookmark',
        tooltip: 'Anchor',
        onAction,
        onSetup: buttonApi => editor.selection.selectorChangedWithUnbind('a:not([href])', buttonApi.setActive).unbind
      });
      editor.ui.registry.addMenuItem('anchor', {
        icon: 'bookmark',
        text: 'Anchor...',
        onAction
      });
    };

    var Plugin = () => {
      global$2.add('anchor', editor => {
        register$2(editor);
        setup(editor);
        register$1(editor);
        register(editor);
        editor.on('PreInit', () => {
          registerFormats(editor);
        });
      });
    };

    Plugin();

})();
