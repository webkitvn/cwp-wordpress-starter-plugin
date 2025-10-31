# CWP WordPress Starter Plugin - Developer Guide

## 🚀 Quick Start

This enhanced starter plugin provides a robust foundation for WordPress plugin development with built-in error handling, logging, admin notices, REST API support, and AJAX handlers.

## 📁 Enhanced Structure

```
cwp-wordpress-starter-plugin/
├── admin/
│   ├── php/
│   │   ├── class-plugin.php           # Main plugin class (singleton)
│   │   ├── class-logger.php           # Logging system
│   │   ├── class-admin-notices.php    # Admin notices handler
│   │   ├── abstract-rest-controller.php # REST API base class
│   │   ├── class-example-rest-controller.php # REST API example
│   │   ├── class-ajax-handler.php     # AJAX handler
│   │   └── class-helpers.php          # Helper utilities
│   └── views/
│       ├── admin-page.php             # Admin dashboard
│       └── settings-page.php          # Settings page
```

## 🔧 Core Features

### 1. **Singleton Plugin Instance**

Access the plugin instance globally:

```php
// Get plugin instance
$plugin = \CWP\StarterPlugin\Plugin::get_instance();

// Or use the global function
$plugin = cwp_plugin();

// Access services
$logger = $plugin->logger;
$notices = $plugin->notices;
```

### 2. **Error Handling & Logging**

Built-in logging system with automatic file management:

```php
// Access logger
$logger = cwp_plugin()->logger;

// Log different levels
$logger->error('Critical error occurred', ['user_id' => 123]);
$logger->warning('Something needs attention');
$logger->info('User action completed');
$logger->debug('Debug information'); // Only logs if WP_DEBUG is true

// Logs are stored in: wp-content/uploads/cwp-plugin-logs/
// Old logs are automatically cleaned up after 30 days
```

### 3. **Admin Notices System**

Display notices to admin users:

```php
$notices = cwp_plugin()->notices;

// Different notice types
$notices->success('Operation completed successfully!');
$notices->error('An error occurred!');
$notices->warning('Please check your settings');
$notices->info('New feature available');

// Persistent notices (survive page reload)
$notices->success('Plugin activated!', true, true);

// Dismissible notices with unique ID
$notices->add(
    'Check out our documentation',
    'info',
    true,  // dismissible
    true,  // persistent
    'doc_notice_v1' // unique ID
);
```

### 4. **REST API Development**

Create REST endpoints easily:

```php
// 1. Create your controller extending Abstract_REST_Controller
class My_REST_Controller extends Abstract_REST_Controller {
    
    public function __construct() {
        $this->base = 'my-endpoint';
        parent::__construct();
    }
    
    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->base, [
            'methods' => 'GET',
            'callback' => [$this, 'get_items'],
            'permission_callback' => [$this, 'get_items_permissions_check']
        ]);
    }
    
    public function get_items($request) {
        // Built-in parameter validation
        $params = $this->validate_params(
            $request->get_params(),
            [
                'search' => ['type' => 'string'],
                'page' => ['type' => 'integer', 'default' => 1]
            ]
        );
        
        // Return standardized response
        return $this->success_response($data);
    }
}

// 2. Initialize in main plugin
add_action('rest_api_init', function() {
    $controller = new My_REST_Controller();
});
```

**API Endpoints:**
- Base namespace: `cwp/v1`
- Example endpoint: `/wp-json/cwp/v1/example`
- Built-in validation and sanitization
- Standardized error handling
- Automatic logging

### 5. **AJAX Handling**

Centralized AJAX handler with security:

```php
// Frontend JavaScript
jQuery.ajax({
    url: ajaxurl, // or cwp_ajax.ajax_url in frontend
    type: 'POST',
    data: {
        action: 'cwp_admin_action',
        action_type: 'clear_cache',
        nonce: '<?php echo wp_create_nonce('cwp_admin_nonce'); ?>'
    },
    success: function(response) {
        if (response.success) {
            console.log(response.data.message);
        }
    }
});
```

**Available AJAX Actions:**
- `cwp_public_action` - Public actions (logged-in and guests)
- `cwp_admin_action` - Admin-only actions
- `cwp_save_settings` - Save plugin settings
- `cwp_get_data` - Fetch data with pagination
- `cwp_process_item` - Process individual items

### 6. **Helper Utilities**

Common utility functions:

```php
use CWP\StarterPlugin\Helpers;

// Sanitize arrays recursively
$clean_data = Helpers::sanitize_array($_POST['data'], [
    'email' => 'email',
    'url' => 'url',
    'number' => 'int'
]);

// Check request types
if (Helpers::is_ajax()) { /* AJAX request */ }
if (Helpers::is_rest()) { /* REST request */ }

// User utilities
$ip = Helpers::get_user_ip();
$can_edit = Helpers::user_can('edit_posts');

// Formatting
echo Helpers::format_bytes(1048576); // "1 MB"
echo Helpers::time_ago('-2 hours'); // "2 hours ago"

// Nonce helpers
echo Helpers::nonce_field('my_action');
$valid = Helpers::verify_nonce($_POST['nonce'], 'my_action');

// JSON responses
Helpers::json_response(['success' => true]);
```

## 🎯 Usage Examples

### Creating a New Feature

1. **Add a Service Class**:

```php
// admin/php/class-my-feature.php
namespace CWP\StarterPlugin;

class My_Feature {
    private $logger;
    
    public function __construct() {
        $this->logger = cwp_plugin()->logger;
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('init', [$this, 'register_post_type']);
    }
    
    public function register_post_type() {
        // Your logic here
        $this->logger->info('Custom post type registered');
    }
}
```

2. **Register with Plugin**:

```php
// In Plugin::init_services()
$this->register_service('my_feature', new My_Feature());
```

3. **Access Anywhere**:

```php
$my_feature = cwp_plugin()->get_service('my_feature');
```

### Adding Admin Page with AJAX

```php
// 1. Add menu in Plugin class
public function add_admin_menu() {
    add_submenu_page(
        'cwp-plugin',
        'My Feature',
        'My Feature',
        'manage_options',
        'cwp-my-feature',
        [$this, 'render_my_feature_page']
    );
}

// 2. Create view file: admin/views/my-feature-page.php
?>
<div class="wrap">
    <h1>My Feature</h1>
    <button id="cwp-process" class="button">Process</button>
</div>

<script>
jQuery('#cwp-process').click(function() {
    jQuery.post(ajaxurl, {
        action: 'cwp_process_item',
        item_id: 123,
        item_action: 'process',
        nonce: '<?php echo wp_create_nonce('cwp_process_item'); ?>'
    }, function(response) {
        if (response.success) {
            alert(response.data.message);
        }
    });
});
</script>
```

### Creating a REST Endpoint

```php
// 1. Create controller
class Products_Controller extends Abstract_REST_Controller {
    
    public function __construct() {
        $this->base = 'products';
        parent::__construct();
    }
    
    public function register_routes() {
        // GET /wp-json/cwp/v1/products
        register_rest_route($this->namespace, '/' . $this->base, [
            'methods' => 'GET',
            'callback' => [$this, 'get_products'],
            'permission_callback' => '__return_true', // Public endpoint
            'args' => [
                'category' => [
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ]
            ]
        ]);
    }
    
    public function get_products($request) {
        $category = $request->get_param('category');
        
        // Fetch products
        $products = $this->fetch_products($category);
        
        // Log request
        $this->logger->info('Products fetched', [
            'count' => count($products),
            'category' => $category
        ]);
        
        return $this->success_response($products);
    }
}

// 2. Initialize
add_action('rest_api_init', function() {
    new Products_Controller();
});
```

## 🔐 Security Best Practices

1. **Always verify nonces** in AJAX handlers
2. **Check capabilities** before sensitive operations
3. **Sanitize all input** using Helpers::sanitize_array()
4. **Validate REST parameters** using validate_params()
5. **Log security events** for audit trails
6. **Use prepared statements** for database queries

## 🚦 Development Workflow

1. **Enable Debug Mode**:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

2. **Monitor Logs**:
- Check `/wp-content/uploads/cwp-plugin-logs/`
- Review daily log files
- Old logs auto-cleanup after 30 days

3. **Test REST Endpoints**:
```bash
# List items
curl http://yoursite.com/wp-json/cwp/v1/example

# Create item (with authentication)
curl -X POST http://yoursite.com/wp-json/cwp/v1/example \
  -H "X-WP-Nonce: YOUR_NONCE" \
  -d '{"title":"Test Item"}'
```

4. **Test AJAX Handlers**:
- Use browser console
- Check Network tab for requests/responses
- Review server logs for errors

## 📋 Checklist for New Features

- [ ] Create feature class in `/admin/php/`
- [ ] Register with Plugin service container
- [ ] Add REST controller if API needed
- [ ] Add AJAX handlers if needed
- [ ] Create admin page views in `/admin/views/`
- [ ] Add TypeScript/JavaScript in `/src/`
- [ ] Write tests in `/tests/`
- [ ] Update documentation
- [ ] Add logging for important operations
- [ ] Implement proper error handling
- [ ] Add admin notices for user feedback
- [ ] Follow WordPress coding standards

## 🎨 Customization

### Change Plugin Prefix

Replace `cwp` with your prefix:
1. Find & replace `cwp_` in PHP files
2. Find & replace `cwp-` in CSS/JS files
3. Update namespace `CWP\` to `YourPrefix\`
4. Update text domain `cwp-starter-plugin`

### Add Custom Services

```php
// 1. Create service class
class Email_Service {
    public function send($to, $subject, $message) {
        // Implementation
    }
}

// 2. Register in Plugin::init_services()
$this->register_service('email', new Email_Service());

// 3. Use anywhere
cwp_plugin()->get_service('email')->send(...);
```

## 🐛 Troubleshooting

**Plugin won't activate:**
- Check PHP error logs
- Ensure PHP 7.4+
- Verify file permissions

**AJAX not working:**
- Check browser console
- Verify nonces
- Check user capabilities

**REST API errors:**
- Check permalinks (Settings > Permalinks)
- Verify authentication
- Review permission callbacks

**Logs not writing:**
- Check `/wp-content/uploads/` permissions
- Ensure `WP_DEBUG` is true
- Verify disk space

## 📚 Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [REST API Handbook](https://developer.wordpress.org/rest-api/)
- [AJAX in Plugins](https://codex.wordpress.org/AJAX_in_Plugins)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
