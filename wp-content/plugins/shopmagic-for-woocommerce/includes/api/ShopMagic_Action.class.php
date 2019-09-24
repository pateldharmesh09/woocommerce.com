<?php
/**
 * ShopMagic's Actions Base class.
 *
 * Register and handle action which can be running.
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ShopMagic_Entity' ) ) {
    require_once(SHOPMAGIC_BASE_DIR.'/includes/api/ShopMagic_Entity.class.php');
}

/**
 * ShopMagic Actions Base class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
abstract class ShopMagic_Action extends ShopMagic_Entity {

    /**
     * @var array current action settings
     */
    protected $data;

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
        parent::__construct($core, $automation_id);

        $this->data = $data;
    }

    /**
     * Execute an action
     *
     * @param $event ShopMagic_Event an event which is fired this action
     * @param $key int order in automation action list
     * @since   1.0.0
     */
    abstract protected function execute($event, $key);

    /**
     * Show parameters window in an admin side widget
     *
     * @param $automation_id integer called automation id
     * @param $data array current action settings to set default values
     * @param $name_prefix string prefix for form control name attributes
     *
     * @since   1.0.0
     */
    static function show_parameters($automation_id, $data, $name_prefix) {

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
    static function save_parameters($automation_id, &$data, $post) {

    }

    /**
     * Check data domain in events and actions
     *
     * if action contains data which is not provided in event, we return false;
     *
     * @param $automation_id integer called automation id
     * @param $core ShopMagic
     *
     * @return boolean
     * @since   1.0.0
     */
    static function check_compatibility($automation_id, $core) {
        $event_slug = get_post_meta( $automation_id, '_event', true );


        $event_class = $core->get_event($event_slug);
        if ($event_class) {

            // here we find common data domains for an event and an action
            $common_domains = array_intersect($event_class::get_data_domains(), self::get_data_domains());

            // if common data domains similar to action required data  domain, then action compatible with an event
            if (self::get_data_domains() == $common_domains) {
                return true;
            }
            else {
                return false;
            }

        }
        else {
            // no event class exists for this slug
            return false;
        }
    }


}
