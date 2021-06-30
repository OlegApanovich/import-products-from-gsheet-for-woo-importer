<?php

namespace GSWOO\Services;

use Exception;
use GSWOO\Abstracts\GoogleApiTokenAbstract;
use Google_Service_Sheets;
use Google_Service_Drive;
use WP_Error;

class GoogleApiTokenAuthCodeMethodService extends GoogleApiTokenAbstract {

	/**
	 * App ID.
	 *
	 * @var string
	 *
	 * @since  2.0.0
	 */
	const OAUTH2_ID = '836707027943-am8hdf20f7r5bi48f0r5pta545p7k7l2.apps.googleusercontent.com';

	/**
	 * App secret.
	 *
	 * @var string
	 *
	 * @since  2.0.0
	 */
	const OAUTH2_SECRET = 'UQtT-ty55dLXCjetDdM6tOts';

	/**
	 * App redirect.
	 *
	 * @var string
	 *
	 * @since  2.0.0
	 */
	const OAUTH2_REDIRECT = 'urn:ietf:wg:oauth:2.0:oob';

	/**
	 * Google API auth code.
	 *
	 * @var string
	 *
	 * @since  2.0.0
	 */
	public $google_code;

	/**
	 * GoogleApiTokenAuthCodeMethodService constructor.
	 *
	 * @param string $google_code
	 */
	public function __construct( $google_code ) {
		$this->google_code = $google_code;

		parent::__construct();
	}

	/**
	 * Set google API client.
	 *
	 * @since 2.0.0
	 */
	public function set_client() {
		try {
			$this->client->setClientId( self::OAUTH2_ID );
			$this->client->setClientSecret( self::OAUTH2_SECRET );
			$this->client->setRedirectUri( self::OAUTH2_REDIRECT );
			$this->client->setScopes( Google_Service_Sheets::SPREADSHEETS );
			$this->client->setScopes( Google_Service_Drive::DRIVE_METADATA_READONLY );
			$this->client->setAccessType( 'offline' );
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
			$this->client->fetchAccessTokenWithAuthCode( $this->google_code );
			$token = $this->client->getAccessToken();
		} catch ( Exception $e ) {
			$this->token_error = new WP_Error( 'token_error', '(' . __METHOD__ . ')' . $e->getMessage() );
			$token             = '';
		}

		return $token;
	}
}
