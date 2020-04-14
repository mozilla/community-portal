<?php 
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<div class="lightbox__container">
    <button id="close-share-lightbox" class="btn btn--close">
        <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M25 1L1 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M1 1L25 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <div class="share-lightbox">
        <p class="title--secondary"><?php echo __('Share', 'community-portal') ?></p> 
        <ul class="share-link-container">
            <li class="share-link">
                <a href="#" id="copy-share-link" class="btn btn--light btn--share share-link__copy">
                    <?php _e('Copy share link', 'community-portal') ?>
                </a>
            </li>
            <li class="share-link">
                <a href="<?php echo esc_attr('https://www.facebook.com/sharer/sharer.php?u='.$url)?>" class="btn btn--light btn--share share-link__facebook">
                    <?php _e('Share to Facebook', 'community-portal') ?>
                </a>
            </li>
            <li class="share-link">
                <a href="<?php echo esc_attr('https://twitter.com/intent/tweet?url='.$url) ?>" class="btn btn--light btn--share share-link__twitter">
                    <?php _e('Share to Twitter', 'community-portal') ?>
                </a>
            </li>
            <li class="share-link">
                <a href="<?php echo esc_attr('https://discourse.mozilla.org/new-topic?title='.$url)?>" class="btn btn--light btn--share share-link__discourse">
                    <?php _e('Share to Discourse', 'community-portal') ?>
                </a>
            </li>
            <li class="share-link">
                <a href="<?php echo esc_attr('https://telegram.me/share/url?url='.$url) ?>" class="btn btn--light btn--share share-link__telegram" >
                    <?php echo __('Share to Telegram', 'community-portal') ?>
                </a>
            </li>
        </ul>
    </div>
</div>