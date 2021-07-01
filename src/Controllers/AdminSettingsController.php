<?php
/**
 * Plugin settings controller
 *
 * @since 2.0.0
 *
 * @package GSWOO\Controllers
 */

namespace GSWOO\Controllers;

use GSWOO\Models\AdminSettingsModel;
use GSWOO\Services\SheetInterplayService;

defined( 'ABSPATH' ) || exit;

/**
 * Admin settings controller
 *
 * @since 2.0.0
 */
class AdminSettingsController {

	/**
	 * Instance of AdminSettingsController class.
	 *
	 * @since  2.0.0
	 * @var object AdminSettingsController
	 */
	public $settings_model;

	/**
	 * Instance of SheetInterplayService class.
	 *
	 * @since  2.0.0
	 * @var object SheetInterplayService
	 */
	public $sheet_interplay_service;

	/**
	 * AdminSettingsController constructor.
	 */
	public function __construct() {
		$this->settings_model          = new AdminSettingsModel();
		$this->sheet_interplay_service = new SheetInterplayService();
	}

	/**
	 * Add main plugin settings form.
	 *
	 * @since 2.0.0
	 *
	 * @noinspection PhpUnusedLocalVariableInspection
	 */
	public function settings_form() {

		$active_tab = $this->settings_model->get_active_auth_tab();

		include_once GSWOO_URI_ABSPATH
					 . '/src/Views/html-admin-settings-form.php';
	}

	/**
	 * Prerequisites and display section settings
	 *
	 * @since 2.0.0
	 * @noinspection PhpUnusedLocalVariableInspection
	 */
	public function display_common_section() {
		$options = $this->settings_model->get_plugin_options();

		$response = $this->settings_model->process_connection( $options );

		// if ( $this->settings_model->is_response_error( $response ) || ! empty( $options['google_api_key'] ) ) {
		// $sheets_list =
		// $this->
		// sheet_interplay_service->
		// set_api_connect( $response['token'] )->
		// get_google_drive_sheets_list();
		// }

		$google_auth_type = $this->settings_model->get_active_auth_tab();

		include_once GSWOO_URI_ABSPATH
					 . '/src/Views/html-admin-settings-common-section.php';
	}

	/**
	 * Prerequisites and display section settings
	 *
	 * @since 2.0.0
	 * @noinspection PhpUnusedLocalVariableInspection
	 */
	public function display_settings_assertion_method_section() {

		$options = $this->settings_model->get_plugin_options();

		include_once GSWOO_URI_ABSPATH
					 . '/src/Views/html-admin-settings-assertion-method-section.php';
	}

	/**
	 * Prerequisites and display section settings
	 *
	 * @since 2.0.0
	 */
	public function display_settings_auth_code_method_section() {

		$options = $this->settings_model->get_plugin_options();

		if ( empty( $options['google_code_oauth2'] ) ) {
			include_once GSWOO_URI_ABSPATH
						 . '/src/Views/html-admin-settings-auth-code-method-section-receive.php';
		} else {
			include_once GSWOO_URI_ABSPATH
						 . '/src/Views/html-admin-settings-auth-code-method-section-restore.php';
		}
	}

	/**
	 * Plugin connection google API error message on WC product screen
	 *
	 * @since 2.0.0
	 *
	 * @noinspection PhpUnusedLocalVariableInspection
	 */
	public function display_settings_require_admin_notice() {

		if ( ! gswoo_is_woocommerce_product_list_screen() ) {
			return;
		}

		$menu_page_url = menu_page_url( 'woocommerce_import_products_google_sheet_menu', false );

		include_once GSWOO_URI_ABSPATH
					 . '/src/Views/html-admin-settings-require-admin-notice.php';
	}
}
