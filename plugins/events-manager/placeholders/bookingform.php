<?php  
    //count tickets and available tickets
    $EM_Tickets = $EM_Event->get_bookings()->get_tickets()
?>

<div id="em-booking" class="em-booking <?php if( get_option('dbem_css_rsvp') ) echo 'css-booking'; ?>">
    <?php
        $cancel = $_REQUEST['cancel'];  
		// We are firstly checking if the user has already booked a ticket at this event, if so offer a link to view their bookings.
        $EM_Booking = $EM_Event->get_bookings()->has_booking();

        if($EM_Booking !== false && $cancel !== null) {
            
            $EM_Booking->cancel();
            $EM_Booking->delete();
            
            $updatedUrl = remove_query_arg('cancel', $_SERVER['REQUEST_URI']);

    ?> 
        <script type="text/javascript">
            window.history.replaceState("","", "<?php echo $updatedUrl ?>")
        </script>
    <?php 
            $EM_Booking = $EM_Event->get_bookings()->has_booking();
        }   
    ?>
    
    <?php  if(is_object($EM_Booking)): ?>
    <a class="em-bookings-cancel events-single__cancel btn btn--submit btn--dark" href="<?php echo esc_attr(add_query_arg(array('cancel' => true), $_SERVER['REQUEST_URI']))?>" onclick="if( !confirm('<?php print __("Are you sure you dont want to attend this event?", "community-portal"); ?>') ){ return false; }">
        <?php echo __('Will Not Attend') ?>
    </a>
    <?php else: ?>
    <form 
        class="em-booking-form" 
        name='booking-form' 
        method='post' 
        action='<?php echo remove_query_arg('cancel', apply_filters('em_booking_form_action_url','')); ?>'
    >
        <input type='hidden' name='action' value='booking_add'/>
        <input type='hidden' name='event_id' value='<?php echo $EM_Event->get_bookings()->event_id; ?>'/>
        <input type='hidden' name='_wpnonce' value='<?php echo wp_create_nonce('booking_add'); ?>'/>
        <?php
            $count = 0;
            foreach ($EM_Tickets as $ticket) {
                if ($count < 1) {
        ?>
        <input type="hidden" name="<?php echo __("em_tickets[".$ticket->ticket_id."][spaces]") ?>" value="1">
        <?php   
                    $count++;
                }
            }
        ?>
        <input type="submit" class="btn btn--dark btn--submit <?php if(is_admin()) echo 'button-primary '; ?>em-booking-submit" id="em-booking-submit" value="<?php echo esc_attr('Attend'); ?>" />
    </form>	
    <?php endif; ?>
</div>