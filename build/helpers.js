const { paths } = require('./config');

module.exports = {
	public: filename => [paths.dest, filename].join('/'),
	compiled: filename => [paths.compiled, filename].join('/'),
	src: filename => [paths.src, filename].join('/'),
};
