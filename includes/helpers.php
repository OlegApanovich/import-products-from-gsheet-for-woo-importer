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

if ( ! function_exists( 'gswoo_is_plugin_active' ) ) :
	/**
	 * Verify if a plugin is active, if not deactivate the actual plugin and show an error.
	 *
	 * @see https://gist.github.com/dianjuar/9a398c9e86a20a30868eee0c653e0ca4
	 *
	 * @since  1.0.0
	 *
	 * @param string $my_plugin_name The plugin name trying to activate. The name of this plugin.
	 * @param string $dependency_plugin_name The dependency plugin name.
	 * @param string $path_to_plugin Path of the plugin
	 * to verify with the format 'dependency_plugin/dependency_plugin.php'.
	 * @param string $version_to_check Optional, verify certain version of the dependent plugin.
	 *
	 * @return bool
	 */
	function gswoo_is_plugin_active(
		$my_plugin_name,
		$dependency_plugin_name,
		$path_to_plugin,
		$version_to_check = null
	) {
		$success = true;
		// Needed to the function "deactivate_plugins" works.
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( ! is_plugin_active( $path_to_plugin ) ) {
			// Deactivate the current plugin.
			deactivate_plugins( 'import-products-from-gsheet-for-woo-importer/import-products-from-gsheet-for-woo-importer.php' );

			// Show an error alert on the admin area.
			add_action(
				'admin_notices',
				function () use (
				$my_plugin_name,
				$dependency_plugin_name
				) {
					?>
				<div class="updated error">
					<p>
						<?php
						echo wp_kses(
							sprintf(
								// translators: %1$s: current plugin name, %2$s plugin name that current plugin dependent on.
								__(
									'The plugin <strong>"%1$s"</strong> needs the plugin <strong>"%2$s"</strong> active',
									'import-products-from-gsheet-for-woo-importer'
								),
								$my_plugin_name,
								$dependency_plugin_name
							),
							array( 'strong' => array() )
						);
						echo '<br>';
						echo wp_kses(
							sprintf(
								// translators: %1$s: current plugin name.
								__(
									'%1$1s%2$2s has been deactivated%3$3s',
									'import-products-from-gsheet-for-woo-importer'
								),
								'<strong>',
								$my_plugin_name,
								'<srong>'
							),
							array( 'strong' => array() )
						);
						?>
					</p>
				</div>
					<?php
				}
			);

			$success = false;
		} else {
			// If version to check is not defined do nothing.
			if ( null === $version_to_check ) {
				/**
				 * Allow empty return
				 *
				 * @noinspection PhpInconsistentReturnPointsInspection
				 */
				return;
			}

			// Get the plugin dependency info.
			$dep_plugin_data =
				get_plugin_data( WP_PLUGIN_DIR . '/' . $path_to_plugin );

			// Compare version.
			$error = ! version_compare(
				$dep_plugin_data['Version'],
				$version_to_check,
				'>='
			);

			if ( $error ) {

				// Deactivate the current plugin.
				deactivate_plugins( 'import-products-from-gsheet-for-woo-importer/import-products-from-gsheet-for-woo-importer.php' );

				add_action(
					'admin_notices',
					function () use (
					$my_plugin_name,
					$dependency_plugin_name,
					$version_to_check
					) {
						?>
					<div class="updated error">
						<p>
							<?php
							echo wp_kses(
								sprintf(
									// translators: %1$s: current plugin name,%2$s plugin version to check, %3$s plugin name that current plugin dependent on.
									__(
										'The plugin <strong>"%1$s"</strong> needs the <strong>version %2$s</strong> or newer of <strong>"%3$s"</strong>',
										'import-products-from-gsheet-for-woo-importer'
									),
									$my_plugin_name,
									$version_to_check,
									$dependency_plugin_name
								),
								array( 'strong' => array() )
							);
							echo '<br>';
							echo wp_kses(
								sprintf(
									// translators: %1$s: current plugin name.
									__(
										'%1$1s%2$2s has been deactivated%3$3s',
										'import-products-from-gsheet-for-woo-importer'
									),
									'<strong>',
									$my_plugin_name,
									'<srong>'
								),
								array( 'strong' => array() )
							);
							?>
						</p>
					</div>
						<?php
						if ( isset( $_GET['activate'] ) ) {
							unset( $_GET['activate'] );
						}
					}
				);

				$success = false;
			}
		}

		return $success;
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

