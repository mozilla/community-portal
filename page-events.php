<?php get_header(); ?>
    <div class="events__header">
        <div class="row middle-md events__container">
            <div class="col-md-6 events__header__text">
                <h1 class="events__title"><?php the_title() ?></h1>
                <p class="events__text"><?php _e('Ready to join the movement? Check out what\'s happening soon in your area. ', 'community-portal'); ?></p>
                <p class="events__text"><?php _e('Explore community events near you, ', 'community-portal'); ?><a href="<?php echo add_query_arg(array('action' => 'edit'), get_site_url('','events/edit-event'))?>"><?php _e('organize your own!', 'community-portal'); ?></a></p>
            </div>
        </div>
    </div>
    <div class="content events__container">
        <?php if(have_posts()) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <?php the_content() ?>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
<?php get_footer(); ?>