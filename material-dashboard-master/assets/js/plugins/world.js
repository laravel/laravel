! function(t, e) {
  "object" == typeof exports && "undefined" != typeof module ? module.exports = e() : "function" == typeof define && define.amd ? define(e) : (t = "undefined" != typeof globalThis ? globalThis : t || self).jsVectorMap = e()
}(this, (function() {
  "use strict";
  Element.prototype.matches || (Element.prototype.matches = Element.prototype.matchesSelector || Element.prototype.mozMatchesSelector || Element.prototype.msMatchesSelector || Element.prototype.oMatchesSelector || Element.prototype.webkitMatchesSelector || function(t) {
    for (var e = (this.document || this.ownerDocument).querySelectorAll(t), i = e.length; --i >= 0 && e.item(i) !== this;);
    return i > -1
  }), Object.assign || Object.defineProperty(Object, "assign", {
    enumerable: !1,
    configurable: !0,
    writable: !0,
    value: function(t) {
      if (null == t) throw new TypeError("Cannot convert first argument to object");
      for (var e = Object(t), i = 1; i < arguments.length; i++) {
        var s = arguments[i];
        if (null != s) {
          s = Object(s);
          for (var a = Object.keys(Object(s)), r = 0, n = a.length; r < n; r++) {
            var o = a[r],
              h = Object.getOwnPropertyDescriptor(s, o);
            void 0 !== h && h.enumerable && (e[o] = s[o])
          }
        }
      }
      return e
    }
  });
  var t = {},
    e = 1,
    i = function(i, s, a, r) {
      void 0 === r && (r = {}), t["jvm:" + s + "::" + e++] = {
        selector: i,
        handler: a
      }, i.addEventListener(s, a, r)
    },
    s = function(e, i, s) {
      var a = i.split(":")[1];
      e.removeEventListener(a, s), delete t[i]
    },
    a = function() {
      return t
    },
    r = function() {
      function t(t) {
        return t instanceof Element ? (this.selector = t, this) : (this.selector = document.querySelector(t), this)
      }
      var e = t.prototype;
      return e.on = function(t, e, s) {
        return void 0 === s && (s = {}), i(this.selector, t, e, s), this
      }, e.delegate = function(t, e, i) {
        for (var s in e = e.split(" ")) this.on(e[s], (function(e) {
          var s = e.target;
          s.matches(t) && i.call(s, e)
        }))
      }, e.css = function(t) {
        for (var e in t) this.selector.style[e] = t[e];
        return this
      }, e.text = function(t) {
        return t ? (this.selector.textContent = t, this) : this.selector.textContent
      }, e.attr = function(t, e) {
        return t && e ? (this.selector.setAttribute(t, e), this) : this.selector.getAttribute(t)
      }, e.addClass = function(t) {
        return this.selector.classList ? (this.selector.classList.add(t), this) : (-1 == this.selector.className.split(" ").indexOf(t) && (this.selector.className += " " + t), this)
      }, e.append = function(t) {
        return this.selector.appendChild(t), this
      }, e.show = function() {
        this.css({
          display: "block"
        })
      }, e.hide = function() {
        this.css({
          display: "none"
        })
      }, e.height = function() {
        return this.selector.offsetHeight
      }, e.width = function() {
        return this.selector.offsetWidth
      }, t
    }(),
    n = function(t) {
      return function(t) {
        return !!t && "object" == typeof t
      }(t) && ! function(t) {
        var e = Object.prototype.toString.call(t);
        return "[object RegExp]" === e || "[object Date]" === e || function(t) {
          return t.$$typeof === o
        }(t)
      }(t)
    };
  var o = "function" == typeof Symbol && Symbol.for ? Symbol.for("react.element") : 60103;

  function h(t, e) {
    return !1 !== e.clone && e.isMergeableObject(t) ? d((i = t, Array.isArray(i) ? [] : {}), t, e) : t;
    var i
  }

  function l(t, e, i) {
    return t.concat(e).map((function(t) {
      return h(t, i)
    }))
  }

  function c(t) {
    return Object.keys(t).concat(function(t) {
      return Object.getOwnPropertySymbols ? Object.getOwnPropertySymbols(t).filter((function(e) {
        return t.propertyIsEnumerable(e)
      })) : []
    }(t))
  }

  function u(t, e) {
    try {
      return e in t
    } catch (t) {
      return !1
    }
  }

  function p(t, e, i) {
    var s = {};
    return i.isMergeableObject(t) && c(t).forEach((function(e) {
      s[e] = h(t[e], i)
    })), c(e).forEach((function(a) {
      (function(t, e) {
        return u(t, e) && !(Object.hasOwnProperty.call(t, e) && Object.propertyIsEnumerable.call(t, e))
      })(t, a) || (u(t, a) && i.isMergeableObject(e[a]) ? s[a] = function(t, e) {
        if (!e.customMerge) return d;
        var i = e.customMerge(t);
        return "function" == typeof i ? i : d
      }(a, i)(t[a], e[a], i) : s[a] = h(e[a], i))
    })), s
  }
  var d = function(t, e, i) {
      (i = i || {}).arrayMerge = i.arrayMerge || l, i.isMergeableObject = i.isMergeableObject || n, i.cloneUnlessOtherwiseSpecified = h;
      var s = Array.isArray(e);
      return s === Array.isArray(t) ? s ? i.arrayMerge(t, e, i) : p(t, e, i) : h(e, i)
    },
    f = {
      isImageUrl: function(t) {
        return /\.(jpg|gif|png)$/.test(t)
      },
      createElement: function(t, e, i, s) {
        void 0 === s && (s = !1);
        var a = document.createElement(t);
        return i && (a[s ? "innerHTML" : "textContent"] = i), e && (a.className = e), a
      },
      removeElement: function(t) {
        t.parentNode.removeChild(t)
      },
      $: function(t) {
        return new r(t)
      },
      hyphenate: function(t) {
        return t.replace(/[\w]([A-Z])/g, (function(t) {
          return t[0] + "-" + t[1]
        })).toLowerCase()
      },
      isFunc: function(t) {
        return "function" == typeof t
      },
      isObj: function(t) {
        return "object" == typeof t
      },
      isStr: function(t) {
        return "string" == typeof t
      },
      isArr: function(t) {
        return Array.isArray(t)
      },
      merge: function(t, e) {
        return Object.assign(t, e)
      },
      mergeDeeply: function(t, e) {
        return d(t, e)
      },
      keys: function(t) {
        return Object.keys(t)
      }
    };

  function m(t, e) {
    t.prototype = Object.create(e.prototype), t.prototype.constructor = t, g(t, e)
  }

  function g(t, e) {
    return (g = Object.setPrototypeOf || function(t, e) {
      return t.__proto__ = e, t
    })(t, e)
  }
  var v = function() {
      function t(t, e) {
        this._name = t, this.node = this.createElement(t), e && this.set(e)
      }
      var e = t.prototype;
      return e.createElement = function(t) {
        return document.createElementNS("http://www.w3.org/2000/svg", t)
      }, e.addClass = function(t) {
        this.node.setAttribute("class", t)
      }, e.getBBox = function() {
        return this.node.getBBox()
      }, e.set = function(t, e) {
        if (f.isObj(t))
          for (var i in t) this.applyAttr(i, t[i]);
        else this.applyAttr(t, e)
      }, e.get = function(t) {
        return this.style.initial[t]
      }, e.applyAttr = function(t, e) {
        this.node.setAttribute(f.hyphenate(t), e)
      }, e.remove = function() {
        this.node.parentNode.removeChild(this.node)
      }, t
    }(),
    y = function(t) {
      function e(e, i, s) {
        var a;
        return void 0 === s && (s = {}), (a = t.call(this, e, i) || this).isHovered = !1, a.isSelected = !1, a.style = s, a.style.current = {}, a.updateStyle(), a
      }
      m(e, t);
      var i = e.prototype;
      return i.setStyle = function(t, e) {
        var i;
        f.isObj(t) ? f.merge(this.style.current, t) : f.merge(this.style.current, ((i = {})[t] = e, i));
        this.updateStyle()
      }, i.updateStyle = function() {
        var t = {};
        f.merge(t, this.style.initial), f.merge(t, this.style.current), this.isHovered && f.merge(t, this.style.hover), this.isSelected && (f.merge(t, this.style.selected), this.isHovered && f.merge(t, this.style.selectedHover)), this.set(t)
      }, e
    }(v),
    b = function(t) {
      function e(e, i) {
        return t.call(this, "text", e, i) || this
      }
      return m(e, t), e.prototype.applyAttr = function(e, i) {
        "text" === e ? this.node.textContent = i : t.prototype.applyAttr.call(this, e, i)
      }, e
    }(y),
    S = function(t) {
      function e(e, i) {
        return t.call(this, "image", e, i) || this
      }
      return m(e, t), e.prototype.applyAttr = function(e, i) {
        var s;
        "image" === e ? (f.isObj(i) ? (s = i.url, this.offset = i.offset || [0, 0]) : (s = i, this.offset = [0, 0]), this.node.setAttributeNS("http://www.w3.org/1999/xlink", "href", s), this.width = 23, this.height = 23, this.applyAttr("width", this.width), this.applyAttr("height", this.height), this.applyAttr("x", this.cx - this.width / 2 + this.offset[0]), this.applyAttr("y", this.cy - this.height / 2 + this.offset[1])) : "cx" == e ? (this.cx = i, this.width && this.applyAttr("x", i - this.width / 2 + this.offset[0])) : "cy" == e ? (this.cy = i, this.height && this.applyAttr("y", i - this.height / 2 + this.offset[1])) : t.prototype.applyAttr.apply(this, arguments)
      }, e
    }(y),
    w = function(t) {
      function e(e) {
        var i;
        return (i = t.call(this, "svg") || this)._container = e, i._defsElement = new v("defs"), i._rootElement = new v("g", {
          id: "jvm-regions-group"
        }), i.node.appendChild(i._defsElement.node), i.node.appendChild(i._rootElement.node), i._container.append(i.node), i
      }
      m(e, t);
      var i = e.prototype;
      return i.setSize = function(t, e) {
        this.node.setAttribute("width", t), this.node.setAttribute("height", e)
      }, i.applyTransformParams = function(t, e, i) {
        this._rootElement.node.setAttribute("transform", "scale(" + t + ") translate(" + e + ", " + i + ")")
      }, i.createPath = function(t, e) {
        var i = new y("path", t, e);
        return i.node.setAttribute("fill-rule", "evenodd"), this.add(i)
      }, i.createCircle = function(t, e, i) {
        var s = new y("circle", t, e);
        return this.add(s, i)
      }, i.createLine = function(t, e, i) {
        var s = new y("line", t, e);
        return this.add(s, i)
      }, i.createText = function(t, e, i) {
        var s = new b(t, e);
        return this.add(s, i)
      }, i.createImage = function(t, e, i) {
        var s = new S(t, e);
        return this.add(s, i)
      }, i.createGroup = function(t) {
        var e = new v("g");
        return this.node.appendChild(e.node), t && (e.node.id = t), e.canvas = this, e
      }, i.add = function(t, e) {
        return (e = e || this._rootElement).node.appendChild(t.node), t
      }, e
    }(v);

  function k(t, e, i) {
    var s = f.$(e),
      a = -1 === s.attr("class").indexOf("jvm-region") ? "marker" : "region",
      r = "region" === a ? s.attr("data-code") : s.attr("data-index"),
      n = a + ":select";
    return i && (n = a + ".tooltip:show"), {
      event: n,
      type: a,
      code: r,
      element: "region" === a ? t.regions[r].element : t.markers[r].element,
      tooltipText: "region" === a ? t.mapData.paths[r].name || "" : t.markers[r].config.name || ""
    }
  }
  var x = function() {
      function t() {}
      var e = t.prototype;
      return e.getLabelText = function(t, e) {
        if (e) {
          if (f.isFunc(e.render)) {
            var i = [];
            return this.config.marker && i.push(this.config.marker), i.push(t), e.render.apply(this, i)
          }
          return t
        }
      }, e.getLabelOffsets = function(t, e) {
        return f.isFunc(e.offsets) ? e.offsets(t) : f.isArr(e.offsets) ? e.offsets[t] : [0, 0]
      }, e.setStyle = function(t, e) {
        this.shape.setStyle(t, e)
      }, e.remove = function() {
        this.shape.remove(), this.label && this.label.remove()
      }, e.hover = function(t) {
        this._setStatus("isHovered", t)
      }, e.select = function(t) {
        this._setStatus("isSelected", t)
      }, e._setStatus = function(t, e) {
        this.shape[t] = e, this.shape.updateStyle(), this[t] = e, this.label && (this.label[t] = e, this.label.updateStyle())
      }, t
    }(),
    M = function(t) {
      function e(e) {
        var i, s = e.map,
          a = e.code,
          r = e.path,
          n = e.style,
          o = e.label,
          h = e.labelStyle,
          l = e.labelsGroup;
        (i = t.call(this) || this).config = arguments[0], i.canvas = s.canvas, i.map = s, i.shape = i.canvas.createPath({
          d: r,
          dataCode: a
        }, n), i.shape.addClass("jvm-region jvm-element");
        var c = i.shape.getBBox(),
          u = i.getLabelText(a, o);
        if (o && u) {
          var p = i.getLabelOffsets(a);
          i.labelX = c.x + c.width / 2 + p[0], i.labelY = c.y + c.height / 2 + p[1], i.label = i.canvas.createText({
            text: u,
            textAnchor: "middle",
            alignmentBaseline: "central",
            dataCode: a,
            x: i.labelX,
            y: i.labelY
          }, h, l), i.label.addClass("jvm-region jvm-element")
        }
        return i
      }
      return m(e, t), e.prototype.updateLabelPosition = function() {
        this.label && this.label.set({
          x: this.labelX * this.map.scale + this.map.transX * this.map.scale,
          y: this.labelY * this.map.scale + this.map.transY * this.map.scale
        })
      }, e
    }(x);
  var _ = function(t) {
    function e(e) {
      var i, s = e.index,
        a = e.map,
        r = e.style,
        n = e.x1,
        o = e.y1,
        h = e.x2,
        l = e.y2,
        c = e.group;
      return (i = t.call(this) || this).shape = a.canvas.createLine({
        x1: n,
        y1: o,
        x2: h,
        y2: l,
        dataIndex: s
      }, r, c), i.shape.addClass("jvm-line"), i
    }
    return m(e, t), e
  }(x);

  function j(t, e) {
    return t.toLowerCase() + ":to:" + e.toLowerCase()
  }
  var E = function(t) {
    function e(e) {
      var i, s = e.index,
        a = e.style,
        r = e.label,
        n = e.cx,
        o = e.cy,
        h = e.map,
        l = e.group;
      return (i = t.call(this) || this)._map = h, i._isImage = !!a.initial.image, i.config = arguments[0], i.shape = h.canvas[i._isImage ? "createImage" : "createCircle"]({
        dataIndex: s,
        cx: n,
        cy: o
      }, i._getStyle(), l), i.shape.addClass("jvm-marker jvm-element"), i._isImage && i.updateLabelPosition(), r && i._createLabel(i.config), i
    }
    m(e, t);
    var i = e.prototype;
    return i.updateLabelPosition = function() {
      this.label && this.label.set({
        x: this._labelX * this._map.scale + this._offsets[0] + this._map.transX * this._map.scale + 5 + (this._isImage ? (this.shape.width || 0) / 2 : this.shape.node.r.baseVal.value),
        y: this._labelY * this._map.scale + this._map.transY * this._map.scale + this._offsets[1]
      })
    }, i._createLabel = function(t) {
      var e = t.index,
        i = t.map,
        s = t.label,
        a = t.labelsGroup,
        r = t.cx,
        n = t.cy,
        o = t.marker,
        h = t.isRecentlyCreated,
        l = this.getLabelText(e, s);
      this._labelX = r / i.scale - i.transX, this._labelY = n / i.scale - i.transY, this._offsets = h && o.offsets ? o.offsets : this.getLabelOffsets(e, s), this.label = i.canvas.createText({
        text: l,
        dataIndex: e,
        x: this._labelX,
        y: this._labelY,
        dy: "0.6ex"
      }, i.params.markerLabelStyle, a), this.label.addClass("jvm-marker jvm-element"), h && this.updateLabelPosition()
    }, i._getStyle = function() {
      var t = {};
      return this._isImage ? t.initial = {
        image: this.config.style.initial.image
      } : t = this.config.style, t
    }, e
  }(x);
  var O = function() {
      function t(t) {
        void 0 === t && (t = {}), this._options = t, this._map = this._options.map, this._series = this._options.series, this._body = f.createElement("div", "jvm-legend"), this._options.cssClass && this._body.setAttribute("class", this._options.cssClass), t.vertical ? this._map.legendVertical.appendChild(this._body) : this._map.legendHorizontal.appendChild(this._body), this.render()
      }
      return t.prototype.render = function() {
        var t, e, i, s = this._series.scale.getTicks(),
          a = f.createElement("div", "jvm-legend-inner");
        if (this._body.innderHTML = "", this._options.title) {
          var r = f.createElement("div", "jvm-legend-title", this._options.title);
          this._body.appendChild(r)
        }
        this._body.appendChild(a);
        for (var n = 0; n < s.length; n++) {
          switch (t = f.createElement("div", "jvm-legend-tick"), e = f.createElement("div", "jvm-legend-tick-sample"), this._series.config.attribute) {
            case "fill":
              f.isImageUrl(s[n].value) ? e.style.background = "url(" + s[n].value + ")" : e.style.background = s[n].value;
              break;
            case "stroke":
              e.style.background = s[n].value;
              break;
            case "image":
              e.style.background = "url(" + (f.isObj(s[n].value) ? s[n].value.url : s[n].value) + ") no-repeat center center", e.style.backgroundSize = "cover"
          }
          t.appendChild(e), i = s[n].label, this._options.labelRender && (i = this._options.labelRender(i));
          var o = f.createElement("div", "jvm-legend-tick-text", i);
          t.appendChild(o), a.appendChild(t)
        }
      }, t
    }(),
    C = function() {
      function t(t) {
        this._scale = t
      }
      var e = t.prototype;
      return e.getValue = function(t) {
        return this._scale[t]
      }, e.getTicks = function() {
        var t = [];
        for (var e in this._scale) t.push({
          label: e,
          value: this._scale[e]
        });
        return t
      }, t
    }(),
    X = function() {
      function t(t, e, i) {
        void 0 === t && (t = {}), this._map = i, this._elements = e, this._values = t.values || {}, this.config = t, this.config.attribute = t.attribute || "fill", t.attributes && this.setAttributes(t.attributes), f.isObj(t.scale) && (this.scale = new C(t.scale)), this.config.legend && (this.legend = new O(f.merge({
          map: this._map,
          series: this
        }, this.config.legend))), this.setValues(this._values)
      }
      var e = t.prototype;
      return e.setValues = function(t) {
        var e = {};
        for (var i in t) t[i] && (e[i] = this.scale.getValue(t[i]));
        this.setAttributes(e)
      }, e.setAttributes = function(t) {
        for (var e in t) this._elements[e] && this._elements[e].element.setStyle(this.config.attribute, t[e])
      }, e.clear = function() {
        var t, e = {};
        for (t in this._values) this._elements[t] && (e[t] = this._elements[t].element.shape.style.initial[this.config.attribute]);
        this.setAttributes(e), this._values = {}
      }, t
    }();
  var Y = {
    mill: function(t, e, i) {
      return {
        x: this.radius * (e - i) * this.radDeg,
        y: -this.radius * Math.log(Math.tan((45 + .4 * t) * this.radDeg)) / .8
      }
    },
    merc: function(t, e, i) {
      return {
        x: this.radius * (e - i) * this.radDeg,
        y: -this.radius * Math.log(Math.tan(Math.PI / 4 + t * Math.PI / 360))
      }
    },
    aea: function(t, e, i) {
      var s = i * this.radDeg,
        a = 29.5 * this.radDeg,
        r = 45.5 * this.radDeg,
        n = t * this.radDeg,
        o = e * this.radDeg,
        h = (Math.sin(a) + Math.sin(r)) / 2,
        l = Math.cos(a) * Math.cos(a) + 2 * h * Math.sin(a),
        c = h * (o - s),
        u = Math.sqrt(l - 2 * h * Math.sin(n)) / h,
        p = Math.sqrt(l - 2 * h * Math.sin(0)) / h;
      return {
        x: u * Math.sin(c) * this.radius,
        y: -(p - u * Math.cos(c)) * this.radius
      }
    },
    lcc: function(t, e, i) {
      var s = i * this.radDeg,
        a = e * this.radDeg,
        r = 33 * this.radDeg,
        n = 45 * this.radDeg,
        o = t * this.radDeg,
        h = Math.log(Math.cos(r) * (1 / Math.cos(n))) / Math.log(Math.tan(Math.PI / 4 + n / 2) * (1 / Math.tan(Math.PI / 4 + r / 2))),
        l = Math.cos(r) * Math.pow(Math.tan(Math.PI / 4 + r / 2), h) / h,
        c = l * Math.pow(1 / Math.tan(Math.PI / 4 + o / 2), h),
        u = l * Math.pow(1 / Math.tan(Math.PI / 4 + 0), h);
      return {
        x: c * Math.sin(h * (a - s)) * this.radius,
        y: -(u - c * Math.cos(h * (a - s))) * this.radius
      }
    }
  };
  Y.degRad = 180 / Math.PI, Y.radDeg = Math.PI / 180, Y.radius = 6381372;
  var L = function() {
    function t(t, e) {
      var i = t.scale,
        s = t.values;
      this._scale = i, this._values = s, this._fromColor = this.hexToRgb(i[0]), this._toColor = this.hexToRgb(i[1]), this._map = e, this.setMinMaxValues(s), this.visualize()
    }
    var e = t.prototype;
    return e.setMinMaxValues = function(t) {
      for (var e in this.min = Number.MAX_VALUE, this.max = 0, t)(e = parseFloat(t[e])) > this.max && (this.max = e), e < this.min && (this.min = e)
    }, e.visualize = function() {
      var t, e = {};
      for (var i in this._values) t = parseFloat(this._values[i]), isNaN(t) || (e[i] = this.getValue(t));
      this.setAttributes(e)
    }, e.setAttributes = function(t) {
      for (var e in t) this._map.regions[e] && this._map.regions[e].element.setStyle("fill", t[e])
    }, e.getValue = function(t) {
      for (var e, i = "#", s = 0; s < 3; s++) i += (1 === (e = Math.round(this._fromColor[s] + (this._toColor[s] - this._fromColor[s]) * ((t - this.min) / (this.max - this.min))).toString(16)).length ? "0" : "") + e;
      return i
    }, e.hexToRgb = function(t) {
      var e = 0,
        i = 0,
        s = 0;
      return 4 == t.length ? (e = "0x" + t[1] + t[1], i = "0x" + t[2] + t[2], s = "0x" + t[3] + t[3]) : 7 == t.length && (e = "0x" + t[1] + t[2], i = "0x" + t[3] + t[4], s = "0x" + t[5] + t[6]), [parseInt(e), parseInt(i), parseInt(s)]
    }, t
  }();
  var T = Object.freeze({
      __proto__: null,
      handleContainerEvents: function() {
        var t, e, i = this,
          s = !1,
          a = this;
        this.params.draggable && (this.container.on("mousemove", (function(i) {
          return s && (a.transX -= (t - i.pageX) / a.scale, a.transY -= (e - i.pageY) / a.scale, a.applyTransform(), t = i.pageX, e = i.pageY), !1
        })).on("mousedown", (function(i) {
          return s = !0, t = i.pageX, e = i.pageY, !1
        })), f.$("body").on("mouseup", (function() {
          s = !1
        }))), this.params.zoomOnScroll && this.container.on("wheel", (function(t) {
          var e = 0;
          e = (t.deltaY || -t.wheelDelta || t.detail) >> 10 || 1, e *= 75;
          var s = i.container.selector.getBoundingClientRect(),
            r = t.pageX - s.left - window.pageXOffset,
            n = t.pageY - s.top - window.pageYOffset,
            o = Math.pow(1 + a.params.zoomOnScrollSpeed / 1e3, -1.5 * e);
          a.tooltip && a.tooltip.hide(), a.setScale(a.scale * o, r, n)
        }), {
          passive: !0
        })
      },
      handleElementEvents: function() {
        var t = this;
        this.container.delegate(".jvm-element", "mouseover mouseout", (function(e) {
          var i = k(t, this, !0),
            s = t.params.showTooltip;
          "mouseover" === e.type ? e.defaultPrevented || (i.element.hover(!0), s && (t.tooltip.text(i.tooltipText), t.tooltip.show(), t.emit(i.event, [t.tooltip, i.code]))) : (i.element.hover(!1), s && t.tooltip.hide())
        })), this.container.delegate(".jvm-element", "mouseup", (function(e) {
          var i = k(t, this);
          if ("region" === i.type && t.params.regionsSelectable || "marker" === i.type && t.params.markersSelectable && !e.defaultPrevented) {
            var s = i.element;
            t.params[i.type + "sSelectableOne"] && t.clearSelected(i.type + "s"), i.element.isSelected ? s.select(!1) : s.select(!0), t.emit(i.event, [i.code, s.isSelected, t.getSelected(i.type + "s")])
          }
        }))
      },
      handleZoomButtons: function() {
        var t = this,
          e = this,
          s = f.createElement("div", "jvm-zoom-btn jvm-zoomin", "&#43;", !0),
          a = f.createElement("div", "jvm-zoom-btn jvm-zoomout", "&#x2212", !0);
        this.container.append(s).append(a), i(s, "click", (function() {
          t.setScale(e.scale * e.params.zoomStep, e.width / 2, e.height / 2, !1, e.params.zoomAnimate)
        })), i(a, "click", (function() {
          t.setScale(e.scale / e.params.zoomStep, e.width / 2, e.height / 2, !1, e.params.zoomAnimate)
        }))
      },
      bindContainerTouchEvents: function() {
        var t, e, i, s, a, r, n, o = this,
          h = function(h) {
            var l, c, u, p, d = h.touches;
            if ("touchstart" == h.type && (n = 0), 1 == d.length) 1 == n && (u = o.transX, p = o.transY, o.transX -= (i - d[0].pageX) / o.scale, o.transY -= (s - d[0].pageY) / o.scale, o.tooltip.hide(), o.applyTransform(), u == o.transX && p == o.transY || h.preventDefault()), i = d[0].pageX, s = d[0].pageY;
            else if (2 == d.length)
              if (2 == n) c = Math.sqrt(Math.pow(d[0].pageX - d[1].pageX, 2) + Math.pow(d[0].pageY - d[1].pageY, 2)) / e, o.setScale(t * c, a, r), o.tooltip.hide(), h.preventDefault();
              else {
                var f = o.container.selector.getBoundingClientRect();
                l = {
                  top: f.top + window.scrollY,
                  left: f.left + window.scrollX
                }, a = d[0].pageX > d[1].pageX ? d[1].pageX + (d[0].pageX - d[1].pageX) / 2 : d[0].pageX + (d[1].pageX - d[0].pageX) / 2, r = d[0].pageY > d[1].pageY ? d[1].pageY + (d[0].pageY - d[1].pageY) / 2 : d[0].pageY + (d[1].pageY - d[0].pageY) / 2, a -= l.left, r -= l.top, t = o.scale, e = Math.sqrt(Math.pow(d[0].pageX - d[1].pageX, 2) + Math.pow(d[0].pageY - d[1].pageY, 2))
              } n = d.length
          };
        this.container.on("touchstart", h).on("touchmove", h)
      },
      createRegions: function() {
        var t, e;
        for (t in this.regionLabelsGroup = this.regionLabelsGroup || this.canvas.createGroup("jvm-regions-labels-group"), this.mapData.paths) e = new M({
          map: this,
          code: t,
          path: this.mapData.paths[t].path,
          style: f.merge({}, this.params.regionStyle),
          labelStyle: this.params.regionLabelStyle,
          labelsGroup: this.regionLabelsGroup,
          label: this.params.labels && this.params.labels.regions
        }), this.regions[t] = {
          config: this.mapData.paths[t],
          element: e
        }
      },
      createLines: function(t, e, i) {
        var s = this;
        void 0 === i && (i = !1);
        var a, r = !1,
          n = !1;
        for (var o in this.linesGroup = this.linesGroup || this.canvas.createGroup("jvm-lines-group"), t) {
          var h = t[o];
          for (var l in e) {
            var c = i ? e[l].config : e[l];
            c.name === h.from && (r = this.getMarkerPosition(c)), c.name === h.to && (n = this.getMarkerPosition(c))
          }!1 !== r && !1 !== n && (a = new _({
            index: o,
            map: this,
            style: f.mergeDeeply({
              initial: this.params.lineStyle
            }, {
              initial: h.style || {}
            }),
            x1: r.x,
            y1: r.y,
            x2: n.x,
            y2: n.y,
            group: this.linesGroup
          }), i && Object.keys(this.lines).forEach((function(e) {
            e === j(t[0].from, t[0].to) && s.lines[e].element.remove()
          })), this.lines[j(h.from, h.to)] = {
            element: a,
            config: h
          })
        }
      },
      createMarkers: function(t, e) {
        var i, s, a, r, n = this;
        for (var o in void 0 === t && (t = {}), void 0 === e && (e = !1), this.markersGroup = this.markersGroup || this.canvas.createGroup("jvm-markers-group"), this.markerLabelsGroup = this.markerLabelsGroup || this.canvas.createGroup("jvm-markers-labels-group"), t) {
          if (i = t[o], a = this.getMarkerPosition(i), r = i.coords.join(":"), e) {
            if (f.keys(this.markers).filter((function(t) {
                return n.markers[t]._uid === r
              })).length) continue;
            o = f.keys(this.markers).length
          }!1 !== a && (s = new E({
            index: o,
            map: this,
            style: f.mergeDeeply(this.params.markerStyle, {
              initial: i.style || {}
            }),
            label: this.params.labels && this.params.labels.markers,
            labelsGroup: this.markerLabelsGroup,
            cx: a.x,
            cy: a.y,
            group: this.markersGroup,
            marker: i,
            isRecentlyCreated: e
          }), this.markers[o] && this.removeMarkers([o]), this.markers[o] = {
            _uid: r,
            config: i,
            element: s
          })
        }
      },
      createTooltip: function() {
        var t = this,
          e = f.createElement("div", "jvm-tooltip");
        this.tooltip = f.$(document.body.appendChild(e)), this.container.on("mousemove", (function(i) {
          if ("block" === t.tooltip.selector.style.display) {
            var s = t.container.selector.querySelector("#jvm-regions-group").getBoundingClientRect(),
              a = e.getBoundingClientRect(),
              r = a.height,
              n = a.width,
              o = i.clientY <= s.top + r + 5,
              h = i.pageY - r - 5,
              l = i.pageX - n - 5;
            o && (h += r + 5, l -= 10), i.clientX < s.left + n + 5 && (l = i.pageX + 5 + 2, o && (l += 10)), t.tooltip.css({
              top: h + "px",
              left: l + "px"
            })
          }
        }))
      },
      createSeries: function() {
        for (var t in this.series = {
            markers: [],
            regions: []
          }, this.params.series)
          for (var e = 0; e < this.params.series[t].length; e++) this.series[t][e] = new X(this.params.series[t][e], this[t], this)
      },
      applyTransform: function() {
        var t, e, i, s;
        this.defaultWidth * this.scale <= this.width ? (t = (this.width - this.defaultWidth * this.scale) / (2 * this.scale), i = (this.width - this.defaultWidth * this.scale) / (2 * this.scale)) : (t = 0, i = (this.width - this.defaultWidth * this.scale) / this.scale), this.defaultHeight * this.scale <= this.height ? (e = (this.height - this.defaultHeight * this.scale) / (2 * this.scale), s = (this.height - this.defaultHeight * this.scale) / (2 * this.scale)) : (e = 0, s = (this.height - this.defaultHeight * this.scale) / this.scale), this.transY > e ? this.transY = e : this.transY < s && (this.transY = s), this.transX > t ? this.transX = t : this.transX < i && (this.transX = i), this.canvas.applyTransformParams(this.scale, this.transX, this.transY), this.markers && this.repositionMarkers(), this.lines && this.repositionLines(), this.repositionLabels()
      },
      setFocus: function(t) {
        var e = this;
        void 0 === t && (t = {});
        var i, s = [];
        if (t.region ? s.push(t.region) : t.regions && (s = t.regions), s.length) return s.forEach((function(t) {
          if (e.regions[t]) {
            var s = e.regions[t].element.shape.getBBox();
            s && (i = void 0 === i ? s : {
              x: Math.min(i.x, s.x),
              y: Math.min(i.y, s.y),
              width: Math.max(i.x + i.width, s.x + s.width) - Math.min(i.x, s.x),
              height: Math.max(i.y + i.height, s.y + s.height) - Math.min(i.y, s.y)
            })
          }
        })), this.setScale(Math.min(this.width / i.width, this.height / i.height), -(i.x + i.width / 2), -(i.y + i.height / 2), !0, t.animate);
        if (t.coords) {
          var a = this.coordsToPoint(t.coords[0], t.coords[1]),
            r = this.transX - a.x / this.scale,
            n = this.transY - a.y / this.scale;
          return this.setScale(t.scale * this.baseScale, r, n, !0, t.animate)
        }
      },
      resize: function() {
        var t = this.baseScale;
        this.width / this.height > this.defaultWidth / this.defaultHeight ? (this.baseScale = this.height / this.defaultHeight, this.baseTransX = Math.abs(this.width - this.defaultWidth * this.baseScale) / (2 * this.baseScale)) : (this.baseScale = this.width / this.defaultWidth, this.baseTransY = Math.abs(this.height - this.defaultHeight * this.baseScale) / (2 * this.baseScale)), this.scale *= this.baseScale / t, this.transX *= this.baseScale / t, this.transY *= this.baseScale / t
      },
      setScale: function(t, e, i, s, a) {
        var r, n, o, h, l, c, u, p, d, f, m = this,
          g = 0,
          v = Math.abs(Math.round(60 * (t - this.scale) / Math.max(t, this.scale)));
        t > this.params.zoomMax * this.baseScale ? t = this.params.zoomMax * this.baseScale : t < this.params.zoomMin * this.baseScale && (t = this.params.zoomMin * this.baseScale), void 0 !== e && void 0 !== i && (r = t / this.scale, s ? (d = e + this.defaultWidth * (this.width / (this.defaultWidth * t)) / 2, f = i + this.defaultHeight * (this.height / (this.defaultHeight * t)) / 2) : (d = this.transX - (r - 1) / t * e, f = this.transY - (r - 1) / t * i)), a && v > 0 ? (o = this.scale, h = (t - o) / v, l = this.transX * this.scale, u = this.transY * this.scale, c = (d * t - l) / v, p = (f * t - u) / v, n = setInterval((function() {
          g += 1, m.scale = o + h * g, m.transX = (l + c * g) / m.scale, m.transY = (u + p * g) / m.scale, m.applyTransform(), g == v && (clearInterval(n), m.emit("viewport:changed", [m.scale, m.transX, m.transY]))
        }), 10)) : (this.transX = d, this.transY = f, this.scale = t, this.applyTransform(), this.emit("viewport:changed", [this.scale, this.transX, this.transY]))
      },
      updateSize: function() {
        this.width = this.container.width(), this.height = this.container.height(), this.resize(), this.canvas.setSize(this.width, this.height), this.applyTransform()
      },
      coordsToPoint: function(t, e) {
        var i, s, a, r = z.maps[this.params.map].projection,
          n = r.centralMeridian;
        return i = Y[r.type](t, e, n), !!(s = this.getInsetForPoint(i.x, i.y)) && (a = s.bbox, i.x = (i.x - a[0].x) / (a[1].x - a[0].x) * s.width * this.scale, i.y = (i.y - a[0].y) / (a[1].y - a[0].y) * s.height * this.scale, {
          x: i.x + this.transX * this.scale + s.left * this.scale,
          y: i.y + this.transY * this.scale + s.top * this.scale
        })
      },
      getInsetForPoint: function(t, e) {
        var i, s, a = z.maps[this.params.map].insets;
        for (i = 0; i < a.length; i++)
          if (t > (s = a[i].bbox)[0].x && t < s[1].x && e > s[0].y && e < s[1].y) return a[i]
      },
      getMarkerPosition: function(t) {
        var e = t.coords;
        return z.maps[this.params.map].projection ? this.coordsToPoint.apply(this, e) : {
          x: e[0] * this.scale + this.transX * this.scale,
          y: e[1] * this.scale + this.transY * this.scale
        }
      },
      repositionLines: function() {
        var t = !1,
          e = !1;
        for (var i in this.lines) {
          for (var s in this.markers) {
            var a = this.markers[s];
            a.config.name === this.lines[i].config.from && (t = this.getMarkerPosition(a.config)), a.config.name === this.lines[i].config.to && (e = this.getMarkerPosition(a.config))
          }!1 !== t && !1 !== e && this.lines[i].element.setStyle({
            x1: t.x,
            y1: t.y,
            x2: e.x,
            y2: e.y
          })
        }
      },
      repositionMarkers: function() {
        var t;
        for (var e in this.markers) !1 !== (t = this.getMarkerPosition(this.markers[e].config)) && this.markers[e].element.setStyle({
          cx: t.x,
          cy: t.y
        })
      },
      repositionLabels: function() {
        var t = this.params.labels;
        if (t) {
          if (t.regions)
            for (var e in this.regions) this.regions[e].element.updateLabelPosition();
          if (t.markers)
            for (var i in this.markers) this.markers[i].element.updateLabelPosition()
        }
      },
      visualizeData: function(t) {
        f.isObj(t) && (this.dataVisualization = new L(t, this))
      }
    }),
    A = {
      onViewportChange: "viewport:changed",
      onRegionSelected: "region:select",
      onMarkerSelected: "marker:select",
      onRegionTooltipShow: "region.tooltip:show",
      onMarkerTooltipShow: "marker.tooltip:show",
      onLoaded: "map:loaded"
    },
    z = function() {
      function t(e) {
        if (void 0 === e && (e = {}), this.params = f.mergeDeeply(t.defaults, e), !t.maps[this.params.map]) throw new Error("Attempt to use map which was not loaded: " + e.map);
        this.mapData = t.maps[this.params.map], this.regions = {}, this.markers = {}, this.lines = {}, this.defaultWidth = this.mapData.width, this.defaultHeight = this.mapData.height, this.height = 0, this.width = 0, this.scale = 1, this.baseScale = 1, this.transX = 0, this.transY = 0, this.baseTransX = 0, this.baseTransY = 0, this.selector = e.selector, "loading" !== window.document.readyState ? this.init(e.selector) : window.addEventListener("DOMContentLoaded", this.init.bind(this, e.selector))
      }
      var e = t.prototype;
      return e.init = function(t) {
        var e = this.params;
        this.container = f.$(t).addClass("jvm-container"), this.canvas = new w(this.container, this.width, this.height), this.setBackgroundColor(e.backgroundColor), this.handleContainerEvents(), this.createRegions(), this.updateSize(), this.createLines(e.lines || {}, e.markers || {}), this.createMarkers(e.markers), this.handleElementEvents(), this.repositionLabels(), e.showTooltip && this.createTooltip(), e.zoomButtons && this.handleZoomButtons(), e.selectedRegions && this.setSelected("regions", e.selectedRegions), e.selectedMarkers && this.setSelected("markers", e.selectedMarkers), e.focusOn && this.setFocus(e.focusOn), e.visualizeData && this.visualizeData(e.visualizeData), e.bindTouchEvents && ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch) && this.bindContainerTouchEvents(), e.series && (this.container.append(this.legendHorizontal = f.createElement("div", "jvm-series-container jvm-series-h")).append(this.legendVertical = f.createElement("div", "jvm-series-container jvm-series-v")), this.createSeries()), this.emit("map:loaded", [this])
      }, e.emit = function(t, e) {
        for (var i in A) A[i] === t && f.isFunc(this.params[i]) && this.params[i].apply(this, e)
      }, e.setBackgroundColor = function(t) {
        this.container.css({
          backgroundColor: t
        })
      }, e.getSelected = function(t) {
        var e, i = [];
        for (e in this[t]) this[t][e].element.isSelected && i.push(e);
        return i
      }, e.clearSelected = function(t) {
        var e = this;
        this.getSelected(t).forEach((function(i) {
          e[t][i].element.select(!1)
        }))
      }, e.setSelected = function(t, e) {
        var i = this;
        e.forEach((function(e) {
          i[t][e] && i[t][e].element.select(!0)
        }))
      }, e.getSelectedRegions = function() {
        return this.getSelected("regions")
      }, e.clearSelectedRegions = function() {
        var t = this;
        this.getSelected("regions").forEach((function(e) {
          t.regions[e].element.select(!1)
        }))
      }, e.getSelectedMarkers = function() {
        return this.getSelected("markers")
      }, e.clearSelectedMarkers = function() {
        var t = this;
        this.getSelected("markers").forEach((function(e) {
          t.markers[e].element.select(!1)
        }))
      }, e.addMarker = function(t) {
        console.warn("`addMarker` method is depreacted, please use `addMarkers` instead."), this.createMarkers([t], !0)
      }, e.addMarkers = function(t) {
        this.createMarkers(t, !0)
      }, e.removeMarkers = function(t) {
        var e = this;
        t || (t = Object.keys(this.markers)), t.forEach((function(t) {
          e.markers[t].element.remove(), delete e.markers[t]
        }))
      }, e.addLine = function(t, e, i) {
        void 0 === i && (i = {}), this.createLines([{
          from: t,
          to: e,
          style: i
        }], this.markers, !0)
      }, e.reset = function() {
        for (var t in this.series)
          for (var e = 0; e < this.series[t].length; e++) this.series[t][e].clear();
        this.legendHorizontal && (f.removeElement(this.legendHorizontal), this.legendHorizontal = null), this.legendVertical && (f.removeElement(this.legendVertical), this.legendVertical = null), this.scale = this.baseScale, this.transX = this.baseTransX, this.transY = this.baseTransY, this.applyTransform(), this.clearSelectedMarkers(), this.clearSelectedRegions(), this.removeMarkers()
      }, e.destroy = function(t) {
        var e = this;
        void 0 === t && (t = !0);
        var i = a(),
          r = this.tooltip.selector,
          n = Object.keys;
        f.removeElement(r), n(i).forEach((function(t) {
          s(i[t].selector, t, i[t].handler)
        })), t && n(this).forEach((function(t) {
          try {
            delete e[t]
          } catch (t) {}
        }))
      }, e.extend = function(e, i) {
        t.prototype[e] = i
      }, e.getUtils = function() {
        return f
      }, t
    }();
  z.maps = {}, z.defaults = {
    map: "world",
    backgroundColor: "tranparent",
    draggable: !0,
    zoomButtons: !0,
    zoomOnScroll: !0,
    zoomOnScrollSpeed: 3,
    zoomMax: 12,
    zoomMin: 1,
    zoomAnimate: !0,
    showTooltip: !0,
    zoomStep: 1.5,
    bindTouchEvents: !0,
    lineStyle: {
      stroke: "#808080",
      strokeWidth: 1,
      strokeLinecap: "round"
    },
    markersSelectable: !1,
    markersSelectableOne: !1,
    markerStyle: {
      initial: {
        r: 7,
        fill: "#374151",
        fillOpacity: 1,
        stroke: "#FFF",
        strokeWidth: 5,
        strokeOpacity: .5
      },
      hover: {
        fill: "#3cc0ff",
        cursor: "pointer"
      },
      selected: {
        fill: "blue"
      },
      selectedHover: {}
    },
    markerLabelStyle: {
      initial: {
        fontFamily: "Verdana",
        fontSize: 12,
        fontWeight: 500,
        cursor: "default",
        fill: "#374151"
      },
      hover: {
        cursor: "pointer"
      },
      selected: {},
      selectedHover: {}
    },
    regionsSelectable: !1,
    regionsSelectableOne: !1,
    regionStyle: {
      initial: {
        fill: "#dee2e8",
        fillOpacity: 1,
        stroke: "none",
        strokeWidth: 0
      },
      hover: {
        fillOpacity: .7,
        cursor: "pointer"
      },
      selected: {
        fill: "#9ca3af"
      },
      selectedHover: {}
    },
    regionLabelStyle: {
      initial: {
        fontFamily: "Verdana",
        fontSize: "12",
        fontWeight: "bold",
        cursor: "default",
        fill: "#35373e"
      },
      hover: {
        cursor: "pointer"
      }
    }
  }, Object.assign(z.prototype, T);
  var P = function() {
    function t(t) {
      if (void 0 === t && (t = {}), !t.selector) throw new Error("Selector is not given.");
      return new z(t)
    }
    return t.prototype.addMap = function(t, e) {
      z.maps[t] = e
    }, t
  }();
  return window.jsVectorMap = P
}));


jsVectorMap.prototype.addMap("world", {
  insets: [{
    width: 900,
    top: 0,
    left: 0,
    height: 440.70631074413296,
    bbox: [{
      y: -12671671.123330014,
      x: -20004297.151525836
    }, {
      y: 6930392.025135122,
      x: 20026572.39474939
    }]
  }],
  paths: {
    BD: {
      path: "M651.84,230.21l-0.6,-2.0l-1.36,-1.71l-2.31,-0.11l-0.41,0.48l0.2,0.94l-0.53,0.99l-0.72,-0.36l-0.68,0.35l-1.2,-0.36l-0.37,-2.0l-0.81,-1.86l0.39,-1.46l-0.22,-0.47l-1.14,-0.53l0.29,-0.5l1.48,-0.94l0.03,-0.65l-1.55,-1.22l0.55,-1.14l1.61,0.94l1.04,0.15l0.18,1.54l0.34,0.35l5.64,0.63l-0.84,1.64l-1.22,0.34l-0.77,1.51l0.07,0.47l1.37,1.37l0.67,-0.19l0.42,-1.39l1.21,3.84l-0.03,1.21l-0.33,-0.15l-0.4,0.28Z",
      name: "Bangladesh"
    },
    BE: {
      path: "M429.29,144.05l1.91,0.24l2.1,-0.63l2.63,1.99l-0.21,1.66l-0.69,0.4l-0.18,1.2l-1.66,-1.13l-1.39,0.15l-2.73,-2.7l-1.17,-0.18l-0.16,-0.52l1.54,-0.5Z",
      name: "Belgium"
    },
    BF: {
      path: "M421.42,247.64l-0.11,0.95l0.34,1.16l1.4,1.71l0.07,1.1l0.32,0.37l2.55,0.51l-0.04,1.28l-0.38,0.53l-1.07,0.21l-0.72,1.18l-0.63,0.21l-3.22,-0.25l-0.94,0.39l-5.4,-0.05l-0.39,0.38l0.16,2.73l-1.23,-0.43l-1.17,0.1l-0.89,0.57l-2.27,-1.72l-0.13,-1.11l0.61,-0.96l0.02,-0.93l1.87,-1.98l0.44,-1.81l0.43,-0.39l1.28,0.26l1.05,-0.52l0.47,-0.73l1.84,-1.09l0.55,-0.83l2.2,-1.0l1.15,-0.3l0.72,0.45l1.13,-0.01Z",
      name: "Burkina Faso"
    },
    BG: {
      path: "M491.65,168.18l-0.86,0.88l-0.91,2.17l0.48,1.34l-1.6,-0.24l-2.55,0.95l-0.28,1.51l-1.8,0.22l-2.0,-1.0l-1.92,0.79l-1.42,-0.07l-0.15,-1.63l-1.05,-0.97l0.0,-0.8l1.2,-1.57l0.01,-0.56l-1.14,-1.23l-0.05,-0.94l0.88,0.97l0.88,-0.2l1.91,0.47l3.68,0.16l1.42,-0.81l2.72,-0.66l2.55,1.24Z",
      name: "Bulgaria"
    },
    BA: {
      path: "M463.49,163.65l2.1,0.5l1.72,-0.03l1.52,0.68l-0.36,0.78l0.08,0.45l1.04,1.02l-0.25,0.98l-1.81,1.15l-0.38,1.38l-1.67,-0.87l-0.89,-1.2l-2.11,-1.83l-1.63,-2.22l0.23,-0.57l0.48,0.38l0.55,-0.06l0.43,-0.51l0.94,-0.06Z",
      name: "Bosnia and Herz."
    },
    BN: {
      path: "M707.48,273.58l0.68,-0.65l1.41,-0.91l-0.15,1.63l-0.81,-0.05l-0.61,0.58l-0.53,-0.6Z",
      name: "Brunei"
    },
    BO: {
      path: "M263.83,340.69l-3.09,-0.23l-0.38,0.23l-0.7,1.52l-1.31,-1.53l-3.28,-0.64l-2.37,2.4l-1.31,0.26l-0.88,-3.26l-1.3,-2.86l0.74,-2.37l-0.13,-0.43l-1.2,-1.01l-0.37,-1.89l-1.08,-1.55l1.45,-2.56l-0.96,-2.33l0.47,-1.06l-0.34,-0.73l0.91,-1.32l0.16,-3.84l0.5,-1.18l-1.81,-3.41l2.46,0.07l0.8,-0.85l3.4,-1.91l2.66,-0.35l-0.19,1.38l0.3,1.07l-0.05,1.97l2.72,2.27l2.88,0.49l0.89,0.86l1.79,0.58l0.98,0.7l1.71,0.05l1.17,0.61l0.6,2.7l-0.7,0.54l0.96,2.99l0.37,0.28l4.3,0.1l-0.25,1.2l0.27,1.02l1.43,0.9l0.5,1.35l-0.41,1.86l-0.65,1.08l0.12,1.35l-2.69,-1.65l-2.4,-0.03l-4.36,0.76l-1.49,2.5l-0.11,1.52l-0.75,2.37Z",
      name: "Bolivia"
    },
    JP: {
      path: "M781.12,166.87l1.81,0.68l1.62,-0.97l0.39,2.42l-3.35,0.75l-2.23,2.88l-3.63,-1.9l-0.56,0.2l-1.26,3.05l-2.16,0.03l-0.29,-2.51l1.08,-2.03l2.45,-0.16l0.37,-0.33l1.25,-5.94l2.47,2.71l2.03,1.12ZM773.56,187.34l-0.91,2.22l0.37,1.52l-1.14,1.75l-3.02,1.26l-4.58,0.27l-3.34,3.01l-1.25,-0.8l-0.09,-1.9l-0.46,-0.38l-4.35,0.62l-3.0,1.32l-2.85,0.05l-0.37,0.27l0.13,0.44l2.32,1.89l-1.54,4.34l-1.26,0.9l-0.79,-0.7l0.56,-2.27l-0.21,-0.45l-1.47,-0.75l-0.74,-1.4l2.12,-0.84l1.26,-1.7l2.45,-1.42l1.83,-1.91l4.78,-0.81l2.6,0.57l0.44,-0.21l2.39,-4.66l1.29,1.06l0.5,0.01l5.1,-4.02l1.69,-3.73l-0.38,-3.4l0.9,-1.61l2.14,-0.44l1.23,3.72l-0.07,2.18l-2.23,2.84l-0.04,3.16ZM757.78,196.26l0.19,0.56l-1.01,1.21l-1.16,-0.68l-1.28,0.65l-0.69,1.45l-1.02,-0.5l0.01,-0.93l1.14,-1.38l1.57,0.14l0.85,-0.98l1.4,0.46Z",
      name: "Japan"
    },
    BI: {
      path: "M495.45,295.49l-1.08,-2.99l1.14,-0.11l0.64,-1.19l0.76,0.09l0.65,1.83l-2.1,2.36Z",
      name: "Burundi"
    },
    BJ: {
      path: "M429.57,255.75l-0.05,0.8l0.5,1.34l-0.42,0.86l0.17,0.79l-1.81,2.12l-0.57,1.76l-0.08,5.42l-1.41,0.2l-0.48,-1.36l0.11,-5.71l-0.52,-0.7l-0.2,-1.35l-1.48,-1.48l0.21,-0.9l0.89,-0.43l0.42,-0.92l1.27,-0.36l1.22,-1.34l0.61,-0.0l1.62,1.24Z",
      name: "Benin"
    },
    BT: {
      path: "M650.32,213.86l0.84,0.71l-0.12,1.1l-3.76,-0.11l-1.57,0.4l-1.93,-0.87l1.48,-1.96l1.13,-0.57l1.63,0.57l1.33,0.08l0.99,0.65Z",
      name: "Bhutan"
    },
    JM: {
      path: "M228.38,239.28l-0.8,0.4l-2.26,-1.06l0.84,-0.23l2.14,0.3l1.17,0.56l-1.08,0.03Z",
      name: "Jamaica"
    },
    BW: {
      path: "M483.92,330.07l2.27,4.01l2.83,2.86l0.96,0.31l0.78,2.43l2.13,0.61l1.02,0.76l-3.0,1.64l-2.32,2.02l-1.54,2.69l-1.52,0.45l-0.64,1.94l-1.34,0.52l-1.85,-0.12l-1.21,-0.74l-1.35,-0.3l-1.22,0.62l-0.75,1.37l-2.31,1.9l-1.4,0.21l-0.35,-0.59l0.16,-1.75l-1.48,-2.54l-0.62,-0.43l-0.0,-7.1l2.08,-0.08l0.39,-0.4l0.07,-8.9l5.19,-0.93l0.8,0.89l0.51,0.07l1.5,-0.95l2.21,-0.49Z",
      name: "Botswana"
    },
    BR: {
      path: "M259.98,275.05l3.24,0.7l0.65,-0.53l4.55,-1.32l1.08,-1.06l-0.02,-0.63l0.55,-0.05l0.28,0.28l-0.26,0.87l0.22,0.48l0.73,0.32l0.4,0.81l-0.62,0.86l-0.4,2.13l0.82,2.56l1.69,1.43l1.43,0.2l3.17,-1.68l3.18,0.3l0.65,-0.75l-0.27,-0.92l1.9,-0.09l2.39,0.99l1.06,-0.61l0.84,0.78l1.2,-0.18l1.18,-1.06l0.84,-1.94l1.36,-2.11l0.37,-0.05l1.89,5.45l1.33,0.59l0.05,1.28l-1.77,1.94l0.02,0.56l1.02,0.87l4.07,0.36l0.08,2.16l0.66,0.29l1.74,-1.5l6.97,2.32l1.02,1.22l-0.35,1.18l0.49,0.5l2.81,-0.74l4.77,1.3l3.75,-0.08l3.57,2.0l3.29,2.86l1.93,0.72l2.12,0.12l0.71,0.62l1.21,4.51l-0.95,3.98l-4.72,5.06l-1.64,2.92l-1.72,2.05l-0.8,0.3l-0.72,2.03l0.18,4.75l-0.94,5.53l-0.81,1.13l-0.43,3.36l-2.55,3.5l-0.4,2.51l-1.86,1.04l-0.67,1.53l-2.54,0.01l-3.94,1.01l-1.83,1.2l-2.87,0.82l-3.03,2.19l-2.2,2.83l-0.36,2.0l0.4,1.58l-0.44,2.6l-0.51,1.2l-1.77,1.54l-2.75,4.78l-3.83,3.42l-1.24,2.74l-1.18,1.15l-0.36,-0.83l0.95,-1.14l0.01,-0.5l-1.52,-1.97l-4.56,-3.32l-1.03,-0.0l-2.38,-2.02l-0.81,-0.0l5.34,-5.45l3.77,-2.58l0.22,-2.46l-1.35,-1.81l-0.91,0.07l0.58,-2.33l0.01,-1.54l-1.11,-0.83l-1.75,0.3l-0.44,-3.11l-0.52,-0.95l-1.88,-0.88l-1.24,0.47l-2.17,-0.41l0.15,-3.21l-0.62,-1.34l0.66,-0.73l-0.22,-1.34l0.66,-1.13l0.44,-2.04l-0.61,-1.83l-1.4,-0.86l-0.2,-0.75l0.34,-1.39l-0.38,-0.5l-4.52,-0.1l-0.72,-2.22l0.59,-0.42l-0.03,-1.1l-0.5,-0.87l-0.32,-1.7l-1.45,-0.76l-1.63,-0.02l-1.05,-0.72l-1.6,-0.48l-1.13,-0.99l-2.69,-0.4l-2.47,-2.06l0.13,-4.35l-0.45,-0.45l-3.46,0.5l-3.44,1.94l-0.6,0.74l-2.9,-0.17l-1.47,0.42l-0.72,-0.18l0.15,-3.52l-0.63,-0.34l-1.94,1.41l-1.87,-0.06l-0.83,-1.18l-1.37,-0.26l0.21,-1.01l-1.35,-1.49l-0.88,-1.91l0.56,-0.6l-0.0,-0.81l1.29,-0.62l0.22,-0.43l-0.22,-1.19l0.61,-0.91l0.15,-0.99l2.65,-1.58l1.99,-0.47l0.42,-0.36l2.06,0.11l0.42,-0.33l1.19,-8.0l-0.41,-1.56l-1.1,-1.0l0.01,-1.33l1.91,-0.42l0.08,-0.96l-0.33,-0.43l-1.14,-0.2l-0.02,-0.83l4.47,0.05l0.82,-0.67l0.82,1.81l0.8,0.07l1.15,1.1l2.26,-0.05l0.71,-0.83l2.78,-0.96l0.48,-1.13l1.6,-0.64l0.24,-0.47l-0.48,-0.82l-1.83,-0.19l-0.36,-3.22Z",
      name: "Brazil"
    },
    BS: {
      path: "M226.4,223.87l-0.48,-1.15l-0.84,-0.75l0.36,-1.11l0.95,1.95l0.01,1.06ZM225.56,216.43l-1.87,0.29l-0.04,-0.22l0.74,-0.14l1.17,0.06Z",
      name: "Bahamas"
    },
    BY: {
      path: "M493.84,128.32l0.29,0.7l0.49,0.23l1.19,-0.38l2.09,0.72l0.19,1.26l-0.45,1.24l1.57,2.26l0.89,0.59l0.17,0.81l1.58,0.56l0.4,0.5l-0.53,0.41l-1.87,-0.11l-0.73,0.38l-0.13,0.52l1.04,2.74l-1.91,0.26l-0.89,0.99l-0.11,1.18l-2.73,-0.04l-0.53,-0.62l-0.52,-0.08l-0.75,0.46l-0.91,-0.42l-1.92,-0.07l-2.75,-0.79l-2.6,-0.28l-2.0,0.07l-1.5,0.92l-0.67,0.07l-0.08,-1.22l-0.59,-1.19l1.36,-0.88l0.01,-1.35l-0.7,-1.41l-0.07,-1.0l2.16,-0.02l2.72,-1.3l0.75,-2.04l1.91,-1.04l0.2,-0.41l-0.19,-1.25l3.8,-1.78l2.3,0.77Z",
      name: "Belarus"
    },
    BZ: {
      path: "M198.03,244.38l0.1,-4.49l0.69,-0.06l0.74,-1.3l0.34,0.28l-0.4,1.3l0.17,0.58l-0.34,2.25l-1.3,1.42Z",
      name: "Belize"
    },
    RU: {
      path: "M491.55,115.25l2.55,-1.85l-0.01,-0.65l-2.2,-1.5l7.32,-6.76l1.03,-2.11l-0.13,-0.49l-3.46,-2.52l0.86,-2.7l-2.11,-2.81l1.56,-3.67l-2.77,-4.52l2.15,-2.99l-0.08,-0.55l-3.65,-2.73l0.3,-2.54l1.81,-0.37l4.26,-1.77l2.42,-1.45l4.06,2.61l6.79,1.04l9.34,4.85l1.78,1.88l0.14,2.46l-2.55,2.02l-3.9,1.06l-11.07,-3.14l-2.06,0.53l-0.13,0.7l3.94,2.94l0.31,5.86l0.26,0.36l5.14,2.24l0.58,-0.29l0.32,-1.94l-1.35,-1.78l1.13,-1.09l6.13,2.42l2.11,-0.98l0.18,-0.56l-1.51,-2.67l5.41,-3.76l2.07,0.22l2.26,1.41l0.57,-0.16l1.46,-2.87l-0.05,-0.44l-1.92,-2.32l1.12,-2.32l-1.32,-2.27l5.87,1.16l1.04,1.75l-2.59,0.43l-0.33,0.4l0.02,2.36l2.46,1.83l3.87,-0.91l0.86,-2.8l13.69,-5.65l0.99,0.11l-1.92,2.06l0.23,0.67l3.11,0.45l2.0,-1.48l4.56,-0.12l3.64,-1.73l2.65,2.44l0.56,-0.01l2.85,-2.88l-0.01,-0.57l-2.35,-2.29l0.9,-1.01l7.14,1.3l3.41,1.36l9.05,4.97l0.51,-0.11l1.67,-2.27l-0.05,-0.53l-2.43,-2.21l-0.06,-0.78l-0.34,-0.36l-2.52,-0.36l0.64,-1.93l-1.32,-3.46l-0.06,-1.21l4.48,-4.06l1.69,-4.29l1.6,-0.81l6.23,1.18l0.44,2.21l-2.29,3.64l0.06,0.5l1.47,1.39l0.76,3.0l-0.56,6.03l2.69,2.82l-0.96,2.57l-4.86,5.95l0.23,0.64l2.86,0.61l0.42,-0.17l0.93,-1.4l2.64,-1.03l0.87,-2.24l2.09,-1.96l0.07,-0.5l-1.36,-2.28l1.09,-2.69l-0.32,-0.55l-2.47,-0.33l-0.5,-2.06l1.94,-4.38l-0.06,-0.42l-2.96,-3.4l4.12,-2.88l0.16,-0.4l-0.51,-2.93l0.54,-0.05l1.13,2.25l-0.96,4.35l0.27,0.47l2.68,0.84l0.5,-0.51l-1.02,-2.99l3.79,-1.66l5.01,-0.24l4.53,2.61l0.48,-0.06l0.07,-0.48l-2.18,-3.82l-0.23,-4.67l3.98,-0.9l5.97,0.21l5.49,-0.64l0.27,-0.65l-1.83,-2.31l2.56,-2.9l2.87,-0.17l4.8,-2.47l6.54,-0.67l1.03,-1.42l6.25,-0.45l2.32,1.11l5.53,-2.7l4.5,0.08l0.39,-0.28l0.66,-2.15l2.26,-2.12l5.69,-2.11l3.21,1.29l-2.46,0.94l-0.25,0.42l0.34,0.35l5.41,0.77l0.61,2.33l0.58,0.25l2.2,-1.22l7.13,0.07l5.51,2.47l1.79,1.72l-0.53,2.24l-9.16,4.15l-1.97,1.52l0.16,0.71l6.77,1.91l2.16,-0.78l1.13,2.74l0.67,0.11l1.01,-1.15l3.81,-0.73l7.7,0.77l0.54,1.99l0.36,0.29l10.47,0.71l0.43,-0.38l0.13,-3.23l4.87,0.78l3.95,-0.02l3.83,2.4l1.03,2.71l-1.35,1.79l0.02,0.5l3.15,3.64l4.07,1.96l0.53,-0.18l2.23,-4.47l3.95,1.93l4.16,-1.21l4.73,1.39l2.05,-1.26l3.94,0.62l0.43,-0.55l-1.68,-4.02l2.89,-1.8l22.31,3.03l2.16,2.75l6.55,3.51l10.29,-0.81l4.82,0.73l1.85,1.66l-0.29,3.08l0.25,0.41l3.08,1.26l3.56,-0.88l4.35,-0.11l4.8,0.87l4.57,-0.47l4.23,3.79l0.43,0.07l3.1,-1.4l0.16,-0.6l-1.88,-2.62l0.85,-1.52l7.71,1.21l5.22,-0.26l7.09,2.09l9.59,5.22l6.35,4.11l-0.2,2.38l1.88,1.41l0.6,-0.42l-0.48,-2.53l6.15,0.57l4.4,3.51l-1.97,1.43l-4.0,0.41l-0.36,0.39l-0.06,3.79l-0.74,0.62l-2.07,-0.11l-1.91,-1.39l-3.14,-1.11l-0.78,-1.85l-2.72,-0.68l-2.63,0.49l-1.04,-1.1l0.46,-1.31l-0.5,-0.51l-3.0,0.98l-0.22,0.58l0.99,1.7l-1.21,1.48l-3.04,1.68l-3.12,-0.28l-0.4,0.23l0.09,0.46l2.2,2.09l1.46,3.2l1.15,1.1l0.24,1.33l-0.42,0.67l-4.63,-0.77l-6.96,2.9l-2.19,0.44l-7.6,5.06l-0.84,1.45l-3.61,-2.37l-6.24,2.82l-0.94,-1.15l-0.53,-0.08l-2.28,1.52l-3.2,-0.49l-0.44,0.27l-0.78,2.37l-3.05,3.78l0.09,1.47l0.29,0.36l2.54,0.72l-0.29,4.53l-1.97,0.11l-0.35,0.26l-1.07,2.94l0.8,1.45l-3.91,1.58l-1.05,3.95l-3.48,0.77l-0.3,0.3l-0.72,3.29l-3.09,2.65l-0.7,-1.74l-2.44,-12.44l1.16,-4.71l2.04,-2.06l0.22,-1.64l3.8,-0.86l4.46,-4.61l4.28,-3.81l4.48,-3.01l2.17,-5.63l-0.42,-0.54l-3.04,0.33l-1.77,3.31l-5.86,3.86l-1.86,-4.25l-0.45,-0.23l-6.46,1.3l-6.47,6.44l-0.01,0.55l1.58,1.74l-8.24,1.17l0.15,-2.2l-0.34,-0.42l-3.89,-0.56l-3.25,1.81l-7.62,-0.62l-8.45,1.19l-17.71,15.41l0.22,0.7l3.74,0.41l1.36,2.17l2.43,0.76l1.88,-1.68l2.4,0.2l3.4,3.54l0.08,2.6l-1.95,3.42l-0.21,3.9l-1.1,5.06l-3.71,4.54l-0.87,2.21l-8.29,8.89l-3.19,1.7l-1.32,0.03l-1.45,-1.36l-0.49,-0.04l-2.27,1.5l0.41,-3.65l-0.59,-2.47l1.75,-0.89l2.91,0.53l0.42,-0.2l1.68,-3.03l0.87,-3.46l0.97,-1.18l1.32,-2.88l-0.45,-0.56l-4.14,0.95l-2.19,1.25l-3.41,-0.0l-1.06,-2.93l-2.97,-2.3l-4.28,-1.06l-1.75,-5.07l-2.66,-5.01l-2.29,-1.29l-3.75,-1.01l-3.44,0.08l-3.18,0.62l-2.24,1.77l0.05,0.66l1.18,0.69l0.02,1.43l-1.33,1.05l-2.26,3.51l-0.04,1.43l-3.16,1.84l-2.82,-1.16l-3.01,0.23l-1.35,-1.07l-1.5,-0.35l-3.9,2.31l-3.22,0.52l-2.27,0.79l-3.05,-0.51l-2.21,0.03l-1.48,-1.6l-2.6,-1.63l-2.63,-0.43l-5.46,1.01l-3.23,-1.25l-0.72,-2.57l-5.2,-1.24l-2.75,-1.36l-0.5,0.12l-2.59,3.45l0.84,2.1l-2.06,1.93l-3.41,-0.77l-2.42,-0.12l-1.83,-1.54l-2.53,-0.05l-2.42,-0.98l-3.86,1.57l-4.72,2.78l-3.3,0.75l-1.55,-1.92l-3.0,0.41l-1.11,-1.33l-1.62,-0.59l-1.31,-1.94l-1.38,-0.6l-3.7,0.79l-3.31,-1.83l-0.51,0.11l-0.99,1.29l-5.29,-8.05l-2.96,-2.48l0.65,-0.77l0.01,-0.51l-0.5,-0.11l-6.2,3.21l-1.84,0.15l0.15,-1.39l-0.26,-0.42l-3.22,-1.17l-2.46,0.7l-0.69,-3.16l-0.32,-0.31l-4.5,-0.75l-2.47,1.47l-6.19,1.27l-1.29,0.86l-9.51,1.3l-1.15,1.17l-0.03,0.53l1.47,1.9l-1.89,0.69l-0.22,0.56l0.31,0.6l-2.11,1.44l0.03,0.68l3.75,2.12l-0.39,0.98l-3.23,-0.13l-0.86,0.86l-3.09,-1.59l-3.97,0.07l-2.66,1.35l-8.32,-3.56l-4.07,0.06l-5.39,3.68l-0.39,2.0l-2.03,-1.5l-0.59,0.13l-2.0,3.59l0.57,0.93l-1.28,2.16l0.06,0.48l2.13,2.17l1.95,0.04l1.37,1.82l-0.23,1.46l0.25,0.43l0.83,0.33l-0.8,1.31l-2.49,0.62l-2.49,3.2l0.0,0.49l2.17,2.78l-0.15,2.18l2.5,3.24l-1.58,1.59l-0.7,-0.13l-1.63,-1.72l-2.29,-0.84l-0.94,-1.31l-2.34,-0.63l-1.48,0.4l-0.43,-0.47l-3.51,-1.48l-5.76,-1.01l-0.45,0.19l-2.89,-2.34l-2.9,-1.2l-1.53,-1.29l1.29,-0.43l2.08,-2.61l-0.05,-0.55l-0.89,-0.79l3.05,-1.06l0.27,-0.42l-0.07,-0.69l-0.49,-0.35l-1.73,0.39l0.04,-0.68l1.04,-0.72l2.66,-0.48l0.4,-1.32l-0.5,-1.6l0.92,-1.54l0.03,-1.17l-0.29,-0.37l-3.69,-1.06l-1.41,0.02l-1.42,-1.41l-2.19,0.38l-2.77,-1.01l-0.03,-0.59l-0.89,-1.43l-2.0,-0.32l-0.11,-0.54l0.49,-0.53l0.01,-0.53l-1.6,-1.9l-3.58,0.02l-0.88,0.73l-0.46,-0.07l-1.0,-2.79l2.22,-0.02l0.97,-0.74l0.07,-0.57l-0.9,-1.04l-1.35,-0.48l-0.11,-0.7l-0.95,-0.58l-1.38,-1.99l0.46,-0.98l-0.51,-1.96l-2.45,-0.84l-1.21,0.3l-0.46,-0.76l-2.46,-0.83l-0.72,-1.87l-0.21,-1.69l-0.99,-0.85l0.85,-1.17l-0.7,-3.21l1.66,-1.97l-0.16,-0.79ZM749.2,170.72l-0.6,0.4l-0.13,0.16l-0.01,-0.51l0.74,-0.05ZM874.85,67.94l-5.63,0.48l-0.26,-0.84l3.15,-1.89l1.94,0.01l3.19,1.16l-2.39,1.09ZM797.39,48.49l-2.0,1.36l-3.8,-0.42l-4.25,-1.8l0.35,-0.97l9.69,1.83ZM783.67,46.12l-1.63,3.09l-8.98,-0.13l-4.09,1.14l-4.54,-2.97l1.16,-3.01l3.05,-0.89l6.5,0.22l8.54,2.56ZM778.2,134.98l-0.56,-0.9l0.27,-0.12l0.29,1.01ZM778.34,135.48l0.94,3.53l-0.05,3.38l1.05,3.39l2.18,5.0l-2.89,-0.83l-0.49,0.26l-1.54,4.65l2.42,3.5l-0.04,1.13l-1.24,-1.24l-0.61,0.06l-1.09,1.61l-0.28,-1.61l0.27,-3.1l-0.28,-3.4l0.58,-2.47l0.11,-4.39l-1.46,-3.36l0.21,-4.32l2.15,-1.46l0.07,-0.34ZM771.95,56.61l1.76,-1.42l2.89,-0.42l3.28,1.71l0.14,0.6l-3.27,0.03l-4.81,-0.5ZM683.76,31.09l-13.01,1.93l4.03,-6.35l1.82,-0.56l1.73,0.34l5.99,2.98l-0.56,1.66ZM670.85,27.93l-5.08,0.64l-6.86,-1.57l-3.99,-2.05l-2.1,-4.16l-2.6,-0.87l5.72,-3.5l5.2,-1.28l4.69,2.85l5.59,5.4l-0.56,4.53ZM564.15,68.94l-0.64,0.17l-7.85,-0.57l-0.86,-2.04l-4.28,-1.17l-0.28,-1.94l2.27,-0.89l0.25,-0.39l-0.08,-2.38l4.81,-3.97l-0.15,-0.7l-1.47,-0.38l5.3,-3.81l0.15,-0.44l-0.58,-1.94l5.28,-2.51l8.21,-3.27l8.28,-0.96l4.35,-1.94l4.6,-0.64l1.36,1.61l-1.34,1.28l-16.43,4.94l-7.97,4.88l-7.74,9.63l0.66,4.14l4.16,3.27ZM548.81,18.48l-5.5,1.18l-0.58,1.02l-2.59,0.84l-2.13,-1.07l1.12,-1.42l-0.3,-0.65l-2.33,-0.07l1.68,-0.36l3.47,-0.06l0.42,1.29l0.66,0.16l1.38,-1.34l2.15,-0.88l2.94,1.01l-0.39,0.36ZM477.37,133.15l-4.08,0.05l-2.56,-0.32l0.33,-0.87l3.17,-1.03l3.24,0.96l-0.09,1.23Z",
      name: "Russia"
    },
    RW: {
      path: "M497.0,288.25l0.71,1.01l-0.11,1.09l-1.63,0.03l-1.04,1.39l-0.83,-0.11l0.51,-1.2l0.08,-1.34l0.42,-0.41l0.7,0.14l1.19,-0.61Z",
      name: "Rwanda"
    },
    RS: {
      path: "M469.4,163.99l0.42,-0.5l-0.01,-0.52l-1.15,-1.63l1.43,-0.62l1.33,0.12l1.17,1.06l0.46,1.13l1.34,0.64l0.35,1.35l1.46,0.9l0.76,-0.29l0.2,0.69l-0.48,0.78l0.22,1.12l1.05,1.22l-0.77,0.8l-0.37,1.52l-1.21,0.08l0.24,-0.64l-0.39,-0.54l-2.08,-1.64l-0.9,0.05l-0.48,0.94l-2.12,-1.37l0.53,-1.6l-1.11,-1.37l0.51,-1.1l-0.41,-0.57Z",
      name: "Serbia"
    },
    LT: {
      path: "M486.93,129.3l0.17,1.12l-1.81,0.98l-0.72,2.02l-2.47,1.18l-2.1,-0.02l-0.73,-1.05l-1.06,-0.3l-0.09,-1.87l-3.56,-1.13l-0.43,-2.36l2.48,-0.94l4.12,0.22l2.25,-0.31l0.52,0.69l1.24,0.21l2.19,1.56Z",
      name: "Lithuania"
    },
    LU: {
      path: "M436.08,149.45l-0.48,-0.07l0.3,-1.28l0.27,0.4l-0.09,0.96Z",
      name: "Luxembourg"
    },
    LR: {
      path: "M399.36,265.97l0.18,1.54l-0.48,0.99l0.08,0.47l2.47,1.8l-0.33,2.8l-2.65,-1.13l-5.78,-4.61l0.58,-1.32l2.1,-2.33l0.86,-0.22l0.77,1.14l-0.14,0.85l0.59,0.87l1.0,0.14l0.76,-0.99Z",
      name: "Liberia"
    },
    RO: {
      path: "M487.53,154.23l0.6,0.24l2.87,3.98l-0.17,2.69l0.45,1.42l1.32,0.81l1.35,-0.42l0.76,0.36l0.02,0.31l-0.83,0.45l-0.59,-0.22l-0.54,0.3l-0.62,3.3l-1.0,-0.22l-2.07,-1.13l-2.95,0.71l-1.25,0.76l-3.51,-0.15l-1.89,-0.47l-0.87,0.16l-0.82,-1.3l0.29,-0.26l-0.06,-0.64l-1.09,-0.34l-0.56,0.5l-1.05,-0.64l-0.39,-1.39l-1.36,-0.65l-0.35,-1.0l-0.83,-0.75l1.54,-0.54l2.66,-4.21l2.4,-1.24l2.96,0.34l1.48,0.73l0.79,-0.45l1.78,-0.3l0.75,-0.74l0.79,0.0Z",
      name: "Romania"
    },
    GW: {
      path: "M386.23,253.6l-0.29,0.84l0.15,0.6l-2.21,0.59l-0.86,0.96l-1.04,-0.83l-1.09,-0.23l-0.54,-1.06l-0.66,-0.49l2.41,-0.48l4.13,0.1Z",
      name: "Guinea-Bissau"
    },
    GT: {
      path: "M195.08,249.77l-2.48,-0.37l-1.03,-0.45l-1.14,-0.89l0.3,-0.99l-0.24,-0.68l0.96,-1.66l2.98,-0.01l0.4,-0.37l-0.19,-1.28l-1.67,-1.4l0.51,-0.4l0.0,-1.05l3.85,0.02l-0.21,4.53l0.4,0.43l1.46,0.38l-1.48,0.98l-0.35,0.7l0.12,0.57l-2.2,1.96Z",
      name: "Guatemala"
    },
    GR: {
      path: "M487.07,174.59l-0.59,1.43l-0.37,0.21l-2.84,-0.35l-3.03,0.77l-0.18,0.68l1.28,1.23l-0.61,0.23l-1.14,0.0l-1.2,-1.39l-0.63,0.03l-0.53,1.01l0.56,1.76l1.03,1.19l-0.56,0.38l-0.05,0.62l2.52,2.12l0.02,0.87l-1.78,-0.59l-0.48,0.56l0.5,1.0l-1.07,0.2l-0.3,0.53l0.75,2.01l-0.98,0.02l-1.84,-1.12l-1.37,-4.2l-2.21,-2.95l-0.11,-0.56l1.04,-1.28l0.2,-0.95l0.85,-0.66l0.03,-0.46l1.32,-0.21l1.01,-0.64l1.22,0.05l0.65,-0.56l2.26,-0.0l1.82,-0.75l1.85,1.0l2.28,-0.28l0.35,-0.39l0.01,-0.77l0.34,0.22ZM480.49,192.16l0.58,0.4l-0.68,-0.12l0.11,-0.28ZM482.52,192.82l2.51,0.06l0.24,0.32l-1.99,0.13l-0.77,-0.51Z",
      name: "Greece"
    },
    GQ: {
      path: "M448.79,279.62l0.02,2.22l-4.09,0.0l0.69,-2.27l3.38,0.05Z",
      name: "Eq. Guinea"
    },
    GY: {
      path: "M277.42,270.07l-0.32,1.83l-1.32,0.57l-0.23,0.46l-0.28,2.0l1.11,1.82l0.83,0.19l0.32,1.25l1.13,1.62l-1.21,-0.19l-1.08,0.71l-1.77,0.5l-0.44,0.46l-0.86,-0.09l-1.32,-1.01l-0.77,-2.27l0.36,-1.9l0.68,-1.23l-0.57,-1.17l-0.74,-0.43l0.12,-1.16l-0.9,-0.69l-1.1,0.09l-1.31,-1.48l0.53,-0.72l-0.04,-0.84l1.99,-0.86l0.05,-0.59l-0.71,-0.78l0.14,-0.57l1.66,-1.24l1.36,0.77l1.41,1.49l0.06,1.15l0.37,0.38l0.8,0.05l2.06,1.86Z",
      name: "Guyana"
    },
    GE: {
      path: "M521.71,168.93l5.29,0.89l4.07,2.01l1.41,-0.44l2.07,0.56l0.68,1.1l1.07,0.55l-0.12,0.59l0.98,1.29l-1.01,-0.13l-1.81,-0.83l-0.94,0.47l-3.23,0.43l-2.29,-1.39l-2.33,0.05l0.21,-0.97l-0.76,-2.26l-1.45,-1.12l-1.43,-0.39l-0.41,-0.42Z",
      name: "Georgia"
    },
    GB: {
      path: "M412.61,118.72l-2.19,3.22l-0.0,0.45l5.13,-0.3l-0.53,2.37l-2.2,3.12l0.29,0.63l2.37,0.21l2.33,4.3l1.76,0.69l2.2,5.12l2.94,0.77l-0.23,1.62l-1.15,0.88l-0.1,0.52l0.82,1.42l-1.86,1.43l-3.3,-0.02l-4.12,0.87l-1.04,-0.58l-0.47,0.06l-1.51,1.41l-2.12,-0.34l-1.86,1.18l-0.6,-0.29l3.19,-3.0l2.16,-0.69l0.28,-0.41l-0.34,-0.36l-3.73,-0.53l-0.4,-0.76l2.2,-0.87l0.17,-0.61l-1.26,-1.67l0.36,-1.7l3.38,0.28l0.43,-0.33l0.37,-1.99l-1.79,-2.49l-3.11,-0.72l-0.38,-0.59l0.79,-1.35l-0.04,-0.46l-0.82,-0.97l-0.61,0.01l-0.68,0.84l-0.1,-2.34l-1.23,-1.88l0.85,-3.47l1.77,-2.68l1.85,0.26l2.17,-0.22ZM406.26,132.86l-1.01,1.77l-1.57,-0.59l-1.16,0.01l0.37,-1.54l-0.39,-1.39l1.45,-0.1l2.3,1.84Z",
      name: "United Kingdom"
    },
    GA: {
      path: "M453.24,279.52l-0.08,0.98l0.7,1.29l2.36,0.24l-0.98,2.63l1.18,1.79l0.25,1.78l-0.29,1.52l-0.6,0.93l-1.84,-0.09l-1.23,-1.11l-0.66,0.23l-0.15,0.84l-1.42,0.26l-1.02,0.7l-0.11,0.52l0.77,1.35l-1.34,0.97l-3.94,-4.3l-1.44,-2.45l0.06,-0.6l0.54,-0.81l1.05,-3.46l4.17,-0.07l0.4,-0.4l-0.02,-2.66l2.39,0.21l1.25,-0.27Z",
      name: "Gabon"
    },
    GN: {
      path: "M391.8,254.11l0.47,0.8l1.11,-0.32l0.98,0.7l1.07,0.2l2.26,-1.22l0.64,0.44l1.13,1.56l-0.48,1.4l0.8,0.3l-0.08,0.48l0.46,0.68l-0.35,1.36l1.05,2.61l-1.0,0.69l0.03,1.41l-0.72,-0.06l-1.08,1.0l-0.24,-0.27l0.07,-1.11l-1.05,-1.54l-1.79,0.21l-0.35,-2.01l-1.6,-2.18l-2.0,-0.0l-1.31,0.54l-1.95,2.18l-1.86,-2.19l-1.2,-0.78l-0.3,-1.11l-0.8,-0.85l0.65,-0.72l0.81,-0.03l1.64,-0.8l0.23,-1.87l2.67,0.64l0.89,-0.3l1.21,0.15Z",
      name: "Guinea"
    },
    GM: {
      path: "M379.31,251.39l0.1,-0.35l2.43,-0.07l0.74,-0.61l0.51,-0.03l0.77,0.49l-1.03,-0.3l-1.87,0.9l-1.65,-0.04ZM384.03,250.91l0.91,0.05l0.75,-0.24l-0.59,0.31l-1.08,-0.13Z",
      name: "Gambia"
    },
    GL: {
      path: "M353.02,1.2l14.69,4.67l-3.68,1.89l-22.97,0.86l-0.36,0.27l0.12,0.43l1.55,1.18l8.79,-0.66l7.48,2.07l4.86,-1.77l1.66,1.73l-2.53,3.19l-0.01,0.48l0.46,0.15l6.35,-2.2l12.06,-2.31l7.24,1.13l1.09,1.99l-9.79,4.01l-1.44,1.32l-7.87,0.98l-0.35,0.41l0.38,0.38l5.07,0.24l-2.53,3.58l-2.07,3.81l0.08,6.05l2.57,3.11l-3.22,0.2l-4.12,1.66l-0.05,0.72l4.45,2.65l0.51,3.75l-2.3,0.4l-0.25,0.64l2.79,3.69l-4.82,0.31l-0.36,0.29l0.16,0.44l2.62,1.8l-0.59,1.22l-3.3,0.7l-3.45,0.01l-0.29,0.68l3.03,3.12l0.02,1.34l-4.4,-1.73l-1.72,1.35l0.15,0.66l3.31,1.15l3.13,2.71l0.81,3.16l-3.85,0.75l-4.89,-4.26l-0.47,-0.03l-0.17,0.44l0.79,2.86l-2.71,2.21l-0.13,0.44l0.37,0.27l8.73,0.34l-12.32,6.64l-7.24,1.48l-2.94,0.08l-2.69,1.75l-3.43,4.41l-5.24,2.84l-1.73,0.18l-7.12,2.1l-2.15,2.52l-0.13,2.99l-1.19,2.45l-4.01,3.09l-0.14,0.44l0.97,2.9l-2.28,6.48l-3.1,0.2l-3.83,-3.07l-4.86,-0.02l-2.25,-1.93l-1.7,-3.79l-4.3,-4.84l-1.21,-2.49l-0.44,-3.8l-3.32,-3.63l0.84,-2.86l-1.56,-1.7l2.28,-4.6l3.83,-1.74l1.03,-1.96l0.52,-3.47l-0.59,-0.41l-4.17,2.21l-2.07,0.58l-2.72,-1.28l-0.15,-2.71l0.85,-2.09l2.01,-0.06l5.06,1.2l0.46,-0.23l-0.14,-0.49l-6.54,-4.47l-2.67,0.55l-1.58,-0.86l2.56,-4.01l-0.03,-0.48l-1.5,-1.74l-4.98,-8.5l-3.13,-1.96l0.03,-1.88l-0.24,-0.37l-6.85,-3.02l-5.36,-0.38l-12.7,0.58l-2.78,-1.57l-3.66,-2.77l5.73,-1.45l5.0,-0.28l0.38,-0.38l-0.35,-0.41l-10.67,-1.38l-5.3,-2.06l0.25,-1.54l18.41,-5.26l1.22,-2.27l-0.25,-0.55l-6.14,-1.86l1.68,-1.77l8.55,-4.03l3.59,-0.63l0.3,-0.54l-0.88,-2.27l5.47,-1.47l7.65,-0.95l7.55,-0.05l3.04,1.85l6.48,-3.27l5.81,2.22l3.56,0.5l5.16,1.94l0.5,-0.21l-0.17,-0.52l-5.71,-3.13l0.28,-2.13l8.12,-3.6l8.7,0.28l3.35,-2.34l8.71,-0.6l19.93,0.8Z",
      name: "Greenland"
    },
    KW: {
      path: "M540.81,207.91l0.37,0.86l-0.17,0.76l0.6,1.53l-0.95,0.04l-0.82,-1.28l-1.57,-0.18l1.31,-1.88l1.22,0.17Z",
      name: "Kuwait"
    },
    GH: {
      path: "M420.53,257.51l-0.01,0.72l0.96,1.2l0.24,3.73l0.59,0.95l-0.51,2.1l0.19,1.41l1.02,2.21l-6.97,2.84l-1.8,-0.57l0.04,-0.89l-1.02,-2.04l0.61,-2.65l1.07,-2.32l-0.96,-6.47l5.01,0.07l0.94,-0.39l0.61,0.11Z",
      name: "Ghana"
    },
    OM: {
      path: "M568.09,230.93l-0.91,1.67l-1.22,0.04l-0.6,0.76l-0.41,1.51l0.27,1.58l-1.16,0.05l-1.56,0.97l-0.76,1.74l-1.62,0.05l-0.98,0.65l-0.17,1.15l-0.89,0.52l-1.49,-0.18l-2.4,0.94l-2.47,-5.4l7.35,-2.71l1.67,-5.23l-1.12,-2.09l0.05,-0.83l0.67,-1.0l0.07,-1.05l0.9,-0.42l-0.05,-2.07l0.7,-0.01l1.0,1.62l1.51,1.08l3.3,0.84l1.73,2.29l0.81,0.37l-1.23,2.35l-0.99,0.79Z",
      name: "Oman"
    },
    _2: {
      path: "M531.15,258.94l1.51,0.12l5.13,-0.95l5.3,-1.48l-0.01,4.4l-2.67,3.39l-1.85,0.01l-8.04,-2.94l-2.55,-3.17l1.12,-1.71l2.04,2.34Z",
      name: "Somaliland"
    },
    _1: {
      path: "M472.77,172.64l-1.08,-1.29l0.96,-0.77l0.29,-0.83l1.98,1.64l-0.36,0.67l-1.79,0.58Z",
      name: "Kosovo"
    },
    _0: {
      path: "M504.91,192.87l0.34,0.01l0.27,-0.07l-0.29,0.26l-0.31,-0.2Z",
      name: "N. Cyprus"
    },
    JO: {
      path: "M518.64,201.38l-5.14,1.56l-0.19,0.65l2.16,2.39l-0.89,1.14l-1.71,0.34l-1.71,1.8l-2.34,-0.37l1.21,-4.32l0.56,-4.07l2.8,0.94l4.46,-2.71l0.79,2.66Z",
      name: "Jordan"
    },
    HR: {
      path: "M455.59,162.84l1.09,0.07l-0.82,0.94l-0.27,-1.01ZM456.96,162.92l0.62,-0.41l1.73,0.45l0.42,-0.4l-0.01,-0.59l0.86,-0.52l0.2,-1.05l1.63,-0.68l2.57,1.68l2.07,0.6l0.87,-0.31l1.05,1.57l-0.52,0.63l-1.05,-0.56l-1.68,0.04l-2.1,-0.5l-1.29,0.06l-0.57,0.49l-0.59,-0.47l-0.62,0.16l-0.46,1.7l1.79,2.42l2.79,2.75l-1.18,-0.87l-2.21,-0.87l-1.67,-1.78l0.13,-0.63l-1.05,-1.19l-0.32,-1.27l-1.42,-0.43Z",
      name: "Croatia"
    },
    HT: {
      path: "M237.05,238.38l-1.16,0.43l-0.91,-0.55l0.05,-0.2l2.02,0.31ZM237.53,238.43l1.06,0.12l-0.05,0.01l-1.01,-0.12ZM239.25,238.45l0.79,-0.51l0.06,-0.62l-1.02,-1.0l0.02,-0.82l-0.3,-0.4l-0.93,-0.32l3.16,0.45l0.02,1.84l-0.48,0.34l-0.08,0.58l0.54,0.72l-1.78,-0.26Z",
      name: "Haiti"
    },
    HU: {
      path: "M462.08,157.89l0.65,-1.59l-0.09,-0.44l0.64,-0.0l0.39,-0.34l0.1,-0.69l1.75,0.87l2.32,-0.37l0.43,-0.66l3.49,-0.78l0.69,-0.78l0.57,-0.14l2.57,0.93l0.67,-0.23l1.03,0.65l0.08,0.37l-1.42,0.71l-2.59,4.14l-1.8,0.53l-1.68,-0.1l-2.74,1.23l-1.85,-0.54l-2.54,-1.66l-0.66,-1.1Z",
      name: "Hungary"
    },
    HN: {
      path: "M199.6,249.52l-1.7,-1.21l0.06,-0.94l3.04,-2.14l2.37,0.28l1.27,-0.09l1.1,-0.52l1.3,0.28l1.14,-0.25l1.38,0.37l2.23,1.37l-2.36,0.93l-1.23,-0.39l-0.88,1.3l-1.28,0.99l-0.98,-0.22l-0.42,0.52l-0.96,0.05l-0.36,0.41l0.04,0.88l-0.52,0.6l-0.3,0.04l-0.3,-0.55l-0.66,-0.31l0.11,-0.67l-0.48,-0.65l-0.87,-0.26l-0.73,0.2Z",
      name: "Honduras"
    },
    PR: {
      path: "M256.17,238.73l-0.26,0.27l-2.83,0.05l-0.07,-0.55l1.95,-0.1l1.22,0.33Z",
      name: "Puerto Rico"
    },
    PS: {
      path: "M509.21,203.07l0.1,-0.06l-0.02,0.03l-0.09,0.03ZM509.36,202.91l-0.02,-0.63l-0.33,-0.16l0.31,-1.09l0.24,0.1l-0.2,1.78Z",
      name: "Palestine"
    },
    PT: {
      path: "M401.84,187.38l-0.64,0.47l-1.13,-0.35l-0.91,0.17l0.28,-1.78l-0.24,-1.78l-1.25,-0.56l-0.45,-0.84l0.17,-1.66l1.01,-1.18l0.69,-2.92l-0.04,-1.39l-0.59,-1.9l1.3,-0.85l0.84,1.35l3.1,-0.3l0.46,0.99l-1.05,0.94l-0.03,2.16l-0.41,0.57l-0.08,1.1l-0.79,0.18l-0.26,0.59l0.91,1.6l-0.63,1.75l0.76,1.09l-1.1,1.52l0.07,1.05Z",
      name: "Portugal"
    },
    PY: {
      path: "M274.9,336.12l0.74,1.52l-0.16,3.45l0.32,0.41l2.64,0.5l1.11,-0.47l1.4,0.59l0.36,0.6l0.53,3.42l1.27,0.4l0.98,-0.38l0.51,0.27l-0.0,1.18l-1.21,5.32l-2.09,1.9l-1.8,0.4l-4.71,-0.98l2.2,-3.63l-0.32,-1.5l-2.78,-1.28l-3.03,-1.94l-2.07,-0.44l-4.34,-4.06l0.91,-2.9l0.08,-1.42l1.07,-2.04l4.13,-0.72l2.18,0.03l2.05,1.17l0.03,0.59Z",
      name: "Paraguay"
    },
    PA: {
      path: "M213.8,263.68l0.26,-1.52l-0.36,-0.26l-0.01,-0.49l0.44,-0.1l0.93,1.4l1.26,0.03l0.77,0.49l1.38,-0.23l2.51,-1.11l0.86,-0.72l3.45,0.85l1.4,1.18l0.41,1.74l-0.21,0.34l-0.53,-0.12l-0.47,0.29l-0.16,0.6l-0.68,-1.28l0.45,-0.49l-0.19,-0.66l-0.47,-0.13l-0.54,-0.84l-1.5,-0.75l-1.1,0.16l-0.75,0.99l-1.62,0.84l-0.18,0.96l0.85,0.97l-0.58,0.45l-0.69,0.08l-0.34,-1.18l-1.27,0.03l-0.71,-1.05l-2.59,-0.46Z",
      name: "Panama"
    },
    PG: {
      path: "M808.58,298.86l2.54,2.56l-0.13,0.26l-0.33,0.12l-0.87,-0.78l-1.22,-2.16ZM801.41,293.04l0.5,0.29l0.26,0.27l-0.49,-0.35l-0.27,-0.21ZM803.17,294.58l0.59,0.5l0.08,1.06l-0.29,-0.91l-0.38,-0.65ZM796.68,298.41l0.52,0.75l1.43,-0.19l2.27,-1.81l-0.01,-1.43l1.12,0.16l-0.04,1.1l-0.7,1.28l-1.12,0.18l-0.62,0.79l-2.46,1.11l-1.17,-0.0l-3.08,-1.25l3.41,0.0l0.45,-0.68ZM789.15,303.55l2.31,1.8l1.59,2.61l1.34,0.13l-0.06,0.66l0.31,0.43l1.06,0.24l0.06,0.65l2.25,1.05l-1.22,0.13l-0.72,-0.63l-4.56,-0.65l-3.22,-2.87l-1.49,-2.34l-3.27,-1.1l-2.38,0.72l-1.59,0.86l-0.2,0.42l0.27,1.55l-1.55,0.68l-1.36,-0.4l-2.21,-0.09l-0.08,-15.41l8.39,2.93l2.95,2.4l0.6,1.64l4.02,1.49l0.31,0.68l-1.76,0.21l-0.33,0.52l0.55,1.68Z",
      name: "Papua New Guinea"
    },
    PE: {
      path: "M244.96,295.21l-1.26,-0.07l-0.57,0.42l-1.93,0.45l-2.98,1.75l-0.36,1.36l-0.58,0.8l0.12,1.37l-1.24,0.59l-0.22,1.22l-0.62,0.84l1.04,2.27l1.28,1.44l-0.41,0.84l0.32,0.57l1.48,0.13l1.16,1.37l2.21,0.07l1.63,-1.08l-0.13,3.02l0.3,0.4l1.14,0.29l1.31,-0.34l1.9,3.59l-0.48,0.85l-0.17,3.85l-0.94,1.59l0.35,0.75l-0.47,1.07l0.98,1.97l-2.1,3.82l-0.98,0.5l-2.17,-1.28l-0.39,-1.16l-4.95,-2.58l-4.46,-2.79l-1.84,-1.51l-0.91,-1.84l0.3,-0.96l-2.11,-3.33l-4.82,-9.68l-1.04,-1.2l-0.87,-1.94l-3.4,-2.48l0.58,-1.18l-1.13,-2.23l0.66,-1.49l1.45,-1.15l-0.6,0.98l0.07,0.92l0.47,0.36l1.74,0.03l0.97,1.17l0.54,0.07l1.42,-1.03l0.6,-1.84l1.42,-2.02l3.04,-1.04l2.73,-2.62l0.86,-1.74l-0.1,-1.87l1.44,1.02l0.9,1.25l1.06,0.59l1.7,2.73l1.86,0.31l1.45,-0.61l0.96,0.39l1.36,-0.19l1.45,0.89l-1.4,2.21l0.31,0.61l0.59,0.05l0.47,0.5Z",
      name: "Peru"
    },
    PK: {
      path: "M615.09,192.34l-1.83,1.81l-2.6,0.39l-3.73,-0.68l-1.58,1.33l-0.09,0.42l1.77,4.39l1.7,1.23l-1.69,1.27l-0.12,2.14l-2.33,2.64l-1.6,2.8l-2.46,2.67l-3.03,-0.07l-2.76,2.83l0.05,0.6l1.5,1.11l0.26,1.9l1.44,1.5l0.37,1.68l-5.01,-0.01l-1.78,1.7l-1.42,-0.52l-0.76,-1.87l-2.27,-2.15l-11.61,0.86l0.71,-2.34l3.43,-1.32l0.25,-0.44l-0.21,-1.24l-1.2,-0.65l-0.28,-2.46l-2.29,-1.14l-1.28,-1.94l2.82,0.94l2.62,-0.38l1.42,0.33l0.76,-0.56l1.71,0.19l3.25,-1.14l0.27,-0.36l0.08,-2.19l1.18,-1.32l1.68,0.0l0.58,-0.82l1.6,-0.3l1.19,0.16l0.98,-0.78l0.02,-1.88l0.93,-1.47l1.48,-0.66l0.19,-0.55l-0.66,-1.25l2.04,-0.11l0.69,-1.01l-0.02,-1.16l1.11,-1.06l-0.17,-1.78l-0.49,-1.03l1.15,-0.98l5.42,-0.91l2.6,-0.82l1.6,1.16l0.97,2.34l3.45,0.97Z",
      name: "Pakistan"
    },
    PH: {
      path: "M737.01,263.84l0.39,2.97l-0.44,1.18l-0.55,-1.53l-0.67,-0.14l-1.17,1.28l0.65,2.09l-0.42,0.69l-2.48,-1.23l-0.57,-1.49l0.65,-1.03l-0.1,-0.54l-1.59,-1.19l-0.56,0.08l-0.65,0.87l-1.23,0.0l-1.58,0.97l0.83,-1.8l2.56,-1.42l0.65,0.84l0.45,0.13l1.9,-0.69l0.56,-1.11l1.5,-0.06l0.38,-0.43l-0.09,-1.19l1.21,0.71l0.36,2.02ZM733.59,256.58l0.05,0.75l0.08,0.26l-0.8,-0.42l-0.18,-0.71l0.85,0.12ZM734.08,256.1l-0.12,-1.12l-1.0,-1.27l1.36,0.03l0.53,0.73l0.51,2.04l-1.27,-0.4ZM733.76,257.68l0.38,0.98l-0.32,0.15l-0.07,-1.13ZM724.65,238.43l1.46,0.7l0.72,-0.31l-0.32,1.17l0.79,1.71l-0.57,1.84l-1.53,1.04l-0.39,2.25l0.56,2.04l1.63,0.57l1.16,-0.27l2.71,1.23l-0.19,1.08l0.76,0.84l-0.08,0.36l-1.4,-0.9l-0.88,-1.27l-0.66,0.0l-0.38,0.55l-1.6,-1.31l-2.15,0.36l-0.87,-0.39l0.07,-0.61l0.66,-0.55l-0.01,-0.62l-0.75,-0.59l-0.72,0.44l-0.74,-0.87l-0.39,-2.49l0.32,0.27l0.66,-0.28l0.26,-3.97l0.7,-2.02l1.14,0.0ZM731.03,258.87l-0.88,0.85l-1.19,1.94l-1.05,-1.19l0.93,-1.1l0.32,-1.47l0.52,-0.06l-0.27,1.15l0.22,0.45l0.49,-0.12l1.0,-1.32l-0.08,0.85ZM726.83,255.78l0.83,0.38l1.17,-0.0l-0.02,0.48l-2.0,1.4l0.03,-2.26ZM724.81,252.09l-0.38,1.27l-1.42,-1.95l1.2,0.05l0.6,0.63ZM716.55,261.82l1.1,-0.95l0.03,-0.03l-0.28,0.36l-0.85,0.61ZM719.22,259.06l0.04,-0.06l0.8,-1.53l0.16,0.75l-1.0,0.84Z",
      name: "Philippines"
    },
    PL: {
      path: "M468.44,149.42l-1.11,-1.54l-1.86,-0.33l-0.48,-1.05l-1.72,-0.37l-0.65,0.69l-0.72,-0.36l0.11,-0.61l-0.33,-0.46l-1.75,-0.27l-1.04,-0.93l-0.94,-1.94l0.16,-1.22l-0.62,-1.8l-0.78,-1.07l0.57,-1.04l-0.48,-1.43l1.41,-0.83l6.91,-2.71l2.14,0.5l0.52,0.91l5.51,0.44l4.55,-0.05l1.07,0.31l0.48,0.84l0.15,1.58l0.65,1.2l-0.01,0.99l-1.27,0.58l-0.19,0.54l0.73,1.48l0.08,1.55l1.2,2.76l-0.17,0.58l-1.23,0.44l-2.27,2.72l0.18,0.95l-1.97,-1.03l-1.98,0.4l-1.36,-0.28l-1.24,0.58l-1.07,-0.97l-1.16,0.24Z",
      name: "Poland"
    },
    ZM: {
      path: "M481.47,313.3l0.39,0.31l2.52,0.14l0.99,1.17l2.01,0.35l1.4,-0.64l0.69,1.17l1.78,0.33l1.84,2.35l2.23,0.18l0.4,-0.43l-0.21,-2.74l-0.62,-0.3l-0.48,0.32l-1.98,-1.17l0.72,-5.29l-0.51,-1.18l0.57,-1.3l3.68,-0.62l0.26,0.63l1.21,0.63l0.9,-0.22l2.16,0.67l1.33,0.71l1.07,1.02l0.56,1.87l-0.88,2.7l0.43,2.09l-0.73,0.87l-0.76,2.37l0.59,0.68l-6.6,1.83l-0.29,0.44l0.19,1.45l-1.68,0.35l-1.43,1.02l-0.38,0.87l-0.87,0.26l-3.48,3.69l-4.16,-0.53l-1.52,-1.0l-1.77,-0.13l-1.83,0.52l-3.04,-3.4l0.11,-7.59l4.82,0.03l0.39,-0.49l-0.18,-0.76l0.33,-0.83l-0.4,-1.36l0.24,-1.05Z",
      name: "Zambia"
    },
    EH: {
      path: "M384.42,230.28l0.25,-0.79l1.06,-1.29l0.8,-3.51l3.38,-2.78l0.7,-1.81l0.06,4.84l-1.98,0.2l-0.94,1.59l0.39,3.56l-3.7,-0.01ZM392.01,218.1l0.7,-1.8l1.77,-0.24l2.09,0.34l0.95,-0.62l1.28,-0.07l-0.0,2.51l-6.79,-0.12Z",
      name: "W. Sahara"
    },
    EE: {
      path: "M485.71,115.04l2.64,0.6l2.56,0.11l-1.6,1.91l0.61,3.54l-0.81,0.87l-1.78,-0.01l-3.22,-1.76l-1.8,0.45l0.21,-1.53l-0.58,-0.41l-0.69,0.34l-1.26,-1.03l-0.17,-1.63l2.83,-0.92l3.05,-0.52Z",
      name: "Estonia"
    },
    EG: {
      path: "M492.06,205.03l1.46,0.42l2.95,-1.64l2.04,-0.21l1.53,0.3l0.59,1.19l0.69,0.04l0.41,-0.64l1.81,0.58l1.95,0.16l1.04,-0.51l1.42,4.08l-2.03,4.54l-1.66,-1.77l-1.76,-3.85l-0.64,-0.12l-0.36,0.67l1.04,2.88l3.44,6.95l1.78,3.04l2.03,2.65l-0.36,0.53l0.23,2.01l2.7,2.19l-28.41,0.0l0.0,-18.96l-0.73,-2.2l0.59,-1.56l-0.32,-1.26l0.68,-0.99l3.06,-0.04l4.82,1.52Z",
      name: "Egypt"
    },
    ZA: {
      path: "M467.14,373.21l-0.13,-1.96l-0.68,-1.56l0.7,-0.68l-0.13,-2.33l-4.56,-8.19l0.77,-0.86l0.6,0.45l0.69,1.31l2.83,0.72l1.5,-0.26l2.24,-1.39l0.19,-9.55l1.35,2.3l-0.21,1.5l0.61,1.2l0.4,0.19l1.79,-0.27l2.6,-2.07l0.69,-1.32l0.96,-0.48l2.19,1.04l2.04,0.13l1.77,-0.65l0.85,-2.12l1.38,-0.33l1.59,-2.76l2.15,-1.89l3.41,-1.87l2.0,0.45l1.02,-0.28l0.99,0.2l1.75,5.29l-0.38,3.25l-0.81,-0.23l-1.0,0.46l-0.87,1.68l-0.05,1.16l1.97,1.84l1.47,-0.29l0.69,-1.18l1.09,0.01l-0.76,3.69l-0.58,1.09l-2.2,1.79l-3.17,4.76l-2.8,2.83l-3.57,2.88l-2.53,1.05l-1.22,0.14l-0.51,0.7l-1.18,-0.32l-1.39,0.5l-2.59,-0.52l-1.61,0.33l-1.18,-0.11l-2.55,1.1l-2.1,0.44l-1.6,1.07l-0.85,0.05l-0.93,-0.89l-0.93,-0.15l-0.97,-1.13l-0.25,0.05ZM491.45,364.19l0.62,-0.93l1.48,-0.59l1.18,-2.19l-0.07,-0.49l-1.99,-1.69l-1.66,0.56l-1.43,1.14l-1.34,1.73l0.02,0.51l1.88,2.11l1.31,-0.16Z",
      name: "South Africa"
    },
    EC: {
      path: "M231.86,285.53l0.29,1.59l-0.69,1.45l-2.61,2.51l-3.13,1.11l-1.53,2.18l-0.49,1.68l-1.0,0.73l-1.02,-1.11l-1.78,-0.16l0.67,-1.15l-0.24,-0.86l1.25,-2.13l-0.54,-1.09l-0.67,-0.08l-0.72,0.87l-0.87,-0.64l0.35,-0.69l-0.36,-1.96l0.81,-0.51l0.45,-1.51l0.92,-1.57l-0.07,-0.97l2.65,-1.33l2.75,1.35l0.77,1.05l2.12,0.35l0.76,-0.32l1.96,1.21Z",
      name: "Ecuador"
    },
    AL: {
      path: "M470.32,171.8l0.74,0.03l0.92,0.89l-0.17,1.95l0.36,1.28l1.01,0.82l-1.82,2.83l-0.19,-0.61l-1.25,-0.89l-0.18,-1.2l0.53,-2.82l-0.54,-1.47l0.6,-0.83Z",
      name: "Albania"
    },
    AO: {
      path: "M461.55,300.03l1.26,3.15l1.94,2.36l2.47,-0.53l1.25,0.32l0.44,-0.18l0.93,-1.92l1.31,-0.08l0.41,-0.44l0.47,-0.0l-0.1,0.41l0.39,0.49l2.65,-0.02l0.03,1.19l0.48,1.01l-0.34,1.52l0.18,1.55l0.83,1.04l-0.13,2.85l0.54,0.39l3.96,-0.41l-0.1,1.79l0.39,1.05l-0.24,1.43l-4.7,-0.03l-0.4,0.39l-0.12,8.13l2.92,3.49l-3.83,0.88l-5.89,-0.36l-1.88,-1.24l-10.47,0.22l-1.3,-1.01l-1.85,-0.16l-2.4,0.77l-0.15,-1.06l0.33,-2.16l1.0,-3.45l1.35,-3.2l2.24,-2.8l0.33,-2.06l-0.13,-1.53l-0.8,-1.08l-1.21,-2.87l0.87,-1.62l-1.27,-4.12l-1.17,-1.53l2.47,-0.63l7.03,0.03ZM451.71,298.87l-0.47,-1.25l1.25,-1.11l0.32,0.3l-0.99,1.03l-0.12,1.03Z",
      name: "Angola"
    },
    KZ: {
      path: "M552.8,172.89l0.46,-1.27l-0.48,-1.05l-2.96,-1.19l-1.06,-2.58l-1.37,-0.87l-0.03,-0.3l1.95,0.23l0.45,-0.38l0.08,-1.96l1.75,-0.41l2.1,0.45l0.48,-0.33l0.45,-3.04l-0.45,-2.09l-0.41,-0.31l-2.42,0.15l-2.36,-0.73l-2.87,1.37l-2.17,0.61l-0.85,-0.34l0.13,-1.61l-1.6,-2.12l-2.02,-0.08l-1.78,-1.82l1.29,-2.18l-0.57,-0.95l1.62,-2.91l2.21,1.63l0.63,-0.27l0.29,-2.22l4.92,-3.43l3.71,-0.08l8.4,3.6l2.92,-1.36l3.77,-0.06l3.11,1.66l0.51,-0.11l0.6,-0.81l3.31,0.13l0.39,-0.25l0.63,-1.57l-0.17,-0.5l-3.5,-1.98l1.87,-1.27l-0.13,-1.03l1.98,-0.72l0.18,-0.62l-1.59,-2.06l0.81,-0.82l9.23,-1.18l1.33,-0.88l6.18,-1.26l2.26,-1.42l4.08,0.68l0.73,3.33l0.51,0.3l2.48,-0.8l2.79,1.02l-0.17,1.56l0.43,0.44l2.55,-0.24l4.89,-2.53l0.03,0.32l3.15,2.61l5.56,8.47l0.65,0.02l1.12,-1.46l3.15,1.74l3.76,-0.78l1.15,0.49l1.14,1.8l1.84,0.76l0.99,1.29l3.35,-0.25l1.02,1.52l-1.6,1.81l-1.93,0.28l-0.34,0.38l-0.11,3.05l-1.13,1.16l-4.75,-1.0l-0.46,0.27l-1.76,5.47l-1.1,0.59l-4.91,1.23l-0.27,0.54l2.1,4.97l-1.37,0.63l-0.23,0.41l0.13,1.13l-0.88,-0.25l-1.42,-1.13l-7.89,-0.4l-0.92,0.31l-3.73,-1.22l-1.42,0.63l-0.53,1.66l-3.72,-0.94l-1.85,0.43l-0.76,1.4l-4.65,2.62l-1.13,2.08l-0.44,0.01l-0.92,-1.4l-2.87,-0.09l-0.45,-2.14l-0.38,-0.32l-0.8,-0.01l0.0,-2.96l-3.0,-2.22l-7.31,0.58l-2.35,-2.68l-6.71,-3.69l-6.45,1.83l-0.29,0.39l0.1,10.85l-0.7,0.08l-1.62,-2.17l-1.83,-0.96l-3.11,0.59l-0.64,0.51Z",
      name: "Kazakhstan"
    },
    ET: {
      path: "M516.04,247.79l1.1,0.84l1.63,-0.45l0.68,0.47l1.63,0.03l2.01,0.94l1.73,1.66l1.64,2.07l-1.52,2.04l0.16,1.72l0.39,0.38l2.05,0.0l-0.36,1.03l2.86,3.58l8.32,3.08l1.31,0.02l-6.32,6.75l-3.1,0.11l-2.36,1.77l-1.47,0.04l-0.86,0.79l-1.38,-0.0l-1.32,-0.81l-2.29,1.05l-0.76,0.98l-3.29,-0.41l-3.07,-2.07l-1.8,-0.07l-0.62,-0.6l0.0,-1.24l-0.28,-0.38l-1.15,-0.37l-1.4,-2.59l-1.19,-0.68l-0.47,-1.0l-1.27,-1.23l-1.16,-0.22l0.43,-0.72l1.45,-0.28l0.41,-0.95l-0.03,-2.21l0.68,-2.44l1.05,-0.63l1.43,-3.06l1.57,-1.37l1.02,-2.51l0.35,-1.88l2.52,0.46l0.44,-0.24l0.58,-1.43Z",
      name: "Ethiopia"
    },
    ZW: {
      path: "M498.91,341.09l-1.11,-0.22l-0.92,0.28l-2.09,-0.44l-1.5,-1.11l-1.89,-0.43l-0.62,-1.4l-0.01,-0.84l-0.3,-0.38l-0.97,-0.25l-2.71,-2.74l-1.92,-3.32l3.83,0.45l3.73,-3.82l1.08,-0.44l0.26,-0.77l1.25,-0.9l1.41,-0.26l0.5,0.89l1.99,-0.05l1.72,1.17l1.11,0.17l1.05,0.66l0.01,2.99l-0.59,3.76l0.38,0.86l-0.23,1.23l-0.39,0.35l-0.63,1.81l-2.43,2.75Z",
      name: "Zimbabwe"
    },
    ES: {
      path: "M416.0,169.21l1.07,1.17l4.61,1.38l1.06,-0.57l2.6,1.26l2.71,-0.3l0.09,1.12l-2.14,1.8l-3.11,0.61l-0.31,0.31l-0.2,0.89l-1.54,1.69l-0.97,2.4l0.84,1.74l-1.32,1.27l-0.48,1.68l-1.88,0.65l-1.66,2.07l-5.36,-0.01l-1.79,1.08l-0.89,0.98l-0.88,-0.17l-0.79,-0.82l-0.68,-1.59l-2.37,-0.63l-0.11,-0.5l1.21,-1.82l-0.77,-1.13l0.61,-1.68l-0.76,-1.62l0.87,-0.49l0.09,-1.25l0.42,-0.6l0.03,-2.11l0.99,-0.69l0.13,-0.5l-1.03,-1.73l-1.46,-0.11l-0.61,0.38l-1.06,0.0l-0.52,-1.23l-0.53,-0.21l-1.32,0.67l-0.01,-1.49l-0.75,-0.96l3.03,-1.88l2.99,0.53l3.32,-0.02l2.63,0.51l6.01,-0.06Z",
      name: "Spain"
    },
    ER: {
      path: "M520.38,246.23l3.42,2.43l3.5,3.77l0.84,0.54l-0.95,-0.01l-3.51,-3.89l-2.33,-1.15l-1.73,-0.07l-0.91,-0.51l-1.26,0.51l-1.34,-1.02l-0.61,0.17l-0.66,1.61l-2.35,-0.43l-0.17,-0.67l1.29,-5.29l0.61,-0.61l1.95,-0.53l0.87,-1.01l1.17,2.41l0.68,2.33l1.49,1.43Z",
      name: "Eritrea"
    },
    ME: {
      path: "M468.91,172.53l-1.22,-1.02l0.47,-1.81l0.89,-0.72l2.26,1.51l-0.5,0.57l-0.75,-0.27l-1.14,1.73Z",
      name: "Montenegro"
    },
    MD: {
      path: "M488.41,153.73l1.4,-0.27l1.72,0.93l1.07,0.15l0.85,0.65l-0.14,0.84l0.96,0.85l1.12,2.47l-1.15,-0.07l-0.66,-0.41l-0.52,0.25l-0.09,0.86l-1.08,1.89l-0.27,-0.86l0.25,-1.34l-0.16,-1.6l-3.29,-4.34Z",
      name: "Moldova"
    },
    MG: {
      path: "M545.91,319.14l0.4,3.03l0.62,1.21l-0.21,1.02l-0.57,-0.8l-0.69,-0.01l-0.47,0.76l0.41,2.12l-0.18,0.87l-0.73,0.78l-0.15,2.14l-4.71,15.2l-1.06,2.88l-3.92,1.64l-3.12,-1.49l-0.6,-1.21l-0.19,-2.4l-0.86,-2.05l-0.21,-1.77l0.38,-1.62l1.21,-0.75l0.01,-0.76l1.19,-2.04l0.23,-1.66l-1.06,-2.99l-0.19,-2.21l0.81,-1.33l0.32,-1.46l4.63,-1.22l3.44,-3.0l0.85,-1.4l-0.08,-0.7l0.78,-0.04l1.38,-1.77l0.13,-1.64l0.45,-0.61l1.16,1.69l0.59,1.6Z",
      name: "Madagascar"
    },
    MA: {
      path: "M378.78,230.02l0.06,-0.59l0.92,-0.73l0.82,-1.37l-0.09,-1.04l0.79,-1.7l1.31,-1.58l0.96,-0.59l0.66,-1.55l0.09,-1.47l0.81,-1.48l1.72,-1.07l1.55,-2.69l1.16,-0.96l2.44,-0.39l1.94,-1.82l1.31,-0.78l2.09,-2.28l-0.51,-3.65l1.24,-3.7l1.5,-1.75l4.46,-2.57l2.37,-4.47l1.44,0.01l1.68,1.21l2.32,-0.19l3.47,0.65l0.8,1.54l0.16,1.71l0.86,2.96l0.56,0.59l-0.26,0.61l-3.05,0.44l-1.26,1.05l-1.33,0.22l-0.33,0.37l-0.09,1.78l-2.68,1.0l-1.07,1.42l-4.47,1.13l-4.04,2.01l-0.54,4.64l-1.15,0.06l-0.92,0.61l-1.96,-0.35l-2.42,0.54l-0.74,1.9l-0.86,0.4l-1.14,3.26l-3.53,3.01l-0.8,3.55l-0.96,1.1l-0.29,0.82l-4.95,0.18Z",
      name: "Morocco"
    },
    UZ: {
      path: "M598.64,172.75l-1.63,1.52l0.06,0.64l1.85,1.12l1.97,-0.64l2.21,1.17l-2.52,1.68l-2.59,-0.22l-0.18,-0.41l0.46,-1.23l-0.45,-0.53l-3.35,0.69l-2.1,3.51l-1.87,-0.12l-1.03,1.51l0.22,0.55l1.64,0.62l0.46,1.83l-1.19,2.49l-2.66,-0.53l0.05,-1.36l-0.26,-0.39l-3.3,-1.23l-2.56,-1.4l-4.4,-3.34l-1.34,-3.14l-1.08,-0.6l-2.58,0.13l-0.69,-0.44l-0.47,-2.52l-3.37,-1.6l-0.43,0.05l-2.07,1.72l-2.1,1.01l-0.21,0.47l0.28,1.01l-1.91,0.03l-0.09,-10.5l5.99,-1.7l6.19,3.54l2.71,2.84l7.05,-0.67l2.71,2.01l-0.17,2.81l0.39,0.42l0.9,0.02l0.44,2.14l0.38,0.32l2.94,0.09l0.95,1.42l1.28,-0.24l1.05,-2.04l4.43,-2.5Z",
      name: "Uzbekistan"
    },
    MM: {
      path: "M673.9,230.21l-1.97,1.57l-0.57,0.96l-1.4,0.6l-1.36,1.05l-1.99,0.36l-1.08,2.66l-0.91,0.4l-0.19,0.55l1.21,2.27l2.52,3.43l-0.79,1.91l-0.74,0.41l-0.17,0.52l0.65,1.37l1.61,1.95l0.25,2.58l0.9,2.13l-1.92,3.57l0.68,-2.25l-0.81,-1.74l0.19,-2.65l-1.05,-1.53l-1.24,-6.17l-1.12,-2.26l-0.6,-0.13l-4.34,3.02l-2.39,-0.65l0.77,-2.84l-0.52,-2.61l-1.91,-2.96l0.25,-0.75l-0.29,-0.51l-1.33,-0.3l-1.61,-1.93l-0.1,-1.3l0.82,-0.24l0.04,-1.64l1.02,-0.52l0.21,-0.45l-0.23,-0.95l0.54,-0.96l0.08,-2.22l1.46,0.45l0.47,-0.2l1.12,-2.19l0.16,-1.35l1.33,-2.16l-0.0,-1.52l2.89,-1.66l1.63,0.44l0.5,-0.44l-0.17,-1.4l0.64,-0.36l0.08,-1.04l0.77,-0.11l0.71,1.35l1.06,0.69l-0.03,3.86l-2.38,2.37l-0.3,3.15l0.46,0.43l2.28,-0.38l0.51,2.08l1.47,0.67l-0.6,1.8l0.19,0.48l2.97,1.48l1.64,-0.55l0.02,0.32Z",
      name: "Myanmar"
    },
    ML: {
      path: "M392.61,254.08l-0.19,-2.37l-0.99,-0.87l-0.44,-1.3l-0.09,-1.28l0.81,-0.58l0.35,-1.24l2.37,0.65l1.31,-0.47l0.86,0.15l0.66,-0.56l9.83,-0.04l0.38,-0.28l0.56,-1.8l-0.44,-0.65l-2.35,-21.95l3.27,-0.04l16.7,11.38l0.74,1.31l2.5,1.09l0.02,1.38l0.44,0.39l2.34,-0.21l0.01,5.38l-1.28,1.61l-0.26,1.49l-5.31,0.57l-1.07,0.92l-2.9,0.1l-0.86,-0.48l-1.38,0.36l-2.4,1.08l-0.6,0.87l-1.85,1.09l-0.43,0.7l-0.79,0.39l-1.44,-0.21l-0.81,0.84l-0.34,1.64l-1.91,2.02l-0.06,1.03l-0.67,1.22l0.13,1.16l-0.97,0.39l-0.23,-0.64l-0.52,-0.24l-1.35,0.4l-0.34,0.55l-2.69,-0.28l-0.37,-0.35l-0.02,-0.9l-0.65,-0.35l0.45,-0.64l-0.03,-0.53l-2.12,-2.44l-0.76,-0.01l-2.0,1.16l-0.78,-0.15l-0.8,-0.67l-1.21,0.23Z",
      name: "Mali"
    },
    MN: {
      path: "M676.61,146.48l3.81,1.68l5.67,-1.0l2.37,0.41l2.34,1.5l1.79,1.75l2.29,-0.03l3.12,0.52l2.47,-0.81l3.41,-0.59l3.53,-2.21l1.25,0.29l1.53,1.13l2.27,-0.21l-2.66,5.01l0.64,1.68l0.47,0.21l1.32,-0.38l2.38,0.48l2.02,-1.11l1.76,0.89l2.06,2.02l-0.13,0.53l-1.72,-0.29l-3.77,0.46l-1.88,0.99l-1.76,1.99l-3.71,1.17l-2.45,1.6l-3.83,-0.87l-0.41,0.17l-1.31,1.99l1.04,2.24l-1.52,0.9l-1.74,1.57l-2.79,1.02l-3.78,0.13l-4.05,1.05l-2.77,1.52l-1.16,-0.85l-2.94,0.0l-3.62,-1.79l-2.58,-0.49l-3.4,0.41l-5.12,-0.67l-2.63,0.06l-1.31,-1.6l-1.4,-3.0l-1.48,-0.33l-3.13,-1.94l-6.16,-0.93l-0.71,-1.06l0.86,-3.82l-1.93,-2.71l-3.5,-1.18l-1.95,-1.58l-0.5,-1.72l2.34,-0.52l4.75,-2.8l3.62,-1.47l2.18,0.97l2.46,0.05l1.81,1.53l2.46,0.12l3.95,0.71l2.43,-2.28l0.08,-0.48l-0.9,-1.72l2.24,-2.98l2.62,1.27l4.94,1.17l0.43,2.24Z",
      name: "Mongolia"
    },
    MK: {
      path: "M472.8,173.98l0.49,-0.71l3.57,-0.71l1.0,0.77l0.13,1.45l-0.65,0.53l-1.15,-0.05l-1.12,0.67l-1.39,0.22l-0.79,-0.55l-0.29,-1.03l0.19,-0.6Z",
      name: "Macedonia"
    },
    MW: {
      path: "M505.5,309.31l0.85,1.95l0.15,2.86l-0.69,1.65l0.71,1.8l0.06,1.28l0.49,0.64l0.07,1.06l0.4,0.55l0.8,-0.23l0.55,0.61l0.69,-0.21l0.34,0.6l0.19,2.94l-1.04,0.62l-0.54,1.25l-1.11,-1.08l-0.16,-1.56l0.51,-1.31l-0.32,-1.3l-0.99,-0.65l-0.82,0.12l-2.36,-1.64l0.63,-1.96l0.82,-1.18l-0.46,-2.01l0.9,-2.86l-0.94,-2.51l0.96,0.18l0.29,0.4Z",
      name: "Malawi"
    },
    MR: {
      path: "M407.36,220.66l-2.58,0.03l-0.39,0.44l2.42,22.56l0.36,0.43l-0.39,1.24l-9.75,0.04l-0.56,0.53l-0.91,-0.11l-1.27,0.45l-1.61,-0.66l-0.97,0.03l-0.36,0.29l-0.38,1.35l-0.42,0.23l-2.93,-3.4l-2.96,-1.52l-1.62,-0.03l-1.27,0.54l-1.12,-0.2l-0.65,0.4l-0.08,-0.49l0.68,-1.29l0.31,-2.43l-0.57,-3.91l0.23,-1.21l-0.69,-1.5l-1.15,-1.02l0.25,-0.39l9.58,0.02l0.4,-0.45l-0.46,-3.68l0.47,-1.04l2.12,-0.21l0.36,-0.4l-0.08,-6.4l7.81,0.13l0.41,-0.4l0.01,-3.31l7.76,5.35Z",
      name: "Mauritania"
    },
    UG: {
      path: "M498.55,276.32l0.7,-0.46l1.65,0.5l1.96,-0.57l1.7,0.01l1.45,-0.98l0.91,1.33l1.33,3.95l-2.57,4.03l-1.46,-0.4l-2.54,0.91l-1.37,1.61l-0.01,0.81l-2.42,-0.01l-2.26,1.01l-0.17,-1.59l0.58,-1.04l0.14,-1.94l1.37,-2.28l1.78,-1.58l-0.17,-0.65l-0.72,-0.24l0.13,-2.43Z",
      name: "Uganda"
    },
    MY: {
      path: "M717.47,273.46l-1.39,0.65l-2.12,-0.41l-2.88,-0.0l-0.38,0.28l-0.84,2.75l-0.99,0.96l-1.21,3.29l-1.73,0.45l-2.45,-0.68l-1.39,0.31l-1.33,1.15l-1.59,-0.14l-1.41,0.44l-1.44,-1.19l-0.18,-0.73l1.34,0.53l1.93,-0.47l0.75,-2.22l4.02,-1.03l2.75,-3.21l0.82,0.94l0.64,-0.05l0.4,-0.65l0.96,0.06l0.42,-0.36l0.24,-2.68l1.81,-1.64l1.21,-1.86l0.63,-0.01l1.07,1.05l0.34,1.28l3.44,1.35l-0.06,0.35l-1.37,0.1l-0.35,0.54l0.32,0.88ZM673.68,269.59l0.17,1.09l0.47,0.33l1.65,-0.3l0.87,-0.94l1.61,1.52l0.98,1.56l-0.12,2.81l0.41,2.29l0.95,0.9l0.88,2.44l-1.27,0.12l-5.1,-3.67l-0.34,-1.29l-1.37,-1.59l-0.33,-1.97l-0.88,-1.4l0.25,-1.68l-0.46,-1.05l1.63,0.84Z",
      name: "Malaysia"
    },
    MX: {
      path: "M133.12,200.41l0.2,0.47l9.63,3.33l6.96,-0.02l0.4,-0.4l0.0,-0.74l3.77,0.0l3.55,2.93l1.39,2.83l1.52,1.04l2.08,0.82l0.47,-0.14l1.46,-2.0l1.73,-0.04l1.59,0.98l2.05,3.35l1.47,1.56l1.26,3.14l2.18,1.02l2.26,0.58l-1.18,3.72l-0.42,5.04l1.79,4.89l1.62,1.89l0.61,1.52l1.2,1.42l2.55,0.66l1.37,1.1l7.54,-1.89l1.86,-1.3l1.14,-4.3l4.1,-1.21l3.57,-0.11l0.32,0.3l-0.06,0.94l-1.26,1.45l-0.67,1.71l0.38,0.7l-0.72,2.27l-0.49,-0.3l-1.0,0.08l-1.0,1.39l-0.47,-0.11l-0.53,0.47l-4.26,-0.02l-0.4,0.4l-0.0,1.06l-1.1,0.26l0.1,0.44l1.82,1.44l0.56,0.91l-3.19,0.21l-1.21,2.09l0.24,0.72l-0.2,0.44l-2.24,-2.18l-1.45,-0.93l-2.22,-0.69l-1.52,0.22l-3.07,1.16l-10.55,-3.85l-2.86,-1.96l-3.78,-0.92l-1.08,-1.19l-2.62,-1.43l-1.18,-1.54l-0.38,-0.81l0.66,-0.63l-0.18,-0.53l0.52,-0.76l0.01,-0.91l-2.0,-3.82l-2.21,-2.63l-2.53,-2.09l-1.19,-1.62l-2.2,-1.17l-0.3,-0.43l0.34,-1.48l-0.21,-0.45l-1.23,-0.6l-1.36,-1.2l-0.59,-1.78l-1.54,-0.47l-2.44,-2.55l-0.16,-0.9l-1.33,-2.03l-0.84,-1.99l-0.16,-1.33l-1.81,-1.1l-0.97,0.05l-1.31,-0.7l-0.57,0.22l-0.4,1.12l0.72,3.77l3.51,3.89l0.28,0.78l0.53,0.26l0.41,1.43l1.33,1.73l1.58,1.41l0.8,2.39l1.43,2.41l0.13,1.32l0.37,0.36l1.04,0.08l1.67,2.28l-0.85,0.76l-0.66,-1.51l-1.68,-1.54l-2.91,-1.87l0.06,-1.82l-0.54,-1.68l-2.91,-2.03l-0.55,0.09l-1.95,-1.1l-0.88,-0.94l0.68,-0.08l0.93,-1.01l0.08,-1.78l-1.93,-1.94l-1.46,-0.77l-3.75,-7.56l4.88,-0.42Z",
      name: "Mexico"
    },
    VU: {
      path: "M839.04,322.8l0.22,1.14l-0.44,0.03l-0.2,-1.45l0.42,0.27Z",
      name: "Vanuatu"
    },
    FR: {
      path: "M444.48,172.62l-0.64,1.78l-0.58,-0.31l-0.49,-1.72l0.4,-0.89l1.0,-0.72l0.3,1.85ZM429.64,147.1l1.78,1.58l1.46,-0.13l2.1,1.42l1.35,0.27l1.23,0.83l3.04,0.5l-1.03,1.85l-0.3,2.12l-0.41,0.32l-0.95,-0.24l-0.5,0.43l0.06,0.61l-1.81,1.92l-0.04,1.42l0.55,0.38l0.88,-0.36l0.61,0.97l-0.03,1.0l0.57,0.91l-0.75,1.09l0.65,2.39l1.27,0.57l-0.18,0.82l-2.01,1.53l-4.77,-0.8l-3.82,1.0l-0.53,1.85l-2.49,0.34l-2.71,-1.31l-1.16,0.57l-4.31,-1.29l-0.72,-0.86l1.19,-1.78l0.39,-6.45l-2.58,-3.3l-1.9,-1.66l-3.72,-1.23l-0.19,-1.72l2.81,-0.61l4.12,0.81l0.47,-0.48l-0.6,-2.77l1.94,0.95l5.83,-2.54l0.92,-2.74l1.6,-0.49l0.24,0.78l1.36,0.33l1.05,1.19ZM289.01,278.39l-0.81,0.8l-0.78,0.12l-0.5,-0.66l-0.56,-0.1l-0.91,0.6l-0.46,-0.22l1.09,-2.96l-0.96,-1.77l-0.17,-1.49l1.07,-1.77l2.32,0.75l2.51,2.01l0.3,0.74l-2.14,3.96Z",
      name: "France"
    },
    FI: {
      path: "M492.17,76.39l-0.23,3.5l3.52,2.63l-2.08,2.88l-0.02,0.44l2.8,4.56l-1.59,3.31l2.16,3.24l-0.94,2.39l0.14,0.47l3.44,2.51l-0.77,1.62l-7.52,6.95l-4.5,0.31l-4.38,1.37l-3.8,0.74l-1.44,-1.96l-2.17,-1.11l0.5,-3.66l-1.16,-3.33l1.09,-2.08l2.21,-2.42l5.67,-4.32l1.64,-0.83l0.21,-0.42l-0.46,-2.02l-3.38,-1.89l-0.75,-1.43l-0.22,-6.74l-6.79,-4.8l0.8,-0.62l2.54,2.12l3.46,-0.12l3.0,0.96l2.51,-2.11l1.17,-3.08l3.55,-1.38l2.76,1.53l-0.95,2.79Z",
      name: "Finland"
    },
    FJ: {
      path: "M871.53,326.34l-2.8,1.05l-0.08,-0.23l2.97,-1.21l-0.1,0.39ZM867.58,329.25l0.43,0.37l-0.27,0.88l-1.24,0.28l-1.04,-0.24l-0.14,-0.66l0.63,-0.58l0.92,0.26l0.7,-0.31Z",
      name: "Fiji"
    },
    FK: {
      path: "M274.36,425.85l1.44,1.08l-0.47,0.73l-3.0,0.89l-0.96,-1.0l-0.52,-0.05l-1.83,1.29l-0.73,-0.88l2.46,-1.64l1.93,0.76l1.67,-1.19Z",
      name: "Falkland Is."
    },
    NI: {
      path: "M202.33,252.67l0.81,-0.18l1.03,-1.02l-0.04,-0.88l0.68,-0.0l0.63,-0.54l0.97,0.22l1.53,-1.26l0.58,-0.99l1.17,0.34l2.41,-0.94l0.13,1.32l-0.81,1.94l0.1,2.74l-0.36,0.37l-0.11,1.75l-0.47,0.81l0.18,1.14l-1.73,-0.85l-0.71,0.27l-1.47,-0.6l-0.52,0.16l-4.01,-3.81Z",
      name: "Nicaragua"
    },
    NL: {
      path: "M430.31,143.39l0.6,-0.5l2.13,-4.8l3.2,-1.33l1.74,0.08l0.33,0.8l-0.59,2.92l-0.5,0.99l-1.26,0.0l-0.4,0.45l0.33,2.7l-2.2,-1.78l-2.62,0.58l-0.75,-0.11Z",
      name: "Netherlands"
    },
    NO: {
      path: "M491.44,67.41l6.8,2.89l-2.29,0.86l-0.15,0.65l2.33,2.38l-4.98,1.79l0.84,-2.45l-0.18,-0.48l-3.55,-1.8l-3.89,1.52l-1.42,3.38l-2.12,1.72l-2.64,-1.0l-3.11,0.21l-2.66,-2.22l-0.5,-0.01l-1.41,1.1l-1.44,0.17l-0.35,0.35l-0.32,2.47l-4.32,-0.64l-0.44,0.29l-0.58,2.11l-2.45,0.2l-4.15,7.68l-3.88,5.76l0.78,1.62l-0.64,1.16l-2.24,-0.06l-0.38,0.24l-1.66,3.89l0.15,5.17l1.57,2.04l-0.78,4.16l-2.02,2.48l-0.85,1.63l-1.3,-1.75l-0.58,-0.07l-4.87,4.19l-3.1,0.79l-3.16,-1.7l-0.85,-3.77l-0.77,-8.55l2.14,-2.31l6.55,-3.27l5.02,-4.17l10.63,-13.84l10.98,-8.7l5.35,-1.91l4.34,0.12l3.69,-3.64l4.49,0.19l4.37,-0.89ZM484.55,20.04l4.26,1.75l-3.1,2.55l-7.1,0.65l-7.08,-0.9l-0.37,-1.31l-0.37,-0.29l-3.44,-0.1l-2.08,-2.0l6.87,-1.44l3.9,1.31l2.39,-1.64l6.13,1.4ZM481.69,33.93l-4.45,1.74l-3.54,-0.99l1.12,-0.9l0.05,-0.58l-1.06,-1.22l4.22,-0.89l1.09,1.97l2.57,0.87ZM466.44,24.04l7.43,3.77l-5.41,1.86l-1.58,4.08l-2.26,1.2l-1.12,4.11l-2.61,0.18l-4.79,-2.86l1.84,-1.54l-0.1,-0.68l-3.69,-1.53l-4.77,-4.51l-1.73,-3.89l6.11,-1.82l1.54,1.92l3.57,-0.08l1.2,-1.96l3.32,-0.18l3.05,1.92Z",
      name: "Norway"
    },
    NA: {
      path: "M474.26,330.66l-0.97,0.04l-0.38,0.4l-0.07,8.9l-2.09,0.08l-0.39,0.4l-0.0,17.42l-1.98,1.23l-1.17,0.17l-2.44,-0.66l-0.48,-1.13l-0.99,-0.74l-0.54,0.05l-0.9,1.01l-1.53,-1.68l-0.93,-1.88l-1.99,-8.56l-0.06,-3.12l-0.33,-1.52l-2.3,-3.34l-1.91,-4.83l-1.96,-2.43l-0.12,-1.57l2.33,-0.79l1.43,0.07l1.81,1.13l10.23,-0.25l1.84,1.23l5.87,0.35ZM474.66,330.64l6.51,-1.6l1.9,0.39l-1.69,0.4l-1.31,0.83l-1.12,-0.94l-4.29,0.92Z",
      name: "Namibia"
    },
    NC: {
      path: "M838.78,341.24l-0.33,0.22l-2.9,-1.75l-3.26,-3.37l1.65,0.83l4.85,4.07Z",
      name: "New Caledonia"
    },
    NE: {
      path: "M454.75,226.53l1.33,1.37l0.48,0.07l1.27,-0.7l0.53,3.52l0.94,0.83l0.17,0.92l0.81,0.69l-0.44,0.95l-0.96,5.26l-0.13,3.22l-3.04,2.31l-1.22,3.57l1.02,1.24l-0.0,1.46l0.39,0.4l1.13,0.04l-0.9,1.25l-1.47,-2.42l-0.86,-0.29l-2.09,1.37l-1.74,-0.67l-1.45,-0.17l-0.85,0.35l-1.36,-0.07l-1.64,1.09l-1.06,0.05l-2.94,-1.28l-1.44,0.59l-1.01,-0.03l-0.97,-0.94l-2.7,-0.98l-2.69,0.3l-0.87,0.64l-0.47,1.6l-0.75,1.16l-0.12,1.53l-1.57,-1.1l-1.31,0.24l0.03,-0.81l-0.32,-0.41l-2.59,-0.52l-0.15,-1.16l-1.35,-1.6l-0.29,-1.0l0.13,-0.84l1.29,-0.08l1.08,-0.92l3.31,-0.22l2.22,-0.41l0.32,-0.34l0.2,-1.47l1.39,-1.88l-0.01,-5.66l3.36,-1.12l7.24,-5.12l8.42,-4.92l3.69,1.06Z",
      name: "Niger"
    },
    NG: {
      path: "M456.32,253.89l0.64,0.65l-0.28,1.04l-2.11,2.01l-2.03,5.18l-1.37,1.16l-1.15,3.18l-1.33,0.66l-1.46,-0.97l-1.21,0.16l-1.38,1.36l-0.91,0.24l-1.79,4.06l-2.33,0.81l-1.11,-0.07l-0.86,0.5l-1.71,-0.05l-1.19,-1.39l-0.89,-1.89l-1.77,-1.66l-3.95,-0.08l0.07,-5.21l0.42,-1.43l1.95,-2.3l-0.14,-0.91l0.43,-1.18l-0.53,-1.41l0.25,-2.92l0.72,-1.07l0.32,-1.34l0.46,-0.39l2.47,-0.28l2.34,0.89l1.15,1.02l1.28,0.04l1.22,-0.58l3.03,1.27l1.49,-0.14l1.36,-1.0l1.33,0.07l0.82,-0.35l3.45,0.8l1.82,-1.32l1.84,2.67l0.66,0.16Z",
      name: "Nigeria"
    },
    NZ: {
      path: "M857.8,379.65l1.86,3.12l0.44,0.18l0.3,-0.38l0.03,-1.23l0.38,0.27l0.57,2.31l2.02,0.94l1.81,0.27l1.57,-1.06l0.7,0.18l-1.15,3.59l-1.98,0.11l-0.74,1.2l0.2,1.11l-2.42,3.98l-1.49,0.92l-1.04,-0.85l1.21,-2.05l-0.81,-2.01l-2.63,-1.25l0.04,-0.57l1.82,-1.19l0.43,-2.34l-0.16,-2.03l-0.95,-1.82l-0.06,-0.72l-3.11,-3.64l-0.79,-1.52l1.56,1.45l1.76,0.66l0.65,2.34ZM853.83,393.59l0.57,1.24l0.59,0.16l1.42,-0.97l0.46,0.79l0.0,1.03l-2.47,3.48l-1.26,1.2l-0.06,0.5l0.55,0.87l-1.41,0.07l-2.33,1.38l-2.03,5.02l-3.02,2.16l-2.06,-0.06l-1.71,-1.04l-2.47,-0.2l-0.27,-0.73l1.22,-2.1l3.05,-2.94l1.62,-0.59l4.02,-2.82l1.57,-1.67l1.07,-2.16l0.88,-0.7l0.48,-1.75l1.24,-0.97l0.35,0.79Z",
      name: "New Zealand"
    },
    NP: {
      path: "M641.14,213.62l0.01,3.19l-1.74,0.04l-4.8,-0.86l-1.58,-1.39l-3.37,-0.34l-7.65,-3.7l0.8,-2.09l2.33,-1.7l1.77,0.75l2.49,1.76l1.38,0.41l0.99,1.35l1.9,0.52l1.99,1.17l5.49,0.9Z",
      name: "Nepal"
    },
    CI: {
      path: "M407.4,259.27l0.86,0.42l0.56,0.9l1.13,0.53l1.19,-0.61l0.97,-0.08l1.42,0.54l0.6,3.24l-1.03,2.08l-0.65,2.84l1.06,2.33l-0.06,0.53l-2.54,-0.47l-1.66,0.03l-3.06,0.46l-4.11,1.6l0.32,-3.06l-1.18,-1.31l-1.32,-0.66l0.42,-0.85l-0.2,-1.4l0.5,-0.67l0.01,-1.59l0.84,-0.32l0.26,-0.5l-1.15,-3.01l0.12,-0.5l0.51,-0.25l0.66,0.31l1.93,0.02l0.67,-0.71l0.71,-0.14l0.25,0.69l0.57,0.22l1.4,-0.61Z",
      name: "Côte d'Ivoire"
    },
    CH: {
      path: "M444.62,156.35l-0.29,0.87l0.18,0.53l1.13,0.58l1.0,0.1l-0.1,0.65l-0.79,0.38l-1.72,-0.37l-0.45,0.23l-0.45,1.04l-0.75,0.06l-0.84,-0.4l-1.32,1.0l-0.96,0.12l-0.88,-0.55l-0.81,-1.3l-0.49,-0.16l-0.63,0.26l0.02,-0.65l1.71,-1.66l0.1,-0.56l0.93,0.08l0.58,-0.46l1.99,0.02l0.66,-0.61l2.19,0.79Z",
      name: "Switzerland"
    },
    CO: {
      path: "M242.07,254.93l-1.7,0.59l-0.59,1.18l-1.7,1.69l-0.38,1.93l-0.67,1.43l0.31,0.57l1.03,0.13l0.25,0.9l0.57,0.64l-0.04,2.34l1.64,1.42l3.16,-0.24l1.26,0.28l1.67,2.06l0.41,0.13l4.09,-0.39l0.45,0.22l-0.92,1.95l-0.2,1.8l0.52,1.83l0.75,1.05l-1.12,1.1l0.07,0.63l0.84,0.51l0.74,1.29l-0.39,-0.45l-0.59,-0.01l-0.71,0.74l-4.71,-0.05l-0.4,0.41l0.03,1.57l0.33,0.39l1.11,0.2l-1.68,0.4l-0.29,0.38l-0.01,1.82l1.16,1.14l0.34,1.25l-1.05,7.05l-1.04,-0.87l1.26,-1.99l-0.13,-0.56l-2.18,-1.23l-1.38,0.2l-1.14,-0.38l-1.27,0.61l-1.55,-0.26l-1.38,-2.46l-1.23,-0.75l-0.85,-1.2l-1.67,-1.19l-0.86,0.13l-2.11,-1.32l-1.01,0.31l-1.8,-0.29l-0.52,-0.91l-3.09,-1.68l0.77,-0.52l-0.1,-1.12l0.41,-0.64l1.34,-0.32l2.0,-2.88l-0.11,-0.57l-0.66,-0.43l0.39,-1.38l-0.52,-2.1l0.49,-0.83l-0.4,-2.13l-0.97,-1.35l0.17,-0.66l0.86,-0.08l0.47,-0.75l-0.46,-1.63l1.41,-0.07l1.8,-1.69l0.93,-0.24l0.3,-0.38l0.45,-2.76l1.22,-1.0l1.44,-0.04l0.45,-0.5l1.91,0.12l2.93,-1.84l1.15,-1.14l0.91,0.46l-0.25,0.45Z",
      name: "Colombia"
    },
    CN: {
      path: "M740.23,148.97l4.57,1.3l2.8,2.17l0.98,2.9l0.38,0.27l3.8,0.0l2.32,-1.28l3.29,-0.75l-0.96,2.09l-1.02,1.28l-0.85,3.4l-1.52,2.73l-2.76,-0.5l-2.4,1.13l-0.21,0.45l0.64,2.57l-0.32,3.2l-0.94,0.06l-0.37,0.89l-0.91,-1.01l-0.64,0.07l-0.92,1.57l-3.73,1.25l-0.26,0.48l0.26,1.06l-1.5,-0.08l-1.09,-0.86l-0.56,0.06l-1.67,2.06l-2.7,1.56l-2.03,1.88l-3.4,0.83l-1.93,1.4l-1.15,0.34l0.33,-0.7l-0.41,-0.89l1.79,-1.79l0.02,-0.54l-1.32,-1.56l-0.48,-0.1l-2.24,1.09l-2.83,2.06l-1.51,1.83l-2.28,0.13l-1.55,1.49l-0.04,0.5l1.32,1.97l2.0,0.58l0.31,1.35l1.98,0.84l3.0,-1.96l2.0,1.02l1.49,0.11l0.22,0.83l-3.37,0.86l-1.12,1.48l-2.5,1.52l-1.29,1.99l0.14,0.56l2.57,1.48l0.97,2.7l3.17,4.63l-0.03,1.66l-1.35,0.65l-0.2,0.51l0.6,1.47l1.4,0.91l-0.89,3.82l-1.43,0.38l-3.85,6.44l-2.27,3.11l-6.78,4.57l-2.73,0.29l-1.45,1.04l-0.62,-0.61l-0.55,-0.01l-1.36,1.25l-3.39,1.27l-2.61,0.4l-1.1,2.79l-0.81,0.09l-0.49,-1.42l0.5,-0.85l-0.25,-0.59l-3.36,-0.84l-1.3,0.4l-2.31,-0.62l-0.94,-0.84l0.33,-1.28l-0.3,-0.49l-2.19,-0.46l-1.13,-0.93l-0.47,-0.02l-2.06,1.36l-4.29,0.28l-2.76,1.05l-0.28,0.43l0.32,2.53l-0.59,-0.03l-0.19,-1.34l-0.55,-0.34l-1.68,0.7l-2.46,-1.23l0.62,-1.87l-0.26,-0.51l-1.37,-0.44l-0.54,-2.22l-0.45,-0.3l-2.13,0.35l0.24,-2.48l2.39,-2.4l0.03,-4.31l-1.19,-0.92l-0.78,-1.49l-0.41,-0.21l-1.41,0.19l-1.98,-0.3l0.46,-1.07l-1.17,-1.7l-0.55,-0.11l-1.63,1.05l-2.25,-0.57l-2.89,1.73l-2.25,1.98l-1.75,0.29l-1.17,-0.71l-3.31,-0.65l-1.48,0.79l-1.04,1.27l-0.12,-1.17l-0.54,-0.34l-1.44,0.54l-5.55,-0.86l-1.98,-1.16l-1.89,-0.54l-0.99,-1.35l-1.34,-0.37l-2.55,-1.79l-2.01,-0.84l-1.21,0.56l-5.57,-3.45l-0.53,-2.31l1.19,0.25l0.48,-0.37l0.08,-1.42l-0.98,-1.56l0.15,-2.44l-2.69,-3.32l-4.12,-1.23l-0.67,-2.0l-1.92,-1.48l-0.38,-0.7l-0.51,-3.01l-1.52,-0.66l-0.7,0.13l-0.48,-2.05l0.55,-0.51l-0.09,-0.82l2.03,-1.19l1.6,-0.54l2.56,0.38l0.42,-0.22l0.85,-1.7l3.0,-0.33l1.1,-1.26l4.05,-1.77l0.39,-0.91l-0.17,-1.44l1.45,-0.67l0.2,-0.52l-2.07,-4.9l4.51,-1.12l1.37,-0.73l1.89,-5.51l4.98,0.86l1.51,-1.7l0.11,-2.87l1.99,-0.38l1.83,-2.06l0.49,-0.13l0.68,2.08l2.23,1.77l3.44,1.16l1.55,2.29l-0.92,3.49l0.96,1.67l6.54,1.13l2.95,1.87l1.47,0.35l1.06,2.62l1.53,1.91l3.05,0.08l5.14,0.67l3.37,-0.41l2.36,0.43l3.65,1.8l3.06,0.04l1.45,0.88l2.87,-1.59l3.95,-1.02l3.83,-0.14l3.06,-1.14l1.77,-1.6l1.72,-1.01l0.17,-0.49l-1.1,-2.05l1.02,-1.54l4.02,0.8l2.45,-1.61l3.76,-1.19l1.96,-2.13l1.63,-0.83l3.51,-0.4l1.92,0.34l0.46,-0.3l0.17,-1.5l-2.27,-2.22l-2.11,-1.09l-2.18,1.11l-2.32,-0.47l-1.29,0.32l-0.4,-0.82l2.73,-5.16l3.02,1.06l3.53,-2.06l0.18,-1.68l2.16,-3.35l1.49,-1.35l-0.03,-1.85l-1.07,-0.85l1.54,-1.26l2.98,-0.59l3.23,-0.09l3.64,0.99l2.04,1.16l3.29,6.71l0.92,3.19ZM696.92,237.31l-1.87,1.08l-1.63,-0.64l-0.06,-1.79l1.03,-0.98l2.58,-0.69l1.16,0.05l0.3,0.54l-0.98,1.06l-0.53,1.37Z",
      name: "China"
    },
    CM: {
      path: "M457.92,257.49l1.05,1.91l-1.4,0.16l-1.05,-0.23l-0.45,0.22l-0.54,1.19l0.08,0.45l1.48,1.47l1.05,0.45l1.01,2.46l-1.52,2.99l-0.68,0.68l-0.13,3.69l2.38,3.84l1.09,0.8l0.24,2.48l-3.67,-1.14l-11.27,-0.13l0.23,-1.79l-0.98,-1.66l-1.19,-0.54l-0.44,-0.97l-0.6,-0.42l1.71,-4.27l0.75,-0.13l1.38,-1.36l0.65,-0.03l1.71,0.99l1.93,-1.12l1.14,-3.18l1.38,-1.17l2.0,-5.14l2.17,-2.13l0.3,-1.64l-0.86,-0.88l0.03,-0.33l0.94,1.28l0.07,3.22Z",
      name: "Cameroon"
    },
    CL: {
      path: "M246.5,429.18l-3.14,1.83l-0.57,3.16l-0.64,0.05l-2.68,-1.06l-2.82,-2.33l-3.04,-1.89l-0.69,-1.85l0.63,-2.14l-1.21,-2.11l-0.31,-5.37l1.01,-2.91l2.57,-2.38l-0.18,-0.68l-3.16,-0.77l2.05,-2.47l0.77,-4.65l2.32,0.9l0.54,-0.29l1.31,-6.31l-0.22,-0.44l-1.68,-0.8l-0.56,0.28l-0.7,3.36l-0.81,-0.22l1.56,-9.41l1.15,-2.24l-0.71,-2.82l-0.18,-2.84l1.01,-0.33l3.26,-9.14l1.07,-4.22l-0.56,-4.21l0.74,-2.34l-0.29,-3.27l1.46,-3.34l2.04,-16.59l-0.66,-7.76l1.03,-0.53l0.54,-0.9l0.79,1.14l0.32,1.78l1.25,1.16l-0.69,2.55l1.33,2.9l0.97,3.59l0.46,0.29l1.5,-0.3l0.11,0.23l-0.76,2.44l-2.57,1.23l-0.23,0.37l0.08,4.33l-0.46,0.77l0.56,1.21l-1.58,1.51l-1.68,2.62l-0.89,2.47l0.2,2.7l-1.48,2.73l1.12,5.09l0.64,0.61l-0.01,2.29l-1.38,2.68l0.01,2.4l-1.89,2.04l0.02,2.75l0.69,2.57l-1.43,1.13l-1.26,5.68l0.39,3.51l-0.97,0.89l0.58,3.5l1.02,1.14l-0.65,1.02l0.15,0.57l1.0,0.53l0.16,0.69l-1.03,0.85l0.26,1.75l-0.89,4.03l-1.31,2.66l0.24,1.75l-0.71,1.83l-1.99,1.7l0.3,3.67l0.88,1.19l1.58,0.01l0.01,2.21l1.04,1.95l5.98,0.63ZM248.69,430.79l0.0,7.33l0.4,0.4l3.52,0.05l-0.44,0.75l-1.94,0.98l-2.49,-0.37l-1.88,-1.06l-2.55,-0.49l-5.59,-3.71l-2.38,-2.63l4.1,2.48l3.32,1.23l0.45,-0.12l1.29,-1.57l0.83,-2.32l2.05,-1.24l1.31,0.29Z",
      name: "Chile"
    },
    CA: {
      path: "M280.06,145.6l-1.67,2.88l0.07,0.49l0.5,0.04l1.46,-0.98l1.0,0.42l-0.56,0.72l0.17,0.62l2.22,0.89l1.35,-0.71l1.95,0.78l-0.66,2.01l0.5,0.51l1.32,-0.42l0.98,3.17l-0.91,2.41l-0.8,0.08l-1.23,-0.45l0.47,-2.25l-0.89,-0.83l-0.48,0.06l-2.78,2.63l-0.34,-0.02l1.02,-0.85l-0.14,-0.69l-2.4,-0.77l-7.4,0.08l-0.17,-0.41l1.3,-0.94l0.02,-0.64l-0.73,-0.58l1.85,-1.74l2.57,-5.16l1.47,-1.79l1.99,-1.05l0.46,0.06l-1.53,2.45ZM68.32,74.16l4.13,0.95l4.02,2.14l2.61,0.4l2.47,-1.89l2.88,-1.31l3.85,0.48l3.71,-1.94l3.82,-1.04l1.56,1.68l0.49,0.08l1.87,-1.04l0.65,-1.98l1.24,0.35l4.16,3.94l0.54,0.01l2.75,-2.49l0.26,2.59l0.49,0.35l3.08,-0.73l1.04,-1.27l2.73,0.23l3.83,1.86l5.86,1.61l3.47,0.75l2.44,-0.26l2.73,1.78l-2.98,1.81l-0.19,0.41l0.31,0.32l4.53,0.92l6.87,-0.5l2.0,-0.69l2.49,2.39l0.53,0.02l2.72,-2.16l-0.02,-0.64l-2.16,-1.54l1.15,-1.06l4.83,-0.61l1.84,0.95l2.48,2.31l3.01,-0.23l4.55,1.92l3.85,-0.67l3.61,0.1l0.41,-0.44l-0.25,-2.36l1.79,-0.61l3.49,1.32l-0.01,3.77l0.31,0.39l0.45,-0.22l1.48,-3.16l1.74,0.1l0.41,-0.3l1.13,-4.37l-2.78,-3.11l-2.8,-1.74l0.19,-4.64l2.71,-3.07l2.98,0.67l2.41,1.95l3.19,4.8l-1.99,1.97l0.21,0.68l4.33,0.84l-0.01,4.15l0.25,0.37l0.44,-0.09l3.07,-3.15l2.54,2.39l-0.61,3.33l2.42,2.88l0.61,0.0l2.61,-3.08l1.88,-3.82l0.17,-4.58l6.72,0.94l3.13,2.04l0.13,1.82l-1.76,2.19l-0.01,0.49l1.66,2.16l-0.26,1.71l-4.68,2.8l-3.28,0.61l-2.47,-1.2l-0.55,0.23l-0.73,2.04l-2.38,3.43l-0.74,1.77l-2.74,2.57l-3.44,0.25l-2.21,1.78l-0.28,2.53l-2.82,0.55l-3.12,3.22l-2.72,4.31l-1.03,3.17l-0.14,4.31l0.33,0.41l3.44,0.57l2.24,5.95l0.45,0.23l3.4,-0.69l4.52,1.51l2.43,1.31l1.91,1.73l3.1,0.96l2.62,1.46l6.6,0.54l-0.35,2.74l0.81,3.53l1.81,3.78l3.83,3.3l0.45,0.04l2.1,-1.28l1.37,-3.69l-1.31,-5.38l-1.45,-1.58l3.57,-1.47l2.84,-2.46l1.52,-2.8l-0.25,-2.55l-1.7,-3.07l-2.85,-2.61l2.8,-3.95l-1.08,-3.37l-0.79,-5.67l1.36,-0.7l6.76,1.41l2.12,-0.96l5.12,3.36l1.05,1.61l4.08,0.26l-0.06,2.87l0.83,4.7l0.3,0.32l2.16,0.54l1.73,2.06l0.5,0.09l3.63,-2.03l2.52,-4.19l1.26,-1.32l7.6,11.72l-0.92,2.04l0.16,0.51l3.3,1.97l2.22,1.98l4.1,0.98l1.43,0.99l0.95,2.79l2.1,0.68l0.84,1.08l0.17,3.45l-3.37,2.26l-4.22,1.24l-3.06,2.63l-4.06,0.51l-5.35,-0.69l-6.39,0.2l-2.3,2.41l-3.26,1.51l-6.47,7.15l-0.06,0.48l0.44,0.19l2.13,-0.52l4.17,-4.24l5.12,-2.62l3.52,-0.3l1.69,1.21l-2.12,2.21l0.81,3.47l1.02,2.61l3.47,1.6l4.14,-0.45l2.15,-2.8l0.26,1.48l1.14,0.8l-2.56,1.69l-5.5,1.82l-2.54,1.27l-2.74,2.15l-1.4,-0.16l-0.07,-2.01l4.14,-2.44l0.18,-0.45l-0.39,-0.29l-6.63,0.45l-1.39,-1.49l-0.14,-4.43l-1.11,-0.91l-1.82,0.39l-0.66,-0.66l-0.6,0.03l-1.91,2.39l-0.82,2.52l-0.8,1.27l-1.67,0.56l-0.46,0.76l-8.31,0.07l-1.21,0.62l-2.35,1.97l-0.71,-0.14l-1.37,0.96l-1.12,-0.48l-4.74,1.26l-0.9,1.17l0.21,0.62l1.73,0.3l-1.81,0.31l-1.85,0.81l-2.11,-0.13l-2.95,1.78l-0.69,-0.09l1.39,-2.1l1.73,-1.21l0.1,-2.29l1.16,-1.99l0.49,0.53l2.03,0.42l1.2,-1.16l0.02,-0.47l-2.66,-3.51l-2.28,-0.61l-5.64,-0.71l-0.4,-0.57l-0.79,0.13l0.2,-0.41l-0.22,-0.55l-0.68,-0.26l0.19,-1.26l-0.78,-0.73l0.31,-0.64l-0.29,-0.57l-2.6,-0.44l-0.75,-1.63l-0.94,-0.66l-4.31,-0.65l-1.13,1.19l-1.48,0.59l-0.85,1.06l-2.83,-0.76l-2.09,0.39l-2.39,-0.97l-4.24,-0.7l-0.57,-0.4l-0.41,-1.63l-0.4,-0.3l-0.85,0.02l-0.39,0.4l-0.01,0.85l-69.13,-0.01l-6.51,-4.52l-4.5,-1.38l-1.26,-2.66l0.33,-1.93l-0.23,-0.43l-3.01,-1.35l-0.55,-2.77l-2.89,-2.38l-0.04,-1.45l1.39,-1.83l-0.28,-2.55l-4.16,-2.2l-4.07,-6.6l-4.02,-3.22l-1.3,-1.88l-0.5,-0.13l-2.51,1.21l-2.23,1.87l-3.85,-3.88l-2.44,-1.04l-2.22,-0.13l0.03,-37.49ZM260.37,148.65l3.04,0.76l2.26,1.2l-3.78,-0.95l-1.53,-1.01ZM249.4,3.81l6.68,0.49l5.32,0.79l4.26,1.57l-0.07,1.1l-5.85,2.53l-6.02,1.21l-2.39,1.39l-0.18,0.45l0.39,0.29l4.01,-0.02l-4.65,2.82l-4.2,1.74l-4.19,4.59l-5.03,0.92l-1.67,1.15l-7.47,0.59l-0.37,0.37l0.32,0.42l2.41,0.49l-0.81,0.47l-0.12,0.59l1.83,2.41l-2.02,1.59l-3.81,1.51l-1.32,2.16l-3.38,1.53l-0.22,0.48l0.35,1.19l0.4,0.29l3.88,-0.18l0.03,0.61l-6.33,2.95l-6.41,-1.4l-7.43,0.79l-3.72,-0.62l-4.4,-0.25l-0.23,-1.83l4.29,-1.11l0.28,-0.51l-1.1,-3.45l1.0,-0.25l6.58,2.28l0.47,-0.16l-0.05,-0.49l-3.41,-3.45l-3.58,-0.98l1.48,-1.55l4.34,-1.29l0.97,-2.19l-0.16,-0.48l-3.42,-2.13l-0.81,-2.26l6.2,0.22l2.24,0.58l3.91,-2.1l0.2,-0.43l-0.35,-0.32l-5.64,-0.67l-8.73,0.36l-4.26,-1.9l-2.12,-2.4l-2.78,-1.66l-0.41,-1.52l3.31,-1.03l2.93,-0.2l4.91,-0.99l3.7,-2.27l2.87,0.3l2.62,1.67l0.56,-0.14l1.82,-3.2l3.13,-0.94l4.44,-0.69l7.53,-0.26l1.48,0.67l7.19,-1.06l10.8,0.79ZM203.85,57.54l0.01,0.42l1.97,2.97l0.68,-0.02l2.24,-3.72l5.95,-1.86l4.01,4.64l-0.35,2.91l0.5,0.43l4.95,-1.36l2.32,-1.8l5.31,2.28l3.27,2.11l0.3,1.84l0.48,0.33l4.42,-0.99l2.64,2.87l5.97,1.77l2.06,1.72l2.11,3.71l-4.19,1.86l-0.01,0.73l5.9,2.83l3.94,0.94l3.78,3.95l3.46,0.25l-0.63,2.37l-4.11,4.47l-2.76,-1.56l-3.9,-3.94l-3.59,0.41l-0.33,0.34l-0.19,2.72l2.63,2.38l3.42,1.89l0.94,0.97l1.55,3.75l-0.7,2.29l-2.74,-0.92l-6.25,-3.15l-0.51,0.13l0.05,0.52l6.07,5.69l0.18,0.59l-6.09,-1.39l-5.31,-2.24l-2.63,-1.66l0.6,-0.77l-0.12,-0.6l-7.39,-4.01l-0.59,0.37l0.03,0.79l-6.73,0.6l-1.69,-1.1l1.36,-2.46l4.51,-0.07l5.15,-0.52l0.31,-0.6l-0.74,-1.3l0.78,-1.84l3.21,-4.05l-0.67,-2.35l-1.11,-1.6l-3.84,-2.1l-4.35,-1.28l0.91,-0.63l0.06,-0.61l-2.65,-2.75l-2.34,-0.36l-1.89,-1.46l-0.53,0.03l-1.24,1.23l-4.36,0.55l-9.04,-0.99l-9.26,-1.98l-1.6,-1.22l2.22,-1.77l0.13,-0.44l-0.38,-0.27l-3.22,-0.02l-0.72,-4.25l1.83,-4.04l2.42,-1.85l5.5,-1.1l-1.39,2.35ZM261.19,159.33l2.07,0.61l1.44,-0.04l-1.15,0.63l-2.94,-1.23l-0.4,-0.68l0.36,-0.37l0.61,1.07ZM230.83,84.39l-2.37,0.18l-0.49,-1.63l0.93,-2.09l1.94,-0.51l1.62,0.99l0.02,1.52l-1.66,1.54ZM229.43,58.25l0.11,0.65l-4.87,-0.21l-2.72,0.62l-3.1,-2.57l0.08,-1.26l0.86,-0.23l5.57,0.51l4.08,2.5ZM222.0,105.02l-0.72,1.49l-0.63,-0.19l-0.48,-0.84l0.81,-0.99l0.65,0.05l0.37,0.46ZM183.74,38.32l2.9,1.7l4.79,-0.01l1.84,1.46l-0.49,1.68l0.23,0.48l2.82,1.14l1.76,1.26l7.01,0.65l4.1,-1.1l5.03,-0.43l3.93,0.35l2.48,1.77l0.46,1.7l-1.3,1.1l-3.56,1.01l-3.23,-0.59l-7.17,0.76l-5.09,0.09l-3.99,-0.6l-6.42,-1.54l-0.79,-2.51l-0.3,-2.49l-2.64,-2.5l-5.32,-0.72l-2.52,-1.4l0.68,-1.57l4.78,0.31ZM207.38,91.35l0.4,1.56l0.56,0.26l1.06,-0.52l1.32,0.96l5.42,2.57l0.2,1.68l0.46,0.35l1.68,-0.28l1.15,0.85l-1.55,0.87l-3.61,-0.88l-1.32,-1.69l-0.57,-0.06l-2.45,2.1l-3.12,1.79l-0.7,-1.87l-0.42,-0.26l-2.16,0.24l1.39,-1.39l0.32,-3.14l0.76,-3.35l1.18,0.22ZM215.49,102.6l-2.67,1.95l-1.4,-0.07l-0.3,-0.58l1.53,-1.48l2.84,0.18ZM202.7,24.12l2.53,1.59l-2.87,1.4l-4.53,4.05l-4.25,0.38l-5.03,-0.68l-2.45,-2.04l0.03,-1.62l1.82,-1.37l0.14,-0.45l-0.38,-0.27l-4.45,0.04l-2.59,-1.76l-1.41,-2.29l1.57,-2.32l1.62,-1.66l2.44,-0.39l0.25,-0.65l-0.6,-0.74l4.86,-0.25l3.24,3.11l8.16,2.3l1.9,3.61ZM187.47,59.2l-2.76,3.49l-2.38,-0.15l-1.44,-3.84l0.04,-2.2l1.19,-1.88l2.3,-1.23l5.07,0.17l4.11,1.02l-3.24,3.72l-2.88,0.89ZM186.07,48.79l-1.08,1.53l-3.34,-0.34l-2.56,-1.1l1.03,-1.75l3.25,-1.23l1.95,1.58l0.75,1.3ZM185.71,35.32l-5.3,-0.2l-0.32,-0.71l4.31,0.07l1.3,0.84ZM180.68,32.48l-3.34,1.0l-1.79,-1.1l-0.98,-1.87l-0.15,-1.73l4.1,0.53l2.67,1.7l-0.51,1.47ZM180.9,76.31l-1.1,1.08l-3.13,-1.23l-2.12,0.43l-2.71,-1.57l1.72,-1.09l1.55,-1.72l3.81,1.9l1.98,2.2ZM169.74,54.87l2.96,0.97l4.17,-0.57l0.41,0.88l-2.14,2.11l0.09,0.64l3.55,1.92l-0.4,3.72l-3.79,1.65l-2.17,-0.35l-1.72,-1.74l-6.02,-3.5l0.03,-0.85l4.68,0.54l0.4,-0.21l-0.05,-0.45l-2.48,-2.81l2.46,-1.95ZM174.45,40.74l1.37,1.73l0.07,2.44l-1.05,3.45l-3.79,0.47l-2.32,-0.69l0.05,-2.64l-0.44,-0.41l-3.68,0.35l-0.12,-3.1l2.45,0.1l3.67,-1.73l3.41,0.29l0.37,-0.26ZM170.05,31.55l0.67,1.56l-3.33,-0.49l-4.22,-1.77l-4.35,-0.16l1.4,-0.94l-0.06,-0.7l-2.81,-1.23l-0.12,-1.39l4.39,0.68l6.62,1.98l1.81,2.47ZM134.5,58.13l-1.02,1.82l0.45,0.58l5.4,-1.39l3.33,2.29l0.49,-0.03l2.6,-2.23l1.94,1.32l2.0,4.5l0.7,0.06l1.3,-2.29l-1.63,-4.46l1.69,-0.54l2.31,0.71l2.65,1.81l2.49,7.92l8.48,4.27l-0.19,1.35l-3.79,0.33l-0.26,0.67l1.4,1.49l-0.58,1.1l-4.23,-0.64l-4.43,-1.19l-3.0,0.28l-4.66,1.47l-10.52,1.04l-1.43,-2.02l-3.42,-1.2l-2.21,0.43l-2.51,-2.86l4.84,-1.05l3.6,0.19l3.27,-0.78l0.31,-0.39l-0.31,-0.39l-4.84,-1.06l-8.79,0.27l-0.85,-1.07l5.26,-1.66l0.27,-0.45l-0.4,-0.34l-3.8,0.06l-3.81,-1.06l1.81,-3.01l1.66,-1.79l6.48,-2.81l1.97,0.71ZM158.7,56.61l-1.7,2.44l-3.2,-2.75l0.37,-0.3l3.11,-0.18l1.42,0.79ZM149.61,42.73l1.01,1.89l0.5,0.18l2.14,-0.82l2.23,0.19l0.36,2.04l-1.33,2.09l-8.28,0.76l-6.35,2.15l-3.41,0.1l-0.19,-0.96l4.9,-2.08l0.23,-0.46l-0.41,-0.31l-11.25,0.59l-2.89,-0.74l3.04,-4.44l2.14,-1.32l6.81,1.69l4.58,3.06l4.37,0.39l0.36,-0.63l-3.36,-4.6l1.85,-1.53l2.18,0.51l0.77,2.26ZM144.76,34.41l-4.36,1.44l-3.0,-1.4l1.46,-1.24l3.47,-0.52l2.96,0.71l-0.52,1.01ZM145.13,29.83l-1.9,0.66l-3.67,-0.0l2.27,-1.61l3.3,0.95ZM118.92,65.79l-6.03,2.02l-1.33,-1.9l-5.38,-2.28l2.59,-5.05l2.16,-3.14l-0.02,-0.48l-1.97,-2.41l7.64,-0.7l3.6,1.02l6.3,0.27l4.42,2.95l-2.53,0.98l-6.24,3.43l-3.1,3.28l-0.11,2.01ZM129.54,35.53l-0.28,3.37l-1.72,1.62l-2.33,0.28l-4.61,2.19l-3.86,0.76l-2.64,-0.87l3.72,-3.4l5.01,-3.34l3.72,0.07l3.0,-0.67ZM111.09,152.69l-0.67,0.24l-3.85,-1.37l-0.83,-1.17l-2.12,-1.07l-0.66,-1.02l-2.4,-0.55l-0.74,-1.71l6.02,1.45l2.0,2.55l2.52,1.39l0.73,1.27ZM87.8,134.64l0.89,0.29l1.86,-0.21l-0.65,3.34l1.69,2.33l-1.31,-1.33l-0.99,-1.62l-1.17,-0.98l-0.33,-1.82Z",
      name: "Canada"
    },
    CG: {
      path: "M466.72,276.48l-0.1,1.03l-1.25,2.97l-0.19,3.62l-0.46,1.78l-0.23,0.63l-1.61,1.19l-1.21,1.39l-1.09,2.43l0.04,2.09l-3.25,3.24l-0.5,-0.24l-0.5,-0.83l-1.36,-0.02l-0.98,0.89l-1.68,-0.99l-1.54,1.24l-1.52,-1.96l1.57,-1.14l0.11,-0.52l-0.77,-1.35l2.1,-0.66l0.39,-0.73l1.05,0.82l2.21,0.11l1.12,-1.37l0.37,-1.81l-0.27,-2.09l-1.13,-1.5l1.0,-2.69l-0.13,-0.45l-0.92,-0.58l-1.6,0.17l-0.51,-0.94l0.1,-0.61l2.75,0.09l3.97,1.24l0.51,-0.33l0.17,-1.28l1.24,-2.21l1.28,-1.14l2.76,0.49Z",
      name: "Congo"
    },
    CF: {
      path: "M461.16,278.2l-0.26,-1.19l-1.09,-0.77l-0.84,-1.17l-0.29,-1.0l-1.04,-1.15l0.08,-3.43l0.58,-0.49l1.16,-2.35l1.85,-0.17l0.61,-0.62l0.97,0.58l3.15,-0.96l2.48,-1.92l0.02,-0.96l2.81,0.02l2.36,-1.17l1.93,-2.85l1.16,-0.93l1.11,-0.3l0.27,0.86l1.34,1.47l-0.39,2.01l0.3,1.01l4.01,2.75l0.17,0.93l2.63,2.31l0.6,1.44l2.08,1.4l-3.84,-0.21l-1.94,0.88l-1.23,-0.49l-2.67,1.2l-1.29,-0.18l-0.51,0.36l-0.6,1.22l-3.35,-0.65l-1.57,-0.91l-2.42,-0.83l-1.45,0.91l-0.97,1.27l-0.26,1.56l-3.22,-0.43l-1.49,1.33l-0.94,1.62Z",
      name: "Central African Rep."
    },
    CD: {
      path: "M487.01,272.38l2.34,-0.14l1.35,1.84l1.34,0.45l0.86,-0.39l1.21,0.12l1.07,-0.41l0.54,0.89l2.04,1.54l-0.14,2.72l0.7,0.54l-1.38,1.13l-1.53,2.54l-0.17,2.05l-0.59,1.08l-0.02,1.72l-0.72,0.84l-0.66,3.01l0.63,1.32l-0.44,4.26l0.64,1.47l-0.37,1.22l0.86,1.8l1.53,1.41l0.3,1.26l0.44,0.5l-4.08,0.75l-0.92,1.81l0.51,1.34l-0.74,5.43l0.17,0.38l2.45,1.46l0.54,-0.1l0.12,1.62l-1.28,-0.01l-1.85,-2.35l-1.94,-0.45l-0.48,-1.13l-0.55,-0.2l-1.41,0.74l-1.71,-0.3l-1.01,-1.18l-2.49,-0.19l-0.44,-0.77l-1.98,-0.21l-2.88,0.36l0.11,-2.41l-0.85,-1.13l-0.16,-1.36l0.32,-1.73l-0.46,-0.89l-0.04,-1.49l-0.4,-0.39l-2.53,0.02l0.1,-0.41l-0.39,-0.49l-1.28,0.01l-0.43,0.45l-1.62,0.32l-0.83,1.79l-1.09,-0.28l-2.4,0.52l-1.37,-1.91l-1.3,-3.3l-0.38,-0.27l-7.39,-0.03l-2.46,0.42l0.5,-0.45l0.37,-1.47l0.66,-0.38l0.92,0.08l0.73,-0.82l0.87,0.02l0.31,0.68l1.4,0.36l3.59,-3.63l0.01,-2.23l1.02,-2.29l2.69,-2.39l0.43,-0.99l0.49,-1.96l0.17,-3.51l1.25,-2.95l0.36,-3.14l0.86,-1.13l1.1,-0.66l3.57,1.73l3.65,0.73l0.46,-0.21l0.8,-1.46l1.24,0.19l2.61,-1.17l0.81,0.44l1.04,-0.03l0.59,-0.66l0.7,-0.16l1.81,0.25Z",
      name: "Dem. Rep. Congo"
    },
    CZ: {
      path: "M458.46,144.88l1.22,1.01l1.47,0.23l0.13,0.93l1.36,0.68l0.54,-0.2l0.24,-0.55l1.15,0.25l0.53,1.09l1.68,0.18l0.6,0.84l-1.04,0.73l-0.96,1.28l-1.6,0.17l-0.55,0.56l-1.04,-0.46l-1.05,0.15l-2.12,-0.96l-1.05,0.34l-1.2,1.12l-1.56,-0.87l-2.57,-2.1l-0.53,-1.88l4.7,-2.52l0.71,0.26l0.9,-0.28Z",
      name: "Czech Rep."
    },
    CY: {
      path: "M504.36,193.47l0.43,0.28l-1.28,0.57l-0.92,-0.28l-0.24,-0.46l2.01,-0.13Z",
      name: "Cyprus"
    },
    CR: {
      path: "M211.34,258.05l0.48,0.99l1.6,1.6l-0.54,0.45l0.29,1.42l-0.25,1.19l-1.09,-0.59l-0.05,-1.25l-2.46,-1.42l-0.28,-0.77l-0.66,-0.45l-0.45,-0.0l-0.11,1.04l-1.32,-0.95l0.31,-1.3l-0.36,-0.6l0.31,-0.27l1.42,0.58l1.29,-0.14l0.56,0.56l0.74,0.17l0.55,-0.27Z",
      name: "Costa Rica"
    },
    CU: {
      path: "M221.21,227.25l1.27,1.02l2.19,-0.28l4.43,3.33l2.08,0.43l-0.1,0.38l0.36,0.5l1.75,0.1l1.48,0.84l-3.11,0.51l-4.15,-0.03l0.77,-0.67l-0.04,-0.64l-1.2,-0.74l-1.49,-0.16l-0.7,-0.61l-0.56,-1.4l-0.4,-0.25l-1.34,0.1l-2.2,-0.66l-0.88,-0.58l-3.18,-0.4l-0.27,-0.16l0.58,-0.74l-0.36,-0.29l-2.72,-0.05l-1.7,1.29l-0.91,0.03l-0.61,0.69l-1.01,0.22l1.11,-1.29l1.01,-0.52l3.69,-1.01l3.98,0.21l2.21,0.84Z",
      name: "Cuba"
    },
    SZ: {
      path: "M500.35,351.36l0.5,2.04l-0.38,0.89l-1.05,0.21l-1.23,-1.2l-0.02,-0.64l0.83,-1.57l1.34,0.27Z",
      name: "Swaziland"
    },
    SY: {
      path: "M511.0,199.79l0.05,-1.33l0.54,-1.36l1.28,-0.99l0.13,-0.45l-0.41,-1.11l-1.14,-0.36l-0.19,-1.74l0.52,-1.0l1.29,-1.21l0.2,-1.18l0.59,0.23l2.62,-0.76l1.36,0.52l2.06,-0.01l2.95,-1.08l3.25,-0.26l-0.67,0.94l-1.28,0.66l-0.21,0.4l0.23,2.01l-0.88,3.19l-10.15,5.73l-2.15,-0.85Z",
      name: "Syria"
    },
    KG: {
      path: "M621.35,172.32l-3.87,1.69l-0.96,1.18l-3.04,0.34l-1.13,1.86l-2.36,-0.35l-1.99,0.63l-2.39,1.4l0.06,0.95l-0.4,0.37l-4.52,0.43l-3.02,-0.93l-2.37,0.17l0.11,-0.79l2.32,0.42l1.13,-0.88l1.99,0.2l3.21,-2.14l-0.03,-0.69l-2.97,-1.57l-1.94,0.65l-1.22,-0.74l1.71,-1.58l-0.12,-0.67l-0.36,-0.15l0.32,-0.77l1.36,-0.35l4.02,1.02l0.49,-0.3l0.35,-1.59l1.09,-0.48l3.42,1.22l1.11,-0.31l7.64,0.39l1.16,1.0l1.23,0.39Z",
      name: "Kyrgyzstan"
    },
    KE: {
      path: "M506.26,284.69l1.87,-2.56l0.93,-2.15l-1.38,-4.08l-1.06,-1.6l2.82,-2.75l0.79,0.26l0.12,1.41l0.86,0.83l1.9,0.11l3.28,2.13l3.57,0.44l1.05,-1.12l1.96,-0.9l0.82,0.68l1.16,0.09l-1.78,2.45l0.03,9.12l1.3,1.94l-1.37,0.78l-0.67,1.03l-1.08,0.46l-0.34,1.67l-0.81,1.07l-0.45,1.55l-0.68,0.56l-3.2,-2.23l-0.35,-1.58l-8.86,-4.98l0.14,-1.6l-0.57,-1.04Z",
      name: "Kenya"
    },
    SS: {
      path: "M481.71,263.34l1.07,-0.72l1.2,-3.18l1.36,-0.26l1.61,1.99l0.87,0.34l1.1,-0.41l1.5,0.07l0.57,0.53l2.49,0.0l0.44,-0.63l1.07,-0.4l0.45,-0.84l0.59,-0.33l1.9,1.33l1.6,-0.2l2.83,-3.33l-0.32,-2.21l1.59,-0.52l-0.24,1.6l0.3,1.83l1.35,1.18l0.2,1.87l0.35,0.41l0.02,1.53l-0.23,0.47l-1.42,0.25l-0.85,1.44l0.3,0.6l1.4,0.16l1.11,1.08l0.59,1.13l1.03,0.53l1.28,2.36l-4.41,3.98l-1.74,0.01l-1.89,0.55l-1.47,-0.52l-1.15,0.57l-2.96,-2.62l-1.3,0.49l-1.06,-0.15l-0.79,0.39l-0.82,-0.22l-1.8,-2.7l-1.91,-1.1l-0.66,-1.5l-2.62,-2.32l-0.18,-0.94l-2.37,-1.6Z",
      name: "S. Sudan"
    },
    SR: {
      path: "M283.12,270.19l2.1,0.53l-1.08,1.95l0.2,1.72l0.93,1.49l-0.59,2.03l-0.43,0.71l-1.12,-0.42l-1.32,0.22l-0.93,-0.2l-0.46,0.26l-0.25,0.73l0.33,0.7l-0.89,-0.13l-1.39,-1.97l-0.31,-1.34l-0.97,-0.31l-0.89,-1.47l0.35,-1.61l1.45,-0.82l0.33,-1.87l2.61,0.44l0.57,-0.47l1.75,-0.16Z",
      name: "Suriname"
    },
    KH: {
      path: "M689.52,249.39l0.49,1.45l-0.28,2.74l-4.0,1.86l-0.16,0.6l0.68,0.95l-2.06,0.17l-2.05,0.97l-1.82,-0.32l-2.12,-3.7l-0.55,-2.85l1.4,-1.85l3.02,-0.45l2.23,0.35l2.01,0.98l0.51,-0.14l0.95,-1.48l1.74,0.74Z",
      name: "Cambodia"
    },
    SV: {
      path: "M195.8,250.13l1.4,-1.19l2.24,1.45l0.98,-0.27l0.44,0.2l-0.27,1.05l-1.14,-0.03l-3.64,-1.21Z",
      name: "El Salvador"
    },
    SK: {
      path: "M476.82,151.17l-1.14,1.9l-2.73,-0.92l-0.82,0.2l-0.74,0.8l-3.46,0.73l-0.47,0.69l-1.76,0.33l-1.88,-1.0l-0.18,-0.81l0.38,-0.75l1.87,-0.32l1.74,-1.89l0.83,0.16l0.79,-0.34l1.51,1.04l1.34,-0.63l1.25,0.3l1.65,-0.42l1.81,0.95Z",
      name: "Slovakia"
    },
    KR: {
      path: "M737.51,185.84l0.98,-0.1l0.87,-1.17l2.69,-0.32l0.33,-0.29l1.76,2.79l0.58,1.76l0.02,3.12l-0.8,1.32l-2.21,0.55l-1.93,1.13l-1.8,0.19l-0.2,-1.1l0.43,-2.28l-0.95,-2.56l1.43,-0.37l0.23,-0.62l-1.43,-2.06Z",
      name: "Korea"
    },
    SI: {
      path: "M456.18,162.07l-0.51,-1.32l0.18,-1.05l1.69,0.2l1.42,-0.71l2.09,-0.07l0.62,-0.51l0.21,0.47l-1.61,0.67l-0.44,1.34l-0.66,0.24l-0.26,0.82l-1.22,-0.49l-0.84,0.46l-0.69,-0.04Z",
      name: "Slovenia"
    },
    KP: {
      path: "M736.77,185.16l-0.92,-0.42l-0.88,0.62l-1.21,-0.88l0.96,-1.15l0.59,-2.59l-0.46,-0.74l-2.09,-0.77l1.64,-1.52l2.72,-1.58l1.58,-1.91l1.11,0.78l2.17,0.11l0.41,-0.5l-0.3,-1.22l3.52,-1.18l0.94,-1.4l0.98,1.08l-2.19,2.18l0.01,2.14l-1.06,0.54l-1.41,1.4l-1.7,0.52l-1.25,1.09l-0.14,1.98l0.94,0.45l1.15,1.04l-0.13,0.26l-2.6,0.29l-1.13,1.29l-1.22,0.08Z",
      name: "Dem. Rep. Korea"
    },
    SO: {
      path: "M525.13,288.48l-1.13,-1.57l-0.03,-8.86l2.66,-3.38l1.67,-0.13l2.13,-1.69l3.41,-0.23l7.08,-7.55l2.91,-3.69l0.08,-4.82l2.98,-0.67l1.24,-0.86l0.45,-0.0l-0.2,3.0l-1.21,3.62l-2.73,5.97l-2.13,3.65l-5.03,6.16l-8.56,6.4l-2.78,3.08l-0.8,1.56Z",
      name: "Somalia"
    },
    SN: {
      path: "M390.09,248.21l0.12,1.55l0.49,1.46l0.96,0.82l0.05,1.28l-1.26,-0.19l-0.75,0.33l-1.84,-0.61l-5.84,-0.13l-2.54,0.51l-0.22,-1.03l1.77,0.04l2.01,-0.91l1.03,0.48l1.09,0.04l1.29,-0.62l0.14,-0.58l-0.51,-0.74l-1.81,0.25l-1.13,-0.63l-0.79,0.04l-0.72,0.61l-2.31,0.06l-0.92,-1.77l-0.81,-0.64l0.64,-0.35l2.46,-3.74l1.04,0.19l1.38,-0.56l1.19,-0.02l2.72,1.37l3.03,3.48Z",
      name: "Senegal"
    },
    SL: {
      path: "M394.46,264.11l-1.73,1.98l-0.58,1.33l-2.07,-1.06l-1.22,-1.26l-0.65,-2.39l1.16,-0.96l0.67,-1.17l1.21,-0.52l1.66,0.0l1.03,1.64l0.52,2.41Z",
      name: "Sierra Leone"
    },
    SB: {
      path: "M826.69,311.6l-0.61,0.09l-0.2,-0.33l0.37,0.15l0.44,0.09ZM824.18,307.38l-0.26,-0.3l-0.31,-0.91l0.03,0.0l0.54,1.21ZM823.04,309.33l-1.66,-0.22l-0.2,-0.52l1.16,0.28l0.69,0.46ZM819.28,304.68l1.14,0.65l0.02,0.03l-0.81,-0.44l-0.35,-0.23Z",
      name: "Solomon Is."
    },
    SA: {
      path: "M537.53,210.34l2.0,0.24l0.9,1.32l1.49,-0.06l0.87,2.08l1.29,0.76l0.51,0.99l1.56,1.03l-0.1,1.9l0.32,0.9l1.58,2.47l0.76,0.53l0.7,-0.04l1.68,4.23l7.53,1.33l0.51,-0.29l0.77,1.25l-1.55,4.87l-7.29,2.52l-7.3,1.03l-2.34,1.17l-1.88,2.74l-0.76,0.28l-0.82,-0.78l-0.91,0.12l-2.88,-0.51l-3.51,0.25l-0.86,-0.56l-0.57,0.15l-0.66,1.27l0.16,1.11l-0.43,0.32l-0.93,-1.4l-0.33,-1.16l-1.23,-0.88l-1.27,-2.06l-0.78,-2.22l-1.73,-1.79l-1.14,-0.48l-1.54,-2.31l-0.21,-3.41l-1.44,-2.93l-1.27,-1.16l-1.33,-0.57l-1.31,-3.37l-0.77,-0.67l-0.97,-1.97l-2.8,-4.03l-1.06,-0.17l0.37,-1.96l0.2,-0.72l2.74,0.3l1.08,-0.84l0.6,-0.94l1.74,-0.35l0.65,-1.03l0.71,-0.4l0.1,-0.62l-2.06,-2.28l4.39,-1.22l0.48,-0.37l2.77,0.69l3.66,1.9l7.03,5.5l4.87,0.3Z",
      name: "Saudi Arabia"
    },
    SE: {
      path: "M480.22,89.3l-4.03,1.17l-2.43,2.86l0.26,2.57l-8.77,6.64l-1.78,5.79l1.78,2.68l2.22,1.96l-2.07,3.77l-2.72,1.13l-0.95,6.04l-1.29,3.01l-2.74,-0.31l-0.4,0.22l-1.31,2.59l-2.34,0.13l-0.75,-3.09l-2.08,-4.03l-1.83,-4.96l1.0,-1.93l2.14,-2.7l0.83,-4.45l-1.6,-2.17l-0.15,-4.94l1.48,-3.39l2.58,-0.15l0.87,-1.59l-0.78,-1.57l3.76,-5.59l4.04,-7.48l2.17,0.01l0.39,-0.29l0.57,-2.07l4.37,0.64l0.46,-0.34l0.33,-2.56l1.1,-0.13l6.94,4.87l0.06,6.32l0.66,1.36Z",
      name: "Sweden"
    },
    SD: {
      path: "M505.98,259.4l-0.34,-0.77l-1.17,-0.9l-0.26,-1.61l0.29,-1.81l-0.34,-0.46l-1.16,-0.17l-0.54,0.59l-1.23,0.11l-0.28,0.65l0.53,0.65l0.17,1.22l-2.44,3.0l-0.96,0.19l-2.39,-1.4l-0.95,0.52l-0.38,0.78l-1.11,0.41l-0.29,0.5l-1.94,0.0l-0.54,-0.52l-1.81,-0.09l-0.95,0.4l-2.45,-2.35l-2.07,0.54l-0.73,1.26l-0.6,2.1l-1.25,0.58l-0.75,-0.62l0.27,-2.65l-1.48,-1.78l-0.22,-1.48l-0.92,-0.96l-0.02,-1.29l-0.57,-1.16l-0.68,-0.16l0.69,-1.29l-0.18,-1.14l0.65,-0.62l0.03,-0.55l-0.36,-0.41l1.55,-2.97l1.91,0.16l0.43,-0.4l-0.1,-10.94l2.49,-0.01l0.4,-0.4l-0.0,-4.82l29.02,0.0l0.64,2.04l-0.49,0.66l0.36,2.69l0.93,3.16l2.12,1.55l-0.89,1.04l-1.72,0.39l-0.98,0.9l-1.43,5.65l0.24,1.15l-0.38,2.06l-0.96,2.38l-1.53,1.31l-1.32,2.91l-1.22,0.86l-0.37,1.34Z",
      name: "Sudan"
    },
    DO: {
      path: "M241.8,239.2l0.05,-0.65l-0.46,-0.73l0.42,-0.44l0.19,-1.0l-0.09,-1.53l1.66,0.01l1.99,0.63l0.33,0.67l1.28,0.19l0.33,0.76l1.0,0.08l0.8,0.62l-0.45,0.51l-1.13,-0.47l-1.88,-0.01l-1.27,0.59l-0.75,-0.55l-1.01,0.54l-0.79,1.4l-0.23,-0.61Z",
      name: "Dominican Rep."
    },
    DJ: {
      path: "M528.43,256.18l-0.45,0.66l-0.58,-0.25l-1.51,0.13l-0.18,-1.01l1.45,-1.95l0.83,0.17l0.77,-0.44l0.2,1.0l-1.2,0.51l-0.06,0.7l0.73,0.47Z",
      name: "Djibouti"
    },
    DK: {
      path: "M452.28,129.07l-1.19,2.24l-2.13,-1.6l-0.23,-0.95l2.98,-0.95l0.57,1.26ZM447.74,126.31l-0.26,0.57l-0.88,-0.07l-1.8,2.53l0.48,1.69l-1.09,0.36l-1.61,-0.39l-0.89,-1.69l-0.07,-3.43l0.96,-1.73l2.02,-0.2l1.09,-1.07l1.33,-0.67l-0.05,1.06l-0.73,1.41l0.3,1.0l1.2,0.64Z",
      name: "Denmark"
    },
    DE: {
      path: "M453.14,155.55l-0.55,-0.36l-1.2,-0.1l-1.87,0.57l-2.13,-0.13l-0.56,0.63l-0.86,-0.6l-0.96,0.09l-2.57,-0.93l-0.85,0.67l-1.47,-0.02l0.24,-1.75l1.23,-2.14l-0.28,-0.59l-3.52,-0.58l-0.92,-0.66l0.12,-1.2l-0.48,-0.88l0.27,-2.17l-0.37,-3.03l1.41,-0.22l0.63,-1.26l0.66,-3.19l-0.41,-1.18l0.26,-0.39l1.66,-0.15l0.33,0.54l0.62,0.07l1.7,-1.69l-0.54,-3.02l1.37,0.33l1.31,-0.37l0.31,1.18l2.25,0.71l-0.02,0.92l0.5,0.4l2.55,-0.65l1.34,-0.87l2.57,1.24l1.06,0.98l0.48,1.44l-0.57,0.74l-0.0,0.48l0.87,1.15l0.57,1.64l-0.14,1.29l0.82,1.7l-1.5,-0.07l-0.56,0.57l-4.47,2.15l-0.22,0.54l0.68,2.26l2.58,2.16l-0.66,1.11l-0.79,0.36l-0.23,0.43l0.32,1.87Z",
      name: "Germany"
    },
    YE: {
      path: "M528.27,246.72l0.26,-0.42l-0.22,-1.01l0.19,-1.5l0.92,-0.69l-0.07,-1.35l0.39,-0.75l1.01,0.47l3.34,-0.27l3.76,0.41l0.95,0.81l1.36,-0.58l1.74,-2.62l2.18,-1.09l6.86,-0.94l2.48,5.41l-1.64,0.76l-0.56,1.9l-6.23,2.16l-2.29,1.8l-1.93,0.05l-1.41,1.02l-4.24,0.74l-1.72,1.49l-3.28,0.19l-0.52,-1.18l0.02,-1.51l-1.34,-3.29Z",
      name: "Yemen"
    },
    AT: {
      path: "M462.89,152.8l0.04,2.25l-1.07,0.0l-0.33,0.63l0.36,0.51l-1.04,2.13l-2.02,0.07l-1.33,0.7l-5.29,-0.99l-0.47,-0.93l-0.44,-0.21l-2.47,0.55l-0.42,0.51l-3.18,-0.81l0.43,-0.91l1.12,0.78l0.6,-0.17l0.25,-0.58l1.93,0.12l1.86,-0.56l1.0,0.08l0.68,0.57l0.62,-0.15l0.26,-0.77l-0.3,-1.78l0.8,-0.44l0.68,-1.15l1.52,0.85l0.47,-0.06l1.34,-1.25l0.64,-0.17l1.81,0.92l1.28,-0.11l0.7,0.37Z",
      name: "Austria"
    },
    DZ: {
      path: "M441.46,188.44l-0.32,1.07l0.39,2.64l-0.54,2.16l-1.58,1.82l0.37,2.39l1.91,1.55l0.18,0.8l1.42,1.03l1.84,7.23l0.12,1.16l-0.57,5.0l0.2,1.51l-0.87,0.99l-0.02,0.51l1.41,1.86l0.14,1.2l0.89,1.48l0.5,0.16l0.98,-0.41l1.73,1.08l0.82,1.23l-8.22,4.81l-7.23,5.11l-3.43,1.13l-2.3,0.21l-0.28,-1.59l-2.56,-1.09l-0.67,-1.25l-26.12,-17.86l0.01,-3.47l3.77,-1.88l2.44,-0.41l2.12,-0.75l1.08,-1.42l2.81,-1.05l0.35,-2.08l1.33,-0.29l1.04,-0.94l3.47,-0.69l0.46,-1.08l-0.1,-0.45l-0.58,-0.52l-0.82,-2.81l-0.19,-1.83l-0.78,-1.49l2.03,-1.31l2.63,-0.48l1.7,-1.22l2.31,-0.84l8.24,-0.73l1.49,0.38l2.28,-1.1l2.46,-0.02l0.92,0.6l1.35,-0.05Z",
      name: "Algeria"
    },
    US: {
      path: "M892.72,99.2l1.31,0.53l1.41,-0.37l1.89,0.98l1.89,0.42l-1.32,0.58l-2.9,-1.53l-2.08,0.22l-0.26,-0.15l0.07,-0.67ZM183.22,150.47l0.37,1.47l1.12,0.85l4.23,0.7l2.39,0.98l2.17,-0.38l1.85,0.5l-1.55,0.65l-3.49,2.61l-0.16,0.77l0.5,0.39l2.33,-0.61l1.77,1.02l5.15,-2.4l-0.31,0.65l0.25,0.56l1.36,0.38l1.71,1.16l4.7,-0.88l0.67,0.85l1.31,0.21l0.58,0.58l-1.34,0.17l-2.18,-0.32l-3.6,0.89l-2.71,3.25l0.35,0.9l0.59,-0.0l0.55,-0.6l-1.36,4.65l0.29,3.09l0.67,1.58l0.61,0.45l1.77,-0.44l1.6,-1.96l0.14,-2.21l-0.82,-1.96l0.11,-1.13l1.19,-2.37l0.44,-0.33l0.48,0.75l0.4,-0.29l0.4,-1.37l0.6,-0.47l0.24,-0.8l1.69,0.49l1.65,1.08l-0.03,2.37l-1.27,1.13l-0.0,1.13l0.87,0.36l1.66,-1.29l0.5,0.17l0.5,2.6l-2.49,3.75l0.17,0.61l1.54,0.62l1.48,0.17l1.92,-0.44l4.72,-2.15l2.16,-1.8l-0.05,-1.24l0.75,-0.22l3.92,0.36l2.12,-1.05l0.21,-0.4l-0.28,-1.48l3.27,-2.4l8.32,-0.02l0.56,-0.82l1.9,-0.77l0.93,-1.51l0.74,-2.37l1.58,-1.98l0.92,0.62l1.47,-0.47l0.8,0.66l-0.0,4.09l1.96,2.6l-2.34,1.31l-5.37,2.09l-1.83,2.72l0.02,1.79l0.83,1.59l0.54,0.23l-6.19,0.94l-2.2,0.89l-0.23,0.48l0.45,0.29l2.99,-0.46l-2.19,0.56l-1.13,0.0l-0.15,-0.32l-0.48,0.08l-0.76,0.82l0.22,0.67l0.32,0.06l-0.41,1.62l-1.27,1.58l-1.48,-1.07l-0.49,-0.04l-0.16,0.46l0.52,1.58l0.61,0.59l0.03,0.79l-0.95,1.38l-1.21,-1.22l-0.27,-2.27l-0.35,-0.35l-0.42,0.25l-0.48,1.27l0.33,1.41l-0.97,-0.27l-0.48,0.24l0.18,0.5l1.52,0.83l0.1,2.52l0.79,0.51l0.52,3.42l-1.42,1.88l-2.47,0.8l-1.71,1.66l-1.31,0.25l-1.27,1.03l-0.43,0.99l-2.69,1.78l-2.64,3.03l-0.45,2.12l0.45,2.08l0.85,2.38l1.09,1.9l0.04,1.2l1.16,3.06l-0.18,2.69l-0.55,1.43l-0.47,0.21l-0.89,-0.23l-0.49,-1.18l-0.87,-0.56l-2.75,-5.16l0.48,-1.68l-0.72,-1.78l-2.01,-2.38l-1.12,-0.53l-2.72,1.18l-1.47,-1.35l-1.57,-0.68l-2.99,0.31l-2.17,-0.3l-2.0,0.19l-1.15,0.46l-0.19,0.58l0.39,0.63l0.14,1.34l-0.84,-0.2l-0.84,0.46l-1.58,-0.07l-2.08,-1.44l-2.09,0.33l-1.91,-0.62l-3.73,0.84l-2.39,2.07l-2.54,1.22l-1.45,1.41l-0.61,1.38l0.34,3.71l-0.29,0.02l-3.5,-1.33l-1.25,-3.11l-1.44,-1.5l-2.24,-3.56l-1.76,-1.09l-2.27,-0.01l-1.71,2.07l-1.76,-0.69l-1.16,-0.74l-1.52,-2.98l-3.93,-3.16l-4.34,-0.0l-0.4,0.4l-0.0,0.74l-6.5,0.02l-9.02,-3.14l-0.34,-0.71l-5.7,0.49l-0.43,-1.29l-1.62,-1.61l-1.14,-0.38l-0.55,-0.88l-1.28,-0.13l-1.01,-0.77l-2.22,-0.27l-0.43,-0.3l-0.36,-1.58l-2.4,-2.83l-2.01,-3.85l-0.06,-0.9l-2.92,-3.26l-0.33,-2.29l-1.3,-1.66l0.52,-2.37l-0.09,-2.57l-0.78,-2.3l0.95,-2.82l0.61,-5.68l-0.47,-4.27l-1.46,-4.08l3.19,0.79l1.26,2.83l0.69,0.08l0.69,-1.14l-1.1,-4.79l68.76,-0.0l0.4,-0.4l0.14,-0.86ZM32.44,67.52l1.73,1.97l0.55,0.05l0.99,-0.79l3.65,0.24l-0.09,0.62l0.32,0.45l3.83,0.77l2.61,-0.43l5.19,1.4l4.84,0.43l1.89,0.57l3.42,-0.7l6.14,1.87l-0.03,38.06l0.38,0.4l2.39,0.11l2.31,0.98l3.9,3.99l0.55,0.04l2.4,-2.03l2.16,-1.04l1.2,1.71l3.95,3.14l4.09,6.63l4.2,2.29l0.06,1.83l-1.02,1.23l-1.16,-1.08l-2.04,-1.03l-0.67,-2.89l-3.28,-3.03l-1.65,-3.57l-6.35,-0.32l-2.82,-1.01l-5.26,-3.85l-6.77,-2.04l-3.53,0.3l-4.81,-1.69l-3.25,-1.63l-2.78,0.8l-0.28,0.46l0.44,2.21l-3.91,0.96l-2.26,1.27l-2.3,0.65l-0.27,-1.65l1.05,-3.42l2.49,-1.09l0.16,-0.6l-0.69,-0.96l-0.55,-0.1l-3.19,2.12l-1.78,2.56l-3.55,2.61l-0.04,0.61l1.56,1.52l-2.07,2.29l-5.11,2.57l-0.77,1.66l-3.76,1.77l-0.92,1.73l-2.69,1.38l-1.81,-0.22l-6.95,3.32l-3.97,0.91l4.85,-2.5l2.59,-1.86l3.26,-0.52l1.19,-1.4l3.42,-2.1l2.59,-2.27l0.42,-2.68l1.23,-2.1l-0.04,-0.46l-0.45,-0.11l-2.68,1.03l-0.63,-0.49l-0.53,0.03l-1.05,1.04l-1.36,-1.54l-0.66,0.08l-0.32,0.62l-0.58,-1.14l-0.56,-0.16l-2.41,1.42l-1.07,-0.0l-0.17,-1.75l0.3,-1.71l-1.61,-1.33l-3.41,0.59l-1.96,-1.63l-1.57,-0.84l-0.15,-2.21l-1.7,-1.43l0.82,-1.88l1.99,-2.12l0.88,-1.92l1.71,-0.24l2.04,0.51l1.87,-1.77l1.91,0.25l1.91,-1.23l0.17,-0.43l-0.47,-1.82l-1.07,-0.7l1.39,-1.17l0.12,-0.45l-0.39,-0.26l-1.65,0.07l-2.66,0.88l-0.75,0.78l-1.92,-0.8l-3.46,0.44l-3.44,-0.91l-1.06,-1.61l-2.65,-1.99l2.91,-1.43l5.5,-2.0l1.52,0.0l-0.26,1.62l0.41,0.46l5.29,-0.16l0.3,-0.65l-2.03,-2.59l-3.14,-1.68l-1.79,-2.12l-2.4,-1.83l-3.09,-1.24l1.04,-1.69l4.23,-0.14l3.36,-2.07l0.73,-2.27l2.39,-1.99l2.42,-0.52l4.65,-1.97l2.46,0.23l3.71,-2.35l3.5,0.89ZM37.6,123.41l-2.25,1.23l-0.95,-0.69l-0.29,-1.24l3.21,-1.63l1.42,0.21l0.67,0.7l-1.8,1.42ZM31.06,234.03l0.98,0.47l0.74,0.87l-1.77,1.07l-0.44,-1.53l0.49,-0.89ZM29.34,232.07l0.18,0.05l0.08,0.05l-0.16,0.03l-0.11,-0.14ZM25.16,230.17l0.05,-0.03l0.18,0.22l-0.13,-0.01l-0.1,-0.18ZM5.89,113.26l-1.08,0.41l-2.21,-1.12l1.53,-0.4l1.62,0.28l0.14,0.83Z",
      name: "United States"
    },
    LV: {
      path: "M489.16,122.85l0.96,0.66l0.22,1.65l0.68,1.76l-3.65,1.7l-2.23,-1.58l-1.29,-0.26l-0.68,-0.77l-2.42,0.34l-4.16,-0.23l-2.47,0.9l0.06,-1.98l1.13,-2.06l1.95,-1.02l2.12,2.58l2.01,-0.07l0.38,-0.33l0.44,-2.52l1.76,-0.53l3.06,1.7l2.15,0.07Z",
      name: "Latvia"
    },
    UY: {
      path: "M286.85,372.74l-0.92,1.5l-2.59,1.44l-1.69,-0.52l-1.42,0.26l-2.39,-1.19l-1.52,0.08l-1.27,-1.3l0.16,-1.5l0.56,-0.79l-0.02,-2.73l1.21,-4.74l1.19,-0.21l2.37,2.0l1.08,0.03l4.36,3.17l1.22,1.6l-0.96,1.5l0.61,1.4Z",
      name: "Uruguay"
    },
    LB: {
      path: "M510.37,198.01l-0.88,0.51l1.82,-3.54l0.62,0.08l0.22,0.61l-1.13,0.88l-0.65,1.47Z",
      name: "Lebanon"
    },
    LA: {
      path: "M689.54,248.53l-1.76,-0.74l-0.49,0.15l-0.94,1.46l-1.32,-0.64l0.62,-0.98l0.11,-2.17l-2.04,-2.42l-0.25,-2.65l-1.9,-2.1l-2.15,-0.31l-0.78,0.91l-1.12,0.06l-1.05,-0.4l-2.06,1.2l-0.04,-1.59l0.61,-2.68l-0.36,-0.49l-1.35,-0.1l-0.11,-1.23l-0.96,-0.88l1.96,-1.89l0.39,0.36l1.33,0.07l0.42,-0.45l-0.34,-2.66l0.7,-0.21l1.28,1.81l1.11,2.35l0.36,0.23l2.82,0.02l0.71,1.67l-1.39,0.65l-0.72,0.93l0.13,0.6l2.91,1.51l3.6,5.25l1.88,1.78l0.56,1.62l-0.35,1.96Z",
      name: "Lao PDR"
    },
    TW: {
      path: "M724.01,226.68l-0.74,1.48l-0.9,-1.52l-0.25,-1.74l1.38,-2.44l1.73,-1.74l0.64,0.44l-1.85,5.52Z",
      name: "Taiwan"
    },
    TT: {
      path: "M266.64,259.32l0.28,-1.16l1.13,-0.22l-0.06,1.2l-1.35,0.18Z",
      name: "Trinidad and Tobago"
    },
    TR: {
      path: "M513.21,175.47l3.64,1.17l3.05,-0.44l2.1,0.26l3.11,-1.56l2.46,-0.13l2.19,1.33l0.33,0.82l-0.22,1.33l0.25,0.44l2.28,1.13l-1.17,0.57l-0.21,0.45l0.75,3.2l-0.41,1.16l1.13,1.92l-0.55,0.22l-0.9,-0.67l-2.91,-0.37l-1.24,0.46l-4.23,0.41l-2.81,1.05l-1.91,0.01l-1.52,-0.53l-2.58,0.75l-0.66,-0.45l-0.62,0.3l-0.12,1.45l-0.89,0.84l-0.47,-0.67l0.79,-1.3l-0.41,-0.2l-1.43,0.23l-2.0,-0.63l-2.02,1.65l-3.51,0.3l-2.13,-1.53l-2.7,-0.1l-0.86,1.24l-1.38,0.27l-2.29,-1.44l-2.71,-0.01l-1.37,-2.65l-1.68,-1.52l1.07,-1.99l-0.09,-0.49l-1.27,-1.12l2.37,-2.41l3.7,-0.11l1.28,-2.24l4.49,0.37l3.21,-1.97l2.81,-0.82l3.99,-0.06l4.29,2.07ZM488.79,176.72l-1.72,1.31l-0.5,-0.88l1.37,-2.57l-0.7,-0.85l1.7,-0.63l1.8,0.34l0.46,1.17l1.76,0.78l-2.87,0.32l-1.3,1.01Z",
      name: "Turkey"
    },
    LK: {
      path: "M624.16,268.99l-1.82,0.48l-0.99,-1.67l-0.42,-3.46l0.95,-3.43l1.21,0.98l2.26,4.19l-0.34,2.33l-0.85,0.58Z",
      name: "Sri Lanka"
    },
    TN: {
      path: "M448.1,188.24l-1.0,1.27l-0.02,1.32l0.84,0.88l-0.28,2.09l-1.53,1.32l-0.12,0.42l0.48,1.54l1.42,0.32l0.53,1.11l0.9,0.52l-0.11,1.67l-3.54,2.64l-0.1,2.38l-0.58,0.3l-0.96,-4.45l-1.54,-1.25l-0.16,-0.78l-1.92,-1.56l-0.18,-1.76l1.51,-1.62l0.59,-2.34l-0.38,-2.78l0.42,-1.21l2.45,-1.05l1.29,0.26l-0.06,1.11l0.58,0.38l1.47,-0.73Z",
      name: "Tunisia"
    },
    TL: {
      path: "M734.55,307.93l-0.1,-0.97l4.5,-0.86l-2.82,1.28l-1.59,0.55Z",
      name: "Timor-Leste"
    },
    TM: {
      path: "M553.03,173.76l-0.04,0.34l-0.09,-0.22l0.13,-0.12ZM555.87,172.66l0.45,-0.1l1.48,0.74l2.06,2.43l4.07,-0.18l0.38,-0.51l-0.32,-1.19l1.92,-0.94l1.91,-1.59l2.94,1.39l0.43,2.47l1.19,0.67l2.58,-0.13l0.62,0.4l1.32,3.12l4.54,3.44l2.67,1.45l3.06,1.14l-0.04,1.05l-1.33,-0.75l-0.59,0.19l-0.32,0.84l-2.2,0.81l-0.46,2.13l-1.21,0.74l-1.91,0.42l-0.73,1.33l-1.56,0.31l-2.22,-0.94l-0.2,-2.17l-0.38,-0.36l-1.73,-0.09l-2.76,-2.46l-2.14,-0.4l-2.84,-1.48l-1.78,-0.27l-1.24,0.53l-1.57,-0.08l-2.0,1.69l-1.7,0.43l-0.36,-1.58l0.36,-2.98l-0.22,-0.4l-1.65,-0.84l0.54,-1.69l-0.34,-0.52l-1.22,-0.13l0.36,-1.64l2.22,0.59l2.2,-0.95l0.12,-0.65l-1.77,-1.74l-0.66,-1.57Z",
      name: "Turkmenistan"
    },
    TJ: {
      path: "M597.75,178.82l-2.54,-0.44l-0.47,0.34l-0.24,1.7l0.43,0.45l2.64,-0.22l3.18,0.95l4.39,-0.41l0.56,2.37l0.52,0.29l0.67,-0.24l1.11,0.49l0.21,2.13l-3.76,-0.21l-1.8,1.32l-1.76,0.74l-0.61,-0.58l0.21,-2.23l-0.64,-0.49l-0.07,-0.93l-1.36,-0.66l-0.45,0.07l-1.08,1.01l-0.55,1.48l-1.31,-0.05l-0.95,1.16l-0.9,-0.35l-1.86,0.74l1.26,-2.83l-0.54,-2.17l-1.67,-0.82l0.33,-0.66l2.18,-0.04l1.19,-1.63l0.76,-1.79l2.43,-0.5l-0.26,1.0l0.73,1.05Z",
      name: "Tajikistan"
    },
    LS: {
      path: "M491.06,363.48l-0.49,0.15l-1.49,-1.67l1.1,-1.43l2.19,-1.44l1.51,1.27l-0.98,1.82l-1.23,0.38l-0.62,0.93Z",
      name: "Lesotho"
    },
    TH: {
      path: "M670.27,255.86l-1.41,3.87l0.15,2.0l0.38,0.36l1.38,0.07l0.9,2.04l0.55,2.34l1.4,1.44l1.61,0.38l0.96,0.97l-0.5,0.64l-1.1,0.2l-0.34,-1.18l-2.04,-1.1l-0.63,0.23l-0.63,-0.62l-0.48,-1.3l-2.56,-2.63l-0.73,0.41l0.95,-3.89l2.16,-4.22ZM670.67,254.77l-0.92,-2.18l-0.26,-2.61l-2.14,-3.06l0.71,-0.49l0.89,-2.59l-3.61,-5.45l0.87,-0.51l1.05,-2.58l1.74,-0.18l2.6,-1.59l0.76,0.56l0.13,1.39l0.37,0.36l1.23,0.09l-0.51,2.28l0.05,2.42l0.6,0.34l2.43,-1.42l0.77,0.39l1.47,-0.07l0.71,-0.88l1.48,0.14l1.71,1.88l0.25,2.65l1.92,2.11l-0.1,1.89l-0.61,0.86l-2.22,-0.33l-3.5,0.64l-1.6,2.12l0.36,2.58l-1.51,-0.79l-1.84,-0.01l0.28,-1.52l-0.4,-0.47l-2.21,0.01l-0.4,0.37l-0.19,2.74l-0.34,0.93Z",
      name: "Thailand"
    },
    TF: {
      path: "M596.68,420.38l-3.2,0.18l-0.05,-1.26l0.39,-1.41l1.3,0.78l2.08,0.35l-0.52,1.36Z",
      name: "Fr. S. Antarctic Lands"
    },
    TG: {
      path: "M422.7,257.63l-0.09,1.23l1.53,1.52l0.08,1.09l0.5,0.65l-0.11,5.62l0.49,1.47l-1.31,0.35l-1.02,-2.13l-0.18,-1.12l0.53,-2.19l-0.63,-1.16l-0.22,-3.68l-1.01,-1.4l0.07,-0.28l1.37,0.03Z",
      name: "Togo"
    },
    TD: {
      path: "M480.25,235.49l0.12,9.57l-2.1,0.05l-1.14,1.89l-0.69,1.63l0.34,0.73l-0.66,0.91l0.24,0.89l-0.86,1.95l0.45,0.5l0.6,-0.1l0.34,0.64l0.03,1.38l0.9,1.04l-1.45,0.43l-1.27,1.03l-1.83,2.76l-2.16,1.07l-2.31,-0.15l-0.86,0.25l-0.26,0.49l0.17,0.61l-2.11,1.68l-2.85,0.87l-1.09,-0.57l-0.73,0.66l-1.12,0.1l-1.1,-3.12l-1.25,-0.64l-1.22,-1.22l0.29,-0.64l3.01,0.04l0.35,-0.6l-1.3,-2.2l-0.08,-3.31l-0.97,-1.66l0.22,-1.04l-0.38,-0.48l-1.22,-0.04l0.0,-1.25l-0.98,-1.07l0.96,-3.01l3.25,-2.65l0.13,-3.33l0.95,-5.18l0.52,-1.07l-0.1,-0.48l-0.91,-0.78l-0.2,-0.96l-0.8,-0.58l-0.55,-3.65l2.1,-1.2l19.57,9.83Z",
      name: "Chad"
    },
    LY: {
      path: "M483.48,203.15l-0.75,1.1l0.29,1.39l-0.6,1.83l0.73,2.14l0.0,24.12l-2.48,0.01l-0.41,0.85l-19.41,-9.76l-4.41,2.28l-1.37,-1.33l-3.82,-1.1l-1.14,-1.65l-1.98,-1.23l-1.22,0.32l-0.66,-1.11l-0.17,-1.26l-1.28,-1.69l0.87,-1.19l-0.07,-4.34l0.43,-2.27l-0.86,-3.45l1.13,-0.76l0.22,-1.16l-0.2,-1.03l3.48,-2.61l0.29,-1.94l2.45,0.8l1.18,-0.21l1.98,0.44l3.15,1.18l1.37,2.54l5.72,1.67l2.64,1.35l1.61,-0.72l1.29,-1.34l-0.44,-2.34l0.66,-1.13l1.67,-1.21l1.57,-0.35l3.14,0.53l1.08,1.28l3.99,0.78l0.36,0.54Z",
      name: "Libya"
    },
    AE: {
      path: "M550.76,223.97l1.88,-0.4l3.84,0.02l4.78,-4.75l0.19,0.36l0.26,1.58l-0.81,0.01l-0.39,0.35l-0.08,2.04l-0.81,0.63l-0.01,0.96l-0.66,0.99l-0.39,1.41l-7.08,-1.25l-0.7,-1.96Z",
      name: "United Arab Emirates"
    },
    VE: {
      path: "M240.68,256.69l0.53,0.75l-0.02,1.06l-1.07,1.78l0.95,2.0l0.42,0.22l1.4,-0.44l0.56,-1.83l-0.77,-1.17l-0.1,-1.47l2.82,-0.93l0.26,-0.49l-0.28,-0.96l0.3,-0.28l0.66,1.31l1.96,0.26l1.4,1.22l0.08,0.68l0.39,0.35l4.81,-0.22l1.49,1.11l1.92,0.31l1.67,-0.84l0.22,-0.6l3.44,-0.14l-0.17,0.55l0.86,1.19l2.19,0.35l1.67,1.1l0.37,1.86l0.41,0.32l1.55,0.17l-1.66,1.35l-0.22,0.92l0.65,0.97l-1.67,0.54l-0.3,0.4l0.04,0.99l-0.56,0.57l-0.01,0.55l1.85,2.27l-0.66,0.69l-4.47,1.29l-0.72,0.54l-3.69,-0.9l-0.71,0.27l-0.02,0.7l0.91,0.53l-0.08,1.54l0.35,1.58l0.35,0.31l1.66,0.17l-1.3,0.52l-0.48,1.13l-2.68,0.91l-0.6,0.77l-1.57,0.13l-1.17,-1.13l-0.8,-2.52l-1.25,-1.26l1.02,-1.23l-1.29,-2.95l0.18,-1.62l1.0,-2.21l-0.2,-0.49l-1.14,-0.46l-4.02,0.36l-1.82,-2.1l-1.57,-0.33l-2.99,0.22l-1.06,-0.97l0.25,-1.23l-0.2,-1.01l-0.59,-0.69l-0.29,-1.06l-1.08,-0.39l0.78,-2.79l1.9,-2.11Z",
      name: "Venezuela"
    },
    AF: {
      path: "M600.7,188.88l-1.57,1.3l-0.1,0.48l0.8,2.31l-1.09,1.04l-0.03,1.27l-0.48,0.71l-2.16,-0.08l-0.37,0.59l0.78,1.48l-1.38,0.69l-1.06,1.69l0.06,1.7l-0.65,0.52l-0.91,-0.21l-1.91,0.36l-0.48,0.77l-1.88,0.13l-1.4,1.56l-0.18,2.32l-2.91,1.02l-1.65,-0.23l-0.71,0.55l-1.41,-0.3l-2.41,0.39l-3.52,-1.17l1.96,-2.35l-0.21,-1.78l-0.3,-0.34l-1.63,-0.4l-0.19,-1.58l-0.75,-2.03l0.95,-1.36l-0.19,-0.6l-0.73,-0.28l1.47,-4.8l2.14,0.9l2.12,-0.36l0.74,-1.34l1.77,-0.39l1.54,-0.92l0.63,-2.31l1.87,-0.5l0.49,-0.81l0.94,0.56l2.13,0.11l2.55,0.92l1.95,-0.83l0.65,0.43l0.56,-0.13l0.69,-1.12l1.57,-0.08l0.72,-1.66l0.79,-0.74l0.8,0.39l-0.17,0.56l0.71,0.58l-0.08,2.39l1.11,0.95ZM601.37,188.71l1.73,-0.71l1.43,-1.18l4.03,0.35l-2.23,0.74l-4.95,0.8Z",
      name: "Afghanistan"
    },
    IQ: {
      path: "M530.82,187.47l0.79,0.66l1.26,-0.28l1.46,3.08l1.63,0.94l0.14,1.23l-1.22,1.05l-0.53,2.52l1.73,2.67l3.12,1.62l1.15,1.88l-0.38,1.85l0.39,0.48l0.41,-0.0l0.02,1.07l0.76,0.94l-2.47,-0.1l-1.71,2.44l-4.31,-0.2l-7.02,-5.48l-3.73,-1.94l-2.88,-0.73l-0.85,-2.87l5.45,-3.02l0.95,-3.43l-0.19,-1.96l1.27,-0.7l1.22,-1.7l0.87,-0.36l2.69,0.34Z",
      name: "Iraq"
    },
    IS: {
      path: "M384.14,88.06l-0.37,2.61l2.54,2.51l-2.9,2.75l-9.19,3.4l-9.25,-1.66l1.7,-1.22l-0.1,-0.7l-4.05,-1.47l2.96,-0.53l0.33,-0.43l-0.11,-1.2l-0.33,-0.36l-4.67,-0.85l1.28,-2.04l3.45,-0.56l3.77,2.72l0.44,0.02l3.64,-2.16l3.3,1.08l3.98,-2.16l3.58,0.26Z",
      name: "Iceland"
    },
    IR: {
      path: "M533.43,187.16l-1.27,-2.15l0.42,-0.98l-0.71,-3.04l1.03,-0.5l0.33,0.83l1.26,1.35l2.05,0.51l1.11,-0.16l2.89,-2.11l0.62,-0.14l0.39,0.46l-0.72,1.2l0.06,0.49l1.56,1.53l0.65,0.04l0.67,1.81l2.56,0.83l1.87,1.48l3.69,0.49l3.91,-0.76l0.47,-0.73l2.17,-0.6l1.66,-1.54l1.51,0.08l1.18,-0.53l1.59,0.24l2.83,1.48l1.88,0.3l2.77,2.47l1.77,0.18l0.18,1.99l-1.68,5.49l0.24,0.5l0.61,0.23l-0.82,1.48l0.8,2.18l0.19,1.71l0.3,0.34l1.63,0.4l0.15,1.32l-2.15,2.35l-0.01,0.53l2.21,3.03l2.34,1.24l0.06,2.14l1.24,0.72l0.11,0.69l-3.31,1.27l-1.08,3.03l-9.68,-1.68l-0.99,-3.05l-1.43,-0.73l-2.17,0.46l-2.47,1.26l-2.83,-0.82l-2.46,-2.02l-2.41,-0.8l-3.42,-6.06l-0.48,-0.2l-1.18,0.39l-1.44,-0.82l-0.5,0.08l-0.65,0.74l-0.97,-1.01l-0.02,-1.31l-0.71,-0.39l0.26,-1.81l-1.29,-2.11l-3.13,-1.63l-1.58,-2.43l0.5,-1.9l1.31,-1.26l-0.19,-1.66l-1.74,-1.1l-1.57,-3.3Z",
      name: "Iran"
    },
    AM: {
      path: "M536.99,182.33l-0.28,0.03l-1.23,-2.13l-0.93,0.01l-0.62,-0.66l-0.69,-0.07l-0.96,-0.81l-1.56,-0.62l0.19,-1.12l-0.26,-0.79l2.72,-0.36l1.09,1.01l-0.17,0.92l1.02,0.78l-0.47,0.62l0.08,0.56l2.04,1.23l0.04,1.4Z",
      name: "Armenia"
    },
    IT: {
      path: "M451.59,158.63l3.48,0.94l-0.21,1.17l0.3,0.83l-1.49,-0.24l-2.04,1.1l-0.21,0.39l0.13,1.45l-0.25,1.12l0.82,1.57l2.39,1.63l1.31,2.54l2.79,2.43l2.05,0.08l0.21,0.23l-0.39,0.33l0.09,0.67l4.05,1.97l2.17,1.76l-0.16,0.36l-1.17,-1.08l-2.18,-0.49l-0.44,0.2l-1.05,1.91l0.14,0.54l1.57,0.95l-0.19,0.98l-1.06,0.33l-1.25,2.34l-0.37,0.08l0.0,-0.33l1.0,-2.45l-1.73,-3.17l-1.12,-0.51l-0.88,-1.33l-1.51,-0.51l-1.27,-1.25l-1.75,-0.18l-4.12,-3.21l-1.62,-1.65l-1.03,-3.19l-3.53,-1.36l-1.3,0.51l-1.69,1.41l0.16,-0.72l-0.28,-0.47l-1.14,-0.33l-0.53,-1.96l0.72,-0.78l0.04,-0.48l-0.65,-1.17l0.8,0.39l1.4,-0.23l1.11,-0.84l0.52,0.35l1.19,-0.1l0.75,-1.2l1.53,0.33l1.36,-0.56l0.35,-1.14l1.08,0.32l0.68,-0.64l1.98,-0.44l0.42,0.82ZM459.19,184.75l-0.65,1.65l0.32,1.05l-0.31,0.89l-1.5,-0.85l-4.5,-1.67l0.19,-0.82l2.67,0.23l3.78,-0.48ZM443.93,176.05l1.18,1.66l-0.3,3.32l-1.06,-0.01l-0.77,0.73l-0.53,-0.44l-0.1,-3.37l-0.39,-1.22l1.04,0.01l0.92,-0.68Z",
      name: "Italy"
    },
    VN: {
      path: "M690.56,230.25l-2.7,1.82l-2.09,2.46l-0.63,1.95l4.31,6.45l2.32,1.65l1.43,1.94l1.11,4.59l-0.32,4.24l-1.93,1.54l-2.84,1.61l-2.11,2.15l-2.73,2.06l-0.59,-1.05l0.63,-1.53l-0.13,-0.47l-1.34,-1.04l1.51,-0.71l2.55,-0.18l0.3,-0.63l-0.82,-1.14l4.0,-2.07l0.31,-3.05l-0.57,-1.77l0.42,-2.66l-0.73,-1.97l-1.86,-1.76l-3.63,-5.29l-2.72,-1.46l0.36,-0.47l1.5,-0.64l0.21,-0.52l-0.97,-2.27l-0.37,-0.24l-2.83,-0.02l-2.24,-3.9l0.83,-0.4l4.39,-0.29l2.06,-1.31l1.15,0.89l1.88,0.4l-0.17,1.51l1.35,1.16l1.67,0.45Z",
      name: "Vietnam"
    },
    AR: {
      path: "M249.29,428.93l-2.33,-0.52l-5.83,-0.43l-0.89,-1.66l0.05,-2.37l-0.45,-0.4l-1.43,0.18l-0.67,-0.91l-0.2,-3.13l1.88,-1.47l0.79,-2.04l-0.25,-1.7l1.3,-2.68l0.91,-4.15l-0.22,-1.69l0.85,-0.45l0.2,-0.44l-0.27,-1.16l-0.98,-0.68l0.59,-0.92l-0.05,-0.5l-1.04,-1.07l-0.52,-3.1l0.97,-0.86l-0.42,-3.58l1.2,-5.43l1.38,-0.98l0.16,-0.43l-0.75,-2.79l-0.01,-2.43l1.78,-1.75l0.06,-2.57l1.43,-2.85l0.01,-2.58l-0.69,-0.74l-1.09,-4.52l1.47,-2.7l-0.18,-2.79l0.85,-2.35l1.59,-2.46l1.73,-1.64l0.05,-0.52l-0.6,-0.84l0.44,-0.85l-0.07,-4.19l2.7,-1.44l0.86,-2.75l-0.21,-0.71l1.76,-2.01l2.9,0.57l1.38,1.78l0.68,-0.08l0.87,-1.87l2.39,0.09l4.95,4.77l2.17,0.49l3.0,1.92l2.47,1.0l0.25,0.82l-2.37,3.93l0.23,0.59l5.39,1.16l2.12,-0.44l2.45,-2.16l0.5,-2.38l0.76,-0.31l0.98,1.2l-0.04,1.8l-3.67,2.51l-2.85,2.66l-3.43,3.88l-1.3,5.07l0.01,2.72l-0.54,0.73l-0.36,3.28l3.14,2.64l-0.16,2.11l1.4,1.11l-0.1,1.09l-2.29,3.52l-3.55,1.49l-4.92,0.6l-2.71,-0.29l-0.43,0.51l0.5,1.65l-0.49,2.1l0.38,1.42l-1.19,0.83l-2.36,0.38l-2.3,-1.04l-1.38,0.83l0.41,3.64l1.69,0.91l1.4,-0.71l0.36,0.76l-2.04,0.86l-2.01,1.89l-0.97,4.63l-2.34,0.1l-2.09,1.78l-0.61,2.75l2.46,2.31l2.17,0.63l-0.7,2.32l-2.83,1.73l-1.73,3.86l-2.17,1.22l-1.16,1.67l0.75,3.76l1.04,1.28ZM256.71,438.88l-2.0,0.15l-1.4,-1.22l-3.82,-0.1l-0.0,-5.83l1.6,3.05l3.26,2.07l3.08,0.78l-0.71,1.1Z",
      name: "Argentina"
    },
    AU: {
      path: "M705.8,353.26l0.26,0.04l0.17,-0.47l-0.48,-1.42l0.92,1.11l0.45,0.15l0.27,-0.39l-0.1,-1.56l-1.98,-3.63l1.09,-3.31l-0.24,-1.57l0.34,-0.62l0.38,1.06l0.43,-0.19l0.99,-1.7l1.91,-0.83l1.29,-1.15l1.81,-0.91l0.96,-0.17l0.92,0.26l1.92,-0.95l1.47,-0.28l1.03,-0.8l1.43,0.04l2.78,-0.84l1.36,-1.15l0.71,-1.45l1.41,-1.26l0.3,-2.58l1.27,-1.59l0.78,1.65l0.54,0.19l1.07,-0.51l0.15,-0.6l-0.73,-1.0l0.45,-0.71l0.78,0.39l0.58,-0.3l0.28,-1.82l1.87,-2.14l1.12,-0.39l0.28,-0.58l0.62,0.17l0.53,-0.73l1.87,-0.57l1.65,1.05l1.35,1.48l3.39,0.38l0.43,-0.54l-0.46,-1.23l1.05,-1.79l1.04,-0.61l0.14,-0.55l-0.25,-0.41l0.88,-1.17l1.31,-0.77l1.3,0.27l2.1,-0.48l0.31,-0.4l-0.05,-1.3l-0.92,-0.77l1.48,0.56l1.41,1.07l2.11,0.65l0.81,-0.2l1.4,0.7l1.69,-0.66l0.8,0.19l0.64,-0.33l0.71,0.77l-1.33,1.94l-0.71,0.07l-0.35,0.51l0.24,0.86l-1.52,2.35l0.12,1.05l2.15,1.65l1.97,0.85l3.04,2.36l1.97,0.65l0.55,0.88l2.72,0.85l1.84,-1.1l2.07,-5.97l-0.42,-3.59l0.3,-1.73l0.47,-0.87l-0.31,-0.68l1.09,-3.28l0.46,-0.47l0.4,0.71l0.16,1.51l0.65,0.52l0.16,1.04l0.85,1.21l0.12,2.38l0.9,2.0l0.57,0.18l1.3,-0.78l1.69,1.7l-0.2,1.08l0.53,2.2l0.39,1.3l0.68,0.48l0.6,1.95l-0.19,1.48l0.81,1.76l6.01,3.69l-0.11,0.76l1.38,1.58l0.95,2.77l0.58,0.22l0.72,-0.41l0.8,0.9l0.61,0.01l0.46,2.41l4.81,4.71l0.66,2.02l-0.07,3.31l1.14,2.2l-0.13,2.24l-1.1,3.68l0.03,1.64l-0.47,1.89l-1.05,2.4l-1.9,1.47l-1.72,3.51l-2.38,6.09l-0.24,2.82l-1.14,0.8l-2.85,0.15l-2.31,1.19l-2.51,2.25l-3.09,-1.57l0.3,-1.15l-0.54,-0.47l-1.5,0.63l-2.01,1.94l-7.12,-2.18l-1.48,-1.63l-1.14,-3.74l-1.45,-1.26l-1.81,-0.26l0.56,-1.18l-0.61,-2.1l-0.72,-0.1l-1.14,1.82l-0.9,0.21l0.63,-0.82l0.36,-1.55l0.92,-1.31l-0.13,-2.34l-0.7,-0.22l-2.0,2.34l-1.51,0.93l-0.94,2.01l-1.35,-0.81l-0.02,-1.52l-1.57,-2.04l-1.09,-0.88l0.24,-0.33l-0.14,-0.59l-3.21,-1.69l-1.83,-0.12l-2.54,-1.35l-4.58,0.28l-6.02,1.9l-2.53,-0.13l-2.62,1.41l-2.13,0.63l-1.49,2.6l-3.49,0.31l-2.29,-0.5l-3.48,0.43l-1.6,1.47l-0.81,-0.04l-2.37,1.63l-3.26,-0.1l-3.72,-2.21l0.04,-1.05l1.19,-0.46l0.49,-0.89l0.21,-2.97l-0.28,-1.64l-1.34,-2.86l-0.38,-1.47l0.05,-1.72l-0.95,-1.7l-0.18,-0.97l-1.01,-0.99l-0.29,-1.98l-1.13,-1.75ZM784.92,393.44l2.65,1.02l3.23,-0.96l1.09,0.14l0.15,3.06l-0.85,1.13l-0.17,1.63l-0.87,-0.24l-1.57,1.91l-1.68,-0.18l-1.4,-2.36l-0.37,-2.04l-1.39,-2.51l0.04,-0.8l1.15,0.18Z",
      name: "Australia"
    },
    IL: {
      path: "M507.76,203.05l0.4,-0.78l0.18,0.4l-0.33,1.03l0.52,0.44l0.68,-0.22l-0.86,3.6l-1.16,-3.32l0.59,-0.74l-0.03,-0.41ZM508.73,200.34l0.37,-1.02l0.64,0.0l0.52,-0.51l-0.49,1.53l-0.56,-0.24l-0.48,0.23Z",
      name: "Israel"
    },
    IN: {
      path: "M623.34,207.03l-1.24,1.04l-0.97,2.55l0.22,0.51l8.04,3.87l3.42,0.37l1.57,1.38l4.92,0.88l2.18,-0.04l0.38,-0.3l0.29,-1.24l-0.32,-1.64l0.14,-0.87l0.82,-0.31l0.45,2.48l2.28,1.02l1.77,-0.38l4.14,0.1l0.38,-0.36l0.18,-1.66l-0.5,-0.65l1.37,-0.29l2.25,-1.99l2.7,-1.62l1.93,0.62l1.8,-0.98l0.79,1.14l-0.68,0.91l0.26,0.63l2.42,0.36l0.09,0.47l-0.83,0.75l0.13,1.07l-1.52,-0.29l-3.24,1.86l-0.13,1.78l-1.32,2.14l-0.18,1.39l-0.93,1.82l-1.64,-0.5l-0.52,0.37l-0.09,2.63l-0.56,1.11l0.19,0.81l-0.53,0.27l-1.18,-3.73l-1.08,-0.27l-0.38,0.31l-0.24,1.0l-0.66,-0.66l0.54,-1.06l1.22,-0.34l1.15,-2.25l-0.24,-0.56l-1.57,-0.47l-4.34,-0.28l-0.18,-1.56l-0.35,-0.35l-1.11,-0.12l-1.91,-1.12l-0.56,0.17l-0.88,1.82l0.11,0.49l1.36,1.07l-1.09,0.69l-0.69,1.11l0.18,0.56l1.24,0.57l-0.32,1.54l0.85,1.94l0.36,2.01l-0.22,0.59l-4.58,0.52l-0.33,0.42l0.13,1.8l-1.17,1.36l-3.65,1.81l-2.79,3.03l-4.32,3.28l-0.18,1.27l-4.65,1.79l-0.77,2.16l0.64,5.3l-1.06,2.49l-0.01,3.94l-1.24,0.28l-1.14,1.93l0.39,0.84l-1.68,0.53l-1.04,1.83l-0.65,0.47l-2.06,-2.05l-2.1,-6.02l-2.2,-3.64l-1.05,-4.75l-2.29,-3.57l-1.76,-8.2l0.01,-3.11l-0.49,-2.53l-0.55,-0.29l-3.53,1.52l-1.53,-0.27l-2.86,-2.77l0.85,-0.67l0.08,-0.55l-0.74,-1.03l-2.67,-2.06l1.24,-1.32l5.34,0.01l0.39,-0.49l-0.5,-2.29l-1.42,-1.46l-0.27,-1.93l-1.43,-1.2l2.31,-2.37l3.05,0.06l2.62,-2.85l1.6,-2.81l2.4,-2.73l0.07,-2.04l1.97,-1.48l-0.02,-0.65l-1.93,-1.31l-0.82,-1.78l-0.8,-2.21l0.9,-0.89l3.59,0.65l2.92,-0.42l2.33,-2.19l2.31,2.85l-0.24,2.13l0.99,1.59l-0.05,0.82l-1.34,-0.28l-0.47,0.48l0.7,3.06l2.62,1.99l2.99,1.65Z",
      name: "India"
    },
    TZ: {
      path: "M495.56,296.42l2.8,-3.12l-0.02,-0.81l-0.64,-1.3l0.68,-0.52l0.14,-1.47l-0.76,-1.25l0.31,-0.11l2.26,0.03l-0.51,2.76l0.76,1.3l0.5,0.12l1.05,-0.53l1.19,-0.12l0.61,0.24l1.43,-0.62l0.1,-0.67l-0.71,-0.62l1.57,-1.7l8.65,4.86l0.32,1.53l3.34,2.33l-1.05,2.8l0.13,1.61l1.63,1.12l-0.6,1.76l-0.01,2.33l1.89,4.03l0.57,0.43l-1.46,1.08l-2.61,0.94l-1.43,-0.04l-1.06,0.77l-2.29,0.36l-2.87,-0.68l-0.83,0.07l-0.63,-0.75l-0.31,-2.78l-1.32,-1.35l-3.25,-0.77l-3.96,-1.58l-1.18,-2.41l-0.32,-1.75l-1.76,-1.49l0.42,-1.05l-0.44,-0.89l0.08,-0.96l-0.46,-0.58l0.06,-0.56Z",
      name: "Tanzania"
    },
    AZ: {
      path: "M539.29,175.73l1.33,0.32l1.94,-1.8l2.3,3.34l1.43,0.43l-1.26,0.15l-0.35,0.32l-0.8,3.14l-0.99,0.96l0.05,1.11l-1.26,-1.13l0.7,-1.18l-0.04,-0.47l-0.74,-0.86l-1.48,0.15l-2.34,1.71l-0.03,-1.27l-2.03,-1.35l0.47,-0.62l-0.08,-0.56l-1.03,-0.79l0.29,-0.43l-0.14,-0.58l-1.13,-0.86l1.89,0.68l1.69,0.06l0.37,-0.87l-0.81,-1.37l0.42,0.06l1.63,1.72ZM533.78,180.57l0.61,0.46l0.69,-0.0l0.59,1.15l-0.68,-0.15l-1.21,-1.45Z",
      name: "Azerbaijan"
    },
    IE: {
      path: "M405.08,135.42l0.35,2.06l-1.75,2.78l-4.22,1.88l-2.84,-0.4l1.73,-3.0l-1.18,-3.53l4.6,-3.74l0.32,1.15l-0.49,1.74l0.4,0.51l1.47,-0.04l1.6,0.6Z",
      name: "Ireland"
    },
    ID: {
      path: "M756.47,287.89l0.69,4.01l2.79,1.78l0.51,-0.1l2.04,-2.59l2.71,-1.43l2.05,-0.0l3.9,1.73l2.46,0.45l0.08,15.12l-1.75,-1.54l-2.54,-0.51l-0.88,0.71l-2.32,0.06l0.69,-1.33l1.45,-0.64l0.23,-0.46l-0.65,-2.74l-1.24,-2.21l-5.04,-2.29l-2.09,-0.23l-3.68,-2.27l-0.55,0.13l-0.65,1.07l-0.52,0.12l-0.55,-1.89l-1.21,-0.78l1.84,-0.62l1.72,0.05l0.39,-0.52l-0.21,-0.66l-0.38,-0.28l-3.45,-0.0l-1.13,-1.48l-2.1,-0.43l-0.52,-0.6l2.69,-0.48l1.28,-0.78l3.66,0.94l0.3,0.71ZM757.91,300.34l-0.62,0.82l-0.1,-0.8l0.59,-1.12l0.13,1.1ZM747.38,292.98l0.34,0.72l-1.22,-0.57l-4.68,-0.1l0.27,-0.62l2.78,-0.09l2.52,0.67ZM741.05,285.25l-0.67,-2.88l0.64,-2.01l0.41,0.86l1.21,0.18l0.16,0.7l-0.1,1.68l-0.84,-0.16l-0.46,0.3l-0.34,1.34ZM739.05,293.5l-0.5,0.44l-1.34,-0.36l-0.17,-0.37l1.73,-0.08l0.27,0.36ZM721.45,284.51l-0.19,1.97l2.24,2.23l0.54,0.02l1.27,-1.07l2.75,-0.5l-0.9,1.21l-2.11,0.93l-0.16,0.6l2.22,3.01l-0.3,1.07l1.36,1.74l-2.26,0.85l-0.28,-0.31l0.12,-1.19l-1.64,-1.34l0.17,-2.23l-0.56,-0.39l-1.67,0.76l-0.23,0.39l0.3,6.17l-1.1,0.25l-0.69,-0.47l0.64,-2.21l-0.39,-2.42l-0.39,-0.34l-0.8,-0.01l-0.58,-1.29l0.98,-1.6l0.35,-1.96l1.32,-3.87ZM728.59,296.27l0.38,0.49l-0.02,1.28l-0.88,0.49l-0.53,-0.47l1.04,-1.79ZM729.04,286.98l0.27,-0.05l-0.02,0.13l-0.24,-0.08ZM721.68,284.05l0.16,-0.32l1.89,-1.65l1.83,0.68l3.16,0.35l2.94,-0.1l2.39,-1.66l-1.73,2.13l-1.66,0.43l-2.41,-0.48l-4.17,0.13l-2.39,0.51ZM730.55,310.47l1.11,-1.93l2.03,-0.82l0.08,0.62l-1.45,1.67l-1.77,0.46ZM728.12,305.88l-0.1,0.38l-3.46,0.66l-2.91,-0.27l-0.0,-0.25l1.54,-0.41l1.66,0.73l1.67,-0.19l1.61,-0.65ZM722.9,310.24l-0.64,0.03l-2.26,-1.2l1.11,-0.24l1.78,1.41ZM716.26,305.77l0.88,0.51l1.28,-0.17l0.2,0.35l-4.65,0.73l0.39,-0.67l1.15,-0.02l0.75,-0.73ZM711.66,293.84l-0.38,-0.16l-2.54,1.01l-1.12,-1.44l-1.69,-0.13l-1.16,-0.75l-3.04,0.77l-1.1,-1.15l-3.31,-0.11l-0.35,-3.05l-1.35,-0.95l-1.11,-1.98l-0.33,-2.06l0.27,-2.14l0.9,-1.01l0.37,1.15l2.09,1.49l1.53,-0.48l1.82,0.08l1.38,-1.19l1.0,-0.18l2.28,0.67l2.26,-0.53l1.52,-3.64l1.01,-0.99l0.78,-2.57l4.1,0.3l-1.11,1.77l0.02,0.46l1.7,2.2l-0.23,1.39l2.07,1.71l-2.33,0.42l-0.88,1.9l0.1,2.05l-2.4,1.9l-0.06,2.45l-0.7,2.79ZM692.58,302.03l0.35,0.26l4.8,0.25l0.78,-0.97l4.17,1.09l1.13,1.68l3.69,0.45l2.13,1.04l-1.8,0.6l-2.77,-0.99l-4.8,-0.12l-5.24,-1.41l-1.84,-0.25l-1.11,0.3l-4.26,-0.97l-0.7,-1.14l-1.59,-0.13l1.18,-1.65l2.74,0.13l2.87,1.13l0.26,0.68ZM685.53,299.17l-2.22,0.04l-2.06,-2.03l-3.15,-2.01l-2.93,-3.51l-3.11,-5.33l-2.2,-2.12l-1.64,-4.06l-2.32,-1.69l-1.27,-2.07l-1.96,-1.5l-2.51,-2.65l-0.11,-0.66l4.81,0.53l2.15,2.38l3.31,2.74l2.35,2.66l2.7,0.17l1.95,1.59l1.54,2.17l1.59,0.95l-0.84,1.71l0.15,0.52l1.44,0.87l0.79,0.1l0.4,1.58l0.87,1.4l1.96,0.39l1.0,1.31l-0.6,3.01l-0.09,3.5Z",
      name: "Indonesia"
    },
    UA: {
      path: "M492.5,162.44l1.28,-2.49l1.82,0.19l0.66,-0.23l0.09,-0.71l-0.25,-0.75l-0.79,-0.72l-0.33,-1.21l-0.86,-0.62l-0.02,-1.19l-1.13,-0.86l-1.15,-0.19l-2.04,-1.0l-1.66,0.32l-0.66,0.47l-0.92,-0.0l-0.84,0.78l-2.48,0.7l-1.18,-0.71l-3.07,-0.36l-0.89,0.43l-0.24,-0.55l-1.11,-0.7l0.35,-0.93l1.26,-1.02l-0.54,-1.23l2.04,-2.43l1.4,-0.62l0.25,-1.19l-1.04,-2.39l0.83,-0.13l1.28,-0.84l1.8,-0.07l2.47,0.26l2.86,0.81l1.88,0.06l0.86,0.44l1.04,-0.41l0.77,0.66l2.18,-0.15l0.92,0.3l0.52,-0.34l0.15,-1.53l0.56,-0.54l2.85,-0.05l0.84,-0.72l3.04,-0.18l1.23,1.46l-0.48,0.77l0.21,1.03l0.36,0.32l1.8,0.14l0.93,2.08l3.18,1.15l1.94,-0.45l1.67,1.49l1.4,-0.03l3.35,0.96l0.02,0.54l-0.96,1.59l0.47,1.97l-0.26,0.7l-2.36,0.28l-1.29,0.89l-0.23,1.38l-1.83,0.27l-1.58,0.97l-2.41,0.21l-2.16,1.17l-0.21,0.38l0.34,2.26l1.23,0.75l2.13,-0.08l-0.14,0.31l-2.65,0.53l-3.23,1.69l-0.87,-0.39l0.42,-1.1l-0.25,-0.52l-2.21,-0.73l2.35,-1.06l0.12,-0.65l-0.93,-0.82l-3.62,-0.74l-0.13,-0.89l-0.46,-0.34l-2.61,0.59l-0.91,1.69l-1.71,2.04l-0.86,-0.4l-1.62,0.27Z",
      name: "Ukraine"
    },
    QA: {
      path: "M549.33,221.64l-0.76,-0.23l-0.14,-1.64l0.84,-1.29l0.47,0.52l0.04,1.34l-0.45,1.3Z",
      name: "Qatar"
    },
    MZ: {
      path: "M508.58,318.75l-0.34,-2.57l0.51,-2.05l3.55,0.63l2.5,-0.38l1.02,-0.76l1.49,0.01l2.74,-0.98l1.66,-1.2l0.5,9.24l0.41,1.23l-0.68,1.67l-0.93,1.71l-1.5,1.5l-5.16,2.28l-2.78,2.73l-1.02,0.53l-1.71,1.8l-0.98,0.57l-0.35,2.41l1.16,1.94l0.49,2.17l0.43,0.31l-0.06,2.06l-0.39,1.17l0.5,0.72l-0.25,0.73l-0.92,0.83l-5.12,2.39l-1.22,1.36l0.21,1.13l0.58,0.39l-0.11,0.72l-1.22,-0.01l-0.73,-2.97l0.42,-3.09l-1.78,-5.37l2.49,-2.81l0.69,-1.89l0.44,-0.43l0.28,-1.53l-0.39,-0.93l0.59,-3.65l-0.01,-3.26l-1.49,-1.16l-1.2,-0.22l-1.74,-1.17l-1.92,0.01l-0.29,-2.08l7.06,-1.96l1.28,1.09l0.89,-0.1l0.67,0.44l0.1,0.73l-0.51,1.29l0.19,1.81l1.75,1.83l0.65,-0.13l0.71,-1.65l1.17,-0.86l-0.26,-3.47l-1.05,-1.85l-1.04,-0.94Z",
      name: "Mozambique"
    }
  },
  height: 440.70631074413296,
  width: 900,
  projection: {
    type: "mill",
    centralMeridian: 11.5
  }
});

// world merc

jsVectorMap.prototype.addMap("world_merc", {
  "insets": [{
    "width": 900,
    "top": 0,
    "height": 583.0802520919394,
    "bbox": [{
      "y": -18449355.69035302,
      "x": -20004297.151525836
    }, {
      "y": 7485321.539093307,
      "x": 20026572.394749384
    }],
    "left": 0
  }],
  "paths": {
    "BD": {
      "path": "M651.84,359.63l-0.6,-2.05l-1.36,-1.76l-2.31,-0.11l-0.41,0.48l0.2,0.98l-0.54,1.03l-0.71,-0.37l-0.68,0.36l-1.19,-0.37l-0.37,-2.06l-0.81,-1.92l0.39,-1.52l-0.21,-0.46l-1.16,-0.55l0.3,-0.55l1.48,-0.98l0.03,-0.64l-1.56,-1.27l0.56,-1.2l1.6,0.97l1.04,0.16l0.18,1.62l0.33,0.35l5.65,0.65l-0.86,1.73l-1.21,0.35l-0.77,1.56l0.07,0.46l1.37,1.41l0.68,-0.19l0.42,-1.44l1.21,3.96l-0.03,1.26l-0.32,-0.15l-0.41,0.28Z",
      "name": "Bangladesh"
    },
    "BE": {
      "path": "M429.3,264.88l1.93,0.28l2.07,-0.74l1.41,1.55l1.25,0.86l-0.23,2.13l-0.68,0.42l-0.18,1.46l-1.63,-1.32l-1.4,0.17l-2.72,-3.22l-1.17,-0.21l-0.2,-0.77l1.57,-0.62Z",
      "name": "Belgium"
    },
    "BF": {
      "path": "M421.42,377.38l-0.11,0.96l0.34,1.18l1.4,1.73l0.07,1.11l0.32,0.37l2.56,0.52l-0.04,1.3l-0.38,0.54l-1.07,0.21l-0.73,1.19l-0.63,0.21l-3.22,-0.25l-0.94,0.39l-5.4,-0.05l-0.39,0.38l0.16,2.75l-1.23,-0.43l-1.17,0.1l-0.89,0.57l-2.27,-1.73l-0.13,-1.12l0.61,-0.96l0.01,-0.93l1.87,-2.0l0.44,-1.83l0.43,-0.39l1.28,0.26l1.05,-0.52l0.47,-0.73l1.84,-1.1l0.55,-0.84l2.2,-1.01l1.15,-0.31l0.72,0.46l1.13,-0.01Z",
      "name": "Burkina Faso"
    },
    "BG": {
      "path": "M491.72,293.09l-0.93,1.06l-0.91,2.45l0.52,1.52l-1.65,-0.27l-2.55,1.06l-0.27,1.69l-1.79,0.25l-2.03,-1.11l-1.92,0.88l-1.4,-0.07l-0.15,-1.87l-1.09,-1.09l0.34,-1.71l0.91,-1.02l0.01,-0.52l-1.15,-1.41l-0.06,-1.14l0.44,0.87l0.46,0.21l0.87,-0.23l1.91,0.53l3.68,0.18l1.44,-0.92l2.7,-0.74l1.67,1.16l0.95,0.26Z",
      "name": "Bulgaria"
    },
    "BA": {
      "path": "M463.49,287.91l2.09,0.57l1.72,-0.03l1.56,0.78l-0.4,0.99l1.14,1.61l-0.27,1.19l-1.82,1.31l-0.37,1.54l-1.65,-0.96l-0.89,-1.36l-2.11,-2.07l-1.65,-2.57l0.25,-0.7l0.45,0.41l0.59,-0.06l0.43,-0.59l0.92,-0.06Z",
      "name": "Bosnia and Herz."
    },
    "BN": {
      "path": "M707.48,403.47l0.69,-0.65l1.41,-0.91l-0.15,1.64l-0.81,-0.05l-0.61,0.58l-0.53,-0.6Z",
      "name": "Brunei"
    },
    "BO": {
      "path": "M263.83,471.11l-3.09,-0.24l-0.38,0.24l-0.7,1.56l-1.31,-1.57l-3.28,-0.66l-2.38,2.47l-1.3,0.27l-0.88,-3.36l-1.31,-2.93l0.74,-2.43l-0.12,-0.42l-1.2,-1.03l-0.37,-1.92l-1.09,-1.59l1.46,-2.61l-0.97,-2.36l0.48,-1.07l-0.35,-0.74l0.91,-1.33l0.16,-3.89l0.5,-1.18l-1.81,-3.45l2.46,0.08l0.8,-0.85l3.4,-1.92l2.66,-0.35l-0.19,1.39l0.3,1.07l-0.05,1.98l2.72,2.29l2.88,0.49l0.89,0.87l1.79,0.59l0.98,0.71l1.71,0.05l1.17,0.61l0.6,2.74l-0.7,0.54l0.96,3.03l0.37,0.28l4.3,0.1l-0.25,1.22l0.27,1.03l1.43,0.92l0.5,1.38l-0.41,1.9l-0.65,1.11l0.13,1.37l-2.69,-1.68l-2.4,-0.03l-4.36,0.77l-1.49,2.56l-0.1,1.55l-0.75,2.44Z",
      "name": "Bolivia"
    },
    "JP": {
      "path": "M781.1,291.58l1.81,0.77l1.63,-1.08l0.4,2.83l-3.6,1.02l-1.98,3.05l-3.61,-2.12l-0.58,0.21l-1.27,3.44l-2.14,0.04l-0.3,-2.88l1.09,-2.32l2.44,-0.17l0.37,-0.34l1.26,-6.78l2.45,3.07l2.03,1.27ZM773.56,314.42l-0.92,2.42l0.38,1.64l-1.15,1.91l-3.02,1.35l-4.59,0.3l-3.33,3.22l-1.25,-0.86l-0.09,-2.06l-0.46,-0.38l-4.35,0.67l-3.0,1.42l-2.84,0.06l-0.37,0.26l0.11,0.44l2.34,2.04l-1.55,4.67l-1.25,0.95l-0.8,-0.75l0.56,-2.43l-0.2,-0.44l-1.47,-0.8l-0.77,-1.54l2.14,-0.91l1.27,-1.83l2.45,-1.53l1.83,-2.06l4.77,-0.88l2.6,0.61l0.45,-0.22l2.39,-5.05l1.27,1.14l0.53,0.01l5.1,-4.39l1.68,-4.08l-0.39,-3.75l0.92,-1.82l2.11,-0.49l1.24,4.16l-0.07,2.45l-2.25,3.13l-0.03,3.43ZM757.77,324.02l0.2,0.64l-1.01,1.31l-1.17,-0.72l-1.28,0.7l-0.69,1.54l-1.01,-0.53l0.01,-1.04l1.14,-1.49l1.58,0.15l0.85,-1.05l1.38,0.49Z",
      "name": "Japan"
    },
    "BI": {
      "path": "M495.45,425.39l-1.08,-2.99l1.14,-0.11l0.64,-1.19l0.76,0.09l0.65,1.83l-2.1,2.37Z",
      "name": "Burundi"
    },
    "BJ": {
      "path": "M429.57,385.57l-0.05,0.81l0.5,1.35l-0.42,0.87l0.17,0.79l-1.82,2.14l-0.57,1.77l-0.08,5.44l-1.41,0.2l-0.48,-1.36l0.11,-5.73l-0.52,-0.7l-0.2,-1.35l-1.48,-1.49l0.22,-0.91l0.89,-0.43l0.42,-0.93l1.27,-0.36l1.22,-1.35l0.61,-0.0l1.62,1.25Z",
      "name": "Benin"
    },
    "BT": {
      "path": "M650.32,342.67l0.85,0.75l-0.12,1.18l-3.76,-0.12l-1.57,0.41l-1.93,-0.91l1.49,-2.09l1.12,-0.6l1.62,0.6l1.33,0.09l0.98,0.68Z",
      "name": "Bhutan"
    },
    "JM": {
      "path": "M228.38,368.9l-0.8,0.41l-2.27,-1.09l0.84,-0.25l2.14,0.31l1.18,0.59l-1.09,0.03Z",
      "name": "Jamaica"
    },
    "BW": {
      "path": "M483.92,460.24l2.27,4.08l2.83,2.92l0.96,0.32l0.77,2.5l2.13,0.63l1.04,0.8l-3.01,1.7l-2.32,2.09l-1.54,2.79l-1.52,0.46l-0.64,2.01l-1.34,0.54l-1.84,-0.12l-1.21,-0.77l-1.36,-0.31l-1.22,0.64l-0.75,1.42l-2.31,1.98l-1.39,0.22l-0.36,-0.63l0.16,-1.82l-1.48,-2.63l-0.62,-0.44l-0.0,-7.35l2.08,-0.08l0.38,-0.4l0.07,-9.12l1.56,-0.08l3.63,-0.87l0.8,0.91l0.52,0.07l1.5,-0.97l2.2,-0.5Z",
      "name": "Botswana"
    },
    "BR": {
      "path": "M259.98,404.95l3.24,0.7l0.65,-0.53l4.55,-1.32l1.08,-1.06l-0.02,-0.64l0.55,-0.05l0.28,0.28l-0.26,0.87l0.22,0.48l0.73,0.32l0.4,0.81l-0.62,0.86l-0.4,2.13l0.82,2.56l1.69,1.43l1.43,0.2l3.17,-1.68l3.18,0.3l0.65,-0.75l-0.27,-0.92l1.9,-0.09l2.39,0.99l1.06,-0.61l0.84,0.78l1.2,-0.18l1.18,-1.06l0.84,-1.94l1.36,-2.11l0.37,-0.05l1.89,5.46l1.33,0.59l0.05,1.28l-1.77,1.94l0.02,0.56l1.02,0.87l4.07,0.36l0.08,2.16l0.66,0.29l1.74,-1.5l6.97,2.32l1.02,1.22l-0.35,1.18l0.49,0.5l2.81,-0.74l4.77,1.3l3.75,-0.08l3.57,2.0l3.29,2.86l1.93,0.73l2.12,0.12l0.71,0.62l1.21,4.52l-0.95,4.0l-4.72,5.09l-1.64,2.95l-1.72,2.07l-0.8,0.3l-0.72,2.05l0.18,4.81l-0.94,5.62l-0.81,1.15l-0.43,3.44l-2.55,3.58l-0.4,2.59l-1.86,1.08l-0.67,1.57l-2.54,0.01l-3.94,1.05l-1.83,1.24l-2.87,0.85l-3.03,2.27l-2.2,2.92l-0.36,2.08l0.4,1.64l-0.45,2.73l-0.52,1.26l-1.77,1.62l-2.75,5.05l-3.83,3.63l-1.23,2.92l-1.18,1.22l-0.37,-0.92l0.96,-1.23l0.01,-0.48l-1.52,-2.09l-4.56,-3.52l-1.03,-0.01l-2.38,-2.13l-0.85,0.0l5.38,-5.77l3.77,-2.69l0.21,-2.55l-1.34,-1.86l-0.92,0.07l0.59,-2.44l0.01,-1.59l-1.11,-0.85l-1.75,0.31l-0.44,-3.22l-0.52,-0.97l-1.88,-0.9l-1.24,0.48l-2.17,-0.43l0.15,-3.31l-0.63,-1.37l0.67,-0.74l-0.22,-1.37l0.66,-1.16l0.44,-2.08l-0.61,-1.86l-1.4,-0.87l-0.2,-0.77l0.34,-1.41l-0.38,-0.49l-4.52,-0.1l-0.72,-2.27l0.59,-0.42l-0.03,-1.12l-0.5,-0.87l-0.32,-1.71l-1.45,-0.76l-1.63,-0.02l-1.05,-0.73l-1.6,-0.48l-1.13,-1.0l-2.69,-0.41l-2.47,-2.08l0.13,-4.38l-0.45,-0.45l-3.46,0.5l-3.44,1.95l-0.6,0.74l-2.89,-0.17l-1.47,0.42l-0.72,-0.18l0.15,-3.54l-0.64,-0.34l-1.94,1.42l-1.87,-0.06l-0.83,-1.19l-1.38,-0.27l0.21,-1.01l-1.35,-1.5l-0.88,-1.92l0.56,-0.6l-0.0,-0.81l1.29,-0.62l0.22,-0.43l-0.22,-1.19l0.61,-0.91l0.15,-0.99l2.65,-1.58l1.99,-0.47l0.42,-0.36l2.06,0.11l0.42,-0.33l1.19,-8.0l-0.41,-1.56l-1.1,-1.0l0.01,-1.33l1.91,-0.42l0.08,-0.96l-0.33,-0.43l-1.14,-0.2l-0.02,-0.83l4.47,0.05l0.82,-0.67l0.82,1.81l0.8,0.07l1.15,1.1l2.26,-0.05l0.71,-0.83l2.78,-0.96l0.48,-1.13l1.6,-0.64l0.24,-0.47l-0.48,-0.83l-1.83,-0.19l-0.36,-3.22Z",
      "name": "Brazil"
    },
    "BS": {
      "path": "M227.69,345.88l0.0,-0.01l0.0,0.0l-0.0,0.01ZM226.4,353.1l-0.48,-1.18l-0.85,-0.78l0.36,-1.17l0.95,2.03l0.01,1.1ZM225.65,345.38l-1.96,0.32l-0.04,-0.26l0.74,-0.14l1.26,0.08Z",
      "name": "Bahamas"
    },
    "BY": {
      "path": "M493.82,245.43l0.3,0.93l0.53,0.25l1.16,-0.47l2.08,0.9l0.2,1.73l-0.48,1.43l1.57,2.82l0.93,0.75l0.13,0.97l1.58,0.7l0.48,0.74l-0.6,0.57l-1.85,-0.13l-0.76,0.48l-0.12,0.47l1.08,3.5l-1.96,0.33l-0.87,1.12l-0.12,1.49l-0.67,-0.22l-2.03,0.17l-0.52,-0.75l-0.57,-0.09l-0.72,0.54l-0.9,-0.5l-1.91,-0.08l-2.74,-0.95l-2.61,-0.34l-2.01,0.09l-1.52,1.11l-0.65,0.08l-0.07,-1.5l-0.64,-1.57l1.4,-1.01l0.01,-1.65l-0.7,-1.69l-0.08,-1.37l2.2,-0.03l2.72,-1.61l0.73,-2.54l2.1,-1.69l-0.2,-1.69l3.82,-2.26l2.27,0.97Z",
      "name": "Belarus"
    },
    "BZ": {
      "path": "M198.03,374.09l0.1,-4.57l0.69,-0.06l0.74,-1.32l0.34,0.28l-0.4,1.33l0.17,0.59l-0.34,2.3l-1.3,1.44Z",
      "name": "Belize"
    },
    "RU": {
      "path": "M491.5,228.55l2.65,-2.55l-0.01,-0.58l-2.35,-2.15l7.46,-9.43l1.0,-2.89l-0.09,-0.41l-3.55,-3.64l0.93,-3.78l-2.18,-4.19l1.62,-5.27l-2.85,-6.95l2.24,-4.74l-0.06,-0.43l-3.73,-4.33l0.33,-4.4l1.87,-0.61l4.26,-2.85l2.35,-2.28l3.83,4.05l6.96,1.77l9.34,7.63l1.83,2.99l0.16,4.03l-2.62,3.11l-3.84,1.55l-11.03,-4.69l-2.16,0.81l-0.14,0.63l3.99,4.45l0.31,8.71l5.34,3.55l0.64,-0.27l0.32,-2.78l-1.43,-2.53l1.23,-1.72l5.74,3.47l0.43,-0.01l2.11,-1.42l0.15,-0.48l-1.59,-4.12l5.51,-5.69l1.99,0.31l2.25,2.09l0.65,-0.16l1.46,-4.3l-2.03,-4.0l1.18,-3.78l-1.5,-3.67l5.98,1.86l1.2,3.14l-2.74,0.7l-0.3,0.39l0.02,3.61l2.07,2.45l0.43,0.11l3.87,-1.38l0.85,-4.25l13.69,-8.82l1.16,0.21l-2.17,3.65l0.26,0.59l3.11,0.7l0.4,-0.14l1.68,-2.16l4.51,-0.18l3.61,-2.68l2.61,3.78l0.67,-0.02l2.85,-4.55l-0.0,-0.43l-2.5,-3.89l1.03,-1.89l7.03,2.08l3.39,2.18l9.05,7.85l0.62,-0.13l1.64,-3.95l-2.48,-3.58l-0.07,-1.39l-0.31,-0.37l-2.62,-0.61l0.73,-3.21l-1.33,-5.76l-0.07,-2.28l4.55,-7.04l1.67,-7.53l1.59,-1.44l6.17,2.09l0.48,4.29l-2.34,6.42l1.55,2.76l0.79,5.18l-0.57,9.85l2.73,4.33l-1.02,4.26l-4.88,9.07l0.23,0.57l2.86,0.92l0.49,-0.22l0.94,-2.13l2.83,-1.82l0.65,-3.1l2.12,-3.05l-1.37,-4.06l1.14,-4.42l-0.31,-0.49l-2.47,-0.52l-0.55,-3.59l1.95,-7.61l-3.13,-6.05l4.31,-5.2l-0.45,-5.83l0.53,-0.08l1.2,4.22l-0.98,7.66l0.21,0.4l2.68,1.42l0.58,-0.43l-1.09,-5.45l3.9,-2.98l4.9,-0.41l4.5,4.5l0.49,0.06l0.17,-0.47l-2.21,-6.76l-0.24,-8.85l4.01,-1.66l5.93,0.39l5.54,-1.19l0.28,-0.55l-1.97,-4.64l2.73,-5.9l2.89,-0.36l4.78,-4.84l6.49,-1.33l1.07,-2.85l6.11,-0.9l1.91,2.17l0.58,0.02l5.5,-5.45l4.43,0.17l0.41,-0.34l0.68,-4.62l2.32,-4.63l5.58,-4.48l3.69,3.23l-3.04,2.5l0.14,0.69l5.42,1.64l0.64,5.13l0.7,0.21l2.17,-2.49l6.98,0.14l5.48,5.07l1.92,3.72l-0.59,4.98l-2.66,2.78l-6.56,5.27l-1.96,2.84l0.18,0.6l3.08,1.27l3.68,2.26l0.45,-0.02l1.76,-1.33l1.14,5.11l0.34,0.31l0.41,-0.22l1.03,-2.14l3.75,-1.32l7.65,1.4l0.57,3.81l0.35,0.34l10.47,1.28l0.45,-0.39l0.13,-6.16l4.81,1.41l3.93,-0.03l3.85,4.37l1.1,5.17l-1.42,3.65l3.15,6.24l4.05,3.25l0.63,-0.2l2.24,-7.6l3.55,3.15l0.44,0.06l4.09,-2.03l4.67,2.34l0.49,-0.1l1.68,-2.01l3.85,1.04l0.49,-0.48l-1.76,-7.3l3.0,-3.3l22.19,5.31l2.15,4.74l6.55,5.95l10.36,-1.34l4.76,1.21l1.93,2.89l-0.3,5.24l3.26,2.4l3.66,-1.4l4.3,-0.18l4.84,1.4l4.5,-0.75l4.22,6.04l0.56,0.1l3.1,-2.22l0.13,-0.49l-1.96,-4.39l0.94,-2.74l7.63,1.95l5.23,-0.41l7.05,3.36l9.59,8.27l6.43,6.42l-0.21,3.79l1.82,1.88l0.45,0.06l0.21,-0.41l-0.52,-4.08l6.13,0.86l4.58,5.48l-2.15,2.3l-3.97,0.6l-0.34,0.39l-0.06,5.64l-0.78,0.94l-1.98,-0.15l-1.91,-1.99l-3.16,-1.63l-0.77,-2.69l-2.54,-0.99l-2.81,0.69l-1.11,-1.73l0.5,-2.12l-0.56,-0.45l-3.0,1.46l-0.2,0.51l1.06,2.68l-1.31,2.33l-3.03,2.42l-3.08,-0.41l-0.37,0.63l2.22,3.03l1.47,4.59l1.16,1.53l0.26,2.04l-0.46,1.02l-4.64,-1.05l-6.95,4.01l-2.18,0.6l-7.62,6.88l-0.81,1.88l-3.15,-3.07l-0.49,-0.06l-6.18,3.75l-0.93,-1.52l-0.61,-0.09l-2.26,2.01l-3.15,-0.64l-0.47,0.3l-0.79,3.18l-3.03,4.85l0.09,1.91l0.26,0.36l2.58,0.95l-0.3,6.03l-1.97,0.14l-0.36,0.29l-1.07,3.72l0.87,1.82l-4.01,2.02l-1.04,4.88l-3.49,0.95l-0.29,0.32l-0.73,4.06l-3.07,3.18l-0.71,-2.11l-2.45,-15.41l1.17,-6.06l2.06,-2.67l0.2,-2.12l3.83,-1.13l4.47,-6.06l4.28,-5.09l4.48,-4.07l2.13,-7.67l-0.45,-0.5l-3.36,0.72l-1.47,4.3l-5.81,5.21l-1.86,-5.8l-0.49,-0.26l-6.68,1.94l-6.27,8.55l-0.01,0.46l1.74,2.54l-8.37,1.57l0.16,-3.05l-0.32,-0.41l-3.89,-0.75l-3.3,2.39l-7.61,-0.82l-8.47,1.58l-17.7,19.78l0.24,0.67l3.73,0.52l1.14,2.49l2.65,1.15l0.46,-0.13l1.47,-1.95l2.35,0.24l3.43,4.41l0.08,3.28l-1.96,4.11l-0.21,4.69l-1.11,6.02l-3.72,5.32l-0.87,2.56l-8.3,10.17l-3.18,1.92l-1.29,0.04l-1.45,-1.54l-0.53,-0.05l-2.48,1.84l0.28,-0.27l0.36,-4.08l-0.6,-2.85l1.77,-1.03l2.89,0.6l0.44,-0.22l1.71,-3.57l0.84,-3.92l0.97,-1.37l1.32,-3.37l-0.48,-0.53l-4.14,1.11l-2.19,1.46l-3.38,-0.0l-1.05,-3.43l-2.97,-2.72l-4.29,-1.26l-1.76,-6.1l-2.63,-6.06l-2.3,-1.58l-3.75,-1.25l-3.46,0.09l-3.19,0.77l-2.26,2.18l0.05,0.61l1.21,0.86l0.03,1.88l-1.34,1.28l-2.26,4.23l-0.03,1.71l-3.16,2.2l-2.8,-1.36l-3.02,0.27l-1.18,-1.17l-1.68,-0.52l-3.94,2.75l-3.21,0.62l-2.27,0.93l-3.04,-0.6l-2.21,0.03l-1.47,-1.89l-2.61,-1.95l-2.65,-0.52l-5.44,1.21l-3.23,-1.49l-0.71,-3.08l-5.2,-1.5l-2.75,-1.64l-0.54,0.13l-2.59,4.17l0.89,2.46l-2.1,2.34l-3.38,-0.91l-2.42,-0.14l-1.85,-1.84l-2.51,-0.06l-2.46,-1.17l-3.86,1.89l-4.72,3.31l-3.26,0.87l-1.17,-2.07l-0.41,-0.2l-2.97,0.48l-1.1,-1.58l-1.62,-0.7l-1.31,-2.32l-1.38,-0.72l-3.71,0.94l-3.3,-2.2l-0.56,0.12l-0.97,1.52l-5.27,-9.77l-3.03,-3.13l0.73,-1.08l-0.04,-0.5l-0.5,-0.06l-6.2,3.97l-1.82,0.18l0.16,-1.83l-0.23,-0.4l-3.22,-1.46l-2.47,0.85l-0.7,-4.0l-0.31,-0.32l-4.5,-0.95l-2.52,1.84l-6.18,1.58l-1.3,1.08l-9.51,1.62l-1.15,1.45l-0.03,0.46l1.56,2.48l-1.98,0.89l-0.21,0.52l0.35,0.85l-2.18,1.8l0.03,0.64l3.81,2.6l-0.44,1.31l-3.21,-0.16l-0.87,1.02l-3.08,-1.9l-3.97,0.08l-2.66,1.61l-8.29,-4.28l-4.1,0.06l-5.42,4.44l-0.37,2.36l-2.0,-1.76l-0.63,0.13l-2.0,4.27l0.61,1.02l-1.32,2.63l0.05,0.44l2.13,2.54l1.95,0.05l1.39,2.15l-0.23,1.74l1.12,0.83l-0.86,1.61l-2.49,0.71l-2.49,3.66l0.0,0.45l2.19,3.19l-0.16,2.44l2.54,3.7l-1.62,1.81l-0.67,-0.14l-1.63,-1.93l-2.29,-0.94l-0.94,-1.47l-2.34,-0.71l-1.48,0.44l-0.42,-0.51l-3.52,-1.68l-5.76,-1.14l-0.47,0.2l-2.87,-2.64l-2.9,-1.36l-1.63,-1.56l1.39,-0.52l2.08,-3.01l-0.04,-0.51l-0.98,-1.01l3.14,-1.27l0.25,-0.4l-0.07,-0.8l-0.5,-0.35l-1.72,0.45l0.04,-0.92l1.06,-0.85l2.31,-0.26l0.34,-0.28l0.4,-1.47l-0.51,-1.94l0.95,-1.86l0.01,-1.32l-0.27,-0.37l-3.69,-1.26l-1.41,0.02l-1.42,-1.68l-0.43,-0.12l-1.78,0.57l-2.78,-1.21l-0.01,-0.71l-0.89,-1.73l-2.01,-0.38l-0.13,-0.77l0.53,-1.15l-1.6,-2.31l-3.58,0.03l-0.92,0.88l-0.42,-0.07l-1.05,-3.54l2.29,-0.07l0.97,-0.92l0.06,-0.51l-0.9,-1.27l-1.4,-0.62l-0.06,-0.85l-0.95,-0.73l-1.43,-2.57l0.49,-1.21l-0.25,-2.07l-2.69,-1.38l-1.22,0.37l-0.45,-0.94l-2.46,-1.05l-0.74,-2.46l-0.21,-2.19l-1.07,-1.09l0.93,-1.49l-0.72,-4.29l1.7,-2.67l-0.24,-0.98ZM749.34,295.94l-0.76,0.56l-0.11,0.15l-0.01,-0.65l0.87,-0.06ZM871.96,154.57l2.04,-0.2l3.29,2.04l-0.13,0.64l-2.37,1.7l-5.54,0.79l-0.34,-1.85l3.05,-3.11ZM797.75,123.25l-2.42,3.18l-3.66,-0.78l-4.39,-3.6l0.47,-2.52l10.01,3.72ZM783.79,118.53l-1.81,6.68l-8.92,-0.26l-4.06,2.13l-4.64,-5.86l1.28,-6.57l3.04,-1.79l6.39,0.44l8.71,5.22ZM778.23,253.99l-0.64,-1.28l0.31,-0.17l0.33,1.45ZM778.36,254.55l0.92,4.28l-0.05,4.08l1.05,4.08l2.23,6.09l-2.91,-0.99l-0.51,0.27l-1.54,5.47l2.42,4.01l-0.04,1.39l-1.22,-1.41l-0.65,0.06l-1.07,1.83l-0.29,-1.88l0.28,-3.61l-0.28,-4.01l0.58,-2.92l0.11,-5.24l-1.46,-4.02l0.21,-5.38l2.23,-2.09ZM780.09,139.86l-3.31,0.05l-5.09,-1.07l2.11,-3.11l2.77,-0.74l3.29,3.15l0.23,1.71ZM683.7,87.54l-13.17,4.38l4.34,-15.76l1.75,-1.29l1.59,0.74l6.17,7.25l-0.68,4.69ZM670.82,80.26l-5.03,1.48l-6.76,-3.64l-4.04,-4.98l-1.9,-10.03l-3.29,-2.93l6.28,-10.21l5.0,-3.39l4.63,7.67l5.72,14.22l-0.6,11.8ZM564.4,160.28l-0.92,0.41l-7.78,-0.94l-0.83,-3.41l-4.32,-2.0l-0.33,-3.85l2.54,-1.96l-0.08,-4.42l4.9,-7.29l-0.16,-0.58l-1.86,-0.88l5.7,-7.68l-0.57,-4.44l5.43,-5.07l8.18,-6.55l8.25,-1.96l4.4,-4.05l4.43,-1.3l1.54,3.81l-1.55,3.04l-16.43,9.84l-7.93,9.27l-7.69,17.13l0.59,6.93l4.49,5.95ZM548.68,56.87l-5.47,3.05l-0.54,2.57l-2.49,2.05l-2.33,-2.98l1.37,-4.49l-0.35,-0.52l-4.3,-0.36l3.7,-2.13l3.34,-0.17l0.47,3.78l0.35,0.35l0.42,-0.25l1.41,-3.62l2.04,-2.24l3.21,2.97l-0.81,1.96ZM477.39,251.71l-4.1,0.06l-2.6,-0.41l0.38,-1.28l3.15,-1.29l3.25,1.22l-0.09,1.7Z",
      "name": "Russia"
    },
    "RW": {
      "path": "M497.0,418.15l0.71,1.01l-0.11,1.09l-1.63,0.03l-1.04,1.39l-0.83,-0.11l0.51,-1.2l0.08,-1.34l0.42,-0.41l0.7,0.14l1.19,-0.61Z",
      "name": "Rwanda"
    },
    "RS": {
      "path": "M469.33,288.43l0.49,-1.17l-1.2,-1.97l1.47,-0.73l1.3,0.13l1.18,1.23l0.45,1.29l1.35,0.74l0.34,1.53l1.46,1.02l0.76,-0.3l0.25,0.82l-0.51,0.87l0.22,1.27l1.08,1.41l-0.8,0.94l-0.38,1.72l-1.22,0.09l0.27,-0.81l-2.46,-2.38l-0.93,0.06l-0.47,1.05l-2.15,-1.58l0.57,-1.85l-1.13,-1.51l0.53,-1.32l-0.49,-0.55Z",
      "name": "Serbia"
    },
    "TL": {
      "path": "M734.55,437.87l-0.09,-0.98l4.5,-0.86l-2.82,1.28l-1.59,0.55Z",
      "name": "Timor-Leste"
    },
    "TM": {
      "path": "M553.03,299.38l-0.05,0.44l-0.1,-0.29l0.15,-0.15ZM555.85,298.15l0.46,-0.11l1.47,0.82l2.08,2.72l4.07,-0.21l0.38,-0.49l-0.34,-1.39l1.95,-1.07l1.9,-1.78l2.93,1.56l0.41,2.75l1.21,0.76l2.57,-0.15l0.62,0.45l1.32,3.46l4.54,3.8l2.67,1.6l3.07,1.26l-0.04,1.22l-1.32,-0.81l-0.61,0.19l-0.32,0.93l-2.19,0.86l-0.47,2.34l-1.21,0.81l-1.91,0.45l-0.73,1.44l-1.54,0.33l-2.22,-1.01l-0.2,-2.37l-0.37,-0.37l-1.72,-0.1l-2.76,-2.67l-2.14,-0.44l-2.84,-1.62l-1.78,-0.29l-1.25,0.58l-1.56,-0.09l-2.01,1.85l-1.69,0.47l-0.37,-1.75l0.36,-3.28l-0.2,-0.39l-1.68,-0.94l0.55,-1.92l-0.34,-0.51l-1.23,-0.14l0.38,-1.9l2.23,0.64l2.2,-1.06l0.12,-0.63l-1.77,-1.94l-0.69,-1.85Z",
      "name": "Turkmenistan"
    },
    "TJ": {
      "path": "M597.8,305.02l-0.08,0.09l-2.5,-0.5l-0.48,0.34l-0.24,1.88l0.43,0.45l2.63,-0.24l3.18,1.04l4.38,-0.45l0.56,2.63l0.54,0.29l0.66,-0.26l1.12,0.54l0.21,2.4l-3.76,-0.23l-1.81,1.45l-1.74,0.8l-0.62,-0.64l0.22,-2.47l-0.65,-0.49l-0.04,-1.02l-1.36,-0.73l-0.48,0.07l-1.08,1.11l-0.54,1.62l-1.3,-0.06l-0.96,1.26l-0.91,-0.37l-1.63,0.91l-0.24,-0.12l1.28,-3.1l-0.54,-2.38l-1.69,-0.89l0.36,-0.8l2.18,-0.05l1.19,-1.8l0.76,-1.99l2.44,-0.56l-0.28,1.13l0.36,0.91l0.43,0.25Z",
      "name": "Tajikistan"
    },
    "RO": {
      "path": "M487.52,276.99l0.59,0.28l2.89,4.68l-0.18,3.12l0.45,1.64l1.3,0.9l1.37,-0.47l0.76,0.41l0.03,0.46l-0.83,0.52l-0.57,-0.25l-0.55,0.3l-0.63,3.8l-0.98,-0.24l-2.1,-1.28l-2.95,0.81l-1.25,0.86l-3.49,-0.17l-1.88,-0.53l-0.87,0.17l-0.86,-1.54l0.34,-0.35l-0.05,-0.61l-0.62,-0.44l-0.51,0.04l-0.55,0.55l-1.04,-0.73l-0.17,-1.29l-1.58,-1.05l-0.34,-1.15l-0.92,-0.96l1.63,-0.65l2.66,-4.89l2.39,-1.44l2.93,0.39l1.06,0.83l0.47,0.02l0.79,-0.53l1.77,-0.34l0.76,-0.87l0.76,0.0Z",
      "name": "Romania"
    },
    "GW": {
      "path": "M386.23,383.41l-0.29,0.84l0.15,0.61l-2.21,0.6l-0.86,0.96l-1.04,-0.83l-1.09,-0.23l-0.54,-1.07l-0.66,-0.5l2.41,-0.49l4.13,0.1Z",
      "name": "Guinea-Bissau"
    },
    "GT": {
      "path": "M195.08,379.54l-2.48,-0.37l-1.03,-0.46l-1.14,-0.9l0.3,-1.01l-0.24,-0.68l0.96,-1.69l2.98,-0.01l0.4,-0.37l-0.19,-1.29l-1.68,-1.44l0.53,-0.4l0.0,-1.08l3.85,0.02l-0.21,4.61l0.4,0.43l1.48,0.38l-1.5,1.01l-0.34,0.71l0.12,0.57l-2.2,1.98Z",
      "name": "Guatemala"
    },
    "GR": {
      "path": "M487.09,300.31l-0.62,1.67l-0.37,0.23l-2.84,-0.38l-3.03,0.86l-0.18,0.66l1.34,1.43l-0.67,0.28l-1.12,0.0l-1.2,-1.54l-0.65,0.03l-0.52,1.05l0.56,1.95l1.06,1.34l-0.61,0.46l-0.05,0.59l2.53,2.34l0.02,1.02l-1.77,-0.64l-0.5,0.54l0.53,1.16l-1.1,0.23l-0.3,0.52l0.77,2.24l-0.99,0.02l-1.84,-1.22l-1.37,-4.59l-2.21,-3.25l-0.12,-0.67l1.06,-1.44l0.2,-1.06l0.84,-0.7l0.03,-0.55l1.33,-0.24l1.01,-0.71l1.21,0.06l0.67,-0.62l2.26,-0.01l1.8,-0.83l1.85,1.11l2.28,-0.31l0.35,-0.39l0.01,-0.9l0.35,0.26ZM480.49,319.61l0.67,0.51l-0.8,-0.16l0.13,-0.35ZM482.3,320.35l2.74,0.05l0.29,0.4l-2.04,0.15l-0.32,-0.47l-0.67,-0.13Z",
      "name": "Greece"
    },
    "GQ": {
      "path": "M448.79,409.52l0.02,2.22l-4.09,0.0l0.69,-2.27l3.38,0.05Z",
      "name": "Eq. Guinea"
    },
    "GY": {
      "path": "M277.42,399.96l-0.32,1.83l-1.32,0.57l-0.23,0.46l-0.28,2.01l1.11,1.82l0.83,0.19l0.32,1.25l1.13,1.62l-1.21,-0.19l-1.08,0.71l-1.77,0.5l-0.44,0.46l-0.86,-0.09l-1.32,-1.01l-0.77,-2.27l0.36,-1.91l0.68,-1.23l-0.57,-1.17l-0.74,-0.43l0.12,-1.16l-0.9,-0.69l-1.1,0.09l-1.31,-1.48l0.53,-0.72l-0.04,-0.84l1.99,-0.86l0.05,-0.59l-0.71,-0.78l0.14,-0.57l1.66,-1.24l1.36,0.77l1.41,1.5l0.06,1.15l0.37,0.38l0.8,0.05l2.06,1.87Z",
      "name": "Guyana"
    },
    "GE": {
      "path": "M521.61,293.9l5.38,1.03l3.26,1.57l0.84,0.7l1.39,-0.49l2.05,0.63l0.69,1.25l1.15,0.65l-0.2,0.63l1.05,1.54l-1.06,-0.15l-1.81,-0.93l-0.97,0.52l-3.21,0.48l-2.28,-1.55l-2.37,0.06l0.23,-1.11l-0.75,-2.51l-1.45,-1.26l-1.43,-0.44l-0.53,-0.61Z",
      "name": "Georgia"
    },
    "GB": {
      "path": "M412.72,233.04l-2.32,4.44l0.45,0.57l2.5,-0.63l2.22,0.02l-0.56,3.24l-2.22,4.0l0.31,0.59l2.36,0.26l2.34,5.43l1.76,0.84l2.21,6.35l2.96,0.93l-0.25,2.13l-1.17,1.09l-0.09,0.47l0.87,1.82l-1.92,1.78l-3.29,-0.02l-4.09,1.04l-1.02,-0.68l-0.52,0.07l-1.5,1.67l-2.09,-0.4l-1.88,1.4l-0.67,-0.39l3.29,-3.71l2.15,-0.83l0.25,-0.41l-0.33,-0.35l-3.72,-0.64l-0.47,-1.06l2.27,-1.1l0.17,-0.57l-1.29,-2.09l0.39,-2.22l3.35,0.34l0.44,-0.34l0.37,-2.46l-1.77,-2.98l-3.1,-0.89l-0.43,-0.84l0.8,-2.18l-0.82,-1.22l-0.67,0.01l-0.66,1.02l-0.1,-3.02l-1.24,-2.37l0.87,-4.6l1.78,-3.54l1.83,0.33l2.26,-0.3ZM406.3,251.21l-1.06,2.32l-1.53,-0.71l-1.21,0.0l0.4,-1.97l-0.42,-1.89l1.46,-0.13l2.36,2.36Z",
      "name": "United Kingdom"
    },
    "GA": {
      "path": "M453.24,409.42l-0.08,0.98l0.7,1.29l2.36,0.24l-0.98,2.63l1.18,1.79l0.25,1.78l-0.29,1.52l-0.6,0.93l-1.84,-0.09l-1.23,-1.11l-0.66,0.23l-0.15,0.84l-1.42,0.26l-1.02,0.7l-0.11,0.52l0.77,1.35l-1.34,0.98l-3.94,-4.31l-1.44,-2.45l0.06,-0.6l0.54,-0.81l1.05,-3.46l4.17,-0.07l0.4,-0.4l-0.02,-2.66l2.39,0.21l1.25,-0.27Z",
      "name": "Gabon"
    },
    "GN": {
      "path": "M391.8,383.91l0.47,0.81l1.11,-0.32l0.98,0.71l1.07,0.2l2.26,-1.23l0.63,0.44l1.13,1.58l-0.48,1.41l0.8,0.3l-0.08,0.48l0.46,0.69l-0.35,1.37l1.05,2.63l-1.0,0.69l0.03,1.42l-0.72,-0.06l-1.07,1.01l-0.24,-0.27l0.07,-1.11l-1.05,-1.55l-0.49,-0.14l-1.3,0.36l-0.35,-2.01l-1.6,-2.19l-2.0,-0.0l-1.31,0.54l-1.95,2.19l-1.86,-2.2l-1.2,-0.78l-0.3,-1.12l-0.8,-0.86l0.65,-0.73l0.81,-0.03l1.64,-0.8l0.23,-1.88l2.67,0.64l0.89,-0.31l1.21,0.15Z",
      "name": "Guinea"
    },
    "GM": {
      "path": "M379.31,381.18l0.1,-0.36l2.43,-0.07l0.74,-0.62l0.5,-0.03l0.83,0.53l-1.08,-0.33l-1.87,0.91l-1.65,-0.04ZM384.0,380.68l0.95,0.06l0.76,-0.23l-0.59,0.32l-1.11,-0.15Z",
      "name": "Gambia"
    },
    "GL": {
      "path": "M352.9,3.19l15.35,16.28l-4.35,6.99l-9.4,0.81l-13.48,1.81l-0.32,0.54l1.26,3.26l0.46,0.25l8.67,-1.96l7.39,6.05l0.55,-0.04l4.4,-4.95l1.83,5.61l-2.72,9.68l0.18,0.45l0.48,-0.06l6.34,-6.15l11.94,-6.62l7.14,3.24l1.33,6.85l-10.07,11.17l-1.42,3.42l-7.83,2.5l-0.28,0.42l0.35,0.36l5.33,0.65l-2.8,9.83l-2.03,8.69l0.08,13.63l2.84,7.11l-3.6,0.49l-4.12,3.47l-0.05,0.56l4.54,5.53l0.56,8.17l-2.39,0.81l-0.24,0.53l3.05,7.7l-5.05,0.6l-0.27,0.64l2.78,3.54l-0.72,2.75l-3.27,1.26l-3.42,0.02l-0.35,0.59l3.09,5.7l0.03,2.82l-4.32,-2.99l-0.57,0.13l-1.29,2.22l0.14,0.54l3.3,2.0l3.18,4.75l0.88,5.79l-3.85,1.25l-4.86,-7.12l-0.48,-0.14l-0.24,0.44l0.83,5.08l-2.81,3.81l0.3,0.64l9.17,0.61l-6.07,5.68l-6.74,5.42l-7.2,2.3l-2.98,0.14l-2.66,2.67l-3.44,6.75l-5.23,4.25l-1.73,0.27l-7.11,3.08l-2.15,3.69l-0.09,4.21l-1.22,3.58l-4.03,4.36l0.89,4.48l-2.31,8.95l-3.05,0.26l-3.56,-4.0l-5.12,-0.16l-2.26,-2.64l-1.69,-5.21l-4.31,-6.82l-1.24,-3.62l-0.4,-5.4l-3.39,-5.47l0.87,-4.47l-1.62,-2.41l2.37,-7.41l3.81,-2.67l1.01,-3.01l0.52,-5.6l-0.22,-0.39l-0.45,0.06l-4.16,3.58l-1.99,0.9l-2.73,-2.07l-0.16,-4.72l0.9,-3.66l1.94,-0.09l5.03,1.98l0.47,-0.14l-0.03,-0.49l-6.54,-7.53l-0.47,-0.11l-2.25,1.0l-1.7,-1.6l2.69,-7.67l-1.51,-3.12l-4.99,-15.74l-3.17,-3.76l-0.11,-4.29l-6.93,-6.07l-5.4,-0.76l-12.62,1.16l-2.75,-3.16l-4.1,-6.46l6.13,-3.31l4.96,-0.6l0.35,-0.37l-0.29,-0.42l-10.63,-2.99l-5.42,-4.66l0.32,-4.37l9.32,-6.03l9.34,-6.65l0.97,-5.04l-0.15,-0.39l-6.52,-4.97l2.06,-5.6l8.57,-10.89l3.56,-1.73l0.22,-0.41l-1.01,-7.43l5.7,-4.5l7.58,-2.82l7.37,-0.16l2.62,5.4l0.69,0.04l6.35,-9.67l5.63,6.55l3.58,1.5l5.14,5.66l0.54,0.05l0.1,-0.53l-5.89,-9.52l0.33,-7.89l8.21,-11.86l8.55,0.93l0.41,-0.25l3.12,-7.8l8.58,-2.09l19.79,2.78Z",
      "name": "Greenland"
    },
    "GH": {
      "path": "M420.53,387.35l-0.01,0.72l0.96,1.2l0.24,3.75l0.59,0.95l-0.51,2.1l0.19,1.41l1.02,2.22l-6.97,2.85l-1.8,-0.57l0.04,-0.89l-1.02,-2.04l0.61,-2.66l1.07,-2.33l-0.96,-6.5l5.01,0.07l0.94,-0.39l0.61,0.11Z",
      "name": "Ghana"
    },
    "OM": {
      "path": "M568.09,360.37l-0.91,1.71l-1.22,0.04l-0.59,0.78l-0.41,1.53l0.26,1.63l-1.16,0.05l-1.56,0.99l-0.76,1.78l-1.62,0.05l-0.98,0.66l-0.17,1.17l-0.89,0.53l-1.49,-0.18l-2.4,0.95l-2.48,-5.51l7.35,-2.77l1.67,-5.36l-1.12,-2.14l0.05,-0.87l0.67,-1.04l0.07,-1.08l0.91,-0.43l-0.05,-2.14l0.7,-0.01l1.01,1.68l1.51,1.12l3.3,0.87l1.73,2.37l0.81,0.38l-1.23,2.44l-0.99,0.81ZM561.83,347.23l-0.0,-0.01l0.01,-0.01l-0.0,0.02Z",
      "name": "Oman"
    },
    "TN": {
      "path": "M448.18,315.32l-1.08,1.46l-0.02,1.43l0.84,0.93l-0.29,2.3l-1.65,1.83l0.48,1.65l1.41,0.33l0.53,1.2l0.9,0.55l-0.11,1.83l-3.54,2.81l-0.09,2.52l-0.58,0.32l-0.96,-4.72l-1.54,-1.32l-0.15,-0.82l-1.93,-1.68l-0.19,-1.93l1.52,-1.74l0.59,-2.52l-0.38,-3.0l0.43,-1.35l2.45,-1.14l1.29,0.28l-0.06,1.25l0.59,0.37l1.54,-0.84Z",
      "name": "Tunisia"
    },
    "JO": {
      "path": "M518.65,329.54l-5.15,1.67l-0.19,0.64l2.19,2.56l-0.58,0.44l-0.33,0.78l-1.71,0.36l-1.71,1.89l-2.34,-0.38l1.21,-4.6l0.56,-4.33l2.81,0.99l4.45,-2.88l0.8,2.87Z",
      "name": "Jordan"
    },
    "HR": {
      "path": "M455.59,286.98l1.42,0.1l0.57,-0.46l0.74,0.44l0.98,0.07l0.43,-0.4l-0.01,-0.73l0.86,-0.57l0.21,-1.25l1.62,-0.78l2.55,1.93l2.07,0.69l0.88,-0.35l1.09,1.85l-0.56,0.77l-1.05,-0.63l-1.67,0.05l-2.1,-0.57l-1.3,0.07l-0.58,0.54l-0.57,-0.52l-0.65,0.16l-0.47,1.84l1.79,2.75l2.11,2.07l0.81,1.23l-1.27,-1.06l-2.2,-0.99l-1.73,-2.1l0.2,-0.63l-1.06,-1.38l-0.31,-1.43l-1.61,-0.56l-0.49,0.2l-0.45,0.89l-0.26,-1.24Z",
      "name": "Croatia"
    },
    "HT": {
      "path": "M238.65,368.15l-1.58,-0.17l-1.19,0.44l-0.91,-0.56l0.06,-0.21l3.62,0.5ZM239.22,368.07l0.82,-0.54l0.06,-0.62l-1.02,-1.03l0.02,-0.84l-0.3,-0.39l-0.93,-0.35l3.16,0.46l0.02,1.9l-0.48,0.35l-0.07,0.58l0.54,0.74l-1.81,-0.26Z",
      "name": "Haiti"
    },
    "HU": {
      "path": "M462.05,281.37l0.68,-1.93l-0.16,-0.54l0.71,-0.0l0.39,-0.35l0.1,-0.84l1.72,1.0l2.35,-0.43l0.43,-0.77l3.49,-0.92l0.69,-0.91l0.54,-0.15l2.55,1.09l0.69,-0.26l1.03,0.76l0.1,0.55l-1.45,0.83l-2.6,4.82l-1.79,0.61l-1.69,-0.11l-2.72,1.41l-1.83,-0.61l-2.55,-1.92l-0.7,-1.3Z",
      "name": "Hungary"
    },
    "HN": {
      "path": "M199.6,379.29l-1.71,-1.22l0.07,-0.96l3.04,-2.17l2.37,0.29l1.27,-0.09l1.1,-0.53l1.3,0.28l1.14,-0.26l1.37,0.37l2.25,1.39l-2.37,0.95l-1.23,-0.4l-0.88,1.31l-1.28,1.0l-0.43,-0.3l-0.55,0.08l-0.42,0.53l-0.96,0.05l-0.36,0.41l0.04,0.89l-0.52,0.6l-0.3,0.04l-0.3,-0.56l-0.66,-0.32l0.12,-0.68l-0.48,-0.66l-0.63,-0.25l-0.97,0.2Z",
      "name": "Honduras"
    },
    "PR": {
      "path": "M256.17,368.34l-0.27,0.28l-2.83,0.06l-0.07,-0.57l1.95,-0.1l1.23,0.34Z",
      "name": "Puerto Rico"
    },
    "PS": {
      "path": "M509.06,331.4l0.27,-0.17l-0.04,0.09l-0.23,0.08ZM509.37,331.14l-0.03,-0.63l-0.35,-0.18l0.32,-1.21l0.24,0.11l-0.19,1.91Z",
      "name": "Palestine"
    },
    "PT": {
      "path": "M401.85,314.47l-0.65,0.52l-1.11,-0.37l-0.93,0.18l0.29,-1.97l-0.24,-1.95l-1.24,-0.59l-0.47,-0.95l0.18,-1.87l1.01,-1.29l0.69,-3.25l-0.04,-1.52l-0.59,-2.16l1.29,-0.96l0.85,1.5l3.09,-0.33l0.49,1.17l-1.07,1.02l-0.03,2.43l-0.41,0.6l-0.08,1.25l-0.8,0.2l-0.26,0.57l0.93,1.79l-0.64,1.95l0.78,1.16l-1.12,1.72l0.08,1.13Z",
      "name": "Portugal"
    },
    "PY": {
      "path": "M274.9,466.41l0.74,1.55l-0.16,3.55l0.32,0.41l2.64,0.52l1.11,-0.48l1.4,0.6l0.36,0.62l0.53,3.53l1.27,0.41l0.98,-0.39l0.52,0.28l-0.0,1.23l-1.21,5.54l-2.09,1.99l-1.8,0.41l-4.72,-1.03l2.21,-3.81l-0.32,-1.54l-2.77,-1.32l-3.03,-2.01l-2.07,-0.45l-4.34,-4.19l0.91,-2.99l0.08,-1.45l1.07,-2.09l4.13,-0.73l2.18,0.04l2.06,1.2l0.03,0.61Z",
      "name": "Paraguay"
    },
    "PA": {
      "path": "M213.79,393.56l0.26,-1.53l-0.36,-0.26l-0.01,-0.5l0.44,-0.1l0.93,1.4l1.26,0.03l0.77,0.5l1.38,-0.24l2.51,-1.12l0.86,-0.72l3.45,0.85l1.4,1.19l0.41,1.75l-0.21,0.34l-0.53,-0.12l-0.47,0.29l-0.16,0.6l-0.68,-1.28l0.45,-0.49l-0.19,-0.66l-0.47,-0.13l-0.54,-0.84l-1.5,-0.75l-1.1,0.16l-0.75,0.99l-1.62,0.84l-0.18,0.96l0.85,0.97l-0.58,0.45l-0.69,0.08l-0.34,-1.18l-1.27,0.03l-0.71,-1.05l-2.59,-0.47Z",
      "name": "Panama"
    },
    "PG": {
      "path": "M808.58,428.76l2.54,2.57l-0.13,0.26l-0.33,0.12l-0.87,-0.78l-1.22,-2.17ZM801.41,422.94l0.51,0.29l0.26,0.27l-0.49,-0.36l-0.28,-0.21ZM803.17,424.48l0.59,0.5l0.08,1.06l-0.29,-0.91l-0.38,-0.65ZM796.68,428.31l0.52,0.75l1.43,-0.19l2.27,-1.82l-0.01,-1.43l1.12,0.16l-0.04,1.1l-0.7,1.28l-1.12,0.18l-0.62,0.79l-2.46,1.11l-1.17,-0.0l-3.08,-1.25l3.41,0.0l0.45,-0.68ZM789.15,433.47l2.31,1.81l1.59,2.62l1.34,0.14l-0.06,0.66l0.31,0.43l1.06,0.24l0.06,0.66l2.25,1.06l-1.21,0.13l-0.72,-0.64l-4.56,-0.65l-3.22,-2.89l-1.49,-2.35l-3.27,-1.11l-2.38,0.72l-1.59,0.86l-0.2,0.42l0.27,1.56l-1.55,0.69l-1.36,-0.4l-2.21,-0.09l-0.08,-15.44l8.39,2.93l2.95,2.4l0.6,1.64l4.02,1.5l0.31,0.69l-1.76,0.21l-0.33,0.52l0.55,1.68Z",
      "name": "Papua New Guinea"
    },
    "PE": {
      "path": "M244.97,425.11l-1.26,-0.07l-0.57,0.42l-1.93,0.45l-2.98,1.76l-0.36,1.36l-0.58,0.8l0.12,1.37l-1.24,0.6l-0.22,1.22l-0.62,0.84l1.04,2.28l1.28,1.44l-0.41,0.85l0.32,0.57l1.48,0.13l1.16,1.37l2.21,0.07l1.63,-1.08l-0.13,3.04l0.3,0.4l1.14,0.29l1.31,-0.35l1.9,3.62l-0.48,0.86l-0.17,3.89l-0.94,1.6l0.35,0.76l-0.48,1.08l0.98,2.0l-2.1,3.89l-0.97,0.51l-2.17,-1.31l-0.39,-1.18l-4.95,-2.62l-4.46,-2.82l-1.85,-1.53l-0.91,-1.87l0.3,-0.97l-2.11,-3.36l-4.82,-9.74l-1.04,-1.2l-0.87,-1.95l-3.4,-2.49l0.58,-1.18l-1.13,-2.23l0.66,-1.5l1.45,-1.15l-0.6,0.99l0.07,0.92l0.47,0.36l1.74,0.03l0.97,1.17l0.54,0.07l1.42,-1.03l0.6,-1.84l1.42,-2.02l3.04,-1.04l2.73,-2.62l0.86,-1.74l-0.1,-1.87l1.44,1.02l0.9,1.25l1.06,0.59l1.7,2.73l1.86,0.31l1.45,-0.61l0.96,0.39l1.36,-0.19l1.45,0.89l-1.4,2.21l0.31,0.61l0.59,0.05l0.47,0.5Z",
      "name": "Peru"
    },
    "PK": {
      "path": "M615.13,319.81l-1.88,2.0l-2.59,0.42l-3.73,-0.73l-1.6,1.43l-0.09,0.4l1.77,4.7l1.73,1.32l-1.73,1.38l-0.11,2.26l-2.34,2.8l-1.59,2.95l-2.46,2.8l-3.03,-0.07l-2.76,2.96l0.05,0.59l1.51,1.16l0.26,1.98l1.44,1.55l0.37,1.77l-5.02,-0.01l-1.78,1.76l-1.41,-0.53l-0.76,-1.94l-2.27,-2.23l-11.61,0.89l0.72,-2.47l3.43,-1.37l0.25,-0.43l-0.21,-1.29l-1.2,-0.67l-0.28,-2.57l-2.29,-1.2l-1.32,-2.09l2.85,1.0l2.62,-0.4l1.42,0.35l0.77,-0.59l1.71,0.2l3.25,-1.2l0.26,-0.36l0.08,-2.33l1.19,-1.41l1.68,0.0l0.58,-0.87l1.59,-0.32l1.2,0.17l0.98,-0.83l0.01,-1.99l0.94,-1.58l1.48,-0.71l0.19,-0.54l-0.69,-1.39l2.06,-0.12l0.69,-1.09l-0.03,-1.23l1.12,-1.15l-0.18,-1.88l-0.5,-1.14l1.17,-1.09l5.42,-0.99l2.59,-0.89l1.6,1.26l0.97,2.53l3.5,1.06Z",
      "name": "Pakistan"
    },
    "PH": {
      "path": "M737.01,393.71l0.39,2.98l-0.44,1.19l-0.55,-1.53l-0.67,-0.14l-1.17,1.28l0.65,2.1l-0.42,0.69l-2.48,-1.23l-0.58,-1.49l0.66,-1.03l-0.1,-0.53l-1.59,-1.19l-0.56,0.08l-0.65,0.87l-1.23,0.0l-1.58,0.97l0.83,-1.81l2.56,-1.42l0.65,0.84l0.45,0.13l1.9,-0.69l0.56,-1.12l1.5,-0.06l0.38,-0.43l-0.09,-1.2l1.21,0.72l0.36,2.03ZM733.59,386.41l0.05,0.76l0.08,0.27l-0.8,-0.42l-0.18,-0.72l0.85,0.12ZM734.08,385.93l-0.12,-1.13l-1.01,-1.29l1.36,0.03l0.53,0.73l0.51,2.06l-1.27,-0.4ZM733.76,387.52l0.39,0.99l-0.32,0.15l-0.07,-1.14ZM724.65,368.03l1.46,0.71l0.72,-0.31l-0.32,1.19l0.79,1.74l-0.57,1.88l-1.53,1.06l-0.39,2.27l0.56,2.06l1.63,0.57l1.16,-0.27l2.72,1.24l-0.19,1.1l0.77,0.85l-0.08,0.37l-1.4,-0.9l-0.88,-1.29l-0.66,0.0l-0.38,0.55l-1.6,-1.32l-2.15,0.36l-0.87,-0.4l0.07,-0.62l0.66,-0.56l-0.01,-0.62l-0.75,-0.6l-0.72,0.44l-0.73,-0.88l-0.39,-2.53l0.32,0.27l0.66,-0.28l0.26,-4.04l0.71,-2.06l1.14,0.0ZM731.03,388.72l-0.88,0.85l-1.19,1.95l-1.05,-1.2l0.93,-1.11l0.32,-1.48l0.52,-0.06l-0.27,1.16l0.22,0.45l0.49,-0.12l1.0,-1.32l-0.08,0.86ZM726.83,385.61l0.83,0.38l1.17,-0.0l-0.02,0.48l-2.0,1.41l0.02,-2.28ZM724.81,381.88l-0.39,1.29l-1.42,-1.98l1.2,0.05l0.6,0.64ZM716.54,391.7l1.12,-0.97l0.03,-0.03l-0.28,0.38l-0.87,0.63ZM719.21,388.91l0.04,-0.07l0.8,-1.54l0.16,0.76l-1.01,0.85Z",
      "name": "Philippines"
    },
    "PL": {
      "path": "M468.45,271.45l-1.1,-1.82l-1.87,-0.39l-0.48,-1.25l-1.72,-0.44l-0.47,0.25l-0.21,0.56l-0.72,-0.43l0.12,-0.82l-0.32,-0.45l-1.74,-0.32l-1.05,-1.13l-0.96,-2.4l0.17,-1.46l-0.62,-2.19l-0.82,-1.37l0.61,-1.22l-0.51,-1.88l1.46,-1.07l6.88,-3.37l2.12,0.62l0.15,0.81l0.38,0.33l5.51,0.54l4.53,-0.06l1.06,0.38l0.5,1.09l0.14,1.93l0.66,1.51l-0.01,1.34l-1.3,0.73l-0.17,0.5l0.74,1.83l0.07,1.86l1.22,3.37l-0.19,0.78l-1.23,0.53l-2.27,3.23l0.24,1.15l-1.99,-1.23l-2.01,0.46l-1.38,-0.32l-1.2,0.67l-1.05,-1.13l-1.17,0.27Z",
      "name": "Poland"
    },
    "ZM": {
      "path": "M481.47,443.27l0.39,0.31l2.52,0.15l0.99,1.18l2.01,0.36l1.4,-0.64l0.69,1.18l1.78,0.33l1.84,2.38l2.24,0.19l0.4,-0.43l-0.21,-2.77l-0.62,-0.3l-0.48,0.33l-1.98,-1.18l0.72,-5.32l-0.51,-1.19l0.58,-1.31l3.68,-0.62l0.26,0.64l1.21,0.63l0.9,-0.22l2.16,0.67l1.33,0.71l1.07,1.02l0.56,1.89l-0.88,2.72l0.43,2.1l-0.73,0.88l-0.76,2.39l0.6,0.68l-6.61,1.85l-0.29,0.44l0.19,1.47l-1.69,0.36l-1.43,1.04l-0.38,0.89l-0.87,0.26l-3.48,3.75l-4.15,-0.54l-1.52,-1.01l-1.77,-0.14l-1.82,0.53l-3.04,-3.46l0.11,-7.69l4.82,0.03l0.39,-0.49l-0.18,-0.76l0.33,-0.84l-0.4,-1.37l0.24,-1.06Z",
      "name": "Zambia"
    },
    "EH": {
      "path": "M384.42,359.7l0.26,-0.83l1.06,-1.32l0.8,-3.63l3.38,-2.88l0.69,-1.87l0.06,5.03l-1.98,0.21l-0.94,1.63l0.39,3.66l-3.71,-0.01ZM392.0,347.13l0.72,-1.91l1.77,-0.25l2.09,0.35l0.96,-0.65l1.27,-0.07l-0.0,2.65l-6.8,-0.12Z",
      "name": "W. Sahara"
    },
    "EE": {
      "path": "M485.7,228.2l2.62,0.79l2.44,-0.11l0.18,0.41l-1.67,2.62l0.66,4.56l-0.85,1.18l-1.72,-0.01l-3.21,-2.27l-1.85,0.58l0.22,-2.14l-0.62,-0.38l-0.64,0.42l-1.26,-1.35l-0.18,-2.36l2.87,-1.24l3.02,-0.69Z",
      "name": "Estonia"
    },
    "EG": {
      "path": "M492.06,333.38l1.47,0.44l2.95,-1.74l2.03,-0.22l1.52,0.32l0.6,1.27l0.7,0.04l0.41,-0.68l1.8,0.61l1.95,0.17l1.04,-0.54l1.43,4.34l-2.03,4.78l-1.66,-1.85l-1.76,-4.05l-0.65,-0.12l-0.35,0.67l1.04,3.03l3.44,7.26l1.77,3.16l2.04,2.76l-0.37,0.54l0.22,2.06l2.73,2.28l-28.43,0.0l0.0,-19.72l-0.73,-2.31l0.6,-1.66l-0.33,-1.32l0.69,-1.07l3.05,-0.04l4.82,1.62Z",
      "name": "Egypt"
    },
    "ZA": {
      "path": "M467.15,505.21l-0.13,-2.11l-0.69,-1.7l0.71,-0.7l-0.12,-2.46l-4.57,-8.67l0.78,-0.92l0.59,0.47l0.69,1.37l2.83,0.75l1.5,-0.27l2.24,-1.46l0.18,-9.94l1.35,2.39l-0.21,1.57l0.61,1.24l0.41,0.2l1.79,-0.29l2.61,-2.16l0.69,-1.37l0.95,-0.5l2.19,1.08l2.04,0.14l1.78,-0.67l0.85,-2.2l1.38,-0.34l1.59,-2.85l2.15,-1.95l3.41,-1.92l1.99,0.46l1.02,-0.28l0.99,0.2l1.75,5.47l-0.37,3.39l-0.82,-0.24l-1.0,0.47l-0.87,1.75l-0.04,1.2l1.98,1.91l1.47,-0.3l0.7,-1.24l1.09,0.01l-0.77,3.89l-0.58,1.15l-2.2,1.88l-3.17,5.02l-2.8,3.01l-3.57,3.07l-2.53,1.12l-1.22,0.15l-0.51,0.75l-1.17,-0.34l-1.4,0.54l-2.58,-0.55l-1.62,0.35l-1.19,-0.11l-2.54,1.18l-2.1,0.47l-1.6,1.15l-0.84,0.05l-0.93,-0.95l-0.93,-0.16l-0.97,-1.21l-0.25,0.05ZM491.46,495.56l0.62,-0.98l1.48,-0.62l1.18,-2.31l-0.07,-0.48l-1.99,-1.77l-1.68,0.59l-1.42,1.19l-1.34,1.82l0.02,0.49l1.88,2.23l1.32,-0.17Z",
      "name": "South Africa"
    },
    "EC": {
      "path": "M231.86,415.43l0.29,1.59l-0.69,1.45l-2.61,2.51l-3.13,1.11l-1.53,2.18l-0.49,1.68l-1.0,0.73l-1.02,-1.11l-1.78,-0.16l0.67,-1.15l-0.24,-0.86l1.25,-2.13l-0.54,-1.09l-0.67,-0.08l-0.72,0.87l-0.87,-0.64l0.35,-0.69l-0.36,-1.96l0.81,-0.51l0.45,-1.51l0.92,-1.57l-0.07,-0.97l2.65,-1.33l2.75,1.35l0.77,1.05l2.12,0.35l0.76,-0.32l1.96,1.21Z",
      "name": "Ecuador"
    },
    "IT": {
      "path": "M451.58,282.14l3.5,1.08l-0.22,1.43l0.34,1.0l-1.55,-0.28l-2.22,1.64l0.13,1.69l-0.27,1.22l0.82,1.78l2.39,1.84l1.3,2.87l2.79,2.73l2.05,0.1l0.25,0.31l-0.43,0.41l0.09,0.64l4.05,2.19l2.2,2.0l-0.17,0.42l-1.16,-1.17l-2.18,-0.54l-0.45,0.21l-1.05,2.12l0.14,0.51l1.59,1.06l-0.2,1.15l-1.06,0.36l-1.25,2.57l-0.36,0.08l0.0,-0.41l1.01,-2.65l-1.73,-3.5l-1.12,-0.56l-0.67,-1.29l-1.72,-0.75l-1.01,-1.25l-2.01,-0.35l-4.11,-3.59l-1.63,-1.87l-1.03,-3.6l-3.56,-1.55l-1.3,0.58l-1.68,1.6l0.17,-0.9l-0.27,-0.45l-1.14,-0.37l-0.55,-2.31l0.78,-1.37l-0.66,-1.44l0.81,0.44l1.41,-0.27l1.08,-0.94l0.53,0.39l1.19,-0.11l0.75,-1.38l1.51,0.37l1.39,-0.65l0.34,-1.31l1.06,0.36l0.5,-0.22l0.21,-0.51l1.95,-0.5l0.42,0.96ZM459.21,311.54l-0.67,1.87l0.33,1.12l-0.32,0.99l-1.48,-0.91l-4.52,-1.83l0.21,-0.97l2.67,0.25l3.8,-0.53ZM443.92,301.94l1.19,1.86l-0.3,3.74l-1.07,-0.01l-0.75,0.79l-0.53,-0.48l-0.1,-3.76l-0.41,-1.41l1.07,0.0l0.9,-0.74Z",
      "name": "Italy"
    },
    "VN": {
      "path": "M690.58,359.66l-2.72,1.89l-2.09,2.52l-0.63,1.98l4.31,6.55l2.32,1.68l1.44,1.97l1.11,4.65l-0.32,4.28l-1.93,1.55l-2.84,1.62l-2.11,2.17l-2.73,2.07l-0.59,-1.06l0.63,-1.54l-0.12,-0.47l-1.34,-1.05l1.51,-0.72l2.55,-0.18l0.3,-0.63l-0.82,-1.16l4.0,-2.09l0.31,-3.08l-0.57,-1.79l0.42,-2.69l-0.73,-1.99l-1.86,-1.79l-3.63,-5.38l-2.73,-1.5l0.37,-0.5l1.5,-0.65l0.21,-0.52l-0.97,-2.33l-0.37,-0.25l-2.83,-0.02l-2.25,-4.02l0.84,-0.42l4.39,-0.3l2.06,-1.35l1.15,0.91l1.88,0.41l-0.18,1.55l1.36,1.19l1.69,0.47Z",
      "name": "Vietnam"
    },
    "SB": {
      "path": "M826.68,441.55l-0.6,0.09l-0.2,-0.34l0.37,0.15l0.44,0.09ZM824.18,437.32l-0.26,-0.31l-0.31,-0.91l0.03,0.0l0.54,1.22ZM823.04,439.28l-1.66,-0.22l-0.2,-0.53l1.16,0.28l0.7,0.47ZM819.26,434.58l1.17,0.66l0.03,0.04l-0.82,-0.45l-0.38,-0.25Z",
      "name": "Solomon Is."
    },
    "ET": {
      "path": "M516.04,377.54l1.1,0.85l1.63,-0.46l0.68,0.48l1.63,0.03l2.01,0.96l1.73,1.68l1.64,2.1l-1.52,2.06l0.16,1.73l0.39,0.38l2.05,0.01l-0.36,1.03l2.86,3.6l8.32,3.09l1.32,0.02l-6.33,6.76l-3.1,0.11l-2.36,1.77l-1.47,0.04l-0.86,0.79l-1.38,-0.0l-1.32,-0.81l-2.29,1.05l-0.76,0.98l-3.29,-0.41l-3.07,-2.07l-1.8,-0.07l-0.62,-0.6l0.0,-1.24l-0.28,-0.38l-1.15,-0.37l-1.4,-2.6l-1.19,-0.69l-0.47,-1.01l-1.27,-1.23l-1.16,-0.22l0.43,-0.73l1.45,-0.28l0.41,-0.95l-0.03,-2.22l0.68,-2.45l1.05,-0.63l1.43,-3.08l1.57,-1.38l1.02,-2.53l0.35,-1.9l2.52,0.47l0.44,-0.24l0.58,-1.44Z",
      "name": "Ethiopia"
    },
    "SO": {
      "path": "M525.13,418.38l-1.13,-1.57l-0.03,-8.86l2.66,-3.38l1.67,-0.13l2.13,-1.69l3.41,-0.23l7.08,-7.57l2.91,-3.71l0.08,-4.85l2.98,-0.67l1.24,-0.87l0.45,-0.0l-0.2,3.03l-1.21,3.64l-2.73,6.0l-2.13,3.66l-5.03,6.17l-8.56,6.4l-2.78,3.08l-0.8,1.56Z",
      "name": "Somalia"
    },
    "ZW": {
      "path": "M498.91,471.53l-1.1,-0.22l-0.92,0.29l-2.09,-0.46l-1.49,-1.14l-1.89,-0.44l-0.62,-1.44l-0.01,-0.86l-0.3,-0.38l-0.97,-0.26l-2.72,-2.8l-1.93,-3.41l3.83,0.46l3.74,-3.89l1.08,-0.44l0.26,-0.78l1.25,-0.91l1.41,-0.26l0.5,0.9l1.99,-0.05l1.72,1.19l1.11,0.18l1.05,0.68l0.01,3.05l-0.59,3.84l0.38,0.87l-0.23,1.26l-0.39,0.36l-0.64,1.86l-2.43,2.82Z",
      "name": "Zimbabwe"
    },
    "ES": {
      "path": "M415.99,294.24l1.08,1.32l4.61,1.55l1.08,-0.64l2.58,1.41l2.72,-0.33l0.09,1.34l-2.15,2.02l-3.1,0.68l-0.31,0.31l-0.2,1.01l-1.54,1.87l-0.97,2.65l0.86,1.9l-1.34,1.4l-0.49,1.86l-1.88,0.7l-1.66,2.25l-5.35,-0.01l-1.81,1.17l-0.88,1.06l-0.86,-0.18l-0.79,-0.9l-0.68,-1.73l-2.37,-0.68l-0.12,-0.6l1.21,-2.0l-0.78,-1.19l0.62,-1.89l-0.8,-1.8l0.89,-0.51l0.09,-1.41l0.42,-0.63l0.03,-2.39l1.01,-0.78l0.12,-0.47l-1.04,-1.93l-1.46,-0.12l-0.63,0.42l-1.04,0.0l-0.53,-1.39l-0.55,-0.22l-1.31,0.73l0.07,-1.41l-0.87,-1.4l3.08,-2.16l2.98,0.6l3.32,-0.02l2.62,0.58l6.01,-0.06Z",
      "name": "Spain"
    },
    "ER": {
      "path": "M520.38,375.96l3.42,2.46l3.5,3.81l0.85,0.55l-0.95,-0.01l-3.51,-3.92l-2.33,-1.16l-1.73,-0.07l-0.91,-0.51l-1.25,0.52l-1.34,-1.03l-0.62,0.17l-0.66,1.63l-2.34,-0.43l-0.18,-0.68l1.29,-5.37l0.62,-0.63l1.95,-0.54l0.87,-1.03l1.17,2.45l0.68,2.36l1.49,1.45Z",
      "name": "Eritrea"
    },
    "ME": {
      "path": "M468.91,298.06l-1.24,-1.13l0.5,-2.11l0.88,-0.81l2.29,1.73l-0.52,0.71l-0.77,-0.3l-1.14,1.91Z",
      "name": "Montenegro"
    },
    "MD": {
      "path": "M491.9,285.98l-0.28,-1.04l0.25,-1.54l-0.15,-1.8l-3.32,-5.2l1.4,-0.31l1.71,1.08l1.07,0.18l0.88,0.78l0.03,1.44l0.78,0.52l0.33,1.38l0.81,0.94l0.0,0.67l-1.14,-0.08l-0.7,-0.47l-0.52,0.29l-0.06,0.94l-1.08,2.21Z",
      "name": "Moldova"
    },
    "MG": {
      "path": "M545.91,449.15l0.4,3.06l0.63,1.22l-0.21,1.04l-0.56,-0.81l-0.69,-0.01l-0.47,0.77l0.41,2.15l-0.18,0.89l-0.72,0.79l-0.15,2.18l-5.77,18.57l-3.92,1.7l-3.12,-1.54l-0.6,-1.26l-0.19,-2.48l-0.86,-2.12l-0.21,-1.83l0.39,-1.67l1.21,-0.76l0.01,-0.79l1.19,-2.08l0.23,-1.69l-1.06,-3.05l-0.19,-2.26l0.81,-1.36l0.32,-1.49l4.63,-1.23l3.44,-3.04l0.85,-1.42l-0.09,-0.71l0.78,-0.04l1.38,-1.79l0.13,-1.65l0.45,-0.62l1.16,1.7l0.59,1.62Z",
      "name": "Madagascar"
    },
    "MA": {
      "path": "M378.77,359.44l0.06,-0.63l0.93,-0.75l0.82,-1.41l-0.09,-1.07l0.79,-1.77l1.31,-1.64l0.95,-0.61l0.66,-1.61l0.09,-1.52l0.81,-1.54l1.72,-1.11l1.55,-2.81l1.16,-1.0l2.44,-0.41l1.94,-1.91l1.31,-0.82l2.09,-2.4l-0.51,-3.84l1.25,-3.95l1.5,-1.88l4.46,-2.74l2.37,-4.82l1.43,0.01l1.7,1.31l2.31,-0.21l3.46,0.7l0.81,1.67l0.16,1.84l0.86,3.17l0.57,0.63l-0.27,0.69l-3.05,0.46l-1.26,1.11l-1.33,0.24l-0.33,0.37l-0.09,1.91l-2.69,1.06l-1.07,1.5l-1.89,0.72l-2.58,0.47l-4.04,2.12l-0.53,4.86l-1.16,0.07l-0.92,0.64l-1.96,-0.36l-2.42,0.56l-0.74,1.99l-0.86,0.41l-1.14,3.39l-3.53,3.11l-0.81,3.66l-0.96,1.14l-0.29,0.84l-4.94,0.19Z",
      "name": "Morocco"
    },
    "UZ": {
      "path": "M598.64,298.24l-1.64,1.79l0.06,0.61l1.85,1.26l1.99,-0.71l2.27,1.34l-2.58,1.91l-2.57,-0.24l-0.2,-0.5l0.47,-1.39l-0.47,-0.52l-3.35,0.77l-2.1,3.89l-1.86,-0.14l-0.39,0.23l-0.65,1.43l0.21,0.53l1.65,0.69l0.47,2.05l-1.21,2.74l-1.54,-0.54l-1.11,-0.04l0.05,-1.53l-0.25,-0.38l-3.3,-1.35l-2.56,-1.53l-4.4,-3.69l-1.33,-3.48l-1.1,-0.68l-2.57,0.15l-0.7,-0.5l-0.46,-2.81l-3.37,-1.79l-0.46,0.06l-2.07,1.94l-2.09,1.14l-0.2,0.45l0.29,1.2l-1.92,0.03l-0.09,-11.97l5.98,-1.95l6.18,4.04l2.35,3.08l7.41,-0.61l2.72,2.28l-0.18,3.21l0.39,0.42l0.89,0.02l0.45,2.42l0.38,0.33l2.93,0.1l0.96,1.58l1.29,-0.25l1.05,-2.28l3.18,-2.25l1.24,-0.54Z",
      "name": "Uzbekistan"
    },
    "MM": {
      "path": "M673.9,359.64l-1.97,1.62l-0.57,0.98l-1.4,0.62l-1.36,1.08l-1.99,0.36l-1.08,2.72l-0.91,0.41l-0.19,0.55l1.21,2.31l2.52,3.49l-0.79,1.95l-0.74,0.41l-0.17,0.52l0.65,1.39l1.61,1.98l0.25,2.61l0.9,2.15l-1.92,3.6l0.68,-2.27l-0.81,-1.75l0.19,-2.68l-1.05,-1.54l-1.24,-6.25l-1.12,-2.29l-0.61,-0.13l-4.33,3.06l-2.39,-0.66l0.77,-2.89l-0.52,-2.65l-1.92,-3.02l0.25,-0.78l-0.29,-0.51l-1.33,-0.31l-1.61,-1.97l-0.1,-1.35l0.82,-0.23l0.04,-1.7l1.03,-0.53l0.21,-0.44l-0.23,-0.99l0.54,-0.98l0.08,-2.3l1.45,0.46l0.48,-0.2l1.12,-2.26l0.16,-1.4l1.34,-2.25l-0.01,-1.58l2.89,-1.73l1.62,0.46l0.51,-0.43l-0.17,-1.48l0.65,-0.39l0.07,-1.08l0.77,-0.11l0.71,1.41l1.06,0.72l-0.03,4.05l-2.38,2.46l-0.3,3.26l0.47,0.43l2.27,-0.39l0.51,2.15l1.47,0.69l-0.61,1.87l0.19,0.47l2.97,1.52l1.64,-0.56l0.02,0.35Z",
      "name": "Myanmar"
    },
    "ML": {
      "path": "M392.61,383.9l-0.19,-2.39l-0.99,-0.88l-0.44,-1.31l-0.09,-1.3l0.81,-0.59l0.35,-1.26l2.37,0.66l1.31,-0.48l0.86,0.15l0.66,-0.57l9.83,-0.04l0.38,-0.28l0.56,-1.82l-0.44,-0.66l-2.35,-22.51l3.26,-0.04l16.7,11.72l0.74,1.34l2.5,1.11l0.02,1.42l0.44,0.39l2.34,-0.22l0.01,5.49l-1.28,1.64l-0.26,1.51l-5.31,0.58l-1.08,0.93l-2.9,0.1l-0.87,-0.48l-1.38,0.37l-2.4,1.1l-0.6,0.88l-1.86,1.1l-0.43,0.71l-0.79,0.4l-1.44,-0.21l-0.81,0.84l-0.34,1.65l-1.91,2.04l-0.06,1.04l-0.67,1.23l0.13,1.17l-0.97,0.39l-0.23,-0.65l-0.52,-0.24l-1.35,0.4l-0.34,0.55l-2.69,-0.29l-0.37,-0.36l-0.02,-0.91l-0.65,-0.35l0.45,-0.65l-0.03,-0.52l-2.12,-2.46l-0.76,-0.01l-2.0,1.17l-0.78,-0.15l-0.8,-0.67l-1.21,0.23Z",
      "name": "Mali"
    },
    "MN": {
      "path": "M676.61,267.85l3.78,1.95l5.69,-1.19l2.35,0.48l2.34,1.79l1.81,2.09l2.28,-0.04l3.11,0.62l2.49,-0.96l3.42,-0.7l3.51,-2.62l1.21,0.34l1.56,1.35l2.31,-0.25l-2.72,6.05l0.64,1.85l0.5,0.22l1.31,-0.44l2.36,0.55l2.04,-1.29l1.73,1.03l2.1,2.39l-0.15,0.72l-1.72,-0.34l-3.79,0.54l-1.88,1.14l-1.76,2.29l-3.71,1.35l-2.44,1.82l-3.81,-0.99l-0.44,0.19l-1.31,2.27l1.07,2.53l-1.56,1.04l-1.74,1.78l-2.78,1.14l-3.78,0.14l-4.05,1.18l-2.75,1.69l-1.16,-0.94l-2.93,0.0l-3.61,-2.0l-2.59,-0.55l-3.41,0.46l-5.11,-0.75l-2.62,0.07l-1.31,-1.82l-1.4,-3.4l-1.47,-0.37l-3.14,-2.22l-6.15,-1.06l-0.73,-1.26l0.89,-4.37l-1.73,-2.97l-3.7,-1.54l-1.96,-1.86l-0.53,-2.16l2.39,-0.63l4.75,-3.33l3.59,-1.75l2.18,1.16l2.44,0.05l1.83,1.83l2.46,0.14l3.58,0.97l0.4,-0.12l2.43,-2.72l0.07,-0.43l-0.93,-2.14l2.28,-3.66l2.59,1.52l4.94,1.41l0.44,2.74Z",
      "name": "Mongolia"
    },
    "MK": {
      "path": "M472.81,299.6l0.49,-0.78l3.56,-0.8l1.01,0.87l0.14,1.71l-0.66,0.59l-1.14,-0.05l-1.14,0.75l-1.37,0.24l-0.79,-0.61l-0.3,-1.19l0.2,-0.73Z",
      "name": "Macedonia"
    },
    "MW": {
      "path": "M505.5,439.25l0.85,1.96l0.15,2.88l-0.69,1.66l0.72,1.81l0.06,1.29l0.49,0.64l0.07,1.07l0.4,0.55l0.8,-0.23l0.55,0.62l0.7,-0.21l0.34,0.6l0.19,2.98l-1.04,0.63l-0.53,1.27l-1.11,-1.1l-0.16,-1.59l0.51,-1.33l-0.32,-1.32l-0.99,-0.65l-0.82,0.12l-2.36,-1.66l0.63,-1.99l0.82,-1.18l-0.46,-2.03l0.9,-2.88l-0.95,-2.53l0.97,0.19l0.29,0.41Z",
      "name": "Malawi"
    },
    "MR": {
      "path": "M407.4,349.79l-2.62,0.03l-0.39,0.44l2.42,23.13l0.37,0.43l-0.39,1.27l-9.75,0.04l-0.56,0.54l-0.91,-0.11l-1.27,0.46l-1.61,-0.66l-0.98,0.03l-0.36,0.29l-0.38,1.37l-0.42,0.24l-2.93,-3.44l-2.96,-1.55l-1.62,-0.03l-1.27,0.55l-1.12,-0.2l-0.65,0.4l-0.08,-0.51l0.68,-1.31l0.31,-2.47l-0.57,-3.99l0.23,-1.25l-0.68,-1.53l-1.16,-1.05l0.25,-0.42l9.58,0.02l0.4,-0.45l-0.46,-3.79l0.47,-1.08l2.11,-0.22l0.36,-0.4l-0.08,-6.64l7.81,0.14l0.41,-0.4l0.01,-3.47l7.8,5.59Z",
      "name": "Mauritania"
    },
    "UG": {
      "path": "M498.55,406.22l0.7,-0.46l1.65,0.5l1.96,-0.57l1.7,0.01l1.45,-0.98l0.91,1.33l1.33,3.95l-2.57,4.03l-1.46,-0.4l-2.54,0.91l-1.37,1.61l-0.01,0.81l-2.42,-0.01l-2.26,1.01l-0.17,-1.59l0.58,-1.04l0.14,-1.94l1.37,-2.28l1.78,-1.58l-0.17,-0.65l-0.72,-0.24l0.13,-2.43Z",
      "name": "Uganda"
    },
    "MY": {
      "path": "M717.48,403.36l-1.39,0.65l-2.12,-0.41l-2.88,-0.0l-0.38,0.28l-0.84,2.75l-0.99,0.96l-1.21,3.29l-1.73,0.45l-2.45,-0.68l-1.39,0.31l-1.33,1.15l-1.59,-0.14l-1.41,0.44l-1.44,-1.19l-0.18,-0.73l1.34,0.53l1.93,-0.47l0.75,-2.23l4.02,-1.03l2.75,-3.21l0.82,0.94l0.64,-0.05l0.4,-0.65l0.96,0.06l0.42,-0.36l0.24,-2.69l1.81,-1.65l1.21,-1.87l0.63,-0.01l1.07,1.06l0.34,1.28l3.44,1.35l-0.06,0.35l-1.37,0.1l-0.35,0.54l0.32,0.88ZM673.68,399.48l0.17,1.1l0.47,0.33l1.65,-0.3l0.87,-0.94l1.61,1.52l0.98,1.57l-0.12,2.81l0.41,2.29l0.95,0.9l0.88,2.44l-1.27,0.12l-5.1,-3.68l-0.34,-1.29l-1.37,-1.59l-0.33,-1.97l-0.88,-1.4l0.25,-1.68l-0.46,-1.06l1.63,0.84Z",
      "name": "Malaysia"
    },
    "MX": {
      "path": "M133.1,328.46l0.22,0.49l9.64,3.54l6.96,-0.02l0.4,-0.4l0.0,-0.81l3.76,0.0l3.55,3.11l1.4,2.99l1.51,1.09l2.08,0.86l0.48,-0.14l1.46,-2.1l1.72,-0.05l1.59,1.03l2.06,3.53l1.47,1.63l1.26,3.28l2.18,1.06l2.27,0.6l-1.19,3.88l-0.42,5.19l1.79,5.01l1.62,1.94l0.61,1.55l1.2,1.45l2.55,0.67l1.38,1.13l7.54,-1.93l1.86,-1.32l1.14,-4.4l4.1,-1.24l3.56,-0.11l0.32,0.31l-0.06,0.97l-1.26,1.49l-0.67,1.74l0.38,0.71l-0.73,2.32l-0.49,-0.3l-1.0,0.08l-1.0,1.41l-0.47,-0.11l-0.53,0.47l-4.26,-0.02l-0.4,0.4l-0.0,1.08l-1.1,0.26l0.1,0.44l1.82,1.46l0.56,0.94l-3.19,0.21l-1.21,2.12l0.24,0.73l-0.2,0.45l-2.24,-2.21l-1.45,-0.94l-2.22,-0.7l-1.52,0.23l-3.06,1.18l-10.55,-3.9l-2.86,-2.0l-3.78,-0.94l-1.08,-1.21l-2.62,-1.46l-1.18,-1.57l-0.39,-0.85l0.66,-0.64l-0.19,-0.55l0.53,-0.77l0.01,-0.93l-2.0,-3.91l-2.21,-2.71l-2.53,-2.16l-1.19,-1.68l-2.2,-1.21l-0.31,-0.45l0.34,-1.56l-0.21,-0.44l-1.23,-0.63l-1.36,-1.26l-0.59,-1.87l-1.53,-0.48l-2.44,-2.68l-0.15,-0.94l-1.33,-2.14l-0.84,-2.11l-0.15,-1.39l-1.81,-1.16l-0.98,0.05l-1.31,-0.74l-0.58,0.22l-0.4,1.19l0.71,3.95l3.51,4.09l0.28,0.83l0.53,0.26l0.41,1.51l1.33,1.8l1.58,1.46l0.8,2.49l1.43,2.51l0.13,1.37l0.37,0.36l1.03,0.08l1.68,2.38l-0.84,0.79l-0.66,-1.55l-1.68,-1.59l-2.91,-1.94l0.06,-1.89l-0.53,-1.73l-2.91,-2.11l-0.56,0.08l-1.95,-1.14l-0.92,-1.02l0.72,-0.08l0.93,-1.06l0.08,-1.82l-1.93,-2.04l-1.46,-0.81l-3.76,-8.06l4.87,-0.45Z",
      "name": "Mexico"
    },
    "IL": {
      "path": "M507.77,331.27l0.39,-0.81l0.2,0.43l-0.34,1.09l0.52,0.43l0.68,-0.23l-0.86,3.84l-1.16,-3.52l0.6,-0.8l-0.03,-0.44ZM508.72,328.43l0.38,-1.13l0.64,0.0l0.52,-0.54l0.02,0.67l-0.52,1.01l-0.55,-0.25l-0.5,0.24Z",
      "name": "Israel"
    },
    "FR": {
      "path": "M444.48,298.15l-0.65,2.02l-0.56,-0.34l-0.51,-1.98l0.42,-1.04l0.99,-0.8l0.31,2.13ZM429.62,268.54l1.78,1.88l1.48,-0.14l2.08,1.68l1.36,0.33l1.23,0.98l3.1,0.6l-1.08,2.26l-0.3,2.52l-0.41,0.38l-0.92,-0.28l-0.51,0.42l0.07,0.77l-1.82,2.19l-0.04,1.65l0.57,0.37l0.85,-0.41l0.62,1.14l-0.04,1.13l0.61,1.11l-0.78,1.22l0.65,2.72l1.29,0.62l-0.19,1.03l-2.02,1.73l-4.75,-0.9l-3.84,1.13l-0.52,2.09l-2.47,0.37l-2.7,-1.47l-1.18,0.64l-4.28,-1.44l-0.76,-1.02l1.21,-2.03l0.41,-7.31l-2.58,-3.82l-1.89,-1.93l-3.74,-1.44l-0.2,-2.16l2.82,-0.72l4.11,0.96l0.48,-0.46l-0.62,-3.38l1.98,1.12l5.83,-3.02l0.91,-3.28l1.57,-0.58l0.25,0.97l1.34,0.35l1.05,1.43ZM289.01,408.29l-0.81,0.8l-0.78,0.12l-0.5,-0.66l-0.56,-0.1l-0.91,0.6l-0.46,-0.22l1.09,-2.96l-0.96,-1.77l-0.17,-1.49l1.07,-1.77l2.32,0.75l2.51,2.01l0.3,0.74l-2.14,3.96Z",
      "name": "France"
    },
    "XS": {
      "path": "M531.15,388.78l1.52,0.12l5.13,-0.96l5.3,-1.49l-0.01,4.43l-2.67,3.4l-1.85,0.01l-8.04,-2.95l-2.55,-3.19l1.12,-1.73l2.04,2.35Z",
      "name": "Somaliland"
    },
    "FI": {
      "path": "M492.16,172.43l-0.28,5.17l3.67,4.26l-2.21,4.98l2.86,6.98l-1.64,5.01l2.21,4.51l-0.98,3.55l3.63,4.02l-0.84,2.48l-7.53,9.52l-4.5,0.42l-4.38,1.84l-3.74,0.97l-1.3,-2.46l-2.36,-1.68l0.53,-4.89l-1.2,-4.86l1.14,-3.04l2.23,-3.46l5.68,-6.22l1.8,-1.58l-0.4,-2.8l-3.4,-2.81l-0.79,-2.25l-0.16,-10.13l-7.02,-7.77l0.96,-1.19l2.47,3.3l3.5,-0.17l2.57,1.6l0.53,-0.09l2.46,-3.23l1.19,-5.07l3.49,-2.23l2.82,2.55l-1.01,4.77Z",
      "name": "Finland"
    },
    "FJ": {
      "path": "M869.95,457.1l-1.21,0.42l-0.08,-0.24l2.98,-1.23l-0.15,0.44l-1.54,0.62ZM867.58,459.4l0.43,0.38l-0.27,0.91l-1.24,0.29l-1.04,-0.25l-0.14,-0.69l0.64,-0.59l0.92,0.26l0.7,-0.31Z",
      "name": "Fiji"
    },
    "FK": {
      "path": "M274.37,564.69l1.48,1.33l-0.53,1.0l-2.96,1.07l-0.95,-1.2l-0.57,-0.05l-1.79,1.54l-0.79,-1.16l2.52,-2.03l1.9,0.9l0.46,-0.09l1.23,-1.32Z",
      "name": "Falkland Is."
    },
    "NI": {
      "path": "M202.32,382.47l0.82,-0.18l1.03,-1.02l-0.04,-0.89l0.68,-0.0l0.63,-0.54l0.97,0.23l1.53,-1.28l0.58,-1.0l1.17,0.35l2.41,-0.95l0.13,1.34l-0.81,1.96l0.1,2.77l-0.36,0.38l-0.11,1.76l-0.47,0.81l0.18,1.15l-1.73,-0.86l-0.71,0.27l-1.47,-0.6l-0.52,0.16l-4.02,-3.85Z",
      "name": "Nicaragua"
    },
    "NL": {
      "path": "M430.16,264.22l0.76,-0.72l2.14,-5.88l3.19,-1.63l1.7,0.1l0.35,1.07l-0.6,3.64l-0.51,1.24l-1.24,0.0l-0.4,0.44l0.34,3.35l-2.18,-2.14l-0.43,-0.11l-2.22,0.8l-0.89,-0.15Z",
      "name": "Netherlands"
    },
    "NO": {
      "path": "M491.42,157.32l7.17,5.11l-2.71,1.67l-0.13,0.55l2.55,4.24l-3.9,2.61l-1.31,0.42l0.79,-4.7l-3.21,-2.91l-0.48,-0.04l-4.06,2.73l-1.21,5.15l-2.11,2.72l-2.64,-1.54l-3.04,0.32l-2.65,-3.53l-0.63,-0.01l-1.41,1.75l-1.41,0.26l-0.33,0.36l-0.33,4.08l-4.27,-0.99l-0.48,0.32l-0.6,3.44l-2.07,-0.02l-0.38,0.27l-4.15,11.7l-3.88,8.48l0.84,2.18l-0.71,1.86l-2.2,-0.09l-0.4,0.28l-1.64,5.41l0.15,7.19l1.58,2.74l-0.8,5.79l-2.04,3.34l-0.83,2.09l-1.27,-2.26l-0.65,-0.07l-4.87,5.52l-3.05,1.02l-3.16,-2.22l-0.86,-5.06l-0.78,-11.7l2.19,-3.29l6.55,-4.59l5.02,-5.96l4.64,-8.4l6.0,-12.26l11.0,-13.83l5.32,-3.11l3.99,0.38l0.38,-0.19l3.69,-6.04l4.48,0.3l4.3,-1.47ZM484.42,59.58l4.68,4.94l-3.51,7.19l-6.97,1.55l-7.03,-2.18l-0.42,-3.6l-0.37,-0.35l-3.35,-0.23l-2.51,-6.12l7.16,-3.9l3.42,3.43l0.63,-0.09l2.33,-4.19l5.93,3.56ZM482.22,93.35l-4.99,4.27l-3.84,-2.35l1.56,-3.06l-1.38,-3.53l4.4,-2.11l0.89,4.13l3.36,2.65ZM466.32,69.71l8.02,9.81l-6.13,5.05l-1.37,8.88l-2.22,2.36l-1.15,9.08l-2.49,0.35l-5.08,-6.44l2.14,-3.9l-0.08,-0.49l-3.69,-3.4l-4.82,-10.44l-1.89,-10.23l6.16,-4.58l1.22,4.4l0.41,0.29l3.57,-0.19l0.37,-0.32l0.9,-4.57l3.14,-0.43l3.02,4.76Z",
      "name": "Norway"
    },
    "NA": {
      "path": "M474.4,460.84l-1.11,0.05l-0.38,0.4l-0.07,9.11l-2.09,0.08l-0.38,0.4l-0.0,18.09l-1.98,1.29l-1.16,0.18l-2.43,-0.69l-0.48,-1.18l-0.99,-0.78l-0.55,0.05l-0.9,1.05l-1.52,-1.75l-0.94,-1.97l-1.99,-8.9l-0.06,-3.23l-0.33,-1.56l-2.3,-3.43l-1.91,-4.94l-1.96,-2.48l-0.12,-1.61l2.33,-0.8l1.43,0.07l1.82,1.15l10.23,-0.26l1.84,1.26l6.01,0.37ZM474.58,460.83l6.59,-1.65l1.91,0.41l-1.71,0.41l-1.31,0.85l-1.12,-0.95l-4.36,0.94Z",
      "name": "Namibia"
    },
    "VU": {
      "path": "M839.03,452.86l0.23,1.16l-0.44,0.03l-0.2,-1.47l0.42,0.28Z",
      "name": "Vanuatu"
    },
    "NC": {
      "path": "M838.79,471.67l-0.34,0.23l-2.9,-1.8l-3.27,-3.48l1.65,0.85l4.86,4.19Z",
      "name": "New Caledonia"
    },
    "NE": {
      "path": "M454.74,355.83l1.33,1.41l0.49,0.07l1.26,-0.72l0.53,3.62l0.94,0.85l0.17,0.94l0.82,0.72l-0.45,0.98l-0.96,5.37l-0.13,3.28l-3.05,2.34l-1.22,3.61l1.02,1.25l-0.0,1.48l0.39,0.4l1.13,0.04l-0.1,0.49l-0.45,0.09l-0.35,0.68l-1.47,-2.44l-0.86,-0.29l-2.09,1.38l-1.73,-0.67l-1.45,-0.17l-0.85,0.35l-1.36,-0.07l-1.64,1.1l-1.06,0.05l-2.94,-1.29l-1.44,0.59l-1.01,-0.03l-0.97,-0.95l-2.7,-0.99l-2.69,0.31l-0.87,0.65l-0.46,1.62l-0.74,1.17l-0.12,1.55l-1.57,-1.1l-1.31,0.24l0.03,-0.82l-0.32,-0.41l-2.59,-0.52l-0.15,-1.17l-1.36,-1.62l-0.29,-1.01l0.13,-0.85l1.29,-0.08l1.08,-0.93l3.31,-0.22l2.22,-0.41l0.32,-0.34l0.2,-1.5l1.39,-1.91l-0.01,-5.78l3.37,-1.15l7.24,-5.24l8.41,-5.07l3.69,1.09Z",
      "name": "Niger"
    },
    "NG": {
      "path": "M456.32,383.7l0.64,0.66l-0.28,1.06l-2.11,2.02l-2.03,5.2l-1.37,1.16l-1.15,3.19l-1.33,0.66l-1.46,-0.97l-1.21,0.16l-1.38,1.37l-0.91,0.24l-1.79,4.07l-2.33,0.81l-1.11,-0.07l-0.86,0.51l-1.71,-0.05l-1.19,-1.39l-0.89,-1.9l-1.77,-1.66l-3.95,-0.08l0.07,-5.23l0.42,-1.44l1.95,-2.32l-0.14,-0.91l0.43,-1.18l-0.53,-1.42l0.25,-2.95l0.72,-1.08l0.32,-1.35l0.46,-0.39l2.47,-0.28l2.34,0.89l1.15,1.03l1.28,0.04l1.22,-0.59l3.03,1.28l1.5,-0.14l1.36,-1.01l1.32,0.07l0.82,-0.35l3.45,0.81l1.82,-1.34l1.84,2.7l0.66,0.16Z",
      "name": "Nigeria"
    },
    "NZ": {
      "path": "M857.8,512.11l1.85,3.38l0.45,0.2l0.3,-0.38l0.03,-1.36l0.38,0.29l0.56,2.51l2.02,1.03l1.81,0.29l1.59,-1.16l0.7,0.2l-1.16,4.01l-1.98,0.12l-0.73,1.27l0.21,1.25l-2.44,4.45l-1.47,1.02l-0.42,-0.65l-0.66,-0.3l1.25,-2.35l-0.81,-2.16l-2.64,-1.38l0.04,-0.7l1.82,-1.29l0.42,-2.46l-0.15,-2.29l-0.96,-2.0l-0.05,-0.75l-3.11,-3.94l-0.82,-1.69l1.57,1.56l1.76,0.72l0.66,2.55ZM853.83,527.42l0.57,1.38l0.61,0.17l1.4,-1.06l0.46,0.9l0.0,1.2l-2.48,3.93l-1.26,1.36l-0.06,0.47l0.6,1.08l-1.47,0.09l-2.32,1.54l-2.04,5.78l-3.02,2.49l-2.03,-0.07l-1.72,-1.2l-2.46,-0.23l-0.29,-0.92l1.25,-2.46l3.05,-3.36l1.62,-0.67l4.01,-3.18l1.56,-1.87l1.08,-2.44l1.01,-1.01l0.35,-1.73l1.23,-1.07l0.35,0.88Z",
      "name": "New Zealand"
    },
    "NP": {
      "path": "M641.15,342.42l-0.0,3.36l-1.74,0.04l-4.8,-0.9l-1.59,-1.45l-3.36,-0.36l-7.66,-3.88l0.81,-2.23l2.33,-1.79l1.77,0.78l2.49,1.85l1.38,0.43l0.99,1.42l1.89,0.55l1.99,1.22l5.5,0.95Z",
      "name": "Nepal"
    },
    "XK": {
      "path": "M472.78,298.18l-1.1,-1.47l0.98,-0.9l0.29,-0.94l2.0,1.84l-0.4,0.85l-1.77,0.62Z",
      "name": "Kosovo"
    },
    "CI": {
      "path": "M407.4,389.11l0.86,0.42l0.56,0.9l1.13,0.54l1.19,-0.61l0.97,-0.08l1.42,0.54l0.6,3.25l-1.03,2.09l-0.65,2.85l1.06,2.33l-0.06,0.53l-2.54,-0.47l-1.66,0.03l-3.06,0.47l-4.11,1.61l0.32,-3.06l-1.18,-1.31l-1.32,-0.67l0.42,-0.86l-0.2,-1.4l0.5,-0.68l0.01,-1.59l0.84,-0.33l0.26,-0.5l-1.15,-3.02l0.12,-0.51l0.51,-0.25l0.66,0.31l1.93,0.02l0.67,-0.72l0.71,-0.14l0.25,0.7l0.57,0.22l1.4,-0.61Z",
      "name": "C\u00f4te d'Ivoire"
    },
    "CH": {
      "path": "M444.61,279.47l-0.29,1.12l0.16,0.5l1.13,0.67l1.03,0.12l-0.12,0.88l-0.79,0.44l-1.7,-0.42l-0.47,0.25l-0.46,1.23l-0.72,0.07l-0.3,-0.39l-0.58,-0.06l-1.31,1.14l-0.93,0.13l-0.87,-0.62l-0.82,-1.51l-0.52,-0.17l-0.61,0.29l0.02,-0.85l1.73,-1.95l0.07,-0.65l0.96,0.08l0.57,-0.53l1.97,0.02l0.67,-0.71l2.16,0.92Z",
      "name": "Switzerland"
    },
    "CO": {
      "path": "M242.07,384.75l-1.7,0.59l-0.59,1.19l-1.7,1.7l-0.37,1.94l-0.67,1.44l0.31,0.57l1.03,0.14l0.25,0.91l0.57,0.65l-0.04,2.35l1.64,1.42l3.16,-0.24l1.26,0.28l1.67,2.06l0.41,0.13l4.09,-0.39l0.45,0.22l-0.92,1.95l-0.2,1.8l0.52,1.83l0.75,1.05l-1.12,1.1l0.07,0.63l0.84,0.51l0.74,1.3l-0.39,-0.45l-0.59,-0.01l-0.71,0.74l-4.71,-0.05l-0.4,0.41l0.03,1.57l0.33,0.39l1.11,0.2l-1.68,0.4l-0.29,0.38l-0.01,1.82l1.16,1.14l0.34,1.25l-1.05,7.05l-1.04,-0.87l1.26,-1.99l-0.13,-0.56l-2.18,-1.23l-1.38,0.2l-1.14,-0.38l-1.27,0.61l-1.55,-0.26l-1.38,-2.46l-1.23,-0.75l-0.85,-1.2l-1.67,-1.19l-0.86,0.13l-2.11,-1.32l-1.01,0.31l-1.8,-0.29l-0.52,-0.91l-3.09,-1.68l0.77,-0.52l-0.1,-1.12l0.41,-0.64l1.34,-0.32l2.0,-2.88l-0.11,-0.57l-0.67,-0.43l0.39,-1.38l-0.52,-2.11l0.49,-0.83l-0.4,-2.13l-0.97,-1.36l0.17,-0.67l0.86,-0.08l0.47,-0.75l-0.46,-1.63l1.41,-0.07l1.8,-1.7l0.93,-0.24l0.3,-0.38l0.45,-2.78l1.22,-1.01l1.44,-0.04l0.45,-0.5l1.91,0.12l2.93,-1.85l1.15,-1.15l0.91,0.47l-0.26,0.45Z",
      "name": "Colombia"
    },
    "CN": {
      "path": "M740.22,270.81l4.55,1.5l2.81,2.58l0.98,3.43l0.38,0.29l3.8,0.0l2.34,-1.51l3.31,-0.89l-1.01,2.59l-1.01,1.46l-0.85,3.95l-1.53,3.16l-2.73,-0.57l-2.43,1.3l-0.19,0.43l0.65,2.95l-0.32,3.68l-0.94,0.07l-0.37,0.4l0.01,0.58l-0.89,-1.11l-0.67,0.07l-0.92,1.77l-3.72,1.4l-0.25,0.46l0.28,1.25l-1.5,-0.08l-1.08,-0.96l-0.59,0.06l-1.68,2.31l-2.7,1.74l-2.03,2.08l-3.39,0.92l-1.93,1.54l-1.22,0.4l0.42,-0.81l-0.43,-1.03l1.81,-2.01l0.02,-0.51l-1.32,-1.73l-0.51,-0.11l-2.25,1.21l-2.83,2.28l-1.5,2.02l-2.27,0.14l-1.56,1.64l-0.04,0.47l1.32,2.16l2.01,0.63l0.3,1.47l1.98,0.92l0.42,-0.05l2.6,-2.09l1.99,1.1l1.5,0.12l0.24,0.97l-3.39,0.94l-1.12,1.61l-2.5,1.64l-1.29,2.15l0.13,0.55l2.57,1.6l0.97,2.9l3.17,4.94l-0.03,1.8l-1.36,0.69l-0.19,0.5l0.6,1.55l1.41,0.95l-0.9,4.05l-1.43,0.4l-3.85,6.72l-2.28,3.23l-6.78,4.72l-2.73,0.3l-1.45,1.07l-0.61,-0.62l-0.56,-0.01l-1.36,1.29l-3.39,1.31l-2.61,0.41l-1.1,2.86l-0.81,0.09l-0.5,-1.47l0.5,-0.88l-0.25,-0.59l-3.36,-0.86l-1.3,0.41l-2.3,-0.64l-0.95,-0.87l0.34,-1.33l-0.3,-0.49l-2.19,-0.48l-1.13,-0.96l-0.48,-0.03l-2.06,1.4l-4.28,0.28l-2.76,1.08l-0.28,0.43l0.32,2.61l-0.59,-0.03l-0.19,-1.39l-0.56,-0.34l-1.67,0.72l-2.47,-1.26l0.63,-1.94l-0.25,-0.5l-1.37,-0.46l-0.55,-2.3l-0.46,-0.3l-2.13,0.37l0.24,-2.6l2.39,-2.48l0.03,-4.49l-1.19,-0.94l-0.79,-1.57l-0.41,-0.22l-1.4,0.2l-2.0,-0.32l0.48,-1.12l-1.17,-1.78l-0.56,-0.11l-1.62,1.1l-2.25,-0.6l-2.89,1.82l-2.25,2.08l-1.74,0.31l-1.17,-0.74l-3.32,-0.68l-1.48,0.83l-1.04,1.32l-0.12,-1.23l-0.54,-0.34l-1.44,0.56l-5.54,-0.9l-1.98,-1.22l-1.89,-0.56l-0.99,-1.42l-1.34,-0.39l-2.55,-1.88l-2.01,-0.89l-1.21,0.59l-5.57,-3.64l-0.54,-2.5l1.19,0.26l0.49,-0.37l0.08,-1.52l-0.98,-1.65l0.16,-2.6l-2.69,-3.58l-4.12,-1.33l-0.68,-2.18l-1.91,-1.6l-0.38,-0.78l-0.5,-3.27l-1.52,-0.73l-0.7,0.14l-0.49,-2.31l0.57,-0.59l-0.13,-0.89l2.06,-1.34l1.59,-0.59l2.55,0.42l0.43,-0.23l0.85,-1.9l2.99,-0.37l1.11,-1.41l4.04,-1.97l0.39,-0.97l-0.17,-1.67l1.48,-0.77l0.19,-0.49l-2.1,-5.65l4.54,-1.3l1.38,-0.84l1.88,-6.37l4.59,1.12l0.4,-0.13l1.49,-1.91l0.11,-3.42l2.01,-0.45l1.83,-2.43l0.45,-0.15l0.67,2.44l2.23,2.08l3.44,1.35l1.58,2.72l-0.93,4.08l0.95,1.84l6.54,1.28l2.95,2.14l1.48,0.4l1.07,3.0l1.52,2.13l3.06,0.09l5.13,0.76l3.38,-0.46l2.34,0.48l3.65,2.02l3.07,0.05l0.99,0.93l0.48,0.05l2.87,-1.78l3.94,-1.15l3.84,-0.16l3.06,-1.29l1.77,-1.81l1.72,-1.14l0.16,-0.47l-1.12,-2.36l1.05,-1.82l4.03,0.9l2.45,-1.85l3.76,-1.36l1.97,-2.46l1.63,-0.96l3.49,-0.47l1.91,0.4l0.47,-0.31l0.18,-1.65l-2.27,-2.59l-2.11,-1.27l-0.44,0.02l-1.78,1.27l-2.29,-0.54l-1.28,0.37l-0.43,-1.02l2.76,-6.16l3.03,1.25l3.53,-2.45l0.15,-1.96l2.18,-4.08l1.47,-1.55l-0.03,-2.26l-1.16,-1.03l1.66,-1.66l2.96,-0.72l3.21,-0.11l3.62,1.21l2.05,1.43l3.31,8.17l0.92,3.82ZM696.92,366.89l-1.87,1.1l-1.63,-0.65l-0.06,-1.84l1.03,-1.01l2.58,-0.7l1.15,0.05l0.31,0.56l-0.98,1.09l-0.53,1.4Z",
      "name": "China"
    },
    "CM": {
      "path": "M457.92,387.33l1.06,1.92l-1.4,0.16l-1.05,-0.23l-0.45,0.23l-0.54,1.2l0.08,0.45l1.48,1.48l1.05,0.45l1.01,2.47l-1.52,3.0l-0.68,0.68l-0.13,3.69l2.38,3.84l1.09,0.8l0.24,2.48l-3.67,-1.14l-11.27,-0.13l0.23,-1.79l-0.98,-1.66l-1.19,-0.54l-0.44,-0.97l-0.6,-0.42l1.71,-4.28l0.75,-0.13l1.38,-1.37l0.65,-0.03l1.71,0.99l1.93,-1.12l1.14,-3.2l1.38,-1.17l2.0,-5.16l2.17,-2.15l0.3,-1.65l-0.86,-0.89l0.18,-0.37l0.8,1.32l0.07,3.24Z",
      "name": "Cameroon"
    },
    "CL": {
      "path": "M246.67,568.71l-3.34,2.4l-0.55,3.89l-0.62,0.06l-2.66,-1.3l-2.82,-2.86l-3.06,-2.32l-0.71,-2.33l0.65,-2.52l-1.22,-2.56l-0.31,-6.43l1.02,-3.46l2.58,-2.79l-0.19,-0.66l-3.24,-0.91l2.11,-2.91l0.78,-5.35l2.3,1.02l0.56,-0.29l1.31,-7.14l-0.2,-0.42l-1.68,-0.9l-0.58,0.28l-0.7,3.81l-0.82,-0.25l1.58,-10.59l1.15,-2.43l-0.71,-3.1l-0.18,-3.15l1.02,-0.35l3.26,-9.88l1.07,-4.5l-0.56,-4.47l0.74,-2.47l-0.29,-3.45l1.46,-3.5l2.04,-17.19l-0.67,-7.94l1.04,-0.54l0.54,-0.92l0.79,1.16l0.32,1.82l1.25,1.19l-0.69,2.61l1.33,2.98l0.97,3.7l0.47,0.29l1.49,-0.31l0.11,0.25l-0.77,2.53l-2.57,1.28l-0.22,0.37l0.08,4.51l-0.47,0.8l0.58,1.25l-1.59,1.59l-1.68,2.74l-0.89,2.6l0.21,2.85l-1.49,2.9l1.12,5.38l0.64,0.64l-0.01,2.49l-1.39,2.89l0.02,2.59l-1.89,2.18l0.02,2.98l0.7,2.85l-1.44,1.23l-1.26,6.27l0.39,3.95l-0.98,0.94l0.58,3.94l1.04,1.3l-0.69,1.22l0.14,0.54l1.01,0.61l0.18,0.88l-1.04,0.92l0.26,2.03l-0.89,4.69l-1.31,3.11l0.25,2.01l-0.73,2.21l-1.97,1.93l0.28,4.31l0.88,1.43l1.6,0.0l-0.01,2.68l1.04,2.36l6.16,0.76ZM248.69,570.67l0.0,9.15l0.4,0.4l3.58,0.07l-0.53,1.14l-1.93,1.23l-2.45,-0.46l-1.9,-1.34l-2.54,-0.61l-5.59,-4.63l-2.57,-3.5l4.23,3.11l3.32,1.53l0.5,-0.14l1.29,-1.95l0.83,-2.85l2.04,-1.51l1.3,0.35Z",
      "name": "Chile"
    },
    "XC": {
      "path": "M504.86,320.38l0.39,0.01l0.27,-0.07l-0.3,0.35l-0.36,-0.28Z",
      "name": "N. Cyprus"
    },
    "CA": {
      "path": "M280.04,266.9l-1.66,3.44l0.11,0.49l0.5,-0.0l1.44,-1.15l1.05,0.52l-0.63,0.98l0.16,0.58l2.22,1.06l1.38,-0.83l1.97,0.93l-0.68,2.46l0.52,0.48l1.3,-0.48l0.99,3.78l-0.93,2.87l-0.77,0.09l-1.25,-0.52l0.49,-2.7l-0.87,-0.87l-0.52,0.06l-2.77,3.06l-0.43,-0.04l1.14,-1.12l-0.14,-0.66l-2.4,-0.9l-7.4,0.09l-0.2,-0.58l1.35,-1.14l0.02,-0.6l-0.8,-0.75l1.91,-2.12l2.57,-6.16l1.48,-2.16l1.98,-1.26l0.5,0.08l-1.6,3.09ZM68.32,168.48l4.07,1.51l3.89,3.35l2.78,0.73l0.42,-0.15l2.16,-2.88l2.84,-2.09l3.89,0.75l3.71,-3.14l3.71,-1.66l1.54,2.72l0.62,0.1l1.99,-1.93l0.48,-2.97l1.15,0.53l4.17,6.45l0.67,0.01l2.68,-3.95l0.27,4.33l0.54,0.35l3.08,-1.17l1.05,-2.04l2.63,0.36l3.83,3.0l5.86,2.58l3.48,1.19l2.44,-0.39l2.95,3.04l-3.23,3.06l0.16,0.67l4.53,1.42l6.92,-0.76l1.96,-1.04l2.47,3.65l0.64,0.03l2.72,-3.33l-0.01,-0.52l-2.34,-2.61l1.33,-1.93l2.87,-0.3l1.88,-0.64l1.8,1.47l2.48,3.63l0.41,0.17l2.63,-0.5l4.62,2.96l3.83,-1.03l3.59,0.16l0.42,-0.43l-0.27,-3.92l1.8,-0.96l3.49,2.08l-0.01,6.03l0.34,0.4l0.44,-0.28l1.5,-4.95l1.69,0.15l0.43,-0.33l1.13,-6.89l-2.74,-4.66l-2.86,-2.89l0.19,-8.09l2.75,-5.34l2.86,1.11l2.44,3.36l3.31,8.33l-2.12,3.42l0.22,0.59l4.38,1.37l-0.01,6.85l0.29,0.39l0.45,-0.18l3.02,-4.91l2.56,3.84l-0.68,5.11l2.42,4.42l0.7,0.0l2.61,-4.74l1.86,-5.93l0.15,-7.44l3.08,0.48l3.57,1.03l3.18,3.35l0.14,3.2l-1.81,3.53l1.71,3.82l-0.29,2.9l-4.72,4.27l-3.21,0.89l-2.43,-1.77l-0.62,0.23l-0.74,3.09l-2.4,5.08l-0.73,2.58l-2.76,3.73l-3.68,0.5l-2.07,2.63l-0.15,3.32l-2.86,0.78l-3.1,4.45l-2.74,5.98l-0.98,4.09l-0.14,5.74l0.31,0.4l3.44,0.75l2.25,7.78l0.48,0.26l3.37,-0.88l4.49,1.92l2.43,1.68l1.92,2.2l3.09,1.21l2.61,1.84l6.65,0.69l-0.36,3.49l0.8,4.33l1.81,4.63l3.81,3.97l0.51,0.05l2.08,-1.51l1.37,-4.39l-1.31,-6.63l-1.54,-2.05l3.69,-1.91l2.84,-3.1l1.49,-3.43l-0.24,-3.19l-1.7,-3.97l-2.92,-3.49l2.86,-5.19l-1.09,-4.55l-0.81,-7.95l1.39,-0.99l4.1,1.4l2.62,0.54l2.14,-1.31l5.09,4.62l1.07,2.2l4.09,0.36l-0.06,3.98l0.83,6.25l2.42,1.04l1.74,2.7l0.57,0.11l3.63,-2.66l2.51,-5.54l1.22,-1.73l7.63,15.44l-0.95,2.7l0.14,0.45l3.3,2.51l2.23,2.5l4.1,1.23l1.45,1.25l0.96,3.51l2.08,0.8l0.87,1.37l0.17,4.34l-3.4,2.77l-4.22,1.5l-3.06,3.15l-4.04,0.61l-5.35,-0.82l-6.4,0.25l-2.32,2.87l-3.25,1.78l-6.48,8.38l-0.03,0.47l0.45,0.17l2.33,-0.73l3.98,-4.83l5.12,-3.08l3.49,-0.36l1.77,1.49l-2.18,2.58l0.8,4.03l1.01,2.99l3.5,1.85l4.14,-0.52l2.14,-3.2l0.24,1.68l1.22,0.99l-2.64,2.0l-5.49,2.09l-2.54,1.45l-2.73,2.43l-1.38,-0.18l-0.08,-2.39l4.16,-2.8l0.16,-0.45l-0.39,-0.28l-4.01,0.12l-2.61,0.4l-1.4,-1.73l-0.12,-5.1l-1.11,-1.06l-1.83,0.44l-0.65,-0.76l-0.63,0.03l-1.91,2.77l-0.81,2.9l-0.81,1.48l-1.66,0.64l-0.47,0.87l-8.32,0.08l-1.21,0.71l-2.33,2.23l-0.72,-0.14l-1.36,1.08l-1.12,-0.54l-4.75,1.43l-0.9,1.32l0.21,0.59l1.7,0.22l0.05,0.22l-1.84,0.36l-1.85,0.9l-1.19,-0.29l-0.92,0.15l-2.95,2.0l-0.71,-0.11l0.32,-0.68l1.12,-1.78l1.72,-1.33l0.09,-2.6l1.16,-2.28l0.48,0.59l2.03,0.48l0.42,-0.16l0.82,-1.6l-2.66,-4.02l-2.29,-0.71l-5.63,-0.81l-0.4,-0.66l-0.86,0.2l0.27,-0.64l-0.21,-0.52l-0.72,-0.32l0.32,-1.06l-0.91,-1.28l0.34,-0.82l-0.29,-0.55l-2.6,-0.52l-0.76,-1.93l-0.95,-0.76l-1.67,-0.09l-2.67,-0.67l-1.13,1.4l-1.48,0.69l-0.85,1.24l-2.8,-0.89l-2.1,0.45l-2.38,-1.13l-4.23,-0.83l-0.58,-0.48l-0.42,-1.96l-0.4,-0.32l-0.85,0.02l-0.39,0.4l-0.01,1.07l-69.11,-0.01l-6.5,-5.37l-4.5,-1.66l-1.29,-3.28l0.34,-2.39l-0.2,-0.41l-3.03,-1.66l-0.52,-3.39l-2.92,-2.97l-0.05,-1.94l1.39,-2.23l-0.07,-2.8l-4.34,-3.13l-4.08,-8.55l-4.01,-4.22l-1.31,-2.51l-0.57,-0.15l-2.51,1.6l-2.18,2.42l-3.81,-5.1l-2.44,-1.39l-2.26,-0.18l0.03,-55.45ZM265.75,272.87l-0.72,0.04l-3.11,-1.15l-1.72,-1.35l3.19,0.89l2.36,1.57ZM249.33,12.09l6.65,1.61l5.26,2.56l4.43,5.22l-0.1,4.84l-5.98,7.79l-6.13,3.67l-2.26,3.84l0.35,0.6l4.74,-0.08l-5.52,9.28l-4.14,4.52l-4.23,11.87l-5.01,2.26l-1.69,2.82l-7.4,1.42l-0.32,0.34l0.22,0.41l3.02,1.48l-1.51,2.34l2.02,6.18l-2.26,4.04l-3.94,3.58l-1.16,4.49l-3.53,3.68l0.35,2.54l0.44,0.34l3.85,-0.39l0.04,2.09l-6.37,6.12l-6.3,-2.81l-7.5,1.6l-3.7,-1.27l-4.4,-0.52l-0.28,-4.64l4.41,-2.41l0.2,-0.41l-1.19,-8.1l1.06,-0.58l6.49,4.94l0.49,-0.0l0.12,-0.48l-3.41,-7.64l-3.92,-2.37l1.85,-4.46l4.51,-3.29l0.71,-4.65l-3.55,-5.6l-0.98,-6.84l6.22,0.58l1.88,1.51l0.57,-0.08l3.91,-5.41l-0.21,-0.62l-5.64,-1.76l-8.71,0.93l-4.24,-5.03l-2.06,-6.44l-2.92,-4.92l-0.52,-5.65l3.5,-3.22l2.94,-0.62l4.91,-2.99l3.67,-6.97l2.62,0.86l2.63,5.2l0.41,0.22l0.34,-0.32l1.88,-10.36l3.17,-3.13l4.37,-2.24l7.32,-0.83l1.2,2.03l0.52,0.16l7.1,-3.49l10.71,2.64ZM203.82,140.61l1.98,5.56l0.38,0.26l0.37,-0.27l2.27,-6.74l5.84,-3.34l4.06,8.5l-0.37,5.31l0.57,0.39l4.95,-2.38l2.28,-3.11l5.2,3.94l3.34,3.74l0.31,3.32l0.54,0.34l4.32,-1.65l2.44,4.64l6.13,3.12l2.09,2.87l2.25,6.4l-4.35,3.07l-0.01,0.65l5.9,4.44l3.95,1.47l3.53,5.87l3.81,0.57l-0.69,3.91l-4.11,6.58l-2.68,-2.22l-3.9,-5.85l-0.43,-0.17l-3.24,0.78l-0.3,0.35l-0.24,3.8l2.63,3.5l3.42,2.75l0.96,1.44l1.58,5.48l-0.73,3.38l-2.67,-1.26l-6.25,-4.45l-0.52,0.05l-0.04,0.52l6.1,8.03l0.24,1.1l-6.09,-1.92l-5.3,-3.12l-2.77,-2.46l0.72,-1.31l-0.1,-0.51l-7.38,-5.75l-0.64,0.33l0.03,1.33l-6.7,0.85l-1.79,-1.68l1.46,-3.85l4.49,-0.1l5.15,-0.77l0.31,-0.54l-0.79,-2.04l0.83,-2.91l3.22,-6.15l-0.67,-3.24l-1.07,-2.43l-3.84,-3.29l-4.67,-2.18l1.24,-1.37l0.05,-0.47l-2.65,-4.44l-2.33,-0.57l-1.88,-2.37l-0.65,0.04l-1.25,2.02l-4.3,0.88l-9.0,-1.6l-5.26,-2.14l-3.98,-1.1l-1.81,-2.3l2.43,-3.26l-0.32,-0.64l-3.2,-0.03l-0.75,-7.66l1.89,-7.38l2.46,-3.41l5.58,-2.04l-1.59,4.91ZM261.18,282.95l2.07,0.7l1.54,-0.05l-0.57,0.69l-0.66,0.17l-2.92,-1.41l-0.44,-0.86l0.38,-0.46l0.61,1.23ZM230.78,185.0l-2.28,0.26l-0.54,-2.72l0.98,-3.45l1.88,-0.76l1.65,1.57l0.03,2.61l-0.24,0.76l-1.47,1.73ZM229.41,141.37l0.16,1.75l-4.89,-0.38l-2.72,1.08l-0.48,-0.34l-2.65,-4.39l0.09,-2.82l0.87,-0.43l5.47,0.92l4.14,4.61ZM222.03,214.7l-0.78,2.22l-0.56,-0.23l-0.54,-1.3l0.87,-1.54l0.57,0.07l0.44,0.77ZM183.65,102.44l3.0,3.59l4.7,-0.02l1.97,3.24l-0.41,4.19l2.83,2.3l1.84,2.54l6.99,1.27l4.2,-2.19l4.96,-0.84l3.84,0.67l2.53,3.56l0.53,3.8l-1.43,2.32l-3.48,1.88l-3.25,-1.1l-7.15,1.44l-5.04,0.16l-3.95,-1.13l-6.43,-2.95l-0.83,-5.12l-0.3,-4.98l-2.56,-4.72l-5.31,-1.46l-2.69,-3.1l0.83,-3.99l4.63,0.64ZM207.36,195.03l0.42,2.4l0.63,0.26l0.99,-0.72l1.27,1.36l5.47,3.76l0.21,2.54l0.49,0.36l1.62,-0.39l1.33,1.4l-1.71,1.36l-3.54,-1.23l-1.33,-2.43l-0.66,-0.06l-2.46,2.99l-3.05,2.47l-0.7,-2.67l-0.45,-0.29l-2.39,0.38l1.64,-2.22l0.32,-4.55l0.78,-5.03l1.13,0.31ZM215.49,211.5l-2.69,2.74l-1.33,-0.09l-0.38,-1.01l1.61,-2.18l2.82,0.04l-0.02,0.5ZM202.66,70.17l2.91,4.33l-3.3,3.83l-4.54,9.4l-4.14,0.83l-4.93,-1.5l-2.57,-4.9l0.04,-4.53l1.93,-3.49l-0.36,-0.59l-4.35,0.1l-2.61,-4.34l-1.55,-6.33l1.71,-6.55l1.67,-4.57l2.41,-1.04l0.22,-0.48l-0.96,-3.26l5.05,-0.73l3.21,8.41l8.21,6.06l1.95,9.35ZM187.39,143.67l-2.74,6.11l-2.28,-0.24l-1.49,-6.99l0.04,-4.2l1.26,-3.63l2.29,-2.28l4.96,0.3l4.35,2.01l-3.51,7.33l-2.87,1.59ZM186.12,124.07l-1.2,3.26l-3.2,-0.62l-2.75,-2.26l1.22,-4.02l3.15,-2.36l1.93,3.09l0.86,2.91ZM185.64,96.93l-0.83,0.24l-4.33,-0.68l-0.51,-2.52l4.35,0.15l1.52,1.89l-0.2,0.91ZM180.62,90.66l-3.24,2.16l-1.76,-2.41l-1.05,-4.51l-0.18,-4.75l2.69,0.43l1.32,0.77l2.85,4.19l-0.63,4.11ZM180.98,172.19l-1.22,1.91l-3.04,-1.9l-2.16,0.64l-2.93,-2.72l1.98,-2.02l1.52,-2.75l3.72,3.03l2.13,3.8ZM169.77,135.22l2.97,1.73l4.08,-1.03l0.51,2.03l-2.26,4.02l0.07,0.48l3.66,3.51l-0.43,6.97l-3.8,2.82l-2.06,-0.56l-1.71,-2.96l-6.1,-6.18l0.04,-2.04l4.64,0.95l0.44,-0.57l-2.66,-5.4l2.61,-3.78ZM174.46,107.75l1.36,3.53l0.08,5.21l-1.09,7.07l-3.71,0.89l-2.35,-1.35l0.05,-5.54l-0.47,-0.4l-3.64,0.69l-0.14,-7.04l2.56,0.16l3.62,-3.51l3.32,0.59l0.42,-0.3ZM170.01,87.71l0.84,4.38l-3.36,-1.1l-4.3,-4.01l-4.91,-0.41l2.06,-3.18l-0.05,-0.5l-2.92,-2.99l-0.16,-4.33l4.31,1.6l6.62,4.67l1.87,5.86ZM134.6,141.21l-1.16,3.7l0.55,0.48l5.29,-2.43l3.29,4.01l0.64,-0.03l2.53,-3.85l1.89,2.29l2.03,7.94l0.37,0.3l0.4,-0.26l1.28,-3.56l-1.72,-8.28l1.76,-1.01l2.22,1.24l2.69,3.29l2.45,13.62l8.57,7.16l-0.23,2.66l-3.8,0.53l-0.29,0.6l1.51,2.57l-0.67,2.03l-4.14,-1.0l-4.49,-1.91l-3.03,0.47l-4.65,2.34l-10.43,1.63l-1.41,-3.17l-3.42,-1.92l-2.23,0.65l-2.72,-5.01l5.02,-1.82l3.63,0.3l3.27,-1.29l0.25,-0.38l-0.26,-0.37l-4.84,-1.75l-5.5,0.57l-3.28,-0.14l-1.06,-2.23l5.47,-2.91l0.2,-0.46l-0.4,-0.3l-3.77,0.11l-3.96,-1.88l1.97,-5.68l1.69,-3.21l6.41,-4.99l2.07,1.35ZM158.82,138.54l-1.83,4.71l-3.34,-5.15l0.6,-0.86l2.98,-0.32l1.59,1.62ZM149.59,111.85l0.99,3.73l0.63,0.21l2.09,-1.62l2.15,0.37l0.41,4.59l-1.42,4.36l-8.24,1.45l-6.38,4.09l-3.32,0.18l-0.26,-2.47l5.03,-4.13l0.12,-0.46l-0.41,-0.24l-11.2,1.15l-3.08,-1.54l3.28,-9.52l2.11,-2.66l6.67,3.38l4.39,5.99l4.63,0.92l0.44,-0.53l-3.52,-9.7l2.01,-3.46l2.07,1.01l0.81,4.89ZM145.71,84.15l-2.55,2.05l-3.61,-0.01l0.03,-1.26l2.32,-3.45l0.99,0.43l2.82,2.24ZM144.69,94.95l-4.27,3.06l-3.27,-3.31l1.81,-3.41l3.34,-1.13l3.11,1.67l-0.73,3.12ZM118.92,155.09l-5.99,3.39l-1.29,-3.14l-5.55,-4.03l2.72,-9.3l2.17,-5.73l-2.25,-5.4l7.82,-1.34l3.61,1.91l6.24,0.5l2.31,2.51l2.44,3.4l-2.87,2.01l-6.21,6.07l-3.1,5.73l-0.05,3.42ZM129.56,96.45l-0.31,7.96l-1.8,3.53l-2.35,0.59l-4.6,4.46l-3.74,1.48l-2.92,-1.93l4.07,-7.68l5.0,-7.12l3.62,0.15l3.02,-1.45ZM111.13,275.3l-0.71,0.3l-3.83,-1.6l-0.83,-1.38l-2.13,-1.28l-0.67,-1.21l-2.4,-0.65l-0.75,-2.19l3.73,1.32l2.25,0.41l2.0,3.05l2.52,1.64l0.8,1.62ZM87.8,253.38l0.9,0.35l1.87,-0.27l-0.67,4.25l1.83,2.97l-1.42,-1.69l-0.98,-1.97l-1.19,-1.23l-0.34,-2.41Z",
      "name": "Canada"
    },
    "CG": {
      "path": "M466.72,406.37l-0.1,1.03l-1.25,2.97l-0.19,3.62l-0.46,1.78l-0.23,0.63l-1.61,1.19l-1.21,1.39l-1.09,2.43l0.04,2.09l-3.25,3.25l-0.5,-0.24l-0.5,-0.83l-1.36,-0.02l-0.98,0.89l-1.68,-0.99l-1.54,1.24l-1.52,-1.96l1.57,-1.14l0.11,-0.52l-0.77,-1.35l2.1,-0.66l0.39,-0.73l1.05,0.82l2.21,0.11l1.12,-1.37l0.37,-1.81l-0.27,-2.09l-1.13,-1.5l1.0,-2.69l-0.13,-0.45l-0.92,-0.58l-1.6,0.17l-0.51,-0.94l0.1,-0.61l2.75,0.09l3.97,1.24l0.51,-0.33l0.17,-1.28l1.24,-2.21l1.28,-1.14l2.76,0.49Z",
      "name": "Congo"
    },
    "CF": {
      "path": "M461.16,408.1l-0.26,-1.19l-1.09,-0.77l-0.84,-1.18l-0.29,-1.0l-1.04,-1.15l0.08,-3.44l0.58,-0.49l1.16,-2.36l1.85,-0.17l0.61,-0.62l0.97,0.58l3.15,-0.97l2.48,-1.92l0.02,-0.96l2.82,0.02l2.36,-1.18l1.93,-2.86l1.16,-0.94l1.11,-0.31l0.27,0.87l1.34,1.48l-0.39,2.02l0.3,1.01l4.01,2.76l0.17,0.93l2.63,2.31l0.6,1.44l2.08,1.4l-3.84,-0.21l-1.94,0.88l-1.24,-0.49l-2.67,1.2l-1.29,-0.18l-0.51,0.37l-0.6,1.22l-3.35,-0.65l-1.57,-0.91l-2.42,-0.83l-1.45,0.91l-0.97,1.28l-0.26,1.56l-3.22,-0.43l-1.49,1.33l-0.94,1.62Z",
      "name": "Central African Rep."
    },
    "CD": {
      "path": "M487.01,402.27l2.34,-0.14l1.35,1.84l1.34,0.45l0.86,-0.39l1.21,0.12l1.07,-0.41l0.54,0.89l2.04,1.54l-0.14,2.72l0.7,0.54l-1.38,1.13l-1.53,2.54l-0.17,2.05l-0.59,1.08l-0.02,1.72l-0.72,0.84l-0.66,3.01l0.63,1.32l-0.44,4.26l0.64,1.47l-0.37,1.22l0.86,1.8l1.53,1.42l0.3,1.27l0.44,0.51l-4.08,0.75l-0.92,1.82l0.51,1.35l-0.74,5.46l0.17,0.38l2.45,1.47l0.54,-0.1l0.12,1.64l-1.28,-0.01l-1.85,-2.37l-1.94,-0.45l-0.48,-1.14l-0.56,-0.2l-1.41,0.74l-1.71,-0.3l-1.01,-1.19l-2.49,-0.2l-0.44,-0.77l-1.98,-0.21l-2.88,0.36l0.11,-2.42l-0.85,-1.13l-0.16,-1.36l0.32,-1.74l-0.47,-0.89l-0.04,-1.5l-0.4,-0.39l-2.53,0.02l0.1,-0.41l-0.39,-0.49l-1.28,0.01l-0.43,0.46l-1.62,0.32l-0.83,1.8l-1.09,-0.28l-2.4,0.52l-1.37,-1.91l-1.3,-3.31l-0.38,-0.27l-7.39,-0.03l-2.46,0.42l0.5,-0.45l0.37,-1.47l0.66,-0.38l0.92,0.08l0.73,-0.82l0.87,0.02l0.31,0.68l1.4,0.36l3.59,-3.63l0.01,-2.23l1.02,-2.29l2.69,-2.39l0.43,-0.99l0.49,-1.96l0.17,-3.51l1.25,-2.95l0.36,-3.15l0.86,-1.13l1.1,-0.67l3.57,1.73l3.65,0.73l0.46,-0.21l0.8,-1.46l1.24,0.19l2.61,-1.17l0.81,0.44l1.04,-0.03l0.59,-0.66l0.7,-0.16l1.81,0.25Z",
      "name": "Dem. Rep. Congo"
    },
    "CZ": {
      "path": "M458.44,265.89l1.23,1.2l1.49,0.27l0.09,1.1l1.36,0.81l0.58,-0.21l0.25,-0.67l1.12,0.29l0.53,1.3l1.67,0.21l0.69,1.14l-1.4,1.19l-0.12,0.65l-0.55,0.55l-1.59,0.21l-0.56,0.65l-1.03,-0.52l-1.03,0.17l-2.15,-1.12l-1.05,0.4l-1.18,1.3l-1.53,-1.0l-2.59,-2.49l-0.57,-2.36l1.48,-0.7l0.99,-1.01l1.72,-0.74l0.54,-0.59l0.73,0.29l0.87,-0.32Z",
      "name": "Czech Rep."
    },
    "CY": {
      "path": "M504.35,321.02l0.49,0.34l-1.34,0.65l-0.91,-0.29l-0.26,-0.55l2.02,-0.14Z",
      "name": "Cyprus"
    },
    "CR": {
      "path": "M211.34,387.89l0.48,1.0l1.61,1.62l-0.54,0.45l0.3,1.42l-0.25,1.2l-1.09,-0.6l-0.05,-1.25l-2.46,-1.43l-0.28,-0.77l-0.66,-0.45l-0.45,-0.0l-0.11,1.05l-1.32,-0.95l0.31,-1.31l-0.36,-0.6l0.31,-0.27l1.42,0.58l1.29,-0.14l0.56,0.56l0.74,0.17l0.55,-0.27Z",
      "name": "Costa Rica"
    },
    "CU": {
      "path": "M221.21,356.57l1.27,1.05l2.18,-0.29l4.43,3.42l2.09,0.45l-0.1,0.41l0.36,0.49l1.75,0.1l1.44,0.97l-3.07,0.42l-4.17,-0.03l0.79,-0.7l-0.04,-0.63l-1.2,-0.76l-1.49,-0.16l-0.7,-0.62l-0.56,-1.44l-0.4,-0.25l-1.34,0.1l-2.2,-0.68l-0.89,-0.6l-3.18,-0.41l-0.28,-0.17l0.6,-0.76l-0.36,-0.29l-2.73,-0.05l-1.7,1.33l-0.91,0.03l-0.61,0.71l-1.03,0.22l1.14,-1.35l1.01,-0.54l3.69,-1.04l3.98,0.22l2.21,0.87Z",
      "name": "Cuba"
    },
    "SZ": {
      "path": "M500.35,482.11l0.5,2.14l-0.39,0.94l-1.04,0.22l-1.23,-1.25l-0.02,-0.69l0.84,-1.65l1.34,0.28Z",
      "name": "Swaziland"
    },
    "SY": {
      "path": "M510.98,327.85l0.08,-1.44l0.55,-1.47l1.28,-1.07l0.12,-0.44l-0.41,-1.19l-1.14,-0.38l-0.19,-1.91l0.53,-1.11l1.29,-1.31l0.19,-1.27l0.6,0.24l2.61,-0.82l1.36,0.56l2.06,-0.01l2.95,-1.17l3.29,-0.29l-0.72,1.1l-1.49,1.11l0.23,2.19l-0.89,3.46l-10.14,6.13l-2.17,-0.92Z",
      "name": "Syria"
    },
    "KG": {
      "path": "M621.37,297.76l-3.91,1.98l-0.95,1.31l-3.03,0.37l-1.14,2.06l-2.35,-0.39l-2.01,0.7l-2.39,1.55l0.09,1.02l-0.42,0.44l-4.5,0.47l-3.01,-1.02l-2.38,0.19l0.12,-0.96l2.3,0.46l1.14,-0.97l1.99,0.21l3.21,-2.37l-0.03,-0.67l-2.97,-1.75l-1.95,0.72l-1.27,-0.86l1.77,-1.84l-0.12,-0.64l-0.4,-0.18l0.36,-0.95l1.35,-0.39l4.01,1.14l0.5,-0.31l0.35,-1.82l1.08,-0.54l3.4,1.37l1.14,-0.35l7.61,0.43l1.15,1.13l1.27,0.45Z",
      "name": "Kyrgyzstan"
    },
    "KE": {
      "path": "M506.26,414.59l1.87,-2.56l0.93,-2.15l-1.38,-4.08l-1.06,-1.6l2.82,-2.75l0.79,0.26l0.12,1.41l0.86,0.83l1.9,0.11l3.28,2.13l3.57,0.44l1.05,-1.12l1.96,-0.9l0.82,0.69l1.16,0.09l-1.78,2.45l0.03,9.12l1.3,1.94l-1.37,0.78l-0.67,1.03l-1.08,0.46l-0.34,1.67l-0.81,1.07l-0.45,1.55l-0.68,0.56l-3.2,-2.23l-0.35,-1.58l-8.86,-4.98l0.14,-1.6l-0.57,-1.04Z",
      "name": "Kenya"
    },
    "SS": {
      "path": "M481.71,393.21l1.07,-0.73l1.2,-3.2l1.36,-0.26l1.61,2.0l0.87,0.34l1.11,-0.41l1.5,0.07l0.57,0.53l2.49,0.0l0.44,-0.63l1.07,-0.4l0.45,-0.84l0.59,-0.33l1.9,1.34l1.6,-0.2l2.83,-3.35l-0.32,-2.23l1.6,-0.53l-0.24,1.62l0.3,1.84l1.34,1.18l0.2,1.88l0.35,0.41l0.02,1.54l-0.23,0.47l-1.42,0.25l-0.85,1.44l0.3,0.6l1.4,0.17l1.12,1.08l0.59,1.13l1.03,0.53l1.28,2.37l-4.42,3.99l-1.74,0.01l-1.89,0.55l-1.47,-0.52l-1.15,0.57l-2.96,-2.62l-1.3,0.49l-1.06,-0.15l-0.79,0.39l-0.82,-0.22l-1.8,-2.7l-1.91,-1.1l-0.66,-1.5l-2.62,-2.33l-0.18,-0.94l-2.37,-1.61Z",
      "name": "S. Sudan"
    },
    "SR": {
      "path": "M283.12,400.08l2.1,0.53l-1.08,1.95l0.2,1.72l0.93,1.49l-0.59,2.04l-0.43,0.71l-1.12,-0.42l-1.32,0.22l-0.93,-0.2l-0.46,0.26l-0.25,0.73l0.33,0.7l-0.89,-0.13l-1.39,-1.98l-0.31,-1.34l-0.97,-0.31l-0.89,-1.47l0.35,-1.61l1.45,-0.82l0.33,-1.87l2.61,0.44l0.58,-0.47l1.75,-0.16Z",
      "name": "Suriname"
    },
    "KH": {
      "path": "M689.52,379.15l0.5,1.47l-0.28,2.77l-4.0,1.87l-0.16,0.59l0.69,0.97l-2.06,0.17l-2.05,0.97l-1.82,-0.32l-0.9,-1.17l-1.23,-2.56l-0.55,-2.88l1.4,-1.87l3.01,-0.46l2.23,0.35l2.01,0.99l0.51,-0.14l0.95,-1.49l1.74,0.75Z",
      "name": "Cambodia"
    },
    "SV": {
      "path": "M195.8,379.9l1.41,-1.21l2.24,1.46l0.98,-0.27l0.44,0.21l-0.27,1.07l-1.14,-0.03l-3.65,-1.23Z",
      "name": "El Salvador"
    },
    "SK": {
      "path": "M476.87,273.43l-1.2,2.33l-2.74,-1.08l-1.05,0.4l-0.52,0.78l-3.44,0.85l-0.48,0.81l-1.74,0.38l-1.88,-1.17l-0.2,-1.03l0.4,-0.94l1.02,0.01l0.86,-0.39l1.74,-2.23l0.83,0.19l0.76,-0.39l1.06,1.14l0.49,0.08l1.33,-0.74l1.26,0.34l1.63,-0.49l1.87,1.16Z",
      "name": "Slovakia"
    },
    "KR": {
      "path": "M737.47,312.73l1.03,-0.11l0.87,-1.28l2.69,-0.35l0.32,-0.3l1.75,3.04l0.59,1.94l0.02,3.41l-0.81,1.45l-2.22,0.59l-1.92,1.21l-1.79,0.21l-0.2,-1.21l0.44,-2.44l-0.97,-2.83l1.45,-0.41l0.23,-0.6l-1.48,-2.32Z",
      "name": "Korea"
    },
    "SI": {
      "path": "M456.18,286.22l-0.51,-1.56l0.2,-1.29l1.68,0.23l1.44,-0.83l2.08,-0.09l0.62,-0.56l0.24,0.62l-1.66,0.8l-0.43,1.53l-0.67,0.28l-0.24,0.94l-1.2,-0.55l-0.54,0.09l-0.33,0.43l-0.67,-0.05Z",
      "name": "Slovenia"
    },
    "KP": {
      "path": "M736.77,312.06l-0.91,-0.45l-0.89,0.68l-1.23,-0.97l0.49,-1.01l0.5,-0.32l0.58,-2.78l-0.45,-0.8l-1.38,-0.34l-0.75,-0.55l1.69,-1.74l2.72,-1.75l1.57,-2.11l1.1,0.86l2.17,0.12l0.41,-0.49l-0.32,-1.43l3.54,-1.33l0.93,-1.56l1.03,1.28l-1.46,1.26l-0.79,1.2l0.02,2.38l-1.08,0.61l-1.41,1.55l-1.7,0.58l-1.23,1.17l-0.16,2.14l2.12,1.67l-0.16,0.33l-2.59,0.32l-1.14,1.41l-1.21,0.08Z",
      "name": "Dem. Rep. Korea"
    },
    "KW": {
      "path": "M540.8,336.41l0.38,0.92l-0.17,0.78l0.61,1.64l-0.95,0.04l-0.83,-1.35l-1.59,-0.2l1.34,-2.02l1.21,0.17Z",
      "name": "Kuwait"
    },
    "SN": {
      "path": "M390.09,377.95l0.12,1.57l0.49,1.48l0.96,0.82l0.05,1.3l-1.26,-0.19l-0.75,0.33l-1.84,-0.62l-5.84,-0.13l-2.54,0.51l-0.22,-1.04l1.78,0.04l2.01,-0.92l1.03,0.48l1.09,0.05l1.29,-0.62l0.14,-0.58l-0.51,-0.74l-1.81,0.25l-1.13,-0.64l-0.79,0.04l-0.72,0.61l-2.31,0.06l-0.92,-1.79l-0.82,-0.65l0.64,-0.36l1.81,-3.15l0.65,-0.64l1.04,0.19l1.39,-0.56l1.19,-0.02l2.72,1.39l3.03,3.53Z",
      "name": "Senegal"
    },
    "SL": {
      "path": "M394.46,393.98l-1.73,1.98l-0.58,1.34l-2.07,-1.06l-1.22,-1.26l-0.65,-2.4l1.16,-0.97l0.67,-1.18l1.21,-0.52l1.66,0.0l1.03,1.65l0.52,2.42Z",
      "name": "Sierra Leone"
    },
    "KZ": {
      "path": "M552.75,298.52l0.51,-1.47l-0.48,-1.19l-2.96,-1.32l-1.07,-2.94l-1.37,-0.98l-0.03,-0.45l1.95,0.27l0.45,-0.38l0.09,-2.29l1.75,-0.47l2.09,0.51l0.49,-0.34l0.45,-3.5l-0.45,-2.38l-0.42,-0.32l-2.41,0.17l-2.39,-0.84l-2.87,1.59l-2.15,0.7l-0.86,-0.4l0.15,-1.86l-1.6,-2.47l-2.02,-0.09l-1.83,-2.19l1.33,-2.64l-0.61,-1.04l1.66,-3.54l2.17,1.91l0.66,-0.26l0.29,-2.7l4.94,-4.15l3.67,-0.1l8.38,4.33l2.97,-1.63l3.74,-0.08l3.1,1.99l0.56,-0.13l0.6,-0.97l3.28,0.16l0.4,-0.27l0.63,-1.89l-0.15,-0.46l-3.62,-2.47l1.99,-1.65l-0.2,-1.23l2.05,-0.92l0.17,-0.58l-1.66,-2.63l0.88,-1.1l9.22,-1.46l1.35,-1.1l6.17,-1.58l2.26,-1.78l4.05,0.85l0.74,4.22l0.54,0.3l2.46,-0.98l2.8,1.27l-0.18,2.03l0.44,0.43l2.58,-0.3l4.83,-3.09l0.03,0.36l3.16,3.23l5.57,10.31l0.69,0.03l1.11,-1.75l3.11,2.07l3.78,-0.93l1.13,0.59l1.15,2.17l1.83,0.89l1.0,1.55l0.4,0.18l2.95,-0.47l1.06,1.89l-1.65,2.2l-1.92,0.33l-0.33,0.38l-0.12,3.61l-1.14,1.37l-4.73,-1.15l-0.48,0.28l-1.76,6.36l-1.1,0.68l-4.91,1.4l-0.26,0.52l2.13,5.72l-1.4,0.73l-0.08,1.73l-0.87,-0.28l-1.43,-1.27l-7.9,-0.45l-0.92,0.34l-3.74,-1.37l-1.63,0.99l-0.31,1.59l-3.7,-1.05l-1.87,0.48l-0.76,1.57l-1.35,0.6l-3.3,2.34l-1.12,2.31l-0.42,0.01l-0.93,-1.56l-2.86,-0.1l-0.45,-2.43l-0.39,-0.33l-0.81,-0.02l0.02,-3.32l-3.0,-2.52l-4.58,0.18l-2.74,0.47l-2.34,-3.04l-6.74,-4.23l-6.45,2.1l-0.28,0.38l0.1,12.31l-0.69,0.09l-1.62,-2.42l-1.83,-1.07l-3.13,0.66l-0.68,0.6Z",
      "name": "Kazakhstan"
    },
    "SA": {
      "path": "M537.53,338.97l2.0,0.25l0.91,1.39l1.49,-0.06l0.88,2.19l1.29,0.79l0.51,1.03l1.56,1.08l-0.1,1.98l0.32,0.93l1.57,2.56l0.76,0.55l0.71,-0.04l1.37,4.1l7.83,1.63l0.51,-0.29l0.77,1.29l-1.56,5.0l-7.29,2.58l-7.31,1.05l-2.34,1.19l-1.88,2.79l-0.76,0.28l-0.83,-0.79l-0.91,0.12l-2.88,-0.52l-3.5,0.25l-0.86,-0.57l-0.58,0.15l-0.66,1.29l0.16,1.12l-0.43,0.33l-0.93,-1.42l-0.33,-1.18l-1.23,-0.89l-1.27,-2.1l-0.78,-2.27l-1.73,-1.83l-1.14,-0.49l-1.54,-2.37l-0.2,-3.5l-1.44,-3.02l-1.27,-1.19l-1.33,-0.58l-1.31,-3.5l-0.77,-0.7l-0.97,-2.05l-2.8,-4.2l-1.07,-0.17l0.59,-2.85l2.75,0.31l1.08,-0.88l0.6,-0.99l1.74,-0.36l0.65,-1.08l0.72,-0.43l0.1,-0.6l-2.09,-2.45l4.42,-1.3l0.48,-0.39l2.75,0.73l3.66,2.01l7.03,5.8l4.88,0.32Z",
      "name": "Saudi Arabia"
    },
    "SE": {
      "path": "M480.3,192.35l-4.15,1.76l-2.43,4.19l0.32,3.66l-3.86,4.45l-4.93,4.95l-1.79,7.79l1.78,3.64l2.29,2.71l-2.14,5.19l-2.69,1.39l-0.95,7.87l-1.3,3.9l-2.71,-0.39l-0.43,0.25l-1.32,3.3l-2.29,0.16l-0.75,-3.94l-2.09,-5.18l-1.86,-6.56l1.04,-2.66l2.12,-3.53l0.83,-6.02l-1.6,-2.83l-0.15,-7.02l1.52,-4.93l2.18,0.09l0.39,-0.26l0.87,-2.28l-0.85,-2.14l3.83,-8.36l4.06,-11.45l2.12,0.02l0.4,-0.33l0.59,-3.35l4.31,1.0l0.49,-0.36l0.34,-4.24l1.04,-0.19l6.98,7.72l0.07,9.8l0.74,2.18Z",
      "name": "Sweden"
    },
    "SD": {
      "path": "M505.98,389.25l-0.34,-0.78l-1.17,-0.91l-0.27,-1.62l0.29,-1.82l-0.34,-0.46l-1.16,-0.18l-0.54,0.59l-1.23,0.11l-0.28,0.65l0.53,0.66l0.17,1.23l-2.44,3.01l-0.96,0.2l-2.39,-1.41l-0.95,0.52l-0.38,0.78l-1.11,0.41l-0.29,0.5l-1.94,0.0l-0.54,-0.52l-1.81,-0.09l-0.95,0.41l-2.45,-2.36l-2.07,0.54l-0.73,1.27l-0.6,2.11l-1.25,0.58l-0.75,-0.62l0.27,-2.67l-1.48,-1.78l-0.22,-1.49l-0.92,-0.97l-0.02,-1.3l-0.57,-1.17l-0.69,-0.16l0.7,-1.31l-0.18,-1.15l0.65,-0.63l0.03,-0.55l-0.36,-0.42l1.56,-3.02l1.91,0.16l0.43,-0.4l-0.1,-11.14l2.49,-0.01l0.4,-0.4l-0.0,-4.96l29.02,0.0l0.65,2.11l-0.49,0.67l0.36,2.75l0.93,3.22l2.12,1.59l-0.9,1.07l-1.72,0.4l-0.98,0.91l-1.42,5.73l0.24,1.16l-0.38,2.09l-0.97,2.4l-1.53,1.32l-1.32,2.93l-1.22,0.86l-0.37,1.34Z",
      "name": "Sudan"
    },
    "DO": {
      "path": "M241.8,368.82l0.05,-0.67l-0.47,-0.75l0.43,-0.45l0.19,-1.02l-0.09,-1.57l1.66,0.01l1.99,0.64l0.33,0.69l1.29,0.19l0.33,0.77l0.99,0.09l0.81,0.64l-0.46,0.53l-1.13,-0.48l-1.87,-0.01l-1.27,0.6l-0.75,-0.56l-1.01,0.55l-0.79,1.43l-0.23,-0.62Z",
      "name": "Dominican Rep."
    },
    "DJ": {
      "path": "M528.43,386.01l-0.45,0.67l-0.58,-0.25l-1.51,0.13l-0.18,-1.02l1.45,-1.97l0.83,0.17l0.77,-0.44l0.2,1.01l-1.21,0.52l-0.06,0.7l0.73,0.48Z",
      "name": "Djibouti"
    },
    "DK": {
      "path": "M452.3,246.5l-1.22,2.88l-2.11,-1.99l-0.26,-1.39l2.98,-1.2l0.61,1.7ZM447.78,242.9l-0.32,0.89l-0.89,-0.07l-1.8,3.21l0.54,2.1l-1.13,0.47l-1.58,-0.48l-0.91,-2.19l-0.07,-4.44l0.99,-2.3l2.0,-0.26l1.11,-1.38l1.3,-0.85l-0.05,1.54l-0.73,1.69l0.3,1.28l1.25,0.79Z",
      "name": "Denmark"
    },
    "DE": {
      "path": "M453.15,278.66l-0.56,-0.42l-1.2,-0.11l-1.89,0.66l-2.12,-0.15l-0.57,0.71l-0.83,-0.67l-0.98,0.09l-2.56,-1.08l-0.49,0.15l-0.39,0.62l-1.46,-0.02l0.26,-2.16l1.24,-2.54l-0.28,-0.57l-3.51,-0.68l-0.95,-0.81l0.12,-1.49l-0.49,-1.0l0.27,-2.61l-0.38,-3.76l1.43,-0.25l0.63,-1.53l0.65,-3.87l-0.43,-1.44l0.31,-0.56l1.61,-0.18l0.34,0.68l0.67,0.07l1.7,-2.09l-0.57,-3.77l1.35,0.41l1.33,-0.45l0.28,1.46l2.27,0.9l-0.02,1.24l0.52,0.39l2.55,-0.8l1.33,-1.07l2.53,1.51l1.08,1.24l0.51,1.88l-0.61,1.39l0.88,1.43l0.58,2.06l-0.16,1.52l0.87,2.18l-0.54,0.2l-0.49,-0.34l-0.54,0.07l-0.57,0.68l-1.71,0.73l-1.01,1.02l-1.75,0.82l-0.2,0.5l0.84,2.98l2.45,2.3l-0.71,1.4l-1.0,0.83l0.33,2.27Z",
      "name": "Germany"
    },
    "YE": {
      "path": "M528.26,376.46l0.26,-0.43l-0.22,-1.03l0.28,-0.61l-0.09,-0.91l0.92,-0.7l-0.08,-1.37l0.39,-0.76l1.01,0.48l3.33,-0.27l3.76,0.42l0.95,0.82l1.36,-0.59l1.74,-2.67l2.18,-1.11l6.86,-0.96l2.48,5.52l-1.64,0.77l-0.56,1.93l-6.23,2.19l-2.29,1.82l-1.93,0.05l-1.41,1.03l-4.24,0.75l-1.72,1.5l-3.28,0.19l-0.52,-1.19l0.02,-1.52l-1.34,-3.33Z",
      "name": "Yemen"
    },
    "DZ": {
      "path": "M441.47,315.57l-0.34,1.19l0.39,2.88l-0.55,2.35l-1.58,1.92l0.36,2.53l1.92,1.66l0.17,0.85l1.43,1.1l1.85,7.66l0.13,1.23l-0.57,5.23l0.2,1.59l-0.88,1.03l-0.02,0.5l1.41,1.93l0.14,1.24l0.89,1.54l0.5,0.17l0.97,-0.42l1.72,1.11l0.83,1.29l-8.23,4.95l-7.23,5.24l-3.43,1.15l-2.3,0.21l-0.28,-1.63l-2.56,-1.12l-0.67,-1.28l-26.12,-18.48l0.01,-3.67l3.77,-1.98l2.44,-0.43l2.12,-0.8l1.08,-1.5l2.81,-1.11l0.34,-2.2l1.34,-0.31l1.04,-1.0l3.46,-0.73l0.36,-1.59l-0.58,-0.56l-0.83,-3.02l-0.18,-1.95l-0.8,-1.65l2.06,-1.44l2.62,-0.52l1.71,-1.32l2.31,-0.91l8.23,-0.8l1.51,0.41l2.27,-1.19l2.45,-0.02l0.91,0.65l1.38,-0.05Z",
      "name": "Algeria"
    },
    "US": {
      "path": "M892.73,206.44l1.34,0.72l1.36,-0.5l1.85,1.36l2.21,0.69l-1.59,1.04l-2.57,-2.02l-2.38,0.18l-0.3,-0.25l0.09,-1.21ZM183.2,272.56l0.38,1.78l1.12,0.96l4.22,0.82l2.39,1.15l2.19,-0.43l2.01,0.64l-1.73,0.85l-3.49,3.04l-0.14,0.83l0.52,0.39l2.3,-0.7l1.8,1.17l5.17,-2.8l-0.37,0.89l0.24,0.53l1.35,0.45l1.71,1.35l4.7,-1.01l0.4,0.77l1.58,0.45l0.68,0.78l-1.42,0.21l-2.2,-0.37l-3.59,1.03l-2.72,3.73l0.35,0.91l0.62,-0.0l0.61,-0.75l-1.43,5.39l0.29,3.47l0.67,1.77l0.61,0.48l1.03,-0.07l0.75,-0.43l1.59,-2.19l0.13,-2.45l-0.82,-2.2l0.11,-1.33l1.2,-2.74l0.42,-0.36l0.48,0.84l0.4,-0.3l0.4,-1.6l0.59,-0.51l0.24,-0.94l1.66,0.56l1.67,1.25l-0.03,2.8l-1.28,1.3l0.02,1.21l0.87,0.37l1.67,-1.46l0.49,0.18l0.51,3.02l-2.51,4.23l0.17,0.59l1.54,0.69l1.51,0.19l1.93,-0.49l4.72,-2.41l2.16,-2.03l-0.08,-1.39l0.77,-0.26l3.91,0.4l2.14,-1.19l0.19,-0.39l-0.31,-1.71l2.31,-2.21l1.0,-0.57l8.31,-0.03l0.57,-0.94l1.9,-0.88l0.92,-1.72l0.75,-2.75l1.58,-2.29l0.94,0.69l1.44,-0.54l0.81,0.77l-0.0,4.78l1.98,3.01l-2.38,1.52l-5.36,2.37l-1.81,3.03l0.01,1.98l0.83,1.79l0.78,0.27l-6.43,1.12l-2.21,1.0l-0.21,0.48l0.45,0.28l3.52,-0.57l-2.73,0.77l-1.77,-0.26l-0.76,0.91l0.23,0.65l0.34,0.07l-0.43,1.87l-1.26,1.73l-1.46,-1.16l-0.49,-0.06l-0.18,0.46l0.52,1.74l0.61,0.64l0.03,0.92l-0.94,1.5l-1.22,-1.31l-0.28,-2.52l-0.35,-0.35l-0.42,0.27l-0.48,1.39l0.34,1.57l-0.97,-0.29l-0.48,0.22l0.16,0.5l1.54,0.91l0.1,2.78l0.78,0.52l0.53,3.76l-1.43,2.04l-2.47,0.86l-1.71,1.78l-1.31,0.27l-1.27,1.11l-0.43,1.05l-2.7,1.91l-2.64,3.21l-0.45,2.23l0.45,2.17l0.85,2.51l1.09,2.0l0.04,1.26l1.16,3.2l-0.18,2.82l-0.55,1.49l-0.47,0.22l-0.88,-0.24l-0.33,-1.01l-1.03,-0.79l-2.75,-5.4l0.46,-2.04l-0.76,-1.66l-1.95,-2.41l-1.47,-0.55l-2.38,1.23l-1.46,-1.42l-1.79,-0.75l-2.78,0.36l-2.27,-0.31l-2.03,0.23l-1.04,0.45l-0.18,0.57l0.39,0.67l0.19,1.47l-0.9,-0.23l-0.84,0.49l-1.57,-0.08l-2.08,-1.52l-2.08,0.34l-1.91,-0.65l-3.74,0.89l-2.39,2.17l-2.54,1.28l-1.45,1.47l-0.61,1.43l-0.02,1.98l0.38,1.9l-1.99,-0.55l-1.81,-0.8l-1.25,-3.25l-1.44,-1.57l-2.24,-3.73l-1.76,-1.15l-2.28,-0.01l-1.71,2.18l-1.74,-0.72l-1.16,-0.78l-1.52,-3.14l-3.94,-3.35l-4.34,-0.0l-0.4,0.4l-0.0,0.81l-6.5,0.02l-9.04,-3.34l-0.33,-0.75l-5.69,0.52l-0.43,-1.37l-1.62,-1.72l-1.14,-0.41l-0.55,-0.94l-1.27,-0.14l-1.02,-0.83l-2.22,-0.29l-0.43,-0.33l-0.36,-1.7l-2.4,-3.06l-2.02,-4.21l-0.05,-0.96l-2.93,-3.59l-0.33,-2.54l-1.3,-1.83l0.52,-2.65l-0.09,-2.87l-0.78,-2.59l0.96,-3.2l0.61,-6.46l-0.46,-4.91l-1.48,-4.8l0.09,-0.23l3.09,1.09l1.27,3.33l0.71,0.07l0.68,-1.24l-1.12,-5.71l68.79,-0.0l0.4,-0.4l0.13,-1.09ZM32.37,157.48l1.75,3.33l0.67,0.06l0.98,-1.29l3.62,0.39l-0.12,1.35l0.27,0.41l3.83,1.28l2.65,-0.7l5.14,2.3l4.86,0.72l1.87,0.93l3.47,-1.11l3.64,2.11l2.52,0.95l-0.03,56.12l0.38,0.4l2.37,0.14l2.29,1.31l3.91,5.31l0.63,0.04l2.4,-2.69l2.1,-1.34l1.18,2.24l3.95,4.14l4.1,8.6l4.22,2.91l0.06,2.46l-1.03,1.56l-1.12,-1.31l-2.06,-1.31l-0.68,-3.73l-3.26,-3.82l-1.32,-4.34l-0.33,-0.28l-6.34,-0.42l-2.8,-1.31l-5.26,-5.09l-6.77,-2.72l-3.55,0.39l-4.79,-2.25l-3.33,-2.21l-2.78,1.09l-0.25,0.43l0.46,3.15l-3.97,1.29l-2.26,1.69l-2.25,0.84l-0.29,-2.33l1.07,-4.71l2.51,-1.5l0.15,-0.53l-0.69,-1.3l-0.62,-0.11l-3.19,2.88l-1.77,3.43l-3.56,3.49l-0.04,0.53l1.65,2.14l-2.16,3.15l-5.1,3.33l-0.76,2.13l-3.78,2.28l-0.91,2.19l-2.68,1.74l-1.82,-0.27l-6.95,4.17l-3.92,1.13l2.36,-1.94l2.5,-1.4l2.58,-2.35l3.26,-0.66l1.2,-1.79l3.42,-2.69l2.56,-2.83l0.42,-3.52l1.25,-2.78l-0.09,-0.45l-0.46,-0.07l-2.63,1.33l-0.6,-0.62l-0.6,0.03l-1.02,1.31l-1.33,-1.98l-0.71,0.08l-0.3,0.77l-0.56,-1.45l-0.62,-0.17l-2.39,1.85l-1.03,-0.0l-0.18,-2.46l0.44,-1.74l-1.7,-2.14l-0.41,-0.11l-3.01,0.89l-1.94,-2.17l-1.61,-1.16l-0.11,-2.96l-1.78,-2.05l0.88,-2.78l2.01,-2.96l0.87,-2.7l1.66,-0.33l1.59,0.82l0.5,-0.12l1.86,-2.47l1.93,0.32l1.91,-1.75l-0.34,-2.97l-1.22,-1.04l1.59,-1.93l-0.33,-0.65l-1.69,0.11l-2.66,1.27l-0.72,1.08l-1.92,-1.11l-3.43,0.63l-3.41,-1.3l-1.05,-2.33l-2.87,-3.16l3.14,-2.29l5.47,-2.98l1.51,0.0l-0.29,2.67l0.42,0.44l5.29,-0.24l0.34,-0.59l-2.03,-3.88l-3.12,-2.51l-1.79,-3.25l-2.4,-2.83l-3.25,-2.04l1.19,-3.05l4.45,-0.33l3.16,-3.2l0.69,-3.62l2.43,-3.32l2.42,-0.86l4.6,-3.26l2.51,0.36l3.66,-3.91l3.4,1.47ZM37.56,239.39l-2.21,1.54l-0.94,-0.87l-0.32,-1.79l3.24,-2.14l1.37,0.26l0.77,1.05l-1.9,1.94ZM31.06,363.53l0.98,0.48l0.75,0.91l-1.77,1.1l-0.44,-1.57l0.48,-0.92ZM29.32,361.52l0.19,0.06l0.11,0.07l-0.18,0.04l-0.12,-0.16ZM25.2,359.55l0.2,0.24l-0.14,-0.02l-0.05,-0.23ZM5.91,226.07l-1.09,0.55l-2.4,-1.69l1.72,-0.6l1.6,0.37l0.17,1.37Z",
      "name": "United States"
    },
    "UY": {
      "path": "M286.86,504.69l-0.94,1.64l-2.58,1.54l-1.67,-0.55l-1.42,0.28l-2.4,-1.28l-1.51,0.09l-1.28,-1.4l0.16,-1.65l0.56,-0.83l-0.02,-2.91l1.22,-5.04l1.18,-0.23l2.36,2.12l1.08,0.03l4.36,3.37l1.24,1.73l-0.98,1.58l0.62,1.52Z",
      "name": "Uruguay"
    },
    "LB": {
      "path": "M510.37,325.96l-0.89,0.55l1.84,-3.86l0.6,0.08l0.24,0.7l-1.15,0.96l-0.64,1.57Z",
      "name": "Lebanon"
    },
    "LA": {
      "path": "M689.54,378.29l-1.76,-0.75l-0.5,0.15l-0.94,1.48l-1.33,-0.65l0.62,-0.99l0.11,-2.2l-2.04,-2.45l-0.25,-2.69l-1.9,-2.14l-2.15,-0.31l-0.79,0.93l-1.12,0.06l-1.06,-0.4l-2.05,1.22l-0.04,-1.63l0.61,-2.74l-0.36,-0.49l-1.35,-0.1l-0.11,-1.26l-0.97,-0.9l0.33,-0.61l1.63,-1.34l0.39,0.36l1.33,0.07l0.42,-0.45l-0.34,-2.75l0.7,-0.21l1.28,1.86l1.11,2.41l0.36,0.23l2.82,0.02l0.72,1.72l-1.4,0.67l-0.72,0.95l0.13,0.59l2.91,1.54l3.61,5.34l1.88,1.81l0.57,1.65l-0.35,1.99Z",
      "name": "Lao PDR"
    },
    "TW": {
      "path": "M724.01,356.0l-0.73,1.52l-0.9,-1.56l-0.26,-1.81l1.38,-2.53l1.73,-1.8l0.64,0.46l-1.86,5.73Z",
      "name": "Taiwan"
    },
    "TT": {
      "path": "M266.64,389.17l0.28,-1.17l1.13,-0.22l-0.06,1.21l-1.35,0.18Z",
      "name": "Trinidad and Tobago"
    },
    "TR": {
      "path": "M513.19,301.28l3.65,1.31l3.06,-0.48l2.09,0.29l3.13,-1.74l2.44,-0.15l2.19,1.49l0.35,0.95l-0.23,1.5l0.24,0.43l2.34,1.31l-1.23,0.67l-0.2,0.43l0.75,3.55l-0.42,1.23l1.16,2.15l-0.57,0.25l-0.9,-0.73l-2.91,-0.41l-1.25,0.5l-4.23,0.45l-2.81,1.15l-1.9,0.01l-1.54,-0.57l-2.56,0.81l-0.66,-0.49l-0.64,0.29l-0.12,1.59l-0.89,0.9l-0.49,-0.75l0.8,-1.4l-0.41,-0.19l-1.43,0.25l-2.0,-0.69l-2.04,1.79l-3.49,0.32l-2.14,-1.66l-2.7,-0.1l-0.87,1.34l-1.36,0.29l-2.28,-1.56l-2.71,-0.02l-1.37,-2.89l-1.7,-1.68l1.09,-2.23l-0.08,-0.46l-1.31,-1.28l2.41,-2.71l3.68,-0.13l0.36,-0.25l0.94,-2.24l4.48,0.41l3.23,-2.2l2.8,-0.91l3.98,-0.07l4.28,2.31ZM488.78,302.77l-1.7,1.44l-0.51,-0.99l1.37,-2.91l-0.78,-0.93l1.78,-0.74l1.78,0.37l0.45,1.31l1.81,0.89l-0.14,0.26l-2.76,0.17l-1.31,1.13Z",
      "name": "Turkey"
    },
    "LK": {
      "path": "M624.16,398.87l-1.82,0.48l-0.99,-1.67l-0.42,-3.47l0.95,-3.45l1.21,0.98l2.26,4.21l-0.34,2.34l-0.85,0.58Z",
      "name": "Sri Lanka"
    },
    "LV": {
      "path": "M489.13,238.44l0.98,0.86l0.21,2.15l0.72,2.39l-3.68,2.17l-2.21,-1.98l-1.3,-0.34l-0.27,-0.73l-0.45,-0.25l-2.41,0.44l-4.15,-0.29l-2.48,1.13l0.07,-2.68l1.15,-2.72l1.91,-1.29l2.14,3.3l2.01,-0.09l0.38,-0.35l0.45,-3.34l1.74,-0.68l3.03,2.19l2.16,0.1Z",
      "name": "Latvia"
    },
    "LT": {
      "path": "M486.92,246.68l0.19,1.58l-2.02,1.5l-0.54,2.27l-2.48,1.47l-2.05,-0.02l-0.5,-1.08l-1.3,-0.59l-0.07,-2.33l-1.21,-0.74l-2.38,-0.69l-0.45,-3.18l2.51,-1.21l4.09,0.28l2.23,-0.39l0.52,0.88l1.23,0.27l2.22,1.99Z",
      "name": "Lithuania"
    },
    "LU": {
      "path": "M436.07,271.5l-0.48,-0.1l0.29,-1.66l0.29,0.51l-0.1,1.25Z",
      "name": "Luxembourg"
    },
    "LR": {
      "path": "M399.36,395.85l0.18,1.54l-0.49,1.0l0.08,0.47l2.47,1.8l-0.33,2.81l-2.65,-1.13l-5.78,-4.62l0.58,-1.32l2.1,-2.34l0.86,-0.22l0.77,1.14l-0.14,0.86l0.59,0.87l1.0,0.14l0.76,-0.99Z",
      "name": "Liberia"
    },
    "LS": {
      "path": "M491.05,494.85l-0.48,0.15l-1.5,-1.78l1.12,-1.53l2.18,-1.51l1.52,1.34l-0.99,1.94l-1.23,0.4l-0.62,0.98Z",
      "name": "Lesotho"
    },
    "TH": {
      "path": "M670.27,385.68l-1.41,3.9l0.15,2.01l0.38,0.36l1.38,0.07l0.9,2.05l0.55,2.34l1.4,1.45l1.61,0.38l0.96,0.97l-0.5,0.64l-1.1,0.2l-0.34,-1.18l-2.04,-1.1l-0.63,0.23l-0.63,-0.62l-0.48,-1.3l-2.55,-2.64l-0.73,0.41l0.95,-3.91l2.16,-4.25ZM670.67,384.59l-0.92,-2.2l-0.26,-2.64l-2.14,-3.1l0.72,-0.5l0.89,-2.62l-2.62,-3.66l-0.99,-1.9l0.88,-0.52l1.05,-2.63l1.74,-0.19l2.59,-1.63l0.76,0.58l0.13,1.42l0.37,0.36l1.23,0.09l-0.52,2.34l0.05,2.46l0.6,0.33l2.43,-1.45l0.77,0.4l1.47,-0.08l0.71,-0.89l1.48,0.14l1.71,1.92l0.25,2.69l1.92,2.15l-0.1,1.92l-0.61,0.87l-2.22,-0.33l-3.5,0.65l-1.6,2.14l0.36,2.6l-1.51,-0.79l-1.85,-0.01l0.28,-1.54l-0.4,-0.47l-2.21,0.02l-0.4,0.37l-0.19,2.77l-0.34,0.94Z",
      "name": "Thailand"
    },
    "TF": {
      "path": "M596.66,558.28l-3.18,0.21l-0.05,-1.59l0.4,-1.7l1.28,0.9l2.08,0.42l-0.53,1.76Z",
      "name": "Fr. S. Antarctic Lands"
    },
    "TG": {
      "path": "M422.7,387.47l-0.1,1.24l1.53,1.53l0.08,1.1l0.5,0.65l-0.11,5.64l0.49,1.47l-1.31,0.35l-1.02,-2.13l-0.18,-1.13l0.53,-2.2l-0.63,-1.16l-0.22,-3.7l-1.01,-1.41l0.07,-0.29l1.37,0.03Z",
      "name": "Togo"
    },
    "TD": {
      "path": "M480.25,365.02l0.12,9.75l-2.1,0.05l-1.14,1.91l-0.69,1.65l0.34,0.73l-0.66,0.92l0.24,0.9l-0.86,1.97l0.45,0.5l0.59,-0.1l0.34,0.65l0.03,1.39l0.9,1.06l-1.45,0.43l-1.27,1.03l-1.83,2.78l-2.16,1.08l-2.31,-0.15l-0.86,0.25l-0.26,0.49l0.17,0.62l-2.11,1.69l-2.85,0.87l-1.09,-0.57l-0.73,0.67l-1.12,0.1l-1.1,-3.13l-1.25,-0.64l-1.22,-1.23l0.3,-0.65l3.01,0.04l0.35,-0.6l-1.3,-2.21l-0.08,-3.33l-0.97,-1.68l0.22,-1.06l-0.38,-0.48l-1.22,-0.04l0.0,-1.27l-0.98,-1.08l0.97,-3.05l3.25,-2.68l0.13,-3.38l0.95,-5.29l0.52,-1.1l-0.1,-0.47l-0.91,-0.8l-0.19,-0.98l-0.8,-0.6l-0.55,-3.77l2.11,-1.24l19.56,10.1Z",
      "name": "Chad"
    },
    "LY": {
      "path": "M483.49,331.4l-0.77,1.19l0.3,1.46l-0.6,1.92l0.73,2.26l0.0,25.02l-2.48,0.01l-0.41,0.87l-19.41,-10.02l-4.41,2.35l-1.37,-1.37l-3.82,-1.13l-1.14,-1.71l-1.98,-1.28l-1.22,0.33l-0.67,-1.15l-0.16,-1.3l-1.29,-1.77l0.88,-1.24l-0.07,-4.54l0.43,-2.38l-0.86,-3.65l1.13,-0.8l0.22,-1.23l-0.21,-1.1l3.49,-2.78l0.28,-2.06l2.44,0.85l1.18,-0.22l1.97,0.47l3.14,1.26l1.37,2.7l5.71,1.77l2.64,1.43l1.62,-0.76l1.29,-1.41l-0.45,-2.46l0.67,-1.22l1.67,-1.29l1.56,-0.37l3.13,0.56l1.09,1.36l3.98,0.83l0.38,0.6Z",
      "name": "Libya"
    },
    "AE": {
      "path": "M550.76,353.19l1.89,-0.42l3.84,0.02l4.77,-4.92l0.19,0.38l0.26,1.67l-0.82,0.01l-0.39,0.35l-0.08,2.12l-0.82,0.64l-0.01,1.0l-0.67,1.03l-0.39,1.45l-7.07,-1.29l-0.71,-2.04Z",
      "name": "United Arab Emirates"
    },
    "VE": {
      "path": "M240.68,386.52l0.53,0.75l-0.02,1.07l-1.07,1.78l0.95,2.01l0.42,0.23l1.4,-0.44l0.56,-1.84l-0.77,-1.17l-0.1,-1.49l2.83,-0.94l0.26,-0.49l-0.28,-0.97l0.3,-0.28l0.66,1.32l1.96,0.26l1.4,1.23l0.08,0.69l0.39,0.35l4.81,-0.23l1.49,1.12l1.92,0.31l1.67,-0.84l0.22,-0.61l3.44,-0.14l-0.18,0.56l0.86,1.2l2.19,0.35l1.68,1.1l0.37,1.87l0.41,0.32l1.56,0.17l-1.66,1.36l-0.22,0.92l0.66,0.98l-1.67,0.54l-0.3,0.4l0.04,0.99l-0.56,0.57l-0.01,0.55l1.85,2.27l-0.66,0.69l-4.47,1.29l-0.72,0.54l-3.69,-0.9l-0.71,0.27l-0.02,0.7l0.91,0.53l-0.08,1.55l0.35,1.58l0.35,0.31l1.66,0.17l-1.3,0.52l-0.48,1.13l-2.68,0.91l-0.6,0.77l-1.57,0.13l-1.17,-1.13l-0.8,-2.52l-1.25,-1.26l1.02,-1.23l-1.29,-2.95l0.18,-1.62l1.0,-2.21l-0.2,-0.49l-1.14,-0.47l-4.02,0.36l-1.82,-2.11l-1.57,-0.33l-2.99,0.23l-1.06,-0.98l0.25,-1.24l-0.2,-1.02l-0.59,-0.69l-0.29,-1.06l-1.08,-0.39l0.78,-2.81l1.9,-2.12Z",
      "name": "Venezuela"
    },
    "AF": {
      "path": "M600.86,316.06l-1.73,1.47l0.72,3.0l-1.1,1.13l-0.02,1.35l-0.49,0.78l-2.15,-0.09l-0.37,0.58l0.8,1.63l-1.4,0.74l-1.06,1.8l0.07,1.81l-0.66,0.56l-0.91,-0.22l-1.91,0.38l-0.48,0.81l-1.88,0.14l-1.49,1.9l-0.08,2.2l-2.91,1.07l-1.64,-0.24l-0.72,0.58l-1.41,-0.31l-2.4,0.41l-3.54,-1.24l1.98,-2.49l-0.21,-1.88l-0.3,-0.34l-1.63,-0.42l-0.19,-1.69l-0.76,-2.19l0.96,-1.48l-0.18,-0.59l-0.75,-0.31l1.48,-5.22l2.12,0.97l2.14,-0.38l0.74,-1.45l1.77,-0.42l1.54,-1.0l0.62,-2.51l1.88,-0.54l0.48,-0.87l0.93,0.61l2.13,0.12l2.55,1.01l1.96,-0.89l0.64,0.46l0.58,-0.13l0.69,-1.23l1.58,-0.09l0.47,-0.64l0.24,-1.17l0.79,-0.81l0.81,0.43l-0.19,0.66l0.71,0.58l-0.09,2.61l1.28,1.05ZM601.25,315.96l1.86,-0.88l1.42,-1.28l3.93,0.22l0.11,0.23l-2.26,0.81l-5.06,0.9Z",
      "name": "Afghanistan"
    },
    "IQ": {
      "path": "M530.81,314.51l0.79,0.72l1.26,-0.3l1.46,3.35l1.63,1.01l0.15,1.38l-1.23,1.13l-0.53,2.67l1.73,2.85l3.12,1.72l1.16,2.02l-0.38,1.98l0.39,0.48l0.41,-0.0l0.02,1.16l0.79,1.02l-2.51,-0.11l-1.71,2.58l-4.3,-0.21l-7.02,-5.78l-3.73,-2.06l-2.89,-0.78l-0.86,-3.1l5.46,-3.23l0.95,-3.7l-0.2,-2.14l1.28,-0.77l1.22,-1.86l0.86,-0.39l2.67,0.37Z",
      "name": "Iraq"
    },
    "IS": {
      "path": "M384.17,190.14l-0.45,3.88l2.67,3.88l-3.04,4.17l-9.15,4.83l-9.47,-2.42l1.99,-2.05l-0.1,-0.63l-4.53,-2.38l3.43,-0.89l0.3,-0.41l-0.11,-1.75l-0.3,-0.36l-4.81,-1.29l1.43,-3.39l3.37,-0.82l3.74,4.02l0.56,0.03l3.59,-3.17l2.9,1.61l0.45,-0.04l3.95,-3.21l3.58,0.38Z",
      "name": "Iceland"
    },
    "IR": {
      "path": "M533.43,314.24l-1.29,-2.38l0.43,-1.06l-0.72,-3.4l1.03,-0.56l0.32,0.9l1.26,1.49l2.06,0.57l1.12,-0.18l2.89,-2.33l0.6,-0.15l0.42,0.54l-0.74,1.37l0.06,0.46l1.56,1.68l0.66,0.05l0.67,1.99l2.55,0.89l1.88,1.61l3.7,0.53l3.91,-0.83l0.47,-0.8l2.17,-0.66l1.65,-1.68l1.49,0.08l1.19,-0.57l1.57,0.26l2.84,1.62l1.88,0.32l2.77,2.69l1.78,0.2l0.18,2.19l-1.69,5.93l0.23,0.49l0.64,0.26l-0.85,1.58l0.81,2.33l0.19,1.83l0.3,0.35l1.63,0.43l0.16,1.43l-2.16,2.5l-0.01,0.51l2.21,3.19l2.35,1.3l0.06,2.26l1.24,0.74l0.12,0.75l-3.31,1.33l-1.08,3.14l-9.68,-1.74l-0.99,-3.18l-1.43,-0.75l-2.18,0.48l-2.47,1.31l-2.82,-0.86l-2.46,-2.11l-2.41,-0.84l-3.42,-6.37l-0.49,-0.2l-1.17,0.41l-1.43,-0.86l-0.51,0.09l-0.64,0.77l-0.97,-1.07l-0.02,-1.4l-0.71,-0.39l0.27,-1.92l-1.29,-2.25l-3.13,-1.73l-1.59,-2.62l0.51,-2.08l1.3,-1.32l-0.19,-1.79l-1.73,-1.17l-1.57,-3.6Z",
      "name": "Iran"
    },
    "AM": {
      "path": "M537.0,308.96l-0.27,0.03l-1.24,-2.34l-0.92,0.01l-0.62,-0.73l-0.69,-0.08l-0.96,-0.89l-1.58,-0.69l0.2,-1.3l-0.28,-0.9l2.73,-0.41l1.13,1.15l-0.21,1.0l1.06,0.9l-0.5,0.74l0.08,0.53l2.05,1.37l0.04,1.62Z",
      "name": "Armenia"
    },
    "AL": {
      "path": "M470.32,297.19l0.73,0.03l0.93,0.99l0.13,0.95l-0.3,1.27l0.36,1.43l1.02,0.9l-1.82,3.2l-0.18,-0.65l-1.26,-1.0l-0.19,-1.36l0.53,-3.17l-0.55,-1.64l0.61,-0.94Z",
      "name": "Albania"
    },
    "AO": {
      "path": "M461.55,429.93l1.26,3.16l1.94,2.36l2.47,-0.54l1.25,0.32l0.44,-0.18l0.93,-1.92l1.31,-0.08l0.41,-0.44l0.47,-0.0l-0.1,0.41l0.39,0.49l2.65,-0.02l0.03,1.2l0.48,1.02l-0.34,1.52l0.18,1.56l0.83,1.04l-0.13,2.87l0.54,0.39l3.96,-0.41l-0.1,1.81l0.39,1.06l-0.24,1.45l-4.7,-0.03l-0.4,0.39l-0.12,8.23l2.93,3.55l-3.84,0.9l-5.89,-0.36l-1.88,-1.27l-10.47,0.23l-1.3,-1.03l-1.85,-0.16l-2.4,0.78l-0.15,-1.08l0.33,-2.2l1.0,-3.5l1.35,-3.24l2.24,-2.82l0.33,-2.07l-0.13,-1.54l-0.8,-1.08l-1.21,-2.88l0.87,-1.62l-1.27,-4.13l-1.17,-1.53l2.47,-0.63l7.03,0.03ZM451.71,428.77l-0.47,-1.26l1.25,-1.11l0.32,0.3l-0.99,1.03l-0.12,1.04Z",
      "name": "Angola"
    },
    "AR": {
      "path": "M258.05,471.85l1.38,1.83l0.68,-0.08l0.87,-1.93l2.39,0.09l4.94,4.92l2.17,0.51l2.99,1.99l2.47,1.04l0.26,0.88l-2.38,4.1l0.23,0.58l5.39,1.21l2.13,-0.46l2.46,-2.25l0.49,-2.47l0.76,-0.32l0.98,1.25l-0.04,1.9l-3.67,2.62l-2.85,2.79l-3.42,4.08l-1.3,5.37l0.01,2.9l-0.54,0.77l-0.36,3.52l3.15,2.82l-0.31,1.9l1.54,1.59l-0.1,1.23l-2.3,3.86l-3.55,1.64l-4.91,0.65l-2.7,-0.32l-0.43,0.5l0.5,1.83l-0.49,2.34l0.4,1.59l-1.21,0.94l-2.34,0.42l-2.29,-1.15l-1.41,0.93l0.41,3.97l1.69,1.02l1.41,-0.77l0.39,0.92l-2.08,0.99l-2.01,2.14l-0.47,3.69l-0.49,1.57l-2.34,0.12l-2.08,2.01l-0.63,3.07l2.46,2.67l2.21,0.74l-0.73,2.83l-2.84,2.04l-1.73,4.57l-2.18,1.47l-1.15,1.98l0.77,4.43l1.16,1.7l-2.44,-0.66l-5.82,-0.52l-0.91,-2.06l0.05,-2.9l-0.46,-0.4l-1.41,0.21l-0.69,-1.12l-0.2,-3.82l1.89,-1.73l0.79,-2.4l-0.26,-1.97l1.31,-3.13l0.91,-4.79l-0.23,-1.96l1.06,-0.95l-0.27,-1.32l-1.01,-0.76l0.63,-1.12l-0.05,-0.46l-1.05,-1.22l-0.53,-3.58l0.97,-0.92l-0.42,-4.02l1.21,-6.04l1.53,-1.49l-0.75,-3.06l-0.01,-2.68l1.79,-1.91l0.05,-2.76l1.43,-3.06l0.01,-2.77l-0.69,-0.77l-1.09,-4.84l1.48,-2.87l-0.19,-2.93l0.85,-2.48l1.59,-2.58l1.73,-1.72l0.05,-0.51l-0.61,-0.89l0.45,-0.89l-0.07,-4.37l2.71,-1.48l0.86,-2.84l-0.22,-0.73l1.77,-2.07l2.9,0.58ZM256.68,580.89l-1.95,0.18l-1.42,-1.53l-3.82,-0.12l-0.0,-7.37l1.57,3.7l3.26,2.57l3.18,1.01l-0.81,1.56Z",
      "name": "Argentina"
    },
    "AU": {
      "path": "M705.79,484.09l0.27,0.04l0.18,-0.47l-0.49,-1.51l0.92,1.16l0.45,0.15l0.28,-0.39l-0.09,-1.61l-1.99,-3.77l1.09,-3.43l-0.24,-1.62l0.34,-0.64l0.38,1.08l0.43,-0.19l0.99,-1.75l1.91,-0.85l1.29,-1.18l1.81,-0.93l0.95,-0.17l0.93,0.27l1.92,-0.97l1.46,-0.29l1.03,-0.82l1.44,0.04l2.78,-0.86l1.36,-1.18l0.71,-1.48l1.41,-1.28l0.3,-2.63l1.27,-1.61l0.78,1.67l0.54,0.19l1.07,-0.52l0.15,-0.59l-0.73,-1.02l0.45,-0.73l0.78,0.4l0.58,-0.3l0.28,-1.84l1.87,-2.17l1.12,-0.39l0.28,-0.58l0.62,0.17l0.5,-0.36l0.03,-0.38l1.87,-0.58l1.65,1.06l1.35,1.49l3.4,0.39l0.44,-0.54l-0.46,-1.24l1.05,-1.82l1.04,-0.62l0.14,-0.55l-0.25,-0.41l0.88,-1.19l1.31,-0.78l1.31,0.27l2.1,-0.48l0.31,-0.4l-0.05,-1.31l-0.92,-0.78l1.48,0.56l1.41,1.08l2.11,0.65l0.81,-0.21l1.4,0.71l1.69,-0.67l0.8,0.19l0.64,-0.33l0.71,0.78l-1.33,1.96l-0.71,0.07l-0.35,0.51l0.24,0.87l-1.52,2.38l0.12,1.06l2.15,1.66l1.97,0.86l3.04,2.4l1.97,0.66l0.54,0.89l2.72,0.87l1.84,-1.12l2.07,-6.05l-0.43,-3.63l0.3,-1.75l0.47,-0.87l-0.32,-0.69l1.09,-3.31l0.46,-0.47l0.4,0.71l0.17,1.52l0.65,0.53l0.15,1.04l0.85,1.22l0.12,2.41l0.9,2.03l0.57,0.18l1.3,-0.79l1.69,1.73l-0.2,1.09l0.53,2.23l0.39,1.32l0.68,0.49l0.6,1.99l-0.2,1.51l0.81,1.79l2.87,1.56l3.14,2.21l-0.12,0.78l1.38,1.62l0.95,2.84l0.58,0.22l0.71,-0.42l0.8,0.92l0.61,0.01l0.46,2.48l4.82,4.87l0.66,2.1l-0.07,3.44l1.15,2.31l-0.13,2.37l-1.1,3.88l0.04,1.73l-0.48,2.02l-1.05,2.56l-1.9,1.57l-1.73,3.77l-2.38,6.57l-0.24,3.08l-1.15,0.88l-2.86,0.16l-2.31,1.3l-2.5,2.46l-1.81,-1.24l-1.29,-0.49l0.31,-1.32l-0.55,-0.46l-1.5,0.69l-2.01,2.12l-7.1,-2.39l-1.49,-1.79l-1.13,-4.06l-1.45,-1.37l-1.84,-0.28l0.58,-1.28l-0.61,-2.26l-0.73,-0.1l-1.14,1.96l-0.94,0.24l0.6,-0.77l0.44,-1.84l0.99,-1.67l-0.2,-2.22l-0.28,-0.35l-0.43,0.13l-2.0,2.51l-1.51,1.0l-0.93,2.15l-1.35,-0.87l-0.01,-1.63l-1.57,-2.18l-1.11,-0.96l0.27,-0.39l-0.13,-0.58l-3.21,-1.8l-1.84,-0.13l-2.55,-1.44l-4.58,0.3l-6.02,2.02l-2.54,-0.14l-2.62,1.5l-2.13,0.67l-1.49,2.78l-3.48,0.33l-2.3,-0.54l-3.48,0.46l-1.6,1.58l-0.81,-0.03l-2.36,1.75l-3.24,-0.11l-3.72,-2.38l0.04,-1.18l1.19,-0.49l0.48,-0.93l0.21,-3.17l-0.28,-1.75l-1.34,-3.02l-0.39,-1.56l0.06,-1.8l-0.96,-1.79l-0.17,-1.0l-1.02,-1.04l-0.29,-2.09l-1.15,-1.85ZM784.91,527.24l2.67,1.14l3.23,-1.06l1.08,0.16l0.16,3.5l-0.85,1.25l-0.18,1.86l-0.27,-0.29l-0.62,0.04l-1.56,2.15l-1.66,-0.2l-1.41,-2.68l-0.37,-2.29l-1.4,-2.82l0.04,-0.96l1.14,0.2Z",
      "name": "Australia"
    },
    "AT": {
      "path": "M462.92,275.34l0.01,2.75l-1.06,0.01l-0.34,0.61l0.39,0.64l-1.07,2.55l-2.0,0.08l-1.34,0.81l-5.27,-1.14l-0.48,-1.1l-0.47,-0.23l-2.47,0.64l-0.42,0.58l-2.45,-0.51l-0.75,-0.44l0.44,-1.16l1.11,0.9l0.63,-0.17l0.25,-0.69l1.91,0.14l1.87,-0.66l0.97,0.09l0.68,0.66l0.65,-0.15l0.25,-0.83l-0.31,-2.16l0.82,-0.52l0.68,-1.35l1.49,0.98l0.52,-0.07l1.34,-1.47l0.61,-0.2l1.79,1.07l1.3,-0.12l0.74,0.46Z",
      "name": "Austria"
    },
    "IN": {
      "path": "M623.36,335.51l-1.27,1.12l-0.97,2.68l0.21,0.5l8.04,4.05l3.43,0.39l1.57,1.44l4.92,0.91l2.18,-0.04l0.38,-0.3l0.29,-1.28l-0.32,-1.72l0.15,-0.92l0.82,-0.32l0.44,2.59l2.28,1.07l1.78,-0.4l4.14,0.1l0.38,-0.36l0.18,-1.73l-0.53,-0.69l1.4,-0.31l2.25,-2.09l2.69,-1.7l1.92,0.64l1.8,-1.03l0.8,1.22l-0.69,0.98l0.26,0.63l2.42,0.38l0.09,0.52l-0.83,0.77l0.13,1.14l-1.53,-0.3l-3.24,1.94l-0.12,1.84l-1.32,2.23l-0.17,1.44l-0.93,1.89l-1.63,-0.52l-0.52,0.37l-0.09,2.72l-0.56,1.13l0.2,0.85l-0.53,0.28l-1.18,-3.85l-1.08,-0.27l-0.38,0.31l-0.24,1.03l-0.66,-0.68l0.55,-1.12l1.21,-0.35l1.15,-2.33l-0.23,-0.56l-1.58,-0.49l-4.33,-0.29l-0.19,-1.63l-0.35,-0.35l-1.11,-0.13l-1.91,-1.16l-0.57,0.17l-0.88,1.89l0.11,0.48l1.38,1.12l-1.11,0.73l-0.69,1.14l0.18,0.55l1.24,0.59l-0.32,1.59l0.85,2.01l0.36,2.08l-0.22,0.62l-4.58,0.54l-0.33,0.42l0.13,1.86l-1.18,1.39l-3.65,1.85l-2.79,3.1l-4.32,3.33l-0.18,1.29l-4.65,1.82l-0.77,2.19l0.64,5.37l-1.06,2.51l-0.01,3.97l-1.24,0.28l-1.14,1.94l0.39,0.85l-1.69,0.53l-1.04,1.84l-0.65,0.47l-2.06,-2.06l-2.1,-6.05l-2.2,-3.67l-1.05,-4.8l-2.29,-3.61l-1.76,-8.34l0.01,-3.18l-0.49,-2.59l-0.55,-0.29l-3.53,1.56l-1.52,-0.28l-2.87,-2.86l0.86,-0.7l0.08,-0.54l-0.74,-1.06l-2.68,-2.13l1.26,-1.38l5.33,0.01l0.39,-0.48l-0.5,-2.37l-1.42,-1.51l-0.27,-2.01l-1.44,-1.26l2.33,-2.5l3.05,0.07l2.62,-2.99l1.6,-2.96l2.4,-2.88l0.06,-2.16l1.98,-1.58l-0.01,-0.64l-1.93,-1.4l-0.82,-1.91l-0.81,-2.4l0.91,-0.97l3.58,0.7l2.93,-0.45l2.32,-2.35l2.31,3.07l-0.24,2.31l0.99,1.68l-0.05,0.92l-1.34,-0.3l-0.48,0.47l0.7,3.26l2.61,2.09l3.02,1.77Z",
      "name": "India"
    },
    "TZ": {
      "path": "M495.56,426.32l2.8,-3.13l-0.02,-0.82l-0.64,-1.3l0.68,-0.52l0.14,-1.47l-0.76,-1.25l0.31,-0.11l2.26,0.03l-0.51,2.76l0.76,1.3l0.5,0.12l1.05,-0.53l1.19,-0.12l0.61,0.24l1.43,-0.62l0.1,-0.67l-0.71,-0.62l1.57,-1.7l8.65,4.86l0.32,1.53l3.34,2.33l-1.05,2.81l0.13,1.61l1.63,1.12l-0.6,1.77l-0.01,2.33l1.89,4.05l0.57,0.44l-1.47,1.09l-2.61,0.95l-1.43,-0.04l-1.06,0.77l-2.29,0.36l-2.87,-0.69l-0.83,0.07l-0.64,-0.75l-0.31,-2.8l-1.32,-1.36l-3.25,-0.77l-3.96,-1.59l-1.18,-2.42l-0.32,-1.75l-1.76,-1.49l0.42,-1.05l-0.44,-0.89l0.08,-0.96l-0.46,-0.58l0.06,-0.56Z",
      "name": "Tanzania"
    },
    "AZ": {
      "path": "M539.27,301.57l1.33,0.36l0.44,-0.21l0.4,-0.78l1.11,-1.01l2.3,3.71l1.5,0.55l-1.32,0.17l-0.34,0.33l-0.81,3.49l-0.98,1.01l0.05,1.26l-1.28,-1.27l0.73,-1.34l-0.78,-1.39l-1.51,0.17l-2.32,1.87l-0.04,-1.43l-2.05,-1.48l0.5,-0.74l-0.07,-0.53l-1.07,-0.91l0.33,-0.54l-0.14,-0.55l-1.17,-1.02l1.91,0.73l1.71,0.07l0.37,-0.88l-1.01,-1.48l0.2,-0.14l0.4,0.06l1.63,1.92ZM533.76,306.94l0.63,0.52l0.69,-0.0l0.63,1.35l-0.71,-0.18l-1.25,-1.69Z",
      "name": "Azerbaijan"
    },
    "IE": {
      "path": "M405.07,254.34l0.37,2.67l-1.78,3.47l-4.21,2.28l-2.89,-0.5l1.83,-4.09l-1.24,-4.04l4.62,-4.68l0.33,1.5l-0.5,2.21l0.41,0.49l1.45,-0.06l1.61,0.75Z",
      "name": "Ireland"
    },
    "ID": {
      "path": "M756.47,417.79l0.69,4.01l2.79,1.78l0.51,-0.1l2.04,-2.59l2.71,-1.43l2.05,-0.0l3.9,1.73l2.46,0.45l0.08,15.16l-1.75,-1.55l-2.54,-0.51l-0.88,0.72l-2.32,0.06l0.69,-1.33l1.45,-0.64l0.23,-0.46l-0.65,-2.74l-1.24,-2.22l-5.04,-2.3l-2.09,-0.23l-3.68,-2.27l-0.55,0.13l-0.65,1.07l-0.52,0.12l-0.55,-1.89l-1.21,-0.78l1.84,-0.62l1.72,0.05l0.39,-0.52l-0.21,-0.66l-0.38,-0.28l-3.45,-0.0l-1.13,-1.48l-2.1,-0.43l-0.52,-0.61l2.69,-0.48l1.28,-0.78l3.66,0.94l0.3,0.71ZM757.91,430.25l-0.62,0.82l-0.1,-0.8l0.59,-1.12l0.13,1.1ZM747.38,422.88l0.34,0.72l-1.22,-0.57l-4.68,-0.1l0.27,-0.62l2.78,-0.09l2.52,0.67ZM741.05,415.14l-0.67,-2.88l0.64,-2.01l0.41,0.86l1.21,0.18l0.16,0.7l-0.1,1.68l-0.84,-0.16l-0.46,0.3l-0.34,1.34ZM739.05,423.4l-0.5,0.45l-1.34,-0.36l-0.17,-0.37l1.73,-0.08l0.27,0.36ZM721.45,414.41l-0.19,1.97l2.24,2.23l0.54,0.02l1.27,-1.07l2.75,-0.5l-0.9,1.21l-2.11,0.93l-0.16,0.6l2.22,3.01l-0.3,1.07l1.36,1.75l-2.26,0.85l-0.28,-0.31l0.12,-1.19l-1.64,-1.34l0.17,-2.24l-0.56,-0.39l-1.67,0.76l-0.23,0.39l0.3,6.18l-1.1,0.25l-0.69,-0.47l0.64,-2.21l-0.39,-2.42l-0.39,-0.34l-0.8,-0.01l-0.58,-1.29l0.98,-1.6l0.35,-1.96l1.32,-3.87ZM728.59,426.17l0.38,0.5l-0.02,1.28l-0.88,0.49l-0.53,-0.48l1.04,-1.79ZM729.04,416.88l0.27,-0.05l-0.02,0.13l-0.24,-0.08ZM721.68,413.95l0.16,-0.32l1.89,-1.65l1.83,0.68l3.16,0.35l2.94,-0.1l2.39,-1.66l-1.73,2.13l-1.66,0.43l-2.41,-0.48l-4.17,0.13l-2.39,0.51ZM730.55,440.42l1.11,-1.94l2.02,-0.82l0.08,0.62l-1.45,1.68l-1.77,0.46ZM728.12,435.8l-0.1,0.38l-3.46,0.66l-2.91,-0.27l-0.0,-0.25l1.54,-0.41l1.66,0.73l1.67,-0.19l1.61,-0.65ZM722.9,440.18l-0.64,0.03l-2.26,-1.21l1.12,-0.24l1.78,1.42ZM716.26,435.69l0.88,0.51l1.28,-0.17l0.2,0.35l-4.65,0.73l0.4,-0.67l1.15,-0.02l0.75,-0.74ZM711.66,423.74l-0.38,-0.16l-2.54,1.01l-1.12,-1.44l-1.69,-0.13l-1.16,-0.75l-3.04,0.77l-1.1,-1.15l-3.31,-0.11l-0.35,-3.05l-1.35,-0.95l-1.11,-1.98l-0.33,-2.06l0.27,-2.14l0.9,-1.01l0.37,1.15l2.09,1.49l1.53,-0.48l1.82,0.08l1.38,-1.19l1.0,-0.18l2.28,0.67l2.26,-0.53l1.52,-3.64l1.01,-0.99l0.78,-2.57l4.1,0.31l-1.11,1.77l0.02,0.46l1.7,2.2l-0.23,1.39l2.07,1.71l-2.33,0.42l-0.88,1.9l0.1,2.05l-2.4,1.9l-0.06,2.45l-0.7,2.79ZM692.58,431.94l0.35,0.26l4.8,0.25l0.78,-0.97l4.17,1.09l1.13,1.69l3.69,0.45l2.14,1.05l-1.8,0.61l-2.77,-1.0l-4.8,-0.12l-5.24,-1.42l-1.84,-0.25l-1.11,0.3l-4.26,-0.97l-0.7,-1.14l-1.59,-0.13l1.18,-1.66l2.74,0.13l2.87,1.13l0.26,0.69ZM685.53,429.08l-2.22,0.04l-2.06,-2.04l-3.15,-2.01l-2.93,-3.52l-3.11,-5.33l-2.2,-2.12l-1.64,-4.06l-2.32,-1.69l-1.27,-2.07l-1.96,-1.5l-2.51,-2.65l-0.11,-0.66l4.81,0.53l2.15,2.38l3.31,2.74l2.35,2.66l2.7,0.17l1.95,1.59l1.54,2.17l1.59,0.95l-0.84,1.71l0.15,0.52l1.44,0.87l0.79,0.1l0.4,1.58l0.87,1.4l1.96,0.39l1.0,1.31l-0.6,3.01l-0.09,3.51Z",
      "name": "Indonesia"
    },
    "UA": {
      "path": "M493.77,283.66l1.85,0.21l0.66,-0.27l0.1,-0.68l-0.25,-0.87l-0.8,-0.85l-0.34,-1.43l-0.87,-0.71l0.01,-1.37l-1.13,-1.01l-1.16,-0.23l-2.07,-1.18l-1.66,0.37l-0.67,0.55l-0.9,-0.0l-0.86,0.91l-1.69,0.33l-0.76,0.47l-1.18,-0.82l-3.05,-0.42l-0.9,0.48l-0.22,-0.62l-1.16,-0.85l0.86,-1.88l0.25,0.1l0.53,-0.51l-0.57,-1.53l2.08,-2.96l1.38,-0.69l0.26,-1.34l-1.09,-3.02l0.9,-0.18l1.27,-1.02l1.78,-0.08l2.45,0.31l2.87,0.98l1.87,0.08l0.85,0.53l1.06,-0.47l0.78,0.77l2.17,-0.18l0.91,0.35l0.54,-0.34l0.15,-1.9l0.58,-0.67l2.82,-0.06l0.87,-0.86l3.0,-0.22l1.29,1.86l-0.53,0.89l0.21,1.25l0.36,0.33l1.78,0.17l0.93,2.49l3.18,1.38l1.95,-0.52l1.69,1.77l1.39,-0.04l3.36,1.15l0.02,0.75l-0.97,1.91l0.49,2.26l-0.28,0.89l-2.37,0.33l-1.29,1.04l-0.21,1.6l-1.85,0.32l-1.58,1.12l-2.41,0.24l-2.16,1.36l-0.19,0.36l0.32,2.54l1.49,0.93l1.92,-0.16l-0.18,0.47l-2.65,0.61l-3.21,1.92l-0.89,-0.46l0.44,-1.33l-0.24,-0.5l-2.27,-0.86l2.41,-1.32l0.12,-0.62l-0.93,-0.95l-3.62,-0.85l-0.14,-1.08l-0.47,-0.34l-2.32,0.45l-2.91,4.52l-1.19,-0.45l-0.98,0.48l-0.36,-0.21l1.35,-2.93Z",
      "name": "Ukraine"
    },
    "QA": {
      "path": "M549.32,350.8l-0.76,-0.24l-0.14,-1.72l0.84,-1.35l0.47,0.54l0.04,1.41l-0.45,1.36Z",
      "name": "Qatar"
    },
    "MZ": {
      "path": "M508.58,448.77l-0.34,-2.6l0.51,-2.07l3.55,0.64l2.51,-0.38l1.02,-0.76l1.49,0.01l2.74,-0.99l1.66,-1.21l0.51,9.32l0.41,1.25l-0.68,1.69l-0.93,1.74l-1.5,1.52l-5.16,2.32l-2.78,2.78l-1.02,0.54l-1.71,1.84l-0.98,0.59l-0.35,2.45l1.16,1.99l0.49,2.24l0.43,0.31l-0.06,2.14l-0.39,1.21l0.5,0.73l-0.25,0.78l-0.92,0.86l-5.13,2.47l-1.22,1.39l0.21,1.17l0.59,0.4l-0.11,0.78l-1.22,-0.02l-0.73,-3.1l0.42,-3.19l-1.78,-5.56l2.49,-2.89l0.69,-1.93l0.44,-0.43l0.28,-1.57l-0.39,-0.94l0.59,-3.72l-0.01,-3.32l-1.48,-1.17l-1.2,-0.23l-1.74,-1.18l-1.92,0.0l-0.3,-2.12l7.06,-1.98l1.28,1.1l0.89,-0.1l0.67,0.45l0.1,0.75l-0.51,1.3l0.19,1.83l1.75,1.86l0.65,-0.13l0.71,-1.68l1.17,-0.86l-0.26,-3.51l-1.05,-1.87l-1.04,-0.95Z",
      "name": "Mozambique"
    }
  },
  "height": 583.0802520919394,
  "projection": {
    "type": "merc",
    "centralMeridian": 11.5
  },
  "width": 900.0
})