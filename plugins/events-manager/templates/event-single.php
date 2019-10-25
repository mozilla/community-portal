<?php 
  global $EM_Event, $bp;
  $categories = get_the_terms($EM_Event->post_id, EM_TAXONOMY_CATEGORY);  
  $event_meta = get_post_meta($EM_Event->post_id, 'event-meta');
  $img_url = $event_meta[0]->image_url;
  $location_type = $event_meta[0]->location_type;
  $months = array(
    '01' => 'January',
    '02' => 'February',
    '03' => 'March',
    '04' => 'April',
    '05' => 'May',
    '06' => 'June',
    '07' => 'July',
    '08' => 'August',
    '09' => 'September',
    '10' => 'October',
    '11' => 'November',
    '12' => 'December',
  );
  $startDay = substr($EM_Event->event_start_date, 8, 2);
  $startMonth = substr($EM_Event->event_start_date, 5, 2);
  $startYear = substr($EM_Event->event_start_date, 0, 4);
  if ($EM_Event->event_start_date !== $EM_Event->event_end_date):
    $endDay = substr($EM_Event->event_end_date, 8, 2);
    $endMonth = substr($EM_Event->event_end_date, 5, 2);
    $endYear = substr($EM_Event->event_end_date, 0, 4);
  endif;
?>

<div class="content events__container events-single">
  <div class="row">
    <div class="col-sm-12">
      <h1 class="title"><?php echo the_title() ?></h1>
    </div>
    <div class="col-md-7">
      <div class="card card--with-img">
        <div class="card__image"
          <?php 
            if ($img_url !== ''):
          ?>
          style="background-image: url(<?php echo esc_url_raw($img_url); ?>); min-height: 317px; width: 100%;"
          <?php 
            endif;
          ?>
        >
          <button class="btn card__edit-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z"  fill="#0060DF"/>
            </svg>
          </button>
        </div>
        <div class="card__date">
          <p>
            <?php echo __($months[$startMonth].' '.$startDay)?>
            <?php if ($endDay):
                echo __(' - '.$months[$endMonth].' '.$endDay.', '.$endYear);
              else:
                echo __(', '.$startYear);
            endif ?>
          </p>
          <p>
            <?php echo __(substr($EM_Event->event_start_time, 0, 5)); 
              if ($EM_Event->event_end_time !== null):
                echo __(' to '.$EM_Event->event_end_time.' '.$EM_Event->event_timezone);
              endif;
            ?>
          </p>
        </div>
        <button class="btn btn--dark btn--submit">
          <?php echo __('RSVP') ?>
        </button>
      </div>
      <h2><?php echo __("Location") ?></h2>
      <div class="card">
        <div class="card__address">
          <?php if ($location_type !== 'online'): ?>
            <p><?php echo __($EM_Event->location->location_name) ?></p>
            <p><?php echo __($EM_Event->location->location_address) ?></p>
            <p><?php echo __($EM_Event->location->location_town.', '.$EM_Event->location->location_country) ?></p>
          <?php else: ?>
            <p><?php echo __("This is an online-only event") ?></p>
            <a href="<?php echo esc_attr($EM_Event->location->address) ?>"><?php echo __('Meeting link') ?></a>
          <?php endif; ?>
        </div>
        <?php if ($location_type !== 'online'): ?>
          <div class="card__map">
          </div>
        <?php endif ?>
      </div>
      <h2><?php echo __('Description') ?></h2>
      <p><?php echo __($EM_Event->post_content) ?></p>
      <h2><?php echo __('Attendees') ?></h2>
      <?php var_dump($EM_Event) ?>
    </div>
    <div class="col-md-4">
      <div>
        <div class="card">
          <?php 
            if ($EM_Event->location->location_name === "Online" && $EM_Event->location->location_address):
          ?>
            <div>
              <p>Links</p>
              <p><a href="<?php echo $EM_Event->location->location_address?>"><?php echo $EM_Event->location->location_address ?></a></p>
            </div>
          <?php 
            endif;
          ?>
          <?php if ($categories): ?>
          <div>
            <p>Tags</p>
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
          <?php endif; ?>
          <div>
            <p>Part of</p>
            <a href="#">Firefox for good campaign</a>
          </div>
        </div>
        <div class="card events-single__share">
          <button class="btn btn--light btn--share">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 9V15C3 15.3978 3.15804 15.7794 3.43934 16.0607C3.72064 16.342 4.10218 16.5 4.5 16.5H13.5C13.8978 16.5 14.2794 16.342 14.5607 16.0607C14.842 15.7794 15 15.3978 15 15V9M12 4.5L9 1.5M9 1.5L6 4.5M9 1.5V11.25" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Share
          </button>
        </div>
    </div>
    <?php if ($EM_Event->group_id):
      $group = new BP_Groups_Group($EM_Event->group_id);
      $admins = groups_get_group_admins($group->id);
      if ($admins):
        $user = get_userdata($admins[0]->user_id);
        $avatar = get_avatar_url($admins[0]->user_id);
      endif;
      ?> 
      <div class="card events-single__group">
        <p>Hosted by</p>
        <a href="<?php echo get_site_url(null, 'groups/'.bp_get_group_slug($group)) ?>"><?php echo bp_get_group_name($group) ?></a>
        <?php 
          if ($user && $avatar):
            ?>
            <div>
              <img src="<?php echo $avatar ?>" alt="">
              <p><?php echo '@'.$user->user_nicename ?></p>
            </div>
            <?
          endif;
          ?>
      </div>
      <?php
      endif;
    ?>
  </div>
</div>