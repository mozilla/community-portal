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
		<div class="wide wide--md-half">
			<label for="start-date" class="em-event-text event-creator__label"><?php _e('Start date *', 'commuity-portal'); ?></label>				
			<input id="start-date" class="em-date-start em-date-input-loc event-creator__input" type="text" autocomplete="off" required/>
			<input class="em-date-input" type="hidden" name="event_start_date" value="<?php echo $EM_Event->start()->getDate(); ?>" />
			<div class="form__error-container">
				<p class="form__error"><?php _e('This field is required', 'community-portal'); ?></p>
			</div>
		</div>
		<div class="wide wide--md-half">
			<label for="end-date" class="em-event-text event-creator__label"><?php _e('End Date *','commuity-portal'); ?></label>
			<input id="end-date" class="em-date-end em-date-input-loc event-creator__input" autocomplete="off" type="text" required/>
			<input class="em-date-input" type="hidden" name="event_end_date" value="<?php echo $EM_Event->end()->getDate(); ?>" />
			<div class="form__error-container">
				<p class="form__error"><?php _e('This field is required', 'community-portal'); ?></p>
			</div>
		</div>
	</div>
	<div class="event-creator__three-up">
		<div class="wide wide--md-third">
			<label for="start-time" class="em-event-text event-creator__label"><?php _e('Start time *','commuity-portal'); ?></label>
			<input id="start-time" class="em-time-input em-time-start event-creator__dropdown" type="text" size="8" maxlength="8" name="event_start_time" value="<?php echo $EM_Event->start()->i18n($hours_format); ?>" required />
			<div class="form__error-container">
				<p class="form__error"><?php _e('This field is required', 'community-portal'); ?></p>
			</div>
		</div>
		<div class="wide wide--md-third">
			<label for="end-time" class="em-event-text event-creator__label"><?php _e('End time *','commuity-portal'); ?></label>
			<input id="end-time" class="em-time-input em-time-end event-creator__dropdown" type="text" size="8" maxlength="8" name="event_end_time" value="<?php echo $EM_Event->end()->i18n($hours_format); ?>" required />
			<div class="form__error-container">
				<p class="form__error"><?php _e('This field is required', 'community-portal'); ?></p>
			</div>
		</div>
		<?php if( get_option('dbem_timezone_enabled') ): ?>
		<div class="thin wide--md-third">
			<label class="event-creator__label" for="event-timezone event-creator__label"><?php _e('Timezone *', 'commuity-portal'); ?></label>
			<select class="" id="event-timezone" name="event_timezone" aria-describedby="timezone-description" required>
				<?php echo wp_timezone_choice( $EM_Event->get_timezone()->getName(), get_user_locale() ); ?>
			</select>
			<div class="form__error-container">
				<p class="form__error"><?php _e('This field is required', 'community-portal'); ?></p>
			</div>
		</div>
    <?php endif; ?>
	</div>
</div>

