<?php 
    get_header();     
    $options = wp_load_alloptions();
    $theme_directory = get_template_directory_uri();
?>
    <div class="content">
        <div class="not-found">
            <div class="not-found__container">
                <div class="not-found__content">
                    <div class="not-found__404">404</div>
                    <h1 class="not-found__title"><?php if(isset($options['error_404_title']) && strlen($options['error_404_title']) > 0): ?> <?php print __($options['error_404_title']); ?><?php else: ?><?php print __('Page Not Found', 'community-portal'); ?><?php endif; ?></h1>
                    <p class="not-found__copy">
                        <?php if(isset($options['error_404_copy']) && strlen($options['error_404_copy']) > 0): ?>
                            <?php print __($options['error_404_copy']); ?>
                        <?php else: ?>  
                            <?php print __('Ooops, looks like the page you are trying to reach is no longer available. If you are having trouble locating a destination on Community Portal, try visiting the home page.', 'community-portal'); ?>
                        <?php endif; ?>
                    </p>
                    <a href="/" class="not-found__cta"><?php print __("Return Home"); ?></a>
                </div>
                <div class="not-found__image-container"> 
                    <img src="<?php print $theme_directory; ?>/images/404.png" class="not-found__image" />
                </div>
            <div>
        </div>
    </div>
<?php get_footer(); ?>