<?php
/**
 * Library of helper plugin functions
 *
 * @since 1.0.0
 *
 * @package GSWOO
 * @subpackage GSWOO/includes
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'gswoo_validate_dependency_plugin' ) ) :
	/**
	 * Verify if a plugin is active, if not than deactivate the actual our plugin and show an error.
	 *
	 * @since  1.0
	 *
	 * @param string $my_plugin_name The plugin name trying to activate. The name of this plugin.
	 * @param string $dependency_plugin_name The dependency plugin name.
	 * @param string $path_to_plugin Path of the plugin
	 * to verify with the format 'dependency_plugin/dependency_plugin.php'.
	 * @param string $version_to_check Optional, verify certain version of the dependent plugin.
	 *
	 * @return bool
	 */
	function gswoo_validate_dependency_plugin(
		$my_plugin_name,
		$dependency_plugin_name,
		$path_to_plugin,
		$version_to_check = null
	) {
		$success          = true;
		$template_payload = array();
		// Needed to the function "deactivate_plugins" works.
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( ! is_plugin_active( $path_to_plugin ) ) {
			// Show an error alert on the admin area.
			$template_payload = array(
				'my_plugin_name'         => $my_plugin_name,
				'dependency_plugin_name' => $dependency_plugin_name,
				'version_to_check'       => $version_to_check,
			);
			$success          = false;
		} else {
			// Get the plugin dependency info.
			$version =
				gswoo_get_plugin_version( WP_PLUGIN_DIR . '/' . $path_to_plugin );

			// Compare version.
			$is_required_version = ! version_compare(
				$version,
				$version_to_check,
				'>='
			);

			if ( $is_required_version ) {
				$template_payload = array(
					'my_plugin_name'         => $my_plugin_name,
					'dependency_plugin_name' => $dependency_plugin_name,
					'version_to_check'       => $version_to_check,
				);
				$success          = false;
			}
		}

		if ( ! $success ) {
			add_action(
				'admin_notices',
				function () use ( $template_payload ) {
					include WP_PLUGIN_DIR . '/import-products-from-gsheet-for-woo-importer/src/Views/html-admin-required-plugin-notification.php';
				}
			);
		}

		return $success;
	}
endif;

if ( ! function_exists( 'gswoo_get_plugin_version' ) ) :
	/**
	 * Get the plugin version, parsing main plugin file.
	 *
	 * @param string $plugin_file_path
	 *
	 * @return bool|string
	 */
	function gswoo_get_plugin_version( $plugin_file_path ) {
        // phpcs:ignore:WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$plugin_data = file_get_contents( $plugin_file_path );
		if ( preg_match( '/^[ \t\/*#@]*[Vv]ersion\s*:\s*([^\r\n]+)/m', $plugin_data, $matches ) ) {
			return trim( $matches[1] );
		}
		return false;
	}
endif;

if ( ! function_exists( 'gswoo_check_screen' ) ) :
	/**
	 * Check current screen.
	 *
	 * @since  2.0.0
	 *
	 * @param string $get_request_name
	 * @param string $get_request_value
	 *
	 * @return bool
	 */
	function gswoo_check_screen( $get_request_name, $get_request_value ) {
		return ! empty( $_GET[ $get_request_name ] ) && $get_request_value === $_GET[ $get_request_name ];
	}
endif;

if ( ! function_exists( 'gswoo_is_plugin_settings_screen' ) ) :
	/**
	 * Check if current screen is plugin import screen in admin area.
	 *
	 * @since  2.0.0
	 *
	 * @return bool
	 */
	function gswoo_is_plugin_settings_screen() {
		return gswoo_check_screen( 'page', 'woocommerce_import_products_google_sheet_menu' );
	}
endif;

if ( ! function_exists( 'gswoo_is_plugin_importer_screen' ) ) :
	/**
	 * Check if current screen is plugin import screen in admin area.
	 *
	 * @since  2.0.0
	 *
	 * @return bool
	 */
	function gswoo_is_plugin_importer_screen() {
		return gswoo_check_screen( 'page', 'product_importer_google_sheet' );
	}
endif;

if ( ! function_exists( 'gswoo_is_woocommerce_product_screen' ) ) :
	/**
	 * Check if current screen is woocommerce product screen in admin area.
	 *
	 * @since  2.0.0
	 *
	 * @return bool
	 */
	function gswoo_is_woocommerce_product_screen() {
		return gswoo_check_screen( 'post_type', 'product' );
	}
endif;

if ( ! function_exists( 'gswoo_is_woocommerce_product_list_screen' ) ) :
	/**
	 * Check if current screen is woocommerce edit product screen in admin area.
	 *
	 * @since  2.0.0
	 *
	 * @return bool
	 */
	function gswoo_is_woocommerce_product_list_screen() {
		$screen = get_current_screen();

		return ! empty( $screen->id ) && 'edit-product' === $screen->id;
	}
endif;
