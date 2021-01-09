<?php
/**
 * Init WooCommerce data importers
 *
 * @since 1.0.0
 *
 * @package GSWOO
 * @subpackage GSWOO/woocommerce-importer
 */

defined( 'ABSPATH' ) || exit;

/**
 * GSWOO_Admin_Importers Class.
 *
 * @since 1.0.0
 */
class GSWOO_WC_Admin_Importers extends WC_Admin_Importers {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! $this->import_allowed() ) {
			return;
		}

		parent::__construct();

		add_action( 'admin_menu', array( $this, 'add_to_menus' ) );
		add_action( 'admin_head', array( $this, 'hide_from_menus' ) );

		// Register WooCommerce importers.
		$this->importers['product_importer_google_sheet'] = array(
			'menu'       => 'edit.php?post_type=product',
			'name'       => __( 'Product Import Google Sheet', 'import-products-from-gsheet-for-woo-importer' ),
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

		include_once GSWOO_WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php';
		include_once GSWOO_WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';
		include_once GSWOO_URI_ABSPATH . 'woocommerce-importer/class-gswoo-wc-product-csv-importer-controller.php';

		$importer = new GSWOO_WC_Product_CSV_Importer_Controller();
		$importer->dispatch();
	}
}

new GSWOO_WC_Admin_Importers();
