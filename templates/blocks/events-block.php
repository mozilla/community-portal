<?php
/**
 * Events Block - Campaigns
 *
 * Campaign block for events
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

	$all_countries = em_get_countries();
?>
<div class="campaign__events-block">
	<div class="campaign__block-container
	<?php
	if ( $block['keyline'] ) :
		?>
		campaign__block-container--keyline<?php endif; ?>">
		<h2 class="campaign__heading-2"><?php print esc_html( $block['title'] ); ?></h2>
		<div class="campaign__block-content ">

			<?php if ( isset( $block['events'] ) ) : ?>
				<?php
				if ( ! $block['events'] || ( is_array( $block['events'] ) && count( $block['events'] ) < 4 ) ) {
					$args           = array( 'scope' => 'future' );
					$events         = EM_Events::get( $args );
					$related_events = array();

					foreach ( $events as $e ) {
						$event_meta = get_post_meta( $e->post_id, 'event-meta' );

						if ( isset( $event_meta[0]->initiative ) && intval( $event_meta[0]->initiative ) === $post->ID ) {

							$related_events[] = array( 'event' => get_post( $e->post_id ) );
						}

						if ( count( $related_events ) === 4 ) {
							break;
						}
					}

					if ( false === $block['events'] ) {
						$block['events'] = $related_events;
					} else {
						$block['events'] = array_merge( $block['events'], $related_events );
					}
				}

				?>
			<div class="campaign__events-container">
				<?php foreach ( $block['events'] as $event ) : ?>
					<?php
					$event_meta  = get_post_meta( $event['event']->ID, 'event-meta' );
					$em_event    = em_get_event( $event['event']->ID, 'post_id' );
					$event_time  = strtotime( $em_event->event_start_date );
					$event_month = gmdate( 'M', $event_time );
					$event_day   = gmdate( 'j', $event_time );

					$location   = em_get_location( $em_event->location_id );
					$categories = ( ! is_null( $em_event ) ) ? $em_event->get_categories() : false;
					?>
				<a href="<?php print esc_attr( $event['event']->guid ); ?>" class="campaign__event">
					<div class="campaign__event-image" 
					<?php
					if ( isset( $event_meta[0]->image_url ) && strlen( $event_meta[0]->image_url ) > 0 ) :
						?>
						style="background-image: url('<?php print esc_attr( $event_meta[0]->image_url ); ?>')"<?php endif; ?>>
						<div class="campaign__event-date">
							<?php print esc_html( $event_month ) . ' ' . esc_html( $event_day ); ?>
						</div>
					</div>
					<div class="campaign__event-container">
						<h3 class="campaign__event-title"><?php print esc_html( $event['event']->post_title ); ?></h3>
						<div class="campaign__event-time">
							<?php print esc_html( gmdate( 'F j, Y ∙ G:i', $event_time ) ) . esc_html__( ' UTC', 'community-portal' ); ?>
						</div>
						<?php if ( strlen( $location->address ) > 0 || strlen( $location->town ) > 0 || strlen( $location->country ) > 0 ) : ?>
						<div class="campaign__event-location">
							<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php if ( 'OE' === $location->country ) : ?>
								<?php esc_html_e( 'Online Event', 'community-portal' ); ?>
							<?php else : ?>
								<?php if ( $location->address ) : ?>
									<?php print esc_html( $location->address ) . ' - '; ?>
								<?php endif; ?>
								<?php if ( $location->town ) : ?>
									<?php if ( strlen( $location->town ) > 180 ) : ?>
										<?php $city = substr( $location->town, 0, 180 ); ?>
									<?php endif; ?>
									<?php print esc_html( $city ); ?>
									<?php if ( $location->country ) : ?>
										<?php if ( $city ) : ?>
											<?php print ', '; ?>
										<?php endif; ?>
										<?php print esc_html( $all_countries[ $location->country ] ); ?>
									<?php endif; ?>
								<?php else : ?>
									<?php print esc_html( $all_countries[ $location->country ] ); ?>
								<?php endif; ?>
							<?php endif; ?>
						</div>
						<?php endif; ?>
						<?php if ( isset( $event_meta[0]->initiative ) && strlen( $event_meta[0]->initiative ) > 0 ) : ?>
							<?php
							$initiative = get_post( intval( $event_meta[0]->initiative ) );
							?>
						<div class="campaign__campaign-events">
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
							<?php print esc_html_( 'Part of ', 'community-portal' ) . ' ' . esc_html( $initiative->post_title ) . ( 'campaign' === $initiative->post_type ) ? esc_html__( 'Campaign', 'community-portal' ) : esc_html__( 'Activity', 'community-portal' ); ?>
						</div>
						<?php endif; ?>
						<ul class="events__tags">
						<?php if ( false !== $categories && is_array( $categories->terms ) ) : ?>
							<?php foreach ( $categories->terms as $category ) : ?>
								<li class="tag"><?php echo esc_html( $category->name ); ?></li>
								<?php break; ?>
							<?php endforeach; ?>
						<?php endif; ?>
						</ul>
					</div>
				</a>
			<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
