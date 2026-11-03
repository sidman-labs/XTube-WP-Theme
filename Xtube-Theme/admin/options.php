<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Xbox plugin
*/
require_once( get_template_directory() . '/vendor/xbox/xbox.php' );

add_filter( 'wpst-options', 'wpst_options_page' );

function wpst_options_page( $options_table ) {

        $output = '<style>
                .xbox-header-actions,
                #wp-script .xbox-separator { display: none!important; }
                #wp-script .xbox.xbox-boxed .xbox-row:not(.xbox-row-mixed):first-child { margin-top: 0; }
                #wp-script .xbox .xbox-type-title .xbox-content { padding: 0 0 30px; }
                #wp-script .xbox-row .xbox-content { border-left: none; }
                #wp-script .xbox-row .xbox-field-description,
                #wp-script .xbox-row .xbox-field-description a { font-size: 16px; font-style: normal; }
        </style>';

        $output .= '<div id="wp-script">
                                        <div class="content-tabs">';

        $output .= '<div class="tab-content tab-options">';

        $output .= $options_table;

        $output .= '</div>';
        $output .= '</div>';

        $output .= '</div>';
        $output .= '</div>';

        $output .= '</div>';

        return $output;
}

add_action("admin_menu", "VTubeMenu");
function VTubeMenu() {
    add_menu_page("XTube Theme Options", "XTube", "manage_options",
        "wpst-options", null, 'dashicons-buddicons-topics', 61);
}
add_action( 'xbox_init', 'wpst_options' );
function wpst_options() {
        $options = array(
                'id'         => 'wpst-options',
        'title' => 'XTube Theme Options',
        'icon' => XBOX_URL.'/img/xbox-dark.png',
        'skin' => 'pink',// Skins: blue, lightblue, green, teal, pink, purple, bluepurple, yellow, orange'.
        'layout' => 'wide',//Layouts: wide, boxed
        'parent' => false,//The slug name for the parent menu (or the file name of a standard WordPress admin page).
        'capability' => 'manage_options',//https://codex.wordpress.org/Roles_and_Capabilities
        'header' => array(
            'icon' => '<img src="'.XBOX_URL.'/img/xbox-dark.png"/>',
            'desc' => 'Custom description for theme options',
        ),
        'saved_message' => __( 'Settings updated', 'xbox' ),
        'reset_message' => __( 'Settings reset', 'xbox' ),
        );

        $xbox = xbox_new_admin_page( $options );

        $xbox->add_main_tab(
                array(
                        'name'  => esc_html__( 'Main tab', 'wpst' ),
                        'id'    => 'main-tab',
                        'items' => array(
                                'general' => '',
                        ),
                )
        );
                /*******************/
                /***** GENERAL *****/
                /*******************/
                $xbox->open_tab_item( 'general' );
                        $xbox->add_field(
                                array(
                                        'id'   => 'customizer',
                                        'name' => '',
                                        'type' => 'title',
                                        'desc' => sprintf( __( '<a href="%s">Click here</a> to go to the Customizer.', 'wpst' ), get_admin_url() . 'customize.php' ),
                                )
                        );
                $xbox->close_tab_item( 'general' );

        $xbox->close_tab( 'main-tab' );
}
