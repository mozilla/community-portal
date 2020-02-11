<?php
    get_header();
    $p = intval(get_query_var('a')) <= 1 ? 1 : intval(get_query_var('a'));

    $activities_per_page = 12;

    $args = Array(
        'post_type' =>  'activity',
        'per_page'  =>  -1
    );

    $activity_count = 0;
    $activities = new WP_Query($args);

    $activity_count = sizeof($activities->posts);
    $offset = ($p - 1) * $activities_per_page;

    $activities = array_slice($activities->posts, $offset, $activities_per_page);
    $total_pages = ceil($activity_count / $activities_per_page);


?>
<div class="content">
    <div class="activities">
        <div class="activities__hero">
            <div class="activities__hero-container">
                <h1 class="activities__title"><?php print __("Activities"); ?></h1>
                <p class="activities__hero-copy">
                    <?php print __("Activities are “evergreen” because they’re always important, relevant to Mozilla’s mission and need your participation now!"); ?>
                </p>
            </div>
        </div>
        <div class="activities__container">
            <div class="activities__activities">
                <?php foreach($activities AS $activity): ?>
                <?php 
                    $activity_image = wp_get_attachment_url(get_post_thumbnail_id($activity->ID));
                    $activitiy_desc = get_field('card_description', $activity->ID);
                    $time_commitment = get_field('time_commitment', $activity->ID);
                ?>

                <a href="/activities/<?php print $activity->post_name; ?>" class="activities__card">
                    <div class="activities__activity-image" style="background-image: url('<?php print (strlen($activity_image) > 0) ? $activity_image : get_stylesheet_directory_uri().'/images/group.png'; ?>');">
                    </div>
                    <div class="activities__card-content">
                        <h2 class="activities__activity-title"><?php print str_replace('\\', '', stripslashes($activity->post_title)); ?></h2>
                        <div class="activities__copy-container">
                            <p class="activities__copy">
                                <?php 
                                    print $activitiy_desc;
                                ?>
                            </p>
                        </div>
                        <?php
                            $tags = get_the_tags($activity->ID);
                        ?>
                        <div class="activities__tag-container">
                            <?php if(is_array($tags) && sizeof($tags) > 0): ?>
                            <span class="activities__tag"><?php print $tags[0]->name; ?></span>
                            <?php endif; ?>
                            <?php if($time_commitment): ?>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.99992 14.6654C11.6818 14.6654 14.6666 11.6806 14.6666 7.9987C14.6666 4.3168 11.6818 1.33203 7.99992 1.33203C4.31802 1.33203 1.33325 4.3168 1.33325 7.9987C1.33325 11.6806 4.31802 14.6654 7.99992 14.6654Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 4V8L10.6667 9.33333" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="activities__time-commitment"><?php print $time_commitment; ?></span>
                            <?php endif; ?>

                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
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
                <div class="activities__pagination">
                    <div class="activities__pagination-container">
                        <?php if($total_pages > 1): ?>
                        <a href="/activities/?a=<?php print $previous_page?>" class="activities__pagination-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M17 23L6 12L17 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <?php if($page_min > 1): ?><a href="/activities/?a=1" class="activities__pagination-link activities__pagination-link--first"><?php print "1"; ?></a>&hellip; <?php endif; ?>
                        <?php for($x = $page_min - 1; $x < $page_max; $x++): ?>
                        <a href="/activities/?a=<?php print $x + 1; ?>" class="activities__pagination-link<?php if($p == $x + 1):?> activities__pagination-link--active<?php endif; ?><?php if($x === $page_max - 1):?> activities__pagination-link--last<?php endif; ?>"><?php print ($x + 1); ?></a>
                        <?php endfor; ?>
                        <?php if($total_pages > $range && $p < $total_pages - 1): ?>&hellip; <a href="/activities?p=<?php print $total_pages; ?>" class="activities__pagination-link<?php if($p === $total_pages):?> activities__pagination-link--active<?php endif; ?>"><?php print $total_pages; ?></a><?php endif; ?>
                        <a href="/activities/?a=<?php print $next_page; ?>" class="activities__pagination-link">
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
</div>
<?php 

    get_footer();

?>