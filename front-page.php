<?php get_header(); ?>
  <div class="content container">
    <div class="homepage__hero row">
      <div class="homepage__hero__image col-md-6">
        <img src="<?php print get_stylesheet_directory_uri(); ?>/images/homepage-hero.jpg" alt="">
      </div>
      <div class="col-md-6">
        <h1><?php echo __('Welcome to the new ')?><span><?php echo __('Community') ?></span><?php echo __(' Portal') ?></h1>
        <p><?php echo __('This intro is an opportunity to inform new visitors of the portal– e.g. what it is, why people should join, the impact of the community, and/or exciting stats.') ?></p>
        <button class="btn btn--dark btn--submit"><?php echo __('Get Started') ?></button>
      </div>
    </div>
  </div>
<?php get_footer(); ?>