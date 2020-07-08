<?php
/**
 * People listing page
 *
 * People listing page for community portal
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

get_header();
$logged_in           = mozilla_is_logged_in();
$live_user           = wp_get_current_user()->data;
$current_translation = mozilla_get_current_translation();

$template_dir = get_template_directory();
require "{$template_dir}/languages.php";
require "{$theme_directory}/countries.php";

$members_per_page = 20;
$current_page     = isset( $_GET['page'] ) ? intval( $_GET['page'] ) : 0;

$offset = ( $current_page - 1 ) * $members_per_page;

if ( $offset < 0 ) {
	$offset = 0;
}

$args = array(
	'offset' => 0,
	'number' => -1,
);

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

$members           = $wp_user_query->get_results();
$filtered_members  = array();
$used_country_list = array();
$used_languages    = array();

// Time to filter stuff!
foreach ( $members as $index => $member ) {

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

		$key = array_search( $member_country, $countries, true );
		if ( $key ) {
			$used_country_list[ $key ] = $countries[ $key ];
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
			strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
			strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
				in_array( $get_tag, array_map( 'strtolower', $member_tags ), true ) &&
				$info['last_name']->display &&
				stripos( $info['last_name']->value, $last_name ) !== false ) {
					$filtered_members[] = $member;
					continue;
			}
		} else {
			if ( $info['tags']->display && $info['location']->display &&
				array_key_exists( $country_code, $countries ) &&
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
			strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) ) {
				$filtered_members[] = $member;
				continue;
		}


		continue;
	}


	// Search / location / language!
	if ( $search_user && false === $get_tag && $country_code && $language_code ) {
		if ( $info['language']->display &&
			$info['location']->display &&
			array_key_exists( $country_code, $countries ) &&
			strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
			is_array( $info['languages']->value ) &&
			in_array( $language_code, $info['languages']->value, true ) &&
			stripos( false !== $member->data->user_nicename, $search_user ) ) {
				$filtered_members[] = $member;
				continue;
		}

		// Country / First Name / Language!
		if ( $first_name ) {
			if ( $info['location']->display &&
				array_key_exists( $country_code, $countries ) &&
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
			strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
			$info['location']->display &&
			stripos( $member->data->user_nicename, $search_user ) !== false ) {
			$filtered_members[] = $member;
			continue;
		}


		// Country and first name!
		if ( $first_name ) {
			if ( array_key_exists( $country_code, $countries ) &&
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
				$info['location']->display &&
				$info['first_name']->display &&
				stripos( $info['first_name']->value, $first_name ) !== false ) {
				$filtered_members[] = $member;
				continue;
			}
		} else {
			if ( array_key_exists( $country_code, $countries ) &&
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
				$info['location']->display &&
				$info['first_name']->display &&
				stripos( $info['last_name']->value, $last_name ) !== false ) {
				$filtered_members[] = $member;
				continue;
			}
		} else {
			if ( array_key_exists( $country_code, $countries ) &&
				strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
			strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
			strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) &&
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
			strtolower( $countries[ $country_code ] ) === strtolower( $member_country ) ) {
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

if ( $offset >= count( $filtered_members ) ) {
	$offset = count( $filtered_members ) - $members_per_page;
}

$tags    = get_tags( array( 'hide_empty' => false ) );
$members = array_slice( $filtered_members, $offset, $members_per_page );

$total_pages = ceil( count( $filtered_members ) / $members_per_page );

?>
<div class="content">
	<div class="members">
		<div class="members__hero">
			<div class="members__hero-container">
				<h1 class="members__title"><?php esc_html_e( 'People', 'community-portal' ); ?></h1>
				<p class="members__hero-copy">
					<?php esc_html_e( 'Ready to make it official? Set up a profile to attend events, join groups and manage your subscription settings. ', 'community-portal' ); ?>
				</p>
				<div class="members__search-container">
					<form method="GET" action="
					<?php
					if ( $current_translation ) :
						?>
						<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/" class="members__form" id="members-search-form">
						<div class="members__input-container">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M17.5 17.5L13.875 13.875" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<input type="hidden" value="<?php echo esc_html( $get_tag ); ?>" name="tag" id="user-tag" />
						<input type="hidden" value="<?php echo esc_html( $location ); ?>" name="country" id="user-location" />
						<input type="hidden" value="<?php echo esc_html( $get_language ); ?>" name="language" id="user-language" />
						<input type="text" name="u" id="members-search" class="members__search-input" placeholder="<?php esc_attr_e( 'Search people', 'community-portal' ); ?>" value="<?php echo esc_html( $search_user ); ?>" />
						</div>
						<input type="submit" class="members__search-cta" value="<?php esc_attr_e( 'Search', 'community-portal' ); ?>" />
					</form>
				</div>
			</div>
		</div>
		<div class="members__container">
			<div class="members__filter-container members__filter-container--hidden">
				<span><?php esc_attr_e( 'Search criteria:', 'community-portal' ); ?></span>
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
								if( false !== stripos( $loop_tag->slug, '_' ) ) {
									$loop_tag->slug = substr( $loop_tag->slug, 0, stripos( $loop_tag->slug, '_' ) );
								}
							?>
						<option value="<?php echo esc_attr( $loop_tag->slug ); ?>" 
												<?php
												if ( isset( $_GET['tag'] ) && strtolower( trim( $get_tag ) ) === strtolower( $loop_tag->slug ) ) :
													?>
							selected<?php endif; ?>><?php echo esc_html( $loop_tag->name ); ?></option>
						<?php endforeach; ?>
					</select>  
				</div>
				</div>
			<div class="members__show-filters-container">
				<a href="#" class="members__toggle-filter members__toggle-filter--show">
					<span class="filters__show"><?php esc_html_e( 'Show Filters', 'community-portal' ); ?></span>
					<span class="filters__hide"><?php esc_html_e( 'Hide Filters', 'community-portal' ); ?></span>
				</a>
			</div>
			<div class="members__people-container">
			<?php if ( count( $members ) > 0 ) : ?>
				<?php
				if ( isset( $_GET['u'] ) && strlen( $search_user ) > 0 ) :
					?>
					<div class="members__results-for">
					<?php
					esc_html_e( 'Results for ', 'community-portal' );
					echo sprintf( '"%s"', esc_html( sanitize_user( $search_user ) ) );
					?>
					</div><?php endif; ?>
				<?php foreach ( $members as $member ) : ?>
					<?php
					$info = $member->info;

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
						<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/<?php echo esc_attr( $member->data->user_nicename ); ?>" class="members__member-card">
				<div class="members__avatar
					<?php
					if ( false === $info['profile_image']->display || false === $info['profile_image']->value ) :
						?>
					members__avatar--identicon<?php endif; ?>" 
					<?php
					if ( $info['profile_image']->display && $info['profile_image']->value ) :
						?>
					style="background-image: url('<?php echo esc_url( $avatar_url ); ?>')"<?php endif; ?> data-username="<?php echo esc_attr( $member->data->user_nicename ); ?>">
				</div>
				<div class="members__member-info">
					<div class="members__username"><?php echo esc_html( $member->data->user_nicename ); ?></div>
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
					<?php if ( $info['location']->display && $info['location']->value ) : ?>
					<div class="members__location">
						<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>&nbsp;
						<?php echo esc_html( $info['location']->value ); ?>
					</div>
					<?php endif; ?>
				</div>
			</a>
			<?php endforeach; ?>
			</div>
			<?php else : ?>
				<h2 class="members__title--no-members-found"><?php esc_html_e( 'No members found', 'community-portal' ); ?></h2>
			<?php endif; ?>
			<?php
				$range = ( $current_page > 3 ) ? 3 : 5;

			if ( $current_page > $total_pages - 2 ) {
				$range = 5;
			}

				$previous_page = ( $current_page > 1 ) ? $current_page - 1 : 1;
				$next_page     = ( $current_page < $total_pages ) ? $current_page + 1 : $total_pages;

			if ( $total_pages > 1 ) {
				$range_min = ( 0 === $range % 2 ) ? ( $range / 2 ) - 1 : ( $range - 1 ) / 2;
				$range_max = ( 0 === $range % 2 ) ? $range_min + 1 : $range_min;

				$current_page_min = $current_page - $range_min;
				$current_page_max = $current_page + $range_max;

				$current_page_min = ( $current_page_min < 1 ) ? 1 : $current_page_min;
				$current_page_max = ( $current_page_max < ( $current_page_min + $range - 1 ) ) ? $current_page_min + $range - 1 : $current_page_max;

				if ( $current_page_max > $total_pages ) {
					$current_page_min = ( $current_page_min > 1 ) ? $total_pages - $range + 1 : 1;
					$current_page_max = $total_pages;
				}

				if ( $current_page_min < 0 ) {
					$current_page_min = 1;
				}

				if ( $current_page < 1 ) {
					$current_page = 1;
				}

				if ( $current_page > $current_page_max ) {
					$current_page = intval( $current_page_max );
				}
			}

			?>
			<div class="members__pagination">
				<div class="members__pagination-container">
					<?php if ( $total_pages > 1 ) : ?>
					<a href="
						<?php
						if ( $current_translation ) :
							?>
							<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/?page=<?php echo esc_attr( $previous_page ); ?>
													<?php
													if ( $search_user ) :
														?>
						&u=<?php echo esc_attr( $search_user ); ?><?php endif; ?>
						<?php
						if ( isset( $_GET['country'] ) ) :
							?>
						&country=<?php echo esc_attr( $location ); ?><?php endif; ?>
						<?php
						if ( isset( $_GET['tag'] ) ) :
							?>
	&tag=<?php echo esc_attr( $get_tag ); ?><?php endif; ?>
						<?php
						if ( isset( $_GET['language'] ) ) :
							?>
	&language=<?php echo esc_attr( $get_language ); ?><?php endif; ?>" class="members__pagination-link">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<path d="M17 23L6 12L17 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
						<?php
						if ( $current_page_min > 1 ) :
							?>
							<a href="
							<?php
							if ( $current_translation ) :
								?>
								<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/?page=1
							<?php
							if ( $search_user ) :
								?>
							&u=<?php echo esc_attr( $search_user ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['country'] ) ) :
								?>
	&country=<?php echo esc_attr( $location ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['tag'] ) ) :
								?>
	&tag=<?php echo esc_attr( $get_tag ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['language'] ) ) :
								?>
	&language=<?php echo esc_attr( $get_language ); ?><?php endif; ?>" class="members__pagination-link members__pagination-link--first"><?php print '1'; ?></a>&hellip; <?php endif; ?>
						<?php for ( $x = $current_page_min - 1; $x < $current_page_max; $x++ ) : ?>
					<a href="
							<?php
							if ( $current_translation ) :
								?>
								<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/?page=<?php echo esc_attr( $x + 1 ); ?>
													<?php
													if ( $search_user ) :
														?>
						&u=<?php echo esc_attr( $search_user ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['country'] ) ) :
								?>
						&country=<?php echo esc_attr( $location ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['tag'] ) ) :
								?>
	&tag=<?php echo esc_attr( $get_tag ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['language'] ) ) :
								?>
	&language=<?php echo esc_attr( $get_language ); ?><?php endif; ?>" class="members__pagination-link
							<?php
							if ( $current_page === $x + 1 ) :
								?>
	members__pagination-link--active<?php endif; ?>
							<?php
							if ( $x === $current_page_max - 1 ) :
								?>
	members__pagination-link--last<?php endif; ?>"><?php echo esc_attr( $x + 1 ); ?></a>
					<?php endfor; ?>
						<?php
						if ( $total_pages > $range && $current_page < $total_pages - 1 ) :
							?>
							&hellip; <a href="
							<?php
							if ( $current_translation ) :
								?>
								<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/?page=<?php echo esc_attr( $total_pages ); ?>
							<?php
							if ( $search_user ) :
								?>
							&u=<?php echo esc_attr( $search_user ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['country'] ) ) :
								?>
	&country=<?php echo esc_attr( $location ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['tag'] ) ) :
								?>
	&tag=<?php echo esc_attr( $get_tag ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['language'] ) ) :
								?>
	&language=<?php echo esc_attr( $get_language ); ?><?php endif; ?>" class="members__pagination-link
							<?php
							if ( $current_page === $total_pages ) :
								?>
	members__pagination-link--active<?php endif; ?>"><?php echo esc_attr( $total_pages ); ?></a><?php endif; ?>
					<a href="
						<?php
						if ( $current_translation ) :
							?>
							<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/?page=<?php echo esc_attr( $next_page ); ?>
													<?php
													if ( $search_user ) :
														?>
						&u=<?php echo esc_attr( $search_user ); ?><?php endif; ?>
						<?php
						if ( isset( $_GET['country'] ) ) :
							?>
						&country=<?php echo esc_attr( $location ); ?><?php endif; ?>
						<?php
						if ( isset( $_GET['tag'] ) ) :
							?>
	&tag=<?php echo esc_attr( $get_tag ); ?><?php endif; ?>
						<?php
						if ( isset( $_GET['language'] ) ) :
							?>
	&language=<?php echo esc_attr( $get_language ); ?><?php endif; ?>" class="members__pagination-link">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<path d="M7 23L18 12L7 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php


	get_footer();
?>
