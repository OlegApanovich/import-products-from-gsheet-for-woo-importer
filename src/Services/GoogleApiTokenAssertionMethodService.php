<?php

namespace GSWOO\Services;

use Exception;
use Google_Service_Drive;
use Google_Service_Sheets;
use GSWOO\Abstracts\GoogleApiTokenAbstract;
use WP_Error;

class GoogleApiTokenAssertionMethodService extends GoogleApiTokenAbstract {

	/**
	 * Json assertion api key.
	 *
	 * @var string $valid_input
	 */
	public $google_api_key;

	/**
	 * GoogleApiTokenAssertionMethodService constructor.
	 *
	 * @param string $google_api_key
	 */
	public function __construct( $google_api_key ) {
		$this->google_api_key = $google_api_key;
		parent::__construct();
	}

	/**
	 * Set google API client.
	 *
	 * @since 2.0.0
	 */
	public function set_client() {
		try {

			$this->client->useApplicationDefaultCredentials();
			$this->client->setApplicationName( 'Something to do with my representatives' );
			$this->client->setScopes( Google_Service_Sheets::SPREADSHEETS );
			$this->client->setScopes( Google_Service_Drive::DRIVE_METADATA_READONLY );
		} catch ( Exception $e ) {
			$this->token_error = new WP_Error( 'token_error', '(' . __METHOD__ . ')' . $e->getMessage() );
		}
	}

	/**
	 * Get access token
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function fetch_token() {
		try {
			$this->put_key_to_file_access();

			putenv(
				'GOOGLE_APPLICATION_CREDENTIALS=' . GSWOO_URI_ABSPATH . 'assets/client_secret.json'
			);

			$token = $this->client->fetchAccessTokenWithAssertion();
		} catch ( Exception $e ) {
			$token             = '';
			$this->token_error = new WP_Error( 'token_error', '(' . __METHOD__ . ')' . $e->getMessage() );
		}

		return $token;
	}

	/**
	 * Try to set file access
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function put_key_to_file_access() {

		try {
			$try_file_put = file_put_contents(
				GSWOO_URI_ABSPATH . 'assets/client_secret.json',
				$this->google_api_key
			);
		} catch ( Exception $e ) {
			$this->token_error = new WP_Error( 'token_error', '(' . __METHOD__ . ')' . $e->getMessage() );
		}

		if ( empty( $try_file_put ) ) {
			$this->token_error = new WP_Error(
				'token_error',
				'(' . __METHOD__ . ')' . __( 'Put file content error', 'import-products-from-gsheet-for-woo-importer' ),
			);
		}

		return true;
	}
}
