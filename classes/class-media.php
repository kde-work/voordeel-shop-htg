<?php
/**
 * Media management Class.
 *
 * @package HTGVOO_integration\Classes
 * @version 1.0.1
 */
defined( 'ABSPATH' ) || exit;

/**
 * HTGVOO_Media Class.
 */
class HTGVOO_Media {

	/**
	 * Register Media.
	 */
	public static function register() {
		wp_register_style( 'HTG-integration-style', plugins_url( '/assets/css/HTG-integration.css', HTGVOO_ASSETS_DIR ), null, HTGVOO_VERSION );
		wp_register_script( 'HTG-integration', plugins_url( '/assets/js/HTG-integration.js', HTGVOO_ASSETS_DIR ), null, HTGVOO_VERSION );
	}

	/**
	 * Resources.
	 */
	public static function resources() {
//		wp_enqueue_style('HTG-integration-style');
//		wp_enqueue_script('HTG-integration');

		wp_localize_script( 'HTG-integration', 'HTGintegration',
			array(
				'url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'HTG-integration' )
			) );
	}
}
