<?php
/**
 * Backward compatibility between versions action
 *
 * @since 2.0.0
 *
 * @package GSWOO/Actions
 */

namespace GSWOO\Actions;

defined( 'ABSPATH' ) || exit;


/**
 * Class BackwardCompatibilityAction
 *
 * @package GSWOO\Actions
 */
class BackwardCompatibilityAction {

	/**
	 * Constructor for admin settings
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Fire up actions and filter
	 *
	 * @since 2.0.0
	 */
	public function init() {
		add_filter(
			'gswoo_get_active_google_auth_type',
			array( $this, 'add_backward_active_google_auth_type' ),
			10,
			2
		);

		add_filter(
			'gswoo_get_plugin_options',
			array( $this, 'add_backward_get_plugin_options' ),
			10,
			2
		);

		add_filter(
			'gswoo_get_empty_response',
			array( $this, 'add_backward_get_empty_response' ),
			10,
			2
		);
	}

	/**
	 * Renamed option backward compatibility.
	 *
	 * @since 2.0.0
	 * @to all later
	 *
	 * @param string $auth_type
	 * @param array  $options
	 *
	 * @return string
	 */
	public function add_backward_active_google_auth_type( $auth_type, $options ) {
		if ( empty( $options['google_sheet_title'] ) ) {
			return $auth_type;
		} else {
			return 'assertion_method_tab';
		}
	}

	/**
	 * Renamed option backward compatibility.
	 *
	 * @since 2.0.0
	 * @to all later
	 *
	 * @param array                     $options
	 * @param object AdminSettingsModel $settings_model
	 *
	 * @return array
	 */
	public function add_backward_get_plugin_options( $options, $settings_model ) {

		if ( empty( $options['google_sheet_title'] ) || empty( $options['google_api_key'] ) ) {
			return $options;
		}

		$options['google_auth_type'] = 'assertion_method_tab';
		$settings_model->options     = $settings_model->decode_plugin_options( $options );

		$sheets_list =
			$settings_model->
			sheet_interplay_service->
			set_api_connect( '', $settings_model )->
			get_google_drive_sheets_list();

		foreach ( $sheets_list as $sheet ) {
			if ( $sheet['title'] == $options['google_sheet_title'] ) {
				$options['google_sheet_data'] = $sheet['id'];
			}
		}

		return $options;
	}

	/**
	 * Renamed option backward compatibility.
	 *
	 * @since 2.0.0
	 * @to all later
	 *
	 * @param bool                      $is_empty
	 * @param object AdminSettingsModel $settings_model
	 *
	 * @return bool
	 */
	public function add_backward_get_empty_response( $is_empty, $settings_model ) {
		if (
			! empty( $settings_model->options['google_sheet_title'] ) &&
			! empty( $settings_model->options['google_api_key'] ) ) {
			$is_empty = false;
		}

		return $is_empty;
	}
}
