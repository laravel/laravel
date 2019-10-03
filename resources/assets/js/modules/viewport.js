import {
	watchViewport,
} from 'tornis/dist/tornis.es5';
import variables from '../../variables.json';

let width = 0,
	height = 0;

const updateValues = ({
	size,
}) => {
	width = size.x;
	height = size.y;
};

const breakpoints = {};

watchViewport(updateValues);

Object.keys(variables.breakpoints).forEach((name) => {
	let value = variables.breakpoints[name];

	if (variables['em-media-queries']) {
		value /= variables['browser-default-font-size'] || 16;
	}

	breakpoints[name] = value;
});

export const getWidth = () => width;

export const getHeight = () => height;

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
