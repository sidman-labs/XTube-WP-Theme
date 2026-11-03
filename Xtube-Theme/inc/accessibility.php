<?php
/**
 * XTube Accessibility Features v3.0
 * WCAG 2.1 AA compliance implementation
 * 
 * @package XTube
 * @author  Saieed Rahman
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ============================================================
   ACCESSIBILITY CLASS
   ============================================================
 */
class XTube_Accessibility {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_accessibility_assets' ) );
        add_action( 'wp_head', array( $this, 'output_accessibility_meta' ), 1 );
        add_action( 'wp_footer', array( $this, 'output_skip_links' ) );
        add_filter( 'wp_nav_menu_args', array( $this, 'enhance_menu_accessibility' ) );
        add_filter( 'the_content', array( $this, 'enhance_content_accessibility' ) );
        add_filter( 'widget_title', array( $this, 'add_widget_heading_context' ) );
        add_action( 'init', array( $this, 'add_a11y_support' ) );
        
        // Screen reader support
        add_filter( 'navigation_markup_template', array( $this, 'add_screen_reader_text' ) );
        add_filter( 'paginate_links', array( $this, 'add_pagination_labels' ) );
        
        // Form accessibility
        add_filter( 'comment_form_defaults', array( $this, 'enhance_comment_form_accessibility' ) );
        
        // Video player accessibility
        add_action( 'wp_footer', array( $this, 'output_video_player_a11y' ) );
    }
    
    /**
     * Enqueue accessibility assets
     */
    public function enqueue_accessibility_assets(): void {
        wp_enqueue_script(
            'xtube-accessibility',
            get_template_directory_uri() . '/js/accessibility.js',
            array( 'jquery' ),
            wp_get_theme()->get( 'Version' ),
            true
        );
        
        wp_localize_script(
            'xtube-accessibility',
            'xtube_a11y_vars',
            array(
                'focus_outline_visible' => get_theme_mod( 'show_focus_outline', true ),
                'keyboard_navigation' => get_theme_mod( 'enable_keyboard_nav', true ),
                'screen_reader announcements' => get_theme_mod( 'enable_sr_announcements', true ),
            )
        );
    }
    
    /**
     * Output accessibility meta tags
     */
    public function output_accessibility_meta(): void {
        ?>
        <!-- Accessibility Meta Tags -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
        <meta name="color-scheme" content="light dark">
        <meta name="supported-color-schemes" content="light dark">
        <?php
    }
    
    /**
     * Output skip links
     */
    public function output_skip_links(): void {
        ?>
        <!-- Skip Links for Screen Readers -->
        <a class="skip-link screen-reader-text" href="#main-content">
            <?php esc_html_e( 'Skip to main content', 'wpst' ); ?>
        </a>
        <a class="skip-link screen-reader-text" href="#primary-navigation">
            <?php esc_html_e( 'Skip to navigation', 'wpst' ); ?>
        </a>
        <a class="skip-link screen-reader-text" href="#search">
            <?php esc_html_e( 'Skip to search', 'wpst' ); ?>
        </a>
        <?php
    }
    
    /**
     * Enhance menu accessibility
     */
    public function enhance_menu_accessibility( array $args ): array {
        // Add ARIA labels and roles
        $args['container_class'] = isset( $args['container_class'] ) ? $args['container_class'] . ' main-navigation' : 'main-navigation';
        $args['container_aria_label'] = isset( $args['container_aria_label'] ) ? $args['container_aria_label'] : __( 'Primary Navigation', 'wpst' );
        $args['container_role'] = 'navigation';
        
        return $args;
    }
    
    /**
     * Enhance content accessibility
     */
    public function enhance_content_accessibility( string $content ): string {
        // Add proper heading structure
        $content = $this->ensure_heading_hierarchy( $content );
        
        // Add language attributes to content
        $content = $this->add_language_attributes( $content );
        
        // Enhance link accessibility
        $content = $this->enhance_link_accessibility( $content );
        
        // Add table headers
        $content = $this->enhance_table_accessibility( $content );
        
        return $content;
    }
    
    /**
     * Ensure proper heading hierarchy
     */
    private function ensure_heading_hierarchy( string $content ): string {
        // Find all headings and ensure proper hierarchy
        preg_match_all( '/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/i', $content, $matches, PREG_SET_ORDER );
        
        $current_level = 1;
        foreach ( $matches as $match ) {
            $level = intval( $match[1] );
            
            if ( $level > $current_level + 1 ) {
                // Fix skipped heading levels
                $correct_level = $current_level + 1;
                $content = str_replace( $match[0], '<h' . $correct_level . '>' . $match[2] . '</h' . $correct_level . '>', $content );
                $current_level = $correct_level;
            } else {
                $current_level = $level;
            }
        }
        
        return $content;
    }
    
    /**
     * Add language attributes to content
     */
    private function add_language_attributes( string $content ): string {
        $lang = get_bloginfo( 'language' );
        
        // Add lang attribute to content wrapper if not present
        if ( strpos( $content, 'lang=' ) === false ) {
            $content = '<div lang="' . esc_attr( $lang ) . '">' . $content . '</div>';
        }
        
        return $content;
    }
    
    /**
     * Enhance link accessibility
     */
    private function enhance_link_accessibility( string $content ): string {
        // Add aria-labels to links with only icons
        $content = preg_replace(
            '/<a([^>]*?)><i[^>]*class="([^"]*?)fa-([^"]*?)"[^>]*><\/i><\/a>/i',
            '<a$1 aria-label="' . esc_attr__( 'Link', 'wpst' ) . ' $3"><i class="fa-$2 fa-$3" aria-hidden="true"></i></a>',
            $content
        );
        
        // Add target and rel attributes for external links
        $content = preg_replace_callback(
            '/<a([^>]*?href="([^"]*?)")([^>]*)>/i',
            function( $matches ) {
                $url = $matches[2];
                $attrs = $matches[1] . $matches[3];
                
                if ( $this->is_external_url( $url ) ) {
                    $attrs .= ' target="_blank" rel="noopener noreferrer"';
                    if ( strpos( $attrs, 'aria-label' ) === false ) {
                        $attrs .= ' aria-label="' . esc_attr__( 'External link', 'wpst' ) . '"';
                    }
                }
                
                return '<a' . $attrs . '>';
            },
            $content
        );
        
        return $content;
    }
    
    /**
     * Enhance table accessibility
     */
    private function enhance_table_accessibility( string $content ): string {
        // Add scope attributes to table headers
        $content = preg_replace(
            '/<th([^>]*)>/i',
            '<th$1 scope="col">',
            $content
        );
        
        // Add caption to tables without one
        if ( preg_match_all( '/<table[^>]*>(.*?)<\/table>/is', $content, $table_matches ) ) {
            foreach ( $table_matches[0] as $table ) {
                if ( strpos( $table, '<caption' ) === false ) {
                    $table_with_caption = str_replace( '<table', '<table><caption class="screen-reader-text">' . esc_html__( 'Data table', 'wpst' ) . '</caption>', $table );
                    $content = str_replace( $table, $table_with_caption, $content );
                }
            }
        }
        
        return $content;
    }
    
    /**
     * Add widget heading context
     */
    public function add_widget_heading_context( string $title ): string {
        // Add aria-label to widget titles for better context
        return '<span class="widget-title-text">' . $title . '</span>';
    }
    
    /**
     * Add accessibility support
     */
    public function add_a11y_support(): void {
        // Add theme support for accessibility
        add_theme_support( 'accessibility' );
        add_theme_support( 'wcag-2.1' );
        
        // Enable HTML5 semantic markup
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
            'navigation-widgets',
        ) );
        
        // Enable custom logo with accessibility
        add_theme_support( 'custom-logo', array(
            'height'               => 80,
            'width'                => 300,
            'flex-height'          => true,
            'flex-width'           => true,
            'header-text'          => array( 'site-title', 'site-description' ),
            'unlink-homepage-logo' => true,
        ) );
    }
    
    /**
     * Add screen reader text to navigation
     */
    public function add_screen_reader_text( string $template ): string {
        return str_replace(
            '<nav class="navigation %1$s" aria-label="%4$s">',
            '<nav class="navigation %1$s" role="navigation" aria-label="%4$s">',
            $template
        );
    }
    
    /**
     * Add pagination labels
     */
    public function add_pagination_labels( array $links ): array {
        foreach ( $links as $key => $link ) {
            if ( strpos( $link, 'class="prev' ) !== false ) {
                $links[$key] = str_replace( '>', ' aria-label="' . esc_attr__( 'Previous page', 'wpst' ) . '">', $link );
            } elseif ( strpos( $link, 'class="next' ) !== false ) {
                $links[$key] = str_replace( '>', ' aria-label="' . esc_attr__( 'Next page', 'wpst' ) . '">', $link );
            }
        }
        
        return $links;
    }
    
    /**
     * Enhance comment form accessibility
     */
    public function enhance_comment_form_accessibility( array $defaults ): array {
        $defaults['title_reply_before'] = '<h3 id="reply-title" class="comment-reply-title">';
        $defaults['title_reply_after'] = '</h3>';
        
        // Add required attributes
        $defaults['fields']['author'] = str_replace(
            '<input',
            '<input required aria-required="true"',
            $defaults['fields']['author']
        );
        
        $defaults['fields']['email'] = str_replace(
            '<input',
            '<input required aria-required="true"',
            $defaults['fields']['email']
        );
        
        $defaults['comment_field'] = str_replace(
            '<textarea',
            '<textarea required aria-required="true" aria-label="' . esc_attr__( 'Comment', 'wpst' ) . '"',
            $defaults['comment_field']
        );
        
        return $defaults;
    }
    
    /**
     * Output video player accessibility enhancements
     */
    public function output_video_player_a11y(): void {
        if ( ! is_single() || 'post' !== get_post_type() ) {
            return;
        }
        
        ?>
        <script>
        // Video Player Accessibility Enhancements
        document.addEventListener('DOMContentLoaded', function() {
            const videos = document.querySelectorAll('.xtube-video-player');
            
            videos.forEach(function(video) {
                // Add comprehensive ARIA labels
                if (!video.getAttribute('aria-label')) {
                    video.setAttribute('aria-label', '<?php esc_html_e( 'Video player', 'wpst' ); ?>');
                }
                
                // Add keyboard navigation
                video.addEventListener('keydown', function(e) {
                    switch(e.key) {
                        case ' ':
                        case 'Spacebar':
                            e.preventDefault();
                            if (video.paused) {
                                video.play();
                            } else {
                                video.pause();
                            }
                            break;
                        case 'ArrowLeft':
                            e.preventDefault();
                            video.currentTime = Math.max(0, video.currentTime - 10);
                            break;
                        case 'ArrowRight':
                            e.preventDefault();
                            video.currentTime = Math.min(video.duration, video.currentTime + 10);
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            video.volume = Math.min(1, video.volume + 0.1);
                            break;
                        case 'ArrowDown':
                            e.preventDefault();
                            video.volume = Math.max(0, video.volume - 0.1);
                            break;
                        case 'f':
                        case 'F':
                            e.preventDefault();
                            if (video.requestFullscreen) {
                                video.requestFullscreen();
                            }
                            break;
                    }
                });
                
                // Add live region for status announcements
                const liveRegion = document.createElement('div');
                liveRegion.setAttribute('aria-live', 'polite');
                liveRegion.setAttribute('aria-atomic', 'true');
                liveRegion.className = 'sr-only video-status';
                video.parentNode.appendChild(liveRegion);
                
                // Announce video status changes
                video.addEventListener('play', function() {
                    liveRegion.textContent = '<?php esc_html_e( 'Video playing', 'wpst' ); ?>';
                });
                
                video.addEventListener('pause', function() {
                    liveRegion.textContent = '<?php esc_html_e( 'Video paused', 'wpst' ); ?>';
                });
                
                video.addEventListener('ended', function() {
                    liveRegion.textContent = '<?php esc_html_e( 'Video ended', 'wpst' ); ?>';
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Check if URL is external
     */
    private function is_external_url( string $url ): bool {
        $home_url = parse_url( home_url() );
        $link_url = parse_url( $url );
        
        return $link_url['host'] !== $home_url['host'];
    }
}

// Initialize accessibility
XTube_Accessibility::get_instance();

/**
 * ============================================================
   ACCESSIBILITY HELPER FUNCTIONS
   ============================================================
 */

/**
 * Get accessible post title
 */
if ( ! function_exists( 'xtube_get_accessible_title' ) ) {
    function xtube_get_accessible_title( int $post_id = null ): string {
        if ( null === $post_id ) {
            $post_id = get_the_ID();
        }
        
        $title = get_the_title( $post_id );
        $post_type = get_post_type( $post_id );
        
        // Add post type context for screen readers
        $context = '';
        switch ( $post_type ) {
            case 'post':
                $context = ' (' . __( 'Video', 'wpst' ) . ')';
                break;
            case 'page':
                $context = ' (' . __( 'Page', 'wpst' ) . ')';
                break;
        }
        
        return $title . $context;
    }
}

/**
 * Output accessible breadcrumb navigation
 */
if ( ! function_exists( 'xtube_accessible_breadcrumbs' ) ) {
    function xtube_accessible_breadcrumbs(): void {
        if ( is_front_page() ) {
            return;
        }
        
        echo '<nav aria-label="' . esc_attr__( 'Breadcrumb navigation', 'wpst' ) . '" class="breadcrumb">';
        echo '<ol class="breadcrumb-list">';
        
        // Home
        echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'wpst' ) . '</a></li>';
        
        if ( is_category() || is_tag() ) {
            echo '<li aria-current="page">' . single_term_title( '', false ) . '</li>';
        } elseif ( is_single() ) {
            $categories = get_the_category();
            if ( $categories && ! is_wp_error( $categories ) ) {
                echo '<li><a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a></li>';
            }
            echo '<li aria-current="page">' . get_the_title() . '</li>';
        }
        
        echo '</ol>';
        echo '</nav>';
    }
}
