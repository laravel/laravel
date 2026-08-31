import quote = require('./quote');
import parse = require('./parse');

export { quote, parse };

export type ControlOperator = parse.ControlOperator;
export type GlobPattern = parse.GlobPattern;
export type Comment = parse.Comment;
export type ParseEntry = parse.ParseEntry;
export type ParseOptions = parse.ParseOptions;

type Join<T extends readonly string[], D extends string> = T extends readonly []
	? ''
	: T extends readonly [infer F extends string]
		? F
		: T extends readonly [infer F extends string, ...infer R extends string[]]
			? `${F}${D}${Join<R, D>}`
			: string;

declare global {
	interface ReadonlyArray<T> {
		join<This extends readonly string[], D extends string = ','>(this: This, separator?: D): Join<This, D>;
	}

	interface Array<T> {
		join<This extends readonly string[], D extends string = ','>(this: This, separator?: D): Join<This, D>;
	}
}
