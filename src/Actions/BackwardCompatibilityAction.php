<?php
/**
 * Backward compatibility between versions action
 *
 * @since 2.0.0
 *
 * @package GSWOO/Actions
 */

namespace GSWOO\Actions;

use GSWOO\Services\DriveInterplayService;
use GSWOO\Services\SheetInterplayService;

defined( 'ABSPATH' ) || exit;

/**
 * Class BackwardCompatibilityAction
 *
 * @package GSWOO\Actions
 */
class BackwardCompatibilityAction {

	/**
	 * Constructor for admin settings
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Fire up actions and filter
	 *
	 * @since 2.0.0
	 */
	public function init() {
		add_filter(
			'gswoo_get_active_google_auth_type',
			array( $this, 'add_backward_active_google_auth_type' ),
			10,
			2
		);

		add_filter(
			'gswoo_get_plugin_options',
			array( $this, 'add_backward_get_plugin_options' ),
			10,
			2
		);

		add_action( 'admin_init', array( $this, 'remove_code_oauth2_options' ) );

		add_filter(
			'gswoo_get_empty_response',
			array( $this, 'add_backward_get_empty_response' ),
			10,
			2
		);
	}

	/**
	 * Renamed option backward compatibility.
	 *
	 * @since 2.0.0
	 * @to all later
	 *
	 * @param string $auth_type
	 * @param array  $options
	 *
	 * @return string
	 */
	public function add_backward_active_google_auth_type( $auth_type, $options ) {
		if ( empty( $options['google_sheet_title'] ) ) {
			return $auth_type;
		} else {
			return 'assertion_method_tab';
		}
	}

	/**
	 * Renamed option backward compatibility.
	 *
	 * @since 2.0.0
	 * @to all later
	 *
	 * @param array                     $options
	 * @param object AdminSettingsModel $settings_model
	 *
	 * @return array
	 */
	public function add_backward_get_plugin_options( $options, $settings_model ) {

		if ( empty( $options['google_sheet_title'] ) || empty( $options['google_api_key'] ) ) {
			return $options;
		}

		$options['google_auth_type'] = 'assertion_method_tab';
		$settings_model->options     = $settings_model->decode_plugin_options( $options );

		$drive_interplay_service = new DriveInterplayService( $settings_model->options );

		$sheets_list =
			$drive_interplay_service->
			get_google_drive_sheets_list();

		foreach ( $sheets_list as $sheet ) {
			if ( isset( $sheet['title'] ) && $sheet['title'] === $options['google_sheet_title'] ) {
				$options['google_sheet_data'] = $sheet['id'];
			}
		}

		return $options;
	}

	/**
	 * Renamed option backward compatibility.
	 *
	 * @since 2.0.0
	 * @to all later
	 *
	 * @param bool                      $is_empty
	 * @param object AdminSettingsModel $settings_model
	 *
	 * @return bool
	 */
	public function add_backward_get_empty_response( $is_empty, $settings_model ) {
		if (
			! empty( $settings_model->options['google_sheet_title'] ) &&
			! empty( $settings_model->options['google_api_key'] ) ) {
			$is_empty = false;
		}

		return $is_empty;
	}

	/**
	 * Remove 'One Click Connection Mehtod' options.
	 *
	 * @since 2.4
	 * @to all later
	 */
	public function remove_code_oauth2_options() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['page'] ) || 'woocommerce_import_products_google_sheet_menu' !== $_GET['page'] ) {
			return;
		}

		$options = get_option( 'plugin_wc_import_google_sheet_options', array() );

		if ( ! is_array( $options ) || ! isset( $options['google_code_oauth2'] ) ) {
			return;
		}

		unset( $options['google_code_oauth2'] );

		if ( isset( $options['google_auth_type'] ) && 'auth_code_method_tab' === $options['google_auth_type'] ) {
			$options['google_auth_type'] = 'assertion_method_tab';
		}

		update_option( 'plugin_wc_import_google_sheet_options', $options );
		delete_option( 'plugin_wc_import_google_sheet_gs_token' );
	}
}
