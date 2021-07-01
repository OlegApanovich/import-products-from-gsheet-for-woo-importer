<?php

namespace GSWOO\Services;

use Exception;
use Google\Spreadsheet\SpreadsheetService;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;


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
	 * @param string $token
	 */
	public function set_api_connect( $token = '' ) {
		try {
			$serviceRequest = new DefaultServiceRequest( $token );
			ServiceRequestFactory::setInstance( $serviceRequest );
			$spread_sheet_service    = new SpreadsheetService();
			$this->spread_sheet_feed = $spread_sheet_service->getSpreadsheetFeed();
		} catch ( Exception $e ) {
			$this->error = true;
		}

		return $this;
	}

	/**
	 * Get data list of all sheets on a google drive.
	 *
	 * @since  2.0.0
	 * @return self | array
	 */
	function get_google_drive_sheets_list() {
		if ( $this->error ) {
			return $this;
		}

		try {

			$sheets_list = array();

			$sheet_entries_list = $this->spread_sheet_feed->getEntries();

			for ( $i = 0; $i < count( $sheet_entries_list ); $i++ ) {
				$sheets_list[ $i ]['id']    = $sheet_entries_list[ $i ]->getId();
				$sheets_list[ $i ]['title'] = $sheet_entries_list[ $i ]->getTitle();
			}
		} catch ( Exception $e ) {
			$this->error = true;
		}

		return $sheets_list;
	}

	/**
	 * Get sheet csv data.
	 *
	 * @since  2.0.0
	 * @return self | string
	 */
	function get_sheet_csv( $sheet_id ) {
		if ( $this->error ) {
			return $this;
		}

		try {
			$spreadsheet = $this->spread_sheet_feed->getById( $sheet_id );

			$worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
			$worksheet  = $worksheets[0];
		} catch ( Exception $e ) {
			$this->error = true;
		}

		return $worksheet->getCsv();
	}
}
