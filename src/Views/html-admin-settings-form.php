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
	<?php esc_html_e( 'Plugin Options Page', 'import-products-from-gsheet-for-woo-importer' ); ?>
</h1>

<br>

<h2>
	<?php esc_html_e( 'Google API Connect Method', 'import-products-from-gsheet-for-woo-importer' ); ?>
</h2>

<!--suppress HtmlUnknownTarget -->
<form action="options.php" method="post">

	<h2 class="nav-tab-wrapper">
		<a href="?page=woocommerce_import_products_google_sheet_menu&auth_tab=auth_code_method_tab" class="nav-tab <?php echo $active_tab == 'auth_code_method_tab' ? 'nav-tab-active' : ''; ?> ?>">
			<?php _e( 'One Click Auto Connect', 'import-products-from-gsheet-for-woo-importer' ); ?>
		</a>
		<a href="?page=woocommerce_import_products_google_sheet_menu&auth_tab=assertion_method_tab" class="nav-tab <?php echo $active_tab == 'assertion_method_tab' ? 'nav-tab-active' : ''; ?> ?>">
			<?php _e( 'Manual Connect', 'import-products-from-gsheet-for-woo-importer' ); ?>
		</a>
	</h2>

	<?php settings_fields( 'plugin_wc_import_google_sheet_options' ); ?>

	<?php
	if ( 'auth_code_method_tab' == $active_tab ) {
		do_settings_sections( 'oauth2_page' );
	} elseif ( 'assertion_method_tab' == $active_tab ) {
		do_settings_sections( 'api_key_page' );
	}

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
