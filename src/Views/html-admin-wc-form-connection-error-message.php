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
	echo sprintf(
		// translators: %s1: plugin import page url, %s2: closing link tag.
		esc_html__(
			'You do not set plugin google API connection settings properly, please go to %1$1s plugin settings page %2$2s and try to set it again.',
			'import-products-from-gsheet-for-woo-importer'
		),
		'<a href="' . esc_url( menu_page_url( 'woocommerce_import_products_google_sheet_menu', false ) ) . '">',
		'</a>'
	)
	?>
</h2>
