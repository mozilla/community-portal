<?php

    $visibility_options = Array(
        PrivacySettings::REGISTERED_USERS   =>  __('Registered Users'),
        PrivacySettings::PUBLIC_USERS   =>  __('Public (Everyone)'),
        PrivacySettings::PRIVATE_USERS   =>  __('Private (Only Me)'),
    );

    $template_dir = get_template_directory();

    require_once("{$template_dir}/countries.php");
    require_once("{$template_dir}/languages.php");

    $pronouns = Array(
        'She/Her',
        'He/Him',
        'They/Them'
    );
    
    $tags = get_tags(array('hide_empty' => false));
?>

<div class="profile">
    <?php if(bp_is_my_profile() && bp_current_action() == 'edit'): ?>
        <?php 
            // Get current user
            $user = wp_get_current_user()->data;

            // Get default user meta data
            $meta = get_user_meta($user->ID);

            if(isset($meta['community-meta-fields']) && isset($meta['community-meta-fields'][0])) {
                $community_fields = unserialize($meta['community-meta-fields'][0]);
            } else {
                $community_fields = false;
            }

            $form = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : false;

            if($form && isset($form['tags'])) {
                $form_tags = array_filter(explode(',', $form['tags']));
            } else {

                if($community_fields && isset($community_fields['tags'])) {
                    $form_tags = array_filter(explode(',', $community_fields['tags']));
                } else {
                    $form_tags = Array();
                }
            }

            do_action('bp_before_edit_member_page');

            $complete = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete']) && $_POST['complete'] === true) ? true :  false;
            $edit = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit']) && $_POST['edit'] === true) ? true :  false;
            $updated_username = isset($form['username']) ? $form['username'] : false;

            include("{$template_dir}/buddypress/members/single/edit.php");
        ?>
    <?php else: ?>
        <?php 
            // Public profile
            $user_id = bp_displayed_user_id();
            $user = get_user_by('ID', $user_id);
            
            $logged_in = mozilla_is_logged_in();
            $current_user = wp_get_current_user()->data;
            
            $is_me = $logged_in && intval($current_user->ID) === intval($user->ID);
        
            $info = mozilla_get_user_info($current_user, $user, $logged_in);
            include("{$template_dir}/buddypress/members/single/profile.php");           
        ?>
    <?php endif; ?>
</div>	
