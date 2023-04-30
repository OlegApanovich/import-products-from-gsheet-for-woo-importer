<?php
/**
 * Admin View: We use it for notification
 * about required plugin dependency.
 *
 * @since 2.3
 *
 * @var string $my_plugin_name
 * @var string $dependency_plugin_name
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
		?>
	</p>
</div>
