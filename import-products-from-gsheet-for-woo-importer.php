<?php
/**
 * The plugin bootstrap file
 *
 * @since 1.0.0
 * @package GSWOO
 *
 * Plugin Name: GSheet For Woo Importer
 * Plugin URI:  https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer
 * Description: Import woocommerce products from google sheet by using native woocommerce importer
 * Version:     2.2.0
 * Author:      Oleg Apanovich
 * Author URI:  https://github.com/OlegApanovich
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: import-products-from-gsheet-for-woo-importer
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

use GSWOO\Actions\AdminSettingsAction;
use GSWOO\Actions\BackwardCompatibilityAction;
use GSWOO\WoocommerceImporter\WcAdminImporters;

/**
 * Main Plugin Class.
 *
 * @since 1.0.0
 */
final class GSWOO_Plugin {
	/**
	 * The single instance of the class.
	 *
	 * @var    GSWOO_Plugin
	 * @access protected
	 * @since  1.0.0
	 */
	protected static $instance = null;

	/**
	 * The single instance of admin settings class.
	 *
	 * @since  2.0.0
	 * @var    AdminSettingsAction object
	 */
	public $gswoo_settings;

	/**
	 * Main plugin instance.
	 *
	 * Ensures only one instance of plugin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return object Plugin main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * WС_Import_Products_Google_Sheet Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		if ( ! $this->is_request( 'admin' ) ) {
			return;
		}

		$this->includes();

		if ( ! $this->is_woocommerce() ) {
			return;
		}

		$this->define_constants();
		$this->init_hooks();
		$this->init_actions();
	}

	/**
	 * Check if woocommerce already activated.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_woocommerce() {
		return gswoo_is_plugin_active(
			'GSheet For Woo Importer',
			'WooCommerce',
			'woocommerce/woocommerce.php',
			'3.1.0'
		);
	}

	/**
	 * Include required plugin core files.
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		require_once __DIR__ . '/includes/helpers.php';
		require_once __DIR__ . '/vendor/autoload.php';
	}

	/**
	 * Define plugin constants.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		define( 'GSWOO_PLUGIN_FILE', __FILE__ );
		define( 'GSWOO_URI', plugins_url( '', GSWOO_PLUGIN_FILE ) );
		define( 'GSWOO_URI_ABSPATH', dirname( __FILE__ ) . '/' );
		define( 'GSWOO_WC_ABSPATH', WP_PLUGIN_DIR . '/woocommerce/' );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( $this, 'inject_woocommerce_import' ) );
	}

	/**
	 * Fire up all wp actions and filters injections.
	 *
	 * @since 2.0.0
	 */
	private function init_actions() {
		new BackwardCompatibilityAction();
		$this->gswoo_settings = new AdminSettingsAction();
	}

	/**
	 * Initialize plugin woocommerce import injection
	 *
	 * @since 2.0
	 */
	public function inject_woocommerce_import() {
		new WcAdminImporters();
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
		// After init action.
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
		load_plugin_textdomain(
			'import-products-from-gsheet-for-woo-importer',
			false,
			GSWOO_URI_ABSPATH . '/languages'
		);
	}

	/**
	 * What type of request is this scripts?
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpSameParameterValueInspection
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 *
	 * @return bool
	 */
	private function is_request( $type ) {
		$is_type = false;
		switch ( $type ) {
			case 'admin':
				$is_type = is_admin();
				break;
			case 'ajax':
				$is_type = defined( 'DOING_AJAX' );
				break;
			case 'cron':
				$is_type = defined( 'DOING_CRON' );
				break;
			case 'frontend':
				$is_type = ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
				break;
		}

		return $is_type;
	}
}

GSWOO_Plugin::instance();
