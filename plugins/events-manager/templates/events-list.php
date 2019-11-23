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
  $page = $_REQUEST['pno'];
  $args = apply_filters('em_content_events_args', $args);
  $args['pagination'] = '1';
  $view = get_query_var( 'view', $default = '');
  $country = urldecode(get_query_var('country', $default = 'all'));
  $tag = urldecode(get_query_var('tag', $default = 'all'));
  if ($view === 'past') {
    $args['scope'] = $view;
  }  else {
    $args['scope'] = 'future';
  }
  if ($country !== 'all') {
    $args['country'] = $country;
  } 
  if ($tag !== 'all') {
    $args['category'] = $tag;
  }
  $paginationLinks = $args;
  if (isset($page) && strlen($page)):
    $args['page'] = $page;
  endif;
  $args['limit'] = '0';
  $events = EM_Events::get($args);  
  $total_pages = count($events);
  $args['limit'] = '12';
  $events = EM_Events::get($args);  
?>
<div class="row events">
  <div class="events__nav__container">
    <ul class="col-md-12 center-md events__nav">
      <li class="events__nav__item">
        <a 
          class="events__nav__link <?php if ($view === 'future' | $view === "") echo esc_attr("events__nav__link--active") ?>" 
          href="<?php echo add_query_arg(array('view' => 'future', 'country' => $country, 'tag' => $tag), get_site_url('', 'events')) ?>"
        >
          <?php echo __('Upcoming Events'); ?>
        </a>
      </li>
      <?php if (is_user_logged_in()): ?>
      <li class="events__nav__item">
        <a 
          class="events__nav__link <?php if ($view === 'attending') echo esc_attr("events__nav__link--active") ?>" 
          href="<?php echo add_query_arg(array('view' => 'attending', 'country' => $country, 'tag' => $tag), get_site_url('', 'events'), get_site_url('','events')) ?>"
        >
          <?php echo __('Events I\'m attending') ?>
        </a>
      </li>
      <li class="events__nav__item">
        <a 
          class="events__nav__link <?php if ($view === 'organized') echo esc_attr("events__nav__link--active") ?>" 
          href="<?php echo add_query_arg(array('view' => 'organized', 'country' => $country, 'tag' => $tag), get_site_url('', 'events'), get_site_url('','events')) ?>"
        >
          <?php echo __('My Events'); ?>
        </a>
      </li>
      <?php endif; ?>
      <li class="events__nav__item">
        <a 
          class="events__nav__link <?php if ($view === 'past') echo esc_attr("events__nav__link--active") ?>" 
          href="<?php echo add_query_arg(array('view' => 'past', 'country' => $country, 'tag' => $tag), get_site_url('', 'events'), get_site_url('','events'))?>"
        >
          Past events
        </a>
      </li>
    </ul>
    <form class="events__nav--mobile" action="">
      <label class="events__nav__label--mobile" for="eventsView"><?php echo __('Showing:') ?></label>
      <select class="events__nav__options--mobile" name="eventsView" id="eventsView">
        <option <?php if ($view === 'future' || $view === '') echo esc_attr('selected') ?> value="future"><?php echo __('Upcoming Events') ?></option>
        <option <?php if ($view === 'attending') echo esc_attr('selected') ?> value="attending"><?php echo __('Events I\'m Attending') ?></option>
        <option <?php if ($view === 'organized') echo esc_attr('selected') ?> value="organized">Events I've Organized</option>
        <option <?php if ($view === 'past') echo esc_attr('selected') ?> value="past"><?php echo __('Past Events') ?></option>
      </select>
      <svg class="events__nav__icon" width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1.5 3.5L7 9L12.5 3.5" fill="white"/>
        <path d="M1.5 3.5L7 9L12.5 3.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </form>
  </div>
  <?php
    include(locate_template('plugins/events-manager/templates/template-parts/events-filters.php', false, false));
    ?>

      <?php
        if ($view === 'attending') {
          include(locate_template('plugins/events-manager/templates/my-bookings.php', false, false));
        } elseif ($view === 'organized'){
          include(locate_template('plugins/events-manager/buddypress/my-events.php', false, false));
        } else {
          if (count($events)):
            if ($args['search']):
              ?>
              <div class="col-sm-12 events__search-terms">
                <p><?php echo __('Results for "'.$args['search'].'"')?></p>
              </div>
              <?php
            endif;
            ?>
            <div class="row events__cards">
            <?php
            foreach($events as $event) {
              include(locate_template('plugins/events-manager/templates/template-parts/event-cards.php', false, false));
            }
            ?>
      <?php
        if ($total_pages > 12):
      ?>
      <div class="events__pagination col-sm-12">
      <?php 
        echo EM_Events::get_pagination_links($args, $total_pages, $search_action = 'search_events',$default_args = array());
      ?>
    </div>
    <?php
    endif;
    ?>
    <?php
          else:
            ?>
            <div class="events__zero-state col-sm-12">
              <p><?php echo ($args['search'] ? __('No results found. Please try another search term.') : __('There are currently no events.')) ?></p>
            </div>
            <?php
          endif;
        }
      ?>
    </div>
</div>