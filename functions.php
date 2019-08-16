<?php
// Mozilla theme functions file

// Remove the admin header styles for homepage
add_action('get_header', 'remove_admin_login_header');

// Include theme style.css file 
wp_enqueue_style('style', get_stylesheet_uri());

function remove_admin_login_header() {
	remove_action('wp_head', '_admin_bar_bump_cb');
}