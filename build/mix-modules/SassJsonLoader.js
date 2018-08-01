const importer = require('node-sass-json-importer');
const AutomaticComponent = require('laravel-mix/src/components/AutomaticComponent');

module.exports = class SassJsonLoader extends AutomaticComponent {
    /**
     * Rules to be merged with the master webpack loaders.
     *
     * @return {Array|Object}
     */
    webpackRules() {
        return {
            test: /\.scss$/,
            use: [
                {
                    loader: 'sass-loader',
                    options: {
                        importer,
                    },
                },
            ],
        };
    }
};
