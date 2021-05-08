<?php
/**
 * Admin View: Product import form
 *
 * @since 1.0.0
 *
 * @var string $google_api_key
 * @var string $google_sheet_title
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

<textarea id="plugin_google_api_key" required name="plugin_wc_import_google_sheet_options[google_api_key]" rows="14" cols="50" value="<?php echo esc_html( $google_api_key ); ?>">
    <?php echo esc_html( $google_api_key ); ?>
</textarea>

<br>

<h3>
    <label for="plugin_google_sheet_title">
        <?php esc_html_e( 'Google sheet title', 'import-products-from-gsheet-for-woo-importer' ); ?>
    </label>
</h3>

<input id="plugin_google_sheet_title" required name="plugin_wc_import_google_sheet_options[google_sheet_title]" size="40" type="text" value="<?php echo esc_html( $google_sheet_title ); ?>">
