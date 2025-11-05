/**
 * External dependencies
 */
import { defineConfig } from 'vite';
import path from 'path';
import autoprefixer from 'autoprefixer';
import removeConsole from 'vite-plugin-remove-console';

export default defineConfig(({ mode }) => {
	const isProduction = mode === 'production';

	return {
		plugins: isProduction
			? [
					removeConsole({
						// Only remove console statements in production builds
						excludes: ['error', 'warn'], // Keep error and warn logs
					}),
				]
			: [],
		build: {
			// Output directory for built assets
			outDir: 'assets',
			// Don't empty the directory on build (preserve .gitkeep)
			emptyOutDir: false,
			// Build in library mode (not application mode)
			lib: {
				entry: {
					admin: path.resolve(__dirname, 'src/admin/index.ts'),
					public: path.resolve(__dirname, 'src/public/index.ts'),
				},
				formats: ['es'],
				fileName: (format, entryName) => `${entryName}.js`,
			},
			// Generate sourcemaps for debugging
			sourcemap: true,
			// Rollup options
			rollupOptions: {
				output: {
					// Separate CSS files for each entry
					assetFileNames: (assetInfo) => {
						if (assetInfo.name?.endsWith('.css')) {
							// Extract entry name from the CSS file
							const name = assetInfo.name.replace('.css', '');
							return `${name}.css`;
						}
						return assetInfo.name || 'assets/[name][extname]';
					},
				},
			},
		},
		css: {
			preprocessorOptions: {
				scss: {
					// Additional SCSS options can be added here
				},
			},
			postcss: {
				plugins: [autoprefixer],
			},
		},
		resolve: {
			alias: {
				'@': path.resolve(__dirname, './src'),
				'@admin': path.resolve(__dirname, './src/admin'),
				'@public': path.resolve(__dirname, './src/public'),
				'@shared': path.resolve(__dirname, './src/shared'),
			},
		},
		define: {
			'process.env.NODE_ENV': JSON.stringify(
				process.env.NODE_ENV || 'production'
			),
		},
	};
});
