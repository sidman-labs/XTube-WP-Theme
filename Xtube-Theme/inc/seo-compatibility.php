<?php
/**
 * XTube SEO Compatibility v3.0
 * Full compatibility with Rank Math SEO, Yoast SEO, and All in One SEO.
 * Enhanced with Video Schema, Core Web Vitals optimization, and advanced structured data.
 *
 * @package XTube
 * @author  Saieed Rahman
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ============================================================
 * ADVANCED SEO COMPATIBILITY CLASS
 * ============================================================
 */
class XTube_SEO_Compatibility {
    
    public function __construct() {
        add_action( 'init', array( $this, 'init_seo_features' ) );
    }
    
    public function init_seo_features() {
        // Rank Math compatibility
        add_filter( 'rank_math/frontend/disable_head', '__return_false' );
        add_filter( 'rank_math/sitemap/entry', array( $this, 'add_video_data_to_sitemap' ), 10, 3 );
        
        // Yoast SEO compatibility
        add_action( 'wpseo_head', array( $this, 'add_video_schema_to_yoast' ), 10 );
        add_filter( 'wpseo_schema_graph_pieces', array( $this, 'add_video_schema_piece' ), 11, 2 );
        
        // Core SEO features
        add_action( 'wp_head', array( $this, 'output_enhanced_meta_tags' ), 1 );
        add_action( 'wp_head', array( $this, 'output_video_schema' ), 5 );
        add_filter( 'the_content', array( $this, 'optimize_content_for_seo' ), 10 );
        add_filter( 'wp_title', array( $this, 'optimize_title' ), 10, 2 );
        add_filter( 'document_title_parts', array( $this, 'customize_document_title' ), 10 );
        
        // Core Web Vitals optimization
        add_action( 'wp_head', array( $this, 'add_preconnect_tags' ), 2 );
        add_filter( 'wp_get_attachment_image_attributes', array( $this, 'optimize_image_attributes' ), 10 );
        
        // Structured data breadcrumbs
        add_action( 'wp_head', array( $this, 'output_breadcrumb_schema' ), 8 );
    }
    
    /**
     * Add video data to sitemaps
     */
    public function add_video_data_to_sitemap( $url, $type, $post ) {
        if ( 'post' === $post->post_type ) {
            $duration = get_post_meta( $post->ID, 'duration', true );
            $thumbnail = get_the_post_thumbnail_url( $post->ID, 'large' );
            
            if ( $duration || $thumbnail ) {
                $url['video'] = array();
                if ( $duration ) {
                    $url['video']['duration'] = $this->format_duration_for_sitemap( $duration );
                }
                if ( $thumbnail ) {
                    $url['video']['thumbnail_loc'] = $thumbnail;
                }
            }
        }
        return $url;
    }
    
    /**
     * Format duration for sitemap
     */
    private function format_duration_for_sitemap( $duration ) {
        $hours = floor( $duration / 3600 );
        $minutes = floor( ( $duration % 3600 ) / 60 );
        $seconds = $duration % 60;
        return sprintf( '%02d:%02d:%02d', $hours, $minutes, $seconds );
    }
    
    /**
     * Output enhanced meta tags
     */
    public function output_enhanced_meta_tags() {
        // Skip if SEO plugin is handling this
        if ( $this->seo_plugin_is_active() ) {
            return;
        }
        
        global $post;
        if ( ! isset( $post ) ) return;
        
        // Enhanced meta description
        $description = $this->get_seo_description();
        echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
        
        // Open Graph with video specifics
        $this->output_open_graph_tags();
        
        // Twitter Card with player
        $this->output_twitter_card_tags();
        
        // Additional SEO meta tags
        echo '<meta name="theme-color" content="#e50914">' . "\n";
        echo '<meta name="referrer" content="strict-origin-when-cross-origin">' . "\n";
        echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
        
        // Canonical URL
        if ( is_single() || is_page() ) {
            echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '">' . "\n";
        }
    }
    
    /**
     * Get SEO-optimized description
     */
    private function get_seo_description() {
        global $post;
        
        if ( has_excerpt( $post->ID ) ) {
            return strip_tags( get_the_excerpt( $post->ID ) );
        } elseif ( ! empty( $post->post_content ) ) {
            return wp_trim_words( strip_tags( $post->post_content ), 30 );
        } else {
            return get_the_title( $post->ID ) . ' - ' . get_bloginfo( 'description' );
        }
    }
    
    /**
     * Output Open Graph tags
     */
    private function output_open_graph_tags() {
        global $post;
        
        $title = get_the_title( $post->ID );
        $description = $this->get_seo_description();
        $url = get_permalink( $post->ID );
        $thumbnail = $this->get_video_thumbnail();
        
        echo '<meta property="og:type" content="' . ( is_single() ? 'video.other' : 'website' ) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
        
        if ( $thumbnail ) {
            echo '<meta property="og:image" content="' . esc_url( $thumbnail ) . '">' . "\n";
            echo '<meta property="og:image:secure_url" content="' . esc_url( $thumbnail ) . '">' . "\n";
            echo '<meta property="og:image:type" content="image/jpeg">' . "\n";
            
            $image_size = @getimagesize( $thumbnail );
            if ( $image_size ) {
                echo '<meta property="og:image:width" content="' . $image_size[0] . '">' . "\n";
                echo '<meta property="og:image:height" content="' . $image_size[1] . '">' . "\n";
            }
        }
        
        // Video specific OG tags
        if ( is_single() && 'post' === get_post_type() ) {
            $video_url = get_post_meta( $post->ID, 'video_url', true );
            if ( $video_url ) {
                echo '<meta property="og:video" content="' . esc_url( $video_url ) . '">' . "\n";
                echo '<meta property="og:video:type" content="application/x-shockwave-flash">' . "\n";
                echo '<meta property="og:video:width" content="1280">' . "\n";
                echo '<meta property="og:video:height" content="720">' . "\n";
            }
        }
        
        // Locale
        echo '<meta property="og:locale" content="' . get_locale() . '">' . "\n";
    }
    
    /**
     * Output Twitter Card tags
     */
    private function output_twitter_card_tags() {
        global $post;
        
        $title = get_the_title( $post->ID );
        $description = $this->get_seo_description();
        $thumbnail = $this->get_video_thumbnail();
        
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
        
        if ( $thumbnail ) {
            echo '<meta name="twitter:image" content="' . esc_url( $thumbnail ) . '">' . "\n";
        }
        
        // Twitter site handle (if set in customizer)
        if ( $twitter_handle = get_theme_mod( 'twitter_handle', '' ) ) {
            echo '<meta name="twitter:site" content="@' . esc_attr( ltrim( $twitter_handle, '@' ) ) . '">' . "\n";
        }
    }
    
    /**
     * Get video thumbnail URL
     */
    private function get_video_thumbnail() {
        global $post;
        
        $thumb_id = get_post_thumbnail_id( $post->ID );
        if ( $thumb_id ) {
            return wp_get_attachment_image_url( $thumb_id, 'large' );
        }
        
        // Fallback to custom thumb meta
        $thumb_meta = get_post_meta( $post->ID, 'thumb', true );
        if ( $thumb_meta ) {
            return $thumb_meta;
        }
        
        return '';
    }
    
    /**
     * Output Video Schema
     */
    public function output_video_schema() {
        if ( ! is_single() || 'post' !== get_post_type() ) {
            return;
        }
        
        // Skip if SEO plugin is handling schema
        if ( $this->seo_plugin_is_active() ) {
            return;
        }
        
        global $post;
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => get_the_title(),
            'description' => $this->get_seo_description(),
            'thumbnailUrl' => array( $this->get_video_thumbnail() ),
            'uploadDate' => get_the_date( 'c' ),
            'datePublished' => get_the_date( 'c' ),
            'dateModified' => get_the_modified_date( 'c' ),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta( 'display_name' )
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo( 'name' ),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url()
                )
            ),
            'url' => get_permalink(),
            'embedUrl' => get_permalink()
        );
        
        // Add duration
        $duration = get_post_meta( $post->ID, 'duration', true );
        if ( $duration ) {
            $schema['duration'] = $this->format_duration_for_schema( $duration );
        }
        
        // Add video URL
        $video_url = get_post_meta( $post->ID, 'video_url', true );
        if ( $video_url ) {
            $schema['contentUrl'] = $video_url;
        }
        
        // Add views
        $views = get_post_meta( $post->ID, 'post_views_count', true );
        if ( $views ) {
            $schema['interactionStatistic'] = array(
                '@type' => 'InteractionCounter',
                'interactionType' => 'https://schema.org/WatchAction',
                'userInteractionCount' => intval( $views )
            );
        }
        
        // Add rating
        $rating = get_post_meta( $post->ID, 'rate', true );
        if ( $rating ) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => floatval( $rating ),
                'bestRating' => '5',
                'worstRating' => '1'
            );
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }
    
    /**
     * Format duration for schema (ISO 8601)
     */
    private function format_duration_for_schema( $duration ) {
        if ( ! $duration ) return '';
        
        $hours = floor( $duration / 3600 );
        $minutes = floor( ( $duration % 3600 ) / 60 );
        $seconds = $duration % 60;
        
        return 'PT' . $hours . 'H' . $minutes . 'M' . $seconds . 'S';
    }
    
    /**
     * Output breadcrumb schema
     */
    public function output_breadcrumb_schema() {
        if ( is_front_page() ) return;
        
        $breadcrumbs = array();
        
        // Home
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => 1,
            'name' => __( 'Home', 'wpst' ),
            'item' => home_url( '/' )
        );
        
        $position = 2;
        
        if ( is_category() || is_tag() ) {
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => single_term_title( '', false ),
                'item' => get_current_url()
            );
        } elseif ( is_single() ) {
            // Add category if exists
            $categories = get_the_category();
            if ( $categories && ! is_wp_error( $categories ) ) {
                $breadcrumbs[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $categories[0]->name,
                    'item' => get_category_link( $categories[0]->term_id )
                );
            }
            
            // Current video
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => get_the_title(),
                'item' => get_permalink()
            );
        }
        
        if ( count( $breadcrumbs ) > 1 ) {
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbs
            );
            
            echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
        }
    }
    
    /**
     * Check if SEO plugin is active
     */
    private function seo_plugin_is_active() {
        return function_exists( 'rank_math' ) || 
               class_exists( 'RankMath' ) || 
               class_exists( 'WPSEO_Frontend' ) || 
               class_exists( 'Yoast_SEO' ) || 
               defined( 'AIOSEO_VERSION' );
    }
    
    /**
     * Add preconnect tags for performance
     */
    public function add_preconnect_tags() {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="dns-prefetch" href="//www.google-analytics.com">' . "\n";
        echo '<link rel="dns-prefetch" href="//www.googletagmanager.com">' . "\n";
    }
    
    /**
     * Optimize image attributes for Core Web Vitals
     */
    public function optimize_image_attributes( $attr ) {
        if ( ! isset( $attr['loading'] ) ) {
            $attr['loading'] = 'lazy';
        }
        if ( ! isset( $attr['decoding'] ) ) {
            $attr['decoding'] = 'async';
        }
        return $attr;
    }
    
    /**
     * Optimize content for SEO
     */
    public function optimize_content_for_seo( $content ) {
        if ( is_single() && 'post' === get_post_type() ) {
            // Add semantic HTML5 tags
            $content = '<article class="video-content" itemscope itemtype="https://schema.org/VideoObject">' . $content . '</article>';
            
            // Auto-generate internal links based on tags (limited to avoid over-optimization)
            $tags = wp_get_post_tags( get_the_ID(), array( 'number' => 5 ) );
            if ( $tags ) {
                foreach ( $tags as $tag ) {
                    $tag_link = get_tag_link( $tag->term_id );
                    $content = preg_replace( '/\b(' . preg_quote( $tag->name, '/' ) . ')\b/i', '<a href="' . esc_url( $tag_link ) . '" rel="tag" itemprop="keywords">$1</a>', $content, 1 );
                }
            }
        }
        
        return $content;
    }
    
    /**
     * Optimize title
     */
    public function optimize_title( $title, $sep ) {
        if ( is_single() && 'post' === get_post_type() ) {
            global $post;
            $category = get_the_category( $post->ID );
            if ( $category && ! is_wp_error( $category ) ) {
                $title = $title . ' ' . $sep . ' ' . $category[0]->name;
            }
            
            // Add duration for better CTR
            $duration = get_post_meta( get_the_ID(), 'duration', true );
            if ( $duration ) {
                $minutes = floor( $duration / 60 );
                $seconds = $duration % 60;
                $title .= ' (' . sprintf( '%d:%02d', $minutes, $seconds ) . ')';
            }
        }
        
        return $title;
    }
    
    /**
     * Customize document title
     */
    public function customize_document_title( $title ) {
        if ( is_single() && 'post' === get_post_type() ) {
            $duration = get_post_meta( get_the_ID(), 'duration', true );
            if ( $duration ) {
                $minutes = floor( $duration / 60 );
                $seconds = $duration % 60;
                $title['title'] .= ' (' . sprintf( '%d:%02d', $minutes, $seconds ) . ')';
            }
        }
        
        return $title;
    }
    
    /**
     * Add video schema to Yoast
     */
    public function add_video_schema_to_yoast() {
        if ( is_single() && 'post' === get_post_type() ) {
            $this->output_video_schema();
        }
    }
    
    /**
     * Add video schema piece for Yoast
     */
    public function add_video_schema_piece( $pieces, $context ) {
        if ( is_single() && 'post' === get_post_type() ) {
            $pieces[] = new XTube_Video_Schema_Piece( $context );
        }
        return $pieces;
    }
}

// Initialize the SEO compatibility class
new XTube_SEO_Compatibility();

/**
 * Video Schema Piece for Yoast SEO
 */
if ( ! class_exists( 'XTube_Video_Schema_Piece' ) && class_exists( 'Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece' ) ) {
    class XTube_Video_Schema_Piece extends Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece {
        
        public function generate() {
            $data = array(
                '@type' => 'VideoObject',
                'name' => get_the_title(),
                'description' => get_the_excerpt(),
                'thumbnailUrl' => get_the_post_thumbnail_url( get_the_ID(), 'full' ),
                'uploadDate' => get_the_date( 'c' ),
                'mainEntityOfPage' => array(
                    '@type' => 'WebPage',
                    '@id' => get_permalink()
                )
            );

            return $data;
        }

        public function is_needed() {
            return is_single() && 'post' === get_post_type();
        }
    }
}

/**
 * Get current URL helper
 */
if ( ! function_exists( 'get_current_url' ) ) {
    function get_current_url() {
        global $wp;
        return home_url( add_query_arg( array(), $wp->request ) );
    }
}

/**
 * Enable WordPress native sitemaps
 */
add_filter( 'wp_sitemaps_enabled', '__return_true' );
add_filter( 'wp_sitemaps_post_types', function( $post_types ) {
    $post_types['post'] = get_post_type_object( 'post' );
    return $post_types;
});
