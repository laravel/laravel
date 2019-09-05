const forEach = require('lodash/forEach');
const variables = require('../resources/assets/variables.json');

// converters and calculators
const relative = (px, unit = 'rem', base = variables['browser-default-font-size']) => `${px / base}${unit}`;
const letterSpacing = value => `${value / 1000}em`;

// values
const colors = {
	inherit: 'inherit',
	transparent: 'transparent',
	brand: {
		red: '#ff585d',
	},
	black: '#000',
	grey: {
		100: '#fafafa',
		200: '#eee',
		300: '#ddd',
		400: '#ddd',
		500: '#aaa',
		600: '#888',
		700: '#444',
		800: '#222',
		900: '#111',
	},
	white: '#fff',
	blue: '#00f',
	green: '#24b35d',
	red: '#f50023',
	social: {
		twitter: '#55acee',
		facebook: '#3b5998',
		youtube: '#bb0000',
		pinterest: '#cb2027',
		linkedin: '#007bb5',
		instagram: '#8a3ab9',
	},
};

const screens = {};

forEach(variables.breakpoints, (px, key) => {
	screens[key] = relative(px, 'em');
});

const widths = {};

for (let column = 1; column <= variables.columns; column++) {
	widths[`${column}/${variables.columns}`] = `${column / variables.columns * 100}%`;
}

const zIndexes = {};

for (let index = 0; index < variables['z-indexes'].length; index++) {
	zIndexes[variables['z-indexes'][index]] = variables['z-indexes'].length - index;
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
		borderColor: theme => ({
			red: theme('colors.red'),
			grey: {
				'500': theme('colors.grey.500'),
				'700': theme('colors.grey.700'),
			},
		}),
		borderRadius: {
			none: 0,
			'50': '50%',
		},
		borderWidth: {
			default: '1px',
			'0': '0',
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
			xs: 1.1,
			sm: 1.1,
			normal: 1.5,
			lg: 1.75,
			xl: 2,
		},
		maxWidth: {
			container: relative(1400),
			containerWithGutters: relative(1464),
			copy: '35em',
		},
		zIndex: zIndexes,
		extend: {
			inset: {
				'1/2': '50%',
			},
			spacing: {
				em: '1em',
				'half-em': '.5em',
			},
			width: widths,
		},
	},
	variants: {},
	corePlugins: {
		container: false,
	},
};
