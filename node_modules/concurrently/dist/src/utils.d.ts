/**
 * Escapes a string for use in a regular expression.
 */
export declare function escapeRegExp(str: string): string;
type CastArrayResult<T> = T extends undefined | null ? never[] : T extends unknown[] ? T : T[];
/**
 * Casts a value to an array if it's not one.
 */
export declare function castArray<T = never[]>(value?: T): CastArrayResult<T>;
export {};
