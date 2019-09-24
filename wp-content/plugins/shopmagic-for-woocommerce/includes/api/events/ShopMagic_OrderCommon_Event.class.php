<?php
/**
 * ShopMagic's Order Processing Event class.
 *
 * Handle WooCommerce order status change in the "processing" state
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ShopMagic_Event' ) ) {
    require_once(SHOPMAGIC_BASE_DIR.'/includes/api/ShopMagic_Event.class.php');
}

/**
 * ShopMagic Order Processing Event class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
abstract class ShopMagic_OrderCommon_Event extends ShopMagic_Event {


    static protected $name = "Order Common Event";
    static protected $slug = "shopmagic_order_common_event";
    static protected $group = 'orders';
    static protected $data_domains = array('user','order');
    static protected $filter = '';

    /**
     * @var array[] hash list of supported placeholders "slug"=>"Name"
     */
    static protected $placeholders = array(

        'customer_id' => "Customer ID",
        'customer_name' => "Customer Name",
        'customer_first_name' => "Customer First Name",
        'customer_last_name' => "Customer Last Name",
        'customer_email' => "Customer Email",
        'order_id' => "Order ID",
        'order_total' => "Order Total",
        'order_date' => "Order Date",

        // legacy placeholders, to backward compatibility
        // name which is starts with underscore is not shown in placeholders list
        'user_id' => "_User ID",
        'user_name' => "_User Name",
        'user_first_name' => "_User First Name",
        'user_last_name' => "_User Last Name",
        'user_email' => "_User Email"
    );


    /**
     * @var integer current processing order id
     */
    protected $order_id;

    /**
     * Default constructor.
     *
     * @since   1.0.0
     */
    function __construct(ShopMagic $core, $automation_id) {
        // Call parent constructor to load any other defaults not explicitly defined here
        parent::__construct($core, $automation_id);

        // Setup class variables


    }

    /**
     * Processing order event hook handler
     *
     * Callback for a 'woocommerce_order_status_processing' action
     * we can perform here any additional check before we run actions
     *
     * @param $order_id integer id of order, which fires action
     * @since   1.0.0
     */
    function process_event($order_id) {

        $this->order_id = $order_id;
        $this->run_actions();
    }

    /**
     * Returns the $order_id protected var
     *
     * @return integer
     * @since   1.0.0
     */
    function get_order_id(){
        return $this->order_id;
    }

    /**
    * Returns the order objects, associated with an event
    *
    * @return WC_Order
    * @since   1.0.0
    */
    function get_order(){
        $order_factory = new WC_Order_Factory();
        return $order_factory->get_order($this->order_id);
    }

    /**
     * Returns the user objects, associated with an event
     *
     * @return WP_User
     * @since   1.0.0
     */
    function get_user(){
        $order = $this->get_order();
        return $order->get_user();
    }

    /**
     * Show parameters window in an admin side widget
     *
     * @param $automation_id integer current displayed automation
     * @since   1.0.0
     */
    static function show_parameters($automation_id)
    {

    }

    /**
     * Save parameters from POST request, called from an admin side widget
     *
     * @param $automation_id integer current displayed automation
     * @since   1.0.0
     */
    static function save_parameters($automation_id)
    {

    }


    /**
     * Setup placeholders values
     *
     * Before action run setup values for a placeholders
     *
     * @since   1.0.0
     */
    protected function setup_placeholders()
    {
        $order = $this->get_order();

        if ($order) {

            $user = $this->get_user();

            $this->placeholders_values = array(
                'customer_id' => $user->ID,
                'customer_name' => ( !empty( $user->display_name ) ? $user->display_name : $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ),
                'customer_first_name' => ( !empty( $user->first_name ) ? $user->first_name : $order->get_billing_first_name() ),
                'customer_last_name' => ( !empty( $user->last_name ) ? $user->last_name : $order->get_billing_last_name() ),
                'customer_email' => $order->get_billing_email(),
                // legacy placeholders
                'order_id' => $order->get_id(),
                'order_total' => $order->get_total(),
                'order_date' => $order->get_date_created(),
                'user_id' => $user->ID,
                'user_name' => $user->display_name,
                'user_first_name' => $user->first_name,
                'user_last_name' => $user->last_name,
                'user_email' => $order->get_billing_email(),
            );

        }
    }

}
