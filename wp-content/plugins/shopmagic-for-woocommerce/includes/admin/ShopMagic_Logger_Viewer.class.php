<?php

/**
 * Register table with event logs into the Shopmagic settings page
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ShopMagic_Logger_Table' ) ) {
    require_once(SHOPMAGIC_BASE_DIR.'/includes/admin/ShopMagic_Logger_Table.class.php');
}

/**
 * Class ShopMagic_Logger_Viewer
 * Displays log on the settigns page
 */
class ShopMagic_Logger_Viewer {
    /**
     * @var string module ID
     */
    var $id = 'shopmagic';

    /**
     * @var string section slug
     */
    var $logger_section = 'debug';

    /**
     * ShopMagic_Logger_Viewer constructor.
     */
    function __construct() {

        $this->add_actions();
    }

    /**
     *  bind to the hooks
     */
    function  add_actions() {

        add_filter('woocommerce_get_sections_' . $this->id, array($this, 'add_settings_section'), 1, 10);
        add_action('woocommerce_settings_' . $this->id, array( $this, 'show_log' ) );
        add_action('wp_ajax_shopmagic_load_detail_log_data', array($this, 'get_log_data'));
    }

    /**
     *  AJAX handler for log data
     */
    function get_log_data() {

        //error_log('get_log_data!');

        // check nonce
        $nonce = $_POST['paramProcessNonce'];
        if ( ! wp_verify_nonce( $nonce, 'shopmagic-debug-ajax-process-nonce' ) )
            wp_die(); // we don't talk with terrorists

        global $wpdb;

        $record_id = intval($_POST['id']);

        /** @noinspection SqlResolve */
        $sql = "SELECT id, time, severity, source, title, description FROM {$wpdb->prefix}shopmagic_log_data where id = ".$record_id;

        $data = $wpdb->get_row($sql);

        echo json_encode($data);
        wp_die();
    }

    /**
     * Filter hook listener. Adds section into the sections list
     *
     * @param array $sections associative array with sections
     * @return array associative array with sections
     */
    function add_settings_section($sections) {

        $sections[$this->logger_section] = __('Debug');
        return $sections;
    }

    /**
     * Action hook on display setting page
     *
     * Initialize and shows table on the setting page.
     */
    function show_log() {
        global $current_section;

        if ($this->logger_section == $current_section) {

            $eventTable = new ShopMagic_Logger_Table();
            $eventTable->prepare_items();
            $eventTable->views();
            $eventTable->display();
            ?><div id="modal-record-log-detail">
                <div class="error-icon"><span class="dashicons dashicons-warning"></span><div class="error-icon-tooltip">Network connection error</div></div><div class="spinner"></div>
                <div id="record-log-detail-content">
                    <div class="record-field record-id"><?php _e('ID', 'shopmagic'); ?>:&nbsp;<span class="value"></span></div>
                    <div class="record-field record-time"><?php _e('Time', 'shopmagic'); ?>:&nbsp;<span class="value"></span></div>
                    <div class="record-field record-severity"><?php _e('Severity', 'shopmagic'); ?>:&nbsp;<span class="value"></span></div>
                    <div class="record-field record-source"><?php _e('Source', 'shopmagic'); ?>:&nbsp;<span class="value"></span></div>
                    <div class="record-field record-description"><?php _e('Description', 'shopmagic'); ?>:&nbsp;<div class="value"></div></div>
                </div>
            </div><?php
        }
    }
}