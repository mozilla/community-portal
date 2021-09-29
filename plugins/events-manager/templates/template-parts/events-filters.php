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
	$theme_directory = get_template_directory();
	require "{$theme_directory}/languages.php";
	$countries      = em_get_countries();
	$ddm_countries  = array();
	$used_languages = array();
	$filter_events  = EM_Events::get( array( 'scope' => 'all' ) );
	$online_event   = __( 'Online Event', 'community-portal' );

foreach ( $filter_events as $e ) {
	$location     = em_get_location( $e->location_id );
	$country_code = $location->location_country;

	if ( strlen( $country_code ) > 0 && ! in_array( $country_code, $ddm_countries, true ) ) {
		if ( 'OE' === $country_code ) {
			$ddm_countries[ $country_code ] = $online_event;
		} else {
			$ddm_countries[ $country_code ] = $countries[ $country_code ];
		}
	}
	$e_meta = get_post_meta( $e->post_id, 'event-meta' );

	if ( isset( $e_meta[0]->language ) && isset( $languages[ $e_meta[0]->language ] ) ) {
		$used_languages[ $e_meta[0]->language ] = $languages[ $e_meta[0]->language ];
	}
}
	asort( $ddm_countries );
	asort( $used_languages );
	$used_languages = array_unique( $used_languages );

	$categories = EM_Categories::get();

if ( count( $categories ) > 0 ) {
	$current_translation = mozilla_get_current_translation();

	foreach ( $categories as $category ) {
		$term_object                          = mozilla_get_translated_tag( $category );
		$categories[ $category->id ]          = array();
		$categories[ $category->id ]['value'] = $term_object->id;
		$categories[ $category->id ]['label'] = $term_object->name;
	}
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
				'post_type'      => 'campaign',
				'posts_per_page' => -1,
			);

			$campaigns   = new WP_Query( $args );
			$initiatives = array();

			foreach ( $campaigns->posts as $campaign ) {
				$start = strtotime( get_field( 'campaign_start_date', $campaign->ID ) );
				$end   = strtotime( get_field( 'campaign_end_date', $campaign->ID ) );
				$today = time();

				$campaign_status = get_field( 'campaign_status', $campaign->ID );

				if ( strtolower( $campaign_status ) !== 'closed' ) {
					$campaign_id                 = apply_filters( 'wpml_object_id', $campaign->ID, 'campaign', true, 'en' );
					$initiatives[ $campaign_id ] = $campaign->post_title;
					continue;
				}

				if ( $today >= $start && $today <= $end ) {
					$campaign_id                 = apply_filters( 'wpml_object_id', $campaign->ID, 'campaign', true, 'en' );
					$initiatives[ $campaign_id ] = $campaign->post_title;
				}
			}

			$args = array(
				'post_type'      => 'activity',
				'posts_per_page' => -1,
			);

			$activities = new WP_Query( $args );
			foreach ( $activities->posts as $activity ) {
				$activity_id                 = apply_filters( 'wpml_object_id', $activity->ID, 'activity', true, 'en' );
				$initiatives[ $activity_id ] = $activity->post_title;
			}

			$options = $initiatives;
			require locate_template( 'plugins/events-manager/templates/template-parts/options.php', false, false );

			?>
	</form>
</div>
<div class="col-md-12">
	<button class="events__filter__toggle btn btn--large btn--light events__filter__toggle--hide">
		<span class="hide"><?php esc_html_e( 'Hide Filters', 'community-portal' ); ?></span>
		<span class="show"><?php esc_html_e( 'Show Filters', 'community-portal' ); ?></span>
	</button>
</div>
