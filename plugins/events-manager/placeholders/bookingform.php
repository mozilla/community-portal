<?php
/**
 * Booking form
 *
 * Update to booking for events
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php
	$em_event   = $GLOBALS['EM_Event'];
	$em_tickets = $em_event->get_bookings()->get_tickets();
?>

<div id="em-booking" class="em-booking 
<?php
if ( get_option( 'dbem_css_rsvp' ) ) {
	echo 'css-booking';}
?>
">
	<?php
		$cancel = isset( $_REQUEST['cancel'] ) ? sanitize_key( $_REQUEST['cancel'] ) : false;
		// We are firstly checking if the user has already booked a ticket at this event, if so offer a link to view their bookings.
		$em_booking = $em_event->get_bookings()->has_booking();

	if ( false !== $em_booking && null !== $cancel ) {

		$em_booking->cancel();
		$em_booking->delete();

		$updated_url = remove_query_arg( 'cancel', $_SERVER['REQUEST_URI'] );

		?>
		<script type="text/javascript">
			window.history.replaceState("","", "<?php echo $updated_url ; ?>")
		</script>
		<?php
		$em_booking = $em_event->get_bookings()->has_booking();
	}
	?>

	<?php if ( is_object( $em_booking ) ) : ?>
	<a class="em-bookings-cancel events-single__cancel btn btn--submit btn--dark" href="<?php echo esc_attr( add_query_arg( array( 'cancel' => true ), $_SERVER['REQUEST_URI'] ) ); ?>" onclick="if( !confirm('<?php esc_html_e( 'Are you sure you dont want to attend this event?', 'community-portal' ); ?>') ){ return false; }">
		<?php esc_html_e( 'Will Not Attend', 'community-portal' ); ?>
	</a>
	<?php else : ?>
	<form 
		class="em-booking-form" 
		name='booking-form' 
		method='post' 
		action='<?php echo esc_attr( remove_query_arg( 'cancel', apply_filters( 'em_booking_form_action_url', '' ) ) ); ?>'
	>
		<input type='hidden' name='action' value='booking_add'/>
		<input type='hidden' name='event_id' value='<?php echo esc_attr( $em_event->get_bookings()->event_id ); ?>'/>
		<input type='hidden' name='_wpnonce' value='<?php echo esc_attr( wp_create_nonce( 'booking_add' ) ); ?>'/>
		<?php
			$count = 0;
		foreach ( $em_tickets as $ticket ) {
			if ( $count < 1 ) {
				?>
		<input type="hidden" name="<?php echo esc_html( 'em_tickets[' ) . esc_html( $ticket->ticket_id ) . esc_html( '][spaces]' ); ?>" value="1">
				<?php
				$count++;
			}
		}
		?>
		<input type="submit" class="btn btn--dark btn--submit 
		<?php
		if ( is_admin() ) {
			echo esc_attr( 'button-primary ' );}
		?>
		em-booking-submit" id="em-booking-submit" value="<?php esc_html_e( 'Attend', 'community-portal' ); ?>" />
	</form>	
	<?php endif; ?>
</div>
