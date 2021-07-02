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
	 * Plugin options.
	 *
	 * @since  2.0.0
	 * @var array
	 */
	public $options;

	/**
	 * AdminSettingsController constructor.
	 */
	public function __construct() {
		$this->settings_model = new AdminSettingsModel();
		$this->options        = $this->settings_model->get_plugin_options();
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

		$response = $this->settings_model->process_connection( $this->options );

		$google_auth_type = $this->settings_model->get_active_auth_tab();

		include_once GSWOO_URI_ABSPATH
					 . '/src/Views/html-admin-settings-common-section.php';
	}

	/**
	 * Prerequisites and display section settings
	 *
	 * @since 2.0.0
	 */
	public function display_settings_assertion_method_section() {
		if ( empty( $this->options['google_api_key'] ) || ! empty( $this->options['google_code_oauth2_restore'] ) ) {
			include_once GSWOO_URI_ABSPATH
						 . '/src/Views/html-admin-settings-assertion-method-section-receive.php';
		} else {
			include_once GSWOO_URI_ABSPATH
						 . '/src/Views/html-admin-settings-assertion-method-section-restore.php';
		}
	}

	/**
	 * Prerequisites and display section settings
	 *
	 * @since 2.0.0
	 */
	public function display_settings_auth_code_method_section() {

		if ( empty( $this->options['google_code_oauth2'] ) || ! empty( $this->options['google_code_oauth2_restore'] ) ) {
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
