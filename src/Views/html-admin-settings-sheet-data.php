<?php
/**
 * Admin View: Product import settings
 *
 * @since 2.0.0
 *
 * @var array $options
 * @var array $sheets_list
 *
 * @package GSWOO
 */

defined( 'ABSPATH' ) || exit;
?>

<h3>
	<label for="plugin_google_sheet_title_oauth2">
		<?php esc_html_e( 'Google sheet title', 'import-products-from-gsheet-for-woo-importer' ); ?>
	</label>
</h3>

<select id="plugin_google_sheet_data" name="plugin_wc_import_google_sheet_options[google_sheet_data]">
	<option disabled selected value>
		<?php esc_html_e( '-- select an option --', 'import-products-from-gsheet-for-woo-importer' ); ?>
	</option>
	<?php

	foreach ( $sheets_list as $sheet ) {
		$selected = '';
		if ( $options['google_sheet_data'] == $sheet['id'] ) {
			$selected = 'selected';
		}
		?>
		<option value="<?php echo esc_html( $sheet['id'] ); ?>" <?php echo $selected; ?>>
			<?php echo esc_html( $sheet['title'] ); ?>
		</option>
		<?php
	}
	?>
</select>

<script>
	jQuery(document).ready(function() {
		jQuery('#plugin_google_sheet_data').select2();
	});
</script>
