/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    var global$3 = tinymce.util.Tools.resolve('tinymce.PluginManager');

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
    const eq$1 = t => a => t === a;
    const isString = isType$1('string');
    const isArray = isType$1('array');
    const isBoolean = isSimpleType('boolean');
    const isUndefined = eq$1(undefined);
    const isNullable = a => a === null || a === undefined;
    const isNonNullable = a => !isNullable(a);
    const isFunction = isSimpleType('function');
    const isNumber = isSimpleType('number');

    const noop = () => {
    };
    const compose1 = (fbc, fab) => a => fbc(fab(a));
    const constant = value => {
      return () => {
        return value;
      };
    };
    const identity = x => {
      return x;
    };
    const tripleEquals = (a, b) => {
      return a === b;
    };
    function curry(fn, ...initialArgs) {
      return (...restArgs) => {
        const all = initialArgs.concat(restArgs);
        return fn.apply(null, all);
      };
    }
    const call = f => {
      f();
    };
    const never = constant(false);
    const always = constant(true);

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

    const keys = Object.keys;
    const hasOwnProperty = Object.hasOwnProperty;
    const each$1 = (obj, f) => {
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
      each$1(obj, (x, i) => {
        (pred(x, i) ? onTrue : onFalse)(x, i);
      });
    };
    const filter$1 = (obj, pred) => {
      const t = {};
      internalFilter(obj, pred, objAcc(t), noop);
      return t;
    };
    const mapToArray = (obj, f) => {
      const r = [];
      each$1(obj, (value, name) => {
        r.push(f(value, name));
      });
      return r;
    };
    const values = obj => {
      return mapToArray(obj, identity);
    };
    const size = obj => {
      return keys(obj).length;
    };
    const get$4 = (obj, key) => {
      return has(obj, key) ? Optional.from(obj[key]) : Optional.none();
    };
    const has = (obj, key) => hasOwnProperty.call(obj, key);
    const hasNonNullableKey = (obj, key) => has(obj, key) && obj[key] !== undefined && obj[key] !== null;

    const nativeIndexOf = Array.prototype.indexOf;
    const nativePush = Array.prototype.push;
    const rawIndexOf = (ts, t) => nativeIndexOf.call(ts, t);
    const contains = (xs, x) => rawIndexOf(xs, x) > -1;
    const exists = (xs, pred) => {
      for (let i = 0, len = xs.length; i < len; i++) {
        const x = xs[i];
        if (pred(x, i)) {
          return true;
        }
      }
      return false;
    };
    const range = (num, f) => {
      const r = [];
      for (let i = 0; i < num; i++) {
        r.push(f(i));
      }
      return r;
    };
    const map = (xs, f) => {
      const len = xs.length;
      const r = new Array(len);
      for (let i = 0; i < len; i++) {
        const x = xs[i];
        r[i] = f(x, i);
      }
      return r;
    };
    const each = (xs, f) => {
      for (let i = 0, len = xs.length; i < len; i++) {
        const x = xs[i];
        f(x, i);
      }
    };
    const eachr = (xs, f) => {
      for (let i = xs.length - 1; i >= 0; i--) {
        const x = xs[i];
        f(x, i);
      }
    };
    const partition = (xs, pred) => {
      const pass = [];
      const fail = [];
      for (let i = 0, len = xs.length; i < len; i++) {
        const x = xs[i];
        const arr = pred(x, i) ? pass : fail;
        arr.push(x);
      }
      return {
        pass,
        fail
      };
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
    const foldr = (xs, f, acc) => {
      eachr(xs, (x, i) => {
        acc = f(acc, x, i);
      });
      return acc;
    };
    const foldl = (xs, f, acc) => {
      each(xs, (x, i) => {
        acc = f(acc, x, i);
      });
      return acc;
    };
    const findUntil = (xs, pred, until) => {
      for (let i = 0, len = xs.length; i < len; i++) {
        const x = xs[i];
        if (pred(x, i)) {
          return Optional.some(x);
        } else if (until(x, i)) {
          break;
        }
      }
      return Optional.none();
    };
    const find = (xs, pred) => {
      return findUntil(xs, pred, never);
    };
    const flatten$1 = xs => {
      const r = [];
      for (let i = 0, len = xs.length; i < len; ++i) {
        if (!isArray(xs[i])) {
          throw new Error('Arr.flatten item ' + i + ' was not an array, input: ' + xs);
        }
        nativePush.apply(r, xs[i]);
      }
      return r;
    };
    const bind = (xs, f) => flatten$1(map(xs, f));
    const forall = (xs, pred) => {
      for (let i = 0, len = xs.length; i < len; ++i) {
        const x = xs[i];
        if (pred(x, i) !== true) {
          return false;
        }
      }
      return true;
    };
    const mapToObject = (xs, f) => {
      const r = {};
      for (let i = 0, len = xs.length; i < len; i++) {
        const x = xs[i];
        r[String(x)] = f(x, i);
      }
      return r;
    };
    const get$3 = (xs, i) => i >= 0 && i < xs.length ? Optional.some(xs[i]) : Optional.none();
    const head = xs => get$3(xs, 0);
    const last = xs => get$3(xs, xs.length - 1);
    const findMap = (arr, f) => {
      for (let i = 0; i < arr.length; i++) {
        const r = f(arr[i], i);
        if (r.isSome()) {
          return r;
        }
      }
      return Optional.none();
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
      return fromDom$1(div.childNodes[0]);
    };
    const fromTag = (tag, scope) => {
      const doc = scope || document;
      const node = doc.createElement(tag);
      return fromDom$1(node);
    };
    const fromText = (text, scope) => {
      const doc = scope || document;
      const node = doc.createTextNode(text);
      return fromDom$1(node);
    };
    const fromDom$1 = node => {
      if (node === null || node === undefined) {
        throw new Error('Node cannot be null or undefined');
      }
      return { dom: node };
    };
    const fromPoint = (docElm, x, y) => Optional.from(docElm.dom.elementFromPoint(x, y)).map(fromDom$1);
    const SugarElement = {
      fromHtml,
      fromTag,
      fromText,
      fromDom: fromDom$1,
      fromPoint
    };

    typeof window !== 'undefined' ? window : Function('return this;')();

    const COMMENT = 8;
    const DOCUMENT = 9;
    const DOCUMENT_FRAGMENT = 11;
    const ELEMENT = 1;
    const TEXT = 3;

    const name = element => {
      const r = element.dom.nodeName;
      return r.toLowerCase();
    };
    const type = element => element.dom.nodeType;
    const isType = t => element => type(element) === t;
    const isComment = element => type(element) === COMMENT || name(element) === '#comment';
    const isElement = isType(ELEMENT);
    const isText = isType(TEXT);
    const isDocument = isType(DOCUMENT);
    const isDocumentFragment = isType(DOCUMENT_FRAGMENT);
    const isTag = tag => e => isElement(e) && name(e) === tag;

    const is$2 = (element, selector) => {
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
    const bypassSelector = dom => dom.nodeType !== ELEMENT && dom.nodeType !== DOCUMENT && dom.nodeType !== DOCUMENT_FRAGMENT || dom.childElementCount === 0;
    const all$1 = (selector, scope) => {
      const base = scope === undefined ? document : scope.dom;
      return bypassSelector(base) ? [] : map(base.querySelectorAll(selector), SugarElement.fromDom);
    };
    const one = (selector, scope) => {
      const base = scope === undefined ? document : scope.dom;
      return bypassSelector(base) ? Optional.none() : Optional.from(base.querySelector(selector)).map(SugarElement.fromDom);
    };

    const eq = (e1, e2) => e1.dom === e2.dom;
    const is$1 = is$2;

    const owner = element => SugarElement.fromDom(element.dom.ownerDocument);
    const documentOrOwner = dos => isDocument(dos) ? dos : owner(dos);
    const parent = element => Optional.from(element.dom.parentNode).map(SugarElement.fromDom);
    const parents = (element, isRoot) => {
      const stop = isFunction(isRoot) ? isRoot : never;
      let dom = element.dom;
      const ret = [];
      while (dom.parentNode !== null && dom.parentNode !== undefined) {
        const rawParent = dom.parentNode;
        const p = SugarElement.fromDom(rawParent);
        ret.push(p);
        if (stop(p) === true) {
          break;
        } else {
          dom = rawParent;
        }
      }
      return ret;
    };
    const prevSibling = element => Optional.from(element.dom.previousSibling).map(SugarElement.fromDom);
    const nextSibling = element => Optional.from(element.dom.nextSibling).map(SugarElement.fromDom);
    const children$3 = element => map(element.dom.childNodes, SugarElement.fromDom);
    const child$3 = (element, index) => {
      const cs = element.dom.childNodes;
      return Optional.from(cs[index]).map(SugarElement.fromDom);
    };
    const firstChild = element => child$3(element, 0);

    const isShadowRoot = dos => isDocumentFragment(dos) && isNonNullable(dos.dom.host);
    const supported = isFunction(Element.prototype.attachShadow) && isFunction(Node.prototype.getRootNode);
    const getRootNode = supported ? e => SugarElement.fromDom(e.dom.getRootNode()) : documentOrOwner;
    const getShadowRoot = e => {
      const r = getRootNode(e);
      return isShadowRoot(r) ? Optional.some(r) : Optional.none();
    };
    const getShadowHost = e => SugarElement.fromDom(e.dom.host);

    const inBody = element => {
      const dom = isText(element) ? element.dom.parentNode : element.dom;
      if (dom === undefined || dom === null || dom.ownerDocument === null) {
        return false;
      }
      const doc = dom.ownerDocument;
      return getShadowRoot(SugarElement.fromDom(dom)).fold(() => doc.body.contains(dom), compose1(inBody, getShadowHost));
    };

    const children$2 = (scope, predicate) => filter(children$3(scope), predicate);
    const descendants$1 = (scope, predicate) => {
      let result = [];
      each(children$3(scope), x => {
        if (predicate(x)) {
          result = result.concat([x]);
        }
        result = result.concat(descendants$1(x, predicate));
      });
      return result;
    };

    const children$1 = (scope, selector) => children$2(scope, e => is$2(e, selector));
    const descendants = (scope, selector) => all$1(selector, scope);

    var ClosestOrAncestor = (is, ancestor, scope, a, isRoot) => {
      if (is(scope, a)) {
        return Optional.some(scope);
      } else if (isFunction(isRoot) && isRoot(scope)) {
        return Optional.none();
      } else {
        return ancestor(scope, a, isRoot);
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
    const child$2 = (scope, predicate) => {
      const pred = node => predicate(SugarElement.fromDom(node));
      const result = find(scope.dom.childNodes, pred);
      return result.map(SugarElement.fromDom);
    };

    const ancestor = (scope, selector, isRoot) => ancestor$1(scope, e => is$2(e, selector), isRoot);
    const child$1 = (scope, selector) => child$2(scope, e => is$2(e, selector));
    const descendant = (scope, selector) => one(selector, scope);
    const closest = (scope, selector, isRoot) => {
      const is = (element, selector) => is$2(element, selector);
      return ClosestOrAncestor(is, ancestor, scope, selector, isRoot);
    };

    const rawSet = (dom, key, value) => {
      if (isString(value) || isBoolean(value) || isNumber(value)) {
        dom.setAttribute(key, value + '');
      } else {
        console.error('Invalid call to Attribute.set. Key ', key, ':: Value ', value, ':: Element ', dom);
        throw new Error('Attribute value was not simple');
      }
    };
    const set$2 = (element, key, value) => {
      rawSet(element.dom, key, value);
    };
    const setAll = (element, attrs) => {
      const dom = element.dom;
      each$1(attrs, (v, k) => {
        rawSet(dom, k, v);
      });
    };
    const get$2 = (element, key) => {
      const v = element.dom.getAttribute(key);
      return v === null ? undefined : v;
    };
    const getOpt = (element, key) => Optional.from(get$2(element, key));
    const remove$2 = (element, key) => {
      element.dom.removeAttribute(key);
    };
    const clone = element => foldl(element.dom.attributes, (acc, attr) => {
      acc[attr.name] = attr.value;
      return acc;
    }, {});

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
    const lift2 = (oa, ob, f) => oa.isSome() && ob.isSome() ? Optional.some(f(oa.getOrDie(), ob.getOrDie())) : Optional.none();
    const flatten = oot => oot.bind(identity);
    const someIf = (b, a) => b ? Optional.some(a) : Optional.none();

    const removeFromStart = (str, numChars) => {
      return str.substring(numChars);
    };

    const checkRange = (str, substr, start) => substr === '' || str.length >= substr.length && str.substr(start, start + substr.length) === substr;
    const removeLeading = (str, prefix) => {
      return startsWith(str, prefix) ? removeFromStart(str, prefix.length) : str;
    };
    const startsWith = (str, prefix) => {
      return checkRange(str, prefix, 0);
    };
    const blank = r => s => s.replace(r, '');
    const trim = blank(/^\s+|\s+$/g);
    const isNotEmpty = s => s.length > 0;
    const isEmpty = s => !isNotEmpty(s);
    const toInt = (value, radix = 10) => {
      const num = parseInt(value, radix);
      return isNaN(num) ? Optional.none() : Optional.some(num);
    };
    const toFloat = value => {
      const num = parseFloat(value);
      return isNaN(num) ? Optional.none() : Optional.some(num);
    };

    const isSupported = dom => dom.style !== undefined && isFunction(dom.style.getPropertyValue);

    const internalSet = (dom, property, value) => {
      if (!isString(value)) {
        console.error('Invalid call to CSS.set. Property ', property, ':: Value ', value, ':: Element ', dom);
        throw new Error('CSS value must be a string: ' + value);
      }
      if (isSupported(dom)) {
        dom.style.setProperty(property, value);
      }
    };
    const internalRemove = (dom, property) => {
      if (isSupported(dom)) {
        dom.style.removeProperty(property);
      }
    };
    const set$1 = (element, property, value) => {
      const dom = element.dom;
      internalSet(dom, property, value);
    };
    const get$1 = (element, property) => {
      const dom = element.dom;
      const styles = window.getComputedStyle(dom);
      const r = styles.getPropertyValue(property);
      return r === '' && !inBody(element) ? getUnsafeProperty(dom, property) : r;
    };
    const getUnsafeProperty = (dom, property) => isSupported(dom) ? dom.style.getPropertyValue(property) : '';
    const getRaw = (element, property) => {
      const dom = element.dom;
      const raw = getUnsafeProperty(dom, property);
      return Optional.from(raw).filter(r => r.length > 0);
    };
    const remove$1 = (element, property) => {
      const dom = element.dom;
      internalRemove(dom, property);
      if (is(getOpt(element, 'style').map(trim), '')) {
        remove$2(element, 'style');
      }
    };

    const getAttrValue = (cell, name, fallback = 0) => getOpt(cell, name).map(value => parseInt(value, 10)).getOr(fallback);

    const firstLayer = (scope, selector) => {
      return filterFirstLayer(scope, selector, always);
    };
    const filterFirstLayer = (scope, selector, predicate) => {
      return bind(children$3(scope), x => {
        if (is$2(x, selector)) {
          return predicate(x) ? [x] : [];
        } else {
          return filterFirstLayer(x, selector, predicate);
        }
      });
    };

    const validSectionList = [
      'tfoot',
      'thead',
      'tbody',
      'colgroup'
    ];
    const isValidSection = parentName => contains(validSectionList, parentName);
    const grid = (rows, columns) => ({
      rows,
      columns
    });
    const detail = (element, rowspan, colspan) => ({
      element,
      rowspan,
      colspan
    });
    const extended = (element, rowspan, colspan, row, column, isLocked) => ({
      element,
      rowspan,
      colspan,
      row,
      column,
      isLocked
    });
    const rowdetail = (element, cells, section) => ({
      element,
      cells,
      section
    });
    const bounds = (startRow, startCol, finishRow, finishCol) => ({
      startRow,
      startCol,
      finishRow,
      finishCol
    });
    const columnext = (element, colspan, column) => ({
      element,
      colspan,
      column
    });
    const colgroup = (element, columns) => ({
      element,
      columns
    });

    const lookup = (tags, element, isRoot = never) => {
      if (isRoot(element)) {
        return Optional.none();
      }
      if (contains(tags, name(element))) {
        return Optional.some(element);
      }
      const isRootOrUpperTable = elm => is$2(elm, 'table') || isRoot(elm);
      return ancestor(element, tags.join(','), isRootOrUpperTable);
    };
    const cell = (element, isRoot) => lookup([
      'td',
      'th'
    ], element, isRoot);
    const cells = ancestor => firstLayer(ancestor, 'th,td');
    const columns = ancestor => {
      if (is$2(ancestor, 'colgroup')) {
        return children$1(ancestor, 'col');
      } else {
        return bind(columnGroups(ancestor), columnGroup => children$1(columnGroup, 'col'));
      }
    };
    const table = (element, isRoot) => closest(element, 'table', isRoot);
    const rows = ancestor => firstLayer(ancestor, 'tr');
    const columnGroups = ancestor => table(ancestor).fold(constant([]), table => children$1(table, 'colgroup'));

    const fromRowsOrColGroups = (elems, getSection) => map(elems, row => {
      if (name(row) === 'colgroup') {
        const cells = map(columns(row), column => {
          const colspan = getAttrValue(column, 'span', 1);
          return detail(column, 1, colspan);
        });
        return rowdetail(row, cells, 'colgroup');
      } else {
        const cells$1 = map(cells(row), cell => {
          const rowspan = getAttrValue(cell, 'rowspan', 1);
          const colspan = getAttrValue(cell, 'colspan', 1);
          return detail(cell, rowspan, colspan);
        });
        return rowdetail(row, cells$1, getSection(row));
      }
    });
    const getParentSection = group => parent(group).map(parent => {
      const parentName = name(parent);
      return isValidSection(parentName) ? parentName : 'tbody';
    }).getOr('tbody');
    const fromTable$1 = table => {
      const rows$1 = rows(table);
      const columnGroups$1 = columnGroups(table);
      const elems = [
        ...columnGroups$1,
        ...rows$1
      ];
      return fromRowsOrColGroups(elems, getParentSection);
    };

    const LOCKED_COL_ATTR = 'data-snooker-locked-cols';
    const getLockedColumnsFromTable = table => getOpt(table, LOCKED_COL_ATTR).bind(lockedColStr => Optional.from(lockedColStr.match(/\d+/g))).map(lockedCols => mapToObject(lockedCols, always));

    const key = (row, column) => {
      return row + ',' + column;
    };
    const getAt = (warehouse, row, column) => Optional.from(warehouse.access[key(row, column)]);
    const findItem = (warehouse, item, comparator) => {
      const filtered = filterItems(warehouse, detail => {
        return comparator(item, detail.element);
      });
      return filtered.length > 0 ? Optional.some(filtered[0]) : Optional.none();
    };
    const filterItems = (warehouse, predicate) => {
      const all = bind(warehouse.all, r => {
        return r.cells;
      });
      return filter(all, predicate);
    };
    const generateColumns = rowData => {
      const columnsGroup = {};
      let index = 0;
      each(rowData.cells, column => {
        const colspan = column.colspan;
        range(colspan, columnIndex => {
          const colIndex = index + columnIndex;
          columnsGroup[colIndex] = columnext(column.element, colspan, colIndex);
        });
        index += colspan;
      });
      return columnsGroup;
    };
    const generate$1 = list => {
      const access = {};
      const cells = [];
      const tableOpt = head(list).map(rowData => rowData.element).bind(table);
      const lockedColumns = tableOpt.bind(getLockedColumnsFromTable).getOr({});
      let maxRows = 0;
      let maxColumns = 0;
      let rowCount = 0;
      const {
        pass: colgroupRows,
        fail: rows
      } = partition(list, rowData => rowData.section === 'colgroup');
      each(rows, rowData => {
        const currentRow = [];
        each(rowData.cells, rowCell => {
          let start = 0;
          while (access[key(rowCount, start)] !== undefined) {
            start++;
          }
          const isLocked = hasNonNullableKey(lockedColumns, start.toString());
          const current = extended(rowCell.element, rowCell.rowspan, rowCell.colspan, rowCount, start, isLocked);
          for (let occupiedColumnPosition = 0; occupiedColumnPosition < rowCell.colspan; occupiedColumnPosition++) {
            for (let occupiedRowPosition = 0; occupiedRowPosition < rowCell.rowspan; occupiedRowPosition++) {
              const rowPosition = rowCount + occupiedRowPosition;
              const columnPosition = start + occupiedColumnPosition;
              const newpos = key(rowPosition, columnPosition);
              access[newpos] = current;
              maxColumns = Math.max(maxColumns, columnPosition + 1);
            }
          }
          currentRow.push(current);
        });
        maxRows++;
        cells.push(rowdetail(rowData.element, currentRow, rowData.section));
        rowCount++;
      });
      const {columns, colgroups} = last(colgroupRows).map(rowData => {
        const columns = generateColumns(rowData);
        const colgroup$1 = colgroup(rowData.element, values(columns));
        return {
          colgroups: [colgroup$1],
          columns
        };
      }).getOrThunk(() => ({
        colgroups: [],
        columns: {}
      }));
      const grid$1 = grid(maxRows, maxColumns);
      return {
        grid: grid$1,
        access,
        all: cells,
        columns,
        colgroups
      };
    };
    const fromTable = table => {
      const list = fromTable$1(table);
      return generate$1(list);
    };
    const justCells = warehouse => bind(warehouse.all, w => w.cells);
    const justColumns = warehouse => values(warehouse.columns);
    const hasColumns = warehouse => keys(warehouse.columns).length > 0;
    const getColumnAt = (warehouse, columnIndex) => Optional.from(warehouse.columns[columnIndex]);
    const Warehouse = {
      fromTable,
      generate: generate$1,
      getAt,
      findItem,
      filterItems,
      justCells,
      justColumns,
      hasColumns,
      getColumnAt
    };

    var global$2 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    const getTDTHOverallStyle = (dom, elm, name) => {
      const cells = dom.select('td,th', elm);
      let firstChildStyle;
      for (let i = 0; i < cells.length; i++) {
        const currentStyle = dom.getStyle(cells[i], name);
        if (isUndefined(firstChildStyle)) {
          firstChildStyle = currentStyle;
        }
        if (firstChildStyle !== currentStyle) {
          return '';
        }
      }
      return firstChildStyle;
    };
    const setAlign = (editor, elm, name) => {
      global$2.each('left center right'.split(' '), align => {
        if (align !== name) {
          editor.formatter.remove('align' + align, {}, elm);
        }
      });
      if (name) {
        editor.formatter.apply('align' + name, {}, elm);
      }
    };
    const setVAlign = (editor, elm, name) => {
      global$2.each('top middle bottom'.split(' '), align => {
        if (align !== name) {
          editor.formatter.remove('valign' + align, {}, elm);
        }
      });
      if (name) {
        editor.formatter.apply('valign' + name, {}, elm);
      }
    };

    const fireTableModified = (editor, table, data) => {
      editor.dispatch('TableModified', {
        ...data,
        table
      });
    };

    const toNumber = (px, fallback) => toFloat(px).getOr(fallback);
    const getProp = (element, name, fallback) => toNumber(get$1(element, name), fallback);
    const calcContentBoxSize = (element, size, upper, lower) => {
      const paddingUpper = getProp(element, `padding-${ upper }`, 0);
      const paddingLower = getProp(element, `padding-${ lower }`, 0);
      const borderUpper = getProp(element, `border-${ upper }-width`, 0);
      const borderLower = getProp(element, `border-${ lower }-width`, 0);
      return size - paddingUpper - paddingLower - borderUpper - borderLower;
    };
    const getCalculatedWidth = (element, boxSizing) => {
      const dom = element.dom;
      const width = dom.getBoundingClientRect().width || dom.offsetWidth;
      return boxSizing === 'border-box' ? width : calcContentBoxSize(element, width, 'left', 'right');
    };
    const getInnerWidth = element => getCalculatedWidth(element, 'content-box');

    const getInner = getInnerWidth;

    var global$1 = tinymce.util.Tools.resolve('tinymce.Env');

    const defaultTableToolbar = 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol';
    const defaultCellBorderWidths = range(5, i => {
      const size = `${ i + 1 }px`;
      return {
        title: size,
        value: size
      };
    });
    const defaultCellBorderStyles = map([
      'Solid',
      'Dotted',
      'Dashed',
      'Double',
      'Groove',
      'Ridge',
      'Inset',
      'Outset',
      'None',
      'Hidden'
    ], type => {
      return {
        title: type,
        value: type.toLowerCase()
      };
    });
    const defaultWidth = '100%';
    const getPixelForcedWidth = editor => {
      var _a;
      const dom = editor.dom;
      const parentBlock = (_a = dom.getParent(editor.selection.getStart(), dom.isBlock)) !== null && _a !== void 0 ? _a : editor.getBody();
      return getInner(SugarElement.fromDom(parentBlock)) + 'px';
    };
    const determineDefaultStyles = (editor, defaultStyles) => {
      if (isResponsiveForced(editor) || !shouldStyleWithCss(editor)) {
        return defaultStyles;
      } else if (isPixelsForced(editor)) {
        return {
          ...defaultStyles,
          width: getPixelForcedWidth(editor)
        };
      } else {
        return {
          ...defaultStyles,
          width: defaultWidth
        };
      }
    };
    const determineDefaultAttributes = (editor, defaultAttributes) => {
      if (isResponsiveForced(editor) || shouldStyleWithCss(editor)) {
        return defaultAttributes;
      } else if (isPixelsForced(editor)) {
        return {
          ...defaultAttributes,
          width: getPixelForcedWidth(editor)
        };
      } else {
        return {
          ...defaultAttributes,
          width: defaultWidth
        };
      }
    };
    const option = name => editor => editor.options.get(name);
    const register = editor => {
      const registerOption = editor.options.register;
      registerOption('table_border_widths', {
        processor: 'object[]',
        default: defaultCellBorderWidths
      });
      registerOption('table_border_styles', {
        processor: 'object[]',
        default: defaultCellBorderStyles
      });
      registerOption('table_cell_advtab', {
        processor: 'boolean',
        default: true
      });
      registerOption('table_row_advtab', {
        processor: 'boolean',
        default: true
      });
      registerOption('table_advtab', {
        processor: 'boolean',
        default: true
      });
      registerOption('table_appearance_options', {
        processor: 'boolean',
        default: true
      });
      registerOption('table_grid', {
        processor: 'boolean',
        default: !global$1.deviceType.isTouch()
      });
      registerOption('table_cell_class_list', {
        processor: 'object[]',
        default: []
      });
      registerOption('table_row_class_list', {
        processor: 'object[]',
        default: []
      });
      registerOption('table_class_list', {
        processor: 'object[]',
        default: []
      });
      registerOption('table_toolbar', {
        processor: 'string',
        default: defaultTableToolbar
      });
      registerOption('table_background_color_map', {
        processor: 'object[]',
        default: []
      });
      registerOption('table_border_color_map', {
        processor: 'object[]',
        default: []
      });
    };
    const getTableSizingMode = option('table_sizing_mode');
    const getTableBorderWidths = option('table_border_widths');
    const getTableBorderStyles = option('table_border_styles');
    const hasAdvancedCellTab = option('table_cell_advtab');
    const hasAdvancedRowTab = option('table_row_advtab');
    const hasAdvancedTableTab = option('table_advtab');
    const hasAppearanceOptions = option('table_appearance_options');
    const hasTableGrid = option('table_grid');
    const shouldStyleWithCss = option('table_style_by_css');
    const getCellClassList = option('table_cell_class_list');
    const getRowClassList = option('table_row_class_list');
    const getTableClassList = option('table_class_list');
    const getToolbar = option('table_toolbar');
    const getTableBackgroundColorMap = option('table_background_color_map');
    const getTableBorderColorMap = option('table_border_color_map');
    const isPixelsForced = editor => getTableSizingMode(editor) === 'fixed';
    const isResponsiveForced = editor => getTableSizingMode(editor) === 'responsive';
    const getDefaultStyles = editor => {
      const options = editor.options;
      const defaultStyles = options.get('table_default_styles');
      return options.isSet('table_default_styles') ? defaultStyles : determineDefaultStyles(editor, defaultStyles);
    };
    const getDefaultAttributes = editor => {
      const options = editor.options;
      const defaultAttributes = options.get('table_default_attributes');
      return options.isSet('table_default_attributes') ? defaultAttributes : determineDefaultAttributes(editor, defaultAttributes);
    };

    const getNodeName = elm => elm.nodeName.toLowerCase();
    const getBody = editor => SugarElement.fromDom(editor.getBody());
    const getIsRoot = editor => element => eq(element, getBody(editor));
    const removePxSuffix = size => size ? size.replace(/px$/, '') : '';
    const addPxSuffix = size => /^\d+(\.\d+)?$/.test(size) ? size + 'px' : size;
    const getSelectionStart = editor => SugarElement.fromDom(editor.selection.getStart());
    const getSelectionEnd = editor => SugarElement.fromDom(editor.selection.getEnd());

    const isWithin = (bounds, detail) => {
      return detail.column >= bounds.startCol && detail.column + detail.colspan - 1 <= bounds.finishCol && detail.row >= bounds.startRow && detail.row + detail.rowspan - 1 <= bounds.finishRow;
    };
    const isRectangular = (warehouse, bounds) => {
      let isRect = true;
      const detailIsWithin = curry(isWithin, bounds);
      for (let i = bounds.startRow; i <= bounds.finishRow; i++) {
        for (let j = bounds.startCol; j <= bounds.finishCol; j++) {
          isRect = isRect && Warehouse.getAt(warehouse, i, j).exists(detailIsWithin);
        }
      }
      return isRect ? Optional.some(bounds) : Optional.none();
    };

    const getBounds = (detailA, detailB) => {
      return bounds(Math.min(detailA.row, detailB.row), Math.min(detailA.column, detailB.column), Math.max(detailA.row + detailA.rowspan - 1, detailB.row + detailB.rowspan - 1), Math.max(detailA.column + detailA.colspan - 1, detailB.column + detailB.colspan - 1));
    };
    const getAnyBox = (warehouse, startCell, finishCell) => {
      const startCoords = Warehouse.findItem(warehouse, startCell, eq);
      const finishCoords = Warehouse.findItem(warehouse, finishCell, eq);
      return startCoords.bind(sc => {
        return finishCoords.map(fc => {
          return getBounds(sc, fc);
        });
      });
    };
    const getBox$1 = (warehouse, startCell, finishCell) => {
      return getAnyBox(warehouse, startCell, finishCell).bind(bounds => {
        return isRectangular(warehouse, bounds);
      });
    };

    const getBox = (table, first, last) => {
      const warehouse = getWarehouse(table);
      return getBox$1(warehouse, first, last);
    };
    const getWarehouse = Warehouse.fromTable;

    const before = (marker, element) => {
      const parent$1 = parent(marker);
      parent$1.each(v => {
        v.dom.insertBefore(element.dom, marker.dom);
      });
    };
    const after$1 = (marker, element) => {
      const sibling = nextSibling(marker);
      sibling.fold(() => {
        const parent$1 = parent(marker);
        parent$1.each(v => {
          append$1(v, element);
        });
      }, v => {
        before(v, element);
      });
    };
    const prepend = (parent, element) => {
      const firstChild$1 = firstChild(parent);
      firstChild$1.fold(() => {
        append$1(parent, element);
      }, v => {
        parent.dom.insertBefore(element.dom, v.dom);
      });
    };
    const append$1 = (parent, element) => {
      parent.dom.appendChild(element.dom);
    };
    const wrap = (element, wrapper) => {
      before(element, wrapper);
      append$1(wrapper, element);
    };

    const after = (marker, elements) => {
      each(elements, (x, i) => {
        const e = i === 0 ? marker : elements[i - 1];
        after$1(e, x);
      });
    };
    const append = (parent, elements) => {
      each(elements, x => {
        append$1(parent, x);
      });
    };

    const remove = element => {
      const dom = element.dom;
      if (dom.parentNode !== null) {
        dom.parentNode.removeChild(dom);
      }
    };
    const unwrap = wrapper => {
      const children = children$3(wrapper);
      if (children.length > 0) {
        after(wrapper, children);
      }
      remove(wrapper);
    };

    const NodeValue = (is, name) => {
      const get = element => {
        if (!is(element)) {
          throw new Error('Can only get ' + name + ' value of a ' + name + ' node');
        }
        return getOption(element).getOr('');
      };
      const getOption = element => is(element) ? Optional.from(element.dom.nodeValue) : Optional.none();
      const set = (element, value) => {
        if (!is(element)) {
          throw new Error('Can only set raw ' + name + ' value of a ' + name + ' node');
        }
        element.dom.nodeValue = value;
      };
      return {
        get,
        getOption,
        set
      };
    };

    const api = NodeValue(isText, 'text');
    const get = element => api.get(element);
    const set = (element, value) => api.set(element, value);

    var TagBoundaries = [
      'body',
      'p',
      'div',
      'article',
      'aside',
      'figcaption',
      'figure',
      'footer',
      'header',
      'nav',
      'section',
      'ol',
      'ul',
      'li',
      'table',
      'thead',
      'tbody',
      'tfoot',
      'caption',
      'tr',
      'td',
      'th',
      'h1',
      'h2',
      'h3',
      'h4',
      'h5',
      'h6',
      'blockquote',
      'pre',
      'address'
    ];

    var DomUniverse = () => {
      const clone$1 = element => {
        return SugarElement.fromDom(element.dom.cloneNode(false));
      };
      const document = element => documentOrOwner(element).dom;
      const isBoundary = element => {
        if (!isElement(element)) {
          return false;
        }
        if (name(element) === 'body') {
          return true;
        }
        return contains(TagBoundaries, name(element));
      };
      const isEmptyTag = element => {
        if (!isElement(element)) {
          return false;
        }
        return contains([
          'br',
          'img',
          'hr',
          'input'
        ], name(element));
      };
      const isNonEditable = element => isElement(element) && get$2(element, 'contenteditable') === 'false';
      const comparePosition = (element, other) => {
        return element.dom.compareDocumentPosition(other.dom);
      };
      const copyAttributesTo = (source, destination) => {
        const as = clone(source);
        setAll(destination, as);
      };
      const isSpecial = element => {
        const tag = name(element);
        return contains([
          'script',
          'noscript',
          'iframe',
          'noframes',
          'noembed',
          'title',
          'style',
          'textarea',
          'xmp'
        ], tag);
      };
      const getLanguage = element => isElement(element) ? getOpt(element, 'lang') : Optional.none();
      return {
        up: constant({
          selector: ancestor,
          closest: closest,
          predicate: ancestor$1,
          all: parents
        }),
        down: constant({
          selector: descendants,
          predicate: descendants$1
        }),
        styles: constant({
          get: get$1,
          getRaw: getRaw,
          set: set$1,
          remove: remove$1
        }),
        attrs: constant({
          get: get$2,
          set: set$2,
          remove: remove$2,
          copyTo: copyAttributesTo
        }),
        insert: constant({
          before: before,
          after: after$1,
          afterAll: after,
          append: append$1,
          appendAll: append,
          prepend: prepend,
          wrap: wrap
        }),
        remove: constant({
          unwrap: unwrap,
          remove: remove
        }),
        create: constant({
          nu: SugarElement.fromTag,
          clone: clone$1,
          text: SugarElement.fromText
        }),
        query: constant({
          comparePosition,
          prevSibling: prevSibling,
          nextSibling: nextSibling
        }),
        property: constant({
          children: children$3,
          name: name,
          parent: parent,
          document,
          isText: isText,
          isComment: isComment,
          isElement: isElement,
          isSpecial,
          getLanguage,
          getText: get,
          setText: set,
          isBoundary,
          isEmptyTag,
          isNonEditable
        }),
        eq: eq,
        is: is$1
      };
    };

    const all = (universe, look, elements, f) => {
      const head = elements[0];
      const tail = elements.slice(1);
      return f(universe, look, head, tail);
    };
    const oneAll = (universe, look, elements) => {
      return elements.length > 0 ? all(universe, look, elements, unsafeOne) : Optional.none();
    };
    const unsafeOne = (universe, look, head, tail) => {
      const start = look(universe, head);
      return foldr(tail, (b, a) => {
        const current = look(universe, a);
        return commonElement(universe, b, current);
      }, start);
    };
    const commonElement = (universe, start, end) => {
      return start.bind(s => {
        return end.filter(curry(universe.eq, s));
      });
    };

    const sharedOne$1 = oneAll;

    const universe = DomUniverse();
    const sharedOne = (look, elements) => {
      return sharedOne$1(universe, (_universe, element) => {
        return look(element);
      }, elements);
    };

    const lookupTable = container => {
      return ancestor(container, 'table');
    };
    const retrieve$1 = (container, selector) => {
      const sels = descendants(container, selector);
      return sels.length > 0 ? Optional.some(sels) : Optional.none();
    };
    const getEdges = (container, firstSelectedSelector, lastSelectedSelector) => {
      return descendant(container, firstSelectedSelector).bind(first => {
        return descendant(container, lastSelectedSelector).bind(last => {
          return sharedOne(lookupTable, [
            first,
            last
          ]).map(table => {
            return {
              first,
              last,
              table
            };
          });
        });
      });
    };

    const retrieve = (container, selector) => {
      return retrieve$1(container, selector);
    };
    const retrieveBox = (container, firstSelectedSelector, lastSelectedSelector) => {
      return getEdges(container, firstSelectedSelector, lastSelectedSelector).bind(edges => {
        const isRoot = ancestor => {
          return eq(container, ancestor);
        };
        const sectionSelector = 'thead,tfoot,tbody,table';
        const firstAncestor = ancestor(edges.first, sectionSelector, isRoot);
        const lastAncestor = ancestor(edges.last, sectionSelector, isRoot);
        return firstAncestor.bind(fA => {
          return lastAncestor.bind(lA => {
            return eq(fA, lA) ? getBox(edges.table, edges.first, edges.last) : Optional.none();
          });
        });
      });
    };

    const fromDom = nodes => map(nodes, SugarElement.fromDom);

    const strSelected = 'data-mce-selected';
    const strSelectedSelector = 'td[' + strSelected + '],th[' + strSelected + ']';
    const strFirstSelected = 'data-mce-first-selected';
    const strFirstSelectedSelector = 'td[' + strFirstSelected + '],th[' + strFirstSelected + ']';
    const strLastSelected = 'data-mce-last-selected';
    const strLastSelectedSelector = 'td[' + strLastSelected + '],th[' + strLastSelected + ']';
    const ephemera = {
      selected: strSelected,
      selectedSelector: strSelectedSelector,
      firstSelected: strFirstSelected,
      firstSelectedSelector: strFirstSelectedSelector,
      lastSelected: strLastSelected,
      lastSelectedSelector: strLastSelectedSelector
    };

    const getSelectionCellFallback = element => table(element).bind(table => retrieve(table, ephemera.firstSelectedSelector)).fold(constant(element), cells => cells[0]);
    const getSelectionFromSelector = selector => (initCell, isRoot) => {
      const cellName = name(initCell);
      const cell = cellName === 'col' || cellName === 'colgroup' ? getSelectionCellFallback(initCell) : initCell;
      return closest(cell, selector, isRoot);
    };
    const getSelectionCellOrCaption = getSelectionFromSelector('th,td,caption');
    const getSelectionCell = getSelectionFromSelector('th,td');
    const getCellsFromSelection = editor => fromDom(editor.model.table.getSelectedCells());
    const getRowsFromSelection = (selected, selector) => {
      const cellOpt = getSelectionCell(selected);
      const rowsOpt = cellOpt.bind(cell => table(cell)).map(table => rows(table));
      return lift2(cellOpt, rowsOpt, (cell, rows) => filter(rows, row => exists(fromDom(row.dom.cells), rowCell => get$2(rowCell, selector) === '1' || eq(rowCell, cell)))).getOr([]);
    };

    const verticalAlignValues = [
      {
        text: 'None',
        value: ''
      },
      {
        text: 'Top',
        value: 'top'
      },
      {
        text: 'Middle',
        value: 'middle'
      },
      {
        text: 'Bottom',
        value: 'bottom'
      }
    ];

    const hexColour = value => ({ value });
    const shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    const longformRegex = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i;
    const isHexString = hex => shorthandRegex.test(hex) || longformRegex.test(hex);
    const normalizeHex = hex => removeLeading(hex, '#').toUpperCase();
    const fromString$1 = hex => isHexString(hex) ? Optional.some({ value: normalizeHex(hex) }) : Optional.none();
    const toHex = component => {
      const hex = component.toString(16);
      return (hex.length === 1 ? '0' + hex : hex).toUpperCase();
    };
    const fromRgba = rgbaColour => {
      const value = toHex(rgbaColour.red) + toHex(rgbaColour.green) + toHex(rgbaColour.blue);
      return hexColour(value);
    };

    const rgbRegex = /^\s*rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)\s*$/i;
    const rgbaRegex = /^\s*rgba\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d?(?:\.\d+)?)\s*\)\s*$/i;
    const rgbaColour = (red, green, blue, alpha) => ({
      red,
      green,
      blue,
      alpha
    });
    const fromStringValues = (red, green, blue, alpha) => {
      const r = parseInt(red, 10);
      const g = parseInt(green, 10);
      const b = parseInt(blue, 10);
      const a = parseFloat(alpha);
      return rgbaColour(r, g, b, a);
    };
    const fromString = rgbaString => {
      if (rgbaString === 'transparent') {
        return Optional.some(rgbaColour(0, 0, 0, 0));
      }
      const rgbMatch = rgbRegex.exec(rgbaString);
      if (rgbMatch !== null) {
        return Optional.some(fromStringValues(rgbMatch[1], rgbMatch[2], rgbMatch[3], '1'));
      }
      const rgbaMatch = rgbaRegex.exec(rgbaString);
      if (rgbaMatch !== null) {
        return Optional.some(fromStringValues(rgbaMatch[1], rgbaMatch[2], rgbaMatch[3], rgbaMatch[4]));
      }
      return Optional.none();
    };

    const anyToHex = color => fromString$1(color).orThunk(() => fromString(color).map(fromRgba)).getOrThunk(() => {
      const canvas = document.createElement('canvas');
      canvas.height = 1;
      canvas.width = 1;
      const canvasContext = canvas.getContext('2d');
      canvasContext.clearRect(0, 0, canvas.width, canvas.height);
      canvasContext.fillStyle = '#FFFFFF';
      canvasContext.fillStyle = color;
      canvasContext.fillRect(0, 0, 1, 1);
      const rgba = canvasContext.getImageData(0, 0, 1, 1).data;
      const r = rgba[0];
      const g = rgba[1];
      const b = rgba[2];
      const a = rgba[3];
      return fromRgba(rgbaColour(r, g, b, a));
    });
    const rgbaToHexString = color => fromString(color).map(fromRgba).map(h => '#' + h.value).getOr(color);

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

    const singleton = doRevoke => {
      const subject = Cell(Optional.none());
      const revoke = () => subject.get().each(doRevoke);
      const clear = () => {
        revoke();
        subject.set(Optional.none());
      };
      const isSet = () => subject.get().isSome();
      const get = () => subject.get();
      const set = s => {
        revoke();
        subject.set(Optional.some(s));
      };
      return {
        clear,
        isSet,
        get,
        set
      };
    };
    const unbindable = () => singleton(s => s.unbind());

    const onSetupToggle = (editor, formatName, formatValue) => {
      return api => {
        const boundCallback = unbindable();
        const isNone = isEmpty(formatValue);
        const init = () => {
          const selectedCells = getCellsFromSelection(editor);
          const checkNode = cell => editor.formatter.match(formatName, { value: formatValue }, cell.dom, isNone);
          if (isNone) {
            api.setActive(!exists(selectedCells, checkNode));
            boundCallback.set(editor.formatter.formatChanged(formatName, match => api.setActive(!match), true));
          } else {
            api.setActive(forall(selectedCells, checkNode));
            boundCallback.set(editor.formatter.formatChanged(formatName, api.setActive, false, { value: formatValue }));
          }
        };
        editor.initialized ? init() : editor.on('init', init);
        return boundCallback.clear;
      };
    };
    const isListGroup = item => hasNonNullableKey(item, 'menu');
    const buildListItems = items => map(items, item => {
      const text = item.text || item.title || '';
      if (isListGroup(item)) {
        return {
          text,
          items: buildListItems(item.menu)
        };
      } else {
        return {
          text,
          value: item.value
        };
      }
    });
    const buildMenuItems = (editor, items, format, onAction) => map(items, item => {
      const text = item.text || item.title;
      if (isListGroup(item)) {
        return {
          type: 'nestedmenuitem',
          text,
          getSubmenuItems: () => buildMenuItems(editor, item.menu, format, onAction)
        };
      } else {
        return {
          text,
          type: 'togglemenuitem',
          onAction: () => onAction(item.value),
          onSetup: onSetupToggle(editor, format, item.value)
        };
      }
    });
    const applyTableCellStyle = (editor, style) => value => {
      editor.execCommand('mceTableApplyCellStyle', false, { [style]: value });
    };
    const filterNoneItem = list => bind(list, item => {
      if (isListGroup(item)) {
        return [{
            ...item,
            menu: filterNoneItem(item.menu)
          }];
      } else {
        return isNotEmpty(item.value) ? [item] : [];
      }
    });
    const generateMenuItemsCallback = (editor, items, format, onAction) => callback => callback(buildMenuItems(editor, items, format, onAction));
    const buildColorMenu = (editor, colorList, style) => {
      const colorMap = map(colorList, entry => ({
        text: entry.title,
        value: '#' + anyToHex(entry.value).value,
        type: 'choiceitem'
      }));
      return [{
          type: 'fancymenuitem',
          fancytype: 'colorswatch',
          initData: {
            colors: colorMap.length > 0 ? colorMap : undefined,
            allowCustomColors: false
          },
          onAction: data => {
            const value = data.value === 'remove' ? '' : data.value;
            editor.execCommand('mceTableApplyCellStyle', false, { [style]: value });
          }
        }];
    };
    const changeRowHeader = editor => () => {
      const currentType = editor.queryCommandValue('mceTableRowType');
      const newType = currentType === 'header' ? 'body' : 'header';
      editor.execCommand('mceTableRowType', false, { type: newType });
    };
    const changeColumnHeader = editor => () => {
      const currentType = editor.queryCommandValue('mceTableColType');
      const newType = currentType === 'th' ? 'td' : 'th';
      editor.execCommand('mceTableColType', false, { type: newType });
    };

    const getClassList$1 = editor => {
      const classes = buildListItems(getCellClassList(editor));
      if (classes.length > 0) {
        return Optional.some({
          name: 'class',
          type: 'listbox',
          label: 'Class',
          items: classes
        });
      }
      return Optional.none();
    };
    const children = [
      {
        name: 'width',
        type: 'input',
        label: 'Width'
      },
      {
        name: 'height',
        type: 'input',
        label: 'Height'
      },
      {
        name: 'celltype',
        type: 'listbox',
        label: 'Cell type',
        items: [
          {
            text: 'Cell',
            value: 'td'
          },
          {
            text: 'Header cell',
            value: 'th'
          }
        ]
      },
      {
        name: 'scope',
        type: 'listbox',
        label: 'Scope',
        items: [
          {
            text: 'None',
            value: ''
          },
          {
            text: 'Row',
            value: 'row'
          },
          {
            text: 'Column',
            value: 'col'
          },
          {
            text: 'Row group',
            value: 'rowgroup'
          },
          {
            text: 'Column group',
            value: 'colgroup'
          }
        ]
      },
      {
        name: 'halign',
        type: 'listbox',
        label: 'Horizontal align',
        items: [
          {
            text: 'None',
            value: ''
          },
          {
            text: 'Left',
            value: 'left'
          },
          {
            text: 'Center',
            value: 'center'
          },
          {
            text: 'Right',
            value: 'right'
          }
        ]
      },
      {
        name: 'valign',
        type: 'listbox',
        label: 'Vertical align',
        items: verticalAlignValues
      }
    ];
    const getItems$2 = editor => children.concat(getClassList$1(editor).toArray());

    const getAdvancedTab = (editor, dialogName) => {
      const emptyBorderStyle = [{
          text: 'Select...',
          value: ''
        }];
      const advTabItems = [
        {
          name: 'borderstyle',
          type: 'listbox',
          label: 'Border style',
          items: emptyBorderStyle.concat(buildListItems(getTableBorderStyles(editor)))
        },
        {
          name: 'bordercolor',
          type: 'colorinput',
          label: 'Border color'
        },
        {
          name: 'backgroundcolor',
          type: 'colorinput',
          label: 'Background color'
        }
      ];
      const borderWidth = {
        name: 'borderwidth',
        type: 'input',
        label: 'Border width'
      };
      const items = dialogName === 'cell' ? [borderWidth].concat(advTabItems) : advTabItems;
      return {
        title: 'Advanced',
        name: 'advanced',
        items
      };
    };

    const normal = (editor, element) => {
      const dom = editor.dom;
      const setAttrib = (attr, value) => {
        dom.setAttrib(element, attr, value);
      };
      const setStyle = (prop, value) => {
        dom.setStyle(element, prop, value);
      };
      const setFormat = (formatName, value) => {
        if (value === '') {
          editor.formatter.remove(formatName, { value: null }, element, true);
        } else {
          editor.formatter.apply(formatName, { value }, element);
        }
      };
      return {
        setAttrib,
        setStyle,
        setFormat
      };
    };
    const DomModifier = { normal };

    const isHeaderCell = isTag('th');
    const getRowHeaderType = (isHeaderRow, isHeaderCells) => {
      if (isHeaderRow && isHeaderCells) {
        return 'sectionCells';
      } else if (isHeaderRow) {
        return 'section';
      } else {
        return 'cells';
      }
    };
    const getRowType$1 = row => {
      const isHeaderRow = row.section === 'thead';
      const isHeaderCells = is(findCommonCellType(row.cells), 'th');
      if (row.section === 'tfoot') {
        return { type: 'footer' };
      } else if (isHeaderRow || isHeaderCells) {
        return {
          type: 'header',
          subType: getRowHeaderType(isHeaderRow, isHeaderCells)
        };
      } else {
        return { type: 'body' };
      }
    };
    const findCommonCellType = cells => {
      const headerCells = filter(cells, cell => isHeaderCell(cell.element));
      if (headerCells.length === 0) {
        return Optional.some('td');
      } else if (headerCells.length === cells.length) {
        return Optional.some('th');
      } else {
        return Optional.none();
      }
    };
    const findCommonRowType = rows => {
      const rowTypes = map(rows, row => getRowType$1(row).type);
      const hasHeader = contains(rowTypes, 'header');
      const hasFooter = contains(rowTypes, 'footer');
      if (!hasHeader && !hasFooter) {
        return Optional.some('body');
      } else {
        const hasBody = contains(rowTypes, 'body');
        if (hasHeader && !hasBody && !hasFooter) {
          return Optional.some('header');
        } else if (!hasHeader && !hasBody && hasFooter) {
          return Optional.some('footer');
        } else {
          return Optional.none();
        }
      }
    };

    const cached = f => {
      let called = false;
      let r;
      return (...args) => {
        if (!called) {
          called = true;
          r = f.apply(null, args);
        }
        return r;
      };
    };

    const findInWarehouse = (warehouse, element) => findMap(warehouse.all, r => find(r.cells, e => eq(element, e.element)));
    const extractCells = (warehouse, target, predicate) => {
      const details = map(target.selection, cell$1 => {
        return cell(cell$1).bind(lc => findInWarehouse(warehouse, lc)).filter(predicate);
      });
      const cells = cat(details);
      return someIf(cells.length > 0, cells);
    };
    const onMergable = (_warehouse, target) => target.mergable;
    const onUnmergable = (_warehouse, target) => target.unmergable;
    const onCells = (warehouse, target) => extractCells(warehouse, target, always);
    const isUnlockedTableCell = (warehouse, cell) => findInWarehouse(warehouse, cell).exists(detail => !detail.isLocked);
    const allUnlocked = (warehouse, cells) => forall(cells, cell => isUnlockedTableCell(warehouse, cell));
    const onUnlockedMergable = (warehouse, target) => onMergable(warehouse, target).filter(mergeable => allUnlocked(warehouse, mergeable.cells));
    const onUnlockedUnmergable = (warehouse, target) => onUnmergable(warehouse, target).filter(cells => allUnlocked(warehouse, cells));

    const generate = cases => {
      if (!isArray(cases)) {
        throw new Error('cases must be an array');
      }
      if (cases.length === 0) {
        throw new Error('there must be at least one case');
      }
      const constructors = [];
      const adt = {};
      each(cases, (acase, count) => {
        const keys$1 = keys(acase);
        if (keys$1.length !== 1) {
          throw new Error('one and only one name per case');
        }
        const key = keys$1[0];
        const value = acase[key];
        if (adt[key] !== undefined) {
          throw new Error('duplicate key detected:' + key);
        } else if (key === 'cata') {
          throw new Error('cannot have a case named cata (sorry)');
        } else if (!isArray(value)) {
          throw new Error('case arguments must be an array');
        }
        constructors.push(key);
        adt[key] = (...args) => {
          const argLength = args.length;
          if (argLength !== value.length) {
            throw new Error('Wrong number of arguments to case ' + key + '. Expected ' + value.length + ' (' + value + '), got ' + argLength);
          }
          const match = branches => {
            const branchKeys = keys(branches);
            if (constructors.length !== branchKeys.length) {
              throw new Error('Wrong number of arguments to match. Expected: ' + constructors.join(',') + '\nActual: ' + branchKeys.join(','));
            }
            const allReqd = forall(constructors, reqKey => {
              return contains(branchKeys, reqKey);
            });
            if (!allReqd) {
              throw new Error('Not all branches were specified when using match. Specified: ' + branchKeys.join(', ') + '\nRequired: ' + constructors.join(', '));
            }
            return branches[key].apply(null, args);
          };
          return {
            fold: (...foldArgs) => {
              if (foldArgs.length !== cases.length) {
                throw new Error('Wrong number of arguments to fold. Expected ' + cases.length + ', got ' + foldArgs.length);
              }
              const target = foldArgs[count];
              return target.apply(null, args);
            },
            match,
            log: label => {
              console.log(label, {
                constructors,
                constructor: key,
                params: args
              });
            }
          };
        };
      });
      return adt;
    };
    const Adt = { generate };

    const adt = Adt.generate([
      { none: [] },
      { only: ['index'] },
      {
        left: [
          'index',
          'next'
        ]
      },
      {
        middle: [
          'prev',
          'index',
          'next'
        ]
      },
      {
        right: [
          'prev',
          'index'
        ]
      }
    ]);
    ({ ...adt });

    const opGetRowsType = (table, target) => {
      const house = Warehouse.fromTable(table);
      const details = onCells(house, target);
      return details.bind(selectedCells => {
        const lastSelectedCell = selectedCells[selectedCells.length - 1];
        const minRowRange = selectedCells[0].row;
        const maxRowRange = lastSelectedCell.row + lastSelectedCell.rowspan;
        const selectedRows = house.all.slice(minRowRange, maxRowRange);
        return findCommonRowType(selectedRows);
      }).getOr('');
    };
    const getRowsType = opGetRowsType;

    const rgbToHex = value => startsWith(value, 'rgb') ? rgbaToHexString(value) : value;
    const extractAdvancedStyles = elm => {
      const element = SugarElement.fromDom(elm);
      return {
        borderwidth: getRaw(element, 'border-width').getOr(''),
        borderstyle: getRaw(element, 'border-style').getOr(''),
        bordercolor: getRaw(element, 'border-color').map(rgbToHex).getOr(''),
        backgroundcolor: getRaw(element, 'background-color').map(rgbToHex).getOr('')
      };
    };
    const getSharedValues = data => {
      const baseData = data[0];
      const comparisonData = data.slice(1);
      each(comparisonData, items => {
        each(keys(baseData), key => {
          each$1(items, (itemValue, itemKey) => {
            const comparisonValue = baseData[key];
            if (comparisonValue !== '' && key === itemKey) {
              if (comparisonValue !== itemValue) {
                baseData[key] = '';
              }
            }
          });
        });
      });
      return baseData;
    };
    const getAlignment = (formats, formatName, editor, elm) => find(formats, name => !isUndefined(editor.formatter.matchNode(elm, formatName + name))).getOr('');
    const getHAlignment = curry(getAlignment, [
      'left',
      'center',
      'right'
    ], 'align');
    const getVAlignment = curry(getAlignment, [
      'top',
      'middle',
      'bottom'
    ], 'valign');
    const extractDataFromSettings = (editor, hasAdvTableTab) => {
      const style = getDefaultStyles(editor);
      const attrs = getDefaultAttributes(editor);
      const extractAdvancedStyleData = () => ({
        borderstyle: get$4(style, 'border-style').getOr(''),
        bordercolor: rgbToHex(get$4(style, 'border-color').getOr('')),
        backgroundcolor: rgbToHex(get$4(style, 'background-color').getOr(''))
      });
      const defaultData = {
        height: '',
        width: '100%',
        cellspacing: '',
        cellpadding: '',
        caption: false,
        class: '',
        align: '',
        border: ''
      };
      const getBorder = () => {
        const borderWidth = style['border-width'];
        if (shouldStyleWithCss(editor) && borderWidth) {
          return { border: borderWidth };
        }
        return get$4(attrs, 'border').fold(() => ({}), border => ({ border }));
      };
      const advStyle = hasAdvTableTab ? extractAdvancedStyleData() : {};
      const getCellPaddingCellSpacing = () => {
        const spacing = get$4(style, 'border-spacing').or(get$4(attrs, 'cellspacing')).fold(() => ({}), cellspacing => ({ cellspacing }));
        const padding = get$4(style, 'border-padding').or(get$4(attrs, 'cellpadding')).fold(() => ({}), cellpadding => ({ cellpadding }));
        return {
          ...spacing,
          ...padding
        };
      };
      const data = {
        ...defaultData,
        ...style,
        ...attrs,
        ...advStyle,
        ...getBorder(),
        ...getCellPaddingCellSpacing()
      };
      return data;
    };
    const getRowType = elm => table(SugarElement.fromDom(elm)).map(table => {
      const target = { selection: fromDom(elm.cells) };
      return getRowsType(table, target);
    }).getOr('');
    const extractDataFromTableElement = (editor, elm, hasAdvTableTab) => {
      const getBorder = (dom, elm) => {
        const optBorderWidth = getRaw(SugarElement.fromDom(elm), 'border-width');
        if (shouldStyleWithCss(editor) && optBorderWidth.isSome()) {
          return optBorderWidth.getOr('');
        }
        return dom.getAttrib(elm, 'border') || getTDTHOverallStyle(editor.dom, elm, 'border-width') || getTDTHOverallStyle(editor.dom, elm, 'border') || '';
      };
      const dom = editor.dom;
      const cellspacing = shouldStyleWithCss(editor) ? dom.getStyle(elm, 'border-spacing') || dom.getAttrib(elm, 'cellspacing') : dom.getAttrib(elm, 'cellspacing') || dom.getStyle(elm, 'border-spacing');
      const cellpadding = shouldStyleWithCss(editor) ? getTDTHOverallStyle(dom, elm, 'padding') || dom.getAttrib(elm, 'cellpadding') : dom.getAttrib(elm, 'cellpadding') || getTDTHOverallStyle(dom, elm, 'padding');
      return {
        width: dom.getStyle(elm, 'width') || dom.getAttrib(elm, 'width'),
        height: dom.getStyle(elm, 'height') || dom.getAttrib(elm, 'height'),
        cellspacing: cellspacing !== null && cellspacing !== void 0 ? cellspacing : '',
        cellpadding: cellpadding !== null && cellpadding !== void 0 ? cellpadding : '',
        border: getBorder(dom, elm),
        caption: !!dom.select('caption', elm)[0],
        class: dom.getAttrib(elm, 'class', ''),
        align: getHAlignment(editor, elm),
        ...hasAdvTableTab ? extractAdvancedStyles(elm) : {}
      };
    };
    const extractDataFromRowElement = (editor, elm, hasAdvancedRowTab) => {
      const dom = editor.dom;
      return {
        height: dom.getStyle(elm, 'height') || dom.getAttrib(elm, 'height'),
        class: dom.getAttrib(elm, 'class', ''),
        type: getRowType(elm),
        align: getHAlignment(editor, elm),
        ...hasAdvancedRowTab ? extractAdvancedStyles(elm) : {}
      };
    };
    const extractDataFromCellElement = (editor, cell, hasAdvancedCellTab, column) => {
      const dom = editor.dom;
      const colElm = column.getOr(cell);
      const getStyle = (element, style) => dom.getStyle(element, style) || dom.getAttrib(element, style);
      return {
        width: getStyle(colElm, 'width'),
        height: getStyle(cell, 'height'),
        scope: dom.getAttrib(cell, 'scope'),
        celltype: getNodeName(cell),
        class: dom.getAttrib(cell, 'class', ''),
        halign: getHAlignment(editor, cell),
        valign: getVAlignment(editor, cell),
        ...hasAdvancedCellTab ? extractAdvancedStyles(cell) : {}
      };
    };

    const getSelectedCells = (table, cells) => {
      const warehouse = Warehouse.fromTable(table);
      const allCells = Warehouse.justCells(warehouse);
      const filtered = filter(allCells, cellA => exists(cells, cellB => eq(cellA.element, cellB)));
      return map(filtered, cell => ({
        element: cell.element.dom,
        column: Warehouse.getColumnAt(warehouse, cell.column).map(col => col.element.dom)
      }));
    };
    const updateSimpleProps$1 = (modifier, colModifier, data, shouldUpdate) => {
      if (shouldUpdate('scope')) {
        modifier.setAttrib('scope', data.scope);
      }
      if (shouldUpdate('class')) {
        modifier.setAttrib('class', data.class);
      }
      if (shouldUpdate('height')) {
        modifier.setStyle('height', addPxSuffix(data.height));
      }
      if (shouldUpdate('width')) {
        colModifier.setStyle('width', addPxSuffix(data.width));
      }
    };
    const updateAdvancedProps$1 = (modifier, data, shouldUpdate) => {
      if (shouldUpdate('backgroundcolor')) {
        modifier.setFormat('tablecellbackgroundcolor', data.backgroundcolor);
      }
      if (shouldUpdate('bordercolor')) {
        modifier.setFormat('tablecellbordercolor', data.bordercolor);
      }
      if (shouldUpdate('borderstyle')) {
        modifier.setFormat('tablecellborderstyle', data.borderstyle);
      }
      if (shouldUpdate('borderwidth')) {
        modifier.setFormat('tablecellborderwidth', addPxSuffix(data.borderwidth));
      }
    };
    const applyStyleData$1 = (editor, cells, data, wasChanged) => {
      const isSingleCell = cells.length === 1;
      each(cells, item => {
        const cellElm = item.element;
        const shouldOverrideCurrentValue = isSingleCell ? always : wasChanged;
        const modifier = DomModifier.normal(editor, cellElm);
        const colModifier = item.column.map(col => DomModifier.normal(editor, col)).getOr(modifier);
        updateSimpleProps$1(modifier, colModifier, data, shouldOverrideCurrentValue);
        if (hasAdvancedCellTab(editor)) {
          updateAdvancedProps$1(modifier, data, shouldOverrideCurrentValue);
        }
        if (wasChanged('halign')) {
          setAlign(editor, cellElm, data.halign);
        }
        if (wasChanged('valign')) {
          setVAlign(editor, cellElm, data.valign);
        }
      });
    };
    const applyStructureData$1 = (editor, data) => {
      editor.execCommand('mceTableCellType', false, {
        type: data.celltype,
        no_events: true
      });
    };
    const applyCellData = (editor, cells, oldData, data) => {
      const modifiedData = filter$1(data, (value, key) => oldData[key] !== value);
      if (size(modifiedData) > 0 && cells.length >= 1) {
        table(cells[0]).each(table => {
          const selectedCells = getSelectedCells(table, cells);
          const styleModified = size(filter$1(modifiedData, (_value, key) => key !== 'scope' && key !== 'celltype')) > 0;
          const structureModified = has(modifiedData, 'celltype');
          if (styleModified || has(modifiedData, 'scope')) {
            applyStyleData$1(editor, selectedCells, data, curry(has, modifiedData));
          }
          if (structureModified) {
            applyStructureData$1(editor, data);
          }
          fireTableModified(editor, table.dom, {
            structure: structureModified,
            style: styleModified
          });
        });
      }
    };
    const onSubmitCellForm = (editor, cells, oldData, api) => {
      const data = api.getData();
      api.close();
      editor.undoManager.transact(() => {
        applyCellData(editor, cells, oldData, data);
        editor.focus();
      });
    };
    const getData$1 = (editor, cells) => {
      const cellsData = table(cells[0]).map(table => map(getSelectedCells(table, cells), item => extractDataFromCellElement(editor, item.element, hasAdvancedCellTab(editor), item.column)));
      return getSharedValues(cellsData.getOrDie());
    };
    const open$2 = editor => {
      const cells = getCellsFromSelection(editor);
      if (cells.length === 0) {
        return;
      }
      const data = getData$1(editor, cells);
      const dialogTabPanel = {
        type: 'tabpanel',
        tabs: [
          {
            title: 'General',
            name: 'general',
            items: getItems$2(editor)
          },
          getAdvancedTab(editor, 'cell')
        ]
      };
      const dialogPanel = {
        type: 'panel',
        items: [{
            type: 'grid',
            columns: 2,
            items: getItems$2(editor)
          }]
      };
      editor.windowManager.open({
        title: 'Cell Properties',
        size: 'normal',
        body: hasAdvancedCellTab(editor) ? dialogTabPanel : dialogPanel,
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
        initialData: data,
        onSubmit: curry(onSubmitCellForm, editor, cells, data)
      });
    };

    const getClassList = editor => {
      const classes = buildListItems(getRowClassList(editor));
      if (classes.length > 0) {
        return Optional.some({
          name: 'class',
          type: 'listbox',
          label: 'Class',
          items: classes
        });
      }
      return Optional.none();
    };
    const formChildren = [
      {
        type: 'listbox',
        name: 'type',
        label: 'Row type',
        items: [
          {
            text: 'Header',
            value: 'header'
          },
          {
            text: 'Body',
            value: 'body'
          },
          {
            text: 'Footer',
            value: 'footer'
          }
        ]
      },
      {
        type: 'listbox',
        name: 'align',
        label: 'Alignment',
        items: [
          {
            text: 'None',
            value: ''
          },
          {
            text: 'Left',
            value: 'left'
          },
          {
            text: 'Center',
            value: 'center'
          },
          {
            text: 'Right',
            value: 'right'
          }
        ]
      },
      {
        label: 'Height',
        name: 'height',
        type: 'input'
      }
    ];
    const getItems$1 = editor => formChildren.concat(getClassList(editor).toArray());

    const updateSimpleProps = (modifier, data, shouldUpdate) => {
      if (shouldUpdate('class')) {
        modifier.setAttrib('class', data.class);
      }
      if (shouldUpdate('height')) {
        modifier.setStyle('height', addPxSuffix(data.height));
      }
    };
    const updateAdvancedProps = (modifier, data, shouldUpdate) => {
      if (shouldUpdate('backgroundcolor')) {
        modifier.setStyle('background-color', data.backgroundcolor);
      }
      if (shouldUpdate('bordercolor')) {
        modifier.setStyle('border-color', data.bordercolor);
      }
      if (shouldUpdate('borderstyle')) {
        modifier.setStyle('border-style', data.borderstyle);
      }
    };
    const applyStyleData = (editor, rows, data, wasChanged) => {
      const isSingleRow = rows.length === 1;
      const shouldOverrideCurrentValue = isSingleRow ? always : wasChanged;
      each(rows, rowElm => {
        const modifier = DomModifier.normal(editor, rowElm);
        updateSimpleProps(modifier, data, shouldOverrideCurrentValue);
        if (hasAdvancedRowTab(editor)) {
          updateAdvancedProps(modifier, data, shouldOverrideCurrentValue);
        }
        if (wasChanged('align')) {
          setAlign(editor, rowElm, data.align);
        }
      });
    };
    const applyStructureData = (editor, data) => {
      editor.execCommand('mceTableRowType', false, {
        type: data.type,
        no_events: true
      });
    };
    const applyRowData = (editor, rows, oldData, data) => {
      const modifiedData = filter$1(data, (value, key) => oldData[key] !== value);
      if (size(modifiedData) > 0) {
        const typeModified = has(modifiedData, 'type');
        const styleModified = typeModified ? size(modifiedData) > 1 : true;
        if (styleModified) {
          applyStyleData(editor, rows, data, curry(has, modifiedData));
        }
        if (typeModified) {
          applyStructureData(editor, data);
        }
        table(SugarElement.fromDom(rows[0])).each(table => fireTableModified(editor, table.dom, {
          structure: typeModified,
          style: styleModified
        }));
      }
    };
    const onSubmitRowForm = (editor, rows, oldData, api) => {
      const data = api.getData();
      api.close();
      editor.undoManager.transact(() => {
        applyRowData(editor, rows, oldData, data);
        editor.focus();
      });
    };
    const open$1 = editor => {
      const rows = getRowsFromSelection(getSelectionStart(editor), ephemera.selected);
      if (rows.length === 0) {
        return;
      }
      const rowsData = map(rows, rowElm => extractDataFromRowElement(editor, rowElm.dom, hasAdvancedRowTab(editor)));
      const data = getSharedValues(rowsData);
      const dialogTabPanel = {
        type: 'tabpanel',
        tabs: [
          {
            title: 'General',
            name: 'general',
            items: getItems$1(editor)
          },
          getAdvancedTab(editor, 'row')
        ]
      };
      const dialogPanel = {
        type: 'panel',
        items: [{
            type: 'grid',
            columns: 2,
            items: getItems$1(editor)
          }]
      };
      editor.windowManager.open({
        title: 'Row Properties',
        size: 'normal',
        body: hasAdvancedRowTab(editor) ? dialogTabPanel : dialogPanel,
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
        initialData: data,
        onSubmit: curry(onSubmitRowForm, editor, map(rows, r => r.dom), data)
      });
    };

    const getItems = (editor, classes, insertNewTable) => {
      const rowColCountItems = !insertNewTable ? [] : [
        {
          type: 'input',
          name: 'cols',
          label: 'Cols',
          inputMode: 'numeric'
        },
        {
          type: 'input',
          name: 'rows',
          label: 'Rows',
          inputMode: 'numeric'
        }
      ];
      const alwaysItems = [
        {
          type: 'input',
          name: 'width',
          label: 'Width'
        },
        {
          type: 'input',
          name: 'height',
          label: 'Height'
        }
      ];
      const appearanceItems = hasAppearanceOptions(editor) ? [
        {
          type: 'input',
          name: 'cellspacing',
          label: 'Cell spacing',
          inputMode: 'numeric'
        },
        {
          type: 'input',
          name: 'cellpadding',
          label: 'Cell padding',
          inputMode: 'numeric'
        },
        {
          type: 'input',
          name: 'border',
          label: 'Border width'
        },
        {
          type: 'label',
          label: 'Caption',
          items: [{
              type: 'checkbox',
              name: 'caption',
              label: 'Show caption'
            }]
        }
      ] : [];
      const alignmentItem = [{
          type: 'listbox',
          name: 'align',
          label: 'Alignment',
          items: [
            {
              text: 'None',
              value: ''
            },
            {
              text: 'Left',
              value: 'left'
            },
            {
              text: 'Center',
              value: 'center'
            },
            {
              text: 'Right',
              value: 'right'
            }
          ]
        }];
      const classListItem = classes.length > 0 ? [{
          type: 'listbox',
          name: 'class',
          label: 'Class',
          items: classes
        }] : [];
      return rowColCountItems.concat(alwaysItems).concat(appearanceItems).concat(alignmentItem).concat(classListItem);
    };

    const styleTDTH = (dom, elm, name, value) => {
      if (elm.tagName === 'TD' || elm.tagName === 'TH') {
        if (isString(name) && isNonNullable(value)) {
          dom.setStyle(elm, name, value);
        } else {
          dom.setStyles(elm, name);
        }
      } else {
        if (elm.children) {
          for (let i = 0; i < elm.children.length; i++) {
            styleTDTH(dom, elm.children[i], name, value);
          }
        }
      }
    };
    const applyDataToElement = (editor, tableElm, data) => {
      const dom = editor.dom;
      const attrs = {};
      const styles = {};
      if (!isUndefined(data.class)) {
        attrs.class = data.class;
      }
      styles.height = addPxSuffix(data.height);
      if (shouldStyleWithCss(editor)) {
        styles.width = addPxSuffix(data.width);
      } else if (dom.getAttrib(tableElm, 'width')) {
        attrs.width = removePxSuffix(data.width);
      }
      if (shouldStyleWithCss(editor)) {
        styles['border-width'] = addPxSuffix(data.border);
        styles['border-spacing'] = addPxSuffix(data.cellspacing);
      } else {
        attrs.border = data.border;
        attrs.cellpadding = data.cellpadding;
        attrs.cellspacing = data.cellspacing;
      }
      if (shouldStyleWithCss(editor) && tableElm.children) {
        for (let i = 0; i < tableElm.children.length; i++) {
          styleTDTH(dom, tableElm.children[i], {
            'border-width': addPxSuffix(data.border),
            'padding': addPxSuffix(data.cellpadding)
          });
          if (hasAdvancedTableTab(editor)) {
            styleTDTH(dom, tableElm.children[i], { 'border-color': data.bordercolor });
          }
        }
      }
      if (hasAdvancedTableTab(editor)) {
        const advData = data;
        styles['background-color'] = advData.backgroundcolor;
        styles['border-color'] = advData.bordercolor;
        styles['border-style'] = advData.borderstyle;
      }
      attrs.style = dom.serializeStyle({
        ...getDefaultStyles(editor),
        ...styles
      });
      dom.setAttribs(tableElm, {
        ...getDefaultAttributes(editor),
        ...attrs
      });
    };
    const onSubmitTableForm = (editor, tableElm, oldData, api) => {
      const dom = editor.dom;
      const data = api.getData();
      const modifiedData = filter$1(data, (value, key) => oldData[key] !== value);
      api.close();
      if (data.class === '') {
        delete data.class;
      }
      editor.undoManager.transact(() => {
        if (!tableElm) {
          const cols = toInt(data.cols).getOr(1);
          const rows = toInt(data.rows).getOr(1);
          editor.execCommand('mceInsertTable', false, {
            rows,
            columns: cols
          });
          tableElm = getSelectionCell(getSelectionStart(editor), getIsRoot(editor)).bind(cell => table(cell, getIsRoot(editor))).map(table => table.dom).getOrDie();
        }
        if (size(modifiedData) > 0) {
          applyDataToElement(editor, tableElm, data);
          const captionElm = dom.select('caption', tableElm)[0];
          if (captionElm && !data.caption || !captionElm && data.caption) {
            editor.execCommand('mceTableToggleCaption');
          }
          setAlign(editor, tableElm, data.align);
        }
        editor.focus();
        editor.addVisual();
        if (size(modifiedData) > 0) {
          const captionModified = has(modifiedData, 'caption');
          const styleModified = captionModified ? size(modifiedData) > 1 : true;
          fireTableModified(editor, tableElm, {
            structure: captionModified,
            style: styleModified
          });
        }
      });
    };
    const open = (editor, insertNewTable) => {
      const dom = editor.dom;
      let tableElm;
      let data = extractDataFromSettings(editor, hasAdvancedTableTab(editor));
      if (insertNewTable) {
        data.cols = '1';
        data.rows = '1';
        if (hasAdvancedTableTab(editor)) {
          data.borderstyle = '';
          data.bordercolor = '';
          data.backgroundcolor = '';
        }
      } else {
        tableElm = dom.getParent(editor.selection.getStart(), 'table', editor.getBody());
        if (tableElm) {
          data = extractDataFromTableElement(editor, tableElm, hasAdvancedTableTab(editor));
        } else {
          if (hasAdvancedTableTab(editor)) {
            data.borderstyle = '';
            data.bordercolor = '';
            data.backgroundcolor = '';
          }
        }
      }
      const classes = buildListItems(getTableClassList(editor));
      if (classes.length > 0) {
        if (data.class) {
          data.class = data.class.replace(/\s*mce\-item\-table\s*/g, '');
        }
      }
      const generalPanel = {
        type: 'grid',
        columns: 2,
        items: getItems(editor, classes, insertNewTable)
      };
      const nonAdvancedForm = () => ({
        type: 'panel',
        items: [generalPanel]
      });
      const advancedForm = () => ({
        type: 'tabpanel',
        tabs: [
          {
            title: 'General',
            name: 'general',
            items: [generalPanel]
          },
          getAdvancedTab(editor, 'table')
        ]
      });
      const dialogBody = hasAdvancedTableTab(editor) ? advancedForm() : nonAdvancedForm();
      editor.windowManager.open({
        title: 'Table Properties',
        size: 'normal',
        body: dialogBody,
        onSubmit: curry(onSubmitTableForm, editor, tableElm, data),
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
        initialData: data
      });
    };

    const registerCommands = editor => {
      each$1({
        mceTableProps: curry(open, editor, false),
        mceTableRowProps: curry(open$1, editor),
        mceTableCellProps: curry(open$2, editor)
      }, (func, name) => editor.addCommand(name, () => func()));
      editor.addCommand('mceInsertTableDialog', _ui => {
        open(editor, true);
      });
    };

    const child = (scope, selector) => child$1(scope, selector).isSome();

    const selection = identity;
    const unmergable = selectedCells => {
      const hasSpan = (elem, type) => getOpt(elem, type).exists(span => parseInt(span, 10) > 1);
      const hasRowOrColSpan = elem => hasSpan(elem, 'rowspan') || hasSpan(elem, 'colspan');
      return selectedCells.length > 0 && forall(selectedCells, hasRowOrColSpan) ? Optional.some(selectedCells) : Optional.none();
    };
    const mergable = (table, selectedCells, ephemera) => {
      if (selectedCells.length <= 1) {
        return Optional.none();
      } else {
        return retrieveBox(table, ephemera.firstSelectedSelector, ephemera.lastSelectedSelector).map(bounds => ({
          bounds,
          cells: selectedCells
        }));
      }
    };

    const noMenu = cell => ({
      element: cell,
      mergable: Optional.none(),
      unmergable: Optional.none(),
      selection: [cell]
    });
    const forMenu = (selectedCells, table, cell) => ({
      element: cell,
      mergable: mergable(table, selectedCells, ephemera),
      unmergable: unmergable(selectedCells),
      selection: selection(selectedCells)
    });

    const getSelectionTargets = editor => {
      const targets = Cell(Optional.none());
      const changeHandlers = Cell([]);
      let selectionDetails = Optional.none();
      const isCaption = isTag('caption');
      const isDisabledForSelection = key => selectionDetails.forall(details => !details[key]);
      const getStart = () => getSelectionCellOrCaption(getSelectionStart(editor), getIsRoot(editor));
      const getEnd = () => getSelectionCellOrCaption(getSelectionEnd(editor), getIsRoot(editor));
      const findTargets = () => getStart().bind(startCellOrCaption => flatten(lift2(table(startCellOrCaption), getEnd().bind(table), (startTable, endTable) => {
        if (eq(startTable, endTable)) {
          if (isCaption(startCellOrCaption)) {
            return Optional.some(noMenu(startCellOrCaption));
          } else {
            return Optional.some(forMenu(getCellsFromSelection(editor), startTable, startCellOrCaption));
          }
        }
        return Optional.none();
      })));
      const getExtractedDetails = targets => {
        const tableOpt = table(targets.element);
        return tableOpt.map(table => {
          const warehouse = Warehouse.fromTable(table);
          const selectedCells = onCells(warehouse, targets).getOr([]);
          const locked = foldl(selectedCells, (acc, cell) => {
            if (cell.isLocked) {
              acc.onAny = true;
              if (cell.column === 0) {
                acc.onFirst = true;
              } else if (cell.column + cell.colspan >= warehouse.grid.columns) {
                acc.onLast = true;
              }
            }
            return acc;
          }, {
            onAny: false,
            onFirst: false,
            onLast: false
          });
          return {
            mergeable: onUnlockedMergable(warehouse, targets).isSome(),
            unmergeable: onUnlockedUnmergable(warehouse, targets).isSome(),
            locked
          };
        });
      };
      const resetTargets = () => {
        targets.set(cached(findTargets)());
        selectionDetails = targets.get().bind(getExtractedDetails);
        each(changeHandlers.get(), call);
      };
      const setupHandler = handler => {
        handler();
        changeHandlers.set(changeHandlers.get().concat([handler]));
        return () => {
          changeHandlers.set(filter(changeHandlers.get(), h => h !== handler));
        };
      };
      const onSetup = (api, isDisabled) => setupHandler(() => targets.get().fold(() => {
        api.setEnabled(false);
      }, targets => {
        api.setEnabled(!isDisabled(targets));
      }));
      const onSetupWithToggle = (api, isDisabled, isActive) => setupHandler(() => targets.get().fold(() => {
        api.setEnabled(false);
        api.setActive(false);
      }, targets => {
        api.setEnabled(!isDisabled(targets));
        api.setActive(isActive(targets));
      }));
      const isDisabledFromLocked = lockedDisable => selectionDetails.exists(details => details.locked[lockedDisable]);
      const onSetupTable = api => onSetup(api, _ => false);
      const onSetupCellOrRow = api => onSetup(api, targets => isCaption(targets.element));
      const onSetupColumn = lockedDisable => api => onSetup(api, targets => isCaption(targets.element) || isDisabledFromLocked(lockedDisable));
      const onSetupPasteable = getClipboardData => api => onSetup(api, targets => isCaption(targets.element) || getClipboardData().isNone());
      const onSetupPasteableColumn = (getClipboardData, lockedDisable) => api => onSetup(api, targets => isCaption(targets.element) || getClipboardData().isNone() || isDisabledFromLocked(lockedDisable));
      const onSetupMergeable = api => onSetup(api, _targets => isDisabledForSelection('mergeable'));
      const onSetupUnmergeable = api => onSetup(api, _targets => isDisabledForSelection('unmergeable'));
      const onSetupTableWithCaption = api => {
        return onSetupWithToggle(api, never, targets => {
          const tableOpt = table(targets.element, getIsRoot(editor));
          return tableOpt.exists(table => child(table, 'caption'));
        });
      };
      const onSetupTableHeaders = (command, headerType) => api => {
        return onSetupWithToggle(api, targets => isCaption(targets.element), () => editor.queryCommandValue(command) === headerType);
      };
      const onSetupTableRowHeaders = onSetupTableHeaders('mceTableRowType', 'header');
      const onSetupTableColumnHeaders = onSetupTableHeaders('mceTableColType', 'th');
      editor.on('NodeChange ExecCommand TableSelectorChange', resetTargets);
      return {
        onSetupTable,
        onSetupCellOrRow,
        onSetupColumn,
        onSetupPasteable,
        onSetupPasteableColumn,
        onSetupMergeable,
        onSetupUnmergeable,
        resetTargets,
        onSetupTableWithCaption,
        onSetupTableRowHeaders,
        onSetupTableColumnHeaders,
        targets: targets.get
      };
    };

    var global = tinymce.util.Tools.resolve('tinymce.FakeClipboard');

    const tableTypeBase = 'x-tinymce/dom-table-';
    const tableTypeRow = tableTypeBase + 'rows';
    const tableTypeColumn = tableTypeBase + 'columns';
    const getData = type => {
      var _a;
      const items = (_a = global.read()) !== null && _a !== void 0 ? _a : [];
      return findMap(items, item => Optional.from(item.getType(type)));
    };
    const getRows = () => getData(tableTypeRow);
    const getColumns = () => getData(tableTypeColumn);

    const addButtons = (editor, selectionTargets) => {
      editor.ui.registry.addMenuButton('table', {
        tooltip: 'Table',
        icon: 'table',
        fetch: callback => callback('inserttable | cell row column | advtablesort | tableprops deletetable')
      });
      const cmd = command => () => editor.execCommand(command);
      const addButtonIfRegistered = (name, spec) => {
        if (editor.queryCommandSupported(spec.command)) {
          editor.ui.registry.addButton(name, {
            ...spec,
            onAction: isFunction(spec.onAction) ? spec.onAction : cmd(spec.command)
          });
        }
      };
      const addToggleButtonIfRegistered = (name, spec) => {
        if (editor.queryCommandSupported(spec.command)) {
          editor.ui.registry.addToggleButton(name, {
            ...spec,
            onAction: isFunction(spec.onAction) ? spec.onAction : cmd(spec.command)
          });
        }
      };
      addButtonIfRegistered('tableprops', {
        tooltip: 'Table properties',
        command: 'mceTableProps',
        icon: 'table',
        onSetup: selectionTargets.onSetupTable
      });
      addButtonIfRegistered('tabledelete', {
        tooltip: 'Delete table',
        command: 'mceTableDelete',
        icon: 'table-delete-table',
        onSetup: selectionTargets.onSetupTable
      });
      addButtonIfRegistered('tablecellprops', {
        tooltip: 'Cell properties',
        command: 'mceTableCellProps',
        icon: 'table-cell-properties',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      addButtonIfRegistered('tablemergecells', {
        tooltip: 'Merge cells',
        command: 'mceTableMergeCells',
        icon: 'table-merge-cells',
        onSetup: selectionTargets.onSetupMergeable
      });
      addButtonIfRegistered('tablesplitcells', {
        tooltip: 'Split cell',
        command: 'mceTableSplitCells',
        icon: 'table-split-cells',
        onSetup: selectionTargets.onSetupUnmergeable
      });
      addButtonIfRegistered('tableinsertrowbefore', {
        tooltip: 'Insert row before',
        command: 'mceTableInsertRowBefore',
        icon: 'table-insert-row-above',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      addButtonIfRegistered('tableinsertrowafter', {
        tooltip: 'Insert row after',
        command: 'mceTableInsertRowAfter',
        icon: 'table-insert-row-after',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      addButtonIfRegistered('tabledeleterow', {
        tooltip: 'Delete row',
        command: 'mceTableDeleteRow',
        icon: 'table-delete-row',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      addButtonIfRegistered('tablerowprops', {
        tooltip: 'Row properties',
        command: 'mceTableRowProps',
        icon: 'table-row-properties',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      addButtonIfRegistered('tableinsertcolbefore', {
        tooltip: 'Insert column before',
        command: 'mceTableInsertColBefore',
        icon: 'table-insert-column-before',
        onSetup: selectionTargets.onSetupColumn('onFirst')
      });
      addButtonIfRegistered('tableinsertcolafter', {
        tooltip: 'Insert column after',
        command: 'mceTableInsertColAfter',
        icon: 'table-insert-column-after',
        onSetup: selectionTargets.onSetupColumn('onLast')
      });
      addButtonIfRegistered('tabledeletecol', {
        tooltip: 'Delete column',
        command: 'mceTableDeleteCol',
        icon: 'table-delete-column',
        onSetup: selectionTargets.onSetupColumn('onAny')
      });
      addButtonIfRegistered('tablecutrow', {
        tooltip: 'Cut row',
        command: 'mceTableCutRow',
        icon: 'cut-row',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      addButtonIfRegistered('tablecopyrow', {
        tooltip: 'Copy row',
        command: 'mceTableCopyRow',
        icon: 'duplicate-row',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      addButtonIfRegistered('tablepasterowbefore', {
        tooltip: 'Paste row before',
        command: 'mceTablePasteRowBefore',
        icon: 'paste-row-before',
        onSetup: selectionTargets.onSetupPasteable(getRows)
      });
      addButtonIfRegistered('tablepasterowafter', {
        tooltip: 'Paste row after',
        command: 'mceTablePasteRowAfter',
        icon: 'paste-row-after',
        onSetup: selectionTargets.onSetupPasteable(getRows)
      });
      addButtonIfRegistered('tablecutcol', {
        tooltip: 'Cut column',
        command: 'mceTableCutCol',
        icon: 'cut-column',
        onSetup: selectionTargets.onSetupColumn('onAny')
      });
      addButtonIfRegistered('tablecopycol', {
        tooltip: 'Copy column',
        command: 'mceTableCopyCol',
        icon: 'duplicate-column',
        onSetup: selectionTargets.onSetupColumn('onAny')
      });
      addButtonIfRegistered('tablepastecolbefore', {
        tooltip: 'Paste column before',
        command: 'mceTablePasteColBefore',
        icon: 'paste-column-before',
        onSetup: selectionTargets.onSetupPasteableColumn(getColumns, 'onFirst')
      });
      addButtonIfRegistered('tablepastecolafter', {
        tooltip: 'Paste column after',
        command: 'mceTablePasteColAfter',
        icon: 'paste-column-after',
        onSetup: selectionTargets.onSetupPasteableColumn(getColumns, 'onLast')
      });
      addButtonIfRegistered('tableinsertdialog', {
        tooltip: 'Insert table',
        command: 'mceInsertTableDialog',
        icon: 'table'
      });
      const tableClassList = filterNoneItem(getTableClassList(editor));
      if (tableClassList.length !== 0 && editor.queryCommandSupported('mceTableToggleClass')) {
        editor.ui.registry.addMenuButton('tableclass', {
          icon: 'table-classes',
          tooltip: 'Table styles',
          fetch: generateMenuItemsCallback(editor, tableClassList, 'tableclass', value => editor.execCommand('mceTableToggleClass', false, value)),
          onSetup: selectionTargets.onSetupTable
        });
      }
      const tableCellClassList = filterNoneItem(getCellClassList(editor));
      if (tableCellClassList.length !== 0 && editor.queryCommandSupported('mceTableCellToggleClass')) {
        editor.ui.registry.addMenuButton('tablecellclass', {
          icon: 'table-cell-classes',
          tooltip: 'Cell styles',
          fetch: generateMenuItemsCallback(editor, tableCellClassList, 'tablecellclass', value => editor.execCommand('mceTableCellToggleClass', false, value)),
          onSetup: selectionTargets.onSetupCellOrRow
        });
      }
      if (editor.queryCommandSupported('mceTableApplyCellStyle')) {
        editor.ui.registry.addMenuButton('tablecellvalign', {
          icon: 'vertical-align',
          tooltip: 'Vertical align',
          fetch: generateMenuItemsCallback(editor, verticalAlignValues, 'tablecellverticalalign', applyTableCellStyle(editor, 'vertical-align')),
          onSetup: selectionTargets.onSetupCellOrRow
        });
        editor.ui.registry.addMenuButton('tablecellborderwidth', {
          icon: 'border-width',
          tooltip: 'Border width',
          fetch: generateMenuItemsCallback(editor, getTableBorderWidths(editor), 'tablecellborderwidth', applyTableCellStyle(editor, 'border-width')),
          onSetup: selectionTargets.onSetupCellOrRow
        });
        editor.ui.registry.addMenuButton('tablecellborderstyle', {
          icon: 'border-style',
          tooltip: 'Border style',
          fetch: generateMenuItemsCallback(editor, getTableBorderStyles(editor), 'tablecellborderstyle', applyTableCellStyle(editor, 'border-style')),
          onSetup: selectionTargets.onSetupCellOrRow
        });
        editor.ui.registry.addMenuButton('tablecellbackgroundcolor', {
          icon: 'cell-background-color',
          tooltip: 'Background color',
          fetch: callback => callback(buildColorMenu(editor, getTableBackgroundColorMap(editor), 'background-color')),
          onSetup: selectionTargets.onSetupCellOrRow
        });
        editor.ui.registry.addMenuButton('tablecellbordercolor', {
          icon: 'cell-border-color',
          tooltip: 'Border color',
          fetch: callback => callback(buildColorMenu(editor, getTableBorderColorMap(editor), 'border-color')),
          onSetup: selectionTargets.onSetupCellOrRow
        });
      }
      addToggleButtonIfRegistered('tablecaption', {
        tooltip: 'Table caption',
        icon: 'table-caption',
        command: 'mceTableToggleCaption',
        onSetup: selectionTargets.onSetupTableWithCaption
      });
      addToggleButtonIfRegistered('tablerowheader', {
        tooltip: 'Row header',
        icon: 'table-top-header',
        command: 'mceTableRowType',
        onAction: changeRowHeader(editor),
        onSetup: selectionTargets.onSetupTableRowHeaders
      });
      addToggleButtonIfRegistered('tablecolheader', {
        tooltip: 'Column header',
        icon: 'table-left-header',
        command: 'mceTableColType',
        onAction: changeColumnHeader(editor),
        onSetup: selectionTargets.onSetupTableColumnHeaders
      });
    };
    const addToolbars = editor => {
      const isTable = table => editor.dom.is(table, 'table') && editor.getBody().contains(table);
      const toolbar = getToolbar(editor);
      if (toolbar.length > 0) {
        editor.ui.registry.addContextToolbar('table', {
          predicate: isTable,
          items: toolbar,
          scope: 'node',
          position: 'node'
        });
      }
    };

    const addMenuItems = (editor, selectionTargets) => {
      const cmd = command => () => editor.execCommand(command);
      const addMenuIfRegistered = (name, spec) => {
        if (editor.queryCommandSupported(spec.command)) {
          editor.ui.registry.addMenuItem(name, {
            ...spec,
            onAction: isFunction(spec.onAction) ? spec.onAction : cmd(spec.command)
          });
          return true;
        } else {
          return false;
        }
      };
      const addToggleMenuIfRegistered = (name, spec) => {
        if (editor.queryCommandSupported(spec.command)) {
          editor.ui.registry.addToggleMenuItem(name, {
            ...spec,
            onAction: isFunction(spec.onAction) ? spec.onAction : cmd(spec.command)
          });
        }
      };
      const insertTableAction = data => {
        editor.execCommand('mceInsertTable', false, {
          rows: data.numRows,
          columns: data.numColumns
        });
      };
      const hasRowMenuItems = [
        addMenuIfRegistered('tableinsertrowbefore', {
          text: 'Insert row before',
          icon: 'table-insert-row-above',
          command: 'mceTableInsertRowBefore',
          onSetup: selectionTargets.onSetupCellOrRow
        }),
        addMenuIfRegistered('tableinsertrowafter', {
          text: 'Insert row after',
          icon: 'table-insert-row-after',
          command: 'mceTableInsertRowAfter',
          onSetup: selectionTargets.onSetupCellOrRow
        }),
        addMenuIfRegistered('tabledeleterow', {
          text: 'Delete row',
          icon: 'table-delete-row',
          command: 'mceTableDeleteRow',
          onSetup: selectionTargets.onSetupCellOrRow
        }),
        addMenuIfRegistered('tablerowprops', {
          text: 'Row properties',
          icon: 'table-row-properties',
          command: 'mceTableRowProps',
          onSetup: selectionTargets.onSetupCellOrRow
        }),
        addMenuIfRegistered('tablecutrow', {
          text: 'Cut row',
          icon: 'cut-row',
          command: 'mceTableCutRow',
          onSetup: selectionTargets.onSetupCellOrRow
        }),
        addMenuIfRegistered('tablecopyrow', {
          text: 'Copy row',
          icon: 'duplicate-row',
          command: 'mceTableCopyRow',
          onSetup: selectionTargets.onSetupCellOrRow
        }),
        addMenuIfRegistered('tablepasterowbefore', {
          text: 'Paste row before',
          icon: 'paste-row-before',
          command: 'mceTablePasteRowBefore',
          onSetup: selectionTargets.onSetupPasteable(getRows)
        }),
        addMenuIfRegistered('tablepasterowafter', {
          text: 'Paste row after',
          icon: 'paste-row-after',
          command: 'mceTablePasteRowAfter',
          onSetup: selectionTargets.onSetupPasteable(getRows)
        })
      ];
      const hasColumnMenuItems = [
        addMenuIfRegistered('tableinsertcolumnbefore', {
          text: 'Insert column before',
          icon: 'table-insert-column-before',
          command: 'mceTableInsertColBefore',
          onSetup: selectionTargets.onSetupColumn('onFirst')
        }),
        addMenuIfRegistered('tableinsertcolumnafter', {
          text: 'Insert column after',
          icon: 'table-insert-column-after',
          command: 'mceTableInsertColAfter',
          onSetup: selectionTargets.onSetupColumn('onLast')
        }),
        addMenuIfRegistered('tabledeletecolumn', {
          text: 'Delete column',
          icon: 'table-delete-column',
          command: 'mceTableDeleteCol',
          onSetup: selectionTargets.onSetupColumn('onAny')
        }),
        addMenuIfRegistered('tablecutcolumn', {
          text: 'Cut column',
          icon: 'cut-column',
          command: 'mceTableCutCol',
          onSetup: selectionTargets.onSetupColumn('onAny')
        }),
        addMenuIfRegistered('tablecopycolumn', {
          text: 'Copy column',
          icon: 'duplicate-column',
          command: 'mceTableCopyCol',
          onSetup: selectionTargets.onSetupColumn('onAny')
        }),
        addMenuIfRegistered('tablepastecolumnbefore', {
          text: 'Paste column before',
          icon: 'paste-column-before',
          command: 'mceTablePasteColBefore',
          onSetup: selectionTargets.onSetupPasteableColumn(getColumns, 'onFirst')
        }),
        addMenuIfRegistered('tablepastecolumnafter', {
          text: 'Paste column after',
          icon: 'paste-column-after',
          command: 'mceTablePasteColAfter',
          onSetup: selectionTargets.onSetupPasteableColumn(getColumns, 'onLast')
        })
      ];
      const hasCellMenuItems = [
        addMenuIfRegistered('tablecellprops', {
          text: 'Cell properties',
          icon: 'table-cell-properties',
          command: 'mceTableCellProps',
          onSetup: selectionTargets.onSetupCellOrRow
        }),
        addMenuIfRegistered('tablemergecells', {
          text: 'Merge cells',
          icon: 'table-merge-cells',
          command: 'mceTableMergeCells',
          onSetup: selectionTargets.onSetupMergeable
        }),
        addMenuIfRegistered('tablesplitcells', {
          text: 'Split cell',
          icon: 'table-split-cells',
          command: 'mceTableSplitCells',
          onSetup: selectionTargets.onSetupUnmergeable
        })
      ];
      if (!hasTableGrid(editor)) {
        editor.ui.registry.addMenuItem('inserttable', {
          text: 'Table',
          icon: 'table',
          onAction: cmd('mceInsertTableDialog')
        });
      } else {
        editor.ui.registry.addNestedMenuItem('inserttable', {
          text: 'Table',
          icon: 'table',
          getSubmenuItems: () => [{
              type: 'fancymenuitem',
              fancytype: 'inserttable',
              onAction: insertTableAction
            }]
        });
      }
      editor.ui.registry.addMenuItem('inserttabledialog', {
        text: 'Insert table',
        icon: 'table',
        onAction: cmd('mceInsertTableDialog')
      });
      addMenuIfRegistered('tableprops', {
        text: 'Table properties',
        onSetup: selectionTargets.onSetupTable,
        command: 'mceTableProps'
      });
      addMenuIfRegistered('deletetable', {
        text: 'Delete table',
        icon: 'table-delete-table',
        onSetup: selectionTargets.onSetupTable,
        command: 'mceTableDelete'
      });
      if (contains(hasRowMenuItems, true)) {
        editor.ui.registry.addNestedMenuItem('row', {
          type: 'nestedmenuitem',
          text: 'Row',
          getSubmenuItems: constant('tableinsertrowbefore tableinsertrowafter tabledeleterow tablerowprops | tablecutrow tablecopyrow tablepasterowbefore tablepasterowafter')
        });
      }
      if (contains(hasColumnMenuItems, true)) {
        editor.ui.registry.addNestedMenuItem('column', {
          type: 'nestedmenuitem',
          text: 'Column',
          getSubmenuItems: constant('tableinsertcolumnbefore tableinsertcolumnafter tabledeletecolumn | tablecutcolumn tablecopycolumn tablepastecolumnbefore tablepastecolumnafter')
        });
      }
      if (contains(hasCellMenuItems, true)) {
        editor.ui.registry.addNestedMenuItem('cell', {
          type: 'nestedmenuitem',
          text: 'Cell',
          getSubmenuItems: constant('tablecellprops tablemergecells tablesplitcells')
        });
      }
      editor.ui.registry.addContextMenu('table', {
        update: () => {
          selectionTargets.resetTargets();
          return selectionTargets.targets().fold(constant(''), targets => {
            if (name(targets.element) === 'caption') {
              return 'tableprops deletetable';
            } else {
              return 'cell row column | advtablesort | tableprops deletetable';
            }
          });
        }
      });
      const tableClassList = filterNoneItem(getTableClassList(editor));
      if (tableClassList.length !== 0 && editor.queryCommandSupported('mceTableToggleClass')) {
        editor.ui.registry.addNestedMenuItem('tableclass', {
          icon: 'table-classes',
          text: 'Table styles',
          getSubmenuItems: () => buildMenuItems(editor, tableClassList, 'tableclass', value => editor.execCommand('mceTableToggleClass', false, value)),
          onSetup: selectionTargets.onSetupTable
        });
      }
      const tableCellClassList = filterNoneItem(getCellClassList(editor));
      if (tableCellClassList.length !== 0 && editor.queryCommandSupported('mceTableCellToggleClass')) {
        editor.ui.registry.addNestedMenuItem('tablecellclass', {
          icon: 'table-cell-classes',
          text: 'Cell styles',
          getSubmenuItems: () => buildMenuItems(editor, tableCellClassList, 'tablecellclass', value => editor.execCommand('mceTableCellToggleClass', false, value)),
          onSetup: selectionTargets.onSetupCellOrRow
        });
      }
      if (editor.queryCommandSupported('mceTableApplyCellStyle')) {
        editor.ui.registry.addNestedMenuItem('tablecellvalign', {
          icon: 'vertical-align',
          text: 'Vertical align',
          getSubmenuItems: () => buildMenuItems(editor, verticalAlignValues, 'tablecellverticalalign', applyTableCellStyle(editor, 'vertical-align')),
          onSetup: selectionTargets.onSetupCellOrRow
        });
        editor.ui.registry.addNestedMenuItem('tablecellborderwidth', {
          icon: 'border-width',
          text: 'Border width',
          getSubmenuItems: () => buildMenuItems(editor, getTableBorderWidths(editor), 'tablecellborderwidth', applyTableCellStyle(editor, 'border-width')),
          onSetup: selectionTargets.onSetupCellOrRow
        });
        editor.ui.registry.addNestedMenuItem('tablecellborderstyle', {
          icon: 'border-style',
          text: 'Border style',
          getSubmenuItems: () => buildMenuItems(editor, getTableBorderStyles(editor), 'tablecellborderstyle', applyTableCellStyle(editor, 'border-style')),
          onSetup: selectionTargets.onSetupCellOrRow
        });
        editor.ui.registry.addNestedMenuItem('tablecellbackgroundcolor', {
          icon: 'cell-background-color',
          text: 'Background color',
          getSubmenuItems: () => buildColorMenu(editor, getTableBackgroundColorMap(editor), 'background-color'),
          onSetup: selectionTargets.onSetupCellOrRow
        });
        editor.ui.registry.addNestedMenuItem('tablecellbordercolor', {
          icon: 'cell-border-color',
          text: 'Border color',
          getSubmenuItems: () => buildColorMenu(editor, getTableBorderColorMap(editor), 'border-color'),
          onSetup: selectionTargets.onSetupCellOrRow
        });
      }
      addToggleMenuIfRegistered('tablecaption', {
        icon: 'table-caption',
        text: 'Table caption',
        command: 'mceTableToggleCaption',
        onSetup: selectionTargets.onSetupTableWithCaption
      });
      addToggleMenuIfRegistered('tablerowheader', {
        text: 'Row header',
        icon: 'table-top-header',
        command: 'mceTableRowType',
        onAction: changeRowHeader(editor),
        onSetup: selectionTargets.onSetupTableRowHeaders
      });
      addToggleMenuIfRegistered('tablecolheader', {
        text: 'Column header',
        icon: 'table-left-header',
        command: 'mceTableColType',
        onAction: changeColumnHeader(editor),
        onSetup: selectionTargets.onSetupTableRowHeaders
      });
    };

    const Plugin = editor => {
      const selectionTargets = getSelectionTargets(editor);
      register(editor);
      registerCommands(editor);
      addMenuItems(editor, selectionTargets);
      addButtons(editor, selectionTargets);
      addToolbars(editor);
    };
    var Plugin$1 = () => {
      global$3.add('table', Plugin);
    };

    Plugin$1();

})();
