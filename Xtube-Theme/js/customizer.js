/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Header text color.
	// wp.customize( 'header_textcolor', function( value ) {
	// 	value.bind( function( to ) {
	// 		if ( 'blank' === to ) {
	// 			$( '.site-title a, .site-description' ).css( {
	// 				'clip': 'rect(1px, 1px, 1px, 1px)',
	// 				'position': 'absolute'
	// 			} );
	// 		} else {
	// 			$( '.site-title a, .site-description' ).css( {
	// 				'clip': 'auto',
	// 				'position': 'relative'
	// 			} );
	// 			$( '.site-title a, .site-description' ).css( {
	// 				'color': to
	// 			} );
	// 		}
	// 	} );
	// } );

	// Body background color
	wp.customize( 'body_background_color', function( value ) {
        value.bind( function( to ) {			
            $( 'body, .navbar, #wrapper-footer' ).css( 'background-color', to );
        } );
	});

	// Link color
	// wp.customize( 'link_color', function( value ) {
    //     value.bind( function( to ) {
	// 		$( 'a' ).css( 'color', to );
	// 		$( '.navbar a' ).css( 'color', '#FFFFFF' );
	// 		$( '.btn-primary' ).css( 'border-color', to );
    //     } );
	// });

	// Hero background color
	wp.customize( 'hero_background_color', function( value ) {
        value.bind( function( to ) {			
            $( '.hero' ).css( 'background-color', to );
        } );
	});

	// SEO homepage title
	wp.customize( 'seo_home_title', function( value ) {
		value.bind( function( to ) {
			$( 'h1' ).text( to );
		} );
	} );

	// SEO homepage description
	wp.customize( 'seo_home_description', function( value ) {
		value.bind( function( to ) {
			$( 'p.hero-desc' ).text( to );
		} );
	} );
	
} )( jQuery );
