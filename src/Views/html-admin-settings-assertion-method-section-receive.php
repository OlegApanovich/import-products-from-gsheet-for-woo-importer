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
		<?php esc_html_e( 'Google drive API client_secret json', 'import-products-from-gsheet-for-woo-importer' ); ?> ( <a href="https://github.com/OlegApanovich/import-products-from-gsheet-for-woo-importer?tab=readme-ov-file#set-connection-wtih-client_secret-json-code" target="_blank"><?php esc_html_e( 'how to get', 'import-products-from-gsheet-for-woo-importer' ); ?></a> )
	</label>
</h3>

<textarea id="plugin_google_api_key" required name="plugin_wc_import_google_sheet_options[google_api_key]" rows="14" cols="50"></textarea>

<br>
