<?php
/**
 * ShopMagic's template database loader.
 *
 * Extract template form fields in action and prepare it for template engine
 * template name consists form following parts:
 * automation_id%action_id%field_name
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class ShopMagic_Template_DBLoader implements Twig_LoaderInterface, Twig_ExistsLoaderInterface
{

    public function __construct()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {

        $parts = $this->analyzeName($name);
        $actions = get_post_meta(  $parts['automation_id'], '_actions', true );

        if (is_array($actions)) { // if meta exists and it is an array
            $source = $actions[ $parts['action_id']][$parts['field_name']];
        }
        else {
            throw new Twig_Error_Loader(sprintf('Template "%s" does not exist.', $name));
        }

        return $source;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        $parts = $this->analyzeName($name);
        $actions = get_post_meta(  $parts['automation_id'], '_actions', true );

        if (is_array($actions)) { // if meta exists and it is an array
            $source = $actions[ $parts['action_id']][$parts['field_name']];

            if ($source == null) {
                return false;
            }
        }
        else {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {

        $parts = $this->analyzeName($name);
        $automation_date = get_the_date( 'U', $parts['automation_id'] );

        if (false === $automation_date) {
            return false;
        }

        return $automation_date <= $time;
    }


    protected function analyzeName($name) {

        $name_arr = explode('%',$name);
        return array(
            'automation_id' => $name_arr[0],
            'action_id' => $name_arr[1],
            'field_name' => $name_arr[2]
        );

    }

}