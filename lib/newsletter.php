<?php 

function mozilla_newsletter_subscribe() {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$user = wp_get_current_user();
		if (isset($user)) {
			if (isset($_POST['subscribed'])) {
				$subscribed = intval($_POST['subscribed']);
				update_user_meta($user->ID, 'newsletter', $subscribed);
			}
			wp_send_json_success(array(
				'status' => 'success',
			));
		}
		wp_send_json_success(array(
			'status' => 'success',
		));
	}
}