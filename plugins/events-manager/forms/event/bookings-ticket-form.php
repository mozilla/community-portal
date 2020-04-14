<?php 
/* 
 * Used for both multiple and single tickets. $col_count will always be 1 in single ticket mode, and be a unique number for each ticket starting from 1 
 * This form should have $EM_Ticket and $col_count available globally. 
 */
global $col_count, $EM_Ticket; /* @var EM_Ticket $EM_Ticket */
$col_count = absint($col_count); //now we know it's a number
$EM_Ticket->ticket_spaces = 1000;
?>
<div class="em-ticket-form">
	<input type="hidden" name="em_tickets[<?php echo $col_count; ?>][ticket_id]" class="ticket_id" value="<?php echo esc_attr($EM_Ticket->ticket_id) ?>" />
	<div class="em-ticket-form-main">
		<div class="ticket-name">
			<label title="<?php _e('Enter a ticket name.','community-portal'); ?>"><?php _e('Name','community-portal') ?></label>
			<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_name]" value="<?php echo esc_attr($EM_Ticket->ticket_name) ?>" class="ticket_name" />
		</div>
		<div class="ticket-description">
			<label><?php _e('Description', 'community-portal') ?></label>
			<textarea name="em_tickets[<?php echo $col_count; ?>][ticket_description]" class="ticket_description"><?php echo esc_html(wp_unslash($EM_Ticket->ticket_description)) ?></textarea>
		</div>
		<div class="ticket-price"><label><?php _e('Price', 'community-portal') ?></label><input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_price]" class="ticket_price" value="<?php echo esc_attr($EM_Ticket->get_price_precise(true)) ?>" /></div>
		<div class="ticket-spaces">
			<label title="<?php _e('Enter a maximum number of spaces (required).', 'community-portal'); ?>"><?php _e('Spaces','community-portal') ?></label>
			<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_spaces]" value=<?php echo esc_attr('1000') ?> class="ticket_spaces" />
		</div>
	</div>
	<div class="em-ticket-form-advanced" style="display:none;">
		<div class="ticket-spaces ticket-spaces-min">
      <label title="<?php _e('Leave either blank for no upper/lower limit.', 'community-portal'); ?>"><?php echo esc_html_x('At least','spaces per booking','community-portal');?></label>
      
			<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_min]" value="<?php echo esc_attr($EM_Ticket->ticket_min) ?>" class="ticket_min" />
			<?php _e('spaces per booking', 'community-portal')?>
		</div>
		<div class="ticket-spaces ticket-spaces-max">
			<label title="<?php _e('Leave either blank for no upper/lower limit.','community-portal'); ?>"><?php echo esc_html_x('At most','spaces per booking', 'community-portal'); ?></label>
			<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_max]" value="<?php echo esc_attr($EM_Ticket->ticket_max) ?>" class="ticket_max" />
			<?php _e('spaces per booking', 'community-portal')?>
		</div>
		<div class="ticket-dates em-time-range em-date-range">
			<div class="ticket-dates-from">
				<label title="<?php _e('Add a start or end date (or both) to impose time constraints on ticket availability. Leave either blank for no upper/lower limit.','community-portal'); ?>">
					<?php _e('Available from','community-portal') ?>
				</label>
				<div class="ticket-dates-from-normal">
					<input type="text" name="ticket_start_pub"  class="em-date-input-loc em-date-start" />
					<input type="hidden" name="em_tickets[<?php echo $col_count; ?>][ticket_start]" class="em-date-input ticket_start" value="<?php echo ( !empty($EM_Ticket->ticket_start) ) ? $EM_Ticket->start()->format("Y-m-d"):''; ?>" />
				</div>
				<div class="ticket-dates-from-recurring">
					<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_start_recurring_days]" size="3" value="<?php if( !empty($EM_Ticket->ticket_meta['recurrences']['start_days']) && is_numeric($EM_Ticket->ticket_meta['recurrences']['start_days'])) echo absint($EM_Ticket->ticket_meta['recurrences']['start_days']); ?>" />
					<?php _e('day(s)','community-portal'); ?>
					<select name="em_tickets[<?php echo $col_count; ?>][ticket_start_recurring_when]" class="ticket-dates-from-recurring-when">
						<option value="before" <?php if( isset($EM_Ticket->ticket_meta['recurrences']['start_days']) && $EM_Ticket->ticket_meta['recurrences']['start_days'] <= 0) echo 'selected="selected"'; ?>><?php echo _e(sprintf(_x('%s the event starts','before or after','community-portal'),_e('Before','community-portal'))); ?></option>
						<option value="after" <?php if( !empty($EM_Ticket->ticket_meta['recurrences']['start_days']) && $EM_Ticket->ticket_meta['recurrences']['start_days'] > 0) echo 'selected="selected"'; ?>><?php echo _e(sprintf(_x('%s the event starts','before or after','community-portal'),_e('After','community-portal'))); ?></option>
					</select>
				</div>
				<?php echo esc_html_x('at', 'time','community-portal'); ?>
				<input id="start-time" class="em-time-input em-time-start" type="text" size="8" maxlength="8" name="em_tickets[<?php echo $col_count; ?>][ticket_start_time]" value="<?php echo ( !empty($EM_Ticket->ticket_start) ) ? $EM_Ticket->start()->format( em_get_hour_format() ):''; ?>" />
			</div>
			<div class="ticket-dates-to">
				<label title="<?php _e('Add a start or end date (or both) to impose time constraints on ticket availability. Leave either blank for no upper/lower limit.','community-portal'); ?>">
					<?php _e('Available until','community-portal') ?>
				</label>
				<div class="ticket-dates-to-normal">
					<input type="text" name="ticket_end_pub" class="em-date-input-loc em-date-end" />
					<input type="hidden" name="em_tickets[<?php echo $col_count; ?>][ticket_end]" class="em-date-input ticket_end" value="<?php echo ( !empty($EM_Ticket->ticket_end) ) ? $EM_Ticket->end()->format("Y-m-d"):''; ?>" />
				</div>
				<div class="ticket-dates-to-recurring">
					<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_end_recurring_days]" size="3" value="<?php if( isset($EM_Ticket->ticket_meta['recurrences']['end_days']) && $EM_Ticket->ticket_meta['recurrences']['end_days'] !== false ) echo absint($EM_Ticket->ticket_meta['recurrences']['end_days']); ?>" />
					<?php _e('day(s)','community-portal'); ?>
					<select name="em_tickets[<?php echo $col_count; ?>][ticket_end_recurring_when]" class="ticket-dates-to-recurring-when">
						<option value="before" <?php if( isset($EM_Ticket->ticket_meta['recurrences']['end_days']) && $EM_Ticket->ticket_meta['recurrences']['end_days'] <= 0) echo 'selected="selected"'; ?>><?php echo esc_html(sprintf(_x('%s the event starts','before or after','community-portal'),_e('Before','community-portal'))); ?></option>
						<option value="after" <?php if( !empty($EM_Ticket->ticket_meta['recurrences']['end_days']) && $EM_Ticket->ticket_meta['recurrences']['end_days'] > 0) echo 'selected="selected"'; ?>><?php echo esc_html(sprintf(_x('%s the event starts','before or after','community-portal'),_e('After','community-portal'))); ?></option>
					</select>
				</div>
				<?php echo esc_html_x('at', 'time','community-portal'); ?>
				<input id="end-time" class="em-time-input em-time-end ticket-times-to-normal" type="text" size="8" maxlength="8" name="em_tickets[<?php echo $col_count; ?>][ticket_end_time]" value="<?php echo ( !empty($EM_Ticket->ticket_end) ) ? $EM_Ticket->end()->format( em_get_hour_format() ):''; ?>" />
			</div>
		</div>
		<?php if( !get_option('dbem_bookings_tickets_single') || count($EM_Ticket->get_event()->get_tickets()->tickets) > 1 ): ?>
		<div class="ticket-required">
			<label title="<?php _e('If checked every booking must select one or the minimum number of this ticket.','community-portal'); ?>"><?php _e('Required?','community-portal') ?></label>
			<input type="checkbox" value="1" name="em_tickets[<?php echo $col_count; ?>][ticket_required]" <?php if($EM_Ticket->ticket_required) echo 'checked="checked"'; ?> class="ticket_required" />
		</div>
		<?php endif; ?>
		<div class="ticket-type">
			<label><?php _e('Available for','community-portal') ?></label>
			<select name="em_tickets[<?php echo $col_count; ?>][ticket_type]" class="ticket_type">
				<option value=""><?php _e('Everyone','community-portal'); ?></option>
				<option value="members" <?php if($EM_Ticket->ticket_members) echo 'selected="selected"'; ?>><?php _e('Logged In Users','community-portal'); ?></option>
				<option value="guests" <?php if($EM_Ticket->ticket_guests) echo 'selected="selected"'; ?>><?php _e('Guest Users','community-portal'); ?></option>
			</select>
		</div>
		<div class="ticket-roles" <?php if( !$EM_Ticket->ticket_members ): ?>style="display:none;"<?php endif; ?>>
			<label><?php _e('Restrict to','community-portal'); ?></label>
			<div>
				<?php 
				$WP_Roles = new WP_Roles();
				foreach($WP_Roles->roles as $role => $role_data){ /* @var $WP_Role WP_Role */
					?>
					<input type="checkbox" name="em_tickets[<?php echo $col_count; ?>][ticket_members_roles][]" value="<?php echo esc_attr($role); ?>" <?php if( in_array($role, $EM_Ticket->ticket_members_roles) ) echo 'checked="checked"' ?> class="ticket_members_roles" /> <?php echo esc_html($role_data['name']); ?><br />
					<?php
				}
				?>
			</div>
		</div>
		<?php do_action('em_ticket_edit_form_fields', $col_count, $EM_Ticket); //do not delete, add your extra fields this way, remember to save them too! ?>
	</div>
	<div class="ticket-options">
		<a href="#" class="ticket-options-advanced show"><span class="show-advanced"><?php _e('Show Advanced Options','community-portal'); ?></span><span class="hide-advanced" style="display:none;"><?php _e('Hide Advanced Options','community-portal'); ?></span></a>
	</div>
</div>	