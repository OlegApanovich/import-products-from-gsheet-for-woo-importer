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
 * @since 2.0.0
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
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->settings_controller = new AdminSettingsController();

		$this->init();
	}

	/**
	 * Fire up actions and filter
	 *
	 * @since 2.0.0
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

		add_action(
			'wp_ajax_restore_action',
			array( $this, 'process_ajax_restore_action' )
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

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_settings_assets' ) );

		$this->set_conditional_init();
	}

	/**
	 * Fire up actions and filter with conditional init
	 *
	 * @since 2.0.0
	 */
	public function set_conditional_init() {
		if ( gswoo_is_woocommerce_product_screen() ) {
			if ( $this->settings_controller->settings_model->is_empty_response() ) {
				add_action( 'admin_notices', array( $this->settings_controller, 'display_settings_require_admin_notice' ) );
			} else {
				add_action( 'init', array( $this, 'init_import_button' ), 0 );
			}
		}
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

		wp_enqueue_style(
			'gswoo-select2-css',
			GSWOO_URI . '/assets/lib/select2/css/select2.min.css',
			array(),
			true
		);

		wp_enqueue_script(
			'gswoo-select2-js',
			GSWOO_URI . '/assets/lib/select2/js/select2.min.js',
			array( 'jquery' ),
			true,
			true
		);

		wp_enqueue_script(
			'gswoo-admin-settings',
			GSWOO_URI . '/assets/js/admin-settings.js',
			array(),
			true,
			true
		);

		wp_enqueue_script(
			'gswoo-admin-settings-ajax',
			GSWOO_URI . '/assets/js/admin-settings-ajax.js',
			array( 'jquery' ),
			true,
			true
		);
		wp_localize_script(
			'gswoo-admin-settings-ajax',
			'gswoo_admin_ajax',
			array(
				'url' => admin_url( 'admin-ajax.php' ),
			)
		);
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

		// Show plugin import button.
		wp_enqueue_script( 'gswoo-import-button' );
	}

	/**
	 * Add submenu to woocommerce admin menu
	 *
	 * @since 2.0.0
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
			'auth_assertion_method_section',
			'',
			array( $this, 'plugin_section_text' ),
			'assertion_method_page'
		);
		add_settings_section(
			'oauth_code_method_section',
			'',
			array( $this, 'plugin_section_text' ),
			'auth_code_method_page'
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
	 * Init plugin settings fields for auth_assertion_method_section
	 *
	 * @since 2.0.0
	 */
	public function init_admin_settings_fields_assertion_method_section() {
		add_settings_field(
			'plugin_google_api_key',
			'',
			array( $this->settings_controller, 'display_settings_assertion_method_section' ),
			'assertion_method_page',
			'auth_assertion_method_section'
		);
	}

	/**
	 * Init plugin settings fields for oauth_code_method_section
	 *
	 * @since 2.0.0
	 */
	public function init_admin_settings_fields_auth_code_method_section() {
		add_settings_field(
			'plugin_google_oauth2_code',
			'',
			array( $this->settings_controller, 'display_settings_auth_code_method_section' ),
			'auth_code_method_page',
			'oauth_code_method_section'
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

		$valid_input['google_api_key']
			= isset( $input['google_api_key'] ) ? esc_html( $input['google_api_key'] ) : '';

		$valid_input['google_code_oauth2']
			= isset( $input['google_code_oauth2'] ) ? sanitize_text_field( $input['google_code_oauth2'] ) : '';

		$valid_input['google_sheet_data']
			= isset( $input['google_sheet_data'] ) ? esc_html( $input['google_sheet_data'] ) : '';

		$valid_input['google_auth_type']
			= isset( $input['google_auth_type'] ) ? esc_html( $input['google_auth_type'] ) : '';

		if ( isset( $input['settings_auth_restore'] ) ) {
			$valid_input['settings_auth_restore'] = filter_var(
				esc_html( $input['settings_auth_restore'] ),
				FILTER_VALIDATE_BOOLEAN
			);
		} else {
			$valid_input['settings_auth_restore'] = false;
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
				sprintf(
					'<a href="%s">%s</a>',
					admin_url( 'admin.php?page=woocommerce_import_products_google_sheet_menu' ),
					esc_html__( 'Settings', 'import-products-from-gsheet-for-woo-importer' )
				),
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

	/**
	 * Function callback for a add_settings_section
	 *
	 * @since 2.0.0
	 */
	public function process_ajax_restore_action() {
		$this->settings_controller->settings_model->delete_options();
		wp_die();
	}
}
