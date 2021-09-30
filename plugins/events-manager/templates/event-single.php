<?php
/**
 * Single Event
 *
 * Main page for single events for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php
	$theme_directory = get_template_directory();
	require "{$theme_directory}/countries.php";
	$em_event = $GLOBALS['EM_Event'];
if ( isset( $GLOBALS['EM_Tags'] ) ) {
	$em_tags = $GLOBALS['EM_Tags'];
}
	$logged_in   = mozilla_is_logged_in();
	$active_user = wp_get_current_user()->data;

	$current_translation = mozilla_get_current_translation();

	global $bp;
	$options = wp_load_alloptions();

	$theme_directory = get_template_directory();
	require "{$theme_directory}/languages.php";

	$map_box_access_token = ( isset( $options['mapbox'] ) && strlen( $options['mapbox'] ) > 0 ) ? trim( $options['mapbox'] ) : false;

	$categories = get_the_terms( $em_event->post_id, EM_TAXONOMY_CATEGORY );
	$event_meta = get_post_meta( $em_event->post_id, 'event-meta' );

	$all_countries = em_get_countries();
	$img_url       = isset( $event_meta[0] ) && isset( $event_meta[0]->image_url ) && strlen( $event_meta[0]->image_url ) > 0 ? $event_meta[0]->image_url : false;

if ( ( ! empty( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['SERVER_PORT'] ) && 'off' !== $_SERVER['HTTPS'] ) || 443 === $_SERVER['SERVER_PORT'] ) {
	$img_url = preg_replace( '/^http:/i', 'https:', $img_url );
} else {
	$avatar_url = $img_url;
}

	$location_type = isset( $event_meta[0] ) && isset( $event_meta[0]->location_type ) && strlen( $event_meta[0]->location_type ) > 0 ? $event_meta[0]->location_type : null;
	$external_url  = isset( $event_meta[0] ) && isset( $event_meta[0]->external_url ) && strlen( $event_meta[0]->external_url ) > 0 ? $event_meta[0]->external_url : false;

	$initiative          = isset( $event_meta[0]->initiative ) ? $event_meta[0]->initiative : false;
	$goal                = isset( $event_meta[0]->goal ) && strlen( $event_meta[0]->goal ) > 0 ? $event_meta[0]->goal : false;
	$language            = isset( $event_meta[0]->language ) && strlen( $event_meta[0]->language ) > 0 ? $languages[ $event_meta[0]->language ] : false;
	$projected_attendees = isset( $event_meta[0]->projected_attendees ) && intval( $event_meta[0]->projected_attendees ) > 0 ? $event_meta[0]->projected_attendees : false;



if ( $em_event->event_start_date !== $em_event->event_end_date ) {
	$date_format          = 'en' === $current_translation ? 'F d' : 'd M';
	$formatted_start_date = mozilla_localize_date( $em_event->event_start_date, $date_format );
	$date_format          = 'en' === $current_translation ? 'F d, Y' : 'd F, Y';
	$formatted_end_date   = mozilla_localize_date( $em_event->event_end_date, $date_format );
} else {
	$date_format          = 'en' === $current_translation ? 'F d, Y' : 'd F, Y';
	$formatted_start_date = mozilla_localize_date( $em_event->event_start_date, $date_format );
}

if ( strpos( $em_event->event_timezone, 'UTC-' ) !== false || strpos( $em_event->event_timezone, 'UTC+' ) !== false ) {
	$timezone                = str_replace( 'UTC+', '', str_replace( 'UTC-', '', $em_event->event_timezone ) );
	$timezone_offset_seconds = ( (int) $timezone * 60 );
	if ( strpos( $em_event->event_timezone, 'UTC-' ) !== false ) {
		$timezone_offset = '-';
	} else {
		$timezone_offset = '+';
	}
	$timezone_offset .= gmdate( 'H:i', mktime( 0, $timezone_offset_seconds ) );
} else {
	$timezone        = new DateTimeZone( $em_event->event_timezone );
	$timezone_offset = new DateTime( 'now', $timezone );
	$timezone_offset = $timezone_offset->format( 'Z' );
	if ( $timezone_offset[0] !== '-' ) {
		$timezone_offset = '+' . $timezone_offset;
	}

	$hours           = floor( substr( $timezone_offset, 1 ) / 3600 );
	$minutes         = ( substr( $timezone_offset, 1 ) % 60 );
	$timezone_offset = $timezone_offset[0] . sprintf( '%02d:%02d', $hours, $minutes );
}


$all_related_events = array();
if ( is_array( $categories ) && count( $categories ) > 0 ) {
	foreach ( $categories as $category ) {
		$related_events = EM_Events::get( array( 'category' => $category->term_id ) );
		if ( count( $related_events ) > 0 ) {
			foreach ( $related_events as $single_event ) {
				if ( $related_events[0]->event_id === $single_event->event_id ) {
					continue;
				}
				if ( $single_event->event_id === $em_event->event_id ) {
					continue;
				}
				$all_related_events[] = $single_event;
				if ( count( $all_related_events ) >= 2 ) {
					break;
				}
			}
		}

		if ( count( $all_related_events ) >= 2 ) {
			break;
		}
	}
}

if ( isset( $em_event->group_id ) ) {
	$group  = new BP_Groups_Group( $em_event->group_id );
	$admins = groups_get_group_admins( $group->id );

	if ( isset( $admins ) ) {
		$user   = get_userdata( $admins[0]->user_id );
		$avatar = get_avatar_url( $admins[0]->user_id );
		$users  = get_current_user_id();
	}
}

	// Set default for var used to count attendees.
	$count = 0;
?>

<div class="content events__container events-single">
	<div class="row">
		<div class="col-sm-12">
			<h1 class="title"><?php echo esc_html( $em_event->event_name ); ?></h1>
		</div>
	</div>
	<div class="row events-single__two-up">
		<div class="col-lg-7 col-md-12">
			<div class="card card--with-img">
				<?php
				if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || 443 === $_SERVER['SERVER_PORT'] ) {
					$img_url = preg_replace( '/^http:/i', 'https:', $img_url );
				} else {
					$img_url = $img_url;
				}
				?>
				<div class="card__image
				<?php
				if ( '' !== $img_url && $img_url ) {
					echo esc_attr( 'card__image--active' );
				} else {
					echo esc_attr( '' );
				}
				?>
					"
					<?php
					if ( $img_url && strlen( $img_url ) > 0 ) :
						?>
					style="background-image: url(<?php echo esc_url_raw( $img_url ); ?>); padding-top: 45.4%; width: 100%;"<?php endif; ?>>
					<?php $current_user_id = get_current_user_id(); ?>
					<?php if ( strval( $current_user_id ) === $em_event->owner || mozilla_is_site_admin() ) : ?>
						<a class="btn card__edit-btn
						<?php
						if ( isset( $img_url ) && strlen( $img_url ) > 0 ) :
							?>
							card__edit-btn--white<?php endif; ?>" href="
															<?php
																echo esc_attr(
																	add_query_arg(
																		array(
																			'action'   => 'edit',
																			'event_id' => $em_event->event_id,
																			'nonce'    => wp_create_nonce( 'edit-event' ),
																		),
																		get_home_url( '', 'events/edit-event/' )
																	)
																);
															?>
															">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04   23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z"  fill="#0060DF"/>
							</svg>
						</a>
					<?php elseif ( isset( $admins ) ) : ?>
						<?php foreach ( $admins as $admin ) : ?>
							<?php if ( $admin->user_id === $current_user_id || intval( get_current_user_id() ) === intval( $em_event->event_owner ) || current_user_can( 'edit_post' ) ) : ?>
								<a class="btn card__edit-btn
								<?php
								if ( $img_url && isset( $_SERVER['REQUEST_URI'] ) ) :
									?>
									card__edit-btn--white<?php endif; ?>" href="
										<?php
																		echo esc_attr(
																			add_query_arg(
																				array(
																					'action'   => 'edit',
																					'event_id' => $em_event->event_id,
																					'nonce'    => wp_create_nonce( 'edit-event' ),
																				),
																				get_home_url( '', 'events/edit-event/' )
																			)
																		);
										?>
																				">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z"  fill="#0060DF"/>
									</svg>
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<div class="card__details">
					<div class="card__date">
						<h2 class="title--secondary">
							<?php
							if ( isset( $formatted_end_date ) ) {
								echo esc_html( $formatted_start_date ) . esc_html( ' - ' ) . esc_html( $formatted_end_date );
							} else {
								echo esc_html( $formatted_start_date );
							}
							?>
						</h2>
						<p card="card__time">
							<?php
							echo esc_html( substr( $em_event->event_start_time, 0, 5 ) );
							if ( null !== $em_event->event_end_time ) {
								echo esc_html( ' - ' ) . esc_html( substr( $em_event->event_end_time, 0, 5 ) ) . esc_html( ' ' ) . esc_html( $em_event->event_timezone );
							}
							?>
						</p>
						<p card="card__time" class="timezone">
							<?php esc_html_e( 'In your timezone:', 'community-portal' ); ?> <span data-start-time="<?php echo esc_html( $em_event->event_start_time ); ?>" data-end-time="<?php echo esc_html( $em_event->event_end_time ); ?>" data-start-date="<?php echo esc_html( $em_event->event_start_date ); ?>" data-end-date="<?php echo esc_html( $em_event->event_end_date ); ?>" data-timezone-offset="<?php echo esc_html( $timezone_offset ); ?>"></span>
						</p>
					</div>
					<?php
					if ( is_user_logged_in() ) {
						echo wp_kses(
							wpautop( substr( trim( $em_event->output( '#_BOOKINGFORM' ) ), 0, 3000 ) ),
							array(
								'div'    => array(
									'id'    => array(),
									'class' => array(),
								),
								'script' => array(),
								'a'      => array(
									'href'    => array(),
									'class'   => array(),
									'onClick' => array(),
								),
								'form'   => array(
									'name'   => array(),
									'action' => array(),
									'class'  => array(),
									'method' => array(),
								),
								'input'  => array(
									'type'  => array(),
									'name'  => array(),
									'class' => array(),
									'value' => array(),
									'id'    => array(),
								),
							)
						);
					} else {
						?>
						<div>
							<button class="btn btn--dark btn--submit event__no-account"><?php esc_html_e( 'I will attend', 'community-portal' ); ?></button>
						</div>
						<?php
					}
					?>
				</div>
			</div>

			<h2 class="title--secondary"><?php esc_html_e( 'Location', 'community-portal' ); ?></h2>
			<div class="card events-single__location">
				<div class="row">
					<div class="card__address col-md-5 col-sm-12">
					<?php $location = $em_event->location; ?>
					<?php if ( isset( $location_type ) && $location_type !== 'online' && isset( $location->location_country ) && strlen( $location->location_country ) > 0 && 'OE' !== $location->location_country ) : ?>
						<p><?php echo esc_html( $location->location_name ); ?></p>
						<p><?php echo esc_html( $location->location_address ); ?></p>
						<?php if ( 'OE' === $location->location_country ) : ?>
							<p><?php esc_html_e( 'Online Event', 'community-portal' ); ?></p>
						<?php else : ?>
							<p><?php echo esc_html( $location->location_town ) . esc_html( ', ' ) . esc_html( $all_countries[ $em_event->location->location_country ] ); ?></p>
						<?php endif; ?>
						<p><a href="<?php print esc_attr( add_query_arg( array( 'country' => $em_event->location->location_country ), get_home_url( null, 'events' ) ) ); ?>"><?php esc_html_e( 'View more events in ', 'community-portal' ); ?><?php print esc_html( $all_countries[ $em_event->location->location_country ] ); ?></a></p>
					<?php else : ?>
						<p><?php esc_html_e( 'This is an online-only event', 'community-portal' ); ?></p>
						<?php if ( ! empty( $em_event->location->name ) && filter_var( $em_event->location->name, FILTER_VALIDATE_URL ) ) : ?>
						<a href="<?php echo esc_attr( $em_event->location->name ); ?>"><?php echo esc_html_e( 'Meeting link', 'community-portal' ); ?>
							<svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M1.33325 8.66732L4.99992 5.00065L1.33325 1.33398" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
						<?php endif; ?>
					<?php endif; ?>
					</div>
					<?php if ( false !== $map_box_access_token ) : ?>

						<?php

						$full_location = rawurlencode( $location->location_address . ' ' . $location->location_town );
						$request       = wp_remote_get( 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . $full_location . '.json?types=address&access_token=' . $map_box_access_token );
						$mapbox_error  = false;
						if ( is_wp_error( $request ) ) {
							$mapbox_error = true;
						} else {
							$body = wp_remote_retrieve_body( $request );
							$data = json_decode( $body );

							if ( ! empty( $data->features ) ) {
								$coordinates = $data->features[0]->geometry->coordinates;
							}
						}
						?>
						<?php if ( false === $mapbox_error && isset( $location_type ) && strlen( $location_type ) && 'online' !== $location_type && 'OE' !== $location->location_country ) : ?>
						<div id='map' class="card__map col-md-7 col-sm-12" style='height: 110px;'></div>
						<script type="text/javascript">
							const geojson =  {
								type: 'FeatureCollection',
								features: [{
								type: 'Feature',
								geometry: {
									type: 'Point',
									coordinates: [<?php echo esc_html( $coordinates[0] ) . esc_html( ', ' ) . esc_html( $coordinates[1] ); ?>]
								},
								properties: {
									title: 'Mapbox',
									description: 'Washington, D.C.'
								}
								}]
							};
							mapboxgl.accessToken = "<?php echo esc_html( $map_box_access_token ); ?>";
							var map = new mapboxgl.Map({
								container: 'map',
								style: 'mapbox://styles/mapbox/streets-v11',
								center: [<?php echo esc_html( $coordinates[0] ) . ', ' . esc_html( $coordinates[1] ); ?> ],
								zoom: 15,
							});
							geojson.features.forEach(function(marker) {
								// create a HTML element for each feature
								var el = document.createElement('div');
								el.className = 'marker';
								// make a marker for each feature and add to the map
								new mapboxgl.Marker(el)
								.setLngLat(marker.geometry.coordinates)
								.addTo(map);
								});
						</script>
					<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>

			<div class="events-single__description">
				<h2 class="title--secondary"><?php esc_html_e( 'Description', 'community-portal' ); ?></h2>

				<p>
				<?php
					echo wp_kses(
						wpautop( substr( trim( $em_event->post_content ), 0, 3000 ) ),
						array(
							'p'  => array(),
							'br' => array(),
							'a'  => array(
								'href' => array(),
							),
							'ol' => array(),
							'ul' => array(),
							'li' => array(),
						)
					);
					?>
				</p>
			</div>
			<?php if ( $goal ) : ?>
				<div class="events-single__description">
					<h2 class="title--secondary"><?php esc_html_e( 'Goals', 'community-portal' ); ?></h2>
					<p>
						<?php echo wp_kses( wpautop( $goal ), wp_kses_allowed_html( 'post' ) ); ?>
					</p>
				</div>
			<?php endif; ?>
			<?php
				$active_bookings = array();
			if ( isset( $em_event->bookings ) ) {
				foreach ( $em_event->bookings as $booking ) {
					if ( '3' !== $booking->booking_status ) {
						$active_bookings[] = $booking;
					}
				}
			}
			?>
			<?php if ( is_array( $active_bookings ) && count( $active_bookings ) > 0 ) : ?>
			<div class="events-single__title--with-parenthetical">
				<h2 class="title--secondary">
					<?php esc_html_e( 'Attendees', 'community-portal' ); ?>
				</h2>
				<p class="events-single__parenthetical">
				(
					<span>
						<?php echo esc_html__( 'Actual:', 'community-portal' ) . ' ' . esc_html( count( $active_bookings ) ); ?>
					</span>
					<?php if ( $projected_attendees ) : ?>
						<span class="expected-attendees"><?php echo esc_html__( 'Expecting:', 'community-portal' ) . ' ' . esc_html( $projected_attendees ); ?></span>
					<?php endif; ?>
				)
				</p>
			</div>
			<div class="row">
				<?php foreach ( $active_bookings as $booking ) : ?>
					<?php
					if ( $count < 8 ) {
						$active_bookings[] = $booking;
						$user              = $booking->person->data;

						$is_me = $logged_in && intval( $active_user->ID ) === intval( $user->ID );
						$info  = mozilla_get_user_info( $active_user, $user, $logged_in );

						if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || 443 === $_SERVER['SERVER_PORT'] ) {
							$avatar_url = preg_replace( '/^http:/i', 'https:', $info['profile_image']->value );
						} else {
							$avatar_url = $info['profile_image']->value;
						}
						?>
					<div class="col-md-6 events-single__member-card">
						<a href="<?php echo esc_attr( get_home_url( null, 'people/' . $user->user_nicename ) ); ?>">
							<div class="events-single__avatar
							<?php
							if ( false === $info['profile_image']->display || false === $info['profile_image']->value ) :
								?>
								members__avatar--identicon<?php endif; ?>"
								<?php
								if ( $info['profile_image']->display && $info['profile_image']->value ) :
									?>
								style="background-image: url('<?php print esc_url_raw( $avatar_url ); ?>')"<?php endif; ?> data-username="<?php print esc_attr( $user->user_nicename ); ?>">
							</div>
							<div class="events-single__user-details">
								<p class="events-single__username"><?php echo esc_html( $user->user_nicename ); ?></p>
							<?php if ( $info['first_name']->display && $info['first_name']->value || $info['last_name']->display && $info['last_name']->value ) : ?>
								<div class="events-single__name">
									<?php
									if ( $info['first_name']->display && $info['first_name']->value ) :
										print esc_html( $info['first_name']->value );
										endif;

									if ( $info['last_name']->display && $info['last_name']->value ) :
										print esc_html( " {$info['last_name']->value}" );
										endif;
									?>
								</div>
								<?php endif; ?>

								<?php if ( $info['location']->display && $info['location']->value && isset( $countries[ $info['location']->value ] ) ) : ?>
									<p class="events-single__country">
										<?php echo esc_html( $countries[ $info['location']->value ] ); ?>
									</p>
								<?php endif; ?>
							</div>
							<?php ++$count; ?>
						</a>
					</div>
						<?php
					} elseif ( $count >= 8 ) {
						?>
						<?php if ( 8 === $count ) : ?>
							<button id="open-attendees-lightbox" class="btn btn--submit btn--light">
								<?php esc_html_e( 'View all attendees', 'community-portal' ); ?>
							</button>
						<?php endif; ?>
						<?php
							++$count;
					}
					?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php require locate_template( 'plugins/events-manager/templates/template-parts/event-single/event-sidebar.php', false, false ); ?>

	<?php if ( count( $all_related_events ) > 0 ) : ?>
		<div class="events-single__related col-sm-12">
			<h2 class="title--secondary"><?php esc_html_e( 'Related Events', 'community-portal' ); ?></h2>
			<div class="row">
				<?php
				foreach ( $all_related_events as $event ) {
					$url = get_home_url( null, '/events/' . $event->slug );
					include locate_template( 'plugins/events-manager/templates/template-parts/single-event-card.php', false, false );
				}
				?>
			</div>
		</div>
	<?php endif; ?>
	<?php if ( isset( $em_event->bookings ) && ! empty( $em_event->bookings ) ) : ?>
	<div id="attendees-lightbox" class="lightbox">
		<div class="lightbox__container">
			<button id="close-attendees-lightbox" class="btn btn--close">
				<svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M25 1L1 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M1 1L25 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>

			<div class="row events-single__all-attendees">
				<p class="title--secondary col-sm-12"><?php echo esc_html( $count ) . esc_html__( ' Attendees', 'community-portal' ); ?></p>
				<?php foreach ( $em_event->bookings as $booking ) : ?>
					<?php if ( ! empty( $booking ) && isset( $booking->booking_status ) && '3' !== $booking->booking_status ) : ?>
						<?php
								$user  = $booking->person->data;
								$is_me = $logged_in && intval( $active_user->ID ) === intval( $user->ID );
								$info  = mozilla_get_user_info( $active_user, $user, $logged_in );

						if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || 443 === $_SERVER['SERVER_PORT'] ) {
							$avatar_url = preg_replace( '/^http:/i', 'https:', $info['profile_image']->value );
						} else {
							$avatar_url = $info['profile_image']->value;
						}
						?>
						<div class="col-md-6 events-single__member-card">
							<a href="<?php echo esc_attr( get_home_url( null, '/people/' . $user->user_nicename ) ); ?>">
								<div class="events-single__avatar
								<?php
								if ( false === $info['profile_image']->display || false === $info['profile_image']->value ) :
									?>
									members__avatar--identicon<?php endif; ?>"
									<?php
									if ( $info['profile_image']->display && $info['profile_image']->value ) :
										?>
									style="background-image: url('<?php print esc_url_raw( $avatar_url ); ?>')"<?php endif; ?> data-username="<?php print esc_attr( $user->user_nicename ); ?>">
								</div>
								<div class="events-single__user-details">
									<p class="events-single__username">
										<?php echo esc_attr( $user->user_nicename ); ?>
									</p>
									<?php if ( $info['first_name']->display && $info['first_name']->value || $info['last_name']->display && $info['last_name']->value ) : ?>
										<div class="events-single__name">
											<?php
											if ( $info['first_name']->display && $info['first_name']->value ) :
												print esc_html( $info['first_name']->value );
												endif;

											if ( $info['last_name']->display && $info['last_name']->value ) :
												print esc_html( " {$info['last_name']->value}" );
												endif;
											?>
										</div>
									<?php endif; ?>
									<?php if ( $info['location']->display && $info['location']->value && isset( $countries[ $info['location']->value ] ) ) : ?>

									<p class="events-single__country">
										<?php echo esc_html( $countries[ $info['location']->value ] ); ?>
									</p>
									<?php endif; ?>
								</div>
							</a>
						</div>
						<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div id="events-share-lightbox" class="lightbox">
		<?php require locate_template( 'templates/share-modal.php', false, false ); ?>
	</div>
	<?php
	if ( ! is_user_logged_in() ) :
		?>
		<div id="event-rsvp-lightbox" class="lightbox">
		<?php include locate_template( 'templates/event-rsvp.php', false, false ); ?>
		</div>
		<?php
		endif;
	?>
</div>
<?php if ( isset( $options['report_email'] ) && is_user_logged_in() && isset( $_SERVER['HTTP_HOST'] ) ) : ?>
<div class="events-single__report-container">
	<?php
		$report_email = trim( sanitize_email( $options['report_email'] ) );
		$subject      = sprintf( '%s %s', __( 'Reporting Event', 'community-portal' ), $em_event->event_name );
	if ( ! empty( $_SERVER['HTTP_HOST'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {
		$server_host = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );
		$server_uri  = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$body        = __( 'Please provide a reason you are reporting this event', 'community-portal' ) . ' https://' . $server_host . $server_uri;
	}
	?>
		<a href="mailto:<?php echo esc_attr( $report_email ); ?>?subject=<?php echo esc_attr( $subject ); ?>&body=<?php echo esc_attr( $body ); ?>" class="group__report-group-link">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M12 8V12" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<circle cx="12" cy="16" r="0.5" fill="#CDCDD4" stroke="#0060DF"/>
			</svg>
			<?php esc_html_e( 'Report Event', 'community-portal' ); ?>
		</a>
</div>

<?php endif ?>
<?php if ( in_array( 'administrator', wp_get_current_user()->roles, true ) ) : ?>
<a href="#" id="events-show-debug-info" class="events-single__show-debug-info">Show Meta Data</a>
<div class="events-single__debug-info events-single__debug-info--hidden">
<h3>Debug Information</h3>

Discourse Group Information
<pre>
	<?php
		$discourse_group_info = mozilla_get_discourse_info( $em_event->post_id, 'event' );
		print_r( $discourse_group_info );
	?>
</pre>

Event Meta
<pre>
	<?php
		print_r( $event_meta );
	?>
</pre>

</div>
<?php endif; ?>
