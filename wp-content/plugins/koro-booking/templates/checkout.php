<?php
/**
 * Checkout template.
 *
 * @package Koro_Booking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$subtotal = Koro_Booking_Cart::subtotal();
?>

<div class="koro-checkout container content-area">
	<h1><?php esc_html_e( 'Checkout', 'koro-booking' ); ?></h1>

	<?php if ( ! empty( $result ) ) : ?>
		<div class="koro-notice koro-notice--<?php echo ! empty( $result['success'] ) ? 'success' : 'error'; ?>">
			<?php echo esc_html( $result['message'] ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $result['success'] ) ) : ?>
		<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return Home', 'koro-booking' ); ?></a></p>
	<?php elseif ( empty( $items ) ) : ?>
		<p class="empty-state"><?php esc_html_e( 'Add services to your cart before checkout.', 'koro-booking' ); ?></p>
		<p><a class="btn btn--ghost" href="<?php echo esc_url( koro_booking_cart_url() ); ?>"><?php esc_html_e( 'View Cart', 'koro-booking' ); ?></a></p>
	<?php else : ?>
		<div class="koro-checkout__grid">
			<form class="koro-booking-form koro-checkout__form" method="post">
				<?php wp_nonce_field( 'koro_checkout', 'koro_checkout_nonce' ); ?>

				<div>
					<label for="customer_name"><?php esc_html_e( 'Full Name', 'koro-booking' ); ?></label>
					<input type="text" name="customer_name" id="customer_name" required>
				</div>

				<div>
					<label for="customer_email"><?php esc_html_e( 'Email', 'koro-booking' ); ?></label>
					<input type="email" name="customer_email" id="customer_email" required>
				</div>

				<div>
					<label for="booking_date"><?php esc_html_e( 'Preferred Date', 'koro-booking' ); ?></label>
					<input type="date" name="booking_date" id="booking_date" min="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>">
				</div>

				<button type="submit" class="btn btn--primary">
					<?php
					printf(
						/* translators: %s: order total */
						esc_html__( 'Pay $%s', 'koro-booking' ),
						esc_html( number_format( $subtotal, 2 ) )
					);
					?>
				</button>
			</form>

			<aside class="koro-checkout__summary">
				<h2><?php esc_html_e( 'Order Summary', 'koro-booking' ); ?></h2>
				<ul>
					<?php foreach ( $items as $item ) : ?>
						<li>
							<?php echo esc_html( $item['title'] ); ?>
							&times; <?php echo esc_html( (string) $item['quantity'] ); ?>
							— $<?php echo esc_html( number_format( (float) $item['price'] * (int) $item['quantity'], 2 ) ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<p><strong><?php esc_html_e( 'Total:', 'koro-booking' ); ?> $<?php echo esc_html( number_format( $subtotal, 2 ) ); ?></strong></p>
			</aside>
		</div>
	<?php endif; ?>
</div>
