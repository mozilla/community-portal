<?php

    // Visibility
    $logged_in = mozilla_is_logged_in();
    $visibility_settings = mozilla_get_user_visibility_settings($user_id);
    $is_me = $logged_in && intval($current_user->ID) === intval($user->ID);

    if(isset($meta['wp_auth0_obj']) && sizeof($meta['wp_auth0_obj']) === 1) {
        $auth0 = json_decode($meta['wp_auth0_obj'][0]);
        $avatar = (isset($auth0->picture)) ? $auth0->picture : false;
    }
?>  

<div class="profile__public-container">
    <section class="profile__card">
        <div class="profile__card-header-container">
            <div class="profile__avatar<?php if($avatar === false): ?> profile__avatar--empty<?php endif; ?>" <?php if($is_me && $avatar || $logged_in && $avatar): ?>style="background-image: url('<?php print $avatar; ?>')"<?php endif; ?>></div>
            <div class="profile__name-container">
                <h3 class="profile__user-title"><?php print $user->user_nicename; ?></h3>
                <span class="profile__user-name">
                    <?php if($logged_in || $visibility_settings['first_name_visibility'] === PrivacySettings::PUBLIC_USERS || $is_me): ?>
                    <?php print "{$meta['first_name'][0]}"; ?>
                    <?php endif; ?>
                    <?php if(($logged_in && $visibility_settings['last_name_visibility'] === PrivacySettings::REGISTERED_USERS) 
                    || $visibility_settings['last_name_visbility'] === PrivacySettings::PUBLIC_USERS 
                    || $is_me): ?>
                    <?php print "{$meta['last_name'][0]}"; ?>
                    <?php endif; ?>
                </span>
            </div>
            <?php if($is_me): ?>
            <div class="profile__edit-link-container">
                <a href="/members/<?php print $user->user_nicename; ?>/profile/edit/group/1" class="profile__link">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="profile__edit-icon">
                    <path d="M8.25 3H3C2.60218 3 2.22064 3.15804 1.93934 3.43934C1.65804 3.72064 1.5 4.10218 1.5 4.5V15C1.5 15.3978 1.65804 15.7794 1.93934 16.0607C2.22064 16.342 2.60218 16.5 3 16.5H13.5C13.8978 16.5 14.2794 16.342 14.5607 16.0607C14.842 15.7794 15 15.3978 15 15V9.75" stroke="#0060DF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.875 1.87419C14.1734 1.57582 14.578 1.4082 15 1.4082C15.422 1.4082 15.8266 1.57582 16.125 1.87419C16.4234 2.17256 16.591 2.57724 16.591 2.99919C16.591 3.42115 16.4234 3.82582 16.125 4.12419L9 11.2492L6 11.9992L6.75 8.99919L13.875 1.87419Z" stroke="#0060DF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <?php print __('Edit'); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <div class="profile__card-contact-container">
            <span class="profile__contact-title"><?php print __('Contact Information'); ?></span>
            <?php if(($logged_in && $visibility_settings['email_visibility'] === PrivacySettings::REGISTERED_USERS)
             || $visibility_settings['email_visibility'] === PrivacySettings::PUBLIC_USERS
             || $is_me): ?>
            <div class="profile__email-container">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="profile__email-icon">
                    <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                    <path d="M9.33366 9.33398H22.667C23.5837 9.33398 24.3337 10.084 24.3337 11.0007V21.0006C24.3337 21.9173 23.5837 22.6673 22.667 22.6673H9.33366C8.41699 22.6673 7.66699 21.9173 7.66699 21.0006V11.0007C7.66699 10.084 8.41699 9.33398 9.33366 9.33398Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M24.3337 11L16.0003 16.8333L7.66699 11" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="profile__details">
                    <?php print __('Email'); ?>
                    <span class="profile__email">
                    <?php 
                        if(isset($meta['email'][0]))
                            print $meta['email'][0];
                    ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </section>


</div>