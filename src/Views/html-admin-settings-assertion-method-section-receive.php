<?php
/**
 * Admin View: Product import form
 *
 * @since 2.0.0
 *
 * @var array $options
 *
 * @noinspection PhpIncludeInspection
 *
 * @package GSWOO
 */

defined( 'ABSPATH' ) || exit;
?>

<h3>
	<label for="plugin_google_api_key">
		<?php esc_html_e( 'Google drive API client_secret json', 'import-products-from-gsheet-for-woo-importer' ); ?>
	</label>
</h3>

<textarea id="plugin_google_api_key" required name="plugin_wc_import_google_sheet_options[google_api_key]" rows="14" cols="50" value=""></textarea>

<br>
