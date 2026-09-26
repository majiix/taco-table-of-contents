<?php
/**
 * Plugin Name: Taco Table of Contents
 * Plugin URI:  https://wordpress.org/plugins/taco-table-of-contents
 * Description: Generates a Table of Contents from heading tags. Supports auto-insertion and manual shortcode placement. Active item highlights on scroll.
 * Version:     1.10.0
 * Author:      micromax
 * Text Domain: taco-table-of-contents
 * Domain Path: /languages
 * License:     GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin constants for paths and URLs.
define( 'TACOTOC_VERSION', '1.10.0' );
define( 'TACOTOC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TACOTOC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Include the frontend logic (shortcodes, assets).
 */
require_once TACOTOC_PLUGIN_DIR . 'includes/frontend.php';

/**
 * Include the admin logic (settings, menus) only if in admin area.
 */
if ( is_admin() ) {
	require_once TACOTOC_PLUGIN_DIR . 'includes/admin.php';
}