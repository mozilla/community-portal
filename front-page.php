<?php get_header(); ?>
    <div class="content">
      <?php 
        $heroTitle = get_field('hero_title');
        $heroSubTitle = get_field('hero_subtitle');
        $heroImage = get_field('hero_image');
      ?>
      <div class="homepage container">
        <div class="row homepage__hero">
          <div class="col-md-4 col-sm-offset-1 homepage__hero__image">
            <img src="<?php echo $heroImage ?>" alt="">
          </div>
          <div class="col-md-5 col-md-offset-1">
            <h1 class="homepage__hero__title title title--main"><?php echo preg_replace('/\b(Community)\b/i', '<span>$0</span>', $heroTitle); ?></h1>
            <p class="homepage__hero__subtitle subtitle"><?php echo $heroSubTitle ?></p>
            <a class="btn btn--dark btn--submit btn--small homepage__hero__cta"><?php echo __('Get Started') ?></a>
          </div>
        </div>
      </div>
    </div>
<?php get_footer(); ?>