<?php
    get_header();
    global $post;

    $featured_image = get_the_post_thumbnail_url();

?>
    <div class="content">
        <section class="activity">
            <div class="activity__container">
                <h1 class="activity__title"><?php print $post->post_title; ?></h1>
                <div class="activity__info">
                    <div class="activity__left-column">
                        <div class="activity__card">
                            <?php if($featured_image): ?>
                            <div class="activity__card-image" style="background-image: url('<?php print $featured_image; ?>');">
                            </div>
                            <?php endif; ?>
                            <div class="activity__card-content">
                           
                                Stuff Left
                            </div>
                        </div>
                    </div>
                    <div class="activity__right-column">
                        <div class="activity__card">
                            <div class="activity__card-content">
                                Stuff Right
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


<?php 
    get_footer();
?>