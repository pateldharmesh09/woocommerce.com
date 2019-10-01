<?php
  /* 
  *Remove default Checkout Fields  
  *How to Remove Fields from Woocommerce Edit Address Form billing and Shipping
*/

add_filter( 'woocommerce_checkout_fields', 'checkoutpage_remove_fields', 9999 ); //checkout page
add_filter( 'woocommerce_billing_fields' , 'custom_override_billing_fields' ); 
add_filter( 'woocommerce_shipping_fields' , 'custom_override_shipping_fields' );

 
function checkoutpage_remove_fields( $woo_checkout_fields_array ) {
 
     //she wanted me to leave these fields in checkout
   
   //unset( $woo_checkout_fields_array['billing']['billing_first_name'] );
   //unset( $woo_checkout_fields_array['billing']['billing_last_name'] );
   //unset( $woo_checkout_fields_array['billing']['billing_phone'] );
   //unset( $woo_checkout_fields_array['billing']['billing_email'] );
  // unset( $woo_checkout_fields_array['order']['order_comments'] );   
 // unset( $woo_checkout_fields_array['billing']['billing_country'] );
  // unset( $woo_checkout_fields_array['billing']['billing_address_1'] );
 // unset( $woo_checkout_fields_array['billing']['billing_address_2'] );
 // unset( $woo_checkout_fields_array['billing']['billing_city'] );
 // unset( $woo_checkout_fields_array['billing']['billing_state'] );    
 // unset( $woo_checkout_fields_array['billing']['billing_postcode'] ); 
  
  
  // and to remove the billing fields below
  unset( $woo_checkout_fields_array['billing']['billing_company']); // remove company fields
  unset( $woo_checkout_fields_array['shipping']['shipping_company']); // remove companyt fields
  return $woo_checkout_fields_array;
}

 function custom_override_billing_fields($fields){

   //to remove the billing fields below
    unset($fields['billing_company']);

    //Make address fields wider
     $fields['billing_address_1']['class'] = array( 'form-row-wide' );
     $fields['billing_address_2']['class'] = array( 'form-row-wide' );
    return $fields;
 }


function  custom_override_shipping_fields($fields){

   //to remove the shippping fields below
     unset($fields['shipping_company']); 

      //Make address fields wider
     $fields['shipping_address_1']['class'] = array( 'form-row-wide' );
     $fields['shipping_address_2']['class'] = array( 'form-row-wide' );

      return $fields;
  }
?>