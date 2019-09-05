const mix = require('laravel-mix');
const ComponentFactory = require('laravel-mix/src/components/ComponentFactory');

const { compiled, src } = require('./helpers');
const { config: { browserSync, css, js }, paths } = require('./config');

const postCssPlugins = [
    require('postcss-easy-import')(),
	require('postcss-mixins')({
		mixins: {
			ratio: (mixin, ratioA, ratioB) => {
				return {
					'&': {
						paddingTop: `${ratioB / ratioA * 100}%`,
					},
				};
			},
		},
	}),
	require('postcss-units')(),
	require('tailwindcss')('./build/tailwind.config.js'),
	require('postcss-nested')(),
    require('postcss-extend-rule')(),
];

if (mix.inProduction() || 1 === 1) {
	postCssPlugins.push(require('@fullhuman/postcss-purgecss')({
		content: [
			src('../views/**/!(styleguide).blade.php'),
			src('js/components/**/*.vue'),
		],
		// https://medium.com/@kyis/vue-tailwind-purgecss-the-right-way-c70d04461475
		defaultExtractor: content => content.match(/[A-Za-z0-9-_/:]*[A-Za-z0-9-_/]+/g) || [],
	}));
}

// Load the multi-lingual support
new ComponentFactory().install(require('./mix-modules/I18n'));

// Allow JSON files to be loaded in Sass
// new ComponentFactory().install(require('./mix-modules/SassJsonLoader'));

// Svg combinating
new ComponentFactory().install(require('./mix-modules/SvgSprite'));

if (js.lint) {
	// Load JavaScript linter support
	new ComponentFactory().install(require('./mix-modules/ESLintLoader'));
}

// Typical setup
mix
	.options({
		autoprefixer: css.autoprefixer,
		cleanCss: css.cleanCss,
		fileLoaderDirs: {
			fonts: `${paths.compiled}/fonts`,
			images: `${paths.compiled}/img`,
		},
		processCssUrls: false,
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

css.files.forEach(filename => mix.postCss(src(`css/${filename}`), compiled('css'), postCssPlugins));
js.files.forEach(filename => mix.js(src(`js/${filename}`), compiled('js')));

// Uncomment if you want to separate vendor files.
// mix.extract(js.extract);

if (mix.inProduction()) {
	mix.version();
} else {
	mix.sourceMaps(false, 'cheap-eval-source-map');
}
