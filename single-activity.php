<?php
/**
 * Activities single page
 *
 * Activities single page
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @since  1.0.0
 */

	get_header();
	global $post;

	$featured_image = get_the_post_thumbnail_url();

	// Get the advanced custom fields for the activity.
	$youtube_video                      = get_field( 'video' );
	$primary_cta_copy                   = get_field( 'primary_cta_copy' );
	$primary_cta_url                    = get_field( 'primary_cta_url' );
	$activity_flow_title                = get_field( 'activity_flow_title' );
	$activity_flow_copy                 = get_field( 'activity_flow_copy' );
	$activity_flow                      = get_field( 'flows' );
	$additional_information_title_left  = get_field( 'additional_information_title_left' );
	$additional_information_copy_left   = get_field( 'additional_information_copy_left' );
	$additional_information_title_right = get_field( 'additional_information_title_right' );
	$additional_information_copy_right  = get_field( 'additional_information_copy_right' );
	$time_commitment                    = get_field( 'time_commitment' );
	$metrics                            = get_field( 'metrics' );

	// Tags for activity.
	$tags = get_the_terms( $post, 'post_tag' );
?>
	<div class="content">
		<section class="activity">
			<div class="activity__container">
				<h1 class="activity__title"><?php echo esc_html( $post->post_title ); ?></h1>
				<div class="activity__info">
					<div class="activity__left-column">
						<div class="activity__card">
							<?php if ( $featured_image ) : ?>
								<div class="activity__card-image" style="background-image: url('<?php echo esc_url_raw( $featured_image ); ?>');">
							</div>
							<?php endif; ?>
							<div class="activity__card-content">
								<div class="activity__cta-container <?php echo ( ! isset( $primary_cta_copy ) || ! strlen( $primary_cta_copy ) > 0 || ! isset( $primary_cta_url ) || ! strlen( $primary_cta_url ) > 0 ? 'activity__cta-container--single' : '' ); ?>">
									<?php if ( isset( $primary_cta_copy ) && strlen( $primary_cta_copy ) > 0 && isset( $primary_cta_url ) && strlen( $primary_cta_url ) > 0 ) : ?>
										<a href="<?php echo esc_url_raw( $primary_cta_url ); ?>" class="activity__cta"><?php echo esc_html( "{$primary_cta_copy}" ); ?></a>
									<?php endif; ?>
									<a href="#" class="activity__cta activity__cta--share">
										<svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M1 9V15C1 15.3978 1.15804 15.7794 1.43934 16.0607C1.72064 16.342 2.10218 16.5 2.5 16.5H11.5C11.8978 16.5 12.2794 16.342 12.5607 16.0607C12.842 15.7794 13 15.3978 13 15V9M10 4.5L7 1.5M7 1.5L4 4.5M7 1.5V11.25" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<?php esc_html_e( 'Share Activity', 'community-portal' ); ?>
									</a>
								</div>
								<?php if (!empty($post->post_content) ): ?>
									<hr class="activity__keyline" />
									<div class="activity__description-container">
										<?php
											echo wp_kses(
												wpautop( $post->post_content ),
												array(
													'h2'     => array( 'class' => array() ),
													'h3'     => array( 'class' => array() ),
													'h4'     => array( 'class' => array() ),
													'p'      => array( 'class' => array() ),
													'br'     => array(),
													'div'    => array( 'class' => array() ),
													'img'    => array(
														'href' => array(),
														'class' => array(),
														'alt'  => array(),
													),
													'a'      => array(
														'class' => array(),
														'href' => array(),
													),
													'figure' => array( 'class' => array() ),
													'figcaption' => array( 'class' => array() ),
												)
											);
											?>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<?php if ( strlen( $youtube_video ) > 0 ) : ?>
							<?php
							preg_match( '/[\\?\\&]v=([^\\?\\&]+)/', $youtube_video, $matches );
							$youtube_id = ( is_array( $matches ) && count( $matches ) > 1 ) ? $matches[1] : false;
							?>
						<div class="activity__card activity__card--video">
							<?php if ( $youtube_id ) : ?>
							<div class="activity__card-content">
								<div class="activity__video-container">
									<iframe class="activity__video" src="https://www.youtube.com/embed/<?php echo esc_attr( $youtube_id ); ?>"></iframe>
								</div>
							</div>
							<?php endif; ?>
						</div>
						<?php endif; ?>
						<?php if ( strlen( $activity_flow_title ) > 0 ) : ?>
							<h2 class="activity__card-title"><?php echo esc_html( $activity_flow_title ); ?></h2>
						<?php endif; 
							if ( is_array( $activity_flow ) && count( $activity_flow ) > 0): 
						?>
							<div class="activity__card activity__card--flow">        
								<div class="activity__card-content">
									<?php
									echo wp_kses(
										$activity_flow_copy,
										array(
											'p'   => array(),
											'a'   => array( 'href' => array() ),
											'div' => array( 'class' => array() ),
										)
									);
									?>
									<?php  if ( strlen( $activity_flow[0]['title'] ) > 0 ) : ?>
									<div class="activity__accordion">
										<?php $accordion_counter = 0; ?>
										<?php foreach ( $activity_flow as $flow ) : ?>
											<?php if ( strlen( $flow['title'] ) > 0 && strlen( $flow['copy'] ) > 0 ) : ?>
											<div class="activity__accordion-container">
												<div role="heading" aria-level="3">
													<button class="activity__accordion-input" id="ac-<?php echo esc_attr( $accordion_counter ); ?>" role="button" name="accordion-<?php echo esc_attr( $accordion_counter ); ?>" type="button" aria-expanded="false" aria-controls="ac-panel-<?php echo esc_attr( $accordion_counter ); ?>">
														<?php echo esc_html( $flow['title'] ); ?>
													</button>
												</div>
												<div id="ac-panel-<?php echo esc_attr( $accordion_counter ); ?>" class="activity__accordion-content" aria-labelledby="ac-<?php echo esc_attr( $accordion_counter ); ?>">
													<?php
													echo wp_kses(
														$flow['copy'],
														array(
															'p' => array(),
															'a' => array(
																'href' => array(),
																'class' => array(),
															),
															'div' => array( 'class' => array() ),
														)
													);
													?>
												</div>
											</div>
											<?php endif; ?>
											<?php $accordion_counter++; ?>
										<?php endforeach; ?>
									</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>
						<?php if ( strlen( $additional_information_title_left ) > 0 ) : ?>
						<h2 class="activity__card-title"><?php echo esc_html( $additional_information_title_left ); ?></h2>               
						<?php endif; ?>
						<?php if ( strlen( $additional_information_copy_left ) > 0 ) : ?>
						<div class="activity__card activity__card--additional">        
							<div class="activity__card-content">
								<?php
								echo wp_kses(
									$additional_information_copy_left,
									array(
										'p'   => array(),
										'div' => array( 'class' => array() ),
										'a'   => array(
											'href'  => array(),
											'class' => array(),
										),
									)
								);
								?>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<div class="activity__right-column">
						<?php if ( (is_array( $tags ) && count( $tags ) > 0 ) || $time_commitment ) : ?>
							<div class="activity__card">
								<div class="activity__card-content">
									<?php if ( is_array( $tags ) && count( $tags ) > 0 ) : ?>
									<span><?php esc_html_e( 'Tags', 'community-portal' ); ?></span>
									<div class="activity__tags">
										<?php foreach ( $tags as $loop_tag ) : ?>
										<span class="activity__tag"><?php echo esc_html( $loop_tag->name ); ?></span>
									<?php endforeach; ?>
									</div>
									<?php endif; ?>
									<?php if ( $time_commitment ) : ?>
									<span><?php esc_html_e( 'Time Commitment', 'community-portal' ); ?></span>
									<div class="activity__time-commitment">
										<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M8 14.6654C11.6819 14.6654 14.6667 11.6806 14.6667 7.9987C14.6667 4.3168 11.6819 1.33203 8 1.33203C4.3181 1.33203 1.33333 4.3168 1.33333 7.9987C1.33333 11.6806 4.3181 14.6654 8 14.6654Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M8 4V8L10.6667 9.33333" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>

										<?php echo esc_html( $time_commitment ); ?>
									</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>
						<?php if ( strlen( $additional_information_title_right ) > 0 || strlen( $additional_information_copy_right ) > 0 ) : ?>
						<div class="activity__card activity__card--additional">
							<div class="activity__card-content">
							<?php if ( strlen( $additional_information_title_right ) > 0 ) : ?>
								<span><?php echo esc_html( $additional_information_title_right ); ?></span>
							<?php endif; ?>
							<?php if ( strlen( $additional_information_copy_right ) > 0 ) : ?>
								<div class="activity__markup">
									<?php
									echo wp_kses(
										$additional_information_copy_right,
										array(
											'p'   => array( 'class' => array() ),
											'div' => array( 'class' => array() ),
											'a'   => array(
												'href'  => array(),
												'class' => array(),
											),
										)
									);
									?>
								</div>
							<?php endif; ?>
							</div>
						</div>
						<?php endif; ?>
						<?php

						$args           = array( 'scope' => 'future' );
						$events         = EM_Events::get( $args );
						$related_events = array();

						$current_translation = mozilla_get_current_translation();
						if ( 'en' === $current_translation ) {
							$initiative_id = intval( $post->ID );
						} else {
							$initiative_id = intval( apply_filters( 'wpml_object_id', $post->ID, 'activity', false, 'en' ) );
						}

						foreach ( $events as $e ) {
							$event_meta = get_post_meta( $e->post_id, 'event-meta' );
							if ( isset( $event_meta[0]->initiative ) && intval( $event_meta[0]->initiative ) === $initiative_id ) {
								$related_events[] = $e;
							}

							if ( 4 === count( $related_events ) ) {
								break;
							}
						}
						?>
						<?php if ( is_array( $related_events ) && count( $related_events ) > 0 ) : ?>
						<div class="activity__card activity__card--related-events">
							<div class="activity__card-content">
								<span><?php esc_html_e( 'Related Events', 'community-portal' ); ?></span>
								<?php foreach ( $related_events as $event ) : ?>
									<?php
										$date_format = 'en' === $current_translation ? '%b %d' : '%d %b';
										$event_date = mozilla_localize_date($event->start_date, $date_format);
										$location = em_get_location( $event->location_id );
									?>
								<a class="activity__event
									<?php
									if ( false === next( $related_events ) ) :
										?>
									activity__event--last<?php endif; ?>" href="<?php echo esc_attr( get_home_url( null, '/events/' . $event->event_slug ) ); ?>"> 
									<div class="activity__event-date">
										<?php echo esc_html( $event_date ); ?>
									</div>
									<div class="activity__event-info">
										<div class="activity__event-title"><?php echo esc_html( $event->event_name ); ?></div>
										<div class="activity__event-time">
											<?php 
												$date_format = 'en' === $current_translation ? '%B %d, %G ∙ %H:%M' : '%d %B, %G ∙ %H:%M';
												$event_date = mozilla_localize_date($event->start_date, $date_format);
												echo esc_html ($event_date) . ' ' . esc_html__('UTC');
											?>
										</div>
										<div class="activity__event-location">
											<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
											<?php if ( 'OE' === $location->location_country ) : ?>
												<?php esc_html_e( 'Online Event', 'community-portal' ); ?>
											<?php elseif ( $location->location_town && $location->location_country ) : ?>
												<?php echo esc_html( "{$location->location_town}, {$countries[$location->location_country]}" ); ?>
											<?php elseif ( $location->location_town && ! $location->location_country ) : ?>
												<?php echo esc_html( "{$location->location_town}" ); ?>
											<?php elseif ( ! $location->location_town && $location->location_country ) : ?>
												<?php echo esc_html( "{$countries[$location->location_country]}" ); ?>
											<?php endif; ?>
										</div>
									</div>
								</a>   
								<?php endforeach; ?>
								<a href="<?php echo esc_attr( add_query_arg( array( 'initiative' => $post->ID ), get_home_url( null, '/events/' ) ) ); ?>" class="activity__events-link">
									<?php esc_html_e( 'View more events', 'community-portal' ); ?><svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.33301 8.66634L5.99967 4.99967L2.33301 1.33301" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
								</a>
							</div>
						<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	<div id="activity-share-lightbox" class="lightbox">
		<?php require locate_template( 'templates/share-modal.php', false, false ); ?>
	</div>


<?php
	get_footer();
?>
