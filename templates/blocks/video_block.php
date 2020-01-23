<div class="campaign__video-block<?php if($block['background_color'] === 'grey'): ?> campaign__video-block--grey<?php endif; ?>">
    <div class="campaign__block-container<?php if($block['keyline']): ?> campaign__block-container--keyline<?php endif; ?>">
        <h2 class="campaign__heading-2"><?php print $block['title']; ?></h2>
        <div class="campaign__block-content">
            <?php print $block['copy']; ?>
        </div>
        <?php if($block['cta']): ?>
            <a href="<?php if($block['cta_link']): ?> <?php print $block['cta_link']; ?><?php else: ?>#<?php endif; ?>" class="campaign__cta"><?php print $block['cta']; ?></a>
        <?php endif; ?>
        <?php 
            preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $block['video'], $matches);
            $youtube_id = (is_array($matches) && sizeof($matches) > 1) ? $matches[1] : false;
        ?>
        <?php if($youtube_id): ?>
        <div class="campaign__video-container">
            <iframe class="campaign__video" src="https://www.youtube.com/embed/<?php print $youtube_id; ?>"></iframe>
        </div>
        <?php endif; ?>
    </div>
</div>