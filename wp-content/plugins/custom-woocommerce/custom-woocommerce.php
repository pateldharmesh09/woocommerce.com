<?php

/*
Plugin Name: custom woocommerce
Description: this plugin can be used for woocommernce for Add text box in product detail page. in which the user can add a message for the gift. Message appears in cart as well on the checkout page. After successfully product purchase that message is sent in email and back end order screen.

Version: 1.0
Author: dharmesh
Author URI: http://wordpress.org/
Plugin URI: http://wordpress.com/
*/
   define('MY_PLUGIN_PATH',plugin_dir_path( __FILE__ ));  // return current plugin path
    define('PLUGIN_URL',plugins_url());                   // return plugin URL

  // include stylesheet && script file
  function custom_woocommerce_assets()
  {
     // load stylesheet
     wp_enqueue_style('plugin_css',PLUGIN_URL.'/custom-woocommerce/assets/css/mystyle.css');

     // load script
     wp_enqueue_script('plugin_js',PLUGIN_URL.'/custom-woocommerce/assets/js/myscript.js','','',true);
  
  }
  add_action('init','custom_woocommerce_assets');

  //Adding a Section to a Settings Tab on woocommerce
  //include file 
    include_once(MY_PLUGIN_PATH.'views/add-settings-section.php');


/*Add custom  fields to WooCommerce Product page */

function add_textbox_before_add_to_cart( ) {
  ?>
  <table>
    <tr>
      <td>Name</td>
      <td>
        <input type = "text" name = "name" id = "name" placeholder = "Name on Gift Card">
      </td>
    </tr>
    <tr>
      <td>Message</td>
      <td>
        <input type = "text" name = "message" id = "message" placeholder = "Your Message on Gift Card">
      </td>
    </tr>
  </table>
  <?php
}

add_action( 'woocommerce_before_add_to_cart_button', 'add_textbox_before_add_to_cart' );



/*--Add data to cart item  */
/*--To add these data we will use WooCommerce filter woocommerce_add_cart_item_data. This filter allows us to add the custom data to the cart item meta. */

function add_cart_item_data( $cart_item_meta, $product_id ) 
{
  if ( isset( $_POST ['name'] ) && isset( $_POST ['message'])) 
   {
    $custom_data  = array() ;
    $custom_data [ 'name' ]    = isset( $_POST ['name'] ) ?  sanitize_text_field ( $_POST ['name'] ) : "" ;
    $custom_data [ 'message' ] = isset( $_POST ['message'] ) ? sanitize_text_field ( $_POST ['message'] ): "" ;
    $cart_item_meta ['custom_data']     = $custom_data ;
  }
  
  return $cart_item_meta;
}
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data', 10, 2 );

/* Display custom data on cart and checkout page */
/* To display that custom data we will use WooCommerce filter woocommerce_get_item_data.*/

function get_item_data ( $other_data, $cart_item ) 
{
  if ( isset( $cart_item [ 'custom_data' ] ) ) {
    $custom_data  = $cart_item [ 'custom_data' ];
      
    $other_data[] = array( 'name' => 'Name',
          'display'  => $custom_data['name'] );
    $other_data[] = array( 'name' => 'Message',
               'display'  => $custom_data['message'] );
  }
  
  return $other_data;
}
add_filter( 'woocommerce_get_item_data', 'get_item_data' , 10, 2 );

/* Add order item meta */
/*  add it to the order item meta we will use WooCommerce action hook woocommerce_add_order_item_meta.*/

function add_order_item_meta ( $item_id, $values ) {
  if ( isset( $values [ 'custom_data' ] ) ) {
    $custom_data  = $values [ 'custom_data' ];
    wc_add_order_item_meta( $item_id, 'Name', $custom_data['name'] );
    wc_add_order_item_meta( $item_id, 'Message ', $custom_data['message'] );
  }
}
add_action( 'woocommerce_add_order_item_meta', 'add_order_item_meta' , 10, 2);
?>



