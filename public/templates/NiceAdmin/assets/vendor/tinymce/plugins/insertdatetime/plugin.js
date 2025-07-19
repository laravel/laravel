/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    var global$1 = tinymce.util.Tools.resolve('tinymce.PluginManager');

    const option = name => editor => editor.options.get(name);
    const register$2 = editor => {
      const registerOption = editor.options.register;
      registerOption('insertdatetime_dateformat', {
        processor: 'string',
        default: editor.translate('%Y-%m-%d')
      });
      registerOption('insertdatetime_timeformat', {
        processor: 'string',
        default: editor.translate('%H:%M:%S')
      });
      registerOption('insertdatetime_formats', {
        processor: 'string[]',
        default: [
          '%H:%M:%S',
          '%Y-%m-%d',
          '%I:%M:%S %p',
          '%D'
        ]
      });
      registerOption('insertdatetime_element', {
        processor: 'boolean',
        default: false
      });
    };
    const getDateFormat = option('insertdatetime_dateformat');
    const getTimeFormat = option('insertdatetime_timeformat');
    const getFormats = option('insertdatetime_formats');
    const shouldInsertTimeElement = option('insertdatetime_element');
    const getDefaultDateTime = editor => {
      const formats = getFormats(editor);
      return formats.length > 0 ? formats[0] : getTimeFormat(editor);
    };

    const daysShort = 'Sun Mon Tue Wed Thu Fri Sat Sun'.split(' ');
    const daysLong = 'Sunday Monday Tuesday Wednesday Thursday Friday Saturday Sunday'.split(' ');
    const monthsShort = 'Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec'.split(' ');
    const monthsLong = 'January February March April May June July August September October November December'.split(' ');
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
    const updateElement = (editor, timeElm, computerTime, userTime) => {
      const newTimeElm = editor.dom.create('time', { datetime: computerTime }, userTime);
      editor.dom.replace(newTimeElm, timeElm);
      editor.selection.select(newTimeElm, true);
      editor.selection.collapse(false);
    };
    const insertDateTime = (editor, format) => {
      if (shouldInsertTimeElement(editor)) {
        const userTime = getDateTime(editor, format);
        let computerTime;
        if (/%[HMSIp]/.test(format)) {
          computerTime = getDateTime(editor, '%Y-%m-%dT%H:%M');
        } else {
          computerTime = getDateTime(editor, '%Y-%m-%d');
        }
        const timeElm = editor.dom.getParent(editor.selection.getStart(), 'time');
        if (timeElm) {
          updateElement(editor, timeElm, computerTime, userTime);
        } else {
          editor.insertContent('<time datetime="' + computerTime + '">' + userTime + '</time>');
        }
      } else {
        editor.insertContent(getDateTime(editor, format));
      }
    };

    const register$1 = editor => {
      editor.addCommand('mceInsertDate', (_ui, value) => {
        insertDateTime(editor, value !== null && value !== void 0 ? value : getDateFormat(editor));
      });
      editor.addCommand('mceInsertTime', (_ui, value) => {
        insertDateTime(editor, value !== null && value !== void 0 ? value : getTimeFormat(editor));
      });
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

    var global = tinymce.util.Tools.resolve('tinymce.util.Tools');

    const register = editor => {
      const formats = getFormats(editor);
      const defaultFormat = Cell(getDefaultDateTime(editor));
      const insertDateTime = format => editor.execCommand('mceInsertDate', false, format);
      editor.ui.registry.addSplitButton('insertdatetime', {
        icon: 'insert-time',
        tooltip: 'Insert date/time',
        select: value => value === defaultFormat.get(),
        fetch: done => {
          done(global.map(formats, format => ({
            type: 'choiceitem',
            text: getDateTime(editor, format),
            value: format
          })));
        },
        onAction: _api => {
          insertDateTime(defaultFormat.get());
        },
        onItemAction: (_api, value) => {
          defaultFormat.set(value);
          insertDateTime(value);
        }
      });
      const makeMenuItemHandler = format => () => {
        defaultFormat.set(format);
        insertDateTime(format);
      };
      editor.ui.registry.addNestedMenuItem('insertdatetime', {
        icon: 'insert-time',
        text: 'Date/time',
        getSubmenuItems: () => global.map(formats, format => ({
          type: 'menuitem',
          text: getDateTime(editor, format),
          onAction: makeMenuItemHandler(format)
        }))
      });
    };

    var Plugin = () => {
      global$1.add('insertdatetime', editor => {
        register$2(editor);
        register$1(editor);
        register(editor);
      });
    };

    Plugin();

})();
