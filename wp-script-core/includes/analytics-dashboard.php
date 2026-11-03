<?php
/**
 * XTube Toolkit - Analytics Dashboard v3.0
 * Comprehensive video analytics and statistics
 * 
 * @package XTubeToolkit
 * @author  Saieed Rahman
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ============================================================
   ANALYTICS DASHBOARD CLASS
   ============================================================
 */
class XTube_Analytics_Dashboard {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'wp_ajax_xtube_get_analytics_data', array( $this, 'get_analytics_data' ) );
        add_action( 'wp_ajax_xtube_export_analytics', array( $this, 'export_analytics' ) );
        
        // Schedule daily analytics update
        add_action( 'wp', array( $this, 'schedule_daily_update' ) );
        add_action( 'xtube_update_daily_analytics', array( $this, 'update_daily_analytics' ) );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu(): void {
        add_menu_page(
            __( 'XTube Analytics', 'wpst' ),
            __( 'Analytics', 'wpst' ),
            'manage_options',
            'xtube-analytics',
            array( $this, 'render_dashboard' ),
            'dashicons-chart-bar',
            25
        );
        
        add_submenu_page(
            'xtube-analytics',
            __( 'Overview', 'wpst' ),
            __( 'Overview', 'wpst' ),
            'manage_options',
            'xtube-analytics',
            array( $this, 'render_dashboard' )
        );
        
        add_submenu_page(
            'xtube-analytics',
            __( 'Video Stats', 'wpst' ),
            __( 'Video Stats', 'wpst' ),
            'manage_options',
            'xtube-video-stats',
            array( $this, 'render_video_stats' )
        );
        
        add_submenu_page(
            'xtube-analytics',
            __( 'User Analytics', 'wpst' ),
            __( 'User Analytics', 'wpst' ),
            'manage_options',
            'xtube-user-analytics',
            array( $this, 'render_user_analytics' )
        );
        
        add_submenu_page(
            'xtube-analytics',
            __( 'Export Data', 'wpst' ),
            __( 'Export Data', 'wpst' ),
            'manage_options',
            'xtube-export-analytics',
            array( $this, 'render_export_page' )
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts( string $hook ): void {
        if ( strpos( $hook, 'xtube-analytics' ) !== false ) {
            wp_enqueue_style(
                'xtube-analytics',
                plugin_dir_url( __FILE__ ) . '../assets/css/analytics.css',
                array(),
                '3.0.0'
            );
            
            wp_enqueue_script(
                'chart-js',
                'https://cdn.jsdelivr.net/npm/chart.js',
                array(),
                '4.4.0',
                true
            );
            
            wp_enqueue_script(
                'xtube-analytics',
                plugin_dir_url( __FILE__ ) . '../assets/js/analytics.js',
                array( 'jquery', 'chart-js' ),
                '3.0.0',
                true
            );
            
            wp_localize_script(
                'xtube-analytics',
                'xtube_analytics_vars',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'xtube_analytics_nonce' ),
                    'currency' => get_option( 'xtube_currency', 'USD' ),
                    'date_format' => get_option( 'date_format' ),
                )
            );
        }
    }
    
    /**
     * Render main dashboard
     */
    public function render_dashboard(): void {
        ?>
        <div class="wrap xtube-analytics">
            <h1><?php esc_html_e( 'XTube Analytics Dashboard', 'wpst' ); ?></h1>
            
            <div class="analytics-filters">
                <select id="date-range" class="regular-text">
                    <option value="7"><?php esc_html_e( 'Last 7 days', 'wpst' ); ?></option>
                    <option value="30" selected><?php esc_html_e( 'Last 30 days', 'wpst' ); ?></option>
                    <option value="90"><?php esc_html_e( 'Last 90 days', 'wpst' ); ?></option>
                    <option value="365"><?php esc_html_e( 'Last year', 'wpst' ); ?></option>
                </select>
                
                <button type="button" id="refresh-data" class="button button-secondary">
                    <span class="dashicons dashicons-update"></span>
                    <?php esc_html_e( 'Refresh', 'wpst' ); ?>
                </button>
            </div>
            
            <div class="analytics-overview">
                <div class="stat-card">
                    <div class="stat-number" id="total-views">-</div>
                    <div class="stat-label"><?php esc_html_e( 'Total Views', 'wpst' ); ?></div>
                    <div class="stat-change" id="views-change">-</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number" id="total-videos">-</div>
                    <div class="stat-label"><?php esc_html_e( 'Total Videos', 'wpst' ); ?></div>
                    <div class="stat-change" id="videos-change">-</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number" id="total-users">-</div>
                    <div class="stat-label"><?php esc_html_e( 'Active Users', 'wpst' ); ?></div>
                    <div class="stat-change" id="users-change">-</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number" id="avg-watch-time">-</div>
                    <div class="stat-label"><?php esc_html_e( 'Avg. Watch Time', 'wpst' ); ?></div>
                    <div class="stat-change" id="watch-time-change">-</div>
                </div>
            </div>
            
            <div class="analytics-charts">
                <div class="chart-container">
                    <h3><?php esc_html_e( 'Views Over Time', 'wpst' ); ?></h3>
                    <canvas id="views-chart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3><?php esc_html_e( 'Popular Videos', 'wpst' ); ?></h3>
                    <canvas id="popular-videos-chart"></canvas>
                </div>
            </div>
            
            <div class="analytics-details">
                <div class="detail-section">
                    <h3><?php esc_html_e( 'Recent Activity', 'wpst' ); ?></h3>
                    <div id="recent-activity" class="activity-list">
                        <div class="loading"><?php esc_html_e( 'Loading...', 'wpst' ); ?></div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h3><?php esc_html_e( 'Top Categories', 'wpst' ); ?></h3>
                    <div id="top-categories" class="category-list">
                        <div class="loading"><?php esc_html_e( 'Loading...', 'wpst' ); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render video stats page
     */
    public function render_video_stats(): void {
        ?>
        <div class="wrap xtube-analytics">
            <h1><?php esc_html_e( 'Video Statistics', 'wpst' ); ?></h1>
            
            <div class="video-stats-filters">
                <input type="text" id="video-search" placeholder="<?php esc_attr_e( 'Search videos...', 'wpst' ); ?>" class="regular-text">
                <select id="category-filter" class="regular-text">
                    <option value=""><?php esc_html_e( 'All Categories', 'wpst' ); ?></option>
                    <?php
                    $categories = get_terms( array( 'taxonomy' => 'video_category', 'hide_empty' => true ) );
                    foreach ( $categories as $category ) {
                        echo '<option value="' . esc_attr( $category->term_id ) . '">' . esc_html( $category->name ) . '</option>';
                    }
                    ?>
                </select>
                <select id="sort-by" class="regular-text">
                    <option value="views"><?php esc_html_e( 'Most Views', 'wpst' ); ?></option>
                    <option value="rating"><?php esc_html_e( 'Highest Rated', 'wpst' ); ?></option>
                    <option value="date"><?php esc_html_e( 'Recently Added', 'wpst' ); ?></option>
                </select>
            </div>
            
            <div class="video-stats-table">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Video', 'wpst' ); ?></th>
                            <th><?php esc_html_e( 'Views', 'wpst' ); ?></th>
                            <th><?php esc_html_e( 'Likes', 'wpst' ); ?></th>
                            <th><?php esc_html_e( 'Rating', 'wpst' ); ?></th>
                            <th><?php esc_html_e( 'Watch Time', 'wpst' ); ?></th>
                            <th><?php esc_html_e( 'Date', 'wpst' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'wpst' ); ?></th>
                        </tr>
                    </thead>
                    <tbody id="video-stats-tbody">
                        <tr>
                            <td colspan="7" class="text-center"><?php esc_html_e( 'Loading...', 'wpst' ); ?></td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="pagination-wrapper">
                    <div class="pagination-info"></div>
                    <div class="pagination-controls"></div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render user analytics page
     */
    public function render_user_analytics(): void {
        ?>
        <div class="wrap xtube-analytics">
            <h1><?php esc_html_e( 'User Analytics', 'wpst' ); ?></h1>
            
            <div class="user-overview">
                <div class="user-stat-card">
                    <div class="stat-number" id="total-registered">-</div>
                    <div class="stat-label"><?php esc_html_e( 'Registered Users', 'wpst' ); ?></div>
                </div>
                
                <div class="user-stat-card">
                    <div class="stat-number" id="active-today">-</div>
                    <div class="stat-label"><?php esc_html_e( 'Active Today', 'wpst' ); ?></div>
                </div>
                
                <div class="user-stat-card">
                    <div class="stat-number" id="new-this-month">-</div>
                    <div class="stat-label"><?php esc_html_e( 'New This Month', 'wpst' ); ?></div>
                </div>
                
                <div class="user-stat-card">
                    <div class="stat-number" id="retention-rate">-</div>
                    <div class="stat-label"><?php esc_html_e( 'Retention Rate', 'wpst' ); ?></div>
                </div>
            </div>
            
            <div class="user-charts">
                <div class="chart-container">
                    <h3><?php esc_html_e( 'User Registration Trend', 'wpst' ); ?></h3>
                    <canvas id="user-registration-chart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3><?php esc_html_e( 'User Activity', 'wpst' ); ?></h3>
                    <canvas id="user-activity-chart"></canvas>
                </div>
            </div>
            
            <div class="top-users-section">
                <h3><?php esc_html_e( 'Most Active Users', 'wpst' ); ?></h3>
                <div id="top-users-list">
                    <div class="loading"><?php esc_html_e( 'Loading...', 'wpst' ); ?></div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render export page
     */
    public function render_export_page(): void {
        ?>
        <div class="wrap xtube-analytics">
            <h1><?php esc_html_e( 'Export Analytics Data', 'wpst' ); ?></h1>
            
            <div class="export-options">
                <div class="export-section">
                    <h3><?php esc_html_e( 'Export Video Data', 'wpst' ); ?></h3>
                    <form id="export-videos-form">
                        <p>
                            <label><?php esc_html_e( 'Date Range:', 'wpst' ); ?></label>
                            <input type="date" name="start_date" class="regular-text">
                            <span><?php esc_html_e( 'to', 'wpst' ); ?></span>
                            <input type="date" name="end_date" class="regular-text">
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="include_views" checked>
                                <?php esc_html_e( 'Include view statistics', 'wpst' ); ?>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="include_ratings" checked>
                                <?php esc_html_e( 'Include ratings', 'wpst' ); ?>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="include_comments" checked>
                                <?php esc_html_e( 'Include comments', 'wpst' ); ?>
                            </label>
                        </p>
                        <p>
                            <label for="video-format"><?php esc_html_e( 'Export Format:', 'wpst' ); ?></label>
                            <select name="format" id="video-format">
                                <option value="csv">CSV</option>
                                <option value="xlsx">Excel</option>
                                <option value="json">JSON</option>
                            </select>
                        </p>
                        <button type="submit" class="button button-primary">
                            <?php esc_html_e( 'Export Video Data', 'wpst' ); ?>
                        </button>
                    </form>
                </div>
                
                <div class="export-section">
                    <h3><?php esc_html_e( 'Export User Data', 'wpst' ); ?></h3>
                    <form id="export-users-form">
                        <p>
                            <label><?php esc_html_e( 'Date Range:', 'wpst' ); ?></label>
                            <input type="date" name="start_date" class="regular-text">
                            <span><?php esc_html_e( 'to', 'wpst' ); ?></span>
                            <input type="date" name="end_date" class="regular-text">
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="include_activity" checked>
                                <?php esc_html_e( 'Include user activity', 'wpst' ); ?>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" name="include_preferences">
                                <?php esc_html_e( 'Include user preferences', 'wpst' ); ?>
                            </label>
                        </p>
                        <p>
                            <label for="user-format"><?php esc_html_e( 'Export Format:', 'wpst' ); ?></label>
                            <select name="format" id="user-format">
                                <option value="csv">CSV</option>
                                <option value="xlsx">Excel</option>
                            </select>
                        </p>
                        <button type="submit" class="button button-primary">
                            <?php esc_html_e( 'Export User Data', 'wpst' ); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get analytics data (AJAX)
     */
    public function get_analytics_data(): void {
        check_ajax_referer( 'xtube_analytics_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Insufficient permissions' );
        }
        
        $type = sanitize_text_field( $_POST['data_type'] ?? 'overview' );
        $days = intval( $_POST['days'] ?? 30 );
        
        switch ( $type ) {
            case 'overview':
                $data = $this->get_overview_data( $days );
                break;
            case 'video_stats':
                $data = $this->get_video_stats_data();
                break;
            case 'user_analytics':
                $data = $this->get_user_analytics_data( $days );
                break;
            default:
                $data = array( 'error' => 'Invalid data type' );
        }
        
        wp_send_json_success( $data );
    }
    
    /**
     * Get overview data
     */
    private function get_overview_data( int $days ): array {
        global $wpdb;
        
        $end_date = current_time( 'Y-m-d' );
        $start_date = date( 'Y-m-d', strtotime( "-$days days" ) );
        
        // Get total views
        $total_views = $wpdb->get_var( $wpdb->prepare(
            "SELECT SUM(CAST(meta_value AS UNSIGNED)) FROM $wpdb->postmeta 
             WHERE meta_key = 'post_views_count' AND post_id IN 
             (SELECT ID FROM $wpdb->posts WHERE post_type = 'video' AND post_status = 'publish')"
        ) ) ?: 0;
        
        // Get previous period views for comparison
        $prev_start = date( 'Y-m-d', strtotime( "-" . ($days * 2) . " days" ) );
        $prev_end = date( 'Y-m-d', strtotime( "-$days days" ) );
        
        $prev_views = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $wpdb->postmeta pm
             JOIN $wpdb->posts p ON pm.post_id = p.ID
             WHERE pm.meta_key = 'post_views_count' 
             AND p.post_type = 'video' 
             AND p.post_date BETWEEN %s AND %s",
            $prev_start . ' 00:00:00',
            $prev_end . ' 23:59:59'
        ) ) ?: 0;
        
        $views_change = $prev_views > 0 ? round( (($total_views - $prev_views) / $prev_views) * 100, 2 ) : 0;
        
        // Get total videos
        $total_videos = wp_count_posts( 'video' )->publish;
        
        // Get active users (users who viewed videos in the period)
        $active_users = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM $wpdb->postmeta pm
             JOIN $wpdb->posts p ON pm.post_id = p.ID
             WHERE pm.meta_key = 'post_views_count'
             AND p.post_type = 'video'
             AND p.post_date BETWEEN %s AND %s",
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ) ) ?: 0;
        
        // Get average watch time (simulated data)
        $avg_watch_time = $this->calculate_average_watch_time( $days );
        
        // Get daily views for chart
        $daily_views = $wpdb->get_results( $wpdb->prepare(
            "SELECT DATE(p.post_date) as date, COUNT(*) as views
             FROM $wpdb->posts p
             WHERE p.post_type = 'video' 
             AND p.post_status = 'publish'
             AND p.post_date BETWEEN %s AND %s
             GROUP BY DATE(p.post_date)
             ORDER BY date ASC",
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ) );
        
        // Get popular videos
        $popular_videos = $wpdb->get_results(
            "SELECT p.ID, p.post_title, pm.meta_value as views
             FROM $wpdb->posts p
             JOIN $wpdb->postmeta pm ON p.ID = pm.post_id
             WHERE p.post_type = 'video' 
             AND p.post_status = 'publish'
             AND pm.meta_key = 'post_views_count'
             ORDER BY CAST(pm.meta_value AS UNSIGNED) DESC
             LIMIT 10"
        );
        
        // Get top categories
        $top_categories = $wpdb->get_results(
            "SELECT t.name, COUNT(*) as video_count
             FROM $wpdb->term_taxonomy tt
             JOIN $wpdb->terms t ON tt.term_id = t.term_id
             JOIN $wpdb->term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
             JOIN $wpdb->posts p ON tr.object_id = p.ID
             WHERE tt.taxonomy = 'video_category'
             AND p.post_type = 'video'
             AND p.post_status = 'publish'
             GROUP BY t.term_id
             ORDER BY video_count DESC
             LIMIT 10"
        );
        
        return array(
            'total_views' => number_format_i18n( $total_views ),
            'views_change' => $views_change,
            'total_videos' => number_format_i18n( $total_videos ),
            'active_users' => number_format_i18n( $active_users ),
            'avg_watch_time' => $this->format_duration( $avg_watch_time ),
            'daily_views' => $daily_views,
            'popular_videos' => $popular_videos,
            'top_categories' => $top_categories,
        );
    }
    
    /**
     * Get video stats data
     */
    private function get_video_stats_data(): array {
        global $wpdb;
        
        $page = intval( $_POST['page'] ?? 1 );
        $per_page = 20;
        $offset = ( $page - 1 ) * $per_page;
        
        $search = sanitize_text_field( $_POST['search'] ?? '' );
        $category = intval( $_POST['category'] ?? 0 );
        $sort = sanitize_text_field( $_POST['sort'] ?? 'views' );
        
        $where_clauses = array( "p.post_type = 'video'", "p.post_status = 'publish'" );
        
        if ( $search ) {
            $where_clauses[] = $wpdb->prepare( "p.post_title LIKE %s", '%' . $wpdb->esc_like( $search ) . '%' );
        }
        
        if ( $category ) {
            $where_clauses[] = $wpdb->prepare( "tr.term_taxonomy_id = %d", $category );
        }
        
        $order_by = match( $sort ) {
            'rating' => 'rating DESC',
            'date' => 'p.post_date DESC',
            default => 'views DESC'
        };
        
        $sql = "SELECT p.ID, p.post_title, p.post_date,
                       COALESCE(CAST(views_meta.meta_value AS UNSIGNED), 0) as views,
                       COALESCE(CAST(likes_meta.meta_value AS UNSIGNED), 0) as likes,
                       COALESCE(CAST(rating_meta.meta_value AS DECIMAL(3,2)), 0) as rating
                FROM $wpdb->posts p
                LEFT JOIN $wpdb->postmeta views_meta ON p.ID = views_meta.post_id AND views_meta.meta_key = 'post_views_count'
                LEFT JOIN $wpdb->postmeta likes_meta ON p.ID = likes_meta.post_id AND likes_meta.meta_key = 'likes_count'
                LEFT JOIN $wpdb->postmeta rating_meta ON p.ID = rating_meta.post_id AND rating_meta.meta_key = 'average_rating'";
        
        if ( $category ) {
            $sql .= " JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id";
        }
        
        $sql .= " WHERE " . implode( ' AND ', $where_clauses );
        $sql .= " ORDER BY $order_by LIMIT %d OFFSET %d";
        
        $videos = $wpdb->get_results( $wpdb->prepare( $sql, $per_page, $offset ) );
        
        // Get total count for pagination
        $count_sql = "SELECT COUNT(*) FROM $wpdb->posts p";
        if ( $category ) {
            $count_sql .= " JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id";
        }
        $count_sql .= " WHERE " . implode( ' AND ', $where_clauses );
        
        $total = $wpdb->get_var( $count_sql );
        
        return array(
            'videos' => $videos,
            'total' => intval( $total ),
            'pages' => ceil( $total / $per_page ),
            'current_page' => $page,
        );
    }
    
    /**
     * Get user analytics data
     */
    private function get_user_analytics_data( int $days ): array {
        global $wpdb;
        
        // Total registered users
        $total_users = count_users();
        $total_registered = $total_users['total_users'];
        
        // Active today
        $active_today = $wpdb->get_var(
            "SELECT COUNT(DISTINCT user_id) 
             FROM $wpdb->usermeta 
             WHERE meta_key = 'last_activity' 
             AND meta_value >= CURDATE()"
        ) ?: 0;
        
        // New this month
        $new_this_month = $wpdb->get_var(
            "SELECT COUNT(*) FROM $wpdb->users 
             WHERE DATE(user_registered) >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        ) ?: 0;
        
        // Calculate retention rate (simplified)
        $retention_rate = $this->calculate_retention_rate();
        
        // User registration trend
        $registration_trend = $wpdb->get_results(
            "SELECT DATE(user_registered) as date, COUNT(*) as users
             FROM $wpdb->users
             WHERE user_registered >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY DATE(user_registered)
             ORDER BY date ASC"
        );
        
        // Most active users
        $top_users = $wpdb->get_results(
            "SELECT u.ID, u.display_name, um.meta_value as activity_count
             FROM $wpdb->users u
             JOIN $wpdb->usermeta um ON u.ID = um.user_id
             WHERE um.meta_key = 'video_views_count'
             ORDER BY CAST(um.meta_value AS UNSIGNED) DESC
             LIMIT 10"
        );
        
        return array(
            'total_registered' => number_format_i18n( $total_registered ),
            'active_today' => number_format_i18n( $active_today ),
            'new_this_month' => number_format_i18n( $new_this_month ),
            'retention_rate' => round( $retention_rate, 2 ) . '%',
            'registration_trend' => $registration_trend,
            'top_users' => $top_users,
        );
    }
    
    /**
     * Export analytics data
     */
    public function export_analytics(): void {
        check_ajax_referer( 'xtube_analytics_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Insufficient permissions' );
        }
        
        $type = sanitize_text_field( $_POST['export_type'] ?? 'videos' );
        $format = sanitize_text_field( $_POST['format'] ?? 'csv' );
        
        switch ( $type ) {
            case 'videos':
                $data = $this->export_video_data();
                $filename = 'video-analytics-' . date( 'Y-m-d' ) . '.' . $format;
                break;
            case 'users':
                $data = $this->export_user_data();
                $filename = 'user-analytics-' . date( 'Y-m-d' ) . '.' . $format;
                break;
            default:
                wp_send_json_error( 'Invalid export type' );
        }
        
        if ( $format === 'csv' ) {
            $this->download_csv( $data, $filename );
        } elseif ( $format === 'json' ) {
            $this->download_json( $data, $filename );
        }
        
        wp_die();
    }
    
    /**
     * Export video data
     */
    private function export_video_data(): array {
        global $wpdb;
        
        $videos = $wpdb->get_results(
            "SELECT p.ID, p.post_title, p.post_date, p.post_content,
                    COALESCE(CAST(views_meta.meta_value AS UNSIGNED), 0) as views,
                    COALESCE(CAST(likes_meta.meta_value AS UNSIGNED), 0) as likes,
                    COALESCE(CAST(rating_meta.meta_value AS DECIMAL(3,2)), 0) as rating
             FROM $wpdb->posts p
             LEFT JOIN $wpdb->postmeta views_meta ON p.ID = views_meta.post_id AND views_meta.meta_key = 'post_views_count'
             LEFT JOIN $wpdb->postmeta likes_meta ON p.ID = likes_meta.post_id AND likes_meta.meta_key = 'likes_count'
             LEFT JOIN $wpdb->postmeta rating_meta ON p.ID = rating_meta.post_id AND rating_meta.meta_key = 'average_rating'
             WHERE p.post_type = 'video' AND p.post_status = 'publish'
             ORDER BY p.post_date DESC"
        );
        
        $data = array();
        $data[] = array( 'ID', 'Title', 'Date', 'Views', 'Likes', 'Rating' );
        
        foreach ( $videos as $video ) {
            $data[] = array(
                $video->ID,
                $video->post_title,
                $video->post_date,
                $video->views,
                $video->likes,
                $video->rating,
            );
        }
        
        return $data;
    }
    
    /**
     * Export user data
     */
    private function export_user_data(): array {
        global $wpdb;
        
        $users = $wpdb->get_results(
            "SELECT u.ID, u.user_login, u.user_email, u.display_name, u.user_registered,
                    COALESCE(CAST(activity_meta.meta_value AS UNSIGNED), 0) as activity_count
             FROM $wpdb->users u
             LEFT JOIN $wpdb->usermeta activity_meta ON u.ID = activity_meta.user_id AND activity_meta.meta_key = 'video_views_count'
             ORDER BY u.user_registered DESC"
        );
        
        $data = array();
        $data[] = array( 'ID', 'Username', 'Email', 'Display Name', 'Registration Date', 'Activity Count' );
        
        foreach ( $users as $user ) {
            $data[] = array(
                $user->ID,
                $user->user_login,
                $user->user_email,
                $user->display_name,
                $user->user_registered,
                $user->activity_count,
            );
        }
        
        return $data;
    }
    
    /**
     * Download CSV file
     */
    private function download_csv( array $data, string $filename ): void {
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        
        $output = fopen( 'php://output', 'w' );
        
        foreach ( $data as $row ) {
            fputcsv( $output, $row );
        }
        
        fclose( $output );
    }
    
    /**
     * Download JSON file
     */
    private function download_json( array $data, string $filename ): void {
        header( 'Content-Type: application/json' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        
        echo json_encode( $data, JSON_PRETTY_PRINT );
    }
    
    /**
     * Schedule daily analytics update
     */
    public function schedule_daily_update(): void {
        if ( ! wp_next_scheduled( 'xtube_update_daily_analytics' ) ) {
            wp_schedule_event( time(), 'daily', 'xtube_update_daily_analytics' );
        }
    }
    
    /**
     * Update daily analytics
     */
    public function update_daily_analytics(): void {
        // This would run daily to update cached analytics data
        // For now, we'll just log that it ran
        error_log( 'XTube Analytics: Daily update completed' );
    }
    
    /**
     * Calculate average watch time (simulated)
     */
    private function calculate_average_watch_time( int $days ): int {
        // This would typically come from actual video watch tracking
        // For now, return a simulated value
        return rand( 300, 1800 ); // 5-30 minutes in seconds
    }
    
    /**
     * Calculate retention rate (simulated)
     */
    private function calculate_retention_rate(): float {
        // This would typically calculate based on user activity over time
        // For now, return a simulated value
        return rand( 60, 85 );
    }
    
    /**
     * Format duration
     */
    private function format_duration( int $seconds ): string {
        $minutes = floor( $seconds / 60 );
        $remaining_seconds = $seconds % 60;
        
        if ( $minutes >= 60 ) {
            $hours = floor( $minutes / 60 );
            $minutes = $minutes % 60;
            return sprintf( '%d:%02d:%02d', $hours, $minutes, $remaining_seconds );
        }
        
        return sprintf( '%d:%02d', $minutes, $remaining_seconds );
    }
}

// Initialize the analytics dashboard
XTube_Analytics_Dashboard::get_instance();
