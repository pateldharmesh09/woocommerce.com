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
class ShopMagic_PasswordReset_Event extends ShopMagic_Event {


    static protected $name = "Password Reset Event";
    static protected $slug = "shopmagic_password_reset_event";
    static protected $description = 'Triggered when a customer resets their password';
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
        'new_password' => "New Password",

        // legacy placeholders, to backward compatibility
        // name which is starts with underscore is not shown in placeholders list
        'user_id' => "_User ID",
        'user_name' => "_User Name",
        'user_first_name' => "_User First Name",
        'user_last_name' => "_User Last Name",
        'user_email' => "_User Email",
    );


    /**
     * @var WP_User
     */
    protected $user;

    /**
     * @var string
     */
    protected $new_pass;

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

        add_action('password_reset', array($this, 'process_event'),10,2);
    }

    /**
     * Processing order event hook handler
     *
     * @param $order_id integer id of order, which fires action
     * @since   1.0.0
     */
    function process_event( $user, $new_pass) {

        $this->user = $user;
        $this->new_pass = $new_pass;
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
        return $this->user;
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

        $this->placeholders_values = array(
            'customer_id' => $this->user->ID,
            'customer_name' => $this->user->display_name,
            'customer_first_name' => $this->user->first_name,
            'customer_last_name' => $this->user->last_name,
            'customer_email' => $this->user->user_email,
            'new_password' => $this->new_pass,
            // legacy placeholders
            'user_id' => $this->user->ID,
            'user_name' => $this->user->display_name,
            'user_first_name' => $this->user->first_name,
            'user_last_name' => $this->user->last_name,
            'user_email' => $this->user->user_email,
        );


    }

}
