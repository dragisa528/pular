<?php
/**
 * WP and PHP compatibility.
 *
 * Functions used to gracefully fail when a theme doesn't meet the minimum WP or
 * PHP versions required. Note that only code that will work on PHP 5.2.4 should
 * go into this file. Otherwise, it'll break on sites not meeting the minimum
 * PHP requirement. Only call this file after initially checking that the site
 * doesn't meet either the WP or PHP requirement.
 *
 * @package NewTheme
 */

// Add actions to fail at certain points in the load process.
add_action( 'after_switch_theme', 'new_theme_switch_theme' );
add_action( 'template_redirect', 'new_theme_preview' );

/**
 * Returns the compatibility messaged based on whether the WP or PHP minimum
 * requirement wasn't met.
 *
 * @access public
 * @return mixed
 */
function new_theme_compat_message() {
	if ( version_compare( $GLOBALS['wp_version'], '5.8', '<' ) ) {

		return sprintf(
			/* translators: %1$s is supported WordPress version, and %2$s is WordPress version used. */
			esc_html__( 'Theme requires at least WordPress version %1$s. You are running version %2$s. Please upgrade and try again.', 'new-theme' ),
			'5.8',
			$GLOBALS['wp_version']
		);

	} elseif ( version_compare( PHP_VERSION, '7.4', '<' ) ) {

		return sprintf(
			/* translators: %1$s is supported PHP version, and %2$s is PHP version used. */
			esc_html__( 'Theme requires at least PHP version %1$s. You are running version %2$s. Please upgrade and try again.', 'new-theme' ),
			'7.4',
			PHP_VERSION
		);
	}

	return '';
}

/**
 * Switches to the previously active theme after the theme has been activated.
 *
 * @access public
 * @param  string $old_name Old name of the theme.
 * @return void
 */
function new_theme_switch_theme( $old_name ) {

	switch_theme( $old_name ? $old_name : WP_DEFAULT_THEME );

	unset( $_GET['activated'] );

	add_action( 'admin_notices', 'new_theme_upgrade_notice' );
}

/**
 * Outputs an admin notice with the compatibility issue.
 *
 * @access public
 * @return void
 */
function new_theme_upgrade_notice() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( new_theme_compat_message() ) );
}

/**
 * Kills the customizer previewer on installs prior to WP 5.8.
 *
 * @access public
 * @return void
 */
function new_theme_preview() {
	if ( isset( $_GET['preview'] ) ) { // WPCS: CSRF ok.
		wp_die( esc_html( new_theme_compat_message() ) );
	}
}