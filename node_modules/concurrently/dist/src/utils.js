"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.escapeRegExp = escapeRegExp;
exports.castArray = castArray;
/**
 * Escapes a string for use in a regular expression.
 */
function escapeRegExp(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}
/**
 * Casts a value to an array if it's not one.
 */
function castArray(value) {
    return (Array.isArray(value) ? value : value != null ? [value] : []);
}
