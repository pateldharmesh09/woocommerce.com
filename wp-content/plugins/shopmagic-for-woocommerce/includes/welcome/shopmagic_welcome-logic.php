<?php
/**
 * Welcome Logic
 *
 * Welcome code related logic.
 *
 * @since 	1.0.0
 * @package SHOPMAGIC
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Safe Welcome Page Redirect.
if ( ! function_exists( 'shopmagic_safe_welcome_redirect' ) ) {
	// Add to `admin_init`.
	add_action( 'admin_init', 'shopmagic_safe_welcome_redirect' );

	/**
	 * Safe Welcome Page Redirect.
	 *
	 * Safe welcome page redirect which happens only
	 * once and if the site is not a network or MU.
	 *
	 * @since 	1.0.0
	 */
	function shopmagic_safe_welcome_redirect() {
		// Bail if no activation redirect transient is present. (if ! true).
		if ( ! get_transient( '_welcome_redirect_shopmagic' ) ) {
			return;
		}

		// Delete the redirect transient.
		delete_transient( '_welcome_redirect_shopmagic' );

		// Bail if activating from network or bulk sites.
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
		}

		// Redirects to Welcome Page.
		wp_safe_redirect( add_query_arg(
			array(
				'page' => 'shopmagic_welcome_page'
				),
			admin_url( 'edit.php?post_type=shopmagic_automation' )
		) );
	}
}

// Welcome Page Sub menu.
if ( ! function_exists( 'shopmagic_welcome_page' ) ) {
	// Add to `admin_menu`.
	add_action('admin_menu', 'shopmagic_welcome_page');

	/**
	 * Welcome Page Sub menu.
	 *
	 * Add the welcome page inside ShopMagic menu.
	 *
	 * @since 	1.0.0
	 */
	function shopmagic_welcome_page() {
		// Add a global varaible to save the sub menu.
		global $shopmagic_sub_menu;

		// Sub menu itself.
		$shopmagic_sub_menu = add_submenu_page(
			'edit.php?post_type=shopmagic_automation', // The slug name for the parent menu (or the file name of a standard WordPress admin page).
			__( 'Start Here', 'SHOPMAGIC' ), // The text to be displayed in the title tags of the page when the menu is selected.
	    	__( 'Start Here', 'SHOPMAGIC' ), // The text to be used for the menu.
			'manage_options', // The capability required for this menu to be displayed to the user.
			'shopmagic_welcome_page', // The slug name to refer to this menu by (should be unique for this menu).
			'shopmagic_welcome_page_content' ); // The function to be called to output the content for this page.
	}
}

// Welcome Page View.
if ( ! function_exists( 'shopmagic_welcome_page_content' ) ) {
	/**
	 * Welcome Page View.
	 *
	 * Welcome page content i.e. HTML/CSS/PHP.
	 *
	 * @since 	1.0.0
	 */
	function shopmagic_welcome_page_content() {

		// Welcome Page.
		if (file_exists( SHOPMAGIC_BASE_DIR . '/includes/welcome/shopmagic_welcome-view.php') ) {
		   require_once( SHOPMAGIC_BASE_DIR . '/includes/welcome/shopmagic_welcome-view.php' );
		}
	}
}

// CSS for Welcome Page.
if ( ! function_exists( 'shopmagic_styles' ) ) {
	// Enqueue the styles.
	add_action( 'admin_enqueue_scripts', 'shopmagic_styles' );

	/**
	 * Enqueue Styles.
	 *
	 * @param int $hook Hook suffix for the current admin page.
	 * @since 1.0.0
	 */
	function shopmagic_styles( $hook ) {
		// Access the global varaible with saved sub menu.
	    global $shopmagic_sub_menu;

	    // Add style to the welcome page only.
	    if ( $hook != $shopmagic_sub_menu ) {
	      return;
	    }

	    // Welcome page styles.
	    wp_enqueue_style(
	      'shopmagic_style',
	      SHOPMAGIC_PLUGIN_URL . 'assets/css/welcome_style.css',
	      array(),
	      SHOPMAGIC_VERSION,
	      'all'
	    );
	}
}
