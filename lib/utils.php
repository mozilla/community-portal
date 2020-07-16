<?php
/**
 * Utils
 *
 * Util functions
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

/**
 * Add metabox to campaign admin page
 */
function mozilla_campaign_metabox() {
	add_meta_box(
		'campaign-export-events',
		'Export Events',
		'mozilla_show_campaign_metabox',
		'campaign',
		'side',
		'default'
	);

}

/**
 * Markup for campaign metabox
 *
 * @param object $post post object.
 */
function mozilla_show_campaign_metabox( $post ) {
	$nonce = wp_create_nonce( 'campaign-events' );
	echo wp_kses(
		"<div><a href=\"/wp-admin/admin-ajax.php?action=download_campaign_events&campaign={$post->ID}&nonce={$nonce}\">Export events related to this campaign</a></div>",
		array(
			'a'   => array( 'href' => array() ),
			'div' => array(),
		)
	);
}

/**
 * Add metabox to activity admin page
 */
function mozilla_activity_metabox() {
	add_meta_box(
		'activity-export-events',
		'Export Events',
		'mozilla_show_activity_metabox',
		'activity',
		'side',
		'default'
	);

}

/**
 * Markup for activity metabox
 *
 * @param object $post post object.
 */
function mozilla_show_activity_metabox( $post ) {
	echo wp_kses(
		"<div><a href=\"/wp-admin/admin-ajax.php?action=download_activity_events&activity={$post->ID}\">Export events related to this activity</a></div>",
		array(
			'a'   => array( 'href' => array() ),
			'div' => array(),
		)
	);
}

/**
 * General function for uploading images
 */
function mozilla_upload_image() {

	if ( ! empty( $_FILES ) ) {

		if ( isset( $_REQUEST['my_nonce_field'] ) ) {

			$nonce = trim( sanitize_text_field( wp_unslash( $_REQUEST['my_nonce_field'] ) ) );

			if ( wp_verify_nonce( $nonce, 'protect_content' ) ) {

				if ( isset( $_FILES['file'] ) && isset( $_FILES['file']['tmp_name'] ) ) {
					$image_file = sanitize_text_field( wp_unslash( $_FILES['file']['tmp_name'] ) );

					$image     = getimagesize( $image_file );
					$file_size = filesize( $image_file );

					$file_size_kb           = number_format( $file_size / 1024, 2 );
					$options                = wp_load_alloptions();
					$max_files_size_allowed = isset( $options['image_max_filesize'] ) && intval( $options['image_max_filesize'] ) > 0 ? intval( $options['image_max_filesize'] ) : 500;

					if ( $file_size_kb <= $max_files_size_allowed ) {

						if ( isset( $image[2] ) && in_array( $image[2], array( IMAGETYPE_JPEG, IMAGETYPE_PNG ), true ) ) {
							if ( ! empty( $_FILES['file']['name'] ) ) {
								$file_name = sanitize_text_field( wp_unslash( $_FILES['file']['name'] ) );

								WP_Filesystem();
								global $wp_filesystem;
								$data = $wp_filesystem->get_contents( $image_file );

								$uploaded_bits = wp_upload_bits( $file_name, null, $data );

								if ( false !== $uploaded_bits['error'] ) {
									exit();
								} else {
									$uploaded_file             = $uploaded_bits['file'];
									$_SESSION['uploaded_file'] = $uploaded_bits['file'];

									$uploaded_url      = $uploaded_bits['url'];
									$uploaded_filetype = wp_check_filetype( basename( $uploaded_bits['file'] ), null );

									if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
										$uploaded_url = preg_replace( '/^http:/i', 'https:', $uploaded_url );
									}

									if ( isset( $_REQUEST['profile_image'] ) && 'true' === $_REQUEST['profile_image'] ) {
										// Image size check.
										if ( isset( $image[0] ) && isset( $image[1] ) ) {
											if ( $image[0] >= 175 && $image[1] >= 175 ) {
												print esc_url_raw( trim( str_replace( "\n", '', $uploaded_url ) ) );
											} else {

												print esc_html_e( 'Image size is too small', 'community-portal' );

												unlink( $uploaded_bits['file'] );
											}
										} else {
											print esc_html_e( 'Invalid image provided', 'community-portal' );
											unlink( $uploaded_bits['file'] );
										}
									} elseif ( isset( $_REQUEST['group_image'] ) && 'true' === $_REQUEST['group_image'] || isset( $_REQUEST['event_image'] ) && 'true' === $_REQUEST['event_image'] ) {
										if ( isset( $image[0] ) && isset( $image[1] ) ) {
											if ( $image[0] >= 703 && $image[1] >= 400 ) {
												print esc_url_raw( trime( str_replace( "\n", '', $uploaded_url ) ) );
											} else {
												print esc_html_e( 'Image size is too small', 'community-portal' );
												unlink( $uploaded_bits['file'] );
											}
										} else {
											print esc_html_e( 'Invalid image provided', 'community-portal' );
											unlink( $uploaded_bits['file'] );
										}
									} else {
										print esc_url_raw( trim( str_replace( "\n", '', $uploaded_url ) ) );
										unlink( $uploaded_bits['file'] );
									}
								}
							}
						}
					} else {
						print esc_html_e( 'Image size to large ', 'community-portal' ) . esc_html_e( '(250KB maximum)', 'community-portal' );
					}
				}
			}
		}
	}

	die();
}

/**
 * Determines which section a user is on
 */
function mozilla_determine_site_section() {

	if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
		$path_items = array_filter( explode( '/', esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );

		if ( count( $path_items ) > 0 ) {
			if ( mozilla_get_current_translation() && ! empty( $path_items[2] ) ) {
				$section = $path_items[2];
			} else {
				$values  = array_values( $path_items );
				$section = array_shift( $values );
			}

			return $section;
		}
	}

	return false;
}

/**
 * Adds attribute to menu item
 *
 * @param array $attrs element attributes.
 * @param array $item current item.
 * @param array $args argumemnts.
 */
function mozilla_add_menu_attrs( $attrs, $item, $args ) {
	$attrs['class'] = 'menu-item__link';
	return $attrs;
}

/**
 * Initialize scripts
 */
function mozilla_init_scripts() {

	// Vendor scripts.
	wp_enqueue_script( 'dropzonejs', get_stylesheet_directory_uri() . '/js/vendor/dropzone.min.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/vendor/dropzone.min.js' ), false );
	wp_enqueue_script( 'autcomplete', get_stylesheet_directory_uri() . '/js/vendor/autocomplete.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/vendor/autocomplete.js' ), false );
	wp_enqueue_script( 'identicon', get_stylesheet_directory_uri() . '/js/vendor/identicon.js', array(), filemtime( get_template_directory() . '/js/vendor/identicon.js' ), false );
	wp_enqueue_script( 'mapbox', get_stylesheet_directory_uri() . '/js/vendor/mapbox.js', array(), filemtime( get_template_directory() . '/js/vendor/mapbox.js' ), false );

	// Custom scripts.
	wp_enqueue_script( 'groups', get_stylesheet_directory_uri() . '/js/groups.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/groups.js' ), false );
	wp_enqueue_script( 'events', get_stylesheet_directory_uri() . '/js/events.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/events.js' ), false );
	wp_enqueue_script( 'activities', get_stylesheet_directory_uri() . '/js/activities.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/activities.js' ), false );
	wp_enqueue_script( 'cleavejs', get_stylesheet_directory_uri() . '/js/vendor/cleave.min.js', array(), filemtime( get_template_directory() . '/js/vendor/cleave.min.js' ), false );
	wp_enqueue_script( 'nav', get_stylesheet_directory_uri() . '/js/nav.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/nav.js' ), false );
	wp_enqueue_script( 'profile', get_stylesheet_directory_uri() . '/js/profile.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/profile.js' ), false );
	wp_enqueue_script( 'lightbox', get_stylesheet_directory_uri() . '/js/lightbox.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/lightbox.js' ), false );
	wp_enqueue_script( 'gdpr', get_stylesheet_directory_uri() . '/js/gdpr.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/gdpr.js' ), false );
	wp_enqueue_script( 'dropzone', get_stylesheet_directory_uri() . '/js/dropzone.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/dropzone.js' ), false );
	wp_enqueue_script( 'newsletter', get_stylesheet_directory_uri() . '/js/newsletter.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/newsletter.js' ), false );
	wp_enqueue_script( 'mailchimp', get_stylesheet_directory_uri() . '/js/campaigns.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/campaigns.js' ), false );
	wp_enqueue_script( 'language', get_stylesheet_directory_uri() . '/js/language.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/language.js' ), false );

	$google_analytics_id = get_option( 'google_analytics_id' );
	if ( $google_analytics_id ) {
		$url = esc_url( "https://www.googletagmanager.com/gtag/js?id={$google_analytics_id}" );
		wp_enqueue_script( 'google-analytics', $url, array(), '1.0', false );
		$script = '
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag("js", new Date());
		gtag("config", "' . esc_attr( $google_analytics_id ) . '");
    </script>';

		wp_add_inline_script( 'google-analytics', $script, 'after' );

	}
}

/**
 * Initialize front end scripts
 */
function mozilla_init_fe_styles() {
	wp_enqueue_style( 'style', get_stylesheet_uri(), array(), filemtime( get_template_directory() . '/style.css' ), false );
}

/**
 * Initialize admin scripts
 */
function mozilla_init_admin_scripts() {
	$screen = get_current_screen();
	if ( strtolower( $screen->id ) === 'toplevel_page_bp-groups' ) {
		wp_enqueue_style( 'styles', get_stylesheet_directory_uri() . '/style.css', false, '1.0.0' );
		wp_enqueue_script( 'groups', get_stylesheet_directory_uri() . '/js/admin.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/admin.js' ), false );
	}
	if ( strtolower( $screen->id ) === 'toplevel_page_events-export-panel' ) {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-css', '/wp-content/plugins/events-manager/includes/css/jquery-ui.min.css', false, '1.0.0' );
		wp_enqueue_script( 'date', get_stylesheet_directory_uri() . '/js/date.js', array( 'jquery' ), filemtime( get_template_directory() . '/js/date.js' ), false );
	}
}


/**
 * Removes login header
 */
function mozilla_remove_admin_login_header() {
	remove_action( 'wp_head', '_admin_bar_bump_cb' );
}

/**
 * Theme settings
 */
function mozilla_theme_settings() {
	$theme_dir = get_template_directory();

	if ( current_user_can( 'manage_options' ) && ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		if ( isset( $_POST['admin_nonce_field'] ) ) {
			$nonce = trim( sanitize_text_field( wp_unslash( $_POST['admin_nonce_field'] ) ) );

			if ( wp_verify_nonce( $nonce, 'admin_nonce' ) ) {

				if ( isset( $_POST['github_link'] ) ) {
					$github_link = sanitize_text_field( wp_unslash( $_POST['github_link'] ) );
					update_option( 'github_link', $github_link );
				}

				if ( isset( $_POST['community_discourse'] ) ) {
					$community_discourse = sanitize_text_field( wp_unslash( $_POST['community_discourse'] ) );
					update_option( 'community_discourse', $community_discourse );
				}

				if ( isset( $_POST['google_analytics_id'] ) ) {
					$google_analytics_id = sanitize_text_field( wp_unslash( $_POST['google_analytics_id'] ) );
					update_option( 'google_analytics_id', $google_analytics_id );
				}

				if ( isset( $_POST['google_analytics_sri'] ) ) {
					$google_analytics_sri = sanitize_text_field( wp_unslash( $_POST['google_analytics_sri'] ) );
					update_option( 'google_analytics_sri', $google_analytics_sri );
				}

				if ( isset( $_POST['default_open_graph_title'] ) ) {
					$default_open_graph_title = sanitize_text_field( wp_unslash( $_POST['default_open_graph_title'] ) );
					update_option( 'default_open_graph_title', $default_open_graph_title );
				}

				if ( isset( $_POST['default_open_graph_desc'] ) ) {
					$default_open_graph_desc = sanitize_text_field( wp_unslash( $_POST['default_open_graph_desc'] ) );
					update_option( 'default_open_graph_desc', $default_open_graph_desc );
				}

				if ( isset( $_POST['image_max_filesize'] ) ) {
					$image_max_filesize = sanitize_text_field( wp_unslash( $_POST['image_max_filesize'] ) );
					update_option( 'image_max_filesize', intval( $image_max_filesize ) );
				}

				if ( isset( $_POST['error_404_title'] ) ) {
					$error_404_title = sanitize_text_field( wp_unslash( $_POST['error_404_title'] ) );
					update_option( 'error_404_title', $error_404_title );
				}

				if ( isset( $_POST['error_404_copy'] ) ) {
					$error_404_copy = sanitize_text_field( wp_unslash( $_POST['error_404_copy'] ) );
					update_option( 'error_404_copy', $error_404_copy );
				}

				if ( isset( $_POST['discourse_api_key'] ) ) {
					$discourse_api_key = sanitize_text_field( wp_unslash( $_POST['discourse_api_key'] ) );
					update_option( 'discourse_api_key', $discourse_api_key );
				}

				if ( isset( $_POST['discourse_api_url'] ) ) {
					$discourse_api_url = sanitize_text_field( wp_unslash( $_POST['discourse_api_url'] ) );
					update_option( 'discourse_api_url', $discourse_api_url );
				}

				if ( isset( $_POST['discourse_url'] ) ) {
					$discourse_url = sanitize_text_field( wp_unslash( $_POST['discourse_url'] ) );
					update_option( 'discourse_url', $discourse_url );
				}

				if ( isset( $_POST['mapbox'] ) ) {
					$mapbox = sanitize_text_field( wp_unslash( $_POST['mapbox'] ) );
					update_option( 'mapbox', $mapbox );
				}

				if ( isset( $_POST['report_email'] ) ) {
					$report_email = sanitize_email( wp_unslash( $_POST['report_email'] ) );
					update_option( 'report_email', $report_email );
				}

				if ( isset( $_POST['mailchimp'] ) ) {
					$mailchimp = sanitize_text_field( wp_unslash( $_POST['mailchimp'] ) );
					update_option( 'mailchimp', $mailchimp );
				}

				if ( isset( $_POST['company'] ) ) {
					$company = sanitize_text_field( wp_unslash( $_POST['company'] ) );
					update_option( 'company', $company );
				}

				if ( isset( $_POST['address'] ) ) {
					$address = sanitize_text_field( wp_unslash( $_POST['address'] ) );
					update_option( 'address', $address );
				}

				if ( isset( $_POST['city'] ) ) {
					$city = sanitize_text_field( wp_unslash( $_POST['city'] ) );
					update_option( 'city', $city );
				}

				if ( isset( $_POST['state'] ) ) {
					$state = sanitize_text_field( wp_unslash( $_POST['state'] ) );
					update_option( 'state', $state );
				}

				if ( isset( $_POST['zip'] ) ) {
					$zip = sanitize_text_field( wp_unslash( $_POST['zip'] ) );
					update_option( 'zip', $zip );
				}

				if ( isset( $_POST['country'] ) ) {
					$country = sanitize_text_field( wp_unslash( $_POST['country'] ) );
					update_option( 'country', $country );
				}

				if ( isset( $_POST['phone'] ) ) {
					$phone = sanitize_text_field( wp_unslash( $_POST['phone'] ) );
					update_option( 'phone', $phone );
				}
			}
		}
	}

	$options = wp_load_alloptions();
	include "{$theme_dir}/templates/settings.php";
}


/**
 * Include event export template
 */
function mozilla_export_events_control() {
	$theme_dir = get_template_directory();
	include "{$theme_dir}/templates/event-export.php";
}

/**
 * Add new menu item
 */
function mozilla_add_menu_item() {
	add_menu_page( 'Mozilla Settings', 'Mozilla Settings', 'manage_options', 'theme-panel', 'mozilla_theme_settings', null, 99 );
	add_menu_page( 'Mozilla Export Events', 'Export Events', 'manage_options', 'events-export-panel', 'mozilla_export_events_control', 'dashicons-media-spreadsheet', 99 );
}

/**
 * Check if current user is an admin
 */
function mozilla_is_site_admin() {
	return in_array( 'administrator', wp_get_current_user()->roles, true );
}

/**
 * Update body class of page
 *
 * @param array $classes classes for body.
 */
function mozilla_update_body_class( $classes ) {
	$classes[] = 'body';
	return $classes;
}

/**
 * Add menu classes
 *
 * @param array  $classes classes for item.
 * @param object $item menu item.
 * @param array  $args arguments.
 */
function mozilla_menu_class( $classes, $item, $args ) {

	if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
		$request_uri = trim( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );

		$current_translation = mozilla_get_current_translation();
		$path_items          = array_filter( explode( '/', $request_uri ) );
		$menu_url_parts      = explode( '/', $item->url );
		$menu_url            = strtolower( str_replace( '/', '', $item->url ) );

		if ( count( $path_items ) > 0 ) {
			if ( 'en' !== $current_translation && ! empty( $path_items[2] ) && ! empty( $menu_url_parts[2] ) ) {
				if ( strtolower( $path_items[2] ) === strtolower( $menu_url_parts[2] ) ) {
					$item->current = true;
					$classes[]     = 'menu-item--active';
				}
			} else {
				if ( ! empty( $path_items[2] ) && ! empty( $menu_url_parts[1] ) ) {
					if ( strtolower( $path_items[2] ) === strtolower( $menu_url_parts[1] ) ) {
						$item->current = true;
						$classes[]     = 'menu-item--active';
					}
				}
			}
		}
	}

	return $classes;
}

/**
 * Add query variable
 *
 * @param array $vars variables.
 */
function mozilla_add_query_vars_filter( $vars ) {
	$vars[] = 'view';
	$vars[] = 'country';
	$vars[] = 'tag';
	$vars[] = 'a';

	return $vars;
}

/**
 *
 * Create Event Category
 *
 * @param int $term_id id of the existing term.
 * @param int $tt_id id for taxonomy.
 */
function mozilla_create_event_category( $term_id, $tt_id ) {
	$term = get_term( $term_id, 'post_tags' );
	if ( ! empty( $term ) && false === stripos( $term->slug, '_' ) ) {
		wp_insert_term( $term->name, 'event-categories', array( 'slug' => $term->slug ) );
	}
}

add_action( 'create_post_tag', 'mozilla_create_event_category', 10, 2 );

/**
 *
 * Update Event Categories
 *
 * @param int $term_id id of the existing term.
 * @param int $tt_id id for taxonomy.
 */
function mozilla_update_event_category( $term_id, $tt_id ) {
	$term     = get_term( $term_id, 'post_tags' );
	$cat_term = get_term_by( 'slug', $term->slug, 'event-categories' );
	if ( empty( $cat_term ) ) {
		$cat_term = get_term_by( 'name', $term->name, 'event-categories' );
	}

	if ( ! empty( $term ) && ! empty( $cat_term ) && false === stripos( $term->slug, '_' ) ) {
		wp_update_term(
			$cat_term->term_id,
			'event-categories',
			array(
				'slug' => $term->slug,
				'name' => $term->name,
			)
		);
		return;
	}
	mozilla_create_event_category( $term_id, $tt_id );

}
add_action( 'edited_post_tag', 'mozilla_update_event_category', 10, 3 );

/**
 * Delete Event Categories
 *
 * @param int    $term_id id of the existing term.
 * @param int    $tt_id id for taxonomy.
 * @param object $deleted_term the deleted term.
 * @param object $object_ids deleted object.
 */
function mozilla_delete_event_category( $term_id, $tt_id, $deleted_term, $object_ids ) {
	$cat_term = get_term_by( 'slug', $deleted_term->slug, 'event-categories' );
	if ( empty( $cat_term ) ) {
		$cat_term = get_term_by( 'name', $deleted_term->name, 'event-categories' );
	}
	if ( ! empty( $deleted_term ) && ! empty( $cat_term ) && false === stripos( $deleted_term->slug, '_' ) ) {
		wp_delete_term( $cat_term->term_id, 'event-categories' );
	}
}
add_action( 'delete_post_tag', 'mozilla_delete_event_category', 10, 4 );

/**
 * Redirect non admins
 */
function mozilla_redirect_admin() {
	if ( ( ! current_user_can( 'manage_options' ) || current_user_can( 'subscriber' ) ) && ! empty( $_SERVER['PHP_SELF'] ) && '/wp-admin/admin-ajax.php' !== sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ) ) {
		wp_safe_redirect( '/' );
		exit();
	}
}

/**
 * Verify URL
 *
 * @param string  $url url to verify.
 * @param boolean $secure is secure URL.
 */
function mozilla_verify_url( $url, $secure ) {

	if ( preg_match( '/\.[a-zA-Z]{2,4}\b/', $url ) ) {
		$parts = wp_parse_url( $url );
		if ( ! isset( $parts['scheme'] ) ) {
			if ( $secure ) {
				$url = 'https://' . $url;
			} else {
				$url = 'http://' . $url;
			}
		}
	}

	if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return $url;
	}

	return false;
}

/**
 * Add columns to admin
 *
 * @param array $columns table column.
 */
function mozilla_add_group_columns( $columns ) {

	$columns['group_created'] = __( 'Group Created On', 'community-portal' );
	$columns['admins']        = __( 'Admins', 'community-portal' );
	$columns['events']        = __( 'Events', 'community-portal' );
	$columns['verified_date'] = __( 'Group Verified On', 'community-portal' );

	return $columns;

}

/**
 * Add additional info table
 *
 * @param string $retval return value.
 * @param string $column_name name of column.
 * @param array  $item contents of column.
 */
function mozilla_group_addional_column_info( $retval = '', $column_name, $item ) {
	if ( 'group_created' !== $column_name
		&& 'events' !== $column_name
		&& 'admins' !== $column_name
		&& 'verified_date' !== $column_name ) {
		return $retval;
	}

	switch ( $column_name ) {
		case 'group_created':
			if ( isset( $item['date_created'] ) ) {
				if ( strtotime( $item['date_created'] ) < strtotime( '-1 month' ) ) {
					$class = 'admin__group-status--passed';
				} else {
					$class = 'admin__group-status--new';
				}

				return wp_kses( "<div class=\"{$class}\">{$item['date_created']}</div>", array( 'div' => array( 'class' => array() ) ) );
			}

			break;
		case 'events':
			$args = array(
				'group' => $item['id'],
				'scope' => 'all',
			);

			$events = EM_Events::get( $args );
			return count( $events );
		case 'admins':
			$admins = groups_get_group_admins( $item['id'] );
			return count( $admins );
		case 'verified_date':
			$group_meta = groups_get_groupmeta( $item['id'], 'meta' );

			if ( isset( $group_meta['verified_date'] ) ) {
				$date_check = strtotime( '+1 year', $group_meta['verified_date'] );

				if ( $date_check < time() ) {
					$class = 'admin__group-status--red';
				} else {
					$class = 'admin__group-status--new';
				}

				$verified_date = gmdate( 'Y-m-d H:i:s', $group_meta['verified_date'] );
				return wp_kses( "<div class=\"{$class}\">{$verified_date}</div>", array( 'div' => array( 'class' => array() ) ) );
			} else {
				return '-';
			}
	}

	return '-';
}

/**
 * Save post hook
 *
 * @param integer $post_id post ID.
 * @param object  $post post object.
 * @param boolean $update are we updating.
 */
function mozilla_save_post( $post_id, $post, $update ) {

	if ( 'event' === $post->post_type && $update ) {

		$user              = wp_get_current_user();
		$event_update_meta = get_post_meta( $post_id, 'event-meta' );
		$event             = new stdClass();

		if ( isset( $event_update_meta[0]->discourse_group_id ) ) {
			$event->discourse_group_id = $event_update_meta[0]->discourse_group_id;
		}

		if ( isset( $event_update_meta[0]->discourse_group_name ) ) {
			$event->discourse_group_name = $event_update_meta[0]->discourse_group_name;
		}

		if ( isset( $event_update_meta[0]->discourse_group_description ) ) {
			$event->discourse_group_description = $event_update_meta[0]->discourse_group_description;
		}

		if ( isset( $event_update_meta[0]->discourse_group_users ) ) {
			$event->discourse_group_users = $event_update_meta[0]->discourse_group_users;
		}

		if ( isset( $_POST['event_update_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['event_update_field'] ) ), 'event_update' ) ) {

			if ( isset( $_POST['image_url'] ) ) {
				$event->image_url = esc_url_raw( wp_unslash( $_POST['image_url'] ) );
			} else {
				$event->image_url = $event_update_meta[0]->image_url;
			}

			if ( isset( $_POST['location-type'] ) ) {
				$event->location_type = sanitize_text_field( wp_unslash( $_POST['location-type'] ) );
			} else {
				$event->location_type = $event_update_meta[0]->location_type;
			}

			if ( isset( $_POST['event_external_link'] ) ) {
				$event->external_url = esc_url_raw( wp_unslash( $_POST['event_external_link'] ) );
			} else {
				$event->external_url = $event_update_meta[0]->external_url;
			}

			if ( isset( $_POST['language'] ) ) {
				$event->language = sanitize_text_field( wp_unslash( $_POST['language'] ) );
			} else {
				$event->language = $event_update_meta[0]->language;
			}

			if ( isset( $_POST['goal'] ) ) {
				$event->goal = sanitize_textarea_field( wp_unslash( $_POST['goal'] ) );
			} else {
				$event->goal = $event_update_meta[0]->goal;
			}

			if ( isset( $_POST['projected-attendees'] ) ) {
				$event->projected_attendees = sanitize_text_field( wp_unslash( $_POST['projected-attendees'] ) );
			} else {
				$event->projected_attendees = $event_update_meta[0]->projected_attendees;
			}

			if ( isset( $_POST['initiative_id'] ) ) {
				$initiative_id = intval( sanitize_text_field( wp_unslash( $_POST['initiative_id'] ) ) );

				if ( $initiative_id ) {
					$initiative = get_post( $initiative_id );
					if ( $initiative && ( 'campaign' === $initiative->post_type || 'activity' === $initiative->post_type ) ) {
						$event->initiative = $initiative_id;
					}
				}
			} else {
				$event->initiative = $event_update_meta[0]->initiative;
			}
		} else {
			$event = $event_update_meta[0];
		}

		$discourse_api_data = array();

		$discourse_api_data['name']        = $post->post_name;
		$discourse_api_data['description'] = $post->post_content;

		if ( ! empty( $event_update_meta ) && isset( $event_update_meta[0]->discourse_group_id ) ) {
			$discourse_api_data['group_id'] = $event_update_meta[0]->discourse_group_id;
			$discourse_event                = mozilla_get_discourse_info( $post_id, 'event' );
			$discourse_api_data['users']    = $discourse_event['discourse_group_users'];
			$discourse_group                = mozilla_discourse_api( 'groups', $discourse_api_data, 'patch' );
		}

		if ( $discourse_group ) {
			$event->discourse_log = $discourse_group;
		}
		update_post_meta( $post->ID, 'event-meta', $event );

	}
}

/**
 * Check ACF Field for Mailchimp when saving campaigns
 *
 * @param integer $post_id post ID.
 */
function mozilla_acf_save_post( $post_id ) {

	// Check to see if we are autosaving.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$post_type = get_post_type( $post_id );

	// First check that we are dealing with campaigns.
	if ( 'campaign' === $post_type ) {

		$prev_published        = get_post_meta( $post_id, 'prev_published', true );
		$mailchimp_integration = get_field( 'mailchimp_integration', $post_id );

		if ( empty( $prev_published ) && $mailchimp_integration ) {
			$post = get_post( $post_id );
			update_post_meta( $post_id, 'prev_published', true );

			mozilla_create_mailchimp_list( $post );
		}
	}

}

/**
 * When changing status for a post
 *
 * @param string $new_status the new status.
 * @param string $old_status the old status.
 * @param object $post post.
 */
function mozilla_post_status_transition( $new_status, $old_status, $post ) {

	// Support for campaigns already published.
	// Set the required meta here if the old status is publish.
	// If the post is new set a default value of false for prev_publish.
	if ( 'campaign' === $post->post_type ) {
		if ( 'new' === $old_status ) {
			update_post_meta( $post->ID, 'prev_published', false );
		}

		if ( 'publish' === $old_status && ! metadata_exists( 'post', $post->ID, 'prev_published' ) ) {
			update_post_meta( $post->ID, 'prev_published', true );
		}
	}

	if ( 'publish' === $new_status ) {

		if ( 'event' === $post->post_type && 'publish' !== $old_status ) {
			$event = new stdClass();
			if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['event_update_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['event_update_field'] ) ), 'event_update' ) ) {

				if ( isset( $_POST['image_url'] ) ) {
					$event->image_url = esc_url_raw( wp_unslash( $_POST['image_url'] ) );
				} else {
					$event->image_url = '';
				}

				if ( isset( $_POST['location-type'] ) ) {
					$event->location_type = sanitize_text_field( wp_unslash( $_POST['location-type'] ) );
				} else {
					$event->location_type = '';
				}

				if ( isset( $_POST['event_external_link'] ) ) {
					$event->external_url = esc_url_raw( wp_unslash( $_POST['event_external_link'] ) );
				} else {
					$event->external_url = '';
				}

				if ( isset( $_POST['language'] ) ) {
					$event->language = sanitize_text_field( wp_unslash( $_POST['language'] ) );
				} else {
					$event->language = '';
				}

				if ( isset( $_POST['goal'] ) ) {
					$event->goal = sanitize_textarea_field( wp_unslash( $_POST['goal'] ) );
				} else {
					$event->goal = '';
				}

				if ( isset( $_POST['projected-attendees'] ) ) {
					$event->projected_attendees = intval( sanitize_text_field( wp_unslash( $_POST['projected-attendees'] ) ) );
				} else {
					$event->projected_attendees = '';
				}

				if ( isset( $_POST['initiative_id'] ) ) {
					$initiative_id = intval( sanitize_text_field( wp_unslash( $_POST['initiative_id'] ) ) );

					if ( $initiative_id ) {
						$initiative = get_post( $initiative_id );
						if ( $initiative && ( 'campaign' === $initiative->post_type || 'activity' === $initiative->post_type ) ) {
							$event->initiative = $initiative_id;
						}
					}
				}
			}

			$discourse_api_data                = array();
			$discourse_api_data['name']        = $post->post_name;
			$discourse_api_data['description'] = $post->post_content;
			$auth0_ids                         = array();
			$user                              = wp_get_current_user();
			$current_user_auth_id              = mozilla_get_user_auth0( $user->ID );

			if ( false !== $current_user_auth_id ) {
				$auth0_ids[] = $current_user_auth_id;
			}

			$discourse_api_data['users'] = $auth0_ids;
			$discourse_group             = mozilla_discourse_api( 'groups', $discourse_api_data, 'post' );

			if ( $discourse_group ) {
				if ( isset( $discourse_group->id ) ) {
					$event->discourse_group_id = $discourse_group->id;
				} else {
					$event->discourse_log = $discourse_group;
				}
			}

			update_post_meta( $post->ID, 'event-meta', $event );

		}
	}
}

/**
 * Exports users for events
 */
function mozilla_export_users() {

	// Only admins.
	if ( ! is_admin() && in_array( 'administrator', wp_get_current_user()->roles, true ) === false ) {
		return;
	}

	$theme_directory = get_template_directory();
	include "{$theme_directory}/languages.php";
	include "{$theme_directory}/countries.php";

	$users = get_users( array() );

	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment; filename=users.csv;' );
	// CSV Column Titles.
	print "first name, last name, email,date registered, languages, country\n ";
	foreach ( $users as $user ) {
		$meta             = get_user_meta( $user->ID );
		$community_fields = isset( $meta['community-meta-fields'][0] ) ? unserialize( $meta['community-meta-fields'][0] ) : array();

		$first_name     = isset( $meta['first_name'][0] ) ? $meta['first_name'][0] : '';
		$last_name      = isset( $meta['last_name'][0] ) ? $meta['last_name'][0] : '';
		$user_languages = isset( $community_fields['languages'] ) && count( $community_fields['languages'] ) > 0 ? $community_fields['languages'] : array();

		$language_string = '';
		foreach ( $user_languages as $language_code ) {
			if ( strlen( $language_code ) > 0 ) {
				$language_string .= "{$languages[$language_code]},";
			}
		}

		// Remove ending comma.
		$language_string = rtrim( $language_string, ',' );

		$country = isset( $community_fields['country'] ) && strlen( $community_fields['country'] ) > 0 ? $countries[ $community_fields['country'] ] : '';
		$date    = gmdate( 'd/m/Y', strtotime( $user->data->user_registered ) );

		// Print out CSV row.
		$first_name      = esc_html( sanitize_text_field( $first_name ) );
		$last_name       = esc_html( sanitize_text_field( $last_name ) );
		$email           = esc_html( sanitize_text_field( $user->data->user_email ) );
		$date            = esc_html( sanitize_text_field( $date ) );
		$language_string = esc_html( sanitize_text_field( $language_string ) );
		$country         = esc_html( sanitize_text_field( $country ) );

		print "{$first_name},{$last_name},{$email},{$date},\"{$language_string}\",{$country}\n";
	}
	die();
}

/**
 * Hide the emails in menus
 *
 * @param array $items items of the menu.
 * @param array $args arguments.
 */
function mozilla_hide_menu_emails( $items, $args ) {

	foreach ( $items as $index => $item ) {
		if ( false !== stripos( $item->url, 'mailto:' ) && ! is_user_logged_in() ) {
			unset( $items[ $index ] );
		}

		$index++;
	}

	return $items;
}

/**
 * Updates the inline google analytics code and adds SRI
 *
 * @param string $html The code.
 * @param string $handle The name of the code.
 */
function mozilla_update_script_attributes( $html, $handle ) {
	if ( 'google-analytics' === $handle ) {
		$google_analytics_sri = esc_attr( get_option( 'google_analytics_sri' ) );

		if ( $google_analytics_sri ) {
			$needle = "type='text/javascript'";
			$pos    = strpos( $html, $needle );
			return substr_replace( $html, "type='text/javascript' async integrity='{$google_analytics_sri}' crossorigin='anonymous'", $pos, strlen( $needle ) );
		}
	}

	return $html;

}

/**
 * Gets the current language of the site
 */
function mozilla_get_current_translation() {
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		return ICL_LANGUAGE_CODE;
	} else {
		return 'en';
	}
}

/**
 * Returns the formatted/translated date
 *
 * @param mixed  $date the date to be translated.
 * @param string $format the desired format.
 */
function mozilla_localize_date( $date, $format ) {
	$formatted_date = date_i18n( $format, strtotime( $date ) );
	return $formatted_date;
}


/**
 * Maps tags saved by name to be slugs
 *
 * @param string $tag the saved tag.
 */
function mozilla_map_tags( $tag ) {
	$term_obj = get_term_by( 'slug', $tag, 'post_tag' );
	if ( is_object( $term_obj ) && ! empty( $term_obj ) && isset( $term_obj->slug ) && strlen( $term_obj->slug ) > 0 ) {
		if ( false !== stripos( $term_obj->slug, '_' ) ) {
			$term_obj->slug = substr( $term_obj->slug, 0, stripos( $term_obj->slug, '_' ) );
		};
		return $term_obj->slug;
	}
	return '';
}

/**
 * Filters tags for active tags
 *
 * @param string $tag the saved tag.
 */
function mozilla_filter_inactive_tags( $tag ) {
	$term_obj = get_term_by( 'slug', $tag, 'post_tag' );
	return is_object( $term_obj ) && ! empty( $term_obj ) && isset( $term_obj->slug ) && strlen( $term_obj->slug ) > 0;
}

/**
 * Maps initiatives to use IDs for English versions
 *
 * @param mixed $post_object the individual post.
 */
function mozilla_apply_default_post_ids($post) {
	$post_type = $post->post_type;
	$post->ID = apply_filters( 'wpml_object_id', $post->ID, $post_type, true, 'en' );
  return $post;
}

/** 
 * Adjusts filters on ACF post search to allow all events
 *
 * @param array $args current arguments.
 * @param object $post current post.
 * @param integer $post_id current post id.
 */
function mozilla_query_all_events( $args, $post, $post_id ) {
	$args['suppress_filters'] = true;
	return $args;
}