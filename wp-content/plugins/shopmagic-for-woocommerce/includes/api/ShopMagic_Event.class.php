<?php
/**
 * ShopMagic's Events Base class.
 *
 * Register and handle events which can be happen.
 * Events has hierarchical taxonomy, like categories
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
 * ShopMagic Events Base class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
abstract class ShopMagic_Event extends ShopMagic_Entity {

    /**
     * @var string group slug
     */
    static protected $group='';

    /**
     * @var array[] hash list of supported placeholders "slug"=>"Name"
     */
    static protected $placeholders = array();

    /**
     * @var array[] hash list of placeholders values "slug"=>Value
     */
    protected $placeholders_values = array();

    /**
     * Default constructor.
     *
     * @param $core ShopMagic instance of main plugin class
     * @param $automation_id integer called automation id
     *
     * @since   1.0.0
     */
    function __construct(ShopMagic $core, $automation_id) {
        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct($core, $automation_id);
        $this->setup_hook();
        add_filter('shopmagic_placeholders_event_'.static::$slug,array($this,'product_add_placeholder'));
        add_filter('shopmagic_placeholders_values_event_'.static::$slug,array($this,'product_add_placeholder_value'),10);

    }

    /**
     * group variable getter.
     *
     * @since   1.0.0
     * @return integer
     */
    static function get_group() {
        return static::$group;
    }

    /**
     * Register event's hook
     *
     * register event's hook
     *
     * @since   1.0.0
     */
    abstract protected function setup_hook();

    /**
     * Setup placeholders values
     *
     * Before action run setup values for a placeholders
     *
     * @since   1.0.0
     */
    abstract protected function setup_placeholders();

    /**
     * Run registered actions from automation
     *
     *@since 1.0.0
     */
    protected function run_actions() {

        $actions = get_post_meta( $this->automation_id, '_actions', true );

        $this->setup_placeholders();
        $this->placeholders_values = apply_filters('shopmagic_placeholders_values_event_'.static::$slug,$this->placeholders_values, $this );

        $shopmagic_debug_setting = get_option('wc_settings_sm_debug',false);
        $shopmagic_store_messages_setting = get_option('wc_settings_sm_store_messages',false);


        if (is_array($actions)) { // if meta exists and it is an array

            $shopmagic_debug_messages = '';
            foreach ($actions as $key => $action) { //run each registered action
                $action_slug = $action['_action'];
                $action_class_name = $this->core->get_action($action_slug);
                $action_class = new $action_class_name($this->core, $this->automation_id, $action);

                $is_action_delayed = false;

                if(array_key_exists('_action_delayed', $action) && $action['_action_delayed'] == 'on'){
                    $is_action_delayed = true;

                    // Delay offset
                    ($action['_action_delayed_offset_time'] == '' )?  $action_delayed_offset_time = 0 : $action_delayed_offset_time = $action['_action_delayed_offset_time'];

                }

                if( $is_action_delayed ){ // If delayed
                    // time() + : to start concidering the delay from the current UTC time
                    // default exec time even if 'after_event' option is selected from the _action_delay_after select box
                    $action_exec_time = time() + $action_delayed_offset_time;

                    if ( $action['_action_delay_after'] == 'after_last_action' && intval($key) > 0) {

                        // Get offset time of last action if this is not the fist action not $key 0
                        $offset_of_last_action = $actions[ ($key - 1) ]['_action_delayed_offset_time'];

                        // Update the execution time
                        $action_exec_time = time() + $offset_of_last_action + $action_delayed_offset_time;
                    }

                    // Create the single schedule cron
                    wp_schedule_single_event( $action_exec_time , 'shopmagic_delayed_action_hook', array($action_class, $this, $key) );

                }else{ // If not delayed run immediately
                    $action_class->execute($this, $key);
                }


                $shopmagic_debug_messages .="\r\n\tEvent : '".$this->get_name()."' ===> "." Action : '".$action_class->get_name()."'";
            }


            // if debug mode checked from ShopMagic settings page
            if($shopmagic_debug_setting && $shopmagic_debug_setting == 'yes'){
                $err_log_message =  "\r\n===== ".__('Shopmagic Debug','shopmagic')." =====";
                $err_log_message .= $shopmagic_debug_messages;
                $err_log_message .= "\r\n====================";
                error_log($err_log_message);
            }

            // if debug mode checked from ShopMagic settings page
            if($shopmagic_store_messages_setting && $shopmagic_store_messages_setting == 'yes'){
                SHOPMAGIC_LOGGER::log('DEBUG','ShopMagic Events','Event: '.$this->get_name(),$shopmagic_debug_messages );
            }

            // if builtin error log enabled

        }

    }

    /**
     * Show parameters window in an admin side widget
     *
     * @param $automation_id integer current displayed automation
     * @since   1.0.0
     */
    static function show_parameters($automation_id) {

    }

    /**
     * Save parameters from POST request, called from an admin side widget
     *
     * @param $automation_id integer current displayed automation
     * @since   1.0.0
     */
    static function save_parameters($automation_id) {

    }

    /**
     * Provide list of a placeholders, supported by this event
     *
     * @since   1.0.0
     */
    static function get_placeholders() {
        return apply_filters('shopmagic_placeholders_event_'.static::$slug,static::$placeholders );
    }

    /**
     * Placeholders values' getter
     *
     * @return array[]
     * @since   1.0.0
     */
    function get_placeholders_values()
    {
        return  $this->placeholders_values;
    }

    /**
     * Placeholder value setter
     *
     * @param string $placeholder Placeholder name
     * @param string $placeholder_value Placeholder value
     */
    function set_placeholders_values($placeholder,$placeholder_value)
    {
        $this->placeholders_values[$placeholder] = $placeholder_value;
    }

    /**
     * Generate HTML code to show available placeholders in admin area
     *
     * @return string HTML code with placeholders list
     * @since   1.0.0
     */
    static function show_placeholders() {

        $placeholders_list = apply_filters('shopmagic_placeholders_event_'.static::$slug,static::$placeholders );

        $result = '';
        foreach ($placeholders_list as $placeholder => $description) {

            if ($description[0] === '_') {
                continue;
            }
            $result .= '{{'. $placeholder.'}}<br />';

        };

        return $result;
    }

    /**
     * Returns the description of the current Event
     *
     * @return string Event description
     * @since   1.0.4
     */
    static function show_description() {
        if( isset(static::$description) ){
            return static::$description;
        }else{
            return __('No description provided for this event.','shopmagic');
        }
    }

    /**
     * Returns the filter for the current Event
     *
     * @return string Event filter
     * @since   1.2.5
     */
    static function show_filter() {
        if( isset(static::$filter) ){
            return static::$filter;
        } else{
            return __('This Filter is not available for the above event','shopmagic');
        }
    }

    /**
     * Process string to replace placeholders
     *
     * Check string for a placeholders tag ({{ <tag> }}) and replace iy by particular value
     * If event doesn't support this tag - just remove tag
     *
     * @param $string string to be processed
     *
     * @return string processed string
     * @since   1.0.0
     */
    public function process_string($string) {

        $pattern = '/{{\s*%s\s*}}/';
        $result = $string;
        var_dump($this->placeholders_values);
        foreach ($this->placeholders_values as $placeholder => $value) {
            if (is_array($value)) { // array values can be proccessed only via Twig
                continue;
            }
            $regexp = sprintf($pattern, $placeholder);
            $result = preg_replace($regexp, $value, $result);
        }

        // cleanup for unused placeholders
        $pattern = '/{{.*?}}/';
        $result = preg_replace($pattern, '', $result);

        return $result;

    }

    public function product_add_placeholder($placeholders) {
        $placeholders_new = array_slice($placeholders,0,5, true) +
            array('products_ordered' => 'List of ordered products') +
            array_slice($placeholders,5,NULL,true);
        return $placeholders_new;
    }

    public function product_add_placeholder_value($placeholder_values)
    {
        $order = $this->get_order();

        $order_items = $order->get_items();

        // Add product ordered placeholder value.
        $placeholder_values['products_ordered'] = '<ul>';
        foreach ($order_items as $id => $val) {
            $placeholder_values['products_ordered'] .= '<li>' . $val['name'] . ' ' . (print_r($val)) . '</li>';
        }
        $placeholder_values['products_ordered'] .= '</ul>';
        return $placeholder_values;
    }

}