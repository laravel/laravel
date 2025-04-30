/*
 Highcharts JS v11.1.0 (2023-06-05)

 (c) 2009-2021 Torstein Honsi

 License: www.highcharts.com/license
*/
"use strict";
(function (U, M) {
    "object" === typeof module && module.exports
        ? ((M["default"] = M), (module.exports = U.document ? M(U) : M))
        : "function" === typeof define && define.amd
        ? define("highcharts/highcharts", function () {
              return M(U);
          })
        : (U.Highcharts && U.Highcharts.error(16, !0), (U.Highcharts = M(U)));
})("undefined" !== typeof window ? window : this, function (U) {
    function M(a, y, I, L) {
        a.hasOwnProperty(y) ||
            ((a[y] = L.apply(null, I)),
            "function" === typeof CustomEvent &&
                U.dispatchEvent(
                    new CustomEvent("HighchartsModuleLoaded", {
                        detail: { path: y, module: a[y] },
                    })
                ));
    }
    var a = {};
    M(a, "Core/Globals.js", [], function () {
        var a;
        (function (a) {
            a.SVG_NS = "http://www.w3.org/2000/svg";
            a.product = "Highcharts";
            a.version = "11.1.0";
            a.win = "undefined" !== typeof U ? U : {};
            a.doc = a.win.document;
            a.svg =
                a.doc &&
                a.doc.createElementNS &&
                !!a.doc.createElementNS(a.SVG_NS, "svg").createSVGRect;
            a.userAgent = (a.win.navigator && a.win.navigator.userAgent) || "";
            a.isChrome = -1 !== a.userAgent.indexOf("Chrome");
            a.isFirefox = -1 !== a.userAgent.indexOf("Firefox");
            a.isMS = /(edge|msie|trident)/i.test(a.userAgent) && !a.win.opera;
            a.isSafari = !a.isChrome && -1 !== a.userAgent.indexOf("Safari");
            a.isTouchDevice = /(Mobile|Android|Windows Phone)/.test(
                a.userAgent
            );
            a.isWebKit = -1 !== a.userAgent.indexOf("AppleWebKit");
            a.deg2rad = (2 * Math.PI) / 360;
            a.hasBidiBug =
                a.isFirefox &&
                4 > parseInt(a.userAgent.split("Firefox/")[1], 10);
            a.hasTouch = !!a.win.TouchEvent;
            a.marginNames = [
                "plotTop",
                "marginRight",
                "marginBottom",
                "plotLeft",
            ];
            a.noop = function () {};
            a.supportsPassiveEvents = (function () {
                let x = !1;
                if (!a.isMS) {
                    const y = Object.defineProperty({}, "passive", {
                        get: function () {
                            x = !0;
                        },
                    });
                    a.win.addEventListener &&
                        a.win.removeEventListener &&
                        (a.win.addEventListener("testPassive", a.noop, y),
                        a.win.removeEventListener("testPassive", a.noop, y));
                }
                return x;
            })();
            a.charts = [];
            a.dateFormats = {};
            a.seriesTypes = {};
            a.symbolSizes = {};
            a.chartCount = 0;
        })(a || (a = {}));
        ("");
        return a;
    });
    M(a, "Core/Utilities.js", [a["Core/Globals.js"]], function (a) {
        function x(c, b, f, k) {
            const n = b ? "Highcharts error" : "Highcharts warning";
            32 === c && (c = `${n}: Deprecated member`);
            const r = u(c);
            let e = r
                ? `${n} #${c}: www.highcharts.com/errors/${c}/`
                : c.toString();
            if ("undefined" !== typeof k) {
                let c = "";
                r && (e += "?");
                E(k, function (b, n) {
                    c += `\n - ${n}: ${b}`;
                    r && (e += encodeURI(n) + "=" + encodeURI(b));
                });
                e += c;
            }
            d(
                a,
                "displayError",
                { chart: f, code: c, message: e, params: k },
                function () {
                    if (b) return;
                    q.console &&
                        -1 === x.messages.indexOf(e) &&
                        console.warn(e);
                }
            );
            x.messages.push(e);
        }

        function I(c, b) {
            return parseInt(c, b || 10);
        }

        function L(c) {
            return "string" === typeof c;
        }

        function C(c) {
            c = Object.prototype.toString.call(c);
            return "[object Array]" === c || "[object Array Iterator]" === c;
        }

        function z(c, b) {
            return !!c && "object" === typeof c && (!b || !C(c));
        }

        function H(c) {
            return z(c) && "number" === typeof c.nodeType;
        }

        function B(c) {
            const b = c && c.constructor;
            return !(!z(c, !0) || H(c) || !b || !b.name || "Object" === b.name);
        }

        function u(c) {
            return (
                "number" === typeof c &&
                !isNaN(c) &&
                Infinity > c &&
                -Infinity < c
            );
        }

        function v(c) {
            return "undefined" !== typeof c && null !== c;
        }

        function l(c, b, f) {
            const n = L(b) && !v(f);
            let d;
            const k = (b, f) => {
                v(b)
                    ? c.setAttribute(f, b)
                    : n
                    ? (d = c.getAttribute(f)) ||
                      "class" !== f ||
                      (d = c.getAttribute(f + "Name"))
                    : c.removeAttribute(f);
            };
            L(b) ? k(f, b) : E(b, k);
            return d;
        }

        function p(c) {
            return C(c) ? c : [c];
        }

        function t(c, b) {
            let n;
            c || (c = {});
            for (n in b) c[n] = b[n];
            return c;
        }

        function m() {
            const c = arguments,
                b = c.length;
            for (let n = 0; n < b; n++) {
                const b = c[n];
                if ("undefined" !== typeof b && null !== b) return b;
            }
        }

        function h(c, b) {
            a.isMS &&
                !a.svg &&
                b &&
                v(b.opacity) &&
                (b.filter = `alpha(opacity=${100 * b.opacity})`);
            t(c.style, b);
        }

        function g(c) {
            return Math.pow(10, Math.floor(Math.log(c) / Math.LN10));
        }

        function e(c, b) {
            return 1e14 < c ? c : parseFloat(c.toPrecision(b || 14));
        }

        function w(c, b, f) {
            let n;
            if ("width" === b)
                return (
                    (b = Math.min(c.offsetWidth, c.scrollWidth)),
                    (f =
                        c.getBoundingClientRect &&
                        c.getBoundingClientRect().width),
                    f < b && f >= b - 1 && (b = Math.floor(f)),
                    Math.max(
                        0,
                        b -
                            (w(c, "padding-left", !0) || 0) -
                            (w(c, "padding-right", !0) || 0)
                    )
                );
            if ("height" === b)
                return Math.max(
                    0,
                    Math.min(c.offsetHeight, c.scrollHeight) -
                        (w(c, "padding-top", !0) || 0) -
                        (w(c, "padding-bottom", !0) || 0)
                );
            if ((c = q.getComputedStyle(c, void 0)))
                (n = c.getPropertyValue(b)),
                    m(f, "opacity" !== b) && (n = I(n));
            return n;
        }

        function E(c, b, f) {
            for (const n in c)
                Object.hasOwnProperty.call(c, n) &&
                    b.call(f || c[n], c[n], n, c);
        }

        function F(c, b, f) {
            function n(b, n) {
                const f = c.removeEventListener;
                f && f.call(c, b, n, !1);
            }

            function d(f) {
                let d, K;
                c.nodeName &&
                    (b ? ((d = {}), (d[b] = !0)) : (d = f),
                    E(d, function (c, b) {
                        if (f[b]) for (K = f[b].length; K--; ) n(b, f[b][K].fn);
                    }));
            }
            var k = ("function" === typeof c && c.prototype) || c;
            if (Object.hasOwnProperty.call(k, "hcEvents")) {
                const c = k.hcEvents;
                b
                    ? ((k = c[b] || []),
                      f
                          ? ((c[b] = k.filter(function (c) {
                                return f !== c.fn;
                            })),
                            n(b, f))
                          : (d(c), (c[b] = [])))
                    : (d(c), delete k.hcEvents);
            }
        }

        function d(c, b, f, d) {
            f = f || {};
            if (
                r.createEvent &&
                (c.dispatchEvent || (c.fireEvent && c !== a))
            ) {
                var n = r.createEvent("Events");
                n.initEvent(b, !0, !0);
                f = t(n, f);
                c.dispatchEvent ? c.dispatchEvent(f) : c.fireEvent(b, f);
            } else if (c.hcEvents) {
                f.target ||
                    t(f, {
                        preventDefault: function () {
                            f.defaultPrevented = !0;
                        },
                        target: c,
                        type: b,
                    });
                n = [];
                let d = c,
                    K = !1;
                for (; d.hcEvents; )
                    Object.hasOwnProperty.call(d, "hcEvents") &&
                        d.hcEvents[b] &&
                        (n.length && (K = !0),
                        n.unshift.apply(n, d.hcEvents[b])),
                        (d = Object.getPrototypeOf(d));
                K && n.sort((c, b) => c.order - b.order);
                n.forEach((b) => {
                    !1 === b.fn.call(c, f) && f.preventDefault();
                });
            }
            d && !f.defaultPrevented && d.call(c, f);
        }
        const { charts: k, doc: r, win: q } = a;
        (x || (x = {})).messages = [];
        Math.easeInOutSine = function (c) {
            return -0.5 * (Math.cos(Math.PI * c) - 1);
        };
        var G = Array.prototype.find
            ? function (c, b) {
                  return c.find(b);
              }
            : function (c, b) {
                  let f;
                  const n = c.length;
                  for (f = 0; f < n; f++) if (b(c[f], f)) return c[f];
              };
        E(
            {
                map: "map",
                each: "forEach",
                grep: "filter",
                reduce: "reduce",
                some: "some",
            },
            function (c, b) {
                a[b] = function (f) {
                    x(32, !1, void 0, {
                        [`Highcharts.${b}`]: `use Array.${c}`,
                    });
                    return Array.prototype[c].apply(
                        f,
                        [].slice.call(arguments, 1)
                    );
                };
            }
        );
        let b;
        const f = (function () {
            const c = Math.random().toString(36).substring(2, 9) + "-";
            let f = 0;
            return function () {
                return "highcharts-" + (b ? "" : c) + f++;
            };
        })();
        q.jQuery &&
            (q.jQuery.fn.highcharts = function () {
                const c = [].slice.call(arguments);
                if (this[0])
                    return c[0]
                        ? (new a[L(c[0]) ? c.shift() : "Chart"](
                              this[0],
                              c[0],
                              c[1]
                          ),
                          this)
                        : k[l(this[0], "data-highcharts-chart")];
            });
        G = {
            addEvent: function (c, b, f, d = {}) {
                var n = ("function" === typeof c && c.prototype) || c;
                Object.hasOwnProperty.call(n, "hcEvents") || (n.hcEvents = {});
                n = n.hcEvents;
                a.Point &&
                    c instanceof a.Point &&
                    c.series &&
                    c.series.chart &&
                    (c.series.chart.runTrackerClick = !0);
                const k = c.addEventListener;
                k &&
                    k.call(
                        c,
                        b,
                        f,
                        a.supportsPassiveEvents
                            ? {
                                  passive:
                                      void 0 === d.passive
                                          ? -1 !== b.indexOf("touch")
                                          : d.passive,
                                  capture: !1,
                              }
                            : !1
                    );
                n[b] || (n[b] = []);
                n[b].push({
                    fn: f,
                    order: "number" === typeof d.order ? d.order : Infinity,
                });
                n[b].sort((c, b) => c.order - b.order);
                return function () {
                    F(c, b, f);
                };
            },
            arrayMax: function (c) {
                let b = c.length,
                    f = c[0];
                for (; b--; ) c[b] > f && (f = c[b]);
                return f;
            },
            arrayMin: function (c) {
                let b = c.length,
                    f = c[0];
                for (; b--; ) c[b] < f && (f = c[b]);
                return f;
            },
            attr: l,
            clamp: function (c, b, f) {
                return c > b ? (c < f ? c : f) : b;
            },
            clearTimeout: function (c) {
                v(c) && clearTimeout(c);
            },
            correctFloat: e,
            createElement: function (c, b, f, d, K) {
                c = r.createElement(c);
                b && t(c, b);
                K && h(c, { padding: "0", border: "none", margin: "0" });
                f && h(c, f);
                d && d.appendChild(c);
                return c;
            },
            css: h,
            defined: v,
            destroyObjectProperties: function (c, b) {
                E(c, function (f, n) {
                    f && f !== b && f.destroy && f.destroy();
                    delete c[n];
                });
            },
            diffObjects: function (c, b, f, d) {
                function n(b, c, K, k) {
                    const A = f ? c : b;
                    E(b, function (f, q) {
                        if (!k && d && -1 < d.indexOf(q) && c[q]) {
                            f = p(f);
                            K[q] = [];
                            for (
                                let b = 0;
                                b < Math.max(f.length, c[q].length);
                                b++
                            )
                                c[q][b] &&
                                    (void 0 === f[b]
                                        ? (K[q][b] = c[q][b])
                                        : ((K[q][b] = {}),
                                          n(f[b], c[q][b], K[q][b], k + 1)));
                        } else if (z(f, !0) && !f.nodeType)
                            (K[q] = C(f) ? [] : {}),
                                n(f, c[q] || {}, K[q], k + 1),
                                0 !== Object.keys(K[q]).length ||
                                    ("colorAxis" === q && 0 === k) ||
                                    delete K[q];
                        else if (b[q] !== c[q] || (q in b && !(q in c)))
                            K[q] = A[q];
                    });
                }
                const k = {};
                n(c, b, k, 0);
                return k;
            },
            discardElement: function (b) {
                b && b.parentElement && b.parentElement.removeChild(b);
            },
            erase: function (b, f) {
                let c = b.length;
                for (; c--; )
                    if (b[c] === f) {
                        b.splice(c, 1);
                        break;
                    }
            },
            error: x,
            extend: t,
            extendClass: function (b, f) {
                const c = function () {};
                c.prototype = new b();
                t(c.prototype, f);
                return c;
            },
            find: G,
            fireEvent: d,
            getClosestDistance: function (b, f) {
                const c = !f;
                let d, n, k, q;
                b.forEach((b) => {
                    if (1 < b.length)
                        for (q = n = b.length - 1; 0 < q; q--)
                            (k = b[q] - b[q - 1]),
                                0 > k && !c
                                    ? (null === f || void 0 === f
                                          ? void 0
                                          : f(),
                                      (f = void 0))
                                    : k &&
                                      ("undefined" === typeof d || k < d) &&
                                      (d = k);
                });
                return d;
            },
            getMagnitude: g,
            getNestedProperty: function (b, f) {
                for (b = b.split("."); b.length && v(f); ) {
                    const c = b.shift();
                    if ("undefined" === typeof c || "__proto__" === c) return;
                    if ("this" === c) {
                        let b;
                        z(f) && (b = f["@this"]);
                        return null !== b && void 0 !== b ? b : f;
                    }
                    f = f[c];
                    if (
                        !v(f) ||
                        "function" === typeof f ||
                        "number" === typeof f.nodeType ||
                        f === q
                    )
                        return;
                }
                return f;
            },
            getStyle: w,
            inArray: function (b, f, d) {
                x(32, !1, void 0, {
                    "Highcharts.inArray": "use Array.indexOf",
                });
                return f.indexOf(b, d);
            },
            insertItem: function (b, f) {
                const c = b.options.index,
                    d = f.length;
                let n;
                for (n = b.options.isInternal ? d : 0; n < d + 1; n++)
                    if (
                        !f[n] ||
                        (u(c) && c < m(f[n].options.index, f[n]._i)) ||
                        f[n].options.isInternal
                    ) {
                        f.splice(n, 0, b);
                        break;
                    }
                return n;
            },
            isArray: C,
            isClass: B,
            isDOMElement: H,
            isFunction: function (b) {
                return "function" === typeof b;
            },
            isNumber: u,
            isObject: z,
            isString: L,
            keys: function (b) {
                x(32, !1, void 0, { "Highcharts.keys": "use Object.keys" });
                return Object.keys(b);
            },
            merge: function () {
                let b,
                    f = arguments,
                    d = {};
                const k = function (b, c) {
                    "object" !== typeof b && (b = {});
                    E(c, function (f, d) {
                        "__proto__" !== d &&
                            "constructor" !== d &&
                            (!z(f, !0) || B(f) || H(f)
                                ? (b[d] = c[d])
                                : (b[d] = k(b[d] || {}, f)));
                    });
                    return b;
                };
                !0 === f[0] &&
                    ((d = f[1]), (f = Array.prototype.slice.call(f, 2)));
                const K = f.length;
                for (b = 0; b < K; b++) d = k(d, f[b]);
                return d;
            },
            normalizeTickInterval: function (b, f, d, k, K) {
                let c = b;
                d = m(d, g(b));
                const n = b / d;
                f ||
                    ((f = K
                        ? [1, 1.2, 1.5, 2, 2.5, 3, 4, 5, 6, 8, 10]
                        : [1, 2, 2.5, 5, 10]),
                    !1 === k &&
                        (1 === d
                            ? (f = f.filter(function (b) {
                                  return 0 === b % 1;
                              }))
                            : 0.1 >= d && (f = [1 / d])));
                for (
                    k = 0;
                    k < f.length &&
                    !((c = f[k]),
                    (K && c * d >= b) ||
                        (!K && n <= (f[k] + (f[k + 1] || f[k])) / 2));
                    k++
                );
                return (c = e(c * d, -Math.round(Math.log(0.001) / Math.LN10)));
            },
            objectEach: E,
            offset: function (b) {
                const c = r.documentElement;
                b =
                    b.parentElement || b.parentNode
                        ? b.getBoundingClientRect()
                        : {
                              top: 0,
                              left: 0,
                              width: 0,
                              height: 0,
                          };
                return {
                    top:
                        b.top +
                        (q.pageYOffset || c.scrollTop) -
                        (c.clientTop || 0),
                    left:
                        b.left +
                        (q.pageXOffset || c.scrollLeft) -
                        (c.clientLeft || 0),
                    width: b.width,
                    height: b.height,
                };
            },
            pad: function (b, f, d) {
                return (
                    Array(
                        (f || 2) + 1 - String(b).replace("-", "").length
                    ).join(d || "0") + b
                );
            },
            pick: m,
            pInt: I,
            pushUnique: function (b, f) {
                return 0 > b.indexOf(f) && !!b.push(f);
            },
            relativeLength: function (b, f, d) {
                return /%$/.test(b)
                    ? (f * parseFloat(b)) / 100 + (d || 0)
                    : parseFloat(b);
            },
            removeEvent: F,
            splat: p,
            stableSort: function (b, f) {
                const c = b.length;
                let d, k;
                for (k = 0; k < c; k++) b[k].safeI = k;
                b.sort(function (b, c) {
                    d = f(b, c);
                    return 0 === d ? b.safeI - c.safeI : d;
                });
                for (k = 0; k < c; k++) delete b[k].safeI;
            },
            syncTimeout: function (b, f, d) {
                if (0 < f) return setTimeout(b, f, d);
                b.call(0, d);
                return -1;
            },
            timeUnits: {
                millisecond: 1,
                second: 1e3,
                minute: 6e4,
                hour: 36e5,
                day: 864e5,
                week: 6048e5,
                month: 24192e5,
                year: 314496e5,
            },
            uniqueKey: f,
            useSerialIds: function (c) {
                return (b = m(c, b));
            },
            wrap: function (b, f, d) {
                const c = b[f];
                b[f] = function () {
                    const b = arguments,
                        f = this;
                    return d.apply(
                        this,
                        [
                            function () {
                                return c.apply(
                                    f,
                                    arguments.length ? arguments : b
                                );
                            },
                        ].concat([].slice.call(arguments))
                    );
                };
            },
        };
        ("");
        return G;
    });
    M(a, "Core/Chart/ChartDefaults.js", [], function () {
        return {
            alignThresholds: !1,
            panning: { enabled: !1, type: "x" },
            styledMode: !1,
            borderRadius: 0,
            colorCount: 10,
            allowMutatingData: !0,
            ignoreHiddenSeries: !0,
            spacing: [10, 10, 15, 10],
            resetZoomButton: {
                theme: { zIndex: 6 },
                position: { align: "right", x: -10, y: 10 },
            },
            reflow: !0,
            type: "line",
            zooming: {
                singleTouch: !1,
                resetButton: {
                    theme: { zIndex: 6 },
                    position: { align: "right", x: -10, y: 10 },
                },
            },
            width: null,
            height: null,
            borderColor: "#334eff",
            backgroundColor: "#ffffff",
            plotBorderColor: "#cccccc",
        };
    });
    M(
        a,
        "Core/Color/Color.js",
        [a["Core/Globals.js"], a["Core/Utilities.js"]],
        function (a, y) {
            const { isNumber: x, merge: L, pInt: C } = y;
            class z {
                static parse(a) {
                    return a ? new z(a) : z.None;
                }
                constructor(x) {
                    this.rgba = [NaN, NaN, NaN, NaN];
                    this.input = x;
                    const B = a.Color;
                    if (B && B !== z) return new B(x);
                    this.init(x);
                }
                init(a) {
                    let B;
                    let u;
                    if ("object" === typeof a && "undefined" !== typeof a.stops)
                        this.stops = a.stops.map((l) => new z(l[1]));
                    else if ("string" === typeof a) {
                        this.input = a = z.names[a.toLowerCase()] || a;
                        if ("#" === a.charAt(0)) {
                            var v = a.length;
                            var l = parseInt(a.substr(1), 16);
                            7 === v
                                ? (B = [
                                      (l & 16711680) >> 16,
                                      (l & 65280) >> 8,
                                      l & 255,
                                      1,
                                  ])
                                : 4 === v &&
                                  (B = [
                                      ((l & 3840) >> 4) | ((l & 3840) >> 8),
                                      ((l & 240) >> 4) | (l & 240),
                                      ((l & 15) << 4) | (l & 15),
                                      1,
                                  ]);
                        }
                        if (!B)
                            for (l = z.parsers.length; l-- && !B; )
                                (u = z.parsers[l]),
                                    (v = u.regex.exec(a)) && (B = u.parse(v));
                    }
                    B && (this.rgba = B);
                }
                get(a) {
                    const B = this.input,
                        u = this.rgba;
                    if (
                        "object" === typeof B &&
                        "undefined" !== typeof this.stops
                    ) {
                        const v = L(B);
                        v.stops = [].slice.call(v.stops);
                        this.stops.forEach((l, p) => {
                            v.stops[p] = [v.stops[p][0], l.get(a)];
                        });
                        return v;
                    }
                    return u && x(u[0])
                        ? "rgb" === a || (!a && 1 === u[3])
                            ? "rgb(" + u[0] + "," + u[1] + "," + u[2] + ")"
                            : "a" === a
                            ? `${u[3]}`
                            : "rgba(" + u.join(",") + ")"
                        : B;
                }
                brighten(a) {
                    const B = this.rgba;
                    if (this.stops)
                        this.stops.forEach(function (u) {
                            u.brighten(a);
                        });
                    else if (x(a) && 0 !== a)
                        for (let u = 0; 3 > u; u++)
                            (B[u] += C(255 * a)),
                                0 > B[u] && (B[u] = 0),
                                255 < B[u] && (B[u] = 255);
                    return this;
                }
                setOpacity(a) {
                    this.rgba[3] = a;
                    return this;
                }
                tweenTo(a, B) {
                    const u = this.rgba,
                        v = a.rgba;
                    if (!x(u[0]) || !x(v[0])) return a.input || "none";
                    a = 1 !== v[3] || 1 !== u[3];
                    return (
                        (a ? "rgba(" : "rgb(") +
                        Math.round(v[0] + (u[0] - v[0]) * (1 - B)) +
                        "," +
                        Math.round(v[1] + (u[1] - v[1]) * (1 - B)) +
                        "," +
                        Math.round(v[2] + (u[2] - v[2]) * (1 - B)) +
                        (a ? "," + (v[3] + (u[3] - v[3]) * (1 - B)) : "") +
                        ")"
                    );
                }
            }
            z.names = { white: "#ffffff", black: "#000000" };
            z.parsers = [
                {
                    regex: /rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]?(?:\.[0-9]+)?)\s*\)/,
                    parse: function (a) {
                        return [
                            C(a[1]),
                            C(a[2]),
                            C(a[3]),
                            parseFloat(a[4], 10),
                        ];
                    },
                },
                {
                    regex: /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/,
                    parse: function (a) {
                        return [C(a[1]), C(a[2]), C(a[3]), 1];
                    },
                },
            ];
            z.None = new z("");
            ("");
            return z;
        }
    );
    M(a, "Core/Color/Palettes.js", [], function () {
        return {
            colors: "#2caffe #544fc5 #00e272 #fe6a35 #6b8abc #d568fb #2ee0ca #fa4b42 #feb56a #91e8e1".split(
                " "
            ),
        };
    });
    M(
        a,
        "Core/Time.js",
        [a["Core/Globals.js"], a["Core/Utilities.js"]],
        function (a, y) {
            const { win: x } = a,
                {
                    defined: L,
                    error: C,
                    extend: z,
                    isObject: H,
                    merge: B,
                    objectEach: u,
                    pad: v,
                    pick: l,
                    splat: p,
                    timeUnits: t,
                } = y,
                m =
                    a.isSafari &&
                    x.Intl &&
                    x.Intl.DateTimeFormat.prototype.formatRange,
                h =
                    a.isSafari &&
                    x.Intl &&
                    !x.Intl.DateTimeFormat.prototype.formatRange;
            class g {
                constructor(e) {
                    this.options = {};
                    this.variableTimezone = this.useUTC = !1;
                    this.Date = x.Date;
                    this.getTimezoneOffset = this.timezoneOffsetFunction();
                    this.update(e);
                }
                get(e, g) {
                    if (this.variableTimezone || this.timezoneOffset) {
                        const h = g.getTime(),
                            m = h - this.getTimezoneOffset(g);
                        g.setTime(m);
                        e = g["getUTC" + e]();
                        g.setTime(h);
                        return e;
                    }
                    return this.useUTC ? g["getUTC" + e]() : g["get" + e]();
                }
                set(e, g, h) {
                    if (this.variableTimezone || this.timezoneOffset) {
                        if (
                            "Milliseconds" === e ||
                            "Seconds" === e ||
                            ("Minutes" === e &&
                                0 === this.getTimezoneOffset(g) % 36e5)
                        )
                            return g["setUTC" + e](h);
                        var w = this.getTimezoneOffset(g);
                        w = g.getTime() - w;
                        g.setTime(w);
                        g["setUTC" + e](h);
                        e = this.getTimezoneOffset(g);
                        w = g.getTime() + e;
                        return g.setTime(w);
                    }
                    return this.useUTC || (m && "FullYear" === e)
                        ? g["setUTC" + e](h)
                        : g["set" + e](h);
                }
                update(e = {}) {
                    const g = l(e.useUTC, !0);
                    this.options = e = B(!0, this.options, e);
                    this.Date = e.Date || x.Date || Date;
                    this.timezoneOffset =
                        ((this.useUTC = g) && e.timezoneOffset) || void 0;
                    this.getTimezoneOffset = this.timezoneOffsetFunction();
                    this.variableTimezone =
                        g && !(!e.getTimezoneOffset && !e.timezone);
                }
                makeTime(e, g, m, p, d, k) {
                    let r, q, w;
                    this.useUTC
                        ? ((r = this.Date.UTC.apply(0, arguments)),
                          (q = this.getTimezoneOffset(r)),
                          (r += q),
                          (w = this.getTimezoneOffset(r)),
                          q !== w
                              ? (r += w - q)
                              : q - 36e5 !== this.getTimezoneOffset(r - 36e5) ||
                                h ||
                                (r -= 36e5))
                        : (r = new this.Date(
                              e,
                              g,
                              l(m, 1),
                              l(p, 0),
                              l(d, 0),
                              l(k, 0)
                          ).getTime());
                    return r;
                }
                timezoneOffsetFunction() {
                    const e = this,
                        g = this.options,
                        h = g.getTimezoneOffset,
                        m = g.moment || x.moment;
                    if (!this.useUTC)
                        return function (d) {
                            return (
                                6e4 * new Date(d.toString()).getTimezoneOffset()
                            );
                        };
                    if (g.timezone) {
                        if (m)
                            return function (d) {
                                return 6e4 * -m.tz(d, g.timezone).utcOffset();
                            };
                        C(25);
                    }
                    return this.useUTC && h
                        ? function (d) {
                              return 6e4 * h(d.valueOf());
                          }
                        : function () {
                              return 6e4 * (e.timezoneOffset || 0);
                          };
                }
                dateFormat(e, g, h) {
                    if (!L(g) || isNaN(g))
                        return (
                            (a.defaultOptions.lang &&
                                a.defaultOptions.lang.invalidDate) ||
                            ""
                        );
                    e = l(e, "%Y-%m-%d %H:%M:%S");
                    const m = this;
                    var d = new this.Date(g);
                    const k = this.get("Hours", d),
                        r = this.get("Day", d),
                        q = this.get("Date", d),
                        w = this.get("Month", d),
                        b = this.get("FullYear", d),
                        f = a.defaultOptions.lang,
                        c = f && f.weekdays,
                        n = f && f.shortWeekdays;
                    d = z(
                        {
                            a: n ? n[r] : c[r].substr(0, 3),
                            A: c[r],
                            d: v(q),
                            e: v(q, 2, " "),
                            w: r,
                            b: f.shortMonths[w],
                            B: f.months[w],
                            m: v(w + 1),
                            o: w + 1,
                            y: b.toString().substr(2, 2),
                            Y: b,
                            H: v(k),
                            k,
                            I: v(k % 12 || 12),
                            l: k % 12 || 12,
                            M: v(this.get("Minutes", d)),
                            p: 12 > k ? "AM" : "PM",
                            P: 12 > k ? "am" : "pm",
                            S: v(d.getSeconds()),
                            L: v(Math.floor(g % 1e3), 3),
                        },
                        a.dateFormats
                    );
                    u(d, function (b, c) {
                        for (; -1 !== e.indexOf("%" + c); )
                            e = e.replace(
                                "%" + c,
                                "function" === typeof b ? b.call(m, g) : b
                            );
                    });
                    return h ? e.substr(0, 1).toUpperCase() + e.substr(1) : e;
                }
                resolveDTLFormat(e) {
                    return H(e, !0)
                        ? e
                        : ((e = p(e)),
                          {
                              main: e[0],
                              from: e[1],
                              to: e[2],
                          });
                }
                getTimeTicks(e, g, h, m) {
                    const d = this,
                        k = [],
                        r = {};
                    var q = new d.Date(g);
                    const w = e.unitRange,
                        b = e.count || 1;
                    let f;
                    m = l(m, 1);
                    if (L(g)) {
                        d.set(
                            "Milliseconds",
                            q,
                            w >= t.second
                                ? 0
                                : b * Math.floor(d.get("Milliseconds", q) / b)
                        );
                        w >= t.second &&
                            d.set(
                                "Seconds",
                                q,
                                w >= t.minute
                                    ? 0
                                    : b * Math.floor(d.get("Seconds", q) / b)
                            );
                        w >= t.minute &&
                            d.set(
                                "Minutes",
                                q,
                                w >= t.hour
                                    ? 0
                                    : b * Math.floor(d.get("Minutes", q) / b)
                            );
                        w >= t.hour &&
                            d.set(
                                "Hours",
                                q,
                                w >= t.day
                                    ? 0
                                    : b * Math.floor(d.get("Hours", q) / b)
                            );
                        w >= t.day &&
                            d.set(
                                "Date",
                                q,
                                w >= t.month
                                    ? 1
                                    : Math.max(
                                          1,
                                          b * Math.floor(d.get("Date", q) / b)
                                      )
                            );
                        if (w >= t.month) {
                            d.set(
                                "Month",
                                q,
                                w >= t.year
                                    ? 0
                                    : b * Math.floor(d.get("Month", q) / b)
                            );
                            var c = d.get("FullYear", q);
                        }
                        w >= t.year && d.set("FullYear", q, c - (c % b));
                        w === t.week &&
                            ((c = d.get("Day", q)),
                            d.set(
                                "Date",
                                q,
                                d.get("Date", q) - c + m + (c < m ? -7 : 0)
                            ));
                        c = d.get("FullYear", q);
                        m = d.get("Month", q);
                        const n = d.get("Date", q),
                            e = d.get("Hours", q);
                        g = q.getTime();
                        (!d.variableTimezone && d.useUTC) ||
                            !L(h) ||
                            (f =
                                h - g > 4 * t.month ||
                                d.getTimezoneOffset(g) !==
                                    d.getTimezoneOffset(h));
                        g = q.getTime();
                        for (q = 1; g < h; )
                            k.push(g),
                                (g =
                                    w === t.year
                                        ? d.makeTime(c + q * b, 0)
                                        : w === t.month
                                        ? d.makeTime(c, m + q * b)
                                        : !f || (w !== t.day && w !== t.week)
                                        ? f && w === t.hour && 1 < b
                                            ? d.makeTime(c, m, n, e + q * b)
                                            : g + w * b
                                        : d.makeTime(
                                              c,
                                              m,
                                              n + q * b * (w === t.day ? 1 : 7)
                                          )),
                                q++;
                        k.push(g);
                        w <= t.hour &&
                            1e4 > k.length &&
                            k.forEach(function (b) {
                                0 === b % 18e5 &&
                                    "000000000" ===
                                        d.dateFormat("%H%M%S%L", b) &&
                                    (r[b] = "day");
                            });
                    }
                    k.info = z(e, { higherRanks: r, totalRange: w * b });
                    return k;
                }
                getDateFormat(e, g, h, m) {
                    const d = this.dateFormat("%m-%d %H:%M:%S.%L", g),
                        k = {
                            millisecond: 15,
                            second: 12,
                            minute: 9,
                            hour: 6,
                            day: 3,
                        };
                    let r,
                        q = "millisecond";
                    for (r in t) {
                        if (
                            e === t.week &&
                            +this.dateFormat("%w", g) === h &&
                            "00:00:00.000" === d.substr(6)
                        ) {
                            r = "week";
                            break;
                        }
                        if (t[r] > e) {
                            r = q;
                            break;
                        }
                        if (
                            k[r] &&
                            d.substr(k[r]) !== "01-01 00:00:00.000".substr(k[r])
                        )
                            break;
                        "week" !== r && (q = r);
                    }
                    return this.resolveDTLFormat(m[r]).main;
                }
            }
            ("");
            return g;
        }
    );
    M(
        a,
        "Core/Defaults.js",
        [
            a["Core/Chart/ChartDefaults.js"],
            a["Core/Color/Color.js"],
            a["Core/Globals.js"],
            a["Core/Color/Palettes.js"],
            a["Core/Time.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C, z) {
            const { isTouchDevice: x, svg: B } = I,
                { merge: u } = z,
                v = {
                    colors: L.colors,
                    symbols: [
                        "circle",
                        "diamond",
                        "square",
                        "triangle",
                        "triangle-down",
                    ],
                    lang: {
                        loading: "Loading...",
                        months: "January February March April May June July August September October November December".split(
                            " "
                        ),
                        shortMonths:
                            "Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec".split(
                                " "
                            ),
                        weekdays:
                            "Sunday Monday Tuesday Wednesday Thursday Friday Saturday".split(
                                " "
                            ),
                        decimalPoint: ".",
                        numericSymbols: "kMGTPE".split(""),
                        resetZoom: "Reset zoom",
                        resetZoomTitle: "Reset zoom level 1:1",
                        thousandsSep: " ",
                    },
                    global: {},
                    time: {
                        Date: void 0,
                        getTimezoneOffset: void 0,
                        timezone: void 0,
                        timezoneOffset: 0,
                        useUTC: !0,
                    },
                    chart: a,
                    title: {
                        style: { color: "#333333", fontWeight: "bold" },
                        text: "Chart title",
                        align: "center",
                        margin: 15,
                        widthAdjust: -44,
                    },
                    subtitle: {
                        style: { color: "#666666", fontSize: "0.8em" },
                        text: "",
                        align: "center",
                        widthAdjust: -44,
                    },
                    caption: {
                        margin: 15,
                        style: { color: "#666666", fontSize: "0.8em" },
                        text: "",
                        align: "left",
                        verticalAlign: "bottom",
                    },
                    plotOptions: {},
                    legend: {
                        enabled: !0,
                        align: "center",
                        alignColumns: !0,
                        className: "highcharts-no-tooltip",
                        layout: "horizontal",
                        itemMarginBottom: 2,
                        itemMarginTop: 2,
                        labelFormatter: function () {
                            return this.name;
                        },
                        borderColor: "#999999",
                        borderRadius: 0,
                        navigation: {
                            style: { fontSize: "0.8em" },
                            activeColor: "#0022ff",
                            inactiveColor: "#cccccc",
                        },
                        itemStyle: {
                            color: "#333333",
                            cursor: "pointer",
                            fontSize: "0.8em",
                            textDecoration: "none",
                            textOverflow: "ellipsis",
                        },
                        itemHoverStyle: { color: "#000000" },
                        itemHiddenStyle: {
                            color: "#666666",
                            textDecoration: "line-through",
                        },
                        shadow: !1,
                        itemCheckboxStyle: {
                            position: "absolute",
                            width: "13px",
                            height: "13px",
                        },
                        squareSymbol: !0,
                        symbolPadding: 5,
                        verticalAlign: "bottom",
                        x: 0,
                        y: 0,
                        title: {
                            style: { fontSize: "0.8em", fontWeight: "bold" },
                        },
                    },
                    loading: {
                        labelStyle: {
                            fontWeight: "bold",
                            position: "relative",
                            top: "45%",
                        },
                        style: {
                            position: "absolute",
                            backgroundColor: "#ffffff",
                            opacity: 0.5,
                            textAlign: "center",
                        },
                    },
                    tooltip: {
                        enabled: !0,
                        animation: B,
                        borderRadius: 3,
                        dateTimeLabelFormats: {
                            millisecond: "%A, %e %b, %H:%M:%S.%L",
                            second: "%A, %e %b, %H:%M:%S",
                            minute: "%A, %e %b, %H:%M",
                            hour: "%A, %e %b, %H:%M",
                            day: "%A, %e %b %Y",
                            week: "Week from %A, %e %b %Y",
                            month: "%B %Y",
                            year: "%Y",
                        },
                        footerFormat: "",
                        headerShape: "callout",
                        hideDelay: 500,
                        padding: 8,
                        shape: "callout",
                        shared: !1,
                        snap: x ? 25 : 10,
                        headerFormat:
                            '<span style="font-size: 0.8em">{point.key}</span><br/>',
                        pointFormat:
                            '<span style="color:{point.color}">\u25cf</span> {series.name}: <b>{point.y}</b><br/>',
                        backgroundColor: "#ffffff",
                        borderWidth: void 0,
                        shadow: !0,
                        stickOnContact: !1,
                        style: {
                            color: "#333333",
                            cursor: "default",
                            fontSize: "0.8em",
                        },
                        useHTML: !1,
                    },
                    credits: {
                        enabled: !0,
                        href: "https://www.highcharts.com?credits",
                        position: {
                            align: "right",
                            x: -10,
                            verticalAlign: "bottom",
                            y: -5,
                        },
                        style: {
                            cursor: "pointer",
                            color: "#999999",
                            fontSize: "0.6em",
                        },
                        text: "",
                    },
                };
            v.chart.styledMode = !1;
            ("");
            const l = new C(v.time);
            a = {
                defaultOptions: v,
                defaultTime: l,
                getOptions: function () {
                    return v;
                },
                setOptions: function (a) {
                    u(!0, v, a);
                    if (a.time || a.global)
                        I.time
                            ? I.time.update(
                                  u(v.global, v.time, a.global, a.time)
                              )
                            : (I.time = l);
                    return v;
                },
            };
            ("");
            return a;
        }
    );
    M(
        a,
        "Core/Animation/Fx.js",
        [
            a["Core/Color/Color.js"],
            a["Core/Globals.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { parse: x } = a,
                { win: C } = y,
                { isNumber: z, objectEach: H } = I;
            class B {
                constructor(a, v, l) {
                    this.pos = NaN;
                    this.options = v;
                    this.elem = a;
                    this.prop = l;
                }
                dSetter() {
                    var a = this.paths;
                    const v = a && a[0];
                    a = a && a[1];
                    const l = this.now || 0;
                    let p = [];
                    if (1 !== l && v && a)
                        if (v.length === a.length && 1 > l)
                            for (let t = 0; t < a.length; t++) {
                                const m = v[t],
                                    h = a[t],
                                    g = [];
                                for (let e = 0; e < h.length; e++) {
                                    const w = m[e],
                                        a = h[e];
                                    z(w) &&
                                    z(a) &&
                                    ("A" !== h[0] || (4 !== e && 5 !== e))
                                        ? (g[e] = w + l * (a - w))
                                        : (g[e] = a);
                                }
                                p.push(g);
                            }
                        else p = a;
                    else p = this.toD || [];
                    this.elem.attr("d", p, void 0, !0);
                }
                update() {
                    const a = this.elem,
                        v = this.prop,
                        l = this.now,
                        p = this.options.step;
                    if (this[v + "Setter"]) this[v + "Setter"]();
                    else
                        a.attr
                            ? a.element && a.attr(v, l, null, !0)
                            : (a.style[v] = l + this.unit);
                    p && p.call(a, l, this);
                }
                run(a, v, l) {
                    const p = this,
                        t = p.options,
                        m = function (e) {
                            return m.stopped ? !1 : p.step(e);
                        },
                        h =
                            C.requestAnimationFrame ||
                            function (e) {
                                setTimeout(e, 13);
                            },
                        g = function () {
                            for (let e = 0; e < B.timers.length; e++)
                                B.timers[e]() || B.timers.splice(e--, 1);
                            B.timers.length && h(g);
                        };
                    a !== v || this.elem["forceAnimate:" + this.prop]
                        ? ((this.startTime = +new Date()),
                          (this.start = a),
                          (this.end = v),
                          (this.unit = l),
                          (this.now = this.start),
                          (this.pos = 0),
                          (m.elem = this.elem),
                          (m.prop = this.prop),
                          m() && 1 === B.timers.push(m) && h(g))
                        : (delete t.curAnim[this.prop],
                          t.complete &&
                              0 === Object.keys(t.curAnim).length &&
                              t.complete.call(this.elem));
                }
                step(a) {
                    const v = +new Date(),
                        l = this.options,
                        p = this.elem,
                        t = l.complete,
                        m = l.duration,
                        h = l.curAnim;
                    let g;
                    p.attr && !p.element
                        ? (a = !1)
                        : a || v >= m + this.startTime
                        ? ((this.now = this.end),
                          (this.pos = 1),
                          this.update(),
                          (g = h[this.prop] = !0),
                          H(h, function (e) {
                              !0 !== e && (g = !1);
                          }),
                          g && t && t.call(p),
                          (a = !1))
                        : ((this.pos = l.easing((v - this.startTime) / m)),
                          (this.now =
                              this.start + (this.end - this.start) * this.pos),
                          this.update(),
                          (a = !0));
                    return a;
                }
                initPath(a, v, l) {
                    function p(d, k) {
                        for (; d.length < E; ) {
                            var r = d[0];
                            const q = k[E - d.length];
                            q &&
                                "M" === r[0] &&
                                (d[0] =
                                    "C" === q[0]
                                        ? [
                                              "C",
                                              r[1],
                                              r[2],
                                              r[1],
                                              r[2],
                                              r[1],
                                              r[2],
                                          ]
                                        : ["L", r[1], r[2]]);
                            d.unshift(r);
                            g && ((r = d.pop()), d.push(d[d.length - 1], r));
                        }
                    }

                    function t(d, k) {
                        for (; d.length < E; )
                            if (
                                ((k = d[Math.floor(d.length / e) - 1].slice()),
                                "C" === k[0] && ((k[1] = k[5]), (k[2] = k[6])),
                                g)
                            ) {
                                const r = d[Math.floor(d.length / e)].slice();
                                d.splice(d.length / 2, 0, k, r);
                            } else d.push(k);
                    }
                    const m = a.startX,
                        h = a.endX;
                    l = l.slice();
                    const g = a.isArea,
                        e = g ? 2 : 1;
                    let w, E, F;
                    v = v && v.slice();
                    if (!v) return [l, l];
                    if (m && h && h.length) {
                        for (a = 0; a < m.length; a++)
                            if (m[a] === h[0]) {
                                w = a;
                                break;
                            } else if (m[0] === h[h.length - m.length + a]) {
                                w = a;
                                F = !0;
                                break;
                            } else if (
                                m[m.length - 1] === h[h.length - m.length + a]
                            ) {
                                w = m.length - a;
                                break;
                            }
                        "undefined" === typeof w && (v = []);
                    }
                    v.length &&
                        z(w) &&
                        ((E = l.length + w * e),
                        F ? (p(v, l), t(l, v)) : (p(l, v), t(v, l)));
                    return [v, l];
                }
                fillSetter() {
                    B.prototype.strokeSetter.apply(this, arguments);
                }
                strokeSetter() {
                    this.elem.attr(
                        this.prop,
                        x(this.start).tweenTo(x(this.end), this.pos),
                        void 0,
                        !0
                    );
                }
            }
            B.timers = [];
            return B;
        }
    );
    M(
        a,
        "Core/Animation/AnimationUtilities.js",
        [a["Core/Animation/Fx.js"], a["Core/Utilities.js"]],
        function (a, y) {
            function x(a) {
                return u(a)
                    ? v({ duration: 500, defer: 0 }, a)
                    : { duration: a ? 500 : 0, defer: 0 };
            }

            function L(l, m) {
                let h = a.timers.length;
                for (; h--; )
                    a.timers[h].elem !== l ||
                        (m && m !== a.timers[h].prop) ||
                        (a.timers[h].stopped = !0);
            }
            const {
                defined: C,
                getStyle: z,
                isArray: H,
                isNumber: B,
                isObject: u,
                merge: v,
                objectEach: l,
                pick: p,
            } = y;
            return {
                animate: function (p, m, h) {
                    let g,
                        e = "",
                        w,
                        E,
                        F;
                    u(h) ||
                        ((F = arguments),
                        (h = { duration: F[2], easing: F[3], complete: F[4] }));
                    B(h.duration) || (h.duration = 400);
                    h.easing =
                        "function" === typeof h.easing
                            ? h.easing
                            : Math[h.easing] || Math.easeInOutSine;
                    h.curAnim = v(m);
                    l(m, function (d, k) {
                        L(p, k);
                        E = new a(p, h, k);
                        w = void 0;
                        "d" === k && H(m.d)
                            ? ((E.paths = E.initPath(p, p.pathArray, m.d)),
                              (E.toD = m.d),
                              (g = 0),
                              (w = 1))
                            : p.attr
                            ? (g = p.attr(k))
                            : ((g = parseFloat(z(p, k)) || 0),
                              "opacity" !== k && (e = "px"));
                        w || (w = d);
                        "string" === typeof w &&
                            w.match("px") &&
                            (w = w.replace(/px/g, ""));
                        E.run(g, w, e);
                    });
                },
                animObject: x,
                getDeferredAnimation: function (a, m, h) {
                    const g = x(m);
                    let e = 0,
                        w = 0;
                    (h ? [h] : a.series).forEach((h) => {
                        h = x(h.options.animation);
                        e =
                            m && C(m.defer)
                                ? g.defer
                                : Math.max(e, h.duration + h.defer);
                        w = Math.min(g.duration, h.duration);
                    });
                    a.renderer.forExport && (e = 0);
                    return {
                        defer: Math.max(0, e - w),
                        duration: Math.min(e, w),
                    };
                },
                setAnimation: function (a, m) {
                    m.renderer.globalAnimation = p(
                        a,
                        m.options.chart.animation,
                        !0
                    );
                },
                stop: L,
            };
        }
    );
    M(
        a,
        "Core/Renderer/HTML/AST.js",
        [a["Core/Globals.js"], a["Core/Utilities.js"]],
        function (a, y) {
            const { SVG_NS: x, win: L } = a,
                {
                    attr: C,
                    createElement: z,
                    css: H,
                    error: B,
                    isFunction: u,
                    isString: v,
                    objectEach: l,
                    splat: p,
                } = y;
            ({ trustedTypes: y } = L);
            const t =
                y &&
                u(y.createPolicy) &&
                y.createPolicy("highcharts", { createHTML: (e) => e });
            y = t ? t.createHTML("") : "";
            try {
                var m = !!new DOMParser().parseFromString(y, "text/html");
            } catch (e) {
                m = !1;
            }
            const h = m;
            class g {
                static filterUserAttributes(e) {
                    l(e, (h, m) => {
                        let a = !0;
                        -1 === g.allowedAttributes.indexOf(m) && (a = !1);
                        -1 !==
                            [
                                "background",
                                "dynsrc",
                                "href",
                                "lowsrc",
                                "src",
                            ].indexOf(m) &&
                            (a =
                                v(h) &&
                                g.allowedReferences.some(
                                    (d) => 0 === h.indexOf(d)
                                ));
                        a ||
                            (B(33, !1, void 0, {
                                "Invalid attribute in config": `${m}`,
                            }),
                            delete e[m]);
                        v(h) && e[m] && (e[m] = h.replace(/</g, "&lt;"));
                    });
                    return e;
                }
                static parseStyle(e) {
                    return e.split(";").reduce((e, g) => {
                        g = g.split(":").map((d) => d.trim());
                        const h = g.shift();
                        h &&
                            g.length &&
                            (e[
                                h.replace(/-([a-z])/g, (d) =>
                                    d[1].toUpperCase()
                                )
                            ] = g.join(":"));
                        return e;
                    }, {});
                }
                static setElementHTML(e, h) {
                    e.innerHTML = g.emptyHTML;
                    h && new g(h).addToDOM(e);
                }
                constructor(e) {
                    this.nodes =
                        "string" === typeof e ? this.parseMarkup(e) : e;
                }
                addToDOM(e) {
                    function h(e, m) {
                        let d;
                        p(e).forEach(function (k) {
                            var e = k.tagName;
                            const q = k.textContent
                                    ? a.doc.createTextNode(k.textContent)
                                    : void 0,
                                w = g.bypassHTMLFiltering;
                            let b;
                            if (e)
                                if ("#text" === e) b = q;
                                else if (-1 !== g.allowedTags.indexOf(e) || w) {
                                    e = a.doc.createElementNS(
                                        "svg" === e ? x : m.namespaceURI || x,
                                        e
                                    );
                                    const f = k.attributes || {};
                                    l(k, function (b, d) {
                                        "tagName" !== d &&
                                            "attributes" !== d &&
                                            "children" !== d &&
                                            "style" !== d &&
                                            "textContent" !== d &&
                                            (f[d] = b);
                                    });
                                    C(e, w ? f : g.filterUserAttributes(f));
                                    k.style && H(e, k.style);
                                    q && e.appendChild(q);
                                    h(k.children || [], e);
                                    b = e;
                                } else
                                    B(33, !1, void 0, {
                                        "Invalid tagName in config": e,
                                    });
                            b && m.appendChild(b);
                            d = b;
                        });
                        return d;
                    }
                    return h(this.nodes, e);
                }
                parseMarkup(e) {
                    const m = [];
                    e = e.trim().replace(/ style=(["'])/g, " data-style=$1");
                    if (h)
                        e = new DOMParser().parseFromString(
                            t ? t.createHTML(e) : e,
                            "text/html"
                        );
                    else {
                        const g = z("div");
                        g.innerHTML = e;
                        e = { body: g };
                    }
                    const a = (e, d) => {
                        var k = e.nodeName.toLowerCase();
                        const r = { tagName: k };
                        "#text" === k && (r.textContent = e.textContent || "");
                        if ((k = e.attributes)) {
                            const d = {};
                            [].forEach.call(k, (k) => {
                                "data-style" === k.name
                                    ? (r.style = g.parseStyle(k.value))
                                    : (d[k.name] = k.value);
                            });
                            r.attributes = d;
                        }
                        if (e.childNodes.length) {
                            const d = [];
                            [].forEach.call(e.childNodes, (k) => {
                                a(k, d);
                            });
                            d.length && (r.children = d);
                        }
                        d.push(r);
                    };
                    [].forEach.call(e.body.childNodes, (e) => a(e, m));
                    return m;
                }
            }
            g.allowedAttributes =
                "alt aria-controls aria-describedby aria-expanded aria-haspopup aria-hidden aria-label aria-labelledby aria-live aria-pressed aria-readonly aria-roledescription aria-selected class clip-path color colspan cx cy d dx dy disabled fill flood-color flood-opacity height href id in markerHeight markerWidth offset opacity orient padding paddingLeft paddingRight patternUnits r refX refY role scope slope src startOffset stdDeviation stroke stroke-linecap stroke-width style tableValues result rowspan summary target tabindex text-align text-anchor textAnchor textLength title type valign width x x1 x2 xlink:href y y1 y2 zIndex".split(
                    " "
                );
            g.allowedReferences = "https:// http:// mailto: / ../ ./ #".split(
                " "
            );
            g.allowedTags =
                "a abbr b br button caption circle clipPath code dd defs div dl dt em feComponentTransfer feDropShadow feFuncA feFuncB feFuncG feFuncR feGaussianBlur feOffset feMerge feMergeNode filter h1 h2 h3 h4 h5 h6 hr i img li linearGradient marker ol p path pattern pre rect small span stop strong style sub sup svg table text textPath thead title tbody tspan td th tr u ul #text".split(
                    " "
                );
            g.emptyHTML = y;
            g.bypassHTMLFiltering = !1;
            ("");
            return g;
        }
    );
    M(
        a,
        "Core/Templating.js",
        [a["Core/Defaults.js"], a["Core/Utilities.js"]],
        function (a, y) {
            function x(h = "", g, e) {
                const a = /\{([a-zA-Z0-9:\.,;\-\/<>%_@"'= #\(\)]+)\}/g,
                    l = /\(([a-zA-Z0-9:\.,;\-\/<>%_@"'= ]+)\)/g,
                    v = [],
                    d = /f$/,
                    k = /\.([0-9])/,
                    r = C.lang,
                    q = (e && e.time) || z,
                    G = (e && e.numberFormatter) || L,
                    b = (b = "") => {
                        let c;
                        return "true" === b
                            ? !0
                            : "false" === b
                            ? !1
                            : (c = Number(b)).toString() === b
                            ? c
                            : B(b, g);
                    };
                let f,
                    c,
                    n = 0,
                    P;
                for (; null !== (f = a.exec(h)); ) {
                    const b = l.exec(f[1]);
                    b && ((f = b), (P = !0));
                    (c && c.isBlock) ||
                        (c = {
                            ctx: g,
                            expression: f[1],
                            find: f[0],
                            isBlock: "#" === f[1].charAt(0),
                            start: f.index,
                            startInner: f.index + f[0].length,
                            length: f[0].length,
                        });
                    var D = f[1].split(" ")[0].replace("#", "");
                    m[D] &&
                        (c.isBlock && D === c.fn && n++, c.fn || (c.fn = D));
                    D = "else" === f[1];
                    if (c.isBlock && c.fn && (f[1] === `/${c.fn}` || D))
                        if (n) D || n--;
                        else {
                            var K = c.startInner;
                            K = h.substr(K, f.index - K);
                            void 0 === c.body
                                ? ((c.body = K),
                                  (c.startInner = f.index + f[0].length))
                                : (c.elseBody = K);
                            c.find += K + f[0];
                            D || (v.push(c), (c = void 0));
                        }
                    else c.isBlock || v.push(c);
                    if (b && (null === c || void 0 === c || !c.isBlock)) break;
                }
                v.forEach((c) => {
                    const { body: f, elseBody: n, expression: K, fn: e } = c;
                    var A;
                    if (e) {
                        var a = [c],
                            w = K.split(" ");
                        for (A = m[e].length; A--; ) a.unshift(b(w[A + 1]));
                        A = m[e].apply(g, a);
                        c.isBlock &&
                            "boolean" === typeof A &&
                            (A = x(A ? f : n, g));
                    } else
                        (a = K.split(":")),
                            (A = b(a.shift() || "")),
                            a.length &&
                                "number" === typeof A &&
                                ((a = a.join(":")),
                                d.test(a)
                                    ? ((w = parseInt(
                                          (a.match(k) || ["", "-1"])[1],
                                          10
                                      )),
                                      null !== A &&
                                          (A = G(
                                              A,
                                              w,
                                              r.decimalPoint,
                                              -1 < a.indexOf(",")
                                                  ? r.thousandsSep
                                                  : ""
                                          )))
                                    : (A = q.dateFormat(a, A)));
                    h = h.replace(c.find, p(A, ""));
                });
                return P ? x(h, g, e) : h;
            }

            function L(h, g, e, a) {
                h = +h || 0;
                g = +g;
                const m = C.lang;
                var w = (h.toString().split(".")[1] || "").split("e")[0].length;
                const d = h.toString().split("e"),
                    k = g;
                if (-1 === g) g = Math.min(w, 20);
                else if (!v(g)) g = 2;
                else if (g && d[1] && 0 > d[1]) {
                    var r = g + +d[1];
                    0 <= r
                        ? ((d[0] = (+d[0]).toExponential(r).split("e")[0]),
                          (g = r))
                        : ((d[0] = d[0].split(".")[0] || 0),
                          (h =
                              20 > g
                                  ? (d[0] * Math.pow(10, d[1])).toFixed(g)
                                  : 0),
                          (d[1] = 0));
                }
                r = (
                    Math.abs(d[1] ? d[0] : h) +
                    Math.pow(10, -Math.max(g, w) - 1)
                ).toFixed(g);
                w = String(t(r));
                const q = 3 < w.length ? w.length % 3 : 0;
                e = p(e, m.decimalPoint);
                a = p(a, m.thousandsSep);
                h = (0 > h ? "-" : "") + (q ? w.substr(0, q) + a : "");
                h =
                    0 > +d[1] && !k
                        ? "0"
                        : h + w.substr(q).replace(/(\d{3})(?=\d)/g, "$1" + a);
                g && (h += e + r.slice(-g));
                d[1] && 0 !== +h && (h += "e" + d[1]);
                return h;
            }
            const { defaultOptions: C, defaultTime: z } = a,
                {
                    extend: H,
                    getNestedProperty: B,
                    isArray: u,
                    isNumber: v,
                    isObject: l,
                    pick: p,
                    pInt: t,
                } = y,
                m = {
                    add: (h, g) => h + g,
                    divide: (h, g) => (0 !== g ? h / g : ""),
                    eq: (h, g) => h == g,
                    each: function (h) {
                        const g = arguments[arguments.length - 1];
                        return u(h)
                            ? h
                                  .map((e, a) =>
                                      x(
                                          g.body,
                                          H(l(e) ? e : { "@this": e }, {
                                              "@index": a,
                                              "@first": 0 === a,
                                              "@last": a === h.length - 1,
                                          })
                                      )
                                  )
                                  .join("")
                            : !1;
                    },
                    ge: (h, g) => h >= g,
                    gt: (h, g) => h > g,
                    if: (h) => !!h,
                    le: (h, g) => h <= g,
                    lt: (h, g) => h < g,
                    multiply: (h, g) => h * g,
                    ne: (h, g) => h != g,
                    subtract: (h, g) => h - g,
                    unless: (h) => !h,
                };
            return {
                dateFormat: function (h, g, e) {
                    return z.dateFormat(h, g, e);
                },
                format: x,
                helpers: m,
                numberFormat: L,
            };
        }
    );
    M(
        a,
        "Core/Renderer/RendererUtilities.js",
        [a["Core/Utilities.js"]],
        function (a) {
            const { clamp: x, pick: I, stableSort: L } = a;
            var C;
            (function (a) {
                function y(a, u, v) {
                    const l = a;
                    var p = l.reducedLen || u,
                        t = (e, g) => (g.rank || 0) - (e.rank || 0);
                    const m = (e, g) => e.target - g.target;
                    let h,
                        g = !0,
                        e = [],
                        w = 0;
                    for (h = a.length; h--; ) w += a[h].size;
                    if (w > p) {
                        L(a, t);
                        for (w = h = 0; w <= p; ) (w += a[h].size), h++;
                        e = a.splice(h - 1, a.length);
                    }
                    L(a, m);
                    for (
                        a = a.map((e) => ({
                            size: e.size,
                            targets: [e.target],
                            align: I(e.align, 0.5),
                        }));
                        g;

                    ) {
                        for (h = a.length; h--; )
                            (p = a[h]),
                                (t =
                                    (Math.min.apply(0, p.targets) +
                                        Math.max.apply(0, p.targets)) /
                                    2),
                                (p.pos = x(
                                    t - p.size * p.align,
                                    0,
                                    u - p.size
                                ));
                        h = a.length;
                        for (g = !1; h--; )
                            0 < h &&
                                a[h - 1].pos + a[h - 1].size > a[h].pos &&
                                ((a[h - 1].size += a[h].size),
                                (a[h - 1].targets = a[h - 1].targets.concat(
                                    a[h].targets
                                )),
                                (a[h - 1].align = 0.5),
                                a[h - 1].pos + a[h - 1].size > u &&
                                    (a[h - 1].pos = u - a[h - 1].size),
                                a.splice(h, 1),
                                (g = !0));
                    }
                    l.push.apply(l, e);
                    h = 0;
                    a.some((e) => {
                        let g = 0;
                        return (e.targets || []).some(() => {
                            l[h].pos = e.pos + g;
                            if (
                                "undefined" !== typeof v &&
                                Math.abs(l[h].pos - l[h].target) > v
                            )
                                return (
                                    l
                                        .slice(0, h + 1)
                                        .forEach((d) => delete d.pos),
                                    (l.reducedLen =
                                        (l.reducedLen || u) - 0.1 * u),
                                    l.reducedLen > 0.1 * u && y(l, u, v),
                                    !0
                                );
                            g += l[h].size;
                            h++;
                            return !1;
                        });
                    });
                    L(l, m);
                    return l;
                }
                a.distribute = y;
            })(C || (C = {}));
            return C;
        }
    );
    M(
        a,
        "Core/Renderer/SVG/SVGElement.js",
        [
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Color/Color.js"],
            a["Core/Globals.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L) {
            const { animate: x, animObject: z, stop: H } = a,
                { deg2rad: B, doc: u, svg: v, SVG_NS: l, win: p } = I,
                {
                    addEvent: t,
                    attr: m,
                    createElement: h,
                    css: g,
                    defined: e,
                    erase: w,
                    extend: E,
                    fireEvent: F,
                    isArray: d,
                    isFunction: k,
                    isObject: r,
                    isString: q,
                    merge: G,
                    objectEach: b,
                    pick: f,
                    pInt: c,
                    syncTimeout: n,
                    uniqueKey: P,
                } = L;
            class D {
                constructor() {
                    this.element = void 0;
                    this.onEvents = {};
                    this.opacity = 1;
                    this.renderer = void 0;
                    this.SVG_NS = l;
                }
                _defaultGetter(b) {
                    b = f(
                        this[b + "Value"],
                        this[b],
                        this.element ? this.element.getAttribute(b) : null,
                        0
                    );
                    /^[\-0-9\.]+$/.test(b) && (b = parseFloat(b));
                    return b;
                }
                _defaultSetter(b, c, f) {
                    f.setAttribute(c, b);
                }
                add(b) {
                    const c = this.renderer,
                        f = this.element;
                    let d;
                    b && (this.parentGroup = b);
                    "undefined" !== typeof this.textStr &&
                        "text" === this.element.nodeName &&
                        c.buildText(this);
                    this.added = !0;
                    if (!b || b.handleZ || this.zIndex) d = this.zIndexSetter();
                    d || (b ? b.element : c.box).appendChild(f);
                    if (this.onAdd) this.onAdd();
                    return this;
                }
                addClass(b, c) {
                    const f = c ? "" : this.attr("class") || "";
                    b = (b || "")
                        .split(/ /g)
                        .reduce(
                            function (b, c) {
                                -1 === f.indexOf(c) && b.push(c);
                                return b;
                            },
                            f ? [f] : []
                        )
                        .join(" ");
                    b !== f && this.attr("class", b);
                    return this;
                }
                afterSetters() {
                    this.doTransform &&
                        (this.updateTransform(), (this.doTransform = !1));
                }
                align(b, c, d) {
                    const k = {};
                    var n = this.renderer,
                        e = n.alignedObjects,
                        A;
                    let K, g;
                    if (b) {
                        if (
                            ((this.alignOptions = b),
                            (this.alignByTranslate = c),
                            !d || q(d))
                        )
                            (this.alignTo = A = d || "renderer"),
                                w(e, this),
                                e.push(this),
                                (d = void 0);
                    } else
                        (b = this.alignOptions),
                            (c = this.alignByTranslate),
                            (A = this.alignTo);
                    d = f(
                        d,
                        n[A],
                        "scrollablePlotBox" === A ? n.plotBox : void 0,
                        n
                    );
                    A = b.align;
                    const a = b.verticalAlign;
                    n = (d.x || 0) + (b.x || 0);
                    e = (d.y || 0) + (b.y || 0);
                    "right" === A ? (K = 1) : "center" === A && (K = 2);
                    K && (n += (d.width - (b.width || 0)) / K);
                    k[c ? "translateX" : "x"] = Math.round(n);
                    "bottom" === a ? (g = 1) : "middle" === a && (g = 2);
                    g && (e += (d.height - (b.height || 0)) / g);
                    k[c ? "translateY" : "y"] = Math.round(e);
                    this[this.placed ? "animate" : "attr"](k);
                    this.placed = !0;
                    this.alignAttr = k;
                    return this;
                }
                alignSetter(b) {
                    const c = { left: "start", center: "middle", right: "end" };
                    c[b] &&
                        ((this.alignValue = b),
                        this.element.setAttribute("text-anchor", c[b]));
                }
                animate(c, d, k) {
                    const e = z(f(d, this.renderer.globalAnimation, !0));
                    d = e.defer;
                    u.hidden && (e.duration = 0);
                    0 !== e.duration
                        ? (k && (e.complete = k),
                          n(() => {
                              this.element && x(this, c, e);
                          }, d))
                        : (this.attr(c, void 0, k || e.complete),
                          b(
                              c,
                              function (b, c) {
                                  e.step &&
                                      e.step.call(this, b, {
                                          prop: c,
                                          pos: 1,
                                          elem: this,
                                      });
                              },
                              this
                          ));
                    return this;
                }
                applyTextOutline(b) {
                    const c = this.element;
                    -1 !== b.indexOf("contrast") &&
                        (b = b.replace(
                            /contrast/g,
                            this.renderer.getContrast(c.style.fill)
                        ));
                    var f = b.split(" ");
                    b = f[f.length - 1];
                    if ((f = f[0]) && "none" !== f && I.svg) {
                        this.fakeTS = !0;
                        f = f.replace(/(^[\d\.]+)(.*?)$/g, function (b, c, f) {
                            return 2 * Number(c) + f;
                        });
                        this.removeTextOutline();
                        const d = u.createElementNS(l, "tspan");
                        m(d, {
                            class: "highcharts-text-outline",
                            fill: b,
                            stroke: b,
                            "stroke-width": f,
                            "stroke-linejoin": "round",
                        });
                        b = c.querySelector("textPath") || c;
                        [].forEach.call(b.childNodes, (b) => {
                            const c = b.cloneNode(!0);
                            c.removeAttribute &&
                                [
                                    "fill",
                                    "stroke",
                                    "stroke-width",
                                    "stroke",
                                ].forEach((b) => c.removeAttribute(b));
                            d.appendChild(c);
                        });
                        let k = 0;
                        [].forEach.call(
                            b.querySelectorAll("text tspan"),
                            (b) => {
                                k += Number(b.getAttribute("dy"));
                            }
                        );
                        f = u.createElementNS(l, "tspan");
                        f.textContent = "\u200b";
                        m(f, { x: Number(c.getAttribute("x")), dy: -k });
                        d.appendChild(f);
                        b.insertBefore(d, b.firstChild);
                    }
                }
                attr(c, f, d, k) {
                    const n = this.element,
                        e = D.symbolCustomAttribs;
                    let A,
                        q,
                        g = this,
                        a,
                        K;
                    "string" === typeof c &&
                        "undefined" !== typeof f &&
                        ((A = c), (c = {}), (c[A] = f));
                    "string" === typeof c
                        ? (g = (this[c + "Getter"] || this._defaultGetter).call(
                              this,
                              c,
                              n
                          ))
                        : (b(
                              c,
                              function (b, f) {
                                  a = !1;
                                  k || H(this, f);
                                  this.symbolName &&
                                      -1 !== e.indexOf(f) &&
                                      (q || (this.symbolAttr(c), (q = !0)),
                                      (a = !0));
                                  !this.rotation ||
                                      ("x" !== f && "y" !== f) ||
                                      (this.doTransform = !0);
                                  a ||
                                      ((K =
                                          this[f + "Setter"] ||
                                          this._defaultSetter),
                                      K.call(this, b, f, n));
                              },
                              this
                          ),
                          this.afterSetters());
                    d && d.call(this);
                    return g;
                }
                clip(b) {
                    return this.attr(
                        "clip-path",
                        b
                            ? "url(" + this.renderer.url + "#" + b.id + ")"
                            : "none"
                    );
                }
                crisp(b, c) {
                    c = c || b.strokeWidth || 0;
                    const f = (Math.round(c) % 2) / 2;
                    b.x = Math.floor(b.x || this.x || 0) + f;
                    b.y = Math.floor(b.y || this.y || 0) + f;
                    b.width = Math.floor((b.width || this.width || 0) - 2 * f);
                    b.height = Math.floor(
                        (b.height || this.height || 0) - 2 * f
                    );
                    e(b.strokeWidth) && (b.strokeWidth = c);
                    return b;
                }
                complexColor(c, f, k) {
                    const n = this.renderer;
                    let q,
                        g,
                        A,
                        a,
                        r,
                        K,
                        h,
                        J,
                        m,
                        O,
                        w = [],
                        l;
                    F(
                        this.renderer,
                        "complexColor",
                        { args: arguments },
                        function () {
                            c.radialGradient
                                ? (g = "radialGradient")
                                : c.linearGradient && (g = "linearGradient");
                            if (g) {
                                A = c[g];
                                r = n.gradients;
                                K = c.stops;
                                m = k.radialReference;
                                d(A) &&
                                    (c[g] = A =
                                        {
                                            x1: A[0],
                                            y1: A[1],
                                            x2: A[2],
                                            y2: A[3],
                                            gradientUnits: "userSpaceOnUse",
                                        });
                                "radialGradient" === g &&
                                    m &&
                                    !e(A.gradientUnits) &&
                                    ((a = A),
                                    (A = G(A, n.getRadialAttr(m, a), {
                                        gradientUnits: "userSpaceOnUse",
                                    })));
                                b(A, function (b, c) {
                                    "id" !== c && w.push(c, b);
                                });
                                b(K, function (b) {
                                    w.push(b);
                                });
                                w = w.join(",");
                                if (r[w]) O = r[w].attr("id");
                                else {
                                    A.id = O = P();
                                    const b = (r[w] = n
                                        .createElement(g)
                                        .attr(A)
                                        .add(n.defs));
                                    b.radAttr = a;
                                    b.stops = [];
                                    K.forEach(function (c) {
                                        0 === c[1].indexOf("rgba")
                                            ? ((q = y.parse(c[1])),
                                              (h = q.get("rgb")),
                                              (J = q.get("a")))
                                            : ((h = c[1]), (J = 1));
                                        c = n
                                            .createElement("stop")
                                            .attr({
                                                offset: c[0],
                                                "stop-color": h,
                                                "stop-opacity": J,
                                            })
                                            .add(b);
                                        b.stops.push(c);
                                    });
                                }
                                l = "url(" + n.url + "#" + O + ")";
                                k.setAttribute(f, l);
                                k.gradient = w;
                                c.toString = function () {
                                    return l;
                                };
                            }
                        }
                    );
                }
                css(f) {
                    const d = this.styles,
                        k = {},
                        n = this.element;
                    let e,
                        q = !d;
                    d &&
                        b(f, function (b, c) {
                            d && d[c] !== b && ((k[c] = b), (q = !0));
                        });
                    if (q) {
                        d && (f = E(d, k));
                        null === f.width || "auto" === f.width
                            ? delete this.textWidth
                            : "text" === n.nodeName.toLowerCase() &&
                              f.width &&
                              (e = this.textWidth = c(f.width));
                        this.styles = f;
                        e && !v && this.renderer.forExport && delete f.width;
                        const b = G(f);
                        n.namespaceURI === this.SVG_NS &&
                            (["textOutline", "textOverflow", "width"].forEach(
                                (c) => b && delete b[c]
                            ),
                            b.color && (b.fill = b.color));
                        g(n, b);
                    }
                    this.added &&
                        ("text" === this.element.nodeName &&
                            this.renderer.buildText(this),
                        f.textOutline && this.applyTextOutline(f.textOutline));
                    return this;
                }
                dashstyleSetter(b) {
                    let d = this["stroke-width"];
                    "inherit" === d && (d = 1);
                    if ((b = b && b.toLowerCase())) {
                        const k = b
                            .replace("shortdashdotdot", "3,1,1,1,1,1,")
                            .replace("shortdashdot", "3,1,1,1")
                            .replace("shortdot", "1,1,")
                            .replace("shortdash", "3,1,")
                            .replace("longdash", "8,3,")
                            .replace(/dot/g, "1,3,")
                            .replace("dash", "4,3,")
                            .replace(/,$/, "")
                            .split(",");
                        for (b = k.length; b--; )
                            k[b] = "" + c(k[b]) * f(d, NaN);
                        b = k.join(",").replace(/NaN/g, "none");
                        this.element.setAttribute("stroke-dasharray", b);
                    }
                }
                destroy() {
                    const c = this;
                    var f = c.element || {};
                    const d = c.renderer;
                    var k = f.ownerSVGElement;
                    let n = ("SPAN" === f.nodeName && c.parentGroup) || void 0;
                    f.onclick =
                        f.onmouseout =
                        f.onmouseover =
                        f.onmousemove =
                        f.point =
                            null;
                    H(c);
                    if (c.clipPath && k) {
                        const b = c.clipPath;
                        [].forEach.call(
                            k.querySelectorAll("[clip-path],[CLIP-PATH]"),
                            function (c) {
                                -1 <
                                    c
                                        .getAttribute("clip-path")
                                        .indexOf(b.element.id) &&
                                    c.removeAttribute("clip-path");
                            }
                        );
                        c.clipPath = b.destroy();
                    }
                    if (c.stops) {
                        for (k = 0; k < c.stops.length; k++)
                            c.stops[k].destroy();
                        c.stops.length = 0;
                        c.stops = void 0;
                    }
                    for (
                        c.safeRemoveChild(f);
                        n && n.div && 0 === n.div.childNodes.length;

                    )
                        (f = n.parentGroup),
                            c.safeRemoveChild(n.div),
                            delete n.div,
                            (n = f);
                    c.alignTo && w(d.alignedObjects, c);
                    b(c, function (b, f) {
                        c[f] &&
                            c[f].parentGroup === c &&
                            c[f].destroy &&
                            c[f].destroy();
                        delete c[f];
                    });
                }
                dSetter(b, c, f) {
                    d(b) &&
                        ("string" === typeof b[0] &&
                            (b = this.renderer.pathToSegments(b)),
                        (this.pathArray = b),
                        (b = b.reduce(
                            (b, c, f) =>
                                c && c.join
                                    ? (f ? b + " " : "") + c.join(" ")
                                    : (c || "").toString(),
                            ""
                        )));
                    /(NaN| {2}|^$)/.test(b) && (b = "M 0 0");
                    this[c] !== b && (f.setAttribute(c, b), (this[c] = b));
                }
                fadeOut(b) {
                    const c = this;
                    c.animate(
                        { opacity: 0 },
                        {
                            duration: f(b, 150),
                            complete: function () {
                                c.hide();
                            },
                        }
                    );
                }
                fillSetter(b, c, f) {
                    "string" === typeof b
                        ? f.setAttribute(c, b)
                        : b && this.complexColor(b, c, f);
                }
                getBBox(b, c) {
                    const {
                            alignValue: d,
                            element: n,
                            renderer: q,
                            styles: a,
                            textStr: A,
                        } = this,
                        { cache: r, cacheKeys: h } = q;
                    var m = n.namespaceURI === this.SVG_NS;
                    c = f(c, this.rotation, 0);
                    var K = q.styledMode
                        ? n && D.prototype.getStyle.call(n, "font-size")
                        : a && a.fontSize;
                    let J;
                    let N;
                    e(A) &&
                        ((N = A.toString()),
                        -1 === N.indexOf("<") && (N = N.replace(/[0-9]/g, "0")),
                        (N += [
                            "",
                            q.rootFontSize,
                            K,
                            c,
                            this.textWidth,
                            d,
                            a && a.textOverflow,
                            a && a.fontWeight,
                        ].join()));
                    N && !b && (J = r[N]);
                    if (!J) {
                        if (m || q.forExport) {
                            try {
                                var O =
                                    this.fakeTS &&
                                    function (b) {
                                        const c = n.querySelector(
                                            ".highcharts-text-outline"
                                        );
                                        c && g(c, { display: b });
                                    };
                                k(O) && O("none");
                                J = n.getBBox
                                    ? E({}, n.getBBox())
                                    : {
                                          width: n.offsetWidth,
                                          height: n.offsetHeight,
                                          x: 0,
                                          y: 0,
                                      };
                                k(O) && O("");
                            } catch (fa) {
                                ("");
                            }
                            if (!J || 0 > J.width)
                                J = { x: 0, y: 0, width: 0, height: 0 };
                        } else J = this.htmlGetBBox();
                        O = J.width;
                        b = J.height;
                        m &&
                            (J.height = b =
                                { "11px,17": 14, "13px,20": 16 }[
                                    `${K || ""},${Math.round(b)}`
                                ] || b);
                        if (c) {
                            m = Number(n.getAttribute("y") || 0) - J.y;
                            K = { right: 1, center: 0.5 }[d || 0] || 0;
                            var w = c * B,
                                l = (c - 90) * B,
                                p = O * Math.cos(w);
                            c = O * Math.sin(w);
                            var G = Math.cos(l);
                            w = Math.sin(l);
                            O = J.x + K * (O - p) + m * G;
                            l = O + p;
                            G = l - b * G;
                            p = G - p;
                            m = J.y + m - K * c + m * w;
                            K = m + c;
                            b = K - b * w;
                            c = b - c;
                            J.x = Math.min(O, l, G, p);
                            J.y = Math.min(m, K, b, c);
                            J.width = Math.max(O, l, G, p) - J.x;
                            J.height = Math.max(m, K, b, c) - J.y;
                        }
                    }
                    if (N && ("" === A || 0 < J.height)) {
                        for (; 250 < h.length; ) delete r[h.shift()];
                        r[N] || h.push(N);
                        r[N] = J;
                    }
                    return J;
                }
                getStyle(b) {
                    return p
                        .getComputedStyle(this.element || this, "")
                        .getPropertyValue(b);
                }
                hasClass(b) {
                    return (
                        -1 !== ("" + this.attr("class")).split(" ").indexOf(b)
                    );
                }
                hide() {
                    return this.attr({ visibility: "hidden" });
                }
                htmlGetBBox() {
                    return { height: 0, width: 0, x: 0, y: 0 };
                }
                init(b, c) {
                    this.element =
                        "span" === c ? h(c) : u.createElementNS(this.SVG_NS, c);
                    this.renderer = b;
                    F(this, "afterInit");
                }
                on(b, c) {
                    const { onEvents: f } = this;
                    if (f[b]) f[b]();
                    f[b] = t(this.element, b, c);
                    return this;
                }
                opacitySetter(b, c, f) {
                    this.opacity = b = Number(Number(b).toFixed(3));
                    f.setAttribute(c, b);
                }
                removeClass(b) {
                    return this.attr(
                        "class",
                        ("" + this.attr("class"))
                            .replace(
                                q(b) ? new RegExp(`(^| )${b}( |$)`) : b,
                                " "
                            )
                            .replace(/ +/g, " ")
                            .trim()
                    );
                }
                removeTextOutline() {
                    const b = this.element.querySelector(
                        "tspan.highcharts-text-outline"
                    );
                    b && this.safeRemoveChild(b);
                }
                safeRemoveChild(b) {
                    const c = b.parentNode;
                    c && c.removeChild(b);
                }
                setRadialReference(b) {
                    const c =
                        this.element.gradient &&
                        this.renderer.gradients[this.element.gradient];
                    this.element.radialReference = b;
                    c &&
                        c.radAttr &&
                        c.animate(this.renderer.getRadialAttr(b, c.radAttr));
                    return this;
                }
                setTextPath(b, c) {
                    c = G(
                        !0,
                        {
                            enabled: !0,
                            attributes: {
                                dy: -5,
                                startOffset: "50%",
                                textAnchor: "middle",
                            },
                        },
                        c
                    );
                    const f = this.renderer.url,
                        d = this.text || this,
                        k = d.textPath,
                        { attributes: n, enabled: A } = c;
                    b = b || (k && k.path);
                    k && k.undo();
                    b && A
                        ? ((c = t(d, "afterModifyTree", (c) => {
                              if (b && A) {
                                  let A = b.attr("id");
                                  A || b.attr("id", (A = P()));
                                  var k = { x: 0, y: 0 };
                                  e(n.dx) && ((k.dx = n.dx), delete n.dx);
                                  e(n.dy) && ((k.dy = n.dy), delete n.dy);
                                  d.attr(k);
                                  this.attr({ transform: "" });
                                  this.box && (this.box = this.box.destroy());
                                  k = c.nodes.slice(0);
                                  c.nodes.length = 0;
                                  c.nodes[0] = {
                                      tagName: "textPath",
                                      attributes: E(n, {
                                          "text-anchor": n.textAnchor,
                                          href: `${f}#${A}`,
                                      }),
                                      children: k,
                                  };
                              }
                          })),
                          (d.textPath = { path: b, undo: c }))
                        : (d.attr({ dx: 0, dy: 0 }), delete d.textPath);
                    this.added &&
                        ((d.textCache = ""), this.renderer.buildText(d));
                    return this;
                }
                shadow(b) {
                    var c;
                    const { renderer: f } = this,
                        d = G(
                            90 ===
                                (null === (c = this.parentGroup) || void 0 === c
                                    ? void 0
                                    : c.rotation)
                                ? { offsetX: -1, offsetY: -1 }
                                : {},
                            r(b) ? b : {}
                        );
                    c = f.shadowDefinition(d);
                    return this.attr({
                        filter: b ? `url(${f.url}#${c})` : "none",
                    });
                }
                show(b = !0) {
                    return this.attr({ visibility: b ? "inherit" : "visible" });
                }
                ["stroke-widthSetter"](b, c, f) {
                    this[c] = b;
                    f.setAttribute(c, b);
                }
                strokeWidth() {
                    if (!this.renderer.styledMode)
                        return this["stroke-width"] || 0;
                    const b = this.getStyle("stroke-width");
                    let f = 0,
                        d;
                    b.indexOf("px") === b.length - 2
                        ? (f = c(b))
                        : "" !== b &&
                          ((d = u.createElementNS(l, "rect")),
                          m(d, { width: b, "stroke-width": 0 }),
                          this.element.parentNode.appendChild(d),
                          (f = d.getBBox().width),
                          d.parentNode.removeChild(d));
                    return f;
                }
                symbolAttr(b) {
                    const c = this;
                    D.symbolCustomAttribs.forEach(function (d) {
                        c[d] = f(b[d], c[d]);
                    });
                    c.attr({
                        d: c.renderer.symbols[c.symbolName](
                            c.x,
                            c.y,
                            c.width,
                            c.height,
                            c
                        ),
                    });
                }
                textSetter(b) {
                    b !== this.textStr &&
                        (delete this.textPxLength,
                        (this.textStr = b),
                        this.added && this.renderer.buildText(this));
                }
                titleSetter(b) {
                    const c = this.element,
                        d =
                            c.getElementsByTagName("title")[0] ||
                            u.createElementNS(this.SVG_NS, "title");
                    c.insertBefore
                        ? c.insertBefore(d, c.firstChild)
                        : c.appendChild(d);
                    d.textContent = String(f(b, ""))
                        .replace(/<[^>]*>/g, "")
                        .replace(/&lt;/g, "<")
                        .replace(/&gt;/g, ">");
                }
                toFront() {
                    const b = this.element;
                    b.parentNode.appendChild(b);
                    return this;
                }
                translate(b, c) {
                    return this.attr({
                        translateX: b,
                        translateY: c,
                    });
                }
                updateTransform() {
                    const {
                            element: b,
                            matrix: c,
                            rotation: d = 0,
                            scaleX: k,
                            scaleY: n,
                            translateX: q = 0,
                            translateY: A = 0,
                        } = this,
                        g = ["translate(" + q + "," + A + ")"];
                    e(c) && g.push("matrix(" + c.join(",") + ")");
                    d &&
                        g.push(
                            "rotate(" +
                                d +
                                " " +
                                f(
                                    this.rotationOriginX,
                                    b.getAttribute("x"),
                                    0
                                ) +
                                " " +
                                f(
                                    this.rotationOriginY,
                                    b.getAttribute("y") || 0
                                ) +
                                ")"
                        );
                    (e(k) || e(n)) &&
                        g.push("scale(" + f(k, 1) + " " + f(n, 1) + ")");
                    g.length &&
                        !(this.text || this).textPath &&
                        b.setAttribute("transform", g.join(" "));
                }
                visibilitySetter(b, c, f) {
                    "inherit" === b
                        ? f.removeAttribute(c)
                        : this[c] !== b && f.setAttribute(c, b);
                    this[c] = b;
                }
                xGetter(b) {
                    "circle" === this.element.nodeName &&
                        ("x" === b ? (b = "cx") : "y" === b && (b = "cy"));
                    return this._defaultGetter(b);
                }
                zIndexSetter(b, f) {
                    var d = this.renderer,
                        k = this.parentGroup;
                    const n = (k || d).element || d.box,
                        q = this.element;
                    d = n === d.box;
                    let A = !1,
                        g;
                    var a = this.added;
                    let r;
                    e(b)
                        ? (q.setAttribute("data-z-index", b),
                          (b = +b),
                          this[f] === b && (a = !1))
                        : e(this[f]) && q.removeAttribute("data-z-index");
                    this[f] = b;
                    if (a) {
                        (b = this.zIndex) && k && (k.handleZ = !0);
                        f = n.childNodes;
                        for (r = f.length - 1; 0 <= r && !A; r--)
                            if (
                                ((k = f[r]),
                                (a = k.getAttribute("data-z-index")),
                                (g = !e(a)),
                                k !== q)
                            )
                                if (0 > b && g && !d && !r)
                                    n.insertBefore(q, f[r]), (A = !0);
                                else if (c(a) <= b || (g && (!e(b) || 0 <= b)))
                                    n.insertBefore(q, f[r + 1]), (A = !0);
                        A || (n.insertBefore(q, f[d ? 3 : 0]), (A = !0));
                    }
                    return A;
                }
            }
            D.symbolCustomAttribs =
                "anchorX anchorY clockwise end height innerR r start width x y".split(
                    " "
                );
            D.prototype.strokeSetter = D.prototype.fillSetter;
            D.prototype.yGetter = D.prototype.xGetter;
            D.prototype.matrixSetter =
                D.prototype.rotationOriginXSetter =
                D.prototype.rotationOriginYSetter =
                D.prototype.rotationSetter =
                D.prototype.scaleXSetter =
                D.prototype.scaleYSetter =
                D.prototype.translateXSetter =
                D.prototype.translateYSetter =
                D.prototype.verticalAlignSetter =
                    function (b, c) {
                        this[c] = b;
                        this.doTransform = !0;
                    };
            ("");
            return D;
        }
    );
    M(
        a,
        "Core/Renderer/RendererRegistry.js",
        [a["Core/Globals.js"]],
        function (a) {
            var x;
            (function (x) {
                x.rendererTypes = {};
                let y;
                x.getRendererType = function (a = y) {
                    return x.rendererTypes[a] || x.rendererTypes[y];
                };
                x.registerRendererType = function (C, z, H) {
                    x.rendererTypes[C] = z;
                    if (!y || H) (y = C), (a.Renderer = z);
                };
            })(x || (x = {}));
            return x;
        }
    );
    M(
        a,
        "Core/Renderer/SVG/SVGLabel.js",
        [a["Core/Renderer/SVG/SVGElement.js"], a["Core/Utilities.js"]],
        function (a, y) {
            const {
                defined: x,
                extend: L,
                isNumber: C,
                merge: z,
                pick: H,
                removeEvent: B,
            } = y;
            class u extends a {
                constructor(a, l, p, t, m, h, g, e, w, E) {
                    super();
                    this.paddingRightSetter = this.paddingLeftSetter =
                        this.paddingSetter;
                    this.init(a, "g");
                    this.textStr = l;
                    this.x = p;
                    this.y = t;
                    this.anchorX = h;
                    this.anchorY = g;
                    this.baseline = w;
                    this.className = E;
                    this.addClass(
                        "button" === E
                            ? "highcharts-no-tooltip"
                            : "highcharts-label"
                    );
                    E && this.addClass("highcharts-" + E);
                    this.text = a.text(void 0, 0, 0, e).attr({ zIndex: 1 });
                    let v;
                    "string" === typeof m &&
                        ((v = /^url\((.*?)\)$/.test(m)) ||
                            this.renderer.symbols[m]) &&
                        (this.symbolKey = m);
                    this.bBox = u.emptyBBox;
                    this.padding = 3;
                    this.baselineOffset = 0;
                    this.needsBox = a.styledMode || v;
                    this.deferredAttr = {};
                    this.alignFactor = 0;
                }
                alignSetter(a) {
                    a = { left: 0, center: 0.5, right: 1 }[a];
                    a !== this.alignFactor &&
                        ((this.alignFactor = a),
                        this.bBox &&
                            C(this.xSetting) &&
                            this.attr({ x: this.xSetting }));
                }
                anchorXSetter(a, l) {
                    this.anchorX = a;
                    this.boxAttr(
                        l,
                        Math.round(a) - this.getCrispAdjust() - this.xSetting
                    );
                }
                anchorYSetter(a, l) {
                    this.anchorY = a;
                    this.boxAttr(l, a - this.ySetting);
                }
                boxAttr(a, l) {
                    this.box ? this.box.attr(a, l) : (this.deferredAttr[a] = l);
                }
                css(v) {
                    if (v) {
                        const a = {};
                        v = z(v);
                        u.textProps.forEach((l) => {
                            "undefined" !== typeof v[l] &&
                                ((a[l] = v[l]), delete v[l]);
                        });
                        this.text.css(a);
                        "fontSize" in a || "fontWeight" in a
                            ? this.updateTextPadding()
                            : ("width" in a || "textOverflow" in a) &&
                              this.updateBoxSize();
                    }
                    return a.prototype.css.call(this, v);
                }
                destroy() {
                    B(this.element, "mouseenter");
                    B(this.element, "mouseleave");
                    this.text && this.text.destroy();
                    this.box && (this.box = this.box.destroy());
                    a.prototype.destroy.call(this);
                }
                fillSetter(a, l) {
                    a && (this.needsBox = !0);
                    this.fill = a;
                    this.boxAttr(l, a);
                }
                getBBox() {
                    this.textStr &&
                        0 === this.bBox.width &&
                        0 === this.bBox.height &&
                        this.updateBoxSize();
                    const a = this.padding,
                        l = H(this.paddingLeft, a);
                    return {
                        width: this.width,
                        height: this.height,
                        x: this.bBox.x - l,
                        y: this.bBox.y - a,
                    };
                }
                getCrispAdjust() {
                    return this.renderer.styledMode && this.box
                        ? (this.box.strokeWidth() % 2) / 2
                        : ((this["stroke-width"]
                              ? parseInt(this["stroke-width"], 10)
                              : 0) %
                              2) /
                              2;
                }
                heightSetter(a) {
                    this.heightSetting = a;
                }
                onAdd() {
                    this.text.add(this);
                    this.attr({
                        text: H(this.textStr, ""),
                        x: this.x || 0,
                        y: this.y || 0,
                    });
                    this.box &&
                        x(this.anchorX) &&
                        this.attr({
                            anchorX: this.anchorX,
                            anchorY: this.anchorY,
                        });
                }
                paddingSetter(a, l) {
                    C(a)
                        ? a !== this[l] &&
                          ((this[l] = a), this.updateTextPadding())
                        : (this[l] = void 0);
                }
                rSetter(a, l) {
                    this.boxAttr(l, a);
                }
                strokeSetter(a, l) {
                    this.stroke = a;
                    this.boxAttr(l, a);
                }
                ["stroke-widthSetter"](a, l) {
                    a && (this.needsBox = !0);
                    this["stroke-width"] = a;
                    this.boxAttr(l, a);
                }
                ["text-alignSetter"](a) {
                    this.textAlign = a;
                }
                textSetter(a) {
                    "undefined" !== typeof a && this.text.attr({ text: a });
                    this.updateTextPadding();
                }
                updateBoxSize() {
                    var a = this.text;
                    const l = {},
                        p = this.padding,
                        t = (this.bBox =
                            (C(this.widthSetting) &&
                                C(this.heightSetting) &&
                                !this.textAlign) ||
                            !x(a.textStr)
                                ? u.emptyBBox
                                : a.getBBox());
                    this.width = this.getPaddedWidth();
                    this.height = (this.heightSetting || t.height || 0) + 2 * p;
                    const m = this.renderer.fontMetrics(a);
                    this.baselineOffset =
                        p +
                        Math.min(
                            (this.text.firstLineMetrics || m).b,
                            t.height || Infinity
                        );
                    this.heightSetting &&
                        (this.baselineOffset += (this.heightSetting - m.h) / 2);
                    this.needsBox &&
                        !a.textPath &&
                        (this.box ||
                            ((a = this.box =
                                this.symbolKey
                                    ? this.renderer.symbol(this.symbolKey)
                                    : this.renderer.rect()),
                            a.addClass(
                                ("button" === this.className
                                    ? ""
                                    : "highcharts-label-box") +
                                    (this.className
                                        ? " highcharts-" +
                                          this.className +
                                          "-box"
                                        : "")
                            ),
                            a.add(this)),
                        (a = this.getCrispAdjust()),
                        (l.x = a),
                        (l.y = (this.baseline ? -this.baselineOffset : 0) + a),
                        (l.width = Math.round(this.width)),
                        (l.height = Math.round(this.height)),
                        this.box.attr(L(l, this.deferredAttr)),
                        (this.deferredAttr = {}));
                }
                updateTextPadding() {
                    const a = this.text;
                    if (!a.textPath) {
                        this.updateBoxSize();
                        const l = this.baseline ? 0 : this.baselineOffset;
                        let p = H(this.paddingLeft, this.padding);
                        x(this.widthSetting) &&
                            this.bBox &&
                            ("center" === this.textAlign ||
                                "right" === this.textAlign) &&
                            (p +=
                                { center: 0.5, right: 1 }[this.textAlign] *
                                (this.widthSetting - this.bBox.width));
                        if (p !== a.x || l !== a.y)
                            a.attr("x", p),
                                a.hasBoxWidthChanged &&
                                    (this.bBox = a.getBBox(!0)),
                                "undefined" !== typeof l && a.attr("y", l);
                        a.x = p;
                        a.y = l;
                    }
                }
                widthSetter(a) {
                    this.widthSetting = C(a) ? a : void 0;
                }
                getPaddedWidth() {
                    var a = this.padding;
                    const l = H(this.paddingLeft, a);
                    a = H(this.paddingRight, a);
                    return (this.widthSetting || this.bBox.width || 0) + l + a;
                }
                xSetter(a) {
                    this.x = a;
                    this.alignFactor &&
                        ((a -= this.alignFactor * this.getPaddedWidth()),
                        (this["forceAnimate:x"] = !0));
                    this.xSetting = Math.round(a);
                    this.attr("translateX", this.xSetting);
                }
                ySetter(a) {
                    this.ySetting = this.y = Math.round(a);
                    this.attr("translateY", this.ySetting);
                }
            }
            u.emptyBBox = { width: 0, height: 0, x: 0, y: 0 };
            u.textProps =
                "color direction fontFamily fontSize fontStyle fontWeight lineHeight textAlign textDecoration textOutline textOverflow whiteSpace width".split(
                    " "
                );
            return u;
        }
    );
    M(
        a,
        "Core/Renderer/SVG/Symbols.js",
        [a["Core/Utilities.js"]],
        function (a) {
            function x(a, u, v, l, p) {
                const t = [];
                if (p) {
                    const m = p.start || 0,
                        h = H(p.r, v);
                    v = H(p.r, l || v);
                    l = (p.end || 0) - 0.001;
                    const g = p.innerR,
                        e = H(
                            p.open,
                            0.001 > Math.abs((p.end || 0) - m - 2 * Math.PI)
                        ),
                        w = Math.cos(m),
                        E = Math.sin(m),
                        F = Math.cos(l),
                        d = Math.sin(l),
                        k = H(p.longArc, 0.001 > l - m - Math.PI ? 0 : 1);
                    let r = [
                        "A",
                        h,
                        v,
                        0,
                        k,
                        H(p.clockwise, 1),
                        a + h * F,
                        u + v * d,
                    ];
                    r.params = { start: m, end: l, cx: a, cy: u };
                    t.push(["M", a + h * w, u + v * E], r);
                    C(g) &&
                        ((r = [
                            "A",
                            g,
                            g,
                            0,
                            k,
                            C(p.clockwise) ? 1 - p.clockwise : 0,
                            a + g * w,
                            u + g * E,
                        ]),
                        (r.params = { start: l, end: m, cx: a, cy: u }),
                        t.push(
                            e
                                ? ["M", a + g * F, u + g * d]
                                : ["L", a + g * F, u + g * d],
                            r
                        ));
                    e || t.push(["Z"]);
                }
                return t;
            }

            function I(a, u, v, l, p) {
                return p && p.r
                    ? L(a, u, v, l, p)
                    : [
                          ["M", a, u],
                          ["L", a + v, u],
                          ["L", a + v, u + l],
                          ["L", a, u + l],
                          ["Z"],
                      ];
            }

            function L(a, u, v, l, p) {
                p = (null === p || void 0 === p ? void 0 : p.r) || 0;
                return [
                    ["M", a + p, u],
                    ["L", a + v - p, u],
                    ["A", p, p, 0, 0, 1, a + v, u + p],
                    ["L", a + v, u + l - p],
                    ["A", p, p, 0, 0, 1, a + v - p, u + l],
                    ["L", a + p, u + l],
                    ["A", p, p, 0, 0, 1, a, u + l - p],
                    ["L", a, u + p],
                    ["A", p, p, 0, 0, 1, a + p, u],
                    ["Z"],
                ];
            }
            const { defined: C, isNumber: z, pick: H } = a;
            return {
                arc: x,
                callout: function (a, u, v, l, p) {
                    const t = Math.min((p && p.r) || 0, v, l),
                        m = t + 6,
                        h = p && p.anchorX;
                    p = (p && p.anchorY) || 0;
                    const g = L(a, u, v, l, { r: t });
                    if (!z(h)) return g;
                    a + h >= v
                        ? p > u + m && p < u + l - m
                            ? g.splice(
                                  3,
                                  1,
                                  ["L", a + v, p - 6],
                                  ["L", a + v + 6, p],
                                  ["L", a + v, p + 6],
                                  ["L", a + v, u + l - t]
                              )
                            : g.splice(
                                  3,
                                  1,
                                  ["L", a + v, l / 2],
                                  ["L", h, p],
                                  ["L", a + v, l / 2],
                                  ["L", a + v, u + l - t]
                              )
                        : 0 >= a + h
                        ? p > u + m && p < u + l - m
                            ? g.splice(
                                  7,
                                  1,
                                  ["L", a, p + 6],
                                  ["L", a - 6, p],
                                  ["L", a, p - 6],
                                  ["L", a, u + t]
                              )
                            : g.splice(
                                  7,
                                  1,
                                  ["L", a, l / 2],
                                  ["L", h, p],
                                  ["L", a, l / 2],
                                  ["L", a, u + t]
                              )
                        : p && p > l && h > a + m && h < a + v - m
                        ? g.splice(
                              5,
                              1,
                              ["L", h + 6, u + l],
                              ["L", h, u + l + 6],
                              ["L", h - 6, u + l],
                              ["L", a + t, u + l]
                          )
                        : p &&
                          0 > p &&
                          h > a + m &&
                          h < a + v - m &&
                          g.splice(
                              1,
                              1,
                              ["L", h - 6, u],
                              ["L", h, u - 6],
                              ["L", h + 6, u],
                              ["L", v - t, u]
                          );
                    return g;
                },
                circle: function (a, u, v, l) {
                    return x(a + v / 2, u + l / 2, v / 2, l / 2, {
                        start: 0.5 * Math.PI,
                        end: 2.5 * Math.PI,
                        open: !1,
                    });
                },
                diamond: function (a, u, v, l) {
                    return [
                        ["M", a + v / 2, u],
                        ["L", a + v, u + l / 2],
                        ["L", a + v / 2, u + l],
                        ["L", a, u + l / 2],
                        ["Z"],
                    ];
                },
                rect: I,
                roundedRect: L,
                square: I,
                triangle: function (a, u, v, l) {
                    return [
                        ["M", a + v / 2, u],
                        ["L", a + v, u + l],
                        ["L", a, u + l],
                        ["Z"],
                    ];
                },
                "triangle-down": function (a, u, v, l) {
                    return [
                        ["M", a, u],
                        ["L", a + v, u],
                        ["L", a + v / 2, u + l],
                        ["Z"],
                    ];
                },
            };
        }
    );
    M(
        a,
        "Core/Renderer/SVG/TextBuilder.js",
        [
            a["Core/Renderer/HTML/AST.js"],
            a["Core/Globals.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { doc: x, SVG_NS: C, win: z } = y,
                {
                    attr: H,
                    extend: B,
                    fireEvent: u,
                    isString: v,
                    objectEach: l,
                    pick: p,
                } = I;
            class t {
                constructor(a) {
                    const h = a.styles;
                    this.renderer = a.renderer;
                    this.svgElement = a;
                    this.width = a.textWidth;
                    this.textLineHeight = h && h.lineHeight;
                    this.textOutline = h && h.textOutline;
                    this.ellipsis = !(!h || "ellipsis" !== h.textOverflow);
                    this.noWrap = !(!h || "nowrap" !== h.whiteSpace);
                }
                buildSVG() {
                    const m = this.svgElement,
                        h = m.element;
                    var g = m.renderer,
                        e = p(m.textStr, "").toString();
                    const w = -1 !== e.indexOf("<"),
                        l = h.childNodes;
                    g = !m.added && g.box;
                    const t = /<br.*?>/g;
                    var d = [
                        e,
                        this.ellipsis,
                        this.noWrap,
                        this.textLineHeight,
                        this.textOutline,
                        m.getStyle("font-size"),
                        this.width,
                    ].join();
                    if (d !== m.textCache) {
                        m.textCache = d;
                        delete m.actualWidth;
                        for (d = l.length; d--; ) h.removeChild(l[d]);
                        w ||
                        this.ellipsis ||
                        this.width ||
                        m.textPath ||
                        (-1 !== e.indexOf(" ") && (!this.noWrap || t.test(e)))
                            ? "" !== e &&
                              (g && g.appendChild(h),
                              (e = new a(e)),
                              this.modifyTree(e.nodes),
                              e.addToDOM(h),
                              this.modifyDOM(),
                              this.ellipsis &&
                                  -1 !==
                                      (h.textContent || "").indexOf("\u2026") &&
                                  m.attr(
                                      "title",
                                      this.unescapeEntities(m.textStr || "", [
                                          "&lt;",
                                          "&gt;",
                                      ])
                                  ),
                              g && g.removeChild(h))
                            : h.appendChild(
                                  x.createTextNode(this.unescapeEntities(e))
                              );
                        v(this.textOutline) &&
                            m.applyTextOutline &&
                            m.applyTextOutline(this.textOutline);
                    }
                }
                modifyDOM() {
                    const a = this.svgElement,
                        h = H(a.element, "x");
                    a.firstLineMetrics = void 0;
                    let g;
                    for (; (g = a.element.firstChild); )
                        if (/^[\s\u200B]*$/.test(g.textContent || " "))
                            a.element.removeChild(g);
                        else break;
                    [].forEach.call(
                        a.element.querySelectorAll("tspan.highcharts-br"),
                        (e, d) => {
                            e.nextSibling &&
                                e.previousSibling &&
                                (0 === d &&
                                    1 === e.previousSibling.nodeType &&
                                    (a.firstLineMetrics =
                                        a.renderer.fontMetrics(
                                            e.previousSibling
                                        )),
                                H(e, {
                                    dy: this.getLineHeight(e.nextSibling),
                                    x: h,
                                }));
                        }
                    );
                    const e = this.width || 0;
                    if (e) {
                        var w = (g, d) => {
                                var k = g.textContent || "";
                                const r = k
                                    .replace(/([^\^])-/g, "$1- ")
                                    .split(" ");
                                var q =
                                    !this.noWrap &&
                                    (1 < r.length ||
                                        1 < a.element.childNodes.length);
                                const m = this.getLineHeight(d);
                                let b = 0,
                                    f = a.actualWidth;
                                if (this.ellipsis)
                                    k &&
                                        this.truncate(
                                            g,
                                            k,
                                            void 0,
                                            0,
                                            Math.max(0, e - 0.8 * m),
                                            (b, f) =>
                                                b.substring(0, f) + "\u2026"
                                        );
                                else if (q) {
                                    k = [];
                                    for (
                                        q = [];
                                        d.firstChild && d.firstChild !== g;

                                    )
                                        q.push(d.firstChild),
                                            d.removeChild(d.firstChild);
                                    for (; r.length; )
                                        r.length &&
                                            !this.noWrap &&
                                            0 < b &&
                                            (k.push(g.textContent || ""),
                                            (g.textContent = r
                                                .join(" ")
                                                .replace(/- /g, "-"))),
                                            this.truncate(
                                                g,
                                                void 0,
                                                r,
                                                0 === b ? f || 0 : 0,
                                                e,
                                                (b, f) =>
                                                    r
                                                        .slice(0, f)
                                                        .join(" ")
                                                        .replace(/- /g, "-")
                                            ),
                                            (f = a.actualWidth),
                                            b++;
                                    q.forEach((b) => {
                                        d.insertBefore(b, g);
                                    });
                                    k.forEach((b) => {
                                        d.insertBefore(x.createTextNode(b), g);
                                        b = x.createElementNS(C, "tspan");
                                        b.textContent = "\u200b";
                                        H(b, { dy: m, x: h });
                                        d.insertBefore(b, g);
                                    });
                                }
                            },
                            l = (e) => {
                                [].slice.call(e.childNodes).forEach((d) => {
                                    d.nodeType === z.Node.TEXT_NODE
                                        ? w(d, e)
                                        : (-1 !==
                                              d.className.baseVal.indexOf(
                                                  "highcharts-br"
                                              ) && (a.actualWidth = 0),
                                          l(d));
                                });
                            };
                        l(a.element);
                    }
                }
                getLineHeight(a) {
                    a = a.nodeType === z.Node.TEXT_NODE ? a.parentElement : a;
                    return this.textLineHeight
                        ? parseInt(this.textLineHeight.toString(), 10)
                        : this.renderer.fontMetrics(
                              a || this.svgElement.element
                          ).h;
                }
                modifyTree(a) {
                    const h = (g, e) => {
                        const {
                                attributes: m = {},
                                children: l,
                                style: p = {},
                                tagName: d,
                            } = g,
                            k = this.renderer.styledMode;
                        if ("b" === d || "strong" === d)
                            k
                                ? (m["class"] = "highcharts-strong")
                                : (p.fontWeight = "bold");
                        else if ("i" === d || "em" === d)
                            k
                                ? (m["class"] = "highcharts-emphasized")
                                : (p.fontStyle = "italic");
                        p && p.color && (p.fill = p.color);
                        "br" === d
                            ? ((m["class"] = "highcharts-br"),
                              (g.textContent = "\u200b"),
                              (e = a[e + 1]) &&
                                  e.textContent &&
                                  (e.textContent = e.textContent.replace(
                                      /^ +/gm,
                                      ""
                                  )))
                            : "a" === d &&
                              l &&
                              l.some((d) => "#text" === d.tagName) &&
                              (g.children = [
                                  { children: l, tagName: "tspan" },
                              ]);
                        "#text" !== d && "a" !== d && (g.tagName = "tspan");
                        B(g, { attributes: m, style: p });
                        l && l.filter((d) => "#text" !== d.tagName).forEach(h);
                    };
                    a.forEach(h);
                    u(this.svgElement, "afterModifyTree", { nodes: a });
                }
                truncate(a, h, g, e, l, p) {
                    const m = this.svgElement,
                        { rotation: d } = m,
                        k = [];
                    let r = g ? 1 : 0,
                        q = (h || g || "").length,
                        w = q,
                        b,
                        f;
                    const c = function (b, c) {
                        b = c || b;
                        if (
                            (c = a.parentNode) &&
                            "undefined" === typeof k[b] &&
                            c.getSubStringLength
                        )
                            try {
                                k[b] =
                                    e + c.getSubStringLength(0, g ? b + 1 : b);
                            } catch (D) {
                                ("");
                            }
                        return k[b];
                    };
                    m.rotation = 0;
                    f = c(a.textContent.length);
                    if (e + f > l) {
                        for (; r <= q; )
                            (w = Math.ceil((r + q) / 2)),
                                g && (b = p(g, w)),
                                (f = c(w, b && b.length - 1)),
                                r === q
                                    ? (r = q + 1)
                                    : f > l
                                    ? (q = w - 1)
                                    : (r = w);
                        0 === q
                            ? (a.textContent = "")
                            : (h && q === h.length - 1) ||
                              (a.textContent = b || p(h || g, w));
                    }
                    g && g.splice(0, w);
                    m.actualWidth = f;
                    m.rotation = d;
                }
                unescapeEntities(a, h) {
                    l(this.renderer.escapes, function (g, e) {
                        (h && -1 !== h.indexOf(g)) ||
                            (a = a.toString().replace(new RegExp(g, "g"), e));
                    });
                    return a;
                }
            }
            return t;
        }
    );
    M(
        a,
        "Core/Renderer/SVG/SVGRenderer.js",
        [
            a["Core/Renderer/HTML/AST.js"],
            a["Core/Color/Color.js"],
            a["Core/Globals.js"],
            a["Core/Renderer/RendererRegistry.js"],
            a["Core/Renderer/SVG/SVGElement.js"],
            a["Core/Renderer/SVG/SVGLabel.js"],
            a["Core/Renderer/SVG/Symbols.js"],
            a["Core/Renderer/SVG/TextBuilder.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C, z, H, B, u) {
            const {
                    charts: v,
                    deg2rad: l,
                    doc: p,
                    isFirefox: t,
                    isMS: m,
                    isWebKit: h,
                    noop: g,
                    SVG_NS: e,
                    symbolSizes: w,
                    win: E,
                } = I,
                {
                    addEvent: F,
                    attr: d,
                    createElement: k,
                    css: r,
                    defined: q,
                    destroyObjectProperties: G,
                    extend: b,
                    isArray: f,
                    isNumber: c,
                    isObject: n,
                    isString: P,
                    merge: D,
                    pick: K,
                    pInt: x,
                    uniqueKey: T,
                } = u;
            let Z;
            class V {
                constructor(b, c, f, d, a, k, n) {
                    this.width =
                        this.url =
                        this.style =
                        this.imgCount =
                        this.height =
                        this.gradients =
                        this.globalAnimation =
                        this.defs =
                        this.chartIndex =
                        this.cacheKeys =
                        this.cache =
                        this.boxWrapper =
                        this.box =
                        this.alignedObjects =
                            void 0;
                    this.init(b, c, f, d, a, k, n);
                }
                init(b, c, f, a, k, n, J) {
                    const A = this.createElement("svg").attr({
                            version: "1.1",
                            class: "highcharts-root",
                        }),
                        e = A.element;
                    J || A.css(this.getStyle(a));
                    b.appendChild(e);
                    d(b, "dir", "ltr");
                    -1 === b.innerHTML.indexOf("xmlns") &&
                        d(e, "xmlns", this.SVG_NS);
                    this.box = e;
                    this.boxWrapper = A;
                    this.alignedObjects = [];
                    this.url = this.getReferenceURL();
                    this.createElement("desc")
                        .add()
                        .element.appendChild(
                            p.createTextNode("Created with Highcharts 11.1.0")
                        );
                    this.defs = this.createElement("defs").add();
                    this.allowHTML = n;
                    this.forExport = k;
                    this.styledMode = J;
                    this.gradients = {};
                    this.cache = {};
                    this.cacheKeys = [];
                    this.imgCount = 0;
                    this.rootFontSize = A.getStyle("font-size");
                    this.setSize(c, f, !1);
                    let q;
                    t &&
                        b.getBoundingClientRect &&
                        ((c = function () {
                            r(b, { left: 0, top: 0 });
                            q = b.getBoundingClientRect();
                            r(b, {
                                left: Math.ceil(q.left) - q.left + "px",
                                top: Math.ceil(q.top) - q.top + "px",
                            });
                        }),
                        c(),
                        (this.unSubPixelFix = F(E, "resize", c)));
                }
                definition(b) {
                    return new a([b]).addToDOM(this.defs.element);
                }
                getReferenceURL() {
                    if ((t || h) && p.getElementsByTagName("base").length) {
                        if (!q(Z)) {
                            var b = T();
                            b = new a([
                                {
                                    tagName: "svg",
                                    attributes: { width: 8, height: 8 },
                                    children: [
                                        {
                                            tagName: "defs",
                                            children: [
                                                {
                                                    tagName: "clipPath",
                                                    attributes: { id: b },
                                                    children: [
                                                        {
                                                            tagName: "rect",
                                                            attributes: {
                                                                width: 4,
                                                                height: 4,
                                                            },
                                                        },
                                                    ],
                                                },
                                            ],
                                        },
                                        {
                                            tagName: "rect",
                                            attributes: {
                                                id: "hitme",
                                                width: 8,
                                                height: 8,
                                                "clip-path": `url(#${b})`,
                                                fill: "rgba(0,0,0,0.001)",
                                            },
                                        },
                                    ],
                                },
                            ]).addToDOM(p.body);
                            r(b, {
                                position: "fixed",
                                top: 0,
                                left: 0,
                                zIndex: 9e5,
                            });
                            const c = p.elementFromPoint(6, 6);
                            Z = "hitme" === (c && c.id);
                            p.body.removeChild(b);
                        }
                        if (Z)
                            return E.location.href
                                .split("#")[0]
                                .replace(/<[^>]*>/g, "")
                                .replace(/([\('\)])/g, "\\$1")
                                .replace(/ /g, "%20");
                    }
                    return "";
                }
                getStyle(c) {
                    return (this.style = b(
                        {
                            fontFamily: "Helvetica, Arial, sans-serif",
                            fontSize: "1rem",
                        },
                        c
                    ));
                }
                setStyle(b) {
                    this.boxWrapper.css(this.getStyle(b));
                }
                isHidden() {
                    return !this.boxWrapper.getBBox().width;
                }
                destroy() {
                    const b = this.defs;
                    this.box = null;
                    this.boxWrapper = this.boxWrapper.destroy();
                    G(this.gradients || {});
                    this.gradients = null;
                    this.defs = b.destroy();
                    this.unSubPixelFix && this.unSubPixelFix();
                    return (this.alignedObjects = null);
                }
                createElement(b) {
                    const c = new this.Element();
                    c.init(this, b);
                    return c;
                }
                getRadialAttr(b, c) {
                    return {
                        cx: b[0] - b[2] / 2 + (c.cx || 0) * b[2],
                        cy: b[1] - b[2] / 2 + (c.cy || 0) * b[2],
                        r: (c.r || 0) * b[2],
                    };
                }
                shadowDefinition(b) {
                    const c = [
                            `highcharts-drop-shadow-${this.chartIndex}`,
                            ...Object.keys(b).map((c) => b[c]),
                        ]
                            .join("-")
                            .replace(/[^a-z0-9\-]/g, ""),
                        f = D(
                            {
                                color: "#000000",
                                offsetX: 1,
                                offsetY: 1,
                                opacity: 0.15,
                                width: 5,
                            },
                            b
                        );
                    this.defs.element.querySelector(`#${c}`) ||
                        this.definition({
                            tagName: "filter",
                            attributes: { id: c },
                            children: [
                                {
                                    tagName: "feDropShadow",
                                    attributes: {
                                        dx: f.offsetX,
                                        dy: f.offsetY,
                                        "flood-color": f.color,
                                        "flood-opacity": Math.min(
                                            5 * f.opacity,
                                            1
                                        ),
                                        stdDeviation: f.width / 2,
                                    },
                                },
                            ],
                        });
                    return c;
                }
                buildText(b) {
                    new B(b).buildSVG();
                }
                getContrast(b) {
                    b = y.parse(b).rgba.map((b) => {
                        b /= 255;
                        return 0.03928 >= b
                            ? b / 12.92
                            : Math.pow((b + 0.055) / 1.055, 2.4);
                    });
                    b = 0.2126 * b[0] + 0.7152 * b[1] + 0.0722 * b[2];
                    return 1.05 / (b + 0.05) > (b + 0.05) / 0.05
                        ? "#FFFFFF"
                        : "#000000";
                }
                button(c, f, d, k, e = {}, q, J, g, r, h) {
                    const A = this.label(
                            c,
                            f,
                            d,
                            r,
                            void 0,
                            void 0,
                            h,
                            void 0,
                            "button"
                        ),
                        O = this.styledMode;
                    c = e.states || {};
                    let N = 0;
                    e = D(e);
                    delete e.states;
                    const l = D(
                        {
                            color: "#333333",
                            cursor: "pointer",
                            fontSize: "0.8em",
                            fontWeight: "normal",
                        },
                        e.style
                    );
                    delete e.style;
                    let w = a.filterUserAttributes(e);
                    A.attr(D({ padding: 8, r: 2 }, w));
                    let p, G, R;
                    O ||
                        ((w = D(
                            {
                                fill: "#f7f7f7",
                                stroke: "#cccccc",
                                "stroke-width": 1,
                            },
                            w
                        )),
                        (q = D(
                            w,
                            { fill: "#e6e6e6" },
                            a.filterUserAttributes(q || c.hover || {})
                        )),
                        (p = q.style),
                        delete q.style,
                        (J = D(
                            w,
                            {
                                fill: "#e6e9ff",
                                style: {
                                    color: "#000000",
                                    fontWeight: "bold",
                                },
                            },
                            a.filterUserAttributes(J || c.select || {})
                        )),
                        (G = J.style),
                        delete J.style,
                        (g = D(
                            w,
                            { style: { color: "#cccccc" } },
                            a.filterUserAttributes(g || c.disabled || {})
                        )),
                        (R = g.style),
                        delete g.style);
                    F(A.element, m ? "mouseover" : "mouseenter", function () {
                        3 !== N && A.setState(1);
                    });
                    F(A.element, m ? "mouseout" : "mouseleave", function () {
                        3 !== N && A.setState(N);
                    });
                    A.setState = function (b) {
                        1 !== b && (A.state = N = b);
                        A.removeClass(
                            /highcharts-button-(normal|hover|pressed|disabled)/
                        ).addClass(
                            "highcharts-button-" +
                                ["normal", "hover", "pressed", "disabled"][
                                    b || 0
                                ]
                        );
                        O ||
                            (A.attr([w, q, J, g][b || 0]),
                            (b = [l, p, G, R][b || 0]),
                            n(b) && A.css(b));
                    };
                    O ||
                        (A.attr(w).css(b({ cursor: "default" }, l)),
                        h && A.text.css({ pointerEvents: "none" }));
                    return A.on("touchstart", (b) => b.stopPropagation()).on(
                        "click",
                        function (b) {
                            3 !== N && k.call(A, b);
                        }
                    );
                }
                crispLine(b, c, f = "round") {
                    const d = b[0],
                        a = b[1];
                    q(d[1]) &&
                        d[1] === a[1] &&
                        (d[1] = a[1] = Math[f](d[1]) - (c % 2) / 2);
                    q(d[2]) &&
                        d[2] === a[2] &&
                        (d[2] = a[2] = Math[f](d[2]) + (c % 2) / 2);
                    return b;
                }
                path(c) {
                    const d = this.styledMode ? {} : { fill: "none" };
                    f(c) ? (d.d = c) : n(c) && b(d, c);
                    return this.createElement("path").attr(d);
                }
                circle(b, c, f) {
                    b = n(b)
                        ? b
                        : "undefined" === typeof b
                        ? {}
                        : { x: b, y: c, r: f };
                    c = this.createElement("circle");
                    c.xSetter = c.ySetter = function (b, c, f) {
                        f.setAttribute("c" + c, b);
                    };
                    return c.attr(b);
                }
                arc(b, c, f, d, a, k) {
                    n(b)
                        ? ((d = b), (c = d.y), (f = d.r), (b = d.x))
                        : (d = { innerR: d, start: a, end: k });
                    b = this.symbol("arc", b, c, f, f, d);
                    b.r = f;
                    return b;
                }
                rect(c, f, a, k, e, q) {
                    c = n(c)
                        ? c
                        : "undefined" === typeof c
                        ? {}
                        : {
                              x: c,
                              y: f,
                              r: e,
                              width: Math.max(a || 0, 0),
                              height: Math.max(k || 0, 0),
                          };
                    const A = this.createElement("rect");
                    this.styledMode ||
                        ("undefined" !== typeof q &&
                            ((c["stroke-width"] = q), b(c, A.crisp(c))),
                        (c.fill = "none"));
                    A.rSetter = function (b, c, f) {
                        A.r = b;
                        d(f, { rx: b, ry: b });
                    };
                    A.rGetter = function () {
                        return A.r || 0;
                    };
                    return A.attr(c);
                }
                roundedRect(b) {
                    return this.symbol("roundedRect").attr(b);
                }
                setSize(b, c, f) {
                    this.width = b;
                    this.height = c;
                    this.boxWrapper.animate(
                        { width: b, height: c },
                        {
                            step: function () {
                                this.attr({
                                    viewBox:
                                        "0 0 " +
                                        this.attr("width") +
                                        " " +
                                        this.attr("height"),
                                });
                            },
                            duration: K(f, !0) ? void 0 : 0,
                        }
                    );
                    this.alignElements();
                }
                g(b) {
                    const c = this.createElement("g");
                    return b ? c.attr({ class: "highcharts-" + b }) : c;
                }
                image(b, f, d, a, k, n) {
                    const A = { preserveAspectRatio: "none" };
                    c(f) && (A.x = f);
                    c(d) && (A.y = d);
                    c(a) && (A.width = a);
                    c(k) && (A.height = k);
                    const e = this.createElement("image").attr(A);
                    f = function (c) {
                        e.attr({ href: b });
                        n.call(e, c);
                    };
                    n
                        ? (e.attr({
                              href: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==",
                          }),
                          (d = new E.Image()),
                          F(d, "load", f),
                          (d.src = b),
                          d.complete && f({}))
                        : e.attr({ href: b });
                    return e;
                }
                symbol(c, f, a, n, e, g) {
                    const A = this,
                        h = /^url\((.*?)\)$/,
                        O = h.test(c),
                        m = !O && (this.symbols[c] ? c : "circle"),
                        l = m && this.symbols[m];
                    let D, G, P, t;
                    if (l)
                        "number" === typeof f &&
                            (G = l.call(
                                this.symbols,
                                Math.round(f || 0),
                                Math.round(a || 0),
                                n || 0,
                                e || 0,
                                g
                            )),
                            (D = this.path(G)),
                            A.styledMode || D.attr("fill", "none"),
                            b(D, {
                                symbolName: m || void 0,
                                x: f,
                                y: a,
                                width: n,
                                height: e,
                            }),
                            g && b(D, g);
                    else if (O) {
                        P = c.match(h)[1];
                        const b = (D = this.image(P));
                        b.imgwidth = K(g && g.width, w[P] && w[P].width);
                        b.imgheight = K(g && g.height, w[P] && w[P].height);
                        t = (b) => b.attr({ width: b.width, height: b.height });
                        ["width", "height"].forEach(function (c) {
                            b[c + "Setter"] = function (b, c) {
                                this[c] = b;
                                const {
                                    alignByTranslate: f,
                                    element: a,
                                    width: k,
                                    height: A,
                                    imgwidth: n,
                                    imgheight: e,
                                } = this;
                                b = this["img" + c];
                                if (q(b)) {
                                    let J = 1;
                                    g && "within" === g.backgroundSize && k && A
                                        ? ((J = Math.min(k / n, A / e)),
                                          d(a, {
                                              width: Math.round(n * J),
                                              height: Math.round(e * J),
                                          }))
                                        : a && a.setAttribute(c, b);
                                    f ||
                                        this.translate(
                                            ((k || 0) - n * J) / 2,
                                            ((A || 0) - e * J) / 2
                                        );
                                }
                            };
                        });
                        q(f) && b.attr({ x: f, y: a });
                        b.isImg = !0;
                        q(b.imgwidth) && q(b.imgheight)
                            ? t(b)
                            : (b.attr({ width: 0, height: 0 }),
                              k("img", {
                                  onload: function () {
                                      const c = v[A.chartIndex];
                                      0 === this.width &&
                                          (r(this, {
                                              position: "absolute",
                                              top: "-999em",
                                          }),
                                          p.body.appendChild(this));
                                      w[P] = {
                                          width: this.width,
                                          height: this.height,
                                      };
                                      b.imgwidth = this.width;
                                      b.imgheight = this.height;
                                      b.element && t(b);
                                      this.parentNode &&
                                          this.parentNode.removeChild(this);
                                      A.imgCount--;
                                      if (!A.imgCount && c && !c.hasLoaded)
                                          c.onload();
                                  },
                                  src: P,
                              }),
                              this.imgCount++);
                    }
                    return D;
                }
                clipRect(b, c, f, d) {
                    const a = T() + "-",
                        k = this.createElement("clipPath")
                            .attr({ id: a })
                            .add(this.defs);
                    b = this.rect(b, c, f, d, 0).add(k);
                    b.id = a;
                    b.clipPath = k;
                    b.count = 0;
                    return b;
                }
                text(b, c, f, d) {
                    const a = {};
                    if (d && (this.allowHTML || !this.forExport))
                        return this.html(b, c, f);
                    a.x = Math.round(c || 0);
                    f && (a.y = Math.round(f));
                    q(b) && (a.text = b);
                    b = this.createElement("text").attr(a);
                    if (!d || (this.forExport && !this.allowHTML))
                        b.xSetter = function (b, c, f) {
                            const d = f.getElementsByTagName("tspan"),
                                a = f.getAttribute(c);
                            for (let f = 0, k; f < d.length; f++)
                                (k = d[f]),
                                    k.getAttribute(c) === a &&
                                        k.setAttribute(c, b);
                            f.setAttribute(c, b);
                        };
                    return b;
                }
                fontMetrics(b) {
                    b = x(C.prototype.getStyle.call(b, "font-size") || 0);
                    const c = 24 > b ? b + 3 : Math.round(1.2 * b);
                    return { h: c, b: Math.round(0.8 * c), f: b };
                }
                rotCorr(b, c, f) {
                    let d = b;
                    c && f && (d = Math.max(d * Math.cos(c * l), 4));
                    return { x: (-b / 3) * Math.sin(c * l), y: d };
                }
                pathToSegments(b) {
                    const f = [],
                        d = [],
                        a = {
                            A: 8,
                            C: 7,
                            H: 2,
                            L: 3,
                            M: 3,
                            Q: 5,
                            S: 5,
                            T: 3,
                            V: 2,
                        };
                    for (let k = 0; k < b.length; k++)
                        P(d[0]) &&
                            c(b[k]) &&
                            d.length === a[d[0].toUpperCase()] &&
                            b.splice(
                                k,
                                0,
                                d[0].replace("M", "L").replace("m", "l")
                            ),
                            "string" === typeof b[k] &&
                                (d.length && f.push(d.slice(0)),
                                (d.length = 0)),
                            d.push(b[k]);
                    f.push(d.slice(0));
                    return f;
                }
                label(b, c, f, d, a, k, n, e, q) {
                    return new z(this, b, c, f, d, a, k, n, e, q);
                }
                alignElements() {
                    this.alignedObjects.forEach((b) => b.align());
                }
            }
            b(V.prototype, {
                Element: C,
                SVG_NS: e,
                escapes: {
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    "'": "&#39;",
                    '"': "&quot;",
                },
                symbols: H,
                draw: g,
            });
            L.registerRendererType("svg", V, !0);
            ("");
            return V;
        }
    );
    M(
        a,
        "Core/Renderer/HTML/HTMLElement.js",
        [
            a["Core/Globals.js"],
            a["Core/Renderer/SVG/SVGElement.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { isFirefox: x, isMS: C, isWebKit: z, win: H } = a,
                { css: B, defined: u, extend: v, pick: l, pInt: p } = I,
                t = [];
            class m extends y {
                static compose(a) {
                    if (I.pushUnique(t, a)) {
                        const g = m.prototype,
                            e = a.prototype;
                        e.getSpanCorrection = g.getSpanCorrection;
                        e.htmlCss = g.htmlCss;
                        e.htmlGetBBox = g.htmlGetBBox;
                        e.htmlUpdateTransform = g.htmlUpdateTransform;
                        e.setSpanRotation = g.setSpanRotation;
                    }
                    return a;
                }
                getSpanCorrection(a, g, e) {
                    this.xCorr = -a * e;
                    this.yCorr = -g;
                }
                htmlCss(a) {
                    const g =
                            "SPAN" === this.element.tagName &&
                            a &&
                            "width" in a,
                        e = l(g && a.width, void 0);
                    let h;
                    g && (delete a.width, (this.textWidth = e), (h = !0));
                    a &&
                        "ellipsis" === a.textOverflow &&
                        ((a.whiteSpace = "nowrap"), (a.overflow = "hidden"));
                    this.styles = v(this.styles, a);
                    B(this.element, a);
                    h && this.htmlUpdateTransform();
                    return this;
                }
                htmlGetBBox() {
                    const a = this.element;
                    return {
                        x: a.offsetLeft,
                        y: a.offsetTop,
                        width: a.offsetWidth,
                        height: a.offsetHeight,
                    };
                }
                htmlUpdateTransform() {
                    if (this.added) {
                        var a = this.renderer,
                            g = this.element,
                            e = this.x || 0,
                            m = this.y || 0,
                            l = this.textAlign || "left",
                            t = { left: 0, center: 0.5, right: 1 }[l],
                            d = this.styles,
                            k = d && d.whiteSpace;
                        B(g, {
                            marginLeft: this.translateX || 0,
                            marginTop: this.translateY || 0,
                        });
                        if ("SPAN" === g.tagName) {
                            d = this.rotation;
                            const q = this.textWidth && p(this.textWidth),
                                h = [
                                    d,
                                    l,
                                    g.innerHTML,
                                    this.textWidth,
                                    this.textAlign,
                                ].join();
                            let b = !1;
                            if (q !== this.oldTextWidth) {
                                if (this.textPxLength)
                                    var r = this.textPxLength;
                                else
                                    B(g, {
                                        width: "",
                                        whiteSpace: k || "nowrap",
                                    }),
                                        (r = g.offsetWidth);
                                (q > this.oldTextWidth || r > q) &&
                                    (/[ \-]/.test(
                                        g.textContent || g.innerText
                                    ) ||
                                        "ellipsis" === g.style.textOverflow) &&
                                    (B(g, {
                                        width: r > q || d ? q + "px" : "auto",
                                        display: "block",
                                        whiteSpace: k || "normal",
                                    }),
                                    (this.oldTextWidth = q),
                                    (b = !0));
                            }
                            this.hasBoxWidthChanged = b;
                            h !== this.cTT &&
                                ((a = a.fontMetrics(g).b),
                                !u(d) ||
                                    (d === (this.oldRotation || 0) &&
                                        l === this.oldAlign) ||
                                    this.setSpanRotation(d, t, a),
                                this.getSpanCorrection(
                                    (!u(d) && this.textPxLength) ||
                                        g.offsetWidth,
                                    a,
                                    t,
                                    d,
                                    l
                                ));
                            B(g, {
                                left: e + (this.xCorr || 0) + "px",
                                top: m + (this.yCorr || 0) + "px",
                            });
                            this.cTT = h;
                            this.oldRotation = d;
                            this.oldAlign = l;
                        }
                    } else this.alignOnAdd = !0;
                }
                setSpanRotation(a, g, e) {
                    const h = {},
                        m =
                            C && !/Edge/.test(H.navigator.userAgent)
                                ? "-ms-transform"
                                : z
                                ? "-webkit-transform"
                                : x
                                ? "MozTransform"
                                : H.opera
                                ? "-o-transform"
                                : void 0;
                    m &&
                        ((h[m] = h.transform = "rotate(" + a + "deg)"),
                        (h[m + (x ? "Origin" : "-origin")] = h.transformOrigin =
                            100 * g + "% " + e + "px"),
                        B(this.element, h));
                }
            }
            return m;
        }
    );
    M(
        a,
        "Core/Renderer/HTML/HTMLRenderer.js",
        [
            a["Core/Renderer/HTML/AST.js"],
            a["Core/Renderer/SVG/SVGElement.js"],
            a["Core/Renderer/SVG/SVGRenderer.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L) {
            const { attr: x, createElement: z, extend: H, pick: B } = L,
                u = [];
            class v extends I {
                static compose(a) {
                    L.pushUnique(u, a) && (a.prototype.html = v.prototype.html);
                    return a;
                }
                html(l, p, t) {
                    const m = this.createElement("span"),
                        h = m.element,
                        g = m.renderer,
                        e = function (a, e) {
                            ["opacity", "visibility"].forEach(function (g) {
                                a[g + "Setter"] = function (d, k, r) {
                                    const q = a.div ? a.div.style : e;
                                    y.prototype[g + "Setter"].call(
                                        this,
                                        d,
                                        k,
                                        r
                                    );
                                    q && (q[k] = d);
                                };
                            });
                            a.addedSetters = !0;
                        };
                    m.textSetter = function (e) {
                        e !== this.textStr &&
                            (delete this.bBox,
                            delete this.oldTextWidth,
                            a.setElementHTML(this.element, B(e, "")),
                            (this.textStr = e),
                            (m.doTransform = !0));
                    };
                    e(m, m.element.style);
                    m.xSetter =
                        m.ySetter =
                        m.alignSetter =
                        m.rotationSetter =
                            function (a, e) {
                                "align" === e
                                    ? (m.alignValue = m.textAlign = a)
                                    : (m[e] = a);
                                m.doTransform = !0;
                            };
                    m.afterSetters = function () {
                        this.doTransform &&
                            (this.htmlUpdateTransform(),
                            (this.doTransform = !1));
                    };
                    m.attr({ text: l, x: Math.round(p), y: Math.round(t) }).css(
                        { position: "absolute" }
                    );
                    g.styledMode ||
                        m.css({
                            fontFamily: this.style.fontFamily,
                            fontSize: this.style.fontSize,
                        });
                    h.style.whiteSpace = "nowrap";
                    m.css = m.htmlCss;
                    m.add = function (a) {
                        const l = g.box.parentNode,
                            w = [];
                        let d;
                        if ((this.parentGroup = a)) {
                            if (((d = a.div), !d)) {
                                for (; a; ) w.push(a), (a = a.parentGroup);
                                w.reverse().forEach(function (a) {
                                    function k(f, c) {
                                        a[c] = f;
                                        "translateX" === c
                                            ? (b.left = f + "px")
                                            : (b.top = f + "px");
                                        a.doTransform = !0;
                                    }
                                    const q = x(a.element, "class"),
                                        g = a.styles || {};
                                    d = a.div =
                                        a.div ||
                                        z(
                                            "div",
                                            q ? { className: q } : void 0,
                                            {
                                                position: "absolute",
                                                left:
                                                    (a.translateX || 0) + "px",
                                                top: (a.translateY || 0) + "px",
                                                display: a.display,
                                                opacity: a.opacity,
                                                visibility: a.visibility,
                                            },
                                            d || l
                                        );
                                    const b = d.style;
                                    H(a, {
                                        classSetter: (function (b) {
                                            return function (c) {
                                                this.element.setAttribute(
                                                    "class",
                                                    c
                                                );
                                                b.className = c;
                                            };
                                        })(d),
                                        css: function (f) {
                                            m.css.call(a, f);
                                            ["cursor", "pointerEvents"].forEach(
                                                (c) => {
                                                    f[c] && (b[c] = f[c]);
                                                }
                                            );
                                            return a;
                                        },
                                        on: function () {
                                            w[0].div &&
                                                m.on.apply(
                                                    {
                                                        element: w[0].div,
                                                        onEvents: a.onEvents,
                                                    },
                                                    arguments
                                                );
                                            return a;
                                        },
                                        translateXSetter: k,
                                        translateYSetter: k,
                                    });
                                    a.addedSetters || e(a);
                                    a.css(g);
                                });
                            }
                        } else d = l;
                        d.appendChild(h);
                        m.added = !0;
                        m.alignOnAdd && m.htmlUpdateTransform();
                        return m;
                    };
                    return m;
                }
            }
            return v;
        }
    );
    M(a, "Core/Axis/AxisDefaults.js", [], function () {
        var a;
        (function (a) {
            a.defaultXAxisOptions = {
                alignTicks: !0,
                allowDecimals: void 0,
                panningEnabled: !0,
                zIndex: 2,
                zoomEnabled: !0,
                dateTimeLabelFormats: {
                    millisecond: { main: "%H:%M:%S.%L", range: !1 },
                    second: { main: "%H:%M:%S", range: !1 },
                    minute: { main: "%H:%M", range: !1 },
                    hour: { main: "%H:%M", range: !1 },
                    day: { main: "%e %b" },
                    week: { main: "%e %b" },
                    month: { main: "%b '%y" },
                    year: { main: "%Y" },
                },
                endOnTick: !1,
                gridLineDashStyle: "Solid",
                gridZIndex: 1,
                labels: {
                    autoRotation: void 0,
                    autoRotationLimit: 80,
                    distance: 15,
                    enabled: !0,
                    indentation: 10,
                    overflow: "justify",
                    padding: 5,
                    reserveSpace: void 0,
                    rotation: void 0,
                    staggerLines: 0,
                    step: 0,
                    useHTML: !1,
                    zIndex: 7,
                    style: {
                        color: "#333333",
                        cursor: "default",
                        fontSize: "0.8em",
                    },
                },
                maxPadding: 0.01,
                minorGridLineDashStyle: "Solid",
                minorTickLength: 2,
                minorTickPosition: "outside",
                minorTicksPerMajor: 5,
                minPadding: 0.01,
                offset: void 0,
                opposite: !1,
                reversed: void 0,
                reversedStacks: !1,
                showEmpty: !0,
                showFirstLabel: !0,
                showLastLabel: !0,
                startOfWeek: 1,
                startOnTick: !1,
                tickLength: 10,
                tickPixelInterval: 100,
                tickmarkPlacement: "between",
                tickPosition: "outside",
                title: {
                    align: "middle",
                    rotation: 0,
                    useHTML: !1,
                    x: 0,
                    y: 0,
                    style: { color: "#666666", fontSize: "0.8em" },
                },
                type: "linear",
                uniqueNames: !0,
                visible: !0,
                minorGridLineColor: "#f2f2f2",
                minorGridLineWidth: 1,
                minorTickColor: "#999999",
                lineColor: "#333333",
                lineWidth: 1,
                gridLineColor: "#e6e6e6",
                gridLineWidth: void 0,
                tickColor: "#333333",
            };
            a.defaultYAxisOptions = {
                reversedStacks: !0,
                endOnTick: !0,
                maxPadding: 0.05,
                minPadding: 0.05,
                tickPixelInterval: 72,
                showLastLabel: !0,
                labels: { x: void 0 },
                startOnTick: !0,
                title: { rotation: 270, text: "Values" },
                stackLabels: {
                    animation: {},
                    allowOverlap: !1,
                    enabled: !1,
                    crop: !0,
                    overflow: "justify",
                    formatter: function () {
                        const { numberFormatter: a } = this.axis.chart;
                        return a(this.total || 0, -1);
                    },
                    style: {
                        color: "#000000",
                        fontSize: "0.7em",
                        fontWeight: "bold",
                        textOutline: "1px contrast",
                    },
                },
                gridLineWidth: 1,
                lineWidth: 0,
            };
            a.defaultLeftAxisOptions = { title: { rotation: 270 } };
            a.defaultRightAxisOptions = { title: { rotation: 90 } };
            a.defaultBottomAxisOptions = {
                labels: { autoRotation: [-45] },
                margin: 15,
                title: { rotation: 0 },
            };
            a.defaultTopAxisOptions = {
                labels: { autoRotation: [-45] },
                margin: 15,
                title: { rotation: 0 },
            };
        })(a || (a = {}));
        return a;
    });
    M(a, "Core/Foundation.js", [a["Core/Utilities.js"]], function (a) {
        const { addEvent: x, isFunction: I, objectEach: L, removeEvent: C } = a;
        var z;
        (function (a) {
            a.registerEventOptions = function (a, u) {
                a.eventOptions = a.eventOptions || {};
                L(u.events, function (v, l) {
                    a.eventOptions[l] !== v &&
                        (a.eventOptions[l] &&
                            (C(a, l, a.eventOptions[l]),
                            delete a.eventOptions[l]),
                        I(v) &&
                            ((a.eventOptions[l] = v),
                            x(a, l, v, { order: 0 })));
                });
            };
        })(z || (z = {}));
        return z;
    });
    M(
        a,
        "Core/Axis/Tick.js",
        [a["Core/Templating.js"], a["Core/Globals.js"], a["Core/Utilities.js"]],
        function (a, y, I) {
            const { deg2rad: x } = y,
                {
                    clamp: C,
                    correctFloat: z,
                    defined: H,
                    destroyObjectProperties: B,
                    extend: u,
                    fireEvent: v,
                    isNumber: l,
                    merge: p,
                    objectEach: t,
                    pick: m,
                } = I;
            class h {
                constructor(a, e, h, m, l) {
                    this.isNewLabel = this.isNew = !0;
                    this.axis = a;
                    this.pos = e;
                    this.type = h || "";
                    this.parameters = l || {};
                    this.tickmarkOffset = this.parameters.tickmarkOffset;
                    this.options = this.parameters.options;
                    v(this, "init");
                    h || m || this.addLabel();
                }
                addLabel() {
                    const g = this,
                        e = g.axis;
                    var h = e.options;
                    const p = e.chart;
                    var t = e.categories;
                    const d = e.logarithmic,
                        k = e.names,
                        r = g.pos,
                        q = m(g.options && g.options.labels, h.labels);
                    var G = e.tickPositions;
                    const b = r === G[0],
                        f = r === G[G.length - 1],
                        c = (!q.step || 1 === q.step) && 1 === e.tickInterval;
                    G = G.info;
                    let n = g.label,
                        P,
                        D,
                        K;
                    t = this.parameters.category || (t ? m(t[r], k[r], r) : r);
                    d && l(t) && (t = z(d.lin2log(t)));
                    e.dateTime &&
                        (G
                            ? ((D = p.time.resolveDTLFormat(
                                  h.dateTimeLabelFormats[
                                      (!h.grid && G.higherRanks[r]) ||
                                          G.unitName
                                  ]
                              )),
                              (P = D.main))
                            : l(t) &&
                              (P = e.dateTime.getXDateFormat(
                                  t,
                                  h.dateTimeLabelFormats || {}
                              )));
                    g.isFirst = b;
                    g.isLast = f;
                    const x = {
                        axis: e,
                        chart: p,
                        dateTimeLabelFormat: P,
                        isFirst: b,
                        isLast: f,
                        pos: r,
                        tick: g,
                        tickPositionInfo: G,
                        value: t,
                    };
                    v(this, "labelFormat", x);
                    const B = (b) =>
                        q.formatter
                            ? q.formatter.call(b, b)
                            : q.format
                            ? ((b.text = e.defaultLabelFormatter.call(b, b)),
                              a.format(q.format, b, p))
                            : e.defaultLabelFormatter.call(b, b);
                    h = B.call(x, x);
                    const y = D && D.list;
                    g.shortenLabel = y
                        ? function () {
                              for (K = 0; K < y.length; K++)
                                  if (
                                      (u(x, { dateTimeLabelFormat: y[K] }),
                                      n.attr({ text: B.call(x, x) }),
                                      n.getBBox().width <
                                          e.getSlotWidth(g) - 2 * q.padding)
                                  )
                                      return;
                              n.attr({ text: "" });
                          }
                        : void 0;
                    c && e._addedPlotLB && g.moveLabel(h, q);
                    H(n) || g.movedLabel
                        ? n &&
                          n.textStr !== h &&
                          !c &&
                          (!n.textWidth ||
                              q.style.width ||
                              n.styles.width ||
                              n.css({ width: null }),
                          n.attr({ text: h }),
                          (n.textPxLength = n.getBBox().width))
                        : ((g.label = n = g.createLabel({ x: 0, y: 0 }, h, q)),
                          (g.rotation = 0));
                }
                createLabel(a, e, h) {
                    const g = this.axis,
                        m = g.chart;
                    if (
                        (a =
                            H(e) && h.enabled
                                ? m.renderer
                                      .text(e, a.x, a.y, h.useHTML)
                                      .add(g.labelGroup)
                                : null)
                    )
                        m.styledMode || a.css(p(h.style)),
                            (a.textPxLength = a.getBBox().width);
                    return a;
                }
                destroy() {
                    B(this, this.axis);
                }
                getPosition(a, e, h, m) {
                    const g = this.axis,
                        d = g.chart,
                        k = (m && d.oldChartHeight) || d.chartHeight;
                    a = {
                        x: a
                            ? z(
                                  g.translate(e + h, void 0, void 0, m) +
                                      g.transB
                              )
                            : g.left +
                              g.offset +
                              (g.opposite
                                  ? ((m && d.oldChartWidth) || d.chartWidth) -
                                    g.right -
                                    g.left
                                  : 0),
                        y: a
                            ? k -
                              g.bottom +
                              g.offset -
                              (g.opposite ? g.height : 0)
                            : z(
                                  k -
                                      g.translate(e + h, void 0, void 0, m) -
                                      g.transB
                              ),
                    };
                    a.y = C(a.y, -1e5, 1e5);
                    v(this, "afterGetPosition", { pos: a });
                    return a;
                }
                getLabelPosition(a, e, h, l, p, d, k, r) {
                    const q = this.axis,
                        g = q.transA,
                        b =
                            q.isLinked && q.linkedParent
                                ? q.linkedParent.reversed
                                : q.reversed,
                        f = q.staggerLines,
                        c = q.tickRotCorr || { x: 0, y: 0 },
                        n =
                            l || q.reserveSpaceDefault
                                ? 0
                                : -q.labelOffset *
                                  ("center" === q.labelAlign ? 0.5 : 1),
                        w = p.distance,
                        D = {};
                    h =
                        0 === q.side
                            ? h.rotation
                                ? -w
                                : -h.getBBox().height
                            : 2 === q.side
                            ? c.y + w
                            : Math.cos(h.rotation * x) *
                              (c.y - h.getBBox(!1, 0).height / 2);
                    H(p.y) && (h = 0 === q.side && q.horiz ? p.y + h : p.y);
                    a =
                        a +
                        m(p.x, [0, 1, 0, -1][q.side] * w) +
                        n +
                        c.x -
                        (d && l ? d * g * (b ? -1 : 1) : 0);
                    e = e + h - (d && !l ? d * g * (b ? 1 : -1) : 0);
                    f &&
                        ((l = (k / (r || 1)) % f),
                        q.opposite && (l = f - l - 1),
                        (e += (q.labelOffset / f) * l));
                    D.x = a;
                    D.y = Math.round(e);
                    v(this, "afterGetLabelPosition", {
                        pos: D,
                        tickmarkOffset: d,
                        index: k,
                    });
                    return D;
                }
                getLabelSize() {
                    return this.label
                        ? this.label.getBBox()[
                              this.axis.horiz ? "height" : "width"
                          ]
                        : 0;
                }
                getMarkPath(a, e, h, m, l, d) {
                    return d.crispLine(
                        [
                            ["M", a, e],
                            ["L", a + (l ? 0 : -h), e + (l ? h : 0)],
                        ],
                        m
                    );
                }
                handleOverflow(a) {
                    const e = this.axis,
                        g = e.options.labels,
                        h = a.x;
                    var l = e.chart.chartWidth,
                        d = e.chart.spacing;
                    const k = m(e.labelLeft, Math.min(e.pos, d[3]));
                    d = m(
                        e.labelRight,
                        Math.max(e.isRadial ? 0 : e.pos + e.len, l - d[1])
                    );
                    const r = this.label,
                        q = this.rotation,
                        p = { left: 0, center: 0.5, right: 1 }[
                            e.labelAlign || r.attr("align")
                        ],
                        b = r.getBBox().width,
                        f = e.getSlotWidth(this),
                        c = {};
                    let n = f,
                        t = 1,
                        D;
                    if (q || "justify" !== g.overflow)
                        0 > q && h - p * b < k
                            ? (D = Math.round(h / Math.cos(q * x) - k))
                            : 0 < q &&
                              h + p * b > d &&
                              (D = Math.round((l - h) / Math.cos(q * x)));
                    else if (
                        ((l = h + (1 - p) * b),
                        h - p * b < k
                            ? (n = a.x + n * (1 - p) - k)
                            : l > d && ((n = d - a.x + n * p), (t = -1)),
                        (n = Math.min(f, n)),
                        n < f &&
                            "center" === e.labelAlign &&
                            (a.x += t * (f - n - p * (f - Math.min(b, n)))),
                        b > n || (e.autoRotation && (r.styles || {}).width))
                    )
                        D = n;
                    D &&
                        (this.shortenLabel
                            ? this.shortenLabel()
                            : ((c.width = Math.floor(D) + "px"),
                              (g.style || {}).textOverflow ||
                                  (c.textOverflow = "ellipsis"),
                              r.css(c)));
                }
                moveLabel(a, e) {
                    const g = this;
                    var h = g.label;
                    const m = g.axis;
                    let d = !1;
                    h && h.textStr === a
                        ? ((g.movedLabel = h), (d = !0), delete g.label)
                        : t(m.ticks, function (k) {
                              d ||
                                  k.isNew ||
                                  k === g ||
                                  !k.label ||
                                  k.label.textStr !== a ||
                                  ((g.movedLabel = k.label),
                                  (d = !0),
                                  (k.labelPos = g.movedLabel.xy),
                                  delete k.label);
                          });
                    d ||
                        (!g.labelPos && !h) ||
                        ((h = g.labelPos || h.xy),
                        (g.movedLabel = g.createLabel(h, a, e)),
                        g.movedLabel && g.movedLabel.attr({ opacity: 0 }));
                }
                render(a, e, h) {
                    var g = this.axis,
                        l = g.horiz,
                        d = this.pos,
                        k = m(this.tickmarkOffset, g.tickmarkOffset);
                    d = this.getPosition(l, d, k, e);
                    k = d.x;
                    const r = d.y;
                    g =
                        (l && k === g.pos + g.len) || (!l && r === g.pos)
                            ? -1
                            : 1;
                    l = m(h, this.label && this.label.newOpacity, 1);
                    h = m(h, 1);
                    this.isActive = !0;
                    this.renderGridLine(e, h, g);
                    this.renderMark(d, h, g);
                    this.renderLabel(d, e, l, a);
                    this.isNew = !1;
                    v(this, "afterRender");
                }
                renderGridLine(a, e, h) {
                    const g = this.axis,
                        l = g.options,
                        d = {},
                        k = this.pos,
                        r = this.type,
                        q = m(this.tickmarkOffset, g.tickmarkOffset),
                        p = g.chart.renderer;
                    let b = this.gridLine,
                        f = l.gridLineWidth,
                        c = l.gridLineColor,
                        n = l.gridLineDashStyle;
                    "minor" === this.type &&
                        ((f = l.minorGridLineWidth),
                        (c = l.minorGridLineColor),
                        (n = l.minorGridLineDashStyle));
                    b ||
                        (g.chart.styledMode ||
                            ((d.stroke = c),
                            (d["stroke-width"] = f || 0),
                            (d.dashstyle = n)),
                        r || (d.zIndex = 1),
                        a && (e = 0),
                        (this.gridLine = b =
                            p
                                .path()
                                .attr(d)
                                .addClass(
                                    "highcharts-" +
                                        (r ? r + "-" : "") +
                                        "grid-line"
                                )
                                .add(g.gridGroup)));
                    if (
                        b &&
                        (h = g.getPlotLinePath({
                            value: k + q,
                            lineWidth: b.strokeWidth() * h,
                            force: "pass",
                            old: a,
                            acrossPanes: !1,
                        }))
                    )
                        b[a || this.isNew ? "attr" : "animate"]({
                            d: h,
                            opacity: e,
                        });
                }
                renderMark(a, e, h) {
                    const g = this.axis;
                    var l = g.options;
                    const d = g.chart.renderer,
                        k = this.type,
                        r = g.tickSize(k ? k + "Tick" : "tick"),
                        q = a.x;
                    a = a.y;
                    const p = m(
                        l["minor" !== k ? "tickWidth" : "minorTickWidth"],
                        !k && g.isXAxis ? 1 : 0
                    );
                    l = l["minor" !== k ? "tickColor" : "minorTickColor"];
                    let b = this.mark;
                    const f = !b;
                    r &&
                        (g.opposite && (r[0] = -r[0]),
                        b ||
                            ((this.mark = b =
                                d
                                    .path()
                                    .addClass(
                                        "highcharts-" +
                                            (k ? k + "-" : "") +
                                            "tick"
                                    )
                                    .add(g.axisGroup)),
                            g.chart.styledMode ||
                                b.attr({ stroke: l, "stroke-width": p })),
                        b[f ? "attr" : "animate"]({
                            d: this.getMarkPath(
                                q,
                                a,
                                r[0],
                                b.strokeWidth() * h,
                                g.horiz,
                                d
                            ),
                            opacity: e,
                        }));
                }
                renderLabel(a, e, h, p) {
                    var g = this.axis;
                    const d = g.horiz,
                        k = g.options,
                        r = this.label,
                        q = k.labels,
                        t = q.step;
                    g = m(this.tickmarkOffset, g.tickmarkOffset);
                    const b = a.x;
                    a = a.y;
                    let f = !0;
                    r &&
                        l(b) &&
                        ((r.xy = a =
                            this.getLabelPosition(b, a, r, d, q, g, p, t)),
                        (this.isFirst && !this.isLast && !k.showFirstLabel) ||
                        (this.isLast && !this.isFirst && !k.showLastLabel)
                            ? (f = !1)
                            : !d ||
                              q.step ||
                              q.rotation ||
                              e ||
                              0 === h ||
                              this.handleOverflow(a),
                        t && p % t && (f = !1),
                        f && l(a.y)
                            ? ((a.opacity = h),
                              r[this.isNewLabel ? "attr" : "animate"](a).show(
                                  !0
                              ),
                              (this.isNewLabel = !1))
                            : (r.hide(), (this.isNewLabel = !0)));
                }
                replaceMovedLabel() {
                    const a = this.label,
                        e = this.axis;
                    a &&
                        !this.isNew &&
                        (a.animate({ opacity: 0 }, void 0, a.destroy),
                        delete this.label);
                    e.isDirty = !0;
                    this.label = this.movedLabel;
                    delete this.movedLabel;
                }
            }
            ("");
            return h;
        }
    );
    M(
        a,
        "Core/Axis/Axis.js",
        [
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Axis/AxisDefaults.js"],
            a["Core/Color/Color.js"],
            a["Core/Defaults.js"],
            a["Core/Foundation.js"],
            a["Core/Globals.js"],
            a["Core/Axis/Tick.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C, z, H, B) {
            const { animObject: u } = a,
                { defaultOptions: v } = L,
                { registerEventOptions: l } = C,
                { deg2rad: p } = z,
                {
                    arrayMax: t,
                    arrayMin: m,
                    clamp: h,
                    correctFloat: g,
                    defined: e,
                    destroyObjectProperties: w,
                    erase: x,
                    error: F,
                    extend: d,
                    fireEvent: k,
                    getClosestDistance: r,
                    insertItem: q,
                    isArray: G,
                    isNumber: b,
                    isString: f,
                    merge: c,
                    normalizeTickInterval: n,
                    objectEach: P,
                    pick: D,
                    relativeLength: K,
                    removeEvent: X,
                    splat: T,
                    syncTimeout: Z,
                } = B,
                V = (b, c) =>
                    n(
                        c,
                        void 0,
                        void 0,
                        D(
                            b.options.allowDecimals,
                            0.5 > c || void 0 !== b.tickAmount
                        ),
                        !!b.tickAmount
                    );
            class Y {
                constructor(b, c, f) {
                    this.zoomEnabled =
                        this.width =
                        this.visible =
                        this.userOptions =
                        this.translationSlope =
                        this.transB =
                        this.transA =
                        this.top =
                        this.ticks =
                        this.tickRotCorr =
                        this.tickPositions =
                        this.tickmarkOffset =
                        this.tickInterval =
                        this.tickAmount =
                        this.side =
                        this.series =
                        this.right =
                        this.positiveValuesOnly =
                        this.pos =
                        this.pointRangePadding =
                        this.pointRange =
                        this.plotLinesAndBandsGroups =
                        this.plotLinesAndBands =
                        this.paddedTicks =
                        this.overlap =
                        this.options =
                        this.offset =
                        this.names =
                        this.minPixelPadding =
                        this.minorTicks =
                        this.minorTickInterval =
                        this.min =
                        this.maxLabelLength =
                        this.max =
                        this.len =
                        this.left =
                        this.labelFormatter =
                        this.labelEdge =
                        this.isLinked =
                        this.index =
                        this.height =
                        this.hasVisibleSeries =
                        this.hasNames =
                        this.eventOptions =
                        this.coll =
                        this.closestPointRange =
                        this.chart =
                        this.bottom =
                        this.alternateBands =
                            void 0;
                    this.init(b, c, f);
                }
                init(c, f, a = this.coll) {
                    const d = "xAxis" === a;
                    this.chart = c;
                    this.horiz = this.isZAxis || (c.inverted ? !d : d);
                    this.isXAxis = d;
                    this.coll = a;
                    k(this, "init", { userOptions: f });
                    this.opposite = D(f.opposite, this.opposite);
                    this.side = D(
                        f.side,
                        this.side,
                        this.horiz
                            ? this.opposite
                                ? 0
                                : 2
                            : this.opposite
                            ? 1
                            : 3
                    );
                    this.setOptions(f);
                    a = this.options;
                    const A = a.labels,
                        n = a.type;
                    this.userOptions = f;
                    this.minPixelPadding = 0;
                    this.reversed = D(a.reversed, this.reversed);
                    this.visible = a.visible;
                    this.zoomEnabled = a.zoomEnabled;
                    this.hasNames = "category" === n || !0 === a.categories;
                    this.categories =
                        a.categories || (this.hasNames ? [] : void 0);
                    this.names || ((this.names = []), (this.names.keys = {}));
                    this.plotLinesAndBandsGroups = {};
                    this.positiveValuesOnly = !!this.logarithmic;
                    this.isLinked = e(a.linkedTo);
                    this.ticks = {};
                    this.labelEdge = [];
                    this.minorTicks = {};
                    this.plotLinesAndBands = [];
                    this.alternateBands = {};
                    this.len = 0;
                    this.minRange = this.userMinRange = a.minRange || a.maxZoom;
                    this.range = a.range;
                    this.offset = a.offset || 0;
                    this.min = this.max = null;
                    f = D(
                        a.crosshair,
                        T(c.options.tooltip.crosshairs)[d ? 0 : 1]
                    );
                    this.crosshair = !0 === f ? {} : f;
                    -1 === c.axes.indexOf(this) &&
                        (d
                            ? c.axes.splice(c.xAxis.length, 0, this)
                            : c.axes.push(this),
                        q(this, c[this.coll]));
                    c.orderItems(this.coll);
                    this.series = this.series || [];
                    c.inverted &&
                        !this.isZAxis &&
                        d &&
                        "undefined" === typeof this.reversed &&
                        (this.reversed = !0);
                    this.labelRotation = b(A.rotation) ? A.rotation : void 0;
                    l(this, a);
                    k(this, "afterInit");
                }
                setOptions(b) {
                    this.options = c(
                        y.defaultXAxisOptions,
                        "yAxis" === this.coll && y.defaultYAxisOptions,
                        [
                            y.defaultTopAxisOptions,
                            y.defaultRightAxisOptions,
                            y.defaultBottomAxisOptions,
                            y.defaultLeftAxisOptions,
                        ][this.side],
                        c(v[this.coll], b)
                    );
                    k(this, "afterSetOptions", { userOptions: b });
                }
                defaultLabelFormatter(c) {
                    var f = this.axis;
                    ({ numberFormatter: c } = this.chart);
                    const a = b(this.value) ? this.value : NaN,
                        d = f.chart.time,
                        k = this.dateTimeLabelFormat;
                    var n = v.lang;
                    const A = n.numericSymbols;
                    n = n.numericSymbolMagnitude || 1e3;
                    const e = f.logarithmic ? Math.abs(a) : f.tickInterval;
                    let q = A && A.length,
                        g;
                    if (f.categories) g = `${this.value}`;
                    else if (k) g = d.dateFormat(k, a);
                    else if (q && 1e3 <= e)
                        for (; q-- && "undefined" === typeof g; )
                            (f = Math.pow(n, q + 1)),
                                e >= f &&
                                    0 === (10 * a) % f &&
                                    null !== A[q] &&
                                    0 !== a &&
                                    (g = c(a / f, -1) + A[q]);
                    "undefined" === typeof g &&
                        (g =
                            1e4 <= Math.abs(a)
                                ? c(a, -1)
                                : c(a, -1, void 0, ""));
                    return g;
                }
                getSeriesExtremes() {
                    const c = this,
                        f = c.chart;
                    let a;
                    k(this, "getSeriesExtremes", null, function () {
                        c.hasVisibleSeries = !1;
                        c.dataMin = c.dataMax = c.threshold = null;
                        c.softThreshold = !c.isXAxis;
                        c.series.forEach(function (d) {
                            if (
                                d.visible ||
                                !f.options.chart.ignoreHiddenSeries
                            ) {
                                var k = d.options;
                                let f = k.threshold,
                                    n,
                                    A;
                                c.hasVisibleSeries = !0;
                                c.positiveValuesOnly && 0 >= f && (f = null);
                                if (c.isXAxis)
                                    (k = d.xData) &&
                                        k.length &&
                                        ((k = c.logarithmic
                                            ? k.filter((b) => 0 < b)
                                            : k),
                                        (a = d.getXExtremes(k)),
                                        (n = a.min),
                                        (A = a.max),
                                        b(n) ||
                                            n instanceof Date ||
                                            ((k = k.filter(b)),
                                            (a = d.getXExtremes(k)),
                                            (n = a.min),
                                            (A = a.max)),
                                        k.length &&
                                            ((c.dataMin = Math.min(
                                                D(c.dataMin, n),
                                                n
                                            )),
                                            (c.dataMax = Math.max(
                                                D(c.dataMax, A),
                                                A
                                            ))));
                                else if (
                                    ((d = d.applyExtremes()),
                                    b(d.dataMin) &&
                                        ((n = d.dataMin),
                                        (c.dataMin = Math.min(
                                            D(c.dataMin, n),
                                            n
                                        ))),
                                    b(d.dataMax) &&
                                        ((A = d.dataMax),
                                        (c.dataMax = Math.max(
                                            D(c.dataMax, A),
                                            A
                                        ))),
                                    e(f) && (c.threshold = f),
                                    !k.softThreshold || c.positiveValuesOnly)
                                )
                                    c.softThreshold = !1;
                            }
                        });
                    });
                    k(this, "afterGetSeriesExtremes");
                }
                translate(c, f, a, d, k, n) {
                    const e = this.linkedParent || this,
                        A = d && e.old ? e.old.min : e.min;
                    if (!b(A)) return NaN;
                    const q = e.minPixelPadding;
                    k =
                        (e.isOrdinal ||
                            (e.brokenAxis && e.brokenAxis.hasBreaks) ||
                            (e.logarithmic && k)) &&
                        e.lin2val;
                    let J = 1,
                        h = 0;
                    d = d && e.old ? e.old.transA : e.transA;
                    d || (d = e.transA);
                    a && ((J *= -1), (h = e.len));
                    e.reversed && ((J *= -1), (h -= J * (e.sector || e.len)));
                    f
                        ? ((n = (c * J + h - q) / d + A),
                          k && (n = e.lin2val(n)))
                        : (k && (c = e.val2lin(c)),
                          (c = J * (c - A) * d),
                          (n =
                              (e.isRadial ? c : g(c)) +
                              h +
                              J * q +
                              (b(n) ? d * n : 0)));
                    return n;
                }
                toPixels(b, c) {
                    return (
                        this.translate(b, !1, !this.horiz, void 0, !0) +
                        (c ? 0 : this.pos)
                    );
                }
                toValue(b, c) {
                    return this.translate(
                        b - (c ? 0 : this.pos),
                        !0,
                        !this.horiz,
                        void 0,
                        !0
                    );
                }
                getPlotLinePath(c) {
                    function f(b, c, f) {
                        "pass" !== t &&
                            (b < c || b > f) &&
                            (t ? (b = h(b, c, f)) : (K = !0));
                        return b;
                    }
                    const a = this,
                        d = a.chart,
                        n = a.left,
                        e = a.top,
                        A = c.old,
                        q = c.value,
                        g = c.lineWidth,
                        r = (A && d.oldChartHeight) || d.chartHeight,
                        m = (A && d.oldChartWidth) || d.chartWidth,
                        l = a.transB;
                    let p = c.translatedValue,
                        t = c.force,
                        P,
                        w,
                        R,
                        Q,
                        K;
                    c = {
                        value: q,
                        lineWidth: g,
                        old: A,
                        force: t,
                        acrossPanes: c.acrossPanes,
                        translatedValue: p,
                    };
                    k(this, "getPlotLinePath", c, function (c) {
                        p = D(p, a.translate(q, void 0, void 0, A));
                        p = h(p, -1e5, 1e5);
                        P = R = Math.round(p + l);
                        w = Q = Math.round(r - p - l);
                        b(p)
                            ? a.horiz
                                ? ((w = e),
                                  (Q = r - a.bottom),
                                  (P = R = f(P, n, n + a.width)))
                                : ((P = n),
                                  (R = m - a.right),
                                  (w = Q = f(w, e, e + a.height)))
                            : ((K = !0), (t = !1));
                        c.path =
                            K && !t
                                ? null
                                : d.renderer.crispLine(
                                      [
                                          ["M", P, w],
                                          ["L", R, Q],
                                      ],
                                      g || 1
                                  );
                    });
                    return c.path;
                }
                getLinearTickPositions(b, c, f) {
                    const a = g(Math.floor(c / b) * b);
                    f = g(Math.ceil(f / b) * b);
                    const d = [];
                    let k, n;
                    g(a + b) === a && (n = 20);
                    if (this.single) return [c];
                    for (c = a; c <= f; ) {
                        d.push(c);
                        c = g(c + b, n);
                        if (c === k) break;
                        k = c;
                    }
                    return d;
                }
                getMinorTickInterval() {
                    const b = this.options;
                    return !0 === b.minorTicks
                        ? D(b.minorTickInterval, "auto")
                        : !1 === b.minorTicks
                        ? null
                        : b.minorTickInterval;
                }
                getMinorTickPositions() {
                    var b = this.options;
                    const c = this.tickPositions,
                        f = this.minorTickInterval;
                    var a = this.pointRangePadding || 0;
                    const d = this.min - a;
                    a = this.max + a;
                    const k = a - d;
                    let n = [];
                    if (k && k / f < this.len / 3) {
                        const k = this.logarithmic;
                        if (k)
                            this.paddedTicks.forEach(function (b, c, a) {
                                c &&
                                    n.push.apply(
                                        n,
                                        k.getLogTickPositions(
                                            f,
                                            a[c - 1],
                                            a[c],
                                            !0
                                        )
                                    );
                            });
                        else if (
                            this.dateTime &&
                            "auto" === this.getMinorTickInterval()
                        )
                            n = n.concat(
                                this.getTimeTicks(
                                    this.dateTime.normalizeTimeTickInterval(f),
                                    d,
                                    a,
                                    b.startOfWeek
                                )
                            );
                        else
                            for (
                                b = d + ((c[0] - d) % f);
                                b <= a && b !== n[0];
                                b += f
                            )
                                n.push(b);
                    }
                    0 !== n.length && this.trimTicks(n);
                    return n;
                }
                adjustForMinRange() {
                    const b = this.options,
                        c = this.logarithmic;
                    let f = this.min;
                    var a = this.max;
                    let d, k;
                    if (
                        this.isXAxis &&
                        "undefined" === typeof this.minRange &&
                        !c
                    )
                        if (e(b.min) || e(b.max) || e(b.floor) || e(b.ceiling))
                            this.minRange = null;
                        else {
                            var n =
                                r(
                                    this.series.map((b) => {
                                        var c;
                                        return (
                                            (b.xIncrement
                                                ? null === (c = b.xData) ||
                                                  void 0 === c
                                                    ? void 0
                                                    : c.slice(0, 2)
                                                : b.xData) || []
                                        );
                                    })
                                ) || 0;
                            this.minRange = Math.min(
                                5 * n,
                                this.dataMax - this.dataMin
                            );
                        }
                    a - f < this.minRange &&
                        ((n = this.dataMax - this.dataMin >= this.minRange),
                        (k = this.minRange),
                        (a = (k - a + f) / 2),
                        (d = [f - a, D(b.min, f - a)]),
                        n &&
                            (d[2] = c ? c.log2lin(this.dataMin) : this.dataMin),
                        (f = t(d)),
                        (a = [f + k, D(b.max, f + k)]),
                        n &&
                            (a[2] = c ? c.log2lin(this.dataMax) : this.dataMax),
                        (a = m(a)),
                        a - f < k &&
                            ((d[0] = a - k),
                            (d[1] = D(b.min, a - k)),
                            (f = t(d))));
                    this.min = f;
                    this.max = a;
                }
                getClosest() {
                    let b, c;
                    if (this.categories) c = 1;
                    else {
                        const f = [];
                        this.series.forEach(function (b) {
                            var a;
                            const d = b.closestPointRange,
                                k =
                                    b.visible ||
                                    !b.chart.options.chart.ignoreHiddenSeries;
                            1 ===
                            (null === (a = b.xData) || void 0 === a
                                ? void 0
                                : a.length)
                                ? f.push(b.xData[0])
                                : !b.noSharedTooltip &&
                                  e(d) &&
                                  k &&
                                  (c = e(c) ? Math.min(c, d) : d);
                        });
                        f.length && (f.sort((b, c) => b - c), (b = r([f])));
                    }
                    return b && c ? Math.min(b, c) : b || c;
                }
                nameToX(b) {
                    const c = G(this.options.categories),
                        f = c ? this.categories : this.names;
                    let a = b.options.x,
                        d;
                    b.series.requireSorting = !1;
                    e(a) ||
                        (a =
                            this.options.uniqueNames && f
                                ? c
                                    ? f.indexOf(b.name)
                                    : D(f.keys[b.name], -1)
                                : b.series.autoIncrement());
                    -1 === a ? !c && f && (d = f.length) : (d = a);
                    "undefined" !== typeof d
                        ? ((this.names[d] = b.name),
                          (this.names.keys[b.name] = d))
                        : b.x && (d = b.x);
                    return d;
                }
                updateNames() {
                    const b = this,
                        c = this.names;
                    0 < c.length &&
                        (Object.keys(c.keys).forEach(function (b) {
                            delete c.keys[b];
                        }),
                        (c.length = 0),
                        (this.minRange = this.userMinRange),
                        (this.series || []).forEach(function (c) {
                            c.xIncrement = null;
                            if (!c.points || c.isDirtyData)
                                (b.max = Math.max(b.max, c.xData.length - 1)),
                                    c.processData(),
                                    c.generatePoints();
                            c.data.forEach(function (f, a) {
                                let d;
                                f &&
                                    f.options &&
                                    "undefined" !== typeof f.name &&
                                    ((d = b.nameToX(f)),
                                    "undefined" !== typeof d &&
                                        d !== f.x &&
                                        ((f.x = d), (c.xData[a] = d)));
                            });
                        }));
                }
                setAxisTranslation() {
                    const b = this,
                        c = b.max - b.min;
                    var a = b.linkedParent;
                    const d = !!b.categories,
                        n = b.isXAxis;
                    let e = b.axisPointRange || 0,
                        q,
                        g = 0,
                        h = 0,
                        r = b.transA;
                    if (n || d || e)
                        (q = b.getClosest()),
                            a
                                ? ((g = a.minPointOffset),
                                  (h = a.pointRangePadding))
                                : b.series.forEach(function (c) {
                                      const a = d
                                              ? 1
                                              : n
                                              ? D(c.options.pointRange, q, 0)
                                              : b.axisPointRange || 0,
                                          k = c.options.pointPlacement;
                                      e = Math.max(e, a);
                                      if (!b.single || d)
                                          (c = c.is("xrange") ? !n : n),
                                              (g = Math.max(
                                                  g,
                                                  c && f(k) ? 0 : a / 2
                                              )),
                                              (h = Math.max(
                                                  h,
                                                  c && "on" === k ? 0 : a
                                              ));
                                  }),
                            (a =
                                b.ordinal && b.ordinal.slope && q
                                    ? b.ordinal.slope / q
                                    : 1),
                            (b.minPointOffset = g *= a),
                            (b.pointRangePadding = h *= a),
                            (b.pointRange = Math.min(e, b.single && d ? 1 : c)),
                            n && q && (b.closestPointRange = q);
                    b.translationSlope =
                        b.transA =
                        r =
                            b.staticScale || b.len / (c + h || 1);
                    b.transB = b.horiz ? b.left : b.bottom;
                    b.minPixelPadding = r * g;
                    k(this, "afterSetAxisTranslation");
                }
                minFromRange() {
                    return this.max - this.range;
                }
                setTickInterval(c) {
                    var f = this.chart;
                    const a = this.logarithmic,
                        d = this.options,
                        n = this.isXAxis,
                        q = this.isLinked,
                        h = d.tickPixelInterval,
                        A = this.categories,
                        r = this.softThreshold;
                    let m = d.maxPadding,
                        l = d.minPadding;
                    let p =
                            b(d.tickInterval) && 0 <= d.tickInterval
                                ? d.tickInterval
                                : void 0,
                        t = b(this.threshold) ? this.threshold : null,
                        P,
                        w,
                        K;
                    this.dateTime || A || q || this.getTickAmount();
                    w = D(this.userMin, d.min);
                    K = D(this.userMax, d.max);
                    if (q) {
                        this.linkedParent = f[this.coll][d.linkedTo];
                        var R = this.linkedParent.getExtremes();
                        this.min = D(R.min, R.dataMin);
                        this.max = D(R.max, R.dataMax);
                        d.type !== this.linkedParent.options.type &&
                            F(11, 1, f);
                    } else
                        r &&
                            e(t) &&
                            (this.dataMin >= t
                                ? ((R = t), (l = 0))
                                : this.dataMax <= t && ((P = t), (m = 0))),
                            (this.min = D(w, R, this.dataMin)),
                            (this.max = D(K, P, this.dataMax));
                    a &&
                        (this.positiveValuesOnly &&
                            !c &&
                            0 >=
                                Math.min(this.min, D(this.dataMin, this.min)) &&
                            F(10, 1, f),
                        (this.min = g(a.log2lin(this.min), 16)),
                        (this.max = g(a.log2lin(this.max), 16)));
                    this.range &&
                        e(this.max) &&
                        ((this.userMin =
                            this.min =
                            w =
                                Math.max(this.dataMin, this.minFromRange())),
                        (this.userMax = K = this.max),
                        (this.range = null));
                    k(this, "foundExtremes");
                    this.beforePadding && this.beforePadding();
                    this.adjustForMinRange();
                    !b(this.userMin) &&
                        b(d.softMin) &&
                        d.softMin < this.min &&
                        (this.min = w = d.softMin);
                    !b(this.userMax) &&
                        b(d.softMax) &&
                        d.softMax > this.max &&
                        (this.max = K = d.softMax);
                    !(
                        A ||
                        this.axisPointRange ||
                        (this.stacking && this.stacking.usePercentage) ||
                        q
                    ) &&
                        e(this.min) &&
                        e(this.max) &&
                        (f = this.max - this.min) &&
                        (!e(w) && l && (this.min -= f * l),
                        !e(K) && m && (this.max += f * m));
                    !b(this.userMin) &&
                        b(d.floor) &&
                        (this.min = Math.max(this.min, d.floor));
                    !b(this.userMax) &&
                        b(d.ceiling) &&
                        (this.max = Math.min(this.max, d.ceiling));
                    r &&
                        e(this.dataMin) &&
                        ((t = t || 0),
                        !e(w) && this.min < t && this.dataMin >= t
                            ? (this.min = this.options.minRange
                                  ? Math.min(t, this.max - this.minRange)
                                  : t)
                            : !e(K) &&
                              this.max > t &&
                              this.dataMax <= t &&
                              (this.max = this.options.minRange
                                  ? Math.max(t, this.min + this.minRange)
                                  : t));
                    b(this.min) &&
                        b(this.max) &&
                        !this.chart.polar &&
                        this.min > this.max &&
                        (e(this.options.min)
                            ? (this.max = this.min)
                            : e(this.options.max) && (this.min = this.max));
                    this.tickInterval =
                        this.min === this.max ||
                        "undefined" === typeof this.min ||
                        "undefined" === typeof this.max
                            ? 1
                            : q &&
                              this.linkedParent &&
                              !p &&
                              h === this.linkedParent.options.tickPixelInterval
                            ? (p = this.linkedParent.tickInterval)
                            : D(
                                  p,
                                  this.tickAmount
                                      ? (this.max - this.min) /
                                            Math.max(this.tickAmount - 1, 1)
                                      : void 0,
                                  A
                                      ? 1
                                      : ((this.max - this.min) * h) /
                                            Math.max(this.len, h)
                              );
                    if (n && !c) {
                        const b =
                            this.min !== (this.old && this.old.min) ||
                            this.max !== (this.old && this.old.max);
                        this.series.forEach(function (c) {
                            c.forceCrop = c.forceCropping && c.forceCropping();
                            c.processData(b);
                        });
                        k(this, "postProcessData", { hasExtremesChanged: b });
                    }
                    this.setAxisTranslation();
                    k(this, "initialAxisTranslation");
                    this.pointRange &&
                        !p &&
                        (this.tickInterval = Math.max(
                            this.pointRange,
                            this.tickInterval
                        ));
                    c = D(
                        d.minTickInterval,
                        this.dateTime &&
                            !this.series.some((b) => b.noSharedTooltip)
                            ? this.closestPointRange
                            : 0
                    );
                    !p && this.tickInterval < c && (this.tickInterval = c);
                    this.dateTime ||
                        this.logarithmic ||
                        p ||
                        (this.tickInterval = V(this, this.tickInterval));
                    this.tickAmount || (this.tickInterval = this.unsquish());
                    this.setTickPositions();
                }
                setTickPositions() {
                    var c = this.options;
                    const f = c.tickPositions,
                        a = c.tickPositioner;
                    var d = this.getMinorTickInterval(),
                        n = this.hasVerticalPanning(),
                        q = "colorAxis" === this.coll;
                    const g = (q || !n) && c.startOnTick;
                    n = (q || !n) && c.endOnTick;
                    q = [];
                    let h;
                    this.tickmarkOffset =
                        this.categories &&
                        "between" === c.tickmarkPlacement &&
                        1 === this.tickInterval
                            ? 0.5
                            : 0;
                    this.minorTickInterval =
                        "auto" === d && this.tickInterval
                            ? this.tickInterval / c.minorTicksPerMajor
                            : d;
                    this.single =
                        this.min === this.max &&
                        e(this.min) &&
                        !this.tickAmount &&
                        (parseInt(this.min, 10) === this.min ||
                            !1 !== c.allowDecimals);
                    if (f) q = f.slice();
                    else if (b(this.min) && b(this.max)) {
                        if (
                            (this.ordinal && this.ordinal.positions) ||
                            !(
                                (this.max - this.min) / this.tickInterval >
                                Math.max(2 * this.len, 200)
                            )
                        )
                            if (this.dateTime)
                                q = this.getTimeTicks(
                                    this.dateTime.normalizeTimeTickInterval(
                                        this.tickInterval,
                                        c.units
                                    ),
                                    this.min,
                                    this.max,
                                    c.startOfWeek,
                                    this.ordinal && this.ordinal.positions,
                                    this.closestPointRange,
                                    !0
                                );
                            else if (this.logarithmic)
                                q = this.logarithmic.getLogTickPositions(
                                    this.tickInterval,
                                    this.min,
                                    this.max
                                );
                            else
                                for (d = c = this.tickInterval; d <= 2 * c; )
                                    if (
                                        ((q = this.getLinearTickPositions(
                                            this.tickInterval,
                                            this.min,
                                            this.max
                                        )),
                                        this.tickAmount &&
                                            q.length > this.tickAmount)
                                    )
                                        this.tickInterval = V(this, (d *= 1.1));
                                    else break;
                        else (q = [this.min, this.max]), F(19, !1, this.chart);
                        q.length > this.len &&
                            ((q = [q[0], q[q.length - 1]]),
                            q[0] === q[1] && (q.length = 1));
                        a &&
                            ((this.tickPositions = q),
                            (h = a.apply(this, [this.min, this.max])) &&
                                (q = h));
                    }
                    this.tickPositions = q;
                    this.paddedTicks = q.slice(0);
                    this.trimTicks(q, g, n);
                    !this.isLinked &&
                        b(this.min) &&
                        b(this.max) &&
                        (this.single &&
                            2 > q.length &&
                            !this.categories &&
                            !this.series.some(
                                (b) =>
                                    b.is("heatmap") &&
                                    "between" === b.options.pointPlacement
                            ) &&
                            ((this.min -= 0.5), (this.max += 0.5)),
                        f || h || this.adjustTickAmount());
                    k(this, "afterSetTickPositions");
                }
                trimTicks(b, c, f) {
                    const a = b[0],
                        d = b[b.length - 1],
                        n = (!this.isOrdinal && this.minPointOffset) || 0;
                    k(this, "trimTicks");
                    if (!this.isLinked) {
                        if (c && -Infinity !== a) this.min = a;
                        else for (; this.min - n > b[0]; ) b.shift();
                        if (f) this.max = d;
                        else for (; this.max + n < b[b.length - 1]; ) b.pop();
                        0 === b.length &&
                            e(a) &&
                            !this.options.tickPositions &&
                            b.push((d + a) / 2);
                    }
                }
                alignToOthers() {
                    const c = this,
                        f = [this],
                        a = c.options,
                        d =
                            "yAxis" === this.coll &&
                            this.chart.options.chart.alignThresholds,
                        k = [];
                    let n;
                    c.thresholdAlignment = void 0;
                    if (
                        ((!1 !== this.chart.options.chart.alignTicks &&
                            a.alignTicks) ||
                            d) &&
                        !1 !== a.startOnTick &&
                        !1 !== a.endOnTick &&
                        !c.logarithmic
                    ) {
                        const b = (b) => {
                                const { horiz: c, options: f } = b;
                                return [
                                    c ? f.left : f.top,
                                    f.width,
                                    f.height,
                                    f.pane,
                                ].join();
                            },
                            a = b(this);
                        this.chart[this.coll].forEach(function (d) {
                            const { series: k } = d;
                            k.length &&
                                k.some((b) => b.visible) &&
                                d !== c &&
                                b(d) === a &&
                                ((n = !0), f.push(d));
                        });
                    }
                    if (n && d) {
                        f.forEach((f) => {
                            f = f.getThresholdAlignment(c);
                            b(f) && k.push(f);
                        });
                        const a =
                            1 < k.length
                                ? k.reduce((b, c) => b + c, 0) / k.length
                                : void 0;
                        f.forEach((b) => {
                            b.thresholdAlignment = a;
                        });
                    }
                    return n;
                }
                getThresholdAlignment(c) {
                    (!b(this.dataMin) ||
                        (this !== c &&
                            this.series.some(
                                (b) => b.isDirty || b.isDirtyData
                            ))) &&
                        this.getSeriesExtremes();
                    if (b(this.threshold))
                        return (
                            (c = h(
                                (this.threshold - (this.dataMin || 0)) /
                                    ((this.dataMax || 0) - (this.dataMin || 0)),
                                0,
                                1
                            )),
                            this.options.reversed && (c = 1 - c),
                            c
                        );
                }
                getTickAmount() {
                    const b = this.options,
                        c = b.tickPixelInterval;
                    let f = b.tickAmount;
                    !e(b.tickInterval) &&
                        !f &&
                        this.len < c &&
                        !this.isRadial &&
                        !this.logarithmic &&
                        b.startOnTick &&
                        b.endOnTick &&
                        (f = 2);
                    !f &&
                        this.alignToOthers() &&
                        (f = Math.ceil(this.len / c) + 1);
                    4 > f && ((this.finalTickAmt = f), (f = 5));
                    this.tickAmount = f;
                }
                adjustTickAmount() {
                    const c = this,
                        {
                            finalTickAmt: f,
                            max: a,
                            min: d,
                            options: k,
                            tickPositions: n,
                            tickAmount: q,
                            thresholdAlignment: h,
                        } = c,
                        r = n && n.length;
                    var m = D(c.threshold, c.softThreshold ? 0 : null);
                    var l = c.tickInterval;
                    let p;
                    b(h) &&
                        ((p =
                            0.5 > h
                                ? Math.ceil(h * (q - 1))
                                : Math.floor(h * (q - 1))),
                        k.reversed && (p = q - 1 - p));
                    if (c.hasData() && b(d) && b(a)) {
                        const h = () => {
                            c.transA *= (r - 1) / (q - 1);
                            c.min = k.startOnTick ? n[0] : Math.min(d, n[0]);
                            c.max = k.endOnTick
                                ? n[n.length - 1]
                                : Math.max(a, n[n.length - 1]);
                        };
                        if (b(p) && b(c.threshold)) {
                            for (
                                ;
                                n[p] !== m ||
                                n.length !== q ||
                                n[0] > d ||
                                n[n.length - 1] < a;

                            ) {
                                n.length = 0;
                                for (n.push(c.threshold); n.length < q; )
                                    void 0 === n[p] || n[p] > c.threshold
                                        ? n.unshift(g(n[0] - l))
                                        : n.push(g(n[n.length - 1] + l));
                                if (l > 8 * c.tickInterval) break;
                                l *= 2;
                            }
                            h();
                        } else if (r < q) {
                            for (; n.length < q; )
                                n.length % 2 || d === m
                                    ? n.push(g(n[n.length - 1] + l))
                                    : n.unshift(g(n[0] - l));
                            h();
                        }
                        if (e(f)) {
                            for (l = m = n.length; l--; )
                                ((3 === f && 1 === l % 2) ||
                                    (2 >= f && 0 < l && l < m - 1)) &&
                                    n.splice(l, 1);
                            c.finalTickAmt = void 0;
                        }
                    }
                }
                setScale() {
                    let b = !1,
                        c = !1;
                    this.series.forEach(function (f) {
                        b = b || f.isDirtyData || f.isDirty;
                        c = c || (f.xAxis && f.xAxis.isDirty) || !1;
                    });
                    this.setAxisSize();
                    const f = this.len !== (this.old && this.old.len);
                    f ||
                    b ||
                    c ||
                    this.isLinked ||
                    this.forceRedraw ||
                    this.userMin !== (this.old && this.old.userMin) ||
                    this.userMax !== (this.old && this.old.userMax) ||
                    this.alignToOthers()
                        ? (this.stacking &&
                              (this.stacking.resetStacks(),
                              this.stacking.buildStacks()),
                          (this.forceRedraw = !1),
                          this.userMinRange || (this.minRange = void 0),
                          this.getSeriesExtremes(),
                          this.setTickInterval(),
                          this.isDirty ||
                              (this.isDirty =
                                  f ||
                                  this.min !== (this.old && this.old.min) ||
                                  this.max !== (this.old && this.old.max)))
                        : this.stacking && this.stacking.cleanStacks();
                    b && this.panningState && (this.panningState.isDirty = !0);
                    k(this, "afterSetScale");
                }
                setExtremes(b, c, f, a, n) {
                    const e = this,
                        q = e.chart;
                    f = D(f, !0);
                    e.series.forEach(function (b) {
                        delete b.kdTree;
                    });
                    n = d(n, { min: b, max: c });
                    k(e, "setExtremes", n, function () {
                        e.userMin = b;
                        e.userMax = c;
                        e.eventArgs = n;
                        f && q.redraw(a);
                    });
                }
                zoom(b, c) {
                    const f = this,
                        a = this.dataMin,
                        d = this.dataMax,
                        n = this.options,
                        q = Math.min(a, D(n.min, a)),
                        g = Math.max(d, D(n.max, d));
                    b = { newMin: b, newMax: c };
                    k(this, "zoom", b, function (b) {
                        let c = b.newMin,
                            n = b.newMax;
                        if (c !== f.min || n !== f.max)
                            f.allowZoomOutside ||
                                (e(a) && (c < q && (c = q), c > g && (c = g)),
                                e(d) && (n < q && (n = q), n > g && (n = g))),
                                (f.displayBtn =
                                    "undefined" !== typeof c ||
                                    "undefined" !== typeof n),
                                f.setExtremes(c, n, !1, void 0, {
                                    trigger: "zoom",
                                });
                        b.zoomed = !0;
                    });
                    return b.zoomed;
                }
                setAxisSize() {
                    const b = this.chart;
                    var c = this.options;
                    const f = c.offsets || [0, 0, 0, 0],
                        a = this.horiz,
                        d = (this.width = Math.round(
                            K(
                                D(c.width, b.plotWidth - f[3] + f[1]),
                                b.plotWidth
                            )
                        )),
                        n = (this.height = Math.round(
                            K(
                                D(c.height, b.plotHeight - f[0] + f[2]),
                                b.plotHeight
                            )
                        )),
                        k = (this.top = Math.round(
                            K(
                                D(c.top, b.plotTop + f[0]),
                                b.plotHeight,
                                b.plotTop
                            )
                        ));
                    c = this.left = Math.round(
                        K(D(c.left, b.plotLeft + f[3]), b.plotWidth, b.plotLeft)
                    );
                    this.bottom = b.chartHeight - n - k;
                    this.right = b.chartWidth - d - c;
                    this.len = Math.max(a ? d : n, 0);
                    this.pos = a ? c : k;
                }
                getExtremes() {
                    const b = this.logarithmic;
                    return {
                        min: b ? g(b.lin2log(this.min)) : this.min,
                        max: b ? g(b.lin2log(this.max)) : this.max,
                        dataMin: this.dataMin,
                        dataMax: this.dataMax,
                        userMin: this.userMin,
                        userMax: this.userMax,
                    };
                }
                getThreshold(b) {
                    var c = this.logarithmic;
                    const f = c ? c.lin2log(this.min) : this.min;
                    c = c ? c.lin2log(this.max) : this.max;
                    null === b || -Infinity === b
                        ? (b = f)
                        : Infinity === b
                        ? (b = c)
                        : f > b
                        ? (b = f)
                        : c < b && (b = c);
                    return this.translate(b, 0, 1, 0, 1);
                }
                autoLabelAlign(b) {
                    const c = (D(b, 0) - 90 * this.side + 720) % 360;
                    b = { align: "center" };
                    k(this, "autoLabelAlign", b, function (b) {
                        15 < c && 165 > c
                            ? (b.align = "right")
                            : 195 < c && 345 > c && (b.align = "left");
                    });
                    return b.align;
                }
                tickSize(b) {
                    const c = this.options,
                        f = D(
                            c["tick" === b ? "tickWidth" : "minorTickWidth"],
                            "tick" === b && this.isXAxis && !this.categories
                                ? 1
                                : 0
                        );
                    let a = c["tick" === b ? "tickLength" : "minorTickLength"],
                        d;
                    f &&
                        a &&
                        ("inside" === c[b + "Position"] && (a = -a),
                        (d = [a, f]));
                    b = { tickSize: d };
                    k(this, "afterTickSize", b);
                    return b.tickSize;
                }
                labelMetrics() {
                    const b = this.chart.renderer;
                    var c = this.ticks;
                    c = c[Object.keys(c)[0]] || {};
                    return this.chart.renderer.fontMetrics(
                        c.label || c.movedLabel || b.box
                    );
                }
                unsquish() {
                    const c = this.options.labels;
                    var f = this.horiz;
                    const a = this.tickInterval,
                        d =
                            this.len /
                            (((this.categories ? 1 : 0) + this.max - this.min) /
                                a),
                        n = c.rotation,
                        k = 0.75 * this.labelMetrics().h,
                        e = Math.max(this.max - this.min, 0),
                        q = function (b) {
                            let c = b / (d || 1);
                            c = 1 < c ? Math.ceil(c) : 1;
                            c * a > e &&
                                Infinity !== b &&
                                Infinity !== d &&
                                e &&
                                (c = Math.ceil(e / a));
                            return g(c * a);
                        };
                    let h = a,
                        r,
                        l = Number.MAX_VALUE,
                        m;
                    if (f) {
                        if (
                            (c.staggerLines ||
                                (b(n)
                                    ? (m = [n])
                                    : d < c.autoRotationLimit &&
                                      (m = c.autoRotation)),
                            m)
                        ) {
                            let b;
                            for (const c of m)
                                if (c === n || (c && -90 <= c && 90 >= c))
                                    (f = q(Math.abs(k / Math.sin(p * c)))),
                                        (b = f + Math.abs(c / 360)),
                                        b < l && ((l = b), (r = c), (h = f));
                        }
                    } else h = q(k);
                    this.autoRotation = m;
                    this.labelRotation = D(r, b(n) ? n : 0);
                    return c.step ? a : h;
                }
                getSlotWidth(c) {
                    const f = this.chart,
                        a = this.horiz,
                        d = this.options.labels,
                        n = Math.max(
                            this.tickPositions.length -
                                (this.categories ? 0 : 1),
                            1
                        ),
                        k = f.margin[3];
                    if (c && b(c.slotWidth)) return c.slotWidth;
                    if (a && 2 > d.step)
                        return d.rotation
                            ? 0
                            : ((this.staggerLines || 1) * this.len) / n;
                    if (!a) {
                        c = d.style.width;
                        if (void 0 !== c) return parseInt(String(c), 10);
                        if (k) return k - f.spacing[3];
                    }
                    return 0.33 * f.chartWidth;
                }
                renderUnsquish() {
                    const b = this.chart,
                        c = b.renderer,
                        a = this.tickPositions,
                        d = this.ticks,
                        n = this.options.labels,
                        k = n.style,
                        e = this.horiz,
                        q = this.getSlotWidth();
                    var g = Math.max(1, Math.round(q - 2 * n.padding));
                    const h = {},
                        r = this.labelMetrics(),
                        m = k.textOverflow;
                    let l,
                        p,
                        D = 0;
                    f(n.rotation) || (h.rotation = n.rotation || 0);
                    a.forEach(function (b) {
                        b = d[b];
                        b.movedLabel && b.replaceMovedLabel();
                        b &&
                            b.label &&
                            b.label.textPxLength > D &&
                            (D = b.label.textPxLength);
                    });
                    this.maxLabelLength = D;
                    if (this.autoRotation)
                        D > g && D > r.h
                            ? (h.rotation = this.labelRotation)
                            : (this.labelRotation = 0);
                    else if (q && ((l = g), !m))
                        for (p = "clip", g = a.length; !e && g--; ) {
                            var t = a[g];
                            if ((t = d[t].label))
                                t.styles && "ellipsis" === t.styles.textOverflow
                                    ? t.css({ textOverflow: "clip" })
                                    : t.textPxLength > q &&
                                      t.css({ width: q + "px" }),
                                    t.getBBox().height >
                                        this.len / a.length - (r.h - r.f) &&
                                        (t.specificTextOverflow = "ellipsis");
                        }
                    h.rotation &&
                        ((l =
                            D > 0.5 * b.chartHeight ? 0.33 * b.chartHeight : D),
                        m || (p = "ellipsis"));
                    if (
                        (this.labelAlign =
                            n.align || this.autoLabelAlign(this.labelRotation))
                    )
                        h.align = this.labelAlign;
                    a.forEach(function (b) {
                        const c = (b = d[b]) && b.label,
                            f = k.width,
                            a = {};
                        c &&
                            (c.attr(h),
                            b.shortenLabel
                                ? b.shortenLabel()
                                : l &&
                                  !f &&
                                  "nowrap" !== k.whiteSpace &&
                                  (l < c.textPxLength ||
                                      "SPAN" === c.element.tagName)
                                ? ((a.width = l + "px"),
                                  m ||
                                      (a.textOverflow =
                                          c.specificTextOverflow || p),
                                  c.css(a))
                                : c.styles &&
                                  c.styles.width &&
                                  !a.width &&
                                  !f &&
                                  c.css({ width: null }),
                            delete c.specificTextOverflow,
                            (b.rotation = h.rotation));
                    }, this);
                    this.tickRotCorr = c.rotCorr(
                        r.b,
                        this.labelRotation || 0,
                        0 !== this.side
                    );
                }
                hasData() {
                    return (
                        this.series.some(function (b) {
                            return b.hasData();
                        }) ||
                        (this.options.showEmpty && e(this.min) && e(this.max))
                    );
                }
                addTitle(b) {
                    const f = this.chart.renderer,
                        a = this.horiz,
                        d = this.opposite,
                        n = this.options.title,
                        k = this.chart.styledMode;
                    let e;
                    this.axisTitle ||
                        ((e = n.textAlign) ||
                            (e = (
                                a
                                    ? {
                                          low: "left",
                                          middle: "center",
                                          high: "right",
                                      }
                                    : {
                                          low: d ? "right" : "left",
                                          middle: "center",
                                          high: d ? "left" : "right",
                                      }
                            )[n.align]),
                        (this.axisTitle = f
                            .text(n.text || "", 0, 0, n.useHTML)
                            .attr({
                                zIndex: 7,
                                rotation: n.rotation,
                                align: e,
                            })
                            .addClass("highcharts-axis-title")),
                        k || this.axisTitle.css(c(n.style)),
                        this.axisTitle.add(this.axisGroup),
                        (this.axisTitle.isNew = !0));
                    k ||
                        n.style.width ||
                        this.isRadial ||
                        this.axisTitle.css({ width: this.len + "px" });
                    this.axisTitle[b ? "show" : "hide"](b);
                }
                generateTick(b) {
                    const c = this.ticks;
                    c[b] ? c[b].addLabel() : (c[b] = new H(this, b));
                }
                getOffset() {
                    const c = this,
                        {
                            chart: f,
                            horiz: a,
                            options: d,
                            side: n,
                            ticks: q,
                            tickPositions: g,
                            coll: h,
                            axisParent: r,
                        } = c,
                        m = f.renderer,
                        l = f.inverted && !c.isZAxis ? [1, 0, 3, 2][n] : n;
                    var p = c.hasData();
                    const t = d.title;
                    var w = d.labels;
                    const K = b(d.crossing);
                    var G = f.axisOffset;
                    const R = f.clipOffset,
                        Q = [-1, 1, 1, -1][n],
                        v = d.className;
                    let ja,
                        u = 0,
                        x;
                    var E = 0;
                    let F = 0;
                    c.showAxis = ja = p || d.showEmpty;
                    c.staggerLines = (c.horiz && w.staggerLines) || void 0;
                    if (!c.axisGroup) {
                        const b = (b, c, f) =>
                            m
                                .g(b)
                                .attr({ zIndex: f })
                                .addClass(
                                    `highcharts-${h.toLowerCase()}${c} ` +
                                        (this.isRadial
                                            ? `highcharts-radial-axis${c} `
                                            : "") +
                                        (v || "")
                                )
                                .add(r);
                        c.gridGroup = b("grid", "-grid", d.gridZIndex);
                        c.axisGroup = b("axis", "", d.zIndex);
                        c.labelGroup = b("axis-labels", "-labels", w.zIndex);
                    }
                    p || c.isLinked
                        ? (g.forEach(function (b) {
                              c.generateTick(b);
                          }),
                          c.renderUnsquish(),
                          (c.reserveSpaceDefault =
                              0 === n ||
                              2 === n ||
                              { 1: "left", 3: "right" }[n] === c.labelAlign),
                          D(
                              w.reserveSpace,
                              K ? !1 : null,
                              "center" === c.labelAlign ? !0 : null,
                              c.reserveSpaceDefault
                          ) &&
                              g.forEach(function (b) {
                                  F = Math.max(q[b].getLabelSize(), F);
                              }),
                          c.staggerLines && (F *= c.staggerLines),
                          (c.labelOffset = F * (c.opposite ? -1 : 1)))
                        : P(q, function (b, c) {
                              b.destroy();
                              delete q[c];
                          });
                    t &&
                        t.text &&
                        !1 !== t.enabled &&
                        (c.addTitle(ja),
                        ja &&
                            !K &&
                            !1 !== t.reserveSpace &&
                            ((c.titleOffset = u =
                                c.axisTitle.getBBox()[a ? "height" : "width"]),
                            (x = t.offset),
                            (E = e(x) ? 0 : D(t.margin, a ? 5 : 10))));
                    c.renderLine();
                    c.offset =
                        Q * D(d.offset, G[n] ? G[n] + (d.margin || 0) : 0);
                    c.tickRotCorr = c.tickRotCorr || { x: 0, y: 0 };
                    p =
                        0 === n
                            ? -c.labelMetrics().h
                            : 2 === n
                            ? c.tickRotCorr.y
                            : 0;
                    E = Math.abs(F) + E;
                    F &&
                        (E =
                            E -
                            p +
                            Q *
                                (a
                                    ? D(w.y, c.tickRotCorr.y + Q * w.distance)
                                    : D(w.x, Q * w.distance)));
                    c.axisTitleMargin = D(x, E);
                    c.getMaxLabelDimensions &&
                        (c.maxLabelDimensions = c.getMaxLabelDimensions(q, g));
                    "colorAxis" !== h &&
                        ((w = this.tickSize("tick")),
                        (G[n] = Math.max(
                            G[n],
                            (c.axisTitleMargin || 0) + u + Q * c.offset,
                            E,
                            g && g.length && w ? w[0] + Q * c.offset : 0
                        )),
                        (G =
                            !c.axisLine || d.offset
                                ? 0
                                : 2 * Math.floor(c.axisLine.strokeWidth() / 2)),
                        (R[l] = Math.max(R[l], G)));
                    k(this, "afterGetOffset");
                }
                getLinePath(b) {
                    const c = this.chart,
                        f = this.opposite;
                    var a = this.offset;
                    const d = this.horiz,
                        n = this.left + (f ? this.width : 0) + a;
                    a = c.chartHeight - this.bottom - (f ? this.height : 0) + a;
                    f && (b *= -1);
                    return c.renderer.crispLine(
                        [
                            ["M", d ? this.left : n, d ? a : this.top],
                            [
                                "L",
                                d ? c.chartWidth - this.right : n,
                                d ? a : c.chartHeight - this.bottom,
                            ],
                        ],
                        b
                    );
                }
                renderLine() {
                    this.axisLine ||
                        ((this.axisLine = this.chart.renderer
                            .path()
                            .addClass("highcharts-axis-line")
                            .add(this.axisGroup)),
                        this.chart.styledMode ||
                            this.axisLine.attr({
                                stroke: this.options.lineColor,
                                "stroke-width": this.options.lineWidth,
                                zIndex: 7,
                            }));
                }
                getTitlePosition(b) {
                    var c = this.horiz,
                        f = this.left;
                    const a = this.top;
                    var d = this.len;
                    const n = this.options.title,
                        e = c ? f : a,
                        q = this.opposite,
                        g = this.offset,
                        h = n.x,
                        r = n.y,
                        l = this.chart.renderer.fontMetrics(b);
                    b = b ? Math.max(b.getBBox(!1, 0).height - l.h - 1, 0) : 0;
                    d = {
                        low: e + (c ? 0 : d),
                        middle: e + d / 2,
                        high: e + (c ? d : 0),
                    }[n.align];
                    f =
                        (c ? a + this.height : f) +
                        (c ? 1 : -1) *
                            (q ? -1 : 1) *
                            (this.axisTitleMargin || 0) +
                        [-b, b, l.f, -b][this.side];
                    c = {
                        x: c ? d + h : f + (q ? this.width : 0) + g + h,
                        y: c ? f + r - (q ? this.height : 0) + g : d + r,
                    };
                    k(this, "afterGetTitlePosition", { titlePosition: c });
                    return c;
                }
                renderMinorTick(b, c) {
                    const f = this.minorTicks;
                    f[b] || (f[b] = new H(this, b, "minor"));
                    c && f[b].isNew && f[b].render(null, !0);
                    f[b].render(null, !1, 1);
                }
                renderTick(b, c, f) {
                    const a = this.ticks;
                    if (
                        !this.isLinked ||
                        (b >= this.min && b <= this.max) ||
                        (this.grid && this.grid.isColumn)
                    )
                        a[b] || (a[b] = new H(this, b)),
                            f && a[b].isNew && a[b].render(c, !0, -1),
                            a[b].render(c);
                }
                render() {
                    const c = this,
                        f = c.chart,
                        a = c.logarithmic,
                        d = c.options,
                        n = c.isLinked,
                        e = c.tickPositions,
                        q = c.axisTitle,
                        g = c.ticks,
                        h = c.minorTicks,
                        r = c.alternateBands,
                        l = d.stackLabels,
                        m = d.alternateGridColor;
                    var p = d.crossing;
                    const D = c.tickmarkOffset,
                        t = c.axisLine,
                        w = c.showAxis,
                        K = u(f.renderer.globalAnimation);
                    let Q, G;
                    c.labelEdge.length = 0;
                    c.overlap = !1;
                    [g, h, r].forEach(function (b) {
                        P(b, function (b) {
                            b.isActive = !1;
                        });
                    });
                    if (b(p)) {
                        const b = this.isXAxis ? f.yAxis[0] : f.xAxis[0],
                            a = [1, -1, -1, 1][this.side];
                        b &&
                            ((p = b.toPixels(p, !0)),
                            c.horiz && (p = b.len - p),
                            (c.offset = a * p));
                    }
                    if (c.hasData() || n) {
                        const n = c.chart.hasRendered && c.old && b(c.old.min);
                        c.minorTickInterval &&
                            !c.categories &&
                            c.getMinorTickPositions().forEach(function (b) {
                                c.renderMinorTick(b, n);
                            });
                        e.length &&
                            (e.forEach(function (b, f) {
                                c.renderTick(b, f, n);
                            }),
                            D &&
                                (0 === c.min || c.single) &&
                                (g[-1] || (g[-1] = new H(c, -1, null, !0)),
                                g[-1].render(-1)));
                        m &&
                            e.forEach(function (b, d) {
                                G =
                                    "undefined" !== typeof e[d + 1]
                                        ? e[d + 1] + D
                                        : c.max - D;
                                0 === d % 2 &&
                                    b < c.max &&
                                    G <= c.max + (f.polar ? -D : D) &&
                                    (r[b] || (r[b] = new z.PlotLineOrBand(c)),
                                    (Q = b + D),
                                    (r[b].options = {
                                        from: a ? a.lin2log(Q) : Q,
                                        to: a ? a.lin2log(G) : G,
                                        color: m,
                                        className: "highcharts-alternate-grid",
                                    }),
                                    r[b].render(),
                                    (r[b].isActive = !0));
                            });
                        c._addedPlotLB ||
                            ((c._addedPlotLB = !0),
                            (d.plotLines || [])
                                .concat(d.plotBands || [])
                                .forEach(function (b) {
                                    c.addPlotBandOrLine(b);
                                }));
                    }
                    [g, h, r].forEach(function (b) {
                        const c = [],
                            a = K.duration;
                        P(b, function (b, f) {
                            b.isActive ||
                                (b.render(f, !1, 0),
                                (b.isActive = !1),
                                c.push(f));
                        });
                        Z(
                            function () {
                                let f = c.length;
                                for (; f--; )
                                    b[c[f]] &&
                                        !b[c[f]].isActive &&
                                        (b[c[f]].destroy(), delete b[c[f]]);
                            },
                            b !== r && f.hasRendered && a ? a : 0
                        );
                    });
                    t &&
                        (t[t.isPlaced ? "animate" : "attr"]({
                            d: this.getLinePath(t.strokeWidth()),
                        }),
                        (t.isPlaced = !0),
                        t[w ? "show" : "hide"](w));
                    q &&
                        w &&
                        (q[q.isNew ? "attr" : "animate"](c.getTitlePosition(q)),
                        (q.isNew = !1));
                    l &&
                        l.enabled &&
                        c.stacking &&
                        c.stacking.renderStackTotals();
                    c.old = {
                        len: c.len,
                        max: c.max,
                        min: c.min,
                        transA: c.transA,
                        userMax: c.userMax,
                        userMin: c.userMin,
                    };
                    c.isDirty = !1;
                    k(this, "afterRender");
                }
                redraw() {
                    this.visible &&
                        (this.render(),
                        this.plotLinesAndBands.forEach(function (b) {
                            b.render();
                        }));
                    this.series.forEach(function (b) {
                        b.isDirty = !0;
                    });
                }
                getKeepProps() {
                    return this.keepProps || Y.keepProps;
                }
                destroy(b) {
                    const c = this,
                        f = c.plotLinesAndBands,
                        a = this.eventOptions;
                    k(this, "destroy", { keepEvents: b });
                    b || X(c);
                    [c.ticks, c.minorTicks, c.alternateBands].forEach(function (
                        b
                    ) {
                        w(b);
                    });
                    if (f) for (b = f.length; b--; ) f[b].destroy();
                    "axisLine axisTitle axisGroup gridGroup labelGroup cross scrollbar"
                        .split(" ")
                        .forEach(function (b) {
                            c[b] && (c[b] = c[b].destroy());
                        });
                    for (const b in c.plotLinesAndBandsGroups)
                        c.plotLinesAndBandsGroups[b] =
                            c.plotLinesAndBandsGroups[b].destroy();
                    P(c, function (b, f) {
                        -1 === c.getKeepProps().indexOf(f) && delete c[f];
                    });
                    this.eventOptions = a;
                }
                drawCrosshair(b, c) {
                    const f = this.crosshair;
                    var a = D(f && f.snap, !0);
                    const n = this.chart;
                    let q,
                        g = this.cross;
                    k(this, "drawCrosshair", { e: b, point: c });
                    b || (b = this.cross && this.cross.e);
                    if (f && !1 !== (e(c) || !a)) {
                        a
                            ? e(c) &&
                              (q = D(
                                  "colorAxis" !== this.coll
                                      ? c.crosshairPos
                                      : null,
                                  this.isXAxis ? c.plotX : this.len - c.plotY
                              ))
                            : (q =
                                  b &&
                                  (this.horiz
                                      ? b.chartX - this.pos
                                      : this.len - b.chartY + this.pos));
                        if (e(q)) {
                            var h = {
                                value:
                                    c &&
                                    (this.isXAxis ? c.x : D(c.stackY, c.y)),
                                translatedValue: q,
                            };
                            n.polar &&
                                d(h, {
                                    isCrosshair: !0,
                                    chartX: b && b.chartX,
                                    chartY: b && b.chartY,
                                    point: c,
                                });
                            h = this.getPlotLinePath(h) || null;
                        }
                        if (!e(h)) {
                            this.hideCrosshair();
                            return;
                        }
                        a = this.categories && !this.isRadial;
                        g ||
                            ((this.cross = g =
                                n.renderer
                                    .path()
                                    .addClass(
                                        "highcharts-crosshair highcharts-crosshair-" +
                                            (a ? "category " : "thin ") +
                                            (f.className || "")
                                    )
                                    .attr({ zIndex: D(f.zIndex, 2) })
                                    .add()),
                            n.styledMode ||
                                (g
                                    .attr({
                                        stroke:
                                            f.color ||
                                            (a
                                                ? I.parse("#ccd3ff")
                                                      .setOpacity(0.25)
                                                      .get()
                                                : "#cccccc"),
                                        "stroke-width": D(f.width, 1),
                                    })
                                    .css({ "pointer-events": "none" }),
                                f.dashStyle &&
                                    g.attr({ dashstyle: f.dashStyle })));
                        g.show().attr({ d: h });
                        a &&
                            !f.width &&
                            g.attr({ "stroke-width": this.transA });
                        this.cross.e = b;
                    } else this.hideCrosshair();
                    k(this, "afterDrawCrosshair", { e: b, point: c });
                }
                hideCrosshair() {
                    this.cross && this.cross.hide();
                    k(this, "afterHideCrosshair");
                }
                hasVerticalPanning() {
                    const b = this.chart.options.chart.panning;
                    return !!(b && b.enabled && /y/.test(b.type));
                }
                update(b, f) {
                    const a = this.chart;
                    b = c(this.userOptions, b);
                    this.destroy(!0);
                    this.init(a, b);
                    a.isDirtyBox = !0;
                    D(f, !0) && a.redraw();
                }
                remove(b) {
                    const c = this.chart,
                        f = this.coll,
                        a = this.series;
                    let d = a.length;
                    for (; d--; ) a[d] && a[d].remove(!1);
                    x(c.axes, this);
                    x(c[f] || [], this);
                    c.orderItems(f);
                    this.destroy();
                    c.isDirtyBox = !0;
                    D(b, !0) && c.redraw();
                }
                setTitle(b, c) {
                    this.update({ title: b }, c);
                }
                setCategories(b, c) {
                    this.update({ categories: b }, c);
                }
            }
            Y.defaultOptions = y.defaultXAxisOptions;
            Y.keepProps =
                "coll extKey hcEvents names series userMax userMin".split(" ");
            ("");
            return Y;
        }
    );
    M(a, "Core/Axis/DateTimeAxis.js", [a["Core/Utilities.js"]], function (a) {
        const {
            addEvent: x,
            getMagnitude: I,
            normalizeTickInterval: L,
            timeUnits: C,
        } = a;
        var z;
        (function (y) {
            function B() {
                return this.chart.time.getTimeTicks.apply(
                    this.chart.time,
                    arguments
                );
            }

            function u(a) {
                "datetime" !== a.userOptions.type
                    ? (this.dateTime = void 0)
                    : this.dateTime || (this.dateTime = new l(this));
            }
            const v = [];
            y.compose = function (l) {
                a.pushUnique(v, l) &&
                    (l.keepProps.push("dateTime"),
                    (l.prototype.getTimeTicks = B),
                    x(l, "init", u));
                return l;
            };
            class l {
                constructor(a) {
                    this.axis = a;
                }
                normalizeTimeTickInterval(a, l) {
                    const m = l || [
                        [
                            "millisecond",
                            [1, 2, 5, 10, 20, 25, 50, 100, 200, 500],
                        ],
                        ["second", [1, 2, 5, 10, 15, 30]],
                        ["minute", [1, 2, 5, 10, 15, 30]],
                        ["hour", [1, 2, 3, 4, 6, 8, 12]],
                        ["day", [1, 2]],
                        ["week", [1, 2]],
                        ["month", [1, 2, 3, 4, 6]],
                        ["year", null],
                    ];
                    l = m[m.length - 1];
                    let h = C[l[0]],
                        g = l[1],
                        e;
                    for (
                        e = 0;
                        e < m.length &&
                        !((l = m[e]),
                        (h = C[l[0]]),
                        (g = l[1]),
                        m[e + 1] &&
                            a <= (h * g[g.length - 1] + C[m[e + 1][0]]) / 2);
                        e++
                    );
                    h === C.year && a < 5 * h && (g = [1, 2, 5]);
                    a = L(
                        a / h,
                        g,
                        "year" === l[0] ? Math.max(I(a / h), 1) : 1
                    );
                    return { unitRange: h, count: a, unitName: l[0] };
                }
                getXDateFormat(a, l) {
                    const { axis: m } = this,
                        h = m.chart.time;
                    return m.closestPointRange
                        ? h.getDateFormat(
                              m.closestPointRange,
                              a,
                              m.options.startOfWeek,
                              l
                          ) || h.resolveDTLFormat(l.year).main
                        : h.resolveDTLFormat(l.day).main;
                }
            }
            y.Additions = l;
        })(z || (z = {}));
        return z;
    });
    M(
        a,
        "Core/Axis/LogarithmicAxis.js",
        [a["Core/Utilities.js"]],
        function (a) {
            const { addEvent: x, normalizeTickInterval: I, pick: L } = a;
            var C;
            (function (y) {
                function H(a) {
                    let l = this.logarithmic;
                    "logarithmic" !== a.userOptions.type
                        ? (this.logarithmic = void 0)
                        : l || (this.logarithmic = new v(this));
                }

                function B() {
                    const a = this.logarithmic;
                    a &&
                        ((this.lin2val = function (l) {
                            return a.lin2log(l);
                        }),
                        (this.val2lin = function (l) {
                            return a.log2lin(l);
                        }));
                }
                const u = [];
                y.compose = function (l) {
                    a.pushUnique(u, l) &&
                        (l.keepProps.push("logarithmic"),
                        x(l, "init", H),
                        x(l, "afterInit", B));
                    return l;
                };
                class v {
                    constructor(a) {
                        this.axis = a;
                    }
                    getLogTickPositions(a, p, t, m) {
                        const h = this.axis;
                        var g = h.len,
                            e = h.options;
                        let l = [];
                        m || (this.minorAutoInterval = void 0);
                        if (0.5 <= a)
                            (a = Math.round(a)),
                                (l = h.getLinearTickPositions(a, p, t));
                        else if (0.08 <= a) {
                            e = Math.floor(p);
                            let h, w, d, k, r;
                            for (
                                g =
                                    0.3 < a
                                        ? [1, 2, 4]
                                        : 0.15 < a
                                        ? [1, 2, 4, 6, 8]
                                        : [1, 2, 3, 4, 5, 6, 7, 8, 9];
                                e < t + 1 && !r;
                                e++
                            )
                                for (w = g.length, h = 0; h < w && !r; h++)
                                    (d = this.log2lin(this.lin2log(e) * g[h])),
                                        d > p &&
                                            (!m || k <= t) &&
                                            "undefined" !== typeof k &&
                                            l.push(k),
                                        k > t && (r = !0),
                                        (k = d);
                        } else
                            (p = this.lin2log(p)),
                                (t = this.lin2log(t)),
                                (a = m
                                    ? h.getMinorTickInterval()
                                    : e.tickInterval),
                                (a = L(
                                    "auto" === a ? null : a,
                                    this.minorAutoInterval,
                                    ((e.tickPixelInterval / (m ? 5 : 1)) *
                                        (t - p)) /
                                        ((m ? g / h.tickPositions.length : g) ||
                                            1)
                                )),
                                (a = I(a)),
                                (l = h
                                    .getLinearTickPositions(a, p, t)
                                    .map(this.log2lin)),
                                m || (this.minorAutoInterval = a / 5);
                        m || (h.tickInterval = a);
                        return l;
                    }
                    lin2log(a) {
                        return Math.pow(10, a);
                    }
                    log2lin(a) {
                        return Math.log(a) / Math.LN10;
                    }
                }
                y.Additions = v;
            })(C || (C = {}));
            return C;
        }
    );
    M(
        a,
        "Core/Axis/PlotLineOrBand/PlotLineOrBandAxis.js",
        [a["Core/Utilities.js"]],
        function (a) {
            const { erase: x, extend: I, isNumber: L } = a;
            var C;
            (function (y) {
                function H(a) {
                    return this.addPlotBandOrLine(a, "plotBands");
                }

                function B(a, e) {
                    const g = this.userOptions;
                    let l = new h(this, a);
                    this.visible && (l = l.render());
                    if (l) {
                        this._addedPlotLB ||
                            ((this._addedPlotLB = !0),
                            (g.plotLines || [])
                                .concat(g.plotBands || [])
                                .forEach((a) => {
                                    this.addPlotBandOrLine(a);
                                }));
                        if (e) {
                            const h = g[e] || [];
                            h.push(a);
                            g[e] = h;
                        }
                        this.plotLinesAndBands.push(l);
                    }
                    return l;
                }

                function u(a) {
                    return this.addPlotBandOrLine(a, "plotLines");
                }

                function v(a, e, h = this.options) {
                    const g = this.getPlotLinePath({
                            value: e,
                            force: !0,
                            acrossPanes: h.acrossPanes,
                        }),
                        l = [],
                        d = this.horiz;
                    e =
                        !L(this.min) ||
                        !L(this.max) ||
                        (a < this.min && e < this.min) ||
                        (a > this.max && e > this.max);
                    a = this.getPlotLinePath({
                        value: a,
                        force: !0,
                        acrossPanes: h.acrossPanes,
                    });
                    h = 1;
                    let k;
                    if (a && g)
                        for (
                            e && ((k = a.toString() === g.toString()), (h = 0)),
                                e = 0;
                            e < a.length;
                            e += 2
                        ) {
                            const r = a[e],
                                q = a[e + 1],
                                m = g[e],
                                b = g[e + 1];
                            ("M" !== r[0] && "L" !== r[0]) ||
                                ("M" !== q[0] && "L" !== q[0]) ||
                                ("M" !== m[0] && "L" !== m[0]) ||
                                ("M" !== b[0] && "L" !== b[0]) ||
                                (d && m[1] === r[1]
                                    ? ((m[1] += h), (b[1] += h))
                                    : d ||
                                      m[2] !== r[2] ||
                                      ((m[2] += h), (b[2] += h)),
                                l.push(
                                    ["M", r[1], r[2]],
                                    ["L", q[1], q[2]],
                                    ["L", b[1], b[2]],
                                    ["L", m[1], m[2]],
                                    ["Z"]
                                ));
                            l.isFlat = k;
                        }
                    return l;
                }

                function l(a) {
                    this.removePlotBandOrLine(a);
                }

                function p(a) {
                    const e = this.plotLinesAndBands,
                        g = this.options,
                        h = this.userOptions;
                    if (e) {
                        let l = e.length;
                        for (; l--; ) e[l].id === a && e[l].destroy();
                        [
                            g.plotLines || [],
                            h.plotLines || [],
                            g.plotBands || [],
                            h.plotBands || [],
                        ].forEach(function (d) {
                            for (l = d.length; l--; )
                                (d[l] || {}).id === a && x(d, d[l]);
                        });
                    }
                }

                function t(a) {
                    this.removePlotBandOrLine(a);
                }
                const m = [];
                let h;
                y.compose = function (g, e) {
                    h || (h = g);
                    a.pushUnique(m, e) &&
                        I(e.prototype, {
                            addPlotBand: H,
                            addPlotLine: u,
                            addPlotBandOrLine: B,
                            getPlotBandPath: v,
                            removePlotBand: l,
                            removePlotLine: t,
                            removePlotBandOrLine: p,
                        });
                    return e;
                };
            })(C || (C = {}));
            return C;
        }
    );
    M(
        a,
        "Core/Axis/PlotLineOrBand/PlotLineOrBand.js",
        [
            a["Core/Axis/PlotLineOrBand/PlotLineOrBandAxis.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y) {
            const {
                arrayMax: x,
                arrayMin: L,
                defined: C,
                destroyObjectProperties: z,
                erase: H,
                fireEvent: B,
                merge: u,
                objectEach: v,
                pick: l,
            } = y;
            class p {
                static compose(l) {
                    return a.compose(p, l);
                }
                constructor(a, l) {
                    this.axis = a;
                    l && ((this.options = l), (this.id = l.id));
                }
                render() {
                    B(this, "render");
                    const a = this,
                        m = a.axis,
                        h = m.horiz;
                    var g = m.logarithmic;
                    const e = a.options,
                        p = e.color,
                        x = l(e.zIndex, 0),
                        F = e.events,
                        d = {},
                        k = m.chart.renderer;
                    let r = e.label,
                        q = a.label,
                        G = e.to,
                        b = e.from,
                        f = e.value,
                        c = a.svgElem;
                    var n = [];
                    const P = C(b) && C(G);
                    n = C(f);
                    const D = !c,
                        K = {
                            class:
                                "highcharts-plot-" +
                                (P ? "band " : "line ") +
                                (e.className || ""),
                        };
                    let X = P ? "bands" : "lines";
                    g &&
                        ((b = g.log2lin(b)),
                        (G = g.log2lin(G)),
                        (f = g.log2lin(f)));
                    m.chart.styledMode ||
                        (n
                            ? ((K.stroke = p || "#999999"),
                              (K["stroke-width"] = l(e.width, 1)),
                              e.dashStyle && (K.dashstyle = e.dashStyle))
                            : P &&
                              ((K.fill = p || "#e6e9ff"),
                              e.borderWidth &&
                                  ((K.stroke = e.borderColor),
                                  (K["stroke-width"] = e.borderWidth))));
                    d.zIndex = x;
                    X += "-" + x;
                    (g = m.plotLinesAndBandsGroups[X]) ||
                        (m.plotLinesAndBandsGroups[X] = g =
                            k
                                .g("plot-" + X)
                                .attr(d)
                                .add());
                    D && (a.svgElem = c = k.path().attr(K).add(g));
                    if (n)
                        n = m.getPlotLinePath({
                            value: f,
                            lineWidth: c.strokeWidth(),
                            acrossPanes: e.acrossPanes,
                        });
                    else if (P) n = m.getPlotBandPath(b, G, e);
                    else return;
                    !a.eventsAdded &&
                        F &&
                        (v(F, function (b, f) {
                            c.on(f, function (b) {
                                F[f].apply(a, [b]);
                            });
                        }),
                        (a.eventsAdded = !0));
                    (D || !c.d) && n && n.length
                        ? c.attr({ d: n })
                        : c &&
                          (n
                              ? (c.show(), c.animate({ d: n }))
                              : c.d &&
                                (c.hide(), q && (a.label = q = q.destroy())));
                    r &&
                    (C(r.text) || C(r.formatter)) &&
                    n &&
                    n.length &&
                    0 < m.width &&
                    0 < m.height &&
                    !n.isFlat
                        ? ((r = u(
                              {
                                  align: h && P && "center",
                                  x: h ? !P && 4 : 10,
                                  verticalAlign: !h && P && "middle",
                                  y: h ? (P ? 16 : 10) : P ? 6 : -4,
                                  rotation: h && !P && 90,
                              },
                              r
                          )),
                          this.renderLabel(r, n, P, x))
                        : q && q.hide();
                    return a;
                }
                renderLabel(a, l, h, g) {
                    const e = this.axis;
                    var m = e.chart.renderer;
                    let p = this.label;
                    p ||
                        ((this.label = p =
                            m
                                .text(this.getLabelText(a), 0, 0, a.useHTML)
                                .attr({
                                    align: a.textAlign || a.align,
                                    rotation: a.rotation,
                                    class:
                                        "highcharts-plot-" +
                                        (h ? "band" : "line") +
                                        "-label " +
                                        (a.className || ""),
                                    zIndex: g,
                                })
                                .add()),
                        e.chart.styledMode ||
                            p.css(
                                u(
                                    {
                                        fontSize: "0.8em",
                                        textOverflow: "ellipsis",
                                    },
                                    a.style
                                )
                            ));
                    g = l.xBounds || [l[0][1], l[1][1], h ? l[2][1] : l[0][1]];
                    l = l.yBounds || [l[0][2], l[1][2], h ? l[2][2] : l[0][2]];
                    h = L(g);
                    m = L(l);
                    p.align(a, !1, {
                        x: h,
                        y: m,
                        width: x(g) - h,
                        height: x(l) - m,
                    });
                    (p.alignValue && "left" !== p.alignValue) ||
                        ((a = a.clip ? e.width : e.chart.chartWidth),
                        p.css({
                            width:
                                (90 === p.rotation
                                    ? e.height - (p.alignAttr.y - e.top)
                                    : a - (p.alignAttr.x - e.left)) + "px",
                        }));
                    p.show(!0);
                }
                getLabelText(a) {
                    return C(a.formatter) ? a.formatter.call(this) : a.text;
                }
                destroy() {
                    H(this.axis.plotLinesAndBands, this);
                    delete this.axis;
                    z(this);
                }
            }
            ("");
            ("");
            return p;
        }
    );
    M(
        a,
        "Core/Tooltip.js",
        [
            a["Core/Templating.js"],
            a["Core/Globals.js"],
            a["Core/Renderer/RendererUtilities.js"],
            a["Core/Renderer/RendererRegistry.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C) {
            const { format: x } = a,
                { doc: H, isSafari: B } = y,
                { distribute: u } = I,
                {
                    addEvent: v,
                    clamp: l,
                    css: p,
                    discardElement: t,
                    extend: m,
                    fireEvent: h,
                    isArray: g,
                    isNumber: e,
                    isString: w,
                    merge: E,
                    pick: F,
                    splat: d,
                    syncTimeout: k,
                } = C;
            class r {
                constructor(a, d) {
                    this.allowShared = !0;
                    this.container = void 0;
                    this.crosshairs = [];
                    this.distance = 0;
                    this.isHidden = !0;
                    this.isSticky = !1;
                    this.now = {};
                    this.options = {};
                    this.outside = !1;
                    this.chart = a;
                    this.init(a, d);
                }
                bodyFormatter(a) {
                    return a.map(function (a) {
                        const b = a.series.tooltipOptions;
                        return (
                            b[
                                (a.point.formatPrefix || "point") + "Formatter"
                            ] || a.point.tooltipFormatter
                        ).call(
                            a.point,
                            b[(a.point.formatPrefix || "point") + "Format"] ||
                                ""
                        );
                    });
                }
                cleanSplit(a) {
                    this.chart.series.forEach(function (d) {
                        const b = d && d.tt;
                        b &&
                            (!b.isActive || a
                                ? (d.tt = b.destroy())
                                : (b.isActive = !1));
                    });
                }
                defaultFormatter(a) {
                    const k = this.points || d(this);
                    let b;
                    b = [a.tooltipFooterHeaderFormatter(k[0])];
                    b = b.concat(a.bodyFormatter(k));
                    b.push(a.tooltipFooterHeaderFormatter(k[0], !0));
                    return b;
                }
                destroy() {
                    this.label && (this.label = this.label.destroy());
                    this.split &&
                        (this.cleanSplit(!0),
                        this.tt && (this.tt = this.tt.destroy()));
                    this.renderer &&
                        ((this.renderer = this.renderer.destroy()),
                        t(this.container));
                    C.clearTimeout(this.hideTimer);
                    C.clearTimeout(this.tooltipTimeout);
                }
                getAnchor(a, k) {
                    var b = this.chart;
                    const f = b.pointer,
                        c = b.inverted,
                        n = b.plotTop;
                    b = b.plotLeft;
                    a = d(a);
                    a[0].series &&
                        a[0].series.yAxis &&
                        !a[0].series.yAxis.options.reversedStacks &&
                        (a = a.slice().reverse());
                    if (this.followPointer && k)
                        "undefined" === typeof k.chartX && (k = f.normalize(k)),
                            (a = [k.chartX - b, k.chartY - n]);
                    else if (a[0].tooltipPos) a = a[0].tooltipPos;
                    else {
                        let f = 0,
                            d = 0;
                        a.forEach(function (b) {
                            if ((b = b.pos(!0))) (f += b[0]), (d += b[1]);
                        });
                        f /= a.length;
                        d /= a.length;
                        this.shared &&
                            1 < a.length &&
                            k &&
                            (c ? (f = k.chartX) : (d = k.chartY));
                        a = [f - b, d - n];
                    }
                    return a.map(Math.round);
                }
                getClassName(a, d, b) {
                    const f = a.series,
                        c = f.options;
                    return [
                        this.options.className,
                        "highcharts-label",
                        b && "highcharts-tooltip-header",
                        d ? "highcharts-tooltip-box" : "highcharts-tooltip",
                        !b &&
                            "highcharts-color-" + F(a.colorIndex, f.colorIndex),
                        c && c.className,
                    ]
                        .filter(w)
                        .join(" ");
                }
                getLabel() {
                    const a = this,
                        d = this.chart.styledMode,
                        b = this.options,
                        f = this.split && this.allowShared,
                        c =
                            b.style.pointerEvents ||
                            (this.shouldStickOnContact() ? "auto" : "none");
                    let n,
                        k = this.chart.renderer;
                    if (this.label) {
                        var e = !this.label.hasClass("highcharts-label");
                        ((!f && e) || (f && !e)) && this.destroy();
                    }
                    if (!this.label) {
                        if (this.outside) {
                            e = this.chart.options.chart.style;
                            const b = L.getRendererType();
                            this.container = n = y.doc.createElement("div");
                            n.className = "highcharts-tooltip-container";
                            p(n, {
                                position: "absolute",
                                top: "1px",
                                pointerEvents: c,
                                zIndex: Math.max(
                                    this.options.style.zIndex || 0,
                                    ((e && e.zIndex) || 0) + 3
                                ),
                            });
                            y.doc.body.appendChild(n);
                            this.renderer = k = new b(
                                n,
                                0,
                                0,
                                e,
                                void 0,
                                void 0,
                                k.styledMode
                            );
                        }
                        f
                            ? (this.label = k.g("tooltip"))
                            : ((this.label = k
                                  .label(
                                      "",
                                      0,
                                      0,
                                      b.shape,
                                      void 0,
                                      void 0,
                                      b.useHTML,
                                      void 0,
                                      "tooltip"
                                  )
                                  .attr({
                                      padding: b.padding,
                                      r: b.borderRadius,
                                  })),
                              d ||
                                  this.label
                                      .attr({
                                          fill: b.backgroundColor,
                                          "stroke-width": b.borderWidth || 0,
                                      })
                                      .css(b.style)
                                      .css({ pointerEvents: c }));
                        if (a.outside) {
                            const b = this.label,
                                { xSetter: c, ySetter: f } = b;
                            b.xSetter = function (f) {
                                c.call(b, a.distance);
                                n.style.left = f + "px";
                            };
                            b.ySetter = function (c) {
                                f.call(b, a.distance);
                                n.style.top = c + "px";
                            };
                        }
                        this.label.attr({ zIndex: 8 }).shadow(b.shadow).add();
                    }
                    return this.label;
                }
                getPlayingField() {
                    const { body: a, documentElement: d } = H,
                        { chart: b, distance: f, outside: c } = this;
                    return {
                        width: c
                            ? Math.max(
                                  a.scrollWidth,
                                  d.scrollWidth,
                                  a.offsetWidth,
                                  d.offsetWidth,
                                  d.clientWidth
                              ) -
                              2 * f
                            : b.chartWidth,
                        height: c
                            ? Math.max(
                                  a.scrollHeight,
                                  d.scrollHeight,
                                  a.offsetHeight,
                                  d.offsetHeight,
                                  d.clientHeight
                              )
                            : b.chartHeight,
                    };
                }
                getPosition(a, d, b) {
                    const f = this.chart,
                        c = this.distance,
                        n = {},
                        k = (f.inverted && b.h) || 0,
                        e = this.outside;
                    var q = this.getPlayingField();
                    const g = q.width,
                        h = q.height,
                        r = f.pointer.getChartPosition();
                    q = (n) => {
                        const k = "x" === n;
                        return [n, k ? g : h, k ? a : d].concat(
                            e
                                ? [
                                      k ? a * r.scaleX : d * r.scaleY,
                                      k
                                          ? r.left -
                                            c +
                                            (b.plotX + f.plotLeft) * r.scaleX
                                          : r.top -
                                            c +
                                            (b.plotY + f.plotTop) * r.scaleY,
                                      0,
                                      k ? g : h,
                                  ]
                                : [
                                      k ? a : d,
                                      k
                                          ? b.plotX + f.plotLeft
                                          : b.plotY + f.plotTop,
                                      k ? f.plotLeft : f.plotTop,
                                      k
                                          ? f.plotLeft + f.plotWidth
                                          : f.plotTop + f.plotHeight,
                                  ]
                        );
                    };
                    let l = q("y"),
                        m = q("x"),
                        p;
                    q = !!b.negative;
                    !f.polar &&
                        f.hoverSeries &&
                        f.hoverSeries.yAxis &&
                        f.hoverSeries.yAxis.reversed &&
                        (q = !q);
                    const t =
                            !this.followPointer &&
                            F(b.ttBelow, !f.inverted === q),
                        w = function (b, a, f, d, q, g, h) {
                            const l = e
                                    ? "y" === b
                                        ? c * r.scaleY
                                        : c * r.scaleX
                                    : c,
                                m = (f - d) / 2,
                                p = d < q - c,
                                J = q + c + d < a,
                                D = q - l - f + m;
                            q = q + l - m;
                            if (t && J) n[b] = q;
                            else if (!t && p) n[b] = D;
                            else if (p)
                                n[b] = Math.min(h - d, 0 > D - k ? D : D - k);
                            else if (J)
                                n[b] = Math.max(g, q + k + f > a ? q : q + k);
                            else return !1;
                        },
                        G = function (b, a, f, d, k) {
                            let e;
                            k < c || k > a - c
                                ? (e = !1)
                                : (n[b] =
                                      k < f / 2
                                          ? 1
                                          : k > a - d / 2
                                          ? a - d - 2
                                          : k - f / 2);
                            return e;
                        },
                        v = function (b) {
                            const c = l;
                            l = m;
                            m = c;
                            p = b;
                        },
                        J = function () {
                            !1 !== w.apply(0, l)
                                ? !1 !== G.apply(0, m) || p || (v(!0), J())
                                : p
                                ? (n.x = n.y = 0)
                                : (v(!0), J());
                        };
                    (f.inverted || 1 < this.len) && v();
                    J();
                    return n;
                }
                hide(a) {
                    const d = this;
                    C.clearTimeout(this.hideTimer);
                    a = F(a, this.options.hideDelay);
                    this.isHidden ||
                        (this.hideTimer = k(function () {
                            d.getLabel().fadeOut(a ? void 0 : a);
                            d.isHidden = !0;
                        }, a));
                }
                init(a, d) {
                    this.chart = a;
                    this.options = d;
                    this.crosshairs = [];
                    this.now = { x: 0, y: 0 };
                    this.isHidden = !0;
                    this.split = d.split && !a.inverted && !a.polar;
                    this.shared = d.shared || this.split;
                    this.outside = F(
                        d.outside,
                        !(!a.scrollablePixelsX && !a.scrollablePixelsY)
                    );
                }
                shouldStickOnContact(a) {
                    return !(
                        this.followPointer ||
                        !this.options.stickOnContact ||
                        (a &&
                            !this.chart.pointer.inClass(
                                a.target,
                                "highcharts-tooltip"
                            ))
                    );
                }
                move(a, d, b, f) {
                    const c = this,
                        n = c.now,
                        k =
                            !1 !== c.options.animation &&
                            !c.isHidden &&
                            (1 < Math.abs(a - n.x) || 1 < Math.abs(d - n.y)),
                        e = c.followPointer || 1 < c.len;
                    m(n, {
                        x: k ? (2 * n.x + a) / 3 : a,
                        y: k ? (n.y + d) / 2 : d,
                        anchorX: e ? void 0 : k ? (2 * n.anchorX + b) / 3 : b,
                        anchorY: e ? void 0 : k ? (n.anchorY + f) / 2 : f,
                    });
                    c.getLabel().attr(n);
                    c.drawTracker();
                    k &&
                        (C.clearTimeout(this.tooltipTimeout),
                        (this.tooltipTimeout = setTimeout(function () {
                            c && c.move(a, d, b, f);
                        }, 32)));
                }
                refresh(a, k) {
                    const b = this.chart,
                        f = this.options,
                        c = b.pointer,
                        n = d(a),
                        e = n[0],
                        q = [];
                    var r = f.format,
                        l = f.formatter || this.defaultFormatter;
                    const m = this.shared,
                        p = b.styledMode;
                    let t = {};
                    if (f.enabled && e.series) {
                        C.clearTimeout(this.hideTimer);
                        this.allowShared = !(
                            !g(a) &&
                            a.series &&
                            a.series.noSharedTooltip
                        );
                        this.followPointer =
                            !this.split &&
                            e.series.tooltipOptions.followPointer;
                        a = this.getAnchor(a, k);
                        var v = a[0],
                            G = a[1];
                        m && this.allowShared
                            ? (c.applyInactiveState(n),
                              n.forEach(function (b) {
                                  b.setState("hover");
                                  q.push(b.getLabelConfig());
                              }),
                              (t = e.getLabelConfig()),
                              (t.points = q))
                            : (t = e.getLabelConfig());
                        this.len = q.length;
                        r = w(r) ? x(r, t, b) : l.call(t, this);
                        l = e.series;
                        this.distance = F(l.tooltipOptions.distance, 16);
                        if (!1 === r) this.hide();
                        else {
                            if (this.split && this.allowShared)
                                this.renderSplit(r, n);
                            else {
                                let d = v,
                                    g = G;
                                k &&
                                    c.isDirectTouch &&
                                    ((d = k.chartX - b.plotLeft),
                                    (g = k.chartY - b.plotTop));
                                if (
                                    b.polar ||
                                    !1 === l.options.clip ||
                                    n.some(
                                        (b) =>
                                            c.isDirectTouch ||
                                            b.series.shouldShowTooltip(d, g)
                                    )
                                )
                                    (k = this.getLabel()),
                                        (f.style.width && !p) ||
                                            k.css({
                                                width:
                                                    (this.outside
                                                        ? this.getPlayingField()
                                                        : b.spacingBox
                                                    ).width + "px",
                                            }),
                                        k.attr({
                                            text: r && r.join ? r.join("") : r,
                                        }),
                                        k.addClass(this.getClassName(e), !0),
                                        p ||
                                            k.attr({
                                                stroke:
                                                    f.borderColor ||
                                                    e.color ||
                                                    l.color ||
                                                    "#666666",
                                            }),
                                        this.updatePosition({
                                            plotX: v,
                                            plotY: G,
                                            negative: e.negative,
                                            ttBelow: e.ttBelow,
                                            h: a[2] || 0,
                                        });
                                else {
                                    this.hide();
                                    return;
                                }
                            }
                            this.isHidden &&
                                this.label &&
                                this.label.attr({ opacity: 1 }).show();
                            this.isHidden = !1;
                        }
                        h(this, "refresh");
                    }
                }
                renderSplit(a, d) {
                    function b(b, c, a, d, n = !0) {
                        a
                            ? ((c = S ? 0 : z),
                              (b = l(
                                  b - d / 2,
                                  J.left,
                                  J.right - d - (f.outside ? W : 0)
                              )))
                            : ((c -= da),
                              (b = n ? b - d - x : b + x),
                              (b = l(b, n ? b : J.left, J.right)));
                        return { x: b, y: c };
                    }
                    const f = this,
                        {
                            chart: c,
                            chart: {
                                chartWidth: n,
                                chartHeight: k,
                                plotHeight: e,
                                plotLeft: g,
                                plotTop: h,
                                pointer: q,
                                scrollablePixelsY: r = 0,
                                scrollablePixelsX: p,
                                scrollingContainer: {
                                    scrollLeft: t,
                                    scrollTop: v,
                                } = { scrollLeft: 0, scrollTop: 0 },
                                styledMode: G,
                            },
                            distance: x,
                            options: E,
                            options: { positioner: y },
                        } = f,
                        J =
                            f.outside && "number" !== typeof p
                                ? H.documentElement.getBoundingClientRect()
                                : {
                                      left: t,
                                      right: t + n,
                                      top: v,
                                      bottom: v + k,
                                  },
                        N = f.getLabel(),
                        O = this.renderer || c.renderer,
                        S = !(!c.xAxis[0] || !c.xAxis[0].opposite),
                        { left: W, top: ha } = q.getChartPosition();
                    let da = h + v,
                        C = 0,
                        z = e - r;
                    w(a) && (a = [!1, a]);
                    a = a.slice(0, d.length + 1).reduce(function (c, a, n) {
                        if (!1 !== a && "" !== a) {
                            n = d[n - 1] || {
                                isHeader: !0,
                                plotX: d[0].plotX,
                                plotY: e,
                                series: {},
                            };
                            const D = n.isHeader;
                            var k = D ? f : n.series,
                                q;
                            {
                                var r = n;
                                a = a.toString();
                                var m = k.tt;
                                const { isHeader: b, series: c } = r;
                                m ||
                                    ((m = {
                                        padding: E.padding,
                                        r: E.borderRadius,
                                    }),
                                    G ||
                                        ((m.fill = E.backgroundColor),
                                        (m["stroke-width"] =
                                            null !== (q = E.borderWidth) &&
                                            void 0 !== q
                                                ? q
                                                : 1)),
                                    (m = O.label(
                                        "",
                                        0,
                                        0,
                                        E[b ? "headerShape" : "shape"],
                                        void 0,
                                        void 0,
                                        E.useHTML
                                    )
                                        .addClass(f.getClassName(r, !0, b))
                                        .attr(m)
                                        .add(N)));
                                m.isActive = !0;
                                m.attr({ text: a });
                                G ||
                                    m
                                        .css(E.style)
                                        .attr({
                                            stroke:
                                                E.borderColor ||
                                                r.color ||
                                                c.color ||
                                                "#333333",
                                        });
                                q = m;
                            }
                            q = k.tt = q;
                            r = q.getBBox();
                            k = r.width + q.strokeWidth();
                            D && ((C = r.height), (z += C), S && (da -= C));
                            {
                                const {
                                    isHeader: b,
                                    plotX: c = 0,
                                    plotY: f = 0,
                                    series: d,
                                } = n;
                                if (b) {
                                    a = g + c;
                                    var p = h + e / 2;
                                } else {
                                    const { xAxis: b, yAxis: n } = d;
                                    a = b.pos + l(c, -x, b.len + x);
                                    d.shouldShowTooltip(0, n.pos - h + f, {
                                        ignoreX: !0,
                                    }) && (p = n.pos + f);
                                }
                                a = l(a, J.left - x, J.right + x);
                                p = { anchorX: a, anchorY: p };
                            }
                            const { anchorX: t, anchorY: Q } = p;
                            "number" === typeof Q
                                ? ((p = r.height + 1),
                                  (r = y ? y.call(f, k, p, n) : b(t, Q, D, k)),
                                  c.push({
                                      align: y ? 0 : void 0,
                                      anchorX: t,
                                      anchorY: Q,
                                      boxWidth: k,
                                      point: n,
                                      rank: F(r.rank, D ? 1 : 0),
                                      size: p,
                                      target: r.y,
                                      tt: q,
                                      x: r.x,
                                  }))
                                : (q.isActive = !1);
                        }
                        return c;
                    }, []);
                    !y &&
                        a.some((b) => {
                            var { outside: c } = f;
                            c = (c ? W : 0) + b.anchorX;
                            return c < J.left && c + b.boxWidth < J.right
                                ? !0
                                : c < W - J.left + b.boxWidth &&
                                      J.right - c > c;
                        }) &&
                        (a = a.map((c) => {
                            const { x: a, y: f } = b(
                                c.anchorX,
                                c.anchorY,
                                c.point.isHeader,
                                c.boxWidth,
                                !1
                            );
                            return m(c, { target: f, x: a });
                        }));
                    f.cleanSplit();
                    u(a, z);
                    var ca = W,
                        L = W;
                    a.forEach(function (b) {
                        const { x: c, boxWidth: a, isHeader: d } = b;
                        d ||
                            (f.outside && W + c < ca && (ca = W + c),
                            !d && f.outside && ca + a > L && (L = W + c));
                    });
                    a.forEach(function (b) {
                        const {
                                x: c,
                                anchorX: a,
                                anchorY: d,
                                pos: n,
                                point: { isHeader: k },
                            } = b,
                            e = {
                                visibility:
                                    "undefined" === typeof n
                                        ? "hidden"
                                        : "inherit",
                                x: c,
                                y: (n || 0) + da,
                                anchorX: a,
                                anchorY: d,
                            };
                        if (f.outside && c < a) {
                            const b = W - ca;
                            0 < b &&
                                (k || ((e.x = c + b), (e.anchorX = a + b)),
                                k &&
                                    ((e.x = (L - ca) / 2),
                                    (e.anchorX = a + b)));
                        }
                        b.tt.attr(e);
                    });
                    const { container: R, outside: Q, renderer: la } = f;
                    if (Q && R && la) {
                        const { width: b, height: c, x: a, y: f } = N.getBBox();
                        la.setSize(b + a, c + f, !1);
                        R.style.left = ca + "px";
                        R.style.top = ha + "px";
                    }
                    B && N.attr({ opacity: 1 === N.opacity ? 0.999 : 1 });
                }
                drawTracker() {
                    if (this.shouldStickOnContact()) {
                        var a = this.chart,
                            d = this.label,
                            b = this.shared ? a.hoverPoints : a.hoverPoint;
                        if (d && b) {
                            var f = { x: 0, y: 0, width: 0, height: 0 };
                            b = this.getAnchor(b);
                            var c = d.getBBox();
                            b[0] += a.plotLeft - d.translateX;
                            b[1] += a.plotTop - d.translateY;
                            f.x = Math.min(0, b[0]);
                            f.y = Math.min(0, b[1]);
                            f.width =
                                0 > b[0]
                                    ? Math.max(Math.abs(b[0]), c.width - b[0])
                                    : Math.max(Math.abs(b[0]), c.width);
                            f.height =
                                0 > b[1]
                                    ? Math.max(
                                          Math.abs(b[1]),
                                          c.height - Math.abs(b[1])
                                      )
                                    : Math.max(Math.abs(b[1]), c.height);
                            this.tracker
                                ? this.tracker.attr(f)
                                : ((this.tracker = d.renderer
                                      .rect(f)
                                      .addClass("highcharts-tracker")
                                      .add(d)),
                                  a.styledMode ||
                                      this.tracker.attr({
                                          fill: "rgba(0,0,0,0)",
                                      }));
                        }
                    } else
                        this.tracker && (this.tracker = this.tracker.destroy());
                }
                styledModeFormat(a) {
                    return a
                        .replace(
                            'style="font-size: 0.8em"',
                            'class="highcharts-header"'
                        )
                        .replace(
                            /style="color:{(point|series)\.color}"/g,
                            'class="highcharts-color-{$1.colorIndex} {series.options.className} {point.options.className}"'
                        );
                }
                tooltipFooterHeaderFormatter(a, d) {
                    const b = a.series,
                        f = b.tooltipOptions;
                    var c = b.xAxis;
                    const n = c && c.dateTime;
                    c = { isFooter: d, labelConfig: a };
                    let k = f.xDateFormat,
                        g = f[d ? "footerFormat" : "headerFormat"];
                    h(this, "headerFormatter", c, function (c) {
                        n &&
                            !k &&
                            e(a.key) &&
                            (k = n.getXDateFormat(
                                a.key,
                                f.dateTimeLabelFormats
                            ));
                        n &&
                            k &&
                            (
                                (a.point && a.point.tooltipDateKeys) || ["key"]
                            ).forEach(function (b) {
                                g = g.replace(
                                    "{point." + b + "}",
                                    "{point." + b + ":" + k + "}"
                                );
                            });
                        b.chart.styledMode && (g = this.styledModeFormat(g));
                        c.text = x(g, { point: a, series: b }, this.chart);
                    });
                    return c.text;
                }
                update(a) {
                    this.destroy();
                    this.init(this.chart, E(!0, this.options, a));
                }
                updatePosition(a) {
                    const { chart: d, distance: b, options: f } = this;
                    var c = d.pointer;
                    const n = this.getLabel(),
                        {
                            left: k,
                            top: e,
                            scaleX: g,
                            scaleY: h,
                        } = c.getChartPosition();
                    c = (f.positioner || this.getPosition).call(
                        this,
                        n.width,
                        n.height,
                        a
                    );
                    let q = (a.plotX || 0) + d.plotLeft;
                    a = (a.plotY || 0) + d.plotTop;
                    let r;
                    if (this.outside) {
                        f.positioner && ((c.x += k - b), (c.y += e - b));
                        r = (f.borderWidth || 0) + 2 * b;
                        this.renderer.setSize(n.width + r, n.height + r, !1);
                        if (1 !== g || 1 !== h)
                            p(this.container, {
                                transform: `scale(${g}, ${h})`,
                            }),
                                (q *= g),
                                (a *= h);
                        q += k - c.x;
                        a += e - c.y;
                    }
                    this.move(Math.round(c.x), Math.round(c.y || 0), q, a);
                }
            }
            (function (a) {
                const d = [];
                a.compose = function (b) {
                    C.pushUnique(d, b) &&
                        v(b, "afterInit", function () {
                            const b = this.chart;
                            b.options.tooltip &&
                                (b.tooltip = new a(b, b.options.tooltip));
                        });
                };
            })(r || (r = {}));
            ("");
            return r;
        }
    );
    M(
        a,
        "Core/Series/Point.js",
        [
            a["Core/Renderer/HTML/AST.js"],
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Defaults.js"],
            a["Core/Templating.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C) {
            const { animObject: x } = y,
                { defaultOptions: H } = I,
                { format: B } = L,
                {
                    addEvent: u,
                    defined: v,
                    erase: l,
                    extend: p,
                    fireEvent: t,
                    getNestedProperty: m,
                    isArray: h,
                    isFunction: g,
                    isNumber: e,
                    isObject: w,
                    merge: E,
                    objectEach: F,
                    pick: d,
                    syncTimeout: k,
                    removeEvent: r,
                    uniqueKey: q,
                } = C;
            class G {
                constructor() {
                    this.category = void 0;
                    this.destroyed = !1;
                    this.formatPrefix = "point";
                    this.id = void 0;
                    this.isNull = !1;
                    this.percentage = this.options = this.name = void 0;
                    this.selected = !1;
                    this.total = this.shapeArgs = this.series = void 0;
                    this.visible = !0;
                    this.x = void 0;
                }
                animateBeforeDestroy() {
                    const b = this,
                        a = { x: b.startXPos, opacity: 0 },
                        c = b.getGraphicalProps();
                    c.singular.forEach(function (c) {
                        b[c] = b[c].animate(
                            "dataLabel" === c
                                ? {
                                      x: b[c].startXPos,
                                      y: b[c].startYPos,
                                      opacity: 0,
                                  }
                                : a
                        );
                    });
                    c.plural.forEach(function (c) {
                        b[c].forEach(function (c) {
                            c.element &&
                                c.animate(
                                    p(
                                        { x: b.startXPos },
                                        c.startYPos
                                            ? { x: c.startXPos, y: c.startYPos }
                                            : {}
                                    )
                                );
                        });
                    });
                }
                applyOptions(b, a) {
                    const c = this.series,
                        f = c.options.pointValKey || c.pointValKey;
                    b = G.prototype.optionsToObject.call(this, b);
                    p(this, b);
                    this.options = this.options ? p(this.options, b) : b;
                    b.group && delete this.group;
                    b.dataLabels && delete this.dataLabels;
                    f && (this.y = G.prototype.getNestedProperty.call(this, f));
                    this.formatPrefix = (this.isNull =
                        this.isValid && !this.isValid())
                        ? "null"
                        : "point";
                    this.selected && (this.state = "select");
                    "name" in this &&
                        "undefined" === typeof a &&
                        c.xAxis &&
                        c.xAxis.hasNames &&
                        (this.x = c.xAxis.nameToX(this));
                    "undefined" === typeof this.x && c
                        ? (this.x =
                              "undefined" === typeof a ? c.autoIncrement() : a)
                        : e(b.x) &&
                          c.options.relativeXValue &&
                          (this.x = c.autoIncrement(b.x));
                    return this;
                }
                destroy() {
                    if (!this.destroyed) {
                        const a = this;
                        var b = a.series;
                        const c = b.chart;
                        b = b.options.dataSorting;
                        const d = c.hoverPoints,
                            e = x(a.series.chart.renderer.globalAnimation),
                            g = () => {
                                if (
                                    a.graphic ||
                                    a.graphics ||
                                    a.dataLabel ||
                                    a.dataLabels
                                )
                                    r(a), a.destroyElements();
                                for (const b in a) delete a[b];
                            };
                        a.legendItem && c.legend.destroyItem(a);
                        d &&
                            (a.setState(),
                            l(d, a),
                            d.length || (c.hoverPoints = null));
                        if (a === c.hoverPoint) a.onMouseOut();
                        b && b.enabled
                            ? (this.animateBeforeDestroy(), k(g, e.duration))
                            : g();
                        c.pointCount--;
                    }
                    this.destroyed = !0;
                }
                destroyElements(b) {
                    const a = this;
                    b = a.getGraphicalProps(b);
                    b.singular.forEach(function (b) {
                        a[b] = a[b].destroy();
                    });
                    b.plural.forEach(function (b) {
                        a[b].forEach(function (b) {
                            b && b.element && b.destroy();
                        });
                        delete a[b];
                    });
                }
                firePointEvent(b, a, c) {
                    const f = this,
                        d = this.series.options;
                    (d.point.events[b] ||
                        (f.options &&
                            f.options.events &&
                            f.options.events[b])) &&
                        f.importEvents();
                    "click" === b &&
                        d.allowPointSelect &&
                        (c = function (b) {
                            f.select &&
                                f.select(
                                    null,
                                    b.ctrlKey || b.metaKey || b.shiftKey
                                );
                        });
                    t(f, b, a, c);
                }
                getClassName() {
                    return (
                        "highcharts-point" +
                        (this.selected ? " highcharts-point-select" : "") +
                        (this.negative ? " highcharts-negative" : "") +
                        (this.isNull ? " highcharts-null-point" : "") +
                        ("undefined" !== typeof this.colorIndex
                            ? " highcharts-color-" + this.colorIndex
                            : "") +
                        (this.options.className
                            ? " " + this.options.className
                            : "") +
                        (this.zone && this.zone.className
                            ? " " +
                              this.zone.className.replace(
                                  "highcharts-negative",
                                  ""
                              )
                            : "")
                    );
                }
                getGraphicalProps(b) {
                    const a = this,
                        c = [],
                        d = { singular: [], plural: [] };
                    let k, e;
                    b = b || { graphic: 1, dataLabel: 1 };
                    b.graphic && c.push("graphic");
                    b.dataLabel &&
                        c.push(
                            "dataLabel",
                            "dataLabelPath",
                            "dataLabelUpper",
                            "connector"
                        );
                    for (e = c.length; e--; )
                        (k = c[e]), a[k] && d.singular.push(k);
                    ["graphic", "dataLabel", "connector"].forEach(function (c) {
                        const f = c + "s";
                        b[c] && a[f] && d.plural.push(f);
                    });
                    return d;
                }
                getLabelConfig() {
                    return {
                        x: this.category,
                        y: this.y,
                        color: this.color,
                        colorIndex: this.colorIndex,
                        key: this.name || this.category,
                        series: this.series,
                        point: this,
                        percentage: this.percentage,
                        total: this.total || this.stackTotal,
                    };
                }
                getNestedProperty(b) {
                    if (b)
                        return 0 === b.indexOf("custom.")
                            ? m(b, this.options)
                            : this[b];
                }
                getZone() {
                    var b = this.series;
                    const a = b.zones;
                    b = b.zoneAxis || "y";
                    let c,
                        d = 0;
                    for (c = a[d]; this[b] >= c.value; ) c = a[++d];
                    this.nonZonedColor || (this.nonZonedColor = this.color);
                    this.color =
                        c && c.color && !this.options.color
                            ? c.color
                            : this.nonZonedColor;
                    return c;
                }
                hasNewShapeType() {
                    return (
                        (this.graphic &&
                            (this.graphic.symbolName ||
                                this.graphic.element.nodeName)) !==
                        this.shapeType
                    );
                }
                init(b, a, c) {
                    this.series = b;
                    this.applyOptions(a, c);
                    this.id = v(this.id) ? this.id : q();
                    this.resolveColor();
                    b.chart.pointCount++;
                    t(this, "afterInit");
                    return this;
                }
                isValid() {
                    return null !== this.x && e(this.y);
                }
                optionsToObject(b) {
                    var a = this.series;
                    const c = a.options.keys,
                        d = c || a.pointArrayMap || ["y"],
                        k = d.length;
                    let g = {},
                        q = 0,
                        r = 0;
                    if (e(b) || null === b) g[d[0]] = b;
                    else if (h(b))
                        for (
                            !c &&
                            b.length > k &&
                            ((a = typeof b[0]),
                            "string" === a
                                ? (g.name = b[0])
                                : "number" === a && (g.x = b[0]),
                            q++);
                            r < k;

                        )
                            (c && "undefined" === typeof b[q]) ||
                                (0 < d[r].indexOf(".")
                                    ? G.prototype.setNestedProperty(
                                          g,
                                          b[q],
                                          d[r]
                                      )
                                    : (g[d[r]] = b[q])),
                                q++,
                                r++;
                    else
                        "object" === typeof b &&
                            ((g = b),
                            b.dataLabels && (a._hasPointLabels = !0),
                            b.marker && (a._hasPointMarkers = !0));
                    return g;
                }
                pos(b, a = this.plotY) {
                    if (!this.destroyed) {
                        const { plotX: c, series: f } = this,
                            { chart: d, xAxis: k, yAxis: g } = f;
                        let h = 0,
                            q = 0;
                        if (e(c) && e(a))
                            return (
                                b &&
                                    ((h = k ? k.pos : d.plotLeft),
                                    (q = g ? g.pos : d.plotTop)),
                                d.inverted && k && g
                                    ? [g.len - a + q, k.len - c + h]
                                    : [c + h, a + q]
                            );
                    }
                }
                resolveColor() {
                    const b = this.series;
                    var a = b.chart.styledMode;
                    let c;
                    var k = b.chart.options.chart.colorCount;
                    delete this.nonZonedColor;
                    b.options.colorByPoint
                        ? (a ||
                              ((k = b.options.colors || b.chart.options.colors),
                              (c = k[b.colorCounter]),
                              (k = k.length)),
                          (a = b.colorCounter),
                          b.colorCounter++,
                          b.colorCounter === k && (b.colorCounter = 0))
                        : (a || (c = b.color), (a = b.colorIndex));
                    this.colorIndex = d(this.options.colorIndex, a);
                    this.color = d(this.options.color, c);
                }
                setNestedProperty(b, a, c) {
                    c.split(".").reduce(function (b, c, f, d) {
                        b[c] = d.length - 1 === f ? a : w(b[c], !0) ? b[c] : {};
                        return b[c];
                    }, b);
                    return b;
                }
                shouldDraw() {
                    return !this.isNull;
                }
                tooltipFormatter(b) {
                    const a = this.series,
                        c = a.tooltipOptions,
                        k = d(c.valueDecimals, ""),
                        e = c.valuePrefix || "",
                        g = c.valueSuffix || "";
                    a.chart.styledMode &&
                        (b = a.chart.tooltip.styledModeFormat(b));
                    (a.pointArrayMap || ["y"]).forEach(function (c) {
                        c = "{point." + c;
                        if (e || g)
                            b = b.replace(
                                RegExp(c + "}", "g"),
                                e + c + "}" + g
                            );
                        b = b.replace(
                            RegExp(c + "}", "g"),
                            c + ":,." + k + "f}"
                        );
                    });
                    return B(b, { point: this, series: this.series }, a.chart);
                }
                update(b, a, c, k) {
                    function f() {
                        n.applyOptions(b);
                        var f = g && n.hasMockGraphic;
                        f = null === n.y ? !f : f;
                        g &&
                            f &&
                            ((n.graphic = g.destroy()),
                            delete n.hasMockGraphic);
                        w(b, !0) &&
                            (g &&
                                g.element &&
                                b &&
                                b.marker &&
                                "undefined" !== typeof b.marker.symbol &&
                                (n.graphic = g.destroy()),
                            b &&
                                b.dataLabels &&
                                n.dataLabel &&
                                (n.dataLabel = n.dataLabel.destroy()),
                            n.connector &&
                                (n.connector = n.connector.destroy()));
                        r = n.index;
                        e.updateParallelArrays(n, r);
                        q.data[r] =
                            w(q.data[r], !0) || w(b, !0)
                                ? n.options
                                : d(b, q.data[r]);
                        e.isDirty = e.isDirtyData = !0;
                        !e.fixedBox &&
                            e.hasCartesianSeries &&
                            (h.isDirtyBox = !0);
                        "point" === q.legendType && (h.isDirtyLegend = !0);
                        a && h.redraw(c);
                    }
                    const n = this,
                        e = n.series,
                        g = n.graphic,
                        h = e.chart,
                        q = e.options;
                    let r;
                    a = d(a, !0);
                    !1 === k
                        ? f()
                        : n.firePointEvent("update", { options: b }, f);
                }
                remove(b, a) {
                    this.series.removePoint(
                        this.series.data.indexOf(this),
                        b,
                        a
                    );
                }
                select(b, a) {
                    const c = this,
                        f = c.series,
                        k = f.chart;
                    this.selectedStaging = b = d(b, !c.selected);
                    c.firePointEvent(
                        b ? "select" : "unselect",
                        { accumulate: a },
                        function () {
                            c.selected = c.options.selected = b;
                            f.options.data[f.data.indexOf(c)] = c.options;
                            c.setState(b && "select");
                            a ||
                                k.getSelectedPoints().forEach(function (b) {
                                    const a = b.series;
                                    b.selected &&
                                        b !== c &&
                                        ((b.selected = b.options.selected = !1),
                                        (a.options.data[a.data.indexOf(b)] =
                                            b.options),
                                        b.setState(
                                            k.hoverPoints &&
                                                a.options.inactiveOtherPoints
                                                ? "inactive"
                                                : ""
                                        ),
                                        b.firePointEvent("unselect"));
                                });
                        }
                    );
                    delete this.selectedStaging;
                }
                onMouseOver(b) {
                    const a = this.series.chart,
                        c = a.pointer;
                    b = b
                        ? c.normalize(b)
                        : c.getChartCoordinatesFromPoint(this, a.inverted);
                    c.runPointActions(b, this);
                }
                onMouseOut() {
                    const b = this.series.chart;
                    this.firePointEvent("mouseOut");
                    this.series.options.inactiveOtherPoints ||
                        (b.hoverPoints || []).forEach(function (b) {
                            b.setState();
                        });
                    b.hoverPoints = b.hoverPoint = null;
                }
                importEvents() {
                    if (!this.hasImportedEvents) {
                        const b = this,
                            a = E(b.series.options.point, b.options).events;
                        b.events = a;
                        F(a, function (c, a) {
                            g(c) && u(b, a, c);
                        });
                        this.hasImportedEvents = !0;
                    }
                }
                setState(b, f) {
                    const c = this.series;
                    var k = this.state,
                        g = c.options.states[b || "normal"] || {},
                        h = H.plotOptions[c.type].marker && c.options.marker;
                    const q = h && !1 === h.enabled,
                        r = (h && h.states && h.states[b || "normal"]) || {},
                        l = !1 === r.enabled,
                        m = this.marker || {},
                        w = c.chart,
                        v = h && c.markerAttribs;
                    let G = c.halo;
                    var u;
                    let x;
                    var E = c.stateMarkerGraphic;
                    b = b || "";
                    if (
                        !(
                            (b === this.state && !f) ||
                            (this.selected && "select" !== b) ||
                            !1 === g.enabled ||
                            (b && (l || (q && !1 === r.enabled))) ||
                            (b &&
                                m.states &&
                                m.states[b] &&
                                !1 === m.states[b].enabled)
                        )
                    ) {
                        this.state = b;
                        v && (u = c.markerAttribs(this, b));
                        if (this.graphic && !this.hasMockGraphic) {
                            k &&
                                this.graphic.removeClass(
                                    "highcharts-point-" + k
                                );
                            b && this.graphic.addClass("highcharts-point-" + b);
                            if (!w.styledMode) {
                                k = c.pointAttribs(this, b);
                                x = d(w.options.chart.animation, g.animation);
                                const a = k.opacity;
                                c.options.inactiveOtherPoints &&
                                    e(a) &&
                                    ((this.dataLabels || []).forEach(function (
                                        b
                                    ) {
                                        b &&
                                            !b.hasClass(
                                                "highcharts-data-label-hidden"
                                            ) &&
                                            b.animate({ opacity: a }, x);
                                    }),
                                    this.connector &&
                                        this.connector.animate(
                                            { opacity: a },
                                            x
                                        ));
                                this.graphic.animate(k, x);
                            }
                            u &&
                                this.graphic.animate(
                                    u,
                                    d(
                                        w.options.chart.animation,
                                        r.animation,
                                        h.animation
                                    )
                                );
                            E && E.hide();
                        } else {
                            if (b && r) {
                                h = m.symbol || c.symbol;
                                E && E.currentSymbol !== h && (E = E.destroy());
                                if (u)
                                    if (E)
                                        E[f ? "animate" : "attr"]({
                                            x: u.x,
                                            y: u.y,
                                        });
                                    else
                                        h &&
                                            ((c.stateMarkerGraphic = E =
                                                w.renderer
                                                    .symbol(
                                                        h,
                                                        u.x,
                                                        u.y,
                                                        u.width,
                                                        u.height
                                                    )
                                                    .add(c.markerGroup)),
                                            (E.currentSymbol = h));
                                !w.styledMode &&
                                    E &&
                                    "inactive" !== this.state &&
                                    E.attr(c.pointAttribs(this, b));
                            }
                            E &&
                                (E[b && this.isInside ? "show" : "hide"](),
                                (E.element.point = this),
                                E.addClass(this.getClassName(), !0));
                        }
                        g = g.halo;
                        u =
                            ((E = this.graphic || E) && E.visibility) ||
                            "inherit";
                        g && g.size && E && "hidden" !== u && !this.isCluster
                            ? (G ||
                                  (c.halo = G =
                                      w.renderer.path().add(E.parentGroup)),
                              G.show()[f ? "animate" : "attr"]({
                                  d: this.haloPath(g.size),
                              }),
                              G.attr({
                                  class:
                                      "highcharts-halo highcharts-color-" +
                                      d(this.colorIndex, c.colorIndex) +
                                      (this.className
                                          ? " " + this.className
                                          : ""),
                                  visibility: u,
                                  zIndex: -1,
                              }),
                              (G.point = this),
                              w.styledMode ||
                                  G.attr(
                                      p(
                                          {
                                              fill: this.color || c.color,
                                              "fill-opacity": g.opacity,
                                          },
                                          a.filterUserAttributes(
                                              g.attributes || {}
                                          )
                                      )
                                  ))
                            : G &&
                              G.point &&
                              G.point.haloPath &&
                              G.animate(
                                  { d: G.point.haloPath(0) },
                                  null,
                                  G.hide
                              );
                        t(this, "afterSetState", { state: b });
                    }
                }
                haloPath(b) {
                    const a = this.pos();
                    return a
                        ? this.series.chart.renderer.symbols.circle(
                              Math.floor(a[0]) - b,
                              a[1] - b,
                              2 * b,
                              2 * b
                          )
                        : [];
                }
            }
            ("");
            return G;
        }
    );
    M(
        a,
        "Core/Pointer.js",
        [
            a["Core/Color/Color.js"],
            a["Core/Globals.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { parse: x } = a,
                { charts: C, noop: z } = y,
                {
                    addEvent: H,
                    attr: B,
                    css: u,
                    defined: v,
                    extend: l,
                    find: p,
                    fireEvent: t,
                    isNumber: m,
                    isObject: h,
                    objectEach: g,
                    offset: e,
                    pick: w,
                    splat: E,
                } = I;
            class F {
                constructor(a, k) {
                    this.lastValidTouch = {};
                    this.pinchDown = [];
                    this.runChartClick = !1;
                    this.eventsToUnbind = [];
                    this.chart = a;
                    this.hasDragged = !1;
                    this.options = k;
                    this.init(a, k);
                }
                applyInactiveState(a) {
                    let d = [],
                        e;
                    (a || []).forEach(function (a) {
                        e = a.series;
                        d.push(e);
                        e.linkedParent && d.push(e.linkedParent);
                        e.linkedSeries && (d = d.concat(e.linkedSeries));
                        e.navigatorSeries && d.push(e.navigatorSeries);
                    });
                    this.chart.series.forEach(function (a) {
                        -1 === d.indexOf(a)
                            ? a.setState("inactive", !0)
                            : a.options.inactiveOtherPoints &&
                              a.setAllPointsToState("inactive");
                    });
                }
                destroy() {
                    const a = this;
                    this.eventsToUnbind.forEach((a) => a());
                    this.eventsToUnbind = [];
                    y.chartCount ||
                        (F.unbindDocumentMouseUp &&
                            (F.unbindDocumentMouseUp =
                                F.unbindDocumentMouseUp()),
                        F.unbindDocumentTouchEnd &&
                            (F.unbindDocumentTouchEnd =
                                F.unbindDocumentTouchEnd()));
                    clearInterval(a.tooltipTimeout);
                    g(a, function (d, e) {
                        a[e] = void 0;
                    });
                }
                getSelectionMarkerAttrs(a, k) {
                    const d = {
                        args: { chartX: a, chartY: k },
                        attrs: {},
                        shapeType: "rect",
                    };
                    t(this, "getSelectionMarkerAttrs", d, (d) => {
                        const {
                            chart: e,
                            mouseDownX: b = 0,
                            mouseDownY: f = 0,
                            zoomHor: c,
                            zoomVert: n,
                        } = this;
                        d = d.attrs;
                        let g;
                        d.x = e.plotLeft;
                        d.y = e.plotTop;
                        d.width = c ? 1 : e.plotWidth;
                        d.height = n ? 1 : e.plotHeight;
                        c &&
                            ((g = a - b),
                            (d.width = Math.abs(g)),
                            (d.x = (0 < g ? 0 : g) + b));
                        n &&
                            ((g = k - f),
                            (d.height = Math.abs(g)),
                            (d.y = (0 < g ? 0 : g) + f));
                    });
                    return d;
                }
                drag(a) {
                    const d = this.chart,
                        e = d.options.chart;
                    var g = d.plotLeft;
                    const l = d.plotTop,
                        b = d.plotWidth,
                        f = d.plotHeight,
                        c = this.mouseDownX || 0,
                        n = this.mouseDownY || 0,
                        m = h(e.panning)
                            ? e.panning && e.panning.enabled
                            : e.panning,
                        p = e.panKey && a[e.panKey + "Key"];
                    let t = a.chartX,
                        w = a.chartY,
                        v = this.selectionMarker;
                    if (!v || !v.touch)
                        if (
                            (t < g ? (t = g) : t > g + b && (t = g + b),
                            w < l ? (w = l) : w > l + f && (w = l + f),
                            (this.hasDragged = Math.sqrt(
                                Math.pow(c - t, 2) + Math.pow(n - w, 2)
                            )),
                            10 < this.hasDragged)
                        ) {
                            g = d.isInsidePlot(c - g, n - l, {
                                visiblePlotOnly: !0,
                            });
                            const { shapeType: b, attrs: f } =
                                this.getSelectionMarkerAttrs(t, w);
                            (!d.hasCartesianSeries && !d.mapView) ||
                                (!this.zoomX && !this.zoomY) ||
                                !g ||
                                p ||
                                v ||
                                ((this.selectionMarker = v = d.renderer[b]()),
                                v
                                    .attr({
                                        class: "highcharts-selection-marker",
                                        zIndex: 7,
                                    })
                                    .add(),
                                d.styledMode ||
                                    v.attr({
                                        fill:
                                            e.selectionMarkerFill ||
                                            x("#334eff").setOpacity(0.25).get(),
                                    }));
                            v && v.attr(f);
                            g && !v && m && d.pan(a, e.panning);
                        }
                }
                dragStart(a) {
                    const d = this.chart;
                    d.mouseIsDown = a.type;
                    d.cancelClick = !1;
                    d.mouseDownX = this.mouseDownX = a.chartX;
                    d.mouseDownY = this.mouseDownY = a.chartY;
                }
                getSelectionBox(a) {
                    const d = { args: { marker: a }, result: {} };
                    t(this, "getSelectionBox", d, (d) => {
                        d.result = {
                            x: a.attr ? +a.attr("x") : a.x,
                            y: a.attr ? +a.attr("y") : a.y,
                            width: a.attr ? a.attr("width") : a.width,
                            height: a.attr ? a.attr("height") : a.height,
                        };
                    });
                    return d.result;
                }
                drop(a) {
                    const d = this,
                        e = this.chart,
                        g = this.hasPinched;
                    if (this.selectionMarker) {
                        const {
                                x: k,
                                y: b,
                                width: f,
                                height: c,
                            } = this.getSelectionBox(this.selectionMarker),
                            n = {
                                originalEvent: a,
                                xAxis: [],
                                yAxis: [],
                                x: k,
                                y: b,
                                width: f,
                                height: c,
                            };
                        let h = !!e.mapView;
                        if (this.hasDragged || g)
                            e.axes.forEach(function (e) {
                                if (
                                    e.zoomEnabled &&
                                    v(e.min) &&
                                    (g ||
                                        d[
                                            { xAxis: "zoomX", yAxis: "zoomY" }[
                                                e.coll
                                            ]
                                        ]) &&
                                    m(k) &&
                                    m(b) &&
                                    m(f) &&
                                    m(c)
                                ) {
                                    var q = e.horiz;
                                    const d =
                                            "touchend" === a.type
                                                ? e.minPixelPadding
                                                : 0,
                                        g = e.toValue((q ? k : b) + d);
                                    q = e.toValue((q ? k + f : b + c) - d);
                                    n[e.coll].push({
                                        axis: e,
                                        min: Math.min(g, q),
                                        max: Math.max(g, q),
                                    });
                                    h = !0;
                                }
                            }),
                                h &&
                                    t(e, "selection", n, function (b) {
                                        e.zoom(
                                            l(b, g ? { animation: !1 } : null)
                                        );
                                    });
                        m(e.index) &&
                            (this.selectionMarker =
                                this.selectionMarker.destroy());
                        g && this.scaleGroups();
                    }
                    e &&
                        m(e.index) &&
                        (u(e.container, { cursor: e._cursor }),
                        (e.cancelClick = 10 < this.hasDragged),
                        (e.mouseIsDown =
                            this.hasDragged =
                            this.hasPinched =
                                !1),
                        (this.pinchDown = []));
                }
                findNearestKDPoint(a, k, e) {
                    let d;
                    a.forEach(function (a) {
                        var b =
                            !(a.noSharedTooltip && k) &&
                            0 > a.options.findNearestPointBy.indexOf("y");
                        a = a.searchPoint(e, b);
                        if ((b = h(a, !0) && a.series) && !(b = !h(d, !0))) {
                            {
                                b = d.distX - a.distX;
                                const f = d.dist - a.dist,
                                    c =
                                        (a.series.group &&
                                            a.series.group.zIndex) -
                                        (d.series.group &&
                                            d.series.group.zIndex);
                                b =
                                    0 !== b && k
                                        ? b
                                        : 0 !== f
                                        ? f
                                        : 0 !== c
                                        ? c
                                        : d.series.index > a.series.index
                                        ? -1
                                        : 1;
                            }
                            b = 0 < b;
                        }
                        b && (d = a);
                    });
                    return d;
                }
                getChartCoordinatesFromPoint(a, k) {
                    var d = a.series;
                    const e = d.xAxis;
                    d = d.yAxis;
                    const g = a.shapeArgs;
                    if (e && d) {
                        let b = w(a.clientX, a.plotX),
                            f = a.plotY || 0;
                        a.isNode &&
                            g &&
                            m(g.x) &&
                            m(g.y) &&
                            ((b = g.x), (f = g.y));
                        return k
                            ? {
                                  chartX: d.len + d.pos - f,
                                  chartY: e.len + e.pos - b,
                              }
                            : { chartX: b + e.pos, chartY: f + d.pos };
                    }
                    if (g && g.x && g.y) return { chartX: g.x, chartY: g.y };
                }
                getChartPosition() {
                    if (this.chartPosition) return this.chartPosition;
                    var { container: a } = this.chart;
                    const k = e(a);
                    this.chartPosition = {
                        left: k.left,
                        top: k.top,
                        scaleX: 1,
                        scaleY: 1,
                    };
                    const g = a.offsetWidth;
                    a = a.offsetHeight;
                    2 < g &&
                        2 < a &&
                        ((this.chartPosition.scaleX = k.width / g),
                        (this.chartPosition.scaleY = k.height / a));
                    return this.chartPosition;
                }
                getCoordinates(a) {
                    const d = { xAxis: [], yAxis: [] };
                    this.chart.axes.forEach(function (k) {
                        d[k.isXAxis ? "xAxis" : "yAxis"].push({
                            axis: k,
                            value: k.toValue(a[k.horiz ? "chartX" : "chartY"]),
                        });
                    });
                    return d;
                }
                getHoverData(a, k, e, g, l, b) {
                    const f = [];
                    g = !(!g || !a);
                    const c = function (b) {
                        return (
                            b.visible &&
                            !(!l && b.directTouch) &&
                            w(b.options.enableMouseTracking, !0)
                        );
                    };
                    let d,
                        q = {
                            chartX: b ? b.chartX : void 0,
                            chartY: b ? b.chartY : void 0,
                            shared: l,
                        };
                    t(this, "beforeGetHoverData", q);
                    d =
                        k && !k.stickyTracking
                            ? [k]
                            : e.filter(
                                  (b) => b.stickyTracking && (q.filter || c)(b)
                              );
                    const r = g || !b ? a : this.findNearestKDPoint(d, l, b);
                    k = r && r.series;
                    r &&
                        (l && !k.noSharedTooltip
                            ? ((d = e.filter(function (b) {
                                  return q.filter
                                      ? q.filter(b)
                                      : c(b) && !b.noSharedTooltip;
                              })),
                              d.forEach(function (b) {
                                  let c = p(b.points, function (b) {
                                      return b.x === r.x && !b.isNull;
                                  });
                                  h(c) &&
                                      (b.boosted &&
                                          b.boost &&
                                          (c = b.boost.getPoint(c)),
                                      f.push(c));
                              }))
                            : f.push(r));
                    q = { hoverPoint: r };
                    t(this, "afterGetHoverData", q);
                    return {
                        hoverPoint: q.hoverPoint,
                        hoverSeries: k,
                        hoverPoints: f,
                    };
                }
                getPointFromEvent(a) {
                    a = a.target;
                    let d;
                    for (; a && !d; ) (d = a.point), (a = a.parentNode);
                    return d;
                }
                onTrackerMouseOut(a) {
                    a = a.relatedTarget;
                    const d = this.chart.hoverSeries;
                    this.isDirectTouch = !1;
                    if (
                        !(
                            !d ||
                            !a ||
                            d.stickyTracking ||
                            this.inClass(a, "highcharts-tooltip") ||
                            (this.inClass(a, "highcharts-series-" + d.index) &&
                                this.inClass(a, "highcharts-tracker"))
                        )
                    )
                        d.onMouseOut();
                }
                inClass(a, k) {
                    let d;
                    for (; a; ) {
                        if ((d = B(a, "class"))) {
                            if (-1 !== d.indexOf(k)) return !0;
                            if (-1 !== d.indexOf("highcharts-container"))
                                return !1;
                        }
                        a = a.parentElement;
                    }
                }
                init(a, k) {
                    this.options = k;
                    this.chart = a;
                    this.runChartClick = !(
                        !k.chart.events || !k.chart.events.click
                    );
                    this.pinchDown = [];
                    this.lastValidTouch = {};
                    this.setDOMEvents();
                    t(this, "afterInit");
                }
                normalize(a, k) {
                    var d = a.touches,
                        e = d
                            ? d.length
                                ? d.item(0)
                                : w(d.changedTouches, a.changedTouches)[0]
                            : a;
                    k || (k = this.getChartPosition());
                    d = e.pageX - k.left;
                    e = e.pageY - k.top;
                    d /= k.scaleX;
                    e /= k.scaleY;
                    return l(a, {
                        chartX: Math.round(d),
                        chartY: Math.round(e),
                    });
                }
                onContainerClick(a) {
                    const d = this.chart,
                        e = d.hoverPoint;
                    a = this.normalize(a);
                    const g = d.plotLeft,
                        h = d.plotTop;
                    d.cancelClick ||
                        (e && this.inClass(a.target, "highcharts-tracker")
                            ? (t(e.series, "click", l(a, { point: e })),
                              d.hoverPoint && e.firePointEvent("click", a))
                            : (l(a, this.getCoordinates(a)),
                              d.isInsidePlot(a.chartX - g, a.chartY - h, {
                                  visiblePlotOnly: !0,
                              }) && t(d, "click", a)));
                }
                onContainerMouseDown(a) {
                    const d = 1 === ((a.buttons || a.button) & 1);
                    a = this.normalize(a);
                    if (y.isFirefox && 0 !== a.button)
                        this.onContainerMouseMove(a);
                    if ("undefined" === typeof a.button || d)
                        this.zoomOption(a),
                            d && a.preventDefault && a.preventDefault(),
                            this.dragStart(a);
                }
                onContainerMouseLeave(a) {
                    const d = C[w(F.hoverChartIndex, -1)];
                    a = this.normalize(a);
                    d &&
                        a.relatedTarget &&
                        !this.inClass(a.relatedTarget, "highcharts-tooltip") &&
                        (d.pointer.reset(), (d.pointer.chartPosition = void 0));
                }
                onContainerMouseEnter(a) {
                    delete this.chartPosition;
                }
                onContainerMouseMove(a) {
                    const d = this.chart,
                        e = d.tooltip;
                    a = this.normalize(a);
                    this.setHoverChartIndex();
                    ("mousedown" === d.mouseIsDown || this.touchSelect(a)) &&
                        this.drag(a);
                    d.openMenu ||
                        (!this.inClass(a.target, "highcharts-tracker") &&
                            !d.isInsidePlot(
                                a.chartX - d.plotLeft,
                                a.chartY - d.plotTop,
                                { visiblePlotOnly: !0 }
                            )) ||
                        (e && e.shouldStickOnContact(a)) ||
                        (this.inClass(a.target, "highcharts-no-tooltip")
                            ? this.reset(!1, 0)
                            : this.runPointActions(a));
                }
                onDocumentTouchEnd(a) {
                    const d = C[w(F.hoverChartIndex, -1)];
                    d && d.pointer.drop(a);
                }
                onContainerTouchMove(a) {
                    if (this.touchSelect(a)) this.onContainerMouseMove(a);
                    else this.touch(a);
                }
                onContainerTouchStart(a) {
                    if (this.touchSelect(a)) this.onContainerMouseDown(a);
                    else this.zoomOption(a), this.touch(a, !0);
                }
                onDocumentMouseMove(a) {
                    const d = this.chart,
                        e = d.tooltip,
                        g = this.chartPosition;
                    a = this.normalize(a, g);
                    !g ||
                        d.isInsidePlot(
                            a.chartX - d.plotLeft,
                            a.chartY - d.plotTop,
                            { visiblePlotOnly: !0 }
                        ) ||
                        (e && e.shouldStickOnContact(a)) ||
                        this.inClass(a.target, "highcharts-tracker") ||
                        this.reset();
                }
                onDocumentMouseUp(a) {
                    const d = C[w(F.hoverChartIndex, -1)];
                    d && d.pointer.drop(a);
                }
                pinch(a) {
                    const d = this,
                        e = d.chart,
                        g = d.pinchDown,
                        h = a.touches || [],
                        b = h.length,
                        f = d.lastValidTouch,
                        c = d.hasZoom,
                        n = {},
                        m =
                            1 === b &&
                            ((d.inClass(a.target, "highcharts-tracker") &&
                                e.runTrackerClick) ||
                                d.runChartClick),
                        p = {};
                    var v = d.chart.tooltip;
                    v = 1 === b && w(v && v.options.followTouchMove, !0);
                    let u = d.selectionMarker;
                    1 < b ? (d.initiated = !0) : v && (d.initiated = !1);
                    c &&
                        d.initiated &&
                        !m &&
                        !1 !== a.cancelable &&
                        a.preventDefault();
                    [].map.call(h, function (b) {
                        return d.normalize(b);
                    });
                    "touchstart" === a.type
                        ? ([].forEach.call(h, function (b, a) {
                              g[a] = { chartX: b.chartX, chartY: b.chartY };
                          }),
                          (f.x = [g[0].chartX, g[1] && g[1].chartX]),
                          (f.y = [g[0].chartY, g[1] && g[1].chartY]),
                          e.axes.forEach(function (b) {
                              if (b.zoomEnabled) {
                                  const a = e.bounds[b.horiz ? "h" : "v"],
                                      c = b.minPixelPadding,
                                      f = b.toPixels(
                                          Math.min(
                                              w(b.options.min, b.dataMin),
                                              b.dataMin
                                          )
                                      ),
                                      d = b.toPixels(
                                          Math.max(
                                              w(b.options.max, b.dataMax),
                                              b.dataMax
                                          )
                                      ),
                                      n = Math.max(f, d);
                                  a.min = Math.min(b.pos, Math.min(f, d) - c);
                                  a.max = Math.max(b.pos + b.len, n + c);
                              }
                          }),
                          (d.res = !0))
                        : v
                        ? this.runPointActions(d.normalize(a))
                        : g.length &&
                          (t(e, "touchpan", { originalEvent: a }, () => {
                              u ||
                                  (d.selectionMarker = u =
                                      l({ destroy: z, touch: !0 }, e.plotBox));
                              d.pinchTranslate(g, h, n, u, p, f);
                              d.hasPinched = c;
                              d.scaleGroups(n, p);
                          }),
                          d.res && ((d.res = !1), this.reset(!1, 0)));
                }
                pinchTranslate(a, e, g, h, l, b) {
                    this.zoomHor &&
                        this.pinchTranslateDirection(!0, a, e, g, h, l, b);
                    this.zoomVert &&
                        this.pinchTranslateDirection(!1, a, e, g, h, l, b);
                }
                pinchTranslateDirection(a, e, g, h, l, b, f, c) {
                    const d = this.chart,
                        k = a ? "x" : "y",
                        q = a ? "X" : "Y",
                        m = "chart" + q,
                        r = a ? "width" : "height",
                        p = d["plot" + (a ? "Left" : "Top")],
                        t = d.inverted,
                        w = d.bounds[a ? "h" : "v"],
                        v = 1 === e.length,
                        u = e[0][m],
                        x = !v && e[1][m];
                    e = function () {
                        "number" === typeof N &&
                            20 < Math.abs(u - x) &&
                            (F = c || Math.abs(J - N) / Math.abs(u - x));
                        E = (p - J) / F + u;
                        G = d["plot" + (a ? "Width" : "Height")] / F;
                    };
                    let G,
                        E,
                        F = c || 1,
                        J = g[0][m],
                        N = !v && g[1][m],
                        O;
                    e();
                    g = E;
                    g < w.min
                        ? ((g = w.min), (O = !0))
                        : g + G > w.max && ((g = w.max - G), (O = !0));
                    O
                        ? ((J -= 0.8 * (J - f[k][0])),
                          "number" === typeof N && (N -= 0.8 * (N - f[k][1])),
                          e())
                        : (f[k] = [J, N]);
                    t || ((b[k] = E - p), (b[r] = G));
                    b = t ? 1 / F : F;
                    l[r] = G;
                    l[k] = g;
                    h[t ? (a ? "scaleY" : "scaleX") : "scale" + q] = F;
                    h["translate" + q] = b * p + (J - b * u);
                }
                reset(a, e) {
                    const d = this.chart,
                        k = d.hoverSeries,
                        g = d.hoverPoint,
                        b = d.hoverPoints,
                        f = d.tooltip,
                        c = f && f.shared ? b : g;
                    a &&
                        c &&
                        E(c).forEach(function (b) {
                            b.series.isCartesian &&
                                "undefined" === typeof b.plotX &&
                                (a = !1);
                        });
                    if (a)
                        f &&
                            c &&
                            E(c).length &&
                            (f.refresh(c),
                            f.shared && b
                                ? b.forEach(function (b) {
                                      b.setState(b.state, !0);
                                      b.series.isCartesian &&
                                          (b.series.xAxis.crosshair &&
                                              b.series.xAxis.drawCrosshair(
                                                  null,
                                                  b
                                              ),
                                          b.series.yAxis.crosshair &&
                                              b.series.yAxis.drawCrosshair(
                                                  null,
                                                  b
                                              ));
                                  })
                                : g &&
                                  (g.setState(g.state, !0),
                                  d.axes.forEach(function (b) {
                                      b.crosshair &&
                                          g.series[b.coll] === b &&
                                          b.drawCrosshair(null, g);
                                  })));
                    else {
                        if (g) g.onMouseOut();
                        b &&
                            b.forEach(function (b) {
                                b.setState();
                            });
                        if (k) k.onMouseOut();
                        f && f.hide(e);
                        this.unDocMouseMove &&
                            (this.unDocMouseMove = this.unDocMouseMove());
                        d.axes.forEach(function (b) {
                            b.hideCrosshair();
                        });
                        this.hoverX = d.hoverPoints = d.hoverPoint = null;
                    }
                }
                runPointActions(a, e, g) {
                    const d = this.chart,
                        k =
                            d.tooltip && d.tooltip.options.enabled
                                ? d.tooltip
                                : void 0,
                        b = k ? k.shared : !1;
                    let f = e || d.hoverPoint,
                        c = (f && f.series) || d.hoverSeries;
                    e = this.getHoverData(
                        f,
                        c,
                        d.series,
                        (!a || "touchmove" !== a.type) &&
                            (!!e || (c && c.directTouch && this.isDirectTouch)),
                        b,
                        a
                    );
                    f = e.hoverPoint;
                    c = e.hoverSeries;
                    const n = e.hoverPoints;
                    e =
                        c &&
                        c.tooltipOptions.followPointer &&
                        !c.tooltipOptions.split;
                    const h = b && c && !c.noSharedTooltip;
                    if (f && (g || f !== d.hoverPoint || (k && k.isHidden))) {
                        (d.hoverPoints || []).forEach(function (b) {
                            -1 === n.indexOf(b) && b.setState();
                        });
                        if (d.hoverSeries !== c) c.onMouseOver();
                        this.applyInactiveState(n);
                        (n || []).forEach(function (b) {
                            b.setState("hover");
                        });
                        d.hoverPoint && d.hoverPoint.firePointEvent("mouseOut");
                        if (!f.series) return;
                        d.hoverPoints = n;
                        d.hoverPoint = f;
                        f.firePointEvent("mouseOver", void 0, () => {
                            k && f && k.refresh(h ? n : f, a);
                        });
                    } else
                        e &&
                            k &&
                            !k.isHidden &&
                            ((g = k.getAnchor([{}], a)),
                            d.isInsidePlot(g[0], g[1], {
                                visiblePlotOnly: !0,
                            }) &&
                                k.updatePosition({
                                    plotX: g[0],
                                    plotY: g[1],
                                }));
                    this.unDocMouseMove ||
                        ((this.unDocMouseMove = H(
                            d.container.ownerDocument,
                            "mousemove",
                            function (b) {
                                const a = C[F.hoverChartIndex];
                                if (a) a.pointer.onDocumentMouseMove(b);
                            }
                        )),
                        this.eventsToUnbind.push(this.unDocMouseMove));
                    d.axes.forEach(function (b) {
                        const c = w((b.crosshair || {}).snap, !0);
                        let f;
                        c &&
                            (((f = d.hoverPoint) && f.series[b.coll] === b) ||
                                (f = p(
                                    n,
                                    (a) => a.series && a.series[b.coll] === b
                                )));
                        f || !c ? b.drawCrosshair(a, f) : b.hideCrosshair();
                    });
                }
                scaleGroups(a, e) {
                    const d = this.chart;
                    d.series.forEach(function (k) {
                        const g = a || k.getPlotBox();
                        k.group &&
                            ((k.xAxis && k.xAxis.zoomEnabled) || d.mapView) &&
                            (k.group.attr(g),
                            k.markerGroup &&
                                (k.markerGroup.attr(g),
                                k.markerGroup.clip(e ? d.clipRect : null)),
                            k.dataLabelsGroup && k.dataLabelsGroup.attr(g));
                    });
                    d.clipRect.attr(e || d.clipBox);
                }
                setDOMEvents() {
                    const a = this.chart.container,
                        e = a.ownerDocument;
                    a.onmousedown = this.onContainerMouseDown.bind(this);
                    a.onmousemove = this.onContainerMouseMove.bind(this);
                    a.onclick = this.onContainerClick.bind(this);
                    this.eventsToUnbind.push(
                        H(
                            a,
                            "mouseenter",
                            this.onContainerMouseEnter.bind(this)
                        )
                    );
                    this.eventsToUnbind.push(
                        H(
                            a,
                            "mouseleave",
                            this.onContainerMouseLeave.bind(this)
                        )
                    );
                    F.unbindDocumentMouseUp ||
                        (F.unbindDocumentMouseUp = H(
                            e,
                            "mouseup",
                            this.onDocumentMouseUp.bind(this)
                        ));
                    let g = this.chart.renderTo.parentElement;
                    for (; g && "BODY" !== g.tagName; )
                        this.eventsToUnbind.push(
                            H(g, "scroll", () => {
                                delete this.chartPosition;
                            })
                        ),
                            (g = g.parentElement);
                    y.hasTouch &&
                        (this.eventsToUnbind.push(
                            H(
                                a,
                                "touchstart",
                                this.onContainerTouchStart.bind(this),
                                { passive: !1 }
                            )
                        ),
                        this.eventsToUnbind.push(
                            H(
                                a,
                                "touchmove",
                                this.onContainerTouchMove.bind(this),
                                { passive: !1 }
                            )
                        ),
                        F.unbindDocumentTouchEnd ||
                            (F.unbindDocumentTouchEnd = H(
                                e,
                                "touchend",
                                this.onDocumentTouchEnd.bind(this),
                                { passive: !1 }
                            )));
                }
                setHoverChartIndex() {
                    const a = this.chart,
                        e = y.charts[w(F.hoverChartIndex, -1)];
                    if (e && e !== a)
                        e.pointer.onContainerMouseLeave({
                            relatedTarget: a.container,
                        });
                    (e && e.mouseIsDown) || (F.hoverChartIndex = a.index);
                }
                touch(a, e) {
                    const d = this.chart;
                    let g, k;
                    this.setHoverChartIndex();
                    1 === a.touches.length
                        ? ((a = this.normalize(a)),
                          (k = d.isInsidePlot(
                              a.chartX - d.plotLeft,
                              a.chartY - d.plotTop,
                              { visiblePlotOnly: !0 }
                          )) && !d.openMenu
                              ? (e && this.runPointActions(a),
                                "touchmove" === a.type &&
                                    ((e = this.pinchDown),
                                    (g = e[0]
                                        ? 4 <=
                                          Math.sqrt(
                                              Math.pow(
                                                  e[0].chartX - a.chartX,
                                                  2
                                              ) +
                                                  Math.pow(
                                                      e[0].chartY - a.chartY,
                                                      2
                                                  )
                                          )
                                        : !1)),
                                w(g, !0) && this.pinch(a))
                              : e && this.reset())
                        : 2 === a.touches.length && this.pinch(a);
                }
                touchSelect(a) {
                    return !(
                        !this.chart.zooming.singleTouch ||
                        !a.touches ||
                        1 !== a.touches.length
                    );
                }
                zoomOption(a) {
                    const d = this.chart,
                        e = d.inverted;
                    var g = d.zooming.type || "";
                    /touch/.test(a.type) && (g = w(d.zooming.pinchType, g));
                    this.zoomX = a = /x/.test(g);
                    this.zoomY = g = /y/.test(g);
                    this.zoomHor = (a && !e) || (g && e);
                    this.zoomVert = (g && !e) || (a && e);
                    this.hasZoom = a || g;
                }
            }
            (function (a) {
                const d = [],
                    e = [];
                a.compose = function (d) {
                    I.pushUnique(e, d) &&
                        H(d, "beforeRender", function () {
                            this.pointer = new a(this, this.options);
                        });
                };
                a.dissolve = function () {
                    for (let a = 0, e = d.length; a < e; ++a) d[a]();
                    d.length = 0;
                };
            })(F || (F = {}));
            ("");
            return F;
        }
    );
    M(
        a,
        "Core/Legend/Legend.js",
        [
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Templating.js"],
            a["Core/Globals.js"],
            a["Core/Series/Point.js"],
            a["Core/Renderer/RendererUtilities.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C, z) {
            const { animObject: x, setAnimation: B } = a,
                { format: u } = y,
                { marginNames: v } = I,
                { distribute: l } = C,
                {
                    addEvent: p,
                    createElement: t,
                    css: m,
                    defined: h,
                    discardElement: g,
                    find: e,
                    fireEvent: w,
                    isNumber: E,
                    merge: F,
                    pick: d,
                    relativeLength: k,
                    stableSort: r,
                    syncTimeout: q,
                } = z;
            class G {
                constructor(b, a) {
                    this.allItems = [];
                    this.contentGroup = this.box = void 0;
                    this.display = !1;
                    this.group = void 0;
                    this.offsetWidth =
                        this.maxLegendWidth =
                        this.maxItemWidth =
                        this.legendWidth =
                        this.legendHeight =
                        this.lastLineHeight =
                        this.lastItemY =
                        this.itemY =
                        this.itemX =
                        this.itemMarginTop =
                        this.itemMarginBottom =
                        this.itemHeight =
                        this.initialItemY =
                            0;
                    this.options = void 0;
                    this.padding = 0;
                    this.pages = [];
                    this.proximate = !1;
                    this.scrollGroup = void 0;
                    this.widthOption =
                        this.totalItemWidth =
                        this.titleHeight =
                        this.symbolWidth =
                        this.symbolHeight =
                            0;
                    this.chart = b;
                    this.init(b, a);
                }
                init(b, a) {
                    this.chart = b;
                    this.setOptions(a);
                    a.enabled &&
                        (this.render(),
                        p(this.chart, "endResize", function () {
                            this.legend.positionCheckboxes();
                        }),
                        p(this.chart, "render", () => {
                            this.proximate &&
                                (this.proximatePositions(),
                                this.positionItems());
                        }));
                }
                setOptions(b) {
                    const a = d(b.padding, 8);
                    this.options = b;
                    this.chart.styledMode ||
                        ((this.itemStyle = b.itemStyle),
                        (this.itemHiddenStyle = F(
                            this.itemStyle,
                            b.itemHiddenStyle
                        )));
                    this.itemMarginTop = b.itemMarginTop;
                    this.itemMarginBottom = b.itemMarginBottom;
                    this.padding = a;
                    this.initialItemY = a - 5;
                    this.symbolWidth = d(b.symbolWidth, 16);
                    this.pages = [];
                    this.proximate =
                        "proximate" === b.layout && !this.chart.inverted;
                    this.baseline = void 0;
                }
                update(b, a) {
                    const c = this.chart;
                    this.setOptions(F(!0, this.options, b));
                    this.destroy();
                    c.isDirtyLegend = c.isDirtyBox = !0;
                    d(a, !0) && c.redraw();
                    w(this, "afterUpdate");
                }
                colorizeItem(b, a) {
                    const {
                        group: c,
                        label: f,
                        line: d,
                        symbol: e,
                    } = b.legendItem || {};
                    if (c)
                        c[a ? "removeClass" : "addClass"](
                            "highcharts-legend-item-hidden"
                        );
                    if (!this.chart.styledMode) {
                        const { itemHiddenStyle: c } = this,
                            g = c.color,
                            k = a ? b.color || g : g,
                            n = b.options && b.options.marker;
                        let h = { fill: k };
                        null === f || void 0 === f
                            ? void 0
                            : f.css(F(a ? this.itemStyle : c));
                        null === d || void 0 === d
                            ? void 0
                            : d.attr({ stroke: k });
                        e &&
                            (n &&
                                e.isMarker &&
                                ((h = b.pointAttribs()),
                                a || (h.stroke = h.fill = g)),
                            e.attr(h));
                    }
                    w(this, "afterColorizeItem", { item: b, visible: a });
                }
                positionItems() {
                    this.allItems.forEach(this.positionItem, this);
                    this.chart.isResizing || this.positionCheckboxes();
                }
                positionItem(b) {
                    const { group: a, x: c = 0, y: d = 0 } = b.legendItem || {};
                    var e = this.options,
                        g = e.symbolPadding;
                    const k = !e.rtl;
                    e = b.checkbox;
                    a &&
                        a.element &&
                        ((g = {
                            translateX: k
                                ? c
                                : this.legendWidth - c - 2 * g - 4,
                            translateY: d,
                        }),
                        a[h(a.translateY) ? "animate" : "attr"](
                            g,
                            void 0,
                            () => {
                                w(this, "afterPositionItem", { item: b });
                            }
                        ));
                    e && ((e.x = c), (e.y = d));
                }
                destroyItem(b) {
                    const a = b.checkbox,
                        c = b.legendItem || {};
                    for (const b of ["group", "label", "line", "symbol"])
                        c[b] && (c[b] = c[b].destroy());
                    a && g(a);
                    b.legendItem = void 0;
                }
                destroy() {
                    for (const b of this.getAllItems()) this.destroyItem(b);
                    for (const b of "clipRect up down pager nav box title group".split(
                        " "
                    ))
                        this[b] && (this[b] = this[b].destroy());
                    this.display = null;
                }
                positionCheckboxes() {
                    const b = this.group && this.group.alignAttr,
                        a = this.clipHeight || this.legendHeight,
                        c = this.titleHeight;
                    let d;
                    b &&
                        ((d = b.translateY),
                        this.allItems.forEach(function (f) {
                            const e = f.checkbox;
                            let g;
                            e &&
                                ((g =
                                    d + c + e.y + (this.scrollOffset || 0) + 3),
                                m(e, {
                                    left:
                                        b.translateX +
                                        f.checkboxOffset +
                                        e.x -
                                        20 +
                                        "px",
                                    top: g + "px",
                                    display:
                                        this.proximate ||
                                        (g > d - 6 && g < d + a - 6)
                                            ? ""
                                            : "none",
                                }));
                        }, this));
                }
                renderTitle() {
                    var b = this.options;
                    const a = this.padding,
                        c = b.title;
                    let d = 0;
                    c.text &&
                        (this.title ||
                            ((this.title = this.chart.renderer
                                .label(
                                    c.text,
                                    a - 3,
                                    a - 4,
                                    void 0,
                                    void 0,
                                    void 0,
                                    b.useHTML,
                                    void 0,
                                    "legend-title"
                                )
                                .attr({ zIndex: 1 })),
                            this.chart.styledMode || this.title.css(c.style),
                            this.title.add(this.group)),
                        c.width ||
                            this.title.css({
                                width: this.maxLegendWidth + "px",
                            }),
                        (b = this.title.getBBox()),
                        (d = b.height),
                        (this.offsetWidth = b.width),
                        this.contentGroup.attr({ translateY: d }));
                    this.titleHeight = d;
                }
                setText(b) {
                    const a = this.options;
                    b.legendItem.label.attr({
                        text: a.labelFormat
                            ? u(a.labelFormat, b, this.chart)
                            : a.labelFormatter.call(b),
                    });
                }
                renderItem(b) {
                    const a = (b.legendItem = b.legendItem || {});
                    var c = this.chart,
                        e = c.renderer;
                    const g = this.options,
                        k = this.symbolWidth,
                        h = g.symbolPadding || 0,
                        l = this.itemStyle,
                        m = this.itemHiddenStyle,
                        q =
                            "horizontal" === g.layout
                                ? d(g.itemDistance, 20)
                                : 0,
                        r = !g.rtl,
                        p = !b.series,
                        t = !p && b.series.drawLegendSymbol ? b.series : b;
                    var w = t.options;
                    const v = this.createCheckboxForItem && w && w.showCheckbox,
                        u = g.useHTML,
                        x = b.options.className;
                    let J = a.label;
                    w = k + h + q + (v ? 20 : 0);
                    J ||
                        ((a.group = e
                            .g("legend-item")
                            .addClass(
                                "highcharts-" +
                                    t.type +
                                    "-series highcharts-color-" +
                                    b.colorIndex +
                                    (x ? " " + x : "") +
                                    (p ? " highcharts-series-" + b.index : "")
                            )
                            .attr({ zIndex: 1 })
                            .add(this.scrollGroup)),
                        (a.label = J =
                            e.text("", r ? k + h : -h, this.baseline || 0, u)),
                        c.styledMode || J.css(F(b.visible ? l : m)),
                        J.attr({ align: r ? "left" : "right", zIndex: 2 }).add(
                            a.group
                        ),
                        this.baseline ||
                            ((this.fontMetrics = e.fontMetrics(J)),
                            (this.baseline =
                                this.fontMetrics.f + 3 + this.itemMarginTop),
                            J.attr("y", this.baseline),
                            (this.symbolHeight = d(
                                g.symbolHeight,
                                this.fontMetrics.f
                            )),
                            g.squareSymbol &&
                                ((this.symbolWidth = d(
                                    g.symbolWidth,
                                    Math.max(this.symbolHeight, 16)
                                )),
                                (w = this.symbolWidth + h + q + (v ? 20 : 0)),
                                r && J.attr("x", this.symbolWidth + h))),
                        t.drawLegendSymbol(this, b),
                        this.setItemEvents && this.setItemEvents(b, J, u));
                    v &&
                        !b.checkbox &&
                        this.createCheckboxForItem &&
                        this.createCheckboxForItem(b);
                    this.colorizeItem(b, b.visible);
                    (!c.styledMode && l.width) ||
                        J.css({
                            width:
                                (g.itemWidth ||
                                    this.widthOption ||
                                    c.spacingBox.width) -
                                w +
                                "px",
                        });
                    this.setText(b);
                    c = J.getBBox();
                    e = (this.fontMetrics && this.fontMetrics.h) || 0;
                    b.itemWidth = b.checkboxOffset =
                        g.itemWidth || a.labelWidth || c.width + w;
                    this.maxItemWidth = Math.max(
                        this.maxItemWidth,
                        b.itemWidth
                    );
                    this.totalItemWidth += b.itemWidth;
                    this.itemHeight = b.itemHeight = Math.round(
                        a.labelHeight || (c.height > 1.5 * e ? c.height : e)
                    );
                }
                layoutItem(b) {
                    var a = this.options;
                    const c = this.padding,
                        e = "horizontal" === a.layout,
                        g = b.itemHeight,
                        k = this.itemMarginBottom,
                        h = this.itemMarginTop,
                        l = e ? d(a.itemDistance, 20) : 0,
                        m = this.maxLegendWidth;
                    a =
                        a.alignColumns && this.totalItemWidth > m
                            ? this.maxItemWidth
                            : b.itemWidth;
                    const q = b.legendItem || {};
                    e &&
                        this.itemX - c + a > m &&
                        ((this.itemX = c),
                        this.lastLineHeight &&
                            (this.itemY += h + this.lastLineHeight + k),
                        (this.lastLineHeight = 0));
                    this.lastItemY = h + this.itemY + k;
                    this.lastLineHeight = Math.max(g, this.lastLineHeight);
                    q.x = this.itemX;
                    q.y = this.itemY;
                    e
                        ? (this.itemX += a)
                        : ((this.itemY += h + g + k),
                          (this.lastLineHeight = g));
                    this.offsetWidth =
                        this.widthOption ||
                        Math.max(
                            (e ? this.itemX - c - (b.checkbox ? 0 : l) : a) + c,
                            this.offsetWidth
                        );
                }
                getAllItems() {
                    let b = [];
                    this.chart.series.forEach(function (a) {
                        const c = a && a.options;
                        a &&
                            d(
                                c.showInLegend,
                                h(c.linkedTo) ? !1 : void 0,
                                !0
                            ) &&
                            (b = b.concat(
                                (a.legendItem || {}).labels ||
                                    ("point" === c.legendType ? a.data : a)
                            ));
                    });
                    w(this, "afterGetAllItems", { allItems: b });
                    return b;
                }
                getAlignment() {
                    const b = this.options;
                    return this.proximate
                        ? b.align.charAt(0) + "tv"
                        : b.floating
                        ? ""
                        : b.align.charAt(0) +
                          b.verticalAlign.charAt(0) +
                          b.layout.charAt(0);
                }
                adjustMargins(b, a) {
                    const c = this.chart,
                        f = this.options,
                        e = this.getAlignment();
                    e &&
                        [
                            /(lth|ct|rth)/,
                            /(rtv|rm|rbv)/,
                            /(rbh|cb|lbh)/,
                            /(lbv|lm|ltv)/,
                        ].forEach(function (g, k) {
                            g.test(e) &&
                                !h(b[k]) &&
                                (c[v[k]] = Math.max(
                                    c[v[k]],
                                    c.legend[
                                        (k + 1) % 2
                                            ? "legendHeight"
                                            : "legendWidth"
                                    ] +
                                        [1, -1, -1, 1][k] *
                                            f[k % 2 ? "x" : "y"] +
                                        d(f.margin, 12) +
                                        a[k] +
                                        (c.titleOffset[k] || 0)
                                ));
                        });
                }
                proximatePositions() {
                    const b = this.chart,
                        a = [],
                        c = "left" === this.options.align;
                    this.allItems.forEach(function (d) {
                        var f;
                        var g = c;
                        let k;
                        d.yAxis &&
                            (d.xAxis.options.reversed && (g = !g),
                            d.points &&
                                (f = e(
                                    g ? d.points : d.points.slice(0).reverse(),
                                    function (b) {
                                        return E(b.plotY);
                                    }
                                )),
                            (g =
                                this.itemMarginTop +
                                d.legendItem.label.getBBox().height +
                                this.itemMarginBottom),
                            (k = d.yAxis.top - b.plotTop),
                            d.visible
                                ? ((f = f ? f.plotY : d.yAxis.height),
                                  (f += k - 0.3 * g))
                                : (f = k + d.yAxis.height),
                            a.push({ target: f, size: g, item: d }));
                    }, this);
                    let d;
                    for (const c of l(a, b.plotHeight))
                        (d = c.item.legendItem || {}),
                            E(c.pos) &&
                                (d.y = b.plotTop - b.spacing[0] + c.pos);
                }
                render() {
                    const b = this.chart,
                        a = b.renderer,
                        c = this.options,
                        d = this.padding;
                    var e = this.getAllItems();
                    let g,
                        h = this.group,
                        l = this.box;
                    this.itemX = d;
                    this.itemY = this.initialItemY;
                    this.lastItemY = this.offsetWidth = 0;
                    this.widthOption = k(c.width, b.spacingBox.width - d);
                    var m = b.spacingBox.width - 2 * d - c.x;
                    -1 <
                        ["rm", "lm"].indexOf(
                            this.getAlignment().substring(0, 2)
                        ) && (m /= 2);
                    this.maxLegendWidth = this.widthOption || m;
                    h ||
                        ((this.group = h =
                            a
                                .g("legend")
                                .addClass(c.className || "")
                                .attr({ zIndex: 7 })
                                .add()),
                        (this.contentGroup = a.g().attr({ zIndex: 1 }).add(h)),
                        (this.scrollGroup = a.g().add(this.contentGroup)));
                    this.renderTitle();
                    r(
                        e,
                        (b, a) =>
                            ((b.options && b.options.legendIndex) || 0) -
                            ((a.options && a.options.legendIndex) || 0)
                    );
                    c.reversed && e.reverse();
                    this.allItems = e;
                    this.display = m = !!e.length;
                    this.itemHeight =
                        this.totalItemWidth =
                        this.maxItemWidth =
                        this.lastLineHeight =
                            0;
                    e.forEach(this.renderItem, this);
                    e.forEach(this.layoutItem, this);
                    e = (this.widthOption || this.offsetWidth) + d;
                    g = this.lastItemY + this.lastLineHeight + this.titleHeight;
                    g = this.handleOverflow(g);
                    g += d;
                    l ||
                        (this.box = l =
                            a
                                .rect()
                                .addClass("highcharts-legend-box")
                                .attr({ r: c.borderRadius })
                                .add(h));
                    b.styledMode ||
                        l
                            .attr({
                                stroke: c.borderColor,
                                "stroke-width": c.borderWidth || 0,
                                fill: c.backgroundColor || "none",
                            })
                            .shadow(c.shadow);
                    if (0 < e && 0 < g)
                        l[l.placed ? "animate" : "attr"](
                            l.crisp.call(
                                {},
                                { x: 0, y: 0, width: e, height: g },
                                l.strokeWidth()
                            )
                        );
                    h[m ? "show" : "hide"]();
                    b.styledMode &&
                        "none" === h.getStyle("display") &&
                        (e = g = 0);
                    this.legendWidth = e;
                    this.legendHeight = g;
                    m && this.align();
                    this.proximate || this.positionItems();
                    w(this, "afterRender");
                }
                align(b = this.chart.spacingBox) {
                    const a = this.chart,
                        c = this.options;
                    let d = b.y;
                    /(lth|ct|rth)/.test(this.getAlignment()) &&
                    0 < a.titleOffset[0]
                        ? (d += a.titleOffset[0])
                        : /(lbh|cb|rbh)/.test(this.getAlignment()) &&
                          0 < a.titleOffset[2] &&
                          (d -= a.titleOffset[2]);
                    d !== b.y && (b = F(b, { y: d }));
                    a.hasRendered || (this.group.placed = !1);
                    this.group.align(
                        F(c, {
                            width: this.legendWidth,
                            height: this.legendHeight,
                            verticalAlign: this.proximate
                                ? "top"
                                : c.verticalAlign,
                        }),
                        !0,
                        b
                    );
                }
                handleOverflow(b) {
                    const a = this,
                        c = this.chart,
                        e = c.renderer,
                        g = this.options;
                    var k = g.y;
                    const h = "top" === g.verticalAlign,
                        l = this.padding,
                        m = g.maxHeight,
                        q = g.navigation,
                        r = d(q.animation, !0),
                        p = q.arrowSize || 12,
                        t = this.pages,
                        w = this.allItems,
                        v = function (b) {
                            "number" === typeof b
                                ? E.attr({ height: b })
                                : E &&
                                  ((a.clipRect = E.destroy()),
                                  a.contentGroup.clip());
                            a.contentGroup.div &&
                                (a.contentGroup.div.style.clip = b
                                    ? "rect(" +
                                      l +
                                      "px,9999px," +
                                      (l + b) +
                                      "px,0)"
                                    : "auto");
                        },
                        u = function (b) {
                            a[b] = e
                                .circle(0, 0, 1.3 * p)
                                .translate(p / 2, p / 2)
                                .add(O);
                            c.styledMode ||
                                a[b].attr("fill", "rgba(0,0,0,0.0001)");
                            return a[b];
                        };
                    let x, J, N;
                    k = c.spacingBox.height + (h ? -k : k) - l;
                    let O = this.nav,
                        E = this.clipRect;
                    "horizontal" !== g.layout ||
                        "middle" === g.verticalAlign ||
                        g.floating ||
                        (k /= 2);
                    m && (k = Math.min(k, m));
                    t.length = 0;
                    b && 0 < k && b > k && !1 !== q.enabled
                        ? ((this.clipHeight = x =
                              Math.max(k - 20 - this.titleHeight - l, 0)),
                          (this.currentPage = d(this.currentPage, 1)),
                          (this.fullHeight = b),
                          w.forEach((b, a) => {
                              N = b.legendItem || {};
                              b = N.y || 0;
                              const c = Math.round(N.label.getBBox().height);
                              let d = t.length;
                              if (
                                  !d ||
                                  (b - t[d - 1] > x && (J || b) !== t[d - 1])
                              )
                                  t.push(J || b), d++;
                              N.pageIx = d - 1;
                              J && ((w[a - 1].legendItem || {}).pageIx = d - 1);
                              a === w.length - 1 &&
                                  b + c - t[d - 1] > x &&
                                  b > t[d - 1] &&
                                  (t.push(b), (N.pageIx = d));
                              b !== J && (J = b);
                          }),
                          E ||
                              ((E = a.clipRect = e.clipRect(0, l - 2, 9999, 0)),
                              a.contentGroup.clip(E)),
                          v(x),
                          O ||
                              ((this.nav = O =
                                  e.g().attr({ zIndex: 1 }).add(this.group)),
                              (this.up = e
                                  .symbol("triangle", 0, 0, p, p)
                                  .add(O)),
                              u("upTracker").on("click", function () {
                                  a.scroll(-1, r);
                              }),
                              (this.pager = e
                                  .text("", 15, 10)
                                  .addClass("highcharts-legend-navigation")),
                              !c.styledMode &&
                                  q.style &&
                                  this.pager.css(q.style),
                              this.pager.add(O),
                              (this.down = e
                                  .symbol("triangle-down", 0, 0, p, p)
                                  .add(O)),
                              u("downTracker").on("click", function () {
                                  a.scroll(1, r);
                              })),
                          a.scroll(0),
                          (b = k))
                        : O &&
                          (v(),
                          (this.nav = O.destroy()),
                          this.scrollGroup.attr({ translateY: 1 }),
                          (this.clipHeight = 0));
                    return b;
                }
                scroll(b, a) {
                    const c = this.chart,
                        f = this.pages,
                        e = f.length,
                        g = this.clipHeight,
                        k = this.options.navigation,
                        h = this.pager,
                        l = this.padding;
                    let m = this.currentPage + b;
                    m > e && (m = e);
                    0 < m &&
                        ("undefined" !== typeof a && B(a, c),
                        this.nav.attr({
                            translateX: l,
                            translateY: g + this.padding + 7 + this.titleHeight,
                            visibility: "inherit",
                        }),
                        [this.up, this.upTracker].forEach(function (b) {
                            b.attr({
                                class:
                                    1 === m
                                        ? "highcharts-legend-nav-inactive"
                                        : "highcharts-legend-nav-active",
                            });
                        }),
                        h.attr({ text: m + "/" + e }),
                        [this.down, this.downTracker].forEach(function (b) {
                            b.attr({
                                x: 18 + this.pager.getBBox().width,
                                class:
                                    m === e
                                        ? "highcharts-legend-nav-inactive"
                                        : "highcharts-legend-nav-active",
                            });
                        }, this),
                        c.styledMode ||
                            (this.up.attr({
                                fill: 1 === m ? k.inactiveColor : k.activeColor,
                            }),
                            this.upTracker.css({
                                cursor: 1 === m ? "default" : "pointer",
                            }),
                            this.down.attr({
                                fill: m === e ? k.inactiveColor : k.activeColor,
                            }),
                            this.downTracker.css({
                                cursor: m === e ? "default" : "pointer",
                            })),
                        (this.scrollOffset = -f[m - 1] + this.initialItemY),
                        this.scrollGroup.animate({
                            translateY: this.scrollOffset,
                        }),
                        (this.currentPage = m),
                        this.positionCheckboxes(),
                        (b = x(d(a, c.renderer.globalAnimation, !0))),
                        q(() => {
                            w(this, "afterScroll", { currentPage: m });
                        }, b.duration));
                }
                setItemEvents(b, a, c) {
                    const d = this,
                        f = b.legendItem || {},
                        e = d.chart.renderer.boxWrapper,
                        g = b instanceof L,
                        k =
                            "highcharts-legend-" +
                            (g ? "point" : "series") +
                            "-active",
                        h = d.chart.styledMode;
                    c = c ? [a, f.symbol] : [f.group];
                    const l = (a) => {
                        d.allItems.forEach((c) => {
                            b !== c &&
                                [c]
                                    .concat(c.linkedSeries || [])
                                    .forEach((b) => {
                                        b.setState(a, !g);
                                    });
                        });
                    };
                    for (const f of c)
                        if (f)
                            f.on("mouseover", function () {
                                b.visible && l("inactive");
                                b.setState("hover");
                                b.visible && e.addClass(k);
                                h || a.css(d.options.itemHoverStyle);
                            })
                                .on("mouseout", function () {
                                    d.chart.styledMode ||
                                        a.css(
                                            F(
                                                b.visible
                                                    ? d.itemStyle
                                                    : d.itemHiddenStyle
                                            )
                                        );
                                    l("");
                                    e.removeClass(k);
                                    b.setState();
                                })
                                .on("click", function (a) {
                                    const c = function () {
                                        b.setVisible && b.setVisible();
                                        l(b.visible ? "inactive" : "");
                                    };
                                    e.removeClass(k);
                                    a = { browserEvent: a };
                                    b.firePointEvent
                                        ? b.firePointEvent(
                                              "legendItemClick",
                                              a,
                                              c
                                          )
                                        : w(b, "legendItemClick", a, c);
                                });
                }
                createCheckboxForItem(b) {
                    b.checkbox = t(
                        "input",
                        {
                            type: "checkbox",
                            className: "highcharts-legend-checkbox",
                            checked: b.selected,
                            defaultChecked: b.selected,
                        },
                        this.options.itemCheckboxStyle,
                        this.chart.container
                    );
                    p(b.checkbox, "click", function (a) {
                        w(
                            b.series || b,
                            "checkboxClick",
                            { checked: a.target.checked, item: b },
                            function () {
                                b.select();
                            }
                        );
                    });
                }
            }
            (function (b) {
                const a = [];
                b.compose = function (c) {
                    z.pushUnique(a, c) &&
                        p(c, "beforeMargins", function () {
                            this.legend = new b(this, this.options.legend);
                        });
                };
            })(G || (G = {}));
            ("");
            return G;
        }
    );
    M(a, "Core/Legend/LegendSymbol.js", [a["Core/Utilities.js"]], function (a) {
        const { extend: x, merge: I, pick: L } = a;
        var C;
        (function (a) {
            a.lineMarker = function (a, B) {
                B = this.legendItem = this.legendItem || {};
                var u = this.options;
                const v = a.symbolWidth,
                    l = a.symbolHeight,
                    p = l / 2,
                    t = this.chart.renderer,
                    m = B.group;
                a = a.baseline - Math.round(0.3 * a.fontMetrics.b);
                let h = {},
                    g = u.marker,
                    e = 0;
                this.chart.styledMode ||
                    ((h = { "stroke-width": Math.min(u.lineWidth || 0, 24) }),
                    u.dashStyle
                        ? (h.dashstyle = u.dashStyle)
                        : "square" !== u.linecap &&
                          (h["stroke-linecap"] = "round"));
                B.line = t.path().addClass("highcharts-graph").attr(h).add(m);
                h["stroke-linecap"] &&
                    (e = Math.min(B.line.strokeWidth(), v) / 2);
                v &&
                    B.line.attr({
                        d: [
                            ["M", e, a],
                            ["L", v - e, a],
                        ],
                    });
                g &&
                    !1 !== g.enabled &&
                    v &&
                    ((u = Math.min(L(g.radius, p), p)),
                    0 === this.symbol.indexOf("url") &&
                        ((g = I(g, { width: l, height: l })), (u = 0)),
                    (B.symbol = B =
                        t
                            .symbol(
                                this.symbol,
                                v / 2 - u,
                                a - u,
                                2 * u,
                                2 * u,
                                x({ context: "legend" }, g)
                            )
                            .addClass("highcharts-point")
                            .add(m)),
                    (B.isMarker = !0));
            };
            a.rectangle = function (a, x) {
                x = x.legendItem || {};
                const u = a.symbolHeight,
                    v = a.options.squareSymbol;
                x.symbol = this.chart.renderer
                    .rect(
                        v ? (a.symbolWidth - u) / 2 : 0,
                        a.baseline - u + 1,
                        v ? u : a.symbolWidth,
                        u,
                        L(a.options.symbolRadius, u / 2)
                    )
                    .addClass("highcharts-point")
                    .attr({ zIndex: 3 })
                    .add(x.group);
            };
        })(C || (C = {}));
        return C;
    });
    M(a, "Core/Series/SeriesDefaults.js", [], function () {
        return {
            lineWidth: 1,
            allowPointSelect: !1,
            crisp: !0,
            showCheckbox: !1,
            animation: { duration: 1e3 },
            enableMouseTracking: !0,
            events: {},
            marker: {
                enabledThreshold: 2,
                lineColor: "#ffffff",
                lineWidth: 0,
                radius: 4,
                states: {
                    normal: { animation: !0 },
                    hover: {
                        animation: { duration: 150 },
                        enabled: !0,
                        radiusPlus: 2,
                        lineWidthPlus: 1,
                    },
                    select: {
                        fillColor: "#cccccc",
                        lineColor: "#000000",
                        lineWidth: 2,
                    },
                },
            },
            point: { events: {} },
            dataLabels: {
                animation: {},
                align: "center",
                borderWidth: 0,
                defer: !0,
                formatter: function () {
                    const { numberFormatter: a } = this.series.chart;
                    return "number" !== typeof this.y ? "" : a(this.y, -1);
                },
                padding: 5,
                style: {
                    fontSize: "0.7em",
                    fontWeight: "bold",
                    color: "contrast",
                    textOutline: "1px contrast",
                },
                verticalAlign: "bottom",
                x: 0,
                y: 0,
            },
            cropThreshold: 300,
            opacity: 1,
            pointRange: 0,
            softThreshold: !0,
            states: {
                normal: { animation: !0 },
                hover: {
                    animation: { duration: 150 },
                    lineWidthPlus: 1,
                    marker: {},
                    halo: { size: 10, opacity: 0.25 },
                },
                select: { animation: { duration: 0 } },
                inactive: { animation: { duration: 150 }, opacity: 0.2 },
            },
            stickyTracking: !0,
            turboThreshold: 1e3,
            findNearestPointBy: "x",
        };
    });
    M(
        a,
        "Core/Series/SeriesRegistry.js",
        [
            a["Core/Globals.js"],
            a["Core/Defaults.js"],
            a["Core/Series/Point.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L) {
            const { defaultOptions: x } = y,
                { extendClass: z, merge: H } = L;
            var B;
            (function (u) {
                function v(a, p) {
                    const l = x.plotOptions || {},
                        m = p.defaultOptions,
                        h = p.prototype;
                    h.type = a;
                    h.pointClass || (h.pointClass = I);
                    m && (l[a] = m);
                    u.seriesTypes[a] = p;
                }
                u.seriesTypes = a.seriesTypes;
                u.registerSeriesType = v;
                u.seriesType = function (a, p, t, m, h) {
                    const g = x.plotOptions || {};
                    p = p || "";
                    g[a] = H(g[p], t);
                    v(a, z(u.seriesTypes[p] || function () {}, m));
                    u.seriesTypes[a].prototype.type = a;
                    h && (u.seriesTypes[a].prototype.pointClass = z(I, h));
                    return u.seriesTypes[a];
                };
            })(B || (B = {}));
            return B;
        }
    );
    M(
        a,
        "Core/Series/Series.js",
        [
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Defaults.js"],
            a["Core/Foundation.js"],
            a["Core/Globals.js"],
            a["Core/Legend/LegendSymbol.js"],
            a["Core/Series/Point.js"],
            a["Core/Series/SeriesDefaults.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Renderer/SVG/SVGElement.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C, z, H, B, u, v) {
            const { animObject: l, setAnimation: p } = a,
                { defaultOptions: t } = y,
                { registerEventOptions: m } = I,
                { hasTouch: h, svg: g, win: e } = L,
                { seriesTypes: w } = B,
                {
                    arrayMax: x,
                    arrayMin: F,
                    clamp: d,
                    correctFloat: k,
                    defined: r,
                    diffObjects: q,
                    erase: G,
                    error: b,
                    extend: f,
                    find: c,
                    fireEvent: n,
                    getClosestDistance: P,
                    getNestedProperty: D,
                    insertItem: K,
                    isArray: X,
                    isNumber: T,
                    isString: Z,
                    merge: V,
                    objectEach: Y,
                    pick: A,
                    removeEvent: M,
                    splat: ia,
                    syncTimeout: ba,
                } = v;
            class aa {
                constructor() {
                    this.zones =
                        this.yAxis =
                        this.xAxis =
                        this.userOptions =
                        this.tooltipOptions =
                        this.processedYData =
                        this.processedXData =
                        this.points =
                        this.options =
                        this.linkedSeries =
                        this.index =
                        this.eventsToUnbind =
                        this.eventOptions =
                        this.data =
                        this.chart =
                        this._i =
                            void 0;
                }
                init(b, a) {
                    n(this, "init", { options: a });
                    const c = this,
                        d = b.series;
                    this.eventsToUnbind = [];
                    c.chart = b;
                    c.options = c.setOptions(a);
                    a = c.options;
                    c.linkedSeries = [];
                    c.bindAxes();
                    f(c, {
                        name: a.name,
                        state: "",
                        visible: !1 !== a.visible,
                        selected: !0 === a.selected,
                    });
                    m(this, a);
                    const e = a.events;
                    if (
                        (e && e.click) ||
                        (a.point && a.point.events && a.point.events.click) ||
                        a.allowPointSelect
                    )
                        b.runTrackerClick = !0;
                    c.getColor();
                    c.getSymbol();
                    c.parallelArrays.forEach(function (b) {
                        c[b + "Data"] || (c[b + "Data"] = []);
                    });
                    c.isCartesian && (b.hasCartesianSeries = !0);
                    let g;
                    d.length && (g = d[d.length - 1]);
                    c._i = A(g && g._i, -1) + 1;
                    c.opacity = c.options.opacity;
                    b.orderItems("series", K(this, d));
                    a.dataSorting && a.dataSorting.enabled
                        ? c.setDataSortingOptions()
                        : c.points || c.data || c.setData(a.data, !1);
                    n(this, "afterInit");
                }
                is(b) {
                    return w[b] && this instanceof w[b];
                }
                bindAxes() {
                    const a = this,
                        c = a.options,
                        d = a.chart;
                    let f;
                    n(this, "bindAxes", null, function () {
                        (a.axisTypes || []).forEach(function (e) {
                            d[e].forEach(function (b) {
                                f = b.options;
                                if (
                                    A(c[e], 0) === b.index ||
                                    ("undefined" !== typeof c[e] &&
                                        c[e] === f.id)
                                )
                                    K(a, b.series),
                                        (a[e] = b),
                                        (b.isDirty = !0);
                            });
                            a[e] || a.optionalAxis === e || b(18, !0, d);
                        });
                    });
                    n(this, "afterBindAxes");
                }
                updateParallelArrays(b, a, c) {
                    const d = b.series,
                        f = T(a)
                            ? function (c) {
                                  const f =
                                      "y" === c && d.toYData
                                          ? d.toYData(b)
                                          : b[c];
                                  d[c + "Data"][a] = f;
                              }
                            : function (b) {
                                  Array.prototype[a].apply(d[b + "Data"], c);
                              };
                    d.parallelArrays.forEach(f);
                }
                hasData() {
                    return (
                        (this.visible &&
                            "undefined" !== typeof this.dataMax &&
                            "undefined" !== typeof this.dataMin) ||
                        (this.visible && this.yData && 0 < this.yData.length)
                    );
                }
                autoIncrement(b) {
                    var a = this.options;
                    const c = a.pointIntervalUnit,
                        d = a.relativeXValue,
                        f = this.chart.time;
                    let e = this.xIncrement,
                        g;
                    e = A(e, a.pointStart, 0);
                    this.pointInterval = g = A(
                        this.pointInterval,
                        a.pointInterval,
                        1
                    );
                    d && T(b) && (g *= b);
                    c &&
                        ((a = new f.Date(e)),
                        "day" === c
                            ? f.set("Date", a, f.get("Date", a) + g)
                            : "month" === c
                            ? f.set("Month", a, f.get("Month", a) + g)
                            : "year" === c &&
                              f.set("FullYear", a, f.get("FullYear", a) + g),
                        (g = a.getTime() - e));
                    if (d && T(b)) return e + g;
                    this.xIncrement = e + g;
                    return e;
                }
                setDataSortingOptions() {
                    const b = this.options;
                    f(this, {
                        requireSorting: !1,
                        sorted: !1,
                        enabledDataSorting: !0,
                        allowDG: !1,
                    });
                    r(b.pointRange) || (b.pointRange = 1);
                }
                setOptions(b) {
                    var a, c;
                    const d = this.chart;
                    var f = d.options.plotOptions,
                        e = d.userOptions || {};
                    const g = V(b);
                    b = d.styledMode;
                    const k = { plotOptions: f, userOptions: g };
                    n(this, "setOptions", k);
                    const h = k.plotOptions[this.type];
                    e = e.plotOptions || {};
                    const l = e.series || {},
                        m = t.plotOptions[this.type] || {},
                        q = e[this.type] || {};
                    this.userOptions = k.userOptions;
                    f = V(h, f.series, q, g);
                    this.tooltipOptions = V(
                        t.tooltip,
                        null === (a = t.plotOptions.series) || void 0 === a
                            ? void 0
                            : a.tooltip,
                        null === m || void 0 === m ? void 0 : m.tooltip,
                        d.userOptions.tooltip,
                        null === (c = e.series) || void 0 === c
                            ? void 0
                            : c.tooltip,
                        q.tooltip,
                        g.tooltip
                    );
                    this.stickyTracking = A(
                        g.stickyTracking,
                        q.stickyTracking,
                        l.stickyTracking,
                        this.tooltipOptions.shared && !this.noSharedTooltip
                            ? !0
                            : f.stickyTracking
                    );
                    null === h.marker && delete f.marker;
                    this.zoneAxis = f.zoneAxis;
                    c = this.zones = (f.zones || []).slice();
                    (!f.negativeColor && !f.negativeFillColor) ||
                        f.zones ||
                        ((a = {
                            value:
                                f[this.zoneAxis + "Threshold"] ||
                                f.threshold ||
                                0,
                            className: "highcharts-negative",
                        }),
                        b ||
                            ((a.color = f.negativeColor),
                            (a.fillColor = f.negativeFillColor)),
                        c.push(a));
                    c.length &&
                        r(c[c.length - 1].value) &&
                        c.push(
                            b
                                ? {}
                                : {
                                      color: this.color,
                                      fillColor: this.fillColor,
                                  }
                        );
                    n(this, "afterSetOptions", { options: f });
                    return f;
                }
                getName() {
                    return A(this.options.name, "Series " + (this.index + 1));
                }
                getCyclic(b, a, c) {
                    const d = this.chart,
                        f = `${b}Index`,
                        e = `${b}Counter`,
                        g =
                            (null === c || void 0 === c ? void 0 : c.length) ||
                            d.options.chart.colorCount;
                    if (!a) {
                        var k = A(
                            "color" === b ? this.options.colorIndex : void 0,
                            this[f]
                        );
                        r(k) ||
                            (d.series.length || (d[e] = 0),
                            (k = d[e] % g),
                            (d[e] += 1));
                        c && (a = c[k]);
                    }
                    "undefined" !== typeof k && (this[f] = k);
                    this[b] = a;
                }
                getColor() {
                    this.chart.styledMode
                        ? this.getCyclic("color")
                        : this.options.colorByPoint
                        ? (this.color = "#cccccc")
                        : this.getCyclic(
                              "color",
                              this.options.color ||
                                  t.plotOptions[this.type].color,
                              this.chart.options.colors
                          );
                }
                getPointsCollection() {
                    return (
                        (this.hasGroupedData ? this.points : this.data) || []
                    );
                }
                getSymbol() {
                    this.getCyclic(
                        "symbol",
                        this.options.marker.symbol,
                        this.chart.options.symbols
                    );
                }
                findPointIndex(b, a) {
                    const d = b.id,
                        f = b.x,
                        e = this.points;
                    var g = this.options.dataSorting,
                        k;
                    let h, n;
                    if (d) (g = this.chart.get(d)), g instanceof z && (k = g);
                    else if (
                        this.linkedParent ||
                        this.enabledDataSorting ||
                        this.options.relativeXValue
                    )
                        if (
                            ((k = (a) => !a.touched && a.index === b.index),
                            g && g.matchByName
                                ? (k = (a) => !a.touched && a.name === b.name)
                                : this.options.relativeXValue &&
                                  (k = (a) =>
                                      !a.touched && a.options.x === b.x),
                            (k = c(e, k)),
                            !k)
                        )
                            return;
                    k &&
                        ((n = k && k.index),
                        "undefined" !== typeof n && (h = !0));
                    "undefined" === typeof n &&
                        T(f) &&
                        (n = this.xData.indexOf(f, a));
                    -1 !== n &&
                        "undefined" !== typeof n &&
                        this.cropped &&
                        (n = n >= this.cropStart ? n - this.cropStart : n);
                    !h && T(n) && e[n] && e[n].touched && (n = void 0);
                    return n;
                }
                updateData(b, a) {
                    const c = this.options,
                        d = c.dataSorting,
                        f = this.points,
                        e = [],
                        g = this.requireSorting,
                        k = b.length === f.length;
                    let n,
                        h,
                        l,
                        m = !0;
                    this.xIncrement = null;
                    b.forEach(function (b, a) {
                        var h =
                            (r(b) &&
                                this.pointClass.prototype.optionsToObject.call(
                                    { series: this },
                                    b
                                )) ||
                            {};
                        const m = h.x;
                        if (h.id || T(m)) {
                            if (
                                ((h = this.findPointIndex(h, l)),
                                -1 === h || "undefined" === typeof h
                                    ? e.push(b)
                                    : f[h] && b !== c.data[h]
                                    ? (f[h].update(b, !1, null, !1),
                                      (f[h].touched = !0),
                                      g && (l = h + 1))
                                    : f[h] && (f[h].touched = !0),
                                !k ||
                                    a !== h ||
                                    (d && d.enabled) ||
                                    this.hasDerivedData)
                            )
                                n = !0;
                        } else e.push(b);
                    }, this);
                    if (n)
                        for (b = f.length; b--; )
                            (h = f[b]) &&
                                !h.touched &&
                                h.remove &&
                                h.remove(!1, a);
                    else
                        !k || (d && d.enabled)
                            ? (m = !1)
                            : (b.forEach(function (b, a) {
                                  b === f[a].y ||
                                      f[a].destroyed ||
                                      f[a].update(b, !1, null, !1);
                              }),
                              (e.length = 0));
                    f.forEach(function (b) {
                        b && (b.touched = !1);
                    });
                    if (!m) return !1;
                    e.forEach(function (b) {
                        this.addPoint(b, !1, null, null, !1);
                    }, this);
                    null === this.xIncrement &&
                        this.xData &&
                        this.xData.length &&
                        ((this.xIncrement = x(this.xData)),
                        this.autoIncrement());
                    return !0;
                }
                setData(a, c = !0, d, f) {
                    var e;
                    const g = this,
                        k = g.points,
                        h = (k && k.length) || 0,
                        n = g.options,
                        l = g.chart,
                        m = n.dataSorting,
                        q = g.xAxis,
                        p = n.turboThreshold,
                        r = this.xData,
                        t = this.yData;
                    var w = g.pointArrayMap;
                    w = w && w.length;
                    const J = n.keys;
                    let v,
                        u = 0,
                        O = 1,
                        x = null;
                    if (!l.options.chart.allowMutatingData) {
                        n.data && delete g.options.data;
                        g.userOptions.data && delete g.userOptions.data;
                        var N = V(!0, a);
                    }
                    a = N || a || [];
                    N = a.length;
                    m && m.enabled && (a = this.sortData(a));
                    l.options.chart.allowMutatingData &&
                        !1 !== f &&
                        N &&
                        h &&
                        !g.cropped &&
                        !g.hasGroupedData &&
                        g.visible &&
                        !g.boosted &&
                        (v = this.updateData(a, d));
                    if (!v) {
                        g.xIncrement = null;
                        g.colorCounter = 0;
                        this.parallelArrays.forEach(function (b) {
                            g[b + "Data"].length = 0;
                        });
                        if (p && N > p)
                            if (((x = g.getFirstValidPoint(a)), T(x)))
                                for (d = 0; d < N; d++)
                                    (r[d] = this.autoIncrement()),
                                        (t[d] = a[d]);
                            else if (X(x))
                                if (w)
                                    if (x.length === w)
                                        for (d = 0; d < N; d++)
                                            (r[d] = this.autoIncrement()),
                                                (t[d] = a[d]);
                                    else
                                        for (d = 0; d < N; d++)
                                            (f = a[d]),
                                                (r[d] = f[0]),
                                                (t[d] = f.slice(1, w + 1));
                                else if (
                                    (J &&
                                        ((u = J.indexOf("x")),
                                        (O = J.indexOf("y")),
                                        (u = 0 <= u ? u : 0),
                                        (O = 0 <= O ? O : 1)),
                                    1 === x.length && (O = 0),
                                    u === O)
                                )
                                    for (d = 0; d < N; d++)
                                        (r[d] = this.autoIncrement()),
                                            (t[d] = a[d][O]);
                                else
                                    for (d = 0; d < N; d++)
                                        (f = a[d]),
                                            (r[d] = f[u]),
                                            (t[d] = f[O]);
                            else b(12, !1, l);
                        else
                            for (d = 0; d < N; d++)
                                (f = { series: g }),
                                    g.pointClass.prototype.applyOptions.apply(
                                        f,
                                        [a[d]]
                                    ),
                                    g.updateParallelArrays(f, d);
                        t && Z(t[0]) && b(14, !0, l);
                        g.data = [];
                        g.options.data = g.userOptions.data = a;
                        for (d = h; d--; )
                            null === (e = k[d]) || void 0 === e
                                ? void 0
                                : e.destroy();
                        q && (q.minRange = q.userMinRange);
                        g.isDirty = l.isDirtyBox = !0;
                        g.isDirtyData = !!k;
                        d = !1;
                    }
                    "point" === n.legendType &&
                        (this.processData(), this.generatePoints());
                    c && l.redraw(d);
                }
                sortData(b) {
                    const a = this,
                        c = a.options.dataSorting.sortKey || "y",
                        d = function (b, a) {
                            return (
                                (r(a) &&
                                    b.pointClass.prototype.optionsToObject.call(
                                        { series: b },
                                        a
                                    )) ||
                                {}
                            );
                        };
                    b.forEach(function (c, f) {
                        b[f] = d(a, c);
                        b[f].index = f;
                    }, this);
                    b.concat()
                        .sort((b, a) => {
                            b = D(c, b);
                            a = D(c, a);
                            return a < b ? -1 : a > b ? 1 : 0;
                        })
                        .forEach(function (b, a) {
                            b.x = a;
                        }, this);
                    a.linkedSeries &&
                        a.linkedSeries.forEach(function (a) {
                            const c = a.options,
                                f = c.data;
                            (c.dataSorting && c.dataSorting.enabled) ||
                                !f ||
                                (f.forEach(function (c, e) {
                                    f[e] = d(a, c);
                                    b[e] &&
                                        ((f[e].x = b[e].x), (f[e].index = e));
                                }),
                                a.setData(f, !1));
                        });
                    return b;
                }
                getProcessedData(a) {
                    const c = this;
                    var d = c.xAxis,
                        f = c.options;
                    const e = f.cropThreshold,
                        g = a || c.getExtremesFromAll || f.getExtremesFromAll,
                        k = null === d || void 0 === d ? void 0 : d.logarithmic,
                        h = c.isCartesian;
                    let n = 0;
                    let l;
                    a = c.xData;
                    f = c.yData;
                    let m = !1;
                    const q = a.length;
                    if (d) {
                        var r = d.getExtremes();
                        l = r.min;
                        r = r.max;
                        m = !(!d.categories || d.names.length);
                    }
                    if (h && c.sorted && !g && (!e || q > e || c.forceCrop))
                        if (a[q - 1] < l || a[0] > r) (a = []), (f = []);
                        else if (c.yData && (a[0] < l || a[q - 1] > r)) {
                            var p = this.cropData(c.xData, c.yData, l, r);
                            a = p.xData;
                            f = p.yData;
                            n = p.start;
                            p = !0;
                        }
                    d = P(
                        [k ? a.map(k.log2lin) : a],
                        () => c.requireSorting && !m && b(15, !1, c.chart)
                    );
                    return {
                        xData: a,
                        yData: f,
                        cropped: p,
                        cropStart: n,
                        closestPointRange: d,
                    };
                }
                processData(b) {
                    const a = this.xAxis;
                    if (
                        this.isCartesian &&
                        !this.isDirty &&
                        !a.isDirty &&
                        !this.yAxis.isDirty &&
                        !b
                    )
                        return !1;
                    b = this.getProcessedData();
                    this.cropped = b.cropped;
                    this.cropStart = b.cropStart;
                    this.processedXData = b.xData;
                    this.processedYData = b.yData;
                    this.closestPointRange = this.basePointRange =
                        b.closestPointRange;
                    n(this, "afterProcessData");
                }
                cropData(b, a, c, d, f) {
                    const e = b.length;
                    let g,
                        k = 0,
                        h = e;
                    f = A(f, this.cropShoulder);
                    for (g = 0; g < e; g++)
                        if (b[g] >= c) {
                            k = Math.max(0, g - f);
                            break;
                        }
                    for (c = g; c < e; c++)
                        if (b[c] > d) {
                            h = c + f;
                            break;
                        }
                    return {
                        xData: b.slice(k, h),
                        yData: a.slice(k, h),
                        start: k,
                        end: h,
                    };
                }
                generatePoints() {
                    var b = this.options;
                    const a = this.processedData || b.data,
                        c = this.processedXData,
                        d = this.processedYData,
                        e = this.pointClass,
                        g = c.length,
                        k = this.cropStart || 0,
                        h = this.hasGroupedData,
                        l = b.keys,
                        m = [];
                    b = b.dataGrouping && b.dataGrouping.groupAll ? k : 0;
                    let q;
                    let r,
                        p,
                        t = this.data;
                    if (!t && !h) {
                        var w = [];
                        w.length = a.length;
                        t = this.data = w;
                    }
                    l && h && (this.options.keys = !1);
                    for (p = 0; p < g; p++)
                        (w = k + p),
                            h
                                ? ((r = new e().init(
                                      this,
                                      [c[p]].concat(ia(d[p]))
                                  )),
                                  (r.dataGroup = this.groupMap[b + p]),
                                  r.dataGroup.options &&
                                      ((r.options = r.dataGroup.options),
                                      f(r, r.dataGroup.options),
                                      delete r.dataLabels))
                                : (r = t[w]) ||
                                  "undefined" === typeof a[w] ||
                                  (t[w] = r = new e().init(this, a[w], c[p])),
                            r && ((r.index = h ? b + p : w), (m[p] = r));
                    this.options.keys = l;
                    if (t && (g !== (q = t.length) || h))
                        for (p = 0; p < q; p++)
                            p !== k || h || (p += g),
                                t[p] &&
                                    (t[p].destroyElements(),
                                    (t[p].plotX = void 0));
                    this.data = t;
                    this.points = m;
                    n(this, "afterGeneratePoints");
                }
                getXExtremes(b) {
                    return { min: F(b), max: x(b) };
                }
                getExtremes(b, a) {
                    const c = this.xAxis;
                    var d = this.yAxis;
                    const f = this.processedXData || this.xData,
                        e = [],
                        g = this.requireSorting ? this.cropShoulder : 0;
                    d = d ? d.positiveValuesOnly : !1;
                    let k,
                        h = 0,
                        l = 0,
                        m = 0;
                    b = b || this.stackedYData || this.processedYData || [];
                    const q = b.length;
                    if (c) {
                        var p = c.getExtremes();
                        h = p.min;
                        l = p.max;
                    }
                    for (k = 0; k < q; k++) {
                        var r = f[k];
                        p = b[k];
                        var t = (T(p) || X(p)) && (p.length || 0 < p || !d);
                        r =
                            a ||
                            this.getExtremesFromAll ||
                            this.options.getExtremesFromAll ||
                            this.cropped ||
                            !c ||
                            ((f[k + g] || r) >= h && (f[k - g] || r) <= l);
                        if (t && r)
                            if ((t = p.length))
                                for (; t--; ) T(p[t]) && (e[m++] = p[t]);
                            else e[m++] = p;
                    }
                    b = { activeYData: e, dataMin: F(e), dataMax: x(e) };
                    n(this, "afterGetExtremes", { dataExtremes: b });
                    return b;
                }
                applyExtremes() {
                    const b = this.getExtremes();
                    this.dataMin = b.dataMin;
                    this.dataMax = b.dataMax;
                    return b;
                }
                getFirstValidPoint(b) {
                    const a = b.length;
                    let c = 0,
                        d = null;
                    for (; null === d && c < a; ) (d = b[c]), c++;
                    return d;
                }
                translate() {
                    var b;
                    this.processedXData || this.processData();
                    this.generatePoints();
                    var a = this.options;
                    const c = a.stacking,
                        f = this.xAxis,
                        e = f.categories,
                        g = this.enabledDataSorting,
                        h = this.yAxis,
                        l = this.points,
                        m = l.length,
                        q = this.pointPlacementToXValue(),
                        p = !!q,
                        t = a.threshold;
                    a = a.startFromThreshold ? t : 0;
                    let w,
                        v,
                        u,
                        x,
                        D = Number.MAX_VALUE;
                    for (w = 0; w < m; w++) {
                        const n = l[w],
                            m = n.x;
                        let J,
                            Q,
                            R = n.y,
                            O = n.low;
                        const E =
                            c &&
                            (null === (b = h.stacking) || void 0 === b
                                ? void 0
                                : b.stacks[
                                      (this.negStacks && R < (a ? 0 : t)
                                          ? "-"
                                          : "") + this.stackKey
                                  ]);
                        v = f.translate(m, !1, !1, !1, !0, q);
                        n.plotX = T(v) ? k(d(v, -1e5, 1e5)) : void 0;
                        c &&
                            this.visible &&
                            E &&
                            E[m] &&
                            ((x = this.getStackIndicator(x, m, this.index)),
                            !n.isNull &&
                                x.key &&
                                ((J = E[m]), (Q = J.points[x.key])),
                            J &&
                                X(Q) &&
                                ((O = Q[0]),
                                (R = Q[1]),
                                O === a &&
                                    x.key === E[m].base &&
                                    (O = A(T(t) ? t : h.min)),
                                h.positiveValuesOnly &&
                                    r(O) &&
                                    0 >= O &&
                                    (O = void 0),
                                (n.total = n.stackTotal = A(J.total)),
                                (n.percentage =
                                    r(n.y) && J.total
                                        ? (n.y / J.total) * 100
                                        : void 0),
                                (n.stackY = R),
                                this.irregularWidths ||
                                    J.setOffset(
                                        this.pointXOffset || 0,
                                        this.barW || 0,
                                        void 0,
                                        void 0,
                                        void 0,
                                        this.xAxis
                                    )));
                        n.yBottom = r(O)
                            ? d(h.translate(O, !1, !0, !1, !0), -1e5, 1e5)
                            : void 0;
                        this.dataModify &&
                            (R = this.dataModify.modifyValue(R, w));
                        let N;
                        T(R) &&
                            void 0 !== n.plotX &&
                            ((N = h.translate(R, !1, !0, !1, !0)),
                            (N = T(N) ? d(N, -1e5, 1e5) : void 0));
                        n.plotY = N;
                        n.isInside = this.isPointInside(n);
                        n.clientX = p
                            ? k(f.translate(m, !1, !1, !1, !0, q))
                            : v;
                        n.negative = (n.y || 0) < (t || 0);
                        n.category = A(e && e[n.x], n.x);
                        n.isNull ||
                            !1 === n.visible ||
                            ("undefined" !== typeof u &&
                                (D = Math.min(D, Math.abs(v - u))),
                            (u = v));
                        n.zone = this.zones.length ? n.getZone() : void 0;
                        !n.graphic && this.group && g && (n.isNew = !0);
                    }
                    this.closestPointRangePx = D;
                    n(this, "afterTranslate");
                }
                getValidPoints(b, a, c) {
                    const d = this.chart;
                    return (b || this.points || []).filter(function (b) {
                        const { plotX: f, plotY: e } = b;
                        return (!c && (b.isNull || !T(e))) ||
                            (a &&
                                !d.isInsidePlot(f, e, { inverted: d.inverted }))
                            ? !1
                            : !1 !== b.visible;
                    });
                }
                getClipBox() {
                    const { chart: b, xAxis: a, yAxis: c } = this,
                        d = V(b.clipBox);
                    a && a.len !== b.plotSizeX && (d.width = a.len);
                    c && c.len !== b.plotSizeY && (d.height = c.len);
                    return d;
                }
                getSharedClipKey() {
                    return (this.sharedClipKey =
                        (this.options.xAxis || 0) +
                        "," +
                        (this.options.yAxis || 0));
                }
                setClip() {
                    const { chart: b, group: a, markerGroup: c } = this,
                        d = b.sharedClips,
                        f = b.renderer,
                        e = this.getClipBox(),
                        g = this.getSharedClipKey();
                    let k = d[g];
                    k ? k.animate(e) : (d[g] = k = f.clipRect(e));
                    a && a.clip(!1 === this.options.clip ? void 0 : k);
                    c && c.clip();
                }
                animate(b) {
                    const { chart: a, group: c, markerGroup: d } = this,
                        f = a.inverted;
                    var e = l(this.options.animation),
                        g = [
                            this.getSharedClipKey(),
                            e.duration,
                            e.easing,
                            e.defer,
                        ].join();
                    let k = a.sharedClips[g],
                        n = a.sharedClips[g + "m"];
                    if (b && c)
                        (e = this.getClipBox()),
                            k
                                ? k.attr("height", e.height)
                                : ((e.width = 0),
                                  f && (e.x = a.plotHeight),
                                  (k = a.renderer.clipRect(e)),
                                  (a.sharedClips[g] = k),
                                  (n = a.renderer.clipRect({
                                      x: -99,
                                      y: -99,
                                      width: f ? a.plotWidth + 199 : 99,
                                      height: f ? 99 : a.plotHeight + 199,
                                  })),
                                  (a.sharedClips[g + "m"] = n)),
                            c.clip(k),
                            d && d.clip(n);
                    else if (k && !k.hasClass("highcharts-animating")) {
                        g = this.getClipBox();
                        const b = e.step;
                        d &&
                            d.element.childNodes.length &&
                            (e.step = function (a, c) {
                                b && b.apply(c, arguments);
                                "width" === c.prop &&
                                    n &&
                                    n.element &&
                                    n.attr(f ? "height" : "width", a + 99);
                            });
                        k.addClass("highcharts-animating").animate(g, e);
                    }
                }
                afterAnimate() {
                    this.setClip();
                    Y(this.chart.sharedClips, (b, a, c) => {
                        b &&
                            !this.chart.container.querySelector(
                                `[clip-path="url(#${b.id})"]`
                            ) &&
                            (b.destroy(), delete c[a]);
                    });
                    this.finishedAnimating = !0;
                    n(this, "afterAnimate");
                }
                drawPoints(b = this.points) {
                    const a = this.chart,
                        c = a.styledMode,
                        { colorAxis: d, options: f } = this,
                        e = f.marker,
                        g = this[this.specialGroup || "markerGroup"],
                        k = this.xAxis,
                        n = A(
                            e.enabled,
                            !k || k.isRadial ? !0 : null,
                            this.closestPointRangePx >=
                                e.enabledThreshold * e.radius
                        );
                    let h, l, m, q;
                    let p, r;
                    if (!1 !== e.enabled || this._hasPointMarkers)
                        for (h = 0; h < b.length; h++) {
                            l = b[h];
                            q = (m = l.graphic) ? "animate" : "attr";
                            var t = l.marker || {};
                            p = !!l.marker;
                            if (
                                ((n && "undefined" === typeof t.enabled) ||
                                    t.enabled) &&
                                !l.isNull &&
                                !1 !== l.visible
                            ) {
                                const b = A(t.symbol, this.symbol, "rect");
                                r = this.markerAttribs(
                                    l,
                                    l.selected && "select"
                                );
                                this.enabledDataSorting &&
                                    (l.startXPos = k.reversed
                                        ? -(r.width || 0)
                                        : k.width);
                                const f = !1 !== l.isInside;
                                !m &&
                                    f &&
                                    (0 < (r.width || 0) || l.hasImage) &&
                                    ((l.graphic = m =
                                        a.renderer
                                            .symbol(
                                                b,
                                                r.x,
                                                r.y,
                                                r.width,
                                                r.height,
                                                p ? t : e
                                            )
                                            .add(g)),
                                    this.enabledDataSorting &&
                                        a.hasRendered &&
                                        (m.attr({ x: l.startXPos }),
                                        (q = "animate")));
                                m &&
                                    "animate" === q &&
                                    m[f ? "show" : "hide"](f).animate(r);
                                if (m)
                                    if (
                                        ((t = this.pointAttribs(
                                            l,
                                            c || !l.selected ? void 0 : "select"
                                        )),
                                        c)
                                    )
                                        d && m.css({ fill: t.fill });
                                    else m[q](t);
                                m && m.addClass(l.getClassName(), !0);
                            } else m && (l.graphic = m.destroy());
                        }
                }
                markerAttribs(b, a) {
                    const c = this.options;
                    var d = c.marker;
                    const f = b.marker || {},
                        e = f.symbol || d.symbol,
                        g = {};
                    let k = A(f.radius, d && d.radius);
                    a &&
                        ((d = d.states[a]),
                        (a = f.states && f.states[a]),
                        (k = A(
                            a && a.radius,
                            d && d.radius,
                            k && k + ((d && d.radiusPlus) || 0)
                        )));
                    b.hasImage = e && 0 === e.indexOf("url");
                    b.hasImage && (k = 0);
                    b = b.pos();
                    T(k) &&
                        b &&
                        ((g.x = b[0] - k),
                        (g.y = b[1] - k),
                        c.crisp && (g.x = Math.floor(g.x)));
                    k && (g.width = g.height = 2 * k);
                    return g;
                }
                pointAttribs(b, a) {
                    var c = this.options.marker,
                        d = b && b.options;
                    const f = (d && d.marker) || {};
                    var e = d && d.color,
                        g = b && b.color;
                    const k = b && b.zone && b.zone.color;
                    let n = this.color;
                    b = A(f.lineWidth, c.lineWidth);
                    d = 1;
                    n = e || k || g || n;
                    e = f.fillColor || c.fillColor || n;
                    g = f.lineColor || c.lineColor || n;
                    a = a || "normal";
                    c = c.states[a] || {};
                    a = (f.states && f.states[a]) || {};
                    b = A(
                        a.lineWidth,
                        c.lineWidth,
                        b + A(a.lineWidthPlus, c.lineWidthPlus, 0)
                    );
                    e = a.fillColor || c.fillColor || e;
                    g = a.lineColor || c.lineColor || g;
                    d = A(a.opacity, c.opacity, d);
                    return {
                        stroke: g,
                        "stroke-width": b,
                        fill: e,
                        opacity: d,
                    };
                }
                destroy(b) {
                    const a = this,
                        c = a.chart,
                        d = /AppleWebKit\/533/.test(e.navigator.userAgent),
                        f = a.data || [];
                    let g, k, h, l;
                    n(a, "destroy", { keepEventsForUpdate: b });
                    this.removeEvents(b);
                    (a.axisTypes || []).forEach(function (b) {
                        (l = a[b]) &&
                            l.series &&
                            (G(l.series, a), (l.isDirty = l.forceRedraw = !0));
                    });
                    a.legendItem && a.chart.legend.destroyItem(a);
                    for (k = f.length; k--; )
                        (h = f[k]) && h.destroy && h.destroy();
                    a.clips && a.clips.forEach((b) => b.destroy());
                    v.clearTimeout(a.animationTimeout);
                    Y(a, function (b, a) {
                        b instanceof u &&
                            !b.survive &&
                            ((g = d && "group" === a ? "hide" : "destroy"),
                            b[g]());
                    });
                    c.hoverSeries === a && (c.hoverSeries = void 0);
                    G(c.series, a);
                    c.orderItems("series");
                    Y(a, function (c, d) {
                        (b && "hcEvents" === d) || delete a[d];
                    });
                }
                applyZones() {
                    const b = this,
                        a = this.chart,
                        c = a.renderer,
                        f = this.zones,
                        e = this.clips || [],
                        g = this.graph,
                        k = this.area,
                        n = Math.max(a.plotWidth, a.plotHeight),
                        h = this[(this.zoneAxis || "y") + "Axis"],
                        l = a.inverted;
                    let m,
                        q,
                        p,
                        r,
                        t,
                        w,
                        v,
                        u,
                        x,
                        D,
                        E,
                        G = !1;
                    f.length && (g || k) && h && "undefined" !== typeof h.min
                        ? ((t = h.reversed),
                          (w = h.horiz),
                          g && !this.showLine && g.hide(),
                          k && k.hide(),
                          (r = h.getExtremes()),
                          f.forEach(function (f, Q) {
                              m = t
                                  ? w
                                      ? a.plotWidth
                                      : 0
                                  : w
                                  ? 0
                                  : h.toPixels(r.min) || 0;
                              m = d(A(q, m), 0, n);
                              q = d(
                                  Math.round(
                                      h.toPixels(A(f.value, r.max), !0) || 0
                                  ),
                                  0,
                                  n
                              );
                              G && (m = q = h.toPixels(r.max));
                              v = Math.abs(m - q);
                              u = Math.min(m, q);
                              x = Math.max(m, q);
                              h.isXAxis
                                  ? ((p = {
                                        x: l ? x : u,
                                        y: 0,
                                        width: v,
                                        height: n,
                                    }),
                                    w || (p.x = a.plotHeight - p.x))
                                  : ((p = {
                                        x: 0,
                                        y: l ? x : u,
                                        width: n,
                                        height: v,
                                    }),
                                    w && (p.y = a.plotWidth - p.y));
                              e[Q] ? e[Q].animate(p) : (e[Q] = c.clipRect(p));
                              D = b["zone-area-" + Q];
                              E = b["zone-graph-" + Q];
                              g && E && E.clip(e[Q]);
                              k && D && D.clip(e[Q]);
                              G = f.value > r.max;
                              b.resetZones && 0 === q && (q = void 0);
                          }),
                          (this.clips = e))
                        : b.visible && (g && g.show(), k && k.show());
                }
                plotGroup(b, a, c, d, f) {
                    let e = this[b];
                    const g = !e;
                    c = { visibility: c, zIndex: d || 0.1 };
                    "undefined" === typeof this.opacity ||
                        this.chart.styledMode ||
                        "inactive" === this.state ||
                        (c.opacity = this.opacity);
                    g && (this[b] = e = this.chart.renderer.g().add(f));
                    e.addClass(
                        "highcharts-" +
                            a +
                            " highcharts-series-" +
                            this.index +
                            " highcharts-" +
                            this.type +
                            "-series " +
                            (r(this.colorIndex)
                                ? "highcharts-color-" + this.colorIndex + " "
                                : "") +
                            (this.options.className || "") +
                            (e.hasClass("highcharts-tracker")
                                ? " highcharts-tracker"
                                : ""),
                        !0
                    );
                    e.attr(c)[g ? "attr" : "animate"](this.getPlotBox(a));
                    return e;
                }
                getPlotBox(b) {
                    let a = this.xAxis,
                        c = this.yAxis;
                    const d = this.chart;
                    b =
                        d.inverted &&
                        !d.polar &&
                        a &&
                        !1 !== this.invertible &&
                        "series" === b;
                    d.inverted && ((a = c), (c = this.xAxis));
                    return {
                        translateX: a ? a.left : d.plotLeft,
                        translateY: c ? c.top : d.plotTop,
                        rotation: b ? 90 : 0,
                        rotationOriginX: b ? (a.len - c.len) / 2 : 0,
                        rotationOriginY: b ? (a.len + c.len) / 2 : 0,
                        scaleX: b ? -1 : 1,
                        scaleY: 1,
                    };
                }
                removeEvents(b) {
                    b || M(this);
                    this.eventsToUnbind.length &&
                        (this.eventsToUnbind.forEach(function (b) {
                            b();
                        }),
                        (this.eventsToUnbind.length = 0));
                }
                render() {
                    const b = this;
                    var a = b.chart;
                    const c = b.options,
                        d = l(c.animation),
                        f = b.visible ? "inherit" : "hidden",
                        e = c.zIndex,
                        g = b.hasRendered;
                    a = a.seriesGroup;
                    let k = b.finishedAnimating ? 0 : d.duration;
                    n(this, "render");
                    b.plotGroup("group", "series", f, e, a);
                    b.markerGroup = b.plotGroup(
                        "markerGroup",
                        "markers",
                        f,
                        e,
                        a
                    );
                    !1 !== c.clip && b.setClip();
                    b.animate && k && b.animate(!0);
                    b.drawGraph && (b.drawGraph(), b.applyZones());
                    b.visible && b.drawPoints();
                    b.drawDataLabels && b.drawDataLabels();
                    b.redrawPoints && b.redrawPoints();
                    b.drawTracker && c.enableMouseTracking && b.drawTracker();
                    b.animate && k && b.animate();
                    g ||
                        (k && d.defer && (k += d.defer),
                        (b.animationTimeout = ba(function () {
                            b.afterAnimate();
                        }, k || 0)));
                    b.isDirty = !1;
                    b.hasRendered = !0;
                    n(b, "afterRender");
                }
                redraw() {
                    const b = this.isDirty || this.isDirtyData;
                    this.translate();
                    this.render();
                    b && delete this.kdTree;
                }
                searchPoint(b, a) {
                    const c = this.xAxis,
                        d = this.yAxis,
                        f = this.chart.inverted;
                    return this.searchKDTree(
                        {
                            clientX: f
                                ? c.len - b.chartY + c.pos
                                : b.chartX - c.pos,
                            plotY: f
                                ? d.len - b.chartX + d.pos
                                : b.chartY - d.pos,
                        },
                        a,
                        b
                    );
                }
                buildKDTree(b) {
                    function a(b, d, f) {
                        var e = b && b.length;
                        let g;
                        if (e)
                            return (
                                (g = c.kdAxisArray[d % f]),
                                b.sort(function (b, a) {
                                    return b[g] - a[g];
                                }),
                                (e = Math.floor(e / 2)),
                                {
                                    point: b[e],
                                    left: a(b.slice(0, e), d + 1, f),
                                    right: a(b.slice(e + 1), d + 1, f),
                                }
                            );
                    }
                    this.buildingKdTree = !0;
                    const c = this,
                        d =
                            -1 < c.options.findNearestPointBy.indexOf("y")
                                ? 2
                                : 1;
                    delete c.kdTree;
                    ba(
                        function () {
                            c.kdTree = a(
                                c.getValidPoints(null, !c.directTouch),
                                d,
                                d
                            );
                            c.buildingKdTree = !1;
                        },
                        c.options.kdNow || (b && "touchstart" === b.type)
                            ? 0
                            : 1
                    );
                }
                searchKDTree(b, a, c) {
                    function d(b, a, c, n) {
                        const h = a.point;
                        var l = f.kdAxisArray[c % n];
                        let m = h;
                        var q =
                            r(b[e]) && r(h[e])
                                ? Math.pow(b[e] - h[e], 2)
                                : null;
                        var p =
                            r(b[g]) && r(h[g])
                                ? Math.pow(b[g] - h[g], 2)
                                : null;
                        p = (q || 0) + (p || 0);
                        h.dist = r(p) ? Math.sqrt(p) : Number.MAX_VALUE;
                        h.distX = r(q) ? Math.sqrt(q) : Number.MAX_VALUE;
                        l = b[l] - h[l];
                        p = 0 > l ? "left" : "right";
                        q = 0 > l ? "right" : "left";
                        a[p] &&
                            ((p = d(b, a[p], c + 1, n)),
                            (m = p[k] < m[k] ? p : h));
                        a[q] &&
                            Math.sqrt(l * l) < m[k] &&
                            ((b = d(b, a[q], c + 1, n)),
                            (m = b[k] < m[k] ? b : m));
                        return m;
                    }
                    const f = this,
                        e = this.kdAxisArray[0],
                        g = this.kdAxisArray[1],
                        k = a ? "distX" : "dist";
                    a = -1 < f.options.findNearestPointBy.indexOf("y") ? 2 : 1;
                    this.kdTree || this.buildingKdTree || this.buildKDTree(c);
                    if (this.kdTree) return d(b, this.kdTree, a, a);
                }
                pointPlacementToXValue() {
                    const {
                        options: { pointPlacement: b, pointRange: a },
                        xAxis: c,
                    } = this;
                    let d = b;
                    "between" === d && (d = c.reversed ? -0.5 : 0.5);
                    return T(d) ? d * (a || c.pointRange) : 0;
                }
                isPointInside(b) {
                    const { chart: a, xAxis: c, yAxis: d } = this;
                    return (
                        "undefined" !== typeof b.plotY &&
                        "undefined" !== typeof b.plotX &&
                        0 <= b.plotY &&
                        b.plotY <= (d ? d.len : a.plotHeight) &&
                        0 <= b.plotX &&
                        b.plotX <= (c ? c.len : a.plotWidth)
                    );
                }
                drawTracker() {
                    const b = this,
                        a = b.options,
                        c = a.trackByArea,
                        d = [].concat(c ? b.areaPath : b.graphPath),
                        f = b.chart,
                        e = f.pointer,
                        k = f.renderer,
                        l = f.options.tooltip.snap,
                        m = b.tracker,
                        q = function (c) {
                            if (a.enableMouseTracking && f.hoverSeries !== b)
                                b.onMouseOver();
                        },
                        p = "rgba(192,192,192," + (g ? 0.0001 : 0.002) + ")";
                    m
                        ? m.attr({ d })
                        : b.graph &&
                          ((b.tracker = k
                              .path(d)
                              .attr({
                                  visibility: b.visible ? "inherit" : "hidden",
                                  zIndex: 2,
                              })
                              .addClass(
                                  c
                                      ? "highcharts-tracker-area"
                                      : "highcharts-tracker-line"
                              )
                              .add(b.group)),
                          f.styledMode ||
                              b.tracker.attr({
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  stroke: p,
                                  fill: c ? p : "none",
                                  "stroke-width":
                                      b.graph.strokeWidth() + (c ? 0 : 2 * l),
                              }),
                          [b.tracker, b.markerGroup, b.dataLabelsGroup].forEach(
                              function (b) {
                                  if (
                                      b &&
                                      (b
                                          .addClass("highcharts-tracker")
                                          .on("mouseover", q)
                                          .on("mouseout", function (b) {
                                              e.onTrackerMouseOut(b);
                                          }),
                                      a.cursor &&
                                          !f.styledMode &&
                                          b.css({ cursor: a.cursor }),
                                      h)
                                  )
                                      b.on("touchstart", q);
                              }
                          ));
                    n(this, "afterDrawTracker");
                }
                addPoint(b, a, c, d, f) {
                    const e = this.options,
                        g = this.data,
                        k = this.chart;
                    var h = this.xAxis;
                    h = h && h.hasNames && h.names;
                    const l = e.data,
                        m = this.xData;
                    let q, p;
                    a = A(a, !0);
                    const r = { series: this };
                    this.pointClass.prototype.applyOptions.apply(r, [b]);
                    const t = r.x;
                    p = m.length;
                    if (this.requireSorting && t < m[p - 1])
                        for (q = !0; p && m[p - 1] > t; ) p--;
                    this.updateParallelArrays(r, "splice", [p, 0, 0]);
                    this.updateParallelArrays(r, p);
                    h && r.name && (h[t] = r.name);
                    l.splice(p, 0, b);
                    if (q || this.processedData)
                        this.data.splice(p, 0, null), this.processData();
                    "point" === e.legendType && this.generatePoints();
                    c &&
                        (g[0] && g[0].remove
                            ? g[0].remove(!1)
                            : (g.shift(),
                              this.updateParallelArrays(r, "shift"),
                              l.shift()));
                    !1 !== f && n(this, "addPoint", { point: r });
                    this.isDirtyData = this.isDirty = !0;
                    a && k.redraw(d);
                }
                removePoint(b, a, c) {
                    const d = this,
                        f = d.data,
                        e = f[b],
                        g = d.points,
                        k = d.chart,
                        h = function () {
                            g && g.length === f.length && g.splice(b, 1);
                            f.splice(b, 1);
                            d.options.data.splice(b, 1);
                            d.updateParallelArrays(
                                e || { series: d },
                                "splice",
                                [b, 1]
                            );
                            e && e.destroy();
                            d.isDirty = !0;
                            d.isDirtyData = !0;
                            a && k.redraw();
                        };
                    p(c, k);
                    a = A(a, !0);
                    e ? e.firePointEvent("remove", null, h) : h();
                }
                remove(b, a, c, d) {
                    function f() {
                        e.destroy(d);
                        g.isDirtyLegend = g.isDirtyBox = !0;
                        g.linkSeries(d);
                        A(b, !0) && g.redraw(a);
                    }
                    const e = this,
                        g = e.chart;
                    !1 !== c ? n(e, "remove", null, f) : f();
                }
                update(a, c) {
                    a = q(a, this.userOptions);
                    n(this, "update", { options: a });
                    const d = this,
                        e = d.chart;
                    var g = d.userOptions;
                    const k = d.initialType || d.type;
                    var h = e.options.plotOptions;
                    const l = w[k].prototype;
                    var m = d.finishedAnimating && { animation: !1 };
                    const p = {};
                    let r,
                        t = [
                            "colorIndex",
                            "eventOptions",
                            "navigatorSeries",
                            "symbolIndex",
                            "baseSeries",
                        ],
                        v = a.type || g.type || e.options.chart.type;
                    const u = !(
                        this.hasDerivedData ||
                        (v && v !== this.type) ||
                        "undefined" !== typeof a.pointStart ||
                        "undefined" !== typeof a.pointInterval ||
                        "undefined" !== typeof a.relativeXValue ||
                        a.joinBy ||
                        a.mapData ||
                        d.hasOptionChanged("dataGrouping") ||
                        d.hasOptionChanged("pointStart") ||
                        d.hasOptionChanged("pointInterval") ||
                        d.hasOptionChanged("pointIntervalUnit") ||
                        d.hasOptionChanged("keys")
                    );
                    v = v || k;
                    u &&
                        (t.push(
                            "data",
                            "isDirtyData",
                            "points",
                            "processedData",
                            "processedXData",
                            "processedYData",
                            "xIncrement",
                            "cropped",
                            "_hasPointMarkers",
                            "_hasPointLabels",
                            "clips",
                            "nodes",
                            "layout",
                            "level",
                            "mapMap",
                            "mapData",
                            "minY",
                            "maxY",
                            "minX",
                            "maxX"
                        ),
                        !1 !== a.visible && t.push("area", "graph"),
                        d.parallelArrays.forEach(function (b) {
                            t.push(b + "Data");
                        }),
                        a.data &&
                            (a.dataSorting &&
                                f(d.options.dataSorting, a.dataSorting),
                            this.setData(a.data, !1)));
                    a = V(
                        g,
                        m,
                        {
                            index:
                                "undefined" === typeof g.index
                                    ? d.index
                                    : g.index,
                            pointStart: A(
                                h && h.series && h.series.pointStart,
                                g.pointStart,
                                d.xData[0]
                            ),
                        },
                        !u && { data: d.options.data },
                        a
                    );
                    u && a.data && (a.data = d.options.data);
                    t = [
                        "group",
                        "markerGroup",
                        "dataLabelsGroup",
                        "transformGroup",
                    ].concat(t);
                    t.forEach(function (b) {
                        t[b] = d[b];
                        delete d[b];
                    });
                    h = !1;
                    if (w[v]) {
                        if (((h = v !== d.type), d.remove(!1, !1, !1, !0), h))
                            if (Object.setPrototypeOf)
                                Object.setPrototypeOf(d, w[v].prototype);
                            else {
                                m =
                                    Object.hasOwnProperty.call(d, "hcEvents") &&
                                    d.hcEvents;
                                for (r in l) d[r] = void 0;
                                f(d, w[v].prototype);
                                m ? (d.hcEvents = m) : delete d.hcEvents;
                            }
                    } else b(17, !0, e, { missingModuleFor: v });
                    t.forEach(function (b) {
                        d[b] = t[b];
                    });
                    d.init(e, a);
                    if (u && this.points) {
                        a = d.options;
                        if (!1 === a.visible)
                            (p.graphic = 1), (p.dataLabel = 1);
                        else if (!d._hasPointLabels) {
                            const { marker: b, dataLabels: c } = a;
                            g = g.marker || {};
                            !b ||
                                (!1 !== b.enabled &&
                                    g.symbol === b.symbol &&
                                    g.height === b.height &&
                                    g.width === b.width) ||
                                (p.graphic = 1);
                            c && !1 === c.enabled && (p.dataLabel = 1);
                        }
                        for (const b of this.points)
                            b &&
                                b.series &&
                                (b.resolveColor(),
                                Object.keys(p).length && b.destroyElements(p),
                                !1 === a.showInLegend &&
                                    b.legendItem &&
                                    e.legend.destroyItem(b));
                    }
                    d.initialType = k;
                    e.linkSeries();
                    h && d.linkedSeries.length && (d.isDirtyData = !0);
                    n(this, "afterUpdate");
                    A(c, !0) && e.redraw(u ? void 0 : !1);
                }
                setName(b) {
                    this.name = this.options.name = this.userOptions.name = b;
                    this.chart.isDirtyLegend = !0;
                }
                hasOptionChanged(b) {
                    const a = this.options[b],
                        c = this.chart.options.plotOptions,
                        d = this.userOptions[b];
                    return d
                        ? a !== d
                        : a !==
                              A(
                                  c && c[this.type] && c[this.type][b],
                                  c && c.series && c.series[b],
                                  a
                              );
                }
                onMouseOver() {
                    const b = this.chart,
                        a = b.hoverSeries;
                    b.pointer.setHoverChartIndex();
                    if (a && a !== this) a.onMouseOut();
                    this.options.events.mouseOver && n(this, "mouseOver");
                    this.setState("hover");
                    b.hoverSeries = this;
                }
                onMouseOut() {
                    const b = this.options,
                        a = this.chart,
                        c = a.tooltip,
                        d = a.hoverPoint;
                    a.hoverSeries = null;
                    if (d) d.onMouseOut();
                    this && b.events.mouseOut && n(this, "mouseOut");
                    !c ||
                        this.stickyTracking ||
                        (c.shared && !this.noSharedTooltip) ||
                        c.hide();
                    a.series.forEach(function (b) {
                        b.setState("", !0);
                    });
                }
                setState(b, a) {
                    const c = this;
                    var d = c.options;
                    const f = c.graph,
                        e = d.inactiveOtherPoints,
                        g = d.states,
                        k = A(
                            g[b || "normal"] && g[b || "normal"].animation,
                            c.chart.options.chart.animation
                        );
                    let h = d.lineWidth,
                        n = 0,
                        l = d.opacity;
                    b = b || "";
                    if (
                        c.state !== b &&
                        ([c.group, c.markerGroup, c.dataLabelsGroup].forEach(
                            function (a) {
                                a &&
                                    (c.state &&
                                        a.removeClass(
                                            "highcharts-series-" + c.state
                                        ),
                                    b && a.addClass("highcharts-series-" + b));
                            }
                        ),
                        (c.state = b),
                        !c.chart.styledMode)
                    ) {
                        if (g[b] && !1 === g[b].enabled) return;
                        b &&
                            ((h =
                                g[b].lineWidth ||
                                h + (g[b].lineWidthPlus || 0)),
                            (l = A(g[b].opacity, l)));
                        if (f && !f.dashstyle && T(h))
                            for (
                                d = { "stroke-width": h }, f.animate(d, k);
                                c["zone-graph-" + n];

                            )
                                c["zone-graph-" + n].animate(d, k), (n += 1);
                        e ||
                            [
                                c.group,
                                c.markerGroup,
                                c.dataLabelsGroup,
                                c.labelBySeries,
                            ].forEach(function (b) {
                                b && b.animate({ opacity: l }, k);
                            });
                    }
                    a && e && c.points && c.setAllPointsToState(b || void 0);
                }
                setAllPointsToState(b) {
                    this.points.forEach(function (a) {
                        a.setState && a.setState(b);
                    });
                }
                setVisible(b, a) {
                    const c = this,
                        d = c.chart,
                        f = d.options.chart.ignoreHiddenSeries,
                        e = c.visible,
                        g = (c.visible =
                            b =
                            c.options.visible =
                            c.userOptions.visible =
                                "undefined" === typeof b ? !e : b)
                            ? "show"
                            : "hide";
                    [
                        "group",
                        "dataLabelsGroup",
                        "markerGroup",
                        "tracker",
                        "tt",
                    ].forEach(function (b) {
                        if (c[b]) c[b][g]();
                    });
                    if (
                        d.hoverSeries === c ||
                        (d.hoverPoint && d.hoverPoint.series) === c
                    )
                        c.onMouseOut();
                    c.legendItem && d.legend.colorizeItem(c, b);
                    c.isDirty = !0;
                    c.options.stacking &&
                        d.series.forEach(function (b) {
                            b.options.stacking && b.visible && (b.isDirty = !0);
                        });
                    c.linkedSeries.forEach(function (a) {
                        a.setVisible(b, !1);
                    });
                    f && (d.isDirtyBox = !0);
                    n(c, g);
                    !1 !== a && d.redraw();
                }
                show() {
                    this.setVisible(!0);
                }
                hide() {
                    this.setVisible(!1);
                }
                select(b) {
                    this.selected =
                        b =
                        this.options.selected =
                            "undefined" === typeof b ? !this.selected : b;
                    this.checkbox && (this.checkbox.checked = b);
                    n(this, b ? "select" : "unselect");
                }
                shouldShowTooltip(b, a, c = {}) {
                    c.series = this;
                    c.visiblePlotOnly = !0;
                    return this.chart.isInsidePlot(b, a, c);
                }
                drawLegendSymbol(b, a) {
                    var c;
                    null ===
                        (c = C[this.options.legendSymbol || "rectangle"]) ||
                    void 0 === c
                        ? void 0
                        : c.call(this, b, a);
                }
            }
            aa.defaultOptions = H;
            aa.types = B.seriesTypes;
            aa.registerType = B.registerSeriesType;
            f(aa.prototype, {
                axisTypes: ["xAxis", "yAxis"],
                coll: "series",
                colorCounter: 0,
                cropShoulder: 1,
                directTouch: !1,
                isCartesian: !0,
                kdAxisArray: ["clientX", "plotY"],
                parallelArrays: ["x", "y"],
                pointClass: z,
                requireSorting: !0,
                sorted: !0,
            });
            B.series = aa;
            ("");
            ("");
            return aa;
        }
    );
    M(
        a,
        "Core/Chart/Chart.js",
        [
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Axis/Axis.js"],
            a["Core/Defaults.js"],
            a["Core/Templating.js"],
            a["Core/Foundation.js"],
            a["Core/Globals.js"],
            a["Core/Renderer/RendererRegistry.js"],
            a["Core/Series/Series.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Renderer/SVG/SVGRenderer.js"],
            a["Core/Time.js"],
            a["Core/Utilities.js"],
            a["Core/Renderer/HTML/AST.js"],
        ],
        function (a, y, I, L, C, z, H, B, u, v, l, p, t) {
            const { animate: m, animObject: h, setAnimation: g } = a,
                { defaultOptions: e, defaultTime: w } = I,
                { numberFormat: x } = L,
                { registerEventOptions: F } = C,
                { charts: d, doc: k, marginNames: r, svg: q, win: G } = z,
                { seriesTypes: b } = u,
                {
                    addEvent: f,
                    attr: c,
                    createElement: n,
                    css: P,
                    defined: D,
                    diffObjects: K,
                    discardElement: X,
                    erase: T,
                    error: Z,
                    extend: V,
                    find: Y,
                    fireEvent: A,
                    getStyle: M,
                    isArray: ia,
                    isNumber: ba,
                    isObject: aa,
                    isString: J,
                    merge: N,
                    objectEach: O,
                    pick: S,
                    pInt: W,
                    relativeLength: ha,
                    removeEvent: da,
                    splat: fa,
                    syncTimeout: ka,
                    uniqueKey: ca,
                } = p;
            class ea {
                static chart(b, a, c) {
                    return new ea(b, a, c);
                }
                constructor(b, a, c) {
                    this.series =
                        this.renderTo =
                        this.renderer =
                        this.pointer =
                        this.pointCount =
                        this.plotWidth =
                        this.plotTop =
                        this.plotLeft =
                        this.plotHeight =
                        this.plotBox =
                        this.options =
                        this.numberFormatter =
                        this.margin =
                        this.labelCollectors =
                        this.isResizing =
                        this.index =
                        this.eventOptions =
                        this.container =
                        this.colorCounter =
                        this.clipBox =
                        this.chartWidth =
                        this.chartHeight =
                        this.bounds =
                        this.axisOffset =
                        this.axes =
                            void 0;
                    this.sharedClips = {};
                    this.zooming =
                        this.yAxis =
                        this.xAxis =
                        this.userOptions =
                        this.titleOffset =
                        this.time =
                        this.symbolCounter =
                        this.spacingBox =
                        this.spacing =
                            void 0;
                    this.getArgs(b, a, c);
                }
                getArgs(b, a, c) {
                    J(b) || b.nodeName
                        ? ((this.renderTo = b), this.init(a, c))
                        : this.init(b, a);
                }
                setZoomOptions() {
                    const b = this.options.chart,
                        a = b.zooming;
                    this.zooming = Object.assign(Object.assign({}, a), {
                        type: S(b.zoomType, a.type),
                        key: S(b.zoomKey, a.key),
                        pinchType: S(b.pinchType, a.pinchType),
                        singleTouch: S(b.zoomBySingleTouch, a.singleTouch, !1),
                        resetButton: N(a.resetButton, b.resetZoomButton),
                    });
                }
                init(b, a) {
                    A(this, "init", { args: arguments }, function () {
                        const c = N(e, b),
                            f = c.chart;
                        this.userOptions = V({}, b);
                        this.margin = [];
                        this.spacing = [];
                        this.bounds = { h: {}, v: {} };
                        this.labelCollectors = [];
                        this.callback = a;
                        this.isResizing = 0;
                        this.options = c;
                        this.axes = [];
                        this.series = [];
                        this.time =
                            b.time && Object.keys(b.time).length
                                ? new l(b.time)
                                : z.time;
                        this.numberFormatter = f.numberFormatter || x;
                        this.styledMode = f.styledMode;
                        this.hasCartesianSeries = f.showAxes;
                        this.index = d.length;
                        d.push(this);
                        z.chartCount++;
                        F(this, f);
                        this.xAxis = [];
                        this.yAxis = [];
                        this.pointCount =
                            this.colorCounter =
                            this.symbolCounter =
                                0;
                        this.setZoomOptions();
                        A(this, "afterInit");
                        this.firstRender();
                    });
                }
                initSeries(a) {
                    var c = this.options.chart;
                    c = a.type || c.type;
                    const d = b[c];
                    d || Z(17, !0, this, { missingModuleFor: c });
                    c = new d();
                    "function" === typeof c.init && c.init(this, a);
                    return c;
                }
                setSeriesData() {
                    this.getSeriesOrderByLinks().forEach(function (b) {
                        b.points ||
                            b.data ||
                            !b.enabledDataSorting ||
                            b.setData(b.options.data, !1);
                    });
                }
                getSeriesOrderByLinks() {
                    return this.series.concat().sort(function (b, a) {
                        return b.linkedSeries.length || a.linkedSeries.length
                            ? a.linkedSeries.length - b.linkedSeries.length
                            : 0;
                    });
                }
                orderItems(b, a = 0) {
                    const c = this[b],
                        d = (this.options[b] = fa(this.options[b]).slice());
                    b = this.userOptions[b] = this.userOptions[b]
                        ? fa(this.userOptions[b]).slice()
                        : [];
                    this.hasRendered && (d.splice(a), b.splice(a));
                    if (c)
                        for (let f = a, e = c.length; f < e; ++f)
                            if ((a = c[f]))
                                (a.index = f),
                                    a instanceof B && (a.name = a.getName()),
                                    a.options.isInternal ||
                                        ((d[f] = a.options),
                                        (b[f] = a.userOptions));
                }
                isInsidePlot(b, a, c = {}) {
                    const {
                        inverted: d,
                        plotBox: f,
                        plotLeft: e,
                        plotTop: g,
                        scrollablePlotBox: k,
                    } = this;
                    var h = 0;
                    let n = 0;
                    c.visiblePlotOnly &&
                        this.scrollingContainer &&
                        ({ scrollLeft: h, scrollTop: n } =
                            this.scrollingContainer);
                    const l = c.series,
                        m = (c.visiblePlotOnly && k) || f;
                    var q = c.inverted ? a : b;
                    a = c.inverted ? b : a;
                    b = { x: q, y: a, isInsidePlot: !0, options: c };
                    if (!c.ignoreX) {
                        const a = (l &&
                            (d && !this.polar ? l.yAxis : l.xAxis)) || {
                            pos: e,
                            len: Infinity,
                        };
                        q = c.paneCoordinates ? a.pos + q : e + q;
                        (q >= Math.max(h + e, a.pos) &&
                            q <= Math.min(h + e + m.width, a.pos + a.len)) ||
                            (b.isInsidePlot = !1);
                    }
                    !c.ignoreY &&
                        b.isInsidePlot &&
                        ((h = (!d && c.axis && !c.axis.isXAxis && c.axis) ||
                            (l && (d ? l.xAxis : l.yAxis)) || {
                                pos: g,
                                len: Infinity,
                            }),
                        (c = c.paneCoordinates ? h.pos + a : g + a),
                        (c >= Math.max(n + g, h.pos) &&
                            c <= Math.min(n + g + m.height, h.pos + h.len)) ||
                            (b.isInsidePlot = !1));
                    A(this, "afterIsInsidePlot", b);
                    return b.isInsidePlot;
                }
                redraw(b) {
                    A(this, "beforeRedraw");
                    const a = this.hasCartesianSeries
                            ? this.axes
                            : this.colorAxis || [],
                        c = this.series,
                        d = this.pointer,
                        f = this.legend,
                        e = this.userOptions.legend,
                        k = this.renderer,
                        h = k.isHidden(),
                        n = [];
                    let l,
                        m,
                        q = this.isDirtyBox,
                        p = this.isDirtyLegend,
                        r;
                    k.rootFontSize = k.boxWrapper.getStyle("font-size");
                    this.setResponsive && this.setResponsive(!1);
                    g(this.hasRendered ? b : !1, this);
                    h && this.temporaryDisplay();
                    this.layOutTitles(!1);
                    for (b = c.length; b--; )
                        if (
                            ((r = c[b]),
                            r.options.stacking || r.options.centerInCategory)
                        )
                            if (((m = !0), r.isDirty)) {
                                l = !0;
                                break;
                            }
                    if (l)
                        for (b = c.length; b--; )
                            (r = c[b]), r.options.stacking && (r.isDirty = !0);
                    c.forEach(function (b) {
                        b.isDirty &&
                            ("point" === b.options.legendType
                                ? ("function" === typeof b.updateTotals &&
                                      b.updateTotals(),
                                  (p = !0))
                                : e &&
                                  (e.labelFormatter || e.labelFormat) &&
                                  (p = !0));
                        b.isDirtyData && A(b, "updatedData");
                    });
                    p &&
                        f &&
                        f.options.enabled &&
                        (f.render(), (this.isDirtyLegend = !1));
                    m && this.getStacks();
                    a.forEach(function (b) {
                        b.updateNames();
                        b.setScale();
                    });
                    this.getMargins();
                    a.forEach(function (b) {
                        b.isDirty && (q = !0);
                    });
                    a.forEach(function (b) {
                        const a = b.min + "," + b.max;
                        b.extKey !== a &&
                            ((b.extKey = a),
                            n.push(function () {
                                A(
                                    b,
                                    "afterSetExtremes",
                                    V(b.eventArgs, b.getExtremes())
                                );
                                delete b.eventArgs;
                            }));
                        (q || m) && b.redraw();
                    });
                    q && this.drawChartBox();
                    A(this, "predraw");
                    c.forEach(function (b) {
                        (q || b.isDirty) && b.visible && b.redraw();
                        b.isDirtyData = !1;
                    });
                    d && d.reset(!0);
                    k.draw();
                    A(this, "redraw");
                    A(this, "render");
                    h && this.temporaryDisplay(!0);
                    n.forEach(function (b) {
                        b.call();
                    });
                }
                get(b) {
                    function a(a) {
                        return a.id === b || (a.options && a.options.id === b);
                    }
                    const c = this.series;
                    let d = Y(this.axes, a) || Y(this.series, a);
                    for (let b = 0; !d && b < c.length; b++)
                        d = Y(c[b].points || [], a);
                    return d;
                }
                getAxes() {
                    const b = this.options;
                    A(this, "getAxes");
                    for (const a of ["xAxis", "yAxis"]) {
                        const c = (b[a] = fa(b[a] || {}));
                        for (const b of c) new y(this, b, a);
                    }
                    A(this, "afterGetAxes");
                }
                getSelectedPoints() {
                    return this.series.reduce((b, a) => {
                        a.getPointsCollection().forEach((a) => {
                            S(a.selectedStaging, a.selected) && b.push(a);
                        });
                        return b;
                    }, []);
                }
                getSelectedSeries() {
                    return this.series.filter(function (b) {
                        return b.selected;
                    });
                }
                setTitle(b, a, c) {
                    this.applyDescription("title", b);
                    this.applyDescription("subtitle", a);
                    this.applyDescription("caption", void 0);
                    this.layOutTitles(c);
                }
                applyDescription(b, a) {
                    const c = this,
                        d = (this.options[b] = N(this.options[b], a));
                    let f = this[b];
                    f && a && (this[b] = f = f.destroy());
                    d &&
                        !f &&
                        ((f = this.renderer
                            .text(d.text, 0, 0, d.useHTML)
                            .attr({
                                align: d.align,
                                class: "highcharts-" + b,
                                zIndex: d.zIndex || 4,
                            })
                            .add()),
                        (f.update = function (a, d) {
                            c.applyDescription(b, a);
                            c.layOutTitles(d);
                        }),
                        this.styledMode ||
                            f.css(
                                V(
                                    "title" === b
                                        ? {
                                              fontSize: this.options.isStock
                                                  ? "1em"
                                                  : "1.2em",
                                          }
                                        : {},
                                    d.style
                                )
                            ),
                        (this[b] = f));
                }
                layOutTitles(b = !0) {
                    const a = [0, 0, 0],
                        c = this.renderer,
                        d = this.spacingBox;
                    ["title", "subtitle", "caption"].forEach(function (b) {
                        const f = this[b],
                            e = this.options[b],
                            g = e.verticalAlign || "top";
                        b =
                            "title" === b
                                ? "top" === g
                                    ? -3
                                    : 0
                                : "top" === g
                                ? a[0] + 2
                                : 0;
                        if (f) {
                            f.css({
                                width:
                                    (e.width ||
                                        d.width + (e.widthAdjust || 0)) + "px",
                            });
                            const k = c.fontMetrics(f).b,
                                h = Math.round(f.getBBox(e.useHTML).height);
                            f.align(
                                V(
                                    {
                                        y: "bottom" === g ? k : b + k,
                                        height: h,
                                    },
                                    e
                                ),
                                !1,
                                "spacingBox"
                            );
                            e.floating ||
                                ("top" === g
                                    ? (a[0] = Math.ceil(a[0] + h))
                                    : "bottom" === g &&
                                      (a[2] = Math.ceil(a[2] + h)));
                        }
                    }, this);
                    a[0] &&
                        "top" === (this.options.title.verticalAlign || "top") &&
                        (a[0] += this.options.title.margin);
                    a[2] &&
                        "bottom" === this.options.caption.verticalAlign &&
                        (a[2] += this.options.caption.margin);
                    const f =
                        !this.titleOffset ||
                        this.titleOffset.join(",") !== a.join(",");
                    this.titleOffset = a;
                    A(this, "afterLayOutTitles");
                    !this.isDirtyBox &&
                        f &&
                        ((this.isDirtyBox = this.isDirtyLegend = f),
                        this.hasRendered &&
                            b &&
                            this.isDirtyBox &&
                            this.redraw());
                }
                getContainerBox() {
                    return {
                        width: M(this.renderTo, "width", !0) || 0,
                        height: M(this.renderTo, "height", !0) || 0,
                    };
                }
                getChartSize() {
                    var b = this.options.chart;
                    const a = b.width;
                    b = b.height;
                    const c = this.getContainerBox();
                    this.chartWidth = Math.max(0, a || c.width || 600);
                    this.chartHeight = Math.max(
                        0,
                        ha(b, this.chartWidth) ||
                            (1 < c.height ? c.height : 400)
                    );
                    this.containerBox = c;
                }
                temporaryDisplay(b) {
                    let a = this.renderTo;
                    if (b)
                        for (; a && a.style; )
                            a.hcOrigStyle &&
                                (P(a, a.hcOrigStyle), delete a.hcOrigStyle),
                                a.hcOrigDetached &&
                                    (k.body.removeChild(a),
                                    (a.hcOrigDetached = !1)),
                                (a = a.parentNode);
                    else
                        for (; a && a.style; ) {
                            k.body.contains(a) ||
                                a.parentNode ||
                                ((a.hcOrigDetached = !0),
                                k.body.appendChild(a));
                            if (
                                "none" === M(a, "display", !1) ||
                                a.hcOricDetached
                            )
                                (a.hcOrigStyle = {
                                    display: a.style.display,
                                    height: a.style.height,
                                    overflow: a.style.overflow,
                                }),
                                    (b = {
                                        display: "block",
                                        overflow: "hidden",
                                    }),
                                    a !== this.renderTo && (b.height = 0),
                                    P(a, b),
                                    a.offsetWidth ||
                                        a.style.setProperty(
                                            "display",
                                            "block",
                                            "important"
                                        );
                            a = a.parentNode;
                            if (a === k.body) break;
                        }
                }
                setClassName(b) {
                    this.container.className =
                        "highcharts-container " + (b || "");
                }
                getContainer() {
                    const b = this.options,
                        a = b.chart;
                    var f = ca();
                    let e,
                        h = this.renderTo;
                    h || (this.renderTo = h = a.renderTo);
                    J(h) && (this.renderTo = h = k.getElementById(h));
                    h || Z(13, !0, this);
                    var l = W(c(h, "data-highcharts-chart"));
                    ba(l) && d[l] && d[l].hasRendered && d[l].destroy();
                    c(h, "data-highcharts-chart", this.index);
                    h.innerHTML = t.emptyHTML;
                    a.skipClone || h.offsetWidth || this.temporaryDisplay();
                    this.getChartSize();
                    l = this.chartWidth;
                    const m = this.chartHeight;
                    P(h, { overflow: "hidden" });
                    this.styledMode ||
                        (e = V(
                            {
                                position: "relative",
                                overflow: "hidden",
                                width: l + "px",
                                height: m + "px",
                                textAlign: "left",
                                lineHeight: "normal",
                                zIndex: 0,
                                "-webkit-tap-highlight-color": "rgba(0,0,0,0)",
                                userSelect: "none",
                                "touch-action": "manipulation",
                                outline: "none",
                            },
                            a.style || {}
                        ));
                    this.container = f = n("div", { id: f }, e, h);
                    this._cursor = f.style.cursor;
                    this.renderer = new (
                        a.renderer || !q ? H.getRendererType(a.renderer) : v
                    )(
                        f,
                        l,
                        m,
                        void 0,
                        a.forExport,
                        b.exporting && b.exporting.allowHTML,
                        this.styledMode
                    );
                    this.containerBox = this.getContainerBox();
                    g(void 0, this);
                    this.setClassName(a.className);
                    if (this.styledMode)
                        for (const a in b.defs)
                            this.renderer.definition(b.defs[a]);
                    else this.renderer.setStyle(a.style);
                    this.renderer.chartIndex = this.index;
                    A(this, "afterGetContainer");
                }
                getMargins(b) {
                    const { spacing: a, margin: c, titleOffset: d } = this;
                    this.resetMargins();
                    d[0] &&
                        !D(c[0]) &&
                        (this.plotTop = Math.max(this.plotTop, d[0] + a[0]));
                    d[2] &&
                        !D(c[2]) &&
                        (this.marginBottom = Math.max(
                            this.marginBottom,
                            d[2] + a[2]
                        ));
                    this.legend &&
                        this.legend.display &&
                        this.legend.adjustMargins(c, a);
                    A(this, "getMargins");
                    b || this.getAxisMargins();
                }
                getAxisMargins() {
                    const b = this,
                        a = (b.axisOffset = [0, 0, 0, 0]),
                        c = b.colorAxis,
                        d = b.margin,
                        f = function (b) {
                            b.forEach(function (b) {
                                b.visible && b.getOffset();
                            });
                        };
                    b.hasCartesianSeries ? f(b.axes) : c && c.length && f(c);
                    r.forEach(function (c, f) {
                        D(d[f]) || (b[c] += a[f]);
                    });
                    b.setChartSize();
                }
                getOptions() {
                    return K(this.userOptions, e);
                }
                reflow(b) {
                    const a = this;
                    var c = a.options.chart;
                    c = D(c.width) && D(c.height);
                    const d = a.containerBox,
                        f = a.getContainerBox();
                    delete a.pointer.chartPosition;
                    if (!c && !a.isPrinting && d && f.width) {
                        if (f.width !== d.width || f.height !== d.height)
                            p.clearTimeout(a.reflowTimeout),
                                (a.reflowTimeout = ka(
                                    function () {
                                        a.container &&
                                            a.setSize(void 0, void 0, !1);
                                    },
                                    b ? 100 : 0
                                ));
                        a.containerBox = f;
                    }
                }
                setReflow() {
                    const b = this;
                    var a = (a) => {
                        var c;
                        (null === (c = b.options) || void 0 === c
                            ? 0
                            : c.chart.reflow) &&
                            b.hasLoaded &&
                            b.reflow(a);
                    };
                    "function" === typeof ResizeObserver
                        ? new ResizeObserver(a).observe(b.renderTo)
                        : ((a = f(G, "resize", a)), f(this, "destroy", a));
                }
                setSize(b, a, c) {
                    const d = this,
                        f = d.renderer;
                    d.isResizing += 1;
                    g(c, d);
                    c = f.globalAnimation;
                    d.oldChartHeight = d.chartHeight;
                    d.oldChartWidth = d.chartWidth;
                    "undefined" !== typeof b && (d.options.chart.width = b);
                    "undefined" !== typeof a && (d.options.chart.height = a);
                    d.getChartSize();
                    d.styledMode ||
                        (c ? m : P)(
                            d.container,
                            {
                                width: d.chartWidth + "px",
                                height: d.chartHeight + "px",
                            },
                            c
                        );
                    d.setChartSize(!0);
                    f.setSize(d.chartWidth, d.chartHeight, c);
                    d.axes.forEach(function (b) {
                        b.isDirty = !0;
                        b.setScale();
                    });
                    d.isDirtyLegend = !0;
                    d.isDirtyBox = !0;
                    d.layOutTitles();
                    d.getMargins();
                    d.redraw(c);
                    d.oldChartHeight = null;
                    A(d, "resize");
                    ka(function () {
                        d &&
                            A(d, "endResize", null, function () {
                                --d.isResizing;
                            });
                    }, h(c).duration);
                }
                setChartSize(b) {
                    var a = this.inverted;
                    const c = this.renderer;
                    var d = this.chartWidth,
                        f = this.chartHeight;
                    const e = this.options.chart,
                        g = this.spacing,
                        k = this.clipOffset;
                    let h, n, l, m;
                    this.plotLeft = h = Math.round(this.plotLeft);
                    this.plotTop = n = Math.round(this.plotTop);
                    this.plotWidth = l = Math.max(
                        0,
                        Math.round(d - h - this.marginRight)
                    );
                    this.plotHeight = m = Math.max(
                        0,
                        Math.round(f - n - this.marginBottom)
                    );
                    this.plotSizeX = a ? m : l;
                    this.plotSizeY = a ? l : m;
                    this.plotBorderWidth = e.plotBorderWidth || 0;
                    this.spacingBox = c.spacingBox = {
                        x: g[3],
                        y: g[0],
                        width: d - g[3] - g[1],
                        height: f - g[0] - g[2],
                    };
                    this.plotBox = c.plotBox = {
                        x: h,
                        y: n,
                        width: l,
                        height: m,
                    };
                    a = 2 * Math.floor(this.plotBorderWidth / 2);
                    d = Math.ceil(Math.max(a, k[3]) / 2);
                    f = Math.ceil(Math.max(a, k[0]) / 2);
                    this.clipBox = {
                        x: d,
                        y: f,
                        width: Math.floor(
                            this.plotSizeX - Math.max(a, k[1]) / 2 - d
                        ),
                        height: Math.max(
                            0,
                            Math.floor(
                                this.plotSizeY - Math.max(a, k[2]) / 2 - f
                            )
                        ),
                    };
                    b ||
                        (this.axes.forEach(function (b) {
                            b.setAxisSize();
                            b.setAxisTranslation();
                        }),
                        c.alignElements());
                    A(this, "afterSetChartSize", { skipAxes: b });
                }
                resetMargins() {
                    A(this, "resetMargins");
                    const b = this,
                        a = b.options.chart;
                    ["margin", "spacing"].forEach(function (c) {
                        const d = a[c],
                            f = aa(d) ? d : [d, d, d, d];
                        ["Top", "Right", "Bottom", "Left"].forEach(function (
                            d,
                            e
                        ) {
                            b[c][e] = S(a[c + d], f[e]);
                        });
                    });
                    r.forEach(function (a, c) {
                        b[a] = S(b.margin[c], b.spacing[c]);
                    });
                    b.axisOffset = [0, 0, 0, 0];
                    b.clipOffset = [0, 0, 0, 0];
                }
                drawChartBox() {
                    const b = this.options.chart,
                        a = this.renderer,
                        c = this.chartWidth,
                        d = this.chartHeight,
                        f = this.styledMode,
                        e = this.plotBGImage;
                    var g = b.backgroundColor;
                    const k = b.plotBackgroundColor,
                        h = b.plotBackgroundImage,
                        n = this.plotLeft,
                        l = this.plotTop,
                        m = this.plotWidth,
                        q = this.plotHeight,
                        p = this.plotBox,
                        r = this.clipRect,
                        t = this.clipBox;
                    let w = this.chartBackground,
                        v = this.plotBackground,
                        u = this.plotBorder,
                        x,
                        D,
                        E = "animate";
                    w ||
                        ((this.chartBackground = w =
                            a.rect().addClass("highcharts-background").add()),
                        (E = "attr"));
                    if (f) x = D = w.strokeWidth();
                    else {
                        x = b.borderWidth || 0;
                        D = x + (b.shadow ? 8 : 0);
                        g = { fill: g || "none" };
                        if (x || w["stroke-width"])
                            (g.stroke = b.borderColor), (g["stroke-width"] = x);
                        w.attr(g).shadow(b.shadow);
                    }
                    w[E]({
                        x: D / 2,
                        y: D / 2,
                        width: c - D - (x % 2),
                        height: d - D - (x % 2),
                        r: b.borderRadius,
                    });
                    E = "animate";
                    v ||
                        ((E = "attr"),
                        (this.plotBackground = v =
                            a
                                .rect()
                                .addClass("highcharts-plot-background")
                                .add()));
                    v[E](p);
                    f ||
                        (v.attr({ fill: k || "none" }).shadow(b.plotShadow),
                        h &&
                            (e
                                ? (h !== e.attr("href") && e.attr("href", h),
                                  e.animate(p))
                                : (this.plotBGImage = a
                                      .image(h, n, l, m, q)
                                      .add())));
                    r
                        ? r.animate({ width: t.width, height: t.height })
                        : (this.clipRect = a.clipRect(t));
                    E = "animate";
                    u ||
                        ((E = "attr"),
                        (this.plotBorder = u =
                            a
                                .rect()
                                .addClass("highcharts-plot-border")
                                .attr({ zIndex: 1 })
                                .add()));
                    f ||
                        u.attr({
                            stroke: b.plotBorderColor,
                            "stroke-width": b.plotBorderWidth || 0,
                            fill: "none",
                        });
                    u[E](
                        u.crisp(
                            { x: n, y: l, width: m, height: q },
                            -u.strokeWidth()
                        )
                    );
                    this.isDirtyBox = !1;
                    A(this, "afterDrawChartBox");
                }
                propFromSeries() {
                    const a = this,
                        c = a.options.chart,
                        d = a.options.series;
                    let f, e, g;
                    ["inverted", "angular", "polar"].forEach(function (k) {
                        e = b[c.type];
                        g = c[k] || (e && e.prototype[k]);
                        for (f = d && d.length; !g && f--; )
                            (e = b[d[f].type]) && e.prototype[k] && (g = !0);
                        a[k] = g;
                    });
                }
                linkSeries(b) {
                    const a = this,
                        c = a.series;
                    c.forEach(function (b) {
                        b.linkedSeries.length = 0;
                    });
                    c.forEach(function (b) {
                        let c = b.options.linkedTo;
                        J(c) &&
                            (c =
                                ":previous" === c
                                    ? a.series[b.index - 1]
                                    : a.get(c)) &&
                            c.linkedParent !== b &&
                            (c.linkedSeries.push(b),
                            (b.linkedParent = c),
                            c.enabledDataSorting && b.setDataSortingOptions(),
                            (b.visible = S(
                                b.options.visible,
                                c.options.visible,
                                b.visible
                            )));
                    });
                    A(this, "afterLinkSeries", { isUpdating: b });
                }
                renderSeries() {
                    this.series.forEach(function (b) {
                        b.translate();
                        b.render();
                    });
                }
                render() {
                    const b = this.axes,
                        a = this.colorAxis,
                        c = this.renderer,
                        d = function (b) {
                            b.forEach(function (b) {
                                b.visible && b.render();
                            });
                        };
                    let f = 0;
                    this.setTitle();
                    A(this, "beforeMargins");
                    this.getStacks && this.getStacks();
                    this.getMargins(!0);
                    this.setChartSize();
                    const e = this.plotWidth;
                    b.some(function (b) {
                        if (
                            b.horiz &&
                            b.visible &&
                            b.options.labels.enabled &&
                            b.series.length
                        )
                            return (f = 21), !0;
                    });
                    const g = (this.plotHeight = Math.max(
                        this.plotHeight - f,
                        0
                    ));
                    b.forEach(function (b) {
                        b.setScale();
                    });
                    this.getAxisMargins();
                    const k = 1.1 < e / this.plotWidth,
                        h = 1.05 < g / this.plotHeight;
                    if (k || h)
                        b.forEach(function (b) {
                            ((b.horiz && k) || (!b.horiz && h)) &&
                                b.setTickInterval(!0);
                        }),
                            this.getMargins();
                    this.drawChartBox();
                    this.hasCartesianSeries ? d(b) : a && a.length && d(a);
                    this.seriesGroup ||
                        (this.seriesGroup = c
                            .g("series-group")
                            .attr({ zIndex: 3 })
                            .shadow(this.options.chart.seriesGroupShadow)
                            .add());
                    this.renderSeries();
                    this.addCredits();
                    this.setResponsive && this.setResponsive();
                    this.hasRendered = !0;
                }
                addCredits(b) {
                    const a = this,
                        c = N(!0, this.options.credits, b);
                    c.enabled &&
                        !this.credits &&
                        ((this.credits = this.renderer
                            .text(c.text + (this.mapCredits || ""), 0, 0)
                            .addClass("highcharts-credits")
                            .on("click", function () {
                                c.href && (G.location.href = c.href);
                            })
                            .attr({ align: c.position.align, zIndex: 8 })),
                        a.styledMode || this.credits.css(c.style),
                        this.credits.add().align(c.position),
                        (this.credits.update = function (b) {
                            a.credits = a.credits.destroy();
                            a.addCredits(b);
                        }));
                }
                destroy() {
                    const b = this,
                        a = b.axes,
                        c = b.series,
                        f = b.container,
                        e = f && f.parentNode;
                    let g;
                    A(b, "destroy");
                    b.renderer.forExport ? T(d, b) : (d[b.index] = void 0);
                    z.chartCount--;
                    b.renderTo.removeAttribute("data-highcharts-chart");
                    da(b);
                    for (g = a.length; g--; ) a[g] = a[g].destroy();
                    this.scroller &&
                        this.scroller.destroy &&
                        this.scroller.destroy();
                    for (g = c.length; g--; ) c[g] = c[g].destroy();
                    "title subtitle chartBackground plotBackground plotBGImage plotBorder seriesGroup clipRect credits pointer rangeSelector legend resetZoomButton tooltip renderer"
                        .split(" ")
                        .forEach(function (a) {
                            const c = b[a];
                            c && c.destroy && (b[a] = c.destroy());
                        });
                    f && ((f.innerHTML = t.emptyHTML), da(f), e && X(f));
                    O(b, function (a, c) {
                        delete b[c];
                    });
                }
                firstRender() {
                    const b = this,
                        a = b.options;
                    b.getContainer();
                    b.resetMargins();
                    b.setChartSize();
                    b.propFromSeries();
                    b.getAxes();
                    const c = ia(a.series) ? a.series : [];
                    a.series = [];
                    c.forEach(function (a) {
                        b.initSeries(a);
                    });
                    b.linkSeries();
                    b.setSeriesData();
                    A(b, "beforeRender");
                    b.render();
                    b.pointer.getChartPosition();
                    if (!b.renderer.imgCount && !b.hasLoaded) b.onload();
                    b.temporaryDisplay(!0);
                }
                onload() {
                    this.callbacks
                        .concat([this.callback])
                        .forEach(function (b) {
                            b &&
                                "undefined" !== typeof this.index &&
                                b.apply(this, [this]);
                        }, this);
                    A(this, "load");
                    A(this, "render");
                    D(this.index) && this.setReflow();
                    this.warnIfA11yModuleNotLoaded();
                    this.hasLoaded = !0;
                }
                warnIfA11yModuleNotLoaded() {
                    const { options: b, title: a } = this;
                    b &&
                        !this.accessibility &&
                        (this.renderer.boxWrapper.attr({
                            role: "img",
                            "aria-label": (
                                (a && a.element.textContent) ||
                                ""
                            ).replace(/</g, "&lt;"),
                        }),
                        (b.accessibility && !1 === b.accessibility.enabled) ||
                            Z(
                                'Highcharts warning: Consider including the "accessibility.js" module to make your chart more usable for people with disabilities. Set the "accessibility.enabled" option to false to remove this warning. See https://www.highcharts.com/docs/accessibility/accessibility-module.',
                                !1,
                                this
                            ));
                }
                addSeries(b, a, c) {
                    const d = this;
                    let f;
                    b &&
                        ((a = S(a, !0)),
                        A(d, "addSeries", { options: b }, function () {
                            f = d.initSeries(b);
                            d.isDirtyLegend = !0;
                            d.linkSeries();
                            f.enabledDataSorting && f.setData(b.data, !1);
                            A(d, "afterAddSeries", { series: f });
                            a && d.redraw(c);
                        }));
                    return f;
                }
                addAxis(b, a, c, d) {
                    return this.createAxis(a ? "xAxis" : "yAxis", {
                        axis: b,
                        redraw: c,
                        animation: d,
                    });
                }
                addColorAxis(b, a, c) {
                    return this.createAxis("colorAxis", {
                        axis: b,
                        redraw: a,
                        animation: c,
                    });
                }
                createAxis(b, a) {
                    b = new y(this, a.axis, b);
                    S(a.redraw, !0) && this.redraw(a.animation);
                    return b;
                }
                showLoading(b) {
                    const a = this,
                        c = a.options,
                        d = c.loading,
                        e = function () {
                            g &&
                                P(g, {
                                    left: a.plotLeft + "px",
                                    top: a.plotTop + "px",
                                    width: a.plotWidth + "px",
                                    height: a.plotHeight + "px",
                                });
                        };
                    let g = a.loadingDiv,
                        k = a.loadingSpan;
                    g ||
                        (a.loadingDiv = g =
                            n(
                                "div",
                                {
                                    className:
                                        "highcharts-loading highcharts-loading-hidden",
                                },
                                null,
                                a.container
                            ));
                    k ||
                        ((a.loadingSpan = k =
                            n(
                                "span",
                                { className: "highcharts-loading-inner" },
                                null,
                                g
                            )),
                        f(a, "redraw", e));
                    g.className = "highcharts-loading";
                    t.setElementHTML(k, S(b, c.lang.loading, ""));
                    a.styledMode ||
                        (P(g, V(d.style, { zIndex: 10 })),
                        P(k, d.labelStyle),
                        a.loadingShown ||
                            (P(g, { opacity: 0, display: "" }),
                            m(
                                g,
                                { opacity: d.style.opacity || 0.5 },
                                { duration: d.showDuration || 0 }
                            )));
                    a.loadingShown = !0;
                    e();
                }
                hideLoading() {
                    const b = this.options,
                        a = this.loadingDiv;
                    a &&
                        ((a.className =
                            "highcharts-loading highcharts-loading-hidden"),
                        this.styledMode ||
                            m(
                                a,
                                { opacity: 0 },
                                {
                                    duration: b.loading.hideDuration || 100,
                                    complete: function () {
                                        P(a, { display: "none" });
                                    },
                                }
                            ));
                    this.loadingShown = !1;
                }
                update(b, a, c, d) {
                    const f = this,
                        e = {
                            credits: "addCredits",
                            title: "setTitle",
                            subtitle: "setSubtitle",
                            caption: "setCaption",
                        },
                        g = b.isResponsiveOptions,
                        k = [];
                    let h, n;
                    A(f, "update", { options: b });
                    g || f.setResponsive(!1, !0);
                    b = K(b, f.options);
                    f.userOptions = N(f.userOptions, b);
                    var m = b.chart;
                    if (m) {
                        N(!0, f.options.chart, m);
                        this.setZoomOptions();
                        "className" in m && f.setClassName(m.className);
                        if ("inverted" in m || "polar" in m || "type" in m) {
                            f.propFromSeries();
                            var q = !0;
                        }
                        "alignTicks" in m && (q = !0);
                        "events" in m && F(this, m);
                        O(m, function (b, a) {
                            -1 !==
                                f.propsRequireUpdateSeries.indexOf(
                                    "chart." + a
                                ) && (h = !0);
                            -1 !== f.propsRequireDirtyBox.indexOf(a) &&
                                (f.isDirtyBox = !0);
                            -1 !== f.propsRequireReflow.indexOf(a) &&
                                (g ? (f.isDirtyBox = !0) : (n = !0));
                        });
                        !f.styledMode &&
                            m.style &&
                            f.renderer.setStyle(f.options.chart.style || {});
                    }
                    !f.styledMode &&
                        b.colors &&
                        (this.options.colors = b.colors);
                    b.time &&
                        (this.time === w && (this.time = new l(b.time)),
                        N(!0, f.options.time, b.time));
                    O(b, function (a, c) {
                        if (f[c] && "function" === typeof f[c].update)
                            f[c].update(a, !1);
                        else if ("function" === typeof f[e[c]]) f[e[c]](a);
                        else
                            "colors" !== c &&
                                -1 === f.collectionsWithUpdate.indexOf(c) &&
                                N(!0, f.options[c], b[c]);
                        "chart" !== c &&
                            -1 !== f.propsRequireUpdateSeries.indexOf(c) &&
                            (h = !0);
                    });
                    this.collectionsWithUpdate.forEach(function (a) {
                        b[a] &&
                            (fa(b[a]).forEach(function (b, d) {
                                const e = D(b.id);
                                let g;
                                e && (g = f.get(b.id));
                                !g &&
                                    f[a] &&
                                    (g = f[a][S(b.index, d)]) &&
                                    ((e && D(g.options.id)) ||
                                        g.options.isInternal) &&
                                    (g = void 0);
                                g &&
                                    g.coll === a &&
                                    (g.update(b, !1), c && (g.touched = !0));
                                !g &&
                                    c &&
                                    f.collectionsWithInit[a] &&
                                    (f.collectionsWithInit[a][0].apply(
                                        f,
                                        [b]
                                            .concat(
                                                f.collectionsWithInit[a][1] ||
                                                    []
                                            )
                                            .concat([!1])
                                    ).touched = !0);
                            }),
                            c &&
                                f[a].forEach(function (b) {
                                    b.touched || b.options.isInternal
                                        ? delete b.touched
                                        : k.push(b);
                                }));
                    });
                    k.forEach(function (b) {
                        b.chart && b.remove && b.remove(!1);
                    });
                    q &&
                        f.axes.forEach(function (b) {
                            b.update({}, !1);
                        });
                    h &&
                        f.getSeriesOrderByLinks().forEach(function (b) {
                            b.chart && b.update({}, !1);
                        }, this);
                    q = m && m.width;
                    m =
                        m &&
                        (J(m.height)
                            ? ha(m.height, q || f.chartWidth)
                            : m.height);
                    n ||
                    (ba(q) && q !== f.chartWidth) ||
                    (ba(m) && m !== f.chartHeight)
                        ? f.setSize(q, m, d)
                        : S(a, !0) && f.redraw(d);
                    A(f, "afterUpdate", {
                        options: b,
                        redraw: a,
                        animation: d,
                    });
                }
                setSubtitle(b, a) {
                    this.applyDescription("subtitle", b);
                    this.layOutTitles(a);
                }
                setCaption(b, a) {
                    this.applyDescription("caption", b);
                    this.layOutTitles(a);
                }
                showResetZoom() {
                    function b() {
                        a.zoomOut();
                    }
                    const a = this,
                        c = e.lang,
                        d = a.zooming.resetButton,
                        f = d.theme,
                        g =
                            "chart" === d.relativeTo ||
                            "spacingBox" === d.relativeTo
                                ? null
                                : "scrollablePlotBox";
                    A(this, "beforeShowResetZoom", null, function () {
                        a.resetZoomButton = a.renderer
                            .button(c.resetZoom, null, null, b, f)
                            .attr({
                                align: d.position.align,
                                title: c.resetZoomTitle,
                            })
                            .addClass("highcharts-reset-zoom")
                            .add()
                            .align(d.position, !1, g);
                    });
                    A(this, "afterShowResetZoom");
                }
                zoomOut() {
                    A(this, "selection", { resetSelection: !0 }, this.zoom);
                }
                zoom(b) {
                    const a = this,
                        c = a.pointer;
                    let d = !1,
                        f;
                    !b || b.resetSelection
                        ? (a.axes.forEach(function (b) {
                              f = b.zoom();
                          }),
                          (c.initiated = !1))
                        : b.xAxis.concat(b.yAxis).forEach(function (b) {
                              const e = b.axis;
                              if (
                                  (c[e.isXAxis ? "zoomX" : "zoomY"] &&
                                      D(c.mouseDownX) &&
                                      D(c.mouseDownY) &&
                                      a.isInsidePlot(
                                          c.mouseDownX - a.plotLeft,
                                          c.mouseDownY - a.plotTop,
                                          { axis: e }
                                      )) ||
                                  !D(a.inverted ? c.mouseDownX : c.mouseDownY)
                              )
                                  (f = e.zoom(b.min, b.max)),
                                      e.displayBtn && (d = !0);
                          });
                    const e = a.resetZoomButton;
                    d && !e
                        ? a.showResetZoom()
                        : !d && aa(e) && (a.resetZoomButton = e.destroy());
                    f &&
                        a.redraw(
                            S(
                                a.options.chart.animation,
                                b && b.animation,
                                100 > a.pointCount
                            )
                        );
                }
                pan(b, a) {
                    const c = this,
                        d = c.hoverPoints;
                    a = "object" === typeof a ? a : { enabled: a, type: "x" };
                    const f = c.options.chart;
                    f && f.panning && (f.panning = a);
                    const e = a.type;
                    let g;
                    A(this, "pan", { originalEvent: b }, function () {
                        d &&
                            d.forEach(function (b) {
                                b.setState();
                            });
                        let a = c.xAxis;
                        "xy" === e
                            ? (a = a.concat(c.yAxis))
                            : "y" === e && (a = c.yAxis);
                        const f = {};
                        a.forEach(function (a) {
                            if (
                                a.options.panningEnabled &&
                                !a.options.isInternal
                            ) {
                                var d = a.horiz,
                                    k = b[d ? "chartX" : "chartY"];
                                d = d ? "mouseDownX" : "mouseDownY";
                                var h = c[d],
                                    n = a.minPointOffset || 0,
                                    l =
                                        (a.reversed && !c.inverted) ||
                                        (!a.reversed && c.inverted)
                                            ? -1
                                            : 1,
                                    m = a.getExtremes(),
                                    q = a.toValue(h - k, !0) + n * l,
                                    p =
                                        a.toValue(h + a.len - k, !0) -
                                        (n * l ||
                                            (a.isXAxis &&
                                                a.pointRangePadding) ||
                                            0),
                                    r = p < q;
                                l = a.hasVerticalPanning();
                                h = r ? p : q;
                                q = r ? q : p;
                                var t = a.panningState;
                                !l ||
                                    a.isXAxis ||
                                    (t && !t.isDirty) ||
                                    a.series.forEach(function (b) {
                                        var a = b.getProcessedData(!0);
                                        a = b.getExtremes(a.yData, !0);
                                        t ||
                                            (t = {
                                                startMin: Number.MAX_VALUE,
                                                startMax: -Number.MAX_VALUE,
                                            });
                                        ba(a.dataMin) &&
                                            ba(a.dataMax) &&
                                            ((t.startMin = Math.min(
                                                S(
                                                    b.options.threshold,
                                                    Infinity
                                                ),
                                                a.dataMin,
                                                t.startMin
                                            )),
                                            (t.startMax = Math.max(
                                                S(
                                                    b.options.threshold,
                                                    -Infinity
                                                ),
                                                a.dataMax,
                                                t.startMax
                                            )));
                                    });
                                l = Math.min(
                                    S(t && t.startMin, m.dataMin),
                                    n
                                        ? m.min
                                        : a.toValue(
                                              a.toPixels(m.min) -
                                                  a.minPixelPadding
                                          )
                                );
                                p = Math.max(
                                    S(t && t.startMax, m.dataMax),
                                    n
                                        ? m.max
                                        : a.toValue(
                                              a.toPixels(m.max) +
                                                  a.minPixelPadding
                                          )
                                );
                                a.panningState = t;
                                a.isOrdinal ||
                                    ((n = l - h),
                                    0 < n && ((q += n), (h = l)),
                                    (n = q - p),
                                    0 < n && ((q = p), (h -= n)),
                                    a.series.length &&
                                        h !== m.min &&
                                        q !== m.max &&
                                        h >= l &&
                                        q <= p &&
                                        (a.setExtremes(h, q, !1, !1, {
                                            trigger: "pan",
                                        }),
                                        !c.resetZoomButton &&
                                            h !== l &&
                                            q !== p &&
                                            e.match("y") &&
                                            (c.showResetZoom(),
                                            (a.displayBtn = !1)),
                                        (g = !0)),
                                    (f[d] = k));
                            }
                        });
                        O(f, (b, a) => {
                            c[a] = b;
                        });
                        g && c.redraw(!1);
                        P(c.container, { cursor: "move" });
                    });
                }
            }
            V(ea.prototype, {
                callbacks: [],
                collectionsWithInit: {
                    xAxis: [ea.prototype.addAxis, [!0]],
                    yAxis: [ea.prototype.addAxis, [!1]],
                    series: [ea.prototype.addSeries],
                },
                collectionsWithUpdate: ["xAxis", "yAxis", "series"],
                propsRequireDirtyBox:
                    "backgroundColor borderColor borderWidth borderRadius plotBackgroundColor plotBackgroundImage plotBorderColor plotBorderWidth plotShadow shadow".split(
                        " "
                    ),
                propsRequireReflow:
                    "margin marginTop marginRight marginBottom marginLeft spacing spacingTop spacingRight spacingBottom spacingLeft".split(
                        " "
                    ),
                propsRequireUpdateSeries:
                    "chart.inverted chart.polar chart.ignoreHiddenSeries chart.type colors plotOptions time tooltip".split(
                        " "
                    ),
            });
            ("");
            return ea;
        }
    );
    M(
        a,
        "Extensions/ScrollablePlotArea.js",
        [
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Axis/Axis.js"],
            a["Core/Chart/Chart.js"],
            a["Core/Series/Series.js"],
            a["Core/Renderer/RendererRegistry.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C, z) {
            const { stop: x } = a,
                {
                    addEvent: B,
                    createElement: u,
                    defined: v,
                    merge: l,
                    pick: p,
                } = z;
            B(I, "afterSetChartSize", function (a) {
                var m = this.options.chart.scrollablePlotArea,
                    h = m && m.minWidth;
                m = m && m.minHeight;
                let g;
                if (!this.renderer.forExport) {
                    if (h) {
                        if (
                            (this.scrollablePixelsX = h =
                                Math.max(0, h - this.chartWidth))
                        )
                            (this.scrollablePlotBox =
                                this.renderer.scrollablePlotBox =
                                    l(this.plotBox)),
                                (this.plotBox.width = this.plotWidth += h),
                                this.inverted
                                    ? (this.clipBox.height += h)
                                    : (this.clipBox.width += h),
                                (g = { 1: { name: "right", value: h } });
                    } else
                        m &&
                            ((this.scrollablePixelsY = h =
                                Math.max(0, m - this.chartHeight)),
                            v(h) &&
                                ((this.scrollablePlotBox =
                                    this.renderer.scrollablePlotBox =
                                        l(this.plotBox)),
                                (this.plotBox.height = this.plotHeight += h),
                                this.inverted
                                    ? (this.clipBox.width += h)
                                    : (this.clipBox.height += h),
                                (g = { 2: { name: "bottom", value: h } })));
                    g &&
                        !a.skipAxes &&
                        this.axes.forEach(function (a) {
                            g[a.side]
                                ? (a.getPlotLinePath = function () {
                                      let e = g[a.side].name,
                                          h = this[e],
                                          l;
                                      this[e] = h - g[a.side].value;
                                      l = y.prototype.getPlotLinePath.apply(
                                          this,
                                          arguments
                                      );
                                      this[e] = h;
                                      return l;
                                  })
                                : (a.setAxisSize(), a.setAxisTranslation());
                        });
                }
            });
            B(I, "render", function () {
                this.scrollablePixelsX || this.scrollablePixelsY
                    ? (this.setUpScrolling && this.setUpScrolling(),
                      this.applyFixed())
                    : this.fixedDiv && this.applyFixed();
            });
            I.prototype.setUpScrolling = function () {
                const a = {
                    WebkitOverflowScrolling: "touch",
                    overflowX: "hidden",
                    overflowY: "hidden",
                };
                this.scrollablePixelsX && (a.overflowX = "auto");
                this.scrollablePixelsY && (a.overflowY = "auto");
                this.scrollingParent = u(
                    "div",
                    { className: "highcharts-scrolling-parent" },
                    { position: "relative" },
                    this.renderTo
                );
                this.scrollingContainer = u(
                    "div",
                    { className: "highcharts-scrolling" },
                    a,
                    this.scrollingParent
                );
                let l;
                B(this.scrollingContainer, "scroll", () => {
                    this.pointer &&
                        (delete this.pointer.chartPosition,
                        this.hoverPoint && (l = this.hoverPoint),
                        this.pointer.runPointActions(void 0, l, !0));
                });
                this.innerContainer = u(
                    "div",
                    { className: "highcharts-inner-container" },
                    null,
                    this.scrollingContainer
                );
                this.innerContainer.appendChild(this.container);
                this.setUpScrolling = null;
            };
            I.prototype.moveFixedElements = function () {
                let a = this.container,
                    l = this.fixedRenderer,
                    h =
                        ".highcharts-breadcrumbs-group .highcharts-contextbutton .highcharts-credits .highcharts-legend .highcharts-legend-checkbox .highcharts-navigator-series .highcharts-navigator-xaxis .highcharts-navigator-yaxis .highcharts-navigator .highcharts-reset-zoom .highcharts-drillup-button .highcharts-scrollbar .highcharts-subtitle .highcharts-title".split(
                            " "
                        ),
                    g;
                this.scrollablePixelsX && !this.inverted
                    ? (g = ".highcharts-yaxis")
                    : this.scrollablePixelsX && this.inverted
                    ? (g = ".highcharts-xaxis")
                    : this.scrollablePixelsY && !this.inverted
                    ? (g = ".highcharts-xaxis")
                    : this.scrollablePixelsY &&
                      this.inverted &&
                      (g = ".highcharts-yaxis");
                g &&
                    h.push(
                        `${g}:not(.highcharts-radial-axis)`,
                        `${g}-labels:not(.highcharts-radial-axis-labels)`
                    );
                h.forEach(function (e) {
                    [].forEach.call(a.querySelectorAll(e), function (a) {
                        (a.namespaceURI === l.SVG_NS
                            ? l.box
                            : l.box.parentNode
                        ).appendChild(a);
                        a.style.pointerEvents = "auto";
                    });
                });
            };
            I.prototype.applyFixed = function () {
                var a = !this.fixedDiv,
                    l = this.options.chart,
                    h = l.scrollablePlotArea,
                    g = C.getRendererType();
                a
                    ? ((this.fixedDiv = u(
                          "div",
                          { className: "highcharts-fixed" },
                          {
                              position: "absolute",
                              overflow: "hidden",
                              pointerEvents: "none",
                              zIndex: ((l.style && l.style.zIndex) || 0) + 2,
                              top: 0,
                          },
                          null,
                          !0
                      )),
                      this.scrollingContainer &&
                          this.scrollingContainer.parentNode.insertBefore(
                              this.fixedDiv,
                              this.scrollingContainer
                          ),
                      (this.renderTo.style.overflow = "visible"),
                      (this.fixedRenderer = l =
                          new g(
                              this.fixedDiv,
                              this.chartWidth,
                              this.chartHeight,
                              this.options.chart.style
                          )),
                      (this.scrollableMask = l
                          .path()
                          .attr({
                              fill:
                                  this.options.chart.backgroundColor || "#fff",
                              "fill-opacity": p(h.opacity, 0.85),
                              zIndex: -1,
                          })
                          .addClass("highcharts-scrollable-mask")
                          .add()),
                      B(this, "afterShowResetZoom", this.moveFixedElements),
                      B(this, "afterApplyDrilldown", this.moveFixedElements),
                      B(this, "afterLayOutTitles", this.moveFixedElements))
                    : this.fixedRenderer.setSize(
                          this.chartWidth,
                          this.chartHeight
                      );
                if (this.scrollableDirty || a)
                    (this.scrollableDirty = !1), this.moveFixedElements();
                l = this.chartWidth + (this.scrollablePixelsX || 0);
                g = this.chartHeight + (this.scrollablePixelsY || 0);
                x(this.container);
                this.container.style.width = l + "px";
                this.container.style.height = g + "px";
                this.renderer.boxWrapper.attr({
                    width: l,
                    height: g,
                    viewBox: [0, 0, l, g].join(" "),
                });
                this.chartBackground.attr({ width: l, height: g });
                this.scrollingContainer.style.height = this.chartHeight + "px";
                a &&
                    (h.scrollPositionX &&
                        (this.scrollingContainer.scrollLeft =
                            this.scrollablePixelsX * h.scrollPositionX),
                    h.scrollPositionY &&
                        (this.scrollingContainer.scrollTop =
                            this.scrollablePixelsY * h.scrollPositionY));
                g = this.axisOffset;
                a = this.plotTop - g[0] - 1;
                h = this.plotLeft - g[3] - 1;
                l = this.plotTop + this.plotHeight + g[2] + 1;
                g = this.plotLeft + this.plotWidth + g[1] + 1;
                let e =
                        this.plotLeft +
                        this.plotWidth -
                        (this.scrollablePixelsX || 0),
                    w =
                        this.plotTop +
                        this.plotHeight -
                        (this.scrollablePixelsY || 0);
                a = this.scrollablePixelsX
                    ? [
                          ["M", 0, a],
                          ["L", this.plotLeft - 1, a],
                          ["L", this.plotLeft - 1, l],
                          ["L", 0, l],
                          ["Z"],
                          ["M", e, a],
                          ["L", this.chartWidth, a],
                          ["L", this.chartWidth, l],
                          ["L", e, l],
                          ["Z"],
                      ]
                    : this.scrollablePixelsY
                    ? [
                          ["M", h, 0],
                          ["L", h, this.plotTop - 1],
                          ["L", g, this.plotTop - 1],
                          ["L", g, 0],
                          ["Z"],
                          ["M", h, w],
                          ["L", h, this.chartHeight],
                          ["L", g, this.chartHeight],
                          ["L", g, w],
                          ["Z"],
                      ]
                    : [["M", 0, 0]];
                "adjustHeight" !== this.redrawTrigger &&
                    this.scrollableMask.attr({ d: a });
            };
            B(y, "afterInit", function () {
                this.chart.scrollableDirty = !0;
            });
            B(L, "show", function () {
                this.chart.scrollableDirty = !0;
            });
            ("");
        }
    );
    M(
        a,
        "Core/Axis/Stacking/StackItem.js",
        [
            a["Core/Templating.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { format: x } = a,
                { series: C } = y,
                {
                    destroyObjectProperties: z,
                    fireEvent: H,
                    isNumber: B,
                    pick: u,
                } = I;
            class v {
                constructor(a, p, t, m, h) {
                    const g = a.chart.inverted,
                        e = a.reversed;
                    this.axis = a;
                    a = this.isNegative = !!t !== !!e;
                    this.options = p = p || {};
                    this.x = m;
                    this.cumulative = this.total = null;
                    this.points = {};
                    this.hasValidPoints = !1;
                    this.stack = h;
                    this.rightCliff = this.leftCliff = 0;
                    this.alignOptions = {
                        align:
                            p.align || (g ? (a ? "left" : "right") : "center"),
                        verticalAlign:
                            p.verticalAlign ||
                            (g ? "middle" : a ? "bottom" : "top"),
                        y: p.y,
                        x: p.x,
                    };
                    this.textAlign =
                        p.textAlign || (g ? (a ? "right" : "left") : "center");
                }
                destroy() {
                    z(this, this.axis);
                }
                render(a) {
                    const l = this.axis.chart,
                        t = this.options;
                    var m = t.format;
                    m = m ? x(m, this, l) : t.formatter.call(this);
                    this.label
                        ? this.label.attr({ text: m, visibility: "hidden" })
                        : ((this.label = l.renderer.label(
                              m,
                              null,
                              void 0,
                              t.shape,
                              void 0,
                              void 0,
                              t.useHTML,
                              !1,
                              "stack-labels"
                          )),
                          (m = {
                              r: t.borderRadius || 0,
                              text: m,
                              padding: u(t.padding, 5),
                              visibility: "hidden",
                          }),
                          l.styledMode ||
                              ((m.fill = t.backgroundColor),
                              (m.stroke = t.borderColor),
                              (m["stroke-width"] = t.borderWidth),
                              this.label.css(t.style || {})),
                          this.label.attr(m),
                          this.label.added || this.label.add(a));
                    this.label.labelrank = l.plotSizeY;
                    H(this, "afterRender");
                }
                setOffset(a, p, t, m, h, g) {
                    const {
                            alignOptions: e,
                            axis: l,
                            label: v,
                            options: x,
                            textAlign: d,
                        } = this,
                        k = l.chart;
                    t = this.getStackBox({
                        xOffset: a,
                        width: p,
                        boxBottom: t,
                        boxTop: m,
                        defaultX: h,
                        xAxis: g,
                    });
                    var { verticalAlign: r } = e;
                    if (v && t) {
                        m = v.getBBox();
                        h = v.padding;
                        g = "justify" === u(x.overflow, "justify");
                        e.x = x.x || 0;
                        e.y = x.y || 0;
                        const { x: a, y: p } = this.adjustStackPosition({
                            labelBox: m,
                            verticalAlign: r,
                            textAlign: d,
                        });
                        t.x -= a;
                        t.y -= p;
                        v.align(e, !1, t);
                        (r = k.isInsidePlot(
                            v.alignAttr.x + e.x + a,
                            v.alignAttr.y + e.y + p
                        )) || (g = !1);
                        g &&
                            C.prototype.justifyDataLabel.call(
                                l,
                                v,
                                e,
                                v.alignAttr,
                                m,
                                t
                            );
                        v.attr({
                            x: v.alignAttr.x,
                            y: v.alignAttr.y,
                            rotation: x.rotation,
                            rotationOriginX: m.width / 2,
                            rotationOriginY: m.height / 2,
                        });
                        u(!g && x.crop, !0) &&
                            (r =
                                B(v.x) &&
                                B(v.y) &&
                                k.isInsidePlot(v.x - h + v.width, v.y) &&
                                k.isInsidePlot(v.x + h, v.y));
                        v[r ? "show" : "hide"]();
                    }
                    H(this, "afterSetOffset", { xOffset: a, width: p });
                }
                adjustStackPosition({
                    labelBox: a,
                    verticalAlign: p,
                    textAlign: t,
                }) {
                    const l = {
                        bottom: 0,
                        middle: 1,
                        top: 2,
                        right: 1,
                        center: 0,
                        left: -1,
                    };
                    return {
                        x: a.width / 2 + (a.width / 2) * l[t],
                        y: (a.height / 2) * l[p],
                    };
                }
                getStackBox(a) {
                    var l = this.axis;
                    const t = l.chart,
                        {
                            boxTop: m,
                            defaultX: h,
                            xOffset: g,
                            width: e,
                            boxBottom: w,
                        } = a;
                    var v = l.stacking.usePercentage
                        ? 100
                        : u(m, this.total, 0);
                    v = l.toPixels(v);
                    a = a.xAxis || t.xAxis[0];
                    const x = u(h, a.translate(this.x)) + g;
                    l = l.toPixels(
                        w ||
                            (B(l.min) &&
                                l.logarithmic &&
                                l.logarithmic.lin2log(l.min)) ||
                            0
                    );
                    l = Math.abs(v - l);
                    const d = this.isNegative;
                    return t.inverted
                        ? {
                              x: (d ? v : v - l) - t.plotLeft,
                              y: a.height - x - e,
                              width: l,
                              height: e,
                          }
                        : {
                              x: x + a.transB - t.plotLeft,
                              y: (d ? v - l : v) - t.plotTop,
                              width: e,
                              height: l,
                          };
                }
            }
            ("");
            return v;
        }
    );
    M(
        a,
        "Core/Axis/Stacking/StackingAxis.js",
        [
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Axis/Axis.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Axis/Stacking/StackItem.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C) {
            function x() {
                const b = this,
                    a = b.inverted;
                b.yAxis.forEach((b) => {
                    b.stacking &&
                        b.stacking.stacks &&
                        b.hasVisibleSeries &&
                        (b.stacking.oldStacks = b.stacking.stacks);
                });
                b.series.forEach((c) => {
                    const d = (c.xAxis && c.xAxis.options) || {};
                    !c.options.stacking ||
                        (!0 !== c.visible &&
                            !1 !== b.options.chart.ignoreHiddenSeries) ||
                        (c.stackKey = [
                            c.type,
                            q(c.options.stack, ""),
                            a ? d.top : d.left,
                            a ? d.height : d.width,
                        ].join());
                });
            }

            function H() {
                const b = this.stacking;
                if (b) {
                    var a = b.stacks;
                    r(a, function (b, c) {
                        E(b);
                        a[c] = null;
                    });
                    b && b.stackTotalGroup && b.stackTotalGroup.destroy();
                }
            }

            function B() {
                "yAxis" !== this.coll ||
                    this.stacking ||
                    (this.stacking = new G(this));
            }

            function u(b, a, d, e) {
                !w(b) || b.x !== a || (e && b.stackKey !== e)
                    ? (b = { x: a, index: 0, key: e, stackKey: e })
                    : b.index++;
                b.key = [d, a, b.index].join();
                return b;
            }

            function v() {
                const b = this,
                    a = b.stackKey,
                    d = b.yAxis.stacking.stacks,
                    e = b.processedXData,
                    g = b[b.options.stacking + "Stacker"];
                let k;
                g &&
                    [a, "-" + a].forEach((a) => {
                        let c = e.length;
                        let f;
                        for (; c--; ) {
                            var h = e[c];
                            k = b.getStackIndicator(k, h, b.index, a);
                            (f = (h = d[a] && d[a][h]) && h.points[k.key]) &&
                                g.call(b, f, h, c);
                        }
                    });
            }

            function l(b, a, d) {
                a = a.total ? 100 / a.total : 0;
                b[0] = e(b[0] * a);
                b[1] = e(b[1] * a);
                this.stackedYData[d] = b[1];
            }

            function p() {
                const b = this.yAxis.stacking;
                this.options.centerInCategory &&
                (this.is("column") || this.is("columnrange")) &&
                !this.options.stacking &&
                1 < this.chart.series.length
                    ? h.setStackedPoints.call(this, "group")
                    : b &&
                      r(b.stacks, (a, d) => {
                          "group" === d.slice(-5) &&
                              (r(a, (b) => b.destroy()), delete b.stacks[d]);
                      });
            }

            function t(b) {
                var a = this.chart;
                const f = b || this.options.stacking;
                if (
                    f &&
                    (!0 === this.visible ||
                        !1 === a.options.chart.ignoreHiddenSeries)
                ) {
                    var g = this.processedXData,
                        k = this.processedYData,
                        h = [],
                        l = k.length,
                        m = this.options,
                        p = m.threshold,
                        r = q(m.startFromThreshold && p, 0);
                    m = m.stack;
                    b = b ? `${this.type},${f}` : this.stackKey;
                    var t = "-" + b,
                        v = this.negStacks;
                    a = "group" === f ? a.yAxis[0] : this.yAxis;
                    var u = a.stacking.stacks,
                        x = a.stacking.oldStacks,
                        E,
                        G;
                    a.stacking.stacksTouched += 1;
                    for (G = 0; G < l; G++) {
                        var F = g[G];
                        var B = k[G];
                        var y = this.getStackIndicator(y, F, this.index);
                        var C = y.key;
                        var z = (E = v && B < (r ? 0 : p)) ? t : b;
                        u[z] || (u[z] = {});
                        u[z][F] ||
                            (x[z] && x[z][F]
                                ? ((u[z][F] = x[z][F]), (u[z][F].total = null))
                                : (u[z][F] = new L(
                                      a,
                                      a.options.stackLabels,
                                      !!E,
                                      F,
                                      m
                                  )));
                        z = u[z][F];
                        null !== B
                            ? ((z.points[C] = z.points[this.index] =
                                  [q(z.cumulative, r)]),
                              w(z.cumulative) || (z.base = C),
                              (z.touched = a.stacking.stacksTouched),
                              0 < y.index &&
                                  !1 === this.singleStacks &&
                                  (z.points[C][0] =
                                      z.points[this.index + "," + F + ",0"][0]))
                            : (z.points[C] = z.points[this.index] = null);
                        "percent" === f
                            ? ((E = E ? b : t),
                              v && u[E] && u[E][F]
                                  ? ((E = u[E][F]),
                                    (z.total = E.total =
                                        Math.max(E.total, z.total) +
                                            Math.abs(B) || 0))
                                  : (z.total = e(z.total + (Math.abs(B) || 0))))
                            : "group" === f
                            ? (d(B) && (B = B[0]),
                              null !== B && (z.total = (z.total || 0) + 1))
                            : (z.total = e(z.total + (B || 0)));
                        z.cumulative =
                            "group" === f
                                ? (z.total || 1) - 1
                                : e(q(z.cumulative, r) + (B || 0));
                        null !== B &&
                            (z.points[C].push(z.cumulative),
                            (h[G] = z.cumulative),
                            (z.hasValidPoints = !0));
                    }
                    "percent" === f && (a.stacking.usePercentage = !0);
                    "group" !== f && (this.stackedYData = h);
                    a.stacking.oldStacks = {};
                }
            }
            const { getDeferredAnimation: m } = a,
                {
                    series: { prototype: h },
                } = I,
                {
                    addEvent: g,
                    correctFloat: e,
                    defined: w,
                    destroyObjectProperties: E,
                    fireEvent: F,
                    isArray: d,
                    isNumber: k,
                    objectEach: r,
                    pick: q,
                } = C;
            class G {
                constructor(b) {
                    this.oldStacks = {};
                    this.stacks = {};
                    this.stacksTouched = 0;
                    this.axis = b;
                }
                buildStacks() {
                    const b = this.axis,
                        a = b.series,
                        d = b.options.reversedStacks,
                        e = a.length;
                    let g, k;
                    this.usePercentage = !1;
                    for (k = e; k--; )
                        (g = a[d ? k : e - k - 1]),
                            g.setStackedPoints(),
                            g.setGroupedPoints();
                    for (k = 0; k < e; k++) a[k].modifyStacks();
                    F(b, "afterBuildStacks");
                }
                cleanStacks() {
                    let b;
                    this.oldStacks && (b = this.stacks = this.oldStacks);
                    r(b, function (b) {
                        r(b, function (b) {
                            b.cumulative = b.total;
                        });
                    });
                }
                resetStacks() {
                    r(this.stacks, (b) => {
                        r(b, (a, d) => {
                            k(a.touched) && a.touched < this.stacksTouched
                                ? (a.destroy(), delete b[d])
                                : ((a.total = null), (a.cumulative = null));
                        });
                    });
                }
                renderStackTotals() {
                    var b = this.axis;
                    const a = b.chart,
                        d = a.renderer,
                        e = this.stacks;
                    b = m(
                        a,
                        (b.options.stackLabels &&
                            b.options.stackLabels.animation) ||
                            !1
                    );
                    const g = (this.stackTotalGroup =
                        this.stackTotalGroup ||
                        d
                            .g("stack-labels")
                            .attr({ zIndex: 6, opacity: 0 })
                            .add());
                    g.translate(a.plotLeft, a.plotTop);
                    r(e, function (b) {
                        r(b, function (b) {
                            b.render(g);
                        });
                    });
                    g.animate({ opacity: 1 }, b);
                }
            }
            var b;
            (function (b) {
                const a = [];
                b.compose = function (b, c, d) {
                    C.pushUnique(a, b) && (g(b, "init", B), g(b, "destroy", H));
                    C.pushUnique(a, c) && (c.prototype.getStacks = x);
                    C.pushUnique(a, d) &&
                        ((b = d.prototype),
                        (b.getStackIndicator = u),
                        (b.modifyStacks = v),
                        (b.percentStacker = l),
                        (b.setGroupedPoints = p),
                        (b.setStackedPoints = t));
                };
            })(b || (b = {}));
            return b;
        }
    );
    M(
        a,
        "Series/Line/LineSeries.js",
        [
            a["Core/Series/Series.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { defined: x, merge: C } = I;
            class z extends a {
                constructor() {
                    super(...arguments);
                    this.points = this.options = this.data = void 0;
                }
                drawGraph() {
                    const a = this,
                        x = this.options,
                        u = (this.gappedPath || this.getGraphPath).call(this),
                        v = this.chart.styledMode;
                    let l = [["graph", "highcharts-graph"]];
                    v ||
                        l[0].push(
                            x.lineColor || this.color || "#cccccc",
                            x.dashStyle
                        );
                    l = a.getZonesGraphs(l);
                    l.forEach(function (l, t) {
                        var m = l[0];
                        let h = a[m];
                        const g = h ? "animate" : "attr";
                        h
                            ? ((h.endX = a.preventGraphAnimation
                                  ? null
                                  : u.xMap),
                              h.animate({ d: u }))
                            : u.length &&
                              (a[m] = h =
                                  a.chart.renderer
                                      .path(u)
                                      .addClass(l[1])
                                      .attr({ zIndex: 1 })
                                      .add(a.group));
                        h &&
                            !v &&
                            ((m = {
                                stroke: l[2],
                                "stroke-width": x.lineWidth || 0,
                                fill: (a.fillGraph && a.color) || "none",
                            }),
                            l[3]
                                ? (m.dashstyle = l[3])
                                : "square" !== x.linecap &&
                                  (m["stroke-linecap"] = m["stroke-linejoin"] =
                                      "round"),
                            h[g](m).shadow(2 > t && x.shadow));
                        h && ((h.startX = u.xMap), (h.isArea = u.isArea));
                    });
                }
                getGraphPath(a, B, u) {
                    const v = this,
                        l = v.options,
                        p = [],
                        t = [];
                    let m,
                        h = l.step;
                    a = a || v.points;
                    const g = a.reversed;
                    g && a.reverse();
                    (h = { right: 1, center: 2 }[h] || (h && 3)) &&
                        g &&
                        (h = 4 - h);
                    a = this.getValidPoints(
                        a,
                        !1,
                        !(l.connectNulls && !B && !u)
                    );
                    a.forEach(function (e, g) {
                        const w = e.plotX,
                            F = e.plotY,
                            d = a[g - 1],
                            k = e.isNull || "number" !== typeof F;
                        (e.leftCliff || (d && d.rightCliff)) && !u && (m = !0);
                        k && !x(B) && 0 < g
                            ? (m = !l.connectNulls)
                            : k && !B
                            ? (m = !0)
                            : (0 === g || m
                                  ? (g = [["M", e.plotX, e.plotY]])
                                  : v.getPointSpline
                                  ? (g = [v.getPointSpline(a, e, g)])
                                  : h
                                  ? ((g =
                                        1 === h
                                            ? [["L", d.plotX, F]]
                                            : 2 === h
                                            ? [
                                                  [
                                                      "L",
                                                      (d.plotX + w) / 2,
                                                      d.plotY,
                                                  ],
                                                  ["L", (d.plotX + w) / 2, F],
                                              ]
                                            : [["L", w, d.plotY]]),
                                    g.push(["L", w, F]))
                                  : (g = [["L", w, F]]),
                              t.push(e.x),
                              h && (t.push(e.x), 2 === h && t.push(e.x)),
                              p.push.apply(p, g),
                              (m = !1));
                    });
                    p.xMap = t;
                    return (v.graphPath = p);
                }
                getZonesGraphs(a) {
                    this.zones.forEach(function (x, u) {
                        u = [
                            "zone-graph-" + u,
                            "highcharts-graph highcharts-zone-graph-" +
                                u +
                                " " +
                                (x.className || ""),
                        ];
                        this.chart.styledMode ||
                            u.push(
                                x.color || this.color,
                                x.dashStyle || this.options.dashStyle
                            );
                        a.push(u);
                    }, this);
                    return a;
                }
            }
            z.defaultOptions = C(a.defaultOptions, {
                legendSymbol: "lineMarker",
            });
            y.registerSeriesType("line", z);
            ("");
            return z;
        }
    );
    M(
        a,
        "Series/Area/AreaSeries.js",
        [
            a["Core/Color/Color.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const {
                    seriesTypes: { line: x },
                } = y,
                { extend: C, merge: z, objectEach: H, pick: B } = I;
            class u extends x {
                constructor() {
                    super(...arguments);
                    this.points = this.options = this.data = void 0;
                }
                drawGraph() {
                    this.areaPath = [];
                    super.drawGraph.apply(this);
                    const a = this,
                        l = this.areaPath,
                        p = this.options,
                        t = [
                            [
                                "area",
                                "highcharts-area",
                                this.color,
                                p.fillColor,
                            ],
                        ];
                    this.zones.forEach(function (l, h) {
                        t.push([
                            "zone-area-" + h,
                            "highcharts-area highcharts-zone-area-" +
                                h +
                                " " +
                                l.className,
                            l.color || a.color,
                            l.fillColor || p.fillColor,
                        ]);
                    });
                    t.forEach(function (m) {
                        const h = m[0],
                            g = {};
                        let e = a[h];
                        const t = e ? "animate" : "attr";
                        e
                            ? ((e.endX = a.preventGraphAnimation
                                  ? null
                                  : l.xMap),
                              e.animate({ d: l }))
                            : ((g.zIndex = 0),
                              (e = a[h] =
                                  a.chart.renderer
                                      .path(l)
                                      .addClass(m[1])
                                      .add(a.group)),
                              (e.isArea = !0));
                        a.chart.styledMode ||
                            (m[3]
                                ? (g.fill = m[3])
                                : ((g.fill = m[2]),
                                  (g["fill-opacity"] = B(
                                      p.fillOpacity,
                                      0.75
                                  ))));
                        e[t](g);
                        e.startX = l.xMap;
                        e.shiftUnit = p.step ? 2 : 1;
                    });
                }
                getGraphPath(a) {
                    var l = x.prototype.getGraphPath,
                        p = this.options;
                    const t = p.stacking,
                        m = this.yAxis,
                        h = [],
                        g = [],
                        e = this.index,
                        w = m.stacking.stacks[this.stackKey],
                        v = p.threshold,
                        u = Math.round(m.getThreshold(p.threshold));
                    p = B(p.connectNulls, "percent" === t);
                    var d = function (d, b, f) {
                        var c = a[d];
                        d = t && w[c.x].points[e];
                        const n = c[f + "Null"] || 0;
                        f = c[f + "Cliff"] || 0;
                        let l, q;
                        c = !0;
                        f || n
                            ? ((l = (n ? d[0] : d[1]) + f),
                              (q = d[0] + f),
                              (c = !!n))
                            : !t && a[b] && a[b].isNull && (l = q = v);
                        "undefined" !== typeof l &&
                            (g.push({
                                plotX: k,
                                plotY: null === l ? u : m.getThreshold(l),
                                isNull: c,
                                isCliff: !0,
                            }),
                            h.push({
                                plotX: k,
                                plotY: null === q ? u : m.getThreshold(q),
                                doCurve: !1,
                            }));
                    };
                    let k;
                    a = a || this.points;
                    t && (a = this.getStackPoints(a));
                    for (let e = 0, b = a.length; e < b; ++e) {
                        t ||
                            (a[e].leftCliff =
                                a[e].rightCliff =
                                a[e].leftNull =
                                a[e].rightNull =
                                    void 0);
                        var r = a[e].isNull;
                        k = B(a[e].rectPlotX, a[e].plotX);
                        var q = t ? B(a[e].yBottom, u) : u;
                        if (!r || p)
                            p || d(e, e - 1, "left"),
                                (r && !t && p) ||
                                    (g.push(a[e]),
                                    h.push({ x: e, plotX: k, plotY: q })),
                                p || d(e, e + 1, "right");
                    }
                    d = l.call(this, g, !0, !0);
                    h.reversed = !0;
                    r = l.call(this, h, !0, !0);
                    (q = r[0]) && "M" === q[0] && (r[0] = ["L", q[1], q[2]]);
                    r = d.concat(r);
                    r.length && r.push(["Z"]);
                    l = l.call(this, g, !1, p);
                    r.xMap = d.xMap;
                    this.areaPath = r;
                    return l;
                }
                getStackPoints(a) {
                    const l = this,
                        p = [],
                        t = [],
                        m = this.xAxis,
                        h = this.yAxis,
                        g = h.stacking.stacks[this.stackKey],
                        e = {},
                        w = h.series,
                        v = w.length,
                        u = h.options.reversedStacks ? 1 : -1,
                        d = w.indexOf(l);
                    a = a || this.points;
                    if (this.options.stacking) {
                        for (let d = 0; d < a.length; d++)
                            (a[d].leftNull = a[d].rightNull = void 0),
                                (e[a[d].x] = a[d]);
                        H(g, function (a, d) {
                            null !== a.total && t.push(d);
                        });
                        t.sort(function (a, d) {
                            return a - d;
                        });
                        const k = w.map((a) => a.visible);
                        t.forEach(function (a, q) {
                            let r = 0,
                                b,
                                f;
                            if (e[a] && !e[a].isNull)
                                p.push(e[a]),
                                    [-1, 1].forEach(function (c) {
                                        const h =
                                                1 === c
                                                    ? "rightNull"
                                                    : "leftNull",
                                            m = g[t[q + c]];
                                        let p = 0;
                                        if (m) {
                                            let c = d;
                                            for (; 0 <= c && c < v; ) {
                                                const d = w[c].index;
                                                b = m.points[d];
                                                b ||
                                                    (d === l.index
                                                        ? (e[a][h] = !0)
                                                        : k[c] &&
                                                          (f =
                                                              g[a].points[d]) &&
                                                          (p -= f[1] - f[0]));
                                                c += u;
                                            }
                                        }
                                        e[a][
                                            1 === c ? "rightCliff" : "leftCliff"
                                        ] = p;
                                    });
                            else {
                                let c = d;
                                for (; 0 <= c && c < v; ) {
                                    if ((b = g[a].points[w[c].index])) {
                                        r = b[1];
                                        break;
                                    }
                                    c += u;
                                }
                                r = B(r, 0);
                                r = h.translate(r, 0, 1, 0, 1);
                                p.push({
                                    isNull: !0,
                                    plotX: m.translate(a, 0, 0, 0, 1),
                                    x: a,
                                    plotY: r,
                                    yBottom: r,
                                });
                            }
                        });
                    }
                    return p;
                }
            }
            u.defaultOptions = z(x.defaultOptions, {
                threshold: 0,
                legendSymbol: "rectangle",
            });
            C(u.prototype, { singleStacks: !1 });
            y.registerSeriesType("area", u);
            ("");
            return u;
        }
    );
    M(
        a,
        "Series/Spline/SplineSeries.js",
        [a["Core/Series/SeriesRegistry.js"], a["Core/Utilities.js"]],
        function (a, y) {
            const { line: x } = a.seriesTypes,
                { merge: L, pick: C } = y;
            class z extends x {
                constructor() {
                    super(...arguments);
                    this.points = this.options = this.data = void 0;
                }
                getPointSpline(a, x, u) {
                    const v = x.plotX || 0,
                        l = x.plotY || 0,
                        p = a[u - 1];
                    u = a[u + 1];
                    let t, m;
                    let h;
                    if (
                        p &&
                        !p.isNull &&
                        !1 !== p.doCurve &&
                        !x.isCliff &&
                        u &&
                        !u.isNull &&
                        !1 !== u.doCurve &&
                        !x.isCliff
                    ) {
                        a = p.plotY || 0;
                        var g = u.plotX || 0;
                        u = u.plotY || 0;
                        let e = 0;
                        t = (1.5 * v + (p.plotX || 0)) / 2.5;
                        m = (1.5 * l + a) / 2.5;
                        g = (1.5 * v + g) / 2.5;
                        h = (1.5 * l + u) / 2.5;
                        g !== t && (e = ((h - m) * (g - v)) / (g - t) + l - h);
                        m += e;
                        h += e;
                        m > a && m > l
                            ? ((m = Math.max(a, l)), (h = 2 * l - m))
                            : m < a &&
                              m < l &&
                              ((m = Math.min(a, l)), (h = 2 * l - m));
                        h > u && h > l
                            ? ((h = Math.max(u, l)), (m = 2 * l - h))
                            : h < u &&
                              h < l &&
                              ((h = Math.min(u, l)), (m = 2 * l - h));
                        x.rightContX = g;
                        x.rightContY = h;
                    }
                    x = [
                        "C",
                        C(p.rightContX, p.plotX, 0),
                        C(p.rightContY, p.plotY, 0),
                        C(t, v, 0),
                        C(m, l, 0),
                        v,
                        l,
                    ];
                    p.rightContX = p.rightContY = void 0;
                    return x;
                }
            }
            z.defaultOptions = L(x.defaultOptions);
            a.registerSeriesType("spline", z);
            ("");
            return z;
        }
    );
    M(
        a,
        "Series/AreaSpline/AreaSplineSeries.js",
        [
            a["Series/Spline/SplineSeries.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const {
                    area: x,
                    area: { prototype: C },
                } = y.seriesTypes,
                { extend: z, merge: H } = I;
            class B extends a {
                constructor() {
                    super(...arguments);
                    this.options = this.points = this.data = void 0;
                }
            }
            B.defaultOptions = H(a.defaultOptions, x.defaultOptions);
            z(B.prototype, {
                getGraphPath: C.getGraphPath,
                getStackPoints: C.getStackPoints,
                drawGraph: C.drawGraph,
            });
            y.registerSeriesType("areaspline", B);
            ("");
            return B;
        }
    );
    M(a, "Series/Column/ColumnSeriesDefaults.js", [], function () {
        "";
        return {
            borderRadius: 3,
            centerInCategory: !1,
            groupPadding: 0.2,
            marker: null,
            pointPadding: 0.1,
            minPointLength: 0,
            cropThreshold: 50,
            pointRange: null,
            states: {
                hover: { halo: !1, brightness: 0.1 },
                select: { color: "#cccccc", borderColor: "#000000" },
            },
            dataLabels: { align: void 0, verticalAlign: void 0, y: void 0 },
            startFromThreshold: !0,
            stickyTracking: !1,
            tooltip: { distance: 6 },
            threshold: 0,
            borderColor: "#ffffff",
        };
    });
    M(
        a,
        "Series/Column/ColumnSeries.js",
        [
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Color/Color.js"],
            a["Series/Column/ColumnSeriesDefaults.js"],
            a["Core/Globals.js"],
            a["Core/Series/Series.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C, z, H) {
            const { animObject: x } = a,
                { parse: u } = y,
                { hasTouch: v, noop: l } = L,
                {
                    clamp: p,
                    defined: t,
                    extend: m,
                    fireEvent: h,
                    isArray: g,
                    isNumber: e,
                    merge: w,
                    pick: E,
                    objectEach: F,
                } = H;
            class d extends C {
                constructor() {
                    super(...arguments);
                    this.points =
                        this.options =
                        this.group =
                        this.data =
                        this.borderWidth =
                            void 0;
                }
                animate(a) {
                    const d = this,
                        e = this.yAxis,
                        g = e.pos,
                        b = d.options,
                        f = this.chart.inverted,
                        c = {},
                        k = f ? "translateX" : "translateY";
                    let h;
                    a
                        ? ((c.scaleY = 0.001),
                          (a = p(e.toPixels(b.threshold), g, g + e.len)),
                          f ? (c.translateX = a - e.len) : (c.translateY = a),
                          d.clipBox && d.setClip(),
                          d.group.attr(c))
                        : ((h = Number(d.group.attr(k))),
                          d.group.animate(
                              { scaleY: 1 },
                              m(x(d.options.animation), {
                                  step: function (b, a) {
                                      d.group &&
                                          ((c[k] = h + a.pos * (g - h)),
                                          d.group.attr(c));
                                  },
                              })
                          ));
                }
                init(a, d) {
                    super.init.apply(this, arguments);
                    const e = this;
                    a = e.chart;
                    a.hasRendered &&
                        a.series.forEach(function (a) {
                            a.type === e.type && (a.isDirty = !0);
                        });
                }
                getColumnMetrics() {
                    const a = this;
                    var d = a.options;
                    const e = a.xAxis,
                        g = a.yAxis;
                    var b = e.options.reversedStacks;
                    b = (e.reversed && !b) || (!e.reversed && b);
                    const f = {};
                    let c,
                        h = 0;
                    !1 === d.grouping
                        ? (h = 1)
                        : a.chart.series.forEach(function (b) {
                              const d = b.yAxis,
                                  e = b.options;
                              let k;
                              b.type !== a.type ||
                                  (!b.visible &&
                                      a.chart.options.chart
                                          .ignoreHiddenSeries) ||
                                  g.len !== d.len ||
                                  g.pos !== d.pos ||
                                  (e.stacking && "group" !== e.stacking
                                      ? ((c = b.stackKey),
                                        "undefined" === typeof f[c] &&
                                            (f[c] = h++),
                                        (k = f[c]))
                                      : !1 !== e.grouping && (k = h++),
                                  (b.columnIndex = k));
                          });
                    const l = Math.min(
                            Math.abs(e.transA) *
                                ((e.ordinal && e.ordinal.slope) ||
                                    d.pointRange ||
                                    e.closestPointRange ||
                                    e.tickInterval ||
                                    1),
                            e.len
                        ),
                        m = l * d.groupPadding,
                        p = (l - 2 * m) / (h || 1);
                    d = Math.min(
                        d.maxPointWidth || e.len,
                        E(d.pointWidth, p * (1 - 2 * d.pointPadding))
                    );
                    a.columnMetrics = {
                        width: d,
                        offset:
                            (p - d) / 2 +
                            (m +
                                ((a.columnIndex || 0) + (b ? 1 : 0)) * p -
                                l / 2) *
                                (b ? -1 : 1),
                        paddedWidth: p,
                        columnCount: h,
                    };
                    return a.columnMetrics;
                }
                crispCol(a, d, e, g) {
                    var b = this.borderWidth,
                        f = -(b % 2 ? 0.5 : 0);
                    b = b % 2 ? 0.5 : 1;
                    this.options.crisp &&
                        ((e = Math.round(a + e) + f),
                        (a = Math.round(a) + f),
                        (e -= a));
                    g = Math.round(d + g) + b;
                    f = 0.5 >= Math.abs(d) && 0.5 < g;
                    d = Math.round(d) + b;
                    g -= d;
                    f && g && (--d, (g += 1));
                    return { x: a, y: d, width: e, height: g };
                }
                adjustForMissingColumns(a, d, e, h) {
                    const b = this.options.stacking;
                    if (!e.isNull && 1 < h.columnCount) {
                        const f = this.yAxis.options.reversedStacks;
                        let c = 0,
                            k = f ? 0 : -h.columnCount;
                        F(
                            this.yAxis.stacking && this.yAxis.stacking.stacks,
                            (a) => {
                                if ("number" === typeof e.x) {
                                    const d = a[e.x.toString()];
                                    d &&
                                        ((a = d.points[this.index]),
                                        b
                                            ? (a && (c = k),
                                              d.hasValidPoints &&
                                                  (f ? k++ : k--))
                                            : g(a) &&
                                              ((a = Object.keys(d.points)
                                                  .filter(
                                                      (b) =>
                                                          !b.match(",") &&
                                                          d.points[b] &&
                                                          1 < d.points[b].length
                                                  )
                                                  .map(parseFloat)
                                                  .sort((b, a) => a - b)),
                                              (c = a.indexOf(this.index)),
                                              (k = a.length)));
                                }
                            }
                        );
                        a =
                            (e.plotX || 0) +
                            ((k - 1) * h.paddedWidth + d) / 2 -
                            d -
                            c * h.paddedWidth;
                    }
                    return a;
                }
                translate() {
                    const a = this,
                        d = a.chart,
                        g = a.options;
                    var l = (a.dense =
                        2 > a.closestPointRange * a.xAxis.transA);
                    l = a.borderWidth = E(g.borderWidth, l ? 0 : 1);
                    const b = a.xAxis,
                        f = a.yAxis,
                        c = g.threshold,
                        n = E(g.minPointLength, 5),
                        m = a.getColumnMetrics(),
                        w = m.width,
                        v = (a.pointXOffset = m.offset),
                        u = a.dataMin,
                        x = a.dataMax;
                    let F = (a.barW = Math.max(w, 1 + 2 * l)),
                        y = (a.translatedThreshold = f.getThreshold(c));
                    d.inverted && (y -= 0.5);
                    g.pointPadding && (F = Math.ceil(F));
                    C.prototype.translate.apply(a);
                    a.points.forEach(function (k) {
                        const h = E(k.yBottom, y);
                        var l = 999 + Math.abs(h),
                            q = k.plotX || 0;
                        l = p(k.plotY, -l, f.len + l);
                        let r = Math.min(l, h),
                            D = Math.max(l, h) - r,
                            z = w,
                            B = q + v,
                            G = F;
                        n &&
                            Math.abs(D) < n &&
                            ((D = n),
                            (q =
                                (!f.reversed && !k.negative) ||
                                (f.reversed && k.negative)),
                            e(c) &&
                                e(x) &&
                                k.y === c &&
                                x <= c &&
                                (f.min || 0) < c &&
                                (u !== x || (f.max || 0) <= c) &&
                                ((q = !q), (k.negative = !k.negative)),
                            (r =
                                Math.abs(r - y) > n ? h - n : y - (q ? n : 0)));
                        t(k.options.pointWidth) &&
                            ((z = G = Math.ceil(k.options.pointWidth)),
                            (B -= Math.round((z - w) / 2)));
                        g.centerInCategory &&
                            (B = a.adjustForMissingColumns(B, z, k, m));
                        k.barX = B;
                        k.pointWidth = z;
                        k.tooltipPos = d.inverted
                            ? [
                                  p(
                                      f.len + f.pos - d.plotLeft - l,
                                      f.pos - d.plotLeft,
                                      f.len + f.pos - d.plotLeft
                                  ),
                                  b.len + b.pos - d.plotTop - B - G / 2,
                                  D,
                              ]
                            : [
                                  b.left - d.plotLeft + B + G / 2,
                                  p(
                                      l + f.pos - d.plotTop,
                                      f.pos - d.plotTop,
                                      f.len + f.pos - d.plotTop
                                  ),
                                  D,
                              ];
                        k.shapeType =
                            a.pointClass.prototype.shapeType || "roundedRect";
                        k.shapeArgs = a.crispCol(
                            B,
                            k.isNull ? y : r,
                            G,
                            k.isNull ? 0 : D
                        );
                    });
                    h(this, "afterColumnTranslate");
                }
                drawGraph() {
                    this.group[this.dense ? "addClass" : "removeClass"](
                        "highcharts-dense-data"
                    );
                }
                pointAttribs(a, d) {
                    const e = this.options;
                    var g = this.pointAttrToOptions || {},
                        b = g.stroke || "borderColor";
                    const f = g["stroke-width"] || "borderWidth";
                    let c,
                        k = (a && a.color) || this.color,
                        h = (a && a[b]) || e[b] || k;
                    g = (a && a.options.dashStyle) || e.dashStyle;
                    let l = (a && a[f]) || e[f] || this[f] || 0,
                        m = E(a && a.opacity, e.opacity, 1);
                    a &&
                        this.zones.length &&
                        ((c = a.getZone()),
                        (k =
                            a.options.color ||
                            (c && (c.color || a.nonZonedColor)) ||
                            this.color),
                        c &&
                            ((h = c.borderColor || h),
                            (g = c.dashStyle || g),
                            (l = c.borderWidth || l)));
                    d &&
                        a &&
                        ((a = w(
                            e.states[d],
                            (a.options.states && a.options.states[d]) || {}
                        )),
                        (d = a.brightness),
                        (k =
                            a.color ||
                            ("undefined" !== typeof d &&
                                u(k).brighten(a.brightness).get()) ||
                            k),
                        (h = a[b] || h),
                        (l = a[f] || l),
                        (g = a.dashStyle || g),
                        (m = E(a.opacity, m)));
                    b = { fill: k, stroke: h, "stroke-width": l, opacity: m };
                    g && (b.dashstyle = g);
                    return b;
                }
                drawPoints(a = this.points) {
                    const d = this,
                        g = this.chart,
                        k = d.options,
                        b = g.renderer,
                        f = k.animationLimit || 250;
                    let c;
                    a.forEach(function (a) {
                        let h = a.graphic,
                            l = !!h,
                            n = h && g.pointCount < f ? "animate" : "attr";
                        if (e(a.plotY) && null !== a.y) {
                            c = a.shapeArgs;
                            h && a.hasNewShapeType() && (h = h.destroy());
                            d.enabledDataSorting &&
                                (a.startXPos = d.xAxis.reversed
                                    ? -(c ? c.width || 0 : 0)
                                    : d.xAxis.width);
                            h ||
                                ((a.graphic = h =
                                    b[a.shapeType](c).add(
                                        a.group || d.group
                                    )) &&
                                    d.enabledDataSorting &&
                                    g.hasRendered &&
                                    g.pointCount < f &&
                                    (h.attr({ x: a.startXPos }),
                                    (l = !0),
                                    (n = "animate")));
                            if (h && l) h[n](w(c));
                            g.styledMode ||
                                h[n](
                                    d.pointAttribs(a, a.selected && "select")
                                ).shadow(!1 !== a.allowShadow && k.shadow);
                            h &&
                                (h.addClass(a.getClassName(), !0),
                                h.attr({
                                    visibility: a.visible
                                        ? "inherit"
                                        : "hidden",
                                }));
                        } else h && (a.graphic = h.destroy());
                    });
                }
                drawTracker(a = this.points) {
                    const d = this,
                        e = d.chart,
                        k = e.pointer,
                        b = function (a) {
                            const b = k.getPointFromEvent(a);
                            "undefined" !== typeof b &&
                                d.options.enableMouseTracking &&
                                ((k.isDirectTouch = !0), b.onMouseOver(a));
                        };
                    let f;
                    a.forEach(function (a) {
                        f = g(a.dataLabels)
                            ? a.dataLabels
                            : a.dataLabel
                            ? [a.dataLabel]
                            : [];
                        a.graphic && (a.graphic.element.point = a);
                        f.forEach(function (b) {
                            b.div ? (b.div.point = a) : (b.element.point = a);
                        });
                    });
                    d._hasTracking ||
                        (d.trackerGroups.forEach(function (a) {
                            if (d[a]) {
                                d[a]
                                    .addClass("highcharts-tracker")
                                    .on("mouseover", b)
                                    .on("mouseout", function (a) {
                                        k.onTrackerMouseOut(a);
                                    });
                                if (v) d[a].on("touchstart", b);
                                !e.styledMode &&
                                    d.options.cursor &&
                                    d[a].css({ cursor: d.options.cursor });
                            }
                        }),
                        (d._hasTracking = !0));
                    h(this, "afterDrawTracker");
                }
                remove() {
                    const a = this,
                        d = a.chart;
                    d.hasRendered &&
                        d.series.forEach(function (d) {
                            d.type === a.type && (d.isDirty = !0);
                        });
                    C.prototype.remove.apply(a, arguments);
                }
            }
            d.defaultOptions = w(C.defaultOptions, I);
            m(d.prototype, {
                cropShoulder: 0,
                directTouch: !0,
                getSymbol: l,
                negStacks: !0,
                trackerGroups: ["group", "dataLabelsGroup"],
            });
            z.registerSeriesType("column", d);
            ("");
            return d;
        }
    );
    M(
        a,
        "Core/Series/DataLabel.js",
        [
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Templating.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { getDeferredAnimation: x } = a,
                { format: C } = y,
                {
                    defined: z,
                    extend: H,
                    fireEvent: B,
                    isArray: u,
                    isString: v,
                    merge: l,
                    objectEach: p,
                    pick: t,
                    splat: m,
                } = I;
            var h;
            (function (a) {
                function e(a, d, c, e, g) {
                    const b = this.chart;
                    var f = this.isCartesian && b.inverted;
                    const k = this.enabledDataSorting;
                    var h = a.plotX,
                        l = a.plotY;
                    const n = c.rotation;
                    var m = c.align;
                    l =
                        z(h) &&
                        z(l) &&
                        b.isInsidePlot(h, Math.round(l), {
                            inverted: f,
                            paneCoordinates: !0,
                            series: this,
                        });
                    let p = "justify" === t(c.overflow, k ? "none" : "justify");
                    f =
                        this.visible &&
                        !1 !== a.visible &&
                        z(h) &&
                        (a.series.forceDL ||
                            (k && !p) ||
                            l ||
                            (t(c.inside, !!this.options.stacking) &&
                                e &&
                                b.isInsidePlot(
                                    h,
                                    f ? e.x + 1 : e.y + e.height - 1,
                                    {
                                        inverted: f,
                                        paneCoordinates: !0,
                                        series: this,
                                    }
                                )));
                    h = a.pos();
                    if (f && h) {
                        n && d.attr({ align: m });
                        m = d.getBBox(!0);
                        var q = [0, 0];
                        var r = b.renderer.fontMetrics(d).b;
                        e = H(
                            {
                                x: h[0],
                                y: Math.round(h[1]),
                                width: 0,
                                height: 0,
                            },
                            e
                        );
                        H(c, { width: m.width, height: m.height });
                        n
                            ? ((p = !1),
                              (q = b.renderer.rotCorr(r, n)),
                              (r = {
                                  x: e.x + (c.x || 0) + e.width / 2 + q.x,
                                  y:
                                      e.y +
                                      (c.y || 0) +
                                      { top: 0, middle: 0.5, bottom: 1 }[
                                          c.verticalAlign
                                      ] *
                                          e.height,
                              }),
                              (q = [
                                  m.x - Number(d.attr("x")),
                                  m.y - Number(d.attr("y")),
                              ]),
                              k &&
                                  this.xAxis &&
                                  !p &&
                                  this.setDataLabelStartPos(a, d, g, l, r),
                              d[g ? "attr" : "animate"](r))
                            : (k &&
                                  this.xAxis &&
                                  !p &&
                                  this.setDataLabelStartPos(a, d, g, l, e),
                              d.align(c, void 0, e),
                              (r = d.alignAttr));
                        if (p && 0 <= e.height)
                            this.justifyDataLabel(d, c, r, m, e, g);
                        else if (t(c.crop, !0)) {
                            let { x: a, y: c } = r;
                            a += q[0];
                            c += q[1];
                            f =
                                b.isInsidePlot(a, c, {
                                    paneCoordinates: !0,
                                    series: this,
                                }) &&
                                b.isInsidePlot(a + m.width, c + m.height, {
                                    paneCoordinates: !0,
                                    series: this,
                                });
                        }
                        if (c.shape && !n)
                            d[g ? "attr" : "animate"]({
                                anchorX: h[0],
                                anchorY: h[1],
                            });
                    }
                    g && k && (d.placed = !1);
                    f || (k && !p) ? d.show() : (d.hide(), (d.placed = !1));
                }

                function g(a, d) {
                    var b = d.filter;
                    return b
                        ? ((d = b.operator),
                          (a = a[b.property]),
                          (b = b.value),
                          (">" === d && a > b) ||
                          ("<" === d && a < b) ||
                          (">=" === d && a >= b) ||
                          ("<=" === d && a <= b) ||
                          ("==" === d && a == b) ||
                          ("===" === d && a === b)
                              ? !0
                              : !1)
                        : !0;
                }

                function h() {
                    return this.plotGroup(
                        "dataLabelsGroup",
                        "data-labels",
                        this.hasRendered ? "inherit" : "hidden",
                        this.options.dataLabels.zIndex || 6
                    );
                }

                function F(a) {
                    const b = this.hasRendered || 0,
                        c = this.initDataLabelsGroup().attr({ opacity: +b });
                    !b &&
                        c &&
                        (this.visible && c.show(),
                        this.options.animation
                            ? c.animate({ opacity: 1 }, a)
                            : c.attr({ opacity: 1 }));
                    return c;
                }

                function d(a = this.points) {
                    var b, c;
                    const d = this,
                        e = d.chart,
                        k = d.options,
                        h = e.renderer,
                        { backgroundColor: l, plotBackgroundColor: q } =
                            e.options.chart,
                        w = e.options.plotOptions,
                        E = h.getContrast(
                            (v(q) && q) || (v(l) && l) || "#000000"
                        );
                    let F = k.dataLabels,
                        A,
                        y;
                    var G = m(F)[0];
                    const H = G.animation;
                    G = G.defer ? x(e, H, d) : { defer: 0, duration: 0 };
                    F = r(
                        r(
                            null ===
                                (b =
                                    null === w || void 0 === w
                                        ? void 0
                                        : w.series) || void 0 === b
                                ? void 0
                                : b.dataLabels,
                            null ===
                                (c =
                                    null === w || void 0 === w
                                        ? void 0
                                        : w[d.type]) || void 0 === c
                                ? void 0
                                : c.dataLabels
                        ),
                        F
                    );
                    B(this, "drawDataLabels");
                    if (u(F) || F.enabled || d._hasPointLabels)
                        (y = this.initDataLabels(G)),
                            a.forEach((a) => {
                                var b;
                                const c = a.dataLabels || [];
                                A = m(
                                    r(
                                        F,
                                        a.dlOptions ||
                                            (null === (b = a.options) ||
                                            void 0 === b
                                                ? void 0
                                                : b.dataLabels)
                                    )
                                );
                                A.forEach((b, f) => {
                                    var l,
                                        m =
                                            b.enabled &&
                                            (!a.isNull || a.dataLabelOnNull) &&
                                            g(a, b);
                                    const n = a.connectors
                                            ? a.connectors[f]
                                            : a.connector,
                                        q = b.style || {};
                                    let r = {},
                                        w = c[f],
                                        u = !w;
                                    const x = t(b.distance, a.labelDistance);
                                    if (m) {
                                        var A = t(
                                            b[a.formatPrefix + "Format"],
                                            b.format
                                        );
                                        var F = a.getLabelConfig();
                                        F = z(A)
                                            ? C(A, F, e)
                                            : (
                                                  b[
                                                      a.formatPrefix +
                                                          "Formatter"
                                                  ] || b.formatter
                                              ).call(F, b);
                                        A = b.rotation;
                                        e.styledMode ||
                                            ((q.color = t(
                                                b.color,
                                                q.color,
                                                v(d.color) ? d.color : void 0,
                                                "#000000"
                                            )),
                                            "contrast" === q.color
                                                ? ((a.contrastColor =
                                                      h.getContrast(
                                                          a.color || d.color
                                                      )),
                                                  (q.color =
                                                      (!z(x) && b.inside) ||
                                                      0 > (x || 0) ||
                                                      k.stacking
                                                          ? a.contrastColor
                                                          : E))
                                                : delete a.contrastColor,
                                            k.cursor && (q.cursor = k.cursor));
                                        r = {
                                            r: b.borderRadius || 0,
                                            rotation: A,
                                            padding: b.padding,
                                            zIndex: 1,
                                        };
                                        if (!e.styledMode) {
                                            const {
                                                backgroundColor: c,
                                                borderColor: d,
                                            } = b;
                                            r.fill = "auto" === c ? a.color : c;
                                            r.stroke =
                                                "auto" === d ? a.color : d;
                                            r["stroke-width"] = b.borderWidth;
                                        }
                                        p(r, (a, b) => {
                                            "undefined" === typeof a &&
                                                delete r[b];
                                        });
                                    }
                                    !w ||
                                        (m &&
                                            z(F) &&
                                            !!w.div === !!b.useHTML &&
                                            ((w.rotation && b.rotation) ||
                                                w.rotation === b.rotation)) ||
                                        ((w = void 0),
                                        (u = !0),
                                        n &&
                                            a.connector &&
                                            ((a.connector =
                                                a.connector.destroy()),
                                            a.connectors &&
                                                (1 === a.connectors.length
                                                    ? delete a.connectors
                                                    : delete a.connectors[f])));
                                    m &&
                                        z(F) &&
                                        (w
                                            ? (r.text = F)
                                            : (w = A
                                                  ? h
                                                        .text(
                                                            F,
                                                            0,
                                                            0,
                                                            b.useHTML
                                                        )
                                                        .addClass(
                                                            "highcharts-data-label"
                                                        )
                                                  : h.label(
                                                        F,
                                                        0,
                                                        0,
                                                        b.shape,
                                                        void 0,
                                                        void 0,
                                                        b.useHTML,
                                                        void 0,
                                                        "data-label"
                                                    )) &&
                                              w.addClass(
                                                  " highcharts-data-label-color-" +
                                                      a.colorIndex +
                                                      " " +
                                                      (b.className || "") +
                                                      (b.useHTML
                                                          ? " highcharts-tracker"
                                                          : "")
                                              ),
                                        w &&
                                            ((w.options = b),
                                            w.attr(r),
                                            e.styledMode ||
                                                w.css(q).shadow(b.shadow),
                                            (m =
                                                b[
                                                    a.formatPrefix + "TextPath"
                                                ] || b.textPath) &&
                                                !b.useHTML &&
                                                (w.setTextPath(
                                                    (null ===
                                                        (l =
                                                            a.getDataLabelPath) ||
                                                    void 0 === l
                                                        ? void 0
                                                        : l.call(a, w)) ||
                                                        a.graphic,
                                                    m
                                                ),
                                                a.dataLabelPath &&
                                                    !m.enabled &&
                                                    (a.dataLabelPath =
                                                        a.dataLabelPath.destroy())),
                                            w.added || w.add(y),
                                            d.alignDataLabel(
                                                a,
                                                w,
                                                b,
                                                void 0,
                                                u
                                            ),
                                            (w.isActive = !0),
                                            c[f] &&
                                                c[f] !== w &&
                                                c[f].destroy(),
                                            (c[f] = w)));
                                });
                                for (b = c.length; b--; )
                                    c[b].isActive
                                        ? (c[b].isActive = !1)
                                        : (c[b].destroy(), c.splice(b, 1));
                                a.dataLabel = c[0];
                                a.dataLabels = c;
                            });
                    B(this, "afterDrawDataLabels");
                }

                function k(a, d, c, e, g, k) {
                    const b = this.chart,
                        f = d.align,
                        h = d.verticalAlign,
                        l = a.box ? 0 : a.padding || 0;
                    let { x: m = 0, y: n = 0 } = d,
                        p,
                        q;
                    p = (c.x || 0) + l;
                    0 > p &&
                        ("right" === f && 0 <= m
                            ? ((d.align = "left"), (d.inside = !0))
                            : (m -= p),
                        (q = !0));
                    p = (c.x || 0) + e.width - l;
                    p > b.plotWidth &&
                        ("left" === f && 0 >= m
                            ? ((d.align = "right"), (d.inside = !0))
                            : (m += b.plotWidth - p),
                        (q = !0));
                    p = c.y + l;
                    0 > p &&
                        ("bottom" === h && 0 <= n
                            ? ((d.verticalAlign = "top"), (d.inside = !0))
                            : (n -= p),
                        (q = !0));
                    p = (c.y || 0) + e.height - l;
                    p > b.plotHeight &&
                        ("top" === h && 0 >= n
                            ? ((d.verticalAlign = "bottom"), (d.inside = !0))
                            : (n += b.plotHeight - p),
                        (q = !0));
                    q &&
                        ((d.x = m),
                        (d.y = n),
                        (a.placed = !k),
                        a.align(d, void 0, g));
                    return q;
                }

                function r(a, d) {
                    let b = [],
                        f;
                    if (u(a) && !u(d))
                        b = a.map(function (a) {
                            return l(a, d);
                        });
                    else if (u(d) && !u(a))
                        b = d.map(function (b) {
                            return l(a, b);
                        });
                    else if (!u(a) && !u(d)) b = l(a, d);
                    else if (u(a) && u(d))
                        for (f = Math.max(a.length, d.length); f--; )
                            b[f] = l(a[f], d[f]);
                    return b;
                }

                function q(a, d, c, e, g) {
                    const b = this.chart,
                        f = b.inverted,
                        k = this.xAxis,
                        h = k.reversed,
                        l = f ? d.height / 2 : d.width / 2;
                    a = (a = a.pointWidth) ? a / 2 : 0;
                    d.startXPos = f ? g.x : h ? -l - a : k.width - l + a;
                    d.startYPos = f
                        ? h
                            ? this.yAxis.height - l + a
                            : -l - a
                        : g.y;
                    e
                        ? "hidden" === d.visibility &&
                          (d.show(),
                          d.attr({ opacity: 0 }).animate({ opacity: 1 }))
                        : d
                              .attr({ opacity: 1 })
                              .animate({ opacity: 0 }, void 0, d.hide);
                    b.hasRendered &&
                        (c && d.attr({ x: d.startXPos, y: d.startYPos }),
                        (d.placed = !0));
                }
                const y = [];
                a.compose = function (a) {
                    I.pushUnique(y, a) &&
                        ((a = a.prototype),
                        (a.initDataLabelsGroup = h),
                        (a.initDataLabels = F),
                        (a.alignDataLabel = e),
                        (a.drawDataLabels = d),
                        (a.justifyDataLabel = k),
                        (a.setDataLabelStartPos = q));
                };
            })(h || (h = {}));
            ("");
            return h;
        }
    );
    M(
        a,
        "Series/Column/ColumnDataLabel.js",
        [
            a["Core/Series/DataLabel.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { series: x } = y,
                { merge: C, pick: z } = I;
            var H;
            (function (y) {
                function u(a, p, t, m, h) {
                    let g = this.chart.inverted;
                    var e = a.series;
                    let l = (e.xAxis ? e.xAxis.len : this.chart.plotSizeX) || 0;
                    e = (e.yAxis ? e.yAxis.len : this.chart.plotSizeY) || 0;
                    var v = a.dlBox || a.shapeArgs;
                    let u = z(
                            a.below,
                            a.plotY > z(this.translatedThreshold, e)
                        ),
                        d = z(t.inside, !!this.options.stacking);
                    v &&
                        ((m = C(v)),
                        0 > m.y && ((m.height += m.y), (m.y = 0)),
                        (v = m.y + m.height - e),
                        0 < v && v < m.height && (m.height -= v),
                        g &&
                            (m = {
                                x: e - m.y - m.height,
                                y: l - m.x - m.width,
                                width: m.height,
                                height: m.width,
                            }),
                        d ||
                            (g
                                ? ((m.x += u ? 0 : m.width), (m.width = 0))
                                : ((m.y += u ? m.height : 0), (m.height = 0))));
                    t.align = z(
                        t.align,
                        !g || d ? "center" : u ? "right" : "left"
                    );
                    t.verticalAlign = z(
                        t.verticalAlign,
                        g || d ? "middle" : u ? "top" : "bottom"
                    );
                    x.prototype.alignDataLabel.call(this, a, p, t, m, h);
                    t.inside &&
                        a.contrastColor &&
                        p.css({ color: a.contrastColor });
                }
                const v = [];
                y.compose = function (l) {
                    a.compose(x);
                    I.pushUnique(v, l) && (l.prototype.alignDataLabel = u);
                };
            })(H || (H = {}));
            return H;
        }
    );
    M(
        a,
        "Series/Bar/BarSeries.js",
        [
            a["Series/Column/ColumnSeries.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { extend: x, merge: C } = I;
            class z extends a {
                constructor() {
                    super(...arguments);
                    this.points = this.options = this.data = void 0;
                }
            }
            z.defaultOptions = C(a.defaultOptions, {});
            x(z.prototype, { inverted: !0 });
            y.registerSeriesType("bar", z);
            ("");
            return z;
        }
    );
    M(a, "Series/Scatter/ScatterSeriesDefaults.js", [], function () {
        "";
        return {
            lineWidth: 0,
            findNearestPointBy: "xy",
            jitter: { x: 0, y: 0 },
            marker: { enabled: !0 },
            tooltip: {
                headerFormat:
                    '<span style="color:{point.color}">\u25cf</span> <span style="font-size: 0.8em"> {series.name}</span><br/>',
                pointFormat: "x: <b>{point.x}</b><br/>y: <b>{point.y}</b><br/>",
            },
        };
    });
    M(
        a,
        "Series/Scatter/ScatterSeries.js",
        [
            a["Series/Scatter/ScatterSeriesDefaults.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { column: x, line: C } = y.seriesTypes,
                { addEvent: z, extend: H, merge: B } = I;
            class u extends C {
                constructor() {
                    super(...arguments);
                    this.points = this.options = this.data = void 0;
                }
                applyJitter() {
                    const a = this,
                        l = this.options.jitter,
                        p = this.points.length;
                    l &&
                        this.points.forEach(function (t, m) {
                            ["x", "y"].forEach(function (h, g) {
                                let e = "plot" + h.toUpperCase(),
                                    w,
                                    v;
                                if (l[h] && !t.isNull) {
                                    var u = a[h + "Axis"];
                                    v = l[h] * u.transA;
                                    u &&
                                        !u.isLog &&
                                        ((w = Math.max(0, t[e] - v)),
                                        (u = Math.min(u.len, t[e] + v)),
                                        (g = 1e4 * Math.sin(m + g * p)),
                                        (g -= Math.floor(g)),
                                        (t[e] = w + (u - w) * g),
                                        "x" === h && (t.clientX = t.plotX));
                                }
                            });
                        });
                }
                drawGraph() {
                    this.options.lineWidth
                        ? super.drawGraph()
                        : this.graph && (this.graph = this.graph.destroy());
                }
            }
            u.defaultOptions = B(C.defaultOptions, a);
            H(u.prototype, {
                drawTracker: x.prototype.drawTracker,
                sorted: !1,
                requireSorting: !1,
                noSharedTooltip: !0,
                trackerGroups: ["group", "markerGroup", "dataLabelsGroup"],
                takeOrdinalPosition: !1,
            });
            z(u, "afterTranslate", function () {
                this.applyJitter();
            });
            y.registerSeriesType("scatter", u);
            return u;
        }
    );
    M(
        a,
        "Series/CenteredUtilities.js",
        [
            a["Core/Globals.js"],
            a["Core/Series/Series.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { deg2rad: x } = a,
                { fireEvent: C, isNumber: z, pick: H, relativeLength: B } = I;
            var u;
            (function (a) {
                a.getCenter = function () {
                    var a = this.options,
                        p = this.chart;
                    const t = 2 * (a.slicedOffset || 0),
                        m = p.plotWidth - 2 * t,
                        h = p.plotHeight - 2 * t;
                    var g = a.center;
                    const e = Math.min(m, h),
                        w = a.thickness;
                    var v = a.size;
                    let u = a.innerSize || 0;
                    "string" === typeof v && (v = parseFloat(v));
                    "string" === typeof u && (u = parseFloat(u));
                    a = [
                        H(g[0], "50%"),
                        H(g[1], "50%"),
                        H(v && 0 > v ? void 0 : a.size, "100%"),
                        H(u && 0 > u ? void 0 : a.innerSize || 0, "0%"),
                    ];
                    !p.angular || this instanceof y || (a[3] = 0);
                    for (g = 0; 4 > g; ++g)
                        (v = a[g]),
                            (p = 2 > g || (2 === g && /%$/.test(v))),
                            (a[g] = B(v, [m, h, e, a[2]][g]) + (p ? t : 0));
                    a[3] > a[2] && (a[3] = a[2]);
                    z(w) && 2 * w < a[2] && 0 < w && (a[3] = a[2] - 2 * w);
                    C(this, "afterGetCenter", { positions: a });
                    return a;
                };
                a.getStartAndEndRadians = function (a, p) {
                    a = z(a) ? a : 0;
                    p = z(p) && p > a && 360 > p - a ? p : a + 360;
                    return { start: x * (a + -90), end: x * (p + -90) };
                };
            })(u || (u = {}));
            ("");
            return u;
        }
    );
    M(
        a,
        "Series/Pie/PiePoint.js",
        [
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Series/Point.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I) {
            const { setAnimation: x } = a,
                {
                    addEvent: C,
                    defined: z,
                    extend: H,
                    isNumber: B,
                    pick: u,
                    relativeLength: v,
                } = I;
            class l extends y {
                constructor() {
                    super(...arguments);
                    this.series = this.options = this.labelDistance = void 0;
                }
                getConnectorPath() {
                    const a = this.labelPosition,
                        l = this.series.options.dataLabels,
                        m = this.connectorShapes;
                    let h = l.connectorShape;
                    m[h] && (h = m[h]);
                    return h.call(
                        this,
                        {
                            x: a.computed.x,
                            y: a.computed.y,
                            alignment: a.alignment,
                        },
                        a.connectorPosition,
                        l
                    );
                }
                getTranslate() {
                    return this.sliced
                        ? this.slicedTranslation
                        : { translateX: 0, translateY: 0 };
                }
                haloPath(a) {
                    const l = this.shapeArgs;
                    return this.sliced || !this.visible
                        ? []
                        : this.series.chart.renderer.symbols.arc(
                              l.x,
                              l.y,
                              l.r + a,
                              l.r + a,
                              {
                                  innerR: l.r - 1,
                                  start: l.start,
                                  end: l.end,
                                  borderRadius: l.borderRadius,
                              }
                          );
                }
                init() {
                    super.init.apply(this, arguments);
                    this.name = u(this.name, "Slice");
                    const a = (a) => {
                        this.slice("select" === a.type);
                    };
                    C(this, "select", a);
                    C(this, "unselect", a);
                    return this;
                }
                isValid() {
                    return B(this.y) && 0 <= this.y;
                }
                setVisible(a, l) {
                    const m = this.series,
                        h = m.chart,
                        g = m.options.ignoreHiddenPoint;
                    l = u(l, g);
                    a !== this.visible &&
                        ((this.visible =
                            this.options.visible =
                            a =
                                "undefined" === typeof a ? !this.visible : a),
                        (m.options.data[m.data.indexOf(this)] = this.options),
                        ["graphic", "dataLabel", "connector"].forEach((e) => {
                            if (this[e]) this[e][a ? "show" : "hide"](a);
                        }),
                        this.legendItem && h.legend.colorizeItem(this, a),
                        a || "hover" !== this.state || this.setState(""),
                        g && (m.isDirty = !0),
                        l && h.redraw());
                }
                slice(a, l, m) {
                    const h = this.series;
                    x(m, h.chart);
                    u(l, !0);
                    this.sliced = this.options.sliced = z(a) ? a : !this.sliced;
                    h.options.data[h.data.indexOf(this)] = this.options;
                    this.graphic && this.graphic.animate(this.getTranslate());
                }
            }
            H(l.prototype, {
                connectorShapes: {
                    fixedOffset: function (a, l, m) {
                        const h = l.breakAt;
                        l = l.touchingSliceAt;
                        return [
                            ["M", a.x, a.y],
                            m.softConnector
                                ? [
                                      "C",
                                      a.x + ("left" === a.alignment ? -5 : 5),
                                      a.y,
                                      2 * h.x - l.x,
                                      2 * h.y - l.y,
                                      h.x,
                                      h.y,
                                  ]
                                : ["L", h.x, h.y],
                            ["L", l.x, l.y],
                        ];
                    },
                    straight: function (a, l) {
                        l = l.touchingSliceAt;
                        return [
                            ["M", a.x, a.y],
                            ["L", l.x, l.y],
                        ];
                    },
                    crookedLine: function (a, l, m) {
                        const { breakAt: h, touchingSliceAt: g } = l;
                        ({ series: l } = this);
                        const [e, p, t] = l.center,
                            u = t / 2,
                            d = l.chart.plotWidth,
                            k = l.chart.plotLeft;
                        l = "left" === a.alignment;
                        const { x: r, y: q } = a;
                        m.crookDistance
                            ? ((a = v(m.crookDistance, 1)),
                              (a = l
                                  ? e + u + (d + k - e - u) * (1 - a)
                                  : k + (e - u) * a))
                            : (a =
                                  e +
                                  (p - q) *
                                      Math.tan(
                                          (this.angle || 0) - Math.PI / 2
                                      ));
                        m = [["M", r, q]];
                        (l ? a <= r && a >= h.x : a >= r && a <= h.x) &&
                            m.push(["L", a, q]);
                        m.push(["L", h.x, h.y], ["L", g.x, g.y]);
                        return m;
                    },
                },
            });
            return l;
        }
    );
    M(a, "Series/Pie/PieSeriesDefaults.js", [], function () {
        "";
        return {
            borderRadius: 3,
            center: [null, null],
            clip: !1,
            colorByPoint: !0,
            dataLabels: {
                allowOverlap: !0,
                connectorPadding: 5,
                connectorShape: "crookedLine",
                crookDistance: void 0,
                distance: 30,
                enabled: !0,
                formatter: function () {
                    return this.point.isNull ? void 0 : this.point.name;
                },
                softConnector: !0,
                x: 0,
            },
            fillColor: void 0,
            ignoreHiddenPoint: !0,
            inactiveOtherPoints: !0,
            legendType: "point",
            marker: null,
            size: null,
            showInLegend: !1,
            slicedOffset: 10,
            stickyTracking: !1,
            tooltip: { followPointer: !0 },
            borderColor: "#ffffff",
            borderWidth: 1,
            lineWidth: void 0,
            states: { hover: { brightness: 0.1 } },
        };
    });
    M(
        a,
        "Series/Pie/PieSeries.js",
        [
            a["Series/CenteredUtilities.js"],
            a["Series/Column/ColumnSeries.js"],
            a["Core/Globals.js"],
            a["Series/Pie/PiePoint.js"],
            a["Series/Pie/PieSeriesDefaults.js"],
            a["Core/Series/Series.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Renderer/SVG/Symbols.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C, z, H, B, u) {
            const { getStartAndEndRadians: v } = a;
            ({ noop: I } = I);
            const {
                clamp: l,
                extend: p,
                fireEvent: t,
                merge: m,
                pick: h,
                relativeLength: g,
            } = u;
            class e extends z {
                constructor() {
                    super(...arguments);
                    this.points =
                        this.options =
                        this.maxLabelDistance =
                        this.data =
                        this.center =
                            void 0;
                }
                animate(a) {
                    const e = this,
                        g = e.points,
                        d = e.startAngleRad;
                    a ||
                        g.forEach(function (a) {
                            const g = a.graphic,
                                k = a.shapeArgs;
                            g &&
                                k &&
                                (g.attr({
                                    r: h(a.startR, e.center && e.center[3] / 2),
                                    start: d,
                                    end: d,
                                }),
                                g.animate(
                                    { r: k.r, start: k.start, end: k.end },
                                    e.options.animation
                                ));
                        });
                }
                drawEmpty() {
                    const a = this.startAngleRad,
                        e = this.endAngleRad,
                        g = this.options;
                    let d, k;
                    0 === this.total && this.center
                        ? ((d = this.center[0]),
                          (k = this.center[1]),
                          this.graph ||
                              (this.graph = this.chart.renderer
                                  .arc(d, k, this.center[1] / 2, 0, a, e)
                                  .addClass("highcharts-empty-series")
                                  .add(this.group)),
                          this.graph.attr({
                              d: B.arc(d, k, this.center[2] / 2, 0, {
                                  start: a,
                                  end: e,
                                  innerR: this.center[3] / 2,
                              }),
                          }),
                          this.chart.styledMode ||
                              this.graph.attr({
                                  "stroke-width": g.borderWidth,
                                  fill: g.fillColor || "none",
                                  stroke: g.color || "#cccccc",
                              }))
                        : this.graph && (this.graph = this.graph.destroy());
                }
                drawPoints() {
                    const a = this.chart.renderer;
                    this.points.forEach(function (e) {
                        e.graphic &&
                            e.hasNewShapeType() &&
                            (e.graphic = e.graphic.destroy());
                        e.graphic ||
                            ((e.graphic = a[e.shapeType](e.shapeArgs).add(
                                e.series.group
                            )),
                            (e.delayedRendering = !0));
                    });
                }
                generatePoints() {
                    super.generatePoints();
                    this.updateTotals();
                }
                getX(a, e, g) {
                    const d = this.center,
                        k = this.radii ? this.radii[g.index] || 0 : d[2] / 2;
                    a = Math.asin(l((a - d[1]) / (k + g.labelDistance), -1, 1));
                    return (
                        d[0] +
                        (e ? -1 : 1) * Math.cos(a) * (k + g.labelDistance) +
                        (0 < g.labelDistance
                            ? (e ? -1 : 1) * this.options.dataLabels.padding
                            : 0)
                    );
                }
                hasData() {
                    return !!this.processedXData.length;
                }
                redrawPoints() {
                    const a = this,
                        e = a.chart;
                    let g, d, k, h;
                    this.drawEmpty();
                    a.group &&
                        !e.styledMode &&
                        a.group.shadow(a.options.shadow);
                    a.points.forEach(function (l) {
                        const q = {};
                        d = l.graphic;
                        !l.isNull && d
                            ? ((h = l.shapeArgs),
                              (g = l.getTranslate()),
                              e.styledMode ||
                                  (k = a.pointAttribs(
                                      l,
                                      l.selected && "select"
                                  )),
                              l.delayedRendering
                                  ? (d
                                        .setRadialReference(a.center)
                                        .attr(h)
                                        .attr(g),
                                    e.styledMode ||
                                        d
                                            .attr(k)
                                            .attr({
                                                "stroke-linejoin": "round",
                                            }),
                                    (l.delayedRendering = !1))
                                  : (d.setRadialReference(a.center),
                                    e.styledMode || m(!0, q, k),
                                    m(!0, q, h, g),
                                    d.animate(q)),
                              d.attr({
                                  visibility: l.visible ? "inherit" : "hidden",
                              }),
                              d.addClass(l.getClassName(), !0))
                            : d && (l.graphic = d.destroy());
                    });
                }
                sortByAngle(a, e) {
                    a.sort(function (a, d) {
                        return (
                            "undefined" !== typeof a.angle &&
                            (d.angle - a.angle) * e
                        );
                    });
                }
                translate(a) {
                    t(this, "translate");
                    this.generatePoints();
                    var e = this.options;
                    const l = e.slicedOffset,
                        d = l + (e.borderWidth || 0);
                    var k = v(e.startAngle, e.endAngle);
                    const m = (this.startAngleRad = k.start);
                    k = (this.endAngleRad = k.end) - m;
                    const q = this.points,
                        p = e.dataLabels.distance;
                    e = e.ignoreHiddenPoint;
                    const b = q.length;
                    let f,
                        c,
                        n,
                        w = 0;
                    a || (this.center = a = this.getCenter());
                    for (c = 0; c < b; c++) {
                        n = q[c];
                        var u = m + w * k;
                        !n.isValid() ||
                            (e && !n.visible) ||
                            (w += n.percentage / 100);
                        var x = m + w * k;
                        var y = {
                            x: a[0],
                            y: a[1],
                            r: a[2] / 2,
                            innerR: a[3] / 2,
                            start: Math.round(1e3 * u) / 1e3,
                            end: Math.round(1e3 * x) / 1e3,
                        };
                        n.shapeType = "arc";
                        n.shapeArgs = y;
                        n.labelDistance = h(
                            n.options.dataLabels &&
                                n.options.dataLabels.distance,
                            p
                        );
                        n.labelDistance = g(n.labelDistance, y.r);
                        this.maxLabelDistance = Math.max(
                            this.maxLabelDistance || 0,
                            n.labelDistance
                        );
                        x = (x + u) / 2;
                        x > 1.5 * Math.PI
                            ? (x -= 2 * Math.PI)
                            : x < -Math.PI / 2 && (x += 2 * Math.PI);
                        n.slicedTranslation = {
                            translateX: Math.round(Math.cos(x) * l),
                            translateY: Math.round(Math.sin(x) * l),
                        };
                        y = (Math.cos(x) * a[2]) / 2;
                        f = (Math.sin(x) * a[2]) / 2;
                        n.tooltipPos = [a[0] + 0.7 * y, a[1] + 0.7 * f];
                        n.half = x < -Math.PI / 2 || x > Math.PI / 2 ? 1 : 0;
                        n.angle = x;
                        u = Math.min(d, n.labelDistance / 5);
                        n.labelPosition = {
                            natural: {
                                x: a[0] + y + Math.cos(x) * n.labelDistance,
                                y: a[1] + f + Math.sin(x) * n.labelDistance,
                            },
                            computed: {},
                            alignment:
                                0 > n.labelDistance
                                    ? "center"
                                    : n.half
                                    ? "right"
                                    : "left",
                            connectorPosition: {
                                breakAt: {
                                    x: a[0] + y + Math.cos(x) * u,
                                    y: a[1] + f + Math.sin(x) * u,
                                },
                                touchingSliceAt: { x: a[0] + y, y: a[1] + f },
                            },
                        };
                    }
                    t(this, "afterTranslate");
                }
                updateTotals() {
                    const a = this.points,
                        e = a.length,
                        g = this.options.ignoreHiddenPoint;
                    let d,
                        k,
                        h = 0;
                    for (d = 0; d < e; d++)
                        (k = a[d]),
                            !k.isValid() || (g && !k.visible) || (h += k.y);
                    this.total = h;
                    for (d = 0; d < e; d++)
                        (k = a[d]),
                            (k.percentage =
                                0 < h && (k.visible || !g)
                                    ? (k.y / h) * 100
                                    : 0),
                            (k.total = h);
                }
            }
            e.defaultOptions = m(z.defaultOptions, C);
            p(e.prototype, {
                axisTypes: [],
                directTouch: !0,
                drawGraph: void 0,
                drawTracker: y.prototype.drawTracker,
                getCenter: a.getCenter,
                getSymbol: I,
                isCartesian: !1,
                noSharedTooltip: !0,
                pointAttribs: y.prototype.pointAttribs,
                pointClass: L,
                requireSorting: !1,
                searchPoint: I,
                trackerGroups: ["group", "dataLabelsGroup"],
            });
            H.registerSeriesType("pie", e);
            return e;
        }
    );
    M(
        a,
        "Series/Pie/PieDataLabel.js",
        [
            a["Core/Series/DataLabel.js"],
            a["Core/Globals.js"],
            a["Core/Renderer/RendererUtilities.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C) {
            const { noop: x } = y,
                { distribute: H } = I,
                { series: B } = L,
                {
                    arrayMax: u,
                    clamp: v,
                    defined: l,
                    merge: p,
                    pick: t,
                    relativeLength: m,
                } = C;
            var h;
            (function (g) {
                function e() {
                    const a = this,
                        d = a.data,
                        e = a.chart,
                        g = a.options.dataLabels || {},
                        b = g.connectorPadding,
                        f = e.plotWidth,
                        c = e.plotHeight,
                        h = e.plotLeft,
                        m = Math.round(e.chartWidth / 3),
                        w = a.center,
                        v = w[2] / 2,
                        x = w[1],
                        y = [[], []],
                        z = [0, 0, 0, 0],
                        F = a.dataLabelPositioners;
                    let C, A, E, I, L, M, J, N, O, S, W, U;
                    a.visible &&
                        (g.enabled || a._hasPointLabels) &&
                        (d.forEach(function (a) {
                            a.dataLabel &&
                                a.visible &&
                                a.dataLabel.shortened &&
                                (a.dataLabel
                                    .attr({ width: "auto" })
                                    .css({
                                        width: "auto",
                                        textOverflow: "clip",
                                    }),
                                (a.dataLabel.shortened = !1));
                        }),
                        B.prototype.drawDataLabels.apply(a),
                        d.forEach(function (a) {
                            a.dataLabel &&
                                (a.visible
                                    ? (y[a.half].push(a),
                                      (a.dataLabel._pos = null),
                                      !l(g.style.width) &&
                                          !l(
                                              a.options.dataLabels &&
                                                  a.options.dataLabels.style &&
                                                  a.options.dataLabels.style
                                                      .width
                                          ) &&
                                          a.dataLabel.getBBox().width > m &&
                                          (a.dataLabel.css({
                                              width: Math.round(0.7 * m) + "px",
                                          }),
                                          (a.dataLabel.shortened = !0)))
                                    : ((a.dataLabel = a.dataLabel.destroy()),
                                      a.dataLabels &&
                                          1 === a.dataLabels.length &&
                                          delete a.dataLabels));
                        }),
                        y.forEach((d, k) => {
                            const m = d.length,
                                n = [];
                            let q,
                                p = 0;
                            if (m) {
                                a.sortByAngle(d, k - 0.5);
                                if (0 < a.maxLabelDistance) {
                                    var r = Math.max(
                                        0,
                                        x - v - a.maxLabelDistance
                                    );
                                    q = Math.min(
                                        x + v + a.maxLabelDistance,
                                        e.plotHeight
                                    );
                                    d.forEach(function (a) {
                                        0 < a.labelDistance &&
                                            a.dataLabel &&
                                            ((a.top = Math.max(
                                                0,
                                                x - v - a.labelDistance
                                            )),
                                            (a.bottom = Math.min(
                                                x + v + a.labelDistance,
                                                e.plotHeight
                                            )),
                                            (p =
                                                a.dataLabel.getBBox().height ||
                                                21),
                                            (a.distributeBox = {
                                                target:
                                                    a.labelPosition.natural.y -
                                                    a.top +
                                                    p / 2,
                                                size: p,
                                                rank: a.y,
                                            }),
                                            n.push(a.distributeBox));
                                    });
                                    r = q + p - r;
                                    H(n, r, r / 5);
                                }
                                for (W = 0; W < m; W++) {
                                    C = d[W];
                                    M = C.labelPosition;
                                    I = C.dataLabel;
                                    S = !1 === C.visible ? "hidden" : "inherit";
                                    O = r = M.natural.y;
                                    n &&
                                        l(C.distributeBox) &&
                                        ("undefined" ===
                                        typeof C.distributeBox.pos
                                            ? (S = "hidden")
                                            : ((J = C.distributeBox.size),
                                              (O = F.radialDistributionY(C))));
                                    delete C.positionIndex;
                                    if (g.justify) N = F.justify(C, v, w);
                                    else
                                        switch (g.alignTo) {
                                            case "connectors":
                                                N = F.alignToConnectors(
                                                    d,
                                                    k,
                                                    f,
                                                    h
                                                );
                                                break;
                                            case "plotEdges":
                                                N = F.alignToPlotEdges(
                                                    I,
                                                    k,
                                                    f,
                                                    h
                                                );
                                                break;
                                            default:
                                                N = F.radialDistributionX(
                                                    a,
                                                    C,
                                                    O,
                                                    r
                                                );
                                        }
                                    I._attr = {
                                        visibility: S,
                                        align: M.alignment,
                                    };
                                    U = C.options.dataLabels || {};
                                    I._pos = {
                                        x:
                                            N +
                                            t(U.x, g.x) +
                                            ({ left: b, right: -b }[
                                                M.alignment
                                            ] || 0),
                                        y:
                                            O +
                                            t(U.y, g.y) -
                                            I.getBBox().height / 2,
                                    };
                                    M &&
                                        ((M.computed.x = N),
                                        (M.computed.y = O));
                                    t(g.crop, !0) &&
                                        ((L = I.getBBox().width),
                                        (r = null),
                                        N - L < b && 1 === k
                                            ? ((r = Math.round(L - N + b)),
                                              (z[3] = Math.max(r, z[3])))
                                            : N + L > f - b &&
                                              0 === k &&
                                              ((r = Math.round(N + L - f + b)),
                                              (z[1] = Math.max(r, z[1]))),
                                        0 > O - J / 2
                                            ? (z[0] = Math.max(
                                                  Math.round(-O + J / 2),
                                                  z[0]
                                              ))
                                            : O + J / 2 > c &&
                                              (z[2] = Math.max(
                                                  Math.round(O + J / 2 - c),
                                                  z[2]
                                              )),
                                        (I.sideOverflow = r));
                                }
                            }
                        }),
                        0 === u(z) || this.verifyDataLabelOverflow(z)) &&
                        (this.placeDataLabels(),
                        this.points.forEach(function (b) {
                            U = p(g, b.options.dataLabels);
                            if ((A = t(U.connectorWidth, 1))) {
                                let c;
                                E = b.connector;
                                if (
                                    (I = b.dataLabel) &&
                                    I._pos &&
                                    b.visible &&
                                    0 < b.labelDistance
                                ) {
                                    S = I._attr.visibility;
                                    if ((c = !E))
                                        (b.connector = E =
                                            e.renderer
                                                .path()
                                                .addClass(
                                                    "highcharts-data-label-connector  highcharts-color-" +
                                                        b.colorIndex +
                                                        (b.className
                                                            ? " " + b.className
                                                            : "")
                                                )
                                                .add(a.dataLabelsGroup)),
                                            e.styledMode ||
                                                E.attr({
                                                    "stroke-width": A,
                                                    stroke:
                                                        U.connectorColor ||
                                                        b.color ||
                                                        "#666666",
                                                });
                                    E[c ? "attr" : "animate"]({
                                        d: b.getConnectorPath(),
                                    });
                                    E.attr("visibility", S);
                                } else E && (b.connector = E.destroy());
                            }
                        }));
                }

                function h() {
                    this.points.forEach(function (a) {
                        let d = a.dataLabel,
                            e;
                        d &&
                            a.visible &&
                            ((e = d._pos)
                                ? (d.sideOverflow &&
                                      ((d._attr.width = Math.max(
                                          d.getBBox().width - d.sideOverflow,
                                          0
                                      )),
                                      d.css({
                                          width: d._attr.width + "px",
                                          textOverflow:
                                              (
                                                  this.options.dataLabels
                                                      .style || {}
                                              ).textOverflow || "ellipsis",
                                      }),
                                      (d.shortened = !0)),
                                  d.attr(d._attr),
                                  d[d.moved ? "animate" : "attr"](e),
                                  (d.moved = !0))
                                : d && d.attr({ y: -9999 }));
                        delete a.distributeBox;
                    }, this);
                }

                function y(a) {
                    let d = this.center,
                        e = this.options,
                        g = e.center,
                        b = e.minSize || 80,
                        f,
                        c = null !== e.size;
                    c ||
                        (null !== g[0]
                            ? (f = Math.max(d[2] - Math.max(a[1], a[3]), b))
                            : ((f = Math.max(d[2] - a[1] - a[3], b)),
                              (d[0] += (a[3] - a[1]) / 2)),
                        null !== g[1]
                            ? (f = v(f, b, d[2] - Math.max(a[0], a[2])))
                            : ((f = v(f, b, d[2] - a[0] - a[2])),
                              (d[1] += (a[0] - a[2]) / 2)),
                        f < d[2]
                            ? ((d[2] = f),
                              (d[3] = Math.min(
                                  e.thickness
                                      ? Math.max(0, f - 2 * e.thickness)
                                      : Math.max(0, m(e.innerSize || 0, f)),
                                  f
                              )),
                              this.translate(d),
                              this.drawDataLabels && this.drawDataLabels())
                            : (c = !0));
                    return c;
                }
                const z = [],
                    d = {
                        radialDistributionY: function (a) {
                            return a.top + a.distributeBox.pos;
                        },
                        radialDistributionX: function (a, d, e, g) {
                            return a.getX(
                                e < d.top + 2 || e > d.bottom - 2 ? g : e,
                                d.half,
                                d
                            );
                        },
                        justify: function (a, d, e) {
                            return (
                                e[0] + (a.half ? -1 : 1) * (d + a.labelDistance)
                            );
                        },
                        alignToPlotEdges: function (a, d, e, g) {
                            a = a.getBBox().width;
                            return d ? a + g : e - a - g;
                        },
                        alignToConnectors: function (a, d, e, g) {
                            let b = 0,
                                f;
                            a.forEach(function (a) {
                                f = a.dataLabel.getBBox().width;
                                f > b && (b = f);
                            });
                            return d ? b + g : e - b - g;
                        },
                    };
                g.compose = function (g) {
                    a.compose(B);
                    C.pushUnique(z, g) &&
                        ((g = g.prototype),
                        (g.dataLabelPositioners = d),
                        (g.alignDataLabel = x),
                        (g.drawDataLabels = e),
                        (g.placeDataLabels = h),
                        (g.verifyDataLabelOverflow = y));
                };
            })(h || (h = {}));
            return h;
        }
    );
    M(
        a,
        "Extensions/OverlappingDataLabels.js",
        [a["Core/Chart/Chart.js"], a["Core/Utilities.js"]],
        function (a, y) {
            function x(a, l) {
                let p,
                    t = !1;
                a &&
                    ((p = a.newOpacity),
                    a.oldOpacity !== p &&
                        (a.alignAttr && a.placed
                            ? (a[p ? "removeClass" : "addClass"](
                                  "highcharts-data-label-hidden"
                              ),
                              (t = !0),
                              (a.alignAttr.opacity = p),
                              a[a.isOld ? "animate" : "attr"](
                                  a.alignAttr,
                                  null,
                                  function () {
                                      l.styledMode ||
                                          a.css({
                                              pointerEvents: p
                                                  ? "auto"
                                                  : "none",
                                          });
                                  }
                              ),
                              C(l, "afterHideOverlappingLabel"))
                            : a.attr({ opacity: p })),
                    (a.isOld = !0));
                return t;
            }
            const {
                addEvent: L,
                fireEvent: C,
                isArray: z,
                isNumber: H,
                objectEach: B,
                pick: u,
            } = y;
            L(a, "render", function () {
                let a = this,
                    l = [];
                (this.labelCollectors || []).forEach(function (a) {
                    l = l.concat(a());
                });
                (this.yAxis || []).forEach(function (a) {
                    a.stacking &&
                        a.options.stackLabels &&
                        !a.options.stackLabels.allowOverlap &&
                        B(a.stacking.stacks, function (a) {
                            B(a, function (a) {
                                a.label && l.push(a.label);
                            });
                        });
                });
                (this.series || []).forEach(function (p) {
                    var t = p.options.dataLabels;
                    p.visible &&
                        (!1 !== t.enabled || p._hasPointLabels) &&
                        ((t = (m) =>
                            m.forEach((h) => {
                                h.visible &&
                                    (z(h.dataLabels)
                                        ? h.dataLabels
                                        : h.dataLabel
                                        ? [h.dataLabel]
                                        : []
                                    ).forEach(function (g) {
                                        const e = g.options;
                                        g.labelrank = u(
                                            e.labelrank,
                                            h.labelrank,
                                            h.shapeArgs && h.shapeArgs.height
                                        );
                                        e.allowOverlap
                                            ? ((g.oldOpacity = g.opacity),
                                              (g.newOpacity = 1),
                                              x(g, a))
                                            : l.push(g);
                                    });
                            })),
                        t(p.nodes || []),
                        t(p.points));
                });
                this.hideOverlappingLabels(l);
            });
            a.prototype.hideOverlappingLabels = function (a) {
                let l = this,
                    p = a.length,
                    t = l.renderer;
                var m;
                let h;
                let g,
                    e,
                    w,
                    u = !1;
                var v = function (a) {
                    let d, e;
                    var g;
                    let h = a.box ? 0 : a.padding || 0,
                        b = (g = 0),
                        f,
                        c;
                    if (a && (!a.alignAttr || a.placed))
                        return (
                            (d = a.alignAttr || {
                                x: a.attr("x"),
                                y: a.attr("y"),
                            }),
                            (e = a.parentGroup),
                            a.width ||
                                ((g = a.getBBox()),
                                (a.width = g.width),
                                (a.height = g.height),
                                (g = t.fontMetrics(a.element).h)),
                            (f = a.width - 2 * h),
                            (c = { left: "0", center: "0.5", right: "1" }[
                                a.alignValue
                            ])
                                ? (b = +c * f)
                                : H(a.x) &&
                                  Math.round(a.x) !== a.translateX &&
                                  (b = a.x - a.translateX),
                            {
                                x: d.x + (e.translateX || 0) + h - (b || 0),
                                y: d.y + (e.translateY || 0) + h - g,
                                width: a.width - 2 * h,
                                height: a.height - 2 * h,
                            }
                        );
                };
                for (h = 0; h < p; h++)
                    if ((m = a[h]))
                        (m.oldOpacity = m.opacity),
                            (m.newOpacity = 1),
                            (m.absoluteBox = v(m));
                a.sort(function (a, e) {
                    return (e.labelrank || 0) - (a.labelrank || 0);
                });
                for (h = 0; h < p; h++)
                    for (e = (v = a[h]) && v.absoluteBox, m = h + 1; m < p; ++m)
                        (w = (g = a[m]) && g.absoluteBox),
                            !e ||
                                !w ||
                                v === g ||
                                0 === v.newOpacity ||
                                0 === g.newOpacity ||
                                "hidden" === v.visibility ||
                                "hidden" === g.visibility ||
                                w.x >= e.x + e.width ||
                                w.x + w.width <= e.x ||
                                w.y >= e.y + e.height ||
                                w.y + w.height <= e.y ||
                                ((v.labelrank < g.labelrank
                                    ? v
                                    : g
                                ).newOpacity = 0);
                a.forEach(function (a) {
                    x(a, l) && (u = !0);
                });
                u && C(l, "afterHideAllOverlappingLabels");
            };
        }
    );
    M(
        a,
        "Extensions/BorderRadius.js",
        [
            a["Core/Defaults.js"],
            a["Core/Series/Series.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Core/Renderer/SVG/SVGElement.js"],
            a["Core/Renderer/SVG/SVGRenderer.js"],
            a["Core/Utilities.js"],
        ],
        function (a, y, I, L, C, z) {
            const { defaultOptions: x } = a;
            ({ seriesTypes: a } = I);
            const {
                    addEvent: B,
                    extend: u,
                    isObject: v,
                    merge: l,
                    relativeLength: p,
                } = z,
                t = {
                    radius: 0,
                    scope: "stack",
                    where: void 0,
                },
                m = (a, g) => {
                    v(a) || (a = { radius: a || 0 });
                    return l(t, g, a);
                };
            if (-1 === L.symbolCustomAttribs.indexOf("borderRadius")) {
                L.symbolCustomAttribs.push(
                    "borderRadius",
                    "brBoxHeight",
                    "brBoxY"
                );
                const h = C.prototype.symbols.arc;
                C.prototype.symbols.arc = function (a, g, l, m, d = {}) {
                    a = h(a, g, l, m, d);
                    const {
                        innerR: e = 0,
                        r = l,
                        start: q = 0,
                        end: t = 0,
                    } = d;
                    if (d.open || !d.borderRadius) return a;
                    l = t - q;
                    g = Math.sin(l / 2);
                    d = Math.max(
                        Math.min(
                            p(d.borderRadius || 0, r - e),
                            (r - e) / 2,
                            (r * g) / (1 + g)
                        ),
                        0
                    );
                    l = Math.min(d, (l / Math.PI) * 2 * e);
                    for (g = a.length - 1; g--; ) {
                        {
                            let e = void 0,
                                h = void 0,
                                k = void 0;
                            m = a;
                            var b = g,
                                f = 1 < g ? l : d,
                                c = m[b],
                                n = m[b + 1];
                            "Z" === n[0] && (n = m[0]);
                            ("M" !== c[0] && "L" !== c[0]) || "A" !== n[0]
                                ? "A" !== c[0] ||
                                  ("M" !== n[0] && "L" !== n[0]) ||
                                  ((k = n), (h = c))
                                : ((k = c), (h = n), (e = !0));
                            if (k && h && h.params) {
                                c = h[1];
                                var w = h[5];
                                n = h.params;
                                const { start: a, end: d, cx: g, cy: l } = n;
                                var u = w ? c - f : c + f;
                                const p = u ? Math.asin(f / u) : 0;
                                w = w ? p : -p;
                                u *= Math.cos(p);
                                e
                                    ? ((n.start = a + w),
                                      (k[1] = g + u * Math.cos(a)),
                                      (k[2] = l + u * Math.sin(a)),
                                      m.splice(b + 1, 0, [
                                          "A",
                                          f,
                                          f,
                                          0,
                                          0,
                                          1,
                                          g + c * Math.cos(n.start),
                                          l + c * Math.sin(n.start),
                                      ]))
                                    : ((n.end = d - w),
                                      (h[6] = g + c * Math.cos(n.end)),
                                      (h[7] = l + c * Math.sin(n.end)),
                                      m.splice(b + 1, 0, [
                                          "A",
                                          f,
                                          f,
                                          0,
                                          0,
                                          1,
                                          g + u * Math.cos(d),
                                          l + u * Math.sin(d),
                                      ]));
                                h[4] =
                                    Math.abs(n.end - n.start) < Math.PI ? 0 : 1;
                            }
                        }
                    }
                    return a;
                };
                const g = C.prototype.symbols.roundedRect;
                C.prototype.symbols.roundedRect = function (
                    a,
                    h,
                    l,
                    m,
                    d = {}
                ) {
                    const e = g(a, h, l, m, d),
                        { r: p = 0, brBoxHeight: q = m, brBoxY: t = h } = d;
                    var b = h - t,
                        f = t + q - (h + m);
                    d = -0.1 < b - p ? 0 : p;
                    const c = -0.1 < f - p ? 0 : p;
                    var n = Math.max(d && b, 0);
                    const u = Math.max(c && f, 0);
                    f = [a + d, h];
                    b = [a + l - d, h];
                    const w = [a + l, h + d],
                        v = [a + l, h + m - c],
                        x = [a + l - c, h + m],
                        y = [a + c, h + m],
                        z = [a, h + m - c],
                        B = [a, h + d];
                    if (n) {
                        const a = Math.sqrt(
                            Math.pow(d, 2) - Math.pow(d - n, 2)
                        );
                        f[0] -= a;
                        b[0] += a;
                        w[1] = B[1] = h + d - n;
                    }
                    m < d - n &&
                        ((n = Math.sqrt(
                            Math.pow(d, 2) - Math.pow(d - n - m, 2)
                        )),
                        (w[0] = v[0] = a + l - d + n),
                        (x[0] = Math.min(w[0], x[0])),
                        (y[0] = Math.max(v[0], y[0])),
                        (z[0] = B[0] = a + d - n),
                        (w[1] = B[1] = h + m));
                    u &&
                        ((n = Math.sqrt(Math.pow(c, 2) - Math.pow(c - u, 2))),
                        (x[0] += n),
                        (y[0] -= n),
                        (v[1] = z[1] = h + m - c + u));
                    m < c - u &&
                        ((m = Math.sqrt(
                            Math.pow(c, 2) - Math.pow(c - u - m, 2)
                        )),
                        (w[0] = v[0] = a + l - c + m),
                        (b[0] = Math.min(w[0], b[0])),
                        (f[0] = Math.max(v[0], f[0])),
                        (z[0] = B[0] = a + c - m),
                        (v[1] = z[1] = h));
                    e.length = 0;
                    e.push(
                        ["M", ...f],
                        ["L", ...b],
                        ["A", d, d, 0, 0, 1, ...w],
                        ["L", ...v],
                        ["A", c, c, 0, 0, 1, ...x],
                        ["L", ...y],
                        ["A", c, c, 0, 0, 1, ...z],
                        ["L", ...B],
                        ["A", d, d, 0, 0, 1, ...f],
                        ["Z"]
                    );
                    return e;
                };
                B(a.pie, "afterTranslate", function () {
                    const a = m(this.options.borderRadius);
                    for (const e of this.points) {
                        const g = e.shapeArgs;
                        g &&
                            (g.borderRadius = p(
                                a.radius,
                                (g.r || 0) - (g.innerR || 0)
                            ));
                    }
                });
                B(
                    y,
                    "afterColumnTranslate",
                    function () {
                        var a, g;
                        if (
                            this.options.borderRadius &&
                            (!this.chart.is3d || !this.chart.is3d())
                        ) {
                            const { options: e, yAxis: r } = this,
                                q = "percent" === e.stacking;
                            var h =
                                null ===
                                    (g =
                                        null === (a = x.plotOptions) ||
                                        void 0 === a
                                            ? void 0
                                            : a[this.type]) || void 0 === g
                                    ? void 0
                                    : g.borderRadius;
                            a = m(e.borderRadius, v(h) ? h : {});
                            g = r.options.reversed;
                            for (const k of this.points)
                                if (
                                    (({ shapeArgs: h } = k),
                                    "roundedRect" === k.shapeType && h)
                                ) {
                                    const {
                                        width: b = 0,
                                        height: f = 0,
                                        y: c = 0,
                                    } = h;
                                    var l = c,
                                        d = f;
                                    "stack" === a.scope &&
                                        k.stackTotal &&
                                        ((l = r.translate(
                                            q ? 100 : k.stackTotal,
                                            !1,
                                            !0,
                                            !1,
                                            !0
                                        )),
                                        (d = r.translate(
                                            e.threshold || 0,
                                            !1,
                                            !0,
                                            !1,
                                            !0
                                        )),
                                        (d = this.crispCol(
                                            0,
                                            Math.min(l, d),
                                            0,
                                            Math.abs(l - d)
                                        )),
                                        (l = d.y),
                                        (d = d.height));
                                    const m =
                                        -1 ===
                                        (k.negative ? -1 : 1) * (g ? -1 : 1);
                                    let t = a.where;
                                    !t &&
                                        this.is("waterfall") &&
                                        Math.abs(
                                            (k.yBottom || 0) -
                                                (this.translatedThreshold || 0)
                                        ) > this.borderWidth &&
                                        (t = "all");
                                    t || (t = "end");
                                    const v =
                                        Math.min(
                                            p(a.radius, b),
                                            b / 2,
                                            "all" === t ? f / 2 : Infinity
                                        ) || 0;
                                    "end" === t && (m && (l -= v), (d += v));
                                    u(h, { brBoxHeight: d, brBoxY: l, r: v });
                                }
                        }
                    },
                    { order: 9 }
                );
            }
            y = { optionsToObject: m };
            ("");
            return y;
        }
    );
    M(a, "Core/Responsive.js", [a["Core/Utilities.js"]], function (a) {
        const {
            diffObjects: x,
            extend: I,
            find: L,
            merge: C,
            pick: z,
            uniqueKey: H,
        } = a;
        var B;
        (function (u) {
            function v(a, l) {
                const h = a.condition;
                (
                    h.callback ||
                    function () {
                        return (
                            this.chartWidth <=
                                z(h.maxWidth, Number.MAX_VALUE) &&
                            this.chartHeight <=
                                z(h.maxHeight, Number.MAX_VALUE) &&
                            this.chartWidth >= z(h.minWidth, 0) &&
                            this.chartHeight >= z(h.minHeight, 0)
                        );
                    }
                ).call(this) && l.push(a._id);
            }

            function l(a, l) {
                const h = this.options.responsive;
                var g = this.currentResponsive;
                let e = [];
                !l &&
                    h &&
                    h.rules &&
                    h.rules.forEach((a) => {
                        "undefined" === typeof a._id && (a._id = H());
                        this.matchResponsiveRule(a, e);
                    }, this);
                l = C(
                    ...e
                        .map((a) =>
                            L((h || {}).rules || [], (e) => e._id === a)
                        )
                        .map((a) => a && a.chartOptions)
                );
                l.isResponsiveOptions = !0;
                e = e.toString() || void 0;
                e !== (g && g.ruleIds) &&
                    (g && this.update(g.undoOptions, a, !0),
                    e
                        ? ((g = x(
                              l,
                              this.options,
                              !0,
                              this.collectionsWithUpdate
                          )),
                          (g.isResponsiveOptions = !0),
                          (this.currentResponsive = {
                              ruleIds: e,
                              mergedOptions: l,
                              undoOptions: g,
                          }),
                          this.update(l, a, !0))
                        : (this.currentResponsive = void 0));
            }
            const p = [];
            u.compose = function (t) {
                a.pushUnique(p, t) &&
                    I(t.prototype, {
                        matchResponsiveRule: v,
                        setResponsive: l,
                    });
                return t;
            };
        })(B || (B = {}));
        ("");
        ("");
        return B;
    });
    M(
        a,
        "masters/highcharts.src.js",
        [
            a["Core/Globals.js"],
            a["Core/Utilities.js"],
            a["Core/Defaults.js"],
            a["Core/Animation/Fx.js"],
            a["Core/Animation/AnimationUtilities.js"],
            a["Core/Renderer/HTML/AST.js"],
            a["Core/Templating.js"],
            a["Core/Renderer/RendererUtilities.js"],
            a["Core/Renderer/SVG/SVGElement.js"],
            a["Core/Renderer/SVG/SVGRenderer.js"],
            a["Core/Renderer/HTML/HTMLElement.js"],
            a["Core/Renderer/HTML/HTMLRenderer.js"],
            a["Core/Axis/Axis.js"],
            a["Core/Axis/DateTimeAxis.js"],
            a["Core/Axis/LogarithmicAxis.js"],
            a["Core/Axis/PlotLineOrBand/PlotLineOrBand.js"],
            a["Core/Axis/Tick.js"],
            a["Core/Tooltip.js"],
            a["Core/Series/Point.js"],
            a["Core/Pointer.js"],
            a["Core/Legend/Legend.js"],
            a["Core/Chart/Chart.js"],
            a["Core/Axis/Stacking/StackingAxis.js"],
            a["Core/Axis/Stacking/StackItem.js"],
            a["Core/Series/Series.js"],
            a["Core/Series/SeriesRegistry.js"],
            a["Series/Column/ColumnSeries.js"],
            a["Series/Column/ColumnDataLabel.js"],
            a["Series/Pie/PieSeries.js"],
            a["Series/Pie/PieDataLabel.js"],
            a["Core/Series/DataLabel.js"],
            a["Core/Responsive.js"],
            a["Core/Color/Color.js"],
            a["Core/Time.js"],
        ],
        function (
            a,
            y,
            I,
            L,
            C,
            z,
            H,
            B,
            u,
            v,
            l,
            p,
            t,
            m,
            h,
            g,
            e,
            w,
            E,
            F,
            d,
            k,
            r,
            q,
            G,
            b,
            f,
            c,
            n,
            M,
            D,
            K,
            U,
            T
        ) {
            a.animate = C.animate;
            a.animObject = C.animObject;
            a.getDeferredAnimation = C.getDeferredAnimation;
            a.setAnimation = C.setAnimation;
            a.stop = C.stop;
            a.timers = L.timers;
            a.AST = z;
            a.Axis = t;
            a.Chart = k;
            a.chart = k.chart;
            a.Fx = L;
            a.Legend = d;
            a.PlotLineOrBand = g;
            a.Point = E;
            a.Pointer = F;
            a.Series = G;
            a.StackItem = q;
            a.SVGElement = u;
            a.SVGRenderer = v;
            a.Templating = H;
            a.Tick = e;
            a.Time = T;
            a.Tooltip = w;
            a.Color = U;
            a.color = U.parse;
            p.compose(v);
            l.compose(u);
            F.compose(k);
            d.compose(k);
            a.defaultOptions = I.defaultOptions;
            a.getOptions = I.getOptions;
            a.time = I.defaultTime;
            a.setOptions = I.setOptions;
            a.dateFormat = H.dateFormat;
            a.format = H.format;
            a.numberFormat = H.numberFormat;
            a.addEvent = y.addEvent;
            a.arrayMax = y.arrayMax;
            a.arrayMin = y.arrayMin;
            a.attr = y.attr;
            a.clearTimeout = y.clearTimeout;
            a.correctFloat = y.correctFloat;
            a.createElement = y.createElement;
            a.css = y.css;
            a.defined = y.defined;
            a.destroyObjectProperties = y.destroyObjectProperties;
            a.discardElement = y.discardElement;
            a.distribute = B.distribute;
            a.erase = y.erase;
            a.error = y.error;
            a.extend = y.extend;
            a.extendClass = y.extendClass;
            a.find = y.find;
            a.fireEvent = y.fireEvent;
            a.getMagnitude = y.getMagnitude;
            a.getStyle = y.getStyle;
            a.inArray = y.inArray;
            a.isArray = y.isArray;
            a.isClass = y.isClass;
            a.isDOMElement = y.isDOMElement;
            a.isFunction = y.isFunction;
            a.isNumber = y.isNumber;
            a.isObject = y.isObject;
            a.isString = y.isString;
            a.keys = y.keys;
            a.merge = y.merge;
            a.normalizeTickInterval = y.normalizeTickInterval;
            a.objectEach = y.objectEach;
            a.offset = y.offset;
            a.pad = y.pad;
            a.pick = y.pick;
            a.pInt = y.pInt;
            a.relativeLength = y.relativeLength;
            a.removeEvent = y.removeEvent;
            a.seriesType = b.seriesType;
            a.splat = y.splat;
            a.stableSort = y.stableSort;
            a.syncTimeout = y.syncTimeout;
            a.timeUnits = y.timeUnits;
            a.uniqueKey = y.uniqueKey;
            a.useSerialIds = y.useSerialIds;
            a.wrap = y.wrap;
            c.compose(f);
            D.compose(G);
            m.compose(t);
            h.compose(t);
            M.compose(n);
            g.compose(t);
            K.compose(k);
            r.compose(t, k, G);
            w.compose(F);
            return a;
        }
    );
    a["masters/highcharts.src.js"]._modules = a;
    return a["masters/highcharts.src.js"];
});
//# sourceMappingURL=highcharts.js.map
