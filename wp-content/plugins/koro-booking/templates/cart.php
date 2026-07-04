<?php
/**
 * Cart template.
 *
 * @package Koro_Booking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$subtotal = Koro_Booking_Cart::subtotal();
?>

<div class="koro-cart container content-area">
	<h1><?php esc_html_e( 'Your Cart', 'koro-booking' ); ?></h1>

	<?php if ( isset( $_GET['added'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
		<div class="koro-notice koro-notice--success"><?php esc_html_e( 'Service added to cart.', 'koro-booking' ); ?></div>
	<?php endif; ?>

	<?php if ( empty( $items ) ) : ?>
		<p class="empty-state"><?php esc_html_e( 'Your cart is empty.', 'koro-booking' ); ?></p>
		<p><a class="btn btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'koro_service' ) ?: home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Browse Services', 'koro-booking' ); ?></a></p>
	<?php else : ?>
		<table class="koro-cart-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Service', 'koro-booking' ); ?></th>
					<th><?php esc_html_e( 'Price', 'koro-booking' ); ?></th>
					<th><?php esc_html_e( 'Qty', 'koro-booking' ); ?></th>
					<th><?php esc_html_e( 'Line Total', 'koro-booking' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $items as $item ) : ?>
					<tr>
						<td><?php echo esc_html( $item['title'] ); ?></td>
						<td>$<?php echo esc_html( number_format( (float) $item['price'], 2 ) ); ?></td>
						<td>
							<form method="post">
								<?php wp_nonce_field( 'koro_cart', 'koro_cart_nonce' ); ?>
								<input type="hidden" name="koro_cart_action" value="update">
								<input type="hidden" name="service_id" value="<?php echo esc_attr( (string) $item['service_id'] ); ?>">
								<input type="number" name="quantity" value="<?php echo esc_attr( (string) $item['quantity'] ); ?>" min="1" max="10" style="width:4rem">
								<button type="submit" class="button-link"><?php esc_html_e( 'Update', 'koro-booking' ); ?></button>
							</form>
						</td>
						<td>$<?php echo esc_html( number_format( (float) $item['price'] * (int) $item['quantity'], 2 ) ); ?></td>
						<td>
							<form method="post">
								<?php wp_nonce_field( 'koro_cart', 'koro_cart_nonce' ); ?>
								<input type="hidden" name="koro_cart_action" value="remove">
								<input type="hidden" name="service_id" value="<?php echo esc_attr( (string) $item['service_id'] ); ?>">
								<button type="submit" class="button-link"><?php esc_html_e( 'Remove', 'koro-booking' ); ?></button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3"><?php esc_html_e( 'Subtotal', 'koro-booking' ); ?></th>
					<td colspan="2"><strong>$<?php echo esc_html( number_format( $subtotal, 2 ) ); ?></strong></td>
				</tr>
			</tfoot>
		</table>

		<p style="margin-top:1.5rem">
			<a class="btn btn--primary" href="<?php echo esc_url( koro_booking_checkout_url() ); ?>"><?php esc_html_e( 'Proceed to Checkout', 'koro-booking' ); ?></a>
		</p>
	<?php endif; ?>
</div>
