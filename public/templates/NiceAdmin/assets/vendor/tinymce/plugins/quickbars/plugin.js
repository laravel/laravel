/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    var global$2 = tinymce.util.Tools.resolve('tinymce.PluginManager');

    const hasProto = (v, constructor, predicate) => {
      var _a;
      if (predicate(v, constructor.prototype)) {
        return true;
      } else {
        return ((_a = v.constructor) === null || _a === void 0 ? void 0 : _a.name) === constructor.name;
      }
    };
    const typeOf = x => {
      const t = typeof x;
      if (x === null) {
        return 'null';
      } else if (t === 'object' && Array.isArray(x)) {
        return 'array';
      } else if (t === 'object' && hasProto(x, String, (o, proto) => proto.isPrototypeOf(o))) {
        return 'string';
      } else {
        return t;
      }
    };
    const isType = type => value => typeOf(value) === type;
    const isSimpleType = type => value => typeof value === type;
    const isString = isType('string');
    const isBoolean = isSimpleType('boolean');
    const isNullable = a => a === null || a === undefined;
    const isNonNullable = a => !isNullable(a);
    const isFunction = isSimpleType('function');

    const option = name => editor => editor.options.get(name);
    const register = editor => {
      const registerOption = editor.options.register;
      const toolbarProcessor = defaultValue => value => {
        const valid = isBoolean(value) || isString(value);
        if (valid) {
          if (isBoolean(value)) {
            return {
              value: value ? defaultValue : '',
              valid
            };
          } else {
            return {
              value: value.trim(),
              valid
            };
          }
        } else {
          return {
            valid: false,
            message: 'Must be a boolean or string.'
          };
        }
      };
      const defaultSelectionToolbar = 'bold italic | quicklink h2 h3 blockquote';
      registerOption('quickbars_selection_toolbar', {
        processor: toolbarProcessor(defaultSelectionToolbar),
        default: defaultSelectionToolbar
      });
      const defaultInsertToolbar = 'quickimage quicktable';
      registerOption('quickbars_insert_toolbar', {
        processor: toolbarProcessor(defaultInsertToolbar),
        default: defaultInsertToolbar
      });
      const defaultImageToolbar = 'alignleft aligncenter alignright';
      registerOption('quickbars_image_toolbar', {
        processor: toolbarProcessor(defaultImageToolbar),
        default: defaultImageToolbar
      });
    };
    const getTextSelectionToolbarItems = option('quickbars_selection_toolbar');
    const getInsertToolbarItems = option('quickbars_insert_toolbar');
    const getImageToolbarItems = option('quickbars_image_toolbar');

    let unique = 0;
    const generate = prefix => {
      const date = new Date();
      const time = date.getTime();
      const random = Math.floor(Math.random() * 1000000000);
      unique++;
      return prefix + '_' + random + unique + String(time);
    };

    const insertTable = (editor, columns, rows) => {
      editor.execCommand('mceInsertTable', false, {
        rows,
        columns
      });
    };
    const insertBlob = (editor, base64, blob) => {
      const blobCache = editor.editorUpload.blobCache;
      const blobInfo = blobCache.create(generate('mceu'), blob, base64);
      blobCache.add(blobInfo);
      editor.insertContent(editor.dom.createHTML('img', { src: blobInfo.blobUri() }));
    };

    const blobToBase64 = blob => {
      return new Promise(resolve => {
        const reader = new FileReader();
        reader.onloadend = () => {
          resolve(reader.result.split(',')[1]);
        };
        reader.readAsDataURL(blob);
      });
    };

    var global$1 = tinymce.util.Tools.resolve('tinymce.Env');

    var global = tinymce.util.Tools.resolve('tinymce.util.Delay');

    const pickFile = editor => new Promise(resolve => {
      const fileInput = document.createElement('input');
      fileInput.type = 'file';
      fileInput.accept = 'image/*';
      fileInput.style.position = 'fixed';
      fileInput.style.left = '0';
      fileInput.style.top = '0';
      fileInput.style.opacity = '0.001';
      document.body.appendChild(fileInput);
      const changeHandler = e => {
        resolve(Array.prototype.slice.call(e.target.files));
      };
      fileInput.addEventListener('change', changeHandler);
      const cancelHandler = e => {
        const cleanup = () => {
          var _a;
          resolve([]);
          (_a = fileInput.parentNode) === null || _a === void 0 ? void 0 : _a.removeChild(fileInput);
        };
        if (global$1.os.isAndroid() && e.type !== 'remove') {
          global.setEditorTimeout(editor, cleanup, 0);
        } else {
          cleanup();
        }
        editor.off('focusin remove', cancelHandler);
      };
      editor.on('focusin remove', cancelHandler);
      fileInput.click();
    });

    const setupButtons = editor => {
      editor.ui.registry.addButton('quickimage', {
        icon: 'image',
        tooltip: 'Insert image',
        onAction: () => {
          pickFile(editor).then(files => {
            if (files.length > 0) {
              const blob = files[0];
              blobToBase64(blob).then(base64 => {
                insertBlob(editor, base64, blob);
              });
            }
          });
        }
      });
      editor.ui.registry.addButton('quicktable', {
        icon: 'table',
        tooltip: 'Insert table',
        onAction: () => {
          insertTable(editor, 2, 2);
        }
      });
    };

    const constant = value => {
      return () => {
        return value;
      };
    };
    const never = constant(false);

    class Optional {
      constructor(tag, value) {
        this.tag = tag;
        this.value = value;
      }
      static some(value) {
        return new Optional(true, value);
      }
      static none() {
        return Optional.singletonNone;
      }
      fold(onNone, onSome) {
        if (this.tag) {
          return onSome(this.value);
        } else {
          return onNone();
        }
      }
      isSome() {
        return this.tag;
      }
      isNone() {
        return !this.tag;
      }
      map(mapper) {
        if (this.tag) {
          return Optional.some(mapper(this.value));
        } else {
          return Optional.none();
        }
      }
      bind(binder) {
        if (this.tag) {
          return binder(this.value);
        } else {
          return Optional.none();
        }
      }
      exists(predicate) {
        return this.tag && predicate(this.value);
      }
      forall(predicate) {
        return !this.tag || predicate(this.value);
      }
      filter(predicate) {
        if (!this.tag || predicate(this.value)) {
          return this;
        } else {
          return Optional.none();
        }
      }
      getOr(replacement) {
        return this.tag ? this.value : replacement;
      }
      or(replacement) {
        return this.tag ? this : replacement;
      }
      getOrThunk(thunk) {
        return this.tag ? this.value : thunk();
      }
      orThunk(thunk) {
        return this.tag ? this : thunk();
      }
      getOrDie(message) {
        if (!this.tag) {
          throw new Error(message !== null && message !== void 0 ? message : 'Called getOrDie on None');
        } else {
          return this.value;
        }
      }
      static from(value) {
        return isNonNullable(value) ? Optional.some(value) : Optional.none();
      }
      getOrNull() {
        return this.tag ? this.value : null;
      }
      getOrUndefined() {
        return this.value;
      }
      each(worker) {
        if (this.tag) {
          worker(this.value);
        }
      }
      toArray() {
        return this.tag ? [this.value] : [];
      }
      toString() {
        return this.tag ? `some(${ this.value })` : 'none()';
      }
    }
    Optional.singletonNone = new Optional(false);

    typeof window !== 'undefined' ? window : Function('return this;')();

    const ELEMENT = 1;

    const name = element => {
      const r = element.dom.nodeName;
      return r.toLowerCase();
    };

    const has = (element, key) => {
      const dom = element.dom;
      return dom && dom.hasAttribute ? dom.hasAttribute(key) : false;
    };

    var ClosestOrAncestor = (is, ancestor, scope, a, isRoot) => {
      if (is(scope, a)) {
        return Optional.some(scope);
      } else if (isFunction(isRoot) && isRoot(scope)) {
        return Optional.none();
      } else {
        return ancestor(scope, a, isRoot);
      }
    };

    const fromHtml = (html, scope) => {
      const doc = scope || document;
      const div = doc.createElement('div');
      div.innerHTML = html;
      if (!div.hasChildNodes() || div.childNodes.length > 1) {
        const message = 'HTML does not have a single root node';
        console.error(message, html);
        throw new Error(message);
      }
      return fromDom(div.childNodes[0]);
    };
    const fromTag = (tag, scope) => {
      const doc = scope || document;
      const node = doc.createElement(tag);
      return fromDom(node);
    };
    const fromText = (text, scope) => {
      const doc = scope || document;
      const node = doc.createTextNode(text);
      return fromDom(node);
    };
    const fromDom = node => {
      if (node === null || node === undefined) {
        throw new Error('Node cannot be null or undefined');
      }
      return { dom: node };
    };
    const fromPoint = (docElm, x, y) => Optional.from(docElm.dom.elementFromPoint(x, y)).map(fromDom);
    const SugarElement = {
      fromHtml,
      fromTag,
      fromText,
      fromDom,
      fromPoint
    };

    const is = (element, selector) => {
      const dom = element.dom;
      if (dom.nodeType !== ELEMENT) {
        return false;
      } else {
        const elem = dom;
        if (elem.matches !== undefined) {
          return elem.matches(selector);
        } else if (elem.msMatchesSelector !== undefined) {
          return elem.msMatchesSelector(selector);
        } else if (elem.webkitMatchesSelector !== undefined) {
          return elem.webkitMatchesSelector(selector);
        } else if (elem.mozMatchesSelector !== undefined) {
          return elem.mozMatchesSelector(selector);
        } else {
          throw new Error('Browser lacks native selectors');
        }
      }
    };

    const ancestor$1 = (scope, predicate, isRoot) => {
      let element = scope.dom;
      const stop = isFunction(isRoot) ? isRoot : never;
      while (element.parentNode) {
        element = element.parentNode;
        const el = SugarElement.fromDom(element);
        if (predicate(el)) {
          return Optional.some(el);
        } else if (stop(el)) {
          break;
        }
      }
      return Optional.none();
    };
    const closest$2 = (scope, predicate, isRoot) => {
      const is = (s, test) => test(s);
      return ClosestOrAncestor(is, ancestor$1, scope, predicate, isRoot);
    };

    const closest$1 = (scope, predicate, isRoot) => closest$2(scope, predicate, isRoot).isSome();

    const ancestor = (scope, selector, isRoot) => ancestor$1(scope, e => is(e, selector), isRoot);
    const closest = (scope, selector, isRoot) => {
      const is$1 = (element, selector) => is(element, selector);
      return ClosestOrAncestor(is$1, ancestor, scope, selector, isRoot);
    };

    const addToEditor$1 = editor => {
      const insertToolbarItems = getInsertToolbarItems(editor);
      if (insertToolbarItems.length > 0) {
        editor.ui.registry.addContextToolbar('quickblock', {
          predicate: node => {
            const sugarNode = SugarElement.fromDom(node);
            const textBlockElementsMap = editor.schema.getTextBlockElements();
            const isRoot = elem => elem.dom === editor.getBody();
            return !has(sugarNode, 'data-mce-bogus') && closest(sugarNode, 'table,[data-mce-bogus="all"]', isRoot).fold(() => closest$1(sugarNode, elem => name(elem) in textBlockElementsMap && editor.dom.isEmpty(elem.dom), isRoot), never);
          },
          items: insertToolbarItems,
          position: 'line',
          scope: 'editor'
        });
      }
    };

    const addToEditor = editor => {
      const isEditable = node => editor.dom.getContentEditableParent(node) !== 'false';
      const isImage = node => node.nodeName === 'IMG' || node.nodeName === 'FIGURE' && /image/i.test(node.className);
      const imageToolbarItems = getImageToolbarItems(editor);
      if (imageToolbarItems.length > 0) {
        editor.ui.registry.addContextToolbar('imageselection', {
          predicate: isImage,
          items: imageToolbarItems,
          position: 'node'
        });
      }
      const textToolbarItems = getTextSelectionToolbarItems(editor);
      if (textToolbarItems.length > 0) {
        editor.ui.registry.addContextToolbar('textselection', {
          predicate: node => !isImage(node) && !editor.selection.isCollapsed() && isEditable(node),
          items: textToolbarItems,
          position: 'selection',
          scope: 'editor'
        });
      }
    };

    var Plugin = () => {
      global$2.add('quickbars', editor => {
        register(editor);
        setupButtons(editor);
        addToEditor$1(editor);
        addToEditor(editor);
      });
    };

    Plugin();

})();
