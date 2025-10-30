module.exports = {
	root: true,
	extends: [
		'plugin:@wordpress/eslint-plugin/recommended',
		'plugin:@typescript-eslint/recommended',
	],
	parser: '@typescript-eslint/parser',
	parserOptions: {
		ecmaVersion: 2022,
		sourceType: 'module',
		ecmaFeatures: {
			jsx: true,
		},
	},
	plugins: ['@typescript-eslint'],
	env: {
		browser: true,
		es2022: true,
		node: true,
	},
	rules: {
		'@wordpress/dependency-group': 'warn',
		'@wordpress/no-unsafe-wp-apis': 'warn',
		'no-console': ['warn', { allow: ['warn', 'error'] }],
	},
	settings: {
		react: {
			version: 'detect',
		},
	},
	ignorePatterns: [
		'assets/**',
		'vendor/**',
		'node_modules/**',
		'*.config.js',
		'*.config.ts',
	],
};
