<?php
/**
 * Booking orders (custom post type).
 *
 * @package Koro_Booking
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Store and manage booking orders.
 */
final class Koro_Booking_Orders {

	public static function register_post_type(): void {
		register_post_type(
			'koro_order',
			array(
				'labels'              => array(
					'name'          => __( 'Bookings', 'koro-booking' ),
					'singular_name' => __( 'Booking', 'koro-booking' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'koro-dashboard',
				'capability_type'     => 'koro_order',
				'map_meta_cap'        => true,
				'supports'            => array( 'title' ),
				'menu_icon'           => 'dashicons-clipboard',
			)
		);
	}

	/**
	 * Create order from checkout data.
	 *
	 * @param array<string, mixed> $data Order payload.
	 */
	public static function create( array $data ): int {
		$order_id = wp_insert_post(
			array(
				'post_type'   => 'koro_order',
				'post_status' => 'publish',
				'post_title'  => sprintf(
					/* translators: %s: customer name */
					__( 'Booking — %s', 'koro-booking' ),
					sanitize_text_field( (string) ( $data['customer_name'] ?? '' ) )
				),
			),
			true
		);

		if ( is_wp_error( $order_id ) ) {
			return 0;
		}

		update_post_meta( $order_id, '_koro_customer_name', sanitize_text_field( (string) ( $data['customer_name'] ?? '' ) ) );
		update_post_meta( $order_id, '_koro_customer_email', sanitize_email( (string) ( $data['customer_email'] ?? '' ) ) );
		update_post_meta( $order_id, '_koro_booking_date', sanitize_text_field( (string) ( $data['booking_date'] ?? '' ) ) );
		update_post_meta( $order_id, '_koro_items', wp_json_encode( $data['items'] ?? array() ) );
		update_post_meta( $order_id, '_koro_subtotal', (float) ( $data['subtotal'] ?? 0 ) );
		update_post_meta( $order_id, '_koro_status', sanitize_key( (string) ( $data['status'] ?? 'pending' ) ) );

		return (int) $order_id;
	}

	public static function update_status( int $order_id, string $status ): void {
		update_post_meta( $order_id, '_koro_status', sanitize_key( $status ) );
	}

	public static function complete( int $order_id, string $transaction_id ): void {
		update_post_meta( $order_id, '_koro_status', 'completed' );
		update_post_meta( $order_id, '_koro_transaction_id', sanitize_text_field( $transaction_id ) );
	}

	public static function send_confirmation_email( int $order_id ): void {
		$email = get_post_meta( $order_id, '_koro_customer_email', true );
		$name  = get_post_meta( $order_id, '_koro_customer_name', true );
		$date  = get_post_meta( $order_id, '_koro_booking_date', true );
		$total = (float) get_post_meta( $order_id, '_koro_subtotal', true );

		if ( ! is_email( $email ) ) {
			return;
		}

		$subject = __( 'Your booking is confirmed', 'koro-booking' );
		$body    = sprintf(
			__( "Hi %1\$s,\n\nYour booking for %2\$s is confirmed.\nTotal: $%3\$s\n\nThank you for choosing us.", 'koro-booking' ),
			sanitize_text_field( (string) $name ),
			sanitize_text_field( (string) $date ),
			number_format( $total, 2 )
		);

		wp_mail( $email, $subject, $body );
	}

	/**
	 * Count orders by status meta.
	 */
	public static function count_by_status( string $status ): int {
		$query = new WP_Query(
			array(
				'post_type'      => 'koro_order',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'   => '_koro_status',
						'value' => $status,
					),
				),
			)
		);

		return (int) $query->found_posts;
	}

	/**
	 * Recent orders for dashboard.
	 *
	 * @return WP_Post[]
	 */
	public static function recent( int $limit = 5 ): array {
		$query = new WP_Query(
			array(
				'post_type'      => 'koro_order',
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		return $query->posts;
	}
}
