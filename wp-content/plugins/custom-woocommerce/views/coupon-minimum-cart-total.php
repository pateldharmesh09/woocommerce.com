<?php
/**
* Apply a coupon for minimum cart total
*/

add_action( 'woocommerce_before_cart' , 'add_coupon_notice' );
add_action( 'woocommerce_before_checkout_form' , 'add_coupon_notice' );

function add_coupon_notice() 
 {

      $cart_total = WC()->cart->get_subtotal();
      $minimum_amount = 700;  // $700
      $currency_code = get_woocommerce_currency();
      wc_clear_notices();

     if ( $cart_total < $minimum_amount ) {
            WC()->cart->remove_coupon( 'FIXEDCARTCOUPON' );
            wc_print_notice( "Get $20 off if you spend more than $minimum_amount $currency_code!", 'notice' );
        } 
      else {
            WC()->cart->apply_coupon( 'FIXEDCARTCOUPON');
            wc_print_notice( 'You just got $20  off your order!', 'notice' );
        }
        wc_clear_notices();
 }
?> 
