<?php
    $page = isset($_REQUEST['pno']) ? intval($_REQUEST['pno']) : 1;
	$args = apply_filters('em_content_events_args', $args);
	if (
		isset($args['search']) && 
		(strpos($args['search'], '"') !== false || 
		strpos($args['search'], "'") !== false || 
		strpos($args['search'], '\\') !== false)
	) {
		$args['search'] = preg_replace('/^\"|\"$|^\'|\'$/', "", $args['search']);
		$original_search = $args['search'];
		$args['search'] = addslashes($args['search']);
	} else {
		$original_search = $args['search'];
	}
    $view = get_query_var('view', $default = '');
    $country = urldecode(get_query_var('country', $default = 'all'));
    $tag = urldecode(get_query_var('tag', $default = 'all'));

    $args['scope'] = 'future';
    switch(strtolower(trim($view))) {
        case 'past': 
            $args['scope'] = 'past';
            break;
        case 'organized':
            if(is_user_logged_in()) {
                $user_id = get_current_user_id();
                $args['scope'] = 'all';
                $args['owner'] = $user_id;
                $args['status'] = false;
            }
            break;
        case 'attending':
            $args['scope'] = 'all';
            $args['bookings'] = 'user';
            break;
    }

    if($country !== 'all') {
        $args['country'] = $country;
    } 

    if($tag !== 'all') {
        $args['category'] = $tag;
    }

    $args['limit'] = '0';
    $all_events = EM_Events::get($args);  
    $events = Array();
	$initiative = isset($_GET['initiative']) && strlen($_GET['initiative']) > 0 && strtolower($_GET['initiative']) !== 'all' ? $_GET['initiative'] : false;
	$language = isset($_GET['language']) && strlen($_GET['language']) > 0 && strtolower($_GET['language']) !== 'all' ? $_GET['language'] : false;
	if ($initiative || $language) {
		foreach($all_events AS $e) {
			$event_meta = get_post_meta($e->post_id, 'event-meta');
			if ($initiative && $language) {
				if(
					(isset($event_meta[0]->initiative) && intval($event_meta[0]->initiative) === intval($_GET['initiative'])) && 
					(isset($event_meta[0]->language) && strtolower($event_meta[0]->language) === strtolower($_GET['language']))
				) {
					$events[] = $e;
				}
			} elseif ($initiative) {
				if (isset($event_meta[0]->initiative) && intval($event_meta[0]->initiative) === intval($_GET['initiative'])) {
					$events[] = $e;
				}
			} else {
				if (isset($event_meta[0]->language) && strtolower($event_meta[0]->language) === strtolower($_GET['language'])) {
					$events[] = $e;
				}
			}
			
		}
    } else {
        $events = $all_events;
	}


    $events_per_page = 12;
    $offset = ($page - 1) * $events_per_page;

    $event_count = sizeof($events);
    $events = array_slice($events, $offset, $events_per_page);
    $total_pages = ceil($event_count / $events_per_page);

?>

<div class="row events">
    <div class="events__nav__container">
        <ul class="col-md-12 center-md events__nav">
            <li class="events__nav__item">
                <a 
                class="events__nav__link <?php if ($view === 'future' | $view === "") echo esc_attr("events__nav__link--active") ?>" 
                href="<?php echo add_query_arg(array('view' => 'future', 'country' => $country, 'tag' => $tag), get_site_url('', 'events')) ?>"
                >
                    <?php echo __('Upcoming Events', "community-portal"); ?>
                </a>
            </li>
            <?php 
                $logged_in = is_user_logged_in();
            ?>
            <?php if ($logged_in): ?>
            <li class="events__nav__item">
                <a 
                class="events__nav__link <?php if ($view === 'attending') echo esc_attr("events__nav__link--active") ?>" 
                href="<?php echo add_query_arg(array('view' => 'attending', 'country' => $country, 'tag' => $tag), get_site_url('', 'events'), get_site_url('','events')) ?>"
                >
                    <?php echo __('Events I\'m attending', "community-portal") ?>
                </a>
            </li>
            <li class="events__nav__item">
                <a 
                class="events__nav__link <?php if ($view === 'organized') echo esc_attr("events__nav__link--active") ?>" 
                href="<?php echo add_query_arg(array('view' => 'organized', 'country' => $country, 'tag' => $tag), get_site_url('', 'events'), get_site_url('','events')) ?>"
                >
                    <?php echo __('My Events', "community-portal"); ?>
                </a>
            </li>
            <?php endif; ?>
            <li class="events__nav__item">
                <a 
                class="events__nav__link <?php if ($view === 'past') echo esc_attr("events__nav__link--active") ?>" 
                href="<?php echo add_query_arg(array('view' => 'past', 'country' => $country, 'tag' => $tag), get_site_url('', 'events'), get_site_url('','events'))?>"
                >
                    <?php print __('Past events', "community-portal"); ?>
                </a>
            </li>
        </ul>
        <form class="events__nav--mobile" action="">
            <label class="events__nav__label--mobile" for="eventsView"><?php echo __('Showing:') ?></label>
            <select class="events__nav__options--mobile" name="eventsView" id="eventsView">
                <option <?php if ($view === 'future' || $view === '') echo esc_attr('selected') ?> value="future"><?php echo __('Upcoming Events') ?></option>
                <?php if($logged_in): ?><option <?php if ($view === 'attending') echo esc_attr('selected') ?> value="attending"><?php echo __('Events I\'m Attending') ?></option><?php endif; ?>
                <?php if($logged_in): ?><option <?php if ($view === 'organized') echo esc_attr('selected') ?> value="organized">Events I've Organized</option><?php endif; ?>
                <option <?php if ($view === 'past') echo esc_attr('selected') ?> value="past"><?php echo __('Past Events') ?></option>
            </select>
            <svg class="events__nav__icon" width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.5 3.5L7 9L12.5 3.5" fill="white"/>
                <path d="M1.5 3.5L7 9L12.5 3.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </form>
    </div>
    <?php include(locate_template('plugins/events-manager/templates/template-parts/events-filters.php', false, false)); ?>
    <?php if(count($events)): ?>
    <?php if(isset($original_search)): ?>
        <div class="col-sm-12 events__search-terms">
            <p><?php echo __('Results for "'.$original_search.'"')?></p>
        </div>
    <?php endif; ?>
    <div class="row events__cards">
    <?php
        foreach($events AS $event) {
            include(locate_template('plugins/events-manager/templates/template-parts/event-cards.php', false, false));
        }
    ?>
    <?php 
        $range = ($page > 3) ? 3 : 5;
        
        if($page > $total_pages - 2) 
            $range = 5;
        
        $previous_page = ($page > 1) ? $page - 1 : 1;
        $next_page = ($page <= $total_pages) ? $page + 1 : $total_pages;

        if($total_pages > 1 ) {
            $range_min = ($range % 2 == 0) ? ($range / 2) - 1 : ($range - 1) / 2;
            $range_max = ($range % 2 == 0) ? $range_min + 1 : $range_min;

            $page_min  = $page - $range_min;
            $page_max = $page + $range_max;

            $page_min = ($page_min < 1 ) ? 1 : $page_min;
            $page_max = ($page_max < ($page_min + $range - 1)) ? $page_min + $range - 1 : $page_max;

            if($page_max > $total_pages) {
                $page_min = ($page_min > 1) ? $total_pages - $range + 1 : 1;
                $page_max = $total_pages;
            }
        }
    ?>
    <div class="campaigns__pagination">
        <div class="campaigns__pagination-container">
            <?php if($total_pages > 1): ?>
			<a href="/events/?pno=<?php print $previous_page?><?php if($country && $country != 'all'): ?>&country=<?php print $country; ?><?php endif; ?><?php if($tag && $tag != 'all'): ?>&tag=<?php print $tag; ?><?php endif; ?><?php if(isset($_GET['initiative']) && strlen($_GET['initiative']) > 0 && strtolower($_GET['initiative']) != 'all'): ?>&initiative=<?php print $_GET['initiative']; ?><?php endif; ?><?php if (isset($_GET['language']) && strlen($_GET['language']) > 0 && strtolower($_GET['language']) !== 'all'):?>&language=<?php print $_GET['language']; ?><?php endif; ?><?php if(strlen($view) > 0): ?>&view=<?php print trim($view); ?><?php endif; ?>" class="campaigns__pagination-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M17 23L6 12L17 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <?php if($page_min > 1): ?>
				<a href="/events/?pno=1<?php if($country && $country != 'all'): ?>&country=<?php print $country; ?><?php endif; ?><?php if($tag && $tag != 'all'): ?>&tag=<?php print $tag; ?><?php endif; ?><?php if(isset($_GET['initiative']) && strlen($_GET['initiative']) > 0 && strtolower($_GET['initiative']) != 'all'): ?>&initiative=<?php print $_GET['initiative']; ?><?php endif; ?><?php if (isset($_GET['language']) && strlen($_GET['language']) > 0 && strtolower($_GET['language']) !== 'all'):?>&language=<?php print $_GET['language']; ?><?php endif; ?><?php if(strlen($view) > 0): ?>&view=<?php print trim($view); ?><?php endif; ?>" 
					class="campaigns__pagination-link campaigns__pagination-link--first"><?php print "1"; ?>
				</a>
					&hellip; 
					<?php endif; ?>
            <?php for($x = $page_min - 1; $x < $page_max; $x++): ?>
            <a href="/events/?pno=<?php print $x + 1; ?><?php if($country && $country != 'all'): ?>&country=<?php print $country; ?><?php endif; ?><?php if($tag && $tag != 'all'): ?>&tag=<?php print $tag; ?><?php endif; ?><?php if(isset($_GET['initiative']) && strlen($_GET['initiative']) > 0 && strtolower($_GET['initiative']) != 'all'): ?>&initiative=<?php print $_GET['initiative']; ?><?php endif; ?><?php if (isset($_GET['language']) && strlen($_GET['language']) > 0 && strtolower($_GET['language']) !== 'all'):?>&language=<?php print $_GET['language']; ?><?php endif; ?><?php if(strlen($view) > 0): ?>&view=<?php print trim($view); ?><?php endif; ?>" 
				class="campaigns__pagination-link<?php if($page == $x + 1):?> campaigns__pagination-link--active<?php endif; ?><?php if($x === $page_max - 1):?> campaigns__pagination-link--last<?php endif; ?>"><?php print ($x + 1); ?>
			</a>
            <?php endfor; ?>
            <?php if($total_pages > $range && $page < $total_pages - 1): ?>&hellip; 
				<a href="/events/?pno=<?php print $total_pages; ?><?php if($country && $country != 'all'): ?>&country=<?php print $country; ?><?php endif; ?><?php if($tag && $tag != 'all'): ?>&tag=<?php print $tag; ?><?php endif; ?><?php if(isset($_GET['initiative']) && strlen($_GET['initiative']) > 0 && strtolower($_GET['initiative']) != 'all'): ?>&initiative=<?php print $_GET['initiative']; ?><?php endif; ?><?php if (isset($_GET['language']) && strlen($_GET['language']) > 0 && strtolower($_GET['language']) !== 'all'):?>&language=<?php print $_GET['language']; ?><?php endif; ?><?php if(strlen($view) > 0): ?>&view=<?php print trim($view); ?><?php endif; ?>" 
					class="campaigns__pagination-link<?php if($page === $total_pages):?> campaigns__pagination-link--active<?php endif; ?>"><?php print $total_pages; ?>
				</a>
			<?php endif; ?>
            <a href="/events/?pno=<?php print $next_page; ?><?php if($country && $country != 'all'): ?>&country=<?php print $country; ?><?php endif; ?><?php if($tag && $tag != 'all'): ?>&tag=<?php print $tag; ?><?php endif; ?><?php if(isset($_GET['initiative']) && strlen($_GET['initiative']) > 0 && strtolower($_GET['initiative']) != 'all'): ?>&initiative=<?php print $_GET['initiative']; ?><?php endif; ?><?php if(strlen($view) > 0): ?>&view=<?php print $view; ?><?php endif; ?>" class="campaigns__pagination-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M7 23L18 12L7 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
        <div class="events__zero-state col-sm-12">
            <p><?php echo ($original_search ? __('No results found. Please try another search term.', "community-portal") : __('There are currently no events.', "community-portal")) ?></p>
        </div>
    <?php endif; ?>
    </div>
</div>