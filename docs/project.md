# Taco Table of Contents

## Overview
Taco Table of Contents is a lightweight, performance-focused WordPress plugin that automatically generates a responsive, collapsible Table of Contents from post/page headings with smooth scrolling, active scroll spying, and built-in SEO clean-URL protections.

## Tech Stack
- **PHP**: 7.4+ (WordPress plugin standard)
- **JavaScript**: Vanilla ES6+ (no external dependencies, zero jQuery requirement)
- **CSS**: Modern CSS with CSS variables and responsive rules
- **Platform**: WordPress 5.8+

## Dependencies
- WordPress Core (Settings API, Shortcode API, Script Localization)
- No third-party PHP packages or JS libraries required

## Architecture
- `taco-table-of-contents.php`: Plugin entry point, constant definitions, and bootstrap loader.
- `includes/admin.php`: Admin menu, Settings API registration, field render callbacks, and sanitization logic.
- `includes/frontend.php`: Frontend asset registration, shortcode (`[taco_toc]`), and auto-insertion filter (`the_content`).
- `assets/js/taco-toc.js`: Client-side heading parser, DOM hierarchy generator, collapsible toggle handlers, smooth scrolling, scroll-spy highlight tracker, and URL hash sanitization.
- `assets/css/taco-toc.css`: Frontend presentation, nesting indentation, toggle icons, and skeleton loading animations.
- `assets/css/taco-admin.css`: Admin settings page grid, card layout, and form element styles.

## Current Features
- **Auto-Detection**: Scans headings (`<h1>` to `<h6>`) within configurable content containers.
- **Custom Selectors**: Configurable content container selector (e.g. `.entry-content`).
- **Flexible Placement**: Automatic insertion before/after post content, or manual placement using `[taco_toc]`.
- **Skeleton Loader**: Zero Cumulative Layout Shift (CLS) with lightweight animated placeholder skeletons.
- **Collapsible Headings**: Interactive +/- toggles with selectable collapsible heading tiers.
- **Scroll Spy**: Tracks viewport position and highlights the currently read heading in the TOC.
- **Clean URLs & SEO Mode**: Eliminates URL anchor fragment indexation and keyword cannibalization:
  - Intercepts clicks and performs offset-aware smooth scrolling without mutating the address bar.
  - Adds `rel="nofollow"` to TOC links to suppress crawler fragment mapping.
  - Automatically sanitizes incoming `#hashes` on load using `history.replaceState`.
  - Configurable admin toggle under Settings > Taco TOC.

## Verification Commands
Run syntax and linting checks:
```powershell
rtk php -l includes/admin.php
rtk php -l includes/frontend.php
rtk node --check assets/js/taco-toc.js
```
