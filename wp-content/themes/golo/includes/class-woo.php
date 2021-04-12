<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom functions for WooCommerce
 *
 */
if ( ! class_exists( 'Golo_Woo' ) ) {

	class Golo_Woo 
	{

		/**
		 * The constructor.
		 */
		public function __construct() 
		{

			/******************************************************************************************
			 * Shop Page (Product Archive Page)
			 *****************************************************************************************/

			// Remove breadcrumb
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

			add_filter( 'woocommerce_pagination_args', 	'golo_woo_pagination' );
			function golo_woo_pagination( $args ) {
				$args['prev_text'] = '<i class="las la-angle-left"></i>';
				$args['next_text'] = '<i class="las la-angle-right"></i>';
				return $args;
			}

			// Hide default wishlist button
			add_filter( 'yith_wcwl_positions',
				function () {
					return array(
						'add-to-cart' => array(
							'hook'     => '',
							'priority' => 0,
						),
						'thumbnails'  => array(
							'hook'     => '',
							'priority' => 0,
						),
						'summary'     => array(
							'hook'     => '',
							'priority' => 0,
						),
					);
				} 
			);

			// Change number of Related & Up sells product
			add_filter( 'woocommerce_output_related_products_args',
				function ( $args ) {

					$args['posts_per_page'] = 4;
					$args['columns']        = 4;

					return $args;
				} 
			);

			// Hide default compare button
			add_filter( 'yith_woocompare_remove_compare_link_by_cat', '__return_true' );

			/******************************************************************************************
			 * Checkout
			 *****************************************************************************************/
			function custom_override_checkout_fields($fields) {
			    $fields['billing']['billing_first_name']['priority'] = 2;
			    $fields['billing']['billing_last_name']['priority'] = 2;
			    $fields['billing']['billing_company']['priority'] = 3;
			    $fields['billing']['billing_country']['priority'] = 1;
			    $fields['billing']['billing_state']['priority'] = 5;
			    $fields['billing']['billing_address_1']['priority'] = 6;
			    $fields['billing']['billing_address_2']['priority'] = 7;
			    $fields['billing']['billing_city']['priority'] = 8;
			    $fields['billing']['billing_postcode']['priority'] = 9;
			    $fields['billing']['billing_email']['priority'] = 10;
			    $fields['billing']['billing_phone']['priority'] = 11;

			    $fields['billing']['billing_first_name']['placeholder'] = 'First Name';
			    $fields['billing']['billing_last_name']['placeholder'] = 'Last Name';
			    
			    $fields['billing']['billing_email']['label'] = 'Info';
			    $fields['billing']['billing_email']['placeholder'] = 'Email';
			    $fields['billing']['billing_phone']['placeholder'] = 'Phone';

			    return $fields;
			}
			
			// Ajax refreshing mini cart count and content
			if ( ! function_exists( 'is_woocommerce_activated' ) ) {
                add_filter( 'woocommerce_add_to_cart_fragments', 'my_header_add_to_cart_fragment' );
                function my_header_add_to_cart_fragment( $fragments ) {
                    $count = WC()->cart->get_cart_contents_count();
                
                    $fragments['#cart_count'] = '<span id="cart_count" class="cart__amount">' . esc_attr( $count ) . '</span>';
                
                    ob_start();
                    ?>
                    <div id="mini-cart-content" class="sub-menu sub-menu--right sub-menu--cart">
                    <?php my_wc_mini_cart_content(); ?>
                    <div>
                    <?php
                
                    $fragments['#mini-cart-content'] = ob_get_clean();
                
                    return $fragments;
                }
			}

			// Utility function that outputs the mini cart content
			if ( ! function_exists( 'my_wc_mini_cart_content' ) ) {
				function my_wc_mini_cart_content(){
				    $cart = WC()->cart->get_cart();

				    foreach ( $cart as $cart_item_key => $cart_item  ):
				        $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				        $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				            $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				            $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				            $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				            if(isset($cart_item['variation']['attribute_pa_size'])) {
				                $variation_val = $cart_item['variation']['attribute_pa_size'];
				                $term_obj  = get_term_by('slug', $variation_val, 'pa_size');
				                $size_name = $term_obj->name;
				            }
				            ?>

				            <div class="media mini-cart__item woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
				               <?php echo $thumbnail; ?>

				                <div class="media-body mini-cart__item_body">
				                    <div class="mini-cart__item__heading mt-0"><?php echo $product_name; ?></div>
				                    <?php
				                    echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<div class="cart__item__price">' .
				                    sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) .
				                    '</div>', $cart_item, $cart_item_key );

				                    if( isset($size_name) ) { ?>
				                        <div class="mini-cart__item__size"><?php echo $size_name; ?></div>
				                    <?php } ?>
				                </div>

				                <div class="mini-cart__item_remove ">
				                    <?php
				                    echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
				                        '<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
				                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
				                        __( 'Remove this item', 'golo-framework' ),
				                        esc_attr( $product_id ),
				                        esc_attr( $cart_item_key ),
				                        esc_attr( $_product->get_sku() )
				                    ), $cart_item_key );
				                    ?>
				                </div>
				            </div>
				            <?php
				        }
				    endforeach; ?>

				    <a href="<?php echo get_permalink( wc_get_page_id( 'checkout' ) ); ?>" class="btn btn-dark btn-block"><span class="btn__text"><?php _e('Checkout', 'frosted'); ?></span></a>
				    <?php
				}
			}
		}

	}

	new Golo_Woo();
}
