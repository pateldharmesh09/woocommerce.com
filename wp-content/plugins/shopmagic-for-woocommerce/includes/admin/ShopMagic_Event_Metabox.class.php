<?php
/**
 * ShopMagic's Event Meta Box.
 *
 * Prepare and show Event value of automation post type
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
class ShopMagic_Event_Metabox {

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
        add_meta_box('shopmagic_event_metabox',__('Event','shopmagic'),array($this,'draw_metabox'),'shopmagic_automation','normal');

    }
    /**
     * Adds action hooks.
     *
     * @since   1.0.0
     */
    private function add_actions() {
        add_action( 'save_post', array($this, 'save_metabox') );
        add_action('wp_ajax_shopmagic_load_event_params', array($this, 'load_event_params'));
    }

    public function load_event_params() {

        // check nonce
        $nonce = $_POST['paramProcessNonce'];
        if ( ! wp_verify_nonce( $nonce, 'shopmagic-ajax-process-nonce' ) )
            wp_die(); // we don't talk with terrorists

        $event = sanitize_text_field($_POST['event_slug']);
        $post_id = intval($_POST['post']);
        $event_class = $this->core->get_event($event);
        if ($event_class) {

            ob_start();
            $event_class::show_parameters($post_id);
            $event_box = ob_get_contents();
            ob_end_clean();

            echo json_encode(array(
                'event_box' => $event_box,
                'description_box' => $event_class::show_description(),
                'placeholders_box' => $event_class::show_placeholders(),
                'data_domains' => $event_class::get_data_domains(),
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
        // initialize available events
        $events = $this->core->get_events();
        wp_nonce_field( plugin_basename(SHOPMAGIC_BASE_FILE ), 'shopmagic_event_meta_box' );
        $event_slug = get_post_meta( $post->ID, '_event', true );
        ?>
        <div id="_shopmagic_edit_page"></div>
        <table class="form-table">
            <tr>
                <td>
                    <select name="_event" id="_event" title="<?php _e('Event', 'shopmagic');?>">
                        <optgroup label="">
                            <option value="" <?php selected( "", $event_slug ); ?>>-</option>

        <?php
        // order all events by group
        /**
         * compares events by groups object
         * @param ShopMagic_Event $a
         * @param ShopMagic_Event $b
         * @return int compare result
         */
        function cmp($a, $b)
        {
            return strcmp($a::get_group(), $b::get_group());
        }
        usort($events, "cmp");

        $prevGroup = '';
        foreach ($events as $event) {
            if ($prevGroup != $event::get_group()) { // group was changed
                ?></optgroup><optgroup label="<?php echo $this->core->get_event_group_name($event::get_group());?> "><?php
                $prevGroup = $event::get_group();
            }
            ?><option value="<?php echo $event::get_slug(); ?>" <?php selected( $event::get_slug(), $event_slug ); ?>><?php echo $event::get_name(); ?></option><?php
        }
        ?>
                        </optgroup>
                    </select><div class="error-icon"><span class="dashicons dashicons-warning"></span><div class="error-icon-tooltip">Network connection error</div></div><div class="spinner"></div>
                    <div id="event-desc-area">
                        <h2>Event description :</h2>
                        <div>
                             <i class="content"></i>
                        </div>
                        <div style="color: darkorange; margin-top:10px">
                              <i><b>Note: Automations not working?<b> Make sure that you are placing an order using the front-end of your store when testing automations, not by clicking the 'Add Order' button in <span style="white-space:nowrap">WooCommerce-->Orders</span>. If you're still not receiving emails, <a href="https://shopmagic.app/knowledgebase/my-automation-emails-are-not-being-sent-out/" target="blank">checkout our guide</a>.</i>

                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td id="event-config-area">
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
        if(isset($_POST['post_type']) && $_POST['post_type'] == 'shopmagic_automation' && isset( $_POST['_event'] ) ) {

            // if auto saving skip saving our meta box data
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                return;

            $event = sanitize_text_field( $_POST['_event']);

            //check nonce for security
            wp_verify_nonce(plugin_basename(SHOPMAGIC_BASE_FILE ), 'shopmagic_event_meta_box' );

            // save the meta box data as post meta using the post ID as a unique prefix
            update_post_meta( $post_id, '_event',  $event );

            // process event-specific save
            $event_class = $this->core->get_event($event);
            if ($event_class) {
                $event_class::save_parameters($post_id);
            }
        }
    }
}
?>
