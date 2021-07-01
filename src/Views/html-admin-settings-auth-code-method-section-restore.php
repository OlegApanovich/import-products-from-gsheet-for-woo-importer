<?php
/**
 * Admin View: Product import settings
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
	<label for="plugin_google_oauth2_code">
		<?php esc_html_e( 'Google Access Code', 'import-products-from-gsheet-for-woo-importer' ); ?>
	</label>
</h3>

<button class="button-primary" onclick="add_hidden_restore_field(this);">
	<strong>
		<?php _e( 'Restore', 'import-products-from-gsheet-for-woo-importer' ); ?>
	</strong>
</button>

<input readonly type="password" id="plugin_google_oauth2_code" name="plugin_wc_import_google_sheet_options[google_code_oauth2]" size="40" value="<?php echo esc_html( $options['google_code_oauth2'] ); ?>">

<input type="hidden" name="plugin_wc_import_google_sheet_options[google_code_oauth2_restore]" value="false">

<br>

<script>
	function add_hidden_restore_field() {
		jQuery('input[name="plugin_wc_import_google_sheet_options[google_code_oauth2_restore]"]').attr('value', 'true');
	}
</script>
