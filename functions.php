<?php
// Mozilla theme functions file

// Remove the admin header styles for homepage
add_action('get_header', 'remove_admin_login_header');

// Native Wordpress Actions
add_action('init', 'mozilla_custom_menu');
add_action('wp_enqueue_scripts', 'mozilla_init_scripts');
add_action('wp_ajax_nopriv_upload_group_image', 'mozilla_upload_image');
add_action('wp_ajax_upload_group_image', 'mozilla_upload_image');

// Buddypress Actions
add_action('bp_before_create_group_page', 'mozilla_create_group');


// Filters
add_filter('nav_menu_link_attributes', 'mozilla_add_menu_attrs', 10, 3);
add_filter('nav_menu_css_class', 'mozilla_add_active_page' , 10 , 2);

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

function mozilla_add_active_page($classes, $item) {

    $pagename = strtolower(get_query_var('pagename'));  
    if($pagename === strtolower($item->post_name)) {
        $classes[] = 'menu-item--active';
    }

    return $classes;
}

function mozilla_init_scripts() {
    wp_enqueue_script('dropzonejs', get_stylesheet_directory_uri()."/js/vendor/dropzone.min.js", array('jquery'));
    wp_enqueue_script('groups', get_stylesheet_directory_uri()."/js/groups.js", array('jquery'));
    
}

// If the create group page is called create a group 
function mozilla_create_group() {

    if(is_user_logged_in()) {
        // If we're posting data lets create a group
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        } else {
    
        }
    } else {
        wp_redirect("/");
    }
}

function mozilla_upload_image() {

    if(!empty($_FILES) && wp_verify_nonce($_REQUEST['my_nonce_field'], 'protect_content')) {
		$uploaded_bits = wp_upload_bits($_FILES['file']['name'], null, file_get_contents($_FILES['file']['tmp_name']));
    
		if (false !== $uploaded_bits['error']) {
            
		} else {
            $uploaded_file     = $uploaded_bits['file'];
            $uploaded_url      = $uploaded_bits['url'];
            $uploaded_filetype = wp_check_filetype(basename($uploaded_bits['file'] ), null);
    
            print $uploaded_url;
        }
    }
	die();
}
