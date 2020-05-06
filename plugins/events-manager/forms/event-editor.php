<?php
/**
 * Event Editor
 *
 * Form for editing events for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php


global $EM_Event, $EM_Notices, $bp, $EM_Ticket;
$em_event   = $EM_Event;
$em_notices = $EM_Notices;
$em_ticket  = $EM_Ticket;
global $bp;

$theme_directory = get_template_directory();
require "{$theme_directory}/languages.php";

mozilla_match_categories();
if ( isset( $_REQUEST['nonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['nonce'] ), 'edit-event' ) && isset( $_REQUEST['event_id'] ) ) {
	$event_id                  = sanitize_key( $_REQUEST['event_id'] );
	$event_meta                = get_post_meta( $em_event->post_id, 'event-meta' );
	$external_url              = $event_meta[0]->external_url;
	$event_initiative          = isset( $event_meta[0]->initiative ) && strlen( $event_meta[0]->initiative ) > 0 ? intval( $event_meta[0]->initiative ) : false;
	$event_language            = isset( $event_meta[0]->language ) && strlen( $event_meta[0]->language ) > 0 ? $event_meta[0]->language : false;
	$event_projected_attendees = isset( $event_meta[0]->projected_attendees ) ? $event_meta[0]->projected_attendees : false;
	$event_goal                = isset( $event_meta[0]->goal ) && strlen( $event_meta[0]->goal ) > 0 ? trim( $event_meta[0]->goal ) : '';
}
?>

<?php if ( is_object( $em_event ) && ! $em_event->can_manage( 'edit_events', 'edit_others_events' ) ) { ?>
	<div class="event-creator event-wrap"><h2><?php esc_html_e( 'Unauthorized Access', 'commuity-portal' ); ?></h2><p><?php echo esc_html__( 'You do not have the rights to manage this ', 'commuity-portal' ) . esc_html( ' ' ) . esc_attr__( 'Event', 'commuity-portal' ); ?></p></div>
	<?php
	return false;
} elseif ( ! is_object( $em_event ) ) {
	$em_event = new EM_Event();
	
}

$required = apply_filters( 'em_required_html', '<i>*</i>' );
echo esc_html( $em_notices );

if ( ! empty( $_REQUEST['success'] ) ) {
	if ( ! get_option( 'dbem_events_form_reshow' ) ) {
		return false;
	}
}

?>

<form enctype='multipart/form-data' id="event-form" novalidate class="em-event-admin-editor 
<?php
if ( $em_event->is_recurring() ) {
	echo 'em-event-admin-recurring';}
?>
" method="post" action="<?php echo esc_url( add_query_arg( array( 'success' => null ) ) ); ?>">
<?php wp_nonce_field( 'protect_content', 'my_nonce_field' ); ?>
	<div class="event-wrap event-creator">
		<?php do_action( 'em_front_event_form_header', $em_event ); ?>
		<div class="inside event-form-name event">
			<div class="event-creator__three-up">
				<div class="wide--double">
					<label class="event-form-name event-creator__label" for="event-name"><?php esc_html_e( 'Event Name *', 'commuity-portal' ); ?></label>
					<input class="event-creator__input event-creator__input" type="text" name="event_name" id="event-name" required value="<?php echo esc_attr( $em_event->event_name ); ?>" />
				</div>
				<div class="wide wide--md-third">
					<label class="event-creator__label" for="language"><?php esc_html_e( 'Language', 'community-portal' ); ?></label>
					<select class="event-creator__dropdown" name="language" id="language">
						<option value="0" disabled selected><?php esc_html_e( 'Language', 'community-portal' ); ?></option>
						<?php foreach ( $languages as $index => $language ) : ?>
							<option value="<?php echo esc_attr( $index ); ?>" <?php echo ( $event_language && $event_language === $index ? 'selected' : '' ); ?>> <?php echo esc_attr( $language ); ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
			<?php if ( $em_event->can_manage( 'upload_event_images', 'upload_event_images' ) ) : ?>
				<?php em_locate_template( 'forms/event/featured-image-public.php', true ); ?>
			<?php endif; ?>
			<?php em_locate_template( 'forms/event/when.php', true ); ?>
			<div class="inside event-form-where">
				<?php
				if ( ! is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) :
						em_locate_template( 'forms/event/location-moz.php', true );
					else :
						em_locate_template( 'forms/event/location.php', true );
					endif
					?>
			</div>
		</div> 	
	</div>
	<?php if ( ! is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) : ?>
		<div class="event-wrap event-creator">
			<div class="event-editor">
				<div class="event-creator__three-up">
					<div class="half">
						<label class="event-form-details event-creator__label" for="event-description"><?php esc_html_e( 'Event description *', 'commuity-portal' ); ?></label>
						<textarea name="content" id="event-description" placeholder="<?php esc_attr_e( 'Add in the details of your event’s agenda here. If this is a multi-day event, you can add in the details of each day’s schedule and start/end time.', 'community-portal' ); ?>" rows="10" id="event-description" class="event-creator__input event-creator__textarea" style="width:100%" required maxlength="3000"><?php print esc_html( $em_event->post_content ); ?></textarea>
					</div>
					<div class="half">
						<label class="event-form-details event-creator__label" for="event-goal"><?php esc_html_e( 'Event goal(s)', 'commuity-portal' ); ?></label>
						<textarea name="goal" id="event-goal" rows="10" id="event-goal" class="event-creator__input event-creator__textarea" style="width:100%" maxlength="3000"><?php print esc_html( $event_goal ); ?></textarea>
					</div>
				</div>
			<?php
					$args = array(
						'post_type' => 'campaign',
						'per_page'  => -1,
					);

					$campaigns   = new WP_Query( $args );
					$initiatives = array();

					foreach ( $campaigns->posts as $campaign ) {
						$start = strtotime( get_field( 'campaign_start_date', $campaign->ID ) );
						$end   = strtotime( get_field( 'campaign_end_date', $campaign->ID ) );
						$today = time();

						$campaign_status = get_field( 'campaign_status', $campaign->ID );

						if ( strtolower( $campaign_status ) !== 'closed' ) {
							$initiatives[] = $campaign;
							continue;
						}

						if ( $today >= $start && $today <= $end ) {
							$initiatives[] = $campaign;
						}
					}

					$args = array(
						'post_type' => 'activity',
						'per_page'  => -1,
					);

					$activities  = new WP_Query( $args );
					$initiatives = array_merge( $initiatives, $activities->posts );

					?>
			<?php if ( count( $initiatives ) > 0 ) : ?>
			<div class="event-creator__three-up">
				<div class="wide">
					<label class="event-creator__label" for="event-projected-attendees"><?php esc_html_e( 'Expected # of attendees', 'community-portal' ); ?></label>
					<?php
					if ( $event_projected_attendees ) {
						$projected_attendees_value = $event_projected_attendees;
					} else {
						$projected_attendees_value = '';
					}
					?>
					<input class="event-creator__input" type="text" id="event-projected-attendees" name="projected-attendees" value="<?php echo esc_attr( $projected_attendees_value ); ?>">
				</div>
				<div class="wide--double">
					<label class="event-form-details event-creator__label" for="initiative"><?php esc_html_e( 'Is this event part of an activity or campaign?', 'community-portal' ); ?></label>
					<select name="initiative_id" id="initiative" class="event-creator__dropdown">
					<option value=""><?php esc_html_e( 'No', 'community-portal' ); ?></option>
					<?php foreach ( $initiatives as $initiative ) : ?>
					<option value="<?php print esc_attr( $initiative->ID ); ?>"
						<?php
						if ( $event_initiative && $event_initiative === $initiative->ID ) :
							?>
							selected<?php endif; ?>><?php print esc_html( $initiative->post_title ); ?> (
						<?php
						if ( 'campaign' === $initiative->post_type ) :
							esc_html_e( 'Campaign', 'community-portal' );
						else :
							esc_html_e( 'Activity', 'community-portal' );
						endif;
						?>
					)</option>
					<?php endforeach; ?>
					</select>
				</div>
			</div>
			<?php endif; ?>
		<?php
		if ( get_option( 'dbem_categories_enabled' ) ) {
			em_locate_template( 'forms/event/categories-public.php', true ); }
		?>
			<div class="event-creator__container">
				<label class="event-creator__label" for="event-creator-link"><?php esc_html_e( 'External link URL', 'commuity-portal' ); ?></label>
				<?php
				if ( isset( $external_url ) && '' !== $external_url ) {
					$url_value = esc_url_raw( $external_url );
				} else {
					$url_value = '';
				}
				?>
				<input type="text" class="event-creator__input" name="event_external_link" id="event-creator-link" value="<?php echo esc_url_raw( $url_value ); ?>" />
			</div>
			<?php em_locate_template( 'forms/event/group.php', true ); ?>
		</div>
	</div>
	<?php endif; ?>
	<div class="event-creator__hidden">
        <?php em_locate_template('forms/event/bookings.php', true); ?>
    </div>
	<?php if ( ! $event_id ) : ?>
	<div class="event-wrap event-creator">
		<div class="event-creator__container">
			<p>
				<?php esc_html_e( 'The Mozilla Project welcomes contributions from everyone who shares our goals and wants to contribute in a healthy and constructive manner within our communities. By creating an event on this platform you are agreeing to respect and adhere to', 'community-portal' ); ?> 
				<a class="event-creator__link" href="https://www.mozilla.org/about/governance/policies/participation/"><?php esc_html_e( 'Mozilla’s Community Participation Guidelines (“CPG”)', 'community-portal' ); ?></a> 
				<?php esc_html_e( 'in order to help us create a safe and positive community experience for all. Events that do not share our goals, or violate the CPG in any way, will be removed from the platform and potentially subject to further consequences.', 'community-portal' ); ?>
			</p>
		</div>
		<div class="event-creator__container cpg">
			<input class="checkbox--hidden" type="checkbox" id="cpg" required 
			<?php
			if ( $event_id ) {
				echo 'checked'; }
			?>
			>
			<label class="cpg__label event-creator__cpg" for="cpg">
				<?php esc_html_e( 'I agree to respect and adhere to Mozilla’s Community Participation Guidelines *', 'community-portal' ); ?>
			</label>
		</div>
	</div>
	<?php endif; ?>
	<div class="submit event-creator__submit">
		<?php
		if ( isset( $event_id ) ) :
			if ( intval( get_current_user_id() ) === intval( $em_event->event_owner ) || mozilla_is_site_admin() ) :
				?>
		<a class="btn btn--light btn--submit event-creator__cancel em-event-delete" href="
				<?php
				$delete_url = add_query_arg(
					array(
						'action'   => 'event_delete',
						'event_id' => $event_id,
						'_wpnonce' => wp_create_nonce( 'event_delete_' . $event_id ),
					),
					get_site_url( null, 'events/edit-event/' )
				);
				echo esc_url_raw( $delete_url );
				?>
			">
				<?php esc_html_e( 'Cancel Event', 'community-portal' ); ?>
		</a>
				<?php
			endif;
		endif;
		if ( ! isset( $event_id ) ) :
			$value_string = esc_attr__( 'Create Event', 'commuity-portal' );
		else :
			$value_string = esc_attr__( 'Update Event', 'commuity-portal' );
		endif;
		?>
		<input id="event-creator__submit-btn" type='submit' class='button-primary btn btn--dark btn--submit' 
		value="<?php echo esc_attr( $value_string ); ?>"
		/>
		<input type="hidden" name="event_id" value="<?php echo esc_attr( $em_event->event_id ); ?>" />
		<?php
		if ( ! $event_id ) {
			$event_rsvp = '1';
		} else {
			$event_rsvp = '';
		}
		?>
		<input type="hidden" name="event_rsvp" value="<?php echo esc_attr( $event_rsvp ); ?>" />
		<input type="hidden" name="_wpnonce" id="my_nonce_field" value="<?php echo esc_attr( wp_create_nonce( 'wpnonce_event_save' ) ); ?>" />
		<?php wp_nonce_field( 'event_update', 'event_update_field' ); ?>
		<?php wp_nonce_field( 'event_create', 'event_create_field' ); ?>
		<input type="hidden" name="action" value="event_save" />
		<?php if ( ! empty( $_REQUEST['redirect_to'] ) ) : ?>
			<input type="hidden" name="redirect_to" value="
			<?php
			if ( $event_id ) {
				echo esc_attr( get_site_url() ) . esc_attr( '/events/' ) . esc_attr( $em_event->event_slug );
			} else {
				esc_attr( get_site_url() ) . esc_attr( '/events/' );
			}
			?>
				" />
		<?php endif; ?>
	</div>		
</form>
