/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
    'use strict';

    var global$1 = tinymce.util.Tools.resolve('tinymce.PluginManager');

    const eq = t => a => t === a;
    const isNull = eq(null);
    const isUndefined = eq(undefined);
    const isNullable = a => a === null || a === undefined;
    const isNonNullable = a => !isNullable(a);

    const noop = () => {
    };
    const constant = value => {
      return () => {
        return value;
      };
    };
    const never = constant(false);

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
    const map$1 = (xs, f) => {
      const len = xs.length;
      const r = new Array(len);
      for (let i = 0; i < len; i++) {
        const x = xs[i];
        r[i] = f(x, i);
      }
      return r;
    };
    const each$1 = (xs, f) => {
      for (let i = 0, len = xs.length; i < len; i++) {
        const x = xs[i];
        f(x, i);
      }
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

    const last = (fn, rate) => {
      let timer = null;
      const cancel = () => {
        if (!isNull(timer)) {
          clearTimeout(timer);
          timer = null;
        }
      };
      const throttle = (...args) => {
        cancel();
        timer = setTimeout(() => {
          timer = null;
          fn.apply(null, args);
        }, rate);
      };
      return {
        cancel,
        throttle
      };
    };

    const insertEmoticon = (editor, ch) => {
      editor.insertContent(ch);
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
    const map = (obj, f) => {
      return tupleMap(obj, (x, i) => ({
        k: i,
        v: f(x, i)
      }));
    };
    const tupleMap = (obj, f) => {
      const r = {};
      each(obj, (x, i) => {
        const tuple = f(x, i);
        r[tuple.k] = tuple.v;
      });
      return r;
    };
    const has = (obj, key) => hasOwnProperty.call(obj, key);

    const shallow = (old, nu) => {
      return nu;
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
    const merge = baseMerge(shallow);

    const singleton = doRevoke => {
      const subject = Cell(Optional.none());
      const revoke = () => subject.get().each(doRevoke);
      const clear = () => {
        revoke();
        subject.set(Optional.none());
      };
      const isSet = () => subject.get().isSome();
      const get = () => subject.get();
      const set = s => {
        revoke();
        subject.set(Optional.some(s));
      };
      return {
        clear,
        isSet,
        get,
        set
      };
    };
    const value = () => {
      const subject = singleton(noop);
      const on = f => subject.get().each(f);
      return {
        ...subject,
        on
      };
    };

    const checkRange = (str, substr, start) => substr === '' || str.length >= substr.length && str.substr(start, start + substr.length) === substr;
    const contains = (str, substr, start = 0, end) => {
      const idx = str.indexOf(substr, start);
      if (idx !== -1) {
        return isUndefined(end) ? true : idx + substr.length <= end;
      } else {
        return false;
      }
    };
    const startsWith = (str, prefix) => {
      return checkRange(str, prefix, 0);
    };

    var global = tinymce.util.Tools.resolve('tinymce.Resource');

    const DEFAULT_ID = 'tinymce.plugins.emoticons';
    const option = name => editor => editor.options.get(name);
    const register$2 = (editor, pluginUrl) => {
      const registerOption = editor.options.register;
      registerOption('emoticons_database', {
        processor: 'string',
        default: 'emojis'
      });
      registerOption('emoticons_database_url', {
        processor: 'string',
        default: `${ pluginUrl }/js/${ getEmojiDatabase(editor) }${ editor.suffix }.js`
      });
      registerOption('emoticons_database_id', {
        processor: 'string',
        default: DEFAULT_ID
      });
      registerOption('emoticons_append', {
        processor: 'object',
        default: {}
      });
      registerOption('emoticons_images_url', {
        processor: 'string',
        default: 'https://twemoji.maxcdn.com/v/13.0.1/72x72/'
      });
    };
    const getEmojiDatabase = option('emoticons_database');
    const getEmojiDatabaseUrl = option('emoticons_database_url');
    const getEmojiDatabaseId = option('emoticons_database_id');
    const getAppendedEmoji = option('emoticons_append');
    const getEmojiImageUrl = option('emoticons_images_url');

    const ALL_CATEGORY = 'All';
    const categoryNameMap = {
      symbols: 'Symbols',
      people: 'People',
      animals_and_nature: 'Animals and Nature',
      food_and_drink: 'Food and Drink',
      activity: 'Activity',
      travel_and_places: 'Travel and Places',
      objects: 'Objects',
      flags: 'Flags',
      user: 'User Defined'
    };
    const translateCategory = (categories, name) => has(categories, name) ? categories[name] : name;
    const getUserDefinedEmoji = editor => {
      const userDefinedEmoticons = getAppendedEmoji(editor);
      return map(userDefinedEmoticons, value => ({
        keywords: [],
        category: 'user',
        ...value
      }));
    };
    const initDatabase = (editor, databaseUrl, databaseId) => {
      const categories = value();
      const all = value();
      const emojiImagesUrl = getEmojiImageUrl(editor);
      const getEmoji = lib => {
        if (startsWith(lib.char, '<img')) {
          return lib.char.replace(/src="([^"]+)"/, (match, url) => `src="${ emojiImagesUrl }${ url }"`);
        } else {
          return lib.char;
        }
      };
      const processEmojis = emojis => {
        const cats = {};
        const everything = [];
        each(emojis, (lib, title) => {
          const entry = {
            title,
            keywords: lib.keywords,
            char: getEmoji(lib),
            category: translateCategory(categoryNameMap, lib.category)
          };
          const current = cats[entry.category] !== undefined ? cats[entry.category] : [];
          cats[entry.category] = current.concat([entry]);
          everything.push(entry);
        });
        categories.set(cats);
        all.set(everything);
      };
      editor.on('init', () => {
        global.load(databaseId, databaseUrl).then(emojis => {
          const userEmojis = getUserDefinedEmoji(editor);
          processEmojis(merge(emojis, userEmojis));
        }, err => {
          console.log(`Failed to load emojis: ${ err }`);
          categories.set({});
          all.set([]);
        });
      });
      const listCategory = category => {
        if (category === ALL_CATEGORY) {
          return listAll();
        }
        return categories.get().bind(cats => Optional.from(cats[category])).getOr([]);
      };
      const listAll = () => all.get().getOr([]);
      const listCategories = () => [ALL_CATEGORY].concat(keys(categories.get().getOr({})));
      const waitForLoad = () => {
        if (hasLoaded()) {
          return Promise.resolve(true);
        } else {
          return new Promise((resolve, reject) => {
            let numRetries = 15;
            const interval = setInterval(() => {
              if (hasLoaded()) {
                clearInterval(interval);
                resolve(true);
              } else {
                numRetries--;
                if (numRetries < 0) {
                  console.log('Could not load emojis from url: ' + databaseUrl);
                  clearInterval(interval);
                  reject(false);
                }
              }
            }, 100);
          });
        }
      };
      const hasLoaded = () => categories.isSet() && all.isSet();
      return {
        listCategories,
        hasLoaded,
        waitForLoad,
        listAll,
        listCategory
      };
    };

    const emojiMatches = (emoji, lowerCasePattern) => contains(emoji.title.toLowerCase(), lowerCasePattern) || exists(emoji.keywords, k => contains(k.toLowerCase(), lowerCasePattern));
    const emojisFrom = (list, pattern, maxResults) => {
      const matches = [];
      const lowerCasePattern = pattern.toLowerCase();
      const reachedLimit = maxResults.fold(() => never, max => size => size >= max);
      for (let i = 0; i < list.length; i++) {
        if (pattern.length === 0 || emojiMatches(list[i], lowerCasePattern)) {
          matches.push({
            value: list[i].char,
            text: list[i].title,
            icon: list[i].char
          });
          if (reachedLimit(matches.length)) {
            break;
          }
        }
      }
      return matches;
    };

    const patternName = 'pattern';
    const open = (editor, database) => {
      const initialState = {
        pattern: '',
        results: emojisFrom(database.listAll(), '', Optional.some(300))
      };
      const currentTab = Cell(ALL_CATEGORY);
      const scan = dialogApi => {
        const dialogData = dialogApi.getData();
        const category = currentTab.get();
        const candidates = database.listCategory(category);
        const results = emojisFrom(candidates, dialogData[patternName], category === ALL_CATEGORY ? Optional.some(300) : Optional.none());
        dialogApi.setData({ results });
      };
      const updateFilter = last(dialogApi => {
        scan(dialogApi);
      }, 200);
      const searchField = {
        label: 'Search',
        type: 'input',
        name: patternName
      };
      const resultsField = {
        type: 'collection',
        name: 'results'
      };
      const getInitialState = () => {
        const body = {
          type: 'tabpanel',
          tabs: map$1(database.listCategories(), cat => ({
            title: cat,
            name: cat,
            items: [
              searchField,
              resultsField
            ]
          }))
        };
        return {
          title: 'Emojis',
          size: 'normal',
          body,
          initialData: initialState,
          onTabChange: (dialogApi, details) => {
            currentTab.set(details.newTabName);
            updateFilter.throttle(dialogApi);
          },
          onChange: updateFilter.throttle,
          onAction: (dialogApi, actionData) => {
            if (actionData.name === 'results') {
              insertEmoticon(editor, actionData.value);
              dialogApi.close();
            }
          },
          buttons: [{
              type: 'cancel',
              text: 'Close',
              primary: true
            }]
        };
      };
      const dialogApi = editor.windowManager.open(getInitialState());
      dialogApi.focus(patternName);
      if (!database.hasLoaded()) {
        dialogApi.block('Loading emojis...');
        database.waitForLoad().then(() => {
          dialogApi.redial(getInitialState());
          updateFilter.throttle(dialogApi);
          dialogApi.focus(patternName);
          dialogApi.unblock();
        }).catch(_err => {
          dialogApi.redial({
            title: 'Emojis',
            body: {
              type: 'panel',
              items: [{
                  type: 'alertbanner',
                  level: 'error',
                  icon: 'warning',
                  text: 'Could not load emojis'
                }]
            },
            buttons: [{
                type: 'cancel',
                text: 'Close',
                primary: true
              }],
            initialData: {
              pattern: '',
              results: []
            }
          });
          dialogApi.focus(patternName);
          dialogApi.unblock();
        });
      }
    };

    const register$1 = (editor, database) => {
      editor.addCommand('mceEmoticons', () => open(editor, database));
    };

    const setup = editor => {
      editor.on('PreInit', () => {
        editor.parser.addAttributeFilter('data-emoticon', nodes => {
          each$1(nodes, node => {
            node.attr('data-mce-resize', 'false');
            node.attr('data-mce-placeholder', '1');
          });
        });
      });
    };

    const init = (editor, database) => {
      editor.ui.registry.addAutocompleter('emoticons', {
        trigger: ':',
        columns: 'auto',
        minChars: 2,
        fetch: (pattern, maxResults) => database.waitForLoad().then(() => {
          const candidates = database.listAll();
          return emojisFrom(candidates, pattern, Optional.some(maxResults));
        }),
        onAction: (autocompleteApi, rng, value) => {
          editor.selection.setRng(rng);
          editor.insertContent(value);
          autocompleteApi.hide();
        }
      });
    };

    const register = editor => {
      const onAction = () => editor.execCommand('mceEmoticons');
      editor.ui.registry.addButton('emoticons', {
        tooltip: 'Emojis',
        icon: 'emoji',
        onAction
      });
      editor.ui.registry.addMenuItem('emoticons', {
        text: 'Emojis...',
        icon: 'emoji',
        onAction
      });
    };

    var Plugin = () => {
      global$1.add('emoticons', (editor, pluginUrl) => {
        register$2(editor, pluginUrl);
        const databaseUrl = getEmojiDatabaseUrl(editor);
        const databaseId = getEmojiDatabaseId(editor);
        const database = initDatabase(editor, databaseUrl, databaseId);
        register$1(editor, database);
        register(editor);
        init(editor, database);
        setup(editor);
      });
    };

    Plugin();

})();
