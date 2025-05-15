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
    var isString = isType('string');
    var isArray = isType('array');
    var isBoolean = isSimpleType('boolean');
    var isNumber = isSimpleType('number');

    var noop = function () {
    };
    var constant = function (value) {
      return function () {
        return value;
      };
    };
    var never = constant(false);
    var always = constant(true);

    var punctuationStr = '[!-#%-*,-\\/:;?@\\[-\\]_{}\xA1\xAB\xB7\xBB\xBF;\xB7\u055A-\u055F\u0589\u058A\u05BE\u05C0\u05C3\u05C6\u05F3\u05F4\u0609\u060A\u060C\u060D\u061B\u061E\u061F\u066A-\u066D\u06D4\u0700-\u070D\u07F7-\u07F9\u0830-\u083E\u085E\u0964\u0965\u0970\u0DF4\u0E4F\u0E5A\u0E5B\u0F04-\u0F12\u0F3A-\u0F3D\u0F85\u0FD0-\u0FD4\u0FD9\u0FDA\u104A-\u104F\u10FB\u1361-\u1368\u1400\u166D\u166E\u169B\u169C\u16EB-\u16ED\u1735\u1736\u17D4-\u17D6\u17D8-\u17DA\u1800-\u180A\u1944\u1945\u1A1E\u1A1F\u1AA0-\u1AA6\u1AA8-\u1AAD\u1B5A-\u1B60\u1BFC-\u1BFF\u1C3B-\u1C3F\u1C7E\u1C7F\u1CD3\u2010-\u2027\u2030-\u2043\u2045-\u2051\u2053-\u205E\u207D\u207E\u208D\u208E\u3008\u3009\u2768-\u2775\u27C5\u27C6\u27E6-\u27EF\u2983-\u2998\u29D8-\u29DB\u29FC\u29FD\u2CF9-\u2CFC\u2CFE\u2CFF\u2D70\u2E00-\u2E2E\u2E30\u2E31\u3001-\u3003\u3008-\u3011\u3014-\u301F\u3030\u303D\u30A0\u30FB\uA4FE\uA4FF\uA60D-\uA60F\uA673\uA67E\uA6F2-\uA6F7\uA874-\uA877\uA8CE\uA8CF\uA8F8-\uA8FA\uA92E\uA92F\uA95F\uA9C1-\uA9CD\uA9DE\uA9DF\uAA5C-\uAA5F\uAADE\uAADF\uABEB\uFD3E\uFD3F\uFE10-\uFE19\uFE30-\uFE52\uFE54-\uFE61\uFE63\uFE68\uFE6A\uFE6B\uFF01-\uFF03\uFF05-\uFF0A\uFF0C-\uFF0F\uFF1A\uFF1B\uFF1F\uFF20\uFF3B-\uFF3D\uff3f\uFF5B\uFF5D\uFF5F-\uFF65]';

    var punctuation = constant(punctuationStr);

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

    var punctuation$1 = punctuation;

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    var nativeSlice = Array.prototype.slice;
    var nativePush = Array.prototype.push;
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
    var eachr = function (xs, f) {
      for (var i = xs.length - 1; i >= 0; i--) {
        var x = xs[i];
        f(x, i);
      }
    };
    var groupBy = function (xs, f) {
      if (xs.length === 0) {
        return [];
      } else {
        var wasType = f(xs[0]);
        var r = [];
        var group = [];
        for (var i = 0, len = xs.length; i < len; i++) {
          var x = xs[i];
          var type = f(x);
          if (type !== wasType) {
            r.push(group);
            group = [];
          }
          wasType = type;
          group.push(x);
        }
        if (group.length !== 0) {
          r.push(group);
        }
        return r;
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
    var sort = function (xs, comparator) {
      var copy = nativeSlice.call(xs, 0);
      copy.sort(comparator);
      return copy;
    };

    var hasOwnProperty = Object.hasOwnProperty;
    var has = function (obj, key) {
      return hasOwnProperty.call(obj, key);
    };

    var Global = typeof window !== 'undefined' ? window : Function('return this;')();

    var DOCUMENT = 9;
    var DOCUMENT_FRAGMENT = 11;
    var ELEMENT = 1;
    var TEXT = 3;

    var type = function (element) {
      return element.dom.nodeType;
    };
    var isType$1 = function (t) {
      return function (element) {
        return type(element) === t;
      };
    };
    var isText = isType$1(TEXT);

    var rawSet = function (dom, key, value) {
      if (isString(value) || isBoolean(value) || isNumber(value)) {
        dom.setAttribute(key, value + '');
      } else {
        console.error('Invalid call to Attribute.set. Key ', key, ':: Value ', value, ':: Element ', dom);
        throw new Error('Attribute value was not simple');
      }
    };
    var set = function (element, key, value) {
      rawSet(element.dom, key, value);
    };

    var compareDocumentPosition = function (a, b, match) {
      return (a.compareDocumentPosition(b) & match) !== 0;
    };
    var documentPositionPreceding = function (a, b) {
      return compareDocumentPosition(a, b, Node.DOCUMENT_POSITION_PRECEDING);
    };

    var fromHtml = function (html, scope) {
      var doc = scope || document;
      var div = doc.createElement('div');
      div.innerHTML = html;
      if (!div.hasChildNodes() || div.childNodes.length > 1) {
        console.error('HTML does not have a single root node', html);
        throw new Error('HTML must have a single root node');
      }
      return fromDom(div.childNodes[0]);
    };
    var fromTag = function (tag, scope) {
      var doc = scope || document;
      var node = doc.createElement(tag);
      return fromDom(node);
    };
    var fromText = function (text, scope) {
      var doc = scope || document;
      var node = doc.createTextNode(text);
      return fromDom(node);
    };
    var fromDom = function (node) {
      if (node === null || node === undefined) {
        throw new Error('Node cannot be null or undefined');
      }
      return { dom: node };
    };
    var fromPoint = function (docElm, x, y) {
      return Optional.from(docElm.dom.elementFromPoint(x, y)).map(fromDom);
    };
    var SugarElement = {
      fromHtml: fromHtml,
      fromTag: fromTag,
      fromText: fromText,
      fromDom: fromDom,
      fromPoint: fromPoint
    };

    var bypassSelector = function (dom) {
      return dom.nodeType !== ELEMENT && dom.nodeType !== DOCUMENT && dom.nodeType !== DOCUMENT_FRAGMENT || dom.childElementCount === 0;
    };
    var all = function (selector, scope) {
      var base = scope === undefined ? document : scope.dom;
      return bypassSelector(base) ? [] : map(base.querySelectorAll(selector), SugarElement.fromDom);
    };

    var parent = function (element) {
      return Optional.from(element.dom.parentNode).map(SugarElement.fromDom);
    };
    var children = function (element) {
      return map(element.dom.childNodes, SugarElement.fromDom);
    };
    var spot = function (element, offset) {
      return {
        element: element,
        offset: offset
      };
    };
    var leaf = function (element, offset) {
      var cs = children(element);
      return cs.length > 0 && offset < cs.length ? spot(cs[offset], 0) : spot(element, offset);
    };

    var before = function (marker, element) {
      var parent$1 = parent(marker);
      parent$1.each(function (v) {
        v.dom.insertBefore(element.dom, marker.dom);
      });
    };
    var append = function (parent, element) {
      parent.dom.appendChild(element.dom);
    };
    var wrap = function (element, wrapper) {
      before(element, wrapper);
      append(wrapper, element);
    };

    var NodeValue = function (is, name) {
      var get = function (element) {
        if (!is(element)) {
          throw new Error('Can only get ' + name + ' value of a ' + name + ' node');
        }
        return getOption(element).getOr('');
      };
      var getOption = function (element) {
        return is(element) ? Optional.from(element.dom.nodeValue) : Optional.none();
      };
      var set = function (element, value) {
        if (!is(element)) {
          throw new Error('Can only set raw ' + name + ' value of a ' + name + ' node');
        }
        element.dom.nodeValue = value;
      };
      return {
        get: get,
        getOption: getOption,
        set: set
      };
    };

    var api = NodeValue(isText, 'text');
    var get = function (element) {
      return api.get(element);
    };

    var descendants = function (scope, selector) {
      return all(selector, scope);
    };

    var global$2 = tinymce.util.Tools.resolve('tinymce.dom.TreeWalker');

    var isSimpleBoundary = function (dom, node) {
      return dom.isBlock(node) || has(dom.schema.getShortEndedElements(), node.nodeName);
    };
    var isContentEditableFalse = function (dom, node) {
      return dom.getContentEditable(node) === 'false';
    };
    var isContentEditableTrueInCef = function (dom, node) {
      return dom.getContentEditable(node) === 'true' && dom.getContentEditableParent(node.parentNode) === 'false';
    };
    var isHidden = function (dom, node) {
      return !dom.isBlock(node) && has(dom.schema.getWhiteSpaceElements(), node.nodeName);
    };
    var isBoundary = function (dom, node) {
      return isSimpleBoundary(dom, node) || isContentEditableFalse(dom, node) || isHidden(dom, node) || isContentEditableTrueInCef(dom, node);
    };
    var isText$1 = function (node) {
      return node.nodeType === 3;
    };
    var nuSection = function () {
      return {
        sOffset: 0,
        fOffset: 0,
        elements: []
      };
    };
    var toLeaf = function (node, offset) {
      return leaf(SugarElement.fromDom(node), offset);
    };
    var walk = function (dom, walkerFn, startNode, callbacks, endNode, skipStart) {
      if (skipStart === void 0) {
        skipStart = true;
      }
      var next = skipStart ? walkerFn(false) : startNode;
      while (next) {
        var isCefNode = isContentEditableFalse(dom, next);
        if (isCefNode || isHidden(dom, next)) {
          var stopWalking = isCefNode ? callbacks.cef(next) : callbacks.boundary(next);
          if (stopWalking) {
            break;
          } else {
            next = walkerFn(true);
            continue;
          }
        } else if (isSimpleBoundary(dom, next)) {
          if (callbacks.boundary(next)) {
            break;
          }
        } else if (isText$1(next)) {
          callbacks.text(next);
        }
        if (next === endNode) {
          break;
        } else {
          next = walkerFn(false);
        }
      }
    };
    var collectTextToBoundary = function (dom, section, node, rootNode, forwards) {
      if (isBoundary(dom, node)) {
        return;
      }
      var rootBlock = dom.getParent(rootNode, dom.isBlock);
      var walker = new global$2(node, rootBlock);
      var walkerFn = forwards ? walker.next.bind(walker) : walker.prev.bind(walker);
      walk(dom, walkerFn, node, {
        boundary: always,
        cef: always,
        text: function (next) {
          if (forwards) {
            section.fOffset += next.length;
          } else {
            section.sOffset += next.length;
          }
          section.elements.push(SugarElement.fromDom(next));
        }
      });
    };
    var collect = function (dom, rootNode, startNode, endNode, callbacks, skipStart) {
      if (skipStart === void 0) {
        skipStart = true;
      }
      var walker = new global$2(startNode, rootNode);
      var sections = [];
      var current = nuSection();
      collectTextToBoundary(dom, current, startNode, rootNode, false);
      var finishSection = function () {
        if (current.elements.length > 0) {
          sections.push(current);
          current = nuSection();
        }
        return false;
      };
      walk(dom, walker.next.bind(walker), startNode, {
        boundary: finishSection,
        cef: function (node) {
          finishSection();
          if (callbacks) {
            sections.push.apply(sections, callbacks.cef(node));
          }
          return false;
        },
        text: function (next) {
          current.elements.push(SugarElement.fromDom(next));
          if (callbacks) {
            callbacks.text(next, current);
          }
        }
      }, endNode, skipStart);
      if (endNode) {
        collectTextToBoundary(dom, current, endNode, rootNode, true);
      }
      finishSection();
      return sections;
    };
    var collectRangeSections = function (dom, rng) {
      var start = toLeaf(rng.startContainer, rng.startOffset);
      var startNode = start.element.dom;
      var end = toLeaf(rng.endContainer, rng.endOffset);
      var endNode = end.element.dom;
      return collect(dom, rng.commonAncestorContainer, startNode, endNode, {
        text: function (node, section) {
          if (node === endNode) {
            section.fOffset += node.length - end.offset;
          } else if (node === startNode) {
            section.sOffset += start.offset;
          }
        },
        cef: function (node) {
          var sections = bind(descendants(SugarElement.fromDom(node), '*[contenteditable=true]'), function (e) {
            var ceTrueNode = e.dom;
            return collect(dom, ceTrueNode, ceTrueNode);
          });
          return sort(sections, function (a, b) {
            return documentPositionPreceding(a.elements[0].dom, b.elements[0].dom) ? 1 : -1;
          });
        }
      }, false);
    };
    var fromRng = function (dom, rng) {
      return rng.collapsed ? [] : collectRangeSections(dom, rng);
    };
    var fromNode = function (dom, node) {
      var rng = dom.createRng();
      rng.selectNode(node);
      return fromRng(dom, rng);
    };
    var fromNodes = function (dom, nodes) {
      return bind(nodes, function (node) {
        return fromNode(dom, node);
      });
    };

    var find = function (text, pattern, start, finish) {
      if (start === void 0) {
        start = 0;
      }
      if (finish === void 0) {
        finish = text.length;
      }
      var regex = pattern.regex;
      regex.lastIndex = start;
      var results = [];
      var match;
      while (match = regex.exec(text)) {
        var matchedText = match[pattern.matchIndex];
        var matchStart = match.index + match[0].indexOf(matchedText);
        var matchFinish = matchStart + matchedText.length;
        if (matchFinish > finish) {
          break;
        }
        results.push({
          start: matchStart,
          finish: matchFinish
        });
        regex.lastIndex = matchFinish;
      }
      return results;
    };
    var extract = function (elements, matches) {
      var nodePositions = foldl(elements, function (acc, element) {
        var content = get(element);
        var start = acc.last;
        var finish = start + content.length;
        var positions = bind(matches, function (match, matchIdx) {
          if (match.start < finish && match.finish > start) {
            return [{
                element: element,
                start: Math.max(start, match.start) - start,
                finish: Math.min(finish, match.finish) - start,
                matchId: matchIdx
              }];
          } else {
            return [];
          }
        });
        return {
          results: acc.results.concat(positions),
          last: finish
        };
      }, {
        results: [],
        last: 0
      }).results;
      return groupBy(nodePositions, function (position) {
        return position.matchId;
      });
    };

    var find$1 = function (pattern, sections) {
      return bind(sections, function (section) {
        var elements = section.elements;
        var content = map(elements, get).join('');
        var positions = find(content, pattern, section.sOffset, content.length - section.fOffset);
        return extract(elements, positions);
      });
    };
    var mark = function (matches, replacementNode) {
      eachr(matches, function (match, idx) {
        eachr(match, function (pos) {
          var wrapper = SugarElement.fromDom(replacementNode.cloneNode(false));
          set(wrapper, 'data-mce-index', idx);
          var textNode = pos.element.dom;
          if (textNode.length === pos.finish && pos.start === 0) {
            wrap(pos.element, wrapper);
          } else {
            if (textNode.length !== pos.finish) {
              textNode.splitText(pos.finish);
            }
            var matchNode = textNode.splitText(pos.start);
            wrap(SugarElement.fromDom(matchNode), wrapper);
          }
        });
      });
    };
    var findAndMark = function (dom, pattern, node, replacementNode) {
      var textSections = fromNode(dom, node);
      var matches = find$1(pattern, textSections);
      mark(matches, replacementNode);
      return matches.length;
    };
    var findAndMarkInSelection = function (dom, pattern, selection, replacementNode) {
      var bookmark = selection.getBookmark();
      var nodes = dom.select('td[data-mce-selected],th[data-mce-selected]');
      var textSections = nodes.length > 0 ? fromNodes(dom, nodes) : fromRng(dom, selection.getRng());
      var matches = find$1(pattern, textSections);
      mark(matches, replacementNode);
      selection.moveToBookmark(bookmark);
      return matches.length;
    };

    var getElmIndex = function (elm) {
      var value = elm.getAttribute('data-mce-index');
      if (typeof value === 'number') {
        return '' + value;
      }
      return value;
    };
    var markAllMatches = function (editor, currentSearchState, pattern, inSelection) {
      var marker = editor.dom.create('span', { 'data-mce-bogus': 1 });
      marker.className = 'mce-match-marker';
      var node = editor.getBody();
      done(editor, currentSearchState, false);
      if (inSelection) {
        return findAndMarkInSelection(editor.dom, pattern, editor.selection, marker);
      } else {
        return findAndMark(editor.dom, pattern, node, marker);
      }
    };
    var unwrap = function (node) {
      var parentNode = node.parentNode;
      if (node.firstChild) {
        parentNode.insertBefore(node.firstChild, node);
      }
      node.parentNode.removeChild(node);
    };
    var findSpansByIndex = function (editor, index) {
      var spans = [];
      var nodes = global$1.toArray(editor.getBody().getElementsByTagName('span'));
      if (nodes.length) {
        for (var i = 0; i < nodes.length; i++) {
          var nodeIndex = getElmIndex(nodes[i]);
          if (nodeIndex === null || !nodeIndex.length) {
            continue;
          }
          if (nodeIndex === index.toString()) {
            spans.push(nodes[i]);
          }
        }
      }
      return spans;
    };
    var moveSelection = function (editor, currentSearchState, forward) {
      var searchState = currentSearchState.get();
      var testIndex = searchState.index;
      var dom = editor.dom;
      forward = forward !== false;
      if (forward) {
        if (testIndex + 1 === searchState.count) {
          testIndex = 0;
        } else {
          testIndex++;
        }
      } else {
        if (testIndex - 1 === -1) {
          testIndex = searchState.count - 1;
        } else {
          testIndex--;
        }
      }
      dom.removeClass(findSpansByIndex(editor, searchState.index), 'mce-match-marker-selected');
      var spans = findSpansByIndex(editor, testIndex);
      if (spans.length) {
        dom.addClass(findSpansByIndex(editor, testIndex), 'mce-match-marker-selected');
        editor.selection.scrollIntoView(spans[0]);
        return testIndex;
      }
      return -1;
    };
    var removeNode = function (dom, node) {
      var parent = node.parentNode;
      dom.remove(node);
      if (dom.isEmpty(parent)) {
        dom.remove(parent);
      }
    };
    var escapeSearchText = function (text, wholeWord) {
      var escapedText = text.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&').replace(/\s/g, '[^\\S\\r\\n\\uFEFF]');
      var wordRegex = '(' + escapedText + ')';
      return wholeWord ? '(?:^|\\s|' + punctuation$1() + ')' + wordRegex + ('(?=$|\\s|' + punctuation$1() + ')') : wordRegex;
    };
    var find$2 = function (editor, currentSearchState, text, matchCase, wholeWord, inSelection) {
      var escapedText = escapeSearchText(text, wholeWord);
      var pattern = {
        regex: new RegExp(escapedText, matchCase ? 'g' : 'gi'),
        matchIndex: 1
      };
      var count = markAllMatches(editor, currentSearchState, pattern, inSelection);
      if (count) {
        var newIndex = moveSelection(editor, currentSearchState, true);
        currentSearchState.set({
          index: newIndex,
          count: count,
          text: text,
          matchCase: matchCase,
          wholeWord: wholeWord,
          inSelection: inSelection
        });
      }
      return count;
    };
    var next = function (editor, currentSearchState) {
      var index = moveSelection(editor, currentSearchState, true);
      currentSearchState.set(__assign(__assign({}, currentSearchState.get()), { index: index }));
    };
    var prev = function (editor, currentSearchState) {
      var index = moveSelection(editor, currentSearchState, false);
      currentSearchState.set(__assign(__assign({}, currentSearchState.get()), { index: index }));
    };
    var isMatchSpan = function (node) {
      var matchIndex = getElmIndex(node);
      return matchIndex !== null && matchIndex.length > 0;
    };
    var replace = function (editor, currentSearchState, text, forward, all) {
      var searchState = currentSearchState.get();
      var currentIndex = searchState.index;
      var currentMatchIndex, nextIndex = currentIndex;
      forward = forward !== false;
      var node = editor.getBody();
      var nodes = global$1.grep(global$1.toArray(node.getElementsByTagName('span')), isMatchSpan);
      for (var i = 0; i < nodes.length; i++) {
        var nodeIndex = getElmIndex(nodes[i]);
        var matchIndex = currentMatchIndex = parseInt(nodeIndex, 10);
        if (all || matchIndex === searchState.index) {
          if (text.length) {
            nodes[i].firstChild.nodeValue = text;
            unwrap(nodes[i]);
          } else {
            removeNode(editor.dom, nodes[i]);
          }
          while (nodes[++i]) {
            matchIndex = parseInt(getElmIndex(nodes[i]), 10);
            if (matchIndex === currentMatchIndex) {
              removeNode(editor.dom, nodes[i]);
            } else {
              i--;
              break;
            }
          }
          if (forward) {
            nextIndex--;
          }
        } else if (currentMatchIndex > currentIndex) {
          nodes[i].setAttribute('data-mce-index', String(currentMatchIndex - 1));
        }
      }
      currentSearchState.set(__assign(__assign({}, searchState), {
        count: all ? 0 : searchState.count - 1,
        index: nextIndex
      }));
      if (forward) {
        next(editor, currentSearchState);
      } else {
        prev(editor, currentSearchState);
      }
      return !all && currentSearchState.get().count > 0;
    };
    var done = function (editor, currentSearchState, keepEditorSelection) {
      var i, startContainer, endContainer;
      var searchState = currentSearchState.get();
      var nodes = global$1.toArray(editor.getBody().getElementsByTagName('span'));
      for (i = 0; i < nodes.length; i++) {
        var nodeIndex = getElmIndex(nodes[i]);
        if (nodeIndex !== null && nodeIndex.length) {
          if (nodeIndex === searchState.index.toString()) {
            if (!startContainer) {
              startContainer = nodes[i].firstChild;
            }
            endContainer = nodes[i].firstChild;
          }
          unwrap(nodes[i]);
        }
      }
      currentSearchState.set(__assign(__assign({}, searchState), {
        index: -1,
        count: 0,
        text: ''
      }));
      if (startContainer && endContainer) {
        var rng = editor.dom.createRng();
        rng.setStart(startContainer, 0);
        rng.setEnd(endContainer, endContainer.data.length);
        if (keepEditorSelection !== false) {
          editor.selection.setRng(rng);
        }
        return rng;
      }
    };
    var hasNext = function (editor, currentSearchState) {
      return currentSearchState.get().count > 1;
    };
    var hasPrev = function (editor, currentSearchState) {
      return currentSearchState.get().count > 1;
    };

    var get$1 = function (editor, currentState) {
      var done$1 = function (keepEditorSelection) {
        return done(editor, currentState, keepEditorSelection);
      };
      var find = function (text, matchCase, wholeWord, inSelection) {
        if (inSelection === void 0) {
          inSelection = false;
        }
        return find$2(editor, currentState, text, matchCase, wholeWord, inSelection);
      };
      var next$1 = function () {
        return next(editor, currentState);
      };
      var prev$1 = function () {
        return prev(editor, currentState);
      };
      var replace$1 = function (text, forward, all) {
        return replace(editor, currentState, text, forward, all);
      };
      return {
        done: done$1,
        find: find,
        next: next$1,
        prev: prev$1,
        replace: replace$1
      };
    };

    var value = function () {
      var subject = Cell(Optional.none());
      var clear = function () {
        return subject.set(Optional.none());
      };
      var set = function (s) {
        return subject.set(Optional.some(s));
      };
      var isSet = function () {
        return subject.get().isSome();
      };
      var on = function (f) {
        return subject.get().each(f);
      };
      return {
        clear: clear,
        set: set,
        isSet: isSet,
        on: on
      };
    };

    var global$3 = tinymce.util.Tools.resolve('tinymce.Env');

    var open = function (editor, currentSearchState) {
      var dialogApi = value();
      editor.undoManager.add();
      var selectedText = global$1.trim(editor.selection.getContent({ format: 'text' }));
      var updateButtonStates = function (api) {
        var updateNext = hasNext(editor, currentSearchState) ? api.enable : api.disable;
        updateNext('next');
        var updatePrev = hasPrev(editor, currentSearchState) ? api.enable : api.disable;
        updatePrev('prev');
      };
      var updateSearchState = function (api) {
        var data = api.getData();
        var current = currentSearchState.get();
        currentSearchState.set(__assign(__assign({}, current), {
          matchCase: data.matchcase,
          wholeWord: data.wholewords,
          inSelection: data.inselection
        }));
      };
      var disableAll = function (api, disable) {
        var buttons = [
          'replace',
          'replaceall',
          'prev',
          'next'
        ];
        var toggle = disable ? api.disable : api.enable;
        each(buttons, toggle);
      };
      var notFoundAlert = function (api) {
        editor.windowManager.alert('Could not find the specified string.', function () {
          api.focus('findtext');
        });
      };
      var focusButtonIfRequired = function (api, name) {
        if (global$3.browser.isSafari() && global$3.deviceType.isTouch() && (name === 'find' || name === 'replace' || name === 'replaceall')) {
          api.focus(name);
        }
      };
      var reset = function (api) {
        done(editor, currentSearchState, false);
        disableAll(api, true);
        updateButtonStates(api);
      };
      var doFind = function (api) {
        var data = api.getData();
        var last = currentSearchState.get();
        if (!data.findtext.length) {
          reset(api);
          return;
        }
        if (last.text === data.findtext && last.matchCase === data.matchcase && last.wholeWord === data.wholewords) {
          next(editor, currentSearchState);
        } else {
          var count = find$2(editor, currentSearchState, data.findtext, data.matchcase, data.wholewords, data.inselection);
          if (count <= 0) {
            notFoundAlert(api);
          }
          disableAll(api, count === 0);
        }
        updateButtonStates(api);
      };
      var initialState = currentSearchState.get();
      var initialData = {
        findtext: selectedText,
        replacetext: '',
        wholewords: initialState.wholeWord,
        matchcase: initialState.matchCase,
        inselection: initialState.inSelection
      };
      var spec = {
        title: 'Find and Replace',
        size: 'normal',
        body: {
          type: 'panel',
          items: [
            {
              type: 'bar',
              items: [
                {
                  type: 'input',
                  name: 'findtext',
                  placeholder: 'Find',
                  maximized: true,
                  inputMode: 'search'
                },
                {
                  type: 'button',
                  name: 'prev',
                  text: 'Previous',
                  icon: 'action-prev',
                  disabled: true,
                  borderless: true
                },
                {
                  type: 'button',
                  name: 'next',
                  text: 'Next',
                  icon: 'action-next',
                  disabled: true,
                  borderless: true
                }
              ]
            },
            {
              type: 'input',
              name: 'replacetext',
              placeholder: 'Replace with',
              inputMode: 'search'
            }
          ]
        },
        buttons: [
          {
            type: 'menu',
            name: 'options',
            icon: 'preferences',
            tooltip: 'Preferences',
            align: 'start',
            items: [
              {
                type: 'togglemenuitem',
                name: 'matchcase',
                text: 'Match case'
              },
              {
                type: 'togglemenuitem',
                name: 'wholewords',
                text: 'Find whole words only'
              },
              {
                type: 'togglemenuitem',
                name: 'inselection',
                text: 'Find in selection'
              }
            ]
          },
          {
            type: 'custom',
            name: 'find',
            text: 'Find',
            primary: true
          },
          {
            type: 'custom',
            name: 'replace',
            text: 'Replace',
            disabled: true
          },
          {
            type: 'custom',
            name: 'replaceall',
            text: 'Replace all',
            disabled: true
          }
        ],
        initialData: initialData,
        onChange: function (api, details) {
          if (details.name === 'findtext' && currentSearchState.get().count > 0) {
            reset(api);
          }
        },
        onAction: function (api, details) {
          var data = api.getData();
          switch (details.name) {
          case 'find':
            doFind(api);
            break;
          case 'replace':
            if (!replace(editor, currentSearchState, data.replacetext)) {
              reset(api);
            } else {
              updateButtonStates(api);
            }
            break;
          case 'replaceall':
            replace(editor, currentSearchState, data.replacetext, true, true);
            reset(api);
            break;
          case 'prev':
            prev(editor, currentSearchState);
            updateButtonStates(api);
            break;
          case 'next':
            next(editor, currentSearchState);
            updateButtonStates(api);
            break;
          case 'matchcase':
          case 'wholewords':
          case 'inselection':
            updateSearchState(api);
            reset(api);
            break;
          }
          focusButtonIfRequired(api, details.name);
        },
        onSubmit: function (api) {
          doFind(api);
          focusButtonIfRequired(api, 'find');
        },
        onClose: function () {
          editor.focus();
          done(editor, currentSearchState);
          editor.undoManager.add();
        }
      };
      dialogApi.set(editor.windowManager.open(spec, { inline: 'toolbar' }));
    };

    var register = function (editor, currentSearchState) {
      editor.addCommand('SearchReplace', function () {
        open(editor, currentSearchState);
      });
    };

    var showDialog = function (editor, currentSearchState) {
      return function () {
        open(editor, currentSearchState);
      };
    };
    var register$1 = function (editor, currentSearchState) {
      editor.ui.registry.addMenuItem('searchreplace', {
        text: 'Find and replace...',
        shortcut: 'Meta+F',
        onAction: showDialog(editor, currentSearchState),
        icon: 'search'
      });
      editor.ui.registry.addButton('searchreplace', {
        tooltip: 'Find and replace',
        onAction: showDialog(editor, currentSearchState),
        icon: 'search'
      });
      editor.shortcuts.add('Meta+F', '', showDialog(editor, currentSearchState));
    };

    function Plugin () {
      global.add('searchreplace', function (editor) {
        var currentSearchState = Cell({
          index: -1,
          count: 0,
          text: '',
          matchCase: false,
          wholeWord: false,
          inSelection: false
        });
        register(editor, currentSearchState);
        register$1(editor, currentSearchState);
        return get$1(editor, currentSearchState);
      });
    }

    Plugin();

}());
