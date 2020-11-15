<?php
/**
 * HTGVOO_integration Class.
 *
 * @package HTGVOO_integration\Classes
 * @version 1.0.1
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class HTGVOO_integration
 */
class HTGVOO_integration {

	/**
	 * HTGVOO_integration version.
	 *
	 * @var string
	 */
	public $version = '1.0.1';

	/**
	 * The single instance of the class.
	 *
	 * @var HTGVOO_integration
	 */
	protected static $_instance = null;

	/**
	 * HTGVOO_integration Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
		$this->init_shortcodes();

		do_action( 'HTGVOO_integration_loaded' );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
//		if ( $this->is_request( 'admin' ) ) {
//			include_once HTGVOO_PLUGIN_DIR . 'classes/admin/class-htgvoo-admin.php';
//		}

		/**
		 * Core classes.
		 */
		include_once HTGVOO_PLUGIN_DIR . 'classes/class-shortCodes.php';
		include_once HTGVOO_PLUGIN_DIR . 'classes/class-media.php';
		include_once HTGVOO_PLUGIN_DIR . 'classes/class-data.php';
		include_once HTGVOO_PLUGIN_DIR . 'classes/class-requests.php';
		include_once HTGVOO_PLUGIN_DIR . 'classes/class-mail.php';
		include_once HTGVOO_PLUGIN_DIR . 'classes/class-report.php';
	}

	/**
	 * Main HTGVOO_integration Instance.
	 *
	 * Ensures only one instance of HTGVOO_integration is loaded or can be loaded.
	 *
	 * @static
	 * @see HTGVOO_EP()
	 * @return HTGVOO_integration - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * On WordPress Init.
	 */
	public function on_init() {
		$this->init_taxonomies();
		$this->init_post_types();
	}

	/**
	 * Init shortcodes.
	 */
	public function init_shortcodes() {
		add_shortcode( 'waterTesting', array( 'HTGVOO_shortCodes', 'waterTesting' ) );
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
//		add_action( 'wp_ajax_HTGVOO_save', array( 'HTGVOO_Requests', 'save' ), 10 );

		add_action( 'wp_enqueue_scripts', array( 'HTGVOO_media', 'register' ), 10 );

//		add_action( 'admin_enqueue_scripts', array( 'HTGVOO_Media', 'register' ), 10 );
//		add_action( 'admin_enqueue_scripts', array( 'HTGVOO_Admin_Output', 'register' ), 20 );
		add_action( 'init', array( $this, 'on_init' ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Define HTGVOO_integration Constants.
	 */
	private function define_constants() {
		$this->define( 'HTGVOO_ASSETS_DIR', HTGVOO_PLUGIN_DIR . 'assets' );
		$this->define( 'HTGVOO_ASSETS_URL', get_option( 'siteurl' ) . str_replace( str_replace( '\\', '/', ABSPATH ), '/', HTGVOO_ASSETS_DIR ) );
		$this->define( 'HTGVOO_VERSION', $this->version );
	}

	/**
	 * Add the HTGVOO taxonomies.
	 */
	public function init_taxonomies() {
	}

	/**
	 * Add the HTGVOO post types.
	 */
	public function init_post_types() {
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! defined( 'REST_REQUEST' );
		}
		return false;
	}
}