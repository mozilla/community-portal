<?php
  global $EM_Event;
  $categories = EM_Categories::get(array('orderby'=>'name','hide_empty'=>0));
?>
<?php if( count($categories) > 0 ): ?>
  <div class="event-categories">
    <!-- START Categories -->
    <label for="event_categories[]"><?php _e ( 'Select tag(s) for your event*', 'events-manager'); ?></label>
    <select name="event_categories[]" multiple size="10">
      <?php
        $selected = $EM_Event->get_categories()->get_ids();
        $walker = new EM_Walker_CategoryMultiselect();
        $args_em = array( 'hide_empty' => 0, 'name' => 'event_categories[]', 'hierarchical' => true, 'id' => EM_TAXONOMY_CATEGORY, 'taxonomy' => EM_TAXONOMY_CATEGORY, 'selected' => $selected, 'walker'=> $walker); echo walk_category_dropdown_tree($categories, 0, $args_em);
      ?></select>
    <!-- END Categories -->
  </div>
<?php endif; ?>