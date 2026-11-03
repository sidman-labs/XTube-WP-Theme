<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) ) {
	$content_width = 1200; /* pixels - Updated for modern displays */
}

add_action( 'after_setup_theme', 'vtt_setup' );
if ( ! function_exists( 'vtt_setup' ) ) {
        /**
         * Sets up theme defaults and registers support for various WordPress features.
         *
         * Note that this function is hooked into the after_setup_theme hook, which
         * runs before the init hook. The init hook is too late for some features, such
         * as indicating support for post thumbnails.
         */
        function vtt_setup() {
                /*
                 * Make theme available for translation.
                 * Translations can be filed in the /languages/ directory.
                 * If you're building a theme based on wpst, use a find and replace
                 * to change 'wpst' to the name of your theme in all the template files
                 */
                $lang = ( current( explode( '_', get_locale() ) ) );
                if ( 'zh' === $lang ) {
                        $lang = 'zh-TW';
                }
                $textdomain = 'wpst';
                $mofile     = get_template_directory() . "/languages/{$textdomain}_{$lang}.mo";
                load_textdomain( $textdomain, $mofile );

                // Add default posts and comments RSS feed links to head.
                add_theme_support( 'automatic-feed-links' );
                /*
                 * Let WordPress manage the document title.
                 * By adding theme support, we declare that this theme does not use a
                 * hard-coded <title> tag in the document head, and expect WordPress to
                 * provide it for us.
                 */
                add_theme_support( 'title-tag' );
                // This theme uses wp_nav_menu() in one location.
                register_nav_menus(
                        array(
                                'vtt-primary-menu' => __( 'Primary Menu', 'wpst' ),
                                'vtt-footer-menu'  => __( 'Footer Menu', 'wpst' ),
                        )
                );
                /*
                 * Switch default core markup for search form, comment form, and comments
                 * to output valid HTML5.
                 */
                add_theme_support(
                        'html5',
                        array(
                                'search-form',
                                'comment-form',
                                'comment-list',
                                'gallery',
                                'caption',
                        )
                );
                /*
                 * Adding Thumbnail basic support
                 */
                add_theme_support( 'post-thumbnails' );
                /*
                 * Adding support for Widget edit icons in customizer
                 */
                add_theme_support( 'customize-selective-refresh-widgets' );
                /*
                 * Enable support for Post Formats.
                 * See http://codex.wordpress.org/Post_Formats
                 */
                add_theme_support(
                        'post-formats',
                        array(
                                'aside',
                                'image',
                                'video',
                                'quote',
                                'link',
                        )
                );
                // Set up the WordPress core custom background feature.
                // add_theme_support( 'custom-background', apply_filters( 'vtt_custom_background_args', array(
                //      'default-color' => 'ffffff',
                //      'default-image' => '',
                // ) ) );
                // Set up the WordPress Theme logo feature.
                // add_theme_support( 'custom-logo' );

                // Add support for responsive embedded content.
                add_theme_support( 'responsive-embeds' );

			// WordPress 6.5+ native lazy-loading images with performance optimization
			add_theme_support( 'native-lazy-loading' );
			
			// Core Web Vitals optimization
			add_theme_support( 'html5', array(
				'style',
				'script',
			) );
			
			// Automatic feed links for SEO
			add_theme_support( 'automatic-feed-links' );
			
			// Post formats for diverse content
			add_theme_support(
				'post-formats',
				array(
					'aside',
					'image',
					'video',
					'quote',
					'link',
					'gallery',
					'audio',
				)
			);
			
			// Custom background support
			add_theme_support( 'custom-background', array(
				'default-color' => '#141414',
				'default-image' => '',
			) );
			
			// Support for custom logo (WordPress native)
			add_theme_support( 'custom-logo', array(
				'height'      => 80,
				'width'       => 300,
				'flex-height' => true,
				'flex-width'  => true,
				'header-text' => array( 'site-title', 'site-description' ),
			) );
			
			// Wide & full alignment support for Gutenberg & Elementor
			add_theme_support( 'align-wide' );
			
			// Block editor stylesheet integration
			add_theme_support( 'wp-block-styles' );
			add_theme_support( 'editor-styles' );
			add_editor_style( array( 'css/custom-editor-style.css', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Hind+Siliguri:wght@300;400;500;600;700&display=swap' ) );
			
			// Gutenberg color palette matching theme colors
			add_theme_support( 'editor-color-palette', array(
				array( 'name' => __( 'Primary',   'wpst' ), 'slug' => 'primary',   'color' => '#e50914' ),
				array( 'name' => __( 'Secondary', 'wpst' ), 'slug' => 'secondary', 'color' => '#ff6b6b' ),
				array( 'name' => __( 'Dark',      'wpst' ), 'slug' => 'dark',      'color' => '#141414' ),
				array( 'name' => __( 'Medium',    'wpst' ), 'slug' => 'medium',    'color' => '#333333' ),
				array( 'name' => __( 'Light',     'wpst' ), 'slug' => 'light',     'color' => '#f5f5f5' ),
				array( 'name' => __( 'White',     'wpst' ), 'slug' => 'white',     'color' => '#ffffff' ),
			) );
			
			// Gutenberg font sizes with responsive design
			add_theme_support( 'editor-font-sizes', array(
				array( 'name' => __( 'Extra Small', 'wpst' ), 'size' => 12, 'slug' => 'extra-small' ),
				array( 'name' => __( 'Small',       'wpst' ), 'size' => 14, 'slug' => 'small'       ),
				array( 'name' => __( 'Normal',      'wpst' ), 'size' => 16, 'slug' => 'normal'      ),
				array( 'name' => __( 'Medium',      'wpst' ), 'size' => 18, 'slug' => 'medium'      ),
				array( 'name' => __( 'Large',       'wpst' ), 'size' => 24, 'slug' => 'large'       ),
				array( 'name' => __( 'Extra Large', 'wpst' ), 'size' => 32, 'slug' => 'extra-large' ),
				array( 'name' => __( 'Huge',        'wpst' ), 'size' => 48, 'slug' => 'huge'        ),
			) );
			
			// WordPress 5.8+ block widgets
			add_theme_support( 'widgets-block-editor' );
			
			// Full site editing support (WordPress 5.9+)
			add_theme_support( 'block-templates' );
			
			// Responsive embedded content
			add_theme_support( 'responsive-embeds' );
			
			// Theme units for consistent spacing
			add_theme_support( 'custom-spacing' );
			
			// Custom line heights
			add_theme_support( 'custom-line-height' );
			
			// Experimental features for future compatibility
			add_theme_support( 'experimental-link-color' );
			add_theme_support( 'experimental-custom-spacing' );
			
			// SEO and Schema markup support
			add_theme_support( 'structured-data' );

                // Check and setup theme default settings.
                vtt_setup_theme_default_settings();
        }
}

add_filter( 'excerpt_more', 'vtt_custom_excerpt_more' );
if ( ! function_exists( 'vtt_custom_excerpt_more' ) ) {
        /**
         * Removes the ... from the excerpt read more link
         *
         * @param string $more The excerpt.
         *
         * @return string
         */
        function vtt_custom_excerpt_more( $more ) {
                if ( ! is_admin() ) {
                        $more = '';
                }
                return $more;
        }
}

add_filter( 'wp_trim_excerpt', 'vtt_all_excerpts_get_more_link' );
if ( ! function_exists( 'vtt_all_excerpts_get_more_link' ) ) {
        /**
         * Adds a custom read more link to all excerpts, manually or automatically generated
         *
         * @param string $post_excerpt Posts's excerpt.
         *
         * @return string
         */
        function vtt_all_excerpts_get_more_link( $post_excerpt ) {
                if ( ! is_admin() ) {
                        $post_excerpt = $post_excerpt . ' [...]<p><a class="btn btn-primary vtt-read-more-link" href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . __(
                                'Read More...',
                                'wpst'
                        ) . '</a></p>';
                }
                return $post_excerpt;
        }
}
