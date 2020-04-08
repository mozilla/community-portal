<?php
	/* This general search will find matches within event_name, event_notes, and the location_name, address, town, state and country. */ 
	$args = !empty($args) ? $args:array(); /* @var $args array */ 
?>
<div class="em-search-text em-search-field">
	<label>
		<span class="screen-reader-text"><?php echo esc_html($args['search_term_label']); ?></span>
		<input type="text" name="em_search" class="em-events-search-text em-search-text" value="<?php echo esc_attr($args['search']); ?>" placeholder="<?php print __('Search events', 'community-portal'); ?>" />
	</label>
</div>
