<?php
/**
 * Edit Events
 *
 * Page to edit all events for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php
	$user = wp_get_current_user()->data;
	$meta = get_user_meta( $user->ID );

if ( $user->ID && ( ! isset( $meta['agree'][0] ) || 'I Agree' !== $meta['agree'][0] ) ) {
	wp_safe_redirect( "/people/{$user->user_nicename}/profile/edit/group/1/" );
	exit();
}


if ( ! empty( $_GET['success'] ) && '1' === $_GET['success'] ) {
	wp_safe_redirect( '/events' );
	exit();
}

if ( isset( $_REQUEST['event_id'] ) && isset( $_REQUEST['nonce'] ) && wp_verify_nonce( 'edit-event' ) ) {
	$event_id = sanitize_key( $_REQUEST['event_id'] );
}


get_header();
?>
<div class="events__header events__header--edit">
	<div class="row middle-md event-creator__container">
		<div class="col-md-6 events__header__text">
		<h1 class="title"><?php ( $event_id ? esc_html_e( 'Edit Event', 'community-portal' ) : esc_html_e( 'Create Event', 'community-portal' ) ); ?></h1>
		</div>
	</div>
</div>
<div class="content event-creator__container-main">
<?php if ( isset( $user ) ) : ?>
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<?php the_content(); ?>
		<?php endwhile; ?>
	<?php endif; ?>
<?php else : ?>
	<p><?php esc_html_e( 'You are not authorized to edit this event.', 'community-portal' ); ?></p>
<?php endif; ?>
</div>
<?php get_footer(); ?>
