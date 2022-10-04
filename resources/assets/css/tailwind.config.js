const { src } = require('../../../build/helpers');
const { relative, variables } = require('./helpers');

// tailwind settings
module.exports = {
	content: [
		src('../views/**/*.blade.php'),
		src('js/**/*.{js,vue}'),
	],
	theme: {
		screens: Object.fromEntries(
			Object.entries(variables.breakpoints).map(([name, px]) => [name, relative(px, 'em')])
		),
		colors: {
			current: 'currentColor',
			inherit: 'inherit',
			transparent: 'transparent',
			black: '#000',
			white: '#fff',
			grey: {
				50: '#fafafa',
				100: '#f5f5f5',
				200: '#e5e5e5',
				300: '#d4d4d4',
				400: '#a3a3a3',
				500: '#737373',
				600: '#525252',
				700: '#404040',
				800: '#262626',
				900: '#171717',
			},
			green: '#22c55e',
			red: '#ef4444',
			focus: '#3b82f6',
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
			aspectRatio: {
				'16/9': '16 / 9',
			},
			inset: (theme, { negative }) => ({
				...theme('width'),
				...(negative(theme('width'))),
			}),
			maxWidth: {
				container: relative(1440),
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
