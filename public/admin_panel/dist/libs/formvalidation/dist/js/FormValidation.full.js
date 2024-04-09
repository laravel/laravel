/**
 * FormValidation (https://formvalidation.io), v1.10.0 (2236098)
 * The best validation library for JavaScript
 * (c) 2013 - 2021 Nguyen Huu Phuoc <me@phuoc.ng>
 */

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
  typeof define === 'function' && define.amd ? define(['exports'], factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.FormValidation = {}));
})(this, (function (exports) { 'use strict';

  function t$15(t) {
    var e = t.length;
    var l = [[0, 1, 2, 3, 4, 5, 6, 7, 8, 9], [0, 2, 4, 6, 8, 1, 3, 5, 7, 9]];
    var n = 0;
    var r = 0;

    while (e--) {
      r += l[n][parseInt(t.charAt(e), 10)];
      n = 1 - n;
    }

    return r % 10 === 0 && r > 0;
  }

  function t$14(t) {
    var e = t.length;
    var n = 5;

    for (var r = 0; r < e; r++) {
      n = ((n || 10) * 2 % 11 + parseInt(t.charAt(r), 10)) % 10;
    }

    return n === 1;
  }

  function t$13(t) {
    var e = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var n = t.length;
    var o = e.length;
    var l = Math.floor(o / 2);

    for (var r = 0; r < n; r++) {
      l = ((l || o) * 2 % (o + 1) + e.indexOf(t.charAt(r))) % o;
    }

    return l === 1;
  }

  function t$12(t) {
    var e = [[0, 1, 2, 3, 4, 5, 6, 7, 8, 9], [1, 2, 3, 4, 0, 6, 7, 8, 9, 5], [2, 3, 4, 0, 1, 7, 8, 9, 5, 6], [3, 4, 0, 1, 2, 8, 9, 5, 6, 7], [4, 0, 1, 2, 3, 9, 5, 6, 7, 8], [5, 9, 8, 7, 6, 0, 4, 3, 2, 1], [6, 5, 9, 8, 7, 1, 0, 4, 3, 2], [7, 6, 5, 9, 8, 2, 1, 0, 4, 3], [8, 7, 6, 5, 9, 3, 2, 1, 0, 4], [9, 8, 7, 6, 5, 4, 3, 2, 1, 0]];
    var n = [[0, 1, 2, 3, 4, 5, 6, 7, 8, 9], [1, 5, 7, 6, 2, 8, 3, 0, 9, 4], [5, 8, 0, 3, 7, 9, 6, 1, 4, 2], [8, 9, 1, 6, 0, 4, 3, 5, 2, 7], [9, 4, 5, 3, 1, 2, 6, 8, 7, 0], [4, 2, 8, 6, 5, 7, 3, 9, 0, 1], [2, 7, 9, 3, 8, 0, 6, 4, 1, 5], [7, 0, 4, 6, 9, 1, 3, 2, 5, 8]];
    var o = t.reverse();
    var r = 0;

    for (var _t = 0; _t < o.length; _t++) {
      r = e[r][n[_t % 8][o[_t]]];
    }

    return r === 0;
  }

  var index$3 = {
    luhn: t$15,
    mod11And10: t$14,
    mod37And36: t$13,
    verhoeff: t$12
  };

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    Object.defineProperty(Constructor, "prototype", {
      writable: false
    });
    return Constructor;
  }

  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {
        value: value,
        enumerable: true,
        configurable: true,
        writable: true
      });
    } else {
      obj[key] = value;
    }

    return obj;
  }

  function _inherits(subClass, superClass) {
    if (typeof superClass !== "function" && superClass !== null) {
      throw new TypeError("Super expression must either be null or a function");
    }

    subClass.prototype = Object.create(superClass && superClass.prototype, {
      constructor: {
        value: subClass,
        writable: true,
        configurable: true
      }
    });
    Object.defineProperty(subClass, "prototype", {
      writable: false
    });
    if (superClass) _setPrototypeOf(subClass, superClass);
  }

  function _getPrototypeOf(o) {
    _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) {
      return o.__proto__ || Object.getPrototypeOf(o);
    };
    return _getPrototypeOf(o);
  }

  function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
      o.__proto__ = p;
      return o;
    };
    return _setPrototypeOf(o, p);
  }

  function _isNativeReflectConstruct() {
    if (typeof Reflect === "undefined" || !Reflect.construct) return false;
    if (Reflect.construct.sham) return false;
    if (typeof Proxy === "function") return true;

    try {
      Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
      return true;
    } catch (e) {
      return false;
    }
  }

  function _assertThisInitialized(self) {
    if (self === void 0) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    }

    return self;
  }

  function _possibleConstructorReturn(self, call) {
    if (call && (typeof call === "object" || typeof call === "function")) {
      return call;
    } else if (call !== void 0) {
      throw new TypeError("Derived constructors may only return object or undefined");
    }

    return _assertThisInitialized(self);
  }

  function _createSuper(Derived) {
    var hasNativeReflectConstruct = _isNativeReflectConstruct();

    return function _createSuperInternal() {
      var Super = _getPrototypeOf(Derived),
          result;

      if (hasNativeReflectConstruct) {
        var NewTarget = _getPrototypeOf(this).constructor;

        result = Reflect.construct(Super, arguments, NewTarget);
      } else {
        result = Super.apply(this, arguments);
      }

      return _possibleConstructorReturn(this, result);
    };
  }

  function _unsupportedIterableToArray(o, minLen) {
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray(o, minLen);
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return Array.from(o);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
  }

  function _arrayLikeToArray(arr, len) {
    if (len == null || len > arr.length) len = arr.length;

    for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];

    return arr2;
  }

  function _createForOfIteratorHelper(o, allowArrayLike) {
    var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"];

    if (!it) {
      if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
        if (it) o = it;
        var i = 0;

        var F = function () {};

        return {
          s: F,
          n: function () {
            if (i >= o.length) return {
              done: true
            };
            return {
              done: false,
              value: o[i++]
            };
          },
          e: function (e) {
            throw e;
          },
          f: F
        };
      }

      throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }

    var normalCompletion = true,
        didErr = false,
        err;
    return {
      s: function () {
        it = it.call(o);
      },
      n: function () {
        var step = it.next();
        normalCompletion = step.done;
        return step;
      },
      e: function (e) {
        didErr = true;
        err = e;
      },
      f: function () {
        try {
          if (!normalCompletion && it.return != null) it.return();
        } finally {
          if (didErr) throw err;
        }
      }
    };
  }

  function s$a() {
    return {
      fns: {},
      clear: function clear() {
        this.fns = {};
      },
      emit: function emit(s) {
        for (var _len = arguments.length, f = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          f[_key - 1] = arguments[_key];
        }

        (this.fns[s] || []).map(function (s) {
          return s.apply(s, f);
        });
      },
      off: function off(s, f) {
        if (this.fns[s]) {
          var n = this.fns[s].indexOf(f);

          if (n >= 0) {
            this.fns[s].splice(n, 1);
          }
        }
      },
      on: function on(s, f) {
        (this.fns[s] = this.fns[s] || []).push(f);
      }
    };
  }

  function t$11() {
    return {
      filters: {},
      add: function add(t, e) {
        (this.filters[t] = this.filters[t] || []).push(e);
      },
      clear: function clear() {
        this.filters = {};
      },
      execute: function execute(t, e, i) {
        if (!this.filters[t] || !this.filters[t].length) {
          return e;
        }

        var s = e;
        var r = this.filters[t];
        var l = r.length;

        for (var _t = 0; _t < l; _t++) {
          s = r[_t].apply(s, i);
        }

        return s;
      },
      remove: function remove(t, e) {
        if (this.filters[t]) {
          this.filters[t] = this.filters[t].filter(function (t) {
            return t !== e;
          });
        }
      }
    };
  }

  function e$H(e, t, r, n) {
    var o = (r.getAttribute("type") || "").toLowerCase();
    var c = r.tagName.toLowerCase();

    if (c === "textarea") {
      return r.value;
    }

    if (c === "select") {
      var _e = r;
      var _t = _e.selectedIndex;
      return _t >= 0 ? _e.options.item(_t).value : "";
    }

    if (c === "input") {
      if ("radio" === o || "checkbox" === o) {
        var _e2 = n.filter(function (e) {
          return e.checked;
        }).length;
        return _e2 === 0 ? "" : _e2 + "";
      } else {
        return r.value;
      }
    }

    return "";
  }

  function r$d(r, e) {
    var t = Array.isArray(e) ? e : [e];
    var a = r;
    t.forEach(function (r) {
      a = a.replace("%s", r);
    });
    return a;
  }

  function s$9() {
    var s = function s(e) {
      return parseFloat("".concat(e).replace(",", "."));
    };

    return {
      validate: function validate(a) {
        var t = a.value;

        if (t === "") {
          return {
            valid: true
          };
        }

        var n = Object.assign({}, {
          inclusive: true,
          message: ""
        }, a.options);
        var l = s(n.min);
        var o = s(n.max);
        return n.inclusive ? {
          message: r$d(a.l10n ? n.message || a.l10n.between["default"] : n.message, ["".concat(l), "".concat(o)]),
          valid: parseFloat(t) >= l && parseFloat(t) <= o
        } : {
          message: r$d(a.l10n ? n.message || a.l10n.between.notInclusive : n.message, ["".concat(l), "".concat(o)]),
          valid: parseFloat(t) > l && parseFloat(t) < o
        };
      }
    };
  }

  function t$10() {
    return {
      validate: function validate(t) {
        return {
          valid: true
        };
      }
    };
  }

  function t$$(t, n) {
    if ("function" === typeof t) {
      return t.apply(this, n);
    } else if ("string" === typeof t) {
      var e = t;

      if ("()" === e.substring(e.length - 2)) {
        e = e.substring(0, e.length - 2);
      }

      var i = e.split(".");
      var o = i.pop();
      var f = window;

      var _iterator = _createForOfIteratorHelper(i),
          _step;

      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var _t = _step.value;
          f = f[_t];
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }

      return typeof f[o] === "undefined" ? null : f[o].apply(this, n);
    }
  }

  function o$4() {
    return {
      validate: function validate(o) {
        var l = t$$(o.options.callback, [o]);
        return "boolean" === typeof l ? {
          valid: l
        } : l;
      }
    };
  }

  function t$_() {
    return {
      validate: function validate(t) {
        var o = "select" === t.element.tagName.toLowerCase() ? t.element.querySelectorAll("option:checked").length : t.elements.filter(function (e) {
          return e.checked;
        }).length;
        var s = t.options.min ? "".concat(t.options.min) : "";
        var n = t.options.max ? "".concat(t.options.max) : "";
        var a = t.l10n ? t.options.message || t.l10n.choice["default"] : t.options.message;
        var l = !(s && o < parseInt(s, 10) || n && o > parseInt(n, 10));

        switch (true) {
          case !!s && !!n:
            a = r$d(t.l10n ? t.l10n.choice.between : t.options.message, [s, n]);
            break;

          case !!s:
            a = r$d(t.l10n ? t.l10n.choice.more : t.options.message, s);
            break;

          case !!n:
            a = r$d(t.l10n ? t.l10n.choice.less : t.options.message, n);
            break;
        }

        return {
          message: a,
          valid: l
        };
      }
    };
  }

  var t$Z = {
    AMERICAN_EXPRESS: {
      length: [15],
      prefix: ["34", "37"]
    },
    DANKORT: {
      length: [16],
      prefix: ["5019"]
    },
    DINERS_CLUB: {
      length: [14],
      prefix: ["300", "301", "302", "303", "304", "305", "36"]
    },
    DINERS_CLUB_US: {
      length: [16],
      prefix: ["54", "55"]
    },
    DISCOVER: {
      length: [16],
      prefix: ["6011", "622126", "622127", "622128", "622129", "62213", "62214", "62215", "62216", "62217", "62218", "62219", "6222", "6223", "6224", "6225", "6226", "6227", "6228", "62290", "62291", "622920", "622921", "622922", "622923", "622924", "622925", "644", "645", "646", "647", "648", "649", "65"]
    },
    ELO: {
      length: [16],
      prefix: ["4011", "4312", "4389", "4514", "4573", "4576", "5041", "5066", "5067", "509", "6277", "6362", "6363", "650", "6516", "6550"]
    },
    FORBRUGSFORENINGEN: {
      length: [16],
      prefix: ["600722"]
    },
    JCB: {
      length: [16],
      prefix: ["3528", "3529", "353", "354", "355", "356", "357", "358"]
    },
    LASER: {
      length: [16, 17, 18, 19],
      prefix: ["6304", "6706", "6771", "6709"]
    },
    MAESTRO: {
      length: [12, 13, 14, 15, 16, 17, 18, 19],
      prefix: ["5018", "5020", "5038", "5868", "6304", "6759", "6761", "6762", "6763", "6764", "6765", "6766"]
    },
    MASTERCARD: {
      length: [16],
      prefix: ["51", "52", "53", "54", "55"]
    },
    SOLO: {
      length: [16, 18, 19],
      prefix: ["6334", "6767"]
    },
    UNIONPAY: {
      length: [16, 17, 18, 19],
      prefix: ["622126", "622127", "622128", "622129", "62213", "62214", "62215", "62216", "62217", "62218", "62219", "6222", "6223", "6224", "6225", "6226", "6227", "6228", "62290", "62291", "622920", "622921", "622922", "622923", "622924", "622925"]
    },
    VISA: {
      length: [16],
      prefix: ["4"]
    },
    VISA_ELECTRON: {
      length: [16],
      prefix: ["4026", "417500", "4405", "4508", "4844", "4913", "4917"]
    }
  };
  function l$2() {
    return {
      validate: function validate(l) {
        if (l.value === "") {
          return {
            meta: {
              type: null
            },
            valid: true
          };
        }

        if (/[^0-9-\s]+/.test(l.value)) {
          return {
            meta: {
              type: null
            },
            valid: false
          };
        }

        var r = l.value.replace(/\D/g, "");

        if (!t$15(r)) {
          return {
            meta: {
              type: null
            },
            valid: false
          };
        }

        for (var _i = 0, _Object$keys = Object.keys(t$Z); _i < _Object$keys.length; _i++) {
          var _e = _Object$keys[_i];

          for (var n in t$Z[_e].prefix) {
            if (l.value.substr(0, t$Z[_e].prefix[n].length) === t$Z[_e].prefix[n] && t$Z[_e].length.indexOf(r.length) !== -1) {
              return {
                meta: {
                  type: _e
                },
                valid: true
              };
            }
          }
        }

        return {
          meta: {
            type: null
          },
          valid: false
        };
      }
    };
  }

  function t$Y(t, e, n, r) {
    if (isNaN(t) || isNaN(e) || isNaN(n)) {
      return false;
    }

    if (t < 1e3 || t > 9999 || e <= 0 || e > 12) {
      return false;
    }

    var s = [31, t % 400 === 0 || t % 100 !== 0 && t % 4 === 0 ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    if (n <= 0 || n > s[e - 1]) {
      return false;
    }

    if (r === true) {
      var _r = new Date();

      var _s = _r.getFullYear();

      var a = _r.getMonth();

      var u = _r.getDate();

      return t < _s || t === _s && e - 1 < a || t === _s && e - 1 === a && n < u;
    }

    return true;
  }

  function n$1() {
    var n = function n(t, e, _n) {
      var s = e.indexOf("YYYY");
      var a = e.indexOf("MM");
      var l = e.indexOf("DD");

      if (s === -1 || a === -1 || l === -1) {
        return null;
      }

      var o = t.split(" ");
      var r = o[0].split(_n);

      if (r.length < 3) {
        return null;
      }

      var c = new Date(parseInt(r[s], 10), parseInt(r[a], 10) - 1, parseInt(r[l], 10));

      if (o.length > 1) {
        var _t = o[1].split(":");

        c.setHours(_t.length > 0 ? parseInt(_t[0], 10) : 0);
        c.setMinutes(_t.length > 1 ? parseInt(_t[1], 10) : 0);
        c.setSeconds(_t.length > 2 ? parseInt(_t[2], 10) : 0);
      }

      return c;
    };

    var s = function s(t, e) {
      var n = e.replace(/Y/g, "y").replace(/M/g, "m").replace(/D/g, "d").replace(/:m/g, ":M").replace(/:mm/g, ":MM").replace(/:S/, ":s").replace(/:SS/, ":ss");
      var s = t.getDate();
      var a = s < 10 ? "0".concat(s) : s;
      var l = t.getMonth() + 1;
      var o = l < 10 ? "0".concat(l) : l;
      var r = "".concat(t.getFullYear()).substr(2);
      var c = t.getFullYear();
      var i = t.getHours() % 12 || 12;
      var g = i < 10 ? "0".concat(i) : i;
      var u = t.getHours();
      var m = u < 10 ? "0".concat(u) : u;
      var d = t.getMinutes();
      var f = d < 10 ? "0".concat(d) : d;
      var p = t.getSeconds();
      var h = p < 10 ? "0".concat(p) : p;
      var $ = {
        H: "".concat(u),
        HH: "".concat(m),
        M: "".concat(d),
        MM: "".concat(f),
        d: "".concat(s),
        dd: "".concat(a),
        h: "".concat(i),
        hh: "".concat(g),
        m: "".concat(l),
        mm: "".concat(o),
        s: "".concat(p),
        ss: "".concat(h),
        yy: "".concat(r),
        yyyy: "".concat(c)
      };
      return n.replace(/d{1,4}|m{1,4}|yy(?:yy)?|([HhMs])\1?|"[^"]*"|'[^']*'/g, function (t) {
        return $[t] ? $[t] : t.slice(1, t.length - 1);
      });
    };

    return {
      validate: function validate(a) {
        if (a.value === "") {
          return {
            meta: {
              date: null
            },
            valid: true
          };
        }

        var l = Object.assign({}, {
          format: a.element && a.element.getAttribute("type") === "date" ? "YYYY-MM-DD" : "MM/DD/YYYY",
          message: ""
        }, a.options);
        var o = a.l10n ? a.l10n.date["default"] : l.message;
        var r = {
          message: "".concat(o),
          meta: {
            date: null
          },
          valid: false
        };
        var c = l.format.split(" ");
        var i = c.length > 1 ? c[1] : null;
        var g = c.length > 2 ? c[2] : null;
        var u = a.value.split(" ");
        var m = u[0];
        var d = u.length > 1 ? u[1] : null;

        if (c.length !== u.length) {
          return r;
        }

        var f = l.separator || (m.indexOf("/") !== -1 ? "/" : m.indexOf("-") !== -1 ? "-" : m.indexOf(".") !== -1 ? "." : "/");

        if (f === null || m.indexOf(f) === -1) {
          return r;
        }

        var p = m.split(f);
        var h = c[0].split(f);

        if (p.length !== h.length) {
          return r;
        }

        var $ = p[h.indexOf("YYYY")];
        var M = p[h.indexOf("MM")];
        var Y = p[h.indexOf("DD")];

        if (!/^\d+$/.test($) || !/^\d+$/.test(M) || !/^\d+$/.test(Y) || $.length > 4 || M.length > 2 || Y.length > 2) {
          return r;
        }

        var D = parseInt($, 10);
        var x = parseInt(M, 10);
        var y = parseInt(Y, 10);

        if (!t$Y(D, x, y)) {
          return r;
        }

        var I = new Date(D, x - 1, y);

        if (i) {
          var _t2 = d.split(":");

          if (i.split(":").length !== _t2.length) {
            return r;
          }

          var _e = _t2.length > 0 ? _t2[0].length <= 2 && /^\d+$/.test(_t2[0]) ? parseInt(_t2[0], 10) : -1 : 0;

          var _n2 = _t2.length > 1 ? _t2[1].length <= 2 && /^\d+$/.test(_t2[1]) ? parseInt(_t2[1], 10) : -1 : 0;

          var _s = _t2.length > 2 ? _t2[2].length <= 2 && /^\d+$/.test(_t2[2]) ? parseInt(_t2[2], 10) : -1 : 0;

          if (_e === -1 || _n2 === -1 || _s === -1) {
            return r;
          }

          if (_s < 0 || _s > 60) {
            return r;
          }

          if (_e < 0 || _e >= 24 || g && _e > 12) {
            return r;
          }

          if (_n2 < 0 || _n2 > 59) {
            return r;
          }

          I.setHours(_e);
          I.setMinutes(_n2);
          I.setSeconds(_s);
        }

        var O = typeof l.min === "function" ? l.min() : l.min;
        var v = O instanceof Date ? O : O ? n(O, h, f) : I;
        var H = typeof l.max === "function" ? l.max() : l.max;
        var T = H instanceof Date ? H : H ? n(H, h, f) : I;
        var S = O instanceof Date ? s(v, l.format) : O;
        var b = H instanceof Date ? s(T, l.format) : H;

        switch (true) {
          case !!S && !b:
            return {
              message: r$d(a.l10n ? a.l10n.date.min : o, S),
              meta: {
                date: I
              },
              valid: I.getTime() >= v.getTime()
            };

          case !!b && !S:
            return {
              message: r$d(a.l10n ? a.l10n.date.max : o, b),
              meta: {
                date: I
              },
              valid: I.getTime() <= T.getTime()
            };

          case !!b && !!S:
            return {
              message: r$d(a.l10n ? a.l10n.date.range : o, [S, b]),
              meta: {
                date: I
              },
              valid: I.getTime() <= T.getTime() && I.getTime() >= v.getTime()
            };

          default:
            return {
              message: "".concat(o),
              meta: {
                date: I
              },
              valid: true
            };
        }
      }
    };
  }

  function o$3() {
    return {
      validate: function validate(o) {
        var t = "function" === typeof o.options.compare ? o.options.compare.call(this) : o.options.compare;
        return {
          valid: t === "" || o.value !== t
        };
      }
    };
  }

  function e$G() {
    return {
      validate: function validate(e) {
        return {
          valid: e.value === "" || /^\d+$/.test(e.value)
        };
      }
    };
  }

  function t$X() {
    var t = function t(_t3, e) {
      var s = _t3.split(/"/);

      var l = s.length;
      var n = [];
      var r = "";

      for (var _t = 0; _t < l; _t++) {
        if (_t % 2 === 0) {
          var _l = s[_t].split(e);

          var a = _l.length;

          if (a === 1) {
            r += _l[0];
          } else {
            n.push(r + _l[0]);

            for (var _t2 = 1; _t2 < a - 1; _t2++) {
              n.push(_l[_t2]);
            }

            r = _l[a - 1];
          }
        } else {
          r += '"' + s[_t];

          if (_t < l - 1) {
            r += '"';
          }
        }
      }

      n.push(r);
      return n;
    };

    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        var s = Object.assign({}, {
          multiple: false,
          separator: /[,;]/
        }, e.options);
        var l = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
        var n = s.multiple === true || "".concat(s.multiple) === "true";

        if (n) {
          var _n = s.separator || /[,;]/;

          var r = t(e.value, _n);
          var a = r.length;

          for (var _t4 = 0; _t4 < a; _t4++) {
            if (!l.test(r[_t4])) {
              return {
                valid: false
              };
            }
          }

          return {
            valid: true
          };
        } else {
          return {
            valid: l.test(e.value)
          };
        }
      }
    };
  }

  function e$F() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        var t;
        var i = e.options.extension ? e.options.extension.toLowerCase().split(",") : null;
        var s = e.options.type ? e.options.type.toLowerCase().split(",") : null;
        var n = window["File"] && window["FileList"] && window["FileReader"];

        if (n) {
          var _n = e.element.files;
          var o = _n.length;
          var a = 0;

          if (e.options.maxFiles && o > parseInt("".concat(e.options.maxFiles), 10)) {
            return {
              meta: {
                error: "INVALID_MAX_FILES"
              },
              valid: false
            };
          }

          if (e.options.minFiles && o < parseInt("".concat(e.options.minFiles), 10)) {
            return {
              meta: {
                error: "INVALID_MIN_FILES"
              },
              valid: false
            };
          }

          var r = {};

          for (var l = 0; l < o; l++) {
            a += _n[l].size;
            t = _n[l].name.substr(_n[l].name.lastIndexOf(".") + 1);
            r = {
              ext: t,
              file: _n[l],
              size: _n[l].size,
              type: _n[l].type
            };

            if (e.options.minSize && _n[l].size < parseInt("".concat(e.options.minSize), 10)) {
              return {
                meta: Object.assign({}, {
                  error: "INVALID_MIN_SIZE"
                }, r),
                valid: false
              };
            }

            if (e.options.maxSize && _n[l].size > parseInt("".concat(e.options.maxSize), 10)) {
              return {
                meta: Object.assign({}, {
                  error: "INVALID_MAX_SIZE"
                }, r),
                valid: false
              };
            }

            if (i && i.indexOf(t.toLowerCase()) === -1) {
              return {
                meta: Object.assign({}, {
                  error: "INVALID_EXTENSION"
                }, r),
                valid: false
              };
            }

            if (_n[l].type && s && s.indexOf(_n[l].type.toLowerCase()) === -1) {
              return {
                meta: Object.assign({}, {
                  error: "INVALID_TYPE"
                }, r),
                valid: false
              };
            }
          }

          if (e.options.maxTotalSize && a > parseInt("".concat(e.options.maxTotalSize), 10)) {
            return {
              meta: Object.assign({}, {
                error: "INVALID_MAX_TOTAL_SIZE",
                totalSize: a
              }, r),
              valid: false
            };
          }

          if (e.options.minTotalSize && a < parseInt("".concat(e.options.minTotalSize), 10)) {
            return {
              meta: Object.assign({}, {
                error: "INVALID_MIN_TOTAL_SIZE",
                totalSize: a
              }, r),
              valid: false
            };
          }
        } else {
          t = e.value.substr(e.value.lastIndexOf(".") + 1);

          if (i && i.indexOf(t.toLowerCase()) === -1) {
            return {
              meta: {
                error: "INVALID_EXTENSION",
                ext: t
              },
              valid: false
            };
          }
        }

        return {
          valid: true
        };
      }
    };
  }

  function a$7() {
    return {
      validate: function validate(a) {
        if (a.value === "") {
          return {
            valid: true
          };
        }

        var s = Object.assign({}, {
          inclusive: true,
          message: ""
        }, a.options);
        var t = parseFloat("".concat(s.min).replace(",", "."));
        return s.inclusive ? {
          message: r$d(a.l10n ? s.message || a.l10n.greaterThan["default"] : s.message, "".concat(t)),
          valid: parseFloat(a.value) >= t
        } : {
          message: r$d(a.l10n ? s.message || a.l10n.greaterThan.notInclusive : s.message, "".concat(t)),
          valid: parseFloat(a.value) > t
        };
      }
    };
  }

  function o$2() {
    return {
      validate: function validate(o) {
        var t = "function" === typeof o.options.compare ? o.options.compare.call(this) : o.options.compare;
        return {
          valid: t === "" || o.value === t
        };
      }
    };
  }

  function a$6() {
    return {
      validate: function validate(a) {
        if (a.value === "") {
          return {
            valid: true
          };
        }

        var e = Object.assign({}, {
          decimalSeparator: ".",
          thousandsSeparator: ""
        }, a.options);
        var t = e.decimalSeparator === "." ? "\\." : e.decimalSeparator;
        var r = e.thousandsSeparator === "." ? "\\." : e.thousandsSeparator;
        var o = new RegExp("^-?[0-9]{1,3}(".concat(r, "[0-9]{3})*(").concat(t, "[0-9]+)?$"));
        var n = new RegExp(r, "g");
        var s = "".concat(a.value);

        if (!o.test(s)) {
          return {
            valid: false
          };
        }

        if (r) {
          s = s.replace(n, "");
        }

        if (t) {
          s = s.replace(t, ".");
        }

        var i = parseFloat(s);
        return {
          valid: !isNaN(i) && isFinite(i) && Math.floor(i) === i
        };
      }
    };
  }

  function d() {
    return {
      validate: function validate(d) {
        if (d.value === "") {
          return {
            valid: true
          };
        }

        var a = Object.assign({}, {
          ipv4: true,
          ipv6: true
        }, d.options);
        var e = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\/([0-9]|[1-2][0-9]|3[0-2]))?$/;
        var s = /^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*(\/(\d|\d\d|1[0-1]\d|12[0-8]))?$/;

        switch (true) {
          case a.ipv4 && !a.ipv6:
            return {
              message: d.l10n ? a.message || d.l10n.ip.ipv4 : a.message,
              valid: e.test(d.value)
            };

          case !a.ipv4 && a.ipv6:
            return {
              message: d.l10n ? a.message || d.l10n.ip.ipv6 : a.message,
              valid: s.test(d.value)
            };

          case a.ipv4 && a.ipv6:
          default:
            return {
              message: d.l10n ? a.message || d.l10n.ip["default"] : a.message,
              valid: e.test(d.value) || s.test(d.value)
            };
        }
      }
    };
  }

  function s$8() {
    return {
      validate: function validate(s) {
        if (s.value === "") {
          return {
            valid: true
          };
        }

        var a = Object.assign({}, {
          inclusive: true,
          message: ""
        }, s.options);
        var l = parseFloat("".concat(a.max).replace(",", "."));
        return a.inclusive ? {
          message: r$d(s.l10n ? a.message || s.l10n.lessThan["default"] : a.message, "".concat(l)),
          valid: parseFloat(s.value) <= l
        } : {
          message: r$d(s.l10n ? a.message || s.l10n.lessThan.notInclusive : a.message, "".concat(l)),
          valid: parseFloat(s.value) < l
        };
      }
    };
  }

  function t$W() {
    return {
      validate: function validate(t) {
        var n = !!t.options && !!t.options.trim;
        var o = t.value;
        return {
          valid: !n && o !== "" || n && o !== "" && o.trim() !== ""
        };
      }
    };
  }

  function a$5() {
    return {
      validate: function validate(a) {
        if (a.value === "") {
          return {
            valid: true
          };
        }

        var e = Object.assign({}, {
          decimalSeparator: ".",
          thousandsSeparator: ""
        }, a.options);
        var t = "".concat(a.value);

        if (t.substr(0, 1) === e.decimalSeparator) {
          t = "0".concat(e.decimalSeparator).concat(t.substr(1));
        } else if (t.substr(0, 2) === "-".concat(e.decimalSeparator)) {
          t = "-0".concat(e.decimalSeparator).concat(t.substr(2));
        }

        var r = e.decimalSeparator === "." ? "\\." : e.decimalSeparator;
        var s = e.thousandsSeparator === "." ? "\\." : e.thousandsSeparator;
        var i = new RegExp("^-?[0-9]{1,3}(".concat(s, "[0-9]{3})*(").concat(r, "[0-9]+)?$"));
        var o = new RegExp(s, "g");

        if (!i.test(t)) {
          return {
            valid: false
          };
        }

        if (s) {
          t = t.replace(o, "");
        }

        if (r) {
          t = t.replace(r, ".");
        }

        var l = parseFloat(t);
        return {
          valid: !isNaN(l) && isFinite(l)
        };
      }
    };
  }

  function r$c() {
    return {
      validate: function validate(r) {
        return t$$(r.options.promise, [r]);
      }
    };
  }

  function e$E() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        var t = e.options.regexp;

        if (t instanceof RegExp) {
          return {
            valid: t.test(e.value)
          };
        } else {
          var n = t.toString();
          var o = e.options.flags ? new RegExp(n, e.options.flags) : new RegExp(n);
          return {
            valid: o.test(e.value)
          };
        }
      }
    };
  }

  var e$D = function e(_e) {
    return Array(_e).fill("").map(function (e) {
      return Math.random().toString(36).charAt(2);
    }).join("");
  };

  function t$V(t, n) {
    var o = function o(e) {
      return Object.keys(e).map(function (t) {
        return "".concat(encodeURIComponent(t), "=").concat(encodeURIComponent(e[t]));
      }).join("&");
    };

    return new Promise(function (s, a) {
      var r = Object.assign({}, {
        crossDomain: false,
        headers: {},
        method: "GET",
        params: {}
      }, n);
      var d = Object.keys(r.params).map(function (e) {
        return "".concat(encodeURIComponent(e), "=").concat(encodeURIComponent(r.params[e]));
      }).join("&");
      var c = t.indexOf("?");
      var i = "GET" === r.method ? "".concat(t).concat(c ? "?" : "&").concat(d) : t;

      if (r.crossDomain) {
        var _t = document.createElement("script");

        var _n = "___FormValidationFetch_".concat(e$D(12), "___");

        window[_n] = function (e) {
          delete window[_n];
          s(e);
        };

        _t.src = "".concat(i).concat(c ? "&" : "?", "callback=").concat(_n);
        _t.async = true;

        _t.addEventListener("load", function () {
          _t.parentNode.removeChild(_t);
        });

        _t.addEventListener("error", function () {
          return a;
        });

        document.head.appendChild(_t);
      } else {
        var _e2 = new XMLHttpRequest();

        _e2.open(r.method, i);

        _e2.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        if ("POST" === r.method) {
          _e2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        }

        Object.keys(r.headers).forEach(function (t) {
          return _e2.setRequestHeader(t, r.headers[t]);
        });

        _e2.addEventListener("load", function () {
          s(JSON.parse(this.responseText));
        });

        _e2.addEventListener("error", function () {
          return a;
        });

        _e2.send(o(r.params));
      }
    });
  }

  function a$4() {
    var a = {
      crossDomain: false,
      data: {},
      headers: {},
      method: "GET",
      validKey: "valid"
    };
    return {
      validate: function validate(t) {
        if (t.value === "") {
          return Promise.resolve({
            valid: true
          });
        }

        var s = Object.assign({}, a, t.options);
        var r = s.data;

        if ("function" === typeof s.data) {
          r = s.data.call(this, t);
        }

        if ("string" === typeof r) {
          r = JSON.parse(r);
        }

        r[s.name || t.field] = t.value;
        var o = "function" === typeof s.url ? s.url.call(this, t) : s.url;
        return t$V(o, {
          crossDomain: s.crossDomain,
          headers: s.headers,
          method: s.method,
          params: r
        }).then(function (e) {
          return Promise.resolve({
            message: e["message"],
            meta: e,
            valid: "".concat(e[s.validKey]) === "true"
          });
        })["catch"](function (e) {
          return Promise.reject({
            valid: false
          });
        });
      }
    };
  }

  function e$C() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        var a = Object.assign({}, {
          "case": "lower"
        }, e.options);
        var s = (a["case"] || "lower").toLowerCase();
        return {
          message: a.message || (e.l10n ? "upper" === s ? e.l10n.stringCase.upper : e.l10n.stringCase["default"] : a.message),
          valid: "upper" === s ? e.value === e.value.toUpperCase() : e.value === e.value.toLowerCase()
        };
      }
    };
  }

  function t$U() {
    var t = function t(e) {
      var t = e.length;

      for (var s = e.length - 1; s >= 0; s--) {
        var n = e.charCodeAt(s);

        if (n > 127 && n <= 2047) {
          t++;
        } else if (n > 2047 && n <= 65535) {
          t += 2;
        }

        if (n >= 56320 && n <= 57343) {
          s--;
        }
      }

      return "".concat(t);
    };

    return {
      validate: function validate(s) {
        var n = Object.assign({}, {
          message: "",
          trim: false,
          utf8Bytes: false
        }, s.options);
        var a = n.trim === true || "".concat(n.trim) === "true" ? s.value.trim() : s.value;

        if (a === "") {
          return {
            valid: true
          };
        }

        var r = n.min ? "".concat(n.min) : "";
        var l = n.max ? "".concat(n.max) : "";
        var i = n.utf8Bytes ? t(a) : a.length;
        var g = true;
        var m = s.l10n ? n.message || s.l10n.stringLength["default"] : n.message;

        if (r && i < parseInt(r, 10) || l && i > parseInt(l, 10)) {
          g = false;
        }

        switch (true) {
          case !!r && !!l:
            m = r$d(s.l10n ? n.message || s.l10n.stringLength.between : n.message, [r, l]);
            break;

          case !!r:
            m = r$d(s.l10n ? n.message || s.l10n.stringLength.more : n.message, "".concat(parseInt(r, 10)));
            break;

          case !!l:
            m = r$d(s.l10n ? n.message || s.l10n.stringLength.less : n.message, "".concat(parseInt(l, 10)));
            break;
        }

        return {
          message: m,
          valid: g
        };
      }
    };
  }

  function t$T() {
    var t = {
      allowEmptyProtocol: false,
      allowLocal: false,
      protocol: "http, https, ftp"
    };
    return {
      validate: function validate(o) {
        if (o.value === "") {
          return {
            valid: true
          };
        }

        var a = Object.assign({}, t, o.options);
        var l = a.allowLocal === true || "".concat(a.allowLocal) === "true";
        var f = a.allowEmptyProtocol === true || "".concat(a.allowEmptyProtocol) === "true";
        var u = a.protocol.split(",").join("|").replace(/\s/g, "");
        var e = new RegExp("^" + "(?:(?:" + u + ")://)" + (f ? "?" : "") + "(?:\\S+(?::\\S*)?@)?" + "(?:" + (l ? "" : "(?!(?:10|127)(?:\\.\\d{1,3}){3})" + "(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})" + "(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})") + "(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])" + "(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}" + "(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))" + "|" + "(?:(?:[a-z\\u00a1-\\uffff0-9]-?)*[a-z\\u00a1-\\uffff0-9]+)" + "(?:\\.(?:[a-z\\u00a1-\\uffff0-9]-?)*[a-z\\u00a1-\\uffff0-9])*" + "(?:\\.(?:[a-z\\u00a1-\\uffff]{2,}))" + (l ? "?" : "") + ")" + "(?::\\d{2,5})?" + "(?:/[^\\s]*)?$", "i");
        return {
          valid: e.test(o.value)
        };
      }
    };
  }

  function a$3() {
    return {
      validate: function validate(a) {
        return {
          valid: a.value === "" || /^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{4})$/.test(a.value)
        };
      }
    };
  }

  function a$2() {
    return {
      validate: function validate(a) {
        return {
          valid: a.value === "" || /^[a-zA-Z]{6}[a-zA-Z0-9]{2}([a-zA-Z0-9]{3})?$/.test(a.value)
        };
      }
    };
  }

  function e$B() {
    var e = ["hex", "rgb", "rgba", "hsl", "hsla", "keyword"];
    var a = ["aliceblue", "antiquewhite", "aqua", "aquamarine", "azure", "beige", "bisque", "black", "blanchedalmond", "blue", "blueviolet", "brown", "burlywood", "cadetblue", "chartreuse", "chocolate", "coral", "cornflowerblue", "cornsilk", "crimson", "cyan", "darkblue", "darkcyan", "darkgoldenrod", "darkgray", "darkgreen", "darkgrey", "darkkhaki", "darkmagenta", "darkolivegreen", "darkorange", "darkorchid", "darkred", "darksalmon", "darkseagreen", "darkslateblue", "darkslategray", "darkslategrey", "darkturquoise", "darkviolet", "deeppink", "deepskyblue", "dimgray", "dimgrey", "dodgerblue", "firebrick", "floralwhite", "forestgreen", "fuchsia", "gainsboro", "ghostwhite", "gold", "goldenrod", "gray", "green", "greenyellow", "grey", "honeydew", "hotpink", "indianred", "indigo", "ivory", "khaki", "lavender", "lavenderblush", "lawngreen", "lemonchiffon", "lightblue", "lightcoral", "lightcyan", "lightgoldenrodyellow", "lightgray", "lightgreen", "lightgrey", "lightpink", "lightsalmon", "lightseagreen", "lightskyblue", "lightslategray", "lightslategrey", "lightsteelblue", "lightyellow", "lime", "limegreen", "linen", "magenta", "maroon", "mediumaquamarine", "mediumblue", "mediumorchid", "mediumpurple", "mediumseagreen", "mediumslateblue", "mediumspringgreen", "mediumturquoise", "mediumvioletred", "midnightblue", "mintcream", "mistyrose", "moccasin", "navajowhite", "navy", "oldlace", "olive", "olivedrab", "orange", "orangered", "orchid", "palegoldenrod", "palegreen", "paleturquoise", "palevioletred", "papayawhip", "peachpuff", "peru", "pink", "plum", "powderblue", "purple", "red", "rosybrown", "royalblue", "saddlebrown", "salmon", "sandybrown", "seagreen", "seashell", "sienna", "silver", "skyblue", "slateblue", "slategray", "slategrey", "snow", "springgreen", "steelblue", "tan", "teal", "thistle", "tomato", "transparent", "turquoise", "violet", "wheat", "white", "whitesmoke", "yellow", "yellowgreen"];

    var r = function r(e) {
      return /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(e);
    };

    var l = function l(e) {
      return /^hsl\((\s*(-?\d+)\s*,)(\s*(\b(0?\d{1,2}|100)\b%)\s*,)(\s*(\b(0?\d{1,2}|100)\b%)\s*)\)$/.test(e);
    };

    var s = function s(e) {
      return /^hsla\((\s*(-?\d+)\s*,)(\s*(\b(0?\d{1,2}|100)\b%)\s*,){2}(\s*(0?(\.\d+)?|1(\.0+)?)\s*)\)$/.test(e);
    };

    var t = function t(e) {
      return a.indexOf(e) >= 0;
    };

    var i = function i(e) {
      return /^rgb\((\s*(\b([01]?\d{1,2}|2[0-4]\d|25[0-5])\b)\s*,){2}(\s*(\b([01]?\d{1,2}|2[0-4]\d|25[0-5])\b)\s*)\)$/.test(e) || /^rgb\((\s*(\b(0?\d{1,2}|100)\b%)\s*,){2}(\s*(\b(0?\d{1,2}|100)\b%)\s*)\)$/.test(e);
    };

    var o = function o(e) {
      return /^rgba\((\s*(\b([01]?\d{1,2}|2[0-4]\d|25[0-5])\b)\s*,){3}(\s*(0?(\.\d+)?|1(\.0+)?)\s*)\)$/.test(e) || /^rgba\((\s*(\b(0?\d{1,2}|100)\b%)\s*,){3}(\s*(0?(\.\d+)?|1(\.0+)?)\s*)\)$/.test(e);
    };

    return {
      validate: function validate(a) {
        if (a.value === "") {
          return {
            valid: true
          };
        }

        var n = typeof a.options.type === "string" ? a.options.type.toString().replace(/s/g, "").split(",") : a.options.type || e;

        var _iterator = _createForOfIteratorHelper(n),
            _step;

        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var d = _step.value;

            var _n = d.toLowerCase();

            if (e.indexOf(_n) === -1) {
              continue;
            }

            var g = true;

            switch (_n) {
              case "hex":
                g = r(a.value);
                break;

              case "hsl":
                g = l(a.value);
                break;

              case "hsla":
                g = s(a.value);
                break;

              case "keyword":
                g = t(a.value);
                break;

              case "rgb":
                g = i(a.value);
                break;

              case "rgba":
                g = o(a.value);
                break;
            }

            if (g) {
              return {
                valid: true
              };
            }
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }

        return {
          valid: false
        };
      }
    };
  }

  function t$S() {
    return {
      validate: function validate(t) {
        if (t.value === "") {
          return {
            valid: true
          };
        }

        var e = t.value.toUpperCase();

        if (!/^[0123456789ABCDEFGHJKLMNPQRSTUVWXYZ*@#]{9}$/.test(e)) {
          return {
            valid: false
          };
        }

        var r = e.split("");
        var a = r.pop();
        var n = r.map(function (t) {
          var e = t.charCodeAt(0);

          switch (true) {
            case t === "*":
              return 36;

            case t === "@":
              return 37;

            case t === "#":
              return 38;

            case e >= "A".charCodeAt(0) && e <= "Z".charCodeAt(0):
              return e - "A".charCodeAt(0) + 10;

            default:
              return parseInt(t, 10);
          }
        });
        var c = n.map(function (t, e) {
          var r = e % 2 === 0 ? t : 2 * t;
          return Math.floor(r / 10) + r % 10;
        }).reduce(function (t, e) {
          return t + e;
        }, 0);
        var o = (10 - c % 10) % 10;
        return {
          valid: a === "".concat(o)
        };
      }
    };
  }

  function e$A() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        if (!/^(\d{8}|\d{12}|\d{13}|\d{14})$/.test(e.value)) {
          return {
            valid: false
          };
        }

        var t = e.value.length;
        var a = 0;
        var l = t === 8 ? [3, 1] : [1, 3];

        for (var r = 0; r < t - 1; r++) {
          a += parseInt(e.value.charAt(r), 10) * l[r % 2];
        }

        a = (10 - a % 10) % 10;
        return {
          valid: "".concat(a) === e.value.charAt(t - 1)
        };
      }
    };
  }

  function e$z() {
    var e = {
      ANDOVER: ["10", "12"],
      ATLANTA: ["60", "67"],
      AUSTIN: ["50", "53"],
      BROOKHAVEN: ["01", "02", "03", "04", "05", "06", "11", "13", "14", "16", "21", "22", "23", "25", "34", "51", "52", "54", "55", "56", "57", "58", "59", "65"],
      CINCINNATI: ["30", "32", "35", "36", "37", "38", "61"],
      FRESNO: ["15", "24"],
      INTERNET: ["20", "26", "27", "45", "46", "47"],
      KANSAS_CITY: ["40", "44"],
      MEMPHIS: ["94", "95"],
      OGDEN: ["80", "90"],
      PHILADELPHIA: ["33", "39", "41", "42", "43", "48", "62", "63", "64", "66", "68", "71", "72", "73", "74", "75", "76", "77", "81", "82", "83", "84", "85", "86", "87", "88", "91", "92", "93", "98", "99"],
      SMALL_BUSINESS_ADMINISTRATION: ["31"]
    };
    return {
      validate: function validate(t) {
        if (t.value === "") {
          return {
            meta: null,
            valid: true
          };
        }

        if (!/^[0-9]{2}-?[0-9]{7}$/.test(t.value)) {
          return {
            meta: null,
            valid: false
          };
        }

        var a = "".concat(t.value.substr(0, 2));

        for (var _t in e) {
          if (e[_t].indexOf(a) !== -1) {
            return {
              meta: {
                campus: _t
              },
              valid: true
            };
          }
        }

        return {
          meta: null,
          valid: false
        };
      }
    };
  }

  function r$b() {
    return {
      validate: function validate(r) {
        if (r.value === "") {
          return {
            valid: true
          };
        }

        var t = r.value.toUpperCase();

        if (!/^[GRID:]*([0-9A-Z]{2})[-\s]*([0-9A-Z]{5})[-\s]*([0-9A-Z]{10})[-\s]*([0-9A-Z]{1})$/g.test(t)) {
          return {
            valid: false
          };
        }

        t = t.replace(/\s/g, "").replace(/-/g, "");

        if ("GRID:" === t.substr(0, 5)) {
          t = t.substr(5);
        }

        return {
          valid: t$13(t)
        };
      }
    };
  }

  function e$y() {
    return {
      validate: function validate(e) {
        return {
          valid: e.value === "" || /^[0-9a-fA-F]+$/.test(e.value)
        };
      }
    };
  }

  function Z() {
    var Z = {
      AD: "AD[0-9]{2}[0-9]{4}[0-9]{4}[A-Z0-9]{12}",
      AE: "AE[0-9]{2}[0-9]{3}[0-9]{16}",
      AL: "AL[0-9]{2}[0-9]{8}[A-Z0-9]{16}",
      AO: "AO[0-9]{2}[0-9]{21}",
      AT: "AT[0-9]{2}[0-9]{5}[0-9]{11}",
      AZ: "AZ[0-9]{2}[A-Z]{4}[A-Z0-9]{20}",
      BA: "BA[0-9]{2}[0-9]{3}[0-9]{3}[0-9]{8}[0-9]{2}",
      BE: "BE[0-9]{2}[0-9]{3}[0-9]{7}[0-9]{2}",
      BF: "BF[0-9]{2}[0-9]{23}",
      BG: "BG[0-9]{2}[A-Z]{4}[0-9]{4}[0-9]{2}[A-Z0-9]{8}",
      BH: "BH[0-9]{2}[A-Z]{4}[A-Z0-9]{14}",
      BI: "BI[0-9]{2}[0-9]{12}",
      BJ: "BJ[0-9]{2}[A-Z]{1}[0-9]{23}",
      BR: "BR[0-9]{2}[0-9]{8}[0-9]{5}[0-9]{10}[A-Z][A-Z0-9]",
      CH: "CH[0-9]{2}[0-9]{5}[A-Z0-9]{12}",
      CI: "CI[0-9]{2}[A-Z]{1}[0-9]{23}",
      CM: "CM[0-9]{2}[0-9]{23}",
      CR: "CR[0-9]{2}[0-9][0-9]{3}[0-9]{14}",
      CV: "CV[0-9]{2}[0-9]{21}",
      CY: "CY[0-9]{2}[0-9]{3}[0-9]{5}[A-Z0-9]{16}",
      CZ: "CZ[0-9]{2}[0-9]{20}",
      DE: "DE[0-9]{2}[0-9]{8}[0-9]{10}",
      DK: "DK[0-9]{2}[0-9]{14}",
      DO: "DO[0-9]{2}[A-Z0-9]{4}[0-9]{20}",
      DZ: "DZ[0-9]{2}[0-9]{20}",
      EE: "EE[0-9]{2}[0-9]{2}[0-9]{2}[0-9]{11}[0-9]{1}",
      ES: "ES[0-9]{2}[0-9]{4}[0-9]{4}[0-9]{1}[0-9]{1}[0-9]{10}",
      FI: "FI[0-9]{2}[0-9]{6}[0-9]{7}[0-9]{1}",
      FO: "FO[0-9]{2}[0-9]{4}[0-9]{9}[0-9]{1}",
      FR: "FR[0-9]{2}[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}",
      GB: "GB[0-9]{2}[A-Z]{4}[0-9]{6}[0-9]{8}",
      GE: "GE[0-9]{2}[A-Z]{2}[0-9]{16}",
      GI: "GI[0-9]{2}[A-Z]{4}[A-Z0-9]{15}",
      GL: "GL[0-9]{2}[0-9]{4}[0-9]{9}[0-9]{1}",
      GR: "GR[0-9]{2}[0-9]{3}[0-9]{4}[A-Z0-9]{16}",
      GT: "GT[0-9]{2}[A-Z0-9]{4}[A-Z0-9]{20}",
      HR: "HR[0-9]{2}[0-9]{7}[0-9]{10}",
      HU: "HU[0-9]{2}[0-9]{3}[0-9]{4}[0-9]{1}[0-9]{15}[0-9]{1}",
      IE: "IE[0-9]{2}[A-Z]{4}[0-9]{6}[0-9]{8}",
      IL: "IL[0-9]{2}[0-9]{3}[0-9]{3}[0-9]{13}",
      IR: "IR[0-9]{2}[0-9]{22}",
      IS: "IS[0-9]{2}[0-9]{4}[0-9]{2}[0-9]{6}[0-9]{10}",
      IT: "IT[0-9]{2}[A-Z]{1}[0-9]{5}[0-9]{5}[A-Z0-9]{12}",
      JO: "JO[0-9]{2}[A-Z]{4}[0-9]{4}[0]{8}[A-Z0-9]{10}",
      KW: "KW[0-9]{2}[A-Z]{4}[0-9]{22}",
      KZ: "KZ[0-9]{2}[0-9]{3}[A-Z0-9]{13}",
      LB: "LB[0-9]{2}[0-9]{4}[A-Z0-9]{20}",
      LI: "LI[0-9]{2}[0-9]{5}[A-Z0-9]{12}",
      LT: "LT[0-9]{2}[0-9]{5}[0-9]{11}",
      LU: "LU[0-9]{2}[0-9]{3}[A-Z0-9]{13}",
      LV: "LV[0-9]{2}[A-Z]{4}[A-Z0-9]{13}",
      MC: "MC[0-9]{2}[0-9]{5}[0-9]{5}[A-Z0-9]{11}[0-9]{2}",
      MD: "MD[0-9]{2}[A-Z0-9]{20}",
      ME: "ME[0-9]{2}[0-9]{3}[0-9]{13}[0-9]{2}",
      MG: "MG[0-9]{2}[0-9]{23}",
      MK: "MK[0-9]{2}[0-9]{3}[A-Z0-9]{10}[0-9]{2}",
      ML: "ML[0-9]{2}[A-Z]{1}[0-9]{23}",
      MR: "MR13[0-9]{5}[0-9]{5}[0-9]{11}[0-9]{2}",
      MT: "MT[0-9]{2}[A-Z]{4}[0-9]{5}[A-Z0-9]{18}",
      MU: "MU[0-9]{2}[A-Z]{4}[0-9]{2}[0-9]{2}[0-9]{12}[0-9]{3}[A-Z]{3}",
      MZ: "MZ[0-9]{2}[0-9]{21}",
      NL: "NL[0-9]{2}[A-Z]{4}[0-9]{10}",
      NO: "NO[0-9]{2}[0-9]{4}[0-9]{6}[0-9]{1}",
      PK: "PK[0-9]{2}[A-Z]{4}[A-Z0-9]{16}",
      PL: "PL[0-9]{2}[0-9]{8}[0-9]{16}",
      PS: "PS[0-9]{2}[A-Z]{4}[A-Z0-9]{21}",
      PT: "PT[0-9]{2}[0-9]{4}[0-9]{4}[0-9]{11}[0-9]{2}",
      QA: "QA[0-9]{2}[A-Z]{4}[A-Z0-9]{21}",
      RO: "RO[0-9]{2}[A-Z]{4}[A-Z0-9]{16}",
      RS: "RS[0-9]{2}[0-9]{3}[0-9]{13}[0-9]{2}",
      SA: "SA[0-9]{2}[0-9]{2}[A-Z0-9]{18}",
      SE: "SE[0-9]{2}[0-9]{3}[0-9]{16}[0-9]{1}",
      SI: "SI[0-9]{2}[0-9]{5}[0-9]{8}[0-9]{2}",
      SK: "SK[0-9]{2}[0-9]{4}[0-9]{6}[0-9]{10}",
      SM: "SM[0-9]{2}[A-Z]{1}[0-9]{5}[0-9]{5}[A-Z0-9]{12}",
      SN: "SN[0-9]{2}[A-Z]{1}[0-9]{23}",
      TL: "TL38[0-9]{3}[0-9]{14}[0-9]{2}",
      TN: "TN59[0-9]{2}[0-9]{3}[0-9]{13}[0-9]{2}",
      TR: "TR[0-9]{2}[0-9]{5}[A-Z0-9]{1}[A-Z0-9]{16}",
      VG: "VG[0-9]{2}[A-Z]{4}[0-9]{16}",
      XK: "XK[0-9]{2}[0-9]{4}[0-9]{10}[0-9]{2}"
    };
    var e = ["AT", "BE", "BG", "CH", "CY", "CZ", "DE", "DK", "EE", "ES", "FI", "FR", "GB", "GI", "GR", "HR", "HU", "IE", "IS", "IT", "LI", "LT", "LU", "LV", "MC", "MT", "NL", "NO", "PL", "PT", "RO", "SE", "SI", "SK", "SM"];
    return {
      validate: function validate(s) {
        if (s.value === "") {
          return {
            valid: true
          };
        }

        var t = Object.assign({}, {
          message: ""
        }, s.options);
        var a = s.value.replace(/[^a-zA-Z0-9]/g, "").toUpperCase();
        var r = t.country || a.substr(0, 2);

        if (!Z[r]) {
          return {
            message: t.message,
            valid: false
          };
        }

        if (t.sepa !== undefined) {
          var _A = e.indexOf(r) !== -1;

          if ((t.sepa === "true" || t.sepa === true) && !_A || (t.sepa === "false" || t.sepa === false) && _A) {
            return {
              message: t.message,
              valid: false
            };
          }
        }

        var n = r$d(s.l10n ? t.message || s.l10n.iban.country : t.message, s.l10n ? s.l10n.iban.countries[r] : r);

        if (!new RegExp("^".concat(Z[r], "$")).test(s.value)) {
          return {
            message: n,
            valid: false
          };
        }

        a = "".concat(a.substr(4)).concat(a.substr(0, 4));
        a = a.split("").map(function (A) {
          var Z = A.charCodeAt(0);
          return Z >= "A".charCodeAt(0) && Z <= "Z".charCodeAt(0) ? Z - "A".charCodeAt(0) + 10 : A;
        }).join("");
        var I = parseInt(a.substr(0, 1), 10);
        var L = a.length;

        for (var _A2 = 1; _A2 < L; ++_A2) {
          I = (I * 10 + parseInt(a.substr(_A2, 1), 10)) % 97;
        }

        return {
          message: n,
          valid: I === 1
        };
      }
    };
  }

  function t$R(t) {
    var e = t.replace(/\./g, "");
    return {
      meta: {},
      valid: /^\d{7,8}$/.test(e)
    };
  }

  function t$Q(t, r) {
    if (!/^\d{13}$/.test(t)) {
      return false;
    }

    var e = parseInt(t.substr(0, 2), 10);
    var s = parseInt(t.substr(2, 2), 10);
    var n = parseInt(t.substr(7, 2), 10);
    var a = parseInt(t.substr(12, 1), 10);

    if (e > 31 || s > 12) {
      return false;
    }

    var u = 0;

    for (var _r = 0; _r < 6; _r++) {
      u += (7 - _r) * (parseInt(t.charAt(_r), 10) + parseInt(t.charAt(_r + 6), 10));
    }

    u = 11 - u % 11;

    if (u === 10 || u === 11) {
      u = 0;
    }

    if (u !== a) {
      return false;
    }

    switch (r.toUpperCase()) {
      case "BA":
        return 10 <= n && n <= 19;

      case "MK":
        return 41 <= n && n <= 49;

      case "ME":
        return 20 <= n && n <= 29;

      case "RS":
        return 70 <= n && n <= 99;

      case "SI":
        return 50 <= n && n <= 59;

      default:
        return true;
    }
  }

  function r$a(r) {
    return {
      meta: {},
      valid: t$Q(r, "BA")
    };
  }

  function e$x(e) {
    if (!/^\d{10}$/.test(e) && !/^\d{6}\s\d{3}\s\d{1}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var s = e.replace(/\s/g, "");
    var r = parseInt(s.substr(0, 2), 10) + 1900;
    var a = parseInt(s.substr(2, 2), 10);
    var l = parseInt(s.substr(4, 2), 10);

    if (a > 40) {
      r += 100;
      a -= 40;
    } else if (a > 20) {
      r -= 100;
      a -= 20;
    }

    if (!t$Y(r, a, l)) {
      return {
        meta: {},
        valid: false
      };
    }

    var i = 0;
    var n = [2, 4, 8, 5, 10, 9, 7, 3, 6];

    for (var _t = 0; _t < 9; _t++) {
      i += parseInt(s.charAt(_t), 10) * n[_t];
    }

    i = i % 11 % 10;
    return {
      meta: {},
      valid: "".concat(i) === s.substr(9, 1)
    };
  }

  function t$P(t) {
    var e = t.replace(/\D/g, "");

    if (!/^\d{11}$/.test(e) || /^1{11}|2{11}|3{11}|4{11}|5{11}|6{11}|7{11}|8{11}|9{11}|0{11}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var a = 0;
    var r;

    for (r = 0; r < 9; r++) {
      a += (10 - r) * parseInt(e.charAt(r), 10);
    }

    a = 11 - a % 11;

    if (a === 10 || a === 11) {
      a = 0;
    }

    if ("".concat(a) !== e.charAt(9)) {
      return {
        meta: {},
        valid: false
      };
    }

    var f = 0;

    for (r = 0; r < 10; r++) {
      f += (11 - r) * parseInt(e.charAt(r), 10);
    }

    f = 11 - f % 11;

    if (f === 10 || f === 11) {
      f = 0;
    }

    return {
      meta: {},
      valid: "".concat(f) === e.charAt(10)
    };
  }

  function t$O(t) {
    if (!/^756[.]{0,1}[0-9]{4}[.]{0,1}[0-9]{4}[.]{0,1}[0-9]{2}$/.test(t)) {
      return {
        meta: {},
        valid: false
      };
    }

    var e = t.replace(/\D/g, "").substr(3);
    var r = e.length;
    var a = r === 8 ? [3, 1] : [1, 3];
    var n = 0;

    for (var _t = 0; _t < r - 1; _t++) {
      n += parseInt(e.charAt(_t), 10) * a[_t % 2];
    }

    n = 10 - n % 10;
    return {
      meta: {},
      valid: "".concat(n) === e.charAt(r - 1)
    };
  }

  function e$w(e) {
    if (!/^\d{7,8}[-]{0,1}[0-9K]$/i.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var t = e.replace(/-/g, "");

    while (t.length < 9) {
      t = "0".concat(t);
    }

    var l = [3, 2, 7, 6, 5, 4, 3, 2];
    var a = 0;

    for (var _e = 0; _e < 8; _e++) {
      a += parseInt(t.charAt(_e), 10) * l[_e];
    }

    a = 11 - a % 11;
    var r = "".concat(a);

    if (a === 11) {
      r = "0";
    } else if (a === 10) {
      r = "K";
    }

    return {
      meta: {},
      valid: r === t.charAt(8).toUpperCase()
    };
  }

  function r$9(r) {
    var s = r.trim();

    if (!/^\d{15}$/.test(s) && !/^\d{17}[\dXx]{1}$/.test(s)) {
      return {
        meta: {},
        valid: false
      };
    }

    var e = {
      11: {
        0: [0],
        1: [[0, 9], [11, 17]],
        2: [0, 28, 29]
      },
      12: {
        0: [0],
        1: [[0, 16]],
        2: [0, 21, 23, 25]
      },
      13: {
        0: [0],
        1: [[0, 5], 7, 8, 21, [23, 33], [81, 85]],
        2: [[0, 5], [7, 9], [23, 25], 27, 29, 30, 81, 83],
        3: [[0, 4], [21, 24]],
        4: [[0, 4], 6, 21, [23, 35], 81],
        5: [[0, 3], [21, 35], 81, 82],
        6: [[0, 4], [21, 38], [81, 84]],
        7: [[0, 3], 5, 6, [21, 33]],
        8: [[0, 4], [21, 28]],
        9: [[0, 3], [21, 30], [81, 84]],
        10: [[0, 3], [22, 26], 28, 81, 82],
        11: [[0, 2], [21, 28], 81, 82]
      },
      14: {
        0: [0],
        1: [0, 1, [5, 10], [21, 23], 81],
        2: [[0, 3], 11, 12, [21, 27]],
        3: [[0, 3], 11, 21, 22],
        4: [[0, 2], 11, 21, [23, 31], 81],
        5: [[0, 2], 21, 22, 24, 25, 81],
        6: [[0, 3], [21, 24]],
        7: [[0, 2], [21, 29], 81],
        8: [[0, 2], [21, 30], 81, 82],
        9: [[0, 2], [21, 32], 81],
        10: [[0, 2], [21, 34], 81, 82],
        11: [[0, 2], [21, 30], 81, 82],
        23: [[0, 3], 22, 23, [25, 30], 32, 33]
      },
      15: {
        0: [0],
        1: [[0, 5], [21, 25]],
        2: [[0, 7], [21, 23]],
        3: [[0, 4]],
        4: [[0, 4], [21, 26], [28, 30]],
        5: [[0, 2], [21, 26], 81],
        6: [[0, 2], [21, 27]],
        7: [[0, 3], [21, 27], [81, 85]],
        8: [[0, 2], [21, 26]],
        9: [[0, 2], [21, 29], 81],
        22: [[0, 2], [21, 24]],
        25: [[0, 2], [22, 31]],
        26: [[0, 2], [24, 27], [29, 32], 34],
        28: [0, 1, [22, 27]],
        29: [0, [21, 23]]
      },
      21: {
        0: [0],
        1: [[0, 6], [11, 14], [22, 24], 81],
        2: [[0, 4], [11, 13], 24, [81, 83]],
        3: [[0, 4], 11, 21, 23, 81],
        4: [[0, 4], 11, [21, 23]],
        5: [[0, 5], 21, 22],
        6: [[0, 4], 24, 81, 82],
        7: [[0, 3], 11, 26, 27, 81, 82],
        8: [[0, 4], 11, 81, 82],
        9: [[0, 5], 11, 21, 22],
        10: [[0, 5], 11, 21, 81],
        11: [[0, 3], 21, 22],
        12: [[0, 2], 4, 21, 23, 24, 81, 82],
        13: [[0, 3], 21, 22, 24, 81, 82],
        14: [[0, 4], 21, 22, 81]
      },
      22: {
        0: [0],
        1: [[0, 6], 12, 22, [81, 83]],
        2: [[0, 4], 11, 21, [81, 84]],
        3: [[0, 3], 22, 23, 81, 82],
        4: [[0, 3], 21, 22],
        5: [[0, 3], 21, 23, 24, 81, 82],
        6: [[0, 2], 4, 5, [21, 23], 25, 81],
        7: [[0, 2], [21, 24], 81],
        8: [[0, 2], 21, 22, 81, 82],
        24: [[0, 6], 24, 26]
      },
      23: {
        0: [0],
        1: [[0, 12], 21, [23, 29], [81, 84]],
        2: [[0, 8], 21, [23, 25], 27, [29, 31], 81],
        3: [[0, 7], 21, 81, 82],
        4: [[0, 7], 21, 22],
        5: [[0, 3], 5, 6, [21, 24]],
        6: [[0, 6], [21, 24]],
        7: [[0, 16], 22, 81],
        8: [[0, 5], 11, 22, 26, 28, 33, 81, 82],
        9: [[0, 4], 21],
        10: [[0, 5], 24, 25, 81, [83, 85]],
        11: [[0, 2], 21, 23, 24, 81, 82],
        12: [[0, 2], [21, 26], [81, 83]],
        27: [[0, 4], [21, 23]]
      },
      31: {
        0: [0],
        1: [0, 1, [3, 10], [12, 20]],
        2: [0, 30]
      },
      32: {
        0: [0],
        1: [[0, 7], 11, [13, 18], 24, 25],
        2: [[0, 6], 11, 81, 82],
        3: [[0, 5], 11, 12, [21, 24], 81, 82],
        4: [[0, 2], 4, 5, 11, 12, 81, 82],
        5: [[0, 9], [81, 85]],
        6: [[0, 2], 11, 12, 21, 23, [81, 84]],
        7: [0, 1, 3, 5, 6, [21, 24]],
        8: [[0, 4], 11, 26, [29, 31]],
        9: [[0, 3], [21, 25], 28, 81, 82],
        10: [[0, 3], 11, 12, 23, 81, 84, 88],
        11: [[0, 2], 11, 12, [81, 83]],
        12: [[0, 4], [81, 84]],
        13: [[0, 2], 11, [21, 24]]
      },
      33: {
        0: [0],
        1: [[0, 6], [8, 10], 22, 27, 82, 83, 85],
        2: [0, 1, [3, 6], 11, 12, 25, 26, [81, 83]],
        3: [[0, 4], 22, 24, [26, 29], 81, 82],
        4: [[0, 2], 11, 21, 24, [81, 83]],
        5: [[0, 3], [21, 23]],
        6: [[0, 2], 21, 24, [81, 83]],
        7: [[0, 3], 23, 26, 27, [81, 84]],
        8: [[0, 3], 22, 24, 25, 81],
        9: [[0, 3], 21, 22],
        10: [[0, 4], [21, 24], 81, 82],
        11: [[0, 2], [21, 27], 81]
      },
      34: {
        0: [0],
        1: [[0, 4], 11, [21, 24], 81],
        2: [[0, 4], 7, 8, [21, 23], 25],
        3: [[0, 4], 11, [21, 23]],
        4: [[0, 6], 21],
        5: [[0, 4], 6, [21, 23]],
        6: [[0, 4], 21],
        7: [[0, 3], 11, 21],
        8: [[0, 3], 11, [22, 28], 81],
        10: [[0, 4], [21, 24]],
        11: [[0, 3], 22, [24, 26], 81, 82],
        12: [[0, 4], 21, 22, 25, 26, 82],
        13: [[0, 2], [21, 24]],
        14: [[0, 2], [21, 24]],
        15: [[0, 3], [21, 25]],
        16: [[0, 2], [21, 23]],
        17: [[0, 2], [21, 23]],
        18: [[0, 2], [21, 25], 81]
      },
      35: {
        0: [0],
        1: [[0, 5], 11, [21, 25], 28, 81, 82],
        2: [[0, 6], [11, 13]],
        3: [[0, 5], 22],
        4: [[0, 3], 21, [23, 30], 81],
        5: [[0, 5], 21, [24, 27], [81, 83]],
        6: [[0, 3], [22, 29], 81],
        7: [[0, 2], [21, 25], [81, 84]],
        8: [[0, 2], [21, 25], 81],
        9: [[0, 2], [21, 26], 81, 82]
      },
      36: {
        0: [0],
        1: [[0, 5], 11, [21, 24]],
        2: [[0, 3], 22, 81],
        3: [[0, 2], 13, [21, 23]],
        4: [[0, 3], 21, [23, 30], 81, 82],
        5: [[0, 2], 21],
        6: [[0, 2], 22, 81],
        7: [[0, 2], [21, 35], 81, 82],
        8: [[0, 3], [21, 30], 81],
        9: [[0, 2], [21, 26], [81, 83]],
        10: [[0, 2], [21, 30]],
        11: [[0, 2], [21, 30], 81]
      },
      37: {
        0: [0],
        1: [[0, 5], 12, 13, [24, 26], 81],
        2: [[0, 3], 5, [11, 14], [81, 85]],
        3: [[0, 6], [21, 23]],
        4: [[0, 6], 81],
        5: [[0, 3], [21, 23]],
        6: [[0, 2], [11, 13], 34, [81, 87]],
        7: [[0, 5], 24, 25, [81, 86]],
        8: [[0, 2], 11, [26, 32], [81, 83]],
        9: [[0, 3], 11, 21, 23, 82, 83],
        10: [[0, 2], [81, 83]],
        11: [[0, 3], 21, 22],
        12: [[0, 3]],
        13: [[0, 2], 11, 12, [21, 29]],
        14: [[0, 2], [21, 28], 81, 82],
        15: [[0, 2], [21, 26], 81],
        16: [[0, 2], [21, 26]],
        17: [[0, 2], [21, 28]]
      },
      41: {
        0: [0],
        1: [[0, 6], 8, 22, [81, 85]],
        2: [[0, 5], 11, [21, 25]],
        3: [[0, 7], 11, [22, 29], 81],
        4: [[0, 4], 11, [21, 23], 25, 81, 82],
        5: [[0, 3], 5, 6, 22, 23, 26, 27, 81],
        6: [[0, 3], 11, 21, 22],
        7: [[0, 4], 11, 21, [24, 28], 81, 82],
        8: [[0, 4], 11, [21, 23], 25, [81, 83]],
        9: [[0, 2], 22, 23, [26, 28]],
        10: [[0, 2], [23, 25], 81, 82],
        11: [[0, 4], [21, 23]],
        12: [[0, 2], 21, 22, 24, 81, 82],
        13: [[0, 3], [21, 30], 81],
        14: [[0, 3], [21, 26], 81],
        15: [[0, 3], [21, 28]],
        16: [[0, 2], [21, 28], 81],
        17: [[0, 2], [21, 29]],
        90: [0, 1]
      },
      42: {
        0: [0],
        1: [[0, 7], [11, 17]],
        2: [[0, 5], 22, 81],
        3: [[0, 3], [21, 25], 81],
        5: [[0, 6], [25, 29], [81, 83]],
        6: [[0, 2], 6, 7, [24, 26], [82, 84]],
        7: [[0, 4]],
        8: [[0, 2], 4, 21, 22, 81],
        9: [[0, 2], [21, 23], 81, 82, 84],
        10: [[0, 3], [22, 24], 81, 83, 87],
        11: [[0, 2], [21, 27], 81, 82],
        12: [[0, 2], [21, 24], 81],
        13: [[0, 3], 21, 81],
        28: [[0, 2], 22, 23, [25, 28]],
        90: [0, [4, 6], 21]
      },
      43: {
        0: [0],
        1: [[0, 5], 11, 12, 21, 22, 24, 81],
        2: [[0, 4], 11, 21, [23, 25], 81],
        3: [[0, 2], 4, 21, 81, 82],
        4: [0, 1, [5, 8], 12, [21, 24], 26, 81, 82],
        5: [[0, 3], 11, [21, 25], [27, 29], 81],
        6: [[0, 3], 11, 21, 23, 24, 26, 81, 82],
        7: [[0, 3], [21, 26], 81],
        8: [[0, 2], 11, 21, 22],
        9: [[0, 3], [21, 23], 81],
        10: [[0, 3], [21, 28], 81],
        11: [[0, 3], [21, 29]],
        12: [[0, 2], [21, 30], 81],
        13: [[0, 2], 21, 22, 81, 82],
        31: [0, 1, [22, 27], 30]
      },
      44: {
        0: [0],
        1: [[0, 7], [11, 16], 83, 84],
        2: [[0, 5], 21, 22, 24, 29, 32, 33, 81, 82],
        3: [0, 1, [3, 8]],
        4: [[0, 4]],
        5: [0, 1, [6, 15], 23, 82, 83],
        6: [0, 1, [4, 8]],
        7: [0, 1, [3, 5], 81, [83, 85]],
        8: [[0, 4], 11, 23, 25, [81, 83]],
        9: [[0, 3], 23, [81, 83]],
        12: [[0, 3], [23, 26], 83, 84],
        13: [[0, 3], [22, 24], 81],
        14: [[0, 2], [21, 24], 26, 27, 81],
        15: [[0, 2], 21, 23, 81],
        16: [[0, 2], [21, 25]],
        17: [[0, 2], 21, 23, 81],
        18: [[0, 3], 21, 23, [25, 27], 81, 82],
        19: [0],
        20: [0],
        51: [[0, 3], 21, 22],
        52: [[0, 3], 21, 22, 24, 81],
        53: [[0, 2], [21, 23], 81]
      },
      45: {
        0: [0],
        1: [[0, 9], [21, 27]],
        2: [[0, 5], [21, 26]],
        3: [[0, 5], 11, 12, [21, 32]],
        4: [0, 1, [3, 6], 11, [21, 23], 81],
        5: [[0, 3], 12, 21],
        6: [[0, 3], 21, 81],
        7: [[0, 3], 21, 22],
        8: [[0, 4], 21, 81],
        9: [[0, 3], [21, 24], 81],
        10: [[0, 2], [21, 31]],
        11: [[0, 2], [21, 23]],
        12: [[0, 2], [21, 29], 81],
        13: [[0, 2], [21, 24], 81],
        14: [[0, 2], [21, 25], 81]
      },
      46: {
        0: [0],
        1: [0, 1, [5, 8]],
        2: [0, 1],
        3: [0, [21, 23]],
        90: [[0, 3], [5, 7], [21, 39]]
      },
      50: {
        0: [0],
        1: [[0, 19]],
        2: [0, [22, 38], [40, 43]],
        3: [0, [81, 84]]
      },
      51: {
        0: [0],
        1: [0, 1, [4, 8], [12, 15], [21, 24], 29, 31, 32, [81, 84]],
        3: [[0, 4], 11, 21, 22],
        4: [[0, 3], 11, 21, 22],
        5: [[0, 4], 21, 22, 24, 25],
        6: [0, 1, 3, 23, 26, [81, 83]],
        7: [0, 1, 3, 4, [22, 27], 81],
        8: [[0, 2], 11, 12, [21, 24]],
        9: [[0, 4], [21, 23]],
        10: [[0, 2], 11, 24, 25, 28],
        11: [[0, 2], [11, 13], 23, 24, 26, 29, 32, 33, 81],
        13: [[0, 4], [21, 25], 81],
        14: [[0, 2], [21, 25]],
        15: [[0, 3], [21, 29]],
        16: [[0, 3], [21, 23], 81],
        17: [[0, 3], [21, 25], 81],
        18: [[0, 3], [21, 27]],
        19: [[0, 3], [21, 23]],
        20: [[0, 2], 21, 22, 81],
        32: [0, [21, 33]],
        33: [0, [21, 38]],
        34: [0, 1, [22, 37]]
      },
      52: {
        0: [0],
        1: [[0, 3], [11, 15], [21, 23], 81],
        2: [0, 1, 3, 21, 22],
        3: [[0, 3], [21, 30], 81, 82],
        4: [[0, 2], [21, 25]],
        5: [[0, 2], [21, 27]],
        6: [[0, 3], [21, 28]],
        22: [0, 1, [22, 30]],
        23: [0, 1, [22, 28]],
        24: [0, 1, [22, 28]],
        26: [0, 1, [22, 36]],
        27: [[0, 2], 22, 23, [25, 32]]
      },
      53: {
        0: [0],
        1: [[0, 3], [11, 14], 21, 22, [24, 29], 81],
        3: [[0, 2], [21, 26], 28, 81],
        4: [[0, 2], [21, 28]],
        5: [[0, 2], [21, 24]],
        6: [[0, 2], [21, 30]],
        7: [[0, 2], [21, 24]],
        8: [[0, 2], [21, 29]],
        9: [[0, 2], [21, 27]],
        23: [0, 1, [22, 29], 31],
        25: [[0, 4], [22, 32]],
        26: [0, 1, [21, 28]],
        27: [0, 1, [22, 30]],
        28: [0, 1, 22, 23],
        29: [0, 1, [22, 32]],
        31: [0, 2, 3, [22, 24]],
        34: [0, [21, 23]],
        33: [0, 21, [23, 25]],
        35: [0, [21, 28]]
      },
      54: {
        0: [0],
        1: [[0, 2], [21, 27]],
        21: [0, [21, 29], 32, 33],
        22: [0, [21, 29], [31, 33]],
        23: [0, 1, [22, 38]],
        24: [0, [21, 31]],
        25: [0, [21, 27]],
        26: [0, [21, 27]]
      },
      61: {
        0: [0],
        1: [[0, 4], [11, 16], 22, [24, 26]],
        2: [[0, 4], 22],
        3: [[0, 4], [21, 24], [26, 31]],
        4: [[0, 4], [22, 31], 81],
        5: [[0, 2], [21, 28], 81, 82],
        6: [[0, 2], [21, 32]],
        7: [[0, 2], [21, 30]],
        8: [[0, 2], [21, 31]],
        9: [[0, 2], [21, 29]],
        10: [[0, 2], [21, 26]]
      },
      62: {
        0: [0],
        1: [[0, 5], 11, [21, 23]],
        2: [0, 1],
        3: [[0, 2], 21],
        4: [[0, 3], [21, 23]],
        5: [[0, 3], [21, 25]],
        6: [[0, 2], [21, 23]],
        7: [[0, 2], [21, 25]],
        8: [[0, 2], [21, 26]],
        9: [[0, 2], [21, 24], 81, 82],
        10: [[0, 2], [21, 27]],
        11: [[0, 2], [21, 26]],
        12: [[0, 2], [21, 28]],
        24: [0, 21, [24, 29]],
        26: [0, 21, [23, 30]],
        29: [0, 1, [21, 27]],
        30: [0, 1, [21, 27]]
      },
      63: {
        0: [0],
        1: [[0, 5], [21, 23]],
        2: [0, 2, [21, 25]],
        21: [0, [21, 23], [26, 28]],
        22: [0, [21, 24]],
        23: [0, [21, 24]],
        25: [0, [21, 25]],
        26: [0, [21, 26]],
        27: [0, 1, [21, 26]],
        28: [[0, 2], [21, 23]]
      },
      64: {
        0: [0],
        1: [0, 1, [4, 6], 21, 22, 81],
        2: [[0, 3], 5, [21, 23]],
        3: [[0, 3], [21, 24], 81],
        4: [[0, 2], [21, 25]],
        5: [[0, 2], 21, 22]
      },
      65: {
        0: [0],
        1: [[0, 9], 21],
        2: [[0, 5]],
        21: [0, 1, 22, 23],
        22: [0, 1, 22, 23],
        23: [[0, 3], [23, 25], 27, 28],
        28: [0, 1, [22, 29]],
        29: [0, 1, [22, 29]],
        30: [0, 1, [22, 24]],
        31: [0, 1, [21, 31]],
        32: [0, 1, [21, 27]],
        40: [0, 2, 3, [21, 28]],
        42: [[0, 2], 21, [23, 26]],
        43: [0, 1, [21, 26]],
        90: [[0, 4]],
        27: [[0, 2], 22, 23]
      },
      71: {
        0: [0]
      },
      81: {
        0: [0]
      },
      82: {
        0: [0]
      }
    };
    var a = parseInt(s.substr(0, 2), 10);
    var n = parseInt(s.substr(2, 2), 10);
    var l = parseInt(s.substr(4, 2), 10);

    if (!e[a] || !e[a][n]) {
      return {
        meta: {},
        valid: false
      };
    }

    var i = false;
    var u = e[a][n];
    var o;

    for (o = 0; o < u.length; o++) {
      if (Array.isArray(u[o]) && u[o][0] <= l && l <= u[o][1] || !Array.isArray(u[o]) && l === u[o]) {
        i = true;
        break;
      }
    }

    if (!i) {
      return {
        meta: {},
        valid: false
      };
    }

    var f;

    if (s.length === 18) {
      f = s.substr(6, 8);
    } else {
      f = "19".concat(s.substr(6, 6));
    }

    var c = parseInt(f.substr(0, 4), 10);
    var p = parseInt(f.substr(4, 2), 10);
    var d = parseInt(f.substr(6, 2), 10);

    if (!t$Y(c, p, d)) {
      return {
        meta: {},
        valid: false
      };
    }

    if (s.length === 18) {
      var _t = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
      var _r = 0;

      for (o = 0; o < 17; o++) {
        _r += parseInt(s.charAt(o), 10) * _t[o];
      }

      _r = (12 - _r % 11) % 11;

      var _e = s.charAt(17).toUpperCase() !== "X" ? parseInt(s.charAt(17), 10) : 10;

      return {
        meta: {},
        valid: _e === _r
      };
    }

    return {
      meta: {},
      valid: true
    };
  }

  function t$N(t) {
    var e = t.replace(/\./g, "").replace("-", "");

    if (!/^\d{8,16}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = e.length;
    var a = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
    var l = 0;

    for (var _t = r - 2; _t >= 0; _t--) {
      l += parseInt(e.charAt(_t), 10) * a[_t];
    }

    l = l % 11;

    if (l >= 2) {
      l = 11 - l;
    }

    return {
      meta: {},
      valid: "".concat(l) === e.substr(r - 1)
    };
  }

  function e$v(e) {
    if (!/^\d{9,10}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = 1900 + parseInt(e.substr(0, 2), 10);
    var s = parseInt(e.substr(2, 2), 10) % 50 % 20;
    var a = parseInt(e.substr(4, 2), 10);

    if (e.length === 9) {
      if (r >= 1980) {
        r -= 100;
      }

      if (r > 1953) {
        return {
          meta: {},
          valid: false
        };
      }
    } else if (r < 1954) {
      r += 100;
    }

    if (!t$Y(r, s, a)) {
      return {
        meta: {},
        valid: false
      };
    }

    if (e.length === 10) {
      var _t = parseInt(e.substr(0, 9), 10) % 11;

      if (r < 1985) {
        _t = _t % 10;
      }

      return {
        meta: {},
        valid: "".concat(_t) === e.substr(9, 1)
      };
    }

    return {
      meta: {},
      valid: true
    };
  }

  function e$u(e) {
    if (!/^[0-9]{6}[-]{0,1}[0-9]{4}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var a = e.replace(/-/g, "");
    var r = parseInt(a.substr(0, 2), 10);
    var s = parseInt(a.substr(2, 2), 10);
    var n = parseInt(a.substr(4, 2), 10);

    switch (true) {
      case "5678".indexOf(a.charAt(6)) !== -1 && n >= 58:
        n += 1800;
        break;

      case "0123".indexOf(a.charAt(6)) !== -1:
      case "49".indexOf(a.charAt(6)) !== -1 && n >= 37:
        n += 1900;
        break;

      default:
        n += 2e3;
        break;
    }

    return {
      meta: {},
      valid: t$Y(n, s, r)
    };
  }

  function t$M(t) {
    var e = /^[0-9]{8}[-]{0,1}[A-HJ-NP-TV-Z]$/.test(t);
    var s = /^[XYZ][-]{0,1}[0-9]{7}[-]{0,1}[A-HJ-NP-TV-Z]$/.test(t);
    var n = /^[A-HNPQS][-]{0,1}[0-9]{7}[-]{0,1}[0-9A-J]$/.test(t);

    if (!e && !s && !n) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = t.replace(/-/g, "");
    var l;
    var a;
    var f = true;

    if (e || s) {
      a = "DNI";

      var _t = "XYZ".indexOf(r.charAt(0));

      if (_t !== -1) {
        r = _t + r.substr(1) + "";
        a = "NIE";
      }

      l = parseInt(r.substr(0, 8), 10);
      l = "TRWAGMYFPDXBNJZSQVHLCKE"[l % 23];
      return {
        meta: {
          type: a
        },
        valid: l === r.substr(8, 1)
      };
    } else {
      l = r.substr(1, 7);
      a = "CIF";
      var _t2 = r[0];

      var _e = r.substr(-1);

      var _s = 0;

      for (var _t3 = 0; _t3 < l.length; _t3++) {
        if (_t3 % 2 !== 0) {
          _s += parseInt(l[_t3], 10);
        } else {
          var _e2 = "" + parseInt(l[_t3], 10) * 2;

          _s += parseInt(_e2[0], 10);

          if (_e2.length === 2) {
            _s += parseInt(_e2[1], 10);
          }
        }
      }

      var _n = _s - Math.floor(_s / 10) * 10;

      if (_n !== 0) {
        _n = 10 - _n;
      }

      if ("KQS".indexOf(_t2) !== -1) {
        f = _e === "JABCDEFGHI"[_n];
      } else if ("ABEH".indexOf(_t2) !== -1) {
        f = _e === "" + _n;
      } else {
        f = _e === "" + _n || _e === "JABCDEFGHI"[_n];
      }

      return {
        meta: {
          type: a
        },
        valid: f
      };
    }
  }

  function s$7(s) {
    if (!/^[0-9]{6}[-+A][0-9]{3}[0-9ABCDEFHJKLMNPRSTUVWXY]$/.test(s)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = parseInt(s.substr(0, 2), 10);
    var a = parseInt(s.substr(2, 2), 10);
    var e = parseInt(s.substr(4, 2), 10);
    var n = {
      "+": 1800,
      "-": 1900,
      A: 2e3
    };
    e = n[s.charAt(6)] + e;

    if (!t$Y(e, a, r)) {
      return {
        meta: {},
        valid: false
      };
    }

    var u = parseInt(s.substr(7, 3), 10);

    if (u < 2) {
      return {
        meta: {},
        valid: false
      };
    }

    var i = parseInt(s.substr(0, 6) + s.substr(7, 3) + "", 10);
    return {
      meta: {},
      valid: "0123456789ABCDEFHJKLMNPRSTUVWXY".charAt(i % 31) === s.charAt(10)
    };
  }

  function t$L(t) {
    var s = t.toUpperCase();

    if (!/^(1|2)\d{2}\d{2}(\d{2}|\d[A-Z]|\d{3})\d{2,3}\d{3}\d{2}$/.test(s)) {
      return {
        meta: {},
        valid: false
      };
    }

    var e = s.substr(5, 2);

    switch (true) {
      case /^\d{2}$/.test(e):
        s = t;
        break;

      case e === "2A":
        s = "".concat(t.substr(0, 5), "19").concat(t.substr(7));
        break;

      case e === "2B":
        s = "".concat(t.substr(0, 5), "18").concat(t.substr(7));
        break;

      default:
        return {
          meta: {},
          valid: false
        };
    }

    var r = 97 - parseInt(s.substr(0, 13), 10) % 97;
    var a = r < 10 ? "0".concat(r) : "".concat(r);
    return {
      meta: {},
      valid: a === s.substr(13)
    };
  }

  function t$K(t) {
    var e = t.toUpperCase();

    if (!/^[A-MP-Z]{1,2}[0-9]{6}[0-9A]$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var s = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var n = e.charAt(0);
    var r = e.charAt(1);
    var a = 0;
    var c = e;

    if (/^[A-Z]$/.test(r)) {
      a += 9 * (10 + s.indexOf(n));
      a += 8 * (10 + s.indexOf(r));
      c = e.substr(2);
    } else {
      a += 9 * 36;
      a += 8 * (10 + s.indexOf(n));
      c = e.substr(1);
    }

    var o = c.length;

    for (var _t = 0; _t < o - 1; _t++) {
      a += (7 - _t) * parseInt(c.charAt(_t), 10);
    }

    var f = a % 11;
    var l = f === 0 ? "0" : 11 - f === 10 ? "A" : "".concat(11 - f);
    return {
      meta: {},
      valid: l === c.charAt(o - 1)
    };
  }

  function o$1(o) {
    return {
      meta: {},
      valid: /^[0-9]{11}$/.test(o) && t$14(o)
    };
  }

  function e$t(e) {
    if (!/^[2-9]\d{11}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = e.split("").map(function (t) {
      return parseInt(t, 10);
    });
    return {
      meta: {},
      valid: t$12(r)
    };
  }

  function t$J(t) {
    if (!/^\d{7}[A-W][AHWTX]?$/.test(t)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = function r(t) {
      var r = t;

      while (r.length < 7) {
        r = "0".concat(r);
      }

      var e = "WABCDEFGHIJKLMNOPQRSTUV";
      var s = 0;

      for (var _t = 0; _t < 7; _t++) {
        s += parseInt(r.charAt(_t), 10) * (8 - _t);
      }

      s += 9 * e.indexOf(r.substr(7));
      return e[s % 23];
    };

    var e = t.length === 9 && ("A" === t.charAt(8) || "H" === t.charAt(8)) ? t.charAt(7) === r(t.substr(0, 7) + t.substr(8) + "") : t.charAt(7) === r(t.substr(0, 7));
    return {
      meta: {},
      valid: e
    };
  }

  function e$s(e) {
    if (!/^\d{1,9}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    return {
      meta: {},
      valid: t$15(e)
    };
  }

  function e$r(e) {
    if (!/^[0-9]{6}[-]{0,1}[0-9]{4}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = e.replace(/-/g, "");
    var s = parseInt(r.substr(0, 2), 10);
    var a = parseInt(r.substr(2, 2), 10);
    var n = parseInt(r.substr(4, 2), 10);
    var l = parseInt(r.charAt(9), 10);
    n = l === 9 ? 1900 + n : (20 + l) * 100 + n;

    if (!t$Y(n, a, s, true)) {
      return {
        meta: {},
        valid: false
      };
    }

    var c = [3, 2, 7, 6, 5, 4, 3, 2];
    var i = 0;

    for (var _t = 0; _t < 8; _t++) {
      i += parseInt(r.charAt(_t), 10) * c[_t];
    }

    i = 11 - i % 11;
    return {
      meta: {},
      valid: "".concat(i) === r.charAt(8)
    };
  }

  function e$q(e) {
    var a = e.replace("-", "");

    if (!/^\d{13}$/.test(a)) {
      return {
        meta: {},
        valid: false
      };
    }

    var s = a.charAt(6);
    var r = parseInt(a.substr(0, 2), 10);
    var c = parseInt(a.substr(2, 2), 10);
    var n = parseInt(a.substr(4, 2), 10);

    switch (s) {
      case "1":
      case "2":
      case "5":
      case "6":
        r += 1900;
        break;

      case "3":
      case "4":
      case "7":
      case "8":
        r += 2e3;
        break;

      default:
        r += 1800;
        break;
    }

    if (!t$Y(r, c, n)) {
      return {
        meta: {},
        valid: false
      };
    }

    var l = [2, 3, 4, 5, 6, 7, 8, 9, 2, 3, 4, 5];
    var o = a.length;
    var i = 0;

    for (var _t = 0; _t < o - 1; _t++) {
      i += l[_t] * parseInt(a.charAt(_t), 10);
    }

    var u = (11 - i % 11) % 10;
    return {
      meta: {},
      valid: "".concat(u) === a.charAt(o - 1)
    };
  }

  function r$8(r) {
    if (!/^[0-9]{11}$/.test(r)) {
      return {
        meta: {},
        valid: false
      };
    }

    var e = parseInt(r.charAt(0), 10);
    var a = parseInt(r.substr(1, 2), 10);
    var s = parseInt(r.substr(3, 2), 10);
    var n = parseInt(r.substr(5, 2), 10);
    var i = e % 2 === 0 ? 17 + e / 2 : 17 + (e + 1) / 2;
    a = i * 100 + a;

    if (!t$Y(a, s, n, true)) {
      return {
        meta: {},
        valid: false
      };
    }

    var l = [1, 2, 3, 4, 5, 6, 7, 8, 9, 1];
    var f = 0;
    var u;

    for (u = 0; u < 10; u++) {
      f += parseInt(r.charAt(u), 10) * l[u];
    }

    f = f % 11;

    if (f !== 10) {
      return {
        meta: {},
        valid: "".concat(f) === r.charAt(10)
      };
    }

    f = 0;
    l = [3, 4, 5, 6, 7, 8, 9, 1, 2, 3];

    for (u = 0; u < 10; u++) {
      f += parseInt(r.charAt(u), 10) * l[u];
    }

    f = f % 11;

    if (f === 10) {
      f = 0;
    }

    return {
      meta: {},
      valid: "".concat(f) === r.charAt(10)
    };
  }

  function e$p(e) {
    if (!/^[0-9]{6}[-]{0,1}[0-9]{5}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = e.replace(/\D/g, "");
    var a = parseInt(r.substr(0, 2), 10);
    var s = parseInt(r.substr(2, 2), 10);
    var n = parseInt(r.substr(4, 2), 10);
    n = n + 1800 + parseInt(r.charAt(6), 10) * 100;

    if (!t$Y(n, s, a, true)) {
      return {
        meta: {},
        valid: false
      };
    }

    var l = 0;
    var i = [10, 5, 8, 4, 2, 1, 6, 3, 7, 9];

    for (var _t = 0; _t < 10; _t++) {
      l += parseInt(r.charAt(_t), 10) * i[_t];
    }

    l = (l + 1) % 11 % 10;
    return {
      meta: {},
      valid: "".concat(l) === r.charAt(10)
    };
  }

  function r$7(r) {
    return {
      meta: {},
      valid: t$Q(r, "ME")
    };
  }

  function r$6(r) {
    return {
      meta: {},
      valid: t$Q(r, "MK")
    };
  }

  function O(O) {
    var t = O.toUpperCase();

    if (!/^[A-Z]{4}\d{6}[A-Z]{6}[0-9A-Z]\d$/.test(t)) {
      return {
        meta: {},
        valid: false
      };
    }

    var C = ["BACA", "BAKA", "BUEI", "BUEY", "CACA", "CACO", "CAGA", "CAGO", "CAKA", "CAKO", "COGE", "COGI", "COJA", "COJE", "COJI", "COJO", "COLA", "CULO", "FALO", "FETO", "GETA", "GUEI", "GUEY", "JETA", "JOTO", "KACA", "KACO", "KAGA", "KAGO", "KAKA", "KAKO", "KOGE", "KOGI", "KOJA", "KOJE", "KOJI", "KOJO", "KOLA", "KULO", "LILO", "LOCA", "LOCO", "LOKA", "LOKO", "MAME", "MAMO", "MEAR", "MEAS", "MEON", "MIAR", "MION", "MOCO", "MOKO", "MULA", "MULO", "NACA", "NACO", "PEDA", "PEDO", "PENE", "PIPI", "PITO", "POPO", "PUTA", "PUTO", "QULO", "RATA", "ROBA", "ROBE", "ROBO", "RUIN", "SENO", "TETA", "VACA", "VAGA", "VAGO", "VAKA", "VUEI", "VUEY", "WUEI", "WUEY"];
    var e = t.substr(0, 4);

    if (C.indexOf(e) >= 0) {
      return {
        meta: {},
        valid: false
      };
    }

    var s = parseInt(t.substr(4, 2), 10);
    var r = parseInt(t.substr(6, 2), 10);
    var a = parseInt(t.substr(6, 2), 10);

    if (/^[0-9]$/.test(t.charAt(16))) {
      s += 1900;
    } else {
      s += 2e3;
    }

    if (!t$Y(s, r, a)) {
      return {
        meta: {},
        valid: false
      };
    }

    var E = t.charAt(10);

    if (E !== "H" && E !== "M") {
      return {
        meta: {},
        valid: false
      };
    }

    var n = t.substr(11, 2);
    var K = ["AS", "BC", "BS", "CC", "CH", "CL", "CM", "CS", "DF", "DG", "GR", "GT", "HG", "JC", "MC", "MN", "MS", "NE", "NL", "NT", "OC", "PL", "QR", "QT", "SL", "SP", "SR", "TC", "TL", "TS", "VZ", "YN", "ZS"];

    if (K.indexOf(n) === -1) {
      return {
        meta: {},
        valid: false
      };
    }

    var i = "0123456789ABCDEFGHIJKLMN&OPQRSTUVWXYZ";
    var L = 0;
    var l = t.length;

    for (var _A = 0; _A < l - 1; _A++) {
      L += (18 - _A) * i.indexOf(t.charAt(_A));
    }

    L = (10 - L % 10) % 10;
    return {
      meta: {},
      valid: "".concat(L) === t.charAt(l - 1)
    };
  }

  function s$6(s) {
    if (!/^\d{12}$/.test(s)) {
      return {
        meta: {},
        valid: false
      };
    }

    var e = parseInt(s.substr(0, 2), 10);
    var r = parseInt(s.substr(2, 2), 10);
    var a = parseInt(s.substr(4, 2), 10);

    if (!t$Y(e + 1900, r, a) && !t$Y(e + 2e3, r, a)) {
      return {
        meta: {},
        valid: false
      };
    }

    var n = s.substr(6, 2);
    var i = ["17", "18", "19", "20", "69", "70", "73", "80", "81", "94", "95", "96", "97"];
    return {
      meta: {},
      valid: i.indexOf(n) === -1
    };
  }

  function e$o(e) {
    if (e.length < 8) {
      return {
        meta: {},
        valid: false
      };
    }

    var t = e;

    if (t.length === 8) {
      t = "0".concat(t);
    }

    if (!/^[0-9]{4}[.]{0,1}[0-9]{2}[.]{0,1}[0-9]{3}$/.test(t)) {
      return {
        meta: {},
        valid: false
      };
    }

    t = t.replace(/\./g, "");

    if (parseInt(t, 10) === 0) {
      return {
        meta: {},
        valid: false
      };
    }

    var a = 0;
    var l = t.length;

    for (var _e = 0; _e < l - 1; _e++) {
      a += (9 - _e) * parseInt(t.charAt(_e), 10);
    }

    a = a % 11;

    if (a === 10) {
      a = 0;
    }

    return {
      meta: {},
      valid: "".concat(a) === t.charAt(l - 1)
    };
  }

  function t$I(t) {
    if (!/^\d{11}$/.test(t)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = function r(t) {
      var r = [3, 7, 6, 1, 8, 9, 4, 5, 2];
      var e = 0;

      for (var n = 0; n < 9; n++) {
        e += r[n] * parseInt(t.charAt(n), 10);
      }

      return 11 - e % 11;
    };

    var e = function e(t) {
      var r = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
      var e = 0;

      for (var n = 0; n < 10; n++) {
        e += r[n] * parseInt(t.charAt(n), 10);
      }

      return 11 - e % 11;
    };

    return {
      meta: {},
      valid: "".concat(r(t)) === t.substr(-2, 1) && "".concat(e(t)) === t.substr(-1)
    };
  }

  function t$H(t) {
    if (!/^\d{8}[0-9A-Z]*$/.test(t)) {
      return {
        meta: {},
        valid: false
      };
    }

    if (t.length === 8) {
      return {
        meta: {},
        valid: true
      };
    }

    var e = [3, 2, 7, 6, 5, 4, 3, 2];
    var r = 0;

    for (var _a = 0; _a < 8; _a++) {
      r += e[_a] * parseInt(t.charAt(_a), 10);
    }

    var a = r % 11;
    var n = [6, 5, 4, 3, 2, 1, 1, 0, 9, 8, 7][a];
    var c = "KJIHGFEDCBA".charAt(a);
    return {
      meta: {},
      valid: t.charAt(8) === "".concat(n) || t.charAt(8) === c
    };
  }

  function t$G(t) {
    if (!/^[0-9]{11}$/.test(t)) {
      return {
        meta: {},
        valid: false
      };
    }

    var e = 0;
    var a = t.length;
    var r = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 7];

    for (var n = 0; n < a - 1; n++) {
      e += r[n] * parseInt(t.charAt(n), 10);
    }

    e = e % 10;

    if (e === 0) {
      e = 10;
    }

    e = 10 - e;
    return {
      meta: {},
      valid: "".concat(e) === t.charAt(a - 1)
    };
  }

  function e$n(e) {
    if (!/^[0-9]{13}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var a = parseInt(e.charAt(0), 10);

    if (a === 0 || a === 7 || a === 8) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = parseInt(e.substr(1, 2), 10);
    var s = parseInt(e.substr(3, 2), 10);
    var n = parseInt(e.substr(5, 2), 10);
    var i = {
      1: 1900,
      2: 1900,
      3: 1800,
      4: 1800,
      5: 2e3,
      6: 2e3
    };

    if (n > 31 && s > 12) {
      return {
        meta: {},
        valid: false
      };
    }

    if (a !== 9) {
      r = i[a + ""] + r;

      if (!t$Y(r, s, n)) {
        return {
          meta: {},
          valid: false
        };
      }
    }

    var l = 0;
    var f = [2, 7, 9, 1, 4, 6, 3, 5, 8, 2, 7, 9];
    var o = e.length;

    for (var _t = 0; _t < o - 1; _t++) {
      l += parseInt(e.charAt(_t), 10) * f[_t];
    }

    l = l % 11;

    if (l === 10) {
      l = 1;
    }

    return {
      meta: {},
      valid: "".concat(l) === e.charAt(o - 1)
    };
  }

  function r$5(r) {
    return {
      meta: {},
      valid: t$Q(r, "RS")
    };
  }

  function r$4(r) {
    if (!/^[0-9]{10}$/.test(r) && !/^[0-9]{6}[-|+][0-9]{4}$/.test(r)) {
      return {
        meta: {},
        valid: false
      };
    }

    var e = r.replace(/[^0-9]/g, "");
    var a = parseInt(e.substr(0, 2), 10) + 1900;
    var n = parseInt(e.substr(2, 2), 10);
    var i = parseInt(e.substr(4, 2), 10);

    if (!t$Y(a, n, i)) {
      return {
        meta: {},
        valid: false
      };
    }

    return {
      meta: {},
      valid: t$15(e)
    };
  }

  function r$3(r) {
    return {
      meta: {},
      valid: t$Q(r, "SI")
    };
  }

  function t$F(t) {
    return {
      meta: {},
      valid: /^\d{5}$/.test(t)
    };
  }

  function t$E(t) {
    if (t.length !== 13) {
      return {
        meta: {},
        valid: false
      };
    }

    var e = 0;

    for (var a = 0; a < 12; a++) {
      e += parseInt(t.charAt(a), 10) * (13 - a);
    }

    return {
      meta: {},
      valid: (11 - e % 11) % 10 === parseInt(t.charAt(12), 10)
    };
  }

  function t$D(t) {
    if (t.length !== 11) {
      return {
        meta: {},
        valid: false
      };
    }

    var e = 0;

    for (var a = 0; a < 10; a++) {
      e += parseInt(t.charAt(a), 10);
    }

    return {
      meta: {},
      valid: e % 10 === parseInt(t.charAt(10), 10)
    };
  }

  function t$C(t) {
    var e = t.toUpperCase();

    if (!/^[A-Z][12][0-9]{8}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var a = e.length;
    var n = "ABCDEFGHJKLMNPQRSTUVXYWZIO";
    var r = n.indexOf(e.charAt(0)) + 10;
    var o = Math.floor(r / 10) + r % 10 * (a - 1);
    var s = 0;

    for (var _t = 1; _t < a - 1; _t++) {
      s += parseInt(e.charAt(_t), 10) * (a - 1 - _t);
    }

    return {
      meta: {},
      valid: (o + s + parseInt(e.charAt(a - 1), 10)) % 10 === 0
    };
  }

  function t$B(t) {
    if (!/^\d{8}$/.test(t)) {
      return {
        meta: {},
        valid: false
      };
    }

    var e = [2, 9, 8, 7, 6, 3, 4];
    var a = 0;

    for (var r = 0; r < 7; r++) {
      a += parseInt(t.charAt(r), 10) * e[r];
    }

    a = a % 10;

    if (a > 0) {
      a = 10 - a;
    }

    return {
      meta: {},
      valid: "".concat(a) === t.charAt(7)
    };
  }

  function r$2(r) {
    if (!/^[0-9]{10}[0|1][8|9][0-9]$/.test(r)) {
      return {
        meta: {},
        valid: false
      };
    }

    var s = parseInt(r.substr(0, 2), 10);
    var a = new Date().getFullYear() % 100;
    var l = parseInt(r.substr(2, 2), 10);
    var n = parseInt(r.substr(4, 2), 10);
    s = s >= a ? s + 1900 : s + 2e3;

    if (!t$Y(s, l, n)) {
      return {
        meta: {},
        valid: false
      };
    }

    return {
      meta: {},
      valid: t$15(r)
    };
  }

  function F() {
    var F = ["AR", "BA", "BG", "BR", "CH", "CL", "CN", "CO", "CZ", "DK", "EE", "ES", "FI", "FR", "HK", "HR", "ID", "IE", "IL", "IS", "KR", "LT", "LV", "ME", "MK", "MX", "MY", "NL", "NO", "PE", "PL", "RO", "RS", "SE", "SI", "SK", "SM", "TH", "TR", "TW", "UY", "ZA"];
    return {
      validate: function validate(P) {
        if (P.value === "") {
          return {
            valid: true
          };
        }

        var Y = Object.assign({}, {
          message: ""
        }, P.options);
        var Z = P.value.substr(0, 2);

        if ("function" === typeof Y.country) {
          Z = Y.country.call(this);
        } else {
          Z = Y.country;
        }

        if (F.indexOf(Z) === -1) {
          return {
            valid: true
          };
        }

        var G = {
          meta: {},
          valid: true
        };

        switch (Z.toLowerCase()) {
          case "ar":
            G = t$R(P.value);
            break;

          case "ba":
            G = r$a(P.value);
            break;

          case "bg":
            G = e$x(P.value);
            break;

          case "br":
            G = t$P(P.value);
            break;

          case "ch":
            G = t$O(P.value);
            break;

          case "cl":
            G = e$w(P.value);
            break;

          case "cn":
            G = r$9(P.value);
            break;

          case "co":
            G = t$N(P.value);
            break;

          case "cz":
            G = e$v(P.value);
            break;

          case "dk":
            G = e$u(P.value);
            break;

          case "ee":
            G = r$8(P.value);
            break;

          case "es":
            G = t$M(P.value);
            break;

          case "fi":
            G = s$7(P.value);
            break;

          case "fr":
            G = t$L(P.value);
            break;

          case "hk":
            G = t$K(P.value);
            break;

          case "hr":
            G = o$1(P.value);
            break;

          case "id":
            G = e$t(P.value);
            break;

          case "ie":
            G = t$J(P.value);
            break;

          case "il":
            G = e$s(P.value);
            break;

          case "is":
            G = e$r(P.value);
            break;

          case "kr":
            G = e$q(P.value);
            break;

          case "lt":
            G = r$8(P.value);
            break;

          case "lv":
            G = e$p(P.value);
            break;

          case "me":
            G = r$7(P.value);
            break;

          case "mk":
            G = r$6(P.value);
            break;

          case "mx":
            G = O(P.value);
            break;

          case "my":
            G = s$6(P.value);
            break;

          case "nl":
            G = e$o(P.value);
            break;

          case "no":
            G = t$I(P.value);
            break;

          case "pe":
            G = t$H(P.value);
            break;

          case "pl":
            G = t$G(P.value);
            break;

          case "ro":
            G = e$n(P.value);
            break;

          case "rs":
            G = r$5(P.value);
            break;

          case "se":
            G = r$4(P.value);
            break;

          case "si":
            G = r$3(P.value);
            break;

          case "sk":
            G = e$v(P.value);
            break;

          case "sm":
            G = t$F(P.value);
            break;

          case "th":
            G = t$E(P.value);
            break;

          case "tr":
            G = t$D(P.value);
            break;

          case "tw":
            G = t$C(P.value);
            break;

          case "uy":
            G = t$B(P.value);
            break;

          case "za":
            G = r$2(P.value);
            break;
        }

        var V = r$d(P.l10n && P.l10n.id ? Y.message || P.l10n.id.country : Y.message, P.l10n && P.l10n.id && P.l10n.id.countries ? P.l10n.id.countries[Z.toUpperCase()] : Z.toUpperCase());
        return Object.assign({}, {
          message: V
        }, G);
      }
    };
  }

  function t$A() {
    return {
      validate: function validate(t) {
        if (t.value === "") {
          return {
            valid: true
          };
        }

        switch (true) {
          case /^\d{15}$/.test(t.value):
          case /^\d{2}-\d{6}-\d{6}-\d{1}$/.test(t.value):
          case /^\d{2}\s\d{6}\s\d{6}\s\d{1}$/.test(t.value):
            return {
              valid: t$15(t.value.replace(/[^0-9]/g, ""))
            };

          case /^\d{14}$/.test(t.value):
          case /^\d{16}$/.test(t.value):
          case /^\d{2}-\d{6}-\d{6}(|-\d{2})$/.test(t.value):
          case /^\d{2}\s\d{6}\s\d{6}(|\s\d{2})$/.test(t.value):
            return {
              valid: true
            };

          default:
            return {
              valid: false
            };
        }
      }
    };
  }

  function e$m() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        if (!/^IMO \d{7}$/i.test(e.value)) {
          return {
            valid: false
          };
        }

        var t = e.value.replace(/^.*(\d{7})$/, "$1");
        var r = 0;

        for (var _e = 6; _e >= 1; _e--) {
          r += parseInt(t.slice(6 - _e, -_e), 10) * (_e + 1);
        }

        return {
          valid: r % 10 === parseInt(t.charAt(6), 10)
        };
      }
    };
  }

  function e$l() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            meta: {
              type: null
            },
            valid: true
          };
        }

        var t;

        switch (true) {
          case /^\d{9}[\dX]$/.test(e.value):
          case e.value.length === 13 && /^(\d+)-(\d+)-(\d+)-([\dX])$/.test(e.value):
          case e.value.length === 13 && /^(\d+)\s(\d+)\s(\d+)\s([\dX])$/.test(e.value):
            t = "ISBN10";
            break;

          case /^(978|979)\d{9}[\dX]$/.test(e.value):
          case e.value.length === 17 && /^(978|979)-(\d+)-(\d+)-(\d+)-([\dX])$/.test(e.value):
          case e.value.length === 17 && /^(978|979)\s(\d+)\s(\d+)\s(\d+)\s([\dX])$/.test(e.value):
            t = "ISBN13";
            break;

          default:
            return {
              meta: {
                type: null
              },
              valid: false
            };
        }

        var a = e.value.replace(/[^0-9X]/gi, "").split("");
        var l = a.length;
        var s = 0;
        var d;
        var u;

        switch (t) {
          case "ISBN10":
            s = 0;

            for (d = 0; d < l - 1; d++) {
              s += parseInt(a[d], 10) * (10 - d);
            }

            u = 11 - s % 11;

            if (u === 11) {
              u = 0;
            } else if (u === 10) {
              u = "X";
            }

            return {
              meta: {
                type: t
              },
              valid: "".concat(u) === a[l - 1]
            };

          case "ISBN13":
            s = 0;

            for (d = 0; d < l - 1; d++) {
              s += d % 2 === 0 ? parseInt(a[d], 10) : parseInt(a[d], 10) * 3;
            }

            u = 10 - s % 10;

            if (u === 10) {
              u = "0";
            }

            return {
              meta: {
                type: t
              },
              valid: "".concat(u) === a[l - 1]
            };
        }
      }
    };
  }

  function M() {
    var M = "AF|AX|AL|DZ|AS|AD|AO|AI|AQ|AG|AR|AM|AW|AU|AT|AZ|BS|BH|BD|BB|BY|BE|BZ|BJ|BM|BT|BO|BQ|BA|BW|" + "BV|BR|IO|BN|BG|BF|BI|KH|CM|CA|CV|KY|CF|TD|CL|CN|CX|CC|CO|KM|CG|CD|CK|CR|CI|HR|CU|CW|CY|CZ|DK|DJ|DM|DO|EC|EG|" + "SV|GQ|ER|EE|ET|FK|FO|FJ|FI|FR|GF|PF|TF|GA|GM|GE|DE|GH|GI|GR|GL|GD|GP|GU|GT|GG|GN|GW|GY|HT|HM|VA|HN|HK|HU|IS|" + "IN|ID|IR|IQ|IE|IM|IL|IT|JM|JP|JE|JO|KZ|KE|KI|KP|KR|KW|KG|LA|LV|LB|LS|LR|LY|LI|LT|LU|MO|MK|MG|MW|MY|MV|ML|MT|" + "MH|MQ|MR|MU|YT|MX|FM|MD|MC|MN|ME|MS|MA|MZ|MM|NA|NR|NP|NL|NC|NZ|NI|NE|NG|NU|NF|MP|NO|OM|PK|PW|PS|PA|PG|PY|PE|" + "PH|PN|PL|PT|PR|QA|RE|RO|RU|RW|BL|SH|KN|LC|MF|PM|VC|WS|SM|ST|SA|SN|RS|SC|SL|SG|SX|SK|SI|SB|SO|ZA|GS|SS|ES|LK|" + "SD|SR|SJ|SZ|SE|CH|SY|TW|TJ|TZ|TH|TL|TG|TK|TO|TT|TN|TR|TM|TC|TV|UG|UA|AE|GB|US|UM|UY|UZ|VU|VE|VN|VG|VI|WF|EH|" + "YE|ZM|ZW";
    return {
      validate: function validate(t) {
        if (t.value === "") {
          return {
            valid: true
          };
        }

        var S = t.value.toUpperCase();
        var A = new RegExp("^(" + M + ")[0-9A-Z]{10}$");

        if (!A.test(t.value)) {
          return {
            valid: false
          };
        }

        var G = S.length;
        var C = "";
        var T;

        for (T = 0; T < G - 1; T++) {
          var _M = S.charCodeAt(T);

          C += _M > 57 ? (_M - 55).toString() : S.charAt(T);
        }

        var e = "";
        var B = C.length;
        var E = B % 2 !== 0 ? 0 : 1;

        for (T = 0; T < B; T++) {
          e += parseInt(C[T], 10) * (T % 2 === E ? 2 : 1) + "";
        }

        var N = 0;

        for (T = 0; T < e.length; T++) {
          N += parseInt(e.charAt(T), 10);
        }

        N = (10 - N % 10) % 10;
        return {
          valid: "".concat(N) === S.charAt(G - 1)
        };
      }
    };
  }

  function e$k() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            meta: null,
            valid: true
          };
        }

        var t;

        switch (true) {
          case /^M\d{9}$/.test(e.value):
          case /^M-\d{4}-\d{4}-\d{1}$/.test(e.value):
          case /^M\s\d{4}\s\d{4}\s\d{1}$/.test(e.value):
            t = "ISMN10";
            break;

          case /^9790\d{9}$/.test(e.value):
          case /^979-0-\d{4}-\d{4}-\d{1}$/.test(e.value):
          case /^979\s0\s\d{4}\s\d{4}\s\d{1}$/.test(e.value):
            t = "ISMN13";
            break;

          default:
            return {
              meta: null,
              valid: false
            };
        }

        var a = e.value;

        if ("ISMN10" === t) {
          a = "9790".concat(a.substr(1));
        }

        a = a.replace(/[^0-9]/gi, "");
        var s = 0;
        var l = a.length;
        var d = [1, 3];

        for (var _e = 0; _e < l - 1; _e++) {
          s += parseInt(a.charAt(_e), 10) * d[_e % 2];
        }

        s = (10 - s % 10) % 10;
        return {
          meta: {
            type: t
          },
          valid: "".concat(s) === a.charAt(l - 1)
        };
      }
    };
  }

  function e$j() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        if (!/^\d{4}-\d{3}[\dX]$/.test(e.value)) {
          return {
            valid: false
          };
        }

        var t = e.value.replace(/[^0-9X]/gi, "").split("");
        var l = t.length;
        var r = 0;

        if (t[7] === "X") {
          t[7] = "10";
        }

        for (var _e = 0; _e < l; _e++) {
          r += parseInt(t[_e], 10) * (8 - _e);
        }

        return {
          valid: r % 11 === 0
        };
      }
    };
  }

  function a$1() {
    return {
      validate: function validate(a) {
        return {
          valid: a.value === "" || /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/.test(a.value) || /^([0-9A-Fa-f]{4}\.){2}([0-9A-Fa-f]{4})$/.test(a.value)
        };
      }
    };
  }

  function e$i() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        var r = e.value;

        if (/^[0-9A-F]{15}$/i.test(r) || /^[0-9A-F]{2}[- ][0-9A-F]{6}[- ][0-9A-F]{6}[- ][0-9A-F]$/i.test(r) || /^\d{19}$/.test(r) || /^\d{5}[- ]\d{5}[- ]\d{4}[- ]\d{4}[- ]\d$/.test(r)) {
          var _e = r.charAt(r.length - 1).toUpperCase();

          r = r.replace(/[- ]/g, "");

          if (r.match(/^\d*$/i)) {
            return {
              valid: t$15(r)
            };
          }

          r = r.slice(0, -1);
          var a = "";
          var i;

          for (i = 1; i <= 13; i += 2) {
            a += (parseInt(r.charAt(i), 16) * 2).toString(16);
          }

          var l = 0;

          for (i = 0; i < a.length; i++) {
            l += parseInt(a.charAt(i), 16);
          }

          return {
            valid: l % 10 === 0 ? _e === "0" : _e === ((Math.floor((l + 10) / 10) * 10 - l) * 2).toString(16).toUpperCase()
          };
        }

        if (/^[0-9A-F]{14}$/i.test(r) || /^[0-9A-F]{2}[- ][0-9A-F]{6}[- ][0-9A-F]{6}$/i.test(r) || /^\d{18}$/.test(r) || /^\d{5}[- ]\d{5}[- ]\d{4}[- ]\d{4}$/.test(r)) {
          return {
            valid: true
          };
        }

        return {
          valid: false
        };
      }
    };
  }

  function e$h() {
    var e = ["AE", "BG", "BR", "CN", "CZ", "DE", "DK", "ES", "FR", "GB", "IN", "MA", "NL", "PK", "RO", "RU", "SK", "TH", "US", "VE"];
    return {
      validate: function validate(t) {
        if (t.value === "") {
          return {
            valid: true
          };
        }

        var a = Object.assign({}, {
          message: ""
        }, t.options);
        var d = t.value.trim();
        var r = d.substr(0, 2);

        if ("function" === typeof a.country) {
          r = a.country.call(this);
        } else {
          r = a.country;
        }

        if (!r || e.indexOf(r.toUpperCase()) === -1) {
          return {
            valid: true
          };
        }

        var c = true;

        switch (r.toUpperCase()) {
          case "AE":
            c = /^(((\+|00)?971[\s.-]?(\(0\)[\s.-]?)?|0)(\(5(0|2|5|6)\)|5(0|2|5|6)|2|3|4|6|7|9)|60)([\s.-]?[0-9]){7}$/.test(d);
            break;

          case "BG":
            c = /^(0|359|00)(((700|900)[0-9]{5}|((800)[0-9]{5}|(800)[0-9]{4}))|(87|88|89)([0-9]{7})|((2[0-9]{7})|(([3-9][0-9])(([0-9]{6})|([0-9]{5})))))$/.test(d.replace(/\+|\s|-|\/|\(|\)/gi, ""));
            break;

          case "BR":
            c = /^(([\d]{4}[-.\s]{1}[\d]{2,3}[-.\s]{1}[\d]{2}[-.\s]{1}[\d]{2})|([\d]{4}[-.\s]{1}[\d]{3}[-.\s]{1}[\d]{4})|((\(?\+?[0-9]{2}\)?\s?)?(\(?\d{2}\)?\s?)?\d{4,5}[-.\s]?\d{4}))$/.test(d);
            break;

          case "CN":
            c = /^((00|\+)?(86(?:-| )))?((\d{11})|(\d{3}[- ]{1}\d{4}[- ]{1}\d{4})|((\d{2,4}[- ]){1}(\d{7,8}|(\d{3,4}[- ]{1}\d{4}))([- ]{1}\d{1,4})?))$/.test(d);
            break;

          case "CZ":
            c = /^(((00)([- ]?)|\+)(420)([- ]?))?((\d{3})([- ]?)){2}(\d{3})$/.test(d);
            break;

          case "DE":
            c = /^(((((((00|\+)49[ \-/]?)|0)[1-9][0-9]{1,4})[ \-/]?)|((((00|\+)49\()|\(0)[1-9][0-9]{1,4}\)[ \-/]?))[0-9]{1,7}([ \-/]?[0-9]{1,5})?)$/.test(d);
            break;

          case "DK":
            c = /^(\+45|0045|\(45\))?\s?[2-9](\s?\d){7}$/.test(d);
            break;

          case "ES":
            c = /^(?:(?:(?:\+|00)34\D?))?(?:5|6|7|8|9)(?:\d\D?){8}$/.test(d);
            break;

          case "FR":
            c = /^(?:(?:(?:\+|00)33[ ]?(?:\(0\)[ ]?)?)|0){1}[1-9]{1}([ .-]?)(?:\d{2}\1?){3}\d{2}$/.test(d);
            break;

          case "GB":
            c = /^\(?(?:(?:0(?:0|11)\)?[\s-]?\(?|\+)44\)?[\s-]?\(?(?:0\)?[\s-]?\(?)?|0)(?:\d{2}\)?[\s-]?\d{4}[\s-]?\d{4}|\d{3}\)?[\s-]?\d{3}[\s-]?\d{3,4}|\d{4}\)?[\s-]?(?:\d{5}|\d{3}[\s-]?\d{3})|\d{5}\)?[\s-]?\d{4,5}|8(?:00[\s-]?11[\s-]?11|45[\s-]?46[\s-]?4\d))(?:(?:[\s-]?(?:x|ext\.?\s?|#)\d+)?)$/.test(d);
            break;

          case "IN":
            c = /((\+?)((0[ -]+)*|(91 )*)(\d{12}|\d{10}))|\d{5}([- ]*)\d{6}/.test(d);
            break;

          case "MA":
            c = /^(?:(?:(?:\+|00)212[\s]?(?:[\s]?\(0\)[\s]?)?)|0){1}(?:5[\s.-]?[2-3]|6[\s.-]?[13-9]){1}[0-9]{1}(?:[\s.-]?\d{2}){3}$/.test(d);
            break;

          case "NL":
            c = /^((\+|00(\s|\s?-\s?)?)31(\s|\s?-\s?)?(\(0\)[-\s]?)?|0)[1-9]((\s|\s?-\s?)?[0-9])((\s|\s?-\s?)?[0-9])((\s|\s?-\s?)?[0-9])\s?[0-9]\s?[0-9]\s?[0-9]\s?[0-9]\s?[0-9]$/gm.test(d);
            break;

          case "PK":
            c = /^0?3[0-9]{2}[0-9]{7}$/.test(d);
            break;

          case "RO":
            c = /^(\+4|)?(07[0-8]{1}[0-9]{1}|02[0-9]{2}|03[0-9]{2}){1}?(\s|\.|-)?([0-9]{3}(\s|\.|-|)){2}$/g.test(d);
            break;

          case "RU":
            c = /^((8|\+7|007)[-./ ]?)?([(/.]?\d{3}[)/.]?[-./ ]?)?[\d\-./ ]{7,10}$/g.test(d);
            break;

          case "SK":
            c = /^(((00)([- ]?)|\+)(421)([- ]?))?((\d{3})([- ]?)){2}(\d{3})$/.test(d);
            break;

          case "TH":
            c = /^0\(?([6|8-9]{2})*-([0-9]{3})*-([0-9]{4})$/.test(d);
            break;

          case "VE":
            c = /^0(?:2(?:12|4[0-9]|5[1-9]|6[0-9]|7[0-8]|8[1-35-8]|9[1-5]|3[45789])|4(?:1[246]|2[46]))\d{7}$/.test(d);
            break;

          case "US":
          default:
            c = /^(?:(1-?)|(\+1 ?))?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$/.test(d);
            break;
        }

        return {
          message: r$d(t.l10n && t.l10n.phone ? a.message || t.l10n.phone.country : a.message, t.l10n && t.l10n.phone && t.l10n.phone.countries ? t.l10n.phone.countries[r] : r),
          valid: c
        };
      }
    };
  }

  function e$g() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        if (!/^\d{9}$/.test(e.value)) {
          return {
            valid: false
          };
        }

        var t = 0;

        for (var a = 0; a < e.value.length; a += 3) {
          t += parseInt(e.value.charAt(a), 10) * 3 + parseInt(e.value.charAt(a + 1), 10) * 7 + parseInt(e.value.charAt(a + 2), 10);
        }

        return {
          valid: t !== 0 && t % 10 === 0
        };
      }
    };
  }

  function t$z() {
    return {
      validate: function validate(t) {
        if (t.value === "") {
          return {
            valid: true
          };
        }

        var e = t.value.toUpperCase();

        if (!/^[0-9A-Z]{7}$/.test(e)) {
          return {
            valid: false
          };
        }

        var r = [1, 3, 1, 7, 3, 9, 1];
        var a = e.length;
        var l = 0;

        for (var _t = 0; _t < a - 1; _t++) {
          l += r[_t] * parseInt(e.charAt(_t), 36);
        }

        l = (10 - l % 10) % 10;
        return {
          valid: "".concat(l) === e.charAt(a - 1)
        };
      }
    };
  }

  function e$f() {
    return {
      validate: function validate(e) {
        return {
          valid: e.value === "" || /^\d{9}$/.test(e.value) && t$15(e.value)
        };
      }
    };
  }

  function e$e() {
    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        var t = e.value.length;
        var l = 0;
        var r;

        for (var a = 0; a < t; a++) {
          r = parseInt(e.value.charAt(a), 10);

          if (a % 2 === 0) {
            r = r * 2;

            if (r > 9) {
              r -= 9;
            }
          }

          l += r;
        }

        return {
          valid: l % 10 === 0
        };
      }
    };
  }

  function e$d() {
    var e = function e(t, _e) {
      var s = Math.pow(10, _e);
      var a = t * s;
      var n;

      switch (true) {
        case a === 0:
          n = 0;
          break;

        case a > 0:
          n = 1;
          break;

        case a < 0:
          n = -1;
          break;
      }

      var r = a % 1 === .5 * n;
      return r ? (Math.floor(a) + (n > 0 ? 1 : 0)) / s : Math.round(a) / s;
    };

    var s = function s(t, _s) {
      if (_s === 0) {
        return 1;
      }

      var a = "".concat(t).split(".");
      var n = "".concat(_s).split(".");
      var r = (a.length === 1 ? 0 : a[1].length) + (n.length === 1 ? 0 : n[1].length);
      return e(t - _s * Math.floor(t / _s), r);
    };

    return {
      validate: function validate(e) {
        if (e.value === "") {
          return {
            valid: true
          };
        }

        var a = parseFloat(e.value);

        if (isNaN(a) || !isFinite(a)) {
          return {
            valid: false
          };
        }

        var n = Object.assign({}, {
          baseValue: 0,
          message: "",
          step: 1
        }, e.options);
        var r = s(a - n.baseValue, n.step);
        return {
          message: r$d(e.l10n ? n.message || e.l10n.step["default"] : n.message, "".concat(n.step)),
          valid: r === 0 || r === n.step
        };
      }
    };
  }

  function s$5() {
    return {
      validate: function validate(s) {
        if (s.value === "") {
          return {
            valid: true
          };
        }

        var A = Object.assign({}, {
          message: ""
        }, s.options);
        var i = {
          3: /^[0-9A-F]{8}-[0-9A-F]{4}-3[0-9A-F]{3}-[0-9A-F]{4}-[0-9A-F]{12}$/i,
          4: /^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i,
          5: /^[0-9A-F]{8}-[0-9A-F]{4}-5[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i,
          all: /^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i
        };
        var n = A.version ? "".concat(A.version) : "all";
        return {
          message: A.version ? r$d(s.l10n ? A.message || s.l10n.uuid.version : A.message, A.version) : s.l10n ? s.l10n.uuid["default"] : A.message,
          valid: null === i[n] ? true : i[n].test(s.value)
        };
      }
    };
  }

  function t$y(t) {
    var e = t.replace("-", "");

    if (/^AR[0-9]{11}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{11}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
    var a = 0;

    for (var _t = 0; _t < 10; _t++) {
      a += parseInt(e.charAt(_t), 10) * r[_t];
    }

    a = 11 - a % 11;

    if (a === 11) {
      a = 0;
    }

    return {
      meta: {},
      valid: "".concat(a) === e.substr(10)
    };
  }

  function t$x(t) {
    var e = t;

    if (/^ATU[0-9]{8}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^U[0-9]{8}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    e = e.substr(1);
    var r = [1, 2, 1, 2, 1, 2, 1];
    var s = 0;
    var a = 0;

    for (var _t = 0; _t < 7; _t++) {
      a = parseInt(e.charAt(_t), 10) * r[_t];

      if (a > 9) {
        a = Math.floor(a / 10) + a % 10;
      }

      s += a;
    }

    s = 10 - (s + 4) % 10;

    if (s === 10) {
      s = 0;
    }

    return {
      meta: {},
      valid: "".concat(s) === e.substr(7, 1)
    };
  }

  function t$w(t) {
    var e = t;

    if (/^BE[0]?[0-9]{9}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0]?[0-9]{9}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    if (e.length === 9) {
      e = "0".concat(e);
    }

    if (e.substr(1, 1) === "0") {
      return {
        meta: {},
        valid: false
      };
    }

    var s = parseInt(e.substr(0, 8), 10) + parseInt(e.substr(8, 2), 10);
    return {
      meta: {},
      valid: s % 97 === 0
    };
  }

  function r$1(r) {
    var e = r;

    if (/^BG[0-9]{9,10}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{9,10}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var s = 0;
    var n = 0;

    if (e.length === 9) {
      for (n = 0; n < 8; n++) {
        s += parseInt(e.charAt(n), 10) * (n + 1);
      }

      s = s % 11;

      if (s === 10) {
        s = 0;

        for (n = 0; n < 8; n++) {
          s += parseInt(e.charAt(n), 10) * (n + 3);
        }

        s = s % 11;
      }

      s = s % 10;
      return {
        meta: {},
        valid: "".concat(s) === e.substr(8)
      };
    } else {
      var _r = function _r(r) {
        var e = parseInt(r.substr(0, 2), 10) + 1900;
        var s = parseInt(r.substr(2, 2), 10);
        var n = parseInt(r.substr(4, 2), 10);

        if (s > 40) {
          e += 100;
          s -= 40;
        } else if (s > 20) {
          e -= 100;
          s -= 20;
        }

        if (!t$Y(e, s, n)) {
          return false;
        }

        var a = [2, 4, 8, 5, 10, 9, 7, 3, 6];
        var l = 0;

        for (var _t = 0; _t < 9; _t++) {
          l += parseInt(r.charAt(_t), 10) * a[_t];
        }

        l = l % 11 % 10;
        return "".concat(l) === r.substr(9, 1);
      };

      var _s = function _s(t) {
        var r = [21, 19, 17, 13, 11, 9, 7, 3, 1];
        var e = 0;

        for (var _s2 = 0; _s2 < 9; _s2++) {
          e += parseInt(t.charAt(_s2), 10) * r[_s2];
        }

        e = e % 10;
        return "".concat(e) === t.substr(9, 1);
      };

      var _n = function _n(t) {
        var r = [4, 3, 2, 7, 6, 5, 4, 3, 2];
        var e = 0;

        for (var _s3 = 0; _s3 < 9; _s3++) {
          e += parseInt(t.charAt(_s3), 10) * r[_s3];
        }

        e = 11 - e % 11;

        if (e === 10) {
          return false;
        }

        if (e === 11) {
          e = 0;
        }

        return "".concat(e) === t.substr(9, 1);
      };

      return {
        meta: {},
        valid: _r(e) || _s(e) || _n(e)
      };
    }
  }

  function t$v(t) {
    if (t === "") {
      return {
        meta: {},
        valid: true
      };
    }

    var e = t.replace(/[^\d]+/g, "");

    if (e === "" || e.length !== 14) {
      return {
        meta: {},
        valid: false
      };
    }

    if (e === "00000000000000" || e === "11111111111111" || e === "22222222222222" || e === "33333333333333" || e === "44444444444444" || e === "55555555555555" || e === "66666666666666" || e === "77777777777777" || e === "88888888888888" || e === "99999999999999") {
      return {
        meta: {},
        valid: false
      };
    }

    var r = e.length - 2;
    var a = e.substring(0, r);
    var l = e.substring(r);
    var n = 0;
    var i = r - 7;
    var s;

    for (s = r; s >= 1; s--) {
      n += parseInt(a.charAt(r - s), 10) * i--;

      if (i < 2) {
        i = 9;
      }
    }

    var f = n % 11 < 2 ? 0 : 11 - n % 11;

    if (f !== parseInt(l.charAt(0), 10)) {
      return {
        meta: {},
        valid: false
      };
    }

    r = r + 1;
    a = e.substring(0, r);
    n = 0;
    i = r - 7;

    for (s = r; s >= 1; s--) {
      n += parseInt(a.charAt(r - s), 10) * i--;

      if (i < 2) {
        i = 9;
      }
    }

    f = n % 11 < 2 ? 0 : 11 - n % 11;
    return {
      meta: {},
      valid: f === parseInt(l.charAt(1), 10)
    };
  }

  function t$u(t) {
    var e = t;

    if (/^CHE[0-9]{9}(MWST|TVA|IVA|TPV)?$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^E[0-9]{9}(MWST|TVA|IVA|TPV)?$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    e = e.substr(1);
    var r = [5, 4, 3, 2, 7, 6, 5, 4];
    var s = 0;

    for (var _t = 0; _t < 8; _t++) {
      s += parseInt(e.charAt(_t), 10) * r[_t];
    }

    s = 11 - s % 11;

    if (s === 10) {
      return {
        meta: {},
        valid: false
      };
    }

    if (s === 11) {
      s = 0;
    }

    return {
      meta: {},
      valid: "".concat(s) === e.substr(8, 1)
    };
  }

  function t$t(t) {
    var e = t;

    if (/^CY[0-5|9][0-9]{7}[A-Z]$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-5|9][0-9]{7}[A-Z]$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    if (e.substr(0, 2) === "12") {
      return {
        meta: {},
        valid: false
      };
    }

    var r = 0;
    var s = {
      0: 1,
      1: 0,
      2: 5,
      3: 7,
      4: 9,
      5: 13,
      6: 15,
      7: 17,
      8: 19,
      9: 21
    };

    for (var _t = 0; _t < 8; _t++) {
      var a = parseInt(e.charAt(_t), 10);

      if (_t % 2 === 0) {
        a = s["".concat(a)];
      }

      r += a;
    }

    return {
      meta: {},
      valid: "".concat("ABCDEFGHIJKLMNOPQRSTUVWXYZ"[r % 26]) === e.substr(8, 1)
    };
  }

  function e$c(e) {
    var r = e;

    if (/^CZ[0-9]{8,10}$/.test(r)) {
      r = r.substr(2);
    }

    if (!/^[0-9]{8,10}$/.test(r)) {
      return {
        meta: {},
        valid: false
      };
    }

    var a = 0;
    var s = 0;

    if (r.length === 8) {
      if ("".concat(r.charAt(0)) === "9") {
        return {
          meta: {},
          valid: false
        };
      }

      a = 0;

      for (s = 0; s < 7; s++) {
        a += parseInt(r.charAt(s), 10) * (8 - s);
      }

      a = 11 - a % 11;

      if (a === 10) {
        a = 0;
      }

      if (a === 11) {
        a = 1;
      }

      return {
        meta: {},
        valid: "".concat(a) === r.substr(7, 1)
      };
    } else if (r.length === 9 && "".concat(r.charAt(0)) === "6") {
      a = 0;

      for (s = 0; s < 7; s++) {
        a += parseInt(r.charAt(s + 1), 10) * (8 - s);
      }

      a = 11 - a % 11;

      if (a === 10) {
        a = 0;
      }

      if (a === 11) {
        a = 1;
      }

      a = [8, 7, 6, 5, 4, 3, 2, 1, 0, 9, 10][a - 1];
      return {
        meta: {},
        valid: "".concat(a) === r.substr(8, 1)
      };
    } else if (r.length === 9 || r.length === 10) {
      var _e = 1900 + parseInt(r.substr(0, 2), 10);

      var _a = parseInt(r.substr(2, 2), 10) % 50 % 20;

      var _s = parseInt(r.substr(4, 2), 10);

      if (r.length === 9) {
        if (_e >= 1980) {
          _e -= 100;
        }

        if (_e > 1953) {
          return {
            meta: {},
            valid: false
          };
        }
      } else if (_e < 1954) {
        _e += 100;
      }

      if (!t$Y(_e, _a, _s)) {
        return {
          meta: {},
          valid: false
        };
      }

      if (r.length === 10) {
        var _t = parseInt(r.substr(0, 9), 10) % 11;

        if (_e < 1985) {
          _t = _t % 10;
        }

        return {
          meta: {},
          valid: "".concat(_t) === r.substr(9, 1)
        };
      }

      return {
        meta: {},
        valid: true
      };
    }

    return {
      meta: {},
      valid: false
    };
  }

  function e$b(e) {
    var r = e;

    if (/^DE[0-9]{9}$/.test(r)) {
      r = r.substr(2);
    }

    if (!/^[0-9]{9}$/.test(r)) {
      return {
        meta: {},
        valid: false
      };
    }

    return {
      meta: {},
      valid: t$14(r)
    };
  }

  function t$s(t) {
    var e = t;

    if (/^DK[0-9]{8}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{8}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = 0;
    var a = [2, 7, 6, 5, 4, 3, 2, 1];

    for (var _t = 0; _t < 8; _t++) {
      r += parseInt(e.charAt(_t), 10) * a[_t];
    }

    return {
      meta: {},
      valid: r % 11 === 0
    };
  }

  function t$r(t) {
    var e = t;

    if (/^EE[0-9]{9}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{9}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = 0;
    var a = [3, 7, 1, 3, 7, 1, 3, 7, 1];

    for (var _t = 0; _t < 9; _t++) {
      r += parseInt(e.charAt(_t), 10) * a[_t];
    }

    return {
      meta: {},
      valid: r % 10 === 0
    };
  }

  function t$q(t) {
    var e = t;

    if (/^ES[0-9A-Z][0-9]{7}[0-9A-Z]$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9A-Z][0-9]{7}[0-9A-Z]$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var s = function s(t) {
      var e = parseInt(t.substr(0, 8), 10);
      return "".concat("TRWAGMYFPDXBNJZSQVHLCKE"[e % 23]) === t.substr(8, 1);
    };

    var r = function r(t) {
      var e = ["XYZ".indexOf(t.charAt(0)), t.substr(1)].join("");
      var s = "TRWAGMYFPDXBNJZSQVHLCKE"[parseInt(e, 10) % 23];
      return "".concat(s) === t.substr(8, 1);
    };

    var n = function n(t) {
      var e = t.charAt(0);
      var s;

      if ("KLM".indexOf(e) !== -1) {
        s = parseInt(t.substr(1, 8), 10);
        s = "TRWAGMYFPDXBNJZSQVHLCKE"[s % 23];
        return "".concat(s) === t.substr(8, 1);
      } else if ("ABCDEFGHJNPQRSUVW".indexOf(e) !== -1) {
        var _e = [2, 1, 2, 1, 2, 1, 2];
        var _s = 0;
        var _r = 0;

        for (var _n = 0; _n < 7; _n++) {
          _r = parseInt(t.charAt(_n + 1), 10) * _e[_n];

          if (_r > 9) {
            _r = Math.floor(_r / 10) + _r % 10;
          }

          _s += _r;
        }

        _s = 10 - _s % 10;

        if (_s === 10) {
          _s = 0;
        }

        return "".concat(_s) === t.substr(8, 1) || "JABCDEFGHI"[_s] === t.substr(8, 1);
      }

      return false;
    };

    var a = e.charAt(0);

    if (/^[0-9]$/.test(a)) {
      return {
        meta: {
          type: "DNI"
        },
        valid: s(e)
      };
    } else if (/^[XYZ]$/.test(a)) {
      return {
        meta: {
          type: "NIE"
        },
        valid: r(e)
      };
    } else {
      return {
        meta: {
          type: "CIF"
        },
        valid: n(e)
      };
    }
  }

  function t$p(t) {
    var e = t;

    if (/^FI[0-9]{8}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{8}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = [7, 9, 10, 5, 8, 4, 2, 1];
    var a = 0;

    for (var _t = 0; _t < 8; _t++) {
      a += parseInt(e.charAt(_t), 10) * r[_t];
    }

    return {
      meta: {},
      valid: a % 11 === 0
    };
  }

  function e$a(e) {
    var r = e;

    if (/^FR[0-9A-Z]{2}[0-9]{9}$/.test(r)) {
      r = r.substr(2);
    }

    if (!/^[0-9A-Z]{2}[0-9]{9}$/.test(r)) {
      return {
        meta: {},
        valid: false
      };
    }

    if (r.substr(2, 4) !== "000") {
      return {
        meta: {},
        valid: t$15(r.substr(2))
      };
    }

    if (/^[0-9]{2}$/.test(r.substr(0, 2))) {
      return {
        meta: {},
        valid: r.substr(0, 2) === "".concat(parseInt(r.substr(2) + "12", 10) % 97)
      };
    } else {
      var _t = "0123456789ABCDEFGHJKLMNPQRSTUVWXYZ";

      var _e;

      if (/^[0-9]$/.test(r.charAt(0))) {
        _e = _t.indexOf(r.charAt(0)) * 24 + _t.indexOf(r.charAt(1)) - 10;
      } else {
        _e = _t.indexOf(r.charAt(0)) * 34 + _t.indexOf(r.charAt(1)) - 100;
      }

      return {
        meta: {},
        valid: (parseInt(r.substr(2), 10) + 1 + Math.floor(_e / 11)) % 11 === _e % 11
      };
    }
  }

  function t$o(t) {
    var s = t;

    if (/^GB[0-9]{9}$/.test(s) || /^GB[0-9]{12}$/.test(s) || /^GBGD[0-9]{3}$/.test(s) || /^GBHA[0-9]{3}$/.test(s) || /^GB(GD|HA)8888[0-9]{5}$/.test(s)) {
      s = s.substr(2);
    }

    if (!/^[0-9]{9}$/.test(s) && !/^[0-9]{12}$/.test(s) && !/^GD[0-9]{3}$/.test(s) && !/^HA[0-9]{3}$/.test(s) && !/^(GD|HA)8888[0-9]{5}$/.test(s)) {
      return {
        meta: {},
        valid: false
      };
    }

    var e = s.length;

    if (e === 5) {
      var _t = s.substr(0, 2);

      var _e = parseInt(s.substr(2), 10);

      return {
        meta: {},
        valid: "GD" === _t && _e < 500 || "HA" === _t && _e >= 500
      };
    } else if (e === 11 && ("GD8888" === s.substr(0, 6) || "HA8888" === s.substr(0, 6))) {
      if ("GD" === s.substr(0, 2) && parseInt(s.substr(6, 3), 10) >= 500 || "HA" === s.substr(0, 2) && parseInt(s.substr(6, 3), 10) < 500) {
        return {
          meta: {},
          valid: false
        };
      }

      return {
        meta: {},
        valid: parseInt(s.substr(6, 3), 10) % 97 === parseInt(s.substr(9, 2), 10)
      };
    } else if (e === 9 || e === 12) {
      var _t2 = [8, 7, 6, 5, 4, 3, 2, 10, 1];
      var _e2 = 0;

      for (var _r = 0; _r < 9; _r++) {
        _e2 += parseInt(s.charAt(_r), 10) * _t2[_r];
      }

      _e2 = _e2 % 97;
      var r = parseInt(s.substr(0, 3), 10) >= 100 ? _e2 === 0 || _e2 === 42 || _e2 === 55 : _e2 === 0;
      return {
        meta: {},
        valid: r
      };
    }

    return {
      meta: {},
      valid: true
    };
  }

  function t$n(t) {
    var e = t;

    if (/^(GR|EL)[0-9]{9}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{9}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    if (e.length === 8) {
      e = "0".concat(e);
    }

    var r = [256, 128, 64, 32, 16, 8, 4, 2];
    var s = 0;

    for (var _t = 0; _t < 8; _t++) {
      s += parseInt(e.charAt(_t), 10) * r[_t];
    }

    s = s % 11 % 10;
    return {
      meta: {},
      valid: "".concat(s) === e.substr(8, 1)
    };
  }

  function e$9(e) {
    var r = e;

    if (/^HR[0-9]{11}$/.test(r)) {
      r = r.substr(2);
    }

    if (!/^[0-9]{11}$/.test(r)) {
      return {
        meta: {},
        valid: false
      };
    }

    return {
      meta: {},
      valid: t$14(r)
    };
  }

  function t$m(t) {
    var e = t;

    if (/^HU[0-9]{8}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{8}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = [9, 7, 3, 1, 9, 7, 3, 1];
    var a = 0;

    for (var _t = 0; _t < 8; _t++) {
      a += parseInt(e.charAt(_t), 10) * r[_t];
    }

    return {
      meta: {},
      valid: a % 10 === 0
    };
  }

  function t$l(t) {
    var e = t;

    if (/^IE[0-9][0-9A-Z*+][0-9]{5}[A-Z]{1,2}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9][0-9A-Z*+][0-9]{5}[A-Z]{1,2}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = function r(t) {
      var e = t;

      while (e.length < 7) {
        e = "0".concat(e);
      }

      var r = "WABCDEFGHIJKLMNOPQRSTUV";
      var s = 0;

      for (var _t = 0; _t < 7; _t++) {
        s += parseInt(e.charAt(_t), 10) * (8 - _t);
      }

      s += 9 * r.indexOf(e.substr(7));
      return r[s % 23];
    };

    if (/^[0-9]+$/.test(e.substr(0, 7))) {
      return {
        meta: {},
        valid: e.charAt(7) === r("".concat(e.substr(0, 7)).concat(e.substr(8)))
      };
    } else if ("ABCDEFGHIJKLMNOPQRSTUVWXYZ+*".indexOf(e.charAt(1)) !== -1) {
      return {
        meta: {},
        valid: e.charAt(7) === r("".concat(e.substr(2, 5)).concat(e.substr(0, 1)))
      };
    }

    return {
      meta: {},
      valid: true
    };
  }

  function t$k(t) {
    var e = t;

    if (/^IS[0-9]{5,6}$/.test(e)) {
      e = e.substr(2);
    }

    return {
      meta: {},
      valid: /^[0-9]{5,6}$/.test(e)
    };
  }

  function e$8(e) {
    var r = e;

    if (/^IT[0-9]{11}$/.test(r)) {
      r = r.substr(2);
    }

    if (!/^[0-9]{11}$/.test(r)) {
      return {
        meta: {},
        valid: false
      };
    }

    if (parseInt(r.substr(0, 7), 10) === 0) {
      return {
        meta: {},
        valid: false
      };
    }

    var a = parseInt(r.substr(7, 3), 10);

    if (a < 1 || a > 201 && a !== 999 && a !== 888) {
      return {
        meta: {},
        valid: false
      };
    }

    return {
      meta: {},
      valid: t$15(r)
    };
  }

  function t$j(t) {
    var e = t;

    if (/^LT([0-9]{7}1[0-9]|[0-9]{10}1[0-9])$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^([0-9]{7}1[0-9]|[0-9]{10}1[0-9])$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = e.length;
    var a = 0;
    var l;

    for (l = 0; l < r - 1; l++) {
      a += parseInt(e.charAt(l), 10) * (1 + l % 9);
    }

    var f = a % 11;

    if (f === 10) {
      a = 0;

      for (l = 0; l < r - 1; l++) {
        a += parseInt(e.charAt(l), 10) * (1 + (l + 2) % 9);
      }
    }

    f = f % 11 % 10;
    return {
      meta: {},
      valid: "".concat(f) === e.charAt(r - 1)
    };
  }

  function t$i(t) {
    var e = t;

    if (/^LU[0-9]{8}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{8}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    return {
      meta: {},
      valid: "".concat(parseInt(e.substr(0, 6), 10) % 89) === e.substr(6, 2)
    };
  }

  function e$7(e) {
    var r = e;

    if (/^LV[0-9]{11}$/.test(r)) {
      r = r.substr(2);
    }

    if (!/^[0-9]{11}$/.test(r)) {
      return {
        meta: {},
        valid: false
      };
    }

    var s = parseInt(r.charAt(0), 10);
    var a = r.length;
    var n = 0;
    var l = [];
    var i;

    if (s > 3) {
      n = 0;
      l = [9, 1, 4, 8, 3, 10, 2, 5, 7, 6, 1];

      for (i = 0; i < a; i++) {
        n += parseInt(r.charAt(i), 10) * l[i];
      }

      n = n % 11;
      return {
        meta: {},
        valid: n === 3
      };
    } else {
      var _e = parseInt(r.substr(0, 2), 10);

      var _s = parseInt(r.substr(2, 2), 10);

      var f = parseInt(r.substr(4, 2), 10);
      f = f + 1800 + parseInt(r.charAt(6), 10) * 100;

      if (!t$Y(f, _s, _e)) {
        return {
          meta: {},
          valid: false
        };
      }

      n = 0;
      l = [10, 5, 8, 4, 2, 1, 6, 3, 7, 9];

      for (i = 0; i < a - 1; i++) {
        n += parseInt(r.charAt(i), 10) * l[i];
      }

      n = (n + 1) % 11 % 10;
      return {
        meta: {},
        valid: "".concat(n) === r.charAt(a - 1)
      };
    }
  }

  function t$h(t) {
    var e = t;

    if (/^MT[0-9]{8}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{8}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = [3, 4, 6, 7, 8, 9, 10, 1];
    var a = 0;

    for (var _t = 0; _t < 8; _t++) {
      a += parseInt(e.charAt(_t), 10) * r[_t];
    }

    return {
      meta: {},
      valid: a % 37 === 0
    };
  }

  function t$g(t) {
    return t.split("").map(function (t) {
      var n = t.charCodeAt(0);
      return n >= 65 && n <= 90 ? n - 55 : t;
    }).join("").split("").map(function (t) {
      return parseInt(t, 10);
    });
  }

  function n(n) {
    var e = t$g(n);
    var r = 0;
    var o = e.length;

    for (var _t = 0; _t < o - 1; ++_t) {
      r = (r + e[_t]) * 10 % 97;
    }

    r += e[o - 1];
    return r % 97 === 1;
  }

  function e$6(e) {
    var i = e;

    if (/^NL[0-9]{9}B[0-9]{2}$/.test(i)) {
      i = i.substr(2);
    }

    if (!/^[0-9]{9}B[0-9]{2}$/.test(i)) {
      return {
        meta: {},
        valid: false
      };
    }

    var o = i.substr(0, 9);
    return {
      meta: {},
      valid: e$o(o).valid || n("NL".concat(i))
    };
  }

  function t$f(t) {
    var e = t;

    if (/^NO[0-9]{9}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{9}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = [3, 2, 7, 6, 5, 4, 3, 2];
    var s = 0;

    for (var _t = 0; _t < 8; _t++) {
      s += parseInt(e.charAt(_t), 10) * r[_t];
    }

    s = 11 - s % 11;

    if (s === 11) {
      s = 0;
    }

    return {
      meta: {},
      valid: "".concat(s) === e.substr(8, 1)
    };
  }

  function t$e(t) {
    var e = t;

    if (/^PL[0-9]{10}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{10}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = [6, 5, 7, 2, 3, 4, 5, 6, 7, -1];
    var a = 0;

    for (var _t = 0; _t < 10; _t++) {
      a += parseInt(e.charAt(_t), 10) * r[_t];
    }

    return {
      meta: {},
      valid: a % 11 === 0
    };
  }

  function t$d(t) {
    var e = t;

    if (/^PT[0-9]{9}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{9}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = [9, 8, 7, 6, 5, 4, 3, 2];
    var s = 0;

    for (var _t = 0; _t < 8; _t++) {
      s += parseInt(e.charAt(_t), 10) * r[_t];
    }

    s = 11 - s % 11;

    if (s > 9) {
      s = 0;
    }

    return {
      meta: {},
      valid: "".concat(s) === e.substr(8, 1)
    };
  }

  function t$c(t) {
    var e = t;

    if (/^RO[1-9][0-9]{1,9}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[1-9][0-9]{1,9}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var s = e.length;
    var r = [7, 5, 3, 2, 1, 7, 5, 3, 2].slice(10 - s);
    var l = 0;

    for (var _t = 0; _t < s - 1; _t++) {
      l += parseInt(e.charAt(_t), 10) * r[_t];
    }

    l = 10 * l % 11 % 10;
    return {
      meta: {},
      valid: "".concat(l) === e.substr(s - 1, 1)
    };
  }

  function t$b(t) {
    var e = t;

    if (/^RS[0-9]{9}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[0-9]{9}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = 10;
    var a = 0;

    for (var _t = 0; _t < 8; _t++) {
      a = (parseInt(e.charAt(_t), 10) + r) % 10;

      if (a === 0) {
        a = 10;
      }

      r = 2 * a % 11;
    }

    return {
      meta: {},
      valid: (r + parseInt(e.substr(8, 1), 10)) % 10 === 1
    };
  }

  function t$a(t) {
    var e = t;

    if (/^RU([0-9]{10}|[0-9]{12})$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^([0-9]{10}|[0-9]{12})$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = 0;

    if (e.length === 10) {
      var _t = [2, 4, 10, 3, 5, 9, 4, 6, 8, 0];
      var s = 0;

      for (r = 0; r < 10; r++) {
        s += parseInt(e.charAt(r), 10) * _t[r];
      }

      s = s % 11;

      if (s > 9) {
        s = s % 10;
      }

      return {
        meta: {},
        valid: "".concat(s) === e.substr(9, 1)
      };
    } else if (e.length === 12) {
      var _t2 = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0];
      var _s = [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0];
      var a = 0;
      var l = 0;

      for (r = 0; r < 11; r++) {
        a += parseInt(e.charAt(r), 10) * _t2[r];
        l += parseInt(e.charAt(r), 10) * _s[r];
      }

      a = a % 11;

      if (a > 9) {
        a = a % 10;
      }

      l = l % 11;

      if (l > 9) {
        l = l % 10;
      }

      return {
        meta: {},
        valid: "".concat(a) === e.substr(10, 1) && "".concat(l) === e.substr(11, 1)
      };
    }

    return {
      meta: {},
      valid: true
    };
  }

  function e$5(e) {
    var r = e;

    if (/^SE[0-9]{10}01$/.test(r)) {
      r = r.substr(2);
    }

    if (!/^[0-9]{10}01$/.test(r)) {
      return {
        meta: {},
        valid: false
      };
    }

    r = r.substr(0, 10);
    return {
      meta: {},
      valid: t$15(r)
    };
  }

  function t$9(t) {
    var e = t.match(/^(SI)?([1-9][0-9]{7})$/);

    if (!e) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = e[1] ? t.substr(2) : t;
    var a = [8, 7, 6, 5, 4, 3, 2];
    var s = 0;

    for (var _t = 0; _t < 7; _t++) {
      s += parseInt(r.charAt(_t), 10) * a[_t];
    }

    s = 11 - s % 11;

    if (s === 10) {
      s = 0;
    }

    return {
      meta: {},
      valid: "".concat(s) === r.substr(7, 1)
    };
  }

  function t$8(t) {
    var e = t;

    if (/^SK[1-9][0-9][(2-4)|(6-9)][0-9]{7}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[1-9][0-9][(2-4)|(6-9)][0-9]{7}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    return {
      meta: {},
      valid: parseInt(e, 10) % 11 === 0
    };
  }

  function t$7(t) {
    var e = t;

    if (/^VE[VEJPG][0-9]{9}$/.test(e)) {
      e = e.substr(2);
    }

    if (!/^[VEJPG][0-9]{9}$/.test(e)) {
      return {
        meta: {},
        valid: false
      };
    }

    var r = {
      E: 8,
      G: 20,
      J: 12,
      P: 16,
      V: 4
    };
    var s = [3, 2, 7, 6, 5, 4, 3, 2];
    var a = r[e.charAt(0)];

    for (var _t = 0; _t < 8; _t++) {
      a += parseInt(e.charAt(_t + 1), 10) * s[_t];
    }

    a = 11 - a % 11;

    if (a === 11 || a === 10) {
      a = 0;
    }

    return {
      meta: {},
      valid: "".concat(a) === e.substr(9, 1)
    };
  }

  function t$6(t) {
    var e = t;

    if (/^ZA4[0-9]{9}$/.test(e)) {
      e = e.substr(2);
    }

    return {
      meta: {},
      valid: /^4[0-9]{9}$/.test(e)
    };
  }

  function x() {
    var x = ["AR", "AT", "BE", "BG", "BR", "CH", "CY", "CZ", "DE", "DK", "EE", "EL", "ES", "FI", "FR", "GB", "GR", "HR", "HU", "IE", "IS", "IT", "LT", "LU", "LV", "MT", "NL", "NO", "PL", "PT", "RO", "RU", "RS", "SE", "SK", "SI", "VE", "ZA"];
    return {
      validate: function validate(D) {
        var F = D.value;

        if (F === "") {
          return {
            valid: true
          };
        }

        var K = Object.assign({}, {
          message: ""
        }, D.options);
        var N = F.substr(0, 2);

        if ("function" === typeof K.country) {
          N = K.country.call(this);
        } else {
          N = K.country;
        }

        if (x.indexOf(N) === -1) {
          return {
            valid: true
          };
        }

        var P = {
          meta: {},
          valid: true
        };

        switch (N.toLowerCase()) {
          case "ar":
            P = t$y(F);
            break;

          case "at":
            P = t$x(F);
            break;

          case "be":
            P = t$w(F);
            break;

          case "bg":
            P = r$1(F);
            break;

          case "br":
            P = t$v(F);
            break;

          case "ch":
            P = t$u(F);
            break;

          case "cy":
            P = t$t(F);
            break;

          case "cz":
            P = e$c(F);
            break;

          case "de":
            P = e$b(F);
            break;

          case "dk":
            P = t$s(F);
            break;

          case "ee":
            P = t$r(F);
            break;

          case "el":
            P = t$n(F);
            break;

          case "es":
            P = t$q(F);
            break;

          case "fi":
            P = t$p(F);
            break;

          case "fr":
            P = e$a(F);
            break;

          case "gb":
            P = t$o(F);
            break;

          case "gr":
            P = t$n(F);
            break;

          case "hr":
            P = e$9(F);
            break;

          case "hu":
            P = t$m(F);
            break;

          case "ie":
            P = t$l(F);
            break;

          case "is":
            P = t$k(F);
            break;

          case "it":
            P = e$8(F);
            break;

          case "lt":
            P = t$j(F);
            break;

          case "lu":
            P = t$i(F);
            break;

          case "lv":
            P = e$7(F);
            break;

          case "mt":
            P = t$h(F);
            break;

          case "nl":
            P = e$6(F);
            break;

          case "no":
            P = t$f(F);
            break;

          case "pl":
            P = t$e(F);
            break;

          case "pt":
            P = t$d(F);
            break;

          case "ro":
            P = t$c(F);
            break;

          case "rs":
            P = t$b(F);
            break;

          case "ru":
            P = t$a(F);
            break;

          case "se":
            P = e$5(F);
            break;

          case "si":
            P = t$9(F);
            break;

          case "sk":
            P = t$8(F);
            break;

          case "ve":
            P = t$7(F);
            break;

          case "za":
            P = t$6(F);
            break;
        }

        var Z = r$d(D.l10n && D.l10n.vat ? K.message || D.l10n.vat.country : K.message, D.l10n && D.l10n.vat && D.l10n.vat.countries ? D.l10n.vat.countries[N.toUpperCase()] : N.toUpperCase());
        return Object.assign({}, {
          message: Z
        }, P);
      }
    };
  }

  function t$5() {
    return {
      validate: function validate(t) {
        if (t.value === "") {
          return {
            valid: true
          };
        }

        if (!/^[a-hj-npr-z0-9]{8}[0-9xX][a-hj-npr-z0-9]{8}$/i.test(t.value)) {
          return {
            valid: false
          };
        }

        var e = t.value.toUpperCase();
        var r = {
          A: 1,
          B: 2,
          C: 3,
          D: 4,
          E: 5,
          F: 6,
          G: 7,
          H: 8,
          J: 1,
          K: 2,
          L: 3,
          M: 4,
          N: 5,
          P: 7,
          R: 9,
          S: 2,
          T: 3,
          U: 4,
          V: 5,
          W: 6,
          X: 7,
          Y: 8,
          Z: 9,
          0: 0,
          1: 1,
          2: 2,
          3: 3,
          4: 4,
          5: 5,
          6: 6,
          7: 7,
          8: 8,
          9: 9
        };
        var a = [8, 7, 6, 5, 4, 3, 2, 10, 0, 9, 8, 7, 6, 5, 4, 3, 2];
        var l = e.length;
        var n = 0;

        for (var _t = 0; _t < l; _t++) {
          n += r["".concat(e.charAt(_t))] * a[_t];
        }

        var u = "".concat(n % 11);

        if (u === "10") {
          u = "X";
        }

        return {
          valid: u === e.charAt(8)
        };
      }
    };
  }

  function s$4() {
    var s = ["AT", "BG", "BR", "CA", "CH", "CZ", "DE", "DK", "ES", "FR", "GB", "IE", "IN", "IT", "MA", "NL", "PL", "PT", "RO", "RU", "SE", "SG", "SK", "US"];

    var a = function a(e) {
      var s = "[ABCDEFGHIJKLMNOPRSTUWYZ]";
      var a = "[ABCDEFGHKLMNOPQRSTUVWXY]";
      var t = "[ABCDEFGHJKPMNRSTUVWXY]";
      var r = "[ABEHMNPRVWXY]";
      var u = "[ABDEFGHJLNPQRSTUWXYZ]";
      var c = [new RegExp("^(".concat(s, "{1}").concat(a, "?[0-9]{1,2})(\\s*)([0-9]{1}").concat(u, "{2})$"), "i"), new RegExp("^(".concat(s, "{1}[0-9]{1}").concat(t, "{1})(\\s*)([0-9]{1}").concat(u, "{2})$"), "i"), new RegExp("^(".concat(s, "{1}").concat(a, "{1}?[0-9]{1}").concat(r, "{1})(\\s*)([0-9]{1}").concat(u, "{2})$"), "i"), new RegExp("^(BF1)(\\s*)([0-6]{1}[ABDEFGHJLNPQRST]{1}[ABDEFGHJLNPQRSTUWZYZ]{1})$", "i"), /^(GIR)(\s*)(0AA)$/i, /^(BFPO)(\s*)([0-9]{1,4})$/i, /^(BFPO)(\s*)(c\/o\s*[0-9]{1,3})$/i, /^([A-Z]{4})(\s*)(1ZZ)$/i, /^(AI-2640)$/i];

      for (var _i = 0, _c = c; _i < _c.length; _i++) {
        var _s = _c[_i];

        if (_s.test(e)) {
          return true;
        }
      }

      return false;
    };

    return {
      validate: function validate(t) {
        var r = Object.assign({}, {
          message: ""
        }, t.options);

        if (t.value === "" || !r.country) {
          return {
            valid: true
          };
        }

        var u = t.value.substr(0, 2);

        if ("function" === typeof r.country) {
          u = r.country.call(this);
        } else {
          u = r.country;
        }

        if (!u || s.indexOf(u.toUpperCase()) === -1) {
          return {
            valid: true
          };
        }

        var c = false;
        u = u.toUpperCase();

        switch (u) {
          case "AT":
            c = /^([1-9]{1})(\d{3})$/.test(t.value);
            break;

          case "BG":
            c = /^([1-9]{1}[0-9]{3})$/.test(t.value);
            break;

          case "BR":
            c = /^(\d{2})([.]?)(\d{3})([-]?)(\d{3})$/.test(t.value);
            break;

          case "CA":
            c = /^(?:A|B|C|E|G|H|J|K|L|M|N|P|R|S|T|V|X|Y){1}[0-9]{1}(?:A|B|C|E|G|H|J|K|L|M|N|P|R|S|T|V|W|X|Y|Z){1}\s?[0-9]{1}(?:A|B|C|E|G|H|J|K|L|M|N|P|R|S|T|V|W|X|Y|Z){1}[0-9]{1}$/i.test(t.value);
            break;

          case "CH":
            c = /^([1-9]{1})(\d{3})$/.test(t.value);
            break;

          case "CZ":
            c = /^(\d{3})([ ]?)(\d{2})$/.test(t.value);
            break;

          case "DE":
            c = /^(?!01000|99999)(0[1-9]\d{3}|[1-9]\d{4})$/.test(t.value);
            break;

          case "DK":
            c = /^(DK(-|\s)?)?\d{4}$/i.test(t.value);
            break;

          case "ES":
            c = /^(?:0[1-9]|[1-4][0-9]|5[0-2])\d{3}$/.test(t.value);
            break;

          case "FR":
            c = /^[0-9]{5}$/i.test(t.value);
            break;

          case "GB":
            c = a(t.value);
            break;

          case "IN":
            c = /^\d{3}\s?\d{3}$/.test(t.value);
            break;

          case "IE":
            c = /^(D6W|[ACDEFHKNPRTVWXY]\d{2})\s[0-9ACDEFHKNPRTVWXY]{4}$/.test(t.value);
            break;

          case "IT":
            c = /^(I-|IT-)?\d{5}$/i.test(t.value);
            break;

          case "MA":
            c = /^[1-9][0-9]{4}$/i.test(t.value);
            break;

          case "NL":
            c = /^[1-9][0-9]{3} ?(?!sa|sd|ss)[a-z]{2}$/i.test(t.value);
            break;

          case "PL":
            c = /^[0-9]{2}-[0-9]{3}$/.test(t.value);
            break;

          case "PT":
            c = /^[1-9]\d{3}-\d{3}$/.test(t.value);
            break;

          case "RO":
            c = /^(0[1-8]{1}|[1-9]{1}[0-5]{1})?[0-9]{4}$/i.test(t.value);
            break;

          case "RU":
            c = /^[0-9]{6}$/i.test(t.value);
            break;

          case "SE":
            c = /^(S-)?\d{3}\s?\d{2}$/i.test(t.value);
            break;

          case "SG":
            c = /^([0][1-9]|[1-6][0-9]|[7]([0-3]|[5-9])|[8][0-2])(\d{4})$/i.test(t.value);
            break;

          case "SK":
            c = /^(\d{3})([ ]?)(\d{2})$/.test(t.value);
            break;

          case "US":
          default:
            c = /^\d{4,5}([-]?\d{4})?$/.test(t.value);
            break;
        }

        return {
          message: r$d(t.l10n && t.l10n.zipCode ? r.message || t.l10n.zipCode.country : r.message, t.l10n && t.l10n.zipCode && t.l10n.zipCode.countries ? t.l10n.zipCode.countries[u] : u),
          valid: c
        };
      }
    };
  }

  var s$3 = {
    between: s$9,
    blank: t$10,
    callback: o$4,
    choice: t$_,
    creditCard: l$2,
    date: n$1,
    different: o$3,
    digits: e$G,
    emailAddress: t$X,
    file: e$F,
    greaterThan: a$7,
    identical: o$2,
    integer: a$6,
    ip: d,
    lessThan: s$8,
    notEmpty: t$W,
    numeric: a$5,
    promise: r$c,
    regexp: e$E,
    remote: a$4,
    stringCase: e$C,
    stringLength: t$U,
    uri: t$T,
    base64: a$3,
    bic: a$2,
    color: e$B,
    cusip: t$S,
    ean: e$A,
    ein: e$z,
    grid: r$b,
    hex: e$y,
    iban: Z,
    id: F,
    imei: t$A,
    imo: e$m,
    isbn: e$l,
    isin: M,
    ismn: e$k,
    issn: e$j,
    mac: a$1,
    meid: e$i,
    phone: e$h,
    rtn: e$g,
    sedol: t$z,
    siren: e$f,
    siret: e$e,
    step: e$d,
    uuid: s$5,
    vat: x,
    vin: t$5,
    zipCode: s$4
  };

  var l$1 = /*#__PURE__*/function () {
    function l(i, s) {
      _classCallCheck(this, l);

      this.elements = {};
      this.ee = s$a();
      this.filter = t$11();
      this.plugins = {};
      this.results = new Map();
      this.validators = {};
      this.form = i;
      this.fields = s;
    }

    _createClass(l, [{
      key: "on",
      value: function on(e, t) {
        this.ee.on(e, t);
        return this;
      }
    }, {
      key: "off",
      value: function off(e, t) {
        this.ee.off(e, t);
        return this;
      }
    }, {
      key: "emit",
      value: function emit(e) {
        var _this$ee;

        for (var _len = arguments.length, t = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          t[_key - 1] = arguments[_key];
        }

        (_this$ee = this.ee).emit.apply(_this$ee, [e].concat(t));

        return this;
      }
    }, {
      key: "registerPlugin",
      value: function registerPlugin(e, t) {
        if (this.plugins[e]) {
          throw new Error("The plguin ".concat(e, " is registered"));
        }

        t.setCore(this);
        t.install();
        this.plugins[e] = t;
        return this;
      }
    }, {
      key: "deregisterPlugin",
      value: function deregisterPlugin(e) {
        var t = this.plugins[e];

        if (t) {
          t.uninstall();
        }

        delete this.plugins[e];
        return this;
      }
    }, {
      key: "registerValidator",
      value: function registerValidator(e, t) {
        if (this.validators[e]) {
          throw new Error("The validator ".concat(e, " is registered"));
        }

        this.validators[e] = t;
        return this;
      }
    }, {
      key: "registerFilter",
      value: function registerFilter(e, t) {
        this.filter.add(e, t);
        return this;
      }
    }, {
      key: "deregisterFilter",
      value: function deregisterFilter(e, t) {
        this.filter.remove(e, t);
        return this;
      }
    }, {
      key: "executeFilter",
      value: function executeFilter(e, t, i) {
        return this.filter.execute(e, t, i);
      }
    }, {
      key: "addField",
      value: function addField(e, t) {
        var i = Object.assign({}, {
          selector: "",
          validators: {}
        }, t);
        this.fields[e] = this.fields[e] ? {
          selector: i.selector || this.fields[e].selector,
          validators: Object.assign({}, this.fields[e].validators, i.validators)
        } : i;
        this.elements[e] = this.queryElements(e);
        this.emit("core.field.added", {
          elements: this.elements[e],
          field: e,
          options: this.fields[e]
        });
        return this;
      }
    }, {
      key: "removeField",
      value: function removeField(e) {
        if (!this.fields[e]) {
          throw new Error("The field ".concat(e, " validators are not defined. Please ensure the field is added first"));
        }

        var t = this.elements[e];
        var i = this.fields[e];
        delete this.elements[e];
        delete this.fields[e];
        this.emit("core.field.removed", {
          elements: t,
          field: e,
          options: i
        });
        return this;
      }
    }, {
      key: "validate",
      value: function validate() {
        var _this = this;

        this.emit("core.form.validating", {
          formValidation: this
        });
        return this.filter.execute("validate-pre", Promise.resolve(), []).then(function () {
          return Promise.all(Object.keys(_this.fields).map(function (e) {
            return _this.validateField(e);
          })).then(function (e) {
            switch (true) {
              case e.indexOf("Invalid") !== -1:
                _this.emit("core.form.invalid", {
                  formValidation: _this
                });

                return Promise.resolve("Invalid");

              case e.indexOf("NotValidated") !== -1:
                _this.emit("core.form.notvalidated", {
                  formValidation: _this
                });

                return Promise.resolve("NotValidated");

              default:
                _this.emit("core.form.valid", {
                  formValidation: _this
                });

                return Promise.resolve("Valid");
            }
          });
        });
      }
    }, {
      key: "validateField",
      value: function validateField(e) {
        var _this2 = this;

        var t = this.results.get(e);

        if (t === "Valid" || t === "Invalid") {
          return Promise.resolve(t);
        }

        this.emit("core.field.validating", e);
        var i = this.elements[e];

        if (i.length === 0) {
          this.emit("core.field.valid", e);
          return Promise.resolve("Valid");
        }

        var s = i[0].getAttribute("type");

        if ("radio" === s || "checkbox" === s || i.length === 1) {
          return this.validateElement(e, i[0]);
        } else {
          return Promise.all(i.map(function (t) {
            return _this2.validateElement(e, t);
          })).then(function (t) {
            switch (true) {
              case t.indexOf("Invalid") !== -1:
                _this2.emit("core.field.invalid", e);

                _this2.results.set(e, "Invalid");

                return Promise.resolve("Invalid");

              case t.indexOf("NotValidated") !== -1:
                _this2.emit("core.field.notvalidated", e);

                _this2.results["delete"](e);

                return Promise.resolve("NotValidated");

              default:
                _this2.emit("core.field.valid", e);

                _this2.results.set(e, "Valid");

                return Promise.resolve("Valid");
            }
          });
        }
      }
    }, {
      key: "validateElement",
      value: function validateElement(e, t) {
        var _this3 = this;

        this.results["delete"](e);
        var i = this.elements[e];
        var s = this.filter.execute("element-ignored", false, [e, t, i]);

        if (s) {
          this.emit("core.element.ignored", {
            element: t,
            elements: i,
            field: e
          });
          return Promise.resolve("Ignored");
        }

        var _l = this.fields[e].validators;
        this.emit("core.element.validating", {
          element: t,
          elements: i,
          field: e
        });
        var r = Object.keys(_l).map(function (i) {
          return function () {
            return _this3.executeValidator(e, t, i, _l[i]);
          };
        });
        return this.waterfall(r).then(function (s) {
          var _l2 = s.indexOf("Invalid") === -1;

          _this3.emit("core.element.validated", {
            element: t,
            elements: i,
            field: e,
            valid: _l2
          });

          var r = t.getAttribute("type");

          if ("radio" === r || "checkbox" === r || i.length === 1) {
            _this3.emit(_l2 ? "core.field.valid" : "core.field.invalid", e);
          }

          return Promise.resolve(_l2 ? "Valid" : "Invalid");
        })["catch"](function (s) {
          _this3.emit("core.element.notvalidated", {
            element: t,
            elements: i,
            field: e
          });

          return Promise.resolve(s);
        });
      }
    }, {
      key: "executeValidator",
      value: function executeValidator(e, t, i, s) {
        var _this4 = this;

        var _l3 = this.elements[e];
        var r = this.filter.execute("validator-name", i, [i, e]);
        s.message = this.filter.execute("validator-message", s.message, [this.locale, e, r]);

        if (!this.validators[r] || s.enabled === false) {
          this.emit("core.validator.validated", {
            element: t,
            elements: _l3,
            field: e,
            result: this.normalizeResult(e, r, {
              valid: true
            }),
            validator: r
          });
          return Promise.resolve("Valid");
        }

        var a = this.validators[r];
        var d = this.getElementValue(e, t, r);
        var o = this.filter.execute("field-should-validate", true, [e, t, d, i]);

        if (!o) {
          this.emit("core.validator.notvalidated", {
            element: t,
            elements: _l3,
            field: e,
            validator: i
          });
          return Promise.resolve("NotValidated");
        }

        this.emit("core.validator.validating", {
          element: t,
          elements: _l3,
          field: e,
          validator: i
        });
        var n = a().validate({
          element: t,
          elements: _l3,
          field: e,
          l10n: this.localization,
          options: s,
          value: d
        });
        var h = "function" === typeof n["then"];

        if (h) {
          return n.then(function (s) {
            var r = _this4.normalizeResult(e, i, s);

            _this4.emit("core.validator.validated", {
              element: t,
              elements: _l3,
              field: e,
              result: r,
              validator: i
            });

            return r.valid ? "Valid" : "Invalid";
          });
        } else {
          var _s = this.normalizeResult(e, i, n);

          this.emit("core.validator.validated", {
            element: t,
            elements: _l3,
            field: e,
            result: _s,
            validator: i
          });
          return Promise.resolve(_s.valid ? "Valid" : "Invalid");
        }
      }
    }, {
      key: "getElementValue",
      value: function getElementValue(e, t, s) {
        var _l4 = e$H(this.form, e, t, this.elements[e]);

        return this.filter.execute("field-value", _l4, [_l4, e, t, s]);
      }
    }, {
      key: "getElements",
      value: function getElements(e) {
        return this.elements[e];
      }
    }, {
      key: "getFields",
      value: function getFields() {
        return this.fields;
      }
    }, {
      key: "getFormElement",
      value: function getFormElement() {
        return this.form;
      }
    }, {
      key: "getLocale",
      value: function getLocale() {
        return this.locale;
      }
    }, {
      key: "getPlugin",
      value: function getPlugin(e) {
        return this.plugins[e];
      }
    }, {
      key: "updateFieldStatus",
      value: function updateFieldStatus(e, t, i) {
        var _this5 = this;

        var s = this.elements[e];

        var _l5 = s[0].getAttribute("type");

        var r = "radio" === _l5 || "checkbox" === _l5 ? [s[0]] : s;
        r.forEach(function (s) {
          return _this5.updateElementStatus(e, s, t, i);
        });

        if (!i) {
          switch (t) {
            case "NotValidated":
              this.emit("core.field.notvalidated", e);
              this.results["delete"](e);
              break;

            case "Validating":
              this.emit("core.field.validating", e);
              this.results["delete"](e);
              break;

            case "Valid":
              this.emit("core.field.valid", e);
              this.results.set(e, "Valid");
              break;

            case "Invalid":
              this.emit("core.field.invalid", e);
              this.results.set(e, "Invalid");
              break;
          }
        } else if (t === "Invalid") {
          this.emit("core.field.invalid", e);
          this.results.set(e, "Invalid");
        }

        return this;
      }
    }, {
      key: "updateElementStatus",
      value: function updateElementStatus(e, t, i, s) {
        var _this6 = this;

        var _l6 = this.elements[e];
        var r = this.fields[e].validators;
        var a = s ? [s] : Object.keys(r);

        switch (i) {
          case "NotValidated":
            a.forEach(function (i) {
              return _this6.emit("core.validator.notvalidated", {
                element: t,
                elements: _l6,
                field: e,
                validator: i
              });
            });
            this.emit("core.element.notvalidated", {
              element: t,
              elements: _l6,
              field: e
            });
            break;

          case "Validating":
            a.forEach(function (i) {
              return _this6.emit("core.validator.validating", {
                element: t,
                elements: _l6,
                field: e,
                validator: i
              });
            });
            this.emit("core.element.validating", {
              element: t,
              elements: _l6,
              field: e
            });
            break;

          case "Valid":
            a.forEach(function (i) {
              return _this6.emit("core.validator.validated", {
                element: t,
                elements: _l6,
                field: e,
                result: {
                  message: r[i].message,
                  valid: true
                },
                validator: i
              });
            });
            this.emit("core.element.validated", {
              element: t,
              elements: _l6,
              field: e,
              valid: true
            });
            break;

          case "Invalid":
            a.forEach(function (i) {
              return _this6.emit("core.validator.validated", {
                element: t,
                elements: _l6,
                field: e,
                result: {
                  message: r[i].message,
                  valid: false
                },
                validator: i
              });
            });
            this.emit("core.element.validated", {
              element: t,
              elements: _l6,
              field: e,
              valid: false
            });
            break;
        }

        return this;
      }
    }, {
      key: "resetForm",
      value: function resetForm(e) {
        var _this7 = this;

        Object.keys(this.fields).forEach(function (t) {
          return _this7.resetField(t, e);
        });
        this.emit("core.form.reset", {
          formValidation: this,
          reset: e
        });
        return this;
      }
    }, {
      key: "resetField",
      value: function resetField(e, t) {
        if (t) {
          var _t = this.elements[e];

          var _i = _t[0].getAttribute("type");

          _t.forEach(function (e) {
            if ("radio" === _i || "checkbox" === _i) {
              e.removeAttribute("selected");
              e.removeAttribute("checked");
              e.checked = false;
            } else {
              e.setAttribute("value", "");

              if (e instanceof HTMLInputElement || e instanceof HTMLTextAreaElement) {
                e.value = "";
              }
            }
          });
        }

        this.updateFieldStatus(e, "NotValidated");
        this.emit("core.field.reset", {
          field: e,
          reset: t
        });
        return this;
      }
    }, {
      key: "revalidateField",
      value: function revalidateField(e) {
        if (!this.fields[e]) {
          return Promise.resolve("Ignored");
        }

        this.updateFieldStatus(e, "NotValidated");
        return this.validateField(e);
      }
    }, {
      key: "disableValidator",
      value: function disableValidator(e, t) {
        if (!this.fields[e]) {
          return this;
        }

        var i = this.elements[e];
        this.toggleValidator(false, e, t);
        this.emit("core.validator.disabled", {
          elements: i,
          field: e,
          formValidation: this,
          validator: t
        });
        return this;
      }
    }, {
      key: "enableValidator",
      value: function enableValidator(e, t) {
        if (!this.fields[e]) {
          return this;
        }

        var i = this.elements[e];
        this.toggleValidator(true, e, t);
        this.emit("core.validator.enabled", {
          elements: i,
          field: e,
          formValidation: this,
          validator: t
        });
        return this;
      }
    }, {
      key: "updateValidatorOption",
      value: function updateValidatorOption(e, t, i, s) {
        if (this.fields[e] && this.fields[e].validators && this.fields[e].validators[t]) {
          this.fields[e].validators[t][i] = s;
        }

        return this;
      }
    }, {
      key: "setFieldOptions",
      value: function setFieldOptions(e, t) {
        this.fields[e] = t;
        return this;
      }
    }, {
      key: "destroy",
      value: function destroy() {
        var _this8 = this;

        Object.keys(this.plugins).forEach(function (e) {
          return _this8.plugins[e].uninstall();
        });
        this.ee.clear();
        this.filter.clear();
        this.results.clear();
        this.plugins = {};
        return this;
      }
    }, {
      key: "setLocale",
      value: function setLocale(e, t) {
        this.locale = e;
        this.localization = t;
        return this;
      }
    }, {
      key: "waterfall",
      value: function waterfall(e) {
        return e.reduce(function (e, t) {
          return e.then(function (e) {
            return t().then(function (t) {
              e.push(t);
              return e;
            });
          });
        }, Promise.resolve([]));
      }
    }, {
      key: "queryElements",
      value: function queryElements(e) {
        var t = this.fields[e].selector ? "#" === this.fields[e].selector.charAt(0) ? "[id=\"".concat(this.fields[e].selector.substring(1), "\"]") : this.fields[e].selector : "[name=\"".concat(e, "\"]");
        return [].slice.call(this.form.querySelectorAll(t));
      }
    }, {
      key: "normalizeResult",
      value: function normalizeResult(e, t, i) {
        var s = this.fields[e].validators[t];
        return Object.assign({}, i, {
          message: i.message || (s ? s.message : "") || (this.localization && this.localization[t] && this.localization[t]["default"] ? this.localization[t]["default"] : "") || "The field ".concat(e, " is not valid")
        });
      }
    }, {
      key: "toggleValidator",
      value: function toggleValidator(e, t, i) {
        var _this9 = this;

        var s = this.fields[t].validators;

        if (i && s && s[i]) {
          this.fields[t].validators[i].enabled = e;
        } else if (!i) {
          Object.keys(s).forEach(function (i) {
            return _this9.fields[t].validators[i].enabled = e;
          });
        }

        return this.updateFieldStatus(t, "NotValidated", i);
      }
    }]);

    return l;
  }();

  function r(e, t) {
    var i = Object.assign({}, {
      fields: {},
      locale: "en_US",
      plugins: {},
      init: function init(e) {}
    }, t);
    var r = new l$1(e, i.fields);
    r.setLocale(i.locale, i.localization);
    Object.keys(i.plugins).forEach(function (e) {
      return r.registerPlugin(e, i.plugins[e]);
    });
    Object.keys(s$3).forEach(function (e) {
      return r.registerValidator(e, s$3[e]);
    });
    i.init(r);
    Object.keys(i.fields).forEach(function (e) {
      return r.addField(e, i.fields[e]);
    });
    return r;
  }

  var t$4 = /*#__PURE__*/function () {
    function t(_t) {
      _classCallCheck(this, t);

      this.opts = _t;
    }

    _createClass(t, [{
      key: "setCore",
      value: function setCore(_t2) {
        this.core = _t2;
        return this;
      }
    }, {
      key: "install",
      value: function install() {}
    }, {
      key: "uninstall",
      value: function uninstall() {}
    }]);

    return t;
  }();

  var index$2 = {
    getFieldValue: e$H
  };

  var e$4 = /*#__PURE__*/function (_t) {
    _inherits(e, _t);

    var _super = _createSuper(e);

    function e(t) {
      var _this;

      _classCallCheck(this, e);

      _this = _super.call(this, t);
      _this.opts = t || {};
      _this.validatorNameFilter = _this.getValidatorName.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(e, [{
      key: "install",
      value: function install() {
        this.core.registerFilter("validator-name", this.validatorNameFilter);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.deregisterFilter("validator-name", this.validatorNameFilter);
      }
    }, {
      key: "getValidatorName",
      value: function getValidatorName(t, _e) {
        return this.opts[t] || t;
      }
    }]);

    return e;
  }(t$4);

  var i$3 = /*#__PURE__*/function (_e) {
    _inherits(i, _e);

    var _super = _createSuper(i);

    function i() {
      var _this;

      _classCallCheck(this, i);

      _this = _super.call(this, {});
      _this.elementValidatedHandler = _this.onElementValidated.bind(_assertThisInitialized(_this));
      _this.fieldValidHandler = _this.onFieldValid.bind(_assertThisInitialized(_this));
      _this.fieldInvalidHandler = _this.onFieldInvalid.bind(_assertThisInitialized(_this));
      _this.messageDisplayedHandler = _this.onMessageDisplayed.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(i, [{
      key: "install",
      value: function install() {
        this.core.on("core.field.valid", this.fieldValidHandler).on("core.field.invalid", this.fieldInvalidHandler).on("core.element.validated", this.elementValidatedHandler).on("plugins.message.displayed", this.messageDisplayedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.off("core.field.valid", this.fieldValidHandler).off("core.field.invalid", this.fieldInvalidHandler).off("core.element.validated", this.elementValidatedHandler).off("plugins.message.displayed", this.messageDisplayedHandler);
      }
    }, {
      key: "onElementValidated",
      value: function onElementValidated(e) {
        if (e.valid) {
          e.element.setAttribute("aria-invalid", "false");
          e.element.removeAttribute("aria-describedby");
        }
      }
    }, {
      key: "onFieldValid",
      value: function onFieldValid(e) {
        var _i = this.core.getElements(e);

        if (_i) {
          _i.forEach(function (e) {
            e.setAttribute("aria-invalid", "false");
            e.removeAttribute("aria-describedby");
          });
        }
      }
    }, {
      key: "onFieldInvalid",
      value: function onFieldInvalid(e) {
        var _i2 = this.core.getElements(e);

        if (_i2) {
          _i2.forEach(function (e) {
            return e.setAttribute("aria-invalid", "true");
          });
        }
      }
    }, {
      key: "onMessageDisplayed",
      value: function onMessageDisplayed(e) {
        e.messageElement.setAttribute("role", "alert");
        e.messageElement.setAttribute("aria-hidden", "false");

        var _i3 = this.core.getElements(e.field);

        var t = _i3.indexOf(e.element);

        var l = "js-fv-".concat(e.field, "-").concat(t, "-").concat(Date.now(), "-message");
        e.messageElement.setAttribute("id", l);
        e.element.setAttribute("aria-describedby", l);
        var a = e.element.getAttribute("type");

        if ("radio" === a || "checkbox" === a) {
          _i3.forEach(function (e) {
            return e.setAttribute("aria-describedby", l);
          });
        }
      }
    }]);

    return i;
  }(t$4);

  var t$3 = /*#__PURE__*/function (_e) {
    _inherits(t, _e);

    var _super = _createSuper(t);

    function t(e) {
      var _this;

      _classCallCheck(this, t);

      _this = _super.call(this, e);
      _this.addedFields = new Map();
      _this.opts = Object.assign({}, {
        html5Input: false,
        pluginPrefix: "data-fvp-",
        prefix: "data-fv-"
      }, e);
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.fieldRemovedHandler = _this.onFieldRemoved.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(t, [{
      key: "install",
      value: function install() {
        var _this2 = this;

        this.parsePlugins();
        var e = this.parseOptions();
        Object.keys(e).forEach(function (_t) {
          if (!_this2.addedFields.has(_t)) {
            _this2.addedFields.set(_t, true);
          }

          _this2.core.addField(_t, e[_t]);
        });
        this.core.on("core.field.added", this.fieldAddedHandler).on("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.addedFields.clear();
        this.core.off("core.field.added", this.fieldAddedHandler).off("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        var _this3 = this;

        var _t2 = e.elements;

        if (!_t2 || _t2.length === 0 || this.addedFields.has(e.field)) {
          return;
        }

        this.addedFields.set(e.field, true);

        _t2.forEach(function (_t3) {
          var s = _this3.parseElement(_t3);

          if (!_this3.isEmptyOption(s)) {
            var _t12 = {
              selector: e.options.selector,
              validators: Object.assign({}, e.options.validators || {}, s.validators)
            };

            _this3.core.setFieldOptions(e.field, _t12);
          }
        });
      }
    }, {
      key: "onFieldRemoved",
      value: function onFieldRemoved(e) {
        if (e.field && this.addedFields.has(e.field)) {
          this.addedFields["delete"](e.field);
        }
      }
    }, {
      key: "parseOptions",
      value: function parseOptions() {
        var _this4 = this;

        var e = this.opts.prefix;
        var _t5 = {};
        var s = this.core.getFields();
        var a = this.core.getFormElement();
        var i = [].slice.call(a.querySelectorAll("[name], [".concat(e, "field]")));
        i.forEach(function (s) {
          var a = _this4.parseElement(s);

          if (!_this4.isEmptyOption(a)) {
            var _i = s.getAttribute("name") || s.getAttribute("".concat(e, "field"));

            _t5[_i] = Object.assign({}, _t5[_i], a);
          }
        });
        Object.keys(_t5).forEach(function (e) {
          Object.keys(_t5[e].validators).forEach(function (a) {
            _t5[e].validators[a].enabled = _t5[e].validators[a].enabled || false;

            if (s[e] && s[e].validators && s[e].validators[a]) {
              Object.assign(_t5[e].validators[a], s[e].validators[a]);
            }
          });
        });
        return Object.assign({}, s, _t5);
      }
    }, {
      key: "createPluginInstance",
      value: function createPluginInstance(e, _t6) {
        var s = e.split(".");
        var a = window || this;

        for (var _e2 = 0, _t13 = s.length; _e2 < _t13; _e2++) {
          a = a[s[_e2]];
        }

        if (typeof a !== "function") {
          throw new Error("the plugin ".concat(e, " doesn't exist"));
        }

        return new a(_t6);
      }
    }, {
      key: "parsePlugins",
      value: function parsePlugins() {
        var _this5 = this;

        var e = this.core.getFormElement();

        var _t8 = new RegExp("^".concat(this.opts.pluginPrefix, "([a-z0-9-]+)(___)*([a-z0-9-]+)*$"));

        var s = e.attributes.length;
        var a = {};

        for (var i = 0; i < s; i++) {
          var _s = e.attributes[i].name;
          var n = e.attributes[i].value;

          var r = _t8.exec(_s);

          if (r && r.length === 4) {
            var _e3 = this.toCamelCase(r[1]);

            a[_e3] = Object.assign({}, r[3] ? _defineProperty({}, this.toCamelCase(r[3]), n) : {
              enabled: "" === n || "true" === n
            }, a[_e3]);
          }
        }

        Object.keys(a).forEach(function (e) {
          var _t9 = a[e];
          var s = _t9["enabled"];
          var i = _t9["class"];

          if (s && i) {
            delete _t9["enabled"];
            delete _t9["clazz"];

            var _s2 = _this5.createPluginInstance(i, _t9);

            _this5.core.registerPlugin(e, _s2);
          }
        });
      }
    }, {
      key: "isEmptyOption",
      value: function isEmptyOption(e) {
        var _t10 = e.validators;
        return Object.keys(_t10).length === 0 && _t10.constructor === Object;
      }
    }, {
      key: "parseElement",
      value: function parseElement(e) {
        var _t11 = new RegExp("^".concat(this.opts.prefix, "([a-z0-9-]+)(___)*([a-z0-9-]+)*$"));

        var s = e.attributes.length;
        var a = {};
        var i = e.getAttribute("type");

        for (var n = 0; n < s; n++) {
          var _s3 = e.attributes[n].name;
          var r = e.attributes[n].value;

          if (this.opts.html5Input) {
            switch (true) {
              case "minlength" === _s3:
                a["stringLength"] = Object.assign({}, {
                  enabled: true,
                  min: parseInt(r, 10)
                }, a["stringLength"]);
                break;

              case "maxlength" === _s3:
                a["stringLength"] = Object.assign({}, {
                  enabled: true,
                  max: parseInt(r, 10)
                }, a["stringLength"]);
                break;

              case "pattern" === _s3:
                a["regexp"] = Object.assign({}, {
                  enabled: true,
                  regexp: r
                }, a["regexp"]);
                break;

              case "required" === _s3:
                a["notEmpty"] = Object.assign({}, {
                  enabled: true
                }, a["notEmpty"]);
                break;

              case "type" === _s3 && "color" === r:
                a["color"] = Object.assign({}, {
                  enabled: true,
                  type: "hex"
                }, a["color"]);
                break;

              case "type" === _s3 && "email" === r:
                a["emailAddress"] = Object.assign({}, {
                  enabled: true
                }, a["emailAddress"]);
                break;

              case "type" === _s3 && "url" === r:
                a["uri"] = Object.assign({}, {
                  enabled: true
                }, a["uri"]);
                break;

              case "type" === _s3 && "range" === r:
                a["between"] = Object.assign({}, {
                  enabled: true,
                  max: parseFloat(e.getAttribute("max")),
                  min: parseFloat(e.getAttribute("min"))
                }, a["between"]);
                break;

              case "min" === _s3 && i !== "date" && i !== "range":
                a["greaterThan"] = Object.assign({}, {
                  enabled: true,
                  min: parseFloat(r)
                }, a["greaterThan"]);
                break;

              case "max" === _s3 && i !== "date" && i !== "range":
                a["lessThan"] = Object.assign({}, {
                  enabled: true,
                  max: parseFloat(r)
                }, a["lessThan"]);
                break;
            }
          }

          var l = _t11.exec(_s3);

          if (l && l.length === 4) {
            var _e4 = this.toCamelCase(l[1]);

            a[_e4] = Object.assign({}, l[3] ? _defineProperty({}, this.toCamelCase(l[3]), this.normalizeValue(r)) : {
              enabled: "" === r || "true" === r
            }, a[_e4]);
          }
        }

        return {
          validators: a
        };
      }
    }, {
      key: "normalizeValue",
      value: function normalizeValue(e) {
        return e === "true" ? true : e === "false" ? false : e;
      }
    }, {
      key: "toUpperCase",
      value: function toUpperCase(e) {
        return e.charAt(1).toUpperCase();
      }
    }, {
      key: "toCamelCase",
      value: function toCamelCase(e) {
        return e.replace(/-./g, this.toUpperCase);
      }
    }]);

    return t;
  }(t$4);

  var o = /*#__PURE__*/function (_t) {
    _inherits(o, _t);

    var _super = _createSuper(o);

    function o() {
      var _this;

      _classCallCheck(this, o);

      _this = _super.call(this, {});
      _this.onValidHandler = _this.onFormValid.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(o, [{
      key: "install",
      value: function install() {
        var t = this.core.getFormElement();

        if (t.querySelectorAll('[type="submit"][name="submit"]').length) {
          throw new Error("Do not use `submit` for the name attribute of submit button");
        }

        this.core.on("core.form.valid", this.onValidHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.off("core.form.valid", this.onValidHandler);
      }
    }, {
      key: "onFormValid",
      value: function onFormValid() {
        var t = this.core.getFormElement();

        if (t instanceof HTMLFormElement) {
          t.submit();
        }
      }
    }]);

    return o;
  }(t$4);

  var e$3 = /*#__PURE__*/function (_t) {
    _inherits(e, _t);

    var _super = _createSuper(e);

    function e(t) {
      var _this;

      _classCallCheck(this, e);

      _this = _super.call(this, t);
      _this.opts = t || {};
      _this.triggerExecutedHandler = _this.onTriggerExecuted.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(e, [{
      key: "install",
      value: function install() {
        this.core.on("plugins.trigger.executed", this.triggerExecutedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.off("plugins.trigger.executed", this.triggerExecutedHandler);
      }
    }, {
      key: "onTriggerExecuted",
      value: function onTriggerExecuted(t) {
        if (this.opts[t.field]) {
          var _e3 = this.opts[t.field].split(" ");

          var _iterator = _createForOfIteratorHelper(_e3),
              _step;

          try {
            for (_iterator.s(); !(_step = _iterator.n()).done;) {
              var _t2 = _step.value;

              var _e4 = _t2.trim();

              if (this.opts[_e4]) {
                this.core.revalidateField(_e4);
              }
            }
          } catch (err) {
            _iterator.e(err);
          } finally {
            _iterator.f();
          }
        }
      }
    }]);

    return e;
  }(t$4);

  var e$2 = /*#__PURE__*/function (_t) {
    _inherits(e, _t);

    var _super = _createSuper(e);

    function e(t) {
      var _this;

      _classCallCheck(this, e);

      _this = _super.call(this, t);
      _this.opts = Object.assign({}, {
        excluded: e.defaultIgnore
      }, t);
      _this.ignoreValidationFilter = _this.ignoreValidation.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(e, [{
      key: "install",
      value: function install() {
        this.core.registerFilter("element-ignored", this.ignoreValidationFilter);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.core.deregisterFilter("element-ignored", this.ignoreValidationFilter);
      }
    }, {
      key: "ignoreValidation",
      value: function ignoreValidation(t, _e2, i) {
        return this.opts.excluded.apply(this, [t, _e2, i]);
      }
    }], [{
      key: "defaultIgnore",
      value: function defaultIgnore(t, _e, i) {
        var r = !!(_e.offsetWidth || _e.offsetHeight || _e.getClientRects().length);

        var n = _e.getAttribute("disabled");

        return n === "" || n === "disabled" || _e.getAttribute("type") === "hidden" || !r;
      }
    }]);

    return e;
  }(t$4);

  var t$2 = /*#__PURE__*/function (_e) {
    _inherits(t, _e);

    var _super = _createSuper(t);

    function t(e) {
      var _this;

      _classCallCheck(this, t);

      _this = _super.call(this, e);
      _this.statuses = new Map();
      _this.opts = Object.assign({}, {
        onStatusChanged: function onStatusChanged() {}
      }, e);
      _this.elementValidatingHandler = _this.onElementValidating.bind(_assertThisInitialized(_this));
      _this.elementValidatedHandler = _this.onElementValidated.bind(_assertThisInitialized(_this));
      _this.elementNotValidatedHandler = _this.onElementNotValidated.bind(_assertThisInitialized(_this));
      _this.elementIgnoredHandler = _this.onElementIgnored.bind(_assertThisInitialized(_this));
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.fieldRemovedHandler = _this.onFieldRemoved.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(t, [{
      key: "install",
      value: function install() {
        this.core.on("core.element.validating", this.elementValidatingHandler).on("core.element.validated", this.elementValidatedHandler).on("core.element.notvalidated", this.elementNotValidatedHandler).on("core.element.ignored", this.elementIgnoredHandler).on("core.field.added", this.fieldAddedHandler).on("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.statuses.clear();
        this.core.off("core.element.validating", this.elementValidatingHandler).off("core.element.validated", this.elementValidatedHandler).off("core.element.notvalidated", this.elementNotValidatedHandler).off("core.element.ignored", this.elementIgnoredHandler).off("core.field.added", this.fieldAddedHandler).off("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "areFieldsValid",
      value: function areFieldsValid() {
        return Array.from(this.statuses.values()).every(function (e) {
          return e === "Valid" || e === "NotValidated" || e === "Ignored";
        });
      }
    }, {
      key: "getStatuses",
      value: function getStatuses() {
        return this.statuses;
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        this.statuses.set(e.field, "NotValidated");
      }
    }, {
      key: "onFieldRemoved",
      value: function onFieldRemoved(e) {
        if (this.statuses.has(e.field)) {
          this.statuses["delete"](e.field);
        }

        this.opts.onStatusChanged(this.areFieldsValid());
      }
    }, {
      key: "onElementValidating",
      value: function onElementValidating(e) {
        this.statuses.set(e.field, "Validating");
        this.opts.onStatusChanged(false);
      }
    }, {
      key: "onElementValidated",
      value: function onElementValidated(e) {
        this.statuses.set(e.field, e.valid ? "Valid" : "Invalid");

        if (e.valid) {
          this.opts.onStatusChanged(this.areFieldsValid());
        } else {
          this.opts.onStatusChanged(false);
        }
      }
    }, {
      key: "onElementNotValidated",
      value: function onElementNotValidated(e) {
        this.statuses.set(e.field, "NotValidated");
        this.opts.onStatusChanged(false);
      }
    }, {
      key: "onElementIgnored",
      value: function onElementIgnored(e) {
        this.statuses.set(e.field, "Ignored");
        this.opts.onStatusChanged(this.areFieldsValid());
      }
    }]);

    return t;
  }(t$4);

  function s$2(s, a) {
    a.split(" ").forEach(function (a) {
      if (s.classList) {
        s.classList.add(a);
      } else if (" ".concat(s.className, " ").indexOf(" ".concat(a, " "))) {
        s.className += " ".concat(a);
      }
    });
  }

  function a(s, a) {
    a.split(" ").forEach(function (a) {
      s.classList ? s.classList.remove(a) : s.className = s.className.replace(a, "");
    });
  }

  function c(c, e) {
    var t = [];
    var f = [];
    Object.keys(e).forEach(function (s) {
      if (s) {
        e[s] ? t.push(s) : f.push(s);
      }
    });
    f.forEach(function (s) {
      return a(c, s);
    });
    t.forEach(function (a) {
      return s$2(c, a);
    });
  }

  function e$1(e, t) {
    var l = e.matches || e.webkitMatchesSelector || e["mozMatchesSelector"] || e["msMatchesSelector"];

    if (l) {
      return l.call(e, t);
    }

    var c = [].slice.call(e.parentElement.querySelectorAll(t));
    return c.indexOf(e) >= 0;
  }

  function t$1(t, l) {
    var c = t;

    while (c) {
      if (e$1(c, l)) {
        break;
      }

      c = c.parentElement;
    }

    return c;
  }

  var s$1 = /*#__PURE__*/function (_e) {
    _inherits(s, _e);

    var _super = _createSuper(s);

    function s(e) {
      var _this;

      _classCallCheck(this, s);

      _this = _super.call(this, e);
      _this.useDefaultContainer = false;
      _this.messages = new Map();
      _this.defaultContainer = document.createElement("div");
      _this.useDefaultContainer = !e || !e.container;
      _this.opts = Object.assign({}, {
        container: function container(e, t) {
          return _this.defaultContainer;
        }
      }, e);
      _this.elementIgnoredHandler = _this.onElementIgnored.bind(_assertThisInitialized(_this));
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.fieldRemovedHandler = _this.onFieldRemoved.bind(_assertThisInitialized(_this));
      _this.validatorValidatedHandler = _this.onValidatorValidated.bind(_assertThisInitialized(_this));
      _this.validatorNotValidatedHandler = _this.onValidatorNotValidated.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(s, [{
      key: "install",
      value: function install() {
        if (this.useDefaultContainer) {
          this.core.getFormElement().appendChild(this.defaultContainer);
        }

        this.core.on("core.element.ignored", this.elementIgnoredHandler).on("core.field.added", this.fieldAddedHandler).on("core.field.removed", this.fieldRemovedHandler).on("core.validator.validated", this.validatorValidatedHandler).on("core.validator.notvalidated", this.validatorNotValidatedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        if (this.useDefaultContainer) {
          this.core.getFormElement().removeChild(this.defaultContainer);
        }

        this.messages.forEach(function (e) {
          return e.parentNode.removeChild(e);
        });
        this.messages.clear();
        this.core.off("core.element.ignored", this.elementIgnoredHandler).off("core.field.added", this.fieldAddedHandler).off("core.field.removed", this.fieldRemovedHandler).off("core.validator.validated", this.validatorValidatedHandler).off("core.validator.notvalidated", this.validatorNotValidatedHandler);
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        var _this2 = this;

        var t = e.elements;

        if (t) {
          t.forEach(function (e) {
            var t = _this2.messages.get(e);

            if (t) {
              t.parentNode.removeChild(t);

              _this2.messages["delete"](e);
            }
          });
          this.prepareFieldContainer(e.field, t);
        }
      }
    }, {
      key: "onFieldRemoved",
      value: function onFieldRemoved(e) {
        var _this3 = this;

        if (!e.elements.length || !e.field) {
          return;
        }

        var t = e.elements[0].getAttribute("type");

        var _s2 = "radio" === t || "checkbox" === t ? [e.elements[0]] : e.elements;

        _s2.forEach(function (e) {
          if (_this3.messages.has(e)) {
            var _t = _this3.messages.get(e);

            _t.parentNode.removeChild(_t);

            _this3.messages["delete"](e);
          }
        });
      }
    }, {
      key: "prepareFieldContainer",
      value: function prepareFieldContainer(e, t) {
        var _this4 = this;

        if (t.length) {
          var _s12 = t[0].getAttribute("type");

          if ("radio" === _s12 || "checkbox" === _s12) {
            this.prepareElementContainer(e, t[0], t);
          } else {
            t.forEach(function (_s4) {
              return _this4.prepareElementContainer(e, _s4, t);
            });
          }
        }
      }
    }, {
      key: "prepareElementContainer",
      value: function prepareElementContainer(e, _s5, i) {
        var a;

        if ("string" === typeof this.opts.container) {
          var _e2 = "#" === this.opts.container.charAt(0) ? "[id=\"".concat(this.opts.container.substring(1), "\"]") : this.opts.container;

          a = this.core.getFormElement().querySelector(_e2);
        } else {
          a = this.opts.container(e, _s5);
        }

        var l = document.createElement("div");
        a.appendChild(l);
        c(l, {
          "fv-plugins-message-container": true
        });
        this.core.emit("plugins.message.placed", {
          element: _s5,
          elements: i,
          field: e,
          messageElement: l
        });
        this.messages.set(_s5, l);
      }
    }, {
      key: "getMessage",
      value: function getMessage(e) {
        return typeof e.message === "string" ? e.message : e.message[this.core.getLocale()];
      }
    }, {
      key: "onValidatorValidated",
      value: function onValidatorValidated(e) {
        var _s6 = e.elements;
        var i = e.element.getAttribute("type");
        var a = ("radio" === i || "checkbox" === i) && _s6.length > 0 ? _s6[0] : e.element;

        if (this.messages.has(a)) {
          var _s13 = this.messages.get(a);

          var _i = _s13.querySelector("[data-field=\"".concat(e.field, "\"][data-validator=\"").concat(e.validator, "\"]"));

          if (!_i && !e.result.valid) {
            var _i2 = document.createElement("div");

            _i2.innerHTML = this.getMessage(e.result);

            _i2.setAttribute("data-field", e.field);

            _i2.setAttribute("data-validator", e.validator);

            if (this.opts.clazz) {
              c(_i2, _defineProperty({}, this.opts.clazz, true));
            }

            _s13.appendChild(_i2);

            this.core.emit("plugins.message.displayed", {
              element: e.element,
              field: e.field,
              message: e.result.message,
              messageElement: _i2,
              meta: e.result.meta,
              validator: e.validator
            });
          } else if (_i && !e.result.valid) {
            _i.innerHTML = this.getMessage(e.result);
            this.core.emit("plugins.message.displayed", {
              element: e.element,
              field: e.field,
              message: e.result.message,
              messageElement: _i,
              meta: e.result.meta,
              validator: e.validator
            });
          } else if (_i && e.result.valid) {
            _s13.removeChild(_i);
          }
        }
      }
    }, {
      key: "onValidatorNotValidated",
      value: function onValidatorNotValidated(e) {
        var t = e.elements;

        var _s8 = e.element.getAttribute("type");

        var i = "radio" === _s8 || "checkbox" === _s8 ? t[0] : e.element;

        if (this.messages.has(i)) {
          var _t3 = this.messages.get(i);

          var _s14 = _t3.querySelector("[data-field=\"".concat(e.field, "\"][data-validator=\"").concat(e.validator, "\"]"));

          if (_s14) {
            _t3.removeChild(_s14);
          }
        }
      }
    }, {
      key: "onElementIgnored",
      value: function onElementIgnored(e) {
        var t = e.elements;

        var _s10 = e.element.getAttribute("type");

        var i = "radio" === _s10 || "checkbox" === _s10 ? t[0] : e.element;

        if (this.messages.has(i)) {
          var _t4 = this.messages.get(i);

          var _s15 = [].slice.call(_t4.querySelectorAll("[data-field=\"".concat(e.field, "\"]")));

          _s15.forEach(function (e) {
            _t4.removeChild(e);
          });
        }
      }
    }], [{
      key: "getClosestContainer",
      value: function getClosestContainer(e, t, _s) {
        var i = e;

        while (i) {
          if (i === t) {
            break;
          }

          i = i.parentElement;

          if (_s.test(i.className)) {
            break;
          }
        }

        return i;
      }
    }]);

    return s;
  }(t$4);

  var l = /*#__PURE__*/function (_e) {
    _inherits(l, _e);

    var _super = _createSuper(l);

    function l(e) {
      var _this;

      _classCallCheck(this, l);

      _this = _super.call(this, e);
      _this.results = new Map();
      _this.containers = new Map();
      _this.MESSAGE_PLUGIN = "___frameworkMessage";
      _this.opts = Object.assign({}, {
        defaultMessageContainer: true,
        eleInvalidClass: "",
        eleValidClass: "",
        rowClasses: "",
        rowValidatingClass: ""
      }, e);
      _this.elementIgnoredHandler = _this.onElementIgnored.bind(_assertThisInitialized(_this));
      _this.elementValidatingHandler = _this.onElementValidating.bind(_assertThisInitialized(_this));
      _this.elementValidatedHandler = _this.onElementValidated.bind(_assertThisInitialized(_this));
      _this.elementNotValidatedHandler = _this.onElementNotValidated.bind(_assertThisInitialized(_this));
      _this.iconPlacedHandler = _this.onIconPlaced.bind(_assertThisInitialized(_this));
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.fieldRemovedHandler = _this.onFieldRemoved.bind(_assertThisInitialized(_this));
      _this.messagePlacedHandler = _this.onMessagePlaced.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(l, [{
      key: "install",
      value: function install() {
        var _t,
            _this2 = this;

        c(this.core.getFormElement(), (_t = {}, _defineProperty(_t, this.opts.formClass, true), _defineProperty(_t, "fv-plugins-framework", true), _t));
        this.core.on("core.element.ignored", this.elementIgnoredHandler).on("core.element.validating", this.elementValidatingHandler).on("core.element.validated", this.elementValidatedHandler).on("core.element.notvalidated", this.elementNotValidatedHandler).on("plugins.icon.placed", this.iconPlacedHandler).on("core.field.added", this.fieldAddedHandler).on("core.field.removed", this.fieldRemovedHandler);

        if (this.opts.defaultMessageContainer) {
          this.core.registerPlugin(this.MESSAGE_PLUGIN, new s$1({
            clazz: this.opts.messageClass,
            container: function container(e, t) {
              var _l = "string" === typeof _this2.opts.rowSelector ? _this2.opts.rowSelector : _this2.opts.rowSelector(e, t);

              var a = t$1(t, _l);
              return s$1.getClosestContainer(t, a, _this2.opts.rowPattern);
            }
          }));
          this.core.on("plugins.message.placed", this.messagePlacedHandler);
        }
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        var _t2;

        this.results.clear();
        this.containers.clear();
        c(this.core.getFormElement(), (_t2 = {}, _defineProperty(_t2, this.opts.formClass, false), _defineProperty(_t2, "fv-plugins-framework", false), _t2));
        this.core.off("core.element.ignored", this.elementIgnoredHandler).off("core.element.validating", this.elementValidatingHandler).off("core.element.validated", this.elementValidatedHandler).off("core.element.notvalidated", this.elementNotValidatedHandler).off("plugins.icon.placed", this.iconPlacedHandler).off("core.field.added", this.fieldAddedHandler).off("core.field.removed", this.fieldRemovedHandler);

        if (this.opts.defaultMessageContainer) {
          this.core.deregisterPlugin(this.MESSAGE_PLUGIN);
          this.core.off("plugins.message.placed", this.messagePlacedHandler);
        }
      }
    }, {
      key: "onIconPlaced",
      value: function onIconPlaced(e) {}
    }, {
      key: "onMessagePlaced",
      value: function onMessagePlaced(e) {}
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        var _this3 = this;

        var s = e.elements;

        if (s) {
          s.forEach(function (e) {
            var s = _this3.containers.get(e);

            if (s) {
              var _t3;

              c(s, (_t3 = {}, _defineProperty(_t3, _this3.opts.rowInvalidClass, false), _defineProperty(_t3, _this3.opts.rowValidatingClass, false), _defineProperty(_t3, _this3.opts.rowValidClass, false), _defineProperty(_t3, "fv-plugins-icon-container", false), _t3));

              _this3.containers["delete"](e);
            }
          });
          this.prepareFieldContainer(e.field, s);
        }
      }
    }, {
      key: "onFieldRemoved",
      value: function onFieldRemoved(e) {
        var _this4 = this;

        e.elements.forEach(function (e) {
          var s = _this4.containers.get(e);

          if (s) {
            var _t4;

            c(s, (_t4 = {}, _defineProperty(_t4, _this4.opts.rowInvalidClass, false), _defineProperty(_t4, _this4.opts.rowValidatingClass, false), _defineProperty(_t4, _this4.opts.rowValidClass, false), _t4));
          }
        });
      }
    }, {
      key: "prepareFieldContainer",
      value: function prepareFieldContainer(e, t) {
        var _this5 = this;

        if (t.length) {
          var _s = t[0].getAttribute("type");

          if ("radio" === _s || "checkbox" === _s) {
            this.prepareElementContainer(e, t[0]);
          } else {
            t.forEach(function (t) {
              return _this5.prepareElementContainer(e, t);
            });
          }
        }
      }
    }, {
      key: "prepareElementContainer",
      value: function prepareElementContainer(e, i) {
        var _l2 = "string" === typeof this.opts.rowSelector ? this.opts.rowSelector : this.opts.rowSelector(e, i);

        var a = t$1(i, _l2);

        if (a !== i) {
          var _t5;

          c(a, (_t5 = {}, _defineProperty(_t5, this.opts.rowClasses, true), _defineProperty(_t5, "fv-plugins-icon-container", true), _t5));
          this.containers.set(i, a);
        }
      }
    }, {
      key: "onElementValidating",
      value: function onElementValidating(e) {
        var s = e.elements;
        var i = e.element.getAttribute("type");

        var _l3 = "radio" === i || "checkbox" === i ? s[0] : e.element;

        var a = this.containers.get(_l3);

        if (a) {
          var _t6;

          c(a, (_t6 = {}, _defineProperty(_t6, this.opts.rowInvalidClass, false), _defineProperty(_t6, this.opts.rowValidatingClass, true), _defineProperty(_t6, this.opts.rowValidClass, false), _t6));
        }
      }
    }, {
      key: "onElementNotValidated",
      value: function onElementNotValidated(e) {
        this.removeClasses(e.element, e.elements);
      }
    }, {
      key: "onElementIgnored",
      value: function onElementIgnored(e) {
        this.removeClasses(e.element, e.elements);
      }
    }, {
      key: "removeClasses",
      value: function removeClasses(e, s) {
        var _this6 = this;

        var i = e.getAttribute("type");

        var _l4 = "radio" === i || "checkbox" === i ? s[0] : e;

        s.forEach(function (e) {
          var _t7;

          c(e, (_t7 = {}, _defineProperty(_t7, _this6.opts.eleValidClass, false), _defineProperty(_t7, _this6.opts.eleInvalidClass, false), _t7));
        });
        var a = this.containers.get(_l4);

        if (a) {
          var _t8;

          c(a, (_t8 = {}, _defineProperty(_t8, this.opts.rowInvalidClass, false), _defineProperty(_t8, this.opts.rowValidatingClass, false), _defineProperty(_t8, this.opts.rowValidClass, false), _t8));
        }
      }
    }, {
      key: "onElementValidated",
      value: function onElementValidated(e) {
        var _this7 = this;

        var s = e.elements;
        var i = e.element.getAttribute("type");

        var _l5 = "radio" === i || "checkbox" === i ? s[0] : e.element;

        s.forEach(function (s) {
          var _t9;

          c(s, (_t9 = {}, _defineProperty(_t9, _this7.opts.eleValidClass, e.valid), _defineProperty(_t9, _this7.opts.eleInvalidClass, !e.valid), _t9));
        });
        var a = this.containers.get(_l5);

        if (a) {
          if (!e.valid) {
            var _t10;

            this.results.set(_l5, false);
            c(a, (_t10 = {}, _defineProperty(_t10, this.opts.rowInvalidClass, true), _defineProperty(_t10, this.opts.rowValidatingClass, false), _defineProperty(_t10, this.opts.rowValidClass, false), _t10));
          } else {
            this.results["delete"](_l5);
            var _e2 = true;
            this.containers.forEach(function (t, s) {
              if (t === a && _this7.results.get(s) === false) {
                _e2 = false;
              }
            });

            if (_e2) {
              var _t11;

              c(a, (_t11 = {}, _defineProperty(_t11, this.opts.rowInvalidClass, false), _defineProperty(_t11, this.opts.rowValidatingClass, false), _defineProperty(_t11, this.opts.rowValidClass, true), _t11));
            }
          }
        }
      }
    }]);

    return l;
  }(t$4);

  var i$2 = /*#__PURE__*/function (_e) {
    _inherits(i, _e);

    var _super = _createSuper(i);

    function i(e) {
      var _this;

      _classCallCheck(this, i);

      _this = _super.call(this, e);
      _this.icons = new Map();
      _this.opts = Object.assign({}, {
        invalid: "fv-plugins-icon--invalid",
        onPlaced: function onPlaced() {},
        onSet: function onSet() {},
        valid: "fv-plugins-icon--valid",
        validating: "fv-plugins-icon--validating"
      }, e);
      _this.elementValidatingHandler = _this.onElementValidating.bind(_assertThisInitialized(_this));
      _this.elementValidatedHandler = _this.onElementValidated.bind(_assertThisInitialized(_this));
      _this.elementNotValidatedHandler = _this.onElementNotValidated.bind(_assertThisInitialized(_this));
      _this.elementIgnoredHandler = _this.onElementIgnored.bind(_assertThisInitialized(_this));
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(i, [{
      key: "install",
      value: function install() {
        this.core.on("core.element.validating", this.elementValidatingHandler).on("core.element.validated", this.elementValidatedHandler).on("core.element.notvalidated", this.elementNotValidatedHandler).on("core.element.ignored", this.elementIgnoredHandler).on("core.field.added", this.fieldAddedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.icons.forEach(function (e) {
          return e.parentNode.removeChild(e);
        });
        this.icons.clear();
        this.core.off("core.element.validating", this.elementValidatingHandler).off("core.element.validated", this.elementValidatedHandler).off("core.element.notvalidated", this.elementNotValidatedHandler).off("core.element.ignored", this.elementIgnoredHandler).off("core.field.added", this.fieldAddedHandler);
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        var _this2 = this;

        var t = e.elements;

        if (t) {
          t.forEach(function (e) {
            var t = _this2.icons.get(e);

            if (t) {
              t.parentNode.removeChild(t);

              _this2.icons["delete"](e);
            }
          });
          this.prepareFieldIcon(e.field, t);
        }
      }
    }, {
      key: "prepareFieldIcon",
      value: function prepareFieldIcon(e, t) {
        var _this3 = this;

        if (t.length) {
          var _i8 = t[0].getAttribute("type");

          if ("radio" === _i8 || "checkbox" === _i8) {
            this.prepareElementIcon(e, t[0]);
          } else {
            t.forEach(function (t) {
              return _this3.prepareElementIcon(e, t);
            });
          }
        }
      }
    }, {
      key: "prepareElementIcon",
      value: function prepareElementIcon(e, _i2) {
        var n = document.createElement("i");
        n.setAttribute("data-field", e);

        _i2.parentNode.insertBefore(n, _i2.nextSibling);

        c(n, {
          "fv-plugins-icon": true
        });
        var l = {
          classes: {
            invalid: this.opts.invalid,
            valid: this.opts.valid,
            validating: this.opts.validating
          },
          element: _i2,
          field: e,
          iconElement: n
        };
        this.core.emit("plugins.icon.placed", l);
        this.opts.onPlaced(l);
        this.icons.set(_i2, n);
      }
    }, {
      key: "onElementValidating",
      value: function onElementValidating(e) {
        var _this$setClasses;

        var t = this.setClasses(e.field, e.element, e.elements, (_this$setClasses = {}, _defineProperty(_this$setClasses, this.opts.invalid, false), _defineProperty(_this$setClasses, this.opts.valid, false), _defineProperty(_this$setClasses, this.opts.validating, true), _this$setClasses));
        var _i3 = {
          element: e.element,
          field: e.field,
          iconElement: t,
          status: "Validating"
        };
        this.core.emit("plugins.icon.set", _i3);
        this.opts.onSet(_i3);
      }
    }, {
      key: "onElementValidated",
      value: function onElementValidated(e) {
        var _this$setClasses2;

        var t = this.setClasses(e.field, e.element, e.elements, (_this$setClasses2 = {}, _defineProperty(_this$setClasses2, this.opts.invalid, !e.valid), _defineProperty(_this$setClasses2, this.opts.valid, e.valid), _defineProperty(_this$setClasses2, this.opts.validating, false), _this$setClasses2));
        var _i4 = {
          element: e.element,
          field: e.field,
          iconElement: t,
          status: e.valid ? "Valid" : "Invalid"
        };
        this.core.emit("plugins.icon.set", _i4);
        this.opts.onSet(_i4);
      }
    }, {
      key: "onElementNotValidated",
      value: function onElementNotValidated(e) {
        var _this$setClasses3;

        var t = this.setClasses(e.field, e.element, e.elements, (_this$setClasses3 = {}, _defineProperty(_this$setClasses3, this.opts.invalid, false), _defineProperty(_this$setClasses3, this.opts.valid, false), _defineProperty(_this$setClasses3, this.opts.validating, false), _this$setClasses3));
        var _i5 = {
          element: e.element,
          field: e.field,
          iconElement: t,
          status: "NotValidated"
        };
        this.core.emit("plugins.icon.set", _i5);
        this.opts.onSet(_i5);
      }
    }, {
      key: "onElementIgnored",
      value: function onElementIgnored(e) {
        var _this$setClasses4;

        var t = this.setClasses(e.field, e.element, e.elements, (_this$setClasses4 = {}, _defineProperty(_this$setClasses4, this.opts.invalid, false), _defineProperty(_this$setClasses4, this.opts.valid, false), _defineProperty(_this$setClasses4, this.opts.validating, false), _this$setClasses4));
        var _i6 = {
          element: e.element,
          field: e.field,
          iconElement: t,
          status: "Ignored"
        };
        this.core.emit("plugins.icon.set", _i6);
        this.opts.onSet(_i6);
      }
    }, {
      key: "setClasses",
      value: function setClasses(e, _i7, n, l) {
        var s = _i7.getAttribute("type");

        var d = "radio" === s || "checkbox" === s ? n[0] : _i7;

        if (this.icons.has(d)) {
          var _e2 = this.icons.get(d);

          c(_e2, l);
          return _e2;
        } else {
          return null;
        }
      }
    }]);

    return i;
  }(t$4);

  var i$1 = /*#__PURE__*/function (_e) {
    _inherits(i, _e);

    var _super = _createSuper(i);

    function i(e) {
      var _this;

      _classCallCheck(this, i);

      _this = _super.call(this, e);
      _this.invalidFields = new Map();
      _this.opts = Object.assign({}, {
        enabled: true
      }, e);
      _this.validatorHandler = _this.onValidatorValidated.bind(_assertThisInitialized(_this));
      _this.shouldValidateFilter = _this.shouldValidate.bind(_assertThisInitialized(_this));
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.elementNotValidatedHandler = _this.onElementNotValidated.bind(_assertThisInitialized(_this));
      _this.elementValidatingHandler = _this.onElementValidating.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(i, [{
      key: "install",
      value: function install() {
        this.core.on("core.validator.validated", this.validatorHandler).on("core.field.added", this.fieldAddedHandler).on("core.element.notvalidated", this.elementNotValidatedHandler).on("core.element.validating", this.elementValidatingHandler).registerFilter("field-should-validate", this.shouldValidateFilter);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.invalidFields.clear();
        this.core.off("core.validator.validated", this.validatorHandler).off("core.field.added", this.fieldAddedHandler).off("core.element.notvalidated", this.elementNotValidatedHandler).off("core.element.validating", this.elementValidatingHandler).deregisterFilter("field-should-validate", this.shouldValidateFilter);
      }
    }, {
      key: "shouldValidate",
      value: function shouldValidate(e, _i, t, l) {
        var d = (this.opts.enabled === true || this.opts.enabled[e] === true) && this.invalidFields.has(_i) && !!this.invalidFields.get(_i).length && this.invalidFields.get(_i).indexOf(l) === -1;
        return !d;
      }
    }, {
      key: "onValidatorValidated",
      value: function onValidatorValidated(e) {
        var _i2 = this.invalidFields.has(e.element) ? this.invalidFields.get(e.element) : [];

        var t = _i2.indexOf(e.validator);

        if (e.result.valid && t >= 0) {
          _i2.splice(t, 1);
        } else if (!e.result.valid && t === -1) {
          _i2.push(e.validator);
        }

        this.invalidFields.set(e.element, _i2);
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        if (e.elements) {
          this.clearInvalidFields(e.elements);
        }
      }
    }, {
      key: "onElementNotValidated",
      value: function onElementNotValidated(e) {
        this.clearInvalidFields(e.elements);
      }
    }, {
      key: "onElementValidating",
      value: function onElementValidating(e) {
        this.clearInvalidFields(e.elements);
      }
    }, {
      key: "clearInvalidFields",
      value: function clearInvalidFields(e) {
        var _this2 = this;

        e.forEach(function (e) {
          return _this2.invalidFields["delete"](e);
        });
      }
    }]);

    return i;
  }(t$4);

  var e = /*#__PURE__*/function (_t) {
    _inherits(e, _t);

    var _super = _createSuper(e);

    function e(t) {
      var _this;

      _classCallCheck(this, e);

      _this = _super.call(this, t);
      _this.isFormValid = false;
      _this.opts = Object.assign({}, {
        aspNetButton: false,
        buttons: function buttons(t) {
          return [].slice.call(t.querySelectorAll('[type="submit"]:not([formnovalidate])'));
        }
      }, t);
      _this.submitHandler = _this.handleSubmitEvent.bind(_assertThisInitialized(_this));
      _this.buttonClickHandler = _this.handleClickEvent.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(e, [{
      key: "install",
      value: function install() {
        var _this2 = this;

        if (!(this.core.getFormElement() instanceof HTMLFormElement)) {
          return;
        }

        var t = this.core.getFormElement();
        this.submitButtons = this.opts.buttons(t);
        t.setAttribute("novalidate", "novalidate");
        t.addEventListener("submit", this.submitHandler);
        this.hiddenClickedEle = document.createElement("input");
        this.hiddenClickedEle.setAttribute("type", "hidden");
        t.appendChild(this.hiddenClickedEle);
        this.submitButtons.forEach(function (t) {
          t.addEventListener("click", _this2.buttonClickHandler);
        });
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        var _this3 = this;

        var t = this.core.getFormElement();

        if (t instanceof HTMLFormElement) {
          t.removeEventListener("submit", this.submitHandler);
        }

        this.submitButtons.forEach(function (t) {
          t.removeEventListener("click", _this3.buttonClickHandler);
        });
        this.hiddenClickedEle.parentElement.removeChild(this.hiddenClickedEle);
      }
    }, {
      key: "handleSubmitEvent",
      value: function handleSubmitEvent(t) {
        this.validateForm(t);
      }
    }, {
      key: "handleClickEvent",
      value: function handleClickEvent(t) {
        var _e = t.currentTarget;

        if (_e instanceof HTMLElement) {
          if (this.opts.aspNetButton && this.isFormValid === true) ; else {
            var _e3 = this.core.getFormElement();

            _e3.removeEventListener("submit", this.submitHandler);

            this.clickedButton = t.target;
            var i = this.clickedButton.getAttribute("name");
            var s = this.clickedButton.getAttribute("value");

            if (i && s) {
              this.hiddenClickedEle.setAttribute("name", i);
              this.hiddenClickedEle.setAttribute("value", s);
            }

            this.validateForm(t);
          }
        }
      }
    }, {
      key: "validateForm",
      value: function validateForm(t) {
        var _this4 = this;

        t.preventDefault();
        this.core.validate().then(function (t) {
          if (t === "Valid" && _this4.opts.aspNetButton && !_this4.isFormValid && _this4.clickedButton) {
            _this4.isFormValid = true;

            _this4.clickedButton.removeEventListener("click", _this4.buttonClickHandler);

            _this4.clickedButton.click();
          }
        });
      }
    }]);

    return e;
  }(t$4);

  var i = /*#__PURE__*/function (_t) {
    _inherits(i, _t);

    var _super = _createSuper(i);

    function i(t) {
      var _this;

      _classCallCheck(this, i);

      _this = _super.call(this, t);
      _this.messages = new Map();
      _this.opts = Object.assign({}, {
        placement: "top",
        trigger: "click"
      }, t);
      _this.iconPlacedHandler = _this.onIconPlaced.bind(_assertThisInitialized(_this));
      _this.validatorValidatedHandler = _this.onValidatorValidated.bind(_assertThisInitialized(_this));
      _this.elementValidatedHandler = _this.onElementValidated.bind(_assertThisInitialized(_this));
      _this.documentClickHandler = _this.onDocumentClicked.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(i, [{
      key: "install",
      value: function install() {
        this.tip = document.createElement("div");
        c(this.tip, _defineProperty({
          "fv-plugins-tooltip": true
        }, "fv-plugins-tooltip--".concat(this.opts.placement), true));
        document.body.appendChild(this.tip);
        this.core.on("plugins.icon.placed", this.iconPlacedHandler).on("core.validator.validated", this.validatorValidatedHandler).on("core.element.validated", this.elementValidatedHandler);

        if ("click" === this.opts.trigger) {
          document.addEventListener("click", this.documentClickHandler);
        }
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.messages.clear();
        document.body.removeChild(this.tip);
        this.core.off("plugins.icon.placed", this.iconPlacedHandler).off("core.validator.validated", this.validatorValidatedHandler).off("core.element.validated", this.elementValidatedHandler);

        if ("click" === this.opts.trigger) {
          document.removeEventListener("click", this.documentClickHandler);
        }
      }
    }, {
      key: "onIconPlaced",
      value: function onIconPlaced(t) {
        var _this2 = this;

        c(t.iconElement, {
          "fv-plugins-tooltip-icon": true
        });

        switch (this.opts.trigger) {
          case "hover":
            t.iconElement.addEventListener("mouseenter", function (e) {
              return _this2.show(t.element, e);
            });
            t.iconElement.addEventListener("mouseleave", function (t) {
              return _this2.hide();
            });
            break;

          case "click":
          default:
            t.iconElement.addEventListener("click", function (e) {
              return _this2.show(t.element, e);
            });
            break;
        }
      }
    }, {
      key: "onValidatorValidated",
      value: function onValidatorValidated(t) {
        if (!t.result.valid) {
          var _e2 = t.elements;

          var _i4 = t.element.getAttribute("type");

          var s = "radio" === _i4 || "checkbox" === _i4 ? _e2[0] : t.element;
          var o = typeof t.result.message === "string" ? t.result.message : t.result.message[this.core.getLocale()];
          this.messages.set(s, o);
        }
      }
    }, {
      key: "onElementValidated",
      value: function onElementValidated(t) {
        if (t.valid) {
          var _e3 = t.elements;

          var _i5 = t.element.getAttribute("type");

          var s = "radio" === _i5 || "checkbox" === _i5 ? _e3[0] : t.element;
          this.messages["delete"](s);
        }
      }
    }, {
      key: "onDocumentClicked",
      value: function onDocumentClicked(t) {
        this.hide();
      }
    }, {
      key: "show",
      value: function show(t, _i3) {
        _i3.preventDefault();

        _i3.stopPropagation();

        if (!this.messages.has(t)) {
          return;
        }

        c(this.tip, {
          "fv-plugins-tooltip--hide": false
        });
        this.tip.innerHTML = "<div class=\"fv-plugins-tooltip__content\">".concat(this.messages.get(t), "</div>");
        var s = _i3.target;
        var o = s.getBoundingClientRect();

        var _this$tip$getBounding = this.tip.getBoundingClientRect(),
            l = _this$tip$getBounding.height,
            n = _this$tip$getBounding.width;

        var a = 0;
        var d = 0;

        switch (this.opts.placement) {
          case "bottom":
            a = o.top + o.height;
            d = o.left + o.width / 2 - n / 2;
            break;

          case "bottom-left":
            a = o.top + o.height;
            d = o.left;
            break;

          case "bottom-right":
            a = o.top + o.height;
            d = o.left + o.width - n;
            break;

          case "left":
            a = o.top + o.height / 2 - l / 2;
            d = o.left - n;
            break;

          case "right":
            a = o.top + o.height / 2 - l / 2;
            d = o.left + o.width;
            break;

          case "top-left":
            a = o.top - l;
            d = o.left;
            break;

          case "top-right":
            a = o.top - l;
            d = o.left + o.width - n;
            break;

          case "top":
          default:
            a = o.top - l;
            d = o.left + o.width / 2 - n / 2;
            break;
        }

        var c$1 = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
        var r = window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft || 0;
        a = a + c$1;
        d = d + r;
        this.tip.setAttribute("style", "top: ".concat(a, "px; left: ").concat(d, "px"));
      }
    }, {
      key: "hide",
      value: function hide() {
        c(this.tip, {
          "fv-plugins-tooltip--hide": true
        });
      }
    }]);

    return i;
  }(t$4);

  var t = /*#__PURE__*/function (_e) {
    _inherits(t, _e);

    var _super = _createSuper(t);

    function t(e) {
      var _this;

      _classCallCheck(this, t);

      _this = _super.call(this, e);
      _this.handlers = [];
      _this.timers = new Map();

      var _t = document.createElement("div");

      _this.defaultEvent = !("oninput" in _t) ? "keyup" : "input";
      _this.opts = Object.assign({}, {
        delay: 0,
        event: _this.defaultEvent,
        threshold: 0
      }, e);
      _this.fieldAddedHandler = _this.onFieldAdded.bind(_assertThisInitialized(_this));
      _this.fieldRemovedHandler = _this.onFieldRemoved.bind(_assertThisInitialized(_this));
      return _this;
    }

    _createClass(t, [{
      key: "install",
      value: function install() {
        this.core.on("core.field.added", this.fieldAddedHandler).on("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "uninstall",
      value: function uninstall() {
        this.handlers.forEach(function (e) {
          return e.element.removeEventListener(e.event, e.handler);
        });
        this.handlers = [];
        this.timers.forEach(function (e) {
          return window.clearTimeout(e);
        });
        this.timers.clear();
        this.core.off("core.field.added", this.fieldAddedHandler).off("core.field.removed", this.fieldRemovedHandler);
      }
    }, {
      key: "prepareHandler",
      value: function prepareHandler(e, _t2) {
        var _this2 = this;

        _t2.forEach(function (_t3) {
          var i = [];

          if (!!_this2.opts.event && _this2.opts.event[e] === false) {
            i = [];
          } else if (!!_this2.opts.event && !!_this2.opts.event[e] && typeof _this2.opts.event[e] !== "function") {
            i = _this2.opts.event[e].split(" ");
          } else if ("string" === typeof _this2.opts.event && _this2.opts.event !== _this2.defaultEvent) {
            i = _this2.opts.event.split(" ");
          } else {
            var _e2 = _t3.getAttribute("type");

            var s = _t3.tagName.toLowerCase();

            var n = "radio" === _e2 || "checkbox" === _e2 || "file" === _e2 || "select" === s ? "change" : _this2.ieVersion >= 10 && _t3.getAttribute("placeholder") ? "keyup" : _this2.defaultEvent;
            i = [n];
          }

          i.forEach(function (i) {
            var s = function s(i) {
              return _this2.handleEvent(i, e, _t3);
            };

            _this2.handlers.push({
              element: _t3,
              event: i,
              field: e,
              handler: s
            });

            _t3.addEventListener(i, s);
          });
        });
      }
    }, {
      key: "handleEvent",
      value: function handleEvent(e, _t4, i) {
        var _this3 = this;

        if (this.exceedThreshold(_t4, i) && this.core.executeFilter("plugins-trigger-should-validate", true, [_t4, i])) {
          var s = function s() {
            return _this3.core.validateElement(_t4, i).then(function (s) {
              _this3.core.emit("plugins.trigger.executed", {
                element: i,
                event: e,
                field: _t4
              });
            });
          };

          var n = this.opts.delay[_t4] || this.opts.delay;

          if (n === 0) {
            s();
          } else {
            var _e3 = this.timers.get(i);

            if (_e3) {
              window.clearTimeout(_e3);
            }

            this.timers.set(i, window.setTimeout(s, n * 1e3));
          }
        }
      }
    }, {
      key: "onFieldAdded",
      value: function onFieldAdded(e) {
        this.handlers.filter(function (_t5) {
          return _t5.field === e.field;
        }).forEach(function (e) {
          return e.element.removeEventListener(e.event, e.handler);
        });
        this.prepareHandler(e.field, e.elements);
      }
    }, {
      key: "onFieldRemoved",
      value: function onFieldRemoved(e) {
        this.handlers.filter(function (_t6) {
          return _t6.field === e.field && e.elements.indexOf(_t6.element) >= 0;
        }).forEach(function (e) {
          return e.element.removeEventListener(e.event, e.handler);
        });
      }
    }, {
      key: "exceedThreshold",
      value: function exceedThreshold(e, _t7) {
        var i = this.opts.threshold[e] === 0 || this.opts.threshold === 0 ? false : this.opts.threshold[e] || this.opts.threshold;

        if (!i) {
          return true;
        }

        var s = _t7.getAttribute("type");

        if (["button", "checkbox", "file", "hidden", "image", "radio", "reset", "submit"].indexOf(s) !== -1) {
          return true;
        }

        var n = this.core.getElementValue(e, _t7);
        return n.length >= i;
      }
    }]);

    return t;
  }(t$4);

  var index$1 = {
    Alias: e$4,
    Aria: i$3,
    Declarative: t$3,
    DefaultSubmit: o,
    Dependency: e$3,
    Excluded: e$2,
    FieldStatus: t$2,
    Framework: l,
    Icon: i$2,
    Message: s$1,
    Sequence: i$1,
    SubmitButton: e,
    Tooltip: i,
    Trigger: t
  };

  function s(s, t) {
    return s.classList ? s.classList.contains(t) : new RegExp("(^| )".concat(t, "( |$)"), "gi").test(s.className);
  }

  var index = {
    call: t$$,
    classSet: c,
    closest: t$1,
    fetch: t$V,
    format: r$d,
    hasClass: s,
    isValidDate: t$Y
  };

  var p = {};

  exports.Plugin = t$4;
  exports.algorithms = index$3;
  exports.filters = index$2;
  exports.formValidation = r;
  exports.locales = p;
  exports.plugins = index$1;
  exports.utils = index;
  exports.validators = s$3;

  Object.defineProperty(exports, '__esModule', { value: true });

}));
