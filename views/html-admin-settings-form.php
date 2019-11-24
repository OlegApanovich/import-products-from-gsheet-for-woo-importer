<?php
/**
 * Admin View: Product import form
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<h2>
	<?php esc_html_e( 'Import Woocommerce Produts Google Sheet Plugin Options Page', 'import-products-from-gsheet-for-woo-importer' ) ?>
</h2>
<form action="options.php" method="post">
	<?php settings_fields( 'plugin_wc_import_google_sheet_options' ); ?>
	<?php do_settings_sections( 'plugin' ); ?>

	<?php
	$this->set_connection_message();
	?>
	<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form>
