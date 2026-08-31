import { Plugin } from 'vite';

type PluginOptions = {
    /**
     * Optimize and minify the output CSS.
     */
    optimize?: boolean | {
        minify?: boolean;
    };
};
declare function tailwindcss(opts?: PluginOptions): Plugin[];

export { type PluginOptions, tailwindcss as default };
