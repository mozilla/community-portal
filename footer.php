        <footer class="footer">
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
                      
                    <?php 

                        wp_nav_menu(array(
                                        'menu'           =>  'Mozilla',
                                        'menu_class'        =>  'footer-mozilla-menu')
                        ); 
                    ?>
                </div>
                <div class="footer__menu-bottom-container">
                    <p class="footer__copy"><?php print __("Portions of this content are copyright 1998-2019 by individual mozilla.org contributors. Content available under a Creative Commons license."); ?></p>
                    <div class="footer__menu-svg-container">
                        <div class="footer__social-container">
                            
                        </div>
                    </div>
                </div>
            </div>
        </footer> 
    </body>
</html>