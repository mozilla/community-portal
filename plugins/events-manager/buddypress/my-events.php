
<?php
    global $wpdb, $current_user, $EM_Notices, $EM_Person;
    
    $args = apply_filters('em_content_events_args', $args);
    if(is_user_logged_in()):
        $user_id = get_current_user_id();
        $args['scope'] = 'all';
        $args['owner'] = $user_id;
        $args['status'] = false;
        $args['limit'] = '0';
        $total_bookings = count(EM_Events::get($args));
        $args['limit'] = '12';
        $EM_Events = EM_Events::get($args);
        $EM_Person = new EM_Person($user_id);
        $username = $EM_Person->display_name;
        $site_url = get_site_url();
        $country = urldecode(get_query_var('country', $default = 'all'));
        $tag = urldecode(get_query_var('tag', $default = 'all'));

        if(empty($EM_Events)) {
?>
    <div class="col-md-12 events__zero-state">
        <p class="events__search-terms"><?php echo ($args['search'] ? __('No results found. Please try another search term.', "community-portal") :__('You do not have any organized events.', "community-portal")) ?></p>
    </div>
<?php
        }else{

            if($args['search']):
?>
    <div class="col-sm-12 events__search-terms">
        <p><?php echo __('Results for "'.$args['search'].'"', "community-portal")?></p>
    </div>
<?php endif; ?>
    <div class="row events__cards">
        <?php
            foreach($EM_Events as $EM_Event){
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
    <p><?php echo sprintf(__('Please <a href="%s">Log In</a> to view your bookings.', "community-portal"),site_url('wp-login.php?redirect_to=' . urlencode(get_permalink()), 'login'))?></p>
    <?php endif; ?>
    <?php if ($total_bookings > 12): ?>
    <div class="events__pagination col-sm-12">
        <?php
            echo EM_Events::get_pagination_links($args, $total_bookings, $search_action = 'search_events',$default_args = array());
        ?>
    </div>
    <?php endif; ?>
</div>
