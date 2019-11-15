<?php 
  $categories = $event->get_categories();
  $location = em_get_location($event->location_id);
  $site_url = get_site_url();
  $url = $site_url.'/events/'.$event->slug;  
?> 
<div class="col-lg-4 col-md-6 events__column">
  <div class="event-card">
    <a class="events__link" href="<?php echo $url?>">
      <div class="event-card__image"
        <?php 
          $event_meta = get_post_meta($event->post_id, 'event-meta');
          $img_url = $event_meta[0]->image_url;
          if ($img_url && $img_url !== '') {
        ?>
          style="background-image: url(<?php echo $img_url ?>)"
        <?php
          }
        ?>
        >
        <?php 
          $month = substr($event->start_date, 5, 2);
          $date = substr($event->start_date, 8, 2);
          $year = substr($event->start_date, 0, 4);
        ?>
        <p class="event-card__image__date"><span><?php echo __(substr($months[$month],0,3)) ?> </span><span><?php echo __($date) ?></span></p>
      </div>
      <div class="event-card__description">
        <h3 class="event-card__description__title title--event-card"><?php echo $event->event_name; ?></h2>
        <p><?php echo __($months[$month].' '.$date.', '.$year.' @ '.substr($event->event_start_time, 0, 5).' - '.substr($event->event_end_time, 0, 5).' '.$event->event_timezone); ?></p>
        <?php if (strlen($location->address) > 0 || strlen($location->town) > 0 || strlen($location->country) > 0):
        ?>
        <div class="event-card__location">
          <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <p class="text--light text--small">
            <?php
              if ($location->address) {
                echo __($location->address.' - '); 
              }
              if ($location->town) {
                echo __($location->town);
                if ($location->country) {
                  echo __(', '.$allCountries[$location->country]);
                }
              } else {
                echo __($allCountries[$location->country]);
              }
            ?>
          </p>
        </div>
        <?php 
          endif;
        ?>
      </div>
      <ul class="events__tags">
        <?php
          if (is_array($categories->terms)): 
            if (count($categories->terms) <= 2): 
              foreach($categories->terms as $category) {
        ?>
            <li class="tag"><?php echo __($category->name); ?></li>
        <?php
          }
          elseif (count($categories->terms) > 0):
            $i = 0;
            foreach ($categories->terms as $category) {
            ?>
              <li class="tag"><?php echo __($category->name) ?></li>
            <?php
              $i = $i + 1;
              if ($i === 2) {
                break;
              }
            }
            ?>
            <li class="tag"><?php echo __('+'); echo count($categories->terms) - 2; echo __(' more tag(s)'); ?></li>        
            <?php
          endif;
        endif;
        ?>
      </ul>
    </a>
  </div>
</div>