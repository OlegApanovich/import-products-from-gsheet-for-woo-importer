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
								// translators: %1$s: current plugin name, %2$s plugin name that curent plugin dependent on.
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
			) ? true : false;

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
									// translators: %1$s: current plugin name,%2$s plugin version to check, %3$s plugin name that curent plugin dependent on.
									__(
										'The plugin <strong>"%1$s"</strong> needs the <strong>version %2$s</strong> or newer of <strong>"%3$s"</strong>',
										'import-products-from-gsheet-for-woo-importer'
									),
									$my_plugin_name,
									$version_to_check,
									$dependency_plugin_nam
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
