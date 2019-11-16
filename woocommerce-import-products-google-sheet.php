<?php
/**
 * The plugin bootstrap file
 *
 * @since 1.0.0
 * @package Woocommerce_Import_Products_Google_Sheet
 *
 * Plugin Name:  Woocommerce Import Products Google Sheet
 * Plugin URI:   https://github.com/OlegApanovich/woocommerce-import-products-google-sheet
 * Description:  Import woocommerce products from google sheet by using native wordpres importer
 * Version:      1.0.0
 * Author:       Oleg Apanovich
 * Author URI:   https://github.com/OlegApanovich
 * License:      GPL-3.0+
 * License URI:  http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: woocommerce-import-products-google-sheet
 * Domain Path: /languages
 */
defined( 'ABSPATH' ) || exit;

require_once plugin_dir_path( __FILE__ ) . 'includes/helpers.php';

/**
 * Main Plagin Class.
 *
 * @since 1.0.0
 */
final class WС_Import_Products_Google_Sheet {
	/**
	 * The single instance of the class.
	 *
	 * @var    WС_Import_Products_Google_Sheet
	 * @access protected
	 * @since  1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main plugin instance.
	 *
	 * Ensures only one instance of plugin is loaded or can be loaded.
	 *
	 * @since 1.0.0
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
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->define_constants();
		$this->init_hooks();
	}

	/**
	 * Define WС_Import_Products_Google_Sheet Constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		define( 'WC_IMPORT_SHEET_PLUGIN_FILE', __FILE__ );
		define( 'WC_IMPORT_SHEET_URI', plugins_url( '', WC_IMPORT_SHEET_PLUGIN_FILE ) );
		define( 'WC_IMPORT_SHEET_URI_ABSPATH', dirname( __FILE__ ) . '/' );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'admin_init', array( $this, 'init_frontend' ), 0 );
		add_filter('plugin_action_links_' . plugin_basename( __FILE__ ),
			array( $this, 'set_plugin_action_links' ), 10, 1 );
		add_filter( 'woocommerce_screen_ids',
			array( $this, 'add_woocommerce_screen_ids' ), 10, 1 );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		if ( $this->is_request( 'admin' ) ) {
			include_once( WC_IMPORT_SHEET_URI_ABSPATH
			              . 'vendor/autoload.php' );
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
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// Before init action.
		do_action( 'before_woocommerce_import_products_google_sheet_init' );
		// Set up localisation.
		$this->load_plugin_textdomain();
		// After init action
		do_action( 'woocommerce_import_products_google_sheet_init' );
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones
	 * if the same translation is present.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' )
			? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale,
			'woocommerce-import-products-google-sheet' );

		unload_textdomain( 'woocommerce-import-products-google-sheet' );
		load_plugin_textdomain( 'woocommerce-import-products-google-sheet',
			false, WC_IMPORT_SHEET_URI_ABSPATH . '/languages' );
	}

	/**
	 * Init frontend files.
	 *
	 * @since 1.0.0
	 */
	public function init_frontend() {
		wp_register_script( 'wc_import_google_sheet_admin',
			WC_IMPORT_SHEET_URI . '/assets/js/admin.js', [ 'jquery' ] );
		$params = array(
			'urls'    => array(
				'import_products_google_sheet' =>
					current_user_can( 'import' )
					? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer_google_sheet' ) )
					: null
			),
			'strings' => array(
				'import_products_google_sheet' =>
				esc_html__( 'Import From Google Sheet', 'woocommerce-import-products-google-sheet' ),
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
	 *
	 * @param array $screen_ids
	 *
	 * @return array $screen_ids
	 */
	public function add_woocommerce_screen_ids( $screen_ids ) {
		$screen_ids[] = 'product_page_product_importer_google_sheet';

		return $screen_ids;
	}

	/**
	 * Короткое описание функции
	 *
	 * @param int $bar test description this argument
	 *
	 * @return void
	 */
	public function set_plugin_action_links( $links ) {
		return array_merge(
			array(
				'<a href="' . 
				admin_url( 'admin.php?page=woocommerce_import_products_google_sheet_menu' ) . 
				'">' . 
				esc_html__( 'Settings', 'woocommerce-import-products-google-sheet' ) . 
				'</a>'
			),
			$links
		);
		return $links;
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
