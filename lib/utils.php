<?php

function mozilla_upload_image() {

    if(!empty($_FILES) && wp_verify_nonce($_REQUEST['my_nonce_field'], 'protect_content')) {

        if(isset($_FILES['file']) && isset($_FILES['file']['tmp_name'])) {
            $image = getimagesize($_FILES['file']['tmp_name']);
            $image_file = $_FILES['file']['tmp_name'];
            $file_size = filesize($image_file);
    
            $file_size_kb =  number_format($file_size / 1024, 2);
            $options = wp_load_alloptions();
            $max_files_size_allowed = isset($options['image_max_filesize']) && intval($options['image_max_filesize']) > 0 ? intval($options['image_max_filesize']) : 500;

            if($file_size_kb <= $max_files_size_allowed) {
                if(isset($image[2]) && in_array($image[2], Array(IMAGETYPE_JPEG ,IMAGETYPE_PNG))) {
                    $uploaded_bits = wp_upload_bits($_FILES['file']['name'], null, file_get_contents($image_file));
                    
                    if (false !== $uploaded_bits['error']) {
                        
                    } else {
                        $uploaded_file     = $uploaded_bits['file'];
                        $_SESSION['uploaded_file'] = $uploaded_bits['file'];
        
                        $uploaded_url      = $uploaded_bits['url'];
                        $uploaded_filetype = wp_check_filetype(basename($uploaded_bits['file']), null);
                        
                        if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                            $uploaded_url = preg_replace("/^http:/i", "https:", $uploaded_url);
                        }
                        
                        if(isset($_REQUEST['profile_image']) && $_REQUEST['profile_image'] == 'true') {
                            // Image size check
                            if(isset($image[0]) && isset($image[1])) {
                                if($image[0] >= 175 && $image[1] >= 175) {
                                    print trim(str_replace("\n", "", $uploaded_url));
                                } else {
                                    print "Image size is too small";
                                    unlink($uploaded_bits['file']);
                                }
                            } else {
                                print "Invalid image provided"; 
                                unlink($uploaded_bits['file']);
                            }
                        } elseif(isset($_REQUEST['group_image']) && $_REQUEST['group_image'] == 'true' || isset($_REQUEST['event_image']) && $_REQUEST['event_image'] == 'true') {
                            if(isset($image[0]) && isset($image[1])) {
                                if($image[0] >= 703 && $image[1] >= 400) {
                                    print trim(str_replace("\n", "", $uploaded_url));
                                } else {
                                    print "Image size is too small";
                                    unlink($uploaded_bits['file']);
                                }
                            } else {
                                print "Invalid image provided"; 
                                unlink($uploaded_bits['file']);
                            }
                        }  else {
                            print trim(str_replace("\n", "", $uploaded_url));
                            unlink($uploaded_bits['file']);
                        }
                    }
                }
            } else {
                print "Image size to large ({$max_files_size_allowed} KB maximum)";
            }
            
        }
    }
	die();
}

function mozilla_determine_site_section() {
    $path_items = array_filter(explode('/', $_SERVER['REQUEST_URI']));

    if(sizeof($path_items) > 0) {
        $section = array_shift(array_values($path_items));
        return $section;
    }

    return false;
}

function mozilla_add_menu_attrs($attrs, $item, $args) {
    $attrs['class'] = 'menu-item__link';
    return $attrs;
}

function mozilla_init_scripts() {
    // Vendor scripts
    wp_enqueue_script('dropzonejs', get_stylesheet_directory_uri()."/js/vendor/dropzone.min.js", array('jquery'));
    wp_enqueue_script('autcomplete', get_stylesheet_directory_uri()."/js/vendor/autocomplete.js", array('jquery'));
    wp_enqueue_script('identicon', get_stylesheet_directory_uri()."/js/vendor/identicon.js", array());
    wp_register_script('mapbox', "https://api.mapbox.com/mapbox-gl-js/v1.4.1/mapbox-gl.js");
    wp_enqueue_script('mapbox');
    wp_register_style('mapbox-css', 'https://api.mapbox.com/mapbox-gl-js/v1.4.1/mapbox-gl.css');
    wp_enqueue_style('mapbox-css');

    // Custom scripts
    wp_enqueue_script('groups', get_stylesheet_directory_uri()."/js/groups.js", array('jquery'));
    wp_enqueue_script('events', get_stylesheet_directory_uri()."/js/events.js", array('jquery'));
    wp_enqueue_script('activities', get_stylesheet_directory_uri()."/js/activities.js", array('jquery'));
    wp_enqueue_script('cleavejs', get_stylesheet_directory_uri()."/js/vendor/cleave.min.js", array());
    wp_enqueue_script('nav', get_stylesheet_directory_uri()."/js/nav.js", array('jquery'));
    wp_enqueue_script('profile', get_stylesheet_directory_uri()."/js/profile.js", array('jquery'));
    wp_enqueue_script('lightbox', get_stylesheet_directory_uri()."/js/lightbox.js", array('jquery'));
    wp_enqueue_script('gdpr', get_stylesheet_directory_uri()."/js/gdpr.js", array('jquery'));
}

function mozilla_init_admin_scripts() {
    $screen = get_current_screen();

    if(strtolower($screen->id) === 'toplevel_page_bp-groups') {
        wp_enqueue_script('groups', get_stylesheet_directory_uri()."/js/admin.js", array('jquery'));
    }
}

function mozilla_remove_admin_login_header() {
	remove_action('wp_head', '_admin_bar_bump_cb');
}

function mozilla_theme_settings() {
    $theme_dir = get_template_directory();

    if(current_user_can('manage_options') && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['admin_nonce_field']) && wp_verify_nonce($_REQUEST['admin_nonce_field'], 'protect_content')) {
            if(isset($_POST['github_link'])) {
                update_option('github_link', sanitize_text_field($_POST['github_link']));
            }

            if(isset($_POST['community_discourse'])) {
                update_option('community_discourse', sanitize_text_field($_POST['community_discourse']));
            }

            if(isset($_POST['google_analytics_id'])) {
                update_option('google_analytics_id', sanitize_text_field($_POST['google_analytics_id']));
            }

            if(isset($_POST['default_open_graph_title'])) {
                update_option('default_open_graph_title', sanitize_text_field($_POST['default_open_graph_title']));
            }

            if(isset($_POST['default_open_graph_desc'])) {
                update_option('default_open_graph_desc', sanitize_text_field($_POST['default_open_graph_desc']));
            }            

            if(isset($_POST['image_max_filesize'])) {
                update_option('image_max_filesize', sanitize_text_field(intval($_POST['image_max_filesize'])));
            }            

            if(isset($_POST['error_404_title'])) {
                update_option('error_404_title', sanitize_text_field($_POST['error_404_title']));
            }   

            if(isset($_POST['error_404_copy'])) {
                update_option('error_404_copy', sanitize_text_field($_POST['error_404_copy']));
            }   

            if(isset($_POST['discourse_api_key'])) {
                update_option('discourse_api_key', sanitize_text_field($_POST['discourse_api_key']));
            }   

            if(isset($_POST['discourse_api_url'])) {
                update_option('discourse_api_url', sanitize_text_field($_POST['discourse_api_url']));
            }   

            if(isset($_POST['discourse_url'])) {
                update_option('discourse_url', sanitize_text_field($_POST['discourse_url']));
            }   

            if(isset($_POST['mapbox'])) {
                update_option('mapbox', sanitize_text_field($_POST['mapbox']));
            }   
        }
    }

    $options = wp_load_alloptions();
    include "{$theme_dir}/templates/settings.php";

}

function mozilla_add_menu_item() {
    add_menu_page('Mozilla Settings', 'Mozilla Settings', 'manage_options', 'theme-panel', 'mozilla_theme_settings', null, 99);
}

function mozilla_is_site_admin(){
    return in_array('administrator',  wp_get_current_user()->roles);
}

function mozilla_update_body_class( $classes ) {
    $classes[] = "body";
    return $classes; 
}

function mozilla_menu_class($classes, $item, $args) {

    $path_items = array_filter(explode('/', $_SERVER['REQUEST_URI']));
    $menu_url = strtolower(str_replace('/', '', $item->url));
    
    if(sizeof($path_items) > 0) {
        
        if(strtolower($path_items[1]) === $menu_url) {
            $item->current = true;
            $classes[] = 'menu-item--active';
        }
    }

    return $classes;
}

function mozilla_add_query_vars_filter($vars) {
    $vars[] = "view";
    $vars[] = "country";
    $vars[] = "tag";
    $vars[] = "a";

    return $vars;
}

function mozilla_match_categories() {
    $cat_terms = get_terms(EM_TAXONOMY_CATEGORY, array('hide_empty'=>false));
    $wp_terms = get_terms('post_tag', array('hide_empty'=>false));

    $cat_terms_name = array_map(function($n) {
        return $n->name;
    }, $cat_terms);

    $wp_terms = array_map(function($n) {
        return $n->name;
    }, $wp_terms);

    foreach ($wp_terms as $wp_term) {
        if (!in_array($wp_term, $cat_terms_name)) {
            wp_insert_term($wp_term, EM_TAXONOMY_CATEGORY);
        }
    }

    foreach ($cat_terms as $cat_term) {
        if (!in_array($cat_term->name, $wp_terms)) {
            wp_delete_term($cat_term->term_id, EM_TAXONOMY_CATEGORY);
        }
    }
}


function mozilla_redirect_admin() {
    if((!current_user_can('manage_options') || current_user_can('subscriber'))  && '/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF']) {
        wp_redirect("/");
        die();
    }
}

function mozilla_verify_url($url) {
	if (!preg_match('/http(s?)\:\/\//i', $url)):
		$url = 'http://'.$url;
		return $url;
	endif;
	return $url;
}

?>