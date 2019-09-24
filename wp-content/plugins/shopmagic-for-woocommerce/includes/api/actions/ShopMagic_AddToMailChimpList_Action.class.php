<?php
/**
 * ShopMagic's "Add To MailChimp List" Action class.
 *
 * Add concerned clients to a given MailChimp list
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ShopMagic_Action' ) ) {
    require_once(SHOPMAGIC_BASE_DIR.'/includes/api/ShopMagic_Action.class.php');
}


/**
 * ShopMagic Add To MailChimp List Action class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
class ShopMagic_AddToMailChimpList_Action extends ShopMagic_Action {


    static protected $name = "Add Customer to Mailchimp List";
    static protected $slug = "shopmagic_addtomailchimplist_action";
    static protected $data_domains = array('user','order');

    /**
     * Default constructor.
     *
     * @param $core ShopMagic instance of main plugin class
     * @param $automation_id integer called automation id
     * @param $data array current action settings to store in instance
     *
     * @since   1.0.0
     */
    function __construct(ShopMagic $core, $automation_id, $data) {
        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct($core, $automation_id, $data);

        // Setup class variables

    }
    
    /**
     * Execute an action
     *
     * Add the concerned client a list from the parameters
     *
     * @param $event ShopMagic_Event an event which is fired this action
     * @since   1.0.0
     */
    function execute($event, $key)
    {
        /* Get action parameters */
            // mailchimp list id value
            $mailchimp_list_id      = $event->process_string($this->data['_mailchimp_list_id']);

            // mailchimp double optin value
            $mailchimp_doubleoptin  = $event->process_string($this->data['_mailchimp_doubleoptin']);

        /* Get MailChimp parameters from WC setings */
            // API Key
            $mailchimp_api_key = get_option('wc_settings_tab_mailchimp_api_key',false);

        if ( ! class_exists( 'MailChimp_API_Tools' ) ) {
            require_once(SHOPMAGIC_BASE_DIR.'/includes/libraries/MailChimp_API_Tools.class.php');
        }

        try {
            $MailChimpTool = new MailChimp_API_Tools( $mailchimp_api_key );
        }
        catch (Exception $err) {
            error_log($err);
            return;
        }


        // Get customer information         
        $order = $event->get_order();

        // Add as new subscriber
        $MailChimpTool->add_member_from_order($order, $mailchimp_list_id, $mailchimp_doubleoptin );
    }

    /**
     * Show parameters window in an admin side widget
     *
     * @param $automation_id integer called automation id
     * @param $data array current action settings to set default values
     * @param $name_prefix string prefix for form control name attributes
     *
     * @since   1.0.0
     */
    static function show_parameters($automation_id, $data, $name_prefix)
    {

        if ($data['_action'] != self::$slug) { // if data are from other class type then cleanup data array
            $data = array();
        }
        // Read the default List ID value from Woocommerce settings ( ShopMagic Tab )
        $mc_default_list_id = get_option('wc_settings_tab_mailchimp_list_id','');

        if( $mc_default_list_id == '0' ){
            $mc_default_list_id = __('Not assigned yet!', 'shopmagic' );
        }

        // Read the default double optin value from Woocommerce settings ( ShopMagic Tab )
        $mc_default_double_optin = get_option('wc_settings_tab_mailchimp_double_optin','yes');

        if( !array_key_exists('_mailchimp_doubleoptin',$data) ){
            if( in_array(strtolower($mc_default_double_optin) , array('yes','on') ) ){
                $data['_mailchimp_doubleoptin'] = 'checked';
            }else{
                $data['_mailchimp_doubleoptin'] = '';
            }
        }else{
            if( in_array(strtolower($data['_mailchimp_doubleoptin']) , array('yes','on') ) ){
                $data['_mailchimp_doubleoptin'] = 'checked';
            }else{
                $data['_mailchimp_doubleoptin'] = '';
            }
        }

        $is_doubleoptin_checked = $data['_mailchimp_doubleoptin'];


        if ( ! class_exists( 'MailChimp_API_Tools' ) ) {
            require_once(SHOPMAGIC_BASE_DIR.'/includes/libraries/MailChimp_API_Tools.class.php');
        }

        $mc_apiKey_from_settings = get_option('wc_settings_tab_mailchimp_api_key', false);
        try {
            $MailChimpTools = new MailChimp_API_Tools($mc_apiKey_from_settings);
        }
        catch (Exception $err) {
            error_log($err);


            ?>
            <b><?php echo __('Please make sure about the MailChimp API key provided!'); ?></b><br>
            <a href="<?php  echo admin_url( 'admin.php?page=wc-settings&tab=shopmagic&section=mailchimp');?> " target="_blank"><?php echo __('Please, visit settings page: '); ?></a>

            <?php
            return;
        }
        
        // Set the lists names options
        $lists_names_options = $MailChimpTools->get_all_lists_options();

        ?>
       
        <div>
            <label for="mailchimp_list_name">List name:</label><br/>
            <select name="<?php echo $name_prefix;?>[_mailchimp_list_id]" id="mailchimp_list_name" >
                <?php

                $selected_current_list_id = $data['_mailchimp_list_id'];
                
                if( empty($data['_mailchimp_list_id']) ){
                    $selected_current_list_id = $mc_default_list_id;
                }

                $selected_or_not = "";
                foreach ($lists_names_options as $list_id => $list_name) {

                    if($selected_current_list_id == $list_id){
                        $selected_or_not = "selected";
                    }
                    ?>
                        <option value="<?php echo $list_id; ?>" <?php echo($selected_or_not);?>><?php echo $list_name; ?></option>
                    <?php
                }
                ?>
            </select>
            <span><?php _e('The default List ID is','shopmagic') ?> <code><?php echo($mc_default_list_id); ?></code></span>
        </div>
        <div>
            <label for="mailchimp_double-optin">Double-Optin </label>
            <input type="checkbox" name="<?php echo $name_prefix;?>[_mailchimp_doubleoptin]" id="mailchimp_double-optin" <?php echo $is_doubleoptin_checked; ?>/>
        </div>
        <?php
    }

    /**
     * Save parameters from POST request, called from an admin side widget
     *
     * in this method we should analyse $post array and store data accordingly in $data array
     *
     * @param $automation_id integer called automation id
     * @param $data array pointer to an array which is will be stored in meta for an automation
     * @param $post array part from $_POST array, which is belongs for a current action
     *
     * @since   1.0.0
     */
    static function save_parameters($automation_id, &$data, $post)
    {
        $data['_mailchimp_list_id'] = $post["_mailchimp_list_id"];
        // _mailchimp_doubleoptin
        $data['_mailchimp_doubleoptin'] = $post["_mailchimp_doubleoptin"];
    }

}

//*************************************************************************//
// ACTION SPECIFIC INITIALIZATION PART                                     //
//*************************************************************************//

class ShopMagic_AddToMailChimpList_Initialization {

    public function __construct() {

        // Only if checked on settings return false
        $subscribe_on_checkout_setting = get_option('wc_settings_sm_subscribe_on_checkout',false);
        if( $subscribe_on_checkout_setting && $subscribe_on_checkout_setting == 'yes'){

            add_action('woocommerce_checkout_after_customer_details', array($this, 'mc_subscribe_on_checkout_field'));
            add_action('woocommerce_checkout_update_order_meta', array($this, 'mc_subscribe_on_checkout_field_execute'));
        }
    }

    /**
     * MailChimp subscribe on checkout
     *
     * Enable the "MailChimp subscribe on checkout" feature if the user check it from the MagicShop settings
     *
     * @since   1.0.0
     */
    function mc_subscribe_on_checkout_field() {
        /* We may want to leave the comment signs '//' to create a header for the new checkbox */
        //echo '<div><h2>'.__('Subscribe to our newsletter', 'shopmagic' ).'</h2>';

        $checkout = WC()->checkout();

        woocommerce_form_field( 'mailchimp_subscribe_on_checkout', array(
            'type'          => 'checkbox',
            'class'         => array( 'form-row-wide' ),
            'label'         => __( 'Subscribe to our newsletter ', 'shopmagic' ),
        ), $checkout->get_value( 'mailchimp_subscribe_on_checkout' ));

        //echo '</div>';
    }

    /**
     * checkout update order meta
     *
     * @param $order_id int identification of order
     **/
    function mc_subscribe_on_checkout_field_execute( $order_id ) {
        // include static class MailChimp if not existing yet
        if ( ! class_exists( 'MailChimp' ) ) {
            require_once(SHOPMAGIC_BASE_DIR.'/includes/api/other/mailchimp/MailChimp.php');
        }

        if($_POST['mailchimp_subscribe_on_checkout']){
            // The field is checked, now add to newsletter

            // API Key from settings
            $mailchimp_api_key      = get_option('wc_settings_tab_mailchimp_api_key',false);

            // mailchimp list id settings value
            $mailchimp_list_id      = get_option('wc_settings_tab_mailchimp_list_id',false);

            // mailchimp double optin settings value
            $mailchimp_doubleoptin  = get_option('wc_settings_tab_mailchimp_double_optin',false);

            //
            $order = new WC_Order($order_id);

            if ( ! class_exists( 'MailChimp_API_Tools' ) ) {
                require_once(SHOPMAGIC_BASE_DIR.'/includes/libraries/MailChimp_API_Tools.class.php');
            }

            $MailChimpTool = new MailChimp_API_Tools( $mailchimp_api_key );

            // Add as new subscriber
            $MailChimpTool->add_member_from_order($order, $mailchimp_list_id, $mailchimp_doubleoptin );

        }
    }
}

// Activate the subscribe_on_checkout feature if it is checked from the ShopMagic settings
new ShopMagic_AddToMailChimpList_Initialization();


