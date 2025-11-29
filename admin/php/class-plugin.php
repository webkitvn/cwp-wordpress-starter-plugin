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
	 * Plugin instance
	 *
	 * @var Plugin
	 */
	private static $instance = null;

	/**
	 * Service container
	 *
	 * @var array
	 */
	private $services = array();

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	public $logger;

	/**
	 * Admin notices instance
	 *
	 * @var Admin_Notices
	 */
	public $notices;

	/**
	 * Get plugin instance
	 *
	 * @return Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Plugin constructor.
	 *
	 * Registers all WordPress hooks and initializes the plugin.
	 */
	private function __construct() {
		$this->init_services();
		$this->init_hooks();
	}

	/**
	 * Initialize services
	 */
	private function init_services() {
		// Core services
		$this->logger  = new Logger();
		$this->notices = new Admin_Notices();

		// Register services
		$this->services['logger']  = $this->logger;
		$this->services['notices'] = $this->notices;

		// Initialize AJAX handler
		$this->services['ajax'] = new AJAX_Handler();

		// Initialize REST API controllers
		add_action( 'rest_api_init', array( $this, 'init_rest_controllers' ) );

		// Log initialization
		$this->logger->info( 'Plugin initialized', array( 'version' => CWP_PLUGIN_VERSION ) );
	}

	/**
	 * Initialize REST API controllers
	 */
	public function init_rest_controllers() {
		// Example controller - remove or modify as needed
		$example_controller             = new Example_REST_Controller();
		$this->services['rest_example'] = $example_controller;
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Assets
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );

		// Core
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Activation/Deactivation hooks
		register_activation_hook( CWP_PLUGIN_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( CWP_PLUGIN_FILE, array( $this, 'deactivate' ) );

		// Admin
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
		}

		// Cron
		add_action( 'cwp_daily_maintenance', array( $this, 'daily_maintenance' ) );

		// AJAX handlers
		$this->init_ajax_handlers();
	}

	/**
	 * Initialize AJAX handlers
	 */
	private function init_ajax_handlers() {
		// Add your AJAX handlers here
		// add_action( 'wp_ajax_cwp_action', array( $this, 'handle_ajax' ) );
	}

	/**
	 * Plugin activation
	 */
	public function activate() {
		$this->logger->info( 'Plugin activated' );

		// Schedule cron events
		if ( ! wp_next_scheduled( 'cwp_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'cwp_daily_maintenance' );
		}

		// Add activation notice
		$this->notices->success(
			__( 'CWP Plugin has been activated successfully!', 'cwp-starter-plugin' ),
			true,
			true
		);

		// Create necessary database tables or options
		$this->create_tables();

		// Set default options
		$this->set_default_options();

		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation
	 */
	public function deactivate() {
		$this->logger->info( 'Plugin deactivated' );

		// Clear scheduled events
		wp_clear_scheduled_hook( 'cwp_daily_maintenance' );

		flush_rewrite_rules();
	}

	/**
	 * Create database tables
	 */
	private function create_tables() {
		// Add your table creation logic here if needed
	}

	/**
	 * Set default options
	 */
	private function set_default_options() {
		$defaults = array(
			'cwp_version'   => CWP_PLUGIN_VERSION,
			'cwp_settings'  => array(),
			'cwp_first_run' => current_time( 'mysql' ),
		);

		foreach ( $defaults as $option => $value ) {
			if ( false === get_option( $option ) ) {
				add_option( $option, $value );
			}
		}
	}

	/**
	 * Daily maintenance tasks
	 */
	public function daily_maintenance() {
		$this->logger->debug( 'Running daily maintenance' );

		// Clean up old logs
		$this->logger->cleanup_old_logs();

		// Add other maintenance tasks here
		do_action( 'cwp_daily_maintenance_tasks' );
	}

	/**
	 * Admin init
	 */
	public function admin_init() {
		// Register settings
		$this->register_settings();
	}

	/**
	 * Register settings
	 */
	private function register_settings() {
		register_setting(
			'cwp_settings_group',
			'cwp_settings',
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $input Settings input.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		// Add your settings sanitization logic here

		return $sanitized;
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'CWP Plugin', 'cwp-starter-plugin' ),
			__( 'CWP Plugin', 'cwp-starter-plugin' ),
			'manage_options',
			'cwp-plugin',
			array( $this, 'render_admin_page' ),
			'dashicons-admin-generic',
			100
		);

		// Add submenu pages
		add_submenu_page(
			'cwp-plugin',
			__( 'Settings', 'cwp-starter-plugin' ),
			__( 'Settings', 'cwp-starter-plugin' ),
			'manage_options',
			'cwp-plugin-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		include CWP_PLUGIN_DIR . 'admin/views/admin-page.php';
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		include CWP_PLUGIN_DIR . 'admin/views/settings-page.php';
	}

	/**
	 * Get service
	 *
	 * @param string $name Service name.
	 * @return mixed|null
	 */
	public function get_service( $name ) {
		return isset( $this->services[ $name ] ) ? $this->services[ $name ] : null;
	}

	/**
	 * Register service
	 *
	 * @param string $name Service name.
	 * @param mixed  $service Service instance.
	 */
	public function register_service( $name, $service ) {
		$this->services[ $name ] = $service;
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
		wp_script_add_data( 'cwp-admin-js', 'type', 'module' );

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
		wp_script_add_data( 'cwp-public-js', 'type', 'module' );

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
