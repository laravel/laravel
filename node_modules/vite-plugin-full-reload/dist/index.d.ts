import { PluginOption } from 'vite';

/**
 * Configuration for the watched paths.
 */
interface Config {
    /**
     * Whether full reload should happen regardless of the file path.
     * @default true
     */
    always?: boolean;
    /**
     * How many milliseconds to wait before reloading the page after a file change.
     * @default 0
     */
    delay?: number;
    /**
     * Whether to log when a file change triggers a full reload.
     * @default true
     */
    log?: boolean;
    /**
     * Files will be resolved against this path.
     * @default process.cwd()
     */
    root?: string;
}
declare function normalizePaths(root: string, path: string | string[]): string[];
/**
 * Allows to automatically reload the page when a watched file changes.
 */
declare const _default: (paths: string | string[], config?: Config) => PluginOption;

export { Config, _default as default, normalizePaths };
