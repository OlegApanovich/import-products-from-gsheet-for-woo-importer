<?php
/**
 * Admin View: Product import settings
 *
 * @since 2.0.0
 *
 * @var array $response
 * @var array $options
 *
 * @package GSWOO
 */

defined( 'ABSPATH' ) || exit;
?>

<input name="plugin_wc_import_google_sheet_options[google_auth_type]" type="hidden" value="<?php echo esc_html( $options['google_auth_type'] ); ?>">

<?php
// if ( ! empty( $sheets_list ) && is_array( $sheets_list ) ) {
// include GSWOO_URI_ABSPATH . '/src/Views/html-admin-settings-sheet-data.php';
// }

if ( $response ) {
	include GSWOO_URI_ABSPATH . '/src/Views/html-admin-settings-' . $response['status'] . '-connection-message.php';
}
