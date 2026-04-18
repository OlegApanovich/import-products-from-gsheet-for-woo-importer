<?php
/**
 * Plugin connection google API error message on WC product screen
 *
 * @since 2.0.0
 *
 * @var string $menu_page_url
 *
 * @package GSWOO
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="notice notice-warning is-dismissible">
	<p>
		<?php
		printf(
			wp_kses_post(
				// translators: %1$s: opening link tag, %2$s: closing link tag.
				__(
					'We can not show you import google sheet plugin button because you do not set google api connection, please go to %1$s plugin settings page %2$s and try to set it again.',
					'import-products-from-gsheet-for-woo-importer'
				)
			),
			'<a href="' . esc_url( $menu_page_url ) . '">',
			'</a>'
		)
		?>
	</p>
</div>;
