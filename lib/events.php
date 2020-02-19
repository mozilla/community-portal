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