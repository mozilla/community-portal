<?php
// Mozilla theme functions file

// Remove the admin header styles for homepage
add_action('get_header', 'remove_admin_login_header');

// Add custom theme menu
add_action('init', 'mozilla_custom_menu');


// Filters
add_filter('nav_menu_link_attributes', 'mozilla_add_menu_attrs', 10, 3);

// Include theme style.css file not in admin page
if(!is_admin()) 
    wp_enqueue_style('style', get_stylesheet_uri());

function remove_admin_login_header() {
	remove_action('wp_head', '_admin_bar_bump_cb');
}

function mozilla_custom_menu() {
    register_nav_menu('mozilla-theme-menu', __('Mozilla Custom Theme Menu'));
}

function mozilla_add_menu_attrs($attrs, $item, $args) {
    $attrs['class'] = 'menu-item__link';
    return $attrs;
}