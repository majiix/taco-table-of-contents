step 1:
1- Initialized project-scoped documentation under docs directory.
2- Added overview.md, features.md, and changelog.md.
3- Refactored admin settings options checked fields to use the native WordPress checked() helper function.
4- Added a "Settings" filter link next to the plugin name on the plugins administration list page.
5- Modernized the admin UI dashboard panel style for a polished look.
6- Elevate the styling of the frontend Table of Contents container with refined layout, shadow effects, and seamless hover effects.

step 2:
1- Bumped plugin version to 1.9.1 in main header, constants, and stable tags.
2- Appended 1.9.1 release details to readme.txt.

step 3:
1- Added hierarchical numbering (using hyphens for subheadings) to Table of Contents list items in assets/js/taco-toc.js.
2- Styled the new section numbers in assets/css/taco-toc.css using #475569 color for clean horizontal alignment and active state highlights.
3- Bumped plugin version to 1.9.2 in main plugin file, constants, readme.txt, and changelog.md.

step 4:
1- Hardened option inputs and settings sanitization logic in admin.php to filter out non-scalar input, preventing TypeError fatals on PHP 8.0+.
2- Cast settings option values to string/array explicitly in admin.php and frontend.php to avoid PHP 8 TypeErrors.
3- Added try...catch error handling to DOM querySelector calls in taco-toc.js to prevent JS execution halting on invalid selectors.
4- Wrapped URL hash decoding in decodeURIComponent with try...catch to handle Unicode headings correctly and prevent URIErrors.
5- Implemented requestAnimationFrame throttling for scroll event spy highlighting to improve page scrolling performance.
6- Added duplicate ID resolution registry to prevent duplicate HTML element IDs on the same page.
7- Bumped plugin version to 1.9.3 across all relevant files (main file header, constants, readme.txt, and docs/changelog.md).
Commit message: refactor(core): harden codebase to prevent PHP/JS errors and optimize scroll spy performance

step 5:
1- Audited codebase against WordPress 7.1.0 Field Guide specifications.
2- Confirmed full compatibility with persistent admin toolbar, enforced iframed editor, and Settings API updates.
3- Bumped plugin version to 1.9.4 in taco-table-of-contents.php, constants, readme.txt, and docs.
4- Updated "Tested up to" WordPress version to 7.1 across documentation and readme.txt.

step 6:
1- Added Clean URLs & SEO Mode setting toggle in admin panel with boolean sanitization.
2- Localized cleanUrls option to frontend script.
3- Added rel="nofollow" to generated Table of Contents links to suppress crawler fragment mapping.
4- Intercepted TOC link clicks with offset-aware smooth scrolling without mutating the browser address bar.
5- Implemented automatic URL hash sanitization via history.replaceState on load after scrolling to target heading.
6- Created docs/project.md covering overview, tech stack, architecture, and verification.
7- Bumped plugin version to 1.10.0 across all relevant files (main file header, constants, readme.txt, and docs/changelog.md).
