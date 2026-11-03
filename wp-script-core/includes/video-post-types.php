<?php
/**
 * XTube Toolkit - Video Post Types v3.0
 * Custom post types for videos, actors, and video categories
 * 
 * @package XTubeToolkit
 * @author  Saieed Rahman
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ============================================================
   VIDEO POST TYPES CLASS
   ============================================================
 */
class XTube_Video_Post_Types {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_action( 'template_redirect', array( $this, 'handle_video_rewrite_rules' ) );
        add_filter( 'template_include', array( $this, 'video_template_include' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_video_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_video_meta' ), 10, 2 );
        
        // Admin columns
        add_filter( 'manage_video_posts_columns', array( $this, 'video_admin_columns' ) );
        add_action( 'manage_video_posts_custom_column', array( $this, 'video_admin_column_data' ), 10, 2 );
        
        // REST API support
        add_action( 'rest_api_init', array( $this, 'register_rest_fields' ) );
    }
    
    /**
     * Register custom post types
     */
    public function register_post_types(): void {
        // Video Post Type
        register_post_type( 'video', array(
            'labels' => array(
                'name' => __( 'Videos', 'wpst' ),
                'singular_name' => __( 'Video', 'wpst' ),
                'add_new' => __( 'Add New Video', 'wpst' ),
                'add_new_item' => __( 'Add New Video', 'wpst' ),
                'edit_item' => __( 'Edit Video', 'wpst' ),
                'new_item' => __( 'New Video', 'wpst' ),
                'view_item' => __( 'View Video', 'wpst' ),
                'search_items' => __( 'Search Videos', 'wpst' ),
                'not_found' => __( 'No videos found', 'wpst' ),
                'not_found_in_trash' => __( 'No videos found in trash', 'wpst' ),
                'all_items' => __( 'All Videos', 'wpst' ),
                'menu_name' => __( 'Videos', 'wpst' ),
                'featured_image' => __( 'Video Thumbnail', 'wpst' ),
                'set_featured_image' => __( 'Set video thumbnail', 'wpst' ),
                'remove_featured_image' => __( 'Remove video thumbnail', 'wpst' ),
                'use_featured_image' => __( 'Use as video thumbnail', 'wpst' ),
            ),
            'public' => true,
            'has_archive' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'video',
                'with_front' => false,
                'feeds' => true,
            ),
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-video-alt3',
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'author',
                'thumbnail',
                'comments',
                'custom-fields',
                'revisions',
                'post-formats'
            ),
            'taxonomies' => array( 'video_category', 'video_tag', 'actor' ),
            'show_in_rest' => true,
            'rest_base' => 'videos',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ) );
        
        // Actor Post Type
        register_post_type( 'actor', array(
            'labels' => array(
                'name' => __( 'Actors', 'wpst' ),
                'singular_name' => __( 'Actor', 'wpst' ),
                'add_new' => __( 'Add New Actor', 'wpst' ),
                'add_new_item' => __( 'Add New Actor', 'wpst' ),
                'edit_item' => __( 'Edit Actor', 'wpst' ),
                'new_item' => __( 'New Actor', 'wpst' ),
                'view_item' => __( 'View Actor', 'wpst' ),
                'search_items' => __( 'Search Actors', 'wpst' ),
                'not_found' => __( 'No actors found', 'wpst' ),
                'not_found_in_trash' => __( 'No actors found in trash', 'wpst' ),
                'all_items' => __( 'All Actors', 'wpst' ),
                'menu_name' => __( 'Actors', 'wpst' ),
                'featured_image' => __( 'Actor Photo', 'wpst' ),
                'set_featured_image' => __( 'Set actor photo', 'wpst' ),
                'remove_featured_image' => __( 'Remove actor photo', 'wpst' ),
                'use_featured_image' => __( 'Use as actor photo', 'wpst' ),
            ),
            'public' => true,
            'has_archive' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'actor',
                'with_front' => false,
                'feeds' => true,
            ),
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => 6,
            'menu_icon' => 'dashicons-groups',
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'author',
                'thumbnail',
                'comments',
                'custom-fields',
                'revisions'
            ),
            'show_in_rest' => true,
            'rest_base' => 'actors',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ) );
        
        // Playlist Post Type
        register_post_type( 'playlist', array(
            'labels' => array(
                'name' => __( 'Playlists', 'wpst' ),
                'singular_name' => __( 'Playlist', 'wpst' ),
                'add_new' => __( 'Add New Playlist', 'wpst' ),
                'add_new_item' => __( 'Add New Playlist', 'wpst' ),
                'edit_item' => __( 'Edit Playlist', 'wpst' ),
                'new_item' => __( 'New Playlist', 'wpst' ),
                'view_item' => __( 'View Playlist', 'wpst' ),
                'search_items' => __( 'Search Playlists', 'wpst' ),
                'not_found' => __( 'No playlists found', 'wpst' ),
                'not_found_in_trash' => __( 'No playlists found in trash', 'wpst' ),
                'all_items' => __( 'All Playlists', 'wpst' ),
                'menu_name' => __( 'Playlists', 'wpst' ),
            ),
            'public' => true,
            'has_archive' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'playlist',
                'with_front' => false,
                'feeds' => true,
            ),
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => 7,
            'menu_icon' => 'dashicons-playlist-audio',
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'author',
                'thumbnail',
                'comments',
                'custom-fields',
                'revisions'
            ),
            'show_in_rest' => true,
            'rest_base' => 'playlists',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ) );
        
        flush_rewrite_rules();
    }
    
    /**
     * Register taxonomies
     */
    public function register_taxonomies(): void {
        // Video Categories
        register_taxonomy( 'video_category', array( 'video' ), array(
            'labels' => array(
                'name' => __( 'Video Categories', 'wpst' ),
                'singular_name' => __( 'Video Category', 'wpst' ),
                'search_items' => __( 'Search video categories', 'wpst' ),
                'all_items' => __( 'All video categories', 'wpst' ),
                'parent_item' => __( 'Parent video category', 'wpst' ),
                'parent_item_colon' => __( 'Parent video category:', 'wpst' ),
                'edit_item' => __( 'Edit video category', 'wpst' ),
                'update_item' => __( 'Update video category', 'wpst' ),
                'add_new_item' => __( 'Add new video category', 'wpst' ),
                'new_item_name' => __( 'New video category name', 'wpst' ),
                'menu_name' => __( 'Categories', 'wpst' ),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'video-category',
                'hierarchical' => true,
                'with_front' => false,
            ),
            'show_in_rest' => true,
            'rest_base' => 'video_categories',
            'rest_controller_class' => 'WP_REST_Terms_Controller',
        ) );
        
        // Video Tags
        register_taxonomy( 'video_tag', array( 'video' ), array(
            'labels' => array(
                'name' => __( 'Video Tags', 'wpst' ),
                'singular_name' => __( 'Video Tag', 'wpst' ),
                'search_items' => __( 'Search video tags', 'wpst' ),
                'all_items' => __( 'All video tags', 'wpst' ),
                'edit_item' => __( 'Edit video tag', 'wpst' ),
                'update_item' => __( 'Update video tag', 'wpst' ),
                'add_new_item' => __( 'Add new video tag', 'wpst' ),
                'new_item_name' => __( 'New video tag name', 'wpst' ),
                'menu_name' => __( 'Tags', 'wpst' ),
                'popular_items' => __( 'Popular video tags', 'wpst' ),
            ),
            'hierarchical' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'video-tag',
                'hierarchical' => false,
                'with_front' => false,
            ),
            'show_in_rest' => true,
            'rest_base' => 'video_tags',
            'rest_controller_class' => 'WP_REST_Terms_Controller',
        ) );
        
        // Actors (as taxonomy for videos)
        register_taxonomy( 'actor', array( 'video' ), array(
            'labels' => array(
                'name' => __( 'Actors', 'wpst' ),
                'singular_name' => __( 'Actor', 'wpst' ),
                'search_items' => __( 'Search actors', 'wpst' ),
                'all_items' => __( 'All actors', 'wpst' ),
                'edit_item' => __( 'Edit actor', 'wpst' ),
                'update_item' => __( 'Update actor', 'wpst' ),
                'add_new_item' => __( 'Add new actor', 'wpst' ),
                'new_item_name' => __( 'New actor name', 'wpst' ),
                'menu_name' => __( 'Actors', 'wpst' ),
                'popular_items' => __( 'Popular actors', 'wpst' ),
            ),
            'hierarchical' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'actor',
                'hierarchical' => false,
                'with_front' => false,
            ),
            'show_in_rest' => true,
            'rest_base' => 'actors',
            'rest_controller_class' => 'WP_REST_Terms_Controller',
        ) );
        
        // Video Quality
        register_taxonomy( 'video_quality', array( 'video' ), array(
            'labels' => array(
                'name' => __( 'Video Qualities', 'wpst' ),
                'singular_name' => __( 'Video Quality', 'wpst' ),
                'search_items' => __( 'Search video qualities', 'wpst' ),
                'all_items' => __( 'All video qualities', 'wpst' ),
                'edit_item' => __( 'Edit video quality', 'wpst' ),
                'update_item' => __( 'Update video quality', 'wpst' ),
                'add_new_item' => __( 'Add new video quality', 'wpst' ),
                'new_item_name' => __( 'New video quality name', 'wpst' ),
                'menu_name' => __( 'Qualities', 'wpst' ),
            ),
            'hierarchical' => false,
            'show_ui' => false,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => false,
            'show_in_rest' => true,
            'rest_base' => 'video_qualities',
        ) );
    }
    
    /**
     * Handle video rewrite rules
     */
    public function handle_video_rewrite_rules(): void {
        // Custom rewrite rules for video archives
        add_rewrite_rule(
            '^video/([^/]+)/?$',
            'index.php?video_category=$matches[1]',
            'top'
        );
        
        add_rewrite_rule(
            '^actor/([^/]+)/?$',
            'index.php?actor=$matches[1]',
            'top'
        );
        
        add_rewrite_rule(
            '^tag/([^/]+)/?$',
            'index.php?video_tag=$matches[1]',
            'top'
        );
    }
    
    /**
     * Video template include
     */
    public function video_template_include( string $template ): string {
        if ( is_post_type_archive( 'video' ) ) {
            $new_template = locate_template( array( 'archive-video.php', 'archive.php' ) );
            if ( $new_template ) {
                return $new_template;
            }
        }
        
        if ( is_singular( 'video' ) ) {
            $new_template = locate_template( array( 'single-video.php', 'single.php' ) );
            if ( $new_template ) {
                return $new_template;
            }
        }
        
        if ( is_post_type_archive( 'actor' ) ) {
            $new_template = locate_template( array( 'archive-actor.php', 'archive.php' ) );
            if ( $new_template ) {
                return $new_template;
            }
        }
        
        if ( is_singular( 'actor' ) ) {
            $new_template = locate_template( array( 'single-actor.php', 'single.php' ) );
            if ( $new_template ) {
                return $new_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Add video meta boxes
     */
    public function add_video_meta_boxes(): void {
        add_meta_box(
            'video_details',
            __( 'Video Details', 'wpst' ),
            array( $this, 'video_details_meta_box' ),
            'video',
            'normal',
            'high'
        );
        
        add_meta_box(
            'video_stats',
            __( 'Video Statistics', 'wpst' ),
            array( $this, 'video_stats_meta_box' ),
            'video',
            'side',
            'default'
        );
        
        add_meta_box(
            'actor_details',
            __( 'Actor Details', 'wpst' ),
            array( $this, 'actor_details_meta_box' ),
            'actor',
            'normal',
            'high'
        );
    }
    
    /**
     * Video details meta box
     */
    public function video_details_meta_box( WP_Post $post ): void {
        wp_nonce_field( 'xtube_save_video_meta', 'xtube_video_meta_nonce' );
        
        $video_url = get_post_meta( $post->ID, 'video_url', true );
        $duration = get_post_meta( $post->ID, 'duration', true );
        $quality = get_post_meta( $post->ID, 'video_quality', true );
        $is_portrait = get_post_meta( $post->ID, 'is_portrait', false );
        $download_url = get_post_meta( $post->ID, 'download_url', true );
        $trailer_url = get_post_meta( $post->ID, 'trailer_url', true );
        
        ?>
        <div class="xtube-meta-fields">
            <div class="form-field">
                <label for="video_url"><?php esc_html_e( 'Video URL', 'wpst' ); ?></label>
                <input type="url" id="video_url" name="video_url" value="<?php echo esc_url( $video_url ); ?>" class="widefat">
                <p class="description"><?php esc_html_e( 'Enter the full URL to the video file', 'wpst' ); ?></p>
            </div>
            
            <div class="form-field">
                <label for="duration"><?php esc_html_e( 'Duration (seconds)', 'wpst' ); ?></label>
                <input type="number" id="duration" name="duration" value="<?php echo esc_attr( $duration ); ?>" class="small-text">
                <p class="description"><?php esc_html_e( 'Video duration in seconds', 'wpst' ); ?></p>
            </div>
            
            <div class="form-field">
                <label for="quality"><?php esc_html_e( 'Video Quality', 'wpst' ); ?></label>
                <select id="quality" name="video_quality" class="small-text">
                    <option value=""><?php esc_html_e( 'Select quality', 'wpst' ); ?></option>
                    <option value="720p" <?php selected( $quality, '720p' ); ?>>720p HD</option>
                    <option value="1080p" <?php selected( $quality, '1080p' ); ?>>1080p Full HD</option>
                    <option value="4k" <?php selected( $quality, '4k' ); ?>>4K Ultra HD</option>
                </select>
            </div>
            
            <div class="form-field">
                <label>
                    <input type="checkbox" name="is_portrait" value="1" <?php checked( $is_portrait ); ?>>
                    <?php esc_html_e( 'Portrait video (vertical)', 'wpst' ); ?>
                </label>
            </div>
            
            <div class="form-field">
                <label for="trailer_url"><?php esc_html_e( 'Trailer URL', 'wpst' ); ?></label>
                <input type="url" id="trailer_url" name="trailer_url" value="<?php echo esc_url( $trailer_url ); ?>" class="widefat">
                <p class="description"><?php esc_html_e( 'Optional trailer URL', 'wpst' ); ?></p>
            </div>
            
            <div class="form-field">
                <label for="download_url"><?php esc_html_e( 'Download URL', 'wpst' ); ?></label>
                <input type="url" id="download_url" name="download_url" value="<?php echo esc_url( $download_url ); ?>" class="widefat">
                <p class="description"><?php esc_html_e( 'Optional download URL', 'wpst' ); ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Video stats meta box
     */
    public function video_stats_meta_box( WP_Post $post ): void {
        $views = get_post_meta( $post->ID, 'post_views_count', true ) ?: 0;
        $likes = get_post_meta( $post->ID, 'likes_count', true ) ?: 0;
        $rating = get_post_meta( $post->ID, 'average_rating', true ) ?: 0;
        
        ?>
        <div class="video-stats">
            <p><strong><?php esc_html_e( 'Views:', 'wpst' ); ?></strong> <?php echo number_format_i18n( $views ); ?></p>
            <p><strong><?php esc_html_e( 'Likes:', 'wpst' ); ?></strong> <?php echo number_format_i18n( $likes ); ?></p>
            <p><strong><?php esc_html_e( 'Rating:', 'wpst' ); ?></strong> <?php echo esc_html( $rating ); ?>/5</p>
        </div>
        <?php
    }
    
    /**
     * Actor details meta box
     */
    public function actor_details_meta_box( WP_Post $post ): void {
        wp_nonce_field( 'xtube_save_actor_meta', 'xtube_actor_meta_nonce' );
        
        $birth_date = get_post_meta( $post->ID, 'birth_date', true );
        $nationality = get_post_meta( $post->ID, 'nationality', true );
        $bio = get_post_meta( $post->ID, 'actor_bio', true );
        
        ?>
        <div class="xtube-meta-fields">
            <div class="form-field">
                <label for="birth_date"><?php esc_html_e( 'Birth Date', 'wpst' ); ?></label>
                <input type="date" id="birth_date" name="birth_date" value="<?php echo esc_attr( $birth_date ); ?>" class="regular-text">
            </div>
            
            <div class="form-field">
                <label for="nationality"><?php esc_html_e( 'Nationality', 'wpst' ); ?></label>
                <input type="text" id="nationality" name="nationality" value="<?php echo esc_attr( $nationality ); ?>" class="regular-text">
            </div>
            
            <div class="form-field">
                <label for="actor_bio"><?php esc_html_e( 'Biography', 'wpst' ); ?></label>
                <textarea id="actor_bio" name="actor_bio" rows="6" class="large-text"><?php echo esc_textarea( $bio ); ?></textarea>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save video meta
     */
    public function save_video_meta( int $post_id, WP_Post $post ): void {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        if ( $post->post_type === 'video' ) {
            if ( isset( $_POST['xtube_video_meta_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['xtube_video_meta_nonce'] ), 'xtube_save_video_meta' ) ) {
                $fields = array( 'video_url', 'duration', 'video_quality', 'download_url', 'trailer_url' );
                
                foreach ( $fields as $field ) {
                    if ( isset( $_POST[$field] ) ) {
                        $value = sanitize_text_field( wp_unslash( $_POST[$field] ) );
                        update_post_meta( $post_id, $field, $value );
                    }
                }
                
                $is_portrait = isset( $_POST['is_portrait'] ) ? 1 : 0;
                update_post_meta( $post_id, 'is_portrait', $is_portrait );
            }
        }
        
        if ( $post->post_type === 'actor' ) {
            if ( isset( $_POST['xtube_actor_meta_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['xtube_actor_meta_nonce'] ), 'xtube_save_actor_meta' ) ) {
                $fields = array( 'birth_date', 'nationality', 'actor_bio' );
                
                foreach ( $fields as $field ) {
                    if ( isset( $_POST[$field] ) ) {
                        $value = sanitize_text_field( wp_unslash( $_POST[$field] ) );
                        update_post_meta( $post_id, $field, $value );
                    }
                }
            }
        }
    }
    
    /**
     * Video admin columns
     */
    public function video_admin_columns( array $columns ): array {
        $columns['duration'] = __( 'Duration', 'wpst' );
        $columns['quality'] = __( 'Quality', 'wpst' );
        $columns['views'] = __( 'Views', 'wpst' );
        $columns['rating'] = __( 'Rating', 'wpst' );
        
        // Move date to the end
        $date = $columns['date'];
        unset( $columns['date'] );
        $columns['date'] = $date;
        
        return $columns;
    }
    
    /**
     * Video admin column data
     */
    public function video_admin_column_data( string $column, int $post_id ): void {
        switch ( $column ) {
            case 'duration':
                $duration = get_post_meta( $post_id, 'duration', true );
                if ( $duration ) {
                    $minutes = floor( $duration / 60 );
                    $seconds = $duration % 60;
                    echo esc_html( sprintf( '%d:%02d', $minutes, $seconds ) );
                }
                break;
                
            case 'quality':
                $quality = get_post_meta( $post_id, 'video_quality', true );
                echo esc_html( $quality ?: '-' );
                break;
                
            case 'views':
                $views = get_post_meta( $post_id, 'post_views_count', true ) ?: 0;
                echo number_format_i18n( $views );
                break;
                
            case 'rating':
                $rating = get_post_meta( $post_id, 'average_rating', true ) ?: 0;
                echo esc_html( $rating ) . '/5';
                break;
        }
    }
    
    /**
     * Register REST fields
     */
    public function register_rest_fields(): void {
        register_rest_field( 'video', 'video_meta', array(
            'get_callback' => array( $this, 'get_video_meta_field' ),
            'update_callback' => array( $this, 'update_video_meta_field' ),
            'schema' => array(
                'type' => 'object',
                'properties' => array(
                    'video_url' => array( 'type' => 'string' ),
                    'duration' => array( 'type' => 'integer' ),
                    'video_quality' => array( 'type' => 'string' ),
                    'is_portrait' => array( 'type' => 'boolean' ),
                    'views' => array( 'type' => 'integer' ),
                    'rating' => array( 'type' => 'number' ),
                ),
            ),
        ) );
    }
    
    /**
     * Get video meta field
     */
    public function get_video_meta_field( array $object ): array {
        $post_id = $object['id'];
        
        return array(
            'video_url' => get_post_meta( $post_id, 'video_url', true ),
            'duration' => intval( get_post_meta( $post_id, 'duration', true ) ),
            'video_quality' => get_post_meta( $post_id, 'video_quality', true ),
            'is_portrait' => (bool) get_post_meta( $post_id, 'is_portrait', true ),
            'views' => intval( get_post_meta( $post_id, 'post_views_count', true ) ?: 0 ),
            'rating' => floatval( get_post_meta( $post_id, 'average_rating', true ) ?: 0 ),
        );
    }
    
    /**
     * Update video meta field
     */
    public function update_video_meta_field( string $value, WP_Post $object ): void {
        $meta_array = json_decode( $value, true );
        
        if ( is_array( $meta_array ) ) {
            foreach ( $meta_array as $key => $val ) {
                update_post_meta( $object->ID, $key, $val );
            }
        }
    }
}

// Initialize the video post types
XTube_Video_Post_Types::get_instance();
