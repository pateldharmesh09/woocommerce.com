<?php
/**
 * Welcome Page Init
 *
 * Welcome page initializer.
 *
 * @since 	1.0.0
 * @package SHOPMAGIC
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}




/**
 * Welcome Logic.
 *
 * @since 1.0.0
 */
if ( file_exists( SHOPMAGIC_BASE_DIR . '/includes/welcome/shopmagic_welcome-logic.php' ) ) {
    require_once( SHOPMAGIC_BASE_DIR . '/includes/welcome/shopmagic_welcome-logic.php' );
}
