
<?php
    global $wpdb, $current_user, $EM_Notices, $EM_Person;
    $args = apply_filters('em_content_events_args', $args);
    if( is_user_logged_in()):
      $user_id = get_current_user_id();
      if ($args['search']) {
        $EM_Events = EM_Events::get(array('scope'=>'all', 'search' => $args['search'], 'owner'=>$user_id, 'status'=>false));
      } else {
        $EM_Events = EM_Events::get(array('scope'=>'all','owner'=>$user_id, 'status'=>false));
      }
      $EM_Person = new EM_Person($user_id);
      $username = $EM_Person->display_name;
      $site_url = get_site_url();
      $country = urldecode(get_query_var('country', $default = 'all'));
      $tag = urldecode(get_query_var('tag', $default = 'all'));
      if(!empty($show_add_new) && current_user_can('edit_events')) echo '<a class="em-button button add-new-h2" href="'.em_add_get_params($_SERVER['REQUEST_URI'],array('action'=>'edit','scope'=>null,'status'=>null,'event_id'=>null, 'success'=>null)).'">'.__('Add New','events-manager').'</a>';
      ?>
      <?php
        if ( empty($EM_Events) ) {
        ?>
          <div class="col-md-12 events__zero-state">
            <p class="events__search-terms"><?php echo ($args['search'] ? __('No results found. Please try another search term.') :__('You do not have any organized events.')) ?></p>
        </div>
        <?php
        } else {
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
        foreach ( $EM_Events as $EM_Event ) {
          $id = $EM_Event->event_id;
          $url = $site_url.'/'.$EM_Event->event_slug;
          $event = em_get_event($id);
            include(locate_template('plugins/events-manager/templates/template-parts/event-cards.php', false, false));
          }
        }
        $limit = ( !empty($_GET['limit']) ) ? $_GET['limit'] : 20;//Default limit
        $page = ( !empty($_GET['pno']) ) ? $_GET['pno']:1;
        $offset = ( $page > 1 ) ? ($page-1)*$limit : 0;
        echo $EM_Notices;
      ?>
    </div>
    <?php else: ?>
      <p><?php echo sprintf(__('Please <a href="%s">Log In</a> to view your bookings.','events-manager'),site_url('wp-login.php?redirect_to=' . urlencode(get_permalink()), 'login'))?></p>
    <?php endif; ?>
  </div>
