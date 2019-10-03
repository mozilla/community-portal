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
    include(locate_template('template-parts/events-filters.php', false, false));
    if ($view === 'attending') {
      bp_em_attending_content();
    } elseif ($view === 'organized'){
      em_locate_template('buddypress/my-events.php', true);
    } else {
      foreach($events as $event) {
      $url = $site_url.'/events/'.$event->slug;
        include(locate_template('template-parts/event-cards.php', false, false));
      }
    }
    ?>
    </div>