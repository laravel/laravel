export { P as PluginUtils } from './resolve-config-QUZ9b-Gn.mjs';
import { b as PluginFn, C as Config, c as PluginWithConfig, d as PluginWithOptions } from './types-DWdTiksJ.mjs';
export { a as PluginAPI, P as PluginsConfig, T as ThemeConfig } from './types-DWdTiksJ.mjs';
import './colors.mjs';

declare function createPlugin(handler: PluginFn, config?: Partial<Config>): PluginWithConfig;
declare namespace createPlugin {
    var withOptions: <T>(pluginFunction: (options?: T) => PluginFn, configFunction?: (options?: T) => Partial<Config>) => PluginWithOptions<T>;
}

export { Config, PluginFn as PluginCreator, PluginWithConfig, createPlugin as default };
