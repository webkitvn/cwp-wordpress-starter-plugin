# Coding Standards Specification

## ADDED Requirements

### Requirement: PHP Coding Standards Enforcement
The plugin SHALL enforce PHP code style using PHPCS (PHP CodeSniffer) with WordPress and VIP standards, ensuring consistency and maintainability.

#### Scenario: WordPress standard compliance
- **WHEN** running `pnpm lint:php` or `./vendor/bin/phpcs`
- **THEN** PHPCS validates all PHP files against WordPress VIP Go standard ruleset, detecting violations in naming, spacing, documentation, and security

#### Scenario: CWP prefix enforcement
- **WHEN** PHP code defines functions, classes, constants, or hooks
- **THEN** PHPCS enforces that all global identifiers use the `cwp_` prefix to prevent namespace pollution

#### Scenario: Documentation requirements
- **WHEN** PHP functions or classes are defined
- **THEN** PHPCS enforces proper PHPDoc blocks with parameter, return, and description information

#### Scenario: Automatic code fixing
- **WHEN** running `pnpm lint:php:fix` or `./vendor/bin/phpcbf`
- **THEN** PHPCS automatically corrects style issues like spacing, indentation, and formatting where possible

### Requirement: JavaScript/TypeScript Code Standards
The plugin SHALL enforce JavaScript and TypeScript code style using ESLint with WordPress plugin conventions.

#### Scenario: ESLint validation on TypeScript files
- **WHEN** running `pnpm lint:js` or `pnpm lint`
- **THEN** ESLint validates all TypeScript and JavaScript files in `/src`, checking for best practices, unused variables, and WordPress coding patterns

#### Scenario: TypeScript strict mode
- **WHEN** TypeScript files are validated
- **THEN** strict mode is enabled in `tsconfig.json` with:
  - `"strict": true` for maximum type safety
  - `"noImplicitAny"` to require explicit types
  - `"strictNullChecks"` to catch null/undefined issues
  - Use `// @ts-ignore` comments only when justified with explanations

#### Scenario: WordPress ESLint rules applied
- **WHEN** ESLint runs
- **THEN** `@wordpress/eslint-plugin` rules are applied, enforcing WordPress-specific best practices like proper dependency usage and blocking unsafe WP APIs by default

#### Scenario: Automatic code formatting
- **WHEN** running `pnpm format` or `pnpm lint:js:fix`
- **THEN** ESLint automatically fixes formatting and style issues in TypeScript/JavaScript files

### Requirement: CSS/SCSS Code Standards
The plugin SHALL enforce CSS and SCSS style consistency using Prettier.

#### Scenario: Prettier code formatting
- **WHEN** running `pnpm format` or `pnpx prettier --write`
- **THEN** all CSS, SCSS, JSON, and Markdown files are formatted according to the project's Prettier configuration

#### Scenario: SCSS best practices
- **WHEN** SCSS files are written
- **THEN** developers follow nesting guidelines, variable naming conventions, and mixin usage as documented

### Requirement: Pre-commit and CI Integration
The plugin configuration SHALL support running linting and code quality checks as part of development workflow.

#### Scenario: pnpm scripts for all checks
- **WHEN** developer runs `pnpm lint`
- **THEN** all linting checks (PHP, JavaScript, CSS) run in sequence, reporting any violations

#### Scenario: Individual check scripts
- **WHEN** developer runs a specific command
- **THEN** commands like `pnpm lint:php`, `pnpm lint:js`, `pnpm format` allow targeted checks

### Requirement: Code Standards Documentation
The plugin SHALL provide clear documentation of expected code style and standards for contributors.

#### Scenario: Coding standards guide
- **WHEN** a developer reads README.md or CONTRIBUTING.md
- **THEN** they find explanations of:
  - CWP prefix requirements for all global identifiers
  - Expected folder organization and file naming
  - How to run linting and formatting tools
  - How to fix common linting violations

#### Scenario: Editor configuration
- **WHEN** developer opens the project in an IDE (VSCode, PhpStorm, etc.)
- **THEN** .editorconfig, .prettierrc, and .eslintrc files automatically configure formatting for consistent code style
