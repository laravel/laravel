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

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.VK');

    var typeOf = function (x) {
      var t = typeof x;
      if (x === null) {
        return 'null';
      } else if (t === 'object' && (Array.prototype.isPrototypeOf(x) || x.constructor && x.constructor.name === 'Array')) {
        return 'array';
      } else if (t === 'object' && (String.prototype.isPrototypeOf(x) || x.constructor && x.constructor.name === 'String')) {
        return 'string';
      } else {
        return t;
      }
    };
    var isType = function (type) {
      return function (value) {
        return typeOf(value) === type;
      };
    };
    var isSimpleType = function (type) {
      return function (value) {
        return typeof value === type;
      };
    };
    var eq = function (t) {
      return function (a) {
        return t === a;
      };
    };
    var isString = isType('string');
    var isArray = isType('array');
    var isNull = eq(null);
    var isBoolean = isSimpleType('boolean');
    var isFunction = isSimpleType('function');

    var assumeExternalTargets = function (editor) {
      var externalTargets = editor.getParam('link_assume_external_targets', false);
      if (isBoolean(externalTargets) && externalTargets) {
        return 1;
      } else if (isString(externalTargets) && (externalTargets === 'http' || externalTargets === 'https')) {
        return externalTargets;
      }
      return 0;
    };
    var hasContextToolbar = function (editor) {
      return editor.getParam('link_context_toolbar', false, 'boolean');
    };
    var getLinkList = function (editor) {
      return editor.getParam('link_list');
    };
    var getDefaultLinkTarget = function (editor) {
      return editor.getParam('default_link_target');
    };
    var getTargetList = function (editor) {
      return editor.getParam('target_list', true);
    };
    var getRelList = function (editor) {
      return editor.getParam('rel_list', [], 'array');
    };
    var getLinkClassList = function (editor) {
      return editor.getParam('link_class_list', [], 'array');
    };
    var shouldShowLinkTitle = function (editor) {
      return editor.getParam('link_title', true, 'boolean');
    };
    var allowUnsafeLinkTarget = function (editor) {
      return editor.getParam('allow_unsafe_link_target', false, 'boolean');
    };
    var useQuickLink = function (editor) {
      return editor.getParam('link_quicklink', false, 'boolean');
    };
    var getDefaultLinkProtocol = function (editor) {
      return editor.getParam('link_default_protocol', 'http', 'string');
    };

    var noop = function () {
    };
    var constant = function (value) {
      return function () {
        return value;
      };
    };
    var never = constant(false);
    var always = constant(true);

    var none = function () {
      return NONE;
    };
    var NONE = function () {
      var eq = function (o) {
        return o.isNone();
      };
      var call = function (thunk) {
        return thunk();
      };
      var id = function (n) {
        return n;
      };
      var me = {
        fold: function (n, _s) {
          return n();
        },
        is: never,
        isSome: never,
        isNone: always,
        getOr: id,
        getOrThunk: call,
        getOrDie: function (msg) {
          throw new Error(msg || 'error: getOrDie called on none.');
        },
        getOrNull: constant(null),
        getOrUndefined: constant(undefined),
        or: id,
        orThunk: call,
        map: none,
        each: noop,
        bind: none,
        exists: never,
        forall: always,
        filter: none,
        equals: eq,
        equals_: eq,
        toArray: function () {
          return [];
        },
        toString: constant('none()')
      };
      return me;
    }();
    var some = function (a) {
      var constant_a = constant(a);
      var self = function () {
        return me;
      };
      var bind = function (f) {
        return f(a);
      };
      var me = {
        fold: function (n, s) {
          return s(a);
        },
        is: function (v) {
          return a === v;
        },
        isSome: always,
        isNone: never,
        getOr: constant_a,
        getOrThunk: constant_a,
        getOrDie: constant_a,
        getOrNull: constant_a,
        getOrUndefined: constant_a,
        or: self,
        orThunk: self,
        map: function (f) {
          return some(f(a));
        },
        each: function (f) {
          f(a);
        },
        bind: bind,
        exists: bind,
        forall: bind,
        filter: function (f) {
          return f(a) ? me : NONE;
        },
        toArray: function () {
          return [a];
        },
        toString: function () {
          return 'some(' + a + ')';
        },
        equals: function (o) {
          return o.is(a);
        },
        equals_: function (o, elementEq) {
          return o.fold(never, function (b) {
            return elementEq(a, b);
          });
        }
      };
      return me;
    };
    var from = function (value) {
      return value === null || value === undefined ? NONE : some(value);
    };
    var Optional = {
      some: some,
      none: none,
      from: from
    };

    var nativeIndexOf = Array.prototype.indexOf;
    var nativePush = Array.prototype.push;
    var rawIndexOf = function (ts, t) {
      return nativeIndexOf.call(ts, t);
    };
    var contains = function (xs, x) {
      return rawIndexOf(xs, x) > -1;
    };
    var map = function (xs, f) {
      var len = xs.length;
      var r = new Array(len);
      for (var i = 0; i < len; i++) {
        var x = xs[i];
        r[i] = f(x, i);
      }
      return r;
    };
    var each = function (xs, f) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        f(x, i);
      }
    };
    var foldl = function (xs, f, acc) {
      each(xs, function (x) {
        acc = f(acc, x);
      });
      return acc;
    };
    var flatten = function (xs) {
      var r = [];
      for (var i = 0, len = xs.length; i < len; ++i) {
        if (!isArray(xs[i])) {
          throw new Error('Arr.flatten item ' + i + ' was not an array, input: ' + xs);
        }
        nativePush.apply(r, xs[i]);
      }
      return r;
    };
    var bind = function (xs, f) {
      return flatten(map(xs, f));
    };
    var findMap = function (arr, f) {
      for (var i = 0; i < arr.length; i++) {
        var r = f(arr[i], i);
        if (r.isSome()) {
          return r;
        }
      }
      return Optional.none();
    };

    var cat = function (arr) {
      var r = [];
      var push = function (x) {
        r.push(x);
      };
      for (var i = 0; i < arr.length; i++) {
        arr[i].each(push);
      }
      return r;
    };
    var someIf = function (b, a) {
      return b ? Optional.some(a) : Optional.none();
    };

    var global$2 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    var getValue = function (item) {
      return isString(item.value) ? item.value : '';
    };
    var getText = function (item) {
      if (isString(item.text)) {
        return item.text;
      } else if (isString(item.title)) {
        return item.title;
      } else {
        return '';
      }
    };
    var sanitizeList = function (list, extractValue) {
      var out = [];
      global$2.each(list, function (item) {
        var text = getText(item);
        if (item.menu !== undefined) {
          var items = sanitizeList(item.menu, extractValue);
          out.push({
            text: text,
            items: items
          });
        } else {
          var value = extractValue(item);
          out.push({
            text: text,
            value: value
          });
        }
      });
      return out;
    };
    var sanitizeWith = function (extracter) {
      if (extracter === void 0) {
        extracter = getValue;
      }
      return function (list) {
        return Optional.from(list).map(function (list) {
          return sanitizeList(list, extracter);
        });
      };
    };
    var sanitize = function (list) {
      return sanitizeWith(getValue)(list);
    };
    var createUi = function (name, label) {
      return function (items) {
        return {
          name: name,
          type: 'listbox',
          label: label,
          items: items
        };
      };
    };
    var ListOptions = {
      sanitize: sanitize,
      sanitizeWith: sanitizeWith,
      createUi: createUi,
      getValue: getValue
    };

    var __assign = function () {
      __assign = Object.assign || function __assign(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
          s = arguments[i];
          for (var p in s)
            if (Object.prototype.hasOwnProperty.call(s, p))
              t[p] = s[p];
        }
        return t;
      };
      return __assign.apply(this, arguments);
    };

    var keys = Object.keys;
    var hasOwnProperty = Object.hasOwnProperty;
    var each$1 = function (obj, f) {
      var props = keys(obj);
      for (var k = 0, len = props.length; k < len; k++) {
        var i = props[k];
        var x = obj[i];
        f(x, i);
      }
    };
    var objAcc = function (r) {
      return function (x, i) {
        r[i] = x;
      };
    };
    var internalFilter = function (obj, pred, onTrue, onFalse) {
      var r = {};
      each$1(obj, function (x, i) {
        (pred(x, i) ? onTrue : onFalse)(x, i);
      });
      return r;
    };
    var filter = function (obj, pred) {
      var t = {};
      internalFilter(obj, pred, objAcc(t), noop);
      return t;
    };
    var has = function (obj, key) {
      return hasOwnProperty.call(obj, key);
    };
    var hasNonNullableKey = function (obj, key) {
      return has(obj, key) && obj[key] !== undefined && obj[key] !== null;
    };

    var global$3 = tinymce.util.Tools.resolve('tinymce.dom.TreeWalker');

    var isAnchor = function (elm) {
      return elm && elm.nodeName.toLowerCase() === 'a';
    };
    var isLink = function (elm) {
      return isAnchor(elm) && !!getHref(elm);
    };
    var collectNodesInRange = function (rng, predicate) {
      if (rng.collapsed) {
        return [];
      } else {
        var contents = rng.cloneContents();
        var walker = new global$3(contents.firstChild, contents);
        var elements = [];
        var current = contents.firstChild;
        do {
          if (predicate(current)) {
            elements.push(current);
          }
        } while (current = walker.next());
        return elements;
      }
    };
    var hasProtocol = function (url) {
      return /^\w+:/i.test(url);
    };
    var getHref = function (elm) {
      var href = elm.getAttribute('data-mce-href');
      return href ? href : elm.getAttribute('href');
    };
    var applyRelTargetRules = function (rel, isUnsafe) {
      var rules = ['noopener'];
      var rels = rel ? rel.split(/\s+/) : [];
      var toString = function (rels) {
        return global$2.trim(rels.sort().join(' '));
      };
      var addTargetRules = function (rels) {
        rels = removeTargetRules(rels);
        return rels.length > 0 ? rels.concat(rules) : rules;
      };
      var removeTargetRules = function (rels) {
        return rels.filter(function (val) {
          return global$2.inArray(rules, val) === -1;
        });
      };
      var newRels = isUnsafe ? addTargetRules(rels) : removeTargetRules(rels);
      return newRels.length > 0 ? toString(newRels) : '';
    };
    var trimCaretContainers = function (text) {
      return text.replace(/\uFEFF/g, '');
    };
    var getAnchorElement = function (editor, selectedElm) {
      selectedElm = selectedElm || editor.selection.getNode();
      if (isImageFigure(selectedElm)) {
        return editor.dom.select('a[href]', selectedElm)[0];
      } else {
        return editor.dom.getParent(selectedElm, 'a[href]');
      }
    };
    var getAnchorText = function (selection, anchorElm) {
      var text = anchorElm ? anchorElm.innerText || anchorElm.textContent : selection.getContent({ format: 'text' });
      return trimCaretContainers(text);
    };
    var hasLinks = function (elements) {
      return global$2.grep(elements, isLink).length > 0;
    };
    var hasLinksInSelection = function (rng) {
      return collectNodesInRange(rng, isLink).length > 0;
    };
    var isOnlyTextSelected = function (editor) {
      var inlineTextElements = editor.schema.getTextInlineElements();
      var isElement = function (elm) {
        return elm.nodeType === 1 && !isAnchor(elm) && !has(inlineTextElements, elm.nodeName.toLowerCase());
      };
      var elements = collectNodesInRange(editor.selection.getRng(), isElement);
      return elements.length === 0;
    };
    var isImageFigure = function (elm) {
      return elm && elm.nodeName === 'FIGURE' && /\bimage\b/i.test(elm.className);
    };
    var getLinkAttrs = function (data) {
      return foldl([
        'title',
        'rel',
        'class',
        'target'
      ], function (acc, key) {
        data[key].each(function (value) {
          acc[key] = value.length > 0 ? value : null;
        });
        return acc;
      }, { href: data.href });
    };
    var handleExternalTargets = function (href, assumeExternalTargets) {
      if ((assumeExternalTargets === 'http' || assumeExternalTargets === 'https') && !hasProtocol(href)) {
        return assumeExternalTargets + '://' + href;
      }
      return href;
    };
    var applyLinkOverrides = function (editor, linkAttrs) {
      var newLinkAttrs = __assign({}, linkAttrs);
      if (!(getRelList(editor).length > 0) && allowUnsafeLinkTarget(editor) === false) {
        var newRel = applyRelTargetRules(newLinkAttrs.rel, newLinkAttrs.target === '_blank');
        newLinkAttrs.rel = newRel ? newRel : null;
      }
      if (Optional.from(newLinkAttrs.target).isNone() && getTargetList(editor) === false) {
        newLinkAttrs.target = getDefaultLinkTarget(editor);
      }
      newLinkAttrs.href = handleExternalTargets(newLinkAttrs.href, assumeExternalTargets(editor));
      return newLinkAttrs;
    };
    var updateLink = function (editor, anchorElm, text, linkAttrs) {
      text.each(function (text) {
        if (anchorElm.hasOwnProperty('innerText')) {
          anchorElm.innerText = text;
        } else {
          anchorElm.textContent = text;
        }
      });
      editor.dom.setAttribs(anchorElm, linkAttrs);
      editor.selection.select(anchorElm);
    };
    var createLink = function (editor, selectedElm, text, linkAttrs) {
      if (isImageFigure(selectedElm)) {
        linkImageFigure(editor, selectedElm, linkAttrs);
      } else {
        text.fold(function () {
          editor.execCommand('mceInsertLink', false, linkAttrs);
        }, function (text) {
          editor.insertContent(editor.dom.createHTML('a', linkAttrs, editor.dom.encode(text)));
        });
      }
    };
    var linkDomMutation = function (editor, attachState, data) {
      var selectedElm = editor.selection.getNode();
      var anchorElm = getAnchorElement(editor, selectedElm);
      var linkAttrs = applyLinkOverrides(editor, getLinkAttrs(data));
      editor.undoManager.transact(function () {
        if (data.href === attachState.href) {
          attachState.attach();
        }
        if (anchorElm) {
          editor.focus();
          updateLink(editor, anchorElm, data.text, linkAttrs);
        } else {
          createLink(editor, selectedElm, data.text, linkAttrs);
        }
      });
    };
    var unlinkSelection = function (editor) {
      var dom = editor.dom, selection = editor.selection;
      var bookmark = selection.getBookmark();
      var rng = selection.getRng().cloneRange();
      var startAnchorElm = dom.getParent(rng.startContainer, 'a[href]', editor.getBody());
      var endAnchorElm = dom.getParent(rng.endContainer, 'a[href]', editor.getBody());
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
    var unlinkDomMutation = function (editor) {
      editor.undoManager.transact(function () {
        var node = editor.selection.getNode();
        if (isImageFigure(node)) {
          unlinkImageFigure(editor, node);
        } else {
          unlinkSelection(editor);
        }
        editor.focus();
      });
    };
    var unwrapOptions = function (data) {
      var cls = data.class, href = data.href, rel = data.rel, target = data.target, text = data.text, title = data.title;
      return filter({
        class: cls.getOrNull(),
        href: href,
        rel: rel.getOrNull(),
        target: target.getOrNull(),
        text: text.getOrNull(),
        title: title.getOrNull()
      }, function (v, _k) {
        return isNull(v) === false;
      });
    };
    var link = function (editor, attachState, data) {
      editor.hasPlugin('rtc', true) ? editor.execCommand('createlink', false, unwrapOptions(data)) : linkDomMutation(editor, attachState, data);
    };
    var unlink = function (editor) {
      editor.hasPlugin('rtc', true) ? editor.execCommand('unlink') : unlinkDomMutation(editor);
    };
    var unlinkImageFigure = function (editor, fig) {
      var img = editor.dom.select('img', fig)[0];
      if (img) {
        var a = editor.dom.getParents(img, 'a[href]', fig)[0];
        if (a) {
          a.parentNode.insertBefore(img, a);
          editor.dom.remove(a);
        }
      }
    };
    var linkImageFigure = function (editor, fig, attrs) {
      var img = editor.dom.select('img', fig)[0];
      if (img) {
        var a = editor.dom.create('a', attrs);
        img.parentNode.insertBefore(a, img);
        a.appendChild(img);
      }
    };

    var isListGroup = function (item) {
      return hasNonNullableKey(item, 'items');
    };
    var findTextByValue = function (value, catalog) {
      return findMap(catalog, function (item) {
        if (isListGroup(item)) {
          return findTextByValue(value, item.items);
        } else {
          return someIf(item.value === value, item);
        }
      });
    };
    var getDelta = function (persistentText, fieldName, catalog, data) {
      var value = data[fieldName];
      var hasPersistentText = persistentText.length > 0;
      return value !== undefined ? findTextByValue(value, catalog).map(function (i) {
        return {
          url: {
            value: i.value,
            meta: {
              text: hasPersistentText ? persistentText : i.text,
              attach: noop
            }
          },
          text: hasPersistentText ? persistentText : i.text
        };
      }) : Optional.none();
    };
    var findCatalog = function (catalogs, fieldName) {
      if (fieldName === 'link') {
        return catalogs.link;
      } else if (fieldName === 'anchor') {
        return catalogs.anchor;
      } else {
        return Optional.none();
      }
    };
    var init = function (initialData, linkCatalog) {
      var persistentData = {
        text: initialData.text,
        title: initialData.title
      };
      var getTitleFromUrlChange = function (url) {
        return someIf(persistentData.title.length <= 0, Optional.from(url.meta.title).getOr(''));
      };
      var getTextFromUrlChange = function (url) {
        return someIf(persistentData.text.length <= 0, Optional.from(url.meta.text).getOr(url.value));
      };
      var onUrlChange = function (data) {
        var text = getTextFromUrlChange(data.url);
        var title = getTitleFromUrlChange(data.url);
        if (text.isSome() || title.isSome()) {
          return Optional.some(__assign(__assign({}, text.map(function (text) {
            return { text: text };
          }).getOr({})), title.map(function (title) {
            return { title: title };
          }).getOr({})));
        } else {
          return Optional.none();
        }
      };
      var onCatalogChange = function (data, change) {
        var catalog = findCatalog(linkCatalog, change.name).getOr([]);
        return getDelta(persistentData.text, change.name, catalog, data);
      };
      var onChange = function (getData, change) {
        var name = change.name;
        if (name === 'url') {
          return onUrlChange(getData());
        } else if (contains([
            'anchor',
            'link'
          ], name)) {
          return onCatalogChange(getData(), change);
        } else if (name === 'text' || name === 'title') {
          persistentData[name] = getData()[name];
          return Optional.none();
        } else {
          return Optional.none();
        }
      };
      return { onChange: onChange };
    };
    var DialogChanges = {
      init: init,
      getDelta: getDelta
    };

    var global$4 = tinymce.util.Tools.resolve('tinymce.util.Delay');

    var global$5 = tinymce.util.Tools.resolve('tinymce.util.Promise');

    var delayedConfirm = function (editor, message, callback) {
      var rng = editor.selection.getRng();
      global$4.setEditorTimeout(editor, function () {
        editor.windowManager.confirm(message, function (state) {
          editor.selection.setRng(rng);
          callback(state);
        });
      });
    };
    var tryEmailTransform = function (data) {
      var url = data.href;
      var suggestMailTo = url.indexOf('@') > 0 && url.indexOf('/') === -1 && url.indexOf('mailto:') === -1;
      return suggestMailTo ? Optional.some({
        message: 'The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?',
        preprocess: function (oldData) {
          return __assign(__assign({}, oldData), { href: 'mailto:' + url });
        }
      }) : Optional.none();
    };
    var tryProtocolTransform = function (assumeExternalTargets, defaultLinkProtocol) {
      return function (data) {
        var url = data.href;
        var suggestProtocol = assumeExternalTargets === 1 && !hasProtocol(url) || assumeExternalTargets === 0 && /^\s*www(\.|\d\.)/i.test(url);
        return suggestProtocol ? Optional.some({
          message: 'The URL you entered seems to be an external link. Do you want to add the required ' + defaultLinkProtocol + ':// prefix?',
          preprocess: function (oldData) {
            return __assign(__assign({}, oldData), { href: defaultLinkProtocol + '://' + url });
          }
        }) : Optional.none();
      };
    };
    var preprocess = function (editor, data) {
      return findMap([
        tryEmailTransform,
        tryProtocolTransform(assumeExternalTargets(editor), getDefaultLinkProtocol(editor))
      ], function (f) {
        return f(data);
      }).fold(function () {
        return global$5.resolve(data);
      }, function (transform) {
        return new global$5(function (callback) {
          delayedConfirm(editor, transform.message, function (state) {
            callback(state ? transform.preprocess(data) : data);
          });
        });
      });
    };
    var DialogConfirms = { preprocess: preprocess };

    var getAnchors = function (editor) {
      var anchorNodes = editor.dom.select('a:not([href])');
      var anchors = bind(anchorNodes, function (anchor) {
        var id = anchor.name || anchor.id;
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
    var AnchorListOptions = { getAnchors: getAnchors };

    var getClasses = function (editor) {
      var list = getLinkClassList(editor);
      if (list.length > 0) {
        return ListOptions.sanitize(list);
      }
      return Optional.none();
    };
    var ClassListOptions = { getClasses: getClasses };

    var global$6 = tinymce.util.Tools.resolve('tinymce.util.XHR');

    var parseJson = function (text) {
      try {
        return Optional.some(JSON.parse(text));
      } catch (err) {
        return Optional.none();
      }
    };
    var getLinks = function (editor) {
      var extractor = function (item) {
        return editor.convertURL(item.value || item.url, 'href');
      };
      var linkList = getLinkList(editor);
      return new global$5(function (callback) {
        if (isString(linkList)) {
          global$6.send({
            url: linkList,
            success: function (text) {
              return callback(parseJson(text));
            },
            error: function (_) {
              return callback(Optional.none());
            }
          });
        } else if (isFunction(linkList)) {
          linkList(function (output) {
            return callback(Optional.some(output));
          });
        } else {
          callback(Optional.from(linkList));
        }
      }).then(function (optItems) {
        return optItems.bind(ListOptions.sanitizeWith(extractor)).map(function (items) {
          if (items.length > 0) {
            var noneItem = [{
                text: 'None',
                value: ''
              }];
            return noneItem.concat(items);
          } else {
            return items;
          }
        });
      });
    };
    var LinkListOptions = { getLinks: getLinks };

    var getRels = function (editor, initialTarget) {
      var list = getRelList(editor);
      if (list.length > 0) {
        var isTargetBlank_1 = initialTarget.is('_blank');
        var enforceSafe = allowUnsafeLinkTarget(editor) === false;
        var safeRelExtractor = function (item) {
          return applyRelTargetRules(ListOptions.getValue(item), isTargetBlank_1);
        };
        var sanitizer = enforceSafe ? ListOptions.sanitizeWith(safeRelExtractor) : ListOptions.sanitize;
        return sanitizer(list);
      }
      return Optional.none();
    };
    var RelOptions = { getRels: getRels };

    var fallbacks = [
      {
        text: 'Current window',
        value: ''
      },
      {
        text: 'New window',
        value: '_blank'
      }
    ];
    var getTargets = function (editor) {
      var list = getTargetList(editor);
      if (isArray(list)) {
        return ListOptions.sanitize(list).orThunk(function () {
          return Optional.some(fallbacks);
        });
      } else if (list === false) {
        return Optional.none();
      }
      return Optional.some(fallbacks);
    };
    var TargetOptions = { getTargets: getTargets };

    var nonEmptyAttr = function (dom, elem, name) {
      var val = dom.getAttrib(elem, name);
      return val !== null && val.length > 0 ? Optional.some(val) : Optional.none();
    };
    var extractFromAnchor = function (editor, anchor) {
      var dom = editor.dom;
      var onlyText = isOnlyTextSelected(editor);
      var text = onlyText ? Optional.some(getAnchorText(editor.selection, anchor)) : Optional.none();
      var url = anchor ? Optional.some(dom.getAttrib(anchor, 'href')) : Optional.none();
      var target = anchor ? Optional.from(dom.getAttrib(anchor, 'target')) : Optional.none();
      var rel = nonEmptyAttr(dom, anchor, 'rel');
      var linkClass = nonEmptyAttr(dom, anchor, 'class');
      var title = nonEmptyAttr(dom, anchor, 'title');
      return {
        url: url,
        text: text,
        title: title,
        target: target,
        rel: rel,
        linkClass: linkClass
      };
    };
    var collect = function (editor, linkNode) {
      return LinkListOptions.getLinks(editor).then(function (links) {
        var anchor = extractFromAnchor(editor, linkNode);
        return {
          anchor: anchor,
          catalogs: {
            targets: TargetOptions.getTargets(editor),
            rels: RelOptions.getRels(editor, anchor.target),
            classes: ClassListOptions.getClasses(editor),
            anchor: AnchorListOptions.getAnchors(editor),
            link: links
          },
          optNode: Optional.from(linkNode),
          flags: { titleEnabled: shouldShowLinkTitle(editor) }
        };
      });
    };
    var DialogInfo = { collect: collect };

    var handleSubmit = function (editor, info) {
      return function (api) {
        var data = api.getData();
        if (!data.url.value) {
          unlink(editor);
          api.close();
          return;
        }
        var getChangedValue = function (key) {
          return Optional.from(data[key]).filter(function (value) {
            return !info.anchor[key].is(value);
          });
        };
        var changedData = {
          href: data.url.value,
          text: getChangedValue('text'),
          target: getChangedValue('target'),
          rel: getChangedValue('rel'),
          class: getChangedValue('linkClass'),
          title: getChangedValue('title')
        };
        var attachState = {
          href: data.url.value,
          attach: data.url.meta !== undefined && data.url.meta.attach ? data.url.meta.attach : noop
        };
        DialogConfirms.preprocess(editor, changedData).then(function (pData) {
          link(editor, attachState, pData);
        });
        api.close();
      };
    };
    var collectData = function (editor) {
      var anchorNode = getAnchorElement(editor);
      return DialogInfo.collect(editor, anchorNode);
    };
    var getInitialData = function (info, defaultTarget) {
      var anchor = info.anchor;
      var url = anchor.url.getOr('');
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
    var makeDialog = function (settings, onSubmit, editor) {
      var urlInput = [{
          name: 'url',
          type: 'urlinput',
          filetype: 'file',
          label: 'URL'
        }];
      var displayText = settings.anchor.text.map(function () {
        return {
          name: 'text',
          type: 'input',
          label: 'Text to display'
        };
      }).toArray();
      var titleText = settings.flags.titleEnabled ? [{
          name: 'title',
          type: 'input',
          label: 'Title'
        }] : [];
      var defaultTarget = Optional.from(getDefaultLinkTarget(editor));
      var initialData = getInitialData(settings, defaultTarget);
      var catalogs = settings.catalogs;
      var dialogDelta = DialogChanges.init(initialData, catalogs);
      var body = {
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
        body: body,
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
        initialData: initialData,
        onChange: function (api, _a) {
          var name = _a.name;
          dialogDelta.onChange(api.getData, { name: name }).each(function (newData) {
            api.setData(newData);
          });
        },
        onSubmit: onSubmit
      };
    };
    var open = function (editor) {
      var data = collectData(editor);
      data.then(function (info) {
        var onSubmit = handleSubmit(editor, info);
        return makeDialog(info, onSubmit, editor);
      }).then(function (spec) {
        editor.windowManager.open(spec);
      });
    };

    var appendClickRemove = function (link, evt) {
      document.body.appendChild(link);
      link.dispatchEvent(evt);
      document.body.removeChild(link);
    };
    var open$1 = function (url) {
      var link = document.createElement('a');
      link.target = '_blank';
      link.href = url;
      link.rel = 'noreferrer noopener';
      var evt = document.createEvent('MouseEvents');
      evt.initMouseEvent('click', true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
      appendClickRemove(link, evt);
    };

    var getLink = function (editor, elm) {
      return editor.dom.getParent(elm, 'a[href]');
    };
    var getSelectedLink = function (editor) {
      return getLink(editor, editor.selection.getStart());
    };
    var hasOnlyAltModifier = function (e) {
      return e.altKey === true && e.shiftKey === false && e.ctrlKey === false && e.metaKey === false;
    };
    var gotoLink = function (editor, a) {
      if (a) {
        var href = getHref(a);
        if (/^#/.test(href)) {
          var targetEl = editor.$(href);
          if (targetEl.length) {
            editor.selection.scrollIntoView(targetEl[0], true);
          }
        } else {
          open$1(a.href);
        }
      }
    };
    var openDialog = function (editor) {
      return function () {
        open(editor);
      };
    };
    var gotoSelectedLink = function (editor) {
      return function () {
        gotoLink(editor, getSelectedLink(editor));
      };
    };
    var setupGotoLinks = function (editor) {
      editor.on('click', function (e) {
        var link = getLink(editor, e.target);
        if (link && global$1.metaKeyPressed(e)) {
          e.preventDefault();
          gotoLink(editor, link);
        }
      });
      editor.on('keydown', function (e) {
        var link = getSelectedLink(editor);
        if (link && e.keyCode === 13 && hasOnlyAltModifier(e)) {
          e.preventDefault();
          gotoLink(editor, link);
        }
      });
    };
    var toggleState = function (editor, toggler) {
      editor.on('NodeChange', toggler);
      return function () {
        return editor.off('NodeChange', toggler);
      };
    };
    var toggleActiveState = function (editor) {
      return function (api) {
        return toggleState(editor, function () {
          api.setActive(!editor.mode.isReadOnly() && getAnchorElement(editor, editor.selection.getNode()) !== null);
        });
      };
    };
    var toggleEnabledState = function (editor) {
      return function (api) {
        var updateState = function () {
          return api.setDisabled(getAnchorElement(editor, editor.selection.getNode()) === null);
        };
        updateState();
        return toggleState(editor, updateState);
      };
    };
    var toggleUnlinkState = function (editor) {
      return function (api) {
        var hasLinks$1 = function (parents) {
          return hasLinks(parents) || hasLinksInSelection(editor.selection.getRng());
        };
        var parents = editor.dom.getParents(editor.selection.getStart());
        api.setDisabled(!hasLinks$1(parents));
        return toggleState(editor, function (e) {
          return api.setDisabled(!hasLinks$1(e.parents));
        });
      };
    };

    var register = function (editor) {
      editor.addCommand('mceLink', function () {
        if (useQuickLink(editor)) {
          editor.fire('contexttoolbar-show', { toolbarKey: 'quicklink' });
        } else {
          openDialog(editor)();
        }
      });
    };

    var setup = function (editor) {
      editor.addShortcut('Meta+K', '', function () {
        editor.execCommand('mceLink');
      });
    };

    var setupButtons = function (editor) {
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
        onAction: function () {
          return unlink(editor);
        },
        onSetup: toggleUnlinkState(editor)
      });
    };
    var setupMenuItems = function (editor) {
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
        onAction: function () {
          return unlink(editor);
        },
        onSetup: toggleUnlinkState(editor)
      });
    };
    var setupContextMenu = function (editor) {
      var inLink = 'link unlink openlink';
      var noLink = 'link';
      editor.ui.registry.addContextMenu('link', {
        update: function (element) {
          return hasLinks(editor.dom.getParents(element, 'a')) ? inLink : noLink;
        }
      });
    };
    var setupContextToolbars = function (editor) {
      var collapseSelectionToEnd = function (editor) {
        editor.selection.collapse(false);
      };
      var onSetupLink = function (buttonApi) {
        var node = editor.selection.getNode();
        buttonApi.setDisabled(!getAnchorElement(editor, node));
        return noop;
      };
      editor.ui.registry.addContextForm('quicklink', {
        launch: {
          type: 'contextformtogglebutton',
          icon: 'link',
          tooltip: 'Link',
          onSetup: toggleActiveState(editor)
        },
        label: 'Link',
        predicate: function (node) {
          return !!getAnchorElement(editor, node) && hasContextToolbar(editor);
        },
        initValue: function () {
          var elm = getAnchorElement(editor);
          return !!elm ? getHref(elm) : '';
        },
        commands: [
          {
            type: 'contextformtogglebutton',
            icon: 'link',
            tooltip: 'Link',
            primary: true,
            onSetup: function (buttonApi) {
              var node = editor.selection.getNode();
              buttonApi.setActive(!!getAnchorElement(editor, node));
              return toggleActiveState(editor)(buttonApi);
            },
            onAction: function (formApi) {
              var anchor = getAnchorElement(editor);
              var value = formApi.getValue();
              if (!anchor) {
                var attachState = {
                  href: value,
                  attach: noop
                };
                var onlyText = isOnlyTextSelected(editor);
                var text = onlyText ? Optional.some(getAnchorText(editor.selection, anchor)).filter(function (t) {
                  return t.length > 0;
                }).or(Optional.from(value)) : Optional.none();
                link(editor, attachState, {
                  href: value,
                  text: text,
                  title: Optional.none(),
                  rel: Optional.none(),
                  target: Optional.none(),
                  class: Optional.none()
                });
                formApi.hide();
              } else {
                editor.undoManager.transact(function () {
                  editor.dom.setAttrib(anchor, 'href', value);
                  collapseSelectionToEnd(editor);
                  formApi.hide();
                });
              }
            }
          },
          {
            type: 'contextformbutton',
            icon: 'unlink',
            tooltip: 'Remove link',
            onSetup: onSetupLink,
            onAction: function (formApi) {
              unlink(editor);
              formApi.hide();
            }
          },
          {
            type: 'contextformbutton',
            icon: 'new-tab',
            tooltip: 'Open link',
            onSetup: onSetupLink,
            onAction: function (formApi) {
              gotoSelectedLink(editor)();
              formApi.hide();
            }
          }
        ]
      });
    };

    function Plugin () {
      global.add('link', function (editor) {
        setupButtons(editor);
        setupMenuItems(editor);
        setupContextMenu(editor);
        setupContextToolbars(editor);
        setupGotoLinks(editor);
        register(editor);
        setup(editor);
      });
    }

    Plugin();

}());
