import debounce from 'lodash/debounce';
import variables from '../../variables.json';

const breakpoints = {};

// TODO: convert to Object.fromEntries
Object.keys(variables.breakpoints).forEach((name) => {
	let value = variables.breakpoints[name];

	if (variables['em-media-queries']) {
		value /= variables['browser-default-font-size'] || 16;
	}

	breakpoints[name] = value;
});

export default {
	created() {
		this.onDebouncedResize = debounce(this.onResize, 300);
	},

	mounted() {
		this.onResize();

		window.addEventListener('resize', this.onDebouncedResize);
	},

	methods: {
		onResize() {},

		mq(name, extremum = 'min', property = 'width') {
			if (!window.matchMedia) {
				return false;
			}

			let value = breakpoints[name];

			if (!value) {
				throw new Error(`Unkown breakpoint: ${name} is not defined`);
			}

			if (extremum === 'max') {
				value -= variables['em-media-queries'] ? 0.01 : 1;
			}

			const unit = variables['em-media-queries'] ? 'em' : 'px';

			return window.matchMedia(`only screen and (${extremum}-${property}: ${value}${unit})`).matches;
		},
	},
};
