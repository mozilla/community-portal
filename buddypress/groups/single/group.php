<?php
	get_header(); 

    // Lets get the group data
	do_action('bp_before_directory_groups_page');
    global $bp;
	
	$logged_in = mozilla_is_logged_in();
    $current_user = wp_get_current_user()->data;

    $template_dir = get_template_directory();
    include("{$template_dir}/languages.php");
    $months = array(
        '01' => 'Jan',
        '02' => 'Feb',
        '03' => 'Mar',
        '04' => 'Apr',
        '05' => 'May',
        '06' => 'Jun',
        '07' => 'Jul',
        '08' => 'Aug',
        '09' => 'uyp',
        '10' => 'Oct',
        '11' => 'Nov',
        '12' => 'Dec',
    );

    $group = $bp->groups->current_group;
    $group_meta = groups_get_groupmeta($group->id, 'meta');
    $invite_status = groups_get_groupmeta($group->id, 'invite_status');
    $member_count = groups_get_total_member_count($group->id);
    $user = wp_get_current_user();
    $is_member = groups_is_user_member($user->ID, $group->id);
    $admins = groups_get_group_admins($group->id);   
    $discourse_group = mozilla_get_discourse_info($group->id);

    $admin_count = sizeof($admins);
    $logged_in = mozilla_is_logged_in();

    $args = Array(
        'group_id'      =>  $group->id,
    );
	
	$tags = get_tags(Array('hide_empty' => false));
    $members = groups_get_group_members($args); 
    $is_admin = groups_is_user_admin($user->ID, $group->id);
	$current_user = wp_get_current_user()->data;
    switch($group->status) {
        case 'public':
            $verified = true;
            break;
        case 'private':
            $verified = false;
        default: 
            $verified = false;
	}

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
	
	$filtered_members = Array();
    $used_country_list = Array();
    $used_languages = Array();

	// Time to filter stuff
	if (isset($members['members']) && is_array($members['members']) && count($members['members']) > 0) {
		foreach($members['members'] AS $index => $member) {

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
	}
	$count = count($filtered_members);

?>
    <div class="content">
        <div class="group">
            <div class="group__container">
                <h1 class="group__title"><?php print __(str_replace('\\', '', stripslashes($group->name)), "community-portal"); ?></h1>
                <div class="group__details">
                    <?php if($verified): ?>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <ellipse cx="8" cy="7.97569" rx="8" ry="7.97569" fill="#0060DF"/>
                            <path d="M8 5.5L8.7725 7.065L10.5 7.3175L9.25 8.535L9.545 10.255L8 9.4425L6.455 10.255L6.75 8.535L5.5 7.3175L7.2275 7.065L8 5.5Z" fill="white" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <a href="https://discourse.mozilla.org/t/frequently-asked-questions-portal-edition-faq/43224" class="group__status">Verified</a>&nbsp;|
                    <?php else: ?>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.5 7.97569C15.5 12.103 12.1436 15.4514 8 15.4514C3.85643 15.4514 0.5 12.103 0.5 7.97569C0.5 3.84842 3.85643 0.5 8 0.5C12.1436 0.5 15.5 3.84842 15.5 7.97569Z" stroke="#B1B1BC"/>
                            <path d="M8 5.5L8.7725 7.065L10.5 7.3175L9.25 8.535L9.545 10.255L8 9.4425L6.455 10.255L6.75 8.535L5.5 7.3175L7.2275 7.065L8 5.5Z" fill="#B1B1BC" stroke="#B1B1BC" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <a href="https://discourse.mozilla.org/t/frequently-asked-questions-portal-edition-faq/43224" class="group__status">Unverified</a>&nbsp;|
                    <?php endif; ?>
                    <span class="group__location">
                    <?php 
                        if(isset($group_meta['group_city']) && strlen($group_meta['group_city']) > 0) {
                            if(isset($group_meta['group_country']) && strlen($group_meta['group_country']) > 1) {
                                print "<a href=\"/groups/?location={$group_meta['group_country']}\" class=\"group__status\">";
                            }

                            if(strlen($group_meta['group_city']) > 180) {
                                $group_meta['group_city'] = substr($group_meta['group_city'], 0, 180);
                            }

                            print "{$group_meta['group_city']}";
                            if(isset($group_meta['group_country']) && strlen($group_meta['group_country']) > 1) {
                                $country = $countries[$group_meta['group_country']];
                                print ", {$country}</a> | ";
                            } else {
                                print "|";
                            }
                        } else {
                            if(isset($group_meta['group_country']) && strlen($group_meta['group_country']) > 1) {
                                $country = $countries[$group_meta['group_country']];
                                print "<a href=\"/groups/?location={$group_meta['group_country']}\" class=\"group__status\">{$country}</a> | ";
                            }
                        }
                    ?>
                    </span>
                    <span class="group__created">
                    <?php
                        $created = date("F d, Y", strtotime($group->date_created));
                        print "<span> Created {$created}";
                    ?>
                    </span>
                </div>
                <div class="group__nav">
                    <ul class="group__menu">
                        <li class="menu-item"><a class="group__menu-link<?php if(bp_is_group_home() && !$is_events && !$is_people): ?> group__menu-link--active<?php endif; ?>" href="/groups/<?php print $group->slug; ?>"><?php print __("About us", "community-portal"); ?></a></li>
                        <li class="menu-item"><a class="group__menu-link<?php if($is_events): ?> group__menu-link--active<?php endif; ?>" href="/groups/<?php print $group->slug; ?>?view=events"><?php print __("Our Events", "community-portal"); ?></a></li>
                        <li class="menu-item"><a class="group__menu-link<?php if($is_people): ?> group__menu-link--active<?php endif; ?>" href="/groups/<?php print $group->slug; ?>/?view=people"><?php print __("Our Members", "community-portal"); ?></a></li>
                    </ul>
                </div>
                <div class="group__nav group__nav--mobile">
                    <label class="group__nav-select-label"><?php print __('Showing', "community-portal"); ?></label>
                    <div class="select-container">
                        <select class="group__nav-select">
                            <option value="/groups/<?php print $group->slug; ?>"<?php if(bp_is_group_home() && !$is_events && !$is_people): ?> selected<?php endif; ?>><?php print __("About us", "community-portal"); ?></option>
                            <option value="/groups/<?php print $group->slug; ?>?view=events"<?php if($is_events): ?> selected<?php endif; ?>><?php print __("Our Events", "community-portal"); ?></option>
                            <option value="/groups/<?php print $group->slug; ?>?view=people"<?php if($is_people): ?> selected<?php endif; ?>><?php print __("Our Members", "community-portal"); ?></option>
                        </select>
                    </div>
                </div>
                <section class="group__info">
                    <?php if($is_people): ?>
                    <div class="group__members-container">
                        <h2 class="group__card-title"><?php print __("Group Contacts", "community-portal")." ({$admin_count})"; ?></h2>
                        <div class="group__members">
                            <?php foreach($admins AS $admin): ?>
                            <?php 
                                $a = get_user_by('ID', $admin->user_id);                                
                                $is_me = $logged_in && intval($current_user->ID) === intval($admin->user_id);
                                $info = mozilla_get_user_info($current_user, $a, $logged_in);
                                
                                if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                                    $avatar_url = preg_replace("/^http:/i", "https:", $info['profile_image']->value);
                                } else {
                                    $avatar_url = $info['profile_image']->value;
                                }
                    
                            ?>

                            <a href="/people/<?php print $a->user_nicename; ?>" class="members__member-card">
                                <div class="members__avatar<?php if($info['profile_image']->display === false || $info['profile_image']->value === false): ?> members__avatar--identicon<?php endif; ?>" <?php if($info['profile_image']->display && $info['profile_image']->value): ?> style="background-image: url('<?php print $avatar_url; ?>')"<?php endif; ?> data-username="<?php print $a->user_nicename; ?>">

                                </div>
                                <div class="members__member-info">
                                    <div class="members__username"><?php print $a->user_nicename; ?></div>
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

				
                        <h2 class="group__card-title"><?php print __("People", "community-portal"); ?><?php echo " ({$members['count']})" ?></h2>
						<?php if ($members['count'] > 0): ?>
						<div class="members__search-container">
								<form method="GET" action="<?php echo $_SERVER['REQUEST_URI'] ?>" class="members__form" id="members-search-form">
									<div class="members__input-container">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M17.5 17.5L13.875 13.875" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<input type="hidden" value="people" name="view" id="view" />
									<input type="hidden" value="<?php if(isset($_GET['tag']) && strlen($_GET['tag']) > 0): print trim($_GET['tag']); endif; ?>" name="tag" id="user-tag" />
									<input type="hidden" value="<?php if(isset($_GET['location']) && strlen($_GET['location']) > 0): print trim($_GET['location']); endif; ?>" name="location" id="user-location" />
									<input type="hidden" value="<?php if(isset($_GET['language']) && strlen($_GET['language']) > 0): print trim($_GET['language']); endif; ?>" name="language" id="user-language" />
									<input type="text" name="u" id="members-search" class="members__search-input" placeholder="<?php print __("Search people", "community-portal"); ?>" value="<?php if($search_user): ?><?php print $search_user; ?><?php endif; ?>" />
									</div>
									<input type="submit" class="members__search-cta" value="<?php print __("Search", "community-portal"); ?>" />
								</form>
							</div>
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
						
                        <div class="group__members">
						<?php if(sizeof($filtered_members) > 0): ?>
							<?php if(isset($_GET['u']) && strlen($_GET['u']) > 0): ?><div class="members__results-for"><?php print __(sprintf("Results for \"%s\"", $search_user)). " ({$count})"; ?></div>
							<?php endif; ?>			
                            <?php foreach($filtered_members AS $member): ?>
                            <?php
                                $is_me = $logged_in && intval($current_user->ID) === intval($member->user_id);
                                $info = mozilla_get_user_info($current_user, $member, $logged_in);

                                if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                                    $avatar_url = preg_replace("/^http:/i", "https:", $info['profile_image']->value);
                                } else {
                                    $avatar_url = $info['profile_image']->value;
                                }
                    
                            ?>
                            <a href="/people/<?php print $member->user_nicename; ?>" class="members__member-card">
                                <div class="members__avatar<?php if($info['profile_image']->display === false || $info['profile_image']->value === false): ?> members__avatar--identicon<?php endif; ?>" <?php if($info['profile_image']->display && $info['profile_image']->value): ?> style="background-image: url('<?php print $avatar_url; ?>')"<?php endif; ?> data-username="<?php print $member->user_nicename; ?>">

                                </div>
                                <div class="members__member-info">
                                    <div class="members__username"><?php print $member->user_nicename; ?></div>
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
							<?php else: ?>
								<h2 class="members__title--no-members-found"><?php print __('No members found', "community-portal"); ?></h2>
							<?php endif; ?>
                        </div>  
						<?php else: ?>
							<p><?php print __('This group currently has no members', "community-portal"); ?></p>
						<?php endif; ?>
                    </div>
                    <?php elseif($is_events === true): ?>
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

                        $args = Array('group'   =>  $group->id, 'scope' =>  'all');
                        $events = EM_Events::get($args);                                            
                    ?>
                    <div class="row events__cards">
                    <?php foreach($events AS $event): ?>
                        <?php 
                            $categories = $event->get_categories();
                            $location = em_get_location($event->location_id);
                            $site_url = get_site_url();
                            $url = $site_url.'/events/'.$event->slug;  
                            $allCountries = em_get_countries();
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

                                                if($img_url && $img_url !== ''):?>style="background-image: url(<?php echo $img_url ?>)"<?php endif; ?>
                                        >
                                            <?php 

                                                $month = substr($event->start_date, 5, 1);
                                                $date = substr($event->start_date, 8, 2);
                                                $year = substr($event->start_date, 0, 4);
                                                
                                                $event_time = strtotime($event->start_date);
                                            ?>
                                            <p class="event-card__image__date"><span><?php echo date("M", $event_time); ?></span><span><?php echo date("d", $event_time); ?></span></p>
                                        </div>
                                        <div class="event-card__description">
                                            <h3 class="event-card__description__title title--event-card"><?php echo $event->event_name; ?></h2>
                                            <p><?php echo $months[$month].' '.$date.', '.$year.' @ '.substr($event->event_start_time, 0, 5).' - '.substr($event->event_end_time, 0, 5).' '.$event->event_timezone; ?></p>
                                            <div class="event-card__location">
                                                <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <p class="text--light text--small">
                                                    <?php
                                                        if($location->country === 'OE') {
                                                            echo __('Online Event', "community-portal");
                                                        } else {
                                                            if ($location->address) {
                                                                echo $location->address.' - '; 
                                                            }

                                                            if (strlen($location->town) > 0) {
                                                                echo $location->town;
                                                                if ($location->country) {
                                                                    echo ', '.$allCountries[$location->country];
                                                                }
                                                            } else {
                                                                echo $allCountries[$location->country];
                                                            }
                                                        }
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                        <ul class="events__tags">
                                            <?php
                                            if (is_array($categories->terms)): 
												if (count($categories->terms) <= 2): 
													foreach($categories->terms as $category) {
                                            ?>
													<li class="tag"><?php echo $category->name; ?></li>
												<?php
													break;
												}
												endif;
											endif;
                                            ?>
                                        </ul>
                                    </a>
                                </div>
                            </div>
                    <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="group__left-column">
                        <div class="group__card">
                            <?php if(isset($group_meta['group_image_url']) && strlen($group_meta['group_image_url']) > 0): ?>
                            <?php
                                                            
                                if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                                    $group_image_url = preg_replace("/^http:/i", "https:", $group_meta['group_image_url']);
                                } else {
                                    $group_image_url = $group_meta['group_image_url'];
                                }
                            ?>
                            <div class="group__card-image" style="background-image: url('<?php print $group_image_url; ?>');">
                                <?php if($is_admin): ?>
                                <a href="/groups/<?php print $group->slug; ?>/admin/edit-details/" class="group__edit-link">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z" fill="#0060DF"/>
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="group__card-no-image">
                                <?php if($is_admin): ?>
                                <a href="/groups/<?php print $group->slug; ?>/admin/edit-details/" class="group__edit-link">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M23.64 6.36L17.64 0.36C17.16 -0.12 16.44 -0.12 15.96 0.36L0.36 15.96C0.12 16.2 0 16.44 0 16.8V22.8C0 23.52 0.48 24 1.2 24H7.2C7.56 24 7.8 23.88 8.04 23.64L23.64 8.04C24.12 7.56 24.12 6.84 23.64 6.36ZM6.72 21.6H2.4V17.28L16.8 2.88L21.12 7.2L6.72 21.6Z" fill="#0060DF"/>
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <div class="group__card-content">
                                <div class="group__card-cta-container<?php if($is_admin): ?> group__card-cta-container--end<?php endif; ?>">
                                <?php if(!$is_admin): ?>
                                    <?php if($is_member): ?>
                                        <a href="#" class="group__leave-cta" data-group="<?php print $group->id; ?>"><?php print __('Leave Group', "community-portal"); ?></a>
                                    <?php else: ?>
                                        <?php if($invite_status === 'members' || $invite_status === ""): ?>
                                            <a href="#" class="group__join-cta" data-group="<?php print $group->id; ?>"><?php print __('Join Group', "community-portal"); ?></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <a href="#" class="group__share-cta">
                                    <svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 9V15C1 15.3978 1.15804 15.7794 1.43934 16.0607C1.72064 16.342 2.10218 16.5 2.5 16.5H11.5C11.8978 16.5 12.2794 16.342 12.5607 16.0607C12.842 15.7794 13 15.3978 13 15V9M10 4.5L7 1.5M7 1.5L4 4.5M7 1.5V11.25" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php print __('Share Group', "community-portal"); ?>
                                </a>
                                </div>
                                <hr class="group__keyline" />
                                <h2 class="group__card-title"><?php print __("About Us", "community-portal"); ?></h2>
                                <?php print wpautop(substr(trim($group->description), 0, 3000)); ?>
                                <?php if((isset($group_meta['group_telegram']) && strlen($group_meta['group_telegram']) > 0 ) 
                                || (isset($group_meta['group_facebook']) && strlen(trim($group_meta['group_facebook'])) > 0 ) 
                                || (isset($group_meta['group_discourse']) && strlen(trim($group_meta['group_discourse'])) > 0 ) 
                                || (isset($group_meta['group_github']) && strlen(trim($group_meta['group_github'])) > 0) 
								|| (isset($group_meta['group_twitter']) && strlen(trim($group_meta['group_twitter'])) > 0 )
                                || (isset($group_meta['group_matrix']) && strlen(trim($group_meta['group_matrix'])) > 0 )  
                                || (isset($group_meta['group_other']) && strlen($group_meta['group_other']) > 0)): ?>
                                <div class="group__community-links">
                                    <span class="no-line"><?php print __("Community Links", "community-portal"); ?></span>
                                    <?php if(isset($group_meta['group_telegram']) && strlen($group_meta['group_telegram']) > 0): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <path d="M24.3332 7.66699L15.1665 16.8337" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M24.3332 7.66699L18.4998 24.3337L15.1665 16.8337L7.6665 13.5003L24.3332 7.66699Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <a href="<?php print (mozilla_verify_url($group_meta['group_telegram'], false) ? mozilla_verify_url($group_meta['group_telegram'], false) : 'https://t.me/'.$group_meta['group_telegram']) ?>" class="group__social-link"><?php print __("Telegram", "community-portal"); ?></a>
                                        </div>
									<?php endif; ?>
                                    <?php if(isset($group_meta['group_facebook']) && strlen(trim($group_meta['group_facebook'])) > 0): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M26 16C26 10.4771 21.5229 6 16 6C10.4771 6 6 10.4771 6 16C6 20.9913 9.65686 25.1283 14.4375 25.8785V18.8906H11.8984V16H14.4375V13.7969C14.4375 11.2906 15.9304 9.90625 18.2146 9.90625C19.3087 9.90625 20.4531 10.1016 20.4531 10.1016V12.5625H19.1921C17.9499 12.5625 17.5625 13.3333 17.5625 14.1242V16H20.3359L19.8926 18.8906H17.5625V25.8785C22.3431 25.1283 26 20.9913 26 16Z" fill="black"/>
                                            </svg>
                                            <a href="<?php echo (mozilla_verify_url($group_meta['group_facebook'], true) ? mozilla_verify_url($group_meta['group_facebook'], true): 'https://www.facebook.com/'. $group_meta['group_facebook']); ?>" class="group__social-link"><?php print __("Facebook", "community-portal"); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($group_meta['group_discourse']) && strlen(trim($group_meta['group_discourse'])) > 0): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <path d="M23.5 15.5834C23.5029 16.6832 23.2459 17.7683 22.75 18.75C22.162 19.9265 21.2581 20.916 20.1395 21.6078C19.021 22.2995 17.7319 22.6662 16.4167 22.6667C15.3168 22.6696 14.2318 22.4126 13.25 21.9167L8.5 23.5L10.0833 18.75C9.58744 17.7683 9.33047 16.6832 9.33333 15.5834C9.33384 14.2682 9.70051 12.9791 10.3923 11.8605C11.084 10.7419 12.0735 9.838 13.25 9.25002C14.2318 8.75413 15.3168 8.49716 16.4167 8.50002H16.8333C18.5703 8.59585 20.2109 9.32899 21.4409 10.5591C22.671 11.7892 23.4042 13.4297 23.5 15.1667V15.5834Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
                                            <a href="<?php echo (mozilla_verify_url($group_meta['group_discourse'], true) ? mozilla_verify_url($group_meta['group_discourse'], true) : 'https://discourse.mozilla.org/u/'. $group_meta['group_discourse'] .'/summary') ?>" class="group__social-link"><?php print __("Discourse", "community-portal"); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($group_meta['group_github']) && strlen(trim($group_meta['group_github'])) > 0): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <g clip-path="url(#clip0)">
                                                <path d="M13.4998 22.6669C9.33317 23.9169 9.33317 20.5835 7.6665 20.1669M19.3332 25.1669V21.9419C19.3644 21.5445 19.3107 21.145 19.1757 20.77C19.0406 20.395 18.8273 20.053 18.5498 19.7669C21.1665 19.4752 23.9165 18.4835 23.9165 13.9335C23.9163 12.77 23.4687 11.6512 22.6665 10.8085C23.0464 9.79061 23.0195 8.66548 22.5915 7.66686C22.5915 7.66686 21.6082 7.37519 19.3332 8.90019C17.4232 8.38254 15.4098 8.38254 13.4998 8.90019C11.2248 7.37519 10.2415 7.66686 10.2415 7.66686C9.81348 8.66548 9.78662 9.79061 10.1665 10.8085C9.35828 11.6574 8.91027 12.7864 8.9165 13.9585C8.9165 18.4752 11.6665 19.4669 14.2832 19.7919C14.009 20.0752 13.7976 20.413 13.6626 20.7835C13.5276 21.1539 13.4722 21.5486 13.4998 21.9419V25.1669" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0">
                                                <rect width="20" height="20" fill="white" transform="translate(6 6)"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                            <a href="<?php print (mozilla_verify_url($group_meta['group_github'], true) ? mozilla_verify_url($group_meta['group_github'], true) : 'https://www.github.com/'.$group_meta['group_github']); ?>" class="group__social-link"><?php print __("Github", "community-portal"); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($group_meta['group_twitter']) && strlen(trim($group_meta['group_twitter'])) > 0): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <path d="M12.3766 23.9366C19.7469 23.9366 23.7781 17.8303 23.7781 12.535C23.7781 12.3616 23.7781 12.1889 23.7664 12.017C24.5506 11.4498 25.2276 10.7474 25.7656 9.94281C25.0343 10.2669 24.2585 10.4794 23.4641 10.5733C24.3006 10.0725 24.9267 9.28482 25.2258 8.35688C24.4392 8.82364 23.5786 9.15259 22.6812 9.32953C22.0771 8.6871 21.278 8.26169 20.4077 8.11915C19.5374 7.97661 18.6444 8.12487 17.8668 8.541C17.0893 8.95713 16.4706 9.61792 16.1064 10.4211C15.7422 11.2243 15.6529 12.1252 15.8523 12.9842C14.2592 12.9044 12.7006 12.4903 11.2778 11.7691C9.85506 11.0478 8.59987 10.0353 7.59375 8.7975C7.08132 9.67966 6.92438 10.724 7.15487 11.7178C7.38536 12.7116 7.98596 13.5802 8.83437 14.1467C8.19667 14.1278 7.57287 13.9558 7.01562 13.6452C7.01562 13.6616 7.01562 13.6788 7.01562 13.6959C7.01588 14.6211 7.33614 15.5177 7.9221 16.2337C8.50805 16.9496 9.32362 17.4409 10.2305 17.6241C9.64052 17.785 9.02155 17.8085 8.42109 17.6928C8.67716 18.489 9.17568 19.1853 9.84693 19.6843C10.5182 20.1832 11.3286 20.4599 12.1648 20.4756C10.7459 21.5908 8.99302 22.1962 7.18828 22.1944C6.86946 22.1938 6.55094 22.1745 6.23438 22.1366C8.0669 23.3126 10.1992 23.9363 12.3766 23.9334" fill="black"/>
                                            </svg>
                                            <a href="<?php print (mozilla_verify_url($group_meta['group_twitter'], true) ? mozilla_verify_url($group_meta['group_twitter'], true) : 'https://www.twitter.com/'.$group_meta['group_twitter']) ?>" class="group__social-link"><?php print __("Twitter", "community-portal"); ?></a>
                                        </div>
                                    <?php endif; ?>
									<?php if(isset($group_meta['group_matrix']) && strlen(trim($group_meta['group_matrix'])) > 0): ?>
                                        <div class="group__community-link-container">
											<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
												<circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
												<path d="M12.6113 12.6035L12.6729 13.4307C13.1969 12.7881 13.9056 12.4668 14.7988 12.4668C15.7513 12.4668 16.4053 12.8428 16.7607 13.5947C17.2803 12.8428 18.0208 12.4668 18.9824 12.4668C19.7845 12.4668 20.3815 12.7015 20.7734 13.1709C21.1654 13.6357 21.3613 14.3376 21.3613 15.2764V20H19.3789V15.2832C19.3789 14.8639 19.2969 14.5586 19.1328 14.3672C18.9688 14.1712 18.6794 14.0732 18.2646 14.0732C17.6722 14.0732 17.262 14.3558 17.0342 14.9209L17.041 20H15.0654V15.29C15.0654 14.8617 14.9811 14.5518 14.8125 14.3604C14.6439 14.1689 14.3568 14.0732 13.9512 14.0732C13.3906 14.0732 12.985 14.3057 12.7344 14.7705V20H10.7588V12.6035H12.6113Z" fill="black"/>
												<line x1="9" y1="9" x2="6" y2="9" stroke="black" stroke-width="2"/>
												<line x1="26" y1="9" x2="23" y2="9" stroke="black" stroke-width="2"/>
												<line x1="9" y1="24" x2="6" y2="24" stroke="black" stroke-width="2"/>
												<line x1="26" y1="24" x2="23" y2="24" stroke="black" stroke-width="2"/>
												<line x1="7" y1="9" x2="7" y2="23" stroke="black" stroke-width="2"/>
												<line x1="25" y1="9" x2="25" y2="23" stroke="black" stroke-width="2"/>
											</svg>
                                            <a href="<?php print (mozilla_verify_url($group_meta['group_matrix'], true) ? mozilla_verify_url($group_meta['group_matrix'], true) : 'https://chat.mozilla.org/#/room/'.$group_meta['group_matrix']) ?>" class="group__social-link"><?php print __("Matrix", "community-portal"); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($group_meta['group_other']) && strlen($group_meta['group_other']) > 0 && mozilla_verify_url($group_meta['group_other'], false)): ?>
                                        <div class="group__community-link-container">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="16" cy="16" r="16" fill="#CDCDD4"/>
                                                <g clip-path="url(#clip0)">
                                                <path d="M20.1668 23.5V21.8333C20.1668 20.9493 19.8156 20.1014 19.1905 19.4763C18.5654 18.8512 17.7176 18.5 16.8335 18.5H10.1668C9.28277 18.5 8.43493 18.8512 7.80981 19.4763C7.18469 20.1014 6.8335 20.9493 6.8335 21.8333V23.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M13.4998 15.1667C15.3408 15.1667 16.8332 13.6743 16.8332 11.8333C16.8332 9.99238 15.3408 8.5 13.4998 8.5C11.6589 8.5 10.1665 9.99238 10.1665 11.8333C10.1665 13.6743 11.6589 15.1667 13.4998 15.1667Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M25.1665 23.5001V21.8334C25.166 21.0948 24.9201 20.3774 24.4676 19.7937C24.0152 19.2099 23.3816 18.793 22.6665 18.6084" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M19.3335 8.6084C20.0505 8.79198 20.686 9.20898 21.1399 9.79366C21.5937 10.3783 21.84 11.0974 21.84 11.8376C21.84 12.5777 21.5937 13.2968 21.1399 13.8815C20.686 14.4661 20.0505 14.8831 19.3335 15.0667" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0">
                                                <rect width="20" height="20" fill="white" transform="translate(6 6)"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                            <a href="<?php print mozilla_verify_url($group_meta['group_other'], false); ?>" class="group__social-link"><?php print __("Other"); ?></a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if((isset($group_meta['group_meeting_details'])  && $group_meta['group_meeting_details']) || (isset($group_meta['group_address']) && $group_meta['group_address'])): ?>
                        <h2 class="group__card-title"><?php print __('Meetings', "community-portal"); ?></h2>
                        <div class="group__card">
                            <div class="group__card-content">
                                <span class="no-line"><?php print __('Meeting Details', "community-portal"); ?></span>
                                <?php if(isset($group_meta['group_meeting_details'])): ?>
                                <p class="group__card-copy">
                                    <?php print $group_meta['group_meeting_details']; ?>
                                </p>
                                <?php endif; ?>
                                <?php if(isset($group_meta['group_meeting_details']) && isset($group_meta['group_address'])): ?>
                                <hr />
                                <?php endif; ?>
                                <?php if(isset($group_meta['group_address']) && $group_meta['group_address']): ?>
                                <span class="no-line"><?php print __('Location', "community-portal"); ?></span>
                                <?php if(isset($group_meta['group_address_type']) && strtolower($group_meta['group_address_type']) == 'url'): ?>
                                    <div>
                                        <a class="group__meeting-location-link" href="<?php print $group_meta['group_address']; ?>" target="_blank"><?php print $group_meta['group_address']; ?></a>
                                    </div>
                                <?php else: ?>
                                    <p class="group__card-copy">
                                        <?php print $group_meta['group_address']; ?>
                                    </p>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>  
                        </div>
                        <?php endif; ?>
                        
                        <?php if(isset($discourse_group['discourse_category_url']) && strlen($discourse_group['discourse_category_url']) > 0): ?>
                        <?php 
                            $toptics = Array();
                            $options = wp_load_alloptions();

                            if(isset($options['discourse_url']) && strlen($options['discourse_url']) > 0) {
                                $discourse_api_url = rtrim($options['discourse_url'], '/');
                                $api_url = "{$options['discourse_url']}/c/{$discourse_group['discourse_category_id']}";
                                
                                $topics = mozilla_discourse_get_category_topics($api_url);
                                $topics = array_slice($topics, 0, 4);
                            }
                        ?>
                        <?php if(sizeof($topics) > 0): ?>
                        <h2 class="group__card-title"><?php print __('Discussions', "community-portal"); ?></h2>
                        <div class="group__card group__card--table">
                            <div class="group__card-content">
                                <table class="group__announcements">
                                    <thead>
                                        <tr>
                                            <th class="group__table-header group__table-header--topic"><?php print __('Topic', "community-portal"); ?></th>
                                            <th class="group__table-header"><?php print __('Replies', "community-portal"); ?></th>
                                            <th class="group__table-header"><?php print __('Views', "community-portal"); ?></th>
                                            <th class="group__table-header group__table-header--activity"><?php print __('Activity', "community-portal"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($topics AS $topic): ?>
                                        <tr>
                                            <td class="group__table-cell group__table-cell--topic">
                                                <a href="<?php print $options['discourse_url']; ?>/t/topic/<?php print $topic->id; ?>" class="group__topic-link">
                                                    <div class="group__topic-title"><?php print $topic->title; ?></div>
                                                    <div class="group__topic-date"><?php print date("F j, Y", strtotime($topic->created_at)); ?></div>
                                                </a>
                                            </td>
                                            <td class="group__table-cell">
                                                <?php 
                                                    $reply_count = intval($topic->posts_count) > 0  ? intval($topic->posts_count) - 1 : 0;
                                                ?>
                                                <div class="group__topic-replies"><?php print $reply_count; ?></div>
                                            </td>
                                            <td class="group__table-cell">
                                            <div class="group__topic-views"><?php print $topic->views; ?></div>
                                            </td>
                                            <td class="group__table-cell group__table-cell--activity">
                                                <div class="group__topic-activity"><?php print (isset($topic->last_posted_at) && strlen($topic->last_posted_at) > 0) ? date("M j", strtotime($topic->last_posted_at)) : date("M j", strtotime($topic->created_at)) ; ?></div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                        <tr>
                                            <td colspan="4" class="group__table-cell group__table-cell--topic">
                                                <a href="<?php print $discourse_group['discourse_category_url']; ?>" class="group__view-updates-link">
                                                    <?php print __('View more topics', "community-portal"); ?><svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2.33301 8.66732L5.99967 5.00065L2.33301 1.33398" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php endif;  ?>
                    </div>
                    <div class="group__right-column">
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Activity'); ?></span>
                                <?php 
                                    $args = Array(
                                        'group'     => $group->id,
                                        'scope'     => 'month',
                                    );

                                    $events = EM_Events::get($args);
                                    $event_count = sizeof($events);
                                ?>
                                <div class="group__member-count-container">
                                    <span class="group__event-count"><?php print $event_count; ?></span>
                                    Events this month
                                </div>
                                <div class="group__member-count-container">
                                    <a href="/groups/<?php print $group->slug?>?view=people" class="group__member-count"><?php print $member_count; ?></a>
                                    Members
                                </div>
                            </div>
                        </div>
                        <?php 
                            $args = Array(
                                'group'     => $group->id,
                                'orderby'   => 'event_start_date',
                                'order'     => 'DESC',
                                'scope'     =>  'all'
                            );
                            $events = EM_Events::get($args);
                            $event = isset($events[0]) ? $events[0] : false;
                            $event_time = strtotime($event->start_date);
                            $event_date = date("M d", $event_time);

           
                            $location = em_get_location($event->location_id);
                        ?>
                        <?php if($event): ?>
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Related Events', "community-portal"); ?></span>
                                <a class="group__event" href="/events/<?php print $event->event_slug; ?>"> 
                                    <div class="group__event-date">
                                        <?php print $event_date; ?>
                                    </div>
                                    <div class="group__event-info">
                                        <div class="group__event-title"><?php print $event->event_name; ?></div>
                                        <div class="group__event-time">
                                            <?php print date("M d, Y")." ∙ {$event->start_time}"; ?>
                                        </div>
                                        <div class="group__event-location">
                                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14 7.66602C14 12.3327 8 16.3327 8 16.3327C8 16.3327 2 12.3327 2 7.66602C2 6.07472 2.63214 4.54859 3.75736 3.42337C4.88258 2.29816 6.4087 1.66602 8 1.66602C9.5913 1.66602 11.1174 2.29816 12.2426 3.42337C13.3679 4.54859 14 6.07472 14 7.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M8 9.66602C9.10457 9.66602 10 8.77059 10 7.66602C10 6.56145 9.10457 5.66602 8 5.66602C6.89543 5.66602 6 6.56145 6 7.66602C6 8.77059 6.89543 9.66602 8 9.66602Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <?php if($location->location_country === 'OE'): ?>
											<?php print __("Online Event", "community-portal"); ?>
                                            <?php elseif($location->location_town && $location->location_country): ?>
                                                <?php print "{$location->location_town}, {$countries[$location->location_country]}"; ?>
                                            <?php elseif($location->location_town && !$location->location_country): ?>
                                                <?php print "{$location->location_town}"; ?>
                                            <?php elseif(!$location->location_town && $location->location_country): ?>
                                                <?php print "{$countries[$location->location_country]}"; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                                <a href="/groups/<?php print $group->slug; ?>/?view=events" class="group__events-link">
                                    <?php print __('View more events', "community-portal"); ?><svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.33301 8.66634L5.99967 4.99967L2.33301 1.33301" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Group Contacts', "community-portal"); ?></span> 
                                <div class="group__admins">
                                    <?php foreach($admins AS $admin): ?>
                                    <?php
                                        $u = get_userdata($admin->user_id);

                                        $is_me = $logged_in && intval($user->ID) === intval($admin->user_id);
                                        $logged_in = mozilla_is_logged_in();
                                        $is_me = $logged_in && intval($current_user->ID) === intval($admin->user_id);
                                    
                                        $info = mozilla_get_user_info($current_user, $u, $logged_in);
                                        

                                        if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                                            $avatar_url = preg_replace("/^http:/i", "https:", $info['profile_image']->value);
                                        } else {
                                            $avatar_url = $info['profile_image']->value;
                                        }

                                    ?>
                                    <a class="group__admin" href="/people/<?php print $u->user_nicename; ?>">
                                        <div class="members__avatar<?php if($info['profile_image']->display === false || $info['profile_image']->value === false): ?> members__avatar--identicon<?php endif; ?>" <?php if($info['profile_image']->display && $info['profile_image']->value): ?> style="background-image: url('<?php print $avatar_url; ?>')"<?php endif; ?> data-username="<?php print $u->user_nicename; ?>">
                                        </div>
                                        <div class="username">
                                            <div><?php print "@{$u->user_nicename}"; ?></div>
                                            <div class="group__admin-name">
                                                <?php if($info['first_name']->display && $info['first_name']->value): print $info['first_name']->value; ?><?php endif; ?>
                                                <?php if($info['last_name']->display && $info['last_name']->value): print $info['last_name']->value; ?><?php endif; ?>
                                            </div>
                                        </div>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php 

                        
                        ?>
                        <?php if(strlen($group_meta['group_language']) > 0 && array_key_exists(strtolower($group_meta['group_language']), $languages)): ?>
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Preferred Language', "community-portal"); ?></span>
                                <div class="group__tags">
                                    <div class="group__language">
                                        <a href="/groups/?language=<?php print strtolower($group_meta['group_language']); ?>" class="group__language-link"><?php print $languages[strtolower($group_meta['group_language'])]; ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if(sizeof(array_unique($group_meta['group_tags'])) > 0): ?>
                        <div class="group__card">
                            <div class="group__card-content group__card-content--small">
                                <span><?php print __('Tags', "community-portal"); ?></span>
                                <div class="group__tags">
                                    <?php foreach(array_unique($group_meta['group_tags']) AS $tag): ?>
                                    <a href="/groups/?tag=<?php print $tag; ?>" class="group__tag"><?php print $tag; ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </section>
                <?php if(isset($options['report_email'])): ?>
                <div class="group__report-container">
                    <a href="mailto:<?php print $options['report_email']; ?>?subject=<?php print sprintf('%s %s', __('Reporting Group', 'community-portal'), $group->name); ?>&body=<?php print __(sprintf('Please provide a reason you are reporting this group    %s', "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"), 'community-portal'); ?>" class="group__report-group-link">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 8V12" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="16" r="0.5" fill="#CDCDD4" stroke="#0060DF"/>
                        </svg>
                        <?php print __("Report Group", 'community-portal'); ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="groups-share-lightbox" class="lightbox">
      <?php include(locate_template('templates/share-modal.php', false, false)); ?>
    </div>