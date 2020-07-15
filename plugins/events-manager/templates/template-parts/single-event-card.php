<?php
/**
 * Single Event Cards
 *
 * Template for single cards for events on events page for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php

	$template_dir = get_template_directory();

	$categories = ( ! is_null( $event ) ) ? $event->get_categories() : false;
	$location   = em_get_location( $event->location_id );
	$url        = get_home_url( null, '/events/' . $event->slug );

?>
<div class="col-lg-4 col-md-6 events__column">
	<div class="event-card">
		<a class="events__link" href="<?php echo esc_url_raw( $url ); ?>">
			<div class="event-card__image"
			<?php
				$card_event_meta = get_post_meta( $event->post_id, 'event-meta' );
				$img_url         = $card_event_meta[0]->image_url;

			if ( ( ! empty( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['SERVER_PORT'] ) && 'off' !== $_SERVER['HTTPS'] ) || 443 === $_SERVER['SERVER_PORT'] ) {
				$img_url = preg_replace( '/^http:/i', 'https:', $img_url );
			} else {
				$img_url = $img_url;
			}
			?>

			<?php if ( $img_url && '' !== $img_url ) : ?>
				style="background-image: url(<?php echo esc_url_raw( $img_url ); ?>)"
			<?php endif; ?>
			>
				<?php
					$month               = substr( $event->start_date, 5, 2 );
					$date                = substr( $event->start_date, 8, 2 );
					$event_year          = substr( $event->start_date, 0, 4 );
					$current_translation = mozilla_get_current_translation();
					$date_format         = 'en' === $current_translation ? 'M d' : 'd M';
					$formatted_date      = mozilla_localize_date( $event->start_date, $date_format );
					$formatted_date      = explode( ' ', $formatted_date );
				if ( isset( $formatted_date ) && count( $formatted_date ) > 1 ) :
					?>
					<p class="event-card__image__date"><span><?php echo esc_html( $formatted_date[0] ); ?> </span><span><?php echo esc_html( $formatted_date[1] ); ?></span>
					</p>
				<?php endif; ?>
			</div>
			<div class="event-card__description">
				<h3 class="event-card__description__title title--event-card"><?php echo esc_html( $event->event_name ); ?></h2>
			<?php
				$date_format    = 'en' === $current_translation ? 'F d, Y' : 'd F, Y';
				$formatted_date = mozilla_localize_date( $event->start_date, $date_format );
			?>
				<p><?php echo esc_html( $formatted_date ) . esc_html( ' @ ' ) . esc_html( substr( $event->event_start_time, 0, 5 ) ) . esc_html( ' - ' ) . esc_html( substr( $event->event_end_time, 0, 5 ) ) . esc_html( ' ' ) . esc_html( $event->event_timezone ); ?></p>

				<?php if ( strlen( $location->address ) > 0 || strlen( $location->town ) > 0 || strlen( $location->country ) > 0 ) : ?>
				<div class="event-card__location">
					<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<p class="text--light text--small">
					<?php
					if ( 'OE' === $location->country ) {
						esc_html_e( 'Online Event', 'community-portal' );
					} else {
						if ( $location->address ) {
							echo esc_html( $location->address ) . esc_html( ' - ' );
						}

						if ( isset( $location->town ) && strlen( $location->town ) > 0 ) {
							if ( strlen( $location->town ) > 180 ) {
								$city = substr( $location->town, 0, 180 );
								echo esc_html( $city );
							}

							if ( $location->country && isset( $all_countries[ $location->country ] ) ) {
								if ( isset( $city ) ) {
									print esc_html( ', ' );
								}

								echo esc_html( $all_countries[ $location->country ] );
							}
						} else {
							echo esc_html( $all_countries[ $location->country ] );
						}
					}
					?>
					</p>
				</div>
				<?php endif; ?>
				<?php if ( isset( $card_event_meta[0]->initiative ) && strlen( $card_event_meta[0]->initiative ) > 0 ) : ?>
					<?php
					$initiative = get_post( intval( $card_event_meta[0]->initiative ) );
					?>
				<div class="events__campaign">
					<?php if ( 'campaign' === $initiative->post_type ) : ?>
					<svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M10.233 4.89288L6.46173 5.83569C6.46173 5.83569 2.87906 6.58994 2.31337 7.15562C1.86082 7.60817 2.06196 8.03558 2.21909 8.19271C2.59621 8.56984 3.94757 9.92119 4.57611 10.5497" stroke="#737373" stroke-width="2"/>
						<path d="M14.0041 8.66376L13.0613 12.435C13.0613 12.435 12.307 16.0177 11.7414 16.5834C11.2888 17.0359 10.8614 16.8348 10.7043 16.6776C10.3271 16.3005 8.97578 14.9492 8.34724 14.3206" stroke="#737373" stroke-width="2"/>
						<path d="M5.24658 11.7637L3.86891 13.1413L4.81172 14.0842L5.75453 15.027L7.1322 13.6493" stroke="#737373" stroke-width="2" stroke-linejoin="round"/>
						<path d="M14.816 7.85125L8.34727 14.32L6.46165 12.4343L4.57603 10.5487L11.0448 4.08001C12.5375 2.58723 14.7898 2.84912 15.4183 3.47766C16.0469 4.1062 16.3088 6.35847 14.816 7.85125Z" stroke="#737373" stroke-width="2"/>
					</svg>
					<?php else : ?>
					<svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M14.6666 7H11.9999L9.99992 13L5.99992 1L3.99992 7H1.33325" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<?php endif; ?>
					<?php
					print esc_html__( 'Part of', 'community-portal' ) . esc_html( '  ' ) . esc_html( $initiative->post_title );
					if ( 'campaign' === $initiative->post_type ) {
						esc_html__( 'Campaign', 'community-portal' );
					} else {
						esc_html__( 'Activity', 'community-portal' );
					}
					?>
				</div>
				<?php endif; ?>
			</div>
			<ul class="events__tags">
			<?php if ( false !== $categories && is_array( $categories->terms ) ) : ?>
				<?php
				foreach ( $categories->terms as $category ) :
					$tag_name = mozilla_get_translated_tag( $category );
					?>
					<li class="tag"><?php echo esc_html( $tag_name ); ?></li>
					<?php break; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</ul>
		</a>
	</div>
</div>
