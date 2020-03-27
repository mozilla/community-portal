
<?php 



print_r($languages);


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

function mozilla_download_campaign_events() {

    if(!is_admin()) {
        return;
    }

    if(isset($_GET['campaign']) && strlen($_GET['campaign']) > 0) {
        $campaign = get_post(intval($_GET['campaign']));

        $args = Array('scope' =>  'all');
        $events = EM_Events::get($args);    
        $related_events = Array();

        foreach($events AS $event) {
            $event_meta = get_post_meta($event->post_id, 'event-meta');
            if(isset($event_meta[0]->initiative) && intval($event_meta[0]->initiative) === $campaign->ID) {
                $event->meta = $event_meta[0];
                $related_events[] = $event;
            }
        }

        $theme_directory = get_template_directory();
        include("{$theme_directory}/languages.php");
        $countries = em_get_countries();

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment;filename=campaign-{$_GET['campaign']}-events.csv");
        $out = fopen('php://output', 'w');

        $heading = Array('ID', 'Event Title', 'Event Start Date', 'Event End Date', 'Description', 'Goals', 'Attendee Count', 'Expected Attendee Count', 'Language', 'Location', 'Tags', 'Hosted By', 'Group');
        fputcsv($out, $heading);

        foreach($related_events AS $related_event) {
            $attendees = sizeof($related_event->get_bookings()->bookings);
            $language = isset($related_event->meta->language) && strlen($related_event->meta->language) > 0  ? $languages[$related_event->meta->language] : 'N/A';
            $event_meta = get_post_meta($related_event->post_id, 'event-meta');
            $location_type = isset($event_meta[0]->location_type) ? $event_meta[0]->location_type : '';
            $location_object = em_get_location($related_event->location_id);
            $tag_object = $related_event->get_categories();
            $tags = '';
            $user_id = $related_event->event_owner; 
            $event_creator = get_user_by('ID', $user_id);

            foreach($tag_object->terms AS $tag) {
                $tags = $tag->name.', ';
            }

            // Remove last comma
            $tags = rtrim($tags, ', ');

            $address = $location_object->address;
            if($location_object->city) {
                $address = $address.' '.$location_object->city;
            }

            if($location_object->town) {
                $address = $address.' '.$location_object->town;
            }

            if($location_object->country) {
                $address = $address.' '.$countries[$location_object->country];
            }

            $location = $location->country === 'OE' ? 'Online' : $address;
            $group_object = new BP_Groups_Group($related_event->group_id);
            $group = ($group_object->id) ? "{$group_object->name} ($group_object->id)" : 'N/A';
            $row = Array(
                            $related_event->event_id, 
                            $related_event->name,
                            $related_event->event_start_date, 
                            $related_event->event_end_date,
                            $related_event->post_content,
                            $related_event->meta->goal,
                            $attendees, 
                            $related_event->meta->projected_attendees,
                            $language,
                            $location,
                            $tags,
                            "{$event_creator->data->user_nicename} ({$user_id})",
                            $group
            );

            fputcsv($out, $row);

        }

        fclose($out);
    }   


    die();
}

?>