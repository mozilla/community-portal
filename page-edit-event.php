<?php 
	$user = wp_get_current_user()->data;
	$meta = get_user_meta($user->ID);

	if($user->ID && (!isset($meta['agree'][0]) || $meta['agree'][0] != 'I Agree')) {
		wp_redirect("/people/{$user->user_nicename}/profile/edit/group/1/");
		die();
	}  

    get_header(); 
    $event_id = $_REQUEST['event_id'];
?>
<div class="events__header events__header--edit">
    <div class="row middle-md event-creator__container">
        <div class="col-md-6 events__header__text">
        <h1 class="title"><?php ($event_id ? _e('Edit Event', 'community-portal') : _e('Create Event', 'community-portal')) ?></h1>
        </div>
    </div>
</div>
<div class="content event-creator__container-main">
<?php if (have_posts()) : ?>
    <?php while(have_posts()) : the_post(); ?>
        <?php the_content(); ?>
    <?php endwhile; ?>
<?php endif; ?>
</div>
<?php get_footer(); ?>