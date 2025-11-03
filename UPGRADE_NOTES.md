# Linting Tools Upgrade Notes

## Summary

This document describes the upgrade of JavaScript linting and PHP_CodeSniffer tools to their latest stable versions.

## JavaScript/ESLint Upgrades

### Packages Upgraded

- **ESLint**: 8.57.1 → 9.39.0 (major version)
- **@wordpress/eslint-plugin**: 17.13.0 → 22.20.0
- **@typescript-eslint/eslint-plugin**: 6.21.0 → 8.46.2
- **@typescript-eslint/parser**: 6.21.0 → 8.46.2

### New Dependencies Added

- `@eslint/js` ^9.39.0
- `@eslint/compat` ^1.4.1
- `@eslint/eslintrc` ^3.3.1
- `typescript-eslint` ^8.46.2
- `eslint-plugin-import` ^2.32.0
- `eslint-plugin-jsdoc` ^61.1.12
- `eslint-plugin-jsx-a11y` ^6.10.2
- `eslint-plugin-react` ^7.37.5
- `eslint-plugin-react-hooks` ^7.0.1
- `eslint-import-resolver-typescript` ^4.4.4
- `eslint-import-resolver-node` ^0.3.9

### Configuration Changes

#### ESLint 9 Flat Config Migration

ESLint 9 introduces a new "flat config" format. Since @wordpress/eslint-plugin doesn't natively support ESLint 9 flat config yet, we use the `@eslint/compat` and `@eslint/eslintrc` compatibility layer.

**Changes made:**

1. **Removed**: `.eslintrc.cjs`
2. **Added**: `eslint.config.js` (new flat config format)
3. **Updated**: `package.json` lint:js script to `eslint src` (removed `--ext` flag, no longer needed with flat config)

The new `eslint.config.js` uses the `FlatCompat` class to translate legacy ESLint configs to the new flat format, ensuring backward compatibility with @wordpress/eslint-plugin.

### Import Resolution

Added TypeScript path resolution for imports using:
- `eslint-import-resolver-typescript` for TypeScript path mapping support
- `eslint-import-resolver-node` for Node.js module resolution

Configuration in `eslint.config.js` includes:
```javascript
settings: {
  'import/resolver': {
    typescript: {
      alwaysTryTypes: true,
      project: './tsconfig.json',
    },
    node: true,
  },
}
```

## PHP_CodeSniffer Upgrades

### Packages Upgraded

- **squizlabs/php_codesniffer**: ^3.7 → ^3.13 (latest 3.x, currently 3.13.4)
- **wp-coding-standards/wpcs**: ^3.0 → ^3.2 (latest, currently 3.2.0)

### Configuration Changes

#### Updated Installed Paths

The composer.json post-install and post-update scripts now include the PHPCSStandards dependencies:

```json
"post-install-cmd": [
  "vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs,vendor/phpcompatibility/php-compatibility,vendor/phpcsstandards/phpcsextra,vendor/phpcsstandards/phpcsutils"
]
```

These additional paths (`phpcsextra` and `phpcsutils`) are required dependencies for wpcs 3.2.0 to function correctly.

### PHPCS Configuration

The `.phpcs.xml.dist` configuration remains compatible with the new versions. No changes were required.

## Breaking Changes and Compatibility

### ESLint 9

- **Flat Config Required**: ESLint 9 no longer supports `.eslintrc.*` files by default
- **Plugin Loading**: Plugins must be explicitly imported in flat config
- **Compatibility Layer**: Used `@eslint/compat` to maintain compatibility with legacy configs

### Peer Dependency Warnings

Some peer dependency warnings appear because @wordpress/eslint-plugin internally depends on older versions of @typescript-eslint packages (v6 vs our v8). These warnings are expected and don't affect functionality, as the compatibility layer handles the version differences.

## Testing

Both linting tools continue to work correctly:

```bash
# Test JavaScript linting
pnpm lint:js

# Test PHP linting  
composer lint

# Test all linting
pnpm lint
```

## Future Considerations

1. **ESLint 9 Native Support**: When @wordpress/eslint-plugin adds native ESLint 9 flat config support, we can remove the compatibility layer and simplify `eslint.config.js`.

2. **PHP_CodeSniffer 4.x**: PHP_CodeSniffer 4.x is available and wpcs 3.2.0 supports it (`^3.13.4 || ^4.0`). Consider upgrading when needed, though 3.13.4 is the latest stable 3.x version and works well.

3. **Peer Dependency Resolution**: Future versions of @wordpress/eslint-plugin may update their @typescript-eslint dependencies to match the latest versions, eliminating the current peer dependency warnings.
