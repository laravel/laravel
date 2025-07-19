/**
 * Creates a Proxy for resolving raw values for options.
 * @param {object[]} scopes - The option scopes to look for values, in resolution order
 * @param {string[]} [prefixes] - The prefixes for values, in resolution order.
 * @param {object[]} [rootScopes] - The root option scopes
 * @param {string|boolean} [fallback] - Parent scopes fallback
 * @param {function} [getTarget] - callback for getting the target for changed values
 * @returns Proxy
 * @private
 */
export function _createResolver(scopes: object[], prefixes?: string[], rootScopes?: object[], fallback?: string | boolean, getTarget?: Function): {
    [Symbol.toStringTag]: string;
    _cacheable: boolean;
    _scopes: any[];
    _rootScopes: any[];
    _fallback: string | boolean;
    _getTarget: Function;
    override: (scope: any) => any;
};
/**
 * Returns an Proxy for resolving option values with context.
 * @param {object} proxy - The Proxy returned by `_createResolver`
 * @param {object} context - Context object for scriptable/indexable options
 * @param {object} [subProxy] - The proxy provided for scriptable options
 * @param {{scriptable: boolean, indexable: boolean, allKeys?: boolean}} [descriptorDefaults] - Defaults for descriptors
 * @private
 */
export function _attachContext(proxy: object, context: object, subProxy?: object, descriptorDefaults?: {
    scriptable: boolean;
    indexable: boolean;
    allKeys?: boolean;
}): {
    _cacheable: boolean;
    _proxy: any;
    _context: any;
    _subProxy: any;
    _stack: Set<any>;
    _descriptors: {
        allKeys: any;
        scriptable: any;
        indexable: any;
        isScriptable: (...args: any[]) => any;
        isIndexable: (...args: any[]) => any;
    };
    setContext: (ctx: any) => any;
    override: (scope: any) => any;
};
/**
 * @private
 */
export function _descriptors(proxy: any, defaults?: {
    scriptable: boolean;
    indexable: boolean;
}): {
    allKeys: any;
    scriptable: any;
    indexable: any;
    isScriptable: (...args: any[]) => any;
    isIndexable: (...args: any[]) => any;
};
export function _parseObjectDataRadialScale(meta: any, data: any, start: any, count: any): any[];
