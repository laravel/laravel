"use strict";
/*
ORIGINAL https://www.npmjs.com/package/tiny-jsonc
BY Fabio Spampinato
MIT license

Copied due to the dependency not being compatible with CommonJS
*/
Object.defineProperty(exports, "__esModule", { value: true });
/* HELPERS */
const stringOrCommentRe = /("(?:\\?[^])*?")|(\/\/.*)|(\/\*[^]*?\*\/)/g;
const stringOrTrailingCommaRe = /("(?:\\?[^])*?")|(,\s*)(?=]|})/g;
/* MAIN */
const JSONC = {
    parse: (text) => {
        text = String(text); // To be extra safe
        try {
            // Fast path for valid JSON
            return JSON.parse(text);
        }
        catch {
            // Slow path for JSONC and invalid inputs
            return JSON.parse(text.replace(stringOrCommentRe, '$1').replace(stringOrTrailingCommaRe, '$1'));
        }
    },
    stringify: JSON.stringify,
};
/* EXPORT */
exports.default = JSONC;
