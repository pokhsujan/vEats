<?php 
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb;

$id = get_the_ID();

if( !empty($place_id) ){
    $id = $place_id;
}

$thispost   = get_post( $id );
$this_content =  apply_filters( 'the_content', $thispost->post_content );

$attach_id = get_post_thumbnail_id($id);
$excerpt   = get_the_excerpt($id);
$enable_address = golo_get_option('enable_address', '0');
$enable_status = golo_get_option('enable_status', '0');
$enable_excerpt = golo_get_option('enable_excerpt', '0');
$display_author = golo_get_option('display_author', '1');
$display_review = golo_get_option('display_review', '1');

$no_image_src  = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
$default_image = golo_get_option('default_place_image', '');

if (preg_match('/\d+x\d+/', $custom_place_image_size)) {
    $image_sizes = explode('x', $custom_place_image_size);
    $width		 = $image_sizes[0];
    $height		 = $image_sizes[1];
    $image_src 	 = golo_image_resize_id($attach_id, $width, $height, true);
    
    if( $default_image != '' )
    {
        if( is_array($default_image) && $default_image['url'] != '' )
        {
            $resize = golo_image_resize_url($default_image['url'], $width, $height, true);
            if ($resize != null && is_array($resize)) {
                $no_image_src = $resize['url'];
            }
        }
    }
} else {

    if (!in_array($custom_place_image_size, array('full', 'thumbnail'))) {
        $custom_place_image_size = 'full';
    }

    $image_src = wp_get_attachment_image_src($attach_id, $custom_place_image_size);
    if ($image_src && !empty($image_src[0])) {
        $image_src = $image_src[0];
    }

    if (!empty($image_src)) {
        list($width, $height) = getimagesize($image_src);
    }

    if($default_image != '')
    {
        if(is_array($default_image) && $default_image['url'] != '')
        {
            $no_image_src = $default_image['url'];
        }
    }
}

if ($image_src == '') {
    $image_src = $no_image_src;
}

$place_meta_data = get_post_custom($id);

$place_address  = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_address']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_address'][0] : '';
$price_short    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_short'][0] : '';
$price_unit    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_unit']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_unit'][0] : '';
$price_range    = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_price_range'][0] : '';
$place_featured = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_featured']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_featured'][0] : '0';
$place_logged   = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_logged']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_logged'][0] : '0';

$opening_monday_time    = get_post_meta($id, GOLO_METABOX_PREFIX . 'opening_monday_time', true);
$opening_tuesday_time   = get_post_meta($id, GOLO_METABOX_PREFIX . 'opening_tuesday_time', true);
$opening_wednesday_time = get_post_meta($id, GOLO_METABOX_PREFIX . 'opening_wednesday_time', true);
$opening_thursday_time  = get_post_meta($id, GOLO_METABOX_PREFIX . 'opening_thursday_time', true);
$opening_friday_time    = get_post_meta($id, GOLO_METABOX_PREFIX . 'opening_friday_time', true);
$opening_saturday_time  = get_post_meta($id, GOLO_METABOX_PREFIX . 'opening_saturday_time', true);
$opening_sunday_time    = get_post_meta($id, GOLO_METABOX_PREFIX . 'opening_sunday_time', true);

$arr_monday_time    = explode('-', $opening_monday_time);
$arr_tuesday_time   = explode('-', $opening_tuesday_time);
$arr_wednesday_time = explode('-', $opening_wednesday_time);
$arr_thursday_time  = explode('-', $opening_thursday_time);
$arr_friday_time    = explode('-', $opening_friday_time);
$arr_saturday_time  = explode('-', $opening_saturday_time);
$arr_sunday_time    = explode('-', $opening_sunday_time);

$storeSchedule = [
    'Mon' => $opening_monday_time,
    'Tue' => $opening_tuesday_time,
    'Wed' => $opening_wednesday_time,
    'Thu' => $opening_thursday_time,
    'Fri' => $opening_friday_time,
    'Sat' => $opening_saturday_time,
    'Sun' => $opening_sunday_time
];

$status = golo_status_time_place($storeSchedule);

if( $place_logged && !is_user_logged_in() ) {
    return;
}

// Rating
$rating = $total_reviews = $total_stars = 0;
$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 )";
$my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $id  AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
$get_comments   = $wpdb->get_results($comments_query);
if (!is_null($get_comments)) {
    foreach ($get_comments as $comment) {
        if ($comment->comment_approved == 1) {
            $total_reviews++;
            if( $comment->meta_value > 0 ){
                $total_stars += $comment->meta_value;
            }
        }
    }

    if ($total_reviews != 0) {
        $rating = number_format($total_stars / $total_reviews, 1);
    }
}

$price = '';

$currency_sign = golo_get_option('currency_sign', '$');
$low_price     = golo_get_option('low_price', '$');
$medium_price  = golo_get_option('medium_price', '$$');
$high_price    = golo_get_option('high_price', '$$$');

if( $price_range && $price_range != 0 ){

    if( $price_range == 1 ){
        $price = esc_html__('Free', 'golo-framework');
    }
    if( $price_range == 2 ){
        $price = $low_price;
    }
    if( $price_range == 3 ){
        $price = $medium_price;
    }
    if( $price_range == 4 ){
        $price = $high_price;
    }
}

if( $price_short ){
    $price = golo_get_format_money( $price_short ) . '/' . $price_unit;
}

$place_type         = get_the_terms( $id, 'place-type');
$place_categories   = get_the_terms( $id, 'place-categories');
$place_city         = get_the_terms( $id, 'place-city');

$classifications         = get_the_terms( $id, 'place-amenities');

$author_id      = get_post_field ('post_author', $id);
$author_name    = get_the_author_meta('display_name', $author_id);
$author_link    = get_author_posts_url($author_id);
$avatar_url     = get_avatar_url($author_id);
$author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $author_id);
$author_avatar_image_id  = get_the_author_meta('author_avatar_image_id', $author_id);
if( !empty($author_avatar_image_url) ){
    $avatar_url = $author_avatar_image_url;
}
$avatar_url = golo_image_resize_url($avatar_url, 32, 32, true);

$effect_class = isset($effect_class) ? $effect_class : '';

$place_item_class[] = 'place-item';
$place_item_class[] = $effect_class;

if( $layout ) 
{
    $place_item_class[] = $layout;
}

if( $place_featured )
{
    $place_item_class[] = 'golo-place-featured';
}

$place_item_class[] = 'place-' . $id;


?>

<div class="<?php echo join(' ', $place_item_class); ?>">
    <div class="place-inner home-featured">
        <?php if( !empty($image_src) ) { ?>
        <div class="place-thumb">
            <a class="entry-thumb" href="<?php echo get_the_permalink($id); ?>"><img width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" src="<?php echo esc_url($image_src) ?>" alt="<?php echo get_the_title($id); ?>" title="<?php echo get_the_title($id); ?>"></a>

            <?php 
                golo_get_template('place/wishlist.php', array(
                    'place_id' => $id
                ));
            ?>

            <?php 
            if( $place_categories ) {
                $cate_id   = $place_categories[0]->term_id;
                $cate_name = $place_categories[0]->name;
                $cate      = get_term_by( 'id', $cate_id, 'place-categories');
                $cate_icon = get_term_meta( $cate_id, 'place_categories_icon_marker', true );
                $city_slug = '';
                if( $place_city ) {
                    $city_slug = $place_city[0]->slug;
                }

                if( $cate_icon ) {
                    if( $cate_icon['url'] ) {
                    ?>
                    
                    <a class="entry-category" href="<?php echo get_term_link($cate); ?>?city=<?php echo esc_attr($city_slug); ?>">
                        <img src="<?php echo esc_url($cate_icon['url']) ?>" alt="<?php echo esc_attr($cate_name); ?>">
                    </a>

                <?php } } ?>

            <?php } ?>

        </div>
        <?php } ?>
        
        <div class="entry-detail">

            <?php if( $place_featured === '1' ) : ?>
            <span class="place-featured">
                <?php esc_html_e('Featured', 'golo-framework'); ?>
            </span>
            <?php endif; ?>
            
            <?php if( $display_author === '1' && !empty($avatar_url['url']) ) : ?>
            <a class="author-avatar hint--top" href="<?php echo esc_url($author_link); ?>" aria-label="<?php echo esc_attr($author_name); ?>">
                <img src="<?php echo esc_url($avatar_url['url']); ?>" title="<?php echo esc_attr($author_name); ?>" alt="<?php echo esc_attr($author_name); ?>" >
            </a>
            <?php endif; ?>

            <div class="entry-head">
                <div class="place-term">
                    <?php if($classifications): ?>
                    <div class="place-type list-item">
                        <?php if( !empty($classifications) ){
                            $type_link = get_term_link($classifications[0]);
                            ?>
                        <span><a href="<?php echo $type_link; ?>"><?php echo $classifications[0]->name; ?></a></span>
                        <?php }?>
                    </div>
                    <?php endif; ?>

                    <?php if($place_city): ?>
                    <?php
                        $city_id = $place_city[0]->term_id;
                        $city    = get_term_by( 'id', $city_id, 'place-city');
                    ?>
                    <div class="place-city">
                        <a href="<?php echo get_term_link($city); ?>"><?php echo $place_city[0]->name; ?></a>
                    </div>
                    <?php endif; ?>
                </div>

                <h3 class="place-title"><a href="<?php echo get_the_permalink($id); ?>"><?php echo get_the_title($id); ?></a></h3>

                <?php if ( $enable_status == '1' ) : ?>

                <?php echo wp_kses_post($status); ?>

                <?php endif; ?>

<!--                --><?php //if ( isset($place_address) && $enable_address == 1) : ?>
<!--                <div class="place-address">-->
<!--                    <p><i class="las la-map-marker"></i>--><?php //echo $place_address; ?><!--</p>-->
<!--                </div>-->
<!--                --><?php //endif; ?>

                <!-- post excerpt -->
                <div class="place-excerpt">
                    <?php if( !empty($excerpt) && $enable_excerpt == '1' ){ ?>
                        <p><?php echo wp_trim_words($excerpt, 10); ?></p>
                    <?php } else {?>
                        <p><?php echo wp_trim_words($this_content, 10); ?></p>
                    <?php } ?>
                </div>

            </div>

            <div class="entry-bottom">
                <?php if( $display_review === '1' ) : ?>
                    <div class="place-preview">
                    <?php if( !empty($my_review) ) { ?>
                        <div class="place-rating">
                            <span><?php echo esc_html($rating); ?></span>
                            <i class="la la-star"></i>
                        </div>
                        <span class="count-reviews">
                            <?php printf(_n('(%s review)', '(%s Reviews)', $total_reviews, 'golo-framework'), $total_reviews); ?>
                        </span>
                    <?php }else{ ?>
                        <span class="no-reviews">
                            <?php esc_html_e('(no reviews)', 'golo-framework'); ?>
                        </span>
                    <?php } ?>
                    </div>
                <?php endif; ?>

                <?php if( !empty($price) ) : ?>
<!--                <div class="place-price">-->
<!--                    <span>                     -->
<!--                        --><?php //echo esc_html($price); ?>
<!--                    </span>-->
<!--                </div>-->
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>