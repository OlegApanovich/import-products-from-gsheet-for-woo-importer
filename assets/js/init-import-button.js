/**
 * Initialize plugin import button.
 *
 * @since 2.0.0
 * @package GSWOO
 */

( function( $, woocommerce_import_google_sheet_admin ) {
	$(
		// Add additional plugin import button to CPT products main page.
		function() {

			var $product_screen = $( '.edit-php.post-type-product' ),
				$title_action   = $product_screen.find( '.page-title-action:first' ),

				form =
				'<form method="post" style="display: inline"><a href="' +
				woocommerce_import_google_sheet_admin.urls.import_products_google_sheet +
				'" class="page-title-action">' +
				woocommerce_import_google_sheet_admin.strings.import_products_google_sheet +
				'</a></form>';

			$title_action.after( form );
		}
	);
})( jQuery, woocommerce_import_google_sheet_admin );
