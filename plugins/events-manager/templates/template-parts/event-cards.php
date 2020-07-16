<?php
/**
 * Event Cards
 *
 * Cards for events page for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php
	$location      = em_get_location( $event->location_id );
	$categories    = get_the_terms( $event->post_id, EM_TAXONOMY_CATEGORY );
	$all_countries = em_get_countries();

if ( isset( $categories ) && is_array( $categories ) ) {
	$all_tags = array_map(
		function( $n ) {
			return strtolower( $n->name );
		},
		$categories
	);
}

include locate_template( 'plugins/events-manager/templates/template-parts/single-event-card.php', false, false );
