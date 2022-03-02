<?php
/**
 * Event Editor
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

global $EM_Event, $EM_Notices, $bp, $EM_Ticket;
	$theme_directory = get_template_directory();
	require "{$theme_directory}/languages.php";

if ( isset( $_REQUEST['event_id'] ) ) {
	$event_id                  = sanitize_text_field( wp_unslash( $_REQUEST['event_id'] ) );
	$event_nonce               = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : false;
	$event_meta                = get_post_meta( $EM_Event->post_id, 'event-meta' );
	$external_url              = isset( $event_meta[0]->external_url ) && strlen( $event_meta[0]->external_url ) > 0 ? $event_meta[0]->external_url : false;
	$event_initiative          = isset( $event_meta[0]->initiative ) && strlen( $event_meta[0]->initiative ) > 0 ? intval( $event_meta[0]->initiative ) : false;
	$event_language            = isset( $event_meta[0]->language ) && strlen( $event_meta[0]->language ) > 0 ? $event_meta[0]->language : false;
	$event_projected_attendees = isset( $event_meta[0]->projected_attendees ) ? trim( $event_meta[0]->projected_attendees ) : null;
	$event_goal                = isset( $event_meta[0]->goal ) && strlen( $event_meta[0]->goal ) > 0 ? $event_meta[0]->goal : false;
}
?>

<?php if ( is_object( $EM_Event ) && ! $EM_Event->can_manage( 'edit_events', 'edit_others_events' ) ) { ?>
	<div class="event-creator event-wrap"><h2><?php esc_html_e( 'Unauthorized Access', 'community-portal' ); ?></h2><p><?php esc_html_e( 'You do not have the rights to manage this event.', 'community-portal' ); ?></p></div>
	<?php
	return false;
} elseif ( ! is_object( $EM_Event ) ) {
	$EM_Event = new EM_Event();
}

$required = apply_filters( 'em_required_html', '<i>*</i>' );
$notices  = wp_kses(
	$EM_Notices,
	array(
		'p'   => array(),
		'div' => array(
			'class' => array(),
		),
	)
);
if ( ! empty( $notices ) ) {
	echo '<div class="event-wrap">' . $notices . '</div>'; // phpcs:ignore
}

if ( ! empty( $_REQUEST['success'] ) ) {
	if ( ! get_option( 'dbem_events_form_reshow' ) ) {
		return false;
	}
}

$action_url = add_query_arg(
	array(
		'action' => 'edit',
		'lang'   => defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'en',
	),
	get_site_url( null, 'events/edit-event' )
);
if ( isset( $event_id ) ) {
	$action_url = add_query_arg( array( 'event_id' => $event_id ), $action_url );
}
if ( isset( $event_nonce ) && false !== $event_nonce ) {
	$action_url = add_query_arg( array( 'nonce' => $event_nonce ), $action_url );
}
$action_url = apply_filters( 'wpml_permalink', $action_url, 'en' );

?>
<form enctype='multipart/form-data' id="event-form" novalidate class="em-event-admin-editor
<?php
if ( $EM_Event->is_recurring() ) {
	echo 'em-event-admin-recurring';
}

?>
" method="post" action="<?php echo esc_url( add_query_arg( array( 'success' => null ), $action_url ) ); ?>">
<?php wp_nonce_field( 'protect_content', 'my_nonce_field' ); ?>
	<div class="event-wrap event-creator">
		<?php do_action( 'em_front_event_form_header', $EM_Event ); ?>
		<?php if ( get_option( 'dbem_events_anonymous_submissions' ) && ! is_user_logged_in() ) : ?>
			<h3 class="event-form-submitter"><?php __( 'Your Details', 'community-portal' ); ?></h3>
			<div class="inside event-form-submitter">
				<div class="event-creator__container">
				<label class="event-creator__label"><?php __( 'Name', 'community-portal' ); ?></label>
				<input class="event-creator__input"type="text" name="event_owner_name" id="event-owner-name" value="<?php echo esc_attr( $EM_Event->event_owner_name ); ?>" />
			</div>
			<div class="event-creator__container">
				<label class="event-creator__label"><?php __( 'Email', 'community-portal' ); ?></label>
				<input type="text" name="event_owner_email" id="event-owner-email" value="<?php echo esc_attr( $EM_Event->event_owner_email ); ?>" />
			</div>
				<?php do_action( 'em_front_event_form_guest' ); ?>
				<?php do_action( 'em_font_event_form_guest' ); // deprecated. ?>
			</div>
		<?php endif; ?>
		<div class="inside event-form-name event">
			<div class="event-creator__three-up">
				<div class="wide--double">
					<label class="event-form-name event-creator__label" for="event-name"><?php esc_html_e( 'Event Name *', 'community-portal' ); ?> </label>
					<input class="event-creator__input event-creator__input" type="text" name="event_name" id="event-name" required value="<?php echo esc_attr( $EM_Event->event_name ); ?>" />
					<div class="form__error-container">
						<p class="form__error"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></p>
					</div>
				</div>
				<div class="wide wide--md-third">
					<label class="event-creator__label" for="language"><?php esc_html_e( 'Language', 'community-portal' ); ?></label>
					<select class="event-creator__dropdown" name="language" id="language">
						<option value="0" disabled selected><?php esc_html_e( ' Language', 'community-portal' ); ?></option>
						<?php foreach ( $languages as $index => $language ) : ?>
							<option value="<?php echo esc_attr( $index ); ?>" <?php echo ( isset( $event_language ) && $event_language === $index ? 'selected' : '' ); ?>> <?php echo esc_html( $language ); ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
			<?php if ( $EM_Event->can_manage( 'upload_event_images', 'upload_event_images' ) ) : ?>
				<?php em_locate_template( 'forms/event/featured-image-public.php', true ); ?>
			<?php endif; ?>
			<?php em_locate_template( 'forms/event/when.php', true ); ?>
			<div class="inside event-form-where">
				<?php
					em_locate_template( 'forms/event/location-moz.php', true );
				?>
			</div>
		</div>
	</div>
	<?php if ( ! is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) : ?>
		<div class="event-wrap event-creator">
			<div class="event-editor">
				<div class="event-creator__three-up">
					<div class="half">
						<label class="event-form-details event-creator__label" for="event-description"><?php esc_html_e( 'Event description *', 'community-portal' ); ?></label>
						<textarea name="content" id="event-description" placeholder="<?php esc_html_e( 'Add in the details of your event’s agenda here. If this is a multi-day event, you can add in the details of each day’s schedule and start/end time.', 'community-portal' ); ?>" rows="10" id="event-description" class="event-creator__input event-creator__textarea" style="width:100%" required maxlength="3000"><?php echo esc_html( $EM_Event->post_content ); ?></textarea>
						<div class="form__error-container">
							<p class="form__error"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></p>
						</div>
					</div>
					<div class="half">
						<label class="event-form-details event-creator__label" for="event-goal"><?php esc_html_e( 'Event goal(s)', 'community-portal' ); ?></label>
						<textarea name="goal" id="event-goal" rows="10" id="event-goal" class="event-creator__input event-creator__textarea" style="width:100%" maxlength="3000"><?php echo esc_html( isset( $event_goal ) && strlen( $event_goal ) > 0 ? $event_goal : '' ); ?></textarea>
					</div>
				</div>
			<?php
					$args = array(
						'post_type'      => 'campaign',
						'posts_per_page' => -1,
					);

					$campaigns   = new WP_Query( $args );
					$initiatives = array();

					foreach ( $campaigns->posts as $campaign ) {
						$start = strtotime( get_field( 'campaign_start_date', $campaign->ID ) );
						$end   = strtotime( get_field( 'campaign_end_date', $campaign->ID ) );
						$today = time();

						$campaign_status = get_field( 'campaign_status', $campaign->ID );

						if ( 'closed' !== strtolower( $campaign_status ) ) {
							$initiatives[] = $campaign;
							continue;
						}

						if ( $today >= $start && $today <= $end ) {
							$initiatives[] = $campaign;
						}
					}

					$args = array(
						'post_type'      => 'activity',
						'posts_per_page' => -1,
					);

					$activities = new WP_Query( $args );

					$initiatives = array_merge( $initiatives, $activities->posts );
					array_map( 'mozilla_apply_default_post_ids', $initiatives );
					?>
			<?php if ( count( $initiatives ) > 0 ) : ?>
			<div class="event-creator__three-up">
				<div class="wide">
					<label class="event-creator__label" for="event-projected-attendees"><?php echo esc_html_e( 'Expected # of attendees', 'community-portal' ); ?></label>
					<input class="event-creator__input" type="text" id="event-projected-attendees" name="projected-attendees" value="<?php echo ( isset( $event_projected_attendees ) && strlen( $event_projected_attendees ) > 0 ? esc_attr( $event_projected_attendees ) : '' ); ?>">
				</div>
				<div class="wide--double">
					<label class="event-form-details event-creator__label" for="initiative"><?php esc_html_e( 'Is this event part of an activity or campaign?', 'community-portal' ); ?></label>
					<select name="initiative_id" id="initiative" class="event-creator__dropdown">
					<option value=""><?php esc_html_e( 'No', 'community-portal' ); ?></option>
					<?php foreach ( $initiatives as $initiative ) : ?>
					<option value="<?php print esc_attr( $initiative->ID ); ?>"
											<?php
											if ( isset( $event_initiative ) && $event_initiative === $initiative->ID ) :
												?>
						selected<?php endif; ?>><?php print esc_html( $initiative->post_title ); ?> (
						<?php
						if ( 'campaign' === $initiative->post_type ) :
							?>
						Campaign
												<?php
else :
	?>
	Activity<?php endif; ?>)</option>
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
				<label class="event-creator__label" for="event-creator-link"><?php esc_html_e( 'External link URL', 'community-portal' ); ?></label>
				<input type="text" class="event-creator__input" name="event_external_link" id="event-creator-link" value="<?php echo esc_attr( ( isset( $external_url ) && '' !== $external_url ) ? esc_attr( $external_url ) : '' ); ?>" />
			</div>
			<?php em_locate_template( 'forms/event/group.php', true ); ?>
		</div>
	</div>
	<?php endif; ?>
	<div class="event-creator__hidden">
		if (!isset($event_id) ) {
			<?php em_locate_template( 'forms/event/bookings.php', true ); ?>
		}
	</div>
	<?php if ( ! isset( $event_id ) ) : ?>
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
			if ( isset( $event_id ) ) {
				echo 'checked'; }
			?>
			>
			<label class="cpg__label event-creator__cpg" for="cpg">
				<?php esc_html_e( 'I agree to respect and adhere to Mozilla’s Community Participation Guidelines *', 'community-portal' ); ?>
			</label>
			<div class="form__error-container">
				<p class="form__error"><?php esc_html_e( 'Please agree to the community guidelines', 'community-portal' ); ?></p>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div class="submit event-creator__submit">
		<?php
		if ( isset( $event_id ) ) :
			if ( intval( get_current_user_id() ) === intval( $EM_Event->event_owner ) || mozilla_is_site_admin() ) :
				?>
		<a id="event-cancel" class="btn btn--light btn--submit event-creator__cancel em-event-delete" data-confirmation="<?php esc_attr_e( 'This action will permanently delete this event. Would you like to continue?', 'community-portal' ); ?>" href="
				<?php
				echo esc_url_raw(
					add_query_arg(
						array(
							'action'   => 'event_delete',
							'event_id' => $event_id,
							'_wpnonce' => wp_create_nonce( 'event_delete_' . $event_id ),
						),
						get_site_url( null, 'events/edit-event/' )
					)
				);
				?>
				">
				<?php esc_html_e( 'Delete Event', 'community-portal' ); ?>
		</a>
				<?php
			endif;
		endif;
		?>
		<?php
		if ( ! isset( $event_id ) ) :
			$update_label = __( 'Create Event', 'community-portal' );
		else :
			$update_label = __( 'Update Event', 'community-portal' );
		endif;
		?>

		<input id="event-creator__submit-btn" type='submit' class='button-primary btn btn--dark btn--submit' value='<?php echo esc_attr( $update_label ); ?>' />
		<?php wp_nonce_field( 'event_update', 'event_update_field' ); ?>
		<input type="hidden" name="event_id" value="<?php echo esc_attr( $EM_Event->event_id ); ?>" />
		<input type="hidden" name="event_rsvp" value=<?php ( isset( $event_id ) ) ? null : esc_attr( '1' ); ?> />
		<input type="hidden" name="_wpnonce" id="my_nonce_field_2" value="<?php echo esc_attr( wp_create_nonce( 'wpnonce_event_save' ) ); ?>" />
		<input type="hidden" name="action" value="event_save" />
		<?php if ( ! empty( $_REQUEST['redirect_to'] ) ) : ?>
			<input type="hidden" name="redirect_to" value="
			<?php
			if ( isset( $event_id ) ) {
				echo esc_attr( get_home_url( null, 'events/' . $EM_Event->event_slug ) );
			} else {
				echo esc_attr( get_home_url( null, 'events' ) ); }
			?>
			" />
		<?php endif; ?>
	</div>
</form>
