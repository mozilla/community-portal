
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
                    $user->data->user_email = 'ryan@playgroundinc.com';

                    $result = mozilla_remove_email_from_list($list, $user->data->user_email);
                    $members_participating = get_post_meta($campaign->ID, 'members-participating', true);
                    $campaigns = get_user_meta($user->ID, 'campaigns', true);

                    if(is_array($members_participating)) {
                        if(($key = array_search($user->ID, $members_participating)) !== false) {
                            unset($members_participating[$key]);
                        }
                    } else {
                        $members_participating = Array();
                    }

                    if(is_array($campaigns)) {
                        if(($key = array_search($campaign->ID, $campaigns)) !== false) {
                            unset($campaigns[$key]);
                        }
                    } else {
                        $campaigns = Array();
                    }

                    update_post_meta($campaign->ID, 'members-participating', $members_participating);
                    update_user_meta($user->ID, 'campaigns', $campaigns);
					print json_encode(Array('status' =>  'OK'));
					
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


function mozilla_mailchimp_subscribe() {

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['campaign']) && strlen(trim($_POST['campaign'])) > 0 && isset($_POST['list']) && strlen($_POST['list']) > 0) {
            $user = wp_get_current_user();

            // Only accepting logged in users at the moment
            if($user->ID !== 0 && isset($user->data->user_email)) {
                $list = trim($_POST['list']);    
                $campaign_id = intval($_POST['campaign']);
                $campaign = get_post($campaign_id);

                if($campaign && $campaign->post_type === 'campaign') {
                    $user->data->user_email = 'ryan@playgroundinc.com';

                    $result = mozilla_add_email_to_list($list, $user->data->user_email);
                    if(isset($result->id)) {
                        $members_participating = get_post_meta($campaign->ID, 'members-participating', true);
                        
                        if(is_array($members_participating)) {
                            $members_participating[] = $user->ID;
                        } else {
                            $members_participating = Array();
                            $members_participating[] = $user->ID;
                        }

                        $members_participating = array_unique($members_participating);
                        
                        $campaigns = get_user_meta($user->ID, 'campaigns', true);
                        if(is_array($campaigns)) {
                            $campaigns[] = $campaign->ID;
                        } else {
                            $campaigns = Array();
                            $campaigns[] = $campaign->ID;
                        }

                        $campaigns = array_unique($campaigns);
    
                        update_post_meta($campaign->ID, 'members-participating', $members_participating);
                        update_user_meta($user->ID, 'campaigns', $campaigns);
                        
                        print json_encode(Array('status' =>  'OK'));
                    } else {
                        print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'User not added'));
                    }
                }

            } else {
                print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'Invalid user'));
            }
        } else {
            print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'Invalid request'));
        }
    } else {
        print json_encode(Array('status'    =>  'ERROR', 'message'  =>  'Invalid request'));
    }

    die();
}
?>