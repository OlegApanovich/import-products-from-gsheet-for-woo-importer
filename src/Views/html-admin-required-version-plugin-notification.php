<?php
/**
 * Admin View: We use it for notification
 * about required plugin dependency version.
 *
 * @since 2.3
 *
 * @var string $my_plugin_name
 * @var string $dependency_plugin_name
 * @var string $version_to_check
 *
 * @package GSWOO
 */

defined( 'ABSPATH' ) || exit;
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
		?>
	</p>
</div>
