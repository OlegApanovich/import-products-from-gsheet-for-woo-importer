<?php
/**
 * Admin View: Product import settings
 *
 * @since 2.0.0
 *
 * @var string $google_code_oauth2
 * @var string $active_tab
 * @var string $message;
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

<a class="button-primary" target="_blank" href="https://accounts.google.com/o/oauth2/auth?access_type=offline&approval_prompt=force&client_id=836707027943-am8hdf20f7r5bi48f0r5pta545p7k7l2.apps.googleusercontent.com&redirect_uri=urn%3Aietf%3Awg%3Aoauth%3A2.0%3Aoob&response_type=code&scope=https://www.googleapis.com/auth/drive.readonly">
	<strong><?php _e( 'Get Code', 'import-products-from-gsheet-for-woo-importer' ); ?></strong>
</a>

<input type="password" id="plugin_google_oauth2_code" placeholder="Enter Code" name="plugin_wc_import_google_sheet_options[google_code_oauth2]" size="40" value="<?php echo esc_html( $google_code_oauth2 ); ?>">

<?php
echo $message;
?>
