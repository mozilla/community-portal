<?php
/**
 * Index
 *
 * Index file for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

if ( is_user_logged_in() && ( ! isset( $meta['agree'][0] ) || 'I Agree' !== $meta['agree'][0] ) ) {
	$user = wp_get_current_user();
	$meta = get_user_meta( $user->ID );

	wp_safe_redirect( "/people/{$user->user_nicename}/profile/edit/group/1/" );
	exit();
}


	get_header();
?>
	<div class="content">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<?php the_content(); ?>
		<?php endwhile; ?>
	<?php endif; ?>
	</div>
<?php get_footer(); ?>
