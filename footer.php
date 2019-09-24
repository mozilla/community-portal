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
                    <?php 
                        wp_nav_menu(array(
                                        'menu'           =>  'Resources',
                                        'menu_class'        =>  'footer-resources-menu')
                        ); 
                    ?>
                </div>
                <div class="footer__menu-bottom-container">
                    <p class="footer__copy"><?php print __("Portions of this content are copyright 1998-2019 by individual mozilla.org contributors. Content available under a Creative Commons license."); ?></p>
                    <?php 
                        wp_nav_menu(array(
                                        'menu'          =>  'Footer Secondary',
                                        'menu_class'    =>  'footer-secondary-menu'
                        ));
                    ?>
                    <div class="footer__menu-svg-container">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 9.50003C19.0034 10.8199 18.6951 12.1219 18.1 13.3C17.3944 14.7118 16.3098 15.8992 14.9674 16.7293C13.6251 17.5594 12.0782 17.9994 10.5 18C9.18013 18.0035 7.87812 17.6951 6.7 17.1L1 19L2.9 13.3C2.30493 12.1219 1.99656 10.8199 2 9.50003C2.00061 7.92179 2.44061 6.37488 3.27072 5.03258C4.10083 3.69028 5.28825 2.6056 6.7 1.90003C7.87812 1.30496 9.18013 0.996587 10.5 1.00003H11C13.0843 1.11502 15.053 1.99479 16.5291 3.47089C18.0052 4.94699 18.885 6.91568 19 9.00003V9.50003Z" stroke="#EDEDF0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0785 0.331055C5.53739 0.331055 0.232666 5.63506 0.232666 12.1783C0.232666 17.4125 3.62685 21.8525 8.33448 23.4191C8.92721 23.5281 9.14321 23.1623 9.14321 22.8481C9.14321 22.5667 9.13303 21.822 9.12721 20.8336C5.83194 21.5492 5.13666 19.2452 5.13666 19.2452C4.59775 17.8765 3.82102 17.5122 3.82102 17.5122C2.74539 16.7776 3.90248 16.7921 3.90248 16.7921C5.09157 16.8758 5.71702 18.0132 5.71702 18.0132C6.77375 19.8234 8.49012 19.3005 9.16503 18.9972C9.27267 18.2322 9.57884 17.71 9.91703 17.414C7.28648 17.1151 4.52066 16.0983 4.52066 11.5587C4.52066 10.2656 4.98248 9.20743 5.7403 8.37979C5.61812 8.08016 5.21157 6.87506 5.85666 5.24452C5.85666 5.24452 6.85084 4.92598 9.11411 6.45907C10.0588 6.1958 11.0727 6.06488 12.0799 6.05979C13.0865 6.06488 14.0996 6.1958 15.0458 6.45907C17.3076 4.92598 18.3003 5.24452 18.3003 5.24452C18.9468 6.87506 18.5403 8.08016 18.4188 8.37979C19.1781 9.20743 19.6363 10.2656 19.6363 11.5587C19.6363 16.11 16.8661 17.1114 14.2276 17.4045C14.6523 17.7703 15.0312 18.4932 15.0312 19.598C15.0312 21.182 15.0167 22.4598 15.0167 22.8481C15.0167 23.1652 15.2305 23.534 15.8312 23.4183C20.5352 21.8482 23.9265 17.4111 23.9265 12.1783C23.9265 5.63506 18.6218 0.331055 12.0785 0.331055Z" fill="#FFFFFE"/>
                        </svg>
                    </div>
                </div>
            </div>
        </footer> 
    </body>
</html>