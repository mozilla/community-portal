<?php
/**
 * Header
 *
 * Header file for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

$user = wp_get_current_user()->data;
$meta = get_user_meta( $user->ID );

$community_fields = isset( $meta['community-meta-fields'][0] ) ? unserialize( $meta['community-meta-fields'][0] ) : array();

if ( isset( $community_fields['image_url'] ) ) {
	$avatar = $community_fields['image_url'];
} else {
	$avatar = false;
}

if ( $avatar && ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || isset( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
	$avatar = preg_replace( '/^http:/i', 'https:', $avatar );
}

$section   = mozilla_determine_site_section();
$theme_url = get_template_directory_uri();

if ( ! empty( $_GET['s'] ) && isset( $_GET['site_search'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['site_search'] ) ), 'site_search_nonce' ) ) {
	$search_text    = sanitize_text_field( wp_unslash( $_GET['s'] ) );
	$original_query = htmlspecialchars( $search_text, ENT_QUOTES, 'UTF-8' );

} else {
	$search_text = sanitize_text_field( wp_unslash( $_GET['s'] ) );
}

if (
		strpos( $original_query, '"' ) !== false ||
		strpos( $original_query, "'" ) !== false ||
		strpos( $original_query, '\\' ) !== false
	) {
	$search_text    = sanitize_text_field( wp_unslash( $_GET['s'] ) );
	$original_query = htmlspecialchars( $search_text, ENT_QUOTES, 'UTF-8' );
	$original_query = preg_replace( '/^\"|\"$|^\'|\'$/', '', $original_query );
} else {
	$search_text = sanitize_text_field( wp_unslash( $_GET['s'] ) );
}

	$protocol = ! empty( wp_get_server_protocol() ) && 0 === stripos( wp_get_server_protocol(), 'https' ) ? 'https://' : 'http://';
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="<?php echo esc_attr( "{$theme_url}/images/favicon.ico" ); ?>" />
		<?php if ( $section ) : ?>
			<?php
			switch ( strtolower( $section ) ) {
				case 'groups':
					global $bp;
					$group       = $bp->groups->current_group;
					$group_meta  = groups_get_groupmeta( $group->id, 'meta' );
					$og_title    = isset( $group->name ) && strlen( $group->name ) > 0 ? "{$group->name} - " . __( 'Mozilla Community Portal', 'community-portal' ) : __( 'Groups - Mozilla Community Portal', 'community-portal' );
					$theme_title = $og_title;
					$og_desc     = isset( $group->description ) && strlen( $group->description ) > 0 ? wp_strip_all_tags( $group->description ) : get_bloginfo( 'description' );
					$og_image    = isset( $group_meta['group_image_url'] ) && strlen( $group_meta['group_image_url'] ) > 0 ? $group_meta['group_image_url'] : get_stylesheet_directory_uri() . '/images/group.png';
					break;

				case 'events':
					global $post;
					$event = em_get_event( $post->ID, 'post_id' );


					$og_title    = isset( $event->event_name ) && strlen( $event->event_name ) > 0 ? "{$event->event_name} - " . __( 'Mozilla Community Portal', 'community-portal' ) : __( 'Events - Mozilla Community Portal', 'community-portal' );
					$theme_title = $og_title;
					$og_desc     = isset( $event->post_content ) && strlen( $event->post_content ) ? wp_strip_all_tags( $event->post_content ) : wp_strip_all_tags( get_bloginfo( 'description' ) );
					
					if ( isset( $event->event_attributes ) ) {
						$event_meta = unserialize( $event->event_attributes['event-meta'] );
						$og_image   = isset( $event_meta->image_url ) && strlen( $event_meta->image_url ) > 0 ? $event_meta->image_url : get_stylesheet_directory_uri() . '/images/event.jpg';
					} else {
						$og_image = get_stylesheet_directory_uri() . '/images/event.jpg';
					}
					break;

				case 'people':
					$user_id     = bp_displayed_user_id();
					$du          = get_user_by( 'ID', $user_id );
					$meta        = get_user_meta( $user_id );
					$og_title    = $du ? "{$du->user_nicename} - " . __( 'Mozilla Community Portal', 'community-portal' ) : __( 'People - Mozilla Community Portal', 'community-portal' );
					$og_image    = get_stylesheet_directory_uri() . '/images/group.png';
					$theme_title = $og_title;
					$og_desc     = wp_strip_all_tags( get_bloginfo( 'description' ) );

					break;
				case 'activities':
					global $post;
					$theme_title = ( 'activity' === trim( $post->post_type ) ) ? "{$post->post_title} - " . __( 'Mozilla Community Portal', 'community-portal' ) : __( 'Activities - Mozilla Community Portal', 'community-portal' );
					$og_title    = $title;
					$og_image    = get_the_post_thumbnail_url();
					$og_desc     = wp_strip_all_tags( substr( $post->post_content, 0, 155 ) );

					break;
				case 'campaigns':
					global $post;
					$theme_title = ( 'campaign' === trim( $post->post_type ) ) ? "{$post->post_title} - " . __( 'Mozilla Community Portal', 'community-portal' ) : __( 'Campaigns - Mozilla Community Portal', 'community-portal' );
					$og_title    = $title;
					$og_image    = get_the_post_thumbnail_url();
					$og_desc     = wp_strip_all_tags( substr( $post->post_content, 0, 155 ) );

					break;
				default:
					$theme_title = get_bloginfo( 'name' ) . ' - ' . get_bloginfo( 'description' );
					$options     = wp_load_alloptions();
					$og_title    = ( isset( $options['default_open_graph_ttitle '] ) ) ? $options['default_open_graph_title'] : '';
					$og_desc     = wp_strip_all_tags( $options['default_open_graph_desc'] );
					$og_image    = get_stylesheet_directory_uri() . '/images/homepage-hero.jpg';
			}
			?>
		<?php else : ?>
			<?php
			$options     = wp_load_alloptions();
			$theme_title = get_bloginfo( 'name' ) . ' - ' . get_bloginfo( 'description' );
			$og_title    = $options['default_open_graph_title'];
			$og_desc     = wp_strip_all_tags( $options['default_open_graph_desc'] );
			$og_image    = get_stylesheet_directory_uri() . '/images/homepage-hero.jpg';
			?>
		<?php endif; ?>

		<meta property="og:title" content="<?php echo esc_attr( $og_title ); ?>">
		<meta property="og:description" content="<?php echo esc_attr( $og_desc ); ?>">
		<meta property="og:image" content="<?php echo esc_attr( $og_image ); ?>">
		<?php if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) : ?>
			<?php
			$http_host   = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );
			$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			?>
		<meta property="og:url" content="<?php echo esc_attr( $protocol . $http_host . htmlspecialchars( $request_uri, ENT_QUOTES, 'UTF-8' ) ); ?>">
		<?php endif; ?>
		<meta name="twitter:card" content="summary_large_image">

		<!--  Non-Essential, But Recommended -->
		<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		<title><?php echo esc_html( $theme_title ); ?></title>


		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
		<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
		<?php endif; ?>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<nav class="nav">
			<div class="nav__header">
				<div class="nav__container">
					<a href="/">
						<img src="<?php echo esc_attr( get_stylesheet_directory_uri() . '/images/logo.svg' ); ?>" />
					</a>
					<div class="nav__login">
						<?php if ( is_user_logged_in() ) : ?>
							<a href="/people/<?php echo esc_attr( $user->user_nicename ); ?>" class="nav__avatar-link">
								<div class="nav__avatar
								<?php
								if ( ! $avatar ) :
									?>
									nav__avatar--empty<?php endif; ?>" 
									<?php
									if ( $avatar ) :
										?>
									style="background-image: url('<?php echo esc_attr( $avatar ); ?>')"<?php endif; ?> data-user="<?php echo esc_attr( $user->user_nicename ); ?>"></div>
								<?php echo esc_html( $user->user_nicename ); ?>
							</a>
							<a href="/wp-login.php?action=logout" class="nav__logout-link"><?php esc_attr_e( 'Log Out', 'community-portal' ); ?></a>
						<?php else : ?>
							<a href="/wp-login.php?action=login" class="nav__login-link"><?php esc_attr_e( 'Log In / Sign Up', 'community-portal' ); ?></a>
						<?php endif; ?>
					</div>
					<div class="nav__search-container">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="nav__search-icon">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M9 5C9 7.20914 7.20914 9 5 9C2.79086 9 1 7.20914 1 5C1 2.79086 2.79086 1 5 1C7.20914 1 9 2.79086 9 5ZM8.00021 9.00021C7.16451 9.62799 6.1257 10 5 10C2.23858 10 0 7.76142 0 5C0 2.23858 2.23858 0 5 0C7.76142 0 10 2.23858 10 5C10 6.27532 9.52253 7.43912 8.73661 8.32239L11.7071 11.2929L11 12L8.00021 9.00021Z" fill="#737373" />
						</svg>
						<form method="GET" action="/">
							<?php wp_nonce_field( 'site_search', 'site_search_nonce' ); ?>
							<input type="text" class="nav__search" placeholder="<?php esc_attr_e( 'Seach', 'community-portal' ); ?>" name="s" value="<?php echo esc_attr( $search_text ); ?>" />
						</form>
					</div>
				</div>
			</div>
			<div class="nav__menu">
				<div class="nav__container">
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'mozilla-theme-menu',
								'menu_id'        => 'Mozilla Main Menu',
								'menu_class'     => 'menu',
							)
						);
						?>
				</div>
			</div>
		</nav>
		<nav class="nav nav--mobile">
			<div class="nav__container">
				<a href="/">
					<svg width="193" height="40" viewBox="0 0 193 40" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect y="23.9711" width="56.5229" height="16.0289" fill="white"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M21.0859 31.0916C20.0511 31.0916 19.4083 31.8533 19.4083 33.1747C19.4083 34.3872 19.9727 35.32 21.0702 35.32C22.1206 35.32 22.8104 34.4805 22.8104 33.1435C22.8104 31.7289 22.0422 31.0916 21.0859 31.0916Z" fill="black"/>
						<mask id="mask0" mask-type="alpha" maskUnits="userSpaceOnUse" x="0" y="23" width="57" height="17">
						<path d="M0 23.9711H56.5212V39.9962H0V23.9711Z" fill="black"/>
						</mask>
						<g mask="url(#mask0)">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M48.8109 34.6203C48.8109 35.071 49.0302 35.4287 49.6418 35.4287C50.363 35.4287 51.1312 34.9156 51.1781 33.7497C50.8491 33.7031 50.4885 33.6564 50.1593 33.6564C49.438 33.6564 48.8109 33.8585 48.8109 34.6203Z" fill="black"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M52.7293 36.8591C51.7888 36.8591 51.2713 36.315 51.1772 35.4599C50.7697 36.1751 50.0484 36.8591 48.9039 36.8591C47.885 36.8591 46.7249 36.315 46.7249 34.8536C46.7249 33.1281 48.4022 32.7239 50.0171 32.7239C50.4091 32.7239 50.8169 32.7395 51.1772 32.7862V32.5529C51.1772 31.8378 51.1617 30.9828 50.0171 30.9828C49.594 30.9828 49.2647 31.014 48.9352 31.1849L48.7069 31.9738L47.092 31.8028L47.3676 30.1901C48.6063 29.6926 49.2331 29.5527 50.3935 29.5527C51.9143 29.5527 53.1999 30.3299 53.1999 31.9312V34.9781C53.1999 35.3823 53.3567 35.5221 53.686 35.5221C53.7801 35.5221 53.8739 35.5065 53.9838 35.4755L53.9994 36.5326C53.6232 36.7348 53.1685 36.8591 52.7293 36.8591ZM41.8775 36.7036L44.9345 26.0083H46.9255L43.8685 36.7036H41.8775ZM37.7697 36.7036L40.8267 26.0083H42.8177L39.7607 36.7036H37.7697ZM34.3291 32.211H36.4456V29.6925H34.3291V32.211ZM34.3291 36.7037H36.4456V34.1852H34.3291V36.7037ZM32.4093 36.7037H26.2322L26.0284 35.6465L29.9164 31.216H27.706L27.3924 32.3043L25.9343 32.1488L26.1851 29.6925H32.3938L32.5506 30.7496L28.6308 35.1802H30.9199L31.2492 34.0921L32.8485 34.2474L32.4093 36.7037ZM21.0067 36.859C18.8273 36.859 17.3222 35.5377 17.3222 33.2992C17.3222 31.2471 18.5765 29.5527 21.1164 29.5527C23.6562 29.5527 24.8948 31.2471 24.8948 33.2059C24.8948 35.4445 23.2642 36.859 21.0067 36.859ZM16.4422 36.7037H13.479V32.755C13.479 31.5425 13.0715 31.0762 12.2719 31.0762C11.2998 31.0762 10.9079 31.7601 10.9079 32.7396V35.1802H11.8485V36.7037H8.88541V32.755C8.88541 31.5425 8.47774 31.0762 7.67814 31.0762C6.70608 31.0762 6.31413 31.7601 6.31413 32.7396V35.1802H7.66254V36.7037H3.35091V35.1802H4.29168V31.216H3.35091V29.6925H6.31413V30.7496C6.7374 30.0036 7.47437 29.5527 8.46202 29.5527C9.4811 29.5527 10.4218 30.0346 10.7667 31.0606C11.1587 30.1278 11.9583 29.5527 13.0715 29.5527C14.3414 29.5527 15.5016 30.3144 15.5016 31.9779V35.1802H16.4422V36.7037ZM-0.00262451 39.9962H56.5203V23.9711H-0.00262451V39.9962Z" fill="black"/>
						</g>
						<path d="M13.2761 14.9207C12.7199 15.4451 11.9254 15.8661 10.8926 16.1839C9.87562 16.4859 8.81894 16.6368 7.72253 16.6368C5.48204 16.6368 3.63086 15.9138 2.16898 14.4678C0.722994 13.006 0 11.1309 0 8.84278C0 7.55569 0.27013 6.28449 0.810389 5.02918C1.35065 3.77387 2.19282 2.73308 3.3369 1.9068C4.48098 1.08052 5.94285 0.667379 7.72253 0.667379C9.02551 0.667379 10.1378 0.850114 11.0594 1.21558C11.981 1.58105 12.712 2.01008 13.2522 2.50267L13.6574 5.8634L11.4885 6.07792L11.1071 3.93277C10.1537 3.31306 9.00962 3.00321 7.67486 3.00321C6.21298 3.00321 5.03712 3.51169 4.14729 4.52864C3.27334 5.5456 2.83636 6.93598 2.83636 8.69976C2.83636 10.543 3.30512 11.9413 4.24263 12.8947C5.18013 13.8322 6.41161 14.301 7.93705 14.301C9.20824 14.301 10.249 14.0785 11.0594 13.6336L11.4408 11.3216L13.6098 11.5361L13.2761 14.9207Z" fill="black"/>
						<path d="M26.398 11.0594C26.398 12.7279 25.8657 14.0785 24.801 15.1114C23.7523 16.1283 22.3858 16.6368 20.7014 16.6368C19.0648 16.6368 17.7459 16.1442 16.7448 15.159C15.7596 14.1739 15.267 12.8391 15.267 11.1548C15.267 9.59755 15.7358 8.26279 16.6733 7.15049C17.6108 6.0223 19.0012 5.45821 20.8444 5.45821C22.7036 5.45821 24.0939 6.01436 25.0156 7.12666C25.9372 8.23895 26.398 9.54988 26.398 11.0594ZM23.7523 10.9879C23.7523 9.84384 23.4742 8.96989 22.9181 8.36608C22.3778 7.76226 21.6707 7.46035 20.7968 7.46035C19.891 7.46035 19.1839 7.78609 18.6754 8.43758C18.167 9.07318 17.9127 9.93124 17.9127 11.0118C17.9127 12.0287 18.1511 12.8868 18.6278 13.5859C19.1204 14.2851 19.8434 14.6347 20.7968 14.6347C21.7184 14.6347 22.4414 14.301 22.9657 13.6336C23.4901 12.9503 23.7523 12.0684 23.7523 10.9879Z" fill="black"/>
						<path d="M47.5051 16.3985H42.7858V14.444H43.3817V10.249C43.3817 9.23208 43.199 8.51703 42.8335 8.10389C42.4839 7.67486 41.9834 7.46035 41.3319 7.46035C40.4897 7.46035 39.87 7.74637 39.4728 8.31841C39.0914 8.89044 38.8928 9.54193 38.8769 10.2729V14.444H40.4738V16.3985H35.7545V14.444H36.3265V10.249C36.3265 9.23208 36.1438 8.51703 35.7783 8.10389C35.4288 7.67486 34.9362 7.46035 34.3006 7.46035C33.4743 7.46035 32.8625 7.74637 32.4653 8.31841C32.068 8.87455 31.8615 9.5181 31.8456 10.249V14.444H33.8477V16.3985H27.6983V14.444H29.2952V7.67486H27.7221V5.69656H31.8456V7.46035C32.1793 6.87242 32.6242 6.39572 33.1803 6.03025C33.7365 5.66478 34.4118 5.48204 35.2063 5.48204C36.0008 5.48204 36.7159 5.68067 37.3515 6.07792C37.9871 6.45928 38.432 7.0631 38.6862 7.88938C39.004 7.15844 39.4648 6.57845 40.0686 6.14942C40.6725 5.7045 41.4113 5.48204 42.2853 5.48204C43.2546 5.48204 44.1047 5.7919 44.8356 6.41161C45.5666 7.03132 45.932 8.00061 45.932 9.31947V14.444H47.5051V16.3985Z" fill="black"/>
						<path d="M68.2676 16.3985H63.5483V14.444H64.1442V10.249C64.1442 9.23208 63.9615 8.51703 63.596 8.10389C63.2464 7.67486 62.7459 7.46035 62.0944 7.46035C61.2522 7.46035 60.6325 7.74637 60.2353 8.31841C59.8539 8.89044 59.6553 9.54193 59.6394 10.2729V14.444H61.2363V16.3985H56.517V14.444H57.089V10.249C57.089 9.23208 56.9063 8.51703 56.5408 8.10389C56.1913 7.67486 55.6987 7.46035 55.0631 7.46035C54.2368 7.46035 53.625 7.74637 53.2278 8.31841C52.8305 8.87455 52.624 9.5181 52.6081 10.249V14.444H54.6102V16.3985H48.4608V14.444H50.0577V7.67486H48.4846V5.69656H52.6081V7.46035C52.9418 6.87242 53.3867 6.39572 53.9428 6.03025C54.499 5.66478 55.1743 5.48204 55.9688 5.48204C56.7633 5.48204 57.4783 5.68067 58.1139 6.07792C58.7495 6.45928 59.1945 7.0631 59.4487 7.88938C59.7665 7.15844 60.2273 6.57845 60.8311 6.14942C61.435 5.7045 62.1738 5.48204 63.0478 5.48204C64.0171 5.48204 64.8672 5.7919 65.5981 6.41161C66.3291 7.03132 66.6945 8.00061 66.6945 9.31947V14.444H68.2676V16.3985Z" fill="black"/>
						<path d="M81.9035 16.3985H77.6847V14.6108C76.9855 15.9456 75.8097 16.613 74.1571 16.613C73.1402 16.613 72.2583 16.3111 71.5114 15.7072C70.7805 15.0875 70.415 14.1103 70.415 12.7755V7.65103H68.8181V5.69656H72.9654V11.846C72.9654 12.8629 73.1481 13.5859 73.5136 14.015C73.879 14.4281 74.3955 14.6347 75.0628 14.6347C75.8573 14.6347 76.485 14.3566 76.9458 13.8004C77.4225 13.2443 77.6688 12.5849 77.6847 11.8221V7.65103H75.7779V5.69656H80.235V14.444H81.9035V16.3985Z" fill="black"/>
						<path d="M95.9702 16.3985H90.2498V14.444H91.8229V10.249C91.8229 9.23208 91.6402 8.51703 91.2747 8.10389C90.9092 7.67486 90.4008 7.46035 89.7493 7.46035C88.9389 7.46035 88.2953 7.72253 87.8186 8.2469C87.3578 8.77127 87.1195 9.41481 87.1036 10.1775V14.444H88.7005V16.3985H82.9563V14.444H84.5533V7.67486H82.8848V5.69656H87.1036V7.43651C87.8504 6.13353 89.0342 5.48204 90.655 5.48204C91.672 5.48204 92.5459 5.7919 93.2769 6.41161C94.0078 7.03132 94.3733 8.00061 94.3733 9.31947V14.444H95.9702V16.3985Z" fill="black"/>
						<path d="M98.4714 3.4799V0.667379H101.069V3.4799H98.4714ZM102.738 16.3985H97.0175V14.444H98.6144V7.67486H96.9221V5.69656H101.141V14.444H102.738V16.3985Z" fill="black"/>
						<path d="M112.51 13.2522C112.208 15.5086 110.953 16.6368 108.744 16.6368C107.6 16.6368 106.686 16.3349 106.003 15.7311C105.336 15.1273 105.002 14.2851 105.002 13.2046V7.57952H103.429V5.69656H105.002V2.3835H107.552V5.69656H111.533V7.57952H107.552V12.7994C107.552 13.9276 108.045 14.4917 109.03 14.4917C109.3 14.4917 109.602 14.3963 109.936 14.2056C110.27 13.9991 110.5 13.4826 110.627 12.6564L112.51 13.2522Z" fill="black"/>
						<path d="M124.59 7.65103H123.47L119.894 16.3985L118.703 19.4732H120.276V21.4753H114.555V19.4732H116.367L117.416 16.4938L113.626 7.65103H112.458V5.69656H117.725V7.65103H116.367L118.846 13.4429H118.989L120.991 7.65103H119.775V5.69656H124.59V7.65103Z" fill="black"/>
						<path d="M142.589 5.83957C142.589 7.5398 142.041 8.82688 140.945 9.70083C139.864 10.5589 138.561 10.9879 137.036 10.9879H134.414V14.1341H136.893V16.3985H129.981V14.1341H131.768V3.17005H129.981V0.905729H136.893C138.816 0.905729 140.246 1.36654 141.183 2.28816C142.121 3.20978 142.589 4.39358 142.589 5.83957ZM139.753 5.88724C139.753 5.02918 139.507 4.3618 139.014 3.8851C138.537 3.4084 137.727 3.17005 136.583 3.17005H134.414V8.74743H136.583C137.711 8.74743 138.522 8.49319 139.014 7.98471C139.507 7.46035 139.753 6.76119 139.753 5.88724Z" fill="black"/>
						<path d="M154.371 11.0594C154.371 12.7279 153.839 14.0785 152.774 15.1114C151.726 16.1283 150.359 16.6368 148.675 16.6368C147.038 16.6368 145.719 16.1442 144.718 15.159C143.733 14.1739 143.24 12.8391 143.24 11.1548C143.24 9.59755 143.709 8.26279 144.647 7.15049C145.584 6.0223 146.975 5.45821 148.818 5.45821C150.677 5.45821 152.067 6.01436 152.989 7.12666C153.91 8.23895 154.371 9.54988 154.371 11.0594ZM151.726 10.9879C151.726 9.84384 151.448 8.96989 150.891 8.36608C150.351 7.76226 149.644 7.46035 148.77 7.46035C147.864 7.46035 147.157 7.78609 146.649 8.43758C146.14 9.07318 145.886 9.93124 145.886 11.0118C145.886 12.0287 146.124 12.8868 146.601 13.5859C147.094 14.2851 147.817 14.6347 148.77 14.6347C149.692 14.6347 150.415 14.301 150.939 13.6336C151.463 12.9503 151.726 12.0684 151.726 10.9879Z" fill="black"/>
						<path d="M162.202 5.52971C162.917 5.52971 163.585 5.72834 164.205 6.12559L164.657 9.46248L162.679 9.70083L162.274 7.86554C161.956 7.70664 161.686 7.62719 161.464 7.62719C161.019 7.62719 160.629 7.80993 160.296 8.1754C159.978 8.54086 159.819 9.04934 159.819 9.70083V14.444H161.94V16.3985H155.672V14.444H157.269V7.67486H155.6V5.69656H159.628V7.6987C159.819 7.09488 160.121 6.5864 160.534 6.17326C160.963 5.74423 161.519 5.52971 162.202 5.52971Z" fill="black"/>
						<path d="M174.611 13.2522C174.309 15.5086 173.054 16.6368 170.845 16.6368C169.701 16.6368 168.788 16.3349 168.104 15.7311C167.437 15.1273 167.103 14.2851 167.103 13.2046V7.57952H165.53V5.69656H167.103V2.3835H169.654V5.69656H173.634V7.57952H169.654V12.7994C169.654 13.9276 170.146 14.4917 171.131 14.4917C171.402 14.4917 171.703 14.3963 172.037 14.2056C172.371 13.9991 172.601 13.4826 172.728 12.6564L174.611 13.2522Z" fill="black"/>
						<path d="M176.272 6.48311C177.067 6.10175 177.774 5.83957 178.394 5.69656C179.014 5.55355 179.729 5.48204 180.539 5.48204C181.731 5.48204 182.708 5.78395 183.471 6.38777C184.249 6.99159 184.639 7.89732 184.639 9.10496V13.9196C184.639 14.5552 184.901 14.873 185.425 14.873C185.568 14.873 185.751 14.8412 185.973 14.7777L185.997 16.1839C185.473 16.4859 184.901 16.6368 184.281 16.6368C182.93 16.6368 182.199 15.9297 182.088 14.5155V14.4678C181.77 15.024 181.318 15.5245 180.73 15.9694C180.158 16.4144 179.443 16.6368 178.584 16.6368C177.838 16.6368 177.099 16.4144 176.368 15.9694C175.653 15.5086 175.295 14.7141 175.295 13.5859C175.295 12.2671 175.82 11.4011 176.868 10.9879C177.917 10.5589 179.053 10.3444 180.277 10.3444C180.579 10.3444 180.889 10.3523 181.206 10.3682C181.524 10.3841 181.818 10.4079 182.088 10.4397V9.84384C182.088 9.20824 181.977 8.6362 181.755 8.12773C181.532 7.61925 180.976 7.36501 180.086 7.36501C179.752 7.36501 179.435 7.38884 179.133 7.43651C178.847 7.48418 178.569 7.57952 178.298 7.72253L177.869 9.36714L175.82 9.15263L176.272 6.48311ZM182.088 12.1558V11.7745C181.818 11.7427 181.54 11.7109 181.254 11.6791C180.968 11.6474 180.674 11.6315 180.372 11.6315C179.737 11.6315 179.164 11.7427 178.656 11.9652C178.163 12.1876 177.917 12.6325 177.917 13.2999C177.917 14.2692 178.434 14.7538 179.466 14.7538C180.07 14.7538 180.642 14.5393 181.182 14.1103C181.723 13.6654 182.025 13.0139 182.088 12.1558Z" fill="black"/>
						<path d="M192.553 16.3985H186.833V14.444H188.43V1.95447H186.738V0H190.956V14.444H192.553V16.3985Z" fill="black"/>
					</svg>
				</a>
				<div class="nav__content">
					<input id="nav-trigger" type="checkbox" class="nav__trigger" />
					<div class="nav__avatar-container">
					<?php if ( is_user_logged_in() ) : ?>
						<a href="/people/<?php echo esc_attr( $user->user_nicename ); ?>" class="nav__avatar-link">
							<div class="nav__avatar
							<?php
							if ( ! $avatar ) :
								?>
								nav__avatar--empty<?php endif; ?>" 
								<?php
								if ( $avatar ) :
									?>
								style="background-image: url('<?php echo esc_attr( $avatar ); ?>')"<?php endif; ?>></div>
							<span class="nav__username"><?php echo esc_html( $user->user_nicename ); ?></span>
						</a>
					<?php endif; ?>
					</div>
					<label for="nav-trigger" class="nav__label">
						<span class="nav__hamburger-line"></span>
						<span class="nav__hamburger-line"></span>
						<span class="nav__hamburger-line"></span>
					</label>

					<?php
						$items = wp_get_nav_menu_items( 'Mozilla Main Menu' );
					?>
					<div class="nav__menu-container">
						<div class="nav__user-container">
						<?php if ( is_user_logged_in() ) : ?>
							<a href="/people/<?php echo esc_attr( $user->user_nicename ); ?>" class="nav__avatar-link">
								<div class="nav__avatar
								<?php
								if ( ! $avatar ) :
									?>
									nav__avatar--empty<?php endif; ?>" 
									<?php
									if ( $avatar ) :
										?>
									style="background-image: url('<?php echo esc_attr( $avatar ); ?>')"<?php endif; ?>>
								</div>
								<?php echo esc_html( $user->user_nicename ); ?>
							</a>
							<a href="/wp-login.php?action=logout" class="nav__logout-link"><?php esc_attr_e( 'Log Out', 'community-portal' ); ?></a>
						<?php else : ?>
							<a href="/wp-login.php?action=login" class="nav__login-link nav__login-link--mobile"><?php esc_attr_e( 'Log In / Sign Up', 'community-portal' ); ?></a>
						<?php endif; ?>
						</div>
						<div class="nav__search-container">
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="nav__search-icon">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M9 5C9 7.20914 7.20914 9 5 9C2.79086 9 1 7.20914 1 5C1 2.79086 2.79086 1 5 1C7.20914 1 9 2.79086 9 5ZM8.00021 9.00021C7.16451 9.62799 6.1257 10 5 10C2.23858 10 0 7.76142 0 5C0 2.23858 2.23858 0 5 0C7.76142 0 10 2.23858 10 5C10 6.27532 9.52253 7.43912 8.73661 8.32239L11.7071 11.2929L11 12L8.00021 9.00021Z" fill="#737373"/>
							</svg>
							<form method="GET" action="/">
								<input type="text" class="nav__search" placeholder="<?php esc_attr_e( 'Seach', 'community-portal' ); ?>" name="s" value="<?php echo esc_attr( $original_query ); ?>" />
							</form>
						</div>
						<ul class="menu--mobile">
            <?php if (isset($items) && is_array($items) && count($items) > 0):?>
						<?php foreach ( $items as $item ) : ?>
							<li class="menu-item"><a href="<?php echo esc_attr( $item->url ); ?>" class="menu-item__link"><?php echo esc_html( $item->post_title ); ?></a></li>
						<?php endforeach; ?>
            <?php endif; ?>
						</ul>
					</div>
				</div>
			</div>
		</nav>
