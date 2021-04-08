<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if ( !is_user_logged_in() ) {
    golo_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}

global $current_user;
wp_get_current_user();
$user_id = $current_user->ID;
$posts_per_page = golo_get_option('booking_total_post', 8);
$args = array(
    'post_type'           => 'booking',
    'post_status'         => array('publish', 'canceled', 'pending'),
    'ignore_sticky_posts' => 1,
    'posts_per_page'      => $posts_per_page,
    'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'orderby'             => 'date',
    'order'               => 'desc',
    'author'              => $user_id,
);
$booking = new WP_Query($args);

?>

<div class="golo-my-booking area-main-control">

    <div class="container">

        <div class="entry-my-booking entry-my-page">

            <?php 
                $user_name = $current_user->display_name;
                $user_package_id = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_id', $user_id);
                $package_title = '';
                if( $user_package_id ) {
                    $package_title = get_the_title($user_package_id);
                }
                $paid_submission_type = golo_get_option('paid_submission_type','no');
            ?>
            <div class="heading-page">
                <h2 class="entry-title"><?php esc_html_e('My Booking', 'golo-framework'); ?></h2>
                
                <?php if ($paid_submission_type == 'per_package') { ?>
                <div class="entry-alert">
                    <span>
                        <?php if( $package_title ) { ?>
                            <?php echo sprintf( __( 'You are currently "%s" package.', 'golo-framework' ), '<strong>' . $package_title . '</strong>'); ?>
                        <?php }else{ ?>
                            <?php esc_html_e('Buy a package to add your place now.', 'golo-framework'); ?>
                        <?php } ?>
                    </span>

                    <a class="accent-color" href="<?php echo golo_get_permalink('packages'); ?>"><?php esc_html_e('Upgrade now', 'golo-framework'); ?></a>
                </div>
                <?php } ?>
            </div>
            
            <?php if ($booking->have_posts()) { ?>
            <div class="entry-my-table">
                <table id="my-booking" class="golo-table">
                    <thead>
                        <tr>
                            <th class="booking-name"><?php esc_html_e('Name', 'golo-framework'); ?></th>
                            <th class="booking-guest"><?php esc_html_e('Guest', 'golo-framework'); ?></th>
                            <th class="booking-date"><?php esc_html_e('Date', 'golo-framework'); ?></th>
                            <th class="booking-time"><?php esc_html_e('Time', 'golo-framework'); ?></th>
                            <th class="booking-status"><?php esc_html_e('Status', 'golo-framework'); ?></th>
                            <th class="booking-action"><?php esc_html_e('Action', 'golo-framework'); ?></th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php while ($booking->have_posts()): $booking->the_post(); ?>
                            <?php 
                            $id = get_the_ID();
                            $item_booking = get_post($id);
                            $status = get_post_status($id);
                            $booking_meta_data = get_post_custom($id);
                            $booking_item_name = isset($booking_meta_data[GOLO_METABOX_PREFIX . 'booking_item_name']) ? $booking_meta_data[GOLO_METABOX_PREFIX . 'booking_item_name'][0] : '';
                            $booking_item_id   = isset($booking_meta_data[GOLO_METABOX_PREFIX . 'booking_item_id']) ? $booking_meta_data[GOLO_METABOX_PREFIX . 'booking_item_id'][0] : '';
                            $booking_adults    = isset($booking_meta_data[GOLO_METABOX_PREFIX . 'booking_adults']) ? $booking_meta_data[GOLO_METABOX_PREFIX . 'booking_adults'][0] : '0';
                            $booking_childrens = isset($booking_meta_data[GOLO_METABOX_PREFIX . 'booking_childrens']) ? $booking_meta_data[GOLO_METABOX_PREFIX . 'booking_childrens'][0] : '0';
                            $booking_date      = isset($booking_meta_data[GOLO_METABOX_PREFIX . 'booking_date']) ? $booking_meta_data[GOLO_METABOX_PREFIX . 'booking_date'][0] : '';
                            $booking_time      = isset($booking_meta_data[GOLO_METABOX_PREFIX . 'booking_time']) ? $booking_meta_data[GOLO_METABOX_PREFIX . 'booking_time'][0] : '';
                            ?>
                            <tr>
                                <td class="booking-name">
                                    <h3 class="booking-title"><?php echo sprintf( __( 'You booked at "%s"', 'golo-framework' ), $booking_item_name); ?></h3>
                                </td>
                                <td class="booking-guest">
                                    <?php echo sprintf( esc_html__( '%1$s adult - %2$s children', 'golo-framework' ), $booking_adults, $booking_childrens ); ?>
                                </td>
                                <td class="booking-date">
                                    <?php echo esc_html($booking_date); ?>
                                </td>
                                <td class="booking-time">
                                    <?php echo esc_html($booking_time); ?>
                                </td>
                                <td class="booking-status status <?php echo esc_attr($status); ?>">
                                    <?php 
                                    if( $status == 'publish' ) {
                                        $status = 'approved';
                                    }

                                    if( $status == 'pending' ) {
                                        $status = 'processing';
                                    }
                                    ?>
                                    <div><?php echo esc_html($status); ?></div>
                                </td>
                                <td class="booking-action">
                                    <?php 
                                        $actions = array();
                                        switch ($status) {
                                            case 'approved' :
                                                $actions['cancel'] = array('label' => __('Cancel', 'golo-framework'),'icon' => '<i class="lar la-times-circle icon-large"></i>','tooltip' => __('Cancel', 'golo-framework'), 'nonce' => true, 'confirm' => esc_html__('Are you sure you want to cancel this booking?', 'golo-framework'));
                                                break;
                                            case 'processing' :
                                                $actions['cancel'] = array('label' => __('Cancel', 'golo-framework'),'icon' => '<i class="lar la-times-circle icon-large"></i>','tooltip' => __('Cancel', 'golo-framework'), 'nonce' => true, 'confirm' => esc_html__('Are you sure you want to cancel this booking?', 'golo-framework'));
                                                break;
                                        }
                                        $actions['delete'] = array('label' => __('Delete', 'golo-framework'),'icon' => '<i class="la la-trash-alt icon-large"></i>','tooltip' => __('Delete', 'golo-framework'), 'nonce' => true, 'confirm' => esc_html__('Are you sure you want to delete this booking?', 'golo-framework'));
                                        ?>
                                            <a class="btn-view hint--top btn-open-popup" href="<?php echo get_permalink($id); ?>" aria-label="<?php esc_attr_e( 'View', 'golo-framework' ); ?>"><i class="las la-external-link-square-alt icon-large"></i></a>

                                            <div class="popup popup-custom popup-booking">
                                                <div class="bg-overlay"></div>
                                                <div class="inner-popup">
                                                    <a href="#" class="btn-close">
                                                        <i class="la la-times icon-large"></i>
                                                    </a>
                                                    
                                                    <div class="entry-title"><h3><?php esc_html_e('Booking Detail', 'golo-framework'); ?></h3></div>
                                                    <ul class="list-group">
                                                        <li class="list-group-item place-name">
                                                            <span><?php esc_html_e('Place:', 'golo-framework'); ?></span>
                                                            <a href="<?php echo get_permalink($booking_item_id); ?>"><strong><?php echo esc_html($booking_item_name); ?></strong></a>
                                                        </li>
                                                        <li class="list-group-item booking-status <?php echo esc_attr($status); ?>">
                                                            <span><?php esc_html_e('Status:', 'golo-framework'); ?></span>
                                                            <strong><?php echo esc_html($status); ?></strong>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <span><?php esc_html_e('Adult:', 'golo-framework'); ?></span>
                                                            <strong><?php echo esc_html($booking_adults); ?></strong>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <span><?php esc_html_e('Children:', 'golo-framework'); ?></span>
                                                            <strong>
                                                                <?php echo esc_html($booking_childrens); ?>
                                                            </strong>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <span><?php esc_html_e('Date:', 'golo-framework'); ?></span>
                                                            <strong>
                                                                <?php echo esc_html($booking_date); ?>
                                                            </strong>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <span><?php esc_html_e('Time:', 'golo-framework'); ?></span>
                                                            <strong>
                                                                <?php echo esc_html($booking_time); ?>
                                                            </strong>
                                                        </li>
                                                    </ul>

                                                </div>
                                            </div>
                                        <?php

                                        $actions = apply_filters('golo_my_booking_actions', $actions, $item_booking);
                                        foreach ($actions as $action => $value) {
                                            $my_booking_page_link = golo_get_permalink('my_booking');
                                            if( get_query_var('paged') > 1 ) {
                                                $my_booking_page_link = $my_booking_page_link . '/page/' . get_query_var('paged');
                                            }
                                            $action_url = add_query_arg(array('action' => $action, 'booking_id' => $item_booking->ID), $my_booking_page_link);
                                            if ($value['nonce']) {
                                                $action_url = wp_nonce_url($action_url, 'golo_my_booking_actions');
                                            }
                                            ?>
                                            <a <?php if (!empty($value['confirm'])): ?> onclick="return confirm('<?php echo esc_html($value['confirm']); ?>')" <?php endif; ?> class="hint--top" href="<?php echo esc_url($action_url); ?>" aria-label="<?php echo esc_html($value['tooltip']); ?>"><?php echo wp_kses_post($value['icon']); ?></a>
                                            <?php
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
                <div class="item-not-found"><?php esc_html_e('No booking found', 'golo-framework'); ?></div>
            <?php } ?>

            <?php
                $max_num_pages = $booking->max_num_pages;
                golo_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'layout' => 'number'));
                wp_reset_postdata();
            ?>
            
        </div>

    </div>
</div>