<?php
$sidebar_pos     = get_theme_mod( 'sidebar_position', 'left' );
$show_duration   = get_theme_mod( 'video_listing_general_show_duration', 'yes' );
$show_title      = get_theme_mod( 'video_listing_general_show_title', 'yes' );
$video_id        = get_the_ID();
$video_display_name = get_the_title();
$video_loop_has_ad  = get_query_var( 'video_loop_has_ad', false );
$video_counter      = get_query_var( 'video_counter', 0 );
$video_order        = '';
$trailer_url        = get_post_meta( get_the_ID(), 'trailer_url', true );

// $video_thumb_url = get_template_directory_uri() . '/img/no-thumb.png'; // Set default thumb.
if ( has_post_thumbnail() ) {
	$video_thumb_url = get_the_post_thumbnail_url( get_the_id(), 'video-thumb' );
} elseif ( '' !== get_post_meta( get_the_ID(), 'thumb', true ) ) {
	$video_thumb_url = get_post_meta( get_the_ID(), 'thumb', true );
}
?>

<?php
if ( $video_loop_has_ad ) :
	$video_order = implode(
		' ',
		array(
			''   => 'order-' . ( $video_counter <= 2 ? '0' : '2' ),
			'sm' => 'order-sm-' . ( $video_counter <= 2 ? '0' : '2' ),
			'md' => 'order-md-' . ( 'none' === $sidebar_pos ? ( $video_counter <= 2 ? '0' : '2' ) : ( $video_counter <= 1 ? '0' : '2' ) ),
			'lg' => 'order-lg-' . ( $video_counter <= 2 ? '0' : '2' ),
			'xl' => 'order-xl-' . ( $video_counter <= 3 ? '0' : '2' ),
		)
	);
endif;
?>

<div class="<?php echo esc_html( $video_order ); ?> col-6 <?php echo 'none' !== $sidebar_pos ? 'col-md-4' : 'col-md-3'; ?> col-lg-3 col-xl-3">
	<div
		class="video-block <?php echo ( '' !== $trailer_url ? 'video-with-trailer' : 'thumbs-rotation' ); ?>"
		data-post-id="<?php echo intval( $video_id ); ?>"
		<?php if ( ! $trailer_url ) : ?>
			data-thumbs="<?php echo vtt_get_multithumbs( $post->ID ); ?>"
		<?php endif; ?>
	>
		<a class="thumb" href="<?php the_permalink(); ?>">
			<div class="video-debounce-bar"></div>
			<div class="lds-dual-ring"></div>
			<?php if($video_thumb_url) : ?>
				<img class="video-img img-fluid" data-src="<?php echo esc_url( $video_thumb_url ); ?>">
			<?php else : ?>
				<div class="no-thumb"></div>
			<?php endif; ?>
			<div class="video-preview"></div>
			<?php if ( vtt_get_video_duration() != '' ) : ?>
				<span class="duration"><?php echo vtt_get_video_duration(); ?></span>
			<?php endif; ?>
		</a>
		<a class="infos" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
			<span class="title"><?php the_title(); ?></span>			
		</a>
		<div class="video-datas">
			<span class="views-number"><?php echo vtt_getPostViews( get_the_ID() ); ?> <?php esc_html_e('views', 'wpst'); ?></span>
			<?php if ( vtt_getPostLikeRate( get_the_ID() ) != false ) : ?>
				<span class="rating"><i class="fa fa-thumbs-up"></i> <?php echo vtt_getPostLikeRate( get_the_ID() ); ?></span>
			<?php endif; ?>				
		</div>
	</div>
</div>

<?php
// Add line breakers for ad zone.
// No breaker needed for <= small devices.
if ( $video_loop_has_ad ) :
	// Sidebar OFF / md + lg case.
	if ( 'none' === $sidebar_pos && 4 === $video_counter ) {
			echo '<div class="d-none d-md-block d-xl-none order-2 w-100"></div>';
	}
	// Sidebar ON / md case.
	if ( 'none' !== $sidebar_pos && 2 === $video_counter ) {
		echo '<div class="d-none d-md-block d-lg-none order-2 w-100"></div>';
	}
	// Sidebar ON / lg case.
	if ( 'none' !== $sidebar_pos && 4 === $video_counter ) {
		echo '<div class="d-none d-lg-block d-xl-none order-2 w-100"></div>';
	}
	// Sidebar ON + OFF / xl case.
	if ( 6 === $video_counter ) {
		echo '<div class="d-none d-xl-block order-2 w-100"></div>';
	}
endif;
