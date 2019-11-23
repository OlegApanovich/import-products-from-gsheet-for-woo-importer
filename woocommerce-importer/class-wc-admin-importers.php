<?php
/**
 * Init WooCommerce data importers.
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Google_Sheet_WC_Admin_Importers Class.
 *
 * @since 1.0.0
 */
class Google_Sheet_WC_Admin_Importers extends WC_Admin_Importers {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! $this->import_allowed() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'add_to_menus' ) );
		add_action( 'admin_head', array( $this, 'hide_from_menus' ) );

		// Register WooCommerce importers.
		$this->importers['product_importer_google_sheet'] = array(
			'menu'       => 'edit.php?post_type=product',
			'name'       => __( 'Product Import Goole Sheet', 'woocommerce-import-products-google-sheet' ),
			'capability' => 'import',
			'callback'   => array( $this, 'product_importer' ),
		);
	}

	/**
	 * The product importer.
	 *
	 * @since 1.0.0
	 */
	public function product_importer() {

		include_once WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php';
		include_once WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';
		include_once WC_IMPORT_SHEET_URI_ABSPATH . 'woocommerce-importer/class-wc-product-csv-importer-controller.php';

		$importer = new Google_Sheet_WC_Product_CSV_Importer_Controller();
		$importer->dispatch();
	}
}

new Google_Sheet_WC_Admin_Importers();
