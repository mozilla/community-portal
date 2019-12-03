<?php
// Mozilla theme functions file

// Remove the admin header styles for homepage
add_action('get_header', 'remove_admin_login_header');

// Native Wordpress Actions
add_action('init', 'mozilla_init');
add_action('wp_enqueue_scripts', 'mozilla_init_scripts');
add_action('admin_enqueue_scripts', 'mozilla_init_admin_scripts');
add_action('admin_menu', 'mozilla_add_menu_item');

// Ajax Calls
add_action('wp_ajax_nopriv_upload_group_image', 'mozilla_upload_image');
add_action('wp_ajax_upload_group_image', 'mozilla_upload_image');
add_action('wp_ajax_join_group', 'mozilla_join_group');
add_action('wp_ajax_nopriv_join_group', 'mozilla_join_group');
add_action('wp_ajax_leave_group', 'mozilla_leave_group');
add_action('wp_ajax_get_users', 'mozilla_get_users');
add_action('wp_ajax_validate_email', 'mozilla_validate_email');
add_action('wp_ajax_nopriv_validate_group', 'mozilla_validate_group_name');
add_action('wp_ajax_validate_group', 'mozilla_validate_group_name');
add_action('wp_ajax_check_user', 'mozilla_validate_username');

// Buddypress Actions
add_action('bp_before_create_group_page', 'mozilla_create_group', 10, 1);
add_action('bp_before_edit_group_page', 'mozilla_edit_group', 10, 1);
add_action('bp_before_edit_member_page', 'mozilla_update_member', 10, 1);

// Removed cause it was causing styling conflicts
remove_action('init', 'bp_nouveau_get_container_classes');
remove_action('em_event_save','bp_em_group_event_save', 1, 2);

// Auth0 Actions
add_action('auth0_user_login', 'mozilla_post_user_creation', 10, 6);

// Filters
add_filter('nav_menu_link_attributes', 'mozilla_add_menu_attrs', 10, 3);
add_filter('nav_menu_css_class', 'mozilla_menu_class', 10, 4);
add_filter('em_get_countries', 'mozilla_add_online_to_countries', 10, 1);
add_filter('em_location_get_countries', 'mozilla_add_online_to_countries', 10, 1);
add_filter('em_booking_save_pre','mozilla_approve_booking', 100, 2);
add_filter('em_event_submission_login', "mozilla_update_events_copy", 10, 1);
add_filter('wp_redirect', 'mozilla_events_redirect');
add_filter('em_event_delete', 'mozilla_delete_events', 10, 2);
add_filter( 'body_class', 'mozilla_update_body_class');
add_filter('acf/load_field/name=featured_group', 'acf_load_bp_groups', 10, 1);

// Events Action
add_action('save_post', 'mozilla_save_event', 10, 3);

$template_dir = get_template_directory();
include("{$template_dir}/countries.php");

// Include theme style.css file not in admin page
if(!is_admin()) {
    wp_enqueue_style('style', get_stylesheet_uri());
}

abstract class PrivacySettings {
    const REGISTERED_USERS = 0;
    const PUBLIC_USERS = 1; 
    const PRIVATE_USERS = 2;
}

function remove_admin_login_header() {
	remove_action('wp_head', '_admin_bar_bump_cb');
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
        'rewrite'            =>  Array('slug'    =>  'p')
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
        'rewrite'            =>  Array('slug'    =>  'activities')
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
        'rewrite'            =>  Array('slug'    =>  'campaigns')
    );

    register_post_type('campaign', $args);
}

function mozilla_add_menu_attrs($attrs, $item, $args) {
    $attrs['class'] = 'menu-item__link';
    return $attrs;
}

function mozilla_init_admin_scripts() {
    $screen = get_current_screen();

    if(strtolower($screen->id) === 'toplevel_page_bp-groups') {
        wp_enqueue_script('groups', get_stylesheet_directory_uri()."/js/admin.js", array('jquery'));
    }


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
    wp_enqueue_script('cleavejs', get_stylesheet_directory_uri()."/js/vendor/cleave.min.js", array());
    wp_enqueue_script('nav', get_stylesheet_directory_uri()."/js/nav.js", array('jquery'));
    wp_enqueue_script('profile', get_stylesheet_directory_uri()."/js/profile.js", array('jquery'));
    wp_enqueue_script('lightbox', get_stylesheet_directory_uri()."/js/lightbox.js", array('jquery'));
    wp_enqueue_script('gdrp', get_stylesheet_directory_uri()."/js/gdrp.js", array('jquery'));
    

}

// If the create group page is called create a group 
function mozilla_create_group() {
    if(is_user_logged_in()) {
        $required = Array(
            'group_name',
            'group_type',
            'group_desc',
            'my_nonce_field'
        );

        $optional = Array(
            'group_address_type',
            'group_address',
            'group_meeting_details',
            'group_discourse',
            'group_facebook',
            'group_telegram',
            'group_github',
            'group_twitter',
            'group_other',
            'group_country',
            'group_city'
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
                            
                            $args['name'] = sanitize_text_field($_POST['group_name']);
                            $args['description'] = sanitize_text_field($_POST['group_desc']);
                            $args['status'] = 'private';
                            
                            $group_id = groups_create_group($args);
                            $meta = Array();

                            if($group_id) {
                                // Loop through optional fields and save to meta
                                foreach($optional AS $field) {
                                    if(isset($_POST[$field]) && $_POST[$field] !== "") {
                                        $meta[$field] = trim(sanitize_text_field($_POST[$field]));
                                    }
                                }

                                $group = groups_get_group(Array('group_id' => $group_id ));
                                $user = wp_get_current_user();

                                if(isset($_POST['group_admin_id']) && $_POST['group_admin_id'] && $group->creator_id == $user->ID) {
                                    $group_admin_user_id = intval($_POST['group_admin_id']);

                                    groups_join_group($group_id, $group_admin_user_id);
                                    $member = new BP_Groups_Member($group_admin_user_id, $group_id); 
                                    do_action('groups_promote_member', $group_id, $group_admin_user_id, 'admin'); 
                                    $member->promote('admin'); 
                                }

                                // Required information but needs to be stored in meta data because buddypress does not support these fields
                                $meta['group_image_url'] = trim(sanitize_text_field($_POST['image_url']));
                                $meta['group_type'] = trim(sanitize_text_field($_POST['group_type']));
                    

                                if(isset($_POST['tags'])) {
                                    $tags = explode(',', $_POST['tags']);
                                    $meta['group_tags'] = array_filter($tags);
                                }

                                $discourse_data = Array();
                                $discourse_data['name'] = $group->name;
                                $discourse_data['description'] = $group->description;

                                $discourse = mozilla_discourse_api('categories', $discourse_data, 'post');
                                
                                if(isset($discourse->id) && $discourse->id) {
                                    $meta['discourse_category_id'] = intval(sanitize_text_field($discourse->id));
                                }

                                if(isset($discourse->url) && strlen($discourse->url) > 0) {
                                    $meta['discourse_category_url'] = sanitize_text_field($discourse->url);
                                }                    
        
                                $result = groups_update_groupmeta($group_id, 'meta', $meta);
                    
                                if($result) {
                                    unset($_SESSION['form']);
                                    $_POST = Array();
                                    $_POST['step'] = 3;
                                    
                                    $_POST['group_slug'] = $group->slug;
                                } else {
                                    groups_delete_group($group_id);
                                    mozilla_discourse_api('categories', Array('group_id'    =>  $discourse->id), 'delete');
                                    $_POST['step'] = 0;
                                }   

                                curl_close($curl);                             
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
                        
                        if(isset($_REQUEST['profile_image']) && $_REQUEST['profile_image'] == 'true') {
                            // Image size check
                            if(isset($image[0]) && isset($image[1])) {
                                if($image[0] >= 175 && $image[1] >= 175) {
                                    print $uploaded_url;
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
                                    print $uploaded_url;
                                } else {
                                    print "Image size is too small";
                                    unlink($uploaded_bits['file']);
                                }
                            } else {
                                print "Invalid image provided"; 
                                unlink($uploaded_bits['file']);
                            }
                        }  else {
                            print $uploaded_url;
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

function mozilla_validate_group_name() {

    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['q'])) {
            $query = $_GET['q'];
            $gid = isset($_GET['gid']) && $_GET['gid'] != 'false' ? intval($_GET['gid']) : false;

            $found = mozilla_search_groups($query, $gid);

            if($found == false) {
                print json_encode(true);
            } else {
                print json_encode(false);
            }
            die();
        }
    }
}

function mozilla_search_groups($name, $gid) {
    $groups = groups_get_groups();
    $group_array = $groups['groups'];

    $found = false;
    foreach($group_array AS $g) {
        if($gid && $gid == $g->id) {
            continue;
        } else {
            $x = trim(strtolower($g->name));
            $y = trim(strtolower($name));
            if(sanitize_text_field($x) ==  sanitize_text_field($y))
                return true;
                    
        }
    }

    return $found;
}

function add_query_vars_filter( $vars ){
  $vars[] = "view";
  $vars[] = "country";
  $vars[] = "tag";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );

function mozilla_validate_username() {

    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['u']) && strlen($_GET['u']) > 0) {
            $u = sanitize_text_field(trim($_GET['u']));
            $current_user_id = get_current_user_id();

            $query = new WP_User_Query(Array(
                'search'            =>  $u,
                'search_columns'    =>  Array(
                    'user_nicename'
                ),
                'exclude'   => Array($current_user_id)
            ));
   
            print (sizeof($query->get_results()) === 0) ? json_encode(true) : json_encode(false);
        }
    }
    die();
}

function mozilla_validate_email() {

    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['u']) && strlen($_GET['u']) > 0) {
            $u = sanitize_text_field(trim($_GET['u']));
            $current_user_id = get_current_user_id();

            $query = new WP_User_Query(Array(
                'search'            =>  $u,
                'search_columns'    =>  Array(
                    'user_email'
                ),
                'exclude'   => Array($current_user_id)
            ));
   
            print (sizeof($query->get_results()) === 0) ? json_encode(true) : json_encode(false);
        }
    }
    die();
}

function mozilla_get_users() {
    $json_users = Array();

    if(isset($_GET['q']) && $_GET['q']) {
        $q = esc_attr(trim($_GET['q']));
        $current_user_id = get_current_user_id();

        $query = new WP_User_Query(Array(
            'search'            =>  "*{$q}*",
            'search_columns'    =>  Array(
                'user_nicename'
            ),
            'exclude'   => Array($current_user_id)
        ));

        print json_encode($query->get_results());

    }
    die();
}

function mozilla_join_group() {
   if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = wp_get_current_user();
        
        if($user->ID) {
            if(isset($_POST['group']) && $_POST['group']) {
                $joined = groups_join_group(intval(trim($_POST['group'])), $user->ID);
                if($joined) {
                    print json_encode(Array('status'   =>  'success', 'msg'  =>  'Joined Group'));
                } else {
                    print json_encode(Array('status'   =>  'error', 'msg'   =>  'Could not join group'));
                }
                die();
            } 
        } else {
            setcookie('mozilla-redirect', $_SERVER['HTTP_REFERER'], 0, "/");
            print json_encode(Array('status'    =>  'error', 'msg'  =>  'Not Logged In'));
            die();
        }
    }

    print json_encode(Array('status'    =>  'error', 'msg'  =>  'Invalid Request'));
    die();
}

function mozilla_leave_group() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = wp_get_current_user();
        if($user->ID) {
            if(isset($_POST['group']) && $_POST['group']) {
                $group = intval(trim($_POST['group']));
                $group_object = groups_get_group(Array('group_id' => $group));
                if($group_object->creator_id !== $user->ID) {
                    $left = groups_leave_group($group, $user->ID);

                    if($left) {
                        print json_encode(Array('status'   =>  'success', 'msg'  =>  'Left Group'));
                    } else {
                        print json_encode(Array('status'   =>  'error', 'msg'   =>  'Could not leave group'));
                    }
                } else {
                    print json_encode(Array('status'   =>  'error', 'msg'   =>  'Admin cannot leave a group'));
                }
                die();
            }
        } else {
            print json_encode(Array('status'    =>  'error', 'msg'  =>  'Not Logged In'));
            die();
        }
    }

    print json_encode(Array('status'    =>  'error', 'msg'  =>  'Invalid Request'));
    die();
}

function mozilla_post_user_creation($user_id, $userinfo, $is_new, $id_token, $access_token, $refresh_token ) {
    $meta = get_user_meta($user_id);


    if($is_new || !isset($meta['agree'][0]) || (isset($meta['agree'][0]) && $meta['agree'][0] != 'I Agree')) {
        $user = get_user_by('ID', $user_id);
        wp_redirect("/members/{$user->data->user_nicename}/profile/edit/group/1/");
        die();        
    }

    if(isset($_COOKIE['mozilla-redirect']) && strlen($_COOKIE['mozilla-redirect']) > 0) {
        $redirect = $_COOKIE['mozilla-redirect'];
        unset($_COOKIE['mozilla-redirect']);
        wp_redirect($redirect);
        die();
    }
}


function mozilla_update_member() {  

    // Submited Form
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(is_user_logged_in()) {
            $user = wp_get_current_user()->data;
            $edit = false;

            // Get current meta to compare to
            $meta = get_user_meta($user->ID);

            $required = Array(
                'username',
                'username_visibility',
                'first_name',
                'first_name_visibility',
                'last_name',
                'last_name_visibility',
                'email',
                'email_visibility',
                'agree'
            );

            $additional_fields = Array(
                'image_url',
                'profile_image_url_visibility',
                'pronoun',
                'city',
                'country',
                'profile_pronoun_visibility',
                'bio',
                'profile_bio_visibility',
                'phone',
                'profile_phone_visibility',
                'discourse',
                'profile_discourse_visibility',
                'facebook',
                'profile_facebook_visibility',
                'twitter',
                'profile_twitter_visibility',
                'linkedin',
                'profile_linkedin_visibility',
                'github',
                'profile_github_visibility',
                'telegram',
                'profile_telegram_visibility',
                'languages',
                'profile_languages_visibility',
                'tags',
                'profile_tags_visibility',
                'profile_groups_joined_visibility',
                'profile_events_attended_visibility',
                'profile_events_organized_visibility',
                'profile_campaigns_visibility',
                'profile_location_visibility'
            );

            // Add additional required fields after initial setup
            if(isset($meta['agree'][0]) && $meta['agree'][0] == 'I Agree') {
                unset($required[8]);
                $required[] = 'profile_location_visibility';
                $_POST['edit'] = true;
            }

            $error = false;
            foreach($required AS $field) {
                if(isset($_POST[$field])) {
                    if($_POST[$field] === "" || $_POST[$field] === 0) {
                        $error = true;
                    }
                }
            }

            // Validate email and username
            if($error === false) {

                if(!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
                    $error = true;
                    $_POST['email_error_message'] = 'Invalid email address';
                }


                $query = new WP_User_Query(Array(
                    'search'            =>  sanitize_text_field(trim($_POST['email'])),
                    'search_columns'    =>  Array(
                        'user_email'
                    ),
                    'exclude'   => Array($user->ID)
                ));

                if(sizeof($query->get_results()) !== 0) {
                    $error = true;
                    $_POST['email_error_message'] = 'This email is already in use';
                }

                $query = new WP_User_Query(Array(
                    'search'            =>  sanitize_text_field(trim($_POST['username'])),
                    'search_columns'    =>  Array(
                        'user_nicename'
                    ),
                    'exclude'   => Array($user->ID)
                ));

                // Validate email

                if(sizeof($query->get_results()) !== 0) {
                    $_POST['username_error_message'] = 'This username is already in use';
                    $error = true;
                }
            }
           
            // Create the user and save meta data
            if($error === false) {

                $_POST['complete'] = true;

                // Update regular wordpress user data
                $data = Array(
                    'ID'            =>  $user->ID,
                    'user_email'    =>  sanitize_text_field(trim($_POST['email'])),
                );

                // We need to udpate the user
                if($_POST['username'] !== $user->user_nicename) {
                    $data['user_nicename'] = sanitize_text_field(trim($_POST['username']));
                }

                wp_update_user($data);

                // No longe need this key
                unset($required[0]);

                foreach($required AS $field) {
                    $form_data = sanitize_text_field(trim($_POST[$field]));
                    update_user_meta($user->ID, $field, $form_data);
                }

                // Update other fields here
                $addtional_meta = Array();

                foreach($additional_fields AS $field) {
                    if(isset($_POST[$field])) {
                        if(is_array($_POST[$field])) {
                            $additional_meta[$field] = array_map('sanitize_text_field', array_filter($_POST[$field]));
                        } else {
                            $additional_meta[$field] = sanitize_text_field(trim($_POST[$field]));
                        }
                    }
                }   

                update_user_meta($user->ID, 'community-meta-fields', $additional_meta);

            }
        }
    }
}

function mozilla_is_logged_in() {
    $current_user = wp_get_current_user()->data;
    return sizeof((Array)$current_user) > 0 ? true : false; 
}

function mozilla_get_user_info($me, $user, $logged_in) {

    // Username is ALWAYS public
    $object = new stdClass();
    $object->value = $user->user_nicename;
    $object->display = true;
    
    $data = Array(
        'username'  =>  $object,
        'id'        =>  $user->ID
    );

    $is_me = $logged_in && intval($me->ID) === intval($user->ID);
    $meta = get_user_meta($user->ID);
    $community_fields = isset($meta['community-meta-fields'][0]) ? unserialize($meta['community-meta-fields'][0]) : Array();

    // First Name
    $object = new stdClass();
    $object->value = isset($meta['first_name'][0]) ? $meta['first_name'][0] : false;
    $object->display = mozilla_display_field('first_name', isset($meta['first_name_visibility'][0]) ? $meta['first_name_visibility'][0] : false, $is_me, $logged_in);
    
    $data['first_name'] = $object;

    // Last Name
    $object = new stdClass();
    $object->value = isset($meta['last_name'][0]) ? $meta['last_name'][0] : false;
    $object->display = mozilla_display_field('last_name', isset($meta['last_name_visibility'][0]) ? $meta['last_name_visibility'][0] : false, $is_me, $logged_in);
    $data['last_name'] = $object;

    // Email
    $object = new stdClass();
    $object->value = isset($meta['email'][0]) ? $meta['email'][0] : false;
    $object->display = mozilla_display_field('email', isset($meta['email_visibility'][0]) ? $meta['email_visibility'][0] : false , $is_me, $logged_in);
    $data['email'] = $object;

    // Location
    global $countries;
    $object = new stdClass();
    if(isset($community_fields['city']) && strlen($community_fields['city']) > 0 && isset($community_fields['country']) && strlen($community_fields['country']) > 1) {
        $object->value = "{$community_fields['city']}, {$countries[$community_fields['country']]}";
    } else {
        if(isset($community_fields['city']) && strlen($community_fields['city']) > 0) {
            $object->value = $community_fields['city'];
        } elseif(isset($community_fields['country']) && strlen($community_fields['country']) > 0) {
            $object->value = $countries[$community_fields['country']];
        } else {
            $object->value = false;
        }
    }
    
    $object->display = mozilla_display_field('location', isset($meta['profile_location_visibility'][0]) ? $meta['profile_location_visibility'][0] : false , $is_me, $logged_in);
    $data['location'] = $object;

    // Profile Image
    $object = new stdClass();
    $object->value = isset($community_fields['image_url']) && strlen($community_fields['image_url']) > 0 ? $community_fields['image_url'] : false;
    $object->display = mozilla_display_field('image_url', isset($community_fields['profile_image_url_visibility']) ? $community_fields['profile_image_url_visibility'] : false , $is_me, $logged_in);
    $data['profile_image'] = $object;

    // Bio
    $object = new stdClass();
    $object->value = isset($community_fields['bio']) && strlen($community_fields['bio']) > 0 ? $community_fields['bio'] : false;
    $object->display = mozilla_display_field('bio', isset($community_fields['bio_visibility']) ? $community_fields['bio_visibility'] : false , $is_me, $logged_in);
    $data['bio'] = $object;

    // Pronoun Visibility 
    $object = new stdClass();
    $object->value = isset($community_fields['pronoun']) && strlen($community_fields['pronoun']) > 0 ? $community_fields['pronoun'] : false;
    $object->display = mozilla_display_field('pronoun', isset($community_fields['pronoun_visibility']) ? $community_fields['pronoun_visibility'] : false , $is_me, $logged_in);
    $data['pronoun'] = $object;

    // Phone
    $object = new stdClass();
    $object->value = isset($community_fields['phone']) ? $community_fields['phone'] : false;
    $object->display = mozilla_display_field('phone', isset($community_fields['phone_visibility']) ? $community_fields['phone_visibility'] : false , $is_me, $logged_in);
    $data['phone'] = $object;

    // Groups Joined
    $object = new stdClass();
    $object->display = mozilla_display_field('groups_joined', isset($community_fields['profile_groups_joined_visibility']) ? $community_fields['profile_groups_joined_visibility'] : false , $is_me, $logged_in);
    $data['groups'] = $object;

    // Events Attended
    $object = new stdClass();
    $object->display = mozilla_display_field('events_attended', isset($community_fields['profile_events_attended_visibility']) ? $community_fields['profile_events_attended_visibility'] : false , $is_me, $logged_in);
    $data['events_attended'] = $object;

    // Events Organized
    $object = new stdClass();
    $object->display = mozilla_display_field('events_organized', isset($community_fields['profile_events_organized_visibility']) ? $community_fields['profile_events_organized_visibility'] : false , $is_me, $logged_in);
    $data['events_organized'] = $object;
    

    // Social Media 
    $object = new stdClass();
    $object->value = isset($community_fields['telegram']) && strlen($community_fields['telegram']) > 0 ? $community_fields['telegram'] : false;
    $object->display = mozilla_display_field('telegram', isset($community_fields['profile_telegram_visibility']) ? $community_fields['profile_telegram_visibility'] : false , $is_me, $logged_in);
    $data['telegram'] = $object;

    $object = new stdClass();
    $object->value = isset($community_fields['facebook']) && strlen($community_fields['facebook']) > 0 ? $community_fields['facebook'] : false;
    $object->display = mozilla_display_field('facebook', isset($community_fields['profile_facebook_visibility']) ? $community_fields['profile_facebook_visibility'] : false , $is_me, $logged_in);
    $data['facebook'] = $object;

    $object = new stdClass();
    $object->value = isset($community_fields['twitter']) && strlen($community_fields['twitter']) > 0 ? $community_fields['twitter'] : false;
    $object->display = mozilla_display_field('twitter', isset($community_fields['profile_twitter_visibility']) ? $community_fields['profile_twitter_visibility'] : false , $is_me, $logged_in);
    $data['twitter'] = $object;

    $object = new stdClass();
    $object->value = isset($community_fields['linkedin']) && strlen($community_fields['linkedin']) > 0 ? $community_fields['linkedin'] : false;
    $object->display = mozilla_display_field('linkedin', isset($community_fields['profile_linkedin_visibility']) ? $community_fields['profile_linkedin_visibility'] : false , $is_me, $logged_in);
    $data['linkedin'] = $object;

    $object = new stdClass();
    $object->value = isset($community_fields['discourse']) && strlen($community_fields['discourse']) > 0 ? $community_fields['discourse'] : false;
    $object->display = mozilla_display_field('discourse', isset($community_fields['profile_discourse_visibility']) ? $community_fields['profile_discourse_visibility'] : false , $is_me, $logged_in);
    $data['discourse'] = $object;

    $object = new stdClass();
    $object->value = isset($community_fields['github']) && strlen($community_fields['github']) > 0 ? $community_fields['github'] : false;
    $object->display = mozilla_display_field('github', isset($community_fields['profile_github_visibility']) ? $community_fields['profile_github_visibility'] : false , $is_me, $logged_in);
    $data['github'] = $object;

    //Languages
    $object = new stdClass();
    $object->value = isset($community_fields['languages']) && sizeof($community_fields['languages']) > 0 ? $community_fields['languages'] : false;
    $object->display = mozilla_display_field('languages', isset($community_fields['languages_visibility']) ? $community_fields['languages_visibility'] : false , $is_me, $logged_in);
    $data['languages'] = $object;

    // Tags
    $object = new stdClass();
    $object->value = isset($community_fields['tags']) && strlen($community_fields['tags']) > 0 ? $community_fields['tags'] : false;
    $object->display = mozilla_display_field('tags', isset($community_fields['tags_visibility']) ? $community_fields['tags_visibility'] : false , $is_me, $logged_in);
    $data['tags'] = $object;
    
    $object = null;
    return $data;
}

function mozilla_display_field($field, $visibility, $is_me, $logged_in) {

    if($is_me)
        return true;

    if($field === 'first_name' && $logged_in)
        return true;

    if($visibility == PrivacySettings::PUBLIC_USERS)
        return true;

    if($visibility === false)
        return false;

    if($logged_in && $visibility == PrivacySettings::REGISTERED_USERS)
        return true;

    return false;
}


function mozilla_save_event($post_id, $post, $update) {
    if ($post->post_type === 'event') {
        $event = new stdClass();
        $event->image_url = esc_url_raw($_POST['image_url']);
        $event->location_type = sanitize_text_field($_POST['location-type']);
        $event->external_url = esc_url_raw($_POST['event_external_link']);
        $event->campaign = sanitize_text_field($_POST['event_campaign']);
        update_post_meta($post_id, 'event-meta', $event);
    }
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

function mozilla_edit_group() {

    $group_id = bp_get_current_group_id();
    $user = wp_get_current_user();

    if($group_id && $user) {

        $is_admin = groups_is_user_admin($user->ID, $group_id);

        if($is_admin !== false) {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $required = Array(
                    'group_name',
                    'group_type',
                    'group_desc',
                    'group_address',
                    'my_nonce_field'
                );
                foreach($required AS $field) {
                    if(isset($_POST[$field])) {
                        if($_POST[$field] === "" || $_POST[$field] === 0) {
                            $error = true;
                        }
                    }
                }
      
                if(isset($_POST['group_name'])) {
                    $error = mozilla_search_groups($_POST['group_name'], $group_id);
                    if($error) {
                        $_POST['group_name_error'] = 'This group name is already taken';
                    }
                }

                // Lets update
                if($error === false) {
                    $args = Array(
                        'group_id'      =>  $group_id,
                        'name'          =>  sanitize_text_field($_POST['group_name']),
                        'description'   =>  sanitize_text_field($_POST['group_desc']),
                    );

                    // Update the group
                    groups_create_group($args);

                    // Update group meta data
                    $meta = Array();
                    $meta['group_image_url'] = isset($_POST['image_url']) ? sanitize_text_field($_POST['image_url']) : '';
                    $meta['group_address_type'] = isset($_POST['group_address_type']) ? sanitize_text_field($_POST['group_address_type']) : 'Address';
                    $meta['group_address'] = isset($_POST['group_address']) ? sanitize_text_field($_POST['group_address']) : '';
                    $meta['group_meeting_details'] = isset($_POST['group_meeting_details']) ? sanitize_text_field($_POST['group_meeting_details']) : '';
                    $meta['group_city'] = isset($_POST['group_city']) ? sanitize_text_field($_POST['group_city']) : '';
                    $meta['group_country'] = isset($_POST['group_country']) ? sanitize_text_field($_POST['group_country']): '';
                    $meta['group_type'] = isset($_POST['group_type']) ? sanitize_text_field($_POST['group_type']) : 'Online';
                    $meta['discourse_category_url'] = isset($_POST['group_discourse_url']) ? sanitize_text_field($_POST['group_discourse_url']) : '';
                    $meta['discourse_category_id'] = isset($_POST['group_discourse_id']) ? sanitize_text_field($_POST['group_discourse_id']) : '';

                    if(isset($_POST['tags'])) {
                        $tags = array_filter(explode(',', $_POST['tags']));
                        $meta['group_tags'] = $tags;
                    }

                    $meta['group_discourse'] = isset($_POST['group_discourse']) ? sanitize_text_field($_POST['group_discourse']) : '';
                    $meta['group_facebook'] = isset($_POST['group_facebook']) ? sanitize_text_field($_POST['group_facebook']) : '';
                    $meta['group_telegram'] = isset($_POST['group_telegram']) ? sanitize_text_field($_POST['group_telegram']) : '';
                    $meta['group_github'] = isset($_POST['group_github']) ? sanitize_text_field($_POST['group_github']) : '';
                    $meta['group_twitter'] = isset($_POST['group_twitter']) ? sanitize_text_field($_POST['group_twitter']) : '';
                    $meta['group_other'] = isset($_POST['group_other']) ? sanitize_text_field($_POST['group_other']) : '';

                    groups_update_groupmeta($group_id, 'meta', $meta);
                    $_POST['done'] = true;
                }
        
            }
        }
    }
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

function mozilla_theme_settings() {
    $theme_dir = get_template_directory();

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['admin_nonce_field']) && wp_verify_nonce($_REQUEST['admin_nonce_field'], 'protect_content')) {
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
        }
    }

    $options = wp_load_alloptions();
    
    include "{$theme_dir}/templates/settings.php";

}

function mozilla_add_menu_item() {
    add_menu_page('Mozilla Settings', 'Mozilla Settings', 'manage_options', 'theme-panel', 'mozilla_theme_settings', null, 99);
}

function mozilla_determine_site_section() {
    $path_items = array_filter(explode('/', $_SERVER['REQUEST_URI']));

    if(sizeof($path_items) > 0) {
        $section = array_shift(array_values($path_items));
        return $section;
    }

    return false;
}

function mozilla_events_redirect($location) {
    if (strpos($location, 'event_id') !== false) {
        $location = get_site_url(null, 'events/');
        return $location;
    }

    return $location;
}


function mozilla_is_site_admin(){
    return in_array('administrator',  wp_get_current_user()->roles);
}

function mozilla_delete_events($id, $post) {
    $post_id = $post->post_id;
    wp_delete_post($post_id);
    return $post;
}

function mozilla_update_body_class( $classes ) {
    $classes[] = "body";
    return $classes; 
}

function acf_load_bp_groups( $field ) {
    $allGroups = groups_get_groups(array());

    foreach ($allGroups['groups'] as $group):
        $groups[] = $group->name.'_'.$group->id;
    endforeach; 

    // Populate choices
    foreach( $groups as $group ) {
        $groupvalues = explode('_', $group);
        $field['choices'][ $groupvalues[1] ] = $groupvalues[0];
    }

    // Return choices
    return $field;
}


function mozilla_add_online_to_countries($countries) {
    $countries = array('OE' => 'Online Event') + $countries;
    return $countries;
}

function mozilla_update_events_copy($string) {
    $string = 'Please <a href="/wp-login.php?action=login">log in</a> to create or join events';
    return $string;
}; 

function mozilla_approve_booking($EM_Booking) {
    if (intval($EM_Booking->booking_status) === 0) {
        $EM_Booking->booking_status = 1;
        return $EM_Booking;
    }

    return $EM_Booking;
}

function mozilla_get_user_auth0($id) {
    $meta = get_user_meta($id);
    return (isset($meta['wp_auth0_id'][0])) ? $meta['wp_auth0_id'][0] : false;
}

function mozilla_discourse_api($type, $data, $request = 'GET') {
    $discourse = false;

    $options = wp_load_alloptions();
    if(isset($options['discourse_api_key']) && strlen($options['discourse_api_key']) > 0 && isset($options['discourse_api_url']) && strlen($options['discourse_api_url']) > 0) {
        // Get the API URL without the trailing slash
        $api_url = rtrim($options['discourse_api_url'], '/');
        $api_key = trim($options['discourse_api_key']);
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HTTPHEADER, Array(
                "Content-Type: Application/json",
                "x-api-key: {$api_key}"
            )
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $type = strtolower($type);
        $api_data = Array();

        switch(strtolower($type)) {
            case 'categories':
                curl_setopt($curl, CURLOPT_URL, "{$api_url}/categories");
                switch(strtolower($request)) {
                    case 'post':
                        if(isset($data['name']) && strlen($data['name']) > 0) {
                            curl_setopt($curl, CURLOPT_POST, 1);
                            $api_data['name'] = $data['name'];

                            if(isset($data['description']) && strlen($data['description']) > 0) 
                                $api_data['description'] = $data['description'];
        
                        }                    
                        break;
                    case 'patch':
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
                        break;
                    case 'delete':
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DEL");
                        if(isset($data['group_id']) && intval($data['group_id']) > 0) {    
                            $api_data['id'] = $data['group_id'];
                        }
                        
                        break;
                }
                break;
            case 'groups':
                curl_setopt($curl, CURLOPT_URL, "{$api_url}/groups");
                switch(strtolower($request)) {
                    case 'post':
                        if(isset($data['name']) && strlen($data['name']) > 0) {
                            curl_setopt($curl, CURLOPT_POST, 1);
                            
                            $api_data['name'] = $data['name'];
                            if(isset($data['description']) && strlen($data['description']) > 0) 
                                $api_data['description'] = $data['description'];

                            if(is_array($data['users'])) {
                                $api_data['users'] = $data['users'];
                            } else {
                                $api_data['users'] = Array();
                            }
                        }

                        break;
                    case 'patch':
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");

                        break;
                    case 'delete':
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DEL");
                        if(isset($data['group_id']) && intval($data['group_id']) > 0) {    
                            $api_data['id'] = $data['group_id'];
                        }
                        break;
                }
                break;
            case 'groups/users':
                curl_setopt($curl, CURLOPT_URL, "{$api_url}/groups/users");
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");

                if(is_array($data['add_users'])) {
                    $api_data['add'] = $data['add_users'];
                }

                if(is_array($data['remove_users'])) {
                    $api_data['remove'] = $data['remove_users'];
                }

                break;
        }

        if(!empty($api_data)) {
            $json_data = json_encode($api_data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        }
        
        $curl_result = curl_exec($curl);
        $discourse = json_decode($curl_result);
    }

    return $discourse;
}

function mozilla_discourse_get_category_topics($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_URL, "{$url}.json");
    $curl_result = curl_exec($curl);
    $discourse_category = json_decode($curl_result);
    
    curl_close($curl);
    
    if(isset($discourse_category->topic_list) && isset($discourse_category->topic_list->topics))
        $topics = is_array($discourse_category->topic_list->topics) ? $discourse_category->topic_list->topics : Array();
    else 
        $topics = Array();

    return $topics;
}

?>
