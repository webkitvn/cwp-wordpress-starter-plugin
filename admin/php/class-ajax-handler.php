<?php
/**
 * AJAX Handler Class
 *
 * @package CWP\StarterPlugin
 */

namespace CWP\StarterPlugin;

/**
 * AJAX Handler for all plugin AJAX requests
 */
class AJAX_Handler {

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->logger = new Logger();
		$this->init_handlers();
	}

	/**
	 * Initialize AJAX handlers
	 */
	private function init_handlers() {
		// Public AJAX handlers
		add_action( 'wp_ajax_nopriv_cwp_public_action', array( $this, 'handle_public_action' ) );
		add_action( 'wp_ajax_cwp_public_action', array( $this, 'handle_public_action' ) );

		// Admin AJAX handlers (logged-in users only)
		add_action( 'wp_ajax_cwp_admin_action', array( $this, 'handle_admin_action' ) );
		add_action( 'wp_ajax_cwp_save_settings', array( $this, 'handle_save_settings' ) );
		add_action( 'wp_ajax_cwp_get_data', array( $this, 'handle_get_data' ) );
		add_action( 'wp_ajax_cwp_process_item', array( $this, 'handle_process_item' ) );
	}

	/**
	 * Handle public AJAX action
	 */
	public function handle_public_action() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'cwp_public_nonce' ) ) {
			$this->error_response( __( 'Security check failed.', 'cwp-starter-plugin' ) );
		}

		// Get and sanitize data
		$data = isset( $_POST['data'] ) ? sanitize_text_field( $_POST['data'] ) : '';

		// Log the request
		$this->logger->info( 'Public AJAX action', array(
			'data' => $data,
			'user_ip' => Helpers::get_user_ip(),
		) );

		// Process the request
		$result = $this->process_public_action( $data );

		// Send response
		$this->success_response( array(
			'message' => __( 'Action processed successfully.', 'cwp-starter-plugin' ),
			'result'  => $result,
		) );
	}

	/**
	 * Handle admin AJAX action
	 */
	public function handle_admin_action() {
		// Check capability
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->error_response( __( 'You do not have permission to perform this action.', 'cwp-starter-plugin' ) );
		}

		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'cwp_admin_nonce' ) ) {
			$this->error_response( __( 'Security check failed.', 'cwp-starter-plugin' ) );
		}

		// Get action type
		$action_type = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : '';

		// Log the request
		$this->logger->info( 'Admin AJAX action', array(
			'action_type' => $action_type,
			'user_id'     => get_current_user_id(),
		) );

		// Process based on action type
		switch ( $action_type ) {
			case 'clear_cache':
				$this->clear_cache();
				break;
			case 'export_settings':
				$this->export_settings();
				break;
			case 'import_settings':
				$this->import_settings();
				break;
			default:
				$this->error_response( __( 'Unknown action type.', 'cwp-starter-plugin' ) );
		}
	}

	/**
	 * Handle save settings AJAX
	 */
	public function handle_save_settings() {
		// Check capability
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->error_response( __( 'You do not have permission to save settings.', 'cwp-starter-plugin' ) );
		}

		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'cwp_save_settings' ) ) {
			$this->error_response( __( 'Security check failed.', 'cwp-starter-plugin' ) );
		}

		// Get settings data
		$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : array();

		// Sanitize settings
		$sanitized_settings = Helpers::sanitize_array( $settings, array(
			'enable_feature' => 'bool',
			'api_key'        => 'text',
			'mode'           => 'key',
			'cache_duration' => 'int',
			'debug_mode'     => 'bool',
			'custom_css'     => 'textarea',
			'webhook_url'    => 'url',
		) );

		// Save settings
		update_option( 'cwp_settings', $sanitized_settings );

		// Log the action
		$this->logger->info( 'Settings saved via AJAX', array(
			'user_id' => get_current_user_id(),
		) );

		// Send success response
		$this->success_response( array(
			'message' => __( 'Settings saved successfully.', 'cwp-starter-plugin' ),
		) );
	}

	/**
	 * Handle get data AJAX
	 */
	public function handle_get_data() {
		// Check capability
		if ( ! current_user_can( 'read' ) ) {
			$this->error_response( __( 'You do not have permission to view this data.', 'cwp-starter-plugin' ) );
		}

		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'cwp_get_data' ) ) {
			$this->error_response( __( 'Security check failed.', 'cwp-starter-plugin' ) );
		}

		// Get parameters
		$type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
		$page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
		$per_page = isset( $_POST['per_page'] ) ? intval( $_POST['per_page'] ) : 10;

		// Fetch data based on type
		$data = $this->fetch_data( $type, $page, $per_page );

		// Send response
		$this->success_response( array(
			'data'     => $data['items'],
			'total'    => $data['total'],
			'page'     => $page,
			'per_page' => $per_page,
		) );
	}

	/**
	 * Handle process item AJAX
	 */
	public function handle_process_item() {
		// Check capability
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->error_response( __( 'You do not have permission to process items.', 'cwp-starter-plugin' ) );
		}

		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'cwp_process_item' ) ) {
			$this->error_response( __( 'Security check failed.', 'cwp-starter-plugin' ) );
		}

		// Get item data
		$item_id = isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : 0;
		$action = isset( $_POST['item_action'] ) ? sanitize_text_field( $_POST['item_action'] ) : '';

		if ( ! $item_id || ! $action ) {
			$this->error_response( __( 'Invalid item data.', 'cwp-starter-plugin' ) );
		}

		// Process the item
		$result = $this->process_item( $item_id, $action );

		if ( ! $result ) {
			$this->error_response( __( 'Failed to process item.', 'cwp-starter-plugin' ) );
		}

		// Send success response
		$this->success_response( array(
			'message' => __( 'Item processed successfully.', 'cwp-starter-plugin' ),
			'item_id' => $item_id,
			'action'  => $action,
		) );
	}

	/**
	 * Process public action
	 *
	 * @param string $data Action data.
	 * @return mixed
	 */
	private function process_public_action( $data ) {
		// Add your processing logic here
		return array(
			'processed' => true,
			'data'      => $data,
			'timestamp' => current_time( 'timestamp' ),
		);
	}

	/**
	 * Clear cache
	 */
	private function clear_cache() {
		// Add your cache clearing logic here
		delete_transient( 'cwp_cache_data' );
		
		$this->logger->info( 'Cache cleared' );
		
		$this->success_response( array(
			'message' => __( 'Cache cleared successfully.', 'cwp-starter-plugin' ),
		) );
	}

	/**
	 * Export settings
	 */
	private function export_settings() {
		$settings = get_option( 'cwp_settings', array() );
		
		$export_data = array(
			'plugin'   => 'cwp-starter-plugin',
			'version'  => CWP_PLUGIN_VERSION,
			'exported' => current_time( 'mysql' ),
			'settings' => $settings,
		);
		
		$this->success_response( array(
			'message' => __( 'Settings exported successfully.', 'cwp-starter-plugin' ),
			'data'    => base64_encode( wp_json_encode( $export_data ) ),
		) );
	}

	/**
	 * Import settings
	 */
	private function import_settings() {
		if ( ! isset( $_POST['import_data'] ) ) {
			$this->error_response( __( 'No import data provided.', 'cwp-starter-plugin' ) );
		}
		
		$import_data = base64_decode( $_POST['import_data'] );
		$data = json_decode( $import_data, true );
		
		if ( ! $data || ! isset( $data['settings'] ) ) {
			$this->error_response( __( 'Invalid import data.', 'cwp-starter-plugin' ) );
		}
		
		update_option( 'cwp_settings', $data['settings'] );
		
		$this->logger->info( 'Settings imported', array(
			'version' => $data['version'],
		) );
		
		$this->success_response( array(
			'message' => __( 'Settings imported successfully.', 'cwp-starter-plugin' ),
		) );
	}

	/**
	 * Fetch data
	 *
	 * @param string $type Data type.
	 * @param int    $page Page number.
	 * @param int    $per_page Items per page.
	 * @return array
	 */
	private function fetch_data( $type, $page, $per_page ) {
		// This is an example - replace with actual data fetching
		$items = array();
		$total = 0;
		
		switch ( $type ) {
			case 'users':
				$users = get_users( array(
					'number' => $per_page,
					'offset' => ( $page - 1 ) * $per_page,
				) );
				
				foreach ( $users as $user ) {
					$items[] = array(
						'id'    => $user->ID,
						'name'  => $user->display_name,
						'email' => $user->user_email,
					);
				}
				
				$total = count_users();
				$total = $total['total_users'];
				break;
				
			default:
				// Default example data
				for ( $i = 1; $i <= $per_page; $i++ ) {
					$items[] = array(
						'id'   => ( $page - 1 ) * $per_page + $i,
						'name' => 'Item ' . ( ( $page - 1 ) * $per_page + $i ),
					);
				}
				$total = 100;
		}
		
		return array(
			'items' => $items,
			'total' => $total,
		);
	}

	/**
	 * Process item
	 *
	 * @param int    $item_id Item ID.
	 * @param string $action Action to perform.
	 * @return bool
	 */
	private function process_item( $item_id, $action ) {
		// Add your item processing logic here
		$this->logger->info( 'Item processed', array(
			'item_id' => $item_id,
			'action'  => $action,
		) );
		
		return true;
	}

	/**
	 * Send success response
	 *
	 * @param mixed $data Response data.
	 */
	private function success_response( $data ) {
		wp_send_json_success( $data );
	}

	/**
	 * Send error response
	 *
	 * @param string $message Error message.
	 * @param mixed  $data Additional data.
	 */
	private function error_response( $message, $data = null ) {
		$this->logger->error( 'AJAX Error: ' . $message );
		wp_send_json_error( array(
			'message' => $message,
			'data'    => $data,
		) );
	}
}
