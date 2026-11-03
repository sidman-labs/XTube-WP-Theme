<?php
/**
 * Advanced Video Player for XTube Theme v3.0
 * Handles portrait and landscape videos with responsive design
 * 
 * @package XTube
 * @author  Saieed Rahman
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ============================================================
 * ADVANCED VIDEO PLAYER CLASS
 * ============================================================
 */
class XTube_Video_Player {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_video_assets' ) );
        add_action( 'wp_footer', array( $this, 'output_video_player_script' ) );
        add_filter( 'the_content', array( $this, 'enhance_video_content' ), 10 );
        add_shortcode( 'xtube_video', array( $this, 'video_shortcode' ) );
        
        // AJAX for video analytics
        add_action( 'wp_ajax_xtube_video_analytics', array( $this, 'handle_video_analytics' ) );
        add_action( 'wp_ajax_nopriv_xtube_video_analytics', array( $this, 'handle_video_analytics' ) );
    }
    
    /**
     * Enqueue video player assets
     */
    public function enqueue_video_assets(): void {
        if ( is_single() && 'post' === get_post_type() ) {
            // Enhanced Video.js CSS with custom theme
            wp_enqueue_style(
                'xtube-videojs',
                get_template_directory_uri() . '/css/video-player.css',
                array(),
                wp_get_theme()->get( 'Version' )
            );
            
            // Video.js core
            if ( ! is_plugin_active( 'clean-tube-player/clean-tube-player.php' ) ) {
                wp_enqueue_script(
                    'xtube-videojs-core',
                    get_template_directory_uri() . '/vendor/videojs/video.min.js',
                    array(),
                    '8.6.1',
                    true
                );
                
                // Video.js quality selector
                wp_enqueue_script(
                    'xtube-videojs-quality',
                    get_template_directory_uri() . '/vendor/videojs/videojs-quality-selector.min.js',
                    array( 'xtube-videojs-core' ),
                    '1.3.0',
                    true
                );
            }
            
            // Custom video player script
            wp_enqueue_script(
                'xtube-video-player',
                get_template_directory_uri() . '/js/xtube-video-player.js',
                array( 'jquery' ),
                wp_get_theme()->get( 'Version' ),
                true
            );
            
            // Localize script
            wp_localize_script(
                'xtube-video-player',
                'xtube_video_vars',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'xtube_video_nonce' ),
                    'enable_analytics' => get_theme_mod( 'enable_video_analytics', true ),
                    'autoplay_next' => get_theme_mod( 'autoplay_next_video', false ),
                    'portrait_handling' => get_theme_mod( 'portrait_video_handling', 'contain' ),
                )
            );
        }
    }
    
    /**
     * Output video player initialization script
     */
    public function output_video_player_script(): void {
        if ( ! is_single() || 'post' !== get_post_type() ) {
            return;
        }
        
        ?>
        <script>
        // XTube Video Player Initialization
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof xtubeVideoPlayer !== 'undefined') {
                xtubeVideoPlayer.init({
                    selector: '.xtube-video-player',
                    portraitMode: '<?php echo esc_js( get_theme_mod( 'portrait_video_handling', 'contain' ) ); ?>',
                    enableAnalytics: <?php echo get_theme_mod( 'enable_video_analytics', true ) ? 'true' : 'false'; ?>,
                    autoplayNext: <?php echo get_theme_mod( 'autoplay_next_video', false ) ? 'true' : 'false'; ?>,
                    theme: '<?php echo esc_js( get_theme_mod( 'video_player_theme', 'dark' ) ); ?>'
                });
            }
        });
        </script>
        <?php
    }
    
    /**
     * Enhance video content with responsive player
     */
    public function enhance_video_content( string $content ): string {
        if ( ! is_single() || 'post' !== get_post_type() ) {
            return $content;
        }
        
        global $post;
        
        // Get video metadata
        $video_url = get_post_meta( $post->ID, 'video_url', true );
        $duration = get_post_meta( $post->ID, 'duration', true );
        $is_portrait = get_post_meta( $post->ID, 'is_portrait', false );
        
        if ( $video_url ) {
            $video_player = $this->generate_video_player( $video_url, $duration, $is_portrait );
            
            // Replace default video embed with enhanced player
            $content = preg_replace( '/<video[^>]*>.*?<\/video>/is', $video_player, $content );
            
            // Add video player after content if no video found
            if ( strpos( $content, '<video' ) === false ) {
                $content .= $video_player;
            }
        }
        
        return $content;
    }
    
    /**
     * Generate responsive video player HTML
     */
    private function generate_video_player( string $video_url, int $duration = 0, bool $is_portrait = false ): string {
        $post_id = get_the_ID();
        $video_id = 'xtube-video-' . $post_id;
        $poster_url = get_the_post_thumbnail_url( $post_id, 'large' );
        
        $classes = array(
            'xtube-video-player',
            'video-js',
            'vjs-default-skin',
            'vjs-big-play-centered'
        );
        
        if ( $is_portrait ) {
            $classes[] = 'video-portrait';
        }
        
        $data_attributes = array(
            'data-setup' => '{"fluid": true, "responsive": true, "aspectRatio": "' . ( $is_portrait ? '9:16' : '16:9' ) . '"}',
            'data-video-id' => $post_id,
            'data-video-url' => esc_url( $video_url ),
            'data-duration' => $duration,
            'data-orientation' => $is_portrait ? 'portrait' : 'landscape'
        );
        
        $player_html = sprintf(
            '<div class="xtube-video-container %1$s">',
            $is_portrait ? 'portrait-container' : 'landscape-container'
        );
        
        $player_html .= sprintf(
            '<video id="%1$s" class="%2$s" %3$s %4$s>',
            esc_attr( $video_id ),
            esc_attr( implode( ' ', $classes ) ),
            $poster_url ? 'poster="' . esc_attr( $poster_url ) . '"' : '',
            $this->build_attributes( $data_attributes )
        );
        
        // Add video sources
        $player_html .= sprintf(
            '<source src="%1$s" type="%2$s">',
            esc_url( $video_url ),
            $this->get_video_mime_type( $video_url )
        );
        
        // Fallback for browsers that don't support HTML5 video
        $player_html .= sprintf(
            '<p class="vjs-no-js">%s <a href="%s">%s</a> %s</p>',
            esc_html__( 'To view this video please enable JavaScript, and consider upgrading to a web browser that', 'wpst' ),
            'https://videojs.com/html5-video-support/',
            esc_html__( 'supports HTML5 video', 'wpst' ),
            esc_html__( 'or download the video directly.', 'wpst' )
        );
        
        $player_html .= '</video>';
        
        // Add video controls overlay
        $player_html .= $this->generate_video_controls( $post_id, $duration, $is_portrait );
        
        $player_html .= '</div>';
        
        return $player_html;
    }
    
    /**
     * Generate video controls overlay
     */
    private function generate_video_controls( int $post_id, int $duration, bool $is_portrait ): string {
        $controls = '<div class="xtube-video-overlay">';
        
        // Video info
        $controls .= '<div class="video-info">';
        $controls .= '<h3 class="video-title">' . get_the_title( $post_id ) . '</h3>';
        
        if ( $duration ) {
            $controls .= '<span class="video-duration">' . $this->format_duration( $duration ) . '</span>';
        }
        
        $controls .= '</div>';
        
        // Action buttons
        $controls .= '<div class="video-actions">';
        $controls .= '<button class="btn btn-sm btn-outline-light like-video" data-video-id="' . $post_id . '">';
        $controls .= '<i class="fas fa-thumbs-up"></i> <span class="like-count">' . $this->get_like_count( $post_id ) . '</span>';
        $controls .= '</button>';
        
        $controls .= '<button class="btn btn-sm btn-outline-light share-video" data-video-id="' . $post_id . '">';
        $controls .= '<i class="fas fa-share"></i> ' . esc_html__( 'Share', 'wpst' );
        $controls .= '</button>';
        
        $controls .= '<button class="btn btn-sm btn-outline-light download-video" href="' . get_post_meta( $post_id, 'download_url', true ) . '">';
        $controls .= '<i class="fas fa-download"></i> ' . esc_html__( 'Download', 'wpst' );
        $controls .= '</button>';
        
        $controls .= '</div>';
        
        $controls .= '</div>';
        
        return $controls;
    }
    
    /**
     * Video shortcode
     */
    public function video_shortcode( array $atts ): string {
        $atts = shortcode_atts( array(
            'id' => get_the_ID(),
            'url' => '',
            'poster' => '',
            'autoplay' => 'false',
            'muted' => 'false',
            'loop' => 'false',
            'portrait' => 'false',
        ), $atts, 'xtube_video' );
        
        $post_id = intval( $atts['id'] );
        $video_url = $atts['url'] ?: get_post_meta( $post_id, 'video_url', true );
        $is_portrait = filter_var( $atts['portrait'], FILTER_VALIDATE_BOOLEAN );
        
        if ( ! $video_url ) {
            return '<p class="error">' . esc_html__( 'No video URL provided.', 'wpst' ) . '</p>';
        }
        
        return $this->generate_video_player( $video_url, 0, $is_portrait );
    }
    
    /**
     * Handle video analytics
     */
    public function handle_video_analytics(): void {
        check_ajax_referer( 'xtube_video_nonce', 'nonce' );
        
        $video_id = intval( $_POST['video_id'] ?? 0 );
        $action = sanitize_text_field( $_POST['analytics_action'] ?? '' );
        
        if ( ! $video_id || ! $action ) {
            wp_die( 'Invalid parameters' );
        }
        
        switch ( $action ) {
            case 'play':
                $this->increment_video_views( $video_id );
                break;
            case 'complete':
                $this->mark_video_completed( $video_id );
                break;
            case 'quality_change':
                $this->track_quality_preference( $video_id, sanitize_text_field( $_POST['quality'] ?? '' ) );
                break;
        }
        
        wp_die( 'Analytics recorded' );
    }
    
    /**
     * Increment video views
     */
    private function increment_video_views( int $video_id ): void {
        $views = get_post_meta( $video_id, 'post_views_count', true ) ?: 0;
        update_post_meta( $video_id, 'post_views_count', $views + 1 );
    }
    
    /**
     * Mark video as completed
     */
    private function mark_video_completed( int $video_id ): void {
        $user_id = get_current_user_id();
        if ( $user_id ) {
            $completed = get_user_meta( $user_id, 'completed_videos', true ) ?: array();
            if ( ! in_array( $video_id, $completed ) ) {
                $completed[] = $video_id;
                update_user_meta( $user_id, 'completed_videos', $completed );
            }
        }
    }
    
    /**
     * Track quality preference
     */
    private function track_quality_preference( int $video_id, string $quality ): void {
        $preferences = get_post_meta( $video_id, 'quality_preferences', true ) ?: array();
        if ( ! isset( $preferences[$quality] ) ) {
            $preferences[$quality] = 0;
        }
        $preferences[$quality]++;
        update_post_meta( $video_id, 'quality_preferences', $preferences );
    }
    
    /**
     * Get like count
     */
    private function get_like_count( int $post_id ): int {
        return intval( get_post_meta( $post_id, 'likes_count', true ) ?: 0 );
    }
    
    /**
     * Format duration
     */
    private function format_duration( int $seconds ): string {
        $hours = floor( $seconds / 3600 );
        $minutes = floor( ( $seconds % 3600 ) / 60 );
        $secs = $seconds % 60;
        
        if ( $hours > 0 ) {
            return sprintf( '%d:%02d:%02d', $hours, $minutes, $secs );
        } else {
            return sprintf( '%d:%02d', $minutes, $secs );
        }
    }
    
    /**
     * Get video MIME type
     */
    private function get_video_mime_type( string $video_url ): string {
        $extension = strtolower( pathinfo( $video_url, PATHINFO_EXTENSION ) );
        
        $mime_types = array(
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'm4v' => 'video/mp4',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
        );
        
        return $mime_types[$extension] ?? 'video/mp4';
    }
    
    /**
     * Build HTML attributes
     */
    private function build_attributes( array $attributes ): string {
        $html = '';
        foreach ( $attributes as $key => $value ) {
            if ( is_bool( $value ) ) {
                if ( $value ) {
                    $html .= ' ' . esc_attr( $key );
                }
            } else {
                $html .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
            }
        }
        return $html;
    }
}

// Initialize the video player
XTube_Video_Player::get_instance();

/**
 * ============================================================
 * HELPER FUNCTIONS
 * ============================================================
 */

/**
 * Get video preview with enhanced player
 */
if ( ! function_exists( 'vtt_get_video_preview' ) ) {
    function vtt_get_video_preview( int $post_id = null ): string {
        if ( null === $post_id ) {
            global $post;
            $post_id = $post->ID;
        }
        
        $post_id = intval( $post_id );
        $trailer_url = get_post_meta( $post_id, 'trailer_url', true );
        
        if ( ! $trailer_url ) {
            return '';
        }
        
        $is_portrait = get_post_meta( $post_id, 'is_portrait', false );
        $poster_url = get_the_post_thumbnail_url( $post_id, 'medium' );
        
        $player = XTube_Video_Player::get_instance();
        return $player->generate_video_player( $trailer_url, 0, $is_portrait );
    }
}
