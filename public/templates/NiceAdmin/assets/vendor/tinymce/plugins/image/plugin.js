/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    var global$4 = tinymce.util.Tools.resolve('tinymce.PluginManager');

    const getPrototypeOf = Object.getPrototypeOf;
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
    const isType = type => value => typeOf(value) === type;
    const isSimpleType = type => value => typeof value === type;
    const eq = t => a => t === a;
    const is = (value, constructor) => isObject(value) && hasProto(value, constructor, (o, proto) => getPrototypeOf(o) === proto);
    const isString = isType('string');
    const isObject = isType('object');
    const isPlainObject = value => is(value, Object);
    const isArray = isType('array');
    const isNull = eq(null);
    const isBoolean = isSimpleType('boolean');
    const isNullable = a => a === null || a === undefined;
    const isNonNullable = a => !isNullable(a);
    const isFunction = isSimpleType('function');
    const isNumber = isSimpleType('number');
    const isArrayOf = (value, pred) => {
      if (isArray(value)) {
        for (let i = 0, len = value.length; i < len; ++i) {
          if (!pred(value[i])) {
            return false;
          }
        }
        return true;
      }
      return false;
    };

    const noop = () => {
    };

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
    const each = (obj, f) => {
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
      each(obj, (x, i) => {
        (pred(x, i) ? onTrue : onFalse)(x, i);
      });
    };
    const filter = (obj, pred) => {
      const t = {};
      internalFilter(obj, pred, objAcc(t), noop);
      return t;
    };
    const has = (obj, key) => hasOwnProperty.call(obj, key);
    const hasNonNullableKey = (obj, key) => has(obj, key) && obj[key] !== undefined && obj[key] !== null;

    const nativePush = Array.prototype.push;
    const flatten = xs => {
      const r = [];
      for (let i = 0, len = xs.length; i < len; ++i) {
        if (!isArray(xs[i])) {
          throw new Error('Arr.flatten item ' + i + ' was not an array, input: ' + xs);
        }
        nativePush.apply(r, xs[i]);
      }
      return r;
    };
    const get = (xs, i) => i >= 0 && i < xs.length ? Optional.some(xs[i]) : Optional.none();
    const head = xs => get(xs, 0);
    const findMap = (arr, f) => {
      for (let i = 0; i < arr.length; i++) {
        const r = f(arr[i], i);
        if (r.isSome()) {
          return r;
        }
      }
      return Optional.none();
    };

    typeof window !== 'undefined' ? window : Function('return this;')();

    const rawSet = (dom, key, value) => {
      if (isString(value) || isBoolean(value) || isNumber(value)) {
        dom.setAttribute(key, value + '');
      } else {
        console.error('Invalid call to Attribute.set. Key ', key, ':: Value ', value, ':: Element ', dom);
        throw new Error('Attribute value was not simple');
      }
    };
    const set = (element, key, value) => {
      rawSet(element.dom, key, value);
    };
    const remove = (element, key) => {
      element.dom.removeAttribute(key);
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
      return fromDom(div.childNodes[0]);
    };
    const fromTag = (tag, scope) => {
      const doc = scope || document;
      const node = doc.createElement(tag);
      return fromDom(node);
    };
    const fromText = (text, scope) => {
      const doc = scope || document;
      const node = doc.createTextNode(text);
      return fromDom(node);
    };
    const fromDom = node => {
      if (node === null || node === undefined) {
        throw new Error('Node cannot be null or undefined');
      }
      return { dom: node };
    };
    const fromPoint = (docElm, x, y) => Optional.from(docElm.dom.elementFromPoint(x, y)).map(fromDom);
    const SugarElement = {
      fromHtml,
      fromTag,
      fromText,
      fromDom,
      fromPoint
    };

    var global$3 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');

    var global$2 = tinymce.util.Tools.resolve('tinymce.util.URI');

    const isNotEmpty = s => s.length > 0;

    const option = name => editor => editor.options.get(name);
    const register$2 = editor => {
      const registerOption = editor.options.register;
      registerOption('image_dimensions', {
        processor: 'boolean',
        default: true
      });
      registerOption('image_advtab', {
        processor: 'boolean',
        default: false
      });
      registerOption('image_uploadtab', {
        processor: 'boolean',
        default: true
      });
      registerOption('image_prepend_url', {
        processor: 'string',
        default: ''
      });
      registerOption('image_class_list', { processor: 'object[]' });
      registerOption('image_description', {
        processor: 'boolean',
        default: true
      });
      registerOption('image_title', {
        processor: 'boolean',
        default: false
      });
      registerOption('image_caption', {
        processor: 'boolean',
        default: false
      });
      registerOption('image_list', {
        processor: value => {
          const valid = value === false || isString(value) || isArrayOf(value, isObject) || isFunction(value);
          return valid ? {
            value,
            valid
          } : {
            valid: false,
            message: 'Must be false, a string, an array or a function.'
          };
        },
        default: false
      });
    };
    const hasDimensions = option('image_dimensions');
    const hasAdvTab = option('image_advtab');
    const hasUploadTab = option('image_uploadtab');
    const getPrependUrl = option('image_prepend_url');
    const getClassList = option('image_class_list');
    const hasDescription = option('image_description');
    const hasImageTitle = option('image_title');
    const hasImageCaption = option('image_caption');
    const getImageList = option('image_list');
    const showAccessibilityOptions = option('a11y_advanced_options');
    const isAutomaticUploadsEnabled = option('automatic_uploads');
    const hasUploadUrl = editor => isNotEmpty(editor.options.get('images_upload_url'));
    const hasUploadHandler = editor => isNonNullable(editor.options.get('images_upload_handler'));

    const parseIntAndGetMax = (val1, val2) => Math.max(parseInt(val1, 10), parseInt(val2, 10));
    const getImageSize = url => new Promise(callback => {
      const img = document.createElement('img');
      const done = dimensions => {
        img.onload = img.onerror = null;
        if (img.parentNode) {
          img.parentNode.removeChild(img);
        }
        callback(dimensions);
      };
      img.onload = () => {
        const width = parseIntAndGetMax(img.width, img.clientWidth);
        const height = parseIntAndGetMax(img.height, img.clientHeight);
        const dimensions = {
          width,
          height
        };
        done(Promise.resolve(dimensions));
      };
      img.onerror = () => {
        done(Promise.reject(`Failed to get image dimensions for: ${ url }`));
      };
      const style = img.style;
      style.visibility = 'hidden';
      style.position = 'fixed';
      style.bottom = style.left = '0px';
      style.width = style.height = 'auto';
      document.body.appendChild(img);
      img.src = url;
    });
    const removePixelSuffix = value => {
      if (value) {
        value = value.replace(/px$/, '');
      }
      return value;
    };
    const addPixelSuffix = value => {
      if (value.length > 0 && /^[0-9]+$/.test(value)) {
        value += 'px';
      }
      return value;
    };
    const mergeMargins = css => {
      if (css.margin) {
        const splitMargin = String(css.margin).split(' ');
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
    const createImageList = (editor, callback) => {
      const imageList = getImageList(editor);
      if (isString(imageList)) {
        fetch(imageList).then(res => {
          if (res.ok) {
            res.json().then(callback);
          }
        });
      } else if (isFunction(imageList)) {
        imageList(callback);
      } else {
        callback(imageList);
      }
    };
    const waitLoadImage = (editor, data, imgElm) => {
      const selectImage = () => {
        imgElm.onload = imgElm.onerror = null;
        if (editor.selection) {
          editor.selection.select(imgElm);
          editor.nodeChanged();
        }
      };
      imgElm.onload = () => {
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
    const blobToDataUri = blob => new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => {
        resolve(reader.result);
      };
      reader.onerror = () => {
        var _a;
        reject((_a = reader.error) === null || _a === void 0 ? void 0 : _a.message);
      };
      reader.readAsDataURL(blob);
    });
    const isPlaceholderImage = imgElm => imgElm.nodeName === 'IMG' && (imgElm.hasAttribute('data-mce-object') || imgElm.hasAttribute('data-mce-placeholder'));
    const isSafeImageUrl = (editor, src) => {
      const getOption = editor.options.get;
      return global$2.isDomSafe(src, 'img', {
        allow_html_data_urls: getOption('allow_html_data_urls'),
        allow_script_urls: getOption('allow_script_urls'),
        allow_svg_data_urls: getOption('allow_svg_data_urls')
      });
    };

    const DOM = global$3.DOM;
    const getHspace = image => {
      if (image.style.marginLeft && image.style.marginRight && image.style.marginLeft === image.style.marginRight) {
        return removePixelSuffix(image.style.marginLeft);
      } else {
        return '';
      }
    };
    const getVspace = image => {
      if (image.style.marginTop && image.style.marginBottom && image.style.marginTop === image.style.marginBottom) {
        return removePixelSuffix(image.style.marginTop);
      } else {
        return '';
      }
    };
    const getBorder = image => {
      if (image.style.borderWidth) {
        return removePixelSuffix(image.style.borderWidth);
      } else {
        return '';
      }
    };
    const getAttrib = (image, name) => {
      var _a;
      if (image.hasAttribute(name)) {
        return (_a = image.getAttribute(name)) !== null && _a !== void 0 ? _a : '';
      } else {
        return '';
      }
    };
    const hasCaption = image => image.parentNode !== null && image.parentNode.nodeName === 'FIGURE';
    const updateAttrib = (image, name, value) => {
      if (value === '' || value === null) {
        image.removeAttribute(name);
      } else {
        image.setAttribute(name, value);
      }
    };
    const wrapInFigure = image => {
      const figureElm = DOM.create('figure', { class: 'image' });
      DOM.insertAfter(figureElm, image);
      figureElm.appendChild(image);
      figureElm.appendChild(DOM.create('figcaption', { contentEditable: 'true' }, 'Caption'));
      figureElm.contentEditable = 'false';
    };
    const removeFigure = image => {
      const figureElm = image.parentNode;
      if (isNonNullable(figureElm)) {
        DOM.insertAfter(image, figureElm);
        DOM.remove(figureElm);
      }
    };
    const toggleCaption = image => {
      if (hasCaption(image)) {
        removeFigure(image);
      } else {
        wrapInFigure(image);
      }
    };
    const normalizeStyle = (image, normalizeCss) => {
      const attrValue = image.getAttribute('style');
      const value = normalizeCss(attrValue !== null ? attrValue : '');
      if (value.length > 0) {
        image.setAttribute('style', value);
        image.setAttribute('data-mce-style', value);
      } else {
        image.removeAttribute('style');
      }
    };
    const setSize = (name, normalizeCss) => (image, name, value) => {
      const styles = image.style;
      if (styles[name]) {
        styles[name] = addPixelSuffix(value);
        normalizeStyle(image, normalizeCss);
      } else {
        updateAttrib(image, name, value);
      }
    };
    const getSize = (image, name) => {
      if (image.style[name]) {
        return removePixelSuffix(image.style[name]);
      } else {
        return getAttrib(image, name);
      }
    };
    const setHspace = (image, value) => {
      const pxValue = addPixelSuffix(value);
      image.style.marginLeft = pxValue;
      image.style.marginRight = pxValue;
    };
    const setVspace = (image, value) => {
      const pxValue = addPixelSuffix(value);
      image.style.marginTop = pxValue;
      image.style.marginBottom = pxValue;
    };
    const setBorder = (image, value) => {
      const pxValue = addPixelSuffix(value);
      image.style.borderWidth = pxValue;
    };
    const setBorderStyle = (image, value) => {
      image.style.borderStyle = value;
    };
    const getBorderStyle = image => {
      var _a;
      return (_a = image.style.borderStyle) !== null && _a !== void 0 ? _a : '';
    };
    const isFigure = elm => isNonNullable(elm) && elm.nodeName === 'FIGURE';
    const isImage = elm => elm.nodeName === 'IMG';
    const getIsDecorative = image => DOM.getAttrib(image, 'alt').length === 0 && DOM.getAttrib(image, 'role') === 'presentation';
    const getAlt = image => {
      if (getIsDecorative(image)) {
        return '';
      } else {
        return getAttrib(image, 'alt');
      }
    };
    const defaultData = () => ({
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
    });
    const getStyleValue = (normalizeCss, data) => {
      var _a;
      const image = document.createElement('img');
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
      return normalizeCss((_a = image.getAttribute('style')) !== null && _a !== void 0 ? _a : '');
    };
    const create = (normalizeCss, data) => {
      const image = document.createElement('img');
      write(normalizeCss, {
        ...data,
        caption: false
      }, image);
      setAlt(image, data.alt, data.isDecorative);
      if (data.caption) {
        const figure = DOM.create('figure', { class: 'image' });
        figure.appendChild(image);
        figure.appendChild(DOM.create('figcaption', { contentEditable: 'true' }, 'Caption'));
        figure.contentEditable = 'false';
        return figure;
      } else {
        return image;
      }
    };
    const read = (normalizeCss, image) => ({
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
      borderStyle: getBorderStyle(image),
      isDecorative: getIsDecorative(image)
    });
    const updateProp = (image, oldData, newData, name, set) => {
      if (newData[name] !== oldData[name]) {
        set(image, name, String(newData[name]));
      }
    };
    const setAlt = (image, alt, isDecorative) => {
      if (isDecorative) {
        DOM.setAttrib(image, 'role', 'presentation');
        const sugarImage = SugarElement.fromDom(image);
        set(sugarImage, 'alt', '');
      } else {
        if (isNull(alt)) {
          const sugarImage = SugarElement.fromDom(image);
          remove(sugarImage, 'alt');
        } else {
          const sugarImage = SugarElement.fromDom(image);
          set(sugarImage, 'alt', alt);
        }
        if (DOM.getAttrib(image, 'role') === 'presentation') {
          DOM.setAttrib(image, 'role', '');
        }
      }
    };
    const updateAlt = (image, oldData, newData) => {
      if (newData.alt !== oldData.alt || newData.isDecorative !== oldData.isDecorative) {
        setAlt(image, newData.alt, newData.isDecorative);
      }
    };
    const normalized = (set, normalizeCss) => (image, name, value) => {
      set(image, value);
      normalizeStyle(image, normalizeCss);
    };
    const write = (normalizeCss, newData, image) => {
      const oldData = read(normalizeCss, image);
      updateProp(image, oldData, newData, 'caption', (image, _name, _value) => toggleCaption(image));
      updateProp(image, oldData, newData, 'src', updateAttrib);
      updateProp(image, oldData, newData, 'title', updateAttrib);
      updateProp(image, oldData, newData, 'width', setSize('width', normalizeCss));
      updateProp(image, oldData, newData, 'height', setSize('height', normalizeCss));
      updateProp(image, oldData, newData, 'class', updateAttrib);
      updateProp(image, oldData, newData, 'style', normalized((image, value) => updateAttrib(image, 'style', value), normalizeCss));
      updateProp(image, oldData, newData, 'hspace', normalized(setHspace, normalizeCss));
      updateProp(image, oldData, newData, 'vspace', normalized(setVspace, normalizeCss));
      updateProp(image, oldData, newData, 'border', normalized(setBorder, normalizeCss));
      updateProp(image, oldData, newData, 'borderStyle', normalized(setBorderStyle, normalizeCss));
      updateAlt(image, oldData, newData);
    };

    const normalizeCss$1 = (editor, cssText) => {
      const css = editor.dom.styles.parse(cssText);
      const mergedCss = mergeMargins(css);
      const compressed = editor.dom.styles.parse(editor.dom.styles.serialize(mergedCss));
      return editor.dom.styles.serialize(compressed);
    };
    const getSelectedImage = editor => {
      const imgElm = editor.selection.getNode();
      const figureElm = editor.dom.getParent(imgElm, 'figure.image');
      if (figureElm) {
        return editor.dom.select('img', figureElm)[0];
      }
      if (imgElm && (imgElm.nodeName !== 'IMG' || isPlaceholderImage(imgElm))) {
        return null;
      }
      return imgElm;
    };
    const splitTextBlock = (editor, figure) => {
      var _a;
      const dom = editor.dom;
      const textBlockElements = filter(editor.schema.getTextBlockElements(), (_, parentElm) => !editor.schema.isValidChild(parentElm, 'figure'));
      const textBlock = dom.getParent(figure.parentNode, node => hasNonNullableKey(textBlockElements, node.nodeName), editor.getBody());
      if (textBlock) {
        return (_a = dom.split(textBlock, figure)) !== null && _a !== void 0 ? _a : figure;
      } else {
        return figure;
      }
    };
    const readImageDataFromSelection = editor => {
      const image = getSelectedImage(editor);
      return image ? read(css => normalizeCss$1(editor, css), image) : defaultData();
    };
    const insertImageAtCaret = (editor, data) => {
      const elm = create(css => normalizeCss$1(editor, css), data);
      editor.dom.setAttrib(elm, 'data-mce-id', '__mcenew');
      editor.focus();
      editor.selection.setContent(elm.outerHTML);
      const insertedElm = editor.dom.select('*[data-mce-id="__mcenew"]')[0];
      editor.dom.setAttrib(insertedElm, 'data-mce-id', null);
      if (isFigure(insertedElm)) {
        const figure = splitTextBlock(editor, insertedElm);
        editor.selection.select(figure);
      } else {
        editor.selection.select(insertedElm);
      }
    };
    const syncSrcAttr = (editor, image) => {
      editor.dom.setAttrib(image, 'src', image.getAttribute('src'));
    };
    const deleteImage = (editor, image) => {
      if (image) {
        const elm = editor.dom.is(image.parentNode, 'figure.image') ? image.parentNode : image;
        editor.dom.remove(elm);
        editor.focus();
        editor.nodeChanged();
        if (editor.dom.isEmpty(editor.getBody())) {
          editor.setContent('');
          editor.selection.setCursorLocation();
        }
      }
    };
    const writeImageDataToSelection = (editor, data) => {
      const image = getSelectedImage(editor);
      if (image) {
        write(css => normalizeCss$1(editor, css), data, image);
        syncSrcAttr(editor, image);
        if (isFigure(image.parentNode)) {
          const figure = image.parentNode;
          splitTextBlock(editor, figure);
          editor.selection.select(image.parentNode);
        } else {
          editor.selection.select(image);
          waitLoadImage(editor, data, image);
        }
      }
    };
    const sanitizeImageData = (editor, data) => {
      const src = data.src;
      return {
        ...data,
        src: isSafeImageUrl(editor, src) ? src : ''
      };
    };
    const insertOrUpdateImage = (editor, partialData) => {
      const image = getSelectedImage(editor);
      if (image) {
        const selectedImageData = read(css => normalizeCss$1(editor, css), image);
        const data = {
          ...selectedImageData,
          ...partialData
        };
        const sanitizedData = sanitizeImageData(editor, data);
        if (data.src) {
          writeImageDataToSelection(editor, sanitizedData);
        } else {
          deleteImage(editor, image);
        }
      } else if (partialData.src) {
        insertImageAtCaret(editor, {
          ...defaultData(),
          ...partialData
        });
      }
    };

    const deep = (old, nu) => {
      const bothObjects = isPlainObject(old) && isPlainObject(nu);
      return bothObjects ? deepMerge(old, nu) : nu;
    };
    const baseMerge = merger => {
      return (...objects) => {
        if (objects.length === 0) {
          throw new Error(`Can't merge zero objects`);
        }
        const ret = {};
        for (let j = 0; j < objects.length; j++) {
          const curObject = objects[j];
          for (const key in curObject) {
            if (has(curObject, key)) {
              ret[key] = merger(ret[key], curObject[key]);
            }
          }
        }
        return ret;
      };
    };
    const deepMerge = baseMerge(deep);

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.ImageUploader');

    var global = tinymce.util.Tools.resolve('tinymce.util.Tools');

    const getValue = item => isString(item.value) ? item.value : '';
    const getText = item => {
      if (isString(item.text)) {
        return item.text;
      } else if (isString(item.title)) {
        return item.title;
      } else {
        return '';
      }
    };
    const sanitizeList = (list, extractValue) => {
      const out = [];
      global.each(list, item => {
        const text = getText(item);
        if (item.menu !== undefined) {
          const items = sanitizeList(item.menu, extractValue);
          out.push({
            text,
            items
          });
        } else {
          const value = extractValue(item);
          out.push({
            text,
            value
          });
        }
      });
      return out;
    };
    const sanitizer = (extractor = getValue) => list => {
      if (list) {
        return Optional.from(list).map(list => sanitizeList(list, extractor));
      } else {
        return Optional.none();
      }
    };
    const sanitize = list => sanitizer(getValue)(list);
    const isGroup = item => has(item, 'items');
    const findEntryDelegate = (list, value) => findMap(list, item => {
      if (isGroup(item)) {
        return findEntryDelegate(item.items, value);
      } else if (item.value === value) {
        return Optional.some(item);
      } else {
        return Optional.none();
      }
    });
    const findEntry = (optList, value) => optList.bind(list => findEntryDelegate(list, value));
    const ListUtils = {
      sanitizer,
      sanitize,
      findEntry
    };

    const makeTab$2 = _info => ({
      title: 'Advanced',
      name: 'advanced',
      items: [{
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
        }]
    });
    const AdvTab = { makeTab: makeTab$2 };

    const collect = editor => {
      const urlListSanitizer = ListUtils.sanitizer(item => editor.convertURL(item.value || item.url || '', 'src'));
      const futureImageList = new Promise(completer => {
        createImageList(editor, imageList => {
          completer(urlListSanitizer(imageList).map(items => flatten([
            [{
                text: 'None',
                value: ''
              }],
            items
          ])));
        });
      });
      const classList = ListUtils.sanitize(getClassList(editor));
      const hasAdvTab$1 = hasAdvTab(editor);
      const hasUploadTab$1 = hasUploadTab(editor);
      const hasUploadUrl$1 = hasUploadUrl(editor);
      const hasUploadHandler$1 = hasUploadHandler(editor);
      const image = readImageDataFromSelection(editor);
      const hasDescription$1 = hasDescription(editor);
      const hasImageTitle$1 = hasImageTitle(editor);
      const hasDimensions$1 = hasDimensions(editor);
      const hasImageCaption$1 = hasImageCaption(editor);
      const hasAccessibilityOptions = showAccessibilityOptions(editor);
      const automaticUploads = isAutomaticUploadsEnabled(editor);
      const prependURL = Optional.some(getPrependUrl(editor)).filter(preUrl => isString(preUrl) && preUrl.length > 0);
      return futureImageList.then(imageList => ({
        image,
        imageList,
        classList,
        hasAdvTab: hasAdvTab$1,
        hasUploadTab: hasUploadTab$1,
        hasUploadUrl: hasUploadUrl$1,
        hasUploadHandler: hasUploadHandler$1,
        hasDescription: hasDescription$1,
        hasImageTitle: hasImageTitle$1,
        hasDimensions: hasDimensions$1,
        hasImageCaption: hasImageCaption$1,
        prependURL,
        hasAccessibilityOptions,
        automaticUploads
      }));
    };

    const makeItems = info => {
      const imageUrl = {
        name: 'src',
        type: 'urlinput',
        filetype: 'image',
        label: 'Source'
      };
      const imageList = info.imageList.map(items => ({
        name: 'images',
        type: 'listbox',
        label: 'Image list',
        items
      }));
      const imageDescription = {
        name: 'alt',
        type: 'input',
        label: 'Alternative description',
        enabled: !(info.hasAccessibilityOptions && info.image.isDecorative)
      };
      const imageTitle = {
        name: 'title',
        type: 'input',
        label: 'Image title'
      };
      const imageDimensions = {
        name: 'dimensions',
        type: 'sizeinput'
      };
      const isDecorative = {
        type: 'label',
        label: 'Accessibility',
        items: [{
            name: 'isDecorative',
            type: 'checkbox',
            label: 'Image is decorative'
          }]
      };
      const classList = info.classList.map(items => ({
        name: 'classes',
        type: 'listbox',
        label: 'Class',
        items
      }));
      const caption = {
        type: 'label',
        label: 'Caption',
        items: [{
            type: 'checkbox',
            name: 'caption',
            label: 'Show caption'
          }]
      };
      const getDialogContainerType = useColumns => useColumns ? {
        type: 'grid',
        columns: 2
      } : { type: 'panel' };
      return flatten([
        [imageUrl],
        imageList.toArray(),
        info.hasAccessibilityOptions && info.hasDescription ? [isDecorative] : [],
        info.hasDescription ? [imageDescription] : [],
        info.hasImageTitle ? [imageTitle] : [],
        info.hasDimensions ? [imageDimensions] : [],
        [{
            ...getDialogContainerType(info.classList.isSome() && info.hasImageCaption),
            items: flatten([
              classList.toArray(),
              info.hasImageCaption ? [caption] : []
            ])
          }]
      ]);
    };
    const makeTab$1 = info => ({
      title: 'General',
      name: 'general',
      items: makeItems(info)
    });
    const MainTab = {
      makeTab: makeTab$1,
      makeItems
    };

    const makeTab = _info => {
      const items = [{
          type: 'dropzone',
          name: 'fileinput'
        }];
      return {
        title: 'Upload',
        name: 'upload',
        items
      };
    };
    const UploadTab = { makeTab };

    const createState = info => ({
      prevImage: ListUtils.findEntry(info.imageList, info.image.src),
      prevAlt: info.image.alt,
      open: true
    });
    const fromImageData = image => ({
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
    });
    const toImageData = (data, removeEmptyAlt) => ({
      src: data.src.value,
      alt: (data.alt === null || data.alt.length === 0) && removeEmptyAlt ? null : data.alt,
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
    });
    const addPrependUrl2 = (info, srcURL) => {
      if (!/^(?:[a-zA-Z]+:)?\/\//.test(srcURL)) {
        return info.prependURL.bind(prependUrl => {
          if (srcURL.substring(0, prependUrl.length) !== prependUrl) {
            return Optional.some(prependUrl + srcURL);
          }
          return Optional.none();
        });
      }
      return Optional.none();
    };
    const addPrependUrl = (info, api) => {
      const data = api.getData();
      addPrependUrl2(info, data.src.value).each(srcURL => {
        api.setData({
          src: {
            value: srcURL,
            meta: data.src.meta
          }
        });
      });
    };
    const formFillFromMeta2 = (info, data, meta) => {
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
        ListUtils.findEntry(info.classList, meta.class).each(entry => {
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
    const formFillFromMeta = (info, api) => {
      const data = api.getData();
      const meta = data.src.meta;
      if (meta !== undefined) {
        const newData = deepMerge({}, data);
        formFillFromMeta2(info, newData, meta);
        api.setData(newData);
      }
    };
    const calculateImageSize = (helpers, info, state, api) => {
      const data = api.getData();
      const url = data.src.value;
      const meta = data.src.meta || {};
      if (!meta.width && !meta.height && info.hasDimensions) {
        if (isNotEmpty(url)) {
          helpers.imageSize(url).then(size => {
            if (state.open) {
              api.setData({ dimensions: size });
            }
          }).catch(e => console.error(e));
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
    const updateImagesDropdown = (info, state, api) => {
      const data = api.getData();
      const image = ListUtils.findEntry(info.imageList, data.src.value);
      state.prevImage = image;
      api.setData({ images: image.map(entry => entry.value).getOr('') });
    };
    const changeSrc = (helpers, info, state, api) => {
      addPrependUrl(info, api);
      formFillFromMeta(info, api);
      calculateImageSize(helpers, info, state, api);
      updateImagesDropdown(info, state, api);
    };
    const changeImages = (helpers, info, state, api) => {
      const data = api.getData();
      const image = ListUtils.findEntry(info.imageList, data.images);
      image.each(img => {
        const updateAlt = data.alt === '' || state.prevImage.map(image => image.text === data.alt).getOr(false);
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
    const changeFileInput = (helpers, info, state, api) => {
      const data = api.getData();
      api.block('Uploading image');
      head(data.fileinput).fold(() => {
        api.unblock();
      }, file => {
        const blobUri = URL.createObjectURL(file);
        const finalize = () => {
          api.unblock();
          URL.revokeObjectURL(blobUri);
        };
        const updateSrcAndSwitchTab = url => {
          api.setData({
            src: {
              value: url,
              meta: {}
            }
          });
          api.showTab('general');
          changeSrc(helpers, info, state, api);
        };
        blobToDataUri(file).then(dataUrl => {
          const blobInfo = helpers.createBlobCache(file, blobUri, dataUrl);
          if (info.automaticUploads) {
            helpers.uploadImage(blobInfo).then(result => {
              updateSrcAndSwitchTab(result.url);
              finalize();
            }).catch(err => {
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
    const changeHandler = (helpers, info, state) => (api, evt) => {
      if (evt.name === 'src') {
        changeSrc(helpers, info, state, api);
      } else if (evt.name === 'images') {
        changeImages(helpers, info, state, api);
      } else if (evt.name === 'alt') {
        state.prevAlt = api.getData().alt;
      } else if (evt.name === 'fileinput') {
        changeFileInput(helpers, info, state, api);
      } else if (evt.name === 'isDecorative') {
        api.setEnabled('alt', !api.getData().isDecorative);
      }
    };
    const closeHandler = state => () => {
      state.open = false;
    };
    const makeDialogBody = info => {
      if (info.hasAdvTab || info.hasUploadUrl || info.hasUploadHandler) {
        const tabPanel = {
          type: 'tabpanel',
          tabs: flatten([
            [MainTab.makeTab(info)],
            info.hasAdvTab ? [AdvTab.makeTab(info)] : [],
            info.hasUploadTab && (info.hasUploadUrl || info.hasUploadHandler) ? [UploadTab.makeTab(info)] : []
          ])
        };
        return tabPanel;
      } else {
        const panel = {
          type: 'panel',
          items: MainTab.makeItems(info)
        };
        return panel;
      }
    };
    const submitHandler = (editor, info, helpers) => api => {
      const data = deepMerge(fromImageData(info.image), api.getData());
      const finalData = {
        ...data,
        style: getStyleValue(helpers.normalizeCss, toImageData(data, false))
      };
      editor.execCommand('mceUpdateImage', false, toImageData(finalData, info.hasAccessibilityOptions));
      editor.editorUpload.uploadImagesAuto();
      api.close();
    };
    const imageSize = editor => url => {
      if (!isSafeImageUrl(editor, url)) {
        return Promise.resolve({
          width: '',
          height: ''
        });
      } else {
        return getImageSize(editor.documentBaseURI.toAbsolute(url)).then(dimensions => ({
          width: String(dimensions.width),
          height: String(dimensions.height)
        }));
      }
    };
    const createBlobCache = editor => (file, blobUri, dataUrl) => {
      var _a;
      return editor.editorUpload.blobCache.create({
        blob: file,
        blobUri,
        name: (_a = file.name) === null || _a === void 0 ? void 0 : _a.replace(/\.[^\.]+$/, ''),
        filename: file.name,
        base64: dataUrl.split(',')[1]
      });
    };
    const addToBlobCache = editor => blobInfo => {
      editor.editorUpload.blobCache.add(blobInfo);
    };
    const alertErr = editor => message => {
      editor.windowManager.alert(message);
    };
    const normalizeCss = editor => cssText => normalizeCss$1(editor, cssText);
    const parseStyle = editor => cssText => editor.dom.parseStyle(cssText);
    const serializeStyle = editor => (stylesArg, name) => editor.dom.serializeStyle(stylesArg, name);
    const uploadImage = editor => blobInfo => global$1(editor).upload([blobInfo], false).then(results => {
      var _a;
      if (results.length === 0) {
        return Promise.reject('Failed to upload image');
      } else if (results[0].status === false) {
        return Promise.reject((_a = results[0].error) === null || _a === void 0 ? void 0 : _a.message);
      } else {
        return results[0];
      }
    });
    const Dialog = editor => {
      const helpers = {
        imageSize: imageSize(editor),
        addToBlobCache: addToBlobCache(editor),
        createBlobCache: createBlobCache(editor),
        alertErr: alertErr(editor),
        normalizeCss: normalizeCss(editor),
        parseStyle: parseStyle(editor),
        serializeStyle: serializeStyle(editor),
        uploadImage: uploadImage(editor)
      };
      const open = () => {
        collect(editor).then(info => {
          const state = createState(info);
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
            onSubmit: submitHandler(editor, info, helpers),
            onChange: changeHandler(helpers, info, state),
            onClose: closeHandler(state)
          };
        }).then(editor.windowManager.open);
      };
      return { open };
    };

    const register$1 = editor => {
      editor.addCommand('mceImage', Dialog(editor).open);
      editor.addCommand('mceUpdateImage', (_ui, data) => {
        editor.undoManager.transact(() => insertOrUpdateImage(editor, data));
      });
    };

    const hasImageClass = node => {
      const className = node.attr('class');
      return isNonNullable(className) && /\bimage\b/.test(className);
    };
    const toggleContentEditableState = state => nodes => {
      let i = nodes.length;
      const toggleContentEditable = node => {
        node.attr('contenteditable', state ? 'true' : null);
      };
      while (i--) {
        const node = nodes[i];
        if (hasImageClass(node)) {
          node.attr('contenteditable', state ? 'false' : null);
          global.each(node.getAll('figcaption'), toggleContentEditable);
        }
      }
    };
    const setup = editor => {
      editor.on('PreInit', () => {
        editor.parser.addNodeFilter('figure', toggleContentEditableState(true));
        editor.serializer.addNodeFilter('figure', toggleContentEditableState(false));
      });
    };

    const register = editor => {
      editor.ui.registry.addToggleButton('image', {
        icon: 'image',
        tooltip: 'Insert/edit image',
        onAction: Dialog(editor).open,
        onSetup: buttonApi => {
          buttonApi.setActive(isNonNullable(getSelectedImage(editor)));
          return editor.selection.selectorChangedWithUnbind('img:not([data-mce-object]):not([data-mce-placeholder]),figure.image', buttonApi.setActive).unbind;
        }
      });
      editor.ui.registry.addMenuItem('image', {
        icon: 'image',
        text: 'Image...',
        onAction: Dialog(editor).open
      });
      editor.ui.registry.addContextMenu('image', { update: element => isFigure(element) || isImage(element) && !isPlaceholderImage(element) ? ['image'] : [] });
    };

    var Plugin = () => {
      global$4.add('image', editor => {
        register$2(editor);
        setup(editor);
        register(editor);
        register$1(editor);
      });
    };

    Plugin();

})();
