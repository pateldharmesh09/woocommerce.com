<?php
/**
 * ShopMagic's Plaeholders Meta Box.
 *
 * Reserve area to show Placeholders for a particular event
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ShopMagic Placeholders Meta Box class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
class ShopMagic_Placeholders_Metabox {

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

        $this->core = $core;
        $this->setup();
    }

    /**
     * Setup metabox.
     *
     * @since   1.0.0
     */
    function setup() {
        add_meta_box('shopmagic_placeholders_metabox',__('Placeholders','shopmagic'),array($this,'draw_metabox'),'shopmagic_automation','side');
    }
    /**
     * Display metabox in admin side
     *
     * @param WP_Post $post
     * @since   1.0.0
     */
    function draw_metabox($post) {
        ?>
        <div id="_shopmagic_placeholders_area"></div>
        <?php
    }
}
?>