const variables = require('../variables.json');

module.exports = {
	letterSpacing: value => `${value / 1000}em`,
	ratio: (x, y) => `${y / x * 100}%`,
	relative: (px, unit = 'rem', base = variables['browser-default-font-size']) =>
		`${px / base}${unit}`,
	withOpacity: (variableName) => {
		return ({ opacityValue }) => {
			if (opacityValue !== undefined) {
				return `rgba(var(${variableName}), ${opacityValue})`;
			}

			return `rgba(var(${variableName}))`;
		};
	},
	variables,
};
