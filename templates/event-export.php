<?php
/**
 * Event Export in admin
 *
 * Event exporting template
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

	$args = array(
		'post_type' => 'campaign',
		'posts_per_page'  => -1,
	);

	$campaigns = new WP_Query( $args );

	$args = array(
		'post_type' => 'activity',
		'posts_per_page'  => -1,
	);

	$activities = new WP_Query( $args );
	?>
<h1>Export Events</h1>
<form method="GET" action="/wp-admin/admin-ajax.php">
	<input type="hidden" name="action" value="export_events" />
	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row">
					<label for="start-date">Start Date</label>
				</th>
				<td>
					<input type="text" id="start-date" name="start" class="custom_date"/>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="end-date">End Date</label>
				</th>
				<td>
					<input type="text" id="end-date" name="end" class="custom_date"/>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="activities">Activities</label>
				</th>
				<td>
					<select name="activity" id="activities">
						<option value=""><?php echo esc_html_e( 'Select', 'community-portal' ); ?></option>
						<?php foreach ( $activities->posts as $activity ) : ?>
							<option value="<?php echo esc_attr( $activity->ID ); ?>"><?php echo esc_html( $activity->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="campaigns">Campaigns</label>
				</th>
				<td>
					<select name="campaign" id="campaigns">
						<option value=""><?php echo esc_html_e( 'Select', 'community-portal' ); ?></option>
						<?php foreach ( $campaigns->posts as $campaign ) : ?>
							<option value="<?php echo esc_attr( $campaign->ID ); ?>"><?php echo esc_html( $campaign->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	<input type="submit" value="Export Events" id="export-events" class="button action" />
</form>
