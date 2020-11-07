<?php
/**
 * THESPA_waterTesting Class.
 *
 * @package THESPA_waterTesting\Classes
 * @version 1.0.13
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class THESPA_waterTesting
 */
class THESPA_waterTesting {

	/**
	 * THESPA_waterTesting version.
	 *
	 * @var string
	 */
	public $version = '1.0.13';

	/**
	 * The single instance of the class.
	 *
	 * @var THESPA_waterTesting
	 */
	protected static $_instance = null;

	/**
	 * THESPA_waterTesting Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
		$this->init_shortcodes();

		do_action( 'THESPA_waterTesting_loaded' );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
//		if ( $this->is_request( 'admin' ) ) {
//			include_once THESPA_PLUGIN_DIR . 'classes/admin/class-thespa-admin.php';
//		}

		/**
		 * Core classes.
		 */
		include_once THESPA_PLUGIN_DIR . 'classes/class-shortCodes.php';
		include_once THESPA_PLUGIN_DIR . 'classes/class-media.php';
		include_once THESPA_PLUGIN_DIR . 'classes/class-data.php';
		include_once THESPA_PLUGIN_DIR . 'classes/class-requests.php';
		include_once THESPA_PLUGIN_DIR . 'classes/class-mail.php';
		include_once THESPA_PLUGIN_DIR . 'classes/class-report.php';
		include_once THESPA_PLUGIN_DIR . 'pdf-report/dompdf/autoload.inc.php';
	}

	/**
	 * Main THESPA_waterTesting Instance.
	 *
	 * Ensures only one instance of THESPA_waterTesting is loaded or can be loaded.
	 *
	 * @static
	 * @see THESPA_EP()
	 * @return THESPA_waterTesting - Main instance.
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
		add_shortcode( 'waterTesting', array( 'THESPA_shortCodes', 'waterTesting' ) );
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_action( 'wp_ajax_thespa_save', array( 'THESPA_Requests', 'save' ), 10 );
		add_action( 'wp_ajax_thespa_remove_test', array( 'THESPA_Requests', 'remove_test' ), 10 );
		add_action( 'wp_ajax_nopriv_thespa_to_email', array( 'THESPA_Requests', 'to_email' ), 10 );
		add_action( 'wp_ajax_thespa_to-email', array( 'THESPA_Requests', 'form_handler' ), 10 );
		add_action( 'wp_ajax_nopriv_thespa_to-email', array( 'THESPA_Requests', 'form_handler' ), 10 );
		add_action( 'wp_ajax_thespa_get-help', array( 'THESPA_Requests', 'form_handler' ), 10 );
		add_action( 'wp_ajax_nopriv_thespa_get-help', array( 'THESPA_Requests', 'form_handler' ), 10 );

		add_action( 'wp_enqueue_scripts', array( 'THESPA_media', 'register' ), 10 );

//		add_action( 'admin_enqueue_scripts', array( 'THESPA_Media', 'register' ), 10 );
//		add_action( 'admin_enqueue_scripts', array( 'THESPA_Admin_Output', 'register' ), 20 );
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
	 * Define THESPA_waterTesting Constants.
	 */
	private function define_constants() {
		$this->define( 'THESPA_ASSETS_DIR', THESPA_PLUGIN_DIR . 'assets' );
		$this->define( 'THESPA_ASSETS_URL', get_option( 'siteurl' ) . str_replace( str_replace( '\\', '/', ABSPATH ), '/', THESPA_ASSETS_DIR ) );
		$this->define( 'THESPA_VERSION', $this->version );
	}

	/**
	 * Add the THESPA taxonomies.
	 */
	public function init_taxonomies() {
		register_taxonomy(
			'thespa-message-tax',
			apply_filters( 'thespa_taxonomy_objects_message', array( 'thespa-message' ) ),
			apply_filters( 'thespa_taxonomy_args_message', array(
				'labels'       => array(
					'name' => 'thespa Message',
				),
				'hierarchical' => true,
				'show_ui'      => false,
				'query_var'    => true,
				'rewrite'      => false,
			) )
		);
	}

	/**
	 * Add the THESPA post types.
	 */
	public function init_post_types() {
		$args = array(
			'labels'              => array(
				'name' => 'Form Messages',
			),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_admin_bar'   => true,
			'publicly_queryable'   => true,
			'supports'            => array(
				'title',
				'revisions',
				'custom-fields',
				'editor',
			)
		);

		register_post_type( 'thespa-message', $args );
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