<div class="campaign__2up-text-block<?php if($block['background_color'] === 'grey'): ?> campaign__2up-text-block--grey<?php endif; ?>">
    <div class="campaign__block-container<?php if($block['keyline']): ?> campaign__block-container--keyline<?php endif; ?>">
        <h2 class="campaign__heading-2 campaign__heading-2--center"><?php print $block['title']; ?></h2>
        <div class="campaign__block-content">
            <div class="campaign__columns-container">
                <div class="campaign__column">
                    <h3 class="campaign__heading-3"><?php print $block['column_1_title']; ?></h3>
                    <?php print $block['column_1_copy']; ?>
                    <?php if($block['column_1_cta']): ?>
                    <a href="<?php if($block['column_1_cta_link']): ?> <?php print $block['column_1_cta_link']; ?><?php else: ?>#<?php endif; ?>" class="campaign__cta"><?php print $block['column_1_cta']; ?></a>
                    <?php endif; ?>
                </div>
                <div class="campaign__column">
                    <h3 class="campaign__heading-3"><?php print $block['column_2_title']; ?></h3>
                    <?php print $block['column_2_copy']; ?>
                    <?php if($block['column_2_cta']): ?>
                    <a href="<?php if($block['column_2_cta_link']): ?> <?php print $block['column_2_cta_link']; ?><?php else: ?>#<?php endif; ?>" class="campaign__cta"><?php print $block['column_2_cta']; ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>