<?php
/**
 * Functions
 *
 * Theme functions file
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

$theme_directory = get_template_directory();

// Include countries.
require "{$theme_directory}/countries.php";

// Require.
require_once "{$theme_directory}/lib/api.php";
require_once "{$theme_directory}/lib/campaigns.php";
require_once "{$theme_directory}/lib/groups.php";
require_once "{$theme_directory}/lib/members.php";
require_once "{$theme_directory}/lib/events.php";
require_once "{$theme_directory}/lib/utils.php";
require_once "{$theme_directory}/lib/newsletter.php";
require_once "{$theme_directory}/lib/campaigns.php";
require_once "{$theme_directory}/lib/activities.php";
require_once "{$theme_directory}/lib/language.php";

require $theme_directory . '/inc/gutenberg-customize.php';

// Native WordPress Actions.
add_action( 'init', 'mozilla_init' );
add_action( 'admin_init', 'mozilla_redirect_admin' );
add_action( 'get_header', 'mozilla_remove_admin_login_header' );
add_action( 'wp_enqueue_scripts', 'mozilla_init_scripts' );
add_action( 'wp_enqueue_scripts', 'mozilla_init_fe_styles' );
add_action( 'admin_enqueue_scripts', 'mozilla_init_admin_scripts' );
add_action( 'admin_menu', 'mozilla_add_menu_item' );

add_action( 'bp_group_admin_edit_after', 'mozilla_save_group' );
add_action( 'save_post', 'mozilla_save_post', 10, 3 );

add_action( 'acf/save_post', 'mozilla_acf_save_post', 10, 3 );

add_action( 'transition_post_status', 'mozilla_post_status_transition', 10, 3 );


add_action( 'bp_groups_admin_meta_boxes', 'mozilla_group_metabox' );


// Ajax Calls.
add_action( 'wp_ajax_nopriv_upload_group_image', 'mozilla_upload_image' );
add_action( 'wp_ajax_upload_group_image', 'mozilla_upload_image' );
add_action( 'wp_ajax_join_group', 'mozilla_join_group' );
add_action( 'wp_ajax_nopriv_join_group', 'mozilla_join_group' );
add_action( 'wp_ajax_leave_group', 'mozilla_leave_group' );
add_action( 'wp_ajax_nopriv_leave_group', 'mozilla_leave_group' );
add_action( 'wp_ajax_get_users', 'mozilla_get_users' );
add_action( 'wp_ajax_validate_email', 'mozilla_validate_email' );
add_action( 'wp_ajax_nopriv_validate_group', 'mozilla_validate_group_name' );
add_action( 'wp_ajax_validate_group', 'mozilla_validate_group_name' );
add_action( 'wp_ajax_check_user', 'mozilla_validate_username' );
add_action( 'wp_ajax_delete_user', 'mozilla_delete_user' );
add_action( 'wp_ajax_get_locations', 'mozilla_get_locations' );
add_action( 'wp_ajax_newsletter_subscribe', 'mozilla_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_newsletter_subscribe', 'mozilla_newsletter_subscribe' );
add_action( 'wp_ajax_mailchimp_unsubscribe', 'mozilla_mailchimp_unsubscribe' );
add_action( 'wp_ajax_nopriv_mailchimp_unsubscribe', 'mozilla_mailchimp_unsubscribe' );
add_action( 'wp_ajax_mailchimp_subscribe', 'mozilla_mailchimp_subscribe' );
add_action( 'wp_ajax_nopriv_mailchimp_subscribe', 'mozilla_mailchimp_subscribe' );
add_action( 'wp_ajax_export_users', 'mozilla_export_users' );
add_action( 'wp_ajax_update_group_discourse', 'mozilla_update_group_discourse_category_id' );
add_action( 'wp_ajax_nopriv_export_events', 'mozilla_event_export' );
add_action( 'wp_ajax_export_events', 'mozilla_event_export' );
add_action( 'wp_ajax_update_event_discourse', 'mozilla_update_event_discourse_data' );
add_action( 'wp_ajax_add_user_to_discourse_group', 'mozilla_add_user_discourse' );
add_action( 'wp_ajax_update_event_discourse', 'mozilla_update_event_discourse_data' );
add_action( 'wp_ajax_add_user_to_discourse_group', 'mozilla_add_user_discourse' );

add_action( 'wp_ajax_download_group_events', 'mozilla_download_group_events' );


add_action( 'wp_ajax_download_campaign_events', 'mozilla_download_campaign_events' );
add_action( 'add_meta_boxes', 'mozilla_campaign_metabox' );


add_action( 'wp_ajax_download_activity_events', 'mozilla_download_activity_events' );
add_action( 'add_meta_boxes', 'mozilla_activity_metabox' );

add_action( 'after_setup_theme', 'mozilla_theme_setup' );


// Auth0 Actions.
add_action( 'auth0_user_login', 'mozilla_post_user_creation', 10, 6 );

// Buddypress Actions.
add_action( 'bp_before_create_group_page', 'mozilla_create_group', 10, 1 );
add_action( 'groups_join_group', 'mozilla_add_members_discourse', 10, 2 );
add_action( 'groups_remove_member', 'mozilla_remove_members_discourse', 10, 2 );


// Remove Actions.
remove_action( 'init', 'bp_nouveau_get_container_classes' );
remove_action( 'em_event_save', 'bp_em_group_event_save', 1, 2 );
remove_action( 'media_buttons', 'media_buttons' );

// Filters.
add_filter( 'send_email_change_email', '__return_false' );
add_filter( 'nav_menu_link_attributes', 'mozilla_add_menu_attrs', 10, 3 );
add_filter( 'nav_menu_css_class', 'mozilla_menu_class', 10, 4 );
add_filter( 'em_get_countries', 'mozilla_add_online_to_countries', 10, 1 );
add_filter( 'em_location_get_countries', 'mozilla_add_online_to_countries', 10, 1 );
add_filter( 'em_booking_save_pre', 'mozilla_approve_booking', 100, 2 );
add_filter( 'em_bookings_deleted', 'mozilla_remove_booking', 100, 2 );

add_filter( 'em_event_submission_login', 'mozilla_update_events_copy', 10, 1 );
add_filter( 'wp_redirect', 'mozilla_events_redirect' );
add_filter( 'em_event_delete', 'mozilla_delete_events', 10, 2 );
add_filter( 'body_class', 'mozilla_update_body_class' );
add_filter( 'acf/load_field/name=featured_group', 'acf_load_bp_groups', 10, 1 );
add_filter( 'acf/fields/post_object/query/name=event', 'mozilla_query_all_events', 10, 3 );
add_filter( 'acf/fields/post_object/query/name=single_event', 'mozilla_query_all_events', 10, 3 );
add_filter( 'query_vars', 'mozilla_add_query_vars_filter' );
add_filter( 'bp_groups_list_table_get_columns', 'mozilla_add_group_columns' );
add_filter( 'bp_groups_admin_get_group_custom_column', 'mozilla_group_addional_column_info', 10, 3 );
add_filter( 'wp_nav_menu_objects', 'mozilla_hide_menu_emails', 10, 2 );
add_filter( 'script_loader_tag', 'mozilla_update_script_attributes', 10, 2 );
add_filter( 'allowed_block_types', 'mozilla_allowed_block_types' );

/**
 * Theme setup function
 */
function mozilla_theme_setup() {
	load_theme_textdomain( 'community-portal', get_template_directory() . '/languages' );
}



/**
 * Initialize theme
 */
function mozilla_init() {
	register_nav_menu( 'mozilla-theme-menu', __( 'Mozilla Custom Theme Menu' ) );
	register_taxonomy_for_object_type( 'category', 'page' );

	$user = wp_get_current_user()->data;
	// Not logged in.
	if ( ! isset( $user->ID ) ) {
		if ( isset( $_GET['redirect_to'] ) ) {
			$redirect_to = esc_url_raw( wp_unslash( $_GET['redirect_to'] ) );
			setcookie( 'mozilla-redirect', $redirect_to, 0, '/' );
		}

		if ( ! empty( $_SERVER['REQUEST_URI'] ) && false !== stripos( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/groups/create/step/group-details/' ) ) {
			setcookie( 'mozilla-redirect', '/groups/create/step/group-details/', 0, '/' );
			wp_safe_redirect( '/wp-login.php?action=login' );
			exit();
		}
	}

	// Static Page.
	$labels = array(
		'name'          => __( 'Static Pages' ),
		'singular_name' => __( 'Static Page' ),
	);

	$args = array(
		'labels'       => $labels,
		'public'       => true,
		'show_in_menu' => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-format-aside',
		'rewrite'      => array( 'slug' => 'p' ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'taxonomies'   => array( 'post_tag' ),
	);

	register_post_type( 'static-page', $args );

	// Create Activities.
	$labels = array(
		'name'          => __( 'Activities' ),
		'singular_name' => __( 'Activity' ),
	);

	$args = array(
		'labels'       => $labels,
		'public'       => true,
		'show_in_menu' => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-chart-line',
		'rewrite'      => array( 'slug' => 'activities' ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'taxonomies'   => array( 'post_tag' ),
	);

	register_post_type( 'activity', $args );

	// Create Campaigns.
	$labels = array(
		'name'          => __( 'Campaigns' ),
		'singular_name' => __( 'Campaign' ),
	);

	$args = array(
		'labels'       => $labels,
		'public'       => true,
		'show_in_menu' => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-admin-site-alt3',
		'rewrite'      => array( 'slug' => 'campaigns' ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'taxonomies'   => array( 'post_tag' ),
	);

	register_post_type( 'campaign', $args );
	add_theme_support( 'post-thumbnails', array( 'post', 'activity', 'campaign', 'static-page' ) );
}

/**
 * This should fix the edit-event page not avalaible.
 */
function force_page_rewrite_rule() {
	add_rewrite_rule( '(.?.+?)(?:/([0-9]+))?/?$', 'index.php?pagename=$matches[1]&page=$matches[2]', 'top' );
}
add_action( 'init', 'force_page_rewrite_rule', 10, 0 );

add_filter( 'wpml_sl_blacklist_requests', 'wpml_sl_blacklist_requests', 10, 2 );

/**
 * Stop WPML from rewriting Event URLs
 *
 * @param array $blacklist current list of blacklisted strings.
 * @param mixed $sitepress the current sitepress instance.
 */
function wpml_sl_blacklist_requests( $blacklist, $sitepress ) {
	$blacklist[] = '/events\/[a-zA-z]/';
	$blacklist[] = '/.+/';
	return $blacklist;
}

/**
 * Gutenberg is unused so don't enqueue assets
 */
function remove_wp_block_library_css() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
}
add_action( 'wp_enqueue_scripts', 'remove_wp_block_library_css', 100 );

// em-event.php 585: apply_filters( 'em_event_load_postdata', $this );.
/*
 * Force RSVP date as the end date
 */
add_filter( 'em_event_load_postdata', 'mozilla_force_rsvp_end', 10, 2 );
function mozilla_force_rsvp_end( $event ) {
	$event->event_rsvp_date = $event->event_end_date;
	return $event;
}
