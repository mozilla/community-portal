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
		<h2 class="campaign__heading-2"><?php print esc_html( $block['title'] ); ?></h2>
		<div class="campaign__block-content">
			<?php
			print wp_kses(
				wpautop( substr( trim( $block['copy'] ), 0, 3000 ) ),
				array(
					'p'  => array(),
					'br' => array(),
					'ul' => array(),
					'li' => array(),
				)
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
