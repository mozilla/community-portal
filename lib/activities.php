<?php
function mozilla_download_activity_events() {

    if(!is_admin()) {
        return;
    }

    if(isset($_GET['activity']) && strlen($_GET['activity']) > 0) {
        $activity = get_post(intval($_GET['activity']));

        $args = Array('scope' =>  'all');
        $events = EM_Events::get($args);    
        $related_events = Array();

        foreach($events AS $event) {
            $event_meta = get_post_meta($event->post_id, 'event-meta');
            if(isset($event_meta[0]->initiative) && intval($event_meta[0]->initiative) === $activity->ID) {
                $event->meta = $event_meta[0];
                $related_events[] = $event;
            }
        }

        
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=activity-{$activity->ID}-events.csv;");
        foreach($related_events AS $related_event) {

        }
    }   


    die();
}




?>