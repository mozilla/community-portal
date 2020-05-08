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
<div class="campaign__3up-text-block
<?php
if ( 'grey' === $block['background_color'] ) :
	?>
	campaign__3up-text-block--grey<?php endif; ?>">
	<div class="campaign__block-container<?php echo $block['keyline'] ? ' campaign__block-container--keyline' : ''; ?>">
	<?php if ( ! empty( $block['title'] ) ) : ?>
		<h2 class="campaign__heading-2 campaign__heading-2--center"><?php print esc_html( $block['title'] ); ?></h2>
	<?php endif; ?>
		<div class="campaign__block-content">
			<div class="campaign__columns-container campaign__columns-container--three">
		<?php foreach ( array( 1, 2, 3 ) as $col ) : ?>
			<?php
			$col_title = $block[ 'column_' . $col . '_title' ];
			$col_copy  = $block[ 'column_' . $col . '_copy' ];
			$col_cta   = $block[ 'column_' . $col . '_cta' ];
			$col_link  = $block[ 'column_' . $col . '_cta_link' ];
			?>
		<div class="campaign__column">
			<?php if ( ! empty( $col_title ) ) : ?>
			<h3 class="campaign__heading-3"><?php print esc_html( $col_title ); ?></h3>
			<?php endif; ?>
			<?php if ( ! empty( $col_copy ) ) : ?>
				<?php
					print wp_kses(
						wpautop( substr( trim( $col_copy ), 0, 3000 ) ),
						wp_kses_allowed_html( 'post' )
					);
				?>
			<?php endif; ?>
			<?php if ( ! empty( $col_cta ) ) : ?>
				<?php if ( ! empty( $col_link ) ) : ?>
			<a href="<?php print esc_attr( $col_link ); ?>" class="campaign__cta"><?php print esc_html( $col_cta ); ?></a>
			<?php else : ?>
			<a href="#" class="campaign__cta"><?php print esc_html( $col_cta ); ?></a>
			<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
