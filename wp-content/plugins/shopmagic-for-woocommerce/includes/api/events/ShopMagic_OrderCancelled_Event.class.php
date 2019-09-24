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

if ( ! class_exists( 'ShopMagic_OrderCommon_Event' ) ) {
    require_once(SHOPMAGIC_BASE_DIR.'/includes/api/events/ShopMagic_OrderCommon_Event.class.php');
}

/**
 * ShopMagic Order Processing Event class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
class ShopMagic_OrderCancelled_Event extends ShopMagic_OrderCommon_Event {


    static protected $name = "Order Cancelled";
    static protected $slug = "shopmagic_order_cancelled_event";
    static protected $description = 'Triggered when order status is set to cancelled';

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

         add_action('woocommerce_order_status_cancelled', array($this, 'process_event'));
     }

}
