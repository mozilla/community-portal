<?php
/* Used by the buddypress and front-end editors to display event time-related information */
global $EM_Event;
$days_names = em_get_days_names();
$hours_format = em_get_hour_format();
$admin_recurring = is_admin() && $EM_Event->is_recurring();
?>
<!-- START recurrence postbox -->
<div id="em-form-with-recurrence" class="event-form-with-recurrence event-form-when">
	<div class="em-date-range event-creator__three-up">
    <div class="wide">
      <label for="start-date" class="em-event-text event-creator__label"><?php _e ( 'Start date ', 'events-manager'); ?></label>				
      <input id="start-date" class="em-date-start em-date-input-loc event-creator__input" type="text" />
    </div>
    <div class="wide">
      <label for="end-date" class="em-event-text event-creator__label"><?php _e('End Date','events-manager'); ?></label>
      <input id="end-date" class="em-date-end em-date-input-loc event-creator__input" type="text" />
    </div>
  </div>
	<div class="event-creator__three-up">
    <div class="wide">
      <label for="start-time" class="em-event-text event-creator__label"><?php _e('Start time','events-manager'); ?></label>
      <input id="start-time" class="em-time-input em-time-start event-creator__dropdown" type="text" size="8" maxlength="8" name="event_start_time" value="<?php echo $EM_Event->start()->i18n($hours_format); ?>" />
    </div>
    <div class="wide">
      <label for="end-time" class="em-event-text event-creator__label"><?php _e('End time','events-manager'); ?></label>
      <input id="end-time" class="em-time-input em-time-end event-creator__dropdown" type="text" size="8" maxlength="8" name="event_end_time" value="<?php echo $EM_Event->end()->i18n($hours_format); ?>" />
    </div>
    <?php if( get_option('dbem_timezone_enabled') ): ?>
      <div class="thin">
        <label class="event-creator__label" for="event-timezone event-creator__label"><?php esc_html_e('Timezone', 'events-manager'); ?></label>
        <select class="" id="event-timezone" name="event_timezone" aria-describedby="timezone-description">
          <?php echo wp_timezone_choice( $EM_Event->get_timezone()->getName(), get_user_locale() ); ?>
        </select>
      </div>
    <?php endif; ?>

  </div>
</div>