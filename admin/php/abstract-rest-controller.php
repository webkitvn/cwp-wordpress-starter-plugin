<?php
/**
 * Abstract REST Controller
 *
 * @package CWP\StarterPlugin
 */

namespace CWP\StarterPlugin;

/**
 * Abstract REST API Controller class
 */
abstract class Abstract_REST_Controller {

	/**
	 * Namespace for REST routes
	 *
	 * @var string
	 */
	protected $namespace = 'cwp/v1';

	/**
	 * Base route
	 *
	 * @var string
	 */
	protected $base = '';

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	protected $logger;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->logger = new Logger();
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register routes - must be implemented by child classes
	 */
	abstract public function register_routes();

	/**
	 * Check permissions for read access
	 *
	 * @return bool|\WP_Error
	 */
	public function get_items_permissions_check() {
		if ( ! current_user_can( 'read' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to view this resource.', 'cwp-starter-plugin' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/**
	 * Check permissions for write access
	 *
	 * @return bool|\WP_Error
	 */
	public function create_item_permissions_check() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to create this resource.', 'cwp-starter-plugin' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/**
	 * Check permissions for update access
	 *
	 * @return bool|\WP_Error
	 */
	public function update_item_permissions_check() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to update this resource.', 'cwp-starter-plugin' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/**
	 * Check permissions for delete access
	 *
	 * @return bool|\WP_Error
	 */
	public function delete_item_permissions_check() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to delete this resource.', 'cwp-starter-plugin' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/**
	 * Validate and sanitize request parameters
	 *
	 * @param array $params Parameters to validate.
	 * @param array $rules Validation rules.
	 * @return array|\WP_Error Sanitized parameters or error.
	 */
	protected function validate_params( $params, $rules ) {
		$sanitized = array();
		$errors = array();

		foreach ( $rules as $field => $rule ) {
			$value = isset( $params[ $field ] ) ? $params[ $field ] : null;

			// Check required fields
			if ( isset( $rule['required'] ) && $rule['required'] && empty( $value ) ) {
				$errors[] = sprintf(
					/* translators: %s: field name */
					__( 'Field "%s" is required.', 'cwp-starter-plugin' ),
					$field
				);
				continue;
			}

			// Skip if not set and not required
			if ( is_null( $value ) && ( ! isset( $rule['required'] ) || ! $rule['required'] ) ) {
				continue;
			}

			// Validate and sanitize based on type
			if ( isset( $rule['type'] ) ) {
				switch ( $rule['type'] ) {
					case 'string':
						$sanitized[ $field ] = sanitize_text_field( $value );
						break;
					case 'email':
						$sanitized[ $field ] = sanitize_email( $value );
						if ( ! is_email( $sanitized[ $field ] ) ) {
							$errors[] = sprintf(
								/* translators: %s: field name */
								__( 'Field "%s" must be a valid email.', 'cwp-starter-plugin' ),
								$field
							);
						}
						break;
					case 'url':
						$sanitized[ $field ] = esc_url_raw( $value );
						break;
					case 'integer':
						$sanitized[ $field ] = intval( $value );
						break;
					case 'boolean':
						$sanitized[ $field ] = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
						break;
					case 'array':
						$sanitized[ $field ] = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : array();
						break;
					default:
						$sanitized[ $field ] = sanitize_text_field( $value );
				}
			}

			// Custom validation callback
			if ( isset( $rule['validate_callback'] ) && is_callable( $rule['validate_callback'] ) ) {
				$is_valid = call_user_func( $rule['validate_callback'], $sanitized[ $field ], $field );
				if ( ! $is_valid ) {
					$errors[] = sprintf(
						/* translators: %s: field name */
						__( 'Field "%s" validation failed.', 'cwp-starter-plugin' ),
						$field
					);
				}
			}
		}

		if ( ! empty( $errors ) ) {
			return new \WP_Error(
				'rest_invalid_params',
				implode( ' ', $errors ),
				array( 'status' => 400 )
			);
		}

		return $sanitized;
	}

	/**
	 * Format successful response
	 *
	 * @param mixed $data Response data.
	 * @param int   $status HTTP status code.
	 * @return \WP_REST_Response
	 */
	protected function success_response( $data, $status = 200 ) {
		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => $data,
			),
			$status
		);
	}

	/**
	 * Format error response
	 *
	 * @param string $code Error code.
	 * @param string $message Error message.
	 * @param int    $status HTTP status code.
	 * @return \WP_Error
	 */
	protected function error_response( $code, $message, $status = 400 ) {
		$this->logger->error( 'REST API Error', array(
			'code'    => $code,
			'message' => $message,
			'status'  => $status,
		) );

		return new \WP_Error( $code, $message, array( 'status' => $status ) );
	}

	/**
	 * Get pagination parameters
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return array
	 */
	protected function get_pagination_params( $request ) {
		return array(
			'page'     => $request->get_param( 'page' ) ?: 1,
			'per_page' => $request->get_param( 'per_page' ) ?: 10,
			'orderby'  => $request->get_param( 'orderby' ) ?: 'date',
			'order'    => $request->get_param( 'order' ) ?: 'DESC',
		);
	}
}
