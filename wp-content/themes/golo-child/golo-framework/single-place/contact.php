<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

$place_id = get_the_ID();

$place_meta_data = get_post_custom( $place_id );

$place_booking_type = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_type']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_type'][0] : '';

if( $place_booking_type == 'info' ) {
    return;
}

$place_phone     = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_phone', true);
$place_phone2     = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_phone2', true);
$place_website   = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_website', true);
$place_email     = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_email', true);
$place_facebook  = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_facebook', true);
$place_instagram = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_instagram', true);

$additional_detail = isset($place_meta_data[GOLO_METABOX_PREFIX . 'additional_detail']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'additional_detail'][0] : '';
$additional_detail_icon = $additional_detail_url = null;
if ($additional_detail > 0) {
    $additional_detail_icon = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'additional_detail_icon', true);
    $additional_detail_url = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'additional_detail_url', true);
}

$place_address  = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_address']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_address'][0] : '';
$place_location = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_location', true);

if (!empty($place_location['location'])) {
    list($lat, $lng) = explode(',', $place_location['location']);
}

if( $place_location && $place_location['address'] )
{
    $google_map_address_url = "http://maps.google.com/?q=" . $place_location['address'];
    $map_address = $place_location['address'];
}
else
{
    $google_map_address_url = "http://maps.google.com/?q=" . $place_address;
    $map_address = $place_address;
}

$additional_fields = golo_render_additional_fields();

?>

<?php if( $map_address || $place_phone || $place_phone2 || $place_website || $place_email || $place_facebook || $place_instagram || $additional_detail ) : ?>
    <div class="place-contact place-area">
        <div class="entry-heading">
            <h3 class="entry-title"><?php esc_html_e('Contact', 'golo-framework'); ?></h3>
        </div>

        <div class="entry-detail">
            <ul>
                <?php if( !empty($map_address) ) : ?>
                    <li>
                        <i class="las la-map-marked-alt large"></i>
                        <a href="<?php echo esc_url($google_map_address_url); ?>" target="_blank"><?php echo esc_html($map_address); ?></a>
                    </li>
                <?php endif; ?>

                <?php if( !empty($place_phone) ) : ?>
                    <li>
                        <i class="la la-phone large"></i>
                        <a href="tel:<?php echo esc_attr($place_phone); ?>"><?php echo esc_html($place_phone); ?></a>
                    </li>
                <?php endif; ?>

                <?php if( !empty($place_phone2) ) : ?>
                    <li>
                        <i class="la la-phone large"></i>
                        <a href="tel:<?php echo esc_attr($place_phone2); ?>"><?php echo esc_html($place_phone2); ?></a>
                    </li>
                <?php endif; ?>

                <?php if( !empty($place_email) ) : ?>
                    <li>
                        <i class="la la-envelope large"></i>
                        <a href="mailto: <?php echo esc_attr($place_email); ?>"><?php echo esc_html($place_email); ?></a>
                    </li>
                <?php endif; ?>

                <?php if( !empty($place_website) ) : ?>
                    <li>
                        <i class="la la-globe large"></i>
                        <a href="<?php echo esc_url($place_website); ?>" target="_blank"><?php echo esc_html($place_website); ?></a>
                    </li>
                <?php endif; ?>

                <?php if( !empty($place_facebook) ) : ?>
                    <li>
                        <i class="la la-facebook-f large"></i>
                        <a href="<?php echo esc_url($place_facebook); ?>" target="_blank"><?php echo esc_html($place_facebook); ?></a>
                    </li>
                <?php endif; ?>

                <?php if( !empty($place_instagram) ) : ?>
                    <li>
                        <i class="la la-instagram icon-large"></i>
                        <a href="<?php echo esc_url($place_instagram); ?>" target="_blank"><?php echo esc_html($place_instagram); ?></a>
                    </li>
                <?php endif; ?>

                <?php if( !empty($additional_detail_icon[0]) && !empty($additional_detail_url[0]) ): ?>
                    <?php for ($i = 0; $i < $additional_detail; $i++) { ?>
                        <?php if (!empty($additional_detail_icon[$i]) && !empty($additional_detail_url[$i])): ?>
                            <li>
                                <i class="<?php echo esc_html($additional_detail_icon[$i]); ?> large"></i>
                                <a href="<?php echo esc_attr($additional_detail_url[$i]); ?>" target="_blank"><?php echo esc_html($additional_detail_url[$i]); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php } ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
<div class="claim-listing-wrapper">
    <div class="btn-send-message">
        <a class="btn-open-popup gl-button claim-listing" href="#">Claim this listing</a>


        <div class="contact-agent popup">
            <div class="bg-overlay"></div>

            <div class="inner-popup custom-scrollbar">

                <a href="#" class="btn-close"><i class="la la-times icon-large"></i></a>

                <div class="entry-heading">
                    <h3></h3>
                </div>

                <div class="content-here">
                    <p>Hey there,<br>
                        Want to Claim or Upgrade your listing?<br>
                        Did you know that VEats allows you to take table bookings requests & recieve takeaway orders all under one platform.<br>
                        Why not upgrade your listing today.  Get in touch with one of our friendly staff to find out more!</p>
                </div>
            </div>
        </div>

    </div>
</div>
