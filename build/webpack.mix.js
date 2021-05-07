const mix = require('laravel-mix');
const ESLintPlugin = require('eslint-webpack-plugin');

const { compiled, src } = require('./helpers');
const { config: { browserSync, css, js }, paths } = require('./config');

const postCssPlugins = [
	require('postcss-import'),
	require('tailwindcss')('./build/tailwind.config.js'),
	require('postcss-nested'),
];

if (js.lint) {
	mix.webpackConfig((webpack) => {
		return {
			plugins: [
				new ESLintPlugin({
					extensions: ['js', 'vue'],
					overrideConfigFile: './build/.eslintrc',
				}),
			],
		};
	});
}

// Typical setup
mix
	.alias({
		'assets': __dirname + '/../resources/assets',
	})
	.options({
		fileLoaderDirs: {
			fonts: `${paths.compiled}/fonts`,
			images: `${paths.compiled}/img`,
		},
		processCssUrls: false,
		postCss: postCssPlugins,
		clearConsole: !(process.env.NO_CLI_FLUSH),
	})
	.browserSync(browserSync)
	.setPublicPath(paths.dest);

css.files.forEach(filename => mix.postCss(src(`css/${filename}`), compiled('css')));
js.files.forEach(filename => mix.js(src(`js/${filename}`), compiled('js')).vue({ version: 2 }));

// Uncomment if you want to separate vendor files.
// mix.extract(js.extract);

if (mix.inProduction()) {
	mix.version();
} else {
	mix.sourceMaps(false, 'eval-cheap-source-map');
}
