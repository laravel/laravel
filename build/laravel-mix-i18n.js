module.exports = class I18n {
    constructor() {
        this.passive = true;
    }

    /**
     * Rules to be merged with the master webpack loaders.
     *
     * @return {Array|Object}
     */
    webpackRules() {
        return {
            // Matches all PHP or JSON files in `resources/lang` directory.
            test: /resources\/lang.+\.php$/,
            loaders: [ 'laravel-localization-loader' ],
        };
    }
};
