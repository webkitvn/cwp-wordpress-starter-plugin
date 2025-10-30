# Design: Plugin Foundation Scaffold

## Context

WordPress plugins often struggle with three foundational issues:
1. **Unclear Organization**: Mixed backend logic, admin screens, and frontend assets create maintenance friction
2. **Inconsistent Tooling**: No standardized build process for modern JavaScript/TypeScript leads to ad-hoc solutions
3. **Standards Drift**: Without enforced PHPCS rules and linting, code quality varies significantly

LocalWP (or similar local environments) already provides a development server, so Vite should be used purely for asset transpilation and bundling, not its development server functionality.

## Goals

- **Primary**: Provide a minimal, well-organized plugin skeleton ready for immediate custom development
- **Extensibility**: Structure enables developers to add features without restructuring
- **Standards Enforcement**: PHPCS, ESLint, and Prettier configured from day one
- **Clear Separation**: Backend (PHP) and frontend (TypeScript/CSS) codebases are clearly divided

## Non-Goals

- Admin framework abstractions (use WordPress REST API and native UI components)
- Complex build optimization (production build handles it; focus on developer experience)
- Testing framework setup (testing is orthogonal to scaffolding)
- Dependency injection containers or advanced OOP patterns

## Decisions

### 1. Directory Structure: Functional Separation

**Decision**: Use `/admin` (backend logic), `/public` (frontend PHP), `/src` (asset sources), `/assets` (compiled output)

**Rationale**:
- Mirrors WordPress plugin conventions with modern asset pipeline
- Clear mental model: `/admin` = backend, `/public` = frontend PHP, `/src` = TypeScript/SCSS sources, `/assets` = compiled
- Supports easy feature slicing later
- No redundancy (no `/admin/php` or `/public/js`)

**Structure**:
```
/admin                     # Backend PHP logic
  /class-plugin.php        # Main plugin class with hook registration
  /hooks/
    /enqueue.php           # Asset enqueueing hooks
    /admin-menu.php        # Admin menu and page hooks
    /rest-api.php          # REST endpoints (if used)

/public                    # Frontend PHP logic
  /shortcodes.php          # Shortcode registrations
  /template-tags.php       # Template helper functions

/src                       # TypeScript/SCSS sources (uncompiled)
  /admin                   # Admin screen assets
    /index.ts              # Main admin JavaScript entry point
    /styles.scss           # Admin-specific styles
  /public                  # User-facing assets
    /index.ts              # Main public JavaScript entry point
    /components/           # Reusable JavaScript components
      /modal.ts
      /form-handler.ts
    /styles.scss           # Public-specific styles
  /shared                  # Shared utilities between admin & public
    /utils.ts
    /constants.ts

/assets                    # Built output (compiled JavaScript, CSS)
  /admin.js                # Compiled admin JavaScript
  /admin.css               # Compiled admin styles
  /public.js               # Compiled public JavaScript
  /public.css              # Compiled public styles
```

### 2. Vite Build Strategy: Build-Only, No Dev Server

**Decision**: Configure Vite to transpile and bundle `/src/*` → `/assets/*` without running its development server.

**Rationale**:
- LocalWP/WordPress runs the development server
- Vite is purely for modern tooling (TypeScript, module bundling, CSS processing)
- Simplifies setup: no port conflicts, no dev server complexity

**Scripts**:
- `pnpm build` - Single production build
- `pnpm build:watch` - Watch mode for development (Vite watch, not server)

### 3. PHPCS Configuration: WordPress + CWP Prefix

**Decision**: Use WordPress-VIP-Go standard with custom prefix rules for `cwp_*` naming.

**Rationale**:
- WordPress-VIP-Go is the most comprehensive modern standard
- CWP prefix prevents namespace pollution
- Easy to enforce via CI/CD

### 4. Backend/Frontend Separation: Class-Based Hooks

**Decision**: Backend (PHP) uses a main `Plugin` class that registers hooks; frontend code in `/src` is independent, vanilla JavaScript with no framework.

**Rationale**:
- Class-based approach scales better than procedural hooks
- Easier to test and reason about data flow
- Vanilla JavaScript minimizes dependencies while supporting future framework adoption
- Supports REST API-first architecture if needed

**Pattern**:
```php
// admin/php/class-plugin.php
namespace CWP\StarterPlugin;

class Plugin {
    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_assets']);
    }

    public function enqueue_admin_assets() {
        wp_enqueue_script('cwp-admin', plugins_url('assets/admin.js', __FILE__), [], '1.0', true);
        wp_enqueue_style('cwp-admin', plugins_url('assets/admin.css', __FILE__), [], '1.0');
    }

    public function enqueue_public_assets() {
        wp_enqueue_script('cwp-public', plugins_url('assets/public.js', __FILE__), [], '1.0', true);
        wp_enqueue_style('cwp-public', plugins_url('assets/public.css', __FILE__), [], '1.0');
    }
}
```

### 5. CSS/SCSS Preprocessing: SCSS + PostCSS + Autoprefixer

**Decision**: Use SCSS with PostCSS and Autoprefixer for modern CSS processing.

**Rationale**:
- SCSS provides variables, mixins, nesting—industry standard
- PostCSS handles vendor prefixes automatically
- Minimal additional complexity; Vite handles integration
- Easy to migrate to plain CSS if needed later

### 6. JavaScript Approach: Vanilla JavaScript (Framework-Agnostic)

**Decision**: No framework by default; developers can add React/Vue/Web Components as needed.

**Rationale**:
- Minimal dependencies keeps scaffold lean
- Vanilla JS code is more portable and maintainable
- Clear documentation for adding frameworks later
- Easier for non-JavaScript-heavy teams

### 7. REST API Pattern: Documented Extension (Not in Scaffold)

**Decision**: No REST endpoint code in core scaffold; provide documentation pattern for developers to extend.

**Rationale**:
- Not all plugins need REST API
- Keeps core scaffold focused on essentials
- Pattern documentation enables easy implementation when needed
- Reduces learning curve for non-API plugins

### 8. Package Management: pnpm + Composer

## Risks & Trade-offs

| Risk | Mitigation |
|------|-----------|
| Directory nesting may feel deep | Encourage flat feature structure within folders; document conventions |
| Vite watch mode requires extra process | Document as part of local dev workflow; consider pnpm scripts orchestration |
| PHPCS rules may be too strict initially | Use `phpcs --show-sources` to identify rules; relax if necessary after first project |

## Migration Plan

No migration needed for a fresh scaffold. If expanding into existing plugin:
1. Adopt the directory structure incrementally
2. Move PHP files into `/admin` as they stabilize
3. Migrate asset sources to `/src` with Vite
4. Update enqueue hooks to point to new asset paths

## Technical Decisions

### Why Vite over Webpack/Rollup?
- Faster local builds and clearer config
- Excellent TypeScript support out of the box
- Minimal configuration for WordPress use case

### Why not use wp-scripts?
- @wordpress/scripts is excellent but opinionated toward block development
- Vite gives more control for mixed plugin types
- Lighter dependency footprint

### Why TypeScript with Strict Mode from start?
- Better IDE support and fewer runtime errors
- Enforces explicit type annotations, catching bugs early
- Easier to enforce standards in team environments
- Can generate `.d.ts` files for API documentation

### Why Class-Based Hooks over Procedural?
- Scales better as plugin grows
- Easier to test in isolation
- Clearer dependency relationships
- Pattern familiar to WordPress developers

### Why Vanilla JavaScript First?
- Minimal dependencies, easier maintenance
- Code stays portable across environments
- Team can add frameworks (React/Vue) later without refactoring
- Reduces complexity for smaller plugins

## Open Questions

- **Question**: Should we include WordPress plugin dependencies (e.g., `@wordpress/api-fetch`) in base template?
  - **Answer**: Add to `package.json` but leave usage optional; developers import only what they need.

- **Question**: Should PHPCS be run as part of pnpm scripts or separate command?
  - **Answer**: Both—add `pnpm lint:php` that runs `phpcs`, but keep CLI tool available for integration with IDEs.
