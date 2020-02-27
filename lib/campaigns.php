<?php 

function mozilla_mailchimp_unsubscribe() {
	if($_SERVER['REQUEST_METHOD'] === 'POST') {
		if(isset($_POST['list']) && strlen($_POST['list']) > 0) {
			$user = wp_get_current_user();
			if(isset($user->data->user_email)) {
				$list = trim($_POST['list']);    
				$campaign_id = intval($_POST['campaign']);
				$campaign = get_post($campaign_id);
				if ($campaign && $campaign->post_type === 'campaign') {
					$result = mozilla_remove_email_from_list($list, $user->data->user_email);
					if(isset($result->id)) {
						print json_encode(Array('status' =>  'success'));
					} else {
						print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'User not unsubscribed'));
					}
				}
			} else {
				print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'Could not find User email'));
			}
		} else {
			print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'No list provided. Please provide list ID'));
		}
	} else {
		print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'This method is not allowed'));
	}
	die();
}