<div class="event-creator__container">
  <label class="event-creator__label" for="event-image"><?php print __("Event Image*"); ?></label>
    <?php
      if ($_REQUEST['event_id']):
        $event = em_get_event($_REQUEST['event_id']); 
        $img_url = get_post_meta($event->post_id, 'event-img-url');
      endif;
    ?>
    <div id="group-photo-uploader" class="event-creator__image-upload"
      <?php if ($img_url[0] !== '' && $img_url[0]):?>
        style="background-image: url(<?php echo $img_url[0]?>); background-size: contain;"
      <?php
        endif;
      ?>
    >
  </div>
  <p class="event-creator__image-instructions"><?php print __("Click or drag a .PNG or .JPG above."); ?></p>
  <input type="hidden" name="image_url" id="image-url" value="<?php print (isset($form['image_url'])) ? $form['image_url'] : '' ?>" />
</div>