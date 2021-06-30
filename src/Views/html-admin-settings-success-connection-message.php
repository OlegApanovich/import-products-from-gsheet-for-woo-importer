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

<h3 style="color: green;">
	<?php
	echo wp_kses( $response['message'], array( 'a' => array( 'href' => array() ) ) );
	?>
</h3>
