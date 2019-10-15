<?php
  global $EM_Event;
  $categories = EM_Categories::get(array('orderby'=>'name','hide_empty'=>0));
?>
<?php if( count($categories) > 0 ): ?>
  <div class="event-categories event-creator__container">
    <!-- START Categories -->
    <fieldset class="event-creator__fieldset">
      <legend class="event-creator__label" for="event_categories[]"><?php _e ( 'Select tag(s) for your event*', 'events-manager'); ?></legend>
        <?php
          $selected = $EM_Event->get_categories()->get_ids();
          foreach($categories as $category) {
          ?>
          <input class="event-creator__checkbox" id="<?php echo $category->id ?>"type="checkbox" name="event_categories[]" value="<?php $category->name?>">
          <label class="event-creator__tag" for="<?php echo $category->id?>"><?php echo $category->name ?></label>
          <?php
          }
        ?>
      <!-- END Categories -->
    </fieldset>
  </div>
<?php endif; ?>