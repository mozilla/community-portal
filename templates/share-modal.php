<div class="lightbox__container">
      <button id="close-share-lightbox" class="btn btn--close">
        <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M25 1L1 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M1 1L25 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <div class="share-lightbox">
        <p class="title--secondary"><?php echo __('Share') ?></p> 
        <ul class="share-link-container">
          <li class="share-link">
            <a href="#" id="copy-share-link" class="btn btn--light btn--share share-link__copy">
              <?php echo __('Copy share link') ?>
            </a>
          </li>
          <li class="share-link">
            <a href="#" class="btn btn--light btn--share share-link__facebook">
              <?php echo __('Share to Facebook') ?>
            </a>
          </li>
          <li class="share-link">
            <a href="<?php echo esc_attr('https://twitter.com/intent/tweet?url='.$url) ?>" class="btn btn--light btn--share share-link__twitter">
              <?php echo __('Share to Twitter') ?>
            </a>
          </li>
          <li class="share-link">
            <a href="" class="btn btn--light btn--share share-link__discourse">
              <?php echo __('Share to Discourse') ?>
            </a>
          </li>
          <li class="share-link">
            <a href="" class="btn btn--light btn--share share-link__telegram">
              <?php echo __('Share to Telegram') ?>
            </a>
          </li>
        </ul>
      </div>
    </div>