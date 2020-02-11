<?php 
    get_header();
    $results = Array();

    $p = intval(get_query_var('page')) <= 1 ? 1 : intval(get_query_var('page'));

    $results_per_page = 12;

    // Lets get some search results
    if(isset($_GET['s']) && strlen($_GET['s']) > 0) {

        $args = Array(
            'posts_per_page'    =>  -1,
            'offset'            =>  0,
            'post_type'         => Array('campaign', 'activity')
        );

        $args['s'] = trim($_GET['s']);

        $query = new WP_Query($args);
        $results = $query->posts;
    }

    // Search Groups
    $group_args = Array(
        'search_terms'  =>  trim($_GET['s']),
        'per_page'      =>  -1
    );

    $groups = groups_get_groups($group_args);

    if(isset($groups['groups']) && is_array($groups['groups']) && sizeof($groups['groups']) > 0) {
        $results = array_merge($results, $groups['groups']);
    }

    // Search Users
    $wp_user_query = new WP_User_Query(Array(
        'offset'    =>  0,
        'number'    =>  -1
    ));

    $logged_in = mozilla_is_logged_in();
    $current_user = wp_get_current_user()->data;

    $members = $wp_user_query->get_results();
    $filtered_members = Array();
    $search_user = trim($_GET['s']);

    foreach($members AS $index  =>  $member) {
        $info = mozilla_get_user_info($current_user, $member, $logged_in);
        $member->info = $info;

        // Username
        if(stripos($member->data->user_nicename, $search_user) !== false) {
        $filtered_members[] = $member;
            continue;
        }

        // First name
        if($info['first_name']->display && stripos($search_user, $info['first_name']->value) !== false) {
            $filtered_members[] = $member;
            continue;
        }

        // Last name
        if($info['last_name']->display && stripos($search_user, $info['last_name']->value) !== false) {
            $filtered_members[] = $member;
            continue;
        }
    }

    if(sizeof($filtered_members) > 0) {
        $results = array_merge($results, $filtered_members);
    }

    // Search Events
    $events_args['scope'] = 'all';  
    $events_args['search'] = trim($_GET['s']);
    $events = EM_Events::get($events_args);
    $allCountries = Array();

    if(sizeof($events) > 0) {
        $allCountries = em_get_countries();
        $results = array_merge($results, $events);
    }

    $count = sizeof($results);
    $offset = ($p - 1) * $results_per_page;

    $results = array_slice($results, $offset, $results_per_page);
    $total_pages = ceil($count / $results_per_page);
?>
    <div class="content">
        <div class="search">
            <div class="search__container">
                <h1 class="search__title"><?php if(strlen($_GET['s']) > 0): ?><?php print __(sprintf('Results for %s', $_GET['s']), 'community-portal'); ?><?php else: ?><?php print __('Search','community-portal'); ?><?php endif; ?></h1>
                <div class="search__search-form-container">
                    <form method="GET" action="/" class="groups__form" id="group-search-form">
                        <div class="search__input-container">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17.5 17.5L13.875 13.875" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input type="text" name="s" id="search" class="groups__search-input" placeholder="<?php print __("Search", "community-portal"); ?>" value="<?php if(isset($_GET['s']) && strlen($_GET['s']) > 0): ?><?php print trim($_GET['s']); ?><?php endif; ?>" />
                        </div>
                        <input type="button" class="groups__search-cta" value="<?php print __("Search", "community-portal"); ?>" />
                    </form>
                </div>
                <div class="search__results">
                <?php foreach($results AS $result): ?>
                    <?php 
                        if(isset($result->post_content) && strlen($result->post_content) > 140) {
                            $description = substr($result->post_content, 0, 140). "...";
                        } else {
                            $description = $result->post_content;
                        }
                    ?>

                    <div class="search__result">
                        <?php if(isset($result->post_type) && $result->post_type === 'campaign'):?>
                        <h3 class="search__result-title search__result-title--campaign"><?php print __("Campaign", 'community-portal'); ?></h3>
                        <a href="/campaigns/<?php print $result->post_name; ?>" class="search__result-link"><?php print $result->post_title; ?></a>
                        <div class="search__result-dates">
                        <?php 
                            $start_date = get_field("campaign_start_date", $result->ID);
                            $end_date = get_field("campaign_end_date", $result->ID);
                        ?>
                        <?php print date("F j, Y", strtotime($start_date)); ?> - <?php print date("F j, Y", strtotime($end_date)); ?>
                        </div>
                        <div class="search__result-description">
                        <?php print $description; ?>
                        </div>
                        <?php endif; ?>                
                        <?php if(isset($result->post_type) && $result->post_type === 'page'):?>
                        <h3 class="search__result-title search__result-title--campaign"><?php print __("Page", 'community-portal'); ?></h3>
                        <a href="/<?php print $result->post_name; ?>" class="search__result-link"><?php print $result->post_title; ?></a>
                        <div class="search__result-dates">
                        <?php 
                            $start_date = get_field("campaign_start_date", $result->ID);
                            $end_date = get_field("campaign_end_date", $result->ID);
                        ?>
                        <?php print date("F j, Y", strtotime($start_date)); ?> - <?php print date("F j, Y", strtotime($end_date)); ?>
                        </div>
                        <div class="search__result-description">
                        <?php print $description; ?>
                        </div>
                        <?php endif; ?>                
                        <?php if(get_class($result) === 'BP_Groups_Group'):?>
                        <h3 class="search__result-title search__result-title--group"><?php print __("Group", 'community-portal'); ?></h3>
                        <a href="/groups/<?php print $result->slug; ?>" class="search__result-link"><?php print $result->name; ?></a>
                        <div class="search__result-description">

                        <?php
                            $group_meta = groups_get_groupmeta($result->id, 'meta');
                            $member_count = groups_get_total_member_count($result->id);
                        ?>
                        </div>
                        <?php if(isset($group_meta['group_city']) && strlen(trim($group_meta['group_city'])) > 0 || isset($group_meta['group_country']) && $group_meta['group_country'] != "0"): ?>
                        <div class="search__group-location">
                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 7.66699C14 12.3337 8 16.3337 8 16.3337C8 16.3337 2 12.3337 2 7.66699C2 6.07569 2.63214 4.54957 3.75736 3.42435C4.88258 2.29913 6.4087 1.66699 8 1.66699C9.5913 1.66699 11.1174 2.29913 12.2426 3.42435C13.3679 4.54957 14 6.07569 14 7.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 9.66699C9.10457 9.66699 10 8.77156 10 7.66699C10 6.56242 9.10457 5.66699 8 5.66699C6.89543 5.66699 6 6.56242 6 7.66699C6 8.77156 6.89543 9.66699 8 9.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?php 
                                if(strlen($group_meta['group_city']) > 180) {
                                    $group_meta['group_city'] = substr($group_meta['group_city'], 0, 180);
                                }
                            ?>
                            <?php print trim($group_meta['group_city']);?>
                            <?php 
                                if(isset($group_meta['group_country']) && strlen($group_meta['group_country']) > 0) {
                                    if(isset($group_meta['group_city']) && strlen($group_meta['group_city']) > 0) {
                                        print trim(", {$countries[$group_meta['group_country']]}");
                                    } else {
                                        print $countries[$group_meta['group_country']];
                                    }
                                }
                            ?>
                        </div>
                        <?php endif; ?>
                        <div class="search__group-members">
                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.3334 14V12.6667C12.3334 11.9594 12.0525 11.2811 11.5524 10.781C11.0523 10.281 10.374 10 9.66675 10H4.33341C3.62617 10 2.94789 10.281 2.4478 10.781C1.9477 11.2811 1.66675 11.9594 1.66675 12.6667V14" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6.99992 7.33333C8.47268 7.33333 9.66659 6.13943 9.66659 4.66667C9.66659 3.19391 8.47268 2 6.99992 2C5.52716 2 4.33325 3.19391 4.33325 4.66667C4.33325 6.13943 5.52716 7.33333 6.99992 7.33333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16.3333 14.0002V12.6669C16.3328 12.0761 16.1362 11.5021 15.7742 11.0351C15.4122 10.5682 14.9053 10.2346 14.3333 10.0869" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M11.6667 2.08691C12.2404 2.23378 12.7488 2.56738 13.1118 3.03512C13.4749 3.50286 13.672 4.07813 13.672 4.67025C13.672 5.26236 13.4749 5.83763 13.1118 6.30537C12.7488 6.77311 12.2404 7.10671 11.6667 7.25358" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?php print "{$member_count}&nbsp;".__("Members", "community-portal"); ?>
                        </div>
                        <?php endif; ?>                
                        <?php if(isset($result->post_type) && $result->post_type === 'activity'):?>
                        <h3 class="search__result-title search__result-title--activity"><?php print __("Activity", 'community-portal'); ?></h3>
                        <a href="/activities/<?php print $result->post_name; ?>" class="search__result-link"><?php print $result->post_title; ?></a>
                        <div class="search__result-description">
                        <?php print $description; ?>
                        </div>
                        <?php endif; ?>                
                        <?php if(isset($result->post_type) && $result->post_type === 'event'):?>
                        <?php
                            $location = em_get_location($result->location_id);
                        ?>
                        <h3 class="search__result-title search__result-title--event"><?php print __("Event", 'community-portal'); ?></h3>
                        <a href="/events/<?php print $result->event_slug; ?>" class="search__result-link"><?php print $result->event_name; ?></a>
                        <?php 
                            // print "<pre>";
                            // print_r($result);
                            // print "</pre>";
                        ?>
                        <div class="search__event-date">
                        <?php print date("F j, Y", strtotime($result->event_start_date)) ; ?>
                        </div>
                        <div class="search__event-location">
                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 7.66699C14 12.3337 8 16.3337 8 16.3337C8 16.3337 2 12.3337 2 7.66699C2 6.07569 2.63214 4.54957 3.75736 3.42435C4.88258 2.29913 6.4087 1.66699 8 1.66699C9.5913 1.66699 11.1174 2.29913 12.2426 3.42435C13.3679 4.54957 14 6.07569 14 7.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 9.66699C9.10457 9.66699 10 8.77156 10 7.66699C10 6.56242 9.10457 5.66699 8 5.66699C6.89543 5.66699 6 6.56242 6 7.66699C6 8.77156 6.89543 9.66699 8 9.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?php if($location->country === 'OE'): ?>
                                <?php print __("Online", "community-portal"); ?>
                            <?php else: ?>

                                <?php if($location->location_address): ?>
                                <?php print $location->location_address; ?> - 
                                <?php endif; ?>
                                <?php if($location->town): ?>
                                <?php 
                                    $city = strlen($location->town) > 180 ? substr($location->town, 0, 180) : $location->town;
                                    print $city;
                                ?>
                                <?php if($location->country): ?>
                                    <?php if($city): ?>
                                    ,&nbsp;
                                    <?php endif; ?>
                                    <?php print $allCountries[$location->country]; ?>
                                <?php endif; ?>
                                    <?php print $allCountries[$location->country]; ?>
                                <?php else: ?>

                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>       

                        <?php if(get_class($result) === 'WP_User'):?>
                        <h3 class="search__result-title search__result-title--member"><?php print __("Member", 'community-portal'); ?></h3>
                        <a href="/members/<?php print $result->user_nicename; ?>" class="search__result-link"><?php print $result->user_nicename; ?></a>
                        <div class="search__member-name">
                        <?php if($result->info['first_name']->display): ?>
                        <?php print $result->info['first_name']->value; ?>
                        <?php endif; ?>
                        <?php if($result->info['last_name']->display): ?>
                        <?php print $result->info['last_name']->value; ?>
                        <?php endif; ?>
                        </div>
                        <?php endif; ?>             
                    </div>   
                <?php endforeach; ?>
                </div>
            </div>
            <?php 
                $range = ($p > 3) ? 3 : 5;
                
                if($p > $total_pages - 2) 
                    $range = 5;
                
                $previous_page = ($p > 1) ? $p - 1 : 1;
                $next_page = ($p <= $total_pages) ? $p + 1 : $total_pages;

                if($total_pages > 1 ) {
                    $range_min = ($range % 2 == 0) ? ($range / 2) - 1 : ($range - 1) / 2;
                    $range_max = ($range % 2 == 0) ? $range_min + 1 : $range_min;

                    $page_min  = $page - $range_min;
                    $page_max = $page + $range_max;

                    $page_min = ($page_min < 1 ) ? 1 : $page_min;
                    $page_max = ($page_max < ($page_min + $range - 1)) ? $page_min + $range - 1 : $page_max;

                    if($page_max > $total_pages) {
                        $page_min = ($page_min > 1) ? $total_pages - $range + 1 : 1;
                        $page_max = $total_pages;
                    }

                }
            ?>

            <div class="campaigns__pagination">
                <div class="campaigns__pagination-container">
                    <?php if($total_pages > 1): ?>
                    <a href="/?s=<?php if(isset($_GET['s']) && strlen($_GET['s'])):?><?php print $_GET['s']; ?><?php endif; ?>&page=<?php print $previous_page?>" class="campaigns__pagination-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M17 23L6 12L17 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <?php if($page_min > 1): ?><a href="/?s=<?php if(isset($_GET['s']) && strlen($_GET['s'])):?><?php print $_GET['s']; ?><?php endif; ?>&page=1" class="campaigns__pagination-link campaigns__pagination-link--first"><?php print "1"; ?></a>&hellip; <?php endif; ?>
                    <?php for($x = $page_min - 1; $x < $page_max; $x++): ?>
                    <a href="/?s=<?php if(isset($_GET['s']) && strlen($_GET['s'])):?><?php print $_GET['s']; ?><?php endif; ?>&page=<?php print $x + 1; ?>" class="campaigns__pagination-link<?php if($p == $x + 1):?> campaigns__pagination-link--active<?php endif; ?><?php if($x === $page_max - 1):?> campaigns__pagination-link--last<?php endif; ?>"><?php print ($x + 1); ?></a>
                    <?php endfor; ?>
                    <?php if($total_pages > $range && $p < $total_pages - 1): ?>&hellip; <a href="/campaigns/?p=<?php print $total_pages; ?>" class="campaigns__pagination-link<?php if($p === $total_pages):?> campaigns__pagination-link--active<?php endif; ?>"><?php print $total_pages; ?></a><?php endif; ?>
                    <a href="/?s=<?php if(isset($_GET['s']) && strlen($_GET['s'])):?><?php print $_GET['s']; ?><?php endif; ?>&page=<?php print $next_page; ?>" class="campaigns__pagination-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M7 23L18 12L7 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php get_footer(); ?>