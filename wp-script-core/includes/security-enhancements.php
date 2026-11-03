<?php
/**
 * XTube Toolkit - Security Enhancements v3.0
 * Comprehensive security features and sanitization
 * 
 * @package XTubeToolkit
 * @author  Saieed Rahman
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ============================================================
   SECURITY ENHANCEMENTS CLASS
   ============================================================
 */
class XTube_Security {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Initialize security features
        $this->init_security_headers();
        $this->init_input_sanitization();
        $this->init_ajax_security();
        $this->init_file_upload_security();
        $this->init_user_security();
        $this->init_content_security();
        $this->init_rate_limiting();
        $this->init_audit_logging();
        
        // Admin security
        add_action( 'admin_init', array( $this, 'admin_security_init' ) );
        
        // REST API security
        add_filter( 'rest_authentication_errors', array( $this, 'rest_api_security' ) );
        
        // Login security
        add_filter( 'authenticate', array( $this, 'enhanced_login_security' ), 20, 3 );
        add_action( 'wp_login_failed', array( $this, 'login_failed_attempt' ), 10, 2 );
        
        // Comment security
        add_filter( 'preprocess_comment', array( $this, 'secure_comment_submission' ), 10, 1 );
        
        // Plugin security
        add_filter( 'plugin_row_meta', array( $this, 'hide_plugin_details' ), 10, 2 );
    }
    
    /**
     * Initialize security headers
     */
    private function init_security_headers(): void {
        add_action( 'send_headers', array( $this, 'add_security_headers' ) );
    }
    
    /**
     * Add security headers
     */
    public function add_security_headers(): void {
        if ( ! headers_sent() ) {
            // Content Security Policy
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google-analytics.com https://www.googletagmanager.com; " .
                   "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
                   "font-src 'self' https://fonts.gstatic.com; " .
                   "img-src 'self' data: https:; " .
                   "media-src 'self' https:; " .
                   "connect-src 'self' https://api.example.com; " .
                   "frame-src 'self'; " .
                   "object-src 'none'; " .
                   "base-uri 'self'; " .
                   "form-action 'self';";
            
            header( "Content-Security-Policy: $csp" );
            
            // X-Frame-Options
            header( 'X-Frame-Options: SAMEORIGIN' );
            
            // X-Content-Type-Options
            header( 'X-Content-Type-Options: nosniff' );
            
            // X-XSS-Protection
            header( 'X-XSS-Protection: 1; mode=block' );
            
            // Referrer Policy
            header( 'Referrer-Policy: strict-origin-when-cross-origin' );
            
            // Permissions Policy
            header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()' );
            
            // Strict Transport Security (HTTPS only)
            if ( is_ssl() ) {
                header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );
            }
        }
    }
    
    /**
     * Initialize input sanitization
     */
    private function init_input_sanitization(): void {
        // Sanitize all user input
        add_filter( 'wp_kses_allowed_html', array( $this, 'custom_kses_allowed_html' ), 10, 2 );
        add_filter( 'sanitize_text_field', array( $this, 'enhanced_text_sanitization' ), 10, 2 );
        
        // Remove malicious scripts from content
        add_filter( 'content_save_pre', array( $this, 'remove_malicious_content' ), 10, 1 );
        add_filter( 'title_save_pre', array( $this, 'remove_malicious_content' ), 10, 1 );
    }
    
    /**
     * Custom KSES allowed HTML
     */
    public function custom_kses_allowed_html( array $tags, string $context ): array {
        if ( 'post' === $context ) {
            // Allow specific HTML5 elements for video content
            $tags['video'] = array(
                'src' => true,
                'poster' => true,
                'width' => true,
                'height' => true,
                'controls' => true,
                'autoplay' => true,
                'loop' => true,
                'muted' => true,
                'preload' => true,
                'class' => true,
                'id' => true,
                'data-*' => true,
            );
            
            $tags['source'] = array(
                'src' => true,
                'type' => true,
                'media' => true,
            );
            
            $tags['track'] = array(
                'src' => true,
                'srclang' => true,
                'label' => true,
                'kind' => true,
                'default' => true,
            );
        }
        
        return $tags;
    }
    
    /**
     * Enhanced text sanitization
     */
    public function enhanced_text_sanitization( string $text ): string {
        // Remove potentially dangerous patterns
        $patterns = array(
            '/javascript:/i',
            '/data:text\/html/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            '/onfocus\s*=/i',
            '/onblur\s*=/i',
            '/onchange\s*=/i',
            '/onsubmit\s*=/i',
        );
        
        foreach ( $patterns as $pattern ) {
            $text = preg_replace( $pattern, '', $text );
        }
        
        return $text;
    }
    
    /**
     * Remove malicious content
     */
    public function remove_malicious_content( string $content ): string {
        // Remove base64 encoded scripts
        $content = preg_replace( '/<script[^>]*>.*?base64.*?<\/script>/is', '', $content );
        
        // Remove iframe and embed tags with suspicious sources
        $content = preg_replace( '/<(iframe|embed)[^>]*src\s*=\s*["\']?(javascript:|data:)/i', '', $content );
        
        // Remove meta refresh tags
        $content = preg_replace( '/<meta[^>]*http-equiv\s*=\s*["\']?refresh["\']?[^>]*>/i', '', $content );
        
        return $content;
    }
    
    /**
     * Initialize AJAX security
     */
    private function init_ajax_security(): void {
        // Verify all AJAX requests
        add_action( 'wp_ajax_xtube_video_analytics', array( $this, 'verify_ajax_nonce' ), 5 );
        add_action( 'wp_ajax_nopriv_xtube_video_analytics', array( $this, 'verify_ajax_nonce' ), 5 );
        add_action( 'wp_ajax_xtube_toggle_dark_mode', array( $this, 'verify_ajax_nonce' ), 5 );
        add_action( 'wp_ajax_nopriv_xtube_toggle_dark_mode', array( $this, 'verify_ajax_nonce' ), 5 );
        
        // Rate limiting for AJAX
        add_action( 'wp_ajax_xtube_video_analytics', array( $this, 'check_ajax_rate_limit' ), 5 );
    }
    
    /**
     * Verify AJAX nonce
     */
    public function verify_ajax_nonce(): void {
        if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['nonce'] ), 'xtube_video_nonce' ) ) {
            wp_die( 'Security check failed' );
        }
    }
    
    /**
     * Check AJAX rate limit
     */
    public function check_ajax_rate_limit(): void {
        $user_id = get_current_user_id();
        $action = sanitize_text_field( $_REQUEST['action'] ?? '' );
        
        if ( ! $user_id ) {
            $ip = $this->get_client_ip();
            $key = 'xtube_rate_limit_' . md5( $ip . $action );
        } else {
            $key = 'xtube_rate_limit_' . $user_id . '_' . $action;
        }
        
        $count = get_transient( $key );
        
        if ( $count === false ) {
            set_transient( $key, 1, 60 ); // 1 minute window
        } elseif ( $count > 30 ) { // 30 requests per minute
            wp_die( 'Rate limit exceeded' );
        } else {
            set_transient( $key, $count + 1, 60 );
        }
    }
    
    /**
     * Initialize file upload security
     */
    private function init_file_upload_security(): void {
        add_filter( 'wp_handle_upload_prefilter', array( $this, 'secure_file_upload' ), 10, 1 );
        add_filter( 'wp_handle_sideload_prefilter', array( $this, 'secure_file_upload' ), 10, 1 );
        
        // Scan uploaded files for malware
        add_filter( 'wp_handle_upload', array( $this, 'scan_uploaded_file' ), 10, 1 );
    }
    
    /**
     * Secure file upload
     */
    public function secure_file_upload( array $file ): array {
        // Check file extension
        $allowed_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'ogg', 'pdf' );
        $file_extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        
        if ( ! in_array( $file_extension, $allowed_extensions, true ) ) {
            $file['error'] = __( 'File type not allowed', 'wpst' );
        }
        
        // Check file size (max 50MB)
        $max_size = 50 * 1024 * 1024; // 50MB in bytes
        if ( $file['size'] > $max_size ) {
            $file['error'] = __( 'File size too large', 'wpst' );
        }
        
        // Validate MIME type
        $allowed_mime_types = array(
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'video/mp4', 'video/webm', 'video/ogg',
            'application/pdf'
        );
        
        if ( isset( $file['type'] ) && ! in_array( $file['type'], $allowed_mime_types, true ) ) {
            $file['error'] = __( 'MIME type not allowed', 'wpst' );
        }
        
        return $file;
    }
    /**
     * Scan uploaded file
     */
    public function scan_uploaded_file( array $upload ): array {
        if ( $upload['error'] !== UPLOAD_ERR_OK ) {
            return $upload;
        }
        
        $file_path = $upload['file'];
        
        // Check for PHP tags in image files
        if ( $this->is_image_file( $file_path ) ) {
            $file_content = file_get_contents( $file_path );
            
            if ( strpos( $file_content, '<?php' ) !== false ) {
                unlink( $file_path );
                $upload['error'] = __( 'Malicious file detected', 'wpst' );
                return $upload;
            }
        }
        
        // Log file upload for audit
        $this->log_security_event( 'file_upload', array(
            'file' => $upload['file'],
            'type' => $upload['type'],
            'size' => $upload['size'],
            'user' => get_current_user_id(),
            'ip' => $this->get_client_ip(),
        ) );
        
        return $upload;
    }
    
    /**
     * Initialize user security
     */
    private function init_user_security(): void {
        // Force strong passwords
        add_filter( 'wp_validate_password', array( $this, 'validate_password_strength' ), 10, 2 );
        
        // Limit login attempts
        add_filter( 'wp_authenticate_user', array( $this, 'check_login_attempts' ), 10, 2 );
        
        // Session security
        add_action( 'wp_login', array( $this, 'secure_session_init' ), 10, 2 );
        add_action( 'wp_logout', array( $this, 'destroy_session_data' ) );
        
        // User enumeration prevention
        add_action( 'template_redirect', array( $this, 'prevent_user_enumeration' ) );
    }
    
    /**
     * Validate password strength
     */
    public function validate_password_strength( bool $valid, string $password ): bool {
        if ( ! $valid ) {
            return $valid;
        }
        
        // Require at least 8 characters
        if ( strlen( $password ) < 8 ) {
            return new WP_Error( 'password_too_short', __( 'Password must be at least 8 characters long', 'wpst' ) );
        }
        
        // Require mixed case
        if ( ! preg_match( '/[a-z]/', $password ) || ! preg_match( '/[A-Z]/', $password ) ) {
            return new WP_Error( 'password_case', __( 'Password must contain both uppercase and lowercase letters', 'wpst' ) );
        }
        
        // Require numbers
        if ( ! preg_match( '/[0-9]/', $password ) ) {
            return new WP_Error( 'password_numbers', __( 'Password must contain at least one number', 'wpst' ) );
        }
        
        // Require special character
        if ( ! preg_match( '/[^a-zA-Z0-9]/', $password ) ) {
            return new WP_Error( 'password_special', __( 'Password must contain at least one special character', 'wpst' ) );
        }
        
        return $valid;
    }
    
    /**
     * Check login attempts
     */
    public function check_login_attempts( WP_User $user, string $password ): WP_User {
        $ip = $this->get_client_ip();
        $key = 'login_attempts_' . md5( $ip );
        
        $attempts = get_transient( $key );
        
        if ( $attempts !== false && $attempts >= 5 ) {
            $remaining = get_option( '_transient_timeout_' . $key ) - time();
            return new WP_Error( 'too_many_attempts', sprintf( __( 'Too many login attempts. Please try again in %d minutes.', 'wpst' ), ceil( $remaining / 60 ) ) );
        }
        
        return $user;
    }
    
    /**
     * Secure session initialization
     */
    public function secure_session_init( string $user_login, WP_User $user ): void {
        // Regenerate session ID
        wp_regenerate_session();
        
        // Set secure session cookie
        if ( is_ssl() ) {
            $session_cookie_params = session_get_cookie_params();
            session_set_cookie_params(
                $session_cookie_params['lifetime'],
                $session_cookie_params['path'],
                $session_cookie_params['domain'],
                true, // Secure
                true  // HttpOnly
            );
        }
        
        // Log successful login
        $this->log_security_event( 'user_login', array(
            'user' => $user->ID,
            'ip' => $this->get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ) );
    }
    
    /**
     * Destroy session data on logout
     */
    public function destroy_session_data(): void {
        // Clear session data
        $_SESSION = array();
        
        // Destroy session cookie
        if ( isset( $_COOKIE[session_name()] ) ) {
            setcookie( session_name(), '', time() - 3600, '/' );
        }
        
        // Destroy session
        session_destroy();
    }
    
    /**
     * Prevent user enumeration
     */
    public function prevent_user_enumeration(): void {
        if ( isset( $_REQUEST['author'] ) && is_numeric( $_REQUEST['author'] ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }
    
    /**
     * Initialize content security
     */
    private function init_content_security(): void {
        // Auto-escape in templates
        add_filter( 'the_content', 'wp_kses_post' );
        add_filter( 'the_title', 'esc_html' );
        add_filter( 'the_excerpt', 'wp_kses_post' );
        
        // Prevent XSS in comments
        add_filter( 'comment_text', 'wp_kses_post' );
        add_filter( 'comment_author', 'esc_html' );
        add_filter( 'comment_url', 'esc_url' );
    }
    
    /**
     * Initialize rate limiting
     */
    private function init_rate_limiting(): void {
        // Limit comment submissions
        add_filter( 'preprocess_comment', array( $this, 'rate_limit_comments' ), 10, 1 );
        
        // Limit search requests
        add_action( 'pre_get_posts', array( $this, 'rate_limit_search' ), 10, 1 );
    }
    
    /**
     * Rate limit comments
     */
    public function rate_limit_comments( array $commentdata ): array {
        $user_id = get_current_user_id();
        $ip = $this->get_client_ip();
        
        if ( $user_id ) {
            $key = 'comment_rate_limit_' . $user_id;
        } else {
            $key = 'comment_rate_limit_' . md5( $ip );
        }
        
        $count = get_transient( $key );
        
        if ( $count !== false && $count >= 5 ) {
            wp_die( __( 'You are posting comments too quickly. Please slow down.', 'wpst' ) );
        }
        
        set_transient( $key, ( $count ?: 0 ) + 1, 300 ); // 5 minutes
        
        return $commentdata;
    }
    
    /**
     * Rate limit search
     */
    public function rate_limit_search( WP_Query $query ): void {
        if ( ! $query->is_search() || is_admin() ) {
            return;
        }
        
        $ip = $this->get_client_ip();
        $key = 'search_rate_limit_' . md5( $ip );
        
        $count = get_transient( $key );
        
        if ( $count !== false && $count >= 20 ) {
            wp_die( __( 'You are searching too frequently. Please wait a moment.', 'wpst' ) );
        }
        
        set_transient( $key, ( $count ?: 0 ) + 1, 60 ); // 1 minute
    }
    
    /**
     * Initialize audit logging
     */
    private function init_audit_logging(): void {
        // Log important actions
        add_action( 'wp_login', array( $this, 'log_login' ), 10, 2 );
        add_action( 'wp_logout', array( $this, 'log_logout' ) );
        add_action( 'user_register', array( $this, 'log_user_register' ), 10, 1 );
        add_action( 'deleted_post', array( $this, 'log_post_deletion' ), 10, 2 );
        add_action( 'transition_post_status', array( $this, 'log_post_status_change' ), 10, 3 );
    }
    
    /**
     * Log security event
     */
    private function log_security_event( string $event_type, array $data ): void {
        $log_entry = array(
            'timestamp' => current_time( 'mysql' ),
            'event_type' => $event_type,
            'data' => $data,
            'ip' => $this->get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        );
        
        // Store in database (you might want to create a custom table for this)
        $logs = get_option( 'xtube_security_logs', array() );
        array_unshift( $logs, $log_entry );
        
        // Keep only last 1000 entries
        $logs = array_slice( $logs, 0, 1000 );
        
        update_option( 'xtube_security_logs', $logs );
    }
    
    /**
     * Admin security initialization
     */
    public function admin_security_init(): void {
        // Hide sensitive information from non-admin users
        if ( ! current_user_can( 'manage_options' ) ) {
            remove_action( 'admin_notices', 'update_nag', 3 );
            remove_filter( 'update_footer', 'core_update_footer' );
        }
        
        // Protect admin area
        add_action( 'admin_init', array( $this, 'protect_admin_area' ), 10 );
    }
    
    /**
     * Protect admin area
     */
    public function protect_admin_area(): void {
        // Force SSL for admin area
        if ( ! is_ssl() && get_option( 'xtube_force_admin_ssl' ) ) {
            if ( 0 !== strpos( $_SERVER['REQUEST_URI'], 'http' ) ) {
                wp_redirect( preg_replace( '|^http://|', 'https://', $_SERVER['REQUEST_URI'] ) );
                exit;
            }
        }
    }
    
    /**
     * REST API security
     */
    public function rest_api_security( $access ) {
        // Limit REST API access for non-authenticated users
        if ( ! is_user_logged_in() && ! $this->is_allowed_rest_endpoint() ) {
            return new WP_Error( 'rest_not_logged_in', __( 'You must be logged in to access the REST API.', 'wpst' ), array( 'status' => 401 ) );
        }
        
        return $access;
    }
    
    /**
     * Check if REST endpoint is allowed
     */
    private function is_allowed_rest_endpoint(): bool {
        $allowed_endpoints = array(
            '/wp/v2/posts',
            '/wp/v2/pages',
            '/wp/v2/categories',
            '/wp/v2/tags',
        );
        
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        
        foreach ( $allowed_endpoints as $endpoint ) {
            if ( strpos( $request_uri, $endpoint ) !== false ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Enhanced login security
     */
    public function enhanced_login_security( $user, string $username, string $password ) {
        if ( is_wp_error( $user ) ) {
            return $user;
        }
        
        // Check if account is locked
        if ( $this->is_account_locked( $user->ID ) ) {
            return new WP_Error( 'account_locked', __( 'This account has been temporarily locked due to security concerns.', 'wpst' ) );
        }
        
        return $user;
    }
    
    /**
     * Handle failed login attempt
     */
    public function login_failed_attempt( string $username, WP_Error $error ): void {
        $ip = $this->get_client_ip();
        $key = 'login_attempts_' . md5( $ip );
        
        $attempts = get_transient( $key );
        set_transient( $key, ( $attempts ?: 0 ) + 1, 300 ); // 5 minutes
        
        // Log failed attempt
        $this->log_security_event( 'login_failed', array(
            'username' => $username,
            'ip' => $ip,
            'error' => $error->get_error_message(),
        ) );
    }
    
    /**
     * Secure comment submission
     */
    public function secure_comment_submission( array $commentdata ): array {
        // Check for suspicious patterns
        $suspicious_patterns = array(
            '/\[url\]/i',
            '/\[link\]/i',
            '/http.*http.*http/i',
            '/<script/i',
            '/javascript:/i',
        );
        
        foreach ( $suspicious_patterns as $pattern ) {
            if ( preg_match( $pattern, $commentdata['comment_content'] ) ) {
                wp_die( __( 'Your comment contains suspicious content and has been blocked.', 'wpst' ) );
            }
        }
        
        return $commentdata;
    }
    
    /**
     * Hide plugin details from non-admin users
     */
    public function hide_plugin_details( array $links, string $file ): array {
        if ( ! current_user_can( 'manage_options' ) ) {
            return array();
        }
        
        return $links;
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip(): string {
        $ip_headers = array(
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        );
        
        foreach ( $ip_headers as $header ) {
            if ( ! empty( $_SERVER[$header] ) ) {
                $ip = $_SERVER[$header];
                
                if ( strpos( $ip, ',' ) !== false ) {
                    $ip = trim( explode( ',', $ip )[0] );
                }
                
                if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Check if file is an image
     */
    private function is_image_file( string $file_path ): bool {
        $image_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp' );
        $extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
        
        return in_array( $extension, $image_extensions, true );
    }
    
    /**
     * Check if account is locked
     */
    private function is_account_locked( int $user_id ): bool {
        $locked_until = get_user_meta( $user_id, 'xtube_account_locked_until', true );
        
        if ( $locked_until && time() < $locked_until ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Log login event
     */
    public function log_login( string $user_login, WP_User $user ): void {
        $this->log_security_event( 'user_login', array(
            'user_id' => $user->ID,
            'username' => $user_login,
        ) );
    }
    
    /**
     * Log logout event
     */
    public function log_logout(): void {
        $this->log_security_event( 'user_logout', array(
            'user_id' => get_current_user_id(),
        ) );
    }
    
    /**
     * Log user registration
     */
    public function log_user_register( int $user_id ): void {
        $this->log_security_event( 'user_register', array(
            'user_id' => $user_id,
        ) );
    }
    
    /**
     * Log post deletion
     */
    public function log_post_deletion( int $post_id, WP_Post $post ): void {
        $this->log_security_event( 'post_deleted', array(
            'post_id' => $post_id,
            'post_type' => $post->post_type,
            'post_title' => $post->post_title,
        ) );
    }
    
    /**
     * Log post status change
     */
    public function log_post_status_change( string $new_status, string $old_status, WP_Post $post ): void {
        if ( $new_status !== $old_status ) {
            $this->log_security_event( 'post_status_change', array(
                'post_id' => $post->ID,
                'post_type' => $post->post_type,
                'old_status' => $old_status,
                'new_status' => $new_status,
            ) );
        }
    }
}

// Initialize security enhancements
XTube_Security::get_instance();
