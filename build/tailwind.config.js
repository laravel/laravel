const forEach = require('lodash/forEach');
const variables = require('../resources/assets/variables.json');

// converters and calculators
const relative = (px, unit = 'rem', base = variables['browser-default-font-size']) => `${px / base}${unit}`;
const letterSpacing = value => `${value / 1000}em`;
const ratio = (x, y) => `${y / x * 100}%`;

// values
const colors = variables['colors'];

const easing = {};

forEach(variables.easing, (value, key) => {
	easing[key] = `cubic-bezier(${value[0]}, ${value[1]}, ${value[2]}, ${value[3]})`;
});

const screens = {};

forEach(variables.breakpoints, (px, key) => {
	screens[key] = relative(px, 'em');
});

const widths = {};

for (let column = 1; column <= variables.columns; column++) {
	widths[`${column}/${variables.columns}`] = `${column / variables.columns * 100}%`;
}

const zIndex = {};

for (let index = 0; index < variables['z-indexes'].length; index++) {
	zIndex[variables['z-indexes'][index]] = variables['z-indexes'].length - index;
}

// tailwind settings
module.exports = {
	theme: {
		screens,
		colors,
		fontFamily: {
			body: ['custom-body', 'Helvetica', 'sans-serif'],
			heading: ['custom-heading', 'Georgia', 'serif'],
			system: ['system-ui', 'sans-serif'],
		},
		boxShadow: {
			none: 'none',
			outline: `0px 0px 3px ${colors.blue}, 0px 0px 6px ${colors.blue}, 0px 0px 9px ${colors.blue}`,
		},
		fontSize: {
			xs: relative(12),
			sm: relative(14),
			base: relative(16),
			lg: relative(18),
			xl: relative(20),
			'2xl': relative(22),
			'3xl': relative(26),
			'4xl': relative(30),
			'5xl': relative(36),
			'6xl': relative(44),
			'100': '100%',
		},
		fontWeight: {
			normal: 400,
			bold: 700,
		},
		letterSpacing: {
			normal: 0,
			wide: letterSpacing(50),
		},
		lineHeight: {
			none: 1,
			tight: 1.1,
			snug: 1.2,
			normal: 1.5,
			relaxed: 1.75,
			loose: 2,
		},
		maxWidth: {
			container: relative(1400),
			copy: '35em',
		},
		transitionTimingFunction: easing,
		zIndex,
		extend: {
			borderRadius: {
				'50': '50%',
			},
			inset: (theme, { negative }) => ({
				'1/2': '50%',
				...widths,
				...(negative(widths)),
			}),
			padding: {
				full: '100%',
				logo: ratio(300, 87),
				'9/16': ratio(16, 9),
				'3/4': ratio(4, 3),
				'4/3': ratio(3, 4),
			},
			spacing: {
				em: '1em',
				'1/2em': '.5em',
			},
			width: {
				...widths,
			},
		},
	},
	variants: {},
	plugins: [
		require('tailwindcss-transitions')(),
	],
	corePlugins: {
		container: false,
	},
};
