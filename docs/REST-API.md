# REST API Extension Guide

This guide explains how to extend the plugin with custom REST API endpoints.

## Overview

WordPress REST API allows you to interact with your plugin via HTTP requests. This is useful for:
- AJAX operations without page reloads
- Integrations with external services
- Building headless WordPress applications
- Mobile app integrations

## Basic REST Endpoint

### 1. Create a REST Controller Class

Create a new file `admin/php/class-rest-controller.php`:

```php
<?php
/**
 * REST API Controller
 *
 * @package CWP\StarterPlugin
 */

namespace CWP\StarterPlugin;

/**
 * REST API controller for custom endpoints.
 */
class REST_Controller {
	/**
	 * Namespace for REST routes.
	 *
	 * @var string
	 */
	protected $namespace = 'cwp/v1';

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/example',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_example' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/example',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_example' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'args'                => array(
					'title' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	/**
	 * GET endpoint handler.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_example( $request ) {
		$data = array(
			'message' => __( 'Hello from REST API!', 'cwp-starter-plugin' ),
			'time'    => current_time( 'mysql' ),
		);

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * POST endpoint handler.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_example( $request ) {
		$title = $request->get_param( 'title' );

		// Your logic here
		$result = array(
			'success' => true,
			'title'   => $title,
			'id'      => 123,
		);

		return new \WP_REST_Response( $result, 201 );
	}

	/**
	 * Check permissions for API access.
	 *
	 * @return bool True if user has permission, false otherwise.
	 */
	public function check_permissions() {
		// Adjust permission check as needed
		return current_user_can( 'manage_options' );
	}
}
```

### 2. Register the Controller

In your main Plugin class (`admin/php/class-plugin.php`), add:

```php
/**
 * Initialize REST API endpoints.
 *
 * @return void
 */
public function init_rest_api() {
	require_once CWP_PLUGIN_DIR . 'admin/php/class-rest-controller.php';
	$rest_controller = new REST_Controller();
	$rest_controller->register_routes();
}
```

And register the hook in the constructor:

```php
add_action( 'rest_api_init', array( $this, 'init_rest_api' ) );
```

## Making API Calls from JavaScript

### Using WordPress API Fetch

Install the package (already included in package.json):

```bash
pnpm install
```

Example usage in TypeScript:

```typescript
import apiFetch from '@wordpress/api-fetch';

interface ExampleResponse {
  message: string;
  time: string;
}

// GET request
async function fetchExample(): Promise<ExampleResponse> {
  try {
    const response = await apiFetch<ExampleResponse>({
      path: '/cwp/v1/example',
      method: 'GET',
    });
    return response;
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
}

// POST request
async function createExample(title: string): Promise<unknown> {
  try {
    const response = await apiFetch({
      path: '/cwp/v1/example',
      method: 'POST',
      data: { title },
    });
    return response;
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
}
```

### Using Fetch API

```typescript
const API_BASE = '/wp-json/cwp/v1';

async function fetchExample() {
  const response = await fetch(`${API_BASE}/example`, {
    headers: {
      'X-WP-Nonce': (window as any).wpApiSettings?.nonce || '',
    },
  });
  
  if (!response.ok) {
    throw new Error('API request failed');
  }
  
  return response.json();
}
```

## Authentication & Permissions

### Cookie Authentication (WordPress Admin)

For requests from the WordPress admin, use cookie authentication with nonces:

```php
// In your PHP enqueue function
wp_localize_script(
	'cwp-admin-js',
	'cwpApi',
	array(
		'nonce' => wp_create_nonce( 'wp_rest' ),
		'root'  => esc_url_raw( rest_url() ),
	)
);
```

```typescript
// In TypeScript
declare global {
  interface Window {
    cwpApi: {
      nonce: string;
      root: string;
    };
  }
}

const headers = {
  'X-WP-Nonce': window.cwpApi.nonce,
};
```

### Permission Callbacks

Different permission levels:

```php
// Anyone can access (public endpoint)
'permission_callback' => '__return_true'

// Logged-in users only
'permission_callback' => 'is_user_logged_in'

// Administrators only
'permission_callback' => function() {
	return current_user_can( 'manage_options' );
}

// Custom capability check
'permission_callback' => function() {
	return current_user_can( 'edit_posts' );
}
```

## Validation & Sanitization

### Input Validation

```php
register_rest_route(
	$this->namespace,
	'/items/(?P<id>\d+)',
	array(
		'args' => array(
			'id'    => array(
				'required'    => true,
				'type'        => 'integer',
				'minimum'     => 1,
				'description' => 'Unique identifier for the item',
			),
			'title' => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => function( $param ) {
					return strlen( $param ) >= 3;
				},
			),
			'email' => array(
				'type'              => 'string',
				'format'            => 'email',
				'sanitize_callback' => 'sanitize_email',
				'validate_callback' => 'is_email',
			),
		),
	)
);
```

## Error Handling

### Returning Errors

```php
public function get_item( $request ) {
	$id = $request->get_param( 'id' );
	$item = $this->get_item_by_id( $id );

	if ( ! $item ) {
		return new \WP_Error(
			'cwp_item_not_found',
			__( 'Item not found', 'cwp-starter-plugin' ),
			array( 'status' => 404 )
		);
	}

	return new \WP_REST_Response( $item, 200 );
}
```

### Handling Errors in TypeScript

```typescript
try {
  const response = await apiFetch({
    path: '/cwp/v1/items/999',
  });
} catch (error: any) {
  if (error.code === 'cwp_item_not_found') {
    console.error('Item not found:', error.message);
  } else {
    console.error('API Error:', error);
  }
}
```

## Testing REST Endpoints

### Using cURL

```bash
# GET request
curl -X GET \
  'http://localhost/wp-json/cwp/v1/example' \
  -H 'Content-Type: application/json'

# POST request with authentication
curl -X POST \
  'http://localhost/wp-json/cwp/v1/example' \
  -H 'Content-Type: application/json' \
  -H 'X-WP-Nonce: YOUR_NONCE_HERE' \
  -d '{"title":"Test Item"}'
```

### Using Postman

1. Set request URL: `http://localhost/wp-json/cwp/v1/example`
2. Set method: GET or POST
3. Add headers:
   - `Content-Type: application/json`
   - `X-WP-Nonce: YOUR_NONCE_HERE` (for authenticated requests)
4. Add body (for POST/PUT): `{"title": "Test"}`

### Using WordPress REST API Console

Install the "WP REST API Controller" plugin for a GUI to test endpoints.

## Best Practices

1. **Use Proper HTTP Methods**
   - GET: Retrieve data
   - POST: Create new resources
   - PUT/PATCH: Update resources
   - DELETE: Remove resources

2. **Version Your API**
   - Use namespaces like `cwp/v1`, `cwp/v2`
   - Maintain backward compatibility

3. **Validate All Inputs**
   - Use `validate_callback` for custom validation
   - Use `sanitize_callback` for cleaning data

4. **Return Appropriate HTTP Status Codes**
   - 200: Success
   - 201: Created
   - 400: Bad Request
   - 401: Unauthorized
   - 403: Forbidden
   - 404: Not Found
   - 500: Server Error

5. **Document Your Endpoints**
   - Use PHPDoc comments
   - Document parameters, return values, and errors
   - Consider using OpenAPI/Swagger

6. **Use Type Hints in TypeScript**
   - Define interfaces for API responses
   - Use proper error handling

## Example: Complete CRUD API

See `admin/php/class-rest-controller.php` for a complete example implementing:
- List items (GET /cwp/v1/items)
- Get single item (GET /cwp/v1/items/:id)
- Create item (POST /cwp/v1/items)
- Update item (PUT /cwp/v1/items/:id)
- Delete item (DELETE /cwp/v1/items/:id)

## Resources

- [WordPress REST API Handbook](https://developer.wordpress.org/rest-api/)
- [REST API Authentication](https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/)
- [@wordpress/api-fetch Documentation](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-api-fetch/)
