<?php
/**
 * ShopMagic's Filters Base class.
 *
 * Register and handle filters which can be happen.
 * Filters has hierarchical taxonomy, like categories
 *
 * @package ShopMagic
 * @version 1.2.5
 * @since   1.2.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ShopMagic_Entity' ) ) {
    require_once(SHOPMAGIC_BASE_DIR.'/includes/api/ShopMagic_Entity.class.php');
}
/**
 * ShopMagic Filters Base class
 *
 * @package ShopMagic
 * @since   1.2.5
 */
abstract class ShopMagic_Filter extends ShopMagic_Entity {

    /**
     * Default constructor.
     *
     * @param $core ShopMagic instance of main plugin class
     * @param $automation_id integer called automation id
     *
     * @since   1.2.5
     */
    function __construct(ShopMagic $core, $automation_id) {
        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct($core, $automation_id);
        $this->setup_hook();
    }


    /**
     * Register filter's hook
     *
     * register filter's hook
     *
     * @since   1.2.5
     */
    abstract protected function setup_hook();

    /**
     * Setup placeholders values
     *
     * Before action run setup values for a placeholders
     *
     * @since   1.2.5
     */
    abstract protected function setup_placeholders();

    /**
     * Run registered actions from automation
     *
     *@since 1.2.5
     */
    protected function run_actions() {

        $actions = get_post_meta( $this->automation_id, '_actions', true );

        $this->setup_placeholders();
        $this->placeholders_values = apply_filters('shopmagic_placeholders_values_filter_'.static::$slug,$this->placeholders_values, $this );

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
                    // default exec time even if 'after_filter' option is selected from the _action_delay_after select box
                    $action_exec_time = time() + $action_delayed_offset_time;

                    if ( $action['_action_delay_after'] == 'after_last_action' && intval($key) > 0) {

                        // Get offset time of last action if this is not the fist action not $key 0
                        $offset_of_last_action = $actions[ ($key - 1) ]['_action_delayed_offset_time'];

                        // Update the execution time
                        $action_exec_time = time() + $offset_of_last_action + $action_delayed_offset_time;
                    }

                    // Create the single schedule cron
                    wp_schedule_single_filter( $action_exec_time , 'shopmagic_delayed_action_hook', array($action_class, $this, $key) );
                
                }else{ // If not delayed run immediately
                    $action_class->execute($this, $key);
                }
                

                $shopmagic_debug_messages .="\r\n\tFilter : '".$this->get_name()."' ===> "." Action : '".$action_class->get_name()."'";
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
                SHOPMAGIC_LOGGER::log('DEBUG','ShopMagic Filters','Filter: '.$this->get_name(),$shopmagic_debug_messages );
            }

            // if builtin error log enabled

        }

    }

    /**
     * Show parameters window in an admin side widget
     *
     * @param $automation_id integer current displayed automation
     * @since   1.2.5
     */
    static function show_parameters($automation_id) {

    }

    /**
     * Save parameters from POST request, called from an admin side widget
     *
     * @param $automation_id integer current displayed automation
     * @since   1.2.5
     */
    static function save_parameters($automation_id) {

    }

    /**
     * Provide list of a placeholders, supported by this filter
     *
     * @since   1.2.5
     */
    static function get_placeholders() {
        return apply_filters('shopmagic_placeholders_filter_'.static::$slug,static::$placeholders );
    }

    /**
     * Returns the description of the current Filter
     *
     * @return string Filter description
     * @since   1.0.4
     */
    static function show_description() {
        if( isset(static::$description) ){
            return static::$description;
        }else{
            return __('No description provided for this filter.','shopmagic');
        }        
    }

    /**
     * Returns the filter for the current Filter
     *
     * @return string Filter filter
     * @since   1.2.5
     */
    static function show_filter() {
        if( isset(static::$filter) ){
            return static::$filter;
        } else{
            return __('This Filter is not available for the above filter','shopmagic');
        }      
    }

    /**
     * Process string to replace placeholders
     *
     * Check string for a placeholders tag ({{ <tag> }}) and replace iy by particular value
     * If filter doesn't support this tag - just remove tag
     *
     * @param $string string to be processed
     *
     * @return string processed string
     * @since   1.2.5
     */
    public function process_string($string) {

        $pattern = '/{{\s*%s\s*}}/';
        $result = $string;
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

}