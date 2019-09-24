<?php
/**
 * ShopMagic's template engine based on twig.
 *
 * Process templates, defined in users' field in actions.
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ShopMagic_Template_DBLoader' ) ) {
    require_once(SHOPMAGIC_BASE_DIR.'/includes/libraries/ShopMagic_Template_DBLoader.class.php');
}

class ShopMagic_Template {

    /**
     * Filesystem Loader class
     * @var Twig_Loader_Filesystem
     */
    var $fs_loader;

    /**
     * Database Loader class
     * @var ShopMagic_Template_DBLoader
     */
    var $db_loader;

    /**
     * Combined Loader class
     * @var Twig_Loader_Chain
     */
    var $loader;

    /**
     * Twig class
     * @var Twig_Environment
     */
    var $twig;

    function __construct() {

        // This is how to call the template engine:
        // apply_filters('shopmagic_render_email_template', $template, $data_array );
        add_filter('shopmagic_render_email_template', array( &$this, 'render_email' ), 10, 2 );

        // This path should always be the last
        $base_path = trailingslashit( SHOPMAGIC_PLUGIN_DIR ).'templates';

        $this->fs_loader = new Twig_Loader_Filesystem( $base_path );
        $this->db_loader = new ShopMagic_Template_DBLoader( );

        // Tap in here to add additional template paths
        $additional_paths = apply_filters('shopmagic_template_paths', array(
            trailingslashit(  get_stylesheet_directory() ).'shopmagic',
        ));

        foreach($additional_paths as $path) {

            // If the directory exists
            if(is_dir($path)) {
                // Tell Twig to use it first
                $this->fs_loader->prependPath($path);
            }
        }

        // You can force debug mode by adding `add_filter( 'shopmagic_twig_debug' '__return_true' );`
        $debug = apply_filters( 'shopmagic_twig_debug', current_user_can( 'manage_options' ) && isset( $_GET['debug'] ) );

        $this->loader = new Twig_Loader_Chain(array($this->db_loader, $this->fs_loader));


        $this->twig = new Twig_Environment($this->loader, array(
            'debug' => !empty($debug),
            'auto_reload' => true
        ));

        if(!empty($debug)) {
            $this->twig->addExtension(new Twig_Extension_Debug());
        }

    }

    /**
     * Render template from email action
     *
     * @param string $template Template slug based on value passed by `shopmagic_render_email_template` filter
     * @param array $data
     * @return string HTML
     */
    function render_email( $template = NULL, $data = array() ) {

        // Generate a cache key based on the result. Only get the first 43 characters because of the transient key length limit.
        $cache_key = substr( 'sm_'.sha1( $template.serialize( $data ) ) , 0, 43 );
        $output = get_transient( $cache_key );

        // If there's no cached result or caching is disabled
        if( empty( $output ) || is_wp_error( $output ) || ( isset($_GET['cache']) && current_user_can( 'manage_options' ) ) ) {

            $output = $this->twig->render($template, $data);

            /**
             * Modify the number of seconds to cache the request for.
             *
             * Default: cache the request for one hour, since we're dealing with changing conditions
             *
             * @var int
             */
            $cache_time = apply_filters( 'shopmagic_cache_time', 0 );

            // The nice thing is that the cache is invalidated when the forecast results change, so there's no need for the cache time to be exact.
            set_transient( $cache_key, $output, ( $cache_time * 2 )  );
        }

        /**
         * Modify the HTML output of the email message
         * @param string $output HTML of the email message
         * @param string $template Template slug based on value passed by `shopmagic_render_template` filter
         * @param array $data Template data array
         */
        $output = apply_filters( 'shopmagic_email_template_result', $output, $template, $data );

        return $output;

    }
}

new ShopMagic_Template();
