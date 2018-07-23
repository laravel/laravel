function requireAll(r) {
	return r.keys().reduce(function (acc, filename) {
		const key = filename
			.replace(/^\.\//, '')
			.replace(/\.php$/, '')
			.replace(/\//g, '.');

		acc[key] = r(filename);

		return acc;
	}, {});
};

module.exports = requireAll(require.context('../../lang/', true, /\.php$/));
