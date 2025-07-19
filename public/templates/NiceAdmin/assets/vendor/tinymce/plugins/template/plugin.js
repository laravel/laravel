/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    var global$2 = tinymce.util.Tools.resolve('tinymce.PluginManager');

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
    const isNullable = a => a === null || a === undefined;
    const isNonNullable = a => !isNullable(a);
    const isFunction = isSimpleType('function');
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

    const constant = value => {
      return () => {
        return value;
      };
    };
    function curry(fn, ...initialArgs) {
      return (...restArgs) => {
        const all = initialArgs.concat(restArgs);
        return fn.apply(null, all);
      };
    }
    const never = constant(false);

    const escape = text => text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    const option = name => editor => editor.options.get(name);
    const register$2 = editor => {
      const registerOption = editor.options.register;
      registerOption('template_cdate_classes', {
        processor: 'string',
        default: 'cdate'
      });
      registerOption('template_mdate_classes', {
        processor: 'string',
        default: 'mdate'
      });
      registerOption('template_selected_content_classes', {
        processor: 'string',
        default: 'selcontent'
      });
      registerOption('template_preview_replace_values', { processor: 'object' });
      registerOption('template_replace_values', { processor: 'object' });
      registerOption('templates', {
        processor: value => isString(value) || isArrayOf(value, isObject) || isFunction(value),
        default: []
      });
      registerOption('template_cdate_format', {
        processor: 'string',
        default: editor.translate('%Y-%m-%d')
      });
      registerOption('template_mdate_format', {
        processor: 'string',
        default: editor.translate('%Y-%m-%d')
      });
    };
    const getCreationDateClasses = option('template_cdate_classes');
    const getModificationDateClasses = option('template_mdate_classes');
    const getSelectedContentClasses = option('template_selected_content_classes');
    const getPreviewReplaceValues = option('template_preview_replace_values');
    const getTemplateReplaceValues = option('template_replace_values');
    const getTemplates = option('templates');
    const getCdateFormat = option('template_cdate_format');
    const getMdateFormat = option('template_mdate_format');
    const getContentStyle = option('content_style');
    const shouldUseContentCssCors = option('content_css_cors');
    const getBodyClass = option('body_class');

    const addZeros = (value, len) => {
      value = '' + value;
      if (value.length < len) {
        for (let i = 0; i < len - value.length; i++) {
          value = '0' + value;
        }
      }
      return value;
    };
    const getDateTime = (editor, fmt, date = new Date()) => {
      const daysShort = 'Sun Mon Tue Wed Thu Fri Sat Sun'.split(' ');
      const daysLong = 'Sunday Monday Tuesday Wednesday Thursday Friday Saturday Sunday'.split(' ');
      const monthsShort = 'Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec'.split(' ');
      const monthsLong = 'January February March April May June July August September October November December'.split(' ');
      fmt = fmt.replace('%D', '%m/%d/%Y');
      fmt = fmt.replace('%r', '%I:%M:%S %p');
      fmt = fmt.replace('%Y', '' + date.getFullYear());
      fmt = fmt.replace('%y', '' + date.getYear());
      fmt = fmt.replace('%m', addZeros(date.getMonth() + 1, 2));
      fmt = fmt.replace('%d', addZeros(date.getDate(), 2));
      fmt = fmt.replace('%H', '' + addZeros(date.getHours(), 2));
      fmt = fmt.replace('%M', '' + addZeros(date.getMinutes(), 2));
      fmt = fmt.replace('%S', '' + addZeros(date.getSeconds(), 2));
      fmt = fmt.replace('%I', '' + ((date.getHours() + 11) % 12 + 1));
      fmt = fmt.replace('%p', '' + (date.getHours() < 12 ? 'AM' : 'PM'));
      fmt = fmt.replace('%B', '' + editor.translate(monthsLong[date.getMonth()]));
      fmt = fmt.replace('%b', '' + editor.translate(monthsShort[date.getMonth()]));
      fmt = fmt.replace('%A', '' + editor.translate(daysLong[date.getDay()]));
      fmt = fmt.replace('%a', '' + editor.translate(daysShort[date.getDay()]));
      fmt = fmt.replace('%%', '%');
      return fmt;
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

    const exists = (xs, pred) => {
      for (let i = 0, len = xs.length; i < len; i++) {
        const x = xs[i];
        if (pred(x, i)) {
          return true;
        }
      }
      return false;
    };
    const map = (xs, f) => {
      const len = xs.length;
      const r = new Array(len);
      for (let i = 0; i < len; i++) {
        const x = xs[i];
        r[i] = f(x, i);
      }
      return r;
    };
    const findUntil = (xs, pred, until) => {
      for (let i = 0, len = xs.length; i < len; i++) {
        const x = xs[i];
        if (pred(x, i)) {
          return Optional.some(x);
        } else if (until(x, i)) {
          break;
        }
      }
      return Optional.none();
    };
    const find = (xs, pred) => {
      return findUntil(xs, pred, never);
    };

    const hasOwnProperty = Object.hasOwnProperty;
    const get = (obj, key) => {
      return has(obj, key) ? Optional.from(obj[key]) : Optional.none();
    };
    const has = (obj, key) => hasOwnProperty.call(obj, key);

    const entitiesAttr = {
      '"': '&quot;',
      '<': '&lt;',
      '>': '&gt;',
      '&': '&amp;',
      '\'': '&#039;'
    };
    const htmlEscape = html => html.replace(/["'<>&]/g, match => get(entitiesAttr, match).getOr(match));
    const hasAnyClasses = (dom, n, classes) => exists(classes.split(/\s+/), c => dom.hasClass(n, c));

    const createTemplateList = (editor, callback) => {
      return () => {
        const templateList = getTemplates(editor);
        if (isFunction(templateList)) {
          templateList(callback);
        } else if (isString(templateList)) {
          fetch(templateList).then(res => {
            if (res.ok) {
              res.json().then(callback);
            }
          });
        } else {
          callback(templateList);
        }
      };
    };
    const replaceTemplateValues = (html, templateValues) => {
      global$1.each(templateValues, (v, k) => {
        if (isFunction(v)) {
          v = v(k);
        }
        html = html.replace(new RegExp('\\{\\$' + escape(k) + '\\}', 'g'), v);
      });
      return html;
    };
    const replaceVals = (editor, scope) => {
      const dom = editor.dom, vl = getTemplateReplaceValues(editor);
      global$1.each(dom.select('*', scope), e => {
        global$1.each(vl, (v, k) => {
          if (dom.hasClass(e, k)) {
            if (isFunction(v)) {
              v(e);
            }
          }
        });
      });
    };
    const insertTemplate = (editor, _ui, html) => {
      const dom = editor.dom;
      const sel = editor.selection.getContent();
      html = replaceTemplateValues(html, getTemplateReplaceValues(editor));
      let el = dom.create('div', {}, html);
      const n = dom.select('.mceTmpl', el);
      if (n && n.length > 0) {
        el = dom.create('div');
        el.appendChild(n[0].cloneNode(true));
      }
      global$1.each(dom.select('*', el), n => {
        if (hasAnyClasses(dom, n, getCreationDateClasses(editor))) {
          n.innerHTML = getDateTime(editor, getCdateFormat(editor));
        }
        if (hasAnyClasses(dom, n, getModificationDateClasses(editor))) {
          n.innerHTML = getDateTime(editor, getMdateFormat(editor));
        }
        if (hasAnyClasses(dom, n, getSelectedContentClasses(editor))) {
          n.innerHTML = sel;
        }
      });
      replaceVals(editor, el);
      editor.execCommand('mceInsertContent', false, el.innerHTML);
      editor.addVisual();
    };

    var global = tinymce.util.Tools.resolve('tinymce.Env');

    const getPreviewContent = (editor, html) => {
      var _a;
      if (html.indexOf('<html>') === -1) {
        let contentCssEntries = '';
        const contentStyle = (_a = getContentStyle(editor)) !== null && _a !== void 0 ? _a : '';
        const cors = shouldUseContentCssCors(editor) ? ' crossorigin="anonymous"' : '';
        global$1.each(editor.contentCSS, url => {
          contentCssEntries += '<link type="text/css" rel="stylesheet" href="' + editor.documentBaseURI.toAbsolute(url) + '"' + cors + '>';
        });
        if (contentStyle) {
          contentCssEntries += '<style type="text/css">' + contentStyle + '</style>';
        }
        const bodyClass = getBodyClass(editor);
        const encode = editor.dom.encode;
        const isMetaKeyPressed = global.os.isMacOS() || global.os.isiOS() ? 'e.metaKey' : 'e.ctrlKey && !e.altKey';
        const preventClicksOnLinksScript = '<script>' + 'document.addEventListener && document.addEventListener("click", function(e) {' + 'for (var elm = e.target; elm; elm = elm.parentNode) {' + 'if (elm.nodeName === "A" && !(' + isMetaKeyPressed + ')) {' + 'e.preventDefault();' + '}' + '}' + '}, false);' + '</script> ';
        const directionality = editor.getBody().dir;
        const dirAttr = directionality ? ' dir="' + encode(directionality) + '"' : '';
        html = '<!DOCTYPE html>' + '<html>' + '<head>' + '<base href="' + encode(editor.documentBaseURI.getURI()) + '">' + contentCssEntries + preventClicksOnLinksScript + '</head>' + '<body class="' + encode(bodyClass) + '"' + dirAttr + '>' + html + '</body>' + '</html>';
      }
      return replaceTemplateValues(html, getPreviewReplaceValues(editor));
    };
    const open = (editor, templateList) => {
      const createTemplates = () => {
        if (!templateList || templateList.length === 0) {
          const message = editor.translate('No templates defined.');
          editor.notificationManager.open({
            text: message,
            type: 'info'
          });
          return Optional.none();
        }
        return Optional.from(global$1.map(templateList, (template, index) => {
          const isUrlTemplate = t => t.url !== undefined;
          return {
            selected: index === 0,
            text: template.title,
            value: {
              url: isUrlTemplate(template) ? Optional.from(template.url) : Optional.none(),
              content: !isUrlTemplate(template) ? Optional.from(template.content) : Optional.none(),
              description: template.description
            }
          };
        }));
      };
      const createSelectBoxItems = templates => map(templates, t => ({
        text: t.text,
        value: t.text
      }));
      const findTemplate = (templates, templateTitle) => find(templates, t => t.text === templateTitle);
      const loadFailedAlert = api => {
        editor.windowManager.alert('Could not load the specified template.', () => api.focus('template'));
      };
      const getTemplateContent = t => t.value.url.fold(() => Promise.resolve(t.value.content.getOr('')), url => fetch(url).then(res => res.ok ? res.text() : Promise.reject()));
      const onChange = (templates, updateDialog) => (api, change) => {
        if (change.name === 'template') {
          const newTemplateTitle = api.getData().template;
          findTemplate(templates, newTemplateTitle).each(t => {
            api.block('Loading...');
            getTemplateContent(t).then(previewHtml => {
              updateDialog(api, t, previewHtml);
            }).catch(() => {
              updateDialog(api, t, '');
              api.setEnabled('save', false);
              loadFailedAlert(api);
            });
          });
        }
      };
      const onSubmit = templates => api => {
        const data = api.getData();
        findTemplate(templates, data.template).each(t => {
          getTemplateContent(t).then(previewHtml => {
            editor.execCommand('mceInsertTemplate', false, previewHtml);
            api.close();
          }).catch(() => {
            api.setEnabled('save', false);
            loadFailedAlert(api);
          });
        });
      };
      const openDialog = templates => {
        const selectBoxItems = createSelectBoxItems(templates);
        const buildDialogSpec = (bodyItems, initialData) => ({
          title: 'Insert Template',
          size: 'large',
          body: {
            type: 'panel',
            items: bodyItems
          },
          initialData,
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
          onSubmit: onSubmit(templates),
          onChange: onChange(templates, updateDialog)
        });
        const updateDialog = (dialogApi, template, previewHtml) => {
          const content = getPreviewContent(editor, previewHtml);
          const bodyItems = [
            {
              type: 'selectbox',
              name: 'template',
              label: 'Templates',
              items: selectBoxItems
            },
            {
              type: 'htmlpanel',
              html: `<p aria-live="polite">${ htmlEscape(template.value.description) }</p>`
            },
            {
              label: 'Preview',
              type: 'iframe',
              name: 'preview',
              sandboxed: false,
              transparent: false
            }
          ];
          const initialData = {
            template: template.text,
            preview: content
          };
          dialogApi.unblock();
          dialogApi.redial(buildDialogSpec(bodyItems, initialData));
          dialogApi.focus('template');
        };
        const dialogApi = editor.windowManager.open(buildDialogSpec([], {
          template: '',
          preview: ''
        }));
        dialogApi.block('Loading...');
        getTemplateContent(templates[0]).then(previewHtml => {
          updateDialog(dialogApi, templates[0], previewHtml);
        }).catch(() => {
          updateDialog(dialogApi, templates[0], '');
          dialogApi.setEnabled('save', false);
          loadFailedAlert(dialogApi);
        });
      };
      const optTemplates = createTemplates();
      optTemplates.each(openDialog);
    };

    const showDialog = editor => templates => {
      open(editor, templates);
    };
    const register$1 = editor => {
      editor.addCommand('mceInsertTemplate', curry(insertTemplate, editor));
      editor.addCommand('mceTemplate', createTemplateList(editor, showDialog(editor)));
    };

    const setup = editor => {
      editor.on('PreProcess', o => {
        const dom = editor.dom, dateFormat = getMdateFormat(editor);
        global$1.each(dom.select('div', o.node), e => {
          if (dom.hasClass(e, 'mceTmpl')) {
            global$1.each(dom.select('*', e), e => {
              if (hasAnyClasses(dom, e, getModificationDateClasses(editor))) {
                e.innerHTML = getDateTime(editor, dateFormat);
              }
            });
            replaceVals(editor, e);
          }
        });
      });
    };

    const register = editor => {
      const onAction = () => editor.execCommand('mceTemplate');
      editor.ui.registry.addButton('template', {
        icon: 'template',
        tooltip: 'Insert template',
        onAction
      });
      editor.ui.registry.addMenuItem('template', {
        icon: 'template',
        text: 'Insert template...',
        onAction
      });
    };

    var Plugin = () => {
      global$2.add('template', editor => {
        register$2(editor);
        register(editor);
        register$1(editor);
        setup(editor);
      });
    };

    Plugin();

})();
