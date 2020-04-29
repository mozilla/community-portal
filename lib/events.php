<?php


function mozilla_update_events_copy($string) {

	$please_string = __('Please', 'community-portal');
    $login_string = __('log in', 'community-portal');
    $create_string = __('to create or join events', 'community-portal');

    $string = "{$please_string} <a href=\"/wp-login.php?action=login\">{$login_string}</a> {$create_string}";
    return $string;
}; 

function mozilla_remove_booking() {
    global $EM_Event;
    $user = wp_get_current_user();


    if($user->ID && $EM_Event->post_id) {    
        $post_id = $EM_Event->post_id;
        $discourse_group_info = mozilla_get_discourse_info($post_id, 'event');
        $discourse_api_data = Array();
        $discourse_api_data['group_id'] = $discourse_group_info['discourse_group_id'];
        $remove = Array();
        $remove[] = mozilla_get_user_auth0($user->ID);
        $discourse_api_data['remove_users'] = $remove;
        
        $discourse = mozilla_discourse_api('groups/users', $discourse_api_data, 'patch');
    }

}


function mozilla_approve_booking($EM_Booking) {
    $user = wp_get_current_user();

    $event_id = $EM_Booking->event_id;
    $post_id = $EM_Booking->event->post_id;
    $discourse_group_info = mozilla_get_discourse_info($post_id, 'event');
        
    $discourse_api_data = Array();
    $discourse_api_data['group_id'] = $discourse_group_info['discourse_group_id'];
    $add = Array();
    $add[] = mozilla_get_user_auth0($user->ID);
    $discourse_api_data['add_users'] = $add;

    $discourse = mozilla_discourse_api('groups/users', $discourse_api_data, 'patch');

    if (intval($EM_Booking->booking_status) === 0) {
        $EM_Booking->booking_status = 1;
        return $EM_Booking;
    }

    return $EM_Booking;
}

function mozilla_events_redirect($location) {
    if (strpos($location, 'event_id') !== false) {
        $location = get_site_url(null, 'events/');
        return $location;
    }

    return $location;
}

function mozilla_delete_events($id, $post) {
    $post_id = $post->post_id;
    wp_delete_post($post_id);
    return $post;
}

function mozilla_add_online_to_countries($countries) {
    $countries = array('OE' => 'Online Event') + $countries;
    return $countries;
}


function mozilla_event_export() {

    if(!is_admin() && in_array('administrator', wp_get_current_user()->roles) === false) {
        return;
    }

    $start = isset($_GET['start']) && strlen($_GET['start']) > 0 ? strtotime(trim($_GET['start'])) : false;
    $end = isset($_GET['end']) && strlen($_GET['end']) > 0 ? strtotime(trim($_GET['end'])) : false;

    $campaign_id = isset($_GET['campaign']) && strlen($_GET['campaign']) > 0 ? intval(trim($_GET['campaign'])) : false;
    $activity_id = isset($_GET['activity']) && strlen($_GET['activity']) > 0 ? intval(trim($_GET['activity'])) : false;

    $args = Array('scope' =>  'all');
    $events = EM_Events::get($args);    
    $related_events = Array();

    $theme_directory = get_template_directory();
    include("{$theme_directory}/languages.php");
    $countries = em_get_countries();

    $related_events = Array();

    foreach($events AS $event) {
        $event_meta = get_post_meta($event->post_id, 'event-meta');

        if($campaign_id || $activity_id) {
            if(isset($event_meta[0]->initiative) && (intval($event_meta[0]->initiative) === $campaign_id || intval($event_meta[0]->initiative) === $activity_id)) {
                if($start && $end) {
                    if(strtotime($event->event_start_date) >= $start && strtotime($event->event_end_date) <= $end) {
                        $event->meta = $event_meta[0];
                        $related_events[] = $event;
                    }
                }
           }
        }

        if($campaign_id === false && $activity_id === false) {
            if($start && $end) {
                if(strtotime($event->event_start_date) >= $start && strtotime($event->event_end_date) <= $end) {
                    $event->meta = $event_meta[0];
                    $related_events[] = $event;
                }
            }
        }
    }

    header("Content-Type: text/csv");
    header("Content-Disposition: attachment;filename=events.csv");
    $out = fopen('php://output', 'w');

    $heading = Array('ID', 'Event Title', 'Event Start Date', 'Event End Date', 'Description', 'Goals', 'Attendee Count', 'Expected Attendee Count', 'Language', 'Location', 'Tags', 'Hosted By', 'User ID', 'Group', 'Group ID', 'Campaign', 'Campaign ID', 'Activity', 'Activity ID');
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

        if($campaign_id) {
            $campaign = get_post($campaign_id);
        } else {
            $campaign = null;
        }

        if($activity_id) {
            $activity = get_post($activity_id);
        } else {
            $activity = null;
        }

        $location = $location->country === 'OE' ? 'Online' : $address;
        $group_object = new BP_Groups_Group($related_event->group_id);
        $group = ($group_object->id) ? $group_object->name : 'N/A';
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
                        $event_creator->data->user_nicename,
                        $user_id,
                        $group,
                        $group_object->id,
                        ($campaign !== null) ? $campaign->post_title : 'N/A',
                        ($campaign !== null) ? $campaign->ID : 'N/A',
                        ($activity !== null) ? $activity->post_title : 'N/A',
                        ($activity !== null) ? $activity->ID : 'N/A',
        );

        fputcsv($out, $row);
    }
    
    fclose($out);

    die();
}

function mozilla_update_event_discourse_data() {

    if(!is_admin() && in_array('administrator', wp_get_current_user()->roles)) {
        return;
    }

    if(isset($_GET['event'])) {
        $event = new EM_Event(intval($_GET['event']), 'post_id');
        $event_meta = get_post_meta(intval($_GET['event']), 'event-meta');

        if(isset($_GET['discourse_group_id'])) {
            $event_meta[0]->discourse_group_id = intval($_GET['discourse_group_id']);
        }

        update_post_meta(intval($_GET['event']), 'event-meta', $event_meta[0]);
    }

    die();
}

function mozilla_add_user_discourse() {

    if(!is_admin() && in_array('administrator', wp_get_current_user()->roles)) {
        return;
    }

    if(isset($_GET['event']) && isset($_GET['user'])) {
        $event = new EM_Event(intval($_GET['event']), 'post_id');
        $discourse_group_info = mozilla_get_discourse_info($_GET['event'], 'event');

        $discourse_api_data = Array();
        $discourse_api_data['group_id'] = $discourse_group_info['discourse_group_id'];
        $user = get_user_by('slug', trim($_GET['user']));

        if($user) {
            $add = Array();
            $add[] = mozilla_get_user_auth0($user->ID);
            $discourse_api_data['add_users'] = $add;
    
            $discourse = mozilla_discourse_api('groups/users', $discourse_api_data, 'patch');
        }
    }


    die();

}

function mozilla_create_booking() {
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'add_event_booking')) {
		if( !empty($_POST['event_id']) ){
			ob_start();
			$em_event = em_get_event( absint($_POST['event_id']) );
			$em_booking = em_get_booking(array(
				'person_id'=>get_current_user_id(), 
				'booking_spaces'=>1, 
				'event_id'=> $_POST['event_id'],
				'booking_status' => 1)
			);
	
			if ( empty($em_event->bookings) ) {
				$em_event->bookings = array();
			}
			array_push($em_event->bookings, $em_booking);
			print_r($array
				// 'status' => $em_bookings->tickets[$em_ticket->ticket_id],
			);
			die();
				// array_push( $em_event->bookings, $em_booking );
				
				// return apply_filters('em_bookings_add', true, $em_booking);

				
				// add_user_to_blog(get_current_blog_id(), get_current_user_id(), get_option('default_role'));
				// die();
			
			// $post_validation = $em_booking->validate();
			// do_action('em_booking_add', $em_event, $em_booking, $post_validation);
			// if( $post_validation ){
			// 	$registration = em_booking_add_registration($em_booking);
				$em_event->get_bookings()->add($em_booking);
			// 	// $em_event->save();
			// 	wp_send_json_success(array(
			// 	'status' => $em_event,
			// 	));
			// 	die();
			// }
			ob_clean();
		}
	}
}

?>