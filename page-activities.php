<?php
    get_header();

    $activities_per_page = 12;
    $p = (isset($_GET['page'])) ? intval($_GET['page']) : 1;
    
    $args = Array(
        'post_type' =>  'activity',
        'per_page'  =>  -1
    );

    $activity_count = 0;
    $activities = new WP_Query($args);

    $activity_count = sizeof($activities->posts);
    $offset = ($p - 1) * $groups_per_page;

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
                ?>

                <a href="/activities/<?php print $activity->post_name; ?>" class="activities__card">
                    <div class="activities__activity-image" style="background-image: url('<?php print (strlen($activity_image) > 0) ? $activity_image : get_stylesheet_directory_uri().'/images/group.png'; ?>');">
                    </div>
                    <div class="activities__card-content">
                        <h2 class="activities__activity-title"><?php print str_replace('\\', '', stripslashes($activity->post_title)); ?></h2>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php 

    get_footer();

?>