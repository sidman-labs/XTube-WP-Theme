<?php
$video_loop_has_ad = get_query_var( 'video_loop_has_ad', false );
?>
<div class="col-6 col-md-4 col-lg-3 col-xl-3">
	<div class="video-block video-block-cat">
		<a class="thumb" href="<?php echo get_category_link( get_cat_ID( $category_name ) ); ?>" title="<?php echo $category_name; ?>">
			<?php echo $category_thumb; ?>
		</a>
		<a class="infos" href="<?php echo get_category_link( get_cat_ID( $category_name ) ); ?>" title="<?php the_title(); ?>">
			<span class="title"><?php echo $category_name; ?></span>
			<div class="video-datas">
				<?php echo intval( $category_videos_count ); ?> <?php esc_html_e('videos', 'wpst'); ?>
			</div>
		</a>
	</div>
</div>

