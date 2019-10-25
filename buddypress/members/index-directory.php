<?php
    get_header(); 
    //$logged_in = mozilla_is_logged_in();
    $logged_in = true;

    $c = count_users();

    $members_per_page = 21;
    $total_pages = ceil($c['total_users'] / $members_per_page);

    $page = isset($_GET['page']) ? intval($_GET['page']) : 0;

    $offset = ($page - 1) * $members_per_page;

    $args = Array(
        'offset'    =>  ($offset > 0) ? $offset : 0,  
        'number'    =>  $members_per_page
    );

    $search_user = isset($_GET['u']) && strlen(trim($_GET['u'])) > 0 ? sanitize_text_field(trim($_GET['u'])) : false;
    if($search_user) {
        $args['search'] = "{$search_user}";
        $args['search_columns'] = Array('nicename');
    }

    $members = get_users($args);

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
            <?php if(sizeof($members) > 0): ?>
            <?php foreach($members AS $member): ?>
            <?php 
                // Get Meta for visibility otions
                $meta = get_user_meta($member->data->ID);
            ?>
            <a href="/members/<?php print $member->data->user_nicename; ?>" class="members__member-card">
                <div class="members__avatar">

                </div>
                <div class="members__member-info">
                    <div class="members__username"><?php print $member->data->user_nicename; ?></div>
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