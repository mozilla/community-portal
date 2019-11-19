<div class="col-lg-4 col-sm-12 events-single__sidebar">
      <div>
        <div class="card events-single__attributes">
          <div class="row">
          <?php 
            if (isset($external_url) && strlen($external_url) > 0 && filter_var($external_url, FILTER_VALIDATE_URL)):
          ?>
            <div class="col-lg-12 col-md-6 col-sm-12">
              <p class="events-single__label"><?php echo __('Links') ?></p>
              <p><a href="<?php echo esc_attr($external_url) ?>"><?php echo __($external_url) ?></a></p>
            </div>
          <?php 
            endif;
          ?>
          <?php if (is_array($categories)): ?>
          <div class="col-lg-12 col-md-6 col-sm-12">
            <p class="events-single__label">Tags</p>
            <ul class="events-single__tags">
              <?php
                foreach($categories as $category) {
                ?>
                <li class="tag"><?php echo $category->name ?></li>
                <?php
              }
            ?>
            </ul>
          </div>
          <?php 
            endif; 

            if (isset($campaign) && $campaign !== 'No'):
          ?>
          <div class="col-lg-12 col-md-6 col-sm-12">
            <p class="events-single__label"><?php echo __('Part of') ?></p>
            <div class="events-single__campaign">            
              <svg width="24" height="24" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.8493 9.33834L10.1925 10.7526C10.1925 10.7526 4.81846 11.8839 3.96994 12.7325C3.29111 13.4113 3.59281 14.0524 3.82851 14.2881C4.3942 14.8538 6.42124 16.8808 7.36405 17.8236" stroke="#0060DF" stroke-width="2"/>
                <path d="M21.5061 14.9956L20.0919 20.6525C20.0919 20.6525 18.9606 26.0265 18.112 26.875C17.4332 27.5539 16.7921 27.2522 16.5564 27.0165C15.9907 26.4508 13.9637 24.4237 13.0209 23.4809" stroke="#0060DF" stroke-width="2"/>
                <path d="M8.36987 19.6465L6.30336 21.713L7.71758 23.1272L9.13179 24.5414L11.1983 22.4749" stroke="#0060DF" stroke-width="2" stroke-linejoin="round"/>
                <path d="M22.7239 13.7788L13.0208 23.4819L10.1924 20.6535L7.36393 17.825L17.067 8.12197C19.3062 5.8828 22.6846 6.27564 23.6274 7.21845C24.5702 8.16126 24.963 11.5397 22.7239 13.7788Z" stroke="#0060DF" stroke-width="2"/>
              </svg>
              <a href="#"><?php echo __('Firefox for good campaign') ?></a>
            </div>
          </div>
          <?php 
            endif 
          ?>
          <div class="events-single__share col-lg-12 col-md-6 col-sm-12 <?php echo (!isset($campaign) && !isset($external_url) && !is_array($categories) && !strlen($external_url) > 0 ? esc_attr('only-share') : null )?>">
          <button id="open-events-share-lightbox" class="btn btn--light btn--share">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 9V15C3 15.3978 3.15804 15.7794 3.43934 16.0607C3.72064 16.342 4.10218 16.5 4.5 16.5H13.5C13.8978 16.5 14.2794 16.342 14.5607 16.0607C14.842 15.7794 15 15.3978 15 15V9M12 4.5L9 1.5M9 1.5L6 4.5M9 1.5V11.25" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?php echo __('Share') ?>
          </button>
          </div>
        </div>
        </div>
      </div>
      <?php
        include(locate_template('plugins/events-manager/templates/template-parts/event-single/event-host.php', false, false));
      ?>