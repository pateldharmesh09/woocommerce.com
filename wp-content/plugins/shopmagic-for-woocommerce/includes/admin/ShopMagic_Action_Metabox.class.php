<?php
/**
 * ShopMagic's Action Meta Box.
 *
 * Prepare and show Action value of automation post type
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ShopMagic Action Meta Box class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
class ShopMagic_Action_Metabox {

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
        add_meta_box('shopmagic_action_metabox',__('Actions','shopmagic'),array($this,'draw_metabox'),'shopmagic_automation','normal');
    }
    /**
     * Adds action hooks.
     *
     * @since   1.0.0
     */
    private function add_actions() {
        add_action( 'save_post', array($this, 'save_metabox') );
        add_action('wp_ajax_shopmagic_load_action_params', array($this, 'load_action_params'));
    }

    /**
     * AJAX callback which shows action edit code
     *
     * @since   1.0.0
     */
    public function load_action_params()
    {

        // check nonce
        $nonce = $_POST['paramProcessNonce'];
        if ( ! wp_verify_nonce( $nonce, 'shopmagic-ajax-process-nonce' ) )
            wp_die(); // we don't talk with terrorists

        $action = sanitize_text_field($_POST['action_slug']);
        $action_id = intval($_POST['action_id']);
        $post_id = intval($_POST['post']);
        $editor_initialized = $_POST['editor_initialized'] === 'true';
        $action_class = $this->core->get_action($action);

        if ($action_class) { // if requested class exists

            // try to load data for this class
            $actions_data = get_post_meta($post_id, '_actions',true);

            $action_params = array();
            if (is_array($actions_data)) { // if data presents then load particular data for an action edit code
                if (count($actions_data) > $action_id) {
                    $action_params = $actions_data[$action_id];
                }
            }

            $action_params['editor_initialized'] =  $editor_initialized;

            // prefix code, to create array in $_POST variable when form submits
            $name_prefix = 'actions['.$action_id.']';

            ob_start();
            $action_class::show_parameters($post_id, $action_params, $name_prefix);
            $action_box = ob_get_contents();
            ob_end_clean();
            echo json_encode(array(
                    'action_box' => $action_box,
                    'data_domains' => $action_class::get_data_domains(),
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
        // initialize available actions
        $available_actions = $this->core->get_actions();
        wp_nonce_field( plugin_basename(SHOPMAGIC_BASE_FILE ), 'shopmagic_action_meta_box' );

        // a template of action page
        // if you modify this html markup, please, check assets/js/admin-handler.js to avoid break the logic

        ?>

        <div class="hidden-editor-container" style="display: none;">
            <?php //wp_editor('Default Content', 'tdmessagereply', array('textarea_name' =>'tdmessagereply_txtrea', 'tinymce'=>true)); ?>

        </div>

        <table class="form-table">
            <tr class="shopmagic-action-header">
                <td>
                    <button class="button button-primary button-large" onclick="addNewAction()" type="button"><i class="dashicons dashicons-plus"></i>&nbsp;New Action</button>
                </td>
            </tr>
        </table>
        <div class="postbox  action-form-table" id="action-area-stub">
            <button type="button" class="handlediv button-link" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Action</span><span class="toggle-indicator" aria-hidden="true"></span></button>
            <div class="error-icon"><span class="dashicons dashicons-warning"></span><div class="error-icon-tooltip">Network connection error</div></div><div class="spinner"></div>
            <h2 class="handle ui-sortable-handle"><span>Action #<span class="action_number">0</span>:&nbsp;
                            <select class="action_main_select" name="_action_stub" id="_action_stub" >
                                <option value="">-</option>
                                <?php
                                foreach ($available_actions as $display_action) {
                                    ?><option value="<?php echo $display_action::get_slug(); ?>"><?php echo $display_action::get_name(); ?></option><?php
                                }
                                ?>
                            </select>
                        </span>&nbsp;<span class="action_title" id="_action_title_stub"></span></h2>
            <div class="inside">
                <table  class="form-table" >
                    <tr>
                        <td>
                            <label for="action_title_stub" id="action_title_label_stub">Description:</label>
                            <input type="text"  class="wide-form-control" name="action_title_stub" id="action_title_stub">
                            
                            <div id="action-settings-area_occ">
                                <?php
                                
                                do_action( 'shopmagic_automation_action_settings', 'occ', array() ); // 'occ' like key

                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td id="action-config-area-stub">

                        </td>
                    </tr>
                    <tr>
                        <td class="shopmagic-action-footer">
                            <button class="button button-primary button-large" onclick="removeAction(this)" type="button"><i class="dashicons dashicons-trash"></i>&nbsp;Remove Action</button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <?php
        // For each stored action we create action area. This is duplicate for template code above.
        // Maybe better is make template file for this area and load it via some kind of templating script
        $actions = get_post_meta( $post->ID, '_actions', true );
        $nextActionIndex = 0;
        if (is_array($actions)) {
            foreach ($actions as $key => $action) {
                ?>
                <div class="postbox  action-form-table closed" id="action-area-<?php echo $key; ?>">
                    <button type="button" class="handlediv button-link" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Action</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                    <div class="error-icon"><span class="dashicons dashicons-warning"></span><div class="error-icon-tooltip">Network connection error</div></div><div class="spinner"></div>
                    <h2 class="hndle ui-sortable-handle"><span>Action #<span class="action_number"><?php echo $key+1; ?></span>:&nbsp;
                                    <select class="action_main_select" name="actions[<?php echo $key; ?>][_action]" id="_actions_<?php echo $key; ?>_action">
                                        <option value="">-</option>
                                        <?php
                                        foreach ($available_actions as $display_action) {
                                            ?>
                                            <option
                                            value="<?php echo $display_action::get_slug(); ?>" <?php selected( $display_action::get_slug(), $action['_action'] ); ?>><?php echo $display_action::get_name(); ?></option><?php
                                        }
                                        ?>
                                    </select>
                        </span>&nbsp;<div class="action_title" id="_action_title_<?php echo $key; ?>"><?php echo $action['_action_title']; ?></div></h2>
                    <div class="inside">

                        <table class="form-table" >
                            <tr>
                                <td>
                                    <label for="action_title_input_<?php echo $key; ?>">Description:</label><br/>
                                    <input type="text" class="wide-form-control half action_title_input" name="actions[<?php echo $key; ?>][_action_title]" id="action_title_input_<?php echo $key; ?>" value="<?php echo $action['_action_title']; ?>">
                                    <br /><label for="desc_tip_<?php echo $key; ?>"><i>Write a helpful description about this action for your reference</i></label>


                                    <div id="action-settings-area_<?php echo $key; ?>">
                                        <?php
                                        
                                        do_action( 'shopmagic_automation_action_settings', $key, $action );

                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td id="action-config-area-<?php echo $key; ?>">

                                </td>
                            </tr>
                            <tr>
                                <td class="shopmagic-action-footer">
                                    <button class="button button-primary button-large" onclick="removeAction(this)" type="button"><i class="dashicons dashicons-trash"></i>&nbsp;Remove Action</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
            }
            $nextActionIndex = count($actions);

        }
        // store global javascript variable to use in admin-side JS when we add new action
        ?>
        <script>
            var nextActionIndex = <?php echo  $nextActionIndex; ?>;
        </script>
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
        if( isset($_POST['post_type']) && $_POST['post_type'] == 'shopmagic_automation' && isset( $_POST['actions'] ) ) {

            // if auto saving skip saving our meta box data
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                return;

            //check nonce for security
            wp_verify_nonce(plugin_basename(SHOPMAGIC_BASE_FILE ), 'shopmagic_action_meta_box' );

            // save the meta box data as post meta using the post ID as a unique prefix

            $actions = $_POST['actions'];

            if (is_array($actions)) {
                // array with data from all actions to store in a post meta
                $meta = array();
                foreach($actions as $key => $action) {
                    // current action data
                    $data = array();
                    $action_stub = sanitize_text_field( $action['_action'] );
                    $action_title = sanitize_text_field( $action['_action_title'] );
                    $action_class = $this->core->get_action($action_stub);
                    if ($action_class) { // if action class exists

                        // call static method to store post data in an array
                        $action_class::save_parameters($post_id, $data, $action);
                        $data['_action'] = $action_stub;
                        $data['_action_title'] = $action_title;
                        
                        $data = apply_filters('shopmagic_settings_save', $data, $action, $key);

                        array_push($meta, $data );

                    }

                }
                // store actions data in a meta
                update_post_meta($post_id, '_actions',$meta);
            }
        }
    }
}
?>