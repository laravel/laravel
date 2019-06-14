const config = {
	indent: '    ',
	maxAttrs: 2,
};

const bladeAttrs = (attrs, level) => {
	const isArray = Array.isArray(attrs);

	return Object.keys(attrs)
		.map((key) => {
			let value = attrs[key];

			let quote = typeof value === 'string' ? "'" : '';

			if (typeof value === 'object' && value !== null) {
				value = `[\n${bladeAttrs(value, level + 1)}\n${config.indent.repeat(level)}]`;
				quote = '';
			}

			const prefix = isArray ? '' : `'${key}' => `;

			return `${config.indent.repeat(level)}${prefix}${quote}${value}${quote},`;
		})
		.join('\n');
};

export const blade = (name, attrs) => attrs
	.map((item) => {
		const bladeAttrsString = bladeAttrs(item, 1);

		const indent = bladeAttrsString ? '\n' : '';

		return `@component('components/${name}', [${indent}${bladeAttrsString}${indent}])\n@endcomponent`;
	})
	.join('\n\n');

const vueAttrs = (attrs) => {
	const keys = Object.keys(attrs);

	const overMaxAttrs = keys.length > config.maxAttrs;

	const items = keys
		.map((key) => {
			const value = attrs[key];

			switch (typeof value) {
				case 'boolean':
					return value ? key : false;

				case 'string':
					return `${key}="${value}"`;

				default: {
					const jsonString = JSON.stringify(value);

					const quote = jsonString.includes('"') ? "'" : '"';

					return `:${key}=${quote}${jsonString}${quote}`;
				}
			}
		})
		.filter(v => v);

	if (!items.length) {
		return '';
	}

	const itemsHtml = items.join(overMaxAttrs ? `\n${config.indent}` : ' ');

	return overMaxAttrs ? `\n${config.indent}${itemsHtml}\n` : `${itemsHtml} `;
};

export const vue = (name, attrs) => attrs
	.map(item => `<${name} ${vueAttrs(item)}/>`)
	.join('\n\n');

export default {
	blade,
	vue,
};
