<?php
/**
 * Event Library
 *
 * Event library functions
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

/**
 * String to login for events
 *
 * @param string $string string to return.
 */
function mozilla_update_events_copy( $string ) {

	$please_string = __( 'Please', 'community-portal' );
	$login_string  = __( 'log in', 'community-portal' );
	$create_string = __( 'to create or join events', 'community-portal' );

	$string = "{$please_string} <a href=\"/wp-login.php?action=login\">{$login_string}</a> {$create_string}";
	return $string;
};

/**
 * Remove booking
 */
function mozilla_remove_booking() {
	global $EM_Event;
	$user = wp_get_current_user();

	if ( $user->ID && $EM_Event->post_id ) {
		$post_id                            = $EM_Event->post_id;
		$discourse_group_info               = mozilla_get_discourse_info( $post_id, 'event' );
		$discourse_api_data                 = array();
		$discourse_api_data['group_id']     = $discourse_group_info['discourse_group_id'];
		$remove                             = array();
		$remove[]                           = mozilla_get_user_auth0( $user->ID );
		$discourse_api_data['remove_users'] = $remove;

		$discourse = mozilla_discourse_api( 'groups/users', $discourse_api_data, 'patch' );
	}

}

/**
 * Add Booking
 *
 * @param object $EM_Booking event manager booking object.
 */
function mozilla_approve_booking( $EM_Booking ) {
	$user = wp_get_current_user();

	$event_id             = $EM_Booking->event_id;
	$post_id              = $EM_Booking->event->post_id;
	$discourse_group_info = mozilla_get_discourse_info( $post_id, 'event' );

	$discourse_api_data              = array();
	$discourse_api_data['group_id']  = $discourse_group_info['discourse_group_id'];
	$add                             = array();
	$add[]                           = mozilla_get_user_auth0( $user->ID );
	$discourse_api_data['add_users'] = $add;

	$discourse = mozilla_discourse_api( 'groups/users', $discourse_api_data, 'patch' );

	if ( intval( $EM_Booking->booking_status ) === 0 ) {
		$EM_Booking->booking_status = 1;
		return $EM_Booking;
	}

	return $EM_Booking;
}

/**
 * Redirect user to location
 *
 * @param string $location string location to redirect to.
 */
function mozilla_events_redirect( $location ) {
	if ( strpos( $location, 'event_id' ) !== false ) {
		$location = get_home_url( null, 'events/' );
		return $location;
	}

	return $location;
}


/**
 * Delete event
 *
 * @param integer $id post id.
 * @param object  $post post object.
 */
function mozilla_delete_events( $id, $post ) {
	$post_id = $post->post_id;
	wp_delete_post( $post_id );
	return $post;
}

/**
 * Adds online to the country list
 *
 * @param array $countries an array of countries.
 */
function mozilla_add_online_to_countries( $countries ) {
	$countries = array( 'OE' => 'Online Event' ) + $countries;
	return $countries;
}

/**
 * Export event
 */
function mozilla_event_export() {

	if ( ! is_admin() && false === in_array( 'administrator', wp_get_current_user()->roles, true ) ) {
		return;
	}

	$start = isset( $_GET['start'] ) && strlen( sanitize_text_field( wp_unslash( $_GET['start'] ) ) ) > 0 ? strtotime( trim( sanitize_text_field( wp_unslash( $_GET['start'] ) ) ) ) : false;
	$end   = isset( $_GET['end'] ) && strlen( sanitize_text_field( wp_unslash( $_GET['end'] ) ) ) > 0 ? strtotime( trim( sanitize_text_field( wp_unslash( $_GET['end'] ) ) ) ) : false;

	$campaign_id = isset( $_GET['campaign'] ) && strlen( sanitize_key( wp_unslash( $_GET['campaign'] ) ) ) > 0 ? intval( trim( sanitize_key( wp_unslash( $_GET['campaign'] ) ) ) ) : false;
	$activity_id = isset( $_GET['activity'] ) && strlen( sanitize_key( wp_unslash( $_GET['activity'] ) ) ) > 0 ? intval( trim( sanitize_key( wp_unslash( $_GET['activity'] ) ) ) ) : false;

	$args           = array( 'scope' => 'all' );
	$events         = EM_Events::get( $args );
	$related_events = array();

	$theme_directory = get_template_directory();
	include "{$theme_directory}/languages.php";
	$countries = em_get_countries();

	$related_events = array();

	foreach ( $events as $event ) {
		$event_meta = get_post_meta( $event->post_id, 'event-meta' );

		if ( $campaign_id || $activity_id ) {
			if ( isset( $event_meta[0]->initiative ) && ( intval( $event_meta[0]->initiative ) === $campaign_id || intval( $event_meta[0]->initiative ) === $activity_id ) ) {
				if ( $start && $end ) {
					if ( strtotime( $event->event_start_date ) >= $start && strtotime( $event->event_end_date ) <= $end ) {
						$event->meta      = $event_meta[0];
						$related_events[] = $event;
					}
				}
			}
		}

		if ( false === $campaign_id && false === $activity_id ) {
			if ( $start && $end ) {
				if ( strtotime( $event->event_start_date ) >= $start && strtotime( $event->event_end_date ) <= $end ) {
					$event->meta      = $event_meta[0];
					$related_events[] = $event;
				}
			}
		}
	}

	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment;filename=events.csv' );
	$out = fopen( 'php://output', 'w' );

	$heading = array( 'ID', 'Event Title', 'Event Start Date', 'Event End Date', 'Description', 'Goals', 'Attendee Count', 'Expected Attendee Count', 'Language', 'Location', 'Tags', 'Hosted By', 'User ID', 'Group', 'Group ID', 'Campaign', 'Campaign ID', 'Activity', 'Activity ID' );
	fputcsv( $out, $heading );
	foreach ( $related_events as $related_event ) {
		$attendees       = count( $related_event->get_bookings()->bookings );
		$language        = isset( $related_event->meta->language ) && strlen( $related_event->meta->language ) > 0 ? $languages[ $related_event->meta->language ] : 'N/A';
		$event_meta      = get_post_meta( $related_event->post_id, 'event-meta' );
		$location_type   = isset( $event_meta[0]->location_type ) ? $event_meta[0]->location_type : '';
		$location_object = em_get_location( $related_event->location_id );
		$tag_object      = $related_event->get_categories();
		$tags            = '';
		$user_id         = $related_event->event_owner;
		$event_creator   = get_user_by( 'ID', $user_id );

		foreach ( $tag_object->terms as $tag ) {
			$tags = $tag->name . ', ';
		}

		// Remove last comma.
		$tags = rtrim( $tags, ', ' );

		$address = $location_object->address;
		if ( $location_object->city ) {
			$address = $address . ' ' . $location_object->city;
		}

		if ( $location_object->town ) {
			$address = $address . ' ' . $location_object->town;
		}

		if ( $location_object->country ) {
			$address = $address . ' ' . $countries[ $location_object->country ];
		}

		if ( $campaign_id ) {
			$campaign = get_post( $campaign_id );
		} else {
			$campaign = null;
		}

		if ( $activity_id ) {
			$activity = get_post( $activity_id );
		} else {
			$activity = null;
		}

		$location     = 'OE' === $location->country ? 'Online' : $address;
		$group_object = new BP_Groups_Group( $related_event->group_id );
		$group        = ( $group_object->id ) ? $group_object->name : 'N/A';
		$row          = array(
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
			( null !== $campaign ) ? $campaign->post_title : 'N/A',
			( null !== $campaign ) ? $campaign->ID : 'N/A',
			( null !== $activity ) ? $activity->post_title : 'N/A',
			( null !== $activity ) ? $activity->ID : 'N/A',
		);

		fputcsv( $out, $row );
	}

	fclose( $out );

	die();
}

/**
 * Updates event discourse data as an admin function
 */
function mozilla_update_event_discourse_data() {

	if ( ! is_admin() && in_array( 'administrator', wp_get_current_user()->roles, true ) ) {
		return;
	}

	if ( isset( $_GET['event'] ) ) {
		$event      = new EM_Event( intval( sanitize_key( wp_unslash( $_GET['event'] ) ) ), 'post_id' );
		$event_meta = get_post_meta( intval( sanitize_key( wp_unslash( $_GET['event'] ) ) ), 'event-meta' );

		if ( isset( $_GET['discourse_group_id'] ) ) {
			$event_meta[0]->discourse_group_id = intval( sanitize_key( wp_unslash( $_GET['discourse_group_id'] ) ) );
		}

		update_post_meta( intval( sanitize_key( wp_unslash( $_GET['event'] ) ) ), 'event-meta', $event_meta[0] );
	}

	die();
}

/**
 * Adds a user to discourse
 */
function mozilla_add_user_discourse() {

	if ( ! is_admin() && in_array( 'administrator', wp_get_current_user()->roles, true ) ) {
		return;
	}

	if ( isset( $_GET['event'] ) && isset( $_GET['user'] ) ) {
		$event                = new EM_Event( intval( sanitize_key( wp_unslash( $_GET['event'] ) ) ), 'post_id' );
		$discourse_group_info = mozilla_get_discourse_info( sanitize_key( wp_unslash( $_GET['event'] ) ), 'event' );

		$discourse_api_data             = array();
		$discourse_api_data['group_id'] = $discourse_group_info['discourse_group_id'];
		$user                           = get_user_by( 'slug', trim( sanitize_key( wp_unslash( $_GET['user'] ) ) ) );

		if ( $user ) {
			$add                             = array();
			$add[]                           = mozilla_get_user_auth0( $user->ID );
			$discourse_api_data['add_users'] = $add;

			$discourse = mozilla_discourse_api( 'groups/users', $discourse_api_data, 'patch' );
		}
	}

	die();

}


/**
 * Get an array of locations
 **/
function mozilla_get_locations() {
	$json_users = array();

	if ( isset( $_GET['q'] ) ) {
		$q = trim( sanitize_user( wp_unslash( $_GET['q'] ) ) );
		if ( strlen( $q ) > 0 ) {
			$all_locations      = EM_Locations::get();
			$matching_locations = array();
			foreach ( $all_locations as $location ) {
				if ( false !== stripos( $location->location_name, $q ) ) {
					$location_type           = get_post_meta( $location->post_id, 'location-type', true );
					$location->location_type = isset( $location_type ) && strlen( $location_type ) > 0 ? $location_type : null;
					array_push( $matching_locations, $location );
					if ( count( $matching_locations ) > 4 ) {
						break;
					}
				}
			}

			echo wp_json_encode( $matching_locations );
		}
	}
	die();
}

/**
 * Add location type to location post metadata
 *
 * @param integer $post_id post ID.
 * @param string  $location_type location type value.
 */
function mozilla_add_location_type( $post_id, $location_type = null ) {
	if ( empty( $post_id ) ) {
		return;
	}
	$location = em_get_location( $post_id );
	if ( ! empty( $location_type ) ) {
		update_post_meta( $location->post_id, 'location-type', $location_type );
		return;
	}
	if ( isset( $_POST['location-type'] ) ) {
		$location_type = sanitize_text_field( wp_unslash( $_POST['location-type'] ) );
		update_post_meta( $post_id, 'location-type', $location_type );
	}
}


/**
 * Add location type to location post metadata
 *
 * @param integer $post_id post ID.
 * @param mixed   $post location post object.
 * @param bool    $update if this is an update.
 */
function mozilla_handle_location_save( $post_id, $post, $update ) {
	if ( isset( $_POST['location-type'] ) ) {
		$location_type = sanitize_text_field( wp_unslash( $_POST['location-type'] ) );
		update_post_meta( $post_id, 'location-type', $location_type );
	}
}

add_action( 'save_post_location', 'mozilla_handle_location_save', 10, 3 );

add_filter( 'em_events_get_sql', 'mozilla_custom_ics', 99999 );

function mozilla_custom_ics( $sql ) {
	// Get events of a Buddypress group
	if ( isset( $_GET['group'] ) ) {
		$group_slug = esc_sql( $_GET['group'] );
		$group      = groups_get_groups( array( 'slug' => array( $group_slug ) ) );
		$group      = $group['groups'][0];
		if ( ! empty( $group->id ) ) {
			$sql = str_replace( ' AND event_owner=0', '', $sql );
			$sql = str_replace( 'WHERE', 'WHERE wp_em_events.group_id=' . $group->id . ' AND ', $sql );
		}
	}

	if ( isset( $_GET['event_id'] ) ) {
		$event_id = esc_sql( $_GET['event_id'] );
		$sql      = str_replace( 'WHERE', 'WHERE wp_em_events.event_id=' . $event_id . ' AND ', $sql );
	}

	return $sql;
}
