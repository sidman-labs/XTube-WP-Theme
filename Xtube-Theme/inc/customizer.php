<?php
/**
 * VTT Theme Customizer
 *
 * @package wpst
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
if ( ! function_exists( 'vtt_customize_register' ) ) {
	/**
	 * Register basic customizer support.
	 *
	 * @param object $wp_customize Customizer reference.
	 */
	function vtt_customize_register( $wp_customize ) {
		// $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		// $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		// $wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

		$wp_customize->remove_section( 'title_tagline' );
		$wp_customize->remove_section( 'colors' );
		$wp_customize->remove_section( 'static_front_page' );
		$wp_customize->remove_section( 'background_image' );
		// $wp_customize->remove_panel( 'widgets' );
	}
}
add_action( 'customize_register', 'vtt_customize_register' );

if ( ! function_exists( 'vtt_theme_customize_register' ) ) {
	/**
	 * Register individual settings through customizer's API.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer reference.
	 */
	function vtt_theme_customize_register( $wp_customize ) {

		/*********************************/
		/****** NEW SECTION GENERAL ******/
		/*********************************/
		// $wp_customize->add_panel(
		// 	'vtt_general_settings',
		// 	array(
		// 		'capability'     => 'edit_theme_options',
		// 		'priority'       => 10,
		// 		'theme_supports' => '',
		// 		'title'          => __( 'General', 'wpst' ),
		// 		'description'    => __( 'General settings', 'wpst' ),
		// 	)
		// );

		// /**
		//  * VIDEO LISTING GENERAL SETTINGS
		//  */
		// $wp_customize->add_section(
		// 	'vtt_video_listing_general_settings',
		// 	array(
		// 		'priority'       => 10,
		// 		'capability'     => 'edit_theme_options',
		// 		'theme_supports' => '',
		// 		'title'          => __( 'Video Listing', 'wpst' ),
		// 		'description'    => __( 'General settings on video listing pages.', 'wpst' ),
		// 		'panel'          => 'vtt_general_settings',
		// 	)
		// );

		// // Show title
		// $wp_customize->add_setting(
		// 	'video_listing_general_show_title',
		// 	array(
		// 		'default'   => 'yes',
		// 		'transport' => 'refresh',
		// 	)
		// );

		// $wp_customize->add_control(
		// 	new WP_Customize_Control(
		// 		$wp_customize,
		// 		'vtt_video_listing_general_show_title',
		// 		array(
		// 			'label'             => __( 'Show Title', 'wpst' ),
		// 			'description'       => __(
		// 				'Show the video title over the thumb.',
		// 				'wpst'
		// 			),
		// 			'section'           => 'vtt_video_listing_general_settings',
		// 			'settings'          => 'video_listing_general_show_title',
		// 			'type'              => 'select',
		// 			'sanitize_callback' => 'vtt_theme_slug_sanitize_select',
		// 			'choices'           => array(
		// 				'yes'  => __( 'Yes', 'wpst' ),
		// 				'no' => __( 'No', 'wpst' ),
		// 			),
		// 			'priority'          => '10',
		// 		)
		// 	)
		// );

		// // Show duration
		// $wp_customize->add_setting(
		// 	'video_listing_general_show_duration',
		// 	array(
		// 		'default'   => 'yes',
		// 		'transport' => 'refresh',
		// 	)
		// );

		// $wp_customize->add_control(
		// 	new WP_Customize_Control(
		// 		$wp_customize,
		// 		'vtt_video_listing_general_show_duration',
		// 		array(
		// 			'label'             => __( 'Show Duration', 'wpst' ),
		// 			'description'       => __(
		// 				'Show the video duration over the thumb.',
		// 				'wpst'
		// 			),
		// 			'section'           => 'vtt_video_listing_general_settings',
		// 			'settings'          => 'video_listing_general_show_duration',
		// 			'type'              => 'select',
		// 			'sanitize_callback' => 'vtt_theme_slug_sanitize_select',
		// 			'choices'           => array(
		// 				'yes'  => __( 'Yes', 'wpst' ),
		// 				'no' => __( 'No', 'wpst' ),
		// 			),
		// 			'priority'          => '10',
		// 		)
		// 	)
		// );

		// /**
		//  * SINGLE VIDEO PAGE
		//  */
		// $wp_customize->add_section(
		// 	'vtt_single_video_pages_general_settings',
		// 	array(
		// 		'priority'       => 10,
		// 		'capability'     => 'edit_theme_options',
		// 		'theme_supports' => '',
		// 		'title'          => __( 'Single Video Pages', 'wpst' ),
		// 		'description'    => __( 'General settings on single video pages.', 'wpst' ),
		// 		'panel'          => 'vtt_general_settings',
		// 	)
		// );

		// // Show related videos aside player
		// $wp_customize->add_setting(
		// 	'single_video_pages_general_show_related_videos_aside_player',
		// 	array(
		// 		'default'   => 'yes',
		// 		'transport' => 'refresh',
		// 	)
		// );

		// $wp_customize->add_control(
		// 	new WP_Customize_Control(
		// 		$wp_customize,
		// 		'vtt_single_video_pages_general_show_related_videos_aside_player',
		// 		array(
		// 			'label'             => __( 'Show Related Videos Aside Player', 'wpst' ),
		// 			'description'       => __(
		// 				'Show the video title over the thumb.',
		// 				'wpst'
		// 			),
		// 			'section'           => 'vtt_single_video_pages_general_settings',
		// 			'settings'          => 'single_video_pages_general_show_related_videos_aside_player',
		// 			'type'              => 'select',
		// 			'sanitize_callback' => 'vtt_theme_slug_sanitize_select',
		// 			'choices'           => array(
		// 				'yes'  => __( 'Yes', 'wpst' ),
		// 				'no' => __( 'No', 'wpst' ),
		// 			),
		// 			'priority'          => '10',
		// 		)
		// 	)
		// );

		/*********************************/
		/****** NEW SECTION GENERAL ******/
		/*********************************/
		$wp_customize->add_section(
			'vtt_general',
			array(
				'title'    => __( 'General', 'wpst' ),
				'priority' => 10,
			)
		);

		// Link color
		$wp_customize->add_setting(
			'main_color', //give it an ID
			array(
				'default'   => '#de2600', // Give it a default
				'transport' => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'vtt_main_color',
				array(
					'label'    => __( 'Main Color', 'wpst' ), //set the label to appear in the Customizer
					'section'  => 'vtt_general', //select the section for it to appear under
					'settings' => 'main_color', //pick the setting it applies to
				)
			)
		);

		// Display tags in menu
		$wp_customize->add_setting(
			'general_display_tags_in_menu',
			array(
				'default'   => 'yes',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_general_display_tags_in_menu',
				array(
					'label'             => __( 'Show tags in menu', 'wpst' ),
					'description'       => __(
						'Show tags inside the main menu.',
						'wpst'
					),
					'section'           => 'vtt_general',
					'settings'          => 'general_display_tags_in_menu',
					'type'              => 'select',
					'sanitize_callback' => 'vtt_theme_slug_sanitize_select',
					'choices'           => array(
						'yes'  => __( 'Yes', 'wpst' ),
						'no' => __( 'No', 'wpst' ),
					)
				)
			)
		);

		// See all tags menu text
		$wp_customize->add_setting(
			'general_see_all_tags_menu_text',
			array(
				'default'   => 'See all tags',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_general_see_all_tags_menu_text',
				array(
					'label'    => __( 'See all tags menu text', 'wpst' ),
					'description'       => __(
						'Change the text of the "See all tags" button in the menu.',
						'wpst'
					),
					'section'  => 'vtt_general',
					'settings' => 'general_see_all_tags_menu_text',
					'type'     => 'text',
				)
			)
		);

		// Homepage video sorting
		$wp_customize->add_setting(
			'general_homepage_video_sorting',
			array(
				'default'   => 'random',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_general_homepage_video_sorting',
				array(
					'label'             => __( 'Homepage video sorting', 'wpst' ),
					'description'       => __(
						'Choose the video sorting by default to display on your homepage.',
						'wpst'
					),
					'section'           => 'vtt_general',
					'settings'          => 'general_homepage_video_sorting',
					'type'              => 'select',
					'sanitize_callback' => 'vtt_theme_slug_sanitize_select',
					'choices'           => array(
						'latest'  => __( 'Latest', 'wpst' ),
						'most-viewed' => __( 'Most Viewed', 'wpst' ),
						'longest' => __( 'Longest', 'wpst' ),
						'popular' => __( 'Popular', 'wpst' ),
						'random' => __( 'Random', 'wpst' ),
					)
				)
			)
		);

		/******************************/
		/****** NEW SECTION LOGO ******/
		/******************************/
		$wp_customize->add_section(
			'vtt_logo',
			array(
				'title'    => __( 'Logo & Favicon', 'wpst' ),
				'priority' => 20,
			)
		);

		$wp_customize->add_setting(
			'logo_file', //give it an ID
			array(
				'default'   => '', // Give it a default
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'vtt_logo_file',
				array(
					'label'      => __( 'Upload a logo image', 'theme_name' ),
					'section'    => 'vtt_logo',
					'settings'   => 'logo_file'
				)
			)
		);

		$wp_customize->add_setting(
			'logo_sep', //give it an ID
			array(
				'default'   => '', // Give it a default
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new Customizer_Library_Help_Text(
				$wp_customize,
				'vtt_logo_sep',
				array(
					'label'             => __( 'Or build your own tube logo online:', 'theme_name' ),
					'section'           => 'vtt_logo',
					// 'priority'          => 20,
					'description'       => '',
					'settings'  		=> 'logo_sep'
				)
			)
		);

		// Word 1
		$wp_customize->add_setting(
			'logo_word_1',
			array(
				'default'   => 'Your',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_logo_word_1',
				array(
					'label'    => __( 'Word 1', 'wpst' ),
					'section'  => 'vtt_logo',
					'settings' => 'logo_word_1',
					'type'     => 'text',
				)
			)
		);

		// Word 1
		$wp_customize->add_setting(
			'logo_word_2',
			array(
				'default'   => 'logo',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_logo_word_2',
				array(
					'label'    => __( 'Word 2', 'wpst' ),
					'section'  => 'vtt_logo',
					'settings' => 'logo_word_2',
					'type'     => 'text',
				)
			)
		);

		$wp_customize->add_setting(
			'favicon_file', //give it an ID
			array(
				'default'   => '', // Give it a default
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'vtt_favicon_file',
				array(
					'label'      => __( 'Upload a favicon image', 'theme_name' ),
					'section'    => 'vtt_logo',
					'settings'   => 'favicon_file',
					'description' => 'png or ico - 32x32'
				)
			)
		);

		/*************************************/
		/****** NEW SECTION ADVERTISING ******/
		/*************************************/
		$wp_customize->add_panel(
			'vtt_ads',
			array(
				'capability'     => 'edit_theme_options',
				'priority'       => 50,
				'theme_supports' => '',
				'title'          => __( 'Advertising', 'wpst' ),
				'description'    => __( 'Settings to add ads into your site', 'wpst' ),
			)
		);

		/**
		 * HOME ADS
		 */
		$wp_customize->add_section(
			'vtt_ads_home',
			array(
				'priority'       => 50,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Homepage', 'wpst' ),
				'description'    => __( 'Add your own ads on the homepage.', 'wpst' ),
				'panel'          => 'vtt_ads',
			)
		);

		// Home Ad inside videos list
		// $wp_customize->add_setting(
		// 	'ads_home_inside_list',
		// 	array(
		// 		'default'   => '',
		// 		'transport' => 'refresh',
		// 	)
		// );

		// $wp_customize->add_control(
		// 	new WP_Customize_Control(
		// 		$wp_customize,
		// 		'vtt_ads_home_inside_list',
		// 		array(
		// 			'label'    => esc_html__( 'Ad zone inside videos list', 'wpst' ),
		// 			'section'  => 'vtt_ads_home',
		// 			'settings' => 'ads_home_inside_list',
		// 			'type'     => 'textarea',
		// 		)
		// 	)
		// );

		// Home Ad page bottom
		$wp_customize->add_setting(
			'ads_home_page_bottom',
			array(
				'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-4.png"></a>',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_ads_home_page_bottom',
				array(
					'label'    => esc_html__( 'Ad zone at the bottom of the page', 'wpst' ),
					'section'  => 'vtt_ads_home',
					'settings' => 'ads_home_page_bottom',
					'type'     => 'textarea',
				)
			)
		);

		/**
		 * SINGLE VIDEO PAGE ADS
		 */
		$wp_customize->add_section(
			'vtt_ads_single_video_page',
			array(
				'priority'       => 50,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Single Video Page', 'wpst' ),
				'description'    => __( 'Add your own ads on single video pages.', 'wpst' ),
				'panel'          => 'vtt_ads',
			)
		);

		// Single video page Ad in player 1
		$wp_customize->add_setting(
			'ads_single_video_page_in_player_1',
			array(
				'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_ads_single_video_page_in_player_1',
				array(
					'label'    => esc_html__( 'In player ad zone 1', 'wpst' ),
					'section'  => 'vtt_ads_single_video_page',
					'settings' => 'ads_single_video_page_in_player_1',
					'type'     => 'textarea',
				)
			)
		);

		// Single video page Ad in player 2
		$wp_customize->add_setting(
			'ads_single_video_page_in_player_2',
			array(
				'default'   => '',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_ads_single_video_page_in_player_2',
				array(
					'label'    => esc_html__( 'In player ad zone 2', 'wpst' ),
					'section'  => 'vtt_ads_single_video_page',
					'settings' => 'ads_single_video_page_in_player_2',
					'type'     => 'textarea',
				)
			)
		);

		// Single video page Ad under player
		$wp_customize->add_setting(
			'ads_single_video_page_under_player',
			array(
				'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-3.png"></a>',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_ads_single_video_page_under_player',
				array(
					'label'    => esc_html__( 'Under player ad zone', 'wpst' ),
					'section'  => 'vtt_ads_single_video_page',
					'settings' => 'ads_single_video_page_under_player',
					'type'     => 'textarea',
				)
			)
		);

		// Single video page Ad beside player 1
		$wp_customize->add_setting(
			'ads_single_video_page_beside_player_1',
			array(
				'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_ads_single_video_page_beside_player_1',
				array(
					'label'    => esc_html__( 'Beside player ad zone 1', 'wpst' ),
					'section'  => 'vtt_ads_single_video_page',
					'settings' => 'ads_single_video_page_beside_player_1',
					'type'     => 'textarea',
				)
			)
		);

		// Single video page Ad beside player 2
		$wp_customize->add_setting(
			'ads_single_video_page_beside_player_2',
			array(
				'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_ads_single_video_page_beside_player_2',
				array(
					'label'    => esc_html__( 'Beside player ad zone 2', 'wpst' ),
					'section'  => 'vtt_ads_single_video_page',
					'settings' => 'ads_single_video_page_beside_player_2',
					'type'     => 'textarea',
				)
			)
		);

		// Single video page bottom ad
		$wp_customize->add_setting(
			'ads_single_video_page_bottom',
			array(
				'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-4.png"></a>',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_ads_single_video_page_bottom',
				array(
					'label'    => esc_html__( 'Ad zone at the bottom of the page', 'wpst' ),
					'section'  => 'vtt_ads_single_video_page',
					'settings' => 'ads_single_video_page_bottom',
					'type'     => 'textarea',
				)
			)
		);

		/**
		 * CATEGORY PAGE ADS
		 */
		$wp_customize->add_section(
			'vtt_ads_category_page',
			array(
				'priority'       => 50,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Category Page', 'wpst' ),
				'description'    => __( 'Add your own ads on the Category pages.', 'wpst' ),
				'panel'          => 'vtt_ads',
			)
		);

		// Category page Ad inside videos list
		// $wp_customize->add_setting(
		// 	'ads_category_page_inside_list',
		// 	array(
		// 		'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>',
		// 		'transport' => 'refresh',
		// 	)
		// );

		// $wp_customize->add_control(
		// 	new WP_Customize_Control(
		// 		$wp_customize,
		// 		'vtt_ads_category_page_inside_list',
		// 		array(
		// 			'label'    => esc_html__( 'Ad zone inside videos list', 'wpst' ),
		// 			'section'  => 'vtt_ads_category_page',
		// 			'settings' => 'ads_category_page_inside_list',
		// 			'type'     => 'textarea',
		// 		)
		// 	)
		// );

		// Category page bottom ad
		$wp_customize->add_setting(
			'ads_category_page_bottom',
			array(
				'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-4.png"></a>',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_ads_category_page_bottom',
				array(
					'label'    => esc_html__( 'Ad zone at the bottom of the page', 'wpst' ),
					'section'  => 'vtt_ads_category_page',
					'settings' => 'ads_category_page_bottom',
					'type'     => 'textarea',
				)
			)
		);

		/**
		 * TAG PAGE ADS
		 */
		$wp_customize->add_section(
			'vtt_ads_tag_page',
			array(
				'priority'       => 50,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Tag Page', 'wpst' ),
				'description'    => __( 'Add your own ads on the Tag pages.', 'wpst' ),
				'panel'          => 'vtt_ads',
			)
		);

		// Tag page Ad inside videos list
		// $wp_customize->add_setting(
		// 	'ads_tag_page_inside_list',
		// 	array(
		// 		'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>',
		// 		'transport' => 'refresh',
		// 	)
		// );

		// $wp_customize->add_control(
		// 	new WP_Customize_Control(
		// 		$wp_customize,
		// 		'vtt_ads_tag_page_inside_list',
		// 		array(
		// 			'label'    => esc_html__( 'Ad zone inside videos list', 'wpst' ),
		// 			'section'  => 'vtt_ads_tag_page',
		// 			'settings' => 'ads_tag_page_inside_list',
		// 			'type'     => 'textarea',
		// 		)
		// 	)
		// );

		// Tag page bottom ad
		$wp_customize->add_setting(
			'ads_tag_page_bottom',
			array(
				'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-4.png"></a>',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_ads_tag_page_bottom',
				array(
					'label'    => esc_html__( 'Ad zone at the bottom of the page', 'wpst' ),
					'section'  => 'vtt_ads_tag_page',
					'settings' => 'ads_tag_page_bottom',
					'type'     => 'textarea',
				)
			)
		);

		/**
		 * ACTOR PAGE ADS
		 */
		$wp_customize->add_section(
			'vtt_ads_actor_page',
			array(
				'priority'       => 50,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Actor Page', 'wpst' ),
				'description'    => __( 'Add your own ads on the actor pages.', 'wpst' ),
				'panel'          => 'vtt_ads',
			)
		);

		// actor page Ad inside videos list
		// $wp_customize->add_setting(
		// 	'ads_actor_page_inside_list',
		// 	array(
		// 		'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>',
		// 		'transport' => 'refresh',
		// 	)
		// );

		// $wp_customize->add_control(
		// 	new WP_Customize_Control(
		// 		$wp_customize,
		// 		'vtt_ads_actor_page_inside_list',
		// 		array(
		// 			'label'    => esc_html__( 'Ad zone inside videos list', 'wpst' ),
		// 			'section'  => 'vtt_ads_actor_page',
		// 			'settings' => 'ads_actor_page_inside_list',
		// 			'type'     => 'textarea',
		// 		)
		// 	)
		// );

		// actor page bottom ad
		$wp_customize->add_setting(
			'ads_actor_page_bottom',
			array(
				'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-4.png"></a>',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_ads_actor_page_bottom',
				array(
					'label'    => esc_html__( 'Ad zone at the bottom of the page', 'wpst' ),
					'section'  => 'vtt_ads_actor_page',
					'settings' => 'ads_actor_page_bottom',
					'type'     => 'textarea',
				)
			)
		);

		/**
		 * SEARCH RESULT PAGE ADS
		 */
		$wp_customize->add_section(
			'vtt_ads_search_result_page',
			array(
				'priority'       => 50,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Search Result Page', 'wpst' ),
				'description'    => __( 'Add your own ads on the search result pages.', 'wpst' ),
				'panel'          => 'vtt_ads',
			)
		);

		// Search result page Ad inside videos list
		// $wp_customize->add_setting(
		// 	'ads_search_result_page_inside_list',
		// 	array(
		// 		'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>',
		// 		'transport' => 'refresh',
		// 	)
		// );

		// $wp_customize->add_control(
		// 	new WP_Customize_Control(
		// 		$wp_customize,
		// 		'vtt_ads_search_result_page_inside_list',
		// 		array(
		// 			'label'    => esc_html__( 'Ad zone inside videos list', 'wpst' ),
		// 			'section'  => 'vtt_ads_search_result_page',
		// 			'settings' => 'ads_search_result_page_inside_list',
		// 			'type'     => 'textarea',
		// 		)
		// 	)
		// );

		// Search result page bottom ad
		$wp_customize->add_setting(
			'ads_search_result_page_bottom',
			array(
				'default'   => '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-4.png"></a>',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_ads_search_result_page_bottom',
				array(
					'label'    => esc_html__( 'Ad zone at the bottom of the page', 'wpst' ),
					'section'  => 'vtt_ads_search_result_page',
					'settings' => 'ads_search_result_page_bottom',
					'type'     => 'textarea',
				)
			)
		);

		/*****************************/
		/****** NEW SECTION SEO ******/
		/*****************************/
		$wp_customize->add_panel(
			'vtt_seo',
			array(
				'priority'       => 60,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'SEO', 'wpst' ),
				'description'    => __( 'Several settings pertaining my theme', 'wpst' ),
			)
		);

		/**
		 * HOME
		 */
		$wp_customize->add_section(
			'vtt_seo_home',
			array(
				'priority'       => 60,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Homepage', 'wpst' ),
				'description'    => __( 'Rewrite content on homepage to improve SEO.', 'wpst' ),
				'panel'          => 'vtt_seo',
			)
		);

		// HOME SEO Title
		$wp_customize->add_setting(
			'seo_home_title',
			array(
				'default'   => get_bloginfo( 'description' ),
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_seo_home_title',
				array(
					'label'    => __( 'Home Title', 'wpst' ),
					'section'  => 'vtt_seo_home',
					'settings' => 'seo_home_title',
					'type'     => 'text',
				)
			)
		);

		// HOME SEO Description
		$wp_customize->add_setting(
			'seo_home_description',
			array(
				'default'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			// new WP_Customize_Control(
			new Text_Editor_Custom_Control(
				$wp_customize,
				'vtt_seo_home_description',
				array(
					'label'    => __( 'Home Description', 'wpst' ),
					'section'  => 'vtt_seo_home',
					'settings' => 'seo_home_description',
					'type'     => 'textarea',
				)
			)
		);

		/**
		 * CAT
		 */
		$wp_customize->add_section(
			'vtt_seo_video_cat',
			array(
				'priority'       => 60,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Category pages', 'wpst' ),
				'description'    => __( 'Rewrite content on category pages to improve SEO.', 'wpst' ) . vtt_add_variables(
					array(
						'%%cat%%' => __( 'display category name.', 'wpst' ),
					)
				),
				'panel'          => 'vtt_seo',
			)
		);

		// Cat SEO Title
		$wp_customize->add_setting(
			'seo_video_cat_title',
			array(
				'default'   => '%%cat%% porn videos',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_seo_video_cat_title',
				array(
					'label'    => __( 'Category Title', 'wpst' ),
					'section'  => 'vtt_seo_video_cat',
					'settings' => 'seo_video_cat_title',
					'type'     => 'text',
				)
			)
		);
		// Cat SEO Description
		$wp_customize->add_setting(
			'seo_video_cat_description',
			array(
				'default'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new Text_Editor_Custom_Control(
				$wp_customize,
				'vtt_seo_video_cat_description',
				array(
					'label'    => __( 'Category Description', 'wpst' ),
					'section'  => 'vtt_seo_video_cat',
					'settings' => 'seo_video_cat_description',
					'type'     => 'textarea',
				)
			)
		);

		/**
		 * TAG
		 */
		$wp_customize->add_section(
			'vtt_seo_video_tag',
			array(
				'priority'       => 60,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Tag pages', 'wpst' ),
				'description'    => __( 'Rewrite content on tag pages to improve SEO.', 'wpst' ) . vtt_add_variables(
					array(
						'%%tag%%' => __( 'display tag name.', 'wpst' ),
					)
				),
				'panel'          => 'vtt_seo',
			)
		);

		// Tag SEO Title
		$wp_customize->add_setting(
			'seo_video_tag_title',
			array(
				'default'   => '%%tag%% porn videos',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_seo_video_tag_title',
				array(
					'label'    => __( 'Tag Title', 'wpst' ),
					'section'  => 'vtt_seo_video_tag',
					'settings' => 'seo_video_tag_title',
					'type'     => 'text',
				)
			)
		);
		// Tag SEO Description
		$wp_customize->add_setting(
			'seo_video_tag_description',
			array(
				'default'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new Text_Editor_Custom_Control(
				$wp_customize,
				'vtt_seo_video_tag_description',
				array(
					'label'    => __( 'Tag Description', 'wpst' ),
					'section'  => 'vtt_seo_video_tag',
					'settings' => 'seo_video_tag_description',
					'type'     => 'textarea',
				)
			)
		);

		/**
		 * ACTOR
		 */
		$wp_customize->add_section(
			'vtt_seo_video_actor',
			array(
				'priority'       => 60,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Actor pages', 'wpst' ),
				'description'    => __( 'Rewrite content on actor pages to improve SEO.', 'wpst' ) . vtt_add_variables(
					array(
						'%%cat%%' => __( 'display actor name.', 'wpst' ),
					)
				),
				'panel'          => 'vtt_seo',
			)
		);

		// Cat SEO Title
		$wp_customize->add_setting(
			'seo_video_actor_title',
			array(
				'default'   => '%%actor%% porn videos',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_seo_video_actor_title',
				array(
					'label'    => __( 'Actor Title', 'wpst' ),
					'section'  => 'vtt_seo_video_actor',
					'settings' => 'seo_video_actor_title',
					'type'     => 'text',
				)
			)
		);
		// Cat SEO Description
		$wp_customize->add_setting(
			'seo_video_actor_description',
			array(
				'default'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new Text_Editor_Custom_Control(
				$wp_customize,
				'vtt_seo_video_actor_description',
				array(
					'label'    => __( 'Actor Description', 'wpst' ),
					'section'  => 'vtt_seo_video_actor',
					'settings' => 'seo_video_actor_description',
					'type'     => 'textarea',
				)
			)
		);

		/**
		 * SEARCH
		 */
		$wp_customize->add_section(
			'vtt_seo_search',
			array(
				'priority'       => 60,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Search result pages', 'wpst' ),
				'description'    => __( 'Rewrite content on search result pages.', 'wpst' ) . vtt_add_variables(
					array(
						'%%search_tag%%'    => __( 'display search tag name.', 'wpst' ),
						'%%search_number%%' => __( 'display search result number.', 'wpst' ),
					)
				),
				'panel'          => 'vtt_seo',
			)
		);

		// Search SEO Title
		$wp_customize->add_setting(
			'seo_search_title',
			array(
				'default'   => '%%search_number%% videos found with %%search_tag%%',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_seo_search_title',
				array(
					'label'    => __( 'Search Title', 'wpst' ),
					'section'  => 'vtt_seo_search',
					'settings' => 'seo_search_title',
					'type'     => 'text',
				)
			)
		);
		// Search SEO Description
		$wp_customize->add_setting(
			'seo_search_description',
			array(
				'default'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new Text_Editor_Custom_Control(
				$wp_customize,
				'vtt_seo_search_description',
				array(
					'label'    => __( 'Search Description', 'wpst' ),
					'section'  => 'vtt_seo_search',
					'settings' => 'seo_search_description',
					'type'     => 'textarea',
				)
			)
		);

		/***********************************/
		/****** NEW SECTION COPYRIGHT ******/
		/***********************************/
		$wp_customize->add_section(
			'vtt_copyright',
			array(
				'title'    => __( 'Copyright', 'wpst' ),
				'priority' => 70,
			)
		);

		// Copyright Text
		$wp_customize->add_setting(
			'copyright_content',
			array(
				'default'   => date( 'Y' ) . ' - ' . get_bloginfo( 'name' ) . '. ' . esc_html__( 'All rights reserved. Powered by WP-Script.com', 'wpst' ),
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new Text_Editor_Custom_Control(
				$wp_customize,
				'vtt_copyright_content',
				array(
					'label'    => __( 'Content', 'wpst' ),
					'section'  => 'vtt_copyright',
					'settings' => 'copyright_content',
					'type'     => 'textarea',
				)
			)
		);

		/*********************************/
		/****** NEW SECTION SCRIPTS ******/
		/*********************************/
		$wp_customize->add_section(
			'vtt_scripts_section',
			array(
				'title'    => __( 'Scripts', 'wpst' ),
				'priority' => 80,
			)
		);

		// Google Analytics
		$wp_customize->add_setting(
			'google_analytics_code',
			array(
				'default'   => '',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_google_analytics_code',
				array(
					'label'    => esc_html__( 'Google Analytics', 'wpst' ),
					'section'  => 'vtt_scripts_section',
					'settings' => 'google_analytics_code',
					'type'     => 'textarea',
					'description' => __( 'Paste here your Google Analytics tracking code.', 'wpst' )
				)
			)
		);

		// Meta verification
		$wp_customize->add_setting(
			'meta_verification_code',
			array(
				'default'   => '',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_meta_verification_code',
				array(
					'label'    => esc_html__( 'Meta Verification', 'wpst' ),
					'section'  => 'vtt_scripts_section',
					'settings' => 'meta_verification_code',
					'type'     => 'textarea',
					'description' => __( 'Paste here meta codes for domain verification.', 'wpst' )
				)
			)
		);

		// Other scripts
		$wp_customize->add_setting(
			'other_script_codes',
			array(
				'default'   => '',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'vtt_other_script_codes',
				array(
					'label'    => esc_html__( 'Other scripts', 'wpst' ),
					'section'  => 'vtt_scripts_section',
					'settings' => 'other_script_codes',
					'type'     => 'textarea',
					'description' => __( 'Paste here your other scripts (eg. popunder script)', 'wpst' )
				)
			)
		);
	}
} // endif function_exists( 'vtt_theme_customize_register' ).
add_action( 'customize_register', 'vtt_theme_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
if ( ! function_exists( 'vtt_customize_preview_js' ) ) {
	/**
	 * Setup JS integration for live previewing.
	 */
	function vtt_customize_preview_js() {
		wp_enqueue_script(
			'vtt_customizer',
			get_template_directory_uri() . '/js/customizer.js',
			array( 'customize-preview' ),
			'1.0.5',
			true
		);
	}
}
// add_action( 'customize_preview_init', 'vtt_customize_preview_js' );

function vtt_customize_css() {
	$main_color = get_theme_mod( 'main_color', '#de2600' );
	?>
	<style type="text/css">
		.video-block .video-debounce-bar {
			background-color: <?php echo $main_color; ?>;
		}
		a,
		a:hover,
		.logo-word-1,
		ul#menu-footer-menu li a:hover,
		.required,
		#show-sharing-buttons:hover i,
		.tags-letter-block .tag-items .tag-item a:hover,
		.post-like a:hover i {
			color: <?php echo $main_color; ?>;
		}
		.btn,
		.btn-primary,
		.post-navigation a,
		.btn:hover,
		.btn-grey-light:hover,
		.membership ul li a:hover,
		#filters .filters-options span a:hover {
			background-color: <?php echo $main_color; ?>!important;
			color: <?php echo vtt_get_brightness( $main_color, '#FFFFFF', '#000000' ); ?>!important;
			border-color: <?php echo $main_color; ?>!important;
		}
		@-webkit-keyframes glowing {
			0% { border-color: <?php echo $main_color; ?>; -webkit-box-shadow: 0 0 3px <?php echo $main_color; ?>; }
			50% { -webkit-box-shadow: 0 0 20px <?php echo $main_color; ?>; }
			100% { border-color: <?php echo $main_color; ?>; -webkit-box-shadow: 0 0 3px <?php echo $main_color; ?>; }
		}

		@-moz-keyframes glowing {
			0% { border-color: <?php echo $main_color; ?>; -moz-box-shadow: 0 0 3px <?php echo $main_color; ?>; }
			50% { -moz-box-shadow: 0 0 20px <?php echo $main_color; ?>; }
			100% { border-color: <?php echo $main_color; ?>; -moz-box-shadow: 0 0 3px <?php echo $main_color; ?>; }
		}

		@-o-keyframes glowing {
			0% { border-color: <?php echo $main_color; ?>; box-shadow: 0 0 3px <?php echo $main_color; ?>; }
			50% { box-shadow: 0 0 20px <?php echo $main_color; ?>; }
			100% { border-color: <?php echo $main_color; ?>; box-shadow: 0 0 3px <?php echo $main_color; ?>; }
		}

		@keyframes glowing {
			0% { border-color: <?php echo $main_color; ?>; box-shadow: 0 0 3px <?php echo $main_color; ?>; }
			50% { box-shadow: 0 0 20px <?php echo $main_color; ?>; }
			100% { border-color: <?php echo $main_color; ?>; box-shadow: 0 0 3px <?php echo $main_color; ?>; }
		}

	</style>
	<?php
}
add_action( 'wp_head', 'vtt_customize_css' );

function vtt_get_brightness( $hex, $light_color, $dark_color ) {
	// returns brightness value from 0 to 255
	// strip off any leading #
	$hex = str_replace( '#', '', $hex );

	$c_r = hexdec( substr( $hex, 0, 2 ) );
	$c_g = hexdec( substr( $hex, 2, 2 ) );
	$c_b = hexdec( substr( $hex, 4, 2 ) );

	return ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000 > 100 ? $dark_color : $light_color;
}


function vtt_hex2rgba($color, $opacity = false) {
	$default = 'rgb(0,0,0)';
	//Return default if no color provided
	if(empty($color))
          return $default;

	//Sanitize $color if "#" is provided
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;
        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }

        //Return rgb(a) color string
        return $output;
}


function vtt_add_variables( $variables ) {
	$output  = '<p style="margin: 10px 0;">Available variable:</p>';
	$output .= '<ul style="padding: 0; margin: 0;">';
	foreach ( $variables as $key => $description ) {
		$output .= '<li style="list-style: none;"><code>' . $key . '</code>: ' . $description . '</li>';
	}
	$output .= '</ul>';
	return $output;
}


/**
 * Custom control for arbitarty text, extend the WP customizer
 *
 */
if ( ! class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class Customizer_Library_Help_Text extends WP_Customize_Control {

    /**
     * Render the control's content.
     *
     */
    public function render_content() {

        echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
        echo '<p class="description">' . $this->description . '</p>';
        echo '<hr />';

    }

}
