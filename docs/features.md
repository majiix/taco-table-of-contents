# Taco Table of Contents Features

Here is a list of features supported by the Taco Table of Contents plugin:

## Generation & Detection
- **Auto-Detection**: Scans the post or page content for heading tags (configurable from `<h1>` to `<h6>`).
- **CSS Selector Mapping**: Allows defining a custom content container selector class or ID (e.g. `.entry-content`).

## Styling & Display
- **Skeleton Loading State**: Renders CSS-animated placeholders while the script parses the document headings client-side.
- **Customizable Layout**: Can automatically insert the Table of Contents before or after the content, or output manually using a shortcode.

## Interaction
- **Collapsible Sub-Headings**: Allows choosing specific heading levels to collapse by default under their parents, displaying expandable +/- toggles.
- **Scroll Tracking (Scroll Spy)**: Tracks scrolling position on the page and highlights the active heading inside the TOC.
- **Direct Anchor Navigation**: Resolves page URLs containing header hashes on load and scrolls directly to the targeted section.

## SEO & URL Hygiene
- **Clean URLs & SEO Mode**: Eliminates URL anchor fragment indexation and keyword cannibalization by intercepting TOC clicks with smooth scrolling without mutating the browser address bar.
- **Crawler Hinting (rel="nofollow")**: Adds `rel="nofollow"` to generated TOC anchor tags to prevent search crawlers from mapping intra-page fragments as separate landing targets.
- **Automatic Hash Sanitization**: Strips incoming URL hashes via `history.replaceState` on load after scrolling to the target heading, preventing visitors from re-sharing fragmented links.
