<div class="event-creator__container">
  <label class="event-creator__label" for="event-image"><?php print __("Event Image*"); ?></label>
    <?php
      if ($_REQUEST['event_id']):
        $event = em_get_event($_REQUEST['event_id']); 
        $event_meta = get_post_meta($event->post_id, 'event-meta');
        $img_url = $event_meta[0]->image_url;
      endif;
    ?>
    <div id="group-photo-uploader" class="event-creator__image-upload"
      <?php if ($img_url !== '' && $img_url):?>
        style="background-image: url(<?php echo esc_url_raw($img_url) ?>); background-size: contain;"
      <?php
        endif;
      ?>
    >
  </div>
  <p class="event-creator__image-instructions"><?php print __("Click or drag a .PNG or .JPG above."); ?></p>
  <input type="hidden" name="image_url" id="image-url" value="<?php print ($img_url ? esc_attr($img_url) : ''  )?>" />
</div>