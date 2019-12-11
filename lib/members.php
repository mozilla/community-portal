<?php
/**
 * Member Theme Functions
 * 
 */


function mozilla_get_user_auth0($id) {
    $meta = get_user_meta($id);
    return (isset($meta['wp_auth0_id'][0])) ? $meta['wp_auth0_id'][0] : false;
}

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


function mozilla_post_user_creation($user_id, $userinfo, $is_new, $id_token, $access_token, $refresh_token ) {
    $meta = get_user_meta($user_id);


    if($is_new || !isset($meta['agree'][0]) || (isset($meta['agree'][0]) && $meta['agree'][0] != 'I Agree')) {
        $user = get_user_by('ID', $user_id);
        wp_redirect("/people/{$user->data->user_nicename}/profile/edit/group/1/");
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

function mozilla_delete_user() {

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(is_user_logged_in()) {
            $user = wp_get_current_user()->data;

            if($user) {
                $rand = substr(md5(time()), 0, 8);
                $anonymous_email = "anonymous{$rand}@anonymous.com";
                $user_check = get_user_by('email', $anonymous_email);
    
                while($user_check !== false) {
                    $rand = substr(md5(time()), 0, 8);
                    $anonymous_email = "anonymous{$rand}@anonymous.com";
                    $user_check = get_user_by('email', $anonymous_email);
                }
    
                $meta = get_user_meta($user->ID);
                $args = Array(
                    'ID'                =>  $user->ID,
                    'user_email'        =>  $anonymous_email,
                    'display_name'      =>  'Anonymous',
                    'first_name'        =>  'Anonymous',
                    'last_name'         =>  'Anonymous',
                    'user_url'          =>  '',
                    'user_nicename'     =>  "Anonymous{$rand}",
                    'user_login'       =>  "Anonymous{$rand}"
                );
    
                update_user_meta($user->ID, 'nickname', 'Anonymous');
                update_user_meta($user->ID, 'first_name', 'Anonymous');
                update_user_meta($user->ID, 'last_name', 'Anonymous');
                update_user_meta($user->ID, 'email', $anonymous_email);
    
                wp_update_user($args);
                delete_user_meta($user->ID, 'community-meta-fields');
                delete_user_meta($user->ID, 'description', '');
                delete_user_meta($user->ID, 'wp_auth0_obj');
                delete_user_meta($user->ID, 'wp_auth0_id');
                delete_user_meta($user->ID, 'first_name_visibility');
                delete_user_meta($user->ID, 'last_name_visibility');
                delete_user_meta($user->ID, 'email_visibility');

                wp_destroy_current_session();
                wp_clear_auth_cookie();
                wp_set_current_user(0);

                print json_encode(Array('status'   =>  'success', 'msg'  =>  'Account Deleted'));
            } else {
                print json_encode(Array('status'   =>  'error', 'msg'  =>  'No user'));
            }
        } else {
            print json_encode(Array('status'   =>  'error', 'msg'  =>  'Invalid Request'));
        }
    }

    die();
}


?>