<?php
/**
 * Text 3up Block - Campaigns
 *
 * Text 3up block for campaigns
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<div class="campaign__text-card-block">
	<div class="campaign__block-container
	<?php
	if ( $block['keyline'] ) :
		?>
		campaign__block-container--keyline<?php endif; ?>">
		<h2 class="campaign__heading-2"><?php print esc_html( $block['title'] ); ?></h2>
		<div class="campaign__block-content">
			<div class="campaign__card">
				<?php
				print wp_kses(
					wpautop( substr( trim( $block['copy'] ), 0, 3000 ) ),
					array(
						'p'  => array(
							'class' => array(),
						),
						'br' => array(),
						'ul' => array(
							'class' => array(),
						),
						'ol' => array(
							'class' => array(),
						),
						'li' => array(
							'class' => array(),
						),
						'a'  => array(
							'href'  => array(),
							'class' => array(),
						),
					)
				);
				?>
			</div>
		</div>
	</div>
</div>
