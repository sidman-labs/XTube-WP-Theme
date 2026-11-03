
<?php
	add_filter(
		'theme_mod_seo_video_title',
		function( $value ) {
			$video_title = get_the_title();
			$value      = str_replace( '%%video_title%%', $video_title, $value );
			return $value;
		}
	);
	add_filter(
		'theme_mod_seo_video_tracking_button',
		function( $value ) {
			$video_title = get_the_title();
			$value      = str_replace( '%%video_title%%', $video_title, $value );
			return $value;
		}
	);
	$video_title        = get_the_title();
	$video_tracking_url = get_post_meta($post->ID, 'tracking_url', true);
	if ( has_post_thumbnail() ) {
		$video_thumb_url = get_the_post_thumbnail_url(get_the_id(), 'video-thumb');
	}else{
		$video_thumb_url = get_post_meta( get_the_ID(), 'thumb', true );
	}

	//Above related videos Ads
	$ads = array(
		'under_player' 		=> vtt_render_shortcodes( get_theme_mod( 'ads_single_video_page_under_player', '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-3.png"></a>' ) ),
		'beside_player_1' 	=> vtt_render_shortcodes( get_theme_mod( 'ads_single_video_page_beside_player_1', '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>' ) ),
		'beside_player_2' 	=> vtt_render_shortcodes( get_theme_mod( 'ads_single_video_page_beside_player_2', '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>' ) ),
		'page_bottom'  		=> vtt_render_shortcodes( get_theme_mod( 'ads_single_video_page_bottom', '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-4.png"></a>' ) ),
	);

// if(have_posts()) : while(have_posts()) : the_post();

$has_vtt_beside_player_ad_zone_desktop = $ads['beside_player_1'] || $ads['beside_player_2']; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
	<section class="single-video-player">
		<div class="container">
			<div class="video-title">
				<h1><?php echo get_theme_mod( 'seo_video_title', $video_title ); ?></h1>
			</div>
			<div class="video-cat-tag">
				<?php $postcats = get_the_category(); ?>
				<?php if( !empty($postcats) ) : ?>
					<?php foreach($postcats as $cat) {
						$cat_list[] = '<a class="btn btn-black" href="' . get_category_link( $cat->term_id ) . '" title="' . $cat->name . '">' . $cat->name . ' <span>' . $cat->count . '</span></a>';
					} echo implode( '', $cat_list ); ?>
				<?php endif; ?>
				<?php $actors = wp_get_post_terms($post->ID, "actors"); ?>
				<?php if( !empty($actors) ) : ?>
					<?php foreach($actors as $actor) {
						$actor_list[] = '<a class="btn btn-primary" href="' . get_term_link($actor->term_id) . '" title="' . $actor->name . '">' . $actor->name . '</a>';
					} echo implode( ' ', $actor_list ); ?>
				<?php endif; ?>
				<?php $posttags = get_the_tags(); ?>
				<?php if( !empty($posttags) ) : ?>
					<?php foreach($posttags as $tag) {
						$tag_list[] = '<a class="btn btn-grey" href="' . get_tag_link( $tag->term_id ) . '" title="' . $tag->name . '">' . $tag->name . '</a>';
					} echo implode(' ', $tag_list ); ?>
				<?php endif; ?>
			</div>
			<div class="d-flex justify-content-between">
				<div class="video-left">
					<div class="video-wrapper">
						<?php get_template_part( 'loop-templates/content', 'video-player' ); ?>
					</div>
					<?php if( get_post_meta( $post->ID, 'unique_ad_under_player', true ) != '' ) : ?>
						<div class="happy-under-player">
							<?php echo get_post_meta( $post->ID, 'unique_ad_under_player', true ); ?>
						</div>
					<?php elseif($ads['under_player']) : ?>
						<div class="happy-under-player">
							<?php echo $ads['under_player']; ?>
						</div>
					<?php endif; ?>
					<div class="videos-infos d-flex align-items-center">
						<div id="video-views"><span class="views-number"></span> <?php esc_html_e('views', 'wpst'); ?></div>
						<div id="rating">
							<span id="video-rate"><?php echo vtt_getPostLikeLink(get_the_ID()); ?></span>
							<?php $is_rated_yet = vtt_getPostLikeRate(get_the_ID()) === false ? " not-rated-yet" : ''; ?>
						</div>
						<div class="video-actions-header">
							<?php $content = get_the_content(); ?>
							<?php if($content) : ?>
								<button class="tab-link active about btn-grey-light" data-tab-id="video-about"><i class="fa fa-info-circle"></i> <span class="d-none d-md-inline-block"><?php esc_html_e('About', 'wpst'); ?></span></button>
							<?php endif; ?>
							<button class="tab-link comment btn-grey-light" data-tab-id="video-comment"><i class="fa fa-comment"></i> <span class="d-none d-md-inline-block"><?php esc_html_e('Comment', 'wpst'); ?></span></button>
							<button class="tab-link share btn-grey-light" data-tab-id="video-share"><i class="fa fa-share-alt"></i> <span class="d-none d-md-inline-block"><?php esc_html_e('Share', 'wpst'); ?></span></button>
						</div>
					</div>

					<div class="video-actions-content">
						<div class="row no-gutters">
							<div class="col-12">
								<div class="tab-content">
									<?php if($content) : ?>
										<div class="video-content-row" id="video-about">
											<?php $video_in_content = false;
											$video_code = array();
											if( preg_match("/\[video.+\]/", get_the_content(), $video_code) ){
												$video_in_content = "/\[video.+\]/";
											}
											elseif( preg_match("/<iframe.+<\/iframe>/", get_the_content(), $video_code) ){
												$video_in_content = "/<iframe.+<\/iframe>/";
											}
											elseif( preg_match("/<video.+<\/video>/", get_the_content(), $video_code) ){
												$video_in_content = "/<video.+<\/video>/";
											}
											elseif( preg_match("/<object.+<\/object>/", get_the_content(), $video_code) ){
												$video_in_content = "/<object.+<\/object>/";
											}
											elseif( preg_match("/https:\/\/www.youtube.com\/watch\?v=.+?\b/", get_the_content(), $video_code) ){
												$video_in_content = "/https:\/\/www.youtube.com\/watch\?v=.+?\b/";
											}
											if( !empty($content) ) : ?>
												<div class="video-description">
													<div class="desc">
														<?php
														$embed_code = get_post_meta($post->ID, 'embed', true);
														$video_url = get_post_meta($post->ID, 'video_url', true);
														$video_shortcode = get_post_meta($post->ID, 'shortcode', true);
														if( $video_in_content != false && ($embed_code == '' && $video_shortcode == '' && $video_url == '' )  ):
																$content = preg_replace($video_in_content, "", get_the_content());
														endif;
														echo apply_filters('the_content', $content); ?>
													</div>
												</div>
											<?php endif; ?>
										</div>
									<?php endif; ?>
									<div id="video-comment" class="video-comments">
										<div class="row">
											<div class="col-12 col-md-8">
												<?php comments_template(); ?>
											</div>
										</div>
									</div>
									<?php get_template_part( 'template-parts/content', 'share-buttons' ); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php if(!wp_is_mobile()) : ?>
					<div class="video-sidebar d-none d-md-block">
						<?php if ( $has_vtt_beside_player_ad_zone_desktop ) : ?>
							<div class="happy-player-beside">
								<div class="zone-1"><?php echo $ads['beside_player_1']; ?></div>
								<div class="zone-2"><?php echo $ads['beside_player_2']; ?></div>
							</div>
						<?php endif; ?>

						<?php if ( function_exists('dynamic_sidebar') && is_active_sidebar('video_sidebar') ) {
							dynamic_sidebar('Video Sidebar');
						} ?>
					</div>
				<?php endif; ?>
			</div>
			<?php
				$related = get_posts( array(
					'category__in' => wp_get_post_categories($post->ID),
					// 'numberposts' => xbox_get_field_value( 'vtt-options', 'related-videos-number' ),
					'numberposts' => '10',
					'post__not_in' => array($post->ID),
					'orderby' => 'rand',
					'order'    => 'ASC',
					)
				);
				if ( $related ) :
			?>
			<div class="related-videos">
				<div class="row no-gutters">
					<?php
						foreach( $related as $post ) {
							setup_postdata($post);
							get_template_part( 'loop-templates/loop', 'video' );
						}
					?>
				</div>
			</div>
			<?php wp_reset_postdata(); endif; ?>
		</div>
	</section>
</article>

<?php if ( $ads['page_bottom'] ) : ?>
	<div class="happy-section"><?php echo $ads['page_bottom']; ?></div>
<?php endif; ?>

<?php // endwhile; endif; ?>
