const importer = require('node-sass-json-importer');
const mix = require('laravel-mix');

require('@ayctor/laravel-mix-svg-sprite');

const { assets, src } = require('./helpers');
const { config: { browserSync, css, js }, paths } = require('./config');

// Load the multi-lingual support
mix.extend('i18n', new (require('./laravel-mix-i18n'))());

mix
	.options({
		autoprefixer: css.autoprefixer,
		cleanCss: css.cleanCss,
		clearConsole: false,
		fileLoaderDirs: {
			fonts: `${paths.assets}/fonts`,
			images: `${paths.assets}/img`,
		},
	})
	.webpackConfig({
		module: {
			rules: [
				{
					test: /\.scss$/,
					use: [
						{
							loader: 'sass-loader',
							options: {
								importer,
							},
						},
					],
				},
			],
		},
	})
	.browserSync(browserSync)
	.setPublicPath(paths.dest)
	.svgSprite({
		src: src('sprite/**/*.svg'),
		filename: assets('img/sprite.svg'),
		svg4everyone: true,
	})
	.copyDirectory(src('static'), assets('static'));

css.files.forEach(filename => mix.sass(src(`scss/${filename}`), assets('css')));
js.files.forEach(filename => mix.js(src(`js/${filename}`), assets('js')));

// Uncomment if you want to separate vendor files.
// mix.extract(js.extract);
