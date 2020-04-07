<?php 
	get_header();
	$user = wp_get_current_user();
	$subscribed = get_user_meta($user->ID, 'newsletter', true);
	$subscribed = isset($subscribed) && strlen($subscribed) > 0 ? $subscribed : '';

	$p = intval(get_query_var('a')) <= 1 ? 1 : intval(get_query_var('a'));

    $campaigns_per_page = 12;

    $args = Array(
        'post_type' =>  'campaign',
        'posts_per_page'  =>  -1
    );

    $campaign_count = 0;
    $campaigns = new WP_Query($args);

    $current_campaign = get_field('current_active_campaign');
    if(!$current_campaign) {
        foreach($campaigns->posts AS $c) {
            $start = strtotime(get_field('campaign_start_date', $c->ID));
            $end = strtotime(get_field('campaign_end_date', $c->ID));
            $today = time();

            $status =  get_field('campaign_status', $c->ID);

            if(strtolower($status) !== 'closed') {
                if($start && !$end) {
                    if($today >= $start) {
                        $current_campaign = $c;
                        break;
                    }
                }
    
                if($start && $end) {
                    if($today >= $start && $today < $end) {
                        $current_campaign = $c;
                        break;
                    }
                }
            }

        }
    }
    
    if($current_campaign) {
        $current_campaign_image = get_the_post_thumbnail_url($current_campaign->ID);
        
        $current_campaign_status = get_field('campaign_status', $current_campaign->ID);
        $current_campaign_hero_cta = get_field('hero_cta', $current_campaign->ID);
        $current_campaign_hero_cta_link = get_field('hero_cta_link', $current_campaign->ID);
        
        $current_campaign_start_date = get_field('campaign_start_date', $current_campaign->ID);
        $current_campaign_end_date = get_field('campaign_end_date', $current_campaign->ID);
        $current_campaign_card_description = get_field('card_description', $current_campaign->ID);
        $current_campaign_tags = get_the_terms($current_campaign, 'post_tag');        
    }
    
    $incoming_campaign = get_field('incoming_campaign');
    if(!$incoming_campaign) {
        foreach($campaigns->posts AS $c) {
            $start = strtotime(get_field('campaign_start_date', $c->ID));
            $today = time();
            
            if($start > $today) {
                $incoming_campaign = $c;
                break;
            }
        }
    }
    
    if($incoming_campaign) {
        $incoming_campaign_image = get_the_post_thumbnail_url($incoming_campaign->ID);
        
        $incoming_campaign_status = get_field('campaign_status', $incoming_campaign->ID);
        $incoming_campaign_hero_cta = get_field('hero_cta', $incoming_campaign->ID);
        $incoming_campaign_hero_cta_link = get_field('hero_cta_link', $incoming_campaign->ID);
        
        $incoming_campaign_start_date = get_field('campaign_start_date', $incoming_campaign->ID);
        $incoming_campaign_end_date = get_field('campaign_end_date', $incoming_campaign->ID);
        $incoming_campaignn_card_description = get_field('card_description', $incoming_campaign->ID);
        $incoming_campaign_tags = get_the_terms($incoming_campaign, 'post_tag');        
	}
	
    $past_campaigns = Array();
    foreach($campaigns->posts  AS $c) {
        $s = get_field('campaign_status', $c->ID);
        $e = strtotime(get_field('campaign_end_date', $c->ID));
        $now = time();

        if(strtolower($s) === 'closed') {
            $past_campaigns[] = $c;
            continue;
        }

        if($e && $now > $e) {
            $past_campaigns[] = $c;
        }
    }

    $campaign_count = sizeof($past_campaigns);
    $offset = ($p - 1) * $campaigns_per_page;

    $campaigns = array_slice($past_campaigns, $offset, $campaigns_per_page);
    $total_pages = ceil($campaign_count / $campaigns_per_page);

?>
<div>
    <div class="campaigns">
        <div class="campaigns__hero">
            <div class="campaigns__hero-container">
                <h1 class="campaigns__title"><?php print __("Campaigns"); ?></h1>
                <p class="campaigns__hero-copy">
                    <?php print __("Campaigns are how we come together to solve big problems. Take a look at our active campaigns and join in!", 'community-portal'); ?>
                </p>
            </div>
        </div>
		<?php if ($incoming_campaign || $current_campaign): ?>
        <div class="campaigns__container">
            <?php if($current_campaign): ?>
            <div class="campaigns__active-campaign">
                <div class="campaigns__active-campaign-hero-container">
                    <div class="campaign__hero-image" style="background-image: url(<?php print $current_campaign_image; ?>);">
                    </div>
                    <div class="campaigns__active-campaign-title-container">
                        <div class="campaigns__active-campaign-status"><?php print $current_campaign_status; ?></div>
                        <h2 class="campaigns__active-campaign-title"><?php print $current_campaign->post_title?></h2>
                        <div class="campaigns__active-campaign-date-container">
                            <?php print $current_campaign_start_date; ?><?php if($current_campaign_end_date): ?> - <?php print $current_campaign_end_date; ?><?php endif; ?>
                        </div>
                        
                        <a href="/campaigns/<?php print $current_campaign->post_name; ?>" class="campaign__hero-cta"><?php print __('Get Involved', 'community-portal'); ?></a>
                        
                    </div>
                </div>
                <div class="campaigns__active-campaign-description">
                    <?php print $current_campaign_card_description; ?>
                </div>
                <?php if(is_array($current_campaign_tags) && sizeof($current_campaign_tags) > 0): ?>
                <div class="campaigns__active-campaign-tags">
                    <span class="campaigns__active-campaign-tag"><?php print $current_campaign_tags[0]->name; ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if($incoming_campaign): ?>
                <div class="campaigns__incoming-campaign-container">
                    <h2 class="campaigns__active-campaign-title"><?php print __("Campaign Incoming!"); ?></h2>
                    <p class="campaigns__incoming-campaign-copy"><?php print __('An extra cool Mozilla campaign is coming soon.  Keep an eye out for when it launches.', 'community-portal'); ?></p>
                    <div class="campaigns__active-campaign">
                    <div class="campaigns__active-campaign-hero-container">
                        <div class="campaign__hero-image" style="background-image: url(<?php print $incoming_campaign_image; ?>);">
                        </div>
                        <div class="campaigns__active-campaign-title-container">
                            <div class="campaigns__active-campaign-status"><?php print $incoming_campaign_status; ?></div>
                            <h2 class="campaigns__active-campaign-title"><?php print $incoming_campaign->post_title?></h2>
                            <div class="campaigns__active-campaign-date-container">
                                <?php print $incoming_campaign_start_date; ?><?php if($incoming_campaign_end_date): ?> - <?php print $incoming_campaign_end_date; ?><?php endif; ?>
                            </div>
                            <a href="/campaigns/<?php print $incoming_campaign->post_name; ?>" class="campaign__hero-cta campaign__hero-cta--secondary"><?php print __('Get Involved', 'community-portal'); ?></a>
                        </div>
                    </div>
                    <div class="campaigns__active-campaign-description">
                        <?php print $incoming_campaignn_card_description; ?>
                    </div>
                    <?php if(is_array($incoming_campaign_tags) && sizeof($incoming_campaign_tags) > 0): ?>
                    <div class="campaigns__active-campaign-tags">
                        <span class="campaigns__active-campaign-tag"><?php print $incoming_campaign_tags[0]->name; ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
		</div>
			<?php 
			endif; 
		endif;
		?>
		<?php 
			if (isset($subscribed) && intval($subscribed) !== 1):
				if ((!$current_campaign  && $incoming_campaign) || ($current_campaign && $incoming_campaign) || (!$current_campaign && !$incoming_campaign)):	
		?>
			<div class="newsletter <?php echo (!$current_campaign && !$incoming_campaign ? 'newsletter__solo' : '')?>">
				<?php include get_template_directory()."/templates/campaigns_newsletter.php"; ?>
			</div>
		<?php 
				endif;
			endif; 
		?>
		<div class="campaigns__container">
            <?php if(sizeof($campaigns) > 0): ?>
            <div class="campaigns__past-campaigns">
                <h2 class="campaigns__active-campaign-title"><?php print __("Past Campaigns"); ?></h2>
                <p class="campaigns__incoming-campaign-copy"><?php print __('Mozilla communities do great work together. These campaigns are over now but feel free to check out what everyone accomplished.'); ?></p>
            </div>
            <div class="campaigns__past-campaigns-container">
            <?php foreach($campaigns AS $campaign): ?>
            <?php 

                $campaign_image = get_the_post_thumbnail_url($campaign->ID);

                $campaign_status = get_field('campaign_status', $campaign->ID);
                $campaign_hero_cta = get_field('hero_cta', $campaign->ID);
                $campaign_hero_cta_link = get_field('hero_cta_link', $campaign->ID);

                $campaign_start_date = get_field('campaign_start_date', $campaign->ID);
                $campaign_end_date = get_field('campaign_end_date', $campaign->ID);
                $campaign_card_description = get_field('card_description', $campaign->ID);
                $campaign_tags = get_the_terms($campaign, 'post_tag');        

            ?>
            <a class="campaigns__campaign" href="/campaigns/<?php print $campaign->post_name; ?>">
                <div class="campaigns__active-campaign-hero-container campaigns__active-campaign-hero-container--card">
                    <div class="campaigns__past-campaign-hero">
                        <div class="campaign__hero-image campaign__hero-image--card" style="background-image: url(<?php print $campaign_image; ?>);">
                        </div>
                        <div class="campaigns__active-campaign-title-container campaigns__active-campaign-title-container--card">
                            <h2 class="campaigns__active-campaign-title campaigns__active-campaign-title--card"><?php print $campaign->post_title?></h2>
                            <div class="campaigns__active-campaign-date-container campaigns__active-campaign-date-container--card">
                                <?php print $campaign_start_date; ?><?php if($campaign_end_date): ?> - <?php print $campaign_end_date; ?><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="campaigns__active-campaign-description campaigns__active-campaign-description--card">
                        <?php print strip_tags($campaign_card_description); ?>
                    </div>
                </div>
                <?php if(is_array($campaign_tags) && sizeof($campaign_tags) > 0): ?>
                <div class="campaigns__active-campaign-tags campaigns__active-campaign-tags--card">
                    <span class="campaigns__active-campaign-tag"><?php print $campaign_tags[0]->name; ?></span>
                </div>
                <?php endif; ?>
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
            </div>
            <div class="campaigns__pagination">
                <div class="campaigns__pagination-container">
                    <?php if($total_pages > 1): ?>
                    <a href="/campaigns/?a=<?php print $previous_page?>" class="campaigns__pagination-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M17 23L6 12L17 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <?php if($page_min > 1): ?><a href="/campaigns/?a=1" class="campaigns__pagination-link campaigns__pagination-link--first"><?php print "1"; ?></a>&hellip; <?php endif; ?>
                    <?php for($x = $page_min - 1; $x < $page_max; $x++): ?>
                    <a href="/campaigns/?a=<?php print $x + 1; ?>" class="campaigns__pagination-link<?php if($p == $x + 1):?> campaigns__pagination-link--active<?php endif; ?><?php if($x === $page_max - 1):?> campaigns__pagination-link--last<?php endif; ?>"><?php print ($x + 1); ?></a>
                    <?php endfor; ?>
                    <?php if($total_pages > $range && $p < $total_pages - 1): ?>&hellip; <a href="/campaigns/?p=<?php print $total_pages; ?>" class="campaigns__pagination-link<?php if($p === $total_pages):?> campaigns__pagination-link--active<?php endif; ?>"><?php print $total_pages; ?></a><?php endif; ?>
                    <a href="/campaigns/?a=<?php print $next_page; ?>" class="campaigns__pagination-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M7 23L18 12L7 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php 
	if (($current_campaign && !$incoming_campaign) && (isset($subscribed) && intval($subscribed) !== 1)) {
?>
	<div class="newsletter newsletter--hero">
		<?php include get_template_directory()."/templates/campaigns_newsletter.php"; ?>
	</div>
	<?php 
	}

    get_footer();

?>