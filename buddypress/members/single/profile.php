<?php

    // Visibility
    $logged_in = mozilla_is_logged_in();
    $is_me = $logged_in && intval($current_user->ID) === intval($user->ID);

    $community_fields = isset($meta['community-meta-fields'][0]) ? unserialize($meta['community-meta-fields'][0]) : Array('f');
    $community_fields['username'] =  $user->user_nicename;
    $community_fields['first_name'] = isset($meta['first_name'][0]) ? $meta['first_name'][0] : '';
    $community_fields['last_name'] = isset($meta['last_name'][0]) ? $meta['last_name'][0] : '';
    $community_fields['email'] = isset($meta['email'][0]) ? $meta['email'][0] : '';
    $community_fields['city'] = isset($meta['city'][0]) ? $meta['city'][0] : '';
    $community_fields['country'] = isset($meta['country'][0]) ? $meta['country'][0] : '';
    
    $fields = Array(
        'username',
        'image_url',
        'email',
        'first_name',
        'last_name',
        'pronoun',
        'bio',
        'phone',
        'city',
        'country',
        'profile_discourse',
        'profile_facebook',
        'profile_twitter',
        'profile_linkedin',
        'profile_github',
        'profile_telegram',
        'languages',
        'tags',
        'profile_groups_joined',
        'profile_events_attended',
        'profile_events_organized',
        'profile_campaigns'
    );

    $visibility_settings = Array();
    foreach($fields AS $field) {
        $field_visibility_name = "{$field}_visibility";
        $visibility = mozilla_determine_field_visibility($field, $field_visibility_name, $community_fields, $is_me, $logged_in);
        $field_visibility_name = ($field === 'city' || $field === 'country') ? 'profile_location_visibility' : $field_visibility_name;
        $visibility_settings[$field_visibility_name] = $visibility;
    }

?>  

<div class="profile__public-container">
    <section class="profile__section">
        <div class="profile__card">
        <div class="profile__card-header-container">
            <?php if($is_me): ?>
            <div class="profile__edit-link-container profile__edit-link-container--mobile">
                <a href="/members/<?php print $user->user_nicename; ?>/profile/edit/group/1" class="profile__link">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="profile__edit-icon">
                    <path d="M8.25 3H3C2.60218 3 2.22064 3.15804 1.93934 3.43934C1.65804 3.72064 1.5 4.10218 1.5 4.5V15C1.5 15.3978 1.65804 15.7794 1.93934 16.0607C2.22064 16.342 2.60218 16.5 3 16.5H13.5C13.8978 16.5 14.2794 16.342 14.5607 16.0607C14.842 15.7794 15 15.3978 15 15V9.75" stroke="#0060DF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.875 1.87419C14.1734 1.57582 14.578 1.4082 15 1.4082C15.422 1.4082 15.8266 1.57582 16.125 1.87419C16.4234 2.17256 16.591 2.57724 16.591 2.99919C16.591 3.42115 16.4234 3.82582 16.125 4.12419L9 11.2492L6 11.9992L6.75 8.99919L13.875 1.87419Z" stroke="#0060DF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <?php print __('Edit'); ?>
                </a>
            </div>
            <?php endif; ?>
            <div class="profile__avatar<?php if(!isset($community_fields['image_url']) || (isset($community_fields['image_url']) && strlen($community_fields['image_url']) <= 0 || !$visibility_settings['image_url_visibility'])): ?> profile__avatar--empty<?php endif; ?>"<?php if($visibility_settings['image_url_visibility']): ?> style="background-image: url('<?php print $community_fields['image_url']; ?>')"<?php endif; ?> data-user="<?php print $user->user_nicename; ?>">
            </div>
            <div class="profile__name-container">
                <h3 class="profile__user-title"><?php print $user->user_nicename; ?></h3>
                <span class="profile__user-name">
                    <?php if($visibility_settings['first_name_visibility']): ?>
                    <?php print "{$community_fields['first_name']}"; ?>
                    <?php endif; ?>
                    <?php if($visibility_settings['last_name_visibility']): ?>
                    <?php print "{$community_fields['last_name']}"; ?>
                    <?php endif; ?>
                </span>
                <?php if($visibility_settings['pronoun_visibility']): ?><div class="profile__pronoun"><?php print $community_fields['pronoun']; ?></div><?php endif; ?>
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
        <?php if($visibility_settings['bio_visibility']): ?>
        <div class="profile__bio-container">
            <p class="profile__bio"><?php print $community_fields['bio']; ?></p>
        </div>
        <?php endif; ?>
        <div class="profile__card-contact-container">
            <span class="profile__contact-title"><?php print __('Contact Information'); ?></span>
            <?php if($visibility_settings['profile_location_visibility']): ?>
            <div class="profile__location-container">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="profile__location-icon">
                    <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                    <g clip-path="url(#clip0)">
                    <path d="M23.5 14.334C23.5 20.1673 16 25.1673 16 25.1673C16 25.1673 8.5 20.1673 8.5 14.334C8.5 12.3449 9.29018 10.4372 10.6967 9.03068C12.1032 7.62416 14.0109 6.83398 16 6.83398C17.9891 6.83398 19.8968 7.62416 21.3033 9.03068C22.7098 10.4372 23.5 12.3449 23.5 14.334Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 16.834C17.3807 16.834 18.5 15.7147 18.5 14.334C18.5 12.9533 17.3807 11.834 16 11.834C14.6193 11.834 13.5 12.9533 13.5 14.334C13.5 15.7147 14.6193 16.834 16 16.834Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                    <defs>
                    <clipPath id="clip0">
                    <rect width="20" height="20" fill="white" transform="translate(6 6)"/>
                    </clipPath>
                    </defs>
                </svg>
                <div class="profile__details">
                    <?php print __('Location'); ?>
                    <span class="profile__email">
                    <?php 
                        if(isset($community_fields['city']))
                            print $community_fields['city'];

                        if(isset($community_fields['city']) && isset($community_fields['country'])) {
                            print ", {$community_fields['country']}";
                        } else {
                            if(isset($community_fields['country'])) {
                                print $community_fields['country'];
                            }
                        }
                    ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
            <?php if($visibility_settings['email_visibility']): ?>
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
                        if(isset($community_fields['email']))
                            print $community_fields['email'];
                    ?>
                    </span>
                </div>

            </div>
            <?php endif; ?>
            <?php if($visibility_settings['phone_visibility']): ?>
            <div class="profile__phone-container">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="profile__phone-icon">
                    <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                    <path d="M24.3332 20.0994V22.5994C24.3341 22.8315 24.2866 23.0612 24.1936 23.2739C24.1006 23.4865 23.9643 23.6774 23.7933 23.8343C23.6222 23.9912 23.4203 24.1107 23.2005 24.185C22.9806 24.2594 22.7477 24.287 22.5165 24.2661C19.9522 23.9875 17.489 23.1112 15.3249 21.7078C13.3114 20.4283 11.6043 18.7212 10.3249 16.7078C8.91651 14.5338 8.04007 12.0586 7.76653 9.48276C7.7457 9.25232 7.77309 9.02006 7.84695 8.80078C7.9208 8.5815 8.03951 8.38 8.1955 8.20911C8.3515 8.03822 8.54137 7.90169 8.75302 7.8082C8.96468 7.71471 9.19348 7.66631 9.42486 7.6661H11.9249C12.3293 7.66212 12.7214 7.80533 13.028 8.06904C13.3346 8.33275 13.5349 8.69897 13.5915 9.09943C13.697 9.89949 13.8927 10.685 14.1749 11.4411C14.287 11.7394 14.3112 12.0635 14.2448 12.3752C14.1783 12.6868 14.0239 12.9729 13.7999 13.1994L12.7415 14.2578C13.9278 16.3441 15.6552 18.0715 17.7415 19.2578L18.7999 18.1994C19.0264 17.9754 19.3125 17.821 19.6241 17.7545C19.9358 17.688 20.2599 17.7123 20.5582 17.8244C21.3143 18.1066 22.0998 18.3022 22.8999 18.4078C23.3047 18.4649 23.6744 18.6688 23.9386 18.9807C24.2029 19.2926 24.3433 19.6907 24.3332 20.0994Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <div class="profile__details">
                    <?php print __('Phone'); ?>
                    <span class="profile__phone">
                    <?php 
                        if(isset($community_fields['phone']))
                            print $community_fields['phone'];
                    ?>
                    </span>
                </div>

            </div>
            <?php endif; ?>
        </div>
        </div>
        <?php 
            $groups = groups_get_user_groups($user->ID);
        ?>
        <?php if($groups['total'] > 0 && $visibility_settings['profile_groups_joined_visibility']): ?>
        <h2 class="profile__heading"><?php print __("Groups I'm In"); ?></h2>
        <?php $group_count = 0; ?>
        <div class="profile__card">
            <?php foreach($groups['groups'] AS $gid): ?>
            <div class="profile__group">
                <?php
                    $group = new BP_Groups_Group($gid);
                    $group_meta = groups_get_groupmeta($gid, 'meta');
                ?>
                <h2 class="profile__group-title"><?php print $group->name; ?></h2>
                <div class="profile__group-location">
                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php if(isset($group_meta['group_city'])): ?> <?php print $group_meta['group_city']; ?><?php endif; ?>
                    <?php if(isset($group_meta['group_country'])): ?> <?php print $countries[$group_meta['group_country']]; ?><?php endif; ?>
                    <?php if(isset($group_meta['group_type'])): ?><?php print "| {$group_meta['group_type']}"; ?><?php endif; ?>
                </div>
                <div class="profile__group-member-count">
                    <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.3337 14V12.6667C12.3337 11.9594 12.0527 11.2811 11.5526 10.781C11.0525 10.281 10.3742 10 9.66699 10H4.33366C3.62641 10 2.94814 10.281 2.44804 10.781C1.94794 11.2811 1.66699 11.9594 1.66699 12.6667V14" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6.99967 7.33333C8.47243 7.33333 9.66634 6.13943 9.66634 4.66667C9.66634 3.19391 8.47243 2 6.99967 2C5.52692 2 4.33301 3.19391 4.33301 4.66667C4.33301 6.13943 5.52692 7.33333 6.99967 7.33333Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16.333 13.9993V12.6659C16.3326 12.0751 16.1359 11.5011 15.7739 11.0341C15.4119 10.5672 14.9051 10.2336 14.333 10.0859" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M11.667 2.08594C12.2406 2.2328 12.749 2.5664 13.1121 3.03414C13.4752 3.50188 13.6722 4.07716 13.6722 4.66927C13.6722 5.26138 13.4752 5.83666 13.1121 6.3044C12.749 6.77214 12.2406 7.10574 11.667 7.2526" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <?php print groups_get_total_member_count($gid);  ?> Members
                </div>
            </div>
            <?php $group_count++; ?>
            <?php if( $group_count > 0 && $group_count < $groups['total']): ?>
            <hr class="profile__group-line" />
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php 
            $event_user = new EM_Person($user->ID);
            $events = $event_user->get_bookings();
        ?>
        <?php if($visibility_settings['profile_events_attended_visibility']): ?>
        <h2 class="profile__heading"><?php print __("Latest Events Attended"); ?></h2>
        <div class="profile__card">
            <div class="profile__event">
                <div class="profile__event-date">
                      Aug 24  
                </div>
                <div class="profile__event-info">
                    <div class="profile__event-title">Build your own Extension for Firefox at AUST</div>
                    <div class="profile__event-time">
                        Aug 24, 2019 ∙ 10:00 UTC
                    </div>
                    <div class="profile__event-location">
                        <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Dhaka, Bangladesh
                    </div>
                </div>
            </div>
            <hr class="profile__group-line" />
            <div class="profile__event">
                <div class="profile__event-date">
                      Aug 24  
                </div>
                <div class="profile__event-info">
                    <div class="profile__event-title">Build your own Extension for Firefox at AUST</div>
                    <div class="profile__event-time">
                        Aug 24, 2019 ∙ 10:00 UTC
                    </div>
                    <div class="profile__event-location">
                        <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Dhaka, Bangladesh
                    </div>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
        <?php if($visibility_settings['profile_events_organized_visibility']): ?>
        <h2 class="profile__heading"><?php print __("Latest Events Organized"); ?></h2>
        <div class="profile__card">
            <div class="profile__event">
                <div class="profile__event-date">
                      Aug 24  
                </div>
                <div class="profile__event-info">
                    <div class="profile__event-title">Build your own Extension for Firefox at AUST</div>
                    <div class="profile__event-time">
                        Aug 24, 2019 ∙ 10:00 UTC
                    </div>
                    <div class="profile__event-location">
                        <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Dhaka, Bangladesh
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </section>
    <section class="profile__section profile__section--right">
        <?php if($visibility_settings['profile_telegram_visibility']
        || $visibility_settings['profile_facebook_visibility']
        || $visibility_settings['profile_twitter_visibility']
        || $visibility_settings['profile_linkedin_visibility']
        || $visibility_settings['profile_discourse_visibility']
        || $visibility_settings['profile_github_visibility']
        ): ?>
        <div class="profile__social-card">
            <?php print __("Social Handles"); ?>
            <div class="profile__social-container">
                <?php if(isset($community_fields['telegram']) && strlen($community_fields['telegram']) > 0 && $visibility_settings['profile_telegram_visibility']): ?>
                <a href="<?php print $community_fields['telegram']; ?>" class="profile__social-link">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                        <path d="M24.3337 7.66602L15.167 16.8327" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M24.3337 7.66602L18.5003 24.3327L15.167 16.8327L7.66699 13.4993L24.3337 7.66602Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php print __('Telegram'); ?>
                </a>
                <?php endif; ?>
                <?php if(isset($community_fields['facebook']) && strlen($community_fields['facebook']) > 0 && $visibility_settings['profile_facebook_visibility']): ?>
                <a href="<?php print $community_fields['facebook']; ?>" class="profile__social-link">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M26 16C26 10.4771 21.5229 6 16 6C10.4771 6 6 10.4771 6 16C6 20.9913 9.65686 25.1283 14.4375 25.8785V18.8906H11.8984V16H14.4375V13.7969C14.4375 11.2906 15.9304 9.90625 18.2146 9.90625C19.3087 9.90625 20.4531 10.1016 20.4531 10.1016V12.5625H19.1921C17.9499 12.5625 17.5625 13.3333 17.5625 14.1242V16H20.3359L19.8926 18.8906H17.5625V25.8785C22.3431 25.1283 26 20.9913 26 16Z" fill="black"/>
                    </svg>
                    <?php print __('Facebook'); ?>
                </a>
                <?php endif; ?>
                <?php if(isset($community_fields['twitter']) && strlen($community_fields['twitter']) > 0 && $visibility_settings['profile_twitter_visibility']): ?>
                <a href="<?php print $community_fields['twitter']; ?>" class="profile__social-link">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                        <path d="M12.3766 23.9366C19.7469 23.9366 23.7781 17.8303 23.7781 12.535C23.7781 12.3616 23.7781 12.1889 23.7664 12.017C24.5506 11.4498 25.2276 10.7474 25.7656 9.94281C25.0343 10.2669 24.2585 10.4794 23.4641 10.5733C24.3006 10.0725 24.9267 9.28482 25.2258 8.35688C24.4392 8.82364 23.5786 9.15259 22.6812 9.32953C22.0771 8.6871 21.278 8.26169 20.4077 8.11915C19.5374 7.97661 18.6444 8.12487 17.8668 8.541C17.0893 8.95713 16.4706 9.61792 16.1064 10.4211C15.7422 11.2243 15.6529 12.1252 15.8523 12.9842C14.2592 12.9044 12.7006 12.4903 11.2778 11.7691C9.85506 11.0478 8.59987 10.0353 7.59375 8.7975C7.08132 9.67966 6.92438 10.724 7.15487 11.7178C7.38536 12.7116 7.98596 13.5802 8.83437 14.1467C8.19667 14.1278 7.57287 13.9558 7.01562 13.6452C7.01562 13.6616 7.01562 13.6788 7.01562 13.6959C7.01588 14.6211 7.33614 15.5177 7.9221 16.2337C8.50805 16.9496 9.32362 17.4409 10.2305 17.6241C9.64052 17.785 9.02155 17.8085 8.42109 17.6928C8.67716 18.489 9.17568 19.1853 9.84693 19.6843C10.5182 20.1832 11.3286 20.4599 12.1648 20.4756C10.7459 21.5908 8.99302 22.1962 7.18828 22.1944C6.86946 22.1938 6.55094 22.1745 6.23438 22.1366C8.0669 23.3126 10.1992 23.9363 12.3766 23.9334" fill="black"/>
                    </svg>
                    <?php print __('Twitter'); ?>
                </a>
                <?php endif; ?>
                <?php if(isset($community_fields['linkedin']) && strlen($community_fields['linkedin']) > 0 && $visibility_settings['profile_linkedin_visibility']): ?>
                <a href="<?php print $community_fields['linkedin']; ?>" class="profile__social-link">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                        <g clip-path="url(#clip0)">
                        <path d="M20.1663 23.5V21.8333C20.1663 20.9493 19.8152 20.1014 19.19 19.4763C18.5649 18.8512 17.7171 18.5 16.833 18.5H10.1663C9.28229 18.5 8.43444 18.8512 7.80932 19.4763C7.1842 20.1014 6.83301 20.9493 6.83301 21.8333V23.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M13.5003 15.1667C15.3413 15.1667 16.8337 13.6743 16.8337 11.8333C16.8337 9.99238 15.3413 8.5 13.5003 8.5C11.6594 8.5 10.167 9.99238 10.167 11.8333C10.167 13.6743 11.6594 15.1667 13.5003 15.1667Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M25.167 23.4991V21.8324C25.1664 21.0939 24.9206 20.3764 24.4681 19.7927C24.0156 19.209 23.3821 18.7921 22.667 18.6074" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19.333 8.60742C20.05 8.79101 20.6855 9.20801 21.1394 9.79268C21.5932 10.3774 21.8395 11.0964 21.8395 11.8366C21.8395 12.5767 21.5932 13.2958 21.1394 13.8805C20.6855 14.4652 20.05 14.8822 19.333 15.0658" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                        <clipPath id="clip0">
                        <rect width="20" height="20" fill="white" transform="translate(6 6)"/>
                        </clipPath>
                        </defs>
                    </svg>

                    <?php print __('Linkedin'); ?>
                </a>
                <?php endif; ?>
                <?php if(isset($community_fields['discourse']) && strlen($community_fields['discourse']) > 0 && $visibility_settings['profile_discourse_visibility']): ?>
                <a href="<?php print $community_fields['discourse']; ?>" class="profile__social-link">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                        <path d="M23.5 15.5834C23.5029 16.6832 23.2459 17.7683 22.75 18.75C22.162 19.9265 21.2581 20.916 20.1395 21.6078C19.021 22.2995 17.7319 22.6662 16.4167 22.6667C15.3168 22.6696 14.2318 22.4126 13.25 21.9167L8.5 23.5L10.0833 18.75C9.58744 17.7683 9.33047 16.6832 9.33333 15.5834C9.33384 14.2682 9.70051 12.9791 10.3923 11.8605C11.084 10.7419 12.0735 9.838 13.25 9.25002C14.2318 8.75413 15.3168 8.49716 16.4167 8.50002H16.8333C18.5703 8.59585 20.2109 9.32899 21.4409 10.5591C22.671 11.7892 23.4042 13.4297 23.5 15.1667V15.5834Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php print __('Discourse'); ?>
                </a>
                <?php endif; ?>
                <?php if(isset($community_fields['github']) && strlen($community_fields['github']) > 0 && $visibility_settings['profile_github_visibility']): ?>
                <a href="<?php print $community_fields['github']; ?>" class="profile__social-link">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                        <g clip-path="url(#clip0)">
                        <path d="M13.5003 22.6669C9.33366 23.9169 9.33366 20.5835 7.66699 20.1669M19.3337 25.1669V21.9419C19.3649 21.5445 19.3112 21.145 19.1762 20.77C19.0411 20.395 18.8278 20.053 18.5503 19.7669C21.167 19.4752 23.917 18.4835 23.917 13.9335C23.9168 12.77 23.4692 11.6512 22.667 10.8085C23.0469 9.79061 23.02 8.66548 22.592 7.66686C22.592 7.66686 21.6087 7.37519 19.3337 8.90019C17.4237 8.38254 15.4103 8.38254 13.5003 8.90019C11.2253 7.37519 10.242 7.66686 10.242 7.66686C9.81397 8.66548 9.78711 9.79061 10.167 10.8085C9.35876 11.6574 8.91076 12.7864 8.91699 13.9585C8.91699 18.4752 11.667 19.4669 14.2837 19.7919C14.0095 20.0752 13.798 20.413 13.6631 20.7835C13.5281 21.1539 13.4727 21.5486 13.5003 21.9419V25.1669" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                        <clipPath id="clip0">
                        <rect width="20" height="20" fill="white" transform="translate(6 6)"/>
                        </clipPath>
                        </defs>
                    </svg>
                    <?php print __('Github'); ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if(isset($community_fields['languages']) && sizeof($community_fields['languages']) > 0 && $visibility_settings['languages_visibility']): ?>
        <div class="profile__languages-card">
            <?php print __('Languages spoken'); ?>
            <div class="profile__languages-container">
            <?php 
                $languages_spoken = sizeof($community_fields['languages']); 
                $index = 0;
            ?>
            <?php foreach($community_fields['languages'] AS $code): ?>
                <?php $index++; ?>
                <span>
                    <?php print $languages[$code]; ?>
                    <?php if( $index < $languages_spoken): ?>
                    <?php print ","; ?>
                    <?php endif; ?>
                </span>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if(isset($community_fields['tags']) && strlen($community_fields['tags']) > 0 && $visibility_settings['tags_visibility']): ?>
        <div class="profile__tags-card">
            <?php print __('Tags'); ?>
            <div class="profile__tags-container">
            <?php 
                $tags = array_filter(explode(',', $community_fields['tags']));

            ?>
            <?php foreach($tags AS $tag): ?>
                <span class="profile__static-tag">
                    <?php print $tag; ?>
                </span>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>

</div>