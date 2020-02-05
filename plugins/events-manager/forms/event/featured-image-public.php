<div class="event-creator__container">
    <label class="event-creator__label" for="event-image"><?php print __("Event Image", "community-portal"); ?></label>

    <?php
        if ($_REQUEST['event_id']) {
            $event = em_get_event($_REQUEST['event_id']); 
            $event_meta = get_post_meta($event->post_id, 'event-meta');
            $img_url = $event_meta[0]->image_url;

            if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                $img_url = preg_replace("/^http:/i", "https:", $img_url);
            }
        }
    ?>
	<div id="dropzone-photo-uploader" class="event-creator__image-upload<?php if(isset($img_url) && strlen($img_url) > 0): ?> create-group__image-upload--done<?php endif; ?>"<?php if(isset($img_url)): ?> style="background-image: url('<?php print $img_url ?>')"<?php endif; ?>> 
		<button id="image-delete" class="btn event-creator__image-delete <?php echo (!isset($img_url) || strlen($img_url) === 0) ? esc_attr('hidden') : null ?>">
			<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle cx="20" cy="20" r="20" fill="white"/>
				<path d="M29 11L11 29" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M11 11L29 29" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>
		<div class="dz-message" data-dz-message="">
			<div class="event-creator__image-instructions">
				<div class="form__error-container">
					<div class="form__error form__error--image"></div>
				</div>
				<button id="dropzone-trigger" class="dropzone__image-instructions event-creator__image-instructions">
					<?php print __("Click or drag a .PNG or .JPG above. Min dimensions 703px by 400px", "community-portal"); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="image_url" id="image-url" value="<?php print ($img_url ? esc_attr($img_url) : ''  )?>" />
