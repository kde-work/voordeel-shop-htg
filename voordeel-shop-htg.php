<?php
/**
 * Plugin Name: HTG integration with voordeel-shop
 * Description: Fullfilment
 * Version: 1.0.1
 * Author: Dmitry K.
 * Author URI: kutalo.com
 * Text Domain: htgvoordeel
 */


// Define HTGVOO_PLUGIN_DIR.
if ( ! defined( 'HTGVOO_PLUGIN_DIR' ) ) {
	define( 'HTGVOO_PLUGIN_DIR', str_replace( '\\', '/', dirname( __FILE__ ) ) . '/' );
}

// Main HTGVOO_integration Class.
if ( ! class_exists( 'HTGVOO_integration' ) ) :
	include_once HTGVOO_PLUGIN_DIR . 'classes/class-HTG-integration.php';
endif;

/**
 * Main instance of HTGVOO_integration.
 *
 * Returns the main instance of HTGVOO_integration to prevent the need to use globals.
 *
 * @return HTGVOO_integration
 */
function HTGVOO_EP() {
	return HTGVOO_integration::instance();
}

// Global for backwards compatibility.
$GLOBALS['HTGVOO_integration'] = HTGVOO_EP();