/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    const Cell = initial => {
      let value = initial;
      const get = () => {
        return value;
      };
      const set = v => {
        value = v;
      };
      return {
        get,
        set
      };
    };

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    const get$2 = toggleState => {
      const isEnabled = () => {
        return toggleState.get();
      };
      return { isEnabled };
    };

    const fireVisualChars = (editor, state) => {
      return editor.dispatch('VisualChars', { state });
    };

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
    const isType$1 = type => value => typeOf(value) === type;
    const isSimpleType = type => value => typeof value === type;
    const eq = t => a => t === a;
    const isString = isType$1('string');
    const isNull = eq(null);
    const isBoolean = isSimpleType('boolean');
    const isNullable = a => a === null || a === undefined;
    const isNonNullable = a => !isNullable(a);
    const isNumber = isSimpleType('number');

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

    const map = (xs, f) => {
      const len = xs.length;
      const r = new Array(len);
      for (let i = 0; i < len; i++) {
        const x = xs[i];
        r[i] = f(x, i);
      }
      return r;
    };
    const each$1 = (xs, f) => {
      for (let i = 0, len = xs.length; i < len; i++) {
        const x = xs[i];
        f(x, i);
      }
    };
    const filter = (xs, pred) => {
      const r = [];
      for (let i = 0, len = xs.length; i < len; i++) {
        const x = xs[i];
        if (pred(x, i)) {
          r.push(x);
        }
      }
      return r;
    };

    const keys = Object.keys;
    const each = (obj, f) => {
      const props = keys(obj);
      for (let k = 0, len = props.length; k < len; k++) {
        const i = props[k];
        const x = obj[i];
        f(x, i);
      }
    };

    typeof window !== 'undefined' ? window : Function('return this;')();

    const TEXT = 3;

    const type = element => element.dom.nodeType;
    const value = element => element.dom.nodeValue;
    const isType = t => element => type(element) === t;
    const isText = isType(TEXT);

    const rawSet = (dom, key, value) => {
      if (isString(value) || isBoolean(value) || isNumber(value)) {
        dom.setAttribute(key, value + '');
      } else {
        console.error('Invalid call to Attribute.set. Key ', key, ':: Value ', value, ':: Element ', dom);
        throw new Error('Attribute value was not simple');
      }
    };
    const set = (element, key, value) => {
      rawSet(element.dom, key, value);
    };
    const get$1 = (element, key) => {
      const v = element.dom.getAttribute(key);
      return v === null ? undefined : v;
    };
    const remove$3 = (element, key) => {
      element.dom.removeAttribute(key);
    };

    const read = (element, attr) => {
      const value = get$1(element, attr);
      return value === undefined || value === '' ? [] : value.split(' ');
    };
    const add$2 = (element, attr, id) => {
      const old = read(element, attr);
      const nu = old.concat([id]);
      set(element, attr, nu.join(' '));
      return true;
    };
    const remove$2 = (element, attr, id) => {
      const nu = filter(read(element, attr), v => v !== id);
      if (nu.length > 0) {
        set(element, attr, nu.join(' '));
      } else {
        remove$3(element, attr);
      }
      return false;
    };

    const supports = element => element.dom.classList !== undefined;
    const get = element => read(element, 'class');
    const add$1 = (element, clazz) => add$2(element, 'class', clazz);
    const remove$1 = (element, clazz) => remove$2(element, 'class', clazz);

    const add = (element, clazz) => {
      if (supports(element)) {
        element.dom.classList.add(clazz);
      } else {
        add$1(element, clazz);
      }
    };
    const cleanClass = element => {
      const classList = supports(element) ? element.dom.classList : get(element);
      if (classList.length === 0) {
        remove$3(element, 'class');
      }
    };
    const remove = (element, clazz) => {
      if (supports(element)) {
        const classList = element.dom.classList;
        classList.remove(clazz);
      } else {
        remove$1(element, clazz);
      }
      cleanClass(element);
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

    const charMap = {
      '\xA0': 'nbsp',
      '\xAD': 'shy'
    };
    const charMapToRegExp = (charMap, global) => {
      let regExp = '';
      each(charMap, (_value, key) => {
        regExp += key;
      });
      return new RegExp('[' + regExp + ']', global ? 'g' : '');
    };
    const charMapToSelector = charMap => {
      let selector = '';
      each(charMap, value => {
        if (selector) {
          selector += ',';
        }
        selector += 'span.mce-' + value;
      });
      return selector;
    };
    const regExp = charMapToRegExp(charMap);
    const regExpGlobal = charMapToRegExp(charMap, true);
    const selector = charMapToSelector(charMap);
    const nbspClass = 'mce-nbsp';

    const wrapCharWithSpan = value => '<span data-mce-bogus="1" class="mce-' + charMap[value] + '">' + value + '</span>';

    const isMatch = n => {
      const value$1 = value(n);
      return isText(n) && isString(value$1) && regExp.test(value$1);
    };
    const filterDescendants = (scope, predicate) => {
      let result = [];
      const dom = scope.dom;
      const children = map(dom.childNodes, SugarElement.fromDom);
      each$1(children, x => {
        if (predicate(x)) {
          result = result.concat([x]);
        }
        result = result.concat(filterDescendants(x, predicate));
      });
      return result;
    };
    const findParentElm = (elm, rootElm) => {
      while (elm.parentNode) {
        if (elm.parentNode === rootElm) {
          return rootElm;
        }
        elm = elm.parentNode;
      }
      return undefined;
    };
    const replaceWithSpans = text => text.replace(regExpGlobal, wrapCharWithSpan);

    const isWrappedNbsp = node => node.nodeName.toLowerCase() === 'span' && node.classList.contains('mce-nbsp-wrap');
    const show = (editor, rootElm) => {
      const dom = editor.dom;
      const nodeList = filterDescendants(SugarElement.fromDom(rootElm), isMatch);
      each$1(nodeList, n => {
        var _a;
        const parent = n.dom.parentNode;
        if (isWrappedNbsp(parent)) {
          add(SugarElement.fromDom(parent), nbspClass);
        } else {
          const withSpans = replaceWithSpans(dom.encode((_a = value(n)) !== null && _a !== void 0 ? _a : ''));
          const div = dom.create('div', {}, withSpans);
          let node;
          while (node = div.lastChild) {
            dom.insertAfter(node, n.dom);
          }
          editor.dom.remove(n.dom);
        }
      });
    };
    const hide = (editor, rootElm) => {
      const nodeList = editor.dom.select(selector, rootElm);
      each$1(nodeList, node => {
        if (isWrappedNbsp(node)) {
          remove(SugarElement.fromDom(node), nbspClass);
        } else {
          editor.dom.remove(node, true);
        }
      });
    };
    const toggle = editor => {
      const body = editor.getBody();
      const bookmark = editor.selection.getBookmark();
      let parentNode = findParentElm(editor.selection.getNode(), body);
      parentNode = parentNode !== undefined ? parentNode : body;
      hide(editor, parentNode);
      show(editor, parentNode);
      editor.selection.moveToBookmark(bookmark);
    };

    const applyVisualChars = (editor, toggleState) => {
      fireVisualChars(editor, toggleState.get());
      const body = editor.getBody();
      if (toggleState.get() === true) {
        show(editor, body);
      } else {
        hide(editor, body);
      }
    };
    const toggleVisualChars = (editor, toggleState) => {
      toggleState.set(!toggleState.get());
      const bookmark = editor.selection.getBookmark();
      applyVisualChars(editor, toggleState);
      editor.selection.moveToBookmark(bookmark);
    };

    const register$2 = (editor, toggleState) => {
      editor.addCommand('mceVisualChars', () => {
        toggleVisualChars(editor, toggleState);
      });
    };

    const option = name => editor => editor.options.get(name);
    const register$1 = editor => {
      const registerOption = editor.options.register;
      registerOption('visualchars_default_state', {
        processor: 'boolean',
        default: false
      });
    };
    const isEnabledByDefault = option('visualchars_default_state');

    const setup$1 = (editor, toggleState) => {
      editor.on('init', () => {
        applyVisualChars(editor, toggleState);
      });
    };

    const first = (fn, rate) => {
      let timer = null;
      const cancel = () => {
        if (!isNull(timer)) {
          clearTimeout(timer);
          timer = null;
        }
      };
      const throttle = (...args) => {
        if (isNull(timer)) {
          timer = setTimeout(() => {
            timer = null;
            fn.apply(null, args);
          }, rate);
        }
      };
      return {
        cancel,
        throttle
      };
    };

    const setup = (editor, toggleState) => {
      const debouncedToggle = first(() => {
        toggle(editor);
      }, 300);
      editor.on('keydown', e => {
        if (toggleState.get() === true) {
          e.keyCode === 13 ? toggle(editor) : debouncedToggle.throttle();
        }
      });
      editor.on('remove', debouncedToggle.cancel);
    };

    const toggleActiveState = (editor, enabledStated) => api => {
      api.setActive(enabledStated.get());
      const editorEventCallback = e => api.setActive(e.state);
      editor.on('VisualChars', editorEventCallback);
      return () => editor.off('VisualChars', editorEventCallback);
    };
    const register = (editor, toggleState) => {
      const onAction = () => editor.execCommand('mceVisualChars');
      editor.ui.registry.addToggleButton('visualchars', {
        tooltip: 'Show invisible characters',
        icon: 'visualchars',
        onAction,
        onSetup: toggleActiveState(editor, toggleState)
      });
      editor.ui.registry.addToggleMenuItem('visualchars', {
        text: 'Show invisible characters',
        icon: 'visualchars',
        onAction,
        onSetup: toggleActiveState(editor, toggleState)
      });
    };

    var Plugin = () => {
      global.add('visualchars', editor => {
        register$1(editor);
        const toggleState = Cell(isEnabledByDefault(editor));
        register$2(editor, toggleState);
        register(editor, toggleState);
        setup(editor, toggleState);
        setup$1(editor, toggleState);
        return get$2(toggleState);
      });
    };

    Plugin();

})();
