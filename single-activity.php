<?php
    get_header();
    global $post;

    $featured_image = get_the_post_thumbnail_url();

    // Get the advanced custom fields for the activity
    $youtube_video = get_field('video');
    $activity_flow_title = get_field('activity_flow_title');
    $activity_flow_copy = get_field('activity_flow_copy');
    $activity_flow = get_field('flows');
    $conversation_title = get_field('conversation_title');
    $conversation_copy = get_field('conversation_copy');
    $time_commitment = get_field('time_commitment');

    // Tags for activity
    $tags = get_the_terms($post, 'post_tag');


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
                                <div class="activity__cta-container">
                                    <a href="#" class="activity__cta"><?php print __("{$post->post_title}"); ?></a>
                                    <a href="#" class="activity__cta activity__cta--share">
                                        <svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 9V15C1 15.3978 1.15804 15.7794 1.43934 16.0607C1.72064 16.342 2.10218 16.5 2.5 16.5H11.5C11.8978 16.5 12.2794 16.342 12.5607 16.0607C12.842 15.7794 13 15.3978 13 15V9M10 4.5L7 1.5M7 1.5L4 4.5M7 1.5V11.25" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <?php print __('Share Activity'); ?>
                                    </a>
                                </div>
                                <div class="activity__description-container">
                                    <?php
                                        print $post->post_content;
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php if(strlen($youtube_video) > 0): ?>
                        <?php 
                            preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $youtube_video, $matches);
                            $youtube_id = (is_array($matches) && sizeof($matches) > 1) ? $matches[1] : false;
                        ?>
                        <div class="activity__card activity__card--video">
                            <?php if($youtube_id): ?>
                            <div class="activity__card-content">
                                <div class="activity__video-container">
                                    <iframe class="activity__video" src="https://www.youtube.com/embed/<?php print $youtube_id; ?>"></iframe>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if(strlen($activity_flow_title) > 0): ?>
                        <h2 class="activity__card-title"><?php print $activity_flow_title; ?></h2>
                        <?php endif; ?>
                        <div class="activity__card activity__card--flow">        
                            <div class="activity__card-content">
                                <?php print $activity_flow_copy; ?>
                                <?php if(is_array($activity_flow) && sizeof($activity_flow) > 0 && strlen($activity_flow[0]['title']) > 0): ?>
                                <div class="activity__accordion">
                                    <?php $accordion_counter = 0; ?>
                                    <?php foreach($activity_flow AS $flow): ?>
                                        <?php if(strlen($flow['title']) > 0 && strlen($flow['copy']) > 0): ?>
                                        <div class="activity__accordion-container">
                                            <input class="activity__accordion-input" id="ac-<?php print $accordion_counter; ?>" name="accordion-<?php print $accordion_counter; ?>" type="checkbox"<?php if($accordion_counter === 0): ?>checked<?php endif; ?> />
                                            <label class="activity__accordion-label" for="ac-<?php print $accordion_counter; ?>"><?php print $flow['title']; ?></label>
                                            <div class="activity__accordion-content">
                                                <?php print $activity_flow[0]['copy']; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    <?php $accordion_counter++; ?>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if(strlen($conversation_title) > 0): ?>
                        <h2 class="activity__card-title"><?php print $conversation_title; ?></h2>               
                        <?php endif; ?>
                        <?php if(strlen($conversation_title) > 0): ?>
                        <div class="activity__card activity__card--conversation">        
                            <div class="activity__card-content">
                                <?php print $conversation_copy; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                    <div class="activity__right-column">
                        <div class="activity__card">
                            <div class="activity__card-content">
                                <?php if(sizeof($tags) > 0): ?>
                                <span><?php print __("Tags"); ?></span>
                                <div class="activity__tags">
                                <?php foreach($tags AS $tag): ?>
                                    <span class="activity__tag"><?php print $tag->name; ?></span>
                                <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                <?php if($time_commitment): ?>
                                <span><?php print __("Time Commitment"); ?></span>
                                    
                                <?php endif; ?>
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