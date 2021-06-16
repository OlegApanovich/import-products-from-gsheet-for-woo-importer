<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since 1.0.0
 * @package GSWOO
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$option_list = array(
	'plugin_wc_import_google_sheet_options',
	'plugin_wc_import_google_sheet_gs_token',
);

foreach ( $option_list as $option_name ) {
	delete_option( $option_name );
}

