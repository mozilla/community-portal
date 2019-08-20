        <footer class="footer">
            <div class="footer__container">
                <div class="footer__logo-container">
                    <img src="<?php print get_stylesheet_directory_uri(); ?>/images/footer-logo.png"  class="footer__logo" alt="Mozilla Logo" />
                    <div class="footer__tag-line"><?php print get_bloginfo('description'); ?></div>
                </div>
                <div class="footer__menu-container">
                    <?php 
                        wp_nav_menu(array(
                                        'menu'           =>  'Footer Primary',
                                        'menu_class'        =>  'footer-menu')
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
                    <?php 
                        wp_nav_menu(array(
                                        'menu'           =>  'Resources',
                                        'menu_class'        =>  'footer-resources-menu')
                        ); 
                    ?>
                </div>
            </div>
        </footer> 
    </body>
</html>