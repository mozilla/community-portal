<?php
    /*
     * Default Events List Template
     * This page displays a list of events, called during the em_content() if this is an events list page.
     * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
     * You can display events however you wish, there are a few variables made available to you:
     *
     * $args - the args passed onto EM_Events::output()
     *
     */
    ?>
    <?php
    $args = apply_filters('em_content_events_args', $args);
    $view = get_query_var( 'view', $default = '');
    if ($view === 'past') {
      $args['scope'] = $view;
    }  else {
      $args['scope'] = 'future';
    }
    $events = EM_Events::get($args);
    $months = array(
      '01' => 'Jan',
      '02' => 'Feb',
      '03' => 'Mar',
      '04' => 'Apr',
      '05' => 'May',
      '06' => 'Jun',
      '07' => 'Jul',
      '08' => 'Aug',
      '09' => 'Sep',
      '10' => 'Oct',
      '11' => 'Nov',
      '12' => 'Dec',
    );
    ?>
    <div class="row events">
    <ul class="col-md-12 center-md events__nav">
      <li class="events__nav__item"><a class="events__nav__link <?php if ($view === 'future' | $view === "") echo "events__nav__link--active" ?>" href="<?php echo add_query_arg('view', 'future', get_site_url('', 'events')) ?>">Upcoming Events</a></li>
      <li class="events__nav__item"><a class="events__nav__link <?php if ($view === 'attending') echo "events__nav__link--active" ?>" href="<?php echo add_query_arg('view', 'attending', get_site_url('','events')) ?>">Events I'm attending</a></li>
      <li class="events__nav__item"><a class="events__nav__link <?php if ($view === 'organized') echo "events__nav__link--active" ?>" href="<?php echo add_query_arg('view', 'organized', get_site_url('','events')) ?>">Events I've organized</a></li>
      <li class="events__nav__item"><a class="events__nav__link <?php if ($view === 'past') echo "events__nav__link--active" ?>" href="<?php echo add_query_arg('view', 'past', get_site_url('','events'))?>">Past events</a></li>
      <!-- <li class="events__nav__item"><a class="events__nav__link " href="<?php echo add_query_arg(array('view' => 'organized', 'action' => 'edit'), get_site_url('','events')) ?>">Create an event</a></li> -->
    </ul>
    <?php
    if ($view === 'attending') {
      bp_em_attending_content();
    } elseif ($view === 'organized'){
      em_locate_template('buddypress/my-events.php', true);
    } else {
      for ($i = 0; $i < count($events); $i = $i + 1) {
        $location = em_get_location($events[$i]->location_id);
        ?>
          <div class="col-md-4">
          <div class="card">
          <!-- <a href="<?php echo get_site_url('', $events[$i]->slug)?>"> -->
          <div class="card__image">
            <?php 
                $imgUrl = wp_get_attachment_url( get_post_thumbnail_id($events[$i]->post_id));
                if ($imgUrl) {
                  ?>
                  <img src="<?php echo $imgUrl ?>" alt="">
                  <?php
                }
              ?>
              <?php 
                $month = substr($events[$i]->start_date, 5, 2);
                $date = substr($events[$i]->start_date, 8, 2);
                $year = substr($events[$i]->start_date, 0, 4);
              ?>
              <p class="card__image__date"><span><?php echo $months[$month] ?> </span><span><?php echo $date ?></span></p>
            </div>
            <div class="card__description">
              <h2><?php echo $events[$i]->event_name; ?></h2>
              <p><?php echo $months[$month].' '.$date.', '.$year.' @ '.substr($events[$i]->event_start_time, 0, 5).' - '.substr($events[$i]->event_end_time, 0, 5).' '.$events[$i]->event_timezone; ?></p>
              <p class="text--light text--small">
              <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
                <?php
                if ($location->address) {
                  echo $location->address.' - '; 
                }
                if ($location->town) {
                  echo $location->town;
                  if ($location->country) {
                    echo ', '.$location->country;
                  }
                } else {
                  echo $location->country;
                }
              ?></p>
              <?php 
                $tags = get_the_terms($events[$i]->post_id, EM_TAXONOMY_CATEGORY);
                if ($tags) {
                  ?>
                  <ul class="events__tags">
                    <?php
                      for ($j=0; $j < count($tags); $j = $j + 1) {
                        ?>
                        <li class="tag"><?php echo $tags[$j]->name ?></li>
                        <?php
                      }
                    ?>
                    </ul>
                  <?php
                }
              ?>
            </div>
            <!-- </a> -->
          </div>
          </div>
        <?php
      }
    }
    ?>
    </div>