<div class="campaign__text-card-block">
	<div class="campaign__block-container<?php if ( $block['keyline'] ) :
		?> campaign__block-container--keyline<?php endif; ?>">
		<h2 class="campaign__heading-2"><?php print esc_html( $block['title'] ); ?></h2>
		<div class="campaign__block-content">
			<div class="campaign__card">
				<?php print wp_kses(
						wpautop( substr( trim( $block['copy'] ), 0, 3000 ) ),
						array(
							'p'  => array(),
							'br' => array(),
							'ul' => array(),
							'li' => array(),
						)
					); ?>
			</div>
		</div>
	</div>
</div>
