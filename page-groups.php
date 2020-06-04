<?php
/**
 * Groups
 *
 * Main page for all groups for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<?php get_header(); ?>
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
