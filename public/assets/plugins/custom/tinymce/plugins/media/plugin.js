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
    var isString = isType('string');
    var isObject = isType('object');
    var isArray = isType('array');
    var isNullable = function (a) {
      return a === null || a === undefined;
    };
    var isNonNullable = function (a) {
      return !isNullable(a);
    };

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

    var nativePush = Array.prototype.push;
    var each = function (xs, f) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        f(x, i);
      }
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
    var get = function (obj, key) {
      return has(obj, key) ? Optional.from(obj[key]) : Optional.none();
    };
    var has = function (obj, key) {
      return hasOwnProperty.call(obj, key);
    };

    var getScripts = function (editor) {
      return editor.getParam('media_scripts');
    };
    var getAudioTemplateCallback = function (editor) {
      return editor.getParam('audio_template_callback');
    };
    var getVideoTemplateCallback = function (editor) {
      return editor.getParam('video_template_callback');
    };
    var hasLiveEmbeds = function (editor) {
      return editor.getParam('media_live_embeds', true);
    };
    var shouldFilterHtml = function (editor) {
      return editor.getParam('media_filter_html', true);
    };
    var getUrlResolver = function (editor) {
      return editor.getParam('media_url_resolver');
    };
    var hasAltSource = function (editor) {
      return editor.getParam('media_alt_source', true);
    };
    var hasPoster = function (editor) {
      return editor.getParam('media_poster', true);
    };
    var hasDimensions = function (editor) {
      return editor.getParam('media_dimensions', true);
    };

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    var global$2 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');

    var global$3 = tinymce.util.Tools.resolve('tinymce.html.SaxParser');

    var getVideoScriptMatch = function (prefixes, src) {
      if (prefixes) {
        for (var i = 0; i < prefixes.length; i++) {
          if (src.indexOf(prefixes[i].filter) !== -1) {
            return prefixes[i];
          }
        }
      }
    };

    var DOM = global$2.DOM;
    var trimPx = function (value) {
      return value.replace(/px$/, '');
    };
    var getEphoxEmbedData = function (attrs) {
      var style = attrs.map.style;
      var styles = style ? DOM.parseStyle(style) : {};
      return {
        type: 'ephox-embed-iri',
        source: attrs.map['data-ephox-embed-iri'],
        altsource: '',
        poster: '',
        width: get(styles, 'max-width').map(trimPx).getOr(''),
        height: get(styles, 'max-height').map(trimPx).getOr('')
      };
    };
    var htmlToData = function (prefixes, html) {
      var isEphoxEmbed = Cell(false);
      var data = {};
      global$3({
        validate: false,
        allow_conditional_comments: true,
        start: function (name, attrs) {
          if (isEphoxEmbed.get()) ; else if (has(attrs.map, 'data-ephox-embed-iri')) {
            isEphoxEmbed.set(true);
            data = getEphoxEmbedData(attrs);
          } else {
            if (!data.source && name === 'param') {
              data.source = attrs.map.movie;
            }
            if (name === 'iframe' || name === 'object' || name === 'embed' || name === 'video' || name === 'audio') {
              if (!data.type) {
                data.type = name;
              }
              data = global$1.extend(attrs.map, data);
            }
            if (name === 'script') {
              var videoScript = getVideoScriptMatch(prefixes, attrs.map.src);
              if (!videoScript) {
                return;
              }
              data = {
                type: 'script',
                source: attrs.map.src,
                width: String(videoScript.width),
                height: String(videoScript.height)
              };
            }
            if (name === 'source') {
              if (!data.source) {
                data.source = attrs.map.src;
              } else if (!data.altsource) {
                data.altsource = attrs.map.src;
              }
            }
            if (name === 'img' && !data.poster) {
              data.poster = attrs.map.src;
            }
          }
        }
      }).parse(html);
      data.source = data.source || data.src || data.data;
      data.altsource = data.altsource || '';
      data.poster = data.poster || '';
      return data;
    };

    var guess = function (url) {
      var mimes = {
        mp3: 'audio/mpeg',
        m4a: 'audio/x-m4a',
        wav: 'audio/wav',
        mp4: 'video/mp4',
        webm: 'video/webm',
        ogg: 'video/ogg',
        swf: 'application/x-shockwave-flash'
      };
      var fileEnd = url.toLowerCase().split('.').pop();
      var mime = mimes[fileEnd];
      return mime ? mime : '';
    };

    var global$4 = tinymce.util.Tools.resolve('tinymce.html.Schema');

    var global$5 = tinymce.util.Tools.resolve('tinymce.html.Writer');

    var DOM$1 = global$2.DOM;
    var addPx = function (value) {
      return /^[0-9.]+$/.test(value) ? value + 'px' : value;
    };
    var setAttributes = function (attrs, updatedAttrs) {
      each$1(updatedAttrs, function (val, name) {
        var value = '' + val;
        if (attrs.map[name]) {
          var i = attrs.length;
          while (i--) {
            var attr = attrs[i];
            if (attr.name === name) {
              if (value) {
                attrs.map[name] = value;
                attr.value = value;
              } else {
                delete attrs.map[name];
                attrs.splice(i, 1);
              }
            }
          }
        } else if (value) {
          attrs.push({
            name: name,
            value: value
          });
          attrs.map[name] = value;
        }
      });
    };
    var updateEphoxEmbed = function (data, attrs) {
      var style = attrs.map.style;
      var styleMap = style ? DOM$1.parseStyle(style) : {};
      styleMap['max-width'] = addPx(data.width);
      styleMap['max-height'] = addPx(data.height);
      setAttributes(attrs, { style: DOM$1.serializeStyle(styleMap) });
    };
    var sources = [
      'source',
      'altsource'
    ];
    var updateHtml = function (html, data, updateAll) {
      var writer = global$5();
      var isEphoxEmbed = Cell(false);
      var sourceCount = 0;
      var hasImage;
      global$3({
        validate: false,
        allow_conditional_comments: true,
        comment: function (text) {
          writer.comment(text);
        },
        cdata: function (text) {
          writer.cdata(text);
        },
        text: function (text, raw) {
          writer.text(text, raw);
        },
        start: function (name, attrs, empty) {
          if (isEphoxEmbed.get()) ; else if (has(attrs.map, 'data-ephox-embed-iri')) {
            isEphoxEmbed.set(true);
            updateEphoxEmbed(data, attrs);
          } else {
            switch (name) {
            case 'video':
            case 'object':
            case 'embed':
            case 'img':
            case 'iframe':
              if (data.height !== undefined && data.width !== undefined) {
                setAttributes(attrs, {
                  width: data.width,
                  height: data.height
                });
              }
              break;
            }
            if (updateAll) {
              switch (name) {
              case 'video':
                setAttributes(attrs, {
                  poster: data.poster,
                  src: ''
                });
                if (data.altsource) {
                  setAttributes(attrs, { src: '' });
                }
                break;
              case 'iframe':
                setAttributes(attrs, { src: data.source });
                break;
              case 'source':
                if (sourceCount < 2) {
                  setAttributes(attrs, {
                    src: data[sources[sourceCount]],
                    type: data[sources[sourceCount] + 'mime']
                  });
                  if (!data[sources[sourceCount]]) {
                    return;
                  }
                }
                sourceCount++;
                break;
              case 'img':
                if (!data.poster) {
                  return;
                }
                hasImage = true;
                break;
              }
            }
          }
          writer.start(name, attrs, empty);
        },
        end: function (name) {
          if (!isEphoxEmbed.get()) {
            if (name === 'video' && updateAll) {
              for (var index = 0; index < 2; index++) {
                if (data[sources[index]]) {
                  var attrs = [];
                  attrs.map = {};
                  if (sourceCount <= index) {
                    setAttributes(attrs, {
                      src: data[sources[index]],
                      type: data[sources[index] + 'mime']
                    });
                    writer.start('source', attrs, true);
                  }
                }
              }
            }
            if (data.poster && name === 'object' && updateAll && !hasImage) {
              var imgAttrs = [];
              imgAttrs.map = {};
              setAttributes(imgAttrs, {
                src: data.poster,
                width: data.width,
                height: data.height
              });
              writer.start('img', imgAttrs, true);
            }
          }
          writer.end(name);
        }
      }, global$4({})).parse(html);
      return writer.getContent();
    };

    var urlPatterns = [
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
    var getProtocol = function (url) {
      var protocolMatches = url.match(/^(https?:\/\/|www\.)(.+)$/i);
      if (protocolMatches && protocolMatches.length > 1) {
        return protocolMatches[1] === 'www.' ? 'https://' : protocolMatches[1];
      } else {
        return 'https://';
      }
    };
    var getUrl = function (pattern, url) {
      var protocol = getProtocol(url);
      var match = pattern.regex.exec(url);
      var newUrl = protocol + pattern.url;
      var _loop_1 = function (i) {
        newUrl = newUrl.replace('$' + i, function () {
          return match[i] ? match[i] : '';
        });
      };
      for (var i = 0; i < match.length; i++) {
        _loop_1(i);
      }
      return newUrl.replace(/\?$/, '');
    };
    var matchPattern = function (url) {
      var patterns = urlPatterns.filter(function (pattern) {
        return pattern.regex.test(url);
      });
      if (patterns.length > 0) {
        return global$1.extend({}, patterns[0], { url: getUrl(patterns[0], url) });
      } else {
        return null;
      }
    };

    var getIframeHtml = function (data) {
      var allowFullscreen = data.allowfullscreen ? ' allowFullscreen="1"' : '';
      return '<iframe src="' + data.source + '" width="' + data.width + '" height="' + data.height + '"' + allowFullscreen + '></iframe>';
    };
    var getFlashHtml = function (data) {
      var html = '<object data="' + data.source + '" width="' + data.width + '" height="' + data.height + '" type="application/x-shockwave-flash">';
      if (data.poster) {
        html += '<img src="' + data.poster + '" width="' + data.width + '" height="' + data.height + '" />';
      }
      html += '</object>';
      return html;
    };
    var getAudioHtml = function (data, audioTemplateCallback) {
      if (audioTemplateCallback) {
        return audioTemplateCallback(data);
      } else {
        return '<audio controls="controls" src="' + data.source + '">' + (data.altsource ? '\n<source src="' + data.altsource + '"' + (data.altsourcemime ? ' type="' + data.altsourcemime + '"' : '') + ' />\n' : '') + '</audio>';
      }
    };
    var getVideoHtml = function (data, videoTemplateCallback) {
      if (videoTemplateCallback) {
        return videoTemplateCallback(data);
      } else {
        return '<video width="' + data.width + '" height="' + data.height + '"' + (data.poster ? ' poster="' + data.poster + '"' : '') + ' controls="controls">\n' + '<source src="' + data.source + '"' + (data.sourcemime ? ' type="' + data.sourcemime + '"' : '') + ' />\n' + (data.altsource ? '<source src="' + data.altsource + '"' + (data.altsourcemime ? ' type="' + data.altsourcemime + '"' : '') + ' />\n' : '') + '</video>';
      }
    };
    var getScriptHtml = function (data) {
      return '<script src="' + data.source + '"></script>';
    };
    var dataToHtml = function (editor, dataIn) {
      var data = global$1.extend({}, dataIn);
      if (!data.source) {
        global$1.extend(data, htmlToData(getScripts(editor), data.embed));
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
      var pattern = matchPattern(data.source);
      if (pattern) {
        data.source = pattern.url;
        data.type = pattern.type;
        data.allowfullscreen = pattern.allowFullscreen;
        data.width = data.width || String(pattern.w);
        data.height = data.height || String(pattern.h);
      }
      if (data.embed) {
        return updateHtml(data.embed, data, true);
      } else {
        var videoScript = getVideoScriptMatch(getScripts(editor), data.source);
        if (videoScript) {
          data.type = 'script';
          data.width = String(videoScript.width);
          data.height = String(videoScript.height);
        }
        var audioTemplateCallback = getAudioTemplateCallback(editor);
        var videoTemplateCallback = getVideoTemplateCallback(editor);
        data.width = data.width || '300';
        data.height = data.height || '150';
        global$1.each(data, function (value, key) {
          data[key] = editor.dom.encode('' + value);
        });
        if (data.type === 'iframe') {
          return getIframeHtml(data);
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

    var global$6 = tinymce.util.Tools.resolve('tinymce.util.Promise');

    var cache = {};
    var embedPromise = function (data, dataToHtml, handler) {
      return new global$6(function (res, rej) {
        var wrappedResolve = function (response) {
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
    var defaultPromise = function (data, dataToHtml) {
      return new global$6(function (res) {
        res({
          html: dataToHtml(data),
          url: data.source
        });
      });
    };
    var loadedData = function (editor) {
      return function (data) {
        return dataToHtml(editor, data);
      };
    };
    var getEmbedHtml = function (editor, data) {
      var embedHandler = getUrlResolver(editor);
      return embedHandler ? embedPromise(data, loadedData(editor), embedHandler) : defaultPromise(data, loadedData(editor));
    };
    var isCached = function (url) {
      return cache.hasOwnProperty(url);
    };

    var extractMeta = function (sourceInput, data) {
      return get(data, sourceInput).bind(function (mainData) {
        return get(mainData, 'meta');
      });
    };
    var getValue = function (data, metaData, sourceInput) {
      return function (prop) {
        var _a;
        var getFromData = function () {
          return get(data, prop);
        };
        var getFromMetaData = function () {
          return get(metaData, prop);
        };
        var getNonEmptyValue = function (c) {
          return get(c, 'value').bind(function (v) {
            return v.length > 0 ? Optional.some(v) : Optional.none();
          });
        };
        var getFromValueFirst = function () {
          return getFromData().bind(function (child) {
            return isObject(child) ? getNonEmptyValue(child).orThunk(getFromMetaData) : getFromMetaData().orThunk(function () {
              return Optional.from(child);
            });
          });
        };
        var getFromMetaFirst = function () {
          return getFromMetaData().orThunk(function () {
            return getFromData().bind(function (child) {
              return isObject(child) ? getNonEmptyValue(child) : Optional.from(child);
            });
          });
        };
        return _a = {}, _a[prop] = (prop === sourceInput ? getFromValueFirst() : getFromMetaFirst()).getOr(''), _a;
      };
    };
    var getDimensions = function (data, metaData) {
      var dimensions = {};
      get(data, 'dimensions').each(function (dims) {
        each([
          'width',
          'height'
        ], function (prop) {
          get(metaData, prop).orThunk(function () {
            return get(dims, prop);
          }).each(function (value) {
            return dimensions[prop] = value;
          });
        });
      });
      return dimensions;
    };
    var unwrap = function (data, sourceInput) {
      var metaData = sourceInput ? extractMeta(sourceInput, data).getOr({}) : {};
      var get = getValue(data, metaData, sourceInput);
      return __assign(__assign(__assign(__assign(__assign({}, get('source')), get('altsource')), get('poster')), get('embed')), getDimensions(data, metaData));
    };
    var wrap = function (data) {
      var wrapped = __assign(__assign({}, data), {
        source: { value: get(data, 'source').getOr('') },
        altsource: { value: get(data, 'altsource').getOr('') },
        poster: { value: get(data, 'poster').getOr('') }
      });
      each([
        'width',
        'height'
      ], function (prop) {
        get(data, prop).each(function (value) {
          var dimensions = wrapped.dimensions || {};
          dimensions[prop] = value;
          wrapped.dimensions = dimensions;
        });
      });
      return wrapped;
    };
    var handleError = function (editor) {
      return function (error) {
        var errorMessage = error && error.msg ? 'Media embed handler error: ' + error.msg : 'Media embed handler threw unknown error.';
        editor.notificationManager.open({
          type: 'error',
          text: errorMessage
        });
      };
    };
    var snippetToData = function (editor, embedSnippet) {
      return htmlToData(getScripts(editor), embedSnippet);
    };
    var isMediaElement = function (element) {
      return element.getAttribute('data-mce-object') || element.getAttribute('data-ephox-embed-iri');
    };
    var getEditorData = function (editor) {
      var element = editor.selection.getNode();
      var snippet = isMediaElement(element) ? editor.serializer.serialize(element, { selection: true }) : '';
      return __assign({ embed: snippet }, htmlToData(getScripts(editor), snippet));
    };
    var addEmbedHtml = function (api, editor) {
      return function (response) {
        if (isString(response.url) && response.url.trim().length > 0) {
          var html = response.html;
          var snippetData = snippetToData(editor, html);
          var nuData = __assign(__assign({}, snippetData), {
            source: response.url,
            embed: html
          });
          api.setData(wrap(nuData));
        }
      };
    };
    var selectPlaceholder = function (editor, beforeObjects) {
      var afterObjects = editor.dom.select('*[data-mce-object]');
      for (var i = 0; i < beforeObjects.length; i++) {
        for (var y = afterObjects.length - 1; y >= 0; y--) {
          if (beforeObjects[i] === afterObjects[y]) {
            afterObjects.splice(y, 1);
          }
        }
      }
      editor.selection.select(afterObjects[0]);
    };
    var handleInsert = function (editor, html) {
      var beforeObjects = editor.dom.select('*[data-mce-object]');
      editor.insertContent(html);
      selectPlaceholder(editor, beforeObjects);
      editor.nodeChanged();
    };
    var submitForm = function (prevData, newData, editor) {
      newData.embed = updateHtml(newData.embed, newData);
      if (newData.embed && (prevData.source === newData.source || isCached(newData.source))) {
        handleInsert(editor, newData.embed);
      } else {
        getEmbedHtml(editor, newData).then(function (response) {
          handleInsert(editor, response.html);
        }).catch(handleError(editor));
      }
    };
    var showDialog = function (editor) {
      var editorData = getEditorData(editor);
      var currentData = Cell(editorData);
      var initialData = wrap(editorData);
      var handleSource = function (prevData, api) {
        var serviceData = unwrap(api.getData(), 'source');
        if (prevData.source !== serviceData.source) {
          addEmbedHtml(win, editor)({
            url: serviceData.source,
            html: ''
          });
          getEmbedHtml(editor, serviceData).then(addEmbedHtml(win, editor)).catch(handleError(editor));
        }
      };
      var handleEmbed = function (api) {
        var data = unwrap(api.getData());
        var dataFromEmbed = snippetToData(editor, data.embed);
        api.setData(wrap(dataFromEmbed));
      };
      var handleUpdate = function (api, sourceInput) {
        var data = unwrap(api.getData(), sourceInput);
        var embed = dataToHtml(editor, data);
        api.setData(wrap(__assign(__assign({}, data), { embed: embed })));
      };
      var mediaInput = [{
          name: 'source',
          type: 'urlinput',
          filetype: 'media',
          label: 'Source'
        }];
      var sizeInput = !hasDimensions(editor) ? [] : [{
          type: 'sizeinput',
          name: 'dimensions',
          label: 'Constrain proportions',
          constrain: true
        }];
      var generalTab = {
        title: 'General',
        name: 'general',
        items: flatten([
          mediaInput,
          sizeInput
        ])
      };
      var embedTextarea = {
        type: 'textarea',
        name: 'embed',
        label: 'Paste your embed code below:'
      };
      var embedTab = {
        title: 'Embed',
        items: [embedTextarea]
      };
      var advancedFormItems = [];
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
      var advancedTab = {
        title: 'Advanced',
        name: 'advanced',
        items: advancedFormItems
      };
      var tabs = [
        generalTab,
        embedTab
      ];
      if (advancedFormItems.length > 0) {
        tabs.push(advancedTab);
      }
      var body = {
        type: 'tabpanel',
        tabs: tabs
      };
      var win = editor.windowManager.open({
        title: 'Insert/Edit Media',
        size: 'normal',
        body: body,
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
        onSubmit: function (api) {
          var serviceData = unwrap(api.getData());
          submitForm(currentData.get(), serviceData, editor);
          api.close();
        },
        onChange: function (api, detail) {
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
        initialData: initialData
      });
    };

    var get$1 = function (editor) {
      var showDialog$1 = function () {
        showDialog(editor);
      };
      return { showDialog: showDialog$1 };
    };

    var register = function (editor) {
      var showDialog$1 = function () {
        showDialog(editor);
      };
      editor.addCommand('mceMedia', showDialog$1);
    };

    var global$7 = tinymce.util.Tools.resolve('tinymce.html.Node');

    var global$8 = tinymce.util.Tools.resolve('tinymce.Env');

    var global$9 = tinymce.util.Tools.resolve('tinymce.html.DomParser');

    var sanitize = function (editor, html) {
      if (shouldFilterHtml(editor) === false) {
        return html;
      }
      var writer = global$5();
      var blocked;
      global$3({
        validate: false,
        allow_conditional_comments: false,
        comment: function (text) {
          if (!blocked) {
            writer.comment(text);
          }
        },
        cdata: function (text) {
          if (!blocked) {
            writer.cdata(text);
          }
        },
        text: function (text, raw) {
          if (!blocked) {
            writer.text(text, raw);
          }
        },
        start: function (name, attrs, empty) {
          blocked = true;
          if (name === 'script' || name === 'noscript' || name === 'svg') {
            return;
          }
          for (var i = attrs.length - 1; i >= 0; i--) {
            var attrName = attrs[i].name;
            if (attrName.indexOf('on') === 0) {
              delete attrs.map[attrName];
              attrs.splice(i, 1);
            }
            if (attrName === 'style') {
              attrs[i].value = editor.dom.serializeStyle(editor.dom.parseStyle(attrs[i].value), name);
            }
          }
          writer.start(name, attrs, empty);
          blocked = false;
        },
        end: function (name) {
          if (blocked) {
            return;
          }
          writer.end(name);
        }
      }, global$4({})).parse(html);
      return writer.getContent();
    };

    var isLiveEmbedNode = function (node) {
      var name = node.name;
      return name === 'iframe' || name === 'video' || name === 'audio';
    };
    var getDimension = function (node, styles, dimension, defaultValue) {
      if (defaultValue === void 0) {
        defaultValue = null;
      }
      var value = node.attr(dimension);
      if (isNonNullable(value)) {
        return value;
      } else if (!has(styles, dimension)) {
        return defaultValue;
      } else {
        return null;
      }
    };
    var setDimensions = function (node, previewNode, styles) {
      var useDefaults = previewNode.name === 'img' || node.name === 'video';
      var defaultWidth = useDefaults ? '300' : null;
      var fallbackHeight = node.name === 'audio' ? '30' : '150';
      var defaultHeight = useDefaults ? fallbackHeight : null;
      previewNode.attr({
        width: getDimension(node, styles, 'width', defaultWidth),
        height: getDimension(node, styles, 'height', defaultHeight)
      });
    };
    var appendNodeContent = function (editor, nodeName, previewNode, html) {
      var newNode = global$9({
        forced_root_block: false,
        validate: false
      }, editor.schema).parse(html, { context: nodeName });
      while (newNode.firstChild) {
        previewNode.append(newNode.firstChild);
      }
    };
    var createPlaceholderNode = function (editor, node) {
      var name = node.name;
      var placeHolder = new global$7('img', 1);
      placeHolder.shortEnded = true;
      retainAttributesAndInnerHtml(editor, node, placeHolder);
      setDimensions(node, placeHolder, {});
      placeHolder.attr({
        'style': node.attr('style'),
        'src': global$8.transparentSrc,
        'data-mce-object': name,
        'class': 'mce-object mce-object-' + name
      });
      return placeHolder;
    };
    var createPreviewNode = function (editor, node) {
      var name = node.name;
      var previewWrapper = new global$7('span', 1);
      previewWrapper.attr({
        'contentEditable': 'false',
        'style': node.attr('style'),
        'data-mce-object': name,
        'class': 'mce-preview-object mce-object-' + name
      });
      retainAttributesAndInnerHtml(editor, node, previewWrapper);
      var styles = editor.dom.parseStyle(node.attr('style'));
      var previewNode = new global$7(name, 1);
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
        var attrs = [
          'controls',
          'crossorigin',
          'currentTime',
          'loop',
          'muted',
          'poster',
          'preload'
        ];
        each(attrs, function (attrName) {
          previewNode.attr(attrName, node.attr(attrName));
        });
        var sanitizedHtml = previewWrapper.attr('data-mce-html');
        if (isNonNullable(sanitizedHtml)) {
          appendNodeContent(editor, name, previewNode, sanitizedHtml);
        }
      }
      var shimNode = new global$7('span', 1);
      shimNode.attr('class', 'mce-shim');
      previewWrapper.append(previewNode);
      previewWrapper.append(shimNode);
      return previewWrapper;
    };
    var retainAttributesAndInnerHtml = function (editor, sourceNode, targetNode) {
      var attribs = sourceNode.attributes;
      var ai = attribs.length;
      while (ai--) {
        var attrName = attribs[ai].name;
        var attrValue = attribs[ai].value;
        if (attrName !== 'width' && attrName !== 'height' && attrName !== 'style') {
          if (attrName === 'data' || attrName === 'src') {
            attrValue = editor.convertURL(attrValue, attrName);
          }
          targetNode.attr('data-mce-p-' + attrName, attrValue);
        }
      }
      var innerHtml = sourceNode.firstChild && sourceNode.firstChild.value;
      if (innerHtml) {
        targetNode.attr('data-mce-html', escape(sanitize(editor, innerHtml)));
        targetNode.firstChild = null;
      }
    };
    var isPageEmbedWrapper = function (node) {
      var nodeClass = node.attr('class');
      return nodeClass && /\btiny-pageembed\b/.test(nodeClass);
    };
    var isWithinEmbedWrapper = function (node) {
      while (node = node.parent) {
        if (node.attr('data-ephox-embed-iri') || isPageEmbedWrapper(node)) {
          return true;
        }
      }
      return false;
    };
    var placeHolderConverter = function (editor) {
      return function (nodes) {
        var i = nodes.length;
        var node;
        var videoScript;
        while (i--) {
          node = nodes[i];
          if (!node.parent) {
            continue;
          }
          if (node.parent.attr('data-mce-object')) {
            continue;
          }
          if (node.name === 'script') {
            videoScript = getVideoScriptMatch(getScripts(editor), node.attr('src'));
            if (!videoScript) {
              continue;
            }
          }
          if (videoScript) {
            if (videoScript.width) {
              node.attr('width', videoScript.width.toString());
            }
            if (videoScript.height) {
              node.attr('height', videoScript.height.toString());
            }
          }
          if (isLiveEmbedNode(node) && hasLiveEmbeds(editor) && global$8.ceFalse) {
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
    };

    var setup = function (editor) {
      editor.on('preInit', function () {
        var specialElements = editor.schema.getSpecialElements();
        global$1.each('video audio iframe object'.split(' '), function (name) {
          specialElements[name] = new RegExp('</' + name + '[^>]*>', 'gi');
        });
        var boolAttrs = editor.schema.getBoolAttrs();
        global$1.each('webkitallowfullscreen mozallowfullscreen allowfullscreen'.split(' '), function (name) {
          boolAttrs[name] = {};
        });
        editor.parser.addNodeFilter('iframe,video,audio,object,embed,script', placeHolderConverter(editor));
        editor.serializer.addAttributeFilter('data-mce-object', function (nodes, name) {
          var i = nodes.length;
          var node;
          var realElm;
          var ai;
          var attribs;
          var innerHtml;
          var innerNode;
          var realElmName;
          var className;
          while (i--) {
            node = nodes[i];
            if (!node.parent) {
              continue;
            }
            realElmName = node.attr(name);
            realElm = new global$7(realElmName, 1);
            if (realElmName !== 'audio' && realElmName !== 'script') {
              className = node.attr('class');
              if (className && className.indexOf('mce-preview-object') !== -1) {
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
            attribs = node.attributes;
            ai = attribs.length;
            while (ai--) {
              var attrName = attribs[ai].name;
              if (attrName.indexOf('data-mce-p-') === 0) {
                realElm.attr(attrName.substr(11), attribs[ai].value);
              }
            }
            if (realElmName === 'script') {
              realElm.attr('type', 'text/javascript');
            }
            innerHtml = node.attr('data-mce-html');
            if (innerHtml) {
              innerNode = new global$7('#text', 3);
              innerNode.raw = true;
              innerNode.value = sanitize(editor, unescape(innerHtml));
              realElm.append(innerNode);
            }
            node.replace(realElm);
          }
        });
      });
      editor.on('SetContent', function () {
        editor.$('span.mce-preview-object').each(function (index, elm) {
          var $elm = editor.$(elm);
          if ($elm.find('span.mce-shim').length === 0) {
            $elm.append('<span class="mce-shim"></span>');
          }
        });
      });
    };

    var setup$1 = function (editor) {
      editor.on('ResolveName', function (e) {
        var name;
        if (e.target.nodeType === 1 && (name = e.target.getAttribute('data-mce-object'))) {
          e.name = name;
        }
      });
    };

    var setup$2 = function (editor) {
      editor.on('click keyup touchend', function () {
        var selectedNode = editor.selection.getNode();
        if (selectedNode && editor.dom.hasClass(selectedNode, 'mce-preview-object')) {
          if (editor.dom.getAttrib(selectedNode, 'data-mce-selected')) {
            selectedNode.setAttribute('data-mce-selected', '2');
          }
        }
      });
      editor.on('ObjectSelected', function (e) {
        var objectType = e.target.getAttribute('data-mce-object');
        if (objectType === 'script') {
          e.preventDefault();
        }
      });
      editor.on('ObjectResized', function (e) {
        var target = e.target;
        var html;
        if (target.getAttribute('data-mce-object')) {
          html = target.getAttribute('data-mce-html');
          if (html) {
            html = unescape(html);
            target.setAttribute('data-mce-html', escape(updateHtml(html, {
              width: String(e.width),
              height: String(e.height)
            })));
          }
        }
      });
    };

    var stateSelectorAdapter = function (editor, selector) {
      return function (buttonApi) {
        return editor.selection.selectorChangedWithUnbind(selector.join(','), buttonApi.setActive).unbind;
      };
    };
    var register$1 = function (editor) {
      editor.ui.registry.addToggleButton('media', {
        tooltip: 'Insert/edit media',
        icon: 'embed',
        onAction: function () {
          editor.execCommand('mceMedia');
        },
        onSetup: stateSelectorAdapter(editor, [
          'img[data-mce-object]',
          'span[data-mce-object]',
          'div[data-ephox-embed-iri]'
        ])
      });
      editor.ui.registry.addMenuItem('media', {
        icon: 'embed',
        text: 'Media...',
        onAction: function () {
          editor.execCommand('mceMedia');
        }
      });
    };

    function Plugin () {
      global.add('media', function (editor) {
        register(editor);
        register$1(editor);
        setup$1(editor);
        setup(editor);
        setup$2(editor);
        return get$1(editor);
      });
    }

    Plugin();

}());
