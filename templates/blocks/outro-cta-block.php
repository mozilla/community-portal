<?php
/**
 * Outro CTA Block - Campaigns
 *
 * Outro CTA block for campaigns
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<div class="campaign__outro-cta-block
<?php
if ( 'grey' === $block['background_color'] ) :
	?>
	campaign__outro-cta-block--grey<?php endif; ?>">
	<div class="campaign__block-container
	<?php
	if ( $block['keyline'] ) :
		?>
		campaign__block-container--keyline<?php endif; ?>">
		<h4 class="campaign__heading-4 campaign__heading-4--center"><?php print esc_html( $block['title'] ); ?></h2>
		<div class="campaign__block-content campaign__block-content--center">
			<a href="<?php print esc_attr( $block['cta_link'] ); ?>" class="campaign__outro-cta"><?php print esc_html( $block['cta'] ); ?></a>
		</div>
	</div>
</div>
