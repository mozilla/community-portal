        <footer class="footer">
            <?php
                if(!isset($_COOKIE['gdpr'])) {
                    $args = Array(
                        'category_name'  =>  'GDPR',
                    );
    
                    $gdpr_post = get_posts($args);
                } else {
                    $gdpr_post = Array();
                }
            ?>

            <?php if(sizeof($gdpr_post) > 0): ?>
            <div class="gdpr">
                <div class="gdpr__container">
                    <a href="#" class="gdpr__close">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 1L1 17" stroke="#F9F9FA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M1 1L17 17" stroke="#F9F9FA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>

                    <h2 class="gdpr__title"><?php print $gdpr_post[0]->post_title; ?></h2>
                    <?php
                        print  $gdpr_post[0]->post_content;
                    ?>
                    <div class="gdpr__cta-container">
                        <a href="#" class="gdpr__cta"><?php print __("Accept"); ?></a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="footer__container">
                <div class="footer__logo-container">
                    <img src="<?php print get_stylesheet_directory_uri(); ?>/images/footer-logo.png"  class="footer__logo" alt="Mozilla Logo" />
                </div>
                <div class="footer__menu-container">
                    <?php 
                        wp_nav_menu(array(
                                        'menu'           =>  'Footer Primary',
                                        'menu_class'        =>  'footer-menu footer-menu--primary')
                        ); 
                    ?>
                    
                    <?php 
                        wp_nav_menu(array(                                 
                                        'menu'           =>  'Mozilla Main Menu',
                                        'menu_class'        =>  'footer-nav-menu')
                        ); 
                    ?>

                    <div class="footer__mozilla-container">
                        <span class="footer__menu-title">Mozilla</span>
                    <?php 
                        
                        wp_nav_menu(array(
                                        'menu'           =>  'Mozilla',
                                        'menu_class'        =>  'footer-mozilla-menu')
                        ); 
                    ?>
                    </div>
                </div>
                <div class="footer__menu-bottom-container">
                    <p class="footer__copy">
                        <?php print __("Portions of this content are copyright 1998-2020 by individual mozilla.org contributors. Content available under a "); ?>
                        <a href="#" class="footer__link"><?php print __(' Creative Commons license.'); ?></a>
                    </p>
                    <div class="footer__menu-legal-container">
                        <?php 
                            wp_nav_menu(array(
                                'menu'           =>  'Legal',
                                'menu_class'     =>  'footer-legal-menu')
                            ); 


                            $options = wp_load_alloptions();
                        ?>
                        <div class="footer__menu-svg-container">
                            <a href="<?php print (isset($options['community_discourse']) && strlen($options['community_discourse']) > 0) ? $options['community_discourse']: '#'; ?>">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 7.66669C14.0023 8.5466 13.7967 9.41461 13.4 10.2C12.9296 11.1412 12.2065 11.9328 11.3116 12.4862C10.4168 13.0396 9.3855 13.3329 8.33333 13.3334C7.45342 13.3356 6.58541 13.1301 5.8 12.7334L2 14L3.26667 10.2C2.86995 9.41461 2.66437 8.5466 2.66667 7.66669C2.66707 6.61452 2.96041 5.58325 3.51381 4.68839C4.06722 3.79352 4.85884 3.0704 5.8 2.60002C6.58541 2.20331 7.45342 1.99772 8.33333 2.00002H8.66667C10.0562 2.07668 11.3687 2.66319 12.3528 3.64726C13.3368 4.63132 13.9233 5.94379 14 7.33335V7.66669Z" stroke="#EDEDF0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                            <a href="<?php print (isset($options['github_link']) && strlen($options['github_link']) > 0) ? $options['github_link']: '#'; ?>">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.00001 13.3334C2.66668 14.3334 2.66668 11.6667 1.33334 11.3334M10.6667 15.3334V12.7534C10.6917 12.4355 10.6487 12.1159 10.5407 11.8159C10.4326 11.5159 10.262 11.2423 10.04 11.0134C12.1333 10.78 14.3333 9.98669 14.3333 6.34669C14.3332 5.4159 13.9751 4.52082 13.3333 3.84669C13.6372 3.03236 13.6158 2.13225 13.2733 1.33335C13.2733 1.33335 12.4867 1.10002 10.6667 2.32002C9.13868 1.9059 7.528 1.9059 6.00001 2.32002C4.18001 1.10002 3.39334 1.33335 3.39334 1.33335C3.05093 2.13225 3.02944 3.03236 3.33334 3.84669C2.68676 4.52582 2.32836 5.42899 2.33334 6.36669C2.33334 9.98002 4.53334 10.7734 6.62668 11.0334C6.40734 11.26 6.23819 11.5303 6.13022 11.8266C6.02225 12.123 5.97788 12.4387 6.00001 12.7534V15.3334" stroke="#EDEDF0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer> 
    </body>
</html>
