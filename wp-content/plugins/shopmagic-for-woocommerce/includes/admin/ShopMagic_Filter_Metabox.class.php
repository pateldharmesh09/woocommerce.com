<?php
/**
 * ShopMagic's Filter Meta Box.
 *
 * Prepare and show Filter value of automation post type
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ShopMagic Filter Meta Box class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
class ShopMagic_Filter_Metabox {

    /**
     * Instance of core ShopMagic files
     *
     * @var string
     */
    protected $core;
    /**
     * Default constructor.
     *
     * @param ShopMagic $core instance of core class
     * @since   1.0.0
     */
    function __construct ( $core) {
        $this->add_actions();
        $this->core = $core;
        $this->setup();
    }

    /**
     * Setup metabox.
     *
     * @since   1.0.0
     */
    function setup() {
        add_meta_box('shopmagic_filter_metabox',__('Filter','shopmagic'),array($this,'draw_metabox'),'shopmagic_automation','normal');

    }

    /**
     * Adds action hooks.
     *
     * @since   1.0.0
     */
    private function add_actions() {
        add_action( 'save_post', array($this, 'save_metabox') );
        add_action('wp_ajax_shopmagic_load_filter_params', array($this, 'load_filter_params'));
    }

    public function load_filter_params() {

        // check nonce
        $nonce = $_POST['paramProcessNonce'];
        if ( ! wp_verify_nonce( $nonce, 'shopmagic-ajax-process-nonce' ) )
            wp_die(); // we don't talk with terrorists

        $filter = sanitize_text_field($_POST['filter_slug']);
        $post_id = intval($_POST['post']);
        $filter_class = $this->core->get_filter($filter);
        if ($filter_class) {

            ob_start();
            $filter_class::show_parameters($post_id);
            $filter_box = ob_get_contents();            
            ob_end_clean();

            echo json_encode(array(
                'filter_box' => $filter_box,
                'description_box' => $filter_class::show_description(),
                )
            );

        }
        wp_die();

    }

    /**
     * Display metabox in admin side
     *
     * @param WP_Post $post
     * @since   1.0.0
     */
    function draw_metabox($post) {
        // initialize available filters
        $filters = $this->core->get_filters();
        wp_nonce_field( plugin_basename(SHOPMAGIC_BASE_FILE ), 'shopmagic_filter_meta_box' );
        $filter_slug = get_post_meta( $post->ID, '_filter', true );
        ?>
        
        <table class="form-table">
            <tr>
                <td>
                    <select name="_filter" id="_filter" title="<?php _e('Filter', 'shopmagic');?>">
                        <option value="" <?php selected( "", $filter_slug ); ?>>-</option>
                        
                        <?php foreach ( $filters as $filter ) { ?>
                            <option value="<?php echo $filter::get_slug(); ?>" <?php selected( $filter::get_slug(), $filter_slug ); ?>><?php echo $filter::get_name(); ?></option>
                        <?php  } ?>
                    </select>
                    <div class="error-icon"><span class="dashicons dashicons-warning"></span>
                        <div class="error-icon-tooltip">Network connection error</div>
                    </div>
                    <div class="spinner"></div>
                    <div id="filter-desc-area">
                        <div>
                             <i class="content"></i>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td id="filter-config-area">
                </td>
            </tr>
        </table>
        <?php

    }

    /**
     * Post save processor
     *
     * @param string $post_id
     * @since   1.0.0
     */
    function save_metabox( $post_id) {

        // process form data if $_POST is set
        if(isset($_POST['post_type']) && $_POST['post_type'] == 'shopmagic_automation' && isset( $_POST['_filter'] ) ) {

            // if auto saving skip saving our meta box data
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                return;

            $filter = sanitize_text_field( $_POST['_filter']);

            //check nonce for security
            wp_verify_nonce(plugin_basename(SHOPMAGIC_BASE_FILE ), 'shopmagic_filter_meta_box' );

            // save the meta box data as post meta using the post ID as a unique prefix
            update_post_meta( $post_id, '_filter',  $filter );

            // process filter-specific save
            $filter_class = $this->core->get_filter($filter);
            if ($filter_class) {
                $filter_class::save_parameters($post_id);
            }
        }

    }

}