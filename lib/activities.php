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

        
$theme_directory = get_template_directory();
        include("{$theme_directory}/languages.php");
        $countries = em_get_countries();

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment;filename=campaign-{$_GET['campaign']}-events.csv");
        $out = fopen('php://output', 'w');

        $heading = Array('ID', 'Event Title', 'Event Start Date', 'Event End Date', 'Description', 'Goals', 'Attendee Count', 'Expected Attendee Count', 'Language', 'Location', 'Tags', 'Hosted By', 'Group');
        fputcsv($out, $heading);

        foreach($related_events AS $related_event) {
            $attendees = isset($related_event->get_bookings()->bookings) && is_array($related_event->get_bookings()->bookings) ? sizeof($related_event->get_bookings()->bookings) : 0;
            $language = isset($related_event->meta->language) && strlen($related_event->meta->language) > 0  ? $languages[$related_event->meta->language] : 'N/A';
            $event_meta = get_post_meta($related_event->post_id, 'event-meta');
            $location_type = isset($event_meta[0]->location_type) ? $event_meta[0]->location_type : '';
            $location_object = em_get_location($related_event->location_id);
            $tag_object = $related_event->get_categories();
            $tags = '';
            $user_id = $related_event->event_owner; 
            $event_creator = get_user_by('ID', $user_id);

            foreach($tag_object->terms AS $tag) {
                $tags = $tag->name.', ';
            }

            // Remove last comma
            $tags = rtrim($tags, ', ');

            $address = $location_object->address;
            if($location_object->city) {
                $address = $address.' '.$location_object->city;
            }

            if($location_object->town) {
                $address = $address.' '.$location_object->town;
            }

            if($location_object->country) {
                $address = $address.' '.$countries[$location_object->country];
            }

            $location = $location->country === 'OE' ? 'Online' : $address;
            $group_object = new BP_Groups_Group($related_event->group_id);
            $group = ($group_object->id) ? "{$group->name} ($group->id)" : 'N/A';
            $row = Array(
                            $related_event->event_id, 
                            $related_event->name,
                            $related_event->event_start_date, 
                            $related_event->event_end_date,
                            $related_event->post_content,
                            $related_event->meta->goal,
                            $attendees, 
                            $related_event->meta->projected_attendees,
                            $language,
                            $location,
                            $tags,
                            "{$event_creator->data->user_nicename} ({$user_id})",
                            $group
            );

            fputcsv($out, $row);

        }

        fclose($out);
    }   


    die();
}




?>