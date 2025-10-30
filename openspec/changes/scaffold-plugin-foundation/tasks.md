# Implementation Tasks: Scaffold Plugin Foundation

## 1. Plugin Bootstrap

- [ ] 1.1 Create root plugin file `cwp-wordpress-starter-plugin.php` with proper WordPress headers
  - Plugin Name, Description, Version, Author, Author URI, License, Text Domain, Domain Path
  - Define `CWP_PLUGIN_VERSION` and `CWP_PLUGIN_DIR` constants
  - Include bootstrap initialization function
  - **Validation**: File exists, passes WordPress plugin validation

- [ ] 1.2 Create main plugin class `admin/php/class-plugin.php` in `CWP\StarterPlugin` namespace
  - Constructor accepts optional dependencies
  - Registers hooks for admin and public asset enqueueing
  - Handles text domain loading for translations
  - Methods: `__construct()`, `enqueue_admin_assets()`, `enqueue_public_assets()`
  - **Validation**: Class is properly namespaced, instantiable, and hooks register correctly

- [ ] 1.3 Create utility hooks file `admin/php/Hooks.php` with placeholder hook functions
  - `cwp_register_scripts_styles()` - Hooks for asset enqueueing
  - Example action/filter hooks for extension
  - **Validation**: Functions follow CWP naming convention, PHPDoc included

## 2. Directory Structure

- [ ] 2.1 Create admin structure directories
  - `/admin` - Backend PHP logic
  - `/admin/hooks` - Organized hook registrations
  - **Validation**: Directories exist and are navigable

- [ ] 2.2 Create public structure directories
  - `/public` - Frontend PHP logic (shortcodes, templates)
  - **Validation**: Directories exist and are navigable

- [ ] 2.3 Create source asset directories
  - `/src/admin` - Admin TypeScript/CSS
  - `/src/public` - Public TypeScript/CSS with components subdirectory
  - `/src/public/components` - Reusable JavaScript components
  - `/src/shared` - Shared utilities
  - **Validation**: Directories exist and are empty/have placeholder files

- [ ] 2.4 Create assets output directory (initially empty)
  - `/assets` - Will hold built JavaScript and CSS
  - Add `.gitkeep` to ensure directory tracking
  - **Validation**: Directory exists and is tracked by git

## 3. TypeScript & JavaScript Source Files

- [ ] 3.1 Create TypeScript configuration file `tsconfig.json`
  - Target ES2020, module ESNext
  - Strict mode enabled
  - Include `/src` files, exclude `/node_modules`, `/assets`, `/vendor`
  - **Validation**: File is valid JSON, TypeScript can compile with it

- [ ] 3.2 Create admin entry point `/src/admin/index.ts`
  - Import admin stylesheet
  - Basic console log or initialization code for demo
  - **Validation**: File compiles without errors

- [ ] 3.3 Create admin stylesheet `/src/admin/styles.scss`
  - Basic SCSS structure with variables for colors, spacing
  - Example utility classes
  - **Validation**: File processes without SCSS errors

- [ ] 3.4 Create public entry point `/src/public/index.ts`
  - Import public stylesheet
  - Basic initialization code
  - **Validation**: File compiles without errors

- [ ] 3.5 Create public stylesheet `/src/public/styles.scss`
  - Basic SCSS structure
  - Example component styles
  - **Validation**: File processes without SCSS errors

- [ ] 3.6 Create shared utilities `/src/shared/utils.ts` and `/src/shared/constants.ts`
  - Common utility functions and constants
  - Importable by both admin and public modules
  - **Validation**: Files are importable without errors

## 4. Vite Build Configuration

- [ ] 4.1 Create `vite.config.ts` file
  - Define build entry points: `/src/admin/index.ts`, `/src/public/index.ts`
  - Configure output to `/assets` with `.js` and `.css` files
  - Set library mode (not application)
  - **Validation**: File is TypeScript valid and recognized by Vite

- [ ] 4.2 Configure SCSS/PostCSS processing
  - Use `sass` and `postcss` loaders
  - Include Autoprefixer plugin
  - **Validation**: `vite build` processes `.scss` files to `.css`

- [ ] 4.3 Create `.postcssrc.json` configuration
  - Configure Autoprefixer for browser targets
  - Target browser support (e.g., last 2 versions)
  - **Validation**: PostCSS processes and prefixes CSS correctly

- [ ] 4.4 Add build scripts to `package.json`
  - `pnpm build`: Runs `vite build`
  - `pnpm build:watch`: Runs `vite build --watch`
  - **Validation**: Both scripts execute without errors

## 5. PHP Code Standards Configuration

- [ ] 5.1 Create `phpcs.xml` configuration file
  - Use WordPress-VIP-Go standard
  - Exclude `/vendor`, `/node_modules`, `/assets`, `/tests`, `/docs`
  - Include paths: `/includes`, `/admin`, `/public`
  - Enforce `cwp_` prefix on functions and hooks
  - **Validation**: PHPCS runs without errors, finds real issues in sample PHP

- [ ] 5.2 Add PHPCS check script to `package.json`
  - `pnpm lint:php`: Runs `phpcs`
  - **Validation**: Script executes and reports correct coding standard

- [ ] 5.3 Create sample PHP file to validate standards
  - `/admin/hooks/enqueue.php`: Demonstrates proper naming conventions
  - Uses `cwp_` prefix for action hooks
  - Includes proper docblocks
  - **Validation**: PHPCS validates file without errors

## 6. Asset Enqueueing

- [ ] 6.1 Create admin asset enqueueing in class-based Plugin
  - Method: `enqueue_admin_assets()`
  - Registers and enqueues `/assets/admin.js` with deps: `['wp-i18n', 'wp-element', 'jquery']`
  - Registers and enqueues `/assets/admin.css` with deps: `['wp-common']`
  - Uses version from `PLUGIN_VERSION` constant
  - **Validation**: Admin assets load in WordPress admin with correct handles

- [ ] 6.2 Create public asset enqueueing in class-based Plugin
  - Method: `enqueue_public_assets()`
  - Registers and enqueues `/assets/public.js`
  - Registers and enqueues `/assets/public.css`
  - Uses version from `PLUGIN_VERSION` constant
  - Skips enqueueing on admin pages
  - **Validation**: Public assets load on frontend

- [ ] 6.3 Add hook registration in Plugin constructor
  - `add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets'])`
  - `add_action('wp_enqueue_scripts', [$this, 'enqueue_public_assets'])`
  - **Validation**: Hooks fire at correct priority and time

## 7. ESLint & Code Formatting

- [ ] 7.1 Create `.eslintrc.json` configuration
  - Use `@wordpress/eslint-plugin` with TypeScript parser
  - Target browser and WordPress environments
  - Include `/src` files, exclude `/assets`, `/vendor`
  - Enable strict rule set
  - **Validation**: ESLint runs and enforces WordPress standards

- [ ] 7.2 Create `.prettierrc.json` configuration
  - Line width: 100
  - Tab width: 2
  - Trailing comma: 'es5'
  - Single quotes: true
  - Semicolons: true
  - **Validation**: Prettier formats consistently

- [ ] 7.3 Add lint scripts to `package.json`
  - `pnpm lint:js`: Runs `eslint src --ext .ts,.tsx`
  - `pnpm format:js`: Runs `prettier --write src`
  - `pnpm lint:styles`: Runs `stylelint src --fix`
  - **Validation**: All scripts execute successfully

- [ ] 7.4 Create `.stylelintrc.json` configuration
  - Use `stylelint-config-standard-scss`
  - Target SCSS files
  - **Validation**: Stylelint processes SCSS without errors

## 8. Composer & PHP Dependencies

- [ ] 8.1 Create `composer.json` file
  - Package name: `cwp/starter-plugin`
  - Require: `php ^7.4`, `squizlabs/php_codesniffer`, `wp-coding-standards/wpcs`
  - PSR-4 autoloading for `CWP\StarterPlugin\` namespace
  - **Validation**: Composer file is valid JSON, composer install works

- [ ] 8.2 Create `composer.lock` file (after running composer install)
  - All PHP dependencies locked to specific versions
  - **Validation**: File is generated and tracked in version control

- [ ] 8.3 Create `.gitignore` for composer artifacts
  - Include `/vendor` directory
  - Exclude `/composer.lock` from .gitignore (commit it for reproducible installs)
  - **Validation**: Composer vendor directory is not tracked

- [ ] 8.4 Add composer scripts to `composer.json`
  - `composer lint`: Runs PHPCS
  - **Validation**: Script is callable and executes PHPCS

## 9. Testing Structure (Templates)

- [ ] 9.1 Create `phpunit.xml.dist` configuration
  - Bootstrap file: `tests/bootstrap.php` (loads WordPress test suite)
  - Test directory: `tests/unit`
  - Exclude `/vendor`, `/node_modules`, `/assets`
  - **Validation**: File is valid XML, PHPUnit recognizes it

- [ ] 9.2 Create `jest.config.js` configuration
  - Preset: `@wordpress/jest-preset-default`
  - Test environment: 'jsdom' (for DOM-based tests)
  - Module map aliases for TypeScript path imports
  - **Validation**: Jest recognizes config, can run test discovery

- [ ] 9.3 Create test directory structure
  - `/tests` directory (root level)
  - `/tests/unit` directory (for PHPUnit tests)
  - `/src/__tests__` directories (for Jest tests alongside source)
  - **Validation**: Directories exist and match conventions

- [ ] 9.4 Add test scripts to `package.json`
  - `pnpm test`: Runs `jest`
  - `pnpm test:watch`: Runs `jest --watch`
  - `pnpm test:php`: Runs `phpunit`
  - **Validation**: All scripts are callable (even if tests don't exist yet)

## 10. GitHub Actions CI

- [ ] 10.1 Create GitHub Actions workflow
  - Create `.github/workflows/lint.yml` with:
    - Trigger: push, pull_request to main branch
    - Jobs for PHP validation (PHPCS)
    - Jobs for JavaScript validation (ESLint)
    - Format checking (Prettier)
    - TypeScript compilation check
  - **Validation**: Workflow file is valid YAML

- [ ] 10.2 Add job matrix for multiple environments
  - PHP versions: 7.4, 8.0, 8.1+ (WordPress compatibility)
  - Node.js LTS versions
  - Parallel execution for speed
  - **Validation**: Workflow runs on multiple versions successfully

- [ ] 10.3 Test workflow execution
  - Push to feature branch
  - Verify GitHub Actions runs automatically
  - Check that failures are reported clearly
  - **Validation**: Workflow appears in Actions tab, runs successfully

## 11. Documentation

- [ ] 11.1 Update README.md
  - Plugin name and description
  - Installation steps (clone, `pnpm install`, `composer install`, `pnpm build`)
  - Build workflow instructions
  - File structure overview
  - Testing and CI setup links
  - Link to contribution guidelines

- [ ] 11.2 Create CONTRIBUTING.md
  - Development setup steps (Node.js, pnpm, Composer)
  - How to run build and linting
  - How to run tests (PHP + JavaScript)
  - Code style guidelines (CWP prefix, naming conventions)
  - How to verify CI will pass locally
  - Common tasks and troubleshooting

- [ ] 11.3 Create REST API extension guide
  - Document in `/docs/REST-API.md`:
    - How to register a custom endpoint
    - Example controller class structure
    - Authentication and permissions patterns
    - Testing REST endpoints locally
  - **Validation**: Guide is clear and follows WordPress REST API standards

- [ ] 11.4 Add inline code comments
  - Main plugin file explaining structure
  - Plugin class documenting hook registration
  - Key functions explaining purpose and parameters
  - **Validation**: Code is readable without external docs

## 12. Git Configuration

- [ ] 12.1 Create/verify `.gitignore`
  - `/node_modules`, `/vendor`, `/assets` (built output)
  - `.env` files, IDE settings, OS files
  - `composer.lock` (optional comment)
  - **Validation**: git status shows no unwanted files

- [ ] 12.2 Add initial commit
  - Include all source files, configs, documentation
  - Exclude build output and dependencies
  - Commit message: "Initialize plugin scaffold"
  - **Validation**: Repository is clean and ready for development

## 13. Final Validation

- [ ] 13.1 Plugin activation test
  - Place plugin in WordPress site
  - Verify it activates without errors
  - Check admin/public assets load correctly in browser DevTools
  - **Validation**: No PHP errors, no JavaScript console errors

- [ ] 13.2 Build process validation
  - Run full build cycle: `pnpm build`
  - Verify assets are generated correctly in `/assets`
  - Verify watch mode works: `pnpm build:watch`
  - **Validation**: All build scripts work as expected

- [ ] 13.3 Code quality baseline
  - All linting checks pass: `pnpm lint`
  - No formatting issues: `pnpm format --check`
  - Composer dependencies valid: `composer validate`
  - **Validation**: `pnpm lint` exits cleanly

- [ ] 13.4 GitHub Actions verification
  - Push to GitHub
  - Verify CI workflow runs in Actions tab
  - All checks pass (PHPCS, ESLint, Prettier, TypeScript)
  - **Validation**: Green checkmarks on all workflows

---

## Parallel Work Opportunities

- **Parallel 1**: Tasks 2-3 (directory structure and TypeScript files) can be done simultaneously
- **Parallel 2**: Tasks 5 (config files) and Task 4 (build setup) can be done alongside
- **Parallel 3**: Tasks 8-10 (Composer, Testing, CI) can run in parallel after core scaffolding
- **Blocking Dependencies**:
  - Task 4 must complete before Task 7 (linting requires pnpm setup)
  - Task 8 must complete before Task 11 (documentation references testing/CI)
  - Task 13 is the final gate—all others must complete first
