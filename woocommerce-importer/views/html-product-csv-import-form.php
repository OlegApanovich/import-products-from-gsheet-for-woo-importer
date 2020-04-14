<?php
/**
 * Admin View: Product import form
 *
 * @since 1.0.0
 *
 * @package GSWOO
 * @subpackage GSWOO/woocommerce-importer/views
 */

defined( 'ABSPATH' ) || exit;
?>
<form class="wc-progress-form-content woocommerce-importer" enctype="multipart/form-data" method="post">
	<header>
		<h2><?php esc_html_e( 'Import products from a Google Sheet', 'import-products-from-gsheet-for-woo-importer' ); ?></h2>
		<p><?php esc_html_e( 'This tool allows you to import (or merge) product data to your store from a Google Sheet created on your Google Drive.', 'import-products-from-gsheet-for-woo-importer' ); ?></p>
	</header>
	<section>
		<table class="form-table woocommerce-importer-options">
			<tbody>
				<tr>
					<th scope="row">
						<label for="upload">
							<?php _e( 'Choose Google Sheet title:', 'import-products-from-gsheet-for-woo-importer' ); ?>
						</label>
					</th>
					<td>
						<?php
						if ( ! empty( $upload_dir['error'] ) ) {
							?>
							<div class="inline error">
								<p><?php esc_html_e( 'Before you can upload your import file, you will need to fix the following error:', 'import-products-from-gsheet-for-woo-importer' ); ?></p>
								<p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p>
							</div>
							<?php
						} else {
							?>
								<?php
								if ( ! empty( $options['google_sheet_title'] ) ) {
									$google_sheet_title_list = array_map( 'trim', explode( ',' , $options['google_sheet_title'] ) );
									?>
									<select name="file">
										<?php
										foreach ( $google_sheet_title_list as $google_sheet_title ) {
											?>
											<option value="<?php echo $google_sheet_title; ?>">
												<?php echo $google_sheet_title; ?>
											</option>
											<?php
										}
										?>
									</select>
									<?php
								} else {
									$menu_page_url = menu_page_url( 'woocommerce_import_products_google_sheet_menu', false );
									?>
									<p style='color: red;'>
										<?php
										echo wp_kses(
											printf(
												// translators: %$s: plugin settings page url.
												__(
													"You do not set any google sheet titles for import, please go to <a href='%s'>plugin option page</a> and set it",
													'import-products-from-gsheet-for-woo-importer'
												),
												$menu_page_url
											),
											array( 'a' => array() )
										);
										?>
									</p>
									<?php
								}
								?>

							<input type="hidden" name="action" value="save" />
							<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
							<br>
							<small>
								<?php
								printf(
									/* translators: %s: maximum upload size */
									esc_html__( 'Maximum size: %s', 'import-products-from-gsheet-for-woo-importer' ),
									esc_html( $size )
								);
								?>
							</small>
							<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<th><label for="woocommerce-importer-update-existing"><?php esc_html_e( 'Update existing products', 'import-products-from-gsheet-for-woo-importer' ); ?></label><br/></th>
					<td>
						<input type="hidden" name="update_existing" value="0" />
						<input type="checkbox" id="woocommerce-importer-update-existing" name="update_existing" value="1" />
						<label for="woocommerce-importer-update-existing"><?php esc_html_e( 'Existing products that match by ID or SKU will be updated. Products that do not exist will be skipped.', 'import-products-from-gsheet-for-woo-importer' ); ?></label>
					</td>
				</tr>
				<tr class="woocommerce-importer-advanced hidden">
					<th>
						<label for="woocommerce-importer-file-url"><?php esc_html_e( 'Alternatively, enter the path to a CSV file on your server:', 'import-products-from-gsheet-for-woo-importer' ); ?></label>
					</th>
					<td>
						<label for="woocommerce-importer-file-url" class="woocommerce-importer-file-url-field-wrapper">
							<code><?php echo esc_html( ABSPATH ) . ' '; ?></code><input type="text" id="woocommerce-importer-file-url" name="file_url" />
						</label>
					</td>
				</tr>
				<tr class="woocommerce-importer-advanced hidden">
					<th><label><?php esc_html_e( 'CSV Delimiter', 'import-products-from-gsheet-for-woo-importer' ); ?></label><br/></th>
					<td><input type="text" name="delimiter" placeholder="," size="2" /></td>
				</tr>
				<tr class="woocommerce-importer-advanced hidden">
					<th><label><?php esc_html_e( 'Use previous column mapping preferences?', 'import-products-from-gsheet-for-woo-importer' ); ?></label><br/></th>
					<td><input type="checkbox" id="woocommerce-importer-map-preferences" name="map_preferences" value="1" /></td>
				</tr>
			</tbody>
		</table>
	</section>
	<script type="text/javascript">
		jQuery(function() {
			jQuery( '.woocommerce-importer-toggle-advanced-options' ).on( 'click', function() {
				var elements = jQuery( '.woocommerce-importer-advanced' );
				if ( elements.is( '.hidden' ) ) {
					elements.removeClass( 'hidden' );
					jQuery( this ).text( jQuery( this ).data( 'hidetext' ) );
				} else {
					elements.addClass( 'hidden' );
					jQuery( this ).text( jQuery( this ).data( 'showtext' ) );
				}
				return false;
			} );
		});
	</script>
	<div class="wc-actions">
		<a href="#" class="woocommerce-importer-toggle-advanced-options" data-hidetext="<?php esc_html_e( 'Hide advanced options', 'import-products-from-gsheet-for-woo-importer' ); ?>" data-showtext="<?php esc_html_e( 'Hide advanced options', 'import-products-from-gsheet-for-woo-importer' ); ?>"><?php esc_html_e( 'Show advanced options', 'import-products-from-gsheet-for-woo-importer' ); ?></a>
		<button type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'Continue', 'import-products-from-gsheet-for-woo-importer' ); ?>" name="save_step"><?php esc_html_e( 'Continue', 'import-products-from-gsheet-for-woo-importer' ); ?></button>
		<?php wp_nonce_field( 'woocommerce-csv-importer' ); ?>
	</div>
</form>
