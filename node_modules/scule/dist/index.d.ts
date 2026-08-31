type Splitter = "-" | "_" | "/" | ".";
type FirstOfString<S extends string> = S extends `${infer F}${string}` ? F : never;
type RemoveFirstOfString<S extends string> = S extends `${string}${infer R}` ? R : never;
type IsUpper<S extends string> = S extends Uppercase<S> ? true : false;
type IsLower<S extends string> = S extends Lowercase<S> ? true : false;
type SameLetterCase<X extends string, Y extends string> = IsUpper<X> extends IsUpper<Y> ? true : IsLower<X> extends IsLower<Y> ? true : false;
type CapitalizedWords<T extends readonly string[], Accumulator extends string = "", Normalize extends boolean | undefined = false> = T extends readonly [infer F extends string, ...infer R extends string[]] ? CapitalizedWords<R, `${Accumulator}${Capitalize<Normalize extends true ? Lowercase<F> : F>}`, Normalize> : Accumulator;
type JoinLowercaseWords<T extends readonly string[], Joiner extends string, Accumulator extends string = ""> = T extends readonly [infer F extends string, ...infer R extends string[]] ? Accumulator extends "" ? JoinLowercaseWords<R, Joiner, `${Accumulator}${Lowercase<F>}`> : JoinLowercaseWords<R, Joiner, `${Accumulator}${Joiner}${Lowercase<F>}`> : Accumulator;
type LastOfArray<T extends any[]> = T extends [...any, infer R] ? R : never;
type RemoveLastOfArray<T extends any[]> = T extends [...infer F, any] ? F : never;
type CaseOptions = {
    normalize?: boolean;
};
type SplitByCase<T, Separator extends string = Splitter, Accumulator extends unknown[] = []> = string extends Separator ? string[] : T extends `${infer F}${infer R}` ? [LastOfArray<Accumulator>] extends [never] ? SplitByCase<R, Separator, [F]> : LastOfArray<Accumulator> extends string ? R extends "" ? SplitByCase<R, Separator, [
    ...RemoveLastOfArray<Accumulator>,
    `${LastOfArray<Accumulator>}${F}`
]> : SameLetterCase<F, FirstOfString<R>> extends true ? F extends Separator ? FirstOfString<R> extends Separator ? SplitByCase<R, Separator, [...Accumulator, ""]> : IsUpper<FirstOfString<R>> extends true ? SplitByCase<RemoveFirstOfString<R>, Separator, [
    ...Accumulator,
    FirstOfString<R>
]> : SplitByCase<R, Separator, [...Accumulator, ""]> : SplitByCase<R, Separator, [
    ...RemoveLastOfArray<Accumulator>,
    `${LastOfArray<Accumulator>}${F}`
]> : IsLower<F> extends true ? SplitByCase<RemoveFirstOfString<R>, Separator, [
    ...RemoveLastOfArray<Accumulator>,
    `${LastOfArray<Accumulator>}${F}`,
    FirstOfString<R>
]> : SplitByCase<R, Separator, [...Accumulator, F]> : never : Accumulator extends [] ? T extends "" ? [] : string[] : Accumulator;
type JoinByCase<T, Joiner extends string> = string extends T ? string : string[] extends T ? string : T extends string ? SplitByCase<T> extends readonly string[] ? JoinLowercaseWords<SplitByCase<T>, Joiner> : never : T extends readonly string[] ? JoinLowercaseWords<T, Joiner> : never;
type PascalCase<T, Normalize extends boolean | undefined = false> = string extends T ? string : string[] extends T ? string : T extends string ? SplitByCase<T> extends readonly string[] ? CapitalizedWords<SplitByCase<T>, "", Normalize> : never : T extends readonly string[] ? CapitalizedWords<T, "", Normalize> : never;
type CamelCase<T, Normalize extends boolean | undefined = false> = string extends T ? string : string[] extends T ? string : Uncapitalize<PascalCase<T, Normalize>>;
type KebabCase<T extends string | readonly string[], Joiner extends string = "-"> = JoinByCase<T, Joiner>;
type SnakeCase<T extends string | readonly string[]> = JoinByCase<T, "_">;
type TrainCase<T, Normalize extends boolean | undefined = false, Joiner extends string = "-"> = string extends T ? string : string[] extends T ? string : T extends string ? SplitByCase<T> extends readonly string[] ? CapitalizedWords<SplitByCase<T>, Joiner> : never : T extends readonly string[] ? CapitalizedWords<T, Joiner, Normalize> : never;
type FlatCase<T extends string | readonly string[], Joiner extends string = ""> = JoinByCase<T, Joiner>;

declare function isUppercase(char?: string): boolean | undefined;
declare function splitByCase<T extends string>(str: T): SplitByCase<T>;
declare function splitByCase<T extends string, Separator extends readonly string[]>(str: T, separators: Separator): SplitByCase<T, Separator[number]>;
declare function upperFirst<S extends string>(str: S): Capitalize<S>;
declare function lowerFirst<S extends string>(str: S): Uncapitalize<S>;
declare function pascalCase(): "";
declare function pascalCase<T extends string | readonly string[], UserCaseOptions extends CaseOptions = CaseOptions>(str: T, opts?: CaseOptions): PascalCase<T, UserCaseOptions["normalize"]>;
declare function camelCase(): "";
declare function camelCase<T extends string | readonly string[], UserCaseOptions extends CaseOptions = CaseOptions>(str: T, opts?: UserCaseOptions): CamelCase<T, UserCaseOptions["normalize"]>;
declare function kebabCase(): "";
declare function kebabCase<T extends string | readonly string[]>(str: T): KebabCase<T>;
declare function kebabCase<T extends string | readonly string[], Joiner extends string>(str: T, joiner: Joiner): KebabCase<T, Joiner>;
declare function snakeCase(): "";
declare function snakeCase<T extends string | readonly string[]>(str: T): SnakeCase<T>;
declare function flatCase(): "";
declare function flatCase<T extends string | readonly string[]>(str: T): FlatCase<T>;
declare function trainCase(): "";
declare function trainCase<T extends string | readonly string[], UserCaseOptions extends CaseOptions = CaseOptions>(str: T, opts?: UserCaseOptions): TrainCase<T, UserCaseOptions["normalize"]>;
declare function titleCase(): "";
declare function titleCase<T extends string | readonly string[], UserCaseOptions extends CaseOptions = CaseOptions>(str: T, opts?: UserCaseOptions): TrainCase<T, UserCaseOptions["normalize"], " ">;

export { type CamelCase, type CaseOptions, type FlatCase, type JoinByCase, type KebabCase, type PascalCase, type SnakeCase, type SplitByCase, type TrainCase, camelCase, flatCase, isUppercase, kebabCase, lowerFirst, pascalCase, snakeCase, splitByCase, titleCase, trainCase, upperFirst };
