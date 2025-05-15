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

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var get = function (customTabs) {
      var addTab = function (spec) {
        var currentCustomTabs = customTabs.get();
        currentCustomTabs[spec.name] = spec;
        customTabs.set(currentCustomTabs);
      };
      return { addTab: addTab };
    };

    var register = function (editor, dialogOpener) {
      editor.addCommand('mceHelp', dialogOpener);
    };

    var register$1 = function (editor, dialogOpener) {
      editor.ui.registry.addButton('help', {
        icon: 'help',
        tooltip: 'Help',
        onAction: dialogOpener
      });
      editor.ui.registry.addMenuItem('help', {
        text: 'Help',
        icon: 'help',
        shortcut: 'Alt+0',
        onAction: dialogOpener
      });
    };

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

    var nativeIndexOf = Array.prototype.indexOf;
    var rawIndexOf = function (ts, t) {
      return nativeIndexOf.call(ts, t);
    };
    var contains = function (xs, x) {
      return rawIndexOf(xs, x) > -1;
    };
    var map = function (xs, f) {
      var len = xs.length;
      var r = new Array(len);
      for (var i = 0; i < len; i++) {
        var x = xs[i];
        r[i] = f(x, i);
      }
      return r;
    };
    var filter = function (xs, pred) {
      var r = [];
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          r.push(x);
        }
      }
      return r;
    };
    var findUntil = function (xs, pred, until) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          return Optional.some(x);
        } else if (until(x, i)) {
          break;
        }
      }
      return Optional.none();
    };
    var find = function (xs, pred) {
      return findUntil(xs, pred, never);
    };

    var keys = Object.keys;
    var hasOwnProperty = Object.hasOwnProperty;
    var get$1 = function (obj, key) {
      return has(obj, key) ? Optional.from(obj[key]) : Optional.none();
    };
    var has = function (obj, key) {
      return hasOwnProperty.call(obj, key);
    };

    var cat = function (arr) {
      var r = [];
      var push = function (x) {
        r.push(x);
      };
      for (var i = 0; i < arr.length; i++) {
        arr[i].each(push);
      }
      return r;
    };

    var getHelpTabs = function (editor) {
      return Optional.from(editor.getParam('help_tabs'));
    };
    var getForcedPlugins = function (editor) {
      return editor.getParam('forced_plugins');
    };

    var description = '<h1>Editor UI keyboard navigation</h1>\n\n<h2>Activating keyboard navigation</h2>\n\n<p>The sections of the outer UI of the editor - the menubar, toolbar, sidebar and footer - are all keyboard navigable. As such, there are multiple ways to activate keyboard navigation:</p>\n<ul>\n  <li>Focus the menubar: Alt + F9 (Windows) or &#x2325;F9 (MacOS)</li>\n  <li>Focus the toolbar: Alt + F10 (Windows) or &#x2325;F10 (MacOS)</li>\n  <li>Focus the footer: Alt + F11 (Windows) or &#x2325;F11 (MacOS)</li>\n</ul>\n\n<p>Focusing the menubar or toolbar will start keyboard navigation at the first item in the menubar or toolbar, which will be highlighted with a gray background. Focusing the footer will start keyboard navigation at the first item in the element path, which will be highlighted with an underline. </p>\n\n<h2>Moving between UI sections</h2>\n\n<p>When keyboard navigation is active, pressing tab will move the focus to the next major section of the UI, where applicable. These sections are:</p>\n<ul>\n  <li>the menubar</li>\n  <li>each group of the toolbar </li>\n  <li>the sidebar</li>\n  <li>the element path in the footer </li>\n  <li>the wordcount toggle button in the footer </li>\n  <li>the branding link in the footer </li>\n  <li>the editor resize handle in the footer</li>\n</ul>\n\n<p>Pressing shift + tab will move backwards through the same sections, except when moving from the footer to the toolbar. Focusing the element path then pressing shift + tab will move focus to the first toolbar group, not the last.</p>\n\n<h2>Moving within UI sections</h2>\n\n<p>Keyboard navigation within UI sections can usually be achieved using the left and right arrow keys. This includes:</p>\n<ul>\n  <li>moving between menus in the menubar</li>\n  <li>moving between buttons in a toolbar group</li>\n  <li>moving between items in the element path</li>\n</ul>\n\n<p>In all these UI sections, keyboard navigation will cycle within the section. For example, focusing the last button in a toolbar group then pressing right arrow will move focus to the first item in the same toolbar group. </p>\n\n<h1>Executing buttons</h1>\n\n<p>To execute a button, navigate the selection to the desired button and hit space or enter.</p>\n\n<h1>Opening, navigating and closing menus</h1>\n\n<p>When focusing a menubar button or a toolbar button with a menu, pressing space, enter or down arrow will open the menu. When the menu opens the first item will be selected. To move up or down the menu, press the up or down arrow key respectively. This is the same for submenus, which can also be opened and closed using the left and right arrow keys.</p>\n\n<p>To close any active menu, hit the escape key. When a menu is closed the selection will be restored to its previous selection. This also works for closing submenus.</p>\n\n<h1>Context toolbars and menus</h1>\n\n<p>To focus an open context toolbar such as the table context toolbar, press Ctrl + F9 (Windows) or &#x2303;F9 (MacOS).</p>\n\n<p>Context toolbar navigation is the same as toolbar navigation, and context menu navigation is the same as standard menu navigation.</p>\n\n<h1>Dialog navigation</h1>\n\n<p>There are two types of dialog UIs in TinyMCE: tabbed dialogs and non-tabbed dialogs.</p>\n\n<p>When a non-tabbed dialog is opened, the first interactive component in the dialog will be focused. Users can navigate between interactive components by pressing tab. This includes any footer buttons. Navigation will cycle back to the first dialog component if tab is pressed while focusing the last component in the dialog. Pressing shift + tab will navigate backwards.</p>\n\n<p>When a tabbed dialog is opened, the first button in the tab menu is focused. Pressing tab will navigate to the first interactive component in that tab, and will cycle through the tab\u2019s components, the footer buttons, then back to the tab button. To switch to another tab, focus the tab button for the current tab, then use the arrow keys to cycle through the tab buttons.</p>';
    var tab = function () {
      var body = {
        type: 'htmlpanel',
        presets: 'document',
        html: description
      };
      return {
        name: 'keyboardnav',
        title: 'Keyboard Navigation',
        items: [body]
      };
    };

    var global$1 = tinymce.util.Tools.resolve('tinymce.Env');

    var convertText = function (source) {
      var mac = {
        alt: '&#x2325;',
        ctrl: '&#x2303;',
        shift: '&#x21E7;',
        meta: '&#x2318;',
        access: '&#x2303;&#x2325;'
      };
      var other = {
        meta: 'Ctrl ',
        access: 'Shift + Alt '
      };
      var replace = global$1.mac ? mac : other;
      var shortcut = source.split('+');
      var updated = map(shortcut, function (segment) {
        var search = segment.toLowerCase().trim();
        return has(replace, search) ? replace[search] : segment;
      });
      return global$1.mac ? updated.join('').replace(/\s/, '') : updated.join('+');
    };

    var shortcuts = [
      {
        shortcuts: ['Meta + B'],
        action: 'Bold'
      },
      {
        shortcuts: ['Meta + I'],
        action: 'Italic'
      },
      {
        shortcuts: ['Meta + U'],
        action: 'Underline'
      },
      {
        shortcuts: ['Meta + A'],
        action: 'Select all'
      },
      {
        shortcuts: [
          'Meta + Y',
          'Meta + Shift + Z'
        ],
        action: 'Redo'
      },
      {
        shortcuts: ['Meta + Z'],
        action: 'Undo'
      },
      {
        shortcuts: ['Access + 1'],
        action: 'Heading 1'
      },
      {
        shortcuts: ['Access + 2'],
        action: 'Heading 2'
      },
      {
        shortcuts: ['Access + 3'],
        action: 'Heading 3'
      },
      {
        shortcuts: ['Access + 4'],
        action: 'Heading 4'
      },
      {
        shortcuts: ['Access + 5'],
        action: 'Heading 5'
      },
      {
        shortcuts: ['Access + 6'],
        action: 'Heading 6'
      },
      {
        shortcuts: ['Access + 7'],
        action: 'Paragraph'
      },
      {
        shortcuts: ['Access + 8'],
        action: 'Div'
      },
      {
        shortcuts: ['Access + 9'],
        action: 'Address'
      },
      {
        shortcuts: ['Alt + 0'],
        action: 'Open help dialog'
      },
      {
        shortcuts: ['Alt + F9'],
        action: 'Focus to menubar'
      },
      {
        shortcuts: ['Alt + F10'],
        action: 'Focus to toolbar'
      },
      {
        shortcuts: ['Alt + F11'],
        action: 'Focus to element path'
      },
      {
        shortcuts: ['Ctrl + F9'],
        action: 'Focus to contextual toolbar'
      },
      {
        shortcuts: ['Shift + Enter'],
        action: 'Open popup menu for split buttons'
      },
      {
        shortcuts: ['Meta + K'],
        action: 'Insert link (if link plugin activated)'
      },
      {
        shortcuts: ['Meta + S'],
        action: 'Save (if save plugin activated)'
      },
      {
        shortcuts: ['Meta + F'],
        action: 'Find (if searchreplace plugin activated)'
      },
      {
        shortcuts: ['Meta + Shift + F'],
        action: 'Switch to or from fullscreen mode'
      }
    ];

    var tab$1 = function () {
      var shortcutList = map(shortcuts, function (shortcut) {
        var shortcutText = map(shortcut.shortcuts, convertText).join(' or ');
        return [
          shortcut.action,
          shortcutText
        ];
      });
      var tablePanel = {
        type: 'table',
        header: [
          'Action',
          'Shortcut'
        ],
        cells: shortcutList
      };
      return {
        name: 'shortcuts',
        title: 'Handy Shortcuts',
        items: [tablePanel]
      };
    };

    var global$2 = tinymce.util.Tools.resolve('tinymce.util.I18n');

    var premiumType = 'premium';
    var openSourceType = 'opensource';
    var urls = map([
      {
        key: 'advlist',
        name: 'Advanced List'
      },
      {
        key: 'anchor',
        name: 'Anchor'
      },
      {
        key: 'autolink',
        name: 'Autolink'
      },
      {
        key: 'autoresize',
        name: 'Autoresize'
      },
      {
        key: 'autosave',
        name: 'Autosave'
      },
      {
        key: 'bbcode',
        name: 'BBCode'
      },
      {
        key: 'charmap',
        name: 'Character Map'
      },
      {
        key: 'code',
        name: 'Code'
      },
      {
        key: 'codesample',
        name: 'Code Sample'
      },
      {
        key: 'colorpicker',
        name: 'Color Picker'
      },
      {
        key: 'directionality',
        name: 'Directionality'
      },
      {
        key: 'emoticons',
        name: 'Emoticons'
      },
      {
        key: 'fullpage',
        name: 'Full Page'
      },
      {
        key: 'fullscreen',
        name: 'Full Screen'
      },
      {
        key: 'help',
        name: 'Help'
      },
      {
        key: 'hr',
        name: 'Horizontal Rule'
      },
      {
        key: 'image',
        name: 'Image'
      },
      {
        key: 'imagetools',
        name: 'Image Tools'
      },
      {
        key: 'importcss',
        name: 'Import CSS'
      },
      {
        key: 'insertdatetime',
        name: 'Insert Date/Time'
      },
      {
        key: 'legacyoutput',
        name: 'Legacy Output'
      },
      {
        key: 'link',
        name: 'Link'
      },
      {
        key: 'lists',
        name: 'Lists'
      },
      {
        key: 'media',
        name: 'Media'
      },
      {
        key: 'nonbreaking',
        name: 'Nonbreaking'
      },
      {
        key: 'noneditable',
        name: 'Noneditable'
      },
      {
        key: 'pagebreak',
        name: 'Page Break'
      },
      {
        key: 'paste',
        name: 'Paste'
      },
      {
        key: 'preview',
        name: 'Preview'
      },
      {
        key: 'print',
        name: 'Print'
      },
      {
        key: 'save',
        name: 'Save'
      },
      {
        key: 'searchreplace',
        name: 'Search and Replace'
      },
      {
        key: 'spellchecker',
        name: 'Spell Checker'
      },
      {
        key: 'tabfocus',
        name: 'Tab Focus'
      },
      {
        key: 'table',
        name: 'Table'
      },
      {
        key: 'template',
        name: 'Template'
      },
      {
        key: 'textcolor',
        name: 'Text Color'
      },
      {
        key: 'textpattern',
        name: 'Text Pattern'
      },
      {
        key: 'toc',
        name: 'Table of Contents'
      },
      {
        key: 'visualblocks',
        name: 'Visual Blocks'
      },
      {
        key: 'visualchars',
        name: 'Visual Characters'
      },
      {
        key: 'wordcount',
        name: 'Word Count'
      },
      {
        key: 'advcode',
        name: 'Advanced Code Editor*',
        type: premiumType
      },
      {
        key: 'formatpainter',
        name: 'Format Painter*',
        type: premiumType
      },
      {
        key: 'powerpaste',
        name: 'PowerPaste*',
        type: premiumType
      },
      {
        key: 'tinydrive',
        name: 'Tiny Drive*',
        type: premiumType
      },
      {
        key: 'tinymcespellchecker',
        name: 'Spell Checker Pro*',
        type: premiumType
      },
      {
        key: 'a11ychecker',
        name: 'Accessibility Checker*',
        type: premiumType
      },
      {
        key: 'linkchecker',
        name: 'Link Checker*',
        type: premiumType
      },
      {
        key: 'mentions',
        name: 'Mentions*',
        type: premiumType
      },
      {
        key: 'mediaembed',
        name: 'Enhanced Media Embed*',
        type: premiumType
      },
      {
        key: 'checklist',
        name: 'Checklist*',
        type: premiumType
      },
      {
        key: 'casechange',
        name: 'Case Change*',
        type: premiumType
      },
      {
        key: 'permanentpen',
        name: 'Permanent Pen*',
        type: premiumType
      },
      {
        key: 'pageembed',
        name: 'Page Embed*',
        type: premiumType
      },
      {
        key: 'tinycomments',
        name: 'Tiny Comments*',
        type: premiumType,
        slug: 'comments'
      },
      {
        key: 'advtable',
        name: 'Advanced Tables*',
        type: premiumType
      },
      {
        key: 'autocorrect',
        name: 'Autocorrect*',
        type: premiumType
      },
      {
        key: 'export',
        name: 'Export*',
        type: premiumType
      }
    ], function (item) {
      return __assign(__assign({}, item), {
        type: item.type || openSourceType,
        slug: item.slug || item.key
      });
    });

    var tab$2 = function (editor) {
      var availablePlugins = function () {
        var premiumPlugins = [
          'Accessibility Checker',
          'Advanced Code Editor',
          'Advanced Tables',
          'Case Change',
          'Checklist',
          'Export',
          'Tiny Comments',
          'Tiny Drive',
          'Enhanced Media Embed',
          'Format Painter',
          'Link Checker',
          'Mentions',
          'MoxieManager',
          'Page Embed',
          'Permanent Pen',
          'PowerPaste',
          'Spell Checker Pro'
        ];
        var premiumPluginList = map(premiumPlugins, function (plugin) {
          return '<li>' + global$2.translate(plugin) + '</li>';
        }).join('');
        return '<div data-mce-tabstop="1" tabindex="-1">' + '<p><b>' + global$2.translate('Premium plugins:') + '</b></p>' + '<ul>' + premiumPluginList + '<li class="tox-help__more-link" "><a href="https://www.tiny.cloud/pricing/?utm_campaign=editor_referral&utm_medium=help_dialog&utm_source=tinymce" target="_blank">' + global$2.translate('Learn more...') + '</a></li>' + '</ul>' + '</div>';
      };
      var makeLink = function (p) {
        return '<a href="' + p.url + '" target="_blank" rel="noopener">' + p.name + '</a>';
      };
      var maybeUrlize = function (editor, key) {
        return find(urls, function (x) {
          return x.key === key;
        }).fold(function () {
          var getMetadata = editor.plugins[key].getMetadata;
          return typeof getMetadata === 'function' ? makeLink(getMetadata()) : key;
        }, function (x) {
          return makeLink({
            name: x.name,
            url: 'https://www.tiny.cloud/docs/plugins/' + x.type + '/' + x.slug
          });
        });
      };
      var getPluginKeys = function (editor) {
        var keys$1 = keys(editor.plugins);
        var forced_plugins = getForcedPlugins(editor);
        return forced_plugins === undefined ? keys$1 : filter(keys$1, function (k) {
          return !contains(forced_plugins, k);
        });
      };
      var pluginLister = function (editor) {
        var pluginKeys = getPluginKeys(editor);
        var pluginLis = map(pluginKeys, function (key) {
          return '<li>' + maybeUrlize(editor, key) + '</li>';
        });
        var count = pluginLis.length;
        var pluginsString = pluginLis.join('');
        var html = '<p><b>' + global$2.translate([
          'Plugins installed ({0}):',
          count
        ]) + '</b></p>' + '<ul>' + pluginsString + '</ul>';
        return html;
      };
      var installedPlugins = function (editor) {
        if (editor == null) {
          return '';
        }
        return '<div data-mce-tabstop="1" tabindex="-1">' + pluginLister(editor) + '</div>';
      };
      var htmlPanel = {
        type: 'htmlpanel',
        presets: 'document',
        html: [
          installedPlugins(editor),
          availablePlugins()
        ].join('')
      };
      return {
        name: 'plugins',
        title: 'Plugins',
        items: [htmlPanel]
      };
    };

    var global$3 = tinymce.util.Tools.resolve('tinymce.EditorManager');

    var tab$3 = function () {
      var getVersion = function (major, minor) {
        return major.indexOf('@') === 0 ? 'X.X.X' : major + '.' + minor;
      };
      var version = getVersion(global$3.majorVersion, global$3.minorVersion);
      var changeLogLink = '<a href="https://www.tiny.cloud/docs/changelog/?utm_campaign=editor_referral&utm_medium=help_dialog&utm_source=tinymce" target="_blank">TinyMCE ' + version + '</a>';
      var htmlPanel = {
        type: 'htmlpanel',
        html: '<p>' + global$2.translate([
          'You are using {0}',
          changeLogLink
        ]) + '</p>',
        presets: 'document'
      };
      return {
        name: 'versions',
        title: 'Version',
        items: [htmlPanel]
      };
    };

    var parseHelpTabsSetting = function (tabsFromSettings, tabs) {
      var newTabs = {};
      var names = map(tabsFromSettings, function (t) {
        if (typeof t === 'string') {
          if (has(tabs, t)) {
            newTabs[t] = tabs[t];
          }
          return t;
        } else {
          newTabs[t.name] = t;
          return t.name;
        }
      });
      return {
        tabs: newTabs,
        names: names
      };
    };
    var getNamesFromTabs = function (tabs) {
      var names = keys(tabs);
      var idx = names.indexOf('versions');
      if (idx !== -1) {
        names.splice(idx, 1);
        names.push('versions');
      }
      return {
        tabs: tabs,
        names: names
      };
    };
    var parseCustomTabs = function (editor, customTabs) {
      var _a;
      var shortcuts = tab$1();
      var nav = tab();
      var plugins = tab$2(editor);
      var versions = tab$3();
      var tabs = __assign((_a = {}, _a[shortcuts.name] = shortcuts, _a[nav.name] = nav, _a[plugins.name] = plugins, _a[versions.name] = versions, _a), customTabs.get());
      return getHelpTabs(editor).fold(function () {
        return getNamesFromTabs(tabs);
      }, function (tabsFromSettings) {
        return parseHelpTabsSetting(tabsFromSettings, tabs);
      });
    };
    var init = function (editor, customTabs) {
      return function () {
        var _a = parseCustomTabs(editor, customTabs), tabs = _a.tabs, names = _a.names;
        var foundTabs = map(names, function (name) {
          return get$1(tabs, name);
        });
        var dialogTabs = cat(foundTabs);
        var body = {
          type: 'tabpanel',
          tabs: dialogTabs
        };
        editor.windowManager.open({
          title: 'Help',
          size: 'medium',
          body: body,
          buttons: [{
              type: 'cancel',
              name: 'close',
              text: 'Close',
              primary: true
            }],
          initialData: {}
        });
      };
    };

    function Plugin () {
      global.add('help', function (editor) {
        var customTabs = Cell({});
        var api = get(customTabs);
        var dialogOpener = init(editor, customTabs);
        register$1(editor, dialogOpener);
        register(editor, dialogOpener);
        editor.shortcuts.add('Alt+0', 'Open help dialog', 'mceHelp');
        return api;
      });
    }

    Plugin();

}());
