# Scaffold Plugin Foundation

## Why

Rapid WordPress plugin development requires a solid foundation that combines WordPress best practices with modern frontend tooling. Current development often lacks clear structure, inconsistent coding standards, and unclear asset management, leading to technical debt and slow onboarding for new developers. A lean, feature-minimal skeleton with pre-configured build tools, standards enforcement, and clear architecture separation enables developers to start custom work immediately without reinventing foundational patterns.

## What Changes

- **Plugin Structure**: Establish a clear directory hierarchy separating PHP logic, administration interfaces, and frontend user-facing code with class-based hook registration
- **Asset Building**: Integrate Vite with SCSS/PostCSS for transpiling, bundling, and optimizing assets from `/src` directory without relying on its development server
- **Coding Standards**: Configure PHPCS with WordPress-VIP-Go standard, ESLint with strict TypeScript, and Prettier for consistent formatting
- **Development Setup**: Provide pnpm + Composer, build configuration, testing structure templates, and GitHub Actions CI workflow for automation
- **REST API Documentation**: Include guide for developers to extend with optional REST endpoints
- **Testing Foundation**: Reserve directory structure and configuration templates for PHPUnit and Jest

## Impact

**Affected Capabilities**:
- `plugin-structure` (NEW) - Class-based directory organization and bootstrap
- `asset-building` (NEW) - Vite configuration with SCSS/PostCSS for build-only workflow
- `coding-standards` (NEW) - PHPCS, ESLint strict mode, and linting enforcement
- `development-setup` (NEW) - pnpm + Composer, build scripts, testing templates, CI workflows

**Affected Code**:
- Plugin bootstrap: `cwp-wordpress-starter-plugin.php`
- Main class: `admin/php/class-plugin.php` with hook registration
- Build configs: `vite.config.ts`, `package.json`, `composer.json`
- PHPCS configuration: `.phpcs.xml.dist`
- GitHub Actions: `.github/workflows/lint.yml`
- Directory structure: `/src`, `/assets`, `/admin`, `/public`, `/tests`, `/docs`
- Configuration: `.eslintrc.cjs`, `tsconfig.json`, `.prettierrc.json`, `.editorconfig`

**Key Files Created**:
- Root plugin file with proper headers and class instantiation
- Class-based plugin architecture for hook management
- Vite + SCSS/PostCSS configuration for asset building
- Package.json + composer.json for dependency management
- PHPCS ruleset for WordPress standards
- GitHub Actions workflow for automated CI/CD
- Testing configuration templates for PHPUnit and Jest
- REST API extension guide documentation
- Directory scaffolding for organized code

**Non-Breaking**: This is foundational setup—no existing behavior modified.
