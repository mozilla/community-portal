<?php
/**
 * Events Filters
 *
 * Filters for events page for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php
	global $wpdb;
	$nonce = sanitize_key( $_GET['nonce']);
	$theme_directory = get_template_directory();
	require "{$theme_directory}/languages.php";

	$countries      = em_get_countries();
	$em_countries   = $wpdb->get_results( 'SELECT DISTINCT location_country FROM ' . EM_LOCATIONS_TABLE . " WHERE location_country IS NOT NULL AND location_country != '' AND location_status=1 ORDER BY location_country ASC", ARRAY_N );
	$ddm_countries  = array();
	$used_languages = array();

foreach ( $em_countries as $em_country ) {
	$ddm_countries[ $em_country[0] ] = $countries[ $em_country[0] ];
}

	asort( $ddm_countries );

foreach ( $all_events as $e ) {
	$e_meta = get_post_meta( $e->post_id, 'event-meta' );

	if ( isset( $e_meta[0]->language ) && isset( $languages[ $e_meta[0]->language ] ) ) {
		$used_languages[ $e_meta[0]->language ] = $languages[ $e_meta[0]->language ];
	}
}

	asort( $used_languages );
	$used_languages = array_unique( $used_languages );


	$categories = EM_Categories::get();
if ( count( $categories ) > 0 ) {
	foreach ( $categories as $category ) {
		$categories[ $category->id ] = $category->name;
	}
} else {
	$categories = array(
		'Localization (L10N)',
		'User Support (SUMO)',
		'Testing',
		'Common Voice',
		'Coding',
		'Design',
		'Advocacy',
		'Documentation',
		'Evangelism',
		'Marketing',
	);
}
?>
<div class="col-md-12 events__filter">
	<p class="events__filter__title"><?php esc_html_e( 'Filter By:', 'community-portal' ); ?></p>
	<form action="" class="events__filter__form">
		<?php
			$field_name  = 'Country';
			$field_label = __( 'Location', 'community-portal' );
			$options     = $ddm_countries;
			require locate_template( 'plugins/events-manager/templates/template-parts/options.php', false, false );

		if ( count( $used_languages ) > 0 ) {
			$field_name  = 'Language';
			$field_label = __( 'Language', 'community-portal' );
			$options     = $used_languages;
			include locate_template( 'plugins/events-manager/templates/template-parts/options.php', false, false );
		}

			$field_name  = 'Tag';
			$field_label = __( 'Tag', 'community-portal' );
			$options     = $categories;
			require locate_template( 'plugins/events-manager/templates/template-parts/options.php', false, false );


			$field_name  = 'Initiative';
			$field_label = __( 'Campaign or Activity', 'community-portal' );
			$args        = array(
				'post_type' => 'campaign',
				'per_page'  => -1,
			);

			$campaigns   = new WP_Query( $args );
			$initiatives = array();

			foreach ( $campaigns->posts as $campaign ) {
				$start = strtotime( get_field( 'campaign_start_date', $campaign->ID ) );
				$end   = strtotime( get_field( 'campaign_end_date', $campaign->ID ) );
				$today = time();

				$campaign_status = get_field( 'campaign_status', $campaign->ID );

				if ( strtolower( $campaign_status ) !== 'closed' ) {
					$initiatives[ $campaign->ID ] = $campaign->post_title;
					continue;
				}

				if ( $today >= $start && $today <= $end ) {
					$initiatives[ $campaign->ID ] = $campaign->post_title;
				}
			}

			$args = array(
				'post_type' => 'activity',
				'per_page'  => -1,
			);

			$activities = new WP_Query( $args );
			foreach ( $activities->posts as $activity ) {
				$initiatives[ $activity->ID ] = $activity->post_title;
			}

			$options = $initiatives;
			require locate_template( 'plugins/events-manager/templates/template-parts/options.php', false, false );

			?>
			<?php 
				wp_nonce_field('events-filter', 'events-filter-nonce');
			?>
	</form>
</div>
<div class="col-md-12">
	<button class="events__filter__toggle btn btn--large btn--light">
		<?php esc_html_e( 'Hide Filters', 'community-portal' ); ?>
	</button>
</div>
