<?php
/**
 * Text Image Block - Campaigns
 *
 * Text Image block for campaigns
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<div class="campaign__image-text-block
<?php
if ( 'grey' === $block['background_color'] ) :
	?>
	campaign__image-text-block--grey<?php endif; ?>">
	<div class="campaign__block-container<?php echo $block['keyline'] ? ' campaign__block-container--keyline' : ''; ?>">
		<div class="campaign__block-content">
			<div class="campaign__image-text-block-container">
				<div class="campaign__image-text-block-text-container">
					<h2 class="campaign__heading-2"><?php print esc_html( $block['title'] ); ?></h2>
					<?php
					print wp_kses(
						wpautop( substr( trim( $block['copy'] ), 0, 3000 ) ),
						wp_kses_allowed_html( 'post' )
					);
					?>
				</div>
				<div class="campaign__image-text-block-image-container">
					<img src="<?php print esc_attr( $block['image']['url'] ); ?>"  class="campaign__image-text-block-image"/>
				</div>
			</div>
		</div>
	</div>
</div>
