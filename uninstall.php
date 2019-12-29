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

$option_name = 'plugin_wc_import_google_sheet_options';

delete_option( $option_name );
