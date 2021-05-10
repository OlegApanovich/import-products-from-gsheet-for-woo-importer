<?php
/**
 * Admin View: Product import form
 *
 * @since 1.0.0
 *
 * @package GSWOO
 */

use GSWOO\AdminSettingsHandler;

defined( 'ABSPATH' ) || exit;
?>

<h2>
	<?php esc_html_e( 'Import Woocommerce Products Google Sheet', 'import-products-from-gsheet-for-woo-importer' ); ?>
</h2>

<!--suppress HtmlUnknownTarget -->
<form action="options.php" method="post">

	<?php settings_fields( 'plugin_wc_import_google_sheet_options' ); ?>

	<?php do_settings_sections( 'plugin' ); ?>

	<?php
	$admin_settings_handler = new AdminSettingsHandler();
	$admin_settings_handler->set_connection_message();
	?>

	<input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes', 'import-products-from-gsheet-for-woo-importer' ); ?>" />
</form>
