<?php
    //Thumbnail
    $thumb = get_post_meta($post->ID, 'thumb', true);
    if ( has_post_thumbnail() ) {
        $thumb_id = get_post_thumbnail_id();
        $thumb_url = wp_get_attachment_image_src($thumb_id, 'video-thumb', true);
        $poster = $thumb_url[0];
    }else{
        $poster = $thumb;
    }

    $post_data = vtt_get_post_data($post->ID);
    $url_image = has_post_thumbnail() ? wp_get_attachment_url( get_post_thumbnail_id($post->ID)) : $thumb;
    $current_url = get_permalink( $post->ID );
    $current_title = $post_data->post_title;
    $current_desc = '';
    if( !empty($post_data->post_content) ){
        $current_desc = esc_html($post_data->post_content);
    }else{
        $current_desc = 'test';
    }
    
?>
<div id="video-share">
    <!-- Twitter -->
    <a target="_blank" href="https://twitter.com/home?status=<?php print $current_url;?>"><i id="twitter" class="fa fa-twitter"></i></a>
    <!-- Reddit -->   
    <a target="_blank" href="http://www.reddit.com/submit?url"><i id="reddit" class="fa fa-reddit-square"></i></a>
    <!-- Google Plus -->    
    <a target="_blank" href="https://plus.google.com/share?url=<?php print $current_url;?>"><i id="googleplus" class="fa fa-google-plus"></i></a>   
    <!-- VK -->
    <script type="text/javascript" src="https://vk.com/js/api/share.js?95" charset="windows-1251"></script>
    <a href="http://vk.com/share.php?url=<?php print $current_url;?>" target="_blank"><i id="vk" class="fa fa-vk"></i></a>
    <!-- Email -->
    <a target="_blank" href="mailto:?subject=&amp;body=<?php the_permalink(); ?>"><i id="email" class="fa fa-envelope"></i></a>
</div>