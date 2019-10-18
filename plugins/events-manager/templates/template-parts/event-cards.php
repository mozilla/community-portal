<?php 
  $location = em_get_location($event->location_id);
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
  $categories = get_the_terms($event->post_id, EM_TAXONOMY_CATEGORY);
  $allCountries = em_get_countries();
  if ($categories) {
    $allTags = array_map(function($n) {
      return $n->name;
    }, $categories);
  }
  if ($tag !== 'all' && !$categories && $tag !== '') {
    echo 'hidden';
  } else if ($tag !== 'all' && $country !== 'all' && $tag !== '' && $country !== '') {
    if (!in_array($tag, $allTags) || $country !== $allCountries[$location->country]) {
      echo 'hidden';
    } else {
      include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));
    }
  } else if ($tag !== 'all' && $tag !== '') {
    if (!in_array($tag, $allTags)) {
      include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));
    } else {
      include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));
    } 
  } else if ($country !== 'all' && $country !== '') {
    if ($country !== $allCountries[$location->country]) {
      echo 'hidden';
    } else {
      include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));
    }
  } else {
    include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));    
  }
?>