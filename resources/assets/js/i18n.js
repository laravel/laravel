import Lang from 'lang.js';

const contexts = require.context('../../lang/', true, /\.php$/);

const messages = contexts.keys().reduce((acc, filename) => {
	const key = filename
		.replace(/^\.\//, '')
		.replace(/\.php$/, '')
		.replace(/\//g, '.');

	acc[key] = contexts(filename);

	return acc;
}, {});

export default new Lang({
	messages,
	locale: 'en',
	fallback: 'en',
});
