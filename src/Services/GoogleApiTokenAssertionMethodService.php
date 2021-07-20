<?php
/**
 * Obtain google API token service with assertion method.
 *
 * @since 2.0.0
 *
 * @package GSWOO\Services
 */

namespace GSWOO\Services;

use Exception;
use GSWOO\Abstracts\GoogleApiTokenAbstract;
use WP_Error;

/**
 * Class GoogleApiTokenAssertionMethodService
 *
 * @since 2.0.0
 * @package GSWOO\Services
 */
class GoogleApiTokenAssertionMethodService extends GoogleApiTokenAbstract {

	/**
	 * Json assertion api key.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $google_api_key;

	/**
	 * GoogleApiTokenAssertionMethodService constructor.
	 *
	 * @since 2.0.0
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
			$this->client->setScopes(
				array(
					'https://www.googleapis.com/auth/drive',
					'https://spreadsheets.google.com/feeds',
				)
			);
		} catch ( Exception $e ) {
			$this->error = new WP_Error( 'token_error', '(' . __METHOD__ . ')' . $e->getMessage() );
		}
	}

	/**
	 * Get access token.
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
			$token       = '';
			$this->error = new WP_Error( 'token_error', '(' . __METHOD__ . ')' . $e->getMessage() );
		}

		return $token;
	}

	/**
	 * Try to put key to access file.
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
			$this->error = new WP_Error( 'token_error', '(' . __METHOD__ . ')' . $e->getMessage() );
		}

		if ( empty( $try_file_put ) ) {
			$this->error = new WP_Error(
				'token_error',
				'(' . __METHOD__ . ')' . __( 'Put file content error', 'import-products-from-gsheet-for-woo-importer' )
			);
		}

		return true;
	}
}
