<?php
/**
 * The plugin bootstrap file
 *
 * @since 1.0.0
 *
 * Plugin Name:  GSheet For Woo Importer
 * Plugin URI:   https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer
 * Description:  Import woocommerce products from google sheet by using native woocommerce importer
 * Version:      1.0.0
 * Author:       Oleg Apanovich
 * Author URI:   https://github.com/OlegApanovich
 * License:      GPL-3.0+
 * License URI:  http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:  import-products-from-gsheet-for-woo-importer
 * Domain Path:  /languages
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Plagin Class.
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
	 * Main plugin instance.
	 *
	 * Ensures only one instance of plugin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Plagin - Main instance.
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
		require_once plugin_dir_path( __FILE__ ) . 'includes/helpers.php';

		// Check if woocommerce is already active.
		$woocommerce_check = gswoo_is_plugin_active(
			'GSheet For Woo Importer',
			'WooCommerce',
			'woocommerce/woocommerce.php',
			'3.1.0'
		);

		if ( $woocommerce_check ) {
			// Add woocommerce activation checkup.
			$this->define_constants();
			$this->init_hooks();
		}
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
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'admin_init', array( $this, 'init_frontend' ), 0 );
		add_filter(
			'plugin_action_links_' . plugin_basename( __FILE__ ),
			array( $this, 'set_plugin_action_links' ),
			10,
			1
		);
		add_filter(
			'woocommerce_screen_ids',
			array( $this, 'add_woocommerce_screen_ids' ),
			10,
			1
		);
	}

	/**
	 * Include required plugin core files
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		if ( $this->is_request( 'admin' ) ) {
			include_once GSWOO_URI_ABSPATH . 'lib/autoload.php';
			include_once GSWOO_URI_ABSPATH . 'includes/class-gswoo-admin-settings.php';
			include_once GSWOO_URI_ABSPATH . 'woocommerce-importer/class-gswoo-wc-admin-importers.php';
			include_once GSWOO_URI_ABSPATH . 'includes/class-gswoo-wrapper-api-google-drive.php';
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
	 * Init frontend files.
	 *
	 * @since 1.0.0
	 */
	public function init_frontend() {
		wp_register_script(
			'wc_import_google_sheet_admin',
			GSWOO_URI . '/assets/js/admin.js',
			array( 'jquery' ),
			true,
			true
		);
		$params = array(
			'urls'    => array(
				'import_products_google_sheet' =>
					current_user_can( 'import' )
					? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer_google_sheet' ) )
					: null,
			),
			'strings' => array(
				'import_products_google_sheet' =>
				esc_html__( 'Import From Google Sheet', 'import-products-from-gsheet-for-woo-importer' ),
			),
		);
		wp_localize_script(
			'wc_import_google_sheet_admin',
			'woocommerce_import_google_sheet_admin',
			$params
		);

		$settings = new GSWOO_Admin_Settings();
		$check    = $settings->check_user_input( $settings->get_plugin_options() );

		if ( $check ) {
			wp_enqueue_script( 'wc_import_google_sheet_admin' );
		}
	}

	/**
	 * Define constant if not already set.
	 *
	 * @since 1.0.0
	 *
	 * @param  string      $name contant name.
	 * @param  string|bool $value constant value.
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
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Add plugin provided screen to woocommerce admin area
	 *
	 * @since 1.0.0
	 *
	 * @param array $screen_ids all screen ids.
	 *
	 * @return array $screen_ids
	 */
	public function add_woocommerce_screen_ids( $screen_ids ) {
		$screen_ids[] = 'product_page_product_importer_google_sheet';

		return $screen_ids;
	}

	/**
	 * Set additional links on a plugin admin dashbord page
	 *
	 * @since 1.0.0
	 *
	 * @param array $links all links.
	 *
	 * @return array
	 */
	public function set_plugin_action_links( $links ) {
		return array_merge(
			array(
				'<a href="' .
				admin_url( 'admin.php?page=woocommerce_import_products_google_sheet_menu' ) .
				'">' .
				esc_html__( 'Settings', 'import-products-from-gsheet-for-woo-importer' ) .
				'</a>',
			),
			$links
		);
	}
}

GSWOO_Plugin::instance();
