/**
 * TinyMCE version 6.3.2 (2023-02-22)
 */

(function () {
  'use strict';

  var global$1 = tinymce.util.Tools.resolve('tinymce.PluginManager');

  const link = () => /(?:[A-Za-z][A-Za-z\d.+-]{0,14}:\/\/(?:[-.~*+=!&;:'%@?^${}(),\w]+@)?|www\.|[-;:&=+$,.\w]+@)[A-Za-z\d-]+(?:\.[A-Za-z\d-]+)*(?::\d+)?(?:\/(?:[-.~*+=!;:'%@$(),\/\w]*[-~*+=%@$()\/\w])?)?(?:\?(?:[-.~*+=!&;:'%@?^${}(),\/\w]+))?(?:#(?:[-.~*+=!&;:'%@?^${}(),\/\w]+))?/g;

  const option = name => editor => editor.options.get(name);
  const register = editor => {
    const registerOption = editor.options.register;
    registerOption('autolink_pattern', {
      processor: 'regexp',
      default: new RegExp('^' + link().source + '$', 'i')
    });
    registerOption('link_default_target', { processor: 'string' });
    registerOption('link_default_protocol', {
      processor: 'string',
      default: 'https'
    });
  };
  const getAutoLinkPattern = option('autolink_pattern');
  const getDefaultLinkTarget = option('link_default_target');
  const getDefaultLinkProtocol = option('link_default_protocol');
  const allowUnsafeLinkTarget = option('allow_unsafe_link_target');

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
  const eq = t => a => t === a;
  const isString = isType('string');
  const isUndefined = eq(undefined);
  const isNullable = a => a === null || a === undefined;
  const isNonNullable = a => !isNullable(a);

  const not = f => t => !f(t);

  const hasOwnProperty = Object.hasOwnProperty;
  const has = (obj, key) => hasOwnProperty.call(obj, key);

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

  const zeroWidth = '\uFEFF';
  const isZwsp = char => char === zeroWidth;
  const removeZwsp = s => s.replace(/\uFEFF/g, '');

  var global = tinymce.util.Tools.resolve('tinymce.dom.TextSeeker');

  const isTextNode = node => node.nodeType === 3;
  const isElement = node => node.nodeType === 1;
  const isBracketOrSpace = char => /^[(\[{ \u00a0]$/.test(char);
  const hasProtocol = url => /^([A-Za-z][A-Za-z\d.+-]*:\/\/)|mailto:/.test(url);
  const isPunctuation = char => /[?!,.;:]/.test(char);
  const findChar = (text, index, predicate) => {
    for (let i = index - 1; i >= 0; i--) {
      const char = text.charAt(i);
      if (!isZwsp(char) && predicate(char)) {
        return i;
      }
    }
    return -1;
  };
  const freefallRtl = (container, offset) => {
    let tempNode = container;
    let tempOffset = offset;
    while (isElement(tempNode) && tempNode.childNodes[tempOffset]) {
      tempNode = tempNode.childNodes[tempOffset];
      tempOffset = isTextNode(tempNode) ? tempNode.data.length : tempNode.childNodes.length;
    }
    return {
      container: tempNode,
      offset: tempOffset
    };
  };

  const parseCurrentLine = (editor, offset) => {
    var _a;
    const voidElements = editor.schema.getVoidElements();
    const autoLinkPattern = getAutoLinkPattern(editor);
    const {dom, selection} = editor;
    if (dom.getParent(selection.getNode(), 'a[href]') !== null) {
      return null;
    }
    const rng = selection.getRng();
    const textSeeker = global(dom, node => {
      return dom.isBlock(node) || has(voidElements, node.nodeName.toLowerCase()) || dom.getContentEditable(node) === 'false';
    });
    const {
      container: endContainer,
      offset: endOffset
    } = freefallRtl(rng.endContainer, rng.endOffset);
    const root = (_a = dom.getParent(endContainer, dom.isBlock)) !== null && _a !== void 0 ? _a : dom.getRoot();
    const endSpot = textSeeker.backwards(endContainer, endOffset + offset, (node, offset) => {
      const text = node.data;
      const idx = findChar(text, offset, not(isBracketOrSpace));
      return idx === -1 || isPunctuation(text[idx]) ? idx : idx + 1;
    }, root);
    if (!endSpot) {
      return null;
    }
    let lastTextNode = endSpot.container;
    const startSpot = textSeeker.backwards(endSpot.container, endSpot.offset, (node, offset) => {
      lastTextNode = node;
      const idx = findChar(node.data, offset, isBracketOrSpace);
      return idx === -1 ? idx : idx + 1;
    }, root);
    const newRng = dom.createRng();
    if (!startSpot) {
      newRng.setStart(lastTextNode, 0);
    } else {
      newRng.setStart(startSpot.container, startSpot.offset);
    }
    newRng.setEnd(endSpot.container, endSpot.offset);
    const rngText = removeZwsp(newRng.toString());
    const matches = rngText.match(autoLinkPattern);
    if (matches) {
      let url = matches[0];
      if (startsWith(url, 'www.')) {
        const protocol = getDefaultLinkProtocol(editor);
        url = protocol + '://' + url;
      } else if (contains(url, '@') && !hasProtocol(url)) {
        url = 'mailto:' + url;
      }
      return {
        rng: newRng,
        url
      };
    } else {
      return null;
    }
  };
  const convertToLink = (editor, result) => {
    const {dom, selection} = editor;
    const {rng, url} = result;
    const bookmark = selection.getBookmark();
    selection.setRng(rng);
    const command = 'createlink';
    const args = {
      command,
      ui: false,
      value: url
    };
    const beforeExecEvent = editor.dispatch('BeforeExecCommand', args);
    if (!beforeExecEvent.isDefaultPrevented()) {
      editor.getDoc().execCommand(command, false, url);
      editor.dispatch('ExecCommand', args);
      const defaultLinkTarget = getDefaultLinkTarget(editor);
      if (isString(defaultLinkTarget)) {
        const anchor = selection.getNode();
        dom.setAttrib(anchor, 'target', defaultLinkTarget);
        if (defaultLinkTarget === '_blank' && !allowUnsafeLinkTarget(editor)) {
          dom.setAttrib(anchor, 'rel', 'noopener');
        }
      }
    }
    selection.moveToBookmark(bookmark);
    editor.nodeChanged();
  };
  const handleSpacebar = editor => {
    const result = parseCurrentLine(editor, -1);
    if (isNonNullable(result)) {
      convertToLink(editor, result);
    }
  };
  const handleBracket = handleSpacebar;
  const handleEnter = editor => {
    const result = parseCurrentLine(editor, 0);
    if (isNonNullable(result)) {
      convertToLink(editor, result);
    }
  };
  const setup = editor => {
    editor.on('keydown', e => {
      if (e.keyCode === 13 && !e.isDefaultPrevented()) {
        handleEnter(editor);
      }
    });
    editor.on('keyup', e => {
      if (e.keyCode === 32) {
        handleSpacebar(editor);
      } else if (e.keyCode === 48 && e.shiftKey || e.keyCode === 221) {
        handleBracket(editor);
      }
    });
  };

  var Plugin = () => {
    global$1.add('autolink', editor => {
      register(editor);
      setup(editor);
    });
  };

  Plugin();

})();
