<?php 
    get_header();     
    $options = wp_load_alloptions();
    
?>
    <div class="content">
        <div class="not-found">
            <div class="not-found__container">
                <h1 class="not-found__title">404: <?php if(isset($options['error_404_title']) && strlen($options['error_404_title']) > 0): ?> <?php print __($options['error_404_title']); ?><?php else: ?><?php print __('Page Not Found'); ?><?php endif; ?></h1>
                <p class="not-found__copy">
                    <?php if(isset($options['error_404_copy']) && strlen($options['error_404_copy']) > 0): ?>
                        <?php print __($options['error_404_copy']); ?>
                    <?php else: ?>  
                        <?php print __('We could not find the page you are looking for.'); ?>
                    <?php endif; ?>
                </p>
            <div>
        </div>
    </div>
<?php get_footer(); ?>