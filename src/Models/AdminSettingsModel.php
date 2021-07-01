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

use GSWOO\Services\GoogleApiTokenAssertionMethodService;
use GSWOO\Services\GoogleApiTokenAuthCodeMethodService;
use GSWOO\Abstracts\GoogleApiTokenAbstract;

defined( 'ABSPATH' ) || exit;

/**
 * Admin settings controller
 *
 * @since 2.0.0
 */
class AdminSettingsModel {

	/**
	 * Get currently activated Google API Connect Method options tab
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_active_auth_tab() {
		if ( isset( $_GET['auth_tab'] ) ) {
			$active_tab = $_GET['auth_tab'];
		} else {
			$options = $this->get_plugin_options();

			if ( empty( $options['google_auth_type'] ) ) {
				$active_tab = 'auth_code_method_tab';
			} else {
				$active_tab = $options['google_auth_type'];
			}
		}

		return $active_tab;
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

		// if ( is_array( $options ) ) {
		// foreach ( $options as $options_name => $option_value ) {
		// $options[ $options_name ] = wp_specialchars_decode( $option_value, ENT_QUOTES );
		// }
		// }
		//
		// we must predefined some options even if we do not have it in db
		// $predefined_option_list = array(
		// 'google_api_key',
		// 'google_code_oauth2',
		// 'google_auth_type' => '',
		// );

		// foreach ( $predefined_option_list as $predefined_option_name => $predefined_option_value ) {
		//
		// if ( empty( $predefined_option_value ) ) {
		// $predefined_option_value = '';
		// }
		//
		// if ( empty( $options[ $predefined_option_name ] ) ) {
		// $options[ $predefined_option_name ] = $predefined_option_value;
		// }
		// }

		ob_start();
		var_dump( $options );
		$imp_to_file = ob_get_clean();
		file_put_contents( '/var/www/html/test.html', $imp_to_file, FILE_APPEND );

		return $options;
	}

	/**
	 * Process connection to google API.
	 *
	 * @since 2.0.0
	 *
	 * @var array $options
	 *
	 * @return array (
	 *      'status'  => [error, warning, success]
	 *      'message' => text,
	 *      'token'   => string
	 * )
	 */
	public function process_connection( $options = array() ) {

		$response = array();

		if ( ! $options ) {
			$options = $this->get_plugin_options();
		}

		if ( $this->is_empty_response( $options ) ) {
			return $response;
		}

		$token_service = $this->get_token_service( $options );

		if ( is_wp_error( $token_service->token_error ) ) {
			$error = reset( $token_service->token_error->errors );
			return $this->get_error_connection_message( $error[0] );
		}

		return $this->get_success_connection_message( $token_service );
	}

	/**
	 * Initialize google API token service.
	 *
	 * @param $options
	 *
	 * @noinspection PhpUndefinedVariableInspection
	 *
	 * @return GoogleApiTokenAbstract
	 */
	public function get_token_service( $options ) {
		switch ( $options['google_auth_type'] ) {
			case 'assertion_method_tab':
				$token_service = new GoogleApiTokenAssertionMethodService( $options['google_api_key'] );
				break;
			case 'auth_code_method_tab':
				$token_service = new GoogleApiTokenAuthCodeMethodService( $options['google_code_oauth2'] );
				break;
		}

		return $token_service;
	}

	/**
	 * Check if user connection message is empty.
	 *
	 * @since 2.0.0
	 *
	 * @param array $options
	 *
	 * @return bool
	 */
	public function is_empty_response( $options ) {

		$is_empty = false;
		if ( empty( $options['google_auth_type'] ) ) {
			$is_empty = true;
		} else {
			switch ( $options['google_auth_type'] ) {
				case 'assertion_method_tab':
					if ( ! $options['google_sheet_title'] && ! $options['google_api_key'] ) {
						$is_empty = true;
					}
					break;
				case 'auth_code_method_tab':
					if ( ! empty( $options['google_code_oauth2_restore'] ) ) {
						$this->delete_options();
						$is_empty = true;
					}

					if ( empty( $options['google_code_oauth2'] ) ) {
						$is_empty = true;
					}
					break;
			}
		}

		return $is_empty;
	}

	/**
	 * Return message after error access to google api by provided user credentials.
	 *
	 * @since 2.0.0
	 *
	 * @var string $error
	 *
	 * @return array
	 */
	public function get_error_connection_message( $error ) {
		$return['status'] = 'error';

		$return['message'] = sprintf(
		// translators: %s: plugin import page url.
			__(
				"We can't set connection with google API by your provided credentials. Error: \"%s\"",
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
	 * @param object $token_service GoogleApiTokenAbstract
	 *
	 * @return array
	 */
	public function get_success_connection_message( $token_service ) {
		$menu_page_url = menu_page_url( 'product_importer_google_sheet', false );

		$token = json_decode( $token_service->token );
		$token = $token->access_token;

		$return['token'] = $token;

		$return['status'] = 'success';

		$return['message'] = sprintf(
		// translators: %s: plugin import page url.
			__(
				'Your settings was received successfully, now you can go to <a href="%s">import products spread sheet page</a> and try import',
				'import-products-from-gsheet-for-woo-importer'
			),
			$menu_page_url
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
	 * Check if processing google api request response has error.
	 *
	 * @param array $response
	 *
	 * @return bool
	 */
	public function is_response_error( $response ) {
		return ! empty( $response['status'] ) && 'error' == $response['status'];
	}
}
