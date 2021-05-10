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
 * @since 1.0.0
 */
class AdminSettingsHandler {
	/**
	 * Try to check user inputs and set error message if input is not valid
	 *
	 * @since 1.0.0
	 *
	 * @param array $user_input Options after input validation.
	 *
	 * @return bool
	 */
	public function check_user_input( $user_input ) {
		if ( $this->put_key_to_file_access( $user_input ) ) {
			try {
				$google_api_obj = new WrapperApiGoogleDrive();
				try {
					$google_api_obj->set_sheet( $user_input['google_sheet_title'] );

					$check = true;
				} catch ( Exception $e ) {
					$check = false;
				}
			} catch ( Exception $e ) {
				$check = false;
			}
		} else {
			$check = false;
		}

		return $check;
	}

	/**
	 * Retrieve connection message by user input
	 *
	 * @since 1.0.0
	 *
	 * @noinspection HtmlUnknownTarget
	 *
	 * @param array $valid_input Options after input validation.
	 *
	 * @return string
	 */
	public function get_connection_message( $valid_input ) {
		$message = '';

		if ( empty( $valid_input ) ) {
			return $message;
		}

		if ( $this->put_key_to_file_access( $valid_input ) ) {
			try {
				$google_api_obj = new WrapperApiGoogleDrive();
				// fall in catch exception if error retrieve.
				$google_api_obj->set_sheet( $valid_input['google_sheet_title'] );
				$menu_page_url = menu_page_url( 'product_importer_google_sheet', false );

				$message = sprintf(
					// translators: %s: plugin import page url.
					__(
						'Your settings was received successfully, now you can go to <a href="%s">import products spread sheet page</a> and try import',
						'import-products-from-gsheet-for-woo-importer'
					),
					$menu_page_url
				);
			} catch ( Exception $e ) {
				if ( empty( $e->getMessage() ) ) {
					$message = esc_html__(
						"We can't receive spreadsheet by your provided settings, please check settings and try it again",
						'import-products-from-gsheet-for-woo-importer'
					);
				} else {
					$message = sprintf(
						// translators: %s: error message.
						__(
							"We can't received spreadsheet by your provided settings, please check settings and try it again. Google API response error: '%s'",
							'import-products-from-gsheet-for-woo-importer'
						),
						$e->getMessage()
					);
				}
			}
		} else {
			$message = sprintf(
				// translators: %1$s: path to assets directory of file plugin.
				esc_html__(
					'Please check if plugin %1$s assets directory has write permission',
					'import-products-from-gsheet-for-woo-importer'
				),
				GSWOO_URI_ABSPATH
			);
		}

		return $message;
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

		if ( empty( $valid_input ) ) {
			return false;
		}

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
	 * After try establish connection to google drive api
	 * try to display user success or error message
	 *
	 * @since 1.0.0
	 * @noinspection PhpUnused
	 */
	public function set_connection_message() {
		$options = $this->get_plugin_options();

		$connection_message = $this->get_connection_message( $options );
		// Input already validated through settings API.
		$check = $this->check_user_input( $options );

		if ( $check ) {
			echo '<h3 style="color:green">' . wp_kses( $connection_message, array( 'a' => array( 'href' => array() ) ) ) . '</h3>';
		} else {
			echo '<h3 style="color:red">' . wp_kses( $connection_message, array( 'a' => array( 'href' => array() ) ) ) . '</h3>';
		}
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

		if ( ! empty( $options['google_sheet_title'] ) && ! empty( $options['google_api_key'] ) ) {
			$options['google_sheet_title'] = wp_specialchars_decode( $options['google_sheet_title'], ENT_QUOTES );
			$options['google_api_key']     = wp_specialchars_decode( $options['google_api_key'], ENT_QUOTES );
		}

		return $options;
	}
}
