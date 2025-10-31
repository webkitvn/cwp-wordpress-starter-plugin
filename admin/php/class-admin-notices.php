<?php
/**
 * Admin Notices Handler
 *
 * @package CWP\StarterPlugin
 */

namespace CWP\StarterPlugin;

/**
 * Admin Notices class for displaying notifications in WordPress admin
 */
class Admin_Notices {

	/**
	 * Notices to be displayed
	 *
	 * @var array
	 */
	private $notices = array();

	/**
	 * Option name for persistent notices
	 *
	 * @var string
	 */
	private $option_name = 'cwp_admin_notices';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'display_notices' ) );
		add_action( 'admin_init', array( $this, 'load_persistent_notices' ) );
		add_action( 'wp_ajax_cwp_dismiss_notice', array( $this, 'ajax_dismiss_notice' ) );
	}

	/**
	 * Add a notice
	 *
	 * @param string $message     The notice message.
	 * @param string $type        Type of notice (success, error, warning, info).
	 * @param bool   $dismissible Whether the notice is dismissible.
	 * @param bool   $persistent  Whether to persist the notice across page loads.
	 * @param string $id          Unique ID for dismissible notices.
	 */
	public function add( $message, $type = 'info', $dismissible = true, $persistent = false, $id = '' ) {
		$notice = array(
			'message'     => $message,
			'type'        => $type,
			'dismissible' => $dismissible,
			'id'          => $id ?: uniqid( 'cwp_notice_' ),
		);

		if ( $persistent ) {
			$this->save_persistent_notice( $notice );
		} else {
			$this->notices[] = $notice;
		}
	}

	/**
	 * Add success notice
	 *
	 * @param string $message     The notice message.
	 * @param bool   $dismissible Whether the notice is dismissible.
	 * @param bool   $persistent  Whether to persist the notice.
	 */
	public function success( $message, $dismissible = true, $persistent = false ) {
		$this->add( $message, 'success', $dismissible, $persistent );
	}

	/**
	 * Add error notice
	 *
	 * @param string $message     The notice message.
	 * @param bool   $dismissible Whether the notice is dismissible.
	 * @param bool   $persistent  Whether to persist the notice.
	 */
	public function error( $message, $dismissible = true, $persistent = false ) {
		$this->add( $message, 'error', $dismissible, $persistent );
	}

	/**
	 * Add warning notice
	 *
	 * @param string $message     The notice message.
	 * @param bool   $dismissible Whether the notice is dismissible.
	 * @param bool   $persistent  Whether to persist the notice.
	 */
	public function warning( $message, $dismissible = true, $persistent = false ) {
		$this->add( $message, 'warning', $dismissible, $persistent );
	}

	/**
	 * Add info notice
	 *
	 * @param string $message     The notice message.
	 * @param bool   $dismissible Whether the notice is dismissible.
	 * @param bool   $persistent  Whether to persist the notice.
	 */
	public function info( $message, $dismissible = true, $persistent = false ) {
		$this->add( $message, 'info', $dismissible, $persistent );
	}

	/**
	 * Display all notices
	 */
	public function display_notices() {
		$all_notices = array_merge( $this->notices, $this->get_persistent_notices() );

		foreach ( $all_notices as $notice ) {
			$this->render_notice( $notice );
		}

		// Clear non-persistent notices after display
		$this->notices = array();
	}

	/**
	 * Render a single notice
	 *
	 * @param array $notice Notice data.
	 */
	private function render_notice( $notice ) {
		$classes = array(
			'notice',
			'notice-' . $notice['type'],
			'cwp-notice',
		);

		if ( $notice['dismissible'] ) {
			$classes[] = 'is-dismissible';
		}

		printf(
			'<div class="%s" data-notice-id="%s">%s</div>',
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( $notice['id'] ),
			wp_kses_post( wpautop( $notice['message'] ) )
		);

		// Add JavaScript for persistent dismissible notices
		if ( $notice['dismissible'] && ! empty( $notice['id'] ) ) {
			$this->enqueue_dismiss_script();
		}
	}

	/**
	 * Save persistent notice
	 *
	 * @param array $notice Notice data.
	 */
	private function save_persistent_notice( $notice ) {
		$notices = get_option( $this->option_name, array() );
		
		// Check if notice with same ID already exists
		$exists = false;
		foreach ( $notices as $key => $existing ) {
			if ( $existing['id'] === $notice['id'] ) {
				$notices[ $key ] = $notice;
				$exists = true;
				break;
			}
		}

		if ( ! $exists ) {
			$notices[] = $notice;
		}

		update_option( $this->option_name, $notices );
	}

	/**
	 * Get persistent notices
	 *
	 * @return array
	 */
	private function get_persistent_notices() {
		return get_option( $this->option_name, array() );
	}

	/**
	 * Load persistent notices on admin_init
	 */
	public function load_persistent_notices() {
		// Check for dismissed notices
		$dismissed = get_user_meta( get_current_user_id(), 'cwp_dismissed_notices', true );
		if ( ! is_array( $dismissed ) ) {
			$dismissed = array();
		}

		// Filter out dismissed notices
		$notices = $this->get_persistent_notices();
		$filtered = array();

		foreach ( $notices as $notice ) {
			if ( ! in_array( $notice['id'], $dismissed, true ) ) {
				$filtered[] = $notice;
			}
		}

		// Update if we filtered any
		if ( count( $filtered ) !== count( $notices ) ) {
			update_option( $this->option_name, $filtered );
		}
	}

	/**
	 * AJAX handler for dismissing notices
	 */
	public function ajax_dismiss_notice() {
		check_ajax_referer( 'cwp_dismiss_notice', 'nonce' );

		$notice_id = isset( $_POST['notice_id'] ) ? sanitize_text_field( $_POST['notice_id'] ) : '';
		
		if ( empty( $notice_id ) ) {
			wp_die();
		}

		// Add to user's dismissed notices
		$dismissed = get_user_meta( get_current_user_id(), 'cwp_dismissed_notices', true );
		if ( ! is_array( $dismissed ) ) {
			$dismissed = array();
		}

		if ( ! in_array( $notice_id, $dismissed, true ) ) {
			$dismissed[] = $notice_id;
			update_user_meta( get_current_user_id(), 'cwp_dismissed_notices', $dismissed );
		}

		// Remove from persistent notices
		$notices = $this->get_persistent_notices();
		$filtered = array();

		foreach ( $notices as $notice ) {
			if ( $notice['id'] !== $notice_id ) {
				$filtered[] = $notice;
			}
		}

		update_option( $this->option_name, $filtered );

		wp_send_json_success();
	}

	/**
	 * Enqueue dismiss script
	 */
	private function enqueue_dismiss_script() {
		static $enqueued = false;

		if ( $enqueued ) {
			return;
		}

		?>
		<script>
		jQuery(document).ready(function($) {
			$('.cwp-notice.is-dismissible').on('click', '.notice-dismiss', function() {
				var $notice = $(this).closest('.notice');
				var noticeId = $notice.data('notice-id');
				
				if (noticeId) {
					$.post(ajaxurl, {
						action: 'cwp_dismiss_notice',
						notice_id: noticeId,
						nonce: '<?php echo wp_create_nonce( 'cwp_dismiss_notice' ); ?>'
					});
				}
			});
		});
		</script>
		<?php

		$enqueued = true;
	}

	/**
	 * Clear all notices
	 */
	public function clear_all() {
		$this->notices = array();
		delete_option( $this->option_name );
	}
}
