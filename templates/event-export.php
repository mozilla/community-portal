<?php
	$args = Array(
		'post_type' =>  'campaign',
		'per_page'  =>  -1
	);

	$campaigns = new WP_Query($args);

	$args = Array(
		'post_type' => 'activity',
	);

	$activities = new WP_Query($args);
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
					<select name="activitiy" id="activities">
						<option value=""><?php print __('Select', 'community-portal');?></option>
						<?php foreach($activities->posts as $activity): ?>
							<option value="<?php echo $activity->ID ?>"><?php echo $activity->post_title ?></option>
						<?php endforeach;?>
					</select>
                </td>
            </tr>
			<tr>
                <th scope="row">
                    <label for="campaigns">Campaigns</label>
                </th>
                <td>
					<select name="campaign" id="campaigns">
						<option value=""><?php print __('Select', 'community-portal');?></option>
						<?php foreach($campaigns->posts as $campaign): ?>
							<option value="<?php echo $campaign->ID ?>"><?php echo $campaign->post_title ?></option>
						<?php endforeach;?>
					</select>
                </td>
            </tr>
        </tbody>
    </table>
    <input type="submit" value="Export Events" id="export-events" class="button action" />
</form>
