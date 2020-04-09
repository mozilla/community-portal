<?php
    get_header();
    global $post;

    $campaign_image = get_the_post_thumbnail_url($post->ID);

    $campaign_status = get_field('campaign_status');
    $campaign_hero_cta = get_field('hero_cta');
    $campaign_hero_unsub_cta = get_field('hero_cta_unsub');
    $campaign_hero_cta_link = get_field('hero_cta_link');

    $campaign_start_date = get_field('campaign_start_date');
    $campaign_end_date = get_field('campaign_end_date');

	$campaign_content = get_field('campaign_content');
	
	$mailchimp_info = get_post_meta($post->ID, 'mailchimp-list-id', true);

    $tags = get_the_terms($post, 'post_tag');

    $mailchimp = get_post_meta($post->ID, 'mailchimp-list-id', true);
    $logged_in = mozilla_is_logged_in();

    $user = wp_get_current_user();
    $members_participating = get_post_meta($post->ID, 'members-participating', true);
    $sub = true;

    if($logged_in && is_array($members_participating) && in_array($user->ID, $members_participating)) {
        $sub = false;
    }

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
						<?php if(($campaign_hero_cta && $mailchimp && isset($mailchimp->id)) || is_preview()): ?>
							<?php if ($logged_in): ?>
								<a href="<?php print ($campaign_hero_cta_link) ? $campaign_hero_cta_link : '#'; ?>" class="campaign__hero-cta<?php if($mailchimp && isset($mailchimp->id) && $sub === true): ?> campaign__hero-cta--sub<?php else: ?> campaign__hero-cta--unsub<?php endif; ?>"<?php if($mailchimp && isset($mailchimp->id)): ?><?php if($mailchimp): ?> data-list="<?php print $mailchimp->id; ?>"<?php endif; ?><?php endif; ?> data-unsub-copy="<?php print $campaign_hero_unsub_cta; ?>" data-sub-copy="<?php print $campaign_hero_cta; ?>" data-campaign="<?php print $post->ID;?>"><?php print $sub ? $campaign_hero_cta : $campaign_hero_unsub_cta; ?></a>
							<?php else: ?>
								<a href="<?php print ($campaign_hero_cta_link) ? $campaign_hero_cta_link : '#'; ?>" class="campaign__hero-cta campaign__hero-cta--no-account" data-list="<?php print $mailchimp->id; ?>" data-campaign="<?php print $post->ID;?>"><?php print  $campaign_hero_cta ?></a>
							<?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="campaign__intro">
                <div class="campaign__intro-card">
                    <?php print $post->post_content; ?>
                    <hr class="campaign__keyline" />
                    <div class="campaign__share-container">
                        <div class="campaign__tag-container">
                            <?php print __('Tags', 'community-portal'); ?>
                            <?php if(is_array($tags) && sizeof($tags) > 0): ?>
                                <span class="campaign__tag"><?php print $tags[0]->name; ?></span>
                            <?php endif; ?>
                        </div>
                        <a href="#" class="campaign__share-cta">
                            <svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 9V15C1 15.3978 1.15804 15.7794 1.43934 16.0607C1.72064 16.342 2.10218 16.5 2.5 16.5H11.5C11.8978 16.5 12.2794 16.342 12.5607 16.0607C12.842 15.7794 13 15.3978 13 15V9M10 4.5L7 1.5M7 1.5L4 4.5M7 1.5V11.25" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?php print __('Share Campaign', 'community-portal'); ?>

                        </a>
						
                    </div>

                </div>
            </div>
            <div class="campaign__container">
                <?php if($campaign_content && is_array($campaign_content)): ?>
                <?php foreach($campaign_content AS $block): ?>
                <?php 
                    $theme_dir = get_template_directory();
                    switch($block['acf_fc_layout']) {
                        case 'text_1up_block':
                            include "{$theme_dir}/templates/blocks/text_1up_block.php";
                            break;
                        case 'text_2up_block':
                            include "{$theme_dir}/templates/blocks/text_2up_block.php";
                            break;
                        case 'text_3up_block':
                            include "{$theme_dir}/templates/blocks/text_3up_block.php";
                            break;
                        case 'text_image':
                            include "{$theme_dir}/templates/blocks/text_image_block.php";
                            break;
                        case 'text_card':
                            include "{$theme_dir}/templates/blocks/text_card_block.php";
                            break;
                        case 'events_block':
                            include "{$theme_dir}/templates/blocks/events_block.php";
                            break;
                        case 'video_block':
                            include "{$theme_dir}/templates/blocks/video_block.php";
                            break;
                        case 'imagery_block':
                            include "{$theme_dir}/templates/blocks/imagery_block.php";
                            break;            
                        case 'outro_cta_block':
                            include "{$theme_dir}/templates/blocks/outro_cta_block.php";
                            break;            
                    }
                ?>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="campaign-share-lightbox" class="lightbox">
        <?php include(locate_template('templates/share-modal.php', false, false)); ?>
    </div>
    <?php if(($campaign_hero_cta && !$logged_in && $mailchimp && isset($mailchimp->id)) || is_preview()): ?>
		<div id="campaign-rsvp-lightbox" class="lightbox">
			<?php include(locate_template('templates/campaign-rsvp.php', false, false)); ?>
		</div>
	<?php endif; ?>
    

<?php 
    get_footer();
?>
