<?php
/**
 * Group Theme Functions
 * 
 */


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
                            $args['description'] = sanitize_textarea_field($_POST['group_desc']);
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

                                $auth0Ids = Array();
                                $auth0Ids[] = mozilla_get_user_auth0($user->ID);

                                if(isset($_POST['group_admin_id']) && $_POST['group_admin_id'] && $group->creator_id == $user->ID) {
                                    $group_admin_user_id = intval($_POST['group_admin_id']);

                                    groups_join_group($group_id, $group_admin_user_id);
                                    $member = new BP_Groups_Member($group_admin_user_id, $group_id); 
                                    do_action('groups_promote_member', $group_id, $group_admin_user_id, 'admin'); 
                                    $member->promote('admin'); 
                                    $auth0Ids[] = mozilla_get_user_auth0($group_admin_user_id);
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

                                if(!empty($auth0Ids))
                                    $discourse_data['users'] = $auth0Ids;

                                $discourse_group = mozilla_discourse_api('groups', $discourse_data, 'post');
                                
                                if($discourse_group) {
                                    $meta['discourse_group_id'] = intval(sanitize_text_field($discourse_group->id));
                                }

                                // Don't need this data anymore 
                                unset($discourse_data['users']);
                                $discourse_data['groups'] = Array(intval($discourse_group->id));
                                $discourse = mozilla_discourse_api('categories', $discourse_data, 'post');
                                
                                if($discourse && isset($discourse->id) && $discourse->id) {
                                    $meta['discourse_category_id'] = intval(sanitize_text_field($discourse->id));
                                }            
        
                                $result = groups_update_groupmeta($group_id, 'meta', $meta);
                    
                                if($result) {
                                    unset($_SESSION['form']);
                                    $_POST = Array();
                                    $_POST['step'] = 3;
                            
                                    $_POST['group_slug'] = $group->slug;
                                } else {
                                    groups_delete_group($group_id);
                                    mozilla_discourse_api('categories', Array('category_id'    =>  $discourse->id), 'delete');
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
                        'description'   =>  sanitize_textarea_field($_POST['group_desc']),
                    );

                    // Update the group
                    groups_create_group($args);
                    // Update both category and group 
                    $discourse_api_data = Array();
                    $meta = Array();

                    $group_discourse_info = mozilla_get_discourse_info($group_id, 'group');

                    // Update Group Category on Discourse
                    $discourse_api_data['category_id'] = $group_discourse_info['discourse_category_id'];
                    $discourse_api_data['name'] = $args['name'];
                    $discourse_api_data['description'] = $args['description'];
                    $discourse_api_data['groups'] = Array(intval($group_discourse_info['discourse_group_id']));

                    $discourse_category = mozilla_discourse_api('categories', $discourse_api_data, 'patch');

                    // Update Group Meta locally
                    $meta['discourse_category_id'] = $group_discourse_info['discourse_category_id'];
                    if($discourse_category)
                        $meta['discourse_category_url'] = $discourse_category->url;
                    else 
                        $meta['discourse_category_url'] = $group_discourse_info['discourse_category_url'];

                    // Update Group on Discourse
                    $discourse_api_data = Array();
                    $discourse_api_data['group_id'] = $group_discourse_info['discourse_group_id'];
                    $discourse_api_data['name'] = $args['name'];
                    $discourse_api_data['description'] = $args['description'];
                    $discourse_api_data['users'] = $group_discourse_info['discourse_group_users'];

                    $discourse_group = mozilla_discourse_api('groups', $discourse_api_data, 'patch');
                    $meta['discourse_group_id'] = $group_discourse_info['discourse_group_id'];

                    if($discourse_group)
                        $meta['discourse_group_name'] = $discourse_group->discourse_group_name;
                    else    
                        $meta['discourse_group_name'] = $group_discourse_info['discourse_group_name'];


                    // Update group meta data
                    $meta['group_image_url'] = isset($_POST['image_url']) ? sanitize_text_field($_POST['image_url']) : '';
                    $meta['group_address_type'] = isset($_POST['group_address_type']) ? sanitize_text_field($_POST['group_address_type']) : 'Address';
                    $meta['group_address'] = isset($_POST['group_address']) ? sanitize_text_field($_POST['group_address']) : '';
                    $meta['group_meeting_details'] = isset($_POST['group_meeting_details']) ? sanitize_text_field($_POST['group_meeting_details']) : '';
                    $meta['group_city'] = isset($_POST['group_city']) ? sanitize_text_field($_POST['group_city']) : '';
                    $meta['group_country'] = isset($_POST['group_country']) ? sanitize_text_field($_POST['group_country']): '';
                    $meta['group_type'] = isset($_POST['group_type']) ? sanitize_text_field($_POST['group_type']) : 'Online';
                    

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


function mozilla_join_group() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
         $user = wp_get_current_user();
         $invite_status = groups_get_groupmeta($group->id, 'invite_status');
         if($user->ID &&  $invite_status === 'members') {
             if(isset($_POST['group']) && $_POST['group']) {
                 $group_id = intval($_POST['group']);
                 $joined = groups_join_group($group_id, $user->ID);
 
                 if($joined) {
                     $discourse_group_info = mozilla_get_discourse_info($group_id);
                     $discourse_api_data = Array();
                     $discourse_users = Array();
 
                     $discourse_users[] = mozilla_get_user_auth0($user->ID);
                     $discourse_api_data['group_id'] = $discourse_group_info['discourse_group_id'];
                     $discourse_api_data['add_users'] = $discourse_users;
 
                     $discourse = mozilla_discourse_api('groups/users', $discourse_api_data, 'patch');
             
 
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
                         $discourse_group_info = mozilla_get_discourse_info($group);
                         $discourse_api_data = Array();
                         $discourse_users = Array();
 
                         $discourse_users[] = mozilla_get_user_auth0($user->ID);
                         $discourse_api_data['group_id'] = $discourse_group_info['discourse_group_id'];
                         $discourse_api_data['remove_users'] = $discourse_users;
                         $discourse = mozilla_discourse_api('groups/users', $discourse_api_data, 'patch');
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


?>