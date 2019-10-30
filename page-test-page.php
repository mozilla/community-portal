<?php get_header(); ?>
    <div class="content events__container">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
          <?php the_content() ?>

          <div class="row">
            <?php 
              $events = EM_Events::get();
              foreach($events as $event) {
                include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));
              }
            ?>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
<?php get_footer(); ?>