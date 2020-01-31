<div class="campaign__image-text-block<?php if($block['background_color'] === 'grey'): ?> campaign__image-text-block--grey<?php endif; ?>">
    <div class="campaign__block-container<?php if($block['keyline']): ?> campaign__block-container--keyline<?php endif; ?>">
        <div class="campaign__block-content">
            <div class="campaign__image-text-block-container">
                <div class="campaign__image-text-block-text-container">
                    <h2 class="campaign__heading-2"><?php print $block['title']; ?></h2>
                    <?php print $block['copy']; ?>
                </div>
                <div class="campaign__image-text-block-image-container">
                    <img src="<?php print $block['image']['url']; ?>"  class="campaign__image-text-block-image"/>
                </div>
            </div>
        </div>
    </div>
</div>