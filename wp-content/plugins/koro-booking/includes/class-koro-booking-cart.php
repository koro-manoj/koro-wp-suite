<?php
/**
 * Session-based booking cart.
 *
 * @package Koro_Booking
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manage cart items in user session.
 */
final class Koro_Booking_Cart {

	private const SESSION_KEY = 'koro_booking_cart';

	/**
	 * Get cart items keyed by service ID.
	 *
	 * @return array<int, array{service_id:int,quantity:int,price:float,title:string}>
	 */
	public static function get_items(): array {
		self::maybe_start_session();

		$items = $_SESSION[ self::SESSION_KEY ] ?? array();
		return is_array( $items ) ? $items : array();
	}

	/**
	 * Add service to cart.
	 */
	public static function add( int $service_id, int $quantity = 1 ): bool {
		$post = get_post( $service_id );
		if ( ! $post || 'koro_service' !== $post->post_type || 'publish' !== $post->post_status ) {
			return false;
		}

		$quantity = max( 1, min( 10, $quantity ) );
		$price    = (float) get_post_meta( $service_id, '_koro_price', true );
		$items    = self::get_items();

		if ( isset( $items[ $service_id ] ) ) {
			$items[ $service_id ]['quantity'] = min( 10, $items[ $service_id ]['quantity'] + $quantity );
		} else {
			$items[ $service_id ] = array(
				'service_id' => $service_id,
				'quantity'   => $quantity,
				'price'      => $price,
				'title'      => get_the_title( $service_id ),
			);
		}

		self::persist( $items );
		return true;
	}

	/**
	 * Update item quantity.
	 */
	public static function update( int $service_id, int $quantity ): bool {
		$items = self::get_items();
		if ( ! isset( $items[ $service_id ] ) ) {
			return false;
		}

		if ( $quantity <= 0 ) {
			unset( $items[ $service_id ] );
		} else {
			$items[ $service_id ]['quantity'] = max( 1, min( 10, $quantity ) );
		}

		self::persist( $items );
		return true;
	}

	/**
	 * Remove item from cart.
	 */
	public static function remove( int $service_id ): void {
		$items = self::get_items();
		unset( $items[ $service_id ] );
		self::persist( $items );
	}

	/**
	 * Clear cart.
	 */
	public static function clear(): void {
		self::persist( array() );
	}

	/**
	 * Total item count.
	 */
	public static function count(): int {
		$count = 0;
		foreach ( self::get_items() as $item ) {
			$count += (int) ( $item['quantity'] ?? 0 );
		}
		return $count;
	}

	/**
	 * Cart subtotal.
	 */
	public static function subtotal(): float {
		$total = 0.0;
		foreach ( self::get_items() as $item ) {
			$total += (float) $item['price'] * (int) $item['quantity'];
		}
		return round( $total, 2 );
	}

	/**
	 * Persist cart to session.
	 *
	 * @param array<int, array<string, mixed>> $items Cart items.
	 */
	private static function persist( array $items ): void {
		self::maybe_start_session();
		$_SESSION[ self::SESSION_KEY ] = $items;
	}

	/**
	 * Start PHP session when needed.
	 */
	public static function maybe_start_session(): void {
		if ( PHP_SESSION_ACTIVE === session_status() ) {
			return;
		}

		if ( headers_sent() ) {
			return;
		}

		session_start(
			array(
				'read_and_close' => false,
				'cookie_httponly' => true,
				'cookie_samesite' => 'Lax',
			)
		);
	}
}

/**
 * Public helper: cart item count.
 */
function koro_booking_cart_count(): int {
	return Koro_Booking_Cart::count();
}

/**
 * Public helper: cart page URL.
 */
function koro_booking_cart_url(): string {
	$page_id = (int) get_option( 'koro_booking_cart_page_id', 0 );
	if ( $page_id > 0 ) {
		return (string) get_permalink( $page_id );
	}
	return home_url( '/cart/' );
}

/**
 * Public helper: checkout page URL.
 */
function koro_booking_checkout_url(): string {
	$page_id = (int) get_option( 'koro_booking_checkout_page_id', 0 );
	if ( $page_id > 0 ) {
		return (string) get_permalink( $page_id );
	}
	return home_url( '/checkout/' );
}
