/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    var global$4 = tinymce.util.Tools.resolve('tinymce.PluginManager');

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
    const isString = isType('string');
    const isObject = isType('object');
    const isArray = isType('array');
    const isFunction = isSimpleType('function');

    var global$3 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');

    var global$2 = tinymce.util.Tools.resolve('tinymce.EditorManager');

    var global$1 = tinymce.util.Tools.resolve('tinymce.Env');

    var global = tinymce.util.Tools.resolve('tinymce.util.Tools');

    const option = name => editor => editor.options.get(name);
    const register = editor => {
      const registerOption = editor.options.register;
      const filterProcessor = value => isString(value) || isFunction(value) || isObject(value);
      registerOption('importcss_merge_classes', {
        processor: 'boolean',
        default: true
      });
      registerOption('importcss_exclusive', {
        processor: 'boolean',
        default: true
      });
      registerOption('importcss_selector_converter', { processor: 'function' });
      registerOption('importcss_selector_filter', { processor: filterProcessor });
      registerOption('importcss_file_filter', { processor: filterProcessor });
      registerOption('importcss_groups', { processor: 'object[]' });
      registerOption('importcss_append', {
        processor: 'boolean',
        default: false
      });
    };
    const shouldMergeClasses = option('importcss_merge_classes');
    const shouldImportExclusive = option('importcss_exclusive');
    const getSelectorConverter = option('importcss_selector_converter');
    const getSelectorFilter = option('importcss_selector_filter');
    const getCssGroups = option('importcss_groups');
    const shouldAppend = option('importcss_append');
    const getFileFilter = option('importcss_file_filter');
    const getSkin = option('skin');
    const getSkinUrl = option('skin_url');

    const nativePush = Array.prototype.push;
    const map = (xs, f) => {
      const len = xs.length;
      const r = new Array(len);
      for (let i = 0; i < len; i++) {
        const x = xs[i];
        r[i] = f(x, i);
      }
      return r;
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
    const bind = (xs, f) => flatten(map(xs, f));

    const generate = () => {
      const ungroupedOrder = [];
      const groupOrder = [];
      const groups = {};
      const addItemToGroup = (groupTitle, itemInfo) => {
        if (groups[groupTitle]) {
          groups[groupTitle].push(itemInfo);
        } else {
          groupOrder.push(groupTitle);
          groups[groupTitle] = [itemInfo];
        }
      };
      const addItem = itemInfo => {
        ungroupedOrder.push(itemInfo);
      };
      const toFormats = () => {
        const groupItems = bind(groupOrder, g => {
          const items = groups[g];
          return items.length === 0 ? [] : [{
              title: g,
              items
            }];
        });
        return groupItems.concat(ungroupedOrder);
      };
      return {
        addItemToGroup,
        addItem,
        toFormats
      };
    };

    const internalEditorStyle = /^\.(?:ephox|tiny-pageembed|mce)(?:[.-]+\w+)+$/;
    const removeCacheSuffix = url => {
      const cacheSuffix = global$1.cacheSuffix;
      if (isString(url)) {
        url = url.replace('?' + cacheSuffix, '').replace('&' + cacheSuffix, '');
      }
      return url;
    };
    const isSkinContentCss = (editor, href) => {
      const skin = getSkin(editor);
      if (skin) {
        const skinUrlBase = getSkinUrl(editor);
        const skinUrl = skinUrlBase ? editor.documentBaseURI.toAbsolute(skinUrlBase) : global$2.baseURL + '/skins/ui/' + skin;
        const contentSkinUrlPart = global$2.baseURL + '/skins/content/';
        return href === skinUrl + '/content' + (editor.inline ? '.inline' : '') + '.min.css' || href.indexOf(contentSkinUrlPart) !== -1;
      }
      return false;
    };
    const compileFilter = filter => {
      if (isString(filter)) {
        return value => {
          return value.indexOf(filter) !== -1;
        };
      } else if (filter instanceof RegExp) {
        return value => {
          return filter.test(value);
        };
      }
      return filter;
    };
    const isCssImportRule = rule => rule.styleSheet;
    const isCssPageRule = rule => rule.selectorText;
    const getSelectors = (editor, doc, fileFilter) => {
      const selectors = [];
      const contentCSSUrls = {};
      const append = (styleSheet, imported) => {
        let href = styleSheet.href;
        let rules;
        href = removeCacheSuffix(href);
        if (!href || fileFilter && !fileFilter(href, imported) || isSkinContentCss(editor, href)) {
          return;
        }
        global.each(styleSheet.imports, styleSheet => {
          append(styleSheet, true);
        });
        try {
          rules = styleSheet.cssRules || styleSheet.rules;
        } catch (e) {
        }
        global.each(rules, cssRule => {
          if (isCssImportRule(cssRule)) {
            append(cssRule.styleSheet, true);
          } else if (isCssPageRule(cssRule)) {
            global.each(cssRule.selectorText.split(','), selector => {
              selectors.push(global.trim(selector));
            });
          }
        });
      };
      global.each(editor.contentCSS, url => {
        contentCSSUrls[url] = true;
      });
      if (!fileFilter) {
        fileFilter = (href, imported) => {
          return imported || contentCSSUrls[href];
        };
      }
      try {
        global.each(doc.styleSheets, styleSheet => {
          append(styleSheet);
        });
      } catch (e) {
      }
      return selectors;
    };
    const defaultConvertSelectorToFormat = (editor, selectorText) => {
      let format = {};
      const selector = /^(?:([a-z0-9\-_]+))?(\.[a-z0-9_\-\.]+)$/i.exec(selectorText);
      if (!selector) {
        return;
      }
      const elementName = selector[1];
      const classes = selector[2].substr(1).split('.').join(' ');
      const inlineSelectorElements = global.makeMap('a,img');
      if (selector[1]) {
        format = { title: selectorText };
        if (editor.schema.getTextBlockElements()[elementName]) {
          format.block = elementName;
        } else if (editor.schema.getBlockElements()[elementName] || inlineSelectorElements[elementName.toLowerCase()]) {
          format.selector = elementName;
        } else {
          format.inline = elementName;
        }
      } else if (selector[2]) {
        format = {
          inline: 'span',
          title: selectorText.substr(1),
          classes
        };
      }
      if (shouldMergeClasses(editor)) {
        format.classes = classes;
      } else {
        format.attributes = { class: classes };
      }
      return format;
    };
    const getGroupsBySelector = (groups, selector) => {
      return global.grep(groups, group => {
        return !group.filter || group.filter(selector);
      });
    };
    const compileUserDefinedGroups = groups => {
      return global.map(groups, group => {
        return global.extend({}, group, {
          original: group,
          selectors: {},
          filter: compileFilter(group.filter)
        });
      });
    };
    const isExclusiveMode = (editor, group) => {
      return group === null || shouldImportExclusive(editor);
    };
    const isUniqueSelector = (editor, selector, group, globallyUniqueSelectors) => {
      return !(isExclusiveMode(editor, group) ? selector in globallyUniqueSelectors : selector in group.selectors);
    };
    const markUniqueSelector = (editor, selector, group, globallyUniqueSelectors) => {
      if (isExclusiveMode(editor, group)) {
        globallyUniqueSelectors[selector] = true;
      } else {
        group.selectors[selector] = true;
      }
    };
    const convertSelectorToFormat = (editor, plugin, selector, group) => {
      let selectorConverter;
      const converter = getSelectorConverter(editor);
      if (group && group.selector_converter) {
        selectorConverter = group.selector_converter;
      } else if (converter) {
        selectorConverter = converter;
      } else {
        selectorConverter = () => {
          return defaultConvertSelectorToFormat(editor, selector);
        };
      }
      return selectorConverter.call(plugin, selector, group);
    };
    const setup = editor => {
      editor.on('init', () => {
        const model = generate();
        const globallyUniqueSelectors = {};
        const selectorFilter = compileFilter(getSelectorFilter(editor));
        const groups = compileUserDefinedGroups(getCssGroups(editor));
        const processSelector = (selector, group) => {
          if (isUniqueSelector(editor, selector, group, globallyUniqueSelectors)) {
            markUniqueSelector(editor, selector, group, globallyUniqueSelectors);
            const format = convertSelectorToFormat(editor, editor.plugins.importcss, selector, group);
            if (format) {
              const formatName = format.name || global$3.DOM.uniqueId();
              editor.formatter.register(formatName, format);
              return {
                title: format.title,
                format: formatName
              };
            }
          }
          return null;
        };
        global.each(getSelectors(editor, editor.getDoc(), compileFilter(getFileFilter(editor))), selector => {
          if (!internalEditorStyle.test(selector)) {
            if (!selectorFilter || selectorFilter(selector)) {
              const selectorGroups = getGroupsBySelector(groups, selector);
              if (selectorGroups.length > 0) {
                global.each(selectorGroups, group => {
                  const menuItem = processSelector(selector, group);
                  if (menuItem) {
                    model.addItemToGroup(group.title, menuItem);
                  }
                });
              } else {
                const menuItem = processSelector(selector, null);
                if (menuItem) {
                  model.addItem(menuItem);
                }
              }
            }
          }
        });
        const items = model.toFormats();
        editor.dispatch('addStyleModifications', {
          items,
          replace: !shouldAppend(editor)
        });
      });
    };

    const get = editor => {
      const convertSelectorToFormat = selectorText => {
        return defaultConvertSelectorToFormat(editor, selectorText);
      };
      return { convertSelectorToFormat };
    };

    var Plugin = () => {
      global$4.add('importcss', editor => {
        register(editor);
        setup(editor);
        return get(editor);
      });
    };

    Plugin();

})();
