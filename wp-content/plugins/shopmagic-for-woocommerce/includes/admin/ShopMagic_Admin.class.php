<?php
/**
 * ShopMagic's Admin area handler.
 *
 * Prepare admin area classes and variables
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ShopMagic Event Meta Box class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
class ShopMagic_Admin {

    /**
     * Default constructor.
     *
     * @param ShopMagic $core instance of core class
     * @since   1.0.0
     */
    function __construct ( $core) {
        $this->includes();
        $this->add_actions();

        $this->setup_admin_menu();


        new ShopMagic_Event_Metabox($core);
        new ShopMagic_Filter_Metabox($core);
        new ShopMagic_Action_Metabox($core);
        new ShopMagic_Placeholders_Metabox($core);
        new ShopMagic_Logger_Viewer();
        new ShopMagic_Email_Template_Loader();

    }

    /**
     * Include wrapper.
     *
     * Includes core classes
     *
     *
     * @since   1.0.0
     */
    function includes() {

        // admin classes
        require_once(SHOPMAGIC_BASE_DIR.'/includes/admin/ShopMagic_Event_Metabox.class.php');
        require_once(SHOPMAGIC_BASE_DIR.'/includes/admin/ShopMagic_Filter_Metabox.class.php');
        require_once(SHOPMAGIC_BASE_DIR.'/includes/admin/ShopMagic_Action_Metabox.class.php');
        require_once(SHOPMAGIC_BASE_DIR.'/includes/admin/ShopMagic_Placeholders_Metabox.class.php');
        require_once(SHOPMAGIC_BASE_DIR.'/includes/admin/ShopMagic_WC_Settings_Tab.class.php');
        require_once(SHOPMAGIC_BASE_DIR.'/includes/admin/ShopMagic_Logger_Viewer.class.php');
        require_once(SHOPMAGIC_BASE_DIR.'/includes/admin/ShopMagic_Email_Template_Loader.class.php');

    }
    /**
     * Adds action hooks.
     *
     * @since   1.0.0
     */
    private function add_actions() {

       // add_action('admin_menu', array($this, 'setup_admin_menu'));
        add_action( 'admin_enqueue_scripts', array($this,'admin_scripts' ));
    }

    /**
     * Includes admin scripts in admin area
     *
     * @param string $hook hook, describes page
     */
    public function admin_scripts($hook) {

        if ( 'woocommerce_page_wc-settings' == $hook) {
            wp_enqueue_style( 'shopmagic-admin', SHOPMAGIC_PLUGIN_URL . 'assets/css/admin-style.css',array(),'206082602');
            wp_enqueue_script('shopmagic-debug-log-handler', SHOPMAGIC_PLUGIN_URL . '/assets/js/sm-debug-handler.js', array('jquery', 'jquery-ui-dialog'));

            wp_localize_script('shopmagic-debug-log-handler', 'ShopMagic', array(
                    // URL to wp-admin/admin-ajax.php to process the request
                    'ajaxurl' => admin_url('admin-ajax.php'),

                    // generate a nonce with a unique ID "shopmagic-debug-ajax-process-nonce"
                    // so that you can check it later when an AJAX request is sent
                    'paramProcessNonce' => wp_create_nonce('shopmagic-debug-ajax-process-nonce'),
                )
            );

        }

        //KRM - Changed to use get_current_screen function to check that on a shopmagic admin page
        // if ( 'post.php' != $hook && 'post-new.php' != $hook) {
        //     return;
        // }

        $current_screen = get_current_screen();
        if ( $current_screen->post_type != 'shopmagic_automation') {
            return;
        }

        wp_enqueue_script( 'shopmagic-admin-handler', SHOPMAGIC_PLUGIN_URL . 'assets/js/admin-handler.js', array('jquery', 'wp-util'));

        wp_localize_script( 'shopmagic-admin-handler', 'ShopMagic', array(
                // URL to wp-admin/admin-ajax.php to process the request
                'ajaxurl'          => admin_url( 'admin-ajax.php' ),

                // generate a nonce with a unique ID "shopmagic-ajax-process-nonce"
                // so that you can check it later when an AJAX request is sent
                'paramProcessNonce' => wp_create_nonce( 'shopmagic-ajax-process-nonce' ),
            )
        );

        /** @var $woocommerce WooCommerce*/
        global $woocommerce;
        wp_enqueue_style( 'shopmagic-admin', SHOPMAGIC_PLUGIN_URL . 'assets/css/admin-style.css',array(),'206082602');
        wp_enqueue_style( 'woocommerce_admin_styles-css', $woocommerce->plugin_url() . '/assets/css/admin.css',array(),'206082602');

        // include woocommerece scripts for product select box
        wp_enqueue_script( 'backbone-modal', $woocommerce->plugin_url() . '/assets/js/admin/backbone-modal.js', array('backbone'),'206082602');
        wp_enqueue_script( 'wc-blockUI', $woocommerce->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI.min.js',array(),'206082602');
        wp_enqueue_script( 'wc-tipTip', $woocommerce->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js',array(),'206082602');
        wp_enqueue_script( 'woocommerce_admin', $woocommerce->plugin_url() . '/assets/js/admin/woocommerce_admin.js',array('jquery'),'206082602');
        wp_enqueue_script( 'select2', $woocommerce->plugin_url() . '/assets/js/select2/select2.min.js', array(),'206082602');
        wp_enqueue_script( 'accounting', $woocommerce->plugin_url() . '/assets/js/accounting/accounting.min.js', array(),'206082602');
        wp_enqueue_script( 'round', $woocommerce->plugin_url() . '/assets/js/round/round.min.js', array(),'206082602');
        wp_enqueue_script( 'stupidtable', $woocommerce->plugin_url() . '/assets/js/stupidtable/stupidtable.min.js', array(),'206082602');
        wp_enqueue_script( 'meta-boxes',$woocommerce->plugin_url() . '/assets/js/admin/meta-boxes.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round', 'wc-enhanced-select', 'plupload-all', 'stupidtable', 'jquery-tiptip' ),'206082602');

    }

    /**
     * Initializes admin page menu.
     *
     * Adds submenu in WooCommerce menu
     *
     * @since   1.0.0
     */
    function setup_admin_menu() {
        //Settings Menu Item
        add_submenu_page( 'edit.php?post_type=shopmagic_automation', __('Settings','shopmagic'), __('Settings','shopmagic'), 'manage_options', 'admin.php?page=wc-settings&tab=shopmagic', null);
    }
}
?>
