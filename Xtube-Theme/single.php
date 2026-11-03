<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
get_header();

add_filter(
	'theme_mod_seo_related_videos_title',
	function( $value ) {
		$video_title = get_the_title();
		$value      = str_replace( '%%video_title%%', $video_title, $value );
		return $value;
	}
); ?>

<div class="wrapper" id="single-wrapper">	
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			get_template_part( 'loop-templates/content', 'single-video' );
		endwhile;
	endif;
	?>
</div>
<?php get_footer(); ?>
