/*!
 * vue-router v5.2.0
 * (c) 2026 Eduardo San Martin Morote
 * @license MIT
 */
import { C as createTreeNodeValue, S as TreeNodeValueStatic, _ as getPascalCaseRouteName, a as ResolvedOptions, b as TreeNodeValueGroup, c as RoutesFolderOptionResolved, g as getFileBasedRouteName, h as EditableTreeNode, i as ParamParsersOptions, l as ServerContext, o as RoutesFolder, p as resolveOptions, r as Options, s as RoutesFolderOption, t as DEFAULT_OPTIONS, v as TreeNode, x as TreeNodeValueParam, y as TreeNodeValue } from "../options-P-0BPDru.mjs";
import { StringFilter } from "unplugin";
import { Plugin } from "vite";
//#region src/unplugin/core/context.d.ts
declare function createRoutesContext(options: ResolvedOptions): {
  scanPages: (startWatchers?: boolean) => Promise<void>;
  writeConfigFiles: () => void;
  setServerContext: (_server: ServerContext) => void;
  stopWatcher: () => void;
  generateRoutes: () => string;
  generateResolver: () => string;
  definePageTransform(code: string, id: string): import("unplugin").Thenable<import("unplugin").TransformResult>;
};
//#endregion
//#region src/experimental/data-loaders/auto-exports.d.ts
/**
 * {@link AutoExportLoaders} options.
 */
interface AutoExportLoadersOptions {
  /**
   * Filter page components to apply the auto-export. Passed to `transform.filter.id`.
   */
  transformFilter: StringFilter;
  /**
   * Globs to match the paths of the loaders.
   */
  loadersPathsGlobs: string | string[];
  /**
   * Root of the project. All paths are resolved relatively to this one.
   * @default `process.cwd()`
   */
  root?: string;
}
/**
 * Vite Plugin to automatically export loaders from page components.
 *
 * @param options Options
 * @experimental - This API is experimental and can be changed in the future. It's used internally by `experimental.autoExportsDataLoaders`
 */
declare function AutoExportLoaders({ transformFilter, loadersPathsGlobs, root }: AutoExportLoadersOptions): Plugin;
//#endregion
//#region src/unplugin/index.d.ts
declare const _default: import("unplugin").UnpluginInstance<Options | undefined, boolean>;
/**
 * Adds useful auto imports to the AutoImport config:
 * @example
 * ```js
 * import { VueRouterAutoImports } from 'vue-router/unplugin'
 *
 * AutoImport({
 *   imports: [VueRouterAutoImports],
 * }),
 * ```
 */
declare const VueRouterAutoImports: Record<string, Array<string | [importName: string, alias: string]>>;
//#endregion
export { AutoExportLoaders, type AutoExportLoadersOptions, DEFAULT_OPTIONS, EditableTreeNode, type Options, type ParamParsersOptions, type ResolvedOptions, type RoutesFolder, type RoutesFolderOption, type RoutesFolderOptionResolved, type TreeNode, type TreeNodeValue, type TreeNodeValueGroup, type TreeNodeValueParam, type TreeNodeValueStatic, VueRouterAutoImports, createRoutesContext, createTreeNodeValue, _default as default, getFileBasedRouteName, getPascalCaseRouteName, resolveOptions };