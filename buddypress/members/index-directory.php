<?php
    get_header(); 
    $logged_in = mozilla_is_logged_in();

    $members_per_page = 20;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 0;

    $offset = ($page - 1) * $members_per_page;
    
    if($offset < 0)
        $offset = 0;

    $args = Array('offset'  => 0, 'number'  =>  -1);

    $search_user = isset($_GET['u']) && strlen(trim($_GET['u'])) > 0 ? sanitize_text_field(trim($_GET['u'])) : false;
    if($search_user) {
        $args['search'] = "*{$search_user}*";
        $args['search_columns'] = Array('nicename');
    }

    $wp_user_query = new WP_User_Query($args);
    $members = Array();
    $members = $wp_user_query->get_results();
    
    if($logged_in && $search_user) {
        $wp_user_query = new WP_User_Query(Array(
            'number'        =>  -1,
            'offset'        =>  0,
            'meta_query'    =>  Array(
                Array(
                    'key' => 'first_name',
                    'value' => $search_user,
                    'compare' => 'LIKE'
                )
            )
        ));

        $first_name_members = $wp_user_query->get_results();
    } else {
        $first_name_members = Array();
    }

    $total_members = array_merge($members, $first_name_members);
    $filtered_members = array_unique($total_members, SORT_REGULAR);

    
    if($offset >= sizeof($filtered_members)) {
        $offset = sizeof($filtered_members) - $members_per_page;
    }

    $members = array_slice($filtered_members, $offset, $members_per_page);

    $total_pages = ceil(sizeof($filtered_members) / $members_per_page);

?>
<div class="content">
    <div class="members">
        <div class="members__hero">
            <div class="members__hero-container">
                <h1 class="members__title"><?php print __("People"); ?></h1>
                <p class="members__hero-copy">
                    <?php print __("A short paragraph about why members are great and why you should become one, if you’re not. Lorem ipsum dolor sit amet, consectetur adipiscing elit."); ?>
                </p>
                <div class="members__search-container">
                    <form method="GET" action="/members/" class="members__form">
                        <div class="members__input-container">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.5 17.5L13.875 13.875" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                        <input type="text" name="u" id="members-search" class="members__search-input" placeholder="<?php print __("Search"); ?>" value="<?php if($search_user): ?><?php print $search_user; ?><?php endif; ?>" />
                        </div>
                        <input type="submit" class="members__search-cta" value="<?php print __("Search"); ?>" />
                    </form>
                </div>
            </div>
        </div>
        <div class="members__container">
            <div class="members__people-container">
            <?php if(sizeof($members) > 0): ?>
            <?php foreach($members AS $member): ?>
            <?php 
                // Get Meta for visibility otions
                $meta = get_user_meta($member->data->ID);
                $community_fields = isset($meta['community-meta-fields'][0]) ? unserialize($meta['community-meta-fields'][0]) : Array();
                $community_fields['first_name'] = isset($meta['first_name'][0]) ? $meta['first_name'][0] : '';
                $community_fields['last_name'] = isset($meta['last_name'][0]) ? $meta['last_name'][0] : '';

                $visibility_settings = Array();

                $fields = Array(
                    'image_url',
                    'country',
                    'first_name',
                    'last_name'
                );
                
                $is_me = $logged_in && intval($current_user->ID) === intval($member->data->ID);

                foreach($fields AS $field) {
                    $field_visibility_name = "{$field}_visibility";
                    if($field == 'image_url') {
                        $field_visibility_name = 'profile_image_url_visibility';
                    }

                    $visibility = mozilla_determine_field_visibility($field, $field_visibility_name, $community_fields, $is_me, $logged_in);
                    $field_visibility_name = ($field === 'country') ? 'profile_location_visibility' : $field_visibility_name;
                    $visibility_settings[$field_visibility_name] = $visibility;
                }

                if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                    $avatar_url = preg_replace("/^http:/i", "https:", $community_fields['image_url']);
                } else {
                    $avatar_url = $community_fields['image_url'];
                }
                

            ?>
            <a href="/members/<?php print $member->data->user_nicename; ?>" class="members__member-card">
                <div class="members__avatar<?php if($visibility_settings['profile_image_url_visibility'] === false || !isset($community_fields['image_url']) || strlen($community_fields['image_url']) === 0): ?> members__avatar--identicon<?php endif; ?>" <?php if($visibility_settings['profile_image_url_visibility'] && isset($community_fields['image_url']) && strlen($community_fields['image_url']) > 0): ?> style="background-image: url('<?php print $avatar_url; ?>')"<?php endif; ?> data-username="<?php print $member->data->user_nicename; ?>">
                </div>
                <div class="members__member-info">
                    <div class="members__username"><?php print $member->data->user_nicename; ?></div>
                    <div class="members__name">
                        <?php 
                            if($visibility_settings['first_name_visibility'] || $logged_in) {
                                print $meta['first_name'][0];
                            }
                            if($visibility_settings['last_name_visibility']) {
                                print " {$meta['last_name'][0]}";
                            }
                        ?>
                    </div>
                    <?php if($visibility_settings['profile_location_visibility'] !== false && isset($community_fields['country']) && strlen($community_fields['country']) > 0): ?>
                    <div class="members__location">
                        <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>&nbsp;
                        <?php 
                            print $countries[$community_fields['country']];    
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
            </div>
            <?php else: ?>
                <h2 class="members__title--no-members-found">No members found</h2>
            <?php endif; ?>
            <?php 
                $range = ($page > 3) ? 3 : 5;
                
                if($page > $total_pages - 2) 
                    $range = 5;
                
                $previous_page = ($page > 1) ? $page - 1 : 1;
                $next_page = ($page < $total_pages) ? $page + 1 : $total_pages;

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

                    if($page_min < 0) {
                        $page_min = 1;
                    }

                    if($page < 1) {
                        $page = 1;
                    }

                    if($page > $page_max) {
                        $page = intval($page_max);
                    }
                }
            
            ?>
            <div class="members__pagination">
                <div class="members__pagination-container">
                    <?php if($total_pages > 1): ?>
                    <a href="/members/?page=<?php print $previous_page?><?php if($search_user): ?>&u=<?php print $search_user; ?><?php endif; ?>" class="members__pagination-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M17 23L6 12L17 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <?php if($page_min > 1): ?><a href="/members/?page=1<?php if($search_user): ?>&u=<?php print $search_user; ?><?php endif; ?>" class="members__pagination-link members__pagination-link--first"><?php print "1"; ?></a>&hellip; <?php endif; ?>
                    <?php for($x = $page_min - 1; $x < $page_max; $x++): ?>
                    <a href="/members/?page=<?php print $x + 1; ?><?php if($search_user): ?>&u=<?php print $search_user; ?><?php endif; ?>" class="members__pagination-link<?php if($page === $x + 1):?> members__pagination-link--active<?php endif; ?><?php if($x === $page_max - 1):?> members__pagination-link--last<?php endif; ?>"><?php print ($x + 1); ?></a>
                    <?php endfor; ?>
                    <?php if($total_pages > $range && $page < $total_pages - 1): ?>&hellip; <a href="/members/?page=<?php print $total_pages; ?><?php if($search_user): ?>&u=<?php print $search_user; ?><?php endif; ?>" class="members__pagination-link<?php if($page === $total_pages):?> members__pagination-link--active<?php endif; ?>"><?php print $total_pages; ?></a><?php endif; ?>
                    <a href="/members/?page=<?php print $next_page; ?><?php if($search_user): ?>&u=<?php print $search_user; ?><?php endif; ?>" class="members__pagination-link">
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
<?php 


    get_footer();
?>