<?php
/**
 * Plugin Name: Online water testing
 * Description: Form and personal cabinet
 * Version: 1.0.13
 * Author: Dmitry K.
 * Author URI: kutalo.com
 * Text Domain: thespashoppe
 */


// Define THESPA_PLUGIN_DIR.
if ( ! defined( 'THESPA_PLUGIN_DIR' ) ) {
	define( 'THESPA_PLUGIN_DIR', str_replace( '\\', '/', dirname( __FILE__ ) ) . '/' );
}

// Main THESPA_waterTesting Class.
if ( ! class_exists( 'THESPA_waterTesting' ) ) :
	include_once THESPA_PLUGIN_DIR . 'classes/class-waterTesting.php';
endif;

/**
 * Main instance of THESPA_waterTesting.
 *
 * Returns the main instance of THESPA_waterTesting to prevent the need to use globals.
 *
 * @return THESPA_waterTesting
 */
function THESPA_EP() {
	return THESPA_waterTesting::instance();
}

// Global for backwards compatibility.
$GLOBALS['THESPA_waterTesting'] = THESPA_EP();