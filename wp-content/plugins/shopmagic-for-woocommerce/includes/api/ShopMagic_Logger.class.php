<?php

/**
 * Wrapper for message logging functions
 */
class ShopMagic_Logger
{
    /**
     * Log a record
     *
     * @param string $severity severity of record (ERR, WARN, INFO, DEBUG)
     * @param string $source source of record (module name)
     * @param string $message  message short title
     * @param string $description detailed message description
     */
    static function log( $severity, $source, $message, $description='') {

        if (get_option('wc_settings_sm_store_messages',false)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'shopmagic_log_data';

            $wpdb->insert(
                $table_name,
                array(
                    'severity' => $severity,
                    'source' => $source,
                    'title' => $message,
                    'description' => $description
                )
            );
        }
    }
}

