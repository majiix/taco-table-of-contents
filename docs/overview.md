# Project Overview: Taco Table of Contents

Taco Table of Contents is a lightweight and performant WordPress plugin that automatically scans headings in post/page content and generates a Table of Contents (TOC). It features a modern client-side generation approach, saving server rendering overhead and keeping the page load time incredibly fast.

## Tech Stack
- **Backend**: PHP 7.4+
- **Frontend**: Vanilla JavaScript (ES6+), Vanilla CSS
- **WordPress Compatibility**: Designed for WordPress 5.0+, tested up to 7.1

## System Architecture

The plugin is structured in a clean, modular fashion to separate frontend loading from admin-specific settings:

- **Main File (`taco-table-of-contents.php`)**: Bootstrap and constant definitions. Includes components selectively.
- **Admin Config (`includes/admin.php`)**: Handles option registration via WordPress Settings API and builds the modern options panel.
- **Frontend Controller (`includes/frontend.php`)**: Enqueues CSS/JS assets, initializes the shortcode, and handles auto-insertion.
- **Assets (`assets/`)**:
  - `css/taco-admin.css` and `css/taco-toc.css`: Stylesheets for admin settings layout and the frontend TOC component.
  - `js/taco-toc.js`: Handles DOM parser selection, heading hierarchy building, scroll spy highlighting, and the collapsible toggle interactions.

## Verification
Verification should be completed manually by:
1. Activating the plugin.
2. Confirming that the Settings action link appears on the plugins list.
3. Accessing the Settings panel under Settings > Taco TOC to change settings.
4. Rendering a page with heading tags to verify layout, toggle expand/collapse, and active scroll spy highlighting.
