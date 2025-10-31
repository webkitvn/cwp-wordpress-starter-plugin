<?php
/**
 * Helper Functions
 *
 * @package CWP\StarterPlugin
 */

namespace CWP\StarterPlugin;

/**
 * Helper utility class
 */
class Helpers {

	/**
	 * Sanitize and validate an array recursively
	 *
	 * @param array $array Array to sanitize.
	 * @param array $rules Sanitization rules.
	 * @return array
	 */
	public static function sanitize_array( $array, $rules = array() ) {
		$sanitized = array();
		
		foreach ( $array as $key => $value ) {
			if ( isset( $rules[ $key ] ) ) {
				$sanitized[ $key ] = self::sanitize_by_type( $value, $rules[ $key ] );
			} else {
				if ( is_array( $value ) ) {
					$sanitized[ $key ] = self::sanitize_array( $value );
				} else {
					$sanitized[ $key ] = sanitize_text_field( $value );
				}
			}
		}
		
		return $sanitized;
	}

	/**
	 * Sanitize value by type
	 *
	 * @param mixed  $value Value to sanitize.
	 * @param string $type Type of sanitization.
	 * @return mixed
	 */
	public static function sanitize_by_type( $value, $type ) {
		switch ( $type ) {
			case 'email':
				return sanitize_email( $value );
			case 'url':
				return esc_url_raw( $value );
			case 'int':
			case 'integer':
				return intval( $value );
			case 'float':
				return floatval( $value );
			case 'boolean':
			case 'bool':
				return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
			case 'html':
				return wp_kses_post( $value );
			case 'textarea':
				return sanitize_textarea_field( $value );
			case 'key':
				return sanitize_key( $value );
			case 'title':
				return sanitize_title( $value );
			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Check if request is AJAX
	 *
	 * @return bool
	 */
	public static function is_ajax() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Check if request is REST API
	 *
	 * @return bool
	 */
	public static function is_rest() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}

	/**
	 * Get current user IP address
	 *
	 * @return string
	 */
	public static function get_user_ip() {
		$ip_keys = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' );
		
		foreach ( $ip_keys as $key ) {
			if ( array_key_exists( $key, $_SERVER ) === true ) {
				$ips = explode( ',', $_SERVER[ $key ] );
				foreach ( $ips as $ip ) {
					$ip = trim( $ip );
					if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
						return $ip;
					}
				}
			}
		}
		
		return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
	}

	/**
	 * Format bytes to human readable
	 *
	 * @param int $bytes Bytes to format.
	 * @param int $precision Decimal precision.
	 * @return string
	 */
	public static function format_bytes( $bytes, $precision = 2 ) {
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		
		$bytes = max( $bytes, 0 );
		$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow = min( $pow, count( $units ) - 1 );
		
		$bytes /= pow( 1024, $pow );
		
		return round( $bytes, $precision ) . ' ' . $units[ $pow ];
	}

	/**
	 * Get time ago string
	 *
	 * @param int|string $time Timestamp or date string.
	 * @return string
	 */
	public static function time_ago( $time ) {
		$time = is_numeric( $time ) ? $time : strtotime( $time );
		$diff = time() - $time;
		
		if ( $diff < 60 ) {
			return __( 'just now', 'cwp-starter-plugin' );
		} elseif ( $diff < 3600 ) {
			$mins = round( $diff / 60 );
			return sprintf(
				/* translators: %d: number of minutes */
				_n( '%d minute ago', '%d minutes ago', $mins, 'cwp-starter-plugin' ),
				$mins
			);
		} elseif ( $diff < 86400 ) {
			$hours = round( $diff / 3600 );
			return sprintf(
				/* translators: %d: number of hours */
				_n( '%d hour ago', '%d hours ago', $hours, 'cwp-starter-plugin' ),
				$hours
			);
		} elseif ( $diff < 604800 ) {
			$days = round( $diff / 86400 );
			return sprintf(
				/* translators: %d: number of days */
				_n( '%d day ago', '%d days ago', $days, 'cwp-starter-plugin' ),
				$days
			);
		} else {
			return date_i18n( get_option( 'date_format' ), $time );
		}
	}

	/**
	 * Generate random string
	 *
	 * @param int $length String length.
	 * @return string
	 */
	public static function random_string( $length = 10 ) {
		return wp_generate_password( $length, false );
	}

	/**
	 * Check if plugin is active
	 *
	 * @param string $plugin Plugin file path.
	 * @return bool
	 */
	public static function is_plugin_active( $plugin ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( $plugin );
	}

	/**
	 * Get plugin data
	 *
	 * @param string $key Specific data key.
	 * @return mixed
	 */
	public static function get_plugin_data( $key = '' ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		$plugin_data = get_plugin_data( CWP_PLUGIN_FILE );
		
		if ( $key && isset( $plugin_data[ $key ] ) ) {
			return $plugin_data[ $key ];
		}
		
		return $plugin_data;
	}

	/**
	 * Create nonce field
	 *
	 * @param string $action Nonce action.
	 * @param string $name Nonce name.
	 * @return string
	 */
	public static function nonce_field( $action = 'cwp_action', $name = 'cwp_nonce' ) {
		return wp_nonce_field( $action, $name, true, false );
	}

	/**
	 * Verify nonce
	 *
	 * @param string $nonce Nonce value.
	 * @param string $action Nonce action.
	 * @return bool
	 */
	public static function verify_nonce( $nonce, $action = 'cwp_action' ) {
		return wp_verify_nonce( $nonce, $action );
	}

	/**
	 * Safe redirect
	 *
	 * @param string $url Redirect URL.
	 * @param int    $status HTTP status code.
	 */
	public static function redirect( $url, $status = 302 ) {
		wp_safe_redirect( $url, $status );
		exit;
	}

	/**
	 * Get template part
	 *
	 * @param string $template Template name.
	 * @param array  $args Arguments to pass to template.
	 */
	public static function get_template( $template, $args = array() ) {
		$file = CWP_PLUGIN_DIR . 'templates/' . $template . '.php';
		
		if ( ! file_exists( $file ) ) {
			return;
		}
		
		if ( ! empty( $args ) ) {
			extract( $args );
		}
		
		include $file;
	}

	/**
	 * JSON response for AJAX
	 *
	 * @param mixed $data Response data.
	 * @param bool  $success Success status.
	 * @param int   $status_code HTTP status code.
	 */
	public static function json_response( $data, $success = true, $status_code = 200 ) {
		wp_send_json( array(
			'success' => $success,
			'data'    => $data,
		), $status_code );
	}

	/**
	 * Check user capability with logging
	 *
	 * @param string $capability Capability to check.
	 * @param int    $user_id User ID.
	 * @return bool
	 */
	public static function user_can( $capability = 'manage_options', $user_id = null ) {
		if ( is_null( $user_id ) ) {
			$can = current_user_can( $capability );
		} else {
			$can = user_can( $user_id, $capability );
		}
		
		if ( ! $can ) {
			$plugin = Plugin::get_instance();
			$plugin->logger->warning( 'Capability check failed', array(
				'capability' => $capability,
				'user_id'    => $user_id ?: get_current_user_id(),
			) );
		}
		
		return $can;
	}
}
