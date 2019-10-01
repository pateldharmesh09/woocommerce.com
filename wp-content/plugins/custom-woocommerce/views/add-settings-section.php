<?php 
/**
* Adding a Section to a Settings Tab on woocommerce 
 *Adding a Section to a Settings Tab on woocommerce 
 * Create the section beneath the products tab
 **/
add_filter( 'woocommerce_get_sections_products', 'wc_customize_add_section',10,1);
function wc_customize_add_section( $sections ) {
  
  $sections['wc_customizer_shop'] = __( 'customize Shop Page', 'storefront' );
  return $sections;
  }

/**
 * Add settings to the specific section we created before
 */
add_filter( 'woocommerce_get_settings_products', 'wc_customize_all_settings', 10, 2 );
function wc_customize_all_settings( $settings, $current_section ) {
  /**
   * Check the current section is what we want
   **/
  if ( $current_section == 'wc_customizer_shop' ) {
     
      $settings_wc_customize = array();
     // Add Title to the Settings
    $settings_wc_customize[] = array(
                            'name' => __( 'WooCommerce shop page Settings', 'storefront' ),
                            'type' => 'title', 
                            'desc' => __( 'The following options are used to configure WooCommerce shop page', 'text-domain' ),
                            'id' => 'wc_customize_product' );
    
    // Add first text field option
    $settings_wc_customize[] = array(
      'name'     => __( 'product per row', 'storefront' ),
      'desc_tip' => __( 'The numbe r type here will change the product on per row', 'storefront' ),
      'id'       => 'wc_product_per_row',
      'type'     => 'text',
      'css'      => 'min-width:300px;',
      'desc'     => __( 'type the number', 'storefront' ),
    );
    // Add second text field option
     $settings_wc_customize[] = array(
      'name'     => __( 'product per page', 'storefront' ),
      'desc_tip' => __( 'The number type here will change the product on per page', 'storefront' ),
      'id'       => 'wc_product_per_page',
      'type'     => 'text',
      'css'      => 'min-width:300px;',
      'desc'     => __( 'type the number', 'text-domain' ),
    );
   
    $settings_wc_customize[] = array( 'type' => 'sectionend', 'id' => 'wc_customize' );
    return $settings_wc_customize;
   } 
  /* If not, return the standard settings*/
   else {
    return $settings;
  }
}


/* your newly created settings use through the get_option function and the defined ID of the setting. */
 
 /**
 * Change number or products per row to 3
 */ 
 add_filter('loop_shop_columns', 'loop_columns');
   if (!function_exists('loop_columns')) 
    {
       function loop_columns() {
          
          $row = get_option('wc_product_per_row') ? get_option('wc_product_per_row'):3;
        
          return $row; // 3 product per row 
       }
    }


 /**
 * Change number of products that are displayed per page (shop page)
 */
add_filter( 'loop_shop_per_page', 'num_of_product_per_page',20,1);

function num_of_product_per_page( $col) {
  // $cols contains the current number of products per page based on the value stored on Options -> Reading
  // Return the number of products you wanna show per page.
    $col = get_option('wc_product_per_page') ? get_option('wc_product_per_page'):9;
  
    return $col; // 9 product per page
}
?>