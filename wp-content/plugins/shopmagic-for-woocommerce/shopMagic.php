<?php
/*
Plugin Name: ShopMagic for WooCommerce
Plugin URI: http://shopmagic.app/
Description: Marketing Automation and Custom Email Designer for WooCommerce
Version: 1.3.1
Author:  RistrettoApps
Author URI:
Text Domain: shopmagic
*/

if (!defined('ABSPATH')) exit;

//ShopMagic Version for Welcome Page (leave atop as to not forget to change)
if ( ! defined( 'SHOPMAGIC_VERSION' ) )
    define( 'SHOPMAGIC_VERSION', '1.3.1' );


/**
 * ShopMagic core class.
 *
 * Register plugin and make instances of core classes
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */
class ShopMagic {

    /**
     * List of available events
     *
     * @var array
     */
    private $events = array();

    /**
     * @var array hash list of available groups, uses to display in list
     */
    protected $eventGroups=array();

    /**
     * List of available actions
     *
     * @var array
     */
    private $actions = array();

    /**
     * List of available filters
     *
     * @var array
     */
    private $filters = array();

    /**
     * Default constructor.
     *
     * Initialize plugin core and build environment
     *
     * @since   1.0.0
     */
    function __construct() {

        $this->define_constants();
        //$this->includes();

        $this->add_actions();

        require_once('includes/Automations_PostType.class.php');
        require_once('includes/ShopMagic_CreateTables.class.php');
        require_once('includes/admin/ShopMagic_Admin-Alerts-Popups.php');

        // initialize core classes
        new ShopMagic_Automations_PostType();
        new ShopMagic_CreateTables();

        //Initialize Welcome Page
        $this->initiate_welcome_page();

    }

    /**
     * Shows admin message if WooCommerce is not active.
     *
     * @since   1.0.0
     */
    function shopmagic_wc_needed_error_notice() {
        ?>
        <div class="error notice">
            <p><?php _e( 'Shopmagic need WooCommerce to be installed.', 'shopmagic' ); ?></p>
        </div>
        <?php

    }

    /**
     *  Check plugin requirement's
     */
    public function check_requirements() {
        if( !class_exists( 'WooCommerce' ) ) {
            // The plugin ShopMagic is turned on
            add_action( 'admin_notices', array($this,'shopmagic_wc_needed_error_notice' ));
        }
    }

    /**
     * Setup entities and perform requirements check for plugin.
     *
     * @since   1.0.0
     */
    function setup_entities() {


        $this->events = array(
            'shopmagic_order_new_event'=>'ShopMagic_OrderNew_Event',
            'shopmagic_order_pending_event'=>'ShopMagic_OrderPending_Event',
            'shopmagic_order_processing_event'=>'ShopMagic_OrderProcessing_Event',
            'shopmagic_order_cancelled_event'=>'ShopMagic_OrderCancelled_Event',
            'shopmagic_order_completed_event'=>'ShopMagic_OrderCompleted_Event',
            'shopmagic_order_failed_event'=>'ShopMagic_OrderFailed_Event',
            'shopmagic_order_on_hold_event'=>'ShopMagic_OrderOnHold_Event',
            'shopmagic_order_refunded_event'=>'ShopMagic_OrderRefunded_Event',

            'shopmagic_password_reset_event' => 'ShopMagic_PasswordReset_Event',
            'shopmagic_new_account_event' => 'ShopMagic_NewAccount_Event',
        );

        $this->eventGroups = array(
            'orders' => __('Orders', 'shopmagic'),
            'users' => __('User Management', 'shopmagic')
        );

        $this->actions = array(
            'shopmagic_sendemail_action'=>'ShopMagic_SendEmail_Action',
            'shopmagic_addtomailchimplist_action'=>'ShopMagic_AddToMailChimpList_Action'
        );

        $this->filters = array(
            'shopmagic_product_purchased_filter' => 'ShopMagic_Product_Purchased_Filter',
        );

        // Collecting available events, actions and placeholders
        $this->events = apply_filters( 'shopmagic_events', $this->events);
        $this->eventGroups = apply_filters( 'shopmagic_event_groups', $this->eventGroups);
        $this->actions = apply_filters( 'shopmagic_actions', $this->actions);
        $this->filters = apply_filters( 'shopmagic_filters', $this->filters);

        do_action('shopmagic_initialize_filters');
        do_action('shopmagic_initialize_events');
        do_action('shopmagic_initialize_actions');
        if ( wp_doing_ajax() ) {
            do_action('shopmagic_initialize_product_filter');
        }

    }

    /**
     * Definition wrapper.
     *
     * Creates some useful def's in environment to handle
     * plugin paths
     *
     * @since   1.0.0
     */
    function define_constants() {

        if ( ! defined( 'SHOPMAGIC_BASE_FILE' ) )
            define( 'SHOPMAGIC_BASE_FILE', __FILE__ );
        if ( ! defined( 'SHOPMAGIC_BASE_DIR' ) )
            define( 'SHOPMAGIC_BASE_DIR', dirname( SHOPMAGIC_BASE_FILE ) );
        if ( ! defined( 'SHOPMAGIC_PLUGIN_URL' ) )
            define( 'SHOPMAGIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        if ( ! defined( 'SHOPMAGIC_PLUGIN_DIR' ) )
            define( 'SHOPMAGIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
          if ( ! defined( 'SHOPMAGIC_DIR_NAME' ) ) //Plugin Folder Name.
            define( 'SHOPMAGIC_DIR_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );

        //ShopMagic DB Version
        if ( ! defined( 'SHOPMAGIC_DB_VERSION' ) )
            define( 'SHOPMAGIC_DB_VERSION', '1.0' );

        require_once('includes/api/ShopMagic_Logger.class.php');
        if ( ! defined( 'SHOPMAGIC_LOGGER' ) )
            define( 'SHOPMAGIC_LOGGER', 'ShopMagic_Logger' );
    }


    /**
     * Include wrapper.
     *
     * Includes core classes
     * @since   1.0.0
     */
    function includes() {

        //Ensure Twig Autoloader hasn't already been declared, then load Composer classes
        if(!in_array("Twig_Autoloader", get_declared_classes())) {

            // Composer classes autoloader
			require_once ('vendor/autoload.php');
        }

        // Include base classes for events and actions
        require_once('includes/api/ShopMagic_Event.class.php');
        require_once('includes/api/ShopMagic_Action.class.php');

        require_once('includes/libraries/ShopMagic_Template.class.php');
    }

    /**
     * Adds action hooks.
     *
     * @since   1.0.0
     */
    private function add_actions() {

        add_action('shopmagic_initialize_events', array($this, 'initialize_default_events'));
        add_action('shopmagic_initialize_actions', array($this, 'initialize_default_actions'));
        add_action('shopmagic_initialize_filters', array($this, 'initialize_default_filters'));
        add_action('wp_loaded', array($this, 'check_requirements'));
        add_action('wp_loaded', array($this,'register_active_automations'),30);
        add_action('wp_loaded', array($this,'includes'),20);
        add_action('wp_loaded', array($this, 'product_purchased_filter_backward' ), 10 );
        add_action('admin_init',array($this, 'setup_admin_area' ));
        add_action('wp_loaded', array($this,'setup_entities'),20);
        add_action('shopmagic_initialize_product_filter',array($this, 'register_product_purchased_filter_ajax'));

        add_filter( 'mce_external_plugins', array($this, 'setup_tinymce_pluign'));
        // Add TinyMCE custom button
        add_filter( 'mce_buttons', array($this, 'add_tinymce_toolbar_button'));
    }


    /**
     * Update Options for backward compatibility
     *
     * @since   1.3.1
     */
    public function product_purchased_filter_backward() {

        global $post;

        $args = array(
            'post_type'     =>  'shopmagic_automation',
            'meta_query' => array(
                array(
                    'key'     => '_event',
                    'value'   => 'shopmagic_product_purchased_event',
                    'compare' => '=',
                ),
            ),
        );

        $update_option_query = new WP_Query( $args );

        if( $update_option_query->have_posts() ) {
          while( $update_option_query->have_posts() ) {
            $update_option_query->the_post();
            update_post_meta( $post->ID, '_event', 'shopmagic_order_completed_event' );
            update_post_meta( $post->ID, '_filter', 'shopmagic_product_purchased_filter' );
          } // end while
        } // end if
        wp_reset_postdata();
    }


    /**
     * Includes additional TinyMCE plugins, which is not shipped with WP
     *
     * @param array $plugins array of plugins
     * @return array array of plugins
     */
    public function setup_tinymce_pluign($plugins) {
        $plugins['imgalign'] = SHOPMAGIC_PLUGIN_URL.'/assets/js/tinymce/imgalign/plugin.js' ;
//        $plugins['legacyoutput'] = SHOPMAGIC_PLUGIN_URL.'/assets/js/tinymce/legacyoutput/plugin.min.js' ;
        return $plugins;
    }

    /**
     * Adds a button to the TinyMCE / Visual Editor which the user can click
     * to insert a link with a custom CSS class.
     *
     * @param array $buttons Array of registered TinyMCE Buttons
     * @return array Modified array of registered TinyMCE Buttons
     */
    function add_tinymce_toolbar_button($buttons) {
        array_push($buttons, '|', 'imgalign');
        return $buttons;
    }

    /**
     * Run class ShopMagic_Product_Purchased_Filter for saving actions with products in settings page
     * via Ajax (adding, deleting)
     *
     *
     * @since   1.1.
     */
    public function register_product_purchased_filter_ajax() {
        $post_data = ( isset( $_POST['post'] ) ? intval($_POST['post']) : '' );
        new ShopMagic_Product_Purchased_Filter( $this, $post_data );
    }

    /**
     * Setup admin area class
     *
     *
     * @since   1.0.0
     */
    public function setup_admin_area() {
        require_once('includes/admin/ShopMagic_Admin.class.php');
        new ShopMagic_Admin($this);
        //  Include feedback survey page
        if ( file_exists( SHOPMAGIC_BASE_DIR . '/includes/admin/Shopmagic_Survey.php' ) ){
            require_once('includes/admin/Shopmagic_Survey.php');
        }

    }


    /**
     * Initializes default Events.
     *
     * Load classes with default events
     *
     * @since   1.0.0
     */
    function initialize_default_events() {

        foreach ($this->events as $event) {
            if (!class_exists($event)) {
                include_once(SHOPMAGIC_BASE_DIR . '/includes/api/events/'.$event.'.class.php');
            }
        }
    }

    /**
     * Initializes default Actions.
     *
     * Load classes with default actions
     *
     * @since   1.0.0
     */
    function initialize_default_actions() {

        foreach ($this->actions as $action) {
            if (!class_exists($action) && file_exists(SHOPMAGIC_BASE_DIR . '/includes/api/actions/'.$action.'.class.php')) {
                include_once(SHOPMAGIC_BASE_DIR . '/includes/api/actions/'.$action.'.class.php');
            }
        }
    }


    /**
     * Initializes default Filters.
     *
     * Load classes with default filters
     *
     * @since   1.2.5
     */
    function initialize_default_filters() {

        foreach ( $this->filters as $filter ) {
            if ( ! class_exists( $filter ) ) {
                include_once( SHOPMAGIC_BASE_DIR . '/includes/api/filters/'.$filter.'.class.php' );
            }
        }
    }

    /**
     * Returns array event classes.
     *
     *
     * @return ShopMagic_Event[]
     * @since   1.0.0
     */
    function get_events() {

        return $this->events;
    }

    /**
     * Returns array  action classes.
     *
     *
     * @return ShopMagic_Action[]
     * @since   1.0.0
     */
    function get_actions() {

        return $this->actions;
    }

    /**
     * Returns array filter classes.
     *
     *
     * @return ShopMagic_Filter[]
     * @since   1.2.5
     */
    function get_filters() {

        return $this->filters;
    }

    /**
     * Returns event class name.
     *
     *
     * @return ShopMagic_Event
     * @param $slug string event slug
     * @since   1.0.0
     */
    function get_event($slug) {

        return $this->events[$slug];
    }

    /**
     * Returns array of initialized action objects .
     *
     *
     * @param $slug string action slug
     * @return ShopMagic_Action
     * @since   1.0.0
     */
    function get_action($slug) {

        return $this->actions[$slug];
    }

    /**
     * Returns filter class name.
     *
     *
     * @return ShopMagic_Filter
     * @param $slug string filter slug
     * @since   1.2.5
     */
    function get_filter($slug) {

        return $this->filters[$slug];
    }


    /**
     * Resolves event group slug into printable name
     *
     * @param $slug string
     * @return string
     * @since   1.0.0
     */
    function get_event_group_name($slug) {
        return $this->eventGroups[$slug];
    }

    /**
     * Register active automations
     *
     * Select active automations,  register according events and setup its classes
     *
     * @since   1.0.0
     */
    function register_active_automations() {

        $args = array(
            'post_type' => 'shopmagic_automation',
            'post_status' => 'publish', // only active automations
            'posts_per_page'=>-1   // all of them
        );

        $automations = new WP_Query( $args );

        if ( $automations->have_posts() ) {
            while ( $automations->have_posts() ) {
                $automations->the_post();

                // get the event;
                $event_slug = get_post_meta(get_the_ID(), '_event', true);

                if (isset($this->events[$event_slug])) {
                    new $this->events[$event_slug]($this, get_the_ID());
                }

            }
            /* Restore original Post Data */
            wp_reset_postdata();
        };
    }

    /**
     * Setup Welcome Mat Redirect
     *
     * Select active automations,  register according events and setup its classes
     *
     * @since   1.0.0
     */
    function initiate_welcome_page() {

        // Add the transient on plugin activation.
        if ( ! function_exists( 'shopmagic_welcome_page' ) ) {

            // Hook that runs on plugin activation.
            register_activation_hook( SHOPMAGIC_BASE_FILE, 'welcome_activate' );
            /**
             * Add the transient.
             *
             * Add the welcome page transient.
             *
             * @since 1.0.0
             */
            function welcome_activate() {

                // Transient max age is 60 seconds.
                set_transient( '_welcome_redirect_shopmagic', true, 60 );
            }
        }
        // Delete the Transient on plugin deactivation.
        if ( ! function_exists( 'shopmagic_welcome_page' ) ) {
            // Hook that runs on plugin deactivation.
            register_deactivation_hook( SHOPMAGIC_BASE_FILE, 'welcome_deactivate' );

            /**
             * Delete the Transient on plugin deactivation.
             *
             * Delete the welcome page transient.
             *
             * @since   2.0.0
             */
            function welcome_deactivate() {
              delete_transient( '_welcome_redirect_shopmagic' );
            }
        }

        //Welcome Page Initiation
        if ( file_exists( SHOPMAGIC_BASE_DIR . '/includes/welcome/shopmagic_welcome-init.php' ) )
            require_once( SHOPMAGIC_BASE_DIR . '/includes/welcome/shopmagic_welcome-init.php' );
    }

}

new ShopMagic();
