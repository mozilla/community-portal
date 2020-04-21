<div class="campaign__text-card-block">
    <div class="campaign__block-container<?php if($block['keyline']): ?> campaign__block-container--keyline<?php endif; ?>">
        <h2 class="campaign__heading-2"><?php print $block['title']; ?></h2>
        <div class="campaign__block-content">
            <div class="campaign__card">
                <?php print $block['copy']; ?>
            </div>
        </div>
    </div>
</div>