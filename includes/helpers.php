<?php
defined( 'ABSPATH' ) || exit;

/**
 * Verify if a plugin is active, if not deactivate the actual plugin an show an error
 *
 * by https://gist.github.com/dianjuar/9a398c9e86a20a30868eee0c653e0ca4
 *
 * @param  [string]  $my_plugin_name
 *                   The plugin name trying to activate. The name of this plugin
 *                   Ex:
 *                   WooCommerce new Shipping Method
 *
 * @param  [string]  $dependency_plugin_name
 *                   The dependency plugin name.
 *                   Ex:
 *                   WooCommerce.
 *
 * @param  [string]  $path_to_plugin
 *                   Path of the plugin to verify with the format 'dependency_plugin/dependency_plugin.php'
 *                   Ex:
 *                   woocommerce/woocommerce.php
 *
 * @param  [string] $textdomain
 *                   Text domain to looking the localization (the translated strings)
 *
 * @param  [string] $version_to_check
 *                   Optional, verify certain version of the dependent plugin
 */
if ( ! function_exists( 'wc_import_google_sheet_is_plugin_active' ) ) :
	function wc_import_google_sheet_is_plugin_active(
		$my_plugin_name,
		$dependency_plugin_name,
		$path_to_plugin,
		$textdomain = '',
		$version_to_check = null
	) {
		# Needed to the function "deactivate_plugins" works
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( ! is_plugin_active( $path_to_plugin ) ) {
			# Deactivate the current plugin
			deactivate_plugins( 'woocommerce-import-products-google-sheet/woocommerce-import-products-google-sheet.php' );

			# Show an error alert on the admin area
			add_action( 'admin_notices', function () use (
				$my_plugin_name,
				$dependency_plugin_name,
				$textdomain
			) {
				?>
                <div class="updated error">
                    <p>
						<?php
						echo sprintf(
							__( 'The plugin <strong>"%s"</strong> needs the plugin <strong>"%s"</strong> active',
								$textdomain ),
							$my_plugin_name, $dependency_plugin_name
						);
						echo '<br>';
						echo sprintf(
							__( '<strong>%s has been deactivated</strong>',
								$textdomain ),
							$my_plugin_name
						);
						?>
                    </p>
                </div>
				<?php
			} );
		} else {
			# If version to check is not defined do nothing
			if ( $version_to_check === null ) {
				return;
			}

			# Get the plugin dependency info
			$dep_plugin_data =
				get_plugin_data( WP_PLUGIN_DIR . '/' . $path_to_plugin );

			# Compare version
			$error = ! version_compare( $dep_plugin_data['Version'],
				$version_to_check, '>=' ) ? true : false;

			if ( $error ) {

				# Deactivate the current plugin
				deactivate_plugins( 'woocommerce-import-products-google-sheet/woocommerce-import-products-google-sheet.php' );

				add_action( 'admin_notices', function () use (
					$my_plugin_name,
					$dependency_plugin_name,
					$version_to_check, 
					$textdomain
				) {
					?>
                    <div class="updated error">
                        <p>
							<?php
							echo sprintf(
								__( 'The plugin <strong>"%s"</strong> needs the <strong>version %s</strong> or newer of <strong>"%s"</strong>',
									$textdomain ),
								$my_plugin_name,
								$version_to_check,
								$dependency_plugin_name
							);
							echo '<br>';
							echo sprintf(
								__( '<strong>%s has been deactivated</strong>',
									$textdomain ),
								$my_plugin_name
							);
							?>
                        </p>
                    </div>
					<?php
					if ( isset( $_GET['activate'] ) ) {
						unset( $_GET['activate'] );
					}
				} );
			}
		}
	}
endif;