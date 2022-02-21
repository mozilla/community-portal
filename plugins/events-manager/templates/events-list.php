<?php
/**
 * Events List
 *
 * Iterate through events page for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php
	$countries    = em_get_countries();
	$current_page = isset( $_REQUEST['pno'] ) ? intval( sanitize_key( $_REQUEST['pno'] ) ) : 1;
	$args         = apply_filters( 'em_content_events_args', $args );

if (
	isset( $args['search'] ) &&
		( strpos( $args['search'], '"' ) !== false ||
		strpos( $args['search'], "'" ) !== false ||
		strpos( $args['search'], '\\' ) !== false )
	) {
	$args['search']  = preg_replace( '/^\"|\"$|^\'|\'$/', '', $args['search'] );
	$original_search = $args['search'];
	$args['search']  = addslashes( $args['search'] );
} elseif ( isset( $args['search'] ) ) {
	$original_search = $args['search'];
}
	$view = htmlspecialchars( get_query_var( 'view', $default = '' ), ENT_QUOTES, 'UTF-8' );

	$country   = urldecode( htmlspecialchars( urldecode( get_query_var( 'country', $default = 'all' ) ), ENT_QUOTES, 'UTF-8' ) );
	$event_tag = urldecode( htmlspecialchars( get_query_var( 'tag', $default = 'all' ), ENT_QUOTES, 'UTF-8' ) );

	$args['scope'] = 'future';
switch ( strtolower( trim( $view ) ) ) {
	case 'past':
		$args['scope'] = 'past';
		$args['order'] = 'desc';
		break;
	case 'organized':
		if ( is_user_logged_in() ) {
			$user_id        = get_current_user_id();
			$args['scope']  = 'all';
			$args['owner']  = $user_id;
			$args['status'] = false;
		}
		break;
	case 'attending':
		$args['scope']    = 'all';
		$args['bookings'] = 'user';
		break;
}

if ( 'all' !== strtolower( $country ) ) {
	$args['country'] = $countries[ $country ];
}

if ( 'all' !== $event_tag ) {
	$current_translation = mozilla_get_current_translation();
	if ( $current_translation && stripos( $event_tag, '_' . $current_translation ) !== false ) {
		$event_tag = substr( $event_tag, 0, stripos( $event_tag, '_' . $current_translation ) );
	}
	$args['category'] = $event_tag;
}

if ( isset( $args['tag'] ) ) {
	unset( $args['tag'] );
}

	$args['limit']    = '0';
	$all_events       = EM_Events::get( $args );
	$events           = array();
	$initiative_input = isset( $_GET['initiative'] ) ? sanitize_text_field( wp_unslash( $_GET['initiative'] ) ) : null;
	$language_input   = isset( $_GET['language'] ) ? sanitize_text_field( wp_unslash( $_GET['language'] ) ) : null;
	$event_initiative = isset( $initiative_input ) && strlen( $initiative_input ) > 0 && strtolower( $initiative_input ) !== 'all' ? $initiative_input : false;
	$event_language   = isset( $language_input ) && strlen( $language_input ) > 0 && strtolower( $language_input ) !== 'all' ? $language_input : false;
if ( $event_initiative || $event_language ) {
	foreach ( $all_events as $e ) {
		$event_meta = get_post_meta( $e->post_id, 'event-meta' );
		if ( $event_initiative && $event_language ) {
			if (
				( isset( $event_meta[0]->initiative ) && intval( $event_meta[0]->initiative ) === intval( $event_initiative ) ) &&
				( isset( $event_meta[0]->language ) && strtolower( $event_meta[0]->language ) === strtolower( $event_language ) )
			) {
				$events[] = $e;
			}
		} elseif ( $event_initiative ) {
			if ( isset( $event_meta[0]->initiative ) && intval( $event_meta[0]->initiative ) === intval( $event_initiative ) ) {
				$events[] = $e;
			}
		} else {
			if ( isset( $event_meta[0]->language ) && strtolower( $event_meta[0]->language ) === strtolower( $event_language ) ) {
				$events[] = $e;
			}
		}
	}
} else {
	$events = $all_events;
}

	$events_per_page = 12;
	$offset          = ( $current_page - 1 ) * $events_per_page;

	$event_count = count( $events );
	$events      = array_slice( $events, $offset, $events_per_page );
	$total_pages = ceil( $event_count / $events_per_page );

?>

<div class="row events">
	<div class="events__nav__container">
		<ul class="col-md-12 center-md events__nav">
			<li class="events__nav__item">
				<a
				class="events__nav__link
				<?php
				if ( 'future' === $view | '' === $view ) {
					echo esc_attr( 'events__nav__link--active' );}
				?>
					"
				href="
				<?php
				echo esc_url_raw(
					add_query_arg(
						array(
							'view'    => 'future',
							'country' => $country,
							'tag'     => $event_tag,
						),
						get_home_url( '', 'events' )
					)
				);
				?>
				"
				>
					<?php esc_html_e( 'Upcoming Events', 'community-portal' ); ?>
				</a>
			</li>
			<?php
				$logged_in = is_user_logged_in();
			?>
			<?php if ( $logged_in ) : ?>
			<li class="events__nav__item">
				<a
				class="events__nav__link
				<?php
				if ( 'attending' === $view ) {
					echo esc_attr( 'events__nav__link--active' );}
				?>
					"
				href="
				<?php
				echo esc_url_raw(
					add_query_arg(
						array(
							'view'    => 'attending',
							'country' => $country,
							'tag'     => $event_tag,
						),
						get_home_url( null, 'events' )
					)
				);
				?>
				"
				>
					<?php esc_html_e( 'Events I\'m attending', 'community-portal' ); ?>
				</a>
			</li>
			<li class="events__nav__item">
				<a
				class="events__nav__link
				<?php
				if ( 'organized' === $view ) {
					echo esc_attr( 'events__nav__link--active' );}
				?>
					"
				href="
				<?php
				echo esc_url_raw(
					add_query_arg(
						array(
							'view'    => 'organized',
							'country' => $country,
							'tag'     => $event_tag,
						),
						get_home_url( null, 'events' )
					)
				);
				?>
				"
				>
					<?php esc_html_e( 'My Events', 'community-portal' ); ?>
				</a>
			</li>
			<?php endif; ?>
			<li class="events__nav__item">
				<a
				class="events__nav__link
				<?php
				if ( 'past' === $view ) {
					echo esc_attr( 'events__nav__link--active' );}
				?>
					"
				href="
				<?php
				echo esc_url_raw(
					add_query_arg(
						array(
							'view'    => 'past',
							'country' => $country,
							'tag'     => $event_tag,
						),
						get_home_url( '', 'events' ),
						get_home_url( '', 'events' )
					)
				);
				?>
				"
				>
					<?php esc_html_e( 'Past events', 'community-portal' ); ?>
				</a>
			</li>
		</ul>
		<form class="events__nav--mobile" action="">
			<label class="events__nav__label--mobile" for="eventsView"><?php esc_html_e( 'Showing:', 'community-portal' ); ?></label>
			<select class="events__nav__options--mobile" name="eventsView" id="eventsView">
				<option
				<?php
				if ( 'future' === $view || '' === $view ) {
					echo esc_attr( 'selected' );}
				?>
					value="future"><?php esc_html_e( 'Upcoming Events', 'community-portal' ); ?></option>
				<?php
				if ( $logged_in ) :
					?>
					<option
					<?php
					if ( 'attending' === $view ) {
						echo esc_attr( 'selected' );}
					?>
					value="attending"><?php esc_html_e( 'Events I\'m Attending', 'community-portal' ); ?></option><?php endif; ?>
				<?php
				if ( $logged_in ) :
					?>
					<option
					<?php
					if ( 'organized' === $view ) {
						echo esc_attr( 'selected' );}
					?>
					value="organized"><?php esc_html_e( 'Events I\'ve Organized', 'community-portal' ); ?></option><?php endif; ?>
				<option
				<?php
				if ( 'past' === $view ) {
					echo esc_attr( 'selected' );}
				?>
				value="past"><?php esc_html_e( 'Past Events', 'community-portal' ); ?></option>
			</select>
			<svg class="events__nav__icon" width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M1.5 3.5L7 9L12.5 3.5" fill="white"/>
				<path d="M1.5 3.5L7 9L12.5 3.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</form>
	</div>
	<?php require locate_template( 'plugins/events-manager/templates/template-parts/events-filters.php', false, false ); ?>
	<?php if ( count( $events ) ) : ?>
		<?php if ( isset( $original_search ) ) : ?>
		<div class="col-sm-12 events__search-terms">
			<p>
			<?php
			esc_html_e( 'Results for ', 'community-portal' );
			echo esc_html( $original_search );
			?>
			</p>
		</div>
	<?php endif; ?>
	<div class="row events__cards">
		<?php
		foreach ( $events as $event ) {
			if ( isset( $event->event_name ) && strlen( $event->event_name ) > 0 ) {
				include locate_template( 'plugins/events-manager/templates/template-parts/event-cards.php', false, false );
			}
		}
		?>
		<?php
		$range = ( $current_page > 3 ) ? 3 : 5;

		if ( $current_page > $total_pages - 2 ) {
			$range = 5;
		}

		$previous_page = ( $current_page > 1 ) ? $current_page - 1 : 1;
		$next_page     = ( $current_page <= $total_pages ) ? $current_page + 1 : $total_pages;

		if ( $total_pages > 1 ) {
			$range_min = ( 0 === $range % 2 ) ? ( $range / 2 ) - 1 : ( $range - 1 ) / 2;
			$range_max = ( 0 === $range % 2 ) ? $range_min + 1 : $range_min;

			$page_min = $current_page - $range_min;
			$page_max = $current_page + $range_max;

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
			<?php
				if ( $total_pages > 1 ) {
					$url = '';
					if ( 'all' !== $country && $country ) {
						$url = '&country=' . esc_attr( $country );
					}

					if ( $event_tag && 'all' !== $event_tag ) {
						$url = $url . '&tag=' . esc_attr( $event_tag );
					}

					if ( $event_initiative && 'all' !== strtolower( $event_initiative ) ) {
						$url = $url . '&initiative=' . esc_attr( htmlspecialchars( $initiative_input, ENT_QUOTES, 'UTF-8' ) );
					}

					if ( $event_language && 'all' !== $event_language ) {
						$url = $url . '&language=' . esc_attr( htmlspecialchars( $event_language, ENT_QUOTES, 'UTF-8' ) );
					}

					if ( $event_language && 'all' !== $event_language ) {
						$url = $url . '&view=' .  esc_attr( trim( $view ) );
					}
			?>
			<a href="/events/?pno=<?php print esc_attr( $previous_page ) . esc_attr( $url ); ?>" class="campaigns__pagination-link campaigns__pagination-link--arrow">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<path d="M17 23L6 12L17 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</a>
			<?php if ( $page_min > 1 ) : ?>
				<a href="/events/?pno=1<?php print $url; ?>"
					class="campaigns__pagination-link campaigns__pagination-link--first"><?php print esc_html( '1' ); ?>
				</a>
					&hellip;
					<?php endif; ?>
				<?php for ( $x = $page_min - 1; $x < $page_max; $x++ ) : ?>
			<a href="/events/?pno=<?php print esc_attr( $x + 1 ) . $url; ?>"
				class="campaigns__pagination-link
					<?php
					if ( $current_page === $x + 1 ) :
						?>
						campaigns__pagination-link--active<?php endif; ?>
					<?php
					if ( $x === $page_max - 1 ) :
						?>
						campaigns__pagination-link--last<?php endif; ?>"><?php print esc_attr( $x + 1 ); ?>
			</a>
			<?php endfor; ?>
				<?php
				if ( $total_pages > $range && $current_page < $total_pages - 1 ) :
					?>
					&hellip;

				<?php
					$url = '';
					if ( 'all' !== $country && $country ) {
						$url = '&country=' . esc_attr( $country );
					}

					if ( $event_tag && 'all' !== $event_tag ) {
						$url = $url . '&tag=' . esc_attr( $event_tag );
					}

					if ( $event_initiative && 'all' !== $event_initiative ) {
						$url = $url . '&initiative=' . esc_attr( htmlspecialchars( $initiative, ENT_QUOTES, 'UTF-8' ) );
					}

					if ( $event_language && 'all' !== $event_language ) {
						$url = $url . '&language=' . esc_attr( htmlspecialchars( $event_language, ENT_QUOTES, 'UTF-8' ) );
					}

					if ( strlen( $view ) > 0 ) {
						$url = $url . '&view=' .  esc_attr( trim( $view ) );
					}
				?>
				<a href="/events/?pno=<?php print esc_attr( $total_pages ) . $url; ?>"
					class="campaigns__pagination-link
					<?php
					if ( $current_page === $total_pages ) :
						?>
					campaigns__pagination-link--active<?php endif; ?>"><?php print esc_attr( $total_pages ); ?>
				</a>
			<?php endif; ?>
			<a href="/events/?pno=<?php print esc_attr( $next_page ) . esc_attr( $url ); ?>" class="campaigns__pagination-link campaigns__pagination-link--arrow">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
				<path d="M7 23L18 12L7 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
			</a>
			<?php } ?>
		</div>
	</div>
	<?php else : ?>
		<div class="events__zero-state col-sm-12">
			<p>
			<?php
			if ( isset( $original_search ) ) {
				esc_html_e( 'No results found. Please try another search term.', 'community-portal' );
			} else {
				esc_html_e( 'There are currently no events.', 'community-portal' );
			}
			?>
			</p>
		</div>
	<?php endif; ?>
	</div>
</div>
