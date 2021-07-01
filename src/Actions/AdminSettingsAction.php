<?php
/**
 * Plugin settings
 *
 * @since 2.0.0
 *
 * @package GSWOO/Actions
 */

namespace GSWOO\Actions;

use GSWOO\Controllers\AdminSettingsController;

defined( 'ABSPATH' ) || exit;

/**
 * Admin settings
 *
 * @since 1.0.0
 */
class AdminSettingsAction {

	/**
	 * Instance of admin settings controller class.
	 *
	 * @since  2.0.0
	 * @var object AdminSettingsController
	 */
	public $settings_controller;

	/**
	 * Constructor for admin settings
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->settings_controller = new AdminSettingsController();

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
			array( $this, 'init_plugin_admin_settings' )
		);
		add_action(
			'admin_init',
			array( $this, 'init_plugin_admin_settings_sections' )
		);
		add_action(
			'admin_init',
			array( $this, 'init_admin_settings_fields_assertion_method_section' )
		);
		add_action(
			'admin_init',
			array( $this, 'init_admin_settings_fields_auth_code_method_section' )
		);
		add_action(
			'admin_init',
			array( $this, 'init_admin_settings_fields_common_section' )
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

		// if ( $this->settings_controller->is_api_connection_success_by_current_options() ) {
		// add_action( 'init', array( $this, 'init_import_button' ), 0 );
		// } else {
		// add_action( 'admin_notices', array( $this->settings_controller, 'display_settings_require_admin_notice' ) );
		// }

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_settings_assets' ) );
	}

	/**
	 * Enqueue assets for plugin setting page
	 *
	 * @since 2.0.0
	 */
	public function enqueue_settings_assets() {
		if ( ! gswoo_is_plugin_settings_screen() ) {
			return;
		}

		wp_register_style(
			'gswoo-select2-css',
			GSWOO_URI . '/assets/lib/select2/css/select2.min.css',
		);
		wp_enqueue_style( 'gswoo-select2-css' );

		wp_register_script(
			'gswoo-select2-js',
			GSWOO_URI . '/assets/lib/select2/js/select2.min.js',
			array( 'jquery' ),
			true,
			true
		);
		wp_enqueue_script( 'gswoo-select2-js' );
	}

	/**
	 * Init import button.
	 *
	 * @since 2.0.0
	 */
	public function init_import_button() {

		if ( ! gswoo_is_woocommerce_product_screen() ) {
			return;
		}

		wp_register_script(
			'gswoo-import-button',
			GSWOO_URI . '/assets/js/init-import-button.js',
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
			'gswoo-import-button',
			'woocommerce_import_google_sheet_admin',
			$params
		);

		// show plugin import button
		wp_enqueue_script( 'gswoo-import-button' );
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
			array( $this->settings_controller, 'settings_form' )
		);
	}

	/**
	 * Init plugin settings.
	 *
	 * @since 2.0.0
	 */
	public function init_plugin_admin_settings() {
		register_setting(
			'plugin_wc_import_google_sheet_options',
			'plugin_wc_import_google_sheet_options',
			array( 'sanitize_callback' => array( $this, 'sanitize_options' ) )
		);
	}

	/**
	 * Init plugin settings sections
	 *
	 * @since 2.0.0
	 */
	public function init_plugin_admin_settings_sections() {
		add_settings_section(
			'auth_api_key_section',
			'',
			array( $this, 'plugin_section_text' ),
			'api_key_page'
		);
		add_settings_section(
			'oauth2_section',
			'',
			array( $this, 'plugin_section_text' ),
			'oauth2_page'
		);
		add_settings_section(
			'common_section',
			'',
			array( $this, 'plugin_section_text' ),
			'common_page'
		);
	}

	/**
	 * Init plugin settings fields for common_section
	 *
	 * @since 2.0.0
	 */
	public function init_admin_settings_fields_common_section() {
		add_settings_field(
			'plugin_auth_type',
			'',
			array( $this->settings_controller, 'display_common_section' ),
			'common_page',
			'common_section'
		);
		add_settings_field(
			'plugin_sheet_data',
			'',
			array( $this->settings_controller, 'display_common_section' ),
			'common_page',
			'common_section'
		);
	}

	/**
	 * Init plugin settings fields for auth_api_key_section
	 *
	 * @since 2.0.0
	 */
	public function init_admin_settings_fields_assertion_method_section() {
		add_settings_field(
			'plugin_google_api_key',
			'',
			array( $this->settings_controller, 'display_settings_assertion_method_section' ),
			'api_key_page',
			'auth_api_key_section'
		);
	}

	/**
	 * Init plugin settings fields for oauth2_section
	 *
	 * @since 2.0.0
	 */
	public function init_admin_settings_fields_auth_code_method_section() {
		add_settings_field(
			'plugin_google_oauth2_code',
			'',
			array( $this->settings_controller, 'display_settings_auth_code_method_section' ),
			'oauth2_page',
			'oauth2_section'
		);
	}

	/**
	 * Set options and sanitize user input
	 *
	 * @noinspection PhpUnused
	 *
	 * @since 2.0.0
	 *
	 * @param array $input Option input value.
	 *
	 * @return array
	 */
	public function sanitize_options( $input ) {
		$valid_input['google_sheet_title']         = esc_html( $input['google_sheet_title'] );
		$valid_input['google_api_key']             = esc_html( $input['google_api_key'] );
		$valid_input['google_code_oauth2']         = sanitize_text_field( $input['google_code_oauth2'] );
		$valid_input['google_sheet_data']          = esc_html( $input['google_sheet_data'] );
		$valid_input['google_auth_type']           = esc_html( $input['google_auth_type'] );
		$valid_input['google_code_oauth2_restore'] = filter_var(
			esc_html( $input['google_code_oauth2_restore'] ),
			FILTER_VALIDATE_BOOLEAN
		);

		// after sanitizing we produce some logic dependency resolving
		return $this->resolve_options_logic_dependencies( $valid_input );
	}

	/**
	 * Resolve options dependencies
	 *
	 * @param array $valid_input
	 *
	 * @return array
	 */
	public function resolve_options_logic_dependencies( $valid_input ) {
		if ( ! empty( $valid_input['google_code_oauth2_restore'] ) ) {
			$valid_input['google_code_oauth2'] = '';
		}

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

	/**
	 * Function callback for a add_settings_section
	 *
	 * @since 1.0.0
	 */
	public function plugin_section_text() {
		echo '<p></p>';
	}
}
