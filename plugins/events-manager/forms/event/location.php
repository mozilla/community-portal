<?php
global $EM_Event;
$required = apply_filters('em_required_html','<i>*</i>');
?>
<div id="em-location-data" class="em-location-data">
	<div class="em-location-data">
		<?php 
			global $EM_Location;
			if( $EM_Event->location_id !== 0 ){
				$EM_Location = $EM_Event->get_location();
			}elseif(get_option('dbem_default_location') > 0){
				$EM_Location = em_get_location(get_option('dbem_default_location'));
			}else{
				$EM_Location = new EM_Location();
			}
		?>
    <div class="event-creator__three-up">
      <label class="event-creator__label" for="online">Where will this event be held?</label>
      <select class="event-creator__dropdown" name="location-type" id="location-type">
        <option value="online">Online</option>
        <option value="address">Physical Location</option>
      </select>
    </div>
		<div class="em-location-data-name">
			<label class="event-creator__label"for="location-name"><?php _e ( 'Location Name:', 'events-manager')?></label>
      <input id='location-id' name='location_id' type='hidden' value='<?php echo esc_attr($EM_Location->location_id); ?>' size='15' />
      <input class="event-creator__input" id="location-name" type="text" name="location_name" value="<?php echo esc_attr($EM_Location->location_name, ENT_QUOTES); ?>" /><?php echo $required; ?>											
    </div>
		<div class="em-location-data-address">
			<div><?php _e ( 'Address:', 'events-manager')?>&nbsp;</div>
			<div>
				<input id="location-address" type="text" name="location_address" value="<?php echo esc_attr($EM_Location->location_address); ; ?>" /><?php echo $required; ?>
			</div>
		</div>
		<div class="em-location-data-town">
			<div><?php _e ( 'City/Town:', 'events-manager')?>&nbsp;</div>
			<div>
				<input id="location-town" type="text" name="location_town" value="<?php echo esc_attr($EM_Location->location_town); ?>" /><?php echo $required; ?>
			</div>
		</div>
		<div class="em-location-data-state">
			<div><?php _e ( 'State/County:', 'events-manager')?>&nbsp;</div>
			<div>
				<input id="location-state" type="text" name="location_state" value="<?php echo esc_attr($EM_Location->location_state); ?>" />
			</div>
		</div>
		<div class="em-location-data-postcode">
			<div><?php _e ( 'Postcode:', 'events-manager')?>&nbsp;</div>
			<div>
				<input id="location-postcode" type="text" name="location_postcode" value="<?php echo esc_attr($EM_Location->location_postcode); ?>" />
			</div>
		</div>
		<div class="em-location-data-region">
			<div><?php _e ( 'Region:', 'events-manager')?>&nbsp;</div>
			<div>
				<input id="location-region" type="text" name="location_region" value="<?php echo esc_attr($EM_Location->location_region); ?>" />
			</div>
		</div>
		<div class="em-location-data-country">
			<div><?php _e ( 'Country:', 'events-manager')?>&nbsp;</div>
			<div>
				<select id="location-country" name="location_country">
					<option value="0" <?php echo ( $EM_Location->location_country == '' && $EM_Location->location_id == '' && get_option('dbem_location_default_country') == '' ) ? 'selected="selected"':''; ?>><?php _e('none selected','events-manager'); ?></option>
					<?php foreach(em_get_countries() as $country_key => $country_name): ?>
					<option value="<?php echo esc_attr($country_key); ?>" <?php echo ( $EM_Location->location_country == $country_key || ($EM_Location->location_country == '' && $EM_Location->location_id == '' && get_option('dbem_location_default_country')==$country_key) ) ? 'selected="selected"':''; ?>><?php echo esc_html($country_name); ?></option>
					<?php endforeach; ?>
				</select><?php echo $required; ?>
			</div>
		</div>
	</div>
	<?php if ( get_option( 'dbem_gmap_is_active' ) ) em_locate_template('forms/map-container.php',true); ?>
	<br style="clear:both;" />
</div>