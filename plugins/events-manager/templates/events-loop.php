<?php
/**
 * Events Loop
 *
 * Loop for event page for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php

	$args['scope'] = 'future';
	$events        = EM_Events::get( $args );

	foreach ( $events as $event ) {
		include locate_template( 'plugins/events-manager/templates/template-parts/event-cards.php', false, false );
	}


