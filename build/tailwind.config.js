const variables = require('../resources/assets/variables.json');

// const breakpoints = [
// 	{
// 		name: 'phone',
// 	},
// 	{
// 		name: 'phone-wide',
// 	},
// 	{
// 		name: 'phablet',
// 	},
// 	{
// 		name: 'tablet-small',
// 	},
// 	{
// 		name: 'tablet',
// 	},
// 	{
// 		name: 'tablet-wide',
// 	},
// 	{
// 		name: 'desktop',
// 	},
// 	{
// 		name: 'desktop-wide',
// 	},
// ];

const colors = variables.colors;

const toRem = px => variables['rems-on'] ? `${px / variables['browser-default-font-size']}rem` : `${px}`;

const toEm = px => variables['em-media-queries'] ? `${px / variables['browser-default-font-size']}em` : `${px}px`;

const screens = {};

// breakpoints.forEach(({ name }) => {
// 	screens[name] = toEm(variables.breakpoints[name]);
// });

for (breakpoint in variables.breakpoints) {
	screens[breakpoint] = toEm(variables.breakpoints[breakpoint]);
}

const columnWidths = {};

for (let column = 1; column <= variables.columns; column++) {
	columnWidths[`${column}/${variables.columns}`] = `${column / variables.columns * 100}%`;
}

const zIndices = {};

for (let index = 0; index < variables.zIndex.length; index++) {
	zIndices[variables.zIndex[index]] = variables.zIndex.length - index;
}

module.exports = {
	// prefix: 'tw-',
	theme: {
		screens,
		colors,
		// colors: {
		// 	inherit: 'inherit',
		// 	'brand-red': '#ff585d',
		// 	black: '#555',
		// 	white: '#fff',
		// },
		// fontFamily: {
		// 	'cortec-body': ['e-din-next', 'sans-serif'],
		// 	'cortec-heading': ['e-league-gothic', 'sans-serif'],
		// },
		boxShadow: {
			outline: `0px 0px 3px ${colors.blue}, 0px 0px 6px ${colors.blue}, 0px 0px 9px ${colors.blue}`,
		},
		fontSize: {
			xs: toRem(12),
			sm: toRem(14),
			base: toRem(16),
			lg: toRem(18),
			xl: toRem(20),
			'2xl': toRem(22),
			'3xl': toRem(26),
			'4xl': toRem(30),
			'5xl': toRem(36),
			'6xl': toRem(44),
		},
		// fontWeight: {
		// 	light: 300,
		// 	bold: 700,
		// },
		// letterSpacing: {
		// 	wide: '0.05em',
		// 	widest: '0.15em',
		// },
		lineHeight: {
			none: 1,
			xs: 1.1,
			sm: 1.1,
			normal: 1.5,
			lg: 1.75,
			xl: 2,
		},
		// opacity: {
		// 	'20': .2,
		// 	'30': .3,
		// 	'40': .4,
		// },
		zIndex: zIndices,
		extend: {
			// height: {
			// 	// carousel: '50rem',
			// 	// heading: '.7375em',
			// },
			// maxWidth: {
			// 	// container: '100rem',
			// 	// logo: '30rem',
			// },
			// padding: {
			// 	tooltip: '.785em',
			// },
			// screens: {
			// 	tall: {
			// 		'raw': `(min-width: ${tall.width}) and (min-height: ${tall.height})`,
			// 	},
			// },
			inset: {
				'4': toRem(4),
			},
			spacing: {
				em: '1em',
				'half-em': '.5em',
			},
			width: columnWidths,
			// width: {
			// 	caret: '.5556em',
			// 	'2px': '2px',
			// },
		},
	},
	// variants: {
	// 	alignItems: ['responsive'],
	// 	backgroundAttachment: [],
	// 	backgroundColor: [],
	// 	backgroundPosition: [],
	// 	backgroundRepeat: [],
	// 	backgroundSize: [],
	// 	borderRadius: [],
	// 	display: ['responsive'],
	// 	fill: [],
	// 	flex: ['responsive'],
	// 	flexDirection: ['responsive'],
	// 	fontFamily: [],
	// 	fontSize: ['responsive'],
	// 	fontSmoothing: [],
	// 	fontStyle: [],
	// 	fontWeight: [],
	// 	height: ['responsive'],
	// 	inset: ['responsive'],
	// 	justifyContent: ['responsive'],
	// 	letterSpacing: [],
	// 	lineHeight: [],
	// 	margin: ['responsive'],
	// 	maxHeight: ['responsive'],
	// 	maxWidth: ['responsive'],
	// 	minHeight: ['responsive'],
	// 	minWidth: ['responsive'],
	// 	objectFit: [],
	// 	opacity: [],
	// 	overflow: ['responsive'],
	// 	padding: ['responsive'],
	// 	pointerEvents: ['responsive'],
	// 	position: ['responsive'],
	// 	stroke: [],
	// 	textAlign: ['responsive'],
	// 	textColor: [],
	// 	textTransform: [],
	// 	userSelect: ['responsive'],
	// 	verticalAlign: ['responsive'],
	// 	whitespace: ['responsive'],
	// 	width: ['responsive'],
	// 	wordBreak: [],
	// },
	// corePlugins: {
	// 	alignContent: false,
	// 	alignSelf: false,
	// 	appearance: false,
	// 	borderCollapse: false,
	// 	borderColor: false,
	// 	borderStyle: false,
	// 	borderWidth: false,
	// 	boxShadow: false,
	// 	container: false,
	// 	cursor: false,
	// 	flexGrow: false,
	// 	flexShrink: false,
	// 	flexWrap: false,
	// 	float: false,
	// 	listStylePosition: false,
	// 	listStyleType: false,
	// 	objectPosition: false,
	// 	order: false,
	// 	outline: false,
	// 	preflight: false,
	// 	resize: false,
	// 	tableLayout: false,
	// 	textDecoration: false,
	// 	visibility: false,
	// 	zIndex: false,
	// },
};
