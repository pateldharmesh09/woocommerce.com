<?php
/**
 * Creates tables and populate data for plugin
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class ShopMagic_CreateTables
{
    /**
     * ShopMagic_CreateTables constructor. Registering activation hooks
     */
    function __construct() {
        register_activation_hook( SHOPMAGIC_BASE_FILE, array($this, 'install') );
        register_activation_hook( SHOPMAGIC_BASE_FILE, array($this, 'install_data') );
    }

    /**
     *  Creates tables
     */
    function install() {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


        $table_name_data = $wpdb->prefix . 'shopmagic_log_data';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name_data (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time timestamp,
            severity varchar(10) NOT NULL,
            source varchar(50) NOT NULL,
            title varchar(255) NOT NULL,
            description text NOT NULL,
            PRIMARY KEY  (id), 
            KEY sld_time (time),
            KEY sld_severity (severity),
            KEY sld_source (source)
        ) $charset_collate;";

        dbDelta( $sql );

        add_option( 'shopmagic_db_version', SHOPMAGIC_DB_VERSION );
    }

    /**
     * Populates initial data into table
     */
    function install_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'shopmagic_log_data';

        $wpdb->insert(
            $table_name,
            array(
                'severity' => 'INFO',
                'source' => 'ShopMagic',
                'title' => __('ShopMagic has been successfully activated','shopmagic'),
                'description' => __('Welcome to the world of awesomeness automations! Let The Magic begin!')
            )
        );
    }
}
