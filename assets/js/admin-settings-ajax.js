/**
 * Admin setting ajax actions.
 *
 * @since 2.0.0
 * @package GSWOO
 */

jQuery( document ).ready(
	function($) {
		$( '#auth_code_method_tab, #assertion_method_tab' ).on(
			'click touchmove',
			function (e) {
				if ( $( '.restore-button' ).length ) {
					e.preventDefault();
					var data = {action: 'restore_action'},
					href     = $( this ).attr( 'href' );

					$.ajax(
						{
							type: "POST",
							url: gswoo_admin_ajax.url,
							data: data,
							success: function () {
								window.location = href;
							}
						}
					);
				}
			}
		);
	}
);
