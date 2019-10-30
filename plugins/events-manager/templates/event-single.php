<?php 
  global $EM_Event, $bp, $EM_Tags;;
  
  $mapBoxAccessToken = 'pk.eyJ1Ijoia3ljYXBzdGlja3BnIiwiYSI6ImNrMmM0MnJ0ODJocHQzY3BlMmdkZGxucnYifQ.j4K7gEui7_BoPezbyGmZuw';
  $categories = get_the_terms($EM_Event->post_id, EM_TAXONOMY_CATEGORY);  
  $event_meta = get_post_meta($EM_Event->post_id, 'event-meta');
  $allCountries = em_get_countries();
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
            if ($img_url && $img_url !== ''):
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
        <div class="card__details">
        <div class="card__date">
          <h2 class="title--secondary">
            <?php if ($endDay):
                echo __($months[$startMonth].' '.$startDay.' - '.$months[$endMonth].' '.$endDay.', '.$endYear);
              else:
                echo __($months[$startMonth].' '.$startDay.', '.$startYear);
            endif ?>
            
          </h2>
          <p card="card__time">
            <?php echo __(substr($EM_Event->event_start_time, 0, 5)); 
              if ($EM_Event->event_end_time !== null):
                echo __(' to '.substr($EM_Event->event_end_time, 0, 5).' '.$EM_Event->event_timezone);
              endif;
            ?>
          </p>

        </div>
        <?php echo $EM_Event->output('#_BOOKINGFORM'); ?>

            </div>
      </div>
      <h2 class="title--secondary"><?php echo __("Location") ?></h2>
      <div class="card events-single__location">
        <div class="card__address">
          <?php if ($location_type !== 'online'): 
            $location = $EM_Event->location;
            ?>
            <p><?php echo __($location->location_name) ?></p>
            <p><?php echo __($location->location_address) ?></p>
            <p><?php echo __($location->location_town.', '.$allCountries[$EM_Event->location->location_country]) ?></p>
          <?php else: ?>
            <p><?php echo __("This is an online-only event") ?></p>
            <a href="<?php echo esc_attr($EM_Event->location->address) ?>"><?php echo __('Meeting link') ?></a>
          <?php endif; ?>
        </div>
        <?php
          $fullLocation = rawurlencode($location->location_address.' '.$location->location_town);
          $request = wp_remote_get('https://api.mapbox.com/geocoding/v5/mapbox.places/'.$fullLocation.'.json?types=address&access_token='.$mapBoxAccessToken);
          if (is_wp_error($request)):
            return false;
          endif;
          $body = wp_remote_retrieve_body( $request );
          $data = json_decode( $body );
          $coordinates = $data->features[0]->geometry->coordinates;
        ?>
        <?php if ($location_type !== 'online'): ?>
          <div id='map' class="card__map" style='height: 110px;'></div>
          <script>
            const geojson =  {
              type: 'FeatureCollection',
              features: [{
                type: 'Feature',
                geometry: {
                  type: 'Point',
                  coordinates: [<?php echo $coordinates[0].', '.$coordinates[1]; ?>]
                },
                properties: {
                  title: 'Mapbox',
                  description: 'Washington, D.C.'
                }
              }]
            };
            mapboxgl.accessToken = "<?php echo $mapBoxAccessToken ?>";
            var map = new mapboxgl.Map({
              container: 'map', 
              style: 'mapbox://styles/mapbox/streets-v11',
              center: [<?php echo $coordinates[0].', '.$coordinates[1]; ?> ],
              zoom: 15,
            });
            geojson.features.forEach(function(marker) {
              // create a HTML element for each feature
              var el = document.createElement('div');
              el.className = 'marker';

              // make a marker for each feature and add to the map
              new mapboxgl.Marker(el)
                .setLngLat(marker.geometry.coordinates)
                .addTo(map);
              });
        </script>
        <?php endif ?>
      </div>
      <div class="events-single__description">
        <h2 class="title--secondary"><?php echo __('Description') ?></h2>
        <p><?php echo __($EM_Event->post_content) ?></p>
      </div>
      <h2 class="title--secondary"><?php echo __('Attendees') ?></h2>
      <div class="row">
        <?php
          if ($EM_Event->bookings): 
          foreach ($EM_Event->bookings as $booking) {
            if ($booking->booking_status === '3') {
              break;
            }
            $user = $booking->person->data;
            $avatar = get_avatar_url($user->ID);
            ?>
              <div class="col-md-6 events-single__member-card">
                <?php 
                  if ($avatar):
                ?>
                <div class="events-single__avatar">
                  <img src="<?php echo esc_attr($avatar)?>" alt="">                    
                </div>
                <div class="events-single__user-details">
                    <p class="events-single__username"><?php echo __('@'.$user->display_name) ?></p>
                    <p class="events-single__name"><?php echo __($user->user_nicename) ?></p>
                
                </div>
            <?php
            endif;
            ?>
              </div>
            <?php
          }
          ?>
            
        <?php endif;?>
      </div>
    </div>
    <div class="col-md-4">
      <div>
        <div class="card events-single__attributes">
          <?php 
            if ($EM_Event->location->location_name === "Online" && $EM_Event->location->location_address):
          ?>
            <div>
              <p class="events-single__label">Links</p>
              <p><a href="<?php echo $EM_Event->location->location_address?>"><?php echo $EM_Event->location->location_address ?></a></p>
            </div>
          <?php 
            endif;
          ?>
          <?php if ($categories): ?>
          <div>
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
          <?php endif; ?>
          <div>
            <p class="events-single__label">Part of</p>
            <div class="events-single__campaign">            
              <svg width="24" height="24" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.8493 9.33834L10.1925 10.7526C10.1925 10.7526 4.81846 11.8839 3.96994 12.7325C3.29111 13.4113 3.59281 14.0524 3.82851 14.2881C4.3942 14.8538 6.42124 16.8808 7.36405 17.8236" stroke="#0060DF" stroke-width="2"/>
                <path d="M21.5061 14.9956L20.0919 20.6525C20.0919 20.6525 18.9606 26.0265 18.112 26.875C17.4332 27.5539 16.7921 27.2522 16.5564 27.0165C15.9907 26.4508 13.9637 24.4237 13.0209 23.4809" stroke="#0060DF" stroke-width="2"/>
                <path d="M8.36987 19.6465L6.30336 21.713L7.71758 23.1272L9.13179 24.5414L11.1983 22.4749" stroke="#0060DF" stroke-width="2" stroke-linejoin="round"/>
                <path d="M22.7239 13.7788L13.0208 23.4819L10.1924 20.6535L7.36393 17.825L17.067 8.12197C19.3062 5.8828 22.6846 6.27564 23.6274 7.21845C24.5702 8.16126 24.963 11.5397 22.7239 13.7788Z" stroke="#0060DF" stroke-width="2"/>
              </svg>
              <a href="#">Firefox for good campaign</a>
            </div>
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
        <p class="events-single__label">Hosted by</p>
        <a href="<?php echo get_site_url(null, 'groups/'.bp_get_group_slug($group)) ?>"><?php echo bp_get_group_name($group) ?></a>
        <?php 
          if ($user && $avatar):
            ?>
            <div class="events-single__member-card">
              <img class="events-single__avatar" src="<?php echo $avatar ?>" alt="">
              <p class="events-single__username"><?php echo '@'.$user->user_nicename ?></p>
            </div>
            <?
          endif;
          ?>
      </div>
      <?php
      endif;
    ?>
  </div>
  <div class="col-sm-12">
    <div class="events-single__report">
      <button class="btn events-single__report-btn">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M12 8V12" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="12" cy="16" r="0.5" fill="#CDCDD4" stroke="#0060DF"/>
        </svg>
          Report this event
        </button>
      </div>
  </div>
</div>
<div class="events-single__related">
  <h2 class="title--secondary"><?php echo __('Related Events') ?></h2>
  <?php 
    $allRelatedEvents = array();
    if (count($categories) > 0):
      foreach ($categories as $category) {
        $relatedEvents = EM_Events::get(array('category' => $category->id));
        if (count($relatedEvents) > 0) {
          foreach ($relatedEvents as $singleEvent) {
            if ($allRelatedEvents[0] && $allRelatedEvents[0]->event_id === $singleEvent->event_id) {
              break;
            }
            $allRelatedEvents[] = $singleEvent;
            if (count($allRelatedEvents) >= 2) {
              break;
            }
          }
        }
        if (count($allRelatedEvents) >= 2) {
          break;
        }
      }
    endif;
    if (count($allRelatedEvents) > 0):
    ?>
      <div class="row">
      <?php
      foreach($allRelatedEvents as $event) {
        $url = $site_url.'/events/'.$event->slug;

        include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));
      }
      ?>
      </div>
      <?php 
    endif;
  ?>
</div>