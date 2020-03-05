<?php
// Mozilla theme functions file
$theme_directory = get_template_directory();

abstract class PrivacySettings {
    const REGISTERED_USERS = 0;
    const PUBLIC_USERS = 1; 
    const PRIVATE_USERS = 2;
}

// Include countries
include("{$theme_directory}/countries.php");

// Require
require_once("{$theme_directory}/lib/api.php");
require_once("{$theme_directory}/lib/campaigns.php");
require_once("{$theme_directory}/lib/groups.php");
require_once("{$theme_directory}/lib/members.php");
require_once("{$theme_directory}/lib/events.php");
require_once("{$theme_directory}/lib/utils.php");
require_once("{$theme_directory}/lib/campaigns.php");

// Native Wordpress Actions
add_action('init', 'mozilla_init');
add_action('admin_init', 'mozilla_redirect_admin');
add_action('get_header', 'mozilla_remove_admin_login_header');
add_action('wp_enqueue_scripts', 'mozilla_init_scripts');
add_action('admin_enqueue_scripts', 'mozilla_init_admin_scripts');
add_action('admin_menu', 'mozilla_add_menu_item');

add_action('bp_group_admin_edit_after', 'mozilla_save_group');
add_action('save_post', 'mozilla_save_post', 10, 3);

add_action('transition_post_status', 'mozilla_post_status_transition', 10, 3);


// Ajax Calls
add_action('wp_ajax_nopriv_upload_group_image', 'mozilla_upload_image');
add_action('wp_ajax_upload_group_image', 'mozilla_upload_image');
add_action('wp_ajax_join_group', 'mozilla_join_group');
add_action('wp_ajax_nopriv_join_group', 'mozilla_join_group');
add_action('wp_ajax_leave_group', 'mozilla_leave_group');
add_action('wp_ajax_nopriv_leave_group', 'mozilla_leave_group');
add_action('wp_ajax_get_users', 'mozilla_get_users');
add_action('wp_ajax_validate_email', 'mozilla_validate_email');
add_action('wp_ajax_nopriv_validate_group', 'mozilla_validate_group_name');
add_action('wp_ajax_validate_group', 'mozilla_validate_group_name');
add_action('wp_ajax_check_user', 'mozilla_validate_username');
add_action('wp_ajax_delete_user', 'mozilla_delete_user');
add_action('wp_ajax_mailchimp_unsubscribe', 'mozilla_mailchimp_unsubscribe');
add_action('wp_ajax_nopriv_mailchimp_unsubscribe', 'mozilla_mailchimp_unsubscribe');
add_action('wp_ajax_mailchimp_subscribe', 'mozilla_mailchimp_subscribe');
add_action('wp_ajax_nopriv_mailchimp_subscribe', 'mozilla_mailchimp_subscribe');


// Auth0 Actions
add_action('auth0_user_login', 'mozilla_post_user_creation', 10, 6);

// Buddypress Actions
add_action('bp_before_create_group_page', 'mozilla_create_group', 10, 1);
add_action('bp_before_edit_group_page', 'mozilla_edit_group', 10, 1);
add_action('bp_before_edit_member_page', 'mozilla_update_member', 10, 1);

add_action('groups_join_group', 'mozilla_add_members_discourse', 10, 2);
add_action('groups_remove_member', 'mozilla_remove_members_discourse', 10, 2);


// Remove Actions
remove_action('init', 'bp_nouveau_get_container_classes');
remove_action('em_event_save','bp_em_group_event_save', 1, 2);

// Filters
add_filter('nav_menu_link_attributes', 'mozilla_add_menu_attrs', 10, 3);
add_filter('nav_menu_css_class', 'mozilla_menu_class', 10, 4);
add_filter('em_get_countries', 'mozilla_add_online_to_countries', 10, 1);
add_filter('em_location_get_countries', 'mozilla_add_online_to_countries', 10, 1);
add_filter('em_booking_save_pre','mozilla_approve_booking', 100, 2);
add_filter('em_bookings_deleted', 'mozilla_remove_booking', 100, 2);

add_filter('em_event_submission_login', "mozilla_update_events_copy", 10, 1);
add_filter('wp_redirect', 'mozilla_events_redirect');
add_filter('em_event_delete', 'mozilla_delete_events', 10, 2);
add_filter('body_class', 'mozilla_update_body_class');
add_filter('acf/load_field/name=featured_group', 'acf_load_bp_groups', 10, 1);
add_filter('query_vars', 'mozilla_add_query_vars_filter');
add_filter('bp_groups_list_table_get_columns', 'mozilla_add_group_columns');
add_filter('bp_groups_admin_get_group_custom_column', 'mozilla_group_addional_column_info', 10, 3);



// Include theme style.css file not in admin page
if(!is_admin()) {
    wp_enqueue_style('style', get_stylesheet_uri());
}

function mozilla_init() {
    register_nav_menu('mozilla-theme-menu', __('Mozilla Custom Theme Menu'));
    register_taxonomy_for_object_type('category', 'page'); 

    $user = wp_get_current_user()->data;
    // Not logged in
    if(!isset($user->ID)) {
        if(isset($_GET['redirect_to'])) {
            setcookie("mozilla-redirect", $_GET['redirect_to'], 0, "/");
        }

        if(stripos($_SERVER['REQUEST_URI'], "/groups/create/step/group-details/") !== false) {
            setcookie("mozilla-redirect", "/groups/create/step/group-details/", 0, "/");
            wp_redirect('/wp-login.php?action=login');
            die();
        }

    }

    // Static Page
    $labels = Array(
        'name'              =>  __('Static Pages'),
        'singular_name'     =>  __('Static Page')
    );

    $args = Array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-format-aside',
        'rewrite'            =>  Array('slug'    =>  'p'),
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'taxonomies'         => array('post_tag')
    );

    register_post_type('static-page', $args);

    
    // Create Activities
    $labels = Array(
        'name'              =>  __('Activities'),
        'singular_name'     =>  __('Activity')
    );

    $args = Array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-chart-line',
        'rewrite'            => Array('slug'    =>  'activities'),
        'supports'           => Array('title', 'editor', 'thumbnail', 'excerpt'),
        'taxonomies'         => Array('post_tag')
    );

    register_post_type('activity', $args);

    // Create Campaigns
    $labels = Array(
        'name'              =>  __('Campaigns'),
        'singular_name'     =>  __('Campaign')
    );

    $args = Array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-admin-site-alt3',
        'rewrite'            =>  Array('slug'    =>  'campaigns'),
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'taxonomies'         => Array('post_tag')
    );

    register_post_type('campaign', $args);
    add_theme_support('post-thumbnails', array( 'post', 'activity', 'campaign', 'static-page')); 
}


?>
