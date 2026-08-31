import { CodeTransform, MagicString, MagicStringAST } from "magic-string-ast";
import { FilterPattern, FilterPattern as FilterPattern$1, createFilter as createRollupFilter, normalizePath } from "unplugin-utils";
import { SFCDescriptor, SFCParseResult, SFCScriptBlock as SFCScriptBlock$1 } from "@vue/compiler-sfc";
import * as t from "@babel/types";
import { Node, Program } from "@babel/types";
import { ResolvedOptions } from "@vitejs/plugin-vue";
import { Plugin } from "rollup";
import { HmrContext, Plugin as Plugin$1 } from "vite";
export * from "magic-string-ast";
export * from "ast-kit";

//#region src/ast.d.ts
declare function checkInvalidScopeReference(node: t.Node | undefined, method: string, setupBindings: string[]): void;
declare function isStaticExpression(node: t.Node, options?: Partial<Record<"object" | "fn" | "objectMethod" | "array" | "unary" | "regex", boolean> & {
  magicComment?: string;
}>): boolean;
declare function isStaticObjectKey(node: t.ObjectExpression): boolean;
/**
* @param node must be a static expression, SpreadElement is not supported
*/
declare function resolveObjectExpression(node: t.ObjectExpression): Record<string | number, t.ObjectMethod | t.ObjectProperty> | undefined;
declare const HELPER_PREFIX = "__MACROS_";
declare function importHelperFn(s: MagicString, offset: number, imported: string, local?: string, from?: string, prefix?: string): string;
//#endregion
//#region src/constants.d.ts
declare const DEFINE_PROPS = "defineProps";
declare const DEFINE_PROPS_DOLLAR = "$defineProps";
declare const DEFINE_PROPS_REFS = "definePropsRefs";
declare const DEFINE_EMITS = "defineEmits";
declare const WITH_DEFAULTS = "withDefaults";
declare const DEFINE_OPTIONS = "defineOptions";
declare const DEFINE_MODELS = "defineModels";
declare const DEFINE_MODELS_DOLLAR = "$defineModels";
declare const DEFINE_SETUP_COMPONENT = "defineSetupComponent";
declare const DEFINE_RENDER = "defineRender";
declare const DEFINE_SLOTS = "defineSlots";
declare const DEFINE_STYLEX = "defineStyleX";
declare const DEFINE_PROP = "defineProp";
declare const DEFINE_PROP_DOLLAR = "$defineProp";
declare const DEFINE_EMIT = "defineEmit";
declare const REPO_ISSUE_URL: "https://github.com/vue-macros/vue-macros/issues";
declare const REGEX_SRC_FILE: RegExp;
declare const REGEX_SETUP_SFC: RegExp;
declare const REGEX_SETUP_SFC_SUB: RegExp;
declare const REGEX_VUE_SFC: RegExp;
/** webpack only */
declare const REGEX_VUE_SUB: RegExp;
/** webpack only */
declare const REGEX_VUE_SUB_SETUP: RegExp;
declare const REGEX_NODE_MODULES: RegExp;
declare const REGEX_SUPPORTED_EXT: RegExp;
declare const VIRTUAL_ID_PREFIX = "/vue-macros";
//#endregion
//#region src/dep.d.ts
declare function detectVueVersion(root?: string, defaultVersion?: number): number;
//#endregion
//#region src/error.d.ts
declare class TransformError<T extends string> extends SyntaxError {
  message: T;
  constructor(message: T);
}
//#endregion
//#region src/unplugin.d.ts
interface FilterOptions {
  include?: FilterPattern$1;
  exclude?: FilterPattern$1;
}
declare function createFilter(options: FilterOptions): (id: string | unknown) => boolean;
interface VuePluginApi {
  options: ResolvedOptions;
  version: string;
}
declare function getVuePluginApi(plugins: Readonly<(Plugin | Plugin$1)[]> | undefined): VuePluginApi | null;
declare enum FilterFileType {
  /** Vue SFC */
  VUE_SFC = 0,
  /** Vue SFC with `<script setup>` */
  VUE_SFC_WITH_SETUP = 1,
  /** foo.setup.tsx */
  SETUP_SFC = 2,
  /** Source files */
  SRC_FILE = 3,
}
declare function getFilterPattern(types: FilterFileType[], framework?: string): RegExp[];
declare function hackViteHMR(ctx: HmrContext, filter: (id: string | unknown) => boolean, callback: (code: string, id: string) => CodeTransform | undefined | Promise<CodeTransform | undefined>): void;
//#endregion
//#region src/options.d.ts
interface BaseOptions extends FilterOptions {
  version?: number;
  isProduction?: boolean;
}
//#endregion
//#region src/types.d.ts
type MarkRequired<T, K extends keyof T> = Omit<T, K> & Required<Pick<T, K>>;
type Overwrite<T, U> = Pick<T, Exclude<keyof T, keyof U>> & U;
type RecordToUnion<T extends Record<string, any>> = T[keyof T];
type UnionToIntersection<U> = (U extends unknown ? (arg: U) => 0 : never) extends ((arg: infer I) => 0) ? I : never;
//#endregion
//#region src/vue.d.ts
type SFCScriptBlock = Omit<SFCScriptBlock$1, "scriptAst" | "scriptSetupAst">;
type SFC = Omit<SFCDescriptor, "script" | "scriptSetup"> & {
  sfc: SFCParseResult;
  script?: SFCScriptBlock | null;
  scriptSetup?: SFCScriptBlock | null;
  lang: string | undefined;
  getScriptAst: () => Program | undefined;
  getSetupAst: () => Program | undefined;
  offset: number;
} & Pick<SFCParseResult, "errors">;
declare function parseSFC(code: string, id: string): SFC;
declare function getFileCodeAndLang(code: string, id: string): {
  code: string;
  lang: string;
};
declare function addNormalScript({
  script,
  lang
}: SFC, s: MagicString): {
  start(): number;
  end(): void;
};
declare function removeMacroImport(node: Node, s: MagicStringAST, offset: number): true | undefined;
//#endregion
export { BaseOptions, DEFINE_EMIT, DEFINE_EMITS, DEFINE_MODELS, DEFINE_MODELS_DOLLAR, DEFINE_OPTIONS, DEFINE_PROP, DEFINE_PROPS, DEFINE_PROPS_DOLLAR, DEFINE_PROPS_REFS, DEFINE_PROP_DOLLAR, DEFINE_RENDER, DEFINE_SETUP_COMPONENT, DEFINE_SLOTS, DEFINE_STYLEX, FilterFileType, FilterOptions, type FilterPattern, HELPER_PREFIX, MarkRequired, Overwrite, REGEX_NODE_MODULES, REGEX_SETUP_SFC, REGEX_SETUP_SFC_SUB, REGEX_SRC_FILE, REGEX_SUPPORTED_EXT, REGEX_VUE_SFC, REGEX_VUE_SUB, REGEX_VUE_SUB_SETUP, REPO_ISSUE_URL, RecordToUnion, SFC, SFCScriptBlock, TransformError, UnionToIntersection, VIRTUAL_ID_PREFIX, VuePluginApi, WITH_DEFAULTS, addNormalScript, checkInvalidScopeReference, createFilter, createRollupFilter, detectVueVersion, getFileCodeAndLang, getFilterPattern, getVuePluginApi, hackViteHMR, importHelperFn, isStaticExpression, isStaticObjectKey, normalizePath, parseSFC, removeMacroImport, resolveObjectExpression };