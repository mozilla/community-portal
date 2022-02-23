<?php
/**
 * Member profile page
 *
 * Member public profile page
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<?php
	require "{$theme_directory}/languages.php";
	require "{$theme_directory}/countries.php";
	require "{$theme_directory}/pronouns.php";
	$current_translation = mozilla_get_current_translation();
	$show_minimum_items  = 3;

	$event_countries = em_get_countries();

if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
	$avatar_url = preg_replace( '/^http:/i', 'https:', $info['profile_image']->value );
} else {
	$avatar_url = $info['profile_image']->value;
}

?>
<div class="profile__public-container">
	<section class="profile__section">
		<div class="profile__card">
			<div class="profile__card-header-container">
				<?php if ( $is_me ) : ?>
					<div class="profile__edit-link-container profile__edit-link-container--mobile">
					<a href="
					<?php
					if ( $current_translation ) :
						?>
						<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/<?php echo esc_attr( $info['username']->value ); ?>/profile/edit/group/1" class="profile__link">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z" fill="#0060DF"/>
							</svg>
						</a>
					</div>
				<?php endif; ?>
				<div class="profile__avatar
				<?php
				if ( false === $info['profile_image']->value || false === $info['profile_image']->display ) :
					?>
					profile__avatar--empty<?php endif; ?>"
					<?php
					if ( $info['profile_image']->display ) :
						?>
					style="background-image: url('<?php echo esc_attr( $avatar_url ); ?>')"<?php endif; ?> data-user="<?php echo esc_attr( $info['username']->value ); ?>">
				</div>
				<div class="profile__name-container">
					<h3 class="profile__user-title"><?php echo esc_html( $info['username']->value ); ?></h3>
					<span class="profile__user-name">
						<?php if ( $info['first_name']->display ) : ?>
							<?php echo esc_html( "{$info['first_name']->value}" ); ?>
						<?php endif; ?>
						<?php if ( $info['last_name']->display ) : ?>
							<?php echo esc_html( "{$info['last_name']->value}" ); ?>
						<?php endif; ?>
					</span>
					<?php if ( $info['pronoun']->display && isset( $pronouns[ $info['pronoun']->value ] ) ) : ?>
						<div class="profile__pronoun">
							<?php echo esc_html( $pronouns[ $info['pronoun']->value ] ); ?>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $is_me ) : ?>
					<div class="profile__edit-link-container">
						<a href="
						<?php
						if ( $current_translation ) :
							?>
							<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/people/<?php echo esc_attr( $info['username']->value ); ?>/profile/edit/group/1" class="profile__link">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z" fill="#0060DF"/>
							</svg>
						</a>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( $info['bio']->display ) : ?>
				<div class="profile__bio-container">
					<?php
					echo wp_kses(
						wpautop( substr( trim( $info['bio']->value ), 0, 3000 ) ),
						array(
							'p'  => array(),
							'br' => array(),
						)
					);
					?>
				</div>
			<?php endif; ?>
			<div class="profile__card-contact-container">
				<?php if ( ( $info['location']->display && $info['location']->value ) || ( $info['email']->value && $info['email']->display ) || ( $info['phone']->value && $info['phone']->display ) ) : ?>
					<span class="profile__contact-title"><?php esc_html_e( 'Contact Information', 'community-portal' ); ?></span>
				<?php endif; ?>
				<?php if ( $info['location']->display && $info['location']->value && $countries[ $info['location']->value ] ) : ?>
					<div class="profile__location-container">
						<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="profile__location-icon">
							<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
							<g clip-path="url(#clip0)">
								<path d="M23.5 14.334C23.5 20.1673 16 25.1673 16 25.1673C16 25.1673 8.5 20.1673 8.5 14.334C8.5 12.3449 9.29018 10.4372 10.6967 9.03068C12.1032 7.62416 14.0109 6.83398 16 6.83398C17.9891 6.83398 19.8968 7.62416 21.3033 9.03068C22.7098 10.4372 23.5 12.3449 23.5 14.334Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M16 16.834C17.3807 16.834 18.5 15.7147 18.5 14.334C18.5 12.9533 17.3807 11.834 16 11.834C14.6193 11.834 13.5 12.9533 13.5 14.334C13.5 15.7147 14.6193 16.834 16 16.834Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</g>
							<defs>
								<clipPath id="clip0">
									<rect width="20" height="20" fill="white" transform="translate(6 6)"/>
								</clipPath>
							</defs>
						</svg>
						<?php esc_html_e( 'Location', 'community-portal' ); ?>
						<div class="profile__details">
							<span class="profile__city-country">
								<?php echo esc_html( $countries[ $info['location']->value ] ); ?>
							</span>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( $info['email']->display && $info['email']->value ) : ?>
					<div class="profile__email-container">
						<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="profile__email-icon">
							<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
							<path d="M9.33366 9.33398H22.667C23.5837 9.33398 24.3337 10.084 24.3337 11.0007V21.0006C24.3337 21.9173 23.5837 22.6673 22.667 22.6673H9.33366C8.41699 22.6673 7.66699 21.9173 7.66699 21.0006V11.0007C7.66699 10.084 8.41699 9.33398 9.33366 9.33398Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M24.3337 11L16.0003 16.8333L7.66699 11" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<?php esc_html_e( 'Email', 'community-portal' ); ?>
						<div class="profile__details">
							<span class="profile__email">
								<?php echo esc_html( $info['email']->value ); ?>
							</span>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( $info['phone']->display && $info['phone']->value ) : ?>
					<div class="profile__phone-container">
						<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="profile__phone-icon">
							<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
							<path d="M24.3332 20.0994V22.5994C24.3341 22.8315 24.2866 23.0612 24.1936 23.2739C24.1006 23.4865 23.9643 23.6774 23.7933 23.8343C23.6222 23.9912 23.4203 24.1107 23.2005 24.185C22.9806 24.2594 22.7477 24.287 22.5165 24.2661C19.9522 23.9875 17.489 23.1112 15.3249 21.7078C13.3114 20.4283 11.6043 18.7212 10.3249 16.7078C8.91651 14.5338 8.04007 12.0586 7.76653 9.48276C7.7457 9.25232 7.77309 9.02006 7.84695 8.80078C7.9208 8.5815 8.03951 8.38 8.1955 8.20911C8.3515 8.03822 8.54137 7.90169 8.75302 7.8082C8.96468 7.71471 9.19348 7.66631 9.42486 7.6661H11.9249C12.3293 7.66212 12.7214 7.80533 13.028 8.06904C13.3346 8.33275 13.5349 8.69897 13.5915 9.09943C13.697 9.89949 13.8927 10.685 14.1749 11.4411C14.287 11.7394 14.3112 12.0635 14.2448 12.3752C14.1783 12.6868 14.0239 12.9729 13.7999 13.1994L12.7415 14.2578C13.9278 16.3441 15.6552 18.0715 17.7415 19.2578L18.7999 18.1994C19.0264 17.9754 19.3125 17.821 19.6241 17.7545C19.9358 17.688 20.2599 17.7123 20.5582 17.8244C21.3143 18.1066 22.0998 18.3022 22.8999 18.4078C23.3047 18.4649 23.6744 18.6688 23.9386 18.9807C24.2029 19.2926 24.3433 19.6907 24.3332 20.0994Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<div class="profile__details">
							<?php esc_html_e( 'Phone', 'community-portal' ); ?>
							<span class="profile__phone">
								<?php echo esc_html( $info['phone']->value ); ?>
							</span>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		if ( $info['groups']->display ) :
			$groups = groups_get_user_groups( $info['id'] );
			if ( $groups['total'] > 0 ) :
				?>
					<h2 class="profile__heading"><?php esc_html_e( 'Groups I\'m In', 'community-portal' ); ?></h2>
				<?php $group_count = 0; ?>
					<div class="profile__card profile__card--links">
					<?php foreach ( $groups['groups'] as $gid ) : ?>
							<?php
								$group        = new BP_Groups_Group( $gid );
								$group_meta   = groups_get_groupmeta( $gid, 'meta' );
								$group_hidden = '';
							if ( $group_count >= $show_minimum_items ) {
								$group_hidden = ' hidden';
							}
							?>
							<a class="profile__group<?php echo esc_html( $group_hidden ); ?>" href="<?php echo esc_attr( get_home_url( null, 'groups/' . $group->slug ) ); ?>">
								<h2 class="profile__group-title"><?php echo esc_html( str_replace( '\\', '', stripslashes( $group->name ) ) ); ?></h2>
								<?php
								if ( ( isset( $group_meta['group_city'] ) && strlen( trim( $group_meta['group_city'] ) ) > 0 ) || ( isset( $group_meta['group_country'] ) && strlen( trim( $group_meta['group_country'] ) ) > 1 ) || isset( $group_meta['group_type'] ) ) :
									?>
									<div class="profile__group-location">
										<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									<?php
									if ( isset( $group_meta['group_city'] ) && strlen( $group_meta['group_city'] ) > 0 ) {
										?>
										<span>
										<?php
											echo esc_html( $group_meta['group_city'] );
										?>
										</span>
										<?php
									}
									if ( isset( $group_meta['group_country'] ) && strlen( $group_meta['group_country'] ) > 1 ) {
										?>
										<span>
											<?php echo esc_html( $countries[ $group_meta['group_country'] ] ); ?>
										</span>
										<?php
									}
									if ( isset( $group_meta['group_type'] ) ) {
										?>
										<span class="profile__group-location__online">
											<?php
											if ( 'Online' === $group_meta['group_type'] ) {
												esc_html_e( 'Online', 'community-portal' );
											} else {
												esc_html_e( 'Offline', 'community-portal' );
											}
											?>
										</span>
										<?php
									}
									?>
									</div>
								<?php endif; ?>
								<div class="profile__group-member-count">
									<svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M12.3337 14V12.6667C12.3337 11.9594 12.0527 11.2811 11.5526 10.781C11.0525 10.281 10.3742 10 9.66699 10H4.33366C3.62641 10 2.94814 10.281 2.44804 10.781C1.94794 11.2811 1.66699 11.9594 1.66699 12.6667V14" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M6.99967 7.33333C8.47243 7.33333 9.66634 6.13943 9.66634 4.66667C9.66634 3.19391 8.47243 2 6.99967 2C5.52692 2 4.33301 3.19391 4.33301 4.66667C4.33301 6.13943 5.52692 7.33333 6.99967 7.33333Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M16.333 13.9993V12.6659C16.3326 12.0751 16.1359 11.5011 15.7739 11.0341C15.4119 10.5672 14.9051 10.2336 14.333 10.0859" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M11.667 2.08594C12.2406 2.2328 12.749 2.5664 13.1121 3.03414C13.4752 3.50188 13.6722 4.07716 13.6722 4.66927C13.6722 5.26138 13.4752 5.83666 13.1121 6.3044C12.749 6.77214 12.2406 7.10574 11.667 7.2526" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<?php echo esc_html( groups_get_total_member_count( $gid ) ); ?> <?php esc_html_e( 'Members', 'community-portal' ); ?>
								</div>
							</a>
							<?php $group_count++; ?>
							<hr class="profile__group-line<?php echo esc_html( $group_hidden ); ?>" />
					<?php endforeach; ?>
					<?php if ( $group_count >= $show_minimum_items ) : ?>
						<a href="#" class="group__events-link show-more">
									<?php esc_html__( 'View All', 'community-portal' ) . ' (' . esc_html( $group_count ) . ')'; ?><svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.33301 8.66634L5.99967 4.99967L2.33301 1.33301" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
								</a>
					<?php endif; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( $info['events_attended']->display ) : ?>
				<?php
					$event_user            = new EM_Person( $info['id'] );
					$events                = $event_user->get_bookings();
					$events_attended_count = 0;
				?>
				<?php if ( count( $events->bookings ) > 0 ) : ?>
					<h2 class="profile__heading"><?php esc_html_e( 'Recent Events', 'community-portal' ); ?></h2>
					<div class="profile__card profile__card--links">
						<?php
							$events = array_map( 'mozilla_replace_bookings_with_events', $events->bookings );
						?>
						<?php foreach ( $events as $event ) : ?>
							<?php
								$event_time  = strtotime( $event->event_start_date );
								$date_format = 'en' === $current_translation ? 'M d' : 'd M';
								$event_date  = mozilla_localize_date( $event->event_start_date, $date_format );
								$location    = em_get_location( $event->location_id );

								$event_hidden = '';
							if ( $events_attended_count >= $show_minimum_items ) {
								$event_hidden = ' hidden';
							}
							?>
							<a class="profile__event<?php echo esc_html( $event_hidden ); ?>" href="<?php echo esc_attr( get_home_url( null, 'events/' . $event->slug ) ); ?>">
								<div class="profile__event-date">
									<?php echo esc_html( $event_date ); ?>
								</div>
								<div class="profile__event-info">
									<div class="profile__event-title">
										<?php echo esc_html( $event->event_name ); ?>
									</div>
									<div class="profile__event-time">
										<?php
											$date_format = 'en' === $current_translation ? 'F d, Y ∙ H:i' : 'd F, Y ∙ H:i';
											$event_date  = mozilla_localize_date( $event->event_start_date . $event->event_start_time, $date_format );
											echo esc_html( $event_date );
										?>
									</div>
									<?php if ( ! empty( $location->location_id ) ) : ?>
										<div class="profile__event-location">
											<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
											<?php
											if ( 'OE' === $location->location_country ) {
												esc_html_e( 'Online Event', 'community-portal' );
											} elseif ( $location->location_town && $location->location_country ) {
												echo esc_html( "{$location->location_town}, {$event_countries[$location->location_country]}" );
											} elseif ( $location->location_town && ! $location->location_country ) {
												echo esc_html( "{$location->location_town}" );
											} elseif ( ! $location->location_town && $location->location_country ) {
												echo esc_html( "{$event_countries[$location->location_country]}" );
											}
											?>
										</div>
									<?php endif; ?>
								</div>
							</a>
							<?php $events_attended_count++; ?>
							<hr class="profile__group-line<?php echo esc_html( $event_hidden ); ?>" />
						<?php endforeach; ?>
						<?php if ( $events_attended_count >= $show_minimum_items ) : ?>
							<a href="#" class="group__events-link show-more">
										<?php echo esc_html__( 'View All', 'community-portal' ) . '(' . esc_html( $events_attended_count ) . ')'; ?><svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.33301 8.66634L5.99967 4.99967L2.33301 1.33301" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
									</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $info['events_organized']->display ) : ?>
				<?php
					$args                     = array(
						'owner'        => $info['id'],
						'scope'        => 'all',
						'private_only' => true,
						'pagination'   => false,
						'orderby'      => 'event_start_date',
						'order'        => 'DESC',
					);
					$private_events_organized = EM_Events::get( $args );
					$args                     = array(
						'owner'      => $info['id'],
						'scope'      => 'all',
						'private'    => false,
						'pagination' => false,
						'orderby'    => 'event_start_date',
						'order'      => 'DESC',
					);
					$events_organized         = EM_Events::get( $args );
					$events_organized         = array_unique( array_merge( $events_organized, $private_events_organized ), SORT_REGULAR );
					$events_organized_count   = 0;
					?>
				<?php if ( count( $events_organized ) > 0 ) : ?>
					<h2 class="profile__heading">
						<?php esc_html_e( 'Organized Events', 'community-portal' ); ?>
					</h2>
					<div class="profile__card profile__card--links">
						<?php foreach ( $events_organized as $event ) : ?>
							<?php
								$date_format = 'en' === $current_translation ? 'M d' : 'd M';
								$event_date  = mozilla_localize_date( $event->event_start_date, $date_format );
								$location    = em_get_location( $event->location_id );

								$event_hidden = '';
							if ( $events_organized_count >= $show_minimum_items ) {
								$event_hidden = ' hidden';
							}
							?>
							<a class="profile__event<?php echo esc_html( $event_hidden ); ?>" href="<?php echo esc_attr( get_home_url( null, 'events/' . $event->slug ) ); ?>">
								<div class="profile__event-date">
									<?php echo esc_html( $event_date ); ?>
								</div>
								<div class="profile__event-info">
									<div class="profile__event-title">
										<?php echo esc_html( $event->event_name ); ?>
									</div>
									<div class="profile__event-time">
										<?php
											$date_format = 'en' === $current_translation ? 'F d, Y ∙ H:i' : 'd F, Y ∙ H:i';
											$event_date  = mozilla_localize_date( $event->event_start_date . $event->event_start_time, $date_format );
											echo esc_html( $event_date );
										?>
									</div>
									<?php if ( ! empty( $location->location_id ) ) : ?>
										<div class="profile__event-location">
											<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
											<?php
											if ( 'OE' === $location->location_country ) {
												esc_html_e( 'Online Event', 'community-portal' );
											} elseif ( $location->location_town && $location->location_country ) {
												echo esc_html( "{$location->location_town}, {$event_countries[$location->location_country]}" );
											} elseif ( $location->location_town && ! $location->location_country ) {
												echo esc_html( "{$location->location_town}" );
											} elseif ( ! $location->location_town && $location->location_country ) {
												echo esc_html( "{$event_countries[$location->location_country]}" );
											}
											?>
										</div>
									<?php endif; ?>
								</div>
							</a>
							<?php
								$events_organized_count++;
							?>
							<hr class="profile__group-line<?php echo esc_html( $event_hidden ); ?>" />
						<?php endforeach; ?>
						<?php if ( $events_organized_count >= $show_minimum_items ) : ?>
							<a href="#" class="group__events-link show-more">
										<?php echo esc_html__( 'View All', 'community-portal' ) . ' (' . esc_html( $events_organized_count ) . ')'; ?><svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.33301 8.66634L5.99967 4.99967L2.33301 1.33301" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
									</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( $info['campaigns_participated']->display ) : ?>
				<?php
					$campaigns = get_user_meta( $user->ID, 'campaigns', true );

					$campaign_count   = 0;
					$campaign_objects = array();


				if ( is_array( $campaigns ) ) {
					foreach ( $campaigns as $cid ) {
						$object = get_post( $cid );
						if ( ! empty( $object ) ) {
							$campaign_objects[ get_field( 'campaign_start_date', $object->ID ) ] = $object;
						}
					}
				}
				ksort( $campaign_objects );
				?>
				<?php if ( count( $campaign_objects ) > 0 ) : ?>
					<h2 class="profile__heading"><?php esc_html_e( 'Campaigns Participated In', 'community-portal' ); ?></h2>
					<div class="profile__card profile__card--links">
					<?php foreach ( $campaign_objects as $campaign ) : ?>
						<?php if ( $campaign ) : ?>
							<?php
								$description   = get_field( 'card_description', $campaign->ID );
								$start         = get_field( 'campaign_start_date', $campaign->ID );
								$end           = get_field( 'campaign_end_date', $campaign->ID );
								$campaign_tags = get_the_terms( $campaign, 'post_tag' );

								$campaign_hidden = '';
							if ( $campaign_count >= $show_minimum_items ) {
								$campaign_hidden = ' hidden';
							}
							?>
							<a class="profile__campaign<?php echo esc_html( $campaign_hidden ); ?>" href="<?php echo esc_attr( get_home_url( null, 'campaigns/' . $campaign->post_name ) ); ?>">
								<h3 class="profile__campaign-title"><?php echo esc_html( $campaign->post_title ); ?></h3>
								<div class="profile__campaign-dates">
									<?php
									if ( $end ) {
										$date_format = 'en' === $current_translation ? 'F d' : 'd F';
										echo esc_html( mozilla_localize_date( $start, $date_format ) );
										?>
										-
											<?php
											echo esc_html( mozilla_localize_date( $end, $date_format . ' Y' ) );
									} else {
										$date_format = 'en' === $current_translation ? 'F d Y' : 'd F Y';
										echo esc_html( mozilla_localize_date( $start, $date_format ) );
									}
									?>
								</div>
								<div class="profile__campaign-desc">
									<?php echo esc_html( wp_strip_all_tags( $description ) ); ?>
								</div>
								<?php if ( is_array( $campaign_tags ) && count( $campaign_tags ) > 0 ) : ?>
									<div class="profile__campaign-tags">
										<?php foreach ( $campaign_tags as $ctag ) : ?>
											<span class="profile__static-tag">
												<?php echo esc_html( $ctag->name ); ?>
											</span>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</a>
							<?php $campaign_count++; ?>
							<hr class="profile__group-line<?php echo esc_html( $campaign_hidden ); ?>" />
						<?php endif; ?>
					<?php endforeach; ?>
					<?php if ( $campaign_count >= $show_minimum_items ) : ?>
						<a href="#" class="group__events-link show-more">
										<?php echo esc_html__( 'View All', 'community-portal' ) . ' (' . esc_html( $campaign_count ) . ')'; ?><svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.33301 8.66634L5.99967 4.99967L2.33301 1.33301" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
									</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</section>
	<section class="profile__section profile__section--right">
		<?php
		if (
				( $info['telegram']->display && $info['telegram']->value ) ||
				( $info['facebook']->display && $info['facebook']->value ) ||
				( $info['twitter']->display && $info['twitter']->value ) ||
				( $info['linkedin']->display && $info['linkedin']->value ) ||
				( $info['discourse']->display && $info['discourse']->value ) ||
				( $info['github']->display && $info['github']->value ) ||
				( $info['matrix']->display && $info['matrix']->value ) ||
				( $info['youtube']->display && $info['youtube']->value ) ||
				( $info['peertube']->display && $info['peertube']->value ) ||
				( $info['pixelfed']->display && $info['pixelfed']->value ) ||
				( $info['mastodon']->display && $info['mastodon']->value )
			) :
			?>
			<div class="profile__social-card profile__card--right">
			<?php esc_html_e( 'Social Handles', 'community-portal' ); ?>
				<div class="profile__social-container">
				<?php if ( $info['telegram']->value && $info['telegram']->display ) : ?>
						<a href="<?php echo mozilla_verify_url( $info['telegram']->value, true ) ? esc_attr( mozilla_verify_url( $info['telegram']->value, true ) ) : esc_attr( "https://t.me/{$info['telegram']->value}" ); ?>" class="profile__social-link">
							<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
								<path d="M24.3337 7.66602L15.167 16.8327" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M24.3337 7.66602L18.5003 24.3327L15.167 16.8327L7.66699 13.4993L24.3337 7.66602Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php esc_html_e( 'Telegram', 'community-portal' ); ?>
						</a>
					<?php endif; ?>
				<?php if ( $info['facebook']->value && $info['facebook']->display ) : ?>
						<a href="<?php print mozilla_verify_url( $info['facebook']->value, true ) ? esc_attr( mozilla_verify_url( $info['facebook']->value, true ) ) : esc_attr( "https://www.facebook.com/{$info['facebook']->value}" ); ?>" class="profile__social-link">
							<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
								<path fill-rule="evenodd" clip-rule="evenodd" d="M26 16C26 10.4771 21.5229 6 16 6C10.4771 6 6 10.4771 6 16C6 20.9913 9.65686 25.1283 14.4375 25.8785V18.8906H11.8984V16H14.4375V13.7969C14.4375 11.2906 15.9304 9.90625 18.2146 9.90625C19.3087 9.90625 20.4531 10.1016 20.4531 10.1016V12.5625H19.1921C17.9499 12.5625 17.5625 13.3333 17.5625 14.1242V16H20.3359L19.8926 18.8906H17.5625V25.8785C22.3431 25.1283 26 20.9913 26 16Z" fill="black"/>
							</svg>
							<?php esc_html_e( 'Facebook', 'community-portal' ); ?>
						</a>
					<?php endif; ?>
				<?php if ( $info['twitter']->value && $info['twitter']->display ) : ?>
						<a href="<?php print mozilla_verify_url( $info['twitter']->value, true ) ? esc_attr( mozilla_verify_url( $info['twitter']->value, true ) ) : esc_attr( "https://www.twitter.com/{$info['twitter']->value}" ); ?>" class="profile__social-link">
							<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
								<path d="M12.3766 23.9366C19.7469 23.9366 23.7781 17.8303 23.7781 12.535C23.7781 12.3616 23.7781 12.1889 23.7664 12.017C24.5506 11.4498 25.2276 10.7474 25.7656 9.94281C25.0343 10.2669 24.2585 10.4794 23.4641 10.5733C24.3006 10.0725 24.9267 9.28482 25.2258 8.35688C24.4392 8.82364 23.5786 9.15259 22.6812 9.32953C22.0771 8.6871 21.278 8.26169 20.4077 8.11915C19.5374 7.97661 18.6444 8.12487 17.8668 8.541C17.0893 8.95713 16.4706 9.61792 16.1064 10.4211C15.7422 11.2243 15.6529 12.1252 15.8523 12.9842C14.2592 12.9044 12.7006 12.4903 11.2778 11.7691C9.85506 11.0478 8.59987 10.0353 7.59375 8.7975C7.08132 9.67966 6.92438 10.724 7.15487 11.7178C7.38536 12.7116 7.98596 13.5802 8.83437 14.1467C8.19667 14.1278 7.57287 13.9558 7.01562 13.6452C7.01562 13.6616 7.01562 13.6788 7.01562 13.6959C7.01588 14.6211 7.33614 15.5177 7.9221 16.2337C8.50805 16.9496 9.32362 17.4409 10.2305 17.6241C9.64052 17.785 9.02155 17.8085 8.42109 17.6928C8.67716 18.489 9.17568 19.1853 9.84693 19.6843C10.5182 20.1832 11.3286 20.4599 12.1648 20.4756C10.7459 21.5908 8.99302 22.1962 7.18828 22.1944C6.86946 22.1938 6.55094 22.1745 6.23438 22.1366C8.0669 23.3126 10.1992 23.9363 12.3766 23.9334" fill="black"/>
							</svg>
							<?php esc_html_e( 'Twitter', 'community-portal' ); ?>
						</a>
					<?php endif; ?>
				<?php if ( $info['linkedin']->value && $info['linkedin']->display ) : ?>
						<a href="<?php print mozilla_verify_url( $info['linkedin']->value, true ) ? esc_attr( mozilla_verify_url( $info['linkedin']->value, true ) ) : esc_attr( "https://www.linkedin.com/in/{$info['linkedin']->value}" ); ?>" class="profile__social-link">
							<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
								<g transform="translate(6 6)">
									<path d="M13.333 6.66602C14.6591 6.66602 15.9309 7.1928 16.8685 8.13048C17.8062 9.06816 18.333 10.3399 18.333 11.666V17.4993H14.9997V11.666C14.9997 11.224 14.8241 10.8001 14.5115 10.4875C14.199 10.1749 13.775 9.99935 13.333 9.99935C12.891 9.99935 12.4671 10.1749 12.1545 10.4875C11.8419 10.8001 11.6663 11.224 11.6663 11.666V17.4993H8.33301V11.666C8.33301 10.3399 8.85979 9.06816 9.79747 8.13048C10.7352 7.1928 12.0069 6.66602 13.333 6.66602V6.66602Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M5.00033 7.5H1.66699V17.5H5.00033V7.5Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M3.33366 4.99935C4.25413 4.99935 5.00033 4.25316 5.00033 3.33268C5.00033 2.41221 4.25413 1.66602 3.33366 1.66602C2.41318 1.66602 1.66699 2.41221 1.66699 3.33268C1.66699 4.25316 2.41318 4.99935 3.33366 4.99935Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</g>
							</svg>
							<?php esc_html_e( 'Linkedin', 'community-portal' ); ?>
						</a>
					<?php endif; ?>
				<?php if ( $info['discourse']->value && $info['discourse']->display ) : ?>
						<a href="<?php print mozilla_verify_url( $info['discourse']->value, true ) ? esc_attr( mozilla_verify_url( $info['discourse']->value, true ) ) : esc_attr( "https://discourse.mozilla.org/u/{$info['discourse']->value}/summary" ); ?>" class="profile__social-link">
							<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
								<path d="M23.5 15.5834C23.5029 16.6832 23.2459 17.7683 22.75 18.75C22.162 19.9265 21.2581 20.916 20.1395 21.6078C19.021 22.2995 17.7319 22.6662 16.4167 22.6667C15.3168 22.6696 14.2318 22.4126 13.25 21.9167L8.5 23.5L10.0833 18.75C9.58744 17.7683 9.33047 16.6832 9.33333 15.5834C9.33384 14.2682 9.70051 12.9791 10.3923 11.8605C11.084 10.7419 12.0735 9.838 13.25 9.25002C14.2318 8.75413 15.3168 8.49716 16.4167 8.50002H16.8333C18.5703 8.59585 20.2109 9.32899 21.4409 10.5591C22.671 11.7892 23.4042 13.4297 23.5 15.1667V15.5834Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php esc_html_e( 'Discourse', 'community-portal' ); ?>
						</a>
					<?php endif; ?>
				<?php if ( $info['github']->value && $info['github']->display ) : ?>
						<a href="<?php print mozilla_verify_url( $info['github']->value, true ) ? esc_attr( mozilla_verify_url( $info['github']->value, true ) ) : esc_attr( "https://www.github.com/{$info['github']->value}" ); ?>" class="profile__social-link">
							<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
								<g clip-path="url(#clip0)">
									<path d="M13.5003 22.6669C9.33366 23.9169 9.33366 20.5835 7.66699 20.1669M19.3337 25.1669V21.9419C19.3649 21.5445 19.3112 21.145 19.1762 20.77C19.0411 20.395 18.8278 20.053 18.5503 19.7669C21.167 19.4752 23.917 18.4835 23.917 13.9335C23.9168 12.77 23.4692 11.6512 22.667 10.8085C23.0469 9.79061 23.02 8.66548 22.592 7.66686C22.592 7.66686 21.6087 7.37519 19.3337 8.90019C17.4237 8.38254 15.4103 8.38254 13.5003 8.90019C11.2253 7.37519 10.242 7.66686 10.242 7.66686C9.81397 8.66548 9.78711 9.79061 10.167 10.8085C9.35876 11.6574 8.91076 12.7864 8.91699 13.9585C8.91699 18.4752 11.667 19.4669 14.2837 19.7919C14.0095 20.0752 13.798 20.413 13.6631 20.7835C13.5281 21.1539 13.4727 21.5486 13.5003 21.9419V25.1669" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</g>
								<defs>
									<clipPath id="clip0">
										<rect width="20" height="20" fill="white" transform="translate(6 6)"/>
									</clipPath>
								</defs>
							</svg>
							<?php esc_html_e( 'Github', 'community-portal' ); ?>
						</a>
					<?php endif; ?>
				<?php if ( $info['matrix']->value && $info['matrix']->display ) : ?>
						<a href="<?php echo esc_attr( "https://matrix.to/#/@{$info['matrix']->value}" ); ?>" class="profile__social-link">
							<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
								<path d="M12.6113 12.6035L12.6729 13.4307C13.1969 12.7881 13.9056 12.4668 14.7988 12.4668C15.7513 12.4668 16.4053 12.8428 16.7607 13.5947C17.2803 12.8428 18.0208 12.4668 18.9824 12.4668C19.7845 12.4668 20.3815 12.7015 20.7734 13.1709C21.1654 13.6357 21.3613 14.3376 21.3613 15.2764V20H19.3789V15.2832C19.3789 14.8639 19.2969 14.5586 19.1328 14.3672C18.9688 14.1712 18.6794 14.0732 18.2646 14.0732C17.6722 14.0732 17.262 14.3558 17.0342 14.9209L17.041 20H15.0654V15.29C15.0654 14.8617 14.9811 14.5518 14.8125 14.3604C14.6439 14.1689 14.3568 14.0732 13.9512 14.0732C13.3906 14.0732 12.985 14.3057 12.7344 14.7705V20H10.7588V12.6035H12.6113Z" fill="black"/>
								<line x1="9" y1="9" x2="6" y2="9" stroke="black" stroke-width="2"/>
								<line x1="26" y1="9" x2="23" y2="9" stroke="black" stroke-width="2"/>
								<line x1="9" y1="24" x2="6" y2="24" stroke="black" stroke-width="2"/>
								<line x1="26" y1="24" x2="23" y2="24" stroke="black" stroke-width="2"/>
								<line x1="7" y1="9" x2="7" y2="23" stroke="black" stroke-width="2"/>
								<line x1="25" y1="9" x2="25" y2="23" stroke="black" stroke-width="2"/>
							</svg>
							<?php esc_html_e( 'Matrix', 'community-portal' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $info['mastodon']->value && $info['mastodon']->display ) : ?>
						<a href="<?php echo esc_attr( $info['mastodon']->value ); ?>" class="profile__social-link">
							<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
								width="32" height="32" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512;" xml:space="preserve">
							<g><circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
								<path
							d="m 26.26337,12.22215 c 0,-4.7727884 -3.129697,-6.1713716 -3.129697,-6.1713716 -3.071016,-1.4083636 -11.222898,-1.3936931 -14.2645722,0 0,0 -3.1296968,1.3985832 -3.1296968,6.1713716 0,5.682356 -0.3227499,12.738844 5.18845,14.196109 1.990293,0.523247 3.696957,0.64061 5.075977,0.557478 2.493978,-0.136924 3.897452,-0.890008 3.897452,-0.890008 l -0.08313,-1.814246 c 0,0 -1.784906,0.557477 -3.789866,0.493905 -1.985402,-0.06846 -4.078388,-0.215167 -4.401138,-2.650463 -0.02933,-0.224944 -0.04401,-0.454784 -0.04401,-0.684619 4.205531,1.026931 7.790012,0.445003 8.777822,0.32764 2.758045,-0.32764 5.15911,-2.029413 5.462299,-3.579591 0.479235,-2.430405 0.440114,-5.956205 0.440113,-5.956205 z m -3.692065,6.151811 h -2.28859 v -5.609003 c 0,-2.440187 -3.144367,-2.5331 -3.144367,0.337421 v 3.071013 h -2.273921 v -3.071013 c 0,-2.875411 -3.144368,-2.777608 -3.144368,-0.337421 v 5.609003 H 9.4265781 c 0,-5.995326 -0.2542879,-7.261876 0.9046779,-8.5968873 1.271439,-1.418144 3.921903,-1.5110569 5.100428,0.2982993 l 0.567258,0.95847 0.567258,-0.95847 c 1.183416,-1.8240265 3.838769,-1.7066629 5.100427,-0.2982993 1.168748,1.3399023 0.904678,2.6015613 0.904678,8.5968873 z"
							id="path2"
							style="stroke-width:0.0489015" />
							</g>
							</svg>
							<?php esc_html_e( 'Mastodon', 'community-portal' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $info['youtube']->value && $info['youtube']->display ) : ?>
						<a href="<?php echo esc_attr( $info['youtube']->value ); ?>" class="profile__social-link">
							<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
								<g
								id="g5"
								transform="matrix(0.18122353,0,0,0.18122353,4.9996855,8.298)"><path
								class="st0"
								d="M 118.9,13.3 C 117.5,8.1 113.4,4 108.2,2.6 98.7,0 60.7,0 60.7,0 60.7,0 22.7,0 13.2,2.5 8.1,3.9 3.9,8.1 2.5,13.3 0,22.8 0,42.5 0,42.5 0,42.5 0,62.3 2.5,71.7 3.9,76.9 8,81 13.2,82.4 22.8,85 60.7,85 60.7,85 c 0,0 38,0 47.5,-2.5 5.2,-1.4 9.3,-5.5 10.7,-10.7 2.5,-9.5 2.5,-29.2 2.5,-29.2 0,0 0.1,-19.8 -2.5,-29.3 z"
								id="path7"
								inkscape:connector-curvature="0"
								style="fill:#000000;fill-opacity:1" /><polygon
								class="st1"
								points="80.2,42.5 48.6,24.3 48.6,60.7 "
								id="polygon9"
								style="fill:#ffffff" /></g>
							</svg>
							<?php esc_html_e( 'Youtube', 'community-portal' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $info['peertube']->value && $info['peertube']->display ) : ?>
						<a href="<?php echo esc_attr( $info['peertube']->value ); ?>" class="profile__social-link">
							<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
								<g
									stroke-width="32"
									id="g8"
									transform="matrix(0.03222461,0,0,0.03222461,-80.446184,34.356942)">
									<path
									d="m 2799,-911 v 341.344 l 256,-170.656"
									fill="#211f20"
									id="path2" />
									<path
									d="m 2799,-569.656 v 341.344 l 256,-170.656"
									fill="#737373"
									id="path4" />
									<path
									d="M 3055,-740.344 V -399 l 256,-170.656"
									fill="#f1680d"
									id="path6"
									style="fill:#000000;fill-opacity:1" />
								</g>
							</svg>
							<?php esc_html_e( 'PeerTube', 'community-portal' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $info['pixelfed']->value && $info['pixelfed']->display ) : ?>
						<a href="<?php echo esc_attr( $info['pixelfed']->value ); ?>" class="profile__social-link">
							<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
								<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" transform="matrix(0.44897959,0,0,0.44897959,4.7755103,4.7755103)">
								<g id="icon-copy-6" fill="#000000">
									<path
										d="M 26.198975,16.165416 C 26.119137,15.671725 25.997071,15.183495 25.832778,14.706495 25.40327,13.457749 24.707155,12.326751 23.768817,11.388212 L 18.820091,6.4384255 C 18.628605,6.2468992 18.425398,6.0651186 18.205958,5.8873902 18.945732,2.7779446 21.749367,0.5 25.005686,0.5 c 3.429843,0 6.357469,2.5271874 6.900166,5.8913433 0.06514,0.4048724 0.09821,0.7640171 0.09821,1.1085317 v 6.999875 c 0,0.878027 -0.168989,1.736963 -0.49248,2.549113 -1.067757,-0.56365 -2.289008,-0.883447 -3.586368,-0.883447 z M 23.8074,31.046185 c -0.223984,1.41741 -0.100236,2.878591 0.371194,4.24732 0.429509,1.248746 1.125623,2.379744 2.063962,3.318283 l 4.949627,4.950686 c 0.191175,0.190661 0.394025,0.371879 0.613359,0.549601 C 31.065962,47.221794 28.262192,49.5 25.005686,49.5 c -3.429897,0 -6.357563,-2.527268 -6.900192,-5.891505 -0.06511,-0.404615 -0.09818,-0.76381 -0.09818,-1.10837 V 35.50025 c 0,-0.0064 9e-6,-0.01282 2.7e-5,-0.01923 l 4.59827,-4.434834 z m 11.33941,-5.168387 c 0.05003,-0.01624 0.09995,-0.03293 0.149739,-0.05009 1.250035,-0.431139 2.3801,-1.126732 3.317571,-2.064404 l 4.948727,-4.949787 c 0.191514,-0.191555 0.373282,-0.394839 0.550998,-0.614365 C 47.222277,18.938379 49.5,21.74313 49.5,25.000437 c 0,3.430633 -2.526727,6.358926 -5.890243,6.901671 -0.404528,0.06512 -0.763646,0.0982 -1.108132,0.0982 h -6.998376 c -1.056748,0 -2.085836,-0.244942 -3.038961,-0.709549 -0.505743,-0.246519 -0.977588,-0.551953 -1.406585,-0.90735 1.934087,-0.862289 3.433975,-2.498464 4.089107,-4.505615 z m -2.164945,-7.815834 c 0.505054,-1.12035 0.77179,-2.325631 0.77179,-3.562214 V 7.499875 c 0,-0.2702157 -0.01523,-0.5419946 -0.04484,-0.8235787 2.721205,-1.6753333 6.314594,-1.3031901 8.616831,0.999495 2.425304,2.4266407 2.70907,6.2839547 0.714311,9.0459157 -0.238057,0.330416 -0.469145,0.60835 -0.714262,0.85352 l -4.948726,4.949786 c -0.548388,0.548506 -1.180179,0.993408 -1.874291,1.328019 0.0027,-0.08208 0.0041,-0.164499 0.0041,-0.247231 0,-2.202519 -0.975177,-4.181514 -2.524922,-5.543837 z m -3.863787,12.892655 c 0.731501,0.784141 1.604938,1.434093 2.579733,1.909246 1.188286,0.579239 2.478846,0.886416 3.805438,0.886416 h 6.998376 c 0.270193,0 0.54195,-0.01524 0.823514,-0.04486 1.675415,2.721432 1.303125,6.315677 -0.999442,8.618738 -2.424892,2.425411 -6.282214,2.709064 -9.044062,0.714318 -0.330212,-0.238011 -0.608087,-0.469149 -0.853203,-0.714318 L 27.479705,37.374373 C 26.733064,36.627572 26.178435,35.72613 25.832696,34.723272 25.42472,33.538775 25.341877,32.266466 25.584207,31.046185 h 2.341007 c 0.405859,0 0.80427,-0.0313 1.192864,-0.09157 z M 18.036109,17.005126 C 16.949856,16.519651 15.750783,16.250594 14.496751,16.250594 H 7.4983754 c -0.2663464,0 -0.5341903,0.01474 -0.8120522,0.04341 C 5.0111787,13.5721 5.3832109,9.9787981 7.6856753,7.6758403 10.110567,5.2504289 13.967889,4.9667757 16.729737,6.9615215 c 0.330213,0.2380116 0.608087,0.4691494 0.853204,0.7143188 l 4.948671,4.9506067 c 0.746683,0.746843 1.301306,1.648266 1.647046,2.651102 0.100386,0.291311 0.181087,0.587963 0.242103,0.887867 h -3.785522 c -0.973366,0 -1.872472,0.312024 -2.59913,0.83971 z m -1.774854,18.2486 c -0.0024,0.08203 -0.0035,0.164205 -0.0035,0.246524 v 6.999875 c 0,0.270216 0.01523,0.541995 0.04484,0.823579 C 13.581331,44.999049 9.9879108,44.626888 7.6856753,42.32416 5.2607836,39.898748 4.9771911,36.0406 6.9715096,33.27816 7.2094701,32.947876 7.4405584,32.669943 7.6856753,32.424773 l 4.9487257,-4.949786 c 0.746641,-0.746801 1.64789,-1.30155 2.650534,-1.647363 0.31995,-0.110247 0.64631,-0.196757 0.97632,-0.259529 z m 0,-11.460596 c -0.523494,0.07929 -1.041261,0.205968 -1.546431,0.380038 -1.250035,0.431139 -2.380101,1.126732 -3.317572,2.064404 L 6.4485257,31.187359 C 6.2539281,31.381998 6.0693935,31.588746 5.8889474,31.812338 2.7803249,31.074168 0.5,28.262233 0.5,25.000437 0.5,21.566274 3.0317905,18.636033 6.4096121,18.09742 6.5515625,18.07324 7.2396907,18.00056 7.4983754,18.00056 h 6.9983756 c 0.803206,0 1.5786,0.13853 2.302175,0.393784 -0.342735,0.612344 -0.537671,1.315631 -0.537671,2.0636 z" id="Combined-Shape" />
								</g>
							</g>
							</svg>
							<?php esc_html_e( 'Pixelfed', 'community-portal' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( $info['languages']->value && $info['languages']->display ) : ?>
			<div class="profile__languages-card profile__card--right">
				<?php esc_html_e( 'Languages spoken', 'community-portal' ); ?>
				<div class="profile__languages-container">
					<?php
						$languages_spoken = count( $info['languages']->value );
						$index            = 0;
					?>
					<?php foreach ( $info['languages']->value as $code ) : ?>
						<?php $index++; ?>
						<span>
							<a href="<?php echo esc_attr( add_query_arg( array( 'language' => $code ), get_home_url( null, 'people' ) ) ); ?>" class="profile__languages-link">
								<?php echo esc_html( $languages[ $code ] ); ?>
							</a>
							<?php
							if ( $index < $languages_spoken ) {
								echo esc_html( ',' );
							}
							?>
						</span>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( $info['tags']->value && $info['tags']->display ) { ?>
			<div class="profile__tags-card profile__card--right">
				<?php esc_html_e( 'Tags', 'community-portal' ); ?>
				<div class="profile__tags-container">
				<?php
					$tags = array_filter( explode( ',', $info['tags']->value ) );
				foreach ( $tags as $loop_tag ) {
						$found = false;
						$temp_name = '';
						$exist = get_term_by( 'slug', $loop_tag, 'post_tag' );
					if ( ! is_bool( $exist ) ) {
							$found = true;
							$temp_name = $exist->name;
					}
					?>
						<?php if ( $found ) { ?>
							<a href="<?php echo esc_url_raw( add_query_arg( array( 'tag' => $loop_tag ), get_home_url( null, 'people' ) ) ); ?>" class="profile__static-tag">
								<span>
									<?php echo esc_html( $temp_name ); ?>
								</span>
							</a>
							<?php
						}
				}
				?>
				</div>
			</div>
		<?php } ?>
	</section>
</div>
