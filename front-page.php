<?php get_header(); ?>
    <div class="content">
      <?php 
        $fields = array(
          'hero_title',
          'hero_subtitle',
          'hero_image',
          'hero_cta_existing',
          'hero_cta_new',
          'hero_cta_text',
          'featured_events',
          'featured_events_title',
          'featured_events_cta_text',
          'featured_events_secondary_cta_text'
        );
        $fieldValues = new stdClass();
        foreach ($fields as $field) {
          $fieldValues->$field = get_field($field);
        }
      ?>
      <div class="homepage homepage__container">
        <div class="row homepage__hero">
          <div class="col-md-4 col-sm-offset-1 homepage__hero__image">
            <img src="<?php echo $fieldValues->hero_image['url'] ?>" alt="<?php echo $fieldValues->hero_image['alt'] ?>">
          </div>
          <div class="col-md-5 col-md-offset-1 homepage__hero__text">
            <h1 class="homepage__hero__title title title--main"><?php echo preg_replace('/\b(Community)\b/i', '<span>$0</span>', $fieldValues->hero_title); ?></h1>
            <p class="homepage__hero__subtitle subtitle"><?php echo $fieldValues->hero_subtitle ?></p>
            <a href="<?php echo (is_user_logged_in() ? esc_attr($fieldValues->hero_cta_existing) : esc_attr($fieldValues->hero_cta_new)) ?>"class="btn btn--dark btn--small homepage__hero__cta"><?php echo __($fieldValues->hero_cta_text) ?></a>
          </div>
        </div>
        <div class="homepage__events">
          <div class="homepage__events__background"></div>
          <div class="row homepage__events__meta">
            <div class="col-md-6 col-sm-12">
              <h2 class="subheader homepage__events__subheader"><?php echo __($fieldValues->featured_events_title)?></h2>
            </div>
            <div class="col-md-6 col-sm-12 homepage__events__cta">
              <a href="/events" class="btn btn--small btn--dark"><?php echo __($fieldValues->featured_events_cta_text); ?></a>
            </div>
          </div>
          <div class="row homepage__events__grid">
            <?php 
              foreach($fieldValues->featured_events as $featured_event):
                $event = EM_Events::get(array('post_id' => $featured_event['single_event']->ID));
                $event = $event[0];
                include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));
              endforeach;
            ?>
            <div class="col-lg-4 col-md-6 events__column homepage__events__count">
              <?php 
                $eventsTotal = count(EM_Events::get());
                if ($eventsTotal > 15 && $eventsTotal <= 105):
                  $eventsTotal = floor(($eventsTotal / 15)) * 15;
                  $eventsTotal .= '+';
                elseif ($eventsTotal > 105 && $eventsTotal <= 1005):
                  $eventsTotal = floor(($eventsTotal) / 105) * 105;
                  $eventsTotal .= '+';
                elseif ($eventsTotal > 1005): 
                  $eventsTotal = floor(($eventsTotal / 1005) * 1005);
                  $eventsTotal .= '+';
                else: 
                  $eventsTotal = $eventsTotal - 5;
                endif;
              ?>
              <p>
                <span class="large-number homepage__events__count__span"><?php echo __($eventsTotal) ?></span>
                <?php echo __('More Events.')?>
                <a href="/events" class="homepage__events__count__link"><?php echo __($fieldValues->featured_events_secondary_cta_text) ?></a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php get_footer(); ?>