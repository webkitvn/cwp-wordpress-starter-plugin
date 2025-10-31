<?php
/**
 * Asset Enqueueing Hooks
 *
 * Example hooks for registering and enqueueing scripts and styles.
 *
 * @package CWP\StarterPlugin
 */

namespace CWP\StarterPlugin\Hooks;

/**
 * Register custom scripts and styles.
 *
 * This is an example hook function showing how to extend the plugin
 * with additional scripts or styles.
 *
 * @return void
 */
function cwp_register_scripts_styles() {
	// Example: Register a custom admin script.
	// wp_register_script(
	//     'cwp-custom-admin-script',
	//     CWP_PLUGIN_URL . 'assets/custom-admin.js',
	//     array( 'jquery' ),
	//     CWP_PLUGIN_VERSION,
	//     true
	// );
}
add_action( 'admin_init', __NAMESPACE__ . '\\cwp_register_scripts_styles' );

/**
 * Example filter hook for modifying enqueued assets.
 *
 * @param array $scripts Array of registered scripts.
 * @return array Modified array of scripts.
 */
function cwp_filter_enqueued_scripts( $scripts ) {
	// Example: Modify or filter scripts before enqueueing.
	return $scripts;
}
add_filter( 'cwp_enqueued_scripts', __NAMESPACE__ . '\\cwp_filter_enqueued_scripts' );
