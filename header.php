<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
        <link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
        <?php endif; ?>
        <?php wp_head(); ?>
        <title><?php print get_bloginfo('name'); ?> - <?php print get_bloginfo('description'); ?></title>
    </head>
    <body class="body">
        <nav class="nav">
            <div class="nav__header">
                <div class="nav__container">
                    <img src="<?php print get_stylesheet_directory_uri(); ?>/images/logo.png"  class="nav__logo" alt="Mozilla Logo" />
                    <div class="nav__search-container">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="nav__search-icon">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9 5C9 7.20914 7.20914 9 5 9C2.79086 9 1 7.20914 1 5C1 2.79086 2.79086 1 5 1C7.20914 1 9 2.79086 9 5ZM8.00021 9.00021C7.16451 9.62799 6.1257 10 5 10C2.23858 10 0 7.76142 0 5C0 2.23858 2.23858 0 5 0C7.76142 0 10 2.23858 10 5C10 6.27532 9.52253 7.43912 8.73661 8.32239L11.7071 11.2929L11 12L8.00021 9.00021Z" fill="#D2D2D2"/>
                        </svg>
                        <input type="text" class="nav__search" placeholder="Seach"/>
                    </div>
                    <div class="nav__login">
                        Log In / Sign Up
                    </div>
                </div>
            </div>
            <div class="nav__menu">
                <div class="nav__container">
                    <?php 
                        wp_nav_menu(array(
                                        'theme_location'    => 'mozilla-theme-menu', 
                                        'menu_id'           =>  'Mozilla Main Menu',
                                        'menu_class'        =>  'menu')
                        ); 
                    ?>
                </div>
            </div>
        </nav>