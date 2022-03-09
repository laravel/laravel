const mix = require('laravel-mix');
const path = require('path');

const { compiled, src } = require('./helpers');
const { config: { browserSync }, paths } = require('./config');

require('laravel-mix-eslint');

mix
	.alias({
		'assets': __dirname + '/../resources/assets',
		'@': path.join(__dirname, `../${paths.src}`),
	})
	.webpackConfig((webpack) => {
		return {
			plugins: [
				new webpack.DefinePlugin({
					'__VUE_OPTIONS_API__': JSON.stringify(true),
					'__VUE_PROD_DEVTOOLS__': JSON.stringify(false),
				}),
			],
		};
	})
	.options({
		autoprefixer: {
			options: {
				remove: false,
			},
		},
		fileLoaderDirs: {
			fonts: `${paths.compiled}/fonts`,
			images: `${paths.compiled}/img`,
		},
		processCssUrls: false,
		clearConsole: !(process.env.NO_CLI_FLUSH),
	})
	.js(src('js/app.js'), compiled('js'))
	.vue()
	.eslint({
		extensions: ['js', 'vue'],
		overrideConfigFile: './build/.eslintrc',
	})
	.postCss(src('css/app.css'), compiled('css'), [
		require('postcss-import'),
		require('tailwindcss')('./build/tailwind.config.js'),
		require('postcss-nested'),
	])
	.browserSync(browserSync)
	.setPublicPath(paths.dest);

if (mix.inProduction()) {
	mix.version();
} else {
	mix.sourceMaps(false, 'eval-cheap-source-map');
}
