<?php
/**
 * Theme setting form
 *
 * Theme settings
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<h1>Mozilla Theme Settings</h1>
<form method="POST" action="/wp-admin/admin.php?page=theme-panel">
	<?php wp_nonce_field( 'admin_nonce', 'admin_nonce_field' ); ?>
	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row">
					<label for="report-email">Report Group / Event Email</label>
				</th>
				<td>
					<input type="text" id="report-email" name="report_email" class="regular-text" value="<?php echo isset( $options['report_email'] ) ? esc_attr( $options['report_email'] ) : ''; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="github-link">Github Link</label>
				</th>
				<td>
					<input type="text" id="github-link" name="github_link" class="regular-text" value="<?php echo esc_url_raw( $options['github_link'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="community-discourse">Community Portal Discourse</label>
				</th>
				<td>
					<input type="text" id="community-discourse" name="community_discourse" class="regular-text" value="<?php echo esc_url_raw( $options['community_discourse'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="mailchimp">Mailchimp API Key</label>
				</th>
				<td>
					<input type="text" id="mailchimp" name="mailchimp" class="regular-text" value="<?php echo esc_attr( $options['mailchimp'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="company">Company</label>
				</th>
				<td>
					<input type="text" id="company" name="company" class="regular-text" value="<?php echo esc_attr( $options['company'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="address">Address</label>
				</th>
				<td>
					<input type="text" id="address" name="address" class="regular-text" value="<?php echo esc_attr( $options['address'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="city">City</label>
				</th>
				<td>
					<input type="text" id="city" name="city" class="regular-text" value="<?php echo esc_attr( $options['city'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="state">State / Province</label>
				</th>
				<td>
					<input type="text" id="state" name="state" class="regular-text" value="<?php echo esc_attr( $options['state'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="zip">Postal /Zip</label>
				</th>
				<td>
					<input type="text" id="zip" name="zip" class="regular-text" value="<?php echo esc_attr( $options['zip'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="country">Country</label>
				</th>
				<td>
					<input type="text" id="country" name="country" class="regular-text" value="<?php echo esc_attr( $options['country'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="phone">Phone</label>
				</th>
				<td>
					<input type="text" id="phone" name="phone" class="regular-text" value="<?php echo esc_attr( $options['phone'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="google-analytics-id">Google Analytics ID</label>
				</th>
				<td>
					<input type="text" id="google-analytics-id" name="google_analytics_id" class="regular-text" value="<?php echo esc_attr( $options['google_analytics_id'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="google-analytics-id">Google Analytics SRI Hash</label>
				</th>
				<td>
					<input type="text" id="google-analytics-sri" name="google_analytics_sri" class="regular-text" value="<?php echo esc_attr( $options['google_analytics_sri'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="error-404-copy">Discourse API Key</label>
				</th>
				<td>
					<input type="text" id="discourse-api-key" name="discourse_api_key" class="regular-text" value="<?php print isset( $options['discourse_api_key'] ) ? esc_attr( $options['discourse_api_key'] ) : ''; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="error-404-copy">Discourse API URL</label>
				</th>
				<td>
					<input type="text" id="discourse-api-url" name="discourse_api_url" class="regular-text" value="<?php print isset( $options['discourse_api_url'] ) ? esc_url_raw( $options['discourse_api_url'] ) : ''; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="error-404-copy">Discourse URL</label>
				</th>
				<td>
					<input type="text" id="discourse-url" name="discourse_url" class="regular-text" value="<?php print isset( $options['discourse_url'] ) ? esc_url_raw( $options['discourse_url'] ) : ''; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="error-404-copy">Mapbox Access Token</label>
				</th>
				<td>
					<input type="text" id="mapbox" name="mapbox" class="regular-text" value="<?php print isset( $options['mapbox'] ) ? esc_attr( $options['mapbox'] ) : ''; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="default-open-graph-title">Default Open Graph Title</label>
				</th>
				<td>
					<input type="text" id="default-open-graph-title" name="default_open_graph_title" class="regular-text" value="<?php echo esc_attr( $options['default_open_graph_title'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="default-open-graph-desc">Default Open Graph Description</label>
				</th>
				<td>
					<input type="text" id="default-open-graph-desc" name="default_open_graph_desc" class="regular-text" value="<?php echo esc_attr( $options['default_open_graph_desc'] ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="image-max-filesize">Max Image Filesize Upload (KB)</label>
				</th>
				<td>
					<input type="text" id="image-max-filesize" name="image_max_filesize" class="regular-text" value="<?php echo isset( $options['image_max_filesize'] ) ? esc_attr( $options['image_max_filesize'] ) : 500; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="error-404-title">404 Error Title</label>
				</th>
				<td>
					<input type="text" id="error-404-title" name="error_404_title" class="regular-text" value="<?php echo isset( $options['error_404_title'] ) ? esc_attr( $options['error_404_title'] ) : ''; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="error-404-copy">404 Error Copy</label>
				</th>
				<td>
					<input type="text" id="error-404-copy" name="error_404_copy" class="regular-text" value="<?php echo isset( $options['error_404_copy'] ) ? esc_attr( $options['error_404_copy'] ) : ''; ?>" />
				</td>
			</tr>
		</tbody>
	</table>
	<input type="submit" value="Save Settings" />
</form>
