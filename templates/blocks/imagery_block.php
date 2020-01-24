<div class="campaign__imagery-block<?php if($block['background_color'] === 'grey'): ?> campaign__imagery-block--grey<?php endif; ?>">
    <div class="campaign__block-container<?php if($block['keyline']): ?> campaign__block-container--keyline<?php endif; ?>">
        <h2 class="campaign__heading-2 campaign__heading-2--center"><?php print $block['title']; ?></h2>
        
        <div class="campaign__block-content">
            <?php print $block['copy']; ?>
            <?php if(isset($block['images'])): ?>
            <div class="campaign__imagery-images-container">
            <?php foreach($block['images'] AS $image): ?>
            <div class="campaign__imagery-image-container">
                <img src="<?php print $image['image']['url']; ?>" class="campaign__imagery-image" />
                <div class="campaign__imagery-caption"><?php print $image['caption']; ?></div>
            </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>