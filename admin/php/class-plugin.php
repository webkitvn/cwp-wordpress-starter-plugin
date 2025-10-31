<?php
/**
 * Main Plugin Class
 *
 * @package CWP\StarterPlugin
 */

namespace CWP\StarterPlugin;

/**
 * Main plugin class that handles hook registration and initialization.
 */
class Plugin {

	/**
	 * Plugin constructor.
	 *
	 * Registers all WordPress hooks and initializes the plugin.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Enqueue admin assets (JavaScript and CSS).
	 *
	 * @return void
	 */
	public function enqueue_admin_assets() {
		$admin_js_path  = CWP_PLUGIN_URL . 'assets/admin.js';
		$admin_css_path = CWP_PLUGIN_URL . 'assets/style.css';

		wp_enqueue_script(
			'cwp-admin-js',
			$admin_js_path,
			array( 'wp-i18n', 'wp-element', 'jquery' ),
			CWP_PLUGIN_VERSION,
			true
		);

		wp_enqueue_style(
			'cwp-admin-css',
			$admin_css_path,
			array(),
			CWP_PLUGIN_VERSION
		);
	}

	/**
	 * Enqueue public-facing assets (JavaScript and CSS).
	 *
	 * @return void
	 */
	public function enqueue_public_assets() {
		// Skip enqueueing on admin pages.
		if ( is_admin() ) {
			return;
		}

		$public_js_path  = CWP_PLUGIN_URL . 'assets/public.js';
		$public_css_path = CWP_PLUGIN_URL . 'assets/style.css';

		wp_enqueue_script(
			'cwp-public-js',
			$public_js_path,
			array(),
			CWP_PLUGIN_VERSION,
			true
		);

		wp_enqueue_style(
			'cwp-public-css',
			$public_css_path,
			array(),
			CWP_PLUGIN_VERSION
		);
	}

	/**
	 * Load plugin text domain for translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'cwp-starter-plugin',
			false,
			dirname( plugin_basename( CWP_PLUGIN_FILE ) ) . '/languages'
		);
	}
}
