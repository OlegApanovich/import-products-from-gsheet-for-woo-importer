<?php
/**
 * Admin View: Product import form
 *
 * @since 1.0.0
 *
 * @package GSWOO
 * @subpackage GSWOO/views
 */

defined( 'ABSPATH' ) || exit;
?>

<h3><?php esc_html_e( 'Google drive API client_secret json', 'import-products-from-gsheet-for-woo-importer' ); ?></h3>
<textarea id="plugin_google_api_key" required name="plugin_wc_import_google_sheet_options[google_api_key]" rows="14" cols="50" value="<?php echo esc_html( $google_api_key ); ?>">
<?php echo esc_html( $google_api_key ); ?>
</textarea>
<br>
<h3><?php esc_html_e( 'Google sheet title', 'import-products-from-gsheet-for-woo-importer' ); ?></h3>
<p><?php esc_html_e( 'Add multiple google sheets with comma separator', 'import-products-from-gsheet-for-woo-importer' ) ?></p><br>
<textarea id="plugin_google_sheet_title" required name="plugin_wc_import_google_sheet_options[google_sheet_title]" rows="14" cols="50" value="<?php echo esc_html( $google_sheet_title ); ?>">
<?php echo esc_html( $google_sheet_title ); ?>
</textarea>

