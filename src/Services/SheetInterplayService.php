<?php
/**
 * Service class help to interact with Google sheet API.
 *
 * @since 2.0.0
 *
 * @package GSWOO\Services
 */

namespace GSWOO\Services;

use GSWOO\Abstracts\GoogleApiInterplayAbstract;
use Google_Service_Sheets;
use WP_Error;
use Exception;

/**
 * Class SheetInterplayService
 *
 * @since  2.0.0
 *
 * @package GSWOO\Services
 */
class SheetInterplayService extends GoogleApiInterplayAbstract {

	/**
	 * Instance of Google_Service_Sheets class.
	 *
	 * @since  2.0.0
	 * @var object Google\Service\Sheets\Google_Service_Sheets
	 */
	public $google_service_sheets;

	/**
	 * SheetInterplayService constructor.
	 *
	 * @param array $options
	 */
	public function __construct( $options ) {

		if ( empty( $options['google_auth_type'] ) ) {
			return;
		}

		$this->options = $options;
		$token_service = $this->get_token_service();

		try {
			$this->google_service_sheets = new Google_Service_Sheets( $token_service->client );
		} catch ( Exception $e ) {
			$this->error = new WP_Error(
				'api_connect_error',
				'(' . __METHOD__ . ') ' . $e->getMessage()
			);
		}
	}

	/**
	 * Get sheet csv data.
	 *
	 * @since  2.0.0
	 *
	 * @noinspection PhpUndefinedVariableInspection*
	 *
	 * @param string $sheet_id
	 *
	 * @return WP_Error object|array
	 */
	public function get_sheet_csv( $sheet_id ) {
		if ( $this->error ) {
			return $this->error;
		}

		try {
			$spreadsheet = $this->google_service_sheets->spreadsheets->get( $sheet_id );

			$sheet_name = $spreadsheet[0]->properties->title;

			$sheet =
				$this->google_service_sheets->
				spreadsheets_values->get( $sheet_id, $sheet_name );

		} catch ( Exception $e ) {
			return new WP_Error(
				'get_sheet_csv',
				'(' . __METHOD__ . ') ' . $e->getMessage()
			);
		}

		if ( empty( $sheet->values ) ) {
			return new WP_Error(
				'get_sheet_csv',
				__(
					"We can't receive any data from your google sheet, please check if your spread sheet is not empty",
					'import-products-from-gsheet-for-woo-importer'
				)
			);
		} else {
			return $sheet->values;
		}
	}
}
