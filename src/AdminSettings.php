<?php
/**
 * Plugin settings
 *
 * @since 1.0.0
 *
 * @package GSWOO
 */

namespace GSWOO;

defined( 'ABSPATH' ) || exit;

/**
 * Admin settings
 *
 * @since 1.0.0
 */
class AdminSettings {

	/**
	 * Constructor for admin settings
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init admin settings
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action(
			'admin_init',
			array( $this, 'plugin_admin_init_settings' )
		);
		add_filter(
			'plugin_action_links_' . plugin_basename( GSWOO_PLUGIN_FILE ),
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
	 * Add submenu to woocommerce admin menu
	 *
	 * @since 1.0.0
	 */
	public function add_menu() {
		add_submenu_page(
			'woocommerce',
			esc_html__( 'Import Google Sheet', 'import-products-from-gsheet-for-woo-importer' ),
			esc_html__( 'Import Google Sheet', 'import-products-from-gsheet-for-woo-importer' ),
			'manage_options',
			'woocommerce_import_products_google_sheet_menu',
			array( $this, 'settings_form' )
		);
	}

	/**
	 * Add main plugin settings form
	 *
	 * @since 1.0.0
	 */
	public function settings_form() {
		include_once GSWOO_URI_ABSPATH
					. '/src/Views/html-admin-settings-form.php';
	}

	/**
	 * Init plugin settings
	 *
	 * @since 1.0.0
	 */
	public function plugin_admin_init_settings() {
		register_setting(
			'plugin_wc_import_google_sheet_options',
			'plugin_wc_import_google_sheet_options',
			array( 'sanitize_callback' => array( $this, 'validate_options' ) )
		);

		add_settings_section(
			'plugin_main',
			'',
			array( $this, 'plugin_section_text' ),
			'plugin'
		);

		add_settings_field(
			'plugin_google_api_key',
			'',
			array( $this, 'display_settings' ),
			'plugin',
			'plugin_main'
		);

		add_settings_field(
			'plugin_google_sheet_title',
			'',
			array( $this, 'display_settings' ),
			'plugin',
			'plugin_main'
		);
	}

	/**
	 * Function callback for a add_settings_section
	 *
	 * @since 1.0.0
	 */
	public function plugin_section_text() {
		echo '<p></p>';
	}

	/**
	 * Function callback for a add_settings_field
	 *
	 * @since 1.0.0
	 * @noinspection PhpUnusedLocalVariableInspection
	 */
	public function display_settings() {
		$options = get_option( 'plugin_wc_import_google_sheet_options' );

		$google_api_key = ! empty( $options['google_api_key'] )
			? $options['google_api_key'] : '';

		$google_sheet_title = ! empty( $options['google_sheet_title'] )
			? $options['google_sheet_title'] : '';

		include_once GSWOO_URI_ABSPATH
					. '/src/Views/html-admin-settings-form-options.php';
	}

	/**
	 * Set options and validate user input
	 *
	 * @noinspection PhpUnused
	 *
	 * @since 1.0.0
	 *
	 * @param array $input Option input value.
	 *
	 * @return array
	 */
	public function validate_options( $input ) {
		$valid_input['google_sheet_title'] = esc_html( $input['google_sheet_title'] );
		$valid_input['google_api_key']     = esc_html( $input['google_api_key'] );

		return $valid_input;
	}

	/**
	 * Add plugin provided screen to woocommerce admin area.
	 *
	 * @since 1.0.0
	 *
	 * @param array $screen_ids Screen id list.
	 *
	 * @return array $screen_ids
	 */
	public function add_woocommerce_screen_ids( $screen_ids ) {

		$screen_ids[] = 'product_page_product_importer_google_sheet';

		return $screen_ids;
	}

	/**
	 * Set additional links on a plugin admin dashboard page.
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
