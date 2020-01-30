<?php 
    $all_countries = em_get_countries();
?>
<div class="campaign__events-block">
    <div class="campaign__block-container<?php if($block['keyline']): ?> campaign__block-container--keyline<?php endif; ?>">
        <h2 class="campaign__heading-2"><?php print $block['title']; ?></h2>
        <div class="campaign__block-content ">

            <?php if(isset($block['events'])):?>
            <?php 
                if(!$block['events'] || (is_array($block['events']) && sizeof($block['events']) < 4)) {
                    $args = Array('scope' =>  'future');
                    $events = EM_Events::get($args);        
                    $related_events = Array();

                    foreach($events AS $e) {
                        $event_meta = get_post_meta($e->post_id, 'event-meta');

                        if(isset($event_meta[0]->initiative) && intval($event_meta[0]->initiative) === $post->ID) {
        
                            $related_events[] = Array('event'   =>  get_post($e->post_id));
                        }

                        if(sizeof($related_events) === 4)
                            break;
                    }

                    if($block['events'] === false)
                        $block['events'] = $related_events;
                    else 
                        $block['events'] = array_merge($block['events'], $related_events);
                }

            ?>
            <div class="campaign__events-container">
            <?php foreach($block['events'] AS $event): ?>
                <?php 
                    $event_meta = get_post_meta($event['event']->ID, 'event-meta');
                    $em_event = em_get_event($event['event']->ID, 'post_id'); 
                    $event_time = strtotime($em_event->event_start_date);
                    $event_month = date('M', $event_time);
                    $event_day = date('j', $event_time);

                    $location = em_get_location($em_event->location_id);
                    $categories = (!is_null($em_event)) ? $em_event->get_categories() : false;
                ?>
                <a href="<?php print $event['event']->guid; ?>" class="campaign__event">
                    <div class="campaign__event-image" <?php if(isset($event_meta[0]->image_url) && strlen($event_meta[0]->image_url) > 0): ?>style="background-image: url('<?php print $event_meta[0]->image_url; ?>')"<?php endif; ?>>
                        <div class="campaign__event-date">
                            <?php print "{$event_month} {$event_day}"; ?>
                        </div>
                    </div>
                    <div class="campaign__event-container">
                        <h3 class="campaign__event-title"><?php print $event['event']->post_title; ?></h3>
                        <div class="campaign__event-time">
                            <?php print date('F j, Y ∙ G:i', $event_time).__(" UTC", 'community-portal'); ?>
                        </div>
                        <?php if (strlen($location->address) > 0 || strlen($location->town) > 0 || strlen($location->country) > 0): ?>
                        <div class="campaign__event-location">
                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?php  if($location->country === 'OE'): ?>
                            <?php print __('Online Event', 'community-portal'); ?>
                            <?php else: ?>
                                <?php if ($location->address): ?>
                                <?php print $location->address.' - '; ?>
                                <?php endif; ?>
                                <?php if ($location->town): ?>
                                    <?php if(strlen($location->town) > 180): ?>
                                    <?php $city = substr($location->town, 0, 180); ?>
                                    <?php endif; ?>
                                    <?php print $city; ?>
                                    <?php if($location->country): ?>
                                        <?php if($city): ?>
                                            <?php print ', '; ?>
                                        <?php endif; ?>
                                        <?php print $all_countries[$location->country]; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php print $all_countries[$location->country]; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
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
                    </div>
                </a>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>