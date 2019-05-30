const mix = require('laravel-mix');
const ComponentFactory = require('laravel-mix/src/components/ComponentFactory');

const { assets, public, src } = require('./helpers');
const { config: { browserSync, css, js }, paths } = require('./config');

// Load the multi-lingual support
new ComponentFactory().install(require('./mix-modules/I18n'));

// Allow JSON files to be loaded in Sass
new ComponentFactory().install(require('./mix-modules/SassJsonLoader'));

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
			fonts: `${paths.assets}/fonts`,
			images: `${paths.assets}/img`,
		},
		processCssUrls: false,
	})
	.browserSync(browserSync)
	.setPublicPath(paths.dest)
	.svgSprite(src('sprite/**/*.svg'), {
		output: {
			filename: assets('img/sprite.svg'),
		},
		sprite: {
			prefix: false,
		},
	});

css.files.forEach(filename => mix.sass(src(`scss/${filename}`), assets('css')));
js.files.forEach(filename => mix.js(src(`js/${filename}`), assets('js')));

// Uncomment if you want to separate vendor files.
// mix.extract(js.extract);

if (mix.inProduction()) {
	mix.version();
} else {
	mix.sourceMaps(false, 'cheap-eval-source-map');
}
