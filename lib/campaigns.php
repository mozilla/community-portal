<?php
/**
 * Campaigns Library
 *
 * Campaigns Functionality
 *
 * @package    WordPress
 * @subpackage community-portal
 * @version    1.0.0
 * @author     Playground Inc.
 */

?>

<?php

/**
 * Unsubscribe from MailChimp mailing list
 */
function mozilla_mailchimp_unsubscribe() {
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || false === wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'mailing-list' ) ) {
			print wp_json_encode(
				array(
					'status'  => 'ERROR',
					'message' => 'This action is not allowed',
				)
			);
			die();
		}
		if ( isset( $_POST['list'] ) && isset( $_POST['campaign'] ) && strlen( sanitize_key( $_POST['list'] ) ) > 0 ) {
			$user = wp_get_current_user();

			if ( isset( $user->data->user_email ) ) {
				$list        = trim( sanitize_key( $_POST['list'] ) );
				$campaign_id = intval( sanitize_key( $_POST['campaign'] ) );
				$campaign    = get_post( $campaign_id );

				if ( $campaign && 'campaign' === $campaign->post_type ) {

					$result                = mozilla_remove_email_from_list( $list, $user->data->user_email );
					$members_participating = get_post_meta( $campaign->ID, 'members-participating', true );
					$campaigns             = get_user_meta( $user->ID, 'campaigns', true );

					if ( is_array( $members_participating ) ) {
						$key = array_search( $user->ID, $members_participating, true );
						if ( false !== $key ) {
							unset( $members_participating[ $key ] );
						}
					} else {
						$members_participating = array();
					}

					if ( is_array( $campaigns ) ) {
						$key = array_search( $campaign->ID, $campaigns, true );
						if ( false !== $key ) {
							unset( $campaigns[ $key ] );
						}
					} else {
						$campaigns = array();
					}

					update_post_meta( $campaign->ID, 'members-participating', $members_participating );
					update_user_meta( $user->ID, 'campaigns', $campaigns );
					print wp_json_encode( array( 'status' => 'OK' ) );

				}
			} else {
				print wp_json_encode(
					array(
						'status'  => 'ERROR',
						'message' => 'Could not find User email',
					)
				);
			}
		} else {
			print wp_json_encode(
				array(
					'status'  => 'ERROR',
					'message' => 'No list provided. Please provide list ID',
				)
			);
		}
	} else {
		print wp_json_encode(
			array(
				'status'  => 'ERROR',
				'message' => 'This method is not allowed',
			)
		);
	}
	die();
}

/**
 * Subscribe to MailChimp mailing list
 */
function mozilla_mailchimp_subscribe() {
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || false === wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'mailing-list' ) ) {
			print wp_json_encode(
				array(
					'status'  => 'ERROR',
					'message' => 'This action is not permitted.',
				)
			);
			die();
		}
		if ( isset( $_POST['campaign'] ) && strlen( trim( sanitize_key( $_POST['campaign'] ) ) ) > 0 && isset( $_POST['list'] ) && strlen( sanitize_key( $_POST['list'] ) ) > 0 ) {
			$user = wp_get_current_user();

			// Only accepting logged in users at the moment.
			if ( 0 !== $user->ID && isset( $user->data->user_email ) ) {
				$list        = trim( sanitize_key( $_POST['list'] ) );
				$campaign_id = intval( sanitize_key( $_POST['campaign'] ) );
				$campaign    = get_post( $campaign_id );

				if ( $campaign && 'campaign' === $campaign->post_type ) {

					$result = mozilla_add_email_to_list( $list, $user->data->user_email );
					if ( isset( $result->id ) ) {
						$members_participating = get_post_meta( $campaign->ID, 'members-participating', true );

						if ( is_array( $members_participating ) ) {
							$members_participating[] = $user->ID;
						} else {
							$members_participating   = array();
							$members_participating[] = $user->ID;
						}

						$members_participating = array_unique( $members_participating );

						$campaigns = get_user_meta( $user->ID, 'campaigns', true );
						if ( is_array( $campaigns ) ) {
							$campaigns[] = $campaign->ID;
						} else {
							$campaigns   = array();
							$campaigns[] = $campaign->ID;
						}

						$campaigns = array_unique( $campaigns );

						update_post_meta( $campaign->ID, 'members-participating', $members_participating );
						update_user_meta( $user->ID, 'campaigns', $campaigns );

						print wp_json_encode( array( 'status' => 'OK' ) );
					} else {
						print wp_json_encode(
							array(
								'status'  => 'ERROR',
								'message' => 'User not added',
							)
						);
					}
				}
			} elseif ( isset( $_POST['first_name'] ) && strlen( sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) ) > 0
				&& isset( $_POST['last_name'] ) && strlen( sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) ) > 0
				&& isset( $_POST['email'] ) && strlen( sanitize_email( wp_unslash( $_POST['email'] ) ) ) > 0 ) {
				$list          = trim( sanitize_key( wp_unslash( $_POST['list'] ) ) );
				$name          = array();
				$name['FNAME'] = trim( sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) );
				$name['LNAME'] = trim( sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) );
				$email         = trim( sanitize_email( wp_unslash( $_POST['email'] ) ) );

				$result = mozilla_add_email_to_list( $list, $email, $name );
				if ( $result ) {
					print wp_json_encode( array( 'status' => 'OK' ) );
				}
			} else {
				print wp_json_encode(
					array(
						'status'  => 'ERROR',
						'message' => 'Invalid request',
					)
				);
			}
		} else {
			print wp_json_encode(
				array(
					'status'  => 'ERROR',
					'message' => 'Invalid request',
				)
			);
		}
	} else {
		print wp_json_encode(
			array(
				'status'  => 'ERROR',
				'message' => 'Invalid request',
			)
		);
	}

	die();
}

/**
 * Function to allow admins to download events by campaign
 */
function mozilla_download_campaign_events() {

	if ( ! is_admin() && in_array( 'administrator', wp_get_current_user()->roles, true ) === false ) {
		return;
	}

	// Verify nonce.
	if ( ! isset( $_GET['nonce'] ) || false === wp_verify_nonce( sanitize_key( $_GET['nonce'] ), 'campaign-events' ) ) {
		return;
	}

	if ( isset( $_GET['campaign'] ) && strlen( sanitize_key( $_GET['campaign'] ) ) > 0 ) {
		$campaign_id = sanitize_key( $_GET['campaign'] );
		$campaign    = get_post( intval( sanitize_key( $campaign_id ) ) );

		$args           = array( 'scope' => 'all' );
		$events         = EM_Events::get( $args );
		$related_events = array();

		foreach ( $events as $event ) {
			$event_meta = get_post_meta( $event->post_id, 'event-meta' );
			if ( isset( $event_meta[0]->initiative ) && intval( $event_meta[0]->initiative ) === intval( $campaign->ID ) ) {
				$event->meta      = $event_meta[0];
				$related_events[] = $event;
			}
		}

		$theme_directory = get_template_directory();
		include "{$theme_directory}/languages.php";
		$countries = em_get_countries();

		header( 'Content-Type: text/csv' );
		header( "Content-Disposition: attachment;filename=campaign-{$campaign_id}-events.csv" );
		$out = fopen( 'php://output', 'w' );

		$heading = array( 'ID', 'Event Title', 'Event Start Date', 'Event End Date', 'Description', 'Goals', 'Attendee Count', 'Expected Attendee Count', 'Language', 'Location', 'Tags', 'Hosted By', 'User ID', 'Group', 'Group ID' );
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
			);

			fputcsv( $out, $row );

		}

		fclose( $out );
	}

	die();
}


