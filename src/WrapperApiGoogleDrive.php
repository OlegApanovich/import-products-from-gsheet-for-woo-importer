<?php
/**
 * File contain Wrapper for a PHP Google APIs Client Library
 *
 * @since 1.0.0
 *
 * @package GSWOO
 */

namespace GSWOO;

defined( 'ABSPATH' ) || exit;

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\Exception\SpreadsheetNotFoundException;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\SpreadsheetService;
use Google_Client;

/**
 * Wrapper for a Google APIs Client Library for PHP
 *
 * @since 1.0.0
 */
class WrapperApiGoogleDrive {

	/**
	 * The instance of the SpreadsheetService.
	 *
	 * @var object SpreadsheetService
	 *
	 * @since  1.0.0
	 */
	public $spreadsheet;

	/**
	 * Constructor for GSWOO_Wrapper_Api_Google_Drive
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
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

		$access_token = $client->fetchAccessTokenWithAssertion()['access_token'];
		ServiceRequestFactory::setInstance(
			new DefaultServiceRequest( $access_token )
		);
	}

	/**
	 * Set working sheet
	 *
	 * @since 1.0.0
	 *
	 * @param string $sheet_title title os spreadsheet on a google drive.
	 *
	 * @return object
	 *
	 * @throws SpreadsheetNotFoundException Not fount spreadsheet.
	 */
	public function set_sheet( $sheet_title ) {
		$this->spreadsheet = ( new SpreadsheetService() )
			->getSpreadsheetFeed()
			->getByTitle( $sheet_title );

		return $this->spreadsheet;
	}

	/**
	 * Get working sheet content row by row.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_sheet_content() {
		$sheet_content = array();
		// Get the first worksheet (tab).
		$worksheets = $this->spreadsheet->getWorksheetFeed()->getEntries();
		$worksheet  = $worksheets[0];

		$list_feed = $worksheet->getListFeed();

		// @var ListEntry.
		foreach ( $list_feed->getEntries() as $entry ) {
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
		// Get the first worksheet (tab).
		$worksheets = $this->spreadsheet->getWorksheetFeed()->getEntries();
		$worksheet  = $worksheets[0];

		return $worksheet->getCsv();
	}
}
