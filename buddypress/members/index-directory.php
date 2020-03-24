<?php
    get_header(); 
    $logged_in = mozilla_is_logged_in();
    $current_user = wp_get_current_user()->data;

    $template_dir = get_template_directory();
    include("{$template_dir}/languages.php");
    
    $members_per_page = 20;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 0;

    $offset = ($page - 1) * $members_per_page;

    if($offset < 0)
        $offset = 0;

    $args = Array('offset'  => 0, 'number'  =>  -1);

	$search_user = isset($_GET['u']) && strlen(trim($_GET['u'])) > 0 ? trim($_GET['u']) : false;
	if (
		isset($search_user) && 
		(strpos($search_user, '"') !== false || 
		strpos($search_user, "'") !== false || 
		strpos($search_user, '\\') !== false)
	) {
		$search_user = str_replace('\\', '', $search_user);
		$search_user = preg_replace('/^\"|\"$|^\'|\'$/', "", $search_user);
	}
	
    $first_name = false;
    $last_name = false;

    // We aren't searching a username rather a full name
    if($search_user && strpos($search_user, ' ') !== false) {
        $name = explode(' ', $search_user);
        if(is_array($name) && sizeof($name) === 2) {
            $first_name = $name[0];
            $last_name = $name[1];
        }
	}

    $country_code = isset($_GET['location']) && strlen($_GET['location']) > 0 ? strtoupper(trim($_GET['location'])) : false;
    $get_tag = isset($_GET['tag']) && strlen(trim($_GET['tag'])) > 0 ? strtolower(trim($_GET['tag'])) : false;
    $language_code = isset($_GET['language']) && strlen($_GET['language']) > 0 ? strtolower(trim($_GET['language'])) : false;

    $wp_user_query = new WP_User_Query(Array(
        'offset'    =>  0,
        'number'    =>  -1
    ));

    $members = $wp_user_query->get_results();
    $filtered_members = Array();
    $used_country_list = Array();
    $used_languages = Array();

    // Time to filter stuff
    foreach($members AS $index => $member) {

        $info = mozilla_get_user_info($current_user, $member, $logged_in);
        $member->info = $info;
        $member_tags = array_filter(explode(',', $info['tags']->value));
        $member_country = false;

        if($info['location']->display) {
            if(strpos($info['location']->value, ',') != false) {
                $member_country = explode(',', $info['location']->value);
                foreach($member_country AS $i => $part) {
                    $member_country[$i] = trim($part);
                }

                if(sizeof($member_country) == 2) 
                    $member_country = $member_country[1];
                else
                    $member_country = $info['location']->value;

            } else {
                $member_country = $info['location']->value;
            }

            $key = array_search($member_country, $countries);
            if($key)
                $used_country_list[$key] = $countries[$key];
        }

        
        if(isset($info['languages']) && $info['languages']->display && is_array($info['languages']->value)) {
            foreach($info['languages']->value AS $l) {
                $used_languages[$l] = $languages[$l];
            }
        }

        $used_languages = array_unique($used_languages);
        asort($used_languages);


        // All four criteria to search
        if($country_code && $get_tag && $search_user && $language_code) {
            // Country / Tag / Username / Language
            if($info['tags']->display && 
                $info['location']->display && 
                array_key_exists($country_code, $countries) && 
                strtolower($countries[$country_code]) === strtolower($member_country) && 
                in_array($get_tag, array_map('strtolower', $member_tags)) &&
                stripos($member->data->user_nicename, $search_user) !== false &&
                $info['languages']->display &&
                is_array($info['languages']->value) &&
                in_array($language_code, $info['languages']->value)
                )
            {   
                    $filtered_members[] = $member;
                    continue;
            }

            // Country / Tag / First Name / Language
            if($first_name) {
                if($info['tags']->display && 
                    $info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $first_name) !== false &&
                    $info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value)
                    )
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            } else {
                if($info['tags']->display && 
                    $info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $search_user) !== false &&
                    $info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value)
                    )
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            }

            // Country / Tag / Last Name / Language
            if($last_name) {
                if($info['tags']->display && $info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $last_name) !== false &&
                    $info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value)
                    )
                {   
                        $filtered_members[] = $member;
                        continue;
                }

            } else {
                if($info['tags']->display && $info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $search_user) !== false &&
                    $info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value)
                    )
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            }

            continue;
        }


        // Language / tag / search
        if($country_code === false && $get_tag && $search_user && $language_code) {

            // Language / Tag / Username
            if($info['tags']->display && 
                $info['languages']->display && 
                is_array($info['languages']->value) &&
                in_array($language_code, $info['languages']->value) &&
                in_array($get_tag, array_map('strtolower', $member_tags)) &&
                stripos($member->data->user_nicename, $search_user) !== false)
            {   
                    $filtered_members[] = $member;
                    continue;
            }

            // Language / Tag / First Name
            if($first_name) {
                if($info['tags']->display && 
                    $info['languages']->display && 
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value) &&
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $first_name) !== false)
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            } else {
                if($info['tags']->display && 
                    $info['languages']->display && 
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value) &&
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $search_user) !== false)
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            }

            // Language / Tag / Last Name
            if($last_name) {
                if($info['tags']->display && 
                    $info['languages']->display && 
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value) &&
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $last_name) !== false)
                {   
                        $filtered_members[] = $member;
                        continue;
                }

            } else {
                if($info['tags']->display && 
                    $info['languages']->display && 
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value) &&
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $search_user) !== false)
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            }

            continue;
        }

        // Country / tag / search
        if($country_code && $get_tag && $search_user && $language_code === false) {
            // Country / Tag / Username
            if($info['tags']->display && 
                $info['location']->display && 
                array_key_exists($country_code, $countries) && 
                strtolower($countries[$country_code]) === strtolower($member_country) && 
                in_array($get_tag, array_map('strtolower', $member_tags)) &&
                stripos($member->data->user_nicename, $search_user) !== false)
            {   
                    $filtered_members[] = $member;
                    continue;
            }

            // Country / Tag / First Name
            if($first_name) {
                if($info['tags']->display && 
                    $info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $first_name) !== false)
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            } else {
                if($info['tags']->display && 
                    $info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $search_user) !== false)
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            }

            // Country / Tag / Last Name
            if($last_name) {
                if($info['tags']->display && $info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $last_name) !== false)
                {   
                        $filtered_members[] = $member;
                        continue;
                }

            } else {
                if($info['tags']->display && $info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    in_array($get_tag, array_map('strtolower', $member_tags)) &&
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $search_user) !== false)
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            }

            continue;
        }


        // Location / language / tag
        if($search_user === false && $country_code && $language_code && $get_tag) {
            if($info['languages']->display && 
                $info['tags']->display && 
                in_array($get_tag, array_map('strtolower', $member_tags)) &&
                is_array($info['languages']->value) &&
                in_array($language_code, $info['languages']->value) &&
                $info['location']->display && 
                array_key_exists($country_code, $countries) && 
                strtolower($countries[$country_code]) === strtolower($member_country))
            {   
                    $filtered_members[] = $member;
                    continue;
            }


            continue;
        }
        

        // Search / location / language
        if($search_user && $get_tag === false && $country_code && $language_code) {
            if($info['language']->display && 
                $info['location']->display && 
                array_key_exists($country_code, $countries) && 
                strtolower($countries[$country_code]) === strtolower($member_country) && 
                is_array($info['languages']->value) &&
                in_array($language_code, $info['languages']->value) &&
                stripos($member->data->user_nicename, $search_user) !== false)
            {   
                    $filtered_members[] = $member;
                    continue;
            }

            // Country / First Name / Language
            if($first_name) {
                if($info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $first_name) !== false &&
                    $info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value)
                    )
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            } else {
                if($info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $search_user) !== false &&
                    $info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value)
                    )
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            }

            // Country / Tag / Last Name / Language
            if($last_name) {
                if($info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $last_name) !== false &&
                    $info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value)
                    )
                {   
                        $filtered_members[] = $member;
                        continue;
                }

            } else {
                if($info['location']->display && 
                    array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $search_user) !== false &&
                    $info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value)
                    )
                {   
                        $filtered_members[] = $member;
                        continue;
                }
            }
        
            continue;
        }


        // Country and search
        if($country_code && $search_user && $get_tag === false && $language_code === false) {
            $country_code = strtoupper(trim($_GET['location']));

            // Country and username
            if(array_key_exists($country_code, $countries) && 
                strtolower($countries[$country_code]) === strtolower($member_country) && 
                $info['location']->display &&
                stripos($member->data->user_nicename, $search_user) !== false) 
            {
                $filtered_members[] = $member;
                continue;
            }

            
            // Country and first name
            if($first_name) {
                if(array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    $info['location']->display &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $first_name) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }
            } else {
                if(array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    $info['location']->display &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $search_user) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }
            }

            // Country and last name
            if($last_name) {
                if(array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    $info['location']->display &&
                    $info['first_name']->display &&
                    stripos($info['last_name']->value, $last_name) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }
            } else {
                if(array_key_exists($country_code, $countries) && 
                    strtolower($countries[$country_code]) === strtolower($member_country) && 
                    $info['location']->display &&
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $search_user) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }
            }

            continue;
        }
        

        // Tag and search
        if($get_tag && $search_user && $country_code === false && $language_code === false) {
            // Tag and username
            if(in_array($get_tag, array_map('strtolower', $member_tags)) && 
                $info['tags']->display &&
                stripos($member->data->user_nicename, $search_user) !== false) 
            {
                $filtered_members[] = $member;
                continue;
            }

            // Tag and first name
            if($first_name) {
                if(in_array($get_tag, array_map('strtolower', $member_tags)) && 
                    $info['tags']->display &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $first_name) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }
            } else {
                if(in_array($get_tag, array_map('strtolower', $member_tags)) && 
                    $info['tags']->display &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $search_user) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }

            }

            // Tag and first name
            if($last_name) {
                if(in_array($get_tag, array_map('strtolower', $member_tags)) && 
                    $info['tags']->display &&
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $last_name) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }
            } else {
                if(in_array($get_tag, array_map('strtolower', $member_tags)) && 
                    $info['tags']->display &&
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $search_user) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }
            }

            continue;
        } 


        // Language and search
        if($get_tag === false && $search_user && $country_code === false && $language_code) {
            // Language and username
            if($info['languages']->display &&
                is_array($info['languages']->value) &&
                in_array($language_code, $info['languages']->value) &&
                stripos($member->data->user_nicename, $search_user) !== false) 
            {
                $filtered_members[] = $member;
                continue;
            }

            // Language and first name
            if($first_name) {
                if($info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value) &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $first_name) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }
            } else {
                if($info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value) &&
                    $info['first_name']->display &&
                    stripos($info['first_name']->value, $search_user) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }

            }

            // Language and last name
            if($last_name) {
                if($info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value) &&
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $last_name) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }
            } else {
                if($info['languages']->display &&
                    is_array($info['languages']->value) &&
                    in_array($language_code, $info['languages']->value) &&
                    $info['last_name']->display &&
                    stripos($info['last_name']->value, $search_user) !== false) 
                {
                    $filtered_members[] = $member;
                    continue;
                }
            }

            continue;
        } 


        // Language and tag 
        if($country_code === false && $get_tag && $search_user === false && $language_code) {
            if($info['languages']->display && 
                $info['tags']->display && 
                in_array($get_tag, array_map('strtolower', $member_tags)) &&
                is_array($info['languages']->value) &&
                in_array($language_code, $info['languages']->value))
            {   
                    $filtered_members[] = $member;
                    continue;
            }

            continue;
        }


        // Country and language
        if($country_code && $get_tag === false && $search_user === false && $language_code) {
            if($info['location']->display && 
                array_key_exists($country_code, $countries) && 
                strtolower($countries[$country_code]) === strtolower($member_country) && 
                $info['languages']->display &&
                is_array($info['languages']->value) &&
                in_array($language_code, $info['languages']->value))
            {   
                    $filtered_members[] = $member;
                    continue;
            }
            continue;
        }

        // Country and tag 
        if($country_code && $get_tag && $search_user === false && $language_code === false) {
			
            if($info['tags']->display && 
                $info['location']->display && 
                array_key_exists($country_code, $countries) && 
                strtolower($countries[$country_code]) === strtolower($member_country) && 
                in_array($get_tag, array_map('strtolower', $member_tags)))
            {   
                    $filtered_members[] = $member;
                    continue;
            }

            continue;
        }

        // Just Country
        if($country_code && $get_tag === false && $search_user === false && $language_code === false) {
            if($info['location']->display && 
                array_key_exists($country_code, $countries) && 
                strtolower($countries[$country_code]) === strtolower($member_country))
            {   
                    $filtered_members[] = $member;
                    continue;
            }

            continue;
        }

        // Just Tags
        if($get_tag && $country_code === false && $search_user === false && $language_code === false) {
            if($info['tags']->display && 
                in_array($get_tag, array_map('strtolower', $member_tags)))
            {   
                    $filtered_members[] = $member;
                    continue;
            }

            continue;
        }

        // Just language
        if($language_code && $get_tag === false && $country_code === false && $search_user === false) {
            if($info['languages']->display &&
                is_array($info['languages']->value) &&
                in_array($language_code, $info['languages']->value)
             ) {
                $filtered_members[] = $member;
                continue;
             }

             continue;
        }

        // Just search
        if($search_user && $country_code === false && $get_tag === false && $language_code === false) {
            // Username
            if(stripos($member->data->user_nicename, $search_user) !== false) {
                $filtered_members[] = $member;
                continue;
            }

            // First name
            if($first_name) {
                if($info['first_name']->display && stripos($info['first_name']->value, $first_name) !== false) {
                    $filtered_members[] = $member;
                    continue;
                }
            } else {
                if($info['first_name']->display && stripos($info['first_name']->value, $search_user) !== false) {
                    $filtered_members[] = $member;
                    continue;
                }
            }

            // Last name
            if($last_name) {
                if($info['last_name']->display && stripos($info['last_name']->value, $last_name) !== false) {
                    $filtered_members[] = $member;
                    continue;
                }
            } else {
                if($info['last_name']->display && stripos($info['last_name']->value, $search_user) !== false) {
                    $filtered_members[] = $member;
                    continue;
                }
            }

            continue;
        }
        
        $filtered_members[] = $member;
        
    }

    if($offset >= sizeof($filtered_members)) {
        $offset = sizeof($filtered_members) - $members_per_page;
    }

    $tags = get_tags(Array('hide_empty' => false));
    $members = array_slice($filtered_members, $offset, $members_per_page);

    $total_pages = ceil(sizeof($filtered_members) / $members_per_page);

?>
<div class="content">
    <div class="members">
        <div class="members__hero">
            <div class="members__hero-container">
                <h1 class="members__title"><?php print __("People", "community-portal"); ?></h1>
                <p class="members__hero-copy">
                    <?php print __("Ready to make it official? Set up a profile to attend events, join groups and manage your subscription settings. ", "community-portal"); ?>
                </p>
                <div class="members__search-container">
                    <form method="GET" action="/people/" class="members__form" id="members-search-form">
                        <div class="members__input-container">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.5 17.5L13.875 13.875" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input type="hidden" value="<?php if(isset($_GET['tag']) && strlen($_GET['tag']) > 0): print trim($_GET['tag']); endif; ?>" name="tag" id="user-tag" />
                        <input type="hidden" value="<?php if(isset($_GET['location']) && strlen($_GET['location']) > 0): print trim($_GET['location']); endif; ?>" name="location" id="user-location" />
                        <input type="hidden" value="<?php if(isset($_GET['language']) && strlen($_GET['language']) > 0): print trim($_GET['language']); endif; ?>" name="language" id="user-language" />
                        <input type="text" name="u" id="members-search" class="members__search-input" placeholder="<?php print __("Search people", "community-portal"); ?>" value="<?php if($search_user): ?><?php print $search_user; ?><?php endif; ?>" />
                        </div>
                        <input type="submit" class="members__search-cta" value="<?php print __("Search", "community-portal"); ?>" />
                    </form>
                </div>
            </div>
        </div>
        <div class="members__container">
            <div class="members__filter-container members__filter-container--hidden">
                <span><?php print __("Search criteria:", "community-portal"); ?></span>
                <div class="members__select-container">
                    <label class="members__label">Location </label>
                    <select class="members__location-select">
                        <option value=""><?php print __('Select', "community-portal"); ?></option>
                        <?php foreach($used_country_list AS $code   =>  $country): ?>
                        <option value="<?php print $code; ?>"<?php if(isset($_GET['location']) && strlen($_GET['location']) > 0 && $_GET['location'] == $code): ?> selected<?php endif; ?>><?php print $country; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if(sizeof($used_languages) > 0): ?>
                <div class="members__select-container">
                    <label class="members__label">Language </label>
                    <select class="members__language-select">
                        <option value=""><?php print __('Select', "community-portal"); ?></option>
                        <?php foreach($used_languages AS $code =>   $language): ?>
                        <?php if(strlen($code) > 1): ?>
                        <option value="<?php print $code; ?>" <?php if(isset($_GET['language']) && strtolower(trim($_GET['language'])) == strtolower($code)): ?> selected<?php endif; ?>><?php print $language; ?></option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </select>  
                </div>
                <?php endif; ?>
                <div class="members__select-container">
                    <label class="members__label">Tag </label>
                    <select class="members__tag-select">
                        <option value=""><?php print __('Select', "community-portal"); ?></option>
                        <?php foreach($tags AS $tag): ?>
                        <option value="<?php print $tag->slug; ?>" <?php if(isset($_GET['tag']) && strtolower(trim($_GET['tag'])) == strtolower($tag->slug)): ?> selected<?php endif; ?>><?php print $tag->name; ?></option>
                        <?php endforeach; ?>
                    </select>  
                </div>
            </div>
            <div class="members__show-filters-container">
                <a href="#" class="members__show-filter"><?php print __("Show Filters"); ?></a>
            </div>
            <div class="members__people-container">
            <?php if(sizeof($members) > 0): ?>
            <?php if(isset($_GET['u']) && strlen($_GET['u']) > 0): ?><div class="members__results-for"><?php print __(sprintf("Results for \"%s\"", $search_user)); ?></div><?php endif; ?>
            <?php foreach($members AS $member): ?>
            <?php 
                $info = $member->info;
                
                if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                    $avatar_url = preg_replace("/^http:/i", "https:", $info['profile_image']->value);
                } else {
                    $avatar_url = $info['profile_image']->value;
                }
                
            ?>
            <a href="/people/<?php print $member->data->user_nicename; ?>" class="members__member-card">
                <div class="members__avatar<?php if($info['profile_image']->display === false || $info['profile_image']->value === false): ?> members__avatar--identicon<?php endif; ?>" <?php if($info['profile_image']->display && $info['profile_image']->value): ?> style="background-image: url('<?php print $avatar_url; ?>')"<?php endif; ?> data-username="<?php print $member->data->user_nicename; ?>">
                </div>
                <div class="members__member-info">
                    <div class="members__username"><?php print $member->data->user_nicename; ?></div>
                    <div class="members__name">
                        <?php 
                            if($info['first_name']->display && $info['first_name']->value) {
                                print $info['first_name']->value;
                            }

                            if($info['last_name']->display && $info['last_name']->value) {
                                print " {$info['last_name']->value}";
                            }
                        ?>
                    </div>
                    <?php if($info['location']->display && $info['location']->value): ?>
                    <div class="members__location">
                        <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>&nbsp;
                        <?php print $info['location']->value; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
            </div>
            <?php else: ?>
                <h2 class="members__title--no-members-found"><?php print __('No members found', "community-portal"); ?></h2>
            <?php endif; ?>
            <?php 
                $range = ($page > 3) ? 3 : 5;
                
                if($page > $total_pages - 2) 
                    $range = 5;
                
                $previous_page = ($page > 1) ? $page - 1 : 1;
                $next_page = ($page < $total_pages) ? $page + 1 : $total_pages;

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

                    if($page_min < 0) {
                        $page_min = 1;
                    }

                    if($page < 1) {
                        $page = 1;
                    }

                    if($page > $page_max) {
                        $page = intval($page_max);
                    }
                }
            
            ?>
            <div class="members__pagination">
                <div class="members__pagination-container">
                    <?php if($total_pages > 1): ?>
                    <a href="/people/?page=<?php print $previous_page?><?php if($search_user): ?>&u=<?php print $search_user; ?><?php endif; ?><?php if(isset($_GET['location'])): ?>&location=<?php print $_GET['location']; ?><?php endif; ?><?php if(isset($_GET['tag'])): ?>&tag=<?php print $_GET['tag']; ?><?php endif; ?>" class="members__pagination-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M17 23L6 12L17 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <?php if($page_min > 1): ?><a href="/people/?page=1<?php if($search_user): ?>&u=<?php print $search_user; ?><?php endif; ?><?php if(isset($_GET['location'])): ?>&location=<?php print $_GET['location']; ?><?php endif; ?><?php if(isset($_GET['tag'])): ?>&tag=<?php print $_GET['tag']; ?><?php endif; ?>" class="members__pagination-link members__pagination-link--first"><?php print "1"; ?></a>&hellip; <?php endif; ?>
                    <?php for($x = $page_min - 1; $x < $page_max; $x++): ?>
                    <a href="/people/?page=<?php print $x + 1; ?><?php if($search_user): ?>&u=<?php print $search_user; ?><?php endif; ?><?php if(isset($_GET['location'])): ?>&location=<?php print $_GET['location']; ?><?php endif; ?><?php if(isset($_GET['tag'])): ?>&tag=<?php print $_GET['tag']; ?><?php endif; ?>" class="members__pagination-link<?php if($page === $x + 1):?> members__pagination-link--active<?php endif; ?><?php if($x === $page_max - 1):?> members__pagination-link--last<?php endif; ?>"><?php print ($x + 1); ?></a>
                    <?php endfor; ?>
                    <?php if($total_pages > $range && $page < $total_pages - 1): ?>&hellip; <a href="/people/?page=<?php print $total_pages; ?><?php if($search_user): ?>&u=<?php print $search_user; ?><?php endif; ?><?php if(isset($_GET['location'])): ?>&location=<?php print $_GET['location']; ?><?php endif; ?><?php if(isset($_GET['tag'])): ?>&tag=<?php print $_GET['tag']; ?><?php endif; ?>" class="members__pagination-link<?php if($page === $total_pages):?> members__pagination-link--active<?php endif; ?>"><?php print $total_pages; ?></a><?php endif; ?>
                    <a href="/people/?page=<?php print $next_page; ?><?php if($search_user): ?>&u=<?php print $search_user; ?><?php endif; ?><?php if(isset($_GET['location'])): ?>&location=<?php print $_GET['location']; ?><?php endif; ?><?php if(isset($_GET['tag'])): ?>&tag=<?php print $_GET['tag']; ?><?php endif; ?>" class="members__pagination-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M7 23L18 12L7 1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 


    get_footer();
?>