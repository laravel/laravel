import variables from '../../variables.json';

let width = 0,
	height = 0,
	prevWidth,
	prevHeight,
	timer = null;

const breakpoints = {},
	listeners = {},
	update = () => {
		prevWidth = width;
		prevHeight = height;

		width = window.innerWidth;
		height = window.innerHeight;

		Object.keys(listeners).forEach((name) => {
			listeners[name](prevWidth !== width, prevHeight !== height);
		});
	};

window.addEventListener('resize', () => {
	clearTimeout(timer);

	timer = setTimeout(update, 300);
});

update();

Object.keys(variables.breakpoints).forEach((name) => {
	let value = variables.breakpoints[name];

	if (variables['em-media-queries']) {
		value /= variables['browser-default-font-size'] || 16;
	}

	breakpoints[name] = value;
});

export const getWidth = () => width;

export const getHeight = () => height;

export const addListener = (name, listener) => {
	listeners[name] = listener;
};

export const removeListener = (name) => {
	delete listeners[name];
};

export const mq = (name, extremum = 'min', property = 'width') => {
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
};
