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
    var isObject = isType('object');
    var isArray = isType('array');
    var isBoolean = isSimpleType('boolean');
    var isUndefined = eq(undefined);
    var isNullable = function (a) {
      return a === null || a === undefined;
    };
    var isNonNullable = function (a) {
      return !isNullable(a);
    };
    var isFunction = isSimpleType('function');
    var isNumber = isSimpleType('number');

    var noop = function () {
    };
    var compose = function (fa, fb) {
      return function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          args[_i] = arguments[_i];
        }
        return fa(fb.apply(null, args));
      };
    };
    var compose1 = function (fbc, fab) {
      return function (a) {
        return fbc(fab(a));
      };
    };
    var constant = function (value) {
      return function () {
        return value;
      };
    };
    var identity = function (x) {
      return x;
    };
    function curry(fn) {
      var initialArgs = [];
      for (var _i = 1; _i < arguments.length; _i++) {
        initialArgs[_i - 1] = arguments[_i];
      }
      return function () {
        var restArgs = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          restArgs[_i] = arguments[_i];
        }
        var all = initialArgs.concat(restArgs);
        return fn.apply(null, all);
      };
    }
    var not = function (f) {
      return function (t) {
        return !f(t);
      };
    };
    var die = function (msg) {
      return function () {
        throw new Error(msg);
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

    var nativeSlice = Array.prototype.slice;
    var nativeIndexOf = Array.prototype.indexOf;
    var nativePush = Array.prototype.push;
    var rawIndexOf = function (ts, t) {
      return nativeIndexOf.call(ts, t);
    };
    var contains = function (xs, x) {
      return rawIndexOf(xs, x) > -1;
    };
    var exists = function (xs, pred) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          return true;
        }
      }
      return false;
    };
    var range = function (num, f) {
      var r = [];
      for (var i = 0; i < num; i++) {
        r.push(f(i));
      }
      return r;
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
    var eachr = function (xs, f) {
      for (var i = xs.length - 1; i >= 0; i--) {
        var x = xs[i];
        f(x, i);
      }
    };
    var partition = function (xs, pred) {
      var pass = [];
      var fail = [];
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        var arr = pred(x, i) ? pass : fail;
        arr.push(x);
      }
      return {
        pass: pass,
        fail: fail
      };
    };
    var filter = function (xs, pred) {
      var r = [];
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          r.push(x);
        }
      }
      return r;
    };
    var foldr = function (xs, f, acc) {
      eachr(xs, function (x) {
        acc = f(acc, x);
      });
      return acc;
    };
    var foldl = function (xs, f, acc) {
      each(xs, function (x) {
        acc = f(acc, x);
      });
      return acc;
    };
    var findUntil = function (xs, pred, until) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          return Optional.some(x);
        } else if (until(x, i)) {
          break;
        }
      }
      return Optional.none();
    };
    var find = function (xs, pred) {
      return findUntil(xs, pred, never);
    };
    var findIndex = function (xs, pred) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          return Optional.some(i);
        }
      }
      return Optional.none();
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
    var forall = function (xs, pred) {
      for (var i = 0, len = xs.length; i < len; ++i) {
        var x = xs[i];
        if (pred(x, i) !== true) {
          return false;
        }
      }
      return true;
    };
    var reverse = function (xs) {
      var r = nativeSlice.call(xs, 0);
      r.reverse();
      return r;
    };
    var mapToObject = function (xs, f) {
      var r = {};
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        r[String(x)] = f(x, i);
      }
      return r;
    };
    var pure = function (x) {
      return [x];
    };
    var sort = function (xs, comparator) {
      var copy = nativeSlice.call(xs, 0);
      copy.sort(comparator);
      return copy;
    };
    var get = function (xs, i) {
      return i >= 0 && i < xs.length ? Optional.some(xs[i]) : Optional.none();
    };
    var head = function (xs) {
      return get(xs, 0);
    };
    var last = function (xs) {
      return get(xs, xs.length - 1);
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
    function __spreadArrays() {
      for (var s = 0, i = 0, il = arguments.length; i < il; i++)
        s += arguments[i].length;
      for (var r = Array(s), k = 0, i = 0; i < il; i++)
        for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
          r[k] = a[j];
      return r;
    }

    var cached = function (f) {
      var called = false;
      var r;
      return function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          args[_i] = arguments[_i];
        }
        if (!called) {
          called = true;
          r = f.apply(null, args);
        }
        return r;
      };
    };

    var DeviceType = function (os, browser, userAgent, mediaMatch) {
      var isiPad = os.isiOS() && /ipad/i.test(userAgent) === true;
      var isiPhone = os.isiOS() && !isiPad;
      var isMobile = os.isiOS() || os.isAndroid();
      var isTouch = isMobile || mediaMatch('(pointer:coarse)');
      var isTablet = isiPad || !isiPhone && isMobile && mediaMatch('(min-device-width:768px)');
      var isPhone = isiPhone || isMobile && !isTablet;
      var iOSwebview = browser.isSafari() && os.isiOS() && /safari/i.test(userAgent) === false;
      var isDesktop = !isPhone && !isTablet && !iOSwebview;
      return {
        isiPad: constant(isiPad),
        isiPhone: constant(isiPhone),
        isTablet: constant(isTablet),
        isPhone: constant(isPhone),
        isTouch: constant(isTouch),
        isAndroid: os.isAndroid,
        isiOS: os.isiOS,
        isWebView: constant(iOSwebview),
        isDesktop: constant(isDesktop)
      };
    };

    var firstMatch = function (regexes, s) {
      for (var i = 0; i < regexes.length; i++) {
        var x = regexes[i];
        if (x.test(s)) {
          return x;
        }
      }
      return undefined;
    };
    var find$1 = function (regexes, agent) {
      var r = firstMatch(regexes, agent);
      if (!r) {
        return {
          major: 0,
          minor: 0
        };
      }
      var group = function (i) {
        return Number(agent.replace(r, '$' + i));
      };
      return nu(group(1), group(2));
    };
    var detect = function (versionRegexes, agent) {
      var cleanedAgent = String(agent).toLowerCase();
      if (versionRegexes.length === 0) {
        return unknown();
      }
      return find$1(versionRegexes, cleanedAgent);
    };
    var unknown = function () {
      return nu(0, 0);
    };
    var nu = function (major, minor) {
      return {
        major: major,
        minor: minor
      };
    };
    var Version = {
      nu: nu,
      detect: detect,
      unknown: unknown
    };

    var detect$1 = function (candidates, userAgent) {
      var agent = String(userAgent).toLowerCase();
      return find(candidates, function (candidate) {
        return candidate.search(agent);
      });
    };
    var detectBrowser = function (browsers, userAgent) {
      return detect$1(browsers, userAgent).map(function (browser) {
        var version = Version.detect(browser.versionRegexes, userAgent);
        return {
          current: browser.name,
          version: version
        };
      });
    };
    var detectOs = function (oses, userAgent) {
      return detect$1(oses, userAgent).map(function (os) {
        var version = Version.detect(os.versionRegexes, userAgent);
        return {
          current: os.name,
          version: version
        };
      });
    };
    var UaString = {
      detectBrowser: detectBrowser,
      detectOs: detectOs
    };

    var checkRange = function (str, substr, start) {
      return substr === '' || str.length >= substr.length && str.substr(start, start + substr.length) === substr;
    };
    var contains$1 = function (str, substr) {
      return str.indexOf(substr) !== -1;
    };
    var startsWith = function (str, prefix) {
      return checkRange(str, prefix, 0);
    };
    var endsWith = function (str, suffix) {
      return checkRange(str, suffix, str.length - suffix.length);
    };
    var blank = function (r) {
      return function (s) {
        return s.replace(r, '');
      };
    };
    var trim = blank(/^\s+|\s+$/g);
    var isNotEmpty = function (s) {
      return s.length > 0;
    };

    var normalVersionRegex = /.*?version\/\ ?([0-9]+)\.([0-9]+).*/;
    var checkContains = function (target) {
      return function (uastring) {
        return contains$1(uastring, target);
      };
    };
    var browsers = [
      {
        name: 'Edge',
        versionRegexes: [/.*?edge\/ ?([0-9]+)\.([0-9]+)$/],
        search: function (uastring) {
          return contains$1(uastring, 'edge/') && contains$1(uastring, 'chrome') && contains$1(uastring, 'safari') && contains$1(uastring, 'applewebkit');
        }
      },
      {
        name: 'Chrome',
        versionRegexes: [
          /.*?chrome\/([0-9]+)\.([0-9]+).*/,
          normalVersionRegex
        ],
        search: function (uastring) {
          return contains$1(uastring, 'chrome') && !contains$1(uastring, 'chromeframe');
        }
      },
      {
        name: 'IE',
        versionRegexes: [
          /.*?msie\ ?([0-9]+)\.([0-9]+).*/,
          /.*?rv:([0-9]+)\.([0-9]+).*/
        ],
        search: function (uastring) {
          return contains$1(uastring, 'msie') || contains$1(uastring, 'trident');
        }
      },
      {
        name: 'Opera',
        versionRegexes: [
          normalVersionRegex,
          /.*?opera\/([0-9]+)\.([0-9]+).*/
        ],
        search: checkContains('opera')
      },
      {
        name: 'Firefox',
        versionRegexes: [/.*?firefox\/\ ?([0-9]+)\.([0-9]+).*/],
        search: checkContains('firefox')
      },
      {
        name: 'Safari',
        versionRegexes: [
          normalVersionRegex,
          /.*?cpu os ([0-9]+)_([0-9]+).*/
        ],
        search: function (uastring) {
          return (contains$1(uastring, 'safari') || contains$1(uastring, 'mobile/')) && contains$1(uastring, 'applewebkit');
        }
      }
    ];
    var oses = [
      {
        name: 'Windows',
        search: checkContains('win'),
        versionRegexes: [/.*?windows\ nt\ ?([0-9]+)\.([0-9]+).*/]
      },
      {
        name: 'iOS',
        search: function (uastring) {
          return contains$1(uastring, 'iphone') || contains$1(uastring, 'ipad');
        },
        versionRegexes: [
          /.*?version\/\ ?([0-9]+)\.([0-9]+).*/,
          /.*cpu os ([0-9]+)_([0-9]+).*/,
          /.*cpu iphone os ([0-9]+)_([0-9]+).*/
        ]
      },
      {
        name: 'Android',
        search: checkContains('android'),
        versionRegexes: [/.*?android\ ?([0-9]+)\.([0-9]+).*/]
      },
      {
        name: 'OSX',
        search: checkContains('mac os x'),
        versionRegexes: [/.*?mac\ os\ x\ ?([0-9]+)_([0-9]+).*/]
      },
      {
        name: 'Linux',
        search: checkContains('linux'),
        versionRegexes: []
      },
      {
        name: 'Solaris',
        search: checkContains('sunos'),
        versionRegexes: []
      },
      {
        name: 'FreeBSD',
        search: checkContains('freebsd'),
        versionRegexes: []
      },
      {
        name: 'ChromeOS',
        search: checkContains('cros'),
        versionRegexes: [/.*?chrome\/([0-9]+)\.([0-9]+).*/]
      }
    ];
    var PlatformInfo = {
      browsers: constant(browsers),
      oses: constant(oses)
    };

    var edge = 'Edge';
    var chrome = 'Chrome';
    var ie = 'IE';
    var opera = 'Opera';
    var firefox = 'Firefox';
    var safari = 'Safari';
    var unknown$1 = function () {
      return nu$1({
        current: undefined,
        version: Version.unknown()
      });
    };
    var nu$1 = function (info) {
      var current = info.current;
      var version = info.version;
      var isBrowser = function (name) {
        return function () {
          return current === name;
        };
      };
      return {
        current: current,
        version: version,
        isEdge: isBrowser(edge),
        isChrome: isBrowser(chrome),
        isIE: isBrowser(ie),
        isOpera: isBrowser(opera),
        isFirefox: isBrowser(firefox),
        isSafari: isBrowser(safari)
      };
    };
    var Browser = {
      unknown: unknown$1,
      nu: nu$1,
      edge: constant(edge),
      chrome: constant(chrome),
      ie: constant(ie),
      opera: constant(opera),
      firefox: constant(firefox),
      safari: constant(safari)
    };

    var windows = 'Windows';
    var ios = 'iOS';
    var android = 'Android';
    var linux = 'Linux';
    var osx = 'OSX';
    var solaris = 'Solaris';
    var freebsd = 'FreeBSD';
    var chromeos = 'ChromeOS';
    var unknown$2 = function () {
      return nu$2({
        current: undefined,
        version: Version.unknown()
      });
    };
    var nu$2 = function (info) {
      var current = info.current;
      var version = info.version;
      var isOS = function (name) {
        return function () {
          return current === name;
        };
      };
      return {
        current: current,
        version: version,
        isWindows: isOS(windows),
        isiOS: isOS(ios),
        isAndroid: isOS(android),
        isOSX: isOS(osx),
        isLinux: isOS(linux),
        isSolaris: isOS(solaris),
        isFreeBSD: isOS(freebsd),
        isChromeOS: isOS(chromeos)
      };
    };
    var OperatingSystem = {
      unknown: unknown$2,
      nu: nu$2,
      windows: constant(windows),
      ios: constant(ios),
      android: constant(android),
      linux: constant(linux),
      osx: constant(osx),
      solaris: constant(solaris),
      freebsd: constant(freebsd),
      chromeos: constant(chromeos)
    };

    var detect$2 = function (userAgent, mediaMatch) {
      var browsers = PlatformInfo.browsers();
      var oses = PlatformInfo.oses();
      var browser = UaString.detectBrowser(browsers, userAgent).fold(Browser.unknown, Browser.nu);
      var os = UaString.detectOs(oses, userAgent).fold(OperatingSystem.unknown, OperatingSystem.nu);
      var deviceType = DeviceType(os, browser, userAgent, mediaMatch);
      return {
        browser: browser,
        os: os,
        deviceType: deviceType
      };
    };
    var PlatformDetection = { detect: detect$2 };

    var mediaMatch = function (query) {
      return window.matchMedia(query).matches;
    };
    var platform = cached(function () {
      return PlatformDetection.detect(navigator.userAgent, mediaMatch);
    });
    var detect$3 = function () {
      return platform();
    };

    var compareDocumentPosition = function (a, b, match) {
      return (a.compareDocumentPosition(b) & match) !== 0;
    };
    var documentPositionContainedBy = function (a, b) {
      return compareDocumentPosition(a, b, Node.DOCUMENT_POSITION_CONTAINED_BY);
    };

    var COMMENT = 8;
    var DOCUMENT = 9;
    var DOCUMENT_FRAGMENT = 11;
    var ELEMENT = 1;
    var TEXT = 3;

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

    var is = function (element, selector) {
      var dom = element.dom;
      if (dom.nodeType !== ELEMENT) {
        return false;
      } else {
        var elem = dom;
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
    var bypassSelector = function (dom) {
      return dom.nodeType !== ELEMENT && dom.nodeType !== DOCUMENT && dom.nodeType !== DOCUMENT_FRAGMENT || dom.childElementCount === 0;
    };
    var all = function (selector, scope) {
      var base = scope === undefined ? document : scope.dom;
      return bypassSelector(base) ? [] : map(base.querySelectorAll(selector), SugarElement.fromDom);
    };
    var one = function (selector, scope) {
      var base = scope === undefined ? document : scope.dom;
      return bypassSelector(base) ? Optional.none() : Optional.from(base.querySelector(selector)).map(SugarElement.fromDom);
    };

    var eq$1 = function (e1, e2) {
      return e1.dom === e2.dom;
    };
    var regularContains = function (e1, e2) {
      var d1 = e1.dom;
      var d2 = e2.dom;
      return d1 === d2 ? false : d1.contains(d2);
    };
    var ieContains = function (e1, e2) {
      return documentPositionContainedBy(e1.dom, e2.dom);
    };
    var contains$2 = function (e1, e2) {
      return detect$3().browser.isIE() ? ieContains(e1, e2) : regularContains(e1, e2);
    };
    var is$1 = is;

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
    var map$1 = function (obj, f) {
      return tupleMap(obj, function (x, i) {
        return {
          k: i,
          v: f(x, i)
        };
      });
    };
    var tupleMap = function (obj, f) {
      var r = {};
      each$1(obj, function (x, i) {
        var tuple = f(x, i);
        r[tuple.k] = tuple.v;
      });
      return r;
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
    var filter$1 = function (obj, pred) {
      var t = {};
      internalFilter(obj, pred, objAcc(t), noop);
      return t;
    };
    var mapToArray = function (obj, f) {
      var r = [];
      each$1(obj, function (value, name) {
        r.push(f(value, name));
      });
      return r;
    };
    var values = function (obj) {
      return mapToArray(obj, function (v) {
        return v;
      });
    };
    var size = function (obj) {
      return keys(obj).length;
    };
    var get$1 = function (obj, key) {
      return has(obj, key) ? Optional.from(obj[key]) : Optional.none();
    };
    var has = function (obj, key) {
      return hasOwnProperty.call(obj, key);
    };
    var hasNonNullableKey = function (obj, key) {
      return has(obj, key) && obj[key] !== undefined && obj[key] !== null;
    };
    var isEmpty = function (r) {
      for (var x in r) {
        if (hasOwnProperty.call(r, x)) {
          return false;
        }
      }
      return true;
    };

    var validSectionList = [
      'tfoot',
      'thead',
      'tbody',
      'colgroup'
    ];
    var isValidSection = function (parentName) {
      return contains(validSectionList, parentName);
    };
    var grid = function (rows, columns) {
      return {
        rows: rows,
        columns: columns
      };
    };
    var address = function (row, column) {
      return {
        row: row,
        column: column
      };
    };
    var detail = function (element, rowspan, colspan) {
      return {
        element: element,
        rowspan: rowspan,
        colspan: colspan
      };
    };
    var detailnew = function (element, rowspan, colspan, isNew) {
      return {
        element: element,
        rowspan: rowspan,
        colspan: colspan,
        isNew: isNew
      };
    };
    var extended = function (element, rowspan, colspan, row, column, isLocked) {
      return {
        element: element,
        rowspan: rowspan,
        colspan: colspan,
        row: row,
        column: column,
        isLocked: isLocked
      };
    };
    var rowdata = function (element, cells, section) {
      return {
        element: element,
        cells: cells,
        section: section
      };
    };
    var elementnew = function (element, isNew, isLocked) {
      return {
        element: element,
        isNew: isNew,
        isLocked: isLocked
      };
    };
    var rowdatanew = function (element, cells, section, isNew) {
      return {
        element: element,
        cells: cells,
        section: section,
        isNew: isNew
      };
    };
    var rowcells = function (cells, section) {
      return {
        cells: cells,
        section: section
      };
    };
    var rowdetails = function (details, section) {
      return {
        details: details,
        section: section
      };
    };
    var bounds = function (startRow, startCol, finishRow, finishCol) {
      return {
        startRow: startRow,
        startCol: startCol,
        finishRow: finishRow,
        finishCol: finishCol
      };
    };
    var columnext = function (element, colspan, column) {
      return {
        element: element,
        colspan: colspan,
        column: column
      };
    };

    var Global = typeof window !== 'undefined' ? window : Function('return this;')();

    var name = function (element) {
      var r = element.dom.nodeName;
      return r.toLowerCase();
    };
    var type = function (element) {
      return element.dom.nodeType;
    };
    var isType$1 = function (t) {
      return function (element) {
        return type(element) === t;
      };
    };
    var isComment = function (element) {
      return type(element) === COMMENT || name(element) === '#comment';
    };
    var isElement = isType$1(ELEMENT);
    var isText = isType$1(TEXT);
    var isDocument = isType$1(DOCUMENT);
    var isDocumentFragment = isType$1(DOCUMENT_FRAGMENT);
    var isTag = function (tag) {
      return function (e) {
        return isElement(e) && name(e) === tag;
      };
    };

    var owner = function (element) {
      return SugarElement.fromDom(element.dom.ownerDocument);
    };
    var documentOrOwner = function (dos) {
      return isDocument(dos) ? dos : owner(dos);
    };
    var defaultView = function (element) {
      return SugarElement.fromDom(documentOrOwner(element).dom.defaultView);
    };
    var parent = function (element) {
      return Optional.from(element.dom.parentNode).map(SugarElement.fromDom);
    };
    var parents = function (element, isRoot) {
      var stop = isFunction(isRoot) ? isRoot : never;
      var dom = element.dom;
      var ret = [];
      while (dom.parentNode !== null && dom.parentNode !== undefined) {
        var rawParent = dom.parentNode;
        var p = SugarElement.fromDom(rawParent);
        ret.push(p);
        if (stop(p) === true) {
          break;
        } else {
          dom = rawParent;
        }
      }
      return ret;
    };
    var offsetParent = function (element) {
      return Optional.from(element.dom.offsetParent).map(SugarElement.fromDom);
    };
    var prevSibling = function (element) {
      return Optional.from(element.dom.previousSibling).map(SugarElement.fromDom);
    };
    var nextSibling = function (element) {
      return Optional.from(element.dom.nextSibling).map(SugarElement.fromDom);
    };
    var children = function (element) {
      return map(element.dom.childNodes, SugarElement.fromDom);
    };
    var child = function (element, index) {
      var cs = element.dom.childNodes;
      return Optional.from(cs[index]).map(SugarElement.fromDom);
    };
    var firstChild = function (element) {
      return child(element, 0);
    };

    var isShadowRoot = function (dos) {
      return isDocumentFragment(dos) && isNonNullable(dos.dom.host);
    };
    var supported = isFunction(Element.prototype.attachShadow) && isFunction(Node.prototype.getRootNode);
    var isSupported = constant(supported);
    var getRootNode = supported ? function (e) {
      return SugarElement.fromDom(e.dom.getRootNode());
    } : documentOrOwner;
    var getShadowRoot = function (e) {
      var r = getRootNode(e);
      return isShadowRoot(r) ? Optional.some(r) : Optional.none();
    };
    var getShadowHost = function (e) {
      return SugarElement.fromDom(e.dom.host);
    };
    var getOriginalEventTarget = function (event) {
      if (isSupported() && isNonNullable(event.target)) {
        var el = SugarElement.fromDom(event.target);
        if (isElement(el) && isOpenShadowHost(el)) {
          if (event.composed && event.composedPath) {
            var composedPath = event.composedPath();
            if (composedPath) {
              return head(composedPath);
            }
          }
        }
      }
      return Optional.from(event.target);
    };
    var isOpenShadowHost = function (element) {
      return isNonNullable(element.dom.shadowRoot);
    };

    var inBody = function (element) {
      var dom = isText(element) ? element.dom.parentNode : element.dom;
      if (dom === undefined || dom === null || dom.ownerDocument === null) {
        return false;
      }
      var doc = dom.ownerDocument;
      return getShadowRoot(SugarElement.fromDom(dom)).fold(function () {
        return doc.body.contains(dom);
      }, compose1(inBody, getShadowHost));
    };
    var body = function () {
      return getBody(SugarElement.fromDom(document));
    };
    var getBody = function (doc) {
      var b = doc.dom.body;
      if (b === null || b === undefined) {
        throw new Error('Body is not available yet');
      }
      return SugarElement.fromDom(b);
    };

    var ancestors = function (scope, predicate, isRoot) {
      return filter(parents(scope, isRoot), predicate);
    };
    var children$1 = function (scope, predicate) {
      return filter(children(scope), predicate);
    };
    var descendants = function (scope, predicate) {
      var result = [];
      each(children(scope), function (x) {
        if (predicate(x)) {
          result = result.concat([x]);
        }
        result = result.concat(descendants(x, predicate));
      });
      return result;
    };

    var ancestors$1 = function (scope, selector, isRoot) {
      return ancestors(scope, function (e) {
        return is(e, selector);
      }, isRoot);
    };
    var children$2 = function (scope, selector) {
      return children$1(scope, function (e) {
        return is(e, selector);
      });
    };
    var descendants$1 = function (scope, selector) {
      return all(selector, scope);
    };

    function ClosestOrAncestor (is, ancestor, scope, a, isRoot) {
      if (is(scope, a)) {
        return Optional.some(scope);
      } else if (isFunction(isRoot) && isRoot(scope)) {
        return Optional.none();
      } else {
        return ancestor(scope, a, isRoot);
      }
    }

    var ancestor = function (scope, predicate, isRoot) {
      var element = scope.dom;
      var stop = isFunction(isRoot) ? isRoot : never;
      while (element.parentNode) {
        element = element.parentNode;
        var el = SugarElement.fromDom(element);
        if (predicate(el)) {
          return Optional.some(el);
        } else if (stop(el)) {
          break;
        }
      }
      return Optional.none();
    };
    var closest = function (scope, predicate, isRoot) {
      var is = function (s, test) {
        return test(s);
      };
      return ClosestOrAncestor(is, ancestor, scope, predicate, isRoot);
    };
    var child$1 = function (scope, predicate) {
      var pred = function (node) {
        return predicate(SugarElement.fromDom(node));
      };
      var result = find(scope.dom.childNodes, pred);
      return result.map(SugarElement.fromDom);
    };
    var descendant = function (scope, predicate) {
      var descend = function (node) {
        for (var i = 0; i < node.childNodes.length; i++) {
          var child_1 = SugarElement.fromDom(node.childNodes[i]);
          if (predicate(child_1)) {
            return Optional.some(child_1);
          }
          var res = descend(node.childNodes[i]);
          if (res.isSome()) {
            return res;
          }
        }
        return Optional.none();
      };
      return descend(scope.dom);
    };

    var ancestor$1 = function (scope, selector, isRoot) {
      return ancestor(scope, function (e) {
        return is(e, selector);
      }, isRoot);
    };
    var child$2 = function (scope, selector) {
      return child$1(scope, function (e) {
        return is(e, selector);
      });
    };
    var descendant$1 = function (scope, selector) {
      return one(selector, scope);
    };
    var closest$1 = function (scope, selector, isRoot) {
      var is$1 = function (element, selector) {
        return is(element, selector);
      };
      return ClosestOrAncestor(is$1, ancestor$1, scope, selector, isRoot);
    };

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
    var setAll = function (element, attrs) {
      var dom = element.dom;
      each$1(attrs, function (v, k) {
        rawSet(dom, k, v);
      });
    };
    var get$2 = function (element, key) {
      var v = element.dom.getAttribute(key);
      return v === null ? undefined : v;
    };
    var getOpt = function (element, key) {
      return Optional.from(get$2(element, key));
    };
    var remove = function (element, key) {
      element.dom.removeAttribute(key);
    };
    var clone = function (element) {
      return foldl(element.dom.attributes, function (acc, attr) {
        acc[attr.name] = attr.value;
        return acc;
      }, {});
    };

    var isSupported$1 = function (dom) {
      return dom.style !== undefined && isFunction(dom.style.getPropertyValue);
    };

    var internalSet = function (dom, property, value) {
      if (!isString(value)) {
        console.error('Invalid call to CSS.set. Property ', property, ':: Value ', value, ':: Element ', dom);
        throw new Error('CSS value must be a string: ' + value);
      }
      if (isSupported$1(dom)) {
        dom.style.setProperty(property, value);
      }
    };
    var internalRemove = function (dom, property) {
      if (isSupported$1(dom)) {
        dom.style.removeProperty(property);
      }
    };
    var set$1 = function (element, property, value) {
      var dom = element.dom;
      internalSet(dom, property, value);
    };
    var setAll$1 = function (element, css) {
      var dom = element.dom;
      each$1(css, function (v, k) {
        internalSet(dom, k, v);
      });
    };
    var get$3 = function (element, property) {
      var dom = element.dom;
      var styles = window.getComputedStyle(dom);
      var r = styles.getPropertyValue(property);
      return r === '' && !inBody(element) ? getUnsafeProperty(dom, property) : r;
    };
    var getUnsafeProperty = function (dom, property) {
      return isSupported$1(dom) ? dom.style.getPropertyValue(property) : '';
    };
    var getRaw = function (element, property) {
      var dom = element.dom;
      var raw = getUnsafeProperty(dom, property);
      return Optional.from(raw).filter(function (r) {
        return r.length > 0;
      });
    };
    var remove$1 = function (element, property) {
      var dom = element.dom;
      internalRemove(dom, property);
      if (getOpt(element, 'style').map(trim).is('')) {
        remove(element, 'style');
      }
    };
    var copy = function (source, target) {
      var sourceDom = source.dom;
      var targetDom = target.dom;
      if (isSupported$1(sourceDom) && isSupported$1(targetDom)) {
        targetDom.style.cssText = sourceDom.style.cssText;
      }
    };

    var getAttrValue = function (cell, name, fallback) {
      if (fallback === void 0) {
        fallback = 0;
      }
      return getOpt(cell, name).map(function (value) {
        return parseInt(value, 10);
      }).getOr(fallback);
    };
    var getSpan = function (cell, type) {
      return getAttrValue(cell, type, 1);
    };
    var hasColspan = function (cell) {
      return getSpan(cell, 'colspan') > 1;
    };
    var hasRowspan = function (cell) {
      return getSpan(cell, 'rowspan') > 1;
    };
    var getCssValue = function (element, property) {
      return parseInt(get$3(element, property), 10);
    };
    var minWidth = constant(10);
    var minHeight = constant(10);

    var firstLayer = function (scope, selector) {
      return filterFirstLayer(scope, selector, always);
    };
    var filterFirstLayer = function (scope, selector, predicate) {
      return bind(children(scope), function (x) {
        if (is(x, selector)) {
          return predicate(x) ? [x] : [];
        } else {
          return filterFirstLayer(x, selector, predicate);
        }
      });
    };

    var lookup = function (tags, element, isRoot) {
      if (isRoot === void 0) {
        isRoot = never;
      }
      if (isRoot(element)) {
        return Optional.none();
      }
      if (contains(tags, name(element))) {
        return Optional.some(element);
      }
      var isRootOrUpperTable = function (elm) {
        return is(elm, 'table') || isRoot(elm);
      };
      return ancestor$1(element, tags.join(','), isRootOrUpperTable);
    };
    var cell = function (element, isRoot) {
      return lookup([
        'td',
        'th'
      ], element, isRoot);
    };
    var cells = function (ancestor) {
      return firstLayer(ancestor, 'th,td');
    };
    var columns = function (ancestor) {
      if (is(ancestor, 'colgroup')) {
        return children$2(ancestor, 'col');
      } else {
        return bind(columnGroups(ancestor), function (columnGroup) {
          return children$2(columnGroup, 'col');
        });
      }
    };
    var table = function (element, isRoot) {
      return closest$1(element, 'table', isRoot);
    };
    var rows = function (ancestor) {
      return firstLayer(ancestor, 'tr');
    };
    var columnGroups = function (ancestor) {
      return table(ancestor).fold(constant([]), function (table) {
        return children$2(table, 'colgroup');
      });
    };

    var fromRowsOrColGroups = function (elems, getSection) {
      return map(elems, function (row) {
        if (name(row) === 'colgroup') {
          var cells$1 = map(columns(row), function (column) {
            var colspan = getAttrValue(column, 'span', 1);
            return detail(column, 1, colspan);
          });
          return rowdata(row, cells$1, 'colgroup');
        } else {
          var cells$1 = map(cells(row), function (cell) {
            var rowspan = getAttrValue(cell, 'rowspan', 1);
            var colspan = getAttrValue(cell, 'colspan', 1);
            return detail(cell, rowspan, colspan);
          });
          return rowdata(row, cells$1, getSection(row));
        }
      });
    };
    var getParentSection = function (group) {
      return parent(group).map(function (parent) {
        var parentName = name(parent);
        return isValidSection(parentName) ? parentName : 'tbody';
      }).getOr('tbody');
    };
    var fromTable = function (table) {
      var rows$1 = rows(table);
      var columnGroups$1 = columnGroups(table);
      var elems = __spreadArrays(columnGroups$1, rows$1);
      return fromRowsOrColGroups(elems, getParentSection);
    };
    var fromPastedRows = function (elems, section) {
      return fromRowsOrColGroups(elems, function () {
        return section;
      });
    };

    var addCells = function (gridRow, index, cells) {
      var existingCells = gridRow.cells;
      var before = existingCells.slice(0, index);
      var after = existingCells.slice(index);
      var newCells = before.concat(cells).concat(after);
      return setCells(gridRow, newCells);
    };
    var addCell = function (gridRow, index, cell) {
      return addCells(gridRow, index, [cell]);
    };
    var mutateCell = function (gridRow, index, cell) {
      var cells = gridRow.cells;
      cells[index] = cell;
    };
    var setCells = function (gridRow, cells) {
      return rowcells(cells, gridRow.section);
    };
    var mapCells = function (gridRow, f) {
      var cells = gridRow.cells;
      var r = map(cells, f);
      return rowcells(r, gridRow.section);
    };
    var getCell = function (gridRow, index) {
      return gridRow.cells[index];
    };
    var getCellElement = function (gridRow, index) {
      return getCell(gridRow, index).element;
    };
    var cellLength = function (gridRow) {
      return gridRow.cells.length;
    };
    var extractGridDetails = function (grid) {
      var result = partition(grid, function (row) {
        return row.section === 'colgroup';
      });
      return {
        rows: result.fail,
        cols: result.pass
      };
    };

    var LOCKED_COL_ATTR = 'data-snooker-locked-cols';
    var getLockedColumnsFromTable = function (table) {
      return getOpt(table, LOCKED_COL_ATTR).bind(function (lockedColStr) {
        return Optional.from(lockedColStr.match(/\d+/g));
      }).map(function (lockedCols) {
        return mapToObject(lockedCols, always);
      });
    };
    var getLockedColumnsFromGrid = function (grid) {
      var locked = foldl(extractGridDetails(grid).rows, function (acc, row) {
        each(row.cells, function (cell, idx) {
          if (cell.isLocked) {
            acc[idx] = true;
          }
        });
        return acc;
      }, {});
      var lockedArr = mapToArray(locked, function (_val, key) {
        return parseInt(key, 10);
      });
      return sort(lockedArr);
    };

    var key = function (row, column) {
      return row + ',' + column;
    };
    var getAt = function (warehouse, row, column) {
      var raw = warehouse.access[key(row, column)];
      return raw !== undefined ? Optional.some(raw) : Optional.none();
    };
    var findItem = function (warehouse, item, comparator) {
      var filtered = filterItems(warehouse, function (detail) {
        return comparator(item, detail.element);
      });
      return filtered.length > 0 ? Optional.some(filtered[0]) : Optional.none();
    };
    var filterItems = function (warehouse, predicate) {
      var all = bind(warehouse.all, function (r) {
        return r.cells;
      });
      return filter(all, predicate);
    };
    var generateColumns = function (rowData) {
      var columnsGroup = {};
      var index = 0;
      each(rowData.cells, function (column) {
        var colspan = column.colspan;
        range(colspan, function (columnIndex) {
          var colIndex = index + columnIndex;
          columnsGroup[colIndex] = columnext(column.element, colspan, colIndex);
        });
        index += colspan;
      });
      return columnsGroup;
    };
    var generate = function (list) {
      var access = {};
      var cells = [];
      var columns = {};
      var tableOpt = head(list).map(function (rowData) {
        return rowData.element;
      }).bind(table);
      var lockedColumns = tableOpt.bind(getLockedColumnsFromTable).getOr({});
      var maxRows = 0;
      var maxColumns = 0;
      var rowCount = 0;
      each(list, function (rowData) {
        if (rowData.section === 'colgroup') {
          columns = generateColumns(rowData);
        } else {
          var currentRow_1 = [];
          each(rowData.cells, function (rowCell) {
            var start = 0;
            while (access[key(rowCount, start)] !== undefined) {
              start++;
            }
            var isLocked = hasNonNullableKey(lockedColumns, start.toString());
            var current = extended(rowCell.element, rowCell.rowspan, rowCell.colspan, rowCount, start, isLocked);
            for (var occupiedColumnPosition = 0; occupiedColumnPosition < rowCell.colspan; occupiedColumnPosition++) {
              for (var occupiedRowPosition = 0; occupiedRowPosition < rowCell.rowspan; occupiedRowPosition++) {
                var rowPosition = rowCount + occupiedRowPosition;
                var columnPosition = start + occupiedColumnPosition;
                var newpos = key(rowPosition, columnPosition);
                access[newpos] = current;
                maxColumns = Math.max(maxColumns, columnPosition + 1);
              }
            }
            currentRow_1.push(current);
          });
          maxRows++;
          cells.push(rowdata(rowData.element, currentRow_1, rowData.section));
          rowCount++;
        }
      });
      var grid$1 = grid(maxRows, maxColumns);
      return {
        grid: grid$1,
        access: access,
        all: cells,
        columns: columns
      };
    };
    var fromTable$1 = function (table) {
      var list = fromTable(table);
      return generate(list);
    };
    var justCells = function (warehouse) {
      return bind(warehouse.all, function (w) {
        return w.cells;
      });
    };
    var justColumns = function (warehouse) {
      return values(warehouse.columns);
    };
    var hasColumns = function (warehouse) {
      return keys(warehouse.columns).length > 0;
    };
    var getColumnAt = function (warehouse, columnIndex) {
      return Optional.from(warehouse.columns[columnIndex]);
    };
    var Warehouse = {
      fromTable: fromTable$1,
      generate: generate,
      getAt: getAt,
      findItem: findItem,
      filterItems: filterItems,
      justCells: justCells,
      justColumns: justColumns,
      hasColumns: hasColumns,
      getColumnAt: getColumnAt
    };

    var inSelection = function (bounds, detail) {
      var leftEdge = detail.column;
      var rightEdge = detail.column + detail.colspan - 1;
      var topEdge = detail.row;
      var bottomEdge = detail.row + detail.rowspan - 1;
      return leftEdge <= bounds.finishCol && rightEdge >= bounds.startCol && (topEdge <= bounds.finishRow && bottomEdge >= bounds.startRow);
    };
    var isWithin = function (bounds, detail) {
      return detail.column >= bounds.startCol && detail.column + detail.colspan - 1 <= bounds.finishCol && detail.row >= bounds.startRow && detail.row + detail.rowspan - 1 <= bounds.finishRow;
    };
    var isRectangular = function (warehouse, bounds) {
      var isRect = true;
      var detailIsWithin = curry(isWithin, bounds);
      for (var i = bounds.startRow; i <= bounds.finishRow; i++) {
        for (var j = bounds.startCol; j <= bounds.finishCol; j++) {
          isRect = isRect && Warehouse.getAt(warehouse, i, j).exists(detailIsWithin);
        }
      }
      return isRect ? Optional.some(bounds) : Optional.none();
    };

    var getBounds = function (detailA, detailB) {
      return bounds(Math.min(detailA.row, detailB.row), Math.min(detailA.column, detailB.column), Math.max(detailA.row + detailA.rowspan - 1, detailB.row + detailB.rowspan - 1), Math.max(detailA.column + detailA.colspan - 1, detailB.column + detailB.colspan - 1));
    };
    var getAnyBox = function (warehouse, startCell, finishCell) {
      var startCoords = Warehouse.findItem(warehouse, startCell, eq$1);
      var finishCoords = Warehouse.findItem(warehouse, finishCell, eq$1);
      return startCoords.bind(function (sc) {
        return finishCoords.map(function (fc) {
          return getBounds(sc, fc);
        });
      });
    };
    var getBox = function (warehouse, startCell, finishCell) {
      return getAnyBox(warehouse, startCell, finishCell).bind(function (bounds) {
        return isRectangular(warehouse, bounds);
      });
    };

    var moveBy = function (warehouse, cell, row, column) {
      return Warehouse.findItem(warehouse, cell, eq$1).bind(function (detail) {
        var startRow = row > 0 ? detail.row + detail.rowspan - 1 : detail.row;
        var startCol = column > 0 ? detail.column + detail.colspan - 1 : detail.column;
        var dest = Warehouse.getAt(warehouse, startRow + row, startCol + column);
        return dest.map(function (d) {
          return d.element;
        });
      });
    };
    var intercepts = function (warehouse, start, finish) {
      return getAnyBox(warehouse, start, finish).map(function (bounds) {
        var inside = Warehouse.filterItems(warehouse, curry(inSelection, bounds));
        return map(inside, function (detail) {
          return detail.element;
        });
      });
    };
    var parentCell = function (warehouse, innerCell) {
      var isContainedBy = function (c1, c2) {
        return contains$2(c2, c1);
      };
      return Warehouse.findItem(warehouse, innerCell, isContainedBy).map(function (detail) {
        return detail.element;
      });
    };

    var moveBy$1 = function (cell, deltaRow, deltaColumn) {
      return table(cell).bind(function (table) {
        var warehouse = getWarehouse(table);
        return moveBy(warehouse, cell, deltaRow, deltaColumn);
      });
    };
    var intercepts$1 = function (table, first, last) {
      var warehouse = getWarehouse(table);
      return intercepts(warehouse, first, last);
    };
    var nestedIntercepts = function (table, first, firstTable, last, lastTable) {
      var warehouse = getWarehouse(table);
      var optStartCell = eq$1(table, firstTable) ? Optional.some(first) : parentCell(warehouse, first);
      var optLastCell = eq$1(table, lastTable) ? Optional.some(last) : parentCell(warehouse, last);
      return optStartCell.bind(function (startCell) {
        return optLastCell.bind(function (lastCell) {
          return intercepts(warehouse, startCell, lastCell);
        });
      });
    };
    var getBox$1 = function (table, first, last) {
      var warehouse = getWarehouse(table);
      return getBox(warehouse, first, last);
    };
    var getWarehouse = Warehouse.fromTable;

    var before = function (marker, element) {
      var parent$1 = parent(marker);
      parent$1.each(function (v) {
        v.dom.insertBefore(element.dom, marker.dom);
      });
    };
    var after = function (marker, element) {
      var sibling = nextSibling(marker);
      sibling.fold(function () {
        var parent$1 = parent(marker);
        parent$1.each(function (v) {
          append(v, element);
        });
      }, function (v) {
        before(v, element);
      });
    };
    var prepend = function (parent, element) {
      var firstChild$1 = firstChild(parent);
      firstChild$1.fold(function () {
        append(parent, element);
      }, function (v) {
        parent.dom.insertBefore(element.dom, v.dom);
      });
    };
    var append = function (parent, element) {
      parent.dom.appendChild(element.dom);
    };
    var wrap = function (element, wrapper) {
      before(element, wrapper);
      append(wrapper, element);
    };

    var before$1 = function (marker, elements) {
      each(elements, function (x) {
        before(marker, x);
      });
    };
    var after$1 = function (marker, elements) {
      each(elements, function (x, i) {
        var e = i === 0 ? marker : elements[i - 1];
        after(e, x);
      });
    };
    var append$1 = function (parent, elements) {
      each(elements, function (x) {
        append(parent, x);
      });
    };

    var empty = function (element) {
      element.dom.textContent = '';
      each(children(element), function (rogue) {
        remove$2(rogue);
      });
    };
    var remove$2 = function (element) {
      var dom = element.dom;
      if (dom.parentNode !== null) {
        dom.parentNode.removeChild(dom);
      }
    };
    var unwrap = function (wrapper) {
      var children$1 = children(wrapper);
      if (children$1.length > 0) {
        before$1(wrapper, children$1);
      }
      remove$2(wrapper);
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
    var get$4 = function (element) {
      return api.get(element);
    };
    var getOption = function (element) {
      return api.getOption(element);
    };
    var set$2 = function (element, value) {
      return api.set(element, value);
    };

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

    function DomUniverse () {
      var clone$1 = function (element) {
        return SugarElement.fromDom(element.dom.cloneNode(false));
      };
      var document = function (element) {
        return documentOrOwner(element).dom;
      };
      var isBoundary = function (element) {
        if (!isElement(element)) {
          return false;
        }
        if (name(element) === 'body') {
          return true;
        }
        return contains(TagBoundaries, name(element));
      };
      var isEmptyTag = function (element) {
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
      var isNonEditable = function (element) {
        return isElement(element) && get$2(element, 'contenteditable') === 'false';
      };
      var comparePosition = function (element, other) {
        return element.dom.compareDocumentPosition(other.dom);
      };
      var copyAttributesTo = function (source, destination) {
        var as = clone(source);
        setAll(destination, as);
      };
      var isSpecial = function (element) {
        var tag = name(element);
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
      return {
        up: constant({
          selector: ancestor$1,
          closest: closest$1,
          predicate: ancestor,
          all: parents
        }),
        down: constant({
          selector: descendants$1,
          predicate: descendants
        }),
        styles: constant({
          get: get$3,
          getRaw: getRaw,
          set: set$1,
          remove: remove$1
        }),
        attrs: constant({
          get: get$2,
          set: set,
          remove: remove,
          copyTo: copyAttributesTo
        }),
        insert: constant({
          before: before,
          after: after,
          afterAll: after$1,
          append: append,
          appendAll: append$1,
          prepend: prepend,
          wrap: wrap
        }),
        remove: constant({
          unwrap: unwrap,
          remove: remove$2
        }),
        create: constant({
          nu: SugarElement.fromTag,
          clone: clone$1,
          text: SugarElement.fromText
        }),
        query: constant({
          comparePosition: comparePosition,
          prevSibling: prevSibling,
          nextSibling: nextSibling
        }),
        property: constant({
          children: children,
          name: name,
          parent: parent,
          document: document,
          isText: isText,
          isComment: isComment,
          isElement: isElement,
          isSpecial: isSpecial,
          getText: get$4,
          setText: set$2,
          isBoundary: isBoundary,
          isEmptyTag: isEmptyTag,
          isNonEditable: isNonEditable
        }),
        eq: eq$1,
        is: is$1
      };
    }

    var all$1 = function (universe, look, elements, f) {
      var head = elements[0];
      var tail = elements.slice(1);
      return f(universe, look, head, tail);
    };
    var oneAll = function (universe, look, elements) {
      return elements.length > 0 ? all$1(universe, look, elements, unsafeOne) : Optional.none();
    };
    var unsafeOne = function (universe, look, head, tail) {
      var start = look(universe, head);
      return foldr(tail, function (b, a) {
        var current = look(universe, a);
        return commonElement(universe, b, current);
      }, start);
    };
    var commonElement = function (universe, start, end) {
      return start.bind(function (s) {
        return end.filter(curry(universe.eq, s));
      });
    };

    var eq$2 = function (universe, item) {
      return curry(universe.eq, item);
    };
    var ancestors$2 = function (universe, start, end, isRoot) {
      if (isRoot === void 0) {
        isRoot = never;
      }
      var ps1 = [start].concat(universe.up().all(start));
      var ps2 = [end].concat(universe.up().all(end));
      var prune = function (path) {
        var index = findIndex(path, isRoot);
        return index.fold(function () {
          return path;
        }, function (ind) {
          return path.slice(0, ind + 1);
        });
      };
      var pruned1 = prune(ps1);
      var pruned2 = prune(ps2);
      var shared = find(pruned1, function (x) {
        return exists(pruned2, eq$2(universe, x));
      });
      return {
        firstpath: pruned1,
        secondpath: pruned2,
        shared: shared
      };
    };

    var sharedOne = oneAll;
    var ancestors$3 = ancestors$2;

    var universe = DomUniverse();
    var sharedOne$1 = function (look, elements) {
      return sharedOne(universe, function (_universe, element) {
        return look(element);
      }, elements);
    };
    var ancestors$4 = function (start, finish, isRoot) {
      return ancestors$3(universe, start, finish, isRoot);
    };

    var lookupTable = function (container) {
      return ancestor$1(container, 'table');
    };
    var identify = function (start, finish, isRoot) {
      var getIsRoot = function (rootTable) {
        return function (element) {
          return isRoot !== undefined && isRoot(element) || eq$1(element, rootTable);
        };
      };
      if (eq$1(start, finish)) {
        return Optional.some({
          boxes: Optional.some([start]),
          start: start,
          finish: finish
        });
      } else {
        return lookupTable(start).bind(function (startTable) {
          return lookupTable(finish).bind(function (finishTable) {
            if (eq$1(startTable, finishTable)) {
              return Optional.some({
                boxes: intercepts$1(startTable, start, finish),
                start: start,
                finish: finish
              });
            } else if (contains$2(startTable, finishTable)) {
              var ancestorCells = ancestors$1(finish, 'td,th', getIsRoot(startTable));
              var finishCell = ancestorCells.length > 0 ? ancestorCells[ancestorCells.length - 1] : finish;
              return Optional.some({
                boxes: nestedIntercepts(startTable, start, startTable, finish, finishTable),
                start: start,
                finish: finishCell
              });
            } else if (contains$2(finishTable, startTable)) {
              var ancestorCells = ancestors$1(start, 'td,th', getIsRoot(finishTable));
              var startCell = ancestorCells.length > 0 ? ancestorCells[ancestorCells.length - 1] : start;
              return Optional.some({
                boxes: nestedIntercepts(finishTable, start, startTable, finish, finishTable),
                start: start,
                finish: startCell
              });
            } else {
              return ancestors$4(start, finish).shared.bind(function (lca) {
                return closest$1(lca, 'table', isRoot).bind(function (lcaTable) {
                  var finishAncestorCells = ancestors$1(finish, 'td,th', getIsRoot(lcaTable));
                  var finishCell = finishAncestorCells.length > 0 ? finishAncestorCells[finishAncestorCells.length - 1] : finish;
                  var startAncestorCells = ancestors$1(start, 'td,th', getIsRoot(lcaTable));
                  var startCell = startAncestorCells.length > 0 ? startAncestorCells[startAncestorCells.length - 1] : start;
                  return Optional.some({
                    boxes: nestedIntercepts(lcaTable, start, startTable, finish, finishTable),
                    start: startCell,
                    finish: finishCell
                  });
                });
              });
            }
          });
        });
      }
    };
    var retrieve = function (container, selector) {
      var sels = descendants$1(container, selector);
      return sels.length > 0 ? Optional.some(sels) : Optional.none();
    };
    var getLast = function (boxes, lastSelectedSelector) {
      return find(boxes, function (box) {
        return is(box, lastSelectedSelector);
      });
    };
    var getEdges = function (container, firstSelectedSelector, lastSelectedSelector) {
      return descendant$1(container, firstSelectedSelector).bind(function (first) {
        return descendant$1(container, lastSelectedSelector).bind(function (last) {
          return sharedOne$1(lookupTable, [
            first,
            last
          ]).map(function (table) {
            return {
              first: first,
              last: last,
              table: table
            };
          });
        });
      });
    };
    var expandTo = function (finish, firstSelectedSelector) {
      return ancestor$1(finish, 'table').bind(function (table) {
        return descendant$1(table, firstSelectedSelector).bind(function (start) {
          return identify(start, finish).bind(function (identified) {
            return identified.boxes.map(function (boxes) {
              return {
                boxes: boxes,
                start: identified.start,
                finish: identified.finish
              };
            });
          });
        });
      });
    };
    var shiftSelection = function (boxes, deltaRow, deltaColumn, firstSelectedSelector, lastSelectedSelector) {
      return getLast(boxes, lastSelectedSelector).bind(function (last) {
        return moveBy$1(last, deltaRow, deltaColumn).bind(function (finish) {
          return expandTo(finish, firstSelectedSelector);
        });
      });
    };

    var retrieve$1 = function (container, selector) {
      return retrieve(container, selector);
    };
    var retrieveBox = function (container, firstSelectedSelector, lastSelectedSelector) {
      return getEdges(container, firstSelectedSelector, lastSelectedSelector).bind(function (edges) {
        var isRoot = function (ancestor) {
          return eq$1(container, ancestor);
        };
        var sectionSelector = 'thead,tfoot,tbody,table';
        var firstAncestor = ancestor$1(edges.first, sectionSelector, isRoot);
        var lastAncestor = ancestor$1(edges.last, sectionSelector, isRoot);
        return firstAncestor.bind(function (fA) {
          return lastAncestor.bind(function (lA) {
            return eq$1(fA, lA) ? getBox$1(edges.table, edges.first, edges.last) : Optional.none();
          });
        });
      });
    };

    var generate$1 = function (cases) {
      if (!isArray(cases)) {
        throw new Error('cases must be an array');
      }
      if (cases.length === 0) {
        throw new Error('there must be at least one case');
      }
      var constructors = [];
      var adt = {};
      each(cases, function (acase, count) {
        var keys$1 = keys(acase);
        if (keys$1.length !== 1) {
          throw new Error('one and only one name per case');
        }
        var key = keys$1[0];
        var value = acase[key];
        if (adt[key] !== undefined) {
          throw new Error('duplicate key detected:' + key);
        } else if (key === 'cata') {
          throw new Error('cannot have a case named cata (sorry)');
        } else if (!isArray(value)) {
          throw new Error('case arguments must be an array');
        }
        constructors.push(key);
        adt[key] = function () {
          var args = [];
          for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
          }
          var argLength = args.length;
          if (argLength !== value.length) {
            throw new Error('Wrong number of arguments to case ' + key + '. Expected ' + value.length + ' (' + value + '), got ' + argLength);
          }
          var match = function (branches) {
            var branchKeys = keys(branches);
            if (constructors.length !== branchKeys.length) {
              throw new Error('Wrong number of arguments to match. Expected: ' + constructors.join(',') + '\nActual: ' + branchKeys.join(','));
            }
            var allReqd = forall(constructors, function (reqKey) {
              return contains(branchKeys, reqKey);
            });
            if (!allReqd) {
              throw new Error('Not all branches were specified when using match. Specified: ' + branchKeys.join(', ') + '\nRequired: ' + constructors.join(', '));
            }
            return branches[key].apply(null, args);
          };
          return {
            fold: function () {
              var foldArgs = [];
              for (var _i = 0; _i < arguments.length; _i++) {
                foldArgs[_i] = arguments[_i];
              }
              if (foldArgs.length !== cases.length) {
                throw new Error('Wrong number of arguments to fold. Expected ' + cases.length + ', got ' + foldArgs.length);
              }
              var target = foldArgs[count];
              return target.apply(null, args);
            },
            match: match,
            log: function (label) {
              console.log(label, {
                constructors: constructors,
                constructor: key,
                params: args
              });
            }
          };
        };
      });
      return adt;
    };
    var Adt = { generate: generate$1 };

    var type$1 = Adt.generate([
      { none: [] },
      { multiple: ['elements'] },
      { single: ['element'] }
    ]);
    var cata = function (subject, onNone, onMultiple, onSingle) {
      return subject.fold(onNone, onMultiple, onSingle);
    };
    var none$1 = type$1.none;
    var multiple = type$1.multiple;
    var single = type$1.single;

    var Selections = function (lazyRoot, getStart, selectedSelector) {
      var get = function () {
        return retrieve$1(lazyRoot(), selectedSelector).fold(function () {
          return getStart().map(single).getOrThunk(none$1);
        }, function (cells) {
          return multiple(cells);
        });
      };
      return { get: get };
    };

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var clone$1 = function (original, isDeep) {
      return SugarElement.fromDom(original.dom.cloneNode(isDeep));
    };
    var shallow = function (original) {
      return clone$1(original, false);
    };
    var deep = function (original) {
      return clone$1(original, true);
    };
    var shallowAs = function (original, tag) {
      var nu = SugarElement.fromTag(tag);
      var attributes = clone(original);
      setAll(nu, attributes);
      return nu;
    };
    var copy$1 = function (original, tag) {
      var nu = shallowAs(original, tag);
      var cloneChildren = children(deep(original));
      append$1(nu, cloneChildren);
      return nu;
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
    var lift2 = function (oa, ob, f) {
      return oa.isSome() && ob.isSome() ? Optional.some(f(oa.getOrDie(), ob.getOrDie())) : Optional.none();
    };
    var bindFrom = function (a, f) {
      return a !== undefined && a !== null ? f(a) : Optional.none();
    };
    var someIf = function (b, a) {
      return b ? Optional.some(a) : Optional.none();
    };

    var Dimension = function (name, getOffset) {
      var set = function (element, h) {
        if (!isNumber(h) && !h.match(/^[0-9]+$/)) {
          throw new Error(name + '.set accepts only positive integer values. Value was ' + h);
        }
        var dom = element.dom;
        if (isSupported$1(dom)) {
          dom.style[name] = h + 'px';
        }
      };
      var get = function (element) {
        var r = getOffset(element);
        if (r <= 0 || r === null) {
          var css = get$3(element, name);
          return parseFloat(css) || 0;
        }
        return r;
      };
      var getOuter = get;
      var aggregate = function (element, properties) {
        return foldl(properties, function (acc, property) {
          var val = get$3(element, property);
          var value = val === undefined ? 0 : parseInt(val, 10);
          return isNaN(value) ? acc : acc + value;
        }, 0);
      };
      var max = function (element, value, properties) {
        var cumulativeInclusions = aggregate(element, properties);
        var absoluteMax = value > cumulativeInclusions ? value - cumulativeInclusions : 0;
        return absoluteMax;
      };
      return {
        set: set,
        get: get,
        getOuter: getOuter,
        aggregate: aggregate,
        max: max
      };
    };

    var api$1 = Dimension('width', function (element) {
      return element.dom.offsetWidth;
    });
    var get$5 = function (element) {
      return api$1.get(element);
    };
    var getOuter = function (element) {
      return api$1.getOuter(element);
    };

    var columns$1 = function (warehouse, isValidCell) {
      if (isValidCell === void 0) {
        isValidCell = always;
      }
      var grid = warehouse.grid;
      var cols = range(grid.columns, identity);
      var rowsArr = range(grid.rows, identity);
      return map(cols, function (col) {
        var getBlock = function () {
          return bind(rowsArr, function (r) {
            return Warehouse.getAt(warehouse, r, col).filter(function (detail) {
              return detail.column === col;
            }).toArray();
          });
        };
        var isValid = function (detail) {
          return detail.colspan === 1 && isValidCell(detail.element);
        };
        var getFallback = function () {
          return Warehouse.getAt(warehouse, 0, col);
        };
        return decide(getBlock, isValid, getFallback);
      });
    };
    var decide = function (getBlock, isValid, getFallback) {
      var inBlock = getBlock();
      var validInBlock = find(inBlock, isValid);
      var detailOption = validInBlock.orThunk(function () {
        return Optional.from(inBlock[0]).orThunk(getFallback);
      });
      return detailOption.map(function (detail) {
        return detail.element;
      });
    };
    var rows$1 = function (warehouse) {
      var grid = warehouse.grid;
      var rowsArr = range(grid.rows, identity);
      var cols = range(grid.columns, identity);
      return map(rowsArr, function (row) {
        var getBlock = function () {
          return bind(cols, function (c) {
            return Warehouse.getAt(warehouse, row, c).filter(function (detail) {
              return detail.row === row;
            }).fold(constant([]), function (detail) {
              return [detail];
            });
          });
        };
        var isSingle = function (detail) {
          return detail.rowspan === 1;
        };
        var getFallback = function () {
          return Warehouse.getAt(warehouse, row, 0);
        };
        return decide(getBlock, isSingle, getFallback);
      });
    };

    var deduce = function (xs, index) {
      if (index < 0 || index >= xs.length - 1) {
        return Optional.none();
      }
      var current = xs[index].fold(function () {
        var rest = reverse(xs.slice(0, index));
        return findMap(rest, function (a, i) {
          return a.map(function (aa) {
            return {
              value: aa,
              delta: i + 1
            };
          });
        });
      }, function (c) {
        return Optional.some({
          value: c,
          delta: 0
        });
      });
      var next = xs[index + 1].fold(function () {
        var rest = xs.slice(index + 1);
        return findMap(rest, function (a, i) {
          return a.map(function (aa) {
            return {
              value: aa,
              delta: i + 1
            };
          });
        });
      }, function (n) {
        return Optional.some({
          value: n,
          delta: 1
        });
      });
      return current.bind(function (c) {
        return next.map(function (n) {
          var extras = n.delta + c.delta;
          return Math.abs(n.value - c.value) / extras;
        });
      });
    };

    var onDirection = function (isLtr, isRtl) {
      return function (element) {
        return getDirection(element) === 'rtl' ? isRtl : isLtr;
      };
    };
    var getDirection = function (element) {
      return get$3(element, 'direction') === 'rtl' ? 'rtl' : 'ltr';
    };

    var api$2 = Dimension('height', function (element) {
      var dom = element.dom;
      return inBody(element) ? dom.getBoundingClientRect().height : dom.offsetHeight;
    });
    var get$6 = function (element) {
      return api$2.get(element);
    };
    var getOuter$1 = function (element) {
      return api$2.getOuter(element);
    };

    var r = function (left, top) {
      var translate = function (x, y) {
        return r(left + x, top + y);
      };
      return {
        left: left,
        top: top,
        translate: translate
      };
    };
    var SugarPosition = r;

    var boxPosition = function (dom) {
      var box = dom.getBoundingClientRect();
      return SugarPosition(box.left, box.top);
    };
    var firstDefinedOrZero = function (a, b) {
      if (a !== undefined) {
        return a;
      } else {
        return b !== undefined ? b : 0;
      }
    };
    var absolute = function (element) {
      var doc = element.dom.ownerDocument;
      var body = doc.body;
      var win = doc.defaultView;
      var html = doc.documentElement;
      if (body === element.dom) {
        return SugarPosition(body.offsetLeft, body.offsetTop);
      }
      var scrollTop = firstDefinedOrZero(win === null || win === void 0 ? void 0 : win.pageYOffset, html.scrollTop);
      var scrollLeft = firstDefinedOrZero(win === null || win === void 0 ? void 0 : win.pageXOffset, html.scrollLeft);
      var clientTop = firstDefinedOrZero(html.clientTop, body.clientTop);
      var clientLeft = firstDefinedOrZero(html.clientLeft, body.clientLeft);
      return viewport(element).translate(scrollLeft - clientLeft, scrollTop - clientTop);
    };
    var viewport = function (element) {
      var dom = element.dom;
      var doc = dom.ownerDocument;
      var body = doc.body;
      if (body === dom) {
        return SugarPosition(body.offsetLeft, body.offsetTop);
      }
      if (!inBody(element)) {
        return SugarPosition(0, 0);
      }
      return boxPosition(dom);
    };

    var rowInfo = function (row, y) {
      return {
        row: row,
        y: y
      };
    };
    var colInfo = function (col, x) {
      return {
        col: col,
        x: x
      };
    };
    var rtlEdge = function (cell) {
      var pos = absolute(cell);
      return pos.left + getOuter(cell);
    };
    var ltrEdge = function (cell) {
      return absolute(cell).left;
    };
    var getLeftEdge = function (index, cell) {
      return colInfo(index, ltrEdge(cell));
    };
    var getRightEdge = function (index, cell) {
      return colInfo(index, rtlEdge(cell));
    };
    var getTop = function (cell) {
      return absolute(cell).top;
    };
    var getTopEdge = function (index, cell) {
      return rowInfo(index, getTop(cell));
    };
    var getBottomEdge = function (index, cell) {
      return rowInfo(index, getTop(cell) + getOuter$1(cell));
    };
    var findPositions = function (getInnerEdge, getOuterEdge, array) {
      if (array.length === 0) {
        return [];
      }
      var lines = map(array.slice(1), function (cellOption, index) {
        return cellOption.map(function (cell) {
          return getInnerEdge(index, cell);
        });
      });
      var lastLine = array[array.length - 1].map(function (cell) {
        return getOuterEdge(array.length - 1, cell);
      });
      return lines.concat([lastLine]);
    };
    var negate = function (step) {
      return -step;
    };
    var height = {
      delta: identity,
      positions: function (optElements) {
        return findPositions(getTopEdge, getBottomEdge, optElements);
      },
      edge: getTop
    };
    var ltr = {
      delta: identity,
      edge: ltrEdge,
      positions: function (optElements) {
        return findPositions(getLeftEdge, getRightEdge, optElements);
      }
    };
    var rtl = {
      delta: negate,
      edge: rtlEdge,
      positions: function (optElements) {
        return findPositions(getRightEdge, getLeftEdge, optElements);
      }
    };
    var detect$4 = onDirection(ltr, rtl);
    var width = {
      delta: function (amount, table) {
        return detect$4(table).delta(amount, table);
      },
      positions: function (cols, table) {
        return detect$4(table).positions(cols, table);
      },
      edge: function (cell) {
        return detect$4(cell).edge(cell);
      }
    };

    var units = {
      unsupportedLength: [
        'em',
        'ex',
        'cap',
        'ch',
        'ic',
        'rem',
        'lh',
        'rlh',
        'vw',
        'vh',
        'vi',
        'vb',
        'vmin',
        'vmax',
        'cm',
        'mm',
        'Q',
        'in',
        'pc',
        'pt',
        'px'
      ],
      fixed: [
        'px',
        'pt'
      ],
      relative: ['%'],
      empty: ['']
    };
    var pattern = function () {
      var decimalDigits = '[0-9]+';
      var signedInteger = '[+-]?' + decimalDigits;
      var exponentPart = '[eE]' + signedInteger;
      var dot = '\\.';
      var opt = function (input) {
        return '(?:' + input + ')?';
      };
      var unsignedDecimalLiteral = [
        'Infinity',
        decimalDigits + dot + opt(decimalDigits) + opt(exponentPart),
        dot + decimalDigits + opt(exponentPart),
        decimalDigits + opt(exponentPart)
      ].join('|');
      var float = '[+-]?(?:' + unsignedDecimalLiteral + ')';
      return new RegExp('^(' + float + ')(.*)$');
    }();
    var isUnit = function (unit, accepted) {
      return exists(accepted, function (acc) {
        return exists(units[acc], function (check) {
          return unit === check;
        });
      });
    };
    var parse = function (input, accepted) {
      var match = Optional.from(pattern.exec(input));
      return match.bind(function (array) {
        var value = Number(array[1]);
        var unitRaw = array[2];
        if (isUnit(unitRaw, accepted)) {
          return Optional.some({
            value: value,
            unit: unitRaw
          });
        } else {
          return Optional.none();
        }
      });
    };

    var needManualCalc = function () {
      var browser = detect$3().browser;
      return browser.isIE() || browser.isEdge();
    };
    var toNumber = function (px, fallback) {
      var num = parseFloat(px);
      return isNaN(num) ? fallback : num;
    };
    var getProp = function (elm, name, fallback) {
      return toNumber(get$3(elm, name), fallback);
    };
    var getCalculatedHeight = function (cell) {
      var height = cell.dom.getBoundingClientRect().height;
      var boxSizing = get$3(cell, 'box-sizing');
      if (boxSizing === 'border-box') {
        return height;
      } else {
        var paddingTop = getProp(cell, 'padding-top', 0);
        var paddingBottom = getProp(cell, 'padding-bottom', 0);
        var borderTop = getProp(cell, 'border-top-width', 0);
        var borderBottom = getProp(cell, 'border-bottom-width', 0);
        var borders = borderTop + borderBottom;
        return height - paddingTop - paddingBottom - borders;
      }
    };
    var getCalculatedWidth = function (cell) {
      var width = cell.dom.getBoundingClientRect().width;
      var boxSizing = get$3(cell, 'box-sizing');
      if (boxSizing === 'border-box') {
        return width;
      } else {
        var paddingLeft = getProp(cell, 'padding-left', 0);
        var paddingRight = getProp(cell, 'padding-right', 0);
        var borderLeft = getProp(cell, 'border-left-width', 0);
        var borderRight = getProp(cell, 'border-right-width', 0);
        var borders = borderLeft + borderRight;
        return width - paddingLeft - paddingRight - borders;
      }
    };
    var getHeight = function (cell) {
      return needManualCalc() ? getCalculatedHeight(cell) : getProp(cell, 'height', get$6(cell));
    };
    var getWidth = function (cell) {
      return needManualCalc() ? getCalculatedWidth(cell) : getProp(cell, 'width', get$5(cell));
    };

    var rPercentageBasedSizeRegex = /(\d+(\.\d+)?)%/;
    var rPixelBasedSizeRegex = /(\d+(\.\d+)?)px|em/;
    var getPercentSize = function (elm, getter) {
      var relativeParent = offsetParent(elm).getOr(getBody(owner(elm)));
      return getter(elm) / getter(relativeParent) * 100;
    };
    var setPixelWidth = function (cell, amount) {
      set$1(cell, 'width', amount + 'px');
    };
    var setPercentageWidth = function (cell, amount) {
      set$1(cell, 'width', amount + '%');
    };
    var setHeight = function (cell, amount) {
      set$1(cell, 'height', amount + 'px');
    };
    var getHeightValue = function (cell) {
      return getRaw(cell, 'height').getOrThunk(function () {
        return getHeight(cell) + 'px';
      });
    };
    var convert = function (cell, number, getter, setter) {
      var newSize = table(cell).map(function (table) {
        var total = getter(table);
        return Math.floor(number / 100 * total);
      }).getOr(number);
      setter(cell, newSize);
      return newSize;
    };
    var normalizePixelSize = function (value, cell, getter, setter) {
      var number = parseInt(value, 10);
      return endsWith(value, '%') && name(cell) !== 'table' ? convert(cell, number, getter, setter) : number;
    };
    var getTotalHeight = function (cell) {
      var value = getHeightValue(cell);
      if (!value) {
        return get$6(cell);
      }
      return normalizePixelSize(value, cell, get$6, setHeight);
    };
    var get$7 = function (cell, type, f) {
      var v = f(cell);
      var span = getSpan(cell, type);
      return v / span;
    };
    var getRawWidth = function (element) {
      var cssWidth = getRaw(element, 'width');
      return cssWidth.fold(function () {
        return Optional.from(get$2(element, 'width'));
      }, function (width) {
        return Optional.some(width);
      });
    };
    var normalizePercentageWidth = function (cellWidth, tableSize) {
      return cellWidth / tableSize.pixelWidth() * 100;
    };
    var choosePercentageSize = function (element, width, tableSize) {
      var percentMatch = rPercentageBasedSizeRegex.exec(width);
      if (percentMatch !== null) {
        return parseFloat(percentMatch[1]);
      } else {
        var intWidth = getWidth(element);
        return normalizePercentageWidth(intWidth, tableSize);
      }
    };
    var getPercentageWidth = function (cell, tableSize) {
      var width = getRawWidth(cell);
      return width.fold(function () {
        var intWidth = get$5(cell);
        return normalizePercentageWidth(intWidth, tableSize);
      }, function (w) {
        return choosePercentageSize(cell, w, tableSize);
      });
    };
    var normalizePixelWidth = function (cellWidth, tableSize) {
      return cellWidth / 100 * tableSize.pixelWidth();
    };
    var choosePixelSize = function (element, width, tableSize) {
      var pixelMatch = rPixelBasedSizeRegex.exec(width);
      if (pixelMatch !== null) {
        return parseInt(pixelMatch[1], 10);
      }
      var percentMatch = rPercentageBasedSizeRegex.exec(width);
      if (percentMatch !== null) {
        var floatWidth = parseFloat(percentMatch[1]);
        return normalizePixelWidth(floatWidth, tableSize);
      }
      return getWidth(element);
    };
    var getPixelWidth = function (cell, tableSize) {
      var width = getRawWidth(cell);
      return width.fold(function () {
        return getWidth(cell);
      }, function (w) {
        return choosePixelSize(cell, w, tableSize);
      });
    };
    var getHeight$1 = function (cell) {
      return get$7(cell, 'rowspan', getTotalHeight);
    };
    var getGenericWidth = function (cell) {
      var width = getRawWidth(cell);
      return width.bind(function (w) {
        return parse(w, [
          'fixed',
          'relative',
          'empty'
        ]);
      });
    };
    var setGenericWidth = function (cell, amount, unit) {
      set$1(cell, 'width', amount + unit);
    };
    var getPixelTableWidth = function (table) {
      return get$5(table) + 'px';
    };
    var getPercentTableWidth = function (table) {
      return getPercentSize(table, get$5) + '%';
    };
    var isPercentSizing = function (table) {
      return getRawWidth(table).exists(function (size) {
        return rPercentageBasedSizeRegex.test(size);
      });
    };
    var isPixelSizing = function (table) {
      return getRawWidth(table).exists(function (size) {
        return rPixelBasedSizeRegex.test(size);
      });
    };
    var isNoneSizing = function (table) {
      return getRawWidth(table).isNone();
    };
    var percentageBasedSizeRegex = constant(rPercentageBasedSizeRegex);
    var pixelBasedSizeRegex = constant(rPixelBasedSizeRegex);

    var isCol = isTag('col');
    var getRaw$1 = function (cell, property, getter) {
      return getRaw(cell, property).fold(function () {
        return getter(cell) + 'px';
      }, function (raw) {
        return raw;
      });
    };
    var getRawW = function (cell, tableSize) {
      var fallback = function (e) {
        return isCol(e) ? get$5(e) : getPixelWidth(e, tableSize);
      };
      return getRaw$1(cell, 'width', fallback);
    };
    var getRawH = function (cell) {
      return getRaw$1(cell, 'height', getHeight$1);
    };
    var justCols = function (warehouse) {
      return map(Warehouse.justColumns(warehouse), function (column) {
        return Optional.from(column.element);
      });
    };
    var hasRawStyle = function (cell, prop) {
      return getRaw(cell, prop).isSome();
    };
    var isValidColumn = function (cell) {
      return !isCol(cell) || hasRawStyle(cell, 'width');
    };
    var getDimension = function (cellOpt, index, backups, filter, getter, fallback) {
      return cellOpt.filter(filter).fold(function () {
        return fallback(deduce(backups, index));
      }, function (cell) {
        return getter(cell);
      });
    };
    var getWidthFrom = function (warehouse, table, getWidth, fallback, tableSize) {
      var columnCells = columns$1(warehouse, function (cell) {
        return hasRawStyle(cell, 'width');
      });
      var columns = Warehouse.hasColumns(warehouse) ? justCols(warehouse) : columnCells;
      var backups = [Optional.some(width.edge(table))].concat(map(width.positions(columnCells, table), function (pos) {
        return pos.map(function (p) {
          return p.x;
        });
      }));
      var colFilter = not(hasColspan);
      return map(columns, function (cellOption, c) {
        return getDimension(cellOption, c, backups, colFilter, function (column) {
          if (isValidColumn(column)) {
            return getWidth(column, tableSize);
          } else {
            var cell = bindFrom(columnCells[c], identity);
            return getDimension(cell, c, backups, colFilter, function (cell) {
              return fallback(Optional.some(get$5(cell)));
            }, fallback);
          }
        }, fallback);
      });
    };
    var getDeduced = function (deduced) {
      return deduced.map(function (d) {
        return d + 'px';
      }).getOr('');
    };
    var getRawWidths = function (warehouse, table, tableSize) {
      return getWidthFrom(warehouse, table, getRawW, getDeduced, tableSize);
    };
    var getPercentageWidths = function (warehouse, table, tableSize) {
      return getWidthFrom(warehouse, table, getPercentageWidth, function (deduced) {
        return deduced.fold(function () {
          return tableSize.minCellWidth();
        }, function (cellWidth) {
          return cellWidth / tableSize.pixelWidth() * 100;
        });
      }, tableSize);
    };
    var getPixelWidths = function (warehouse, table, tableSize) {
      return getWidthFrom(warehouse, table, getPixelWidth, function (deduced) {
        return deduced.getOrThunk(tableSize.minCellWidth);
      }, tableSize);
    };
    var getHeightFrom = function (warehouse, table, direction, getHeight, fallback) {
      var rows = rows$1(warehouse);
      var backups = [Optional.some(direction.edge(table))].concat(map(direction.positions(rows, table), function (pos) {
        return pos.map(function (p) {
          return p.y;
        });
      }));
      return map(rows, function (cellOption, c) {
        return getDimension(cellOption, c, backups, not(hasRowspan), getHeight, fallback);
      });
    };
    var getPixelHeights = function (warehouse, table, direction) {
      return getHeightFrom(warehouse, table, direction, getHeight$1, function (deduced) {
        return deduced.getOrThunk(minHeight);
      });
    };
    var getRawHeights = function (warehouse, table, direction) {
      return getHeightFrom(warehouse, table, direction, getRawH, getDeduced);
    };

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

    var noneSize = function (table) {
      var getWidth = function () {
        return get$5(table);
      };
      var zero = constant(0);
      var getWidths = function (warehouse, tableSize) {
        return getPixelWidths(warehouse, table, tableSize);
      };
      return {
        width: getWidth,
        pixelWidth: getWidth,
        getWidths: getWidths,
        getCellDelta: zero,
        singleColumnWidth: constant([0]),
        minCellWidth: zero,
        setElementWidth: noop,
        adjustTableWidth: noop,
        isRelative: true,
        label: 'none'
      };
    };
    var percentageSize = function (initialWidth, table) {
      var floatWidth = Cell(parseFloat(initialWidth));
      var pixelWidth = Cell(get$5(table));
      var getCellDelta = function (delta) {
        return delta / pixelWidth.get() * 100;
      };
      var singleColumnWidth = function (w, _delta) {
        return [100 - w];
      };
      var minCellWidth = function () {
        return minWidth() / pixelWidth.get() * 100;
      };
      var adjustTableWidth = function (delta) {
        var currentWidth = floatWidth.get();
        var change = delta / 100 * currentWidth;
        var newWidth = currentWidth + change;
        setPercentageWidth(table, newWidth);
        floatWidth.set(newWidth);
        pixelWidth.set(get$5(table));
      };
      var getWidths = function (warehouse, tableSize) {
        return getPercentageWidths(warehouse, table, tableSize);
      };
      return {
        width: floatWidth.get,
        pixelWidth: pixelWidth.get,
        getWidths: getWidths,
        getCellDelta: getCellDelta,
        singleColumnWidth: singleColumnWidth,
        minCellWidth: minCellWidth,
        setElementWidth: setPercentageWidth,
        adjustTableWidth: adjustTableWidth,
        isRelative: true,
        label: 'percent'
      };
    };
    var pixelSize = function (initialWidth, table) {
      var width = Cell(initialWidth);
      var getWidth = width.get;
      var getCellDelta = identity;
      var singleColumnWidth = function (w, delta) {
        var newNext = Math.max(minWidth(), w + delta);
        return [newNext - w];
      };
      var adjustTableWidth = function (delta) {
        var newWidth = getWidth() + delta;
        setPixelWidth(table, newWidth);
        width.set(newWidth);
      };
      var getWidths = function (warehouse, tableSize) {
        return getPixelWidths(warehouse, table, tableSize);
      };
      return {
        width: getWidth,
        pixelWidth: getWidth,
        getWidths: getWidths,
        getCellDelta: getCellDelta,
        singleColumnWidth: singleColumnWidth,
        minCellWidth: minWidth,
        setElementWidth: setPixelWidth,
        adjustTableWidth: adjustTableWidth,
        isRelative: false,
        label: 'pixel'
      };
    };
    var chooseSize = function (element, width) {
      var percentMatch = percentageBasedSizeRegex().exec(width);
      if (percentMatch !== null) {
        return percentageSize(percentMatch[1], element);
      }
      var pixelMatch = pixelBasedSizeRegex().exec(width);
      if (pixelMatch !== null) {
        var intWidth = parseInt(pixelMatch[1], 10);
        return pixelSize(intWidth, element);
      }
      var fallbackWidth = get$5(element);
      return pixelSize(fallbackWidth, element);
    };
    var getTableSize = function (table) {
      var width = getRawWidth(table);
      return width.fold(function () {
        return noneSize(table);
      }, function (w) {
        return chooseSize(table, w);
      });
    };
    var TableSize = {
      getTableSize: getTableSize,
      pixelSize: pixelSize,
      percentageSize: percentageSize,
      noneSize: noneSize
    };

    var statsStruct = function (minRow, minCol, maxRow, maxCol, allCells, selectedCells) {
      return {
        minRow: minRow,
        minCol: minCol,
        maxRow: maxRow,
        maxCol: maxCol,
        allCells: allCells,
        selectedCells: selectedCells
      };
    };
    var findSelectedStats = function (house, isSelected) {
      var totalColumns = house.grid.columns;
      var totalRows = house.grid.rows;
      var minRow = totalRows;
      var minCol = totalColumns;
      var maxRow = 0;
      var maxCol = 0;
      var allCells = [];
      var selectedCells = [];
      each$1(house.access, function (detail) {
        allCells.push(detail);
        if (isSelected(detail)) {
          selectedCells.push(detail);
          var startRow = detail.row;
          var endRow = startRow + detail.rowspan - 1;
          var startCol = detail.column;
          var endCol = startCol + detail.colspan - 1;
          if (startRow < minRow) {
            minRow = startRow;
          } else if (endRow > maxRow) {
            maxRow = endRow;
          }
          if (startCol < minCol) {
            minCol = startCol;
          } else if (endCol > maxCol) {
            maxCol = endCol;
          }
        }
      });
      return statsStruct(minRow, minCol, maxRow, maxCol, allCells, selectedCells);
    };
    var makeCell = function (list, seenSelected, rowIndex) {
      var row = list[rowIndex].element;
      var td = SugarElement.fromTag('td');
      append(td, SugarElement.fromTag('br'));
      var f = seenSelected ? append : prepend;
      f(row, td);
    };
    var fillInGaps = function (list, house, stats, isSelected) {
      var totalColumns = house.grid.columns;
      var totalRows = house.grid.rows;
      for (var i = 0; i < totalRows; i++) {
        var seenSelected = false;
        for (var j = 0; j < totalColumns; j++) {
          if (!(i < stats.minRow || i > stats.maxRow || j < stats.minCol || j > stats.maxCol)) {
            var needCell = Warehouse.getAt(house, i, j).filter(isSelected).isNone();
            if (needCell) {
              makeCell(list, seenSelected, i);
            } else {
              seenSelected = true;
            }
          }
        }
      }
    };
    var clean = function (replica, stats, house, widthDelta) {
      each$1(house.columns, function (col) {
        if (col.column < stats.minCol || col.column > stats.maxCol) {
          remove$2(col.element);
        }
      });
      var emptyRows = filter(firstLayer(replica, 'tr'), function (row) {
        return row.dom.childElementCount === 0;
      });
      each(emptyRows, remove$2);
      if (stats.minCol === stats.maxCol || stats.minRow === stats.maxRow) {
        each(firstLayer(replica, 'th,td'), function (cell) {
          remove(cell, 'rowspan');
          remove(cell, 'colspan');
        });
      }
      remove(replica, LOCKED_COL_ATTR);
      remove(replica, 'data-snooker-col-series');
      var tableSize = TableSize.getTableSize(replica);
      tableSize.adjustTableWidth(widthDelta);
    };
    var getTableWidthDelta = function (table, warehouse, tableSize, stats) {
      if (stats.minCol === 0 && warehouse.grid.columns === stats.maxCol + 1) {
        return 0;
      }
      var colWidths = getPixelWidths(warehouse, table, tableSize);
      var allColsWidth = foldl(colWidths, function (acc, width) {
        return acc + width;
      }, 0);
      var selectedColsWidth = foldl(colWidths.slice(stats.minCol, stats.maxCol + 1), function (acc, width) {
        return acc + width;
      }, 0);
      var newWidth = selectedColsWidth / allColsWidth * tableSize.pixelWidth();
      var delta = newWidth - tableSize.pixelWidth();
      return tableSize.getCellDelta(delta);
    };
    var extract = function (table, selectedSelector) {
      var isSelected = function (detail) {
        return is(detail.element, selectedSelector);
      };
      var replica = deep(table);
      var list = fromTable(replica);
      var tableSize = TableSize.getTableSize(table);
      var replicaHouse = Warehouse.generate(list);
      var replicaStats = findSelectedStats(replicaHouse, isSelected);
      var selector = 'th:not(' + selectedSelector + ')' + ',td:not(' + selectedSelector + ')';
      var unselectedCells = filterFirstLayer(replica, 'th,td', function (cell) {
        return is(cell, selector);
      });
      each(unselectedCells, remove$2);
      fillInGaps(list, replicaHouse, replicaStats, isSelected);
      var house = Warehouse.fromTable(table);
      var widthDelta = getTableWidthDelta(table, house, tableSize, replicaStats);
      clean(replica, replicaStats, replicaHouse, widthDelta);
      return replica;
    };

    var nbsp = '\xA0';

    var getEnd = function (element) {
      return name(element) === 'img' ? 1 : getOption(element).fold(function () {
        return children(element).length;
      }, function (v) {
        return v.length;
      });
    };
    var isTextNodeWithCursorPosition = function (el) {
      return getOption(el).filter(function (text) {
        return text.trim().length !== 0 || text.indexOf(nbsp) > -1;
      }).isSome();
    };
    var elementsWithCursorPosition = [
      'img',
      'br'
    ];
    var isCursorPosition = function (elem) {
      var hasCursorPosition = isTextNodeWithCursorPosition(elem);
      return hasCursorPosition || contains(elementsWithCursorPosition, name(elem));
    };

    var first = function (element) {
      return descendant(element, isCursorPosition);
    };
    var last$1 = function (element) {
      return descendantRtl(element, isCursorPosition);
    };
    var descendantRtl = function (scope, predicate) {
      var descend = function (element) {
        var children$1 = children(element);
        for (var i = children$1.length - 1; i >= 0; i--) {
          var child = children$1[i];
          if (predicate(child)) {
            return Optional.some(child);
          }
          var res = descend(child);
          if (res.isSome()) {
            return res;
          }
        }
        return Optional.none();
      };
      return descend(scope);
    };

    var transferableAttributes = {
      scope: [
        'row',
        'col'
      ]
    };
    var createCell = function () {
      var td = SugarElement.fromTag('td');
      append(td, SugarElement.fromTag('br'));
      return td;
    };
    var createCol = function () {
      return SugarElement.fromTag('col');
    };
    var createColgroup = function () {
      return SugarElement.fromTag('colgroup');
    };
    var replace = function (cell, tag, attrs) {
      var replica = copy$1(cell, tag);
      each$1(attrs, function (v, k) {
        if (v === null) {
          remove(replica, k);
        } else {
          set(replica, k, v);
        }
      });
      return replica;
    };
    var pasteReplace = function (cell) {
      return cell;
    };
    var newRow = function (doc) {
      return function () {
        return SugarElement.fromTag('tr', doc.dom);
      };
    };
    var cloneFormats = function (oldCell, newCell, formats) {
      var first$1 = first(oldCell);
      return first$1.map(function (firstText) {
        var formatSelector = formats.join(',');
        var parents = ancestors$1(firstText, formatSelector, function (element) {
          return eq$1(element, oldCell);
        });
        return foldr(parents, function (last, parent) {
          var clonedFormat = shallow(parent);
          remove(clonedFormat, 'contenteditable');
          append(last, clonedFormat);
          return clonedFormat;
        }, newCell);
      }).getOr(newCell);
    };
    var cloneAppropriateAttributes = function (original, clone) {
      each$1(transferableAttributes, function (validAttributes, attributeName) {
        return getOpt(original, attributeName).filter(function (attribute) {
          return contains(validAttributes, attribute);
        }).each(function (attribute) {
          return set(clone, attributeName, attribute);
        });
      });
    };
    var cellOperations = function (mutate, doc, formatsToClone) {
      var cloneCss = function (prev, clone) {
        copy(prev.element, clone);
        remove$1(clone, 'height');
        if (prev.colspan !== 1) {
          remove$1(clone, 'width');
        }
      };
      var newCell = function (prev) {
        var docu = owner(prev.element);
        var td = SugarElement.fromTag(name(prev.element), docu.dom);
        var formats = formatsToClone.getOr([
          'strong',
          'em',
          'b',
          'i',
          'span',
          'font',
          'h1',
          'h2',
          'h3',
          'h4',
          'h5',
          'h6',
          'p',
          'div'
        ]);
        var lastNode = formats.length > 0 ? cloneFormats(prev.element, td, formats) : td;
        append(lastNode, SugarElement.fromTag('br'));
        cloneCss(prev, td);
        cloneAppropriateAttributes(prev.element, td);
        mutate(prev.element, td);
        return td;
      };
      var newCol = function (prev) {
        var doc = owner(prev.element);
        var col = SugarElement.fromTag(name(prev.element), doc.dom);
        cloneCss(prev, col);
        mutate(prev.element, col);
        return col;
      };
      return {
        col: newCol,
        colgroup: createColgroup,
        row: newRow(doc),
        cell: newCell,
        replace: replace,
        gap: createCell
      };
    };
    var paste = function (doc) {
      return {
        col: createCol,
        colgroup: createColgroup,
        row: newRow(doc),
        cell: createCell,
        replace: pasteReplace,
        gap: createCell
      };
    };

    var fromHtml$1 = function (html, scope) {
      var doc = scope || document;
      var div = doc.createElement('div');
      div.innerHTML = html;
      return children(SugarElement.fromDom(div));
    };
    var fromDom$1 = function (nodes) {
      return map(nodes, SugarElement.fromDom);
    };

    var getNodeName = function (elm) {
      return elm.nodeName.toLowerCase();
    };
    var getBody$1 = function (editor) {
      return SugarElement.fromDom(editor.getBody());
    };
    var getPixelWidth$1 = function (elm) {
      return elm.getBoundingClientRect().width;
    };
    var getPixelHeight = function (elm) {
      return elm.getBoundingClientRect().height;
    };
    var getIsRoot = function (editor) {
      return function (element) {
        return eq$1(element, getBody$1(editor));
      };
    };
    var removePxSuffix = function (size) {
      return size ? size.replace(/px$/, '') : '';
    };
    var addPxSuffix = function (size) {
      return /^\d+(\.\d+)?$/.test(size) ? size + 'px' : size;
    };
    var removeDataStyle = function (table) {
      remove(table, 'data-mce-style');
      var removeStyleAttribute = function (element) {
        return remove(element, 'data-mce-style');
      };
      each(cells(table), removeStyleAttribute);
      each(columns(table), removeStyleAttribute);
    };
    var getRawWidth$1 = function (editor, elm) {
      var raw = editor.dom.getStyle(elm, 'width') || editor.dom.getAttrib(elm, 'width');
      return Optional.from(raw).filter(isNotEmpty);
    };
    var isPercentage = function (value) {
      return /^(\d+(\.\d+)?)%$/.test(value);
    };
    var isPixel = function (value) {
      return /^(\d+(\.\d+)?)px$/.test(value);
    };
    var getSelectionStart = function (editor) {
      return SugarElement.fromDom(editor.selection.getStart());
    };

    var selection = function (selections) {
      return cata(selections.get(), constant([]), identity, pure);
    };
    var unmergable = function (selections) {
      var hasSpan = function (elem, type) {
        return getOpt(elem, type).exists(function (span) {
          return parseInt(span, 10) > 1;
        });
      };
      var hasRowOrColSpan = function (elem) {
        return hasSpan(elem, 'rowspan') || hasSpan(elem, 'colspan');
      };
      var candidates = selection(selections);
      return candidates.length > 0 && forall(candidates, hasRowOrColSpan) ? Optional.some(candidates) : Optional.none();
    };
    var mergable = function (table, selections, ephemera) {
      return cata(selections.get(), Optional.none, function (cells) {
        if (cells.length <= 1) {
          return Optional.none();
        } else {
          return retrieveBox(table, ephemera.firstSelectedSelector, ephemera.lastSelectedSelector).map(function (bounds) {
            return {
              bounds: bounds,
              cells: cells
            };
          });
        }
      }, Optional.none);
    };

    var strSelected = 'data-mce-selected';
    var strSelectedSelector = 'td[' + strSelected + '],th[' + strSelected + ']';
    var strAttributeSelector = '[' + strSelected + ']';
    var strFirstSelected = 'data-mce-first-selected';
    var strFirstSelectedSelector = 'td[' + strFirstSelected + '],th[' + strFirstSelected + ']';
    var strLastSelected = 'data-mce-last-selected';
    var strLastSelectedSelector = 'td[' + strLastSelected + '],th[' + strLastSelected + ']';
    var attributeSelector = strAttributeSelector;
    var ephemera = {
      selected: strSelected,
      selectedSelector: strSelectedSelector,
      firstSelected: strFirstSelected,
      firstSelectedSelector: strFirstSelectedSelector,
      lastSelected: strLastSelected,
      lastSelectedSelector: strLastSelectedSelector
    };

    var noMenu = function (cell) {
      return {
        element: cell,
        mergable: Optional.none(),
        unmergable: Optional.none(),
        selection: [cell]
      };
    };
    var forMenu = function (selections, table, cell) {
      return {
        element: cell,
        mergable: mergable(table, selections, ephemera),
        unmergable: unmergable(selections),
        selection: selection(selections)
      };
    };
    var paste$1 = function (element, clipboard, generators) {
      return {
        element: element,
        clipboard: clipboard,
        generators: generators
      };
    };
    var pasteRows = function (selections, cell, clipboard, generators) {
      return {
        selection: selection(selections),
        clipboard: clipboard,
        generators: generators
      };
    };

    var extractSelected = function (cells) {
      return table(cells[0]).map(function (table) {
        var replica = extract(table, attributeSelector);
        removeDataStyle(replica);
        return [replica];
      });
    };
    var serializeElements = function (editor, elements) {
      return map(elements, function (elm) {
        return editor.selection.serializer.serialize(elm.dom, {});
      }).join('');
    };
    var getTextContent = function (elements) {
      return map(elements, function (element) {
        return element.dom.innerText;
      }).join('');
    };
    var registerEvents = function (editor, selections, actions, cellSelection) {
      editor.on('BeforeGetContent', function (e) {
        var multiCellContext = function (cells) {
          e.preventDefault();
          extractSelected(cells).each(function (elements) {
            e.content = e.format === 'text' ? getTextContent(elements) : serializeElements(editor, elements);
          });
        };
        if (e.selection === true) {
          cata(selections.get(), noop, multiCellContext, noop);
        }
      });
      editor.on('BeforeSetContent', function (e) {
        if (e.selection === true && e.paste === true) {
          var cellOpt = Optional.from(editor.dom.getParent(editor.selection.getStart(), 'th,td'));
          cellOpt.each(function (domCell) {
            var cell = SugarElement.fromDom(domCell);
            table(cell).each(function (table) {
              var elements = filter(fromHtml$1(e.content), function (content) {
                return name(content) !== 'meta';
              });
              var isTable = function (elm) {
                return name(elm) === 'table';
              };
              if (elements.length === 1 && isTable(elements[0])) {
                e.preventDefault();
                var doc = SugarElement.fromDom(editor.getDoc());
                var generators = paste(doc);
                var targets = paste$1(cell, elements[0], generators);
                actions.pasteCells(table, targets).each(function (data) {
                  editor.selection.setRng(data.rng);
                  editor.focus();
                  cellSelection.clear(table);
                });
              }
            });
          });
        }
      });
    };

    var adt = Adt.generate([
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
    var ColumnContext = __assign({}, adt);

    var neighbours = function (input, index) {
      if (input.length === 0) {
        return ColumnContext.none();
      }
      if (input.length === 1) {
        return ColumnContext.only(0);
      }
      if (index === 0) {
        return ColumnContext.left(0, 1);
      }
      if (index === input.length - 1) {
        return ColumnContext.right(index - 1, index);
      }
      if (index > 0 && index < input.length - 1) {
        return ColumnContext.middle(index - 1, index, index + 1);
      }
      return ColumnContext.none();
    };
    var determine = function (input, column, step, tableSize, resize) {
      var result = input.slice(0);
      var context = neighbours(input, column);
      var onNone = constant(map(result, constant(0)));
      var onOnly = function (index) {
        return tableSize.singleColumnWidth(result[index], step);
      };
      var onLeft = function (index, next) {
        return resize.calcLeftEdgeDeltas(result, index, next, step, tableSize.minCellWidth(), tableSize.isRelative);
      };
      var onMiddle = function (prev, index, next) {
        return resize.calcMiddleDeltas(result, prev, index, next, step, tableSize.minCellWidth(), tableSize.isRelative);
      };
      var onRight = function (prev, index) {
        return resize.calcRightEdgeDeltas(result, prev, index, step, tableSize.minCellWidth(), tableSize.isRelative);
      };
      return context.fold(onNone, onOnly, onLeft, onMiddle, onRight);
    };

    var total = function (start, end, measures) {
      var r = 0;
      for (var i = start; i < end; i++) {
        r += measures[i] !== undefined ? measures[i] : 0;
      }
      return r;
    };
    var recalculateWidthForCells = function (warehouse, widths) {
      var all = Warehouse.justCells(warehouse);
      return map(all, function (cell) {
        var width = total(cell.column, cell.column + cell.colspan, widths);
        return {
          element: cell.element,
          width: width,
          colspan: cell.colspan
        };
      });
    };
    var recalculateWidthForColumns = function (warehouse, widths) {
      var groups = Warehouse.justColumns(warehouse);
      return map(groups, function (column, index) {
        return {
          element: column.element,
          width: widths[index],
          colspan: column.colspan
        };
      });
    };
    var recalculateHeightForCells = function (warehouse, heights) {
      var all = Warehouse.justCells(warehouse);
      return map(all, function (cell) {
        var height = total(cell.row, cell.row + cell.rowspan, heights);
        return {
          element: cell.element,
          height: height,
          rowspan: cell.rowspan
        };
      });
    };
    var matchRowHeight = function (warehouse, heights) {
      return map(warehouse.all, function (row, i) {
        return {
          element: row.element,
          height: heights[i]
        };
      });
    };

    var sumUp = function (newSize) {
      return foldr(newSize, function (b, a) {
        return b + a;
      }, 0);
    };
    var recalculate = function (warehouse, widths) {
      if (Warehouse.hasColumns(warehouse)) {
        return recalculateWidthForColumns(warehouse, widths);
      } else {
        return recalculateWidthForCells(warehouse, widths);
      }
    };
    var recalculateAndApply = function (warehouse, widths, tableSize) {
      var newSizes = recalculate(warehouse, widths);
      each(newSizes, function (cell) {
        tableSize.setElementWidth(cell.element, cell.width);
      });
    };
    var adjustWidth = function (table, delta, index, resizing, tableSize) {
      var warehouse = Warehouse.fromTable(table);
      var step = tableSize.getCellDelta(delta);
      var widths = tableSize.getWidths(warehouse, tableSize);
      var isLastColumn = index === warehouse.grid.columns - 1;
      var clampedStep = resizing.clampTableDelta(widths, index, step, tableSize.minCellWidth(), isLastColumn);
      var deltas = determine(widths, index, clampedStep, tableSize, resizing);
      var newWidths = map(deltas, function (dx, i) {
        return dx + widths[i];
      });
      recalculateAndApply(warehouse, newWidths, tableSize);
      resizing.resizeTable(tableSize.adjustTableWidth, clampedStep, isLastColumn);
    };
    var adjustHeight = function (table, delta, index, direction) {
      var warehouse = Warehouse.fromTable(table);
      var heights = getPixelHeights(warehouse, table, direction);
      var newHeights = map(heights, function (dy, i) {
        return index === i ? Math.max(delta + dy, minHeight()) : dy;
      });
      var newCellSizes = recalculateHeightForCells(warehouse, newHeights);
      var newRowSizes = matchRowHeight(warehouse, newHeights);
      each(newRowSizes, function (row) {
        setHeight(row.element, row.height);
      });
      each(newCellSizes, function (cell) {
        setHeight(cell.element, cell.height);
      });
      var total = sumUp(newHeights);
      setHeight(table, total);
    };
    var adjustAndRedistributeWidths = function (_table, list, details, tableSize, resizeBehaviour) {
      var warehouse = Warehouse.generate(list);
      var sizes = tableSize.getWidths(warehouse, tableSize);
      var tablePixelWidth = tableSize.pixelWidth();
      var _a = resizeBehaviour.calcRedestributedWidths(sizes, tablePixelWidth, details.pixelDelta, tableSize.isRelative), newSizes = _a.newSizes, delta = _a.delta;
      recalculateAndApply(warehouse, newSizes, tableSize);
      tableSize.adjustTableWidth(delta);
    };
    var adjustWidthTo = function (_table, list, _info, tableSize) {
      var warehouse = Warehouse.generate(list);
      var widths = tableSize.getWidths(warehouse, tableSize);
      recalculateAndApply(warehouse, widths, tableSize);
    };

    var zero = function (array) {
      return map(array, constant(0));
    };
    var surround = function (sizes, startIndex, endIndex, results, f) {
      return f(sizes.slice(0, startIndex)).concat(results).concat(f(sizes.slice(endIndex)));
    };
    var clampDeltaHelper = function (predicate) {
      return function (sizes, index, delta, minCellSize) {
        if (!predicate(delta)) {
          return delta;
        } else {
          var newSize = Math.max(minCellSize, sizes[index] - Math.abs(delta));
          var diff = Math.abs(newSize - sizes[index]);
          return delta >= 0 ? diff : -diff;
        }
      };
    };
    var clampNegativeDelta = clampDeltaHelper(function (delta) {
      return delta < 0;
    });
    var clampDelta = clampDeltaHelper(always);
    var resizeTable = function () {
      var calcFixedDeltas = function (sizes, index, next, delta, minCellSize) {
        var clampedDelta = clampNegativeDelta(sizes, index, delta, minCellSize);
        return surround(sizes, index, next + 1, [
          clampedDelta,
          0
        ], zero);
      };
      var calcRelativeDeltas = function (sizes, index, delta, minCellSize) {
        var ratio = (100 + delta) / 100;
        var newThis = Math.max(minCellSize, (sizes[index] + delta) / ratio);
        return map(sizes, function (size, idx) {
          var newSize = idx === index ? newThis : size / ratio;
          return newSize - size;
        });
      };
      var calcLeftEdgeDeltas = function (sizes, index, next, delta, minCellSize, isRelative) {
        if (isRelative) {
          return calcRelativeDeltas(sizes, index, delta, minCellSize);
        } else {
          return calcFixedDeltas(sizes, index, next, delta, minCellSize);
        }
      };
      var calcMiddleDeltas = function (sizes, _prev, index, next, delta, minCellSize, isRelative) {
        return calcLeftEdgeDeltas(sizes, index, next, delta, minCellSize, isRelative);
      };
      var resizeTable = function (resizer, delta) {
        return resizer(delta);
      };
      var calcRightEdgeDeltas = function (sizes, _prev, index, delta, minCellSize, isRelative) {
        if (isRelative) {
          return calcRelativeDeltas(sizes, index, delta, minCellSize);
        } else {
          var clampedDelta = clampNegativeDelta(sizes, index, delta, minCellSize);
          return zero(sizes.slice(0, index)).concat([clampedDelta]);
        }
      };
      var calcRedestributedWidths = function (sizes, totalWidth, pixelDelta, isRelative) {
        if (isRelative) {
          var tableWidth = totalWidth + pixelDelta;
          var ratio_1 = tableWidth / totalWidth;
          var newSizes = map(sizes, function (size) {
            return size / ratio_1;
          });
          return {
            delta: ratio_1 * 100 - 100,
            newSizes: newSizes
          };
        } else {
          return {
            delta: pixelDelta,
            newSizes: sizes
          };
        }
      };
      return {
        resizeTable: resizeTable,
        clampTableDelta: clampNegativeDelta,
        calcLeftEdgeDeltas: calcLeftEdgeDeltas,
        calcMiddleDeltas: calcMiddleDeltas,
        calcRightEdgeDeltas: calcRightEdgeDeltas,
        calcRedestributedWidths: calcRedestributedWidths
      };
    };
    var preserveTable = function () {
      var calcLeftEdgeDeltas = function (sizes, index, next, delta, minCellSize) {
        var idx = delta >= 0 ? next : index;
        var clampedDelta = clampDelta(sizes, idx, delta, minCellSize);
        return surround(sizes, index, next + 1, [
          clampedDelta,
          -clampedDelta
        ], zero);
      };
      var calcMiddleDeltas = function (sizes, _prev, index, next, delta, minCellSize) {
        return calcLeftEdgeDeltas(sizes, index, next, delta, minCellSize);
      };
      var resizeTable = function (resizer, delta, isLastColumn) {
        if (isLastColumn) {
          resizer(delta);
        }
      };
      var calcRightEdgeDeltas = function (sizes, _prev, _index, delta, _minCellSize, isRelative) {
        if (isRelative) {
          return zero(sizes);
        } else {
          var diff = delta / sizes.length;
          return map(sizes, constant(diff));
        }
      };
      var clampTableDelta = function (sizes, index, delta, minCellSize, isLastColumn) {
        if (isLastColumn) {
          if (delta >= 0) {
            return delta;
          } else {
            var maxDelta = foldl(sizes, function (a, b) {
              return a + b - minCellSize;
            }, 0);
            return Math.max(-maxDelta, delta);
          }
        } else {
          return clampNegativeDelta(sizes, index, delta, minCellSize);
        }
      };
      var calcRedestributedWidths = function (sizes, _totalWidth, _pixelDelta, _isRelative) {
        return {
          delta: 0,
          newSizes: sizes
        };
      };
      return {
        resizeTable: resizeTable,
        clampTableDelta: clampTableDelta,
        calcLeftEdgeDeltas: calcLeftEdgeDeltas,
        calcMiddleDeltas: calcMiddleDeltas,
        calcRightEdgeDeltas: calcRightEdgeDeltas,
        calcRedestributedWidths: calcRedestributedWidths
      };
    };

    var only = function (element, isResizable) {
      var parent = Optional.from(element.dom.documentElement).map(SugarElement.fromDom).getOr(element);
      return {
        parent: constant(parent),
        view: constant(element),
        origin: constant(SugarPosition(0, 0)),
        isResizable: isResizable
      };
    };
    var detached = function (editable, chrome, isResizable) {
      var origin = function () {
        return absolute(chrome);
      };
      return {
        parent: constant(chrome),
        view: constant(editable),
        origin: origin,
        isResizable: isResizable
      };
    };
    var body$1 = function (editable, chrome, isResizable) {
      return {
        parent: constant(chrome),
        view: constant(editable),
        origin: constant(SugarPosition(0, 0)),
        isResizable: isResizable
      };
    };
    var ResizeWire = {
      only: only,
      detached: detached,
      body: body$1
    };

    var adt$1 = Adt.generate([
      { invalid: ['raw'] },
      { pixels: ['value'] },
      { percent: ['value'] }
    ]);
    var validateFor = function (suffix, type, value) {
      var rawAmount = value.substring(0, value.length - suffix.length);
      var amount = parseFloat(rawAmount);
      return rawAmount === amount.toString() ? type(amount) : adt$1.invalid(value);
    };
    var from$1 = function (value) {
      if (endsWith(value, '%')) {
        return validateFor('%', adt$1.percent, value);
      }
      if (endsWith(value, 'px')) {
        return validateFor('px', adt$1.pixels, value);
      }
      return adt$1.invalid(value);
    };
    var Size = __assign(__assign({}, adt$1), { from: from$1 });

    var redistributeToPercent = function (widths, totalWidth) {
      return map(widths, function (w) {
        var colType = Size.from(w);
        return colType.fold(function () {
          return w;
        }, function (px) {
          var ratio = px / totalWidth * 100;
          return ratio + '%';
        }, function (pc) {
          return pc + '%';
        });
      });
    };
    var redistributeToPx = function (widths, totalWidth, newTotalWidth) {
      var scale = newTotalWidth / totalWidth;
      return map(widths, function (w) {
        var colType = Size.from(w);
        return colType.fold(function () {
          return w;
        }, function (px) {
          return px * scale + 'px';
        }, function (pc) {
          return pc / 100 * newTotalWidth + 'px';
        });
      });
    };
    var redistributeEmpty = function (newWidthType, columns) {
      var f = newWidthType.fold(function () {
        return constant('');
      }, function (pixels) {
        var num = pixels / columns;
        return constant(num + 'px');
      }, function () {
        var num = 100 / columns;
        return constant(num + '%');
      });
      return range(columns, f);
    };
    var redistributeValues = function (newWidthType, widths, totalWidth) {
      return newWidthType.fold(function () {
        return widths;
      }, function (px) {
        return redistributeToPx(widths, totalWidth, px);
      }, function (_pc) {
        return redistributeToPercent(widths, totalWidth);
      });
    };
    var redistribute = function (widths, totalWidth, newWidth) {
      var newType = Size.from(newWidth);
      var floats = forall(widths, function (s) {
        return s === '0px';
      }) ? redistributeEmpty(newType, widths.length) : redistributeValues(newType, widths, totalWidth);
      return normalize(floats);
    };
    var sum = function (values, fallback) {
      if (values.length === 0) {
        return fallback;
      }
      return foldr(values, function (rest, v) {
        return Size.from(v).fold(constant(0), identity, identity) + rest;
      }, 0);
    };
    var roundDown = function (num, unit) {
      var floored = Math.floor(num);
      return {
        value: floored + unit,
        remainder: num - floored
      };
    };
    var add = function (value, amount) {
      return Size.from(value).fold(constant(value), function (px) {
        return px + amount + 'px';
      }, function (pc) {
        return pc + amount + '%';
      });
    };
    var normalize = function (values) {
      if (values.length === 0) {
        return values;
      }
      var scan = foldr(values, function (rest, value) {
        var info = Size.from(value).fold(function () {
          return {
            value: value,
            remainder: 0
          };
        }, function (num) {
          return roundDown(num, 'px');
        }, function (num) {
          return {
            value: num + '%',
            remainder: 0
          };
        });
        return {
          output: [info.value].concat(rest.output),
          remainder: rest.remainder + info.remainder
        };
      }, {
        output: [],
        remainder: 0
      });
      var r = scan.output;
      return r.slice(0, r.length - 1).concat([add(r[r.length - 1], Math.round(scan.remainder))]);
    };
    var validate = Size.from;

    var redistributeToW = function (newWidths, cells, unit) {
      each(cells, function (cell) {
        var widths = newWidths.slice(cell.column, cell.colspan + cell.column);
        var w = sum(widths, minWidth());
        set$1(cell.element, 'width', w + unit);
      });
    };
    var redistributeToColumns = function (newWidths, columns, unit) {
      each(columns, function (column, index) {
        var width = sum([newWidths[index]], minWidth());
        set$1(column.element, 'width', width + unit);
      });
    };
    var redistributeToH = function (newHeights, rows, cells, unit) {
      each(cells, function (cell) {
        var heights = newHeights.slice(cell.row, cell.rowspan + cell.row);
        var h = sum(heights, minHeight());
        set$1(cell.element, 'height', h + unit);
      });
      each(rows, function (row, i) {
        set$1(row.element, 'height', newHeights[i]);
      });
    };
    var getUnit = function (newSize) {
      return validate(newSize).fold(constant('px'), constant('px'), constant('%'));
    };
    var redistribute$1 = function (table, optWidth, optHeight, tableSize) {
      var warehouse = Warehouse.fromTable(table);
      var rows = warehouse.all;
      var cells = Warehouse.justCells(warehouse);
      var columns = Warehouse.justColumns(warehouse);
      optWidth.each(function (newWidth) {
        var widthUnit = getUnit(newWidth);
        var totalWidth = get$5(table);
        var oldWidths = getRawWidths(warehouse, table, tableSize);
        var nuWidths = redistribute(oldWidths, totalWidth, newWidth);
        if (Warehouse.hasColumns(warehouse)) {
          redistributeToColumns(nuWidths, columns, widthUnit);
        } else {
          redistributeToW(nuWidths, cells, widthUnit);
        }
        set$1(table, 'width', newWidth);
      });
      optHeight.each(function (newHeight) {
        var hUnit = getUnit(newHeight);
        var totalHeight = get$6(table);
        var oldHeights = getRawHeights(warehouse, table, height);
        var nuHeights = redistribute(oldHeights, totalHeight, newHeight);
        redistributeToH(nuHeights, rows, cells, hUnit);
        set$1(table, 'height', newHeight);
      });
    };
    var isPercentSizing$1 = isPercentSizing;
    var isPixelSizing$1 = isPixelSizing;
    var isNoneSizing$1 = isNoneSizing;
    var getPercentTableWidth$1 = getPercentTableWidth;

    var getGridSize = function (table) {
      var warehouse = Warehouse.fromTable(table);
      return warehouse.grid;
    };

    var Event = function (fields) {
      var handlers = [];
      var bind = function (handler) {
        if (handler === undefined) {
          throw new Error('Event bind error: undefined handler');
        }
        handlers.push(handler);
      };
      var unbind = function (handler) {
        handlers = filter(handlers, function (h) {
          return h !== handler;
        });
      };
      var trigger = function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          args[_i] = arguments[_i];
        }
        var event = {};
        each(fields, function (name, i) {
          event[name] = args[i];
        });
        each(handlers, function (handler) {
          handler(event);
        });
      };
      return {
        bind: bind,
        unbind: unbind,
        trigger: trigger
      };
    };

    var create = function (typeDefs) {
      var registry = map$1(typeDefs, function (event) {
        return {
          bind: event.bind,
          unbind: event.unbind
        };
      });
      var trigger = map$1(typeDefs, function (event) {
        return event.trigger;
      });
      return {
        registry: registry,
        trigger: trigger
      };
    };

    var last$2 = function (fn, rate) {
      var timer = null;
      var cancel = function () {
        if (timer !== null) {
          clearTimeout(timer);
          timer = null;
        }
      };
      var throttle = function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          args[_i] = arguments[_i];
        }
        if (timer !== null) {
          clearTimeout(timer);
        }
        timer = setTimeout(function () {
          fn.apply(null, args);
          timer = null;
        }, rate);
      };
      return {
        cancel: cancel,
        throttle: throttle
      };
    };

    var sort$1 = function (arr) {
      return arr.slice(0).sort();
    };
    var reqMessage = function (required, keys) {
      throw new Error('All required keys (' + sort$1(required).join(', ') + ') were not specified. Specified keys were: ' + sort$1(keys).join(', ') + '.');
    };
    var unsuppMessage = function (unsupported) {
      throw new Error('Unsupported keys for object: ' + sort$1(unsupported).join(', '));
    };
    var validateStrArr = function (label, array) {
      if (!isArray(array)) {
        throw new Error('The ' + label + ' fields must be an array. Was: ' + array + '.');
      }
      each(array, function (a) {
        if (!isString(a)) {
          throw new Error('The value ' + a + ' in the ' + label + ' fields was not a string.');
        }
      });
    };
    var invalidTypeMessage = function (incorrect, type) {
      throw new Error('All values need to be of type: ' + type + '. Keys (' + sort$1(incorrect).join(', ') + ') were not.');
    };
    var checkDupes = function (everything) {
      var sorted = sort$1(everything);
      var dupe = find(sorted, function (s, i) {
        return i < sorted.length - 1 && s === sorted[i + 1];
      });
      dupe.each(function (d) {
        throw new Error('The field: ' + d + ' occurs more than once in the combined fields: [' + sorted.join(', ') + '].');
      });
    };

    var base = function (handleUnsupported, required) {
      return baseWith(handleUnsupported, required, {
        validate: isFunction,
        label: 'function'
      });
    };
    var baseWith = function (handleUnsupported, required, pred) {
      if (required.length === 0) {
        throw new Error('You must specify at least one required field.');
      }
      validateStrArr('required', required);
      checkDupes(required);
      return function (obj) {
        var keys$1 = keys(obj);
        var allReqd = forall(required, function (req) {
          return contains(keys$1, req);
        });
        if (!allReqd) {
          reqMessage(required, keys$1);
        }
        handleUnsupported(required, keys$1);
        var invalidKeys = filter(required, function (key) {
          return !pred.validate(obj[key], key);
        });
        if (invalidKeys.length > 0) {
          invalidTypeMessage(invalidKeys, pred.label);
        }
        return obj;
      };
    };
    var handleExact = function (required, keys) {
      var unsupported = filter(keys, function (key) {
        return !contains(required, key);
      });
      if (unsupported.length > 0) {
        unsuppMessage(unsupported);
      }
    };
    var exactly = function (required) {
      return base(handleExact, required);
    };

    var DragMode = exactly([
      'compare',
      'extract',
      'mutate',
      'sink'
    ]);
    var DragSink = exactly([
      'element',
      'start',
      'stop',
      'destroy'
    ]);
    var DragApi = exactly([
      'forceDrop',
      'drop',
      'move',
      'delayDrop'
    ]);

    var InDrag = function () {
      var previous = Optional.none();
      var reset = function () {
        previous = Optional.none();
      };
      var update = function (mode, nu) {
        var result = previous.map(function (old) {
          return mode.compare(old, nu);
        });
        previous = Optional.some(nu);
        return result;
      };
      var onEvent = function (event, mode) {
        var dataOption = mode.extract(event);
        dataOption.each(function (data) {
          var offset = update(mode, data);
          offset.each(function (d) {
            events.trigger.move(d);
          });
        });
      };
      var events = create({ move: Event(['info']) });
      return {
        onEvent: onEvent,
        reset: reset,
        events: events.registry
      };
    };

    var NoDrag = function () {
      var events = create({ move: Event(['info']) });
      return {
        onEvent: noop,
        reset: noop,
        events: events.registry
      };
    };

    var Movement = function () {
      var noDragState = NoDrag();
      var inDragState = InDrag();
      var dragState = noDragState;
      var on = function () {
        dragState.reset();
        dragState = inDragState;
      };
      var off = function () {
        dragState.reset();
        dragState = noDragState;
      };
      var onEvent = function (event, mode) {
        dragState.onEvent(event, mode);
      };
      var isOn = function () {
        return dragState === inDragState;
      };
      return {
        on: on,
        off: off,
        isOn: isOn,
        onEvent: onEvent,
        events: inDragState.events
      };
    };

    var setup = function (mutation, mode, settings) {
      var active = false;
      var events = create({
        start: Event([]),
        stop: Event([])
      });
      var movement = Movement();
      var drop = function () {
        sink.stop();
        if (movement.isOn()) {
          movement.off();
          events.trigger.stop();
        }
      };
      var throttledDrop = last$2(drop, 200);
      var go = function (parent) {
        sink.start(parent);
        movement.on();
        events.trigger.start();
      };
      var mousemove = function (event) {
        throttledDrop.cancel();
        movement.onEvent(event, mode);
      };
      movement.events.move.bind(function (event) {
        mode.mutate(mutation, event.info);
      });
      var on = function () {
        active = true;
      };
      var off = function () {
        active = false;
      };
      var runIfActive = function (f) {
        return function () {
          var args = [];
          for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
          }
          if (active) {
            f.apply(null, args);
          }
        };
      };
      var sink = mode.sink(DragApi({
        forceDrop: drop,
        drop: runIfActive(drop),
        move: runIfActive(mousemove),
        delayDrop: runIfActive(throttledDrop.throttle)
      }), settings);
      var destroy = function () {
        sink.destroy();
      };
      return {
        element: sink.element,
        go: go,
        on: on,
        off: off,
        destroy: destroy,
        events: events.registry
      };
    };

    var mkEvent = function (target, x, y, stop, prevent, kill, raw) {
      return {
        target: target,
        x: x,
        y: y,
        stop: stop,
        prevent: prevent,
        kill: kill,
        raw: raw
      };
    };
    var fromRawEvent = function (rawEvent) {
      var target = SugarElement.fromDom(getOriginalEventTarget(rawEvent).getOr(rawEvent.target));
      var stop = function () {
        return rawEvent.stopPropagation();
      };
      var prevent = function () {
        return rawEvent.preventDefault();
      };
      var kill = compose(prevent, stop);
      return mkEvent(target, rawEvent.clientX, rawEvent.clientY, stop, prevent, kill, rawEvent);
    };
    var handle = function (filter, handler) {
      return function (rawEvent) {
        if (filter(rawEvent)) {
          handler(fromRawEvent(rawEvent));
        }
      };
    };
    var binder = function (element, event, filter, handler, useCapture) {
      var wrapped = handle(filter, handler);
      element.dom.addEventListener(event, wrapped, useCapture);
      return { unbind: curry(unbind, element, event, wrapped, useCapture) };
    };
    var bind$1 = function (element, event, filter, handler) {
      return binder(element, event, filter, handler, false);
    };
    var unbind = function (element, event, handler, useCapture) {
      element.dom.removeEventListener(event, handler, useCapture);
    };

    var filter$2 = always;
    var bind$2 = function (element, event, handler) {
      return bind$1(element, event, filter$2, handler);
    };
    var fromRawEvent$1 = fromRawEvent;

    var read = function (element, attr) {
      var value = get$2(element, attr);
      return value === undefined || value === '' ? [] : value.split(' ');
    };
    var add$1 = function (element, attr, id) {
      var old = read(element, attr);
      var nu = old.concat([id]);
      set(element, attr, nu.join(' '));
      return true;
    };
    var remove$3 = function (element, attr, id) {
      var nu = filter(read(element, attr), function (v) {
        return v !== id;
      });
      if (nu.length > 0) {
        set(element, attr, nu.join(' '));
      } else {
        remove(element, attr);
      }
      return false;
    };

    var supports = function (element) {
      return element.dom.classList !== undefined;
    };
    var get$8 = function (element) {
      return read(element, 'class');
    };
    var add$2 = function (element, clazz) {
      return add$1(element, 'class', clazz);
    };
    var remove$4 = function (element, clazz) {
      return remove$3(element, 'class', clazz);
    };

    var add$3 = function (element, clazz) {
      if (supports(element)) {
        element.dom.classList.add(clazz);
      } else {
        add$2(element, clazz);
      }
    };
    var cleanClass = function (element) {
      var classList = supports(element) ? element.dom.classList : get$8(element);
      if (classList.length === 0) {
        remove(element, 'class');
      }
    };
    var remove$5 = function (element, clazz) {
      if (supports(element)) {
        var classList = element.dom.classList;
        classList.remove(clazz);
      } else {
        remove$4(element, clazz);
      }
      cleanClass(element);
    };
    var has$1 = function (element, clazz) {
      return supports(element) && element.dom.classList.contains(clazz);
    };

    var css = function (namespace) {
      var dashNamespace = namespace.replace(/\./g, '-');
      var resolve = function (str) {
        return dashNamespace + '-' + str;
      };
      return { resolve: resolve };
    };

    var styles = css('ephox-dragster');
    var resolve = styles.resolve;

    var Blocker = function (options) {
      var settings = __assign({ layerClass: resolve('blocker') }, options);
      var div = SugarElement.fromTag('div');
      set(div, 'role', 'presentation');
      setAll$1(div, {
        position: 'fixed',
        left: '0px',
        top: '0px',
        width: '100%',
        height: '100%'
      });
      add$3(div, resolve('blocker'));
      add$3(div, settings.layerClass);
      var element = function () {
        return div;
      };
      var destroy = function () {
        remove$2(div);
      };
      return {
        element: element,
        destroy: destroy
      };
    };

    var compare = function (old, nu) {
      return SugarPosition(nu.left - old.left, nu.top - old.top);
    };
    var extract$1 = function (event) {
      return Optional.some(SugarPosition(event.x, event.y));
    };
    var mutate = function (mutation, info) {
      mutation.mutate(info.left, info.top);
    };
    var sink = function (dragApi, settings) {
      var blocker = Blocker(settings);
      var mdown = bind$2(blocker.element(), 'mousedown', dragApi.forceDrop);
      var mup = bind$2(blocker.element(), 'mouseup', dragApi.drop);
      var mmove = bind$2(blocker.element(), 'mousemove', dragApi.move);
      var mout = bind$2(blocker.element(), 'mouseout', dragApi.delayDrop);
      var destroy = function () {
        blocker.destroy();
        mup.unbind();
        mmove.unbind();
        mout.unbind();
        mdown.unbind();
      };
      var start = function (parent) {
        append(parent, blocker.element());
      };
      var stop = function () {
        remove$2(blocker.element());
      };
      return DragSink({
        element: blocker.element,
        start: start,
        stop: stop,
        destroy: destroy
      });
    };
    var MouseDrag = DragMode({
      compare: compare,
      extract: extract$1,
      sink: sink,
      mutate: mutate
    });

    var transform = function (mutation, settings) {
      if (settings === void 0) {
        settings = {};
      }
      var mode = settings.mode !== undefined ? settings.mode : MouseDrag;
      return setup(mutation, mode, settings);
    };

    var isContentEditableTrue = function (elm) {
      return get$2(elm, 'contenteditable') === 'true';
    };
    var findClosestContentEditable = function (target, isRoot) {
      return closest$1(target, '[contenteditable]', isRoot);
    };

    var styles$1 = css('ephox-snooker');
    var resolve$1 = styles$1.resolve;

    var Mutation = function () {
      var events = create({
        drag: Event([
          'xDelta',
          'yDelta'
        ])
      });
      var mutate = function (x, y) {
        events.trigger.drag(x, y);
      };
      return {
        mutate: mutate,
        events: events.registry
      };
    };

    var BarMutation = function () {
      var events = create({
        drag: Event([
          'xDelta',
          'yDelta',
          'target'
        ])
      });
      var target = Optional.none();
      var delegate = Mutation();
      delegate.events.drag.bind(function (event) {
        target.each(function (t) {
          events.trigger.drag(event.xDelta, event.yDelta, t);
        });
      });
      var assign = function (t) {
        target = Optional.some(t);
      };
      var get = function () {
        return target;
      };
      return {
        assign: assign,
        get: get,
        mutate: delegate.mutate,
        events: events.registry
      };
    };

    var col = function (column, x, y, w, h) {
      var bar = SugarElement.fromTag('div');
      setAll$1(bar, {
        position: 'absolute',
        left: x - w / 2 + 'px',
        top: y + 'px',
        height: h + 'px',
        width: w + 'px'
      });
      setAll(bar, {
        'data-column': column,
        'role': 'presentation'
      });
      return bar;
    };
    var row = function (r, x, y, w, h) {
      var bar = SugarElement.fromTag('div');
      setAll$1(bar, {
        position: 'absolute',
        left: x + 'px',
        top: y - h / 2 + 'px',
        height: h + 'px',
        width: w + 'px'
      });
      setAll(bar, {
        'data-row': r,
        'role': 'presentation'
      });
      return bar;
    };

    var resizeBar = resolve$1('resizer-bar');
    var resizeRowBar = resolve$1('resizer-rows');
    var resizeColBar = resolve$1('resizer-cols');
    var BAR_THICKNESS = 7;
    var resizableRows = function (warehouse, isResizable) {
      return bind(warehouse.all, function (row, i) {
        return isResizable(row.element) ? [i] : [];
      });
    };
    var resizableColumns = function (warehouse, isResizable) {
      var resizableCols = [];
      range(warehouse.grid.columns, function (index) {
        var colElmOpt = Warehouse.getColumnAt(warehouse, index).map(function (col) {
          return col.element;
        });
        if (colElmOpt.forall(isResizable)) {
          resizableCols.push(index);
        }
      });
      return filter(resizableCols, function (colIndex) {
        var columnCells = Warehouse.filterItems(warehouse, function (cell) {
          return cell.column === colIndex;
        });
        return forall(columnCells, function (cell) {
          return isResizable(cell.element);
        });
      });
    };
    var destroy = function (wire) {
      var previous = descendants$1(wire.parent(), '.' + resizeBar);
      each(previous, remove$2);
    };
    var drawBar = function (wire, positions, create) {
      var origin = wire.origin();
      each(positions, function (cpOption) {
        cpOption.each(function (cp) {
          var bar = create(origin, cp);
          add$3(bar, resizeBar);
          append(wire.parent(), bar);
        });
      });
    };
    var refreshCol = function (wire, colPositions, position, tableHeight) {
      drawBar(wire, colPositions, function (origin, cp) {
        var colBar = col(cp.col, cp.x - origin.left, position.top - origin.top, BAR_THICKNESS, tableHeight);
        add$3(colBar, resizeColBar);
        return colBar;
      });
    };
    var refreshRow = function (wire, rowPositions, position, tableWidth) {
      drawBar(wire, rowPositions, function (origin, cp) {
        var rowBar = row(cp.row, position.left - origin.left, cp.y - origin.top, tableWidth, BAR_THICKNESS);
        add$3(rowBar, resizeRowBar);
        return rowBar;
      });
    };
    var refreshGrid = function (warhouse, wire, table, rows, cols) {
      var position = absolute(table);
      var isResizable = wire.isResizable;
      var rowPositions = rows.length > 0 ? height.positions(rows, table) : [];
      var resizableRowBars = rowPositions.length > 0 ? resizableRows(warhouse, isResizable) : [];
      var resizableRowPositions = filter(rowPositions, function (_pos, i) {
        return exists(resizableRowBars, function (barIndex) {
          return i === barIndex;
        });
      });
      refreshRow(wire, resizableRowPositions, position, getOuter(table));
      var colPositions = cols.length > 0 ? width.positions(cols, table) : [];
      var resizableColBars = colPositions.length > 0 ? resizableColumns(warhouse, isResizable) : [];
      var resizableColPositions = filter(colPositions, function (_pos, i) {
        return exists(resizableColBars, function (barIndex) {
          return i === barIndex;
        });
      });
      refreshCol(wire, resizableColPositions, position, getOuter$1(table));
    };
    var refresh = function (wire, table) {
      destroy(wire);
      if (wire.isResizable(table)) {
        var warehouse = Warehouse.fromTable(table);
        var rows = rows$1(warehouse);
        var cols = columns$1(warehouse);
        refreshGrid(warehouse, wire, table, rows, cols);
      }
    };
    var each$2 = function (wire, f) {
      var bars = descendants$1(wire.parent(), '.' + resizeBar);
      each(bars, f);
    };
    var hide = function (wire) {
      each$2(wire, function (bar) {
        set$1(bar, 'display', 'none');
      });
    };
    var show = function (wire) {
      each$2(wire, function (bar) {
        set$1(bar, 'display', 'block');
      });
    };
    var isRowBar = function (element) {
      return has$1(element, resizeRowBar);
    };
    var isColBar = function (element) {
      return has$1(element, resizeColBar);
    };

    var resizeBarDragging = resolve$1('resizer-bar-dragging');
    var BarManager = function (wire) {
      var mutation = BarMutation();
      var resizing = transform(mutation, {});
      var hoverTable = Optional.none();
      var getResizer = function (element, type) {
        return Optional.from(get$2(element, type));
      };
      mutation.events.drag.bind(function (event) {
        getResizer(event.target, 'data-row').each(function (_dataRow) {
          var currentRow = getCssValue(event.target, 'top');
          set$1(event.target, 'top', currentRow + event.yDelta + 'px');
        });
        getResizer(event.target, 'data-column').each(function (_dataCol) {
          var currentCol = getCssValue(event.target, 'left');
          set$1(event.target, 'left', currentCol + event.xDelta + 'px');
        });
      });
      var getDelta = function (target, dir) {
        var newX = getCssValue(target, dir);
        var oldX = getAttrValue(target, 'data-initial-' + dir, 0);
        return newX - oldX;
      };
      resizing.events.stop.bind(function () {
        mutation.get().each(function (target) {
          hoverTable.each(function (table) {
            getResizer(target, 'data-row').each(function (row) {
              var delta = getDelta(target, 'top');
              remove(target, 'data-initial-top');
              events.trigger.adjustHeight(table, delta, parseInt(row, 10));
            });
            getResizer(target, 'data-column').each(function (column) {
              var delta = getDelta(target, 'left');
              remove(target, 'data-initial-left');
              events.trigger.adjustWidth(table, delta, parseInt(column, 10));
            });
            refresh(wire, table);
          });
        });
      });
      var handler = function (target, dir) {
        events.trigger.startAdjust();
        mutation.assign(target);
        set(target, 'data-initial-' + dir, getCssValue(target, dir));
        add$3(target, resizeBarDragging);
        set$1(target, 'opacity', '0.2');
        resizing.go(wire.parent());
      };
      var mousedown = bind$2(wire.parent(), 'mousedown', function (event) {
        if (isRowBar(event.target)) {
          handler(event.target, 'top');
        }
        if (isColBar(event.target)) {
          handler(event.target, 'left');
        }
      });
      var isRoot = function (e) {
        return eq$1(e, wire.view());
      };
      var findClosestEditableTable = function (target) {
        return closest$1(target, 'table', isRoot).filter(function (table) {
          return findClosestContentEditable(table, isRoot).exists(isContentEditableTrue);
        });
      };
      var mouseover = bind$2(wire.view(), 'mouseover', function (event) {
        findClosestEditableTable(event.target).fold(function () {
          if (inBody(event.target)) {
            destroy(wire);
          }
        }, function (table) {
          hoverTable = Optional.some(table);
          refresh(wire, table);
        });
      });
      var destroy$1 = function () {
        mousedown.unbind();
        mouseover.unbind();
        resizing.destroy();
        destroy(wire);
      };
      var refresh$1 = function (tbl) {
        refresh(wire, tbl);
      };
      var events = create({
        adjustHeight: Event([
          'table',
          'delta',
          'row'
        ]),
        adjustWidth: Event([
          'table',
          'delta',
          'column'
        ]),
        startAdjust: Event([])
      });
      return {
        destroy: destroy$1,
        refresh: refresh$1,
        on: resizing.on,
        off: resizing.off,
        hideBars: curry(hide, wire),
        showBars: curry(show, wire),
        events: events.registry
      };
    };

    var create$1 = function (wire, resizing, lazySizing) {
      var hdirection = height;
      var vdirection = width;
      var manager = BarManager(wire);
      var events = create({
        beforeResize: Event([
          'table',
          'type'
        ]),
        afterResize: Event([
          'table',
          'type'
        ]),
        startDrag: Event([])
      });
      manager.events.adjustHeight.bind(function (event) {
        var table = event.table;
        events.trigger.beforeResize(table, 'row');
        var delta = hdirection.delta(event.delta, table);
        adjustHeight(table, delta, event.row, hdirection);
        events.trigger.afterResize(table, 'row');
      });
      manager.events.startAdjust.bind(function (_event) {
        events.trigger.startDrag();
      });
      manager.events.adjustWidth.bind(function (event) {
        var table = event.table;
        events.trigger.beforeResize(table, 'col');
        var delta = vdirection.delta(event.delta, table);
        var tableSize = lazySizing(table);
        adjustWidth(table, delta, event.column, resizing, tableSize);
        events.trigger.afterResize(table, 'col');
      });
      return {
        on: manager.on,
        off: manager.off,
        hideBars: manager.hideBars,
        showBars: manager.showBars,
        destroy: manager.destroy,
        events: events.registry
      };
    };
    var TableResize = { create: create$1 };

    var fireNewRow = function (editor, row) {
      return editor.fire('newrow', { node: row });
    };
    var fireNewCell = function (editor, cell) {
      return editor.fire('newcell', { node: cell });
    };
    var fireObjectResizeStart = function (editor, target, width, height, origin) {
      editor.fire('ObjectResizeStart', {
        target: target,
        width: width,
        height: height,
        origin: origin
      });
    };
    var fireObjectResized = function (editor, target, width, height, origin) {
      editor.fire('ObjectResized', {
        target: target,
        width: width,
        height: height,
        origin: origin
      });
    };
    var fireTableSelectionChange = function (editor, cells, start, finish, otherCells) {
      editor.fire('TableSelectionChange', {
        cells: cells,
        start: start,
        finish: finish,
        otherCells: otherCells
      });
    };
    var fireTableSelectionClear = function (editor) {
      editor.fire('TableSelectionClear');
    };
    var fireTableModified = function (editor, table, data) {
      editor.fire('TableModified', __assign(__assign({}, data), { table: table }));
    };
    var styleModified = {
      structure: false,
      style: true
    };
    var structureModified = {
      structure: true,
      style: false
    };

    var defaultTableToolbar = 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol';
    var defaultStyles = {
      'border-collapse': 'collapse',
      'width': '100%'
    };
    var determineDefaultStyles = function (editor) {
      if (isPixelsForced(editor)) {
        var editorWidth = editor.getBody().offsetWidth;
        return __assign(__assign({}, defaultStyles), { width: editorWidth + 'px' });
      } else if (isResponsiveForced(editor)) {
        return filter$1(defaultStyles, function (_value, key) {
          return key !== 'width';
        });
      } else {
        return defaultStyles;
      }
    };
    var defaultAttributes = { border: '1' };
    var defaultColumnResizingBehaviour = 'preservetable';
    var getTableSizingMode = function (editor) {
      return editor.getParam('table_sizing_mode', 'auto');
    };
    var getTableResponseWidth = function (editor) {
      return editor.getParam('table_responsive_width');
    };
    var getDefaultAttributes = function (editor) {
      return editor.getParam('table_default_attributes', defaultAttributes, 'object');
    };
    var getDefaultStyles = function (editor) {
      return editor.getParam('table_default_styles', determineDefaultStyles(editor), 'object');
    };
    var hasTableResizeBars = function (editor) {
      return editor.getParam('table_resize_bars', true, 'boolean');
    };
    var hasTabNavigation = function (editor) {
      return editor.getParam('table_tab_navigation', true, 'boolean');
    };
    var hasAdvancedCellTab = function (editor) {
      return editor.getParam('table_cell_advtab', true, 'boolean');
    };
    var hasAdvancedRowTab = function (editor) {
      return editor.getParam('table_row_advtab', true, 'boolean');
    };
    var hasAdvancedTableTab = function (editor) {
      return editor.getParam('table_advtab', true, 'boolean');
    };
    var hasAppearanceOptions = function (editor) {
      return editor.getParam('table_appearance_options', true, 'boolean');
    };
    var hasTableGrid = function (editor) {
      return editor.getParam('table_grid', true, 'boolean');
    };
    var shouldStyleWithCss = function (editor) {
      return editor.getParam('table_style_by_css', false, 'boolean');
    };
    var getCellClassList = function (editor) {
      return editor.getParam('table_cell_class_list', [], 'array');
    };
    var getRowClassList = function (editor) {
      return editor.getParam('table_row_class_list', [], 'array');
    };
    var getTableClassList = function (editor) {
      return editor.getParam('table_class_list', [], 'array');
    };
    var isPercentagesForced = function (editor) {
      return getTableSizingMode(editor) === 'relative' || getTableResponseWidth(editor) === true;
    };
    var isPixelsForced = function (editor) {
      return getTableSizingMode(editor) === 'fixed' || getTableResponseWidth(editor) === false;
    };
    var isResponsiveForced = function (editor) {
      return getTableSizingMode(editor) === 'responsive';
    };
    var getToolbar = function (editor) {
      return editor.getParam('table_toolbar', defaultTableToolbar);
    };
    var useColumnGroup = function (editor) {
      return editor.getParam('table_use_colgroups', false, 'boolean');
    };
    var getTableHeaderType = function (editor) {
      var defaultValue = 'section';
      var value = editor.getParam('table_header_type', defaultValue, 'string');
      var validValues = [
        'section',
        'cells',
        'sectionCells',
        'auto'
      ];
      if (!contains(validValues, value)) {
        return defaultValue;
      } else {
        return value;
      }
    };
    var getColumnResizingBehaviour = function (editor) {
      var validModes = [
        'preservetable',
        'resizetable'
      ];
      var givenMode = editor.getParam('table_column_resizing', defaultColumnResizingBehaviour, 'string');
      return find(validModes, function (mode) {
        return mode === givenMode;
      }).getOr(defaultColumnResizingBehaviour);
    };
    var isPreserveTableColumnResizing = function (editor) {
      return getColumnResizingBehaviour(editor) === 'preservetable';
    };
    var isResizeTableColumnResizing = function (editor) {
      return getColumnResizingBehaviour(editor) === 'resizetable';
    };
    var getCloneElements = function (editor) {
      var cloneElements = editor.getParam('table_clone_elements');
      if (isString(cloneElements)) {
        return Optional.some(cloneElements.split(/[ ,]/));
      } else if (Array.isArray(cloneElements)) {
        return Optional.some(cloneElements);
      } else {
        return Optional.none();
      }
    };
    var hasObjectResizing = function (editor) {
      var objectResizing = editor.getParam('object_resizing', true);
      return isString(objectResizing) ? objectResizing === 'table' : objectResizing;
    };

    var get$9 = function (editor, table) {
      if (isPercentagesForced(editor)) {
        var width = getRawWidth$1(editor, table.dom).filter(isPercentage).getOrThunk(function () {
          return getPercentTableWidth$1(table);
        });
        return TableSize.percentageSize(width, table);
      } else if (isPixelsForced(editor)) {
        return TableSize.pixelSize(get$5(table), table);
      } else {
        return TableSize.getTableSize(table);
      }
    };

    var cleanupLegacyAttributes = function (element) {
      remove(element, 'width');
    };
    var convertToPercentSize = function (table, tableSize) {
      var newWidth = getPercentTableWidth(table);
      redistribute$1(table, Optional.some(newWidth), Optional.none(), tableSize);
      cleanupLegacyAttributes(table);
    };
    var convertToPixelSize = function (table, tableSize) {
      var newWidth = getPixelTableWidth(table);
      redistribute$1(table, Optional.some(newWidth), Optional.none(), tableSize);
      cleanupLegacyAttributes(table);
    };
    var convertToNoneSize = function (table) {
      remove$1(table, 'width');
      var columns$1 = columns(table);
      var rowElements = columns$1.length > 0 ? columns$1 : cells(table);
      each(rowElements, function (cell) {
        remove$1(cell, 'width');
        cleanupLegacyAttributes(cell);
      });
      cleanupLegacyAttributes(table);
    };

    var enforcePercentage = function (editor, table) {
      var tableSizing = get$9(editor, table);
      convertToPercentSize(table, tableSizing);
    };
    var enforcePixels = function (editor, table) {
      var tableSizing = get$9(editor, table);
      convertToPixelSize(table, tableSizing);
    };
    var enforceNone = convertToNoneSize;
    var syncPixels = function (table) {
      var warehouse = Warehouse.fromTable(table);
      if (!Warehouse.hasColumns(warehouse)) {
        each(cells(table), function (cell) {
          var computedWidth = get$3(cell, 'width');
          set$1(cell, 'width', computedWidth);
          remove(cell, 'width');
        });
      }
    };

    var createContainer = function () {
      var container = SugarElement.fromTag('div');
      setAll$1(container, {
        position: 'static',
        height: '0',
        width: '0',
        padding: '0',
        margin: '0',
        border: '0'
      });
      append(body(), container);
      return container;
    };
    var get$a = function (editor, isResizable) {
      return editor.inline ? ResizeWire.body(getBody$1(editor), createContainer(), isResizable) : ResizeWire.only(SugarElement.fromDom(editor.getDoc()), isResizable);
    };
    var remove$6 = function (editor, wire) {
      if (editor.inline) {
        remove$2(wire.parent());
      }
    };

    var barResizerPrefix = 'bar-';
    var isResizable = function (elm) {
      return get$2(elm, 'data-mce-resize') !== 'false';
    };
    var getResizeHandler = function (editor) {
      var selectionRng = Optional.none();
      var resize = Optional.none();
      var wire = Optional.none();
      var startW;
      var startRawW;
      var isTable = function (elm) {
        return elm.nodeName === 'TABLE';
      };
      var lazyResize = function () {
        return resize;
      };
      var lazyWire = function () {
        return wire.getOr(ResizeWire.only(SugarElement.fromDom(editor.getBody()), isResizable));
      };
      var lazySizing = function (table) {
        return get$9(editor, table);
      };
      var lazyResizingBehaviour = function () {
        return isPreserveTableColumnResizing(editor) ? preserveTable() : resizeTable();
      };
      var getNumColumns = function (table) {
        return getGridSize(table).columns;
      };
      var afterCornerResize = function (table, origin, width) {
        var isRightEdgeResize = endsWith(origin, 'e');
        if (startRawW === '') {
          enforcePercentage(editor, table);
        }
        if (width !== startW && startRawW !== '') {
          set$1(table, 'width', startRawW);
          var resizing = lazyResizingBehaviour();
          var tableSize = lazySizing(table);
          var col = isPreserveTableColumnResizing(editor) || isRightEdgeResize ? getNumColumns(table) - 1 : 0;
          adjustWidth(table, width - startW, col, resizing, tableSize);
        } else if (isPercentage(startRawW)) {
          var percentW = parseFloat(startRawW.replace('%', ''));
          var targetPercentW = width * percentW / startW;
          set$1(table, 'width', targetPercentW + '%');
        }
        if (isPixel(startRawW)) {
          syncPixels(table);
        }
      };
      var destroy = function () {
        resize.each(function (sz) {
          sz.destroy();
        });
        wire.each(function (w) {
          remove$6(editor, w);
        });
      };
      editor.on('init', function () {
        var rawWire = get$a(editor, isResizable);
        wire = Optional.some(rawWire);
        if (hasObjectResizing(editor) && hasTableResizeBars(editor)) {
          var resizing = lazyResizingBehaviour();
          var sz = TableResize.create(rawWire, resizing, lazySizing);
          sz.on();
          sz.events.startDrag.bind(function (_event) {
            selectionRng = Optional.some(editor.selection.getRng());
          });
          sz.events.beforeResize.bind(function (event) {
            var rawTable = event.table.dom;
            fireObjectResizeStart(editor, rawTable, getPixelWidth$1(rawTable), getPixelHeight(rawTable), barResizerPrefix + event.type);
          });
          sz.events.afterResize.bind(function (event) {
            var table = event.table;
            var rawTable = table.dom;
            removeDataStyle(table);
            selectionRng.each(function (rng) {
              editor.selection.setRng(rng);
              editor.focus();
            });
            fireObjectResized(editor, rawTable, getPixelWidth$1(rawTable), getPixelHeight(rawTable), barResizerPrefix + event.type);
            editor.undoManager.add();
          });
          resize = Optional.some(sz);
        }
      });
      editor.on('ObjectResizeStart', function (e) {
        var targetElm = e.target;
        if (isTable(targetElm)) {
          var table = SugarElement.fromDom(targetElm);
          each(editor.dom.select('.mce-clonedresizable'), function (clone) {
            editor.dom.addClass(clone, 'mce-' + getColumnResizingBehaviour(editor) + '-columns');
          });
          if (!isPixelSizing$1(table) && isPixelsForced(editor)) {
            enforcePixels(editor, table);
          } else if (!isPercentSizing$1(table) && isPercentagesForced(editor)) {
            enforcePercentage(editor, table);
          }
          if (isNoneSizing$1(table) && startsWith(e.origin, barResizerPrefix)) {
            enforcePercentage(editor, table);
          }
          startW = e.width;
          startRawW = isResponsiveForced(editor) ? '' : getRawWidth$1(editor, targetElm).getOr('');
        }
      });
      editor.on('ObjectResized', function (e) {
        var targetElm = e.target;
        if (isTable(targetElm)) {
          var table = SugarElement.fromDom(targetElm);
          var origin_1 = e.origin;
          if (startsWith(origin_1, 'corner-')) {
            afterCornerResize(table, origin_1, e.width);
          }
          removeDataStyle(table);
          fireTableModified(editor, table.dom, styleModified);
        }
      });
      editor.on('SwitchMode', function () {
        lazyResize().each(function (resize) {
          if (editor.mode.isReadOnly()) {
            resize.hideBars();
          } else {
            resize.showBars();
          }
        });
      });
      return {
        lazyResize: lazyResize,
        lazyWire: lazyWire,
        destroy: destroy
      };
    };

    var point = function (element, offset) {
      return {
        element: element,
        offset: offset
      };
    };

    var scan = function (universe, element, direction) {
      if (universe.property().isText(element) && universe.property().getText(element).trim().length === 0 || universe.property().isComment(element)) {
        return direction(element).bind(function (elem) {
          return scan(universe, elem, direction).orThunk(function () {
            return Optional.some(elem);
          });
        });
      } else {
        return Optional.none();
      }
    };
    var toEnd = function (universe, element) {
      if (universe.property().isText(element)) {
        return universe.property().getText(element).length;
      }
      var children = universe.property().children(element);
      return children.length;
    };
    var freefallRtl = function (universe, element) {
      var candidate = scan(universe, element, universe.query().prevSibling).getOr(element);
      if (universe.property().isText(candidate)) {
        return point(candidate, toEnd(universe, candidate));
      }
      var children = universe.property().children(candidate);
      return children.length > 0 ? freefallRtl(universe, children[children.length - 1]) : point(candidate, toEnd(universe, candidate));
    };

    var freefallRtl$1 = freefallRtl;

    var universe$1 = DomUniverse();
    var freefallRtl$2 = function (element) {
      return freefallRtl$1(universe$1, element);
    };

    var halve = function (main, other) {
      var colspan = getSpan(main, 'colspan');
      if (colspan === 1) {
        var width = getGenericWidth(main);
        width.each(function (w) {
          var newWidth = w.value / 2;
          setGenericWidth(main, newWidth, w.unit);
          setGenericWidth(other, newWidth, w.unit);
        });
      }
    };

    var setIfNot = function (element, property, value, ignore) {
      if (value === ignore) {
        remove(element, property);
      } else {
        set(element, property, value);
      }
    };
    var insert = function (table, selector, element) {
      last(children$2(table, selector)).fold(function () {
        return prepend(table, element);
      }, function (child) {
        return after(child, element);
      });
    };
    var generateSection = function (table, sectionName) {
      var section = child$2(table, sectionName).getOrThunk(function () {
        var newSection = SugarElement.fromTag(sectionName, owner(table).dom);
        if (sectionName === 'thead') {
          insert(table, 'caption,colgroup', newSection);
        } else if (sectionName === 'colgroup') {
          insert(table, 'caption', newSection);
        } else {
          append(table, newSection);
        }
        return newSection;
      });
      empty(section);
      return section;
    };
    var render = function (table, grid) {
      var newRows = [];
      var newCells = [];
      var syncRows = function (gridSection) {
        return map(gridSection, function (row) {
          if (row.isNew) {
            newRows.push(row.element);
          }
          var tr = row.element;
          empty(tr);
          each(row.cells, function (cell) {
            if (cell.isNew) {
              newCells.push(cell.element);
            }
            setIfNot(cell.element, 'colspan', cell.colspan, 1);
            setIfNot(cell.element, 'rowspan', cell.rowspan, 1);
            append(tr, cell.element);
          });
          return tr;
        });
      };
      var syncColGroup = function (gridSection) {
        return bind(gridSection, function (colGroup) {
          return map(colGroup.cells, function (col) {
            setIfNot(col.element, 'span', col.colspan, 1);
            return col.element;
          });
        });
      };
      var renderSection = function (gridSection, sectionName) {
        var section = generateSection(table, sectionName);
        var sync = sectionName === 'colgroup' ? syncColGroup : syncRows;
        var sectionElems = sync(gridSection);
        append$1(section, sectionElems);
      };
      var removeSection = function (sectionName) {
        child$2(table, sectionName).each(remove$2);
      };
      var renderOrRemoveSection = function (gridSection, sectionName) {
        if (gridSection.length > 0) {
          renderSection(gridSection, sectionName);
        } else {
          removeSection(sectionName);
        }
      };
      var headSection = [];
      var bodySection = [];
      var footSection = [];
      var columnGroupsSection = [];
      each(grid, function (row) {
        switch (row.section) {
        case 'thead':
          headSection.push(row);
          break;
        case 'tbody':
          bodySection.push(row);
          break;
        case 'tfoot':
          footSection.push(row);
          break;
        case 'colgroup':
          columnGroupsSection.push(row);
          break;
        }
      });
      renderOrRemoveSection(columnGroupsSection, 'colgroup');
      renderOrRemoveSection(headSection, 'thead');
      renderOrRemoveSection(bodySection, 'tbody');
      renderOrRemoveSection(footSection, 'tfoot');
      return {
        newRows: newRows,
        newCells: newCells
      };
    };
    var copy$2 = function (grid) {
      return map(grid, function (row) {
        var tr = shallow(row.element);
        each(row.cells, function (cell) {
          var clonedCell = deep(cell.element);
          setIfNot(clonedCell, 'colspan', cell.colspan, 1);
          setIfNot(clonedCell, 'rowspan', cell.rowspan, 1);
          append(tr, clonedCell);
        });
        return tr;
      });
    };

    var getColumn = function (grid, index) {
      return map(grid, function (row) {
        return getCell(row, index);
      });
    };
    var getRow = function (grid, index) {
      return grid[index];
    };
    var findDiff = function (xs, comp) {
      if (xs.length === 0) {
        return 0;
      }
      var first = xs[0];
      var index = findIndex(xs, function (x) {
        return !comp(first.element, x.element);
      });
      return index.fold(function () {
        return xs.length;
      }, function (ind) {
        return ind;
      });
    };
    var subgrid = function (grid, row, column, comparator) {
      var restOfRow = getRow(grid, row).cells.slice(column);
      var endColIndex = findDiff(restOfRow, comparator);
      var restOfColumn = getColumn(grid, column).slice(row);
      var endRowIndex = findDiff(restOfColumn, comparator);
      return {
        colspan: endColIndex,
        rowspan: endRowIndex
      };
    };

    var toDetails = function (grid, comparator) {
      var seen = map(grid, function (row) {
        return map(row.cells, never);
      });
      var updateSeen = function (rowIndex, columnIndex, rowspan, colspan) {
        for (var row = rowIndex; row < rowIndex + rowspan; row++) {
          for (var column = columnIndex; column < columnIndex + colspan; column++) {
            seen[row][column] = true;
          }
        }
      };
      return map(grid, function (row, rowIndex) {
        var details = bind(row.cells, function (cell, columnIndex) {
          if (seen[rowIndex][columnIndex] === false) {
            var result = subgrid(grid, rowIndex, columnIndex, comparator);
            updateSeen(rowIndex, columnIndex, result.rowspan, result.colspan);
            return [detailnew(cell.element, result.rowspan, result.colspan, cell.isNew)];
          } else {
            return [];
          }
        });
        return rowdetails(details, row.section);
      });
    };
    var toGrid = function (warehouse, generators, isNew) {
      var grid = [];
      if (Warehouse.hasColumns(warehouse)) {
        var groupElementNew = map(Warehouse.justColumns(warehouse), function (column) {
          return elementnew(column.element, isNew, false);
        });
        grid.push(rowcells(groupElementNew, 'colgroup'));
      }
      for (var rowIndex = 0; rowIndex < warehouse.grid.rows; rowIndex++) {
        var rowCells = [];
        for (var columnIndex = 0; columnIndex < warehouse.grid.columns; columnIndex++) {
          var element = Warehouse.getAt(warehouse, rowIndex, columnIndex).map(function (item) {
            return elementnew(item.element, isNew, item.isLocked);
          }).getOrThunk(function () {
            return elementnew(generators.gap(), true, false);
          });
          rowCells.push(element);
        }
        var row = rowcells(rowCells, warehouse.all[rowIndex].section);
        grid.push(row);
      }
      return grid;
    };

    var fromWarehouse = function (warehouse, generators) {
      return toGrid(warehouse, generators, false);
    };
    var deriveRows = function (rendered, generators) {
      var findRow = function (details) {
        var rowOfCells = findMap(details, function (detail) {
          return parent(detail.element).map(function (row) {
            var isNew = parent(row).isNone();
            return elementnew(row, isNew, false);
          });
        });
        return rowOfCells.getOrThunk(function () {
          return elementnew(generators.row(), true, false);
        });
      };
      return map(rendered, function (details) {
        var row = findRow(details.details);
        return rowdatanew(row.element, details.details, details.section, row.isNew);
      });
    };
    var toDetailList = function (grid, generators) {
      var rendered = toDetails(grid, eq$1);
      return deriveRows(rendered, generators);
    };
    var findInWarehouse = function (warehouse, element) {
      return findMap(warehouse.all, function (r) {
        return find(r.cells, function (e) {
          return eq$1(element, e.element);
        });
      });
    };
    var extractCells = function (warehouse, target, predicate) {
      var details = map(target.selection, function (cell$1) {
        return cell(cell$1).bind(function (lc) {
          return findInWarehouse(warehouse, lc);
        }).filter(predicate);
      });
      var cells = cat(details);
      return someIf(cells.length > 0, cells);
    };
    var run = function (operation, extract, adjustment, postAction, genWrappers) {
      return function (wire, table, target, generators, sizing, resizeBehaviour) {
        var warehouse = Warehouse.fromTable(table);
        var output = extract(warehouse, target).map(function (info) {
          var model = fromWarehouse(warehouse, generators);
          var result = operation(model, info, eq$1, genWrappers(generators));
          var lockedColumns = getLockedColumnsFromGrid(result.grid);
          var grid = toDetailList(result.grid, generators);
          return {
            info: info,
            grid: grid,
            cursor: result.cursor,
            lockedColumns: lockedColumns
          };
        });
        return output.bind(function (out) {
          var newElements = render(table, out.grid);
          var tableSizing = Optional.from(sizing).getOrThunk(function () {
            return TableSize.getTableSize(table);
          });
          var resizing = Optional.from(resizeBehaviour).getOrThunk(preserveTable);
          adjustment(table, out.grid, out.info, tableSizing, resizing);
          postAction(table);
          refresh(wire, table);
          remove(table, LOCKED_COL_ATTR);
          if (out.lockedColumns.length > 0) {
            set(table, LOCKED_COL_ATTR, out.lockedColumns.join(','));
          }
          return Optional.some({
            cursor: out.cursor,
            newRows: newElements.newRows,
            newCells: newElements.newCells
          });
        });
      };
    };
    var onCell = function (warehouse, target) {
      return cell(target.element).bind(function (cell) {
        return findInWarehouse(warehouse, cell);
      });
    };
    var onPaste = function (warehouse, target) {
      return cell(target.element).bind(function (cell) {
        return findInWarehouse(warehouse, cell).map(function (details) {
          var value = __assign(__assign({}, details), {
            generators: target.generators,
            clipboard: target.clipboard
          });
          return value;
        });
      });
    };
    var onPasteByEditor = function (warehouse, target) {
      return extractCells(warehouse, target, always).map(function (cells) {
        return {
          cells: cells,
          generators: target.generators,
          clipboard: target.clipboard
        };
      });
    };
    var onMergable = function (_warehouse, target) {
      return target.mergable;
    };
    var onUnmergable = function (_warehouse, target) {
      return target.unmergable;
    };
    var onCells = function (warehouse, target) {
      return extractCells(warehouse, target, always);
    };
    var onUnlockedCell = function (warehouse, target) {
      return onCell(warehouse, target).filter(function (detail) {
        return !detail.isLocked;
      });
    };
    var onUnlockedCells = function (warehouse, target) {
      return extractCells(warehouse, target, function (detail) {
        return !detail.isLocked;
      });
    };
    var isUnlockedTableCell = function (warehouse, cell) {
      return findInWarehouse(warehouse, cell).exists(function (detail) {
        return !detail.isLocked;
      });
    };
    var allUnlocked = function (warehouse, cells) {
      return forall(cells, function (cell) {
        return isUnlockedTableCell(warehouse, cell);
      });
    };
    var onUnlockedMergable = function (warehouse, target) {
      return onMergable(warehouse, target).filter(function (mergeable) {
        return allUnlocked(warehouse, mergeable.cells);
      });
    };
    var onUnlockedUnmergable = function (warehouse, target) {
      return onUnmergable(warehouse, target).filter(function (cells) {
        return allUnlocked(warehouse, cells);
      });
    };

    var merge = function (grid, bounds, comparator, substitution) {
      var rows = extractGridDetails(grid).rows;
      if (rows.length === 0) {
        return grid;
      }
      for (var i = bounds.startRow; i <= bounds.finishRow; i++) {
        for (var j = bounds.startCol; j <= bounds.finishCol; j++) {
          var row = rows[i];
          var isLocked = getCell(row, j).isLocked;
          mutateCell(row, j, elementnew(substitution(), false, isLocked));
        }
      }
      return grid;
    };
    var unmerge = function (grid, target, comparator, substitution) {
      var rows = extractGridDetails(grid).rows;
      var first = true;
      for (var i = 0; i < rows.length; i++) {
        for (var j = 0; j < cellLength(rows[0]); j++) {
          var row = rows[i];
          var currentCell = getCell(row, j);
          var currentCellElm = currentCell.element;
          var isToReplace = comparator(currentCellElm, target);
          if (isToReplace === true && first === false) {
            mutateCell(row, j, elementnew(substitution(), true, currentCell.isLocked));
          } else if (isToReplace === true) {
            first = false;
          }
        }
      }
      return grid;
    };
    var uniqueCells = function (row, comparator) {
      return foldl(row, function (rest, cell) {
        return exists(rest, function (currentCell) {
          return comparator(currentCell.element, cell.element);
        }) ? rest : rest.concat([cell]);
      }, []);
    };
    var splitCols = function (grid, index, comparator, substitution) {
      if (index > 0 && index < grid[0].cells.length) {
        each(grid, function (row) {
          var prevCell = row.cells[index - 1];
          var current = row.cells[index];
          var isToReplace = comparator(current.element, prevCell.element);
          if (isToReplace) {
            mutateCell(row, index, elementnew(substitution(), true, current.isLocked));
          }
        });
      }
      return grid;
    };
    var splitRows = function (grid, index, comparator, substitution) {
      var rows = extractGridDetails(grid).rows;
      if (index > 0 && index < rows.length) {
        var rowPrevCells = rows[index - 1].cells;
        var cells = uniqueCells(rowPrevCells, comparator);
        each(cells, function (cell) {
          var replacement = Optional.none();
          for (var i = index; i < rows.length; i++) {
            var _loop_1 = function (j) {
              var row = rows[i];
              var current = getCell(row, j);
              var isToReplace = comparator(current.element, cell.element);
              if (isToReplace) {
                if (replacement.isNone()) {
                  replacement = Optional.some(substitution());
                }
                replacement.each(function (sub) {
                  mutateCell(row, j, elementnew(sub, true, current.isLocked));
                });
              }
            };
            for (var j = 0; j < cellLength(rows[0]); j++) {
              _loop_1(j);
            }
          }
        });
      }
      return grid;
    };

    var value = function (o) {
      var is = function (v) {
        return o === v;
      };
      var or = function (_opt) {
        return value(o);
      };
      var orThunk = function (_f) {
        return value(o);
      };
      var map = function (f) {
        return value(f(o));
      };
      var mapError = function (_f) {
        return value(o);
      };
      var each = function (f) {
        f(o);
      };
      var bind = function (f) {
        return f(o);
      };
      var fold = function (_, onValue) {
        return onValue(o);
      };
      var exists = function (f) {
        return f(o);
      };
      var forall = function (f) {
        return f(o);
      };
      var toOptional = function () {
        return Optional.some(o);
      };
      return {
        is: is,
        isValue: always,
        isError: never,
        getOr: constant(o),
        getOrThunk: constant(o),
        getOrDie: constant(o),
        or: or,
        orThunk: orThunk,
        fold: fold,
        map: map,
        mapError: mapError,
        each: each,
        bind: bind,
        exists: exists,
        forall: forall,
        toOptional: toOptional
      };
    };
    var error = function (message) {
      var getOrThunk = function (f) {
        return f();
      };
      var getOrDie = function () {
        return die(String(message))();
      };
      var or = function (opt) {
        return opt;
      };
      var orThunk = function (f) {
        return f();
      };
      var map = function (_f) {
        return error(message);
      };
      var mapError = function (f) {
        return error(f(message));
      };
      var bind = function (_f) {
        return error(message);
      };
      var fold = function (onError, _) {
        return onError(message);
      };
      return {
        is: never,
        isValue: never,
        isError: always,
        getOr: identity,
        getOrThunk: getOrThunk,
        getOrDie: getOrDie,
        or: or,
        orThunk: orThunk,
        fold: fold,
        map: map,
        mapError: mapError,
        each: noop,
        bind: bind,
        exists: never,
        forall: always,
        toOptional: Optional.none
      };
    };
    var fromOption = function (opt, err) {
      return opt.fold(function () {
        return error(err);
      }, value);
    };
    var Result = {
      value: value,
      error: error,
      fromOption: fromOption
    };

    var measure = function (startAddress, gridA, gridB) {
      if (startAddress.row >= gridA.length || startAddress.column > cellLength(gridA[0])) {
        return Result.error('invalid start address out of table bounds, row: ' + startAddress.row + ', column: ' + startAddress.column);
      }
      var rowRemainder = gridA.slice(startAddress.row);
      var colRemainder = rowRemainder[0].cells.slice(startAddress.column);
      var colRequired = cellLength(gridB[0]);
      var rowRequired = gridB.length;
      return Result.value({
        rowDelta: rowRemainder.length - rowRequired,
        colDelta: colRemainder.length - colRequired
      });
    };
    var measureWidth = function (gridA, gridB) {
      var colLengthA = cellLength(gridA[0]);
      var colLengthB = cellLength(gridB[0]);
      return {
        rowDelta: 0,
        colDelta: colLengthA - colLengthB
      };
    };
    var measureHeight = function (gridA, gridB) {
      var rowLengthA = gridA.length;
      var rowLengthB = gridB.length;
      return {
        rowDelta: rowLengthA - rowLengthB,
        colDelta: 0
      };
    };
    var generateElements = function (amount, row, generators, isLocked) {
      var generator = row.section === 'colgroup' ? generators.col : generators.cell;
      return range(amount, function (idx) {
        return elementnew(generator(), true, isLocked(idx));
      });
    };
    var rowFill = function (grid, amount, generators, lockedColumns) {
      return grid.concat(range(amount, function () {
        var row = grid[grid.length - 1];
        var elements = generateElements(row.cells.length, row, generators, function (idx) {
          return has(lockedColumns, idx.toString());
        });
        return setCells(row, elements);
      }));
    };
    var colFill = function (grid, amount, generators, startIndex) {
      return map(grid, function (row) {
        var newChildren = generateElements(amount, row, generators, never);
        return addCells(row, startIndex, newChildren);
      });
    };
    var lockedColFill = function (grid, generators, lockedColumns) {
      return map(grid, function (row) {
        return foldl(lockedColumns, function (acc, colNum) {
          var newChild = generateElements(1, row, generators, always)[0];
          return addCell(acc, colNum, newChild);
        }, row);
      });
    };
    var tailor = function (gridA, delta, generators) {
      var fillCols = delta.colDelta < 0 ? colFill : identity;
      var fillRows = delta.rowDelta < 0 ? rowFill : identity;
      var lockedColumns = getLockedColumnsFromGrid(gridA);
      var gridWidth = cellLength(gridA[0]);
      var isLastColLocked = exists(lockedColumns, function (locked) {
        return locked === gridWidth - 1;
      });
      var modifiedCols = fillCols(gridA, Math.abs(delta.colDelta), generators, isLastColLocked ? gridWidth - 1 : gridWidth);
      var newLockedColumns = getLockedColumnsFromGrid(modifiedCols);
      return fillRows(modifiedCols, Math.abs(delta.rowDelta), generators, mapToObject(newLockedColumns, always));
    };

    var isSpanning = function (grid, row, col, comparator) {
      var candidate = getCell(grid[row], col);
      var matching = curry(comparator, candidate.element);
      var currentRow = grid[row];
      return grid.length > 1 && cellLength(currentRow) > 1 && (col > 0 && matching(getCellElement(currentRow, col - 1)) || col < currentRow.cells.length - 1 && matching(getCellElement(currentRow, col + 1)) || row > 0 && matching(getCellElement(grid[row - 1], col)) || row < grid.length - 1 && matching(getCellElement(grid[row + 1], col)));
    };
    var mergeTables = function (startAddress, gridA, gridB, generator, comparator, lockedColumns) {
      var startRow = startAddress.row;
      var startCol = startAddress.column;
      var mergeHeight = gridB.length;
      var mergeWidth = cellLength(gridB[0]);
      var endRow = startRow + mergeHeight;
      var endCol = startCol + mergeWidth + lockedColumns.length;
      var lockedColumnObj = mapToObject(lockedColumns, always);
      for (var r = startRow; r < endRow; r++) {
        var skippedCol = 0;
        for (var c = startCol; c < endCol; c++) {
          if (lockedColumnObj[c]) {
            skippedCol++;
            continue;
          }
          if (isSpanning(gridA, r, c, comparator)) {
            unmerge(gridA, getCellElement(gridA[r], c), comparator, generator.cell);
          }
          var gridBColIndex = c - startCol - skippedCol;
          var newCell = getCell(gridB[r - startRow], gridBColIndex);
          var newCellElm = newCell.element;
          var replacement = generator.replace(newCellElm);
          mutateCell(gridA[r], c, elementnew(replacement, true, newCell.isLocked));
        }
      }
      return gridA;
    };
    var getValidStartAddress = function (currentStartAddress, grid, lockedColumns) {
      var gridColLength = cellLength(grid[0]);
      var possibleColAddresses = range(gridColLength - currentStartAddress.column, function (num) {
        return num + currentStartAddress.column;
      });
      var validColAddress = find(possibleColAddresses, function (num) {
        return forall(lockedColumns, function (col) {
          return col !== num;
        });
      }).getOr(gridColLength - 1);
      return __assign(__assign({}, currentStartAddress), { column: validColAddress });
    };
    var getLockedColumnsWithinBounds = function (startAddress, grid, lockedColumns) {
      return filter(lockedColumns, function (colNum) {
        return colNum >= startAddress.column && colNum <= cellLength(grid[0]) + startAddress.column;
      });
    };
    var merge$1 = function (startAddress, gridA, gridB, generator, comparator) {
      var lockedColumns = getLockedColumnsFromGrid(gridA);
      var validStartAddress = getValidStartAddress(startAddress, gridA, lockedColumns);
      var lockedColumnsWithinBounds = getLockedColumnsWithinBounds(validStartAddress, gridB, lockedColumns);
      var result = measure(validStartAddress, gridA, gridB);
      return result.map(function (diff) {
        var delta = __assign(__assign({}, diff), { colDelta: diff.colDelta - lockedColumnsWithinBounds.length });
        var fittedGrid = tailor(gridA, delta, generator);
        var newLockedColumns = getLockedColumnsFromGrid(fittedGrid);
        var newLockedColumnsWithinBounds = getLockedColumnsWithinBounds(validStartAddress, gridB, newLockedColumns);
        return mergeTables(validStartAddress, fittedGrid, gridB, generator, comparator, newLockedColumnsWithinBounds);
      });
    };
    var insertCols = function (index, gridA, gridB, generator, comparator) {
      splitCols(gridA, index, comparator, generator.cell);
      var delta = measureHeight(gridB, gridA);
      var fittedNewGrid = tailor(gridB, delta, generator);
      var secondDelta = measureHeight(gridA, fittedNewGrid);
      var fittedOldGrid = tailor(gridA, secondDelta, generator);
      return map(fittedOldGrid, function (gridRow, i) {
        return addCells(gridRow, index, fittedNewGrid[i].cells);
      });
    };
    var insertRows = function (index, gridA, gridB, generator, comparator) {
      splitRows(gridA, index, comparator, generator.cell);
      var locked = getLockedColumnsFromGrid(gridA);
      var diff = measureWidth(gridA, gridB);
      var delta = __assign(__assign({}, diff), { colDelta: diff.colDelta - locked.length });
      var fittedOldGrid = tailor(gridA, delta, generator);
      var _a = extractGridDetails(fittedOldGrid), oldCols = _a.cols, oldRows = _a.rows;
      var newLocked = getLockedColumnsFromGrid(fittedOldGrid);
      var secondDiff = measureWidth(gridB, gridA);
      var secondDelta = __assign(__assign({}, secondDiff), { colDelta: secondDiff.colDelta + newLocked.length });
      var fittedGridB = lockedColFill(gridB, generator, newLocked);
      var fittedNewGrid = tailor(fittedGridB, secondDelta, generator);
      return oldCols.concat(oldRows.slice(0, index)).concat(fittedNewGrid).concat(oldRows.slice(index, oldRows.length));
    };

    var insertRowAt = function (grid, index, example, comparator, substitution) {
      var _a = extractGridDetails(grid), rows = _a.rows, cols = _a.cols;
      var before = rows.slice(0, index);
      var after = rows.slice(index);
      var between = mapCells(rows[example], function (ex, c) {
        var withinSpan = index > 0 && index < rows.length && comparator(getCellElement(rows[index - 1], c), getCellElement(rows[index], c));
        var ret = withinSpan ? getCell(rows[index], c) : elementnew(substitution(ex.element, comparator), true, ex.isLocked);
        return ret;
      });
      return cols.concat(before).concat([between]).concat(after);
    };
    var getElementFor = function (row, column, section, withinSpan, example, comparator, substitution) {
      if (section === 'colgroup' || !withinSpan) {
        var cell = getCell(row, example);
        return elementnew(substitution(cell.element, comparator), true, false);
      } else {
        return getCell(row, column);
      }
    };
    var insertColumnAt = function (grid, index, example, comparator, substitution) {
      return map(grid, function (row) {
        var withinSpan = index > 0 && index < cellLength(row) && comparator(getCellElement(row, index - 1), getCellElement(row, index));
        var sub = getElementFor(row, index, row.section, withinSpan, example, comparator, substitution);
        return addCell(row, index, sub);
      });
    };
    var deleteColumnsAt = function (grid, columns) {
      return bind(grid, function (row) {
        var existingCells = row.cells;
        var cells = foldr(columns, function (acc, column) {
          return column >= 0 && column < acc.length ? acc.slice(0, column).concat(acc.slice(column + 1)) : acc;
        }, existingCells);
        return cells.length > 0 ? [rowcells(cells, row.section)] : [];
      });
    };
    var deleteRowsAt = function (grid, start, finish) {
      var _a = extractGridDetails(grid), rows = _a.rows, cols = _a.cols;
      return cols.concat(rows.slice(0, start)).concat(rows.slice(finish + 1));
    };

    var replaceIn = function (grid, targets, comparator, substitution) {
      var isTarget = function (cell) {
        return exists(targets, function (target) {
          return comparator(cell.element, target.element);
        });
      };
      return map(grid, function (row) {
        return mapCells(row, function (cell) {
          return isTarget(cell) ? elementnew(substitution(cell.element, comparator), true, cell.isLocked) : cell;
        });
      });
    };
    var notStartRow = function (grid, rowIndex, colIndex, comparator) {
      return getCellElement(grid[rowIndex], colIndex) !== undefined && (rowIndex > 0 && comparator(getCellElement(grid[rowIndex - 1], colIndex), getCellElement(grid[rowIndex], colIndex)));
    };
    var notStartColumn = function (row, index, comparator) {
      return index > 0 && comparator(getCellElement(row, index - 1), getCellElement(row, index));
    };
    var replaceColumn = function (grid, index, comparator, substitution) {
      var rows = extractGridDetails(grid).rows;
      var targets = bind(rows, function (row, i) {
        var alreadyAdded = notStartRow(grid, i, index, comparator) || notStartColumn(row, index, comparator);
        return alreadyAdded ? [] : [getCell(row, index)];
      });
      return replaceIn(grid, targets, comparator, substitution);
    };
    var replaceRow = function (grid, index, comparator, substitution) {
      var rows = extractGridDetails(grid).rows;
      var targetRow = rows[index];
      var targets = bind(targetRow.cells, function (item, i) {
        var alreadyAdded = notStartRow(rows, index, i, comparator) || notStartColumn(targetRow, i, comparator);
        return alreadyAdded ? [] : [item];
      });
      return replaceIn(grid, targets, comparator, substitution);
    };

    var uniqueColumns = function (details) {
      var uniqueCheck = function (rest, detail) {
        var columnExists = exists(rest, function (currentDetail) {
          return currentDetail.column === detail.column;
        });
        return columnExists ? rest : rest.concat([detail]);
      };
      return foldl(details, uniqueCheck, []).sort(function (detailA, detailB) {
        return detailA.column - detailB.column;
      });
    };

    var elementToData = function (element) {
      var colspan = getAttrValue(element, 'colspan', 1);
      var rowspan = getAttrValue(element, 'rowspan', 1);
      return {
        element: element,
        colspan: colspan,
        rowspan: rowspan
      };
    };
    var modification = function (generators, toData) {
      if (toData === void 0) {
        toData = elementToData;
      }
      var position = Cell(Optional.none());
      var nu = function (data) {
        switch (name(data.element)) {
        case 'col':
          return generators.col(data);
        default:
          return generators.cell(data);
        }
      };
      var nuFrom = function (element) {
        var data = toData(element);
        return nu(data);
      };
      var add = function (element) {
        var replacement = nuFrom(element);
        if (position.get().isNone()) {
          position.set(Optional.some(replacement));
        }
        recent = Optional.some({
          item: element,
          replacement: replacement
        });
        return replacement;
      };
      var recent = Optional.none();
      var getOrInit = function (element, comparator) {
        return recent.fold(function () {
          return add(element);
        }, function (p) {
          return comparator(element, p.item) ? p.replacement : add(element);
        });
      };
      return {
        getOrInit: getOrInit,
        cursor: position.get
      };
    };
    var transform$1 = function (scope, tag) {
      return function (generators) {
        var position = Cell(Optional.none());
        var list = [];
        var find$1 = function (element, comparator) {
          return find(list, function (x) {
            return comparator(x.item, element);
          });
        };
        var makeNew = function (element) {
          var attrs = { scope: scope };
          var cell = generators.replace(element, tag, attrs);
          list.push({
            item: element,
            sub: cell
          });
          if (position.get().isNone()) {
            position.set(Optional.some(cell));
          }
          return cell;
        };
        var replaceOrInit = function (element, comparator) {
          if (name(element) === 'col') {
            return element;
          } else {
            return find$1(element, comparator).fold(function () {
              return makeNew(element);
            }, function (p) {
              return comparator(element, p.item) ? p.sub : makeNew(element);
            });
          }
        };
        return {
          replaceOrInit: replaceOrInit,
          cursor: position.get
        };
      };
    };
    var getScopeAttribute = function (cell) {
      return getOpt(cell, 'scope').map(function (attribute) {
        return attribute.substr(0, 3);
      });
    };
    var merging = function (generators) {
      var position = Cell(Optional.none());
      var unmerge = function (cell) {
        if (position.get().isNone()) {
          position.set(Optional.some(cell));
        }
        var scope = getScopeAttribute(cell);
        scope.each(function (attribute) {
          return set(cell, 'scope', attribute);
        });
        return function () {
          var raw = generators.cell({
            element: cell,
            colspan: 1,
            rowspan: 1
          });
          remove$1(raw, 'width');
          remove$1(cell, 'width');
          scope.each(function (attribute) {
            return set(raw, 'scope', attribute);
          });
          return raw;
        };
      };
      var merge = function (cells) {
        var getScopeProperty = function () {
          var stringAttributes = cat(map(cells, getScopeAttribute));
          if (stringAttributes.length === 0) {
            return Optional.none();
          } else {
            var baseScope_1 = stringAttributes[0];
            var scopes_1 = [
              'row',
              'col'
            ];
            var isMixed = exists(stringAttributes, function (attribute) {
              return attribute !== baseScope_1 && contains(scopes_1, attribute);
            });
            return isMixed ? Optional.none() : Optional.from(baseScope_1);
          }
        };
        remove$1(cells[0], 'width');
        getScopeProperty().fold(function () {
          return remove(cells[0], 'scope');
        }, function (attribute) {
          return set(cells[0], 'scope', attribute + 'group');
        });
        return constant(cells[0]);
      };
      return {
        unmerge: unmerge,
        merge: merge,
        cursor: position.get
      };
    };
    var Generators = {
      modification: modification,
      transform: transform$1,
      merging: merging
    };

    var blockList = [
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
      'table',
      'thead',
      'tfoot',
      'tbody',
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
    var isList = function (universe, item) {
      var tagName = universe.property().name(item);
      return contains([
        'ol',
        'ul'
      ], tagName);
    };
    var isBlock = function (universe, item) {
      var tagName = universe.property().name(item);
      return contains(blockList, tagName);
    };
    var isEmptyTag = function (universe, item) {
      return contains([
        'br',
        'img',
        'hr',
        'input'
      ], universe.property().name(item));
    };

    var universe$2 = DomUniverse();
    var isBlock$1 = function (element) {
      return isBlock(universe$2, element);
    };
    var isList$1 = function (element) {
      return isList(universe$2, element);
    };
    var isEmptyTag$1 = function (element) {
      return isEmptyTag(universe$2, element);
    };

    var merge$2 = function (cells) {
      var isBr = function (el) {
        return name(el) === 'br';
      };
      var advancedBr = function (children) {
        return forall(children, function (c) {
          return isBr(c) || isText(c) && get$4(c).trim().length === 0;
        });
      };
      var isListItem = function (el) {
        return name(el) === 'li' || ancestor(el, isList$1).isSome();
      };
      var siblingIsBlock = function (el) {
        return nextSibling(el).map(function (rightSibling) {
          if (isBlock$1(rightSibling)) {
            return true;
          }
          if (isEmptyTag$1(rightSibling)) {
            return name(rightSibling) === 'img' ? false : true;
          }
          return false;
        }).getOr(false);
      };
      var markCell = function (cell) {
        return last$1(cell).bind(function (rightEdge) {
          var rightSiblingIsBlock = siblingIsBlock(rightEdge);
          return parent(rightEdge).map(function (parent) {
            return rightSiblingIsBlock === true || isListItem(parent) || isBr(rightEdge) || isBlock$1(parent) && !eq$1(cell, parent) ? [] : [SugarElement.fromTag('br')];
          });
        }).getOr([]);
      };
      var markContent = function () {
        var content = bind(cells, function (cell) {
          var children$1 = children(cell);
          return advancedBr(children$1) ? [] : children$1.concat(markCell(cell));
        });
        return content.length === 0 ? [SugarElement.fromTag('br')] : content;
      };
      var contents = markContent();
      empty(cells[0]);
      append$1(cells[0], contents);
    };

    var prune = function (table) {
      var cells$1 = cells(table);
      if (cells$1.length === 0) {
        remove$2(table);
      }
    };
    var outcome = function (grid, cursor) {
      return {
        grid: grid,
        cursor: cursor
      };
    };
    var elementFromGrid = function (grid, row, column) {
      var rows = extractGridDetails(grid).rows;
      return findIn(rows, row, column).orThunk(function () {
        return findIn(rows, 0, 0);
      });
    };
    var findIn = function (grid, row, column) {
      return Optional.from(grid[row]).bind(function (r) {
        return Optional.from(r.cells[column]).bind(function (c) {
          return Optional.from(c.element);
        });
      });
    };
    var bundle = function (grid, row, column) {
      var rows = extractGridDetails(grid).rows;
      return outcome(grid, findIn(rows, row, column));
    };
    var uniqueRows = function (details) {
      var rowCompilation = function (rest, detail) {
        var rowExists = exists(rest, function (currentDetail) {
          return currentDetail.row === detail.row;
        });
        return rowExists ? rest : rest.concat([detail]);
      };
      return foldl(details, rowCompilation, []).sort(function (detailA, detailB) {
        return detailA.row - detailB.row;
      });
    };
    var opInsertRowsBefore = function (grid, details, comparator, genWrappers) {
      var targetIndex = details[0].row;
      var rows = uniqueRows(details);
      var newGrid = foldr(rows, function (acc, row) {
        var newG = insertRowAt(acc.grid, targetIndex, row.row + acc.delta, comparator, genWrappers.getOrInit);
        return {
          grid: newG,
          delta: acc.delta + 1
        };
      }, {
        grid: grid,
        delta: 0
      }).grid;
      return bundle(newGrid, targetIndex, details[0].column);
    };
    var opInsertRowsAfter = function (grid, details, comparator, genWrappers) {
      var rows = uniqueRows(details);
      var target = rows[rows.length - 1];
      var targetIndex = target.row + target.rowspan;
      var newGrid = foldr(rows, function (newG, row) {
        return insertRowAt(newG, targetIndex, row.row, comparator, genWrappers.getOrInit);
      }, grid);
      return bundle(newGrid, targetIndex, details[0].column);
    };
    var opInsertColumnsBefore = function (grid, extractDetail, comparator, genWrappers) {
      var details = extractDetail.details;
      var columns = uniqueColumns(details);
      var targetIndex = columns[0].column;
      var newGrid = foldr(columns, function (acc, col) {
        var newG = insertColumnAt(acc.grid, targetIndex, col.column + acc.delta, comparator, genWrappers.getOrInit);
        return {
          grid: newG,
          delta: acc.delta + 1
        };
      }, {
        grid: grid,
        delta: 0
      }).grid;
      return bundle(newGrid, details[0].row, targetIndex);
    };
    var opInsertColumnsAfter = function (grid, extractDetail, comparator, genWrappers) {
      var details = extractDetail.details;
      var target = details[details.length - 1];
      var targetIndex = target.column + target.colspan;
      var columns = uniqueColumns(details);
      var newGrid = foldr(columns, function (newG, col) {
        return insertColumnAt(newG, targetIndex, col.column, comparator, genWrappers.getOrInit);
      }, grid);
      return bundle(newGrid, details[0].row, targetIndex);
    };
    var opMakeRowHeader = function (grid, detail, comparator, genWrappers) {
      var newGrid = replaceRow(grid, detail.row, comparator, genWrappers.replaceOrInit);
      return bundle(newGrid, detail.row, detail.column);
    };
    var opMakeRowsHeader = function (initialGrid, details, comparator, genWrappers) {
      var rows = uniqueRows(details);
      var replacer = function (currentGrid, row) {
        return replaceRow(currentGrid, row.row, comparator, genWrappers.replaceOrInit);
      };
      var newGrid = foldl(rows, replacer, initialGrid);
      return bundle(newGrid, details[0].row, details[0].column);
    };
    var opMakeColumnHeader = function (initialGrid, detail, comparator, genWrappers) {
      var newGrid = replaceColumn(initialGrid, detail.column, comparator, genWrappers.replaceOrInit);
      return bundle(newGrid, detail.row, detail.column);
    };
    var opMakeColumnsHeader = function (initialGrid, details, comparator, genWrappers) {
      var columns = uniqueColumns(details);
      var replacer = function (currentGrid, column) {
        return replaceColumn(currentGrid, column.column, comparator, genWrappers.replaceOrInit);
      };
      var newGrid = foldl(columns, replacer, initialGrid);
      return bundle(newGrid, details[0].row, details[0].column);
    };
    var opUnmakeRowHeader = function (grid, detail, comparator, genWrappers) {
      var newGrid = replaceRow(grid, detail.row, comparator, genWrappers.replaceOrInit);
      return bundle(newGrid, detail.row, detail.column);
    };
    var opUnmakeRowsHeader = function (initialGrid, details, comparator, genWrappers) {
      var rows = uniqueRows(details);
      var replacer = function (currentGrid, row) {
        return replaceRow(currentGrid, row.row, comparator, genWrappers.replaceOrInit);
      };
      var newGrid = foldl(rows, replacer, initialGrid);
      return bundle(newGrid, details[0].row, details[0].column);
    };
    var opUnmakeColumnHeader = function (initialGrid, detail, comparator, genWrappers) {
      var newGrid = replaceColumn(initialGrid, detail.column, comparator, genWrappers.replaceOrInit);
      return bundle(newGrid, detail.row, detail.column);
    };
    var opUnmakeColumnsHeader = function (initialGrid, details, comparator, genWrappers) {
      var columns = uniqueColumns(details);
      var replacer = function (currentGrid, column) {
        return replaceColumn(currentGrid, column.column, comparator, genWrappers.replaceOrInit);
      };
      var newGrid = foldl(columns, replacer, initialGrid);
      return bundle(newGrid, details[0].row, details[0].column);
    };
    var opEraseColumns = function (grid, extractDetail, _comparator, _genWrappers) {
      var columns = uniqueColumns(extractDetail.details);
      var newGrid = deleteColumnsAt(grid, map(columns, function (column) {
        return column.column;
      }));
      var cursor = elementFromGrid(newGrid, columns[0].row, columns[0].column);
      return outcome(newGrid, cursor);
    };
    var opEraseRows = function (grid, details, _comparator, _genWrappers) {
      var rows = uniqueRows(details);
      var newGrid = deleteRowsAt(grid, rows[0].row, rows[rows.length - 1].row);
      var cursor = elementFromGrid(newGrid, details[0].row, details[0].column);
      return outcome(newGrid, cursor);
    };
    var opMergeCells = function (grid, mergable, comparator, genWrappers) {
      var cells = mergable.cells;
      merge$2(cells);
      var newGrid = merge(grid, mergable.bounds, comparator, genWrappers.merge(cells));
      return outcome(newGrid, Optional.from(cells[0]));
    };
    var opUnmergeCells = function (grid, unmergable, comparator, genWrappers) {
      var unmerge$1 = function (b, cell) {
        return unmerge(b, cell, comparator, genWrappers.unmerge(cell));
      };
      var newGrid = foldr(unmergable, unmerge$1, grid);
      return outcome(newGrid, Optional.from(unmergable[0]));
    };
    var opPasteCells = function (grid, pasteDetails, comparator, _genWrappers) {
      var gridify = function (table, generators) {
        var wh = Warehouse.fromTable(table);
        return toGrid(wh, generators, true);
      };
      var gridB = gridify(pasteDetails.clipboard, pasteDetails.generators);
      var startAddress = address(pasteDetails.row, pasteDetails.column);
      var mergedGrid = merge$1(startAddress, grid, gridB, pasteDetails.generators, comparator);
      return mergedGrid.fold(function () {
        return outcome(grid, Optional.some(pasteDetails.element));
      }, function (newGrid) {
        var cursor = elementFromGrid(newGrid, pasteDetails.row, pasteDetails.column);
        return outcome(newGrid, cursor);
      });
    };
    var gridifyRows = function (rows, generators, context) {
      var pasteDetails = fromPastedRows(rows, context.section);
      var wh = Warehouse.generate(pasteDetails);
      return toGrid(wh, generators, true);
    };
    var opPasteColsBefore = function (grid, pasteDetails, comparator, _genWrappers) {
      var rows = extractGridDetails(grid).rows;
      var index = pasteDetails.cells[0].column;
      var context = rows[pasteDetails.cells[0].row];
      var gridB = gridifyRows(pasteDetails.clipboard, pasteDetails.generators, context);
      var mergedGrid = insertCols(index, grid, gridB, pasteDetails.generators, comparator);
      var cursor = elementFromGrid(mergedGrid, pasteDetails.cells[0].row, pasteDetails.cells[0].column);
      return outcome(mergedGrid, cursor);
    };
    var opPasteColsAfter = function (grid, pasteDetails, comparator, _genWrappers) {
      var rows = extractGridDetails(grid).rows;
      var index = pasteDetails.cells[pasteDetails.cells.length - 1].column + pasteDetails.cells[pasteDetails.cells.length - 1].colspan;
      var context = rows[pasteDetails.cells[0].row];
      var gridB = gridifyRows(pasteDetails.clipboard, pasteDetails.generators, context);
      var mergedGrid = insertCols(index, grid, gridB, pasteDetails.generators, comparator);
      var cursor = elementFromGrid(mergedGrid, pasteDetails.cells[0].row, pasteDetails.cells[0].column);
      return outcome(mergedGrid, cursor);
    };
    var opPasteRowsBefore = function (grid, pasteDetails, comparator, _genWrappers) {
      var rows = extractGridDetails(grid).rows;
      var index = pasteDetails.cells[0].row;
      var context = rows[index];
      var gridB = gridifyRows(pasteDetails.clipboard, pasteDetails.generators, context);
      var mergedGrid = insertRows(index, grid, gridB, pasteDetails.generators, comparator);
      var cursor = elementFromGrid(mergedGrid, pasteDetails.cells[0].row, pasteDetails.cells[0].column);
      return outcome(mergedGrid, cursor);
    };
    var opPasteRowsAfter = function (grid, pasteDetails, comparator, _genWrappers) {
      var rows = extractGridDetails(grid).rows;
      var index = pasteDetails.cells[pasteDetails.cells.length - 1].row + pasteDetails.cells[pasteDetails.cells.length - 1].rowspan;
      var context = rows[pasteDetails.cells[0].row];
      var gridB = gridifyRows(pasteDetails.clipboard, pasteDetails.generators, context);
      var mergedGrid = insertRows(index, grid, gridB, pasteDetails.generators, comparator);
      var cursor = elementFromGrid(mergedGrid, pasteDetails.cells[0].row, pasteDetails.cells[0].column);
      return outcome(mergedGrid, cursor);
    };
    var opGetColumnType = function (table, target) {
      var house = Warehouse.fromTable(table);
      var details = onCells(house, target);
      return details.bind(function (selectedCells) {
        var lastSelectedCell = selectedCells[selectedCells.length - 1];
        var minColRange = selectedCells[0].column;
        var maxColRange = lastSelectedCell.column + lastSelectedCell.colspan;
        var selectedColumnCells = flatten(map(house.all, function (row) {
          return filter(row.cells, function (cell) {
            return cell.column >= minColRange && cell.column < maxColRange;
          });
        }));
        return getCellsType(selectedColumnCells, function (cell) {
          return name(cell.element) === 'th';
        });
      }).getOr('');
    };
    var getCellsType = function (cells, headerPred) {
      var headerCells = filter(cells, headerPred);
      if (headerCells.length === 0) {
        return Optional.some('td');
      } else if (headerCells.length === cells.length) {
        return Optional.some('th');
      } else {
        return Optional.none();
      }
    };
    var resize = adjustWidthTo;
    var adjustAndRedistributeWidths$1 = adjustAndRedistributeWidths;
    var firstColumnIsLocked = function (_warehouse, details) {
      return exists(details, function (detail) {
        return detail.column === 0 && detail.isLocked;
      });
    };
    var lastColumnIsLocked = function (warehouse, details) {
      return exists(details, function (detail) {
        return detail.column + detail.colspan >= warehouse.grid.columns && detail.isLocked;
      });
    };
    var getColumnsWidth = function (warehouse, details) {
      var columns = columns$1(warehouse);
      var uniqueCols = uniqueColumns(details);
      return foldl(uniqueCols, function (acc, detail) {
        var column = columns[detail.column];
        var colWidth = column.map(getOuter).getOr(0);
        return acc + colWidth;
      }, 0);
    };
    var insertColumnsExtractor = function (before) {
      return function (warehouse, target) {
        return onCells(warehouse, target).filter(function (details) {
          var checkLocked = before ? firstColumnIsLocked : lastColumnIsLocked;
          return !checkLocked(warehouse, details);
        }).map(function (details) {
          return {
            details: details,
            pixelDelta: getColumnsWidth(warehouse, details)
          };
        });
      };
    };
    var eraseColumnsExtractor = function (warehouse, target) {
      return onUnlockedCells(warehouse, target).map(function (details) {
        return {
          details: details,
          pixelDelta: -getColumnsWidth(warehouse, details)
        };
      });
    };
    var pasteColumnsExtractor = function (before) {
      return function (warehouse, target) {
        return onPasteByEditor(warehouse, target).filter(function (details) {
          var checkLocked = before ? firstColumnIsLocked : lastColumnIsLocked;
          return !checkLocked(warehouse, details.cells);
        });
      };
    };
    var insertRowsBefore = run(opInsertRowsBefore, onCells, noop, noop, Generators.modification);
    var insertRowsAfter = run(opInsertRowsAfter, onCells, noop, noop, Generators.modification);
    var insertColumnsBefore = run(opInsertColumnsBefore, insertColumnsExtractor(true), adjustAndRedistributeWidths$1, noop, Generators.modification);
    var insertColumnsAfter = run(opInsertColumnsAfter, insertColumnsExtractor(false), adjustAndRedistributeWidths$1, noop, Generators.modification);
    var eraseColumns = run(opEraseColumns, eraseColumnsExtractor, adjustAndRedistributeWidths$1, prune, Generators.modification);
    var eraseRows = run(opEraseRows, onCells, noop, prune, Generators.modification);
    var makeColumnHeader = run(opMakeColumnHeader, onUnlockedCell, noop, noop, Generators.transform('row', 'th'));
    var makeColumnsHeader = run(opMakeColumnsHeader, onUnlockedCells, noop, noop, Generators.transform('row', 'th'));
    var unmakeColumnHeader = run(opUnmakeColumnHeader, onUnlockedCell, noop, noop, Generators.transform(null, 'td'));
    var unmakeColumnsHeader = run(opUnmakeColumnsHeader, onUnlockedCells, noop, noop, Generators.transform(null, 'td'));
    var makeRowHeader = run(opMakeRowHeader, onCell, noop, noop, Generators.transform('col', 'th'));
    var makeRowsHeader = run(opMakeRowsHeader, onCells, noop, noop, Generators.transform('col', 'th'));
    var unmakeRowHeader = run(opUnmakeRowHeader, onCell, noop, noop, Generators.transform(null, 'td'));
    var unmakeRowsHeader = run(opUnmakeRowsHeader, onCells, noop, noop, Generators.transform(null, 'td'));
    var mergeCells = run(opMergeCells, onUnlockedMergable, resize, noop, Generators.merging);
    var unmergeCells = run(opUnmergeCells, onUnlockedUnmergable, resize, noop, Generators.merging);
    var pasteCells = run(opPasteCells, onPaste, resize, noop, Generators.modification);
    var pasteColsBefore = run(opPasteColsBefore, pasteColumnsExtractor(true), noop, noop, Generators.modification);
    var pasteColsAfter = run(opPasteColsAfter, pasteColumnsExtractor(false), noop, noop, Generators.modification);
    var pasteRowsBefore = run(opPasteRowsBefore, onPasteByEditor, noop, noop, Generators.modification);
    var pasteRowsAfter = run(opPasteRowsAfter, onPasteByEditor, noop, noop, Generators.modification);
    var getColumnType = opGetColumnType;

    var getSection = function (elm) {
      return getNodeName(elm.parentNode);
    };
    var mapSectionNameToType = function (section) {
      if (section === 'thead') {
        return 'header';
      } else if (section === 'tfoot') {
        return 'footer';
      } else {
        return 'body';
      }
    };
    var detectHeaderRow = function (editor, elm) {
      var isThead = getSection(elm) === 'thead';
      var areAllCellsThs = !exists(elm.cells, function (c) {
        return getNodeName(c) !== 'th';
      });
      return isThead || areAllCellsThs ? Optional.some({
        thead: isThead,
        ths: areAllCellsThs
      }) : Optional.none();
    };
    var getRowType = function (editor, elm) {
      return mapSectionNameToType(detectHeaderRow(editor, elm).fold(function () {
        return getSection(elm);
      }, function (_rowConfig) {
        return 'thead';
      }));
    };
    var switchRowSection = function (dom, rowElm, newSectionName) {
      var tableElm = dom.getParent(rowElm, 'table');
      var oldSectionElm = rowElm.parentNode;
      var oldSectionName = getNodeName(oldSectionElm);
      if (newSectionName !== oldSectionName) {
        var sectionElm_1 = dom.select(newSectionName, tableElm)[0];
        if (!sectionElm_1) {
          sectionElm_1 = dom.create(newSectionName);
          var firstTableChild_1 = tableElm.firstChild;
          if (newSectionName === 'thead') {
            last(children$2(SugarElement.fromDom(tableElm), 'caption,colgroup')).fold(function () {
              return tableElm.insertBefore(sectionElm_1, firstTableChild_1);
            }, function (c) {
              return dom.insertAfter(sectionElm_1, c.dom);
            });
          } else {
            tableElm.appendChild(sectionElm_1);
          }
        }
        if (newSectionName === 'tbody' && oldSectionName === 'thead' && sectionElm_1.firstChild) {
          sectionElm_1.insertBefore(rowElm, sectionElm_1.firstChild);
        } else {
          sectionElm_1.appendChild(rowElm);
        }
        if (!oldSectionElm.hasChildNodes()) {
          dom.remove(oldSectionElm);
        }
      }
    };
    var renameCell = function (editor, cell, newCellType) {
      if (isNonNullable(newCellType) && getNodeName(cell) !== newCellType) {
        var newCellElm = editor.dom.rename(cell, newCellType);
        fireNewCell(editor, newCellElm);
        return newCellElm;
      } else {
        return cell;
      }
    };
    var switchCellType = function (editor, cell, newCellType, scope) {
      var dom = editor.dom;
      var newCell = renameCell(editor, cell, newCellType);
      if (!isUndefined(scope)) {
        dom.setAttrib(newCell, 'scope', scope);
      }
      return newCell;
    };
    var switchCellsType = function (editor, cells, newCellType, scope) {
      return each(cells, function (c) {
        return switchCellType(editor, c, newCellType, scope);
      });
    };
    var switchSectionType = function (editor, rowElm, newType) {
      var determineHeaderRowType = function () {
        var allTableRows = table(SugarElement.fromDom(rowElm.cells[0])).map(function (table) {
          return rows(table);
        }).getOr([]);
        return findMap(allTableRows, function (row) {
          return detectHeaderRow(editor, row.dom);
        }).map(function (detectedType) {
          if (detectedType.thead && detectedType.ths) {
            return 'sectionCells';
          } else {
            return detectedType.thead ? 'section' : 'cells';
          }
        }).getOr('section');
      };
      var dom = editor.dom;
      if (newType === 'header') {
        var headerRowTypeSetting = getTableHeaderType(editor);
        var headerRowType = headerRowTypeSetting === 'auto' ? determineHeaderRowType() : headerRowTypeSetting;
        switchCellsType(editor, rowElm.cells, headerRowType === 'section' ? 'td' : 'th', 'col');
        switchRowSection(dom, rowElm, headerRowType === 'cells' ? 'tbody' : 'thead');
      } else {
        switchCellsType(editor, rowElm.cells, 'td', null);
        switchRowSection(dom, rowElm, newType === 'footer' ? 'tfoot' : 'tbody');
      }
    };

    var getSelectionStartCellFallback = function (start) {
      return table(start).bind(function (table) {
        return retrieve$1(table, ephemera.firstSelectedSelector);
      }).fold(function () {
        return start;
      }, function (cells) {
        return cells[0];
      });
    };
    var getSelectionStartFromSelector = function (selector) {
      return function (start, isRoot) {
        var startCellName = name(start);
        var startCell = startCellName === 'col' || startCellName === 'colgroup' ? getSelectionStartCellFallback(start) : start;
        return closest$1(startCell, selector, isRoot);
      };
    };
    var getSelectionStartCell = getSelectionStartFromSelector('th,td');
    var getSelectionStartCellOrCaption = getSelectionStartFromSelector('th,td,caption');
    var getCellsFromSelection = function (start, selections, isRoot) {
      return getSelectionStartCell(start, isRoot).map(function (_cell) {
        return selection(selections);
      }).getOr([]);
    };
    var getRowsFromSelection = function (start, selector) {
      var cellOpt = getSelectionStartCell(start);
      var rowsOpt = cellOpt.bind(function (cell) {
        return table(cell);
      }).map(function (table) {
        return rows(table);
      });
      return lift2(cellOpt, rowsOpt, function (cell, rows) {
        return filter(rows, function (row) {
          return exists(fromDom$1(row.dom.cells), function (rowCell) {
            return get$2(rowCell, selector) === '1' || eq$1(rowCell, cell);
          });
        });
      }).getOr([]);
    };

    var TableActions = function (editor, lazyWire, selections) {
      var isTableBody = function (editor) {
        return name(getBody$1(editor)) === 'table';
      };
      var lastRowGuard = function (table) {
        return isTableBody(editor) === false || getGridSize(table).rows > 1;
      };
      var lastColumnGuard = function (table) {
        return isTableBody(editor) === false || getGridSize(table).columns > 1;
      };
      var cloneFormats = getCloneElements(editor);
      var colMutationOp = isResizeTableColumnResizing(editor) ? noop : halve;
      var execute = function (operation, guard, mutate, lazyWire, effect) {
        return function (table, target) {
          removeDataStyle(table);
          var wire = lazyWire();
          var doc = SugarElement.fromDom(editor.getDoc());
          var generators = cellOperations(mutate, doc, cloneFormats);
          var sizing = get$9(editor, table);
          var resizeBehaviour = isResizeTableColumnResizing(editor) ? resizeTable() : preserveTable();
          return guard(table) ? operation(wire, table, target, generators, sizing, resizeBehaviour).bind(function (result) {
            each(result.newRows, function (row) {
              fireNewRow(editor, row.dom);
            });
            each(result.newCells, function (cell) {
              fireNewCell(editor, cell.dom);
            });
            return result.cursor.map(function (cell) {
              var des = freefallRtl$2(cell);
              var rng = editor.dom.createRng();
              rng.setStart(des.element.dom, des.offset);
              rng.setEnd(des.element.dom, des.offset);
              return {
                rng: rng,
                effect: effect
              };
            });
          }) : Optional.none();
        };
      };
      var deleteRow = execute(eraseRows, lastRowGuard, noop, lazyWire, structureModified);
      var deleteColumn = execute(eraseColumns, lastColumnGuard, noop, lazyWire, structureModified);
      var insertRowsBefore$1 = execute(insertRowsBefore, always, noop, lazyWire, structureModified);
      var insertRowsAfter$1 = execute(insertRowsAfter, always, noop, lazyWire, structureModified);
      var insertColumnsBefore$1 = execute(insertColumnsBefore, always, colMutationOp, lazyWire, structureModified);
      var insertColumnsAfter$1 = execute(insertColumnsAfter, always, colMutationOp, lazyWire, structureModified);
      var mergeCells$1 = execute(mergeCells, always, noop, lazyWire, structureModified);
      var unmergeCells$1 = execute(unmergeCells, always, noop, lazyWire, structureModified);
      var pasteColsBefore$1 = execute(pasteColsBefore, always, noop, lazyWire, structureModified);
      var pasteColsAfter$1 = execute(pasteColsAfter, always, noop, lazyWire, structureModified);
      var pasteRowsBefore$1 = execute(pasteRowsBefore, always, noop, lazyWire, structureModified);
      var pasteRowsAfter$1 = execute(pasteRowsAfter, always, noop, lazyWire, structureModified);
      var pasteCells$1 = execute(pasteCells, always, noop, lazyWire, structureModified);
      var extractType = function (args, validTypes) {
        return get$1(args, 'type').filter(function (type) {
          return contains(validTypes, type);
        });
      };
      var setTableCellType = function (editor, args) {
        return extractType(args, [
          'td',
          'th'
        ]).each(function (type) {
          var cells = map(getCellsFromSelection(getSelectionStart(editor), selections), function (c) {
            return c.dom;
          });
          switchCellsType(editor, cells, type, null);
        });
      };
      var setTableRowType = function (editor, args) {
        return extractType(args, [
          'header',
          'body',
          'footer'
        ]).each(function (type) {
          map(getRowsFromSelection(getSelectionStart(editor), ephemera.selected), function (row) {
            return switchSectionType(editor, row.dom, type);
          });
        });
      };
      var makeColumnsHeader$1 = execute(makeColumnsHeader, always, noop, lazyWire, structureModified);
      var unmakeColumnsHeader$1 = execute(unmakeColumnsHeader, always, noop, lazyWire, structureModified);
      var getTableRowType = function (editor) {
        var rows = getRowsFromSelection(getSelectionStart(editor), ephemera.selected);
        if (rows.length > 0) {
          var rowTypes = map(rows, function (r) {
            return getRowType(editor, r.dom);
          });
          var hasHeader = contains(rowTypes, 'header');
          var hasFooter = contains(rowTypes, 'footer');
          if (!hasHeader && !hasFooter) {
            return 'body';
          } else {
            var hasBody = contains(rowTypes, 'body');
            if (hasHeader && !hasBody && !hasFooter) {
              return 'header';
            } else if (!hasHeader && !hasBody && hasFooter) {
              return 'footer';
            } else {
              return '';
            }
          }
        }
      };
      var getTableCellType = function (editor) {
        return getCellsType(getCellsFromSelection(getSelectionStart(editor), selections), function (cell) {
          return name(cell) === 'th';
        }).getOr('');
      };
      var getTableColType = getColumnType;
      return {
        deleteRow: deleteRow,
        deleteColumn: deleteColumn,
        insertRowsBefore: insertRowsBefore$1,
        insertRowsAfter: insertRowsAfter$1,
        insertColumnsBefore: insertColumnsBefore$1,
        insertColumnsAfter: insertColumnsAfter$1,
        mergeCells: mergeCells$1,
        unmergeCells: unmergeCells$1,
        pasteColsBefore: pasteColsBefore$1,
        pasteColsAfter: pasteColsAfter$1,
        pasteRowsBefore: pasteRowsBefore$1,
        pasteRowsAfter: pasteRowsAfter$1,
        pasteCells: pasteCells$1,
        setTableCellType: setTableCellType,
        setTableRowType: setTableRowType,
        makeColumnsHeader: makeColumnsHeader$1,
        unmakeColumnsHeader: unmakeColumnsHeader$1,
        getTableRowType: getTableRowType,
        getTableCellType: getTableCellType,
        getTableColType: getTableColType
      };
    };

    var DefaultRenderOptions = {
      styles: {
        'border-collapse': 'collapse',
        'width': '100%'
      },
      attributes: { border: '1' },
      colGroups: false
    };
    var tableHeaderCell = function () {
      return SugarElement.fromTag('th');
    };
    var tableCell = function () {
      return SugarElement.fromTag('td');
    };
    var tableColumn = function () {
      return SugarElement.fromTag('col');
    };
    var createRow = function (columns, rowHeaders, columnHeaders, rowIndex) {
      var tr = SugarElement.fromTag('tr');
      for (var j = 0; j < columns; j++) {
        var td = rowIndex < rowHeaders || j < columnHeaders ? tableHeaderCell() : tableCell();
        if (j < columnHeaders) {
          set(td, 'scope', 'row');
        }
        if (rowIndex < rowHeaders) {
          set(td, 'scope', 'col');
        }
        append(td, SugarElement.fromTag('br'));
        append(tr, td);
      }
      return tr;
    };
    var createGroupRow = function (columns) {
      var columnGroup = SugarElement.fromTag('colgroup');
      range(columns, function () {
        return append(columnGroup, tableColumn());
      });
      return columnGroup;
    };
    var createRows = function (rows, columns, rowHeaders, columnHeaders) {
      return range(rows, function (r) {
        return createRow(columns, rowHeaders, columnHeaders, r);
      });
    };
    var render$1 = function (rows, columns, rowHeaders, columnHeaders, headerType, renderOpts) {
      if (renderOpts === void 0) {
        renderOpts = DefaultRenderOptions;
      }
      var table = SugarElement.fromTag('table');
      var rowHeadersGoInThead = headerType !== 'cells';
      setAll$1(table, renderOpts.styles);
      setAll(table, renderOpts.attributes);
      if (renderOpts.colGroups) {
        append(table, createGroupRow(columns));
      }
      var actualRowHeaders = Math.min(rows, rowHeaders);
      if (rowHeadersGoInThead && rowHeaders > 0) {
        var thead = SugarElement.fromTag('thead');
        append(table, thead);
        var theadRowHeaders = headerType === 'sectionCells' ? actualRowHeaders : 0;
        var theadRows = createRows(rowHeaders, columns, theadRowHeaders, columnHeaders);
        append$1(thead, theadRows);
      }
      var tbody = SugarElement.fromTag('tbody');
      append(table, tbody);
      var numRows = rowHeadersGoInThead ? rows - actualRowHeaders : rows;
      var numRowHeaders = rowHeadersGoInThead ? 0 : rowHeaders;
      var tbodyRows = createRows(numRows, columns, numRowHeaders, columnHeaders);
      append$1(tbody, tbodyRows);
      return table;
    };

    var get$b = function (element) {
      return element.dom.innerHTML;
    };
    var getOuter$2 = function (element) {
      var container = SugarElement.fromTag('div');
      var clone = SugarElement.fromDom(element.dom.cloneNode(true));
      append(container, clone);
      return get$b(container);
    };

    var placeCaretInCell = function (editor, cell) {
      editor.selection.select(cell.dom, true);
      editor.selection.collapse(true);
    };
    var selectFirstCellInTable = function (editor, tableElm) {
      descendant$1(tableElm, 'td,th').each(curry(placeCaretInCell, editor));
    };
    var fireEvents = function (editor, table) {
      each(descendants$1(table, 'tr'), function (row) {
        fireNewRow(editor, row.dom);
        each(descendants$1(row, 'th,td'), function (cell) {
          fireNewCell(editor, cell.dom);
        });
      });
    };
    var isPercentage$1 = function (width) {
      return isString(width) && width.indexOf('%') !== -1;
    };
    var insert$1 = function (editor, columns, rows, colHeaders, rowHeaders) {
      var defaultStyles = getDefaultStyles(editor);
      var options = {
        styles: defaultStyles,
        attributes: getDefaultAttributes(editor),
        colGroups: useColumnGroup(editor)
      };
      editor.undoManager.ignore(function () {
        var table = render$1(rows, columns, rowHeaders, colHeaders, getTableHeaderType(editor), options);
        set(table, 'data-mce-id', '__mce');
        var html = getOuter$2(table);
        editor.insertContent(html);
        editor.addVisual();
      });
      return descendant$1(getBody$1(editor), 'table[data-mce-id="__mce"]').map(function (table) {
        if (isPixelsForced(editor)) {
          enforcePixels(editor, table);
        } else if (isResponsiveForced(editor)) {
          enforceNone(table);
        } else if (isPercentagesForced(editor) || isPercentage$1(defaultStyles.width)) {
          enforcePercentage(editor, table);
        }
        removeDataStyle(table);
        remove(table, 'data-mce-id');
        fireEvents(editor, table);
        selectFirstCellInTable(editor, table);
        return table.dom;
      }).getOr(null);
    };
    var insertTableWithDataValidation = function (editor, rows, columns, options, errorMsg) {
      if (options === void 0) {
        options = {};
      }
      var checkInput = function (val) {
        return isNumber(val) && val > 0;
      };
      if (checkInput(rows) && checkInput(columns)) {
        var headerRows = options.headerRows || 0;
        var headerColumns = options.headerColumns || 0;
        return insert$1(editor, columns, rows, headerColumns, headerRows);
      } else {
        console.error(errorMsg);
        return null;
      }
    };

    var getClipboardElements = function (getClipboard) {
      return function () {
        return getClipboard().fold(function () {
          return [];
        }, function (elems) {
          return map(elems, function (e) {
            return e.dom;
          });
        });
      };
    };
    var setClipboardElements = function (setClipboard) {
      return function (elems) {
        var elmsOpt = elems.length > 0 ? Optional.some(fromDom$1(elems)) : Optional.none();
        setClipboard(elmsOpt);
      };
    };
    var insertTable = function (editor) {
      return function (columns, rows, options) {
        if (options === void 0) {
          options = {};
        }
        var table = insertTableWithDataValidation(editor, rows, columns, options, 'Invalid values for insertTable - rows and columns values are required to insert a table.');
        editor.undoManager.add();
        return table;
      };
    };
    var getApi = function (editor, clipboard, resizeHandler, selectionTargets) {
      return {
        insertTable: insertTable(editor),
        setClipboardRows: setClipboardElements(clipboard.setRows),
        getClipboardRows: getClipboardElements(clipboard.getRows),
        setClipboardCols: setClipboardElements(clipboard.setColumns),
        getClipboardCols: getClipboardElements(clipboard.getColumns),
        resizeHandler: resizeHandler,
        selectionTargets: selectionTargets
      };
    };

    var constrainSpan = function (element, property, value) {
      var currentColspan = getAttrValue(element, property, 1);
      if (value === 1 || currentColspan <= 1) {
        remove(element, property);
      } else {
        set(element, property, Math.min(value, currentColspan));
      }
    };
    var generateColGroup = function (house, minColRange, maxColRange) {
      if (Warehouse.hasColumns(house)) {
        var colsToCopy = filter(Warehouse.justColumns(house), function (col) {
          return col.column >= minColRange && col.column < maxColRange;
        });
        var copiedCols = map(colsToCopy, function (c) {
          var clonedCol = deep(c.element);
          constrainSpan(clonedCol, 'span', maxColRange - minColRange);
          return clonedCol;
        });
        var fakeColgroup = SugarElement.fromTag('colgroup');
        append$1(fakeColgroup, copiedCols);
        return [fakeColgroup];
      } else {
        return [];
      }
    };
    var generateRows = function (house, minColRange, maxColRange) {
      return map(house.all, function (row) {
        var cellsToCopy = filter(row.cells, function (cell) {
          return cell.column >= minColRange && cell.column < maxColRange;
        });
        var copiedCells = map(cellsToCopy, function (cell) {
          var clonedCell = deep(cell.element);
          constrainSpan(clonedCell, 'colspan', maxColRange - minColRange);
          return clonedCell;
        });
        var fakeTR = SugarElement.fromTag('tr');
        append$1(fakeTR, copiedCells);
        return fakeTR;
      });
    };
    var copyCols = function (table, target) {
      var house = Warehouse.fromTable(table);
      var details = onUnlockedCells(house, target);
      return details.map(function (selectedCells) {
        var lastSelectedCell = selectedCells[selectedCells.length - 1];
        var minColRange = selectedCells[0].column;
        var maxColRange = lastSelectedCell.column + lastSelectedCell.colspan;
        var fakeColGroups = generateColGroup(house, minColRange, maxColRange);
        var fakeRows = generateRows(house, minColRange, maxColRange);
        return __spreadArrays(fakeColGroups, fakeRows);
      });
    };

    var copyRows = function (table, target, generators) {
      var warehouse = Warehouse.fromTable(table);
      var details = onCells(warehouse, target);
      return details.bind(function (selectedCells) {
        var grid = toGrid(warehouse, generators, false);
        var rows = extractGridDetails(grid).rows;
        var slicedGrid = rows.slice(selectedCells[0].row, selectedCells[selectedCells.length - 1].row + selectedCells[selectedCells.length - 1].rowspan);
        var filteredGrid = bind(slicedGrid, function (row) {
          var newCells = filter(row.cells, function (cell) {
            return !cell.isLocked;
          });
          return newCells.length > 0 ? [__assign(__assign({}, row), { cells: newCells })] : [];
        });
        var slicedDetails = toDetailList(filteredGrid, generators);
        return someIf(slicedDetails.length > 0, slicedDetails);
      }).map(function (slicedDetails) {
        return copy$2(slicedDetails);
      });
    };

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    var getTDTHOverallStyle = function (dom, elm, name) {
      var cells = dom.select('td,th', elm);
      var firstChildStyle;
      var checkChildren = function (firstChildStyle, elms) {
        for (var i = 0; i < elms.length; i++) {
          var currentStyle = dom.getStyle(elms[i], name);
          if (typeof firstChildStyle === 'undefined') {
            firstChildStyle = currentStyle;
          }
          if (firstChildStyle !== currentStyle) {
            return '';
          }
        }
        return firstChildStyle;
      };
      return checkChildren(firstChildStyle, cells);
    };
    var applyAlign = function (editor, elm, name) {
      if (name) {
        editor.formatter.apply('align' + name, {}, elm);
      }
    };
    var applyVAlign = function (editor, elm, name) {
      if (name) {
        editor.formatter.apply('valign' + name, {}, elm);
      }
    };
    var unApplyAlign = function (editor, elm) {
      global$1.each('left center right'.split(' '), function (name) {
        editor.formatter.remove('align' + name, {}, elm);
      });
    };
    var unApplyVAlign = function (editor, elm) {
      global$1.each('top middle bottom'.split(' '), function (name) {
        editor.formatter.remove('valign' + name, {}, elm);
      });
    };

    var isListGroup = function (item) {
      return hasNonNullableKey(item, 'menu');
    };
    var buildListItems = function (inputList, startItems) {
      var appendItems = function (values, acc) {
        return acc.concat(map(values, function (item) {
          var text = item.text || item.title;
          if (isListGroup(item)) {
            return {
              text: text,
              items: buildListItems(item.menu)
            };
          } else {
            return {
              text: text,
              value: item.value
            };
          }
        }));
      };
      return appendItems(inputList, startItems || []);
    };
    var rgbToHex = function (dom) {
      return function (value) {
        return startsWith(value, 'rgb') ? dom.toHex(value) : value;
      };
    };
    var extractAdvancedStyles = function (dom, elm) {
      var element = SugarElement.fromDom(elm);
      return {
        borderwidth: getRaw(element, 'border-width').getOr(''),
        borderstyle: getRaw(element, 'border-style').getOr(''),
        bordercolor: getRaw(element, 'border-color').map(rgbToHex(dom)).getOr(''),
        backgroundcolor: getRaw(element, 'background-color').map(rgbToHex(dom)).getOr('')
      };
    };
    var getSharedValues = function (data) {
      var baseData = data[0];
      var comparisonData = data.slice(1);
      each(comparisonData, function (items) {
        each(keys(baseData), function (key) {
          each$1(items, function (itemValue, itemKey) {
            var comparisonValue = baseData[key];
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
    var getAdvancedTab = function (dialogName) {
      var advTabItems = [
        {
          name: 'borderstyle',
          type: 'listbox',
          label: 'Border style',
          items: [
            {
              text: 'Select...',
              value: ''
            },
            {
              text: 'Solid',
              value: 'solid'
            },
            {
              text: 'Dotted',
              value: 'dotted'
            },
            {
              text: 'Dashed',
              value: 'dashed'
            },
            {
              text: 'Double',
              value: 'double'
            },
            {
              text: 'Groove',
              value: 'groove'
            },
            {
              text: 'Ridge',
              value: 'ridge'
            },
            {
              text: 'Inset',
              value: 'inset'
            },
            {
              text: 'Outset',
              value: 'outset'
            },
            {
              text: 'None',
              value: 'none'
            },
            {
              text: 'Hidden',
              value: 'hidden'
            }
          ]
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
      var borderWidth = {
        name: 'borderwidth',
        type: 'input',
        label: 'Border width'
      };
      var items = dialogName === 'cell' ? [borderWidth].concat(advTabItems) : advTabItems;
      return {
        title: 'Advanced',
        name: 'advanced',
        items: items
      };
    };
    var getAlignment = function (formats, formatName, editor, elm) {
      return find(formats, function (name) {
        return editor.formatter.matchNode(elm, formatName + name);
      }).getOr('');
    };
    var getHAlignment = curry(getAlignment, [
      'left',
      'center',
      'right'
    ], 'align');
    var getVAlignment = curry(getAlignment, [
      'top',
      'middle',
      'bottom'
    ], 'valign');
    var extractDataFromSettings = function (editor, hasAdvTableTab) {
      var style = getDefaultStyles(editor);
      var attrs = getDefaultAttributes(editor);
      var extractAdvancedStyleData = function (dom) {
        return {
          borderstyle: get$1(style, 'border-style').getOr(''),
          bordercolor: rgbToHex(dom)(get$1(style, 'border-color').getOr('')),
          backgroundcolor: rgbToHex(dom)(get$1(style, 'background-color').getOr(''))
        };
      };
      var defaultData = {
        height: '',
        width: '100%',
        cellspacing: '',
        cellpadding: '',
        caption: false,
        class: '',
        align: '',
        border: ''
      };
      var getBorder = function () {
        var borderWidth = style['border-width'];
        if (shouldStyleWithCss(editor) && borderWidth) {
          return { border: borderWidth };
        }
        return get$1(attrs, 'border').fold(function () {
          return {};
        }, function (border) {
          return { border: border };
        });
      };
      var advStyle = hasAdvTableTab ? extractAdvancedStyleData(editor.dom) : {};
      var getCellPaddingCellSpacing = function () {
        var spacing = get$1(style, 'border-spacing').or(get$1(attrs, 'cellspacing')).fold(function () {
          return {};
        }, function (cellspacing) {
          return { cellspacing: cellspacing };
        });
        var padding = get$1(style, 'border-padding').or(get$1(attrs, 'cellpadding')).fold(function () {
          return {};
        }, function (cellpadding) {
          return { cellpadding: cellpadding };
        });
        return __assign(__assign({}, spacing), padding);
      };
      var data = __assign(__assign(__assign(__assign(__assign(__assign({}, defaultData), style), attrs), advStyle), getBorder()), getCellPaddingCellSpacing());
      return data;
    };
    var extractDataFromTableElement = function (editor, elm, hasAdvTableTab) {
      var getBorder = function (dom, elm) {
        var optBorderWidth = getRaw(SugarElement.fromDom(elm), 'border-width');
        if (shouldStyleWithCss(editor) && optBorderWidth.isSome()) {
          return optBorderWidth.getOr('');
        }
        return dom.getAttrib(elm, 'border') || getTDTHOverallStyle(editor.dom, elm, 'border-width') || getTDTHOverallStyle(editor.dom, elm, 'border');
      };
      var dom = editor.dom;
      return __assign({
        width: dom.getStyle(elm, 'width') || dom.getAttrib(elm, 'width'),
        height: dom.getStyle(elm, 'height') || dom.getAttrib(elm, 'height'),
        cellspacing: dom.getStyle(elm, 'border-spacing') || dom.getAttrib(elm, 'cellspacing'),
        cellpadding: dom.getAttrib(elm, 'cellpadding') || getTDTHOverallStyle(editor.dom, elm, 'padding'),
        border: getBorder(dom, elm),
        caption: !!dom.select('caption', elm)[0],
        class: dom.getAttrib(elm, 'class', ''),
        align: getHAlignment(editor, elm)
      }, hasAdvTableTab ? extractAdvancedStyles(dom, elm) : {});
    };
    var extractDataFromRowElement = function (editor, elm, hasAdvancedRowTab) {
      var dom = editor.dom;
      return __assign({
        height: dom.getStyle(elm, 'height') || dom.getAttrib(elm, 'height'),
        class: dom.getAttrib(elm, 'class', ''),
        type: getRowType(editor, elm),
        align: getHAlignment(editor, elm)
      }, hasAdvancedRowTab ? extractAdvancedStyles(dom, elm) : {});
    };
    var extractDataFromCellElement = function (editor, cell, hasAdvancedCellTab, column) {
      var dom = editor.dom;
      var colElm = column.getOr(cell);
      var getStyle = function (element, style) {
        return dom.getStyle(element, style) || dom.getAttrib(element, style);
      };
      return __assign({
        width: getStyle(colElm, 'width'),
        height: getStyle(cell, 'height'),
        scope: dom.getAttrib(cell, 'scope'),
        celltype: getNodeName(cell),
        class: dom.getAttrib(cell, 'class', ''),
        halign: getHAlignment(editor, cell),
        valign: getVAlignment(editor, cell)
      }, hasAdvancedCellTab ? extractAdvancedStyles(dom, cell) : {});
    };

    var getClassList = function (editor) {
      var classes = buildListItems(getCellClassList(editor));
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
    var children$3 = [
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
        items: [
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
        ]
      }
    ];
    var getItems = function (editor) {
      return children$3.concat(getClassList(editor).toArray());
    };

    var modifiers = function (testTruthy) {
      return function (editor, node) {
        var dom = editor.dom;
        var setAttrib = function (attr, value) {
          if (!testTruthy || value) {
            dom.setAttrib(node, attr, value);
          }
        };
        var setStyle = function (prop, value) {
          if (!testTruthy || value) {
            dom.setStyle(node, prop, value);
          }
        };
        var setFormat = function (formatName, value) {
          if (!testTruthy || value) {
            if (value === '') {
              editor.formatter.remove(formatName, { value: null }, node, true);
            } else {
              editor.formatter.apply(formatName, { value: value }, node);
            }
          }
        };
        return {
          setAttrib: setAttrib,
          setStyle: setStyle,
          setFormat: setFormat
        };
      };
    };
    var DomModifier = {
      normal: modifiers(false),
      ifTruthy: modifiers(true)
    };

    var getSelectedCells = function (cells) {
      return table(cells[0]).map(function (table) {
        var warehouse = Warehouse.fromTable(table);
        var allCells = Warehouse.justCells(warehouse);
        var filtered = filter(allCells, function (cellA) {
          return exists(cells, function (cellB) {
            return eq$1(cellA.element, cellB);
          });
        });
        return map(filtered, function (cell) {
          return {
            element: cell.element.dom,
            column: Warehouse.getColumnAt(warehouse, cell.column).map(function (col) {
              return col.element.dom;
            })
          };
        });
      });
    };
    var updateSimpleProps = function (modifier, colModifier, data) {
      modifier.setAttrib('scope', data.scope);
      modifier.setAttrib('class', data.class);
      modifier.setStyle('height', addPxSuffix(data.height));
      colModifier.setStyle('width', addPxSuffix(data.width));
    };
    var updateAdvancedProps = function (modifier, data) {
      modifier.setFormat('tablecellbackgroundcolor', data.backgroundcolor);
      modifier.setFormat('tablecellbordercolor', data.bordercolor);
      modifier.setFormat('tablecellborderstyle', data.borderstyle);
      modifier.setFormat('tablecellborderwidth', addPxSuffix(data.borderwidth));
    };
    var applyCellData = function (editor, cells, oldData, data) {
      var isSingleCell = cells.length === 1;
      var modifiedData = filter$1(data, function (value, key) {
        return oldData[key] !== value;
      });
      if (size(modifiedData) > 0 && cells.length >= 1) {
        var tableOpt = table(cells[0]);
        getSelectedCells(cells).each(function (selectedCells) {
          each(selectedCells, function (item) {
            var cellElm = switchCellType(editor, item.element, data.celltype);
            var modifier = isSingleCell ? DomModifier.normal(editor, cellElm) : DomModifier.ifTruthy(editor, cellElm);
            var colModifier = item.column.map(function (col) {
              return isSingleCell ? DomModifier.normal(editor, col) : DomModifier.ifTruthy(editor, col);
            }).getOr(modifier);
            updateSimpleProps(modifier, colModifier, data);
            if (hasAdvancedCellTab(editor)) {
              updateAdvancedProps(modifier, data);
            }
            if (isSingleCell) {
              unApplyAlign(editor, cellElm);
              unApplyVAlign(editor, cellElm);
            }
            if (data.halign) {
              applyAlign(editor, cellElm, data.halign);
            }
            if (data.valign) {
              applyVAlign(editor, cellElm, data.valign);
            }
          });
        });
        var styleModified_1 = size(filter$1(modifiedData, function (_value, key) {
          return key !== 'scope' && key !== 'celltype';
        })) > 0;
        tableOpt.each(function (table) {
          return fireTableModified(editor, table.dom, {
            structure: has(modifiedData, 'celltype'),
            style: styleModified_1
          });
        });
      }
    };
    var onSubmitCellForm = function (editor, cells, oldData, api) {
      var data = api.getData();
      api.close();
      editor.undoManager.transact(function () {
        applyCellData(editor, cells, oldData, data);
        editor.focus();
      });
    };
    var getData = function (editor, cells) {
      var cellsData = getSelectedCells(cells).map(function (selectedCells) {
        return map(selectedCells, function (item) {
          return extractDataFromCellElement(editor, item.element, hasAdvancedCellTab(editor), item.column);
        });
      });
      return getSharedValues(cellsData.getOrDie());
    };
    var open = function (editor, selections) {
      var cells = getCellsFromSelection(getSelectionStart(editor), selections);
      if (cells.length === 0) {
        return;
      }
      var data = getData(editor, cells);
      var dialogTabPanel = {
        type: 'tabpanel',
        tabs: [
          {
            title: 'General',
            name: 'general',
            items: getItems(editor)
          },
          getAdvancedTab('cell')
        ]
      };
      var dialogPanel = {
        type: 'panel',
        items: [{
            type: 'grid',
            columns: 2,
            items: getItems(editor)
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

    var getClassList$1 = function (editor) {
      var classes = buildListItems(getRowClassList(editor));
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
    var formChildren = [
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
    var getItems$1 = function (editor) {
      return formChildren.concat(getClassList$1(editor).toArray());
    };

    var updateSimpleProps$1 = function (modifier, data) {
      modifier.setAttrib('class', data.class);
      modifier.setStyle('height', addPxSuffix(data.height));
    };
    var updateAdvancedProps$1 = function (modifier, data) {
      modifier.setStyle('background-color', data.backgroundcolor);
      modifier.setStyle('border-color', data.bordercolor);
      modifier.setStyle('border-style', data.borderstyle);
    };
    var applyRowData = function (editor, rows, oldData, data) {
      var isSingleRow = rows.length === 1;
      var modifiedData = filter$1(data, function (value, key) {
        return oldData[key] !== value;
      });
      if (size(modifiedData) > 0) {
        each(rows, function (rowElm) {
          if (data.type !== getNodeName(rowElm.parentNode)) {
            switchSectionType(editor, rowElm, data.type);
          }
          var modifier = isSingleRow ? DomModifier.normal(editor, rowElm) : DomModifier.ifTruthy(editor, rowElm);
          updateSimpleProps$1(modifier, data);
          if (hasAdvancedRowTab(editor)) {
            updateAdvancedProps$1(modifier, data);
          }
          if (data.align !== oldData.align) {
            unApplyAlign(editor, rowElm);
            applyAlign(editor, rowElm, data.align);
          }
        });
        var typeModified_1 = has(modifiedData, 'type');
        var styleModified_1 = typeModified_1 ? size(modifiedData) > 1 : true;
        table(SugarElement.fromDom(rows[0])).each(function (table) {
          return fireTableModified(editor, table.dom, {
            structure: typeModified_1,
            style: styleModified_1
          });
        });
      }
    };
    var onSubmitRowForm = function (editor, rows, oldData, api) {
      var data = api.getData();
      api.close();
      editor.undoManager.transact(function () {
        applyRowData(editor, rows, oldData, data);
        editor.focus();
      });
    };
    var open$1 = function (editor) {
      var rows = getRowsFromSelection(getSelectionStart(editor), ephemera.selected);
      if (rows.length === 0) {
        return;
      }
      var rowsData = map(rows, function (rowElm) {
        return extractDataFromRowElement(editor, rowElm.dom, hasAdvancedRowTab(editor));
      });
      var data = getSharedValues(rowsData);
      var dialogTabPanel = {
        type: 'tabpanel',
        tabs: [
          {
            title: 'General',
            name: 'general',
            items: getItems$1(editor)
          },
          getAdvancedTab('row')
        ]
      };
      var dialogPanel = {
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
        onSubmit: curry(onSubmitRowForm, editor, map(rows, function (r) {
          return r.dom;
        }), data)
      });
    };

    var global$2 = tinymce.util.Tools.resolve('tinymce.Env');

    var getItems$2 = function (editor, classes, insertNewTable) {
      var rowColCountItems = !insertNewTable ? [] : [
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
      var alwaysItems = [
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
      var appearanceItems = hasAppearanceOptions(editor) ? [
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
      var alignmentItem = [{
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
      var classListItem = classes.length > 0 ? [{
          type: 'listbox',
          name: 'class',
          label: 'Class',
          items: classes
        }] : [];
      return rowColCountItems.concat(alwaysItems).concat(appearanceItems).concat(alignmentItem).concat(classListItem);
    };

    var styleTDTH = function (dom, elm, name, value) {
      if (elm.tagName === 'TD' || elm.tagName === 'TH') {
        if (isString(name)) {
          dom.setStyle(elm, name, value);
        } else {
          dom.setStyle(elm, name);
        }
      } else {
        if (elm.children) {
          for (var i = 0; i < elm.children.length; i++) {
            styleTDTH(dom, elm.children[i], name, value);
          }
        }
      }
    };
    var applyDataToElement = function (editor, tableElm, data) {
      var dom = editor.dom;
      var attrs = {};
      var styles = {};
      attrs.class = data.class;
      styles.height = addPxSuffix(data.height);
      if (dom.getAttrib(tableElm, 'width') && !shouldStyleWithCss(editor)) {
        attrs.width = removePxSuffix(data.width);
      } else {
        styles.width = addPxSuffix(data.width);
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
        for (var i = 0; i < tableElm.children.length; i++) {
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
        styles['background-color'] = data.backgroundcolor;
        styles['border-color'] = data.bordercolor;
        styles['border-style'] = data.borderstyle;
      }
      attrs.style = dom.serializeStyle(__assign(__assign({}, getDefaultStyles(editor)), styles));
      dom.setAttribs(tableElm, __assign(__assign({}, getDefaultAttributes(editor)), attrs));
    };
    var onSubmitTableForm = function (editor, tableElm, oldData, api) {
      var dom = editor.dom;
      var captionElm;
      var data = api.getData();
      var modifiedData = filter$1(data, function (value, key) {
        return oldData[key] !== value;
      });
      api.close();
      if (data.class === '') {
        delete data.class;
      }
      editor.undoManager.transact(function () {
        if (!tableElm) {
          var cols = parseInt(data.cols, 10) || 1;
          var rows = parseInt(data.rows, 10) || 1;
          tableElm = insert$1(editor, cols, rows, 0, 0);
        }
        if (size(modifiedData) > 0) {
          applyDataToElement(editor, tableElm, data);
          captionElm = dom.select('caption', tableElm)[0];
          if (captionElm && !data.caption) {
            dom.remove(captionElm);
          }
          if (!captionElm && data.caption) {
            captionElm = dom.create('caption');
            captionElm.innerHTML = !global$2.ie ? '<br data-mce-bogus="1"/>' : nbsp;
            tableElm.insertBefore(captionElm, tableElm.firstChild);
          }
          if (data.align === '') {
            unApplyAlign(editor, tableElm);
          } else {
            applyAlign(editor, tableElm, data.align);
          }
        }
        editor.focus();
        editor.addVisual();
        if (size(modifiedData) > 0) {
          var captionModified = has(modifiedData, 'caption');
          var styleModified = captionModified ? size(modifiedData) > 1 : true;
          fireTableModified(editor, tableElm, {
            structure: captionModified,
            style: styleModified
          });
        }
      });
    };
    var open$2 = function (editor, insertNewTable) {
      var dom = editor.dom;
      var tableElm;
      var data = extractDataFromSettings(editor, hasAdvancedTableTab(editor));
      if (insertNewTable === false) {
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
      } else {
        data.cols = '1';
        data.rows = '1';
        if (hasAdvancedTableTab(editor)) {
          data.borderstyle = '';
          data.bordercolor = '';
          data.backgroundcolor = '';
        }
      }
      var classes = buildListItems(getTableClassList(editor));
      if (classes.length > 0) {
        if (data.class) {
          data.class = data.class.replace(/\s*mce\-item\-table\s*/g, '');
        }
      }
      var generalPanel = {
        type: 'grid',
        columns: 2,
        items: getItems$2(editor, classes, insertNewTable)
      };
      var nonAdvancedForm = function () {
        return {
          type: 'panel',
          items: [generalPanel]
        };
      };
      var advancedForm = function () {
        return {
          type: 'tabpanel',
          tabs: [
            {
              title: 'General',
              name: 'general',
              items: [generalPanel]
            },
            getAdvancedTab('table')
          ]
        };
      };
      var dialogBody = hasAdvancedTableTab(editor) ? advancedForm() : nonAdvancedForm();
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

    var getSelectionStartCellOrCaption$1 = function (editor) {
      return getSelectionStartCellOrCaption(getSelectionStart(editor), getIsRoot(editor));
    };
    var getSelectionStartCell$1 = function (editor) {
      return getSelectionStartCell(getSelectionStart(editor), getIsRoot(editor));
    };
    var registerCommands = function (editor, actions, cellSelection, selections, clipboard) {
      var isRoot = getIsRoot(editor);
      var eraseTable = function () {
        return getSelectionStartCellOrCaption$1(editor).each(function (cellOrCaption) {
          table(cellOrCaption, isRoot).filter(not(isRoot)).each(function (table) {
            var cursor = SugarElement.fromText('');
            after(table, cursor);
            remove$2(table);
            if (editor.dom.isEmpty(editor.getBody())) {
              editor.setContent('');
              editor.selection.setCursorLocation();
            } else {
              var rng = editor.dom.createRng();
              rng.setStart(cursor.dom, 0);
              rng.setEnd(cursor.dom, 0);
              editor.selection.setRng(rng);
              editor.nodeChanged();
            }
          });
        });
      };
      var setSizingMode = function (sizing) {
        return getSelectionStartCellOrCaption$1(editor).each(function (cellOrCaption) {
          var isForcedSizing = isResponsiveForced(editor) || isPixelsForced(editor) || isPercentagesForced(editor);
          if (!isForcedSizing) {
            table(cellOrCaption, isRoot).each(function (table) {
              if (sizing === 'relative' && !isPercentSizing$1(table)) {
                enforcePercentage(editor, table);
              } else if (sizing === 'fixed' && !isPixelSizing$1(table)) {
                enforcePixels(editor, table);
              } else if (sizing === 'responsive' && !isNoneSizing$1(table)) {
                enforceNone(table);
              }
              removeDataStyle(table);
              fireTableModified(editor, table.dom, structureModified);
            });
          }
        });
      };
      var getTableFromCell = function (cell) {
        return table(cell, isRoot);
      };
      var postExecute = function (table) {
        return function (data) {
          editor.selection.setRng(data.rng);
          editor.focus();
          cellSelection.clear(table);
          removeDataStyle(table);
          fireTableModified(editor, table.dom, data.effect);
        };
      };
      var actOnSelection = function (execute) {
        return getSelectionStartCell$1(editor).each(function (cell) {
          getTableFromCell(cell).each(function (table) {
            var targets = forMenu(selections, table, cell);
            execute(table, targets).each(postExecute(table));
          });
        });
      };
      var copyRowSelection = function () {
        return getSelectionStartCell$1(editor).map(function (cell) {
          return getTableFromCell(cell).bind(function (table) {
            var targets = forMenu(selections, table, cell);
            var generators = cellOperations(noop, SugarElement.fromDom(editor.getDoc()), Optional.none());
            return copyRows(table, targets, generators);
          });
        });
      };
      var copyColSelection = function () {
        return getSelectionStartCell$1(editor).map(function (cell) {
          return getTableFromCell(cell).bind(function (table) {
            var targets = forMenu(selections, table, cell);
            return copyCols(table, targets);
          });
        });
      };
      var pasteOnSelection = function (execute, getRows) {
        return getRows().each(function (rows) {
          var clonedRows = map(rows, function (row) {
            return deep(row);
          });
          getSelectionStartCell$1(editor).each(function (cell) {
            return getTableFromCell(cell).each(function (table) {
              var generators = paste(SugarElement.fromDom(editor.getDoc()));
              var targets = pasteRows(selections, cell, clonedRows, generators);
              execute(table, targets).each(postExecute(table));
            });
          });
        });
      };
      each$1({
        mceTableSplitCells: function () {
          return actOnSelection(actions.unmergeCells);
        },
        mceTableMergeCells: function () {
          return actOnSelection(actions.mergeCells);
        },
        mceTableInsertRowBefore: function () {
          return actOnSelection(actions.insertRowsBefore);
        },
        mceTableInsertRowAfter: function () {
          return actOnSelection(actions.insertRowsAfter);
        },
        mceTableInsertColBefore: function () {
          return actOnSelection(actions.insertColumnsBefore);
        },
        mceTableInsertColAfter: function () {
          return actOnSelection(actions.insertColumnsAfter);
        },
        mceTableDeleteCol: function () {
          return actOnSelection(actions.deleteColumn);
        },
        mceTableDeleteRow: function () {
          return actOnSelection(actions.deleteRow);
        },
        mceTableCutCol: function (_grid) {
          return copyColSelection().each(function (selection) {
            clipboard.setColumns(selection);
            actOnSelection(actions.deleteColumn);
          });
        },
        mceTableCutRow: function (_grid) {
          return copyRowSelection().each(function (selection) {
            clipboard.setRows(selection);
            actOnSelection(actions.deleteRow);
          });
        },
        mceTableCopyCol: function (_grid) {
          return copyColSelection().each(function (selection) {
            return clipboard.setColumns(selection);
          });
        },
        mceTableCopyRow: function (_grid) {
          return copyRowSelection().each(function (selection) {
            return clipboard.setRows(selection);
          });
        },
        mceTablePasteColBefore: function (_grid) {
          return pasteOnSelection(actions.pasteColsBefore, clipboard.getColumns);
        },
        mceTablePasteColAfter: function (_grid) {
          return pasteOnSelection(actions.pasteColsAfter, clipboard.getColumns);
        },
        mceTablePasteRowBefore: function (_grid) {
          return pasteOnSelection(actions.pasteRowsBefore, clipboard.getRows);
        },
        mceTablePasteRowAfter: function (_grid) {
          return pasteOnSelection(actions.pasteRowsAfter, clipboard.getRows);
        },
        mceTableDelete: eraseTable,
        mceTableSizingMode: function (ui, sizing) {
          return setSizingMode(sizing);
        }
      }, function (func, name) {
        return editor.addCommand(name, func);
      });
      var fireTableModifiedForSelection = function (editor, tableOpt) {
        tableOpt.each(function (table) {
          fireTableModified(editor, table.dom, structureModified);
        });
      };
      each$1({
        mceTableCellType: function (_ui, args) {
          var tableOpt = table(getSelectionStart(editor), isRoot);
          actions.setTableCellType(editor, args);
          fireTableModifiedForSelection(editor, tableOpt);
        },
        mceTableRowType: function (_ui, args) {
          var tableOpt = table(getSelectionStart(editor), isRoot);
          actions.setTableRowType(editor, args);
          fireTableModifiedForSelection(editor, tableOpt);
        }
      }, function (func, name) {
        return editor.addCommand(name, func);
      });
      editor.addCommand('mceTableColType', function (_ui, args) {
        return get$1(args, 'type').each(function (type) {
          return actOnSelection(type === 'th' ? actions.makeColumnsHeader : actions.unmakeColumnsHeader);
        });
      });
      each$1({
        mceTableProps: curry(open$2, editor, false),
        mceTableRowProps: curry(open$1, editor),
        mceTableCellProps: curry(open, editor, selections)
      }, function (func, name) {
        return editor.addCommand(name, function () {
          return func();
        });
      });
      editor.addCommand('mceInsertTable', function (_ui, args) {
        if (isObject(args) && keys(args).length > 0) {
          insertTableWithDataValidation(editor, args.rows, args.columns, args.options, 'Invalid values for mceInsertTable - rows and columns values are required to insert a table.');
        } else {
          open$2(editor, true);
        }
      });
      editor.addCommand('mceTableApplyCellStyle', function (_ui, args) {
        var getFormatName = function (style) {
          return 'tablecell' + style.toLowerCase().replace('-', '');
        };
        if (!isObject(args)) {
          return;
        }
        var cells = getCellsFromSelection(getSelectionStart(editor), selections, isRoot);
        if (cells.length === 0) {
          return;
        }
        var validArgs = filter$1(args, function (value, style) {
          return editor.formatter.has(getFormatName(style)) && isString(value);
        });
        if (isEmpty(validArgs)) {
          return;
        }
        each$1(validArgs, function (value, style) {
          each(cells, function (cell) {
            DomModifier.normal(editor, cell.dom).setFormat(getFormatName(style), value);
          });
        });
        getTableFromCell(cells[0]).each(function (table) {
          return fireTableModified(editor, table.dom, styleModified);
        });
      });
    };

    var registerQueryCommands = function (editor, actions, selections) {
      var isRoot = getIsRoot(editor);
      var getTableFromCell = function (cell) {
        return table(cell, isRoot);
      };
      each$1({
        mceTableRowType: function () {
          return actions.getTableRowType(editor);
        },
        mceTableCellType: function () {
          return actions.getTableCellType(editor);
        },
        mceTableColType: function () {
          return getSelectionStartCell(getSelectionStart(editor)).bind(function (cell) {
            return getTableFromCell(cell).map(function (table) {
              var targets = forMenu(selections, table, cell);
              return actions.getTableColType(table, targets);
            });
          }).getOr('');
        }
      }, function (func, name) {
        return editor.addQueryValueHandler(name, func);
      });
    };

    var Clipboard = function () {
      var rows = Cell(Optional.none());
      var cols = Cell(Optional.none());
      var clearClipboard = function (clipboard) {
        clipboard.set(Optional.none());
      };
      return {
        getRows: rows.get,
        setRows: function (r) {
          rows.set(r);
          clearClipboard(cols);
        },
        clearRows: function () {
          return clearClipboard(rows);
        },
        getColumns: cols.get,
        setColumns: function (c) {
          cols.set(c);
          clearClipboard(rows);
        },
        clearColumns: function () {
          return clearClipboard(cols);
        }
      };
    };

    var cellFormats = {
      tablecellbackgroundcolor: {
        selector: 'td,th',
        styles: { backgroundColor: '%value' },
        remove_similar: true
      },
      tablecellbordercolor: {
        selector: 'td,th',
        styles: { borderColor: '%value' },
        remove_similar: true
      },
      tablecellborderstyle: {
        selector: 'td,th',
        styles: { borderStyle: '%value' },
        remove_similar: true
      },
      tablecellborderwidth: {
        selector: 'td,th',
        styles: { borderWidth: '%value' },
        remove_similar: true
      }
    };
    var registerFormats = function (editor) {
      editor.formatter.register(cellFormats);
    };

    var adt$2 = Adt.generate([
      { none: ['current'] },
      { first: ['current'] },
      {
        middle: [
          'current',
          'target'
        ]
      },
      { last: ['current'] }
    ]);
    var none$2 = function (current) {
      if (current === void 0) {
        current = undefined;
      }
      return adt$2.none(current);
    };
    var CellLocation = __assign(__assign({}, adt$2), { none: none$2 });

    var detect$5 = function (current, isRoot) {
      return table(current, isRoot).bind(function (table) {
        var all = cells(table);
        var index = findIndex(all, function (x) {
          return eq$1(current, x);
        });
        return index.map(function (index) {
          return {
            index: index,
            all: all
          };
        });
      });
    };
    var next = function (current, isRoot) {
      var detection = detect$5(current, isRoot);
      return detection.fold(function () {
        return CellLocation.none(current);
      }, function (info) {
        return info.index + 1 < info.all.length ? CellLocation.middle(current, info.all[info.index + 1]) : CellLocation.last(current);
      });
    };
    var prev = function (current, isRoot) {
      var detection = detect$5(current, isRoot);
      return detection.fold(function () {
        return CellLocation.none();
      }, function (info) {
        return info.index - 1 >= 0 ? CellLocation.middle(current, info.all[info.index - 1]) : CellLocation.first(current);
      });
    };

    var create$2 = function (start, soffset, finish, foffset) {
      return {
        start: start,
        soffset: soffset,
        finish: finish,
        foffset: foffset
      };
    };
    var SimRange = { create: create$2 };

    var adt$3 = Adt.generate([
      { before: ['element'] },
      {
        on: [
          'element',
          'offset'
        ]
      },
      { after: ['element'] }
    ]);
    var cata$1 = function (subject, onBefore, onOn, onAfter) {
      return subject.fold(onBefore, onOn, onAfter);
    };
    var getStart = function (situ) {
      return situ.fold(identity, identity, identity);
    };
    var before$2 = adt$3.before;
    var on = adt$3.on;
    var after$2 = adt$3.after;
    var Situ = {
      before: before$2,
      on: on,
      after: after$2,
      cata: cata$1,
      getStart: getStart
    };

    var adt$4 = Adt.generate([
      { domRange: ['rng'] },
      {
        relative: [
          'startSitu',
          'finishSitu'
        ]
      },
      {
        exact: [
          'start',
          'soffset',
          'finish',
          'foffset'
        ]
      }
    ]);
    var exactFromRange = function (simRange) {
      return adt$4.exact(simRange.start, simRange.soffset, simRange.finish, simRange.foffset);
    };
    var getStart$1 = function (selection) {
      return selection.match({
        domRange: function (rng) {
          return SugarElement.fromDom(rng.startContainer);
        },
        relative: function (startSitu, _finishSitu) {
          return Situ.getStart(startSitu);
        },
        exact: function (start, _soffset, _finish, _foffset) {
          return start;
        }
      });
    };
    var domRange = adt$4.domRange;
    var relative = adt$4.relative;
    var exact = adt$4.exact;
    var getWin = function (selection) {
      var start = getStart$1(selection);
      return defaultView(start);
    };
    var range$1 = SimRange.create;
    var SimSelection = {
      domRange: domRange,
      relative: relative,
      exact: exact,
      exactFromRange: exactFromRange,
      getWin: getWin,
      range: range$1
    };

    var selectNodeContents = function (win, element) {
      var rng = win.document.createRange();
      selectNodeContentsUsing(rng, element);
      return rng;
    };
    var selectNodeContentsUsing = function (rng, element) {
      return rng.selectNodeContents(element.dom);
    };
    var setStart = function (rng, situ) {
      situ.fold(function (e) {
        rng.setStartBefore(e.dom);
      }, function (e, o) {
        rng.setStart(e.dom, o);
      }, function (e) {
        rng.setStartAfter(e.dom);
      });
    };
    var setFinish = function (rng, situ) {
      situ.fold(function (e) {
        rng.setEndBefore(e.dom);
      }, function (e, o) {
        rng.setEnd(e.dom, o);
      }, function (e) {
        rng.setEndAfter(e.dom);
      });
    };
    var relativeToNative = function (win, startSitu, finishSitu) {
      var range = win.document.createRange();
      setStart(range, startSitu);
      setFinish(range, finishSitu);
      return range;
    };
    var exactToNative = function (win, start, soffset, finish, foffset) {
      var rng = win.document.createRange();
      rng.setStart(start.dom, soffset);
      rng.setEnd(finish.dom, foffset);
      return rng;
    };
    var toRect = function (rect) {
      return {
        left: rect.left,
        top: rect.top,
        right: rect.right,
        bottom: rect.bottom,
        width: rect.width,
        height: rect.height
      };
    };
    var getFirstRect = function (rng) {
      var rects = rng.getClientRects();
      var rect = rects.length > 0 ? rects[0] : rng.getBoundingClientRect();
      return rect.width > 0 || rect.height > 0 ? Optional.some(rect).map(toRect) : Optional.none();
    };

    var adt$5 = Adt.generate([
      {
        ltr: [
          'start',
          'soffset',
          'finish',
          'foffset'
        ]
      },
      {
        rtl: [
          'start',
          'soffset',
          'finish',
          'foffset'
        ]
      }
    ]);
    var fromRange = function (win, type, range) {
      return type(SugarElement.fromDom(range.startContainer), range.startOffset, SugarElement.fromDom(range.endContainer), range.endOffset);
    };
    var getRanges = function (win, selection) {
      return selection.match({
        domRange: function (rng) {
          return {
            ltr: constant(rng),
            rtl: Optional.none
          };
        },
        relative: function (startSitu, finishSitu) {
          return {
            ltr: cached(function () {
              return relativeToNative(win, startSitu, finishSitu);
            }),
            rtl: cached(function () {
              return Optional.some(relativeToNative(win, finishSitu, startSitu));
            })
          };
        },
        exact: function (start, soffset, finish, foffset) {
          return {
            ltr: cached(function () {
              return exactToNative(win, start, soffset, finish, foffset);
            }),
            rtl: cached(function () {
              return Optional.some(exactToNative(win, finish, foffset, start, soffset));
            })
          };
        }
      });
    };
    var doDiagnose = function (win, ranges) {
      var rng = ranges.ltr();
      if (rng.collapsed) {
        var reversed = ranges.rtl().filter(function (rev) {
          return rev.collapsed === false;
        });
        return reversed.map(function (rev) {
          return adt$5.rtl(SugarElement.fromDom(rev.endContainer), rev.endOffset, SugarElement.fromDom(rev.startContainer), rev.startOffset);
        }).getOrThunk(function () {
          return fromRange(win, adt$5.ltr, rng);
        });
      } else {
        return fromRange(win, adt$5.ltr, rng);
      }
    };
    var diagnose = function (win, selection) {
      var ranges = getRanges(win, selection);
      return doDiagnose(win, ranges);
    };
    var asLtrRange = function (win, selection) {
      var diagnosis = diagnose(win, selection);
      return diagnosis.match({
        ltr: function (start, soffset, finish, foffset) {
          var rng = win.document.createRange();
          rng.setStart(start.dom, soffset);
          rng.setEnd(finish.dom, foffset);
          return rng;
        },
        rtl: function (start, soffset, finish, foffset) {
          var rng = win.document.createRange();
          rng.setStart(finish.dom, foffset);
          rng.setEnd(start.dom, soffset);
          return rng;
        }
      });
    };
    var ltr$1 = adt$5.ltr;
    var rtl$1 = adt$5.rtl;

    var searchForPoint = function (rectForOffset, x, y, maxX, length) {
      if (length === 0) {
        return 0;
      } else if (x === maxX) {
        return length - 1;
      }
      var xDelta = maxX;
      for (var i = 1; i < length; i++) {
        var rect = rectForOffset(i);
        var curDeltaX = Math.abs(x - rect.left);
        if (y <= rect.bottom) {
          if (y < rect.top || curDeltaX > xDelta) {
            return i - 1;
          } else {
            xDelta = curDeltaX;
          }
        }
      }
      return 0;
    };
    var inRect = function (rect, x, y) {
      return x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom;
    };

    var locateOffset = function (doc, textnode, x, y, rect) {
      var rangeForOffset = function (o) {
        var r = doc.dom.createRange();
        r.setStart(textnode.dom, o);
        r.collapse(true);
        return r;
      };
      var rectForOffset = function (o) {
        var r = rangeForOffset(o);
        return r.getBoundingClientRect();
      };
      var length = get$4(textnode).length;
      var offset = searchForPoint(rectForOffset, x, y, rect.right, length);
      return rangeForOffset(offset);
    };
    var locate = function (doc, node, x, y) {
      var r = doc.dom.createRange();
      r.selectNode(node.dom);
      var rects = r.getClientRects();
      var foundRect = findMap(rects, function (rect) {
        return inRect(rect, x, y) ? Optional.some(rect) : Optional.none();
      });
      return foundRect.map(function (rect) {
        return locateOffset(doc, node, x, y, rect);
      });
    };

    var searchInChildren = function (doc, node, x, y) {
      var r = doc.dom.createRange();
      var nodes = children(node);
      return findMap(nodes, function (n) {
        r.selectNode(n.dom);
        return inRect(r.getBoundingClientRect(), x, y) ? locateNode(doc, n, x, y) : Optional.none();
      });
    };
    var locateNode = function (doc, node, x, y) {
      return isText(node) ? locate(doc, node, x, y) : searchInChildren(doc, node, x, y);
    };
    var locate$1 = function (doc, node, x, y) {
      var r = doc.dom.createRange();
      r.selectNode(node.dom);
      var rect = r.getBoundingClientRect();
      var boundedX = Math.max(rect.left, Math.min(rect.right, x));
      var boundedY = Math.max(rect.top, Math.min(rect.bottom, y));
      return locateNode(doc, node, boundedX, boundedY);
    };

    var COLLAPSE_TO_LEFT = true;
    var COLLAPSE_TO_RIGHT = false;
    var getCollapseDirection = function (rect, x) {
      return x - rect.left < rect.right - x ? COLLAPSE_TO_LEFT : COLLAPSE_TO_RIGHT;
    };
    var createCollapsedNode = function (doc, target, collapseDirection) {
      var r = doc.dom.createRange();
      r.selectNode(target.dom);
      r.collapse(collapseDirection);
      return r;
    };
    var locateInElement = function (doc, node, x) {
      var cursorRange = doc.dom.createRange();
      cursorRange.selectNode(node.dom);
      var rect = cursorRange.getBoundingClientRect();
      var collapseDirection = getCollapseDirection(rect, x);
      var f = collapseDirection === COLLAPSE_TO_LEFT ? first : last$1;
      return f(node).map(function (target) {
        return createCollapsedNode(doc, target, collapseDirection);
      });
    };
    var locateInEmpty = function (doc, node, x) {
      var rect = node.dom.getBoundingClientRect();
      var collapseDirection = getCollapseDirection(rect, x);
      return Optional.some(createCollapsedNode(doc, node, collapseDirection));
    };
    var search = function (doc, node, x) {
      var f = children(node).length === 0 ? locateInEmpty : locateInElement;
      return f(doc, node, x);
    };

    var caretPositionFromPoint = function (doc, x, y) {
      return Optional.from(doc.dom.caretPositionFromPoint(x, y)).bind(function (pos) {
        if (pos.offsetNode === null) {
          return Optional.none();
        }
        var r = doc.dom.createRange();
        r.setStart(pos.offsetNode, pos.offset);
        r.collapse();
        return Optional.some(r);
      });
    };
    var caretRangeFromPoint = function (doc, x, y) {
      return Optional.from(doc.dom.caretRangeFromPoint(x, y));
    };
    var searchTextNodes = function (doc, node, x, y) {
      var r = doc.dom.createRange();
      r.selectNode(node.dom);
      var rect = r.getBoundingClientRect();
      var boundedX = Math.max(rect.left, Math.min(rect.right, x));
      var boundedY = Math.max(rect.top, Math.min(rect.bottom, y));
      return locate$1(doc, node, boundedX, boundedY);
    };
    var searchFromPoint = function (doc, x, y) {
      return SugarElement.fromPoint(doc, x, y).bind(function (elem) {
        var fallback = function () {
          return search(doc, elem, x);
        };
        return children(elem).length === 0 ? fallback() : searchTextNodes(doc, elem, x, y).orThunk(fallback);
      });
    };
    var availableSearch = function () {
      if (document.caretPositionFromPoint) {
        return caretPositionFromPoint;
      } else if (document.caretRangeFromPoint) {
        return caretRangeFromPoint;
      } else {
        return searchFromPoint;
      }
    }();
    var fromPoint$1 = function (win, x, y) {
      var doc = SugarElement.fromDom(win.document);
      return availableSearch(doc, x, y).map(function (rng) {
        return SimRange.create(SugarElement.fromDom(rng.startContainer), rng.startOffset, SugarElement.fromDom(rng.endContainer), rng.endOffset);
      });
    };

    var beforeSpecial = function (element, offset) {
      var name$1 = name(element);
      if ('input' === name$1) {
        return Situ.after(element);
      } else if (!contains([
          'br',
          'img'
        ], name$1)) {
        return Situ.on(element, offset);
      } else {
        return offset === 0 ? Situ.before(element) : Situ.after(element);
      }
    };
    var preprocessRelative = function (startSitu, finishSitu) {
      var start = startSitu.fold(Situ.before, beforeSpecial, Situ.after);
      var finish = finishSitu.fold(Situ.before, beforeSpecial, Situ.after);
      return SimSelection.relative(start, finish);
    };
    var preprocessExact = function (start, soffset, finish, foffset) {
      var startSitu = beforeSpecial(start, soffset);
      var finishSitu = beforeSpecial(finish, foffset);
      return SimSelection.relative(startSitu, finishSitu);
    };
    var preprocess = function (selection) {
      return selection.match({
        domRange: function (rng) {
          var start = SugarElement.fromDom(rng.startContainer);
          var finish = SugarElement.fromDom(rng.endContainer);
          return preprocessExact(start, rng.startOffset, finish, rng.endOffset);
        },
        relative: preprocessRelative,
        exact: preprocessExact
      });
    };

    var makeRange = function (start, soffset, finish, foffset) {
      var doc = owner(start);
      var rng = doc.dom.createRange();
      rng.setStart(start.dom, soffset);
      rng.setEnd(finish.dom, foffset);
      return rng;
    };
    var after$3 = function (start, soffset, finish, foffset) {
      var r = makeRange(start, soffset, finish, foffset);
      var same = eq$1(start, finish) && soffset === foffset;
      return r.collapsed && !same;
    };

    var getNativeSelection = function (win) {
      return Optional.from(win.getSelection());
    };
    var doSetNativeRange = function (win, rng) {
      getNativeSelection(win).each(function (selection) {
        selection.removeAllRanges();
        selection.addRange(rng);
      });
    };
    var doSetRange = function (win, start, soffset, finish, foffset) {
      var rng = exactToNative(win, start, soffset, finish, foffset);
      doSetNativeRange(win, rng);
    };
    var setLegacyRtlRange = function (win, selection, start, soffset, finish, foffset) {
      selection.collapse(start.dom, soffset);
      selection.extend(finish.dom, foffset);
    };
    var setRangeFromRelative = function (win, relative) {
      return diagnose(win, relative).match({
        ltr: function (start, soffset, finish, foffset) {
          doSetRange(win, start, soffset, finish, foffset);
        },
        rtl: function (start, soffset, finish, foffset) {
          getNativeSelection(win).each(function (selection) {
            if (selection.setBaseAndExtent) {
              selection.setBaseAndExtent(start.dom, soffset, finish.dom, foffset);
            } else if (selection.extend) {
              try {
                setLegacyRtlRange(win, selection, start, soffset, finish, foffset);
              } catch (e) {
                doSetRange(win, finish, foffset, start, soffset);
              }
            } else {
              doSetRange(win, finish, foffset, start, soffset);
            }
          });
        }
      });
    };
    var setExact = function (win, start, soffset, finish, foffset) {
      var relative = preprocessExact(start, soffset, finish, foffset);
      setRangeFromRelative(win, relative);
    };
    var setRelative = function (win, startSitu, finishSitu) {
      var relative = preprocessRelative(startSitu, finishSitu);
      setRangeFromRelative(win, relative);
    };
    var toNative = function (selection) {
      var win = SimSelection.getWin(selection).dom;
      var getDomRange = function (start, soffset, finish, foffset) {
        return exactToNative(win, start, soffset, finish, foffset);
      };
      var filtered = preprocess(selection);
      return diagnose(win, filtered).match({
        ltr: getDomRange,
        rtl: getDomRange
      });
    };
    var readRange = function (selection) {
      if (selection.rangeCount > 0) {
        var firstRng = selection.getRangeAt(0);
        var lastRng = selection.getRangeAt(selection.rangeCount - 1);
        return Optional.some(SimRange.create(SugarElement.fromDom(firstRng.startContainer), firstRng.startOffset, SugarElement.fromDom(lastRng.endContainer), lastRng.endOffset));
      } else {
        return Optional.none();
      }
    };
    var doGetExact = function (selection) {
      if (selection.anchorNode === null || selection.focusNode === null) {
        return readRange(selection);
      } else {
        var anchor = SugarElement.fromDom(selection.anchorNode);
        var focus_1 = SugarElement.fromDom(selection.focusNode);
        return after$3(anchor, selection.anchorOffset, focus_1, selection.focusOffset) ? Optional.some(SimRange.create(anchor, selection.anchorOffset, focus_1, selection.focusOffset)) : readRange(selection);
      }
    };
    var setToElement = function (win, element) {
      var rng = selectNodeContents(win, element);
      doSetNativeRange(win, rng);
    };
    var getExact = function (win) {
      return getNativeSelection(win).filter(function (sel) {
        return sel.rangeCount > 0;
      }).bind(doGetExact);
    };
    var get$c = function (win) {
      return getExact(win).map(function (range) {
        return SimSelection.exact(range.start, range.soffset, range.finish, range.foffset);
      });
    };
    var getFirstRect$1 = function (win, selection) {
      var rng = asLtrRange(win, selection);
      return getFirstRect(rng);
    };
    var getAtPoint = function (win, x, y) {
      return fromPoint$1(win, x, y);
    };
    var clear = function (win) {
      getNativeSelection(win).each(function (selection) {
        return selection.removeAllRanges();
      });
    };

    var global$3 = tinymce.util.Tools.resolve('tinymce.util.VK');

    var forward = function (editor, isRoot, cell) {
      return go(editor, isRoot, next(cell));
    };
    var backward = function (editor, isRoot, cell) {
      return go(editor, isRoot, prev(cell));
    };
    var getCellFirstCursorPosition = function (editor, cell) {
      var selection = SimSelection.exact(cell, 0, cell, 0);
      return toNative(selection);
    };
    var getNewRowCursorPosition = function (editor, table) {
      var rows = descendants$1(table, 'tr');
      return last(rows).bind(function (last) {
        return descendant$1(last, 'td,th').map(function (first) {
          return getCellFirstCursorPosition(editor, first);
        });
      });
    };
    var go = function (editor, isRoot, cell) {
      return cell.fold(Optional.none, Optional.none, function (current, next) {
        return first(next).map(function (cell) {
          return getCellFirstCursorPosition(editor, cell);
        });
      }, function (current) {
        return table(current, isRoot).bind(function (table) {
          editor.execCommand('mceTableInsertRowAfter');
          return getNewRowCursorPosition(editor, table);
        });
      });
    };
    var rootElements = [
      'table',
      'li',
      'dl'
    ];
    var handle$1 = function (event, editor, cellSelection) {
      if (event.keyCode === global$3.TAB) {
        var body_1 = getBody$1(editor);
        var isRoot_1 = function (element) {
          var name$1 = name(element);
          return eq$1(element, body_1) || contains(rootElements, name$1);
        };
        var rng = editor.selection.getRng();
        var container = SugarElement.fromDom(event.shiftKey ? rng.startContainer : rng.endContainer);
        cell(container, isRoot_1).each(function (cell) {
          event.preventDefault();
          table(cell, isRoot_1).each(cellSelection.clear);
          editor.selection.collapse(event.shiftKey);
          var navigation = event.shiftKey ? backward : forward;
          var rng = navigation(editor, isRoot_1, cell);
          rng.each(function (range) {
            editor.selection.setRng(range);
          });
        });
      }
    };

    var create$3 = function (selection, kill) {
      return {
        selection: selection,
        kill: kill
      };
    };
    var Response = { create: create$3 };

    var create$4 = function (start, soffset, finish, foffset) {
      return {
        start: Situ.on(start, soffset),
        finish: Situ.on(finish, foffset)
      };
    };
    var Situs = { create: create$4 };

    var convertToRange = function (win, selection) {
      var rng = asLtrRange(win, selection);
      return SimRange.create(SugarElement.fromDom(rng.startContainer), rng.startOffset, SugarElement.fromDom(rng.endContainer), rng.endOffset);
    };
    var makeSitus = Situs.create;

    var sync = function (container, isRoot, start, soffset, finish, foffset, selectRange) {
      if (!(eq$1(start, finish) && soffset === foffset)) {
        return closest$1(start, 'td,th', isRoot).bind(function (s) {
          return closest$1(finish, 'td,th', isRoot).bind(function (f) {
            return detect$6(container, isRoot, s, f, selectRange);
          });
        });
      } else {
        return Optional.none();
      }
    };
    var detect$6 = function (container, isRoot, start, finish, selectRange) {
      if (!eq$1(start, finish)) {
        return identify(start, finish, isRoot).bind(function (cellSel) {
          var boxes = cellSel.boxes.getOr([]);
          if (boxes.length > 0) {
            selectRange(container, boxes, cellSel.start, cellSel.finish);
            return Optional.some(Response.create(Optional.some(makeSitus(start, 0, start, getEnd(start))), true));
          } else {
            return Optional.none();
          }
        });
      } else {
        return Optional.none();
      }
    };
    var update = function (rows, columns, container, selected, annotations) {
      var updateSelection = function (newSels) {
        annotations.clearBeforeUpdate(container);
        annotations.selectRange(container, newSels.boxes, newSels.start, newSels.finish);
        return newSels.boxes;
      };
      return shiftSelection(selected, rows, columns, annotations.firstSelectedSelector, annotations.lastSelectedSelector).map(updateSelection);
    };

    var traverse = function (item, mode) {
      return {
        item: item,
        mode: mode
      };
    };
    var backtrack = function (universe, item, _direction, transition) {
      if (transition === void 0) {
        transition = sidestep;
      }
      return universe.property().parent(item).map(function (p) {
        return traverse(p, transition);
      });
    };
    var sidestep = function (universe, item, direction, transition) {
      if (transition === void 0) {
        transition = advance;
      }
      return direction.sibling(universe, item).map(function (p) {
        return traverse(p, transition);
      });
    };
    var advance = function (universe, item, direction, transition) {
      if (transition === void 0) {
        transition = advance;
      }
      var children = universe.property().children(item);
      var result = direction.first(children);
      return result.map(function (r) {
        return traverse(r, transition);
      });
    };
    var successors = [
      {
        current: backtrack,
        next: sidestep,
        fallback: Optional.none()
      },
      {
        current: sidestep,
        next: advance,
        fallback: Optional.some(backtrack)
      },
      {
        current: advance,
        next: advance,
        fallback: Optional.some(sidestep)
      }
    ];
    var go$1 = function (universe, item, mode, direction, rules) {
      if (rules === void 0) {
        rules = successors;
      }
      var ruleOpt = find(rules, function (succ) {
        return succ.current === mode;
      });
      return ruleOpt.bind(function (rule) {
        return rule.current(universe, item, direction, rule.next).orThunk(function () {
          return rule.fallback.bind(function (fb) {
            return go$1(universe, item, fb, direction);
          });
        });
      });
    };

    var left = function () {
      var sibling = function (universe, item) {
        return universe.query().prevSibling(item);
      };
      var first = function (children) {
        return children.length > 0 ? Optional.some(children[children.length - 1]) : Optional.none();
      };
      return {
        sibling: sibling,
        first: first
      };
    };
    var right = function () {
      var sibling = function (universe, item) {
        return universe.query().nextSibling(item);
      };
      var first = function (children) {
        return children.length > 0 ? Optional.some(children[0]) : Optional.none();
      };
      return {
        sibling: sibling,
        first: first
      };
    };
    var Walkers = {
      left: left,
      right: right
    };

    var hone = function (universe, item, predicate, mode, direction, isRoot) {
      var next = go$1(universe, item, mode, direction);
      return next.bind(function (n) {
        if (isRoot(n.item)) {
          return Optional.none();
        } else {
          return predicate(n.item) ? Optional.some(n.item) : hone(universe, n.item, predicate, n.mode, direction, isRoot);
        }
      });
    };
    var left$1 = function (universe, item, predicate, isRoot) {
      return hone(universe, item, predicate, sidestep, Walkers.left(), isRoot);
    };
    var right$1 = function (universe, item, predicate, isRoot) {
      return hone(universe, item, predicate, sidestep, Walkers.right(), isRoot);
    };

    var isLeaf = function (universe) {
      return function (element) {
        return universe.property().children(element).length === 0;
      };
    };
    var before$3 = function (universe, item, isRoot) {
      return seekLeft(universe, item, isLeaf(universe), isRoot);
    };
    var after$4 = function (universe, item, isRoot) {
      return seekRight(universe, item, isLeaf(universe), isRoot);
    };
    var seekLeft = left$1;
    var seekRight = right$1;

    var universe$3 = DomUniverse();
    var before$4 = function (element, isRoot) {
      return before$3(universe$3, element, isRoot);
    };
    var after$5 = function (element, isRoot) {
      return after$4(universe$3, element, isRoot);
    };
    var seekLeft$1 = function (element, predicate, isRoot) {
      return seekLeft(universe$3, element, predicate, isRoot);
    };
    var seekRight$1 = function (element, predicate, isRoot) {
      return seekRight(universe$3, element, predicate, isRoot);
    };

    var ancestor$2 = function (scope, predicate, isRoot) {
      return ancestor(scope, predicate, isRoot).isSome();
    };

    var adt$6 = Adt.generate([
      { none: ['message'] },
      { success: [] },
      { failedUp: ['cell'] },
      { failedDown: ['cell'] }
    ]);
    var isOverlapping = function (bridge, before, after) {
      var beforeBounds = bridge.getRect(before);
      var afterBounds = bridge.getRect(after);
      return afterBounds.right > beforeBounds.left && afterBounds.left < beforeBounds.right;
    };
    var isRow = function (elem) {
      return closest$1(elem, 'tr');
    };
    var verify = function (bridge, before, beforeOffset, after, afterOffset, failure, isRoot) {
      return closest$1(after, 'td,th', isRoot).bind(function (afterCell) {
        return closest$1(before, 'td,th', isRoot).map(function (beforeCell) {
          if (!eq$1(afterCell, beforeCell)) {
            return sharedOne$1(isRow, [
              afterCell,
              beforeCell
            ]).fold(function () {
              return isOverlapping(bridge, beforeCell, afterCell) ? adt$6.success() : failure(beforeCell);
            }, function (_sharedRow) {
              return failure(beforeCell);
            });
          } else {
            return eq$1(after, afterCell) && getEnd(afterCell) === afterOffset ? failure(beforeCell) : adt$6.none('in same cell');
          }
        });
      }).getOr(adt$6.none('default'));
    };
    var cata$2 = function (subject, onNone, onSuccess, onFailedUp, onFailedDown) {
      return subject.fold(onNone, onSuccess, onFailedUp, onFailedDown);
    };
    var BeforeAfter = __assign(__assign({}, adt$6), {
      verify: verify,
      cata: cata$2
    });

    var inParent = function (parent, children, element, index) {
      return {
        parent: parent,
        children: children,
        element: element,
        index: index
      };
    };
    var indexInParent = function (element) {
      return parent(element).bind(function (parent) {
        var children$1 = children(parent);
        return indexOf(children$1, element).map(function (index) {
          return inParent(parent, children$1, element, index);
        });
      });
    };
    var indexOf = function (elements, element) {
      return findIndex(elements, curry(eq$1, element));
    };

    var isBr = function (elem) {
      return name(elem) === 'br';
    };
    var gatherer = function (cand, gather, isRoot) {
      return gather(cand, isRoot).bind(function (target) {
        return isText(target) && get$4(target).trim().length === 0 ? gatherer(target, gather, isRoot) : Optional.some(target);
      });
    };
    var handleBr = function (isRoot, element, direction) {
      return direction.traverse(element).orThunk(function () {
        return gatherer(element, direction.gather, isRoot);
      }).map(direction.relative);
    };
    var findBr = function (element, offset) {
      return child(element, offset).filter(isBr).orThunk(function () {
        return child(element, offset - 1).filter(isBr);
      });
    };
    var handleParent = function (isRoot, element, offset, direction) {
      return findBr(element, offset).bind(function (br) {
        return direction.traverse(br).fold(function () {
          return gatherer(br, direction.gather, isRoot).map(direction.relative);
        }, function (adjacent) {
          return indexInParent(adjacent).map(function (info) {
            return Situ.on(info.parent, info.index);
          });
        });
      });
    };
    var tryBr = function (isRoot, element, offset, direction) {
      var target = isBr(element) ? handleBr(isRoot, element, direction) : handleParent(isRoot, element, offset, direction);
      return target.map(function (tgt) {
        return {
          start: tgt,
          finish: tgt
        };
      });
    };
    var process = function (analysis) {
      return BeforeAfter.cata(analysis, function (_message) {
        return Optional.none();
      }, function () {
        return Optional.none();
      }, function (cell) {
        return Optional.some(point(cell, 0));
      }, function (cell) {
        return Optional.some(point(cell, getEnd(cell)));
      });
    };

    var moveDown = function (caret, amount) {
      return {
        left: caret.left,
        top: caret.top + amount,
        right: caret.right,
        bottom: caret.bottom + amount
      };
    };
    var moveUp = function (caret, amount) {
      return {
        left: caret.left,
        top: caret.top - amount,
        right: caret.right,
        bottom: caret.bottom - amount
      };
    };
    var translate = function (caret, xDelta, yDelta) {
      return {
        left: caret.left + xDelta,
        top: caret.top + yDelta,
        right: caret.right + xDelta,
        bottom: caret.bottom + yDelta
      };
    };
    var getTop$1 = function (caret) {
      return caret.top;
    };
    var getBottom = function (caret) {
      return caret.bottom;
    };

    var getPartialBox = function (bridge, element, offset) {
      if (offset >= 0 && offset < getEnd(element)) {
        return bridge.getRangedRect(element, offset, element, offset + 1);
      } else if (offset > 0) {
        return bridge.getRangedRect(element, offset - 1, element, offset);
      }
      return Optional.none();
    };
    var toCaret = function (rect) {
      return {
        left: rect.left,
        top: rect.top,
        right: rect.right,
        bottom: rect.bottom
      };
    };
    var getElemBox = function (bridge, element) {
      return Optional.some(bridge.getRect(element));
    };
    var getBoxAt = function (bridge, element, offset) {
      if (isElement(element)) {
        return getElemBox(bridge, element).map(toCaret);
      } else if (isText(element)) {
        return getPartialBox(bridge, element, offset).map(toCaret);
      } else {
        return Optional.none();
      }
    };
    var getEntireBox = function (bridge, element) {
      if (isElement(element)) {
        return getElemBox(bridge, element).map(toCaret);
      } else if (isText(element)) {
        return bridge.getRangedRect(element, 0, element, getEnd(element)).map(toCaret);
      } else {
        return Optional.none();
      }
    };

    var JUMP_SIZE = 5;
    var NUM_RETRIES = 100;
    var adt$7 = Adt.generate([
      { none: [] },
      { retry: ['caret'] }
    ]);
    var isOutside = function (caret, box) {
      return caret.left < box.left || Math.abs(box.right - caret.left) < 1 || caret.left > box.right;
    };
    var inOutsideBlock = function (bridge, element, caret) {
      return closest(element, isBlock$1).fold(never, function (cell) {
        return getEntireBox(bridge, cell).exists(function (box) {
          return isOutside(caret, box);
        });
      });
    };
    var adjustDown = function (bridge, element, guessBox, original, caret) {
      var lowerCaret = moveDown(caret, JUMP_SIZE);
      if (Math.abs(guessBox.bottom - original.bottom) < 1) {
        return adt$7.retry(lowerCaret);
      } else if (guessBox.top > caret.bottom) {
        return adt$7.retry(lowerCaret);
      } else if (guessBox.top === caret.bottom) {
        return adt$7.retry(moveDown(caret, 1));
      } else {
        return inOutsideBlock(bridge, element, caret) ? adt$7.retry(translate(lowerCaret, JUMP_SIZE, 0)) : adt$7.none();
      }
    };
    var adjustUp = function (bridge, element, guessBox, original, caret) {
      var higherCaret = moveUp(caret, JUMP_SIZE);
      if (Math.abs(guessBox.top - original.top) < 1) {
        return adt$7.retry(higherCaret);
      } else if (guessBox.bottom < caret.top) {
        return adt$7.retry(higherCaret);
      } else if (guessBox.bottom === caret.top) {
        return adt$7.retry(moveUp(caret, 1));
      } else {
        return inOutsideBlock(bridge, element, caret) ? adt$7.retry(translate(higherCaret, JUMP_SIZE, 0)) : adt$7.none();
      }
    };
    var upMovement = {
      point: getTop$1,
      adjuster: adjustUp,
      move: moveUp,
      gather: before$4
    };
    var downMovement = {
      point: getBottom,
      adjuster: adjustDown,
      move: moveDown,
      gather: after$5
    };
    var isAtTable = function (bridge, x, y) {
      return bridge.elementFromPoint(x, y).filter(function (elm) {
        return name(elm) === 'table';
      }).isSome();
    };
    var adjustForTable = function (bridge, movement, original, caret, numRetries) {
      return adjustTil(bridge, movement, original, movement.move(caret, JUMP_SIZE), numRetries);
    };
    var adjustTil = function (bridge, movement, original, caret, numRetries) {
      if (numRetries === 0) {
        return Optional.some(caret);
      }
      if (isAtTable(bridge, caret.left, movement.point(caret))) {
        return adjustForTable(bridge, movement, original, caret, numRetries - 1);
      }
      return bridge.situsFromPoint(caret.left, movement.point(caret)).bind(function (guess) {
        return guess.start.fold(Optional.none, function (element) {
          return getEntireBox(bridge, element).bind(function (guessBox) {
            return movement.adjuster(bridge, element, guessBox, original, caret).fold(Optional.none, function (newCaret) {
              return adjustTil(bridge, movement, original, newCaret, numRetries - 1);
            });
          }).orThunk(function () {
            return Optional.some(caret);
          });
        }, Optional.none);
      });
    };
    var ieTryDown = function (bridge, caret) {
      return bridge.situsFromPoint(caret.left, caret.bottom + JUMP_SIZE);
    };
    var ieTryUp = function (bridge, caret) {
      return bridge.situsFromPoint(caret.left, caret.top - JUMP_SIZE);
    };
    var checkScroll = function (movement, adjusted, bridge) {
      if (movement.point(adjusted) > bridge.getInnerHeight()) {
        return Optional.some(movement.point(adjusted) - bridge.getInnerHeight());
      } else if (movement.point(adjusted) < 0) {
        return Optional.some(-movement.point(adjusted));
      } else {
        return Optional.none();
      }
    };
    var retry = function (movement, bridge, caret) {
      var moved = movement.move(caret, JUMP_SIZE);
      var adjusted = adjustTil(bridge, movement, caret, moved, NUM_RETRIES).getOr(moved);
      return checkScroll(movement, adjusted, bridge).fold(function () {
        return bridge.situsFromPoint(adjusted.left, movement.point(adjusted));
      }, function (delta) {
        bridge.scrollBy(0, delta);
        return bridge.situsFromPoint(adjusted.left, movement.point(adjusted) - delta);
      });
    };
    var Retries = {
      tryUp: curry(retry, upMovement),
      tryDown: curry(retry, downMovement),
      ieTryUp: ieTryUp,
      ieTryDown: ieTryDown,
      getJumpSize: constant(JUMP_SIZE)
    };

    var MAX_RETRIES = 20;
    var findSpot = function (bridge, isRoot, direction) {
      return bridge.getSelection().bind(function (sel) {
        return tryBr(isRoot, sel.finish, sel.foffset, direction).fold(function () {
          return Optional.some(point(sel.finish, sel.foffset));
        }, function (brNeighbour) {
          var range = bridge.fromSitus(brNeighbour);
          var analysis = BeforeAfter.verify(bridge, sel.finish, sel.foffset, range.finish, range.foffset, direction.failure, isRoot);
          return process(analysis);
        });
      });
    };
    var scan$1 = function (bridge, isRoot, element, offset, direction, numRetries) {
      if (numRetries === 0) {
        return Optional.none();
      }
      return tryCursor(bridge, isRoot, element, offset, direction).bind(function (situs) {
        var range = bridge.fromSitus(situs);
        var analysis = BeforeAfter.verify(bridge, element, offset, range.finish, range.foffset, direction.failure, isRoot);
        return BeforeAfter.cata(analysis, function () {
          return Optional.none();
        }, function () {
          return Optional.some(situs);
        }, function (cell) {
          if (eq$1(element, cell) && offset === 0) {
            return tryAgain(bridge, element, offset, moveUp, direction);
          } else {
            return scan$1(bridge, isRoot, cell, 0, direction, numRetries - 1);
          }
        }, function (cell) {
          if (eq$1(element, cell) && offset === getEnd(cell)) {
            return tryAgain(bridge, element, offset, moveDown, direction);
          } else {
            return scan$1(bridge, isRoot, cell, getEnd(cell), direction, numRetries - 1);
          }
        });
      });
    };
    var tryAgain = function (bridge, element, offset, move, direction) {
      return getBoxAt(bridge, element, offset).bind(function (box) {
        return tryAt(bridge, direction, move(box, Retries.getJumpSize()));
      });
    };
    var tryAt = function (bridge, direction, box) {
      var browser = detect$3().browser;
      if (browser.isChrome() || browser.isSafari() || browser.isFirefox() || browser.isEdge()) {
        return direction.otherRetry(bridge, box);
      } else if (browser.isIE()) {
        return direction.ieRetry(bridge, box);
      } else {
        return Optional.none();
      }
    };
    var tryCursor = function (bridge, isRoot, element, offset, direction) {
      return getBoxAt(bridge, element, offset).bind(function (box) {
        return tryAt(bridge, direction, box);
      });
    };
    var handle$2 = function (bridge, isRoot, direction) {
      return findSpot(bridge, isRoot, direction).bind(function (spot) {
        return scan$1(bridge, isRoot, spot.element, spot.offset, direction, MAX_RETRIES).map(bridge.fromSitus);
      });
    };

    var inSameTable = function (elem, table) {
      return ancestor$2(elem, function (e) {
        return parent(e).exists(function (p) {
          return eq$1(p, table);
        });
      });
    };
    var simulate = function (bridge, isRoot, direction, initial, anchor) {
      return closest$1(initial, 'td,th', isRoot).bind(function (start) {
        return closest$1(start, 'table', isRoot).bind(function (table) {
          if (!inSameTable(anchor, table)) {
            return Optional.none();
          }
          return handle$2(bridge, isRoot, direction).bind(function (range) {
            return closest$1(range.finish, 'td,th', isRoot).map(function (finish) {
              return {
                start: start,
                finish: finish,
                range: range
              };
            });
          });
        });
      });
    };
    var navigate = function (bridge, isRoot, direction, initial, anchor, precheck) {
      if (detect$3().browser.isIE()) {
        return Optional.none();
      } else {
        return precheck(initial, isRoot).orThunk(function () {
          return simulate(bridge, isRoot, direction, initial, anchor).map(function (info) {
            var range = info.range;
            return Response.create(Optional.some(makeSitus(range.start, range.soffset, range.finish, range.foffset)), true);
          });
        });
      }
    };
    var firstUpCheck = function (initial, isRoot) {
      return closest$1(initial, 'tr', isRoot).bind(function (startRow) {
        return closest$1(startRow, 'table', isRoot).bind(function (table) {
          var rows = descendants$1(table, 'tr');
          if (eq$1(startRow, rows[0])) {
            return seekLeft$1(table, function (element) {
              return last$1(element).isSome();
            }, isRoot).map(function (last) {
              var lastOffset = getEnd(last);
              return Response.create(Optional.some(makeSitus(last, lastOffset, last, lastOffset)), true);
            });
          } else {
            return Optional.none();
          }
        });
      });
    };
    var lastDownCheck = function (initial, isRoot) {
      return closest$1(initial, 'tr', isRoot).bind(function (startRow) {
        return closest$1(startRow, 'table', isRoot).bind(function (table) {
          var rows = descendants$1(table, 'tr');
          if (eq$1(startRow, rows[rows.length - 1])) {
            return seekRight$1(table, function (element) {
              return first(element).isSome();
            }, isRoot).map(function (first) {
              return Response.create(Optional.some(makeSitus(first, 0, first, 0)), true);
            });
          } else {
            return Optional.none();
          }
        });
      });
    };
    var select = function (bridge, container, isRoot, direction, initial, anchor, selectRange) {
      return simulate(bridge, isRoot, direction, initial, anchor).bind(function (info) {
        return detect$6(container, isRoot, info.start, info.finish, selectRange);
      });
    };

    var value$1 = function () {
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

    var findCell = function (target, isRoot) {
      return closest$1(target, 'td,th', isRoot);
    };
    var MouseSelection = function (bridge, container, isRoot, annotations) {
      var cursor = value$1();
      var clearstate = cursor.clear;
      var applySelection = function (event) {
        cursor.on(function (start) {
          annotations.clearBeforeUpdate(container);
          findCell(event.target, isRoot).each(function (finish) {
            identify(start, finish, isRoot).each(function (cellSel) {
              var boxes = cellSel.boxes.getOr([]);
              if (boxes.length > 1 || boxes.length === 1 && !eq$1(start, finish)) {
                annotations.selectRange(container, boxes, cellSel.start, cellSel.finish);
                bridge.selectContents(finish);
              }
            });
          });
        });
      };
      var mousedown = function (event) {
        annotations.clear(container);
        findCell(event.target, isRoot).each(cursor.set);
      };
      var mouseover = function (event) {
        applySelection(event);
      };
      var mouseup = function (event) {
        applySelection(event);
        clearstate();
      };
      return {
        clearstate: clearstate,
        mousedown: mousedown,
        mouseover: mouseover,
        mouseup: mouseup
      };
    };

    var down = {
      traverse: nextSibling,
      gather: after$5,
      relative: Situ.before,
      otherRetry: Retries.tryDown,
      ieRetry: Retries.ieTryDown,
      failure: BeforeAfter.failedDown
    };
    var up = {
      traverse: prevSibling,
      gather: before$4,
      relative: Situ.before,
      otherRetry: Retries.tryUp,
      ieRetry: Retries.ieTryUp,
      failure: BeforeAfter.failedUp
    };

    var isKey = function (key) {
      return function (keycode) {
        return keycode === key;
      };
    };
    var isUp = isKey(38);
    var isDown = isKey(40);
    var isNavigation = function (keycode) {
      return keycode >= 37 && keycode <= 40;
    };
    var ltr$2 = {
      isBackward: isKey(37),
      isForward: isKey(39)
    };
    var rtl$2 = {
      isBackward: isKey(39),
      isForward: isKey(37)
    };

    var get$d = function (_DOC) {
      var doc = _DOC !== undefined ? _DOC.dom : document;
      var x = doc.body.scrollLeft || doc.documentElement.scrollLeft;
      var y = doc.body.scrollTop || doc.documentElement.scrollTop;
      return SugarPosition(x, y);
    };
    var by = function (x, y, _DOC) {
      var doc = _DOC !== undefined ? _DOC.dom : document;
      var win = doc.defaultView;
      if (win) {
        win.scrollBy(x, y);
      }
    };

    var WindowBridge = function (win) {
      var elementFromPoint = function (x, y) {
        return SugarElement.fromPoint(SugarElement.fromDom(win.document), x, y);
      };
      var getRect = function (element) {
        return element.dom.getBoundingClientRect();
      };
      var getRangedRect = function (start, soffset, finish, foffset) {
        var sel = SimSelection.exact(start, soffset, finish, foffset);
        return getFirstRect$1(win, sel);
      };
      var getSelection = function () {
        return get$c(win).map(function (exactAdt) {
          return convertToRange(win, exactAdt);
        });
      };
      var fromSitus = function (situs) {
        var relative = SimSelection.relative(situs.start, situs.finish);
        return convertToRange(win, relative);
      };
      var situsFromPoint = function (x, y) {
        return getAtPoint(win, x, y).map(function (exact) {
          return Situs.create(exact.start, exact.soffset, exact.finish, exact.foffset);
        });
      };
      var clearSelection = function () {
        clear(win);
      };
      var collapseSelection = function (toStart) {
        if (toStart === void 0) {
          toStart = false;
        }
        get$c(win).each(function (sel) {
          return sel.fold(function (rng) {
            return rng.collapse(toStart);
          }, function (startSitu, finishSitu) {
            var situ = toStart ? startSitu : finishSitu;
            setRelative(win, situ, situ);
          }, function (start, soffset, finish, foffset) {
            var node = toStart ? start : finish;
            var offset = toStart ? soffset : foffset;
            setExact(win, node, offset, node, offset);
          });
        });
      };
      var selectContents = function (element) {
        setToElement(win, element);
      };
      var setSelection = function (sel) {
        setExact(win, sel.start, sel.soffset, sel.finish, sel.foffset);
      };
      var setRelativeSelection = function (start, finish) {
        setRelative(win, start, finish);
      };
      var getInnerHeight = function () {
        return win.innerHeight;
      };
      var getScrollY = function () {
        var pos = get$d(SugarElement.fromDom(win.document));
        return pos.top;
      };
      var scrollBy = function (x, y) {
        by(x, y, SugarElement.fromDom(win.document));
      };
      return {
        elementFromPoint: elementFromPoint,
        getRect: getRect,
        getRangedRect: getRangedRect,
        getSelection: getSelection,
        fromSitus: fromSitus,
        situsFromPoint: situsFromPoint,
        clearSelection: clearSelection,
        collapseSelection: collapseSelection,
        setSelection: setSelection,
        setRelativeSelection: setRelativeSelection,
        selectContents: selectContents,
        getInnerHeight: getInnerHeight,
        getScrollY: getScrollY,
        scrollBy: scrollBy
      };
    };

    var rc = function (rows, cols) {
      return {
        rows: rows,
        cols: cols
      };
    };
    var mouse = function (win, container, isRoot, annotations) {
      var bridge = WindowBridge(win);
      var handlers = MouseSelection(bridge, container, isRoot, annotations);
      return {
        clearstate: handlers.clearstate,
        mousedown: handlers.mousedown,
        mouseover: handlers.mouseover,
        mouseup: handlers.mouseup
      };
    };
    var keyboard = function (win, container, isRoot, annotations) {
      var bridge = WindowBridge(win);
      var clearToNavigate = function () {
        annotations.clear(container);
        return Optional.none();
      };
      var keydown = function (event, start, soffset, finish, foffset, direction) {
        var realEvent = event.raw;
        var keycode = realEvent.which;
        var shiftKey = realEvent.shiftKey === true;
        var handler = retrieve(container, annotations.selectedSelector).fold(function () {
          if (isDown(keycode) && shiftKey) {
            return curry(select, bridge, container, isRoot, down, finish, start, annotations.selectRange);
          } else if (isUp(keycode) && shiftKey) {
            return curry(select, bridge, container, isRoot, up, finish, start, annotations.selectRange);
          } else if (isDown(keycode)) {
            return curry(navigate, bridge, isRoot, down, finish, start, lastDownCheck);
          } else if (isUp(keycode)) {
            return curry(navigate, bridge, isRoot, up, finish, start, firstUpCheck);
          } else {
            return Optional.none;
          }
        }, function (selected) {
          var update$1 = function (attempts) {
            return function () {
              var navigation = findMap(attempts, function (delta) {
                return update(delta.rows, delta.cols, container, selected, annotations);
              });
              return navigation.fold(function () {
                return getEdges(container, annotations.firstSelectedSelector, annotations.lastSelectedSelector).map(function (edges) {
                  var relative = isDown(keycode) || direction.isForward(keycode) ? Situ.after : Situ.before;
                  bridge.setRelativeSelection(Situ.on(edges.first, 0), relative(edges.table));
                  annotations.clear(container);
                  return Response.create(Optional.none(), true);
                });
              }, function (_) {
                return Optional.some(Response.create(Optional.none(), true));
              });
            };
          };
          if (isDown(keycode) && shiftKey) {
            return update$1([rc(+1, 0)]);
          } else if (isUp(keycode) && shiftKey) {
            return update$1([rc(-1, 0)]);
          } else if (direction.isBackward(keycode) && shiftKey) {
            return update$1([
              rc(0, -1),
              rc(-1, 0)
            ]);
          } else if (direction.isForward(keycode) && shiftKey) {
            return update$1([
              rc(0, +1),
              rc(+1, 0)
            ]);
          } else if (isNavigation(keycode) && shiftKey === false) {
            return clearToNavigate;
          } else {
            return Optional.none;
          }
        });
        return handler();
      };
      var keyup = function (event, start, soffset, finish, foffset) {
        return retrieve(container, annotations.selectedSelector).fold(function () {
          var realEvent = event.raw;
          var keycode = realEvent.which;
          var shiftKey = realEvent.shiftKey === true;
          if (shiftKey === false) {
            return Optional.none();
          }
          if (isNavigation(keycode)) {
            return sync(container, isRoot, start, soffset, finish, foffset, annotations.selectRange);
          } else {
            return Optional.none();
          }
        }, Optional.none);
      };
      return {
        keydown: keydown,
        keyup: keyup
      };
    };
    var external = function (win, container, isRoot, annotations) {
      var bridge = WindowBridge(win);
      return function (start, finish) {
        annotations.clearBeforeUpdate(container);
        identify(start, finish, isRoot).each(function (cellSel) {
          var boxes = cellSel.boxes.getOr([]);
          annotations.selectRange(container, boxes, cellSel.start, cellSel.finish);
          bridge.selectContents(finish);
          bridge.collapseSelection();
        });
      };
    };

    var remove$7 = function (element, classes) {
      each(classes, function (x) {
        remove$5(element, x);
      });
    };

    var addClass = function (clazz) {
      return function (element) {
        add$3(element, clazz);
      };
    };
    var removeClasses = function (classes) {
      return function (element) {
        remove$7(element, classes);
      };
    };

    var byClass = function (ephemera) {
      var addSelectionClass = addClass(ephemera.selected);
      var removeSelectionClasses = removeClasses([
        ephemera.selected,
        ephemera.lastSelected,
        ephemera.firstSelected
      ]);
      var clear = function (container) {
        var sels = descendants$1(container, ephemera.selectedSelector);
        each(sels, removeSelectionClasses);
      };
      var selectRange = function (container, cells, start, finish) {
        clear(container);
        each(cells, addSelectionClass);
        add$3(start, ephemera.firstSelected);
        add$3(finish, ephemera.lastSelected);
      };
      return {
        clearBeforeUpdate: clear,
        clear: clear,
        selectRange: selectRange,
        selectedSelector: ephemera.selectedSelector,
        firstSelectedSelector: ephemera.firstSelectedSelector,
        lastSelectedSelector: ephemera.lastSelectedSelector
      };
    };
    var byAttr = function (ephemera, onSelection, onClear) {
      var removeSelectionAttributes = function (element) {
        remove(element, ephemera.selected);
        remove(element, ephemera.firstSelected);
        remove(element, ephemera.lastSelected);
      };
      var addSelectionAttribute = function (element) {
        set(element, ephemera.selected, '1');
      };
      var clear = function (container) {
        clearBeforeUpdate(container);
        onClear();
      };
      var clearBeforeUpdate = function (container) {
        var sels = descendants$1(container, ephemera.selectedSelector);
        each(sels, removeSelectionAttributes);
      };
      var selectRange = function (container, cells, start, finish) {
        clear(container);
        each(cells, addSelectionAttribute);
        set(start, ephemera.firstSelected, '1');
        set(finish, ephemera.lastSelected, '1');
        onSelection(cells, start, finish);
      };
      return {
        clearBeforeUpdate: clearBeforeUpdate,
        clear: clear,
        selectRange: selectRange,
        selectedSelector: ephemera.selectedSelector,
        firstSelectedSelector: ephemera.firstSelectedSelector,
        lastSelectedSelector: ephemera.lastSelectedSelector
      };
    };
    var SelectionAnnotation = {
      byClass: byClass,
      byAttr: byAttr
    };

    var getUpOrLeftCells = function (grid, selectedCells, generators) {
      var upGrid = grid.slice(0, selectedCells[selectedCells.length - 1].row + 1);
      var upDetails = toDetailList(upGrid, generators);
      return bind(upDetails, function (detail) {
        var slicedCells = detail.cells.slice(0, selectedCells[selectedCells.length - 1].column + 1);
        return map(slicedCells, function (cell) {
          return cell.element;
        });
      });
    };
    var getDownOrRightCells = function (grid, selectedCells, generators) {
      var downGrid = grid.slice(selectedCells[0].row + selectedCells[0].rowspan - 1, grid.length);
      var downDetails = toDetailList(downGrid, generators);
      return bind(downDetails, function (detail) {
        var slicedCells = detail.cells.slice(selectedCells[0].column + selectedCells[0].colspan - 1, detail.cells.length);
        return map(slicedCells, function (cell) {
          return cell.element;
        });
      });
    };
    var getOtherCells = function (table, target, generators) {
      var warehouse = Warehouse.fromTable(table);
      var details = onCells(warehouse, target);
      return details.map(function (selectedCells) {
        var grid = toGrid(warehouse, generators, false);
        var upOrLeftCells = getUpOrLeftCells(grid, selectedCells, generators);
        var downOrRightCells = getDownOrRightCells(grid, selectedCells, generators);
        return {
          upOrLeftCells: upOrLeftCells,
          downOrRightCells: downOrRightCells
        };
      });
    };

    var hasInternalTarget = function (e) {
      return has$1(SugarElement.fromDom(e.target), 'ephox-snooker-resizer-bar') === false;
    };
    function CellSelection (editor, lazyResize, selectionTargets) {
      var onSelection = function (cells, start, finish) {
        selectionTargets.targets().each(function (targets) {
          var tableOpt = table(start);
          tableOpt.each(function (table) {
            var cloneFormats = getCloneElements(editor);
            var generators = cellOperations(noop, SugarElement.fromDom(editor.getDoc()), cloneFormats);
            var otherCells = getOtherCells(table, targets, generators);
            fireTableSelectionChange(editor, cells, start, finish, otherCells);
          });
        });
      };
      var onClear = function () {
        return fireTableSelectionClear(editor);
      };
      var annotations = SelectionAnnotation.byAttr(ephemera, onSelection, onClear);
      editor.on('init', function (_e) {
        var win = editor.getWin();
        var body = getBody$1(editor);
        var isRoot = getIsRoot(editor);
        var syncSelection = function () {
          var sel = editor.selection;
          var start = SugarElement.fromDom(sel.getStart());
          var end = SugarElement.fromDom(sel.getEnd());
          var shared = sharedOne$1(table, [
            start,
            end
          ]);
          shared.fold(function () {
            return annotations.clear(body);
          }, noop);
        };
        var mouseHandlers = mouse(win, body, isRoot, annotations);
        var keyHandlers = keyboard(win, body, isRoot, annotations);
        var external$1 = external(win, body, isRoot, annotations);
        var hasShiftKey = function (event) {
          return event.raw.shiftKey === true;
        };
        editor.on('TableSelectorChange', function (e) {
          return external$1(e.start, e.finish);
        });
        var handleResponse = function (event, response) {
          if (!hasShiftKey(event)) {
            return;
          }
          if (response.kill) {
            event.kill();
          }
          response.selection.each(function (ns) {
            var relative = SimSelection.relative(ns.start, ns.finish);
            var rng = asLtrRange(win, relative);
            editor.selection.setRng(rng);
          });
        };
        var keyup = function (event) {
          var wrappedEvent = fromRawEvent$1(event);
          if (wrappedEvent.raw.shiftKey && isNavigation(wrappedEvent.raw.which)) {
            var rng = editor.selection.getRng();
            var start = SugarElement.fromDom(rng.startContainer);
            var end = SugarElement.fromDom(rng.endContainer);
            keyHandlers.keyup(wrappedEvent, start, rng.startOffset, end, rng.endOffset).each(function (response) {
              handleResponse(wrappedEvent, response);
            });
          }
        };
        var keydown = function (event) {
          var wrappedEvent = fromRawEvent$1(event);
          lazyResize().each(function (resize) {
            return resize.hideBars();
          });
          var rng = editor.selection.getRng();
          var start = SugarElement.fromDom(rng.startContainer);
          var end = SugarElement.fromDom(rng.endContainer);
          var direction = onDirection(ltr$2, rtl$2)(SugarElement.fromDom(editor.selection.getStart()));
          keyHandlers.keydown(wrappedEvent, start, rng.startOffset, end, rng.endOffset, direction).each(function (response) {
            handleResponse(wrappedEvent, response);
          });
          lazyResize().each(function (resize) {
            return resize.showBars();
          });
        };
        var isLeftMouse = function (raw) {
          return raw.button === 0;
        };
        var isLeftButtonPressed = function (raw) {
          if (raw.buttons === undefined) {
            return true;
          }
          if (global$2.browser.isEdge() && raw.buttons === 0) {
            return true;
          }
          return (raw.buttons & 1) !== 0;
        };
        var dragStart = function (_e) {
          mouseHandlers.clearstate();
        };
        var mouseDown = function (e) {
          if (isLeftMouse(e) && hasInternalTarget(e)) {
            mouseHandlers.mousedown(fromRawEvent$1(e));
          }
        };
        var mouseOver = function (e) {
          if (isLeftButtonPressed(e) && hasInternalTarget(e)) {
            mouseHandlers.mouseover(fromRawEvent$1(e));
          }
        };
        var mouseUp = function (e) {
          if (isLeftMouse(e) && hasInternalTarget(e)) {
            mouseHandlers.mouseup(fromRawEvent$1(e));
          }
        };
        var getDoubleTap = function () {
          var lastTarget = Cell(SugarElement.fromDom(body));
          var lastTimeStamp = Cell(0);
          var touchEnd = function (t) {
            var target = SugarElement.fromDom(t.target);
            if (name(target) === 'td' || name(target) === 'th') {
              var lT = lastTarget.get();
              var lTS = lastTimeStamp.get();
              if (eq$1(lT, target) && t.timeStamp - lTS < 300) {
                t.preventDefault();
                external$1(target, target);
              }
            }
            lastTarget.set(target);
            lastTimeStamp.set(t.timeStamp);
          };
          return { touchEnd: touchEnd };
        };
        var doubleTap = getDoubleTap();
        editor.on('dragstart', dragStart);
        editor.on('mousedown', mouseDown);
        editor.on('mouseover', mouseOver);
        editor.on('mouseup', mouseUp);
        editor.on('touchend', doubleTap.touchEnd);
        editor.on('keyup', keyup);
        editor.on('keydown', keydown);
        editor.on('NodeChange', syncSelection);
      });
      return { clear: annotations.clear };
    }

    var getSelectionTargets = function (editor, selections) {
      var targets = Cell(Optional.none());
      var changeHandlers = Cell([]);
      var selectionDetails = Optional.none();
      var isCaption = isTag('caption');
      var isDisabledForSelection = function (key) {
        return selectionDetails.forall(function (details) {
          return !details[key];
        });
      };
      var findTargets = function () {
        return getSelectionStartCellOrCaption(getSelectionStart(editor), getIsRoot(editor)).bind(function (cellOrCaption) {
          var table$1 = table(cellOrCaption);
          return table$1.map(function (table) {
            if (isCaption(cellOrCaption)) {
              return noMenu(cellOrCaption);
            } else {
              return forMenu(selections, table, cellOrCaption);
            }
          });
        });
      };
      var getExtractedDetails = function (targets) {
        var tableOpt = table(targets.element);
        return tableOpt.map(function (table) {
          var warehouse = Warehouse.fromTable(table);
          var selectedCells = onCells(warehouse, targets).getOr([]);
          var locked = foldl(selectedCells, function (acc, cell) {
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
            locked: locked
          };
        });
      };
      var resetTargets = function () {
        targets.set(cached(findTargets)());
        selectionDetails = targets.get().bind(getExtractedDetails);
        each(changeHandlers.get(), function (handler) {
          return handler();
        });
      };
      var onSetup = function (api, isDisabled) {
        var handler = function () {
          return targets.get().fold(function () {
            api.setDisabled(true);
          }, function (targets) {
            api.setDisabled(isDisabled(targets));
          });
        };
        handler();
        changeHandlers.set(changeHandlers.get().concat([handler]));
        return function () {
          changeHandlers.set(filter(changeHandlers.get(), function (h) {
            return h !== handler;
          }));
        };
      };
      var isDisabledFromLocked = function (lockedDisable) {
        return selectionDetails.exists(function (details) {
          return details.locked[lockedDisable];
        });
      };
      var onSetupTable = function (api) {
        return onSetup(api, function (_) {
          return false;
        });
      };
      var onSetupCellOrRow = function (api) {
        return onSetup(api, function (targets) {
          return isCaption(targets.element);
        });
      };
      var onSetupColumn = function (lockedDisable) {
        return function (api) {
          return onSetup(api, function (targets) {
            return isCaption(targets.element) || isDisabledFromLocked(lockedDisable);
          });
        };
      };
      var onSetupPasteable = function (getClipboardData) {
        return function (api) {
          return onSetup(api, function (targets) {
            return isCaption(targets.element) || getClipboardData().isNone();
          });
        };
      };
      var onSetupPasteableColumn = function (getClipboardData, lockedDisable) {
        return function (api) {
          return onSetup(api, function (targets) {
            return isCaption(targets.element) || getClipboardData().isNone() || isDisabledFromLocked(lockedDisable);
          });
        };
      };
      var onSetupMergeable = function (api) {
        return onSetup(api, function (_targets) {
          return isDisabledForSelection('mergeable');
        });
      };
      var onSetupUnmergeable = function (api) {
        return onSetup(api, function (_targets) {
          return isDisabledForSelection('unmergeable');
        });
      };
      editor.on('NodeChange ExecCommand TableSelectorChange', resetTargets);
      return {
        onSetupTable: onSetupTable,
        onSetupCellOrRow: onSetupCellOrRow,
        onSetupColumn: onSetupColumn,
        onSetupPasteable: onSetupPasteable,
        onSetupPasteableColumn: onSetupPasteableColumn,
        onSetupMergeable: onSetupMergeable,
        onSetupUnmergeable: onSetupUnmergeable,
        resetTargets: resetTargets,
        targets: function () {
          return targets.get();
        }
      };
    };

    var addButtons = function (editor, selectionTargets, clipboard) {
      editor.ui.registry.addMenuButton('table', {
        tooltip: 'Table',
        icon: 'table',
        fetch: function (callback) {
          return callback('inserttable | cell row column | advtablesort | tableprops deletetable');
        }
      });
      var cmd = function (command) {
        return function () {
          return editor.execCommand(command);
        };
      };
      editor.ui.registry.addButton('tableprops', {
        tooltip: 'Table properties',
        onAction: cmd('mceTableProps'),
        icon: 'table',
        onSetup: selectionTargets.onSetupTable
      });
      editor.ui.registry.addButton('tabledelete', {
        tooltip: 'Delete table',
        onAction: cmd('mceTableDelete'),
        icon: 'table-delete-table',
        onSetup: selectionTargets.onSetupTable
      });
      editor.ui.registry.addButton('tablecellprops', {
        tooltip: 'Cell properties',
        onAction: cmd('mceTableCellProps'),
        icon: 'table-cell-properties',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addButton('tablemergecells', {
        tooltip: 'Merge cells',
        onAction: cmd('mceTableMergeCells'),
        icon: 'table-merge-cells',
        onSetup: selectionTargets.onSetupMergeable
      });
      editor.ui.registry.addButton('tablesplitcells', {
        tooltip: 'Split cell',
        onAction: cmd('mceTableSplitCells'),
        icon: 'table-split-cells',
        onSetup: selectionTargets.onSetupUnmergeable
      });
      editor.ui.registry.addButton('tableinsertrowbefore', {
        tooltip: 'Insert row before',
        onAction: cmd('mceTableInsertRowBefore'),
        icon: 'table-insert-row-above',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addButton('tableinsertrowafter', {
        tooltip: 'Insert row after',
        onAction: cmd('mceTableInsertRowAfter'),
        icon: 'table-insert-row-after',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addButton('tabledeleterow', {
        tooltip: 'Delete row',
        onAction: cmd('mceTableDeleteRow'),
        icon: 'table-delete-row',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addButton('tablerowprops', {
        tooltip: 'Row properties',
        onAction: cmd('mceTableRowProps'),
        icon: 'table-row-properties',
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addButton('tableinsertcolbefore', {
        tooltip: 'Insert column before',
        onAction: cmd('mceTableInsertColBefore'),
        icon: 'table-insert-column-before',
        onSetup: selectionTargets.onSetupColumn('onFirst')
      });
      editor.ui.registry.addButton('tableinsertcolafter', {
        tooltip: 'Insert column after',
        onAction: cmd('mceTableInsertColAfter'),
        icon: 'table-insert-column-after',
        onSetup: selectionTargets.onSetupColumn('onLast')
      });
      editor.ui.registry.addButton('tabledeletecol', {
        tooltip: 'Delete column',
        onAction: cmd('mceTableDeleteCol'),
        icon: 'table-delete-column',
        onSetup: selectionTargets.onSetupColumn('onAny')
      });
      editor.ui.registry.addButton('tablecutrow', {
        tooltip: 'Cut row',
        icon: 'cut-row',
        onAction: cmd('mceTableCutRow'),
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addButton('tablecopyrow', {
        tooltip: 'Copy row',
        icon: 'duplicate-row',
        onAction: cmd('mceTableCopyRow'),
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addButton('tablepasterowbefore', {
        tooltip: 'Paste row before',
        icon: 'paste-row-before',
        onAction: cmd('mceTablePasteRowBefore'),
        onSetup: selectionTargets.onSetupPasteable(clipboard.getRows)
      });
      editor.ui.registry.addButton('tablepasterowafter', {
        tooltip: 'Paste row after',
        icon: 'paste-row-after',
        onAction: cmd('mceTablePasteRowAfter'),
        onSetup: selectionTargets.onSetupPasteable(clipboard.getRows)
      });
      editor.ui.registry.addButton('tablecutcol', {
        tooltip: 'Cut column',
        icon: 'cut-column',
        onAction: cmd('mceTableCutCol'),
        onSetup: selectionTargets.onSetupColumn('onAny')
      });
      editor.ui.registry.addButton('tablecopycol', {
        tooltip: 'Copy column',
        icon: 'duplicate-column',
        onAction: cmd('mceTableCopyCol'),
        onSetup: selectionTargets.onSetupColumn('onAny')
      });
      editor.ui.registry.addButton('tablepastecolbefore', {
        tooltip: 'Paste column before',
        icon: 'paste-column-before',
        onAction: cmd('mceTablePasteColBefore'),
        onSetup: selectionTargets.onSetupPasteableColumn(clipboard.getColumns, 'onFirst')
      });
      editor.ui.registry.addButton('tablepastecolafter', {
        tooltip: 'Paste column after',
        icon: 'paste-column-after',
        onAction: cmd('mceTablePasteColAfter'),
        onSetup: selectionTargets.onSetupPasteableColumn(clipboard.getColumns, 'onLast')
      });
      editor.ui.registry.addButton('tableinsertdialog', {
        tooltip: 'Insert table',
        onAction: cmd('mceInsertTable'),
        icon: 'table'
      });
    };
    var addToolbars = function (editor) {
      var isTable = function (table) {
        return editor.dom.is(table, 'table') && editor.getBody().contains(table);
      };
      var toolbar = getToolbar(editor);
      if (toolbar.length > 0) {
        editor.ui.registry.addContextToolbar('table', {
          predicate: isTable,
          items: toolbar,
          scope: 'node',
          position: 'node'
        });
      }
    };

    var addMenuItems = function (editor, selectionTargets, clipboard) {
      var cmd = function (command) {
        return function () {
          return editor.execCommand(command);
        };
      };
      var insertTableAction = function (data) {
        editor.execCommand('mceInsertTable', false, {
          rows: data.numRows,
          columns: data.numColumns
        });
      };
      var tableProperties = {
        text: 'Table properties',
        onSetup: selectionTargets.onSetupTable,
        onAction: cmd('mceTableProps')
      };
      var deleteTable = {
        text: 'Delete table',
        icon: 'table-delete-table',
        onSetup: selectionTargets.onSetupTable,
        onAction: cmd('mceTableDelete')
      };
      editor.ui.registry.addMenuItem('tableinsertrowbefore', {
        text: 'Insert row before',
        icon: 'table-insert-row-above',
        onAction: cmd('mceTableInsertRowBefore'),
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addMenuItem('tableinsertrowafter', {
        text: 'Insert row after',
        icon: 'table-insert-row-after',
        onAction: cmd('mceTableInsertRowAfter'),
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addMenuItem('tabledeleterow', {
        text: 'Delete row',
        icon: 'table-delete-row',
        onAction: cmd('mceTableDeleteRow'),
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addMenuItem('tablerowprops', {
        text: 'Row properties',
        icon: 'table-row-properties',
        onAction: cmd('mceTableRowProps'),
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addMenuItem('tablecutrow', {
        text: 'Cut row',
        icon: 'cut-row',
        onAction: cmd('mceTableCutRow'),
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addMenuItem('tablecopyrow', {
        text: 'Copy row',
        icon: 'duplicate-row',
        onAction: cmd('mceTableCopyRow'),
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addMenuItem('tablepasterowbefore', {
        text: 'Paste row before',
        icon: 'paste-row-before',
        onAction: cmd('mceTablePasteRowBefore'),
        onSetup: selectionTargets.onSetupPasteable(clipboard.getRows)
      });
      editor.ui.registry.addMenuItem('tablepasterowafter', {
        text: 'Paste row after',
        icon: 'paste-row-after',
        onAction: cmd('mceTablePasteRowAfter'),
        onSetup: selectionTargets.onSetupPasteable(clipboard.getRows)
      });
      var row = {
        type: 'nestedmenuitem',
        text: 'Row',
        getSubmenuItems: function () {
          return 'tableinsertrowbefore tableinsertrowafter tabledeleterow tablerowprops | tablecutrow tablecopyrow tablepasterowbefore tablepasterowafter';
        }
      };
      editor.ui.registry.addMenuItem('tableinsertcolumnbefore', {
        text: 'Insert column before',
        icon: 'table-insert-column-before',
        onAction: cmd('mceTableInsertColBefore'),
        onSetup: selectionTargets.onSetupColumn('onFirst')
      });
      editor.ui.registry.addMenuItem('tableinsertcolumnafter', {
        text: 'Insert column after',
        icon: 'table-insert-column-after',
        onAction: cmd('mceTableInsertColAfter'),
        onSetup: selectionTargets.onSetupColumn('onLast')
      });
      editor.ui.registry.addMenuItem('tabledeletecolumn', {
        text: 'Delete column',
        icon: 'table-delete-column',
        onAction: cmd('mceTableDeleteCol'),
        onSetup: selectionTargets.onSetupColumn('onAny')
      });
      editor.ui.registry.addMenuItem('tablecutcolumn', {
        text: 'Cut column',
        icon: 'cut-column',
        onAction: cmd('mceTableCutCol'),
        onSetup: selectionTargets.onSetupColumn('onAny')
      });
      editor.ui.registry.addMenuItem('tablecopycolumn', {
        text: 'Copy column',
        icon: 'duplicate-column',
        onAction: cmd('mceTableCopyCol'),
        onSetup: selectionTargets.onSetupColumn('onAny')
      });
      editor.ui.registry.addMenuItem('tablepastecolumnbefore', {
        text: 'Paste column before',
        icon: 'paste-column-before',
        onAction: cmd('mceTablePasteColBefore'),
        onSetup: selectionTargets.onSetupPasteableColumn(clipboard.getColumns, 'onFirst')
      });
      editor.ui.registry.addMenuItem('tablepastecolumnafter', {
        text: 'Paste column after',
        icon: 'paste-column-after',
        onAction: cmd('mceTablePasteColAfter'),
        onSetup: selectionTargets.onSetupPasteableColumn(clipboard.getColumns, 'onLast')
      });
      var column = {
        type: 'nestedmenuitem',
        text: 'Column',
        getSubmenuItems: function () {
          return 'tableinsertcolumnbefore tableinsertcolumnafter tabledeletecolumn | tablecutcolumn tablecopycolumn tablepastecolumnbefore tablepastecolumnafter';
        }
      };
      editor.ui.registry.addMenuItem('tablecellprops', {
        text: 'Cell properties',
        icon: 'table-cell-properties',
        onAction: cmd('mceTableCellProps'),
        onSetup: selectionTargets.onSetupCellOrRow
      });
      editor.ui.registry.addMenuItem('tablemergecells', {
        text: 'Merge cells',
        icon: 'table-merge-cells',
        onAction: cmd('mceTableMergeCells'),
        onSetup: selectionTargets.onSetupMergeable
      });
      editor.ui.registry.addMenuItem('tablesplitcells', {
        text: 'Split cell',
        icon: 'table-split-cells',
        onAction: cmd('mceTableSplitCells'),
        onSetup: selectionTargets.onSetupUnmergeable
      });
      var cell = {
        type: 'nestedmenuitem',
        text: 'Cell',
        getSubmenuItems: function () {
          return 'tablecellprops tablemergecells tablesplitcells';
        }
      };
      if (hasTableGrid(editor) === false) {
        editor.ui.registry.addMenuItem('inserttable', {
          text: 'Table',
          icon: 'table',
          onAction: cmd('mceInsertTable')
        });
      } else {
        editor.ui.registry.addNestedMenuItem('inserttable', {
          text: 'Table',
          icon: 'table',
          getSubmenuItems: function () {
            return [{
                type: 'fancymenuitem',
                fancytype: 'inserttable',
                onAction: insertTableAction
              }];
          }
        });
      }
      editor.ui.registry.addMenuItem('inserttabledialog', {
        text: 'Insert table',
        icon: 'table',
        onAction: cmd('mceInsertTable')
      });
      editor.ui.registry.addMenuItem('tableprops', tableProperties);
      editor.ui.registry.addMenuItem('deletetable', deleteTable);
      editor.ui.registry.addNestedMenuItem('row', row);
      editor.ui.registry.addNestedMenuItem('column', column);
      editor.ui.registry.addNestedMenuItem('cell', cell);
      editor.ui.registry.addContextMenu('table', {
        update: function () {
          selectionTargets.resetTargets();
          return selectionTargets.targets().fold(function () {
            return '';
          }, function (targets) {
            if (name(targets.element) === 'caption') {
              return 'tableprops deletetable';
            } else {
              return 'cell row column | advtablesort | tableprops deletetable';
            }
          });
        }
      });
    };

    var Plugin = function (editor) {
      var selections = Selections(function () {
        return getBody$1(editor);
      }, function () {
        return getSelectionStartCellOrCaption(getSelectionStart(editor));
      }, ephemera.selectedSelector);
      var selectionTargets = getSelectionTargets(editor, selections);
      var resizeHandler = getResizeHandler(editor);
      var cellSelection = CellSelection(editor, resizeHandler.lazyResize, selectionTargets);
      var actions = TableActions(editor, resizeHandler.lazyWire, selections);
      var clipboard = Clipboard();
      registerCommands(editor, actions, cellSelection, selections, clipboard);
      registerQueryCommands(editor, actions, selections);
      registerEvents(editor, selections, actions, cellSelection);
      addMenuItems(editor, selectionTargets, clipboard);
      addButtons(editor, selectionTargets, clipboard);
      addToolbars(editor);
      editor.on('PreInit', function () {
        editor.serializer.addTempAttr(ephemera.firstSelected);
        editor.serializer.addTempAttr(ephemera.lastSelected);
        registerFormats(editor);
      });
      if (hasTabNavigation(editor)) {
        editor.on('keydown', function (e) {
          handle$1(e, editor, cellSelection);
        });
      }
      editor.on('remove', function () {
        resizeHandler.destroy();
      });
      return getApi(editor, clipboard, resizeHandler, selectionTargets);
    };
    function Plugin$1 () {
      global.add('table', Plugin);
    }

    Plugin$1();

}());
