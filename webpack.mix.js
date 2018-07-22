const mix = require('laravel-mix');

// webpack.mix.js
mix.webpackConfig({
	module: {
		rules: [
			{
				// Matches all PHP or JSON files in `resources/lang` directory.
				test: /resources\/lang.+\.php$/,
				loader: 'laravel-localization-loader',
			}
		]
	}
});

mix
	.js('resources/assets/js/app.js', 'public/assets/js')
	.sass('resources/assets/sass/app.scss', 'public/assets/css');
