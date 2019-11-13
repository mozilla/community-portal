<?php
    $events = EM_Events::get($args);
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
    $args['scope'] = 'past';      
    foreach ($events as $event) {
      include(locate_template('plugins/events-manager/templates/template-parts/event-cards.php', false, false));
    }
