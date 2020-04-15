<?php
global $EM_Event;
$required = apply_filters('em_required_html','<i>*</i>');
$event = $_REQUEST['event_id'];
if ($event):
	$event = em_get_event($event); 
	$event_meta = get_post_meta($event->post_id, 'event-meta');
	$location_type = $event_meta[0]->location_type;
endif;
?>
<div id="em-location-data" class="em-location-data">
	<div>
		<button id="em-location-reset" class="btn event-creator__location-reset <?php echo (!$event) ? esc_attr("hidden") : null ?>" style="">
			<a>
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z" fill="#0060DF"/>
				</svg>
				<span class="edit-text"><?php echo __('Edit location details', 'community-portal') ?></span>
			</a>
		</button>
	</div>
	<div class="<?php echo ($event) ? esc_attr('event-creator__location-edit') : null ?> em-location-data event-creator__location">
		<?php 
			global $EM_Location;
			if( $EM_Event->location_id !== 0 ){
				$EM_Location = $EM_Event->get_location();
			} else {
				$EM_Location = new EM_Location();
			}
		?>
		<div class="event-creator__three-up">
			<div class="wide <?php echo ($event) ? esc_attr("wide--md-third") : null ?>">
				<label class="event-creator__label" for="location-type"><?php echo __('Is this event online or on location? *', 'community-portal') ?></label>
				<select class="event-creator__dropdown" name="location-type" id="location-type" <?php echo ($event) ? esc_attr('disabled') : null ?> required>
					<option value="online" <?php if ($location_type === 'online'): echo esc_attr('selected'); endif; ?> default ><?php echo __('Online', "community-portal") ?></option>
					<option value="address" <?php if ($location_type === 'address'): echo 'selected'; endif; ?>><?php echo __('Physical Location', "community-portal") ?></option>
				</select>
				<input id="location-type-placeholder" type="hidden" name="location-type" value=<?php echo (isset($location_type) && strlen($location_type) >0 ? esc_attr($location_type) : null) ?>>
			</div>
			<div class="wide--double">
				<label class="event-creator__label event-creator__label--online" for="location-name" id="location-name-label">
					<span class="online"><?php _e ('Online Meeting Link *', 'commuity-portal')?></span>
					<span class="in-person"><?php _e( 'Location Name *', 'commuity-portal')?></span>	
				</label>
				<input id='location-id' name='location_id' type='hidden' value='<?php echo esc_attr($EM_Location->location_id); ?>' size='15'  />
				<input class="event-creator__input" id="location-name" type="type" name="location_name" required value="<?php echo esc_attr($EM_Location->location_name, ENT_QUOTES); ?>" required />	
			</div>
		</div>
		<div class="event-creator__three-up <?php if ($location_type === 'online' || !$event_id): echo esc_attr('event-creator__hidden') ; endif; ?>">
			<div class="em-location-data-address wide--full">
				<label class="event-creator__label" for="location-address"><?php print __( 'Address *', 'commuity-portal')?></label>
				<input class="event-creator__input" id="location-address" type="text" name="location_address" required value="<?php echo $EM_Location->location_address ? esc_attr($EM_Location->location_address) : "Online" ; ?>" required/>
			</div>
		</div>
		<div class="event-creator__three-up">
			<div class="wide">
				<label id="location-country-label" class="event-creator__label event-creator__label--online " for="location-country">
					<span class="online"><?php _e( 'Where will this event be held? *', 'commuity-portal')?></span>
					<span class="in-person"><?php _e( 'Country', 'commuity-portal')?></span>
				</label>
				<select class="event-creator__dropdown" id="location-country" name="location_country" <?php if ($event) : echo esc_attr("disabled"); endif; ?> required>
					<option value="0" <?php echo ( $EM_Location->location_country == '' && $EM_Location->location_id == '') ? 'selected="selected"':''; ?>><?php _e('Select','commuity-portal'); ?></option>
					<optgroup label="<?php echo __('Online', 'community-portal') ?>">
						<option value="OE" <?php echo ( $EM_Location->location_country == 'OE') ? 'selected="selected"':''; ?>><?php _e('Online Event *','commuity-portal'); ?></option>
					</optgroup>
					<optgroup label="<?php echo __('On Location', 'community-portal') ?>">
						<?php foreach(em_get_countries() as $country_key => $country_name): 
							if ($country_key === 'OE'):
								continue;
							endif;
						?>
						<option value="<?php echo esc_attr($country_key); ?>" <?php echo ( $EM_Location->location_country == $country_key ) ? 'selected="selected"':''; ?>><?php echo esc_html($country_name); ?></option>
						<?php endforeach; ?>
					</optgroup>
				</select>
			</div>
			<div class="wide--double">
				<label class="event-creator__label event-creator__label--online" for="location-town">
					<span class="in-person"><?php print __( 'City *', 'commuity-portal')?></span>
					<span class="online"><?php print __( 'URL *', 'commuity-portal')?></span>
				</label>
				<input class="event-creator__input" id="location-town" type="text" name="location_town" data-string="<?php _e('Online Event', 'community-portal') ?>" value="<?php echo esc_attr($EM_Location->location_town); ?>"  maxlength="180" required/>
			</div>
		</div>
	</div>
</div> 