<?php
defined( 'ABSPATH' ) || exit;

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

/**
 * Wrapper for a Google APIs Client Library for PHP
 *
 * @since 1.0.0
 */
class GSWOO_Wrapper_Api_Google_Drive {

	/**
	 * The instance of the SpreadsheetService
	 *
	 * @since  1.0.0
	 */
	public $spreadsheet;

	/**
	 * Construcotr for GSWOO_Wrapper_Api_Google_Drive
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		putenv(
			'GOOGLE_APPLICATION_CREDENTIALS=' . GSWOO_URI_ABSPATH
				. '/assets/client_secret.json'
		);
		$client = new Google_Client();
		$client->useApplicationDefaultCredentials();

		$client->setApplicationName( 'Something to do with my representatives' );
		$client->setScopes(
			array(
				'https://www.googleapis.com/auth/drive',
				'https://spreadsheets.google.com/feeds',
			)
		);

		if ( $client->isAccessTokenExpired() ) {
			$client->refreshTokenWithAssertion();
		}

		$accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
		ServiceRequestFactory::setInstance(
			new DefaultServiceRequest( $accessToken )
		);
	}

	/**
	 * Set working sheet
	 *
	 * @since 1.0.0
	 *
	 * @param string $sheet_title
	 *
	 * @return object
	 */
	public function set_sheet( $sheet_title ) {
		$this->spreadsheet = ( new Google\Spreadsheet\SpreadsheetService() )
			->getSpreadsheetFeed()
			->getByTitle( $sheet_title );

		return $this->spreadsheet;
	}

	/**
	 * Get working sheet content row by row
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_sheet_content() {
		$sheet_content = array();
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
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_sheet_csv() {
		$sheet_content = array();
		// Get the first worksheet (tab)
		$worksheets = $this->spreadsheet->getWorksheetFeed()->getEntries();
		$worksheet  = $worksheets[0];

		$csv = $worksheet->getCsv();

		return $csv;
	}
}
