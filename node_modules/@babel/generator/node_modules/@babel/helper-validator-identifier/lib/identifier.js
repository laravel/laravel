const bmpIdentifierStart = /[\p{ID_Start}\u088f\u0c5c\u0cdc\ua7ce\ua7cf\ua7d2\ua7d4\ua7f1]/u;
const bmpIdentifier = /[\p{ID_Continue}\u088f\u0c5c\u0cdc\ua7ce\ua7cf\ua7d2\ua7d4\ua7f1\u1acf-\u1add\u1ae0-\u1aeb]/u;
const supplementaryIdentifierStartCodes = [2368, 25, 1388, 2, 3817, 43, 20677, 24, 3, 24, 287, 4, 6146, 7, 1290, 21, 98, 114, 22734, 30, 2, 2, 2, 1, 2, 6, 3, 4, 10, 1, 53307, 5, 5987, 11, 21763, 4297];
const supplementaryIdentifierCodes = [3834, 1, 3173, 7, 633, 9, 51450, 0, 3, 0, 8, 1, 6, 0];

function isInSupplementarySet(code, set) {
  let pos = 0x10000;
  for (let i = 0, length = set.length; i < length; i += 2) {
    pos += set[i];
    if (pos > code) return false;
    pos += set[i + 1];
    if (pos >= code) return true;
  }
  return false;
}
function isIdentifierStart(code) {
  if (code < 65) return code === 36;
  if (code <= 90) return true;
  if (code < 97) return code === 95;
  if (code <= 122) return true;
  if (code <= 0xffff) {
    return code >= 0xaa && bmpIdentifierStart.test(String.fromCharCode(code));
  }
  return !isNaN(code) && code <= 0x10ffff && (bmpIdentifierStart.test(String.fromCodePoint(code)) || isInSupplementarySet(code, supplementaryIdentifierStartCodes));
}
function isIdentifierChar(code) {
  if (code < 48) return code === 36;
  if (code < 58) return true;
  if (code < 65) return false;
  if (code <= 90) return true;
  if (code < 97) return code === 95;
  if (code <= 122) return true;
  if (code <= 0xffff) {
    return code >= 0xaa && bmpIdentifier.test(String.fromCharCode(code));
  }
  return !isNaN(code) && code <= 0x10ffff && (bmpIdentifier.test(String.fromCodePoint(code)) || isInSupplementarySet(code, supplementaryIdentifierStartCodes) || isInSupplementarySet(code, supplementaryIdentifierCodes));
}
function isIdentifierName(name) {
  let isFirst = true;
  for (let i = 0; i < name.length; i++) {
    let cp = name.charCodeAt(i);
    if ((cp & 0xfc00) === 0xd800 && i + 1 < name.length) {
      const trail = name.charCodeAt(++i);
      if ((trail & 0xfc00) === 0xdc00) {
        cp = 0x10000 + ((cp & 0x3ff) << 10) + (trail & 0x3ff);
      }
    }
    if (isFirst) {
      isFirst = false;
      if (!isIdentifierStart(cp)) {
        return false;
      }
    } else if (!isIdentifierChar(cp)) {
      return false;
    }
  }
  return !isFirst;
}

export { isIdentifierChar, isIdentifierName, isIdentifierStart };
//# sourceMappingURL=identifier.js.map
