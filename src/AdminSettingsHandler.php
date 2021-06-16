<?php
/**
 * Plugin settings
 *
 * @since 2.0.0
 *
 * @package GSWOO
 */

namespace GSWOO;

use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Admin settings processing
 *
 * @since 2.0.0
 */
class AdminSettingsHandler {
	/**
	 * Initialize google API connection by user input
	 *
	 * @since 2.0.0
	 *
	 * @noinspection HtmlUnknownTarget
	 *
	 * @param array $options plugin options.
	 *
	 * @return array
	 */
	public function init_connection( $options ) {

		$response = $this->get_empty_connection_response();

		if ( empty( $options ) || empty( $options['google_auth_type'] ) ) {
			return $response;
		}

		switch ( $options['google_auth_type'] ) {
			case 'oauth2_tab':
				$response = $this->set_connection_oauth2_tab( $options );
				break;

			case 'api_key_tab':
				$response = $this->set_connection_api_key_tab( $options );
				break;
		}

		return $response;
	}

	/**
	 * Process connection by user input assets to google API with oauth2 method
	 * And output connection message base on process results.
	 *
	 * @since 2.0.0
	 *
	 * @noinspection HtmlUnknownTarget
	 *
	 * @param array $valid_input
	 *
	 * @return array
	 */
	public function set_connection_oauth2_tab( $valid_input ) {

		$response = $this->get_empty_connection_response();

		if ( $valid_input['google_code_oauth2_restore'] || empty( $valid_input['google_code_oauth2'] ) ) {
			$this->restore_google_oauth2_tab();
			return $response;
		}

		if ( ! $valid_input['google_code_oauth2'] ) {
			return $response;
		}

		$google_api_drive = new WrapperApiGoogleDrive();

		$token = $this->get_plugin_option_oauth2_token();

		if ( $token ) {
			$connection = $google_api_drive->connection_oauth2_method( $token );
		} else {
			$connection = $google_api_drive->connection_init_oauth2_method( $valid_input['google_code_oauth2'] );
		}

		if ( is_wp_error( $connection ) ) {
			$response['message'] = sprintf(
			// translators: %s: plugin import page url.
				__(
					"We can't receive connection by your provided access, please restore current code and try again, API response error message: '%s'",
					'import-products-from-gsheet-for-woo-importer'
				),
				$connection->errors['google_api_oauth2_method_error'][0]
			);

		} else {
			$response = $this->set_success_connection();

			// in case we still do not have sheet title chosen
			if ( ! $valid_input['google_sheet_title_oauth2'] ) {
				$response['message'] = esc_html__(
					'Now please choose google sheet title that we use for a import data.',
					'import-products-from-gsheet-for-woo-importer'
				);
			}
		}

		return $response;
	}

	/**
	 * Process connection by user input assets to google API with json api key method
	 * And output connection message base on process results.
	 *
	 * @since 2.0.0
	 *
	 * @noinspection HtmlUnknownTarget
	 *
	 * @param array $valid_input Options after input validation.
	 *
	 * @return array
	 */
	public function set_connection_api_key_tab( $valid_input ) {

		$response = $this->get_empty_connection_response();

		if ( empty( $valid_input['google_api_key'] ) || empty( 'google_sheet_title' ) ) {
			return $response;
		}

		if ( ! $this->put_key_to_file_access( $valid_input ) ) {
			$response['message'] = sprintf(
			// translators: %1$s: path to assets directory of file plugin.
				esc_html__(
					'Please check if plugin %1$s assets directory has write permission',
					'import-products-from-gsheet-for-woo-importer'
				),
				GSWOO_URI_ABSPATH
			);
		} else {
			$google_api_drive = new WrapperApiGoogleDrive();
			// fall in catch exception if error retrieve.
			$connection = $google_api_drive->connection_init_api_key_method( $valid_input['google_sheet_title'] );

			if ( is_wp_error( $connection ) ) {
				if ( empty( $connection->errors['google_api_key_method_error'][0] ) ) {
					$response['message'] = esc_html__(
						"We can't receive spreadsheet by your provided settings, please check settings and try it again",
						'import-products-from-gsheet-for-woo-importer'
					);
				} else {
					$response['message'] = sprintf(
					// translators: %s: error message.
						__(
							"We can't received spreadsheet by your provided settings, please check settings and try it again. Google API response error: '%s'",
							'import-products-from-gsheet-for-woo-importer'
						),
						$connection->errors['google_api_key_method_error'][0]
					);
				}
			} else {
				$response = $this->set_success_connection();
			}
		}

		return $response;
	}

	/**
	 * Remove oauth2 connection credentials.
	 *
	 * @since 2.0.0
	 */
	public function restore_google_oauth2_tab() {
		delete_option( 'plugin_wc_import_google_sheet_gs_token' );
	}

	/**
	 * Try to set file access
	 *
	 * @since 1.0.0
	 *
	 * @param array $valid_input Options after validation.
	 *
	 * @return bool
	 */
	public function put_key_to_file_access( $valid_input ) {

		try {
			$try_file_put = file_put_contents(
				GSWOO_URI_ABSPATH . 'assets/client_secret.json',
				$valid_input['google_api_key']
			);
		} catch ( Exception $e ) {
			return false;
		}

		if ( empty( $try_file_put ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get success or error message
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnusedParameterInspection
	 *
	 * @param $response
	 *
	 * @return false|string
	 */
	public function get_connection_message( $response ) {
		ob_start();

		include_once GSWOO_URI_ABSPATH
					 . '/src/Views/html-admin-settings-api-connection-message.php';

		return ob_get_clean();
	}

	/**
	 * Get plugin options
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_plugin_options() {
		$options = get_option( 'plugin_wc_import_google_sheet_options' );

		foreach ( $options as $options_name => $option_value ) {
			$options[ $options_name ] = wp_specialchars_decode( $option_value, ENT_QUOTES );
		}

		return $options;
	}

	/**
	 * Get plugin option oauth2 connection method token
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_plugin_option_oauth2_token() {
		return json_decode(
			get_option( 'plugin_wc_import_google_sheet_gs_token' ),
			true
		);
	}

	/**
	 * Return message after success access to google api by provided user credentials.
	 *
	 * @since 2.0.0
	 *
	 * @noinspection HtmlUnknownTarget
	 *
	 * @return array
	 */
	public function set_success_connection() {
		$menu_page_url = menu_page_url( 'product_importer_google_sheet', false );

		$response['is_success'] = true;

		$response['message'] = sprintf(
			// translators: %s: plugin import page url.
			__(
				'Your settings was received successfully, now you can go to <a href="%s">import products spread sheet page</a> and try import',
				'import-products-from-gsheet-for-woo-importer'
			),
			$menu_page_url
		);

		return $response;
	}

	/**
	 * Retrieve empty response to google API connection
	 *
	 * @return array
	 */
	public function get_empty_connection_response() {
		return array(
			'is_success' => false,
			'message'    => '',
		);
	}

	/**
	 * Trying to get google API connection with predefined plugin options.
	 *
	 * @since 2.0.0
	 *
	 * @param array $options plugin options.
	 *
	 * @return array
	 */
	public function get_api_connection_with_plugin_options( $options = array() ) {

		if ( ! $options ) {
			$options = $this->get_plugin_options();
		}

		return $this->init_connection( $options );
	}

	/**
	 * Check google API connection with current plugin options.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_api_connection_success_by_current_options() {

		$result = $this->get_api_connection_with_plugin_options();

		return $result['is_success'];
	}

	/**
	 * Get sheet title from plugin options.
	 *
	 * @param array $options
	 *
	 * @return string
	 */
	public function get_option_sheet_title( $options ) {
		$sheet_title = '';

		if ( empty( $options['google_sheet_title_oauth2'] ) && empty( $options['google_sheet_title'] ) ) {
			return $sheet_title;
		}

		switch ( $options['google_auth_type'] ) {
			case 'oauth2_tab':
				$sheet_title = json_decode( $options['google_sheet_title_oauth2'], true );
				$sheet_title = $sheet_title['title'];
				break;
			case 'api_key_tab':
				$sheet_title = $options['google_sheet_title'];
				break;
		}

		return $sheet_title;
	}
}
