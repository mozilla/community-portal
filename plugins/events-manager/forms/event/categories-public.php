<?php
  global $EM_Event;
  $categories = EM_Categories::get(array('orderby'=>'name','hide_empty'=>0));
?>
<?php if( count($categories) > 0 ): 
  ?>
  <div class="event-categories event-creator__container">
    <!-- START Categories -->
    <fieldset class="event-creator__fieldset" id="event_categories[]">
      <legend class="event-creator__label" for="event_categories[]"><?php _e( 'Select tag(s) for your event', 'commuity-portal'); ?></legend>
        <?php
          $selected = $EM_Event->get_categories()->get_ids();
          foreach($categories as $category) {
          ?>
          <input 
            name="event_categories[]" 
            class="event-creator__checkbox" 
            id="<?php echo esc_attr($category->id) ?>"
            type="checkbox"  
            value="<?php echo esc_attr($category->id) ?>"
            <?php if (in_array($category->id, $selected)) {
              echo esc_attr('checked'); 
            } 
            ?>
          />
          <label class="event-creator__tag" for="<?php echo esc_attr($category->id)?>"><?php echo __($category->name) ?></label>
          <?php
          }
        ?>
        <!-- <input type="hidden" name="event_categories[]" id="event_categories--all" value=""> -->
      <!-- END Categories -->
    </fieldset>
  </div>
<?php endif; ?>