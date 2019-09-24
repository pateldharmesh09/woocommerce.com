<?php
/**
 * ShopMagic's Automation's post type.
 *
 * Stores user defined automations and has additional information
 * to properly handle user automations
 *
 * @package ShopMagic
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ShopMagic Automations Post Type class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
class ShopMagic_Automations_PostType {

    /**
     * Default constructor.
     * Initialize action's hook function
     *
     * @since   1.0.0
     */
    function __construct () {
        $this->add_actions();
    }

    /**
     * Adds action hooks.
     *
     * @since   1.0.0
     */
    private function add_actions() {
        add_action('init', array($this, 'setup_post_type'));
       // add_action('admin_menu', array($this, 'setup_admin_menu'));
    }

    /**
     * Initializes custom post type for Automations.
     *
     * @since   1.0.0
     */
    function setup_post_type() {
        $labels = array(
            'name'               => _x( 'ShopMagic Automations', 'post type general name', 'shopmagic' ),
            'singular_name'      => _x( 'Automation', 'post type singular name', 'shopmagic' ),
            'menu_name'          => _x( 'ShopMagic', 'admin menu', 'shopmagic' ),
            'name_admin_bar'     => _x( 'Automation', 'add new on admin bar', 'shopmagic' ),
            'add_new'            => _x( 'Add New', 'automation', 'shopmagic' ),
            'add_new_item'       => __( 'Add New Automation', 'shopmagic' ),
            'new_item'           => __( 'New Automation', 'shopmagic' ),
            'edit_item'          => __( 'Edit Automation', 'shopmagic' ),
            'view_item'          => __( 'View Automation', 'shopmagic' ),
            'all_items'          => __( 'All Automations', 'shopmagic' ),
            'search_items'       => __( 'Search Automations', 'shopmagic' ),
            'parent_item_colon'  => __( 'Parent Automations:', 'shopmagic' ),
            'not_found'          => __( 'No Automations found.', 'shopmagic' ),
            'not_found_in_trash' => __( 'No Automations found in Trash.', 'shopmagic' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'ShopMagic automation rules.', 'shopmagic' ),
            'public'             => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-admin-generic',
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'automation' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 56,
            'supports'           => array( 'title')
        );

        register_post_type( 'shopmagic_automation', $args );
    }

}
