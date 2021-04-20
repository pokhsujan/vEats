<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue child scripts
 */
add_action('wp_enqueue_scripts', 'golo_child_enqueue_scripts');
if (!function_exists('golo_child_enqueue_scripts')) {

    function golo_child_enqueue_scripts()
    {
        wp_enqueue_style('golo_child-fonts', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/child-fonts.css');
        wp_enqueue_style('golo_child-style', trailingslashit(get_stylesheet_directory_uri()) . 'style.css');
        wp_enqueue_script('golo_child-script', trailingslashit(get_stylesheet_directory_uri()) . 'script.js', array('jquery'), null, true);

        if (is_page('new-place')) {
            wp_enqueue_style('veats-bootstrap-style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
            wp_enqueue_script('veats-bootstrap-script', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), null, true);
        }
    }

}

//rename custom post type using filter hooks
add_filter('glf_register_post_type', 'veats_custom_post_edit');
function veats_custom_post_edit($restaurants)
{

    if (!empty($restaurants) && isset($restaurants['place'])) {
        $restaurants['place']['label'] = "Restaurants";
        $restaurants['place']['singular_name'] = "Restaurant";
    }
    return $restaurants;
}

//rename the custom taxonomy for place
add_filter('glf_register_taxonomy', 'veats_custom_taxanomy_edit');
function veats_custom_taxanomy_edit($categories)
{
    if (isset($categories['place-type'])) {
        $categories['place-type']['singular_name'] = "Service Type";
    }
    if (isset($categories['place-amenities'])) {
        $categories['place-amenities']['label'] = "Classifications";
        $categories['place-amenities']['singular_name'] = "Classification";
        $categories['place-amenities']['rewrite']['slug'] = "classification";
    }
//    format_display($categories, true);
    return $categories;
}


//do_action('golo_before_template_part', $template_name, $template_path, $located, $args);
add_action('golo_before_template_part', 'veats_edit_wishlist_listing', 9, 4);
function veats_edit_wishlist_listing($template_name, $template_path, $located, $args)
{
    if (isset($args['place_id']) && ($args['place_id'] != '')) {
        $placeId = 0;
        if (is_singular('place')) {
            $placeId = get_the_ID();
        }
        if( $placeId != $args['place_id'] ){
            $place_meta_data = get_post_custom($args['place_id']);
            $meta_prefix = GOLO_METABOX_PREFIX;
            $Orderurl = 'javascript:void(0);';
            if (isset($place_meta_data[$meta_prefix . 'order-up-link']) && !empty($place_meta_data[$meta_prefix . 'order-up-link'])) {
                $Orderurl = $place_meta_data[$meta_prefix . 'order-up-link'][0];
            }

            if ($template_name == 'place/wishlist.php') {
                ?>
                <a href="<?= $Orderurl; ?>" class="book-icon-place"><i class="fas fa-utensils"></i></a>
                <?php
            }
        }
    }

}


/* Change Text Site Wide */
function veats_customise_text($translated_text, $text, $domain)
{
    switch ($translated_text) {
        case "Amenities":
            $translated_text = __('Classification', 'golo-framework');
            break;
        case "Place Type":
            $translated_text = __('Service Type', 'golo-framework');
            break;
        case "Add place":
            $translated_text = __('Add Listing', 'golo-framework');
            break;
        case "Type a city or location":
            $translated_text = __('Search City or Restaurant', 'golo-framework');
            break;
        case "Please check your form booking":
            $translated_text = __('Please fill all the required fields.', 'golo-framework');
            break;
    }
    return $translated_text;
}

add_filter('gettext', 'veats_customise_text', 20, 3);


//add new menu item called destination in the url
//add_filter( 'wp_nav_menu_listing-menu_items', 'veats_nav_menu_items', 10, 2 );
//add_filter( 'wp_nav_menu_items', 'veats_nav_menu_items', 10, 2 );
function veats_nav_menu_items($items, $args)
{
    $homelink = '<li class="home"><a href="#">' . __('Naya') . '</a></li>';
    $items = $items . $homelink;
    return $items;
}


function format_display($value, $bool = false)
{
    echo '<pre>';
    print_r($value);
    echo '</pre>';
    if ($bool) die('_ END _');
}

//get store open or close status
function golo_status_time_place($storeSchedule)
{
    $class = '';

    // default status
    $status = sprintf( esc_html__( '%s Closed', 'golo-framework' ), '<i class="las la-door-closed icon-large"></i>' );

    $tzstring = get_option( 'timezone_string' );
    $offset   = get_option( 'gmt_offset' );

    $enable_time_format_24 = golo_get_option('enable_time_format_24', 0);

    if( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ){
        $offset_st = $offset > 0 ? "-$offset" : '+'.absint( $offset );
        $tzstring  = 'Etc/GMT'.$offset_st;
    }

    //Issue with the timezone selected, set to 'UTC'
    if( empty( $tzstring ) ){
        $tzstring = 'UTC';
    }

    $timezone = new DateTimeZone( $tzstring );

    // current or user supplied UNIX timestamp
    $dt = current_datetime();
    $timestamp = strtotime($dt->format("Y-m-d H:i:s"));

    $current_day = date('D', $timestamp);

    $arr = $storeSchedule[$current_day];

    if( !$arr ) {
        return;
    }

    if (strpos($arr, '&') != false) {
        $arr = str_replace( '&', '-', $arr );
    }

    $arr = explode('-', $arr);

    // get current time object
    $currentTime = DateTime::createFromFormat('H:i:s', $dt->format("H:i:s"), new DateTimeZone($tzstring));;

    if( !empty($arr[0]) && !empty($arr[1]) ) {
        $arr[0] = trim($arr[0]);
        $arr[1] = trim($arr[1]);

        if ($enable_time_format_24 == 1) {
            $startTime = DateTime::createFromFormat('H:i', $arr[0], new DateTimeZone($tzstring));
            $endTime   = DateTime::createFromFormat('H:i', $arr[1], new DateTimeZone($tzstring));
        } else {
            $startTime = DateTime::createFromFormat('h:i A', $arr[0], new DateTimeZone($tzstring));
            $endTime   = DateTime::createFromFormat('h:i A', $arr[1], new DateTimeZone($tzstring));
        }

        if ($endTime < $currentTime) {
            if( !empty($arr[2]) && !empty($arr[3]) ) {
                $arr[2] = trim($arr[2]);
                $arr[3] = trim($arr[3]);

                if ($enable_time_format_24 == 1) {
                    $startTime = DateTime::createFromFormat('H:i', $arr[2], new DateTimeZone($tzstring));
                    $endTime   = DateTime::createFromFormat('H:i', $arr[3], new DateTimeZone($tzstring));
                } else {
                    $startTime = DateTime::createFromFormat('h:i A', $arr[2], new DateTimeZone($tzstring));
                    $endTime   = DateTime::createFromFormat('h:i A', $arr[3], new DateTimeZone($tzstring));
                }

                // check if current time is within a range
                if ( ( $startTime < $endTime ) && ($startTime < $currentTime) && ($currentTime < $endTime) ) {
                    $class  = 'open';
                    $status = sprintf( esc_html__( '%s Open now', 'golo-framework' ), '<i class="las la-door-open icon-large"></i>' );
                } else if ( ( $startTime > $endTime ) && (($startTime < $currentTime) || ($currentTime < $endTime)) ) {
                    $class  = 'open';
                    $status = sprintf( esc_html__( '%s Open now', 'golo-framework' ), '<i class="las la-door-open icon-large"></i>' );
                } else {
                    $class  = 'close';
                    $status = sprintf( esc_html__( '%s Closed', 'golo-framework' ), '<i class="las la-door-closed icon-large"></i>' );
                }
            } else {
                $class  = 'close';
                $status = sprintf( esc_html__( '%s Closed', 'golo-framework' ), '<i class="las la-door-closed icon-large"></i>' );
            }
        } else {
            if ( ( $startTime < $endTime ) && ($startTime < $currentTime) && ($currentTime < $endTime) ) {
                $class  = 'open';
                $status = sprintf( esc_html__( '%s Open now', 'golo-framework' ), '<i class="las la-door-open icon-large"></i>' );
            } else if ( ( $startTime > $endTime ) && (($startTime < $currentTime) || ($currentTime < $endTime)) ) {
                $class  = 'open';
                $status = sprintf( esc_html__( '%s Open now', 'golo-framework' ), '<i class="las la-door-open icon-large"></i>' );
            } else {
                $class  = 'close';
                $status = sprintf( esc_html__( '%s Closed', 'golo-framework' ), '<i class="las la-door-closed icon-large"></i>' );
            }
        }

    } else {
        $class  = 'close';
        $status = sprintf( esc_html__( '%s Closed', 'golo-framework' ), '<i class="las la-door-closed icon-large"></i>' );
    }

    return '<div class="place-status ">' . $status . '</div>';
}

//adding shortcode for elementor
function veats_search_from_cities(){
    ob_start();?>
    <?php
    echo ob_get_clean();
}


//increase file upload size
@ini_set( 'upload_max_size' , '32M' );
@ini_set( 'post_max_size', '32M');
@ini_set( 'max_execution_time', '300' );