<?php 
  $location = em_get_location($event->location_id);
  $categories = get_the_terms($event->post_id, EM_TAXONOMY_CATEGORY);
  $allCountries = em_get_countries();
  if (isset($categories) && is_array($categories)) {
    $allTags = array_map(function($n) {
      return $n->name;
    }, $categories);
  }
  if ($tag !== 'all' && !$categories && $tag !== '') {
    return;
  } else if ($tag !== 'all' && $country !== 'all' && $tag !== '' && $country !== '') {
    if (!in_array($tag, $allTags) || $country !== $allCountries[$location->country]) {
      return;
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
      return;
    } else {
      include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));
    }
  } else {
    include(locate_template('plugins/events-manager/templates/template-parts/single-event-card.php', false, false));    
  }
?>