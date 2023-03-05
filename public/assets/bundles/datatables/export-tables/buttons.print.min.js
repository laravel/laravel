(function (c) { "function" === typeof define && define.amd ? define(["jquery", "datatables.net", "datatables.net-buttons"], function (f) { return c(f, window, document) }) : "object" === typeof exports ? module.exports = function (f, b) { f || (f = window); if (!b || !b.fn.dataTable) b = require("datatables.net")(f, b).$; b.fn.dataTable.Buttons || require("datatables.net-buttons")(f, b); return c(b, f, f.document) } : c(jQuery, window, document) })(function (c, f, b, n) {
    var i = c.fn.dataTable, e = b.createElement("a"), m = function (a) {
        e.href = a; a = e.host; -1 === a.indexOf("/") &&
            0 !== e.pathname.indexOf("/") && (a += "/"); return e.protocol + "//" + a + e.pathname + e.search
    }; i.ext.buttons.print = {
        className: "buttons-print", text: function (a) { return a.i18n("buttons.print", "Print") }, action: function (a, b, e, h) {
            var a = b.buttons.exportData(c.extend({ decodeEntities: !1 }, h.exportOptions)), e = b.buttons.exportInfo(h), i = c.map(b.settings()[0].aoColumns, function (b) { return b.sClass }), k = function (b, a) {
                for (var d = "<tr>", c = 0, e = b.length; c < e; c++)d += "<" + a + " " + (i[c] ? 'class="' + i[c] + '"' : "") + ">" + (null === b[c] || b[c] === n ?
                    "" : b[c]) + "</" + a + ">"; return d + "</tr>"
            }, d = '<table class="' + b.table().node().className + '">'; h.header && (d += "<thead>" + k(a.header, "th") + "</thead>"); for (var d = d + "<tbody>", l = 0, o = a.body.length; l < o; l++)d += k(a.body[l], "td"); d += "</tbody>"; h.footer && a.footer && (d += "<tfoot>" + k(a.footer, "th") + "</tfoot>"); var d = d + "</table>", g = f.open("", ""); g.document.close(); var j = "<title>" + e.title + "</title>"; c("style, link").each(function () { var b = j, a = c(this).clone()[0]; "link" === a.nodeName.toLowerCase() && (a.href = m(a.href)); j = b + a.outerHTML });
            try { g.document.head.innerHTML = j } catch (p) { c(g.document.head).html(j) } g.document.body.innerHTML = "<h1>" + e.title + "</h1><div>" + (e.messageTop || "") + "</div>" + d + "<div>" + (e.messageBottom || "") + "</div>"; c(g.document.body).addClass("dt-print-view"); c("img", g.document.body).each(function (b, a) { a.setAttribute("src", m(a.getAttribute("src"))) }); h.customize && h.customize(g, h, b); g.setTimeout(function () { h.autoPrint && (g.print(), g.close()) }, 1E3)
        }, title: "*", messageTop: "*", messageBottom: "*", exportOptions: {}, header: !0, footer: !1,
        autoPrint: !0, customize: null
    }; return i.Buttons
});