const { paths } = require('./config');

// node_modules/.bin/eslint -c build/.eslintrc build

module.exports = {
	public: filename => [paths.dest, filename].join('/'),
	compiled: filename => [paths.compiled, filename].join('/'),
	src: filename => [paths.src, filename].join('/'),
};
