<?php
/**
 * Admin View
 *
 * @since 2.0.0
 *
 * @noinspection PhpIncludeInspection
 *
 * @var array $response
 * @var string $auth_type
 *
 * @package GSWOO
 */

defined( 'ABSPATH' ) || exit;
?>

<input name="plugin_wc_import_google_sheet_options[google_auth_type]" type="hidden" value="<?php echo esc_html( $auth_type ); ?>">

<?php
if ( ! empty( $response['sheets_list'] ) ) {
	?>
	<h3>
		<label for="plugin_google_sheet_data">
			<?php esc_html_e( 'Google sheet title (support only native google-apps.spreadsheet file type)', 'import-products-from-gsheet-for-woo-importer' ); ?>
		</label>
	</h3>

	<select id="plugin_google_sheet_data" name="plugin_wc_import_google_sheet_options[google_sheet_data]">
		<option disabled selected value>
			<?php esc_html_e( '-- select an option --', 'import-products-from-gsheet-for-woo-importer' ); ?>
		</option>
		<?php

		foreach ( $response['sheets_list'] as $sheet ) {
			$selected = '';
			if ( $this->options['google_sheet_data'] === $sheet['id'] ) {
				$selected = 'selected';
			}
			?>
			<option value="<?php echo esc_html( $sheet['id'] ); ?>" <?php echo esc_html( $selected ); ?>>
				<?php echo esc_html( $sheet['title'] ); ?>
			</option>
			<?php
		}
		?>
	</select>
	<?php
}

if ( $response ) {
	include GSWOO_URI_ABSPATH . '/src/Views/html-admin-settings-' . $response['status'] . '-connection-message.php';
}
?>

<script>
	jQuery(document).ready(function() {
		jQuery('#plugin_google_sheet_data').select2();
	});
</script>
