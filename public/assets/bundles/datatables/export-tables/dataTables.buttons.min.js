/*!
 Buttons for DataTables 1.5.2
 ©2016-2018 SpryMedia Ltd - datatables.net/license
*/
(function (d) { "function" === typeof define && define.amd ? define(["jquery", "datatables.net"], function (n) { return d(n, window, document) }) : "object" === typeof exports ? module.exports = function (n, o) { n || (n = window); if (!o || !o.fn.dataTable) o = require("datatables.net")(n, o).$; return d(o, n, n.document) } : d(jQuery, window, document) })(function (d, n, o, m) {
    var i = d.fn.dataTable, x = 0, y = 0, k = i.ext.buttons, l = function (a, b) {
        "undefined" === typeof b && (b = {}); !0 === b && (b = {}); d.isArray(b) && (b = { buttons: b }); this.c = d.extend(!0, {}, l.defaults, b);
        b.buttons && (this.c.buttons = b.buttons); this.s = { dt: new i.Api(a), buttons: [], listenKeys: "", namespace: "dtb" + x++ }; this.dom = { container: d("<" + this.c.dom.container.tag + "/>").addClass(this.c.dom.container.className) }; this._constructor()
    }; d.extend(l.prototype, {
        action: function (a, b) { var c = this._nodeToButton(a); if (b === m) return c.conf.action; c.conf.action = b; return this }, active: function (a, b) {
            var c = this._nodeToButton(a), e = this.c.dom.button.active, c = d(c.node); if (b === m) return c.hasClass(e); c.toggleClass(e, b === m ? !0 :
                b); return this
        }, add: function (a, b) { var c = this.s.buttons; if ("string" === typeof b) { for (var e = b.split("-"), c = this.s, d = 0, h = e.length - 1; d < h; d++)c = c.buttons[1 * e[d]]; c = c.buttons; b = 1 * e[e.length - 1] } this._expandButton(c, a, !1, b); this._draw(); return this }, container: function () { return this.dom.container }, disable: function (a) { a = this._nodeToButton(a); d(a.node).addClass(this.c.dom.button.disabled); return this }, destroy: function () {
            d("body").off("keyup." + this.s.namespace); var a = this.s.buttons.slice(), b, c; b = 0; for (c = a.length; b <
                c; b++)this.remove(a[b].node); this.dom.container.remove(); a = this.s.dt.settings()[0]; b = 0; for (c = a.length; b < c; b++)if (a.inst === this) { a.splice(b, 1); break } return this
        }, enable: function (a, b) { if (!1 === b) return this.disable(a); var c = this._nodeToButton(a); d(c.node).removeClass(this.c.dom.button.disabled); return this }, name: function () { return this.c.name }, node: function (a) { a = this._nodeToButton(a); return d(a.node) }, processing: function (a, b) {
            var c = this._nodeToButton(a); if (b === m) return d(c.node).hasClass("processing");
            d(c.node).toggleClass("processing", b); return this
        }, remove: function (a) { var b = this._nodeToButton(a), c = this._nodeToHost(a), e = this.s.dt; if (b.buttons.length) for (var g = b.buttons.length - 1; 0 <= g; g--)this.remove(b.buttons[g].node); b.conf.destroy && b.conf.destroy.call(e.button(a), e, d(a), b.conf); this._removeKey(b.conf); d(b.node).remove(); a = d.inArray(b, c); c.splice(a, 1); return this }, text: function (a, b) {
            var c = this._nodeToButton(a), e = this.c.dom.collection.buttonLiner, e = c.inCollection && e && e.tag ? e.tag : this.c.dom.buttonLiner.tag,
                g = this.s.dt, h = d(c.node), f = function (a) { return "function" === typeof a ? a(g, h, c.conf) : a }; if (b === m) return f(c.conf.text); c.conf.text = b; e ? h.children(e).html(f(b)) : h.html(f(b)); return this
        }, _constructor: function () {
            var a = this, b = this.s.dt, c = b.settings()[0], e = this.c.buttons; c._buttons || (c._buttons = []); c._buttons.push({ inst: this, name: this.c.name }); for (var g = 0, h = e.length; g < h; g++)this.add(e[g]); b.on("destroy", function (b, e) { e === c && a.destroy() }); d("body").on("keyup." + this.s.namespace, function (b) {
                if (!o.activeElement ||
                    o.activeElement === o.body) { var c = String.fromCharCode(b.keyCode).toLowerCase(); a.s.listenKeys.toLowerCase().indexOf(c) !== -1 && a._keypress(c, b) }
            })
        }, _addKey: function (a) { a.key && (this.s.listenKeys += d.isPlainObject(a.key) ? a.key.key : a.key) }, _draw: function (a, b) { a || (a = this.dom.container, b = this.s.buttons); a.children().detach(); for (var c = 0, e = b.length; c < e; c++)a.append(b[c].inserter), a.append(" "), b[c].buttons && b[c].buttons.length && this._draw(b[c].collection, b[c].buttons) }, _expandButton: function (a, b, c, e) {
            for (var g =
                this.s.dt, h = 0, b = !d.isArray(b) ? [b] : b, f = 0, q = b.length; f < q; f++) { var j = this._resolveExtends(b[f]); if (j) if (d.isArray(j)) this._expandButton(a, j, c, e); else { var p = this._buildButton(j, c); if (p) { e !== m ? (a.splice(e, 0, p), e++) : a.push(p); if (p.conf.buttons) { var u = this.c.dom.collection; p.collection = d("<" + u.tag + "/>").addClass(u.className).attr("role", "menu"); p.conf._collection = p.collection; this._expandButton(p.buttons, p.conf.buttons, !0, e) } j.init && j.init.call(g.button(p.node), g, d(p.node), j); h++ } } }
        }, _buildButton: function (a,
            b) {
            var c = this.c.dom.button, e = this.c.dom.buttonLiner, g = this.c.dom.collection, h = this.s.dt, f = function (b) { return "function" === typeof b ? b(h, j, a) : b }; b && g.button && (c = g.button); b && g.buttonLiner && (e = g.buttonLiner); if (a.available && !a.available(h, a)) return !1; var q = function (a, b, c, e) { e.action.call(b.button(c), a, b, c, e); d(b.table().node()).triggerHandler("buttons-action.dt", [b.button(c), b, c, e]) }, g = a.tag || c.tag, j = d("<" + g + "/>").addClass(c.className).attr("tabindex", this.s.dt.settings()[0].iTabIndex).attr("aria-controls",
                this.s.dt.table().node().id).on("click.dtb", function (b) { b.preventDefault(); !j.hasClass(c.disabled) && a.action && q(b, h, j, a); j.blur() }).on("keyup.dtb", function (b) { b.keyCode === 13 && !j.hasClass(c.disabled) && a.action && q(b, h, j, a) }); "a" === g.toLowerCase() && j.attr("href", "#"); "button" === g.toLowerCase() && j.attr("type", "button"); e.tag ? (g = d("<" + e.tag + "/>").html(f(a.text)).addClass(e.className), "a" === e.tag.toLowerCase() && g.attr("href", "#"), j.append(g)) : j.html(f(a.text)); !1 === a.enabled && j.addClass(c.disabled); a.className &&
                    j.addClass(a.className); a.titleAttr && j.attr("title", f(a.titleAttr)); a.attr && j.attr(a.attr); a.namespace || (a.namespace = ".dt-button-" + y++); e = (e = this.c.dom.buttonContainer) && e.tag ? d("<" + e.tag + "/>").addClass(e.className).append(j) : j; this._addKey(a); return { conf: a, node: j.get(0), inserter: e, buttons: [], inCollection: b, collection: null }
        }, _nodeToButton: function (a, b) {
            b || (b = this.s.buttons); for (var c = 0, e = b.length; c < e; c++) {
                if (b[c].node === a) return b[c]; if (b[c].buttons.length) {
                    var d = this._nodeToButton(a, b[c].buttons);
                    if (d) return d
                }
            }
        }, _nodeToHost: function (a, b) { b || (b = this.s.buttons); for (var c = 0, e = b.length; c < e; c++) { if (b[c].node === a) return b; if (b[c].buttons.length) { var d = this._nodeToHost(a, b[c].buttons); if (d) return d } } }, _keypress: function (a, b) {
            if (!b._buttonsHandled) {
                var c = function (e) {
                    for (var g = 0, h = e.length; g < h; g++) {
                        var f = e[g].conf, q = e[g].node; if (f.key) if (f.key === a) b._buttonsHandled = !0, d(q).click(); else if (d.isPlainObject(f.key) && f.key.key === a && (!f.key.shiftKey || b.shiftKey)) if (!f.key.altKey || b.altKey) if (!f.key.ctrlKey ||
                            b.ctrlKey) if (!f.key.metaKey || b.metaKey) b._buttonsHandled = !0, d(q).click(); e[g].buttons.length && c(e[g].buttons)
                    }
                }; c(this.s.buttons)
            }
        }, _removeKey: function (a) { if (a.key) { var b = d.isPlainObject(a.key) ? a.key.key : a.key, a = this.s.listenKeys.split(""), b = d.inArray(b, a); a.splice(b, 1); this.s.listenKeys = a.join("") } }, _resolveExtends: function (a) {
            for (var b = this.s.dt, c, e, g = function (c) {
                for (var e = 0; !d.isPlainObject(c) && !d.isArray(c);) {
                    if (c === m) return; if ("function" === typeof c) { if (c = c(b, a), !c) return !1 } else if ("string" ===
                        typeof c) { if (!k[c]) throw "Unknown button type: " + c; c = k[c] } e++; if (30 < e) throw "Buttons: Too many iterations";
                } return d.isArray(c) ? c : d.extend({}, c)
            }, a = g(a); a && a.extend;) {
                if (!k[a.extend]) throw "Cannot extend unknown button type: " + a.extend; var h = g(k[a.extend]); if (d.isArray(h)) return h; if (!h) return !1; c = h.className; a = d.extend({}, h, a); c && a.className !== c && (a.className = c + " " + a.className); var f = a.postfixButtons; if (f) { a.buttons || (a.buttons = []); c = 0; for (e = f.length; c < e; c++)a.buttons.push(f[c]); a.postfixButtons = null } if (f =
                    a.prefixButtons) { a.buttons || (a.buttons = []); c = 0; for (e = f.length; c < e; c++)a.buttons.splice(c, 0, f[c]); a.prefixButtons = null } a.extend = h.extend
            } return a
        }
    }); l.background = function (a, b, c) { c === m && (c = 400); a ? d("<div/>").addClass(b).css("display", "none").appendTo("body").fadeIn(c) : d("body > div." + b).fadeOut(c, function () { d(this).removeClass(b).remove() }) }; l.instanceSelector = function (a, b) {
        if (!a) return d.map(b, function (a) { return a.inst }); var c = [], e = d.map(b, function (a) { return a.name }), g = function (a) {
            if (d.isArray(a)) for (var f =
                0, q = a.length; f < q; f++)g(a[f]); else "string" === typeof a ? -1 !== a.indexOf(",") ? g(a.split(",")) : (a = d.inArray(d.trim(a), e), -1 !== a && c.push(b[a].inst)) : "number" === typeof a && c.push(b[a].inst)
        }; g(a); return c
    }; l.buttonSelector = function (a, b) {
        for (var c = [], e = function (a, b, c) { for (var d, g, f = 0, h = b.length; f < h; f++)if (d = b[f]) g = c !== m ? c + f : f + "", a.push({ node: d.node, name: d.conf.name, idx: g }), d.buttons && e(a, d.buttons, g + "-") }, g = function (a, b) {
            var f, h, i = []; e(i, b.s.buttons); f = d.map(i, function (a) { return a.node }); if (d.isArray(a) || a instanceof
                d) { f = 0; for (h = a.length; f < h; f++)g(a[f], b) } else if (null === a || a === m || "*" === a) { f = 0; for (h = i.length; f < h; f++)c.push({ inst: b, node: i[f].node }) } else if ("number" === typeof a) c.push({ inst: b, node: b.s.buttons[a].node }); else if ("string" === typeof a) if (-1 !== a.indexOf(",")) { i = a.split(","); f = 0; for (h = i.length; f < h; f++)g(d.trim(i[f]), b) } else if (a.match(/^\d+(\-\d+)*$/)) f = d.map(i, function (a) { return a.idx }), c.push({ inst: b, node: i[d.inArray(a, f)].node }); else if (-1 !== a.indexOf(":name")) {
                    var k = a.replace(":name", ""); f = 0; for (h =
                        i.length; f < h; f++)i[f].name === k && c.push({ inst: b, node: i[f].node })
                } else d(f).filter(a).each(function () { c.push({ inst: b, node: this }) }); else "object" === typeof a && a.nodeName && (i = d.inArray(a, f), -1 !== i && c.push({ inst: b, node: f[i] }))
        }, h = 0, f = a.length; h < f; h++)g(b, a[h]); return c
    }; l.defaults = {
        buttons: ["copy", "excel", "csv", "pdf", "print"], name: "main", tabIndex: 0, dom: {
            container: { tag: "div", className: "dt-buttons" }, collection: { tag: "div", className: "dt-button-collection" }, button: {
                tag: "button", className: "dt-button", active: "active",
                disabled: "disabled"
            }, buttonLiner: { tag: "span", className: "" }
        }
    }; l.version = "1.5.2"; d.extend(k, {
        collection: {
            text: function (a) { return a.i18n("buttons.collection", "Collection") }, className: "buttons-collection", action: function (a, b, c, e) {
                var g = d(c).parents("div.dt-button-collection"), a = c.position(), h = d(b.table().container()), f = !1, i = c; g.length && (f = d(".dt-button-collection").position(), i = g, d("body").trigger("click.dtb-collection")); i.parents("body")[0] !== o.body && (i = o.body.lastChild); e._collection.addClass(e.collectionLayout).css("display",
                    "none").insertAfter(i).fadeIn(e.fade); g = e._collection.css("position"); f && "absolute" === g ? e._collection.css({ top: f.top, left: f.left }) : "absolute" === g ? (e._collection.css({ top: a.top + c.outerHeight(), left: a.left }), f = h.offset().top + h.height(), f = a.top + c.outerHeight() + e._collection.outerHeight() - f, g = a.top - e._collection.outerHeight(), g = h.offset().top - g, (f > g || e.dropup) && e._collection.css("top", a.top - e._collection.outerHeight() - 5), f = a.left + e._collection.outerWidth(), h = h.offset().left + h.width(), f > h && e._collection.css("left",
                        a.left - (f - h)), c = c.offset().left + e._collection.outerWidth(), c > d(n).width() && e._collection.css("left", a.left - (c - d(n).width()))) : (c = e._collection.height() / 2, c > d(n).height() / 2 && (c = d(n).height() / 2), e._collection.css("marginTop", -1 * c)); e.background && l.background(!0, e.backgroundClassName, e.fade); var j = function () {
                            e._collection.fadeOut(e.fade, function () { e._collection.detach() }); d("div.dt-button-background").off("click.dtb-collection"); l.background(false, e.backgroundClassName, e.fade); d("body").off(".dtb-collection");
                            b.off("buttons-action.b-internal")
                        }; setTimeout(function () { d("div.dt-button-background").on("click.dtb-collection", function () { }); d("body").on("click.dtb-collection", function (a) { var b = d.fn.addBack ? "addBack" : "andSelf"; d(a.target).parents()[b]().filter(e._collection).length || j() }).on("keyup.dtb-collection", function (a) { a.keyCode === 27 && j() }); if (e.autoClose) b.on("buttons-action.b-internal", function () { j() }) }, 10)
            }, background: !0, collectionLayout: "", backgroundClassName: "dt-button-background", autoClose: !1, fade: 400,
            attr: { "aria-haspopup": !0 }
        }, copy: function (a, b) { if (k.copyHtml5) return "copyHtml5"; if (k.copyFlash && k.copyFlash.available(a, b)) return "copyFlash" }, csv: function (a, b) { if (k.csvHtml5 && k.csvHtml5.available(a, b)) return "csvHtml5"; if (k.csvFlash && k.csvFlash.available(a, b)) return "csvFlash" }, excel: function (a, b) { if (k.excelHtml5 && k.excelHtml5.available(a, b)) return "excelHtml5"; if (k.excelFlash && k.excelFlash.available(a, b)) return "excelFlash" }, pdf: function (a, b) {
            if (k.pdfHtml5 && k.pdfHtml5.available(a, b)) return "pdfHtml5";
            if (k.pdfFlash && k.pdfFlash.available(a, b)) return "pdfFlash"
        }, pageLength: function (a) {
            var a = a.settings()[0].aLengthMenu, b = d.isArray(a[0]) ? a[0] : a, c = d.isArray(a[0]) ? a[1] : a, e = function (a) { return a.i18n("buttons.pageLength", { "-1": "Show all rows", _: "Show %d rows" }, a.page.len()) }; return {
                extend: "collection", text: e, className: "buttons-page-length", autoClose: !0, buttons: d.map(b, function (a, b) {
                    return {
                        text: c[b], className: "button-page-length", action: function (b, c) { c.page.len(a).draw() }, init: function (b, c, e) {
                            var d = this,
                                c = function () { d.active(b.page.len() === a) }; b.on("length.dt" + e.namespace, c); c()
                        }, destroy: function (a, b, c) { a.off("length.dt" + c.namespace) }
                    }
                }), init: function (a, b, c) { var d = this; a.on("length.dt" + c.namespace, function () { d.text(e(a)) }) }, destroy: function (a, b, c) { a.off("length.dt" + c.namespace) }
            }
        }
    }); i.Api.register("buttons()", function (a, b) {
        b === m && (b = a, a = m); this.selector.buttonGroup = a; var c = this.iterator(!0, "table", function (c) { if (c._buttons) return l.buttonSelector(l.instanceSelector(a, c._buttons), b) }, !0); c._groupSelector =
            a; return c
    }); i.Api.register("button()", function (a, b) { var c = this.buttons(a, b); 1 < c.length && c.splice(1, c.length); return c }); i.Api.registerPlural("buttons().active()", "button().active()", function (a) { return a === m ? this.map(function (a) { return a.inst.active(a.node) }) : this.each(function (b) { b.inst.active(b.node, a) }) }); i.Api.registerPlural("buttons().action()", "button().action()", function (a) { return a === m ? this.map(function (a) { return a.inst.action(a.node) }) : this.each(function (b) { b.inst.action(b.node, a) }) }); i.Api.register(["buttons().enable()",
        "button().enable()"], function (a) { return this.each(function (b) { b.inst.enable(b.node, a) }) }); i.Api.register(["buttons().disable()", "button().disable()"], function () { return this.each(function (a) { a.inst.disable(a.node) }) }); i.Api.registerPlural("buttons().nodes()", "button().node()", function () { var a = d(); d(this.each(function (b) { a = a.add(b.inst.node(b.node)) })); return a }); i.Api.registerPlural("buttons().processing()", "button().processing()", function (a) {
            return a === m ? this.map(function (a) { return a.inst.processing(a.node) }) :
                this.each(function (b) { b.inst.processing(b.node, a) })
        }); i.Api.registerPlural("buttons().text()", "button().text()", function (a) { return a === m ? this.map(function (a) { return a.inst.text(a.node) }) : this.each(function (b) { b.inst.text(b.node, a) }) }); i.Api.registerPlural("buttons().trigger()", "button().trigger()", function () { return this.each(function (a) { a.inst.node(a.node).trigger("click") }) }); i.Api.registerPlural("buttons().containers()", "buttons().container()", function () {
            var a = d(), b = this._groupSelector; this.iterator(!0,
                "table", function (c) { if (c._buttons) for (var c = l.instanceSelector(b, c._buttons), d = 0, g = c.length; d < g; d++)a = a.add(c[d].container()) }); return a
        }); i.Api.register("button().add()", function (a, b) { var c = this.context; c.length && (c = l.instanceSelector(this._groupSelector, c[0]._buttons), c.length && c[0].add(b, a)); return this.button(this._groupSelector, a) }); i.Api.register("buttons().destroy()", function () { this.pluck("inst").unique().each(function (a) { a.destroy() }); return this }); i.Api.registerPlural("buttons().remove()",
            "buttons().remove()", function () { this.each(function (a) { a.inst.remove(a.node) }); return this }); var r; i.Api.register("buttons.info()", function (a, b, c) {
                var e = this; if (!1 === a) return d("#datatables_buttons_info").fadeOut(function () { d(this).remove() }), clearTimeout(r), r = null, this; r && clearTimeout(r); d("#datatables_buttons_info").length && d("#datatables_buttons_info").remove(); d('<div id="datatables_buttons_info" class="dt-button-info"/>').html(a ? "<h2>" + a + "</h2>" : "").append(d("<div/>")["string" === typeof b ? "html" :
                    "append"](b)).css("display", "none").appendTo("body").fadeIn(); c !== m && 0 !== c && (r = setTimeout(function () { e.buttons.info(!1) }, c)); return this
            }); i.Api.register("buttons.exportData()", function (a) {
                if (this.context.length) {
                    var b = new i.Api(this.context[0]), c = d.extend(!0, {}, {
                        rows: null, columns: "", modifier: { search: "applied", order: "applied" }, orthogonal: "display", stripHtml: !0, stripNewlines: !0, decodeEntities: !0, trim: !0, format: { header: function (a) { return e(a) }, footer: function (a) { return e(a) }, body: function (a) { return e(a) } },
                        customizeData: null
                    }, a), e = function (a) { if ("string" !== typeof a) return a; a = a.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, ""); a = a.replace(/<!\-\-.*?\-\->/g, ""); c.stripHtml && (a = a.replace(/<[^>]*>/g, "")); c.trim && (a = a.replace(/^\s+|\s+$/g, "")); c.stripNewlines && (a = a.replace(/\n/g, " ")); c.decodeEntities && (v.innerHTML = a, a = v.value); return a }, a = b.columns(c.columns).indexes().map(function (a) { var d = b.column(a).header(); return c.format.header(d.innerHTML, a, d) }).toArray(), g = b.table().footer() ? b.columns(c.columns).indexes().map(function (a) {
                        var d =
                            b.column(a).footer(); return c.format.footer(d ? d.innerHTML : "", a, d)
                    }).toArray() : null, h = d.extend({}, c.modifier); b.select && "function" === typeof b.select.info && h.selected === m && b.rows(c.rows, d.extend({ selected: !0 }, h)).any() && d.extend(h, { selected: !0 }); for (var h = b.rows(c.rows, h).indexes().toArray(), f = b.cells(h, c.columns), h = f.render(c.orthogonal).toArray(), f = f.nodes().toArray(), k = a.length, j = [], l = 0, n = 0, o = 0 < k ? h.length / k : 0; n < o; n++) { for (var r = [k], s = 0; s < k; s++)r[s] = c.format.body(h[l], n, s, f[l]), l++; j[n] = r } a = {
                        header: a,
                        footer: g, body: j
                    }; c.customizeData && c.customizeData(a); return a
                }
            }); i.Api.register("buttons.exportInfo()", function (a) {
                a || (a = {}); var b; var c = a; b = "*" === c.filename && "*" !== c.title && c.title !== m && null !== c.title && "" !== c.title ? c.title : c.filename; "function" === typeof b && (b = b()); b === m || null === b ? b = null : (-1 !== b.indexOf("*") && (b = d.trim(b.replace("*", d("head > title").text()))), b = b.replace(/[^a-zA-Z0-9_\u00A1-\uFFFF\.,\-_ !\(\)]/g, ""), (c = t(c.extension)) || (c = ""), b += c); c = t(a.title); c = null === c ? null : -1 !== c.indexOf("*") ?
                    c.replace("*", d("head > title").text() || "Exported data") : c; return { filename: b, title: c, messageTop: w(this, a.message || a.messageTop, "top"), messageBottom: w(this, a.messageBottom, "bottom") }
            }); var t = function (a) { return null === a || a === m ? null : "function" === typeof a ? a() : a }, w = function (a, b, c) { b = t(b); if (null === b) return null; a = d("caption", a.table().container()).eq(0); return "*" === b ? a.css("caption-side") !== c ? null : a.length ? a.text() : "" : b }, v = d("<textarea/>")[0]; d.fn.dataTable.Buttons = l; d.fn.DataTable.Buttons = l; d(o).on("init.dt plugin-init.dt",
                function (a, b) { if ("dt" === a.namespace) { var c = b.oInit.buttons || i.defaults.buttons; c && !b._buttons && (new l(b, c)).container() } }); i.ext.feature.push({ fnInit: function (a) { var a = new i.Api(a), b = a.init().buttons || i.defaults.buttons; return (new l(a, b)).container() }, cFeature: "B" }); return l
});