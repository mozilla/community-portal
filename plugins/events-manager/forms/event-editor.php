<?php
/* WARNING! This file may change in the near future as we intend to add features to the event editor. If at all possible, try making customizations using CSS, jQuery, or using our hooks and filters. - 2012-02-14 */
/* 
 * To ensure compatability, it is recommended you maintain class, id and form name attributes, unless you now what you're doing. 
 * You also must keep the _wpnonce hidden field in this form too.
 */
global $EM_Event, $EM_Notices, $bp, $EM_Ticket;
mozilla_match_categories();
$event_id = $_REQUEST['event_id'];
if (isset($event_id)):
  $event_meta = get_post_meta($EM_Event->post_id, 'event-meta');
  $external_url = $event_meta[0]->external_url;
  $event_campaign = $event_meta[0]->campaign;
  endif;
//check that user can access this page
if(is_object($EM_Event) && !$EM_Event->can_manage('edit_events','edit_others_events') ){
	?>
	<div class="event-creator wrap"><h2><?php esc_html_e('Unauthorized Access','events-manager'); ?></h2><p><?php echo sprintf(__('You do not have the rights to manage this %s.','events-manager'),__('Event','events-manager')); ?></p></div>
	<?php
	return false;
}elseif( !is_object($EM_Event) ){
	$EM_Event = new EM_Event();
}
$required = apply_filters('em_required_html','<i>*</i>');

echo $EM_Notices;
//Success notice
if( !empty($_REQUEST['success']) ){
	if(!get_option('dbem_events_form_reshow')) return false;
}
?>	
<form enctype='multipart/form-data' id="event-form" novalidate class="em-event-admin-editor <?php if( $EM_Event->is_recurring() ) echo 'em-event-admin-recurring' ?>" method="post" action="<?php echo esc_url(add_query_arg(array('success'=>null))); ?>">
<?php print wp_nonce_field('protect_content', 'my_nonce_field'); ?>

	<div class="wrap event-creator">
		<?php do_action('em_front_event_form_header', $EM_Event); ?>
		<?php if(get_option('dbem_events_anonymous_submissions') && !is_user_logged_in()): ?>
			<h3 class="event-form-submitter"><?php esc_html_e( 'Your Details', 'events-manager'); ?></h3>
			<div class="inside event-form-submitter">
				<div class="event-creator__container">
					<label class="event-creator__label"><?php esc_html_e('Name', 'events-manager'); ?></label>
					<input class="event-creator__input"type="text" name="event_owner_name" id="event-owner-name" value="<?php echo esc_attr($EM_Event->event_owner_name); ?>" />
        </div>
				<div class="event-creator__container">
					<label class="event-creator__label"><?php esc_html_e('Email', 'events-manager'); ?></label>
					<input type="text" name="event_owner_email" id="event-owner-email" value="<?php echo esc_attr($EM_Event->event_owner_email); ?>" />
        </div>
				<?php do_action('em_front_event_form_guest'); ?>
				<?php do_action('em_font_event_form_guest'); //deprecated ?>
			</div>
		<?php endif; ?>
		<div class="inside event-form-name event">
      <div class="event-creator__container">
        <label class="event-form-name event-creator__label" for="event-name"><?php esc_html_e( 'Event Name', 'events-manager'); ?></label>
        <input class="event-creator__input event-creator__input" type="text" name="event_name" id="event-name" required value="<?php echo esc_attr($EM_Event->event_name,ENT_QUOTES); ?>" />
      </div>
      <?php if( $EM_Event->can_manage('upload_event_images','upload_event_images') ): ?>
			<?php em_locate_template('forms/event/featured-image-public.php',true); ?>
		<?php endif; ?>
      <?php 
				em_locate_template('forms/event/when.php',true);
    ?>
    <div class="inside event-form-where">
      <?php
        if ( ! is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ): 
          em_locate_template('forms/event/location-moz.php',true);           
        else:
          em_locate_template('forms/event/location.php',true); 
        endif
      ?>
    </div>
    </div> 	
  </div>
  <?php if ( ! is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ): ?>
  <div class="wrap event-creator">
    <div class="event-editor">
      <label class="event-form-details event-creator__label" for="event-description"><?php esc_html_e( 'Event description', 'events-manager'); ?></label>
      <textarea name="content" id="event-description" placeholder="Add in the details of your event’s agenda here. If this is a multi-day event, you can add in the details of each day’s schedule and start/end time." rows="10" id="event-description" class="event-creator__input event-creator__textarea" style="width:100%" required><?php echo __($EM_Event->post_content) ?></textarea>
      <div class="event-creator__container">
          <label class="event-creator__label" for="event-campaign"><?php _e ( 'Is this event part of an initiative?', 'events-manager')?></label>
          <select class="event-creator__dropdown" id="event-campaign" name="event_campaign">
            <option value="No" <?php echo (!isset($event_campaign) || $event_campaign === '') ? esc_attr('selected') : null;?>><?php _e('No','events-manager'); ?></option>
            <option value="1" <?php echo ($event_campaign === '1') ? esc_attr('selected') : null; ?>>Firefox For Good</option>
        </select>
      </div>
      <?php if(get_option('dbem_categories_enabled')) { em_locate_template('forms/event/categories-public.php',true); }  ?>
      <div class="event-creator__container">
        <label class="event-creator__label" for="event-creator-link"><?php esc_html_e('External link URL*', 'events-manager'); ?></label>
        <input type="text" class="event-creator__input" name="event_external_link" id="event-creator-link" value="<?php echo (isset($external_url) && $external_url !== '') ? esc_attr($external_url) : '' ;?>" />
      </div>
      <?php em_locate_template('forms/event/group.php',true); ?>
    </div>
  </div>
  <?php endif; ?>
  <div class="event-creator__hidden">
    <?php em_locate_template('forms/event/bookings.php',true); ?>
  </div>
  <?php if (!$event_id): ?>
  <div class="wrap event-creator">
    <div class="event-creator__container">
      <p>
        <?php echo __('The Mozilla Project welcomes contributions from everyone who shares our goals and wants to contribute in a healthy and constructive manner within our communities. By creating an event on this platform you are agreeing to respect and adhere to') ?> 
        <a href="#"><?php echo __('Mozilla’s Community Participation Guidelines (“CPG”)') ?></a> 
        <?php echo __('in order to help us create a safe and positive community experience for all. Events that do not share our goals, or violate the CPG in any way, will be removed from the platform and potentially subject to further consequences.') ?>
      </p>
    </div>
    <div class="event-creator__container">
      <input class="event-creator__checkbox"type="checkbox" id="cpg" required <?php if ($event_id) { echo 'checked'; }?>>
      <label class="event-creator__cpg" for="cpg">
        <span class="event-creator__custom-check">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </span>
        <span>
          <?php echo __('I agree to respect and adhere to Mozilla’s Community Participation Guidelines') ?>
        </span>
      </label>
    </div>
  </div>
        <?php endif; ?>


  <div class="submit event-creator__submit">

    <?php 
      if (isset($event_id)):
        if (intval(get_current_user_id()) === intval($EM_Event->event_owner) || mozilla_is_site_admin()): 
    ?>
      <a class="btn btn--light btn--submit event-creator__cancel em-event-delete" href="<?php echo add_query_arg(array('action'=>'event_delete', 'event_id' => $event_id, '_wpnonce' => wp_create_nonce('event_delete_'.$event_id)), get_site_url(null, 'events/edit-event/')) ?>">
        <?php echo __('Cancel Event') ?>
      </a>
    <?php 
      endif;
      endif;
    ?>
    <input id="event-creator__submit-btn" type='submit' class='button-primary btn btn--dark btn--submit' 
      value='<?php 
        if (!isset($event_id)):
          echo esc_attr(sprintf( __('Create %s','events-manager'), __('Event','events-manager') )); 
        else: 
          echo esc_attr(sprintf( __('Update %s','events-manager'), __('Event','events-manager') )); 
        endif;
      ?>' 
    />
    <input type="hidden" name="event_id" value="<?php echo $EM_Event->event_id; ?>" />
    <input type="hidden" name="event_rsvp" value=<?php echo ($event_id) ? null : esc_attr('1'); ?> />
    <input type="hidden" name="_wpnonce" id="my_nonce_field" value="<?php echo wp_create_nonce('wpnonce_event_save'); ?>" />
    <input type="hidden" name="action" value="event_save" />
    <?php if( !empty($_REQUEST['redirect_to']) ): 
      ?>
      <input type="hidden" name="redirect_to" value="<?php echo ($event_id ? esc_attr(get_site_url().'/events/'.$EM_Event->event_slug) : esc_attr(get_site_url().'/events/') ); ?>" />
    <?php endif; ?>
  </div>		
</form>