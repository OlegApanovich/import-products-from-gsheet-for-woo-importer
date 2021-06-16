<?php
/**
 * Plugin connection google API error message on WC import form screen
 *
 * @since 2.0.0
 *
 * @var string $menu_page_url
 *
 * @package GSWOO
 */
?>

<h2 style="color: red">
	<?php
	echo sprintf(
		// translators: %s: plugin import page url.
		__(
			'You do not set plugin google API connection assets properly, please go to <a href="%s">plugin settings page</a> and try to set it again',
			'import-products-from-gsheet-for-woo-importer'
		),
		$menu_page_url
	)
	?>
</h2>
