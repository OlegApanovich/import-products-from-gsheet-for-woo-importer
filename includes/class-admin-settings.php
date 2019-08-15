<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Admin settings
 */
class Admin_Settings {

	/**
	 * Construcotr for admin settings
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init amdin settings
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action(
			'admin_init',
			array( $this, 'plugin_admin_init_settings' )
		);
		add_action(
			'admin_init',
			array( $this, 'set_sheet_data_by_options' )
		);
	}

	/**
	 * Add submenu to woocommerce admin menu
	 */
	public function add_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'Import Google Sheet Settings', 'woocommerce-import-products-google-sheet' ),
			__( 'Import Google Sheet Settings', 'woocommerce-import-products-google-sheet' ),
			'manage_options',
			'woocommerce_import_products_google_sheet_menu',
			array( $this, 'settings_form' )
		);
	}

	/**
	 * Add main plugin setings form
	 */
	public function settings_form() {
		include_once( WC_IMPORT_SHEET_URI_ABSPATH
		              . 'views/html-admin-settings-form.php' );
	}

	/**
	 * Init plugin settings
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
	 */
	public function plugin_section_text() {
		echo '<p></p>';
	}

	/**
	 * Function callback for a add_settings_field
	 */
	public function display_settings() {
		$options = get_option( 'plugin_wc_import_google_sheet_options' );

		$google_api_key = ! empty( $options['google_api_key'] )
			? $options['google_api_key'] : '';

		$google_sheet_title = ! empty( $options['google_sheet_title'] )
			? $options['google_sheet_title'] : '';

		include_once( WC_IMPORT_SHEET_URI_ABSPATH
		              . 'views/html-admin-settings-form-options.php' );
	}

	/**
	 * Set options
	 *
	 * @param array $input
	 *
	 * @return array $new_input
	 */
	public function set_options( $input ) {
		$validate_input = $this->validate_options( $input );

		file_put_contents(
			WC_IMPORT_SHEET_URI_ABSPATH . 'assets/client_secret.json',
			$validate_input['google_api_key']
		);

		$this->set_user_error_message( $validate_input );

		return $validate_input;
	}

	/**
	 * Validate user input
	 *
	 * @param array $input
	 *
	 * @return array $validate_input
	 */
	public function validate_options( $input ) {
		$validate_input['google_api_key'] = trim( $input['google_api_key'] );
		$validate_input['google_sheet_title']
		                                  = trim( esc_html( $input['google_sheet_title'] ) );

		return $validate_input;
	}

	/**
	 * Try to get sheet data from saved options
	 */
	public function set_sheet_data_by_options() {
		if (
			! empty( $options = get_option( 'plugin_wc_import_google_sheet_options' ) ) &&
			! empty( $_POST['plugin_wc_import_google_sheet_options'] )
		) {
			$validate_input =
				$this->validate_options( $_POST['plugin_wc_import_google_sheet_options'] );

			$this->set_user_error_message( $validate_input );
		}
	}

	/**
	 * Try to check user inputs and return erorr message if they do not valid
	 *
	 * @param array $validate_input
	 */
	public function set_user_error_message( $validate_input ) {
		try {
			$google_api_obj = new Wrapper_Api_Google_Drive;

			try {
				$google_sheet
					= $google_api_obj->set_sheet( $validate_input['google_sheet_title'] );
				set_transient( 'google_sheet_connection_message', 1 );
			} catch ( Exception $e ) {
				set_transient( 'google_sheet_connection_message',
					esc_html__( 'We can\'t recieve spreeadsheet by your provided settings, please check settings and try it again',
						'woocommerce-import-products-google-sheet' ) );
			}
		} catch ( Exception $e ) {
			if ( ! empty( $e->getMessage() ) ) {
				set_transient( 'google_sheet_connection_message',
					sprintf( __( 'We can\'t set connection to google API by your client_secret json setting, please check it and try again.'
					             . ' API return responce error "%s"',
						'woocommerce-import-products-google-sheet' ),
						$e->getMessage() ) );
			} else {
				set_transient( 'google_sheet_connection_message',
					esc_html__( 'We can\'t set connection to google API by your client_secret json setting, please check it and try again',
						'woocommerce-import-products-google-sheet' ) );
			}
		}
	}

	/**
	 * After try establish connection to google drive api
	 * try to display user success or error message
	 */
	public function get_connection_message() {
		$connection_message_error
			= get_transient( 'google_sheet_connection_message' );

		if ( $connection_message_error === '1' ) {
			$menu_page_url = menu_page_url( 'product_importer_google_sheet',
				false );
			echo '<h3 style="color:green">'
			     . sprintf( __( 'Your settings was recived successfully, now you can go to <a href="%s">import products spread sheet page</a> and try import',
					'woocommerce-import-products-google-sheet' ),
					$menu_page_url ) . '</h3>';
		} else if ( $connection_message_error ) {
			echo '<h3 style="color:red">' . $connection_message_error . '</h3>';
		}

		delete_transient( 'google_sheet_connection_message' );
	}
}

new Admin_Settings();