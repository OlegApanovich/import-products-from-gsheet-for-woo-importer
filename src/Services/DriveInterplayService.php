<?php
/**
 * Service class help to interact with Google Drive API.
 *
 * @since 2.0.0
 *
 * @package GSWOO\Services
 */

namespace GSWOO\Services;

use Google\Service\Drive;
use GSWOO\Abstracts\GoogleApiInterplayAbstract;
use WP_Error;
use Exception;


/**
 * Class SheetInterplayService
 *
 * @since  2.0.0
 *
 * @package GSWOO\Services
 */
class DriveInterplayService extends GoogleApiInterplayAbstract {

	/**
	 * Instance of Google_Service_Drive class.
	 *
	 * @since  2.0.0
	 * @var object Google\Service\Drive\Google_Service_Drive
	 */
	public $google_service_drive;

	/**
	 * SheetInterplayService constructor.
	 *
	 * @since  2.0.0
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
			$this->google_service_drive = new Drive( $token_service->client );
		} catch ( Exception $e ) {
			$this->error = new WP_Error(
				'api_connect_error',
				'(' . __METHOD__ . ') ' . $e->getMessage()
			);
		}
	}

	/**
	 * Get data list of all sheets on a Google Drive.
	 *
	 * @since  2.0.0
	 *
	 * @return  array | object WP_Error
	 */
	public function get_google_drive_sheets_list() {
		if ( $this->error ) {
			return $this->error;
		}

		$sheets_list = array();

		try {
			$params  = array(
				'q' => "mimeType='application/vnd.google-apps.spreadsheet'",
			);
			$results = $this->google_service_drive->files->listFiles( $params );
			foreach ( $results->files as $spreadsheet ) {
				if ( isset( $spreadsheet['kind'] ) && 'drive#file' === $spreadsheet['kind'] ) {
					$sheets_list[] = array(
						'id'    => $spreadsheet['id'],
						'title' => $spreadsheet['name'],
					);
				}
			}
		} catch ( Exception $e ) {
			return new WP_Error(
				'get_sheet_error',
				'(' . __METHOD__ . ') ' . $e->getMessage()
			);
		}

		return $sheets_list;
	}
}
