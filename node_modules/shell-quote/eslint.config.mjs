import ljharb from '@ljharb/eslint-config/flat';

export default [
	...ljharb,
	{
		rules: {
			'array-bracket-newline': 'off',
			complexity: 'off',
			eqeqeq: 'warn',
			'func-style': ['error', 'declaration'],
			'max-depth': 'off',
			'max-lines-per-function': 'off',
			'max-statements': 'off',
			'multiline-comment-style': 'off',
			'no-extra-parens': 'off',
			'no-lonely-if': 'warn',
			'no-negated-condition': 'warn',
			'no-param-reassign': 'warn',
			'no-shadow': 'warn',
			'no-template-curly-in-string': 'off',
		},
	},
	{
		files: ['example/**'],
		rules: {
			'no-console': 'off',
		},
	},
];
