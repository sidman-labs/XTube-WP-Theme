<?php
/**
 * Template Name: Tags
 **/
get_header();
$ads = array(	
    // 'inside_list' => get_theme_mod( 'ads_tag_page_inside_list', '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-2.png"></a>' ),
    'page_bottom'  => vtt_render_shortcodes( get_theme_mod( 'ads_tag_page_bottom', '<a href="#!"><img src="' . get_template_directory_uri() . '/img/happy-4.png"></a>' ) ),
); ?>

<style type="text/css">
.letter-group { width: 100%; }
.letter-cell { width: 5%; height: 2em; text-align: center; padding-top: 8px; margin-bottom: 8px; background: #e0e0e0; float: left; }
.row-cells { width: 70%; float: right; margin-right: 180px; }
.title-cell { width: 30%;  float: left; overflow: hidden; margin-bottom: 8px; }
.clear { clear: both; }
</style>

<div class="archive-tags-list">    
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="archive-content clearfix-after template-tags">
                <h1><?php the_title(); ?></h1>
                <?php
                    if ( have_posts() ) :
                        while ( have_posts() ) :
                            the_post();
                            the_content();
                            $terms = get_terms( 'post_tag' );
                            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                                $term_list = array();
                                foreach ( $terms as $term ) {
                                    $term_name    = vtt_removeAccents( $term->name );
                                    $first_letter = mb_substr( $term_name, 0, 1, 'utf8' );
                                    if ( is_numeric( $first_letter ) ) {
                                        $first_letter = '#';
                                    } else {
                                        $first_letter = strtoupper( $first_letter );
                                    }
                                    $term_list[ $first_letter ][] = $term;
                                }
                                unset( $term );
                                foreach ( $term_list as $key => $value ) {
                                    echo '<div class="tags-letter-block"><div class="tag-letter">' . $key . '</div>';
                                    echo '<div class="tag-items">';
                                    foreach ( $value as $term ) {
                                        echo '<div class="tag-item"><a href="' . get_term_link( $term ) . '" title="' . sprintf( __( 'View all post filed under %s', 'my_localization_domain' ), $term->name ) . '">' . $term->name . ' <span class="count">' . $term->count . '</span></a></div>';
                                    }
                                    echo '</div></div><div class="clear"></div>';
                                }
                            }
                ?>
                </div>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ( $ads['page_bottom'] ) : ?>
    <div class="happy-section"><?php echo $ads['page_bottom']; ?></div>
<?php endif; ?>	

<?php
get_footer();
