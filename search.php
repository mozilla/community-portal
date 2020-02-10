<?php 
    get_header();
    $results = Array();
    // Lets get some search results
    if(isset($_GET['s']) && strlen($_GET['s']) > 0) {





    }


?>
    <div class="content">
        <div class="search">
            <div class="search__container">
                <h1 class="search__title"><?php print __(sprintf('Results for %s', $_GET['s']), 'community-portal'); ?></h1>
            </div>
        </div>
    </div>
<?php get_footer(); ?>