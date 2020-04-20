<?php 
    global $bp;
    $group = $bp->groups->current_group;
    $user = wp_get_current_user();
    $meta = get_user_meta($user->ID);

    // Improved site routing
    if($group) {
        $is_admin = groups_is_user_admin($user->ID, $group->id);
        $edit_group = bp_is_group_admin_page() && $is_admin;
        if($edit_group && ( !isset($meta['agree'][0]) || 'I Agree' !== $meta['agree'][0] ) ) {
            wp_safe_redirect("/people/{$user->user_nicename}/profile/edit/group/1/");
            exit();
        }    
    } else {
        // We are on the user page
        if( bp_is_user() ) {
            if( !empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
                mozilla_update_member();
                wp_safe_redirect("/people/{$user->user_nicename}");
                exit();
            }
        }
    }
    
    get_header(); 
?>
    <div class="content">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
          <?php the_content() ?>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
<?php get_footer(); ?>