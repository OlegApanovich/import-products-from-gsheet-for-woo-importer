<?php
/**
 * Plugin Name: Woocommerce Import Products Google Sheet
 * Plugin URI: https://github.com/OlegApanovich/woocommerce-import-products-google-sheet
 * Description: Import woocommerce products from google sheet by using native wordpres importer
 * Version: 0.1
 * Author: Oleg Apanovich
 * Author URI: https://github.com/OlegApanovich
 * Requires at least: 4.4
 * Tested up to: 5.2
 *
 * Text Domain: woocommerce-import-products-google-sheet
 * Domain Path: /languages/
 *
 * @package
 * @category
 * @author Oleg Apanovich
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path( __FILE__ ) . 'includes/helpers.php';

/**
 * Main Plagin Class.
 *
 * @class      WС_Import_Products_Google_Sheet
 * @version    3.1.0
 */
final class WС_Import_Products_Google_Sheet {
	/**
	 * The single instance of the class.
	 *
	 * @var WС_Import_Products_Google_Sheet
	 * @since 0.1
	 */
	protected static $_instance = null;

	/**
	 * Main plugin instance.
	 *
	 * Ensures only one instance of plugin is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @return Plagin - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * WС_Import_Products_Google_Sheet Constructor.
	 */
	public function __construct() {
		// Add woocommerce activation checkup
		$this->define_constants();
		$this->init_hooks();
	}

	/**
	 * Define WС_Import_Products_Google_Sheet Constants.
	 */
	private function define_constants() {
		define( 'WC_IMPORT_SHEET_PLUGIN_FILE', __FILE__ );
		define( 'WC_IMPORT_SHEET_URI',
			plugins_url( '', WC_IMPORT_SHEET_PLUGIN_FILE ) );
		define( 'WC_IMPORT_SHEET_URI_ABSPATH', dirname( __FILE__ ) . '/' );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since  0.1
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'admin_init', array( $this, 'init_frontend' ), 0 );
		add_filter( 'woocommerce_screen_ids',
			array( $this, 'add_woocommerce_screen_ids' ), 10, 1 );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		if ( $this->is_request( 'admin' ) ) {
			include_once( WC_IMPORT_SHEET_URI_ABSPATH . 'vendor/autoload.php' );
			include_once( WC_IMPORT_SHEET_URI_ABSPATH
			              . 'includes/class-admin-settings.php' );
			include_once( WC_IMPORT_SHEET_URI_ABSPATH
			              . 'woocommerce-importer/class-wc-admin-importers.php' );
			include_once( WC_IMPORT_SHEET_URI_ABSPATH
			              . 'includes/class-wrapper-api-google-drive.php' );
		}
	}

	/**
	 * Init plugin when WordPress Initialises.
	 */
	public function init() {
		// Before init action.
		do_action( 'before_woocommerce_import_products_google_sheet_init' );

		// Set up localisation.
		$this->load_plugin_textdomain();

		do_action( 'woocommerce_import_products_google_sheet_init' );
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/plugins/woocommerce-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' )
			? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale,
			'woocommerce-import-products-google-sheet' );

		unload_textdomain( 'woocommerce-import-products-google-sheet' );
		// load_textdomain( 'woocommerce-import-products-google-sheet', WP_LANG_DIR . '/woocommerce-import-products-google-sheet-' . $locale . '.mo' );
		load_plugin_textdomain( 'woocommerce-import-products-google-sheet',
			false, WC_IMPORT_SHEET_URI_ABSPATH . '/languages' );
	}

	/**
	 * Init frontend files.
	 */
	public function init_frontend() {
		wp_register_script( 'wc_import_google_sheet_admin',
			WC_IMPORT_SHEET_URI . '/assets/js/admin.js', [ 'jquery' ] );
		$params = array(
			'urls'    => array(
				'import_products_google_sheet' => current_user_can( 'import' )
					? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer_google_sheet' ) )
					: null
			),
			'strings' => array(
				'import_products_google_sheet' => __( 'Import From Google Sheet',
					'woocommerce-import-products-google-sheet' ),
			),
		);
		wp_localize_script( 'wc_import_google_sheet_admin',
			'woocommerce_import_google_sheet_admin', $params );

		$settings = new Admin_Settings;
		$options = get_option( 'plugin_wc_import_google_sheet_options' );

		$check = $settings->check_user_input( $options );

		if ( $check ) {
			wp_enqueue_script( 'wc_import_google_sheet_admin' );
		}
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string      $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 *
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) )
				       && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Add plugin provided screen to woocommerce admin area
	 *
	 * @param array $screen_ids
	 *
	 * @return array $screen_ids
	 */
	public function add_woocommerce_screen_ids( $screen_ids ) {
		$screen_ids[] = 'product_page_product_importer_google_sheet';

		return $screen_ids;
	}
}

// check if woocommerce is already active
wc_import_google_sheet_is_plugin_active(
	'Woocommerce Import Products Google Sheet',
	'WooCommerce',
	'woocommerce/woocommerce.php',
	'woocommerce-import-products-google-sheet',
	'3.1.0'
);

WС_Import_Products_Google_Sheet::instance();