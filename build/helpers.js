const { paths } = require('./config');

// node_modules/.bin/eslint -c build/.eslintrc build

module.exports = {
	public: filename => [paths.dest, filename].join('/'),
	assets: filename => [paths.assets, filename].join('/'),
	src: filename => [paths.src, filename].join('/'),
};
