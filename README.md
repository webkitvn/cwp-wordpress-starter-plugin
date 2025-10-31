# CWP WordPress Starter Plugin

A modern WordPress plugin starter template with Vite, TypeScript, SCSS, and comprehensive coding standards enforcement.

## Features

- **Modern Build System**: Vite for fast development and optimized production builds
- **TypeScript**: Full TypeScript support with strict mode enabled
- **SCSS/PostCSS**: Modern CSS preprocessing with autoprefixer
- **Coding Standards**: PHPCS with WordPress-VIP-Go standards, ESLint, Stylelint, and Prettier
- **Testing Ready**: PHPUnit and Jest configurations included
- **CI/CD**: GitHub Actions workflow for automated linting and testing
- **Class-Based Architecture**: Clean, maintainable PHP code structure
- **Asset Management**: Automatic script and style enqueueing

## Requirements

- PHP 7.4 or higher
- Node.js 18.x or higher
- pnpm (recommended) or npm
- Composer
- WordPress 6.0 or higher

## Installation

1. Clone this repository into your WordPress plugins directory:
```bash
cd wp-content/plugins
git clone <repository-url> cwp-wordpress-starter-plugin
cd cwp-wordpress-starter-plugin
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
pnpm install
```

4. Build assets:
```bash
pnpm build
```

5. Activate the plugin in WordPress admin.

## Development

### Build Commands

- `pnpm build` - Build assets for production
- `pnpm build:watch` - Build assets in watch mode for development
- `pnpm dev` - Alias for build:watch

### Linting

- `pnpm lint` - Run all linters (PHP, JavaScript, Styles)
- `pnpm lint:js` - Lint JavaScript/TypeScript files
- `pnpm lint:styles` - Lint SCSS/CSS files
- `pnpm lint:php` - Lint PHP files with PHPCS

### Formatting

- `pnpm format` - Format all code (JavaScript and Styles)
- `pnpm format:js` - Format JavaScript/TypeScript files with Prettier
- `pnpm format:styles` - Format and fix SCSS/CSS files

### Testing

- `pnpm test` - Run Jest tests
- `pnpm test:watch` - Run Jest in watch mode
- `pnpm test:php` - Run PHPUnit tests
- `pnpm typecheck` - Check TypeScript types

## File Structure

```
cwp-wordpress-starter-plugin/
├── admin/                  # Backend PHP logic
│   ├── php/                # PHP classes
│   │   └── class-plugin.php
│   └── hooks/              # Hook registrations
│       └── enqueue.php
├── public/                 # Frontend PHP logic
├── src/                    # Source assets (uncompiled)
│   ├── admin/              # Admin JavaScript/SCSS
│   │   ├── index.ts
│   │   └── styles.scss
│   ├── public/             # Public JavaScript/SCSS
│   │   ├── index.ts
│   │   └── styles.scss
│   └── shared/             # Shared utilities
│       ├── constants.ts
│       └── utils.ts
├── assets/                 # Compiled assets (generated)
├── tests/                  # Test files
│   ├── unit/               # PHPUnit tests
│   └── bootstrap.php       # PHPUnit bootstrap
├── docs/                   # Documentation
├── .github/                # GitHub configuration
│   └── workflows/          # CI/CD workflows
├── cwp-wordpress-starter-plugin.php  # Main plugin file
├── composer.json           # PHP dependencies
├── package.json            # JavaScript dependencies
├── vite.config.ts          # Vite configuration
├── tsconfig.json           # TypeScript configuration
├── .phpcs.xml.dist         # PHPCS configuration
├── .eslintrc.cjs           # ESLint configuration
├── .stylelintrc.json       # Stylelint configuration
└── .prettierrc.json        # Prettier configuration
```

## Naming Conventions

- **PHP Functions**: Use `cwp_` prefix (e.g., `cwp_register_scripts_styles`)
- **CSS Classes**: Use `cwp-` prefix with BEM notation (e.g., `cwp-admin-container__header`)
- **Constants**: Use `CWP_` prefix in uppercase (e.g., `CWP_PLUGIN_VERSION`)
- **Namespaces**: Use `CWP\StarterPlugin` for PHP classes

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our development workflow and coding standards.

## Documentation

- [REST API Extension Guide](docs/REST-API.md) - How to add custom REST endpoints
- [Testing Guide](docs/TESTING.md) - How to write and run tests

## License

GPL v2 or later

## Credits

Built with modern web development tools and WordPress best practices.
