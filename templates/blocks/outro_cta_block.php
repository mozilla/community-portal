<div class="campaign__outro-cta-block<?php if($block['background_color'] === 'grey'): ?> campaign__outro-cta-block--grey<?php endif; ?>">
    <div class="campaign__block-container<?php if($block['keyline']): ?> campaign__block-container--keyline<?php endif; ?>">
        <h4 class="campaign__heading-4 campaign__heading-4--center"><?php print $block['title']; ?></h2>
        <div class="campaign__block-content campaign__block-content--center">
            <a href="<?php print $block['cta_link']?>" class="campaign__outro-cta"><?php print $block['cta']; ?></a>
        </div>
    </div>
</div>