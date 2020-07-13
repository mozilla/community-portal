<?php
/**
 * Search
 *
 * Search page
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

get_header();
$results = array();
$theme_directory     = get_template_directory();
require "{$theme_directory}/countries.php";

$date_format        = 'en' === $current_translation ? 'F d, Y' : 'd F Y';


$p = intval( get_query_var( 'page' ) ) <= 1 ? 1 : intval( get_query_var( 'page' ) );

$results_per_page = 12;

if ( isset( $_GET['s'] ) ) {
	$search_term = sanitize_text_field( wp_unslash( $_GET['s'] ) );
} else {
	$search_term = false;
}

// Lets get some search results.
if ( false !== $search_term ) {

	$args = array(
		'posts_per_page' => -1,
		'offset'         => 0,
		'post_type'      => array( 'campaign', 'activity' ),
	);
	if (
		strpos( $search_term, '"' ) !== false ||
		strpos( $search_term, "'" ) !== false ||
		strpos( $search_term, '\\' ) !== false
	) {
		$search_term    = preg_replace( '/^\"|\"$|^\'|\'$/', '', $search_term );
		$original_query = $search_term;
		$search_term    = addslashes( $search_term );
	} else {
		$original_query = $search_term;
	}

	$args['s'] = trim( $search_term );

	$query   = new WP_Query( $args );
	$results = $query->posts;
}

// Search Events.
$events_args['scope']  = 'all';
$events_args['search'] = trim( $search_term );
$events                = EM_Events::get( $events_args );
$all_countries         = array();

if ( count( $events ) > 0 ) {
	$all_countries = em_get_countries();
	$results       = array_merge( $results, $events );
}

// Search Groups.
$group_args = array(
	'search_terms' => $search_term,
	'per_page'     => -1,
);

$groups = groups_get_groups( $group_args );

if ( isset( $groups['groups'] ) && is_array( $groups['groups'] ) && count( $groups['groups'] ) > 0 ) {
	$results = array_merge( $results, $groups['groups'] );
}

// Search Users.
$wp_user_query = new WP_User_Query(
	array(
		'offset' => 0,
		'number' => -1,
	)
);

$logged_in = mozilla_is_logged_in();
$this_user = wp_get_current_user()->data;
$current_translation = mozilla_get_current_translation();

$members          = $wp_user_query->get_results();
$filtered_members = array();
$search_user      = trim( $search_term );

foreach ( $members as $index  => $member ) {
	$info         = mozilla_get_user_info( $this_user, $member, $logged_in );
	$member->info = $info;

	// Username.
	if ( stripos( $member->data->user_nicename, $search_user ) !== false ) {
		$filtered_members[] = $member;
		continue;
	}

	// First name.
	if ( $info['first_name']->display && stripos( $search_user, $info['first_name']->value ) !== false ) {
		$filtered_members[] = $member;
		continue;
	}

	// Last name.
	if ( $info['last_name']->display && stripos( $search_user, $info['last_name']->value ) !== false ) {
		$filtered_members[] = $member;
		continue;
	}
}

if ( count( $filtered_members ) > 0 ) {
	$results = array_merge( $results, $filtered_members );
}

$count  = count( $results );
$offset = ( $p - 1 ) * $results_per_page;

$results     = array_slice( $results, $offset, $results_per_page );
$total_pages = ceil( $count / $results_per_page );
?>

	<div class="content">
		<div class="search">
			<div class="search__container">
				<h1 class="search__title">
				<?php
				if ( strlen( $original_query ) > 0 ) :
					?>
					<?php esc_html_e( 'Results for ', 'community-portal' ) . $original_query; ?>
					<?php
else :
	?>
					<?php esc_html_e( 'Search', 'community-portal' ); ?><?php endif; ?></h1>
				<div class="search__search-form-container">
					<form method="GET" action="<?php echo get_home_url()?>" class="groups__form" id="group-search-form">
						<div class="search__input-container">
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M17.5 17.5L13.875 13.875" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php
							if ( isset( $original_query ) && strlen( $original_query ) > 0 ) {
								$display_query = $original_query;
							} else {
								$display_query = '';
							}
							?>
							<input type="text" name="s" id="search" class="groups__search-input" placeholder="<?php esc_attr_e( 'Search', 'community-portal' ); ?>" value="<?php echo esc_attr( $display_query ); ?>" />
						</div>
						<input type="button" class="groups__search-cta" value="<?php esc_attr_e( 'Search', 'community-portal' ); ?>" />
					</form>
				</div>
				<div class="search__results">
				<?php foreach ( $results as $result ) : ?>
					<?php
					if ( isset( $result->post_content ) && strlen( $result->post_content ) > 140 ) {
						$description = substr( $result->post_content, 0, 140 ) . '...';
					} else {
						$description = $result->post_content;
					}
					?>

					<div class="search__result">
						<?php if ( isset( $result->post_type ) && 'campaign' === $result->post_type ) : ?>
						<h3 class="search__result-title search__result-title--campaign"><?php esc_html_e( 'Campaign', 'community-portal' ); ?></h3>
						<a href="<?php echo esc_attr( get_home_url(null, 'campaigns/' . $result->post_name)); ?>" class="search__result-link"><?php echo esc_html( $result->post_title ); ?></a>
						<div class="search__result-dates">
							<?php
							$start_date = get_field( 'campaign_start_date', $result->ID );
							$end_date   = get_field( 'campaign_end_date', $result->ID );
							?>
							<?php echo esc_html( date_i18n( $date_format, strtotime( $start_date ) ) ); ?> - <?php echo esc_html( date_i18n( $date_format, strtotime( $end_date ) ) ); ?>
						</div>
						<div class="search__result-description">
							<?php echo wp_kses( $description, array( 'p' => array() ) ); ?>
						</div>
						<?php endif; ?>                
						<?php if ( isset( $result->post_type ) && 'page' === $result->post_type ) : ?>
						<h3 class="search__result-title search__result-title--campaign"><?php esc_html_e( 'Page', 'community-portal' ); ?></h3>
						<div class="search__result-dates">
							<?php
							$start_date = get_field( 'campaign_start_date', $result->ID );
							$end_date   = get_field( 'campaign_end_date', $result->ID );
							?>
							<?php echo esc_html( date_i18n( $date_format, strtotime( $start_date ) ) ); ?> - <?php echo esc_html( date_i18n( $date_format, strtotime( $end_date ) ) ); ?>
						</div>
						<div class="search__result-description">
							<?php echo wp_kses( $description, array( 'p' => array() ) ); ?>
						</div>
						<?php endif; ?>      
						<?php if ( isset( $result->post_type ) && 'event' === $result->post_type ) : ?>
							<?php
							$location = em_get_location( $result->location_id );
							?>
						<h3 class="search__result-title search__result-title--event"><?php esc_html_e( 'Event', 'community-portal' ); ?></h3>
						<a href="<?php echo esc_attr( get_home_url( null, 'events/' . $result->event_slug ) ); ?>" class="search__result-link"><?php echo esc_html( $result->event_name ); ?></a>
						<div class="search__event-date">
							<?php echo esc_html( date_i18n( $date_format, strtotime( $result->event_start_date ) ) ); ?>
							<?php if ( isset( $result->event_start_time ) ) : ?>
							@ <?php echo esc_html( date_i18n( 'H:i', strtotime( $result->event_start_time ) ) ); ?> 
						<?php endif; ?>
							<?php if ( isset( $results->event_end_time ) && $result->event_start_time !== $results->event_end_time ) : ?>
							- <?php echo esc_html( date_i18n( 'H:i', strtotime( $result->event_end_time ) ) ); ?> 
						<?php endif; ?>
						</div>
						<div class="search__event-location">
							<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M14 7.66699C14 12.3337 8 16.3337 8 16.3337C8 16.3337 2 12.3337 2 7.66699C2 6.07569 2.63214 4.54957 3.75736 3.42435C4.88258 2.29913 6.4087 1.66699 8 1.66699C9.5913 1.66699 11.1174 2.29913 12.2426 3.42435C13.3679 4.54957 14 6.07569 14 7.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M8 9.66699C9.10457 9.66699 10 8.77156 10 7.66699C10 6.56242 9.10457 5.66699 8 5.66699C6.89543 5.66699 6 6.56242 6 7.66699C6 8.77156 6.89543 9.66699 8 9.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php if ( 'OE' === $location->country ) : ?>
								<?php esc_html_e( 'Online', 'community-portal' ); ?>
							<?php else : ?>
								<?php if ( $location->location_address ) : ?>
									<?php echo esc_html( $location->location_address ); ?> - 
								<?php endif; ?>
								<?php if ( $location->town ) : ?>
									<?php
										$city = strlen( $location->town ) > 180 ? substr( $location->town, 0, 180 ) : $location->town;
										echo esc_html( $city );
									?>
									<?php if ( $location->country ) : ?>
										<?php if ( $city ) : ?>
											,&nbsp;
										<?php endif; ?>
										<?php echo esc_html( $all_countries[ $location->country ] ); ?>
									<?php endif; ?>
								<?php else : ?>

								<?php endif; ?>
							<?php endif; ?>
						</div>
						<?php endif; ?>              
						<?php if ( 'BP_Groups_Group' === get_class( $result ) ) : ?>
						<h3 class="search__result-title search__result-title--group"><?php print esc_html_e( 'Group', 'community-portal' ); ?></h3>
						<a href="<?php echo esc_attr( get_home_url( null, 'groups/' . $result->slug ) ); ?>" class="search__result-link"><?php echo esc_html( $result->name ); ?></a>
						<div class="search__result-description">

							<?php
							$group_meta   = groups_get_groupmeta( $result->id, 'meta' );
							$member_count = groups_get_total_member_count( $result->id );
							?>
						</div>
							<?php if ( isset( $group_meta['group_city'] ) && strlen( trim( $group_meta['group_city'] ) ) > 0 || isset( $group_meta['group_country'] ) && '0' !== $group_meta['group_country'] ) : ?>
						<div class="search__group-location">
							<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M14 7.66699C14 12.3337 8 16.3337 8 16.3337C8 16.3337 2 12.3337 2 7.66699C2 6.07569 2.63214 4.54957 3.75736 3.42435C4.88258 2.29913 6.4087 1.66699 8 1.66699C9.5913 1.66699 11.1174 2.29913 12.2426 3.42435C13.3679 4.54957 14 6.07569 14 7.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M8 9.66699C9.10457 9.66699 10 8.77156 10 7.66699C10 6.56242 9.10457 5.66699 8 5.66699C6.89543 5.66699 6 6.56242 6 7.66699C6 8.77156 6.89543 9.66699 8 9.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
								<?php
								if ( strlen( $group_meta['group_city'] ) > 180 ) {
									$group_meta['group_city'] = substr( $group_meta['group_city'], 0, 180 );
								}
								?>
								<?php echo esc_html( trim( $group_meta['group_city'] ) ); ?>
								<?php
								if ( isset( $group_meta['group_country'] ) && strlen( $group_meta['group_country'] ) > 0 ) {
									if ( isset( $group_meta['group_city'] ) && strlen( $group_meta['group_city'] ) > 0 ) {
										echo esc_html( trim( ", {$countries[$group_meta['group_country']]}" ) );
									} else {
										echo esc_html( $countries[ $group_meta['group_country'] ] );
									}
								}
								?>
						</div>
						<?php endif; ?>
						<div class="search__group-members">
							<svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M12.3334 14V12.6667C12.3334 11.9594 12.0525 11.2811 11.5524 10.781C11.0523 10.281 10.374 10 9.66675 10H4.33341C3.62617 10 2.94789 10.281 2.4478 10.781C1.9477 11.2811 1.66675 11.9594 1.66675 12.6667V14" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M6.99992 7.33333C8.47268 7.33333 9.66659 6.13943 9.66659 4.66667C9.66659 3.19391 8.47268 2 6.99992 2C5.52716 2 4.33325 3.19391 4.33325 4.66667C4.33325 6.13943 5.52716 7.33333 6.99992 7.33333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M16.3333 14.0002V12.6669C16.3328 12.0761 16.1362 11.5021 15.7742 11.0351C15.4122 10.5682 14.9053 10.2346 14.3333 10.0869" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M11.6667 2.08691C12.2404 2.23378 12.7488 2.56738 13.1118 3.03512C13.4749 3.50286 13.672 4.07813 13.672 4.67025C13.672 5.26236 13.4749 5.83763 13.1118 6.30537C12.7488 6.77311 12.2404 7.10671 11.6667 7.25358" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php echo esc_html( "{$member_count}&nbsp;" . __( 'Members', 'community-portal' ) ); ?>
						</div>
						<?php endif; ?>                
						<?php if ( isset( $result->post_type ) && 'activity' === $result->post_type ) : ?>
						<h3 class="search__result-title search__result-title--activity"><?php echo esc_html_e( 'Activity', 'community-portal' ); ?></h3>
						<a href="<?php echo esc_attr( get_home_url( null, 'activities/' . $result->post_name ) ); ?>" class="search__result-link"><?php echo esc_html( $result->post_title ); ?></a>
						<div class="search__result-description">
							<?php echo wp_kses( $description, array( 'p' => array() ) ); ?>
						</div>
						<?php endif; ?>                   

						<?php if ( 'WP_User' === get_class( $result ) ) : ?>
						<h3 class="search__result-title search__result-title--member"><?php echo esc_html_e( 'Member', 'community-portal' ); ?></h3>
						<a href="<?php echo esc_attr( get_home_url( null, 'people/' . $result->user_nicename ) ); ?>" class="search__result-link"><?php echo esc_html( $result->user_nicename ); ?></a>
						<div class="search__member-name">
							<?php if ( $result->info['first_name']->display ) : ?>
								<?php echo esc_html( $result->info['first_name']->value ); ?>
						<?php endif; ?>
							<?php if ( $result->info['last_name']->display ) : ?>
								<?php echo esc_html( $result->info['last_name']->value ); ?>
						<?php endif; ?>
						</div>
						<?php endif; ?>             
					</div>   
				<?php endforeach; ?>
				</div>
			</div>
			<?php
				$range = ( $p > 3 ) ? 3 : 5;

			if ( $p > $total_pages - 2 ) {
				$range = 5;
			}

				$previous_page = ( $p > 1 ) ? $p - 1 : 1;
				$next_page     = ( $p < $total_pages ) ? $p + 1 : $total_pages;

			if ( $total_pages > 1 ) {
				$range_min = ( 0 === $range % 2 ) ? ( $range / 2 ) - 1 : ( $range - 1 ) / 2;
				$range_max = ( 0 === $range % 2 ) ? $range_min + 1 : $range_min;

				$page_min = $page - $range_min;
				$page_max = $page + $range_max;

				$page_min = ( $page_min < 1 ) ? 1 : $page_min;
				$page_max = ( $page_max < ( $page_min + $range - 1 ) ) ? $page_min + $range - 1 : $page_max;

				if ( $page_max > $total_pages ) {
					$page_min = ( $page_min > 1 ) ? $total_pages - $range + 1 : 1;
					$page_max = $total_pages;
				}
			}
			?>

			<div class="campaigns__pagination">
				<div class="campaigns__pagination-container">
					<?php if ( $total_pages > 1 ) : ?>
					<a 
						href="<?php 
						if ($search_term) {
							echo esc_attr(add_query_arg( array('s' => $search_term, 'page' => $previous_page ), get_home_url()));
						} else {
							echo esc_attr(add_query_arg( array('page' => $previous_page ), get_home_url()));
						}	
						?>" 
						class="campaigns__pagination-link">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<path d="M17 23L6 12L17 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
						<?php
						if ( $page_min > 1 ) :
							?>
							<a 
								href="<?php 
									if ($search_term) {
										echo esc_attr(add_query_arg( array('s' => $search_term, 'page' => $previous_page ), get_home_url()));
									} else {
										echo esc_attr(add_query_arg( array('page' => $previous_page ), get_home_url()));
									}	
								?>" 
								class="campaigns__pagination-link campaigns__pagination-link--first"><?php echo '1'; ?></a>&hellip; <?php endif; ?>
							<?php for ( $x = $page_min - 1; $x < $page_max; $x++ ) : ?>
							<a
								href="<?php 
									if ($search_term) {
										echo esc_attr(add_query_arg( array('s' => $search_term, 'page' => $x + 1 ), get_home_url()));
									} else {
										echo esc_attr(add_query_arg( array('page' => $x + 1 ), get_home_url()));
									}	
								?>"  
								class="campaigns__pagination-link
							<?php
							if ( $p === $x + 1 ) :
								?>
						campaigns__pagination-link--active<?php endif; ?>
							<?php
							if ( $x === $page_max - 1 ) :
								?>
	campaigns__pagination-link--last<?php endif; ?>"><?php echo esc_html( $x + 1 ); ?></a>
					<?php endfor; ?>
						<?php
						if ( $total_pages > $range && $p < $total_pages - 1 ) :
							?>
							&hellip; <a href="<?php echo esc_url( add_query_arg( array( 'p' => $total_pages ), get_home_url( null, 'campaigns') )) ?>" class="campaigns__pagination-link
							<?php
							if ( $p === $total_pages ) :
								?>
							campaigns__pagination-link--active<?php endif; ?>"><?php echo esc_html( $total_pages ); ?></a><?php endif; ?>
					<a 
						href="<?php 
							if ($search_term) {
								echo esc_attr(add_query_arg( array('s' => $search_term, 'page' => $next_page ), get_home_url()));
							} else {
								echo esc_attr(add_query_arg( array('page' => $next_page ), get_home_url()));
							}	
							?>"  
							class="campaigns__pagination-link">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<path d="M7 23L18 12L7 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
<?php get_footer(); ?>
