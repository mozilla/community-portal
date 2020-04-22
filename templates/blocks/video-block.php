<?php
/**
 * Video Block - Campaigns
 *
 * Video block for campaigns
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<div class="campaign__video-block
<?php
if ( 'grey' === $block['background_color'] ) :
	?>
	campaign__video-block--grey<?php endif; ?>">
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
		<?php
			preg_match( '/[\\?\\&]v=([^\\?\\&]+)/', $block['video'], $matches );
			$youtube_id = ( is_array( $matches ) && count( $matches ) > 1 ) ? $matches[1] : false;
		?>
		<?php if ( $youtube_id ) : ?>
		<div class="campaign__video-container">
			<iframe class="campaign__video" src="https://www.youtube.com/embed/<?php print esc_attr( $youtube_id ); ?>"></iframe>
		</div>
		<?php endif; ?>
	</div>
</div>
