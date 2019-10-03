<?php
	global $wpdb, $bp, $EM_Notices;
	/* @var $args array */
	/* @var $EM_Events array */
	/* @var $events_count int */
	/* @var $future_count int */
	/* @var $past_count int */
	/* @var $pending_count int */
	/* @var $url string */
	/* @var $show_add_new bool */
	/* @var $limit int */
	//add new button will only appear if called from em_event_admin template tag, or if the $show_add_new var is set
	?>
		<?php
      $EM_Person = new EM_Person( get_current_user_id() );
      $username = $EM_Person->display_name;
      $site_url = get_site_url();
			echo $EM_Notices;
			if(!empty($show_add_new) && current_user_can('edit_events')) echo '<a class="em-button button add-new-h2" href="'.em_add_get_params($_SERVER['REQUEST_URI'],array('action'=>'edit','scope'=>null,'status'=>null,'event_id'=>null, 'success'=>null)).'">'.__('Add New','events-manager').'</a>';
		?>
				
			<?php
			if ( empty($EM_Events) ) {
				echo get_option ( 'dbem_no_events_message' );
			} else {
			?>
					
      <?php
          foreach ( $EM_Events as $EM_Event ) {
            $id = $EM_Event->event_id;
            $url = $site_url.'/members/'.$username.'/events/my-events/?action=edit&event_id='.$id;
            $event = em_get_event($id);
            include(locate_template('template-parts/event-cards.php', false, false));
          }
      }
      $limit = ( !empty($_GET['limit']) ) ? $_GET['limit'] : 20;//Default limit
      $page = ( !empty($_GET['pno']) ) ? $_GET['pno']:1;
      $offset = ( $page > 1 ) ? ($page-1)*$limit : 0;
      echo $EM_Notices;
    ?>
	</div>

