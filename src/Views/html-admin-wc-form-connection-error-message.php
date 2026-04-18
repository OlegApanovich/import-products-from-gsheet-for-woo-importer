<?php
/**
 * Plugin connection google API error message.
 *
 * @since 2.0.0
 *
 * @package GSWOO
 */

?>
<h2 style="color: red">
	<?php
	printf(
		// translators: %1$s: opening link tag, %2$s: closing link tag.
		wp_kses_post(
			__(
				'You do not set plugin google API connection settings properly, please go to %1$s plugin settings page %2$s and try to set it again.',
				'import-products-from-gsheet-for-woo-importer'
			)
		),
		'<a href="' . esc_url( menu_page_url( 'woocommerce_import_products_google_sheet_menu', false ) ) . '">',
		'</a>'
	)
	?>
</h2>
