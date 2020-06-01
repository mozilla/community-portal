<?php
/**
 * Remove Page
 *
 * Remove Page
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php get_header(); ?>
	<div class="content">
		<div class="page">
			<div class="page__container">
				<h1 class="page__title"><?php echo esc_html( $post->post_title ); ?></h1>
				<section class="page__content">
					<?php
						echo wp_kses( $post->post_content, wp_kses_allowed_html( 'post' ) );
					?>
				</section>
			</div>
		</div>
	</div>
<?php get_footer(); ?>
