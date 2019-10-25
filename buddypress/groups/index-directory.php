<?php 
    // Override the buddypress group listing page template

    // Main header template 
    get_header(); 


    // Execute actions by buddypress
    do_action('bp_before_directory_groups_page');
    do_action('bp_before_directory_groups');

    $groups = groups_get_groups(Array(
        'search_columns'    =>  'name',
        'serach_terms'      =>  'Mozilla'
    ));

    $logged_in = mozilla_is_logged_in();
    $tags = get_tags(Array('hide_empty' => false));
?>


<div class="content">
    <?php do_action('bp_before_directory_groups_content'); ?>
    <?php 
        
        global $wp_query;
        $id = $wp_query->get_queried_object_id();
        $page = get_page($id);
    
    ?>
    <div class="groups">
        <div class="groups__hero">
            <div class="groups__hero-container">
                <h1 class="groups__title"><?php print __("Groups"); ?></h1>
                <p class="groups__hero-copy">
                    <?php print __("A short paragraph about why groups are great and why people should look for some near them."); ?>
                </p>
                <p class="groups__hero-copy">
                    <?php print __("Find a group near you below, or"); ?> <a href="/groups/create/step/group-details/" class="groups__hero-link"><?php print __('create a group'); ?></a>
                </p>
                <div class="groups__search-container">
                    <form method="GET" action="/groups/" class="groups__form">
                        <div class="groups__input-container">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.5 17.5L13.875 13.875" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                        <input type="text" name="u" id="groups-search" class="groups__search-input" placeholder="<?php print __("Search"); ?>" value="<?php if($search_user): ?><?php print $search_user; ?><?php endif; ?>" />
                        </div>
                        <input type="submit" class="groups__search-cta" value="<?php print __("Search"); ?>" />
                    </form>
                </div>
            </div>
        </div>
       
        <div class="groups__container">
            <div class="groups__nav">
                <ul class="groups__menu">
                    <li class="menu-item"><a class="groups__menu-link group__menu-link--active" href="/groups/"><?php print __("Discover Groups"); ?></a></li>
                    <?php if($logged_in): ?><li class="menu-item"><a class="groups__menu-link" href=""><?php print __("Groups I'm in"); ?></a></li><?php endif; ?>
                </ul>
            </div>
            <div class="groups__nav groups__nav--mobile">
                <select class="groups__nav-select">
                    <option value=""><?php print __("Discover Groups"); ?></option>
                    <?php if($logged_in): ?><option value=""><?php print __("Groups I'm in"); ?></option><?php endif; ?>
                </select>
            </div>
            <div class="groups__filter-container">
                <span><?php print __("Filter by:"); ?></span>
                <div class="groups__select-container">
                    <label class="groups__label">Location: </label>
                    <select class="groups__location-select">
                        <option><?php print __('All'); ?></option>
                    <?php foreach($countries AS $code   =>  $country): ?>
                        <option value="<?php print $code; ?>"><?php print $country; ?></option>
                    <?php endforeach; ?>
                    </select>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                </div>
                <div class="groups__select-container">
                    <label class="groups__label">Tag: </label>
                    <select class="groups__tag-select">
                    <option><?php print __('All'); ?></option>
                    <?php foreach($tags AS $tag): ?>
                        <option value="<?php print $tag->name; ?>"><?php print $tag->name; ?></option>
                    <?php endforeach; ?>
                    </select>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>                
                </div>
            </div>
            <div class="groups__list-container">
                <?php do_action('bp_before_groups_loop'); ?>
               
                        <?php do_action('bp_directory_groups_item'); ?>
           
                    <?php do_action('bp_after_directory_groups_list'); ?>
            
                    <div id="message" class="info">
                        <p><?php _e( 'There were no groups found.', 'buddypress' ); ?></p>
                    </div>
             
                <?php do_action('bp_after_groups_loop'); ?>
            </div>
        </div>
    </div>
    <?php do_action('bp_after_directory_groups_content'); ?>
</div>
<?php
    // Main footer template
    get_footer();

    // Fire at the end of template
    do_action('bp_after_directory_groups_page');
?>
