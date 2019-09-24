<?php
/**
 * ShopMagic's AJAX Email template loader class.
 *
 * Load Email template via ajax
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ShopMagic's AJAX Email template loader class.
 *
 * @package ShopMagic
 * @since   1.0.0
 */
class ShopMagic_Email_Template_Loader {
    /**
     * Default constructor.
     *
     * @since   1.0.0
     */
    function __construct() {
        // Add ajax handler
        add_action("wp_ajax_sm_sea_load_email_template", array($this, "load_email_template"));
    }

    /**
     * Ajax handler for loading email template data
     */
    function load_email_template() {
        // check nonce
        $nonce = $_POST['paramProcessNonce'];
        if ( ! wp_verify_nonce( $nonce, 'shopmagic-ajax-process-nonce' ) )
            wp_die(); // we don't talk with terrorists

        $template_slug = sanitize_text_field($_POST['template_slug']);

        $template = file_get_contents(SHOPMAGIC_BASE_DIR.'/templates/emails/'.$template_slug.'.tmpl');

        error_log($template);

        echo preg_replace('/\/\*\*.*?\*\*\//','',$template,1);


        wp_die();
    }
}
?>