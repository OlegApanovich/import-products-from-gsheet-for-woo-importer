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

use Exception;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\Exception\SpreadsheetNotFoundException;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\SpreadsheetService;
use Google_Client;
use DomainException;
use Google_Service_Drive;
use Google_Service_Sheets;
use WP_Error;

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
	 * App ID.
	 *
	 * @var string
	 *
	 * @since  2.0.0
	 */
	const oauth2_id = '836707027943-am8hdf20f7r5bi48f0r5pta545p7k7l2.apps.googleusercontent.com';

	/**
	 * App secret.
	 *
	 * @var string
	 *
	 * @since  2.0.0
	 */
	const oauth2_secret = 'UQtT-ty55dLXCjetDdM6tOts';

	/**
	 * App redirect.
	 *
	 * @var string
	 *
	 * @since  2.0.0
	 */
	const oauth2_redirect = 'urn:ietf:wg:oauth:2.0:oob';

	/**
	 * Initialize google API connection with json api key method.
	 *
	 * @since 2.0.0
	 *
	 * @var string $sheet_title
	 *
	 * @throws DomainException
	 *
	 * @retrun object Google\Client | Wp_Error
	 */
	public function connection_init_api_key_method( $sheet_title ) {
		try {
			putenv(
				'GOOGLE_APPLICATION_CREDENTIALS=' . GSWOO_URI_ABSPATH
				. 'assets/client_secret.json'
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

			$access_token = $client->fetchAccessTokenWithAssertion()['access_token'];
			ServiceRequestFactory::setInstance(
				new DefaultServiceRequest( $access_token )
			);
			// fall in catch exception if error retrieve.
			$this->set_sheet( $sheet_title );
		} catch ( Exception $e ) {
			$client = new WP_Error( 'google_api_key_method_error', $e->getMessage() );
		}

		return $client;
	}

	/**
	 * Initialize google API connection with oauth2 method.
	 *
	 * @param string $google_code_oauth2
	 *
	 * @since 2.0.0
	 *
	 * @retrun object Google\Client | Wp_Error
	 */
	public function connection_init_oauth2_method( $google_code_oauth2 ) {
		try {
			$client = new Google_Client();
			$client->setClientId( self::oauth2_id );
			$client->setClientSecret( self::oauth2_secret );
			$client->setRedirectUri( self::oauth2_redirect );
			$client->setScopes( Google_Service_Sheets::SPREADSHEETS );
			$client->setScopes( Google_Service_Drive::DRIVE_METADATA_READONLY );
			$client->setAccessType( 'offline' );
			$client->fetchAccessTokenWithAuthCode( $google_code_oauth2 );
			$token = $client->getAccessToken();

			if ( ! $token || empty( $token['access_token'] ) ) {
				$client = new WP_Error(
					'google_api_oauth2_error',
					__( 'empty token', 'import-products-from-gsheet-for-woo-importer' )
				);
			}

			$this->update_token( $token );
		} catch ( Exception $e ) {
			$client = new WP_Error( 'google_api_oauth2_method_error', $e->getMessage() );
		}

		return $client;
	}

	/**
	 * Set google API connection with oauth2 method.
	 *
	 * @param array $token
	 *
	 * @since 2.0.0
	 *
	 * @retrun object Google\Client | Wp_Error
	 */
	public function connection_oauth2_method( $token ) {
		try {
			$client = new Google_Client();
			$client->setClientId( self::oauth2_id );
			$client->setClientSecret( self::oauth2_secret );
			$client->setScopes( Google_Service_Sheets::SPREADSHEETS );
			$client->setScopes( Google_Service_Drive::DRIVE_METADATA_READONLY );

			$client->refreshToken( $token['refresh_token'] );
			$client->setAccessType( 'offline' );
			self::update_token( $token );
		} catch ( Exception $e ) {
			$client = new WP_Error( 'google_api_oauth2_error', $e->getMessage() );
		}

		return $client;
	}

	/**
	 * Update oauth2 token data
	 *
	 * @param $token
	 */
	public function update_token( $token ) {
		$token['expire'] = time() + intval( $token['expires_in'] );

		$tokenJson = json_encode( $token );

		update_option( 'plugin_wc_import_google_sheet_gs_token', $tokenJson );
	}

	/**
	 * Set working sheet
	 *
	 * @since 1.0.0
	 *
	 * @param string $sheet_title title spreadsheet on a google drive.
	 *
	 * @throws SpreadsheetNotFoundException Not fount spreadsheet.
	 *
	 * @return object
	 */
	public function set_sheet( $sheet_title ) {
		$this->spreadsheet = ( new SpreadsheetService() )
			->getSpreadsheetFeed()
			->getByTitle( $sheet_title );

		return $this->spreadsheet;
	}

	/**
	 * Get csv worksheet data
	 *
	 * @param string $file_name Google sheet file name.
	 *
	 * @return string
	 * @throws SpreadsheetNotFoundException
	 * @since 1.0.0
	 */
	public function get_sheet_csv( $file_name ) {
		// Get the first worksheet (tab).
		$this->set_sheet( $file_name );
		$worksheets = $this->spreadsheet->getWorksheetFeed()->getEntries();
		$worksheet  = $worksheets[0];

		return $worksheet->getCsv();
	}
}
