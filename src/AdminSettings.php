<?php
/**
 * Plugin settings
 *
 * @since 1.0.0
 *
 * @package GSWOO
 */

namespace GSWOO;

use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Admin settings
 *
 * @since 1.0.0
 */
class AdminSettings {

	/**
	 * Constructor for admin settings
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init admin settings
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action(
			'admin_init',
			array( $this, 'plugin_admin_init_settings' )
		);
		add_filter(
			'plugin_action_links_' . plugin_basename( GSWOO_PLUGIN_FILE ),
			array( $this, 'set_plugin_action_links' ),
			10,
			1
		);
		add_filter(
			'woocommerce_screen_ids',
			array( $this, 'add_woocommerce_screen_ids' ),
			10,
			1
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
			esc_html__( 'Import Google Sheet', 'import-products-from-gsheet-for-woo-importer' ),
			esc_html__( 'Import Google Sheet', 'import-products-from-gsheet-for-woo-importer' ),
			'manage_options',
			'woocommerce_import_products_google_sheet_menu',
			array( $this, 'settings_form' )
		);
	}

	/**
	 * Add main plugin settings form
	 *
	 * @since 1.0.0
	 */
	public function settings_form() {
		include_once GSWOO_URI_ABSPATH
					. '/src/Views/html-admin-settings-form.php';
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
			array( 'sanitize_callback' => array( $this, 'validate_options' ) )
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
			'plugin',
			'plugin_main'
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
	 * @noinspection PhpUnusedLocalVariableInspection
	 */
	public function display_settings() {
		$options = get_option( 'plugin_wc_import_google_sheet_options' );

		$google_api_key = ! empty( $options['google_api_key'] )
			? $options['google_api_key'] : '';

		$google_sheet_title = ! empty( $options['google_sheet_title'] )
			? $options['google_sheet_title'] : '';

		include_once GSWOO_URI_ABSPATH
					. '/src/Views/html-admin-settings-form-options.php';
	}

	/**
	 * Set options and validate user input
	 *
	 * @noinspection PhpUnused
	 *
	 * @since 1.0.0
	 *
	 * @param array $input Option input value.
	 *
	 * @return array
	 */
	public function validate_options( $input ) {
		$valid_input['google_sheet_title'] = esc_html( $input['google_sheet_title'] );
		$valid_input['google_api_key']     = esc_html( $input['google_api_key'] );

		return $valid_input;
	}

	/**
	 * Try to check user inputs and set error message if input is not valid
	 *
	 * @since 1.0.0
	 *
	 * @param array $user_input Options after input validation.
	 *
	 * @return bool
	 */
	public function check_user_input( $user_input ) {
		if ( $this->put_key_to_file_access( $user_input ) ) {
			try {
				$google_api_obj = new WrapperApiGoogleDrive();
				try {
					$google_api_obj->set_sheet( $user_input['google_sheet_title'] );

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
	 * Retrieve connection message by user input
	 *
	 * @since 1.0.0
	 *
	 * @noinspection HtmlUnknownTarget
	 *
	 * @param array $valid_input Options after input validation.
	 *
	 * @return string
	 */
	public function get_connection_message( $valid_input ) {
		$message = '';

		if ( empty( $valid_input ) ) {
			return $message;
		}

		if ( $this->put_key_to_file_access( $valid_input ) ) {
			try {
				$google_api_obj = new WrapperApiGoogleDrive();
				// fall in catch exception if error retrieve.
				$google_api_obj->set_sheet( $valid_input['google_sheet_title'] );
				$menu_page_url = menu_page_url( 'product_importer_google_sheet', false );

				$message = sprintf(
					// translators: %s: plugin import page url.
					__(
						'Your settings was received successfully, now you can go to <a href="%s">import products spread sheet page</a> and try import',
						'import-products-from-gsheet-for-woo-importer'
					),
					$menu_page_url
				);
			} catch ( Exception $e ) {
				if ( empty( $e->getMessage() ) ) {
					$message = esc_html__(
						"We can't receive spreadsheet by your provided settings, please check settings and try it again",
						'import-products-from-gsheet-for-woo-importer'
					);
				} else {
					$message = sprintf(
						// translators: %s: error message.
						__(
							"We can't received spreadsheet by your provided settings, please check settings and try it again. Google API response error: '%s'",
							'import-products-from-gsheet-for-woo-importer'
						),
						$e->getMessage()
					);
				}
			}
		} else {
			$message = sprintf(
				// translators: %1$s: path to assets directory of file plugin.
				esc_html__(
					'Please check if plugin %1$s assets directory has write permission',
					'import-products-from-gsheet-for-woo-importer'
				),
				GSWOO_URI_ABSPATH
			);
		}

		return $message;
	}

	/**
	 * Try to set file access
	 *
	 * @since 1.0.0
	 *
	 * @param array $valid_input Options after validation.
	 *
	 * @return bool
	 */
	public function put_key_to_file_access( $valid_input ) {

		if ( empty( $valid_input ) ) {
			return false;
		}

		try {
			$try_file_put = file_put_contents(
				GSWOO_URI_ABSPATH . 'assets/client_secret.json',
				$valid_input['google_api_key']
			);
		} catch ( Exception $e ) {
			return false;
		}

		if ( empty( $try_file_put ) ) {
			return false;
		}

		return true;
	}

	/**
	 * After try establish connection to google drive api
	 * try to display user success or error message
	 *
	 * @since 1.0.0
	 * @noinspection PhpUnused
	 */
	public function set_connection_message() {
		$options = $this->get_plugin_options();

		$connection_message = $this->get_connection_message( $options );
		// Input already validated through settings API.
		$check = $this->check_user_input( $options );

		if ( $check ) {
			echo '<h3 style="color:green">' . wp_kses( $connection_message, array( 'a' => array( 'href' => array() ) ) ) . '</h3>';
		} else {
			echo '<h3 style="color:red">' . wp_kses( $connection_message, array( 'a' => array( 'href' => array() ) ) ) . '</h3>';
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
			$options['google_api_key']     = wp_specialchars_decode( $options['google_api_key'], ENT_QUOTES );
		}

		return $options;
	}

	/**
	 * Add plugin provided screen to woocommerce admin area.
	 *
	 * @since 1.0.0
	 *
	 * @param array $screen_ids Screen id list.
	 *
	 * @return array $screen_ids
	 */
	public function add_woocommerce_screen_ids( $screen_ids ) {

		$screen_ids[] = 'product_page_product_importer_google_sheet';

		return $screen_ids;
	}

	/**
	 * Set additional links on a plugin admin dashboard page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links all links.
	 *
	 * @return array
	 */
	public function set_plugin_action_links( $links ) {
		return array_merge(
			array(
				'<a href="' .
				admin_url( 'admin.php?page=woocommerce_import_products_google_sheet_menu' ) .
				'">' .
				esc_html__( 'Settings', 'import-products-from-gsheet-for-woo-importer' ) .
				'</a>',
			),
			$links
		);
	}
}
