<?php
// Mozilla theme functions file

// Remove the admin header styles for homepage
add_action('get_header', 'remove_admin_login_header');

// Native Wordpress Actions
add_action('init', 'mozilla_custom_menu');
add_action('wp_enqueue_scripts', 'mozilla_init_scripts');
add_action('wp_ajax_nopriv_upload_group_image', 'mozilla_upload_image');
add_action('wp_ajax_upload_group_image', 'mozilla_upload_image');

add_action('wp_ajax_validate_group', 'mozilla_validate_group_name');


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
    wp_enqueue_script('cleavejs', get_stylesheet_directory_uri()."/js/vendor/cleave.min.js", array());
}

// If the create group page is called create a group 
function mozilla_create_group() {

    if(is_user_logged_in()) {
        $required = Array(
            'group_name',
            'group_type',
            'group_desc',
            'group_city',
            'group_address',
            'group_country',
            'my_nonce_field'
        );


        $optional = Array(
            'image_url',
            'group_address_type',
            'group_address',
            'group_meeting_details',
            'group_discourse',
            'group_facebook',
            'group_telegram',
            'group_github',
            'group_twitter',
            'group_other'
        );

        // If we're posting data lets create a group
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(isset($_POST['step']) && isset($_POST['my_nonce_field']) && wp_verify_nonce($_REQUEST['my_nonce_field'], 'protect_content')) {
                switch($_POST['step']) {
                    case '1':
                        // Gather information
                        $error = false;
                        foreach($required AS $field) {
                            if(isset($_POST[$field])) {
                                if($_POST[$field] === "" || $_POST[$field] === 0) {
                                    $error = true;
                                }

                            }
                        }
  
                        $_SESSION['form'] = $_POST;

                        // Cleanup
                        if($error) {
                            if(isset($_SESSION['uploaded_file']) && file_exists($_SESSION['uploaded_file'])) {
                                $image = getimagesize($_SESSION['uploaded_file']);
                                if(isset($image[2]) && in_array($image[2], Array(IMAGETYPE_JPEG ,IMAGETYPE_PNG))) {
                                    unlink($_SESSION['uploaded_file']);
                                }
                            }

                            $_POST['step'] = 0;                           
                        }
                        
                        break;
                    case 2:

                        if(isset($_POST['agree']) && $_POST['agree']) {
                            $args = Array(
                                'group_id'  =>  0,
                            );
                            
                            $args['name'] = $_POST['group_name'];
                            $args['description'] = $_POST['group_desc'];
                            $args['status'] = 'private';
                            
                            $group_id = groups_create_group($args);
                            $meta = Array();

                            if($group_id) {

                                // Loop through optional fields and save to meta
                                foreach($optional AS $field) {
                                    if(isset($_POST[$field]) && $_POST[$field] !== "") {
                                        $meta[$field] = trim($_POST[$field]);
                                    }
                                }

                                // Required information but needs to be stored in meta data because buddypress does not support these fields
                                $meta['group_image_url'] = trim($_POST['image_url']);
                                $meta['group_city'] = trim($_POST['group_city']);
                                $meta['group_address'] = trim($_POST['group_address']);
                                $meta['group_country'] = trim($_POST['group_country']);
                                $meta['group_type'] = trim($_POST['group_type']);
                       
                                if(isset($_POST['tags'])) {
                                    $tags = explode(',', $_POST['tags']);
                                    $meta['group_tags'] = array_filter($tags);
                                }

                                $result = groups_update_groupmeta($group_id, 'meta', $meta);
                                // Could not update group information so reset form
                                if($result) {
                                    unset($_SESSION['form']);
                                    $_POST = Array();
                                    $_POST['step'] = 3;
                                } else {
                                    groups_delete_group($group_id);
                                    $_POST['step'] = 0;
                                }                                
                            }
                        } else {
                            $_POST['step'] = 2;
                        }

                        break;
                        
                }
            }
        } else {
            unset($_SESSION['form']);
        }
    } else {
        wp_redirect("/");
    }
}

function mozilla_upload_image() {

    if(!empty($_FILES) && wp_verify_nonce($_REQUEST['my_nonce_field'], 'protect_content')) {
        $image = getimagesize($_FILES['file']['tmp_name']);

        if(isset($image[2]) && in_array($image[2], Array(IMAGETYPE_JPEG ,IMAGETYPE_PNG))) {
            $uploaded_bits = wp_upload_bits($_FILES['file']['name'], null, file_get_contents($_FILES['file']['tmp_name']));
            
            if (false !== $uploaded_bits['error']) {
                
            } else {
                $uploaded_file     = $uploaded_bits['file'];
                $_SESSION['uploaded_file'] = $uploaded_bits['file'];
                $uploaded_url      = $uploaded_bits['url'];
                $uploaded_filetype = wp_check_filetype(basename($uploaded_bits['file'] ), null);
        
                print $uploaded_url;
            }
        }
    }
	die();
}

function mozilla_validate_group_name() {
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['q'])) {
            $query = $_GET['q'];
            $group = mozilla_search_groups($query);
            var_dump($group);
            die();
        }
    }
}

function mozilla_search_groups($name) {
    $groups = groups_get_groups();
    $group_array = $groups['groups'];

    $group = array_filter($groups, function($object) {
        return trim(strtolower($object->name)) === trim(strtolower($name));
    });

    return $group;
}