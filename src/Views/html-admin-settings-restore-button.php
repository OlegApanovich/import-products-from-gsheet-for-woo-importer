<?php
/**
 * Admin View
 *
 * @since 2.0.0
 *
 * @package GSWOO
 */

?>

<button class="restore-button button-primary" onclick="processRestoreSettings(this);">
	<strong>
		<?php esc_html_e( 'Restore', 'import-products-from-gsheet-for-woo-importer' ); ?>
	</strong>
</button>
<input type="hidden" name="plugin_wc_import_google_sheet_options[settings_auth_restore]" value="false">
