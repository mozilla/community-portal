<div class="row">
  <?php
    global $wpdb, $current_user, $EM_Notices, $EM_Person;
    if( is_user_logged_in()):
      $EM_Person = new EM_Person( get_current_user_id());
      $EM_Bookings = $EM_Person->get_bookings();
      $bookings_count = count($EM_Bookings->bookings);
      if($bookings_count > 0){
			  //Get events here in one query to speed things up
        $event_ids = array();
			  foreach($EM_Bookings as $EM_Booking) {
          $id = $EM_Booking->event_id;
          $username = $EM_Person->display_name;
          $site_url = get_site_url();
          $url = $site_url.'/members/'.$username.'/events/my-bookings/?event_id='.$id;
          if ($args['search']) {
            $args['scope'] = '';
            $eventsArray = EM_Events::get($args);
            if (count($eventsArray) > 0) {
              foreach ($eventsArray as $eventItem) {
              if ($eventItem->event_id === $id) {
                $event = $eventItem;
                include(locate_template('plugins/events-manager/templates/template-parts/event-cards.php', false, false));
              }
            } 
            }
          } else {
            $event = em_get_event($id);
            include(locate_template('plugins/events-manager/templates/template-parts/event-cards.php', false, false));
          }
        }
        $limit = ( !empty($_GET['limit']) ) ? $_GET['limit'] : 20;//Default limit
        $page = ( !empty($_GET['pno']) ) ? $_GET['pno']:1;
        $offset = ( $page > 1 ) ? ($page-1)*$limit : 0;
        echo $EM_Notices;
      }
    ?>
    <?php else: ?>
      <p><?php echo sprintf(__('Please <a href="%s">Log In</a> to view your bookings.','events-manager'),site_url('wp-login.php?redirect_to=' . urlencode(get_permalink()), 'login'))?></p>
    <?php endif; ?>
  </div>
</div>
