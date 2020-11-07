<?php
/**
 * Media management Class.
 *
 * @package THESPA_waterTesting\Classes
 * @version 1.0.13
 */
defined( 'ABSPATH' ) || exit;

/**
 * THESPA_Media Class.
 */
class THESPA_Media {

	/**
	 * Register Media.
	 */
	public static function register() {
		wp_register_style( 'thespashoppe-water-testing-style', plugins_url( '/assets/css/thespashoppe-water-testing.css', THESPA_ASSETS_DIR ), null, THESPA_VERSION );
		wp_register_style( 'select2-style', plugins_url( '/assets/css/select2.min.css', THESPA_ASSETS_DIR ), null, THESPA_VERSION );
		wp_register_script( 'thespashoppe-water-testing', plugins_url( '/assets/js/thespashoppe-water-testing.js', THESPA_ASSETS_DIR ), null, THESPA_VERSION );
		wp_register_script( 'thespashoppe-template', plugins_url( '/assets/js/thespashoppe-template.js', THESPA_ASSETS_DIR ), null, THESPA_VERSION );
		wp_register_script( 'thespashoppe-actions', plugins_url( '/assets/js/thespashoppe-actions.js', THESPA_ASSETS_DIR ), null, THESPA_VERSION );
		wp_register_script( 'select2', plugins_url( '/assets/js/select2.min.js', THESPA_ASSETS_DIR ), null, THESPA_VERSION );
		wp_register_script( 'js-cookie', plugins_url( '/assets/js/js.cookie.min.js', THESPA_ASSETS_DIR ), null, THESPA_VERSION );
		wp_register_script( 'uuidv4', plugins_url( '/assets/js/uuidv4.min.js', THESPA_ASSETS_DIR ), null, THESPA_VERSION ); // id generator
		wp_register_script( 'date-format', plugins_url( '/assets/js/date-format.js', THESPA_ASSETS_DIR ), null, THESPA_VERSION );
		wp_register_script( 'thespashoppe-products', plugins_url( '/assets/js/thespashoppe-products.js', THESPA_ASSETS_DIR ), null, THESPA_VERSION );
		wp_register_script( 'thespashoppe-modal', plugins_url( '/assets/js/thespashoppe-modal.js', THESPA_ASSETS_DIR ), null, THESPA_VERSION );
	}

	/**
	 * Resources.
	 */
	public static function resources() {
		wp_enqueue_style('select2-style');
		wp_enqueue_style('thespashoppe-water-testing-style');
		wp_enqueue_script('js-cookie');
		wp_enqueue_script('uuidv4');
		wp_enqueue_script('select2');
		wp_enqueue_script('date-format');
		wp_enqueue_script('thespashoppe-template');
		wp_enqueue_script('thespashoppe-water-testing');
		wp_enqueue_script('thespashoppe-modal');
		wp_enqueue_script('thespashoppe-actions');
		wp_enqueue_script('thespashoppe-products');

		wp_localize_script( 'thespashoppe-water-testing', 'theSpaShoppeSettings',
			array(
				'url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'thespashoppe' )
			) );
	}
}
