<?php get_header(); ?>
  <div class="events__header">
    <div class="row middle-md event-creator__container">
      <div class="col-md-6 events__header__text">
        <h1 class="title"><?php the_title() ?></h1>
        <p class="events__text">*Optional information</p>
      </div>
    </div>
  </div>
  <div 
    class="content event-creator__container-main"
  >
    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content() ?>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
<?php get_footer(); ?>