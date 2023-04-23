<?php
/**
 * Google Api Token Abstracts
 *
 * @since 2.0.0
 *
 * @package GSWOO
 */

namespace GSWOO\Abstracts;

use Google_Client;
use WP_Error;

/**
 * Class GoogleApiTokenAbstract
 *
 * @since 2.0.0
 * @package GSWOO\Abstracts
 */
abstract class GoogleApiTokenAbstract {

	/**
	 * Google_Client instance.
	 *
	 * @var object Google_Client
	 *
	 * @since 2.0.0
	 */
	public $client;

	/**
	 * Google API access token.
	 *
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public $token;

	/**
	 * Error aggregator.
	 *
	 * @var object Wp_Error
	 *
	 * @since 2.0.0
	 */
	public $error;

	/**
	 * GoogleApiTokenAbstract constructor.
	 */
	public function __construct() {
		$this->client = new Google_Client();

		$this->init_token();
	}

	/**
	 * Get previously saved token from wp options.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_token() {
		return get_option( 'plugin_wc_import_google_sheet_gs_token' );
	}

	/**
	 * Save fetched token from google API to wp options.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function save_token() {
		return update_option( 'plugin_wc_import_google_sheet_gs_token', $this->token );
	}

	/**
	 * Set google API client.
	 *
	 * @since 2.0.0
	 */
	abstract public function set_client();

	/**
	 * Retrieve token from google API.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	abstract public function fetch_token();

	/**
	 * Initialize access token.
	 *
	 * @since 2.0.0
	 */
	public function init_token() {

		$this->set_client();

		$this->token = $this->get_token();
		if ( $this->token ) {
			$this->client->setAccessToken( $this->token );
		}

		// If there is previous token, and it is not expired.
		if ( ! $this->is_token_null_value() ) {
			if ( ! $this->client->isAccessTokenExpired() ) {
				return;
			}
		}

		// Refresh the token if possible, else fetch a new one.
		if ( $this->client->getRefreshToken() ) {
			$token = $this->client->fetchAccessTokenWithRefreshToken( $this->client->getRefreshToken() );
		} else {
			$token = $this->fetch_token();
		}

		// Check to see if there was an error.
		if ( is_array( $token ) && array_key_exists( 'error', $token ) ) {
			$this->error = new WP_Error( 'token_error', join( ', ', $token ) );
			return;
		}

		if ( ! empty( $token ) ) {
			$this->token = wp_json_encode( $token );
			$this->save_token();
		}
	}

	/**
	 * Check for token with empty value
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_token_null_value() {
		if ( 'null' === $this->token || '{"access_token":"null"}' === $this->token ) {
			return true;
		} else {
			return false;
		}
	}
}
