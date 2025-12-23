# Pet Tools Suite

A modular, performance-focused WordPress plugin architecture for building scalable interactive tools (calculators, predictors, utilities) on high-traffic sites.

This repository demonstrates how I design and build **production-grade WordPress features** that remain maintainable as products, teams, and traffic grow.

---

## TL;DR (for recruiters & hiring managers)

This project demonstrates:

- Senior-level WordPress architecture (beyond theme-level scripting)
- Clean separation of business logic from CMS concerns
- Performance-aware design for high-traffic environments
- Editor-safe tooling for non-technical users
- A scalable foundation for multiple reusable tools
- Security-conscious implementation compatible with WAFs (e.g. Wordfence)

This is not a one-off plugin — it’s a **repeatable system**.

---

## Why this exists

Many WordPress “tools” start as page-specific scripts or shortcode callbacks and slowly accumulate:

- Tight coupling to WordPress globals
- Duplicated logic across pages
- Performance issues at scale
- Fragile editor experiences
- Difficult-to-test business logic

This repository demonstrates how I avoid those traps by designing WordPress features as **software systems**, not snippets.

The architecture intentionally separates business logic from WordPress integration, making features easier to test, extend, and maintain as sites scale.

This mirrors real-world WordPress environments where tools may be embedded across many pages, reused by multiple teams, and expected to perform reliably under heavy traffic.

---

## Architecture Overview

The plugin is organized into clear, intentional layers:

### Domain
Pure PHP business logic with **no WordPress dependencies**.  
Reusable, testable, and isolated from CMS concerns.

### Infrastructure
Shared technical concerns such as caching, analytics hooks, and logging.

### WordPress Integration
REST endpoints, shortcodes, blocks, admin settings, and asset registration.

### Assets
Front-end JavaScript and CSS, built once and loaded only when required.

All WordPress hooks are registered in a **single entry point**, keeping side effects explicit, reviewable, and easy to reason about.

---

## Architecture Diagram

```text
┌─────────────────────────────────────────┐
│              WordPress UI               │
│  (Shortcodes, Blocks, Admin Settings)   │
└───────────────▲─────────────────────────┘
                │
┌───────────────┴─────────────────────────┐
│          WordPress Integration           │
│   REST Controllers / Asset Loading       │
│   Permissions / Nonces / Escaping        │
└───────────────▲─────────────────────────┘
                │
┌───────────────┴─────────────────────────┐
│            Infrastructure                │
│   Caching / Analytics / Logging          │
└───────────────▲─────────────────────────┘
                │
┌───────────────┴─────────────────────────┐
│                Domain                    │
│   Pure PHP Business Logic (No WP)        │
│   Calculators / Predictors               │
└─────────────────────────────────────────┘
```

### Where It Can Be Used

- WordPress block editor (Shortcode block)
- WPBakery / Elementor text blocks
- Page templates
- Widget areas (if shortcodes are enabled)

All scripts and styles are enqueued by the plugin itself.  
No inline JavaScript is required in editor content.

## Key Design Decisions

### Domain Logic Is WordPress-Agnostic

All calculation and business logic lives outside of WordPress so it can be:

- Unit tested without bootstrapping WordPress
- Reused across REST, shortcodes, and blocks
- Modified without touching templates or editor UI

This dramatically improves long-term maintainability.

---

### Explicit Dependency Management

A lightweight dependency container keeps dependencies explicit and avoids hidden global state.

This makes the system:

- Easier to reason about
- Easier to extend with new services
- Safer to modify without unintended side effects

---

### Performance-First Approach

- REST responses are cacheable
- Front-end assets are minimal and conditionally loaded
- Designed to leverage persistent object caching when available
- Transients used as a fallback when object caching is unavailable

The goal is predictable performance even when tools are embedded across many pages.

---

## Why Not Just Use `functions.php`?

For small, disposable features, `functions.php` can be fine.

For **production tools**, it introduces long-term risk:

| functions.php | Pet Tools Suite |
|--------------|----------------|
| Global state | Explicit dependencies |
| Hard to test | Domain logic unit-testable |
| Page-coupled logic | Reusable modules |
| Fragile editor UX | Editor-safe configuration |
| Grows messy over time | Designed to scale |

This project reflects how WordPress is used in **real production environments**, not just tutorials.

---

## Development Setup

### Requirements

- PHP 8.0+
- Composer
- WordPress 6.0+

### Install Dependencies

```text
composer install
composer dump-autoload
```

### Local Development

1. Clone into your WordPress plugins directory:
```text
wp-content/plugins/pet-tools-suite
```

2. Activate the plugin via the WordPress admin.
3. Insert the tool using:
 ```text
   [pettools_puppy_weight]
 ```

### Testing
 ```text
vendor/bin/phpunit
 ```

## Security Considerations

- Input sanitized and validated before use
- Output consistently escaped
- No inline JavaScript injected via editor content
- REST endpoints protected with permission checks and nonces
- Compatible with common WAFs (e.g. Wordfence)

Security is treated as an architectural concern, not a patch.

---

## Future Direction

Planned improvements include:

- Additional calculators and predictors using the same architecture
- Shared UI primitives for consistent tool presentation
- More granular caching strategies
- Expanded test coverage and CI automation

---

## License

This repository is provided for demonstration and educational purposes.  
All rights reserved unless otherwise specified.



