<?php
/**
 * Plugin Name: CWP WordPress Starter Plugin
 * Plugin URI: https://example.com/cwp-wordpress-starter-plugin
 * Description: A modern WordPress plugin starter with Vite, TypeScript, and coding standards enforcement
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cwp-starter-plugin
 * Domain Path: /languages
 *
 * @package CWP\StarterPlugin
 */

namespace CWP\StarterPlugin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'CWP_PLUGIN_VERSION', '1.0.0' );
define( 'CWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CWP_PLUGIN_FILE', __FILE__ );

// Autoload dependencies.
if ( file_exists( CWP_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once CWP_PLUGIN_DIR . 'vendor/autoload.php';
}

// Include main plugin class.
require_once CWP_PLUGIN_DIR . 'admin/php/class-plugin.php';

/**
 * Initialize the plugin.
 *
 * @return Plugin
 */
function cwp_init_plugin() {
	return Plugin::get_instance();
}

// Initialize plugin after WordPress has loaded.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\cwp_init_plugin' );

/**
 * Global function to access plugin instance
 *
 * @return Plugin
 */
function cwp_plugin() {
	return Plugin::get_instance();
}

/**
 * Plugin activation hook.
 *
 * @return void
 */
function cwp_activate_plugin() {
	// Add activation logic here if needed.
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\\cwp_activate_plugin' );

/**
 * Plugin deactivation hook.
 *
 * @return void
 */
function cwp_deactivate_plugin() {
	// Add deactivation logic here if needed.
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\cwp_deactivate_plugin' );
