<?php


function mozilla_update_events_copy($string) {
    $string = 'Please <a href="/wp-login.php?action=login">log in</a> to create or join events';
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

function mozilla_save_event($post_id, $post, $update) {
    if ($post->post_type === 'event') {

        $user = wp_get_current_user();

        $event = new stdClass();
        $event->image_url = esc_url_raw($_POST['image_url']);
        $event->location_type = sanitize_text_field($_POST['location-type']);
        $event->external_url = esc_url_raw($_POST['event_external_link']);
        $event->campaign = sanitize_text_field($_POST['event_campaign']);

        $discourse_api_data = Array();

        $discourse_api_data['name'] = $post->post_name;
        $discourse_api_data['description'] = $post->post_content;
        
        if($update) {
            $event_meta = get_post_meta($post_id, 'event-meta');
            if(!empty($event_meta) && isset($event_meta[0]->discourse_group_id)) {
                $discourse_api_data['group_id'] = $event_meta[0]->discourse_group_id;
                $discourse_event = mozilla_get_discourse_info($post_id, 'event');
                $discourse_api_data['users'] = $discourse_event['discourse_group_users'];
                $discourse_group = mozilla_discourse_api('groups', $discourse_api_data, 'patch');
            }
        } else {
            $auth0Ids = Array();
            $auth0Ids[] = mozilla_get_user_auth0($user->ID);
            $discourse_api_data['users'] = $auth0Ids;
            $discourse_group = mozilla_discourse_api('groups', $discourse_api_data, 'post');
        }

        if($discourse_group) {
            $event->discourse_group_id = $discourse_group->id;
        }

        update_post_meta($post_id, 'event-meta', $event);
    }
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


?>