<?php
/**
 * Admin View
 *
 * @since 2.0.0
 *
 * @var array $options
 *
 * @package GSWOO
 */

defined( 'ABSPATH' ) || exit;

// @codingStandardsIgnoreLine
$code = empty( $_GET['code'] ) ? '' : rawurldecode( $_GET['code'] );
// We need to know where to redirect after authorization.
$plugin_setting_page = '&page=' . ( admin_url( 'admin.php?page=woocommerce_import_products_google_sheet_menu' ) );
$app_service_link    = 'https://monolitpro.info?plugin=import-products-from-gsheet-for-woo-importer?';
$action_name         = '&action=connect-redirect';
$app_link            = $app_service_link . $plugin_setting_page . $action_name;
?>

<h3>
	<label for="plugin_google_oauth2_code">
		<?php esc_html_e( 'Google Access Code', 'import-products-from-gsheet-for-woo-importer' ); ?>
	</label>
</h3>

<a class="button-primary" href="<?php echo esc_html( $app_link ); ?>">
	<strong><?php esc_html_e( 'Get Code', 'import-products-from-gsheet-for-woo-importer' ); ?></strong>
</a>

<input
	type="password"
	id="plugin_google_oauth2_code"
	placeholder="Enter Code"
	name="plugin_wc_import_google_sheet_options[google_code_oauth2]"
	size="40"
	value="<?php echo esc_html( $code ); ?>"
>
