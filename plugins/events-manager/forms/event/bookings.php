<?php
/**
 * Bookings
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

global $EM_Event, $post, $allowedposttags, $EM_Ticket, $col_count;

$reschedule_warnings = ! empty( $EM_Event->event_id ) && $EM_Event->is_recurring() && $EM_Event->event_rsvp;
?>

<div id="event-rsvp-box">
	<input id="event-rsvp" name='event_rsvp' value='1' type='checkbox' <?php echo ( $EM_Event->event_rsvp ) ? 'checked="checked"' : ''; ?> />
	&nbsp;&nbsp;
	<?php esc_html_e( 'Enable registration for this event', 'community-portal' ); ?>
</div>
<div id="event-rsvp-options" style="<?php echo ( $EM_Event->event_rsvp ) ? '' : 'display:none;'; ?>">
	<?php
	do_action( 'em_events_admin_bookings_header', $EM_Event );
	// get tickets here and if there are none, create a blank ticket.
	$EM_Tickets = $EM_Event->get_tickets();
	if ( 0 === count( $EM_Tickets->tickets ) ) {
		$EM_Tickets->tickets[]                 = new EM_Ticket();
		$EM_Tickets->tickets[0]->ticket_spaces = 1000;
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
		// output title.
		if ( get_option( 'dbem_bookings_tickets_single' ) && 1 === count( $EM_Tickets->tickets ) ) {
			?>
			<h4><?php esc_html_e( 'Ticket Options', 'community-portal' ); ?></h4>
			<?php
		} else {
			?>
			<h4><?php esc_html_e( 'Tickets', 'community-portal' ); ?></h4>
			<?php
		}
		// If this event is a recurring template, we need to warn the user that editing tickets will delete previous bookings.
		if ( $reschedule_warnings ) {
			?>
			<div class="recurrence-reschedule-warning">
				<p><?php esc_html_e( 'Modifications to event tickets will cause all bookings to individual recurrences of this event to be deleted.', 'community-portal' ); ?></p>
				<p>			
					<a href="
					<?php
					echo esc_url(
						add_query_arg(
							array(
								'scope'         => 'all',
								'recurrence_id' => $EM_Event->event_id,
							),
							em_get_events_admin_url()
						)
					);
					?>
								">
						<strong><?php esc_html_e( 'You can edit individual recurrences and disassociate them with this recurring event.', 'community-portal' ); ?></strong>
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
		// output ticket options.
		if ( get_option( 'dbem_bookings_tickets_single' ) && 1 === count( $EM_Tickets->tickets ) ) {
			$col_count = 1;
			$EM_Ticket = $EM_Tickets->get_first();
			include em_locate_template( 'forms/ticket-form.php' ); // in future we'll be accessing forms/event/bookings-ticket-form.php directly.
		} else {
			?>
			<p><em><?php esc_html_e( 'You can have single or multiple tickets, where certain tickets become available under certain conditions, e.g. early bookings, group discounts, maximum bookings per ticket, etc.', 'community-portal' ); ?> <?php esc_html_e( 'Basic HTML is allowed in ticket labels and descriptions.', 'community-portal' ); ?></em></p>					
			<table class="form-table">
				<thead>
					<tr valign="top">
						<th colspan="2"><?php esc_html_e( 'Ticket Name', 'community-portal' ); ?></th>
						<th><?php esc_html_e( 'Price', 'community-portal' ); ?></th>
						<th><?php esc_html_e( 'Min/Max', 'community-portal' ); ?></th>
						<th><?php esc_html_e( 'Start/End', 'community-portal' ); ?></th>
						<th><?php esc_html_e( 'Avail. Spaces', 'community-portal' ); ?></th>
						<th><?php esc_html_e( 'Booked Spaces', 'community-portal' ); ?></th>
						<th>&nbsp;</th>
					</tr>
				</thead>    
				<tfoot>
					<tr valign="top">
						<td colspan="8">
							<a href="#" id="em-tickets-add"><?php esc_html_e( 'Add new ticket', 'community-portal' ); ?></a>
						</td>
					</tr>
				</tfoot>
				<?php
					$EM_Ticket           = new EM_Ticket();
					$EM_Ticket->event_id = $EM_Event->event_id;
					array_unshift( $EM_Tickets->tickets, $EM_Ticket ); // prepend template ticket for JS.
					$col_count = 0;
				foreach ( $EM_Tickets->tickets as $EM_Ticket ) {
					?>
						<tbody id="em-ticket-<?php echo esc_attr( $col_count ); ?>" 
							<?php
							if ( 0 === $col_count ) {
								echo 'style="display:none;"';}
							?>
						>
							<tr class="em-tickets-row">
								<td class="ticket-status"><span class="
								<?php
								if ( $EM_Ticket->ticket_id && $EM_Ticket->is_available( true, true ) ) {
									echo 'ticket_on';
								} elseif ( $EM_Ticket->ticket_id > 0 ) {
									echo 'ticket_off';
								} else {
									echo 'ticket_new'; }
								?>
								"></span></td>													
								<td class="ticket-name">
									<span class="ticket_name">
									<?php
									if ( $EM_Ticket->ticket_members ) {
										echo '* ';}
									?>
									<?php echo wp_kses_data( $EM_Ticket->ticket_name ); ?></span>
									<div class="ticket_description"><?php echo wp_kses( $EM_Ticket->ticket_description, $allowedposttags ); ?></div>
									<div class="ticket-actions">
										<a href="#" class="ticket-actions-edit"><?php esc_html_e( 'Edit', 'community-portal' ); ?></a> 
									<?php if ( 0 === $EM_Ticket->get_bookings_count() ) : ?>
										| <a href="<?php bloginfo( 'wpurl' ); ?>/wp-load.php" class="ticket-actions-delete"><?php esc_html_e( 'Delete', 'community-portal' ); ?></a>
										<?php else : ?>
										| <a href="<?php echo esc_url( add_query_arg( 'ticket_id', $EM_Ticket->ticket_id, $EM_Event->get_bookings_url() ) ); ?>"><?php esc_html_e( 'View Bookings', 'community-portal' ); ?></a>
										<?php endif; ?>
									</div>
								</td>
								<td class="ticket-price">
									<span class="ticket_price">
									<?php
									if ( $EM_Ticket->ticket_price ) {
										echo esc_html( $EM_Ticket->get_price_precise( true ) );
									} else {
										echo esc_html_e( 'Free', 'community-portal' ); }
									?>
									</span>
								</td>
								<td class="ticket-limit">
									<span class="ticket_min">
									<?php
									if ( ! empty( $EM_Ticket->ticket_min ) ) {
										echo esc_html( $EM_Ticket->ticket_min );
									} else {
										echo esc_html( '-' ); }
									?>
									</span> / 
									<span class="ticket_max">
									<?php
									if ( ! empty( $EM_Ticket->ticket_max ) ) {
										esc_html( $EM_Ticket->ticket_max );
									} else {
										esc_html( '-' ); }
									?>
									</span>
								</td>
								<td class="ticket-time">
									<span class="ticket_start ticket-dates-from-normal">
									<?php
									if ( ! empty( $EM_Ticket->ticket_start ) ) {
										echo esc_html( $EM_Ticket->start()->format( get_option( 'dbem_date_format' ) ) );
									} else {
										esc_html( '' );
									};
									?>
									</span>
									<span class="ticket_start_recurring_days ticket-dates-from-recurring">
									<?php
									if ( ! empty( $EM_Ticket->ticket_meta['recurrences'] ) ) {
										echo esc_html( $EM_Ticket->ticket_meta['recurrences']['start_days'] );
									}
									?>
									</span>
									<span class="ticket_start_recurring_days_text ticket-dates-from-recurring 
									<?php
									if ( ! empty( $EM_Ticket->ticket_meta['recurrences'] ) && ! is_numeric( $EM_Ticket->ticket_meta['recurrences']['start_days'] ) ) {
										echo esc_attr( 'hidden' );}
									?>
									"><?php esc_html_e( 'day(s)', 'community-portal' ); ?></span>
									<span class="ticket_start_time">
									<?php
									if ( ! empty( $EM_Ticket->ticket_start ) ) {
										echo esc_html( $EM_Ticket->start()->format( em_get_hour_format() ) );
									} else {
										echo esc_html( '' ); }
									?>
									</span>
									<br />
									<span class="ticket_end ticket-dates-from-normal">
									<?php
									if ( ! empty( $EM_Ticket->ticket_end ) ) {
										echo esc_html( $EM_Ticket->end()->format( get_option( 'dbem_date_format' ) ) );
									} else {
										esc_html( '' ); }
									?>
									</span>
									<span class="ticket_end_recurring_days ticket-dates-from-recurring">
									<?php
									if ( ! empty( $EM_Ticket->ticket_meta['recurrences'] ) ) {
										echo esc_html( $EM_Ticket->ticket_meta['recurrences']['end_days'] ); }
									?>
									</span>
									<span class="ticket_end_recurring_days_text ticket-dates-from-recurring 
									<?php
									if ( ! empty( $EM_Ticket->ticket_meta['recurrences'] ) && ! is_numeric( $EM_Ticket->ticket_meta['recurrences']['end_days'] ) ) {
										echo esc_attr( 'hidden' );
									}
									?>
									"><?php esc_html_e( 'day(s)', 'community-portal' ); ?></span>
									<span class="ticket_end_time">
									<?php
									if ( ! empty( $EM_Ticket->ticket_end ) ) {
										echo esc_html( $EM_Ticket->end()->format( em_get_hour_format() ) );
									} else {
										esc_html( '' );
									};
									?>
										</span>
								</td>
								<td class="ticket-qty">
									<span class="ticket_available_spaces"><?php echo esc_html( $EM_Ticket->get_available_spaces() ); ?></span>/
									<span class="ticket_spaces">
									<?php
									if ( $EM_Ticket->get_spaces() ) {
										echo esc_html( $EM_Ticket->get_spaces() );
									} else {
										esc_html( '-' ); }
									?>
									</span>
								</td>
								<td class="ticket-booked-spaces">
									<span class="ticket_booked_spaces"><?php echo esc_html( $EM_Ticket->get_booked_spaces() ); ?></span>
								</td>
							<?php do_action( 'em_event_edit_ticket_td', $EM_Ticket ); ?>
							</tr>
							<tr class="em-tickets-row-form" style="display:none;">
								<td colspan="<?php echo esc_attr( apply_filters( 'em_event_edit_ticket_td_colspan', 7 ) ); ?>">
								<?php include em_locate_template( 'forms/event/bookings-ticket-form.php' ); ?>
									<div class="em-ticket-form-actions">
									<button type="button" class="ticket-actions-edited"><?php esc_html_e( 'Close Ticket Editor', 'community-portal' ); ?></button>
									</div>
								</td>
							</tr>
						</tbody>
						<?php
						$col_count++;
				}
					array_shift( $EM_Tickets->tickets );
				?>
			</table>
			<?php
		}
		?>
		</div>
		<?php if ( $reschedule_warnings ) : // If this event is a recurring template, we need to warn the user that editing tickets will delete previous bookings. ?>
		<div class="recurrence-reschedule-buttons">
		<a href="<?php echo esc_url( add_query_arg( 'recreate_tickets', null ) ); ?>" class="button-secondary em-button em-reschedule-cancel
							<?php
							if ( empty( $_REQUEST['recreate_tickets'] ) ) {
								echo esc_attr( ' reschedule-hidden' );}
							?>
		" data-target=".em-tickets-form">
				<?php esc_html_e( 'Cancel Ticket Recreation', 'community-portal' ); ?>
			</a>
			<a href="<?php echo esc_url( add_query_arg( 'recreate_tickets', '1' ) ); ?>" class="em-reschedule-trigger em-button button-secondary
								<?php
								if ( ! empty( $_REQUEST['recreate_tickets'] ) ) {
									echo esc_attr( ' reschedule-hidden' );}
								?>
			" data-target=".em-tickets-form">
				<?php esc_html_e( 'Modify Recurring Event Tickets ', 'community-portal' ); ?>
			</a>
			<input type="hidden" name="event_recreate_tickets" class="em-reschedule-value" value="<?php echo empty( $_REQUEST['recreate_tickets'] ) ? 0 : 1; ?>" />
		</div>
		<?php endif; ?>
	</div>
	<div id="em-booking-options" class="em-booking-options">
	<?php if ( ! get_option( 'dbem_bookings_tickets_single' ) || count( $EM_Ticket->get_event()->get_tickets()->tickets ) > 1 ) : ?>
	<h4><?php esc_html_e( 'Event Options', 'community-portal' ); ?></h4>
	<p>
		<label><?php esc_html_e( 'Total Spaces', 'community-portal' ); ?></label>
		<input type="text" name="event_spaces" value="
		<?php
		if ( $EM_Event->event_spaces > 0 ) {
			echo esc_html( $EM_Event->event_spaces ); }
		?>
		" /><br />
		<em><?php esc_html_e( 'Individual tickets with remaining spaces will not be available if total booking spaces reach this limit. Leave blank for no limit.', 'community-portal' ); ?></em>
	</p>
	<p>
		<label><?php esc_html_e( 'Maximum Spaces Per Booking', 'community-portal' ); ?></label>
		<input type="text" name="event_rsvp_spaces" value="
		<?php
		if ( $EM_Event->event_rsvp_spaces > 0 ) {
			echo esc_html( $EM_Event->event_rsvp_spaces ); }
		?>
		" /><br />
		<em><?php esc_html_e( 'If set, the total number of spaces for a single booking to this event cannot exceed this amount.', 'community-portal' ); ?><?php esc_html_e( 'Leave blank for no limit.', 'community-portal' ); ?></em>
	</p>
	<p>
		<label><?php esc_html_e( 'Booking Cut-Off Date', 'community-portal' ); ?></label>
		<span class="em-booking-date-normal">
			<span class="em-date-single">
				<input id="em-bookings-date-loc" class="em-date-input-loc" type="text" />
				<input id="em-bookings-date" class="em-date-input" type="hidden" name="event_rsvp_date" value="<?php echo esc_attr( $EM_Event->event_rsvp_date ); ?>" />
			</span>
		</span>
		<span class="em-booking-date-recurring">
			<input type="text" name="recurrence_rsvp_days" size="3" value="<?php echo absint( $EM_Event->recurrence_rsvp_days ); ?>" />
			<?php esc_html_e( 'day(s)', 'community-portal' ); ?>
			<select name="recurrence_rsvp_days_when">
				<option value="before" 
				<?php
				if ( ! empty( $EM_Event->recurrence_rsvp_days ) && $EM_Event->recurrence_rsvp_days <= 0 ) {
					echo esc_attr( 'selected="selected"' );}
				?>
				><?php esc_html_e( 'The event starts before', 'community-portal' ); ?></option>
				<option value="after" 
				<?php
				if ( ! empty( $EM_Event->recurrence_rsvp_days ) && $EM_Event->recurrence_rsvp_days > 0 ) {
					echo esc_attr( 'selected="selected"' );}
				?>
				><?php esc_html_e( 'The event starts after', 'community-portal' ); ?></option>
			</select>
			<?php esc_html_e( 'at', 'community-portal' ); ?>
		</span>
		<input type="text" name="event_rsvp_time" class="em-time-input" maxlength="8" size="8" value="<?php echo esc_html( $EM_Event->rsvp_end()->format( em_get_hour_format() ) ); ?>" />
		<br />
		<em><?php esc_html_e( 'This is the definite date after which bookings will be closed for this event, regardless of individual ticket settings above. Default value will be the event start date.', 'community-portal' ); ?></em>
	</p>
	<?php endif; ?>
	</div>
	<?php
	if ( ! empty( $delete_temp_ticket ) ) {
		array_pop( $EM_Tickets->tickets );
	}
		do_action( 'em_events_admin_bookings_footer', $EM_Event );
	?>
</div>
