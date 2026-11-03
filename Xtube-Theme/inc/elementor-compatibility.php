<?php
/**
 * XTube Elementor Page Builder Compatibility v3.0
 * Enhanced with custom widgets, dynamic tags, and advanced theme builder support.
 *
 * @package XTube
 * @author  Saieed Rahman
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ============================================================
 * ADVANCED ELEMENTOR COMPATIBILITY CLASS
 * ============================================================
 */
class XTube_Elementor_Compatibility {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'elementor/init', array( $this, 'init_elementor_support' ) );
        add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles' ) );
        add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor_styles' ) );
        
        // Performance optimizations
        add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
        add_filter( 'elementor/frontend/print_fa4_fa5_icons', '__return_false' );
        add_action( 'elementor/css/print_post/reset', array( $this, 'reset_elementor_css' ) );
        
        // Custom widgets and dynamic tags
        add_action( 'elementor/dynamic_tags/register', array( $this, 'register_dynamic_tags' ) );
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
        
        // Theme builder enhancements
        add_action( 'elementor/theme/register_locations', array( $this, 'register_theme_locations' ) );
        add_filter( 'elementor/theme/get_location_templates/template_id', array( $this, 'get_location_template_id' ), 10, 2 );
        
        // Breakpoints and responsive design
        add_action( 'elementor/kit/register_tabs', array( $this, 'register_kit_tabs' ), 1, 40 );
        add_filter( 'elementor/editor/localize_settings', array( $this, 'add_editor_settings' ) );
    }
    
    /**
     * Initialize Elementor support
     */
    public function init_elementor_support() {
        // Theme support
        add_theme_support( 'elementor' );
        add_theme_support( 'elementor-pro' );
        add_theme_support( 'elementor-default-colors' );
        add_theme_support( 'elementor-default-typography' );
        add_theme_support( 'elementor-font-awesome' );
        add_theme_support( 'elementor-custom-breakpoints' );
        
        // Register custom fonts
        $this->register_custom_fonts();
        
        // Header/Footer support
        $this->setup_header_footer_support();
    }
    
    /**
     * Register custom fonts with Elementor
     */
    private function register_custom_fonts() {
        add_filter( 'elementor/fonts/groups', array( $this, 'add_font_group' ) );
        add_filter( 'elementor/fonts/additional_fonts', array( $this, 'add_additional_fonts' ) );
    }
    
    public function add_font_group( $font_groups ) {
        $new_group['xtube_fonts'] = __( 'XTube Premium Fonts', 'wpst' );
        return array_merge( $new_group, $font_groups );
    }
    
    public function add_additional_fonts( $additional_fonts ) {
        $additional_fonts['Inter'] = 'xtube_fonts';
        $additional_fonts['Poppins'] = 'xtube_fonts';
        $additional_fonts['Hind Siliguri'] = 'xtube_fonts';
        return $additional_fonts;
    }
    
    /**
     * Setup header/footer support
     */
    private function setup_header_footer_support() {
        add_action( 'wp', array( $this, 'handle_header_footer' ) );
    }
    
    public function handle_header_footer() {
        if ( ! class_exists( '\Elementor\Plugin' ) ) {
            return;
        }
        
        // Check for custom header
        if ( \Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' )->is_cpt_custom_template_supported() ) {
            add_action( 'get_header', array( $this, 'override_header' ) );
            add_action( 'get_footer', array( $this, 'override_footer' ) );
        }
    }
    
    public function override_header() {
        if ( \Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' )->get_template_id( 'header' ) ) {
            require_once get_template_directory() . '/template-parts/header-elementor.php';
            $templates = array();
            $templates[] = 'header-elementor.php';
            locate_template( $templates, true );
        }
    }
    
    public function override_footer() {
        if ( \Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' )->get_template_id( 'footer' ) ) {
            require_once get_template_directory() . '/template-parts/footer-elementor.php';
            $templates = array();
            $templates[] = 'footer-elementor.php';
            locate_template( $templates, true );
        }
    }
    
    /**
     * Register theme locations
     */
    public function register_theme_locations( $elementor_theme_manager ) {
        $elementor_theme_manager->register_location( 'header' );
        $elementor_theme_manager->register_location( 'footer' );
        $elementor_theme_manager->register_location( 'single' );
        $elementor_theme_manager->register_location( 'archive' );
        $elementor_theme_manager->register_location( '404' );
    }
    
    /**
     * Get location template ID
     */
    public function get_location_template_id( $template_id, $location ) {
        // Custom logic for template selection
        return $template_id;
    }
    
    /**
     * Enqueue preview styles
     */
    public function enqueue_preview_styles() {
        // Premium fonts
        wp_enqueue_style(
            'xtube-premium-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap',
            array(),
            null
        );
        
        wp_enqueue_style(
            'xtube-bangla-font',
            'https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap',
            array(),
            null
        );
        
        // Theme styles
        wp_enqueue_style(
            'xtube-elementor-preview',
            get_template_directory_uri() . '/css/elementor-preview.css',
            array(),
            wp_get_theme()->get( 'Version' )
        );
        
        // Dark mode support
        if ( get_theme_mod( 'enable_dark_mode', true ) ) {
            wp_enqueue_style(
                'xtube-elementor-dark',
                get_template_directory_uri() . '/css/elementor-dark.css',
                array(),
                wp_get_theme()->get( 'Version' )
            );
        }
    }
    
    /**
     * Enqueue editor styles
     */
    public function enqueue_editor_styles() {
        wp_enqueue_style(
            'xtube-elementor-editor',
            get_template_directory_uri() . '/css/elementor-editor.css',
            array(),
            wp_get_theme()->get( 'Version' )
        );
    }
    
    /**
     * Reset Elementor CSS for performance
     */
    public function reset_elementor_css( $file_handle ) {
        // Optimize CSS output
        ob_start();
        echo '/* XTube Elementor Optimizations */' . "\n";
        echo '.elementor-widget-video .elementor-widget-container { overflow: hidden; }' . "\n";
        echo '.elementor-widget-video .elementor-video { aspect-ratio: 16/9; }' . "\n";
        echo '.elementor-widget-video.portrait .elementor-video { aspect-ratio: 9/16; }' . "\n";
        $css = ob_get_clean();
        wp_add_inline_style( $file_handle, $css );
    }
    
    /**
     * Register dynamic tags
     */
    public function register_dynamic_tags( $dynamic_tags ) {
        if ( ! class_exists( 'Elementor\Core\DynamicTags\Tag' ) ) {
            return;
        }
        
        // Include dynamic tag files
        require_once get_template_directory() . '/inc/elementor-dynamic-tags/video-duration-tag.php';
        require_once get_template_directory() . '/inc/elementor-dynamic-tags/video-views-tag.php';
        require_once get_template_directory() . '/inc/elementor-dynamic-tags/video-rating-tag.php';
        
        // Register tags
        $dynamic_tags->register_tag( 'XTube_Video_Duration_Tag' );
        $dynamic_tags->register_tag( 'XTube_Video_Views_Tag' );
        $dynamic_tags->register_tag( 'XTube_Video_Rating_Tag' );
    }
    
    /**
     * Register custom widgets
     */
    public function register_widgets( $widgets_manager ) {
        if ( ! class_exists( 'Elementor\Widget_Base' ) ) {
            return;
        }
        
        // Include widget files
        require_once get_template_directory() . '/inc/elementor-widgets/video-player-widget.php';
        require_once get_template_directory() . '/inc/elementor-widgets/video-grid-widget.php';
        require_once get_template_directory() . '/inc/elementor-widgets/video-carousel-widget.php';
        
        // Register widgets
        $widgets_manager->register_widget_type( new \XTube_Video_Player_Widget() );
        $widgets_manager->register_widget_type( new \XTube_Video_Grid_Widget() );
        $widgets_manager->register_widget_type( new \XTube_Video_Carousel_Widget() );
    }
    
    /**
     * Register kit tabs
     */
    public function register_kit_tabs( $kit ) {
        // Add custom settings for video layouts
        $kit->register_tab( 'xtube-video-settings', __( 'XTube Video Settings', 'wpst' ) );
    }
    
    /**
     * Add editor settings
     */
    public function add_editor_settings( $settings ) {
        $settings['xtube_video_settings'] = array(
            'default_video_aspect_ratio' => get_theme_mod( 'default_video_aspect_ratio', '16:9' ),
            'enable_video_lazy_load' => get_theme_mod( 'enable_video_lazy_load', true ),
            'video_player_theme' => get_theme_mod( 'video_player_theme', 'dark' ),
        );
        
        return $settings;
    }
}

// Initialize the Elementor compatibility class
XTube_Elementor_Compatibility::get_instance();

/**
 * ============================================================
 * LEGACY COMPATIBILITY FUNCTIONS
 * ============================================================
 */

// Register Elementor locations (legacy support)
add_action( 'elementor/theme/register_locations', 'xtube_register_elementor_locations' );
function xtube_register_elementor_locations( $elementor_theme_manager ) {
    $elementor_theme_manager->register_all_core_location();
}

// Elementor page template support
add_filter( 'elementor/document/urls/print_elements', '__return_true' );

// Wide/full alignment support
add_action( 'after_setup_theme', 'xtube_elementor_wide_support' );
function xtube_elementor_wide_support() {
    add_theme_support( 'align-wide' );
}

/**
 * ============================================================
 * ELEMENTOR PERFORMANCE OPTIMIZATIONS
 * ============================================================
 */

// Optimize font loading
add_filter( 'elementor/frontend/print_google_fonts', function() {
    return get_theme_mod( 'elementor_load_google_fonts', false );
});

// Optimize icon loading
add_filter( 'elementor/frontend/print_fa4_fa5_icons', function() {
    return get_theme_mod( 'elementor_load_icons', false );
});

// Custom Elementor breakpoints for better mobile experience
add_filter( 'elementor/frontend/breakpoints', function( $breakpoints ) {
    $breakpoints['md'] = array(
        'label' => __( 'Mobile Landscape', 'wpst' ),
        'value' => 768,
        'default_value' => 768,
        'direction' => 'max',
        'is_enabled' => true,
    );
    
    $breakpoints['lg'] = array(
        'label' => __( 'Tablet', 'wpst' ),
        'value' => 1024,
        'default_value' => 1024,
        'direction' => 'max',
        'is_enabled' => true,
    );
    
    return $breakpoints;
});

/**
 * ============================================================
 * ELEMENTOR CUSTOM CSS
 * ============================================================
 */
add_action( 'wp_head', 'xtube_elementor_custom_css', 999 );
function xtube_elementor_custom_css() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        return;
    }
    
    ?>
    <style id="xtube-elementor-custom-css">
        /* Video Player Optimizations */
        .elementor-widget-video .elementor-widget-container {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
        }
        
        .elementor-widget-video .elementor-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Portrait video handling */
        .elementor-widget-video[data-video-orientation="portrait"] .elementor-video {
            aspect-ratio: 9/16;
            object-fit: contain;
            background: #000;
        }
        
        /* Landscape video handling */
        .elementor-widget-video[data-video-orientation="landscape"] .elementor-video {
            aspect-ratio: 16/9;
            object-fit: contain;
        }
        
        /* Dark mode support */
        .elementor-dark-mode .elementor-widget-video {
            background: #141414;
        }
        
        /* Responsive video grid */
        .elementor-widget-xtube-video-grid .xtube-video-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .elementor-widget-xtube-video-grid .xtube-video-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        /* Mobile optimizations */
        @media (max-width: 767px) {
            .elementor-widget-video .elementor-video {
                aspect-ratio: 16/9 !important;
            }
        }
    </style>
    <?php
}
