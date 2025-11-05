/**
 * External dependencies
 */
import { defineConfig } from 'vite';
import path from 'path';
import autoprefixer from 'autoprefixer';

export default defineConfig(({ mode }) => {
	const isProduction = mode === 'production';

	return {
		plugins: [
			...(isProduction
				? [
						{
							name: 'remove-console',
							transform(code) {
								return code.replace(
									/console\.log\([^)]*\);?/g,
									''
								);
							},
						},
					]
				: []),
		],
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
			target: 'es2015',
			// Use esbuild minifier for faster builds
			minify: isProduction ? 'esbuild' : false,
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
			'process.env.NODE_ENV': JSON.stringify(mode),
		},
	};
});
