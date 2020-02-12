<?php 
    // Override the buddypress group listing page template

    // Main header template 
    get_header(); 

    // Execute actions by buddypress
    do_action('bp_before_directory_groups_page');
    do_action('bp_before_directory_groups');
    $logged_in = mozilla_is_logged_in();

    $groups_per_page = 12;
    $p = (isset($_GET['page'])) ? intval($_GET['page']) : 1;
    $args = Array(
        'per_page'  =>  -1
    );

	$q = (isset($_GET['q']) && strlen($_GET['q']) > 0) ? sanitize_text_field(trim($_GET['q'])) : false;
	if (isset($q)) {

		if (strpos($q, '"') || strpos($q, "'") || strpos($q, '\\')) {
			$q = stripslashes($q);
			$q = preg_replace('/^\"|\"$|^\'|\'$/', "", $q);
			$original_query = $q;
			$q = addslashes($q);
		}

		$args['search_columns'] = Array('name');
        $args['search_terms'] = $q;
	}

    $group_count = 0;
    $user = wp_get_current_user()->data;
    
    if($logged_in && isset($_GET['mygroups']) && $_GET['mygroups'] == 'true') {
    
        $groups = Array();
        $args['user_id'] = $user->ID;
        $groups = groups_get_groups($args);

    } else {
        if($q) {
            $args['search_columns'] = Array('name');
            $args['search_terms'] = $q;
        }
    
        $groups = groups_get_groups($args);
    }

	$groups = $groups['groups'];
    $filtered_groups = Array();
    $countries_with_groups = Array();
    $used_country_list = Array();

    foreach($groups AS $group) {
        $meta = groups_get_groupmeta($group->id, 'meta');
        $group->meta = $meta;

        if(isset($meta['group_country']) && strlen($meta['group_country']) > 1) {
            $countries_with_groups[] = $meta['group_country'];
        }

        if(isset($_GET['tag']) && strlen($_GET['tag']) > 0 && isset($_GET['location']) && strlen($_GET['location']) > 0) {
            if(in_array(strtolower(trim($_GET['tag'])), array_map('strtolower', $meta['group_tags'])) && trim(strtolower($_GET['location'])) == strtolower($meta['group_country'])) { 
                $filtered_groups[] = $group;
                continue;
            }
        } elseif(isset($_GET['tag']) && strlen($_GET['tag']) > 0 && (!isset($_GET['location']) || strlen($_GET['location']) === 0)) {
            if(in_array(strtolower(trim($_GET['tag'])), array_map('strtolower', $meta['group_tags']))) {
                $filtered_groups[] = $group;
                continue;
            }
        } elseif(isset($_GET['location']) && strlen($_GET['location']) > 0 && (!isset($_GET['tag']) || strlen($_GET['tag']) === 0)) {
            if(trim(strtolower($_GET['location'])) == strtolower($meta['group_country'])) {
                $filtered_groups[] = $group;
                continue;
            }
        } else {
            $filtered_groups[] = $group;
        }
	}
	
    $country_code_with_groups = array_unique($countries_with_groups);
    
    foreach($country_code_with_groups AS $code) {
        $used_country_list[$code] = $countries[$code];
    }

    ksort($used_country_list);

    $filtered_groups = array_unique($filtered_groups, SORT_REGULAR);
    $group_count = sizeof($filtered_groups);
    $offset = ($p - 1) * $groups_per_page;

    $groups = array_slice($filtered_groups, $offset, $groups_per_page);
    
    $total_pages = ceil($group_count / $groups_per_page);
    $tags = get_tags(Array('hide_empty' => false));
?>


<div class="content">
    <?php do_action('bp_before_directory_groups_content'); ?>
    <?php 
        
        global $wp_query;
        $id = $wp_query->get_queried_object_id();
        $page = get_page($id);
    
    ?>
    <div class="groups">
        <div class="groups__hero">
            <div class="groups__hero-container">
                <h1 class="groups__title"><?php print __("Groups", "community-portal"); ?></h1>
                <p class="groups__hero-copy">
                    <?php print __("Meet up with people who share your passion and join the movement for an open internet.", "community-portal"); ?>
                </p>
                <p class="groups__hero-copy">
                    <?php print __("Look for groups in your area, or", "community-portal"); ?> <a href="/groups/create/step/group-details/" class="groups__hero-link"><?php print __('create your own.', "community-portal"); ?></a>
                    <svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.33337 8.66634L6.00004 4.99967L2.33337 1.33301" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </p>
                <div class="groups__search-container">
                    <form method="GET" action="/groups/" class="groups__form" id="group-search-form">
                        <input type="hidden" value="<?php if(isset($_GET['tag']) && strlen($_GET['tag']) > 0): print trim($_GET['tag']); endif; ?>" name="tag" id="group-tag" />
                        <input type="hidden" value="<?php if(isset($_GET['location']) && strlen($_GET['location']) > 0): print trim($_GET['location']); endif; ?>" name="location" id="group-location" />
                        <input type="hidden" name="mygroups" value="<?php if(isset($_GET['mygroups']) && $_GET['mygroups'] == 'true'): ?>true<?php else: ?>false<?php endif; ?>" />
                        <div class="groups__input-container">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.5 17.5L13.875 13.875" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                        <input type="text" name="q" id="groups-search" class="groups__search-input" placeholder="<?php print __("Search groups", "community-portal"); ?>" value="<?php if(isset($original_query)): ?><?php print $original_query; ?><?php endif; ?>" />
                        </div>
                        <input type="button" class="groups__search-cta" value="<?php print __("Search", "community-portal"); ?>" />
                    </form>
                </div>
            </div>
        </div>
       
        <div class="groups__container">
            <div class="groups__nav">
                <ul class="groups__menu">
                    <li class="menu-item"><a class="groups__menu-link<?php if(!isset($_GET['mygroups']) || (isset($_GET['mygroups']) && $_GET['mygroups'] == 'false')): ?> group__menu-link--active<?php endif; ?>" href="#" data-nav=""><?php print __("Discover Groups"); ?></a></li>
                    <?php if($logged_in): ?><li class="menu-item"><a class="groups__menu-link<?php if(isset($_GET['mygroups']) && $_GET['mygroups'] == 'true'): ?> group__menu-link--active<?php endif; ?>" href="#" data-nav="mygroups"><?php print __("Groups I'm In"); ?></a></li><?php endif; ?>
                </ul>
            </div>
            <div class="groups__nav groups__nav--mobile">
                Showing: 
                <select class="groups__nav-select">
                    <option value="all"><?php print __("Discover Groups", "community-portal"); ?></option>
                    <?php if($logged_in): ?><option value="mygroups"<?php if(isset($_GET['mygroups']) && $_GET['mygroups'] == 'true'): ?>selected<?php endif; ?>><?php print __("Groups I'm in", "community-portal"); ?></option><?php endif; ?>
                </select>            
            </div>
                <div class="groups__filter-container<?php if(!isset($_GET['location']) && !isset($_GET['mygroups'])): ?> groups__filter-container--hidden<?php endif; ?>">
                <span><?php print __("Filter by:", "community-portal"); ?></span>
                <div class="groups__select-container">
                    <label class="groups__label">Location </label>
                    <select class="groups__location-select">
                        <option value=""><?php print __('All', "community-portal"); ?></option>
                        <?php foreach($used_country_list AS $code   =>  $country): ?>
                        <option value="<?php print $code; ?>"<?php if(isset($_GET['location']) && strlen($_GET['location']) > 0 && $_GET['location'] == $code): ?> selected<?php endif; ?>><?php print $country; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="groups__select-container">
                    <label class="groups__label">Tag </label>
                    <select class="groups__tag-select">
                        <option value=""><?php print __('All', "community-portal"); ?></option>
                        <?php foreach($tags AS $tag): ?>
                        <option value="<?php print $tag->slug; ?>" <?php if(isset($_GET['tag']) && strtolower(trim($_GET['tag'])) == strtolower($tag->slug)): ?> selected<?php endif; ?>><?php print $tag->name; ?></option>
                        <?php endforeach; ?>
                    </select>  
                </div>
            </div>
            <div class="groups__show-filters-container">
                <a href="#" class="groups__show-filter"><?php if(isset($_GET['location']) || isset($_GET['mygroups'])): ?><?php print __("Hide Filters", "community-portal"); ?><?php else: ?><?php print __("Show Filters"); ?><?php endif; ?></a>
            </div>
            <div class="groups__groups">
                <?php do_action('bp_before_groups_loop'); ?>
                <?php if(sizeof($groups) === 0): ?>
                    <div class="groups__no-results"><?php print __('No results found.  Please try another search term.', "community-portal"); ?></div>
                <?php else: ?>
                <?php if($original_query): ?>
                <div class="groups__results-query">
                <?php print __("Results for ", "community-portal")."\"{$original_query}\""; ?>
                </div>
                <?php endif; ?>
                <?php foreach($groups AS $group): ?>
                    <?php 
                        $meta = isset($group->meta) && is_array($group->meta) ? $group->meta : Array();
                        $member_count = groups_get_total_member_count($group->id);
                        $group_name = $group->name;

                        if(strlen($group_name) > 45) {
                            $group_name = substr($group_name, 0, 45)."&#133;";
                        }

                        if(is_array($meta) && isset($meta['group_image_url'])) {
                            if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                                $group_image_url = preg_replace("/^http:/i", "https:", $meta['group_image_url']);
                            } else {
                                $group_image_url = $meta['group_image_url'];
                            }
                        }
                    ?>

                    <a href="/groups/<?php print $group->slug; ?>" class="groups__card">
                        
                        <div class="groups__group-image" style="background-image: url('<?php print (isset($meta['group_image_url']) && strlen($meta['group_image_url']) > 0) ? $group_image_url : get_stylesheet_directory_uri().'/images/group.png'; ?>');">
                        </div>
                        <div class="groups__card-content">
                            <h2 class="groups__group-title"><?php print str_replace('\\', '', stripslashes($group_name)); ?></h2>
                                <?php if(isset($meta['group_city']) && strlen(trim($meta['group_city'])) > 0 || isset($meta['group_country']) && $meta['group_country'] != "0"): ?>
                                <div class="groups__card-location">
                                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14 7.66699C14 12.3337 8 16.3337 8 16.3337C8 16.3337 2 12.3337 2 7.66699C2 6.07569 2.63214 4.54957 3.75736 3.42435C4.88258 2.29913 6.4087 1.66699 8 1.66699C9.5913 1.66699 11.1174 2.29913 12.2426 3.42435C13.3679 4.54957 14 6.07569 14 7.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8 9.66699C9.10457 9.66699 10 8.77156 10 7.66699C10 6.56242 9.10457 5.66699 8 5.66699C6.89543 5.66699 6 6.56242 6 7.66699C6 8.77156 6.89543 9.66699 8 9.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php 
                                        if(strlen($meta['group_city']) > 180) {
                                            $meta['group_city'] = substr($meta['group_city'], 0, 180);
                                        }
                                    ?>
                                    <?php print trim($meta['group_city']);?><?php 
                                        if(isset($meta['group_country']) && strlen($meta['group_country']) > 0) {
                                            if(isset($meta['group_city']) && strlen($meta['group_city']) > 0) {
                                                print trim(", {$countries[$meta['group_country']]}");
                                            } else {
                                                print $countries[$meta['group_country']];
                                            }
                                        }
                                    ?>
                                </div>
                                <?php endif; ?>
                                <div class="groups__card-members">
                                    <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.3334 14V12.6667C12.3334 11.9594 12.0525 11.2811 11.5524 10.781C11.0523 10.281 10.374 10 9.66675 10H4.33341C3.62617 10 2.94789 10.281 2.4478 10.781C1.9477 11.2811 1.66675 11.9594 1.66675 12.6667V14" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6.99992 7.33333C8.47268 7.33333 9.66659 6.13943 9.66659 4.66667C9.66659 3.19391 8.47268 2 6.99992 2C5.52716 2 4.33325 3.19391 4.33325 4.66667C4.33325 6.13943 5.52716 7.33333 6.99992 7.33333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M16.3333 14.0002V12.6669C16.3328 12.0761 16.1362 11.5021 15.7742 11.0351C15.4122 10.5682 14.9053 10.2346 14.3333 10.0869" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M11.6667 2.08691C12.2404 2.23378 12.7488 2.56738 13.1118 3.03512C13.4749 3.50286 13.672 4.07813 13.672 4.67025C13.672 5.26236 13.4749 5.83763 13.1118 6.30537C12.7488 6.77311 12.2404 7.10671 11.6667 7.25358" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php print "{$member_count}&nbsp;".__("Members", "community-portal"); ?>
                                </div>
                            <div class="groups__card-info">
                                <div class="groups__card-tags">
                                    <?php 
                                        $tag_counter = 0;
                                    ?>
                                    <?php if(isset($meta['group_tags']) && is_array($meta['group_tags'])): ?>
                                    <?php foreach(array_unique($meta['group_tags']) AS $key =>  $value): ?>
                                        <span class="groups__tag"><?php print $value; ?></span>
                                        <?php $tag_counter++; ?>
                                        <?php if($tag_counter === 2 && sizeof($meta['group_tags']) > 2): ?>
                                        <span class="groups__tag">+ <?php print sizeof($meta['group_tags']) - 2; ?> <?php print __(' more tags', "community-portal"); ?></span>
                                        <?php break; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php do_action('bp_after_groups_loop'); ?>
                <?php 
                    $range = ($p > 3) ? 3 : 5;
                    
                    if($p > $total_pages - 2) 
                        $range = 5;
                    
                    $previous_page = ($p > 1) ? $p - 1 : 1;
                    $next_page = ($p <= $total_pages) ? $p + 1 : $total_pages;

                    if($total_pages > 1 ) {
                        $range_min = ($range % 2 == 0) ? ($range / 2) - 1 : ($range - 1) / 2;
                        $range_max = ($range % 2 == 0) ? $range_min + 1 : $range_min;

                        $page_min  = $p - $range_min;
                        $page_max = $p + $range_max;

                        $page_min = ($page_min < 1 ) ? 1 : $page_min;
                        $page_max = ($page_max < ($page_min + $range - 1)) ? $page_min + $range - 1 : $page_max;

                        if($page_max > $total_pages) {
                            $page_min = ($page_min > 1) ? $total_pages - $range + 1 : 1;
                            $page_max = $total_pages;
                        }

                    }

                ?>
                <div class="groups__pagination">
                    <div class="groups__pagination-container">
                        <?php if($total_pages > 1): ?>
                        <a href="/groups/?page=<?php print $previous_page?><?php if($q): ?>&q=<?php print $q; ?><?php endif; ?><?php if(isset($_GET['mygroups'])): ?>&mygroups=<?php print $_GET['mygroups']; ?><?php endif; ?><?php if(isset($_GET['tag'])): ?>&tag=<?php print $_GET['tag']; ?><?php endif; ?><?php if(isset($_GET['location'])): ?>&location=<?php print $_GET['location']; ?><?php endif; ?>" class="groups__pagination-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M17 23L6 12L17 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <?php if($page_min > 1): ?><a href="/groups/?page=1<?php if($q): ?>&q=<?php print $q; ?><?php endif; ?><?php if(isset($_GET['mygroups'])): ?>&mygroups=<?php print $_GET['mygroups']; ?><?php endif; ?><?php if(isset($_GET['tag'])): ?>&tag=<?php print $_GET['tag']; ?><?php endif; ?><?php if(isset($_GET['location'])): ?>&location=<?php print $_GET['location']; ?><?php endif; ?>" class="groups__pagination-link groups__pagination-link--first"><?php print "1"; ?></a>&hellip; <?php endif; ?>
                        <?php for($x = $page_min - 1; $x < $page_max; $x++): ?>
                        <a href="/groups/?page=<?php print $x + 1; ?><?php if($q): ?>&q=<?php print $q; ?><?php endif; ?><?php if(isset($_GET['mygroups'])): ?>&mygroups=<?php print $_GET['mygroups']; ?><?php endif; ?><?php if(isset($_GET['tag'])): ?>&tag=<?php print $_GET['tag']; ?><?php endif; ?><?php if(isset($_GET['location'])): ?>&location=<?php print $_GET['location']; ?><?php endif; ?>" class="groups__pagination-link<?php if($p == $x + 1):?> groups__pagination-link--active<?php endif; ?><?php if($x === $page_max - 1):?> groups__pagination-link--last<?php endif; ?>"><?php print ($x + 1); ?></a>
                        <?php endfor; ?>
                        <?php if($total_pages > $range && $p < $total_pages - 1): ?>&hellip; <a href="/groups/?page=<?php print $total_pages; ?><?php if($q): ?>&q=<?php print $q; ?><?php endif; ?><?php if($_GET['mygroups']): ?>&mygroups=<?php print $_GET['mygroups']; ?><?php endif; ?><?php if($_GET['tag']): ?>&tag=<?php print $_GET['tag']; ?><?php endif; ?><?php if(isset($_GET['location'])): ?>&location=<?php print $_GET['location']; ?><?php endif; ?>" class="groups__pagination-link<?php if($p === $total_pages):?> groups__pagination-link--active<?php endif; ?>"><?php print $total_pages; ?></a><?php endif; ?>
                        <a href="/groups/?page=<?php print $next_page; ?><?php if($q): ?>&q=<?php print $q; ?><?php endif; ?><?php if(isset($_GET['mygroups'])): ?>&mygroups=<?php print $_GET['mygroups']; ?><?php endif; ?><?php if(isset($_GET['tag'])): ?>&tag=<?php print $_GET['tag']; ?><?php endif; ?><?php if(isset($_GET['location'])): ?>&location=<?php print $_GET['location']; ?><?php endif; ?>" class="groups__pagination-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M7 23L18 12L7 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php do_action('bp_after_directory_groups_content'); ?>
</div>
<?php
    // Main footer template
    get_footer();

    // Fire at the end of template
    do_action('bp_after_directory_groups_page');
?>
