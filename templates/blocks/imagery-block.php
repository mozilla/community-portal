<?php
/**
 * Imagery Block - Campaigns
 *
 * Imagery block for events
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<div class="campaign__imagery-block
<?php
if ( 'grey' === $block['background_color'] ) :
	?>
	campaign__imagery-block--grey<?php endif; ?>">
	<div class="campaign__block-container
	<?php
	if ( $block['keyline'] ) :
		?>
		campaign__block-container--keyline<?php endif; ?>">
		<h2 class="campaign__heading-2 campaign__heading-2--center"><?php print esc_html( $block['title'] ); ?></h2>

		<div class="campaign__block-content">
			<?php
			print wp_kses(
				wpautop( substr( trim( $block['copy'] ), 0, 3000 ) ),
				array(
					'p'  => array(),
					'br' => array(),
				)
			);

			?>
			<?php if ( isset( $block['images'] ) ) : ?>
			<div class="campaign__imagery-images-container">
				<?php foreach ( $block['images'] as $image ) : ?>
			<div class="campaign__imagery-image-container">
				<div style="background-image: url('<?php print esc_attr( $image['image']['url'] ); ?>'); background-color: 
					<?php
					if ( isset( $image['background_color'] ) && strlen( $image['background_color'] ) > 0 ) :
						?>
						<?php print esc_attr( $image['background_color'] ); ?>
						<?php
						else :
							?>
					#ffffff<?php endif; ?>;" class="campaign__imagery-image"></div>
				<div class="campaign__imagery-caption"><?php print esc_html( $image['caption'] ); ?></div>
			</div>
			<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
