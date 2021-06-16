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
		// translators: %s: plugin import page url.
			__(
				'We can not show you import google sheet plugin button because you do not set google api connection, please go to <a href="%s">plugin settings page</a> and try to set it again.',
				'import-products-from-gsheet-for-woo-importer'
			),
			$menu_page_url
		)
		?>
	</p>
</div>;
