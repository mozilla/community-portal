<?php
/**
 * Text 2up Block - Campaigns
 *
 * Text 2up block for campaigns
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<div class="campaign__2up-text-block
<?php
if ( 'grey' === $block['background_color'] ) :
	?>
	campaign__2up-text-block--grey<?php endif; ?>">
	<div class="campaign__block-container
	<?php
	if ( $block['keyline'] ) :
		?>
		campaign__block-container--keyline<?php endif; ?>">
		<h2 class="campaign__heading-2 campaign__heading-2--center"><?php print esc_html( $block['title'] ); ?></h2>
		<div class="campaign__block-content">
			<div class="campaign__columns-container">
				<div class="campaign__column">
					<h3 class="campaign__heading-3"><?php print esc_html( $block['column_1_title'] ); ?></h3>
					<?php
					print wp_kses(
						wpautop( substr( trim( $block['column_1_copy'] ), 0, 3000 ) ),
						array(
							'p'  => array(),
							'br' => array(),
							'ul' => array(),
							'li' => array(),
						)
					);
					?>
					<?php if ( $block['column_1_cta'] ) : ?>
					<a href="
						<?php
						if ( $block['column_1_cta_link'] ) :
							?>
							<?php print esc_attr( $block['column_1_cta_link'] ); ?>
							<?php
else :
	?>
						#<?php endif; ?>" class="campaign__cta"><?php print esc_html( $block['column_1_cta'] ); ?></a>
					<?php endif; ?>
				</div>
				<div class="campaign__column">
					<h3 class="campaign__heading-3"><?php print esc_html( $block['column_2_title'] ); ?></h3>
					<?php
					print wp_kses(
						wpautop( substr( trim( $block['column_2_copy'] ), 0, 3000 ) ),
						array(
							'p'  => array(),
							'br' => array(),
							'ul' => array(),
							'li' => array(),
							'ol' => array(),
						)
					);
					?>
					<?php if ( $block['column_2_cta'] ) : ?>
					<a href="
						<?php
						if ( $block['column_2_cta_link'] ) :
							?>
							<?php print esc_attr( $block['column_2_cta_link'] ); ?>
							<?php
else :
	?>
						#<?php endif; ?>" class="campaign__cta"><?php print esc_html( $block['column_2_cta'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
