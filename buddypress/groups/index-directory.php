<?php
/**
 * Group listing page
 *
 * Group listing page for community portal
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

	// Override the buddypress group listing page template!
	session_start();
	// Main header template!
	get_header();

	$template_dir = get_template_directory();

	require "{$template_dir}/languages.php";
	require "{$theme_directory}/countries.php";

	// Execute actions by buddypress!
	do_action( 'bp_before_directory_groups_page' );
	do_action( 'bp_before_directory_groups' );
	$logged_in = mozilla_is_logged_in();

	$groups_per_page = 12;
	$p               = ( isset( $_GET['page'] ) ) ? intval( $_GET['page'] ) : 1;
	$args            = array(
		'per_page' => -1,
	);

	if ( isset( $_GET['q'] ) ) {
		$q = trim( sanitize_text_field( wp_unslash( $_GET['q'] ) ) );
		if ( strlen( $q ) <= 0 ) {
			$q = false;
		}
	} else {
		$q = false;
	}

	$mygroups     = isset( $_GET['mygroups'] ) ? htmlspecialchars( sanitize_text_field( wp_unslash( $_GET['mygroups'] ) ), ENT_QUOTES, 'UTF-8' ) : '';
	$location     = isset( $_GET['country'] ) ? htmlspecialchars( sanitize_text_field( wp_unslash( $_GET['country'] ) ), ENT_QUOTES, 'UTF-8' ) : '';
	$get_language = isset( $_GET['language'] ) ? htmlspecialchars( sanitize_text_field( wp_unslash( $_GET['language'] ) ), ENT_QUOTES, 'UTF-8' ) : '';
	$get_tag      = isset( $_GET['tag'] ) ? htmlspecialchars( sanitize_text_field( wp_unslash( $_GET['tag'] ), ENT_QUOTES, 'UTF-8' ) ) : '';

	$current_translation = mozilla_get_current_translation();

	if ( $q ) {
		if (
			strpos( $q, '"' ) !== false ||
			strpos( $q, "'" ) !== false ||
			strpos( $q, '\\' ) !== false
		) {
			$q              = stripslashes( $q );
			$q              = preg_replace( '/^\"|\"$|^\'|\'$/', '', $q );
			$original_query = $q;
			$q              = addslashes( $q );
		} else {
			$original_query = $q;
		}

		$args['search_columns'] = array( 'name' );
		$args['search_terms']   = $q;
	}

	$group_count = 0;
	$user        = wp_get_current_user()->data;

	if ( $logged_in && isset( $_GET['mygroups'] ) && 'true' === $_GET['mygroups'] ) {

		$groups          = array();
		$args['user_id'] = $user->ID;
		$groups          = groups_get_groups( $args );

	} else {
		if ( $q ) {
			$args['search_columns'] = array( 'name' );
			$args['search_terms']   = $q;
		}

		$groups = groups_get_groups( $args );
	}

	$groups                = $groups['groups'];
	$filtered_groups       = array();
	$countries_with_groups = array();
	$languages_with_groups = array();
	$used_country_list     = array();
	$used_language_list    = array();


	foreach ( $groups as $group ) {
		$meta        = groups_get_groupmeta( $group->id, 'meta' );
		$group->meta = $meta;

		if ( isset( $meta['group_country'] ) && strlen( $meta['group_country'] ) > 1 ) {
			$countries_with_groups[] = $meta['group_country'];
		}

		if ( isset( $meta['group_language'] ) && strlen( $meta['group_language'] ) > 0 ) {
			$languages_with_groups[] = $meta['group_language'];
		}

		if ( isset( $_GET['tag'] ) && strlen( $get_tag ) > 0
			&& isset( $_GET['country'] ) && strlen( $location ) > 0
			&& isset( $_GET['country'] ) && strlen( $get_language ) > 0 ) {

			if ( isset( $meta['group_language'] ) ) {
				if ( in_array( strtolower( trim( $get_tag ) ), array_map( 'strtolower', $meta['group_tags'] ), true )
					&& trim( strtolower( $location ) ) === strtolower( $meta['group_country'] )
					&& trim( strtolower( $get_language ) ) === strtolower( $meta['group_language'] ) ) {
					$filtered_groups[] = $group;
					continue;
				}
			}
		} elseif ( ( ! isset( $_GET['tag'] ) || 0 === strlen( $get_tag ) )
			&& isset( $_GET['country'] ) && strlen( $location ) > 0
			&& isset( $_GET['language'] ) && strlen( $get_language ) > 0 ) {

			if ( isset( $meta['group_language'] ) ) {
				if ( trim( strtolower( $location ) ) === strtolower( $meta['group_country'] )
				&& trim( strtolower( $get_language ) ) === strtolower( $meta['group_language'] ) ) {
					$filtered_groups[] = $group;
					continue;
				}
			}
		} elseif ( isset( $_GET['tag'] ) && strlen( $get_tag ) > 0
			&& ( ! isset( $_GET['country'] ) || 0 === strlen( $location ) )
			&& ( ! isset( $_GET['language'] ) || 0 === strlen( $get_language ) ) ) {

			if ( in_array( strtolower( trim( $get_tag ) ), array_map( 'strtolower', $meta['group_tags'] ), true ) ) {
				$filtered_groups[] = $group;
				continue;
			}
		} elseif ( isset( $_GET['tag'] ) && strlen( $get_tag ) > 0
			&& ( ! isset( $_GET['country'] ) || strlen( $location ) === 0 )
			&& isset( $_GET['language'] ) && strlen( $get_language ) > 0 ) {

			if ( isset( $meta['group_language'] ) ) {
				if ( in_array( strtolower( trim( $get_tag ) ), array_map( 'strtolower', $meta['group_tags'] ), true )
				&& trim( strtolower( $location ) ) === strtolower( $meta['group_language'] ) ) {
					$filtered_groups[] = $group;
					continue;
				}
			}
		} elseif ( isset( $_GET['country'] ) && strlen( $location ) > 0
			&& ( ! isset( $_GET['language'] ) || 0 === strlen( $get_language ) )
			&& ( ! isset( $_GET['tag'] ) || 0 === strlen( $get_tag ) )
			) {
			if ( trim( strtolower( $location ) ) === strtolower( $meta['group_country'] ) ) {
				$filtered_groups[] = $group;
				continue;
			}
		} elseif ( isset( $_GET['language'] ) && strlen( $get_language ) > 0
			&& ( ! isset( $_GET['country'] ) || 0 === strlen( $location ) )
			&& ( ! isset( $_GET['tag'] ) || 0 === strlen( $get_tag ) )
		) {
			if ( isset( $meta['group_language'] ) ) {
				if ( trim( strtolower( $get_language ) ) === strtolower( $meta['group_language'] ) ) {
					$filtered_groups[] = $group;
					continue;
				}
			}
		} else {
			$filtered_groups[] = $group;
		}
	}

	$country_code_with_groups  = array_unique( $countries_with_groups );
	$language_code_with_groups = array_unique( $languages_with_groups );

	foreach ( $country_code_with_groups as $code ) {
		$used_country_list[ $code ] = $countries[ $code ];
	}

	foreach ( $language_code_with_groups as $code ) {
		if ( ! empty( $languages[ $code ] ) ) {
			$used_language_list[ $code ] = $languages[ $code ];
		}
	}

	asort( $used_language_list );
	asort( $used_country_list );

	$filtered_groups   = array_unique( $filtered_groups, SORT_REGULAR );
	$verified_groups   = array();
	$unverified_groups = array();

	foreach ( $filtered_groups as $g ) {
		if ( 'public' === $g->status ) {
			$verified_groups[] = $g;
		} else {
			$unverified_groups[] = $g;
		}
	}

	// Only Randomize on first page!
	if ( 1 === $p ) {
		unset( $_SESSION['verified_groups'] );
		unset( $_SESSION['unverified_groups'] );

		shuffle( $verified_groups );
		shuffle( $unverified_groups );

		$_SESSION['verified_groups']   = $verified_groups;
		$_SESSION['unverified_groups'] = $unverified_groups;
	} else {

		if ( isset( $_SESSION['verified_groups'] ) ) {
			$verified_groups = $_SESSION['verified_groups'];
		}

		if ( isset( $_SESSION['unverified_groups'] ) ) {
			$unverified_groups = $_SESSION['unverified_groups'];
		}
	}

	$filtered_groups = array_merge( $verified_groups, $unverified_groups );

	$group_count = count( $filtered_groups );
	$offset      = ( $p - 1 ) * $groups_per_page;

	$groups = array_slice( $filtered_groups, $offset, $groups_per_page );


	$total_pages = ceil( $group_count / $groups_per_page );
	$tags        = get_tags( array( 'hide_empty' => false ) );

	?>
<div class="content">
	<?php do_action( 'bp_before_directory_groups_content' ); ?>
	<div class="groups">
		<div class="groups__hero">
			<div class="groups__hero-container">
				<h1 class="groups__title"><?php echo esc_html_e( 'Groups', 'community-portal' ); ?></h1>
				<p class="groups__hero-copy">
					<?php echo esc_html_e( 'Meet up with people who share your passion and join the movement for an open internet.', 'community-portal' ); ?>
				</p>
				<p class="groups__hero-copy">
					<?php echo esc_html_e( 'Look for groups in your area, or', 'community-portal' ); ?> <a href="
				<?php
				if ( $current_translation ) :
					?>
					<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/create/step/group-details/" class="groups__hero-link"><?php echo esc_html_e( 'create your own.', 'community-portal' ); ?></a>
					<svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M2.33337 8.66634L6.00004 4.99967L2.33337 1.33301" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</p>
				<div class="groups__search-container">
					<form method="GET" action="
					<?php
					if ( $current_translation ) :
						?>
						<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/" class="groups__form" id="group-search-form">
						<?php
						if ( isset( $_GET['tag'] ) && strlen( $get_tag ) > 0 ) {
							$get_tag = trim( $get_tag );
						}
						?>
						<input type="hidden" value="<?php echo esc_attr( $get_tag ); ?>" name="tag" id="group-tag" />
						<?php
						if ( isset( $_GET['country'] ) && strlen( $location ) > 0 ) {
							$location = trim( $location );
						}
						?>
						<input type="hidden" value="<?php echo esc_attr( $location ); ?>" name="country" id="group-location" />
						<?php
						if ( isset( $_GET['language'] ) && strlen( $get_language ) > 0 ) {
							$get_language = trim( $get_language );
						}
						?>
						<input type="hidden" value="<?php echo esc_attr( $get_language ); ?>" name="language" id="group-language" />
						<?php
						if ( isset( $_GET['mygroups'] ) && 'true' === $mygroups ) {
							$mygroups = trim( $mygroups );
						} else {
							$mygroups = 'false';
						}
						?>
						<input type="hidden" name="mygroups" value="<?php echo esc_attr( $mygroups ); ?>" />
						<div class="groups__input-container">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M17.5 17.5L13.875 13.875" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
							<input type="text" name="q" id="groups-search" class="groups__search-input" placeholder="<?php esc_attr_e( 'Search groups', 'community-portal' ); ?>" value="<?php echo ! empty( $original_query ) ? esc_attr( $original_query ) : ''; ?>" />
						</div>
						<input type="button" class="groups__search-cta" value="<?php esc_attr_e( 'Search', 'community-portal' ); ?>" />
					</form>
				</div>
			</div>
		</div>	   
		<div class="groups__container">
			<div class="groups__nav">
				<ul class="groups__menu">
					<li class="menu-item"><a class="groups__menu-link
					<?php
					if ( ! isset( $_GET['mygroups'] ) || ( isset( $_GET['mygroups'] ) && 'false' === $_GET['mygroups'] ) ) :
						?>
						group__menu-link--active<?php endif; ?>" href="#" data-nav=""><?php esc_html_e( 'Discover Groups', 'community-portal' ); ?></a></li>
					<?php
					if ( $logged_in ) :
						?>
						<li class="menu-item"><a class="groups__menu-link
						<?php
						if ( isset( $_GET['mygroups'] ) && 'true' === $_GET['mygroups'] ) :
							?>
						group__menu-link--active<?php endif; ?>" href="#" data-nav="mygroups"><?php esc_html_e( 'Groups I\'m In', 'community-portal' ); ?></a></li><?php endif; ?>
				</ul>
			</div>
			<div class="groups__nav groups__nav--mobile">
				<?php echo esc_html_e( 'Showing', 'community-portal' ); ?>
				<select class="groups__nav-select">
					<option value="all"><?php esc_html_e( 'Discover Groups', 'community-portal' ); ?></option>
					<?php
					if ( $logged_in ) :
						?>
						<option value="mygroups"
						<?php
						if ( isset( $_GET['mygroups'] ) && 'true' === $_GET['mygroups'] ) :
							?>
						selected<?php endif; ?>><?php esc_html_e( 'Groups I\'m in', 'community-portal' ); ?></option><?php endif; ?>
				</select>            
			</div>
				<div class="groups__filter-container
				<?php
				if ( ! isset( $_GET['country'] ) && ! isset( $_GET['mygroups'] ) ) :
					?>
					groups__filter-container--hidden<?php endif; ?>">
				<span><?php esc_html_e( 'Filter by:', 'community-portal' ); ?></span>
				<div class="groups__select-container">
					<label class="groups__label"><?php esc_html_e( 'Location', 'community-portal' ); ?></label>
					<select class="groups__location-select">
						<option value=""><?php esc_html_e( 'All', 'community-portal' ); ?></option>
						<?php foreach ( $used_country_list as $code   => $country ) : ?>
						<option value="<?php echo esc_attr( $code ); ?>"
												<?php
												if ( isset( $_GET['country'] ) && strlen( $location ) > 0 && $location === $code ) :
													?>
							selected<?php endif; ?>><?php echo esc_html( $country ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<?php if ( count( $used_language_list ) > 0 ) : ?>
				<div class="groups__select-container">
					<label class="groups__label"><?php esc_html_e( 'Language', 'community-portal' ); ?></label>
					<select class="groups__language-select">
						<option value=""><?php esc_html_e( 'All', 'community-portal' ); ?></option>
						<?php foreach ( $used_language_list as $code   => $language ) : ?>
							<?php if ( strlen( $code ) > 1 ) : ?>
						<option value="<?php echo esc_attr( $code ); ?>"
												<?php
												if ( isset( $_GET['language'] ) && strlen( $get_language ) > 0 && $get_language === $code ) :
													?>
							selected<?php endif; ?>><?php echo esc_html( $language ); ?></option>
						<?php endif; ?>
						<?php endforeach; ?>
					</select>
				</div>
				<?php endif; ?>
				<div class="groups__select-container">
					<label class="groups__label"><?php esc_html_e( 'Tag', 'community-portal' ); ?></label>
					<select class="groups__tag-select">
						<option value=""><?php esc_html_e( 'All', 'community-portal' ); ?></option>

						<?php foreach ( $tags as $loop_tag ) : ?>
							<?php
							if ( false !== stripos( $loop_tag->slug, '_' ) ) {
								$loop_tag->slug = substr( $loop_tag->slug, 0, stripos( $loop_tag->slug, '_' ) );
							}
							?>
						<option value="<?php echo esc_attr( $loop_tag->slug ); ?>" 
											<?php
											if ( isset( $_GET['tag'] ) && strtolower( trim( $get_tag ) ) === strtolower( $loop_tag->slug ) ) :
												?>
							selected<?php endif; ?>><?php echo esc_html( $loop_tag->name ); ?></option>
						<?php endforeach; ?>
					</select>  
				</div>
			</div>
			<div class="groups__show-filters-container">
				<a href="#" class="groups__toggle-filter <?php echo ( isset( $_GET['country'] ) || isset( $_GET['mygroups'] ) ? 'groups__toggle-filter--hide' : 'groups__toggle-filter--show' ); ?>">
					<span class="filters__show"><?php esc_html_e( 'Show Filters', 'community-portal' ); ?></span>
					<span class="filters__hide"><?php esc_html_e( 'Hide Filters', 'community-portal' ); ?></span>
				</a>
			</div>
			<div class="groups__groups">
				<?php do_action( 'bp_before_groups_loop' ); ?>
				<?php if ( 0 === count( $groups ) ) : ?>
					<div class="groups__no-results"><?php esc_html_e( 'No results found.  Please try another search term.', 'community-portal' ); ?></div>
				<?php else : ?>
					<?php if ( ! empty( $original_query ) ) : ?>
				<div class="groups__results-query">
						<?php echo esc_html_e( 'Results for ', 'community-portal' ) . esc_html( "\"{$original_query}\"" ); ?>
				</div>
				<?php endif; ?>
					<?php foreach ( $groups as $group ) : ?>
						<?php
						$meta         = isset( $group->meta ) && is_array( $group->meta ) ? $group->meta : array();
						$member_count = groups_get_total_member_count( $group->id );
						$group_name   = $group->name;

						if ( strlen( $group_name ) > 45 ) {
							$group_name = substr( $group_name, 0, 45 ) . '&#133;';
						}

						if ( is_array( $meta ) && isset( $meta['group_image_url'] ) ) {
							if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
								$group_image_url = preg_replace( '/^http:/i', 'https:', $meta['group_image_url'] );
							} else {
								$group_image_url = $meta['group_image_url'];
							}
						}
						?>
					<a href="
						<?php
						if ( $current_translation ) :
							?>
							<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/<?php echo esc_attr( $group->slug ); ?>" class="groups__card">
						<div class="groups__group-image" style="background-image: url('<?php echo isset( $meta['group_image_url'] ) && strlen( $meta['group_image_url'] ) > 0 ? esc_url_raw( $group_image_url ) : esc_url_raw( get_stylesheet_directory_uri() . '/images/group.png' ); ?>');">
						</div>
						<div class="groups__card-content">
							<div>
							<h2 class="groups__group-title"><?php echo esc_html( str_replace( '\\', '', stripslashes( $group_name ) ) ); ?></h2>
								<?php if ( isset( $meta['group_city'] ) && strlen( trim( $meta['group_city'] ) ) > 0 || isset( $meta['group_country'] ) && '0' !== $meta['group_country'] ) : ?>
								<div class="groups__card-location">
									<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M14 7.66699C14 12.3337 8 16.3337 8 16.3337C8 16.3337 2 12.3337 2 7.66699C2 6.07569 2.63214 4.54957 3.75736 3.42435C4.88258 2.29913 6.4087 1.66699 8 1.66699C9.5913 1.66699 11.1174 2.29913 12.2426 3.42435C13.3679 4.54957 14 6.07569 14 7.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M8 9.66699C9.10457 9.66699 10 8.77156 10 7.66699C10 6.56242 9.10457 5.66699 8 5.66699C6.89543 5.66699 6 6.56242 6 7.66699C6 8.77156 6.89543 9.66699 8 9.66699Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<?php
									if ( strlen( $meta['group_city'] ) > 180 ) {
										$meta['group_city'] = substr( $meta['group_city'], 0, 180 );
									}
									?>
									<?php echo esc_html( $meta['group_city'] ); ?>
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
									<?php echo esc_html( "{$member_count}&nbsp;" ) . esc_html__( 'Members', 'community-portal' ); ?>
								</div>
							</div>
							<div class="groups__card-info">
								<div class="groups__card-tags">
									<?php
										$tag_counter = 0;
										$group_tags = array_unique( array_filter( $meta['group_tags'], 'mozilla_filter_inactive_tags'));
									?>
									<?php if ( isset( $meta['group_tags'] ) && is_array( $meta['group_tags'] ) ) : ?>
									<ul class="groups__card-tags__container">
										<?php foreach ( $group_tags as $key => $value ) : ?>
											<?php

											$system_tag = array_values(
												array_filter(
													$tags,
													function( $e ) use ( &$value ) {
														return $e->slug === $value;
													}
												)
											);
											?>
											<?php if ( ! empty( $system_tag[0]->name ) ) : ?>
										<li class="groups__tag"><?php echo esc_html( $system_tag[0]->name ); ?></li>
												<?php $tag_counter++; ?>
										<?php endif; ?>
											<?php if ( 2 === $tag_counter && count( $group_tags ) > 2 ) : ?>
										<li class="groups__tag">+ <?php echo esc_html( count( $group_tags )  - 2 ); ?> <?php esc_html_e( ' more tags', 'community-portal' ); ?></li>
												<?php break; ?>
										<?php endif; ?>
									<?php endforeach; ?>
									</ul>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</a>
				<?php endforeach; ?>
				<?php endif; ?>
				<?php do_action( 'bp_after_groups_loop' ); ?>
				<?php
					$range = ( $p > 3 ) ? 3 : 5;

				if ( $p > $total_pages - 2 ) {
					$range = 5;
				}

					$previous_page = ( $p > 1 ) ? $p - 1 : 1;
					$next_page     = ( $p <= $total_pages ) ? $p + 1 : $total_pages;

				if ( $total_pages > 1 ) {
					$range_min = ( 0 === $range % 2 ) ? ( $range / 2 ) - 1 : ( $range - 1 ) / 2;
					$range_max = ( 0 === $range % 2 ) ? $range_min + 1 : $range_min;

					$page_min = $p - $range_min;
					$page_max = $p + $range_max;

					$page_min = ( $page_min < 1 ) ? 1 : $page_min;
					$page_max = ( $page_max < ( $page_min + $range - 1 ) ) ? $page_min + $range - 1 : $page_max;

					if ( $page_max > $total_pages ) {
						$page_min = ( $page_min > 1 ) ? $total_pages - $range + 1 : 1;
						$page_max = $total_pages;
					}
				}

				?>
				<div class="groups__pagination">
					<div class="groups__pagination-container">
						<?php if ( $total_pages > 1 ) : ?>
						<a href="
							<?php
							if ( $current_translation ) :
								?>
								<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/?page=<?php echo esc_attr( $previous_page ); ?>
														<?php
														if ( $q ) :
															?>
							&q=<?php echo esc_attr( $q ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['mygroups'] ) ) :
								?>
							&mygroups=<?php echo esc_attr( $mygroups ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['tag'] ) ) :
								?>
	&tag=<?php echo esc_attr( $get_tag ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['country'] ) ) :
								?>
	&country=<?php echo esc_attr( $location ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['language'] ) ) :
								?>
	&language=<?php echo esc_attr( $get_language ); ?><?php endif; ?>" class="groups__pagination-link">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M17 23L6 12L17 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</a>
							<?php
							if ( $page_min > 1 ) :
								?>
								<a href="
								<?php
								if ( $current_translation ) :
									?>
									<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/?page=1
								<?php
								if ( $q ) :
									?>
								&q=<?php echo esc_attr( $q ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['mygroups'] ) ) :
									?>
	&mygroups=<?php echo esc_attr( $mygroups ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['tag'] ) ) :
									?>
	&tag=<?php echo esc_attr( $get_tag ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['country'] ) ) :
									?>
	&country=<?php echo esc_attr( $location ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['language'] ) ) :
									?>
	&language=<?php echo esc_attr( $get_language ); ?><?php endif; ?>" class="groups__pagination-link groups__pagination-link--first"><?php echo esc_html( '1' ); ?></a>&hellip; <?php endif; ?>
							<?php for ( $x = $page_min - 1; $x < $page_max; $x++ ) : ?>
						<a href="
								<?php
								if ( $current_translation ) :
									?>
									<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/?page=<?php echo esc_attr( $x + 1 ); ?>
														<?php
														if ( $q ) :
															?>
							&q=<?php echo esc_attr( $q ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['mygroups'] ) ) :
									?>
							&mygroups=<?php echo esc_attr( $mygroups ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['tag'] ) ) :
									?>
	&tag=<?php echo esc_attr( $get_tag ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['country'] ) ) :
									?>
	&country=<?php echo esc_attr( $location ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['language'] ) ) :
									?>
	&language=<?php echo esc_attr( $get_language ); ?><?php endif; ?>" class="groups__pagination-link
								<?php
								if ( $p === $x + 1 ) :
									?>
	groups__pagination-link--active<?php endif; ?>
								<?php
								if ( $x === $page_max - 1 ) :
									?>
	groups__pagination-link--last<?php endif; ?>"><?php echo esc_html( $x + 1 ); ?></a>
						<?php endfor; ?>
							<?php
							if ( $total_pages > $range && $p < $total_pages - 1 ) :
								?>
								&hellip; <a href="
								<?php
								if ( $current_translation ) :
									?>
									<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/?page=<?php echo esc_attr( $total_pages ); ?>
								<?php
								if ( $q ) :
									?>
								&q=<?php echo esc_attr( $q ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['mygroups'] ) ) :
									?>
	&mygroups=<?php echo esc_attr( $mygroups ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['tag'] ) ) :
									?>
	&tag=<?php echo esc_attr( $get_tag ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['country'] ) ) :
									?>
	&country=<?php echo esc_attr( $location ); ?><?php endif; ?>
								<?php
								if ( isset( $_GET['language'] ) ) :
									?>
	&language=<?php echo esc_attr( $get_language ); ?><?php endif; ?>" class="groups__pagination-link
								<?php
								if ( $p === $total_pages ) :
									?>
	groups__pagination-link--active<?php endif; ?>"><?php echo esc_html( $total_pages ); ?></a><?php endif; ?>
						<a href="
							<?php
							if ( $current_translation ) :
								?>
								<?php echo esc_url_raw( "/{$current_translation}" ); ?><?php endif; ?>/groups/?page=<?php echo esc_attr( $next_page ); ?>
														<?php
														if ( $q ) :
															?>
							&q=<?php echo esc_attr( $q ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['mygroups'] ) ) :
								?>
							&mygroups=<?php echo esc_attr( $mygroups ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['tag'] ) ) :
								?>
	&tag=<?php echo esc_attr( $get_tag ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['country'] ) ) :
								?>
	&country=<?php echo esc_attr( $location ); ?><?php endif; ?>
							<?php
							if ( isset( $_GET['language'] ) ) :
								?>
	&language=<?php echo esc_attr( $get_language ); ?><?php endif; ?>" class="groups__pagination-link">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<path d="M7 23L18 12L7 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php do_action( 'bp_after_directory_groups_content' ); ?>
</div>
<?php
	// Main footer template!
	get_footer();

	// Fire at the end of template!
	do_action( 'bp_after_directory_groups_page' );
?>
