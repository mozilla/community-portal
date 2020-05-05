<?php
/**
 * Used for both multiple and single tickets. $col_count will always be 1 in single ticket mode, and be a unique number for each ticket starting from 1
 * This form should have $EM_Ticket and $col_count available globally.
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

global $col_count, $EM_Ticket, $EM_Event;
$col_count                = absint( $col_count ); // now we know it's a number.

// Can't rename globals but need to follow PHPCS Wordpress standards, so reassign to lower case variable.
$em_ticket = $EM_Ticket;

// If it's a new event create a ticket with the current new event.
if( is_null( $em_ticket ) ) {
	$em_ticket = new EM_Ticket();
	$em_ticket->event_id = $em_event->event_id;
}

$em_ticket->ticket_spaces = 1000;

?>
<div class="em-ticket-form">
	<input type="hidden" name="em_tickets[<?php echo esc_attr( $col_count ); ?>][ticket_id]" class="ticket_id" value="<?php echo esc_attr( $em_ticket->ticket_id ); ?>" />
	<div class="em-ticket-form-main">
		<div class="ticket-name">
			<label title="<?php esc_attr_e( 'Enter a ticket name.', 'community-portal' ); ?>"><?php esc_attr_e( 'Name', 'community-portal' ); ?></label>
			<input type="text" name="em_tickets[<?php echo esc_attr( $col_count ); ?>][ticket_name]" value="<?php echo esc_attr( $em_ticket->ticket_name ); ?>" class="ticket_name" />
		</div>
		<div class="ticket-description">
			<label><?php esc_html_e( 'Description', 'community-portal' ); ?></label>
			<textarea name="em_tickets[<?php echo esc_attr( $col_count ); ?>][ticket_description]" class="ticket_description"><?php echo esc_html( wp_unslash( $em_ticket->ticket_description ) ); ?></textarea>
		</div>
		<div class="ticket-price"><label><?php esc_html_e( 'Price', 'community-portal' ); ?></label><input type="text" name="em_tickets[<?php echo esc_attr( $col_count ); ?>][ticket_price]" class="ticket_price" value="<?php echo esc_attr( $em_ticket->get_price_precise( true ) ); ?>" /></div>
		<div class="ticket-spaces">
			<label title="<?php esc_attr_e( 'Enter a maximum number of spaces (required).', 'community-portal' ); ?>"><?php esc_html_e( 'Spaces', 'community-portal' ); ?></label>
			<input type="text" name="em_tickets[<?php echo esc_attr( $col_count ); ?>][ticket_spaces]" value=<?php echo esc_attr( '1000' ); ?> class="ticket_spaces" />
		</div>
	</div>
	<div class="em-ticket-form-advanced" style="display:none;">
		<div class="ticket-spaces ticket-spaces-min">
		<label title="<?php esc_html_e( 'Leave either blank for no upper/lower limit.', 'community-portal' ); ?>"><?php echo esc_html_x( 'At least', 'spaces per booking', 'community-portal' ); ?></label>

			<input type="text" name="em_tickets[<?php echo esc_attr( $col_count ); ?>][ticket_min]" value="<?php echo esc_attr( $em_ticket->ticket_min ); ?>" class="ticket_min" />
			<?php esc_html_e( 'spaces per booking', 'community-portal' ); ?>
		</div>
		<div class="ticket-spaces ticket-spaces-max">
			<label title="<?php esc_html_e( 'Leave either blank for no upper/lower limit.', 'community-portal' ); ?>"><?php echo esc_html_x( 'At most', 'spaces per booking', 'community-portal' ); ?></label>
			<input type="text" name="em_tickets[<?php echo esc_attr( $col_count ); ?>][ticket_max]" value="<?php echo esc_attr( $em_ticket->ticket_max ); ?>" class="ticket_max" />
			<?php esc_html_e( 'spaces per booking', 'community-portal' ); ?>
		</div>
		<?php do_action( 'em_ticket_edit_form_fields', $col_count, $em_ticket ); // do not delete, add your extra fields this way, remember to save them too! ?>
	</div>
	<div class="ticket-options">
		<a href="#" class="ticket-options-advanced show"><span class="show-advanced"><?php esc_html_e( 'Show Advanced Options', 'community-portal' ); ?></span><span class="hide-advanced" style="display:none;"><?php esc_html_e( 'Hide Advanced Options', 'community-portal' ); ?></span></a>
	</div>
</div>	
