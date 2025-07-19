/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    var global$5 = tinymce.util.Tools.resolve('tinymce.PluginManager');

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
    const eq = t => a => t === a;
    const isString = isType('string');
    const isObject = isType('object');
    const isArray = isType('array');
    const isNull = eq(null);
    const isBoolean = isSimpleType('boolean');
    const isNullable = a => a === null || a === undefined;
    const isNonNullable = a => !isNullable(a);
    const isFunction = isSimpleType('function');
    const isArrayOf = (value, pred) => {
      if (isArray(value)) {
        for (let i = 0, len = value.length; i < len; ++i) {
          if (!pred(value[i])) {
            return false;
          }
        }
        return true;
      }
      return false;
    };

    const noop = () => {
    };
    const constant = value => {
      return () => {
        return value;
      };
    };
    const tripleEquals = (a, b) => {
      return a === b;
    };

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

    const nativeIndexOf = Array.prototype.indexOf;
    const nativePush = Array.prototype.push;
    const rawIndexOf = (ts, t) => nativeIndexOf.call(ts, t);
    const contains = (xs, x) => rawIndexOf(xs, x) > -1;
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
    const foldl = (xs, f, acc) => {
      each$1(xs, (x, i) => {
        acc = f(acc, x, i);
      });
      return acc;
    };
    const flatten = xs => {
      const r = [];
      for (let i = 0, len = xs.length; i < len; ++i) {
        if (!isArray(xs[i])) {
          throw new Error('Arr.flatten item ' + i + ' was not an array, input: ' + xs);
        }
        nativePush.apply(r, xs[i]);
      }
      return r;
    };
    const bind = (xs, f) => flatten(map(xs, f));
    const findMap = (arr, f) => {
      for (let i = 0; i < arr.length; i++) {
        const r = f(arr[i], i);
        if (r.isSome()) {
          return r;
        }
      }
      return Optional.none();
    };

    const is = (lhs, rhs, comparator = tripleEquals) => lhs.exists(left => comparator(left, rhs));
    const cat = arr => {
      const r = [];
      const push = x => {
        r.push(x);
      };
      for (let i = 0; i < arr.length; i++) {
        arr[i].each(push);
      }
      return r;
    };
    const someIf = (b, a) => b ? Optional.some(a) : Optional.none();

    const option = name => editor => editor.options.get(name);
    const register$1 = editor => {
      const registerOption = editor.options.register;
      registerOption('link_assume_external_targets', {
        processor: value => {
          const valid = isString(value) || isBoolean(value);
          if (valid) {
            if (value === true) {
              return {
                value: 1,
                valid
              };
            } else if (value === 'http' || value === 'https') {
              return {
                value,
                valid
              };
            } else {
              return {
                value: 0,
                valid
              };
            }
          } else {
            return {
              valid: false,
              message: 'Must be a string or a boolean.'
            };
          }
        },
        default: false
      });
      registerOption('link_context_toolbar', {
        processor: 'boolean',
        default: false
      });
      registerOption('link_list', { processor: value => isString(value) || isFunction(value) || isArrayOf(value, isObject) });
      registerOption('link_default_target', { processor: 'string' });
      registerOption('link_default_protocol', {
        processor: 'string',
        default: 'https'
      });
      registerOption('link_target_list', {
        processor: value => isBoolean(value) || isArrayOf(value, isObject),
        default: true
      });
      registerOption('link_rel_list', {
        processor: 'object[]',
        default: []
      });
      registerOption('link_class_list', {
        processor: 'object[]',
        default: []
      });
      registerOption('link_title', {
        processor: 'boolean',
        default: true
      });
      registerOption('allow_unsafe_link_target', {
        processor: 'boolean',
        default: false
      });
      registerOption('link_quicklink', {
        processor: 'boolean',
        default: false
      });
    };
    const assumeExternalTargets = option('link_assume_external_targets');
    const hasContextToolbar = option('link_context_toolbar');
    const getLinkList = option('link_list');
    const getDefaultLinkTarget = option('link_default_target');
    const getDefaultLinkProtocol = option('link_default_protocol');
    const getTargetList = option('link_target_list');
    const getRelList = option('link_rel_list');
    const getLinkClassList = option('link_class_list');
    const shouldShowLinkTitle = option('link_title');
    const allowUnsafeLinkTarget = option('allow_unsafe_link_target');
    const useQuickLink = option('link_quicklink');

    var global$4 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    const getValue = item => isString(item.value) ? item.value : '';
    const getText = item => {
      if (isString(item.text)) {
        return item.text;
      } else if (isString(item.title)) {
        return item.title;
      } else {
        return '';
      }
    };
    const sanitizeList = (list, extractValue) => {
      const out = [];
      global$4.each(list, item => {
        const text = getText(item);
        if (item.menu !== undefined) {
          const items = sanitizeList(item.menu, extractValue);
          out.push({
            text,
            items
          });
        } else {
          const value = extractValue(item);
          out.push({
            text,
            value
          });
        }
      });
      return out;
    };
    const sanitizeWith = (extracter = getValue) => list => Optional.from(list).map(list => sanitizeList(list, extracter));
    const sanitize = list => sanitizeWith(getValue)(list);
    const createUi = (name, label) => items => ({
      name,
      type: 'listbox',
      label,
      items
    });
    const ListOptions = {
      sanitize,
      sanitizeWith,
      createUi,
      getValue
    };

    const keys = Object.keys;
    const hasOwnProperty = Object.hasOwnProperty;
    const each = (obj, f) => {
      const props = keys(obj);
      for (let k = 0, len = props.length; k < len; k++) {
        const i = props[k];
        const x = obj[i];
        f(x, i);
      }
    };
    const objAcc = r => (x, i) => {
      r[i] = x;
    };
    const internalFilter = (obj, pred, onTrue, onFalse) => {
      each(obj, (x, i) => {
        (pred(x, i) ? onTrue : onFalse)(x, i);
      });
    };
    const filter = (obj, pred) => {
      const t = {};
      internalFilter(obj, pred, objAcc(t), noop);
      return t;
    };
    const has = (obj, key) => hasOwnProperty.call(obj, key);
    const hasNonNullableKey = (obj, key) => has(obj, key) && obj[key] !== undefined && obj[key] !== null;

    var global$3 = tinymce.util.Tools.resolve('tinymce.dom.TreeWalker');

    var global$2 = tinymce.util.Tools.resolve('tinymce.util.URI');

    const isAnchor = elm => isNonNullable(elm) && elm.nodeName.toLowerCase() === 'a';
    const isLink = elm => isAnchor(elm) && !!getHref(elm);
    const collectNodesInRange = (rng, predicate) => {
      if (rng.collapsed) {
        return [];
      } else {
        const contents = rng.cloneContents();
        const firstChild = contents.firstChild;
        const walker = new global$3(firstChild, contents);
        const elements = [];
        let current = firstChild;
        do {
          if (predicate(current)) {
            elements.push(current);
          }
        } while (current = walker.next());
        return elements;
      }
    };
    const hasProtocol = url => /^\w+:/i.test(url);
    const getHref = elm => {
      var _a, _b;
      return (_b = (_a = elm.getAttribute('data-mce-href')) !== null && _a !== void 0 ? _a : elm.getAttribute('href')) !== null && _b !== void 0 ? _b : '';
    };
    const applyRelTargetRules = (rel, isUnsafe) => {
      const rules = ['noopener'];
      const rels = rel ? rel.split(/\s+/) : [];
      const toString = rels => global$4.trim(rels.sort().join(' '));
      const addTargetRules = rels => {
        rels = removeTargetRules(rels);
        return rels.length > 0 ? rels.concat(rules) : rules;
      };
      const removeTargetRules = rels => rels.filter(val => global$4.inArray(rules, val) === -1);
      const newRels = isUnsafe ? addTargetRules(rels) : removeTargetRules(rels);
      return newRels.length > 0 ? toString(newRels) : '';
    };
    const trimCaretContainers = text => text.replace(/\uFEFF/g, '');
    const getAnchorElement = (editor, selectedElm) => {
      selectedElm = selectedElm || editor.selection.getNode();
      if (isImageFigure(selectedElm)) {
        return Optional.from(editor.dom.select('a[href]', selectedElm)[0]);
      } else {
        return Optional.from(editor.dom.getParent(selectedElm, 'a[href]'));
      }
    };
    const isInAnchor = (editor, selectedElm) => getAnchorElement(editor, selectedElm).isSome();
    const getAnchorText = (selection, anchorElm) => {
      const text = anchorElm.fold(() => selection.getContent({ format: 'text' }), anchorElm => anchorElm.innerText || anchorElm.textContent || '');
      return trimCaretContainers(text);
    };
    const hasLinks = elements => global$4.grep(elements, isLink).length > 0;
    const hasLinksInSelection = rng => collectNodesInRange(rng, isLink).length > 0;
    const isOnlyTextSelected = editor => {
      const inlineTextElements = editor.schema.getTextInlineElements();
      const isElement = elm => elm.nodeType === 1 && !isAnchor(elm) && !has(inlineTextElements, elm.nodeName.toLowerCase());
      const isInBlockAnchor = getAnchorElement(editor).exists(anchor => anchor.hasAttribute('data-mce-block'));
      if (isInBlockAnchor) {
        return false;
      }
      const rng = editor.selection.getRng();
      if (!rng.collapsed) {
        const elements = collectNodesInRange(rng, isElement);
        return elements.length === 0;
      } else {
        return true;
      }
    };
    const isImageFigure = elm => isNonNullable(elm) && elm.nodeName === 'FIGURE' && /\bimage\b/i.test(elm.className);
    const getLinkAttrs = data => {
      const attrs = [
        'title',
        'rel',
        'class',
        'target'
      ];
      return foldl(attrs, (acc, key) => {
        data[key].each(value => {
          acc[key] = value.length > 0 ? value : null;
        });
        return acc;
      }, { href: data.href });
    };
    const handleExternalTargets = (href, assumeExternalTargets) => {
      if ((assumeExternalTargets === 'http' || assumeExternalTargets === 'https') && !hasProtocol(href)) {
        return assumeExternalTargets + '://' + href;
      }
      return href;
    };
    const applyLinkOverrides = (editor, linkAttrs) => {
      const newLinkAttrs = { ...linkAttrs };
      if (getRelList(editor).length === 0 && !allowUnsafeLinkTarget(editor)) {
        const newRel = applyRelTargetRules(newLinkAttrs.rel, newLinkAttrs.target === '_blank');
        newLinkAttrs.rel = newRel ? newRel : null;
      }
      if (Optional.from(newLinkAttrs.target).isNone() && getTargetList(editor) === false) {
        newLinkAttrs.target = getDefaultLinkTarget(editor);
      }
      newLinkAttrs.href = handleExternalTargets(newLinkAttrs.href, assumeExternalTargets(editor));
      return newLinkAttrs;
    };
    const updateLink = (editor, anchorElm, text, linkAttrs) => {
      text.each(text => {
        if (has(anchorElm, 'innerText')) {
          anchorElm.innerText = text;
        } else {
          anchorElm.textContent = text;
        }
      });
      editor.dom.setAttribs(anchorElm, linkAttrs);
      editor.selection.select(anchorElm);
    };
    const createLink = (editor, selectedElm, text, linkAttrs) => {
      const dom = editor.dom;
      if (isImageFigure(selectedElm)) {
        linkImageFigure(dom, selectedElm, linkAttrs);
      } else {
        text.fold(() => {
          editor.execCommand('mceInsertLink', false, linkAttrs);
        }, text => {
          editor.insertContent(dom.createHTML('a', linkAttrs, dom.encode(text)));
        });
      }
    };
    const linkDomMutation = (editor, attachState, data) => {
      const selectedElm = editor.selection.getNode();
      const anchorElm = getAnchorElement(editor, selectedElm);
      const linkAttrs = applyLinkOverrides(editor, getLinkAttrs(data));
      editor.undoManager.transact(() => {
        if (data.href === attachState.href) {
          attachState.attach();
        }
        anchorElm.fold(() => {
          createLink(editor, selectedElm, data.text, linkAttrs);
        }, elm => {
          editor.focus();
          updateLink(editor, elm, data.text, linkAttrs);
        });
      });
    };
    const unlinkSelection = editor => {
      const dom = editor.dom, selection = editor.selection;
      const bookmark = selection.getBookmark();
      const rng = selection.getRng().cloneRange();
      const startAnchorElm = dom.getParent(rng.startContainer, 'a[href]', editor.getBody());
      const endAnchorElm = dom.getParent(rng.endContainer, 'a[href]', editor.getBody());
      if (startAnchorElm) {
        rng.setStartBefore(startAnchorElm);
      }
      if (endAnchorElm) {
        rng.setEndAfter(endAnchorElm);
      }
      selection.setRng(rng);
      editor.execCommand('unlink');
      selection.moveToBookmark(bookmark);
    };
    const unlinkDomMutation = editor => {
      editor.undoManager.transact(() => {
        const node = editor.selection.getNode();
        if (isImageFigure(node)) {
          unlinkImageFigure(editor, node);
        } else {
          unlinkSelection(editor);
        }
        editor.focus();
      });
    };
    const unwrapOptions = data => {
      const {
        class: cls,
        href,
        rel,
        target,
        text,
        title
      } = data;
      return filter({
        class: cls.getOrNull(),
        href,
        rel: rel.getOrNull(),
        target: target.getOrNull(),
        text: text.getOrNull(),
        title: title.getOrNull()
      }, (v, _k) => isNull(v) === false);
    };
    const sanitizeData = (editor, data) => {
      const getOption = editor.options.get;
      const uriOptions = {
        allow_html_data_urls: getOption('allow_html_data_urls'),
        allow_script_urls: getOption('allow_script_urls'),
        allow_svg_data_urls: getOption('allow_svg_data_urls')
      };
      const href = data.href;
      return {
        ...data,
        href: global$2.isDomSafe(href, 'a', uriOptions) ? href : ''
      };
    };
    const link = (editor, attachState, data) => {
      const sanitizedData = sanitizeData(editor, data);
      editor.hasPlugin('rtc', true) ? editor.execCommand('createlink', false, unwrapOptions(sanitizedData)) : linkDomMutation(editor, attachState, sanitizedData);
    };
    const unlink = editor => {
      editor.hasPlugin('rtc', true) ? editor.execCommand('unlink') : unlinkDomMutation(editor);
    };
    const unlinkImageFigure = (editor, fig) => {
      var _a;
      const img = editor.dom.select('img', fig)[0];
      if (img) {
        const a = editor.dom.getParents(img, 'a[href]', fig)[0];
        if (a) {
          (_a = a.parentNode) === null || _a === void 0 ? void 0 : _a.insertBefore(img, a);
          editor.dom.remove(a);
        }
      }
    };
    const linkImageFigure = (dom, fig, attrs) => {
      var _a;
      const img = dom.select('img', fig)[0];
      if (img) {
        const a = dom.create('a', attrs);
        (_a = img.parentNode) === null || _a === void 0 ? void 0 : _a.insertBefore(a, img);
        a.appendChild(img);
      }
    };

    const isListGroup = item => hasNonNullableKey(item, 'items');
    const findTextByValue = (value, catalog) => findMap(catalog, item => {
      if (isListGroup(item)) {
        return findTextByValue(value, item.items);
      } else {
        return someIf(item.value === value, item);
      }
    });
    const getDelta = (persistentText, fieldName, catalog, data) => {
      const value = data[fieldName];
      const hasPersistentText = persistentText.length > 0;
      return value !== undefined ? findTextByValue(value, catalog).map(i => ({
        url: {
          value: i.value,
          meta: {
            text: hasPersistentText ? persistentText : i.text,
            attach: noop
          }
        },
        text: hasPersistentText ? persistentText : i.text
      })) : Optional.none();
    };
    const findCatalog = (catalogs, fieldName) => {
      if (fieldName === 'link') {
        return catalogs.link;
      } else if (fieldName === 'anchor') {
        return catalogs.anchor;
      } else {
        return Optional.none();
      }
    };
    const init = (initialData, linkCatalog) => {
      const persistentData = {
        text: initialData.text,
        title: initialData.title
      };
      const getTitleFromUrlChange = url => {
        var _a;
        return someIf(persistentData.title.length <= 0, Optional.from((_a = url.meta) === null || _a === void 0 ? void 0 : _a.title).getOr(''));
      };
      const getTextFromUrlChange = url => {
        var _a;
        return someIf(persistentData.text.length <= 0, Optional.from((_a = url.meta) === null || _a === void 0 ? void 0 : _a.text).getOr(url.value));
      };
      const onUrlChange = data => {
        const text = getTextFromUrlChange(data.url);
        const title = getTitleFromUrlChange(data.url);
        if (text.isSome() || title.isSome()) {
          return Optional.some({
            ...text.map(text => ({ text })).getOr({}),
            ...title.map(title => ({ title })).getOr({})
          });
        } else {
          return Optional.none();
        }
      };
      const onCatalogChange = (data, change) => {
        const catalog = findCatalog(linkCatalog, change).getOr([]);
        return getDelta(persistentData.text, change, catalog, data);
      };
      const onChange = (getData, change) => {
        const name = change.name;
        if (name === 'url') {
          return onUrlChange(getData());
        } else if (contains([
            'anchor',
            'link'
          ], name)) {
          return onCatalogChange(getData(), name);
        } else if (name === 'text' || name === 'title') {
          persistentData[name] = getData()[name];
          return Optional.none();
        } else {
          return Optional.none();
        }
      };
      return { onChange };
    };
    const DialogChanges = {
      init,
      getDelta
    };

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.Delay');

    const delayedConfirm = (editor, message, callback) => {
      const rng = editor.selection.getRng();
      global$1.setEditorTimeout(editor, () => {
        editor.windowManager.confirm(message, state => {
          editor.selection.setRng(rng);
          callback(state);
        });
      });
    };
    const tryEmailTransform = data => {
      const url = data.href;
      const suggestMailTo = url.indexOf('@') > 0 && url.indexOf('/') === -1 && url.indexOf('mailto:') === -1;
      return suggestMailTo ? Optional.some({
        message: 'The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?',
        preprocess: oldData => ({
          ...oldData,
          href: 'mailto:' + url
        })
      }) : Optional.none();
    };
    const tryProtocolTransform = (assumeExternalTargets, defaultLinkProtocol) => data => {
      const url = data.href;
      const suggestProtocol = assumeExternalTargets === 1 && !hasProtocol(url) || assumeExternalTargets === 0 && /^\s*www(\.|\d\.)/i.test(url);
      return suggestProtocol ? Optional.some({
        message: `The URL you entered seems to be an external link. Do you want to add the required ${ defaultLinkProtocol }:// prefix?`,
        preprocess: oldData => ({
          ...oldData,
          href: defaultLinkProtocol + '://' + url
        })
      }) : Optional.none();
    };
    const preprocess = (editor, data) => findMap([
      tryEmailTransform,
      tryProtocolTransform(assumeExternalTargets(editor), getDefaultLinkProtocol(editor))
    ], f => f(data)).fold(() => Promise.resolve(data), transform => new Promise(callback => {
      delayedConfirm(editor, transform.message, state => {
        callback(state ? transform.preprocess(data) : data);
      });
    }));
    const DialogConfirms = { preprocess };

    const getAnchors = editor => {
      const anchorNodes = editor.dom.select('a:not([href])');
      const anchors = bind(anchorNodes, anchor => {
        const id = anchor.name || anchor.id;
        return id ? [{
            text: id,
            value: '#' + id
          }] : [];
      });
      return anchors.length > 0 ? Optional.some([{
          text: 'None',
          value: ''
        }].concat(anchors)) : Optional.none();
    };
    const AnchorListOptions = { getAnchors };

    const getClasses = editor => {
      const list = getLinkClassList(editor);
      if (list.length > 0) {
        return ListOptions.sanitize(list);
      }
      return Optional.none();
    };
    const ClassListOptions = { getClasses };

    const parseJson = text => {
      try {
        return Optional.some(JSON.parse(text));
      } catch (err) {
        return Optional.none();
      }
    };
    const getLinks = editor => {
      const extractor = item => editor.convertURL(item.value || item.url || '', 'href');
      const linkList = getLinkList(editor);
      return new Promise(resolve => {
        if (isString(linkList)) {
          fetch(linkList).then(res => res.ok ? res.text().then(parseJson) : Promise.reject()).then(resolve, () => resolve(Optional.none()));
        } else if (isFunction(linkList)) {
          linkList(output => resolve(Optional.some(output)));
        } else {
          resolve(Optional.from(linkList));
        }
      }).then(optItems => optItems.bind(ListOptions.sanitizeWith(extractor)).map(items => {
        if (items.length > 0) {
          const noneItem = [{
              text: 'None',
              value: ''
            }];
          return noneItem.concat(items);
        } else {
          return items;
        }
      }));
    };
    const LinkListOptions = { getLinks };

    const getRels = (editor, initialTarget) => {
      const list = getRelList(editor);
      if (list.length > 0) {
        const isTargetBlank = is(initialTarget, '_blank');
        const enforceSafe = allowUnsafeLinkTarget(editor) === false;
        const safeRelExtractor = item => applyRelTargetRules(ListOptions.getValue(item), isTargetBlank);
        const sanitizer = enforceSafe ? ListOptions.sanitizeWith(safeRelExtractor) : ListOptions.sanitize;
        return sanitizer(list);
      }
      return Optional.none();
    };
    const RelOptions = { getRels };

    const fallbacks = [
      {
        text: 'Current window',
        value: ''
      },
      {
        text: 'New window',
        value: '_blank'
      }
    ];
    const getTargets = editor => {
      const list = getTargetList(editor);
      if (isArray(list)) {
        return ListOptions.sanitize(list).orThunk(() => Optional.some(fallbacks));
      } else if (list === false) {
        return Optional.none();
      }
      return Optional.some(fallbacks);
    };
    const TargetOptions = { getTargets };

    const nonEmptyAttr = (dom, elem, name) => {
      const val = dom.getAttrib(elem, name);
      return val !== null && val.length > 0 ? Optional.some(val) : Optional.none();
    };
    const extractFromAnchor = (editor, anchor) => {
      const dom = editor.dom;
      const onlyText = isOnlyTextSelected(editor);
      const text = onlyText ? Optional.some(getAnchorText(editor.selection, anchor)) : Optional.none();
      const url = anchor.bind(anchorElm => Optional.from(dom.getAttrib(anchorElm, 'href')));
      const target = anchor.bind(anchorElm => Optional.from(dom.getAttrib(anchorElm, 'target')));
      const rel = anchor.bind(anchorElm => nonEmptyAttr(dom, anchorElm, 'rel'));
      const linkClass = anchor.bind(anchorElm => nonEmptyAttr(dom, anchorElm, 'class'));
      const title = anchor.bind(anchorElm => nonEmptyAttr(dom, anchorElm, 'title'));
      return {
        url,
        text,
        title,
        target,
        rel,
        linkClass
      };
    };
    const collect = (editor, linkNode) => LinkListOptions.getLinks(editor).then(links => {
      const anchor = extractFromAnchor(editor, linkNode);
      return {
        anchor,
        catalogs: {
          targets: TargetOptions.getTargets(editor),
          rels: RelOptions.getRels(editor, anchor.target),
          classes: ClassListOptions.getClasses(editor),
          anchor: AnchorListOptions.getAnchors(editor),
          link: links
        },
        optNode: linkNode,
        flags: { titleEnabled: shouldShowLinkTitle(editor) }
      };
    });
    const DialogInfo = { collect };

    const handleSubmit = (editor, info) => api => {
      const data = api.getData();
      if (!data.url.value) {
        unlink(editor);
        api.close();
        return;
      }
      const getChangedValue = key => Optional.from(data[key]).filter(value => !is(info.anchor[key], value));
      const changedData = {
        href: data.url.value,
        text: getChangedValue('text'),
        target: getChangedValue('target'),
        rel: getChangedValue('rel'),
        class: getChangedValue('linkClass'),
        title: getChangedValue('title')
      };
      const attachState = {
        href: data.url.value,
        attach: data.url.meta !== undefined && data.url.meta.attach ? data.url.meta.attach : noop
      };
      DialogConfirms.preprocess(editor, changedData).then(pData => {
        link(editor, attachState, pData);
      });
      api.close();
    };
    const collectData = editor => {
      const anchorNode = getAnchorElement(editor);
      return DialogInfo.collect(editor, anchorNode);
    };
    const getInitialData = (info, defaultTarget) => {
      const anchor = info.anchor;
      const url = anchor.url.getOr('');
      return {
        url: {
          value: url,
          meta: { original: { value: url } }
        },
        text: anchor.text.getOr(''),
        title: anchor.title.getOr(''),
        anchor: url,
        link: url,
        rel: anchor.rel.getOr(''),
        target: anchor.target.or(defaultTarget).getOr(''),
        linkClass: anchor.linkClass.getOr('')
      };
    };
    const makeDialog = (settings, onSubmit, editor) => {
      const urlInput = [{
          name: 'url',
          type: 'urlinput',
          filetype: 'file',
          label: 'URL'
        }];
      const displayText = settings.anchor.text.map(() => ({
        name: 'text',
        type: 'input',
        label: 'Text to display'
      })).toArray();
      const titleText = settings.flags.titleEnabled ? [{
          name: 'title',
          type: 'input',
          label: 'Title'
        }] : [];
      const defaultTarget = Optional.from(getDefaultLinkTarget(editor));
      const initialData = getInitialData(settings, defaultTarget);
      const catalogs = settings.catalogs;
      const dialogDelta = DialogChanges.init(initialData, catalogs);
      const body = {
        type: 'panel',
        items: flatten([
          urlInput,
          displayText,
          titleText,
          cat([
            catalogs.anchor.map(ListOptions.createUi('anchor', 'Anchors')),
            catalogs.rels.map(ListOptions.createUi('rel', 'Rel')),
            catalogs.targets.map(ListOptions.createUi('target', 'Open link in...')),
            catalogs.link.map(ListOptions.createUi('link', 'Link list')),
            catalogs.classes.map(ListOptions.createUi('linkClass', 'Class'))
          ])
        ])
      };
      return {
        title: 'Insert/Edit Link',
        size: 'normal',
        body,
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
        initialData,
        onChange: (api, {name}) => {
          dialogDelta.onChange(api.getData, { name }).each(newData => {
            api.setData(newData);
          });
        },
        onSubmit
      };
    };
    const open$1 = editor => {
      const data = collectData(editor);
      data.then(info => {
        const onSubmit = handleSubmit(editor, info);
        return makeDialog(info, onSubmit, editor);
      }).then(spec => {
        editor.windowManager.open(spec);
      });
    };

    const register = editor => {
      editor.addCommand('mceLink', (_ui, value) => {
        if ((value === null || value === void 0 ? void 0 : value.dialog) === true || !useQuickLink(editor)) {
          open$1(editor);
        } else {
          editor.dispatch('contexttoolbar-show', { toolbarKey: 'quicklink' });
        }
      });
    };

    var global = tinymce.util.Tools.resolve('tinymce.util.VK');

    const appendClickRemove = (link, evt) => {
      document.body.appendChild(link);
      link.dispatchEvent(evt);
      document.body.removeChild(link);
    };
    const open = url => {
      const link = document.createElement('a');
      link.target = '_blank';
      link.href = url;
      link.rel = 'noreferrer noopener';
      const evt = document.createEvent('MouseEvents');
      evt.initMouseEvent('click', true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
      appendClickRemove(link, evt);
    };

    const getLink = (editor, elm) => editor.dom.getParent(elm, 'a[href]');
    const getSelectedLink = editor => getLink(editor, editor.selection.getStart());
    const hasOnlyAltModifier = e => {
      return e.altKey === true && e.shiftKey === false && e.ctrlKey === false && e.metaKey === false;
    };
    const gotoLink = (editor, a) => {
      if (a) {
        const href = getHref(a);
        if (/^#/.test(href)) {
          const targetEl = editor.dom.select(href);
          if (targetEl.length) {
            editor.selection.scrollIntoView(targetEl[0], true);
          }
        } else {
          open(a.href);
        }
      }
    };
    const openDialog = editor => () => {
      editor.execCommand('mceLink', false, { dialog: true });
    };
    const gotoSelectedLink = editor => () => {
      gotoLink(editor, getSelectedLink(editor));
    };
    const setupGotoLinks = editor => {
      editor.on('click', e => {
        const link = getLink(editor, e.target);
        if (link && global.metaKeyPressed(e)) {
          e.preventDefault();
          gotoLink(editor, link);
        }
      });
      editor.on('keydown', e => {
        if (!e.isDefaultPrevented() && e.keyCode === 13 && hasOnlyAltModifier(e)) {
          const link = getSelectedLink(editor);
          if (link) {
            e.preventDefault();
            gotoLink(editor, link);
          }
        }
      });
    };
    const toggleState = (editor, toggler) => {
      editor.on('NodeChange', toggler);
      return () => editor.off('NodeChange', toggler);
    };
    const toggleActiveState = editor => api => {
      const updateState = () => api.setActive(!editor.mode.isReadOnly() && isInAnchor(editor, editor.selection.getNode()));
      updateState();
      return toggleState(editor, updateState);
    };
    const toggleEnabledState = editor => api => {
      const updateState = () => api.setEnabled(isInAnchor(editor, editor.selection.getNode()));
      updateState();
      return toggleState(editor, updateState);
    };
    const toggleUnlinkState = editor => api => {
      const hasLinks$1 = parents => hasLinks(parents) || hasLinksInSelection(editor.selection.getRng());
      const parents = editor.dom.getParents(editor.selection.getStart());
      api.setEnabled(hasLinks$1(parents));
      return toggleState(editor, e => api.setEnabled(hasLinks$1(e.parents)));
    };

    const setup = editor => {
      editor.addShortcut('Meta+K', '', () => {
        editor.execCommand('mceLink');
      });
    };

    const setupButtons = editor => {
      editor.ui.registry.addToggleButton('link', {
        icon: 'link',
        tooltip: 'Insert/edit link',
        onAction: openDialog(editor),
        onSetup: toggleActiveState(editor)
      });
      editor.ui.registry.addButton('openlink', {
        icon: 'new-tab',
        tooltip: 'Open link',
        onAction: gotoSelectedLink(editor),
        onSetup: toggleEnabledState(editor)
      });
      editor.ui.registry.addButton('unlink', {
        icon: 'unlink',
        tooltip: 'Remove link',
        onAction: () => unlink(editor),
        onSetup: toggleUnlinkState(editor)
      });
    };
    const setupMenuItems = editor => {
      editor.ui.registry.addMenuItem('openlink', {
        text: 'Open link',
        icon: 'new-tab',
        onAction: gotoSelectedLink(editor),
        onSetup: toggleEnabledState(editor)
      });
      editor.ui.registry.addMenuItem('link', {
        icon: 'link',
        text: 'Link...',
        shortcut: 'Meta+K',
        onAction: openDialog(editor)
      });
      editor.ui.registry.addMenuItem('unlink', {
        icon: 'unlink',
        text: 'Remove link',
        onAction: () => unlink(editor),
        onSetup: toggleUnlinkState(editor)
      });
    };
    const setupContextMenu = editor => {
      const inLink = 'link unlink openlink';
      const noLink = 'link';
      editor.ui.registry.addContextMenu('link', { update: element => hasLinks(editor.dom.getParents(element, 'a')) ? inLink : noLink });
    };
    const setupContextToolbars = editor => {
      const collapseSelectionToEnd = editor => {
        editor.selection.collapse(false);
      };
      const onSetupLink = buttonApi => {
        const node = editor.selection.getNode();
        buttonApi.setEnabled(isInAnchor(editor, node));
        return noop;
      };
      const getLinkText = value => {
        const anchor = getAnchorElement(editor);
        const onlyText = isOnlyTextSelected(editor);
        if (anchor.isNone() && onlyText) {
          const text = getAnchorText(editor.selection, anchor);
          return Optional.some(text.length > 0 ? text : value);
        } else {
          return Optional.none();
        }
      };
      editor.ui.registry.addContextForm('quicklink', {
        launch: {
          type: 'contextformtogglebutton',
          icon: 'link',
          tooltip: 'Link',
          onSetup: toggleActiveState(editor)
        },
        label: 'Link',
        predicate: node => hasContextToolbar(editor) && isInAnchor(editor, node),
        initValue: () => {
          const elm = getAnchorElement(editor);
          return elm.fold(constant(''), getHref);
        },
        commands: [
          {
            type: 'contextformtogglebutton',
            icon: 'link',
            tooltip: 'Link',
            primary: true,
            onSetup: buttonApi => {
              const node = editor.selection.getNode();
              buttonApi.setActive(isInAnchor(editor, node));
              return toggleActiveState(editor)(buttonApi);
            },
            onAction: formApi => {
              const value = formApi.getValue();
              const text = getLinkText(value);
              const attachState = {
                href: value,
                attach: noop
              };
              link(editor, attachState, {
                href: value,
                text,
                title: Optional.none(),
                rel: Optional.none(),
                target: Optional.none(),
                class: Optional.none()
              });
              collapseSelectionToEnd(editor);
              formApi.hide();
            }
          },
          {
            type: 'contextformbutton',
            icon: 'unlink',
            tooltip: 'Remove link',
            onSetup: onSetupLink,
            onAction: formApi => {
              unlink(editor);
              formApi.hide();
            }
          },
          {
            type: 'contextformbutton',
            icon: 'new-tab',
            tooltip: 'Open link',
            onSetup: onSetupLink,
            onAction: formApi => {
              gotoSelectedLink(editor)();
              formApi.hide();
            }
          }
        ]
      });
    };

    var Plugin = () => {
      global$5.add('link', editor => {
        register$1(editor);
        setupButtons(editor);
        setupMenuItems(editor);
        setupContextMenu(editor);
        setupContextToolbars(editor);
        setupGotoLinks(editor);
        register(editor);
        setup(editor);
      });
    };

    Plugin();

})();
