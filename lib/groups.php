<?php
/**
 * Group Library
 *
 * Group library functions
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

/**
 * Create group function
 **/
function mozilla_create_group() {
	if ( is_user_logged_in() ) {
		$required = array(
			'group_name',
			'group_type',
			'group_desc',
			'group_admin_id',
			'my_nonce_field',
		);

		$optional = array(
			'group_address_type',
			'group_address',
			'group_meeting_details',
			'group_discourse',
			'group_facebook',
			'group_telegram',
			'group_github',
			'group_twitter',
			'group_matrix',
			'group_other',
			'group_country',
			'group_city',
			'group_language',
		);

		// If we're posting data lets create a group!
		if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			if ( isset( $_POST['step'] ) && isset( $_REQUEST['group_create_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['group_create_field'] ) ), 'group_create' ) ) {
				$current_step = sanitize_text_field( wp_unslash( $_POST['step'] ) );

				switch ( $current_step ) {
					case '1':
						// Gather information!
						$error = false;
						foreach ( $required as $field ) {
							if ( isset( $_POST[ $field ] ) ) {
								if ( '' === $_POST[ $field ] || 0 === $_POST[ $field ] ) {
									$error = true;
								}
							}
						}

						$_SESSION['form'] = $_POST;

						// Cleanup!
						if ( $error ) {

							if ( isset( $_SESSION['uploaded_file'] ) ) {

								$uploaded_file = sanitize_file_name( wp_unslash( $_SESSION['uploaded_file'] ) );
								if ( file_exists( $uploaded_file ) ) {
									$image = getimagesize( $uploaded_file );
									if ( isset( $image[2] ) && in_array( $image[2], array( IMAGETYPE_JPEG, IMAGETYPE_PNG ), true ) ) {
										unlink( $uploaded_file );
									}
								}
							}

							$_POST['step'] = 0;
						}

						break;
					case 2:
						if ( isset( $_POST['agree'] ) ) {
							$agree = sanitize_text_field( wp_unslash( $_POST['agree'] ) );

							if ( strlen( $agree ) > 0 ) {
								$args = array(
									'group_id' => 0,
								);

								if ( ! empty( $_POST['group_name'] ) && ! empty( $_POST['group_desc'] ) ) {
									$group_name        = sanitize_text_field( wp_unslash( $_POST['group_name'] ) );
									$group_description = sanitize_textarea_field( wp_unslash( $_POST['group_desc'] ) );

									$args['name']        = $group_name;
									$args['description'] = $group_description;
									$args['status']      = 'private';

									$group_id = groups_create_group( $args );
									$meta     = array();

									if ( $group_id ) {
										// Loop through optional fields and save to meta!
										foreach ( $optional as $field ) {
											if ( isset( $_POST[ $field ] ) && '' !== $_POST[ $field ] ) {
												$meta[ $field ] = trim( sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
											}
										}

										$group         = groups_get_group( array( 'group_id' => $group_id ) );
										$group_creator = wp_get_current_user();

										$auth0_ids   = array();
										$auth0_ids[] = mozilla_get_user_auth0( $group_creator->ID );

										if ( isset( $_POST['group_admin_id'] ) && $group->creator_id === $group_creator->ID ) {

											$group_admin_user_id = intval( sanitize_text_field( wp_unslash( $_POST['group_admin_id'] ) ) );

											$user_check = get_userdata( $group_admin_user_id );

											if ( false !== $user_check ) {
												groups_join_group( $group_id, $group_admin_user_id );
												$member = new BP_Groups_Member( $group_admin_user_id, $group_id );
												do_action( 'groups_promote_member', $group_id, $group_admin_user_id, 'admin' );
												$member->promote( 'admin' );
												$auth0_ids[] = mozilla_get_user_auth0( $group_admin_user_id );
											}
										}

										if ( ! empty( $_POST['image_url'] ) && ! empty( $_POST['group_type'] ) && ! empty( $_SERVER['SERVER_NAME'] ) ) {
											$group_image = trim( esc_url_raw( wp_unslash( $_POST['image_url'] ) ) );
											$group_type  = trim( sanitize_text_field( wp_unslash( $_POST['group_type'] ) ) );
											$host_domain = esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) );

											$url_scheme = wp_parse_url( $host_domain );

											if ( 'http' === $url_scheme['scheme'] && ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ) ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === sanitize_key( wp_unslash( $_SERVER['SERVER_PORT'] ) ) ) {
												$host_domain = str_replace( 'http', 'https', $host_domain );
											}

											if ( false !== stripos( $group_image, $host_domain ) ) {
												// Required information but needs to be stored in meta data because buddypress does not support these fields.
												$meta['group_image_url'] = $group_image;
												$meta['group_type']      = $group_type;
											}
										}

										if ( isset( $_POST['tags'] ) ) {
											$tags_string = sanitize_text_field( wp_unslash( $_POST['tags'] ) );

											$tags               = explode( ',', $tags_string );
											$meta['group_tags'] = array_filter( $tags );
										}

										$discourse_data                = array();
										$discourse_data['name']        = $group->name;
										$discourse_data['description'] = $group->description;

										if ( ! empty( $auth0_ids ) ) {
											$discourse_data['users'] = $auth0_ids;
										}

										$discourse_group = mozilla_discourse_api( 'groups', $discourse_data, 'post' );

										if ( $discourse_group ) {
											$meta['discourse_group_id'] = intval( sanitize_text_field( $discourse_group->id ) );
										}

										// Don't need this data anymore!
										unset( $discourse_data['users'] );
										$discourse_data['groups'] = array( intval( $discourse_group->id ) );
										$discourse                = mozilla_discourse_api( 'categories', $discourse_data, 'post' );

										if ( $discourse && isset( $discourse->id ) && $discourse->id ) {
											$meta['discourse_category_id'] = intval( sanitize_text_field( $discourse->id ) );
										}

										$result = groups_update_groupmeta( $group_id, 'meta', $meta );

										if ( $result ) {
											unset( $_SESSION['form'] );
											$_POST         = array();
											$_POST['step'] = 3;

											$_POST['group_slug'] = $group->slug;
										} else {
											groups_delete_group( $group_id );
											mozilla_discourse_api( 'categories', array( 'category_id' => $discourse->id ), 'delete' );
											$_POST['step'] = 0;
										}
									}
								}
							}
						} else {
							$_POST['step'] = 2;
						}

						break;
				}
			}
		} else {
			unset( $_SESSION['form'] );
		}
	} else {
		wp_safe_redirect( '/' );
		exit();
	}
}

/**
 * Edit group function
 **/
function mozilla_edit_group() {

	$group_id = bp_get_current_group_id();
	$user     = wp_get_current_user();

	if ( $group_id && $user ) {

		$is_admin = groups_is_user_admin( $user->ID, $group_id );

		if ( false !== $is_admin ) {
			if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
				if ( isset( $_POST['update_group_nonce_field'] ) ) {
					$nonce = trim( sanitize_text_field( wp_unslash( $_POST['update_group_nonce_field'] ) ) );

					if ( wp_verify_nonce( $nonce, 'update_group' ) ) {

						$required = array(
							'group_name',
							'group_type',
							'group_desc',
							'group_address',
						);

						foreach ( $required as $field ) {
							if ( isset( $_POST[ $field ] ) ) {
								if ( '' === $_POST[ $field ] || 0 === $_POST[ $field ] ) {
									$error = true;
								}
							}
						}

						if ( isset( $_POST['group_name'] ) ) {
							$group_name = trim( sanitize_text_field( wp_unslash( $_POST['group_name'] ) ) );

							$error = mozilla_search_groups( $group_name, $group_id );
							if ( $error ) {
								$_POST['group_name_error'] = __( 'This group name is already taken', 'community-portal' );
							}
						} else {
							$error = true;
						}

						// Lets update!
						if ( false === $error ) {
							$group_name = trim( sanitize_text_field( wp_unslash( $_POST['group_name'] ) ) );
							$group_desc = isset( $_POST['group_desc'] ) ? trim( sanitize_textarea_field( wp_unslash( $_POST['group_desc'] ) ) ) : '';

							$args = array(
								'group_id'    => $group_id,
								'name'        => $group_name,
								'description' => $group_desc,
							);

							// Update the group!
							groups_create_group( $args );

							// Update both category and group!
							$discourse_api_data = array();
							$meta               = groups_get_groupmeta( $group_id, 'meta' );

							$group_discourse_info = mozilla_get_discourse_info( $group_id, 'group' );

							// Update Group Category on Discourse!
							$discourse_api_data['category_id'] = $group_discourse_info['discourse_category_id'];
							$discourse_api_data['name']        = $args['name'];
							$discourse_api_data['description'] = $args['description'];
							$discourse_api_data['groups']      = array( intval( $group_discourse_info['discourse_group_id'] ) );

							$discourse_category = mozilla_discourse_api( 'categories', $discourse_api_data, 'patch' );

							// Update Group Meta locally!
							$meta['discourse_category_id'] = $group_discourse_info['discourse_category_id'];
							if ( $discourse_category ) {
								$meta['discourse_category_url'] = $discourse_category->url;
							} else {
								$meta['discourse_category_url'] = $group_discourse_info['discourse_category_url'];
							}

							// Update Group on Discourse!
							$discourse_api_data                = array();
							$discourse_api_data['group_id']    = $group_discourse_info['discourse_group_id'];
							$discourse_api_data['name']        = $args['name'];
							$discourse_api_data['description'] = $args['description'];
							$discourse_api_data['users']       = $group_discourse_info['discourse_group_users'];

							$discourse_group            = mozilla_discourse_api( 'groups', $discourse_api_data, 'patch' );
							$meta['discourse_group_id'] = $group_discourse_info['discourse_group_id'];

							if ( $discourse_group ) {
								$meta['discourse_group_name'] = $discourse_group->discourse_group_name;
							} else {
								$meta['discourse_group_name'] = $group_discourse_info['discourse_group_name'];
							}

							// Update group meta data!
							if ( ! empty( $_SERVER['SERVER_NAME'] ) ) {
								$host_domain = esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) );

								$url_scheme = wp_parse_url( $host_domain );

								if ( 'http' === $url_scheme['scheme'] && ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ) ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === sanitize_key( wp_unslash( $_SERVER['SERVER_PORT'] ) ) ) {
									$host_domain = str_replace( 'http', 'https', $host_domain );
								}

								if ( ! empty( $_POST['image_url'] ) ) {
									if ( false !== stripos( esc_url_raw( wp_unslash( $_POST['image_url'] ) ), $host_domain ) ) {
										$meta['group_image_url'] = isset( $_POST['image_url'] ) ? esc_url_raw( wp_unslash( $_POST['image_url'] ) ) : '';
									}
								}
							}

							$meta['group_address_type']    = isset( $_POST['group_address_type'] ) ? sanitize_text_field( wp_unslash( $_POST['group_address_type'] ) ) : 'Address';
							$meta['group_address']         = isset( $_POST['group_address'] ) ? sanitize_text_field( wp_unslash( $_POST['group_address'] ) ) : '';
							$meta['group_meeting_details'] = isset( $_POST['group_meeting_details'] ) ? sanitize_text_field( wp_unslash( $_POST['group_meeting_details'] ) ) : '';
							$meta['group_city']            = isset( $_POST['group_city'] ) ? sanitize_text_field( wp_unslash( $_POST['group_city'] ) ) : '';
							$meta['group_country']         = isset( $_POST['group_country'] ) ? sanitize_text_field( wp_unslash( $_POST['group_country'] ) ) : '';
							$meta['group_type']            = isset( $_POST['group_type'] ) ? sanitize_text_field( wp_unslash( $_POST['group_type'] ) ) : 'Online';
							$meta['group_language']        = isset( $_POST['group_language'] ) ? sanitize_text_field( wp_unslash( $_POST['group_language'] ) ) : '';

							// Update tags.
							if ( isset( $_POST['tags'] ) ) {
								$tags_string = sanitize_text_field( wp_unslash( $_POST['tags'] ) );
								$tags        = array_filter( explode( ',', $tags_string ) );

								$meta['group_tags'] = $tags;
							}

							$meta['group_discourse'] = isset( $_POST['group_discourse'] ) ? sanitize_text_field( wp_unslash( $_POST['group_discourse'] ) ) : '';
							$meta['group_facebook']  = isset( $_POST['group_facebook'] ) ? sanitize_text_field( wp_unslash( $_POST['group_facebook'] ) ) : '';
							$meta['group_telegram']  = isset( $_POST['group_telegram'] ) ? sanitize_text_field( wp_unslash( $_POST['group_telegram'] ) ) : '';
							$meta['group_github']    = isset( $_POST['group_github'] ) ? sanitize_text_field( wp_unslash( $_POST['group_github'] ) ) : '';
							$meta['group_twitter']   = isset( $_POST['group_twitter'] ) ? sanitize_text_field( wp_unslash( $_POST['group_twitter'] ) ) : '';
							$meta['group_matrix']    = isset( $_POST['group_matrix'] ) ? sanitize_text_field( wp_unslash( $_POST['group_matrix'] ) ) : '';
							$meta['group_other']     = isset( $_POST['group_other'] ) ? sanitize_text_field( wp_unslash( $_POST['group_other'] ) ) : '';

							groups_update_groupmeta( $group_id, 'meta', $meta );

							global $bp;
							wp_safe_redirect( "/groups/{$bp->groups->current_group->slug}" );
							exit();
						}
					}
				}
			}
		}
	}
}

/**
 * Validates the group name
 */
function mozilla_validate_group_name() {

	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'GET' === $_SERVER['REQUEST_METHOD'] ) {
		if ( isset( $_GET['q'] ) ) {
			$query = sanitize_text_field( wp_unslash( $_GET['q'] ) );
			if ( isset( $_GET['gid'] ) ) {
				$gid = sanitize_text_field( wp_unslash( $_GET['gid'] ) );
				if ( 'false' !== $gid ) {
					intval( $gid );
				} else {
					$gid = false;
				}
			} else {
				$gid = false;
			}

			$found = mozilla_search_groups( $query, $gid );

			if ( false === $found ) {
				print wp_json_encode( true );
			} else {
				print wp_json_encode( false );
			}

			die();
		}
	}
}


/**
 * Search Groups
 *
 * @param string $name name of the group to search.
 * @param int    $gid groupd id not to search by.
 */
function mozilla_search_groups( $name, $gid ) {
	$groups      = groups_get_groups();
	$group_array = $groups['groups'];

	$found = false;
	foreach ( $group_array as $g ) {
		if ( intval( $gid ) && $gid === $g->id ) {
			continue;
		} else {
			$x = trim( strtolower( $g->name ) );
			$y = trim( strtolower( $name ) );
			if ( $x === $y ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Join a group
 */
function mozilla_join_group() {

	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		if ( ! empty( $_POST['join_group_nonce_field'] ) ) {
			$nonce = trim( sanitize_text_field( wp_unslash( $_POST['join_group_nonce_field'] ) ) );
			if ( wp_verify_nonce( $nonce, 'join_group_nonce' ) ) {

				$user = wp_get_current_user();

				if ( $user->ID ) {
					if ( isset( $_POST['group'] ) ) {
						$group_id = intval( sanitize_text_field( wp_unslash( $_POST['group'] ) ) );
						if ( $group_id ) {
							$invite_status = groups_get_groupmeta( $group_id, 'invite_status' );

							if ( 'members' === $invite_status || '' === $invite_status ) {
								$joined = groups_join_group( $group_id, $user->ID );

								if ( $joined ) {
									$discourse_group_info = mozilla_get_discourse_info( $group_id );
									$discourse_api_data   = array();
									$discourse_users      = array();

									$discourse_users[]               = mozilla_get_user_auth0( $user->ID );
									$discourse_api_data['group_id']  = $discourse_group_info['discourse_group_id'];
									$discourse_api_data['add_users'] = $discourse_users;

									$discourse = mozilla_discourse_api( 'groups/users', $discourse_api_data, 'patch' );

									print wp_json_encode(
										array(
											'status' => 'success',
											'msg'    => __(
												'Joined Group',
												'community-portal'
											),
										)
									);
								} else {
									print wp_json_encode(
										array(
											'status' => 'error',
											'msg'    => __(
												'Could not join group',
												'community-portal'
											),
										)
									);
								}
							}
						}

						exit();
					}
				} else {
					if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
						$cookie_ref = esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) );
						setcookie( 'mozilla-redirect', $cookie_ref, 0, '/' );
					}

					print wp_json_encode(
						array(
							'status' => 'error',
							'msg'    => 'Not Logged In',
						)
					);

					exit();
				}
			}
		}
	}

	print wp_json_encode(
		array(
			'status' => 'error',
			'msg'    => 'Invalid Request',
		)
	);

	exit();
}


/**
 * Leave a group
 */
function mozilla_leave_group() {
	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {

		if ( isset( $_POST['leave_group_nonce_field'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['leave_group_nonce_field'] ) );
			if ( wp_verify_nonce( $nonce, 'leave_group_nonce' ) ) {

				$user = wp_get_current_user();
				if ( $user->ID ) {
					if ( ! empty( $_POST['group'] ) ) {

						$group        = intval( trim( sanitize_text_field( wp_unslash( $_POST['group'] ) ) ) );
						$group_object = groups_get_group( array( 'group_id' => $group ) );

						if ( $group_object->creator_id !== $user->ID ) {
							$left = groups_leave_group( $group, $user->ID );

							if ( $left ) {
								$discourse_group_info = mozilla_get_discourse_info( $group );
								$discourse_api_data   = array();
								$discourse_users      = array();

								$discourse_users[]                  = mozilla_get_user_auth0( $user->ID );
								$discourse_api_data['group_id']     = $discourse_group_info['discourse_group_id'];
								$discourse_api_data['remove_users'] = $discourse_users;
								$discourse                          = mozilla_discourse_api( 'groups/users', $discourse_api_data, 'patch' );

								print wp_json_encode(
									array(
										'status' => 'success',
										'msg'    => 'Left Group',
									)
								);
							} else {
								print wp_json_encode(
									array(
										'status' => 'error',
										'msg'    => 'Could not leave group',
									)
								);
							}
						} else {
							print wp_json_encode(
								array(
									'status' => 'error',
									'msg'    => 'Admin cannot leave a group',
								)
							);
						}
						exit();
					}
				} else {
					print wp_json_encode(
						array(
							'status' => 'error',
							'msg'    => 'Not Logged In',
						)
					);
					exit();
				}
			}
		}
	}

	print wp_json_encode(
		array(
			'status' => 'error',
			'msg'    => 'Invalid Request',
		)
	);

	exit();
}

/**
 * Loads group into  acf
 *
 * @param string $field field name.
 */
function acf_load_bp_groups( $field ) {
	$all_groups = groups_get_groups( array( 'per_page' => -1 ) );

	foreach ( $all_groups['groups'] as $group ) :
		$groups[] = $group->name . '_' . $group->id;
	endforeach;

	// Populate choices.
	foreach ( $groups as $group ) {
		$groupvalues                         = explode( '_', $group );
		$field['choices'][ $groupvalues[1] ] = $groupvalues[0];
	}

	// Return choices.
	return $field;
}

/**
 * Add members to discourse
 *
 * @param integer $group_id the group id.
 * @param integer $user_id the user id of the person to add.
 */
function mozilla_add_members_discourse( $group_id, $user_id ) {

	$discourse_group_info = mozilla_get_discourse_info( $group_id );
	$discourse_api_data   = array();
	$discourse_users      = array();

	$discourse_users[]               = mozilla_get_user_auth0( $user_id );
	$discourse_api_data['group_id']  = $discourse_group_info['discourse_group_id'];
	$discourse_api_data['add_users'] = $discourse_users;

	$discourse = mozilla_discourse_api( 'groups/users', $discourse_api_data, 'patch' );
	return true;
}

/**
 * Remove members to discourse
 *
 * @param integer $group_id the group id.
 * @param integer $user_id the user id of the person to remove.
 */
function mozilla_remove_members_discourse( $group_id, $user_id ) {
	$discourse_group_info = mozilla_get_discourse_info( $group_id );
	$discourse_api_data   = array();
	$discourse_users      = array();

	$discourse_users[] = mozilla_get_user_auth0( $user_id );

	$discourse_api_data['group_id']     = $discourse_group_info['discourse_group_id'];
	$discourse_api_data['remove_users'] = $discourse_users;
	$discourse                          = mozilla_discourse_api( 'groups/users', $discourse_api_data, 'patch' );

	return true;
}

/**
 * Update meta data after save hook
 *
 * @param integer $group_id the group id.
 */
function mozilla_save_group( $group_id ) {
	if ( ! is_admin() && false === in_array( 'administrator', wp_get_current_user()->roles, true ) ) {
		return;
	}

	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		if ( ! empty( $_POST['verify_group_nonce_field'] ) ) {
			$nonce = trim( sanitize_text_field( wp_unslash( $_POST['verify_group_nonce_field'] ) ) );
			if ( wp_verify_nonce( $nonce, 'verify_group_nonce' ) ) {

				$group      = groups_get_group( array( 'group_id' => $group_id ) );
				$group_meta = groups_get_groupmeta( $group_id, 'meta' );

				// If verifying group store when we did it.
				if ( ! isset( $group_meta['verified_date'] ) && isset( $_POST['group-status'] ) ) {
					$status = trim( sanitize_text_field( wp_unslash( $_POST['group-status'] ) ) );
					if ( 'public' === $status ) {
						$group_meta['verified_date'] = time();
						groups_update_groupmeta( $group_id, 'meta', $group_meta );
					}
				}

				// If unverifying a group unset the value.
				if ( isset( $group_meta['verified_date'] ) && isset( $_POST['group-status'] ) ) {
					$status = trim( sanitize_text_field( wp_unslash( $_POST['group-status'] ) ) );

					if ( 'public' !== $status ) {
						unset( $group_meta['verified_date'] );
						groups_update_groupmeta( $group_id, 'meta', $group_meta );
					}
				}
			}
		}
	}

}

/**
 * Addmeta box to group admin page
 */
function mozilla_group_metabox() {
	add_meta_box( 'mozilla-group-metabox', __( 'Export Events', 'community-portal' ), 'mozilla_group_markup_metabox', get_current_screen()->id, 'side', 'core' );
}

/**
 * Markup for group metabox
 *
 * @param object $post Post object.
 */
function mozilla_group_markup_metabox( $post ) {
	wp_nonce_field( 'verify_group_nonce', 'verify_group_nonce_field' );
	$id = esc_attr( $post->id );

	echo wp_kses(
		"<div><a href=\"/wp-admin/admin-ajax.php?action=download_group_events&group={$id}\">Export Events</a></div>",
		array(
			'a'   => array( 'href' => array() ),
			'div' => array(),
		)
	);
}

/**
 * Export events
 */
function mozilla_download_group_events() {
	if ( ! is_admin() && in_array( 'administrator', wp_get_current_user()->roles, true ) === false ) {
		return;
	}

	if ( isset( $_GET['group'] ) ) {
		$group_id = intval( sanitize_text_field( wp_unslash( $_GET['group'] ) ) );
		$group    = groups_get_group( array( 'group_id' => $group_id ) );

		$args          = array();
		$args['scope'] = 'all';
		$args['limit'] = '0';

		$events         = EM_Events::get( $args );
		$related_events = array();

		foreach ( $events as $event ) {

			if ( strlen( $event->group_id ) > 0 && intval( $event->group_id ) === intval( $group->id ) ) {
				$related_events[] = $event;
			}
		}

		$theme_directory = get_template_directory();
		include "{$theme_directory}/languages.php";
		$countries = em_get_countries();

		header( 'Content-Type: text/csv' );
		header( "Content-Disposition: attachment;filename=group-{$group_id}-events.csv" );
		$out = fopen( 'php://output', 'w' );

		$heading = array( 'ID', 'Event Title', 'Event Start Date', 'Event End Date', 'Description', 'Goals', 'Attendee Count', 'Expected Attendee Count', 'Language', 'Location', 'Tags', 'Hosted By', 'User ID', 'Group', 'Group ID' );
		fputcsv( $out, $heading );

		foreach ( $related_events as $related_event ) {
			$attendees       = count( $related_event->get_bookings()->bookings );
			$event_meta      = get_post_meta( $related_event->post_id, 'event-meta' );
			$language        = isset( $event_meta[0]->language ) && strlen( $event_meta[0]->language ) > 0 ? $languages[ $event_meta[0]->language ] : 'N/A';
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
			$tags    = rtrim( $tags, ', ' );
			$address = '';

			if ( 'OE' === strtoupper( $location_object->country ) ) {
				$address = $location_object->location_name;
			} else {

				if ( $location_object->location_name ) {
					$address = $location_object->location_name;
				}

				if ( $location_object->address ) {
					$address = $address . ', ' . $location_object->address;
				}

				if ( $location_object->city ) {
					$address = $address . ', ' . $location_object->city;
				}

				if ( $location_object->town ) {
					$address = $address . ', ' . $location_object->town;
				}

				if ( $location_object->country ) {
					$address = $address . ', ' . $countries[ $location_object->country ];
				}
			}

			$location     = $address;
			$group_object = new BP_Groups_Group( $related_event->group_id );
			$group        = ( $group_object->id ) ? $group_object->name : 'N/A';

			$row = array(
				$related_event->event_id,
				$related_event->name,
				$related_event->event_start_date,
				$related_event->event_end_date,
				$related_event->post_content,
				isset( $event_meta[0]->goal ) ? $event_meta[0]->goal : 'N/A',
				$attendees,
				isset( $event_meta[0]->projected_attendees ) ? $event_meta[0]->projected_attendees : 'N/A',
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


