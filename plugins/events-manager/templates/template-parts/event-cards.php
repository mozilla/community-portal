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
			return $n->name;
		},
		$categories
	);
}
if ( 'all' !== $tag && ! $categories && '' !== $tag ) {
	return;
} elseif ( 'all' !== $tag && 'all' !== $country && '' !== $tag && '' !== $country ) {
	if ( ! in_array( $tag, $all_tags, true ) || $country !== $all_countries[ $location->country ] ) {
		return;
	} else {
		include locate_template( 'plugins/events-manager/templates/template-parts/single-event-card.php', false, false );
	}
} elseif ( 'all' !== $tag && '' !== $tag ) {
	if ( ! in_array( $tag, $all_tags, true ) ) {
		include locate_template( 'plugins/events-manager/templates/template-parts/single-event-card.php', false, false );
	} else {
		include locate_template( 'plugins/events-manager/templates/template-parts/single-event-card.php', false, false );
	}
} elseif ( 'all' !== $country && '' !== $country ) {
	if ( $country !== $all_countries[ $location->country ] ) {
		return;
	} else {
		include locate_template( 'plugins/events-manager/templates/template-parts/single-event-card.php', false, false );
	}
} else {
	include locate_template( 'plugins/events-manager/templates/template-parts/single-event-card.php', false, false );
}

