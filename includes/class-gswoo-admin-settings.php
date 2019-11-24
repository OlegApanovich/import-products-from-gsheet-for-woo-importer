<?php
defined( 'ABSPATH' ) || exit;

/**
 * Admin settings
 *
 * @since 1.0.0
 */
class GSWOO_Admin_Settings {

	/**
	 * Construcotr for admin settings
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init amdin settings
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action(
			'admin_init',
			array( $this, 'plugin_admin_init_settings' )
		);
	}

	/**
	 * Add submenu to woocommerce admin menu
	 *
	 * @since 1.0.0
	 */
	public function add_menu() {
		add_submenu_page(
			'woocommerce',
			esc_html__( 'Import Google Sheet Settings','import-products-from-gsheet-for-woo-importer' ),
			esc_html__( 'Import Google Sheet Settings', 'import-products-from-gsheet-for-woo-importer' ),
			'manage_options',
			'woocommerce_import_products_google_sheet_menu',
			array( $this, 'settings_form' )
		);
	}

	/**
	 * Add main plugin setings form
	 *
	 * @since 1.0.0
	 */
	public function settings_form() {
		include_once( GSWOO_URI_ABSPATH
		              . 'views/html-admin-settings-form.php' );
	}

	/**
	 * Init plugin settings
	 *
	 * @since 1.0.0
	 */
	public function plugin_admin_init_settings() {
		register_setting(
			'plugin_wc_import_google_sheet_options',
			'plugin_wc_import_google_sheet_options',
			array( $this, 'set_options' )
		);

		add_settings_section(
			'plugin_main',
			'',
			array( $this, 'plugin_section_text' ),
			'plugin'
		);

		add_settings_field(
			'plugin_google_api_key',
			'',
			array( $this, 'display_settings' ),
			'plugin', 'plugin_main'
		);

		add_settings_field(
			'plugin_google_sheet_title',
			'',
			array( $this, 'display_settings' ),
			'plugin',
			'plugin_main'
		);
	}

	/**
	 * Function callback for a add_settings_section
	 *
	 * @since 1.0.0
	 */
	public function plugin_section_text() {
		echo '<p></p>';
	}

	/**
	 * Function callback for a add_settings_field
	 *
	 * @since 1.0.0
	 */
	public function display_settings() {
		$options = $this->get_plugin_options();

		$google_api_key = ! empty( $options['google_api_key'] )
			? $options['google_api_key'] : '';

		$google_sheet_title = ! empty( $options['google_sheet_title'] )
			? $options['google_sheet_title'] : '';

		include_once( GSWOO_URI_ABSPATH
		              . 'views/html-admin-settings-form-options.php' );
	}

	/**
	 * Set options
	 *
	 * @since 1.0.0
	 *
	 * @param array $input
	 *
	 * @return array
	 */
	public function set_options( $input ) {
		$valid_input = $this->validate_options( $input );

		return $valid_input;
	}

	/**
	 * Validate user input
	 *
	 * @since 1.0.0
	 *
	 * @param array $input
	 *
	 * @return array
	 */
	public function validate_options( $input ) {
		$valid_input['google_sheet_title'] = esc_html( $input['google_sheet_title'] );
		$valid_input['google_api_key'] = esc_html( $input['google_api_key'] );

		return $valid_input;
	}

	/**
	 * Try to check user inputs and set erorr message if input is not valid
	 *
	 * @since 1.0.0
	 *
	 * @param array $validate_input
	 *
	 * @return bool
	 */
	public function check_user_input( $validate_input ) {
		if ( $this->set_file_access( $validate_input ) ) {
			try {
				$google_api_obj = new GSWOO_Wrapper_Api_Google_Drive;
				try {
					$google_sheet
						= $google_api_obj->set_sheet( $validate_input['google_sheet_title'] );

					$check = true;
				} catch ( Exception $e ) {
					$check = false;
				}
			} catch ( Exception $e ) {
				$check = false;
			}
		} else {
			$check = false;
		}

		return $check;
	}

	/**
	 * Retrive connection message by isser input
	 *
	 * @since 1.0.0
	 *
	 * @param array $valid_input
	 *
	 * @return string
	 */
	public function get_connection_message( $valid_input ) {
		$message = '';

		if ( ! empty( $valid_input ) && ! $this->set_file_access( $valid_input ) ) {
			$message = esc_html__(
							'Please check if plugin ' . GSWOO_URI_ABSPATH . 'assets directory has write permission',
							'import-products-from-gsheet-for-woo-importer'
						);
		} elseif ( ! empty( $valid_input ) ) {
			try {
				$google_api_obj = new GSWOO_Wrapper_Api_Google_Drive;

				try {
					$google_sheet
						= $google_api_obj->set_sheet( $valid_input['google_sheet_title'] );
					$menu_page_url
						= menu_page_url( 'product_importer_google_sheet', false );

					$message = sprintf(
								__(
									'Your settings was recived successfully, now you can go to <a href="%s">import products spread sheet page</a> and try import',
									'import-products-from-gsheet-for-woo-importer'
								),
								$menu_page_url
							);
				} catch ( Exception $e ) {
					$message = esc_html__(
								'We can\'t recieve spreeadsheet by your provided settings, please check settings and try it again',
								'import-products-from-gsheet-for-woo-importer'
							);
				}
			} catch ( Exception $e ) {
				if ( ! empty( $e->getMessage() ) ) {
					$message =  sprintf(
									__(
										'We can\'t set connection to google API by your providing settings, please check it and try again.'
										 . ' API return responce error "%s"',
										'import-products-from-gsheet-for-woo-importer'
									),
									$e->getMessage()
								);
				} else {
					$message = esc_html__(
							'We can\'t set connection to google API by your client_secret json setting, please check it and try again',
							'import-products-from-gsheet-for-woo-importer'
						);
				}
			}
		}

		return $message;
	}

	/**
	 * Try to set file access
	 *
	 * @since 1.0.0
	 *
	 * @param array $validate_input
	 *
	 * @return bool
	 */
	public function set_file_access( $valid_input ) {
		$success = file_put_contents(
			GSWOO_URI_ABSPATH . 'assets/client_secret.json',
			$valid_input['google_api_key']
		);

		if ( ! empty( $success ) ) {
			$success = true;
		} else {
			$success = false;
		}

		return $success;
	}

	/**
	 * After try establish connection to google drive api
	 * try to display user success or error message
	 *
	 * @since 1.0.0
	 */
	public function set_connection_message() {
		$options = $this->get_plugin_options();

		$connection_message = $this->get_connection_message( $options );
		$check = $this->check_user_input( $options );

		if ( $check ) {
			echo '<h3 style="color:green">' . $connection_message . '</h3>';
		} else {
			echo '<h3 style="color:red">' . $connection_message . '</h3>';
		}
	}

	/**
	 * Get plugin options
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_plugin_options() {
		$options = get_option( 'plugin_wc_import_google_sheet_options' );

		if ( ! empty( $options['google_sheet_title'] ) && ! empty( $options['google_api_key'] ) ) {
			$options['google_sheet_title'] = wp_specialchars_decode( $options['google_sheet_title'], ENT_QUOTES );
			$options['google_api_key'] = wp_specialchars_decode( $options['google_api_key'], ENT_QUOTES );
		}

		return $options;
	}

}

new GSWOO_Admin_Settings();
