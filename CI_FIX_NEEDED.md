# CI/CD Check Script Issues

## Problem

The automated check scripts use `git status --short -z` with `xargs` to pass files to linters. However, `git status --short` outputs the status prefix (e.g., "M  " for modified files, "A  " for added files) along with the filename.

When piped to `xargs`, the entire string including the status prefix is passed to the linter, causing errors like:
```
No files matching the pattern "M  vite.config.ts" were found.
```

## Affected Commands

1. ESLint check: `git status --short -z -- "*.ts" "*.tsx" | xargs -0 -r pnpm exec eslint --ext .ts,.tsx --max-warnings=0`
2. Stylelint check: `git status --short -z -- "*.css" "*.scss" | xargs -0 -r pnpm exec stylelint`
3. PHPCS check: `git status --short -z -- "*.php" | xargs -0 -r ./vendor/bin/phpcs`

## Solution Options

### Option 1: Use `git diff` instead
```bash
git diff --name-only --diff-filter=ACMR --cached -z -- "*.ts" "*.tsx" | xargs -0 -r pnpm exec eslint --ext .ts,.tsx --max-warnings=0
```

### Option 2: Strip the status prefix with `cut`
```bash
git status --porcelain -- "*.ts" "*.tsx" | cut -c4- | xargs -r pnpm exec eslint --ext .ts,.tsx --max-warnings=0
```
Note: Cannot use `-z` flag with `cut`, so this is less reliable for filenames with spaces.

### Option 3: Use `awk` to extract filenames
```bash
git status --short -- "*.ts" "*.tsx" | awk '{print $NF}' | xargs -r pnpm exec eslint --ext .ts,.tsx --max-warnings=0
```

### Option 4: Use `git ls-files` for tracked files
```bash
git ls-files -m -- "*.ts" "*.tsx" | xargs -r pnpm exec eslint --ext .ts,.tsx --max-warnings=0
```

## Recommended Fix

Use **Option 1** (git diff) as it's the most reliable and handles null-terminated output correctly:

```bash
# TypeScript/ESLint
git diff --name-only --diff-filter=ACMR --cached -z -- "*.ts" "*.tsx" | xargs -0 -r pnpm exec eslint --ext .ts,.tsx --max-warnings=0

# CSS/Stylelint  
git diff --name-only --diff-filter=ACMR --cached -z -- "*.css" "*.scss" | xargs -0 -r pnpm exec stylelint

# PHP/PHPCS
git diff --name-only --diff-filter=ACMR --cached -z -- "*.php" | xargs -0 -r ./vendor/bin/phpcs
```

## Verification

All linters pass when run directly on the files:
```bash
✓ pnpm exec eslint vite.config.ts --ext .ts --max-warnings=0
✓ ./vendor/bin/phpcs admin/php/class-plugin.php (only 1 warning about unused parameter)
✓ pnpm build (completes successfully)
```

The issue is purely in how the CI/check script invokes the linters, not in the code or configurations themselves.
