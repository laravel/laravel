const mix = require('laravel-mix');
const ComponentFactory = require('laravel-mix/src/components/ComponentFactory');

const { compiled, src } = require('./helpers');
const { config: { browserSync, css, js }, paths } = require('./config');

const postCssPlugins = [
	require('postcss-units')(),
	require('tailwindcss')('./build/tailwind.config.js'),
];

if (mix.inProduction()) {
	postCssPlugins.push(require('@fullhuman/postcss-purgecss')({
		content: [
			src('../views/**/!(styleguide).blade.php'),
			src('js/components/**/*.vue'),
		],
		// https://medium.com/@kyis/vue-tailwind-purgecss-the-right-way-c70d04461475
		defaultExtractor: content => content.match(/[A-Za-z0-9-_/:]*[A-Za-z0-9-_/]+/g) || [],
		whitelist: css.purgeCssWhitelist,
		whitelistPatterns: css.purgeCssWhitelistPatterns.map(element => new RegExp(element)),
	}));
}

// Load the multi-lingual support
new ComponentFactory().install(require('./mix-modules/I18n'));

// Svg combinating
new ComponentFactory().install(require('./mix-modules/SvgSprite'));

if (js.lint) {
	// Load JavaScript linter support
	new ComponentFactory().install(require('./mix-modules/ESLintLoader'));
}

// Typical setup
mix
	.options({
		autoprefixer: {
			options: css.autoprefixer,
			enabled: true,
		},
		cleanCss: css.cleanCss,
		fileLoaderDirs: {
			fonts: `${paths.compiled}/fonts`,
			images: `${paths.compiled}/img`,
		},
		processCssUrls: false,
		postCss: postCssPlugins,
		clearConsole: !(process.env.NO_CLI_FLUSH),
	})
	.browserSync(browserSync)
	.setPublicPath(paths.dest)
	.svgSprite(src('sprite/**/*.svg'), {
		output: {
			filename: compiled('img/sprite.svg'),
		},
		sprite: {
			prefix: false,
		},
	});

css.files.forEach(filename => mix.sass(src(`scss/${filename}`), compiled('css')));
js.files.forEach(filename => mix.js(src(`js/${filename}`), compiled('js')));

// Uncomment if you want to separate vendor files.
// mix.extract(js.extract);

if (mix.inProduction()) {
	mix.version();
} else {
	mix.sourceMaps(false, 'cheap-eval-source-map');
}
