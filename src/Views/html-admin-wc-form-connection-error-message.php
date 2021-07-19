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
		// translators: %s: plugin import page url.
		__(
			'You do not set plugin google API connection assets properly, please go to <a href="%s">plugin settings page</a> and try to set it again.',
			'import-products-from-gsheet-for-woo-importer'
		),
		menu_page_url( 'woocommerce_import_products_google_sheet_menu', false )
	)
	?>
</h2>
