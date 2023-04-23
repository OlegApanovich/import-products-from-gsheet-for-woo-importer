<?php
/**
 * Obtain google API token service with auth code method.
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
 * Class GoogleApiTokenAuthCodeMethodService
 *
 * @since 2.0.0
 * @package GSWOO\Services
 */
class GoogleApiTokenAuthCodeMethodService extends GoogleApiTokenAbstract {

	/**
	 * App ID.
	 *
	 * @var string
	 *
	 * @since  2.0.0
	 */
	const OAUTH2_ID = '836707027943-7cdti4g8vtkt0fngg5cvjmp01fg2iksp.apps.googleusercontent.com';

	/**
	 * App secret.
	 *
	 * @var string
	 *
	 * @since  2.0.0
	 */
	const OAUTH2_SECRET = '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';

	/**
	 * App redirect.
	 *
	 * @var string
	 *
	 * @since  2.0.0
	 */
	const OAUTH2_REDIRECT = 'https://monolitpro.info?plugin=import-products-from-gsheet-for-woo-importer&action=oauth';

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
	 * @since  2.0.0
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
			$this->client->setScopes( array( 'https://www.googleapis.com/auth/drive.readonly' ) );
		} catch ( Exception $e ) {
			$this->error = new WP_Error( 'token_error', '(' . __METHOD__ . ')' . $e->getMessage() );
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
            $token['expire'] = time() + $token['expires_in'];
		} catch ( Exception $e ) {
			$this->error = new WP_Error( 'token_error', '(' . __METHOD__ . ')' . $e->getMessage() );
			$token       = '';
		}

		return $token;
	}
}
