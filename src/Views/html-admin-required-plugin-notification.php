<?php
/**
 * Admin View: We use it for notification
 * about required plugin dependency.
 *
 * @since 2.3
 *
 * @var array $template_payload
 *
 * @package GSWOO
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="notice notice-error is-dismissible">
	<p>
		<?php
		echo wp_kses(
			sprintf(
			// translators: %1$s: current plugin name, %2$s plugin name that current plugin dependent on.
				__(
					'The plugin <strong>"%1$s"</strong> needs the plugin <strong>"%2$s"</strong> active with version <strong>%3$s</strong> or higher.',
					'import-products-from-gsheet-for-woo-importer'
				),
				$template_payload['my_plugin_name'],
				$template_payload['dependency_plugin_name'],
				$template_payload['version_to_check']
			),
			array( 'strong' => array() )
		);
		?>
	</p>
</div>
