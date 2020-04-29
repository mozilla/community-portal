<?php
/**
 * Bookings
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */


global $EM_Event, $EM_Ticket, $post, $allowedposttags, $col_count;
$em_event            = $EM_Event;
$reschedule_warnings = ! empty( $em_event->event_id ) && $em_event->is_recurring() && $em_event->event_rsvp;
?>
<div id="event-rsvp-box">
	<input id="event-rsvp" name='event_rsvp' value='1' type='checkbox' <?php echo ( $em_event->event_rsvp ) ? 'checked="checked"' : ''; ?> />
	&nbsp;&nbsp;
	<?php _e( 'Enable registration for this event', 'commuity-portal' ); ?>
</div>
<div id="event-rsvp-options" style="<?php echo ( $em_event->event_rsvp ) ? '' : 'display:none;'; ?>">
	<?php
	do_action( 'em_events_admin_bookings_header', $em_event );
	// get tickets here and if there are none, create a blank ticket
	$em_tickets = $em_event->get_tickets();
	if ( count( $em_tickets->tickets ) == 0 ) {
		$em_tickets->tickets[]                 = new EM_Ticket();
		$em_tickets->tickets[0]->ticket_spaces = 1000;
		$delete_temp_ticket                    = true;
	}
	?>
	<div class="event-rsvp-options-tickets 
	<?php
	if ( $reschedule_warnings ) {
		echo 'em-recurrence-reschedule';}
	?>
	">
		<?php
		// output title
		if ( get_option( 'dbem_bookings_tickets_single' ) && count( $em_tickets->tickets ) == 1 ) {
			?>
			<h4><?php _e( 'Ticket Options', 'commuity-portal' ); ?></h4>
			<?php
		} else {
			?>
			<h4><?php _e( 'Tickets', 'commuity-portal' ); ?></h4>
			<?php
		}
		// If this event is a recurring template, we need to warn the user that editing tickets will delete previous bookings
		if ( $reschedule_warnings ) {
			?>
			<div class="recurrence-reschedule-warning">
				<p><?php _e( 'Modifications to event tickets will cause all bookings to individual recurrences of this event to be deleted.', 'commuity-portal' ); ?></p>
				<p>			
					<a href="
					<?php
					echo esc_url(
						add_query_arg(
							array(
								'scope'         => 'all',
								'recurrence_id' => $em_event->event_id,
							),
							em_get_events_admin_url()
						)
					);
					?>
								">
						<strong><?php _e( 'You can edit individual recurrences and disassociate them with this recurring event.', 'commuity-portal' ); ?></strong>
					</a>
				</p>
			</div>
			<?php
		}
		?>
		<div id="em-tickets-form" class="em-tickets-form
		<?php
		if ( $reschedule_warnings && empty( $_REQUEST['recreate_tickets'] ) ) {
			echo ' reschedule-hidden';}
		?>
		">
		<?php
		// output ticket options
		if ( get_option( 'dbem_bookings_tickets_single' ) && count( $em_tickets->tickets ) == 1 ) {
			$col_count = 1;
			$em_ticket = $em_tickets->get_first();
			include em_locate_template( 'forms/ticket-form.php' ); // in future we'll be accessing forms/event/bookings-ticket-form.php directly
		} else {
			?>
			<p><em><?php _e( 'You can have single or multiple tickets, where certain tickets become available under certain conditions, e.g. early bookings, group discounts, maximum bookings per ticket, etc.', 'commuity-portal' ); ?> <?php _e( 'Basic HTML is allowed in ticket labels and descriptions.', 'commuity-portal' ); ?></em></p>					
			<table class="form-table">
				<thead>
					<tr valign="top">
						<th colspan="2"><?php _e( 'Ticket Name', 'commuity-portal' ); ?></th>
						<th><?php _e( 'Price', 'commuity-portal' ); ?></th>
						<th><?php _e( 'Min/Max', 'commuity-portal' ); ?></th>
						<th><?php _e( 'Start/End', 'commuity-portal' ); ?></th>
						<th><?php _e( 'Avail. Spaces', 'commuity-portal' ); ?></th>
						<th><?php _e( 'Booked Spaces', 'commuity-portal' ); ?></th>
						<th>&nbsp;</th>
					</tr>
				</thead>    
				<tfoot>
					<tr valign="top">
						<td colspan="8">
							<a href="#" id="em-tickets-add"><?php _e( 'Add new ticket', 'commuity-portal' ); ?></a>
						</td>
					</tr>
				</tfoot>
				<?php
					$em_ticket           = new EM_Ticket();
					$em_ticket->event_id = $em_event->event_id;
					array_unshift( $em_tickets->tickets, $em_ticket ); // prepend template ticket for JS
					$col_count = 0;
				foreach ( $em_tickets->tickets as $em_ticket ) {
					/* @var $em_ticket EM_Ticket */
					?>
						<tbody id="em-ticket-<?php echo $col_count; ?>" 
														<?php
														if ( $col_count == 0 ) {
															echo 'style="display:none;"';}
														?>
						>
							<tr class="em-tickets-row">
								<td class="ticket-status"><span class="
								<?php
								if ( $em_ticket->ticket_id && $em_ticket->is_available( true, true ) ) {
									echo 'ticket_on';
								} elseif ( $em_ticket->ticket_id > 0 ) {
									echo 'ticket_off';
								} else {
									echo 'ticket_new'; }
								?>
								"></span></td>													
								<td class="ticket-name">
									<span class="ticket_name">
									<?php
									if ( $em_ticket->ticket_members ) {
										echo '* ';}
									?>
									<?php echo wp_kses_data( $em_ticket->ticket_name ); ?></span>
									<div class="ticket_description"><?php echo wp_kses( $em_ticket->ticket_description, $allowedposttags ); ?></div>
									<div class="ticket-actions">
										<a href="#" class="ticket-actions-edit"><?php _e( 'Edit', 'commuity-portal' ); ?></a> 
									<?php if ( $em_ticket->get_bookings_count() == 0 ) : ?>
										| <a href="<?php bloginfo( 'wpurl' ); ?>/wp-load.php" class="ticket-actions-delete"><?php _e( 'Delete', 'commuity-portal' ); ?></a>
										<?php else : ?>
										| <a href="<?php echo esc_url( add_query_arg( 'ticket_id', $em_ticket->ticket_id, $em_event->get_bookings_url() ) ); ?>"><?php _e( 'View Bookings', 'commuity-portal' ); ?></a>
										<?php endif; ?>
									</div>
								</td>
								<td class="ticket-price">
									<span class="ticket_price"><?php echo ( $em_ticket->ticket_price ) ? esc_html( $em_ticket->get_price_precise( true ) ) : esc_html_e( 'Free', 'commuity-portal' ); ?></span>
								</td>
								<td class="ticket-limit">
									<span class="ticket_min">
									<?php echo ( ! empty( $em_ticket->ticket_min ) ) ? esc_html( $em_ticket->ticket_min ) : '-'; ?>
									</span> / 
									<span class="ticket_max"><?php echo ( ! empty( $em_ticket->ticket_max ) ) ? esc_html( $em_ticket->ticket_max ) : '-'; ?></span>
								</td>
								<td class="ticket-time">
									<span class="ticket_start ticket-dates-from-normal"><?php echo ( ! empty( $em_ticket->ticket_start ) ) ? $em_ticket->start()->format( get_option( 'dbem_date_format' ) ) : ''; ?></span>
									<span class="ticket_start_recurring_days ticket-dates-from-recurring">
									<?php
									if ( ! empty( $em_ticket->ticket_meta['recurrences'] ) ) {
										echo $em_ticket->ticket_meta['recurrences']['start_days'];}
									?>
									</span>
									<span class="ticket_start_recurring_days_text ticket-dates-from-recurring 
									<?php
									if ( ! empty( $em_ticket->ticket_meta['recurrences'] ) && ! is_numeric( $em_ticket->ticket_meta['recurrences']['start_days'] ) ) {
										echo 'hidden';}
									?>
									"><?php _e( 'day(s)', 'commuity-portal' ); ?></span>
									<span class="ticket_start_time"><?php echo ( ! empty( $em_ticket->ticket_start ) ) ? $em_ticket->start()->format( em_get_hour_format() ) : ''; ?></span>
									<br />
									<span class="ticket_end ticket-dates-from-normal"><?php echo ( ! empty( $em_ticket->ticket_end ) ) ? $em_ticket->end()->format( get_option( 'dbem_date_format' ) ) : ''; ?></span>
									<span class="ticket_end_recurring_days ticket-dates-from-recurring">
									<?php
									if ( ! empty( $em_ticket->ticket_meta['recurrences'] ) ) {
										echo $em_ticket->ticket_meta['recurrences']['end_days'];}
									?>
									</span>
									<span class="ticket_end_recurring_days_text ticket-dates-from-recurring 
									<?php
									if ( ! empty( $em_ticket->ticket_meta['recurrences'] ) && ! is_numeric( $em_ticket->ticket_meta['recurrences']['end_days'] ) ) {
										echo 'hidden';}
									?>
									"><?php _e( 'day(s)', 'commuity-portal' ); ?></span>
									<span class="ticket_end_time"><?php echo ( ! empty( $em_ticket->ticket_end ) ) ? $em_ticket->end()->format( em_get_hour_format() ) : ''; ?></span>
								</td>
								<td class="ticket-qty">
									<span class="ticket_available_spaces"><?php echo $em_ticket->get_available_spaces(); ?></span>/
									<span class="ticket_spaces"><?php echo $em_ticket->get_spaces() ? $em_ticket->get_spaces() : '-'; ?></span>
								</td>
								<td class="ticket-booked-spaces">
									<span class="ticket_booked_spaces"><?php echo $em_ticket->get_booked_spaces(); ?></span>
								</td>
							<?php do_action( 'em_event_edit_ticket_td', $em_ticket ); ?>
							</tr>
							<tr class="em-tickets-row-form" style="display:none;">
								<td colspan="<?php echo apply_filters( 'em_event_edit_ticket_td_colspan', 7 ); ?>">
								<?php include em_locate_template( 'forms/event/bookings-ticket-form.php' ); ?>
									<div class="em-ticket-form-actions">
									<button type="button" class="ticket-actions-edited"><?php _e( 'Close Ticket Editor', 'commuity-portal' ); ?></button>
									</div>
								</td>
							</tr>
						</tbody>
						<?php
						$col_count++;
				}
					array_shift( $em_tickets->tickets );
				?>
			</table>
			<?php
		}
		?>
		</div>
		<?php if ( $reschedule_warnings ) : // If this event is a recurring template, we need to warn the user that editing tickets will delete previous bookings ?>
		<div class="recurrence-reschedule-buttons">
		<a href="<?php echo esc_url( add_query_arg( 'recreate_tickets', null ) ); ?>" class="button-secondary em-button em-reschedule-cancel
							<?php
							if ( empty( $_REQUEST['recreate_tickets'] ) ) {
								echo ' reschedule-hidden';}
							?>
		" data-target=".em-tickets-form">
				<?php _e( 'Cancel Ticket Recreation', 'commuity-portal' ); ?>
			</a>
			<a href="<?php echo esc_url( add_query_arg( 'recreate_tickets', '1' ) ); ?>" class="em-reschedule-trigger em-button button-secondary
								<?php
								if ( ! empty( $_REQUEST['recreate_tickets'] ) ) {
									echo ' reschedule-hidden';}
								?>
			" data-target=".em-tickets-form">
				<?php _e( 'Modify Recurring Event Tickets ', 'commuity-portal' ); ?>
			</a>
			<input type="hidden" name="event_recreate_tickets" class="em-reschedule-value" value="<?php echo empty( $_REQUEST['recreate_tickets'] ) ? 0 : 1; ?>" />
		</div>
		<?php endif; ?>
	</div>
	<div id="em-booking-options" class="em-booking-options">
	<?php if ( ! get_option( 'dbem_bookings_tickets_single' ) || count( $em_ticket->get_event()->get_tickets()->tickets ) > 1 ) : ?>
	<h4><?php _e( 'Event Options', 'commuity-portal' ); ?></h4>
	<p>
		<label><?php _e( 'Total Spaces', 'commuity-portal' ); ?></label>
		<input type="text" name="event_spaces" value="
		<?php
		if ( $em_event->event_spaces > 0 ) {
			echo $em_event->event_spaces; }
		?>
		" /><br />
		<em><?php _e( 'Individual tickets with remaining spaces will not be available if total booking spaces reach this limit. Leave blank for no limit.', 'commuity-portal' ); ?></em>
	</p>
	<p>
		<label><?php _e( 'Maximum Spaces Per Booking', 'commuity-portal' ); ?></label>
		<input type="text" name="event_rsvp_spaces" value="
		<?php
		if ( $em_event->event_rsvp_spaces > 0 ) {
			echo $em_event->event_rsvp_spaces; }
		?>
		" /><br />
		<em><?php _e( 'If set, the total number of spaces for a single booking to this event cannot exceed this amount.', 'commuity-portal' ); ?><?php _e( 'Leave blank for no limit.', 'commuity-portal' ); ?></em>
	</p>
	<p>
		<label><?php _e( 'Booking Cut-Off Date', 'commuity-portal' ); ?></label>
		<span class="em-booking-date-normal">
			<span class="em-date-single">
				<input id="em-bookings-date-loc" class="em-date-input-loc" type="text" />
				<input id="em-bookings-date" class="em-date-input" type="hidden" name="event_rsvp_date" value="<?php echo $em_event->event_rsvp_date; ?>" />
			</span>
		</span>
		<span class="em-booking-date-recurring">
			<input type="text" name="recurrence_rsvp_days" size="3" value="<?php echo absint( $em_event->recurrence_rsvp_days ); ?>" />
			<?php _e( 'day(s)', 'commuity-portal' ); ?>
			<select name="recurrence_rsvp_days_when">
				<option value="before" 
				<?php
				if ( ! empty( $em_event->recurrence_rsvp_days ) && $em_event->recurrence_rsvp_days <= 0 ) {
					echo 'selected="selected"';}
				?>
				><?php echo sprintf( _x( '%s the event starts', 'before or after', 'commuity-portal' ), _e( 'Before', 'commuity-portal' ) ); ?></option>
				<option value="after" 
				<?php
				if ( ! empty( $em_event->recurrence_rsvp_days ) && $em_event->recurrence_rsvp_days > 0 ) {
					echo 'selected="selected"';}
				?>
				><?php echo sprintf( _x( '%s the event starts', 'before or after', 'commuity-portal' ), _e( 'After', 'commuity-portal' ) ); ?></option>
			</select>
			<?php _e( 'at', 'commuity-portal' ); ?>
		</span>
		<input type="text" name="event_rsvp_time" class="em-time-input" maxlength="8" size="8" value="<?php echo $em_event->rsvp_end()->format( em_get_hour_format() ); ?>" />
		<br />
		<em><?php _e( 'This is the definite date after which bookings will be closed for this event, regardless of individual ticket settings above. Default value will be the event start date.', 'commuity-portal' ); ?></em>
	</p>
	<?php endif; ?>
	</div>
	<?php
	if ( ! empty( $delete_temp_ticket ) ) {
		array_pop( $em_tickets->tickets );
	}
		do_action( 'em_events_admin_bookings_footer', $em_event );
	?>
</div>
