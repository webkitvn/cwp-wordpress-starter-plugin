# Asset Building Specification

## ADDED Requirements

### Requirement: Vite-Based Asset Build System
The plugin SHALL use Vite to transpile, bundle, and optimize assets from the `/src` directory into `/assets` directory without relying on Vite's development server.

#### Scenario: TypeScript to JavaScript transpilation
- **WHEN** TypeScript files are in `/src/admin` or `/src/public`
- **THEN** running `pnpm build` transpiles TypeScript to JavaScript with source maps, targeting modern browsers (ES2020+)

#### Scenario: CSS and SCSS processing
- **WHEN** SCSS or CSS files are imported in `/src` entry points
- **THEN** running `pnpm build` processes styles with:
  - SCSS → CSS compilation
  - PostCSS autoprefixer for vendor prefixes
  - Minification for production
  - Output to `/assets`

#### Scenario: Module bundling
- **WHEN** multiple TypeScript/JavaScript files are present in `/src`
- **THEN** running `pnpm build` bundles related modules into separate chunks for admin and public, reducing redundant code

#### Scenario: Watch mode for development
- **WHEN** developer runs `pnpm build:watch` during local development
- **THEN** Vite watches `/src` for file changes and automatically rebuilds affected assets without starting a development server

### Requirement: Asset Entry Points
The plugin SHALL define clear entry points for admin and public assets that Vite uses for bundling.

#### Scenario: Admin assets entry point
- **WHEN** Vite builds assets
- **THEN** `/src/admin/index.ts` is the entry point for admin JavaScript, and `/src/admin/styles.scss` is the entry point for admin styles

#### Scenario: Public assets entry point
- **WHEN** Vite builds assets
- **THEN** `/src/public/index.ts` is the entry point for public-facing JavaScript, and `/src/public/styles.scss` is the entry point for public styles

#### Scenario: Shared styles and components included
- **WHEN** admin or public code imports from `/src/shared` or `/src/public/components`
- **THEN** shared utilities and reusable components are properly bundled with the importing entry point without duplication

### Requirement: Vite Configuration Management
The plugin SHALL have a well-documented Vite configuration file that specifies build inputs, outputs, and optimization settings.

#### Scenario: Production build output
- **WHEN** running `pnpm build` in production mode
- **THEN** Vite outputs minified, optimized assets to `/assets` with cache-busting filenames and source maps for debugging

#### Scenario: Development build output
- **WHEN** running `pnpm build:watch` for development
- **THEN** Vite outputs unminified assets with inline source maps to aid debugging in browser dev tools

### Requirement: Asset Enqueueing Integration
The plugin PHP code SHALL correctly enqueue built assets from the `/assets` directory using WordPress functions.

#### Scenario: Admin script and style enqueueing
- **WHEN** admin page loads
- **THEN** PHP calls `wp_enqueue_script('cwp-admin', plugins_url('assets/admin.js', __FILE__))` and `wp_enqueue_style('cwp-admin', plugins_url('assets/admin.css', __FILE__))`

#### Scenario: Public script and style enqueueing
- **WHEN** public page with plugin functionality loads
- **THEN** PHP calls `wp_enqueue_script('cwp-public', plugins_url('assets/public.js', __FILE__))` and `wp_enqueue_style('cwp-public', plugins_url('assets/public.css', __FILE__))`

#### Scenario: Version management for cache busting
- **WHEN** assets are enqueued
- **THEN** version parameter uses plugin version constant (e.g., `CWP_PLUGIN_VERSION`) to ensure browsers fetch fresh assets on updates
