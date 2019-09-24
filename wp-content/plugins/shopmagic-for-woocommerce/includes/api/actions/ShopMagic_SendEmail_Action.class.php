<?php
/**
 * ShopMagic's Send Email Action class.
 *
 * Sends and email message
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ShopMagic_Action' ) ) {
    require_once(SHOPMAGIC_BASE_DIR.'/includes/api/ShopMagic_Action.class.php');
}

/**
 * ShopMagic Send Email Action class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
class ShopMagic_SendEmail_Action extends ShopMagic_Action {


    static protected $name = "Send Email";
    static protected $slug = "shopmagic_sendemail_action";
    static protected $data_domains = array('user');

    /**
     * Default constructor.
     *
     * @param $core ShopMagic instance of main plugin class
     * @param $automation_id integer called automation id
     * @param $data array current action settings to store in instance
     *
     * @since   1.0.0
     */
    function __construct(ShopMagic $core, $automation_id, $data) {
        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct($core, $automation_id, $data);

        // Setup class variables

    }

    /**
     * Execute an action
     *
     * Sends email with particular parameters
     *
     * @param $event ShopMagic_Event an event which is fired this action
     * @since   1.0.0
     */
    function execute($event, $key)
    {

        $subject = $event->process_string($this->data['subject_value']);
        $to = $event->process_string($this->data['to_value']);

        //$message = apply_filters('shopmagic_render_email_template',$this->automation_id.'%'.$key.'%message_text', $event->get_placeholders_values());
        $message = wpautop($event->process_string($this->data['message_text']));

        error_log($message);

        add_filter( 'wp_mail_content_type',array($this,'set_mail_content_type') );

        //Set From and To to match WooCommerce Settings
        add_filter( 'wp_mail_from', function( $email ) { 
            return get_option( 'woocommerce_email_from_address' ); 
            });
        add_filter( 'wp_mail_from_name', function( $name ) { 
            return get_option( 'woocommerce_email_from_name' );
            });

        //Build Message
        wp_mail( $to, $subject, $message );

        remove_filter('wp_mail_content_type',array($this,'set_mail_content_type'));

    }

    /**
     * Action callback to set more complex context type for sending email
     *
     * @return string content type for email
     */
    function set_mail_content_type(){
        return "text/html";
    }

    /**
     * Show parameters window in an admin side widget
     *
     * @param $automation_id integer called automation id
     * @param $data array current action settings to set default values
     * @param $name_prefix string prefix for form control name attributes
     *
     * @since   1.0.0
     */
    static function show_parameters($automation_id, $data, $name_prefix)
    {
        $editor_initialized = false;
        if ($data['editor_initialized'] === true) {
            $editor_initialized = true;
        }

        if ($data['_action'] != self::$slug) { // if data are from other class type then cleanup data array
            $data = array( // default values
                'to_value' => '{{user_email}}',
            );
        }

        $editor_id = str_replace('[','_',$name_prefix.'[message_text]');
        $editor_id = str_replace(']','_',$editor_id);

        wp_print_styles('media-views');

        //get list of available templates
        $dir_list = scandir(SHOPMAGIC_BASE_DIR.'/templates/emails/');
        $list_of_templates = array();
        foreach($dir_list as $file) {
            if (strpos($file, '.tmpl') !== false) {
                // extract template title from file
                $fh = fopen(SHOPMAGIC_BASE_DIR.'/templates/emails/'.$file,'r');
                $line = fgets($fh);
                fclose($fh);
                preg_match('/\/\*\*(.*?)\*\*\//',$line, $res);
                array_push($list_of_templates, '<option value="'.str_replace('.tmpl','',$file).'">'.trim($res[1]).'</option>');
            }
        }

        ?>
        <script>
            window.SM_EditorInitialized = true;
        </script>
        <div class="form-group">
            <label for="subject_value">Subject:</label><br/>
            <input type="text" class="wide-form-control" name="<?php echo $name_prefix;?>[subject_value]" id="subject_value" value="<?php echo $data['subject_value']; ?>"/>
        </div>

        <div class="form-group">
            <label for="to_value">To:</label><br/>
            <input type="text" class="wide-form-control" name="<?php echo $name_prefix;?>[to_value]" id="to_value" value="<?php echo $data['to_value']; ?>"/>
        </div>

        <div class="form-group email_templates_<?php echo $editor_id; ?> email_templates" >
            <label for="to_value">Insert predefined block: </label><br/>
            <select type="text" class="wide-form-control" id="predefined_block_<?php echo $editor_id; ?>">
                <?php echo implode('',$list_of_templates); ?>
            </select>
            <div class="et_wrapper">
                <div class="button button-default button-large" onclick="loadEmailTemplate('<?php echo $editor_id; ?>')"><i class="dashicons dashicons-plus"  style="line-height: 30px;"></i> Insert block</div>
                <div class="error-icon"><span class="dashicons dashicons-warning"></span><div class="error-icon-tooltip">Network connection error</div></div>
                <div class="spinner"></div>
            </div>
        </div>

         <div class="form-group">
            <label for="message_text">Message:</label><br/>
            <?php

            wp_enqueue_script('media-upload');
            wp_enqueue_script('wp-embed');
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script('imgareaselect');

            $buttons = '';
            if ( ! class_exists( '_WP_Editors', false ) ) {
                require_once( ABSPATH . WPINC . '/class-wp-editor.php' );
            }
            add_thickbox();

            $set = _WP_Editors::parse_settings($editor_id, array());
            $set['media_buttons'] = true;
            $default_editor = 'html';
            $editor_class = ' class="' . trim( esc_attr( $set['editor_class'] ) . ' wp-editor-area' ) . '"';
            $tabindex = $set['tabindex'] ? ' tabindex="' . (int) $set['tabindex'] . '"' : '';

            if ( ! empty( $set['editor_height'] ) ) {
                $height = ' style="height: ' . (int) $set['editor_height'] . 'px"';
            } else {
                $height = ' rows="' . (int) $set['textarea_rows'] . '"';
            }

            if ($editor_initialized) {
                //$editor_id_attr = esc_attr( $editor_id );
                $editor_id_attr = $editor_id;

                $autocomplete = ' autocomplete="off"';
                $default_editor = $set['default_editor'] ? $set['default_editor'] : wp_default_editor();
                // 'html' is used for the "Text" editor tab.
                //if ( 'html' !== $default_editor ) {
                    $default_editor = 'tinymce';
               // }

                $buttons .= '<button type="button" id="' . $editor_id_attr . '-tmce" class="wp-switch-editor switch-tmce"' .
                    ' data-wp-editor-id="' . $editor_id_attr . '">' . __('Visual') . "</button>\n";
                $buttons .= '<button type="button" id="' . $editor_id_attr . '-html" class="wp-switch-editor switch-html"' .
                    ' data-wp-editor-id="' . $editor_id_attr . '">' . _x( 'Text', 'Name for the Text editor tab (formerly HTML)' ) . "</button>\n";


                $switch_class = 'html' === $default_editor ? 'html-active' : 'tmce-active';
                $wrap_class = 'wp-core-ui wp-editor-wrap ' . $switch_class;

                if ( $set['_content_editor_dfw'] ) {
                    $wrap_class .= ' has-dfw';
                }

                echo '<div id="wp-' . $editor_id_attr . '-wrap" class="' . $wrap_class . '">';

                if ( ! empty( $buttons ) || $set['media_buttons'] ) {
                    echo '<div id="wp-' . $editor_id_attr . '-editor-tools" class="wp-editor-tools hide-if-no-js">';


                    if ( $set['media_buttons'] ) {
                        //self::$has_medialib = true;

                        if ( ! function_exists( 'media_buttons' ) )
                            include( ABSPATH . 'wp-admin/includes/media.php' );

                        echo '<div id="wp-' . $editor_id_attr . '-media-buttons" class="wp-media-buttons">';

                        /**
                         * Fires after the default media button(s) are displayed.
                         *
                         * @since 2.5.0
                         *
                         * @param string $editor_id Unique editor identifier, e.g. 'content'.
                         */
                        do_action( 'media_buttons', $editor_id );
                        echo "</div>\n";
                    }

                    echo '<div class="wp-editor-tabs">' . $buttons . "</div>\n";
                    echo "</div>\n";
                }

                if ( 'content' === $editor_id && ! empty( $GLOBALS['current_screen'] ) && $GLOBALS['current_screen']->base === 'post' ) {
                    $toolbar_id = 'ed_toolbar';
                } else {
                    $toolbar_id = 'qt_' . $editor_id_attr . '_toolbar';
                }

                $quicktags_toolbar = '<div id="' . $toolbar_id . '" class="quicktags-toolbar"></div>';

                $the_editor = apply_filters( 'the_editor', '<div id="wp-' . $editor_id_attr . '-editor-container" class="wp-editor-container">' .
                    $quicktags_toolbar .
                    '<textarea' . $editor_class . $height . $tabindex . $autocomplete . ' cols="40" name="' . $name_prefix . '[message_text]" ' .
                    'id="' . $editor_id_attr . '">%s</textarea></div>' );



                printf( $the_editor, $data['message_text'] );
                ?>
                <script>
                    tinymce.EditorManager.execCommand('mceAddEditor', false, '<?php echo $editor_id; ?>');
                    quicktags('<?php echo $editor_id; ?>');
                </script>
                <?php
                echo "\n</div>\n\n";
            }
            else {
                wp_editor($data['message_text'], $editor_id, array('textarea_name' => $name_prefix . '[message_text]', 'media_buttons' => true));
                \_WP_Editors::enqueue_scripts();
                \_WP_Editors::editor_js();
                echo '<script type="text/javascript" src="' . admin_url() . 'js/editor.js"></script>';

                wp_print_media_templates();
            }
            ?>
            <br /><label for="post editor tip"><i>Copy and paste Placeholder codes (including double brackets) on the right into the message body to personalize<i></label>

        </div>

        <?php

    }

    /**
     * Save parameters from POST request, called from an admin side widget
     *
     * in this method we should analyse $post array and store data accordingly in $data array
     *
     * @param $automation_id integer called automation id
     * @param $data array pointer to an array which is will be stored in meta for an automation
     * @param $post array part from $_POST array, which is belongs for a current action
     *
     * @since   1.0.0
     */
    static function save_parameters($automation_id, &$data, $post)
    {
        $data['subject_value'] = sanitize_text_field($post["subject_value"]);
        $data['to_value'] = sanitize_text_field($post["to_value"]);
        $data['message_text'] = $post["message_text"];
    }

}
