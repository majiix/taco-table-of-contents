<?php
/**
 * Admin specific functionality for Taco Table of Contents.
 *
 * @package Taco_Table_Of_Contents
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Enqueue admin-specific styles.
 *
 * @since 1.7.0
 * @param string $hook The current admin page hook.
 * @return void
 */
function tacotoc_enqueue_admin_assets( $hook ) {
	// Only load on our settings page.
	if ( 'settings_page_tacotoc-settings' !== $hook ) {
		return;
	}

	wp_enqueue_style(
		'tacotoc-admin-css',
		TACOTOC_PLUGIN_URL . 'assets/css/taco-admin.css',
		array(),
		TACOTOC_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'tacotoc_enqueue_admin_assets' );

/**
 * Register the settings for the plugin.
 *
 * @since 1.1.0
 * @return void
 */
function tacotoc_register_settings() {
	// Register content selector setting.
	register_setting(
		'tacotoc_options_group',
		'tacotoc_content_selector',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '.entry-content',
		)
	);

	// Register post types setting with strict validation.
	register_setting(
		'tacotoc_options_group',
		'tacotoc_post_types',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'tacotoc_sanitize_post_types',
			'default'           => array( 'post', 'page' ),
		)
	);

	// Register headings setting.
	register_setting(
		'tacotoc_options_group',
		'tacotoc_headings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'tacotoc_sanitize_headings',
			'default'           => array( 'h1', 'h2', 'h3' ),
		)
	);

	// Register collapsible headings setting.
	register_setting(
		'tacotoc_options_group',
		'tacotoc_collapsible_headings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'tacotoc_sanitize_headings',
			'default'           => array(),
		)
	);

	// Register display location setting.
	register_setting(
		'tacotoc_options_group',
		'tacotoc_display_location',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'manual',
		)
	);

	// Register Clean URLs & SEO Mode setting.
	register_setting(
		'tacotoc_options_group',
		'tacotoc_clean_urls',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'tacotoc_sanitize_checkbox',
			'default'           => 1,
		)
	);

	add_settings_section(
		'tacotoc_main_section',
		__( 'Configuration', 'taco-table-of-contents' ),
		'tacotoc_main_section_callback',
		'tacotoc-settings-page'
	);

	add_settings_field(
		'tacotoc_display_location',
		__( 'Display Location', 'taco-table-of-contents' ),
		'tacotoc_display_location_render',
		'tacotoc-settings-page',
		'tacotoc_main_section'
	);

	add_settings_field(
		'tacotoc_clean_urls',
		__( 'Clean URLs & SEO Mode', 'taco-table-of-contents' ),
		'tacotoc_clean_urls_field_render',
		'tacotoc-settings-page',
		'tacotoc_main_section'
	);

	add_settings_field(
		'tacotoc_headings',
		__( 'Headings to Include', 'taco-table-of-contents' ),
		'tacotoc_headings_field_render',
		'tacotoc-settings-page',
		'tacotoc_main_section'
	);

	add_settings_field(
		'tacotoc_collapsible_headings',
		__( 'Collapsible Headings', 'taco-table-of-contents' ),
		'tacotoc_collapsible_headings_field_render',
		'tacotoc-settings-page',
		'tacotoc_main_section'
	);

	add_settings_field(
		'tacotoc_post_types',
		__( 'Enable on Post Types', 'taco-table-of-contents' ),
		'tacotoc_post_types_field_render',
		'tacotoc-settings-page',
		'tacotoc_main_section'
	);

	add_settings_field(
		'tacotoc_content_selector',
		__( 'Content Selector', 'taco-table-of-contents' ),
		'tacotoc_selector_field_render',
		'tacotoc-settings-page',
		'tacotoc_main_section'
	);
}
add_action( 'admin_init', 'tacotoc_register_settings' );

/**
 * Sanitize the post types checkbox array against a whitelist of valid post types.
 *
 * @since 1.7.1
 * @param array $input The raw input array from the form.
 * @return array The sanitized array of strings.
 */
function tacotoc_sanitize_post_types( $input ) {
	if ( ! is_array( $input ) ) {
		return array();
	}

	// Retrieve a whitelist of valid public post types available on the site.
	$args = array(
		'public' => true,
	);
	$allowed_post_types = get_post_types( $args, 'names' );

	// Sanitize raw input values to keys, ensuring they are scalar.
	$sanitized_input = array();
	foreach ( $input as $value ) {
		if ( is_scalar( $value ) ) {
			$sanitized_input[] = sanitize_key( (string) $value );
		}
	}

	// Intersect the arrays to ensure we only save valid, existing post types.
	return array_intersect( $sanitized_input, $allowed_post_types );
}

/**
 * Sanitize the headings checkbox array.
 *
 * @since 1.5.2
 * @param array $input The raw input array from the form.
 * @return array The sanitized array of strings.
 */
function tacotoc_sanitize_headings( $input ) {
	if ( ! is_array( $input ) ) {
		return array();
	}
	$sanitized_input = array();
	foreach ( $input as $value ) {
		if ( is_scalar( $value ) ) {
			$sanitized_input[] = (string) $value;
		}
	}
	$allowed = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
	return array_intersect( $sanitized_input, $allowed );
}

/**
 * Sanitize a checkbox input to an integer 1 or 0.
 *
 * @since 1.10.0
 * @param mixed $input Raw input value.
 * @return int 1 if checked, 0 otherwise.
 */
function tacotoc_sanitize_checkbox( $input ) {
	return ! empty( $input ) ? 1 : 0;
}

/**
 * Render the description for the main settings section.
 *
 * @since 1.1.0
 * @return void
 */
function tacotoc_main_section_callback() {
	esc_html_e( 'Customize how the Table of Contents appears on your site.', 'taco-table-of-contents' );
}

/**
 * Render the input field for the content selector.
 */
function tacotoc_selector_field_render() {
	$option = (string) get_option( 'tacotoc_content_selector', '.entry-content' );
	?>
	<input type="text"
		name="tacotoc_content_selector"
		value="<?php echo esc_attr( $option ); ?>"
		class="regular-text"
		placeholder=".entry-content" />
	<p class="description">
		<?php esc_html_e( 'The CSS class or ID of the text wrapper (e.g., .entry-content, #main).', 'taco-table-of-contents' ); ?>
	</p>
	<?php
}

/**
 * Render the checkboxes for selecting post types.
 */
function tacotoc_post_types_field_render() {
	$options = get_option( 'tacotoc_post_types', array( 'post', 'page' ) );
	if ( ! is_array( $options ) ) {
		$options = array( 'post', 'page' );
	}

	$args = array(
		'public' => true,
	);
	$post_types = get_post_types( $args, 'objects' );

	echo '<div class="tacotoc-checkbox-grid">';
	foreach ( $post_types as $post_type ) {
		if ( 'attachment' === $post_type->name ) {
			continue;
		}

		?>
		<label class="tacotoc-checkbox-label">
			<input type="checkbox" name="tacotoc_post_types[]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $options, true ) ); ?>>
			<?php echo esc_html( $post_type->label ); ?>
		</label>
		<?php
	}
	echo '</div>';
}

/**
 * Render the checkboxes for selecting headings.
 */
function tacotoc_headings_field_render() {
	$options = get_option( 'tacotoc_headings', array( 'h1', 'h2', 'h3' ) );
	if ( ! is_array( $options ) ) {
		$options = array( 'h1', 'h2', 'h3' );
	}

	$headings = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );

	echo '<div class="tacotoc-checkbox-grid">';
	foreach ( $headings as $heading ) {
		?>
		<label class="tacotoc-checkbox-label">
			<input type="checkbox" name="tacotoc_headings[]" value="<?php echo esc_attr( $heading ); ?>" <?php checked( in_array( $heading, $options, true ) ); ?>>
			<?php echo esc_html( strtoupper( $heading ) ); ?>
		</label>
		<?php
	}
	echo '</div>';
}

/**
 * Render the checkboxes for selecting collapsible headings.
 */
function tacotoc_collapsible_headings_field_render() {
	$options = get_option( 'tacotoc_collapsible_headings', array() );
	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$headings = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );

	echo '<div class="tacotoc-checkbox-grid">';
	foreach ( $headings as $heading ) {
		?>
		<label class="tacotoc-checkbox-label">
			<input type="checkbox" name="tacotoc_collapsible_headings[]" value="<?php echo esc_attr( $heading ); ?>" <?php checked( in_array( $heading, $options, true ) ); ?>>
			<?php echo esc_html( strtoupper( $heading ) ); ?>
		</label>
		<?php
	}
	echo '</div>';
	echo '<p class="description">' . esc_html__( 'Select the headings that should be collapsed under their parent tag by default. A toggle icon (+/-) will be added to the parent.', 'taco-table-of-contents' ) . '</p>';
}

/**
 * Render the display location select.
 */
function tacotoc_display_location_render() {
	$option = (string) get_option( 'tacotoc_display_location', 'manual' );
	?>
	<select name="tacotoc_display_location" class="tacotoc-select">
		<option value="manual" <?php selected( 'manual', $option ); ?>><?php esc_html_e( 'Manual (Shortcode Only)', 'taco-table-of-contents' ); ?></option>
		<option value="before" <?php selected( 'before', $option ); ?>><?php esc_html_e( 'Auto Insert - Before Content', 'taco-table-of-contents' ); ?></option>
		<option value="after" <?php selected( 'after', $option ); ?>><?php esc_html_e( 'Auto Insert - After Content', 'taco-table-of-contents' ); ?></option>
	</select>
	<p class="description">
		<?php esc_html_e( 'Where should the Table of Contents appear automatically?', 'taco-table-of-contents' ); ?>
	</p>
	<?php
}

/**
 * Render the checkbox for Clean URLs & SEO Mode.
 *
 * @since 1.10.0
 * @return void
 */
function tacotoc_clean_urls_field_render() {
	$option = (bool) get_option( 'tacotoc_clean_urls', 1 );
	?>
	<label class="tacotoc-checkbox-label">
		<input type="checkbox" name="tacotoc_clean_urls" value="1" <?php checked( $option ); ?>>
		<?php esc_html_e( 'Prevent URL hash mutation in the address bar & add rel="nofollow"', 'taco-table-of-contents' ); ?>
	</label>
	<p class="description">
		<?php esc_html_e( 'Eliminates URL anchor fragment indexation and keyword cannibalization by keeping the browser address bar clean and preventing crawlers from mapping internal fragments as separate landing pages.', 'taco-table-of-contents' ); ?>
	</p>
	<?php
}

/**
 * Add the settings page to the admin menu.
 *
 * @since 1.1.0
 * @return void
 */
function tacotoc_add_admin_menu() {
	add_options_page(
		__( 'Taco TOC Settings', 'taco-table-of-contents' ),
		__( 'Taco TOC', 'taco-table-of-contents' ),
		'manage_options',
		'tacotoc-settings',
		'tacotoc_render_settings_page'
	);
}
add_action( 'admin_menu', 'tacotoc_add_admin_menu' );

/**
 * Add settings action link to the plugins page.
 *
 * @since 1.9.1
 * @param array $links Array of plugin action links.
 * @return array Modified links.
 */
function tacotoc_add_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=tacotoc-settings">' . __( 'Settings', 'taco-table-of-contents' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( TACOTOC_PLUGIN_DIR . 'taco-table-of-contents.php' ), 'tacotoc_add_settings_link' );

/**
 * Render the HTML for the settings page with a modern layout.
 *
 * @since 1.1.0
 * @return void
 */
function tacotoc_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap tacotoc-admin-wrapper">
		<div class="tacotoc-header">
			<h1><?php echo esc_html( get_admin_page_title() ); ?> <span class="tacotoc-version">v<?php echo esc_html( TACOTOC_VERSION ); ?></span></h1>
		</div>

		<div class="tacotoc-container">
			<!-- Main Settings Column -->
			<div class="tacotoc-main-column">
				<form action="options.php" method="post" class="tacotoc-card">
					<?php
					settings_fields( 'tacotoc_options_group' );
					do_settings_sections( 'tacotoc-settings-page' );
					submit_button( __( 'Save Changes', 'taco-table-of-contents' ), 'primary large' );
					?>
				</form>
			</div>

			<!-- Sidebar Help Column -->
			<div class="tacotoc-sidebar-column">
				<div class="tacotoc-card tacotoc-help-card">
					<h3 class="tacotoc-card-title"><?php esc_html_e( 'How to Use', 'taco-table-of-contents' ); ?></h3>

					<div class="tacotoc-help-item">
						<h4><?php esc_html_e( '1. Auto Insertion', 'taco-table-of-contents' ); ?></h4>
						<p><?php esc_html_e( 'Change "Display Location" to "Before Content" to automatically show the TOC on all enabled post types.', 'taco-table-of-contents' ); ?></p>
					</div>

					<div class="tacotoc-help-item">
						<h4><?php esc_html_e( '2. Manual Placement', 'taco-table-of-contents' ); ?></h4>
						<p><?php esc_html_e( 'Use the shortcode anywhere in your content:', 'taco-table-of-contents' ); ?></p>
						<code class="tacotoc-code">[taco_toc]</code>
					</div>

					<div class="tacotoc-help-item">
						<h4><?php esc_html_e( 'Troubleshooting', 'taco-table-of-contents' ); ?></h4>
						<p><?php esc_html_e( 'If the TOC is empty, check the "Content Selector". It must match the CSS class of the div wrapping your post content (e.g., .entry-content).', 'taco-table-of-contents' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}