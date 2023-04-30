<?php
/**
 * Admin View
 *
 * @since 2.0.0
 *
 * @var array $options
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

<textarea id="plugin_google_api_key" required name="plugin_wc_import_google_sheet_options[google_api_key]" rows="14" cols="50"><?php echo esc_html( $this->options['google_api_key'] ); ?></textarea>
<br>

<?php
require_once GSWOO_URI_ABSPATH . '/src/Views/html-admin-settings-restore-button.php';
?>
<br>
