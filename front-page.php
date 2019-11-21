<?php get_header(); ?>
    <div class="content">
      <?php 
        $fields = array(
          'hero_title',
          'hero_subtitle',
          'hero_image',
          'hero_cta_existing',
          'hero_cta_new',
          'hero_cta_text'
        );
        $fieldValues = new stdClass();
        foreach ($fields as $field) {
          $fieldValues->$field = get_field($field);
        }
      ?>
      <div class="homepage container">
        <div class="row homepage__hero">
          <div class="col-md-4 col-sm-offset-1 homepage__hero__image">
            <img src="<?php echo $fieldValues->hero_image['url'] ?>" alt="<?php echo $fieldValues->hero_image['alt'] ?>">
          </div>
          <div class="col-md-5 col-md-offset-1">
            <h1 class="homepage__hero__title title title--main"><?php echo preg_replace('/\b(Community)\b/i', '<span>$0</span>', $fieldValues->hero_title); ?></h1>
            <p class="homepage__hero__subtitle subtitle"><?php echo $fieldValues->hero_subtitle ?></p>
            <a href="<?php echo (is_user_logged_in() ? esc_attr($fieldValues->hero_cta_existing) : esc_attr($fieldValues->hero_cta_new)) ?>"class="btn btn--dark btn--submit btn--small homepage__hero__cta"><?php echo __($fieldValues->hero_cta_text) ?></a>
          </div>
        </div>
      </div>
    </div>
<?php get_footer(); ?>