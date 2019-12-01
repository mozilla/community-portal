<?php get_header(); ?>
    <div class="content">
        <div class="page">
            <div class="page__container">
                <h1 class="page__title"><?php print $post->post_title; ?></h1>
                <section class="page__content">
                    <?php print $post->post_content; ?>
                </section>
            </div>
        </div>
    </div>
<?php get_footer(); ?>