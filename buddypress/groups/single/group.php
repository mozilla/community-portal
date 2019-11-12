
<?php
    get_header(); 

    // Lets get the group data
    do_action('bp_before_directory_groups_page');
    global $bp;
    $group = $bp->groups->current_group;
    $group_meta = groups_get_groupmeta($group->id, 'meta');
    $member_count = groups_get_total_member_count($group->id);
    $user = wp_get_current_user();
    $is_member = groups_is_user_member($user->ID, $group->id);
    $admins = groups_get_group_admins($group->id);   
    $admin_count = sizeof($admins);

    $args = Array(
        'group_id'      =>  $group->id,
        'group_role'    =>  Array('member')
    );
    
    $members = groups_get_group_members($args); 
    $is_admin = groups_is_user_admin($user->ID, $group->id);

    switch($group->status) {
        case 'public':
            $verified = true;
            break;
        case 'private':
            $verified = false;
        default: 
            $verified = false;
    }
?>
    <div class="content">
        <div class="group">
            <div class="group__container">
                <h1 class="group__title"><?php print __($group->name); ?></h1>
                <div class="group__details">
                    <?php if($verified): ?>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <ellipse cx="8" cy="7.97569" rx="8" ry="7.97569" fill="#0060DF"/>
                            <path d="M8 5.5L8.7725 7.065L10.5 7.3175L9.25 8.535L9.545 10.255L8 9.4425L6.455 10.255L6.75 8.535L5.5 7.3175L7.2275 7.065L8 5.5Z" fill="white" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span class="group__status">Verified</span>&nbsp;|
                    <?php else: ?>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.5 7.97569C15.5 12.103 12.1436 15.4514 8 15.4514C3.85643 15.4514 0.5 12.103 0.5 7.97569C0.5 3.84842 3.85643 0.5 8 0.5C12.1436 0.5 15.5 3.84842 15.5 7.97569Z" stroke="#B1B1BC"/>
                            <path d="M8 5.5L8.7725 7.065L10.5 7.3175L9.25 8.535L9.545 10.255L8 9.4425L6.455 10.255L6.75 8.535L5.5 7.3175L7.2275 7.065L8 5.5Z" fill="#B1B1BC" stroke="#B1B1BC" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span class="group__status">Unverified</span>&nbsp;|
                    <?php endif; ?>
                    <span class="group__location">
                    <?php 
                        if(isset($group_meta['group_city'])) {
                            print $group_meta['group_city'];
                            if(isset($group_meta['group_country'])) {
                                $country = $countries[$group_meta['group_country']];
                                print ", {$country} | ";
                            } else {
                                print "|";
                            }
                        } else {
                            if(isset($group_meta['group_country'])) {
                                $country = $countries[$group_meta['group_country']];
                                print "{$country} | ";
                            }
                        }
                    ?>
                    </span>
                    <span class="group__created">
                    <?php
                        $created = date("F d, Y", strtotime($group->date_created));
                        print "<span> Created {$created}";
                    ?>
                    </span>
                </div>
                <div class="group__nav">
                    <ul class="group__menu">
                    <li class="menu-item"><a class="group__menu-link<?php if(bp_is_group_home()): ?> group__menu-link--active<?php endif; ?>" href="/groups/<?php print $group->slug; ?>"><?php print __("About us"); ?></a></li>
                        <li class="menu-item"><a class="group__menu-link" href=""><?php print __("Our Events"); ?></a></li>
                    <li class="menu-item"><a class="group__menu-link<?php if(bp_is_group_members()): ?> group__menu-link--active<?php endif; ?>" href="/groups/<?php print $group->slug; ?>/members"><?php print __("Our Members"); ?></a></li>
                    </ul>
                </div>
                <div class="group__nav group__nav--mobile">
                    <?php print __('Showing'); ?>
                    <div class="select-container">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                            </g>
                        </svg>
                        <select class="group__nav-select">
                            <option value="/groups/<?php print $group->slug; ?>"<?php if(bp_is_group_home()): ?> selected<?php endif; ?>><?php print __("About us"); ?></option>
                            <option value=""><?php print __("Our Events"); ?></option>
                            <option value="/groups/<?php print $group->slug; ?>"<?php if(bp_is_group_members()): ?> selected<?php endif; ?>><?php print __("Our Members"); ?></option>
                        </select>
                    </div>
                </div>
                <section class="group__info">
                    <?php if(bp_is_group_members()): ?>
                    <div class="group__members-container">

                        <h2 class="group__card-title"><?php print __("Group Contacts")." ({$admin_count})"; ?></h2>
                        <div class="group__members">
                            <?php foreach($admins AS $admin): ?>
                            <?php 
                                $a = get_user_by('ID', $admin->user_id);

                                // Get Meta for visibility otions
                                $meta = get_user_meta($a->ID);
                            ?>
                            <a href="/members/<?php print $a->user_nicename; ?>" class="members__member-card">
                                <div class="members__avatar">

                                </div>
                                <div class="members__member-info">
                                    <div class="members__username"><?php print $a->user_nicename; ?></div>
                                    <div class="members__name">
                                        <?php 
                                            if($logged_in || PrivacySettings::PUBLIC_USERS) {
                                                print $meta['first_name'][0];
                                            }
                                            if($logged_in && $meta['last_name_visibility'][0] === PrivacySettings::REGISTERED_USERS || PrivacySettings::PUBLIC_USERS) {
                                                print " {$meta['last_name'][0]}";
                                            }
                                        ?>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <h2 class="group__card-title"><?php print __("People")." ({$members['count']})"; ?></h2>
                        <div class="group__members">
                            <?php foreach($members['members'] AS $member): ?>

                            <?php
                                // Get Meta for visibility otions
                                $meta = get_user_meta($member->ID);
                            ?>
                            <a href="/members/<?php print $member->user_nicename; ?>" class="members__member-card">
                                <div class="members__avatar">

                                </div>
                                <div class="members__member-info">
                                    <div class="members__username"><?php print $member->user_nicename; ?></div>
                                    <div class="members__name">
                                        <?php 
                                            if($logged_in || PrivacySettings::PUBLIC_USERS) {
                                                print $meta['first_name'][0];
                                            }
                                            if($logged_in && $meta['last_name_visibility'][0] === PrivacySettings::REGISTERED_USERS || PrivacySettings::PUBLIC_USERS) {
                                                print " {$meta['last_name'][0]}";
                                            }
                                        ?>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>  
                    </div>
                    <?php else: ?>
                    <div class="group__left-column">
                        <div class="group__card">
                            <?php if(isset($group_meta['image_url']) && strlen($group_meta['image_url']) > 0): ?>
                            <div class="group__card-image" style="background-image: url('<?php print $group_meta['image_url']; ?>');">
                                <?php if($is_admin): ?>
                                <a href="#" class="group__edit-link">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z" fill="#0060DF"/>
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="group__card-no-image">
                                <?php if($is_admin): ?>
                                <a href="" class="group__edit-link">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z" fill="#0060DF"/>
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <div class="group__card-content">

                                <div class="group__card-cta-container<?php if($is_admin): ?> group__card-cta-container--end<?php endif; ?>">
                                <?php if(!$is_admin): ?>
                                    <?php if($is_member): ?>
                                        <a href="#" class="group__leave-cta" data-group="<?php print $group->id; ?>"><?php print __('Leave Group'); ?></a>
                                    <?php else: ?>
                                        <a href="#" class="group__join-cta" data-group="<?php print $group->id; ?>"><?php print __('Join Group'); ?></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <a href="#" class="group__share-cta">
                                    <svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 9V15C1 15.3978 1.15804 15.7794 1.43934 16.0607C1.72064 16.342 2.10218 16.5 2.5 16.5H11.5C11.8978 16.5 12.2794 16.342 12.5607 16.0607C12.842 15.7794 13 15.3978 13 15V9M10 4.5L7 1.5M7 1.5L4 4.5M7 1.5V11.25" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php print __('Share Group'); ?>
                                </a>
                                </div>
                                <h2 class="group__card-title"><?php print __("About Us"); ?></h2>
                                <p class="group__card-copy">
                                    <?php print $group->description; ?>
                                </p>
                                <?php if(isset($group_meta['group_telegram']) || isset($group_meta['group_facebook']) || isset($group_meta['group_discourse']) || isset($group_meta['group_github']) || isset($group_meta['group_twitter']) || isset($group_meta['group_other'])): ?>
                                <div class="group__community-links">
                                    <span class="no-line"><?php print __("Community Links"); ?></span>
                                    <?php if(isset($group_meta['group_telegram'])): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <path d="M24.3332 7.66699L15.1665 16.8337" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M24.3332 7.66699L18.4998 24.3337L15.1665 16.8337L7.6665 13.5003L24.3332 7.66699Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <a href="<?php print $group_meta['group_telegram']; ?>" class="group__social-link"><?php print __("Telegram"); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($group_meta['group_facebook'])): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M26 16C26 10.4771 21.5229 6 16 6C10.4771 6 6 10.4771 6 16C6 20.9913 9.65686 25.1283 14.4375 25.8785V18.8906H11.8984V16H14.4375V13.7969C14.4375 11.2906 15.9304 9.90625 18.2146 9.90625C19.3087 9.90625 20.4531 10.1016 20.4531 10.1016V12.5625H19.1921C17.9499 12.5625 17.5625 13.3333 17.5625 14.1242V16H20.3359L19.8926 18.8906H17.5625V25.8785C22.3431 25.1283 26 20.9913 26 16Z" fill="black"/>
                                            </svg>
                                            <a href="<?php print $group_meta['group_facebook']; ?>" class="group__social-link"><?php print __("Facebook"); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($group_meta['group_discourse'])): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <path d="M23.5 15.5834C23.5029 16.6832 23.2459 17.7683 22.75 18.75C22.162 19.9265 21.2581 20.916 20.1395 21.6078C19.021 22.2995 17.7319 22.6662 16.4167 22.6667C15.3168 22.6696 14.2318 22.4126 13.25 21.9167L8.5 23.5L10.0833 18.75C9.58744 17.7683 9.33047 16.6832 9.33333 15.5834C9.33384 14.2682 9.70051 12.9791 10.3923 11.8605C11.084 10.7419 12.0735 9.838 13.25 9.25002C14.2318 8.75413 15.3168 8.49716 16.4167 8.50002H16.8333C18.5703 8.59585 20.2109 9.32899 21.4409 10.5591C22.671 11.7892 23.4042 13.4297 23.5 15.1667V15.5834Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <a href="<?php print $group_meta['group_discourse']; ?>" class="group__social-link"><?php print __("Discourse"); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($group_meta['group_github'])): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <g clip-path="url(#clip0)">
                                                <path d="M13.4998 22.6669C9.33317 23.9169 9.33317 20.5835 7.6665 20.1669M19.3332 25.1669V21.9419C19.3644 21.5445 19.3107 21.145 19.1757 20.77C19.0406 20.395 18.8273 20.053 18.5498 19.7669C21.1665 19.4752 23.9165 18.4835 23.9165 13.9335C23.9163 12.77 23.4687 11.6512 22.6665 10.8085C23.0464 9.79061 23.0195 8.66548 22.5915 7.66686C22.5915 7.66686 21.6082 7.37519 19.3332 8.90019C17.4232 8.38254 15.4098 8.38254 13.4998 8.90019C11.2248 7.37519 10.2415 7.66686 10.2415 7.66686C9.81348 8.66548 9.78662 9.79061 10.1665 10.8085C9.35828 11.6574 8.91027 12.7864 8.9165 13.9585C8.9165 18.4752 11.6665 19.4669 14.2832 19.7919C14.009 20.0752 13.7976 20.413 13.6626 20.7835C13.5276 21.1539 13.4722 21.5486 13.4998 21.9419V25.1669" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0">
                                                <rect width="20" height="20" fill="white" transform="translate(6 6)"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                            <a href="<?php print $group_meta['group_github']; ?>" class="group__social-link"><?php print __("Github"); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($group_meta['group_twitter'])): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <path d="M12.3766 23.9366C19.7469 23.9366 23.7781 17.8303 23.7781 12.535C23.7781 12.3616 23.7781 12.1889 23.7664 12.017C24.5506 11.4498 25.2276 10.7474 25.7656 9.94281C25.0343 10.2669 24.2585 10.4794 23.4641 10.5733C24.3006 10.0725 24.9267 9.28482 25.2258 8.35688C24.4392 8.82364 23.5786 9.15259 22.6812 9.32953C22.0771 8.6871 21.278 8.26169 20.4077 8.11915C19.5374 7.97661 18.6444 8.12487 17.8668 8.541C17.0893 8.95713 16.4706 9.61792 16.1064 10.4211C15.7422 11.2243 15.6529 12.1252 15.8523 12.9842C14.2592 12.9044 12.7006 12.4903 11.2778 11.7691C9.85506 11.0478 8.59987 10.0353 7.59375 8.7975C7.08132 9.67966 6.92438 10.724 7.15487 11.7178C7.38536 12.7116 7.98596 13.5802 8.83437 14.1467C8.19667 14.1278 7.57287 13.9558 7.01562 13.6452C7.01562 13.6616 7.01562 13.6788 7.01562 13.6959C7.01588 14.6211 7.33614 15.5177 7.9221 16.2337C8.50805 16.9496 9.32362 17.4409 10.2305 17.6241C9.64052 17.785 9.02155 17.8085 8.42109 17.6928C8.67716 18.489 9.17568 19.1853 9.84693 19.6843C10.5182 20.1832 11.3286 20.4599 12.1648 20.4756C10.7459 21.5908 8.99302 22.1962 7.18828 22.1944C6.86946 22.1938 6.55094 22.1745 6.23438 22.1366C8.0669 23.3126 10.1992 23.9363 12.3766 23.9334" fill="black"/>
                                            </svg>
                                            <a href="<?php print $group_meta['group_twitter']; ?>" class="group__social-link"><?php print __("Twitter"); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($group_meta['group_other'])): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <g clip-path="url(#clip0)">
                                                <path d="M20.1668 23.5V21.8333C20.1668 20.9493 19.8156 20.1014 19.1905 19.4763C18.5654 18.8512 17.7176 18.5 16.8335 18.5H10.1668C9.28277 18.5 8.43493 18.8512 7.80981 19.4763C7.18469 20.1014 6.8335 20.9493 6.8335 21.8333V23.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M13.4998 15.1667C15.3408 15.1667 16.8332 13.6743 16.8332 11.8333C16.8332 9.99238 15.3408 8.5 13.4998 8.5C11.6589 8.5 10.1665 9.99238 10.1665 11.8333C10.1665 13.6743 11.6589 15.1667 13.4998 15.1667Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M25.1665 23.5001V21.8334C25.166 21.0948 24.9201 20.3774 24.4676 19.7937C24.0152 19.2099 23.3816 18.793 22.6665 18.6084" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M19.3335 8.6084C20.0505 8.79198 20.686 9.20898 21.1399 9.79366C21.5937 10.3783 21.84 11.0974 21.84 11.8376C21.84 12.5777 21.5937 13.2968 21.1399 13.8815C20.686 14.4661 20.0505 14.8831 19.3335 15.0667" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0">
                                                <rect width="20" height="20" fill="white" transform="translate(6 6)"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                            <a href="<?php print $group_meta['group_other']; ?>" class="group__social-link"><?php print __("Other"); ?></a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if((isset($group_meta['group_meeting_details'])  && $group_meta['group_meeting_details']) || (isset($group_meta['group_address']) && $group_meta['group_address'])): ?>
                        <h2 class="group__card-title"><?php print __('Meetings'); ?></h2>
                        <div class="group__card">
                            <div class="group__card-content">
                                <span class="no-line"><?php print __('Meeting Details'); ?></span>
                                <?php if(isset($group_meta['group_meeting_details'])): ?>
                                <p class="group__card-copy">
                                    <?php print $group_meta['group_meeting_details']; ?>
                                </p>
                                <?php endif; ?>
                                <?php if(isset($group_meta['group_meeting_details']) && isset($group_meta['group_address'])): ?>
                                <hr />
                                <?php endif; ?>
                                <?php if(isset($group_meta['group_address']) && $group_meta['group_address']): ?>
                                <span class="no-line"><?php print __('Location'); ?></span>
                                <?php if(isset($group_meta['group_address_type']) && strtolower($group_meta['group_address_type']) == 'url'): ?>
                                    <div>
                                        <a class="group__meeting-location-link" href="<?php print $group_meta['group_address']; ?>" target="_blank"><?php print $group_meta['group_address']; ?></a>
                                    </div>
                                <?php else: ?>
                                    <p class="group__card-copy">
                                        <?php print $group_meta['group_address']; ?>
                                    </p>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>  
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="group__right-column">
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Activity'); ?></span>
                                <div class="group__member-count-container">
                                    <span class="group__member-count"><?php print $member_count; ?></span>
                                    Members
                                </div>
                            </div>
                        </div>
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Related Events'); ?></span>
                            </div>
                        </div>
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Group Admins'); ?></span> 
                                <div class="group__admins">
                                    <?php foreach($admins AS $admin): ?>
                                    <?php
                                        $user = get_userdata($admin->user_id);
                                        
                                    ?>
                                    <div class="group__admin">
                                        <div class="avatar"></div>
                                        <span class="username"><?php print "@{$user->user_nicename}"; ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Tags'); ?></span>
                                <div class="group__tags">
                                    <?php foreach($group_meta['group_tags'] AS $tag): ?>
                                    <a class="group__tag"><?php print $tag; ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
