<?php
/**
 * Checkout processor.
 *
 * @package Koro_Booking
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle checkout form submission and payment.
 */
final class Koro_Booking_Checkout {

	/**
	 * Process checkout POST.
	 *
	 * @return array{success:bool,message:string,order_id?:int}
	 */
	public static function process(): array {
		if ( ! isset( $_POST['koro_checkout_nonce'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid request.', 'koro-booking' ),
			);
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['koro_checkout_nonce'] ) ), 'koro_checkout' ) ) {
			return array(
				'success' => false,
				'message' => __( 'Security check failed.', 'koro-booking' ),
			);
		}

		$items = Koro_Booking_Cart::get_items();
		if ( empty( $items ) ) {
			return array(
				'success' => false,
				'message' => __( 'Your cart is empty.', 'koro-booking' ),
			);
		}

		$name  = sanitize_text_field( wp_unslash( $_POST['customer_name'] ?? '' ) );
		$email = sanitize_email( wp_unslash( $_POST['customer_email'] ?? '' ) );
		$date  = sanitize_text_field( wp_unslash( $_POST['booking_date'] ?? '' ) );

		if ( '' === $name || ! is_email( $email ) ) {
			return array(
				'success' => false,
				'message' => __( 'Please provide a valid name and email.', 'koro-booking' ),
			);
		}

		if ( '' === $date || ! self::is_valid_booking_date( $date ) ) {
			return array(
				'success' => false,
				'message' => __( 'Please choose a valid booking date (today or later).', 'koro-booking' ),
			);
		}

		$subtotal = Koro_Booking_Cart::subtotal();

		$order_id = Koro_Booking_Orders::create(
			array(
				'customer_name'  => $name,
				'customer_email' => $email,
				'booking_date'   => $date,
				'items'          => $items,
				'subtotal'       => $subtotal,
				'status'         => 'pending',
			)
		);

		if ( $order_id <= 0 ) {
			return array(
				'success' => false,
				'message' => __( 'Could not create order.', 'koro-booking' ),
			);
		}

		if ( ! function_exists( 'koro_payments_charge' ) ) {
			return array(
				'success' => false,
				'message' => __( 'Payment plugin is not active.', 'koro-booking' ),
			);
		}

		$payment = koro_payments_charge(
			$subtotal,
			'order_' . $order_id,
			array(
				'customer_email' => $email,
				'order_id'       => $order_id,
			)
		);

		if ( empty( $payment['success'] ) ) {
			Koro_Booking_Orders::update_status( $order_id, 'failed' );
			return array(
				'success' => false,
				'message' => $payment['message'] ?? __( 'Payment failed.', 'koro-booking' ),
			);
		}

		Koro_Booking_Orders::complete(
			$order_id,
			(string) ( $payment['transaction_id'] ?? '' )
		);

		Koro_Booking_Orders::send_confirmation_email( $order_id );

		Koro_Booking_Cart::clear();

		return array(
			'success'  => true,
			'message'  => __( 'Booking confirmed. Thank you!', 'koro-booking' ),
			'order_id' => $order_id,
		);
	}

	/**
	 * Validate booking date is today or in the future (Y-m-d).
	 */
	private static function is_valid_booking_date( string $date ): bool {
		$parsed = date_create_from_format( 'Y-m-d', $date );

		if ( false === $parsed ) {
			return false;
		}

		$today = new DateTime( 'today', wp_timezone() );

		return $parsed >= $today;
	}
}
