# Pet Tools Suite

A modular, performance-focused WordPress plugin architecture for building scalable interactive tools (calculators, predictors, utilities) on high-traffic sites.

---

## Why this exists

This repository demonstrates how I approach building **maintainable, scalable WordPress features** that are:

- Safe to run on high-traffic production sites  
- Easy for non-technical editors to use  
- Performance-conscious by default  
- Structured for long-term growth, not one-off solutions  

The architecture intentionally separates **business logic** from **WordPress integration**, making features easier to test, extend, and maintain as sites scale.

This mirrors real-world WordPress environments where tools may be embedded across many pages, reused by multiple teams, and expected to perform reliably under heavy traffic.

---

## Architecture Overview

The plugin is organized into clear, intentional layers:

- **Domain**  
  Pure PHP business logic with no WordPress dependencies. This layer is testable, reusable, and isolated from CMS concerns.

- **Infrastructure**  
  Shared technical concerns such as caching, analytics hooks, and logging.

- **WordPress Integration**  
  REST endpoints, shortcodes, blocks, admin settings, and asset registration.

- **Assets**  
  Front-end JavaScript and CSS, built once and loaded only when required.

All WordPress hooks are registered in a single entry point to keep side effects explicit, reviewable, and easy to reason about.

---

## Current Feature: Puppy Weight Growth Predictor

**Status:** In progress

Planned capabilities:

- Shortcode and Gutenberg block rendering  
- REST API endpoint for fast, reusable calculations  
- Caching of calculated results to improve performance  
- Editor-safe configuration via admin settings  
- Analytics hooks for usage tracking (e.g. dataLayer / GA4 events)  

The feature is designed as a reusable “tool” that could be deployed across many pages or sites without duplication.

---

## Folder Structure

```text
pet-tools-suite/
├─ pet-tools-suite.php          # Plugin bootstrap
├─ composer.json                # Composer configuration + autoloading
├─ readme.md
├─ src/
│  ├─ Plugin.php                # Main plugin orchestrator
│  ├─ Container.php             # Lightweight dependency container
│  ├─ Admin/                    # Admin settings and editor controls
│  ├─ Public/                   # Front-end hooks, assets, shortcodes, blocks
│  ├─ Domain/                   # Pure business logic (no WordPress)
│  │  └─ Calculator/
│  ├─ Rest/                     # REST API endpoints
│  ├─ Infrastructure/           # Caching, analytics, logging
│  └─ Support/                  # Shared helpers and sanitization
├─ assets/
│  ├─ src/                      # Source JS/CSS
│  │  ├─ js/
│  │  └─ css/
│  └─ dist/                     # Built assets
├─ tests/                       # PHPUnit tests
└─ .github/                     # CI / GitHub workflows


## Key Design Decisions

### Domain logic is WordPress-agnostic
All calculation and business logic lives outside of WordPress so it can be:

- Unit tested without bootstrapping WordPress
- Reused across delivery mechanisms (REST, shortcode, block)
- Modified without touching templates, hooks, or editor UI

### Explicit dependency management
A lightweight dependency container keeps dependencies explicit and avoids hidden global state, making the system easier to understand and extend.

### Performance-first approach
- REST responses are cacheable
- Front-end assets are minimal and loaded only when needed
- Designed to leverage persistent object caching when available, with transients as a fallback

---

## Development Setup

Install PHP dependencies:

```bash
composer install
composer dump-autoload


