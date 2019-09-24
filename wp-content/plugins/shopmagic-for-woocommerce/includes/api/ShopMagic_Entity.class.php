<?php
/**
 * ShopMagic's Entity Base class.
 *
 * Provides some basic methods and proprties for each ShopMagic Entity
 * like Events, Actions etc
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ShopMagic Entity Base class
 *
 * @package ShopMagic
 * @since   1.0.0
 */
abstract class ShopMagic_Entity {

    /**
     * Observable name of the entity
     *
     * @var string
     */
    static protected $name = 'Entity';

    /**
     * Unique slug of the entity
     *
     * @var string
     */
    static protected $slug = 'shopmagic_entity';

    /**
     * @var ShopMagic instance of main plugin class
     */
    protected $core;

    /**
     * @var integer called automation id
     */
    protected $automation_id;

    /**
     * @var string[] list of  used data domains (user info, order info e.t.c.)
     */
    static protected $data_domains = array();

    /**
     * Default constructor.
     *
     * @param $core ShopMagic instance of main plugin class
     * @param $automation_id integer called automation id
     *
     * @since   1.0.0
     */
    function __construct (ShopMagic $core, $automation_id) {
        $this->core = $core;
        $this->automation_id = $automation_id;
    }

    /**
     * Getter function for $name property
     *
     * Returns name of the entity
     *
     * @return string
     * @since   1.0.0
     */
    static function get_name() {
        return static::$name;
    }

    /**
     * Getter function for $slug property
     *
     * Returns slug of the entity
     *
     * @return string
     * @since   1.0.0
     */
    static function get_slug() {
        return static::$slug;
    }

    /**
     * Getter function for $automation_id property
     *
     * Returns id of current automation
     *
     * @return integer
     * @since   1.0.0
     */
    function get_automation_id() {
        return $this->automation_id;
    }

    /**
     * Getter function for $data_domains property
     *
     * Returns id of current domains of data
     *
     * @return string[]
     * @since   1.0.0
     */
    static function get_data_domains() {
        return static::$data_domains;
    }


}
