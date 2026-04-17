<?php
/**
 * Admin View: Product import form
 *
 * @since 1.0.0
 *
 * @var string $active_tab
 *
 * @package GSWOO
 */

defined( 'ABSPATH' ) || exit;
?>

<h1>
	<?php esc_html_e( 'Import Google Sheet Plugin Settings Page', 'import-products-from-gsheet-for-woo-importer' ); ?>
</h1>

<!--suppress HtmlUnknownTarget -->
<form action="options.php" method="post">
	<h2 class="nav-tab-wrapper">
		<a id="assertion_method_tab" href="?page=woocommerce_import_products_google_sheet_menu&auth_tab=assertion_method_tab" class="nav-tab nav-tab-active">
			<?php esc_html_e( 'Google API Connect', 'import-products-from-gsheet-for-woo-importer' ); ?>
		</a>
	</h2>

	<?php settings_fields( 'plugin_wc_import_google_sheet_options' ); ?>

	<?php
	do_settings_sections( 'assertion_method_page' );

	do_settings_sections( 'common_page' );
	?>

	<button class="button-primary">
		<strong>
			<?php
			esc_attr_e( 'Save Options', 'import-products-from-gsheet-for-woo-importer' );
			?>
		</strong>
	</button>
</form>
