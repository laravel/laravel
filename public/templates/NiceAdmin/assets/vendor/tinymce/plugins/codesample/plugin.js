/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    var global$2 = tinymce.util.Tools.resolve('tinymce.PluginManager');

    const isNullable = a => a === null || a === undefined;
    const isNonNullable = a => !isNullable(a);

    const constant = value => {
      return () => {
        return value;
      };
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

    const get$1 = (xs, i) => i >= 0 && i < xs.length ? Optional.some(xs[i]) : Optional.none();
    const head = xs => get$1(xs, 0);

    var global$1 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');

    const Global = typeof window !== 'undefined' ? window : Function('return this;')();

    const prismjs = function (global, module, exports) {
      const oldprism = window.Prism;
      window.Prism = { manual: true };
      var _self = typeof window !== 'undefined' ? window : typeof WorkerGlobalScope !== 'undefined' && self instanceof WorkerGlobalScope ? self : {};
      var Prism = function (_self) {
        var lang = /(?:^|\s)lang(?:uage)?-([\w-]+)(?=\s|$)/i;
        var uniqueId = 0;
        var plainTextGrammar = {};
        var _ = {
          manual: _self.Prism && _self.Prism.manual,
          disableWorkerMessageHandler: _self.Prism && _self.Prism.disableWorkerMessageHandler,
          util: {
            encode: function encode(tokens) {
              if (tokens instanceof Token) {
                return new Token(tokens.type, encode(tokens.content), tokens.alias);
              } else if (Array.isArray(tokens)) {
                return tokens.map(encode);
              } else {
                return tokens.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/\u00a0/g, ' ');
              }
            },
            type: function (o) {
              return Object.prototype.toString.call(o).slice(8, -1);
            },
            objId: function (obj) {
              if (!obj['__id']) {
                Object.defineProperty(obj, '__id', { value: ++uniqueId });
              }
              return obj['__id'];
            },
            clone: function deepClone(o, visited) {
              visited = visited || {};
              var clone;
              var id;
              switch (_.util.type(o)) {
              case 'Object':
                id = _.util.objId(o);
                if (visited[id]) {
                  return visited[id];
                }
                clone = {};
                visited[id] = clone;
                for (var key in o) {
                  if (o.hasOwnProperty(key)) {
                    clone[key] = deepClone(o[key], visited);
                  }
                }
                return clone;
              case 'Array':
                id = _.util.objId(o);
                if (visited[id]) {
                  return visited[id];
                }
                clone = [];
                visited[id] = clone;
                o.forEach(function (v, i) {
                  clone[i] = deepClone(v, visited);
                });
                return clone;
              default:
                return o;
              }
            },
            getLanguage: function (element) {
              while (element) {
                var m = lang.exec(element.className);
                if (m) {
                  return m[1].toLowerCase();
                }
                element = element.parentElement;
              }
              return 'none';
            },
            setLanguage: function (element, language) {
              element.className = element.className.replace(RegExp(lang, 'gi'), '');
              element.classList.add('language-' + language);
            },
            currentScript: function () {
              if (typeof document === 'undefined') {
                return null;
              }
              if ('currentScript' in document && 1 < 2) {
                return document.currentScript;
              }
              try {
                throw new Error();
              } catch (err) {
                var src = (/at [^(\r\n]*\((.*):[^:]+:[^:]+\)$/i.exec(err.stack) || [])[1];
                if (src) {
                  var scripts = document.getElementsByTagName('script');
                  for (var i in scripts) {
                    if (scripts[i].src == src) {
                      return scripts[i];
                    }
                  }
                }
                return null;
              }
            },
            isActive: function (element, className, defaultActivation) {
              var no = 'no-' + className;
              while (element) {
                var classList = element.classList;
                if (classList.contains(className)) {
                  return true;
                }
                if (classList.contains(no)) {
                  return false;
                }
                element = element.parentElement;
              }
              return !!defaultActivation;
            }
          },
          languages: {
            plain: plainTextGrammar,
            plaintext: plainTextGrammar,
            text: plainTextGrammar,
            txt: plainTextGrammar,
            extend: function (id, redef) {
              var lang = _.util.clone(_.languages[id]);
              for (var key in redef) {
                lang[key] = redef[key];
              }
              return lang;
            },
            insertBefore: function (inside, before, insert, root) {
              root = root || _.languages;
              var grammar = root[inside];
              var ret = {};
              for (var token in grammar) {
                if (grammar.hasOwnProperty(token)) {
                  if (token == before) {
                    for (var newToken in insert) {
                      if (insert.hasOwnProperty(newToken)) {
                        ret[newToken] = insert[newToken];
                      }
                    }
                  }
                  if (!insert.hasOwnProperty(token)) {
                    ret[token] = grammar[token];
                  }
                }
              }
              var old = root[inside];
              root[inside] = ret;
              _.languages.DFS(_.languages, function (key, value) {
                if (value === old && key != inside) {
                  this[key] = ret;
                }
              });
              return ret;
            },
            DFS: function DFS(o, callback, type, visited) {
              visited = visited || {};
              var objId = _.util.objId;
              for (var i in o) {
                if (o.hasOwnProperty(i)) {
                  callback.call(o, i, o[i], type || i);
                  var property = o[i];
                  var propertyType = _.util.type(property);
                  if (propertyType === 'Object' && !visited[objId(property)]) {
                    visited[objId(property)] = true;
                    DFS(property, callback, null, visited);
                  } else if (propertyType === 'Array' && !visited[objId(property)]) {
                    visited[objId(property)] = true;
                    DFS(property, callback, i, visited);
                  }
                }
              }
            }
          },
          plugins: {},
          highlightAll: function (async, callback) {
            _.highlightAllUnder(document, async, callback);
          },
          highlightAllUnder: function (container, async, callback) {
            var env = {
              callback: callback,
              container: container,
              selector: 'code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code'
            };
            _.hooks.run('before-highlightall', env);
            env.elements = Array.prototype.slice.apply(env.container.querySelectorAll(env.selector));
            _.hooks.run('before-all-elements-highlight', env);
            for (var i = 0, element; element = env.elements[i++];) {
              _.highlightElement(element, async === true, env.callback);
            }
          },
          highlightElement: function (element, async, callback) {
            var language = _.util.getLanguage(element);
            var grammar = _.languages[language];
            _.util.setLanguage(element, language);
            var parent = element.parentElement;
            if (parent && parent.nodeName.toLowerCase() === 'pre') {
              _.util.setLanguage(parent, language);
            }
            var code = element.textContent;
            var env = {
              element: element,
              language: language,
              grammar: grammar,
              code: code
            };
            function insertHighlightedCode(highlightedCode) {
              env.highlightedCode = highlightedCode;
              _.hooks.run('before-insert', env);
              env.element.innerHTML = env.highlightedCode;
              _.hooks.run('after-highlight', env);
              _.hooks.run('complete', env);
              callback && callback.call(env.element);
            }
            _.hooks.run('before-sanity-check', env);
            parent = env.element.parentElement;
            if (parent && parent.nodeName.toLowerCase() === 'pre' && !parent.hasAttribute('tabindex')) {
              parent.setAttribute('tabindex', '0');
            }
            if (!env.code) {
              _.hooks.run('complete', env);
              callback && callback.call(env.element);
              return;
            }
            _.hooks.run('before-highlight', env);
            if (!env.grammar) {
              insertHighlightedCode(_.util.encode(env.code));
              return;
            }
            if (async && _self.Worker) {
              var worker = new Worker(_.filename);
              worker.onmessage = function (evt) {
                insertHighlightedCode(evt.data);
              };
              worker.postMessage(JSON.stringify({
                language: env.language,
                code: env.code,
                immediateClose: true
              }));
            } else {
              insertHighlightedCode(_.highlight(env.code, env.grammar, env.language));
            }
          },
          highlight: function (text, grammar, language) {
            var env = {
              code: text,
              grammar: grammar,
              language: language
            };
            _.hooks.run('before-tokenize', env);
            if (!env.grammar) {
              throw new Error('The language "' + env.language + '" has no grammar.');
            }
            env.tokens = _.tokenize(env.code, env.grammar);
            _.hooks.run('after-tokenize', env);
            return Token.stringify(_.util.encode(env.tokens), env.language);
          },
          tokenize: function (text, grammar) {
            var rest = grammar.rest;
            if (rest) {
              for (var token in rest) {
                grammar[token] = rest[token];
              }
              delete grammar.rest;
            }
            var tokenList = new LinkedList();
            addAfter(tokenList, tokenList.head, text);
            matchGrammar(text, tokenList, grammar, tokenList.head, 0);
            return toArray(tokenList);
          },
          hooks: {
            all: {},
            add: function (name, callback) {
              var hooks = _.hooks.all;
              hooks[name] = hooks[name] || [];
              hooks[name].push(callback);
            },
            run: function (name, env) {
              var callbacks = _.hooks.all[name];
              if (!callbacks || !callbacks.length) {
                return;
              }
              for (var i = 0, callback; callback = callbacks[i++];) {
                callback(env);
              }
            }
          },
          Token: Token
        };
        _self.Prism = _;
        function Token(type, content, alias, matchedStr) {
          this.type = type;
          this.content = content;
          this.alias = alias;
          this.length = (matchedStr || '').length | 0;
        }
        Token.stringify = function stringify(o, language) {
          if (typeof o == 'string') {
            return o;
          }
          if (Array.isArray(o)) {
            var s = '';
            o.forEach(function (e) {
              s += stringify(e, language);
            });
            return s;
          }
          var env = {
            type: o.type,
            content: stringify(o.content, language),
            tag: 'span',
            classes: [
              'token',
              o.type
            ],
            attributes: {},
            language: language
          };
          var aliases = o.alias;
          if (aliases) {
            if (Array.isArray(aliases)) {
              Array.prototype.push.apply(env.classes, aliases);
            } else {
              env.classes.push(aliases);
            }
          }
          _.hooks.run('wrap', env);
          var attributes = '';
          for (var name in env.attributes) {
            attributes += ' ' + name + '="' + (env.attributes[name] || '').replace(/"/g, '&quot;') + '"';
          }
          return '<' + env.tag + ' class="' + env.classes.join(' ') + '"' + attributes + '>' + env.content + '</' + env.tag + '>';
        };
        function matchPattern(pattern, pos, text, lookbehind) {
          pattern.lastIndex = pos;
          var match = pattern.exec(text);
          if (match && lookbehind && match[1]) {
            var lookbehindLength = match[1].length;
            match.index += lookbehindLength;
            match[0] = match[0].slice(lookbehindLength);
          }
          return match;
        }
        function matchGrammar(text, tokenList, grammar, startNode, startPos, rematch) {
          for (var token in grammar) {
            if (!grammar.hasOwnProperty(token) || !grammar[token]) {
              continue;
            }
            var patterns = grammar[token];
            patterns = Array.isArray(patterns) ? patterns : [patterns];
            for (var j = 0; j < patterns.length; ++j) {
              if (rematch && rematch.cause == token + ',' + j) {
                return;
              }
              var patternObj = patterns[j];
              var inside = patternObj.inside;
              var lookbehind = !!patternObj.lookbehind;
              var greedy = !!patternObj.greedy;
              var alias = patternObj.alias;
              if (greedy && !patternObj.pattern.global) {
                var flags = patternObj.pattern.toString().match(/[imsuy]*$/)[0];
                patternObj.pattern = RegExp(patternObj.pattern.source, flags + 'g');
              }
              var pattern = patternObj.pattern || patternObj;
              for (var currentNode = startNode.next, pos = startPos; currentNode !== tokenList.tail; pos += currentNode.value.length, currentNode = currentNode.next) {
                if (rematch && pos >= rematch.reach) {
                  break;
                }
                var str = currentNode.value;
                if (tokenList.length > text.length) {
                  return;
                }
                if (str instanceof Token) {
                  continue;
                }
                var removeCount = 1;
                var match;
                if (greedy) {
                  match = matchPattern(pattern, pos, text, lookbehind);
                  if (!match || match.index >= text.length) {
                    break;
                  }
                  var from = match.index;
                  var to = match.index + match[0].length;
                  var p = pos;
                  p += currentNode.value.length;
                  while (from >= p) {
                    currentNode = currentNode.next;
                    p += currentNode.value.length;
                  }
                  p -= currentNode.value.length;
                  pos = p;
                  if (currentNode.value instanceof Token) {
                    continue;
                  }
                  for (var k = currentNode; k !== tokenList.tail && (p < to || typeof k.value === 'string'); k = k.next) {
                    removeCount++;
                    p += k.value.length;
                  }
                  removeCount--;
                  str = text.slice(pos, p);
                  match.index -= pos;
                } else {
                  match = matchPattern(pattern, 0, str, lookbehind);
                  if (!match) {
                    continue;
                  }
                }
                var from = match.index;
                var matchStr = match[0];
                var before = str.slice(0, from);
                var after = str.slice(from + matchStr.length);
                var reach = pos + str.length;
                if (rematch && reach > rematch.reach) {
                  rematch.reach = reach;
                }
                var removeFrom = currentNode.prev;
                if (before) {
                  removeFrom = addAfter(tokenList, removeFrom, before);
                  pos += before.length;
                }
                removeRange(tokenList, removeFrom, removeCount);
                var wrapped = new Token(token, inside ? _.tokenize(matchStr, inside) : matchStr, alias, matchStr);
                currentNode = addAfter(tokenList, removeFrom, wrapped);
                if (after) {
                  addAfter(tokenList, currentNode, after);
                }
                if (removeCount > 1) {
                  var nestedRematch = {
                    cause: token + ',' + j,
                    reach: reach
                  };
                  matchGrammar(text, tokenList, grammar, currentNode.prev, pos, nestedRematch);
                  if (rematch && nestedRematch.reach > rematch.reach) {
                    rematch.reach = nestedRematch.reach;
                  }
                }
              }
            }
          }
        }
        function LinkedList() {
          var head = {
            value: null,
            prev: null,
            next: null
          };
          var tail = {
            value: null,
            prev: head,
            next: null
          };
          head.next = tail;
          this.head = head;
          this.tail = tail;
          this.length = 0;
        }
        function addAfter(list, node, value) {
          var next = node.next;
          var newNode = {
            value: value,
            prev: node,
            next: next
          };
          node.next = newNode;
          next.prev = newNode;
          list.length++;
          return newNode;
        }
        function removeRange(list, node, count) {
          var next = node.next;
          for (var i = 0; i < count && next !== list.tail; i++) {
            next = next.next;
          }
          node.next = next;
          next.prev = node;
          list.length -= i;
        }
        function toArray(list) {
          var array = [];
          var node = list.head.next;
          while (node !== list.tail) {
            array.push(node.value);
            node = node.next;
          }
          return array;
        }
        if (!_self.document) {
          if (!_self.addEventListener) {
            return _;
          }
          if (!_.disableWorkerMessageHandler) {
            _self.addEventListener('message', function (evt) {
              var message = JSON.parse(evt.data);
              var lang = message.language;
              var code = message.code;
              var immediateClose = message.immediateClose;
              _self.postMessage(_.highlight(code, _.languages[lang], lang));
              if (immediateClose) {
                _self.close();
              }
            }, false);
          }
          return _;
        }
        var script = _.util.currentScript();
        if (script) {
          _.filename = script.src;
          if (script.hasAttribute('data-manual')) {
            _.manual = true;
          }
        }
        function highlightAutomaticallyCallback() {
          if (!_.manual) {
            _.highlightAll();
          }
        }
        if (!_.manual) {
          var readyState = document.readyState;
          if (readyState === 'loading' || readyState === 'interactive' && script && script.defer) {
            document.addEventListener('DOMContentLoaded', highlightAutomaticallyCallback);
          } else {
            if (window.requestAnimationFrame) {
              window.requestAnimationFrame(highlightAutomaticallyCallback);
            } else {
              window.setTimeout(highlightAutomaticallyCallback, 16);
            }
          }
        }
        return _;
      }(_self);
      if (typeof module !== 'undefined' && module.exports) {
        module.exports = Prism;
      }
      if (typeof global !== 'undefined') {
        global.Prism = Prism;
      }
      Prism.languages.clike = {
        'comment': [
          {
            pattern: /(^|[^\\])\/\*[\s\S]*?(?:\*\/|$)/,
            lookbehind: true,
            greedy: true
          },
          {
            pattern: /(^|[^\\:])\/\/.*/,
            lookbehind: true,
            greedy: true
          }
        ],
        'string': {
          pattern: /(["'])(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/,
          greedy: true
        },
        'class-name': {
          pattern: /(\b(?:class|extends|implements|instanceof|interface|new|trait)\s+|\bcatch\s+\()[\w.\\]+/i,
          lookbehind: true,
          inside: { 'punctuation': /[.\\]/ }
        },
        'keyword': /\b(?:break|catch|continue|do|else|finally|for|function|if|in|instanceof|new|null|return|throw|try|while)\b/,
        'boolean': /\b(?:false|true)\b/,
        'function': /\b\w+(?=\()/,
        'number': /\b0x[\da-f]+\b|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:e[+-]?\d+)?/i,
        'operator': /[<>]=?|[!=]=?=?|--?|\+\+?|&&?|\|\|?|[?*/~^%]/,
        'punctuation': /[{}[\];(),.:]/
      };
      (function (Prism) {
        function getPlaceholder(language, index) {
          return '___' + language.toUpperCase() + index + '___';
        }
        Object.defineProperties(Prism.languages['markup-templating'] = {}, {
          buildPlaceholders: {
            value: function (env, language, placeholderPattern, replaceFilter) {
              if (env.language !== language) {
                return;
              }
              var tokenStack = env.tokenStack = [];
              env.code = env.code.replace(placeholderPattern, function (match) {
                if (typeof replaceFilter === 'function' && !replaceFilter(match)) {
                  return match;
                }
                var i = tokenStack.length;
                var placeholder;
                while (env.code.indexOf(placeholder = getPlaceholder(language, i)) !== -1) {
                  ++i;
                }
                tokenStack[i] = match;
                return placeholder;
              });
              env.grammar = Prism.languages.markup;
            }
          },
          tokenizePlaceholders: {
            value: function (env, language) {
              if (env.language !== language || !env.tokenStack) {
                return;
              }
              env.grammar = Prism.languages[language];
              var j = 0;
              var keys = Object.keys(env.tokenStack);
              function walkTokens(tokens) {
                for (var i = 0; i < tokens.length; i++) {
                  if (j >= keys.length) {
                    break;
                  }
                  var token = tokens[i];
                  if (typeof token === 'string' || token.content && typeof token.content === 'string') {
                    var k = keys[j];
                    var t = env.tokenStack[k];
                    var s = typeof token === 'string' ? token : token.content;
                    var placeholder = getPlaceholder(language, k);
                    var index = s.indexOf(placeholder);
                    if (index > -1) {
                      ++j;
                      var before = s.substring(0, index);
                      var middle = new Prism.Token(language, Prism.tokenize(t, env.grammar), 'language-' + language, t);
                      var after = s.substring(index + placeholder.length);
                      var replacement = [];
                      if (before) {
                        replacement.push.apply(replacement, walkTokens([before]));
                      }
                      replacement.push(middle);
                      if (after) {
                        replacement.push.apply(replacement, walkTokens([after]));
                      }
                      if (typeof token === 'string') {
                        tokens.splice.apply(tokens, [
                          i,
                          1
                        ].concat(replacement));
                      } else {
                        token.content = replacement;
                      }
                    }
                  } else if (token.content) {
                    walkTokens(token.content);
                  }
                }
                return tokens;
              }
              walkTokens(env.tokens);
            }
          }
        });
      }(Prism));
      Prism.languages.c = Prism.languages.extend('clike', {
        'comment': {
          pattern: /\/\/(?:[^\r\n\\]|\\(?:\r\n?|\n|(?![\r\n])))*|\/\*[\s\S]*?(?:\*\/|$)/,
          greedy: true
        },
        'string': {
          pattern: /"(?:\\(?:\r\n|[\s\S])|[^"\\\r\n])*"/,
          greedy: true
        },
        'class-name': {
          pattern: /(\b(?:enum|struct)\s+(?:__attribute__\s*\(\([\s\S]*?\)\)\s*)?)\w+|\b[a-z]\w*_t\b/,
          lookbehind: true
        },
        'keyword': /\b(?:_Alignas|_Alignof|_Atomic|_Bool|_Complex|_Generic|_Imaginary|_Noreturn|_Static_assert|_Thread_local|__attribute__|asm|auto|break|case|char|const|continue|default|do|double|else|enum|extern|float|for|goto|if|inline|int|long|register|return|short|signed|sizeof|static|struct|switch|typedef|typeof|union|unsigned|void|volatile|while)\b/,
        'function': /\b[a-z_]\w*(?=\s*\()/i,
        'number': /(?:\b0x(?:[\da-f]+(?:\.[\da-f]*)?|\.[\da-f]+)(?:p[+-]?\d+)?|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:e[+-]?\d+)?)[ful]{0,4}/i,
        'operator': />>=?|<<=?|->|([-+&|:])\1|[?:~]|[-+*/%&|^!=<>]=?/
      });
      Prism.languages.insertBefore('c', 'string', {
        'char': {
          pattern: /'(?:\\(?:\r\n|[\s\S])|[^'\\\r\n]){0,32}'/,
          greedy: true
        }
      });
      Prism.languages.insertBefore('c', 'string', {
        'macro': {
          pattern: /(^[\t ]*)#\s*[a-z](?:[^\r\n\\/]|\/(?!\*)|\/\*(?:[^*]|\*(?!\/))*\*\/|\\(?:\r\n|[\s\S]))*/im,
          lookbehind: true,
          greedy: true,
          alias: 'property',
          inside: {
            'string': [
              {
                pattern: /^(#\s*include\s*)<[^>]+>/,
                lookbehind: true
              },
              Prism.languages.c['string']
            ],
            'char': Prism.languages.c['char'],
            'comment': Prism.languages.c['comment'],
            'macro-name': [
              {
                pattern: /(^#\s*define\s+)\w+\b(?!\()/i,
                lookbehind: true
              },
              {
                pattern: /(^#\s*define\s+)\w+\b(?=\()/i,
                lookbehind: true,
                alias: 'function'
              }
            ],
            'directive': {
              pattern: /^(#\s*)[a-z]+/,
              lookbehind: true,
              alias: 'keyword'
            },
            'directive-hash': /^#/,
            'punctuation': /##|\\(?=[\r\n])/,
            'expression': {
              pattern: /\S[\s\S]*/,
              inside: Prism.languages.c
            }
          }
        }
      });
      Prism.languages.insertBefore('c', 'function', { 'constant': /\b(?:EOF|NULL|SEEK_CUR|SEEK_END|SEEK_SET|__DATE__|__FILE__|__LINE__|__TIMESTAMP__|__TIME__|__func__|stderr|stdin|stdout)\b/ });
      delete Prism.languages.c['boolean'];
      (function (Prism) {
        var keyword = /\b(?:alignas|alignof|asm|auto|bool|break|case|catch|char|char16_t|char32_t|char8_t|class|co_await|co_return|co_yield|compl|concept|const|const_cast|consteval|constexpr|constinit|continue|decltype|default|delete|do|double|dynamic_cast|else|enum|explicit|export|extern|final|float|for|friend|goto|if|import|inline|int|int16_t|int32_t|int64_t|int8_t|long|module|mutable|namespace|new|noexcept|nullptr|operator|override|private|protected|public|register|reinterpret_cast|requires|return|short|signed|sizeof|static|static_assert|static_cast|struct|switch|template|this|thread_local|throw|try|typedef|typeid|typename|uint16_t|uint32_t|uint64_t|uint8_t|union|unsigned|using|virtual|void|volatile|wchar_t|while)\b/;
        var modName = /\b(?!<keyword>)\w+(?:\s*\.\s*\w+)*\b/.source.replace(/<keyword>/g, function () {
          return keyword.source;
        });
        Prism.languages.cpp = Prism.languages.extend('c', {
          'class-name': [
            {
              pattern: RegExp(/(\b(?:class|concept|enum|struct|typename)\s+)(?!<keyword>)\w+/.source.replace(/<keyword>/g, function () {
                return keyword.source;
              })),
              lookbehind: true
            },
            /\b[A-Z]\w*(?=\s*::\s*\w+\s*\()/,
            /\b[A-Z_]\w*(?=\s*::\s*~\w+\s*\()/i,
            /\b\w+(?=\s*<(?:[^<>]|<(?:[^<>]|<[^<>]*>)*>)*>\s*::\s*\w+\s*\()/
          ],
          'keyword': keyword,
          'number': {
            pattern: /(?:\b0b[01']+|\b0x(?:[\da-f']+(?:\.[\da-f']*)?|\.[\da-f']+)(?:p[+-]?[\d']+)?|(?:\b[\d']+(?:\.[\d']*)?|\B\.[\d']+)(?:e[+-]?[\d']+)?)[ful]{0,4}/i,
            greedy: true
          },
          'operator': />>=?|<<=?|->|--|\+\+|&&|\|\||[?:~]|<=>|[-+*/%&|^!=<>]=?|\b(?:and|and_eq|bitand|bitor|not|not_eq|or|or_eq|xor|xor_eq)\b/,
          'boolean': /\b(?:false|true)\b/
        });
        Prism.languages.insertBefore('cpp', 'string', {
          'module': {
            pattern: RegExp(/(\b(?:import|module)\s+)/.source + '(?:' + /"(?:\\(?:\r\n|[\s\S])|[^"\\\r\n])*"|<[^<>\r\n]*>/.source + '|' + /<mod-name>(?:\s*:\s*<mod-name>)?|:\s*<mod-name>/.source.replace(/<mod-name>/g, function () {
              return modName;
            }) + ')'),
            lookbehind: true,
            greedy: true,
            inside: {
              'string': /^[<"][\s\S]+/,
              'operator': /:/,
              'punctuation': /\./
            }
          },
          'raw-string': {
            pattern: /R"([^()\\ ]{0,16})\([\s\S]*?\)\1"/,
            alias: 'string',
            greedy: true
          }
        });
        Prism.languages.insertBefore('cpp', 'keyword', {
          'generic-function': {
            pattern: /\b(?!operator\b)[a-z_]\w*\s*<(?:[^<>]|<[^<>]*>)*>(?=\s*\()/i,
            inside: {
              'function': /^\w+/,
              'generic': {
                pattern: /<[\s\S]+/,
                alias: 'class-name',
                inside: Prism.languages.cpp
              }
            }
          }
        });
        Prism.languages.insertBefore('cpp', 'operator', {
          'double-colon': {
            pattern: /::/,
            alias: 'punctuation'
          }
        });
        Prism.languages.insertBefore('cpp', 'class-name', {
          'base-clause': {
            pattern: /(\b(?:class|struct)\s+\w+\s*:\s*)[^;{}"'\s]+(?:\s+[^;{}"'\s]+)*(?=\s*[;{])/,
            lookbehind: true,
            greedy: true,
            inside: Prism.languages.extend('cpp', {})
          }
        });
        Prism.languages.insertBefore('inside', 'double-colon', { 'class-name': /\b[a-z_]\w*\b(?!\s*::)/i }, Prism.languages.cpp['base-clause']);
      }(Prism));
      (function (Prism) {
        function replace(pattern, replacements) {
          return pattern.replace(/<<(\d+)>>/g, function (m, index) {
            return '(?:' + replacements[+index] + ')';
          });
        }
        function re(pattern, replacements, flags) {
          return RegExp(replace(pattern, replacements), flags || '');
        }
        function nested(pattern, depthLog2) {
          for (var i = 0; i < depthLog2; i++) {
            pattern = pattern.replace(/<<self>>/g, function () {
              return '(?:' + pattern + ')';
            });
          }
          return pattern.replace(/<<self>>/g, '[^\\s\\S]');
        }
        var keywordKinds = {
          type: 'bool byte char decimal double dynamic float int long object sbyte short string uint ulong ushort var void',
          typeDeclaration: 'class enum interface record struct',
          contextual: 'add alias and ascending async await by descending from(?=\\s*(?:\\w|$)) get global group into init(?=\\s*;) join let nameof not notnull on or orderby partial remove select set unmanaged value when where with(?=\\s*{)',
          other: 'abstract as base break case catch checked const continue default delegate do else event explicit extern finally fixed for foreach goto if implicit in internal is lock namespace new null operator out override params private protected public readonly ref return sealed sizeof stackalloc static switch this throw try typeof unchecked unsafe using virtual volatile while yield'
        };
        function keywordsToPattern(words) {
          return '\\b(?:' + words.trim().replace(/ /g, '|') + ')\\b';
        }
        var typeDeclarationKeywords = keywordsToPattern(keywordKinds.typeDeclaration);
        var keywords = RegExp(keywordsToPattern(keywordKinds.type + ' ' + keywordKinds.typeDeclaration + ' ' + keywordKinds.contextual + ' ' + keywordKinds.other));
        var nonTypeKeywords = keywordsToPattern(keywordKinds.typeDeclaration + ' ' + keywordKinds.contextual + ' ' + keywordKinds.other);
        var nonContextualKeywords = keywordsToPattern(keywordKinds.type + ' ' + keywordKinds.typeDeclaration + ' ' + keywordKinds.other);
        var generic = nested(/<(?:[^<>;=+\-*/%&|^]|<<self>>)*>/.source, 2);
        var nestedRound = nested(/\((?:[^()]|<<self>>)*\)/.source, 2);
        var name = /@?\b[A-Za-z_]\w*\b/.source;
        var genericName = replace(/<<0>>(?:\s*<<1>>)?/.source, [
          name,
          generic
        ]);
        var identifier = replace(/(?!<<0>>)<<1>>(?:\s*\.\s*<<1>>)*/.source, [
          nonTypeKeywords,
          genericName
        ]);
        var array = /\[\s*(?:,\s*)*\]/.source;
        var typeExpressionWithoutTuple = replace(/<<0>>(?:\s*(?:\?\s*)?<<1>>)*(?:\s*\?)?/.source, [
          identifier,
          array
        ]);
        var tupleElement = replace(/[^,()<>[\];=+\-*/%&|^]|<<0>>|<<1>>|<<2>>/.source, [
          generic,
          nestedRound,
          array
        ]);
        var tuple = replace(/\(<<0>>+(?:,<<0>>+)+\)/.source, [tupleElement]);
        var typeExpression = replace(/(?:<<0>>|<<1>>)(?:\s*(?:\?\s*)?<<2>>)*(?:\s*\?)?/.source, [
          tuple,
          identifier,
          array
        ]);
        var typeInside = {
          'keyword': keywords,
          'punctuation': /[<>()?,.:[\]]/
        };
        var character = /'(?:[^\r\n'\\]|\\.|\\[Uux][\da-fA-F]{1,8})'/.source;
        var regularString = /"(?:\\.|[^\\"\r\n])*"/.source;
        var verbatimString = /@"(?:""|\\[\s\S]|[^\\"])*"(?!")/.source;
        Prism.languages.csharp = Prism.languages.extend('clike', {
          'string': [
            {
              pattern: re(/(^|[^$\\])<<0>>/.source, [verbatimString]),
              lookbehind: true,
              greedy: true
            },
            {
              pattern: re(/(^|[^@$\\])<<0>>/.source, [regularString]),
              lookbehind: true,
              greedy: true
            }
          ],
          'class-name': [
            {
              pattern: re(/(\busing\s+static\s+)<<0>>(?=\s*;)/.source, [identifier]),
              lookbehind: true,
              inside: typeInside
            },
            {
              pattern: re(/(\busing\s+<<0>>\s*=\s*)<<1>>(?=\s*;)/.source, [
                name,
                typeExpression
              ]),
              lookbehind: true,
              inside: typeInside
            },
            {
              pattern: re(/(\busing\s+)<<0>>(?=\s*=)/.source, [name]),
              lookbehind: true
            },
            {
              pattern: re(/(\b<<0>>\s+)<<1>>/.source, [
                typeDeclarationKeywords,
                genericName
              ]),
              lookbehind: true,
              inside: typeInside
            },
            {
              pattern: re(/(\bcatch\s*\(\s*)<<0>>/.source, [identifier]),
              lookbehind: true,
              inside: typeInside
            },
            {
              pattern: re(/(\bwhere\s+)<<0>>/.source, [name]),
              lookbehind: true
            },
            {
              pattern: re(/(\b(?:is(?:\s+not)?|as)\s+)<<0>>/.source, [typeExpressionWithoutTuple]),
              lookbehind: true,
              inside: typeInside
            },
            {
              pattern: re(/\b<<0>>(?=\s+(?!<<1>>|with\s*\{)<<2>>(?:\s*[=,;:{)\]]|\s+(?:in|when)\b))/.source, [
                typeExpression,
                nonContextualKeywords,
                name
              ]),
              inside: typeInside
            }
          ],
          'keyword': keywords,
          'number': /(?:\b0(?:x[\da-f_]*[\da-f]|b[01_]*[01])|(?:\B\.\d+(?:_+\d+)*|\b\d+(?:_+\d+)*(?:\.\d+(?:_+\d+)*)?)(?:e[-+]?\d+(?:_+\d+)*)?)(?:[dflmu]|lu|ul)?\b/i,
          'operator': />>=?|<<=?|[-=]>|([-+&|])\1|~|\?\?=?|[-+*/%&|^!=<>]=?/,
          'punctuation': /\?\.?|::|[{}[\];(),.:]/
        });
        Prism.languages.insertBefore('csharp', 'number', {
          'range': {
            pattern: /\.\./,
            alias: 'operator'
          }
        });
        Prism.languages.insertBefore('csharp', 'punctuation', {
          'named-parameter': {
            pattern: re(/([(,]\s*)<<0>>(?=\s*:)/.source, [name]),
            lookbehind: true,
            alias: 'punctuation'
          }
        });
        Prism.languages.insertBefore('csharp', 'class-name', {
          'namespace': {
            pattern: re(/(\b(?:namespace|using)\s+)<<0>>(?:\s*\.\s*<<0>>)*(?=\s*[;{])/.source, [name]),
            lookbehind: true,
            inside: { 'punctuation': /\./ }
          },
          'type-expression': {
            pattern: re(/(\b(?:default|sizeof|typeof)\s*\(\s*(?!\s))(?:[^()\s]|\s(?!\s)|<<0>>)*(?=\s*\))/.source, [nestedRound]),
            lookbehind: true,
            alias: 'class-name',
            inside: typeInside
          },
          'return-type': {
            pattern: re(/<<0>>(?=\s+(?:<<1>>\s*(?:=>|[({]|\.\s*this\s*\[)|this\s*\[))/.source, [
              typeExpression,
              identifier
            ]),
            inside: typeInside,
            alias: 'class-name'
          },
          'constructor-invocation': {
            pattern: re(/(\bnew\s+)<<0>>(?=\s*[[({])/.source, [typeExpression]),
            lookbehind: true,
            inside: typeInside,
            alias: 'class-name'
          },
          'generic-method': {
            pattern: re(/<<0>>\s*<<1>>(?=\s*\()/.source, [
              name,
              generic
            ]),
            inside: {
              'function': re(/^<<0>>/.source, [name]),
              'generic': {
                pattern: RegExp(generic),
                alias: 'class-name',
                inside: typeInside
              }
            }
          },
          'type-list': {
            pattern: re(/\b((?:<<0>>\s+<<1>>|record\s+<<1>>\s*<<5>>|where\s+<<2>>)\s*:\s*)(?:<<3>>|<<4>>|<<1>>\s*<<5>>|<<6>>)(?:\s*,\s*(?:<<3>>|<<4>>|<<6>>))*(?=\s*(?:where|[{;]|=>|$))/.source, [
              typeDeclarationKeywords,
              genericName,
              name,
              typeExpression,
              keywords.source,
              nestedRound,
              /\bnew\s*\(\s*\)/.source
            ]),
            lookbehind: true,
            inside: {
              'record-arguments': {
                pattern: re(/(^(?!new\s*\()<<0>>\s*)<<1>>/.source, [
                  genericName,
                  nestedRound
                ]),
                lookbehind: true,
                greedy: true,
                inside: Prism.languages.csharp
              },
              'keyword': keywords,
              'class-name': {
                pattern: RegExp(typeExpression),
                greedy: true,
                inside: typeInside
              },
              'punctuation': /[,()]/
            }
          },
          'preprocessor': {
            pattern: /(^[\t ]*)#.*/m,
            lookbehind: true,
            alias: 'property',
            inside: {
              'directive': {
                pattern: /(#)\b(?:define|elif|else|endif|endregion|error|if|line|nullable|pragma|region|undef|warning)\b/,
                lookbehind: true,
                alias: 'keyword'
              }
            }
          }
        });
        var regularStringOrCharacter = regularString + '|' + character;
        var regularStringCharacterOrComment = replace(/\/(?![*/])|\/\/[^\r\n]*[\r\n]|\/\*(?:[^*]|\*(?!\/))*\*\/|<<0>>/.source, [regularStringOrCharacter]);
        var roundExpression = nested(replace(/[^"'/()]|<<0>>|\(<<self>>*\)/.source, [regularStringCharacterOrComment]), 2);
        var attrTarget = /\b(?:assembly|event|field|method|module|param|property|return|type)\b/.source;
        var attr = replace(/<<0>>(?:\s*\(<<1>>*\))?/.source, [
          identifier,
          roundExpression
        ]);
        Prism.languages.insertBefore('csharp', 'class-name', {
          'attribute': {
            pattern: re(/((?:^|[^\s\w>)?])\s*\[\s*)(?:<<0>>\s*:\s*)?<<1>>(?:\s*,\s*<<1>>)*(?=\s*\])/.source, [
              attrTarget,
              attr
            ]),
            lookbehind: true,
            greedy: true,
            inside: {
              'target': {
                pattern: re(/^<<0>>(?=\s*:)/.source, [attrTarget]),
                alias: 'keyword'
              },
              'attribute-arguments': {
                pattern: re(/\(<<0>>*\)/.source, [roundExpression]),
                inside: Prism.languages.csharp
              },
              'class-name': {
                pattern: RegExp(identifier),
                inside: { 'punctuation': /\./ }
              },
              'punctuation': /[:,]/
            }
          }
        });
        var formatString = /:[^}\r\n]+/.source;
        var mInterpolationRound = nested(replace(/[^"'/()]|<<0>>|\(<<self>>*\)/.source, [regularStringCharacterOrComment]), 2);
        var mInterpolation = replace(/\{(?!\{)(?:(?![}:])<<0>>)*<<1>>?\}/.source, [
          mInterpolationRound,
          formatString
        ]);
        var sInterpolationRound = nested(replace(/[^"'/()]|\/(?!\*)|\/\*(?:[^*]|\*(?!\/))*\*\/|<<0>>|\(<<self>>*\)/.source, [regularStringOrCharacter]), 2);
        var sInterpolation = replace(/\{(?!\{)(?:(?![}:])<<0>>)*<<1>>?\}/.source, [
          sInterpolationRound,
          formatString
        ]);
        function createInterpolationInside(interpolation, interpolationRound) {
          return {
            'interpolation': {
              pattern: re(/((?:^|[^{])(?:\{\{)*)<<0>>/.source, [interpolation]),
              lookbehind: true,
              inside: {
                'format-string': {
                  pattern: re(/(^\{(?:(?![}:])<<0>>)*)<<1>>(?=\}$)/.source, [
                    interpolationRound,
                    formatString
                  ]),
                  lookbehind: true,
                  inside: { 'punctuation': /^:/ }
                },
                'punctuation': /^\{|\}$/,
                'expression': {
                  pattern: /[\s\S]+/,
                  alias: 'language-csharp',
                  inside: Prism.languages.csharp
                }
              }
            },
            'string': /[\s\S]+/
          };
        }
        Prism.languages.insertBefore('csharp', 'string', {
          'interpolation-string': [
            {
              pattern: re(/(^|[^\\])(?:\$@|@\$)"(?:""|\\[\s\S]|\{\{|<<0>>|[^\\{"])*"/.source, [mInterpolation]),
              lookbehind: true,
              greedy: true,
              inside: createInterpolationInside(mInterpolation, mInterpolationRound)
            },
            {
              pattern: re(/(^|[^@\\])\$"(?:\\.|\{\{|<<0>>|[^\\"{])*"/.source, [sInterpolation]),
              lookbehind: true,
              greedy: true,
              inside: createInterpolationInside(sInterpolation, sInterpolationRound)
            }
          ],
          'char': {
            pattern: RegExp(character),
            greedy: true
          }
        });
        Prism.languages.dotnet = Prism.languages.cs = Prism.languages.csharp;
      }(Prism));
      (function (Prism) {
        var string = /(?:"(?:\\(?:\r\n|[\s\S])|[^"\\\r\n])*"|'(?:\\(?:\r\n|[\s\S])|[^'\\\r\n])*')/;
        Prism.languages.css = {
          'comment': /\/\*[\s\S]*?\*\//,
          'atrule': {
            pattern: /@[\w-](?:[^;{\s]|\s+(?![\s{]))*(?:;|(?=\s*\{))/,
            inside: {
              'rule': /^@[\w-]+/,
              'selector-function-argument': {
                pattern: /(\bselector\s*\(\s*(?![\s)]))(?:[^()\s]|\s+(?![\s)])|\((?:[^()]|\([^()]*\))*\))+(?=\s*\))/,
                lookbehind: true,
                alias: 'selector'
              },
              'keyword': {
                pattern: /(^|[^\w-])(?:and|not|only|or)(?![\w-])/,
                lookbehind: true
              }
            }
          },
          'url': {
            pattern: RegExp('\\burl\\((?:' + string.source + '|' + /(?:[^\\\r\n()"']|\\[\s\S])*/.source + ')\\)', 'i'),
            greedy: true,
            inside: {
              'function': /^url/i,
              'punctuation': /^\(|\)$/,
              'string': {
                pattern: RegExp('^' + string.source + '$'),
                alias: 'url'
              }
            }
          },
          'selector': {
            pattern: RegExp('(^|[{}\\s])[^{}\\s](?:[^{};"\'\\s]|\\s+(?![\\s{])|' + string.source + ')*(?=\\s*\\{)'),
            lookbehind: true
          },
          'string': {
            pattern: string,
            greedy: true
          },
          'property': {
            pattern: /(^|[^-\w\xA0-\uFFFF])(?!\s)[-_a-z\xA0-\uFFFF](?:(?!\s)[-\w\xA0-\uFFFF])*(?=\s*:)/i,
            lookbehind: true
          },
          'important': /!important\b/i,
          'function': {
            pattern: /(^|[^-a-z0-9])[-a-z0-9]+(?=\()/i,
            lookbehind: true
          },
          'punctuation': /[(){};:,]/
        };
        Prism.languages.css['atrule'].inside.rest = Prism.languages.css;
        var markup = Prism.languages.markup;
        if (markup) {
          markup.tag.addInlined('style', 'css');
          markup.tag.addAttribute('style', 'css');
        }
      }(Prism));
      (function (Prism) {
        var keywords = /\b(?:abstract|assert|boolean|break|byte|case|catch|char|class|const|continue|default|do|double|else|enum|exports|extends|final|finally|float|for|goto|if|implements|import|instanceof|int|interface|long|module|native|new|non-sealed|null|open|opens|package|permits|private|protected|provides|public|record(?!\s*[(){}[\]<>=%~.:,;?+\-*/&|^])|requires|return|sealed|short|static|strictfp|super|switch|synchronized|this|throw|throws|to|transient|transitive|try|uses|var|void|volatile|while|with|yield)\b/;
        var classNamePrefix = /(?:[a-z]\w*\s*\.\s*)*(?:[A-Z]\w*\s*\.\s*)*/.source;
        var className = {
          pattern: RegExp(/(^|[^\w.])/.source + classNamePrefix + /[A-Z](?:[\d_A-Z]*[a-z]\w*)?\b/.source),
          lookbehind: true,
          inside: {
            'namespace': {
              pattern: /^[a-z]\w*(?:\s*\.\s*[a-z]\w*)*(?:\s*\.)?/,
              inside: { 'punctuation': /\./ }
            },
            'punctuation': /\./
          }
        };
        Prism.languages.java = Prism.languages.extend('clike', {
          'string': {
            pattern: /(^|[^\\])"(?:\\.|[^"\\\r\n])*"/,
            lookbehind: true,
            greedy: true
          },
          'class-name': [
            className,
            {
              pattern: RegExp(/(^|[^\w.])/.source + classNamePrefix + /[A-Z]\w*(?=\s+\w+\s*[;,=()]|\s*(?:\[[\s,]*\]\s*)?::\s*new\b)/.source),
              lookbehind: true,
              inside: className.inside
            },
            {
              pattern: RegExp(/(\b(?:class|enum|extends|implements|instanceof|interface|new|record|throws)\s+)/.source + classNamePrefix + /[A-Z]\w*\b/.source),
              lookbehind: true,
              inside: className.inside
            }
          ],
          'keyword': keywords,
          'function': [
            Prism.languages.clike.function,
            {
              pattern: /(::\s*)[a-z_]\w*/,
              lookbehind: true
            }
          ],
          'number': /\b0b[01][01_]*L?\b|\b0x(?:\.[\da-f_p+-]+|[\da-f_]+(?:\.[\da-f_p+-]+)?)\b|(?:\b\d[\d_]*(?:\.[\d_]*)?|\B\.\d[\d_]*)(?:e[+-]?\d[\d_]*)?[dfl]?/i,
          'operator': {
            pattern: /(^|[^.])(?:<<=?|>>>?=?|->|--|\+\+|&&|\|\||::|[?:~]|[-+*/%&|^!=<>]=?)/m,
            lookbehind: true
          }
        });
        Prism.languages.insertBefore('java', 'string', {
          'triple-quoted-string': {
            pattern: /"""[ \t]*[\r\n](?:(?:"|"")?(?:\\.|[^"\\]))*"""/,
            greedy: true,
            alias: 'string'
          },
          'char': {
            pattern: /'(?:\\.|[^'\\\r\n]){1,6}'/,
            greedy: true
          }
        });
        Prism.languages.insertBefore('java', 'class-name', {
          'annotation': {
            pattern: /(^|[^.])@\w+(?:\s*\.\s*\w+)*/,
            lookbehind: true,
            alias: 'punctuation'
          },
          'generics': {
            pattern: /<(?:[\w\s,.?]|&(?!&)|<(?:[\w\s,.?]|&(?!&)|<(?:[\w\s,.?]|&(?!&)|<(?:[\w\s,.?]|&(?!&))*>)*>)*>)*>/,
            inside: {
              'class-name': className,
              'keyword': keywords,
              'punctuation': /[<>(),.:]/,
              'operator': /[?&|]/
            }
          },
          'import': [
            {
              pattern: RegExp(/(\bimport\s+)/.source + classNamePrefix + /(?:[A-Z]\w*|\*)(?=\s*;)/.source),
              lookbehind: true,
              inside: {
                'namespace': className.inside.namespace,
                'punctuation': /\./,
                'operator': /\*/,
                'class-name': /\w+/
              }
            },
            {
              pattern: RegExp(/(\bimport\s+static\s+)/.source + classNamePrefix + /(?:\w+|\*)(?=\s*;)/.source),
              lookbehind: true,
              alias: 'static',
              inside: {
                'namespace': className.inside.namespace,
                'static': /\b\w+$/,
                'punctuation': /\./,
                'operator': /\*/,
                'class-name': /\w+/
              }
            }
          ],
          'namespace': {
            pattern: RegExp(/(\b(?:exports|import(?:\s+static)?|module|open|opens|package|provides|requires|to|transitive|uses|with)\s+)(?!<keyword>)[a-z]\w*(?:\.[a-z]\w*)*\.?/.source.replace(/<keyword>/g, function () {
              return keywords.source;
            })),
            lookbehind: true,
            inside: { 'punctuation': /\./ }
          }
        });
      }(Prism));
      Prism.languages.javascript = Prism.languages.extend('clike', {
        'class-name': [
          Prism.languages.clike['class-name'],
          {
            pattern: /(^|[^$\w\xA0-\uFFFF])(?!\s)[_$A-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\.(?:constructor|prototype))/,
            lookbehind: true
          }
        ],
        'keyword': [
          {
            pattern: /((?:^|\})\s*)catch\b/,
            lookbehind: true
          },
          {
            pattern: /(^|[^.]|\.\.\.\s*)\b(?:as|assert(?=\s*\{)|async(?=\s*(?:function\b|\(|[$\w\xA0-\uFFFF]|$))|await|break|case|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally(?=\s*(?:\{|$))|for|from(?=\s*(?:['"]|$))|function|(?:get|set)(?=\s*(?:[#\[$\w\xA0-\uFFFF]|$))|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)\b/,
            lookbehind: true
          }
        ],
        'function': /#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*(?:\.\s*(?:apply|bind|call)\s*)?\()/,
        'number': {
          pattern: RegExp(/(^|[^\w$])/.source + '(?:' + (/NaN|Infinity/.source + '|' + /0[bB][01]+(?:_[01]+)*n?/.source + '|' + /0[oO][0-7]+(?:_[0-7]+)*n?/.source + '|' + /0[xX][\dA-Fa-f]+(?:_[\dA-Fa-f]+)*n?/.source + '|' + /\d+(?:_\d+)*n/.source + '|' + /(?:\d+(?:_\d+)*(?:\.(?:\d+(?:_\d+)*)?)?|\.\d+(?:_\d+)*)(?:[Ee][+-]?\d+(?:_\d+)*)?/.source) + ')' + /(?![\w$])/.source),
          lookbehind: true
        },
        'operator': /--|\+\+|\*\*=?|=>|&&=?|\|\|=?|[!=]==|<<=?|>>>?=?|[-+*/%&|^!=<>]=?|\.{3}|\?\?=?|\?\.?|[~:]/
      });
      Prism.languages.javascript['class-name'][0].pattern = /(\b(?:class|extends|implements|instanceof|interface|new)\s+)[\w.\\]+/;
      Prism.languages.insertBefore('javascript', 'keyword', {
        'regex': {
          pattern: RegExp(/((?:^|[^$\w\xA0-\uFFFF."'\])\s]|\b(?:return|yield))\s*)/.source + /\//.source + '(?:' + /(?:\[(?:[^\]\\\r\n]|\\.)*\]|\\.|[^/\\\[\r\n])+\/[dgimyus]{0,7}/.source + '|' + /(?:\[(?:[^[\]\\\r\n]|\\.|\[(?:[^[\]\\\r\n]|\\.|\[(?:[^[\]\\\r\n]|\\.)*\])*\])*\]|\\.|[^/\\\[\r\n])+\/[dgimyus]{0,7}v[dgimyus]{0,7}/.source + ')' + /(?=(?:\s|\/\*(?:[^*]|\*(?!\/))*\*\/)*(?:$|[\r\n,.;:})\]]|\/\/))/.source),
          lookbehind: true,
          greedy: true,
          inside: {
            'regex-source': {
              pattern: /^(\/)[\s\S]+(?=\/[a-z]*$)/,
              lookbehind: true,
              alias: 'language-regex',
              inside: Prism.languages.regex
            },
            'regex-delimiter': /^\/|\/$/,
            'regex-flags': /^[a-z]+$/
          }
        },
        'function-variable': {
          pattern: /#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*[=:]\s*(?:async\s*)?(?:\bfunction\b|(?:\((?:[^()]|\([^()]*\))*\)|(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)\s*=>))/,
          alias: 'function'
        },
        'parameter': [
          {
            pattern: /(function(?:\s+(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)?\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\))/,
            lookbehind: true,
            inside: Prism.languages.javascript
          },
          {
            pattern: /(^|[^$\w\xA0-\uFFFF])(?!\s)[_$a-z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*=>)/i,
            lookbehind: true,
            inside: Prism.languages.javascript
          },
          {
            pattern: /(\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*=>)/,
            lookbehind: true,
            inside: Prism.languages.javascript
          },
          {
            pattern: /((?:\b|\s|^)(?!(?:as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally|for|from|function|get|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|set|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)(?![$\w\xA0-\uFFFF]))(?:(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*\s*)\(\s*|\]\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*\{)/,
            lookbehind: true,
            inside: Prism.languages.javascript
          }
        ],
        'constant': /\b[A-Z](?:[A-Z_]|\dx?)*\b/
      });
      Prism.languages.insertBefore('javascript', 'string', {
        'hashbang': {
          pattern: /^#!.*/,
          greedy: true,
          alias: 'comment'
        },
        'template-string': {
          pattern: /`(?:\\[\s\S]|\$\{(?:[^{}]|\{(?:[^{}]|\{[^}]*\})*\})+\}|(?!\$\{)[^\\`])*`/,
          greedy: true,
          inside: {
            'template-punctuation': {
              pattern: /^`|`$/,
              alias: 'string'
            },
            'interpolation': {
              pattern: /((?:^|[^\\])(?:\\{2})*)\$\{(?:[^{}]|\{(?:[^{}]|\{[^}]*\})*\})+\}/,
              lookbehind: true,
              inside: {
                'interpolation-punctuation': {
                  pattern: /^\$\{|\}$/,
                  alias: 'punctuation'
                },
                rest: Prism.languages.javascript
              }
            },
            'string': /[\s\S]+/
          }
        },
        'string-property': {
          pattern: /((?:^|[,{])[ \t]*)(["'])(?:\\(?:\r\n|[\s\S])|(?!\2)[^\\\r\n])*\2(?=\s*:)/m,
          lookbehind: true,
          greedy: true,
          alias: 'property'
        }
      });
      Prism.languages.insertBefore('javascript', 'operator', {
        'literal-property': {
          pattern: /((?:^|[,{])[ \t]*)(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*:)/m,
          lookbehind: true,
          alias: 'property'
        }
      });
      if (Prism.languages.markup) {
        Prism.languages.markup.tag.addInlined('script', 'javascript');
        Prism.languages.markup.tag.addAttribute(/on(?:abort|blur|change|click|composition(?:end|start|update)|dblclick|error|focus(?:in|out)?|key(?:down|up)|load|mouse(?:down|enter|leave|move|out|over|up)|reset|resize|scroll|select|slotchange|submit|unload|wheel)/.source, 'javascript');
      }
      Prism.languages.js = Prism.languages.javascript;
      Prism.languages.markup = {
        'comment': {
          pattern: /<!--(?:(?!<!--)[\s\S])*?-->/,
          greedy: true
        },
        'prolog': {
          pattern: /<\?[\s\S]+?\?>/,
          greedy: true
        },
        'doctype': {
          pattern: /<!DOCTYPE(?:[^>"'[\]]|"[^"]*"|'[^']*')+(?:\[(?:[^<"'\]]|"[^"]*"|'[^']*'|<(?!!--)|<!--(?:[^-]|-(?!->))*-->)*\]\s*)?>/i,
          greedy: true,
          inside: {
            'internal-subset': {
              pattern: /(^[^\[]*\[)[\s\S]+(?=\]>$)/,
              lookbehind: true,
              greedy: true,
              inside: null
            },
            'string': {
              pattern: /"[^"]*"|'[^']*'/,
              greedy: true
            },
            'punctuation': /^<!|>$|[[\]]/,
            'doctype-tag': /^DOCTYPE/i,
            'name': /[^\s<>'"]+/
          }
        },
        'cdata': {
          pattern: /<!\[CDATA\[[\s\S]*?\]\]>/i,
          greedy: true
        },
        'tag': {
          pattern: /<\/?(?!\d)[^\s>\/=$<%]+(?:\s(?:\s*[^\s>\/=]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))|(?=[\s/>])))+)?\s*\/?>/,
          greedy: true,
          inside: {
            'tag': {
              pattern: /^<\/?[^\s>\/]+/,
              inside: {
                'punctuation': /^<\/?/,
                'namespace': /^[^\s>\/:]+:/
              }
            },
            'special-attr': [],
            'attr-value': {
              pattern: /=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+)/,
              inside: {
                'punctuation': [
                  {
                    pattern: /^=/,
                    alias: 'attr-equals'
                  },
                  /"|'/
                ]
              }
            },
            'punctuation': /\/?>/,
            'attr-name': {
              pattern: /[^\s>\/]+/,
              inside: { 'namespace': /^[^\s>\/:]+:/ }
            }
          }
        },
        'entity': [
          {
            pattern: /&[\da-z]{1,8};/i,
            alias: 'named-entity'
          },
          /&#x?[\da-f]{1,8};/i
        ]
      };
      Prism.languages.markup['tag'].inside['attr-value'].inside['entity'] = Prism.languages.markup['entity'];
      Prism.languages.markup['doctype'].inside['internal-subset'].inside = Prism.languages.markup;
      Prism.hooks.add('wrap', function (env) {
        if (env.type === 'entity') {
          env.attributes['title'] = env.content.replace(/&amp;/, '&');
        }
      });
      Object.defineProperty(Prism.languages.markup.tag, 'addInlined', {
        value: function addInlined(tagName, lang) {
          var includedCdataInside = {};
          includedCdataInside['language-' + lang] = {
            pattern: /(^<!\[CDATA\[)[\s\S]+?(?=\]\]>$)/i,
            lookbehind: true,
            inside: Prism.languages[lang]
          };
          includedCdataInside['cdata'] = /^<!\[CDATA\[|\]\]>$/i;
          var inside = {
            'included-cdata': {
              pattern: /<!\[CDATA\[[\s\S]*?\]\]>/i,
              inside: includedCdataInside
            }
          };
          inside['language-' + lang] = {
            pattern: /[\s\S]+/,
            inside: Prism.languages[lang]
          };
          var def = {};
          def[tagName] = {
            pattern: RegExp(/(<__[^>]*>)(?:<!\[CDATA\[(?:[^\]]|\](?!\]>))*\]\]>|(?!<!\[CDATA\[)[\s\S])*?(?=<\/__>)/.source.replace(/__/g, function () {
              return tagName;
            }), 'i'),
            lookbehind: true,
            greedy: true,
            inside: inside
          };
          Prism.languages.insertBefore('markup', 'cdata', def);
        }
      });
      Object.defineProperty(Prism.languages.markup.tag, 'addAttribute', {
        value: function (attrName, lang) {
          Prism.languages.markup.tag.inside['special-attr'].push({
            pattern: RegExp(/(^|["'\s])/.source + '(?:' + attrName + ')' + /\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))/.source, 'i'),
            lookbehind: true,
            inside: {
              'attr-name': /^[^\s=]+/,
              'attr-value': {
                pattern: /=[\s\S]+/,
                inside: {
                  'value': {
                    pattern: /(^=\s*(["']|(?!["'])))\S[\s\S]*(?=\2$)/,
                    lookbehind: true,
                    alias: [
                      lang,
                      'language-' + lang
                    ],
                    inside: Prism.languages[lang]
                  },
                  'punctuation': [
                    {
                      pattern: /^=/,
                      alias: 'attr-equals'
                    },
                    /"|'/
                  ]
                }
              }
            }
          });
        }
      });
      Prism.languages.html = Prism.languages.markup;
      Prism.languages.mathml = Prism.languages.markup;
      Prism.languages.svg = Prism.languages.markup;
      Prism.languages.xml = Prism.languages.extend('markup', {});
      Prism.languages.ssml = Prism.languages.xml;
      Prism.languages.atom = Prism.languages.xml;
      Prism.languages.rss = Prism.languages.xml;
      (function (Prism) {
        var comment = /\/\*[\s\S]*?\*\/|\/\/.*|#(?!\[).*/;
        var constant = [
          {
            pattern: /\b(?:false|true)\b/i,
            alias: 'boolean'
          },
          {
            pattern: /(::\s*)\b[a-z_]\w*\b(?!\s*\()/i,
            greedy: true,
            lookbehind: true
          },
          {
            pattern: /(\b(?:case|const)\s+)\b[a-z_]\w*(?=\s*[;=])/i,
            greedy: true,
            lookbehind: true
          },
          /\b(?:null)\b/i,
          /\b[A-Z_][A-Z0-9_]*\b(?!\s*\()/
        ];
        var number = /\b0b[01]+(?:_[01]+)*\b|\b0o[0-7]+(?:_[0-7]+)*\b|\b0x[\da-f]+(?:_[\da-f]+)*\b|(?:\b\d+(?:_\d+)*\.?(?:\d+(?:_\d+)*)?|\B\.\d+)(?:e[+-]?\d+)?/i;
        var operator = /<?=>|\?\?=?|\.{3}|\??->|[!=]=?=?|::|\*\*=?|--|\+\+|&&|\|\||<<|>>|[?~]|[/^|%*&<>.+-]=?/;
        var punctuation = /[{}\[\](),:;]/;
        Prism.languages.php = {
          'delimiter': {
            pattern: /\?>$|^<\?(?:php(?=\s)|=)?/i,
            alias: 'important'
          },
          'comment': comment,
          'variable': /\$+(?:\w+\b|(?=\{))/,
          'package': {
            pattern: /(namespace\s+|use\s+(?:function\s+)?)(?:\\?\b[a-z_]\w*)+\b(?!\\)/i,
            lookbehind: true,
            inside: { 'punctuation': /\\/ }
          },
          'class-name-definition': {
            pattern: /(\b(?:class|enum|interface|trait)\s+)\b[a-z_]\w*(?!\\)\b/i,
            lookbehind: true,
            alias: 'class-name'
          },
          'function-definition': {
            pattern: /(\bfunction\s+)[a-z_]\w*(?=\s*\()/i,
            lookbehind: true,
            alias: 'function'
          },
          'keyword': [
            {
              pattern: /(\(\s*)\b(?:array|bool|boolean|float|int|integer|object|string)\b(?=\s*\))/i,
              alias: 'type-casting',
              greedy: true,
              lookbehind: true
            },
            {
              pattern: /([(,?]\s*)\b(?:array(?!\s*\()|bool|callable|(?:false|null)(?=\s*\|)|float|int|iterable|mixed|object|self|static|string)\b(?=\s*\$)/i,
              alias: 'type-hint',
              greedy: true,
              lookbehind: true
            },
            {
              pattern: /(\)\s*:\s*(?:\?\s*)?)\b(?:array(?!\s*\()|bool|callable|(?:false|null)(?=\s*\|)|float|int|iterable|mixed|never|object|self|static|string|void)\b/i,
              alias: 'return-type',
              greedy: true,
              lookbehind: true
            },
            {
              pattern: /\b(?:array(?!\s*\()|bool|float|int|iterable|mixed|object|string|void)\b/i,
              alias: 'type-declaration',
              greedy: true
            },
            {
              pattern: /(\|\s*)(?:false|null)\b|\b(?:false|null)(?=\s*\|)/i,
              alias: 'type-declaration',
              greedy: true,
              lookbehind: true
            },
            {
              pattern: /\b(?:parent|self|static)(?=\s*::)/i,
              alias: 'static-context',
              greedy: true
            },
            {
              pattern: /(\byield\s+)from\b/i,
              lookbehind: true
            },
            /\bclass\b/i,
            {
              pattern: /((?:^|[^\s>:]|(?:^|[^-])>|(?:^|[^:]):)\s*)\b(?:abstract|and|array|as|break|callable|case|catch|clone|const|continue|declare|default|die|do|echo|else|elseif|empty|enddeclare|endfor|endforeach|endif|endswitch|endwhile|enum|eval|exit|extends|final|finally|fn|for|foreach|function|global|goto|if|implements|include|include_once|instanceof|insteadof|interface|isset|list|match|namespace|never|new|or|parent|print|private|protected|public|readonly|require|require_once|return|self|static|switch|throw|trait|try|unset|use|var|while|xor|yield|__halt_compiler)\b/i,
              lookbehind: true
            }
          ],
          'argument-name': {
            pattern: /([(,]\s*)\b[a-z_]\w*(?=\s*:(?!:))/i,
            lookbehind: true
          },
          'class-name': [
            {
              pattern: /(\b(?:extends|implements|instanceof|new(?!\s+self|\s+static))\s+|\bcatch\s*\()\b[a-z_]\w*(?!\\)\b/i,
              greedy: true,
              lookbehind: true
            },
            {
              pattern: /(\|\s*)\b[a-z_]\w*(?!\\)\b/i,
              greedy: true,
              lookbehind: true
            },
            {
              pattern: /\b[a-z_]\w*(?!\\)\b(?=\s*\|)/i,
              greedy: true
            },
            {
              pattern: /(\|\s*)(?:\\?\b[a-z_]\w*)+\b/i,
              alias: 'class-name-fully-qualified',
              greedy: true,
              lookbehind: true,
              inside: { 'punctuation': /\\/ }
            },
            {
              pattern: /(?:\\?\b[a-z_]\w*)+\b(?=\s*\|)/i,
              alias: 'class-name-fully-qualified',
              greedy: true,
              inside: { 'punctuation': /\\/ }
            },
            {
              pattern: /(\b(?:extends|implements|instanceof|new(?!\s+self\b|\s+static\b))\s+|\bcatch\s*\()(?:\\?\b[a-z_]\w*)+\b(?!\\)/i,
              alias: 'class-name-fully-qualified',
              greedy: true,
              lookbehind: true,
              inside: { 'punctuation': /\\/ }
            },
            {
              pattern: /\b[a-z_]\w*(?=\s*\$)/i,
              alias: 'type-declaration',
              greedy: true
            },
            {
              pattern: /(?:\\?\b[a-z_]\w*)+(?=\s*\$)/i,
              alias: [
                'class-name-fully-qualified',
                'type-declaration'
              ],
              greedy: true,
              inside: { 'punctuation': /\\/ }
            },
            {
              pattern: /\b[a-z_]\w*(?=\s*::)/i,
              alias: 'static-context',
              greedy: true
            },
            {
              pattern: /(?:\\?\b[a-z_]\w*)+(?=\s*::)/i,
              alias: [
                'class-name-fully-qualified',
                'static-context'
              ],
              greedy: true,
              inside: { 'punctuation': /\\/ }
            },
            {
              pattern: /([(,?]\s*)[a-z_]\w*(?=\s*\$)/i,
              alias: 'type-hint',
              greedy: true,
              lookbehind: true
            },
            {
              pattern: /([(,?]\s*)(?:\\?\b[a-z_]\w*)+(?=\s*\$)/i,
              alias: [
                'class-name-fully-qualified',
                'type-hint'
              ],
              greedy: true,
              lookbehind: true,
              inside: { 'punctuation': /\\/ }
            },
            {
              pattern: /(\)\s*:\s*(?:\?\s*)?)\b[a-z_]\w*(?!\\)\b/i,
              alias: 'return-type',
              greedy: true,
              lookbehind: true
            },
            {
              pattern: /(\)\s*:\s*(?:\?\s*)?)(?:\\?\b[a-z_]\w*)+\b(?!\\)/i,
              alias: [
                'class-name-fully-qualified',
                'return-type'
              ],
              greedy: true,
              lookbehind: true,
              inside: { 'punctuation': /\\/ }
            }
          ],
          'constant': constant,
          'function': {
            pattern: /(^|[^\\\w])\\?[a-z_](?:[\w\\]*\w)?(?=\s*\()/i,
            lookbehind: true,
            inside: { 'punctuation': /\\/ }
          },
          'property': {
            pattern: /(->\s*)\w+/,
            lookbehind: true
          },
          'number': number,
          'operator': operator,
          'punctuation': punctuation
        };
        var string_interpolation = {
          pattern: /\{\$(?:\{(?:\{[^{}]+\}|[^{}]+)\}|[^{}])+\}|(^|[^\\{])\$+(?:\w+(?:\[[^\r\n\[\]]+\]|->\w+)?)/,
          lookbehind: true,
          inside: Prism.languages.php
        };
        var string = [
          {
            pattern: /<<<'([^']+)'[\r\n](?:.*[\r\n])*?\1;/,
            alias: 'nowdoc-string',
            greedy: true,
            inside: {
              'delimiter': {
                pattern: /^<<<'[^']+'|[a-z_]\w*;$/i,
                alias: 'symbol',
                inside: { 'punctuation': /^<<<'?|[';]$/ }
              }
            }
          },
          {
            pattern: /<<<(?:"([^"]+)"[\r\n](?:.*[\r\n])*?\1;|([a-z_]\w*)[\r\n](?:.*[\r\n])*?\2;)/i,
            alias: 'heredoc-string',
            greedy: true,
            inside: {
              'delimiter': {
                pattern: /^<<<(?:"[^"]+"|[a-z_]\w*)|[a-z_]\w*;$/i,
                alias: 'symbol',
                inside: { 'punctuation': /^<<<"?|[";]$/ }
              },
              'interpolation': string_interpolation
            }
          },
          {
            pattern: /`(?:\\[\s\S]|[^\\`])*`/,
            alias: 'backtick-quoted-string',
            greedy: true
          },
          {
            pattern: /'(?:\\[\s\S]|[^\\'])*'/,
            alias: 'single-quoted-string',
            greedy: true
          },
          {
            pattern: /"(?:\\[\s\S]|[^\\"])*"/,
            alias: 'double-quoted-string',
            greedy: true,
            inside: { 'interpolation': string_interpolation }
          }
        ];
        Prism.languages.insertBefore('php', 'variable', {
          'string': string,
          'attribute': {
            pattern: /#\[(?:[^"'\/#]|\/(?![*/])|\/\/.*$|#(?!\[).*$|\/\*(?:[^*]|\*(?!\/))*\*\/|"(?:\\[\s\S]|[^\\"])*"|'(?:\\[\s\S]|[^\\'])*')+\](?=\s*[a-z$#])/im,
            greedy: true,
            inside: {
              'attribute-content': {
                pattern: /^(#\[)[\s\S]+(?=\]$)/,
                lookbehind: true,
                inside: {
                  'comment': comment,
                  'string': string,
                  'attribute-class-name': [
                    {
                      pattern: /([^:]|^)\b[a-z_]\w*(?!\\)\b/i,
                      alias: 'class-name',
                      greedy: true,
                      lookbehind: true
                    },
                    {
                      pattern: /([^:]|^)(?:\\?\b[a-z_]\w*)+/i,
                      alias: [
                        'class-name',
                        'class-name-fully-qualified'
                      ],
                      greedy: true,
                      lookbehind: true,
                      inside: { 'punctuation': /\\/ }
                    }
                  ],
                  'constant': constant,
                  'number': number,
                  'operator': operator,
                  'punctuation': punctuation
                }
              },
              'delimiter': {
                pattern: /^#\[|\]$/,
                alias: 'punctuation'
              }
            }
          }
        });
        Prism.hooks.add('before-tokenize', function (env) {
          if (!/<\?/.test(env.code)) {
            return;
          }
          var phpPattern = /<\?(?:[^"'/#]|\/(?![*/])|("|')(?:\\[\s\S]|(?!\1)[^\\])*\1|(?:\/\/|#(?!\[))(?:[^?\n\r]|\?(?!>))*(?=$|\?>|[\r\n])|#\[|\/\*(?:[^*]|\*(?!\/))*(?:\*\/|$))*?(?:\?>|$)/g;
          Prism.languages['markup-templating'].buildPlaceholders(env, 'php', phpPattern);
        });
        Prism.hooks.add('after-tokenize', function (env) {
          Prism.languages['markup-templating'].tokenizePlaceholders(env, 'php');
        });
      }(Prism));
      Prism.languages.python = {
        'comment': {
          pattern: /(^|[^\\])#.*/,
          lookbehind: true,
          greedy: true
        },
        'string-interpolation': {
          pattern: /(?:f|fr|rf)(?:("""|''')[\s\S]*?\1|("|')(?:\\.|(?!\2)[^\\\r\n])*\2)/i,
          greedy: true,
          inside: {
            'interpolation': {
              pattern: /((?:^|[^{])(?:\{\{)*)\{(?!\{)(?:[^{}]|\{(?!\{)(?:[^{}]|\{(?!\{)(?:[^{}])+\})+\})+\}/,
              lookbehind: true,
              inside: {
                'format-spec': {
                  pattern: /(:)[^:(){}]+(?=\}$)/,
                  lookbehind: true
                },
                'conversion-option': {
                  pattern: /![sra](?=[:}]$)/,
                  alias: 'punctuation'
                },
                rest: null
              }
            },
            'string': /[\s\S]+/
          }
        },
        'triple-quoted-string': {
          pattern: /(?:[rub]|br|rb)?("""|''')[\s\S]*?\1/i,
          greedy: true,
          alias: 'string'
        },
        'string': {
          pattern: /(?:[rub]|br|rb)?("|')(?:\\.|(?!\1)[^\\\r\n])*\1/i,
          greedy: true
        },
        'function': {
          pattern: /((?:^|\s)def[ \t]+)[a-zA-Z_]\w*(?=\s*\()/g,
          lookbehind: true
        },
        'class-name': {
          pattern: /(\bclass\s+)\w+/i,
          lookbehind: true
        },
        'decorator': {
          pattern: /(^[\t ]*)@\w+(?:\.\w+)*/m,
          lookbehind: true,
          alias: [
            'annotation',
            'punctuation'
          ],
          inside: { 'punctuation': /\./ }
        },
        'keyword': /\b(?:_(?=\s*:)|and|as|assert|async|await|break|case|class|continue|def|del|elif|else|except|exec|finally|for|from|global|if|import|in|is|lambda|match|nonlocal|not|or|pass|print|raise|return|try|while|with|yield)\b/,
        'builtin': /\b(?:__import__|abs|all|any|apply|ascii|basestring|bin|bool|buffer|bytearray|bytes|callable|chr|classmethod|cmp|coerce|compile|complex|delattr|dict|dir|divmod|enumerate|eval|execfile|file|filter|float|format|frozenset|getattr|globals|hasattr|hash|help|hex|id|input|int|intern|isinstance|issubclass|iter|len|list|locals|long|map|max|memoryview|min|next|object|oct|open|ord|pow|property|range|raw_input|reduce|reload|repr|reversed|round|set|setattr|slice|sorted|staticmethod|str|sum|super|tuple|type|unichr|unicode|vars|xrange|zip)\b/,
        'boolean': /\b(?:False|None|True)\b/,
        'number': /\b0(?:b(?:_?[01])+|o(?:_?[0-7])+|x(?:_?[a-f0-9])+)\b|(?:\b\d+(?:_\d+)*(?:\.(?:\d+(?:_\d+)*)?)?|\B\.\d+(?:_\d+)*)(?:e[+-]?\d+(?:_\d+)*)?j?(?!\w)/i,
        'operator': /[-+%=]=?|!=|:=|\*\*?=?|\/\/?=?|<[<=>]?|>[=>]?|[&|^~]/,
        'punctuation': /[{}[\];(),.:]/
      };
      Prism.languages.python['string-interpolation'].inside['interpolation'].inside.rest = Prism.languages.python;
      Prism.languages.py = Prism.languages.python;
      (function (Prism) {
        Prism.languages.ruby = Prism.languages.extend('clike', {
          'comment': {
            pattern: /#.*|^=begin\s[\s\S]*?^=end/m,
            greedy: true
          },
          'class-name': {
            pattern: /(\b(?:class|module)\s+|\bcatch\s+\()[\w.\\]+|\b[A-Z_]\w*(?=\s*\.\s*new\b)/,
            lookbehind: true,
            inside: { 'punctuation': /[.\\]/ }
          },
          'keyword': /\b(?:BEGIN|END|alias|and|begin|break|case|class|def|define_method|defined|do|each|else|elsif|end|ensure|extend|for|if|in|include|module|new|next|nil|not|or|prepend|private|protected|public|raise|redo|require|rescue|retry|return|self|super|then|throw|undef|unless|until|when|while|yield)\b/,
          'operator': /\.{2,3}|&\.|===|<?=>|[!=]?~|(?:&&|\|\||<<|>>|\*\*|[+\-*/%<>!^&|=])=?|[?:]/,
          'punctuation': /[(){}[\].,;]/
        });
        Prism.languages.insertBefore('ruby', 'operator', {
          'double-colon': {
            pattern: /::/,
            alias: 'punctuation'
          }
        });
        var interpolation = {
          pattern: /((?:^|[^\\])(?:\\{2})*)#\{(?:[^{}]|\{[^{}]*\})*\}/,
          lookbehind: true,
          inside: {
            'content': {
              pattern: /^(#\{)[\s\S]+(?=\}$)/,
              lookbehind: true,
              inside: Prism.languages.ruby
            },
            'delimiter': {
              pattern: /^#\{|\}$/,
              alias: 'punctuation'
            }
          }
        };
        delete Prism.languages.ruby.function;
        var percentExpression = '(?:' + [
          /([^a-zA-Z0-9\s{(\[<=])(?:(?!\1)[^\\]|\\[\s\S])*\1/.source,
          /\((?:[^()\\]|\\[\s\S]|\((?:[^()\\]|\\[\s\S])*\))*\)/.source,
          /\{(?:[^{}\\]|\\[\s\S]|\{(?:[^{}\\]|\\[\s\S])*\})*\}/.source,
          /\[(?:[^\[\]\\]|\\[\s\S]|\[(?:[^\[\]\\]|\\[\s\S])*\])*\]/.source,
          /<(?:[^<>\\]|\\[\s\S]|<(?:[^<>\\]|\\[\s\S])*>)*>/.source
        ].join('|') + ')';
        var symbolName = /(?:"(?:\\.|[^"\\\r\n])*"|(?:\b[a-zA-Z_]\w*|[^\s\0-\x7F]+)[?!]?|\$.)/.source;
        Prism.languages.insertBefore('ruby', 'keyword', {
          'regex-literal': [
            {
              pattern: RegExp(/%r/.source + percentExpression + /[egimnosux]{0,6}/.source),
              greedy: true,
              inside: {
                'interpolation': interpolation,
                'regex': /[\s\S]+/
              }
            },
            {
              pattern: /(^|[^/])\/(?!\/)(?:\[[^\r\n\]]+\]|\\.|[^[/\\\r\n])+\/[egimnosux]{0,6}(?=\s*(?:$|[\r\n,.;})#]))/,
              lookbehind: true,
              greedy: true,
              inside: {
                'interpolation': interpolation,
                'regex': /[\s\S]+/
              }
            }
          ],
          'variable': /[@$]+[a-zA-Z_]\w*(?:[?!]|\b)/,
          'symbol': [
            {
              pattern: RegExp(/(^|[^:]):/.source + symbolName),
              lookbehind: true,
              greedy: true
            },
            {
              pattern: RegExp(/([\r\n{(,][ \t]*)/.source + symbolName + /(?=:(?!:))/.source),
              lookbehind: true,
              greedy: true
            }
          ],
          'method-definition': {
            pattern: /(\bdef\s+)\w+(?:\s*\.\s*\w+)?/,
            lookbehind: true,
            inside: {
              'function': /\b\w+$/,
              'keyword': /^self\b/,
              'class-name': /^\w+/,
              'punctuation': /\./
            }
          }
        });
        Prism.languages.insertBefore('ruby', 'string', {
          'string-literal': [
            {
              pattern: RegExp(/%[qQiIwWs]?/.source + percentExpression),
              greedy: true,
              inside: {
                'interpolation': interpolation,
                'string': /[\s\S]+/
              }
            },
            {
              pattern: /("|')(?:#\{[^}]+\}|#(?!\{)|\\(?:\r\n|[\s\S])|(?!\1)[^\\#\r\n])*\1/,
              greedy: true,
              inside: {
                'interpolation': interpolation,
                'string': /[\s\S]+/
              }
            },
            {
              pattern: /<<[-~]?([a-z_]\w*)[\r\n](?:.*[\r\n])*?[\t ]*\1/i,
              alias: 'heredoc-string',
              greedy: true,
              inside: {
                'delimiter': {
                  pattern: /^<<[-~]?[a-z_]\w*|\b[a-z_]\w*$/i,
                  inside: {
                    'symbol': /\b\w+/,
                    'punctuation': /^<<[-~]?/
                  }
                },
                'interpolation': interpolation,
                'string': /[\s\S]+/
              }
            },
            {
              pattern: /<<[-~]?'([a-z_]\w*)'[\r\n](?:.*[\r\n])*?[\t ]*\1/i,
              alias: 'heredoc-string',
              greedy: true,
              inside: {
                'delimiter': {
                  pattern: /^<<[-~]?'[a-z_]\w*'|\b[a-z_]\w*$/i,
                  inside: {
                    'symbol': /\b\w+/,
                    'punctuation': /^<<[-~]?'|'$/
                  }
                },
                'string': /[\s\S]+/
              }
            }
          ],
          'command-literal': [
            {
              pattern: RegExp(/%x/.source + percentExpression),
              greedy: true,
              inside: {
                'interpolation': interpolation,
                'command': {
                  pattern: /[\s\S]+/,
                  alias: 'string'
                }
              }
            },
            {
              pattern: /`(?:#\{[^}]+\}|#(?!\{)|\\(?:\r\n|[\s\S])|[^\\`#\r\n])*`/,
              greedy: true,
              inside: {
                'interpolation': interpolation,
                'command': {
                  pattern: /[\s\S]+/,
                  alias: 'string'
                }
              }
            }
          ]
        });
        delete Prism.languages.ruby.string;
        Prism.languages.insertBefore('ruby', 'number', {
          'builtin': /\b(?:Array|Bignum|Binding|Class|Continuation|Dir|Exception|FalseClass|File|Fixnum|Float|Hash|IO|Integer|MatchData|Method|Module|NilClass|Numeric|Object|Proc|Range|Regexp|Stat|String|Struct|Symbol|TMS|Thread|ThreadGroup|Time|TrueClass)\b/,
          'constant': /\b[A-Z][A-Z0-9_]*(?:[?!]|\b)/
        });
        Prism.languages.rb = Prism.languages.ruby;
      }(Prism));
      window.Prism = oldprism;
      return Prism;
    }(undefined, undefined);

    const option = name => editor => editor.options.get(name);
    const register$2 = editor => {
      const registerOption = editor.options.register;
      registerOption('codesample_languages', { processor: 'object[]' });
      registerOption('codesample_global_prismjs', {
        processor: 'boolean',
        default: false
      });
    };
    const getLanguages$1 = option('codesample_languages');
    const useGlobalPrismJS = option('codesample_global_prismjs');

    const get = editor => Global.Prism && useGlobalPrismJS(editor) ? Global.Prism : prismjs;

    const isCodeSample = elm => {
      return isNonNullable(elm) && elm.nodeName === 'PRE' && elm.className.indexOf('language-') !== -1;
    };

    const getSelectedCodeSample = editor => {
      const node = editor.selection ? editor.selection.getNode() : null;
      return isCodeSample(node) ? Optional.some(node) : Optional.none();
    };
    const insertCodeSample = (editor, language, code) => {
      const dom = editor.dom;
      editor.undoManager.transact(() => {
        const node = getSelectedCodeSample(editor);
        code = global$1.DOM.encode(code);
        return node.fold(() => {
          editor.insertContent('<pre id="__new" class="language-' + language + '">' + code + '</pre>');
          const newPre = dom.select('#__new')[0];
          dom.setAttrib(newPre, 'id', null);
          editor.selection.select(newPre);
        }, n => {
          dom.setAttrib(n, 'class', 'language-' + language);
          n.innerHTML = code;
          get(editor).highlightElement(n);
          editor.selection.select(n);
        });
      });
    };
    const getCurrentCode = editor => {
      const node = getSelectedCodeSample(editor);
      return node.bind(n => Optional.from(n.textContent)).getOr('');
    };

    const getLanguages = editor => {
      const defaultLanguages = [
        {
          text: 'HTML/XML',
          value: 'markup'
        },
        {
          text: 'JavaScript',
          value: 'javascript'
        },
        {
          text: 'CSS',
          value: 'css'
        },
        {
          text: 'PHP',
          value: 'php'
        },
        {
          text: 'Ruby',
          value: 'ruby'
        },
        {
          text: 'Python',
          value: 'python'
        },
        {
          text: 'Java',
          value: 'java'
        },
        {
          text: 'C',
          value: 'c'
        },
        {
          text: 'C#',
          value: 'csharp'
        },
        {
          text: 'C++',
          value: 'cpp'
        }
      ];
      const customLanguages = getLanguages$1(editor);
      return customLanguages ? customLanguages : defaultLanguages;
    };
    const getCurrentLanguage = (editor, fallback) => {
      const node = getSelectedCodeSample(editor);
      return node.fold(() => fallback, n => {
        const matches = n.className.match(/language-(\w+)/);
        return matches ? matches[1] : fallback;
      });
    };

    const open = editor => {
      const languages = getLanguages(editor);
      const defaultLanguage = head(languages).fold(constant(''), l => l.value);
      const currentLanguage = getCurrentLanguage(editor, defaultLanguage);
      const currentCode = getCurrentCode(editor);
      editor.windowManager.open({
        title: 'Insert/Edit Code Sample',
        size: 'large',
        body: {
          type: 'panel',
          items: [
            {
              type: 'selectbox',
              name: 'language',
              label: 'Language',
              items: languages
            },
            {
              type: 'textarea',
              name: 'code',
              label: 'Code view'
            }
          ]
        },
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
        initialData: {
          language: currentLanguage,
          code: currentCode
        },
        onSubmit: api => {
          const data = api.getData();
          insertCodeSample(editor, data.language, data.code);
          api.close();
        }
      });
    };

    const register$1 = editor => {
      editor.addCommand('codesample', () => {
        const node = editor.selection.getNode();
        if (editor.selection.isCollapsed() || isCodeSample(node)) {
          open(editor);
        } else {
          editor.formatter.toggle('code');
        }
      });
    };

    const blank = r => s => s.replace(r, '');
    const trim = blank(/^\s+|\s+$/g);

    var global = tinymce.util.Tools.resolve('tinymce.util.Tools');

    const setup = editor => {
      editor.on('PreProcess', e => {
        const dom = editor.dom;
        const pres = dom.select('pre[contenteditable=false]', e.node);
        global.each(global.grep(pres, isCodeSample), elm => {
          const code = elm.textContent;
          dom.setAttrib(elm, 'class', trim(dom.getAttrib(elm, 'class')));
          dom.setAttrib(elm, 'contentEditable', null);
          dom.setAttrib(elm, 'data-mce-highlighted', null);
          let child;
          while (child = elm.firstChild) {
            elm.removeChild(child);
          }
          const codeElm = dom.add(elm, 'code');
          codeElm.textContent = code;
        });
      });
      editor.on('SetContent', () => {
        const dom = editor.dom;
        const unprocessedCodeSamples = global.grep(dom.select('pre'), elm => {
          return isCodeSample(elm) && dom.getAttrib(elm, 'data-mce-highlighted') !== 'true';
        });
        if (unprocessedCodeSamples.length) {
          editor.undoManager.transact(() => {
            global.each(unprocessedCodeSamples, elm => {
              var _a;
              global.each(dom.select('br', elm), elm => {
                dom.replace(editor.getDoc().createTextNode('\n'), elm);
              });
              elm.innerHTML = dom.encode((_a = elm.textContent) !== null && _a !== void 0 ? _a : '');
              get(editor).highlightElement(elm);
              dom.setAttrib(elm, 'data-mce-highlighted', true);
              elm.className = trim(elm.className);
            });
          });
        }
      });
      editor.on('PreInit', () => {
        editor.parser.addNodeFilter('pre', nodes => {
          var _a;
          for (let i = 0, l = nodes.length; i < l; i++) {
            const node = nodes[i];
            const isCodeSample = ((_a = node.attr('class')) !== null && _a !== void 0 ? _a : '').indexOf('language-') !== -1;
            if (isCodeSample) {
              node.attr('contenteditable', 'false');
              node.attr('data-mce-highlighted', 'false');
            }
          }
        });
      });
    };

    const isCodeSampleSelection = editor => {
      const node = editor.selection.getStart();
      return editor.dom.is(node, 'pre[class*="language-"]');
    };
    const register = editor => {
      const onAction = () => editor.execCommand('codesample');
      editor.ui.registry.addToggleButton('codesample', {
        icon: 'code-sample',
        tooltip: 'Insert/edit code sample',
        onAction,
        onSetup: api => {
          const nodeChangeHandler = () => {
            api.setActive(isCodeSampleSelection(editor));
          };
          editor.on('NodeChange', nodeChangeHandler);
          return () => editor.off('NodeChange', nodeChangeHandler);
        }
      });
      editor.ui.registry.addMenuItem('codesample', {
        text: 'Code sample...',
        icon: 'code-sample',
        onAction
      });
    };

    var Plugin = () => {
      global$2.add('codesample', editor => {
        register$2(editor);
        setup(editor);
        register(editor);
        register$1(editor);
        editor.on('dblclick', ev => {
          if (isCodeSample(ev.target)) {
            open(editor);
          }
        });
      });
    };

    Plugin();

})();
