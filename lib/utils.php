<?php

function mozilla_campaign_metabox() {
    add_meta_box(
        'campaign-export-events',       
        'Export Events',                  
        'mozilla_show_campaign_metabox',  
        'campaign',                 
        'side',
        'default'
    );
    
}

function mozilla_show_campaign_metabox($post) {
    print "<div><a href=\"/wp-admin/admin-ajax.php?action=download_campaign_events&campaign={$post->ID}\">Export events related to this campaign</a></div>";
}

function mozilla_activity_metabox() {
    add_meta_box(
        'activity-export-events',       
        'Export Events',                  
        'mozilla_show_activity_metabox',  
        'activity',                 
        'side',
        'default'
    );
    
}

function mozilla_show_activity_metabox($post) {
    print "<div><a href=\"/wp-admin/admin-ajax.php?action=download_activity_events&activity={$post->ID}\">Export events related to this activity</a></div>";
}


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
    wp_enqueue_script('campaigns', get_stylesheet_directory_uri()."/js/campaigns.js", array('jquery'));
    wp_enqueue_script('activities', get_stylesheet_directory_uri()."/js/activities.js", array('jquery'));
    wp_enqueue_script('cleavejs', get_stylesheet_directory_uri()."/js/vendor/cleave.min.js", array());
    wp_enqueue_script('nav', get_stylesheet_directory_uri()."/js/nav.js", array('jquery'));
    wp_enqueue_script('profile', get_stylesheet_directory_uri()."/js/profile.js", array('jquery'));
    wp_enqueue_script('lightbox', get_stylesheet_directory_uri()."/js/lightbox.js", array('jquery'));
    wp_enqueue_script('gdpr', get_stylesheet_directory_uri()."/js/gdpr.js", array('jquery'));
    wp_enqueue_script('dropzone', get_stylesheet_directory_uri()."/js/dropzone.js", array('jquery'));
    wp_enqueue_script('newsletter', get_stylesheet_directory_uri()."/js/newsletter.js", array('jquery'));
    wp_enqueue_script('mailchimp', get_stylesheet_directory_uri()."/js/campaigns.js", array('jquery'));
}

function mozilla_init_admin_scripts() {
    $screen = get_current_screen();
    if(strtolower($screen->id) === 'toplevel_page_bp-groups') {
        wp_enqueue_style('styles', get_stylesheet_directory_uri()."/style.css", false, '1.0.0');
        wp_enqueue_script('groups', get_stylesheet_directory_uri()."/js/admin.js", array('jquery'));
	}
    if(strtolower($screen->id) === 'toplevel_page_events-export-panel') {
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-ui-css', 'http://mdmozdev.wpengine.com.test/wp-content/plugins/events-manager/includes/css/jquery-ui.min.css');
		wp_enqueue_script('date', get_stylesheet_directory_uri()."/js/date.js", array('jquery'));
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

            if(isset($_POST['report_email'])) {
                update_option('report_email', sanitize_text_field($_POST['report_email']));
            }

            if(isset($_POST['mailchimp'])) {
                update_option('mailchimp', sanitize_text_field($_POST['mailchimp']));
            }

            if(isset($_POST['company'])) {
                update_option('company', sanitize_text_field($_POST['company']));
            }

            if(isset($_POST['address'])) {
                update_option('address', sanitize_text_field($_POST['address']));
            }

            if(isset($_POST['city'])) {
                update_option('city', sanitize_text_field($_POST['city']));
            }

            if(isset($_POST['state'])) {
                update_option('state', sanitize_text_field($_POST['state']));
            }

            if(isset($_POST['zip'])) {
                update_option('zip', sanitize_text_field($_POST['zip']));
            }

            if(isset($_POST['country'])) {
                update_option('country', sanitize_text_field($_POST['country']));
            }

            if(isset($_POST['phone'])) {
                update_option('phone', sanitize_text_field($_POST['phone']));
            }
        }
    }

    $options = wp_load_alloptions();
    include "{$theme_dir}/templates/settings.php";

}

function mozilla_export_events_control() {	
    $theme_dir = get_template_directory();
	include "{$theme_dir}/templates/event-export.php";
	return;
}

function mozilla_add_menu_item() {
    add_menu_page('Mozilla Settings', 'Mozilla Settings', 'manage_options', 'theme-panel', 'mozilla_theme_settings', null, 99);
    add_menu_page('Mozilla Export Events', 'Export Events', 'manage_options', 'events-export-panel', 'mozilla_export_events_control', 'dashicons-media-spreadsheet', 99);
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

function mozilla_verify_url($url, $secure) {
    if (preg_match('/\.[a-zA-Z]{2,4}\b/', $url)) {
        $parts = parse_url($url);
        if (!isset($parts["scheme"])) {
            if ($secure) {
                $url = 'https://'.$url;
            } else {
                $url = 'http://'.$url;

            }
        }
    }

    if(filter_var($url, FILTER_VALIDATE_URL)) {
        return $url;
    }

    return false;
}


function mozilla_add_group_columns($columns) {

    $columns['group_created'] = __("Group Created On", "community-portal");
    $columns['admins'] = __("Admins", "community-portal");
    $columns['events'] = __("Events", 'community-portal');
    $columns['verified_date'] = __("Group Verified On", "community-portal");

    return $columns;
    
}

function mozilla_group_addional_column_info($retval = "", $column_name, $item) {
    if($column_name !== 'group_created' 
        && $column_name !== 'events' 
        && $column_name !== 'admins'
        && $column_name !== 'verified_date') 
        return $retval;



    switch($column_name) {
        case 'group_created':
            if(isset($item['date_created'])) {
                if(strtotime($item['date_created']) < strtotime('-1 month')) {
                    $class = "admin__group-status--passed";
                } else {
                    $class = "admin__group-status--new";
                }

                return "<div class=\"{$class}\">{$item['date_created']}</div>";
            }

            break;
        case 'events':
            $args = Array(
                'group'     => $item['id'],
                'scope'     =>  'all'
            );

            $events = EM_Events::get($args);
            return sizeof($events);

            break;
        case 'admins':
            $admins = groups_get_group_admins($item['id']);
            return sizeof($admins);
            break;

        case 'verified_date':
            $group_meta = groups_get_groupmeta($item['id'], 'meta');

            if(isset($group_meta['verified_date'])) {
                $dateCheck = strtotime('+1 year', $group_meta['verified_date']);

                if($dateCheck < time()) {
                    $class = "admin__group-status--red";
                } else {
                    $class = "admin__group-status--new";
                }

                $verified_date = date("Y-m-d H:i:s", $group_meta['verified_date']);
                return "<div class=\"{$class}\">{$verified_date}</div>";
            } else {
                return "-";
            }
    }

    return '-';
}

function mozilla_save_post($post_id, $post, $update) {

    if($post->post_type === 'event' && $update) {

        $user = wp_get_current_user();
        $event_update_meta = get_post_meta($post->ID, 'event-meta');
        $event = new stdClass();

        if(isset($event_update_meta[0]->discourse_group_id))
            $event->discourse_group_id = $event_update_meta[0]->discourse_group_id;

        if(isset($event_update_meta[0]->discourse_group_name))
            $event->discourse_group_name = $event_update_meta[0]->discourse_group_name;

        if(isset($event_update_meta[0]->discourse_group_description))
            $event->discourse_group_description = $event_update_meta[0]->discourse_group_description;

        if(isset($event_update_meta[0]->discourse_group_users))
            $event->discourse_group_users = $event_update_meta[0]->discourse_group_users;

        $event->image_url = esc_url_raw($_POST['image_url']);
        $event->location_type = sanitize_text_field($_POST['location-type']);
        $event->external_url = esc_url_raw($_POST['event_external_link']);
		$event->language = $_POST['language'] ? sanitize_text_field($_POST['language']) : '';
		$event->goal = $_POST['goal'] ? sanitize_text_field($_POST['goal']): '';
		$event->projected_attendees = $_POST['projected-attendees'] ? intval($_POST['projected-attendees']): '';
        
        if(isset($_POST['initiative_id']) && strlen($_POST['initiative_id']) > 0) {
            $initiative_id = intval($_POST['initiative_id']);
            $initiative = get_post($initiative_id);
            if($initiative && ($initiative->post_type === 'campaign' || $initiative->post_type === 'activity')) {
                $event->initiative = $initiative_id;
            }
        }

        
        $discourse_api_data = Array();

        $discourse_api_data['name'] = $post->post_name;
        $discourse_api_data['description'] = $post->post_content;
        
        if(!empty($event_update_meta) && isset($event_update_meta[0]->discourse_group_id)) {
            $discourse_api_data['group_id'] = $event_update_meta[0]->discourse_group_id;
            $discourse_event = mozilla_get_discourse_info($post_id, 'event');
            $discourse_api_data['users'] = $discourse_event['discourse_group_users'];
            $discourse_group = mozilla_discourse_api('groups', $discourse_api_data, 'patch');
        }

        if($discourse_group) {
            $event->discourse_log = $discourse_group;
        }

        update_post_meta($post->ID, 'event-meta', $event);
    

    }
}


function mozilla_update_group_discourse_category_id() {

    // Only site admins
    if(!is_admin() && in_array('administrator', wp_get_current_user()->roles) === false) {
        die('Invalid Permissions');
    }

    if(isset($_GET['group'])) {

        $group_id = intval($_GET['group']);
        $meta = groups_get_groupmeta($group_id, 'meta');
        print "Before Meta Update<br>";
        print "<pre>";
        print_r($meta);
        print "</pre>";

        if(isset($_GET['category'])) {
            $category_id = intval($_GET['category']);
            
            if(isset($meta['discourse_category_id'])) {
                $meta['discourse_category_id'] = $category_id;
            }

            groups_update_groupmeta($group_id, 'meta', $meta);
        }

        print "After Meta Update<br>";
        $meta = groups_get_groupmeta($group_id, 'meta');
        print "<pre>";
        print_r($meta);
        print "</pre>";
    }
    die();
}

function mozilla_post_status_transition($new_status, $old_status, $post) { 

    if($new_status == 'publish')
    {
        if($post->post_type === 'campaign') {            
            mozilla_create_mailchimp_list($post);
        }    

        if($post->post_type === 'event' && $old_status !== 'publish') {

            $user = wp_get_current_user();
            $event = new stdClass();
            $event->image_url = esc_url_raw($_POST['image_url']);
            $event->location_type = sanitize_text_field($_POST['location-type']);
            $event->external_url = esc_url_raw($_POST['event_external_link']);
            $event->language = $_POST['language'] ? sanitize_text_field($_POST['language']) : '';
            $event->goal = $_POST['goal'] ? sanitize_text_field($_POST['goal']): '';
            $event->projected_attendees = $_POST['projected-attendees'] ? intval($_POST['projected-attendees']): '';

            if(isset($_POST['initiative_id']) && strlen($_POST['initiative_id']) > 0) {
                $initiative_id = intval($_POST['initiative_id']);
                $initiative = get_post($initiative_id);
                if($initiative && ($initiative->post_type === 'campaign' || $initiative->post_type === 'activity')) {
                    $event->initiative = $initiative_id;
                }
            }

            $discourse_api_data = Array();
            $discourse_api_data['name'] = $post->post_name;
            $discourse_api_data['description'] = $post->post_content;
            $auth0Ids = Array();
            $auth0Ids[] = mozilla_get_user_auth0($user->ID);
            $discourse_api_data['users'] = $auth0Ids;
            $discourse_group = mozilla_discourse_api('groups', $discourse_api_data, 'post');

            if($discourse_group) {
                if(isset($discourse_group->id)) {
                    $event->discourse_group_id = $discourse_group->id;
                } else {
                    $event->discourse_log = $discourse_group;
                }
            }

            update_post_meta($post->ID, 'event-meta', $event);

        }

    } 
} 

function mozilla_export_users() {

    // Only admins
    if(!is_admin() && in_array('administrator', wp_get_current_user()->roles) === false) {
        return;
    }

    $theme_directory = get_template_directory();
    include("{$theme_directory}/languages.php");
    include("{$theme_directory}/countries.php");

    $users = get_users(Array());

    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=users.csv;");

    // CSV Column Titles
    print "first name, last name, email,date registered, languages, country\n ";
    foreach($users AS $user) {
        $meta = get_user_meta($user->ID);
        $community_fields = isset($meta['community-meta-fields'][0]) ? unserialize($meta['community-meta-fields'][0]) : Array();
    
        $first_name = isset($meta['first_name'][0]) ? $meta['first_name'][0] : '';
        $last_name = isset($meta['last_name'][0]) ? $meta['last_name'][0] : '';
        $user_languages = isset($community_fields['languages']) && sizeof($community_fields['languages']) > 0 ? $community_fields['languages'] : Array();

        $language_string = '';
        foreach($user_languages AS $language_code) {
            if(strlen($language_code) > 0) {
                $language_string .= "{$languages[$language_code]},";
            }
        }

        // Remove ending comma
        $language_string = rtrim($language_string, ',');

        $country = isset($community_fields['country']) && strlen($community_fields['country']) > 0 ? $countries[$community_fields['country']] : '';
        $date = date("d/m/Y", strtotime($user->data->user_registered));
        
        // Print out CSV row
        print "{$first_name},{$last_name},{$user->data->user_email},{$date},\"{$language_string}\",{$country}\n";
    }
    die();
}



?>