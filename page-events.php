<?php
/**
 * Events
 *
 * Main page for all events for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php 
	get_header();
	mozilla_match_categories(); 
?>
	<div class="events__header">
		<div class="row middle-md events__container">
			<div class="col-md-6 events__header__text">
				<h1 class="events__title"><?php esc_html_e('Events', 'community-portal'); ?></h1>
				<p class="events__text"><?php esc_html_e( 'Ready to join the movement? Check out what\'s happening soon in your area. ', 'community-portal' ); ?></p>
				<p class="events__text"><?php esc_html_e( 'Explore community events near you, ', 'community-portal' ); ?><a href="<?php echo esc_url_raw( add_query_arg( array( 'action' => 'edit' ), get_home_url( '', 'events/edit-event' ) ) ); ?>"><?php esc_html_e( 'organize your own!', 'community-portal' ); ?></a></p>
			</div>
		</div>
	</div>
	<div class="content events__container">
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