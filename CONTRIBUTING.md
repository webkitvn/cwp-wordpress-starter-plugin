# Contributing to CWP WordPress Starter Plugin

Thank you for considering contributing to this project! This document outlines the development workflow and coding standards.

## Development Setup

### Prerequisites

- PHP 7.4 or higher
- Node.js 18.x or higher
- pnpm (recommended) or npm
- Composer
- WordPress development environment (LocalWP, MAMP, Docker, etc.)

### Initial Setup

1. Fork and clone the repository:
```bash
git clone <your-fork-url>
cd cwp-wordpress-starter-plugin
```

2. Install dependencies:
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
pnpm install
```

3. Build assets:
```bash
pnpm build
```

## Development Workflow

### Building Assets

During development, use watch mode to automatically rebuild assets on file changes:

```bash
pnpm build:watch
# or
pnpm dev
```

For production builds:

```bash
pnpm build
```

### Code Quality

Before committing, ensure all code quality checks pass:

```bash
# Run all linters
pnpm lint

# Fix auto-fixable issues
pnpm format
pnpm format:styles
composer lint:fix
```

### Running Tests

```bash
# Run JavaScript tests
pnpm test

# Run PHP tests
pnpm test:php

# Run TypeScript type checking
pnpm typecheck
```

## Coding Standards

### PHP Standards

We follow WordPress VIP Go coding standards enforced by PHPCS.

**Key conventions:**

- Use `cwp_` prefix for all functions and hooks
- Use `CWP_` prefix for constants
- Use `CWP\StarterPlugin` namespace for classes
- Follow WordPress naming conventions (snake_case for functions, PascalCase for classes)
- Always include proper PHPDoc comments

**Example:**

```php
<?php
namespace CWP\StarterPlugin;

/**
 * Example function with proper naming and documentation.
 *
 * @param string $param Description of parameter.
 * @return bool True on success, false on failure.
 */
function cwp_example_function( $param ) {
    return true;
}
```

**Running PHPCS:**

```bash
# Check PHP code
pnpm lint:php

# Auto-fix issues
composer lint:fix
```

### JavaScript/TypeScript Standards

We use ESLint with WordPress plugin and TypeScript rules, enforced by Prettier for formatting.

**Key conventions:**

- Use TypeScript for all new JavaScript code
- Enable strict mode in TypeScript
- Use camelCase for variables and functions
- Use PascalCase for classes and components
- Always add proper type annotations
- Avoid `any` types

**Example:**

```typescript
/**
 * Example utility function
 */
export function exampleFunction(param: string): boolean {
  return param.length > 0;
}
```

**Running ESLint:**

```bash
# Check JavaScript/TypeScript
pnpm lint:js

# Auto-fix issues
pnpm format:js
```

### CSS/SCSS Standards

We use Stylelint with SCSS configuration.

**Key conventions:**

- Use `cwp-` prefix for all CSS classes
- Follow BEM naming convention: `.cwp-block__element--modifier`
- Use SCSS variables for colors, spacing, and other reusable values
- Organize styles by component

**Example:**

```scss
.cwp-component {
  padding: $spacing-md;
  
  &__title {
    font-size: 18px;
    color: $primary-color;
  }
  
  &--featured {
    background-color: $highlight-color;
  }
}
```

**Running Stylelint:**

```bash
# Check styles
pnpm lint:styles

# Auto-fix issues
pnpm format:styles
```

## Git Workflow

### Branching Strategy

- `main` - Production-ready code
- `develop` - Development branch
- `feature/*` - New features
- `fix/*` - Bug fixes
- `docs/*` - Documentation updates

### Commit Messages

Follow conventional commit format:

```
type(scope): subject

body (optional)

footer (optional)
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

**Example:**
```
feat(admin): add settings page for plugin configuration

Added a new settings page in the WordPress admin with options
for configuring plugin behavior.

Closes #123
```

### Pull Request Process

1. Create a feature branch from `develop`
2. Make your changes following the coding standards
3. Run all linters and tests locally:
   ```bash
   pnpm lint
   pnpm test
   pnpm typecheck
   ```
4. Commit your changes with descriptive commit messages
5. Push to your fork and create a pull request to `develop`
6. Wait for CI checks to pass
7. Address any review feedback

## Verifying CI Will Pass

Before pushing, ensure all CI checks will pass:

```bash
# Run all checks that CI runs
pnpm lint        # ESLint, Stylelint, PHPCS
pnpm typecheck   # TypeScript compilation
pnpm build       # Build assets
pnpm test        # Jest tests
pnpm test:php    # PHPUnit tests (if WordPress test suite is set up)
```

## Common Tasks

### Adding a New Admin Feature

1. Create PHP class in `admin/php/`
2. Register hooks in the main Plugin class
3. Create TypeScript/SCSS files in `src/admin/`
4. Import in `src/admin/index.ts`
5. Build and test

### Adding a New Public Feature

1. Create PHP logic in `public/`
2. Create TypeScript/SCSS files in `src/public/components/`
3. Import in `src/public/index.ts`
4. Build and test

### Adding a REST API Endpoint

See [REST API Extension Guide](docs/REST-API.md) for detailed instructions.

## Troubleshooting

### Build Issues

**Problem:** Vite build fails
```bash
# Clear node_modules and reinstall
rm -rf node_modules
pnpm install
```

**Problem:** TypeScript errors
```bash
# Check TypeScript configuration
pnpm typecheck
```

### Linting Issues

**Problem:** PHPCS errors
```bash
# Try auto-fixing
composer lint:fix

# Check specific file
vendor/bin/phpcs path/to/file.php --standard=.phpcs.xml.dist
```

**Problem:** ESLint errors
```bash
# Try auto-fixing
pnpm format:js

# Check specific file
pnpm eslint src/path/to/file.ts
```

### Testing Issues

**Problem:** Jest tests fail
```bash
# Clear Jest cache
pnpm exec jest --clearCache

# Run specific test
pnpm test path/to/test.test.ts
```

## Getting Help

- Open an issue for bug reports or feature requests
- Tag maintainers in pull requests for review
- Check existing issues and pull requests before creating new ones

## Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Focus on improving the codebase
- Help others learn and grow

Thank you for contributing!
