# XTube Toolkit - Core Plugin v3.0

A comprehensive WordPress plugin designed specifically for the XTube theme, providing advanced video management, analytics, security, and customization features.

## 🚀 Features

### Core Functionality
- **Video Post Types**: Custom post types for videos, actors, and playlists
- **Advanced Analytics**: Comprehensive video statistics and user analytics
- **Security Enhancements**: Multi-layered security with audit logging
- **Performance Optimization**: Database optimization and caching
- **SEO Integration**: Schema markup and structured data
- **Accessibility Support**: WCAG 2.1 AA compliance features

### Video Management
- **Custom Post Types**: Video, Actor, and Playlist post types
- **Metadata Management**: Duration, quality, orientation, and more
- **Taxonomy Support**: Categories, tags, and actors
- **REST API**: Full REST API integration
- **Bulk Operations**: Mass editing and management tools

### Analytics Dashboard
- **Real-time Statistics**: Live view counts and engagement metrics
- **Video Performance**: Detailed video analytics and insights
- **User Analytics**: User behavior and engagement tracking
- **Export Capabilities**: CSV, Excel, and JSON data export
- **Custom Reports**: Configurable reporting periods

### Security Features
- **Input Sanitization**: Comprehensive data validation and sanitization
- **Rate Limiting**: Protection against spam and abuse
- **File Upload Security**: Malware scanning and validation
- **Session Security**: Secure session management
- **Audit Logging**: Comprehensive security event logging

## 📋 Requirements

- **WordPress**: 6.0 or higher
- **PHP**: 8.0 or higher
- **Memory**: 128MB or higher recommended
- **XTube Theme**: v3.0 or higher (recommended)

## 🛠 Installation

### Manual Installation

1. Download the plugin zip file
2. Navigate to **Plugins → Add New → Upload Plugin** in WordPress
3. Upload the `xtube-toolkit.zip` file
4. Activate the plugin

### FTP Installation

1. Extract the plugin files
2. Upload the `wp-script-core` folder to `/wp-content/plugins/`
3. Navigate to **Plugins → Installed Plugins**
4. Activate "XTube Toolkit - Core Plugin"

## ⚙️ Configuration

### After Activation

1. **Analytics Dashboard**: Visit **XTube → Analytics** to view statistics
2. **Security Settings**: Configure security options in **XTube → Security**
3. **Video Settings**: Set up video defaults in **XTube → Settings**

### Recommended Settings

#### Security Configuration
- Enable rate limiting for comments and searches
- Configure file upload restrictions
- Set up login attempt limits
- Enable audit logging

#### Analytics Configuration
- Set up data retention periods
- Configure export formats
- Enable real-time tracking
- Set up email notifications

## 🎯 Usage

### Video Management

#### Creating Videos
```php
// Create a new video programmatically
$video_id = wp_insert_post( array(
    'post_type' => 'video',
    'post_title' => 'Video Title',
    'post_content' => 'Video description',
    'post_status' => 'publish'
) );

// Add video metadata
update_post_meta( $video_id, 'video_url', 'https://example.com/video.mp4' );
update_post_meta( $video_id, 'duration', 300 );
update_post_meta( $video_id, 'video_quality', '1080p' );
```

#### Query Videos
```php
// Get recent videos
$videos = new WP_Query( array(
    'post_type' => 'video',
    'posts_per_page' => 10,
    'orderby' => 'date',
    'order' => 'DESC'
) );

// Get videos by category
$category_videos = new WP_Query( array(
    'post_type' => 'video',
    'tax_query' => array(
        array(
            'taxonomy' => 'video_category',
            'field' => 'slug',
            'terms' => 'category-slug'
        )
    )
) );
```

### Analytics Integration

#### Tracking Events
```php
// Track video view
do_action( 'xtube_track_analytics', 'video_view', array(
    'video_id' => $video_id,
    'user_id' => get_current_user_id(),
    'duration' => 300
) );

// Track user engagement
do_action( 'xtube_track_analytics', 'user_engagement', array(
    'action' => 'like',
    'video_id' => $video_id,
    'user_id' => get_current_user_id()
) );
```

#### Getting Statistics
```php
// Get video statistics
$stats = XTube_Analytics_Dashboard::get_instance()->get_video_stats( $video_id );

// Get overview data
$overview = XTube_Analytics_Dashboard::get_instance()->get_overview_data( 30 );
```

### Security Features

#### Custom Validation
```php
// Add custom validation
add_filter( 'xtube_validate_input', function( $value, $field ) {
    if ( $field === 'custom_field' ) {
        return sanitize_text_field( $value );
    }
    return $value;
}, 10, 2 );
```

#### Rate Limiting
```php
// Check rate limit
if ( ! XTube_Security::get_instance()->check_rate_limit( 'custom_action', 5, 60 ) ) {
    wp_die( 'Rate limit exceeded' );
}
```

## 🔧 Hooks and Filters

### Available Actions

```php
// Video events
do_action( 'xtube_video_created', $video_id );
do_action( 'xtube_video_updated', $video_id );
do_action( 'xtube_video_deleted', $video_id );

// Analytics events
do_action( 'xtube_analytics_tracked', $event_type, $data );
do_action( 'xtube_daily_analytics_update' );

// Security events
do_action( 'xtube_security_event', $event_type, $data );
do_action( 'xtube_login_attempt', $username, $success );
```

### Available Filters

```php
// Modify video data
add_filter( 'xtube_video_data', function( $data, $video_id ) {
    $data['custom_field'] = get_post_meta( $video_id, 'custom_field', true );
    return $data;
}, 10, 2 );

// Analytics data filtering
add_filter( 'xtube_analytics_data', function( $data, $type ) {
    if ( $type === 'video_stats' ) {
        // Modify video statistics
    }
    return $data;
}, 10, 2 );

// Security configuration
add_filter( 'xtube_security_config', function( $config ) {
    $config['rate_limit_comments'] = 10;
    return $config;
} );
```

## 📊 Analytics Dashboard

### Overview Section
- Total views and growth metrics
- Video count and publication trends
- Active users and engagement rates
- Average watch time and retention

### Video Statistics
- Individual video performance
- View counts and engagement metrics
- User demographics and devices
- Geographic distribution

### User Analytics
- Registration trends and activity
- User engagement patterns
- Content preferences
- Retention rates

### Export Features
- CSV export for spreadsheet analysis
- Excel export with formatting
- JSON export for API integration
- Custom date ranges and filters

## 🔒 Security Features

### Input Validation
- All user inputs are sanitized and validated
- Custom validation rules for specific fields
- XSS and SQL injection prevention
- File upload security with malware scanning

### Rate Limiting
- Comment submission limits
- Search request throttling
- Login attempt restrictions
- API request limiting

### Audit Logging
- Comprehensive event logging
- Security incident tracking
- User activity monitoring
- Configurable log retention

### Session Security
- Secure session management
- Session fixation prevention
- Timeout and expiration handling
- Multi-factor authentication support

## 🎨 Customization

### Custom Post Types

#### Video Post Type
```php
// Register custom video fields
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'custom_video_fields', 'Custom Fields', 'custom_video_fields_callback', 'video' );
});

function custom_video_fields_callback( $post ) {
    // Custom field HTML
    wp_nonce_field( 'custom_video_fields', 'custom_video_nonce' );
    $custom_value = get_post_meta( $post->ID, '_custom_field', true );
    echo '<input type="text" name="custom_field" value="' . esc_attr( $custom_value ) . '">';
}
```

#### Actor Post Type
```php
// Add custom actor functionality
add_action( 'save_post_actor', function( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Save custom actor data
    if ( isset( $_POST['actor_bio'] ) ) {
        update_post_meta( $post_id, 'actor_bio', sanitize_textarea_field( $_POST['actor_bio'] ) );
    }
});
```

### Analytics Integration

#### Custom Events
```php
// Track custom events
function track_custom_event( $event_name, $data ) {
    do_action( 'xtube_track_analytics', $event_name, $data );
}

// Example usage
track_custom_event( 'video_share', array(
    'video_id' => get_the_ID(),
    'platform' => 'twitter',
    'user_id' => get_current_user_id()
) );
```

#### Custom Reports
```php
// Add custom analytics reports
add_filter( 'xtube_analytics_reports', function( $reports ) {
    $reports['custom_report'] = array(
        'title' => 'Custom Report',
        'callback' => 'generate_custom_report',
        'capability' => 'manage_options'
    );
    return $reports;
});
```

## 🔧 API Integration

### REST API Endpoints

#### Videos
```
GET /wp-json/wp/v2/videos
GET /wp-json/wp/v2/videos/{id}
POST /wp-json/wp/v2/videos
PUT /wp-json/wp/v2/videos/{id}
DELETE /wp-json/wp/v2/videos/{id}
```

#### Actors
```
GET /wp-json/wp/v2/actors
GET /wp-json/wp/v2/actors/{id}
POST /wp-json/wp/v2/actors
PUT /wp-json/wp/v2/actors/{id}
DELETE /wp-json/wp/v2/actors/{id}
```

#### Analytics
```
GET /wp-json/xtube/v1/analytics/overview
GET /wp-json/xtube/v1/analytics/videos/{id}
POST /wp-json/xtube/v1/analytics/track
```

### Custom Endpoints
```php
// Register custom REST endpoint
add_action( 'rest_api_init', function() {
    register_rest_route( 'xtube/v1', '/custom-data', array(
        'methods' => 'GET',
        'callback' => 'get_custom_data',
        'permission_callback' => function() {
            return current_user_can( 'read' );
        }
    ) );
});

function get_custom_data( WP_REST_Request $request ) {
    return array( 'data' => 'Custom data response' );
}
```

## 🐛 Troubleshooting

### Common Issues

#### Analytics Not Tracking
1. Check if plugin is activated
2. Verify WordPress cron is working
3. Check database tables exist
4. Review error logs

#### Security Issues
1. Check WordPress and PHP versions
2. Verify file permissions
3. Review security logs
4. Test with default theme

#### Performance Issues
1. Check database optimization
2. Review caching configuration
3. Monitor server resources
4. Analyze slow queries

### Debug Mode

Enable debugging:
```php
// In wp-config.php
define( 'WP_DEBUG', true );
define( 'XTUBE_DEBUG', true );
define( 'XTUBE_LOG_QUERIES', true );
```

### Logs and Monitoring

#### Security Logs
```php
// Get security logs
$logs = get_option( 'xtube_security_logs', array() );
foreach ( $logs as $log ) {
    error_log( 'Security Event: ' . print_r( $log, true ) );
}
```

#### Analytics Logs
```php
// Check analytics tracking
if ( defined( 'XTUBE_DEBUG' ) && XTUBE_DEBUG ) {
    error_log( 'Analytics Event: ' . $event_type . ' - ' . json_encode( $data ) );
}
```

## 📈 Performance Optimization

### Database Optimization
- Custom database indexes for video queries
- Optimized analytics data storage
- Automatic cleanup of old data
- Query caching integration

### Caching Support
- Compatible with popular caching plugins
- Object caching for analytics data
- Fragment caching for complex queries
- CDN integration support

### Code Optimization
- Lazy loading for analytics data
- Efficient database queries
- Minimal JavaScript footprint
- Optimized CSS delivery

## 🔮 Future Development

### Planned Features
- **AI Integration**: Automated content tagging
- **Live Streaming**: Real-time video capabilities
- **Monetization**: Payment processing integration
- **Multi-language**: Full translation support
- **Mobile App**: Native mobile applications

### API Expansion
- GraphQL support
- Webhook integration
- Third-party service integrations
- Advanced filtering and search

## 📞 Support

### Documentation
- **Plugin Documentation**: Available in plugin admin
- **Developer Guide**: Comprehensive API documentation
- **Video Tutorials**: Step-by-step video guides
- **Community Forum**: User discussions and support

### Getting Help
1. **Check Documentation**: Review plugin documentation
2. **Search Forums**: Look for similar issues
3. **Create Support Ticket**: Contact with detailed information
4. **Provide Details**: Include WordPress version, PHP version, and errors

## 🤝 Contributing

### Development Setup
1. Clone the repository
2. Install dependencies with Composer
3. Enable debug mode
4. Run tests with PHPUnit

### Code Standards
- Follow WordPress coding standards
- Use PHP 8.0+ features
- Include comprehensive documentation
- Add unit tests for new features

### Pull Requests
1. Fork the repository
2. Create feature branch
3. Make changes with tests
4. Submit pull request with description

## 📄 License

This plugin is licensed under the GNU General Public License v2.0 or later.

## 🎯 Changelog

### Version 3.0.0
- Complete plugin rewrite for WordPress 6.5+
- PHP 8.0 compatibility with modern standards
- Advanced analytics dashboard with real-time statistics
- Comprehensive security enhancements
- Custom post types for videos, actors, and playlists
- REST API integration
- Performance optimizations
- Accessibility improvements

### Version 2.x (Legacy)
- Basic video management
- Limited analytics
- No longer supported

---

**XTube Toolkit v3.0** - The ultimate WordPress video management solution.

For the latest updates and support, visit [https://saieed.unaux.com](https://saieed.unaux.com).
