<?php
/**
 * Admin Dashboard Page
 *
 * @package CWP\StarterPlugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get plugin instance
$plugin = \CWP\StarterPlugin\Plugin::get_instance();
?>

<div class="wrap cwp-admin-wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<div class="cwp-dashboard">
		<div class="cwp-dashboard-widgets">
			
			<!-- Welcome Widget -->
			<div class="cwp-widget">
				<h2><?php esc_html_e( 'Welcome to CWP Plugin', 'cwp-starter-plugin' ); ?></h2>
				<p><?php esc_html_e( 'This is your plugin dashboard. Customize this page to show important information and quick actions.', 'cwp-starter-plugin' ); ?></p>
				<p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=cwp-plugin-settings' ) ); ?>" class="button button-primary">
						<?php esc_html_e( 'Configure Settings', 'cwp-starter-plugin' ); ?>
					</a>
				</p>
			</div>

			<!-- Stats Widget -->
			<div class="cwp-widget">
				<h2><?php esc_html_e( 'Quick Stats', 'cwp-starter-plugin' ); ?></h2>
				<ul class="cwp-stats">
					<li>
						<span class="cwp-stat-label"><?php esc_html_e( 'Plugin Version:', 'cwp-starter-plugin' ); ?></span>
						<span class="cwp-stat-value"><?php echo esc_html( CWP_PLUGIN_VERSION ); ?></span>
					</li>
					<li>
						<span class="cwp-stat-label"><?php esc_html_e( 'Active Since:', 'cwp-starter-plugin' ); ?></span>
						<span class="cwp-stat-value">
							<?php 
							$first_run = get_option( 'cwp_first_run' );
							echo $first_run ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $first_run ) ) ) : esc_html__( 'Unknown', 'cwp-starter-plugin' );
							?>
						</span>
					</li>
				</ul>
			</div>

			<!-- Actions Widget -->
			<div class="cwp-widget">
				<h2><?php esc_html_e( 'Quick Actions', 'cwp-starter-plugin' ); ?></h2>
				<p>
					<button class="button cwp-action-button" data-action="clear-cache">
						<?php esc_html_e( 'Clear Cache', 'cwp-starter-plugin' ); ?>
					</button>
					<button class="button cwp-action-button" data-action="export-settings">
						<?php esc_html_e( 'Export Settings', 'cwp-starter-plugin' ); ?>
					</button>
					<button class="button cwp-action-button" data-action="view-logs">
						<?php esc_html_e( 'View Logs', 'cwp-starter-plugin' ); ?>
					</button>
				</p>
			</div>

			<!-- Documentation Widget -->
			<div class="cwp-widget">
				<h2><?php esc_html_e( 'Documentation', 'cwp-starter-plugin' ); ?></h2>
				<ul>
					<li><a href="#" target="_blank"><?php esc_html_e( 'Getting Started Guide', 'cwp-starter-plugin' ); ?></a></li>
					<li><a href="#" target="_blank"><?php esc_html_e( 'API Reference', 'cwp-starter-plugin' ); ?></a></li>
					<li><a href="#" target="_blank"><?php esc_html_e( 'Troubleshooting', 'cwp-starter-plugin' ); ?></a></li>
					<li><a href="#" target="_blank"><?php esc_html_e( 'Support Forum', 'cwp-starter-plugin' ); ?></a></li>
				</ul>
			</div>

		</div>
	</div>
</div>

<style>
.cwp-admin-wrap {
	margin: 20px 20px 0 2px;
}

.cwp-dashboard-widgets {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
	gap: 20px;
	margin-top: 20px;
}

.cwp-widget {
	background: #fff;
	border: 1px solid #ccd0d4;
	border-radius: 4px;
	padding: 20px;
	box-shadow: 0 1px 1px rgba(0,0,0,0.04);
}

.cwp-widget h2 {
	margin-top: 0;
	padding-bottom: 10px;
	border-bottom: 1px solid #eee;
	font-size: 18px;
	font-weight: 600;
}

.cwp-stats {
	list-style: none;
	padding: 0;
	margin: 15px 0;
}

.cwp-stats li {
	display: flex;
	justify-content: space-between;
	padding: 8px 0;
	border-bottom: 1px solid #f0f0f0;
}

.cwp-stats li:last-child {
	border-bottom: none;
}

.cwp-stat-label {
	font-weight: 600;
	color: #666;
}

.cwp-stat-value {
	color: #333;
}

.cwp-action-button {
	margin: 0 5px 5px 0;
}
</style>

<script>
jQuery(document).ready(function($) {
	$('.cwp-action-button').on('click', function() {
		var action = $(this).data('action');
		// Add your AJAX handler here
		console.log('Action clicked:', action);
	});
});
</script>
