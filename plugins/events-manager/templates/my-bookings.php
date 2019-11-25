<div class="row">
  <?php
    global $wpdb, $current_user, $EM_Notices, $EM_Person;
    if( is_user_logged_in()):
      $args['limit'] = '0';
      $args['bookings'] = 'user';
      $totalBookings = count(EM_Events::get($args));
      $args['limit'] = '12';
      $bookings = EM_Events::get($args);
      if(isset($bookings) && is_array($bookings) && count($bookings) > 0) {
        if ($args['search']) {
      ?>
        <div class="col-sm-12 events__search-terms">
          <p><?php echo __("Results for '".$args['search']."'")?></p>
        </div>
      <?php
      }
    ?>
    <div class="row events__cards">
      <?php 
        foreach($bookings as $event) {
          $id = $booking->event_id;
          $username = $EM_Person->display_name;
          $site_url = get_site_url();
          include(locate_template('plugins/events-manager/templates/template-parts/event-cards.php', false, false));
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
      <p><?php echo sprintf(__('Please log n to create or join events','events-manager'),site_url('wp-login.php?redirect_to=' . urlencode(get_permalink()), 'login'))?></p>
    <?php endif; ?>
  </div>
  <?php 
    if ($totalBookings > 12):
  ?>
  <div class="events__pagination col-sm-12">
    <?php 
      echo EM_Events::get_pagination_links($args, $totalBookings, $search_action = 'search_events',$default_args = array());
    ?>
  </div>
    <?php 
    endif;
    ?>
</div>
