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
?>
    <div class="content">
        <div class="group">
            <div class="group__container">
                <h1 class="group__title"><?php print __($group->name); ?></h1>
                <div class="group__details">
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
                        $created = date("F d, Y", strtotime($group->date_created));
                        print "<span class=\"group__member-count\">{$member_count}</span> Group Member(s) | Created {$created}";
                    ?>
                </div>
                <div class="group__nav">
                    <ul class="group__menu">
                        <li class="menu-item"><a class="group__menu-link group__menu-link--active" href=""><?php print __("About us"); ?></a></li>
                        <li class="menu-item"><a class="group__menu-link" href=""><?php print __("Our Events"); ?></a></li>
                        <li class="menu-item"><a class="group__menu-link" href=""><?php print __("Our Members"); ?></a></li>
                    </ul>
                </div>
                <section class="group__info">
                    <div class="group__left-column">
                        <div class="group__card">
                            <div class="group__card-image" style="background-image: url('<?php print $group_meta['image_url']; ?>');"></div>
                            <div class="group__card-content">
                                <?php $is_admin = groups_is_user_admin($user->ID, $group->id); ?>
                                <div class="group__card-cta-container">
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
                                        <a class="group__meeting-location" href="<?php print $group_meta['group_address']; ?>" target="_blank"><?php print $group_meta['group_address']; ?></a>
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

                        <h2 class="group__card-title"><?php print __('Community Links'); ?></h2>
                        <div class="group__card group__card--community-link">
                            <div class="group__card-content">
                                <?php if(isset($group_meta['group_discourse']) && $group_meta['group_discourse']): ?>
                                <div class="group__community-container">
                                    <a class="group__community-link" href="<?php print $group_meta['group_discourse']; ?>"><?php print __('Discourse'); ?></a>
                                </div>
                                <?php ?>
                                <?php if(isset($group_meta['group_facebook']) && $group_meta['group_facebook']): ?>
                                <div class="group__community-container">
                                    <a class="group__community-link" href="<?php print $group_meta['group_facebook']; ?>"><?php print __('Discourse'); ?></a>
                                </div>
                                <?php ?>
                                <?php if(isset($group_meta['group_telegram']) && $group_meta['group_telegram']): ?>
                                <div class="group__community-container">
                                    <a class="group__community-link" href="<?php print $group_meta['group_telegram']; ?>"><?php print __('Discourse'); ?></a>
                                </div>
                                <?php ?>
                                <?php if(isset($group_meta['group_github']) && $group_meta['group_github']): ?>
                                <div class="group__community-container">
                                    <a class="group__community-link" href="<?php print $group_meta['group_github']; ?>"><?php print __('Discourse'); ?></a>
                                </div>
                                <?php ?>
                                <?php if(isset($group_meta['group_twitter']) && $group_meta['group_twitter']): ?>
                                <div class="group__community-container">
                                    <a class="group__community-link" href="<?php print $group_meta['group_twitter']; ?>"><?php print __('Discourse'); ?></a>
                                </div>
                                <?php ?>
                                <?php if(isset($group_meta['group_other']) && $group_meta['group_other']): ?>
                                <div class="group__community-container">
                                    <a class="group__community-link" href="<?php print $group_meta['group_other']; ?>"><?php print __('Discourse'); ?></a>
                                </div>
                                <?php ?>
                            </div>
                        </div>
                    </div>
                    <div class="group__right-column">
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Activity'); ?></span>
                            </div>
                        </div>
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Upcoming Events'); ?></span>
                            </div>
                        </div>
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Group Admins'); ?></span> 
                                <div class="group__admins">
                                    <?php foreach($admins AS $admin): ?>
                                    <?php
                                        $user = get_userdata($admin->user_id);
                                        $avatar = get_avatar_url($admin->user_id);
                                    ?>
                                    <div class="group__admin">
                                        <div class="avatar" style="background-image: url('<?php print $avatar; ?>')"></div>
                                        <span class="username"><?php print "@{$user->display_name}"; ?></span>
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
                </section>
            </div>
        </div>
    </div>
<?php


    do_action('bp_after_group_home_content');
    get_footer();
?>