<?php
    // Lets get the group data
    global $bp;
    $group = $bp->groups->current_group;
    $group_meta = groups_get_groupmeta($group->id, 'meta');
    $member_count = groups_get_total_member_count($group->id);
    $is_member = groups_is_user_member($user->ID, $group->id);
    $admins = groups_get_group_admins($group->id);   
    $admin_count = sizeof($admins);

    $args = Array(
        'group_id'      =>  $group->id,
        'group_role'    =>  Array('member')
    );
    
    $members = groups_get_group_members($args); 
    $is_admin = groups_is_user_admin($user->ID, $group->id);

    $edit_group = bp_is_group_admin_page() && $is_admin;
    $is_events = false;
    $theme_directory = get_template_directory();

    include("{$theme_directory}/countries.php");


    if($edit_group) {
        include("{$theme_directory}/buddypress/groups/single/edit.php");
    } else {
        $is_events = false;
        $is_people = false;
        
        if(isset($_GET['view'])) {
            switch(trim($_GET['view'])) {
                case 'events':
                    $is_events = true;
                    break;
                case 'people':
                    $is_people = true;
                    break;
                default: 
                    $is_events = false;
                    $is_people = false;
            }
        } else {
            $is_events = false;
        }

        include("{$theme_directory}/buddypress/groups/single/group.php");
    }

    do_action('bp_after_group_home_content');
    get_footer();
?>

