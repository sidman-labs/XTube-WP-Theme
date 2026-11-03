<?php
/**
 * Template Name: Categories 
 **/
get_header();

$ads         = array(		
    // 'inside_list' => get_theme_mod( 'ads_category_page_inside_list', '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>' ),
    'page_bottom'  => vtt_render_shortcodes( get_theme_mod( 'ads_category_page_bottom', '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-4.png"></a>' ) ),
); ?>

<div class="wrapper cat-wrapper">    
	<div class="container">
        <h1><?php the_title(); ?></h1>
        <div class="video-loop">
            <div class="row no-gutters">				
                <?php
                if ( have_posts() ) :
                    $video_counter = 0;
                    // set_query_var( 'video_loop_has_ad', ( '' !== $ads['inside_list'] ) );
                    while ( have_posts() ) : the_post();                              
                        //get_query_var to get page id from url
                        $page = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
            
                        // number of categories to show per-page
                        $per_page = 50;
            
                        //count total number of terms related to passed taxonomy
            
                        $categories = get_terms('category');
                        $number_of_series = is_array( $categories ) ? count( $categories ) : 0;
                        $offset           = ( $page - 1 ) * $per_page;
                        $max_num_pages    = (int) ceil( $number_of_series / $per_page );
                        if ( $page > $max_num_pages ) {
                            global $wp_query;
                            $wp_query->set_404();
                            status_header( 404 );
                            get_template_part( 404 );
                            exit();
                        }

                        $terms = get_terms( array(
                            'taxonomy' => 'category',
                            'hide_empty' => true,
                            'number' => $per_page,
                            'offset' => $offset
                            )
                        );

                        $count = is_array($terms) ? count($terms) : 0;

                        if ($count > 0) :
                        
                            foreach ( $terms as $term ) {
                                $args = array(
                                    'post_type'   => 'post',
                                    'posts_per_page' => 1,
                                    'show_count'  => 1,
                                    'orderby'     => 'rand',
                                    'post_status' => 'publish',
                                    'tax_query'   => array(
                                        array(
                                            'taxonomy' => 'category',
                                            'field' => 'slug',
                                            'terms' => $term->slug,
                                        )
                                    )
                                );
                                $video_from_category = new WP_Query( $args );
                                if ( $video_from_category->have_posts() ) {
                                    $video_from_category->the_post();
                                }

                                $image_id          = get_term_meta( $term->term_id, 'category-image-id', true );
                                $cat_image         = wp_get_attachment_image( $image_id, 'video-thumb' );
                                $thumb_url         = '';
                                if ( has_post_thumbnail() ) {
                                    $thumb_url = get_the_post_thumbnail_url( get_the_id(), 'video-thumb' );
                                } elseif ( '' !== get_post_meta( get_the_ID(), 'thumb', true ) ) {
                                    $thumb_url = get_post_meta( get_the_ID(), 'thumb', true );
                                }
                                if ( empty( $thumb_url ) ) {
                                    $thumb_url = get_stylesheet_directory_uri() . '/img/no-thumb.png';
                                }

                                $thumb = '';
                                if ( $cat_image ){
                                    $thumb = $cat_image;
                                } else {
                                    $thumb = '<img class="video-img img-fluid" data-src="' . esc_url( $thumb_url ) . '">';
                                }
                                $video_counter++;
                                set_query_var( 'video_counter', $video_counter );
                                set_query_var( 'category_thumb', $thumb );
                                set_query_var( 'category_name', $term->name );
                                set_query_var( 'category_videos_count', $term->count );

                                    get_template_part( 'loop-templates/loop', 'category' );
                                ?>
<?php }
                            vtt_pagination( null, ceil( $number_of_series / $posts_per_page ) );
                        endif;
                    endwhile;
                endif;
                ?>
            </div>
            <?php //vtt_pagination(); ?>
            <?php wp_reset_postdata(); ?>			
        </div>
    </div>	
    <?php if ( $ads['page_bottom'] ) : ?>
		<div class="happy-section"><?php echo $ads['page_bottom']; ?></div>
	<?php endif; ?>		
	<div class="hero">
		<div class="container">
			<div class="hero-text">
				<p><?php echo get_theme_mod( 'seo_home_description', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.' ); ?></p>
			</div>
		</div>
	</div>	
</div>
<?php get_footer(); ?>