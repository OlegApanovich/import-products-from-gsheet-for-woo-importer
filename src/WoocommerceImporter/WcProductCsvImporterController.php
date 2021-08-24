<?php
/**
 * Class GSWOO_WC_Product_CSV_Importer_Controller file.
 *
 * @since 1.0.0
 *
 * @package GSWOO
 */

namespace GSWOO\WoocommerceImporter;

use GSWOO\Models\AdminSettingsModel;
use WC_Product_CSV_Importer_Controller;
use WP_Error;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Importer' ) ) {
	return;
}

/**
 * Product importer controller - handles file upload and forms in admin.
 *
 * @since 1.0.0
 */
class WcProductCsvImporterController extends WC_Product_CSV_Importer_Controller {

	/**
	 * Instance of AdminSettingsModel class.
	 *
	 * @since  2.0.0
	 * @var object AdminSettingsModel
	 */
	public $settings_model;

	/**
	 * AdminSettingsController constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->settings_model = new AdminSettingsModel();
	}

	/**
	 * Output information about the uploading process.
	 *
	 * @since 1.0.0
	 * @noinspection PhpUnusedLocalVariableInspection
	 */
	protected function upload_form() {
		$bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
		$size       = size_format( $bytes );
		$upload_dir = wp_upload_dir();

		$response =
			$this->settings_model->
			process_connection();

		if (  $this->settings_model->is_response_success( $response ) ) {
			include dirname( __FILE__ ) . '/Views/html-product-csv-import-form.php';
		} else {
			include_once GSWOO_URI_ABSPATH
			             . '/src/Views/html-admin-wc-form-connection-error-message.php';
		}
	}

	/**
	 * Handles the CSV upload and initial parsing of the file to prepare for
	 * displaying author import options.
	 *
	 * @return string|WP_Error
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnusedLocalVariableInspection
	 */
	public function handle_upload() {
		// phpcs:disable WordPress.CSRF.NonceVerification.NoNonceVerification -- Nonce already verified in WC_Product_CSV_Importer_Controller::upload_form_handler()
		$file_url = isset( $_POST['file_url'] ) ? wc_clean( wp_unslash( $_POST['file_url'] ) ) : '';

		if ( empty( $file_url ) ) {
			if ( ! isset( $_REQUEST['gswoo-file'] ) ) {
				return new WP_Error( 'woocommerce_product_csv_importer_upload_file_empty', __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'import-products-from-gsheet-for-woo-importer' ) );
			}

			$upload_dir_arr  = wp_upload_dir();
			$file_name       = sanitize_file_name( $_REQUEST['gswoo-file'] );
			$gswoo_file_sheet_url  = $upload_dir_arr['url'] . '/' . $file_name . '.csv';
			$gswoo_file_sheet_path = $upload_dir_arr['path'] . '/' . $file_name . '.csv';

			$result =
				$this->settings_model->
				replace_import_file_with_gsheet_content( $_REQUEST, $gswoo_file_sheet_path );

			if ( is_wp_error( $result ) ) {
				return $result;
			}

			if ( ! self::is_file_valid_csv( wc_clean( wp_unslash( array_slice( explode( '/', $gswoo_file_sheet_url ), -1 )[0] ) ), false ) ) {
				return new WP_Error(
					'woocommerce_product_csv_importer_upload_file_invalid',
					__(
						'Invalid file type. The importer supports CSV and TXT file formats.',
						'import-products-from-gsheet-for-woo-importer'
					)
				);
			}

			$overrides = array(
				'test_form' => false,
				'mimes'     => self::get_valid_csv_filetypes(),
			);

			$import = $gswoo_file_sheet_path; // WPCS: sanitization ok, input var ok.

			$upload = array(
				'file' => $gswoo_file_sheet_path,
				'url'  => $gswoo_file_sheet_url,
				'type' => 'text/csv',
			);

			// Construct the object array.
			$object = array(
				'post_title'     => basename( $upload['file'] ),
				'post_content'   => $upload['url'],
				'post_mime_type' => $upload['type'],
				'guid'           => $upload['url'],
				'context'        => 'import',
				'post_status'    => 'private',
			);

			// Save the data.
			$id = wp_insert_attachment( $object, $upload['file'] );

			/*
			 * Schedule a cleanup for one day from now in case of failed
			 * import or missing wp_import_cleanup() call.
			 */
			wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', array( $id ) );

			return $upload['file'];
		} elseif ( file_exists( ABSPATH . $file_url ) ) {
			if ( ! self::is_file_valid_csv( ABSPATH . $file_url ) ) {
				return new WP_Error( 'woocommerce_product_csv_importer_upload_file_invalid', __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'import-products-from-gsheet-for-woo-importer' ) );
			}

			return ABSPATH . $file_url;
		}
		// phpcs:enable

		return new WP_Error( 'woocommerce_product_csv_importer_upload_invalid_file', __( 'Please upload or provide the link to a valid CSV file.', 'import-products-from-gsheet-for-woo-importer' ) );
	}
}
