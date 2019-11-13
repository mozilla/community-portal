<div class="row">
  <?php
    global $wpdb, $current_user, $EM_Notices, $EM_Person;
    if( is_user_logged_in()):
      $user_id = get_current_user_id();
      $EM_Person = new EM_Person($user_id);
      $bookingArgs = array(
        'search' => 're',
      );
      $bookings = EM_Events::get($bookingArgs);
      $EM_Bookings = $EM_Person->get_bookings();
      var_dump(count($EM_Bookings->bookings));
      function compareArrays($item, $bookings) {
        foreach($bookings as $booking) {
          if ($booking->event_id === $item->event_id) {
            return $item;
          } 
        }
      }
      $matches = array_filter($EM_Bookings->bookings, function($item) use ($bookings) { return compareArrays($item, $bookings); });
      var_dump($matches);
      $bookings_count = count($EM_Bookings->bookings);
      if($bookings_count > 0) {
			  //Get events here in one query to speed things up
        $event_ids = array();
        ?>
        <?php 
          if ($args['search']) {
            $args['scope'] = '';
            $eventsArray = EM_Events::get($args);
        ?>
            <div class="col-sm-12 events__search-terms">
              <p><?php echo __("Results for '".$args['search']."'")?></p>
            </div>
          <?php
          }
        ?>
        <div class="row events__cards">
        <?php 
        foreach($EM_Bookings as $EM_Booking) {
          $id = $EM_Booking->event_id;
          $username = $EM_Person->display_name;
          $site_url = get_site_url();
          if (isset($eventsArray) && count($eventsArray) > 0) {
            ?>
            <?php
              foreach ($eventsArray as $eventItem) {
                if ($eventItem->event_id === $id) {
                  $event = $eventItem;
                  include(locate_template('plugins/events-manager/templates/template-parts/event-cards.php', false, false));
                }
              } 
          } else {
            $event = em_get_event($id);
            include(locate_template('plugins/events-manager/templates/template-parts/event-cards.php', false, false));
          }
        }
      } else {
        ?>
        <div class="events__zero-state col-sm-12">
          <p><?php echo ($args['search'] ? __('No results found. Please try another search term.') : __('You are not currently attending any events.')) ?></p>
        </div>
        <?php
      }
    ?>
    <?php else: ?>
      <p><?php echo sprintf(__('Please <a href="%s">Log In</a> to view your bookings.','events-manager'),site_url('wp-login.php?redirect_to=' . urlencode(get_permalink()), 'login'))?></p>
    <?php endif; ?>
  </div>
</div>
