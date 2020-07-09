<?php
/**
 * Homepage
 *
 * Homepage file for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<?php get_header(); ?>
	<div class="content content--homepage">
		<?php
			$current_translation = mozilla_get_current_translation();
			$theme_directory = get_template_directory();
			require "{$theme_directory}/countries.php";
		
			$fields = array(
				'hero_title',
				'hero_subtitle',
				'hero_image',
				'hero_cta_existing',
				'hero_cta_new',
				'hero_cta_text',
				'featured_campaign',
				'featured_campaign_title',
				'featured_campaign_copy',
				'featured_events',
				'featured_events_title',
				'featured_events_cta_text',
				'featured_events_secondary_cta_text',
				'featured_groups',
				'featured_groups_title',
				'featured_groups_cta_text',
				'featured_groups_secondary_cta_text',
				'featured_activities',
				'featured_activities_title',
				'featured_activities_cta_text',
			);

			$field_values = new stdClass();
			foreach ( $fields as $field ) {
				$field_values->$field = get_field( $field );
			}

			$has_events = false;
			if ( $field_values->featured_events && is_array( $field_values->featured_events ) && count( $field_values->featured_events ) > 0 ) {
				foreach ( $field_values->featured_events as $e ) {
					if ( isset( $e['single_event'] ) && false !== $e['single_event'] ) {
						$has_events = true;
						break;
					}
				}
			}

			?>
		<div class="homepage homepage__container">
			<div class="homepage__hero">
				<div class="homepage__hero__background">
				</div>
				<div class="row">
					<div class="col-md-5 homepage__hero__splash">
						<?php if ( isset( $field_values->hero_image['url'] ) && strlen( $field_values->hero_image['url'] ) > 0 ) : ?>
							<div class="homepage__hero__image">
								<?php
									$image_alt = isset( $field_values->hero_image['alt'] ) ? esc_attr( $field_values->hero_image['alt'] ) : '';
								?>
								<img src="<?php echo esc_attr( $field_values->hero_image['url'] ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
							</div>
						<?php endif; ?>
					</div>
					<div class="col-md-4 col-md-offset-1">
						<div class="homepage__content">
							<h1 class="homepage__hero__title title title--main"><?php echo esc_html( $field_values->hero_title ); ?></h1>
							<p class="homepage__hero__subtitle subtitle"><?php echo esc_html( $field_values->hero_subtitle ); ?></p>
							<?php if ( ( isset( $field_values->hero_cta_existing ) && strlen( $field_values->hero_cta_existing ) > 0 ) || ( isset( $field_values->hero_cta_new ) && strlen( $field_values->hero_cta_new ) > 0 ) ) : ?>
								<a href="<?php echo ( is_user_logged_in() ? esc_attr( $field_values->hero_cta_existing ) : esc_attr( $field_values->hero_cta_new ) ); ?>"class="btn btn--dark btn--small homepage__hero__cta">
									<?php
										$link_text = isset( $field_values->hero_cta_text ) ? $field_values->hero_cta_text : '';
									?>
									<?php echo esc_html( $link_text ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<?php
			if ( isset( $field_values->featured_campaign ) ) :
				$c = $field_values->featured_campaign;
				if ( $c ) {

					$start = strtotime( get_field( 'campaign_start_date', $c->ID ) . '00:00:00' );
					$end   = strtotime( get_field( 'campaign_end_date', $c->ID ) . '23:59:59' );
					$today = strtotime( 'tomorrow - 1 second' );

					$campain_status = get_field( 'campaign_status', $c->ID );
					if ( 'closed' !== strtolower( $campain_status ) ) {
						if ( $start && ! $end ) {
							if ( $today >= $start ) {
								$current_campaign = $c;
							}
						} elseif ( $start && $end ) {

							if ( $today >= $start && $today <= $end ) {

								$current_campaign = $c;
							}
						}
					}
				}
				if ( isset( $current_campaign ) ) :
					$current_campaign_image = get_the_post_thumbnail_url( $current_campaign->ID );

					$current_campaign_status        = get_field( 'campaign_status', $current_campaign->ID );
					$current_campaign_hero_cta      = get_field( 'hero_cta', $current_campaign->ID );
					$current_campaign_hero_cta_link = get_field( 'hero_cta_link', $current_campaign->ID );

					$current_campaign_start_date       = get_field( 'campaign_start_date', $current_campaign->ID );
					$current_campaign_end_date         = get_field( 'campaign_end_date', $current_campaign->ID );
					$current_campaign_card_description = get_field( 'card_description', $current_campaign->ID );
					$current_campaign_tags             = get_the_terms( $current_campaign, 'post_tag' );
					?>
				<div class="homepage__campaign">
					<div class="homepage__campaign__background"></div>
					<div class="row homepage__campaign__container">
						<div class="col-lg-3 homepage__campaign__meta">
							<img class="homepage__campaign__image" src="<?php echo esc_attr( get_stylesheet_directory_uri() . '/images/homepage-campaign.svg' ); ?>" alt="">
							<div class="homepage__campaign__copy">
								<h2 class="subheader homepage__campaign__subheader"><?php echo esc_html( $field_values->featured_campaign_title ); ?></h2>
								<p>
									<?php echo esc_html( $field_values->featured_campaign_copy ); ?>
								</p>
							</div>
						</div>
						<div class="col-lg-8 col-lg-offset-1 homepage__campaign__active">
						<?php if ( $current_campaign ) : ?>
							<div class="campaigns__active-campaign">
								<div class="campaigns__active-campaign-hero-container">
									<div class="campaign__hero-image" style="background-image: url(<?php echo esc_html( $current_campaign_image ); ?>);">
									</div>
									<div class="campaigns__active-campaign-title-container">
										<div class="campaigns__active-campaign-status"><?php echo esc_html( $current_campaign_status ); ?></div>
										<h2 class="campaigns__active-campaign-title"><?php echo esc_html( $current_campaign->post_title ); ?></h2>
										<div class="campaigns__active-campaign-date-container">
											<?php echo esc_html( $current_campaign_start_date ); ?>
														<?php
														if ( $current_campaign_end_date ) :
															?>
												- <?php echo esc_html( $current_campaign_end_date ); ?><?php endif; ?>
										</div>
										<a href="<?php echo esc_attr( get_home_url( null, 'campaigns/'. $current_campaign->post_name ) ); ?>" class="campaign__hero-cta"><?php echo esc_html_e( 'Get Involved', 'community-portal' ); ?></a>
									</div>
								</div>
							<?php if ( ! empty( $current_campaign_card_description ) ) : ?>
								<div class="campaigns__active-campaign-description">
									<?php echo wp_kses( $current_campaign_card_description, wp_kses_allowed_html( 'post' ) ); ?>
								</div>
				<?php endif; ?>
								<?php if ( is_array( $current_campaign_tags ) && count( $current_campaign_tags ) > 0 ) : ?>
								<div class="campaigns__active-campaign-tags">
									<span class="campaigns__active-campaign-tag"><?php echo esc_html( $current_campaign_tags[0]->name ); ?></span>
								</div>
								<?php endif; ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( $field_values->featured_groups && is_array( $field_values->featured_groups ) && count( $field_values->featured_groups ) > 0 ) : ?>
			<div class="homepage__groups">
				<div class="homepage__groups__background"></div>
				<div class="row homepage__groups__meta">
					<div class="col-md-6 col-sm-12">
						<h2 class="subheader homepage__groups__subheader"><?php echo esc_html( $field_values->featured_groups_title ); ?></h2>
					</div>
					<div class="col-md-6 col-sm-12 homepage__groups__cta">
						<a href="<?php echo esc_attr( get_home_url( null, 'groups')); ?>" class="btn btn--small btn--dark"><?php echo esc_html( $field_values->featured_groups_cta_text ); ?></a>
					</div>
				</div>
				<div class="row homepage__groups__grid">
					<?php
					foreach ( $field_values->featured_groups as $featured_group ) :
						$group        = groups_get_group( array( 'group_id' => $featured_group['featured_group'] ) );
						$meta         = groups_get_groupmeta( $group->id, 'meta' );
						$member_count = groups_get_total_member_count( $group->id );
						?>
						<?php if ( $group->id ) : ?>
					<div class="col-lg-4 col-md-6 groups__column">
						<a href="<?php echo esc_attr( get_home_url( null, 'groups/' . $group->slug ) ); ?>/" class="groups__card groups__card--homepage">
							<?php
							if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
								$meta['group_image_url'] = preg_replace( '/^http:/i', 'https:', $meta['group_image_url'] );
							}
							?>
							<div class="groups__group-image" style="background-image: url('<?php echo esc_attr( ( isset( $meta['group_image_url'] ) && strlen( $meta['group_image_url'] ) > 0 ) ? $meta['group_image_url'] : get_stylesheet_directory_uri() . '/images/group.png' ); ?>');">
							</div>
							<div class="groups__card-content">
								<h2 class="groups__group-title"><?php echo esc_html( $group->name ); ?></h2>
								<?php if ( isset( $meta['group_city'] ) && strlen( trim( $meta['group_city'] ) ) > 0 || isset( $meta['group_country'] ) && '0' !== $meta['group_country'] ) : ?>
								<div class="groups__card-location">
										<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M14 7.66699C14 12.3337 8 16.3337 8 16.3337C8 16.3337 2 12.3337 2 7.66699C2 6.07569 2.63214 4.54957 3.75736 3.42435C4.88258 2.29913 6.4087 1.66699 8 1.66699C9.5913 1.66699 11.1174 2.29913 12.2426 3.42435C13.3679 4.54957 14 6.07569 14 7.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M8 9.66699C9.10457 9.66699 10 8.77156 10 7.66699C10 6.56242 9.10457 5.66699 8 5.66699C6.89543 5.66699 6 6.56242 6 7.66699C6 8.77156 6.89543 9.66699 8 9.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<?php
											echo esc_html( trim( $meta['group_city'] ) );
										?>
											<?php
											if ( isset( $meta['group_country'] ) && strlen( $meta['group_country'] ) > 0 ) {
												if ( isset( $meta['group_city'] ) && strlen( $meta['group_city'] ) > 0 ) {
													echo esc_html( trim( ", {$countries[$meta['group_country']]}" ) );
												} else {
													echo esc_html( $countries[ $meta['group_country'] ] );
												}
											}
											?>
								</div>
								<?php endif; ?>
								<div class="groups__card-members">
										<svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M12.3334 14V12.6667C12.3334 11.9594 12.0525 11.2811 11.5524 10.781C11.0523 10.281 10.374 10 9.66675 10H4.33341C3.62617 10 2.94789 10.281 2.4478 10.781C1.9477 11.2811 1.66675 11.9594 1.66675 12.6667V14" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M6.99992 7.33333C8.47268 7.33333 9.66659 6.13943 9.66659 4.66667C9.66659 3.19391 8.47268 2 6.99992 2C5.52716 2 4.33325 3.19391 4.33325 4.66667C4.33325 6.13943 5.52716 7.33333 6.99992 7.33333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M16.3333 14.0002V12.6669C16.3328 12.0761 16.1362 11.5021 15.7742 11.0351C15.4122 10.5682 14.9053 10.2346 14.3333 10.0869" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M11.6667 2.08691C12.2404 2.23378 12.7488 2.56738 13.1118 3.03512C13.4749 3.50286 13.672 4.07813 13.672 4.67025C13.672 5.26236 13.4749 5.83763 13.1118 6.30537C12.7488 6.77311 12.2404 7.10671 11.6667 7.25358" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<?php echo esc_html( "{$member_count}&nbsp;" ) . esc_html( 'Members', 'community-portal' ); ?>
								</div>
								<div class="groups__card-info">
									<div class="groups__card-tags">
										<?php
												$tag_counter = 0;
												if (isset($meta) && isset($meta['group_tags'])):
										?>
										<ul class="groups__card-tags__container">
										<?php foreach ( $meta['group_tags'] as $key => $value ) : 
											$tag = false;
											if ('en' !== $current_translation) {
												$tag = get_term_by('slug', $value . '_' . $current_translation, 'post_tag');
											}
											if (false === $tag) {
												$tag = get_term_by('slug', $value, 'post_tag');
											}
											if (!empty($tag) ):
										?>
												<li class="groups__tag"><?php echo esc_html( $tag->name ); ?></li>
												<?php $tag_counter++; ?>
												<?php if ( 2 === $tag_counter && count( $meta['group_tags'] ) > 2 ) : ?>
													<li class="groups__tag">+ <?php echo esc_html( count( $meta['group_tags'] ) - 2 ); ?> <?php echo esc_html_e( ' more tags', 'community-portal' ); ?></li>
													<?php break; ?>
												<?php endif; ?>
											<?php endif; ?>
										<?php endforeach; ?>
										</ul>
										<?php endif; ?>	
									</div>
								</div>
							</div>
						</a>
					</div>
					<?php endif; ?>
					<?php endforeach; ?>
					<div class="col-lg-4 col-md-6 events__column homepage__events__count">
						<?php
							$groups_total = count( groups_get_groups( array() ) );
						if ( $groups_total > 15 && $groups_total <= 105 ) :
							$groups_total  = floor( ( $groups_total / 15 ) ) * 15;
							$groups_total .= '+';
							elseif ( $groups_total > 105 && $groups_total <= 1005 ) :
								$groups_total  = floor( ( $groups_total ) / 105 ) * 105;
								$groups_total .= '+';
							elseif ( $groups_total > 1005 ) :
								$groups_total  = floor( ( $groups_total / 1005 ) * 1005 );
								$groups_total .= '+';
							else :
								$groups_total = '10+';
							endif;
							?>
						<p>
							<span class="large-number homepage__events__count__span"><?php echo esc_html( $groups_total ); ?></span>
							<?php esc_html_e( 'More Groups.', 'community-portal' ); ?>
							<a href="<?php echo esc_attr( get_home_url( null, 'groups' ) ); ?>" class="homepage__events__count__link"><?php esc_html( $field_values->featured_groups_secondary_cta_text ); ?></a>
						</p>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<?php if ( $has_events ) : ?>
			<div class="homepage__events">
				<div class="homepage__events__background"></div>
				<div class="row homepage__events__meta">
					<div class="col-md-6 col-sm-12">
						<?php if ( isset( $field_values->featured_events_title ) && strlen( $field_values->featured_events_title ) > 0 ) : ?>
							<h2 class="subheader homepage__events__subheader"><?php echo esc_html( $field_values->featured_events_title ); ?></h2>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-sm-12 homepage__events__cta">
						<?php if ( isset( $field_values->featured_events_cta_text ) && strlen( $field_values->featured_events_cta_text ) > 0 ) : ?>
							<a href="<?php echo esc_attr( get_home_url( null, 'events' ) ); ?>" class="btn btn--small btn--dark"><?php echo esc_html( $field_values->featured_events_cta_text ); ?></a>
						<?php endif; ?>
					</div>
				</div>
				<div class="row homepage__events__grid">
					<?php
					if ( is_array( $field_values->featured_events ) ) {

						foreach ( $field_values->featured_events as $featured_event ) {
							if ( $featured_event['single_event'] ) {
								$event = EM_Events::get(
									array(
										'post_id' => $featured_event['single_event']->ID,
										'scope'   => 'all',
									)
								);
								$values = array_values($event);
								$event = array_shift( $values );
								$all_countries = em_get_countries();
                

								include locate_template( 'plugins/events-manager/templates/template-parts/single-event-card.php', false, false );
							}
						}
					}
					?>
					<div class="col-lg-4 col-md-6 events__column homepage__events__count">
						<?php
							$events_total = count( EM_Events::get() );
						if ( $events_total > 15 && $events_total <= 105 ) :
							$events_total  = floor( ( $events_total / 10 ) ) * 10;
							$events_total .= '+';
							elseif ( $events_total > 105 && $events_total <= 1005 ) :
								$events_total  = floor( ( $events_total ) / 100 ) * 100;
								$events_total .= '+';
							elseif ( $events_total > 1005 ) :
								$events_total  = floor( ( $events_total / 1000 ) * 1000 );
								$events_total .= '+';
							else :
								$events_total = '10+';
							endif;
							?>
						<p>
							<span class="large-number homepage__events__count__span"><?php echo esc_html( $events_total ); ?></span>
							<?php esc_html_e( 'More Events.', 'community-portal' ); ?>
							<?php if ( isset( $field_values->featured_events_secondary_cta_text ) && strlen( $field_values->featured_events_secondary_cta_text ) > 0 ) : ?>
								<a href="<?php echo esc_attr( get_home_url( null, 'events' ) ); ?>" class="homepage__events__count__link"><?php echo esc_html( $field_values->featured_events_secondary_cta_text ); ?></a>
							<?php endif; ?>
						</p>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<?php if ( isset( $field_values->featured_activities ) ) : ?>
			<div class="homepage__activities">
				<div class="homepage__activities__background"></div>
				<div class="row homepage__activities__meta">
					<div class="col-md-6 col-sm-12">
						<?php if ( isset( $field_values->featured_activities_title ) && strlen( $field_values->featured_activities_title ) > 0 ) : ?>
							<h2 class="subheader homepage__activities__subheader"><?php echo esc_html( $field_values->featured_activities_title ); ?></h2>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-sm-12 homepage__events__cta">
						<?php if ( isset( $field_values->featured_activities_cta_text ) && strlen( $field_values->featured_activities_cta_text ) > 0 ) : ?>
							<a href="<?php echo esc_attr( get_home_url( null, 'activities' ) ); ?>" class="btn btn--small btn--dark"><?php echo esc_html( $field_values->featured_activities_cta_text ); ?></a>
						<?php endif; ?>
					</div>
				</div>
				<div class="row homepage__activities__grid">
					<?php
					if ( is_array( $field_values->featured_activities ) ) {
						foreach ( $field_values->featured_activities as $activity ) {
							if ( $activity['single_activity'] ) {
								$activity = $activity['single_activity'];
							} else {
								continue;
							}
							$activity_image  = wp_get_attachment_url( get_post_thumbnail_id( $activity->ID ) );
							$activitiy_desc  = get_field( 'card_description', $activity->ID );
							$time_commitment = get_field( 'time_commitment', $activity->ID );

							?>
							<div class="col-lg-4 col-md-6 activities__column">
								<div class="activities__card">
									<a href="<?php echo esc_attr( get_home_url( null, 'activities/' . $activity->post_name ) ); ?>" class="activities__link">
										<?php
										$background_image = strlen( $activity_image ) > 0 ? $activity_image : get_stylesheet_directory_uri() . '/images/activity.png';

										?>
										<div class="activities__activity-image" style="background-image: url('<?php echo esc_url( $background_image ); ?>');">
										</div>
										<div class="activities__card-content">
											<h2 class="activities__activity-title"><?php echo esc_html( $activity->post_title ); ?></h2>
											<div class="activities__copy-container">
												<?php if (isset($activitiy_desc) && strlen($activitiy_desc) > 0 ): ?>
												<p class="activities__copy">
												<?php
													echo esc_html( $activitiy_desc );
												?>
												</p>
												<?php endif; ?>
											</div>
											<?php
											$tags = get_the_tags( $activity->ID );
											if ( $time_commitment || (is_array($tags) && count($tags) > 0)) :
											?>
											<div class="activities__tag-container">
											<?php if ( is_array( $tags ) && count( $tags ) > 0 ) : ?>
												<span class="activities__tag"><?php echo esc_html( $tags[0]->name ); ?></span>
											<?php endif; ?>
											<?php if ( $time_commitment ) : ?>
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M7.99992 14.6654C11.6818 14.6654 14.6666 11.6806 14.6666 7.9987C14.6666 4.3168 11.6818 1.33203 7.99992 1.33203C4.31802 1.33203 1.33325 4.3168 1.33325 7.9987C1.33325 11.6806 4.31802 14.6654 7.99992 14.6654Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
													<path d="M8 4V8L10.6667 9.33333" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
												<span class="activities__time-commitment"><?php echo esc_html( $time_commitment ); ?></span>
											<?php endif; ?>
											</div>
											<?php endif; ?>
										</div>
									</a>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
<?php get_footer(); ?>
