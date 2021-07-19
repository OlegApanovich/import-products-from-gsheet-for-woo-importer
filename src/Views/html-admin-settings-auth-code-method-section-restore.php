<?php
/**
 * Admin View: Product import settings
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
	<label for="plugin_google_oauth2_code">
		<?php esc_html_e( 'Google Access Code', 'import-products-from-gsheet-for-woo-importer' ); ?>
	</label>
</h3>

<?php
require_once GSWOO_URI_ABSPATH . '/src/Views/html-admin-settings-restore-button.php';
?>

<input readonly type="password" id="plugin_google_oauth2_code" name="plugin_wc_import_google_sheet_options[google_code_oauth2]" size="40" value="<?php echo esc_html( $this->options['google_code_oauth2'] ); ?>">
<br>
