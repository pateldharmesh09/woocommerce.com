<?php

/**
 * Table to show log records
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class ShopMagic_Logger_Table
 * Implements custom table realisation to show table log in the admin area
 */
class ShopMagic_Logger_Table extends WP_List_Table {

    /**
     * ShopMagic_Logger_Table constructor.
     */
    function __construct() {
        parent::__construct( array (
            'singular' => __( 'Event', 'shopmagic' ), //singular name of the listed records
            'plural'   => __( 'Events', 'shopmagic' ), //plural name of the listed records
            'ajax'     => false //should this table support ajax?

        ) );
    }

    /**
     * Text displayed when no customer data is available
     */
    public function no_items() {
        _e( 'No events avaliable.', 'shopmagic' );
    }

    /**
     * Retrieve events’s data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_events( $per_page = 20, $page_number = 1 ) {

        global $wpdb;


        /** @noinspection SqlResolve */
        $sql = "SELECT id, time, severity, source, title FROM {$wpdb->prefix}shopmagic_log_data".self::get_query_filters();

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
        else {
            $sql .= ' ORDER BY  id desc ';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        //error_log( print_r($result, true));
        return $result;
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}shopmagic_log_data".self::get_query_filters();

        return $wpdb->get_var( $sql );
    }

    /** returns filter string for SQL query, if applicable
     *
     *@return string with where clause
     */
    static function get_query_filters() {
        $filters  = array();

        if (!empty($_POST['severity-filter'])) {
            array_push($filters, "severity = '".sanitize_text_field($_POST['severity-filter'])."'");
        }

        if (!empty($_POST['source-filter'])) {
            array_push($filters, "source = '".sanitize_text_field($_POST['source-filter'])."'");
        }

        if (sizeof($filters) > 0) {
            return ' where '.implode(' and ', $filters);
        }

        return '';

    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'id'    => __( '#', 'shopmagic' ),
            'time' => __( 'Time', 'shopmagic' ),
            'severity'    => __( 'Severity', 'shopmagic' ),
            'source'    => __( 'Source', 'shopmagic' ),
            'title'    => __( 'Message', 'shopmagic' )
        );
        //error_log( print_r($columns, true));
        return $columns;
    }

    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default($item, $column_name) {
//        error_log( $item[$column_name]);
        return $item[$column_name];
    }
    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'time' => array( 'time', true ),
            'severity' => array( 'severity', false ),
            'source' => array( 'source', false )
        );

        return $sortable_columns;
    }

    /**
     *  Draw an extra cntrols
     *
     * filter controls at the top of the table
     *
     * @param string $which position of controls
     */
    function extra_tablenav( $which ) {
        global $wpdb;

        if ( $which == "top" ){

            // Query to get available severities:
            /** @noinspection SqlResolve */
            $sql = "SELECT severity FROM {$wpdb->prefix}shopmagic_log_data group by severity ORDER BY  severity asc";
            $severities = $wpdb->get_results( $sql, 'ARRAY_A');
            $severityString = '<option value="">'.__('Severity','shopmagic').'</option>';
            foreach ( $severities as  $severity ) {
                $selected = '';
                if (!empty($_POST['severity-filter']) && $_POST['severity-filter'] === $severity['severity']) {
                    $selected  = ' selected ';
                }
                $severityString .= '<option value="'.$severity['severity'].'"'.$selected.'>'.$severity['severity'].'</option>';
            }

            // Query to get available sources:
            /** @noinspection SqlResolve */
            $sql = "SELECT source FROM {$wpdb->prefix}shopmagic_log_data group by source ORDER BY  source asc";
            $sources = $wpdb->get_results( $sql, 'ARRAY_A');
            $sourceString = '<option value="">'.__('Source','shopmagic').'</option>';
            foreach ( $sources as  $source ) {
                $selected = '';
                if (!empty($_POST['source-filter']) && $_POST['source-filter'] === $source['source']) {
                    $selected  = ' selected ';
                }
                $sourceString .= '<option value="'.$source['source'].'"'.$selected.'>'.$source['source'].'</option>';
            }

            ?>
            <div class="alignleft actions bulkactions">
                <label for="severity-filter" class="screen-reader-text"><?php _e('Severity'); ?></label>
                <select name="severity-filter" id="severity-filter" >
                    <?php echo $severityString; ?>
                </select>
                <label for="source-filter" class="screen-reader-text"><?php _e('Source'); ?></label>
                <select name="source-filter" id="source-filter" >
                    <?php echo $sourceString; ?>
                </select>
                    <input type="submit" id="doaction" class="button action" value="Filter">
            </div>
            <?php
        }
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        // Column headers
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $per_page     = $this->get_items_per_page( 'events_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $this->items = self::get_events( $per_page, $current_page );
    }
    /**
     * @inheritdoc
     */
    public function single_row($item)
    {
//  todo: select appropriate colors
        $color = 'none';
//        error_log(print_r($item, true));
//        if ($item['severity'] === 'DEBUG') {
//           $color = '#00ffff';
//        }
        echo '<tr style="background-color: '.$color.'; cursor: pointer;" onclick="showDetailInfo('.$item['id'].')">';
        $this->single_row_columns( $item );
        echo '</tr>';

    }


}

