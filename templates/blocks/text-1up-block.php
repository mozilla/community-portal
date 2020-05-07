<?php
/**
 * Text 1up Block - Campaigns
 *
 * Text 1up block for campaigns
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

 $title = trim($block['title']);

?>
<div class="campaign__1up-text-block
<?php
if ( 'grey' === $block['background_color'] ) :
	?>
	campaign__1up-text-block--grey<?php endif; ?>">
	<div class="campaign__block-container
	<?php
	if ( $block['keyline'] ) :
		?>
		campaign__block-container--keyline<?php endif; ?>">
    <?php if (!empty($title)) : ?>
		<h2 class="campaign__heading-2"><?php echo wp_kses( $title, wp_kses_allowed_html( 'post' ) ); ?></h2>
    <?php endif; ?>
		<div class="campaign__block-content">
			<?php
			print wp_kses(
				wpautop( substr( trim( $block['copy'] ), 0, 3000 ) ),
				wp_kses_allowed_html( 'post' )
			);
			?>
		</div>
		<?php if ( $block['cta'] ) : ?>
			<a href="
			<?php
			if ( $block['cta_link'] ) :
				?>
					<?php print esc_attr( $block['cta_link'] ); ?>
				<?php
else :
	?>
				#<?php endif; ?>" class="campaign__cta"><?php print esc_html( $block['cta'] ); ?></a>
		<?php endif; ?>
	</div>
</div>
