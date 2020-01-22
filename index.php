<?php 
    global $bp;
    $group = $bp->groups->current_group;
    $user = wp_get_current_user();
    $meta = get_user_meta($user->ID);

    if($group) {
        $is_admin = groups_is_user_admin($user->ID, $group->id);
        $edit_group = bp_is_group_admin_page() && $is_admin;

        if($edit_group && (!isset($meta['agree'][0]) || $meta['agree'][0] != 'I Agree')) {
            wp_redirect("/people/{$user->user_nicename}/profile/edit/group/1/");
            die();
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