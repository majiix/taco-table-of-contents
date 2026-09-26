<?php
/**
 * Frontend display and assets for Taco Table of Contents.
 *
 * @package Taco_Table_Of_Contents
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Register the JavaScript and CSS for the Table of Contents.
 *
 * @since 1.9.0
 * @return void
 */
function tacotoc_register_assets() {
	$handle = 'tacotoc-assets';

	// Register the CSS file.
	wp_register_style(
		$handle,
		TACOTOC_PLUGIN_URL . 'assets/css/taco-toc.css',
		array(),
		TACOTOC_VERSION
	);

	// Register the JS file.
	wp_register_script(
		$handle,
		TACOTOC_PLUGIN_URL . 'assets/js/taco-toc.js',
		array(),
		TACOTOC_VERSION,
		true
	);

	// Retrieve settings.
	$selector = (string) get_option( 'tacotoc_content_selector', '.entry-content' );

	// Retrieve allowed headings, default to h1, h2, h3.
	$headings_list = get_option( 'tacotoc_headings', array( 'h1', 'h2', 'h3' ) );
	if ( ! is_array( $headings_list ) || empty( $headings_list ) ) {
		$headings_list = array( 'h1', 'h2', 'h3' );
	}
	$headings_list = array_map( 'strval', $headings_list );
	$headings_str = implode( ', ', $headings_list );

	// Retrieve collapsible headings.
	$collapsible_list = get_option( 'tacotoc_collapsible_headings', array() );
	if ( ! is_array( $collapsible_list ) ) {
		$collapsible_list = array();
	}
	$collapsible_list = array_map( 'strval', $collapsible_list );
	$collapsible_str = implode( ',', $collapsible_list );

	// Retrieve Clean URLs setting, default to enabled (1).
	$clean_urls = (bool) get_option( 'tacotoc_clean_urls', 1 );

	// Prepare configuration to pass to JavaScript.
	$config_data = array(
		'selector'    => $selector,
		'headings'    => $headings_str,
		'collapsible' => $collapsible_str,
		'cleanUrls'   => $clean_urls,
		'title'       => __( 'Table of Contents', 'taco-table-of-contents' ),
	);

	// Localize the script with data.
	wp_localize_script( $handle, 'tacotoc_config', $config_data );

	// Check post types to auto-enqueue on singular post pages of allowed post types.
	$allowed_types = get_option( 'tacotoc_post_types', array( 'post', 'page' ) );
	if ( ! is_array( $allowed_types ) ) {
		$allowed_types = array( 'post', 'page' );
	}

	if ( is_singular( $allowed_types ) ) {
		wp_enqueue_style( $handle );
		wp_enqueue_script( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'tacotoc_register_assets' );

/**
 * Register the shortcode [taco_toc].
 *
 * Use this shortcode to manually place the TOC.
 *
 * @since 1.2.0
 * @return string HTML for the skeleton loader.
 */
function tacotoc_render_shortcode() {
	// Explicitly enqueue registered assets for manual shortcode use anywhere.
	wp_enqueue_style( 'tacotoc-assets' );
	wp_enqueue_script( 'tacotoc-assets' );

	// We use a class 'tacotoc-wrapper' which JS uses to find the container.
	ob_start();
	?>
	<div class="tacotoc-wrapper">
		<div class="tacotoc-skeleton-line tacotoc-skeleton-header"></div>
		<div class="tacotoc-skeleton-line tacotoc-skeleton-100"></div>
		<div class="tacotoc-skeleton-line tacotoc-skeleton-90"></div>
		<div class="tacotoc-skeleton-line tacotoc-skeleton-95"></div>
		<div class="tacotoc-skeleton-line tacotoc-skeleton-80"></div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'taco_toc', 'tacotoc_render_shortcode' );

/**
 * Automatically insert the TOC into the content based on settings.
 *
 * @since 1.6.0
 * @param string $content The post content.
 * @return string The modified content.
 */
function tacotoc_auto_insert_toc( $content ) {
	static $has_run = false;

	// Ensure content is a valid string.
	if ( ! is_string( $content ) ) {
		return $content;
	}

	// Basic checks: in loop, main query.
	if ( ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	// Ensure we only insert once per page load to avoid duplicate injections.
	if ( $has_run ) {
		return $content;
	}

	// Check post types.
	$allowed_types = get_option( 'tacotoc_post_types', array( 'post', 'page' ) );
	if ( ! is_array( $allowed_types ) ) {
		$allowed_types = array( 'post', 'page' );
	}

	if ( ! is_singular( $allowed_types ) ) {
		return $content;
	}

	$location = get_option( 'tacotoc_display_location', 'manual' );

	if ( 'manual' === $location ) {
		return $content;
	}

	$has_run = true;

	// Generate TOC HTML.
	$toc_html = tacotoc_render_shortcode();

	if ( 'before' === $location ) {
		return $toc_html . $content;
	} elseif ( 'after' === $location ) {
		return $content . $toc_html;
	}

	return $content;
}
add_filter( 'the_content', 'tacotoc_auto_insert_toc' );