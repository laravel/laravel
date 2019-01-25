const AutomaticComponent = require('laravel-mix/src/components/AutomaticComponent');

const fs = require('fs');
const path = require('path');

const exists = (file) => {
    try {
        fs.accessSync(file, fs.constants.F_OK);
        return true;
    } catch (e) {
        return false;
    }
};

const buildSassValue = (value) => {
    if (Array.isArray(value)) {
        return `(${value.reduce((prev, cur) => prev + `"${cur}",`, '')})`;
    }

    if (typeof value === "object") {
        return `(${buildSassMap(value)})`;
    }

    return `${value}`;
};

const buildSassMap = (json) => {
    return Object.keys(json).reduce((prev, cur) => {
        return prev + `"${cur}": ${buildSassValue(json[cur])},`;
    }, '');
};

const end = (done) => (value) => {
    return done ? done(value) : value;
};

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
                        implementation: require('sass'),
                        importer(url, prev, done) {
                            done = end(done);

                            if (!/\.json$/.test(url)) {
                                return done(null);
                            }

                            const parts = url.split('/');
                            const name = path.basename(parts.pop(), '.json');
                            const cwd = process.cwd();

                            const includePaths = this.options.includePaths
                                .split(':')
                                .map(p => path.resolve(cwd, p));

                            try {
                                let resolved = path.join(cwd, url);
                                if (exists(resolved)) {
                                    var json = require(resolved);
                                } else {
                                    for (let i = 0; i < includePaths.length; i++ ) {
                                        resolved = path.join(includePaths[i], url);

                                        if (exists(resolved)) {
                                            var json = require(resolved);
                                        }
                                    }
                                }
                            } catch(err) {
                                return done(err);
                            }

                            if (json) {
                                return done({ contents: `$${name}: (${buildSassMap(json)});` });
                            }

                            return done(null);
                        },
                    },
                },
            ],
        };
    }
};
