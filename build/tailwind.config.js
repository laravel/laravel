const { src } = require('./helpers');
const variables = require('../resources/assets/variables.json');

// converters and calculators
const relative = (px, unit = 'rem', base = variables['browser-default-font-size']) => `${px / base}${unit}`;
const letterSpacing = value => `${value / 1000}em`;
const ratio = (x, y) => `${y / x * 100}%`;

// tailwind settings
module.exports = {
	mode: 'jit',
	purge: {
		content: [
			src('../views/**/*.blade.php'),
			src('js/**/*.{js,vue}'),
		],
	},
	theme: {
		screens: Object.fromEntries(
			Object.entries(variables.breakpoints).map(([name, px]) => [name, relative(px, 'em')])
		),
		colors: {
			transparent: 'transparent',
			current: 'currentColor',
			inherit: 'inherit',
			black: '#000',
			white: '#fff',
			grey: {
				50: '#fafafa',
				100: '#eee',
				200: '#ddd',
				300: '#ccc',
				400: '#aaa',
				500: '#888',
				600: '#666',
				700: '#333',
				800: '#222',
				900: '#111',
			},
			blue: '#3b82f6',
			green: '#22c55e',
			red: '#ef4444',
			social: {
				twitter: '#55acee',
				facebook: '#3b5998',
				youtube: '#bb0000',
				pinterest: '#cb2027',
				linkedin: '#007bb5',
				instagram: '#8a3ab9',
			},
		},
		fontFamily: {
			body: ['custom-body', 'Helvetica', 'sans-serif'],
			heading: ['custom-heading', 'Georgia', 'serif'],
			system: ['system-ui', 'sans-serif'],
		},
		fontSize: (theme) => ({
			full: '100%',
			xs: [relative(12), theme('lineHeight.normal')],
			sm: [relative(14), theme('lineHeight.normal')],
			base: [relative(16), theme('lineHeight.normal')],
			lg: [relative(18), theme('lineHeight.normal')],
			xl: [relative(20), theme('lineHeight.normal')],
			'2xl': [relative(24), theme('lineHeight.snug')],
			'3xl': [relative(30), theme('lineHeight.snug')],
			'4xl': [relative(36), theme('lineHeight.tight')],
			'5xl': [relative(48), theme('lineHeight.extra-tight')],
			'6xl': [relative(60), theme('lineHeight.none')],
			'7xl': [relative(72), theme('lineHeight.none')],
			'8xl': [relative(96), theme('lineHeight.none')],
			'9xl': [relative(128), theme('lineHeight.none')],
		}),
		letterSpacing: {
			normal: 0,
			wide: letterSpacing(50),
		},
		lineHeight: {
			none: 1,
			'extra-tight': 1.1,
			tight: 1.25,
			snug: 1.375,
			normal: 1.5,
			relaxed: 1.625,
			loose: 2,
		},
		extend: {
			inset: (theme, { negative }) => ({
				...theme('width'),
				...(negative(theme('width'))),
			}),
			maxWidth: {
				container: relative(1440),
			},
			padding: {
				full: '100%',
				'9/16': ratio(16, 9),
			},
			spacing: {
				em: '1em',
				'1/2em': '.5em',
			},
			transitionTimingFunction: Object.fromEntries(
				Object.entries(variables.easing).map(([name, v]) => [name, `cubic-bezier(${v.join(', ')})`])
			),
			width: Object.fromEntries([...Array(variables.columns).keys()].map(v => [
				`${v + 1}/${variables.columns}`,
				`${(v + 1) / variables.columns * 100}%`
			])),
			zIndex: {
				'-1': -1,
				1: 1,
				2: 2,
			},
		},
	},
	variants: {},
	corePlugins: {
		container: false,
	},
};
