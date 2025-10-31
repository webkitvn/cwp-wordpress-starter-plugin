<?php
/**
 * Logger Class - Centralized logging system
 *
 * @package CWP\StarterPlugin
 */

namespace CWP\StarterPlugin;

/**
 * Logger class for error handling and debugging
 */
class Logger {

	/**
	 * Log levels
	 */
	const ERROR   = 'error';
	const WARNING = 'warning';
	const INFO    = 'info';
	const DEBUG   = 'debug';

	/**
	 * Whether logging is enabled
	 *
	 * @var bool
	 */
	private $enabled;

	/**
	 * Log file path
	 *
	 * @var string
	 */
	private $log_file;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->enabled  = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$upload_dir     = wp_upload_dir();
		$this->log_file = $upload_dir['basedir'] . '/cwp-plugin-logs/plugin-' . date( 'Y-m-d' ) . '.log';

		// Create log directory if it doesn't exist
		$this->maybe_create_log_directory();
	}

	/**
	 * Create log directory if needed
	 */
	private function maybe_create_log_directory() {
		$dir = dirname( $this->log_file );
		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
			
			// Add .htaccess to prevent direct access
			$htaccess = $dir . '/.htaccess';
			if ( ! file_exists( $htaccess ) ) {
				file_put_contents( $htaccess, 'Deny from all' );
			}
		}
	}

	/**
	 * Log a message
	 *
	 * @param string $message Message to log.
	 * @param string $level   Log level.
	 * @param array  $context Additional context.
	 */
	public function log( $message, $level = self::INFO, $context = array() ) {
		if ( ! $this->enabled && $level !== self::ERROR ) {
			return;
		}

		$timestamp = current_time( 'mysql' );
		$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 );
		$caller    = isset( $backtrace[1] ) ? $backtrace[1] : array();
		
		$log_entry = sprintf(
			'[%s] [%s] %s | File: %s | Line: %s | Context: %s' . PHP_EOL,
			$timestamp,
			strtoupper( $level ),
			$message,
			isset( $caller['file'] ) ? $caller['file'] : 'unknown',
			isset( $caller['line'] ) ? $caller['line'] : 'unknown',
			wp_json_encode( $context )
		);

		// Write to file
		error_log( $log_entry, 3, $this->log_file );

		// Also log errors to PHP error log
		if ( $level === self::ERROR ) {
			error_log( 'CWP Plugin Error: ' . $message );
		}
	}

	/**
	 * Log an error
	 *
	 * @param string $message Error message.
	 * @param array  $context Additional context.
	 */
	public function error( $message, $context = array() ) {
		$this->log( $message, self::ERROR, $context );
	}

	/**
	 * Log a warning
	 *
	 * @param string $message Warning message.
	 * @param array  $context Additional context.
	 */
	public function warning( $message, $context = array() ) {
		$this->log( $message, self::WARNING, $context );
	}

	/**
	 * Log info
	 *
	 * @param string $message Info message.
	 * @param array  $context Additional context.
	 */
	public function info( $message, $context = array() ) {
		$this->log( $message, self::INFO, $context );
	}

	/**
	 * Log debug info
	 *
	 * @param string $message Debug message.
	 * @param array  $context Additional context.
	 */
	public function debug( $message, $context = array() ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$this->log( $message, self::DEBUG, $context );
		}
	}

	/**
	 * Clear old log files (older than 30 days)
	 */
	public function cleanup_old_logs() {
		$dir = dirname( $this->log_file );
		$files = glob( $dir . '/plugin-*.log' );
		
		if ( ! $files ) {
			return;
		}

		$thirty_days_ago = strtotime( '-30 days' );
		
		foreach ( $files as $file ) {
			if ( filemtime( $file ) < $thirty_days_ago ) {
				unlink( $file );
			}
		}
	}
}
