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
class ShopMagic_NewAccount_Event extends ShopMagic_Event {


    static protected $name = "New Account Event";
    static protected $slug = "shopmagic_new_account_event";
    static protected $description = 'Triggered when new customer account gets created via WooCommerce';
    static protected $group = 'users';
    static protected $data_domains = array('user');

    /**
     * @var array[] hash list of supported placeholders "slug"=>"Name"
     */
    static protected $placeholders = array(
        'customer_id' => "Customer ID",
        'customer_name' => "Customer Name",
        'customer_first_name' => "Customer First Name",
        'customer_last_name' => "Customer Last Name",
        'customer_email' => "Customer Email",

        // legacy placeholders, to backward compatibility
        // name which is starts with underscore is not shown in placeholders list
        'user_id' => "_User ID",
        'user_name' => "_User Name",
        'user_first_name' => "_User First Name",
        'user_last_name' => "_User Last Name",
        'user_email' => "_User Email",

    );


    /**
     * @var integer
     */
    protected $user_id;

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
     * Register event's hook
     *
     * register event's hook
     *
     * @since   1.0.0
     */
    protected function setup_hook() {

        add_action('user_register', array($this, 'process_event'),10,1);
    }

    /**
     * Processing order event hook handler
     *
     * @param $user_id integer
     * @since   1.0.0
     */
    function process_event( $user_id) {

        $this->user_id = $user_id;

        $this->run_actions();
    }

    /**
     * Returns the user objects, associated with an event
     *
     * @return WP_User
     * @since   1.0.0
     */
    function get_user()
    {
        return new WP_User($this->user_id);
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
        $user = new WP_User($this->user_id);

        $this->placeholders_values = array(
            'customer_id' => $user->ID,
            'customer_name' => $user->display_name,
            'customer_first_name' => $user->first_name,
            'customer_last_name' => $user->last_name,
            'customer_email' => $user->user_email,

            // legacy placeholders
            'user_id' => $user->ID,
            'user_name' => $user->display_name,
            'user_first_name' => $user->first_name,
            'user_last_name' => $user->last_name,
            'user_email' => $user->user_email
        );


    }

}
