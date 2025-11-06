import { fixupConfigRules } from '@eslint/compat';
import { FlatCompat } from '@eslint/eslintrc';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const compat = new FlatCompat({
    baseDirectory: __dirname,
    resolvePluginsRelativeTo: __dirname,
});

const legacyConfig = compat.config({
    root: true,
    extends: [
        'plugin:@wordpress/eslint-plugin/recommended',
        'plugin:@typescript-eslint/recommended',
    ],
    parser: '@typescript-eslint/parser',
    parserOptions: {
        ecmaVersion: 2022,
        sourceType: 'module',
        project: './tsconfig.json',
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
        'import/resolver': {
            typescript: {
                alwaysTryTypes: true,
                project: './tsconfig.json',
            },
            node: true,
        },
    },
    ignorePatterns: [
        'assets/**',
        'vendor/**',
        'node_modules/**',
        '*.config.js',
    ],
});

export default fixupConfigRules(legacyConfig);
