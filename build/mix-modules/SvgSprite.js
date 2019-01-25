const mix = require('laravel-mix');

class SvgSprite {

    /**
     * All dependencies that should be installed by Mix.
     *
     * @return {Array}
     */
    dependencies() {
        return ['svg-spritemap-webpack-plugin'];
    }

    /**
     * Register the component.
     *
     */
    register(...args) {
        this.arguments = args;
    }

    /*
     * Plugins to be merged with the master webpack config.
     *
     * @return {Array|Object}
     */
    webpackPlugins() {
        let SVGSpritemapPlugin = require('svg-spritemap-webpack-plugin');

        return new SVGSpritemapPlugin(...this.arguments);
    }
}

mix.extend('svgSprite', new SvgSprite());
