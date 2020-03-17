<?php

global $EM_Event, $EM_Notices, $bp, $EM_Ticket;

$theme_directory = get_template_directory();
include("{$theme_directory}/languages.php");


mozilla_match_categories();
if(isset($_REQUEST['event_id'])) {
	$event_id = $_REQUEST['event_id'];
    $event_meta = get_post_meta($EM_Event->post_id, 'event-meta');
	$external_url = $event_meta[0]->external_url;
	$event_initiative = isset($event_meta[0]->initiative) && strlen($event_meta[0]->initiative) > 0 ? intval($event_meta[0]->initiative) : false;   
	$event_language = isset($event_meta[0]->language) && strlen($event_meta[0]->language) > 0 ? $event_meta[0]->language : false;
	$event_projected_attendees = isset($event_meta[0]->projected_attendees) ? $event_meta[0]->projected_attendees : false;
	$event_goal = isset($event_meta[0]->goal) && strlen($event_meta[0]->goal) > 0 ? $event_meta[0]->goal : false;
}
?>

<?php if(is_object($EM_Event) && !$EM_Event->can_manage('edit_events','edit_others_events') ){ ?>
	<div class="event-creator event-wrap"><h2><?php __('Unauthorized Access','commuity-portal'); ?></h2><p><?php echo sprintf(__('You do not have the rights to manage this %s.','commuity-portal'),__('Event','commuity-portal')); ?></p></div>
<?php
    return false;
} elseif (!is_object($EM_Event) ){
	$EM_Event = new EM_Event();
}

$required = apply_filters('em_required_html','<i>*</i>');
echo $EM_Notices;

if(!empty($_REQUEST['success'])){
    if(!get_option('dbem_events_form_reshow'))
        return false;
}

?>	

<form enctype='multipart/form-data' id="event-form" novalidate class="em-event-admin-editor <?php if( $EM_Event->is_recurring() ) echo 'em-event-admin-recurring' ?>" method="post" action="<?php echo esc_url(add_query_arg(array('success'=>null))); ?>">
<?php print wp_nonce_field('protect_content', 'my_nonce_field'); ?>
    <div class="event-wrap event-creator">
		<?php do_action('em_front_event_form_header', $EM_Event); ?>
		<?php if(get_option('dbem_events_anonymous_submissions') && !is_user_logged_in()): ?>
			<h3 class="event-form-submitter"><?php __( 'Your Details', 'commuity-portal'); ?></h3>
			<div class="inside event-form-submitter">
				<div class="event-creator__container">
                <label class="event-creator__label"><?php __('Name', 'commuity-portal'); ?></label>
                <input class="event-creator__input"type="text" name="event_owner_name" id="event-owner-name" value="<?php echo esc_attr($EM_Event->event_owner_name); ?>" />
            </div>
            <div class="event-creator__container">
                <label class="event-creator__label"><?php __('Email', 'commuity-portal'); ?></label>
                <input type="text" name="event_owner_email" id="event-owner-email" value="<?php echo esc_attr($EM_Event->event_owner_email); ?>" />
            </div>
				<?php do_action('em_front_event_form_guest'); ?>
				<?php do_action('em_font_event_form_guest'); //deprecated ?>
			</div>
		<?php endif; ?>
		<div class="inside event-form-name event">
			<div class="event-creator__three-up">
				<div class="wide--double">
					<label class="event-form-name event-creator__label" for="event-name"><?php print __( 'Event Name *', 'commuity-portal'); ?></label>
					<input class="event-creator__input event-creator__input" type="text" name="event_name" id="event-name" required value="<?php echo esc_attr($EM_Event->event_name,ENT_QUOTES); ?>" />
				</div>
				<div class="wide wide--md-third">
					<label class="event-creator__label" for="language"><?php print __('Language') ?></label>
					<select class="event-creator__dropdown" name="language" id="language">
						<option value="0" disabled selected>Language</option>
						<?php foreach($languages as $index=>$language): ?>
							<option value="<?php echo $index ?>" <?php echo ($event_language && $event_language === $index ? 'selected' : '')?>> <?php echo $language; ?></option>
						<?php endforeach ?>
					</select>
				</div>
            </div>
            <?php if( $EM_Event->can_manage('upload_event_images','upload_event_images') ): ?>
				<?php em_locate_template('forms/event/featured-image-public.php',true); ?>
			<?php endif; ?>
            <?php em_locate_template('forms/event/when.php',true); ?>
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
    <?php if(!is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)): ?>
		<div class="event-wrap event-creator">
			<div class="event-editor">
				<div class="event-creator__three-up">
					<div class="half">
						<label class="event-form-details event-creator__label" for="event-goal"><?php print __('Event goal(s)', 'commuity-portal'); ?></label>
						<textarea name="goal" id="event-goal" rows="10" id="event-goal" class="event-creator__input event-creator__textarea" style="width:100%" maxlength="3000"><?php echo ($event_goal ? $event_goal : '') ?></textarea>
					</div>
					<div class="half">
						<label class="event-form-details event-creator__label" for="event-description"><?php print __('Event description *', 'commuity-portal'); ?></label>
						<textarea name="content" id="event-description" placeholder="Add in the details of your event’s agenda here. If this is a multi-day event, you can add in the details of each day’s schedule and start/end time." rows="10" id="event-description" class="event-creator__input event-creator__textarea" style="width:100%" required maxlength="3000"><?php echo __($EM_Event->post_content) ?></textarea>
					</div>
				</div>
			
            <?php 
                    $args = Array(
                        'post_type' =>  'campaign',
                        'per_page'  =>  -1
                    );
                
                    $campaigns = new WP_Query($args);
                    $initiatives = Array();

                    foreach($campaigns->posts AS $campaign) {
                        $start = strtotime(get_field('campaign_start_date', $campaign->ID));
                        $end = strtotime(get_field('campaign_end_date', $campaign->ID));
                        $today = time();

                        $campaign_status = get_field('campaign_status', $campaign->ID);

                        if(strtolower($campaign_status) !== 'closed') {
                            $initiatives[] = $campaign;
                            continue;
                        }

                        if($today >= $start && $today <= $end) {
                            $initiatives[] = $campaign;
                        }
                    }

                    $args = Array(
                        'post_type' =>  'activity',
                        'per_page'  =>  -1
                    );

                    $activities = new WP_Query($args);
                    $initiatives = array_merge($initiatives, $activities->posts);
                    
            ?>
            <?php if(sizeof($initiatives) > 0): ?>
            <div class="event-creator__three-up">
				<div class="wide">
					<label class="event-creator__label" for="event-projected-attendees">Expected # of attendees</label>
					<input class="event-creator__input" type="text" id="event-projected-attendees" name="projected-attendees" value="<?php echo ($event_projected_attendees ? $event_projected_attendees : '') ?>">
				</div>
				<div class="wide--double">
					<label class="event-form-details event-creator__label" for="initiative"><?php print __('Is this event part of an activity or campaign?', 'community-portal'); ?></label>
					<select name="initiative_id" id="initiative" class="event-creator__dropdown">
					<option value=""><?php print __('No', 'community-portal');?></option>
					<?php foreach($initiatives AS $initiative): ?>
					<option value="<?php print $initiative->ID; ?>"<?php if($event_initiative && $event_initiative == $initiative->ID): ?> selected<?php  endif; ?>><?php print $initiative->post_title; ?> (<?php if($initiative->post_type === 'campaign'): ?>Campaign<?php else: ?>Activity<?php endif; ?>)</option>
					<?php endforeach; ?>
					</select>
				</div>
            </div>
			<?php endif; ?>
        <?php if(get_option('dbem_categories_enabled')) { em_locate_template('forms/event/categories-public.php',true); }  ?>
            <div class="event-creator__container">
                <label class="event-creator__label" for="event-creator-link"><?php print __('External link URL', 'commuity-portal'); ?></label>
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
    <div class="event-wrap event-creator">
        <div class="event-creator__container">
            <p>
                <?php echo __('The Mozilla Project welcomes contributions from everyone who shares our goals and wants to contribute in a healthy and constructive manner within our communities. By creating an event on this platform you are agreeing to respect and adhere to') ?> 
                <a class="event-creator__link" href="https://www.mozilla.org/about/governance/policies/participation/"><?php echo __('Mozilla’s Community Participation Guidelines (“CPG”)') ?></a> 
                <?php echo __('in order to help us create a safe and positive community experience for all. Events that do not share our goals, or violate the CPG in any way, will be removed from the platform and potentially subject to further consequences.') ?>
            </p>
        </div>
        <div class="event-creator__container cpg">
            <input class="checkbox--hidden" type="checkbox" id="cpg" required <?php if ($event_id) { echo 'checked'; }?>>
            <label class="cpg__label event-creator__cpg" for="cpg">
                <?php echo __('I agree to respect and adhere to Mozilla’s Community Participation Guidelines *') ?>
            </label>
        </div>
    </div>
    <?php endif; ?>
    <div class="submit event-creator__submit">
        <?php 
        if (isset($event_id)):
            if(intval(get_current_user_id()) === intval($EM_Event->event_owner) || mozilla_is_site_admin()): 
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
            echo esc_attr(sprintf( __('Create %s','commuity-portal'), __('Event','commuity-portal') )); 
        else: 
            echo esc_attr(sprintf( __('Update %s','commuity-portal'), __('Event','commuity-portal') )); 
        endif;
        ?>' 
        />
        <input type="hidden" name="event_id" value="<?php echo $EM_Event->event_id; ?>" />
        <input type="hidden" name="event_rsvp" value=<?php echo ($event_id) ? null : esc_attr('1'); ?> />
        <input type="hidden" name="_wpnonce" id="my_nonce_field" value="<?php echo wp_create_nonce('wpnonce_event_save'); ?>" />
        <input type="hidden" name="action" value="event_save" />
        <?php if( !empty($_REQUEST['redirect_to']) ): ?>
            <input type="hidden" name="redirect_to" value="<?php echo ($event_id ? esc_attr(get_site_url().'/events/'.$EM_Event->event_slug) : esc_attr(get_site_url().'/events/') ); ?>" />
        <?php endif; ?>
    </div>		
</form>