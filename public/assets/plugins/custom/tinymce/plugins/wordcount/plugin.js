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

    var identity = function (x) {
      return x;
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

    var zeroWidth = '\uFEFF';
    var removeZwsp = function (s) {
      return s.replace(/\uFEFF/g, '');
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

    var punctuationStr = '[!-#%-*,-\\/:;?@\\[-\\]_{}\xA1\xAB\xB7\xBB\xBF;\xB7\u055A-\u055F\u0589\u058A\u05BE\u05C0\u05C3\u05C6\u05F3\u05F4\u0609\u060A\u060C\u060D\u061B\u061E\u061F\u066A-\u066D\u06D4\u0700-\u070D\u07F7-\u07F9\u0830-\u083E\u085E\u0964\u0965\u0970\u0DF4\u0E4F\u0E5A\u0E5B\u0F04-\u0F12\u0F3A-\u0F3D\u0F85\u0FD0-\u0FD4\u0FD9\u0FDA\u104A-\u104F\u10FB\u1361-\u1368\u1400\u166D\u166E\u169B\u169C\u16EB-\u16ED\u1735\u1736\u17D4-\u17D6\u17D8-\u17DA\u1800-\u180A\u1944\u1945\u1A1E\u1A1F\u1AA0-\u1AA6\u1AA8-\u1AAD\u1B5A-\u1B60\u1BFC-\u1BFF\u1C3B-\u1C3F\u1C7E\u1C7F\u1CD3\u2010-\u2027\u2030-\u2043\u2045-\u2051\u2053-\u205E\u207D\u207E\u208D\u208E\u3008\u3009\u2768-\u2775\u27C5\u27C6\u27E6-\u27EF\u2983-\u2998\u29D8-\u29DB\u29FC\u29FD\u2CF9-\u2CFC\u2CFE\u2CFF\u2D70\u2E00-\u2E2E\u2E30\u2E31\u3001-\u3003\u3008-\u3011\u3014-\u301F\u3030\u303D\u30A0\u30FB\uA4FE\uA4FF\uA60D-\uA60F\uA673\uA67E\uA6F2-\uA6F7\uA874-\uA877\uA8CE\uA8CF\uA8F8-\uA8FA\uA92E\uA92F\uA95F\uA9C1-\uA9CD\uA9DE\uA9DF\uAA5C-\uAA5F\uAADE\uAADF\uABEB\uFD3E\uFD3F\uFE10-\uFE19\uFE30-\uFE52\uFE54-\uFE61\uFE63\uFE68\uFE6A\uFE6B\uFF01-\uFF03\uFF05-\uFF0A\uFF0C-\uFF0F\uFF1A\uFF1B\uFF1F\uFF20\uFF3B-\uFF3D\uff3f\uFF5B\uFF5D\uFF5F-\uFF65]';
    var regExps = {
      aletter: '[A-Za-z\xaa\xb5\xba\xc0-\xd6\xd8-\xf6\xf8-\u02c1\u02c6-\u02d1\u02e0-\u02e4\u02ec\u02ee\u0370-\u0374\u0376\u0377\u037a-\u037d\u0386\u0388-\u038a\u038c\u038e-\u03a1\u03a3-\u03f5\u03f7-\u0481\u048a-\u0527\u0531-\u0556\u0559\u0561-\u0587\u05d0-\u05ea\u05f0-\u05F3\u0620-\u064a\u066e\u066f\u0671-\u06d3\u06d5\u06e5\u06e6\u06ee\u06ef\u06fa-\u06fc\u06ff\u0710\u0712-\u072f\u074d-\u07a5\u07b1\u07ca-\u07ea\u07f4\u07f5\u07fa\u0800-\u0815\u081a\u0824\u0828\u0840-\u0858\u0904-\u0939\u093d\u0950\u0958-\u0961\u0971-\u0977\u0979-\u097f\u0985-\u098c\u098f\u0990\u0993-\u09a8\u09aa-\u09b0\u09b2\u09b6-\u09b9\u09bd\u09ce\u09dc\u09dd\u09df-\u09e1\u09f0\u09f1\u0a05-\u0a0a\u0a0f\u0a10\u0a13-\u0a28\u0a2a-\u0a30\u0a32\u0a33\u0a35\u0a36\u0a38\u0a39\u0a59-\u0a5c\u0a5e\u0a72-\u0a74\u0a85-\u0a8d\u0a8f-\u0a91\u0a93-\u0aa8\u0aaa-\u0ab0\u0ab2\u0ab3\u0ab5-\u0ab9\u0abd\u0ad0\u0ae0\u0ae1\u0b05-\u0b0c\u0b0f\u0b10\u0b13-\u0b28\u0b2a-\u0b30\u0b32\u0b33\u0b35-\u0b39\u0b3d\u0b5c\u0b5d\u0b5f-\u0b61\u0b71\u0b83\u0b85-\u0b8a\u0b8e-\u0b90\u0b92-\u0b95\u0b99\u0b9a\u0b9c\u0b9e\u0b9f\u0ba3\u0ba4\u0ba8-\u0baa\u0bae-\u0bb9\u0bd0\u0c05-\u0c0c\u0c0e-\u0c10\u0c12-\u0c28\u0c2a-\u0c33\u0c35-\u0c39\u0c3d\u0c58\u0c59\u0c60\u0c61\u0c85-\u0c8c\u0c8e-\u0c90\u0c92-\u0ca8\u0caa-\u0cb3\u0cb5-\u0cb9\u0cbd\u0cde\u0ce0\u0ce1\u0cf1\u0cf2\u0d05-\u0d0c\u0d0e-\u0d10\u0d12-\u0d3a\u0d3d\u0d4e\u0d60\u0d61\u0d7a-\u0d7f\u0d85-\u0d96\u0d9a-\u0db1\u0db3-\u0dbb\u0dbd\u0dc0-\u0dc6\u0f00\u0f40-\u0f47\u0f49-\u0f6c\u0f88-\u0f8c\u10a0-\u10c5\u10d0-\u10fa\u10fc\u1100-\u1248\u124a-\u124d\u1250-\u1256\u1258\u125a-\u125d\u1260-\u1288\u128a-\u128d\u1290-\u12b0\u12b2-\u12b5\u12b8-\u12be\u12c0\u12c2-\u12c5\u12c8-\u12d6\u12d8-\u1310\u1312-\u1315\u1318-\u135a\u1380-\u138f\u13a0-\u13f4\u1401-\u166c\u166f-\u167f\u1681-\u169a\u16a0-\u16ea\u16ee-\u16f0\u1700-\u170c\u170e-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176c\u176e-\u1770\u1820-\u1877\u1880-\u18a8\u18aa\u18b0-\u18f5\u1900-\u191c\u1a00-\u1a16\u1b05-\u1b33\u1b45-\u1b4b\u1b83-\u1ba0\u1bae\u1baf\u1bc0-\u1be5\u1c00-\u1c23\u1c4d-\u1c4f\u1c5a-\u1c7d\u1ce9-\u1cec\u1cee-\u1cf1\u1d00-\u1dbf\u1e00-\u1f15\u1f18-\u1f1d\u1f20-\u1f45\u1f48-\u1f4d\u1f50-\u1f57\u1f59\u1f5b\u1f5d\u1f5f-\u1f7d\u1f80-\u1fb4\u1fb6-\u1fbc\u1fbe\u1fc2-\u1fc4\u1fc6-\u1fcc\u1fd0-\u1fd3\u1fd6-\u1fdb\u1fe0-\u1fec\u1ff2-\u1ff4\u1ff6-\u1ffc\u2071\u207f\u2090-\u209c\u2102\u2107\u210a-\u2113\u2115\u2119-\u211d\u2124\u2126\u2128\u212a-\u212d\u212f-\u2139\u213c-\u213f\u2145-\u2149\u214e\u2160-\u2188\u24B6-\u24E9\u2c00-\u2c2e\u2c30-\u2c5e\u2c60-\u2ce4\u2ceb-\u2cee\u2d00-\u2d25\u2d30-\u2d65\u2d6f\u2d80-\u2d96\u2da0-\u2da6\u2da8-\u2dae\u2db0-\u2db6\u2db8-\u2dbe\u2dc0-\u2dc6\u2dc8-\u2dce\u2dd0-\u2dd6\u2dd8-\u2dde\u2e2f\u3005\u303b\u303c\u3105-\u312d\u3131-\u318e\u31a0-\u31ba\ua000-\ua48c\ua4d0-\ua4fd\ua500-\ua60c\ua610-\ua61f\ua62a\ua62b\ua640-\ua66e\ua67f-\ua697\ua6a0-\ua6ef\ua717-\ua71f\ua722-\ua788\ua78b-\ua78e\ua790\ua791\ua7a0-\ua7a9\ua7fa-\ua801\ua803-\ua805\ua807-\ua80a\ua80c-\ua822\ua840-\ua873\ua882-\ua8b3\ua8f2-\ua8f7\ua8fb\ua90a-\ua925\ua930-\ua946\ua960-\ua97c\ua984-\ua9b2\ua9cf\uaa00-\uaa28\uaa40-\uaa42\uaa44-\uaa4b\uab01-\uab06\uab09-\uab0e\uab11-\uab16\uab20-\uab26\uab28-\uab2e\uabc0-\uabe2\uac00-\ud7a3\ud7b0-\ud7c6\ud7cb-\ud7fb\ufb00-\ufb06\ufb13-\ufb17\ufb1d\ufb1f-\ufb28\ufb2a-\ufb36\ufb38-\ufb3c\ufb3e\ufb40\ufb41\ufb43\ufb44\ufb46-\ufbb1\ufbd3-\ufd3d\ufd50-\ufd8f\ufd92-\ufdc7\ufdf0-\ufdfb\ufe70-\ufe74\ufe76-\ufefc\uff21-\uff3a\uff41-\uff5a\uffa0-\uffbe\uffc2-\uffc7\uffca-\uffcf\uffd2-\uffd7\uffda-\uffdc]',
      midnumlet: '[-\'\\.\u2018\u2019\u2024\uFE52\uFF07\uFF0E]',
      midletter: '[:\xB7\xB7\u05F4\u2027\uFE13\uFE55\uFF1A]',
      midnum: '[\xB1+*/,;;\u0589\u060C\u060D\u066C\u07F8\u2044\uFE10\uFE14\uFE50\uFE54\uFF0C\uFF1B]',
      numeric: '[0-9\u0660-\u0669\u066B\u06f0-\u06f9\u07c0-\u07c9\u0966-\u096f\u09e6-\u09ef\u0a66-\u0a6f\u0ae6-\u0aef\u0b66-\u0b6f\u0be6-\u0bef\u0c66-\u0c6f\u0ce6-\u0cef\u0d66-\u0d6f\u0e50-\u0e59\u0ed0-\u0ed9\u0f20-\u0f29\u1040-\u1049\u1090-\u1099\u17e0-\u17e9\u1810-\u1819\u1946-\u194f\u19d0-\u19d9\u1a80-\u1a89\u1a90-\u1a99\u1b50-\u1b59\u1bb0-\u1bb9\u1c40-\u1c49\u1c50-\u1c59\ua620-\ua629\ua8d0-\ua8d9\ua900-\ua909\ua9d0-\ua9d9\uaa50-\uaa59\uabf0-\uabf9]',
      cr: '\\r',
      lf: '\\n',
      newline: '[\x0B\f\x85\u2028\u2029]',
      extend: '[\u0300-\u036f\u0483-\u0489\u0591-\u05bd\u05bf\u05c1\u05c2\u05c4\u05c5\u05c7\u0610-\u061a\u064b-\u065f\u0670\u06d6-\u06dc\u06df-\u06e4\u06e7\u06e8\u06ea-\u06ed\u0711\u0730-\u074a\u07a6-\u07b0\u07eb-\u07f3\u0816-\u0819\u081b-\u0823\u0825-\u0827\u0829-\u082d\u0859-\u085b\u0900-\u0903\u093a-\u093c\u093e-\u094f\u0951-\u0957\u0962\u0963\u0981-\u0983\u09bc\u09be-\u09c4\u09c7\u09c8\u09cb-\u09cd\u09d7\u09e2\u09e3\u0a01-\u0a03\u0a3c\u0a3e-\u0a42\u0a47\u0a48\u0a4b-\u0a4d\u0a51\u0a70\u0a71\u0a75\u0a81-\u0a83\u0abc\u0abe-\u0ac5\u0ac7-\u0ac9\u0acb-\u0acd\u0ae2\u0ae3\u0b01-\u0b03\u0b3c\u0b3e-\u0b44\u0b47\u0b48\u0b4b-\u0b4d\u0b56\u0b57\u0b62\u0b63\u0b82\u0bbe-\u0bc2\u0bc6-\u0bc8\u0bca-\u0bcd\u0bd7\u0c01-\u0c03\u0c3e-\u0c44\u0c46-\u0c48\u0c4a-\u0c4d\u0c55\u0c56\u0c62\u0c63\u0c82\u0c83\u0cbc\u0cbe-\u0cc4\u0cc6-\u0cc8\u0cca-\u0ccd\u0cd5\u0cd6\u0ce2\u0ce3\u0d02\u0d03\u0d3e-\u0d44\u0d46-\u0d48\u0d4a-\u0d4d\u0d57\u0d62\u0d63\u0d82\u0d83\u0dca\u0dcf-\u0dd4\u0dd6\u0dd8-\u0ddf\u0df2\u0df3\u0e31\u0e34-\u0e3a\u0e47-\u0e4e\u0eb1\u0eb4-\u0eb9\u0ebb\u0ebc\u0ec8-\u0ecd\u0f18\u0f19\u0f35\u0f37\u0f39\u0f3e\u0f3f\u0f71-\u0f84\u0f86\u0f87\u0f8d-\u0f97\u0f99-\u0fbc\u0fc6\u102b-\u103e\u1056-\u1059\u105e-\u1060\u1062-\u1064\u1067-\u106d\u1071-\u1074\u1082-\u108d\u108f\u109a-\u109d\u135d-\u135f\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17b6-\u17d3\u17dd\u180b-\u180d\u18a9\u1920-\u192b\u1930-\u193b\u19b0-\u19c0\u19c8\u19c9\u1a17-\u1a1b\u1a55-\u1a5e\u1a60-\u1a7c\u1a7f\u1b00-\u1b04\u1b34-\u1b44\u1b6b-\u1b73\u1b80-\u1b82\u1ba1-\u1baa\u1be6-\u1bf3\u1c24-\u1c37\u1cd0-\u1cd2\u1cd4-\u1ce8\u1ced\u1cf2\u1dc0-\u1de6\u1dfc-\u1dff\u200c\u200d\u20d0-\u20f0\u2cef-\u2cf1\u2d7f\u2de0-\u2dff\u302a-\u302f\u3099\u309a\ua66f-\uA672\ua67c\ua67d\ua6f0\ua6f1\ua802\ua806\ua80b\ua823-\ua827\ua880\ua881\ua8b4-\ua8c4\ua8e0-\ua8f1\ua926-\ua92d\ua947-\ua953\ua980-\ua983\ua9b3-\ua9c0\uaa29-\uaa36\uaa43\uaa4c\uaa4d\uaa7b\uaab0\uaab2-\uaab4\uaab7\uaab8\uaabe\uaabf\uaac1\uabe3-\uabea\uabec\uabed\ufb1e\ufe00-\ufe0f\ufe20-\ufe26\uff9e\uff9f]',
      format: '[\xAD\u0600-\u0603\u06DD\u070F\u17b4\u17b5\u200E\u200F\u202A-\u202E\u2060-\u2064\u206A-\u206F\uFEFF\uFFF9-\uFFFB]',
      katakana: '[\u3031-\u3035\u309B\u309C\u30A0-\u30fa\u30fc-\u30ff\u31f0-\u31ff\u32D0-\u32FE\u3300-\u3357\uff66-\uff9d]',
      extendnumlet: '[=_\u203f\u2040\u2054\ufe33\ufe34\ufe4d-\ufe4f\uff3f\u2200-\u22FF<>]',
      punctuation: punctuationStr
    };
    var characterIndices = {
      ALETTER: 0,
      MIDNUMLET: 1,
      MIDLETTER: 2,
      MIDNUM: 3,
      NUMERIC: 4,
      CR: 5,
      LF: 6,
      NEWLINE: 7,
      EXTEND: 8,
      FORMAT: 9,
      KATAKANA: 10,
      EXTENDNUMLET: 11,
      AT: 12,
      OTHER: 13
    };
    var SETS = [
      new RegExp(regExps.aletter),
      new RegExp(regExps.midnumlet),
      new RegExp(regExps.midletter),
      new RegExp(regExps.midnum),
      new RegExp(regExps.numeric),
      new RegExp(regExps.cr),
      new RegExp(regExps.lf),
      new RegExp(regExps.newline),
      new RegExp(regExps.extend),
      new RegExp(regExps.format),
      new RegExp(regExps.katakana),
      new RegExp(regExps.extendnumlet),
      new RegExp('@')
    ];
    var EMPTY_STRING = '';
    var PUNCTUATION = new RegExp('^' + regExps.punctuation + '$');
    var WHITESPACE = /^\s+$/;

    var SETS$1 = SETS;
    var OTHER = characterIndices.OTHER;
    var getType = function (char) {
      var type = OTHER;
      var setsLength = SETS$1.length;
      for (var j = 0; j < setsLength; ++j) {
        var set = SETS$1[j];
        if (set && set.test(char)) {
          type = j;
          break;
        }
      }
      return type;
    };
    var memoize = function (func) {
      var cache = {};
      return function (char) {
        if (cache[char]) {
          return cache[char];
        } else {
          var result = func(char);
          cache[char] = result;
          return result;
        }
      };
    };
    var classify = function (characters) {
      var memoized = memoize(getType);
      return map(characters, memoized);
    };

    var isWordBoundary = function (map, index) {
      var type = map[index];
      var nextType = map[index + 1];
      if (index < 0 || index > map.length - 1 && index !== 0) {
        return false;
      }
      if (type === characterIndices.ALETTER && nextType === characterIndices.ALETTER) {
        return false;
      }
      var nextNextType = map[index + 2];
      if (type === characterIndices.ALETTER && (nextType === characterIndices.MIDLETTER || nextType === characterIndices.MIDNUMLET || nextType === characterIndices.AT) && nextNextType === characterIndices.ALETTER) {
        return false;
      }
      var prevType = map[index - 1];
      if ((type === characterIndices.MIDLETTER || type === characterIndices.MIDNUMLET || nextType === characterIndices.AT) && nextType === characterIndices.ALETTER && prevType === characterIndices.ALETTER) {
        return false;
      }
      if ((type === characterIndices.NUMERIC || type === characterIndices.ALETTER) && (nextType === characterIndices.NUMERIC || nextType === characterIndices.ALETTER)) {
        return false;
      }
      if ((type === characterIndices.MIDNUM || type === characterIndices.MIDNUMLET) && nextType === characterIndices.NUMERIC && prevType === characterIndices.NUMERIC) {
        return false;
      }
      if (type === characterIndices.NUMERIC && (nextType === characterIndices.MIDNUM || nextType === characterIndices.MIDNUMLET) && nextNextType === characterIndices.NUMERIC) {
        return false;
      }
      if (type === characterIndices.EXTEND || type === characterIndices.FORMAT || prevType === characterIndices.EXTEND || prevType === characterIndices.FORMAT || nextType === characterIndices.EXTEND || nextType === characterIndices.FORMAT) {
        return false;
      }
      if (type === characterIndices.CR && nextType === characterIndices.LF) {
        return false;
      }
      if (type === characterIndices.NEWLINE || type === characterIndices.CR || type === characterIndices.LF) {
        return true;
      }
      if (nextType === characterIndices.NEWLINE || nextType === characterIndices.CR || nextType === characterIndices.LF) {
        return true;
      }
      if (type === characterIndices.KATAKANA && nextType === characterIndices.KATAKANA) {
        return false;
      }
      if (nextType === characterIndices.EXTENDNUMLET && (type === characterIndices.ALETTER || type === characterIndices.NUMERIC || type === characterIndices.KATAKANA || type === characterIndices.EXTENDNUMLET)) {
        return false;
      }
      if (type === characterIndices.EXTENDNUMLET && (nextType === characterIndices.ALETTER || nextType === characterIndices.NUMERIC || nextType === characterIndices.KATAKANA)) {
        return false;
      }
      if (type === characterIndices.AT) {
        return false;
      }
      return true;
    };

    var EMPTY_STRING$1 = EMPTY_STRING;
    var WHITESPACE$1 = WHITESPACE;
    var PUNCTUATION$1 = PUNCTUATION;
    var isProtocol = function (str) {
      return str === 'http' || str === 'https';
    };
    var findWordEnd = function (characters, startIndex) {
      var i;
      for (i = startIndex; i < characters.length; i++) {
        if (WHITESPACE$1.test(characters[i])) {
          break;
        }
      }
      return i;
    };
    var findUrlEnd = function (characters, startIndex) {
      var endIndex = findWordEnd(characters, startIndex + 1);
      var peakedWord = characters.slice(startIndex + 1, endIndex).join(EMPTY_STRING$1);
      return peakedWord.substr(0, 3) === '://' ? endIndex : startIndex;
    };
    var findWords = function (chars, sChars, characterMap, options) {
      var words = [];
      var word = [];
      for (var i = 0; i < characterMap.length; ++i) {
        word.push(chars[i]);
        if (isWordBoundary(characterMap, i)) {
          var ch = sChars[i];
          if ((options.includeWhitespace || !WHITESPACE$1.test(ch)) && (options.includePunctuation || !PUNCTUATION$1.test(ch))) {
            var startOfWord = i - word.length + 1;
            var endOfWord = i + 1;
            var str = sChars.slice(startOfWord, endOfWord).join(EMPTY_STRING$1);
            if (isProtocol(str)) {
              var endOfUrl = findUrlEnd(sChars, i);
              var url = chars.slice(endOfWord, endOfUrl);
              Array.prototype.push.apply(word, url);
              i = endOfUrl;
            }
            words.push(word);
          }
          word = [];
        }
      }
      return words;
    };
    var getDefaultOptions = function () {
      return {
        includeWhitespace: false,
        includePunctuation: false
      };
    };
    var getWords = function (chars, extract, options) {
      options = __assign(__assign({}, getDefaultOptions()), options);
      var filteredChars = [];
      var extractedChars = [];
      for (var i = 0; i < chars.length; i++) {
        var ch = extract(chars[i]);
        if (ch !== zeroWidth) {
          filteredChars.push(chars[i]);
          extractedChars.push(ch);
        }
      }
      var characterMap = classify(extractedChars);
      return findWords(filteredChars, extractedChars, characterMap, options);
    };

    var getWords$1 = getWords;

    var global$1 = tinymce.util.Tools.resolve('tinymce.dom.TreeWalker');

    var getText = function (node, schema) {
      var blockElements = schema.getBlockElements();
      var shortEndedElements = schema.getShortEndedElements();
      var isNewline = function (node) {
        return blockElements[node.nodeName] || shortEndedElements[node.nodeName];
      };
      var textBlocks = [];
      var txt = '';
      var treeWalker = new global$1(node, node);
      while (node = treeWalker.next()) {
        if (node.nodeType === 3) {
          txt += removeZwsp(node.data);
        } else if (isNewline(node) && txt.length) {
          textBlocks.push(txt);
          txt = '';
        }
      }
      if (txt.length) {
        textBlocks.push(txt);
      }
      return textBlocks;
    };

    var strLen = function (str) {
      return str.replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, '_').length;
    };
    var countWords = function (node, schema) {
      var text = getText(node, schema).join('\n');
      return getWords$1(text.split(''), identity).length;
    };
    var countCharacters = function (node, schema) {
      var text = getText(node, schema).join('');
      return strLen(text);
    };
    var countCharactersWithoutSpaces = function (node, schema) {
      var text = getText(node, schema).join('').replace(/\s/g, '');
      return strLen(text);
    };

    var createBodyCounter = function (editor, count) {
      return function () {
        return count(editor.getBody(), editor.schema);
      };
    };
    var createSelectionCounter = function (editor, count) {
      return function () {
        return count(editor.selection.getRng().cloneContents(), editor.schema);
      };
    };
    var createBodyWordCounter = function (editor) {
      return createBodyCounter(editor, countWords);
    };
    var get = function (editor) {
      return {
        body: {
          getWordCount: createBodyWordCounter(editor),
          getCharacterCount: createBodyCounter(editor, countCharacters),
          getCharacterCountWithoutSpaces: createBodyCounter(editor, countCharactersWithoutSpaces)
        },
        selection: {
          getWordCount: createSelectionCounter(editor, countWords),
          getCharacterCount: createSelectionCounter(editor, countCharacters),
          getCharacterCountWithoutSpaces: createSelectionCounter(editor, countCharactersWithoutSpaces)
        },
        getCount: createBodyWordCounter(editor)
      };
    };

    var global$2 = tinymce.util.Tools.resolve('tinymce.util.Delay');

    var fireWordCountUpdate = function (editor, api) {
      editor.fire('wordCountUpdate', {
        wordCount: {
          words: api.body.getWordCount(),
          characters: api.body.getCharacterCount(),
          charactersWithoutSpaces: api.body.getCharacterCountWithoutSpaces()
        }
      });
    };

    var updateCount = function (editor, api) {
      fireWordCountUpdate(editor, api);
    };
    var setup = function (editor, api, delay) {
      var debouncedUpdate = global$2.debounce(function () {
        return updateCount(editor, api);
      }, delay);
      editor.on('init', function () {
        updateCount(editor, api);
        global$2.setEditorTimeout(editor, function () {
          editor.on('SetContent BeforeAddUndo Undo Redo keyup', debouncedUpdate);
        }, 0);
      });
    };

    var open = function (editor, api) {
      editor.windowManager.open({
        title: 'Word Count',
        body: {
          type: 'panel',
          items: [{
              type: 'table',
              header: [
                'Count',
                'Document',
                'Selection'
              ],
              cells: [
                [
                  'Words',
                  String(api.body.getWordCount()),
                  String(api.selection.getWordCount())
                ],
                [
                  'Characters (no spaces)',
                  String(api.body.getCharacterCountWithoutSpaces()),
                  String(api.selection.getCharacterCountWithoutSpaces())
                ],
                [
                  'Characters',
                  String(api.body.getCharacterCount()),
                  String(api.selection.getCharacterCount())
                ]
              ]
            }]
        },
        buttons: [{
            type: 'cancel',
            name: 'close',
            text: 'Close',
            primary: true
          }]
      });
    };

    var register = function (editor, api) {
      editor.ui.registry.addButton('wordcount', {
        tooltip: 'Word count',
        icon: 'character-count',
        onAction: function () {
          return open(editor, api);
        }
      });
      editor.ui.registry.addMenuItem('wordcount', {
        text: 'Word count',
        icon: 'character-count',
        onAction: function () {
          return open(editor, api);
        }
      });
    };

    function Plugin (delay) {
      if (delay === void 0) {
        delay = 300;
      }
      global.add('wordcount', function (editor) {
        var api = get(editor);
        register(editor, api);
        setup(editor, api, delay);
        return api;
      });
    }

    Plugin();

}());
