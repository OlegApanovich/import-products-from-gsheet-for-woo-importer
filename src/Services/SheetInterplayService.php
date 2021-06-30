<?php

namespace GSWOO\Services;

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\SpreadsheetService;

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
	 * @var object SpreadsheetService
	 *
	 * @since  2.0.0
	 */
	public $spread_sheet_feed;

	/**
	 * SheetInterplayService constructor.
	 *
	 * @since  2.0.0
	 */
	public function __construct( $token ) {
		$serviceRequest = new DefaultServiceRequest( $token );
		ServiceRequestFactory::setInstance( $serviceRequest );
		$spread_sheet_service    = new SpreadsheetService();
		$this->spread_sheet_feed = $spread_sheet_service->getSpreadsheetFeed();
	}

	/**
	 * Get data list of all sheets on a google drive.
	 *
	 * @return array
	 */
	function get_google_drive_sheets_list() {
		$sheets_list = array();

		$sheet_entries_list = $this->spread_sheet_feed->getEntries();

		for ( $i = 0; $i < count( $sheet_entries_list ); $i++ ) {
			$sheets_list[ $i ]['id']    = $sheet_entries_list[ $i ]->getId();
			$sheets_list[ $i ]['title'] = $sheet_entries_list[ $i ]->getTitle();
		}

		return $sheets_list;
	}

	function get_sheet_csv( $sheet_id ) {
		$spreadsheet = $this->spread_sheet_feed->getById( $sheet_id );

		$worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
		$worksheet  = $worksheets[0];

		return $worksheet->getCsv();
	}
}
