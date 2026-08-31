declare function isObject<T>(x: T): x is T & object & Record<PropertyKey, unknown>;

export = isObject;
