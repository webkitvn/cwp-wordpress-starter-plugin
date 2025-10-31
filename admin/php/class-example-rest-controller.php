<?php
/**
 * Example REST Controller
 *
 * @package CWP\StarterPlugin
 */

namespace CWP\StarterPlugin;

/**
 * Example REST API Controller
 * 
 * This is an example implementation showing how to use the Abstract_REST_Controller
 */
class Example_REST_Controller extends Abstract_REST_Controller {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->base = 'example';
		parent::__construct();
	}

	/**
	 * Register REST routes
	 */
	public function register_routes() {
		// GET /cwp/v1/example
		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema(),
				),
			)
		);

		// GET /cwp/v1/example/{id}
		register_rest_route(
			$this->namespace,
			'/' . $this->base . '/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => array(
						'id' => array(
							'required'    => true,
							'type'        => 'integer',
							'description' => __( 'Unique identifier for the resource.', 'cwp-starter-plugin' ),
						),
					),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( false ),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Get a collection of items
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_items( $request ) {
		$params = $this->get_pagination_params( $request );
		
		// Example: Fetch data from database
		$items = $this->fetch_items( $params );
		
		// Log the request
		$this->logger->info( 'REST API: Retrieved items', array(
			'count'  => count( $items ),
			'params' => $params,
		) );

		return $this->success_response( array(
			'items'      => $items,
			'total'      => $this->get_total_items(),
			'page'       => $params['page'],
			'per_page'   => $params['per_page'],
		) );
	}

	/**
	 * Get a single item
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_item( $request ) {
		$id = $request->get_param( 'id' );
		
		// Example: Fetch single item
		$item = $this->fetch_item( $id );
		
		if ( ! $item ) {
			return $this->error_response(
				'item_not_found',
				__( 'Item not found.', 'cwp-starter-plugin' ),
				404
			);
		}

		return $this->success_response( $item );
	}

	/**
	 * Create an item
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_item( $request ) {
		// Validate parameters
		$params = $this->validate_params(
			$request->get_params(),
			array(
				'title' => array(
					'required' => true,
					'type'     => 'string',
				),
				'content' => array(
					'required' => false,
					'type'     => 'string',
				),
				'status' => array(
					'required' => false,
					'type'     => 'string',
					'validate_callback' => function( $value ) {
						return in_array( $value, array( 'draft', 'publish' ), true );
					},
				),
			)
		);

		if ( is_wp_error( $params ) ) {
			return $params;
		}

		// Example: Create item in database
		$item_id = $this->save_item( $params );
		
		if ( ! $item_id ) {
			return $this->error_response(
				'create_failed',
				__( 'Failed to create item.', 'cwp-starter-plugin' ),
				500
			);
		}

		// Log successful creation
		$this->logger->info( 'REST API: Item created', array(
			'id'     => $item_id,
			'params' => $params,
		) );

		// Send admin notice
		$plugin = Plugin::get_instance();
		$plugin->notices->success(
			sprintf(
				/* translators: %d: item ID */
				__( 'Item #%d created successfully via API.', 'cwp-starter-plugin' ),
				$item_id
			),
			true,
			false
		);

		return $this->success_response(
			array(
				'id'      => $item_id,
				'message' => __( 'Item created successfully.', 'cwp-starter-plugin' ),
			),
			201
		);
	}

	/**
	 * Update an item
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_item( $request ) {
		$id = $request->get_param( 'id' );
		
		// Check if item exists
		if ( ! $this->fetch_item( $id ) ) {
			return $this->error_response(
				'item_not_found',
				__( 'Item not found.', 'cwp-starter-plugin' ),
				404
			);
		}

		// Validate parameters
		$params = $this->validate_params(
			$request->get_params(),
			array(
				'title' => array(
					'required' => false,
					'type'     => 'string',
				),
				'content' => array(
					'required' => false,
					'type'     => 'string',
				),
				'status' => array(
					'required' => false,
					'type'     => 'string',
				),
			)
		);

		if ( is_wp_error( $params ) ) {
			return $params;
		}

		// Example: Update item in database
		$updated = $this->update_item_in_db( $id, $params );
		
		if ( ! $updated ) {
			return $this->error_response(
				'update_failed',
				__( 'Failed to update item.', 'cwp-starter-plugin' ),
				500
			);
		}

		return $this->success_response( array(
			'id'      => $id,
			'message' => __( 'Item updated successfully.', 'cwp-starter-plugin' ),
		) );
	}

	/**
	 * Delete an item
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function delete_item( $request ) {
		$id = $request->get_param( 'id' );
		
		// Check if item exists
		if ( ! $this->fetch_item( $id ) ) {
			return $this->error_response(
				'item_not_found',
				__( 'Item not found.', 'cwp-starter-plugin' ),
				404
			);
		}

		// Example: Delete item from database
		$deleted = $this->delete_item_from_db( $id );
		
		if ( ! $deleted ) {
			return $this->error_response(
				'delete_failed',
				__( 'Failed to delete item.', 'cwp-starter-plugin' ),
				500
			);
		}

		return $this->success_response( array(
			'id'      => $id,
			'message' => __( 'Item deleted successfully.', 'cwp-starter-plugin' ),
		) );
	}

	/**
	 * Get collection parameters
	 *
	 * @return array
	 */
	private function get_collection_params() {
		return array(
			'page' => array(
				'description'       => __( 'Current page of the collection.', 'cwp-starter-plugin' ),
				'type'              => 'integer',
				'default'           => 1,
				'minimum'           => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of items per page.', 'cwp-starter-plugin' ),
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
			),
			'search' => array(
				'description'       => __( 'Search term.', 'cwp-starter-plugin' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

	/**
	 * Get endpoint args for item schema
	 *
	 * @param bool $required Whether fields are required.
	 * @return array
	 */
	private function get_endpoint_args_for_item_schema( $required = true ) {
		return array(
			'title' => array(
				'required'          => $required,
				'type'              => 'string',
				'description'       => __( 'Item title.', 'cwp-starter-plugin' ),
				'sanitize_callback' => 'sanitize_text_field',
			),
			'content' => array(
				'required'          => false,
				'type'              => 'string',
				'description'       => __( 'Item content.', 'cwp-starter-plugin' ),
				'sanitize_callback' => 'wp_kses_post',
			),
			'status' => array(
				'required'    => false,
				'type'        => 'string',
				'enum'        => array( 'draft', 'publish' ),
				'default'     => 'draft',
				'description' => __( 'Item status.', 'cwp-starter-plugin' ),
			),
		);
	}

	/**
	 * Example: Fetch items from database
	 *
	 * @param array $params Query parameters.
	 * @return array
	 */
	private function fetch_items( $params ) {
		// This is just an example - replace with actual database query
		return array(
			array(
				'id'      => 1,
				'title'   => 'Example Item 1',
				'content' => 'This is example content.',
				'status'  => 'publish',
			),
			array(
				'id'      => 2,
				'title'   => 'Example Item 2',
				'content' => 'This is more example content.',
				'status'  => 'draft',
			),
		);
	}

	/**
	 * Example: Fetch single item
	 *
	 * @param int $id Item ID.
	 * @return array|null
	 */
	private function fetch_item( $id ) {
		// This is just an example - replace with actual database query
		$items = $this->fetch_items( array() );
		foreach ( $items as $item ) {
			if ( $item['id'] === (int) $id ) {
				return $item;
			}
		}
		return null;
	}

	/**
	 * Example: Get total items count
	 *
	 * @return int
	 */
	private function get_total_items() {
		// This is just an example - replace with actual count query
		return 2;
	}

	/**
	 * Example: Save item to database
	 *
	 * @param array $data Item data.
	 * @return int|false Item ID or false on failure.
	 */
	private function save_item( $data ) {
		// This is just an example - replace with actual database insert
		return rand( 100, 999 );
	}

	/**
	 * Example: Update item in database
	 *
	 * @param int   $id Item ID.
	 * @param array $data Item data.
	 * @return bool
	 */
	private function update_item_in_db( $id, $data ) {
		// This is just an example - replace with actual database update
		return true;
	}

	/**
	 * Example: Delete item from database
	 *
	 * @param int $id Item ID.
	 * @return bool
	 */
	private function delete_item_from_db( $id ) {
		// This is just an example - replace with actual database delete
		return true;
	}
}
