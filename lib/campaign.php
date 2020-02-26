<?php 

function mozilla_mailchimp_unsubscribe() {
	if($_SERVER['REQUEST_METHOD'] === 'POST') {
		if(isset($_POST['campaign']) && strlen($_POST['campaign']) > 0) {
			$user = wp_get_current_user();
			if(isset($user->data->user_email)) {
				$campaign = $_POST['campaign'];    
				$result = mozilla_remove_email_from_list($campaign, $user->data->user_email);
			}
		}
	}
}