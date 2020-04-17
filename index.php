<?php
/**
 * Index
 *
 * Main footer file for the theme.
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @since  1.0.0
 */

global $bp;
$group = $bp->groups->current_group;
$user  = wp_get_current_user();
$meta  = get_user_meta( $user->ID );


if ( $group ) {
	$is_admin   = groups_is_user_admin( $user->ID, $group->id );
	$edit_group = bp_is_group_admin_page() && $is_admin;

	if ( $edit_group && ( ! isset( $meta['agree'][0] ) || 'I Agree' !== $meta['agree'][0] ) ) {
		wp_safe_redirect( "/people/{$user->user_nicename}/profile/edit/group/1/" );
		exit();
	}
} else {
	if ( ( ! isset( $meta['agree'][0] ) || 'I Agree' !== $meta['agree'][0] ) ) {
		wp_safe_redirect( "/people/{$user->user_nicename}/profile/edit/group/1/" );
		exit();
	}
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
