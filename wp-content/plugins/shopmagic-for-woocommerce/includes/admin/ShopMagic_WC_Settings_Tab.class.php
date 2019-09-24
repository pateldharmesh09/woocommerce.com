<?php
/**
 * ShopMagic's Settings page in WoooCommerce settings page .
 *
 * Show And Save Pugin Specific settings
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */
if( !class_exists( 'WooCommerce' ) ) {
    return;
}
if ( ! class_exists( 'WC_Settings_Page' ) ){
    require_once(WP_PLUGIN_DIR."/woocommerce/includes/admin/settings/class-wc-settings-page.php");
}

class ShopMagic_WC_Settings_Tab extends WC_Settings_Page{
    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public function __construct() {

        $this->id    = 'shopmagic';
        $this->label = __( 'ShopMagic', 'shopmagic' );

        add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 50);

        add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
        add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
        add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );

    }
    /**
     * Get sections.
     *
     * @return array
     */
    public function get_sections() {

        $sections = array(
            ''              => __( 'General', 'shopmagic' ),
           'mailchimp'       => __( 'MailChimp', 'shopmagic' ),
        );

        return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
    }
    /**
     * Output the settings.
     */
    public function output() {
        global $current_section;

        // New admin notices place for ShopMagic page
        do_action( 'shopmagic_admin_notices' );

        $settings = $this->get_settings( $current_section );

        WC_Admin_Settings::output_fields( $settings );
    }
    /**
     * Save settings.
     */
    public function save() {
        global $current_section;

        $settings = $this->get_settings( $current_section );
        WC_Admin_Settings::save_fields( $settings );
    }
    /**
     * Get settings array.
     *
     * @return array
     */
    public function get_settings( $current_section = '' ) {
        if ( 'mailchimp' == $current_section ) {

            if ( ! class_exists( 'MailChimp_API_Tools' ) ) {
                require_once(SHOPMAGIC_BASE_DIR.'/includes/libraries/MailChimp_API_Tools.class.php');
            }

            $mc_apiKey_from_settings = get_option('wc_settings_tab_mailchimp_api_key', false);

            try {
                $MailChimpTools = new MailChimp_API_Tools($mc_apiKey_from_settings);
            
                // Set the lists names options
                $lists_names_options = $MailChimpTools->get_all_lists_options();
            }
            catch(Exception $err) {
                error_log($err);
                $lists_names_options = array(
                    "0"=>__( 'Please make sure about the MailChimp API key provided !', 'shopmagic' ),
                );
            }

            $settings = apply_filters( 'woocommerce_shopmagic_settings', array(

                array(
                    'name'     => __( 'MailChimp Settings', 'shopmagic' ),
                    'type'     => 'title',
                    'id'       => 'wc_settings_mailchimp_section_title'
                ),

                // API Key - MailChimp
                array(
                    'name' => __( 'API Key', 'shopmagic' ),
                    'type' => 'text',
                    'css'     => 'min-width:290px;',
                    'desc' => __( 'Insert your API key here which you can create and get from your MailChimp settings.', 'shopmagic' ),
                    'desc_tip' => true,
                    'id'   => 'wc_settings_tab_mailchimp_api_key'
                ),

                // List names - ( Default, could be changed from the add action form )
                array(
                    'name' => __( 'List name', 'shopmagic' ),
                    'type' => 'select',
                    'options' => $lists_names_options,
                    'desc' => __( 'The DEFAULT MailChimp List names to which you want to add clients.', 'shopmagic' ),
                    'desc_tip' => true,
                    'id'   => 'wc_settings_tab_mailchimp_list_id'
                ),

                // Double-Optin - MailChimp
                array(
                    'name' => __( 'Double-Optin', 'shopmagic' ),
                    'type' => 'checkbox',
                    'default' => 'yes',
                    'desc' => __( 'Request permission from client (Unchecking Double-Optin may be against mailchimp policy)', 'shopmagic' ),
                    'desc_tip' => false,
                    'id'   => 'wc_settings_tab_mailchimp_double_optin'
                ),

                // -Tags - a single text field for seller to include tags (comma separated) to be added to mailchimp upon checkout
                array(
                    'name' => __( 'Tags', 'shopmagic' ),
                    'type' => 'text',
                 /* 'default' => '', */
                    'desc' => __( 'A single text field for seller to include tags (comma separated) to be added to mailchimp upon checkout.', 'shopmagic' ),
                    'desc_tip' => true,
                    'id'   => 'wc_settings_tab_mailchimp_tags'
                ),
                /*
                *    --last name
                *    --address
                *    --city
                *    --state/region
                *    --country 
                */

                array(
                    'name' => __( 'Send additional information to MailChimp list', 'shopmagic' ),
                    'type' => 'checkbox',
                    'checkboxgroup'   => 'start',
                    'default' => 'no',
                    'desc' => __( 'Last name', 'shopmagic' ),
                    'desc_tip' => false,
                    'id'   => 'wc_settings_tab_mailchimp_info_lname'
                ),
                array(
                    'type' => 'checkbox',
                    'checkboxgroup'   => '',
                    'default' => 'no',
                    'desc' => __( 'Address', 'shopmagic' ),
                    'desc_tip' => false,
                    'id'   => 'wc_settings_tab_mailchimp_info_address'
                ),
                array(
                    'type' => 'checkbox',
                    'checkboxgroup'   => '',
                    'default' => 'no',
                    'desc' => __( 'City', 'shopmagic' ),
                    'desc_tip' => false,
                    'id'   => 'wc_settings_tab_mailchimp_info_city'
                ),
                array(
                    'type' => 'checkbox',
                    'checkboxgroup'   => '',
                    'default' => 'no',
                    'desc' => __( 'State', 'shopmagic' ),
                    'desc_tip' => false,
                    'id'   => 'wc_settings_tab_mailchimp_info_state'
                ),
                array(
                    'type' => 'checkbox',
                    'checkboxgroup'   => 'end',
                    'default' => 'no',
                    'desc' => __( 'Country', 'shopmagic' ),
                    'desc_tip' => false,
                    'id'   => 'wc_settings_tab_mailchimp_info_country'
                ),
                // End MailChimp section 
                'mailchimp_section_end' => array(
                    'type' => 'sectionend'
                )


            ));
        } else if( 'debug' == $current_section ) { // Debug 
            $settings = apply_filters( 'woocommerce_shopmagic_settings_debug', array(
                // else Debug section 

                array(
                    'name'     => __( 'Debug Settings', 'shopmagic' ),
                    'type'     => 'title',
                    'desc'     => 'Tools to help you see what\'s going on under the hood',
                    'id'       => 'wc_settings_shopmagic_debug_title'
                ),

                // Store events checkbox
                array(
                    'name' => __( 'Record all activity in log', 'shopmagic' ),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'desc' => __( 'Store messages (generated by events and actions) in the event log. Useful for debugging and monitoring. <br /><b>*Warning: May lead to inflated database if left on permanently - use for temporary testing only</b>.', 'shopmagic' ),
                    'desc_tip' => false,
                    'id'   => 'wc_settings_sm_store_messages'
                ),

                // Debug checkbox
                array(
                    'name' => __( 'Debug', 'shopmagic' ),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'desc' => __( 'Logs activity to wp_log file', 'shopmagic' ),
                    'desc_tip' => false,
                    'id'   => 'wc_settings_sm_debug'
                ),

                // End Event Log section 
                'settings_debug_section_end' => array(
                    'type' => 'sectionend'
                ),

                array(
                    'name'     => __( 'Event Log', 'shopmagic' ),
                    'type'     => 'title',
                    'desc'     => false,
                    'id'       => 'wc_settings_shopmagic_debug_title'
                )


            ));

        } else if( '' == $current_section ) { // General
            $settings = apply_filters( 'woocommerce_shopmagic-general_settings', array(
                // else general section     

                array(
                    'name'     => __( 'General ShopMagic settings', 'shopmagic' ),
                    'type'     => 'title',
                    'desc'     => 'Click on the appropriate ShopMagic Add-On settings tab above',
                    'id'       => 'wc_settings_shopmagic_section_title'
                ),

                // Subscribe to Newsletter on Checkout
                array(
                    'name' => __( 'Subscribe on Checkout', 'shopmagic' ),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'desc' => __( 'Ask customer to subscribe to your email mailing list on checkout', 'shopmagic' ),
                    'desc_tip' => __( 'Setup Mailchimp or other email service add-on first'),
                    'id'   => 'wc_settings_sm_subscribe_on_checkout'
                ),

                array(
                    'type' => 'sectionend'
                )       

            ));
        } else {
            $sections = $this->get_sections();
            foreach ($sections as $section_id => $title) {
                if( $current_section == $section_id){
                    $settings = apply_filters( 'woocommerce_shopmagic-'.$current_section.'_settings',array() );
                }
            }
        }

        return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
    }
}

return new ShopMagic_WC_Settings_Tab();

?>