<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue child scripts
 */
add_action( 'wp_enqueue_scripts', 'golo_child_enqueue_scripts' );
if ( ! function_exists( 'golo_child_enqueue_scripts' ) ) {

	function golo_child_enqueue_scripts() {
		wp_enqueue_style( 'golo_child-style', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css' );
		wp_enqueue_script( 'golo_child-script', trailingslashit( get_stylesheet_directory_uri() ) . 'script.js', array( 'jquery' ), null, true );

        if( is_page('new-place') ){
            wp_enqueue_style( 'veats-bootstrap-style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );
            wp_enqueue_script( 'veats-bootstrap-script', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery' ), null, true );
        }
	}

}

//rename custom post type using filter hooks
add_filter( 'glf_register_post_type' , 'veats_custom_post_edit');
function veats_custom_post_edit($restaurants) {

    if( !empty($restaurants) && isset($restaurants['place']) ){
        $restaurants['place']['label'] = "Restaurants";
        $restaurants['place']['singular_name'] = "Restaurant";
    }
    return $restaurants;
}

//rename the custom taxonomy for place
add_filter( 'glf_register_taxonomy' , 'veats_custom_taxanomy_edit');
function veats_custom_taxanomy_edit($categories){
    if( isset($categories['place-type']) ){
        $categories['place-type']['singular_name'] = "Service Type";
    }
    if( isset($categories['place-amenities']) ){
        $categories['place-amenities']['label'] = "Classifications";
        $categories['place-amenities']['singular_name'] = "Classification";
        $categories['place-amenities']['rewrite']['slug'] = "classification";
    }
//    format_display($categories, true);
    return $categories;
}


//do_action('golo_before_template_part', $template_name, $template_path, $located, $args);
add_action( 'golo_before_template_part', 'veats_edit_wishlist_listing', 9, 4 );
function veats_edit_wishlist_listing($template_name, $template_path, $located, $args){
    if($template_name == 'place/wishlist.php'){
        ?>
        <a href="#" class="book-icon-place"><i class="fas fa-utensils"></i></a>
        <?php
    }

    //golo_get_template('content-place.php', array('place_id' => $place->ID,'custom_place_image_size' => $custom_image_size));
}


/* Change Text Site Wide */
function veats_customise_text( $translated_text, $text, $domain ) {
    switch ($translated_text){
        case "Amenities":
            $translated_text = __( 'Classification', 'golo-framework' );
            break;
        case "Place Type":
            $translated_text = __( 'Service Type', 'golo-framework' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'veats_customise_text', 20, 3 );


function format_display($value, $bool=false){
    echo '<pre>';
    print_r($value);
    echo '</pre>';
    if( $bool ) die('_ END _');
}