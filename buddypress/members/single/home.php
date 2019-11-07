<?php

    $visibility_options = Array(
        PrivacySettings::REGISTERED_USERS   =>  __('Registered Users'),
        PrivacySettings::PUBLIC_USERS   =>  __('Public (Everyone)'),
        PrivacySettings::PRIVATE_USERS   =>  __('Private (Only Me)'),
    );

    $template_dir = get_template_directory();

?>

<div class="profile">
    <?php if(bp_is_my_profile() && bp_current_action() == 'edit'): ?>
        <?php 
            // Get current user
            $user = wp_get_current_user()->data;

            // Get user meta data
            $meta = get_user_meta($user->ID);

            // Check if this is their first time / haven't completed their profile by using CPG I agree 
            $form = $_POST;

            do_action('bp_before_edit_member_page');

            $complete = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete']) && $_POST['complete'] === true) ? true :  false;
            $updated_username = isset($form['username']) ? $form['username'] : false;

            include("{$template_dir}/buddypress/members/single/edit.php");
        ?>
    <?php else: ?>
        <?php 
            // Public profile
            $user_id = bp_displayed_user_id();
            $user = get_user_by('ID', $user_id);
            $meta = get_user_meta($user_id);

            $current_user = wp_get_current_user()->data;
        ?>
        <?php include("{$template_dir}/buddypress/members/single/profile.php"); ?>
    <?php endif; ?>
</div>	
