<?php
if ( ! function_exists( 'vtt_get_video_duration' ) ) {
	function vtt_get_video_duration( $type_length = '' ){
		global $post;
		$duration = intval( get_post_meta($post->ID, 'duration', true) );

		if( $duration > 0 ){
			if( $duration >= 3600 ){
				return date( 'H:i:s', $duration );
			}else{
				return date( 'i:s', $duration );
			}
		}else{
			return false;
		}
	}
}

if ( ! function_exists( 'vtt_get_duration_sec' ) ) {
	function vtt_get_duration_sec( $duration, $sponsor ){
		switch ( $sponsor ){
			case 'pornhub' :
			case 'redtube' :
			case 'spankwire' :
			case 'tube8' :
			case 'xhamster' :
			case 'youporn' :
			$min = explode(':', $duration);
			$sec = explode(':', $duration);
			return (int) $min[0] * 60 + (int) $sec[1];
			break;
			case 'xvideos' :
			$duration = str_replace(array('- ', 'h', 'min', 'sec'), array('', 'hours', 'minutes', 'seconds'), $duration);
			return strtotime( $duration ) - strtotime('NOW');
			break;
			default :
			return false;
		}
	}
}

if ( ! function_exists( 'vtt_getPostViews' ) ) {
	function vtt_getPostViews($postID){
		$count_key = 'post_views_count';
		$count = get_post_meta($postID, $count_key, true);
		if($count==''){
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '0');
			return "0";
		}
		return $count;
	}
}

//Duration in ISO 8601
if ( ! function_exists( 'vtt_iso8601_duration' ) ) {
	function vtt_iso8601_duration($seconds) {
		$seconds = (int) $seconds;
		$days = floor($seconds / 86400);
		$seconds = $seconds % 86400;
		$hours = floor($seconds / 3600);
		$seconds = $seconds % 3600;
		$minutes = floor($seconds / 60);
		$seconds = $seconds % 60;
		return sprintf('P%dDT%dH%dM%dS', $days, $hours, $minutes, $seconds);
	}
}

if ( ! function_exists( 'vtt_get_multithumbs' ) ) {
	function vtt_get_multithumbs($post_id){
		global $post;
		$thumbs = null;
		if ( has_post_thumbnail() ){
			$args = array(
				'post_type'   => 'attachment',
				'numberposts' => -1,
				'post_status' => 'any',
				'post_parent' => $post->ID,
				);
			$thumb_size = 'video-thumb';

			$attachments = get_attached_media( 'image' );
				
			if ( count($attachments) > 1 ) {
				foreach ( (array)$attachments as $attachment ) {
					$thumbs_array = wp_get_attachment_image_src($attachment->ID, $thumb_size);
					$thumbs[] = $thumbs_array[0];
				}
				sort($thumbs);
			}else{
				$thumbs = get_post_meta($post_id, 'thumbs', false);
			}
		}else{
			$thumbs = get_post_meta($post_id, 'thumbs', false);
		}
		if( is_ssl() ) {
			$thumbs = str_replace('http://', 'https://', $thumbs);
		}
		if( is_array($thumbs) ){
			return implode(',', $thumbs);
		}
		
		return false;
	}
}

if ( ! function_exists( 'vtt_cats_tags' ) ) {
	function vtt_cats_tags() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {			
			$postcats = get_the_category(); $posttags = get_the_tags();
			if ($postcats || $posttags) {
				// echo '<div class="tags-list">';
				if($postcats !== false) {
					foreach((array)$postcats as $cat) {
						echo '<a href="' . get_category_link( $cat->term_id ) . '" title="' . $cat->name . '">' . ucfirst($cat->name) . '</a> ';
					}
				}
				if($posttags !== false) {
					foreach((array)$posttags as $tag) {
						echo '<a href="' . get_tag_link($tag->term_id) . '" title="' . $tag->name . '">' . ucfirst($tag->name) . '</a> ';
					}
				}
				// echo '</div>';
			}
		}
		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			/* translators: %s: post title */
			comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'wpst' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
			echo '</span>';
		}
	}
}

// This enables the function that lets you set new image sizes
add_theme_support( 'post-thumbnails' );
// These are the new image sizes we cooked up
add_image_size( 'video-thumb', 400 );
// Now we register the size so it appears as an option within the editor
add_filter( 'image_size_names_choose', 'vtt_thumbs_size' );
function vtt_thumbs_size( $sizes ) {
	return array_merge( $sizes, array(
		'video-thumb' => __( 'Video Thumb' ),
	) );
}