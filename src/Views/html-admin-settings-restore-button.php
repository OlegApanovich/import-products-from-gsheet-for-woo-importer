<?php
/**
 * Admin View
 *
 * @since 2.0.0
 *
 * @package GSWOO
 */

?>

<button class="restore-button button-primary" onclick="add_hidden_restore_field(this);">
	<strong>
		<?php esc_html_e( 'Restore', 'import-products-from-gsheet-for-woo-importer' ); ?>
	</strong>
</button>
<input type="hidden" name="plugin_wc_import_google_sheet_options[google_code_oauth2_restore]" value="false">

<script>
	function add_hidden_restore_field() {
		jQuery('input[name="plugin_wc_import_google_sheet_options[google_code_oauth2_restore]"]').attr('value', 'true');
	}
</script>
