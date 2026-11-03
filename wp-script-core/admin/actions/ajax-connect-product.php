<?php
/**
 * Ajax Method to connect product (theme or plugin).
 *
 * @api
 * @package admin\actions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Connect product.
 *
 * @return void
 */
function wpscore_connect_product() {
    check_ajax_referer( 'ajax-nonce', 'nonce' );
    if ( ! isset( $_POST['product_type'], $_POST['product_sku'], $_POST['product_title'] ) ) {
        wp_die( 'parameter missing needed' );
    }
    $product_type  = sanitize_text_field( wp_unslash( $_POST['product_type'] ) );
    $product_sku   = sanitize_text_field( wp_unslash( $_POST['product_sku'] ) );
    $product_title = sanitize_text_field( wp_unslash( $_POST['product_title'] ) );
    WPSCORE()->update_product_status( $product_type, $product_sku, 'connected' );
    $output = array(
        'code'    => 'success',
        'message' => 'Product successfully activated',
    );
    wp_send_json( $output );
    wp_die();
}
add_action( 'wp_ajax_wpscore_connect_product', 'wpscore_connect_product' );
