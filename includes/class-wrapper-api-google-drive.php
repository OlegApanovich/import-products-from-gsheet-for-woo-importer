<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

/**
 * Api google drive
 */
class Wrapper_Api_Google_Drive {

	public $spreadsheet;

	function __construct() {
		putenv( 'GOOGLE_APPLICATION_CREDENTIALS=' . WC_IMPORT_SHEET_URI_ABSPATH
		        . '/assets/client_secret.json' );
		$client = new Google_Client;
		$client->useApplicationDefaultCredentials();

		$client->setApplicationName( "Something to do with my representatives" );
		$client->setScopes( [
			'https://www.googleapis.com/auth/drive',
			'https://spreadsheets.google.com/feeds'
		] );

		if ( $client->isAccessTokenExpired() ) {
			$client->refreshTokenWithAssertion();
		}

		$accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];
		ServiceRequestFactory::setInstance(
			new DefaultServiceRequest( $accessToken )
		);
	}

	/**
	 * Set working sheet
	 *
	 * @param string $sheet_title
	 *
	 * @return
	 */
	public function set_sheet( $sheet_title ) {
		$this->spreadsheet = ( new Google\Spreadsheet\SpreadsheetService )
			->getSpreadsheetFeed()
			->getByTitle( $sheet_title );

		return $this->spreadsheet;
	}

	/**
	 * Get working sheet content row by row
	 *
	 * @return array $sheet_content
	 */
	public function get_sheet_content() {
		$sheet_content = [];
		// Get the first worksheet (tab)
		$worksheets = $this->spreadsheet->getWorksheetFeed()->getEntries();
		$worksheet  = $worksheets[0];

		$listFeed = $worksheet->getListFeed();

		/** @var ListEntry */
		foreach ( $listFeed->getEntries() as $entry ) {
			$sheet_content[] = $entry->getValues();
		}

		return $sheet_content;
	}

	/**
	 * Get csv data of this worksheet
	 *
	 * @return string $csv
	 */
	public function get_sheet_csv() {
		$sheet_content = [];
		// Get the first worksheet (tab)
		$worksheets = $this->spreadsheet->getWorksheetFeed()->getEntries();
		$worksheet  = $worksheets[0];

		$csv = $worksheet->getCsv();

		return $csv;
	}
}