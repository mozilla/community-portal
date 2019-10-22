<div class="create-group__input-container create-group__input-container--short">
  <label class="create-group__label" for="group-desc"><?php print __("Group Photo"); ?></label>
  <div id="group-photo-uploader" class="create-group__image-upload">
      <svg width="75" height="75" viewBox="0 0 75 75" fill="none" xmlns="http://www.w3.org/2000/svg" class="create-group__upload-image-svg">
          <path d="M59.375 9.375H15.625C12.1732 9.375 9.375 12.1732 9.375 15.625V59.375C9.375 62.8268 12.1732 65.625 15.625 65.625H59.375C62.8268 65.625 65.625 62.8268 65.625 59.375V15.625C65.625 12.1732 62.8268 9.375 59.375 9.375Z" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M26.5625 31.25C29.1513 31.25 31.25 29.1513 31.25 26.5625C31.25 23.9737 29.1513 21.875 26.5625 21.875C23.9737 21.875 21.875 23.9737 21.875 26.5625C21.875 29.1513 23.9737 31.25 26.5625 31.25Z" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M65.625 46.875L50 31.25L15.625 65.625" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
  </div>
  <div class="create-group__image-instructions"><?php print __("Click or drag a photo above"); ?></div>
  <input type="hidden" name="image_url" id="image-url" value="<?php print (isset($form['image_url'])) ? $form['image_url'] : '' ?>" />
</div>