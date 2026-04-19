<?php
/**
 * Admin View: Connection message
 *
 * @since 2.0.0
 *
 * @var array $response
 *
 * @package GSWOO
 */

defined( 'ABSPATH' ) || exit;
?>

<h4 style="color: red; max-width: 500px; color: #D32F2F;">
	<?php
	echo wp_kses( $response['message'], array( 'a' => array( 'href' => array() ) ) );
	?>
</h4>
