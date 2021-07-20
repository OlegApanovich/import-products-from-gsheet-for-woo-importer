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
		echo sprintf(
		// translators: %1s: plugin import page link, %2s: close link tag.
			esc_html__(
				'We can not show you import google sheet plugin button because you do not set google api connection, please go to %1$1s plugin settings page %2$2s and try to set it again.',
				'import-products-from-gsheet-for-woo-importer'
			),
			'<a href="' . esc_url( $menu_page_url ) . '">',
			'</a>'
		)
		?>
	</p>
</div>;
