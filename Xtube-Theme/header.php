<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<?php require get_template_directory() . '/inc/init.php'; ?>
<html <?php language_attributes(); ?>>
<head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="profile" href="https://gmpg.org/xfn/11">
        <?php if ( get_theme_mod( 'favicon_file' ) != '' ) : ?>
                <link rel="icon" href="<?php echo esc_url( get_theme_mod( 'favicon_file' ) ); ?>">
        <?php endif; ?>
        <!-- Open Graph & Twitter Card meta (single posts) -->
        <?php if ( is_single() ) {
                require get_template_directory() . '/inc/meta-social.php';
        } ?>
        <!-- Google Analytics -->
        <?php if ( get_theme_mod( 'google_analytics_code' ) != '' ) { echo get_theme_mod( 'google_analytics_code' ); } ?>
        <!-- Meta Verification -->
        <?php if ( get_theme_mod( 'meta_verification_code' ) != '' ) { echo get_theme_mod( 'meta_verification_code' ); } ?>
        <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php do_action( 'wp_body_open' ); ?>
<div class="site" id="page">

        <!-- ******************* The Navbar Area ******************* -->
        <div id="wrapper-navbar" itemscope itemtype="http://schema.org/WebSite">
                <a class="skip-link sr-only sr-only-focusable" href="#content"><?php esc_html_e( 'Skip to content', 'wpst' ); ?></a>
                <div class="header-nav">
                        <div class="container d-md-flex align-items-center justify-content-between">
                                <div class="logo-search d-flex align-items-center">
                                        <!-- Menu mobile -->
                                        <?php if( is_single() || is_tag() || is_search() || is_page_template( 'template-tags.php' )){ $isActive = ''; } else { $isActive = 'is-active'; }
                                        if( wp_is_mobile() ) { $isActive = ''; } ?>
                                        <button class="navbar-toggler hamburger hamburger--slider <?php echo $isActive; ?> d-block" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'wpst' ); ?>">
                                                <span class="hamburger-box">
                                                        <span class="hamburger-inner"></span>
                                                </span>
                                        </button>
                                        <!-- Menu desktop -->
                                        <?php /* <button class="navbar-toggler hamburger hamburger--slider d-none d-md-block" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'wpst' ); ?>">
                                                <span class="hamburger-box">
                                                        <span class="hamburger-inner"></span>
                                                </span>
                                        </button> */ ?>
                                        <div class="logo">
                                                <!-- Your site title as branding in the menu -->
                                                <?php if ( ! get_theme_mod( 'logo_file', '' ) ) { ?>
                                                        <?php if ( is_front_page() && is_home() ) : ?>
                                                                <?php /* <h1 class="navbar-brand mb-0"><a rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a></h1> */ ?>
                                                                <h1>
                                                                        <a class="logo-text" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url">
                                                                                <?php if( get_theme_mod( 'logo_word_1', 'Your' ) != '' || get_theme_mod( 'logo_word_2', 'logo' ) != '' ) : ?>
                                                                                        <span class="logo-word-1"><?php echo get_theme_mod( 'logo_word_1', 'Your' ); ?></span>
                                                                                        <span class="logo-word-2"><?php echo get_theme_mod( 'logo_word_2', 'logo' ); ?></span>
                                                                                <?php else : ?>
                                                                                        <?php bloginfo( 'name' ); ?>
                                                                                <?php endif; ?>
                                                                        </a>
                                                                </h1>
                                                        <?php else : ?>
                                                                <a class="logo-text" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url">
                                                                        <?php if( get_theme_mod( 'logo_word_1', 'Your' ) != '' || get_theme_mod( 'logo_word_2', 'logo' ) != '' ) : ?>
                                                                                <span class="logo-word-1"><?php echo get_theme_mod( 'logo_word_1', 'Your' ); ?></span><span class="logo-word-2"><?php echo get_theme_mod( 'logo_word_2', 'logo' ); ?></span>
                                                                        <?php else : ?>
                                                                                <?php bloginfo( 'name' ); ?>
                                                                        <?php endif; ?>
                                                                </a>
                                                        <?php endif; ?>
                                                <?php } else { ?>
                                                        <?php // the_custom_logo(); ?>
                                                        <a class="logo-img" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><img src="<?php echo get_theme_mod( 'logo_file', '' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"></a> 
                                                <?php } ?><!-- end custom logo -->
                                        </div>                                  
                                        <?php if(!wp_is_mobile()) : ?>
                                                <div class="d-none d-md-block header-search">
                                                        <?php get_template_part( 'template-parts/content', 'header-search' ); ?>
                                                </div>
                                        <?php endif; ?>
                                        <?php // if(wp_is_mobile()) : ?>
                                                <div class="d-block d-md-none membership">                                                          
                                                        <?php if( is_user_logged_in() ) : ?>
                                                        <div class="welcome menu-item-has-children"><?php echo get_avatar( get_current_user_id() ); ?> <?php if(!wp_is_mobile()) : ?><i class="d-none d-md-inline-block fa fa-caret-down"></i><?php endif; ?>
                                                                        <ul class="nav-menu sub-menu">
                                                                                <li class="welcome"><?php esc_html_e('Welcome', 'wpst'); ?> <?php echo wp_get_current_user()->display_name; ?></li>
                                                                                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>submit-a-video"><i class="fa fa-upload"></i> <span class="topbar-item-text"><?php esc_html_e('Submit a Video', 'wpst'); ?></span></a></li>                                        
                                                                                <li id="my-channel"><a href="<?php echo get_author_posts_url(get_current_user_id()); ?>"><i class="fa fa-video-camera"></i> <span class="topbar-item-text"><?php esc_html_e('My Channel', 'wpst'); ?></span></a></li>
                                                                                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>my-profile"><i class="fa fa-user"></i> <span class="topbar-item-text"><?php esc_html_e('My Profile', 'wpst'); ?></span></a></li>
                                                                                <li><a href="<?php echo esc_url(wp_logout_url(is_home()? home_url() : get_permalink()) ); ?>"><i class="fa fa-power-off"></i> <span class="topbar-item-text"><?php esc_html_e('Logout', 'wpst'); ?></span></a></li>
                                                                        </ul>
                                                                </div>
                                                        <?php else : ?>        
                                                                <div class="d-none d-md-inline-block">   
                                                                        <span class="login"><a class="btn btn-primary" href="#wpst-register"><?php esc_html_e('Sign Up', 'wpst'); ?></a></span>             
                                                                        <span class="login"><a class="login-link" href="#wpst-login"><?php esc_html_e('Login', 'wpst'); ?></a></span>
                                                                </div>
                                                                <div class="d-inline-block d-md-none user-mobile">
                                                                        <a href="#wpst-login"><i class="fa fa-user"></i></a>
                                                                </div>
                                                        <?php endif; ?>
                                                </div>
                                        <?php // endif; ?>
                                </div>          
                                
                                <?php if(is_home() /* && !wp_is_mobile() */ ) : ?><h2 class="d-none d-xl-block header-title"><?php echo get_theme_mod('seo_home_title', get_bloginfo('description')); ?> </h2><?php endif; ?>
                                
                                <?php // if(!wp_is_mobile()) : ?>
                                        <div class="d-none d-md-block membership">                                                          
                                                <?php if( is_user_logged_in() ) : ?>
                                                        <div class="welcome menu-item-has-children"><?php echo get_avatar( get_current_user_id() ); ?> <i class="fa fa-caret-down"></i>
                                                                <ul class="nav-menu sub-menu">
                                                                        <li class="welcome"><?php esc_html_e('Welcome', 'wpst'); ?> <?php echo wp_get_current_user()->display_name; ?></li>
                                                                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>submit-a-video"><i class="fa fa-upload"></i> <span class="topbar-item-text"><?php esc_html_e('Submit a Video', 'wpst'); ?></span></a></li>                                        
                                                                        <li id="my-channel"><a href="<?php echo get_author_posts_url(get_current_user_id()); ?>"><i class="fa fa-video-camera"></i> <span class="topbar-item-text"><?php esc_html_e('My Channel', 'wpst'); ?></span></a></li>
                                                                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>my-profile"><i class="fa fa-user"></i> <span class="topbar-item-text"><?php esc_html_e('My Profile', 'wpst'); ?></span></a></li>
                                                                        <li><a href="<?php echo esc_url(wp_logout_url(is_home()? home_url() : get_permalink()) ); ?>"><i class="fa fa-power-off"></i> <span class="topbar-item-text"><?php esc_html_e('Logout', 'wpst'); ?></span></a></li>
                                                                </ul>
                                                        </div>
                                                <?php else : ?>           
                                                        <span class="login"><a class="btn btn-primary" href="#wpst-register"><?php esc_html_e('Sign Up', 'wpst'); ?></a></span>             
                                                        <span class="login"><a class="login-link" href="#wpst-login"><?php esc_html_e('Login', 'wpst'); ?></a></span>
                                                <?php endif; ?>
                                        </div>
                                <?php // endif; ?>              
                        </div>  
                </div>

                <?php if(is_home() /* && wp_is_mobile() */ ) : ?>
                        <h2 class="d-block d-md-none header-title"><?php echo get_theme_mod('seo_home_title', get_bloginfo('description')); ?></h2>
                <?php endif; ?>

                <?php // if(wp_is_mobile()) : ?>
                        <div class="d-block d-md-none header-search">
                                <?php get_template_part( 'template-parts/content', 'header-search' ); ?>
                        </div>
                <?php // endif; ?>

                <nav class="navbar navbar-expand-md navbar-dark">       
                        <div class="container">
                                <!-- The WordPress Menu goes here -->           
                                <?php if( is_single() || is_tag() || is_search() || is_page_template( 'template-tags.php' ) ){ $containerClass = 'collapse navbar-collapse'; } else { $containerClass = 'collapse navbar-collapse show'; }
                                if( wp_is_mobile() ) { $containerClass = 'collapse navbar-collapse'; }
                                wp_nav_menu(
                                        array(
                                                'theme_location'  => 'vtt-primary-menu',
                                                'container_class' => $containerClass,
                                                'container_id'    => 'navbarNavDropdown',
                                                'menu_class'      => 'navbar-nav ml-auto',
                                                'fallback_cb'     => '',
                                                'depth'           => 2,
                                                'walker'          => new Understrap_WP_Bootstrap_Navwalker(),
                                        )
                                ); ?>
                                <?php /* $tags = get_tags(
                                        array(
                                                'orderby' => 'count',
                                                'order' => 'DESC',
                                                'number' => 50
                                                )
                                        );
                                $menu_tags = '<div class="post_tags">';
                                foreach ( $tags as $tag ) {
                                        $tag_link = get_tag_link( $tag->term_id );
                                                        
                                        $menu_tags .= '<a class="btn btn-grey" href="' . $tag_link . '">' . ucfirst($tag->name) . '</a>';
                                }
                                $menu_tags .= '</div>';
                                echo $menu_tags; */ ?>
                        </div><!-- .container -->
                </nav><!-- .site-navigation -->
                
                <div class="clear"></div>
        </div><!-- #wrapper-navbar end -->
