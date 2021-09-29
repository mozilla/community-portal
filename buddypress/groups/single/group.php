<?php
/**
 * Group public profile
 *
 * Group public profile page
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

	get_header();
	// Lets get the group data!
	do_action( 'bp_before_directory_groups_page' );
	global $bp;

	$logged_in           = mozilla_is_logged_in();
	$group_user          = wp_get_current_user()->data;
	$current_translation = mozilla_get_current_translation();

	$template_dir = get_template_directory();
	require "{$template_dir}/languages.php";
	require "{$template_dir}/countries.php";

	$group           = $bp->groups->current_group;
	$group_meta      = groups_get_groupmeta( $group->id, 'meta' );
	$invite_status   = groups_get_groupmeta( $group->id, 'invite_status' );
	$member_count    = groups_get_total_member_count( $group->id );
	$group_user      = wp_get_current_user();
	$is_member       = groups_is_user_member( $group_user->ID, $group->id );
	$admins          = groups_get_group_admins( $group->id );
	$discourse_group = mozilla_get_discourse_info( $group->id );

	$admin_count = count( $admins );
	$logged_in   = mozilla_is_logged_in();

	$args = array(
		'group_id' => $group->id,
	);

	$tags          = get_tags( array( 'hide_empty' => false ) );
	$group_members = groups_get_group_members( $args );

	$is_admin   = groups_is_user_admin( $group_user->ID, $group->id );
	$group_user = wp_get_current_user()->data;
	switch ( $group->status ) {
		case 'public':
			$verified = true;
			break;
		case 'private':
			$verified = false;
			break;
		default:
			$verified = false;
	}

	if ( isset( $_GET['u'] ) ) {
		$search_user = trim( sanitize_text_field( wp_unslash( $_GET['u'] ) ) );
		if ( strlen( $search_user ) > 0 ) {
			$search_user = htmlspecialchars( $search_user, ENT_QUOTES, 'UTF-8' );
		} else {
			$search_user = false;
		}
	} else {
		$search_user = false;
	}

	$location     = isset( $_GET['country'] ) ? htmlspecialchars( sanitize_text_field( wp_unslash( $_GET['country'] ) ), ENT_QUOTES, 'UTF-8' ) : '';
	$get_language = isset( $_GET['language'] ) ? htmlspecialchars( sanitize_text_field( wp_unslash( $_GET['language'] ) ), ENT_QUOTES, 'UTF-8' ) : '';
	$get_tag      = isset( $_GET['tag'] ) ? htmlspecialchars( sanitize_text_field( wp_unslash( $_GET['tag'] ) ), ENT_QUOTES, 'UTF-8' ) : '';

	if (
		isset( $search_user ) &&
		( strpos( $search_user, '"' ) !== false ||
		strpos( $search_user, "'" ) !== false ||
		strpos( $search_user, '\\' ) !== false )
	) {
		$search_user = str_replace( '\\', '', $search_user );
		$search_user = preg_replace( '/^\"|\"$|^\'|\'$/', '', $search_user );
	}

	$first_name = false;
	$last_name  = false;

	// We aren't searching a username rather a full name!
	if ( $search_user && strpos( $search_user, ' ' ) !== false ) {
		$name = explode( ' ', $search_user );
		if ( is_array( $name ) && 2 === count( $name ) ) {
			$first_name = $name[0];
			$last_name  = $name[1];
		}
	}

	$country_code  = strlen( trim( $location ) ) > 0 ? strtoupper( $location ) : false;
	$get_tag       = strlen( trim( $get_tag ) ) > 0 ? strtolower( $get_tag ) : false;
	$language_code = strlen( trim( $get_language ) ) > 0 ? strtolower( $get_language ) : false;

	$wp_user_query = new WP_User_Query(
		array(
			'offset' => 0,
			'number' => -1,
		)
	);

	$members      = $wp_user_query->get_results();
	$real_members = array();
	foreach ( $group_members['members'] as $gp ) {
		$user_lookup = $gp;
		foreach ( $members as $mb ) {
			if ( $user_lookup->ID === $mb->ID ) {
				$real_members[] = $mb;
			}
		}
	}

	$filtered_members  = array();
	$used_country_list = array();
	$used_languages    = array();

	$live_user = ! empty( $live_user ) ? $live_user : false;

	// Time to filter stuff!
	foreach ( $real_members as $index => $member ) {
		$info           = mozilla_get_user_info( $live_user, $member, $logged_in );
		$member->info   = $info;
		$member_tags    = array_filter( explode( ',', $info['tags']->value ) );
		$member_country = false;

		if ( $info['location']->display ) {
			if ( strpos( $info['location']->value, ',' ) !== false ) {
				$member_country = explode( ',', $info['location']->value );
				foreach ( $member_country as $i => $part ) {
					$member_country[ $i ] = trim( $part );
				}

				if ( 2 === count( $member_country ) ) {
					$member_country = $member_country[1];
				} else {
					$member_country = $info['location']->value;
				}
			} else {
				$member_country = $info['location']->value;
			}
			if ( isset( $countries[ $member_country ] ) ) {
				$used_country_list[ $member_country ] = $countries[ $member_country ];
			}
		}


		if ( isset( $info['languages'] ) && $info['languages']->display && is_array( $info['languages']->value ) ) {
			foreach ( $info['languages']->value as $l ) {
				$used_languages[ $l ] = $languages[ $l ];
			}
		}

		$used_languages = array_unique( $used_languages );
		asort( $used_languages );

		// All four criteria to search!
		if ( $country_code && $get_tag && $search_user && $language_code ) {
			// Country / Tag / Username / Language!
			if ( $info['tags']->display &&
				$info['location']->display &&
				array_key_exists( $country_code, $countries ) &&
				strtolower( $country_code ) === strtolower( $member_country ) &&
				in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
				stripos( $member->data->user_nicename, $search_user ) !== false &&
				$info['languages']->display &&
				is_array( $info['languages']->value ) &&
				in_array( $language_code, $info['languages']->value, true )
				) {
					$filtered_members[] = $member;
					continue;
			}

			// Country / Tag / First Name / Language!
			if ( $first_name ) {
				if ( $info['tags']->display &&
					$info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $first_name ) !== false &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true )
					) {
						$filtered_members[] = $member;
						continue;
				}
			} else {
				if ( $info['tags']->display &&
					$info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $search_user ) !== false &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true )
					) {
						$filtered_members[] = $member;
						continue;
				}
			}

			// Country / Tag / Last Name / Language!
			if ( $last_name ) {
				if ( $info['tags']->display && $info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $last_name ) !== false &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true )
					) {
						$filtered_members[] = $member;
						continue;
				}
			} else {
				if ( $info['tags']->display && $info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $search_user ) !== false &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true )
					) {
						$filtered_members[] = $member;
						continue;
				}
			}

			continue;
		}

		// Language / tag / search!
		if ( false === $country_code && $get_tag && $search_user && $language_code ) {

			// Language / Tag / Username!
			if ( $info['tags']->display &&
				$info['languages']->display &&
				is_array( $info['languages']->value ) &&
				in_array( $language_code, $info['languages']->value, true ) &&
				in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
				stripos( $member->data->user_nicename, $search_user ) !== false ) {
					$filtered_members[] = $member;
					continue;
			}

			// Language / Tag / First Name!
			if ( $first_name ) {
				if ( $info['tags']->display &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $first_name ) !== false ) {
						$filtered_members[] = $member;
						continue;
				}
			} else {
				if ( $info['tags']->display &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $search_user ) !== false ) {
						$filtered_members[] = $member;
						continue;
				}
			}

			// Language / Tag / Last Name!
			if ( $last_name ) {
				if ( $info['tags']->display &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $last_name ) !== false ) {
						$filtered_members[] = $member;
						continue;
				}
			} else {
				if ( $info['tags']->display &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $search_user ) !== false ) {
						$filtered_members[] = $member;
						continue;
				}
			}

			continue;
		}

		// Country / tag / search!
		if ( $country_code && $get_tag && $search_user && false === $language_code ) {
			// Country / Tag / Username!
			if ( $info['tags']->display &&
				$info['location']->display &&
				array_key_exists( $country_code, $countries ) &&
				strtolower( $country_code ) === strtolower( $member_country ) &&
				in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
				stripos( $member->data->user_nicename, $search_user ) !== false ) {
					$filtered_members[] = $member;
					continue;
			}

			// Country / Tag / First Name!
			if ( $first_name ) {
				if ( $info['tags']->display &&
					$info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $first_name ) !== false ) {
						$filtered_members[] = $member;
						continue;
				}
			} else {
				if ( $info['tags']->display &&
					$info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $search_user ) !== false ) {
						$filtered_members[] = $member;
						continue;
				}
			}

			// Country / Tag / Last Name!
			if ( $last_name ) {
				if ( $info['tags']->display && $info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $last_name ) !== false ) {
						$filtered_members[] = $member;
						continue;
				}
			} else {
				if ( $info['tags']->display && $info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $search_user ) !== false ) {
						$filtered_members[] = $member;
						continue;
				}
			}

			continue;
		}


		// Location / language / tag!
		if ( false === $search_user && $country_code && $language_code && $get_tag ) {

			if ( $info['languages']->display &&
				$info['tags']->display &&
				in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
				is_array( $info['languages']->value ) &&
				in_array( $language_code, $info['languages']->value, true ) &&
				$info['location']->display &&
				array_key_exists( $country_code, $countries ) &&
				strtolower( $country_code ) === strtolower( $member_country ) ) {
					$filtered_members[] = $member;
					continue;
			}


			continue;
		}


		// Search / location / language!
		if ( $search_user && false === $get_tag && $country_code && $language_code ) {

			if ( $info['languages']->display &&
				$info['location']->display &&
				array_key_exists( $country_code, $countries ) &&
				strtolower( $country_code ) === strtolower( $member_country ) &&
				is_array( $info['languages']->value ) &&
				in_array( $language_code, $info['languages']->value, true ) &&
				false !== stripos( $member->data->user_nicename, $search_user ) ) {
					$filtered_members[] = $member;
					continue;
			}

			// Country / First Name / Language!
			if ( $first_name ) {
				if ( $info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $first_name ) !== false &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true )
					) {
						$filtered_members[] = $member;
						continue;
				}
			} else {
				if ( $info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $search_user ) !== false &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true )
					) {
						$filtered_members[] = $member;
						continue;
				}
			}

			// Country / Tag / Last Name / Language!
			if ( $last_name ) {
				if ( $info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $last_name ) !== false &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true )
					) {
						$filtered_members[] = $member;
						continue;
				}
			} else {
				if ( $info['location']->display &&
					array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $search_user ) !== false &&
					$info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true )
					) {
						$filtered_members[] = $member;
						continue;
				}
			}

			continue;
		}

		// Country and search!
		if ( $country_code && $search_user && false === $get_tag && false === $language_code ) {
			$country_code = strtoupper( $location );

			// Country and username!
			if ( array_key_exists( $country_code, $countries ) &&
				strtolower( $country_code ) === strtolower( $member_country ) &&
				$info['location']->display &&
				stripos( $member->data->user_nicename, $search_user ) !== false ) {
				$filtered_members[] = $member;
				continue;
			}


			// Country and first name!
			if ( $first_name ) {
				if ( array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					$info['location']->display &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $first_name ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			} else {
				if ( array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					$info['location']->display &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $search_user ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			}

			// Country and last name!
			if ( $last_name ) {
				if ( array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					$info['location']->display &&
					$info['first_name']->display &&
					stripos( $info['last_name']->value, $last_name ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			} else {
				if ( array_key_exists( $country_code, $countries ) &&
					strtolower( $country_code ) === strtolower( $member_country ) &&
					$info['location']->display &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $search_user ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			}

			continue;
		}


		// Tag and search!
		if ( $get_tag && $search_user && false === $country_code && false === $language_code ) {
			// Tag and username!
			if ( in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
				$info['tags']->display &&
				stripos( $member->data->user_nicename, $search_user ) !== false ) {
				$filtered_members[] = $member;
				continue;
			}

			// Tag and first name!
			if ( $first_name ) {
				if ( in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['tags']->display &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $first_name ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			} else {
				if ( in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['tags']->display &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $search_user ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			}

			// Tag and first name!
			if ( $last_name ) {
				if ( in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['tags']->display &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $last_name ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			} else {
				if ( in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
					$info['tags']->display &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $search_user ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			}

			continue;
		}


		// Language and search!
		if ( false === $get_tag && $search_user && false === $country_code && $language_code ) {
			// Language and username!
			if ( $info['languages']->display &&
				is_array( $info['languages']->value ) &&
				in_array( $language_code, $info['languages']->value, true ) &&
				stripos( $member->data->user_nicename, $search_user ) !== false ) {
				$filtered_members[] = $member;
				continue;
			}

			// Language and first name!
			if ( $first_name ) {
				if ( $info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true ) &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $first_name ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			} else {
				if ( $info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true ) &&
					$info['first_name']->display &&
					stripos( $info['first_name']->value, $search_user ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			}

			// Language and last name!
			if ( $last_name ) {
				if ( $info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true ) &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $last_name ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			} else {
				if ( $info['languages']->display &&
					is_array( $info['languages']->value ) &&
					in_array( $language_code, $info['languages']->value, true ) &&
					$info['last_name']->display &&
					stripos( $info['last_name']->value, $search_user ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			}
			continue;
		}


		// Language and tag!
		if ( false === $country_code && $get_tag && false === $search_user && $language_code ) {
			if ( $info['languages']->display &&
				$info['tags']->display &&
				in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
				is_array( $info['languages']->value ) &&
				in_array( $language_code, $info['languages']->value, true ) ) {
					$filtered_members[] = $member;
					continue;
			}

			continue;
		}


		// Country and language!
		if ( $country_code && false === $get_tag && false === $search_user && $language_code ) {
			if ( $info['location']->display &&
				array_key_exists( $country_code, $countries ) &&
				strtolower( $country_code ) === strtolower( $member_country ) &&
				$info['languages']->display &&
				is_array( $info['languages']->value ) &&
				in_array( $language_code, $info['languages']->value, true ) ) {
					$filtered_members[] = $member;
					continue;
			}
			continue;
		}

		// Country and tag!
		if ( $country_code && $get_tag && false === $search_user && false === $language_code ) {

			if ( $info['tags']->display &&
				$info['location']->display &&
				array_key_exists( $country_code, $countries ) &&
				strtolower( $country_code ) === strtolower( $member_country ) &&
				in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) ) {
					$filtered_members[] = $member;
					continue;
			}

			continue;
		}


		// Just Country!
		if ( $country_code && false === $get_tag && false === $search_user && false === $language_code ) {

			if ( $info['location']->display &&
				array_key_exists( $country_code, $countries ) &&
				strtolower( $country_code ) === strtolower( $member_country ) ) {
					$filtered_members[] = $member;
					continue;
			}

			continue;
		}

		// Just Tags!
		if ( $get_tag && false === $country_code && false === $search_user && false === $language_code ) {
			if ( $info['tags']->display &&
				in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) ) {
					$filtered_members[] = $member;
					continue;
			}

			continue;
		}

		// Just language!
		if ( $language_code && false === $get_tag && false === $country_code && false === $search_user ) {
			if ( $info['languages']->display &&
				is_array( $info['languages']->value ) &&
				in_array( $language_code, $info['languages']->value, true )
			) {
				$filtered_members[] = $member;
				continue;
			}

			continue;
		}

		// Just search!
		if ( $search_user && false === $country_code && false === $get_tag && false === $language_code ) {
			// Username!
			if ( stripos( $member->data->user_nicename, $search_user ) !== false ) {
				$filtered_members[] = $member;
				continue;
			}

			// First name!
			if ( $first_name ) {
				if ( $info['first_name']->display && stripos( $info['first_name']->value, $first_name ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			} else {
				if ( $info['first_name']->display && stripos( $info['first_name']->value, $search_user ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			}

			// Last name!
			if ( $last_name ) {
				if ( $info['last_name']->display && stripos( $info['last_name']->value, $last_name ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			} else {
				if ( $info['last_name']->display && stripos( $info['last_name']->value, $search_user ) !== false ) {
					$filtered_members[] = $member;
					continue;
				}
			}

			continue;
		}
		$filtered_members[] = $member;
	}

	$count = count( $filtered_members );
	?>
	<div class="content">
		<div class="group">
			<div class="group__container">
				<h1 class="group__title"><?php echo esc_html( str_replace( '\\', '', wp_unslash( $group->name ) ) ); ?></h1>
				<?php wp_nonce_field( 'join_group_nonce', 'join_group_nonce_field' ); ?>
				<?php wp_nonce_field( 'leave_group_nonce', 'leave_group_nonce_field' ); ?>
				<div class="group__details">
					<?php if ( $verified ) : ?>
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<ellipse cx="8" cy="7.97569" rx="8" ry="7.97569" fill="#0060DF"/>
							<path d="M8 5.5L8.7725 7.065L10.5 7.3175L9.25 8.535L9.545 10.255L8 9.4425L6.455 10.255L6.75 8.535L5.5 7.3175L7.2275 7.065L8 5.5Z" fill="white" stroke="white" stroke-width="2" stroke-linecap="round"/>
						</svg>
						<a href="https://discourse.mozilla.org/t/frequently-asked-questions-portal-edition-faq/43224" class="group__status"><?php esc_html_e( 'Verified', 'community-portal' ); ?></a>&nbsp;|
					<?php else : ?>
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M15.5 7.97569C15.5 12.103 12.1436 15.4514 8 15.4514C3.85643 15.4514 0.5 12.103 0.5 7.97569C0.5 3.84842 3.85643 0.5 8 0.5C12.1436 0.5 15.5 3.84842 15.5 7.97569Z" stroke="#B1B1BC"/>
							<path d="M8 5.5L8.7725 7.065L10.5 7.3175L9.25 8.535L9.545 10.255L8 9.4425L6.455 10.255L6.75 8.535L5.5 7.3175L7.2275 7.065L8 5.5Z" fill="#B1B1BC" stroke="#B1B1BC" stroke-width="2" stroke-linecap="round"/>
						</svg>
						<a href="https://discourse.mozilla.org/t/frequently-asked-questions-portal-edition-faq/43224" class="group__status"><?php esc_html_e( 'Unverified', 'community-portal' ); ?></a>&nbsp;|
					<?php endif; ?>
					<span class="group__location">
					<?php
					if ( isset( $group_meta['group_city'] ) && strlen( $group_meta['group_city'] ) > 0 ) {
						if ( isset( $group_meta['group_country'] ) && strlen( $group_meta['group_country'] ) > 1 ) {
							$location_code = $group_meta['group_country'];
							?>
							<a href="
							<?php
							if ( $current_translation ) :
								?>
								<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?><?php echo '/groups/?country=' . esc_attr( $location_code ); ?>" class="group__status">
							<?php
						}

						if ( strlen( $group_meta['group_city'] ) > 180 ) {
							$group_meta['group_city'] = substr( $group_meta['group_city'], 0, 180 );
						}

						echo esc_html( "{$group_meta['group_city']}" );
						if ( isset( $group_meta['group_country'] ) && strlen( $group_meta['group_country'] ) > 1 ) {
							$country = $countries[ $group_meta['group_country'] ];
							echo ', ' . esc_html( $country ) . '</a> | ';
						} else {
							echo '|';
						}
					} else {
						if ( isset( $group_meta['group_country'] ) && strlen( $group_meta['group_country'] ) > 1 ) {
							$country       = $countries[ $group_meta['group_country'] ];
							$location_code = $group_meta['group_country'];
							?>
							<a href="
							<?php
							if ( $current_translation ) :
								?>
								<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?><?php echo '/groups/?country=' . esc_attr( $location_code ); ?>" class="group__status"><?php echo esc_html( $country ); ?></a> |
							<?php
						}
					}
					?>
					</span>
					<span class="group__created">
					<?php
						$date_format  = 'en' === $current_translation ? 'F d, Y' : 'd F, Y';
						$created      = mozilla_localize_date( $group->date_created, $date_format );
						$created_word = __( 'Created', 'community-portal' );
						echo '<span> ' . esc_html( $created_word ) . ' ' . esc_html( $created );
					?>
					</span>
				</div>
				<div class="group__nav">
					<ul class="group__menu">
						<li class="menu-item"><a class="group__menu-link
						<?php
						if ( bp_is_group_home() && ! $is_events && ! $is_people ) :
							?>
							group__menu-link--active<?php endif; ?>" href="
								<?php
								if ( $current_translation ) :
									?>
									<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/<?php echo esc_attr( $group->slug ); ?>"><?php esc_html_e( 'About us', 'community-portal' ); ?></a></li>
						<li class="menu-item"><a class="group__menu-link
						<?php
						if ( $is_events ) :
							?>
							group__menu-link--active<?php endif; ?>" href="
								<?php
								if ( $current_translation ) :
									?>
									<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/<?php echo esc_attr( $group->slug ); ?>?view=events"><?php esc_html_e( 'Our Events', 'community-portal' ); ?></a></li>
						<li class="menu-item"><a class="group__menu-link
						<?php
						if ( $is_people ) :
							?>
							group__menu-link--active<?php endif; ?>" href="
								<?php
								if ( $current_translation ) :
									?>
									<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/<?php echo esc_attr( $group->slug ); ?>/?view=people"><?php esc_html_e( 'Our Members', 'community-portal' ); ?></a></li>
						<li class="menu-item"><a class="group__menu-link" href="
									<?php echo esc_url_raw( '/events.ics?group=' . esc_attr( $group->slug ) ); ?>"><?php esc_html_e( 'ICS Feed', 'community-portal' ); ?></a></li>
					</ul>
				</div>
				<div class="group__nav group__nav--mobile">
					<label class="group__nav-select-label"><?php esc_html_e( 'Showing', 'community-portal' ); ?></label>
					<div class="select-container">
						<select class="group__nav-select">
							<option value="/groups/<?php echo esc_attr( $group->slug ); ?>"
															<?php
															if ( bp_is_group_home() && ! $is_events && ! $is_people ) :
																?>
								selected<?php endif; ?>><?php esc_html_e( 'About us', 'community-portal' ); ?></option>
							<option value="/groups/<?php echo esc_attr( $group->slug ); ?>?view=events"
															<?php
															if ( $is_events ) :
																?>
								selected<?php endif; ?>><?php esc_html_e( 'Our Events', 'community-portal' ); ?></option>
							<option value="/groups/<?php echo esc_attr( $group->slug ); ?>?view=people"
															<?php
															if ( $is_people ) :
																?>
								selected<?php endif; ?>><?php esc_html_e( 'Our Members', 'community-portal' ); ?></option>
						</select>
					</div>
				</div>
				<section class="group__info">
					<?php if ( $is_people ) : ?>
					<div class="group__members-container">
						<h2 class="group__card-title"><?php esc_html_e( 'Group Contacts', 'community-portal' ) . esc_html( " ({$admin_count})" ); ?></h2>
						<div class="group__members">
							<?php foreach ( $admins as $admin ) : ?>
								<?php
								$a     = get_user_by( 'ID', $admin->user_id );
								$is_me = $logged_in && intval( $group_user->ID ) === intval( $admin->user_id );
								$info  = mozilla_get_user_info( $group_user, $a, $logged_in );

								if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
									$avatar_url = preg_replace( '/^http:/i', 'https:', $info['profile_image']->value );
								} else {
									$avatar_url = $info['profile_image']->value;
								}

								?>
							<a href="
								<?php
								if ( $current_translation ) :
									?>
									<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/<?php echo esc_attr( $a->user_nicename ); ?>" class="members__member-card">
								<div class="members__avatar
								<?php
								if ( false === $info['profile_image']->display || false === $info['profile_image']->value ) :
									?>
									members__avatar--identicon<?php endif; ?>"
									<?php
									if ( $info['profile_image']->display && $info['profile_image']->value ) :
										?>
									style="background-image: url('<?php echo esc_url_raw( $avatar_url ); ?>')"<?php endif; ?> data-username="<?php echo esc_attr( $a->user_nicename ); ?>">

								</div>
								<div class="members__member-info">
									<div class="members__username"><?php echo esc_html( $a->user_nicename ); ?></div>
									<div class="members__name">
										<?php
										if ( $info['first_name']->display && $info['first_name']->value ) {
											echo esc_html( $info['first_name']->value );
										}

										if ( $info['last_name']->display && $info['last_name']->value ) {
											echo esc_html( " {$info['last_name']->value}" );
										}
										?>
									</div>
									<?php if ( $info['location']->display && $info['location']->value && isset( $countries[ $info['location']->value ] ) ) : ?>
									<div class="members__location">
										<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>&nbsp;
										<?php echo esc_html( $countries[ $info['location']->value ] ); ?>
									</div>
									<?php endif; ?>
								</div>
							</a>
							<?php endforeach; ?>
						</div>
							<h2 class="group__card-title">
							<?php
							esc_html_e( 'People', 'community-portal' );
							?>
							<?php if ( ! empty( $group_members['count'] ) && $group_members['count'] > 0 ) : ?>
								<?php echo esc_html( " ({$group_members['count']})" ); ?>
							<?php endif; ?></h2>
						<?php if ( $group_members['count'] > 0 ) : ?>
						<div class="group members__search-container">
								<form method="GET" action="<?php echo ! empty( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : ''; ?>" class="members__form" id="members-search-form">
									<div class="members__input-container">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M17.5 17.5L13.875 13.875" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<input type="hidden" value="people" name="view" id="view" />
									<?php
									if ( isset( $_GET['tag'] ) && strlen( $get_tag ) > 0 ) {
										$get_tag = trim( $get_tag );
									} else {
										$get_tag = '';
									}
									?>
									<input type="hidden" value="<?php echo esc_attr( $get_tag ); ?>" name="tag" id="user-tag" />
									<?php
									if ( isset( $_GET['country'] ) && strlen( $location ) > 0 ) {
										$location = trim( $location );
									} else {
										$location = '';
									}
									?>
									<input type="hidden" value="<?php echo esc_attr( $location ); ?>" name="country" id="user-location" />
									<?php
									if ( isset( $_GET['language'] ) && strlen( $get_language ) > 0 ) {
										$get_language = trim( $get_language );
									} else {
										$get_language = '';
									}
									?>
									<input type="hidden" value="<?php echo esc_attr( $get_language ); ?>" name="language" id="user-language" />
									<?php
									if ( $search_user ) {
										$search_user = trim( $search_user );
									} else {
										$search_user = false;
									}
									?>
									<input type="text" name="u" id="members-search" class="members__search-input" placeholder="<?php esc_attr_e( 'Search people', 'community-portal' ); ?>" value="<?php echo esc_attr( $search_user ); ?>" />
						</div>
								<input type="submit" class="members__search-cta" value="<?php echo esc_attr_e( 'Search', 'community-portal' ); ?>" />
								</form>
							</div>
							<div class="groups__filter-container groups__filter-container--hidden">
								<span><?php esc_html_e( 'Search criteria:', 'community-portal' ); ?></span>
								<?php if ( count( $used_country_list ) > 0 ) : ?>
								<div class="members__select-container">
									<label class="members__label"><?php esc_html_e( 'Location', 'community-portal' ); ?></label>
									<select class="members__location-select">
										<option value=""><?php esc_html_e( 'Select', 'community-portal' ); ?></option>
										<?php foreach ( $used_country_list as $code   => $country ) : ?>
										<option value="<?php echo esc_attr( $code ); ?>"
															<?php
															if ( isset( $_GET['country'] ) && strlen( $location ) > 0 && $location === $code ) :
																?>
											selected<?php endif; ?>><?php echo esc_html( $country ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<?php endif; ?>
								<?php if ( count( $used_languages ) > 0 ) : ?>
								<div class="members__select-container">
									<label class="members__label"><?php esc_html_e( 'Language', 'community-portal' ); ?></label>
									<select class="members__language-select">
										<option value=""><?php esc_html_e( 'Select', 'community-portal' ); ?></option>
										<?php foreach ( $used_languages as $code => $language ) : ?>
											<?php if ( strlen( $code ) > 1 ) : ?>
										<option value="<?php echo esc_attr( $code ); ?>"
																<?php
																if ( isset( $_GET['language'] ) && strtolower( trim( $get_language ) ) === strtolower( $code ) ) :
																	?>
											selected<?php endif; ?>><?php echo esc_html( $language ); ?></option>
										<?php endif; ?>
										<?php endforeach; ?>
									</select>
								</div>
								<?php endif; ?>
								<div class="members__select-container">
									<label class="members__label"><?php esc_html_e( 'Tag', 'community-portal' ); ?></label>
									<select class="members__tag-select">
										<option value=""><?php esc_html_e( 'Select', 'community-portal' ); ?></option>
										<?php foreach ( $tags as $loop_tag ) : ?>
											<?php
											if ( $current_translation ) {
												if ( false !== stripos( $loop_tag->slug, '_' ) ) {
													$loop_tag->slug = substr( $loop_tag->slug, 0, stripos( $loop_tag->slug, '_' ) );
												}
											}
											?>
										<option value="<?php echo esc_html( $loop_tag->slug ); ?>"
																<?php
																if ( isset( $_GET['tag'] ) && strtolower( trim( $get_tag ) ) === strtolower( $loop_tag->slug ) ) :
																	?>
											selected<?php endif; ?>><?php echo esc_html( $loop_tag->name ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="groups__show-filters-container">
								<a href="#" class="groups__toggle-filter groups__toggle-filter--show">
									<span class="filters__show"><?php esc_html_e( 'Show Filters', 'community-portal' ); ?></span>
									<span class="filters__hide"><?php esc_html_e( 'Hide Filters', 'community-portal' ); ?></span>
								</a>
							</div>
						<div class="group__members">
							<?php if ( count( $filtered_members ) > 0 ) : ?>
								<?php
								if ( isset( $_GET['u'] ) && strlen( $search_user ) > 0 ) :
									?>
									<div class="members__results-for"><?php esc_html_e( 'Results for ', 'community-portal' ) . "\"{$search_user}\"" . esc_html( " ({$count})" ); ?></div>
							<?php endif; ?>
								<?php foreach ( $filtered_members as $member ) : ?>
									<?php
									$is_me = $logged_in && intval( $group_user->ID ) === intval( $member->user_id );
									$info  = mozilla_get_user_info( $group_user, $member, $logged_in );

									if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
										$avatar_url = preg_replace( '/^http:/i', 'https:', $info['profile_image']->value );
									} else {
										$avatar_url = $info['profile_image']->value;
									}
									?>
							<a href="
									<?php
									if ( $current_translation ) :
										?>
										<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/<?php echo esc_attr( $member->user_nicename ); ?>" class="members__member-card">
								<div class="members__avatar
									<?php
									if ( false === $info['profile_image']->display || false === $info['profile_image']->value ) :
										?>
									members__avatar--identicon<?php endif; ?>"
									<?php
									if ( $info['profile_image']->display && $info['profile_image']->value ) :
										?>
									style="background-image: url('<?php echo esc_url_raw( $avatar_url ); ?>')"<?php endif; ?> data-username="<?php echo esc_attr( $member->user_nicename ); ?>">

								</div>
								<div class="members__member-info">
									<div class="members__username"><?php echo esc_html( $member->user_nicename ); ?></div>
									<div class="members__name">
										<?php
										if ( $info['first_name']->display && $info['first_name']->value ) {
											echo esc_html( $info['first_name']->value );
										}

										if ( $info['last_name']->display && $info['last_name']->value ) {
											echo esc_html( " {$info['last_name']->value}" );
										}
										?>
									</div>
									<?php if ( $info['location']->display && $info['location']->value && isset( $countries[ $info['location']->value ] ) ) : ?>
										<div class="members__location">
											<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>&nbsp;
											<?php echo esc_html( $countries[ $info['location']->value ] ); ?>
										</div>
									<?php endif; ?>
								</div>
							</a>
							<?php endforeach; ?>
							<?php else : ?>
								<h2 class="members__title--no-members-found"><?php esc_html_e( 'No members found', 'community-portal' ); ?></h2>
							<?php endif; ?>
						</div>
						<?php else : ?>
							<p><?php esc_html_e( 'This group currently has no members', 'community-portal' ); ?></p>
						<?php endif; ?>
					</div>
					<?php elseif ( true === $is_events ) : ?>
						<?php

						$args   = array(
							'group'   => $group->id,
							'scope'   => 'all',
							'orderby' => 'event_start_date',
							'order'   => 'DESC',
						);
						$events = EM_Events::get( $args );
						?>
					<div class="row events__cards">
						<?php foreach ( $events as $event ) : ?>
							<?php
							$categories    = $event->get_categories();
							$location      = em_get_location( $event->location_id );
							$site_url      = get_home_url( null, 'events/' );
							$url           = $site_url . $event->slug;
							$all_countries = em_get_countries();
							$time          = gmdate( 'm', strtotime( $event->start_date ) );

							include locate_template( 'plugins/events-manager/templates/template-parts/single-event-card.php', false, false );

							?>
					<?php endforeach; ?>
					</div>
					<?php else : ?>
					<div class="group__left-column">
						<div class="group__card">
							<?php if ( isset( $group_meta['group_image_url'] ) && strlen( $group_meta['group_image_url'] ) > 0 ) : ?>
								<?php
								if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
									$group_image_url = preg_replace( '/^http:/i', 'https:', $group_meta['group_image_url'] );
								} else {
									$group_image_url = $group_meta['group_image_url'];
								}
								?>
							<div class="group__card-image" style="background-image: url('<?php echo esc_url_raw( $group_image_url ); ?>');">
								<?php if ( $is_admin ) : ?>
								<a href="
									<?php
									if ( $current_translation ) :
										?>
										<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/<?php echo esc_attr( $group->slug ); ?>/admin/edit-details/" class="group__edit-link">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z" fill="#0060DF"/>
									</svg>
								</a>
								<?php endif; ?>
							</div>
							<?php else : ?>
							<div class="group__card-no-image">
								<?php if ( $is_admin ) : ?>
								<a href="
									<?php
									if ( $current_translation ) :
										?>
										<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/<?php echo esc_attr( $group->slug ); ?>/admin/edit-details/" class="group__edit-link">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z" fill="#0060DF"/>
									</svg>
								</a>
								<?php endif; ?>
							</div>
							<?php endif; ?>
							<div class="group__card-content">
								<div class="group__card-cta-container
								<?php
								if ( $is_admin ) :
									?>
									group__card-cta-container--end<?php endif; ?>">
								<?php if ( ! $is_admin ) : ?>
									<?php if ( $is_member ) : ?>
										<a href="#" class="group__leave-cta" data-group="<?php print esc_attr( $group->id ); ?>">
											<span class="join"><?php esc_html_e( 'Join Group', 'community-portal' ); ?></span>
											<span class="leave"><?php esc_html_e( 'Leave Group', 'community-portal' ); ?></span>
										</a>
									<?php else : ?>
										<?php if ( 'members' === $invite_status || '' === $invite_status ) : ?>
											<a href="#" class="group__join-cta" data-group="<?php print esc_attr( $group->id ); ?>">
												<span class="join"><?php esc_html_e( 'Join Group', 'community-portal' ); ?></span>
												<span class="leave"><?php esc_html_e( 'Leave Group', 'community-portal' ); ?></span>
											</a>
										<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>
								<a href="#" class="group__share-cta">
									<svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M1 9V15C1 15.3978 1.15804 15.7794 1.43934 16.0607C1.72064 16.342 2.10218 16.5 2.5 16.5H11.5C11.8978 16.5 12.2794 16.342 12.5607 16.0607C12.842 15.7794 13 15.3978 13 15V9M10 4.5L7 1.5M7 1.5L4 4.5M7 1.5V11.25" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<?php esc_html_e( 'Share Group', 'community-portal' ); ?>
								</a>
								</div>
								<hr class="group__keyline" />
								<h2 class="group__card-title"><?php esc_html_e( 'About Us', 'community-portal' ); ?></h2>
								<?php
								echo wp_kses(
									wpautop( substr( trim( $group->description ), 0, 3000 ) ),
									array(
										'p'  => array(),
										'br' => array(),
									)
								);
								?>
								<?php
								if ( ( isset( $group_meta['group_telegram'] ) && strlen( $group_meta['group_telegram'] ) > 0 )
								|| ( isset( $group_meta['group_facebook'] ) && strlen( trim( $group_meta['group_facebook'] ) ) > 0 )
								|| ( isset( $group_meta['group_discourse'] ) && strlen( trim( $group_meta['group_discourse'] ) ) > 0 )
								|| ( isset( $group_meta['group_github'] ) && strlen( trim( $group_meta['group_github'] ) ) > 0 )
								|| ( isset( $group_meta['group_twitter'] ) && strlen( trim( $group_meta['group_twitter'] ) ) > 0 )
								|| ( isset( $group_meta['group_matrix'] ) && strlen( trim( $group_meta['group_matrix'] ) ) > 0 )
								|| ( isset( $group_meta['group_mastodon'] ) && strlen( trim( $group_meta['group_mastodon'] ) ) > 0 )
								|| ( isset( $group_meta['group_youtube'] ) && strlen( trim( $group_meta['group_youtube'] ) ) > 0 )
								|| ( isset( $group_meta['group_peertube'] ) && strlen( trim( $group_meta['group_peertube'] ) ) > 0 )
								|| ( isset( $group_meta['group_pixelfed'] ) && strlen( trim( $group_meta['group_pixelfed'] ) ) > 0 )
								|| ( isset( $group_meta['group_other'] ) && strlen( $group_meta['group_other'] ) > 0 ) ) :
									?>
								<div class="group__community-links">
									<span class="no-line"><?php esc_html_e( 'Community Links', 'community-portal' ); ?></span>
									<?php if ( isset( $group_meta['group_telegram'] ) && strlen( $group_meta['group_telegram'] ) > 0 ) : ?>
										<div class="group__community-link-container">
											<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
												<path d="M24.3332 7.66699L15.1665 16.8337" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M24.3332 7.66699L18.4998 24.3337L15.1665 16.8337L7.6665 13.5003L24.3332 7.66699Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
											<a href="<?php echo ( mozilla_verify_url( $group_meta['group_telegram'], false ) ? esc_url_raw( mozilla_verify_url( $group_meta['group_telegram'], false ) ) : 'https://t.me/' . esc_attr( $group_meta['group_telegram'] ) ); ?>" class="group__social-link"><?php esc_html_e( 'Telegram', 'community-portal' ); ?></a>
										</div>
									<?php endif; ?>
									<?php if ( isset( $group_meta['group_facebook'] ) && strlen( trim( $group_meta['group_facebook'] ) ) > 0 ) : ?>
										<div class="group__community-link-container">
											<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
												<path fill-rule="evenodd" clip-rule="evenodd" d="M26 16C26 10.4771 21.5229 6 16 6C10.4771 6 6 10.4771 6 16C6 20.9913 9.65686 25.1283 14.4375 25.8785V18.8906H11.8984V16H14.4375V13.7969C14.4375 11.2906 15.9304 9.90625 18.2146 9.90625C19.3087 9.90625 20.4531 10.1016 20.4531 10.1016V12.5625H19.1921C17.9499 12.5625 17.5625 13.3333 17.5625 14.1242V16H20.3359L19.8926 18.8906H17.5625V25.8785C22.3431 25.1283 26 20.9913 26 16Z" fill="black"/>
											</svg>
											<a href="<?php echo ( mozilla_verify_url( $group_meta['group_facebook'], true ) ? esc_url_raw( mozilla_verify_url( $group_meta['group_facebook'], true ) ) : 'https://www.facebook.com/' . esc_attr( $group_meta['group_facebook'] ) ); ?>" class="group__social-link"><?php esc_html_e( 'Facebook', 'community-portal' ); ?></a>
										</div>
									<?php endif; ?>
									<?php if ( isset( $group_meta['group_discourse'] ) && strlen( trim( $group_meta['group_discourse'] ) ) > 0 ) : ?>
										<div class="group__community-link-container">
											<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
												<path d="M23.5 15.5834C23.5029 16.6832 23.2459 17.7683 22.75 18.75C22.162 19.9265 21.2581 20.916 20.1395 21.6078C19.021 22.2995 17.7319 22.6662 16.4167 22.6667C15.3168 22.6696 14.2318 22.4126 13.25 21.9167L8.5 23.5L10.0833 18.75C9.58744 17.7683 9.33047 16.6832 9.33333 15.5834C9.33384 14.2682 9.70051 12.9791 10.3923 11.8605C11.084 10.7419 12.0735 9.838 13.25 9.25002C14.2318 8.75413 15.3168 8.49716 16.4167 8.50002H16.8333C18.5703 8.59585 20.2109 9.32899 21.4409 10.5591C22.671 11.7892 23.4042 13.4297 23.5 15.1667V15.5834Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
											<a href="<?php echo ( mozilla_verify_url( $group_meta['group_discourse'], true ) ? esc_url_raw( mozilla_verify_url( $group_meta['group_discourse'], true ) ) : 'https://discourse.mozilla.org/u/' . esc_attr( $group_meta['group_discourse'] ) . '/summary' ); ?>" class="group__social-link"><?php esc_html_e( 'Discourse', 'community-portal' ); ?></a>
										</div>
									<?php endif; ?>
									<?php if ( isset( $group_meta['group_github'] ) && strlen( trim( $group_meta['group_github'] ) ) > 0 ) : ?>
										<div class="group__community-link-container">
											<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
												<g clip-path="url(#clip0)">
												<path d="M13.4998 22.6669C9.33317 23.9169 9.33317 20.5835 7.6665 20.1669M19.3332 25.1669V21.9419C19.3644 21.5445 19.3107 21.145 19.1757 20.77C19.0406 20.395 18.8273 20.053 18.5498 19.7669C21.1665 19.4752 23.9165 18.4835 23.9165 13.9335C23.9163 12.77 23.4687 11.6512 22.6665 10.8085C23.0464 9.79061 23.0195 8.66548 22.5915 7.66686C22.5915 7.66686 21.6082 7.37519 19.3332 8.90019C17.4232 8.38254 15.4098 8.38254 13.4998 8.90019C11.2248 7.37519 10.2415 7.66686 10.2415 7.66686C9.81348 8.66548 9.78662 9.79061 10.1665 10.8085C9.35828 11.6574 8.91027 12.7864 8.9165 13.9585C8.9165 18.4752 11.6665 19.4669 14.2832 19.7919C14.009 20.0752 13.7976 20.413 13.6626 20.7835C13.5276 21.1539 13.4722 21.5486 13.4998 21.9419V25.1669" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</g>
												<defs>
												<clipPath id="clip0">
												<rect width="20" height="20" fill="white" transform="translate(6 6)"/>
												</clipPath>
												</defs>
											</svg>
											<a href="<?php echo ( mozilla_verify_url( $group_meta['group_github'], true ) ? esc_url_raw( mozilla_verify_url( $group_meta['group_github'], true ) ) : 'https://www.github.com/' . esc_attr( $group_meta['group_github'] ) ); ?>" class="group__social-link"><?php esc_html_e( 'Github', 'community-portal' ); ?></a>
										</div>
									<?php endif; ?>
									<?php if ( isset( $group_meta['group_twitter'] ) && strlen( trim( $group_meta['group_twitter'] ) ) > 0 ) : ?>
										<div class="group__community-link-container">
											<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
												<path d="M12.3766 23.9366C19.7469 23.9366 23.7781 17.8303 23.7781 12.535C23.7781 12.3616 23.7781 12.1889 23.7664 12.017C24.5506 11.4498 25.2276 10.7474 25.7656 9.94281C25.0343 10.2669 24.2585 10.4794 23.4641 10.5733C24.3006 10.0725 24.9267 9.28482 25.2258 8.35688C24.4392 8.82364 23.5786 9.15259 22.6812 9.32953C22.0771 8.6871 21.278 8.26169 20.4077 8.11915C19.5374 7.97661 18.6444 8.12487 17.8668 8.541C17.0893 8.95713 16.4706 9.61792 16.1064 10.4211C15.7422 11.2243 15.6529 12.1252 15.8523 12.9842C14.2592 12.9044 12.7006 12.4903 11.2778 11.7691C9.85506 11.0478 8.59987 10.0353 7.59375 8.7975C7.08132 9.67966 6.92438 10.724 7.15487 11.7178C7.38536 12.7116 7.98596 13.5802 8.83437 14.1467C8.19667 14.1278 7.57287 13.9558 7.01562 13.6452C7.01562 13.6616 7.01562 13.6788 7.01562 13.6959C7.01588 14.6211 7.33614 15.5177 7.9221 16.2337C8.50805 16.9496 9.32362 17.4409 10.2305 17.6241C9.64052 17.785 9.02155 17.8085 8.42109 17.6928C8.67716 18.489 9.17568 19.1853 9.84693 19.6843C10.5182 20.1832 11.3286 20.4599 12.1648 20.4756C10.7459 21.5908 8.99302 22.1962 7.18828 22.1944C6.86946 22.1938 6.55094 22.1745 6.23438 22.1366C8.0669 23.3126 10.1992 23.9363 12.3766 23.9334" fill="black"/>
											</svg>
											<a href="<?php echo ( mozilla_verify_url( $group_meta['group_twitter'], true ) ? esc_url_raw( mozilla_verify_url( $group_meta['group_twitter'], true ) ) : 'https://www.twitter.com/' . esc_attr( $group_meta['group_twitter'] ) ); ?>" class="group__social-link"><?php esc_html_e( 'Twitter', 'community-portal' ); ?></a>
										</div>
									<?php endif; ?>
									<?php if ( isset( $group_meta['group_matrix'] ) && strlen( trim( $group_meta['group_matrix'] ) ) > 0 ) : ?>
										<div class="group__community-link-container">
											<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
												<path d="M12.6113 12.6035L12.6729 13.4307C13.1969 12.7881 13.9056 12.4668 14.7988 12.4668C15.7513 12.4668 16.4053 12.8428 16.7607 13.5947C17.2803 12.8428 18.0208 12.4668 18.9824 12.4668C19.7845 12.4668 20.3815 12.7015 20.7734 13.1709C21.1654 13.6357 21.3613 14.3376 21.3613 15.2764V20H19.3789V15.2832C19.3789 14.8639 19.2969 14.5586 19.1328 14.3672C18.9688 14.1712 18.6794 14.0732 18.2646 14.0732C17.6722 14.0732 17.262 14.3558 17.0342 14.9209L17.041 20H15.0654V15.29C15.0654 14.8617 14.9811 14.5518 14.8125 14.3604C14.6439 14.1689 14.3568 14.0732 13.9512 14.0732C13.3906 14.0732 12.985 14.3057 12.7344 14.7705V20H10.7588V12.6035H12.6113Z" fill="black"/>
												<line x1="9" y1="9" x2="6" y2="9" stroke="black" stroke-width="2"/>
												<line x1="26" y1="9" x2="23" y2="9" stroke="black" stroke-width="2"/>
												<line x1="9" y1="24" x2="6" y2="24" stroke="black" stroke-width="2"/>
												<line x1="26" y1="24" x2="23" y2="24" stroke="black" stroke-width="2"/>
												<line x1="7" y1="9" x2="7" y2="23" stroke="black" stroke-width="2"/>
												<line x1="25" y1="9" x2="25" y2="23" stroke="black" stroke-width="2"/>
											</svg>
											<a href="<?php echo ( mozilla_verify_url( $group_meta['group_matrix'], true ) ? esc_url_raw( mozilla_verify_url( $group_meta['group_matrix'], true ) ) : 'https://chat.mozilla.org/#/room/#' . esc_attr( $group_meta['group_matrix'] ) ); ?>" class="group__social-link"><?php esc_html_e( 'Matrix', 'community-portal' ); ?></a>
										</div>
									<?php endif; ?>
									<?php if ( isset( $group_meta['group_youtube'] ) && strlen( trim( $group_meta['group_youtube'] ) ) > 0 ) : ?>
										<div class="group__community-link-container">
											<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
												<g
												id="g5"
												transform="matrix(0.18122353,0,0,0.18122353,4.9996855,8.298)"><path
												class="st0"
												d="M 118.9,13.3 C 117.5,8.1 113.4,4 108.2,2.6 98.7,0 60.7,0 60.7,0 60.7,0 22.7,0 13.2,2.5 8.1,3.9 3.9,8.1 2.5,13.3 0,22.8 0,42.5 0,42.5 0,42.5 0,62.3 2.5,71.7 3.9,76.9 8,81 13.2,82.4 22.8,85 60.7,85 60.7,85 c 0,0 38,0 47.5,-2.5 5.2,-1.4 9.3,-5.5 10.7,-10.7 2.5,-9.5 2.5,-29.2 2.5,-29.2 0,0 0.1,-19.8 -2.5,-29.3 z"
												id="path7"
												inkscape:connector-curvature="0"
												style="fill:#000000;fill-opacity:1" /><polygon
												class="st1"
												points="80.2,42.5 48.6,24.3 48.6,60.7 "
												id="polygon9"
												style="fill:#ffffff" /></g>
											</svg>
											<a href="<?php echo ( esc_attr( $group_meta['group_youtube'] ) ); ?>" class="group__social-link"><?php esc_html_e( 'Youtube', 'community-portal' ); ?></a>
										</div>
									<?php endif; ?>
									<?php if ( isset( $group_meta['group_peertube'] ) && strlen( trim( $group_meta['group_peertube'] ) ) > 0 ) : ?>
										<div class="group__community-link-container">
											<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
												<g
													stroke-width="32"
													id="g8"
													transform="matrix(0.03222461,0,0,0.03222461,-80.446184,34.356942)">
													<path
													d="m 2799,-911 v 341.344 l 256,-170.656"
													fill="#211f20"
													id="path2" />
													<path
													d="m 2799,-569.656 v 341.344 l 256,-170.656"
													fill="#737373"
													id="path4" />
													<path
													d="M 3055,-740.344 V -399 l 256,-170.656"
													fill="#f1680d"
													id="path6"
													style="fill:#000000;fill-opacity:1" />
												</g>
											</svg>
											<a href="<?php echo ( esc_attr( $group_meta['group_peertube'] ) ); ?>" class="group__social-link"><?php esc_html_e( 'Peertube', 'community-portal' ); ?></a>
										</div>
									<?php endif; ?>
									<?php if ( isset( $group_meta['group_mastodon'] ) && strlen( trim( $group_meta['group_mastodon'] ) ) > 0 ) : ?>
										<div class="group__community-link-container">
											<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
												width="32" height="32" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512;" xml:space="preserve">
											<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
											<g
												id="Page-1"
												stroke="none"
												stroke-width="1"
												fill="none"
												fill-rule="evenodd"
												transform="matrix(0.44897959,0,0,0.44897959,4.7755103,4.7755103)">
												<g
												id="icon-copy-6"
												fill="#000000">
												<path
													d="M 26.198975,16.165416 C 26.119137,15.671725 25.997071,15.183495 25.832778,14.706495 25.40327,13.457749 24.707155,12.326751 23.768817,11.388212 L 18.820091,6.4384255 C 18.628605,6.2468992 18.425398,6.0651186 18.205958,5.8873902 18.945732,2.7779446 21.749367,0.5 25.005686,0.5 c 3.429843,0 6.357469,2.5271874 6.900166,5.8913433 0.06514,0.4048724 0.09821,0.7640171 0.09821,1.1085317 v 6.999875 c 0,0.878027 -0.168989,1.736963 -0.49248,2.549113 -1.067757,-0.56365 -2.289008,-0.883447 -3.586368,-0.883447 z M 23.8074,31.046185 c -0.223984,1.41741 -0.100236,2.878591 0.371194,4.24732 0.429509,1.248746 1.125623,2.379744 2.063962,3.318283 l 4.949627,4.950686 c 0.191175,0.190661 0.394025,0.371879 0.613359,0.549601 C 31.065962,47.221794 28.262192,49.5 25.005686,49.5 c -3.429897,0 -6.357563,-2.527268 -6.900192,-5.891505 -0.06511,-0.404615 -0.09818,-0.76381 -0.09818,-1.10837 V 35.50025 c 0,-0.0064 9e-6,-0.01282 2.7e-5,-0.01923 l 4.59827,-4.434834 z m 11.33941,-5.168387 c 0.05003,-0.01624 0.09995,-0.03293 0.149739,-0.05009 1.250035,-0.431139 2.3801,-1.126732 3.317571,-2.064404 l 4.948727,-4.949787 c 0.191514,-0.191555 0.373282,-0.394839 0.550998,-0.614365 C 47.222277,18.938379 49.5,21.74313 49.5,25.000437 c 0,3.430633 -2.526727,6.358926 -5.890243,6.901671 -0.404528,0.06512 -0.763646,0.0982 -1.108132,0.0982 h -6.998376 c -1.056748,0 -2.085836,-0.244942 -3.038961,-0.709549 -0.505743,-0.246519 -0.977588,-0.551953 -1.406585,-0.90735 1.934087,-0.862289 3.433975,-2.498464 4.089107,-4.505615 z m -2.164945,-7.815834 c 0.505054,-1.12035 0.77179,-2.325631 0.77179,-3.562214 V 7.499875 c 0,-0.2702157 -0.01523,-0.5419946 -0.04484,-0.8235787 2.721205,-1.6753333 6.314594,-1.3031901 8.616831,0.999495 2.425304,2.4266407 2.70907,6.2839547 0.714311,9.0459157 -0.238057,0.330416 -0.469145,0.60835 -0.714262,0.85352 l -4.948726,4.949786 c -0.548388,0.548506 -1.180179,0.993408 -1.874291,1.328019 0.0027,-0.08208 0.0041,-0.164499 0.0041,-0.247231 0,-2.202519 -0.975177,-4.181514 -2.524922,-5.543837 z m -3.863787,12.892655 c 0.731501,0.784141 1.604938,1.434093 2.579733,1.909246 1.188286,0.579239 2.478846,0.886416 3.805438,0.886416 h 6.998376 c 0.270193,0 0.54195,-0.01524 0.823514,-0.04486 1.675415,2.721432 1.303125,6.315677 -0.999442,8.618738 -2.424892,2.425411 -6.282214,2.709064 -9.044062,0.714318 -0.330212,-0.238011 -0.608087,-0.469149 -0.853203,-0.714318 L 27.479705,37.374373 C 26.733064,36.627572 26.178435,35.72613 25.832696,34.723272 25.42472,33.538775 25.341877,32.266466 25.584207,31.046185 h 2.341007 c 0.405859,0 0.80427,-0.0313 1.192864,-0.09157 z M 18.036109,17.005126 C 16.949856,16.519651 15.750783,16.250594 14.496751,16.250594 H 7.4983754 c -0.2663464,0 -0.5341903,0.01474 -0.8120522,0.04341 C 5.0111787,13.5721 5.3832109,9.9787981 7.6856753,7.6758403 10.110567,5.2504289 13.967889,4.9667757 16.729737,6.9615215 c 0.330213,0.2380116 0.608087,0.4691494 0.853204,0.7143188 l 4.948671,4.9506067 c 0.746683,0.746843 1.301306,1.648266 1.647046,2.651102 0.100386,0.291311 0.181087,0.587963 0.242103,0.887867 h -3.785522 c -0.973366,0 -1.872472,0.312024 -2.59913,0.83971 z m -1.774854,18.2486 c -0.0024,0.08203 -0.0035,0.164205 -0.0035,0.246524 v 6.999875 c 0,0.270216 0.01523,0.541995 0.04484,0.823579 C 13.581331,44.999049 9.9879108,44.626888 7.6856753,42.32416 5.2607836,39.898748 4.9771911,36.0406 6.9715096,33.27816 7.2094701,32.947876 7.4405584,32.669943 7.6856753,32.424773 l 4.9487257,-4.949786 c 0.746641,-0.746801 1.64789,-1.30155 2.650534,-1.647363 0.31995,-0.110247 0.64631,-0.196757 0.97632,-0.259529 z m 0,-11.460596 c -0.523494,0.07929 -1.041261,0.205968 -1.546431,0.380038 -1.250035,0.431139 -2.380101,1.126732 -3.317572,2.064404 L 6.4485257,31.187359 C 6.2539281,31.381998 6.0693935,31.588746 5.8889474,31.812338 2.7803249,31.074168 0.5,28.262233 0.5,25.000437 0.5,21.566274 3.0317905,18.636033 6.4096121,18.09742 6.5515625,18.07324 7.2396907,18.00056 7.4983754,18.00056 h 6.9983756 c 0.803206,0 1.5786,0.13853 2.302175,0.393784 -0.342735,0.612344 -0.537671,1.315631 -0.537671,2.0636 z"
													id="Combined-Shape" />
												</g>
											</g>
											</svg>
											<a href="<?php echo ( esc_attr( $group_meta['group_mastodon'] ) ); ?>" class="group__social-link"><?php esc_html_e( 'Mastodon', 'community-portal' ); ?></a>
										</div>
									<?php endif; ?>
									<?php if ( isset( $group_meta['group_pixelfed'] ) && strlen( trim( $group_meta['group_pixelfed'] ) ) > 0 ) : ?>
										<div class="group__community-link-container">
											<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
												<path d="M12.6113 12.6035L12.6729 13.4307C13.1969 12.7881 13.9056 12.4668 14.7988 12.4668C15.7513 12.4668 16.4053 12.8428 16.7607 13.5947C17.2803 12.8428 18.0208 12.4668 18.9824 12.4668C19.7845 12.4668 20.3815 12.7015 20.7734 13.1709C21.1654 13.6357 21.3613 14.3376 21.3613 15.2764V20H19.3789V15.2832C19.3789 14.8639 19.2969 14.5586 19.1328 14.3672C18.9688 14.1712 18.6794 14.0732 18.2646 14.0732C17.6722 14.0732 17.262 14.3558 17.0342 14.9209L17.041 20H15.0654V15.29C15.0654 14.8617 14.9811 14.5518 14.8125 14.3604C14.6439 14.1689 14.3568 14.0732 13.9512 14.0732C13.3906 14.0732 12.985 14.3057 12.7344 14.7705V20H10.7588V12.6035H12.6113Z" fill="black"/>
												<line x1="9" y1="9" x2="6" y2="9" stroke="black" stroke-width="2"/>
												<line x1="26" y1="9" x2="23" y2="9" stroke="black" stroke-width="2"/>
												<line x1="9" y1="24" x2="6" y2="24" stroke="black" stroke-width="2"/>
												<line x1="26" y1="24" x2="23" y2="24" stroke="black" stroke-width="2"/>
												<line x1="7" y1="9" x2="7" y2="23" stroke="black" stroke-width="2"/>
												<line x1="25" y1="9" x2="25" y2="23" stroke="black" stroke-width="2"/>
											</svg>
											<a href="<?php echo ( esc_attr( $group_meta['group_pixelfed'] ) ); ?>" class="group__social-link"><?php esc_html_e( 'Pixelfed', 'community-portal' ); ?></a>
										</div>
									<?php endif; ?>
									<?php if ( isset( $group_meta['group_other'] ) && strlen( $group_meta['group_other'] ) > 0 && mozilla_verify_url( $group_meta['group_other'], false ) ) : ?>
										<div class="group__community-link-container">
											<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
												<g clip-path="url(#clip0)">
												<path d="M20.1668 23.5V21.8333C20.1668 20.9493 19.8156 20.1014 19.1905 19.4763C18.5654 18.8512 17.7176 18.5 16.8335 18.5H10.1668C9.28277 18.5 8.43493 18.8512 7.80981 19.4763C7.18469 20.1014 6.8335 20.9493 6.8335 21.8333V23.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M13.4998 15.1667C15.3408 15.1667 16.8332 13.6743 16.8332 11.8333C16.8332 9.99238 15.3408 8.5 13.4998 8.5C11.6589 8.5 10.1665 9.99238 10.1665 11.8333C10.1665 13.6743 11.6589 15.1667 13.4998 15.1667Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M25.1665 23.5001V21.8334C25.166 21.0948 24.9201 20.3774 24.4676 19.7937C24.0152 19.2099 23.3816 18.793 22.6665 18.6084" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M19.3335 8.6084C20.0505 8.79198 20.686 9.20898 21.1399 9.79366C21.5937 10.3783 21.84 11.0974 21.84 11.8376C21.84 12.5777 21.5937 13.2968 21.1399 13.8815C20.686 14.4661 20.0505 14.8831 19.3335 15.0667" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</g>
												<defs>
												<clipPath id="clip0">
												<rect width="20" height="20" fill="white" transform="translate(6 6)"/>
												</clipPath>
												</defs>
											</svg>
											<a href="<?php echo esc_url_raw( mozilla_verify_url( $group_meta['group_other'], false ) ); ?>" class="group__social-link"><?php esc_html_e( 'Other', 'community-portal' ); ?></a>
										</div>
									<?php endif; ?>
								</div>
								<?php endif; ?>
							</div>
						</div>
						<?php if ( ( isset( $group_meta['group_meeting_details'] ) && $group_meta['group_meeting_details'] ) || ( isset( $group_meta['group_address'] ) && $group_meta['group_address'] ) ) : ?>
						<h2 class="group__card-title"><?php esc_html_e( 'Meetings', 'community-portal' ); ?></h2>
						<div class="group__card">
							<div class="group__card-content">
								<?php if ( isset( $group_meta['group_meeting_details'] ) && strlen( trim( $group_meta['group_meeting_details'] ) ) > 0 ) : ?>
								<span class="no-line"><?php esc_html_e( 'Meeting Details', 'community-portal' ); ?></span>
								<p class="group__card-copy">
									<?php echo esc_html( $group_meta['group_meeting_details'] ); ?>
								</p>
								<?php endif; ?>
								<?php if ( isset( $group_meta['group_meeting_details'] ) && strlen( trim( $group_meta['group_meeting_details'] ) ) > 0 && isset( $group_meta['group_address'] ) && strlen( trim( $group_meta['group_address'] ) ) > 0 ) : ?>
								<hr />
								<?php endif; ?>
								<?php if ( isset( $group_meta['group_address'] ) && $group_meta['group_address'] ) : ?>
								<span class="no-line"><?php esc_html_e( 'Location', 'community-portal' ); ?></span>
									<?php if ( isset( $group_meta['group_address_type'] ) && 'url' === strtolower( $group_meta['group_address_type'] ) ) : ?>
									<div>
										<a class="group__meeting-location-link" href="<?php echo esc_url_raw( $group_meta['group_address'] ); ?>" target="_blank"><?php echo esc_html( $group_meta['group_address'] ); ?></a>
									</div>
								<?php else : ?>
									<p class="group__card-copy">
										<?php echo esc_html( $group_meta['group_address'] ); ?>
									</p>
								<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
						<?php endif; ?>
						<?php if ( isset( $discourse_group['discourse_category_url'] ) && strlen( $discourse_group['discourse_category_url'] ) > 0 ) : ?>
							<?php
							$toptics = array();
							$options = wp_load_alloptions();

							if ( isset( $options['discourse_url'] ) && strlen( $options['discourse_url'] ) > 0 ) {
								$discourse_api_url = rtrim( $options['discourse_url'], '/' );

								$discourse_category_id = intval( trim( $discourse_group['discourse_category_id'] ) );
								$api_url               = "{$options['discourse_url']}/c/{$discourse_category_id}";

								$topics = mozilla_discourse_get_category_topics( $api_url );
								$topics = array_slice( $topics, 0, 4 );
							}
							?>
							<?php if ( count( $topics ) > 0 ) : ?>
						<h2 class="group__card-title"><?php esc_html_e( 'Discussions', 'community-portal' ); ?></h2>
						<div class="group__card group__card--table">
							<div class="group__card-content">
								<table class="group__announcements">
									<thead>
										<tr>
											<th class="group__table-header group__table-header--topic"><?php esc_html_e( 'Topic', 'community-portal' ); ?></th>
											<th class="group__table-header"><?php esc_html_e( 'Replies', 'community-portal' ); ?></th>
											<th class="group__table-header"><?php esc_html_e( 'Views', 'community-portal' ); ?></th>
											<th class="group__table-header group__table-header--activity"><?php esc_html_e( 'Activity', 'community-portal' ); ?></th>
										</tr>
									</thead>
									<tbody>
									<?php foreach ( $topics as $topic ) : ?>
										<tr>
											<td class="group__table-cell group__table-cell--topic">
												<a href="<?php echo esc_url_raw( $options['discourse_url'] ); ?>/t/topic/<?php echo esc_attr( $topic->id ); ?>" class="group__topic-link">
													<div class="group__topic-title"><?php echo esc_html( $topic->title ); ?></div>
													<div class="group__topic-date"><?php print esc_html( gmdate( 'F j, Y', strtotime( $topic->created_at ) ) ); ?></div>
												</a>
											</td>
											<td class="group__table-cell">
												<?php
													$reply_count = intval( $topic->posts_count ) > 0 ? intval( $topic->posts_count ) - 1 : 0;
												?>
												<div class="group__topic-replies"><?php echo esc_html( $reply_count ); ?></div>
											</td>
											<td class="group__table-cell">
											<div class="group__topic-views"><?php echo esc_html( $topic->views ); ?></div>
											</td>
											<td class="group__table-cell group__table-cell--activity">
												<div class="group__topic-activity"><?php echo isset( $topic->last_posted_at ) && strlen( $topic->last_posted_at ) > 0 ? esc_html( gmdate( 'M j', strtotime( $topic->last_posted_at ) ) ) : esc_html( gmdate( 'M j', strtotime( $topic->created_at ) ) ); ?></div>
											</td>
										</tr>
									<?php endforeach; ?>
										<tr>
											<td colspan="4" class="group__table-cell group__table-cell--topic">
												<a href="<?php echo esc_url_raw( $discourse_group['discourse_category_url'] ); ?>" class="group__view-updates-link">
													<?php esc_html_e( 'View more topics', 'community-portal' ); ?><svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M2.33301 8.66732L5.99967 5.00065L2.33301 1.33398" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
													</svg>
												</a>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<?php endif; ?>
						<?php endif; ?>
					</div>
					<div class="group__right-column">
						<div class="group__card">
							<div class="group__card-content group__card-content--small">
								<div>
									<p class="group__card-content__subtitle"><?php esc_html_e( 'Activity', 'community-portal' ); ?></p>
								</div>
								<?php
									$args = array(
										'group' => $group->id,
										'scope' => 'month',
									);

									$events      = EM_Events::get( $args );
									$event_count = count( $events );
									?>
								<a href="
								<?php
								if ( $current_translation ) :
									?>
									<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/<?php echo esc_attr( $group->slug ); ?>/?view=events" class="group__member-count">
									<div class="group__member-count-container">
										<p>
											<span class="group__member-count__numeral"><?php echo esc_html( $event_count ); ?></span>
											<span><?php esc_html_e( 'Events this month', 'community-portal' ); ?></span>
										</p>
									</div>
								</a>

								<a href="
									<?php
									if ( $current_translation ) :
										?>
								<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/<?php echo esc_attr( $group->slug ); ?>?view=people" class="group__member-count">
									<div class="group__member-count-container">
										<p>
											<span class="group__member-count__numeral"><?php echo esc_html( $member_count ); ?></span>
											<span><?php esc_html_e( 'Members', 'community-portal' ); ?></span>
										</p>
									</div>
								</a>
							</div>
						</div>
						<?php
							$args   = array(
								'group'   => $group->id,
								'orderby' => 'event_start_date',
								'order'   => 'DESC',
								'scope'   => 'all',
							);
							$events = EM_Events::get( $args );
							$event  = isset( $events[0] ) && ! empty( $events[0] ) ? $events[0] : false;
							$event_date;
							if ( $event && isset( $event->start_date ) ) {
								$date_format = 'en' === $current_translation ? 'M d' : 'd M';
								$event_date  = mozilla_localize_date( $event->start_date, $date_format );
							}

							$location   = $event ? em_get_location( $event->location_id ) : null;
							$event_link = $event ? get_home_url( null, 'events/' . $event->event_slug ) : null;
							?>
						<?php if ( $event ) : ?>
						<div class="group__card">
							<div class="group__card-content group__card-content--small">
								<div>
									<p class="group__card-content__subtitle"><?php esc_html_e( 'Related Events', 'community-portal' ); ?></p>
								</div>

								<a class="group__event wtf" href="<?php echo esc_url_raw( $event_link ); ?>">
									<div class="group__event-date">
										<?php echo esc_html( $event_date ); ?>
									</div>
									<div class="group__event-info">
										<div class="group__event-title"><?php echo esc_html( $event->event_name ); ?></div>
										<div class="group__event-time">
											<?php
												$date_format    = 'en' === $current_translation ? 'F d, Y ∙ H:i' : 'd F, Y ∙ H:i';
												$formatted_date = mozilla_localize_date( $event->start_date, $date_format );
												echo esc_html( $formatted_date ) . ' ' . esc_html__( 'UTC' );
											?>
										</div>
										<div class="group__event-location">
											<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
											<?php if ( 'OE' === $location->location_country ) : ?>
												<?php esc_html_e( 'Online Event', 'community-portal' ); ?>
											<?php elseif ( $location->location_town && $location->location_country ) : ?>
												<?php echo esc_html( "{$location->location_town}, {$countries[$location->location_country]}" ); ?>
											<?php elseif ( $location->location_town && ! $location->location_country ) : ?>
												<?php echo esc_html( "{$location->location_town}" ); ?>
											<?php elseif ( ! $location->location_town && $location->location_country ) : ?>
												<?php echo esc_html( "{$countries[$location->location_country]}" ); ?>
											<?php endif; ?>
										</div>
									</div>
								</a>
								<a href="
								<?php
								if ( $current_translation ) :
									?>
									<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/<?php echo esc_attr( $group->slug ); ?>/?view=events" class="group__events-link">
									<?php esc_html_e( 'View more events', 'community-portal' ); ?><svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.33301 8.66634L5.99967 4.99967L2.33301 1.33301" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
								</a>
							</div>
						</div>
						<?php endif; ?>
						<div class="group__card">
							<div class="group__card-content group__card-content--small">
								<div>
									<p class="group__card-content__subtitle"><?php esc_html_e( 'Group Contacts', 'community-portal' ); ?></p>
								</div>
								<div class="group__admins">
									<?php foreach ( $admins as $admin ) : ?>
										<?php
										$u = get_userdata( $admin->user_id );

										$is_me     = $logged_in && intval( $group_user->ID ) === intval( $admin->user_id );
										$logged_in = mozilla_is_logged_in();
										$is_me     = $logged_in && intval( $group_user->ID ) === intval( $admin->user_id );

										$info = mozilla_get_user_info( $group_user, $u, $logged_in );


										if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
											$avatar_url = preg_replace( '/^http:/i', 'https:', $info['profile_image']->value );
										} else {
											$avatar_url = $info['profile_image']->value;
										}

										?>
									<a class="group__admin" href="
										<?php
										if ( $current_translation ) :
											?>
											<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/<?php echo esc_attr( $u->user_nicename ); ?>">
										<div class="members__avatar
										<?php
										if ( false === $info['profile_image']->display || false === $info['profile_image']->value ) :
											?>
											members__avatar--identicon<?php endif; ?>"
											<?php
											if ( $info['profile_image']->display && $info['profile_image']->value ) :
												?>
											style="background-image: url('<?php echo esc_url_raw( $avatar_url ); ?>')"<?php endif; ?> data-username="<?php echo esc_attr( $u->user_nicename ); ?>">
										</div>
										<div class="username">
											<p class="group__admin-username"><?php echo esc_html( "@{$u->user_nicename}" ); ?></>
											<p class="group__admin-name">
												<?php
												if ( $info['first_name']->display && $info['first_name']->value ) :
													echo esc_html( $info['first_name']->value );
													?>
													<?php endif; ?>
												<?php
												if ( $info['last_name']->display && $info['last_name']->value ) :
													echo esc_html( $info['last_name']->value );
													?>
													<?php endif; ?>
											</p>
										</div>
									</a>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
							<?php if ( isset( $group_meta['group_language'] ) && strlen( $group_meta['group_language'] ) > 0 && array_key_exists( strtolower( $group_meta['group_language'] ), $languages ) ) : ?>
						<div class="group__card">
							<div class="group__card-content group__card-content--small">
								<div>
									<p class="group__card-content__subtitle"><?php esc_html_e( 'Preferred Language', 'community-portal' ); ?></p>
								</div>
								<div class="group__language">
									<a href="<?php echo esc_url_raw( add_query_arg( array( 'language' => strtolower( $group_meta['group_language'] ) ), get_home_url( null, 'groups' ) ) ); ?>" class="group__language-link"><?php echo esc_html( $languages[ strtolower( $group_meta['group_language'] ) ] ); ?></a>
								</div>
							</div>
						</div>
						<?php endif; ?>
						<?php $group_tags = isset( $group_meta['group_tags'] ) ? array_unique( array_filter( $group_meta['group_tags'], 'mozilla_filter_inactive_tags' ) ) : false; ?>
						<?php if ( isset( $group_tags ) && is_array( $group_tags ) && count( $group_tags ) > 0 ) : ?>
						<div class="group__card">
							<div class="group__card-content group__card-content--small">
								<div>
									<p class="group__card-content__subtitle"><?php esc_html_e( 'Tags', 'community-portal' ); ?></p>
								</div>
								<div class="group__tags">
									<?php foreach ( $group_tags as $tag_loop ) : ?>

										<?php
										foreach ( $tags as $t ) {
											$found = false;
											if ( $current_translation ) {
												$temp_slug = $t->slug;
												if ( false !== stripos( $temp_slug, '_' ) ) {
													$temp_slug = substr( $temp_slug, 0, stripos( $temp_slug, '_' ) );
												}

												if ( $tag_loop === $temp_slug ) {
													$tag_name = $t->name;
													$found    = true;
													break;
												}
											} else {
												if ( $t->slug === $tag_loop ) {
													$temp_slug = $t->slug;
													$tag_name  = $t->name;
													$found     = true;
													break;
												}
											}
										}
										?>
										<?php if ( ! empty( $tag_name ) ) : ?>
											<a href="
											<?php
											if ( $current_translation ) :
												?>
												<?php echo esc_url_raw( "/{$current_translation}/" ); ?><?php endif; ?>/groups/?tag=<?php echo esc_attr( $tag_loop ); ?>" class="group__tag"><?php echo esc_html( $tag_name ); ?></a>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</section>
				<?php if ( isset( $options['report_email'] ) && is_user_logged_in() ) : ?>
				<div class="group__report-container">
						<?php
							$report_email = trim( sanitize_email( $options['report_email'] ) );
							$subject      = sprintf( '%s %s', __( 'Reporting Group', 'community-portal' ), $group->name );
						if ( ! empty( $_SERVER['HTTP_HOST'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {
							$server_host = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );
							$server_uri  = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
							$body        = __( 'Please provide a reason you are reporting this group', 'community-portal' ) . " https://{$server_host}{$server_uri}";
						}
						?>
						<a href="mailto:<?php echo esc_attr( $report_email ); ?>?subject=<?php echo esc_attr( $subject ); ?>&body=<?php echo esc_attr( $body ); ?>" class="group__report-group-link">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M12 8V12" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<circle cx="12" cy="16" r="0.5" fill="#CDCDD4" stroke="#0060DF"/>
						</svg>
						<?php esc_html_e( 'Report Group', 'community-portal' ); ?>
					</a>
				</div>
				<?php endif; ?>
				<?php if ( in_array( 'administrator', wp_get_current_user()->roles, true ) ) : ?>
				<a href="#" id="group-show-debug-info" class="group__show-debug-info">Show Meta Data</a>
				<div class="group__debug-info group__debug-info--hidden">
					<h3>Debug Information</h3>

					Discourse Group Information
					<pre>
						<?php print_r( $discourse_group ); ?>
					</pre>

					Group Meta
					<pre>
						<?php print_r( $group_meta ); ?>
					</pre>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div id="groups-share-lightbox" class="lightbox">
		<?php require locate_template( 'templates/share-modal.php', false, false ); ?>
	</div>

