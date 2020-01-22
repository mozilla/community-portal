<?php
    get_header();
    global $post;

    $campaign_image = get_the_post_thumbnail_url($post->ID);

    $campaign_status = get_field('campaign_status');
    $campaign_hero_cta = get_field('hero_cta');
    $campaign_hero_cta_link = get_field('hero_cta_link');

    $campaign_start_date = get_field('campaign_start_date');
    $campaign_end_date = get_field('campaign_end_date');

    $tags = get_the_terms($post, 'post_tag');

?>
    <div class="content">
        <div class="campaign">
            <div class="campaign__hero">
                <div class="campaign__hero-container">
                    <div class="campaign__hero-image" style="background-image: url(<?php print $campaign_image; ?>);">
                    </div>
                    <div class="campaign__hero-content-container">
                        <span class="campaign__status"><?php print $campaign_status; ?></span>
                        <h1 class="campaign__hero-title"><?php print $post->post_title; ?></h1>
                        <div class="campaign__date-container">
                            <?php print $campaign_start_date; ?>
                            <?php if($campaign_end_date): ?>
                            - <?php print $campaign_end_date; ?>
                            <?php endif; ?>
                        </div>
                        <?php if($campaign_hero_cta): ?>
                        <a href="<?php print ($campaign_hero_cta_link) ? $campaign_hero_cta_link : '#'; ?>" class="campaign__hero-cta"><?php print $campaign_hero_cta; ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="campaign__intro">
                <div class="campaign__intro-card">
                    <?php print $post->post_content; ?>
                    <hr class="campaign__keyline" />
                    <div class="campaign__share-container">
                        <?php print __('Tags'); ?>
                        <?php if(sizeof($tags) > 0): ?>
                            <span class="campaign__tag"><?php print $tags[0]->name; ?></span>
                        <?php endif; ?>
                        <a href="#"><?php print __('Share Campaign'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php 
    get_footer();
?>
