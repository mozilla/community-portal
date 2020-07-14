<?php
/**
 * Group input for events
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

	$em_event    = $GLOBALS['EM_Event'];
	$em_location = $GLOBALS['EM_Location'];

	if ( 0 !== $em_event->location_id ) {
		$em_location = $em_event->get_location();
	} else {
		$em_location = new EM_Location();
	}

	$required = apply_filters( 'em_required_html', '<i>*</i>' );
	if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['nonce'] ), 'edit-event' ) && isset( $_REQUEST['event_id'] ) ) {
		$event         = sanitize_key( $_REQUEST['event_id'] );
		$event         = em_get_event( $event );
		$event_meta    = get_post_meta( $event->post_id, 'event-meta' );
		$location_type = get_post_meta($em_location->post_id, 'location-type', true);
		$location_type = isset( $location_type ) && strlen( $location_type ) > 0 ? $location_type : null;

	} else {
		$event = false;
	}

?>
<div id="em-location-data" class="em-location-data">
	<div>
		<button 
			id="em-location-reset"
			class="btn event-creator__location-reset <?php if ( ! $event ) { echo esc_attr( 'hidden' ); } ?>" 
		>
			<a>
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z" fill="#0060DF"/>
				</svg>
				<span class="edit-text"><?php esc_html_e( 'Edit location details', 'community-portal' ); ?></span>
			</a>
		</button>
	</div>
	<div class="<?php if ( $event ) { echo esc_attr( 'event-creator__location-edit' ); } ?> em-location-data event-creator__location">
		<div class="event-creator__three-up">
			<div 
				class="wide <?php if ( $event ) { echo esc_attr( 'wide--md-third' ); } ?>"
			>
				<label class="event-creator__label" for="location-type">
					<?php esc_html_e( 'Is this event online or on location? *', 'community-portal' ); ?>
				</label>
				<select 
					class="event-creator__dropdown" 
					name="location-type" 
					id="location-type" 
					<?php 
						if ( $event ) {
							echo esc_attr( 'readonly selected' ); 
						}
					?>
					required
				>
					<option 
						value="online" 
						<?php 
							if ( isset( $location_type ) && 'online' === $location_type ) :
								echo esc_attr( 'selected' );
							endif;
						?>
						default 
					>
						<?php esc_html_e( 'Online', 'community-portal' ); ?>
					</option>
					<option 
						value="address" 
						<?php
							if ( isset( $location_type ) && 'address' === $location_type ) :
								echo esc_attr( 'selected' );
							endif;
						?>
					>
						<?php esc_html_e( 'Physical Location', 'community-portal' ); ?>
					</option>
				</select>
				<div class="form__error-container">
					<p class="form__error"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></p>
				</div>
			</div>
			<div class="wide--double">
				<?php
					$location_class = isset( $location_type ) && 'address' === $location_type ? 'event-creator__label--in-person' : 'event-creator__label--online';
				?>
				<label class="event-creator__label <?php echo esc_attr( $location_class ); ?>" for="location-name__mozilla" id="location-name-label">
					<span class="online"><?php esc_html_e( 'Online Meeting Link *', 'community-portal' ); ?></span>
					<span class="in-person"><?php esc_html_e( 'Location Name *', 'community-portal' ); ?></span>	
				</label>
				<input id='location-id' name='location_id' type='hidden' value='<?php echo esc_attr( $em_location->location_id ); ?>' size='15'  />
				<input class="event-creator__input" id="location-name-mozilla" type="type" name="location_name" required value="<?php echo esc_attr( $em_location->location_name ); ?>" required />	
				<div class="form__error-container">
					<p class="form__error">
						<span class="in-person"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></span>
						<span class="online"><?php esc_html_e( 'This field requires a valid url', 'community-portal' ); ?></span>
					</p>
				</div>
			</div>
		</div>
		<div class="event-creator__three-up 
		<?php
		if ( isset( $location_type ) && 'online' === $location_type || ! isset( $event_id ) ) :
			echo esc_attr( 'event-creator__hidden' );
		endif;
		?>
		">
			<div class="em-location-data-address wide--full">
				<label class="event-creator__label" for="location-address"><?php esc_html_e( 'Address *', 'community-portal' ); ?></label>
				<input class="event-creator__input" id="location-address" type="text" name="location_address" required value="<?php $em_location->location_address ? print esc_attr( $em_location->location_address ) : esc_html_e( 'Online', 'community-portal' ); ?>" required/> 
				<div class="form__error-container">
					<p class="form__error"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></p>
				</div>
			</div>
		</div>
		<div class="event-creator__three-up">
			<div class="wide">
				<label id="location-country-label" class="event-creator__label event-creator__label--online " for="location-country">
					<span class="online"><?php esc_html_e( 'Where will this event be held? *', 'community-portal' ); ?></span>
					<span class="in-person"><?php esc_html_e( 'Country', 'community-portal' ); ?></span>
				</label>
				<select class="event-creator__dropdown" id="location-country" name="location_country" 
				required>
					<option value="0" 
					<?php
					if ( '' === $em_location->location_country && '' === $em_location->location_id ) {
						echo esc_attr( 'selected="selected"' );
					} else {
						echo esc_attr( '' ); }
					?>
					><?php esc_html_e( 'Select', 'community-portal' ); ?></option>
					<optgroup label="<?php esc_html_e( 'Online', 'community-portal' ); ?>">
						<option value="OE" 
						<?php
						if ( 'OE' === $em_location->location_country ) {
							echo esc_attr( 'selected' );
						}
						?>
						><?php esc_html_e( 'Online Event *', 'community-portal' ); ?></option>
					</optgroup>
					<optgroup label="<?php esc_html_e( 'On Location', 'community-portal' ); ?>">
						<?php
						foreach ( em_get_countries() as $country_key => $country_name ) :
							if ( 'OE' === $country_key ) :
								continue;
							endif;
							?>
						<option value="<?php echo esc_attr( $country_key ); ?>" 
							<?php
							if ( $em_location->location_country === $country_key ) {
								echo esc_attr( 'selected' );
							}
							?>
						><?php echo esc_html( $country_name ); ?></option>
						<?php endforeach; ?>
					</optgroup>
				</select>
				<div class="form__error-container">
					<p class="form__error"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></p>
				</div>
			</div>
			<div class="wide--double">
			<label class="event-creator__label event-creator__label--in-person" for="location-town">
					<span class="in-person"><?php esc_html_e( 'City *', 'community-portal' ); ?></span>
					<span class="online"><?php esc_html_e( 'URL *', 'community-portal' ); ?></span>
				</label>
				<input class="event-creator__input" id="location-town" type="text" name="location_town" data-string="<?php esc_html_e( 'Online Event', 'community-portal' ); ?>" value="<?php echo esc_attr( $em_location->location_town ); ?>"  maxlength="180" required/>
				<div class="form__error-container">
					<p class="form__error">
						<?php esc_html_e( 'This field is required', 'community-portal' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
