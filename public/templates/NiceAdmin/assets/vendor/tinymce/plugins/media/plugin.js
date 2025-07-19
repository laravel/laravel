/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    var global$6 = tinymce.util.Tools.resolve('tinymce.PluginManager');

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
    const isString = isType('string');
    const isObject = isType('object');
    const isArray = isType('array');
    const isNullable = a => a === null || a === undefined;
    const isNonNullable = a => !isNullable(a);

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

    const nativePush = Array.prototype.push;
    const each$1 = (xs, f) => {
      for (let i = 0, len = xs.length; i < len; i++) {
        const x = xs[i];
        f(x, i);
      }
    };
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
    const get$1 = (obj, key) => {
      return has(obj, key) ? Optional.from(obj[key]) : Optional.none();
    };
    const has = (obj, key) => hasOwnProperty.call(obj, key);

    const option = name => editor => editor.options.get(name);
    const register$2 = editor => {
      const registerOption = editor.options.register;
      registerOption('audio_template_callback', { processor: 'function' });
      registerOption('video_template_callback', { processor: 'function' });
      registerOption('iframe_template_callback', { processor: 'function' });
      registerOption('media_live_embeds', {
        processor: 'boolean',
        default: true
      });
      registerOption('media_filter_html', {
        processor: 'boolean',
        default: true
      });
      registerOption('media_url_resolver', { processor: 'function' });
      registerOption('media_alt_source', {
        processor: 'boolean',
        default: true
      });
      registerOption('media_poster', {
        processor: 'boolean',
        default: true
      });
      registerOption('media_dimensions', {
        processor: 'boolean',
        default: true
      });
    };
    const getAudioTemplateCallback = option('audio_template_callback');
    const getVideoTemplateCallback = option('video_template_callback');
    const getIframeTemplateCallback = option('iframe_template_callback');
    const hasLiveEmbeds = option('media_live_embeds');
    const shouldFilterHtml = option('media_filter_html');
    const getUrlResolver = option('media_url_resolver');
    const hasAltSource = option('media_alt_source');
    const hasPoster = option('media_poster');
    const hasDimensions = option('media_dimensions');

    var global$5 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    var global$4 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');

    var global$3 = tinymce.util.Tools.resolve('tinymce.html.DomParser');

    const DOM$1 = global$4.DOM;
    const trimPx = value => value.replace(/px$/, '');
    const getEphoxEmbedData = node => {
      const style = node.attr('style');
      const styles = style ? DOM$1.parseStyle(style) : {};
      return {
        type: 'ephox-embed-iri',
        source: node.attr('data-ephox-embed-iri'),
        altsource: '',
        poster: '',
        width: get$1(styles, 'max-width').map(trimPx).getOr(''),
        height: get$1(styles, 'max-height').map(trimPx).getOr('')
      };
    };
    const htmlToData = (html, schema) => {
      let data = {};
      const parser = global$3({
        validate: false,
        forced_root_block: false
      }, schema);
      const rootNode = parser.parse(html);
      for (let node = rootNode; node; node = node.walk()) {
        if (node.type === 1) {
          const name = node.name;
          if (node.attr('data-ephox-embed-iri')) {
            data = getEphoxEmbedData(node);
            break;
          } else {
            if (!data.source && name === 'param') {
              data.source = node.attr('movie');
            }
            if (name === 'iframe' || name === 'object' || name === 'embed' || name === 'video' || name === 'audio') {
              if (!data.type) {
                data.type = name;
              }
              data = global$5.extend(node.attributes.map, data);
            }
            if (name === 'script') {
              data = {
                type: 'script',
                source: node.attr('src')
              };
            }
            if (name === 'source') {
              if (!data.source) {
                data.source = node.attr('src');
              } else if (!data.altsource) {
                data.altsource = node.attr('src');
              }
            }
            if (name === 'img' && !data.poster) {
              data.poster = node.attr('src');
            }
          }
        }
      }
      data.source = data.source || data.src || '';
      data.altsource = data.altsource || '';
      data.poster = data.poster || '';
      return data;
    };

    const guess = url => {
      var _a;
      const mimes = {
        mp3: 'audio/mpeg',
        m4a: 'audio/x-m4a',
        wav: 'audio/wav',
        mp4: 'video/mp4',
        webm: 'video/webm',
        ogg: 'video/ogg',
        swf: 'application/x-shockwave-flash'
      };
      const fileEnd = (_a = url.toLowerCase().split('.').pop()) !== null && _a !== void 0 ? _a : '';
      return get$1(mimes, fileEnd).getOr('');
    };

    var global$2 = tinymce.util.Tools.resolve('tinymce.html.Node');

    var global$1 = tinymce.util.Tools.resolve('tinymce.html.Serializer');

    const Parser = (schema, settings = {}) => global$3({
      forced_root_block: false,
      validate: false,
      allow_conditional_comments: true,
      ...settings
    }, schema);

    const DOM = global$4.DOM;
    const addPx = value => /^[0-9.]+$/.test(value) ? value + 'px' : value;
    const updateEphoxEmbed = (data, node) => {
      const style = node.attr('style');
      const styleMap = style ? DOM.parseStyle(style) : {};
      if (isNonNullable(data.width)) {
        styleMap['max-width'] = addPx(data.width);
      }
      if (isNonNullable(data.height)) {
        styleMap['max-height'] = addPx(data.height);
      }
      node.attr('style', DOM.serializeStyle(styleMap));
    };
    const sources = [
      'source',
      'altsource'
    ];
    const updateHtml = (html, data, updateAll, schema) => {
      let numSources = 0;
      let sourceCount = 0;
      const parser = Parser(schema);
      parser.addNodeFilter('source', nodes => numSources = nodes.length);
      const rootNode = parser.parse(html);
      for (let node = rootNode; node; node = node.walk()) {
        if (node.type === 1) {
          const name = node.name;
          if (node.attr('data-ephox-embed-iri')) {
            updateEphoxEmbed(data, node);
            break;
          } else {
            switch (name) {
            case 'video':
            case 'object':
            case 'embed':
            case 'img':
            case 'iframe':
              if (data.height !== undefined && data.width !== undefined) {
                node.attr('width', data.width);
                node.attr('height', data.height);
              }
              break;
            }
            if (updateAll) {
              switch (name) {
              case 'video':
                node.attr('poster', data.poster);
                node.attr('src', null);
                for (let index = numSources; index < 2; index++) {
                  if (data[sources[index]]) {
                    const source = new global$2('source', 1);
                    source.attr('src', data[sources[index]]);
                    source.attr('type', data[sources[index] + 'mime'] || null);
                    node.append(source);
                  }
                }
                break;
              case 'iframe':
                node.attr('src', data.source);
                break;
              case 'object':
                const hasImage = node.getAll('img').length > 0;
                if (data.poster && !hasImage) {
                  node.attr('src', data.poster);
                  const img = new global$2('img', 1);
                  img.attr('src', data.poster);
                  img.attr('width', data.width);
                  img.attr('height', data.height);
                  node.append(img);
                }
                break;
              case 'source':
                if (sourceCount < 2) {
                  node.attr('src', data[sources[sourceCount]]);
                  node.attr('type', data[sources[sourceCount] + 'mime'] || null);
                  if (!data[sources[sourceCount]]) {
                    node.remove();
                    continue;
                  }
                }
                sourceCount++;
                break;
              case 'img':
                if (!data.poster) {
                  node.remove();
                }
                break;
              }
            }
          }
        }
      }
      return global$1({}, schema).serialize(rootNode);
    };

    const urlPatterns = [
      {
        regex: /youtu\.be\/([\w\-_\?&=.]+)/i,
        type: 'iframe',
        w: 560,
        h: 314,
        url: 'www.youtube.com/embed/$1',
        allowFullscreen: true
      },
      {
        regex: /youtube\.com(.+)v=([^&]+)(&([a-z0-9&=\-_]+))?/i,
        type: 'iframe',
        w: 560,
        h: 314,
        url: 'www.youtube.com/embed/$2?$4',
        allowFullscreen: true
      },
      {
        regex: /youtube.com\/embed\/([a-z0-9\?&=\-_]+)/i,
        type: 'iframe',
        w: 560,
        h: 314,
        url: 'www.youtube.com/embed/$1',
        allowFullscreen: true
      },
      {
        regex: /vimeo\.com\/([0-9]+)/,
        type: 'iframe',
        w: 425,
        h: 350,
        url: 'player.vimeo.com/video/$1?title=0&byline=0&portrait=0&color=8dc7dc',
        allowFullscreen: true
      },
      {
        regex: /vimeo\.com\/(.*)\/([0-9]+)/,
        type: 'iframe',
        w: 425,
        h: 350,
        url: 'player.vimeo.com/video/$2?title=0&amp;byline=0',
        allowFullscreen: true
      },
      {
        regex: /maps\.google\.([a-z]{2,3})\/maps\/(.+)msid=(.+)/,
        type: 'iframe',
        w: 425,
        h: 350,
        url: 'maps.google.com/maps/ms?msid=$2&output=embed"',
        allowFullscreen: false
      },
      {
        regex: /dailymotion\.com\/video\/([^_]+)/,
        type: 'iframe',
        w: 480,
        h: 270,
        url: 'www.dailymotion.com/embed/video/$1',
        allowFullscreen: true
      },
      {
        regex: /dai\.ly\/([^_]+)/,
        type: 'iframe',
        w: 480,
        h: 270,
        url: 'www.dailymotion.com/embed/video/$1',
        allowFullscreen: true
      }
    ];
    const getProtocol = url => {
      const protocolMatches = url.match(/^(https?:\/\/|www\.)(.+)$/i);
      if (protocolMatches && protocolMatches.length > 1) {
        return protocolMatches[1] === 'www.' ? 'https://' : protocolMatches[1];
      } else {
        return 'https://';
      }
    };
    const getUrl = (pattern, url) => {
      const protocol = getProtocol(url);
      const match = pattern.regex.exec(url);
      let newUrl = protocol + pattern.url;
      if (isNonNullable(match)) {
        for (let i = 0; i < match.length; i++) {
          newUrl = newUrl.replace('$' + i, () => match[i] ? match[i] : '');
        }
      }
      return newUrl.replace(/\?$/, '');
    };
    const matchPattern = url => {
      const patterns = urlPatterns.filter(pattern => pattern.regex.test(url));
      if (patterns.length > 0) {
        return global$5.extend({}, patterns[0], { url: getUrl(patterns[0], url) });
      } else {
        return null;
      }
    };

    const getIframeHtml = (data, iframeTemplateCallback) => {
      if (iframeTemplateCallback) {
        return iframeTemplateCallback(data);
      } else {
        const allowFullscreen = data.allowfullscreen ? ' allowFullscreen="1"' : '';
        return '<iframe src="' + data.source + '" width="' + data.width + '" height="' + data.height + '"' + allowFullscreen + '></iframe>';
      }
    };
    const getFlashHtml = data => {
      let html = '<object data="' + data.source + '" width="' + data.width + '" height="' + data.height + '" type="application/x-shockwave-flash">';
      if (data.poster) {
        html += '<img src="' + data.poster + '" width="' + data.width + '" height="' + data.height + '" />';
      }
      html += '</object>';
      return html;
    };
    const getAudioHtml = (data, audioTemplateCallback) => {
      if (audioTemplateCallback) {
        return audioTemplateCallback(data);
      } else {
        return '<audio controls="controls" src="' + data.source + '">' + (data.altsource ? '\n<source src="' + data.altsource + '"' + (data.altsourcemime ? ' type="' + data.altsourcemime + '"' : '') + ' />\n' : '') + '</audio>';
      }
    };
    const getVideoHtml = (data, videoTemplateCallback) => {
      if (videoTemplateCallback) {
        return videoTemplateCallback(data);
      } else {
        return '<video width="' + data.width + '" height="' + data.height + '"' + (data.poster ? ' poster="' + data.poster + '"' : '') + ' controls="controls">\n' + '<source src="' + data.source + '"' + (data.sourcemime ? ' type="' + data.sourcemime + '"' : '') + ' />\n' + (data.altsource ? '<source src="' + data.altsource + '"' + (data.altsourcemime ? ' type="' + data.altsourcemime + '"' : '') + ' />\n' : '') + '</video>';
      }
    };
    const getScriptHtml = data => {
      return '<script src="' + data.source + '"></script>';
    };
    const dataToHtml = (editor, dataIn) => {
      var _a;
      const data = global$5.extend({}, dataIn);
      if (!data.source) {
        global$5.extend(data, htmlToData((_a = data.embed) !== null && _a !== void 0 ? _a : '', editor.schema));
        if (!data.source) {
          return '';
        }
      }
      if (!data.altsource) {
        data.altsource = '';
      }
      if (!data.poster) {
        data.poster = '';
      }
      data.source = editor.convertURL(data.source, 'source');
      data.altsource = editor.convertURL(data.altsource, 'source');
      data.sourcemime = guess(data.source);
      data.altsourcemime = guess(data.altsource);
      data.poster = editor.convertURL(data.poster, 'poster');
      const pattern = matchPattern(data.source);
      if (pattern) {
        data.source = pattern.url;
        data.type = pattern.type;
        data.allowfullscreen = pattern.allowFullscreen;
        data.width = data.width || String(pattern.w);
        data.height = data.height || String(pattern.h);
      }
      if (data.embed) {
        return updateHtml(data.embed, data, true, editor.schema);
      } else {
        const audioTemplateCallback = getAudioTemplateCallback(editor);
        const videoTemplateCallback = getVideoTemplateCallback(editor);
        const iframeTemplateCallback = getIframeTemplateCallback(editor);
        data.width = data.width || '300';
        data.height = data.height || '150';
        global$5.each(data, (value, key) => {
          data[key] = editor.dom.encode('' + value);
        });
        if (data.type === 'iframe') {
          return getIframeHtml(data, iframeTemplateCallback);
        } else if (data.sourcemime === 'application/x-shockwave-flash') {
          return getFlashHtml(data);
        } else if (data.sourcemime.indexOf('audio') !== -1) {
          return getAudioHtml(data, audioTemplateCallback);
        } else if (data.type === 'script') {
          return getScriptHtml(data);
        } else {
          return getVideoHtml(data, videoTemplateCallback);
        }
      }
    };

    const isMediaElement = element => element.hasAttribute('data-mce-object') || element.hasAttribute('data-ephox-embed-iri');
    const setup$2 = editor => {
      editor.on('click keyup touchend', () => {
        const selectedNode = editor.selection.getNode();
        if (selectedNode && editor.dom.hasClass(selectedNode, 'mce-preview-object')) {
          if (editor.dom.getAttrib(selectedNode, 'data-mce-selected')) {
            selectedNode.setAttribute('data-mce-selected', '2');
          }
        }
      });
      editor.on('ObjectSelected', e => {
        const objectType = e.target.getAttribute('data-mce-object');
        if (objectType === 'script') {
          e.preventDefault();
        }
      });
      editor.on('ObjectResized', e => {
        const target = e.target;
        if (target.getAttribute('data-mce-object')) {
          let html = target.getAttribute('data-mce-html');
          if (html) {
            html = unescape(html);
            target.setAttribute('data-mce-html', escape(updateHtml(html, {
              width: String(e.width),
              height: String(e.height)
            }, false, editor.schema)));
          }
        }
      });
    };

    const cache = {};
    const embedPromise = (data, dataToHtml, handler) => {
      return new Promise((res, rej) => {
        const wrappedResolve = response => {
          if (response.html) {
            cache[data.source] = response;
          }
          return res({
            url: data.source,
            html: response.html ? response.html : dataToHtml(data)
          });
        };
        if (cache[data.source]) {
          wrappedResolve(cache[data.source]);
        } else {
          handler({ url: data.source }, wrappedResolve, rej);
        }
      });
    };
    const defaultPromise = (data, dataToHtml) => Promise.resolve({
      html: dataToHtml(data),
      url: data.source
    });
    const loadedData = editor => data => dataToHtml(editor, data);
    const getEmbedHtml = (editor, data) => {
      const embedHandler = getUrlResolver(editor);
      return embedHandler ? embedPromise(data, loadedData(editor), embedHandler) : defaultPromise(data, loadedData(editor));
    };
    const isCached = url => has(cache, url);

    const extractMeta = (sourceInput, data) => get$1(data, sourceInput).bind(mainData => get$1(mainData, 'meta'));
    const getValue = (data, metaData, sourceInput) => prop => {
      const getFromData = () => get$1(data, prop);
      const getFromMetaData = () => get$1(metaData, prop);
      const getNonEmptyValue = c => get$1(c, 'value').bind(v => v.length > 0 ? Optional.some(v) : Optional.none());
      const getFromValueFirst = () => getFromData().bind(child => isObject(child) ? getNonEmptyValue(child).orThunk(getFromMetaData) : getFromMetaData().orThunk(() => Optional.from(child)));
      const getFromMetaFirst = () => getFromMetaData().orThunk(() => getFromData().bind(child => isObject(child) ? getNonEmptyValue(child) : Optional.from(child)));
      return { [prop]: (prop === sourceInput ? getFromValueFirst() : getFromMetaFirst()).getOr('') };
    };
    const getDimensions = (data, metaData) => {
      const dimensions = {};
      get$1(data, 'dimensions').each(dims => {
        each$1([
          'width',
          'height'
        ], prop => {
          get$1(metaData, prop).orThunk(() => get$1(dims, prop)).each(value => dimensions[prop] = value);
        });
      });
      return dimensions;
    };
    const unwrap = (data, sourceInput) => {
      const metaData = sourceInput && sourceInput !== 'dimensions' ? extractMeta(sourceInput, data).getOr({}) : {};
      const get = getValue(data, metaData, sourceInput);
      return {
        ...get('source'),
        ...get('altsource'),
        ...get('poster'),
        ...get('embed'),
        ...getDimensions(data, metaData)
      };
    };
    const wrap = data => {
      const wrapped = {
        ...data,
        source: { value: get$1(data, 'source').getOr('') },
        altsource: { value: get$1(data, 'altsource').getOr('') },
        poster: { value: get$1(data, 'poster').getOr('') }
      };
      each$1([
        'width',
        'height'
      ], prop => {
        get$1(data, prop).each(value => {
          const dimensions = wrapped.dimensions || {};
          dimensions[prop] = value;
          wrapped.dimensions = dimensions;
        });
      });
      return wrapped;
    };
    const handleError = editor => error => {
      const errorMessage = error && error.msg ? 'Media embed handler error: ' + error.msg : 'Media embed handler threw unknown error.';
      editor.notificationManager.open({
        type: 'error',
        text: errorMessage
      });
    };
    const getEditorData = editor => {
      const element = editor.selection.getNode();
      const snippet = isMediaElement(element) ? editor.serializer.serialize(element, { selection: true }) : '';
      return {
        embed: snippet,
        ...htmlToData(snippet, editor.schema)
      };
    };
    const addEmbedHtml = (api, editor) => response => {
      if (isString(response.url) && response.url.trim().length > 0) {
        const html = response.html;
        const snippetData = htmlToData(html, editor.schema);
        const nuData = {
          ...snippetData,
          source: response.url,
          embed: html
        };
        api.setData(wrap(nuData));
      }
    };
    const selectPlaceholder = (editor, beforeObjects) => {
      const afterObjects = editor.dom.select('*[data-mce-object]');
      for (let i = 0; i < beforeObjects.length; i++) {
        for (let y = afterObjects.length - 1; y >= 0; y--) {
          if (beforeObjects[i] === afterObjects[y]) {
            afterObjects.splice(y, 1);
          }
        }
      }
      editor.selection.select(afterObjects[0]);
    };
    const handleInsert = (editor, html) => {
      const beforeObjects = editor.dom.select('*[data-mce-object]');
      editor.insertContent(html);
      selectPlaceholder(editor, beforeObjects);
      editor.nodeChanged();
    };
    const submitForm = (prevData, newData, editor) => {
      var _a;
      newData.embed = updateHtml((_a = newData.embed) !== null && _a !== void 0 ? _a : '', newData, false, editor.schema);
      if (newData.embed && (prevData.source === newData.source || isCached(newData.source))) {
        handleInsert(editor, newData.embed);
      } else {
        getEmbedHtml(editor, newData).then(response => {
          handleInsert(editor, response.html);
        }).catch(handleError(editor));
      }
    };
    const showDialog = editor => {
      const editorData = getEditorData(editor);
      const currentData = Cell(editorData);
      const initialData = wrap(editorData);
      const handleSource = (prevData, api) => {
        const serviceData = unwrap(api.getData(), 'source');
        if (prevData.source !== serviceData.source) {
          addEmbedHtml(win, editor)({
            url: serviceData.source,
            html: ''
          });
          getEmbedHtml(editor, serviceData).then(addEmbedHtml(win, editor)).catch(handleError(editor));
        }
      };
      const handleEmbed = api => {
        var _a;
        const data = unwrap(api.getData());
        const dataFromEmbed = htmlToData((_a = data.embed) !== null && _a !== void 0 ? _a : '', editor.schema);
        api.setData(wrap(dataFromEmbed));
      };
      const handleUpdate = (api, sourceInput) => {
        const data = unwrap(api.getData(), sourceInput);
        const embed = dataToHtml(editor, data);
        api.setData(wrap({
          ...data,
          embed
        }));
      };
      const mediaInput = [{
          name: 'source',
          type: 'urlinput',
          filetype: 'media',
          label: 'Source'
        }];
      const sizeInput = !hasDimensions(editor) ? [] : [{
          type: 'sizeinput',
          name: 'dimensions',
          label: 'Constrain proportions',
          constrain: true
        }];
      const generalTab = {
        title: 'General',
        name: 'general',
        items: flatten([
          mediaInput,
          sizeInput
        ])
      };
      const embedTextarea = {
        type: 'textarea',
        name: 'embed',
        label: 'Paste your embed code below:'
      };
      const embedTab = {
        title: 'Embed',
        items: [embedTextarea]
      };
      const advancedFormItems = [];
      if (hasAltSource(editor)) {
        advancedFormItems.push({
          name: 'altsource',
          type: 'urlinput',
          filetype: 'media',
          label: 'Alternative source URL'
        });
      }
      if (hasPoster(editor)) {
        advancedFormItems.push({
          name: 'poster',
          type: 'urlinput',
          filetype: 'image',
          label: 'Media poster (Image URL)'
        });
      }
      const advancedTab = {
        title: 'Advanced',
        name: 'advanced',
        items: advancedFormItems
      };
      const tabs = [
        generalTab,
        embedTab
      ];
      if (advancedFormItems.length > 0) {
        tabs.push(advancedTab);
      }
      const body = {
        type: 'tabpanel',
        tabs
      };
      const win = editor.windowManager.open({
        title: 'Insert/Edit Media',
        size: 'normal',
        body,
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
        onSubmit: api => {
          const serviceData = unwrap(api.getData());
          submitForm(currentData.get(), serviceData, editor);
          api.close();
        },
        onChange: (api, detail) => {
          switch (detail.name) {
          case 'source':
            handleSource(currentData.get(), api);
            break;
          case 'embed':
            handleEmbed(api);
            break;
          case 'dimensions':
          case 'altsource':
          case 'poster':
            handleUpdate(api, detail.name);
            break;
          }
          currentData.set(unwrap(api.getData()));
        },
        initialData
      });
    };

    const get = editor => {
      const showDialog$1 = () => {
        showDialog(editor);
      };
      return { showDialog: showDialog$1 };
    };

    const register$1 = editor => {
      const showDialog$1 = () => {
        showDialog(editor);
      };
      editor.addCommand('mceMedia', showDialog$1);
    };

    const checkRange = (str, substr, start) => substr === '' || str.length >= substr.length && str.substr(start, start + substr.length) === substr;
    const startsWith = (str, prefix) => {
      return checkRange(str, prefix, 0);
    };

    var global = tinymce.util.Tools.resolve('tinymce.Env');

    const isLiveEmbedNode = node => {
      const name = node.name;
      return name === 'iframe' || name === 'video' || name === 'audio';
    };
    const getDimension = (node, styles, dimension, defaultValue = null) => {
      const value = node.attr(dimension);
      if (isNonNullable(value)) {
        return value;
      } else if (!has(styles, dimension)) {
        return defaultValue;
      } else {
        return null;
      }
    };
    const setDimensions = (node, previewNode, styles) => {
      const useDefaults = previewNode.name === 'img' || node.name === 'video';
      const defaultWidth = useDefaults ? '300' : null;
      const fallbackHeight = node.name === 'audio' ? '30' : '150';
      const defaultHeight = useDefaults ? fallbackHeight : null;
      previewNode.attr({
        width: getDimension(node, styles, 'width', defaultWidth),
        height: getDimension(node, styles, 'height', defaultHeight)
      });
    };
    const appendNodeContent = (editor, nodeName, previewNode, html) => {
      const newNode = Parser(editor.schema).parse(html, { context: nodeName });
      while (newNode.firstChild) {
        previewNode.append(newNode.firstChild);
      }
    };
    const createPlaceholderNode = (editor, node) => {
      const name = node.name;
      const placeHolder = new global$2('img', 1);
      retainAttributesAndInnerHtml(editor, node, placeHolder);
      setDimensions(node, placeHolder, {});
      placeHolder.attr({
        'style': node.attr('style'),
        'src': global.transparentSrc,
        'data-mce-object': name,
        'class': 'mce-object mce-object-' + name
      });
      return placeHolder;
    };
    const createPreviewNode = (editor, node) => {
      var _a;
      const name = node.name;
      const previewWrapper = new global$2('span', 1);
      previewWrapper.attr({
        'contentEditable': 'false',
        'style': node.attr('style'),
        'data-mce-object': name,
        'class': 'mce-preview-object mce-object-' + name
      });
      retainAttributesAndInnerHtml(editor, node, previewWrapper);
      const styles = editor.dom.parseStyle((_a = node.attr('style')) !== null && _a !== void 0 ? _a : '');
      const previewNode = new global$2(name, 1);
      setDimensions(node, previewNode, styles);
      previewNode.attr({
        src: node.attr('src'),
        style: node.attr('style'),
        class: node.attr('class')
      });
      if (name === 'iframe') {
        previewNode.attr({
          allowfullscreen: node.attr('allowfullscreen'),
          frameborder: '0'
        });
      } else {
        const attrs = [
          'controls',
          'crossorigin',
          'currentTime',
          'loop',
          'muted',
          'poster',
          'preload'
        ];
        each$1(attrs, attrName => {
          previewNode.attr(attrName, node.attr(attrName));
        });
        const sanitizedHtml = previewWrapper.attr('data-mce-html');
        if (isNonNullable(sanitizedHtml)) {
          appendNodeContent(editor, name, previewNode, unescape(sanitizedHtml));
        }
      }
      const shimNode = new global$2('span', 1);
      shimNode.attr('class', 'mce-shim');
      previewWrapper.append(previewNode);
      previewWrapper.append(shimNode);
      return previewWrapper;
    };
    const retainAttributesAndInnerHtml = (editor, sourceNode, targetNode) => {
      var _a;
      const attribs = (_a = sourceNode.attributes) !== null && _a !== void 0 ? _a : [];
      let ai = attribs.length;
      while (ai--) {
        const attrName = attribs[ai].name;
        let attrValue = attribs[ai].value;
        if (attrName !== 'width' && attrName !== 'height' && attrName !== 'style' && !startsWith(attrName, 'data-mce-')) {
          if (attrName === 'data' || attrName === 'src') {
            attrValue = editor.convertURL(attrValue, attrName);
          }
          targetNode.attr('data-mce-p-' + attrName, attrValue);
        }
      }
      const serializer = global$1({ inner: true }, editor.schema);
      const tempNode = new global$2('div', 1);
      each$1(sourceNode.children(), child => tempNode.append(child));
      const innerHtml = serializer.serialize(tempNode);
      if (innerHtml) {
        targetNode.attr('data-mce-html', escape(innerHtml));
        targetNode.empty();
      }
    };
    const isPageEmbedWrapper = node => {
      const nodeClass = node.attr('class');
      return isString(nodeClass) && /\btiny-pageembed\b/.test(nodeClass);
    };
    const isWithinEmbedWrapper = node => {
      let tempNode = node;
      while (tempNode = tempNode.parent) {
        if (tempNode.attr('data-ephox-embed-iri') || isPageEmbedWrapper(tempNode)) {
          return true;
        }
      }
      return false;
    };
    const placeHolderConverter = editor => nodes => {
      let i = nodes.length;
      let node;
      while (i--) {
        node = nodes[i];
        if (!node.parent) {
          continue;
        }
        if (node.parent.attr('data-mce-object')) {
          continue;
        }
        if (isLiveEmbedNode(node) && hasLiveEmbeds(editor)) {
          if (!isWithinEmbedWrapper(node)) {
            node.replace(createPreviewNode(editor, node));
          }
        } else {
          if (!isWithinEmbedWrapper(node)) {
            node.replace(createPlaceholderNode(editor, node));
          }
        }
      }
    };

    const parseAndSanitize = (editor, context, html) => {
      const validate = shouldFilterHtml(editor);
      return Parser(editor.schema, { validate }).parse(html, { context });
    };

    const setup$1 = editor => {
      editor.on('PreInit', () => {
        const {schema, serializer, parser} = editor;
        const boolAttrs = schema.getBoolAttrs();
        each$1('webkitallowfullscreen mozallowfullscreen'.split(' '), name => {
          boolAttrs[name] = {};
        });
        each({ embed: ['wmode'] }, (attrs, name) => {
          const rule = schema.getElementRule(name);
          if (rule) {
            each$1(attrs, attr => {
              rule.attributes[attr] = {};
              rule.attributesOrder.push(attr);
            });
          }
        });
        parser.addNodeFilter('iframe,video,audio,object,embed,script', placeHolderConverter(editor));
        serializer.addAttributeFilter('data-mce-object', (nodes, name) => {
          var _a;
          let i = nodes.length;
          while (i--) {
            const node = nodes[i];
            if (!node.parent) {
              continue;
            }
            const realElmName = node.attr(name);
            const realElm = new global$2(realElmName, 1);
            if (realElmName !== 'audio' && realElmName !== 'script') {
              const className = node.attr('class');
              if (className && className.indexOf('mce-preview-object') !== -1 && node.firstChild) {
                realElm.attr({
                  width: node.firstChild.attr('width'),
                  height: node.firstChild.attr('height')
                });
              } else {
                realElm.attr({
                  width: node.attr('width'),
                  height: node.attr('height')
                });
              }
            }
            realElm.attr({ style: node.attr('style') });
            const attribs = (_a = node.attributes) !== null && _a !== void 0 ? _a : [];
            let ai = attribs.length;
            while (ai--) {
              const attrName = attribs[ai].name;
              if (attrName.indexOf('data-mce-p-') === 0) {
                realElm.attr(attrName.substr(11), attribs[ai].value);
              }
            }
            if (realElmName === 'script') {
              realElm.attr('type', 'text/javascript');
            }
            const innerHtml = node.attr('data-mce-html');
            if (innerHtml) {
              const fragment = parseAndSanitize(editor, realElmName, unescape(innerHtml));
              each$1(fragment.children(), child => realElm.append(child));
            }
            node.replace(realElm);
          }
        });
      });
      editor.on('SetContent', () => {
        const dom = editor.dom;
        each$1(dom.select('span.mce-preview-object'), elm => {
          if (dom.select('span.mce-shim', elm).length === 0) {
            dom.add(elm, 'span', { class: 'mce-shim' });
          }
        });
      });
    };

    const setup = editor => {
      editor.on('ResolveName', e => {
        let name;
        if (e.target.nodeType === 1 && (name = e.target.getAttribute('data-mce-object'))) {
          e.name = name;
        }
      });
    };

    const register = editor => {
      const onAction = () => editor.execCommand('mceMedia');
      editor.ui.registry.addToggleButton('media', {
        tooltip: 'Insert/edit media',
        icon: 'embed',
        onAction,
        onSetup: buttonApi => {
          const selection = editor.selection;
          buttonApi.setActive(isMediaElement(selection.getNode()));
          return selection.selectorChangedWithUnbind('img[data-mce-object],span[data-mce-object],div[data-ephox-embed-iri]', buttonApi.setActive).unbind;
        }
      });
      editor.ui.registry.addMenuItem('media', {
        icon: 'embed',
        text: 'Media...',
        onAction
      });
    };

    var Plugin = () => {
      global$6.add('media', editor => {
        register$2(editor);
        register$1(editor);
        register(editor);
        setup(editor);
        setup$1(editor);
        setup$2(editor);
        return get(editor);
      });
    };

    Plugin();

})();
