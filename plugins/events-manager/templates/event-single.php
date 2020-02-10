<?php 
    $logged_in = mozilla_is_logged_in();
    $current_user = wp_get_current_user()->data;

    global $EM_Event, $bp, $EM_Tags;
    $options = wp_load_alloptions();

    $mapBoxAccessToken = (isset($options['mapbox']) && strlen($options['mapbox']) > 0) ? trim($options['mapbox']) : false;
    
    $categories = get_the_terms($EM_Event->post_id, EM_TAXONOMY_CATEGORY);  
    $event_meta = get_post_meta($EM_Event->post_id, 'event-meta');
    $allCountries = em_get_countries();
    $img_url = $event_meta[0]->image_url;

    if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
        $img_url = preg_replace("/^http:/i", "https:", $img_url);
    } else {
        $avatar_url = $img_url;
    }

    $location_type = $event_meta[0]->location_type;
    $external_url = $event_meta[0]->external_url;
    $campaign = $event_meta[0]->campaign;
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

    if ($EM_Event->event_start_date !== $EM_Event->event_end_date) {
        $endDay = substr($EM_Event->event_end_date, 8, 2);
        $endMonth = substr($EM_Event->event_end_date, 5, 2);
        $endYear = substr($EM_Event->event_end_date, 0, 4);
    }

    $allRelatedEvents = array();
    if (is_array($categories) && count($categories) > 0) {
        foreach ($categories as $category) {
            $relatedEvents = EM_Events::get(array('category' => $category->term_id));
            if (count($relatedEvents) > 0) {
                foreach ($relatedEvents as $singleEvent) {
                    if ($allRelatedEvents[0]->event_id === $singleEvent->event_id) {
                    continue;
                    }
                    if ($singleEvent->event_id === $EM_Event->event_id) {
                        continue;
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
    }

    if (isset($EM_Event->group_id)) {
        $group = new BP_Groups_Group($EM_Event->group_id);
        $admins = groups_get_group_admins($group->id);

        if (isset($admins)) {
            $user = get_userdata($admins[0]->user_id);
            $avatar = get_avatar_url($admins[0]->user_id);
            $users = get_current_user_id();
        }
    }
?>

<div class="content events__container events-single">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="title"><?php echo __($EM_Event->event_name) ?></h1>
        </div>
    </div>
    <div class="row events-single__two-up">
        <div class="col-lg-7 col-md-12">
            <div class="card card--with-img">
                <?php     
                    if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                        $img_url = preg_replace("/^http:/i", "https:", $img_url);
                    } else {
                        $img_url = $img_url;
                    }
                ?>
                <div class="card__image" <?php if($img_url && $img_url !== ''): ?>style="background-image: url(<?php echo esc_url_raw($img_url); ?>); min-height: 317px; width: 100%;"<?php endif; ?>>
                    <?php $current_user_id = get_current_user_id(); ?>
                    <?php if(strval($current_user_id) == $EM_Event->owner || mozilla_is_site_admin()): ?>
                        <a class="btn card__edit-btn<?php if($img_url):?> card__edit-btn--white<?php endif; ?>" href="<?php echo esc_attr(get_site_url().'/events/edit-event/?action=edit&event_id='.$EM_Event->event_id)?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04   23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z"  fill="#0060DF"/>
                            </svg>
                        </a>
                    <?php elseif(isset($admins)): ?>
                        <?php foreach($admins as $admin): ?>
                            <?php if ($admin->user_id === $current_user_id || intval(get_current_user_id()) === intval($EM_Event->event_owner) || current_user_can('edit_post')): ?>  
                                <a class="btn card__edit-btn<?php if($img_url):?> card__edit-btn--white<?php endif; ?>" href="<?php echo esc_attr($_SERVER['REQUEST_URI'].'events/edit-event/?action=edit&event_id='.$EM_Event->event_id)?>">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z"  fill="#0060DF"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="card__details">
                    <div class="card__date">
                        <h2 class="title--secondary">
                            <?php 
                                if($endDay){
                                    echo $months[$startMonth].' '.$startDay.' - '.$months[$endMonth].' '.$endDay.', '.$endYear;
                                } else {
                                    echo $months[$startMonth].' '.$startDay.', '.$startYear;
                                } 
                            ?>
                        </h2>
                        <p card="card__time">
                            <?php echo __(substr($EM_Event->event_start_time, 0, 5)); 
                                if ($EM_Event->event_end_time !== null) {
                                    echo ' to '.substr($EM_Event->event_end_time, 0, 5).' '.$EM_Event->event_timezone;
                                }
                            ?>
                        </p>
                    </div>
                    <?php 
                        if(is_user_logged_in()) {
                            echo $EM_Event->output('#_BOOKINGFORM'); 
                        }
                    ?>
                </div>
            </div>

            <h2 class="title--secondary"><?php echo __("Location") ?></h2>
            <div class="card events-single__location">
                <div class="row">
                    <div class="card__address col-md-5 col-sm-12">
                    <?php $location = $EM_Event->location; ?>
                    <?php if (isset($location_type) && strlen($location_type) > 0 && $location_type !== 'online' && $location->location_country !== 'OE'): ?>
                        <p><?php echo $location->location_name; ?></p>
                        <p><?php echo $location->location_address; ?></p>
                        <?php if ($location->location_country === 'OE'): ?>
                            <p><?php echo __('Online Event', "community-portal") ?></p>
                        <?php else: ?>
                            <p><?php echo __($location->location_town.', '.$allCountries[$EM_Event->location->location_country]) ?></p>
                        <?php endif; ?>
                        <p><a href="/events/?country=<?php print $allCountries[$EM_Event->location->location_country]; ?>"><?php print __('View more events in ',  "community-portal"); ?><?php print $allCountries[$EM_Event->location->location_country]; ?></a></p>
                    <?php else: ?>
                        <p><?php echo __("This is an online-only event", "community-portal") ?></p>
                        <?php if(filter_var($EM_Event->location->name, FILTER_VALIDATE_URL)): ?>
                        <a href="<?php echo esc_attr($EM_Event->location->name) ?>"><?php echo __('Meeting link', "community-portal") ?>
                            <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.33325 8.66732L4.99992 5.00065L1.33325 1.33398" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    </div>
                    <?php if($mapBoxAccessToken !== false): ?>
                    <?php
                        $fullLocation = rawurlencode($location->location_address.' '.$location->location_town);
                        $request = wp_remote_get('https://api.mapbox.com/geocoding/v5/mapbox.places/'.$fullLocation.'.json?types=address&access_token='.$mapBoxAccessToken);

                        if (is_wp_error($request)) {
                            return false;
                        }

                        $body = wp_remote_retrieve_body( $request );
                        $data = json_decode( $body );
                        $coordinates = $data->features[0]->geometry->coordinates; 
                    ?>
                    <?php if (isset($location_type) && strlen($location_type) && $location_type !== 'online' && $location->location_country !== 'OE'): ?>
                        <div id='map' class="card__map col-md-7 col-sm-12" style='height: 110px;'></div>
                        <script type="text/javascript">
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
                    <?php endif; ?>
					<?php endif; ?>
				</div>
            </div>
            <div class="events-single__description">
                <h2 class="title--secondary"><?php echo __('Description') ?></h2>
                <p><?php echo wpautop($EM_Event->post_content); ?></p>
            </div>
            <?php
                $activeBookings = array();
                if (isset($EM_Event->bookings)) {
                    foreach ($EM_Event->bookings as $booking) {
                        if ($booking->booking_status !== '3' && $count < 8) {
                            $activeBookings[] = $booking;
                        }
                    }
                }
            ?>
            <?php if (is_array($activeBookings) && count($activeBookings) > 0): ?>
            <h2 class="title--secondary"><?php echo __('Attendees') ?> (<?php print sizeof($activeBookings); ?>)</h2>
            <div class="row">
                <?php $count = 0; ?>  
                <?php foreach ($activeBookings as $booking): ?>
                    <?php
                        if ($count < 8) {
                            $activeBookings[] = $booking;
                            $user = $booking->person->data;
                            
                            $is_me = $logged_in && intval($current_user->ID) === intval($user->ID);
                            $info = mozilla_get_user_info($current_user, $user, $logged_in);

                            if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                                $avatar_url = preg_replace("/^http:/i", "https:", $info['profile_image']->value);
                            } else {
                                $avatar_url = $info['profile_image']->value;
                            }
                    ?>
                    <div class="col-md-6 events-single__member-card">
                        <a href="<?php echo '/people/'.$user->user_nicename; ?>">
                            <div class="events-single__avatar<?php if($info['profile_image']->display === false || $info['profile_image']->value === false) : ?> members__avatar--identicon<?php endif; ?>" <?php if($info['profile_image']->display && $info['profile_image']->value): ?> style="background-image: url('<?php print $avatar_url; ?>')"<?php endif; ?> data-username="<?php print $user->user_nicename; ?>">
                            </div>
                            <div class="events-single__user-details"> 
                                <p class="events-single__username"><?php echo $user->user_nicename; ?></p>
                                <?php if ($info['first_name']->display && $info['first_name']->value || $info['last_name']->display && $info['last_name']->value): ?>
                                <div class="events-single__name">
                                    <?php 
                                        if ($info['first_name']->display && $info['first_name']->value): 
                                            print $info['first_name']->value;
                                        endif; 

                                        if ($info['last_name']->display && $info['last_name']->value):
                                            print " {$info['last_name']->value}";
                                        endif; 
                                    ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($info['location']->display && $info['location']->value): ?>
                                    <p class="events-single__country">
                                        <?php echo $info['location']->value; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <?php $count = $count + 1; ?>
                        </a>
                    </div>
                    <?php
                        } else if ($count >= 8) {
                    ?>
                        <?php if ($count === 8): ?>
                            <button id="open-attendees-lightbox" class="btn btn--submit btn--light">
                                <?php echo __('View all attendees'); ?>
                            </button>
                        <?php endif; ?>
                    <?php
                            $count = $count + 1;
                        }
                    ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php include(locate_template('plugins/events-manager/templates/template-parts/event-single/event-sidebar.php', false, false)); ?>

    <?php if(count($allRelatedEvents) > 0): ?>
        <div class="events-single__related col-sm-12">
            <h2 class="title--secondary"><?php echo __('Related Events') ?></h2>
            <div class="row">
                <?php
                    foreach($allRelatedEvents as $event) {
                        $url = $site_url.'/events/'.$event->slug;
                        include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));
                    }
                ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($EM_Event->bookings)): ?>
    <div id="attendees-lightbox" class="lightbox">
        <div class="lightbox__container">
            <button id="close-attendees-lightbox" class="btn btn--close">
                <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M25 1L1 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M1 1L25 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <div class="row events-single__all-attendees">
                <p class="title--secondary col-sm-12"><?php echo $count.__(' Attendees', "community-portal") ?></p>
                <?php foreach($EM_Event->bookings as $booking): ?>    
                    <?php if($booking->booking_status !== '3'): ?>
                        <?php
                                $user = $booking->person->data;
                                $is_me = $logged_in && intval($current_user->ID) === intval($user->ID);
                                $info = mozilla_get_user_info($current_user, $user, $logged_in);

                                if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                                    $avatar_url = preg_replace("/^http:/i", "https:", $info['profile_image']->value);
                                } else {
                                    $avatar_url = $info['profile_image']->value;
                                }
                        ?>
                        <div class="col-md-6 events-single__member-card">
                            <a href="<?php echo '/people/'.$user->user_nicename; ?>")>
                                <div class="events-single__avatar<?php if($info['profile_image']->display === false || $info['profile_image']->value === false) : ?> members__avatar--identicon<?php endif; ?>" <?php if($visibility_settings['image_url_visibility'] && strlen($community_fields['image_url']) > 0): ?> style="background-image: url('<?php print $avatar_url; ?>')"<?php endif; ?> data-username="<?php print $user->user_nicename; ?>">
                                </div>
                                <div class="events-single__user-details"> 
                                    <p class="events-single__username">
                                        <?php echo $user->user_nicename; ?>
                                    </p>
                                    <?php if ($info['first_name']->display && $info['first_name']->value || $info['last_name']->display && $info['last_name']->value): ?>
                                        <div class="events-single__name">
                                            <?php 
                                                if ($info['first_name']->display && $info['first_name']->value): 
                                                    print $info['first_name']->value;
                                                endif; 

                                                if ($info['last_name']->display && $info['last_name']->value):
                                                    print " {$info['last_name']->value}";
                                                endif; 
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($info['location']->display && $info['location']->value): ?>
                                    <p class="events-single__country">
                                        <?php echo $info['location']->value; ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                        <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div id="events-share-lightbox" class="lightbox">
        <?php include(locate_template('templates/share-modal.php', false, false)); ?>
    </div>
</div>
<?php if(isset($options['report_email'])): ?>
<div class="events-single__report-container">
    <a href="mailto:<?php print $options['report_email']; ?>?subject=<?php print sprintf('%s %s', __('Reporting Event', 'community-portal'), $group->name); ?>&body=<?php print sprintf('%s %s', __('Please provide a reason you are reporting this event', 'community-portal'), "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"); ?>" class="events-single__report-group-link">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12 8V12" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="16" r="0.5" fill="#CDCDD4" stroke="#0060DF"/>
        </svg>
        <?php print __("Report Event", 'community-portal'); ?>
    </a>                                           
</div>
<?php endif ?>