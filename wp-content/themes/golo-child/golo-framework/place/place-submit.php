<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

$additional_fields = golo_render_additional_fields();
wp_enqueue_script(GOLO_PLUGIN_PREFIX . 'frontend');
wp_enqueue_script('jquery-validate');
wp_localize_script(GOLO_PLUGIN_PREFIX . 'frontend', 'golo_submit_vars',
    array(
        'ajax_url'  => GOLO_AJAX_URL,
        'not_found' => esc_html__("We didn't find any results, you can retry with other keyword.", 'golo-framework'),
        'not_place' => esc_html__('No place found', 'golo-framework'),
        'my_places' => get_site_url() . '/my-places',
        'additional_fields' => $additional_fields,
    )
);

global $current_user,$hide_place_fields, $hide_place_group_fields;
wp_get_current_user();
$user_id = $current_user->ID;
$paid_submission_type = golo_get_option('paid_submission_type','no');

$hide_place_fields = golo_get_option('hide_place_fields', array());
if (!is_array($hide_place_fields)) {
    $hide_place_fields = array();
}

$hide_place_group_fields = golo_get_option('hide_place_group_fields', array());
if (!is_array($hide_place_group_fields)) {
    $hide_place_group_fields = array();
}
?>

<!--new here-->
<div class="content-new-listing">
    <div class="golo-form-content" id="new-listing-holder">
        <?php
        $layout = golo_get_option('place_form_sections', array('general', 'hightlights', 'menu', 'location', 'contact', 'additional', 'socials', 'time-opening', 'media', 'booking') );
        unset($layout['sort_order']);
        $keys   = array_keys($layout);
        $total  = count($keys);
        $form   = 'submit-place';
        $action = 'add_place';
        ?>

        <h2><?php esc_html_e('Add new Listing', 'golo-framework'); ?></h2>
        <div class="row">
            <div class="col-md-8">
                <form action="#" method="post" id="submit_place_form" class="place-manager-form" enctype="multipart/form-data" data-titleerror="<?php echo esc_html__('Please enter place name', 'golo-framework'); ?>" data-deserror="<?php echo esc_html__('Please enter place description', 'golo-framework'); ?>" data-caterror="<?php echo esc_html__('Please choosen category', 'golo-framework'); ?>" data-typeerror="<?php echo esc_html__('Please choosen type', 'golo-framework'); ?>" data-maperror="<?php echo esc_html__('Please enter place address', 'golo-framework'); ?>" data-imgerror="<?php echo esc_html__('Please upload featured image', 'golo-framework'); ?>">
                    <?php
                    foreach ($layout as $value) {
                        $index = array_search($value,$keys);
                        $prev_key = $next_key = $step_name = '';
                        switch ($value) {
                            case 'general':
                                $step_name = esc_html__('General', 'golo-framework');
                                break;
                            case 'hightlights':
                                $step_name = esc_html__('Hightlights', 'golo-framework');
                                break;
                            case 'menu':
                                $step_name = esc_html__('Menu', 'golo-framework');
                                break;
                            case 'location':
                                $step_name = esc_html__('Location', 'golo-framework');
                                break;
                            case 'contact':
                                $step_name = esc_html__('Contact info', 'golo-framework');
                                break;
                            case 'additional':
                                $step_name = esc_html__('Additional fields', 'golo-framework');
                                break;
                            case 'socials':
                                $step_name = esc_html__('Social networks', 'golo-framework');
                                break;
                            case 'time-opening':
                                $step_name = esc_html__('Opening hours', 'golo-framework');
                                break;
                            case 'details':
                                $step_name = esc_html__('Additional details', 'golo-framework');
                                break;
                            case 'media':
                                $step_name = esc_html__('Media', 'golo-framework');
                                break;
                            case 'booking':
                                $step_name = esc_html__('Booking Type ?', 'golo-framework');
                                break;
                            case 'private_note':
                                $step_name = esc_html__('Private note', 'golo-framework');
                                break;
                        }
                        if( $index > 0 )
                        {
                            $prev_key = $keys[$index - 1];
                        }
                        if( $index < $total - 1 ){
                            $next_key = $keys[$index + 1];
                        }
                        ?>

                        <?php if (!in_array($value, $hide_place_group_fields)) : ?>
                            <div class="group-field" id="<?php echo esc_attr($value); ?>">
                                <h3><?php //echo esc_html($step_name); ?></h3>
                                <?php golo_get_template('place/submit-place/' . $value . '.php'); ?>
                            </div>
                        <?php endif; ?>

                    <?php } ?>

                    <?php if ( !is_user_logged_in() ) { ?>
                        <?php $enable_login_to_submit = golo_get_option('enable_login_to_submit', '1'); ?>
                        <?php if( $enable_login_to_submit == '1' ) { ?>
                            <div class="btn-submit-place golo-button account logged-out">
                                <a href="#popup-form" class="btn-login"><?php esc_html_e('Login to Submit','golo-framework'); ?></a>
                            </div>
                        <?php }else{ ?>
                            <button type="submit" class="button btn-submit-place gl-button" name="submit_place">
                                <span><?php esc_html_e('Submit', 'golo-framework'); ?></span>
                                <span class="btn-loading"><i class="la la-circle-notch la-spin large"></i></span>
                            </button>
                        <?php } ?>

                    <?php }else{ ?>

                        <?php
                        $has_package = true;
                        if ($paid_submission_type == 'per_package') {
                            $current_package_key = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_key', $user_id);
                            $place_package_key = get_post_meta($user_id, GOLO_METABOX_PREFIX . 'package_key', true);
                            $golo_profile = new Golo_Profile();
                            $check_package = $golo_profile->user_package_available($user_id);
                            if( ($check_package == -1) || ($check_package == 0) )
                            {
                                $has_package = false;
                            }
                        }
                        ?>
                        <?php if( $has_package ) { ?>
                            <button type="submit" class="button btn-submit-place gl-button" name="submit_place">
                                <span><?php esc_html_e('Submit', 'golo-framework'); ?></span>
                                <span class="btn-loading"><i class="la la-circle-notch la-spin large"></i></span>
                            </button>
                        <?php }else{ ?>
                            <div class="package-out-stock">
                                <span><?php esc_html_e('Upgrade package to add place!', 'golo-framework'); ?></span>
                                <div class="golo-button">
                                    <a href="<?php echo golo_get_permalink('packages'); ?>"><?php esc_html_e('Upgrade now', 'golo-framework'); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>

                    <?php wp_nonce_field('golo_submit_place_action', 'golo_submit_place_nonce_field'); ?>

                    <input type="hidden" name="place_form" value="<?php echo esc_attr($form); ?>"/>
                    <input type="hidden" name="place_action" value="<?php echo esc_attr($action) ?>"/>
                    <input type="hidden" name="place_id" value="<?php echo esc_attr($place_id); ?>"/>
                </form>
            </div>
            <div class="col-md-4">
                <div class="desc-image-holder">
                    <img src="<?=get_stylesheet_directory_uri();?>/assets/images/connect.jpg">
                </div>
            </div>
        </div>
    </div>
<!--    get_template_directory_uri()-->
<!--    <div class="golo-other-content">-->
<!--        <div class="partner-us">-->
<!--            <h3>Why partner with VEats?</h3>-->
<!--            <div class="row">-->
<!--                <div class="col-md-4">-->
<!--                    <div class="partner-inner">-->
<!--                        <p><b>Increase Booking</b></p>-->
<!--                        <img src="--><?//=get_stylesheet_directory_uri();?><!--/assets/images/mobile-payment-2.png" class="booking-icons">-->
<!--                        <p>Join a well-oiled marketing machine and watch the orders come in through your door and online.</p>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="col-md-4">-->
<!--                    <div class="partner-inner">-->
<!--                        <p><b>Increase takeaway orders</b></p>-->
<!--                        <img src="--><?//=get_stylesheet_directory_uri();?><!--/assets/images/delivery.png" class="booking-icons">-->
<!--                        <p>Join a well-oiled marketing machine and watch the orders come in through your door and online.</p>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="col-md-4">-->
<!--                    <div class="partner-inner">-->
<!--                        <p><b>Reach new growing audiences</b></p>-->
<!--                        <img src="--><?//=get_stylesheet_directory_uri();?><!--/assets/images/images.png" class="booking-icons">-->
<!--                        <p>Join a well-oiled marketing machine and watch the orders come in through your door and online.</p>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="join-us">-->
<!--            <h3>Join Australia’s First Plant-based Booking & Ordering platform</h3>-->
<!--        </div>-->
<!--    </div>-->
</div>
<!--new end here-->