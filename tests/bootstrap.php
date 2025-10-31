<?php
/**
 * PHPUnit Bootstrap File
 *
 * This file is used to bootstrap PHPUnit tests for the plugin.
 *
 * @package CWP\StarterPlugin\Tests
 */

// Load the Composer autoloader.
if ( file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __DIR__ ) . '/vendor/autoload.php';
}

// Define constants for testing.
if ( ! defined( 'CWP_PLUGIN_DIR' ) ) {
	define( 'CWP_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
}

if ( ! defined( 'CWP_PLUGIN_VERSION' ) ) {
	define( 'CWP_PLUGIN_VERSION', '1.0.0-test' );
}

// Load the WordPress test environment if available.
// This assumes you have wp-tests-config.php set up in your development environment.
// If not, you may need to adjust this path or use WP_Mock instead.
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Load WordPress test functions if the directory exists.
if ( file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	require_once $_tests_dir . '/includes/functions.php';

	/**
	 * Manually load the plugin being tested.
	 */
	function _manually_load_plugin() {
		require dirname( __DIR__ ) . '/cwp-wordpress-starter-plugin.php';
	}
	tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

	// Start up the WP testing environment.
	require $_tests_dir . '/includes/bootstrap.php';
} else {
	// If WordPress test suite is not available, use WP_Mock or continue with basic tests.
	echo "WordPress test suite not found. Consider setting WP_TESTS_DIR environment variable.\n";
}
