<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'wpst_scripts' );
add_action( 'wp_enqueue_scripts', 'wpst_admin_scripts' );

if ( ! function_exists( 'wpst_scripts' ) ) {
        /**
         * Load theme's JavaScript and CSS sources.
         */
        function wpst_scripts() {
                // Get the theme data.
                $the_theme     = wp_get_theme();
                $theme_version = $the_theme->get( 'Version' );

                $css_version = $theme_version . '.' . filemtime( get_template_directory() . '/css/theme.min.css' );
                wp_enqueue_style( 'vtt-styles', get_template_directory_uri() . '/css/theme.min.css', array(), $css_version );

                // Fonts: Poppins (English) + Hind Siliguri (Bangla) — one combined request
                wp_enqueue_style(
                        'xtube-fonts',
                        'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&family=Hind+Siliguri:wght@300;400;500;600;700&display=swap',
                        array(),
                        null
                );

                if( is_single() && !is_plugin_active( 'clean-tube-player/clean-tube-player.php' ) ){
                        wp_enqueue_style( 'vtt-videojs-style', get_template_directory_uri() . '/vendor/videojs/video-js.css', array(), '7.4.1', 'all' );
                }
                wp_enqueue_style( 'vtt-custom-style', get_template_directory_uri() . '/css/custom.css', array(), $css_version );

                wp_enqueue_script( 'jquery' );

                if( is_single() && (!is_plugin_active( 'clean-tube-player/clean-tube-player.php' ) || !is_plugin_active( 'kenplayer-transformer/transform.php') ) ){
                        wp_enqueue_script( 'vtt-videojs', get_template_directory_uri() . '/vendor/videojs/video.min.js', array(), '7.4.1', true );
                        wp_enqueue_script( 'vtt-videojs-quality-selector', get_template_directory_uri() . '/vendor/videojs/videojs-quality-selector.min.js', array('vtt-videojs'), '1.1.2', true);
                }
                $js_version = $theme_version . '.' . filemtime( get_template_directory() . '/js/theme.min.js' );
                wp_enqueue_script( 'vtt-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $js_version, true );
                wp_enqueue_script( 'vtt-readmore', get_template_directory_uri() . '/js/readmore.min.js', array(), '2.2.1', true );
                wp_enqueue_script( 'vtt-main', get_template_directory_uri() . '/js/main.js', array(), '2.0.0', true );
                wp_localize_script(
                        'vtt-main',
                        'vtt_ajax_var',
                        array(
                                'url'            => str_replace( array( 'http:', 'https:' ), '', admin_url( 'admin-ajax.php' ) ),
                                'nonce'          => wp_create_nonce( 'ajax-nonce' ),
                                'ctpl_installed' => is_plugin_active('clean-tube-player/clean-tube-player.php')
                        )
                );

                if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                        wp_enqueue_script( 'comment-reply' );
                }
        }
}

if ( ! function_exists( 'wpst_admin_scripts' ) ) {
        /**
         * Load theme's JavaScript and CSS sources.
         */
        function wpst_admin_scripts() {
                $the_theme     = wp_get_theme();
                $theme_version = $the_theme->get( 'Version' );
                $js_version    = $theme_version . '.' . filemtime( get_template_directory() . '/js/theme.min.js' );
                $css_version   = $theme_version . '.' . filemtime( get_template_directory() . '/css/theme.min.css' );
                wp_enqueue_style( 'vtt-customizer-style', get_template_directory_uri() . '/admin/assets/css/customizer-css.css', array(), $css_version );
                wp_enqueue_script( 'vtt-admin', get_template_directory_uri() . '/admin/assets/js/admin.js', array('jquery'), $js_version, true );
                wp_localize_script( 'vtt-admin', 'admin_ajax_var', array(
                        'url'   => admin_url( 'admin-ajax.php' ),
                        'nonce' => wp_create_nonce( 'ajax-nonce' )
                ));
        }
}
