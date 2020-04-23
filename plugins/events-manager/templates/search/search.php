<?php
/**
 * Events Search
 *
 * This general search will find matches within event_name, event_notes, and the location_name, address, town, state and country.
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

	$args = ! empty( $args ) ? $args : array();
?>
<div class="em-search-text em-search-field">
	<label>
		<span class="screen-reader-text"><?php echo esc_html( $args['search_term_label'] ); ?></span>
		<input type="text" name="em_search" class="em-events-search-text em-search-text" value="<?php echo esc_attr( $args['search'] ); ?>" placeholder="<?php print esc_html__( 'Search events', 'community-portal' ); ?>" />
	</label>
</div>
