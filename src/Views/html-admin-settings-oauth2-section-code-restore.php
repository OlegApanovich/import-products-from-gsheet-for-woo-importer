<?php
/**
 * Admin View: Product import settings
 *
 * @since 2.0.0
 *
 * @var string $google_code_oauth2
 * @var string $google_sheet_title_oauth2
 * @var string $active_tab
 * @var string $message
 * @var array $all_sheets
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

<input readonly type="password" id="plugin_google_oauth2_code" name="plugin_wc_import_google_sheet_options[google_code_oauth2]" size="40" value="<?php echo esc_html( $google_code_oauth2 ); ?>">

<br>

<h3>
	<label for="plugin_google_sheet_title_oauth2">
		<?php esc_html_e( 'Google sheet title', 'import-products-from-gsheet-for-woo-importer' ); ?>
	</label>
</h3>

<?php
if ( is_array( $all_sheets ) ) {
	?>
	<select id="plugin_google_sheet_title_oauth2" name="plugin_wc_import_google_sheet_options[google_sheet_title_oauth2]">
		<option disabled selected value>
			<?php esc_html_e( '-- select an option --', 'import-products-from-gsheet-for-woo-importer' ); ?>
		</option>
		<?php
		foreach ( $all_sheets as $sheet ) {
			$selected = '';
			if ( $google_sheet_title_oauth2 == $sheet['title'] ) {
				$selected = 'selected';
			}
			?>
			<option value="<?php echo esc_html( json_encode( $sheet ) ); ?>" <?php echo $selected; ?>>
				<?php echo esc_html( $sheet['title'] ); ?>
			</option>
			<?php
		}
		?>
	</select>
	<?php
}
?>

<input type="hidden" name="plugin_wc_import_google_sheet_options[google_code_oauth2_restore]" value="false">

<?php
echo $message;
?>

<script>
	function add_hidden_restore_field() {
		jQuery('input[name="plugin_wc_import_google_sheet_options[google_code_oauth2_restore]"]').attr('value', 'true');
	}

	jQuery(document).ready(function() {
		jQuery('#plugin_google_sheet_title_oauth2').select2();
	});
</script>
