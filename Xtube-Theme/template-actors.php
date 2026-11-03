<?php
/**
 * Template Name: Actors
 **/
get_header(); ?>
<?php
	// $sidebar_pos = get_theme_mod( 'sidebar_position', 'left' );
	$ads         = array(
		// 'inside_list' => vtt_render_shortcodes( get_theme_mod( 'ads_actor_page_inside_list', '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>' ) ),
		'page_bottom'  => vtt_render_shortcodes( get_theme_mod( 'ads_actor_page_bottom', '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-4.png"></a>' ) ),
	);
	?>

<div class="wrapper cat-wrapper">    
	<div class="container">
        <h1><?php the_title(); ?></h1>
        <div class="video-loop">
            <div class="row no-gutters">
                <?php
                if ( have_posts() ) :
                    $video_counter = 0;
                    // set_query_var( 'video_loop_has_ad', ( '' !== $ads['inside_list'] ) );
                    while ( have_posts() ) :
                        the_post();
                        //get_query_var to get page id from url
                        $page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                        // number of categories to show per-page
                        $per_page = 60;
                        //count total number of terms related to passed taxonomy
                        $actors           = get_terms( 'actors' );
                        $number_of_series = is_array( $actors ) ? count( $actors ) : 0;
                        $offset           = ( $page - 1 ) * $per_page;
                        $max_num_pages    = (int) ceil( $number_of_series / $per_page );

                        if ( $page > $max_num_pages ) {
                            global $wp_query;
                            $wp_query->set_404();
                            status_header( 404 );
                            get_template_part( 404 );
                            exit();
                        }
                        $terms = get_terms(
                            array(
                                'taxonomy'   => 'actors',
                                'hide_empty' => true,
                                'number'     => $posts_per_page,
                                'offset'     => $offset,
                            )
                        );
                        $count = is_array( $terms ) ? count( $terms ) : 0;

                        if ( $count > 0 ) :
                            foreach ( $terms as $term ) {
                                $args = array(
                                    'post_type'      => 'post',
                                    'posts_per_page' => 1,
                                    'show_count'     => 1,
                                    'orderby'        => 'rand',
                                    'post_status'    => 'publish',
                                    'tax_query'      => array(
                                        array(
                                            'taxonomy' => 'actors',
                                            'field'    => 'slug',
                                            'terms'    => $term->slug,
                                        ),
                                    ),
                                );
                                $video_from_actor = new WP_Query( $args );
                                if ( $video_from_actor->have_posts() ) {
                                    $video_from_actor->the_post();
                                }
                                $video_counter++;
                                ?>
                                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                                    <div class="video-block video-block-cat">
                                        <a class="thumb" href="<?php echo bloginfo( 'url' ); ?>?actors=<?php echo $term->slug; ?>" title="<?php echo $term->name; ?>">
                            <?php
                                $image_id  = get_term_meta( $term->term_id, 'actors-image-id', true );
                                $cat_image = wp_get_attachment_image( $image_id, 'video-thumb' );
                                if ( $cat_image ){
                                    echo $cat_image;
                                }else{
                                    $thumb_url = '';
                                    if ( has_post_thumbnail() ) {
                                        $thumb_url = get_the_post_thumbnail_url( get_the_id(), 'video-thumb' );
                                    } elseif ( '' !== get_post_meta( get_the_ID(), 'thumb', true ) ) {
                                        $thumb_url = get_post_meta( get_the_ID(), 'thumb', true );
                                    }
                                    if ( empty( $thumb_url ) ) {
                                        $thumb_url = get_stylesheet_directory_uri() . '/img/no-thumb.png';
                                    }
                                    echo '<img class="video-img img-fluid" data-src="' . esc_url( $thumb_url ) . '">';
                                }
                                ?>
                                        </a>
                                        <a class="infos" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                                            <span class="title"><?php echo $term->name; ?></span>
                                            <div class="video-datas">
                                                <?php echo intval( $term->count ) ?> videos
                                            </div>
                                        </a>
                                    </div>
                                </div>
                        <?php }
                            vtt_pagination( null, ceil( $number_of_series / $posts_per_page ) );
                        endif;
                    endwhile;
                endif;
                ?>
            </div>
        </div>
        <?php wp_reset_postdata(); ?>
	</div>
</div>
<?php if ( $ads['page_bottom'] ) : ?>
	<div class="happy-section"><?php echo $ads['page_bottom']; ?></div>
<?php endif; ?>
<?php
	get_footer();
