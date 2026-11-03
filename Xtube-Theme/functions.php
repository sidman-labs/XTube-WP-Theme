<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
/**
 * WP-Script Core required.
 */
require get_template_directory() . '/vendor/tgm-activation/class-tgm-plugin-activation.php';
require get_template_directory() . '/inc/plugin-activation.php';

add_action( 'widgets_init', 'vtt_widgets_init' );

if( ! function_exists('is_plugin_active') ) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if( is_plugin_active('wp-script-core/wp-script-core.php') ){
        // Load inc files.
        $vtt_includes = array(
                '/customizer.php',                                              // Customizer additions.
                '/theme-activation.php',                                // Create pages and menu on theme activation.
                '/enqueue.php',                                                 // Enqueue scripts and styles.
                '/post-like.php',                                               // Post like
                '/video-functions.php',                 // Video functions.
                '/actors.php',                                                  // Actors CPT
                '/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker.
                '/template-tags.php',                                   // Custom template tags for this theme.
                '/category-image.php',                                  // Category image upload feature.
                '/actor-image.php',                                     // Actor image upload feature.
                '/pagination.php',                                              // Custom pagination for this theme.
                '/theme-settings.php',                  // Initialize theme default settings.
                '/setup.php',                           // Theme setup and custom theme supports.
                '/widgets.php',                         // Register widget area.
                // '/widget-video.php',                         // Load widget video blocks.
                '/hooks.php',                           // Custom hooks.
                '/extras.php',                          // Custom functions that act independently of the theme templates.
                '/custom-comments.php',                 // Custom Comments file.
                '/jetpack.php',                         // Load Jetpack compatibility file.
                '/woocommerce.php',                     // Load WooCommerce functions.
                '/editor.php',                          // Load Editor functions.
                '/deprecated.php',                      // Load deprecated functions.
        );
        // SEO, Elementor, and Dark Mode compatibility (always loaded)
        $vtt_includes[] = '/seo-compatibility.php';
        $vtt_includes[] = '/elementor-compatibility.php';
        // $vtt_includes[] = '/dark-mode.php'; // Dark mode disabled
        $vtt_includes[] = '/advanced-video-player.php';

        foreach ( $vtt_includes as $file ) {
                $filepath = locate_template( 'inc' . $file );
                if ( $filepath ) {
                        require_once $filepath;
                }
        }

        // Load ajax files.
        $vtt_ajax = array(
                '/get-async-post-data.php',
                '/load-video-preview.php',
                '/login-register.php',
                '/post-like.php',
                '/post-views.php',
        );
        foreach ( $vtt_ajax as $file ) {
                $filepath = locate_template( 'ajax' . $file );
                if ( $filepath ) {
                        require_once $filepath;
                }
        }

        // Load admin files.
        $vtt_admin = array(
                '/options.php',                                                 // Options
                '/metabox.php',                                                 // Video information metabox
        );
        foreach ( $vtt_admin as $file ) {
                $filepath = locate_template( 'admin' . $file );
                if ( $filepath ) {
                        require_once $filepath;
                }
        }
}

// Customizer text editor.
if ( class_exists( 'WP_Customize_Control' ) ) {
        class Text_Editor_Custom_Control extends WP_Customize_Control {
                public $type = 'textarea';
                
                /**
                 * Render the content on the theme customizer page
                 */
                public function render_content(): void {
                        echo '<label>';
                        echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
                        $settings = array(
                                'media_buttons' => false,
                                'quicktags'     => false,
                        );
                        $this->filter_editor_setting_link();
                        wp_editor( $this->value(), $this->id, $settings );
                        echo '</label>';

                        do_action( 'admin_footer' );
                        do_action( 'admin_print_footer_scripts' );
                }
                
                private function filter_editor_setting_link(): void {
                        add_filter( 'the_editor', array( $this, 'filter_editor_setting_link_callback' ) );
                }
                
                public function filter_editor_setting_link_callback( string $output ): string {
                        return preg_replace( '/<textarea/', '<textarea ' . $this->get_link(), $output, 1 );
                }
        }
}

function vtt_editor_customizer_script() {
        wp_enqueue_script( 'wp-editor-customizer', get_template_directory_uri() . '/js/customizer-panel.js', array( 'jquery' ), rand(), true );
}
add_action( 'customize_controls_enqueue_scripts', 'vtt_editor_customizer_script' );

function vtt_posts_filter( $query ) {
        if ( is_admin() || ! $query->is_main_query() )
                return;

        if ( !is_single() ) {
                $filter = '';
                if(is_home()) {
                        $filter = get_theme_mod('general_homepage_video_sorting', 'random');
                }               
                if(isset($_GET['filter'])) {
                        $filter = $_GET['filter'];
                }
                // if(is_home()){
                //      $query->set( 'posts_per_page', 25 );
                // }else{
                //      $query->set( 'posts_per_page', 50 );
                // }
                $query->set( 'posts_per_page', 50 );
                // Change posts per page when ads in list are set.
                if ( !is_home() && ( '' !== get_theme_mod( 'ads_home_inside_list', '' ) || '' !== get_theme_mod( 'ads_category_page_inside_list', '' ) || '' !== get_theme_mod( 'ads_tag_page_inside_list', '' ) || '' !== get_theme_mod( 'ads_actor_page_inside_list', '' ) || '' !== get_theme_mod( 'ads_search_result_page_inside_list', '' ) ) ) {
                        $query->set( 'posts_per_page', 46 );
                }
                switch($filter) {
                        case 'latest' :
                                $query->set( 'orderby', 'date');
                                $query->set( 'order', 'DESC');
                                break;
                        case 'most-viewed' :
                                $query->set( 'meta_key', 'post_views_count');
                                $query->set( 'orderby', 'meta_value_num');
                                $query->set( 'order', 'DESC');
                                break;
                        case 'longest' :
                                $query->set( 'meta_key', 'duration');
                                $query->set( 'orderby', 'meta_value_num');
                                $query->set( 'order', 'DESC');
                                break;
                        case 'popular' :
                                $query->set( 'orderby', 'meta_value_num');
                                $query->set( 'meta_query', array(
                                                                                                'relation'  => 'OR',
                                                                                                array(
                                                                                                        'key'     => 'rate',
                                                                                                        'compare' => 'NOT EXISTS'
                                                                                                ),
                                                                                                array(
                                                                                                        'key'     => 'rate',
                                                                                                        'compare' => 'EXISTS'
                                                                                                )
                                                                                        )
                                                        );
                                $query->set( 'order', 'DESC');
                                break;
                        case 'random' :
                                $query->set( 'orderby', 'rand');
                                $query->set( 'order', 'DESC');
                                break;
                        default;
                }
                $query->set( 'post_status', 'publish' );
                $query->set( 'order', 'DESC' );
        }
        return $query;
}
add_action( 'pre_get_posts', 'vtt_posts_filter' );

if ( ! function_exists( 'vtt_get_video_preview' ) ) {
        function vtt_get_video_preview( $post_id = null ) {
                if ( null === $post_id ) {
                        global $post;
                        $post_id = $post->ID;
                }
                $post_id    = intval( $post_id );
                $trailer_url = get_post_meta( $post_id, 'trailer_url', true );
                $trailer_format = explode( '.',  $trailer_url);
                $trailer_format = $trailer_format[ count( $trailer_format ) - 1];
                $trailer_video = '<video width="100%" height="100%" autoplay loop muted preload="none"><source src="' . $trailer_url . '" type="video/' . $trailer_format . '">Your browser does not support the video tag.</video>';
                if($trailer_url){
                        return $trailer_video;
                }else{
                        return;
                }
        }
}

/**
 * Replace accented characters with non accented
 */
function vtt_removeAccents($str) {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
        return str_replace($a, $b, $str);
}

/**
 * Selected filters
 */
function vtt_selected_filter($filter){
        $current_filter = '';
    if(isset($_GET['filter'])) {
        $current_filter = $_GET['filter'];
    }
        if($current_filter == $filter) {
                return 'active';
        }
        return false;
}

function vtt_get_filter_title(){
        $title = '';
        $filter = '';
        if(isset($_GET['filter'])) {
        $filter = $_GET['filter'];
    }else{
                $filter = xbox_get_field_value( 'vtt-options', 'show-videos-homepage' );
        }
    switch($filter) {
        case 'latest' :
            $title = esc_html__('Recently added videos', 'wpst');
            break;
        case 'most-viewed' :
            $title = esc_html__('Most viewed videos', 'wpst');
            break;
        case 'longest' :
            $title = esc_html__('Longest videos', 'wpst');
            break;
        case 'popular' :
            $title = esc_html__('Popular videos', 'wpst');
            break;
        case 'random' :
            $title = esc_html__('Random videos', 'wpst');
            break;
        default :
                        $title = esc_html__('Recently added videos', 'wpst');
            break;
    }
        return $title;
}

// function vtt_widgets_init() {
//      register_sidebar(
//              array(
//                      'name'                  => esc_html__('Homepage', 'wpst'),
//                      'id'                    => 'homepage',
//                      'description'   => esc_html__('Display widgets on your homepage.', 'wpst'),
//                      'before_widget' => '<section id="%1$s" class="widget %2$s">',
//                      'after_widget'  => '</section>',
//                      'before_title'  => '<h2 class="widget-title">',
//                      'after_title'   => '</h2>',
//              )
//      );
//      register_sidebar(
//              array(
//                      'name'                  => esc_html__('Video Sidebar', 'wpst'),
//                      'id'                    => 'video_sidebar',
//                      'description'   => esc_html__('Display widgets in the single video page sidebar.', 'wpst'),
//                      'before_widget' => '<section id="%1$s" class="widget %2$s">',
//                      'after_widget'  => '</section>',
//                      'before_title'  => '<h2 class="widget-title">',
//                      'after_title'   => '</h2>',
//              )
//      );
// }

add_filter("mce_css", "vtt_remove_mce_css");
function vtt_remove_mce_css($stylesheets){
    return "";
}

function vtt_get_nopaging_url() {
    global $wp;

    $current_url =  home_url( $wp->request );
    $position = strpos( $current_url , '/page' );
    $nopaging_url = ( $position ) ? substr( $current_url, 0, $position ) : $current_url;

    return trailingslashit( $nopaging_url );
}

function vtt_duration_custom_field( $updated, $field ){
        $duration_hh = isset( $_POST['duration_hh'] ) ? $_POST['duration_hh'] : 0;
        $duration_mm = isset( $_POST['duration_mm'] ) ? $_POST['duration_mm'] : 0;
        $duration_ss = isset( $_POST['duration_ss'] ) ? $_POST['duration_ss'] : 0;
        $field->save( $duration_hh * 3600 + $duration_mm * 60 + $duration_ss );
}
add_action('xbox_after_save_field_duration', 'vtt_duration_custom_field', 10, 2);

function vtt_render_shortcodes( $content ){
        $regex = '/\[(.+)\]/m';
        preg_match_all($regex, $content, $matches, PREG_SET_ORDER, 0);

        // Print the entire match result
        if(is_array($matches)){
                foreach( $matches as $shortcode ){
                        $shortcode_with_brackets = $shortcode[0];
                        $shortcode_without_brackets = $shortcode[1];
                        $should_be_shortcode = explode(' ', $shortcode_without_brackets);
                        $should_be_shortcode = current($should_be_shortcode);
                        if( shortcode_exists($should_be_shortcode) ){
                                $shortcode = do_shortcode( $shortcode_with_brackets );
                                return $shortcode;
                        }
                }
        }
        return $content;
}

function vtt_change_post_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Videos';
    $submenu['edit.php'][5][0] = 'Videos';
        $submenu['edit.php'][10][0] = 'Add Video';
        $submenu['edit.php'][15][0] = 'Video Categories';
        $submenu['edit.php'][16][0] = 'Video Tags';
}
function vtt_change_post_object() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Videos';
    $labels->singular_name = 'Videos';
    $labels->add_new = 'Add Video';
    $labels->add_new_item = 'Add Video';
    $labels->edit_item = 'Edit Video';
    $labels->new_item = 'Videos';
    $labels->view_item = 'View Videos';
    $labels->search_items = 'Search Videos';
    $labels->not_found = 'No Videos found';
    $labels->not_found_in_trash = 'No Videos found in Trash';
    $labels->all_items = 'All Videos';
    $labels->menu_name = 'Videos';
    $labels->name_admin_bar = 'Videos';
}

add_action( 'admin_menu', 'vtt_change_post_label' );
add_action( 'init', 'vtt_change_post_object' );

function vtt_change_cat_object() {
    global $wp_taxonomies;
    $labels = &$wp_taxonomies['category']->labels;
    $labels->name = 'Video Category';
    $labels->singular_name = 'Video Category';
    $labels->add_new = 'Add Video Category';
    $labels->add_new_item = 'Add Video Category';
    $labels->edit_item = 'Edit Video Category';
    $labels->new_item = 'Video Category';
    $labels->view_item = 'View Video Category';
    $labels->search_items = 'Search Video Categories';
    $labels->not_found = 'No Video Categories found';
    $labels->not_found_in_trash = 'No Video Categories found in Trash';
    $labels->all_items = 'All Video Categories';
    $labels->menu_name = 'Video Category';
    $labels->name_admin_bar = 'Video Category';
}
add_action( 'init', 'vtt_change_cat_object' );

function vtt_change_tag_object() {
    global $wp_taxonomies;
    $labels = &$wp_taxonomies['post_tag']->labels;
    $labels->name = 'Video Tag';
    $labels->singular_name = 'Video Tag';
    $labels->add_new = 'Add Video Tag';
    $labels->add_new_item = 'Add Video Tag';
    $labels->edit_item = 'Edit Video Tag';
    $labels->new_item = 'Video Tag';
    $labels->view_item = 'View Video Tag';
    $labels->search_items = 'Search Video Tags';
    $labels->not_found = 'No Video Tags found';
    $labels->not_found_in_trash = 'No Video Tags found in Trash';
    $labels->all_items = 'All Video Tags';
    $labels->menu_name = 'Video Tag';
    $labels->name_admin_bar = 'Video Tag';
}
add_action( 'init', 'vtt_change_tag_object' );

function replace_admin_menu_icons_css() {
    ?>
    <style>
        #menu-posts .dashicons-admin-post::before, #menu-posts .dashicons-format-standard::before {
                        content: "\f236";
                }
    </style>
    <?php
}

add_action( 'admin_head', 'replace_admin_menu_icons_css' );

function vtt_rss_post_thumbnail($content) {
        global $post;
        if(has_post_thumbnail($post->ID)) {
        $content = '<p>' . get_the_post_thumbnail($post->ID) . '</p>' . $content;
        }
        return $content;
}
add_filter('the_excerpt_rss', 'vtt_rss_post_thumbnail');
add_filter('the_content_feed', 'vtt_rss_post_thumbnail');

/* Remove admin bar for logged in users */
function vtt_remove_admin_bar() {
        if ( !current_user_can('administrator') && !is_admin() && xbox_get_field_value( 'vtt-options', 'display-admin-bar' ) == 'off' ) {
                show_admin_bar(false);
                remove_action('wp_head', '_admin_bar_bump_cb');
        }
}
add_action('get_header', 'vtt_remove_admin_bar');

/**
 * Modify the "must_log_in" string of the comment form.
 */
add_filter( 'comment_form_defaults', function( $fields ) {
    $fields['must_log_in'] = sprintf(
        __( '<p class="must-log-in">
                 You must be <a href="#vtt-login">logged in</a> to post a comment.</p>'
        ),
        wp_registration_url(),
        wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
    );
    return $fields;
});

function vtt_get_sources_from_hls($hls_url) {
        $pared_hls_url = parse_url($hls_url);
        $referer = $pared_hls_url['scheme'] . '://' . $pared_hls_url['host'];

        $base_url_regex = '/^(.+)\//';
        preg_match($base_url_regex, $hls_url, $m3u8_matches, PREG_OFFSET_CAPTURE, 0);
        $base_url = $m3u8_matches[0][0];

        $hls_body = vtt_curl($hls_url, $referer);
        // $hls_body_regex = '/(hls-.+\s)/mU';
        $hls_body_regex = '/RESOLUTION=\d+x(.+)\W.*\s(.+\.m3u8.*)(?:$|\s)/mU';

        preg_match_all($hls_body_regex, $hls_body, $hls_matches, PREG_SET_ORDER, 0);

        $resolutions = array();

        foreach( $hls_matches as $hls_match ) {
          $res = $hls_match[1];
          $hls_file = $base_url . $hls_match[2];
          $resolutions[$res] = $hls_file;
        }
        krsort($resolutions);
        foreach( $resolutions as $res => $hls_file ) {
          $label = (int) $res > 3000 ? '4K' : (string) $res . 'p';
          $output[] = "<source src=\"$hls_file\" label=\"$label\" type=\"application/x-mpegURL\"/>";
        }
        return implode('', $output);
}

function vtt_curl($url, $referer, $type=null){
        ini_set('memory_limit','256M');
        $agent = ($type != null) ? 'Mozilla/5.0 (Linux; U; Android 4.0; en-us; GT-I9300 Build/IMM76D)' : 'Mozilla/5.0(Windows;U;WindowsNT5.0;en-US;rv:1.4)Gecko/20030624Netscape/7.1(ax)';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $page = curl_exec($ch);
        curl_close($ch);
        return $page;
}

function vtt_add_specific_menu_location_atts( $atts, $item, $args ) {
    // check if the item is in the primary menu
    if( $args->theme_location == 'vtt-primary-menu' ) {
      // add the desired attributes:
      $atts['class'] = 'btn btn-primary';
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'vtt_add_specific_menu_location_atts', 10, 3 );

/**
 * Add tags to menu
 */
if(!wp_is_mobile()) {
        add_filter( 'wp_nav_menu_items', 'vtt_custom_menu', 10, 2);
}
function vtt_custom_menu( $items, $args ) {
    if( $args->theme_location == 'vtt-primary-menu' && get_theme_mod('general_display_tags_in_menu', 'yes') == 'yes'){
                $menu_tags = get_tags(
                        array(
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'number' => 60
                                )
                        );
                if($menu_tags) {
                    foreach ( $menu_tags as $menu_tag ) {
                            $menu_tag_link = get_tag_link( $menu_tag->term_id );                                        
                            $items .= '<li class="menu-item nav-item tag-menu"><a class="btn btn-grey" href="' . $menu_tag_link . '">' . ucfirst($menu_tag->name) . '</a></li>';
                    }
                    $items .= '<li class="menu-item nav-item tag-menu see-all-tags"><a class="btn btn-grey" href="' . get_bloginfo( 'url' ) . '/tags">' . get_theme_mod('general_see_all_tags_menu_text', 'See all tags') . '</a></li>';
            }
    }
    return $items;
}

// add_action('init', 'vtt_data_booster');
function vtt_data_booster(){
    $args = array(
        'posts_per_page'   => -1,
        'post_type'        => 'post',
        'suppress_filters' => true
    );
        $posts_array = get_posts( $args );
    foreach($posts_array as $post_array) {
                update_post_meta($post_array->ID, 'post_views_count', rand(2000,9999));
                // update_post_meta($post_array->ID, 'likes_count', rand(200,1999));
        // update_post_meta($post_array->ID, 'dislikes_count', rand(20,150));
    }
}
