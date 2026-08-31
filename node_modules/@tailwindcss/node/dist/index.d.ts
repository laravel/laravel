import { AstNode as AstNode$1 } from './ast';
import { Candidate, Variant } from './candidate';
import { compileAstNodes } from './compile';
import { ClassEntry, VariantEntry, CanonicalizeOptions } from './intellisense';
import { Theme } from './theme';
import { Utilities } from './utilities';
import { Variants } from './variants';
import * as tailwindcss from 'tailwindcss';
import { Polyfills, Features } from 'tailwindcss';
export { Features, Polyfills } from 'tailwindcss';

declare const DEBUG: boolean;

declare const env_DEBUG: typeof DEBUG;
declare namespace env {
  export { env_DEBUG as DEBUG };
}

declare const enum CompileAstFlags {
    None = 0,
    RespectImportant = 1
}
type DesignSystem = {
    theme: Theme;
    utilities: Utilities;
    variants: Variants;
    invalidCandidates: Set<string>;
    important: boolean;
    getClassOrder(classes: string[]): [string, bigint | null][];
    getClassList(): ClassEntry[];
    getVariants(): VariantEntry[];
    parseCandidate(candidate: string): Readonly<Candidate>[];
    parseVariant(variant: string): Readonly<Variant> | null;
    compileAstNodes(candidate: Candidate, flags?: CompileAstFlags): ReturnType<typeof compileAstNodes>;
    printCandidate(candidate: Candidate): string;
    printVariant(variant: Variant): string;
    getVariantOrder(): Map<Variant, number>;
    resolveThemeValue(path: string, forceInline?: boolean): string | undefined;
    trackUsedVariables(raw: string): void;
    canonicalizeCandidates(candidates: string[], options?: CanonicalizeOptions): string[];
    candidatesToCss(classes: string[]): (string | null)[];
    candidatesToAst(classes: string[]): AstNode$1[][];
    storage: Record<symbol, unknown>;
};

/**
 * The source code for one or more nodes in the AST
 *
 * This generally corresponds to a stylesheet
 */
interface Source {
    /**
     * The path to the file that contains the referenced source code
     *
     * If this references the *output* source code, this is `null`.
     */
    file: string | null;
    /**
     * The referenced source code
     */
    code: string;
}
/**
 * The file and offsets within it that this node covers
 *
 * This can represent either:
 * - A location in the original CSS which caused this node to be created
 * - A location in the output CSS where this node resides
 */
type SourceLocation = [source: Source, start: number, end: number];

/**
 * Line offset tables are the key to generating our source maps. They allow us
 * to store indexes with our AST nodes and later convert them into positions as
 * when given the source that the indexes refer to.
 */
/**
 * A position in source code
 *
 * https://tc39.es/ecma426/#sec-position-record-type
 */
interface Position {
    /** The line number, one-based */
    line: number;
    /** The column/character number, one-based */
    column: number;
}

interface OriginalPosition extends Position {
    source: DecodedSource;
}
/**
 * A "decoded" sourcemap
 *
 * @see https://tc39.es/ecma426/#decoded-source-map-record
 */
interface DecodedSourceMap {
    file: string | null;
    sources: DecodedSource[];
    mappings: DecodedMapping[];
}
/**
 * A "decoded" source
 *
 * @see https://tc39.es/ecma426/#decoded-source-record
 */
interface DecodedSource {
    url: string | null;
    content: string | null;
    ignore: boolean;
}
/**
 * A "decoded" mapping
 *
 * @see https://tc39.es/ecma426/#decoded-mapping-record
 */
interface DecodedMapping {
    originalPosition: OriginalPosition | null;
    generatedPosition: Position;
    name: string | null;
}

type StyleRule = {
    kind: 'rule';
    selector: string;
    nodes: AstNode[];
    src?: SourceLocation;
    dst?: SourceLocation;
};
type AtRule = {
    kind: 'at-rule';
    name: string;
    params: string;
    nodes: AstNode[];
    src?: SourceLocation;
    dst?: SourceLocation;
};
type Declaration = {
    kind: 'declaration';
    property: string;
    value: string | undefined;
    important: boolean;
    src?: SourceLocation;
    dst?: SourceLocation;
};
type Comment = {
    kind: 'comment';
    value: string;
    src?: SourceLocation;
    dst?: SourceLocation;
};
type Context = {
    kind: 'context';
    context: Record<string, string | boolean>;
    nodes: AstNode[];
    src?: undefined;
    dst?: undefined;
};
type AtRoot = {
    kind: 'at-root';
    nodes: AstNode[];
    src?: undefined;
    dst?: undefined;
};
type AstNode = StyleRule | AtRule | Declaration | Comment | Context | AtRoot;

type Resolver = (id: string, base: string) => Promise<string | false | undefined>;
interface CompileOptions {
    base: string;
    from?: string;
    onDependency: (path: string) => void;
    shouldRewriteUrls?: boolean;
    polyfills?: Polyfills;
    customCssResolver?: Resolver;
    customJsResolver?: Resolver;
}
declare function compileAst(ast: AstNode[], options: CompileOptions): Promise<{
    sources: {
        base: string;
        pattern: string;
        negated: boolean;
    }[];
    root: "none" | {
        base: string;
        pattern: string;
    } | null;
    features: Features;
    build(candidates: string[]): AstNode[];
}>;
declare function compile(css: string, options: CompileOptions): Promise<{
    sources: {
        base: string;
        pattern: string;
        negated: boolean;
    }[];
    root: "none" | {
        base: string;
        pattern: string;
    } | null;
    features: Features;
    build(candidates: string[]): string;
    buildSourceMap(): tailwindcss.DecodedSourceMap;
}>;
declare function __unstable__loadDesignSystem(css: string, { base }: {
    base: string;
}): Promise<DesignSystem>;
declare function loadModule(id: string, base: string, onDependency: (path: string) => void, customJsResolver?: Resolver): Promise<{
    path: string;
    base: string;
    module: any;
}>;

declare class Instrumentation implements Disposable {
    #private;
    private shouldReport;
    private defaultFlush;
    constructor(shouldReport?: boolean, defaultFlush?: (message: string) => undefined);
    hit(label: string): void;
    start(label: string): void;
    end(label: string): void;
    track(label: string): {
        [Symbol.dispose]: () => void;
        [Symbol.asyncDispose]: () => void;
    };
    span<T>(label: string, fn: () => T): T;
    reset(): void;
    report(flush?: (message: string) => undefined): void;
    [Symbol.dispose](): void;
}

declare function normalizePath(originalPath: string): string;

interface OptimizeOptions {
    /**
     * The file being transformed
     */
    file?: string;
    /**
     * Enabled minified output
     */
    minify?: boolean;
    /**
     * The output source map before optimization
     *
     * If omitted a resulting source map will not be available
     */
    map?: string;
}
interface TransformResult {
    code: string;
    map: string | undefined;
}
declare function optimize(input: string, { file, minify, map }?: OptimizeOptions): TransformResult;

interface SourceMap {
    readonly raw: string;
    readonly inline: string;
    comment(url: string): string;
}
declare function toSourceMap(map: DecodedSourceMap | string): SourceMap;

export { type CompileOptions, type DecodedSource, type DecodedSourceMap, Instrumentation, type OptimizeOptions, type Resolver, type SourceMap, type TransformResult, __unstable__loadDesignSystem, compile, compileAst, env, loadModule, normalizePath, optimize, toSourceMap };
