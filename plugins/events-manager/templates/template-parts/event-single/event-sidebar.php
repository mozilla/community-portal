<div class="col-lg-4 col-sm-12 events-single__sidebar">
    <div>
        <div class="card events-single__attributes">
            <div class="row">
            <?php if (isset($external_url) && strlen($external_url) > 0 && filter_var($external_url, FILTER_VALIDATE_URL)): ?>
                <div class="col-lg-12 col-md-6 col-sm-12">
                    <p class="events-single__label"><?php echo __('Links') ?></p>
                    <p><a href="<?php echo esc_attr($external_url) ?>" class="events-single__externam-link"><?php echo __($external_url) ?></a></p>
                </div>
            <?php endif; ?>
            <?php if (is_array($categories)): ?>
                <div class="col-lg-12 col-md-6 col-sm-12">
                    <p class="events-single__label"><?php print __('Tags', 'community-portal'); ?></p>
                    <ul class="events-single__tags">
                        <?php foreach($categories as $category): ?>
                        <li class="tag"><a class="events-single__tag-link" href="/events/?tag=<?php print $category->name; ?>"><?php echo $category->name ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if($campaign): ?>
                <?php 
                    $c = get_post($campaign);
                ?>
                <div class="col-lg-12 col-md-6 col-sm-12">
                    <p class="events-single__label"><?php print __('Part of'); ?></p>
                    <a href="/campaigns/<?php print $c->post_name; ?>" class="events-single__externam-link events-single__externam-link--icon">
                        <?php print $c->post_title; ?>
                    </a>
                </div>
            <?php endif; ?>
            <div class="events-single__share col-lg-12 col-md-6 col-sm-12 <?php echo (!isset($campaign) && !isset($external_url) && !is_array($categories) && !strlen($external_url) > 0 ? esc_attr('only-share') : null )?>">
                <button id="open-events-share-lightbox" class="btn btn--light btn--share">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 9V15C3 15.3978 3.15804 15.7794 3.43934 16.0607C3.72064 16.342 4.10218 16.5 4.5 16.5H13.5C13.8978 16.5 14.2794 16.342 14.5607 16.0607C14.842 15.7794 15 15.3978 15 15V9M12 4.5L9 1.5M9 1.5L6 4.5M9 1.5V11.25" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php echo __('Share') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php include(locate_template('plugins/events-manager/templates/template-parts/event-single/event-host.php', false, false)); ?>