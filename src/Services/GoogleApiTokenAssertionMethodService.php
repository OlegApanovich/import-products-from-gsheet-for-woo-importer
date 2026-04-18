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
			$this->client->setAuthConfig( json_decode( $this->google_api_key, true ) );
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
			$token = $this->client->fetchAccessTokenWithAssertion();
		} catch ( Exception $e ) {
			$token       = '';
			$this->error = new WP_Error( 'token_error', '(' . __METHOD__ . ')' . $e->getMessage() );
		}

		return $token;
	}
}
