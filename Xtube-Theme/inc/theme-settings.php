<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'vtt_setup_theme_default_settings' ) ) {
	function vtt_setup_theme_default_settings() {

		// check if settings are set, if not set defaults.
		// Caution: DO NOT check existence using === always check with == .
		// Latest blog posts style.
		$vtt_posts_index_style = get_theme_mod( 'vtt_posts_index_style' );
		if ( '' == $vtt_posts_index_style ) {
			set_theme_mod( 'vtt_posts_index_style', 'default' );
		}

		// Sidebar position.
		$vtt_sidebar_position = get_theme_mod( 'vtt_sidebar_position' );
		if ( '' == $vtt_sidebar_position ) {
			set_theme_mod( 'vtt_sidebar_position', 'left' );
		}

		// Container width.
		// $vtt_container_type = get_theme_mod( 'vtt_container_type' );
		// if ( '' == $vtt_container_type ) {
		// 	set_theme_mod( 'vtt_container_type', 'container' );
		// }
	}
}
