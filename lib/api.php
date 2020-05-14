<?php
/**
 * API
 *
 * API functions
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

/**
 * Get Discourse Information
 *
 * @param integer $id post id.
 * @param string  $type group / category.
 */
function mozilla_get_discourse_info( $id, $type = 'group' ) {
	$discourse_info = array();

	if ( 'event' === $type ) {
		if ( $id ) {
			$event_meta = get_post_meta( $id, 'event-meta' );

			if ( ! empty( $event_meta ) && isset( $event_meta[0]->discourse_group_id ) ) {
				$data                                 = array();
				$data['group_id']                     = $event_meta[0]->discourse_group_id;
				$discourse_group                      = mozilla_discourse_api( 'groups', $data, 'get' );
				$discourse_info['discourse_group_id'] = $data['group_id'];

				if ( $discourse_group && ! isset( $discourse_group->status ) ) {

					$discourse_info['discourse_group_name']        = $discourse_group->name;
					$discourse_info['discourse_group_description'] = $discourse_group->description;
					$discourse_info['discourse_group_users']       = $discourse_group->users;
				}
			}
		}
		return $discourse_info;
	} else {

		if ( $id ) {
			$group_meta = groups_get_groupmeta( $id, 'meta' );
			if ( isset( $group_meta['discourse_category_id'] ) ) {
				$data                                    = array();
				$data['category_id']                     = $group_meta['discourse_category_id'];
				$discourse_category                      = mozilla_discourse_api( 'categories', $data, 'get' );
				$discourse_info['discourse_category_id'] = $group_meta['discourse_category_id'];
				if ( $discourse_category && ! isset( $discourse_category->status ) ) {
					$discourse_info['discourse_category_name']        = $discourse_category->name;
					$discourse_info['discourse_category_description'] = $discourse_category->description;
					$discourse_info['discourse_category_url']         = $discourse_category->url;
					$discourse_info['discourse_category_groups']      = $discourse_category->groups;
				}
			}

			if ( isset( $group_meta['discourse_group_id'] ) ) {
				$discourse_info['discourse_group_id'] = $group_meta['discourse_group_id'];
				$data                                 = array();
				$data['group_id']                     = $group_meta['discourse_group_id'];
				$discourse_group                      = mozilla_discourse_api( 'groups', $data, 'get' );
				if ( $discourse_group && ! isset( $discourse_group->status ) ) {
					$discourse_info['discourse_group_name']        = $discourse_group->name;
					$discourse_info['discourse_group_description'] = $discourse_group->description;
					$discourse_info['discourse_group_users']       = $discourse_group->users;
				}
			}
			return $discourse_info;
		}
	}

	return false;
}

/**
 * Makes a Discourse API call
 *
 * @param string $type category or group.
 * @param array  $data data to use in the API call.
 * @param string $request type of call to make (post, get, patch, delete).
 */
function mozilla_discourse_api( $type, $data, $request = 'get' ) {
	$discourse = false;

	$options = wp_load_alloptions();
	if ( isset( $options['discourse_api_key'] ) && strlen( $options['discourse_api_key'] ) > 0 && isset( $options['discourse_api_url'] ) && strlen( $options['discourse_api_url'] ) > 0 ) {

		// Get the API URL without the trailing slash.
		$api_url = rtrim( $options['discourse_api_url'], '/' );
		$api_key = trim( $options['discourse_api_key'] );

		$curl = curl_init();

		curl_setopt(
			$curl,
			CURLOPT_HTTPHEADER,
			array(
				'Content-Type: Application/json',
				"x-api-key: {$api_key}",
			)
		);
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$type     = strtolower( $type );
		$api_data = array();

		$request = strtolower( $request );
		$type    = strtolower( $type );

		switch ( $type ) {
			case 'categories':
				switch ( $request ) {
					case 'post':
						if ( isset( $data['name'] ) && strlen( $data['name'] ) > 0 ) {
							curl_setopt( $curl, CURLOPT_URL, "{$api_url}/categories" );
							curl_setopt( $curl, CURLOPT_POST, 1 );
							$api_data['name'] = $data['name'];

							if ( isset( $data['description'] ) && strlen( $data['description'] ) > 0 ) {
								$api_data['description'] = $data['description'];
							}

							if ( isset( $data['groups'] ) && ! empty( $data['groups'] ) ) {
								$api_data['groups'] = $data['groups'];
							}
						}
						break;
					case 'patch':
						if ( isset( $data['category_id'] ) && intval( $data['category_id'] ) > 0 ) {
							$api_data['name'] = $data['name'];
							if ( isset( $data['description'] ) && strlen( $data['description'] ) > 0 ) {
								$api_data['description'] = $data['description'];
							}

							if ( isset( $data['groups'] ) && ! empty( $data['groups'] ) ) {
								$api_data['groups'] = $data['groups'];
							}

							curl_setopt( $curl, CURLOPT_URL, "{$api_url}/categories/{$data['category_id']}" );
							curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PATCH' );
						}

						break;
					case 'delete':
						if ( isset( $data['category_id'] ) && intval( $data['category_id'] ) > 0 ) {
							curl_setopt( $curl, CURLOPT_URL, "{$api_url}/categories/{$data['category_id']}" );
							curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'DEL' );
						}

						break;
					default:
						if ( isset( $data['category_id'] ) && intval( $data['category_id'] ) > 0 ) {
							curl_setopt( $curl, CURLOPT_URL, "{$api_url}/categories/{$data['category_id']}" );
						}

						curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'GET' );

				}
				break;
			case 'groups':
				switch ( $request ) {
					case 'post':
						if ( isset( $data['name'] ) && strlen( $data['name'] ) > 0 ) {
							curl_setopt( $curl, CURLOPT_POST, 1 );
							curl_setopt( $curl, CURLOPT_URL, "{$api_url}/groups" );

							$api_data['name'] = $data['name'];
							if ( isset( $data['description'] ) && strlen( $data['description'] ) > 0 ) {
								$api_data['description'] = $data['description'];
							}

							if ( isset( $data['users'] ) && is_array( $data['users'] ) ) {
								$api_data['users'] = $data['users'];
							}
						}

						break;
					case 'patch':
						if ( isset( $data['group_id'] ) && intval( $data['group_id'] ) > 0 ) {
							$api_data['name'] = $data['name'];
							if ( isset( $data['description'] ) && strlen( $data['description'] ) > 0 ) {
								$api_data['description'] = $data['description'];
							}

							curl_setopt( $curl, CURLOPT_URL, "{$api_url}/groups/{$data['group_id']}" );
							curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PATCH' );
						}

						break;
					case 'delete':
						if ( isset( $data['group_id'] ) && intval( $data['group_id'] ) > 0 ) {
							curl_setopt( $curl, CURLOPT_URL, "{$api_url}/groups/{$data['group_id']}" );
							curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'DEL' );
						}

						break;
					default:
						if ( isset( $data['group_id'] ) && intval( $data['group_id'] ) > 0 ) {
							curl_setopt( $curl, CURLOPT_URL, "{$api_url}/groups/{$data['group_id']}" );
							curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'GET' );
						}
				}
				break;
			case 'groups/users':
				if ( isset( $data['group_id'] ) && intval( $data['group_id'] ) > 0 ) {
					curl_setopt( $curl, CURLOPT_URL, "{$api_url}/groups/{$data['group_id']}/users" );
					curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PATCH' );

					if ( isset( $data['add_users'] ) && is_array( $data['add_users'] ) ) {
						$api_data['add'] = $data['add_users'];
					}

					if ( isset( $data['remove_users'] ) && is_array( $data['remove_users'] ) ) {
						$api_data['remove'] = $data['remove_users'];
					}
				}

				break;
		}

		if ( ! empty( $api_data ) || 'get' !== $request ) {
			$json_data = wp_json_encode( $api_data );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $json_data );
		}

		$curl_result = curl_exec( $curl );
		$discourse   = json_decode( $curl_result );
	}

	return $discourse;
}

/**
 * Gets discourse category topics
 *
 * @param string $url URL to get topics.
 */
function mozilla_discourse_get_category_topics( $url ) {
	$curl = curl_init();
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

	curl_setopt( $curl, CURLOPT_URL, "{$url}.json" );
	$curl_result        = curl_exec( $curl );
	$discourse_category = json_decode( $curl_result );

	curl_close( $curl );

	if ( isset( $discourse_category->topic_list ) && isset( $discourse_category->topic_list->topics ) ) {
		$topics = is_array( $discourse_category->topic_list->topics ) ? $discourse_category->topic_list->topics : array();
	} else {
		$topics = array();
	}

	return $topics;
}

/**
 * Create mailchimp mailing list
 *
 * @param object $campaign campaign object.
 */
function mozilla_create_mailchimp_list( $campaign ) {
	$options = wp_load_alloptions();

	$create_audience = get_field( 'mailchimp_integration', $campaign->ID );

	if ( $create_audience && isset( $options['mailchimp'] ) ) {
		$api_key = trim( $options['mailchimp'] );

		if ( ! empty( $api_key ) ) {
			$dc = end( explode( '-', $api_key ) );
		}

		if ( ! empty( $dc ) ) {
			$mailchimp_check = get_post_meta( $campaign->ID, 'mailchimp-list-id', true );

			if ( empty( $mailchimp_check ) ) {

				$curl    = curl_init();
				$api_url = "https://{$dc}.api.mailchimp.com/3.0/lists";

				curl_setopt( $curl, CURLOPT_URL, $api_url );
				curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
				curl_setopt( $curl, CURLOPT_USERPWD, 'user:' . $api_key );
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $curl, CURLOPT_POST, true );

				$campaign_list_name = "{$campaign->post_title} - Mozilla";

				$data         = array();
				$data['name'] = $campaign_list_name;

				$data['contact'] = array(
					'company'  => $options['company'],
					'address1' => $options['address'],
					'city'     => $options['city'],
					'state'    => $options['state'],
					'zip'      => $options['zip'],
					'country'  => $options['country'],
					'phone'    => $options['phone'],
				);

				$data['campaign_defaults'] = array(
					'from_name'  => $campaign_list_name,
					'from_email' => $options['report_email'],
					'subject'    => $campaign_list_name,
					'language'   => 'English',
				);

				$data['permission_reminder'] = "You are participating in the Mozilla {$campaign->post_title} campaign";
				$data['email_type_option']   = true;

				$json = wp_json_encode( $data );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $json );
				$result = curl_exec( $curl );
				curl_close( $curl );

				$json_result = json_decode( $result );
				return update_post_meta( $campaign->ID, 'mailchimp-list-id', $json_result );
			}
		}
	}
	return false;
}

/**
 * Remove email from mailing list
 *
 * @param integer $id ID of mailing list.
 * @param string  $email The email to remove.
 */
function mozilla_remove_email_from_list( $id, $email ) {
	$options         = wp_load_alloptions();
	$subscriber_hash = md5( strtolower( $email ) );

	if ( isset( $options['mailchimp'] ) ) {
		$api_key = trim( $options['mailchimp'] );

		if ( ! empty( $api_key ) ) {
			$dc = end( explode( '-', $api_key ) );
		}
		if ( ! empty( $dc ) ) {
			$curl    = curl_init();
			$api_url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$id}/members/{$subscriber_hash}";

			curl_setopt( $curl, CURLOPT_URL, $api_url );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
			curl_setopt( $curl, CURLOPT_USERPWD, 'user:' . $api_key );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'DELETE' );

			$result = curl_exec( $curl );
			curl_close( $curl );

			$json_result = json_decode( $result );
			return $json_result;
		}
	}

	return false;
}

/**
 * Add email to mailchimp mailing list
 *
 * @param integer $id ID of mailing list.
 * @param string  $email email to add.
 * @param string  $name name.
 */
function mozilla_add_email_to_list( $id, $email, $name = false ) {

	$options = wp_load_alloptions();

	if ( isset( $options['mailchimp'] ) ) {
		$api_key = trim( $options['mailchimp'] );

		if ( ! empty( $api_key ) ) {
			$dc = end( explode( '-', $api_key ) );
		}

		if ( ! empty( $dc ) ) {
			$curl    = curl_init();
			$api_url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$id}/members";

			curl_setopt( $curl, CURLOPT_URL, $api_url );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
			curl_setopt( $curl, CURLOPT_USERPWD, 'user:' . $api_key );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

			$data                  = array();
			$data['email_address'] = $email;
			$data['status']        = 'subscribed';
			if ( $name ) {
				$data['merge_fields'] = $name;
			}

			$json = wp_json_encode( $data );

			curl_setopt( $curl, CURLOPT_POSTFIELDS, $json );
			$result = curl_exec( $curl );
			curl_close( $curl );
			$json_result = json_decode( $result );
			return $json_result;
		}
	}

	return false;
}


