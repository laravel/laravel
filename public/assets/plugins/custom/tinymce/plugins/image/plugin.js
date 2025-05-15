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
    var eq = function (t) {
      return function (a) {
        return t === a;
      };
    };
    var isString = isType('string');
    var isObject = isType('object');
    var isArray = isType('array');
    var isNull = eq(null);
    var isBoolean = isSimpleType('boolean');
    var isNullable = function (a) {
      return a === null || a === undefined;
    };
    var isNonNullable = function (a) {
      return !isNullable(a);
    };
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

    var keys = Object.keys;
    var hasOwnProperty = Object.hasOwnProperty;
    var each = function (obj, f) {
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
      each(obj, function (x, i) {
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

    var nativePush = Array.prototype.push;
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
    var get = function (xs, i) {
      return i >= 0 && i < xs.length ? Optional.some(xs[i]) : Optional.none();
    };
    var head = function (xs) {
      return get(xs, 0);
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

    var Global = typeof window !== 'undefined' ? window : Function('return this;')();

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
    var remove = function (element, key) {
      element.dom.removeAttribute(key);
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

    var global$1 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');

    var global$2 = tinymce.util.Tools.resolve('tinymce.util.Promise');

    var global$3 = tinymce.util.Tools.resolve('tinymce.util.XHR');

    var hasDimensions = function (editor) {
      return editor.getParam('image_dimensions', true, 'boolean');
    };
    var hasAdvTab = function (editor) {
      return editor.getParam('image_advtab', false, 'boolean');
    };
    var hasUploadTab = function (editor) {
      return editor.getParam('image_uploadtab', true, 'boolean');
    };
    var getPrependUrl = function (editor) {
      return editor.getParam('image_prepend_url', '', 'string');
    };
    var getClassList = function (editor) {
      return editor.getParam('image_class_list');
    };
    var hasDescription = function (editor) {
      return editor.getParam('image_description', true, 'boolean');
    };
    var hasImageTitle = function (editor) {
      return editor.getParam('image_title', false, 'boolean');
    };
    var hasImageCaption = function (editor) {
      return editor.getParam('image_caption', false, 'boolean');
    };
    var getImageList = function (editor) {
      return editor.getParam('image_list', false);
    };
    var hasUploadUrl = function (editor) {
      return isNonNullable(editor.getParam('images_upload_url'));
    };
    var hasUploadHandler = function (editor) {
      return isNonNullable(editor.getParam('images_upload_handler'));
    };
    var showAccessibilityOptions = function (editor) {
      return editor.getParam('a11y_advanced_options', false, 'boolean');
    };
    var isAutomaticUploadsEnabled = function (editor) {
      return editor.getParam('automatic_uploads', true, 'boolean');
    };

    var parseIntAndGetMax = function (val1, val2) {
      return Math.max(parseInt(val1, 10), parseInt(val2, 10));
    };
    var getImageSize = function (url) {
      return new global$2(function (callback) {
        var img = document.createElement('img');
        var done = function (dimensions) {
          if (img.parentNode) {
            img.parentNode.removeChild(img);
          }
          callback(dimensions);
        };
        img.onload = function () {
          var width = parseIntAndGetMax(img.width, img.clientWidth);
          var height = parseIntAndGetMax(img.height, img.clientHeight);
          var dimensions = {
            width: width,
            height: height
          };
          done(global$2.resolve(dimensions));
        };
        img.onerror = function () {
          done(global$2.reject('Failed to get image dimensions for: ' + url));
        };
        var style = img.style;
        style.visibility = 'hidden';
        style.position = 'fixed';
        style.bottom = style.left = '0px';
        style.width = style.height = 'auto';
        document.body.appendChild(img);
        img.src = url;
      });
    };
    var removePixelSuffix = function (value) {
      if (value) {
        value = value.replace(/px$/, '');
      }
      return value;
    };
    var addPixelSuffix = function (value) {
      if (value.length > 0 && /^[0-9]+$/.test(value)) {
        value += 'px';
      }
      return value;
    };
    var mergeMargins = function (css) {
      if (css.margin) {
        var splitMargin = String(css.margin).split(' ');
        switch (splitMargin.length) {
        case 1:
          css['margin-top'] = css['margin-top'] || splitMargin[0];
          css['margin-right'] = css['margin-right'] || splitMargin[0];
          css['margin-bottom'] = css['margin-bottom'] || splitMargin[0];
          css['margin-left'] = css['margin-left'] || splitMargin[0];
          break;
        case 2:
          css['margin-top'] = css['margin-top'] || splitMargin[0];
          css['margin-right'] = css['margin-right'] || splitMargin[1];
          css['margin-bottom'] = css['margin-bottom'] || splitMargin[0];
          css['margin-left'] = css['margin-left'] || splitMargin[1];
          break;
        case 3:
          css['margin-top'] = css['margin-top'] || splitMargin[0];
          css['margin-right'] = css['margin-right'] || splitMargin[1];
          css['margin-bottom'] = css['margin-bottom'] || splitMargin[2];
          css['margin-left'] = css['margin-left'] || splitMargin[1];
          break;
        case 4:
          css['margin-top'] = css['margin-top'] || splitMargin[0];
          css['margin-right'] = css['margin-right'] || splitMargin[1];
          css['margin-bottom'] = css['margin-bottom'] || splitMargin[2];
          css['margin-left'] = css['margin-left'] || splitMargin[3];
        }
        delete css.margin;
      }
      return css;
    };
    var createImageList = function (editor, callback) {
      var imageList = getImageList(editor);
      if (typeof imageList === 'string') {
        global$3.send({
          url: imageList,
          success: function (text) {
            callback(JSON.parse(text));
          }
        });
      } else if (typeof imageList === 'function') {
        imageList(callback);
      } else {
        callback(imageList);
      }
    };
    var waitLoadImage = function (editor, data, imgElm) {
      var selectImage = function () {
        imgElm.onload = imgElm.onerror = null;
        if (editor.selection) {
          editor.selection.select(imgElm);
          editor.nodeChanged();
        }
      };
      imgElm.onload = function () {
        if (!data.width && !data.height && hasDimensions(editor)) {
          editor.dom.setAttribs(imgElm, {
            width: String(imgElm.clientWidth),
            height: String(imgElm.clientHeight)
          });
        }
        selectImage();
      };
      imgElm.onerror = selectImage;
    };
    var blobToDataUri = function (blob) {
      return new global$2(function (resolve, reject) {
        var reader = new FileReader();
        reader.onload = function () {
          resolve(reader.result);
        };
        reader.onerror = function () {
          reject(reader.error.message);
        };
        reader.readAsDataURL(blob);
      });
    };
    var isPlaceholderImage = function (imgElm) {
      return imgElm.nodeName === 'IMG' && (imgElm.hasAttribute('data-mce-object') || imgElm.hasAttribute('data-mce-placeholder'));
    };

    var DOM = global$1.DOM;
    var getHspace = function (image) {
      if (image.style.marginLeft && image.style.marginRight && image.style.marginLeft === image.style.marginRight) {
        return removePixelSuffix(image.style.marginLeft);
      } else {
        return '';
      }
    };
    var getVspace = function (image) {
      if (image.style.marginTop && image.style.marginBottom && image.style.marginTop === image.style.marginBottom) {
        return removePixelSuffix(image.style.marginTop);
      } else {
        return '';
      }
    };
    var getBorder = function (image) {
      if (image.style.borderWidth) {
        return removePixelSuffix(image.style.borderWidth);
      } else {
        return '';
      }
    };
    var getAttrib = function (image, name) {
      if (image.hasAttribute(name)) {
        return image.getAttribute(name);
      } else {
        return '';
      }
    };
    var getStyle = function (image, name) {
      return image.style[name] ? image.style[name] : '';
    };
    var hasCaption = function (image) {
      return image.parentNode !== null && image.parentNode.nodeName === 'FIGURE';
    };
    var updateAttrib = function (image, name, value) {
      if (value === '') {
        image.removeAttribute(name);
      } else {
        image.setAttribute(name, value);
      }
    };
    var wrapInFigure = function (image) {
      var figureElm = DOM.create('figure', { class: 'image' });
      DOM.insertAfter(figureElm, image);
      figureElm.appendChild(image);
      figureElm.appendChild(DOM.create('figcaption', { contentEditable: 'true' }, 'Caption'));
      figureElm.contentEditable = 'false';
    };
    var removeFigure = function (image) {
      var figureElm = image.parentNode;
      DOM.insertAfter(image, figureElm);
      DOM.remove(figureElm);
    };
    var toggleCaption = function (image) {
      if (hasCaption(image)) {
        removeFigure(image);
      } else {
        wrapInFigure(image);
      }
    };
    var normalizeStyle = function (image, normalizeCss) {
      var attrValue = image.getAttribute('style');
      var value = normalizeCss(attrValue !== null ? attrValue : '');
      if (value.length > 0) {
        image.setAttribute('style', value);
        image.setAttribute('data-mce-style', value);
      } else {
        image.removeAttribute('style');
      }
    };
    var setSize = function (name, normalizeCss) {
      return function (image, name, value) {
        if (image.style[name]) {
          image.style[name] = addPixelSuffix(value);
          normalizeStyle(image, normalizeCss);
        } else {
          updateAttrib(image, name, value);
        }
      };
    };
    var getSize = function (image, name) {
      if (image.style[name]) {
        return removePixelSuffix(image.style[name]);
      } else {
        return getAttrib(image, name);
      }
    };
    var setHspace = function (image, value) {
      var pxValue = addPixelSuffix(value);
      image.style.marginLeft = pxValue;
      image.style.marginRight = pxValue;
    };
    var setVspace = function (image, value) {
      var pxValue = addPixelSuffix(value);
      image.style.marginTop = pxValue;
      image.style.marginBottom = pxValue;
    };
    var setBorder = function (image, value) {
      var pxValue = addPixelSuffix(value);
      image.style.borderWidth = pxValue;
    };
    var setBorderStyle = function (image, value) {
      image.style.borderStyle = value;
    };
    var getBorderStyle = function (image) {
      return getStyle(image, 'borderStyle');
    };
    var isFigure = function (elm) {
      return elm.nodeName === 'FIGURE';
    };
    var isImage = function (elm) {
      return elm.nodeName === 'IMG';
    };
    var getIsDecorative = function (image) {
      return DOM.getAttrib(image, 'alt').length === 0 && DOM.getAttrib(image, 'role') === 'presentation';
    };
    var getAlt = function (image) {
      if (getIsDecorative(image)) {
        return '';
      } else {
        return getAttrib(image, 'alt');
      }
    };
    var defaultData = function () {
      return {
        src: '',
        alt: '',
        title: '',
        width: '',
        height: '',
        class: '',
        style: '',
        caption: false,
        hspace: '',
        vspace: '',
        border: '',
        borderStyle: '',
        isDecorative: false
      };
    };
    var getStyleValue = function (normalizeCss, data) {
      var image = document.createElement('img');
      updateAttrib(image, 'style', data.style);
      if (getHspace(image) || data.hspace !== '') {
        setHspace(image, data.hspace);
      }
      if (getVspace(image) || data.vspace !== '') {
        setVspace(image, data.vspace);
      }
      if (getBorder(image) || data.border !== '') {
        setBorder(image, data.border);
      }
      if (getBorderStyle(image) || data.borderStyle !== '') {
        setBorderStyle(image, data.borderStyle);
      }
      return normalizeCss(image.getAttribute('style'));
    };
    var create = function (normalizeCss, data) {
      var image = document.createElement('img');
      write(normalizeCss, __assign(__assign({}, data), { caption: false }), image);
      setAlt(image, data.alt, data.isDecorative);
      if (data.caption) {
        var figure = DOM.create('figure', { class: 'image' });
        figure.appendChild(image);
        figure.appendChild(DOM.create('figcaption', { contentEditable: 'true' }, 'Caption'));
        figure.contentEditable = 'false';
        return figure;
      } else {
        return image;
      }
    };
    var read = function (normalizeCss, image) {
      return {
        src: getAttrib(image, 'src'),
        alt: getAlt(image),
        title: getAttrib(image, 'title'),
        width: getSize(image, 'width'),
        height: getSize(image, 'height'),
        class: getAttrib(image, 'class'),
        style: normalizeCss(getAttrib(image, 'style')),
        caption: hasCaption(image),
        hspace: getHspace(image),
        vspace: getVspace(image),
        border: getBorder(image),
        borderStyle: getStyle(image, 'borderStyle'),
        isDecorative: getIsDecorative(image)
      };
    };
    var updateProp = function (image, oldData, newData, name, set) {
      if (newData[name] !== oldData[name]) {
        set(image, name, newData[name]);
      }
    };
    var setAlt = function (image, alt, isDecorative) {
      if (isDecorative) {
        DOM.setAttrib(image, 'role', 'presentation');
        var sugarImage = SugarElement.fromDom(image);
        set(sugarImage, 'alt', '');
      } else {
        if (isNull(alt)) {
          var sugarImage = SugarElement.fromDom(image);
          remove(sugarImage, 'alt');
        } else {
          var sugarImage = SugarElement.fromDom(image);
          set(sugarImage, 'alt', alt);
        }
        if (DOM.getAttrib(image, 'role') === 'presentation') {
          DOM.setAttrib(image, 'role', '');
        }
      }
    };
    var updateAlt = function (image, oldData, newData) {
      if (newData.alt !== oldData.alt || newData.isDecorative !== oldData.isDecorative) {
        setAlt(image, newData.alt, newData.isDecorative);
      }
    };
    var normalized = function (set, normalizeCss) {
      return function (image, name, value) {
        set(image, value);
        normalizeStyle(image, normalizeCss);
      };
    };
    var write = function (normalizeCss, newData, image) {
      var oldData = read(normalizeCss, image);
      updateProp(image, oldData, newData, 'caption', function (image, _name, _value) {
        return toggleCaption(image);
      });
      updateProp(image, oldData, newData, 'src', updateAttrib);
      updateProp(image, oldData, newData, 'title', updateAttrib);
      updateProp(image, oldData, newData, 'width', setSize('width', normalizeCss));
      updateProp(image, oldData, newData, 'height', setSize('height', normalizeCss));
      updateProp(image, oldData, newData, 'class', updateAttrib);
      updateProp(image, oldData, newData, 'style', normalized(function (image, value) {
        return updateAttrib(image, 'style', value);
      }, normalizeCss));
      updateProp(image, oldData, newData, 'hspace', normalized(setHspace, normalizeCss));
      updateProp(image, oldData, newData, 'vspace', normalized(setVspace, normalizeCss));
      updateProp(image, oldData, newData, 'border', normalized(setBorder, normalizeCss));
      updateProp(image, oldData, newData, 'borderStyle', normalized(setBorderStyle, normalizeCss));
      updateAlt(image, oldData, newData);
    };

    var normalizeCss = function (editor, cssText) {
      var css = editor.dom.styles.parse(cssText);
      var mergedCss = mergeMargins(css);
      var compressed = editor.dom.styles.parse(editor.dom.styles.serialize(mergedCss));
      return editor.dom.styles.serialize(compressed);
    };
    var getSelectedImage = function (editor) {
      var imgElm = editor.selection.getNode();
      var figureElm = editor.dom.getParent(imgElm, 'figure.image');
      if (figureElm) {
        return editor.dom.select('img', figureElm)[0];
      }
      if (imgElm && (imgElm.nodeName !== 'IMG' || isPlaceholderImage(imgElm))) {
        return null;
      }
      return imgElm;
    };
    var splitTextBlock = function (editor, figure) {
      var dom = editor.dom;
      var textBlockElements = filter(editor.schema.getTextBlockElements(), function (_, parentElm) {
        return !editor.schema.isValidChild(parentElm, 'figure');
      });
      var textBlock = dom.getParent(figure.parentNode, function (node) {
        return hasNonNullableKey(textBlockElements, node.nodeName);
      }, editor.getBody());
      if (textBlock) {
        return dom.split(textBlock, figure);
      } else {
        return figure;
      }
    };
    var readImageDataFromSelection = function (editor) {
      var image = getSelectedImage(editor);
      return image ? read(function (css) {
        return normalizeCss(editor, css);
      }, image) : defaultData();
    };
    var insertImageAtCaret = function (editor, data) {
      var elm = create(function (css) {
        return normalizeCss(editor, css);
      }, data);
      editor.dom.setAttrib(elm, 'data-mce-id', '__mcenew');
      editor.focus();
      editor.selection.setContent(elm.outerHTML);
      var insertedElm = editor.dom.select('*[data-mce-id="__mcenew"]')[0];
      editor.dom.setAttrib(insertedElm, 'data-mce-id', null);
      if (isFigure(insertedElm)) {
        var figure = splitTextBlock(editor, insertedElm);
        editor.selection.select(figure);
      } else {
        editor.selection.select(insertedElm);
      }
    };
    var syncSrcAttr = function (editor, image) {
      editor.dom.setAttrib(image, 'src', image.getAttribute('src'));
    };
    var deleteImage = function (editor, image) {
      if (image) {
        var elm = editor.dom.is(image.parentNode, 'figure.image') ? image.parentNode : image;
        editor.dom.remove(elm);
        editor.focus();
        editor.nodeChanged();
        if (editor.dom.isEmpty(editor.getBody())) {
          editor.setContent('');
          editor.selection.setCursorLocation();
        }
      }
    };
    var writeImageDataToSelection = function (editor, data) {
      var image = getSelectedImage(editor);
      write(function (css) {
        return normalizeCss(editor, css);
      }, data, image);
      syncSrcAttr(editor, image);
      if (isFigure(image.parentNode)) {
        var figure = image.parentNode;
        splitTextBlock(editor, figure);
        editor.selection.select(image.parentNode);
      } else {
        editor.selection.select(image);
        waitLoadImage(editor, data, image);
      }
    };
    var insertOrUpdateImage = function (editor, partialData) {
      var image = getSelectedImage(editor);
      if (image) {
        var selectedImageData = read(function (css) {
          return normalizeCss(editor, css);
        }, image);
        var data = __assign(__assign({}, selectedImageData), partialData);
        if (data.src) {
          writeImageDataToSelection(editor, data);
        } else {
          deleteImage(editor, image);
        }
      } else if (partialData.src) {
        insertImageAtCaret(editor, __assign(__assign({}, defaultData()), partialData));
      }
    };

    var hasOwnProperty$1 = Object.prototype.hasOwnProperty;
    var deep = function (old, nu) {
      var bothObjects = isObject(old) && isObject(nu);
      return bothObjects ? deepMerge(old, nu) : nu;
    };
    var baseMerge = function (merger) {
      return function () {
        var objects = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          objects[_i] = arguments[_i];
        }
        if (objects.length === 0) {
          throw new Error('Can\'t merge zero objects');
        }
        var ret = {};
        for (var j = 0; j < objects.length; j++) {
          var curObject = objects[j];
          for (var key in curObject) {
            if (hasOwnProperty$1.call(curObject, key)) {
              ret[key] = merger(ret[key], curObject[key]);
            }
          }
        }
        return ret;
      };
    };
    var deepMerge = baseMerge(deep);

    var isNotEmpty = function (s) {
      return s.length > 0;
    };

    var global$4 = tinymce.util.Tools.resolve('tinymce.util.ImageUploader');

    var global$5 = tinymce.util.Tools.resolve('tinymce.util.Tools');

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
      global$5.each(list, function (item) {
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
    var sanitizer = function (extracter) {
      if (extracter === void 0) {
        extracter = getValue;
      }
      return function (list) {
        if (list) {
          return Optional.from(list).map(function (list) {
            return sanitizeList(list, extracter);
          });
        } else {
          return Optional.none();
        }
      };
    };
    var sanitize = function (list) {
      return sanitizer(getValue)(list);
    };
    var isGroup = function (item) {
      return Object.prototype.hasOwnProperty.call(item, 'items');
    };
    var findEntryDelegate = function (list, value) {
      return findMap(list, function (item) {
        if (isGroup(item)) {
          return findEntryDelegate(item.items, value);
        } else if (item.value === value) {
          return Optional.some(item);
        } else {
          return Optional.none();
        }
      });
    };
    var findEntry = function (optList, value) {
      return optList.bind(function (list) {
        return findEntryDelegate(list, value);
      });
    };
    var ListUtils = {
      sanitizer: sanitizer,
      sanitize: sanitize,
      findEntry: findEntry
    };

    var makeTab = function (_info) {
      return {
        title: 'Advanced',
        name: 'advanced',
        items: [
          {
            type: 'input',
            label: 'Style',
            name: 'style'
          },
          {
            type: 'grid',
            columns: 2,
            items: [
              {
                type: 'input',
                label: 'Vertical space',
                name: 'vspace',
                inputMode: 'numeric'
              },
              {
                type: 'input',
                label: 'Horizontal space',
                name: 'hspace',
                inputMode: 'numeric'
              },
              {
                type: 'input',
                label: 'Border width',
                name: 'border',
                inputMode: 'numeric'
              },
              {
                type: 'listbox',
                name: 'borderstyle',
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
              }
            ]
          }
        ]
      };
    };
    var AdvTab = { makeTab: makeTab };

    var collect = function (editor) {
      var urlListSanitizer = ListUtils.sanitizer(function (item) {
        return editor.convertURL(item.value || item.url, 'src');
      });
      var futureImageList = new global$2(function (completer) {
        createImageList(editor, function (imageList) {
          completer(urlListSanitizer(imageList).map(function (items) {
            return flatten([
              [{
                  text: 'None',
                  value: ''
                }],
              items
            ]);
          }));
        });
      });
      var classList = ListUtils.sanitize(getClassList(editor));
      var hasAdvTab$1 = hasAdvTab(editor);
      var hasUploadTab$1 = hasUploadTab(editor);
      var hasUploadUrl$1 = hasUploadUrl(editor);
      var hasUploadHandler$1 = hasUploadHandler(editor);
      var image = readImageDataFromSelection(editor);
      var hasDescription$1 = hasDescription(editor);
      var hasImageTitle$1 = hasImageTitle(editor);
      var hasDimensions$1 = hasDimensions(editor);
      var hasImageCaption$1 = hasImageCaption(editor);
      var hasAccessibilityOptions = showAccessibilityOptions(editor);
      var automaticUploads = isAutomaticUploadsEnabled(editor);
      var prependURL = Optional.some(getPrependUrl(editor)).filter(function (preUrl) {
        return isString(preUrl) && preUrl.length > 0;
      });
      return futureImageList.then(function (imageList) {
        return {
          image: image,
          imageList: imageList,
          classList: classList,
          hasAdvTab: hasAdvTab$1,
          hasUploadTab: hasUploadTab$1,
          hasUploadUrl: hasUploadUrl$1,
          hasUploadHandler: hasUploadHandler$1,
          hasDescription: hasDescription$1,
          hasImageTitle: hasImageTitle$1,
          hasDimensions: hasDimensions$1,
          hasImageCaption: hasImageCaption$1,
          prependURL: prependURL,
          hasAccessibilityOptions: hasAccessibilityOptions,
          automaticUploads: automaticUploads
        };
      });
    };

    var makeItems = function (info) {
      var imageUrl = {
        name: 'src',
        type: 'urlinput',
        filetype: 'image',
        label: 'Source'
      };
      var imageList = info.imageList.map(function (items) {
        return {
          name: 'images',
          type: 'listbox',
          label: 'Image list',
          items: items
        };
      });
      var imageDescription = {
        name: 'alt',
        type: 'input',
        label: 'Alternative description',
        disabled: info.hasAccessibilityOptions && info.image.isDecorative
      };
      var imageTitle = {
        name: 'title',
        type: 'input',
        label: 'Image title'
      };
      var imageDimensions = {
        name: 'dimensions',
        type: 'sizeinput'
      };
      var isDecorative = {
        type: 'label',
        label: 'Accessibility',
        items: [{
            name: 'isDecorative',
            type: 'checkbox',
            label: 'Image is decorative'
          }]
      };
      var classList = info.classList.map(function (items) {
        return {
          name: 'classes',
          type: 'listbox',
          label: 'Class',
          items: items
        };
      });
      var caption = {
        type: 'label',
        label: 'Caption',
        items: [{
            type: 'checkbox',
            name: 'caption',
            label: 'Show caption'
          }]
      };
      var getDialogContainerType = function (useColumns) {
        return useColumns ? {
          type: 'grid',
          columns: 2
        } : { type: 'panel' };
      };
      return flatten([
        [imageUrl],
        imageList.toArray(),
        info.hasAccessibilityOptions && info.hasDescription ? [isDecorative] : [],
        info.hasDescription ? [imageDescription] : [],
        info.hasImageTitle ? [imageTitle] : [],
        info.hasDimensions ? [imageDimensions] : [],
        [__assign(__assign({}, getDialogContainerType(info.classList.isSome() && info.hasImageCaption)), {
            items: flatten([
              classList.toArray(),
              info.hasImageCaption ? [caption] : []
            ])
          })]
      ]);
    };
    var makeTab$1 = function (info) {
      return {
        title: 'General',
        name: 'general',
        items: makeItems(info)
      };
    };
    var MainTab = {
      makeTab: makeTab$1,
      makeItems: makeItems
    };

    var makeTab$2 = function (_info) {
      var items = [{
          type: 'dropzone',
          name: 'fileinput'
        }];
      return {
        title: 'Upload',
        name: 'upload',
        items: items
      };
    };
    var UploadTab = { makeTab: makeTab$2 };

    var createState = function (info) {
      return {
        prevImage: ListUtils.findEntry(info.imageList, info.image.src),
        prevAlt: info.image.alt,
        open: true
      };
    };
    var fromImageData = function (image) {
      return {
        src: {
          value: image.src,
          meta: {}
        },
        images: image.src,
        alt: image.alt,
        title: image.title,
        dimensions: {
          width: image.width,
          height: image.height
        },
        classes: image.class,
        caption: image.caption,
        style: image.style,
        vspace: image.vspace,
        border: image.border,
        hspace: image.hspace,
        borderstyle: image.borderStyle,
        fileinput: [],
        isDecorative: image.isDecorative
      };
    };
    var toImageData = function (data, removeEmptyAlt) {
      return {
        src: data.src.value,
        alt: data.alt.length === 0 && removeEmptyAlt ? null : data.alt,
        title: data.title,
        width: data.dimensions.width,
        height: data.dimensions.height,
        class: data.classes,
        style: data.style,
        caption: data.caption,
        hspace: data.hspace,
        vspace: data.vspace,
        border: data.border,
        borderStyle: data.borderstyle,
        isDecorative: data.isDecorative
      };
    };
    var addPrependUrl2 = function (info, srcURL) {
      if (!/^(?:[a-zA-Z]+:)?\/\//.test(srcURL)) {
        return info.prependURL.bind(function (prependUrl) {
          if (srcURL.substring(0, prependUrl.length) !== prependUrl) {
            return Optional.some(prependUrl + srcURL);
          }
          return Optional.none();
        });
      }
      return Optional.none();
    };
    var addPrependUrl = function (info, api) {
      var data = api.getData();
      addPrependUrl2(info, data.src.value).each(function (srcURL) {
        api.setData({
          src: {
            value: srcURL,
            meta: data.src.meta
          }
        });
      });
    };
    var formFillFromMeta2 = function (info, data, meta) {
      if (info.hasDescription && isString(meta.alt)) {
        data.alt = meta.alt;
      }
      if (info.hasAccessibilityOptions) {
        data.isDecorative = meta.isDecorative || data.isDecorative || false;
      }
      if (info.hasImageTitle && isString(meta.title)) {
        data.title = meta.title;
      }
      if (info.hasDimensions) {
        if (isString(meta.width)) {
          data.dimensions.width = meta.width;
        }
        if (isString(meta.height)) {
          data.dimensions.height = meta.height;
        }
      }
      if (isString(meta.class)) {
        ListUtils.findEntry(info.classList, meta.class).each(function (entry) {
          data.classes = entry.value;
        });
      }
      if (info.hasImageCaption) {
        if (isBoolean(meta.caption)) {
          data.caption = meta.caption;
        }
      }
      if (info.hasAdvTab) {
        if (isString(meta.style)) {
          data.style = meta.style;
        }
        if (isString(meta.vspace)) {
          data.vspace = meta.vspace;
        }
        if (isString(meta.border)) {
          data.border = meta.border;
        }
        if (isString(meta.hspace)) {
          data.hspace = meta.hspace;
        }
        if (isString(meta.borderstyle)) {
          data.borderstyle = meta.borderstyle;
        }
      }
    };
    var formFillFromMeta = function (info, api) {
      var data = api.getData();
      var meta = data.src.meta;
      if (meta !== undefined) {
        var newData = deepMerge({}, data);
        formFillFromMeta2(info, newData, meta);
        api.setData(newData);
      }
    };
    var calculateImageSize = function (helpers, info, state, api) {
      var data = api.getData();
      var url = data.src.value;
      var meta = data.src.meta || {};
      if (!meta.width && !meta.height && info.hasDimensions) {
        if (isNotEmpty(url)) {
          helpers.imageSize(url).then(function (size) {
            if (state.open) {
              api.setData({ dimensions: size });
            }
          }).catch(function (e) {
            return console.error(e);
          });
        } else {
          api.setData({
            dimensions: {
              width: '',
              height: ''
            }
          });
        }
      }
    };
    var updateImagesDropdown = function (info, state, api) {
      var data = api.getData();
      var image = ListUtils.findEntry(info.imageList, data.src.value);
      state.prevImage = image;
      api.setData({
        images: image.map(function (entry) {
          return entry.value;
        }).getOr('')
      });
    };
    var changeSrc = function (helpers, info, state, api) {
      addPrependUrl(info, api);
      formFillFromMeta(info, api);
      calculateImageSize(helpers, info, state, api);
      updateImagesDropdown(info, state, api);
    };
    var changeImages = function (helpers, info, state, api) {
      var data = api.getData();
      var image = ListUtils.findEntry(info.imageList, data.images);
      image.each(function (img) {
        var updateAlt = data.alt === '' || state.prevImage.map(function (image) {
          return image.text === data.alt;
        }).getOr(false);
        if (updateAlt) {
          if (img.value === '') {
            api.setData({
              src: img,
              alt: state.prevAlt
            });
          } else {
            api.setData({
              src: img,
              alt: img.text
            });
          }
        } else {
          api.setData({ src: img });
        }
      });
      state.prevImage = image;
      changeSrc(helpers, info, state, api);
    };
    var calcVSpace = function (css) {
      var matchingTopBottom = css['margin-top'] && css['margin-bottom'] && css['margin-top'] === css['margin-bottom'];
      return matchingTopBottom ? removePixelSuffix(String(css['margin-top'])) : '';
    };
    var calcHSpace = function (css) {
      var matchingLeftRight = css['margin-right'] && css['margin-left'] && css['margin-right'] === css['margin-left'];
      return matchingLeftRight ? removePixelSuffix(String(css['margin-right'])) : '';
    };
    var calcBorderWidth = function (css) {
      return css['border-width'] ? removePixelSuffix(String(css['border-width'])) : '';
    };
    var calcBorderStyle = function (css) {
      return css['border-style'] ? String(css['border-style']) : '';
    };
    var calcStyle = function (parseStyle, serializeStyle, css) {
      return serializeStyle(parseStyle(serializeStyle(css)));
    };
    var changeStyle2 = function (parseStyle, serializeStyle, data) {
      var css = mergeMargins(parseStyle(data.style));
      var dataCopy = deepMerge({}, data);
      dataCopy.vspace = calcVSpace(css);
      dataCopy.hspace = calcHSpace(css);
      dataCopy.border = calcBorderWidth(css);
      dataCopy.borderstyle = calcBorderStyle(css);
      dataCopy.style = calcStyle(parseStyle, serializeStyle, css);
      return dataCopy;
    };
    var changeStyle = function (helpers, api) {
      var data = api.getData();
      var newData = changeStyle2(helpers.parseStyle, helpers.serializeStyle, data);
      api.setData(newData);
    };
    var changeAStyle = function (helpers, info, api) {
      var data = deepMerge(fromImageData(info.image), api.getData());
      var style = getStyleValue(helpers.normalizeCss, toImageData(data, false));
      api.setData({ style: style });
    };
    var changeFileInput = function (helpers, info, state, api) {
      var data = api.getData();
      api.block('Uploading image');
      head(data.fileinput).fold(function () {
        api.unblock();
      }, function (file) {
        var blobUri = URL.createObjectURL(file);
        var finalize = function () {
          api.unblock();
          URL.revokeObjectURL(blobUri);
        };
        var updateSrcAndSwitchTab = function (url) {
          api.setData({
            src: {
              value: url,
              meta: {}
            }
          });
          api.showTab('general');
          changeSrc(helpers, info, state, api);
        };
        blobToDataUri(file).then(function (dataUrl) {
          var blobInfo = helpers.createBlobCache(file, blobUri, dataUrl);
          if (info.automaticUploads) {
            helpers.uploadImage(blobInfo).then(function (result) {
              updateSrcAndSwitchTab(result.url);
              finalize();
            }).catch(function (err) {
              finalize();
              helpers.alertErr(err);
            });
          } else {
            helpers.addToBlobCache(blobInfo);
            updateSrcAndSwitchTab(blobInfo.blobUri());
            api.unblock();
          }
        });
      });
    };
    var changeHandler = function (helpers, info, state) {
      return function (api, evt) {
        if (evt.name === 'src') {
          changeSrc(helpers, info, state, api);
        } else if (evt.name === 'images') {
          changeImages(helpers, info, state, api);
        } else if (evt.name === 'alt') {
          state.prevAlt = api.getData().alt;
        } else if (evt.name === 'style') {
          changeStyle(helpers, api);
        } else if (evt.name === 'vspace' || evt.name === 'hspace' || evt.name === 'border' || evt.name === 'borderstyle') {
          changeAStyle(helpers, info, api);
        } else if (evt.name === 'fileinput') {
          changeFileInput(helpers, info, state, api);
        } else if (evt.name === 'isDecorative') {
          if (api.getData().isDecorative) {
            api.disable('alt');
          } else {
            api.enable('alt');
          }
        }
      };
    };
    var closeHandler = function (state) {
      return function () {
        state.open = false;
      };
    };
    var makeDialogBody = function (info) {
      if (info.hasAdvTab || info.hasUploadUrl || info.hasUploadHandler) {
        var tabPanel = {
          type: 'tabpanel',
          tabs: flatten([
            [MainTab.makeTab(info)],
            info.hasAdvTab ? [AdvTab.makeTab(info)] : [],
            info.hasUploadTab && (info.hasUploadUrl || info.hasUploadHandler) ? [UploadTab.makeTab(info)] : []
          ])
        };
        return tabPanel;
      } else {
        var panel = {
          type: 'panel',
          items: MainTab.makeItems(info)
        };
        return panel;
      }
    };
    var makeDialog = function (helpers) {
      return function (info) {
        var state = createState(info);
        return {
          title: 'Insert/Edit Image',
          size: 'normal',
          body: makeDialogBody(info),
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
          initialData: fromImageData(info.image),
          onSubmit: helpers.onSubmit(info),
          onChange: changeHandler(helpers, info, state),
          onClose: closeHandler(state)
        };
      };
    };
    var submitHandler = function (editor) {
      return function (info) {
        return function (api) {
          var data = deepMerge(fromImageData(info.image), api.getData());
          editor.execCommand('mceUpdateImage', false, toImageData(data, info.hasAccessibilityOptions));
          editor.editorUpload.uploadImagesAuto();
          api.close();
        };
      };
    };
    var imageSize = function (editor) {
      return function (url) {
        return getImageSize(editor.documentBaseURI.toAbsolute(url)).then(function (dimensions) {
          return {
            width: String(dimensions.width),
            height: String(dimensions.height)
          };
        });
      };
    };
    var createBlobCache = function (editor) {
      return function (file, blobUri, dataUrl) {
        return editor.editorUpload.blobCache.create({
          blob: file,
          blobUri: blobUri,
          name: file.name ? file.name.replace(/\.[^\.]+$/, '') : null,
          filename: file.name,
          base64: dataUrl.split(',')[1]
        });
      };
    };
    var addToBlobCache = function (editor) {
      return function (blobInfo) {
        editor.editorUpload.blobCache.add(blobInfo);
      };
    };
    var alertErr = function (editor) {
      return function (message) {
        editor.windowManager.alert(message);
      };
    };
    var normalizeCss$1 = function (editor) {
      return function (cssText) {
        return normalizeCss(editor, cssText);
      };
    };
    var parseStyle = function (editor) {
      return function (cssText) {
        return editor.dom.parseStyle(cssText);
      };
    };
    var serializeStyle = function (editor) {
      return function (stylesArg, name) {
        return editor.dom.serializeStyle(stylesArg, name);
      };
    };
    var uploadImage = function (editor) {
      return function (blobInfo) {
        return global$4(editor).upload([blobInfo], false).then(function (results) {
          if (results.length === 0) {
            return global$2.reject('Failed to upload image');
          } else if (results[0].status === false) {
            return global$2.reject(results[0].error.message);
          } else {
            return results[0];
          }
        });
      };
    };
    var Dialog = function (editor) {
      var helpers = {
        onSubmit: submitHandler(editor),
        imageSize: imageSize(editor),
        addToBlobCache: addToBlobCache(editor),
        createBlobCache: createBlobCache(editor),
        alertErr: alertErr(editor),
        normalizeCss: normalizeCss$1(editor),
        parseStyle: parseStyle(editor),
        serializeStyle: serializeStyle(editor),
        uploadImage: uploadImage(editor)
      };
      var open = function () {
        collect(editor).then(makeDialog(helpers)).then(editor.windowManager.open);
      };
      return { open: open };
    };

    var register = function (editor) {
      editor.addCommand('mceImage', Dialog(editor).open);
      editor.addCommand('mceUpdateImage', function (_ui, data) {
        editor.undoManager.transact(function () {
          return insertOrUpdateImage(editor, data);
        });
      });
    };

    var hasImageClass = function (node) {
      var className = node.attr('class');
      return className && /\bimage\b/.test(className);
    };
    var toggleContentEditableState = function (state) {
      return function (nodes) {
        var i = nodes.length;
        var toggleContentEditable = function (node) {
          node.attr('contenteditable', state ? 'true' : null);
        };
        while (i--) {
          var node = nodes[i];
          if (hasImageClass(node)) {
            node.attr('contenteditable', state ? 'false' : null);
            global$5.each(node.getAll('figcaption'), toggleContentEditable);
          }
        }
      };
    };
    var setup = function (editor) {
      editor.on('PreInit', function () {
        editor.parser.addNodeFilter('figure', toggleContentEditableState(true));
        editor.serializer.addNodeFilter('figure', toggleContentEditableState(false));
      });
    };

    var register$1 = function (editor) {
      editor.ui.registry.addToggleButton('image', {
        icon: 'image',
        tooltip: 'Insert/edit image',
        onAction: Dialog(editor).open,
        onSetup: function (buttonApi) {
          return editor.selection.selectorChangedWithUnbind('img:not([data-mce-object],[data-mce-placeholder]),figure.image', buttonApi.setActive).unbind;
        }
      });
      editor.ui.registry.addMenuItem('image', {
        icon: 'image',
        text: 'Image...',
        onAction: Dialog(editor).open
      });
      editor.ui.registry.addContextMenu('image', {
        update: function (element) {
          return isFigure(element) || isImage(element) && !isPlaceholderImage(element) ? ['image'] : [];
        }
      });
    };

    function Plugin () {
      global.add('image', function (editor) {
        setup(editor);
        register$1(editor);
        register(editor);
      });
    }

    Plugin();

}());
