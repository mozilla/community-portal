<?php 
    // Override the buddypress group listing page template

    // Main header template 
    get_header(); 


    // Execute actions by buddypress
    do_action('bp_before_directory_groups_page');
    do_action('bp_before_directory_groups');
?>


<div class="content">
    <?php do_action('bp_before_directory_groups_content'); ?>

    <div class="groups">
        <div class="groups__container">
            <div class="groups__left">
                <h2 class="groups__title"><?php print __("Groups"); ?></h2>
                <a href="" class="groups__button groups__button--active"><?php print __("Discover Groups"); ?></a>
                <a href="" class="groups__button"><?php print __("Groups I'm in"); ?></a>
                <a href="" class="groups__button"><?php print __("Create Group"); ?></a>
            </div>
            <div class="groups__right">
                <div class="groups__search-container">
                    <div class="groups__search-input-container">
                        <input type="text" placeholder="<?php print __("Search Mozilla Groups"); ?>" class="groups__search-input" /> 
                        <button class="groups__search-button"><?php print __("Apply"); ?></button> 
                    </div>
                </div>
                <div class="groups__list-container">
                    <?php do_action('bp_before_groups_loop'); ?>
                    <?php if(bp_has_groups(bp_ajax_querystring('groups'))): ?>
                        <?php do_action('bp_before_directory_groups_list'); ?>
                        <?php while ( bp_groups() ) : bp_the_group(); ?>
                            <div class="groups__group">
                                <div class="groups__group-image-container">

                                </div>
                                <div class="groups__group-details-container">
                                    <h3 class="groups__group-title"><?php bp_group_name(); ?></h3>
                                    <div class="groups__group-details"><?php print groups_get_total_group_count(); ?> Members</div>
                                    <?php bp_group_description(); ?>
                                </div>
                            </div>                                
                            <?php do_action('bp_directory_groups_item'); ?>
                        <?php endwhile; ?>
                        <?php do_action('bp_after_directory_groups_list'); ?>
                    <?php else: ?>
                        <div id="message" class="info">
                            <p><?php _e( 'There were no groups found.', 'buddypress' ); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php do_action('bp_after_groups_loop'); ?>
                </div>
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
