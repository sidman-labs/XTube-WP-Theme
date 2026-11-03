<?php
/**
 * Open Graph & Twitter Card meta tags for single posts.
 * Falls back gracefully when Rank Math / Yoast SEO is active (handled in seo-compatibility.php).
 *
 * @package XTube
 */

// Skip if a SEO plugin is already handling meta tags
if ( function_exists( 'xtube_maybe_disable_theme_meta' ) && xtube_maybe_disable_theme_meta() ) {
	return;
}

global $post;

if ( ! isset( $post ) ) {
	return;
}

$thumb_id    = get_post_thumbnail_id( $post->ID );
$thumb_url   = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : get_post_meta( $post->ID, 'thumb', true );
$post_data   = function_exists( 'vtt_get_post_data' ) ? vtt_get_post_data( $post->ID ) : $post;
$current_url = get_permalink( $post->ID );
$site_name   = get_bloginfo( 'name' );

$current_title = isset( $post_data->post_title ) ? $post_data->post_title : get_the_title( $post->ID );

$current_desc = '';
if ( ! empty( $post->post_excerpt ) ) {
	$current_desc = esc_attr( strip_tags( $post->post_excerpt ) );
} elseif ( ! empty( $post_data->post_content ) ) {
	$current_desc = esc_attr( wp_trim_words( strip_tags( $post_data->post_content ), 30 ) );
} else {
	$current_desc = esc_attr( $current_title );
}
?>

<!-- Open Graph / Facebook -->
<meta property="og:type" content="<?php echo is_single() ? 'article' : 'website'; ?>">
<meta property="og:url" content="<?php echo esc_url( $current_url ); ?>">
<meta property="og:title" content="<?php echo esc_attr( $current_title ); ?>">
<meta property="og:description" content="<?php echo $current_desc; ?>">
<meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>">
<?php if ( $thumb_url ) : ?>
<meta property="og:image" content="<?php echo esc_url( $thumb_url ); ?>">
<meta property="og:image:secure_url" content="<?php echo esc_url( $thumb_url ); ?>">
<meta property="og:image:width" content="1280">
<meta property="og:image:height" content="720">
<?php endif; ?>

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr( $current_title ); ?>">
<meta name="twitter:description" content="<?php echo $current_desc; ?>">
<?php if ( $thumb_url ) : ?>
<meta name="twitter:image" content="<?php echo esc_url( $thumb_url ); ?>">
<?php endif; ?>
