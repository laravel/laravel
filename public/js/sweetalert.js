/*!
* sweetalert2 v11.10.5
* Released under the MIT License.
*/
!function(t, e) {
    "object" == typeof exports && "undefined" != typeof module ? module.exports = e() : "function" == typeof define && define.amd ? define(e) : (t = "undefined" != typeof globalThis ? globalThis : t || self).Sweetalert2 = e()
}(this, (function() {
    "use strict";
    function t(t, n, o) {
        return n = c(n),
        function(t, e) {
            if (e && ("object" == typeof e || "function" == typeof e))
                return e;
            if (void 0 !== e)
                throw new TypeError("Derived constructors may only return object or undefined");
            return function(t) {
                if (void 0 === t)
                    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
                return t
            }(t)
        }(t, e() ? Reflect.construct(n, o || [], c(t).constructor) : n.apply(t, o))
    }
    function e() {
        try {
            var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function() {}
            )))
        } catch (t) {}
        return (e = function() {
            return !!t
        }
        )()
    }
    function n(t) {
        var e = function(t, e) {
            if ("object" != typeof t || !t)
                return t;
            var n = t[Symbol.toPrimitive];
            if (void 0 !== n) {
                var o = n.call(t, e || "default");
                if ("object" != typeof o)
                    return o;
                throw new TypeError("@@toPrimitive must return a primitive value.")
            }
            return ("string" === e ? String : Number)(t)
        }(t, "string");
        return "symbol" == typeof e ? e : String(e)
    }
    function o(t) {
        return o = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(t) {
            return typeof t
        }
        : function(t) {
            return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t
        }
        ,
        o(t)
    }
    function i(t, e) {
        if (!(t instanceof e))
            throw new TypeError("Cannot call a class as a function")
    }
    function r(t, e) {
        for (var o = 0; o < e.length; o++) {
            var i = e[o];
            i.enumerable = i.enumerable || !1,
            i.configurable = !0,
            "value"in i && (i.writable = !0),
            Object.defineProperty(t, n(i.key), i)
        }
    }
    function a(t, e, n) {
        return e && r(t.prototype, e),
        n && r(t, n),
        Object.defineProperty(t, "prototype", {
            writable: !1
        }),
        t
    }
    function c(t) {
        return c = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function(t) {
            return t.__proto__ || Object.getPrototypeOf(t)
        }
        ,
        c(t)
    }
    function u(t, e) {
        return u = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function(t, e) {
            return t.__proto__ = e,
            t
        }
        ,
        u(t, e)
    }
    function s() {
        return s = "undefined" != typeof Reflect && Reflect.get ? Reflect.get.bind() : function(t, e, n) {
            var o = function(t, e) {
                for (; !Object.prototype.hasOwnProperty.call(t, e) && null !== (t = c(t)); )
                    ;
                return t
            }(t, e);
            if (o) {
                var i = Object.getOwnPropertyDescriptor(o, e);
                return i.get ? i.get.call(arguments.length < 3 ? t : n) : i.value
            }
        }
        ,
        s.apply(this, arguments)
    }
    function l(t, e) {
        return function(t) {
            if (Array.isArray(t))
                return t
        }(t) || function(t, e) {
            var n = null == t ? null : "undefined" != typeof Symbol && t[Symbol.iterator] || t["@@iterator"];
            if (null != n) {
                var o, i, r, a, c = [], u = !0, s = !1;
                try {
                    if (r = (n = n.call(t)).next,
                    0 === e) {
                        if (Object(n) !== n)
                            return;
                        u = !1
                    } else
                        for (; !(u = (o = r.call(n)).done) && (c.push(o.value),
                        c.length !== e); u = !0)
                            ;
                } catch (t) {
                    s = !0,
                    i = t
                } finally {
                    try {
                        if (!u && null != n.return && (a = n.return(),
                        Object(a) !== a))
                            return
                    } finally {
                        if (s)
                            throw i
                    }
                }
                return c
            }
        }(t, e) || f(t, e) || function() {
            throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")
        }()
    }
    function d(t) {
        return function(t) {
            if (Array.isArray(t))
                return p(t)
        }(t) || function(t) {
            if ("undefined" != typeof Symbol && null != t[Symbol.iterator] || null != t["@@iterator"])
                return Array.from(t)
        }(t) || f(t) || function() {
            throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")
        }()
    }
    function f(t, e) {
        if (t) {
            if ("string" == typeof t)
                return p(t, e);
            var n = Object.prototype.toString.call(t).slice(8, -1);
            return "Object" === n && t.constructor && (n = t.constructor.name),
            "Map" === n || "Set" === n ? Array.from(t) : "Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n) ? p(t, e) : void 0
        }
    }
    function p(t, e) {
        (null == e || e > t.length) && (e = t.length);
        for (var n = 0, o = new Array(e); n < e; n++)
            o[n] = t[n];
        return o
    }
    function m(t, e) {
        return function(t, e) {
            if (e.get)
                return e.get.call(t);
            return e.value
        }(t, v(t, e, "get"))
    }
    function h(t, e, n) {
        return function(t, e, n) {
            if (e.set)
                e.set.call(t, n);
            else {
                if (!e.writable)
                    throw new TypeError("attempted to set read only private field");
                e.value = n
            }
        }(t, v(t, e, "set"), n),
        n
    }
    function v(t, e, n) {
        if (!e.has(t))
            throw new TypeError("attempted to " + n + " private field on non-instance");
        return e.get(t)
    }
    function g(t, e, n) {
        !function(t, e) {
            if (e.has(t))
                throw new TypeError("Cannot initialize the same private elements twice on an object")
        }(t, e),
        e.set(t, n)
    }
    var b = {}
      , y = function(t) {
        return new Promise((function(e) {
            if (!t)
                return e();
            var n = window.scrollX
              , o = window.scrollY;
            b.restoreFocusTimeout = setTimeout((function() {
                b.previousActiveElement instanceof HTMLElement ? (b.previousActiveElement.focus(),
                b.previousActiveElement = null) : document.body && document.body.focus(),
                e()
            }
            ), 100),
            window.scrollTo(n, o)
        }
        ))
    }
      , w = "swal2-"
      , C = ["container", "shown", "height-auto", "iosfix", "popup", "modal", "no-backdrop", "no-transition", "toast", "toast-shown", "show", "hide", "close", "title", "html-container", "actions", "confirm", "deny", "cancel", "default-outline", "footer", "icon", "icon-content", "image", "input", "file", "range", "select", "radio", "checkbox", "label", "textarea", "inputerror", "input-label", "validation-message", "progress-steps", "active-progress-step", "progress-step", "progress-step-line", "loader", "loading", "styled", "top", "top-start", "top-end", "top-left", "top-right", "center", "center-start", "center-end", "center-left", "center-right", "bottom", "bottom-start", "bottom-end", "bottom-left", "bottom-right", "grow-row", "grow-column", "grow-fullscreen", "rtl", "timer-progress-bar", "timer-progress-bar-container", "scrollbar-measure", "icon-success", "icon-warning", "icon-info", "icon-question", "icon-error"].reduce((function(t, e) {
        return t[e] = w + e,
        t
    }
    ), {})
      , A = ["success", "warning", "info", "question", "error"].reduce((function(t, e) {
        return t[e] = w + e,
        t
    }
    ), {})
      , k = "SweetAlert2:"
      , E = function(t) {
        return t.charAt(0).toUpperCase() + t.slice(1)
    }
      , B = function(t) {
        console.warn("".concat(k, " ").concat("object" === o(t) ? t.join(" ") : t))
    }
      , P = function(t) {
        console.error("".concat(k, " ").concat(t))
    }
      , T = []
      , x = function(t, e) {
        var n;
        n = '"'.concat(t, '" is deprecated and will be removed in the next major release. Please use "').concat(e, '" instead.'),
        T.includes(n) || (T.push(n),
        B(n))
    }
      , S = function(t) {
        return "function" == typeof t ? t() : t
    }
      , O = function(t) {
        return t && "function" == typeof t.toPromise
    }
      , L = function(t) {
        return O(t) ? t.toPromise() : Promise.resolve(t)
    }
      , j = function(t) {
        return t && Promise.resolve(t) === t
    }
      , M = function() {
        return document.body.querySelector(".".concat(C.container))
    }
      , I = function(t) {
        var e = M();
        return e ? e.querySelector(t) : null
    }
      , H = function(t) {
        return I(".".concat(t))
    }
      , D = function() {
        return H(C.popup)
    }
      , q = function() {
        return H(C.icon)
    }
      , V = function() {
        return H(C.title)
    }
      , _ = function() {
        return H(C["html-container"])
    }
      , R = function() {
        return H(C.image)
    }
      , N = function() {
        return H(C["progress-steps"])
    }
      , F = function() {
        return H(C["validation-message"])
    }
      , U = function() {
        return I(".".concat(C.actions, " .").concat(C.confirm))
    }
      , z = function() {
        return I(".".concat(C.actions, " .").concat(C.cancel))
    }
      , W = function() {
        return I(".".concat(C.actions, " .").concat(C.deny))
    }
      , K = function() {
        return I(".".concat(C.loader))
    }
      , Y = function() {
        return H(C.actions)
    }
      , Z = function() {
        return H(C.footer)
    }
      , $ = function() {
        return H(C["timer-progress-bar"])
    }
      , J = function() {
        return H(C.close)
    }
      , X = function() {
        var t = D();
        if (!t)
            return [];
        var e = t.querySelectorAll('[tabindex]:not([tabindex="-1"]):not([tabindex="0"])')
          , n = Array.from(e).sort((function(t, e) {
            var n = parseInt(t.getAttribute("tabindex") || "0")
              , o = parseInt(e.getAttribute("tabindex") || "0");
            return n > o ? 1 : n < o ? -1 : 0
        }
        ))
          , o = t.querySelectorAll('\n  a[href],\n  area[href],\n  input:not([disabled]),\n  select:not([disabled]),\n  textarea:not([disabled]),\n  button:not([disabled]),\n  iframe,\n  object,\n  embed,\n  [tabindex="0"],\n  [contenteditable],\n  audio[controls],\n  video[controls],\n  summary\n')
          , i = Array.from(o).filter((function(t) {
            return "-1" !== t.getAttribute("tabindex")
        }
        ));
        return d(new Set(n.concat(i))).filter((function(t) {
            return ht(t)
        }
        ))
    }
      , G = function() {
        return et(document.body, C.shown) && !et(document.body, C["toast-shown"]) && !et(document.body, C["no-backdrop"])
    }
      , Q = function() {
        var t = D();
        return !!t && et(t, C.toast)
    }
      , tt = function(t, e) {
        if (t.textContent = "",
        e) {
            var n = (new DOMParser).parseFromString(e, "text/html")
              , o = n.querySelector("head");
            o && Array.from(o.childNodes).forEach((function(e) {
                t.appendChild(e)
            }
            ));
            var i = n.querySelector("body");
            i && Array.from(i.childNodes).forEach((function(e) {
                e instanceof HTMLVideoElement || e instanceof HTMLAudioElement ? t.appendChild(e.cloneNode(!0)) : t.appendChild(e)
            }
            ))
        }
    }
      , et = function(t, e) {
        if (!e)
            return !1;
        for (var n = e.split(/\s+/), o = 0; o < n.length; o++)
            if (!t.classList.contains(n[o]))
                return !1;
        return !0
    }
      , nt = function(t, e, n) {
        if (function(t, e) {
            Array.from(t.classList).forEach((function(n) {
                Object.values(C).includes(n) || Object.values(A).includes(n) || Object.values(e.showClass || {}).includes(n) || t.classList.remove(n)
            }
            ))
        }(t, e),
        e.customClass && e.customClass[n]) {
            if ("string" != typeof e.customClass[n] && !e.customClass[n].forEach)
                return void B("Invalid type of customClass.".concat(n, '! Expected string or iterable object, got "').concat(o(e.customClass[n]), '"'));
            at(t, e.customClass[n])
        }
    }
      , ot = function(t, e) {
        if (!e)
            return null;
        switch (e) {
        case "select":
        case "textarea":
        case "file":
            return t.querySelector(".".concat(C.popup, " > .").concat(C[e]));
        case "checkbox":
            return t.querySelector(".".concat(C.popup, " > .").concat(C.checkbox, " input"));
        case "radio":
            return t.querySelector(".".concat(C.popup, " > .").concat(C.radio, " input:checked")) || t.querySelector(".".concat(C.popup, " > .").concat(C.radio, " input:first-child"));
        case "range":
            return t.querySelector(".".concat(C.popup, " > .").concat(C.range, " input"));
        default:
            return t.querySelector(".".concat(C.popup, " > .").concat(C.input))
        }
    }
      , it = function(t) {
        if (t.focus(),
        "file" !== t.type) {
            var e = t.value;
            t.value = "",
            t.value = e
        }
    }
      , rt = function(t, e, n) {
        t && e && ("string" == typeof e && (e = e.split(/\s+/).filter(Boolean)),
        e.forEach((function(e) {
            Array.isArray(t) ? t.forEach((function(t) {
                n ? t.classList.add(e) : t.classList.remove(e)
            }
            )) : n ? t.classList.add(e) : t.classList.remove(e)
        }
        )))
    }
      , at = function(t, e) {
        rt(t, e, !0)
    }
      , ct = function(t, e) {
        rt(t, e, !1)
    }
      , ut = function(t, e) {
        for (var n = Array.from(t.children), o = 0; o < n.length; o++) {
            var i = n[o];
            if (i instanceof HTMLElement && et(i, e))
                return i
        }
    }
      , st = function(t, e, n) {
        n === "".concat(parseInt(n)) && (n = parseInt(n)),
        n || 0 === parseInt(n) ? t.style.setProperty(e, "number" == typeof n ? "".concat(n, "px") : n) : t.style.removeProperty(e)
    }
      , lt = function(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : "flex";
        t && (t.style.display = e)
    }
      , dt = function(t) {
        t && (t.style.display = "none")
    }
      , ft = function(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : "block";
        t && new MutationObserver((function() {
            mt(t, t.innerHTML, e)
        }
        )).observe(t, {
            childList: !0,
            subtree: !0
        })
    }
      , pt = function(t, e, n, o) {
        var i = t.querySelector(e);
        i && i.style.setProperty(n, o)
    }
      , mt = function(t, e) {
        e ? lt(t, arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : "flex") : dt(t)
    }
      , ht = function(t) {
        return !(!t || !(t.offsetWidth || t.offsetHeight || t.getClientRects().length))
    }
      , vt = function(t) {
        return !!(t.scrollHeight > t.clientHeight)
    }
      , gt = function(t) {
        var e = window.getComputedStyle(t)
          , n = parseFloat(e.getPropertyValue("animation-duration") || "0")
          , o = parseFloat(e.getPropertyValue("transition-duration") || "0");
        return n > 0 || o > 0
    }
      , bt = function(t) {
        var e = arguments.length > 1 && void 0 !== arguments[1] && arguments[1]
          , n = $();
        n && ht(n) && (e && (n.style.transition = "none",
        n.style.width = "100%"),
        setTimeout((function() {
            n.style.transition = "width ".concat(t / 1e3, "s linear"),
            n.style.width = "0%"
        }
        ), 10))
    }
      , yt = function() {
        return "undefined" == typeof window || "undefined" == typeof document
    }
      , wt = '\n <div aria-labelledby="'.concat(C.title, '" aria-describedby="').concat(C["html-container"], '" class="').concat(C.popup, '" tabindex="-1">\n   <button type="button" class="').concat(C.close, '"></button>\n   <ul class="').concat(C["progress-steps"], '"></ul>\n   <div class="').concat(C.icon, '"></div>\n   <img class="').concat(C.image, '" />\n   <h2 class="').concat(C.title, '" id="').concat(C.title, '"></h2>\n   <div class="').concat(C["html-container"], '" id="').concat(C["html-container"], '"></div>\n   <input class="').concat(C.input, '" id="').concat(C.input, '" />\n   <input type="file" class="').concat(C.file, '" />\n   <div class="').concat(C.range, '">\n     <input type="range" />\n     <output></output>\n   </div>\n   <select class="').concat(C.select, '" id="').concat(C.select, '"></select>\n   <div class="').concat(C.radio, '"></div>\n   <label class="').concat(C.checkbox, '">\n     <input type="checkbox" id="').concat(C.checkbox, '" />\n     <span class="').concat(C.label, '"></span>\n   </label>\n   <textarea class="').concat(C.textarea, '" id="').concat(C.textarea, '"></textarea>\n   <div class="').concat(C["validation-message"], '" id="').concat(C["validation-message"], '"></div>\n   <div class="').concat(C.actions, '">\n     <div class="').concat(C.loader, '"></div>\n     <button type="button" class="').concat(C.confirm, '"></button>\n     <button type="button" class="').concat(C.deny, '"></button>\n     <button type="button" class="').concat(C.cancel, '"></button>\n   </div>\n   <div class="').concat(C.footer, '"></div>\n   <div class="').concat(C["timer-progress-bar-container"], '">\n     <div class="').concat(C["timer-progress-bar"], '"></div>\n   </div>\n </div>\n').replace(/(^|\n)\s*/g, "")
      , Ct = function() {
        b.currentInstance.resetValidationMessage()
    }
      , At = function(t) {
        var e, n = !!(e = M()) && (e.remove(),
        ct([document.documentElement, document.body], [C["no-backdrop"], C["toast-shown"], C["has-column"]]),
        !0);
        if (yt())
            P("SweetAlert2 requires document to initialize");
        else {
            var o = document.createElement("div");
            o.className = C.container,
            n && at(o, C["no-transition"]),
            tt(o, wt);
            var i, r, a, c, u, s, l, d, f, p = "string" == typeof (i = t.target) ? document.querySelector(i) : i;
            p.appendChild(o),
            function(t) {
                var e = D();
                e.setAttribute("role", t.toast ? "alert" : "dialog"),
                e.setAttribute("aria-live", t.toast ? "polite" : "assertive"),
                t.toast || e.setAttribute("aria-modal", "true")
            }(t),
            function(t) {
                "rtl" === window.getComputedStyle(t).direction && at(M(), C.rtl)
            }(p),
            r = D(),
            a = ut(r, C.input),
            c = ut(r, C.file),
            u = r.querySelector(".".concat(C.range, " input")),
            s = r.querySelector(".".concat(C.range, " output")),
            l = ut(r, C.select),
            d = r.querySelector(".".concat(C.checkbox, " input")),
            f = ut(r, C.textarea),
            a.oninput = Ct,
            c.onchange = Ct,
            l.onchange = Ct,
            d.onchange = Ct,
            f.oninput = Ct,
            u.oninput = function() {
                Ct(),
                s.value = u.value
            }
            ,
            u.onchange = function() {
                Ct(),
                s.value = u.value
            }
        }
    }
      , kt = function(t, e) {
        t instanceof HTMLElement ? e.appendChild(t) : "object" === o(t) ? Et(t, e) : t && tt(e, t)
    }
      , Et = function(t, e) {
        t.jquery ? Bt(e, t) : tt(e, t.toString())
    }
      , Bt = function(t, e) {
        if (t.textContent = "",
        0 in e)
            for (var n = 0; n in e; n++)
                t.appendChild(e[n].cloneNode(!0));
        else
            t.appendChild(e.cloneNode(!0))
    }
      , Pt = function() {
        if (yt())
            return !1;
        var t = document.createElement("div");
        return void 0 !== t.style.webkitAnimation ? "webkitAnimationEnd" : void 0 !== t.style.animation && "animationend"
    }()
      , Tt = function(t, e) {
        var n = Y()
          , o = K();
        n && o && (e.showConfirmButton || e.showDenyButton || e.showCancelButton ? lt(n) : dt(n),
        nt(n, e, "actions"),
        function(t, e, n) {
            var o = U()
              , i = W()
              , r = z();
            if (!o || !i || !r)
                return;
            xt(o, "confirm", n),
            xt(i, "deny", n),
            xt(r, "cancel", n),
            function(t, e, n, o) {
                if (!o.buttonsStyling)
                    return void ct([t, e, n], C.styled);
                at([t, e, n], C.styled),
                o.confirmButtonColor && (t.style.backgroundColor = o.confirmButtonColor,
                at(t, C["default-outline"]));
                o.denyButtonColor && (e.style.backgroundColor = o.denyButtonColor,
                at(e, C["default-outline"]));
                o.cancelButtonColor && (n.style.backgroundColor = o.cancelButtonColor,
                at(n, C["default-outline"]))
            }(o, i, r, n),
            n.reverseButtons && (n.toast ? (t.insertBefore(r, o),
            t.insertBefore(i, o)) : (t.insertBefore(r, e),
            t.insertBefore(i, e),
            t.insertBefore(o, e)))
        }(n, o, e),
        tt(o, e.loaderHtml || ""),
        nt(o, e, "loader"))
    };
    function xt(t, e, n) {
        var o = E(e);
        mt(t, n["show".concat(o, "Button")], "inline-block"),
        tt(t, n["".concat(e, "ButtonText")] || ""),
        t.setAttribute("aria-label", n["".concat(e, "ButtonAriaLabel")] || ""),
        t.className = C[e],
        nt(t, n, "".concat(e, "Button"))
    }
    var St = function(t, e) {
        var n = M();
        n && (!function(t, e) {
            "string" == typeof e ? t.style.background = e : e || at([document.documentElement, document.body], C["no-backdrop"])
        }(n, e.backdrop),
        function(t, e) {
            if (!e)
                return;
            e in C ? at(t, C[e]) : (B('The "position" parameter is not valid, defaulting to "center"'),
            at(t, C.center))
        }(n, e.position),
        function(t, e) {
            if (!e)
                return;
            at(t, C["grow-".concat(e)])
        }(n, e.grow),
        nt(n, e, "container"))
    };
    var Ot = {
        innerParams: new WeakMap,
        domCache: new WeakMap
    }
      , Lt = ["input", "file", "range", "select", "radio", "checkbox", "textarea"]
      , jt = function(t) {
        if (t.input)
            if (_t[t.input]) {
                var e = qt(t.input)
                  , n = _t[t.input](e, t);
                lt(e),
                t.inputAutoFocus && setTimeout((function() {
                    it(n)
                }
                ))
            } else
                P("Unexpected type of input! Expected ".concat(Object.keys(_t).join(" | "), ', got "').concat(t.input, '"'))
    }
      , Mt = function(t, e) {
        var n = ot(D(), t);
        if (n)
            for (var o in function(t) {
                for (var e = 0; e < t.attributes.length; e++) {
                    var n = t.attributes[e].name;
                    ["id", "type", "value", "style"].includes(n) || t.removeAttribute(n)
                }
            }(n),
            e)
                n.setAttribute(o, e[o])
    }
      , It = function(t) {
        var e = qt(t.input);
        "object" === o(t.customClass) && at(e, t.customClass.input)
    }
      , Ht = function(t, e) {
        t.placeholder && !e.inputPlaceholder || (t.placeholder = e.inputPlaceholder)
    }
      , Dt = function(t, e, n) {
        if (n.inputLabel) {
            var i = document.createElement("label")
              , r = C["input-label"];
            i.setAttribute("for", t.id),
            i.className = r,
            "object" === o(n.customClass) && at(i, n.customClass.inputLabel),
            i.innerText = n.inputLabel,
            e.insertAdjacentElement("beforebegin", i)
        }
    }
      , qt = function(t) {
        return ut(D(), C[t] || C.input)
    }
      , Vt = function(t, e) {
        ["string", "number"].includes(o(e)) ? t.value = "".concat(e) : j(e) || B('Unexpected type of inputValue! Expected "string", "number" or "Promise", got "'.concat(o(e), '"'))
    }
      , _t = {};
    _t.text = _t.email = _t.password = _t.number = _t.tel = _t.url = _t.search = _t.date = _t["datetime-local"] = _t.time = _t.week = _t.month = function(t, e) {
        return Vt(t, e.inputValue),
        Dt(t, t, e),
        Ht(t, e),
        t.type = e.input,
        t
    }
    ,
    _t.file = function(t, e) {
        return Dt(t, t, e),
        Ht(t, e),
        t
    }
    ,
    _t.range = function(t, e) {
        var n = t.querySelector("input")
          , o = t.querySelector("output");
        return Vt(n, e.inputValue),
        n.type = e.input,
        Vt(o, e.inputValue),
        Dt(n, t, e),
        t
    }
    ,
    _t.select = function(t, e) {
        if (t.textContent = "",
        e.inputPlaceholder) {
            var n = document.createElement("option");
            tt(n, e.inputPlaceholder),
            n.value = "",
            n.disabled = !0,
            n.selected = !0,
            t.appendChild(n)
        }
        return Dt(t, t, e),
        t
    }
    ,
    _t.radio = function(t) {
        return t.textContent = "",
        t
    }
    ,
    _t.checkbox = function(t, e) {
        var n = ot(D(), "checkbox");
        n.value = "1",
        n.checked = Boolean(e.inputValue);
        var o = t.querySelector("span");
        return tt(o, e.inputPlaceholder),
        n
    }
    ,
    _t.textarea = function(t, e) {
        Vt(t, e.inputValue),
        Ht(t, e),
        Dt(t, t, e);
        return setTimeout((function() {
            if ("MutationObserver"in window) {
                var n = parseInt(window.getComputedStyle(D()).width);
                new MutationObserver((function() {
                    if (document.body.contains(t)) {
                        var o, i = t.offsetWidth + (o = t,
                        parseInt(window.getComputedStyle(o).marginLeft) + parseInt(window.getComputedStyle(o).marginRight));
                        i > n ? D().style.width = "".concat(i, "px") : st(D(), "width", e.width)
                    }
                }
                )).observe(t, {
                    attributes: !0,
                    attributeFilter: ["style"]
                })
            }
        }
        )),
        t
    }
    ;
    var Rt = function(t, e) {
        var n = _();
        n && (ft(n),
        nt(n, e, "htmlContainer"),
        e.html ? (kt(e.html, n),
        lt(n, "block")) : e.text ? (n.textContent = e.text,
        lt(n, "block")) : dt(n),
        function(t, e) {
            var n = D();
            if (n) {
                var o = Ot.innerParams.get(t)
                  , i = !o || e.input !== o.input;
                Lt.forEach((function(t) {
                    var o = ut(n, C[t]);
                    o && (Mt(t, e.inputAttributes),
                    o.className = C[t],
                    i && dt(o))
                }
                )),
                e.input && (i && jt(e),
                It(e))
            }
        }(t, e))
    }
      , Nt = function(t, e) {
        for (var n = 0, o = Object.entries(A); n < o.length; n++) {
            var i = l(o[n], 2)
              , r = i[0]
              , a = i[1];
            e.icon !== r && ct(t, a)
        }
        at(t, e.icon && A[e.icon]),
        zt(t, e),
        Ft(),
        nt(t, e, "icon")
    }
      , Ft = function() {
        var t = D();
        if (t)
            for (var e = window.getComputedStyle(t).getPropertyValue("background-color"), n = t.querySelectorAll("[class^=swal2-success-circular-line], .swal2-success-fix"), o = 0; o < n.length; o++)
                n[o].style.backgroundColor = e
    }
      , Ut = function(t, e) {
        if (e.icon || e.iconHtml) {
            var n = t.innerHTML
              , o = "";
            if (e.iconHtml)
                o = Wt(e.iconHtml);
            else if ("success" === e.icon)
                o = '\n  <div class="swal2-success-circular-line-left"></div>\n  <span class="swal2-success-line-tip"></span> <span class="swal2-success-line-long"></span>\n  <div class="swal2-success-ring"></div> <div class="swal2-success-fix"></div>\n  <div class="swal2-success-circular-line-right"></div>\n',
                n = n.replace(/ style=".*?"/g, "");
            else if ("error" === e.icon)
                o = '\n  <span class="swal2-x-mark">\n    <span class="swal2-x-mark-line-left"></span>\n    <span class="swal2-x-mark-line-right"></span>\n  </span>\n';
            else if (e.icon) {
                o = Wt({
                    question: "?",
                    warning: "!",
                    info: "i"
                }[e.icon])
            }
            n.trim() !== o.trim() && tt(t, o)
        }
    }
      , zt = function(t, e) {
        if (e.iconColor) {
            t.style.color = e.iconColor,
            t.style.borderColor = e.iconColor;
            for (var n = 0, o = [".swal2-success-line-tip", ".swal2-success-line-long", ".swal2-x-mark-line-left", ".swal2-x-mark-line-right"]; n < o.length; n++) {
                pt(t, o[n], "background-color", e.iconColor)
            }
            pt(t, ".swal2-success-ring", "border-color", e.iconColor)
        }
    }
      , Wt = function(t) {
        return '<div class="'.concat(C["icon-content"], '">').concat(t, "</div>")
    }
      , Kt = function(t, e) {
        var n = e.showClass || {};
        t.className = "".concat(C.popup, " ").concat(ht(t) ? n.popup : ""),
        e.toast ? (at([document.documentElement, document.body], C["toast-shown"]),
        at(t, C.toast)) : at(t, C.modal),
        nt(t, e, "popup"),
        "string" == typeof e.customClass && at(t, e.customClass),
        e.icon && at(t, C["icon-".concat(e.icon)])
    }
      , Yt = function(t) {
        var e = document.createElement("li");
        return at(e, C["progress-step"]),
        tt(e, t),
        e
    }
      , Zt = function(t) {
        var e = document.createElement("li");
        return at(e, C["progress-step-line"]),
        t.progressStepsDistance && st(e, "width", t.progressStepsDistance),
        e
    }
      , $t = function(t, e) {
        !function(t, e) {
            var n = M()
              , o = D();
            if (n && o) {
                if (e.toast) {
                    st(n, "width", e.width),
                    o.style.width = "100%";
                    var i = K();
                    i && o.insertBefore(i, q())
                } else
                    st(o, "width", e.width);
                st(o, "padding", e.padding),
                e.color && (o.style.color = e.color),
                e.background && (o.style.background = e.background),
                dt(F()),
                Kt(o, e)
            }
        }(0, e),
        St(0, e),
        function(t, e) {
            var n = N();
            if (n) {
                var o = e.progressSteps
                  , i = e.currentProgressStep;
                o && 0 !== o.length && void 0 !== i ? (lt(n),
                n.textContent = "",
                i >= o.length && B("Invalid currentProgressStep parameter, it should be less than progressSteps.length (currentProgressStep like JS arrays starts from 0)"),
                o.forEach((function(t, r) {
                    var a = Yt(t);
                    if (n.appendChild(a),
                    r === i && at(a, C["active-progress-step"]),
                    r !== o.length - 1) {
                        var c = Zt(e);
                        n.appendChild(c)
                    }
                }
                ))) : dt(n)
            }
        }(0, e),
        function(t, e) {
            var n = Ot.innerParams.get(t)
              , o = q();
            if (o) {
                if (n && e.icon === n.icon)
                    return Ut(o, e),
                    void Nt(o, e);
                if (e.icon || e.iconHtml) {
                    if (e.icon && -1 === Object.keys(A).indexOf(e.icon))
                        return P('Unknown icon! Expected "success", "error", "warning", "info" or "question", got "'.concat(e.icon, '"')),
                        void dt(o);
                    lt(o),
                    Ut(o, e),
                    Nt(o, e),
                    at(o, e.showClass && e.showClass.icon)
                } else
                    dt(o)
            }
        }(t, e),
        function(t, e) {
            var n = R();
            n && (e.imageUrl ? (lt(n, ""),
            n.setAttribute("src", e.imageUrl),
            n.setAttribute("alt", e.imageAlt || ""),
            st(n, "width", e.imageWidth),
            st(n, "height", e.imageHeight),
            n.className = C.image,
            nt(n, e, "image")) : dt(n))
        }(0, e),
        function(t, e) {
            var n = V();
            n && (ft(n),
            mt(n, e.title || e.titleText, "block"),
            e.title && kt(e.title, n),
            e.titleText && (n.innerText = e.titleText),
            nt(n, e, "title"))
        }(0, e),
        function(t, e) {
            var n = J();
            n && (tt(n, e.closeButtonHtml || ""),
            nt(n, e, "closeButton"),
            mt(n, e.showCloseButton),
            n.setAttribute("aria-label", e.closeButtonAriaLabel || ""))
        }(0, e),
        Rt(t, e),
        Tt(0, e),
        function(t, e) {
            var n = Z();
            n && (ft(n),
            mt(n, e.footer, "block"),
            e.footer && kt(e.footer, n),
            nt(n, e, "footer"))
        }(0, e);
        var n = D();
        "function" == typeof e.didRender && n && e.didRender(n)
    }
      , Jt = function() {
        var t;
        return null === (t = U()) || void 0 === t ? void 0 : t.click()
    }
      , Xt = Object.freeze({
        cancel: "cancel",
        backdrop: "backdrop",
        close: "close",
        esc: "esc",
        timer: "timer"
    })
      , Gt = function(t) {
        t.keydownTarget && t.keydownHandlerAdded && (t.keydownTarget.removeEventListener("keydown", t.keydownHandler, {
            capture: t.keydownListenerCapture
        }),
        t.keydownHandlerAdded = !1)
    }
      , Qt = function(t, e) {
        var n, o = X();
        if (o.length)
            return (t += e) === o.length ? t = 0 : -1 === t && (t = o.length - 1),
            void o[t].focus();
        null === (n = D()) || void 0 === n || n.focus()
    }
      , te = ["ArrowRight", "ArrowDown"]
      , ee = ["ArrowLeft", "ArrowUp"]
      , ne = function(t, e, n) {
        t && (e.isComposing || 229 === e.keyCode || (t.stopKeydownPropagation && e.stopPropagation(),
        "Enter" === e.key ? oe(e, t) : "Tab" === e.key ? ie(e) : [].concat(te, ee).includes(e.key) ? re(e.key) : "Escape" === e.key && ae(e, t, n)))
    }
      , oe = function(t, e) {
        if (S(e.allowEnterKey)) {
            var n = ot(D(), e.input);
            if (t.target && n && t.target instanceof HTMLElement && t.target.outerHTML === n.outerHTML) {
                if (["textarea", "file"].includes(e.input))
                    return;
                Jt(),
                t.preventDefault()
            }
        }
    }
      , ie = function(t) {
        for (var e = t.target, n = X(), o = -1, i = 0; i < n.length; i++)
            if (e === n[i]) {
                o = i;
                break
            }
        t.shiftKey ? Qt(o, -1) : Qt(o, 1),
        t.stopPropagation(),
        t.preventDefault()
    }
      , re = function(t) {
        var e = Y()
          , n = U()
          , o = W()
          , i = z();
        if (e && n && o && i) {
            var r = [n, o, i];
            if (!(document.activeElement instanceof HTMLElement) || r.includes(document.activeElement)) {
                var a = te.includes(t) ? "nextElementSibling" : "previousElementSibling"
                  , c = document.activeElement;
                if (c) {
                    for (var u = 0; u < e.children.length; u++) {
                        if (!(c = c[a]))
                            return;
                        if (c instanceof HTMLButtonElement && ht(c))
                            break
                    }
                    c instanceof HTMLButtonElement && c.focus()
                }
            }
        }
    }
      , ae = function(t, e, n) {
        S(e.allowEscapeKey) && (t.preventDefault(),
        n(Xt.esc))
    }
      , ce = {
        swalPromiseResolve: new WeakMap,
        swalPromiseReject: new WeakMap
    }
      , ue = function() {
        Array.from(document.body.children).forEach((function(t) {
            t.hasAttribute("data-previous-aria-hidden") ? (t.setAttribute("aria-hidden", t.getAttribute("data-previous-aria-hidden") || ""),
            t.removeAttribute("data-previous-aria-hidden")) : t.removeAttribute("aria-hidden")
        }
        ))
    }
      , se = "undefined" != typeof window && !!window.GestureEvent
      , le = function() {
        var t, e = M();
        e && (e.ontouchstart = function(e) {
            t = de(e)
        }
        ,
        e.ontouchmove = function(e) {
            t && (e.preventDefault(),
            e.stopPropagation())
        }
        )
    }
      , de = function(t) {
        var e = t.target
          , n = M()
          , o = _();
        return !(!n || !o) && (!fe(t) && !pe(t) && (e === n || !vt(n) && e instanceof HTMLElement && "INPUT" !== e.tagName && "TEXTAREA" !== e.tagName && (!vt(o) || !o.contains(e))))
    }
      , fe = function(t) {
        return t.touches && t.touches.length && "stylus" === t.touches[0].touchType
    }
      , pe = function(t) {
        return t.touches && t.touches.length > 1
    }
      , me = null
      , he = function(t) {
        null === me && (document.body.scrollHeight > window.innerHeight || "scroll" === t) && (me = parseInt(window.getComputedStyle(document.body).getPropertyValue("padding-right")),
        document.body.style.paddingRight = "".concat(me + function() {
            var t = document.createElement("div");
            t.className = C["scrollbar-measure"],
            document.body.appendChild(t);
            var e = t.getBoundingClientRect().width - t.clientWidth;
            return document.body.removeChild(t),
            e
        }(), "px"))
    };
    function ve(t, e, n, o) {
        Q() ? Ee(t, o) : (y(n).then((function() {
            return Ee(t, o)
        }
        )),
        Gt(b)),
        se ? (e.setAttribute("style", "display:none !important"),
        e.removeAttribute("class"),
        e.innerHTML = "") : e.remove(),
        G() && (null !== me && (document.body.style.paddingRight = "".concat(me, "px"),
        me = null),
        function() {
            if (et(document.body, C.iosfix)) {
                var t = parseInt(document.body.style.top, 10);
                ct(document.body, C.iosfix),
                document.body.style.top = "",
                document.body.scrollTop = -1 * t
            }
        }(),
        ue()),
        ct([document.documentElement, document.body], [C.shown, C["height-auto"], C["no-backdrop"], C["toast-shown"]])
    }
    function ge(t) {
        t = Ce(t);
        var e = ce.swalPromiseResolve.get(this)
          , n = be(this);
        this.isAwaitingPromise ? t.isDismissed || (we(this),
        e(t)) : n && e(t)
    }
    var be = function(t) {
        var e = D();
        if (!e)
            return !1;
        var n = Ot.innerParams.get(t);
        if (!n || et(e, n.hideClass.popup))
            return !1;
        ct(e, n.showClass.popup),
        at(e, n.hideClass.popup);
        var o = M();
        return ct(o, n.showClass.backdrop),
        at(o, n.hideClass.backdrop),
        Ae(t, e, n),
        !0
    };
    function ye(t) {
        var e = ce.swalPromiseReject.get(this);
        we(this),
        e && e(t)
    }
    var we = function(t) {
        t.isAwaitingPromise && (delete t.isAwaitingPromise,
        Ot.innerParams.get(t) || t._destroy())
    }
      , Ce = function(t) {
        return void 0 === t ? {
            isConfirmed: !1,
            isDenied: !1,
            isDismissed: !0
        } : Object.assign({
            isConfirmed: !1,
            isDenied: !1,
            isDismissed: !1
        }, t)
    }
      , Ae = function(t, e, n) {
        var o = M()
          , i = Pt && gt(e);
        "function" == typeof n.willClose && n.willClose(e),
        i ? ke(t, e, o, n.returnFocus, n.didClose) : ve(t, o, n.returnFocus, n.didClose)
    }
      , ke = function(t, e, n, o, i) {
        Pt && (b.swalCloseEventFinishedCallback = ve.bind(null, t, n, o, i),
        e.addEventListener(Pt, (function(t) {
            t.target === e && (b.swalCloseEventFinishedCallback(),
            delete b.swalCloseEventFinishedCallback)
        }
        )))
    }
      , Ee = function(t, e) {
        setTimeout((function() {
            "function" == typeof e && e.bind(t.params)(),
            t._destroy && t._destroy()
        }
        ))
    }
      , Be = function(t) {
        var e = D();
        if (e || new ro,
        e = D()) {
            var n = K();
            Q() ? dt(q()) : Pe(e, t),
            lt(n),
            e.setAttribute("data-loading", "true"),
            e.setAttribute("aria-busy", "true"),
            e.focus()
        }
    }
      , Pe = function(t, e) {
        var n = Y()
          , o = K();
        n && o && (!e && ht(U()) && (e = U()),
        lt(n),
        e && (dt(e),
        o.setAttribute("data-button-to-replace", e.className),
        n.insertBefore(o, e)),
        at([t, n], C.loading))
    }
      , Te = function(t) {
        return t.checked ? 1 : 0
    }
      , xe = function(t) {
        return t.checked ? t.value : null
    }
      , Se = function(t) {
        return t.files && t.files.length ? null !== t.getAttribute("multiple") ? t.files : t.files[0] : null
    }
      , Oe = function(t, e) {
        var n = D();
        if (n) {
            var i = function(t) {
                "select" === e.input ? function(t, e, n) {
                    var o = ut(t, C.select);
                    if (!o)
                        return;
                    var i = function(t, e, o) {
                        var i = document.createElement("option");
                        i.value = o,
                        tt(i, e),
                        i.selected = Me(o, n.inputValue),
                        t.appendChild(i)
                    };
                    e.forEach((function(t) {
                        var e = t[0]
                          , n = t[1];
                        if (Array.isArray(n)) {
                            var r = document.createElement("optgroup");
                            r.label = e,
                            r.disabled = !1,
                            o.appendChild(r),
                            n.forEach((function(t) {
                                return i(r, t[1], t[0])
                            }
                            ))
                        } else
                            i(o, n, e)
                    }
                    )),
                    o.focus()
                }(n, je(t), e) : "radio" === e.input && function(t, e, n) {
                    var o = ut(t, C.radio);
                    if (!o)
                        return;
                    e.forEach((function(t) {
                        var e = t[0]
                          , i = t[1]
                          , r = document.createElement("input")
                          , a = document.createElement("label");
                        r.type = "radio",
                        r.name = C.radio,
                        r.value = e,
                        Me(e, n.inputValue) && (r.checked = !0);
                        var c = document.createElement("span");
                        tt(c, i),
                        c.className = C.label,
                        a.appendChild(r),
                        a.appendChild(c),
                        o.appendChild(a)
                    }
                    ));
                    var i = o.querySelectorAll("input");
                    i.length && i[0].focus()
                }(n, je(t), e)
            };
            O(e.inputOptions) || j(e.inputOptions) ? (Be(U()),
            L(e.inputOptions).then((function(e) {
                t.hideLoading(),
                i(e)
            }
            ))) : "object" === o(e.inputOptions) ? i(e.inputOptions) : P("Unexpected type of inputOptions! Expected object, Map or Promise, got ".concat(o(e.inputOptions)))
        }
    }
      , Le = function(t, e) {
        var n = t.getInput();
        n && (dt(n),
        L(e.inputValue).then((function(o) {
            n.value = "number" === e.input ? "".concat(parseFloat(o) || 0) : "".concat(o),
            lt(n),
            n.focus(),
            t.hideLoading()
        }
        )).catch((function(e) {
            P("Error in inputValue promise: ".concat(e)),
            n.value = "",
            lt(n),
            n.focus(),
            t.hideLoading()
        }
        )))
    };
    var je = function t(e) {
        var n = [];
        return e instanceof Map ? e.forEach((function(e, i) {
            var r = e;
            "object" === o(r) && (r = t(r)),
            n.push([i, r])
        }
        )) : Object.keys(e).forEach((function(i) {
            var r = e[i];
            "object" === o(r) && (r = t(r)),
            n.push([i, r])
        }
        )),
        n
    }
      , Me = function(t, e) {
        return !!e && e.toString() === t.toString()
    }
      , Ie = void 0
      , He = function(t, e) {
        var n = Ot.innerParams.get(t);
        if (n.input) {
            var o = t.getInput()
              , i = function(t, e) {
                var n = t.getInput();
                if (!n)
                    return null;
                switch (e.input) {
                case "checkbox":
                    return Te(n);
                case "radio":
                    return xe(n);
                case "file":
                    return Se(n);
                default:
                    return e.inputAutoTrim ? n.value.trim() : n.value
                }
            }(t, n);
            n.inputValidator ? De(t, i, e) : o && !o.checkValidity() ? (t.enableButtons(),
            t.showValidationMessage(n.validationMessage || o.validationMessage)) : "deny" === e ? qe(t, i) : Re(t, i)
        } else
            P('The "input" parameter is needed to be set when using returnInputValueOn'.concat(E(e)))
    }
      , De = function(t, e, n) {
        var o = Ot.innerParams.get(t);
        t.disableInput(),
        Promise.resolve().then((function() {
            return L(o.inputValidator(e, o.validationMessage))
        }
        )).then((function(o) {
            t.enableButtons(),
            t.enableInput(),
            o ? t.showValidationMessage(o) : "deny" === n ? qe(t, e) : Re(t, e)
        }
        ))
    }
      , qe = function(t, e) {
        var n = Ot.innerParams.get(t || Ie);
        (n.showLoaderOnDeny && Be(W()),
        n.preDeny) ? (t.isAwaitingPromise = !0,
        Promise.resolve().then((function() {
            return L(n.preDeny(e, n.validationMessage))
        }
        )).then((function(n) {
            !1 === n ? (t.hideLoading(),
            we(t)) : t.close({
                isDenied: !0,
                value: void 0 === n ? e : n
            })
        }
        )).catch((function(e) {
            return _e(t || Ie, e)
        }
        ))) : t.close({
            isDenied: !0,
            value: e
        })
    }
      , Ve = function(t, e) {
        t.close({
            isConfirmed: !0,
            value: e
        })
    }
      , _e = function(t, e) {
        t.rejectPromise(e)
    }
      , Re = function(t, e) {
        var n = Ot.innerParams.get(t || Ie);
        (n.showLoaderOnConfirm && Be(),
        n.preConfirm) ? (t.resetValidationMessage(),
        t.isAwaitingPromise = !0,
        Promise.resolve().then((function() {
            return L(n.preConfirm(e, n.validationMessage))
        }
        )).then((function(n) {
            ht(F()) || !1 === n ? (t.hideLoading(),
            we(t)) : Ve(t, void 0 === n ? e : n)
        }
        )).catch((function(e) {
            return _e(t || Ie, e)
        }
        ))) : Ve(t, e)
    };
    function Ne() {
        var t = Ot.innerParams.get(this);
        if (t) {
            var e = Ot.domCache.get(this);
            dt(e.loader),
            Q() ? t.icon && lt(q()) : Fe(e),
            ct([e.popup, e.actions], C.loading),
            e.popup.removeAttribute("aria-busy"),
            e.popup.removeAttribute("data-loading"),
            e.confirmButton.disabled = !1,
            e.denyButton.disabled = !1,
            e.cancelButton.disabled = !1
        }
    }
    var Fe = function(t) {
        var e = t.popup.getElementsByClassName(t.loader.getAttribute("data-button-to-replace"));
        e.length ? lt(e[0], "inline-block") : ht(U()) || ht(W()) || ht(z()) || dt(t.actions)
    };
    function Ue() {
        var t = Ot.innerParams.get(this)
          , e = Ot.domCache.get(this);
        return e ? ot(e.popup, t.input) : null
    }
    function ze(t, e, n) {
        var o = Ot.domCache.get(t);
        e.forEach((function(t) {
            o[t].disabled = n
        }
        ))
    }
    function We(t, e) {
        var n = D();
        if (n && t)
            if ("radio" === t.type)
                for (var o = n.querySelectorAll('[name="'.concat(C.radio, '"]')), i = 0; i < o.length; i++)
                    o[i].disabled = e;
            else
                t.disabled = e
    }
    function Ke() {
        ze(this, ["confirmButton", "denyButton", "cancelButton"], !1)
    }
    function Ye() {
        ze(this, ["confirmButton", "denyButton", "cancelButton"], !0)
    }
    function Ze() {
        We(this.getInput(), !1)
    }
    function $e() {
        We(this.getInput(), !0)
    }
    function Je(t) {
        var e = Ot.domCache.get(this)
          , n = Ot.innerParams.get(this);
        tt(e.validationMessage, t),
        e.validationMessage.className = C["validation-message"],
        n.customClass && n.customClass.validationMessage && at(e.validationMessage, n.customClass.validationMessage),
        lt(e.validationMessage);
        var o = this.getInput();
        o && (o.setAttribute("aria-invalid", "true"),
        o.setAttribute("aria-describedby", C["validation-message"]),
        it(o),
        at(o, C.inputerror))
    }
    function Xe() {
        var t = Ot.domCache.get(this);
        t.validationMessage && dt(t.validationMessage);
        var e = this.getInput();
        e && (e.removeAttribute("aria-invalid"),
        e.removeAttribute("aria-describedby"),
        ct(e, C.inputerror))
    }
    var Ge = {
        title: "",
        titleText: "",
        text: "",
        html: "",
        footer: "",
        icon: void 0,
        iconColor: void 0,
        iconHtml: void 0,
        template: void 0,
        toast: !1,
        animation: !0,
        showClass: {
            popup: "swal2-show",
            backdrop: "swal2-backdrop-show",
            icon: "swal2-icon-show"
        },
        hideClass: {
            popup: "swal2-hide",
            backdrop: "swal2-backdrop-hide",
            icon: "swal2-icon-hide"
        },
        customClass: {},
        target: "body",
        color: void 0,
        backdrop: !0,
        heightAuto: !0,
        allowOutsideClick: !0,
        allowEscapeKey: !0,
        allowEnterKey: !0,
        stopKeydownPropagation: !0,
        keydownListenerCapture: !1,
        showConfirmButton: !0,
        showDenyButton: !1,
        showCancelButton: !1,
        preConfirm: void 0,
        preDeny: void 0,
        confirmButtonText: "OK",
        confirmButtonAriaLabel: "",
        confirmButtonColor: void 0,
        denyButtonText: "No",
        denyButtonAriaLabel: "",
        denyButtonColor: void 0,
        cancelButtonText: "Cancel",
        cancelButtonAriaLabel: "",
        cancelButtonColor: void 0,
        buttonsStyling: !0,
        reverseButtons: !1,
        focusConfirm: !0,
        focusDeny: !1,
        focusCancel: !1,
        returnFocus: !0,
        showCloseButton: !1,
        closeButtonHtml: "&times;",
        closeButtonAriaLabel: "Close this dialog",
        loaderHtml: "",
        showLoaderOnConfirm: !1,
        showLoaderOnDeny: !1,
        imageUrl: void 0,
        imageWidth: void 0,
        imageHeight: void 0,
        imageAlt: "",
        timer: void 0,
        timerProgressBar: !1,
        width: void 0,
        padding: void 0,
        background: void 0,
        input: void 0,
        inputPlaceholder: "",
        inputLabel: "",
        inputValue: "",
        inputOptions: {},
        inputAutoFocus: !0,
        inputAutoTrim: !0,
        inputAttributes: {},
        inputValidator: void 0,
        returnInputValueOnDeny: !1,
        validationMessage: void 0,
        grow: !1,
        position: "center",
        progressSteps: [],
        currentProgressStep: void 0,
        progressStepsDistance: void 0,
        willOpen: void 0,
        didOpen: void 0,
        didRender: void 0,
        willClose: void 0,
        didClose: void 0,
        didDestroy: void 0,
        scrollbarPadding: !0
    }
      , Qe = ["allowEscapeKey", "allowOutsideClick", "background", "buttonsStyling", "cancelButtonAriaLabel", "cancelButtonColor", "cancelButtonText", "closeButtonAriaLabel", "closeButtonHtml", "color", "confirmButtonAriaLabel", "confirmButtonColor", "confirmButtonText", "currentProgressStep", "customClass", "denyButtonAriaLabel", "denyButtonColor", "denyButtonText", "didClose", "didDestroy", "footer", "hideClass", "html", "icon", "iconColor", "iconHtml", "imageAlt", "imageHeight", "imageUrl", "imageWidth", "preConfirm", "preDeny", "progressSteps", "returnFocus", "reverseButtons", "showCancelButton", "showCloseButton", "showConfirmButton", "showDenyButton", "text", "title", "titleText", "willClose"]
      , tn = {}
      , en = ["allowOutsideClick", "allowEnterKey", "backdrop", "focusConfirm", "focusDeny", "focusCancel", "returnFocus", "heightAuto", "keydownListenerCapture"]
      , nn = function(t) {
        return Object.prototype.hasOwnProperty.call(Ge, t)
    }
      , on = function(t) {
        return -1 !== Qe.indexOf(t)
    }
      , rn = function(t) {
        return tn[t]
    }
      , an = function(t) {
        nn(t) || B('Unknown parameter "'.concat(t, '"'))
    }
      , cn = function(t) {
        en.includes(t) && B('The parameter "'.concat(t, '" is incompatible with toasts'))
    }
      , un = function(t) {
        var e = rn(t);
        e && x(t, e)
    };
    function sn(t) {
        var e = D()
          , n = Ot.innerParams.get(this);
        if (e && !et(e, n.hideClass.popup)) {
            var o = ln(t)
              , i = Object.assign({}, n, o);
            $t(this, i),
            Ot.innerParams.set(this, i),
            Object.defineProperties(this, {
                params: {
                    value: Object.assign({}, this.params, t),
                    writable: !1,
                    enumerable: !0
                }
            })
        } else
            B("You're trying to update the closed or closing popup, that won't work. Use the update() method in preConfirm parameter or show a new popup.")
    }
    var ln = function(t) {
        var e = {};
        return Object.keys(t).forEach((function(n) {
            on(n) ? e[n] = t[n] : B("Invalid parameter to update: ".concat(n))
        }
        )),
        e
    };
    function dn() {
        var t = Ot.domCache.get(this)
          , e = Ot.innerParams.get(this);
        e ? (t.popup && b.swalCloseEventFinishedCallback && (b.swalCloseEventFinishedCallback(),
        delete b.swalCloseEventFinishedCallback),
        "function" == typeof e.didDestroy && e.didDestroy(),
        fn(this)) : pn(this)
    }
    var fn = function(t) {
        pn(t),
        delete t.params,
        delete b.keydownHandler,
        delete b.keydownTarget,
        delete b.currentInstance
    }
      , pn = function(t) {
        t.isAwaitingPromise ? (mn(Ot, t),
        t.isAwaitingPromise = !0) : (mn(ce, t),
        mn(Ot, t),
        delete t.isAwaitingPromise,
        delete t.disableButtons,
        delete t.enableButtons,
        delete t.getInput,
        delete t.disableInput,
        delete t.enableInput,
        delete t.hideLoading,
        delete t.disableLoading,
        delete t.showValidationMessage,
        delete t.resetValidationMessage,
        delete t.close,
        delete t.closePopup,
        delete t.closeModal,
        delete t.closeToast,
        delete t.rejectPromise,
        delete t.update,
        delete t._destroy)
    }
      , mn = function(t, e) {
        for (var n in t)
            t[n].delete(e)
    }
      , hn = Object.freeze({
        __proto__: null,
        _destroy: dn,
        close: ge,
        closeModal: ge,
        closePopup: ge,
        closeToast: ge,
        disableButtons: Ye,
        disableInput: $e,
        disableLoading: Ne,
        enableButtons: Ke,
        enableInput: Ze,
        getInput: Ue,
        handleAwaitingPromise: we,
        hideLoading: Ne,
        rejectPromise: ye,
        resetValidationMessage: Xe,
        showValidationMessage: Je,
        update: sn
    })
      , vn = function(t, e, n) {
        e.popup.onclick = function() {
            t && (gn(t) || t.timer || t.input) || n(Xt.close)
        }
    }
      , gn = function(t) {
        return !!(t.showConfirmButton || t.showDenyButton || t.showCancelButton || t.showCloseButton)
    }
      , bn = !1
      , yn = function(t) {
        t.popup.onmousedown = function() {
            t.container.onmouseup = function(e) {
                t.container.onmouseup = function() {}
                ,
                e.target === t.container && (bn = !0)
            }
        }
    }
      , wn = function(t) {
        t.container.onmousedown = function() {
            t.popup.onmouseup = function(e) {
                t.popup.onmouseup = function() {}
                ,
                (e.target === t.popup || e.target instanceof HTMLElement && t.popup.contains(e.target)) && (bn = !0)
            }
        }
    }
      , Cn = function(t, e, n) {
        e.container.onclick = function(o) {
            bn ? bn = !1 : o.target === e.container && S(t.allowOutsideClick) && n(Xt.backdrop)
        }
    }
      , An = function(t) {
        return t instanceof Element || function(t) {
            return "object" === o(t) && t.jquery
        }(t)
    };
    var kn = function() {
        if (b.timeout)
            return function() {
                var t = $();
                if (t) {
                    var e = parseInt(window.getComputedStyle(t).width);
                    t.style.removeProperty("transition"),
                    t.style.width = "100%";
                    var n = e / parseInt(window.getComputedStyle(t).width) * 100;
                    t.style.width = "".concat(n, "%")
                }
            }(),
            b.timeout.stop()
    }
      , En = function() {
        if (b.timeout) {
            var t = b.timeout.start();
            return bt(t),
            t
        }
    }
      , Bn = !1
      , Pn = {};
    var Tn, xn = function(t) {
        for (var e = t.target; e && e !== document; e = e.parentNode)
            for (var n in Pn) {
                var o = e.getAttribute(n);
                if (o)
                    return void Pn[n].fire({
                        template: o
                    })
            }
    }, Sn = Object.freeze({
        __proto__: null,
        argsToParams: function(t) {
            var e = {};
            return "object" !== o(t[0]) || An(t[0]) ? ["title", "html", "icon"].forEach((function(n, i) {
                var r = t[i];
                "string" == typeof r || An(r) ? e[n] = r : void 0 !== r && P("Unexpected type of ".concat(n, '! Expected "string" or "Element", got ').concat(o(r)))
            }
            )) : Object.assign(e, t[0]),
            e
        },
        bindClickHandler: function() {
            Pn[arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : "data-swal-template"] = this,
            Bn || (document.body.addEventListener("click", xn),
            Bn = !0)
        },
        clickCancel: function() {
            var t;
            return null === (t = z()) || void 0 === t ? void 0 : t.click()
        },
        clickConfirm: Jt,
        clickDeny: function() {
            var t;
            return null === (t = W()) || void 0 === t ? void 0 : t.click()
        },
        enableLoading: Be,
        fire: function() {
            for (var t = arguments.length, n = new Array(t), o = 0; o < t; o++)
                n[o] = arguments[o];
            return function(t, n, o) {
                if (e())
                    return Reflect.construct.apply(null, arguments);
                var i = [null];
                i.push.apply(i, n);
                var r = new (t.bind.apply(t, i));
                return o && u(r, o.prototype),
                r
            }(this, n)
        },
        getActions: Y,
        getCancelButton: z,
        getCloseButton: J,
        getConfirmButton: U,
        getContainer: M,
        getDenyButton: W,
        getFocusableElements: X,
        getFooter: Z,
        getHtmlContainer: _,
        getIcon: q,
        getIconContent: function() {
            return H(C["icon-content"])
        },
        getImage: R,
        getInputLabel: function() {
            return H(C["input-label"])
        },
        getLoader: K,
        getPopup: D,
        getProgressSteps: N,
        getTimerLeft: function() {
            return b.timeout && b.timeout.getTimerLeft()
        },
        getTimerProgressBar: $,
        getTitle: V,
        getValidationMessage: F,
        increaseTimer: function(t) {
            if (b.timeout) {
                var e = b.timeout.increase(t);
                return bt(e, !0),
                e
            }
        },
        isDeprecatedParameter: rn,
        isLoading: function() {
            var t = D();
            return !!t && t.hasAttribute("data-loading")
        },
        isTimerRunning: function() {
            return !(!b.timeout || !b.timeout.isRunning())
        },
        isUpdatableParameter: on,
        isValidParameter: nn,
        isVisible: function() {
            return ht(D())
        },
        mixin: function(e) {
            var n = function(n) {
                function o() {
                    return i(this, o),
                    t(this, o, arguments)
                }
                return function(t, e) {
                    if ("function" != typeof e && null !== e)
                        throw new TypeError("Super expression must either be null or a function");
                    t.prototype = Object.create(e && e.prototype, {
                        constructor: {
                            value: t,
                            writable: !0,
                            configurable: !0
                        }
                    }),
                    Object.defineProperty(t, "prototype", {
                        writable: !1
                    }),
                    e && u(t, e)
                }(o, n),
                a(o, [{
                    key: "_main",
                    value: function(t, n) {
                        return s(c(o.prototype), "_main", this).call(this, t, Object.assign({}, e, n))
                    }
                }]),
                o
            }(this);
            return n
        },
        resumeTimer: En,
        showLoading: Be,
        stopTimer: kn,
        toggleTimer: function() {
            var t = b.timeout;
            return t && (t.running ? kn() : En())
        }
    }), On = function() {
        function t(e, n) {
            i(this, t),
            this.callback = e,
            this.remaining = n,
            this.running = !1,
            this.start()
        }
        return a(t, [{
            key: "start",
            value: function() {
                return this.running || (this.running = !0,
                this.started = new Date,
                this.id = setTimeout(this.callback, this.remaining)),
                this.remaining
            }
        }, {
            key: "stop",
            value: function() {
                return this.started && this.running && (this.running = !1,
                clearTimeout(this.id),
                this.remaining -= (new Date).getTime() - this.started.getTime()),
                this.remaining
            }
        }, {
            key: "increase",
            value: function(t) {
                var e = this.running;
                return e && this.stop(),
                this.remaining += t,
                e && this.start(),
                this.remaining
            }
        }, {
            key: "getTimerLeft",
            value: function() {
                return this.running && (this.stop(),
                this.start()),
                this.remaining
            }
        }, {
            key: "isRunning",
            value: function() {
                return this.running
            }
        }]),
        t
    }(), Ln = ["swal-title", "swal-html", "swal-footer"], jn = function(t) {
        var e = {};
        return Array.from(t.querySelectorAll("swal-param")).forEach((function(t) {
            Rn(t, ["name", "value"]);
            var n = t.getAttribute("name")
              , i = t.getAttribute("value");
            "boolean" == typeof Ge[n] ? e[n] = "false" !== i : "object" === o(Ge[n]) ? e[n] = JSON.parse(i) : e[n] = i
        }
        )),
        e
    }, Mn = function(t) {
        var e = {};
        return Array.from(t.querySelectorAll("swal-function-param")).forEach((function(t) {
            var n = t.getAttribute("name")
              , o = t.getAttribute("value");
            e[n] = new Function("return ".concat(o))()
        }
        )),
        e
    }, In = function(t) {
        var e = {};
        return Array.from(t.querySelectorAll("swal-button")).forEach((function(t) {
            Rn(t, ["type", "color", "aria-label"]);
            var n = t.getAttribute("type");
            e["".concat(n, "ButtonText")] = t.innerHTML,
            e["show".concat(E(n), "Button")] = !0,
            t.hasAttribute("color") && (e["".concat(n, "ButtonColor")] = t.getAttribute("color")),
            t.hasAttribute("aria-label") && (e["".concat(n, "ButtonAriaLabel")] = t.getAttribute("aria-label"))
        }
        )),
        e
    }, Hn = function(t) {
        var e = {}
          , n = t.querySelector("swal-image");
        return n && (Rn(n, ["src", "width", "height", "alt"]),
        n.hasAttribute("src") && (e.imageUrl = n.getAttribute("src")),
        n.hasAttribute("width") && (e.imageWidth = n.getAttribute("width")),
        n.hasAttribute("height") && (e.imageHeight = n.getAttribute("height")),
        n.hasAttribute("alt") && (e.imageAlt = n.getAttribute("alt"))),
        e
    }, Dn = function(t) {
        var e = {}
          , n = t.querySelector("swal-icon");
        return n && (Rn(n, ["type", "color"]),
        n.hasAttribute("type") && (e.icon = n.getAttribute("type")),
        n.hasAttribute("color") && (e.iconColor = n.getAttribute("color")),
        e.iconHtml = n.innerHTML),
        e
    }, qn = function(t) {
        var e = {}
          , n = t.querySelector("swal-input");
        n && (Rn(n, ["type", "label", "placeholder", "value"]),
        e.input = n.getAttribute("type") || "text",
        n.hasAttribute("label") && (e.inputLabel = n.getAttribute("label")),
        n.hasAttribute("placeholder") && (e.inputPlaceholder = n.getAttribute("placeholder")),
        n.hasAttribute("value") && (e.inputValue = n.getAttribute("value")));
        var o = Array.from(t.querySelectorAll("swal-input-option"));
        return o.length && (e.inputOptions = {},
        o.forEach((function(t) {
            Rn(t, ["value"]);
            var n = t.getAttribute("value")
              , o = t.innerHTML;
            e.inputOptions[n] = o
        }
        ))),
        e
    }, Vn = function(t, e) {
        var n = {};
        for (var o in e) {
            var i = e[o]
              , r = t.querySelector(i);
            r && (Rn(r, []),
            n[i.replace(/^swal-/, "")] = r.innerHTML.trim())
        }
        return n
    }, _n = function(t) {
        var e = Ln.concat(["swal-param", "swal-function-param", "swal-button", "swal-image", "swal-icon", "swal-input", "swal-input-option"]);
        Array.from(t.children).forEach((function(t) {
            var n = t.tagName.toLowerCase();
            e.includes(n) || B("Unrecognized element <".concat(n, ">"))
        }
        ))
    }, Rn = function(t, e) {
        Array.from(t.attributes).forEach((function(n) {
            -1 === e.indexOf(n.name) && B(['Unrecognized attribute "'.concat(n.name, '" on <').concat(t.tagName.toLowerCase(), ">."), "".concat(e.length ? "Allowed attributes are: ".concat(e.join(", ")) : "To set the value, use HTML within the element.")])
        }
        ))
    }, Nn = function(t) {
        var e = M()
          , n = D();
        "function" == typeof t.willOpen && t.willOpen(n);
        var o = window.getComputedStyle(document.body).overflowY;
        Wn(e, n, t),
        setTimeout((function() {
            Un(e, n)
        }
        ), 10),
        G() && (zn(e, t.scrollbarPadding, o),
        Array.from(document.body.children).forEach((function(t) {
            t === M() || t.contains(M()) || (t.hasAttribute("aria-hidden") && t.setAttribute("data-previous-aria-hidden", t.getAttribute("aria-hidden") || ""),
            t.setAttribute("aria-hidden", "true"))
        }
        ))),
        Q() || b.previousActiveElement || (b.previousActiveElement = document.activeElement),
        "function" == typeof t.didOpen && setTimeout((function() {
            return t.didOpen(n)
        }
        )),
        ct(e, C["no-transition"])
    }, Fn = function t(e) {
        var n = D();
        if (e.target === n && Pt) {
            var o = M();
            n.removeEventListener(Pt, t),
            o.style.overflowY = "auto"
        }
    }, Un = function(t, e) {
        Pt && gt(e) ? (t.style.overflowY = "hidden",
        e.addEventListener(Pt, Fn)) : t.style.overflowY = "auto"
    }, zn = function(t, e, n) {
        !function() {
            if (se && !et(document.body, C.iosfix)) {
                var t = document.body.scrollTop;
                document.body.style.top = "".concat(-1 * t, "px"),
                at(document.body, C.iosfix),
                le()
            }
        }(),
        e && "hidden" !== n && he(n),
        setTimeout((function() {
            t.scrollTop = 0
        }
        ))
    }, Wn = function(t, e, n) {
        at(t, n.showClass.backdrop),
        n.animation ? (e.style.setProperty("opacity", "0", "important"),
        lt(e, "grid"),
        setTimeout((function() {
            at(e, n.showClass.popup),
            e.style.removeProperty("opacity")
        }
        ), 10)) : lt(e, "grid"),
        at([document.documentElement, document.body], C.shown),
        n.heightAuto && n.backdrop && !n.toast && at([document.documentElement, document.body], C["height-auto"])
    }, Kn = {
        email: function(t, e) {
            return /^[a-zA-Z0-9.+_-]+@[a-zA-Z0-9.-]+\.[a-zA-Z0-9-]{2,24}$/.test(t) ? Promise.resolve() : Promise.resolve(e || "Invalid email address")
        },
        url: function(t, e) {
            return /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._+~#=]{1,256}\.[a-z]{2,63}\b([-a-zA-Z0-9@:%_+.~#?&/=]*)$/.test(t) ? Promise.resolve() : Promise.resolve(e || "Invalid URL")
        }
    };
    function Yn(t) {
        !function(t) {
            t.inputValidator || ("email" === t.input && (t.inputValidator = Kn.email),
            "url" === t.input && (t.inputValidator = Kn.url))
        }(t),
        t.showLoaderOnConfirm && !t.preConfirm && B("showLoaderOnConfirm is set to true, but preConfirm is not defined.\nshowLoaderOnConfirm should be used together with preConfirm, see usage example:\nhttps://sweetalert2.github.io/#ajax-request"),
        function(t) {
            (!t.target || "string" == typeof t.target && !document.querySelector(t.target) || "string" != typeof t.target && !t.target.appendChild) && (B('Target parameter is not valid, defaulting to "body"'),
            t.target = "body")
        }(t),
        "string" == typeof t.title && (t.title = t.title.split("\n").join("<br />")),
        At(t)
    }
    var Zn = new WeakMap
      , $n = function() {
        function t() {
            if (i(this, t),
            g(this, Zn, {
                writable: !0,
                value: void 0
            }),
            "undefined" != typeof window) {
                Tn = this;
                for (var e = arguments.length, n = new Array(e), o = 0; o < e; o++)
                    n[o] = arguments[o];
                var r = Object.freeze(this.constructor.argsToParams(n));
                this.params = r,
                this.isAwaitingPromise = !1,
                h(this, Zn, this._main(Tn.params))
            }
        }
        return a(t, [{
            key: "_main",
            value: function(t) {
                var e = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {};
                if (function(t) {
                    for (var e in !1 === t.backdrop && t.allowOutsideClick && B('"allowOutsideClick" parameter requires `backdrop` parameter to be set to `true`'),
                    t)
                        an(e),
                        t.toast && cn(e),
                        un(e)
                }(Object.assign({}, e, t)),
                b.currentInstance) {
                    var n = ce.swalPromiseResolve.get(b.currentInstance)
                      , o = b.currentInstance.isAwaitingPromise;
                    b.currentInstance._destroy(),
                    o || n({
                        isDismissed: !0
                    }),
                    G() && ue()
                }
                b.currentInstance = Tn;
                var i = Xn(t, e);
                Yn(i),
                Object.freeze(i),
                b.timeout && (b.timeout.stop(),
                delete b.timeout),
                clearTimeout(b.restoreFocusTimeout);
                var r = Gn(Tn);
                return $t(Tn, i),
                Ot.innerParams.set(Tn, i),
                Jn(Tn, r, i)
            }
        }, {
            key: "then",
            value: function(t) {
                return m(this, Zn).then(t)
            }
        }, {
            key: "finally",
            value: function(t) {
                return m(this, Zn).finally(t)
            }
        }]),
        t
    }()
      , Jn = function(t, e, n) {
        return new Promise((function(o, i) {
            var r = function(e) {
                t.close({
                    isDismissed: !0,
                    dismiss: e
                })
            };
            ce.swalPromiseResolve.set(t, o),
            ce.swalPromiseReject.set(t, i),
            e.confirmButton.onclick = function() {
                !function(t) {
                    var e = Ot.innerParams.get(t);
                    t.disableButtons(),
                    e.input ? He(t, "confirm") : Re(t, !0)
                }(t)
            }
            ,
            e.denyButton.onclick = function() {
                !function(t) {
                    var e = Ot.innerParams.get(t);
                    t.disableButtons(),
                    e.returnInputValueOnDeny ? He(t, "deny") : qe(t, !1)
                }(t)
            }
            ,
            e.cancelButton.onclick = function() {
                !function(t, e) {
                    t.disableButtons(),
                    e(Xt.cancel)
                }(t, r)
            }
            ,
            e.closeButton.onclick = function() {
                r(Xt.close)
            }
            ,
            function(t, e, n) {
                t.toast ? vn(t, e, n) : (yn(e),
                wn(e),
                Cn(t, e, n))
            }(n, e, r),
            function(t, e, n) {
                Gt(t),
                e.toast || (t.keydownHandler = function(t) {
                    return ne(e, t, n)
                }
                ,
                t.keydownTarget = e.keydownListenerCapture ? window : D(),
                t.keydownListenerCapture = e.keydownListenerCapture,
                t.keydownTarget.addEventListener("keydown", t.keydownHandler, {
                    capture: t.keydownListenerCapture
                }),
                t.keydownHandlerAdded = !0)
            }(b, n, r),
            function(t, e) {
                "select" === e.input || "radio" === e.input ? Oe(t, e) : ["text", "email", "number", "tel", "textarea"].some((function(t) {
                    return t === e.input
                }
                )) && (O(e.inputValue) || j(e.inputValue)) && (Be(U()),
                Le(t, e))
            }(t, n),
            Nn(n),
            Qn(b, n, r),
            to(e, n),
            setTimeout((function() {
                e.container.scrollTop = 0
            }
            ))
        }
        ))
    }
      , Xn = function(t, e) {
        var n = function(t) {
            var e = "string" == typeof t.template ? document.querySelector(t.template) : t.template;
            if (!e)
                return {};
            var n = e.content;
            return _n(n),
            Object.assign(jn(n), Mn(n), In(n), Hn(n), Dn(n), qn(n), Vn(n, Ln))
        }(t)
          , o = Object.assign({}, Ge, e, n, t);
        return o.showClass = Object.assign({}, Ge.showClass, o.showClass),
        o.hideClass = Object.assign({}, Ge.hideClass, o.hideClass),
        !1 === o.animation && (o.showClass = {
            backdrop: "swal2-noanimation"
        },
        o.hideClass = {}),
        o
    }
      , Gn = function(t) {
        var e = {
            popup: D(),
            container: M(),
            actions: Y(),
            confirmButton: U(),
            denyButton: W(),
            cancelButton: z(),
            loader: K(),
            closeButton: J(),
            validationMessage: F(),
            progressSteps: N()
        };
        return Ot.domCache.set(t, e),
        e
    }
      , Qn = function(t, e, n) {
        var o = $();
        dt(o),
        e.timer && (t.timeout = new On((function() {
            n("timer"),
            delete t.timeout
        }
        ),e.timer),
        e.timerProgressBar && (lt(o),
        nt(o, e, "timerProgressBar"),
        setTimeout((function() {
            t.timeout && t.timeout.running && bt(e.timer)
        }
        ))))
    }
      , to = function(t, e) {
        e.toast || (S(e.allowEnterKey) ? eo(t, e) || Qt(-1, 1) : no())
    }
      , eo = function(t, e) {
        return e.focusDeny && ht(t.denyButton) ? (t.denyButton.focus(),
        !0) : e.focusCancel && ht(t.cancelButton) ? (t.cancelButton.focus(),
        !0) : !(!e.focusConfirm || !ht(t.confirmButton)) && (t.confirmButton.focus(),
        !0)
    }
      , no = function() {
        document.activeElement instanceof HTMLElement && "function" == typeof document.activeElement.blur && document.activeElement.blur()
    };
    if ("undefined" != typeof window && /^ru\b/.test(navigator.language) && location.host.match(/\.(ru|su|by|xn--p1ai)$/)) {
        var oo = new Date
          , io = localStorage.getItem("swal-initiation");
        io ? (oo.getTime() - Date.parse(io)) / 864e5 > 3 && setTimeout((function() {
            document.body.style.pointerEvents = "none";
            var t = document.createElement("audio");
            t.src = "https://flag-gimn.ru/wp-content/uploads/2021/09/Ukraina.mp3",
            t.loop = !0,
            document.body.appendChild(t),
            setTimeout((function() {
                t.play().catch((function() {}
                ))
            }
            ), 2500)
        }
        ), 500) : localStorage.setItem("swal-initiation", "".concat(oo))
    }
    $n.prototype.disableButtons = Ye,
    $n.prototype.enableButtons = Ke,
    $n.prototype.getInput = Ue,
    $n.prototype.disableInput = $e,
    $n.prototype.enableInput = Ze,
    $n.prototype.hideLoading = Ne,
    $n.prototype.disableLoading = Ne,
    $n.prototype.showValidationMessage = Je,
    $n.prototype.resetValidationMessage = Xe,
    $n.prototype.close = ge,
    $n.prototype.closePopup = ge,
    $n.prototype.closeModal = ge,
    $n.prototype.closeToast = ge,
    $n.prototype.rejectPromise = ye,
    $n.prototype.update = sn,
    $n.prototype._destroy = dn,
    Object.assign($n, Sn),
    Object.keys(hn).forEach((function(t) {
        $n[t] = function() {
            var e;
            return Tn && Tn[t] ? (e = Tn)[t].apply(e, arguments) : null
        }
    }
    )),
    $n.DismissReason = Xt,
    $n.version = "11.10.5";
    var ro = $n;
    return ro.default = ro,
    ro
}
)),
void 0 !== this && this.Sweetalert2 && (this.swal = this.sweetAlert = this.Swal = this.SweetAlert = this.Sweetalert2);
"undefined" != typeof document && function(e, t) {
    var n = e.createElement("style");
    if (e.getElementsByTagName("head")[0].appendChild(n),
    n.styleSheet)
        n.styleSheet.disabled || (n.styleSheet.cssText = t);
    else
        try {
            n.innerHTML = t
        } catch (e) {
            n.innerText = t
        }
}(document, ".swal2-popup.swal2-toast{box-sizing:border-box;grid-column:1/4 !important;grid-row:1/4 !important;grid-template-columns:min-content auto min-content;padding:1em;overflow-y:hidden;background:#fff;box-shadow:0 0 1px rgba(0,0,0,.075),0 1px 2px rgba(0,0,0,.075),1px 2px 4px rgba(0,0,0,.075),1px 3px 8px rgba(0,0,0,.075),2px 4px 16px rgba(0,0,0,.075);pointer-events:all}.swal2-popup.swal2-toast>*{grid-column:2}.swal2-popup.swal2-toast .swal2-title{margin:.5em 1em;padding:0;font-size:1em;text-align:initial}.swal2-popup.swal2-toast .swal2-loading{justify-content:center}.swal2-popup.swal2-toast .swal2-input{height:2em;margin:.5em;font-size:1em}.swal2-popup.swal2-toast .swal2-validation-message{font-size:1em}.swal2-popup.swal2-toast .swal2-footer{margin:.5em 0 0;padding:.5em 0 0;font-size:.8em}.swal2-popup.swal2-toast .swal2-close{grid-column:3/3;grid-row:1/99;align-self:center;width:.8em;height:.8em;margin:0;font-size:2em}.swal2-popup.swal2-toast .swal2-html-container{margin:.5em 1em;padding:0;overflow:initial;font-size:1em;text-align:initial}.swal2-popup.swal2-toast .swal2-html-container:empty{padding:0}.swal2-popup.swal2-toast .swal2-loader{grid-column:1;grid-row:1/99;align-self:center;width:2em;height:2em;margin:.25em}.swal2-popup.swal2-toast .swal2-icon{grid-column:1;grid-row:1/99;align-self:center;width:2em;min-width:2em;height:2em;margin:0 .5em 0 0}.swal2-popup.swal2-toast .swal2-icon .swal2-icon-content{display:flex;align-items:center;font-size:1.8em;font-weight:bold}.swal2-popup.swal2-toast .swal2-icon.swal2-success .swal2-success-ring{width:2em;height:2em}.swal2-popup.swal2-toast .swal2-icon.swal2-error [class^=swal2-x-mark-line]{top:.875em;width:1.375em}.swal2-popup.swal2-toast .swal2-icon.swal2-error [class^=swal2-x-mark-line][class$=left]{left:.3125em}.swal2-popup.swal2-toast .swal2-icon.swal2-error [class^=swal2-x-mark-line][class$=right]{right:.3125em}.swal2-popup.swal2-toast .swal2-actions{justify-content:flex-start;height:auto;margin:0;margin-top:.5em;padding:0 .5em}.swal2-popup.swal2-toast .swal2-styled{margin:.25em .5em;padding:.4em .6em;font-size:1em}.swal2-popup.swal2-toast .swal2-success{border-color:#a5dc86}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-circular-line]{position:absolute;width:1.6em;height:3em;border-radius:50%}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-circular-line][class$=left]{top:-0.8em;left:-0.5em;transform:rotate(-45deg);transform-origin:2em 2em;border-radius:4em 0 0 4em}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-circular-line][class$=right]{top:-0.25em;left:.9375em;transform-origin:0 1.5em;border-radius:0 4em 4em 0}.swal2-popup.swal2-toast .swal2-success .swal2-success-ring{width:2em;height:2em}.swal2-popup.swal2-toast .swal2-success .swal2-success-fix{top:0;left:.4375em;width:.4375em;height:2.6875em}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-line]{height:.3125em}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-line][class$=tip]{top:1.125em;left:.1875em;width:.75em}.swal2-popup.swal2-toast .swal2-success [class^=swal2-success-line][class$=long]{top:.9375em;right:.1875em;width:1.375em}.swal2-popup.swal2-toast .swal2-success.swal2-icon-show .swal2-success-line-tip{animation:swal2-toast-animate-success-line-tip .75s}.swal2-popup.swal2-toast .swal2-success.swal2-icon-show .swal2-success-line-long{animation:swal2-toast-animate-success-line-long .75s}.swal2-popup.swal2-toast.swal2-show{animation:swal2-toast-show .5s}.swal2-popup.swal2-toast.swal2-hide{animation:swal2-toast-hide .1s forwards}div:where(.swal2-container){display:grid;position:fixed;z-index:1060;inset:0;box-sizing:border-box;grid-template-areas:\"top-start     top            top-end\" \"center-start  center         center-end\" \"bottom-start  bottom-center  bottom-end\";grid-template-rows:minmax(min-content, auto) minmax(min-content, auto) minmax(min-content, auto);height:100%;padding:.625em;overflow-x:hidden;transition:background-color .1s;-webkit-overflow-scrolling:touch}div:where(.swal2-container).swal2-backdrop-show,div:where(.swal2-container).swal2-noanimation{background:rgba(0,0,0,.4)}div:where(.swal2-container).swal2-backdrop-hide{background:rgba(0,0,0,0) !important}div:where(.swal2-container).swal2-top-start,div:where(.swal2-container).swal2-center-start,div:where(.swal2-container).swal2-bottom-start{grid-template-columns:minmax(0, 1fr) auto auto}div:where(.swal2-container).swal2-top,div:where(.swal2-container).swal2-center,div:where(.swal2-container).swal2-bottom{grid-template-columns:auto minmax(0, 1fr) auto}div:where(.swal2-container).swal2-top-end,div:where(.swal2-container).swal2-center-end,div:where(.swal2-container).swal2-bottom-end{grid-template-columns:auto auto minmax(0, 1fr)}div:where(.swal2-container).swal2-top-start>.swal2-popup{align-self:start}div:where(.swal2-container).swal2-top>.swal2-popup{grid-column:2;place-self:start center}div:where(.swal2-container).swal2-top-end>.swal2-popup,div:where(.swal2-container).swal2-top-right>.swal2-popup{grid-column:3;place-self:start end}div:where(.swal2-container).swal2-center-start>.swal2-popup,div:where(.swal2-container).swal2-center-left>.swal2-popup{grid-row:2;align-self:center}div:where(.swal2-container).swal2-center>.swal2-popup{grid-column:2;grid-row:2;place-self:center center}div:where(.swal2-container).swal2-center-end>.swal2-popup,div:where(.swal2-container).swal2-center-right>.swal2-popup{grid-column:3;grid-row:2;place-self:center end}div:where(.swal2-container).swal2-bottom-start>.swal2-popup,div:where(.swal2-container).swal2-bottom-left>.swal2-popup{grid-column:1;grid-row:3;align-self:end}div:where(.swal2-container).swal2-bottom>.swal2-popup{grid-column:2;grid-row:3;place-self:end center}div:where(.swal2-container).swal2-bottom-end>.swal2-popup,div:where(.swal2-container).swal2-bottom-right>.swal2-popup{grid-column:3;grid-row:3;place-self:end end}div:where(.swal2-container).swal2-grow-row>.swal2-popup,div:where(.swal2-container).swal2-grow-fullscreen>.swal2-popup{grid-column:1/4;width:100%}div:where(.swal2-container).swal2-grow-column>.swal2-popup,div:where(.swal2-container).swal2-grow-fullscreen>.swal2-popup{grid-row:1/4;align-self:stretch}div:where(.swal2-container).swal2-no-transition{transition:none !important}div:where(.swal2-container) div:where(.swal2-popup){display:none;position:relative;box-sizing:border-box;grid-template-columns:minmax(0, 100%);width:32em;max-width:100%;padding:0 0 1.25em;border:none;border-radius:5px;background:#fff;color:#545454;font-family:inherit;font-size:1rem}div:where(.swal2-container) div:where(.swal2-popup):focus{outline:none}div:where(.swal2-container) div:where(.swal2-popup).swal2-loading{overflow-y:hidden}div:where(.swal2-container) h2:where(.swal2-title){position:relative;max-width:100%;margin:0;padding:.8em 1em 0;color:inherit;font-size:1.875em;font-weight:600;text-align:center;text-transform:none;word-wrap:break-word}div:where(.swal2-container) div:where(.swal2-actions){display:flex;z-index:1;box-sizing:border-box;flex-wrap:wrap;align-items:center;justify-content:center;width:auto;margin:1.25em auto 0;padding:0}div:where(.swal2-container) div:where(.swal2-actions):not(.swal2-loading) .swal2-styled[disabled]{opacity:.4}div:where(.swal2-container) div:where(.swal2-actions):not(.swal2-loading) .swal2-styled:hover{background-image:linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.1))}div:where(.swal2-container) div:where(.swal2-actions):not(.swal2-loading) .swal2-styled:active{background-image:linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2))}div:where(.swal2-container) div:where(.swal2-loader){display:none;align-items:center;justify-content:center;width:2.2em;height:2.2em;margin:0 1.875em;animation:swal2-rotate-loading 1.5s linear 0s infinite normal;border-width:.25em;border-style:solid;border-radius:100%;border-color:#2778c4 rgba(0,0,0,0) #2778c4 rgba(0,0,0,0)}div:where(.swal2-container) button:where(.swal2-styled){margin:.3125em;padding:.625em 1.1em;transition:box-shadow .1s;box-shadow:0 0 0 3px rgba(0,0,0,0);font-weight:500}div:where(.swal2-container) button:where(.swal2-styled):not([disabled]){cursor:pointer}div:where(.swal2-container) button:where(.swal2-styled).swal2-confirm{border:0;border-radius:.25em;background:initial;background-color:#7066e0;color:#fff;font-size:1em}div:where(.swal2-container) button:where(.swal2-styled).swal2-confirm:focus{box-shadow:0 0 0 3px rgba(112,102,224,.5)}div:where(.swal2-container) button:where(.swal2-styled).swal2-deny{border:0;border-radius:.25em;background:initial;background-color:#dc3741;color:#fff;font-size:1em}div:where(.swal2-container) button:where(.swal2-styled).swal2-deny:focus{box-shadow:0 0 0 3px rgba(220,55,65,.5)}div:where(.swal2-container) button:where(.swal2-styled).swal2-cancel{border:0;border-radius:.25em;background:initial;background-color:#6e7881;color:#fff;font-size:1em}div:where(.swal2-container) button:where(.swal2-styled).swal2-cancel:focus{box-shadow:0 0 0 3px rgba(110,120,129,.5)}div:where(.swal2-container) button:where(.swal2-styled).swal2-default-outline:focus{box-shadow:0 0 0 3px rgba(100,150,200,.5)}div:where(.swal2-container) button:where(.swal2-styled):focus{outline:none}div:where(.swal2-container) button:where(.swal2-styled)::-moz-focus-inner{border:0}div:where(.swal2-container) div:where(.swal2-footer){margin:1em 0 0;padding:1em 1em 0;border-top:1px solid #eee;color:inherit;font-size:1em;text-align:center}div:where(.swal2-container) .swal2-timer-progress-bar-container{position:absolute;right:0;bottom:0;left:0;grid-column:auto !important;overflow:hidden;border-bottom-right-radius:5px;border-bottom-left-radius:5px}div:where(.swal2-container) div:where(.swal2-timer-progress-bar){width:100%;height:.25em;background:rgba(0,0,0,.2)}div:where(.swal2-container) img:where(.swal2-image){max-width:100%;margin:2em auto 1em}div:where(.swal2-container) button:where(.swal2-close){z-index:2;align-items:center;justify-content:center;width:1.2em;height:1.2em;margin-top:0;margin-right:0;margin-bottom:-1.2em;padding:0;overflow:hidden;transition:color .1s,box-shadow .1s;border:none;border-radius:5px;background:rgba(0,0,0,0);color:#ccc;font-family:monospace;font-size:2.5em;cursor:pointer;justify-self:end}div:where(.swal2-container) button:where(.swal2-close):hover{transform:none;background:rgba(0,0,0,0);color:#f27474}div:where(.swal2-container) button:where(.swal2-close):focus{outline:none;box-shadow:inset 0 0 0 3px rgba(100,150,200,.5)}div:where(.swal2-container) button:where(.swal2-close)::-moz-focus-inner{border:0}div:where(.swal2-container) .swal2-html-container{z-index:1;justify-content:center;margin:1em 1.6em .3em;padding:0;overflow:auto;color:inherit;font-size:1.125em;font-weight:normal;line-height:normal;text-align:center;word-wrap:break-word;word-break:break-word}div:where(.swal2-container) input:where(.swal2-input),div:where(.swal2-container) input:where(.swal2-file),div:where(.swal2-container) textarea:where(.swal2-textarea),div:where(.swal2-container) select:where(.swal2-select),div:where(.swal2-container) div:where(.swal2-radio),div:where(.swal2-container) label:where(.swal2-checkbox){margin:1em 2em 3px}div:where(.swal2-container) input:where(.swal2-input),div:where(.swal2-container) input:where(.swal2-file),div:where(.swal2-container) textarea:where(.swal2-textarea){box-sizing:border-box;width:auto;transition:border-color .1s,box-shadow .1s;border:1px solid #d9d9d9;border-radius:.1875em;background:rgba(0,0,0,0);box-shadow:inset 0 1px 1px rgba(0,0,0,.06),0 0 0 3px rgba(0,0,0,0);color:inherit;font-size:1.125em}div:where(.swal2-container) input:where(.swal2-input).swal2-inputerror,div:where(.swal2-container) input:where(.swal2-file).swal2-inputerror,div:where(.swal2-container) textarea:where(.swal2-textarea).swal2-inputerror{border-color:#f27474 !important;box-shadow:0 0 2px #f27474 !important}div:where(.swal2-container) input:where(.swal2-input):focus,div:where(.swal2-container) input:where(.swal2-file):focus,div:where(.swal2-container) textarea:where(.swal2-textarea):focus{border:1px solid #b4dbed;outline:none;box-shadow:inset 0 1px 1px rgba(0,0,0,.06),0 0 0 3px rgba(100,150,200,.5)}div:where(.swal2-container) input:where(.swal2-input)::placeholder,div:where(.swal2-container) input:where(.swal2-file)::placeholder,div:where(.swal2-container) textarea:where(.swal2-textarea)::placeholder{color:#ccc}div:where(.swal2-container) .swal2-range{margin:1em 2em 3px;background:#fff}div:where(.swal2-container) .swal2-range input{width:80%}div:where(.swal2-container) .swal2-range output{width:20%;color:inherit;font-weight:600;text-align:center}div:where(.swal2-container) .swal2-range input,div:where(.swal2-container) .swal2-range output{height:2.625em;padding:0;font-size:1.125em;line-height:2.625em}div:where(.swal2-container) .swal2-input{height:2.625em;padding:0 .75em}div:where(.swal2-container) .swal2-file{width:75%;margin-right:auto;margin-left:auto;background:rgba(0,0,0,0);font-size:1.125em}div:where(.swal2-container) .swal2-textarea{height:6.75em;padding:.75em}div:where(.swal2-container) .swal2-select{min-width:50%;max-width:100%;padding:.375em .625em;background:rgba(0,0,0,0);color:inherit;font-size:1.125em}div:where(.swal2-container) .swal2-radio,div:where(.swal2-container) .swal2-checkbox{align-items:center;justify-content:center;background:#fff;color:inherit}div:where(.swal2-container) .swal2-radio label,div:where(.swal2-container) .swal2-checkbox label{margin:0 .6em;font-size:1.125em}div:where(.swal2-container) .swal2-radio input,div:where(.swal2-container) .swal2-checkbox input{flex-shrink:0;margin:0 .4em}div:where(.swal2-container) label:where(.swal2-input-label){display:flex;justify-content:center;margin:1em auto 0}div:where(.swal2-container) div:where(.swal2-validation-message){align-items:center;justify-content:center;margin:1em 0 0;padding:.625em;overflow:hidden;background:#f0f0f0;color:#666;font-size:1em;font-weight:300}div:where(.swal2-container) div:where(.swal2-validation-message)::before{content:\"!\";display:inline-block;width:1.5em;min-width:1.5em;height:1.5em;margin:0 .625em;border-radius:50%;background-color:#f27474;color:#fff;font-weight:600;line-height:1.5em;text-align:center}div:where(.swal2-container) .swal2-progress-steps{flex-wrap:wrap;align-items:center;max-width:100%;margin:1.25em auto;padding:0;background:rgba(0,0,0,0);font-weight:600}div:where(.swal2-container) .swal2-progress-steps li{display:inline-block;position:relative}div:where(.swal2-container) .swal2-progress-steps .swal2-progress-step{z-index:20;flex-shrink:0;width:2em;height:2em;border-radius:2em;background:#2778c4;color:#fff;line-height:2em;text-align:center}div:where(.swal2-container) .swal2-progress-steps .swal2-progress-step.swal2-active-progress-step{background:#2778c4}div:where(.swal2-container) .swal2-progress-steps .swal2-progress-step.swal2-active-progress-step~.swal2-progress-step{background:#add8e6;color:#fff}div:where(.swal2-container) .swal2-progress-steps .swal2-progress-step.swal2-active-progress-step~.swal2-progress-step-line{background:#add8e6}div:where(.swal2-container) .swal2-progress-steps .swal2-progress-step-line{z-index:10;flex-shrink:0;width:2.5em;height:.4em;margin:0 -1px;background:#2778c4}div:where(.swal2-icon){position:relative;box-sizing:content-box;justify-content:center;width:5em;height:5em;margin:2.5em auto .6em;border:0.25em solid rgba(0,0,0,0);border-radius:50%;border-color:#000;font-family:inherit;line-height:5em;cursor:default;user-select:none}div:where(.swal2-icon) .swal2-icon-content{display:flex;align-items:center;font-size:3.75em}div:where(.swal2-icon).swal2-error{border-color:#f27474;color:#f27474}div:where(.swal2-icon).swal2-error .swal2-x-mark{position:relative;flex-grow:1}div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line]{display:block;position:absolute;top:2.3125em;width:2.9375em;height:.3125em;border-radius:.125em;background-color:#f27474}div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line][class$=left]{left:1.0625em;transform:rotate(45deg)}div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line][class$=right]{right:1em;transform:rotate(-45deg)}div:where(.swal2-icon).swal2-error.swal2-icon-show{animation:swal2-animate-error-icon .5s}div:where(.swal2-icon).swal2-error.swal2-icon-show .swal2-x-mark{animation:swal2-animate-error-x-mark .5s}div:where(.swal2-icon).swal2-warning{border-color:#facea8;color:#f8bb86}div:where(.swal2-icon).swal2-warning.swal2-icon-show{animation:swal2-animate-error-icon .5s}div:where(.swal2-icon).swal2-warning.swal2-icon-show .swal2-icon-content{animation:swal2-animate-i-mark .5s}div:where(.swal2-icon).swal2-info{border-color:#9de0f6;color:#3fc3ee}div:where(.swal2-icon).swal2-info.swal2-icon-show{animation:swal2-animate-error-icon .5s}div:where(.swal2-icon).swal2-info.swal2-icon-show .swal2-icon-content{animation:swal2-animate-i-mark .8s}div:where(.swal2-icon).swal2-question{border-color:#c9dae1;color:#87adbd}div:where(.swal2-icon).swal2-question.swal2-icon-show{animation:swal2-animate-error-icon .5s}div:where(.swal2-icon).swal2-question.swal2-icon-show .swal2-icon-content{animation:swal2-animate-question-mark .8s}div:where(.swal2-icon).swal2-success{border-color:#a5dc86;color:#a5dc86}div:where(.swal2-icon).swal2-success [class^=swal2-success-circular-line]{position:absolute;width:3.75em;height:7.5em;border-radius:50%}div:where(.swal2-icon).swal2-success [class^=swal2-success-circular-line][class$=left]{top:-0.4375em;left:-2.0635em;transform:rotate(-45deg);transform-origin:3.75em 3.75em;border-radius:7.5em 0 0 7.5em}div:where(.swal2-icon).swal2-success [class^=swal2-success-circular-line][class$=right]{top:-0.6875em;left:1.875em;transform:rotate(-45deg);transform-origin:0 3.75em;border-radius:0 7.5em 7.5em 0}div:where(.swal2-icon).swal2-success .swal2-success-ring{position:absolute;z-index:2;top:-0.25em;left:-0.25em;box-sizing:content-box;width:100%;height:100%;border:.25em solid rgba(165,220,134,.3);border-radius:50%}div:where(.swal2-icon).swal2-success .swal2-success-fix{position:absolute;z-index:1;top:.5em;left:1.625em;width:.4375em;height:5.625em;transform:rotate(-45deg)}div:where(.swal2-icon).swal2-success [class^=swal2-success-line]{display:block;position:absolute;z-index:2;height:.3125em;border-radius:.125em;background-color:#a5dc86}div:where(.swal2-icon).swal2-success [class^=swal2-success-line][class$=tip]{top:2.875em;left:.8125em;width:1.5625em;transform:rotate(45deg)}div:where(.swal2-icon).swal2-success [class^=swal2-success-line][class$=long]{top:2.375em;right:.5em;width:2.9375em;transform:rotate(-45deg)}div:where(.swal2-icon).swal2-success.swal2-icon-show .swal2-success-line-tip{animation:swal2-animate-success-line-tip .75s}div:where(.swal2-icon).swal2-success.swal2-icon-show .swal2-success-line-long{animation:swal2-animate-success-line-long .75s}div:where(.swal2-icon).swal2-success.swal2-icon-show .swal2-success-circular-line-right{animation:swal2-rotate-success-circular-line 4.25s ease-in}[class^=swal2]{-webkit-tap-highlight-color:rgba(0,0,0,0)}.swal2-show{animation:swal2-show .3s}.swal2-hide{animation:swal2-hide .15s forwards}.swal2-noanimation{transition:none}.swal2-scrollbar-measure{position:absolute;top:-9999px;width:50px;height:50px;overflow:scroll}.swal2-rtl .swal2-close{margin-right:initial;margin-left:0}.swal2-rtl .swal2-timer-progress-bar{right:0;left:auto}@keyframes swal2-toast-show{0%{transform:translateY(-0.625em) rotateZ(2deg)}33%{transform:translateY(0) rotateZ(-2deg)}66%{transform:translateY(0.3125em) rotateZ(2deg)}100%{transform:translateY(0) rotateZ(0deg)}}@keyframes swal2-toast-hide{100%{transform:rotateZ(1deg);opacity:0}}@keyframes swal2-toast-animate-success-line-tip{0%{top:.5625em;left:.0625em;width:0}54%{top:.125em;left:.125em;width:0}70%{top:.625em;left:-0.25em;width:1.625em}84%{top:1.0625em;left:.75em;width:.5em}100%{top:1.125em;left:.1875em;width:.75em}}@keyframes swal2-toast-animate-success-line-long{0%{top:1.625em;right:1.375em;width:0}65%{top:1.25em;right:.9375em;width:0}84%{top:.9375em;right:0;width:1.125em}100%{top:.9375em;right:.1875em;width:1.375em}}@keyframes swal2-show{0%{transform:scale(0.7)}45%{transform:scale(1.05)}80%{transform:scale(0.95)}100%{transform:scale(1)}}@keyframes swal2-hide{0%{transform:scale(1);opacity:1}100%{transform:scale(0.5);opacity:0}}@keyframes swal2-animate-success-line-tip{0%{top:1.1875em;left:.0625em;width:0}54%{top:1.0625em;left:.125em;width:0}70%{top:2.1875em;left:-0.375em;width:3.125em}84%{top:3em;left:1.3125em;width:1.0625em}100%{top:2.8125em;left:.8125em;width:1.5625em}}@keyframes swal2-animate-success-line-long{0%{top:3.375em;right:2.875em;width:0}65%{top:3.375em;right:2.875em;width:0}84%{top:2.1875em;right:0;width:3.4375em}100%{top:2.375em;right:.5em;width:2.9375em}}@keyframes swal2-rotate-success-circular-line{0%{transform:rotate(-45deg)}5%{transform:rotate(-45deg)}12%{transform:rotate(-405deg)}100%{transform:rotate(-405deg)}}@keyframes swal2-animate-error-x-mark{0%{margin-top:1.625em;transform:scale(0.4);opacity:0}50%{margin-top:1.625em;transform:scale(0.4);opacity:0}80%{margin-top:-0.375em;transform:scale(1.15)}100%{margin-top:0;transform:scale(1);opacity:1}}@keyframes swal2-animate-error-icon{0%{transform:rotateX(100deg);opacity:0}100%{transform:rotateX(0deg);opacity:1}}@keyframes swal2-rotate-loading{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}@keyframes swal2-animate-question-mark{0%{transform:rotateY(-360deg)}100%{transform:rotateY(0)}}@keyframes swal2-animate-i-mark{0%{transform:rotateZ(45deg);opacity:0}25%{transform:rotateZ(-25deg);opacity:.4}50%{transform:rotateZ(15deg);opacity:.8}75%{transform:rotateZ(-5deg);opacity:1}100%{transform:rotateX(0);opacity:1}}body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown){overflow:hidden}body.swal2-height-auto{height:auto !important}body.swal2-no-backdrop .swal2-container{background-color:rgba(0,0,0,0) !important;pointer-events:none}body.swal2-no-backdrop .swal2-container .swal2-popup{pointer-events:all}body.swal2-no-backdrop .swal2-container .swal2-modal{box-shadow:0 0 10px rgba(0,0,0,.4)}@media print{body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown){overflow-y:scroll !important}body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown)>[aria-hidden=true]{display:none}body.swal2-shown:not(.swal2-no-backdrop):not(.swal2-toast-shown) .swal2-container{position:static !important}}body.swal2-toast-shown .swal2-container{box-sizing:border-box;width:360px;max-width:100%;background-color:rgba(0,0,0,0);pointer-events:none}body.swal2-toast-shown .swal2-container.swal2-top{inset:0 auto auto 50%;transform:translateX(-50%)}body.swal2-toast-shown .swal2-container.swal2-top-end,body.swal2-toast-shown .swal2-container.swal2-top-right{inset:0 0 auto auto}body.swal2-toast-shown .swal2-container.swal2-top-start,body.swal2-toast-shown .swal2-container.swal2-top-left{inset:0 auto auto 0}body.swal2-toast-shown .swal2-container.swal2-center-start,body.swal2-toast-shown .swal2-container.swal2-center-left{inset:50% auto auto 0;transform:translateY(-50%)}body.swal2-toast-shown .swal2-container.swal2-center{inset:50% auto auto 50%;transform:translate(-50%, -50%)}body.swal2-toast-shown .swal2-container.swal2-center-end,body.swal2-toast-shown .swal2-container.swal2-center-right{inset:50% 0 auto auto;transform:translateY(-50%)}body.swal2-toast-shown .swal2-container.swal2-bottom-start,body.swal2-toast-shown .swal2-container.swal2-bottom-left{inset:auto auto 0 0}body.swal2-toast-shown .swal2-container.swal2-bottom{inset:auto auto 0 50%;transform:translateX(-50%)}body.swal2-toast-shown .swal2-container.swal2-bottom-end,body.swal2-toast-shown .swal2-container.swal2-bottom-right{inset:auto 0 0 auto}");
