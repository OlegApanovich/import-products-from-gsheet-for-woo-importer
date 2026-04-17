<?php
/**
 * Google Api Interplay Abstracts
 *
 * @since 2.0.0
 *
 * @package GSWOO
 */

namespace GSWOO\Abstracts;

use GSWOO\Services\GoogleApiTokenAssertionMethodService;

/**
 * Class GoogleApiInterplayAbstract
 *
 * @since 2.0.0
 * @package GSWOO\Abstracts
 */
abstract class GoogleApiInterplayAbstract {
	/**
	 * Error aggregator.
	 *
	 * @var object Wp_Error
	 *
	 * @since 2.0.0
	 */
	public $error;

	/**
	 * Plugin options.
	 *
	 * @since  2.0.0
	 * @var array
	 */
	public $options;

	/**
	 * Initialize google API token service.
	 *
	 * @since 2.0.0
	 *
	 * @noinspection PhpUndefinedVariableInspection
	 *
	 * @return GoogleApiTokenAbstract
	 */
	public function get_token_service() {
		$token_service = new GoogleApiTokenAssertionMethodService( $this->options['google_api_key'] );

		return $token_service;
	}
}
