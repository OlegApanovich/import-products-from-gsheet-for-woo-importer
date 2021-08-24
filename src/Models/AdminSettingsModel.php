<?php
/**
 * Plugin settings model
 * Collect data for plugin settings views
 *
 * @since 2.0.0
 *
 * @package GSWOO\Models
 */

namespace GSWOO\Models;

use GSWOO\Services\SheetInterplayService;
use GSWOO\Services\DriveInterplayService;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Admin settings model
 *
 * @since 2.0.0
 */
class AdminSettingsModel {

	/**
	 * Instance of SheetInterplayService class.
	 *
	 * @since  2.0.0
	 * @var object SheetInterplayService
	 */
	public $sheet_interplay_service;

	/**
	 * Plugin options.
	 *
	 * @since  2.0.0
	 * @var array
	 */
	public $options;

	/**
	 * AdminSettingsModel constructor.
	 */
	public function __construct() {
		$this->options                 = $this->get_plugin_options();
		$this->sheet_interplay_service = new SheetInterplayService( $this->options );
		$this->drive_interplay_service = new DriveInterplayService( $this->options );
	}

	/**
	 * Get currently activated google API connection method.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_active_google_auth_type() {
		if ( isset( $_GET['auth_tab'] ) ) {
			$auth_type = sanitize_text_field( wp_unslash( $_GET['auth_tab'] ) );
		} else {
			if ( empty( $this->options['google_auth_type'] ) ) {
				// fallback for default method.
				$auth_type = 'auth_code_method_tab';
			} else {
				$auth_type = $this->options['google_auth_type'];
			}
		}

		return apply_filters( 'gswoo_get_active_google_auth_type', $auth_type, $this->options );
	}

	/**
	 * Get plugin options
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_plugin_options() {
		$options = get_option( 'plugin_wc_import_google_sheet_options' );

		$options = $this->decode_plugin_options( $options );

		return apply_filters( 'gswoo_get_plugin_options', $options, $this );
	}

	/**
	 * Decode special chars in plugin options.
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function decode_plugin_options( $options ) {
		if ( ! is_array( $options ) ) {
			return $options;
		}

		foreach ( $options as $options_name => $option_value ) {
			$options[ $options_name ] = wp_specialchars_decode( $option_value, ENT_QUOTES );
		}

		return $options;
	}

	/**
	 * Process connection to google API.
	 *
	 * @since 2.0.0
	 *
	 * @return array (
	 *      'status'  => [error, warning, success]
	 *      'message' => text,
	 *      'sheets_list' => array
	 * )
	 */
	public function process_connection() {

		$response = array();

		if ( $this->is_empty_response() || $this->is_process_restore_response() ) {
			return $response;
		}

		$sheets_list =
			$this->
			drive_interplay_service->
			get_google_drive_sheets_list();

		if ( is_wp_error( $sheets_list ) ) {
			return $this->get_error_connection_response( $sheets_list );
		}

		if ( empty( $this->options['google_sheet_data'] ) ) {
			return $this->get_warning_connection_response( $sheets_list );
		}

		$sheet_title =
			$this->get_sheet_title_from_sheet_list(
				$this->options['google_sheet_data'],
				$sheets_list
			);

		if ( is_wp_error( $sheet_title ) ) {
			return $this->get_error_connection_response( $sheet_title );
		}

		return $this->get_success_connection_response( $sheets_list );
	}

	/**
	 * Check user data for empty fields.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_empty_response() {

		$is_empty = false;

		if ( empty( $this->options ) || empty( $this->options['google_auth_type'] ) ) {
			$is_empty = true;
		} else {
			switch ( $this->options['google_auth_type'] ) {
				case 'assertion_method_tab':
					if ( ! $this->options['google_api_key'] ) {
						$is_empty = true;
					}
					break;
				case 'auth_code_method_tab':
					if ( empty( $this->options['google_code_oauth2'] ) ) {
						$is_empty = true;
					}
					break;
			}
		}

		return apply_filters( 'gswoo_get_empty_response', $is_empty, $this );
	}

	/**
	 * Check if user options has restore response and process it if it has.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_process_restore_response() {
		if ( empty( $this->options['settings_auth_restore'] ) ) {
			return false;
		} else {
			$this->delete_options();
			return true;
		}
	}

	/**
	 * Return message after error access to google api by provided user credentials.
	 *
	 * @since 2.0.0
	 *
	 * @param object $wp_error WP_Error.
	 *
	 * @return array
	 */
	public function get_error_connection_response( $wp_error ) {
		$error = reset( $wp_error->errors );
		$error = $error[0];

		$return['status'] = 'error';

		$return['message'] = sprintf(
		// translators: %s: plugin import page url.
			__(
				"We can't set connection with google API by your provided credentials. Please check your settings and internet connection and try again. Error: %s",
				'import-products-from-gsheet-for-woo-importer'
			),
			$error
		);

		return $return;
	}

	/**
	 * Return message after success access to google api by provided user credentials.
	 *
	 * @since 2.0.0
	 *
	 * @param array $sheets_list
	 *
	 * @return array
	 */
	public function get_success_connection_response( $sheets_list ) {
		$menu_page_url = menu_page_url( 'product_importer_google_sheet', false );

		$return['status'] = 'success';

		$return['sheets_list'] = $sheets_list;

		$return['message'] = sprintf(
		// translators: %s: plugin import page url.
			__(
				'Your settings was received successfully, now you can go to <a href="%s">import products spreadsheet page</a> and try import',
				'import-products-from-gsheet-for-woo-importer'
			),
			$menu_page_url
		);

		return $return;
	}

	/**
	 * Return warning connection message.
	 *
	 * @since 2.0.0
	 *
	 * @param array $sheets_list
	 *
	 * @return array
	 */
	public function get_warning_connection_response( $sheets_list ) {
		$return['status'] = 'warning';

		$return['sheets_list'] = $sheets_list;

		$return['message'] = __(
			'We successfully connected to google API by your provided credentials. Please select google sheet title for import process.',
			'import-products-from-gsheet-for-woo-importer'
		);

		return $return;
	}

	/**
	 * Delete all options.
	 *
	 * @since 2.0.0
	 */
	public function delete_options() {
		delete_option( 'plugin_wc_import_google_sheet_gs_token' );
		delete_option( 'plugin_wc_import_google_sheet_options' );
	}

	/**
	 * Check if processing google api request response is success.
	 *
	 * @since 2.0.0
	 *
	 * @param array $response
	 *
	 * @return bool
	 */
	public function is_response_success( $response ) {
		return ! empty( $response['status'] ) && 'success' === $response['status'];
	}

	/**
	 * Get sheet title from sheet list
	 *
	 * @since 2.0.0
	 *
	 * @param string $sheet_id
	 * @param array  $sheet_list
	 *
	 * @return string | WP_Error
	 */
	public function get_sheet_title_from_sheet_list( $sheet_id, $sheet_list ) {
		$sheet_title = '';

		foreach ( $sheet_list as $sheet ) {
			if ( ! empty( $sheet['id'] ) && $sheet['id'] === $sheet_id ) {
				$sheet_title = $sheet['title'];
			}
		}

		if ( ! $sheet_title ) {
			return new WP_Error(
				'api_data_error',
				'(' . __METHOD__ . ') ' .
				__(
					"Can't find predefined sheet on a Google drive, please reset your setting and try to set them again",
					'import-products-from-gsheet-for-woo-importer'
				)
			);
		}

		return $sheet_title;
	}

	/**
	 * Replace woocommerce import file with Google sheet content.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $import_form_data
	 * @param string $file_sheet_path
	 *
	 * @return bool | WP_Error
	 */
	public function replace_import_file_with_gsheet_content( $import_form_data, $file_sheet_path ) {
		if ( $import_form_data['delimiter'] ) {
			$delimiter = $import_form_data['delimiter'];
		} else {
			$delimiter = ',';
		}

		$sheet_data =
			$this->
			sheet_interplay_service->
			get_sheet_csv( $import_form_data['gswoo-file'], $import_form_data['gswoo-sheet-name'] );

		if ( is_wp_error( $sheet_data ) ) {
			return $sheet_data;
		}

		$resource = fopen( $file_sheet_path, 'w' );

		if ( ! $resource ) {
			return new WP_Error(
				'file_create_content_error',
				'(' . __METHOD__ . ') ' .
				sprintf(
				// translators: %s: file path.
					__(
						"We can't create import file, please check your server file write permissions to file: %s",
						'import-products-from-gsheet-for-woo-importer'
					),
					$file_sheet_path
				)
			);
		}

		$first_string = array_shift( $sheet_data );
		$is_success   = fputcsv( $resource, $first_string, $delimiter );

		if ( ! $first_string || ! $is_success ) {
			return new WP_Error(
				'file_create_content_error',
				'(' . __METHOD__ . ') ' .
				sprintf(
				// translators: %s: file path.
					__(
						"We can't insert data to your import file, please check if your spread sheet is not empty, and your your server has write permission to file: %s",
						'import-products-from-gsheet-for-woo-importer'
					),
					$file_sheet_path
				)
			);
		}

		foreach ( $sheet_data as $sheet_string ) {
			fputcsv( $resource, $sheet_string, $delimiter );
		}

		fclose( $resource );

		return true;
	}
}
