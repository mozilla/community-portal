<?php
/**
 * Members theme
 *
 * Members library functions
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<?php

$theme_directory = get_template_directory();
require "{$theme_directory}/class-privacysettings.php";

/**
 * Get auth0
 *
 * @param integer $id WordPress user id.
 **/
function mozilla_get_user_auth0( $id ) {
	$meta = get_user_meta( $id );
	return ( isset( $meta['wp_auth0_id'][0] ) ) ? $meta['wp_auth0_id'][0] : false;
}

/**
 * Validate user name
 **/
function mozilla_validate_username() {

	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'GET' === $_SERVER['REQUEST_METHOD'] ) {
		if ( isset( $_GET['u'] ) ) {
			$u = trim( sanitize_user( wp_unslash( $_GET['u'] ) ) );
			if ( strlen( $u ) > 0 ) {

				$current_user_id = get_current_user_id();

				$query = new WP_User_Query(
					array(
						'search'         => $u,
						'search_columns' => array(
							'user_nicename',
						),
						'exclude'        => array( $current_user_id ),
					)
				);
				echo count( $query->get_results() ) === 0 ? wp_json_encode( true ) : wp_json_encode( false );
			}
		}
	}
	die();
}

/**
 * Validate email
 **/
function mozilla_validate_email() {

	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'GET' === $_SERVER['REQUEST_METHOD'] ) {
		if ( isset( $_GET['u'] ) ) {
			$u = trim( sanitize_email( wp_unslash( $_GET['u'] ) ) );
			if ( strlen( $u ) > 0 ) {
				$current_user_id = get_current_user_id();

				$query = new WP_User_Query(
					array(
						'search'         => $u,
						'search_columns' => array(
							'user_email',
						),
						'exclude'        => array( $current_user_id ),
					)
				);

				echo count( $query->get_results() ) === 0 ? wp_json_encode( true ) : wp_json_encode( false );
				die();
			}
		}
	}

	die();
}

/**
 * Get a list of users
 **/
function mozilla_get_users() {
	$json_users = array();

	if ( isset( $_GET['q'] ) ) {
		$q = trim( sanitize_user( wp_unslash( $_GET['q'] ) ) );
		if ( strlen( $q ) > 0 ) {
			$current_user_id = get_current_user_id();

			$query = new WP_User_Query(
				array(
					'search'         => "*{$q}*",
					'search_columns' => array(
						'user_nicename',
					),
					'exclude'        => array( $current_user_id ),
				)
			);
			echo wp_json_encode( $query->get_results() );
		}
	}
	die();
}

/**
 * After user is created
 *
 * @param integer $user_id WordPress user id.
 * @param array   $userinfo User meta.
 * @param boolean $is_new Is this a new user.
 * @param string  $id_token token string.
 * @param string  $access_token token string.
 * @param string  $refresh_token token string.
 **/
function mozilla_post_user_creation( $user_id, $userinfo, $is_new, $id_token, $access_token, $refresh_token ) {
	$meta                = get_user_meta( $user_id );
	$current_translation = mozilla_get_current_translation();

	if ( $is_new || ! isset( $meta['agree'][0] ) || ( isset( $meta['agree'][0] ) && 'I Agree' !== $meta['agree'][0] ) ) {
		$user = get_user_by( 'ID', $user_id );
		if ( $current_translation ) {
			wp_safe_redirect( "/{$current_translation}/people/{$user->data->user_nicename}/profile/edit/group/1/" );
		} else {
			wp_safe_redirect( "/people/{$user->data->user_nicename}/profile/edit/group/1/" );
		}
		exit();
	}

	if ( isset( $_COOKIE['mozilla-redirect'] ) ) {
		$redirect = sanitize_text_field( wp_unslash( $_COOKIE['mozilla-redirect'] ) );
		if ( strlen( $redirect ) > 0 ) {
			unset( $_COOKIE['mozilla-redirect'] );
			wp_safe_redirect( $redirect );
			exit();
		}
	}
}

/**
 * Update member information
 **/
function mozilla_update_member() {
	// Submitted Form!
	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		if ( is_user_logged_in() && ! empty( $_REQUEST['my_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['my_nonce_field'] ) ), 'protect_content' ) ) {
			$user = wp_get_current_user()->data;

			// Get current meta to compare to!
			$meta = get_user_meta( $user->ID );

			$required = array(
				'username',
				'username_visibility',
				'first_name',
				'first_name_visibility',
				'last_name',
				'last_name_visibility',
				'email',
				'email_visibility',
				'agree',
			);

			$additional_fields = array(
				'image_url',
				'profile_image_url_visibility',
				'pronoun',
				'city',
				'country',
				'profile_pronoun_visibility',
				'bio',
				'profile_bio_visibility',
				'phone',
				'profile_phone_visibility',
				'discourse',
				'profile_discourse_visibility',
				'facebook',
				'profile_facebook_visibility',
				'twitter',
				'profile_twitter_visibility',
				'linkedin',
				'profile_linkedin_visibility',
				'github',
				'profile_github_visibility',
				'telegram',
				'profile_telegram_visibility',
				'mastodon',
				'profile_mastodon_visibility',
				'youtube',
				'profile_youtube_visibility',
				'pixelfed',
				'profile_pixelfed_visibility',
				'peertube',
				'profile_peertube_visibility',
				'matrix',
				'profile_matrix_visibility',
				'languages',
				'profile_languages_visibility',
				'tags',
				'profile_tags_visibility',
				'profile_groups_joined_visibility',
				'profile_events_attended_visibility',
				'profile_events_organized_visibility',
				'profile_campaigns_visibility',
				'profile_location_visibility',
			);

			// Add additional required fields after initial setup!
			if ( isset( $meta['agree'][0] ) && 'I Agree' === $meta['agree'][0] ) {
				unset( $required[8] );
				$required[]    = 'profile_location_visibility';
				$_POST['edit'] = true;
			}

			$error = false;
			foreach ( $required as $field ) {
				if ( isset( $_POST[ $field ] ) ) {
					if ( 'username' === $field ) {
						$current_required_field = sanitize_user( wp_unslash( $_POST[ $field ] ) );
					} elseif ( 'email' === $field ) {
						$current_required_field = sanitize_email( wp_unslash( $_POST[ $field ] ) );
					} else {
						$current_required_field = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
					}

					if ( '' === $current_required_field || 0 === $current_required_field ) {
						$error = true;
					}
				}
			}

			// Validate email and username!
			if ( false === $error ) {
				if ( isset( $_POST['email'] ) && isset( $_POST['username'] ) ) {
					$post_email = trim( sanitize_email( wp_unslash( $_POST['email'] ) ) );
					$post_user  = trim( sanitize_user( wp_unslash( $_POST['username'] ) ) );

					if ( ! filter_var( $post_email, FILTER_VALIDATE_EMAIL ) ) {
						$error                        = true;
						$_POST['email_error_message'] = __( 'Invalid email address', 'community-portal' );
					}

					$query = new WP_User_Query(
						array(
							'search'         => $post_email,
							'search_columns' => array(
								'user_email',
							),
							'exclude'        => array( $user->ID ),
						)
					);

					if ( 0 !== count( $query->get_results() ) ) {
						$error                        = true;
						$_POST['email_error_message'] = __( 'This email is already in use', 'community-portal' );
					}

					$query = new WP_User_Query(
						array(
							'search'         => $post_user,
							'search_columns' => array(
								'user_nicename',
							),
							'exclude'        => array( $user->ID ),
						)
					);

					// Validate email!

					if ( 0 !== count( $query->get_results() ) ) {
						$_POST['username_error_message'] = __( 'This username is already in use', 'community-portal' );
						$error                           = true;
					}
				}
			}

			// Create the user and save meta data!
			if ( false === $error ) {
				$_POST['complete'] = true;

				// Update regular WordPress user data!
				$data = array(
					'ID'         => $user->ID,
					'user_email' => trim( sanitize_email( wp_unslash( $_POST['email'] ) ) ),
				);

				// We need to udpate the user!
				$username_update = trim( sanitize_user( wp_unslash( $_POST['username'] ) ) );
				if ( $username_update !== $user->user_nicename ) {
					$data['user_nicename'] = $username_update;
					$user->user_nicename   = $data['user_nicename'];
				}

				wp_update_user( $data );

				// No longe need this key!
				unset( $required[0] );

				foreach ( $required as $field ) {
					if ( isset( $_POST[ $field ] ) ) {
						if ( 'username' === $field ) {
							$form_data = trim( sanitize_user( wp_unslash( $_POST[ $field ] ) ) );
						} else {
							$form_data = trim( sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
						}
						update_user_meta( $user->ID, $field, $form_data );
					}
				}

				// Update other fields here!
				$addtional_meta = array();

				foreach ( $additional_fields as $field ) {
					if ( isset( $_POST[ $field ] ) ) {
						if ( 'bio' === $field ) {
							$current_additional_field = trim( sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) );
						} else {
							if ( ! is_array( $_POST[ $field ] ) ) {
								if ( 'image_url' === $field ) {

									// Make sure the image is hosted with us!
									if ( ! empty( $_SERVER['SERVER_NAME'] ) ) {
										$url         = trim( esc_url_raw( wp_unslash( $_POST[ $field ] ) ) );
										$host_domain = esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) );
										$url_scheme  = wp_parse_url( $host_domain );

										if ( 'http' === $url_scheme['scheme'] && ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ) ) || ( ! empty( $_SERVER['SERVER_PORT'] ) && 443 === sanitize_key( wp_unslash( $_SERVER['SERVER_PORT'] ) ) ) ) {
											$host_domain = str_replace( 'http', 'https', $host_domain );
										}

										if ( false !== stripos( $url, $host_domain ) ) {
											$current_additional_field = $url;
										} else {
											$current_additional_field = false;
										}
									} else {
										$current_additional_field = false;
									}
								} else {
									$current_additional_field = trim( sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
								}
							} else {
								// If we get an array we need to sanitize each value individually!
								if ( ! empty( $_POST[ $field ] ) && is_array( $_POST[ $field ] ) ) {
									$values      = array();
									$field_count = count( $_POST[ $field ] );
									for ( $x = 0; $x < $field_count; $x++ ) {
										if ( ! empty( $_POST[ $field ][ $x ] ) ) {
											$values[] = sanitize_text_field( wp_unslash( $_POST[ $field ][ $x ] ) );
										}
									}
								}

								$current_additional_field = $values;
							}
						}

						$additional_meta[ $field ] = $current_additional_field;
					}
				}
				update_user_meta( $user->ID, 'community-meta-fields', $additional_meta );
				$current_translation = mozilla_get_current_translation();

				if ( $current_translation ) {
					wp_safe_redirect( "/{$current_translation}/people/{$user->user_nicename}" );
				} else {
					wp_safe_redirect( "/people/{$user->user_nicename}" );
				}
				exit();
			}
		}
	}
}

/**
 * Check if user is logged in
 **/
function mozilla_is_logged_in() {
	$current_user = wp_get_current_user()->data;
	return count( (array) $current_user ) > 0 ? true : false;
}

/**
 * Get user information as object
 *
 * @param string  $meta The meta key.
 * @param boolean $is_me It's me Mario.
 * @param boolean $logged_in are they logged in.
 * @param string  $prefix The prefix for the meta.
 **/
function mozilla_user_meta_as_object( $meta, $is_me, $logged_in, $prefix = '' ) {
	$object          = new stdClass();
	$object->value   = isset( $meta[ $meta ][0] ) ? $meta[ $meta ][0] : false;
	$object->display = mozilla_display_field( $meta, isset( $meta[ $prefix . $meta . '_visibility' ][0] ) ? $meta[ $prefix . $meta . '_visibility' ][0] : false, $is_me, $logged_in );

	return $object;
}

/**
 * Get user information
 *
 * @param object  $me current user.
 * @param object  $user user to view.
 * @param boolean $logged_in are they logged in.
 **/
function mozilla_get_user_info( $me, $user, $logged_in ) {
	// Username is ALWAYS public!
	$object          = new stdClass();
	$object->value   = $user->user_nicename;
	$object->display = true;

	$data = array(
		'username' => $object,
		'id'       => $user->ID,
	);

	$is_me            = $me && $logged_in && intval( $me->ID ) === intval( $user->ID );
	$meta             = get_user_meta( $user->ID );
	$community_fields = isset( $meta['community-meta-fields'][0] ) ? unserialize( $meta['community-meta-fields'][0] ) : array();

	// First Name!
	$data['first_name'] = mozilla_user_meta_as_object( 'first_name', $is_me, $logged_in );

	// Last Name!
	$data['last_name'] = mozilla_user_meta_as_object( 'last_name', $is_me, $logged_in );

	// Email!
	$data['email']   = mozilla_user_meta_as_object( 'email', $is_me, $logged_in );

	// Location!
	global $countries;
	$object = new stdClass();
	if ( isset( $community_fields['country'] ) && strlen( $community_fields['country'] ) > 0 && isset( $countries[ $community_fields['country'] ] ) ) {
		$object->value = $community_fields['country'];
	} else {
		$object->value = false;
	}
	$object->display  = mozilla_display_field( 'location', isset( $meta['profile_location_visibility'][0] ) ? $meta['profile_location_visibility'][0] : false, $is_me, $logged_in );
	$data['location'] = $object;

	// City!
	$object = new stdClass();
	if ( isset( $community_fields['city'] ) && strlen( $community_fields['city'] ) > 0 ) {
		$object->value = $community_fields['city'];
	} else {
		$object->value = false;
	}
	$object->display = mozilla_display_field( 'location', isset( $meta['profile_location_visibility'][0] ) ? $meta['profile_location_visibility'][0] : false, $is_me, $logged_in );
	$data['city']    = $object;

	// Profile Image!
	$data['profile_image']   = mozilla_user_meta_as_object( 'image_url', $is_me, $logged_in, 'profile' );

	// Bio!
	$data['bio']     = mozilla_user_meta_as_object( 'bio', $is_me, $logged_in, 'profile' );

	// Pronoun Visibility!
	$data['pronoun'] = mozilla_user_meta_as_object( 'pronoun', $is_me, $logged_in, 'profile' );

	// Phone!
	$data['phone']   = mozilla_user_meta_as_object( 'phone', $is_me, $logged_in );

	// Groups Joined!
	$data['groups']  = mozilla_user_meta_as_object( 'groups_joined', $is_me, $logged_in, 'profile' );

	// Events Attended!
	$data['events_attended'] = mozilla_user_meta_as_object( 'events_attended', $is_me, $logged_in, 'profile' );

	// Events Organized!
	$data['events_organized'] = mozilla_user_meta_as_object( 'events_organized', $is_me, $logged_in, 'profile' );

	// Campaigns!
	$object                         = new StdClass();
	$object->display                = mozilla_display_field( 'campaigns_participated', isset( $community_fields['profile_campaigns_visibility'] ) ? $community_fields['profile_campaigns_visibility'] : false, $is_me, $logged_in );
	$data['campaigns_participated'] = $object;

	// Social Media!
	$data['telegram']  = mozilla_user_meta_as_object( 'telegram', $is_me, $logged_in, 'profile' );
	$data['facebook']  = mozilla_user_meta_as_object( 'facebook', $is_me, $logged_in, 'profile' );
	$data['twitter']   = mozilla_user_meta_as_object( 'twitter', $is_me, $logged_in, 'profile' );
	$data['linkedin']  = mozilla_user_meta_as_object( 'linkedin', $is_me, $logged_in, 'profile' );
	$data['discourse'] = mozilla_user_meta_as_object( 'discourse', $is_me, $logged_in, 'profile' );
	$data['github']    = mozilla_user_meta_as_object( 'github', $is_me, $logged_in, 'profile' );
	$data['matrix']    = mozilla_user_meta_as_object( 'matrix', $is_me, $logged_in, 'profile' );
	$data['mastodon']  = mozilla_user_meta_as_object( 'mastodon', $is_me, $logged_in, 'profile' );
	$data['youtube']   = mozilla_user_meta_as_object( 'youtube', $is_me, $logged_in, 'profile' );
	$data['pixelfed']  = mozilla_user_meta_as_object( 'pixelfed', $is_me, $logged_in, 'profile' );
	$data['peertube']  = mozilla_user_meta_as_object( 'peertube', $is_me, $logged_in, 'profile' );

	// Languages!
	$data['languages'] = mozilla_user_meta_as_object( 'languages', $is_me, $logged_in, 'profile' );

	// Tags!
	$data['tags']      = mozilla_user_meta_as_object( 'tags', $is_me, $logged_in, 'profile' );

	$object = null;
	return $data;
}

/**
 * Check whether to display or not
 *
 * @param string  $field field to check.
 * @param string  $visibility visibility setting.
 * @param boolean $is_me are they viewing themselves.
 * @param boolean $logged_in are they logged in.
 **/
function mozilla_display_field( $field, $visibility, $is_me, $logged_in ) {

	if ( $is_me ) {
		return true;
	}

	if ( 'first_name' === $field && $logged_in ) {
		return true;
	}

	if ( intval( $visibility ) === PrivacySettings::PUBLIC_USERS ) {
		return true;
	}

	if ( false === $visibility ) {
		return false;
	}

	if ( $logged_in && intval( $visibility ) === PrivacySettings::REGISTERED_USERS ) {
		return true;
	}

	return false;
}

/**
 * Delete user
 **/
function mozilla_delete_user() {

	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		if ( is_user_logged_in() ) {
			$user                = wp_get_current_user()->data;
			$current_translation = mozilla_get_current_translation();

			if ( $user ) {
				$rand            = substr( md5( time() ), 0, 8 );
				$anonymous_email = "anonymous{$rand}@anonymous.com";
				$user_check      = get_user_by( 'email', $anonymous_email );

				while ( false !== $user_check ) {
					$rand            = substr( md5( time() ), 0, 8 );
					$anonymous_email = "anonymous{$rand}@anonymous.com";
					$user_check      = get_user_by( 'email', $anonymous_email );
				}

				$meta = get_user_meta( $user->ID );
				$args = array(
					'ID'            => $user->ID,
					'user_email'    => $anonymous_email,
					'display_name'  => 'Anonymous',
					'first_name'    => 'Anonymous',
					'last_name'     => 'Anonymous',
					'user_url'      => '',
					'user_nicename' => "Anonymous{$rand}",
					'user_login'    => "Anonymous{$rand}",
				);

				update_user_meta( $user->ID, 'nickname', 'Anonymous' );
				update_user_meta( $user->ID, 'first_name', 'Anonymous' );
				update_user_meta( $user->ID, 'last_name', 'Anonymous' );
				update_user_meta( $user->ID, 'email', $anonymous_email );

				wp_update_user( $args );
				delete_user_meta( $user->ID, 'community-meta-fields' );
				delete_user_meta( $user->ID, 'description', '' );
				delete_user_meta( $user->ID, 'wp_auth0_obj' );
				delete_user_meta( $user->ID, 'wp_auth0_id' );
				delete_user_meta( $user->ID, 'first_name_visibility' );
				delete_user_meta( $user->ID, 'last_name_visibility' );
				delete_user_meta( $user->ID, 'email_visibility' );

				wp_destroy_current_session();
				wp_clear_auth_cookie();
				wp_set_current_user( 0 );

				echo wp_json_encode(
					array(
						'translation' => $current_translation,
						'status'      => 'success',
						'msg'         => 'Account Deleted',
					)
				);
			} else {
				echo wp_json_encode(
					array(
						'translation' => $current_translation,
						'status'      => 'error',
						'msg'         => 'No user',
					)
				);
			}
		} else {
			echo wp_json_encode(
				array(
					'translation' => $current_translation,
					'status'      => 'error',
					'msg'         => 'Invalid Request',
				)
			);
		}
	}

	die();
}



