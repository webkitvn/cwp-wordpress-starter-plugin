<?php
/**
 * Settings Page Template
 *
 * @package CWP\StarterPlugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get current settings
$settings = get_option( 'cwp_settings', array() );
?>

<div class="wrap cwp-settings-wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'cwp_settings_group' );
		?>

		<div class="cwp-settings-container">
			
			<!-- General Settings -->
			<div class="cwp-settings-section">
				<h2><?php esc_html_e( 'General Settings', 'cwp-starter-plugin' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="cwp_enable_feature">
								<?php esc_html_e( 'Enable Feature', 'cwp-starter-plugin' ); ?>
							</label>
						</th>
						<td>
							<input type="checkbox" 
								id="cwp_enable_feature" 
								name="cwp_settings[enable_feature]" 
								value="1" 
								<?php checked( isset( $settings['enable_feature'] ) ? $settings['enable_feature'] : 0, 1 ); ?> />
							<label for="cwp_enable_feature">
								<?php esc_html_e( 'Enable this awesome feature', 'cwp-starter-plugin' ); ?>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cwp_api_key">
								<?php esc_html_e( 'API Key', 'cwp-starter-plugin' ); ?>
							</label>
						</th>
						<td>
							<input type="text" 
								id="cwp_api_key" 
								name="cwp_settings[api_key]" 
								value="<?php echo esc_attr( isset( $settings['api_key'] ) ? $settings['api_key'] : '' ); ?>" 
								class="regular-text" />
							<p class="description">
								<?php esc_html_e( 'Enter your API key here.', 'cwp-starter-plugin' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cwp_mode">
								<?php esc_html_e( 'Mode', 'cwp-starter-plugin' ); ?>
							</label>
						</th>
						<td>
							<select id="cwp_mode" name="cwp_settings[mode]">
								<option value="basic" <?php selected( isset( $settings['mode'] ) ? $settings['mode'] : 'basic', 'basic' ); ?>>
									<?php esc_html_e( 'Basic', 'cwp-starter-plugin' ); ?>
								</option>
								<option value="advanced" <?php selected( isset( $settings['mode'] ) ? $settings['mode'] : 'basic', 'advanced' ); ?>>
									<?php esc_html_e( 'Advanced', 'cwp-starter-plugin' ); ?>
								</option>
								<option value="expert" <?php selected( isset( $settings['mode'] ) ? $settings['mode'] : 'basic', 'expert' ); ?>>
									<?php esc_html_e( 'Expert', 'cwp-starter-plugin' ); ?>
								</option>
							</select>
						</td>
					</tr>
				</table>
			</div>

			<!-- Advanced Settings -->
			<div class="cwp-settings-section">
				<h2><?php esc_html_e( 'Advanced Settings', 'cwp-starter-plugin' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="cwp_cache_duration">
								<?php esc_html_e( 'Cache Duration', 'cwp-starter-plugin' ); ?>
							</label>
						</th>
						<td>
							<input type="number" 
								id="cwp_cache_duration" 
								name="cwp_settings[cache_duration]" 
								value="<?php echo esc_attr( isset( $settings['cache_duration'] ) ? $settings['cache_duration'] : 3600 ); ?>" 
								class="small-text" />
							<label for="cwp_cache_duration">
								<?php esc_html_e( 'seconds', 'cwp-starter-plugin' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'How long to cache data (in seconds).', 'cwp-starter-plugin' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cwp_debug_mode">
								<?php esc_html_e( 'Debug Mode', 'cwp-starter-plugin' ); ?>
							</label>
						</th>
						<td>
							<input type="checkbox" 
								id="cwp_debug_mode" 
								name="cwp_settings[debug_mode]" 
								value="1" 
								<?php checked( isset( $settings['debug_mode'] ) ? $settings['debug_mode'] : 0, 1 ); ?> />
							<label for="cwp_debug_mode">
								<?php esc_html_e( 'Enable debug mode (logs additional information)', 'cwp-starter-plugin' ); ?>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cwp_custom_css">
								<?php esc_html_e( 'Custom CSS', 'cwp-starter-plugin' ); ?>
							</label>
						</th>
						<td>
							<textarea 
								id="cwp_custom_css" 
								name="cwp_settings[custom_css]" 
								rows="10" 
								cols="50" 
								class="large-text code"><?php echo esc_textarea( isset( $settings['custom_css'] ) ? $settings['custom_css'] : '' ); ?></textarea>
							<p class="description">
								<?php esc_html_e( 'Add custom CSS styles for the frontend.', 'cwp-starter-plugin' ); ?>
							</p>
						</td>
					</tr>
				</table>
			</div>

			<!-- Integration Settings -->
			<div class="cwp-settings-section">
				<h2><?php esc_html_e( 'Integration Settings', 'cwp-starter-plugin' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="cwp_webhook_url">
								<?php esc_html_e( 'Webhook URL', 'cwp-starter-plugin' ); ?>
							</label>
						</th>
						<td>
							<input type="url" 
								id="cwp_webhook_url" 
								name="cwp_settings[webhook_url]" 
								value="<?php echo esc_url( isset( $settings['webhook_url'] ) ? $settings['webhook_url'] : '' ); ?>" 
								class="large-text" />
							<p class="description">
								<?php esc_html_e( 'URL to send webhook notifications.', 'cwp-starter-plugin' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="cwp_allowed_roles">
								<?php esc_html_e( 'Allowed Roles', 'cwp-starter-plugin' ); ?>
							</label>
						</th>
						<td>
							<?php
							$roles = wp_roles()->roles;
							$selected_roles = isset( $settings['allowed_roles'] ) ? $settings['allowed_roles'] : array( 'administrator' );
							foreach ( $roles as $role_key => $role ) {
								?>
								<label style="display: block; margin-bottom: 5px;">
									<input type="checkbox" 
										name="cwp_settings[allowed_roles][]" 
										value="<?php echo esc_attr( $role_key ); ?>" 
										<?php checked( in_array( $role_key, $selected_roles, true ) ); ?> />
									<?php echo esc_html( $role['name'] ); ?>
								</label>
								<?php
							}
							?>
							<p class="description">
								<?php esc_html_e( 'Select which roles can access plugin features.', 'cwp-starter-plugin' ); ?>
							</p>
						</td>
					</tr>
				</table>
			</div>

		</div>

		<?php submit_button(); ?>
	</form>
</div>

<style>
.cwp-settings-wrap {
	margin: 20px 20px 0 2px;
	max-width: 800px;
}

.cwp-settings-container {
	background: #fff;
	border: 1px solid #ccd0d4;
	border-radius: 4px;
	margin-top: 20px;
}

.cwp-settings-section {
	padding: 20px;
	border-bottom: 1px solid #eee;
}

.cwp-settings-section:last-child {
	border-bottom: none;
}

.cwp-settings-section h2 {
	margin-top: 0;
	padding-bottom: 10px;
	border-bottom: 1px solid #eee;
	font-size: 18px;
	font-weight: 600;
}

.form-table {
	margin-top: 20px;
}

.form-table th {
	width: 200px;
}
</style>
