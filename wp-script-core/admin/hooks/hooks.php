<?php
/**
 * WPSCORE Hooks.
 *
 * @api
 * @package CORE\admin\hooks
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Filter to add thumb and partner columns when editing posts.
 *
 * @param array $defaults Array of default columns.
 */
function wpscore_add_columns( $defaults ) {
	$defaults['thumb']   = __( 'Thumbnail', 'wpscore_lang' );
	$defaults['partner'] = __( 'Partner', 'wpscore_lang' );
	return $defaults;
}
add_filter( 'manage_edit-post_columns', 'wpscore_add_columns' );

/**
 * Action to add thumb and partner content in columns when editig posts.
 *
 * @param string $name The name of the column to add content in.
 */
function wpscore_columns_content( $name ) {
	global $post;
	switch ( $name ) {
		case 'thumb':
			$attachment = '';
			$attr       = '';
			if ( isset( $attachment ) && is_object( $attachment ) ) {
				$attr = array(
					'alt'   => trim( wp_strip_all_tags( $attachment->post_excerpt ) ),
					'title' => trim( wp_strip_all_tags( $attachment->post_title ) ),
				);
			}
			if ( has_post_thumbnail() ) {
				echo get_the_post_thumbnail( $post->ID, 'wpscript_thumb_admin', $attr );
			} elseif ( get_post_meta( $post->ID, 'thumb', true ) ) {
				echo wp_kses(
					'<img src="' . get_post_meta( $post->ID, 'thumb', true ) . '" width="95" height="70" alt="' . get_the_title() . '" />',
					wp_kses_allowed_html(
						array(
							'img' => array(
								'alt'    => array(),
								'class'  => array(),
								'height' => array(),
								'src'    => array(),
								'width'  => array(),
							),
						)
					)
				);
			} else {
				echo wp_kses(
					'<img src="https://res.cloudinary.com/themabiz/image/upload/wpscript/sources/admin-no-image.jpg" width="95" height="70" />',
					wp_kses_allowed_html(
						array(
							'img' => array(
								'alt'    => array(),
								'class'  => array(),
								'height' => array(),
								'src'    => array(),
								'width'  => array(),
							),
						)
					)
				);
			}
			break;
		case 'partner':
			$partner = get_post_meta( $post->ID, 'partner', true );
			echo wp_kses(
				'<img src="https://res.cloudinary.com/themabiz/image/upload/wpscript/sources/' . $partner . '.jpg" alt="' . $partner . '"/>',
				wp_kses_allowed_html(
					array(
						'img' => array(
							'alt'    => array(),
							'class'  => array(),
							'height' => array(),
							'src'    => array(),
							'width'  => array(),
						),
					)
				)
			);
			break;
	}
}
add_action( 'manage_posts_custom_column', 'wpscore_columns_content' );

/**
 * Hook to create admin thumbnails for posts listings.
 */
add_image_size( 'wpscript_thumb_admin', '95', '70', '1' );

/**
 * Update client signature when switching theme.
 *
 * @return void
 */
function wpscore_switch_theme() {
	// update site signature.
	WPSCORE()->update_client_signature();
	// call init.
	WPSCORE()->init( true );
}
add_action( 'after_switch_theme', 'wpscore_switch_theme' );

/**
 * Display admin notice when there are some WP-Script porducts updates.
 *
 * @return bool True if a notice is displayed, false if not.
 */
function wpscript_admin_notice_updates() {
    return false;
}
add_action( 'admin_notices', 'wpscript_admin_notice_updates' );
