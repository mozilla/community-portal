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
      <div class="wide">
        <label class="event-creator__label"><?php _e ( 'Where is this event based?', 'events-manager')?></label>
        <select class="event-creator__dropdown" id="location-country" name="location_country">
					<option value="0" <?php echo ( $EM_Location->location_country == '' && $EM_Location->location_id == '' && get_option('dbem_location_default_country') == '' ) ? 'selected="selected"':''; ?>><?php _e('Country','events-manager'); ?></option>
					<?php foreach(em_get_countries() as $country_key => $country_name): ?>
					<option value="<?php echo esc_attr($country_key); ?>" <?php echo ( $EM_Location->location_country == $country_key || ($EM_Location->location_country == '' && $EM_Location->location_id == '' && get_option('dbem_location_default_country')==$country_key) ) ? 'selected="selected"':''; ?>><?php echo esc_html($country_name); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="wide--double">
        <label class="event-creator__label"><?php _e ( 'City', 'events-manager')?></label>
        <input class="event-creator__input" id="location-town" type="text" name="location_town" value="<?php echo esc_attr($EM_Location->location_town); ?>" />
      </div>
    </div>
    <div class="event-creator__three-up">
      <div class="wide">
        <label class="event-creator__label" for="online">Where will this event be held?</label>
        <select class="event-creator__dropdown" name="location-type" id="location-type">
          <option value="online">Online</option>
          <option value="address">Physical Location</option>
        </select>
      </div>
      <div class="event-creator__hidden wide--double">
        <label class="event-creator__label" for="location-name"><?php _e ( 'Location Name:', 'events-manager')?></label>
        <input id='location-id' name='location_id' type='hidden' value='<?php echo esc_attr($EM_Location->location_id); ?>' size='15' />
        <input class="event-creator__input" id="location-name" type="type" name="location_name" required value="<?php echo esc_attr($EM_Location->location_name, ENT_QUOTES); ?>" />			 
      </div>
      <div class="em-location-data-address wide--double wide--full">
        <label class="event-creator__label" id="location-address-label"><?php _e ( 'Online Meeting Link', 'events-manager')?></label>
        <input class="event-creator__input" id="location-address" type="text" name="location_address" required value="<?php echo esc_attr($EM_Location->location_address); ; ?>" />
      </div>
    </div>
	</div>
	<?php if ( get_option( 'dbem_gmap_is_active' ) ) em_locate_template('forms/map-container.php',true); ?>
	<br style="clear:both;" />
</div>