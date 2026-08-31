declare namespace parse {
	/** A shell control operator. */
	export interface ControlOperator {
		op: '||' | '&&' | ';;' | '|&' | '<(' | '<<<' | '>>' | '>&' | '<&' | '&' | ';' | '(' | ')' | '|' | '<' | '>';
	}

	/** A glob pattern parsed from the shell command. */
	export interface GlobPattern {
		op: 'glob';
		pattern: string;
	}

	/** A shell comment. */
	export interface Comment {
		comment: string;
	}

	/** A parsed token returned by {@link parse}. */
	export type ParseEntry = string | ControlOperator | GlobPattern | Comment;

	/** Options for the {@link parse} function. */
	export interface ParseOptions {
		/** Custom escape character. Defaults to `\\`. */
		escape?: string;
	}

	export type Env =
		| Record<string, string | undefined>
		| ((key: string) => string | object | undefined);
}

/**
 * Parses a shell command string into an array of tokens.
 *
 * @param s - The shell command string to parse.
 * @param env - Optional environment variables for expansion, either as an object of string values or a lookup function. When the lookup function returns an object, that object is inserted into the result verbatim.
 * @param opts - Optional parsing options.
 * @returns An array of parsed tokens, including any objects returned by an `env` lookup function.
 */
declare function parse<T extends string | object = never>(
	s: string,
	env?: parse.Env,
	opts?: parse.ParseOptions,
): (parse.ParseEntry | T)[];

export = parse;
