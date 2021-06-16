<?php
/**
 * Admin View: Product import settings
 *
 * @since 2.0.0
 *
 * @var string $google_auth_type
 *
 * @package GSWOO
 */

defined( 'ABSPATH' ) || exit;
?>

<input name="plugin_wc_import_google_sheet_options[google_auth_type]" type="hidden" value="<?php echo esc_html( $google_auth_type ); ?>">
