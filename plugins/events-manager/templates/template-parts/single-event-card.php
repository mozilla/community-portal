<?php 
    $months = array(
        '01' => 'Jan',
        '02' => 'Feb',
        '03' => 'Mar',
        '04' => 'Apr',
        '05' => 'May',
        '06' => 'Jun',
        '07' => 'Jul',
        '08' => 'Aug',
        '09' => 'Sep',
        '10' => 'Oct',
        '11' => 'Nov',
        '12' => 'Dec',
    );

    $categories = (!is_null($event)) ? $event->get_categories() : false;
    $location = em_get_location($event->location_id);
    $site_url = get_site_url();
    $url = $site_url.'/events/'.$event->slug;  
?> 

<div class="col-lg-4 col-md-6 events__column">
    <div class="event-card">
        <a class="events__link" href="<?php echo $url?>">
            <div class="event-card__image"
            <?php 
                $event_meta = get_post_meta($event->post_id, 'event-meta');
                $img_url = $event_meta[0]->image_url;

                if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                    $img_url = preg_replace("/^http:/i", "https:", $img_url);
                } else {
                    $img_url = $img_url;
                }
            ?>

            <?php if ($img_url && $img_url !== ''): ?>
             style="background-image: url(<?php echo $img_url ?>)"
            <?php endif; ?>
            >
                <?php 
                    $month = substr($event->start_date, 5, 2);
                    $date = substr($event->start_date, 8, 2);
                    $year = substr($event->start_date, 0, 4);
                ?>
                <p class="event-card__image__date"><span><?php echo substr($months[$month],0,3) ?> </span><span><?php echo $date; ?></span></p>
            </div>
            <div class="event-card__description">
                <h3 class="event-card__description__title title--event-card"><?php echo $event->event_name; ?></h2>
                <p><?php echo $months[$month].' '.$date.', '.$year.' @ '.substr($event->event_start_time, 0, 5).' - '.substr($event->event_end_time, 0, 5).' '.$event->event_timezone; ?></p>

                <?php if (strlen($location->address) > 0 || strlen($location->town) > 0 || strlen($location->country) > 0): ?>
                <div class="event-card__location">
                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="text--light text--small">
                    <?php
                        if ($location->country === 'OE') {
                            echo __('Online Event');
                        } else {
                            if ($location->address) {
                                echo $location->address.' - '; 
                            }
                            
                            if ($location->town) {
                                if(strlen($location->town) > 180) {
                                    $city = substr($location->town, 0, 180);
                                }

                                echo $city;
                                if ($location->country) {
                                    if($city)
                                        print ', ';

                                    echo $allCountries[$location->country];
                                }
                            } else {
                                echo $allCountries[$location->country];
                            }
                        }
                    ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
            <ul class="events__tags">
            <?php if ($categories !== false && is_array($categories->terms)): ?>
            <?php if ($categories !== false && count($categories->terms) <= 2): ?>
            <?php foreach($categories->terms as $category): ?>
                <li class="tag"><?php echo __($category->name); ?></li>
            <?php endforeach; ?>
            <?php elseif ($categories !== false && count($categories->terms) > 0): ?>
            <?php             
                $i = 0;
            ?>
            <?php foreach ($categories->terms as $category): ?>
                <li class="tag"><?php echo $category->name; ?></li>
                <?php
                    $i = $i + 1;
                    if ($i === 2) {
                        break;
                    }
                ?>
            <?php endforeach; ?>
                <li class="tag"><?php echo __('+'); echo count($categories->terms) - 2; echo __(' more tag(s)'); ?></li>        
            <?php endif; ?>
            <?php endif; ?>
            </ul>
        </a>
    </div>
</div>