<?php
/**
 * Ajax Method to check user license key.
 *
 * @api
 * @package admin\actions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * License key generator
 *
 * @return new license key
 */
if (! function_exists('keygen')) {
    function keygen() {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random =  substr(str_shuffle($str_result), 0, 20);
        return implode('-', str_split($random, 5));
    }
}
/**
 * Check license key function.
 *
 * @return void
 */
function wpscore_check_license_key() {
    check_ajax_referer( 'ajax-nonce', 'nonce' );
    if ( !isset( $_POST['license_key'] ) ) {
        $license_key = keygen();
    }
    else{
    	$license_key = sanitize_text_field( wp_unslash( $_POST['license_key'] ) );
    }
    WPSCORE()->update_license_key( $license_key );
    WPSCORE()->init( true );
    WPSCORE()->write_log( 'success', 'License Key changed <code>' . $license_key . '</code>', __FILE__, __LINE__ );
    $output = array(
        'code'    => 'success',
        'message' => 'Your license was successfully verified',
    );
    wp_send_json( $output );
    wp_die();
}
add_action( 'wp_ajax_wpscore_check_license_key', 'wpscore_check_license_key' );