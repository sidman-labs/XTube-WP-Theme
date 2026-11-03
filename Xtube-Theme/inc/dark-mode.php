<?php
/**
 * XTube Dark Mode Support v3.0
 * Advanced dark mode with system preference detection and smooth transitions
 * 
 * @package XTube
 * @author  Saieed Rahman
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ============================================================
 * DARK MODE CLASS
 * ============================================================
 */
class XTube_Dark_Mode {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_dark_mode_assets' ) );
        add_action( 'wp_head', array( $this, 'output_dark_mode_inline_css' ), 5 );
        add_action( 'wp_footer', array( $this, 'output_dark_mode_script' ) );
        add_action( 'customize_register', array( $this, 'add_customizer_controls' ) );
        add_action( 'wp_ajax_xtube_toggle_dark_mode', array( $this, 'handle_toggle_dark_mode' ) );
        add_action( 'wp_ajax_nopriv_xtube_toggle_dark_mode', array( $this, 'handle_toggle_dark_mode' ) );
        
        // Add body class
        add_filter( 'body_class', array( $this, 'add_body_class' ) );
    }
    
    /**
     * Enqueue dark mode assets
     */
    public function enqueue_dark_mode_assets(): void {
        if ( get_theme_mod( 'enable_dark_mode', true ) ) {
            wp_enqueue_style(
                'xtube-dark-mode',
                get_template_directory_uri() . '/css/dark-mode.css',
                array(),
                wp_get_theme()->get( 'Version' )
            );
            
            // Dark mode toggle script
            wp_enqueue_script(
                'xtube-dark-mode',
                get_template_directory_uri() . '/js/dark-mode.js',
                array( 'jquery' ),
                wp_get_theme()->get( 'Version' ),
                true
            );
            
            // Localize script
            wp_localize_script(
                'xtube-dark-mode',
                'xtube_dark_mode_vars',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'xtube_dark_mode_nonce' ),
                    'enabled' => get_theme_mod( 'enable_dark_mode', true ),
                    'default_mode' => get_theme_mod( 'default_dark_mode', 'system' ),
                    'transition_duration' => get_theme_mod( 'dark_mode_transition', '0.3s' ),
                )
            );
        }
    }
    
    /**
     * Output inline CSS for dark mode
     */
    public function output_dark_mode_inline_css(): void {
        if ( ! get_theme_mod( 'enable_dark_mode', true ) ) {
            return;
        }
        
        $transition_duration = get_theme_mod( 'dark_mode_transition', '0.3s' );
        $primary_color = get_theme_mod( 'primary_color', '#e50914' );
        $background_dark = get_theme_mod( 'background_dark', '#0a0a0a' );
        $surface_dark = get_theme_mod( 'surface_dark', '#141414' );
        $text_dark = get_theme_mod( 'text_dark', '#ffffff' );
        $text_muted_dark = get_theme_mod( 'text_muted_dark', '#a0a0a0' );
        
        ?>
        <style id="xtube-dark-mode-inline-css">
            /* Dark mode transitions */
            body {
                transition: background-color <?php echo esc_attr( $transition_duration ); ?> ease, 
                            color <?php echo esc_attr( $transition_duration ); ?> ease;
            }
            
            body.dark-mode {
                background-color: <?php echo esc_attr( $background_dark ); ?>;
                color: <?php echo esc_attr( $text_dark ); ?>;
            }
            
            /* Dark mode variables */
            :root {
                --xtube-bg-dark: <?php echo esc_attr( $background_dark ); ?>;
                --xtube-surface-dark: <?php echo esc_attr( $surface_dark ); ?>;
                --xtube-text-dark: <?php echo esc_attr( $text_dark ); ?>;
                --xtube-text-muted-dark: <?php echo esc_attr( $text_muted_dark ); ?>;
                --xtube-primary-dark: <?php echo esc_attr( $primary_color ); ?>;
            }
            
            /* Smooth transitions for all elements */
            .dark-mode *,
            .dark-mode *::before,
            .dark-mode *::after {
                transition: background-color <?php echo esc_attr( $transition_duration ); ?> ease,
                            border-color <?php echo esc_attr( $transition_duration ); ?> ease,
                            color <?php echo esc_attr( $transition_duration ); ?> ease,
                            fill <?php echo esc_attr( $transition_duration ); ?> ease;
            }
            
            /* Prevent transitions on load */
            .no-transition * {
                transition: none !important;
            }
            
            /* Dark mode toggle button */
            .dark-mode-toggle {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                background: var(--xtube-surface-dark, #141414);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 50px;
                padding: 8px 16px;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                color: var(--xtube-text-dark, #ffffff);
                backdrop-filter: blur(10px);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
                transition: all 0.3s ease;
            }
            
            .dark-mode-toggle:hover {
                background: var(--xtube-primary-dark, #e50914);
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(229, 9, 20, 0.4);
            }
            
            .dark-mode-toggle .icon {
                width: 16px;
                height: 16px;
                transition: transform 0.3s ease;
            }
            
            .dark-mode-toggle:hover .icon {
                transform: rotate(180deg);
            }
            
            /* System preference detection */
            @media (prefers-color-scheme: dark) {
                body.auto-dark-mode {
                    background-color: <?php echo esc_attr( $background_dark ); ?>;
                    color: <?php echo esc_attr( $text_dark ); ?>;
                }
            }
            
            /* High contrast mode support */
            @media (prefers-contrast: high) {
                .dark-mode {
                    --xtube-bg-dark: #000000;
                    --xtube-surface-dark: #1a1a1a;
                    --xtube-text-dark: #ffffff;
                }
            }
            
            /* Reduced motion support */
            @media (prefers-reduced-motion: reduce) {
                .dark-mode *,
                .dark-mode *::before,
                .dark-mode *::after,
                .dark-mode-toggle {
                    transition: none !important;
                }
            }
        </style>
        <?php
    }
    
    /**
     * Output dark mode script
     */
    public function output_dark_mode_script(): void {
        if ( ! get_theme_mod( 'enable_dark_mode', true ) ) {
            return;
        }
        
        ?>
        <script>
        // Dark mode initialization
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof xtubeDarkMode !== 'undefined') {
                xtubeDarkMode.init({
                    defaultMode: '<?php echo esc_js( get_theme_mod( 'default_dark_mode', 'system' ) ); ?>',
                    showToggle: <?php echo get_theme_mod( 'show_dark_mode_toggle', true ) ? 'true' : 'false'; ?>,
                    rememberPreference: <?php echo get_theme_mod( 'remember_dark_mode', true ) ? 'true' : 'false'; ?>,
                    transitionDuration: '<?php echo esc_js( get_theme_mod( 'dark_mode_transition', '0.3s' ) ); ?>'
                });
            }
        });
        </script>
        <?php
    }
    
    /**
     * Add customizer controls
     */
    public function add_customizer_controls( WP_Customize_Manager $wp_customize ): void {
        // Dark Mode Section
        $wp_customize->add_section( 'xtube_dark_mode', array(
            'title' => __( 'Dark Mode', 'wpst' ),
            'priority' => 30,
            'capability' => 'edit_theme_options',
        ) );
        
        // Enable Dark Mode
        $wp_customize->add_setting( 'enable_dark_mode', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ) );
        
        $wp_customize->add_control( 'enable_dark_mode', array(
            'label' => __( 'Enable Dark Mode', 'wpst' ),
            'section' => 'xtube_dark_mode',
            'type' => 'checkbox',
        ) );
        
        // Default Mode
        $wp_customize->add_setting( 'default_dark_mode', array(
            'default' => 'system',
            'sanitize_callback' => function( $value ) {
                return in_array( $value, array( 'light', 'dark', 'system' ) ) ? $value : 'system';
            },
        ) );
        
        $wp_customize->add_control( 'default_dark_mode', array(
            'label' => __( 'Default Mode', 'wpst' ),
            'section' => 'xtube_dark_mode',
            'type' => 'select',
            'choices' => array(
                'light' => __( 'Light', 'wpst' ),
                'dark' => __( 'Dark', 'wpst' ),
                'system' => __( 'System Preference', 'wpst' ),
            ),
        ) );
        
        // Show Toggle Button
        $wp_customize->add_setting( 'show_dark_mode_toggle', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ) );
        
        $wp_customize->add_control( 'show_dark_mode_toggle', array(
            'label' => __( 'Show Toggle Button', 'wpst' ),
            'section' => 'xtube_dark_mode',
            'type' => 'checkbox',
        ) );
        
        // Remember Preference
        $wp_customize->add_setting( 'remember_dark_mode', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ) );
        
        $wp_customize->add_control( 'remember_dark_mode', array(
            'label' => __( 'Remember User Preference', 'wpst' ),
            'section' => 'xtube_dark_mode',
            'type' => 'checkbox',
        ) );
        
        // Transition Duration
        $wp_customize->add_setting( 'dark_mode_transition', array(
            'default' => '0.3s',
            'sanitize_callback' => function( $value ) {
                return preg_match( '/^[0-9.]+s$/', $value ) ? $value : '0.3s';
            },
        ) );
        
        $wp_customize->add_control( 'dark_mode_transition', array(
            'label' => __( 'Transition Duration', 'wpst' ),
            'section' => 'xtube_dark_mode',
            'type' => 'text',
            'description' => __( 'Enter duration in seconds (e.g., 0.3s)', 'wpst' ),
        ) );
        
        // Dark Mode Colors
        $wp_customize->add_setting( 'background_dark', array(
            'default' => '#0a0a0a',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_dark', array(
            'label' => __( 'Background Color', 'wpst' ),
            'section' => 'xtube_dark_mode',
        ) ) );
        
        $wp_customize->add_setting( 'surface_dark', array(
            'default' => '#141414',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'surface_dark', array(
            'label' => __( 'Surface Color', 'wpst' ),
            'section' => 'xtube_dark_mode',
        ) ) );
        
        $wp_customize->add_setting( 'text_dark', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'text_dark', array(
            'label' => __( 'Text Color', 'wpst' ),
            'section' => 'xtube_dark_mode',
        ) ) );
        
        $wp_customize->add_setting( 'text_muted_dark', array(
            'default' => '#a0a0a0',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );
        
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'text_muted_dark', array(
            'label' => __( 'Muted Text Color', 'wpst' ),
            'section' => 'xtube_dark_mode',
        ) ) );
    }
    
    /**
     * Add body class
     */
    public function add_body_class( array $classes ): array {
        if ( get_theme_mod( 'enable_dark_mode', true ) ) {
            $classes[] = 'dark-mode-supported';
            
            // Add current mode class
            $current_mode = $this->get_current_mode();
            $classes[] = $current_mode . '-mode';
        }
        
        return $classes;
    }
    
    /**
     * Get current dark mode
     */
    private function get_current_mode(): string {
        if ( isset( $_COOKIE['xtube_dark_mode'] ) ) {
            return sanitize_text_field( $_COOKIE['xtube_dark_mode'] ) === 'dark' ? 'dark' : 'light';
        }
        
        return get_theme_mod( 'default_dark_mode', 'system' );
    }
    
    /**
     * Handle dark mode toggle
     */
    public function handle_toggle_dark_mode(): void {
        check_ajax_referer( 'xtube_dark_mode_nonce', 'nonce' );
        
        $mode = isset( $_POST['mode'] ) ? sanitize_text_field( $_POST['mode'] ) : 'light';
        
        if ( in_array( $mode, array( 'light', 'dark' ) ) ) {
            // Set cookie for 1 year
            setcookie( 'xtube_dark_mode', $mode, time() + YEAR_IN_SECONDS, '/', '', is_ssl(), true );
            
            wp_send_json_success( array(
                'mode' => $mode,
                'message' => $mode === 'dark' ? __( 'Dark mode enabled', 'wpst' ) : __( 'Light mode enabled', 'wpst' )
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Invalid mode', 'wpst' ) ) );
        }
    }
}

// Initialize dark mode
XTube_Dark_Mode::get_instance();

/**
 * ============================================================
   HELPER FUNCTIONS
   ============================================================
 */

/**
 * Check if dark mode is active
 */
if ( ! function_exists( 'xtube_is_dark_mode' ) ) {
    function xtube_is_dark_mode(): bool {
        if ( ! get_theme_mod( 'enable_dark_mode', true ) ) {
            return false;
        }
        
        if ( isset( $_COOKIE['xtube_dark_mode'] ) ) {
            return sanitize_text_field( $_COOKIE['xtube_dark_mode'] ) === 'dark';
        }
        
        $default_mode = get_theme_mod( 'default_dark_mode', 'system' );
        
        if ( $default_mode === 'system' ) {
            return isset( $_SERVER['HTTP_SEC_CH_PREFERS_COLOR_SCHEME'] ) && 
                   $_SERVER['HTTP_SEC_CH_PREFERS_COLOR_SCHEME'] === 'dark';
        }
        
        return $default_mode === 'dark';
    }
}

/**
 * Get dark mode toggle button HTML
 */
if ( ! function_exists( 'xtube_dark_mode_toggle' ) ) {
    function xtube_dark_mode_toggle(): string {
        if ( ! get_theme_mod( 'enable_dark_mode', true ) || ! get_theme_mod( 'show_dark_mode_toggle', true ) ) {
            return '';
        }
        
        $is_dark = xtube_is_dark_mode();
        $icon = $is_dark ? '☀️' : '🌙';
        $label = $is_dark ? __( 'Light Mode', 'wpst' ) : __( 'Dark Mode', 'wpst' );
        
        return sprintf(
            '<button class="dark-mode-toggle" aria-label="%s" data-current-mode="%s">
                <span class="icon">%s</span>
                <span class="label">%s</span>
            </button>',
            esc_attr( $label ),
            $is_dark ? 'dark' : 'light',
            esc_html( $icon ),
            esc_html( $label )
        );
    }
}
