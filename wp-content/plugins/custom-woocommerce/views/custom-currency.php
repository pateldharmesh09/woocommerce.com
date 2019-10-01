<?php
/**
 * Custom currency and currency symbol
 */

 //add currency name
add_filter( 'woocommerce_currencies', 'add_my_currency' );

function add_my_currency( $currencies ) 
   {
     $currencies['ABC'] = __( 'My Indian Rupees', 'woocommerce' );
     return $currencies; 
  } 

//add currency symbols
add_filter('woocommerce_currency_symbol', 'add_my_currency_symbol', 10, 2);

function add_my_currency_symbol( $currency_symbol, $currency ) {
     switch( $currency) 
       {
          case 'ABC': $currency_symbol = '@'; break;
       }
     return $currency_symbol;
}
?>