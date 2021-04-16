<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

wp_enqueue_script('google-map');

global $hide_place_fields;

$default_city           = golo_get_option('default_city', '');
$map_default_position   = golo_get_option('map_default_position', '');
$map_type               = golo_get_option('map_type', 'google_map');
$map_zoom_level         = golo_get_option('map_zoom_level', '15');
$type_single_place      = golo_get_option('type_single_place', 'type-1' );

$map_marker_icon_url    = GOLO_PLUGIN_URL . 'assets/images/map-marker-icon.png';


if( $map_type == 'google_map' ) {
    $google_map_style       = golo_get_option('googlemap_style', '');
} else if( $map_type == 'openstreetmap' ) {
    $google_map_style       = golo_get_option('openstreetmap_style', 'streets-v11');
    $openstreetmap_api_key      = Golo_Helper::golo_get_option('openstreetmap_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
} else {
    $google_map_style       = golo_get_option('mapbox_style', 'streets-v11');
    $googlemap_api_key      = Golo_Helper::golo_get_option('mapbox_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
}

$lat = -26.4391;
$lng = 133.2813;

if( $map_default_position ) {
    if( $map_default_position['location'] ) {
        list( $lat, $lng )  = !empty($map_default_position['location']) ? explode( ',', $map_default_position['location'] ) : array('', '');
    }
}
?>

<div class="place-fields-wrap">
    <div class="place-fields place-city">
        <div class="form-group row">
            <?php if (!in_array('city_town', $hide_place_fields)) : ?>
            <div class="col-sm-6">
                <div class="form-group form-select golo-loading-ajax-wrap">
<!--                    <label class="place-fields-title" for="city">--><?php //esc_html_e('City / Town*', 'golo-framework'); ?><!--</label>-->
                    <select name="place_city" id="city" class="golo-place-city-ajax form-control nice-select wide">
                        <option value=""><?php esc_html_e('Select City / Town', 'golo-framework'); ?></option>
                        <?php golo_get_taxonomy_slug('place-city', $default_city); ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="col-sm-6">
                <div class="form-group">
<!--                    <label class="place-fields-title" for="place-zip">--><?php //esc_html_e('Postal Code', 'golo-framework'); ?><!--</label>-->

                    <input type="text" id="place-zip" class="form-control" name="place_zip" placeholder="<?php esc_attr_e('Post Code', 'golo-framework'); ?>" autofill="off" autocomplete="off">
<!--                    <input type="hidden" class="form-control" name="custom_place_city">-->
<!--                    <input type="hidden" class="form-control" name="custom_place_city_location">-->
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!in_array('address', $hide_place_fields)) : ?>
<div class="place-fields-wrap">
    <div class="place-fields place-address">
        <div class="form-group">
<!--            <label class="place-fields-title" for="search-location">--><?php //echo esc_html__('Place Address*', 'golo-framework'); ?><!--</label>-->
            <div class="input-area">
                <input type="text" id="search-location" class="form-control" name="place_map_address" placeholder="<?php esc_attr_e('Full Address', 'golo-framework'); ?>" autocomplete="off">
            </div>
            <input type="hidden" class="form-control place-map-location" name="place_map_location"/>
            <div id="geocoder" class="geocoder"></div>
        </div>
    </div>
</div>

<?php endif; ?>