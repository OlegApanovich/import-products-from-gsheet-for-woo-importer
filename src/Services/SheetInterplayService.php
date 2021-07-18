<?php

namespace GSWOO\Services;

use Exception;
use Google\Spreadsheet\SpreadsheetService;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use WP_Error;


/**
 * Class SheetInterplayService
 *
 * @since  2.0.0
 *
 * @package GSWOO\Services
 */
class SheetInterplayService {

	/**
	 * Google sheet service instance.
	 *
	 * @var object SpreadsheetFeed
	 *
	 * @since  2.0.0
	 */
	public $spread_sheet_feed;

	/**
	 * @var object Wp_Error
	 *
	 * @since 2.0.0
	 */
	public $error;

	/**
	 * Try to bind library with google api.
	 *
	 * @since  2.0.0
	 *
	 * @param string                         $token
	 * @param  bool|object AdminSettingsModel $model
	 */
	public function set_api_connect( $token = '', $model = false ) {
		if ( ! $token && $model ) {
			$token_service = $model->get_token_service();
			if ( is_wp_error( $token_service->error ) ) {
				$this->error = $token_service->error;
				return $this;
			}

			$token = $token_service->token;
		}

		try {
			$token          = $this->get_access_token_from_json_data( $token );
			$serviceRequest = new DefaultServiceRequest( $token );
			ServiceRequestFactory::setInstance( $serviceRequest );
			$spread_sheet_service    = new SpreadsheetService();
			$this->spread_sheet_feed = $spread_sheet_service->getSpreadsheetFeed();
		} catch ( Exception $e ) {
			$this->error = new WP_Error(
				'api_connect_error',
				'(' . __METHOD__ . ') ' . $e->getMessage()
			);
		}

		return $this;
	}

	/**
	 * Get data list of all sheets on a google drive.
	 *
	 * @since  2.0.0
	 *
	 * @return  array | object WP_Error
	 */
	function get_google_drive_sheets_list() {
		if ( $this->error ) {
			return $this->error;
		}

		try {
			$sheets_list = array();

			$sheet_entries_list = $this->spread_sheet_feed->getEntries();

			for ( $i = 0; $i < count( $sheet_entries_list ); $i++ ) {
				$sheets_list[ $i ]['id']    = $sheet_entries_list[ $i ]->getId();
				$sheets_list[ $i ]['title'] = $sheet_entries_list[ $i ]->getTitle();
			}
		} catch ( Exception $e ) {
			$this->error = new WP_Error(
				'get_sheet_error',
				'(' . __METHOD__ . ') ' . $e->getMessage()
			);
		}

		return $sheets_list;
	}

	/**
	 * Get sheet csv data.
	 *
	 * @since  2.0.0
	 *
	 * @noinspection PhpReturnDocTypeMismatchInspection
	 * @noinspection PhpUndefinedVariableInspection*
	 *
	 * @return string|WP_Error
	 */
	function get_sheet_csv( $sheet_id ) {
		if ( $this->error ) {
			return $this->error;
		}

		$sheet_csv = '';
		try {
			$spreadsheet = $this->spread_sheet_feed->getById( $sheet_id );

			$worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
			$worksheet  = $worksheets[0];

			$sheet_csv = $worksheet->getCsv();

		} catch ( Exception $e ) {
			$this->error = new WP_Error(
				'get_sheet_content_error',
				'(' . __METHOD__ . ') ' . $e->getMessage()
			);
		}

		return $sheet_csv;
	}

	/**
	 * Try to pick access token from response token data.
	 *
	 * @param string $token Json response data
	 *
	 * @throws Exception
	 * @return string
	 */
	public function get_access_token_from_json_data( $token ) {
		$token = json_decode( $token );
		if ( empty( $token->access_token ) ) {
			throw new Exception(
				__(
					'Invalid token access data json format',
					'import-products-from-gsheet-for-woo-importer'
				)
			);
		}

		return $token->access_token;
	}
}
